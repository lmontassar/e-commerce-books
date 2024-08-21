<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\CommandeRelation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<CommandeRelation>
 *
 * @method CommandeRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeRelation[]    findAll()
 * @method CommandeRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeRelation::class);
    }

    //    /**
    //     * @return CommandeRelation[] Returns an array of CommandeRelation objects
    //     */
    // public function CalcTotal($Commande): ?float
    // {
    //     return $this->createQueryBuilder('cr')
    //         ->select('SUM(cr.Livre.prix * cr.qte) as total')
    //         ->andWhere('cr.Commande = :Commande')
    //         ->setParameter('Commande', $Commande)
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }

    public function GetLivreselesTestMode()
    {
        return $this->createQueryBuilder("c")
            ->select('SUM(c.Qte) AS qte_vente, c.Livre')
            ->groupBy('c.Livre')
            ->getQuery()
            ->getResult();
    }


    public function GetLivreseles()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT l.id,l.titre, SUM(c.`qte`) AS qte_vente
            FROM commande_relation c join livres l ON l.id = c.livre_id  
            GROUP BY livre_id
            order by qte_vente DESC
            ';
        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }
    public function GetQtePerCategorie(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
                SELECT      c.libelle AS Categorie,
                            SUM(cr.qte) AS QuantiteVendue,
                            (SUM(cr.qte) * 100.0 / (SELECT SUM(cr2.qte) 
                                                    FROM commande_relation cr2)) 
                                                    AS Pourcentage 
                FROM commande_relation cr 
                JOIN livres l ON cr.livre_id = l.id 
                JOIN categories c ON l.categorie_id = c.id 
                GROUP BY c.libelle;
        ";
        $resultSet = $conn->executeQuery($sql,);
        return $resultSet->fetchAllAssociative();
    }
}
