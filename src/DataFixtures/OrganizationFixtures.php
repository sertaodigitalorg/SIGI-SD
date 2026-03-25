<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use App\Entity\Person;
use App\Entity\PersonOrganization;
use App\Entity\PersonOrganizationRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrganizationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // =====================================
        // ORGANIZATION
        // =====================================
        $organization = new Organization();
        $organization->setLegalName('CENTRO DE INOVACAO E TECNOLOGIA SERTAO DIGITAL');
        $organization->setTradeName('Sertão Digital');
        $organization->setCnpj('61.367.666/0001-77');
        $organization->setCreatedAt(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $organization->setUpdatedAt(null);

        $manager->persist($organization);

        // =====================================
        // PERSON
        // =====================================
        $person = new Person();
        $person->setFullName('Wellington Carvalho Silva');
        $person->setCpf('314.269.938-46');
        $person->setCreatedAt(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $person->setUpdatedAt(null);

        $manager->persist($person);

        // =====================================
        // PERSON ORGANIZATION
        // =====================================
        $personOrganization = new PersonOrganization();
        $personOrganization->setPerson($person);
        $personOrganization->setOrganization($organization);
        $personOrganization->setStartDate(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $personOrganization->setEndDate(null);
        $personOrganization->setStatus('Ativo');
        $personOrganization->setNotes('Vínculo institucional inicial do SIGI-SD.');

        $manager->persist($personOrganization);

        // =====================================
        // PERSON ORGANIZATION ROLE
        // =====================================
        $personOrganizationRole = new PersonOrganizationRole();
        $personOrganizationRole->setPersonOrganization($personOrganization);
        $personOrganizationRole->setRole($this->getReference('role_presidente'));
        $personOrganizationRole->setStartDate(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $personOrganizationRole->setEndDate(null);

        $manager->persist($personOrganizationRole);

        // =====================================
        // REFERENCES
        // =====================================
        $this->addReference('organization_sertao_digital', $organization);
        $this->addReference('person_wellington_carvalho_silva', $person);
        $this->addReference('person_organization_wellington_sertao_digital', $personOrganization);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CatalogFixtures::class,
        ];
    }
}