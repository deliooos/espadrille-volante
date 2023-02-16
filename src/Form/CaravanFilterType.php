<?php

namespace App\Form;

use App\Data\CaravanFilter;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaravanFilterType extends AbstractType
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
                    '2 personnes' => 2,
                    '4 personnes' => 4,
                    '6 personnes' => 6,
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CaravanFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
