<?php

namespace App\Repository;

use App\Entity\Timer;
use App\Utils\TimerEdit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    /** @var PaginatorInterface  */
    private $paginator;

    /**
     * TimerRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Timer::class);
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @param $timerId
     * @param $userId
     * @param TimerEdit $timerEdit
     *
     * @throws \Exception
     */
    public function resetStatus($timerId, $userId, TimerEdit $timerEdit)
    {
        $activeTimers = $this->createQueryBuilder('t')
            ->where("t.state='" . Timer::STATE_RUNNING . "'")
            ->andWhere('t.user = ' . $userId)
            ->andWhere('t.id != ' . $timerId)
            ->getQuery()
            ->getResult()
        ;
        $params = ['state' => Timer::STATE_PAUSED];
        foreach ($activeTimers as $timer) {
            $timerEdit->edit($params, $timer);
        }
    }

    /**
     * @param $userId
     * @param int $page
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getTimerListByUserId($userId, $page = 1)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->orderBy( 't.timerStart', 'DESC')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;

        // Paginate the results of the query
        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            25
        );
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
