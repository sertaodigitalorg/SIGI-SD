<?php

namespace App\Repository;

use App\Entity\CoverageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoverageType|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<CoverageType>
 */
class CoverageTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoverageType::class);
    }
}