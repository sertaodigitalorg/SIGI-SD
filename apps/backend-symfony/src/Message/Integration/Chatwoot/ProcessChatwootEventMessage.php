<?php

namespace App\Message\Integration\Chatwoot;

final readonly class ProcessChatwootEventMessage
{
    public function __construct(private int $eventId)
    {
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }
}