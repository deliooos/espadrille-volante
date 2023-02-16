<?php

namespace App\Form;

use App\Entity\Booking;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MobileHomeBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $startDateData = DateTime::createFromFormat('d-m-Y', '05-05-2023') > new DateTime('now') ? DateTime::createFromFormat('d-m-Y', '05-05-2023') : new DateTime();

        $endDateData = DateTime::createFromFormat('d-m-Y', '05-05-2023') > new DateTime('now') ? DateTime::createFromFormat('d-m-Y', '05-05-2023')->modify('+2 week') : DateTime::createFromFormat('d-m-Y', $startDateData)->modify('+2 week');

        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer un email valide',
                    ])
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre numéro de téléphone',
                    ]),
                ],
            ])
            ->add('nbrAdults', TextType::class, [
                'data' => '2',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nombre d\'adultes',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 1,
                        'message' => 'Veuillez entrer un nombre d\'adultes supérieur ou égal à {{ value }}',
                    ]),
                    new LessThanOrEqual([
                        'value' => 8,
                        'message' => 'Veuillez entrer un nombre d\'adultes inférieur ou égal à {{ value }}',
                    ]),
                ],
            ])
            ->add('nbrChildren', TextType::class, [
                'data' => '0',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nombre d\'enfants',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Veuillez entrer un nombre d\'enfants supérieur ou égal à {{ value }}',
                    ]),
                    new LessThanOrEqual([
                        'value' => 8,
                        'message' => 'Veuillez entrer un nombre d\'enfants inférieur ou égal à {{ value }}',
                    ]),
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'data' => $startDateData,
                'property_path' => 'startDate',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une date de début',
                    ]),
                    new LessThan([
                        'propertyPath' => 'endDate',
                    ])
                ],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'data' => $endDateData,
                'property_path' => 'endDate',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une date de fin',
                    ]),
                    new GreaterThan([
                        'propertyPath' => 'startDate',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
