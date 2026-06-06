<?php

namespace App\Repository;

use App\Entity\ContactStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactStatus|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<ContactStatus>
 */
class ContactStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactStatus::class);
    }
}