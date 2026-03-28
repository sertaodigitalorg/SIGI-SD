<?php

namespace App\Form;

use App\Entity\InteractionStatus;
use App\Entity\Organization;
use App\Entity\OrganizationContact;
use App\Entity\OrganizationContactInteraction;
use App\Entity\ResponseType;
use App\Entity\User;
use App\Form\Modifier\OrganizationContactModifier;
use App\Repository\OrganizationContactRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationContactInteractionType extends AbstractType
{
    public function __construct(
        private OrganizationContactRepository $organizationContactRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'legalName',
                'label' => 'Organização',
                'placeholder' => 'Selecione uma organização',
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
            ])
            ->add('interactionStatus', EntityType::class, [
                'class' => InteractionStatus::class,
                'choice_label' => 'name',
                'label' => 'Status da Interação',
                'required' => false,
                'placeholder' => 'Nenhum status',
            ])
            ->add('performedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Realizado Por',
                'required' => false,
                'placeholder' => 'Nenhum usuário',
            ])
            ->add('contactedAt', DateTimeType::class, [
                'label' => 'Data do Contato',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
                'data' => new \DateTimeImmutable(),
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Assunto',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Acompanhamento de proposta'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Mensagem',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Descreva o conteúdo da comunicação',
                ],
            ])
            ->add('responseReceived', CheckboxType::class, [
                'label' => 'Houve Resposta',
                'required' => false,
                'attr' => [
                    'data-toggle-response-field' => true,
                ],
            ])
            ->add('responseType', EntityType::class, [
                'class' => ResponseType::class,
                'choice_label' => 'name',
                'label' => 'Tipo de Resposta',
                'required' => false,
                'placeholder' => 'Selecione tipo de resposta',
                'attr' => [
                    'class' => 'form-select',
                    'data-response-field' => true,
                ],
            ])
            ->add('responseText', TextareaType::class, [
                'label' => 'Texto da Resposta',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Registre a resposta recebida',
                    'data-response-field' => true,
                ],
            ])
            ->add('nextContactAt', DateTimeType::class, [
                'label' => 'Próximo Contato',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Observações',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Notas e detalhes adicionais',
                ],
            ]);

        OrganizationContactModifier::addOrganizationContactListener($builder, $this->organizationContactRepository);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganizationContactInteraction::class,
        ]);
    }
}

