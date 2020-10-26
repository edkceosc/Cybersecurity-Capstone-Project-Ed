<?php

use lib\components\AppInfo;
use lib\service\SessionManagerPhp;

include '../setup.php';

$sessMgr = new SessionManagerPhp();
if ($sessMgr->getAuthenticatedUser()) {
    $sessMgr->logout();
}

header('Location: ' . AppInfo::urlStartPage() . '?message=logout-successful', true, 303);
