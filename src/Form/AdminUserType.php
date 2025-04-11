<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('nomUser', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('mailUser', EmailType::class, [
                'label' => 'Adresse e-mail'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'help' => $options['is_edit'] ? 'Laissez vide pour ne pas changer le mot de passe.' : null
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
                'help' => 'Fichier maximum 2 Mo. Formats acceptés : JPG ou PNG.',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Merci de téléverser une image JPEG ou PNG.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
