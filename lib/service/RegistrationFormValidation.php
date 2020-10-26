<?php

namespace lib\service;

use lib\db\Users;

class RegistrationFormValidation
{

    /**
     * @var Users
     */
    private $dbUsers;

    /**
     * @var bool
     */
    private $values_ok = true;

    /**
     * The username and email will be remembered if validation fails.
     */
    private $username = "";
    private $email = "";

    /**
     * The error messages to show for each field if validation fails.
     */
    private $usernameErr = "";
    private $emailErr = "";
    private $passErr = "";
    private $passRptErr = "";

    public function __construct(Users $dbUsers)
    {
        $this->dbUsers = $dbUsers;
    }

    /**
     * Set the value of the given variable and ensure values_ok is false.
     *
     * @param string $var   The variable to set (passed by reference)
     * @param string $value The value to set
     */
    private function setError(&$var, string $value) {
        $var = $value;
        $this->values_ok = false;
    }

    /**
     * Ensure that all mandatory form values have been entered and are valid.
     *
     * @param string $username          The username entered by the user
     * @param string $email             The email address entered by the user
     * @param string $password          The password entered by the user
     * @param string $password_repeat   The repeated password entered by the user
     *
     * @return boolean Whether all values are ok.
     */
    public function validateValues(
        string $username,
        string $email,
        string $password,
        string $password_repeat
    ): bool {
        if (empty($username)) {
            // Username is empty
            $this->setError($this->usernameErr, "Username is required");
        } else {
            // Save username to keep the value in the form
            $this->username = $username;

            if(preg_match("/[^[:alnum:]\-_]/", $username)) {
                // Username contains invalid characters
                $this->setError($this->usernameErr,
                               "Username must contain only: capital letters (A-Z), "
                               . "lowercase letters (a-z), numbers (0-9), underscore (_), dash (-)");
            } elseif(strlen($username) > 255) {
                // Username is too long
                $this->setError($this->usernameErr,
                               "Username cannot be longer than 255 characters");
            } elseif($this->dbUsers->loadUserByUsername($username)) {
                // Username exists is database
                $this->setError($this->usernameErr, "Username already exists");
            }
        }

        if (empty($email)) {
            // Email address is empty
            $this->setError($this->emailErr, "Email address is required");
        } else {
            // Save email to keep the value in the form
            $this->email = $email;

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Not a valid email address
                $this->setError($this->emailErr, "Email address is not valid");
            } elseif(strlen($email) > 255) {
                // Email address is too long
                $this->setError($this->emailErr,
                               "Email address cannot be longer than 255 characters");
            } elseif($this->dbUsers->loadUserByEmail($email)) {
                // Email address exists is database
                $this->setError($this->emailErr, "Email address already exists");
            }
        }

        if (empty($password)) {
            // Password is empty
            $this->setError($this->passErr, "Password is required");
        } else {
            $passwordError = PasswordStrengthValidation::checkPassword($password, $username ?? '');
            if (!empty($passwordError)) {
                $this->setError($this->passErr, $passwordError);
            }
        }

        if (empty($password_repeat)) {
            // Password Repeat address is empty
            $this->setError($this->passRptErr, "Please enter your password again");
        }

        if (!empty($password)
            and !empty($password_repeat)
            and $password != $password_repeat
        ) {
            // Passwords don't match
            $this->setError($this->passRptErr, "Passwords don't match");
        }

        return $this->values_ok;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getUsernameErr(): string
    {
        return $this->usernameErr;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEmailErr(): string
    {
        return $this->emailErr;
    }

    /**
     * @return string
     */
    public function getPassErr(): string
    {
        return $this->passErr;
    }

    /**
     * @return string
     */
    public function getPassRptErr(): string
    {
        return $this->passRptErr;
    }
}

