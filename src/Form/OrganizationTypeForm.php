<?php

namespace App\Form;

use App\Entity\Organization;
use App\Entity\OrganizationType;
use App\Repository\OrganizationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrganizationTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Organization|null $organization */
        $organization = $options['data'];

        $builder
            ->add('legalName', TextType::class, [
                'label' => 'Razão social',
            ])
            ->add('tradeName', TextType::class, [
                'label' => 'Nome fantasia',
                'required' => false,
            ])
            ->add('acronym', TextType::class, [
                'label' => 'Sigla',
                'required' => false,
            ])
            ->add('cnpj', TextType::class, [
                'label' => 'CNPJ (opcional)',
                'required' => false,
                'help' => 'Preencha apenas se a organização possuir CNPJ próprio.',
                'attr' => [
                    'placeholder' => '00.000.000/0000-00',
                ],
            ])
            ->add('organizationType', EntityType::class, [
                'class' => OrganizationType::class,
                'label' => 'Tipo de organização',
                'placeholder' => 'Selecione um tipo',
                'required' => false,
                'choice_label' => 'name',
            ])
            ->add('parent', EntityType::class, [
                'class' => Organization::class,
                'label' => 'Organização pai',
                'placeholder' => 'Sem organização pai',
                'required' => false,
                'choice_label' => static fn (Organization $item): string => self::buildHierarchyLabel($item),
                'query_builder' => static function (OrganizationRepository $repository) use ($organization) {
                    $queryBuilder = $repository->createAlphabeticalQueryBuilder();

                    if (null !== $organization?->getId()) {
                        $queryBuilder
                            ->andWhere('o.id != :currentId')
                            ->setParameter('currentId', $organization->getId());
                    }

                    return $queryBuilder;
                },
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'placeholder' => 'Selecione um status',
                'required' => false,
                'choices' => array_combine(Organization::getAvailableStatuses(), Organization::getAvailableStatuses()),
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Observações',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
        ]);
    }

    private static function buildHierarchyLabel(Organization $organization): string
    {
        $parts = [];
        $current = $organization;

        while (null !== $current) {
            array_unshift($parts, $current->getAcronym() ?: (string) $current);
            $current = $current->getParent();
        }

        return implode(' > ', $parts);
    }
}