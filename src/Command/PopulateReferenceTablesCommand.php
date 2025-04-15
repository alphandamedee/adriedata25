<?php

namespace App\Command;

use App\Entity\CategorieProduit;
use App\Entity\TypeRam;
use App\Entity\TypeStockage; // Si vous avez aussi cette entité
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-reference-tables',
    description: 'Peuple les tables de référence (catégories, types de RAM, etc.) et migre les données existantes',
)]
class PopulateReferenceTablesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ProduitRepository $produitRepository;

    public function __construct(EntityManagerInterface $entityManager, ProduitRepository $produitRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->produitRepository = $produitRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Peupler CategorieProduit
        $io->section('Peuplement des catégories de produits');
        
        $categories = [
            'Ordinateur', 'Portable', 'Ecran', 'Unité Centrale', 'ADAPTATEUR', 'Alarme', 
            'ALL IN ONE', 'AMPLI', 'APPLE', 'Clé USB', 'Disque Dur', 
            'Enceinte', 'GPS', 'Imprimante', 'iphone', 'MAC', 
            'Machine à Coudre', 'MINI PC', 'NAS', 'Onduleur', 'Photocopieur', 
            'Projecteur', 'PS', 'PS2', 'Routeur', 'Scanner', 'Serveur',
            'Station d1accueil', 'Switch', 'Table', 'Tablette', 
            'Téléphone', 'Téléphone Portable', 'TV'
        ];

        $categoriesByName = [];
        foreach ($categories as $categoryName) {
            // Vérifier si la catégorie existe déjà
            $existingCategory = $this->entityManager->getRepository(CategorieProduit::class)->findOneBy(['nom' => $categoryName]);
            
            if (!$existingCategory) {
                $category = new CategorieProduit();
                $category->setNom($categoryName);
                $this->entityManager->persist($category);
                $io->text("Ajout de la catégorie : $categoryName");
                $categoriesByName[$categoryName] = $category;
            } else {
                $categoriesByName[$categoryName] = $existingCategory;
                $io->text("La catégorie '$categoryName' existe déjà, ignorée.");
            }
        }
        
        $this->entityManager->flush();
        $io->success('Catégories de produits créées avec succès.');

        // 2. Peupler TypeRam
        $io->section('Peuplement des types de RAM');
        
        $typeRams = [
            'DDR1', 'DDR2', 'DDR3', 'DDR4', 'DDR5', 'SDRAM', 'LPDDR4', 'LPDDR5'
        ];

        $typeRamsByName = [];
        foreach ($typeRams as $ramName) {
            // Vérifier si le type existe déjà
            $existingType = $this->entityManager->getRepository(TypeRam::class)->findOneBy(['nom' => $ramName]);
            
            if (!$existingType) {
                $typeRam = new TypeRam();
                $typeRam->setNom($ramName);
                $this->entityManager->persist($typeRam);
                $io->text("Ajout du type de RAM : $ramName");
                $typeRamsByName[$ramName] = $typeRam;
            } else {
                $typeRamsByName[$ramName] = $existingType;
                $io->text("Le type de RAM '$ramName' existe déjà, ignoré.");
            }
        }
        
        $this->entityManager->flush();
        $io->success('Types de RAM créés avec succès.');

        // 3. Mettre à jour les produits existants
        $io->section('Mise à jour des produits existants');
        
        $produits = $this->produitRepository->findAll();
        $updatedCategories = 0;
        $updatedRamTypes = 0;
        
        foreach ($produits as $produit) {
            $categorieValue = $produit->getCategorie();
            if (is_string($categorieValue) && isset($categoriesByName[$categorieValue])) {
                $produit->setCategorie($categoriesByName[$categorieValue]);
                $updatedCategories++;
            }
            
            $typeRamValue = $produit->getTypeRam();
            if (is_string($typeRamValue) && isset($typeRamsByName[$typeRamValue])) {
                $produit->setTypeRam($typeRamsByName[$typeRamValue]);
                $updatedRamTypes++;
            }
        }
        
        $this->entityManager->flush();
        $io->success("Produits mis à jour : $updatedCategories catégories et $updatedRamTypes types de RAM.");

        return Command::SUCCESS;
    }
}