<?php

namespace App\Repository;

use App\Entity\Franchise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Franchise>
 *
 * @method Franchise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Franchise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Franchise[]    findAll()
 * @method Franchise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FranchiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Franchise::class);
    }

    public function add(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getActiveFranchises($value): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->orderBy('f.id', 'ASC')
            ->andWhere('f.isActive = 1')
            ->andWhere('f.domain = :domain')
            ->setParameter('domain', $value);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getInactiveFranchises($value): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->orderBy('f.id', 'ASC')
            ->andWhere('f.isActive = 0')
            ->andWhere('f.domain = :domain')
            ->setParameter('domain', $value);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Franchise[] Returns an array of Franchise objects
     */
    public function findByNameField($value = null): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->orderBy('f.id', 'ASC')
            ->addSelect();

        if ($value) {
            $queryBuilder->andWhere('f.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $value . '%');
        }

        return $queryBuilder
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
