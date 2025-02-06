<?php

namespace App\Repository;

use App\Entity\Yacht;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class YachtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Yacht::class);
    }

    //    /**
    //     * @return Yacht[] Returns an array of Yacht objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('y')
    //            ->andWhere('y.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('y.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Yacht
    //    {
    //        return $this->createQueryBuilder('y')
    //            ->andWhere('y.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
