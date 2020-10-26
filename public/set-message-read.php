<?php

use lib\components\AppInfo;
use lib\db\Connection;
use lib\db\Messages;
use lib\service\SessionManagerPhp;
use lib\service\SymmetricEncryption;

/**
 * This page allows a recipient to set a message as 'read'.
 * Afterwards, the user is redirected to inbox
 * (since this is the only useful page from which a message
 * can be marked as read)
 *
 * Expects a POST request, with params 'msg-id' (the message id to set as marked)
 */

include '../setup.php';

// only accept POST's
if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
    http_response_code(405);
    exit();
}

if (!isset($_POST['msg-id'])) {
    http_response_code(400);
    exit();
}

$msgIdStr = $_POST['msg-id'];
$msgId = intval($msgIdStr);
if (strval($msgId) !== $msgIdStr) {
    http_response_code(400);
    exit();
}

// user (recipient) must be authenticated.
$sessMgr = new SessionManagerPhp();
$recipient = $sessMgr->getAuthenticatedUser();
if (!$recipient) {
    http_response_code(403);
    exit();
}

try {
    $messages = new Messages(Connection::get_db_pdo(), SymmetricEncryption::fromEnvironment());
    $ok = $messages->markAsRead($msgId, $recipient->getId());
    $url = AppInfo::urlInbox();
    $locationHdr = 'Location: ' . $url . '?message=' . ($ok ? 'marked-as-read' : 'failed-to-mark-as-read');
    header($locationHdr, true, 303);
} catch (Exception $ignore) {
    echo 'Internal Server error';
    http_response_code(500);
}

