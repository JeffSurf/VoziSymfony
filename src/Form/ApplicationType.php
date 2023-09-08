<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('url', TextType::class, [
                "help" => "lien visible pour les autres élèves"
            ])
            ->add('ip', TextType::class, [
                "help" => "ip avec l'invite de commande (ipconfig -> iPv4)"
            ])
            ->add('image', FileType::class, [
                "label" => "Image de présentation",
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new File([
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Le fichier est trop volumineux ({{ size }} {{ suffix}}). Taille maximale {{ limit }} {{ suffix }}",
                        "mimeTypes" => [
                            "image/png",
                            "image/jpeg",
                            "image/svg+xml"
                        ],
                        "mimeTypesMessage" => "Le type {{ type }} est invalide. Les formats valides sont {{ types }}"
                    ])
                ]
            ])
            ->add('port')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
