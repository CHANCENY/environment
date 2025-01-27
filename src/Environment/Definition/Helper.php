<?php

namespace Simp\Environment\Definition;

use Random\RandomException;

trait Helper
{
    private function serialized($data): string
    {
        return serialize($data);
    }
    private function encode($data): string
    {
        return json_encode($data);
    }
    private function base64($data): string
    {
        return base64_encode($data);
    }
    private function unserialized($data)
    {
        return unserialize($data);
    }
    private function decode($data)
    {
        return json_decode($data, true);
    }
    private function base64decode($data)
    {
        return base64_decode($data);
    }

    private function hash($data): string
    {
        return $this->encryptText($data, $this->getHash());
    }
    private function dehash($data)
    {
        return $this->decryptText($data, $this->getHash());
    }

    /**
     * @throws RandomException
     */
    public function encryptText($plaintext, $key): string
    {
        $cipher = "aes-256-cbc";
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }

    /**
     * @param $encrypted
     * @param $key
     * @return false|string
     */
    public function decryptText($encrypted, $key): false|string
    {
        $cipher = "aes-256-cbc";
        $encrypted = base64_decode($encrypted);
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($encrypted, 0, $ivLength);
        $ciphertext = substr($encrypted, $ivLength);
        return openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
    }
}