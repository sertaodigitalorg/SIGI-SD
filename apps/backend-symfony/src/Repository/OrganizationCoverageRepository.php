<?php

namespace App\Repository;

use App\Entity\OrganizationCoverage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<OrganizationCoverage>
 */
class OrganizationCoverageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationCoverage::class);
    }
}