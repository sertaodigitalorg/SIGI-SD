<?php

namespace App\Controller\Admin;

use App\Entity\ProtocolSettings;
use App\Entity\User;
use App\Pagination\Paginator;
use App\Repository\AttendanceProtocolRepository;
use App\Repository\ProtocolSettingsRepository;
use App\Service\DashboardService;
use App\Service\Integration\Chatwoot\ChatwootRuntimeConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/atendimentos', name: 'admin_attendance_')]
#[IsGranted(User::ROLE_ADMIN)]
final class AttendanceProtocolController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        AttendanceProtocolRepository $protocolRepository,
        ChatwootRuntimeConfig $chatwootRuntimeConfig,
    ): Response {
        $filters = [
            'from' => (string) $request->query->get('from', ''),
            'to' => (string) $request->query->get('to', ''),
            'channel' => (string) $request->query->get('channel', ''),
            'status' => (string) $request->query->get('status', ''),
            'team' => (string) $request->query->get('team', ''),
            'label' => (string) $request->query->get('label', ''),
            'priority' => (string) $request->query->get('priority', ''),
        ];

        $paginator = (new Paginator($protocolRepository->createFilteredQueryBuilder($filters), 25))
            ->paginate((int) $request->query->get('page', 1));

        return $this->render('admin/attendance/index.html.twig', [
            'paginator' => $paginator,
            'filters' => $filters,
            'channels' => $protocolRepository->findDistinctValues('sourceChannel'),
            'statuses' => $protocolRepository->findDistinctValues('status'),
            'teams' => $protocolRepository->findDistinctValues('responsibleTeam'),
            'priorities' => $protocolRepository->findDistinctValues('priority'),
            'chatwootRuntimeConfig' => $chatwootRuntimeConfig,
        ]);
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(DashboardService $dashboardService): Response
    {
        return $this->render('admin/attendance/dashboard.html.twig', [
            'attendance' => $dashboardService->getAttendanceMetrics(),
        ]);
    }

    #[Route('/configuracao', name: 'settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request,
        ProtocolSettingsRepository $settingsRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $settings = $settingsRepository->getOrCreate();

        if ('POST' === $request->getMethod()) {
            if (!$this->isCsrfTokenValid('protocol_settings', (string) $request->request->get('_token'))) {
                $this->addFlash('warning', 'Nao foi possivel validar a solicitacao.');

                return $this->redirectToRoute('admin_attendance_settings');
            }

            $settings->setSequenceScope((string) $request->request->get('sequence_scope', ProtocolSettings::SCOPE_DAILY));
            $settings->touch();
            $entityManager->persist($settings);
            $entityManager->flush();

            $this->addFlash('success', 'Configuracao de protocolo atualizada.');

            return $this->redirectToRoute('admin_attendance_settings');
        }

        return $this->render('admin/attendance/settings.html.twig', [
            'settings' => $settings,
            'scopes' => ProtocolSettings::getAvailableScopes(),
        ]);
    }
}
