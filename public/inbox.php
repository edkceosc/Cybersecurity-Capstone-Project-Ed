<?php

include __DIR__ . '/../setup.php';

use lib\components\Alertbox;
use lib\db\Connection;
use lib\db\Messages;
use lib\service\SessionManagerPhp;
use lib\service\SymmetricEncryption;

$sessMgr = new SessionManagerPhp();
$user = $sessMgr->getAuthenticatedUser();
if (!$user) {
    header('Location: /login.php?message=login-required', true, 303);
    exit();
}

try {
    $encryption = SymmetricEncryption::fromEnvironment();
    $message = new Messages(Connection::get_db_pdo(), $encryption);
    $boxMessages = $message->loadMessageViewsByRecipient($user->getId());
} catch (Exception $e) {
    echo 'Internal Server Error';
    http_response_code(500);
}

// this variables are required by included templates...
$pageTitle = 'Inbox';
$isInbox = true;

ob_start();

if (isset($_GET['message'])) {
    $isErr = false;
    $msg = '';
    switch ($_GET['message']) {
        case 'already-authenticated':
            $msg = "You are already authenticated.";
            break;
        case 'login-successful':
            $msg = 'Logged in successfully';
            break;
        case 'marked-as-read' :
            $msg = 'Successfully marked message as "read"';
            break;
        case 'failed-to-mark-as-read':
            $isErr = true;
            $msg = 'Failed to mark message as "read"';
            break;
    }
    if (!empty($msg)) {
        // note: the $msg is html-encoded in alert-message.php, no need for `htmlspecialchars` here.
        echo '<section class="section">' . ($isErr ? Alertbox::renderError($msg) : Alertbox::renderInfo($msg)) . '</section>';
    }
}

include TEMPLATE_DIR . '/pages/message-box.php';
$htmlContent = ob_get_clean();

include TEMPLATE_DIR . '/page.php';
