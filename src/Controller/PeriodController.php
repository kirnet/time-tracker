<?php

namespace App\Controller;

use App\Events\Events;
use App\Form\PeriodType;
use App\Message\PeriodAlertQueue;
use App\Repository\PeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PeriodController extends AbstractController
{
    private PeriodRepository $periodRepository;

    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }

    /**
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function edit(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $id = $request->get('id');
        $period = $this->periodRepository->findOneOrCreateById($id);
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($period->getId() === null) {
                $period->setUser($this->getUser());
            }
            $this->periodRepository->save($period);
            if ($period->getAlertTime()) {
                $message = new PeriodAlertQueue('Test rabbitMQ');
                $this->dispatchMessage($message);
            }
//            $event = new GenericEvent($period);
//            $eventDispatcher->dispatch($event, Events::PERIOD_CREATED);

            return $this->redirectToRoute('user_period_list');
        }

        return $this->render('period/edit.html.twig', ['form' => $form->createView()]);
    }
}
