<?php

/**
 * This file should be included by every php script. It sets up the composer autoloader,
 * allowing us to use classes via namespace and classname (without extra include / include_once).
 */

include __DIR__ . '/vendor/autoload.php';

define('TEMPLATE_DIR', __DIR__ . '/templates');

define('BASE_DIR', __DIR__);

// use a function, to not pollute global namespace with variable names...
function appSetup()
{
    $options = [
        'cookie_httponly' => true,
        'use_strict_mode' => true,
        'use_cookies' => true,
        'use_only_cookies' => true,
        'cookie_lifetime' => 0, // ends ends with browser session...
        'cookie_samesite' => 'Lax',
    ];
    // see https://www.php.net/manual/en/reserved.variables.server.php for how this variable works
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || strval($_SERVER['SERVER_PORT']) === '443'
        || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
        || strval($_SERVER['HTTP_X_FORWARDED_PORT']) === '443'
    ) {
        $options['cookie_secure'] = true;
    }
    $ok = session_start($options);
    if (!$ok) {
        if (isset($_SERVER['SERVER_PROTOCOL']) && substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, 4) === 'http') {
            echo '<h6>Internal Server Error</h6>';
            http_response_code(500);
            exit();
        } else {
            // maybe a command line process?
            exit(1);
        }
    }
}

appSetup();
