<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'new_password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne correspondent pas',
                    'required' => true,
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
                    'label' => 'Mettre à jour mon mot de passe',
                    'attr' => [
                        'class' => 'btn btn-block btn-info'
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
