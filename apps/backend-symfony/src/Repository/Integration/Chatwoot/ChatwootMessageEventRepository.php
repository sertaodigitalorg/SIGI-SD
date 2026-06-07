<?php

namespace App\Repository\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatwootMessageEvent>
 */
final class ChatwootMessageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatwootMessageEvent::class);
    }

    public function findDuplicate(
        ChatwootAccount $account,
        ?string $eventType,
        ?string $externalConversationId,
        ?string $externalMessageId,
        string $payloadHash,
    ): ?ChatwootMessageEvent {
        $queryBuilder = $this->createQueryBuilder('event')
            ->andWhere('event.chatwootAccount = :account')
            ->andWhere('event.payloadHash = :payloadHash')
            ->setParameter('account', $account)
            ->setParameter('payloadHash', $payloadHash)
            ->setMaxResults(1);

        $this->applyNullableFilter($queryBuilder, 'event.eventType', 'eventType', $eventType);
        $this->applyNullableFilter($queryBuilder, 'event.externalConversationId', 'externalConversationId', $externalConversationId);
        $this->applyNullableFilter($queryBuilder, 'event.externalMessageId', 'externalMessageId', $externalMessageId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function createFilteredQueryBuilder(?string $status, ?string $eventType, ?ChatwootAccount $account): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('event')
            ->leftJoin('event.chatwootAccount', 'account')
            ->addSelect('account')
            ->orderBy('event.createdAt', 'DESC');

        if (null !== $status && '' !== $status) {
            $queryBuilder
                ->andWhere('event.processingStatus = :status')
                ->setParameter('status', $status);
        }

        if (null !== $eventType && '' !== $eventType) {
            $queryBuilder
                ->andWhere('event.eventType = :eventType')
                ->setParameter('eventType', $eventType);
        }

        if (null !== $account) {
            $queryBuilder
                ->andWhere('event.chatwootAccount = :account')
                ->setParameter('account', $account);
        }

        return $queryBuilder;
    }

    /**
     * @return array<int, string>
     */
    public function findDistinctEventTypes(): array
    {
        $rows = $this->createQueryBuilder('event')
            ->select('DISTINCT event.eventType AS eventType')
            ->andWhere('event.eventType IS NOT NULL')
            ->orderBy('event.eventType', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_values(array_filter(array_map(static fn (array $row): ?string => $row['eventType'] ?? null, $rows)));
    }

    private function applyNullableFilter(QueryBuilder $queryBuilder, string $field, string $parameter, ?string $value): void
    {
        if (null === $value || '' === $value) {
            $queryBuilder->andWhere($field.' IS NULL');

            return;
        }

        $queryBuilder
            ->andWhere($field.' = :'.$parameter)
            ->setParameter($parameter, $value);
    }
}
