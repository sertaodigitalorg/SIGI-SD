<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use App\Entity\OrganizationType;
use App\Entity\Person;
use App\Entity\PersonOrganization;
use App\Entity\PersonOrganizationRole;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrganizationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $ictType = $this->getReference('organization_type_ict', OrganizationType::class);
        $associationType = $this->getReference('organization_type_association', OrganizationType::class);

        $parentOrganization = new Organization();
        $parentOrganization->setLegalName('REDE DE INOVACAO DO SERTAO');
        $parentOrganization->setTradeName('Rede do Sertão');
        $parentOrganization->setCnpj('18.245.320/0001-44');
        $parentOrganization->setAcronym('RIS');
        $parentOrganization->setOrganizationType($associationType);
        $parentOrganization->setStatus(Organization::STATUS_ACTIVE);
        $parentOrganization->setNotes('Entidade mantenedora e articuladora das iniciativas regionais de inovação.');
        $parentOrganization->setUpdatedAt(null);

        $manager->persist($parentOrganization);

        $organization = new Organization();
        $organization->setLegalName('CENTRO DE INOVACAO E TECNOLOGIA SERTAO DIGITAL');
        $organization->setTradeName('Sertão Digital');
        $organization->setCnpj('61.367.666/0001-77');
        $organization->setAcronym('CIT-SD');
        $organization->setParent($parentOrganization);
        $organization->setOrganizationType($ictType);
        $organization->setStatus(Organization::STATUS_ACTIVE);
        $organization->setNotes('Organização âncora do SIGI-SD para inovação, projetos e articulação territorial.');
        $organization->setUpdatedAt(null);

        $manager->persist($organization);

        $person = new Person();
        $person->setFullName('Wellington Carvalho Silva');
        $person->setCpf('314.269.938-46');
        $person->setUpdatedAt(null);

        $manager->persist($person);

        $personOrganization = new PersonOrganization();
        $personOrganization->setPerson($person);
        $personOrganization->setOrganization($organization);
        $personOrganization->setStartDate(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $personOrganization->setEndDate(null);
        $personOrganization->setStatus('Ativo');
        $personOrganization->setNotes('Vínculo institucional inicial do SIGI-SD.');

        $manager->persist($personOrganization);

        $personOrganizationRole = new PersonOrganizationRole();
        $personOrganizationRole->setPersonOrganization($personOrganization);
        $personOrganizationRole->setRole($this->getReference('role_presidente', Role::class));
        $personOrganizationRole->setStartDate(new \DateTimeImmutable('2025-06-06 00:00:00'));
        $personOrganizationRole->setEndDate(null);

        $manager->persist($personOrganizationRole);

        $this->addReference('organization_rede_inovacao_sertao', $parentOrganization);
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
