<?php

namespace App\MessageHandler;

use App\Message\PeriodAlertQueue;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PeriodAlertHandler implements MessageHandlerInterface
{
    public function __invoke(PeriodAlertQueue $arg)
    {
        file_put_contents('/home/kirnet/tmp/1/ttracker.txt', json_encode($arg));
    }
}