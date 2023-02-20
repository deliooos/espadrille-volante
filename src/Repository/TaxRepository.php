<?php

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tax>
 *
 * @method Tax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tax[]    findAll()
 * @method Tax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    public function save(Tax $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tax $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Tax[] Returns an array of Tax objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tax
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @throws NonUniqueResultException
     */
    public function getAdultStayTax()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.label = :label')
            ->setParameter('label', 'stay')
            ->andWhere('t.applicant = :applicant')
            ->setParameter('applicant', 'adult')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getChildStayTax()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.label = :label')
            ->setParameter('label', 'stay')
            ->andWhere('t.applicant = :applicant')
            ->setParameter('applicant', 'child')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getAdultPoolTax()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.label = :label')
            ->setParameter('label', 'pool')
            ->andWhere('t.applicant = :applicant')
            ->setParameter('applicant', 'adult')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getChildPoolTax()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.label = :label')
            ->setParameter('label', 'pool')
            ->andWhere('t.applicant = :applicant')
            ->setParameter('applicant', 'child')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
