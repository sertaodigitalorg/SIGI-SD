<?php

namespace App\Service;

use App\Repository\OrganizationContactInteractionRepository;
use App\Repository\PersonContactInteractionRepository;

class DashboardService
{
    public function __construct(
        private OrganizationContactInteractionRepository $organizationRepository,
        private PersonContactInteractionRepository $personRepository
    ) {
    }

    public function getOrganizationMetrics(): array
    {
        $now = new \DateTimeImmutable();

        return [
            'totalInteractions' => $this->organizationRepository->countTotalInteractions(),
            'overdueFollowUps' => $this->organizationRepository->countOverdueFollowUps($now),
            'responseRate' => $this->organizationRepository->getResponseRate(),
            'recentInteractions' => $this->organizationRepository->getRecentInteractions(10),
            'overdueInteractions' => $this->organizationRepository->getOverdueInteractions($now, 20),
        ];
    }

    public function getPersonMetrics(): array
    {
        $now = new \DateTimeImmutable();

        return [
            'totalInteractions' => $this->personRepository->countTotalInteractions(),
            'overdueFollowUps' => $this->personRepository->countOverdueFollowUps($now),
            'responseRate' => $this->personRepository->getResponseRate(),
            'recentInteractions' => $this->personRepository->getRecentInteractions(10),
            'overdueInteractions' => $this->personRepository->getOverdueInteractions($now, 20),
        ];
    }

    public function getConsolidatedMetrics(): array
    {
        $org = $this->getOrganizationMetrics();
        $person = $this->getPersonMetrics();

        $totalInteractions = $org['totalInteractions'] + $person['totalInteractions'];
        $totalOverdue = $org['overdueFollowUps'] + $person['overdueFollowUps'];

        $overallResponseRate = 0.0;
        if ($totalInteractions > 0) {
            $weightedSum = ($org['responseRate'] * $org['totalInteractions'] / 100)
                + ($person['responseRate'] * $person['totalInteractions'] / 100);
            $overallResponseRate = round(($weightedSum / $totalInteractions) * 100, 2);
        }

        return [
            'totalInteractions' => $totalInteractions,
            'totalOverdueFollowUps' => $totalOverdue,
            'overallResponseRate' => $overallResponseRate,
        ];
    }

    public function getOverdueAlerts(int $limit = 20): array
    {
        $now = new \DateTimeImmutable();

        $personOverdue = $this->personRepository->getOverdueInteractions($now, $limit);
        $orgOverdue = $this->organizationRepository->getOverdueInteractions($now, $limit);

        $alerts = [];

        foreach ($personOverdue as $interaction) {
            $daysLate = $interaction->getNextContactAt() ? $now->diff($interaction->getNextContactAt())->days : 0;
            $alerts[] = [
                'type' => 'PF',
                'name' => $interaction->getPersonContact()?->getPerson()?->getFullName() ?: '-',
                'contact' => sprintf('%s | %s', $interaction->getPersonContact()?->getContactType()?->getName() ?: '-', $interaction->getPersonContact()?->getValue() ?: '-'),
                'nextContactAt' => $interaction->getNextContactAt(),
                'daysLate' => $daysLate,
                'performedBy' => $interaction->getPerformedBy()?->getFullName() ?: '-',
                'interaction' => $interaction,
            ];
        }

        foreach ($orgOverdue as $interaction) {
            $daysLate = $interaction->getNextContactAt() ? $now->diff($interaction->getNextContactAt())->days : 0;
            $alerts[] = [
                'type' => 'PJ',
                'name' => $interaction->getOrganizationContact()?->getOrganization()?->getLegalName() ?: '-',
                'contact' => sprintf('%s | %s', $interaction->getOrganizationContact()?->getContactType()?->getName() ?: '-', $interaction->getOrganizationContact()?->getValue() ?: '-'),
                'nextContactAt' => $interaction->getNextContactAt(),
                'daysLate' => $daysLate,
                'performedBy' => $interaction->getPerformedBy()?->getFullName() ?: '-',
                'interaction' => $interaction,
            ];
        }

        usort($alerts, static fn ($a, $b) => $a['nextContactAt'] <=> $b['nextContactAt']);

        return array_slice($alerts, 0, $limit);
    }
}
