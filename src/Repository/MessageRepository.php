<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

#[AsRepository]
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function countUnreadMessagesForUser(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByUsers(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.expediteur = :user1 AND m.destinataire = :user2) OR (m.expediteur = :user2 AND m.destinataire = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
