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

    public function addOrganizationContactField(FormInterface $form, ?Organization $organization = null): void
    {
        $contacts = [];

        if (null !== $organization) {
            $contacts = $this->repository->findBy(['organization' => $organization], ['contactType' => 'ASC', 'value' => 'ASC']);
        }

        $form->add('organizationContact', EntityType::class, [
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
        ]);
    }

    public static function addOrganizationContactListener(FormBuilderInterface $form, OrganizationContactRepository $repository): void
    {
        $form->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($repository): void {
            $data = $event->getData();
            $form = $event->getForm();

            $organization = $data?->getOrganizationContact()?->getOrganization();

            if ($organization && $form->has('organization')) {
                $form->get('organization')->setData($organization);
            }

            $modifier = new self($repository);
            $modifier->addOrganizationContactField($form, $organization);
        });

        $form->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($repository): void {
            $data = $event->getData();
            $form = $event->getForm();

            $organizationId = $data['organization'] ?? null;
            $organization = null;

            if ($organizationId) {
                $organization = $repository->getEntityManager()->getRepository(Organization::class)->find($organizationId);
            }

            $modifier = new self($repository);
            $modifier->addOrganizationContactField($form, $organization);
        });
    }
}
