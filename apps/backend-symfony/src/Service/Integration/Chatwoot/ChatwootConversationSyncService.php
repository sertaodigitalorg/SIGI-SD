<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\AttendanceProtocol;
use App\Entity\Integration\Chatwoot\ChatwootAccount;
use App\Repository\AttendanceProtocolRepository;
use App\Repository\ProtocolSettingsRepository;
use App\Service\ProtocolNumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class ChatwootConversationSyncService
{
    private const LABEL_SYNCED = 'sigi-sincronizado';
    private const LABEL_PROTOCOL_GENERATED = 'sigi-protocolo-gerado';
    private const LABEL_PROTOCOL_MESSAGE_SENT = 'sigi-mensagem-protocolo-enviada';

    public function __construct(
        private AttendanceProtocolRepository $protocolRepository,
        private ChatwootConversationNormalizer $normalizer,
        private ChatwootChannelMapper $channelMapper,
        private ProtocolNumberGenerator $protocolNumberGenerator,
        private ChatwootApiClient $apiClient,
        private ChatwootContactSyncService $contactSyncService,
        private ProtocolSettingsRepository $settingsRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function syncPayload(array $payload, ?ChatwootAccount $account = null, bool $sendPrivateNote = true): ?AttendanceProtocol
    {
        $data = $this->normalizer->normalize($payload);
        if (null === $data) {
            $this->logger->warning('Chatwoot payload sem identificador de conversa.');

            return null;
        }

        return $this->syncConversationData($data, $account, $sendPrivateNote);
    }

    public function syncConversationId(string $conversationId, ?ChatwootAccount $account = null, bool $sendPrivateNote = true): ?AttendanceProtocol
    {
        $payload = $this->apiClient->getConversation($account, $conversationId);

        return $this->syncPayload($payload, $account, $sendPrivateNote);
    }

    public function syncConversationData(ChatwootConversationData $data, ?ChatwootAccount $account = null, bool $sendPrivateNote = true): AttendanceProtocol
    {
        $protocol = $this->protocolRepository->findOneByConversationId($data->conversationId);
        $isNew = false;

        if (null === $protocol) {
            $protocol = (new AttendanceProtocol())->setChatwootConversationId($data->conversationId);
            $this->protocolNumberGenerator->assign($protocol, $data->createdAt ?? new \DateTimeImmutable());
            $this->entityManager->persist($protocol);
            $isNew = true;
        }

        $person = $this->contactSyncService->sync($data);
        if (null !== $person) {
            $protocol->setPerson($person);
        }

        $protocol->applyChatwootData(
            $data->contactId,
            $data->contactName,
            $data->contactHandle,
            $data->sourceChannel,
            $data->subject,
            $data->status,
            $data->labels,
            $data->team,
            $data->agent,
            $data->priority,
            $data->createdAt,
            $data->updatedAt,
            $data->closedAt,
        );

        if ($sendPrivateNote && !$protocol->isProtocolNoteSent()) {
            try {
                $this->apiClient->createPrivateNote(
                    $account,
                    $data->conversationId,
                    sprintf('Protocolo SIGI gerado automaticamente: %s', $protocol->getProtocolCode()),
                );
                $protocol->markProtocolNoteSent();
            } catch (\Throwable $exception) {
                $this->logger->warning('Nao foi possivel enviar nota privada de protocolo ao Chatwoot.', [
                    'conversation_id' => $data->conversationId,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $reservedLabels = [
            self::LABEL_SYNCED,
            self::LABEL_PROTOCOL_GENERATED,
        ];

        $settings = $this->settingsRepository->getOrCreate();
        if ($settings->shouldSendPublicProtocolMessage() && !$protocol->isCustomerProtocolMessageSent()) {
            try {
                $messageTemplate = $this->channelMapper->protocolMessage($data->channel, $settings->getPublicProtocolMessageTemplate());
                $this->apiClient->createPublicMessage(
                    $account,
                    $data->conversationId,
                    $this->renderPublicProtocolMessage($messageTemplate, $protocol, $data),
                );
                $protocol->markCustomerProtocolMessageSent();
                $reservedLabels[] = self::LABEL_PROTOCOL_MESSAGE_SENT;
            } catch (\Throwable $exception) {
                $this->logger->warning('Nao foi possivel enviar mensagem publica de protocolo ao Chatwoot.', [
                    'conversation_id' => $data->conversationId,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($protocol->isCustomerProtocolMessageSent()) {
            $reservedLabels[] = self::LABEL_PROTOCOL_MESSAGE_SENT;
        }

        $reservedLabels = array_values(array_unique(array_merge(
            $reservedLabels,
            $this->channelMapper->defaultLabels($data->channel, $protocol->isCustomerProtocolMessageSent()),
        )));

        $this->syncOperationalLabels($protocol, $account, $data, $reservedLabels);

        $this->entityManager->flush();
        $this->logger->info($isNew ? 'Protocolo SIGI criado.' : 'Protocolo SIGI atualizado.', [
            'protocol' => $protocol->getProtocolCode(),
            'conversation_id' => $data->conversationId,
            'channel' => $data->channel,
            'contact_id' => $data->contactId,
        ]);

        return $protocol;
    }

    private function renderPublicProtocolMessage(string $template, AttendanceProtocol $protocol, ChatwootConversationData $data): string
    {
        return strtr($template, [
            '{protocol}' => (string) $protocol->getProtocolCode(),
            '{nome}' => (string) ($data->contactName ?? ''),
            '{assunto}' => (string) ($data->subject ?? ''),
            '{canal}' => (string) ($data->sourceChannel ?? ''),
        ]);
    }

    /**
     * @param array<int, string> $reservedLabels
     */
    private function syncOperationalLabels(
        AttendanceProtocol $protocol,
        ?ChatwootAccount $account,
        ChatwootConversationData $data,
        array $reservedLabels,
    ): void {
        $labels = $this->mergeLabels($data->labels, $reservedLabels);
        $currentLabels = $this->mergeLabels($data->labels, []);

        if ($labels === $currentLabels) {
            $protocol->setLabels($currentLabels);

            return;
        }

        try {
            $this->apiClient->applyLabels($account, $data->conversationId, $labels);
            $protocol->setLabels($labels);
        } catch (\Throwable $exception) {
            $protocol->setLabels($currentLabels);
            $this->logger->warning('Nao foi possivel aplicar etiquetas operacionais SIGI no Chatwoot.', [
                'conversation_id' => $data->conversationId,
                'labels' => $reservedLabels,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param array<int, string> $currentLabels
     * @param array<int, string> $labelsToAdd
     *
     * @return array<int, string>
     */
    private function mergeLabels(array $currentLabels, array $labelsToAdd): array
    {
        $labels = [];
        foreach (array_merge($currentLabels, $labelsToAdd) as $label) {
            if (!is_scalar($label)) {
                continue;
            }

            $label = trim((string) $label);
            if ('' === $label) {
                continue;
            }

            $labels[mb_strtolower($label)] = $label;
        }

        return array_values($labels);
    }
}
