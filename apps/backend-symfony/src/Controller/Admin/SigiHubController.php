<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\DashboardService;
use App\Service\Integration\Chatwoot\ChatwootRuntimeConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
final class SigiHubController extends AbstractController
{
    #[Route('/admin', name: 'admin_index', methods: ['GET'])]
    #[Route('/admin/hub-sigi', name: 'admin_sigi_hub', methods: ['GET'])]
    public function __invoke(DashboardService $dashboardService, ChatwootRuntimeConfig $runtimeConfig): Response
    {
        return $this->render('admin/hub/index.html.twig', [
            'attendance' => $dashboardService->getAttendanceMetrics(),
            'hubLinks' => $runtimeConfig->getHubLinks(),
        ]);
    }
}
