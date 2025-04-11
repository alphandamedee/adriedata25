<?php
// filepath: f:\ALPHAND\DatAdrie\ADRIEDATA\src\Form\ProduitType.php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeBarre', TextType::class, [
                'label' => 'Code Barre',
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Ordinateur' => 'Ordinateur',
                    'Portable' => 'Portable',
                    'Ecran' => 'Ecran',
                    'Unité Centrale' => 'Unité Centrale',
                    'ADAPTATEUR' => 'ADAPTATEUR',
                    'Alarme' => 'Alarme',
                    'ALL IN ONE' => 'ALL IN ONE',
                    'AMPLI' => 'AMPLI',
                    'APPLE' => 'APPLE',
                    'Clé USB' => 'Clé USB',
                    'Disque Dur' => 'Disque Dur',
                    'Enceinte' => 'Enceinte',
                    'GPS' => 'GPS',
                    'Imprimante' => 'Imprimante',
                    'iphone' => 'iphone',
                    'MAC' => 'MAC',
                    'Machine à Coudre' => 'Machine à Coudre',
                    'MINI PC' => 'MINI PC',
                    'NAS' => 'NAS',
                    'Onduleur' => 'Onduleur',
                    'Photocopieur' => 'Photocopieur',
                    'Projecteur' => 'Projecteur',
                    'PS' => 'PS',
                    'PS2' => 'PS2',
                    'Routeur' => 'Routeur',
                    'Scanner' => 'Scanner',
                    'Serveur' => 'Serveur',
                    'Station d1accueil' => 'Station d1accueil',
                    'Switch' => 'Switch',
                    'Table' => 'Table',
                    'Tablette' => 'Tablette',
                    'Téléphone' => 'Téléphone',
                    'Téléphone Portable' => 'Téléphone Portable',
                    'TV' => 'TV',
                ],
                'placeholder' => '-- Sélectionnez une catégorie --',
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-select shadow-sm',
                ],
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
            ])
            ->add('stockage', TextType::class, [
                'label' => 'Stockage',
            ])
            ->add('typeStockage', ChoiceType::class, [
                'choices' => [
                    ' ' => ' ',
                    'SSD' => 'SSD',
                    'HDD' => 'HDD',
                ],
                'label' => 'Type de Stockage',
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
                ],
                'label' => 'Statut',
            ])
            ->add('codeEtagere', TextType::class, [
                'label' => 'Étagère',
                'required' => false,
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
            ->add('typeRam', TextType::class, [
                'label' => 'Type de RAM',
                'required' => false,
            // ])
            // ->add('save', SubmitType::class, [
            //     'label' => 'Ajouter le Produit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}