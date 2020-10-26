<?php

namespace lib\utils;

use DateTime;
use DateTimeZone;

abstract class AbstractClock implements Clock
{
    /**
     * @var DateTimeZone
     */
    protected $tzUTC;

    /**
     * ClockImpl constructor.
     */
    protected function __construct()
    {
        $this->tzUTC = new DateTimeZone('UTC');
    }

    public function getCurrentDateTimeUTC(): DateTime
    {
        $dt = new DateTime();
        $dt->setTimestamp($this->getCurrentTimestamp());
        $dt->setTimezone($this->tzUTC);
        return $dt;
    }

    public function isCurrentTimeLaterThan(DateTime $otherTime): bool
    {
        $now = $this->getCurrentTimestamp();
        return $now > $otherTime->getTimestamp();
    }

    public function isCurrentTimeEarlierThan(DateTime $otherTime): bool
    {
        $now = $this->getCurrentTimestamp();
        return $now < $otherTime;
    }

    public function diffSecondsToCurrentTime(DateTime $otherTime): int
    {
        $now = $this->getCurrentTimestamp();
        return $otherTime->getTimestamp() - $now;
    }
}