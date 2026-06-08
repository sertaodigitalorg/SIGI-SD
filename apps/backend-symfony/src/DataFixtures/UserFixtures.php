<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullName, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullName);
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);

            $this->addReference('user_'.$username, $user);
        }

        $manager->flush();
    }

    /**
     * @return array<array{string, string, string, string, array<string>}>
     */
    private function getUserData(): array
    {
        return [
            // [$fullName, $username, $password, $email, $roles]
            ['John User', 'john_user', 'kitten', 'john_user@sertaodigital.org', [User::ROLE_USER]],
            ['Jane Admin', 'jane_admin', 'kitten', 'jane_admin@sertaodigital.org', [User::ROLE_ADMIN]],
            ['Wellington Carvalho', 'wellington', '123456', 'wellington.carvalho@sertaodigital.org', [User::ROLE_ADMIN]],
            ['Administrador SIGI-SD', 'admin', '123456', 'admin@sertaodigital.org', [User::ROLE_ADMIN]],
            ['Usuário Operacional', 'operacional', '123456', 'operacional@sertaodigital.org', [User::ROLE_USER]],
        ];
    }
}
