<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Person|null findOneByCpf(string $cpf)
 * @method Person|null findOneByEmail(string $email)
 *
 * @template-extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function findOneByChatwootContactId(string $chatwootContactId): ?Person
    {
        return $this->findOneBy(['chatwootContactId' => $chatwootContactId]);
    }

    public function findOneByPrimaryEmail(string $email): ?Person
    {
        return $this->findOneBy(['primaryEmail' => mb_strtolower(trim($email))]);
    }

    public function findOneByPrimaryPhone(string $phone): ?Person
    {
        return $this->findOneBy(['primaryPhone' => trim($phone)]);
    }
}
