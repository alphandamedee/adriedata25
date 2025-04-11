<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countDistinctCategories(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.categorie)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function searchByKeyword(string $keyword): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->where('LOWER(p.categorie) LIKE :kw')
            ->orWhere('LOWER(p.marque) LIKE :kw')
            ->orWhere('LOWER(p.modele) LIKE :kw')
            ->orWhere('LOWER(p.codeBarre) LIKE :kw')
            ->orWhere('LOWER(p.statut) LIKE :kw')
            ->setParameter('kw', '%' . strtolower($keyword) . '%')
            ->orderBy('p.idProduit', 'DESC')
            ->getQuery()
            ->getResult();
    }

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
