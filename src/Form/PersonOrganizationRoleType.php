<?php

namespace App\Form;

use App\Entity\PersonOrganizationRole;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonOrganizationRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'label' => 'Papel',
                'placeholder' => 'Selecione um papel',
            ])
            ->add('startDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data de início',
            ])
            ->add('endDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data de fim',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonOrganizationRole::class,
        ]);
    }
}
