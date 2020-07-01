<?php

namespace App\Message;

use App\Entity\Period;
use Doctrine\ORM\Mapping\Entity;

class PeriodAlertQueue
{
    private string $content;
    private string $email;
    private string $name;

    public function __construct(Period $period)
    {
        $this->content = $period->getAlertTime()->format('d-m-Y H:i:s');
        $this->email = $period->getUser()->getEmail();
        $this->name = $period->getName();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}