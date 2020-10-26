<?php


namespace lib\service;


class IpUtils
{

    /**
     * Returns the user's ip address. Returns null if the ip could not be detected.
     */
    public static function getIp(): ?string
    {
        // HTTP_X_FORWARDED_FOR is the IP of the user, which was forwarded by the heroku proxy server.
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = reset($ipList);
            $ip = trim($ip);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }
        if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
            Logger::getInstance()->logMessage(Logger::LEVEL_DEBUG, 'remote IP: ' . $ip);
            return $ip;
        }
        Logger::getInstance()->logMessage(Logger::LEVEL_WARN, 'could not detect remote IP');
        return null;
    }
}