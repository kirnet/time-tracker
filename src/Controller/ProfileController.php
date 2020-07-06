<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\UserType;
use App\Helpers\DatesHelper;
use App\Repository\PeriodRepository;
use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ProfileController extends AbstractController
{
    private PaginatorInterface $paginator;
    private UserRepository $userRepository;
    private ProjectRepository $projectRepository;
    private PeriodRepository $periodRepository;

    /**
     * ProfileController constructor.
     *
     * @param PaginatorInterface $paginator
     * @param UserRepository $userRepository
     * @param ProjectRepository $projectRepository
     * @param PeriodRepository $periodRepository
     */
    public function __construct(
        PaginatorInterface $paginator,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        PeriodRepository $periodRepository

    )
    {
        $this->paginator = $paginator;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->periodRepository = $periodRepository;
    }

    /**
     * @param Request $request
     * @param TimerRepository $timerRepository
     *
     *
     * @return Response
     */
    public function index(Request $request, TimerRepository $timerRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $projects = $this->userRepository->find($user->getId())->getProjects();
        $now = time();
        $page = $request->query->getInt('page', 1);
        $timers = $timerRepository->getTimerListByUserId($user->getId(), $page);
        $params = [
            'timers' => $timers,
            'projects' => $projects,
            'now' => $now
        ];
        if ($request->isXmlHttpRequest()) {
            return new Response($this->renderView('profile/_counters_list.html.twig', $params));
        }

        return $this->render('profile/counters.html.twig', $params);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function project(Request $request)
    {
        $userId = $this->getUser()->getId();
        $page = $request->query->getInt('page', 1);
        $projects = $this->projectRepository->getProjectListByUserId($userId, $page);

//        $projects = ProjectHelper::countTime($projects);

        return $this->render('profile/projects.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function period(Request $request)
    {
        $userId = $this->getUser()->getId();
        $page = $request->query->getInt('page', 1);
        $periods = $this->periodRepository->getPeriodListByUserId($userId, $page);

        return $this->render('profile/periods.html.twig', [
            'periods' => $periods
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function counterBlockForm(Request $request)
    {
        $timerData = $request->get('data');
        $projects = $this->userRepository->find($this->getUser()->getId())->getProjects();
        return new Response($this->renderView('profile/_current_counter_block.html.twig', [
            'projects' => $projects,
            'timerData' => $timerData
        ]));
    }
}
