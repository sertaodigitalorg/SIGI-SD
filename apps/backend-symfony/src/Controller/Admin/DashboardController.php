<?php

namespace App\Controller\Admin;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/dashboard', name: 'admin_dashboard_index')]
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractController
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function __invoke(): Response
    {
        $organization = $this->dashboardService->getOrganizationMetrics();
        $person = $this->dashboardService->getPersonMetrics();
        $consolidated = $this->dashboardService->getConsolidatedMetrics();
        $overdueAlerts = $this->dashboardService->getOverdueAlerts(20);
        $attendance = $this->dashboardService->getAttendanceMetrics();

        return $this->render('admin/dashboard/index.html.twig', [
            'organization' => $organization,
            'person' => $person,
            'consolidated' => $consolidated,
            'overdueAlerts' => $overdueAlerts,
            'attendance' => $attendance,
        ]);
    }
}
