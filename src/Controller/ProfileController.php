<?php

namespace App\Controller;

use App\Repository\TimerRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Timer;

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
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TimerRepository $timerRepository */
        $timerRepository = $em->getRepository(Timer::class);
        $userId = $this->getUser()->getId();

        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $timerRepository->createQueryBuilder('t')
            ->where('t.user = :userId')
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
            5
        );

        return $this->render('profile/index.html.twig', [
            'timers' => $timers,
        ]);
    }
}
