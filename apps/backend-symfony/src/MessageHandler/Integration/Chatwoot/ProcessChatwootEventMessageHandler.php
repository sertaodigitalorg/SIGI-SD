<?php

namespace App\MessageHandler\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;
use App\Message\Integration\Chatwoot\ProcessChatwootEventMessage;
use App\Repository\Integration\Chatwoot\ChatwootMessageEventRepository;
use App\Service\Integration\Chatwoot\ChatwootEventProcessorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessChatwootEventMessageHandler
{
    public function __construct(
        private ChatwootMessageEventRepository $eventRepository,
        private ChatwootEventProcessorService $eventProcessor,
        private LockFactory $lockFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ProcessChatwootEventMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());
        if (!$event instanceof ChatwootMessageEvent || !$this->shouldProcess($event)) {
            return;
        }

        $lock = $this->lockFactory->createLock($this->lockKey($event), 300.0);
        $lock->acquire(true);

        try {
            $this->entityManager->refresh($event);
            if (!$this->shouldProcess($event)) {
                return;
            }

            $this->eventProcessor->process($event);
            $this->entityManager->flush();
        } finally {
            $lock->release();
        }
    }

    private function shouldProcess(ChatwootMessageEvent $event): bool
    {
        return in_array($event->getProcessingStatus(), [
            ChatwootMessageEvent::STATUS_RECEIVED,
            ChatwootMessageEvent::STATUS_FAILED,
        ], true);
    }

    private function lockKey(ChatwootMessageEvent $event): string
    {
        $accountId = $event->getChatwootAccount()?->getId() ?? 'unknown';
        $conversationId = $event->getExternalConversationId() ?: 'event-'.$event->getId();

        return sprintf('chatwoot-event-%s-%s', $accountId, $conversationId);
    }
}