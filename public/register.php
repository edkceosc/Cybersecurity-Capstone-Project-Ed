<?php

include __DIR__ . '/../setup.php';

use lib\db\Connection;
use lib\db\Users;
use lib\service\RegistrationFormValidation;
use lib\service\SessionManagerPhp;

$sessMgr = new SessionManagerPhp();
if ($sessMgr->getAuthenticatedUser()) {
    // redirect to inbox
    header('Location: /inbox.php?message=already-authenticated', true, 303);
    return;
}

try {
    $pdo = Connection::get_db_pdo();
} catch (Exception $e) {
    echo htmlspecialchars('Failed to initialize the database connection');
    http_response_code(500);
    exit();
}
$users = new Users($pdo);
$validator = new RegistrationFormValidation($users);
$registrationErr = "";

$pageTitle = 'Register';

// Register user on submission of the form.
if (isset($_POST['register-submit'])) {
    if ($validator->validateValues(
        $_POST["username"], $_POST["email"], $_POST["password"], $_POST["password-repeat"])
    ) {
        $ok = $users->registerNewUser($_POST["username"], $_POST["email"], $_POST["password"]);
        if ($ok) {
            header("Location: /login.php?message=registration-successful", true, 303);
            exit();
        } else {
            $registrationErr = "Registration failed due to a server error. Please try again later.";
        }
    }
    // else: invalid input: render form...
}

ob_start();
include TEMPLATE_DIR . '/pages/register.php';
$htmlContent = ob_get_clean();

include TEMPLATE_DIR . '/page.php';
