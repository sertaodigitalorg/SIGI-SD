<?php

namespace App\Repository;

use App\Entity\ContactIssueType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactIssueType|null findOneByName(string $name)
 *
 * @template-extends ServiceEntityRepository<ContactIssueType>
 */
class ContactIssueTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactIssueType::class);
    }
}