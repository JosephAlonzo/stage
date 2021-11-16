<?php

namespace App\Repository;

use App\Entity\Sport\Beneficiary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oro\ORM\Query\AST\Functions\SimpleFunction;

/**
 * @method Beneficiary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beneficiary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beneficiary[]    findAll()
 * @method Beneficiary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeneficiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beneficiary::class);
    }


    public function countBeneficiaryByGender($tenant)
    {
        return $this
            ->createQueryBuilder('b')
            ->select("sum(case when b.gender='M' then 1 ELSE 0 END) as male")
            ->addSelect("sum(case when b.gender='F' then 1 ELSE 0 END) as female")
            ->addSelect("count(b.id) as total")
            ->andWhere( 'b.tenant = :tenant')
            ->setParameter('tenant', $tenant)   
            ->getQuery()
            ->getResult();
    }

    public function countBeneficiaries($tenant)
    {
        return $this
            ->createQueryBuilder('b')
            ->select("count(b.id) as total")
            ->andWhere( 'b.tenant = :tenant')
            ->setParameter('tenant', $tenant)   
            ->getQuery()
            ->getResult();
    }


    public function beneficiaryMasculine($tenant)
    {
        $result = $this->createQueryBuilder('b')
                ->select('b.gender')
                ->groupBy('b.gender')
                ;
        
        for ($i=0; $i < 81 ; $i+=5) { 
            $limit = $i+5;
            if($i == 80){
                $result->addSelect("SUM(CASE WHEN ROUND( DATE_DIFF( Cast(CURRENT_TIMESTAMP() as Date), Cast(b.dateBirth as Date)  )/ 365, 0 ) > {$i} then 1 ELSE 0 END) AS _{$limit}_plus ");   
            }
            else{
                $result->addSelect("SUM(CASE WHEN ROUND( DATE_DIFF( Cast(CURRENT_TIMESTAMP() as Date), Cast(b.dateBirth as Date)  )/ 365, 0 ) > {$i} and ROUND( DATE_DIFF( Cast(CURRENT_TIMESTAMP() as Date), Cast(b.dateBirth as Date)  )/ 365, 0 ) < {$limit}  then 1 ELSE 0 END) AS _{$i}_{$limit} ");   
            }
        }
        $result->andWhere( 'b.tenant = :tenant')->setParameter('tenant', $tenant);
        return $result->getQuery()->getResult();
    }

    // /**
    //  * @return Beneficiary[] Returns an array of Beneficiary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Beneficiary
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
