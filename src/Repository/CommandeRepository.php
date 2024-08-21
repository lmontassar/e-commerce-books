<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }


   public function CountOrdersLast12Months($currentDate,$startDate)
   {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            select MONTH(date_commande_at) AS month , YEAR(date_commande_at) as year , COUNT(id) AS orderCount
            from Commande
            where date_commande_at BETWEEN :start AND :end and etat > 0
            group by month
            ';
        $resultSet = $conn->executeQuery($sql, ['start' =>$startDate->format('Y-m-d') ,'end'=>$currentDate->format('Y-m-d')]);
       return $resultSet->fetchAllAssociative();
   }

    public function findPanier($Client) //: ?Commande
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.etat = :val')
            ->andWhere('c.Client = :client')
            ->setParameter('val', 0)
            ->setParameter('client', $Client)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
