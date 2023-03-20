<?php

namespace App\Repository;

use App\Entity\Enpresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Enpresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enpresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enpresa[]    findAll()
 * @method Enpresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enpresa::class);
    }

     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('enpresa')
            ->orderBy('enpresa.izena', 'ASC');
    }

     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createOrderedQueryBuilder()
    {
        return $this->createQueryBuilder('enpresa')
            ->orderBy('enpresa.ordena', 'ASC')
	;
    }

}
