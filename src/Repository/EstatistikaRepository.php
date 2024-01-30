<?php

namespace App\Repository;

use App\Entity\Enpresa;
use App\Entity\Estatistika;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Estatistika|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estatistika|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estatistika[]    findAll()
 * @method Estatistika[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstatistikaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estatistika::class);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function findAllOrderDescQB()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.urtea', 'DESC');
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function findDistinctUrteak()
    {
        return $this->createQueryBuilder('e')
        ->select('est.urtea')
        ->distinct()
        ->from('App:Estatistika', 'est');
    }

    private function _remove_blank_filters($criteria)
    {
        $new_criteria = [];
        foreach ($criteria as $key => $value) {
            if (!empty($value)) {
                $new_criteria[$key] = $value;
            }
        }

        return $new_criteria;
    }

    /**
     *  Return an Estatistika array of the resulting records from the criteria.
     *
     * @param type  $limit
     * @param type  $offset
     *
     * @return array<App\Entity\Estatistika>
     */
    public function findEskakizunKopuruakNoiztikNora(array $criteria, mixed $orderBy = null, $limit = null, $offset = null)
    {
        $criteria1 = $this->_remove_blank_filters($criteria);
        $noiztik = (array_key_exists('noiztik', $criteria1)) ? $criteria1['noiztik'] : null;
        $nora = (array_key_exists('nora', $criteria1)) ? $criteria1['nora'] : new \DateTime();
        $enpresa = (array_key_exists('enpresa', $criteria1)) ? $criteria1['enpresa'] : null;

        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder->select('e.urtea, enp.izena as enpresa, SUM(e.eskakizunak) as eskakizunak')
                     ->leftJoin(Enpresa::class, 'enp', 'WITH', 'e.enpresa = enp.id')
                     ->groupBy('e.urtea, enp.izena');

        if (null !== $nora) {
            $queryBuilder->andWhere('e.data <= :nora')
                            ->setParameter('nora', $nora);
        }
        if (null !== $noiztik) {
            $queryBuilder->andWhere('e.data >= :noiztik')
                            ->setParameter('noiztik', $noiztik);
        }
        if (null !== $enpresa) {
            $queryBuilder->andWhere('e.enpresa_id = :enpresa')
                            ->setParameter('enpresa', $enpresa);
        }

        $resultsArray = $queryBuilder->getQuery()->getResult();
        $results = [];
        foreach($resultsArray as $resultData) {
            $result = new Estatistika();
            $results[] = $result->fill($resultData);
        }
        return $results;
    }
}
