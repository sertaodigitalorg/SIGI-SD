<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\ServiceRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceRequest>
 */
final class ServiceRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequest::class);
    }

    public function findOpenByConversation(Conversation $conversation): ?ServiceRequest
    {
        return $this->createQueryBuilder('request')
            ->andWhere('request.conversation = :conversation')
            ->andWhere('request.status = :status')
            ->setParameter('conversation', $conversation)
            ->setParameter('status', ServiceRequest::STATUS_OPEN)
            ->orderBy('request.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}