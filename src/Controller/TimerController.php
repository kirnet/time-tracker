<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Timer;
use App\Form\TimerType;
use App\Repository\TimerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/timer")
 * Todo Delete this file and all views
 */
class TimerController extends AbstractController
{
    /**
     * @Route("/", name="timer_index", methods="GET")
     */
    public function index(TimerRepository $timerRepository): Response
    {
        return $this->render('timer/index.html.twig', ['timers' => $timerRepository->findAll()]);
    }

    /**
     * @Route("/new", name="timer_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $timer = new Timer();
        $form = $this->createForm(TimerType::class, $timer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($timer);
            $em->flush();

            return $this->redirectToRoute('timer_index');
        }

        return $this->render('timer/new.html.twig', [
            'timer' => $timer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="timer_show", methods="GET")
     */
    public function show(Timer $timer): Response
    {
        return $this->render('timer/show.html.twig', ['timer' => $timer]);
    }

    /**
     * @Route("/edit/{id}", name="timer_edit", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request, TimerRepository $timerRepository): jsonResponse
    {
//        if (!$request->isXmlHttpRequest()) {
//            return new Response('none');
//        }

        $id = $request->get('id', 0);
        $state = $request->get('state');
        $name = $request->get('name');
        $projectId = $request->get('project_id');
//        $time = $request->get('time');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($id) {
            $timer = $timerRepository->find($id);
        } else {
            $timer = new Timer();
            $timer->setTimerStart(new \DateTime());
        }

        $timerRepository->resetStatus($id, $user->getId());

        if ($projectId) {
            $project = $this->getDoctrine()->getRepository(Project::class)->find($projectId);
            $timer->setProject($project);
        }
        $timer->setName($name);
        $timer->setState($state);
        $timer->setUser($user);

        if ($state === Timer::STATE_RUNING && !$timer->getTimerStart()) {
            $timer->setTimerStart(new \DateTime());
        }
        else if (in_array($state, [Timer::STATE_PAUSED, Timer::STATE_STOPPED])) {
//            $timer->setTimerStart(new \DateTime());
            $startTime = $timer->getTimerStart()->getTimestamp();
            $now = new \DateTime();
            $time = $now->getTimestamp() - $startTime;
            $timer->setTime($time);
        }

        $em->persist($timer);
        $em->flush();
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
