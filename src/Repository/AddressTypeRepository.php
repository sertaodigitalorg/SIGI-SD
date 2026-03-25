<?php

namespace App\Repository;

use App\Entity\AddressType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AddressType|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<AddressType>
 */
class AddressTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressType::class);
    }
}