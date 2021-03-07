<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Quel nom souhaitez-vous donner à cette adresse ?',
                    'attr' => [
                        'placeholder' => 'Nommez cette adresse pour la retrouver facilement plus tard'
                    ]
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'attr' => [
                        'placeholder' => 'Renseignez un prénom'
                    ]
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'placeholder' => 'Renseignez un nom'
                    ]
                ]
            )
            ->add(
                'company',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Nom de la société',
                    'attr' => [
                        'placeholder' => '(Facultatif) Renseignez le nom de la société'
                    ]
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'Adresse',
                    'attr' => [
                        'placeholder' => 'Renseignez une l\'adresse postale'
                    ]
                ]
            )
            ->add(
                'postcode',
                TextType::class,
                [
                    'label' => 'Code Postal',
                    'attr' => [
                        'placeholder' => 'Renseignez un code postal'
                    ]
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville',
                    'attr' => [
                        'placeholder' => 'Renseignez la ville'
                    ]
                ]
            )
            ->add(
                'country',
                CountryType::class,
                [
                    'preferred_choices' => ['FR'],
                    'label' => 'Pays',
                    'attr' => [
                        'placeholder' => 'Renseignez un pays'
                    ]
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => 'Téléphone',
                    'attr' => [
                        'placeholder' => 'Renseignez un numéro de téléphone'
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                    'attr' => [
                        'class' => 'btn btn-block btn-info'
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Address::class,
            ]
        );
    }
}
