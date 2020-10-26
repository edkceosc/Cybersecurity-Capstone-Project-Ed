<?php

use lib\service\SessionManagerPhp;

include __DIR__ . '/../setup.php';

$sessMgr = new SessionManagerPhp();
if ($sessMgr->getAuthenticatedUser()) {
    // redirect to inbox
    header('Location: /inbox.php?message=already-authenticated', true, 303);
    return;
}

$pageTitle = 'Coursera Capstone Project Messaging System';

// store pages/index.php as $htmlContent
ob_start();
include TEMPLATE_DIR . '/pages/index.php';
$htmlContent = ob_get_clean();

// then render the page.
include TEMPLATE_DIR . '/page.php';
