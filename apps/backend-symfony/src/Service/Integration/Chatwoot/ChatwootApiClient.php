<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ChatwootApiClient
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function testConnection(ChatwootAccount $account): bool
    {
        if (null === $account->getBaseUrl() || false === filter_var($account->getBaseUrl(), FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            $response = $this->httpClient->request('GET', $account->getBaseUrl(), [
                'headers' => [
                    'X-Access-Token' => $account->getApiToken() ?? '',
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
    public function getInboxes(ChatwootAccount $account): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getConversation(ChatwootAccount $account, string $conversationId): array
    {
        return [];
    }

    /**
     * @param array<int, string> $labels
     */
    public function applyLabels(ChatwootAccount $account, string $conversationId, array $labels): bool
    {
        return false;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function updateCustomAttributes(ChatwootAccount $account, string $conversationId, array $attributes): bool
    {
        return false;
    }
}
