<?php
/**
 * Params:
 *
 * @var string|null $pageTitle
 */

use lib\components\AppInfo;

?>

<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <meta name=viewport content="width=device-width, initial-scale=1"/>

    <meta name=author content="Copyright to whoever created. Empowered by Ed">

    <title><?= (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '') . htmlspecialchars(AppInfo::APP_NAME) ?></title>

    <link rel="stylesheet" media="all" href="<?= AppInfo::urlCss('bulma.css') ?>">
    <link rel="stylesheet" media="all" href="<?= AppInfo::urlCss('index.css') ?>">
    <link rel="stylesheet" href="<?= AppInfo::urlCss('fa-svg.css') ?>">
</head>
