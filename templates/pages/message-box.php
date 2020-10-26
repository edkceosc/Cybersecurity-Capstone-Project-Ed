<?php

/**
 * @var \lib\model\MessageView[] $boxMessages
 * @var string                   $pageTitle
 * @var bool|null                $isInbox (this is used in 'messages_list.php' and 'single-message.php')
 *
 */
?>

<div id="header" class="is-size-1">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
</div>


<div>
    <?php include BASE_DIR . '/templates/messages-list.php'; ?>
</div>
