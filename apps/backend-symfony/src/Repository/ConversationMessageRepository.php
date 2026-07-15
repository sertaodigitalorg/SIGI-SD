<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\ConversationMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConversationMessage>
 */
final class ConversationMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationMessage::class);
    }

    public function findOneByChatwootMessage(Conversation $conversation, string $messageId): ?ConversationMessage
    {
        return $this->findOneBy([
            'conversation' => $conversation,
            'chatwootMessageId' => $messageId,
        ]);
    }
}