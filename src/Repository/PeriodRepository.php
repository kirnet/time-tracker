<?php

namespace App\Repository;

use App\Entity\Period;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Period|null find($id, $lockMode = null, $lockVersion = null)
 * @method Period|null findOneBy(array $criteria, array $orderBy = null)
 * @method Period[]    findAll()
 * @method Period[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;
    private EntityManagerInterface $entityManager;

    /**
     * PeriodRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, Period::class);
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $userId
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPeriodListByUserId($userId, $page = 1)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;

        // Paginate the results of the query
        return $this->paginator->paginate(
        // Doctrine Query, not results
            $queryBuilder,
            // Define the page parameter
            $page,
            // Items per page
            100
        );
    }

    /**
     * @param int $id
     *
     * @return Period
     */
    public function findOneOrCreateById(int $id): Period
    {
        $period = $this->find($id);
        if (!$period) {
            $period = new Period();
        }
        return $period;
    }

    /**
     * @param Period $period
     *
     */
    public function save(Period $period): void
    {
        $this->entityManager->persist($period);
        $this->entityManager->flush();
    }

    /**
     * @param Period $period
     */
    public function delete(Period $period): void
    {
        $this->entityManager->remove($period);
        $this->entityManager->flush();
    }

    // /**
    //  * @return Period[] Returns an array of Period objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Period
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
