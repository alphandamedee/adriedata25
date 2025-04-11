<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'disabled' => $options['disable_fields'],
                'label' => 'Prénom'
            ])
            ->add('nomUser', TextType::class, [
                'disabled' => $options['disable_fields'],
                'label' => 'Nom'
            ])
            ->add('mailUser', EmailType::class, [
                'disabled' => $options['disable_fields'],
                'label' => 'Adresse e-mail'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // Tu peux gérer le hash dans le contrôleur
                'required' => false // Pas obligatoire si on ne change pas
            ])

            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'roleName',
                'label' => 'Rôle de l’utilisateur'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil (JPG ou PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Merci de téléverser une image JPEG ou PNG.',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // si true, le mot de passe n’est pas requis
            'disable_fields' => false,
        ]);
    }
}
