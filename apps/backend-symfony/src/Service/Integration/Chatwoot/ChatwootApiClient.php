<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ChatwootApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ChatwootRuntimeConfig $runtimeConfig,
    ) {
    }

    public function testConnection(ChatwootAccount $account): bool
    {
        try {
            $this->getInboxes($account);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getInboxes(?ChatwootAccount $account = null): array
    {
        $payload = $this->request($account, 'GET', 'inboxes');
        $items = $payload['data']['payload']
            ?? $payload['payload']
            ?? $payload['data']['inboxes']
            ?? $payload['inboxes']
            ?? $payload['data']
            ?? $payload;

        if (!is_array($items)) {
            return [];
        }

        if (!array_is_list($items)) {
            $items = $items['payload'] ?? $items['inboxes'] ?? [];
        }

        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, 'is_array'));
    }

    /**
     * @return array<string, mixed>
     */
    public function getConversation(?ChatwootAccount $account, string $conversationId): array
    {
        return $this->request($account, 'GET', sprintf('conversations/%s', rawurlencode($conversationId)));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecentConversations(?ChatwootAccount $account = null, int $limit = 50, string $status = 'all'): array
    {
        $query = [
            'status' => $status,
        ];

        if (null !== $this->runtimeConfig->getInboxId($account)) {
            $query['inbox_id'] = $this->runtimeConfig->getInboxId($account);
        }

        $payload = $this->request($account, 'GET', 'conversations', [
            'query' => $query,
        ]);

        $items = $payload['data']['payload']
            ?? $payload['payload']
            ?? $payload['data']['conversations']
            ?? $payload['conversations']
            ?? $payload['data']
            ?? $payload;

        if (!is_array($items)) {
            return [];
        }

        if (!array_is_list($items)) {
            $items = $items['payload'] ?? $items['conversations'] ?? [];
        }

        if (!is_array($items)) {
            return [];
        }

        $items = array_slice(array_values(array_filter($items, 'is_array')), 0, $limit);
        $inboxes = $this->getInboxesById($account);

        return array_map(static function (array $conversation) use ($inboxes): array {
            if (!isset($conversation['inbox']) && isset($conversation['inbox_id'])) {
                $inboxId = (string) $conversation['inbox_id'];
                if (isset($inboxes[$inboxId])) {
                    $conversation['inbox'] = $inboxes[$inboxId];
                }
            }

            return $conversation;
        }, $items);
    }

    /**
     * @param array<int, string> $labels
     */
    public function applyLabels(?ChatwootAccount $account, string $conversationId, array $labels): bool
    {
        $this->request($account, 'POST', sprintf('conversations/%s/labels', rawurlencode($conversationId)), [
            'json' => ['labels' => array_values($labels)],
        ]);

        return true;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function updateCustomAttributes(?ChatwootAccount $account, string $conversationId, array $attributes): bool
    {
        $this->request($account, 'POST', sprintf('conversations/%s/custom_attributes', rawurlencode($conversationId)), [
            'json' => ['custom_attributes' => $attributes],
        ]);

        return true;
    }

    public function createPrivateNote(?ChatwootAccount $account, string $conversationId, string $content): bool
    {
        $this->request($account, 'POST', sprintf('conversations/%s/messages', rawurlencode($conversationId)), [
            'json' => [
                'content' => $content,
                'message_type' => 'outgoing',
                'private' => true,
            ],
        ]);

        return true;
    }

    public function createPublicMessage(?ChatwootAccount $account, string $conversationId, string $content): bool
    {
        $this->request($account, 'POST', sprintf('conversations/%s/messages', rawurlencode($conversationId)), [
            'json' => [
                'content' => $content,
                'message_type' => 'outgoing',
                'private' => false,
            ],
        ]);

        return true;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function request(?ChatwootAccount $account, string $method, string $path, array $options = []): array
    {
        $baseUrl = $this->runtimeConfig->getApiBaseUrl($account);
        $accountId = $this->runtimeConfig->getAccountId($account);
        $apiToken = $this->runtimeConfig->getApiToken($account);

        if (null === $baseUrl || null === $accountId || null === $apiToken) {
            throw new \RuntimeException('Configure URL base, ID da conta e API token na conta Chatwoot ativa do SIGI.');
        }

        $url = sprintf('%s/api/v1/accounts/%s/%s', $baseUrl, rawurlencode($accountId), ltrim($path, '/'));
        $options['headers']['api_access_token'] = $apiToken;
        $options['headers']['X-Access-Token'] = $apiToken;
        $options['headers']['Accept'] = 'application/json';
        $options['timeout'] = $options['timeout'] ?? 15;

        try {
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
        } catch (TransportExceptionInterface $exception) {
            throw new \RuntimeException('Falha de comunicacao com Chatwoot: '.$exception->getMessage(), previous: $exception);
        }

        if ($statusCode >= 400) {
            throw new \RuntimeException(sprintf('Chatwoot retornou HTTP %d: %s', $statusCode, mb_substr($content, 0, 500)));
        }

        if ('' === trim($content)) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getInboxesById(?ChatwootAccount $account): array
    {
        $indexed = [];
        foreach ($this->getInboxes($account) as $inbox) {
            if (!isset($inbox['id']) || !is_scalar($inbox['id'])) {
                continue;
            }

            $indexed[(string) $inbox['id']] = $inbox;
        }

        return $indexed;
    }
}
