<?php

namespace App\Helpers;

/**
 * Class DatesHelper
 * @package App\Helpers
 */
class DatesHelper
{
    /**
     * Return diff dates in seconds
     *
     * @param $dateStart
     * @param $dateFinish
     *
     * @return int
     */
    public static function getDatesDiff($dateStart, $dateFinish): int
    {
        return abs($dateStart->getTimestamp() - $dateFinish->getTimestamp());
    }
}