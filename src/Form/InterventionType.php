<?php
namespace App\Form;

use App\Entity\Intervention;
use App\Entity\User;
use App\Entity\TypeRam;
use App\Entity\TypeStockage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intervenant', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'prenom',
                'label' => 'Intervenant',
                'attr' => ['class' => 'form-control', 'readonly' => true],
                'data' => $options['intervenant'] ?? null
                
            ])
            ->add('codeBarre', TextType::class, [
                'label' => 'Code Barre',
                'required' => false
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie',
                'required' => false,
                'attr' => ['readonly' => true]
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('taille', TextType::class, [
                'label' => 'Taille',
                'required' => false
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'required' => false
            ])
            ->add('numeroSerie', TextType::class, [
                'label' => 'Numéro de Série',
                'required' => false
            ])
            ->add('cpu', TextType::class, [
                'label' => 'CPU',
                'required' => false
            ])
            ->add('frequenceCpu', TextType::class, [
                'label' => 'Fréquence CPU',
                'required' => false
            ])
            ->add('ram', ChoiceType::class, [
                'label' => 'RAM',
                'choices' => [
                    'N/A' => 'N/A',
                    '2 Go' => '2 Go',
                    '4 Go' => '4 Go',
                    '8 Go' => '8 Go',
                    '12 Go' => '12 Go',
                    '16 Go' => '16 Go',
                    '24 Go' => '24 Go',
                    '32 Go' => '32 Go',   
                ],
                'placeholder' => 'Selectionner même vide',
                // 'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('typeRam', ChoiceType::class, [
                'label' => 'Type de RAM',
                'choices' => [
                    'N/A' => 'N/A',
                    'DDR3' => 'DDR3',
                    'DDR4' => 'DDR4',
                    'DDR5' => 'DDR5',
                    'LPDDR4' => 'LPDDR4',
                    'LPDDR5' => 'LPDDR5',
                    'SDRAM' => 'SDRAM',
                ],
                'placeholder' => 'Sélectionner...',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('stockage', TextType::class, [
                'label' => 'Stockage (en Go)',
                'attr' => [
                    'placeholder' => 'Ex: 256, 500, 1000',
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('typeStockage', EntityType::class, [
                'class' => TypeStockage::class,
                'choice_label' => 'nom',
                'label' => 'Type de Stockage',
                'required' => false,
                'placeholder' => 'Sélectionner un type de stockage',
                'attr' => ['class' => 'form-control']
            ])
            ->add('carteGraphique', TextType::class, [
                'label' => 'Carte Graphique',
                'required' => false
            ])
            ->add('memoireVideo', TextType::class, [
                'label' => 'Mémoire Vidéo',
                // 'required' => false
            ])
            ->add('systemeExploitation', TextType::class, [
                'label' => 'Système d\'Exploitation',
                'required' => false
            ])
            ->add('versionSe', TextType::class, [
                'label' => 'Version SE',
                'required' => true
            ])
            ->add('dateIntervention', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'data' => new \DateTime(),
                'label' => 'Date d\'Intervention',
                'required' => true,
                'mapped' => true,
                'input' => 'datetime'
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                // 'required' => true
            ])
            ->add('miseAJourWindows', CheckboxType::class, [
                'label' => 'Mise à Jour Windows',
                'required' => false
            ])
            ->add('miseAJourPilotes', CheckboxType::class, [
                'label' => 'Mise à Jour Pilotes',
                'required' => false
            ])
            ->add('autresLogiciels', CheckboxType::class, [
                'label' => 'Autres Logiciels',
                'required' => false
            ])
            ->add('codeEtagere', TextType::class, [
                'label' => 'Code Étagère',
                // 'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le code de l\'étagère',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    ' ' => ' ',
                    'Disponible' => 'Disponible',
                    'Réparé' => 'Réparé',
                    'Vendu' => 'Vendu',
                    'HS' => 'HS',
                    'Déchetterie' => 'Déchetterie',
                    'Autre' => 'Autre',
                ],
                'label' => 'Status',
                // 'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],    
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
            'intervenant' => null,
        ]);
    }
}
