<?php

namespace App\Service\Websocket;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

/**
 * Class InfoTopic
 * @package App\Service\Websocket
 */
class InfoTopic implements TopicInterface
{
    private LoggerInterface $logger;
    protected ClientManipulatorInterface $clientManipulator;

    public function __construct(LoggerInterface $logger, ClientManipulatorInterface $clientManipulator)
    {
        $this->logger = $logger;
        $this->clientManipulator = $clientManipulator;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $username = $this->clientManipulator->getClient($connection)->getUsername();
        $userConnections = $this->clientManipulator->findAllByUsername($topic, $username);

        //$this->logger->info('socket user' . json_encode($user));
        //this will broadcast the message to ALL subscribers of this topic.

        foreach ($userConnections as $userConnection) {
            if ($$userConnection->getConnection()->resourceId !== $connection->resourceId && $username == $userConnection['client']->getUser()->getEmail()) {
                $topic->broadcast(
                    ['msg' => $connection->resourceId . "{$username} has joined " . $topic->getId()],
                    [],
                    [$userConnection['connection']->WAMP->sessionId]
                );
            }
        }
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     *
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     *
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $eligible = $this->createEligible($connection, $topic);
        $exclude = $eligible ? [] : [$connection->WAMP->sessionId];
        $topic->broadcast($event, $exclude, $eligible);
    }

    /**
     * @param object $connection
     * @param Topic $topic
     *
     * @return array
     */
    public function createEligible($connection, Topic $topic): array
    {
        $username = $this->clientManipulator->getClient($connection)->getUsername();
        $subscribers = $this->clientManipulator->getAll($topic);
        $currentSessionId = $connection->WAMP->sessionId;
        $eligible = [];
        foreach ($subscribers as $subscriber) {
            if ($username === $subscriber['client']->getUser()->getEmail() &&
                $currentSessionId !== $subscriber['connection']->WAMP->sessionId
            ) {
                $eligible[] = $subscriber['connection']->WAMP->sessionId;
            }
        }
        return $eligible;
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName(): string
    {
        return 'info.topic';
    }
}