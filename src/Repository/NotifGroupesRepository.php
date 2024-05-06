<?php

namespace App\Repository;

use App\Entity\NotifGroupes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifGroupes>
 *
 * @method NotifGroupes|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifGroupes|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifGroupes[]    findAll()
 * @method NotifGroupes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifGroupesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifGroupes::class);
    }

    public function save(NotifGroupes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifGroupes $entity, bool $flush = false): void
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


    public function findByGroupnameAndNotification($groupname, $notificationId): ?NotifGroupes
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
//     * @return NotifGroupes[] Returns an array of NotifGroupes objects
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
