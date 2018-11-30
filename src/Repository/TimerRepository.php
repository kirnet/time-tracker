<?php

namespace App\Repository;

use App\Entity\Timer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Timer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timer[]    findAll()
 * @method Timer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimerRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * TimerRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Timer::class);
        $this->entityManager = $entityManager;
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

    /**
     * @param Timer $timer
     */
    public function save(Timer $timer): void
    {
        $this->entityManager->persist($timer);
        $this->entityManager->flush();
    }
}
