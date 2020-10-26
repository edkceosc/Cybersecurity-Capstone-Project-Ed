<?php


namespace lib\service;


use Throwable;

class Logger
{

    private static $INSTANCE;

    const LEVEL_ERROR = 'ERROR';
    const LEVEL_WARN = 'WARN';
    const LEVEL_INFO = 'INFO';
    const LEVEL_DEBUG = 'DEBUG';

    /**
     * Not allowed to create custom instances (singleton, use getInstance instead).
     */
    private function __construct()
    {
    }


    public static function getInstance()
    {
        if (!isset(self::$INSTANCE)) {
            self::$INSTANCE = new Logger();
        }
        return self::$INSTANCE;
    }

    public function logMessage(string $level, string $message, ?Throwable $exception = null)
    {
        if ($exception) {
            error_log("$level: $message\n$exception");
        } else {
            error_log("$level: $message");
        }
    }

}