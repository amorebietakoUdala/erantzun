<?php

namespace App\Repository;

use App\Entity\Jatorria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Jatorria|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jatorria|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jatorria[]    findAll()
 * @method Jatorria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JatorriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jatorria::class);
    }
    
    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createOrderedQueryBuilder()
    {
        return $this->createQueryBuilder('jatorria')
            ->orderBy('jatorria.id', 'DESC');
    }
}
