<?php
/**
 * @var \lib\model\MessageView $message
 * @var bool|null $isInbox
 */

use lib\components\AppInfo;

$isInbox = isset($isInbox) && $isInbox;
?>

<article class="message is-dark <?= $isInbox && $message->getMessage()->isRead() ? 'message-is-read' : '' ?>">
    <div class="message-header">
        <p><?= htmlspecialchars($message->getDecryptedTitle()) ?></p>
        <p class="is-pulled-right"><?= htmlspecialchars($message->getMessage()->getMessageDate()) ?> UTC</p>
    </div>
    <div class="message-body">
        <?php if ($isInbox && !$message->getMessage()->isRead()): ?>
            <div class="is-pulled-right">
                <form method="post" action="<?= AppInfo::urlSetMessageAsRead() ?>">
                    <input type="hidden" name="msg-id" value="<?= $message->getMessage()->getId() ?>">
                    <button type="submit" class="button">Mark message as read</button>
                </form>
            </div>
        <?php endif; ?>
        <i><?= htmlspecialchars($message->getSenderName()) ?></i> to <i><?= htmlspecialchars($message->getRecipientName()) ?></i>
        <hr>
        <p>
            <?= nl2br(htmlspecialchars($message->getDecryptedMessageBody())) ?>
        </p>
    </div>
</article>
