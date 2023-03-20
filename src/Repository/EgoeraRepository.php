<?php

namespace App\Repository;

use App\Entity\Egoera;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Egoera|null find($id, $lockMode = null, $lockVersion = null)
 * @method Egoera|null findOneBy(array $criteria, array $orderBy = null)
 * @method Egoera[]    findAll()
 * @method Egoera[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EgoeraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Egoera::class);
    }

     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createOrderedQueryBuilder()
    {
        return $this->createQueryBuilder('egoera')
            ->orderBy('egoera.id', 'DESC');
    }
}
