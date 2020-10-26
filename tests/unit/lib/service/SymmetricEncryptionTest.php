<?php


namespace tests\unit\lib\service;


use Exception;
use lib\service\SymmetricEncryption;
use PHPUnit\Framework\TestCase;

class SymmetricEncryptionTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function test_encrypting_the_same_plaintext_yields_different_results()
    {
        $password = 'some-secret-string';
        $plaintext = 'the message 1';

        $sut = new SymmetricEncryption($password);

        $r1 = $sut->encrypt($plaintext);
        $r2 = $sut->encrypt($plaintext);

        $this->assertNotEmpty($r1);
        $this->assertNotEmpty($r2);
        $this->assertNotEquals($r1, $r2);
    }

    /**
     * @throws Exception
     */
    public function test_decryption_of_ciphertext_yields_original_plaintext()
    {
        $password = 'some-secret-string';
        $plaintext = 'the message 1';

        $sut = new SymmetricEncryption($password);

        $encodedCiphertext = $sut->encrypt($plaintext);
        $decrypted = $sut->decrypt($encodedCiphertext);

        $this->assertEquals($plaintext, $decrypted);
    }
}