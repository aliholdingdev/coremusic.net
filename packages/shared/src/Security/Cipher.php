<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Security;

use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Encryption\SecretKey;
use ParagonIE\Halite\Encryption\PublicKey;
use ParagonIE\Halite\File;

class Cipher
{
    private SecretKey $secretKey;
    private PublicKey $publicKey;

    public function __construct(string $secretKeyPath)
    {
        if (file_exists($secretKeyPath)) {
            $this->secretKey = KeyFactory::loadEncryptionSecretKey($secretKeyPath);
        } else {
            $this->secretKey = KeyFactory::generateEncryptionKey();
            KeyFactory::save($this->secretKey, $secretKeyPath);
        }
        $this->publicKey = $this->secretKey->getPublicKey();
    }

    public function encrypt(string $plaintext): string
    {
        $hidden = new HiddenString($plaintext);
        $encrypted = \ParagonIE\Halite\Encryption::encrypt($hidden, $this->secretKey);
        sodium_memzero($hidden);
        return $encrypted;
    }

    public function decrypt(string $ciphertext): string
    {
        $decrypted = \ParagonIE\Halite\Encryption::decrypt($ciphertext, $this->secretKey);
        $result = $decrypted->getString();
        sodium_memzero($decrypted);
        return $result;
    }

    public function encryptFile(string $sourcePath, string $destPath): void
    {
        File::encryptFile($sourcePath, $destPath, $this->secretKey);
    }

    public function decryptFile(string $sourcePath, string $destPath): void
    {
        File::decryptFile($sourcePath, $destPath, $this->secretKey);
    }
}
