<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;

final class ChatwootEventProcessorService
{
    public function __construct(
        private readonly ChatwootConversationSyncService $conversationSyncService,
        private readonly ChatwootWebhookEventInspector $eventInspector,
    ) {
    }

    public function process(ChatwootMessageEvent $event): void
    {
        try {
            $event->markProcessing();

            $ignoreReason = $this->eventInspector->processabilityReason($event);
            if (null !== $ignoreReason) {
                $event->markIgnored($ignoreReason);

                return;
            }

            $protocol = $this->conversationSyncService->syncPayload($event->getRawPayload(), $event->getChatwootAccount());
            if (null === $protocol) {
                $event->markIgnored('Evento sem conversa Chatwoot sincronizavel.');

                return;
            }

            $event->markProcessed();
        } catch (\Throwable $exception) {
            $event->markFailed('Erro ao processar evento Chatwoot: '.$exception->getMessage());
        }
    }
}