<?php

use lib\components\AppInfo;
use lib\db\Connection;
use lib\db\Users;
use lib\service\IpUtils;
use lib\service\Logger;
use lib\service\LoginService;
use lib\service\SessionManagerPhp;

include __DIR__ . '/../setup.php';

$sessMgr = new SessionManagerPhp();
if ($sessMgr->getAuthenticatedUser()) {
    // redirect to inbox
    header('Location: /inbox.php?message=already-authenticated', true, 303);
    return;
}

if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    // handle form submission
    try {
        $pdo = Connection::get_db_pdo();
        $users = new Users($pdo);
        $loginService = new LoginService($users);
        $result = $loginService->tryLogin($_POST['username'], $_POST['password'], IpUtils::getIp());
        if ($result->isSuccessful()) {
            header('Location: /inbox.php?message=login-successful', true, 303);
        } else {
            if ($result->isLocked()) {
                header('Location: ' . AppInfo::urlLoginPage() . '?message=account-locked', true, 303);
            } elseif ($result->isWrongCredentialsProvided()) {
                header('Location: ' . AppInfo::urlLoginPage() . '?message=credentials-invalid', true, 303);
            }
        }
    } catch (Exception $e) {
        Logger::getInstance()->logMessage(Logger::LEVEL_ERROR, 'technical error during login', $e);
        echo 'Internal server error';
        http_response_code(500);
    }
} else {
    // GET: render page
    $pageTitle = 'Login';

    ob_start();
    include TEMPLATE_DIR . '/pages/login.php';
    $htmlContent = ob_get_clean();

    // then render the page.
    include TEMPLATE_DIR . '/page.php';
}


