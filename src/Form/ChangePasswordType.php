<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Votre Nom',
                    'disabled' => true
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Votre Prénom',
                    'disabled' => true
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Votre Email',
                    'disabled' => true
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => true,
                    'mapped' => false,
                    'label' => 'Mon mot de passe actuel',
                    'attr' => [
                        'placeholder' => 'Veuillez renseigner votre mot de passe actuel'
                    ],
                    'constraints' => [
                        new UserPassword(message: 'Le mot de passe renseigné est invalide')
                    ]
                ]
            )
            ->add(
                'new_password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne correspondent pas',
                    'required' => true,
                    'mapped' => false,
                    'first_options' => [
                        'label' => 'Mon nouveau mot de passe',
                        'attr' => [
                            'placeholder' => 'Veuillez renseigner votre nouveau mot de passe'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirmation de mon nouveau mot de passe',
                        'attr' => [
                            'placeholder' => 'Veuillez confirmer votre nouveau mot de passe'
                        ]
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Mettre à jour'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
