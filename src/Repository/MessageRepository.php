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
    // message compte
    public function countUnreadMessages(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false')
            ->setParameter('user', $user)
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
    
    
    public function getUnreadCountBySender(User $destinataire): array
    {
        try {
            $qb = $this->createQueryBuilder('m')
                ->select('IDENTITY(m.expediteur) AS sender_id, 
                         SUM(CASE WHEN m.lu = false THEN 1 ELSE 0 END) AS unread_count')
                ->where('m.destinataire = :dest')
                ->groupBy('m.expediteur')
                ->setParameter('dest', $destinataire);

            $results = $qb->getQuery()->getResult();

            $counts = [];
            foreach ($results as $row) {
                if ($row['unread_count'] > 0) {
                    $counts[$row['sender_id']] = $row['unread_count'];
                }
            }

            return $counts;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function markMessagesAsRead(User $expediteur, User $destinataire): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.lu', true)
            ->where('m.expediteur = :exp')
            ->andWhere('m.destinataire = :dest')
            ->andWhere('m.lu = false')
            ->setParameter('exp', $expediteur)
            ->setParameter('dest', $destinataire)
            ->getQuery()
            ->execute();
    }

}
