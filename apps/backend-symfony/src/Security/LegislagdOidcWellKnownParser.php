<?php

namespace App\Security;

use Drenso\OidcBundle\OidcWellKnownParserInterface;

final class LegislagdOidcWellKnownParser implements OidcWellKnownParserInterface
{
    private const BACK_CHANNEL_ENDPOINTS = [
        'token_endpoint',
        'introspection_endpoint',
        'userinfo_endpoint',
        'jwks_uri',
        'revocation_endpoint',
    ];

    public function __construct(
        private readonly string $publicIssuer,
        private readonly string $internalIssuer,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    public function parseWellKnown(array $config): array
    {
        $config['issuer'] = $this->publicIssuer;

        if (isset($config['authorization_endpoint']) && is_string($config['authorization_endpoint'])) {
            $config['authorization_endpoint'] = $this->rewriteEndpoint($config['authorization_endpoint'], $this->publicIssuer);
        }

        foreach (self::BACK_CHANNEL_ENDPOINTS as $key) {
            if (isset($config[$key]) && is_string($config[$key])) {
                $config[$key] = $this->rewriteEndpoint($config[$key], $this->internalIssuer);
            }
        }

        return $config;
    }

    private function rewriteEndpoint(string $endpoint, string $baseUrl): string
    {
        $realmPath = $this->realmPath($this->publicIssuer);
        $endpointPath = parse_url($endpoint, PHP_URL_PATH) ?: '';
        $realmPosition = strpos($endpointPath, $realmPath);

        if ($realmPosition === false) {
            return $endpoint;
        }

        $suffix = substr($endpointPath, $realmPosition + strlen($realmPath));
        $query = parse_url($endpoint, PHP_URL_QUERY);
        $fragment = parse_url($endpoint, PHP_URL_FRAGMENT);

        return rtrim($baseUrl, '/')
            . $suffix
            . ($query !== null ? '?' . $query : '')
            . ($fragment !== null ? '#' . $fragment : '');
    }

    private function realmPath(string $issuer): string
    {
        return rtrim(parse_url($issuer, PHP_URL_PATH) ?: '', '/');
    }
}
