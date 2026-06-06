<?php

namespace App\Form\Modifier;

use App\Entity\Person;
use App\Entity\PersonContact;
use App\Repository\PersonContactRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PersonContactModifier
{
    public function __construct(
        private PersonContactRepository $repository
    ) {
    }

    public function addPersonContactField(FormInterface $form, ?Person $person = null, ?PersonContact $personContact = null): void
    {
        $contacts = [];

        if (null !== $person) {
            $contacts = $this->repository->findBy(['person' => $person], ['contactType' => 'ASC', 'value' => 'ASC']);
        }

        // Garantir que o personContact atual está na lista de choices
        if ($personContact && !in_array($personContact, $contacts, true)) {
            $contacts[] = $personContact;
        }

        $options = [
            'class' => PersonContact::class,
            'choices' => $contacts,
            'choice_label' => function (PersonContact $contact) {
                return sprintf('%s — %s', $contact->getContactType()?->getName() ?: '-', $contact->getValue() ?: '-');
            },
            'label' => 'Contato',
            'placeholder' => $person ? 'Selecione um contato' : 'Selecione uma pessoa primeiro',
            'required' => true,
            'attr' => [
                'class' => 'form-select',
                'data-person-contact-select' => true,
            ],
            'disabled' => null === $person,
        ];

        if ($personContact) {
            $options['data'] = $personContact;
        }

        $form->add('personContact', EntityType::class, $options);
    }

    public static function addPersonContactListener(FormBuilderInterface $form, PersonContactRepository $repository): void
    {
        $form->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($repository): void {
            $form = $event->getForm();
            $data = $event->getData();

            $person = $data?->getPersonContact()?->getPerson();
            $personContact = $data?->getPersonContact();

            // Remover e recriar o campo person com o valor correto
            if ($form->has('person')) {
                $form->remove('person');
            }

            $form->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'fullName',
                'label' => 'Pessoa',
                'placeholder' => 'Selecione uma pessoa',
                'data' => $person,
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('p')
                        ->orderBy('p.fullName', 'ASC');
                },
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-person-select' => true,
                ],
                'mapped' => false,
            ]);

            $modifier = new self($repository);
            $modifier->addPersonContactField($form, $person, $personContact);
        });

        $form->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($repository): void {
            $form = $event->getForm();
            $data = $event->getData();

            $personId = $data['person'] ?? null;
            $person = null;
            $personContact = null;

            if ($personId) {
                $person = $repository->getEntityManager()->getRepository(Person::class)->find($personId);
            }

            $personContactId = $data['personContact'] ?? null;
            if ($personContactId && $person) {
                $personContact = $repository->find($personContactId);
            }

            $modifier = new self($repository);
            $modifier->addPersonContactField($form, $person, $personContact);
        });
    }
}
