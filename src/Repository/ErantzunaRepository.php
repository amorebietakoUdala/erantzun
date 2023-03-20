<?php

namespace App\Repository;

use App\Entity\Erantzuna;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Erantzuna|null find($id, $lockMode = null, $lockVersion = null)
 * @method Erantzuna|null findOneBy(array $criteria, array $orderBy = null)
 * @method Erantzuna[]    findAll()
 * @method Erantzuna[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErantzunaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Erantzuna::class);
    }
}
