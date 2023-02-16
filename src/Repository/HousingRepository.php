<?php

namespace App\Repository;

use App\Data\CaravanFilter;
use App\Data\MobileHomeFilter;
use App\Data\SpaceFilter;
use App\Entity\Housing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Housing>
 *
 * @method Housing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Housing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Housing[]    findAll()
 * @method Housing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HousingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Housing::class);
    }

    public function save(Housing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Housing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Housing[] Returns an array of Housing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Housing
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findMobileHomeSearch(MobileHomeFilter $data)
    {
        $query = $this->createQueryBuilder('h')
            ->select('h')
            ->andWhere('h.type = :type')
            ->setParameter('type', 'mobilehome');

        if (!empty($data->startDate) && !empty($data->endDate)) {
            $query = $query
                ->andWhere('h.id NOT IN (
                    SELECT IDENTITY(b.housing)
                    FROM App\Entity\Booking b
                    WHERE b.startDate <= :startDate AND b.endDate >= :endDate
                )')
                ->setParameter('startDate', $data->startDate)
                ->setParameter('endDate', $data->endDate);
        }

        if (!empty($data->size)) {
            $query = $query
                ->andWhere('h.size = :size')
                ->setParameter('size', $data->size);
        }

        if (!empty($data->fromCompanyOnly) && $data->fromCompanyOnly === true) {
            $query = $query
                ->andWhere('h.owner IS NULL');
        }

        return $query->getQuery()->getResult();
    }

    public function findCaravanSearch(CaravanFilter $data)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', 'caravan');

        if (!empty($data->startDate) && !empty($data->endDate)) {
            $query = $query
                ->andWhere('c.id NOT IN (
                    SELECT IDENTITY(b.housing)
                    FROM App\Entity\Booking b
                    WHERE b.startDate <= :startDate AND b.endDate >= :endDate
                )')
                ->setParameter('startDate', $data->startDate)
                ->setParameter('endDate', $data->endDate);
        }

        if (!empty($data->size)) {
            $query = $query
                ->andWhere('c.size = :size')
                ->setParameter('size', $data->size);
        }

        return $query->getQuery()->getResult();
    }

    public function findSpaceSearch(SpaceFilter $data)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'space');

        if (!empty($data->startDate) && !empty($data->endDate)) {
            $query = $query
                ->andWhere('s.id NOT IN (
                    SELECT IDENTITY(b.housing)
                    FROM App\Entity\Booking b
                    WHERE b.startDate <= :startDate AND b.endDate >= :endDate
                )')
                ->setParameter('startDate', $data->startDate)
                ->setParameter('endDate', $data->endDate);
        }

        if (!empty($data->size)) {
            $query = $query
                ->andWhere('s.surface = :size')
                ->setParameter('size', $data->size);
        }

        return $query->getQuery()->getResult();
    }
}
