<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Adresse Email',
            'attr' => [
                'placeholder' => 'Entrez votre email',
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'adresse email ne doit pas être vide.',
                ]),
                new Email([
                    'message' => 'L\'adresse email "{{ value }}" n\'est pas une adresse valide.',
                ]),
            ],
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'placeholder' => 'Entrez votre nom',
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le nom ne doit pas être vide.',
                ]),
                new Length([
                    'max' => 50,
                    'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom ne doit contenir que des lettres.',
                ]),
            ],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => [
                'placeholder' => 'Entrez votre prénom',
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le prénom ne doit pas être vide.',
                ]),
                new Length([
                    'max' => 50,
                    'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères.',
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le prénom ne doit contenir que des lettres.',
                ]),
            ],
        ])
            ->add('Numtel', TelType::class, [
                'label' => 'Numéro de Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de téléphone ne doit pas être vide.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9\-\(\)\/\+\s]*$/',
                        'message' => 'Le numéro de téléphone n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('Adresse', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse',
                    'class' => 'form-control',
                    'rows' => 4,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse ne doit pas être vide.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
