<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ProfileController extends AbstractController
{
    /** @var PaginatorInterface  */
    private $paginator;
    /** @var UserRepository  */
    private $userRepository;

    /**
     * ProfileController constructor.
     *
     * @param PaginatorInterface $paginator
     * @param UserRepository $userRepository
     */
    public function __construct(PaginatorInterface $paginator, UserRepository $userRepository)
    {
        $this->paginator = $paginator;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param TimerRepository $timerRepository
     *
     * @param ProjectRepository $projectRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, TimerRepository $timerRepository, ProjectRepository $projectRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $projects = $this->userRepository->find($user->getId())->getProjects();
        $now = time();
        // Find all the data on the Appointments table, filter your query as you need
        $queryBuilder = $timerRepository->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->orderBy( 't.timerStart', 'DESC')
            ->setParameter('userId', $user->getId())
            ->getQuery()
        ;

        // Paginate the results of the query
        $timers = $this->paginator->paginate(
        // Doctrine Query, not results
            $queryBuilder,
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
        $page = $request->query->getInt('page', 1);
        $projects = $projectRepository->getProjectListByUserId($userId, $page);

//        $projects = ProjectHelper::countTime($projects);

        return $this->render('profile/projects.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/profile/settings", name="user_settings")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settings(Request $request)
    {
        $user = $this->userRepository->find($this->getUser()->getId());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('profile/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
