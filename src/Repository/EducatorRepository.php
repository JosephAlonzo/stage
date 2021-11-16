<?php

namespace App\Repository;

use App\Entity\Sport\Educator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Educator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Educator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Educator[]    findAll()
 * @method Educator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Educator::class);
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
        $query = $this->createQueryBuilder('e')->join('e.user', 'u');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('e')->join('e.user', 'u');
        $countQuery->select('COUNT(e)');

        
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
                    case 'lastName':
                    case 'firstName':
                    case 'phoneNumber':
                    case 'email':
                        $searchQuery = 'u.'.$column['name'].' LIKE :param'.$indexParam;
                        $paramValue = '%'.$searchItem.'%';
                        break;
                    default:
                        $searchQuery = 'e.'.$column['name'].' LIKE :param'.$indexParam;
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
                    case 'phoneNumber':
                    case 'email':
                        $orderColumn = 'u.'.$order['name'];
                        break;
                    default:
                        $orderColumn = 'e.'.$order['name'];
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
    //  * @return Educator[] Returns an array of Educator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Educator
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
