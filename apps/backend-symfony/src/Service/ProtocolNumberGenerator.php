<?php

namespace App\Service;

use App\Entity\AttendanceProtocol;
use App\Repository\AttendanceProtocolRepository;
use App\Repository\ProtocolSettingsRepository;

final readonly class ProtocolNumberGenerator
{
    public function __construct(
        private AttendanceProtocolRepository $protocolRepository,
        private ProtocolSettingsRepository $settingsRepository,
    ) {
    }

    public function assign(AttendanceProtocol $protocol, ?\DateTimeImmutable $date = null): void
    {
        $date ??= new \DateTimeImmutable();
        $settings = $this->settingsRepository->getOrCreate();
        $scope = $settings->getSequenceScope();
        $sequenceNumber = $this->protocolRepository->getNextSequenceNumber($scope, $date);

        $protocol
            ->setSequenceScope($scope)
            ->setSequenceDate($date)
            ->setSequenceNumber($sequenceNumber)
            ->setProtocolCode(sprintf('%s%06d', $date->format('Ymd'), $sequenceNumber));
    }
}
