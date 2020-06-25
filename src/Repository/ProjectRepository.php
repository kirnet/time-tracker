<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    const NUM_PER_PAGE = 25;

    /** @var PaginatorInterface */
    private $paginator;

    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * ProjectRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Project::class);
        $this->paginator     = $paginator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $userId
     *
     * @return mixed
     */
    public function removeByOwnerId($userId)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Project::class, 'p')
            ->where('p.ownerId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;
        return $query->execute();
    }

    /**
     * @param $userId
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getProjectListByUserId($userId, $page = 1)
    {
        $queryBuilder = $this->createQueryBuilder('p')
              ->addSelect('t')
              ->where('u.id = :userId')
              ->orWhere('p.ownerId = :userId')
              ->leftJoin('p.users', 'u')
              ->leftJoin('p.timers', 't')
              ->setParameter('userId', $userId)
              ->getQuery()
        ;

        // Paginate the results of the query
        $projects = $this->paginator->paginate(
        // Doctrine Query, not results
            $queryBuilder,
            // Define the page parameter
            $page,
            // Items per page
            self::NUM_PER_PAGE
        );
        $this->countTotalTime($projects);
        return $projects;
    }

    /**
     * @param $projects
     *
     * @return mixed
     */
    public function countTotalTime($projects)
    {
        foreach ($projects as $project) {
            $counter = 0;
            foreach ($project->getTimers() as $timer) {
                $counter += $timer->getTime();
            }
            $project->totalTime = $counter;
        }

        return $projects;
    }

    /**
     * @param int $userId
     *
     * @return Project[]
     */
    public function findByOwnerId(int $userId)
    {
        return $this->findBy(['owner' => $userId]);
    }

    /**
     * @param int $id
     *
     * @return Project
     * @throws Exception
     */
    public function findOneOrCreateById(int $id): Project
    {
        $project = $this->find($id);
        if (!$project) {
            $project = new Project();
        }
        return $project;
    }

    /**
     * @param Project $project
     *
     */
    public function save(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    /**
     * @param Project $project
     */
    public function delete(Project $project)
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}
