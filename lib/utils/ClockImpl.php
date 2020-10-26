<?php


namespace lib\utils;


/**
 * The default implementation that simply uses `time()` to get the current timestamp.
 */
class ClockImpl extends AbstractClock implements Clock
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getCurrentTimestamp(): int
    {
        return time();
    }
}