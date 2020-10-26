<?php


namespace tests\unit\lib;


use lib\utils\AbstractClock;
use lib\utils\Clock;

/**
 * A clock test double, which can be configured to return a fixed time.
 *
 * @package tests\unit\lib
 */
class ClockStub extends AbstractClock implements Clock
{

    private $timestamp;

    public function __construct(int $timestamp)
    {
        parent::__construct();
        $this->timestamp = $timestamp;
    }

    function getCurrentTimestamp(): int
    {
        return $this->timestamp;
    }
}