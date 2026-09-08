<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Security;

use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\Signatures\SignatureKeyPair;
use ParagonIE\Halite\Signing;
use Ramsey\Uuid\Uuid;

class TokenGenerator
{
    private SignatureKeyPair $keyPair;

    public function __construct(string $keyPairPath)
    {
        if (file_exists($keyPairPath)) {
            $this->keyPair = \ParagonIE\Halite\KeyFactory::loadSignatureKeyPair($keyPairPath);
        } else {
            $this->keyPair = \ParagonIE\Halite\KeyFactory::generateSignatureKeyPair();
            \ParagonIE\Halite\KeyFactory::save($this->keyPair, $keyPairPath);
        }
    }

    public function generateAccessToken(string $userId, int $ttlSeconds = 3600): string
    {
        $payload = json_encode([
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $ttlSeconds,
            'jti' => Uuid::uuid4()->toString(),
        ], JSON_THROW_ON_ERROR);

        $hidden = new HiddenString($payload);
        $signed = Signing::sign($hidden, $this->keyPair->getSecretKey());
        sodium_memzero($hidden);

        return $signed;
    }

    public function generateRefreshToken(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function verifyToken(string $token): ?array
    {
        try {
            $verified = Signing::verify($token, $this->keyPair->getPublicKey());
            $payload = json_decode($verified->getString(), true, 512, JSON_THROW_ON_ERROR);
            sodium_memzero($verified);

            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                return null;
            }

            return $payload;
        } catch (\Throwable) {
            return null;
        }
    }
}
