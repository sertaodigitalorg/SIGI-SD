<?php

namespace App\Service\Integration\Chatwoot;

final readonly class ChatwootConversationData
{
    /**
     * @param array<int, string> $labels
     */
    public function __construct(
        public string $conversationId,
        public ?string $contactId,
        public ?string $contactName,
        public ?string $contactHandle,
        public ?string $sourceChannel,
        public ?string $subject,
        public ?string $status,
        public array $labels,
        public ?string $team,
        public ?string $agent,
        public ?string $priority,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
        public ?\DateTimeImmutable $closedAt,
    ) {
    }
}
