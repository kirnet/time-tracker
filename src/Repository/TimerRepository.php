<?php

namespace App\Repository;

use App\Entity\Timer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Timer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timer[]    findAll()
 * @method Timer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Timer::class);
    }

    public function resetStatus($timerId, $userId)
    {
        $activeTimers = $this->createQueryBuilder('t')
            ->andWhere('t.user=' . $userId)
            ->andWhere("t.state='run'")
            ->andWhere('t.id!=' . $timerId)
            ->getQuery()
            ->getResult()
        ;
        $sql = "UPDATE timer SET state='pause'";
        foreach ($activeTimers as $timer) {
            $createdAt = $timer->getCreatedAt();

        }
    }

    /**
     * @param int $id
     *
     * @return Timer
     */
    public function findOneOrCreateById(int $id)
    {
        $timer = $this->find($id);
        if (!$timer) {
            $timer = new Timer();
        }
        return $timer;
    }

//    /**
//     * @return Timer[] Returns an array of User objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
