<?php

namespace App\Repository;

use App\Entity\NotifEnregistrement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifEnregistrement>
 *
 * @method NotifEnregistrement|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifEnregistrement|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifEnregistrement[]    findAll()
 * @method NotifEnregistrement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifEnregistrementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifEnregistrement::class);
    }

    public function save(NotifEnregistrement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifEnregistrement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByUsername($username): ?array
    {
        return $this->createQueryBuilder('nu')
            ->andWhere('nu.username = :val1')
            ->setParameter('val1', $username)
            ->orderBy('nu.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findByUsernameAndToken($username, $token): ?NotifEnregistrement
    {
        return $this->createQueryBuilder('ne')
            ->andWhere('ne.username = :val1')
            ->andWhere('ne.token = :val2')
            ->setParameter('val1', $username)
            ->setParameter('val2', $token)
//            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return NotifEnregistrement[] Returns an array of NotifEnregistrement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notification
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}