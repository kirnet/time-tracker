<?php

namespace App\Utils;

/**
 * Class ProjectHelper
 * @package App\Utils
 */
class ProjectHelper
{
    /**
     * Count totlal time from all timers
     * @param $projects
     *
     * @return mixed
     */
    public static function countTime($projects)
    {
        foreach ($projects as $project) {
            $counter = 0;
            foreach ($project->getTimers() as $timer) {
                $counter += $timer->getTime();
            }
            $project->time = $counter;
        }

        return $projects;
    }
}