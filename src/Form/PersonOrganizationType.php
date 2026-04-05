<?php

namespace App\Form;

use App\Entity\Organization;
use App\Entity\PersonOrganization;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonOrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_organization']) {
            $builder->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'legalName',
                'required' => true,
                'label' => 'Organização',
                'placeholder' => 'Selecione uma organização',
            ]);
        }

        $builder
            ->add('startDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data de início',
            ])
            ->add('endDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Data de fim',
            ])
            ->add('status', TextType::class, [
                'required' => false,
                'label' => 'Status',
                'help' => 'Informe o status do vínculo, por exemplo Ativo ou Encerrado.',
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'Observações',
                'attr' => ['rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonOrganization::class,
            'include_organization' => true,
        ]);

        $resolver->setAllowedTypes('include_organization', 'bool');
    }
}
