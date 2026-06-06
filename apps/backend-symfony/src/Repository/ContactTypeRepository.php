<?php

namespace App\Repository;

use App\Entity\ContactType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactType|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<ContactType>
 */
class ContactTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactType::class);
    }
}