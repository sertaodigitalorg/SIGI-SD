<?php

namespace App\Repository;

use App\Entity\Person;
use App\Entity\PersonOrganization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<PersonOrganization>
 */
class PersonOrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonOrganization::class);
    }

    public function findByPersonWithRelations(Person $person): array
    {
        return $this->createQueryBuilder('po')
            ->leftJoin('po.organization', 'organization')
            ->addSelect('organization')
            ->leftJoin('po.personOrganizationRoles', 'personOrganizationRoles')
            ->addSelect('personOrganizationRoles')
            ->leftJoin('personOrganizationRoles.role', 'role')
            ->addSelect('role')
            ->where('po.person = :person')
            ->setParameter('person', $person)
            ->orderBy('CASE WHEN po.endDate IS NULL THEN 0 ELSE 1 END', 'ASC')
            ->addOrderBy('po.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}