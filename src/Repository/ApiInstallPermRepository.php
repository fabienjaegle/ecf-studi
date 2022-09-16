<?php

namespace App\Repository;

use App\Entity\ApiInstallPerm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiInstallPerm>
 *
 * @method ApiInstallPerm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiInstallPerm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiInstallPerm[]    findAll()
 * @method ApiInstallPerm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiInstallPermRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiInstallPerm::class);
    }

    public function add(ApiInstallPerm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiInstallPerm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ApiInstallPerm[] Returns an array of ApiInstallPerm objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ApiInstallPerm
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
