<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];

        $builder
            ->add('destinataire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'prenom',
                'label' => 'Destinataire',
                'placeholder' => 'Tous (message public)', // null dans l'entité
                'required' => false,
                'query_builder' => function ($repo) use ($currentUser) {
                    return $repo->createQueryBuilder('u')
                        ->where('u != :me')
                        ->setParameter('me', $currentUser);
                },
                'attr' => ['class' => 'form-select'],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre message...',
                    'rows' => 3,
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Message public',
                'required' => false,
                'mapped' => true
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary mt-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'current_user' => null, // important pour filtrer la liste
        ]);
    }
}
