<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 *
 * @method Livres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livres[]    findAll()
 * @method Livres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

//    /**
//     * @return Livres[] Returns an array of Livres objects
//     */
   public function findGreaterThan($prix): array
   {
       return $this->createQueryBuilder('l')
           ->andWhere('l.prix > :val')
           ->setParameter('val', $prix)
           ->orderBy('l.id', 'ASC')
           //->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }


   public function findBySearch($text): array
   {
       return $this->createQueryBuilder('l')
           ->leftJoin('l.categorie', 'c')
           ->andWhere('l.titre LIKE :text OR l.auteur LIKE :text OR c.libelle LIKE :text')
           ->setParameter('text', '%' . $text . '%')
           ->orderBy('l.id', 'ASC')
           //->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }



   public function findInRange($min,$max): array
   {
       return $this->createQueryBuilder('l')
           ->andWhere('l.prix < :max')
           ->andWhere('l.prix > :min')
           ->setParameter('max', $max)
           ->setParameter('min',$min)
           ->orderBy('l.id', 'ASC')
           //->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneBySomeField($livre)
   {
       return $this->createQueryBuilder('l') 
            ->where('l.Categorie = :categorie')
            ->andWhere('l.Livre != :livre')
            ->setParameter('categorie', $livre->getCategorie())
            ->setParameter('livre', $livre)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
   }
}
