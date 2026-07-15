<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;
use App\Message\Integration\Chatwoot\ProcessChatwootEventMessage;
use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;
use App\Repository\Integration\Chatwoot\ChatwootMessageEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChatwootWebhookService
{
    public function __construct(
        private readonly ChatwootAccountRepository $accountRepository,
        private readonly ChatwootMessageEventRepository $eventRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly ChatwootWebhookEventInspector $eventInspector,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function receive(int $accountId, array $payload, ?string $secret): ChatwootWebhookResult
    {
        $account = $this->accountRepository->find($accountId);
        if (null === $account || !$account->isActive()) {
            return new ChatwootWebhookResult(Response::HTTP_NOT_FOUND, 'not_found');
        }

        if (null === $secret || null === $account->getWebhookSecret() || !hash_equals($account->getWebhookSecret(), $secret)) {
            return new ChatwootWebhookResult(Response::HTTP_FORBIDDEN, 'forbidden');
        }

        $eventType = $this->eventInspector->normalizeEventType($this->extractString($payload, ['event', 'event_type', 'message_type']));
        $externalConversationId = $this->extractString($payload, ['conversation.id', 'conversation_id', 'id']);
        $externalMessageId = $this->extractString($payload, ['message.id', 'message_id']);

        if (null === $externalMessageId && $this->looksLikeMessageEvent($eventType, $payload)) {
            $externalMessageId = $this->extractString($payload, ['id']);
        }

        $payloadHash = $this->hashPayload($payload);
        $duplicate = $this->eventRepository->findDuplicate(
            $account,
            $eventType,
            $externalConversationId,
            $externalMessageId,
            $payloadHash,
        );

        if (null !== $duplicate) {
            return new ChatwootWebhookResult(Response::HTTP_OK, 'ignored', $duplicate);
        }

        $event = (new ChatwootMessageEvent())
            ->setChatwootAccount($account)
            ->setEventType($eventType)
            ->setExternalConversationId($externalConversationId)
            ->setExternalMessageId($externalMessageId)
            ->setPayloadHash($payloadHash)
            ->setRawPayload($payload);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        if (null !== $event->getId()) {
            $this->messageBus->dispatch(new ProcessChatwootEventMessage($event->getId()));
        }

        return new ChatwootWebhookResult(Response::HTTP_ACCEPTED, 'queued', $event);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string>   $paths
     */
    private function extractString(array $payload, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = $this->readPath($payload, $path);
            if (is_scalar($value) && '' !== trim((string) $value)) {
                return (string) $value;
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
     * @param array<string, mixed> $payload
     */
    private function hashPayload(array $payload): string
    {
        $encodedPayload = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return hash('sha256', $encodedPayload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function looksLikeMessageEvent(?string $eventType, array $payload): bool
    {
        if (null !== $eventType && str_contains(strtolower($eventType), 'message')) {
            return true;
        }

        return isset($payload['message']) || isset($payload['message_type']) || isset($payload['content']);
    }
}