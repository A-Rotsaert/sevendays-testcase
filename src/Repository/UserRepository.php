<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
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

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @param $start
     * @param $length
     * @param $orders
     * @param $search
     * @param $columns
     *
     * @return array
     */
    public function getDatatable($start, $length, $orders, $search, $columns)
    {
        $column_mapping = [
            'u.id',
            'u.name',
            'u.email',
            'u.roles',
            'u.createdAt',
            'u.updatedAt'
        ];
        $qb = $this->createQueryBuilder('u')
            ->select('u.id');
        $total = count($qb->getQuery()->getResult());
        $qb->select(implode(',', $column_mapping));
        $filtered = count($qb->getQuery()->getResult());
        $qb->setFirstResult($start)
            ->setMaxResults($length);
        if ($orders[0]['column'] !== "") {
            $qb->orderBy($column_mapping[$orders[0]['column']], strtoupper($orders[0]['dir']));
        } else {
            $qb->orderBy('u.id', 'desc');
        }
        if (!empty($search['value'])) {
            $qb->where('u.id LIKE :searchTerm')
                ->orWhere('u.name LIKE :searchTerm')
                ->orWhere('u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $search['value'] . '%');
        }

        $result = $qb->getQuery()->getArrayResult();
        return [
            'result' => $result,
            'filtered' => $filtered,
            'total' => $total
        ];
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
