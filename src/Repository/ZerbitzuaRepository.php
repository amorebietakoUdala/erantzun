<?php

namespace App\Repository;

use App\Entity\Enpresa;
use App\Entity\Zerbitzua;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Zerbitzua|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zerbitzua|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zerbitzua[]    findAll()
 * @method Zerbitzua[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZerbitzuaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zerbitzua::class);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('zerbitzua')
            ->orderBy('zerbitzua.izena_eu', 'ASC');
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createOrderedQueryBuilder($criteria = null)
    {
	$qb = $this->createQueryBuilder('qb');
        $qb->select('qb')
	    ->leftJoin(Enpresa::class,'e',
		\Doctrine\ORM\Query\Expr\Join::WITH,
		'qb.enpresa = e.id')
	;
        if ( $criteria !== null )
        {
            foreach ( $criteria as $eremua => $filtroa ) {
		if ($eremua !== 'role' && $eremua !== 'locale' ) {
		    $qb->andWhere('qb.'.$eremua.' = :'.$eremua)
			->setParameter($eremua, $filtroa);
		}
            }
        }
	$qb->orderBy('e.ordena', 'ASC');
	$qb->addOrderBy('qb.ordena', 'ASC');
//	dump($qb->getQuery()->getResult(),$criteria, $qb);die;
        return $qb;
    }
    
    public function createZerbitzuAktiboakQueryBuilder($enpresa = null)
    {
	$criteria = ['aktibatua' => true ];
	if ( $enpresa != null ) {
	    $criteria ['enpresa'] = $enpresa;
	}
	$qb = $this->createOrderedQueryBuilder($criteria);
        return $qb;
    }

    public function createZerbitzuakQueryBuilder($enpresa = null)
    {
	$criteria = null;
	if ( $enpresa != null ) {
	    $criteria ['enpresa'] = $enpresa;
	}
	$qb = $this->createOrderedQueryBuilder($criteria);
        return $qb;
    }

}
