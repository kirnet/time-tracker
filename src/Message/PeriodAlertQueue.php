<?php

namespace App\Message;

use App\Entity\Period;
use Doctrine\ORM\Mapping\Entity;

class PeriodAlertQueue
{
    private string $content;

    public function __construct(Period $period)
    {
        $this->content = $period->getAlertTime()->format('d-m-Y H:i:s');
    }

    public function getContent(): string
    {
        return $this->content;
    }
}