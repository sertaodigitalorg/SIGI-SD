<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\User;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Model\OidcTokens;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @method User|null findOneByUsername(string $username)
 * @method User|null findOneByEmail(string $email)
 *
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements OidcUserProviderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->findOneByUsername($identifier)
            ?? throw new UserNotFoundException(sprintf('Usuario "%s" nao encontrado.', $identifier));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User || null === $user->getId()) {
            throw new UnsupportedUserException(sprintf('Instancia "%s" nao suportada.', $user::class));
        }

        return $this->find($user->getId())
            ?? throw new UserNotFoundException('Usuario nao encontrado.');
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function ensureUserExists(string $userIdentifier, OidcUserData $userData, OidcTokens $tokens): void
    {
        $roles = $this->extractRoles($tokens);
        if (!$this->hasSigiRole($roles)) {
            throw new OidcException('Usuario sem perfil SIGI-SD no LegislaGD.');
        }

        $username = $this->normalizeUsername($userIdentifier);
        $email = $userData->getEmail();
        $user = $this->findOneByUsername($username);

        if (!$user && '' !== $email) {
            $user = $this->findOneByEmail($email);
        }

        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(32))));
        }

        $user->setFullName($this->resolveFullName($userData, $username));
        $user->setEmail($email ?: $username.'@legislagd.localhost');
        $user->setRoles(in_array('sigi.admin', $roles, true) ? [User::ROLE_ADMIN] : [User::ROLE_USER]);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function loadOidcUser(string $userIdentifier): UserInterface
    {
        return $this->loadUserByIdentifier($this->normalizeUsername($userIdentifier));
    }

    /**
     * @return string[]
     */
    private function extractRoles(OidcTokens $tokens): array
    {
        return array_values(array_unique(array_merge(
            $this->extractRolesFromJwt($tokens->getIdToken()),
            $this->extractRolesFromJwt($tokens->getAccessToken()),
        )));
    }

    /**
     * @return string[]
     */
    private function extractRolesFromJwt(string $token): array
    {
        $parts = explode('.', $token);
        if (3 !== count($parts)) {
            return [];
        }

        $payload = base64_decode(strtr($parts[1], '-_', '+/'), true);
        if (false === $payload) {
            return [];
        }

        $claims = json_decode($payload, true);
        if (!is_array($claims)) {
            return [];
        }

        $roles = $claims['realm_access']['roles'] ?? [];
        foreach (($claims['resource_access'] ?? []) as $resourceAccess) {
            $roles = array_merge($roles, $resourceAccess['roles'] ?? []);
        }

        return array_filter($roles, 'is_string');
    }

    /**
     * @param string[] $roles
     */
    private function hasSigiRole(array $roles): bool
    {
        return [] !== array_intersect($roles, ['sigi.admin', 'sigi.supervisor', 'sigi.atendente']);
    }

    private function resolveFullName(OidcUserData $userData, string $fallback): string
    {
        $fullName = $userData->getFullName();
        if ('' !== $fullName) {
            return $fullName;
        }

        $name = trim($userData->getGivenName().' '.$userData->getFamilyName());

        return '' !== $name ? $name : $fallback;
    }

    private function normalizeUsername(string $username): string
    {
        $username = preg_replace('/[^a-zA-Z0-9._-]/', '.', $username) ?: 'usuario';

        return substr(trim($username, '.'), 0, 120) ?: 'usuario';
    }
}
