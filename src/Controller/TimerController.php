<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Form\TimerType;
use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use App\Utils\TimerEdit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/timer")
 */
class TimerController extends AbstractController
{
    /**
     * @Route("/", name="timer_index", methods="GET")
     * @param TimerRepository $timerRepository
     *
     * @return Response
     */
    public function index(TimerRepository $timerRepository): Response
    {
        return $this->render('timer/index.html.twig', ['timers' => $timerRepository->findAll()]);
    }

    /**
     * @Route("/{id}", name="timer_show", methods="GET")
     * @param Timer $timer
     *
     * @return Response
     */
    public function show(Timer $timer): Response
    {
        return $this->render('timer/show.html.twig', ['timer' => $timer]);
    }

    /**
     * @Route("/edit/{id}", name="timer_edit", defaults={"id"=0})
     * @param Request $request
     * @param TimerEdit $timerEdit
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function edit(Request $request, TimerEdit $timerEdit): jsonResponse
    {
        $params = [
            'id' => $request->get('id', 0),
            'state' => $request->get('state'),
            'name' => $request->get('name'),
            'projectId' => $request->get('projectId'),
            'time' => $request->get('time'),
            'user' => $this->getUser()
        ];

        $timer = $timerEdit->edit($params);
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer([$normalizer], ['json' => new JsonEncoder()]);
        $res = $serializer->serialize($timer, 'json');
        return new JsonResponse($res, 200, [], true);
    }

    /**
     * @Route("/{id}", name="timer_delete", methods="DELETE")
     * @param Request $request
     * @param Timer $timer
     *
     * @return Response
     */
    public function delete(Request $request, Timer $timer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timer->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($timer);
            $em->flush();
        }

        return $this->redirectToRoute('timer_index');
    }
}
