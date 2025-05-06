<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\CategorieProduit;
use App\Repository\CategorieProduitRepository;
use App\Entity\TypeRam;
use App\Repository\TypeRamRepository;
use App\Entity\TypeStockage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeBarre', TextType::class, [
                'label' => 'Code Barre',
                'attr' => [
                    'minlength' => 19,
                    'maxlength' => 19,
                    'pattern' => '\d{19}',
                    'title' => '19 chiffres requis',
                    'placeholder' => 'Ex : 2024010112345672504',
                ],
                'required' => true,
            ])
            ->add('codeEtagere', TextType::class, [
                'label' => 'Étagère',
                'attr' => [
                    'placeholder' => 'Ex : A1',
                ],
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
                'placeholder' => '-- Sélectionnez une catégorie --',
                'query_builder' => function (CategorieProduitRepository $er) {
                    return $er->createQueryBuilder('c')
                            ->where('c.visible = true')
                            ->orderBy('c.nom', 'ASC');},
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
            ])
            ->add('taille', TextType::class, [
                'label' => 'Taille',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 15.6" (pour écrans/portables) ou Tour/SFF/Mini PC',
                    'class' => 'form-control'
                ],
                'help' => 'Pour écrans/portables: en pouces (ex: 15.6"). Pour UC: Tour, SFF, Mini PC'
            ])
            ->add('stockage', IntegerType::class, [
                'label' => 'Stockage (en Go)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 256, 500, 1000',
                    'class' => 'form-control'
                ]
            ])
            ->add('typeStockage', EntityType::class, [
                'class' => TypeStockage::class,
                'choice_label' => 'nom',
                'label' => 'Type de Stockage',
                'required' => false,
                'placeholder' => 'Sélectionner un type de stockage',
                'attr' => ['class' => 'form-control']
            ])
            ->add('numeroSerie', TextType::class, [
                'label' => 'Numéro de série',
                'required' => false,
            ])
            ->add('cpu', TextType::class, [
                'label' => 'CPU',
                'required' => false,
            ])
            ->add('frequenceCpu', TextType::class, [
                'label' => 'Fréquence CPU',
                'required' => false,
            ])

            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'à intervenir' => 'à intervenir',
                    'Disponible' => 'Disponible',
                    'Réparé' => 'Réparé',
                    'Vendu' => 'Vendu',
                    'HS' => 'HS',
                    'Jetté' => 'Jetté',
                    'A détruire' => 'A détruire',
                ],
                'label' => 'Statut',
            ])
           
            ->add('carteGraphique', TextType::class, [
                'label' => 'Carte Graphique',
                'required' => false,
            ])
            ->add('memoireVideo', TextType::class, [
                'label' => 'Mémoire Vidéo',
                'required' => false,
            ])
            ->add('ram', TextType::class, [
                'label' => 'Capacité RAM',
                'required' => false,
            ])
            ->add('typeRam', EntityType::class, [
                'class' => TypeRam::class,
                'choice_label' => 'nom',
                'placeholder' => '-- Sélectionnez un type de RAM --',
                'query_builder' => function (TypeRamRepository $er) {
                    return $er->createQueryBuilder('t')
                            ->where('t.visible = true')
                            ->orderBy('t.nom', 'ASC');},
                'label' => 'Type de RAM',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}