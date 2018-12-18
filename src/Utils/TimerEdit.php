<?php

namespace App\Utils;


use App\Repository\ProjectRepository;
use App\Repository\TimerRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Timer;

class TimerEdit
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TimerRepository */
    private $timerRepository;

    /**
     * Timer constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param TimerRepository $timerRepository
     */
    public function __construct(ProjectRepository $projectRepository, TimerRepository $timerRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->timerRepository = $timerRepository;
    }

    /**
     * @param array $params
     *
     * @return Timer
     * @throws \Exception
     */
    public function edit($params) : Timer
    {
        $timerId = ($params['id'] ?? 0);
        $timer = $this->timerRepository->findOneOrCreateById($timerId);
        if ($timer->getId() === null) {
            $timer = new Timer();
            $timer->setTimerStart(new \DateTime());
        } else {
            if ($params['state'] === $timer->getState()) {
                return $timer;
            }
        }

//        $this->timerRepository->resetStatus($params['id'], $params['user']->getId());
        if ($params['projectId']) {
            $project = $this->projectRepository->find($params['projectId']);
            $timer->setProject($project);
        }
        $timer->setName($params['name']);
        $timer->setState($params['state']);
        $timer->setUser($params['user']);

        if ($params['state'] === Timer::STATE_RUNNING) {
            $timer->setTimerStart(new \DateTime());
        }
        else if (in_array($params['state'], [Timer::STATE_PAUSED, Timer::STATE_STOPPED])) {
//            $timer->setTimerStart(new \DateTime());
            $startTime = $timer->getTimerStart()->getTimestamp();
            $now = new \DateTime();
            $time = $now->getTimestamp() - $startTime;
            $time += $timer->getTime();
            $timer->setTime($time);
        }

        $this->timerRepository->save($timer);
        return $timer;
    }
}