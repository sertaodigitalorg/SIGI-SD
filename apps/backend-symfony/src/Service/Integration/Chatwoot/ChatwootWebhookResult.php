<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;

final readonly class ChatwootWebhookResult
{
    public function __construct(
        private int $httpStatus,
        private string $status,
        private ?ChatwootMessageEvent $event = null,
    ) {
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getEvent(): ?ChatwootMessageEvent
    {
        return $this->event;
    }
}
