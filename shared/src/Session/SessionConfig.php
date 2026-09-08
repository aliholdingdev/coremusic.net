<?php

declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * SessionConfig — Session parametrelerinin tek kaynak tanımı (SSOT).
 *
 * Tüm session_name / cookie / save_path değerleri buradan okunur.
 * session_* API çağrıları YALNIZCA SessionBootstrapper içinde yapılır.
 */
final class SessionConfig
{
    public const COOKIE_EXPIRY = 42000;

    private function __construct(
        public readonly string $name,
        public readonly string $cookieDomain,
        public readonly string $savePath,
        public readonly bool $secure,
    ) {}

    public static function fromEnvironment(): self
    {
        $name = defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS';

        $savePath = ini_get('session.save_path') ?: 'C:\temp';
        if ($savePath !== '' && !is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
        }

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        );

        return new self(
            name: (string)$name,
            cookieDomain: '.coremusic.net',
            savePath: $savePath,
            secure: $isHttps,
        );
    }

    /** @return array{lifetime: int, path: string, domain: string, secure: bool, httponly: bool, samesite: string} */
    public function cookieParams(): array
    {
        return [
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => $this->cookieDomain,
            'secure'   => $this->secure,
            'httponly'  => true,
            'samesite' => 'Lax',
        ];
    }
}
