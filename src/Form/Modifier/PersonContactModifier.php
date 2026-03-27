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

    public function addPersonContactField(FormInterface $form, ?Person $person = null): void
    {
        $contacts = [];

        if (null !== $person) {
            $contacts = $this->repository->findBy(['person' => $person], ['contactType' => 'ASC', 'value' => 'ASC']);
        }

        $form->add('personContact', EntityType::class, [
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
        ]);
    }

    public static function addPersonContactListener(FormBuilderInterface $form, PersonContactRepository $repository): void
    {
        $form->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($repository): void {
            $form = $event->getForm();
            $data = $event->getData();

            $person = $data?->getPersonContact()?->getPerson();
            
            if ($person && $form->has('person')) {
                $form->get('person')->setData($person);
            }

            $modifier = new self($repository);
            $modifier->addPersonContactField($form, $person);
        });

        $form->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($repository): void {
            $form = $event->getForm();
            $data = $event->getData();

            $personId = $data['person'] ?? null;
            $person = null;

            if ($personId) {
                $person = $repository->getEntityManager()->getRepository(Person::class)->find($personId);
            }

            $modifier = new self($repository);
            $modifier->addPersonContactField($form, $person);
        });
    }
}
