<?php

namespace App\Repository;

use App\Entity\CategorieProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les requêtes de l'entité CategorieProduit
 * Gère les différentes catégories de produits disponibles
 * @extends ServiceEntityRepository<CategorieProduit>
 */
class CategorieProduitRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieProduit::class);
    }

    /**
     * Recherche des catégories par nom
     * 
     * @param mixed $value Le nom à rechercher
     * @return CategorieProduit[] Liste des catégories correspondant au critère
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('c.nom', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche une catégorie unique par un critère spécifique
     * 
     * @param mixed $value La valeur à rechercher
     * @return CategorieProduit|null La catégorie trouvée ou null si aucune trouvée
     */
    public function findOneByNom($value): ?CategorieProduit
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}