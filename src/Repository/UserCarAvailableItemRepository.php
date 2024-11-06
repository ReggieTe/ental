<?php

namespace App\Repository;

use App\Entity\UserCarAvailableItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCarAvailableItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCarAvailableItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCarAvailableItem[]    findAll()
 * @method UserCarAvailableItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCarAvailableItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCarAvailableItem::class);
    }

    // /**
    //  * @return UserCarAvailableItem[] Returns an array of UserCarAvailableItem objects
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
    public function findOneBySomeField($value): ?UserCarAvailableItem
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
