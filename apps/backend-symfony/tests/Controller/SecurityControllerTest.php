<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityControllerTest extends WebTestCase
{
    public function testLegislagdLoginButtonIsHiddenByDefault(): void
    {
        $_SERVER['SIGI_OIDC_ENABLED'] = '0';

        $client = static::createClient();
        $crawler = $client->request('GET', '/en/login');

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('a:contains("Entrar com LegislaGD")')->count());
    }

    public function testLoginKeepsRequestedAdminTargetPath(): void
    {
        $_SERVER['SIGI_OIDC_ENABLED'] = '0';

        $client = static::createClient();
        $client->request('GET', '/en/login', ['redirect_to' => '/en/admin/atendimentos']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(
            '/en/admin/atendimentos',
            $client->getRequest()->getSession()->get('_security.main.target_path')
        );
    }

    public function testLegislagdLoginButtonIsShownWhenEnabled(): void
    {
        $_SERVER['SIGI_OIDC_ENABLED'] = '1';
        $_SERVER['SIGI_OIDC_DISPLAY_NAME'] = 'LegislaGD';
        $_SERVER['SIGI_OIDC_ISSUER'] = 'http://id.legislagd.localhost/realms/legislagd';
        $_SERVER['SIGI_OIDC_CLIENT_ID'] = 'sigi';

        $client = static::createClient();
        $crawler = $client->request('GET', '/en/login');

        $this->assertResponseIsSuccessful();
        $link = $crawler->selectLink('Entrar com LegislaGD')->link();

        $this->assertStringEndsWith('/en/auth/legislagd/login', $link->getUri());
    }

    public function testLegislagdLoginRedirectsToKeycloakAuthorizationEndpoint(): void
    {
        $_SERVER['SIGI_OIDC_ENABLED'] = '1';
        $_SERVER['SIGI_OIDC_ISSUER'] = 'http://id.legislagd.localhost/realms/legislagd';
        $_SERVER['SIGI_OIDC_CLIENT_ID'] = 'sigi';
        $_SERVER['SIGI_OIDC_SCOPES'] = 'openid email profile';

        $client = static::createClient();
        $client->request('GET', '/en/auth/legislagd/login');

        $this->assertResponseRedirects();
        $location = (string) $client->getResponse()->headers->get('Location');

        $this->assertStringStartsWith('http://id.legislagd.localhost/realms/legislagd/protocol/openid-connect/auth?', $location);
        $this->assertStringContainsString('client_id=sigi', $location);
        $this->assertStringContainsString('response_type=code', $location);
        $this->assertStringContainsString('scope=openid%20email%20profile', $location);
    }
}
