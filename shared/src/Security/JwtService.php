<?php declare(strict_types=1);

namespace CoreMusic\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Service — RS256 access-token issue/validate (B-F-01 / P1-9).
 *
 * Hybrid auth (anayasa §7): oturum (session cookie) tarayıcı için, JWT
 * (Bearer) API istemcileri için. firebase/php-jwt ^7.2 kullanılır —
 * şifreleme implement edilmez (zero-hallucination: kütüphane sınırı).
 *
 * Güvenlik kuralları:
 * - RS256 (asimetrik): private.pem YALNIZ imzalama için; validate public.pem ile.
 * - exp/nbf kontrolü decode'da (kütüphane); iss/aud elle doğrulanır (fail-closed).
 * - jti: tek kullanımlık kimlik — user_tokens'ta sha256(jti) ile saklanır,
 *   logout/revoke ile geçersizleşir (revocation, B-F-20 sınıfı).
 * - private.pem asla commit edilmez (.gitignore) ve ASLA loglanmaz.
 */
final class JwtService
{
    private const ALGORITHM = 'RS256';

    private ?string $privateKey = null;
    private ?string $publicKey  = null;

    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly string $issuer = 'coremusic',
        private readonly string $audience = 'coremusic-api',
        private readonly int $ttlSeconds = 3600,
    ) {
    }

    /**
     * Access token üret.
     *
     * @param array<string, mixed> $extra ek claims (ör. role) — iss/aud/sub/iat/nbf/exp/jti EZİLMEZ
     * @return array{token: string, expires_in: int, jti: string, token_type: string}
     */
    public function issue(string $userId, array $extra = []): array
    {
        if ($userId === '') {
            throw new \InvalidArgumentException('JWT sub (userId) boş olamaz.');
        }

        $now = time();
        $jti = bin2hex(random_bytes(16));
        $exp = $now + $this->ttlSeconds;

        $claims = array_merge($extra, [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $userId,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'jti' => $jti,
        ]);

        $token = JWT::encode($claims, $this->privateKey(), self::ALGORITHM);

        return [
            'token'      => $token,
            'expires_in' => $this->ttlSeconds,
            'jti'        => $jti,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Token doğrula — geçerliyse claims dizisi, aksi halde null (fail-closed).
     *
     * İmza, exp, nbf kütüphanece; iss/aud elle kontrol edilir.
     * Revocation (jti'nin user_tokens geçerliliği) BURADA DEĞİL — çağıran
     * katmanda (AuthenticationMiddleware) yapılır, çünkü DB erişimi servise ait.
     *
     * @return array<string, mixed>|null
     */
    public function validate(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        try {
            $claims = JWT::decode($token, new Key($this->publicKey(), self::ALGORITHM));
        } catch (\Throwable) {
            // imza/süre/format hataları — hiçbir ayrıntı sızdırılmaz
            return null;
        }

        if (($claims->iss ?? null) !== $this->issuer) {
            return null;
        }
        if (($claims->aud ?? null) !== $this->audience) {
            return null;
        }
        if (!isset($claims->sub, $claims->jti) || $claims->sub === '' || $claims->jti === '') {
            return null;
        }

        /** @var array<string, mixed> $arr */
        $arr = (array) $claims;
        return $arr;
    }

    private function privateKey(): string
    {
        if ($this->privateKey === null) {
            $pem = is_file($this->privateKeyPath) ? file_get_contents($this->privateKeyPath) : false;
            if ($pem === false || trim((string) $pem) === '') {
                throw new \RuntimeException(
                    'JWT private key okunamadı: ' . $this->privateKeyPath
                    . ' — openssl genpkey -algorithm RSA -out private.pem ile üretin (dosya asla commit edilmez).'
                );
            }
            $this->privateKey = (string) $pem;
        }
        return $this->privateKey;
    }

    private function publicKey(): string
    {
        if ($this->publicKey === null) {
            $pem = is_file($this->publicKeyPath) ? file_get_contents($this->publicKeyPath) : false;
            if ($pem === false || trim((string) $pem) === '') {
                throw new \RuntimeException('JWT public key okunamadı: ' . $this->publicKeyPath);
            }
            $this->publicKey = (string) $pem;
        }
        return $this->publicKey;
    }
}
