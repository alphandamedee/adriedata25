<?php

namespace App\Repository;

use App\Entity\Intervention;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intervention>
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findAll()
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    /**
     * @return Intervention[] Returns an array of Intervention objects
     */
    public function findByDateIntervention(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.dateIntervention = :date')
            ->setParameter('date', $date)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Intervention[] Returns an array of Intervention objects
     */
    public function findByIntervenant($intervenant): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.intervenant = :intervenant')
            ->setParameter('intervenant', $intervenant)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Intervention[] Returns an array of Intervention objects
     */
    public function findByProduit($produit): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.produit = :produit')
            ->setParameter('produit', $produit)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Intervention|null Returns an Intervention object or null
     */
    public function findOneBySomeField($value): ?Intervention
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDate(\DateTime $date)
    {
        $start = (clone $date)->setTime(0, 0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('i')
            ->andWhere('i.dateIntervention BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function countInterventionsToday(): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention = :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function countInterventionsByDate(string $date): int
    {
        $startDate = new \DateTime($date . ' 00:00:00');
        $endDate = new \DateTime($date . ' 23:59:59');

        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention BETWEEN :start AND :end') // ✅ Corrigé : Remplace DATE() par BETWEEN
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countInterventionsByDateRange(\DateTime $startDate, \DateTime $endDate): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByFilters(?User $user = null, ?string $search = '', ?string $start = null, ?string $end = null, ?string $intervenant = ''): array
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.produit', 'p')
            ->leftJoin('i.intervenant', 'u')
            ->addSelect('p', 'u')
            ->orderBy('i.dateIntervention', 'DESC');

        if ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $qb->andWhere('i.intervenant = :user')
            ->setParameter('user', $user);
        }

        if ($search) {
            $qb->andWhere('p.modele LIKE :search OR
            p.categorie LIKE :search OR
            p.marque LIKE :search OR
            i.codeBarre LIKE :search OR
            i.commentaire LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }

        if ($start) {
            $qb->andWhere('i.dateIntervention >= :start')
            ->setParameter('start', new \DateTime($start));
        }

        if ($end) {
            $qb->andWhere('i.dateIntervention <= :end')
            ->setParameter('end', new \DateTime($end));
        }

        if ($intervenant) {
            $qb->andWhere('LOWER(CONCAT(u.prenom, \' \', u.nomUser)) LIKE :intervenant')
            ->setParameter('intervenant', '%' . strtolower($intervenant) . '%');
        }

        return $qb->orderBy('i.dateIntervention', 'DESC')->getQuery()->getResult();
    }

    public function countInterventionsByDateRangeAndFilter(\DateTime $startDate, \DateTime $endDate, ?User $user = null): int
    {
        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        if ($user) {
            $qb->andWhere('i.intervenant = :user')
               ->setParameter('user', $user);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countUniqueProductsByDateRangeAndFilter(\DateTime $startDate, \DateTime $endDate, ?User $user = null): int
    {
        $qb = $this->createQueryBuilder('i')
        ->select('COUNT(DISTINCT i.codeBarre)')
        ->where('i.dateIntervention BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate);

    if ($user) {
        $qb->andWhere('i.intervenant = :user')
           ->setParameter('user', $user);
    }

    return $qb->getQuery()->getSingleScalarResult();
}

    public function findByDateRange(\DateTime $startDate, \DateTime $endDate, ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.produit', 'p')->addSelect('p')
            ->leftJoin('i.intervenant', 'u')->addSelect('u')
            ->where('i.dateIntervention BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('i.dateIntervention', 'ASC');

        if ($user !== null) {
            $qb->andWhere('i.intervenant = :user')
               ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}