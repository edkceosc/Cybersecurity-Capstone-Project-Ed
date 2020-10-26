<?php


namespace lib\service;

/**
 * This class allows to check passwords against some common weaknesses.
 *
 * Note that the purpose of this class is to check new passwords, e.g. during registration,
 * for weaknesses; it cannot verify if a password matches a hash (e.g. during login).
 *
 */
class PasswordStrengthValidation
{

    private static $minLength = 10;

    private static $maxAllowedSeqLength = 3;

    private static $maxAllowedRepetitionLength = 3;

    // how often a single character may occur relative to the length of the whole string:
    // A single character should not occur more then 40% of all chars.
    private static $maxOccurrence = 0.4;

    // A password should not contain any of this strings
    private static $badPasswordParts = ['password', 'asdf', 'qwerty'];

    /**
     * Checks the given password for some common weaknesses:
     *
     * - too long (>255 chars) or too short
     * - only digits
     * - password and username are related (username occurs in the password)
     * - password contains some blacklisted, often used substrings
     * - password contains longer sequences of characters, e.g. "abcd"
     * - password contains a repeating character, "aaaa"
     * - password contains a single char, e.g. "a", too often compared to the length of the whole password.
     *
     * @param string $password
     * @param string $username
     *
     * @return string A string describing the weakness. Empty if the password is ok.
     */
    public static function checkPassword(string $password, string $username): string
    {
        if (strlen($password) > 255) {
            return "Password cannot be longer than 255 characters";
        }
        if (strlen($password) < self::$minLength) {
            return "Password is too short";
        }
        if (preg_match_all("/^[[:digit:]]+$/", $password)) {
            // Password is made up of numbers only
            return "Weak password (contains numbers only)";
        }
        if (!empty($username) && self::containsSubstring($password, $username)) {
            return 'Weak password (must not be related to the username)';
        }
        if (self::containsBlacklistedParts($password)) {
            return 'Weak password (contains blacklisted strings)';
        }

        // apply some heuristics to detect weak passwords.

        // Count how often a character appears
        $charsCounter = [];

        // check for repeating chars ("aaaa") and sequences (e.g "1234", or "abcd")
        $repeatingCounter = 0;
        $repeatingDetected = false;

        $seqCounter = 0;
        $seqDetected = false;

        // character of last iteration
        $lastChar = -1;

        $len = strlen($password);
        for ($i = 0; $i < $len; $i++) {
            $c = $password[$i];
            if (!isset($charsCounter[$c])) {
                $charsCounter[$c] = 0;
            }
            $charsCounter[$c]++;

            // sequence check
            $cOrd = ord($c);
            if ($cOrd === $lastChar + 1) {
                $seqCounter++;
                if ($seqCounter > self::$maxAllowedSeqLength) {
                    $seqDetected = true;
                }
            } else {
                $seqCounter = 1;
            }

            // repeating check
            if ($cOrd === $lastChar) {
                $repeatingCounter++;
                if ($repeatingCounter >= self::$maxAllowedRepetitionLength) {
                    $repeatingDetected = true;
                }
            } else {
                $repeatingCounter = 1;
            }

            $lastChar = $cOrd;
        }

        $tooManyOccurrences = false;
        $maxCountAllowed = doubleval(strlen($password)) * self::$maxOccurrence;
        foreach ($charsCounter as $c) {
            if ($c > $maxCountAllowed) {
                $tooManyOccurrences = true;
                break;
            }
        }
        if ($seqDetected) {
            return 'Weak password (contains sequence)';
        } elseif ($repeatingDetected) {
            return 'Weak password (contains repeating chars)';
        } elseif ($tooManyOccurrences) {
            return 'Weak password (some characters occur too often)';
        }
        // returning empty string means: password is ok
        return '';
    }

    private static function containsBlacklistedParts($password)
    {
        foreach (self::$badPasswordParts as $part) {
            if (self::containsSubstring($password, $part)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if $value contains the  $substr (tested case insensitively)
     */
    private static function containsSubstring(string $value, string $substr): bool
    {
        $haystack = strtolower($value);
        $needle = strtolower($substr);
        return strpos($haystack, $needle) !== false;
    }
}