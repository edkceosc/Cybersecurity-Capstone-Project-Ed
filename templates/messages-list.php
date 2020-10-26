<?php

/**
 * @var \lib\model\MessageView[] $boxMessages;
 * @var bool|null                $isInbox (this is used in 'single-message.php')
 *
 */

use lib\components\Alertbox;

?>

<div>
    <?php if (empty($boxMessages)): ?>
        <?= Alertbox::renderInfo('There are no messages in this box...'); ?>
    <?php else: ?>
        <?php
        foreach ($boxMessages as $message) {
            include TEMPLATE_DIR . '/single-message.php';
        }
        ?>
    <?php endif; ?>
</div>
