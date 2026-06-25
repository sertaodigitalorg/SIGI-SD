<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;

final class ChatwootRuntimeConfig
{
    private ?ChatwootAccount $primaryAccount = null;

    public function __construct(private readonly ChatwootAccountRepository $accountRepository)
    {
    }

    public function getBaseUrl(?ChatwootAccount $account = null): ?string
    {
        $account ??= $this->getPrimaryAccount();
        $baseUrl = $account?->getBaseUrl() ?? $this->readEnv('CHATWOOT_BASE_URL');

        return null === $baseUrl ? null : rtrim($baseUrl, '/');
    }

    public function getAccountId(?ChatwootAccount $account = null): ?string
    {
        $account ??= $this->getPrimaryAccount();

        return $account?->getAccountId() ?? $this->readEnv('CHATWOOT_ACCOUNT_ID');
    }

    public function getApiToken(?ChatwootAccount $account = null): ?string
    {
        $account ??= $this->getPrimaryAccount();

        return $account?->getApiToken() ?? $this->readEnv('CHATWOOT_API_TOKEN');
    }

    public function getInboxId(?ChatwootAccount $account = null): ?string
    {
        $account ??= $this->getPrimaryAccount();

        return $account?->getInboxId() ?? $this->readEnv('CHATWOOT_INBOX_ID');
    }

    public function getConversationUrl(string $conversationId, ?ChatwootAccount $account = null): ?string
    {
        $account ??= $this->getPrimaryAccount();
        $baseUrl = $this->readEnv('SIGI_CHATWOOT_URL') ?? $this->getBaseUrl($account);
        $accountId = $this->getAccountId($account);

        if (null === $baseUrl || null === $accountId) {
            return null;
        }

        return sprintf('%s/app/accounts/%s/conversations/%s', rtrim($baseUrl, '/'), $accountId, $conversationId);
    }

    /**
     * @return array<string, string>
     */
    public function getHubLinks(): array
    {
        $links = [
            'Chatwoot' => $this->readEnv('SIGI_CHATWOOT_URL') ?? $this->readEnv('CHATWOOT_BASE_URL'),
            'Botpress' => $this->readEnv('SIGI_BOTPRESS_URL'),
            'Typebot' => $this->readEnv('SIGI_TYPEBOT_URL'),
            'Portainer' => $this->readEnv('SIGI_PORTAINER_URL'),
            'BI' => $this->readEnv('SIGI_BI_URL'),
            'Documentacao interna' => $this->readEnv('SIGI_DOCS_URL'),
        ];

        return array_filter($links, static fn (?string $url): bool => null !== $url && '' !== trim($url));
    }

    private function getPrimaryAccount(): ?ChatwootAccount
    {
        if (null !== $this->primaryAccount) {
            return $this->primaryAccount;
        }

        $this->primaryAccount = $this->accountRepository->createAdminListQueryBuilder()
            ->andWhere('account.isActive = true')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->primaryAccount;
    }

    private function readEnv(string $name): ?string
    {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);

        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }
}
