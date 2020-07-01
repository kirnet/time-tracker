<?php

namespace App\Controller;

use App\Events\Events;
use App\Form\PeriodType;
use App\Helpers\DatesHelper;
use App\Message\PeriodAlertQueue;
use App\Repository\PeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class PeriodController extends AbstractController
{
    private PeriodRepository $periodRepository;

    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request)
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
                $message = new PeriodAlertQueue($period);
                $delay = DatesHelper::getDatesDiff(new \DateTime(), $period->getAlertTime());
                $this->dispatchMessage($message, [new DelayStamp($delay * 1000)]);
            }
            return $this->redirectToRoute('user_period_list');
        }

        return $this->render('period/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $period = $this->periodRepository->find($id);
        if ($period) {
            $userId = $this->getUser()->getId();
            if ($period->getUser()->getId() === $userId) {
                $this->periodRepository->delete($period);
                return $this->redirectToRoute('user_period_list');
            }
        }
    }
}
