<?php

namespace App\Repository;

use App\Entity\UserCarAdditional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCarAdditional|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCarAdditional|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCarAdditional[]    findAll()
 * @method UserCarAdditional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCarAdditionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCarAdditional::class);
    }

    // /**
    //  * @return UserCarAdditional[] Returns an array of UserCarAdditional objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserCarAdditional
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
