<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\DTO;

/**
 * RegisterRequest DTO — Kayıt isteği için veri transfer nesnesi.
 */
final class RegisterRequest
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly string $gender,
        public readonly bool $agreeTerms,
        public readonly string $clientIp,
        public readonly string $visitorGender,
    ) {}

    public static function fromArray(array $post, array $server, string $visitorGender = 'neutral'): self
    {
        return new self(
            username: trim((string)($post['username'] ?? '')),
            email: trim((string)($post['email'] ?? '')),
            password: (string)($post['password'] ?? ''),
            gender: (string)($post['gender'] ?? $visitorGender),
            agreeTerms: !empty($post['agree_terms']),
            clientIp: (string)($server['REMOTE_ADDR'] ?? '127.0.0.1'),
            visitorGender: $visitorGender,
        );
    }
}
