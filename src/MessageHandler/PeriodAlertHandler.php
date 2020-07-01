<?php

namespace App\MessageHandler;

use App\Message\PeriodAlertQueue;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;

class PeriodAlertHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(PeriodAlertQueue $arg)
    {
        $this->logger->info('info alert');
        $message = sprintf('%s %s %s', $arg->getName(),  $arg->getContent(), $arg->getEmail());
        file_put_contents(
            '/home/kirnet/tmp/1/ttracker.txt',
            $message . PHP_EOL,
            FILE_APPEND
        );
    }
}