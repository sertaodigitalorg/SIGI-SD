<?php

namespace App\Repository;

use App\Entity\ThematicArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThematicArea|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<ThematicArea>
 */
class ThematicAreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThematicArea::class);
    }
}