<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

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
