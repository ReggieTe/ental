<?php

namespace App\Repository;

use App\Entity\UserCarIssue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCarIssue|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCarIssue|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCarIssue[]    findAll()
 * @method UserCarIssue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCarIssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCarIssue::class);
    }

    // /**
    //  * @return UserCarIssue[] Returns an array of UserCarIssue objects
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
    public function findOneBySomeField($value): ?UserCarIssue
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
