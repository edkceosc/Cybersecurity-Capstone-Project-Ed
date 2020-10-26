<?php


namespace lib\service;

use Exception;

/**
 * This class provides a simple interface to encrypt (small) data, using an authenticated encryption scheme.
 *
 * @package lib\service
 */
class SymmetricEncryption
{

    // 16 byte = 128 bit
    private $keyLength = 16;

    private $cipher = 'aes-128-gcm';

    /**
     * @var int
     */
    private $ivLength;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $password The password used for encryption and decryption.
     *
     * @throws Exception If the cipher is not supported.
     */
    public function __construct(string $password)
    {
        if (!in_array($this->cipher, openssl_get_cipher_methods())) {
            throw new Exception('Cipher not supported');
        }
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
        if ($this->ivLength === false) {
            throw new Exception('unexpected error with encryption cipher');
        }
        $this->password = $password;
    }

    /**
     * Creates a new instance, initialized with a password read from the env variable  `MESSAGE_ENCRYPTION_PASSWORD`.
     *
     * @return SymmetricEncryption
     * @throws Exception If the environment has no password variable, or the server does not support the cipher.
     */
    public static function fromEnvironment(): SymmetricEncryption
    {
        $password = getenv('MESSAGE_ENCRYPTION_PASSWORD');
        if (!$password) {
            throw new Exception('Bad configuration: Missing password for message encryption');
        }
        return new SymmetricEncryption($password);
    }

    /**
     * @param string $plaintext
     *
     * @return string
     * @throws Exception
     */
    public function encrypt(string $plaintext)
    {
        list($key, $salt) = $this->deriveKeyFromPassword(null);
        $iv = $this->getStrongRandomData($this->ivLength);
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $key, $options = 0, $iv, $tag);

        // encode data:
        // - salt for key derivation,
        // - iv (initialization vector),
        // - tag (message authentication tag),
        // - ciphertext

        $parts = [
            bin2hex($salt),
            bin2hex($iv),
            bin2hex($tag),
            $ciphertext // no need to hex-encode last part, we know it's the last part via its index; so just read until end.
        ];
        return implode('$', $parts);
    }

    /**
     * @param string $encodedCiphertext
     *
     * @return string
     * @throws Exception
     */
    public function decrypt(string $encodedCiphertext)
    {
        // decode parts first
        $parts = explode('$', $encodedCiphertext, 4);
        $salt = hex2bin($parts[0]);
        $iv = hex2bin($parts[1]);
        $tag = hex2bin($parts[2]);
        $ciphertext = $parts[3];

        if ($salt === false || $iv === false || $tag === false) {
            throw new Exception('Failed to decode encrypted data');
        }
        // derive key, using the salt
        list($key,) = $this->deriveKeyFromPassword($salt);

        // then decrypt
        $result = openssl_decrypt($ciphertext, $this->cipher, $key, $options = 0, $iv, $tag);
        if ($result === false) {
            throw new Exception('Decryption failed');
        }
        return $result;
    }

    /**
     * @param string|null $salt
     *
     * @return array
     * @throws Exception
     */
    private function deriveKeyFromPassword(?string $salt)
    {
        $salt = $salt ?? $this->getStrongRandomData(8);
        $iterations = 10000;
        $key = openssl_pbkdf2($this->password, $salt, $this->keyLength, $iterations, 'sha256');
        if ($key === false) {
            $lastErr = error_get_last();
            $errMsg = print_r($lastErr, true);
            throw new Exception('Could not derive a key from the given password: ' . $errMsg);
        }
        return [$key, $salt];
    }

    /**
     * @param $lengthInBytes
     *
     * @return string
     * @throws Exception
     */
    private function getStrongRandomData($lengthInBytes)
    {
        $rData = openssl_random_pseudo_bytes($lengthInBytes, $strong);
        if (!$strong) {
            throw new Exception('could not create cryptographically strong random data', 500);
        }

        if (!$rData) {
            throw new Exception('could not create secret', 500);
        }
        return $rData;
    }
}