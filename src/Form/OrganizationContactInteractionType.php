<?php

namespace App\Form;

use App\Entity\OrganizationContactInteraction;
use App\Entity\OrganizationContact;
use App\Entity\InteractionStatus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationContactInteractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organizationContact', EntityType::class, [
                'class' => OrganizationContact::class,
                'choice_label' => function (OrganizationContact $contact) {
                    return sprintf('%s | %s', $contact->getOrganization()?->getLegalName() ?: '-', $contact->getContactType()?->getName() ?: '-');
                },
                'label' => 'Contato Institucional',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('c')
                        ->join('c.organization', 'o')
                        ->orderBy('o.legalName', 'ASC');
                },
            ])
            ->add('interactionStatus', EntityType::class, [
                'class' => InteractionStatus::class,
                'choice_label' => 'name',
                'label' => 'Status da Interação',
                'required' => false,
            ])
            ->add('performedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Realizado Por',
                'required' => false,
            ])
            ->add('contactedAt', DateTimeType::class, [
                'label' => 'Data do Contato',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
                'data' => new \DateTimeImmutable(),
            ])
            ->add('subject', TextType::class, [
                'label' => 'Assunto',
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Mensagem',
                'required' => false,
            ])
            ->add('responseReceived', CheckboxType::class, [
                'label' => 'Houve Resposta',
                'required' => false,
            ])
            ->add('responseText', TextareaType::class, [
                'label' => 'Texto da Resposta',
                'required' => false,
            ])
            ->add('nextContactAt', DateTimeType::class, [
                'label' => 'Próximo Contato',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Observações',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganizationContactInteraction::class,
        ]);
    }
}
