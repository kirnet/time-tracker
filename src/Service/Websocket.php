<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class Websocket implements RpcInterface
{
    /** @var LoggerInterface  */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adds the params together
     *
     * Note: $conn isnt used here, but contains the connection of the person making this request.
     *
     * @param ConnectionInterface $connection
     * @param WampRequest $request
     * @param array $params
     *
     * @return array
     */
    public function addFunc(ConnectionInterface $connection, WampRequest $request, $params)
    {
        $this->logger->info('fffffffffffff');
        return ["result" => array_sum($params)];
    }

    /**
     * Name of RPC, use for pubsub router (see step3)
     *
     * @return string
     */
    public function getName()
    {
        return 'acme.rpc';
    }
}