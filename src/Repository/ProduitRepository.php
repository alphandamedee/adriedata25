<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les requêtes de l'entité Produit
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    
    /**
     * Compte le nombre de catégories distinctes dans la table produit
     * 
     * @return int Le nombre total de catégories différentes
     */
    public function countDistinctCategories(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.categorie)')
            ->getQuery() // Exécute la requête
            ->getSingleScalarResult(); // Récupère le résultat unique
    }

    /**
     * Recherche des produits par mot-clé dans plusieurs champs
     * 
     * @param string $keyword Le mot-clé à rechercher
     * @return array Liste des produits correspondant au critère de recherche
     */
    public function searchByKeyword(string $keyword): array
    {
        $qb = $this->createQueryBuilder('p'); 

        return $qb
            ->where('LOWER(p.categorie) LIKE :kw') 
            ->orWhere('LOWER(p.marque) LIKE :kw') 
            ->orWhere('LOWER(p.modele) LIKE :kw')
            ->orWhere('LOWER(p.codeBarre) LIKE :kw')
            ->orWhere('LOWER(p.statut) LIKE :kw')
            ->setParameter('kw', '%' . strtolower($keyword) . '%') // Utilisation de strtolower pour ignorer la casse
            ->orderBy('p.idProduit', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des produits par code-barre ou d'autres champs spécifiques
     * 
     * @param string $search Le terme à rechercher
     * @return array Liste des produits correspondant aux critères
     */
    public function findByCodeBarreOrOtherFields(string $search): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.codeBarre LIKE :search')
            ->orWhere('p.categorie LIKE :search')
            ->orWhere('p.marque LIKE :search')
            ->orWhere('p.modele LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
