<?php

namespace App\Repository;

use App\Entity\TypeRam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les requêtes de l'entité TypeRam
 * Gère les différents types de mémoire RAM disponibles
 * @extends ServiceEntityRepository<TypeRam>
 */
class TypeRamRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRam::class);
    }
    public function findByNom(string $nom): ?TypeRam
    {
        return $this->findOneBy(['nom' => $nom]);
    }

}