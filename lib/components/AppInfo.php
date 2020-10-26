<?php


namespace lib\components;


class AppInfo
{

    const APP_NAME = 'Chat no. 8';

    private static $baseUrl = '/';

    public static function getBaseUrl(): string
    {
        return self::$baseUrl;
    }

    public static function urlCss(string $filename): string
    {
        return self::$baseUrl . 'css/' . $filename;
    }

    public static function urlStartPage(): string
    {
        return self::$baseUrl . 'index.php';
    }

    public static function urlLoginPage(): string
    {
        return self::$baseUrl . 'login.php';
    }

    public static function urlLogout(): string
    {
        return self::$baseUrl . 'logout.php';
    }

    public static function urlRegisterPage(): string
    {
        return self::$baseUrl . 'register.php';
    }

    public static function urlInbox(): string
    {
        return self::$baseUrl . 'inbox.php';
    }

    public static function urlSentMessages(): string
    {
        return self::$baseUrl . 'sent-messages.php';
    }

    public static function urlWriteMessage(): string
    {
        return self::$baseUrl . 'write-message.php';
    }

    public static function urlSetMessageAsRead(): string
    {
        return self::$baseUrl . 'set-message-read.php';
    }
}