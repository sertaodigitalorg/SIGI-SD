<?php

namespace App\Form\Modifier;

use App\Entity\Organization;
use App\Entity\OrganizationContact;
use App\Repository\OrganizationContactRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class OrganizationContactModifier
{
    public function __construct(
        private OrganizationContactRepository $repository
    ) {
    }

    public function addOrganizationContactField(FormInterface $form, ?Organization $organization = null, ?OrganizationContact $organizationContact = null): void
    {
        $contacts = [];

        if (null !== $organization) {
            $contacts = $this->repository->findBy(['organization' => $organization], ['contactType' => 'ASC', 'value' => 'ASC']);
        }

        // Garantir que o organizationContact atual está na lista de choices
        if ($organizationContact && !in_array($organizationContact, $contacts, true)) {
            $contacts[] = $organizationContact;
        }

        $options = [
            'class' => OrganizationContact::class,
            'choices' => $contacts,
            'choice_label' => function (OrganizationContact $contact) {
                return sprintf('%s — %s', $contact->getContactType()?->getName() ?: '-', $contact->getValue() ?: '-');
            },
            'label' => 'Contato',
            'placeholder' => $organization ? 'Selecione um contato' : 'Selecione uma organização primeiro',
            'required' => true,
            'attr' => [
                'class' => 'form-select',
                'data-organization-contact-select' => true,
            ],
            'disabled' => null === $organization,
        ];

        if ($organizationContact) {
            $options['data'] = $organizationContact;
        }

        $form->add('organizationContact', EntityType::class, $options);
    }

    public static function addOrganizationContactListener(FormBuilderInterface $form, OrganizationContactRepository $repository): void
    {
        $form->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($repository): void {
            $data = $event->getData();
            $form = $event->getForm();

            $organization = $data?->getOrganizationContact()?->getOrganization();
            $organizationContact = $data?->getOrganizationContact();

            // Remover e recriar o campo organization com o valor correto
            if ($form->has('organization')) {
                $form->remove('organization');
            }

            $form->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'legalName',
                'label' => 'Organização',
                'placeholder' => 'Selecione uma organização',
                'data' => $organization,
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('o')
                        ->orderBy('o.legalName', 'ASC');
                },
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-organization-select' => true,
                ],
                'mapped' => false,
            ]);

            $modifier = new self($repository);
            $modifier->addOrganizationContactField($form, $organization, $organizationContact);
        });

        $form->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($repository): void {
            $data = $event->getData();
            $form = $event->getForm();

            $organizationId = $data['organization'] ?? null;
            $organization = null;
            $organizationContact = null;

            if ($organizationId) {
                $organization = $repository->getEntityManager()->getRepository(Organization::class)->find($organizationId);
            }

            $organizationContactId = $data['organizationContact'] ?? null;
            if ($organizationContactId && $organization) {
                $organizationContact = $repository->find($organizationContactId);
            }

            $modifier = new self($repository);
            $modifier->addOrganizationContactField($form, $organization, $organizationContact);
        });
    }
}
