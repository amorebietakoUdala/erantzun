<?php

namespace App\Repository;

use App\Entity\Estatistika;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param array $criteria
     * @param mixed $orderBy
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

        $sql =
            'SELECT e.urtea, enp.izena as enpresa, sum(e.eskakizunak) as eskakizunak
             FROM view_estatistikak e 
             LEFT JOIN enpresak enp on enpresa_id = enp.id';
        if (null !== $nora) {
            $sql = $sql.' WHERE e.data <= :nora';
        }
        if (null !== $noiztik) {
            $sql = $sql.' AND e.data >= :noiztik';
        }
        if (null !== $enpresa) {
            $sql = $sql.' AND e.enpresa_id = :enpresa';
        }
        $sql = $sql.' GROUP BY e.urtea, enp.izena';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        if (null !== $noiztik) {
            $stmt->bindValue('noiztik', $noiztik->format('Y-m-d H:i'));
        }
        if (null !== $nora) {
            $stmt->bindValue('nora', $nora->format('Y-m-d H:i'));
        }
        if (null !== $enpresa) {
            $stmt->bindValue('enpresa', $enpresa->getId());
        }
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'App\Entity\Estatistika');
        return $result;
    }
}
