<?php

namespace App\Form;

use App\Entity\Organization;
use App\Entity\Person;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nome Completo',
            ])
            ->add('cpf', TextType::class, [
                'required' => false,
                'label' => 'CPF (opcional)',
                'help' => 'Preencha o CPF apenas quando disponível.',
                'attr' => [
                    'placeholder' => '000.000.000-00',
                ],
            ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'legalName',
                'required' => false,
                'mapped' => false,
                'placeholder' => 'Selecione uma organização (opcional)',
                'label' => 'Organização (vínculo inicial)',
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => false,
                'mapped' => false,
                'placeholder' => 'Selecione um papel (opcional)',
                'label' => 'Papel',
            ])
            ->add('startDate', DateType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Data de início',
                'widget' => 'single_text',
            ])
            ->add('status', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Status do vínculo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}