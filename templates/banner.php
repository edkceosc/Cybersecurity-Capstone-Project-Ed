<?php
/**
 * @var string      $title
 * @var string|null $subtitle
 */
?>
<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <?= htmlspecialchars($title) ?>
            </h1>
            <?php if (isset($subtitle)): ?>
                <h2 class="subtitle">
                    <?= htmlspecialchars($subtitle) ?>
                </h2>
            <?php endif; ?>
        </div>
    </div>
</section>
