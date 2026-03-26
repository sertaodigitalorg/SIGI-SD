<?php

namespace App\DataFixtures;

use App\Entity\OrganizationContact;
use App\Entity\Organization;
use App\Entity\ContactType;
use App\Entity\ContactStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InitialContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $organization = $this->getReference('organization_sertao_digital', Organization::class);

        $emailType = $this->getReference('contact_type_email', ContactType::class);
        $websiteType = $this->getReference('contact_type_website', ContactType::class);
        $whatsAppType = $this->getReference('contact_type_whatsapp', ContactType::class);
        $instagramType = $this->getReference('contact_type_instagram', ContactType::class);

        $activeStatus = $this->getReference('contact_status_active', ContactStatus::class);

        $contacts = [
            [
                'type' => $emailType,
                'value' => 'contato@sertaodigital.org',
                'label' => 'Contato geral',
                'isPrimary' => true,
                'isPublic' => true,
                'notes' => 'E-mail principal do site e canal de entrada geral.',
            ],
            [
                'type' => $emailType,
                'value' => 'atendimento@sertaodigital.org',
                'label' => 'Atendimento',
                'isPrimary' => false,
                'isPublic' => true,
                'notes' => 'Suporte a parceiros, prefeituras e usuários.',
            ],
            [
                'type' => $emailType,
                'value' => 'financeiro@sertaodigital.org',
                'label' => 'Financeiro',
                'isPrimary' => false,
                'isPublic' => true,
                'notes' => 'Canal financeiro, boletos, cobranças e tesouraria.',
            ],
            [
                'type' => $emailType,
                'value' => 'parcerias@sertaodigital.org',
                'label' => 'Parcerias',
                'isPrimary' => false,
                'isPublic' => true,
                'notes' => 'Negociação de convênios, eventos e relações institucionais.',
            ],
            [
                'type' => $emailType,
                'value' => 'projetos@sertaodigital.org',
                'label' => 'Projetos',
                'isPrimary' => false,
                'isPublic' => true,
                'notes' => 'Gestão técnica dos projetos em andamento.',
            ],
            [
                'type' => $emailType,
                'value' => 'diretoria@sertaodigital.org',
                'label' => 'Diretoria',
                'isPrimary' => false,
                'isPublic' => false,
                'notes' => 'Assuntos sigilosos ou estratégicos da liderança.',
            ],
            [
                'type' => $emailType,
                'value' => 'nao-responda@sertaodigital.org',
                'label' => 'Não responda',
                'isPrimary' => false,
                'isPublic' => false,
                'notes' => 'Envio automático de sistemas. Não deve receber respostas humanas.',
            ],
            [
                'type' => $emailType,
                'value' => 'imprensa@sertaodigital.org',
                'label' => 'Imprensa',
                'isPrimary' => false,
                'isPublic' => true,
                'notes' => 'Canal para contato com mídia e jornalistas.',
            ],
            [
                'type' => $websiteType,
                'value' => 'https://sertaodigital.org',
                'label' => 'Site institucional',
                'isPrimary' => true,
                'isPublic' => true,
                'notes' => 'Site oficial do Sertão Digital.',
            ],
            [
                'type' => $whatsAppType,
                'value' => '(83) 98863-3668',
                'label' => 'WhatsApp institucional',
                'isPrimary' => true,
                'isPublic' => true,
                'notes' => 'Contato institucional principal.',
            ],
            [
                'type' => $instagramType,
                'value' => '@sertaodigitalorg',
                'label' => 'Instagram oficial',
                'isPrimary' => true,
                'isPublic' => true,
                'notes' => 'Perfil oficial do projeto.',
            ],
        ];

        foreach ($contacts as $contactData) {
            $contact = new OrganizationContact();
            $contact->setOrganization($organization);
            $contact->setContactType($contactData['type']);
            $contact->setStatus($activeStatus);
            $contact->setIssueType(null);
            $contact->setValue($contactData['value']);
            $contact->setLabel($contactData['label']);
            $contact->setIsPrimary($contactData['isPrimary']);
            $contact->setIsPublic($contactData['isPublic']);
            $contact->setDeactivatedAt(null);
            $contact->setDeactivationReason(null);
            $contact->setNotes($contactData['notes']);

            $manager->persist($contact);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CatalogFixtures::class,
            OrganizationFixtures::class,
        ];
    }
}