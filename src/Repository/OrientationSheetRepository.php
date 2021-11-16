<?php

namespace App\Repository;

use App\Entity\Sport\OrientationSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrientationSheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrientationSheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrientationSheet[]    findAll()
 * @method OrientationSheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrientationSheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrientationSheet::class);
    }

    public function countElement()
    {
        return $this
            ->createQueryBuilder('object')
            ->select("count(object.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('o');
        
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('o');
        $countQuery->select('COUNT(o)');

        
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
            $query->where($otherConditions);
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
                    default:
                        $searchQuery = 'o.'.$column['name'].' LIKE :param'.$indexParam;
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
                    default:
                        $orderColumn = 'o.'.$order['name'];
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

    // /**
    //  * @return OrientationSheet[] Returns an array of OrientationSheet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrientationSheet
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
