<?php

namespace App\Repository;

use App\Entity\Sport\AttendanceSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttendanceSheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttendanceSheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttendanceSheet[]    findAll()
 * @method AttendanceSheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceSheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttendanceSheet::class);
    }

    public function countElement()
    {
        return $this
            ->createQueryBuilder('object')
            ->select("count(object.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params, $groupBy)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('a')
        ->join('a.orientationSheetPlanning', 'op')
        ->join('op.orientationSheet', 'o')
        ->join('op.planning', 'p')
        ->join('o.beneficiary', 'b')
        ->join('p.activity', 'ac')
        ->groupBy($groupBy);

        // Create Count Query
        $countQuery = $this->createQueryBuilder('a')
        ->join('a.orientationSheetPlanning', 'op')
        ->join('op.planning', 'p')
        ->join('op.orientationSheet', 'o')
        ->join('o.beneficiary', 'b')
        ->join('p.activity', 'ac');


        $countQuery->select('COUNT(a)');

        
        // Other conditions than the ones sent by the Ajax call ?
        if ($otherConditions === null)
        {
            // No
            // However, add a "always true" condition to keep an uniform treatment in all cases
            $query->where("1=1");
            $countQuery->where("1=1");
        }
        else
        {
            // Add condition
            $query->andWhere($otherConditions);
            $countQuery->where($otherConditions);
        }
        
        $indexParam = 0;
        // Fields Search
        foreach ($columns as $key => $column)
        {
            if ($column['search']['value'] != '')
            {
                // $searchItem is what we are looking for
                $searchItem = $column['search']['value'];
                $searchQuery = null;
        
                // $column['name'] is the name of the column as sent by the JS
                switch($column['name'])
                {
                    case 'lastName':
                    case 'firstName':
                        $searchQuery = 'b.'.$column['name'].' LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                        break;
                    case 'axe':
                        $searchQuery = 'o.'.$column['name'].' LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                        break;
                    case 'activity':
                        $searchQuery = 'ac.name LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                        break;
                    case 'numberSessions':
                    case 'startDate':
                        $searchQuery = 'p.'.$column['name'].' LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                        break;
                    default:
                        $searchQuery = 'a.'.$column['name'].' LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                    break;
                }
        
                if ($searchQuery !== null)
                {
                    $query->andWhere($searchQuery);
                    $countQuery->andWhere($searchQuery);
                    $query->setParameter(':param'.$indexParam, $paramValue);
                    $countQuery->setParameter(':param'.$indexParam, $paramValue);
                    $indexParam++;
                }
            }
        }
        
        // Limit
        if ($length != '-1')
        {
            $query->setFirstResult($start)->setMaxResults($length);
        }
        
        // Order
        foreach ($orders as $key => $order)
        {
            // $order['name'] is the name of the order column as sent by the JS
            if ($order['name'] != '')
            {
                $orderColumn = null;
            
                switch($order['name'])
                {
                    case 'lastName':
                    case 'firstName':
                        $orderColumn = 'b.'.$order['name'];
                        break;
                    case 'axe':
                        $orderColumn = 'o.'.$order['name'];
                        break;
                    case 'activity':
                    case 'startDate':
                        $orderColumn = 'p.'.$order['name'];
                        break;
                    case 'numberSessions':
                        $orderColumn = 'p.'.$order['name'];
                        break;
                    case 'planning':
                        $orderColumn = 'op.'.$order['name'];
                        break;
                    default:
                        $orderColumn = 'a.'.$order['name'];
                    break;
                }
        
                if ($orderColumn !== null)
                {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }

        }
        
        // Execute
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        
        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );
    }



    /**
     * @return AttendanceSheet[] Returns an array of AttendanceSheet objects
     */
    public function findByOrientationSheet($value)
    {
        return $this->createQueryBuilder('a')
            ->join('a.orientationSheet', 'o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return AttendanceSheet[] Returns an array of AttendanceSheet objects
     */
    public function getAll($tenant, $groupBy, $start = null, $end = null)
    {
        $query = $this->createQueryBuilder('a')
        ->join('a.orientationSheetPlanning', 'op')
        ->join('op.planning', 'p')
        ->join('op.orientationSheet', 'o')
        ->join('o.beneficiary', 'b')
        ->join('p.activity', 'ac')
        ->andWhere( 'a.tenant = :tenant')
        ->setParameter('tenant', $tenant)
        ->andWhere( 'op.confirmed = 1' )
        ->groupBy( $groupBy)
        ->orderBy('p.id');

        
        
        if( $start != null && $end != null )
        {
            $query->andWhere('p.startDate >= :start')
                ->andWhere('p.startDate <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
        }
        return $query->getQuery()->getResult();
    }

    public function countBeneficiary($idPlanning)
    {
        return $this
            ->createQueryBuilder('a')
            ->select("count(o.id)")
            ->join('a.orientationSheetPlanning', 'op')
            ->join('op.planning', 'p')
            ->join('op.orientationSheet', 'o')
            ->join('o.beneficiary', 'b')
            ->andWhere('p.id = :idPlanning')
            ->setParameter('idPlanning', $idPlanning)
            ->andWhere( 'op.confirmed = 1' )
            ->getQuery()
            ->getSingleScalarResult();
    }

    /*
    public function findOneBySomeField($value): ?AttendanceSheet
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
