<?php

namespace App\Repository;

use App\Entity\NotifUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifUtilisateur>
 *
 * @method NotifUtilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifUtilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifUtilisateur[]    findAll()
 * @method NotifUtilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifUtilisateur::class);
    }

    public function save(NotifUtilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifUtilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByUsername($username, $offset, $limit): ?array
    {
        return $this->createQueryBuilder('nu')
            ->andWhere('nu.username = :val1')
            ->setParameter('val1', $username)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('nu.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findByUsernameAndNotification($username, $notificationId): ?NotifUtilisateur
    {
        return $this->createQueryBuilder('nu')
            ->andWhere('nu.username = :val1')
            ->andWhere('nu.notification = :val2')
            ->setParameter('val1', $username)
            ->setParameter('val2', $notificationId)
//            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return NotifUtilisateur[] Returns an array of NotifUtilisateur objects
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
