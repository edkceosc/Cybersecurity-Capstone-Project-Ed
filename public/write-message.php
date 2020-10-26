<?php

use lib\components\Alertbox;
use lib\components\AppInfo;
use lib\db\Connection;
use lib\db\DbUtils;
use lib\db\Messages;
use lib\db\Users;
use lib\service\Logger;
use lib\service\MessageFormValidation;
use lib\service\SessionManagerPhp;
use lib\service\SymmetricEncryption;
use lib\utils\ClockImpl;

include __DIR__ . '/../setup.php';

$sessMgr = new SessionManagerPhp();
$sender = $sessMgr->getAuthenticatedUser();
if (!$sender) {
    header('Location: /login.php?message=login-required', true, 303);
    exit();
}

$logger = Logger::getInstance();

try {
    $encryption = SymmetricEncryption::fromEnvironment();
    $pdo = Connection::get_db_pdo();
    $messages = new Messages($pdo, $encryption);
    $users = new Users($pdo);
    $clock = new ClockImpl();
    $validation = new MessageFormValidation($users);
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
        $validation->validateInput($_POST);
        if ($validation->isValid()) {
            $ok = $messages->insertNewMessage(
                $sender->getId(),
                $validation->getRecipientId(),
                $encryption->encrypt($validation->getTitle()),
                $encryption->encrypt($validation->getMsgBody()),
                $clock->getCurrentDateTimeUTC()->format(DbUtils::SQL_DATE_TIME_FORMAT),
                false
            );
            if ($ok) {
                // redirect to sent messages
                header('Location: ' . AppInfo::urlSentMessages() . '?message=sending-successful', true, 303);
                exit();
            } else {
                // redirect to message form, with error message
                header('Location: ' . AppInfo::urlWriteMessage() . '?message=sending-failed', true, 303);
                exit();
            }
        }
    }
} catch (Exception $e) {
    echo 'Internal Server Error';
    Logger::getInstance()->logMessage(Logger::LEVEL_ERROR, 'error saving message', $e);
    http_response_code(500);
}

// Either handling a GET request, or validation has failed.

ob_start();

if (isset($_GET['message'])) {
    $msg = '';
    switch ($_GET['message']) {
        case 'sending-failed':
            $msg = "Sending the message has failed.";
            break;
    }
    if (!empty($msg)) {
        echo '<section class="section">' . Alertbox::renderError($msg) . '</section>';
    }
}

include TEMPLATE_DIR . '/pages/write-message.php';
$htmlContent = ob_get_clean();

$pageTitle = 'New message';

include TEMPLATE_DIR . '/page.php';

