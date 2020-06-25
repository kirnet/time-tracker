<?php

namespace App\Events;

/**
 * Class Events
 * @package App
 */
final class Events
{
    /**
     * For the event naming conventions, see:
     * https://symfony.com/doc/current/components/event_dispatcher.html#naming-conventions.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    public const PERIOD_CREATED = 'period.created';
}
