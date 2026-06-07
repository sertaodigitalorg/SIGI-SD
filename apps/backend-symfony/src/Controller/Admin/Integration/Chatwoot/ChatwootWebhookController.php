<?php

namespace App\Controller\Admin\Integration\Chatwoot;

use App\Service\Integration\Chatwoot\ChatwootWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/integrations/chatwoot')]
final class ChatwootWebhookController extends AbstractController
{
    #[Route('/webhook/{accountId}', name: 'admin_chatwoot_webhook_receive', methods: ['POST'])]
    public function receive(int $accountId, Request $request, ChatwootWebhookService $webhookService): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->json(['success' => false, 'message' => 'Payload invalido.'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_array($payload)) {
            return $this->json(['success' => false, 'message' => 'Payload invalido.'], Response::HTTP_BAD_REQUEST);
        }

        $secret = $request->headers->get('X-SIGI-CHATWOOT-SECRET')
            ?: $request->headers->get('X-Chatwoot-Webhook-Secret');

        $result = $webhookService->receive($accountId, $payload, $secret);

        return $this->json([
            'success' => Response::HTTP_OK === $result->getHttpStatus(),
            'status' => $result->getStatus(),
        ], $result->getHttpStatus());
    }
}
