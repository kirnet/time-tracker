<?php

namespace App\MessageHandler;

use App\Message\PeriodAlertQueue;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;

class PeriodAlertHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    private ParameterBagInterface $params;

    /**
     * PeriodAlertHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $params
     */
    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->params = $params;
    }

    /**
     * @param PeriodAlertQueue $arg
     *
     * @throws TransportExceptionInterface
     */
    public function __invoke(PeriodAlertQueue $arg)
    {
        $email = new Email();
        $email
            ->from($this->params->get('fromEmail'))
            ->to($arg->getEmail())
            ->text($arg->getContent())
        ;
        $transport = Transport::fromDsn($this->params->get('MAILER_DSN'));
        $mailer = new Mailer($transport);
        try {
            $mailer->send($email);
        } catch(Exception $e) {
            $this->logger->error($e->getMessage());
        }
        $message = sprintf('%s %s %s', $arg->getName(), $arg->getContent(), $arg->getEmail());
        $this->logger->info('info alert: ' . $message);
    }
}