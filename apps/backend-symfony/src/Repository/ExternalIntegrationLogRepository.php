<?php

namespace App\Repository;

use App\Entity\ExternalIntegrationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalIntegrationLog>
 */
final class ExternalIntegrationLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalIntegrationLog::class);
    }
}