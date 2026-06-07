<?php

namespace App\Repository\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatwootAccount>
 */
final class ChatwootAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatwootAccount::class);
    }

    public function createAdminListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('account')
            ->orderBy('account.isActive', 'DESC')
            ->addOrderBy('account.name', 'ASC');
    }
}
