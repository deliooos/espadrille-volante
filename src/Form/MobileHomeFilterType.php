<?php

namespace App\Form;

use App\Data\MobileHomeFilter;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MobileHomeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'data' => DateTime::createFromFormat('d-m-Y', '05-05-2023'),
                'row_attr' => [
                    'class' => 'select',
                ],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'data' => DateTime::createFromFormat('d-m-Y', '05-05-2023')->modify('+2 week'),
            ])
            ->add('size', ChoiceType::class, [
                'choices' => [
                    '3 personnes' => 3,
                    '4 personnes' => 4,
                    '5 personnes' => 5,
                    '8 personnes' => 8,
                ],
            ])
            ->add('fromCompanyOnly', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MobileHomeFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
