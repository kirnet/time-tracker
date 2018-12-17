<?php

namespace App\Service\Websocket;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class InfoTopic implements TopicInterface
{
    /** @var LoggerInterface  */
    private $logger;

    /**
     * @var ClientManipulatorInterface
     */
    protected $clientManipulator;

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
        $userConnection = $this->clientManipulator->findByUsername($topic, $username);

        //$this->logger->info('socket user' . json_encode($user));
        //this will broadcast the message to ALL subscribers of this topic.
        if ($userConnection !== false && $username == $userConnection['client']->getEmail()) {
            $topic->broadcast(
                ['msg' => $connection->resourceId . "{$username} has joined " . $topic->getId()],
                [],
                [$userConnection['connection']->WAMP->sessionId]
            );
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
        /*
            $topic->getId() will contain the FULL requested uri, so you can proceed based on that

            if ($topic->getId() == "acme/channel/shout")
               //shout something to all subs.
        */
        $username = $this->clientManipulator->getClient($connection)->getUsername();
        $subscribers = $this->clientManipulator->getAll($topic);
        $eligible = [];
        foreach ($subscribers as $subscriber) {
            if ($username === $subscriber['client']->getEmail()) {
                $eligible[] = $subscriber['connection']->WAMP->sessionId;
            }
        }

        $topic->broadcast(
            [
                'msg' => $event
            ],
            [],
            $eligible
        );
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'info.topic';
    }
}