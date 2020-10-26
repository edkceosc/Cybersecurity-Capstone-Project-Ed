<?php


namespace lib\components;


class Alertbox
{

    public static function renderError(string $body, ?string $title = null): string
    {
        return self::renderAlertMessage($body, $title, 'is-danger');
    }

    public static function renderSuccess(string $body, ?string $title = null): string
    {
        return self::renderAlertMessage($body, $title, 'is-success');
    }

    public static function renderInfo(string $body, ?string $title = null): string
    {
        return self::renderAlertMessage($body, $title, 'is-info');
    }

    private static function renderAlertMessage(string $body, ?string $title, string $colorModifier): string
    {
        ob_start();
        include TEMPLATE_DIR . '/alert-message.php';
        return ob_get_clean();
    }
}
