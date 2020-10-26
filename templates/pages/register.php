<?php

use lib\components\AppInfo;

/**
 * @var \lib\service\RegistrationFormValidation validation
 * @var string|null $registrationErr
 */

?>

<section class="section">
    <form id="register-form" method="post">
        <h2 class="is-size-3">Create a new account</h2>

        <?php if (empty($registrationErr)): ?>

            <div class="field">
                <?php $errorClass = $validator->getUsernameErr() ? 'is-danger' : ''; ?>

                <label class="is-sr-only" for="field-username">Username</label>

                <div class="control has-icons-left">
                    <input class="input <?= $errorClass ?>" type="text" name="username" required
                           placeholder="Username" id="field-username"
                           autofocus
                           value="<?php echo htmlspecialchars($validator->getUsername());?>" />
                    <span class="icon is-small is-left">
                        <svg focusable="false" class="svg-inline--fa fa-user fa-w-14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
                    </span>
                </div>
                <?php if ($validator->getUsernameErr()): ?>
                <p class="help is-danger"><?= htmlspecialchars($validator->getUsernameErr()) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <?php $errorClass = $validator->getEmailErr() ? 'is-danger' : ''; ?>

                <label class="is-sr-only" for="field-email">E-Mail</label>

                <div class="control has-icons-left">
                    <input class="input <?= $errorClass ?>" type="email" name="email" id="field-email" required
                           placeholder="Email"
                           value="<?php echo htmlspecialchars($validator->getEmail());?>" />
                    <span class="icon is-small is-left">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="envelope" class="svg-inline--fa fa-envelope fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
                    </span>
                </div>
                <?php if ($validator->getEmailErr()): ?>
                    <p class="help is-danger"><?= htmlspecialchars($validator->getEmailErr()) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <?php $errorClass = $validator->getPassErr() || $validator->getPassRptErr() ? 'is-danger' : ''; ?>

                <label class="is-sr-only" for="field-password">Password</label>
                <div class="control has-icons-left">
                    <input class="input <?= $errorClass ?>" type="password" name="password" required
                           id="field-password"
                           minlength="10"
                           placeholder="Password (minimum 10 characters)" />
                    <span class="icon is-small is-left">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" class="svg-inline--fa fa-key fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z"></path></svg>
                    </span>
                </div>
                <?php if ($validator->getPassErr()): ?>
                <p class="help is-danger">
                    <?= htmlspecialchars($validator->getPassErr()); ?>
                    <br><br>
                    Hint: A good way to create a long complex
                    password that is memorable is to select 2 to 3 words, and
                    separate them using different symbols. Alternatively, use a password manager.
                </p>
                <?php endif; ?>
            </div>

            <div class="field">
                <?php $errorClass = $validator->getPassRptErr() ? 'is-danger' : ''; ?>
                <label class="is-sr-only" for="field-password-rpt">Repeat password</label>
                <div class="control has-icons-left">
                    <input class="input <?= $errorClass ?>" type="password" name="password-repeat" required
                           id="field-password-rpt"
                           placeholder="Repeat password" />
                    <span class="icon is-small is-left">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" class="svg-inline--fa fa-key fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z"></path></svg>
                    </span>
                </div>
                <?php if ($validator->getPassRptErr()): ?>
                    <p class="help is-danger"><?= htmlspecialchars($validator->getPassRptErr());?></p>
                <?php endif; ?>
            </div>


            <div class="field">
                <div class="control has-icons-left">
                    <button type="submit" name="register-submit" class="button is-fullwidth is-link">
                        Register
                    </button>
                    <span class="icon">
                        <svg focusable="false" class="svg-inline--fa fa-user-plus fa-w-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M624 208h-64v-64c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v64h-64c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h64v64c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-64h64c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm-400 48c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
                    </span>
                </div>
            </div>

        <?php else: ?>
            <?php /* failed to register user, because of technical error, not invalid data */ ?>

            <div class="field">
            <span id="registration-error">
                <?php echo htmlspecialchars($registrationErr);?>
            </span>
            </div>

        <?php endif; ?>
    </form>
</section>


<div id="main-links">
    <p id="link-home">
        <a href="<?= AppInfo::urlStartPage() ?>">
            <span class="icon button-icon">
                <svg focusable="false" class="svg-inline--fa fa-home fa-w-18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"></path></svg>
            </span> Back to Home
        </a>
    </p>
    <p id="login">
        <a href="<?= AppInfo::urlLoginPage() ?>">
            <span class="icon button-icon">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign-in-alt" class="svg-inline--fa fa-sign-in-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z"></path></svg>
            </span> Log in
        </a>
    </p>
</div>
