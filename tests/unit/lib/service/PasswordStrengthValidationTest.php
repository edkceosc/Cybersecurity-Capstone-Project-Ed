<?php


namespace tests\unit\lib\service;


use lib\service\PasswordStrengthValidation;
use PHPUnit\Framework\TestCase;

class PasswordStrengthValidationTest extends TestCase
{

    public function test_it_returns_empty_string_for_good_passwords()
    {
        $username = 'peter';
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('some.blue8chars', $username));
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('eins two troi', $username));
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('8-Football win', $username));

        // something that might be generated by a password manager
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('SV9t3sZoGu', $username));
        // or with special chars
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('Db\'Y&%73]*', $username));
    }

    public function test_it_fails_on_too_short_passwords()
    {
        // 9 chars
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('SV9t3sZoG', ''));
    }

    public function test_it_fails_if_password_uses_only_digits()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('834192890189', ''));
    }

    public function test_it_fails_if_string_PASSWORD_is_used()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('8xpaSSWordx8', ''));
    }

    public function test_it_fails_password_contains_username()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('iampeter8899', 'peter'));

        // but it should be ok with empty username (username must be checked elsewhere)
        $this->assertEmpty(PasswordStrengthValidation::checkPassword('iampeter8899', ''));
    }

    public function test_it_fails_if_sequence_is_detected()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('hey_bcde_89', ''));
    }

    public function test_it_fails_if_repetition_of_characters_is_detected()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('hey_yyyy_89', ''));
    }

    public function test_it_fails_if_one_character_occurs_too_often()
    {
        $this->assertNotEmpty(PasswordStrengthValidation::checkPassword('x7x8x9x0x1x2x2', ''));
    }

}