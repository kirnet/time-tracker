<?php

namespace App\Service\Websocket;

use App\Repository\UserRepository;
use App\Utils\TimerEdit;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class InfoRpc implements RpcInterface
{
    /** @var LoggerInterface  */
    private $logger;

    /** @var TimerEdit */
    private $timerEdit;

    /** @var ClientManipulatorInterface */
    private $clientManipulator;

    /** @var UserRepository  */
    private $userRepository;

    /**
     * InfoRpc constructor.
     *
     * @param LoggerInterface $logger
     * @param TimerEdit $timerEdit
     * @param ClientManipulatorInterface $clientManipulator
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        TimerEdit $timerEdit,
        ClientManipulatorInterface $clientManipulator,
        UserRepository $userRepository
    )
    {
        $this->logger = $logger;
        $this->timerEdit = $timerEdit;
        $this->clientManipulator = $clientManipulator;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ConnectionInterface $connection
     * @param WampRequest $request
     * @param $params
     *
     * @return array
     * @throws \Exception
     */
    public function timerEdit(ConnectionInterface $connection, WampRequest $request, $params)
    {
        $email = $this->clientManipulator->getClient($connection)->getUsername();
        $params['user'] = $this->userRepository->findByEmail($email);
        $timer = $this->timerEdit->edit($params);
        $response = [
            'id' => $timer->getId(),
            'state' => $timer->getState()
        ];

        return $response;
    }

    /**
     * Name of RPC, use for pubsub router (see step3)
     *
     * @return string
     */
    public function getName(): string
    {
        return 'info.rpc';
    }
}