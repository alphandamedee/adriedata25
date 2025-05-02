<?php

namespace App\Repository;

use App\Entity\TypeStockage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeStockage>
 *
 * @method TypeStockage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeStockage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeStockage[]    findAll()
 * @method TypeStockage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeStockageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeStockage::class);
    }

    public function save(TypeStockage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeStockage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}