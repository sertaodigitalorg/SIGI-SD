<?php

namespace App\Repository;

use App\Entity\InteractionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InteractionStatus|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<InteractionStatus>
 */
class InteractionStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InteractionStatus::class);
    }
}