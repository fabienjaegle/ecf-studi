<?php

namespace App\Repository;

use App\Entity\ApiClientsGrants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiClientsGrants>
 *
 * @method ApiClientsGrants|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiClientsGrants|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiClientsGrants[]    findAll()
 * @method ApiClientsGrants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiClientsGrantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiClientsGrants::class);
    }

    public function add(ApiClientsGrants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiClientsGrants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ApiClientsGrants[] Returns an array of ApiClientsGrants objects
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

//    public function findOneBySomeField($value): ?ApiClientsGrants
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
