<?php

namespace App\Repository;

use App\Entity\Intervention;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les requêtes de l'entité Intervention
 * @extends ServiceEntityRepository<Intervention>
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findAll()
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    /**
     * Recherche les interventions par date
     * 
     * @param \DateTimeInterface $date La date à rechercher
     * @return Intervention[] Liste des interventions pour la date donnée
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
     * Recherche les interventions par intervenant
     * 
     * @param mixed $intervenant L'intervenant à rechercher
     * @return Intervention[] Liste des interventions pour l'intervenant donné
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
     * Recherche les interventions par produit
     * 
     * @param mixed $produit Le produit à rechercher
     * @return Intervention[] Liste des interventions pour le produit donné
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
     * Recherche une intervention par un champ spécifique
     * 
     * @param mixed $value La valeur du champ à rechercher
     * @return Intervention|null L'intervention trouvée ou null
     */
    public function findOneBySomeField($value): ?Intervention
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche les interventions pour une date donnée
     * 
     * @param \DateTime $date La date à rechercher
     * @return Intervention[] Liste des interventions pour la date donnée
     */
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

    /**
     * Compte le nombre d'interventions pour aujourd'hui
     * 
     * @return int Le nombre total d'interventions pour aujourd'hui
     */
    public function countInterventionsToday(): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention = :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    /**
     * Compte le nombre d'interventions pour une date donnée
     * 
     * @param string $date La date à rechercher
     * @return int Le nombre total d'interventions pour la date donnée
     */
    public function countInterventionsByDate(string $date): int
    {
        $startDate = new \DateTime($date . ' 00:00:00');
        $endDate = new \DateTime($date . ' 23:59:59');

        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateIntervention BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre d'interventions pour une plage de dates
     * 
     * @param \DateTime $startDate La date de début
     * @param \DateTime $endDate La date de fin
     * @return int Le nombre total d'interventions pour la plage de dates
     */
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

    /**
     * Recherche les interventions avec des filtres
     * 
     * @param User|null $user L'utilisateur (intervenant) à filtrer
     * @param string|null $search Le terme de recherche
     * @param string|null $start La date de début
     * @param string|null $end La date de fin
     * @param string|null $intervenant Le nom de l'intervenant
     * @return Intervention[] Liste des interventions correspondant aux filtres
     */
    public function findByFilters(?User $user = null, ?string $search = '', ?string $start = null, ?string $end = null, ?string $intervenant = ''): array
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.produit', 'p')
            ->leftJoin('p.categorie', 'c')
            ->leftJoin('i.intervenant', 'u')
            ->addSelect('p', 'c', 'u')
            ->orderBy('i.dateIntervention', 'DESC');

        if ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $qb->andWhere('i.intervenant = :user')
                ->setParameter('user', $user);
        }

        if ($search) {
            $qb->andWhere('p.modele LIKE :search OR
                (c.nom IS NOT NULL AND c.nom LIKE :search) OR
                p.marque LIKE :search OR
                i.codeBarre LIKE :search OR
                i.commentaire LIKE :search OR
                i.categorie LIKE :search')
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
            $qb->andWhere("LOWER(CONCAT(u.prenom, ' ', u.nomUser)) LIKE :intervenant")
                ->setParameter('intervenant', '%' . strtolower($intervenant) . '%');
        }

        return $qb->orderBy('i.dateIntervention', 'DESC')->getQuery()->getResult();
    }

    /**
     * Compte le nombre d'interventions pour une plage de dates et un filtre
     * 
     * @param \DateTime $startDate La date de début
     * @param \DateTime $endDate La date de fin
     * @param User|null $user L'utilisateur (intervenant) à filtrer
     * @return int Le nombre total d'interventions correspondant aux critères
     */
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

    /**
     * Compte le nombre de produits uniques pour une plage de dates et un filtre
     * 
     * @param \DateTime $startDate La date de début
     * @param \DateTime $endDate La date de fin
     * @param User|null $user L'utilisateur (intervenant) à filtrer
     * @return int Le nombre total de produits uniques correspondant aux critères
     */
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

    /**
     * Recherche les interventions pour une plage de dates
     * 
     * @param \DateTime $startDate La date de début
     * @param \DateTime $endDate La date de fin
     * @param User|null $user L'utilisateur (intervenant) à filtrer
     * @return Intervention[] Liste des interventions correspondant aux critères
     */
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

    /**
     * Compte le nombre d'interventions par intervenant
     * 
     * @param User $user L'intervenant dont on veut compter les interventions
     * @return int Le nombre total d'interventions
     */
    public function countByUser(User $user): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.intervenant = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}