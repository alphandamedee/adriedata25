<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les requêtes de l'entité User (utilisateur)
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Compte le nombre d'utilisateurs par rôle
     * 
     * @param string $roleName Le nom du rôle à compter
     * @return int Le nombre d'utilisateurs ayant ce rôle
     */
    public function countByRole(string $roleName): int
    {
        return $this->createQueryBuilder('u')
            ->join('u.role', 'r')
            ->andWhere('r.roleName = :roleName')
            ->setParameter('roleName', $roleName)
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche les utilisateurs par rôle
     * 
     * @param string $role Le rôle à rechercher
     * @return array Liste des utilisateurs ayant ce rôle
     */
    public function findByRole(string $role): array
    {
        $users = $this->createQueryBuilder('u')
            ->orderBy('u.nomUser', 'ASC')
            ->getQuery()
            ->getResult();

        return array_filter($users, function ($user) use ($role) {
            return in_array($role, $user->getRoles(), true);
        });
    }
}
