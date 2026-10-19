<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\DTO;

/**
 * LoginRequest DTO — Login isteği için veri transfer nesnesi.
 *
 * Controller'dan AuthService'e giden veriyi temsil eder.
 * İmmutable — constructor'da set edilir, değiştirilemez.
 */
final class LoginRequest
{
    public function __construct(
        public readonly string $identity,
        public readonly string $password,
        public readonly string $visitorGender,
        public readonly string $clientIp,
    ) {}

    /**
     * HTTP request array'inden oluştur.
     * Session gender, POST'tan değil session'dan alınır (gender gate zaten zorunlu tutar).
     */
    public static function fromArray(array $post, array $server, string $sessionGender = 'neutral'): self
    {
        return new self(
            identity: trim((string)($post['email'] ?? $post['identity'] ?? '')),
            password: (string)($post['password'] ?? ''),
            visitorGender: $sessionGender,
            clientIp: (string)($server['REMOTE_ADDR'] ?? '127.0.0.1'),
        );
    }
}
