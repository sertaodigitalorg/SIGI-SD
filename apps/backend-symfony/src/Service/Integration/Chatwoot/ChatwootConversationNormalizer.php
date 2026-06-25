<?php

namespace App\Service\Integration\Chatwoot;

final class ChatwootConversationNormalizer
{
    /**
     * @param array<string, mixed> $payload
     */
    public function normalize(array $payload): ?ChatwootConversationData
    {
        $conversation = $this->conversationPayload($payload);
        $conversationId = $this->stringValue($conversation, ['id', 'conversation_id', 'display_id']);

        if (null === $conversationId) {
            return null;
        }

        $contact = $this->arrayValue($conversation, ['contact', 'meta.sender', 'sender']) ?? [];
        $assignee = $this->arrayValue($conversation, ['assignee', 'meta.assignee']) ?? [];
        $team = $this->arrayValue($conversation, ['team', 'meta.team']) ?? [];
        $inbox = $this->arrayValue($conversation, ['inbox']) ?? [];
        $customAttributes = $this->arrayValue($conversation, ['custom_attributes']) ?? [];

        return new ChatwootConversationData(
            conversationId: $conversationId,
            contactId: $this->stringValue($contact, ['id', 'contact_id']),
            contactName: $this->stringValue($contact, ['name', 'full_name']),
            contactHandle: $this->firstString([
                $this->stringValue($contact, ['email']),
                $this->stringValue($contact, ['phone_number']),
                $this->stringValue($contact, ['identifier']),
            ]),
            sourceChannel: $this->firstString([
                $this->stringValue($inbox, ['name']),
                $this->stringValue($conversation, ['inbox.name', 'inbox_name', 'channel']),
                $this->stringValue($inbox, ['channel_type']),
                $this->stringValue($conversation, ['source_id', 'inbox_id']),
            ]),
            subject: $this->firstString([
                $this->stringValue($conversation, ['subject']),
                $this->stringValue($conversation, ['additional_attributes.mail_subject']),
                $this->stringValue($conversation, ['messages.0.content']),
            ]),
            status: $this->stringValue($conversation, ['status']),
            labels: $this->labels($conversation),
            team: $this->stringValue($team, ['name']),
            agent: $this->stringValue($assignee, ['name', 'email']),
            priority: $this->firstString([
                $this->stringValue($conversation, ['priority']),
                $this->stringValue($customAttributes, ['priority', 'prioridade']),
            ]),
            createdAt: $this->dateValue($conversation, ['created_at']),
            updatedAt: $this->dateValue($conversation, ['updated_at', 'last_activity_at']),
            closedAt: $this->dateValue($conversation, ['resolved_at', 'closed_at']),
        );
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function conversationPayload(array $payload): array
    {
        $conversation = $this->arrayValue($payload, ['conversation']);

        return $conversation ?? $payload;
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
     *
     * @return array<int, string>
     */
    private function labels(array $payload): array
    {
        $value = $this->readPath($payload, 'labels') ?? $this->readPath($payload, 'label_list');

        if (!is_array($value)) {
            return [];
        }

        $labels = [];
        foreach ($value as $label) {
            if (is_scalar($label)) {
                $labels[] = trim((string) $label);
                continue;
            }

            if (is_array($label)) {
                $name = $this->stringValue($label, ['title', 'name']);
                if (null !== $name) {
                    $labels[] = $name;
                }
            }
        }

        return array_values(array_unique(array_filter($labels)));
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
     * @param array<int, string|null> $values
     */
    private function firstString(array $values): ?string
    {
        foreach ($values as $value) {
            if (null !== $value && '' !== trim($value)) {
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
