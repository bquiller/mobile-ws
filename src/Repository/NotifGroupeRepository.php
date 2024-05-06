<?php

namespace App\Repository;

use App\Entity\NotifGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotifGroupe>
 *
 * @method NotifGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifGroupe[]    findAll()
 * @method NotifGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifGroupe::class);
    }

    public function save(NotifGroupe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotifGroupe $entity, bool $flush = false): void
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


    public function findByGroupnameAndNotification($groupname, $notificationId): ?NotifGroupe
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
//     * @return NotifGroupe[] Returns an array of NotifGroupe objects
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
