<?php

namespace App\EventListener;

use App\Entity\Project;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class UserListener
 * @package App\EventListener
 */
class UserListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $user              = $args->getObject();
        $projectRepository = $args->getObjectManager()->getRepository(Project::class);
        $projectRepository->removeByOwnerId($user->getId());
    }
}