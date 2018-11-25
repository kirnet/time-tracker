<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Timer;
use Doctrine\ORM\Query\Expr\Join;


class ProfileController extends AbstractController
{
    /** @var PaginatorInterface  */
    private $paginator;

    /**
     * ProfileController constructor.
     *
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     *
     * @param TimerRepository $timerRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, TimerRepository $timerRepository, ProjectRepository $projectRepository)
    {
        $userId = $this->getUser()->getId();
        $now = time();
        $projects = $projectRepository->findByUserId($userId);
        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $timerRepository->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->orderBy( 't.timerStart', 'DESC')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;

        // Paginate the results of the query
        $timers = $this->paginator->paginate(
        // Doctrine Query, not results
            $allAppointmentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            25
        );

        return $this->render('profile/counters.html.twig', [
            'timers' => $timers,
            'projects' => $projects,
            'now' => $now
        ]);
    }

    /**
     * @Route("/profile/project", name="user_project_list")
     * @param Request $request
     * @param ProjectRepository $projectRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function project(Request $request, ProjectRepository $projectRepository)
    {
        $userId = $this->getUser()->getId();
        $queryBuilder = $projectRepository->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->leftJoin(Timer::class, 't', Join::WITH, 'p.id=t.project')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;

        // Paginate the results of the query
        $projects = $this->paginator->paginate(
        // Doctrine Query, not results
            $queryBuilder,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        foreach ($projects as $project) {
            $counter = 0;
            foreach ($project->getTimers() as $timer) {
                $counter += $timer->getTime();
            }
            $project->time = $counter;
        }

        return $this->render('profile/projects.html.twig', [
            'projects' => $projects
        ]);
    }
}
