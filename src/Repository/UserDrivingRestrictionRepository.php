<?php

namespace App\Repository;

use App\Entity\UserDrivingRestriction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserDrivingRestriction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDrivingRestriction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDrivingRestriction[]    findAll()
 * @method UserDrivingRestriction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDrivingRestrictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDrivingRestriction::class);
    }

    // /**
    //  * @return UserDrivingRestriction[] Returns an array of UserDrivingRestriction objects
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
    public function findOneBySomeField($value): ?UserDrivingRestriction
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
