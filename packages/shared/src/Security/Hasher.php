<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Security;

use ParagonIE\Halite\Password;
use ParagonIE\Halite\Password\VersionConfig;

class Hasher
{
    public function hashPassword(string $password): string
    {
        $config = new VersionConfig([
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,
            'threads' => 2,
        ]);

        return Password::hash($password, $config);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Password::verify($password, $hash);
    }

    public function hashData(string $data): string
    {
        return sodium_bin2hex(sodium_crypto_generichash($data));
    }

    public function verifyData(string $data, string $hash): bool
    {
        return sodium_compare($this->hashData($data), $hash);
    }
}
