<?php
/**
 * @var \lib\service\MessageFormValidation $validation
 */
?>


<form method="post">
    <div id="header" class="is-size-1">
        <h1>New message</h1>
    </div>

    <div class="field">
        <label class="label" for="field-recipient">Recipient</label>
        <div class="control">
            <input id="field-recipient"
                   name="recipient" class="input"
                   type="text"
                   autofocus
                   value="<?= htmlspecialchars($validation->getRecipientName()) ?>"
                   placeholder="The username of the recipient">
        </div>
        <?php if ($validation->getRecipientErr()): ?>
            <p class="help is-danger"><?= htmlspecialchars($validation->getRecipientErr()) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label class="label" for="field-title">Title / Subject</label>
        <div class="control">
            <input type="text" name="title" id="field-title" class="input"
                   required maxlength="255"
                   value="<?= htmlspecialchars($validation->getTitle()) ?>">
        </div>
    </div>

    <div class="field">
        <label class="label" for="message-body">Message</label>
        <div class="control">
            <textarea id="message-body" name="message-body" class="textarea" rows="9" required>
                <?= htmlspecialchars($validation->getMsgBody()) ?>
            </textarea>
        </div>
    </div>

    <div class="field">
        <div class="control has-icons-left">
            <button type="submit" name="submit" class="button is-fullwidth is-link">
                Submit
            </button>
            <span class="icon">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="paper-plane" class="svg-inline--fa fa-paper-plane fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"></path></svg>
            </span>
        </div>
    </div>
</form>
