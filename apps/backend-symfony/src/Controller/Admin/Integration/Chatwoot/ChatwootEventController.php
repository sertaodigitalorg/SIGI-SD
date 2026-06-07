<?php

namespace App\Controller\Admin\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;
use App\Entity\User;
use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;
use App\Repository\Integration\Chatwoot\ChatwootMessageEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/integrations/chatwoot/events'), IsGranted(User::ROLE_ADMIN)]
final class ChatwootEventController extends AbstractController
{
    #[Route('', name: 'admin_chatwoot_event_index', methods: ['GET'])]
    public function index(
        Request $request,
        ChatwootMessageEventRepository $eventRepository,
        ChatwootAccountRepository $accountRepository,
    ): Response {
        $account = null;
        $accountId = $request->query->get('account');
        if (null !== $accountId && '' !== $accountId) {
            $account = $accountRepository->find((int) $accountId);
        }

        $status = $request->query->get('status');
        $eventType = $request->query->get('eventType');

        return $this->render('admin/integration/chatwoot/events/index.html.twig', [
            'events' => $eventRepository->createFilteredQueryBuilder($status, $eventType, $account)->getQuery()->getResult(),
            'accounts' => $accountRepository->createAdminListQueryBuilder()->getQuery()->getResult(),
            'event_types' => $eventRepository->findDistinctEventTypes(),
            'statuses' => ChatwootMessageEvent::getAvailableStatuses(),
            'filters' => [
                'status' => $status,
                'eventType' => $eventType,
                'account' => $account?->getId(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'admin_chatwoot_event_show', methods: ['GET'])]
    public function show(ChatwootMessageEvent $event): Response
    {
        return $this->render('admin/integration/chatwoot/events/show.html.twig', [
            'event' => $event,
        ]);
    }
}
