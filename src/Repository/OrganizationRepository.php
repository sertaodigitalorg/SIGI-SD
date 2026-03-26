<?php

namespace App\Repository;

use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organization|null findOneByCnpj(string $cnpj)
 * @method Organization|null findOneByEmail(string $email)
 *
 * @template-extends ServiceEntityRepository<Organization>
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function createAlphabeticalQueryBuilder(string $alias = 'o'): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->leftJoin($alias.'.organizationType', 'ot')
            ->addSelect('ot')
            ->leftJoin($alias.'.parent', 'p')
            ->addSelect('p')
            ->orderBy($alias.'.legalName', 'ASC');
    }
}
