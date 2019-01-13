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
     * @param array $params ['id', 'name', 'state', 'user']
     *
     * @param null | Timer $timer
     *
     * @return Timer
     * @throws \Exception
     */
    public function edit($params, $timer = null): Timer
    {
        $timerId = ($params['id'] ?? 0);
        $now = new \DateTime();

        if ($timer === null) {
            $timer = $this->timerRepository->findOneOrCreateById($timerId);
        }
        if ($timer->getId() === null) {
            $timer = new Timer();
            $timer->setTimerStart(new \DateTime());
        } else {
            if ($params['state'] === $timer->getState()) {
                return $timer;
            }
        }

        if (isset($params['projectId'])) {
            $project = $this->projectRepository->find($params['projectId']);
            $timer->setProject($project);
        }
        if (isset($params['name'])) {
            $timer->setName($params['name']);
        }
        if (isset($params['state'])) {
            $timer->setState($params['state']);
        }
        if (isset($params['user'])) {
            $timer->setUser($params['user']);
        }
        if ($params['state'] === Timer::STATE_RUNNING) {
            $timer->setTimerStart(new \DateTime());
        }
        else if (in_array($params['state'], [Timer::STATE_PAUSED, Timer::STATE_STOPPED])) {
            if (!empty($params['time'])) {
                $time = $params['time'];
            } else {
                $startTime = $timer->getTimerStart()->getTimestamp();
                $time = $now->getTimestamp() - $startTime;
                $time += $timer->getTime();
            }

            $timer->setTime($time);
        }
        $this->timerRepository->save($timer);
        if ($timer->getState() === Timer::STATE_RUNNING) {
            $this->timerRepository->resetStatus($timer->getId(), $params['user']->getId(), $this);
        }
        return $timer;
    }
}