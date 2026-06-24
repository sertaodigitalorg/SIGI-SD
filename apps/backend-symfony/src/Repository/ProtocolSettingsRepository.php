<?php

namespace App\Repository;

use App\Entity\ProtocolSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProtocolSettings>
 */
final class ProtocolSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProtocolSettings::class);
    }

    public function getOrCreate(): ProtocolSettings
    {
        $settings = $this->findOneBy([], ['id' => 'ASC']);

        return $settings ?? new ProtocolSettings();
    }
}
