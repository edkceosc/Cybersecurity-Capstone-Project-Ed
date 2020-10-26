<?php
/**
 * @var string|null $pageTitle
 * @var string      $htmlContent
 */

use lib\components\AppInfo;
use lib\components\Banner;
use lib\service\SessionManagerPhp;

$sessMgr = new SessionManagerPhp();
$user = $sessMgr->getAuthenticatedUser();
?>

<body>
<div class="page">

    <?= Banner::renderAppBanner($pageTitle); ?>

    <?php if ($user): ?>
        <section class="section">
            <nav class="level">
                <div class="level-left">
                    <p class="level-item">
                        <a class="button" href="<?= AppInfo::urlInbox() ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="inbox" class="svg-inline--fa fa-inbox fa-w-18 button-icon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M567.938 243.908L462.25 85.374A48.003 48.003 0 0 0 422.311 64H153.689a48 48 0 0 0-39.938 21.374L8.062 243.908A47.994 47.994 0 0 0 0 270.533V400c0 26.51 21.49 48 48 48h480c26.51 0 48-21.49 48-48V270.533a47.994 47.994 0 0 0-8.062-26.625zM162.252 128h251.497l85.333 128H376l-32 64H232l-32-64H76.918l85.334-128z"></path></svg> Inbox
                        </a>
                    </p>
                    <p class="level-item">
                        <a class="button" href="<?= AppInfo::urlSentMessages() ?>">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="mail-bulk" class="svg-inline--fa fa-mail-bulk fa-w-18 button-icon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M160 448c-25.6 0-51.2-22.4-64-32-64-44.8-83.2-60.8-96-70.4V480c0 17.67 14.33 32 32 32h256c17.67 0 32-14.33 32-32V345.6c-12.8 9.6-32 25.6-96 70.4-12.8 9.6-38.4 32-64 32zm128-192H32c-17.67 0-32 14.33-32 32v16c25.6 19.2 22.4 19.2 115.2 86.4 9.6 6.4 28.8 25.6 44.8 25.6s35.2-19.2 44.8-22.4c92.8-67.2 89.6-67.2 115.2-86.4V288c0-17.67-14.33-32-32-32zm256-96H224c-17.67 0-32 14.33-32 32v32h96c33.21 0 60.59 25.42 63.71 57.82l.29-.22V416h192c17.67 0 32-14.33 32-32V192c0-17.67-14.33-32-32-32zm-32 128h-64v-64h64v64zm-352-96c0-35.29 28.71-64 64-64h224V32c0-17.67-14.33-32-32-32H96C78.33 0 64 14.33 64 32v192h96v-32z"></path></svg> Sent messages
                        </a>
                    </p>
                </div>
                <div class="level-right">
                    <form class="level-item nav-logout" method="post" action="<?= AppInfo::urlLogout() ?>">
                        <button type="submit" class="button is-dark">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign-out-alt" class="svg-inline--fa fa-sign-out-alt fa-w-16 button-icon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M497 273L329 441c-15 15-41 4.5-41-17v-96H152c-13.3 0-24-10.7-24-24v-96c0-13.3 10.7-24 24-24h136V88c0-21.4 25.9-32 41-17l168 168c9.3 9.4 9.3 24.6 0 34zM192 436v-40c0-6.6-5.4-12-12-12H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h84c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12H96c-53 0-96 43-96 96v192c0 53 43 96 96 96h84c6.6 0 12-5.4 12-12z"></path></svg> Logout
                        </button>
                    </form>
                    <p class="level-item">
                        <span class="icon">
                            <svg focusable="false" class="svg-inline--fa fa-user fa-w-14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
                        </span>
                        <span class="nav-username"><?= $user->getUsername() ?></span>
                    </p>
                </div>
            </nav>
            <nav class="level">
                <div class="level-left">
                    <p class="level-item">
                        <a class="button" href="<?= AppInfo::urlWriteMessage() ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen" class="svg-inline--fa fa-pen fa-w-16 button-icon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z"></path></svg> New message
                        </a>
                    </p>
                </div>
                <div class="level-right">&nbsp;</div>
            </nav>
        </section>
    <?php endif; ?>


    <div class="container">
        <?= $htmlContent ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

</body>
