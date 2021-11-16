<?php

namespace App\Repository\Sport;

use App\Entity\Sport\OrientationSheetPlannings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrientationSheetPlannings|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrientationSheetPlannings|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrientationSheetPlannings[]    findAll()
 * @method OrientationSheetPlannings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrientationSheetPlanningsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrientationSheetPlannings::class);
    }

    /**
     * @return OrientationSheetPlannings[] Returns an array of OrientationSheetPlannings objects
     */
    public function findByConfirmedActivity($planning)
    {
        return $this->createQueryBuilder('o')
            ->Where('o.planning = :planning')
            ->andWhere('o.confirmed = true')
            ->setParameter('planning', $planning)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?OrientationSheetPlannings
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
