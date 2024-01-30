<?php

namespace App\Repository;

use App\Entity\EskakizunMota;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EskakizunMota|null find($id, $lockMode = null, $lockVersion = null)
 * @method EskakizunMota|null findOneBy(array $criteria, array $orderBy = null)
 * @method EskakizunMota[]    findAll()
 * @method EskakizunMota[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EskakizunMotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EskakizunMota::class);
    }

     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createOrderedQueryBuilder()
    {
        return $this->createQueryBuilder('eskakizunMota')
            ->orderBy('eskakizunMota.id', 'DESC');
    }
}
