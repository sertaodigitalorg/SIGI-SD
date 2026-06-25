<?php

namespace App\Controller;

use App\Service\AI\LocalAiAssistantService;
use App\Service\Integration\Chatwoot\ChatwootApiClient;
use App\Service\Integration\Chatwoot\ChatwootConversationSyncService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chatwoot/assistant', name: 'chatwoot_assistant_')]
final class ChatwootAssistantController extends AbstractController
{
    #[Route('', name: 'panel', methods: ['GET'])]
    public function panel(Request $request): Response
    {
        $response = $this->render('chatwoot_assistant/panel.html.twig', [
            'conversationId' => $this->extractConversationId($request),
        ]);
        $response->headers->remove('X-Frame-Options');

        return $response;
    }

    #[Route('/suggest', name: 'suggest', methods: ['POST'])]
    public function suggest(Request $request, ChatwootApiClient $apiClient, LocalAiAssistantService $assistant): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $conversationId = $this->requiredConversationId($payload);
        if (null === $conversationId) {
            return $this->json(['ok' => false, 'error' => 'Informe a conversa.'], Response::HTTP_BAD_REQUEST);
        }

        $conversation = $apiClient->getConversation(null, $conversationId);
        $messages = $apiClient->getConversationMessages(null, $conversationId);
        $suggestion = $assistant->suggestReply($conversation, $messages, $this->stringValue($payload['instruction'] ?? null));

        return $this->json(['ok' => true, 'suggestion' => $suggestion]);
    }

    #[Route('/private-note', name: 'private_note', methods: ['POST'])]
    public function privateNote(Request $request, ChatwootApiClient $apiClient): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $conversationId = $this->requiredConversationId($payload);
        $content = $this->stringValue($payload['content'] ?? null);

        if (null === $conversationId || null === $content) {
            return $this->json(['ok' => false, 'error' => 'Informe conversa e texto.'], Response::HTTP_BAD_REQUEST);
        }

        $apiClient->createPrivateNote(null, $conversationId, $content);

        return $this->json(['ok' => true]);
    }

    #[Route('/reply', name: 'reply', methods: ['POST'])]
    public function reply(Request $request, ChatwootApiClient $apiClient): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $conversationId = $this->requiredConversationId($payload);
        $content = $this->stringValue($payload['content'] ?? null);

        if (null === $conversationId || null === $content) {
            return $this->json(['ok' => false, 'error' => 'Informe conversa e texto.'], Response::HTTP_BAD_REQUEST);
        }

        $apiClient->createPublicMessage(null, $conversationId, $content);

        return $this->json(['ok' => true]);
    }

    #[Route('/labels', name: 'labels', methods: ['POST'])]
    public function labels(Request $request, ChatwootApiClient $apiClient): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $conversationId = $this->requiredConversationId($payload);
        if (null === $conversationId) {
            return $this->json(['ok' => false, 'error' => 'Informe a conversa.'], Response::HTTP_BAD_REQUEST);
        }

        $conversation = $apiClient->getConversation(null, $conversationId);
        $labels = $this->labelsFromConversation($conversation);
        $labels[] = 'sigi-ia-local';
        $labels[] = 'sigi-sugestao-ia';
        $apiClient->applyLabels(null, $conversationId, $this->normalizeLabels($labels));

        return $this->json(['ok' => true]);
    }

    #[Route('/sync', name: 'sync', methods: ['POST'])]
    public function sync(Request $request, ChatwootConversationSyncService $syncService): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $conversationId = $this->requiredConversationId($payload);
        if (null === $conversationId) {
            return $this->json(['ok' => false, 'error' => 'Informe a conversa.'], Response::HTTP_BAD_REQUEST);
        }

        $protocol = $syncService->syncConversationId($conversationId);

        return $this->json([
            'ok' => true,
            'protocol' => $protocol?->getProtocolCode(),
        ]);
    }

    private function extractConversationId(Request $request): ?string
    {
        foreach (['conversation_id', 'conversationId', 'cw_conversation_id', 'id'] as $key) {
            $value = $this->stringValue($request->query->get($key));
            if (null !== $value) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonPayload(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requiredConversationId(array $payload): ?string
    {
        foreach (['conversationId', 'conversation_id', 'id'] as $key) {
            $value = $this->stringValue($payload[$key] ?? null);
            if (null !== $value) {
                return $value;
            }
        }

        return null;
    }

    private function stringValue(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }

    /**
     * @param array<string, mixed> $conversation
     *
     * @return array<int, string>
     */
    private function labelsFromConversation(array $conversation): array
    {
        $labels = $conversation['labels'] ?? $conversation['payload']['labels'] ?? [];

        return is_array($labels) ? $this->normalizeLabels($labels) : [];
    }

    /**
     * @param array<int, mixed> $labels
     *
     * @return array<int, string>
     */
    private function normalizeLabels(array $labels): array
    {
        $normalized = [];
        foreach ($labels as $label) {
            if (!is_scalar($label)) {
                continue;
            }

            $label = trim((string) $label);
            if ('' !== $label) {
                $normalized[mb_strtolower($label)] = $label;
            }
        }

        return array_values($normalized);
    }
}

