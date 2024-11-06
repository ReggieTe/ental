<?php

namespace App\Repository;

use App\Entity\UserTripChecklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTripChecklist|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTripChecklist|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTripChecklist[]    findAll()
 * @method UserTripChecklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTripChecklistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTripChecklist::class);
    }

    // /**
    //  * @return UserTripChecklist[] Returns an array of UserTripChecklist objects
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
    public function findOneBySomeField($value): ?UserTripChecklist
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
