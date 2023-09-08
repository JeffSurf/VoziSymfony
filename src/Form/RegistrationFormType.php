<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [

            ])
            ->add('prenom',TextType::class, [

            ])
            ->add('pseudo', TextType::class, [

            ])
            ->add('email')
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'options' => ['attr' => ['class' => 'password-field']],
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être identitque',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                "label" => "Image de profil",
                "mapped" => false,
                "required" => false,
                "help" => "Taille maxi (2MB)",
                "constraints" => [
                    new File([
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Le fichier est trop volumineux ({{ size }} {{ suffix }}). Taille maximale {{ limit }} {{ suffix }}",
                        "mimeTypes" => [
                            "image/png",
                            "image/jpeg",
                            "image/svg+xml"
                        ],
                        "mimeTypesMessage" => "Le type {{ type }} est invalide. Les formats valides sont {{ types }}"
                    ])
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
