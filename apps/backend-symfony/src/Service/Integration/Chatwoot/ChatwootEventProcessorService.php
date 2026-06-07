<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;

final class ChatwootEventProcessorService
{
    public function process(ChatwootMessageEvent $event): void
    {
        try {
            $event->markProcessing();

            if (null === $event->getEventType()) {
                $event->markIgnored('Tipo de evento nao identificado no payload.');

                return;
            }

            $event->markProcessed();
        } catch (\Throwable $exception) {
            $event->markFailed('Erro ao processar evento Chatwoot: '.$exception->getMessage());
        }
    }
}
