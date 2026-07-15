<?php

namespace App\Service\Conversation;

use App\Entity\AttendanceProtocol;
use App\Entity\Conversation;
use App\Entity\ConversationMessage;
use App\Entity\Integration\Chatwoot\ChatwootAccount;
use App\Entity\RequestEvent;
use App\Entity\ServiceRequest;
use App\Repository\ConversationMessageRepository;
use App\Repository\ConversationRepository;
use App\Repository\RequestEventRepository;
use App\Repository\ServiceRequestRepository;
use App\Service\Integration\Chatwoot\ChatwootConversationData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

final readonly class ConversationWorkflowSyncService
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private ServiceRequestRepository $serviceRequestRepository,
        private ConversationMessageRepository $messageRepository,
        private RequestEventRepository $eventRepository,
        private Registry $workflowRegistry,
        private ServiceRequestTransitionService $transitionService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function syncFromChatwoot(
        ChatwootConversationData $data,
        AttendanceProtocol $legacyProtocol,
        ?ChatwootAccount $account,
        array $payload,
    ): ServiceRequest {
        $accountId = $this->resolveAccountId($account);
        $conversation = $this->conversationRepository->findOneByChatwootReference($accountId, $data->conversationId);
        $isNewConversation = false;

        if (null === $conversation) {
            $conversation = (new Conversation())
                ->setChatwootAccountId($accountId)
                ->setChatwootConversationId($data->conversationId);
            $this->entityManager->persist($conversation);
            $isNewConversation = true;
        }

        $this->applyConversationData($conversation, $data, $payload);

        $request = $this->serviceRequestRepository->findOneBy(['legacyAttendanceProtocol' => $legacyProtocol])
            ?? $this->serviceRequestRepository->findOpenByConversation($conversation);
        $isNewRequest = false;

        if (null === $request) {
            $request = (new ServiceRequest())
                ->setConversation($conversation)
                ->setLegacyAttendanceProtocol($legacyProtocol)
                ->setProtocol((string) $legacyProtocol->getProtocolCode())
                ->setWorkflowKey(ServiceRequest::DEFAULT_WORKFLOW_KEY)
                ->setCurrentState(ServiceRequest::DEFAULT_STATE);
            $this->entityManager->persist($request);
            $isNewRequest = true;
        }

        $this->applyRequestData($request, $conversation, $data, $legacyProtocol, $payload);
        $this->syncMessage($conversation, $request, $data, $payload);
        $this->advanceInitialWorkflow($request, $conversation, $data, $payload);
        $this->recordEvent($conversation, $request, $data, $payload, $isNewConversation, $isNewRequest);

        return $request;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function applyConversationData(Conversation $conversation, ChatwootConversationData $data, array $payload): void
    {
        $conversation
            ->setChatwootInboxId($this->intValue($data->inboxId))
            ->setChannel($data->channel)
            ->setContactId($data->contactId)
            ->setAssignedAgentId($data->agent)
            ->setAssignedTeamId($data->team)
            ->setClosedAt($data->closedAt)
            ->touch();

        $message = $this->messagePayload($payload);
        $messageAt = $this->dateValue($message, ['created_at', 'updated_at']) ?? $data->updatedAt ?? new \DateTimeImmutable();
        if ('outgoing' === $this->messageDirection($message)) {
            $conversation->setLastOutgoingMessageAt($messageAt);
        } elseif ([] !== $message) {
            $conversation->setLastIncomingMessageAt($messageAt);
        }

        $labels = $this->lowerLabels($data->labels);
        if (null !== $data->agent && !in_array('sigi-retomar-ia', $labels, true)) {
            $conversation
                ->setCurrentController(Conversation::CONTROLLER_HUMAN)
                ->setAutomationEnabled(false);

            return;
        }

        if (in_array('sigi-pausar-ia', $labels, true)) {
            $conversation
                ->setCurrentController(Conversation::CONTROLLER_HUMAN)
                ->setAutomationEnabled(false);

            return;
        }

        if (in_array('sigi-retomar-ia', $labels, true)) {
            $conversation
                ->setCurrentController(Conversation::CONTROLLER_WAITING)
                ->setAutomationEnabled(true);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function applyRequestData(
        ServiceRequest $request,
        Conversation $conversation,
        ChatwootConversationData $data,
        AttendanceProtocol $legacyProtocol,
        array $payload,
    ): void {
        $context = $request->getContext();
        $context['chatwoot'] = [
            'conversation_id' => $data->conversationId,
            'account_id' => $conversation->getChatwootAccountId(),
            'inbox_id' => $conversation->getChatwootInboxId(),
            'contact_id' => $data->contactId,
            'labels' => $data->labels,
            'last_event' => $this->stringValue($payload, ['event', 'event_type', 'message_type']),
        ];

        $request
            ->setLegacyAttendanceProtocol($legacyProtocol)
            ->setServiceType($data->channel)
            ->setPriority($data->priority)
            ->setAssignedAgentId($data->agent)
            ->setAssignedTeamId($data->team)
            ->setContext($context)
            ->touch();

        if (null !== $data->closedAt) {
            $request->markClosed($data->closedAt);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function syncMessage(Conversation $conversation, ServiceRequest $request, ChatwootConversationData $data, array $payload): void
    {
        $messagePayload = $this->messagePayload($payload);
        if ([] === $messagePayload) {
            return;
        }

        $messageId = $this->stringValue($messagePayload, ['id', 'message_id']);
        $message = null !== $messageId ? $this->messageRepository->findOneByChatwootMessage($conversation, $messageId) : null;

        if (null === $message) {
            $message = (new ConversationMessage())->setConversation($conversation);
            $this->entityManager->persist($message);
        }

        $direction = $this->messageDirection($messagePayload);
        $sender = $this->arrayValue($messagePayload, ['sender']) ?? [];
        $messageAt = $this->dateValue($messagePayload, ['created_at', 'updated_at']) ?? $data->updatedAt;

        $message
            ->setServiceRequest($request)
            ->setChatwootMessageId($messageId)
            ->setDirection($direction)
            ->setSenderType($this->senderType($direction, $messagePayload))
            ->setSenderId($this->stringValue($sender, ['id', 'email', 'name']))
            ->setMessageType($this->stringValue($messagePayload, ['content_type', 'message_type']))
            ->setContent($this->stringValue($messagePayload, ['content']))
            ->setAttachments($this->attachments($messagePayload))
            ->setDeliveryStatus($this->stringValue($messagePayload, ['status']))
            ->setSentAt('outgoing' === $direction ? $messageAt : null)
            ->setReceivedAt('incoming' === $direction ? $messageAt : null)
            ->setMetadata([
                'event' => $this->stringValue($payload, ['event', 'event_type']),
                'private' => (bool) ($messagePayload['private'] ?? false),
                'source_channel' => $data->sourceChannel,
            ]);
    }

    private function advanceInitialWorkflow(ServiceRequest $request, Conversation $conversation, ChatwootConversationData $data, array $payload): void
    {
        $workflow = $this->workflowRegistry->get($request, 'service_request');
        $confidence = $this->confidenceValue($payload);

        if ($this->transitionService->routeLowConfidence($request, $confidence)) {
            return;
        }

        if (!$conversation->isAutomationEnabled() && $workflow->can($request, 'request_human')) {
            $this->transitionService->apply($request, 'request_human');

            return;
        }

        if (null !== $data->closedAt) {
            if ($workflow->can($request, 'complete')) {
                $this->transitionService->apply($request, 'complete');
                $request->markCompleted($data->closedAt);
            }
            if ($workflow->can($request, 'close')) {
                $this->transitionService->apply($request, 'close');
                $request->markClosed($data->closedAt);
            }

            return;
        }

        if (ServiceRequest::DEFAULT_STATE === $request->getCurrentState() && $workflow->can($request, 'start_collection')) {
            $this->transitionService->apply($request, 'start_collection');
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function recordEvent(
        Conversation $conversation,
        ServiceRequest $request,
        ChatwootConversationData $data,
        array $payload,
        bool $isNewConversation,
        bool $isNewRequest,
    ): void {
        $eventType = $this->stringValue($payload, ['event', 'event_type', 'message_type']) ?? 'chatwoot_sync';
        $latestEvent = $this->eventRepository->findLatestForRequest($request);
        $eventHash = hash('sha256', json_encode([
            'event' => $eventType,
            'conversation' => $data->conversationId,
            'message' => $this->stringValue($this->messagePayload($payload), ['id', 'message_id']),
            'state' => $request->getCurrentState(),
            'new_conversation' => $isNewConversation,
            'new_request' => $isNewRequest,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        $event = (new RequestEvent())
            ->setConversation($conversation)
            ->setServiceRequest($request)
            ->setEventType($eventType)
            ->setActorType(RequestEvent::ACTOR_INTEGRATION)
            ->setActorId('chatwoot')
            ->setResult($isNewRequest ? 'created' : 'updated')
            ->setStates(null, $request->getCurrentState())
            ->setPreviousEventHash($latestEvent?->getEventHash())
            ->setEventHash($eventHash)
            ->setMetadata([
                'channel' => $data->channel,
                'conversation_id' => $data->conversationId,
                'message_id' => $this->stringValue($this->messagePayload($payload), ['id', 'message_id']),
                'new_conversation' => $isNewConversation,
                'new_request' => $isNewRequest,
            ]);

        $this->entityManager->persist($event);
    }

    private function resolveAccountId(?ChatwootAccount $account): int
    {
        if (null === $account) {
            return 0;
        }

        $accountId = $account->getAccountId();
        if (null !== $accountId && ctype_digit($accountId)) {
            return (int) $accountId;
        }

        return (int) $account->getId();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function messagePayload(array $payload): array
    {
        return $this->arrayValue($payload, ['message'])
            ?? $this->arrayValue($payload, ['conversation.messages.0'])
            ?? ([] !== array_intersect(['content', 'message_type', 'content_type'], array_keys($payload)) ? $payload : []);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function messageDirection(array $payload): string
    {
        $value = $this->stringValue($payload, ['message_type', 'direction']);

        return 'outgoing' === $value ? ConversationMessage::DIRECTION_OUTGOING : ConversationMessage::DIRECTION_INCOMING;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function senderType(string $direction, array $payload): string
    {
        if (ConversationMessage::DIRECTION_INCOMING === $direction) {
            return ConversationMessage::SENDER_CITIZEN;
        }

        return (bool) ($payload['private'] ?? false) ? ConversationMessage::SENDER_SYSTEM : ConversationMessage::SENDER_HUMAN_AGENT;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<int, array<string, mixed>>
     */
    private function attachments(array $payload): array
    {
        $attachments = $payload['attachments'] ?? [];
        if (!is_array($attachments)) {
            return [];
        }

        return array_values(array_filter($attachments, static fn (mixed $item): bool => is_array($item)));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function confidenceValue(array $payload): ?float
    {
        foreach (['confidence', 'custom_attributes.confidence', 'custom_attributes.ai_confidence', 'ai.confidence', 'intent.confidence'] as $path) {
            $value = $this->readPath($payload, $path);
            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string>   $paths
     */
    private function stringValue(array $payload, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = $this->readPath($payload, $path);
            if (is_scalar($value) && '' !== trim((string) $value)) {
                return trim((string) $value);
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string>   $paths
     *
     * @return array<string, mixed>|null
     */
    private function arrayValue(array $payload, array $paths): ?array
    {
        foreach ($paths as $path) {
            $value = $this->readPath($payload, $path);
            if (is_array($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string>   $paths
     */
    private function dateValue(array $payload, array $paths): ?\DateTimeImmutable
    {
        foreach ($paths as $path) {
            $value = $this->readPath($payload, $path);
            if (is_numeric($value)) {
                return (new \DateTimeImmutable())->setTimestamp((int) $value);
            }

            if (is_string($value) && '' !== trim($value)) {
                try {
                    return new \DateTimeImmutable($value);
                } catch (\Throwable) {
                }
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function readPath(array $payload, string $path): mixed
    {
        $current = $payload;
        foreach (explode('.', $path) as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * @param array<int, string> $labels
     *
     * @return array<int, string>
     */
    private function lowerLabels(array $labels): array
    {
        return array_map(static fn (string $label): string => mb_strtolower(trim($label)), $labels);
    }

    private function intValue(?string $value): ?int
    {
        return null !== $value && ctype_digit($value) ? (int) $value : null;
    }
}