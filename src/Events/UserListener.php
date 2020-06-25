<?php

namespace App\Events;

use App\Entity\Project;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

/**
 * Class UserListener
 * @package App\EventListener
 */
class UserListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $obj = $args->getObject();
        if (get_class($obj) === 'App\Entity\User') {
            $projectRepository = $args->getObjectManager()->getRepository(Project::class);
            $projectRepository->removeByOwnerId($obj->getId());
            $this->logger->info('Delete user: ' . $obj->getEmail());
        }
    }
}