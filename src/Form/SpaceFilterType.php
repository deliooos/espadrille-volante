<?php

namespace App\Form;

use App\Data\SpaceFilter;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'data' => DateTime::createFromFormat('d-m-Y', '05-05-2023'),
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'data' => DateTime::createFromFormat('d-m-Y', '05-05-2023')->modify('+2 week'),
            ])
            ->add('size', ChoiceType::class, [
                'choices' => [
                    '8m2' => 8,
                    '12m2' => 12,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SpaceFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
