<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PeriodSubscriber implements EventSubscriberInterface
{
    public function onPeriodCreated($event)
    {
        dd($event);
    }

    public static function getSubscribedEvents()
    {
        return [
            'period.created' => 'onPeriodCreated',
        ];
    }

}
