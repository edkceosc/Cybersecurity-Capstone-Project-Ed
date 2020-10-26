<?php


namespace tests\unit\lib\service;


use lib\db\Users;
use lib\service\RegistrationFormValidation;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class RegistrationFormValidationTest extends TestCase
{
    /**
     * @var Stub
     */
    private $users;

    /**
     * @var RegistrationFormValidation
     */
    private $validator;

    /**
     * Credentials for a dummy user
     */
    private $dummyUserName = 'username';
    private $dummyUserEmail = 'email@example.com';
    private $dummyUserPass = 'complex_*PASS//';

    /**
     * Create a stub of the Users class.
     * @before
     */
    public function setupUserTableAndValidation() {
        $this->users = $this->createStub(Users::class);
        $this->validator = new RegistrationFormValidation($this->users);
    }

    /**
     * Fail validation if the username was not entered.
     */
    public function test_validateValues_returns_false_on_empty_username() {
        $values_ok = $this->validator->validateValues("", "", "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getUsernameErr(), "usernameErr should not be blank");
    }

    /**
     * Fail validation if the username was too long.
     */
    public function test_validateValues_returns_false_on_long_username() {
        $values_ok = $this->validator->validateValues(str_repeat("x", 256), "", "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getUsernameErr(), "usernameErr should not be blank");
    }

    /**
     * Fail validation if the username contained invalid characters.
     */
    public function test_validateValues_returns_false_on_username_with_invalid_characters() {
        $values_ok = $this->validator->validateValues("username=5", "", "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getUsernameErr(), "usernameErr should not be blank");
    }

    /**
     * Fail validation if the email address was not entered.
     */
    public function test_validateValues_returns_false_on_empty_email() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, "", "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getEmailErr(), "emailErr should not be blank");
    }

    /**
     * Fail validation if the email address was too long.
     */
    public function test_validateValues_returns_false_on_long_email() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, str_repeat("x", 256),
                                                                "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getEmailErr(), "emailErr should not be blank");
    }

    /**
     * Generate a list of invalid email addresses for unit testing.
     */
    public function providerInvalidEmailAddress() {
        return array(
            ["email@example"],
            ["email@-example.com"],
            ["email@example..com"],
            ["email.example.com"],
            ["email.@example.com"],
            ["email..email@example.com"],
            [".email@example.com"],
            ["email@example@example.com"],
            ["email@111.222.333.44444"],
            ["@example.com"],
            ["plainaddress"],
            ["email@example.com (Joe Smith)"],
            ["Joe Smith <email@example.com>"],
            ["#@%^%#$@#$@#.com"],
            ["あいうえお@example.com"],
            ["Abc..123@example.com"],
            ['very.unusual."@".unusual.com@example.com\''],
            ['very."(),:;<>[]".VERY."very@\\ "very".unusual@strange.example.com'],
        );
    }
    /**
     * Fail validation if the email address was invalid.
     * @dataProvider providerInvalidEmailAddress
     */
    public function test_validateValues_returns_false_on_invalid_email(string $testEmail) {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $testEmail,
                                                                "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getEmailErr(), "emailErr should not be blank");
    }

    /**
     * Fail validation if the password was not entered.
     */
    public function test_validateValues_returns_false_on_empty_password() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                "", "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassErr(), "passErr should not be blank");
    }

    /**
     * Fail validation if the password was too long.
     */
    public function test_validateValues_returns_false_on_long_password() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                str_repeat("x", 256), "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassErr(), "passErr should not be blank");
    }

    /**
     * Fail validation if the password was too short.
     */
    public function test_validateValues_returns_false_on_short_password() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                str_repeat("x", 5), "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassErr(), "passErr should not be blank");
    }

    /**
     * Fail validation if the password is made up of numbers only.
     */
    public function test_validateValues_returns_false_on_password_made_of_numbers() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                str_repeat("0", 12), "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassErr(), "passErr should not be blank");
    }

    /**
     * Fail validation if the password was not repeated.
     */
    public function test_validateValues_returns_false_on_empty_password_repeat() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                $this->dummyUserPass, "");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassRptErr(), "passRptErr should not be blank");
    }

    /**
     * Fail validation if the password doesn't match the repeated password.
     */
    public function test_validateValues_returns_false_on_password_mismatch() {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $this->dummyUserEmail,
                                                                $this->dummyUserPass, "abc");

        $this->assertFalse($values_ok, 'Validation was expected to return false');
        $this->assertNotEmpty($this->validator->getPassRptErr(), "passRptErr should not be blank");
    }

    /**
     * Generate a list of valid email addresses for unit testing.
     */
    public function providerValidEmailAddress() {
        return array(
            ["email@example.com"],
            ["firstname.lastname@example.com"],
            ["email@subdomain.example.com"],
            ["firstname+lastname@example.com"],
            ["email@[123.123.123.123]"],
            ['"email"@example.com'],
            ["1234567890@example.com"],
            ["email@example-one.com"],
            ["_______@example.com"],
            ["email@example.name"],
            ["email@example.museum"],
            ["email@example.co.jp"],
            ["firstname-lastname@example.com"],
            ['much."more\ unusual"@example.com'],
        );
    }
    /**
     * Successful validation.
     * @dataProvider providerValidEmailAddress
     */
    public function test_validateValues_returns_true_on_successful_validation(string $testEmail) {
        $values_ok = $this->validator->validateValues($this->dummyUserName, $testEmail,
                                                                $this->dummyUserPass, $this->dummyUserPass);

        $this->assertEmpty($this->validator->getUsernameErr(),
                           'usernameErr should be blank, not "' . $this->validator->getUsernameErr() . '"');
        $this->assertEmpty($this->validator->getEmailErr(),
                           'emailErr should be blank, not "' . $this->validator->getEmailErr() . '"');
        $this->assertEmpty($this->validator->getPassErr(),
                           'passErr should be blank, not "' . $this->validator->getPassErr() . '"');
        $this->assertEmpty($this->validator->getPassRptErr(),
                           'passRptErr should be blank, not "' . $this->validator->getPassRptErr() . '"');
        $this->assertTrue($values_ok, 'Validation was expected to return true');
    }
}
