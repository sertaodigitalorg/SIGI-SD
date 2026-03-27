<?php

namespace App\Repository;

use App\Entity\OrganizationContactInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<OrganizationContactInteraction>
 */
class OrganizationContactInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationContactInteraction::class);
    }

    public function countOverdueFollowUps(\DateTimeImmutable $now): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.nextContactAt IS NOT NULL')
            ->andWhere('i.nextContactAt < :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRecentInteractions(int $limit = 10): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.organizationContact', 'oc')
            ->join('oc.organization', 'o')
            ->addSelect('oc')
            ->addSelect('o')
            ->orderBy('i.contactedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getResponseRate(): float
    {
        $total = (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($total === 0) {
            return 0.0;
        }

        $answered = (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.responseReceived = :received')
            ->setParameter('received', true)
            ->getQuery()
            ->getSingleScalarResult();

        return round(($answered / $total) * 100, 2);
    }

    public function countTotalInteractions(): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOverdueInteractions(\DateTimeImmutable $now, int $limit = 50): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.organizationContact', 'oc')
            ->join('oc.organization', 'o')
            ->addSelect('oc')
            ->addSelect('o')
            ->where('i.nextContactAt IS NOT NULL')
            ->andWhere('i.nextContactAt < :now')
            ->setParameter('now', $now)
            ->orderBy('i.nextContactAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}