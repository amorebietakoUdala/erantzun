<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function findAllOrderedByOrdena()
    {
        return $this->createQueryBuilder('erabiltzailea')
            ->orderBy('erabiltzailea.ordena', 'DESC');
    }
    
     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function findArduradunakQueryBuilder($enpresa)
    {
        return $this->createQueryBuilder('arduraduna')
            ->where('arduraduna.roles LIKE :role')
            ->andWhere('arduraduna.enpresa = :enpresa')
            ->setParameter('role', '%ROLE_ARDURADUNA%')
            ->setParameter('enpresa', $enpresa )
            ->orderBy('arduraduna.id', 'DESC');
    }

     /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function findAllArduradunakQueryBuilder()
    {
        return $this->createQueryBuilder('arduraduna')
            ->where('arduraduna.roles LIKE :role')
            ->setParameter('role', '%ROLE_ARDURADUNA%')
            ->orderBy('arduraduna.id', 'DESC');
    }
    
    /**
    * @param string $role
    *
    * @return array
    */
   public function findByRole($role)
   {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
	   ->from($this->_entityName, 'u')
	   ->where('u.roles LIKE :roles')
	   ->setParameter('roles', '%"'.$role.'"%')
	   ->andWhere('u.activated = true');

       return $qb->getQuery()->getResult();
   }
}
