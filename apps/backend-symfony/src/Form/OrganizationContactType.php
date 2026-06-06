<?php

namespace App\Form;

use App\Entity\OrganizationContact;
use App\Entity\Organization;
use App\Entity\ContactType;
use App\Entity\ContactStatus;
use App\Entity\ContactIssueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'legalName',
                'label' => 'Organização',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('o')
                        ->orderBy('o.legalName', 'ASC');
                },
            ])
            ->add('contactType', EntityType::class, [
                'class' => ContactType::class,
                'choice_label' => 'name',
                'label' => 'Tipo de Contato',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('ct')
                        ->orderBy('ct.name', 'ASC');
                },
            ])
            ->add('value', TextType::class, [
                'label' => 'Valor do Contato',
            ])
            ->add('label', TextType::class, [
                'label' => 'Rótulo',
                'required' => false,
            ])
            ->add('status', EntityType::class, [
                'class' => ContactStatus::class,
                'choice_label' => 'name',
                'label' => 'Status',
                'required' => false,
            ])
            ->add('issueType', EntityType::class, [
                'class' => ContactIssueType::class,
                'choice_label' => 'name',
                'label' => 'Motivo do Problema',
                'required' => false,
            ])
            ->add('isPrimary', CheckboxType::class, [
                'label' => 'Contato Principal',
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Público',
                'required' => false,
            ])
            ->add('deactivatedAt', DateTimeType::class, [
                'label' => 'Data de Desativação',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('deactivationReason', TextareaType::class, [
                'label' => 'Motivo da Desativação',
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
            'data_class' => OrganizationContact::class,
        ]);
    }
}