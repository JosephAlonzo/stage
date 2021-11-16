<?php

namespace App\Repository;

use App\Entity\Sport\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Planning|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planning|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planning[]    findAll()
 * @method Planning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    /**
     * @return Planning[] Returns an array of Planning objects
     */
    public function findBetweenDates($start, $end, $tenantId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.startDate >= :start')
            ->andWhere('p.startDate <= :end')
            ->andWhere('p.tenant = :tenant')
            ->orderBy('p.day', 'ASC')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tenant', $tenantId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array[] Returns an array of Planning objects
     */
    public function comparePlanningSessions($planning, $holidays, $end)
    {
        $start = $planning->getStartDate();
        $tenantId = $planning->getTenant();
        $day = $planning->getDay();
        $beginningTime = $planning->getBeginningTime();
        $endingTime = $planning->getEndingTime();
        $place = $planning->getPlace()->getId();
        $educator = $planning->getEducator()->getId();
        $id = $planning->getId();

        $validatePlace = $this->createQueryBuilder('p')
            ->Where('p.beginningTime <= :beginningTime and p.endingTime <= :endingTime')
            ->OrWhere('p.beginningTime >= :beginningTime and p.endingTime >= :endingTime')
            ->OrWhere(':beginningTime <= p.beginningTime and :endingTime >= p.endingTime')
            ->OrWhere(' p.beginningTime <= :beginningTime and p.endingTime >= :endingTime')
            ->andWhere( ':endingTime > p.beginningTime' )
            ->andWhere( ':beginningTime < p.endingTime' )
            ->andwhere('p.endDate >= :start')
            ->andWhere('p.startDate <= :end')
            ->andWhere('p.tenant = :tenant')
            ->andWhere('p.day = :day') 
            ->andWhere('p.place = :place')
            ->orderBy('p.day', 'ASC')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tenant', $tenantId)
            ->setParameter('day', $day)
            ->setParameter('beginningTime', $beginningTime)
            ->setParameter('endingTime', $endingTime)
            ->setParameter('place', $place)
        ;

        $validateEducator = $this->createQueryBuilder('p')
            ->Where('p.beginningTime <= :beginningTime and p.endingTime <= :endingTime')
            ->OrWhere('p.beginningTime >= :beginningTime and p.endingTime >= :endingTime')
            ->OrWhere(':beginningTime <= p.beginningTime and :endingTime >= p.endingTime')
            ->OrWhere(' p.beginningTime <= :beginningTime and p.endingTime >= :endingTime')
            ->andWhere( ':endingTime > p.beginningTime' )
            ->andWhere( ':beginningTime < p.endingTime' )
            ->andwhere('p.endDate >= :start')
            ->andWhere('p.startDate <= :end')
            ->andWhere('p.tenant = :tenant')
            ->andWhere('p.day = :day')
            ->andWhere('p.educator = :educator')
            ->orderBy('p.day', 'ASC')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tenant', $tenantId)
            ->setParameter('day', $day)
            ->setParameter('beginningTime', $beginningTime)
            ->setParameter('endingTime', $endingTime)
            ->setParameter('educator', $educator)
        ;
        if($id){
            $validatePlace
            ->andWhere('p.id != :id')
            ->setParameter('id', $id);

            $validateEducator
            ->andWhere('p.id != :id')
            ->setParameter('id', $id);
        }
        $validatePlace = $validatePlace->getQuery()->getResult();
        $validateEducator = $validateEducator->getQuery()->getResult();
        

        $array =  [ "place" => count($validatePlace) > 0 ? false : true , "educator" => count($validateEducator) > 0 ? false : true   ];
        return $array;
    }

    


    public function getMaxNumberSessions($startDate)
    {

        $start = new \DateTime('first day of january '.  $startDate->format('Y'));
        $end = new \DateTime('last day of december '. $startDate->format('Y'));

        return $this
            ->createQueryBuilder('p')
            ->select('MAX(p.numberSessions) AS maxNumberSessions')
            ->where('p.startDate >= :start')
            ->andWhere('p.startDate <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
