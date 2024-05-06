<?php

namespace App\Repository;

use App\Entity\NotifGroupesUtilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifGroupesUtilisateurs>
 *
 * @method NotifGroupesUtilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifGroupesUtilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifGroupesUtilisateurs[]    findAll()
 * @method NotifGroupesUtilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifGroupesUtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifGroupesUtilisateurs::class);
    }

    public function save(NotifGroupesUtilisateurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifGroupesUtilisateurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByGroupname($groupname, $offset, $limit): ?array
    {
        return $this->createQueryBuilder('nu')
            ->andWhere('nu.groupname = :val1')
            ->setParameter('val1', $groupname)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('nu.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findByGroupnameAndNotification($groupname, $notificationId): ?NotifGroupesUtilisateurs
    {
        return $this->createQueryBuilder('nu')
            ->andWhere('nu.groupname = :val1')
            ->andWhere('nu.notification = :val2')
            ->setParameter('val1', $groupname)
            ->setParameter('val2', $notificationId)
//            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return NotifGroupesUtilisateurs[] Returns an array of NotifGroupesUtilisateurs objects
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
