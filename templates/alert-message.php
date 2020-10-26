<?php

/**
 * @var string|null $title
 * @var string      $body
 * @var string      $colorModifier
 */
?>

<article class="message <?= htmlspecialchars($colorModifier); ?>">
    <?php if ($title): ?>
        <div class="message-header">
            <p><?= htmlspecialchars($title); ?></p>
        </div>
    <?php endif; ?>
    <div class="message-body">
        <?= htmlspecialchars($body); ?>
    </div>
</article>
