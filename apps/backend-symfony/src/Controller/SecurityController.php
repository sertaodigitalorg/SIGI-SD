<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use Drenso\OidcBundle\OidcClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Controller used to manage the application security.
 * See https://symfony.com/doc/current/security/form_login_setup.html.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /*
     * The $user argument type (?User) must be nullable because the login page
     * must be accessible to anonymous visitors too.
     */
    #[Route('/login', name: 'security_login')]
    public function login(
        #[CurrentUser] ?User $user,
        Request $request,
        AuthenticationUtils $helper,
    ): Response {
        // if user is already logged in, don't display the login page again
        if ($user) {
            return $this->redirectToRoute('blog_index');
        }

        $redirectTo = (string) $request->query->get('redirect_to', '');
        if ($this->isLocalRedirectPath($redirectTo)) {
            $this->saveTargetPath($request->getSession(), 'main', $redirectTo);
        } elseif (!$this->getTargetPath($request->getSession(), 'main')) {
            $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('admin_index'));
        }

        return $this->render('security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
            'oidc' => $this->getOidcConfig(),
        ]);
    }

    #[Route('/auth/legislagd/login', name: 'security_legislagd_login', methods: ['GET'])]
    public function legislagdLogin(OidcClientInterface $legislagdOidcClient): RedirectResponse
    {
        $oidc = $this->getOidcConfig();

        if (!$oidc['enabled']) {
            throw $this->createNotFoundException();
        }

        return $legislagdOidcClient->generateAuthorizationRedirect(
            scopes: explode(' ', $oidc['scopes']),
            additionalQueryParams: ['ui_locales' => $this->getEnv('SIGI_OIDC_UI_LOCALES', 'pt-BR')],
        );
    }

    #[Route('/auth/legislagd/callback', name: 'security_legislagd_callback', methods: ['GET'])]
    public function legislagdCallback(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('security_login');
    }

    /**
     * @return array{enabled: bool, display_name: string, issuer: string, client_id: string, scopes: string}
     */
    private function getOidcConfig(): array
    {
        return [
            'enabled' => filter_var($this->getEnv('SIGI_OIDC_ENABLED', '0'), FILTER_VALIDATE_BOOL),
            'display_name' => $this->getEnv('SIGI_OIDC_DISPLAY_NAME', 'LegislaGD'),
            'issuer' => rtrim($this->getEnv('SIGI_OIDC_ISSUER'), '/'),
            'client_id' => $this->getEnv('SIGI_OIDC_CLIENT_ID'),
            'scopes' => $this->getEnv('SIGI_OIDC_SCOPES', 'openid email profile'),
        ];
    }

    private function getEnv(string $name, string $default = ''): string
    {
        $value = $_SERVER[$name] ?? $_ENV[$name] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    private function isLocalRedirectPath(string $path): bool
    {
        return str_starts_with($path, '/') && !str_starts_with($path, '//');
    }
}
