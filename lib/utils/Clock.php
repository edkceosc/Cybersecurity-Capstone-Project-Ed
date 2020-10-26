<?php


namespace lib\utils;

use DateTime;

interface Clock
{

    function getCurrentTimestamp(): int;

    /**
     * Returns an instance of DateTime with current timestampe, and timezone set to UTC.
     *
     * @return DateTime
     */
    function getCurrentDateTimeUTC(): DateTime;

    /**
     * Returns true if and only if the current time is after the $otherTime.
     *
     * @param DateTime $otherTime
     *
     * @return bool
     */
    function isCurrentTimeLaterThan(DateTime $otherTime): bool;

    /**
     * Returns true if and only if the current time is before the $otherTime.
     *
     * @param DateTime $otherTime
     *
     * @return bool
     */
    function isCurrentTimeEarlierThan(DateTime $otherTime): bool;

    /**
     * Returns the number of seconds between the current time and the $otherTime.
     *
     * The return value is positive if $otherTime is in the future, and negative if in the past.
     *
     * @param DateTime $otherTime
     *
     * @return int
     */
    function diffSecondsToCurrentTime(DateTime $otherTime): int;
}