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
    // MessageRepository.php
    public function countUnreadMessages(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = :false')
            ->setParameters([
                'user' => $user,
                'false' => false
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findBySenderAndReceiver(User $sender, User $receiver)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.expediteur = :sender AND m.destinataire = :receiver) OR (m.expediteur = :receiver AND m.destinataire = :sender)')
            ->setParameter('sender', $sender)
            ->setParameter('receiver', $receiver)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function countUnreadMessagesForUser($user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false')
            ->andWhere('m.isPublic = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findConversation(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.expediteur = :user1 AND m.destinataire = :user2) OR (m.expediteur = :user2 AND m.destinataire = :user1)')
            ->orWhere('m.isPublic = :true')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->setParameter('true', true)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findOtherUsers(User $currentUser): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id != :currentUserId')
            ->setParameter('currentUserId', $currentUser->getId())
            ->getQuery()
            ->getResult();
    }
}
