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
        $baseUrl = $this->runtimeConfig->getBaseUrl($account);
        if (null === $baseUrl || false === filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            $response = $this->httpClient->request('GET', $baseUrl, [
                'headers' => [
                    'X-Access-Token' => $this->runtimeConfig->getApiToken($account) ?? '',
                ],
                'timeout' => 5,
            ]);

            return $response->getStatusCode() < 500;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getInboxes(?ChatwootAccount $account = null): array
    {
        return $this->request($account, 'GET', 'inboxes');
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

        $conversations = $payload['payload'] ?? $payload['data'] ?? $payload;
        if (!is_array($conversations)) {
            return [];
        }

        $items = array_is_list($conversations) ? $conversations : ($conversations['conversations'] ?? []);
        if (!is_array($items)) {
            return [];
        }

        return array_slice(array_values(array_filter($items, 'is_array')), 0, $limit);
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

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function request(?ChatwootAccount $account, string $method, string $path, array $options = []): array
    {
        $baseUrl = $this->runtimeConfig->getBaseUrl($account);
        $accountId = $this->runtimeConfig->getAccountId($account);
        $apiToken = $this->runtimeConfig->getApiToken($account);

        if (null === $baseUrl || null === $accountId || null === $apiToken) {
            throw new \RuntimeException('Configure URL base, ID da conta e API token na conta Chatwoot ativa do SIGI.');
        }

        $url = sprintf('%s/api/v1/accounts/%s/%s', $baseUrl, rawurlencode($accountId), ltrim($path, '/'));
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
}
