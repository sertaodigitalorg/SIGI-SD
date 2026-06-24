<?php

namespace App\Repository;

use App\Entity\AttendanceProtocol;
use App\Entity\ProtocolSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttendanceProtocol>
 */
final class AttendanceProtocolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttendanceProtocol::class);
    }

    public function findOneByConversationId(string $conversationId): ?AttendanceProtocol
    {
        return $this->findOneBy(['chatwootConversationId' => $conversationId]);
    }

    public function getNextSequenceNumber(string $scope, \DateTimeImmutable $date): int
    {
        $queryBuilder = $this->createQueryBuilder('protocol')
            ->select('MAX(protocol.sequenceNumber)')
            ->andWhere('protocol.sequenceScope = :scope')
            ->setParameter('scope', $scope);

        if (ProtocolSettings::SCOPE_DAILY === $scope) {
            $queryBuilder
                ->andWhere('protocol.sequenceDate = :date')
                ->setParameter('date', $date->format('Y-m-d'));
        }

        return ((int) $queryBuilder->getQuery()->getSingleScalarResult()) + 1;
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('protocol')
            ->orderBy('protocol.createdAt', 'DESC');

        if (($filters['status'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.status = :status')->setParameter('status', $filters['status']);
        }

        if (($filters['channel'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.sourceChannel = :channel')->setParameter('channel', $filters['channel']);
        }

        if (($filters['team'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.responsibleTeam = :team')->setParameter('team', $filters['team']);
        }

        if (($filters['priority'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.priority = :priority')->setParameter('priority', $filters['priority']);
        }

        if (($filters['label'] ?? '') !== '') {
            $queryBuilder->andWhere('LOWER(protocol.labelsText) LIKE :label')->setParameter('label', '%'.mb_strtolower((string) $filters['label']).'%');
        }

        if (($filters['from'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.createdAt >= :from')->setParameter('from', new \DateTimeImmutable($filters['from'].' 00:00:00'));
        }

        if (($filters['to'] ?? '') !== '') {
            $queryBuilder->andWhere('protocol.createdAt <= :to')->setParameter('to', new \DateTimeImmutable($filters['to'].' 23:59:59'));
        }

        return $queryBuilder;
    }

    public function countByStatus(?string $status = null): int
    {
        $queryBuilder = $this->createQueryBuilder('protocol')->select('COUNT(protocol.id)');

        if (null !== $status) {
            $queryBuilder->andWhere('protocol.status = :status')->setParameter('status', $status);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function countCreatedBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return (int) $this->createQueryBuilder('protocol')
            ->select('COUNT(protocol.id)')
            ->andWhere('protocol.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countHighPriority(): int
    {
        return (int) $this->createQueryBuilder('protocol')
            ->select('COUNT(protocol.id)')
            ->andWhere('LOWER(protocol.priority) IN (:priorities)')
            ->setParameter('priorities', ['alta', 'high', 'urgent', 'urgente'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array<int, array{label: string, total: int}>
     */
    public function countGroupedBy(string $field): array
    {
        $allowedFields = ['sourceChannel', 'status', 'responsibleTeam', 'responsibleAgent', 'priority'];
        if (!in_array($field, $allowedFields, true)) {
            return [];
        }

        $rows = $this->createQueryBuilder('protocol')
            ->select(sprintf('protocol.%s AS label, COUNT(protocol.id) AS total', $field))
            ->groupBy(sprintf('protocol.%s', $field))
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): array => [
            'label' => $row['label'] ?: 'Nao informado',
            'total' => (int) $row['total'],
        ], $rows);
    }

    /**
     * @return array<int, array{label: string, total: int}>
     */
    public function countByLabel(): array
    {
        $totals = [];
        foreach ($this->createQueryBuilder('protocol')->select('protocol.labels')->getQuery()->getScalarResult() as $row) {
            $labels = $row['labels'] ?? [];
            if (is_string($labels)) {
                $decoded = json_decode($labels, true);
                $labels = is_array($decoded) ? $decoded : [];
            }

            foreach ($labels as $label) {
                if (!is_scalar($label) || '' === trim((string) $label)) {
                    continue;
                }

                $normalized = trim((string) $label);
                $totals[$normalized] = ($totals[$normalized] ?? 0) + 1;
            }
        }

        arsort($totals);

        return array_map(static fn (string $label, int $total): array => [
            'label' => $label,
            'total' => $total,
        ], array_keys($totals), array_values($totals));
    }

    /**
     * @return array<int, string>
     */
    public function findDistinctValues(string $field): array
    {
        $allowedFields = ['sourceChannel', 'status', 'responsibleTeam', 'priority'];
        if (!in_array($field, $allowedFields, true)) {
            return [];
        }

        $rows = $this->createQueryBuilder('protocol')
            ->select(sprintf('DISTINCT protocol.%s AS value', $field))
            ->andWhere(sprintf('protocol.%s IS NOT NULL', $field))
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_values(array_filter(array_map(static fn (array $row): ?string => $row['value'] ?? null, $rows)));
    }
}
