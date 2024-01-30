<?php

namespace App\Repository;

use App\Entity\Eskatzailea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Eskatzailea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eskatzailea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eskatzailea[]    findAll()
 * @method Eskatzailea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EskatzaileaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eskatzailea::class);
    }

    /**
     * @return QueryBuilder
     */
    public function createOrderedQueryBuilder()
    {
        return $this->createQueryBuilder('Eskatzailea')
            ->orderBy('eskatzailea.id', 'DESC');
    }
    
    /**
     * 
     * @param type $id
     * @return Eskatzailea
     */
    public function getEskatzaileaById($id)
    {
        $query = $this->createQueryBuilder('Eskatzailea')
            ->where('eskatzailea.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $eskatzailea = $query->getFirstResult();
        return $eskatzailea;
    }

    /**
     * 
     * @param string $izena
     * @return Eskatzailea
     */
    public function getEskatzaileaByIzenaLike($izena)
    {
        $query = $this->createQueryBuilder('eskatzailea')
            ->where('eskatzailea.izena LIKE :izena')
            ->orderBy('eskatzailea.id', 'ASC')
            ->setParameter('izena', '%'.$izena.'%')
            ->getQuery();
        $eskatzailea = $query->getResult();
        
        return $eskatzailea;
    }

     /**
     * @param array $criteria
     * @return QueryBuilder
     */
    public function findAllLikeQueryBuilder($criteria = null )
    {
        $qb = $this->createQueryBuilder('e');
        
        if ( $criteria )
        {
            foreach ( $criteria as $eremua => $filtroa ) {
                $qb->andWhere('e.'.$eremua.' LIKE :'.$eremua)
                    ->setParameter($eremua, '%'.$filtroa.'%');
            }
        }
        return $qb;
    }

     /**
     * @param array $criteria
     * @return Eskatzailea[]
     */
    public function findAllLike($criteria = null )
    {
        return $this->findAllLikeQueryBuilder($criteria)->getQuery()->getResult();
    }
}
