<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;

final class ChatwootWebhookEventInspector
{
    /**
     * @var array<int, string>
     */
    private const PROCESSABLE_EVENTS = [
        'message_created',
        'conversation_created',
        'conversation_updated',
        'conversation_status_changed',
        'conversation_resolved',
        'conversation_opened',
    ];

    /**
     * @var array<int, string>
     */
    private const SIGI_MESSAGE_MARKERS = [
        'protocolo sigi gerado automaticamente',
        'seu protocolo de atendimento',
        'seu numero de protocolo',
        'protocolo-enviado-whatsapp',
        'protocolo-enviado-instagram',
    ];

    public function processabilityReason(ChatwootMessageEvent $event): ?string
    {
        $eventType = $this->normalizeEventType($event->getEventType());
        if (null === $eventType) {
            return 'Tipo de evento nao identificado no payload.';
        }

        if (!in_array($eventType, self::PROCESSABLE_EVENTS, true)) {
            return sprintf('Evento Chatwoot nao processavel: %s.', $eventType);
        }

        if ($this->isSigiGeneratedMessage($event)) {
            return 'Mensagem enviada pelo proprio SIGI ignorada para evitar loop.';
        }

        return null;
    }

    public function normalizeEventType(?string $eventType): ?string
    {
        if (null === $eventType) {
            return null;
        }

        $eventType = mb_strtolower(trim($eventType));
        $eventType = str_replace(['-', ' '], '_', $eventType);

        return '' === $eventType ? null : $eventType;
    }

    public function isSigiGeneratedMessage(ChatwootMessageEvent $event): bool
    {
        $eventType = $this->normalizeEventType($event->getEventType());
        if ('message_created' !== $eventType) {
            return false;
        }

        $message = $this->messagePayload($event->getRawPayload());
        if ([] === $message || 'outgoing' !== $this->stringValue($message, ['message_type', 'direction'])) {
            return false;
        }

        if (true === ($message['private'] ?? false)) {
            return true;
        }

        $content = mb_strtolower($this->stringValue($message, ['content']) ?? '');
        foreach (self::SIGI_MESSAGE_MARKERS as $marker) {
            if (str_contains($content, $marker)) {
                return true;
            }
        }

        $sender = $this->arrayValue($message, ['sender']) ?? [];
        $senderText = mb_strtolower(implode(' ', array_filter([
            $this->stringValue($sender, ['name']),
            $this->stringValue($sender, ['email']),
            $this->stringValue($sender, ['identifier']),
            $this->stringValue($sender, ['custom_attributes.origin']),
            $this->stringValue($sender, ['custom_attributes.source']),
        ])));

        return str_contains($senderText, 'sigi');
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
     */
    private function readPath(array $payload, string $path): mixed
    {
        $current = $payload;
        foreach (explode('.', $path) as $segment) {
            if (is_array($current) && ctype_digit($segment)) {
                $index = (int) $segment;
                if (!array_key_exists($index, $current)) {
                    return null;
                }

                $current = $current[$index];
                continue;
            }

            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }
}