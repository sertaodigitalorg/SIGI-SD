<?php

namespace App\Repository;

use App\Entity\PersonContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<PersonContact>
 */
class PersonContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonContact::class);
    }

    public function findOneByValue(string $value): ?PersonContact
    {
        return $this->findOneBy(['value' => trim($value)]);
    }
}
