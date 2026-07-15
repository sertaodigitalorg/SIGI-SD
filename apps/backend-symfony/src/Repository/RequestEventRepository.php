<?php

namespace App\Repository;

use App\Entity\RequestEvent;
use App\Entity\ServiceRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestEvent>
 */
final class RequestEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestEvent::class);
    }

    public function findLatestForRequest(ServiceRequest $serviceRequest): ?RequestEvent
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.serviceRequest = :request')
            ->setParameter('request', $serviceRequest)
            ->orderBy('event.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}