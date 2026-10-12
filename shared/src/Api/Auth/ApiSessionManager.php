<?php
declare(strict_types=1);

/**
 * API-side session bridge for hybrid auth (ADR-052).
 *
 * @file ApiSessionManager.php
 * @version 1.0.0
 * @see ADR-052-hybrid-auth
 */

namespace CoreMusic\Api\Auth;

use CoreMusic\Contracts\Auth\ISessionManager;

/**
 * api.coremusic.net için session köprüsü.
 *
 * session_start() ÇAĞIRMAZ: aktif oturum varsa $_SESSION üzerinden okur/yazar,
 * yoksa istek-boyu bellek üzerinde çalışır. Böylece API pipeline'ı ek cookie
 * üretmeden çalışır ve AuthenticationMiddleware'in ISessionManager bağımlılığı
 * shared katmanında karşılanmış olur.
 */
final class ApiSessionManager implements ISessionManager
{
    /** @var array<string, mixed> */
    private array $store = [];

    private bool $authenticated = false;

    private ?string $userId = null;

    private string $cspNonce = '';

    public function setAuthUser(array $user): void
    {
        $this->authenticated = true;
        $this->userId = isset($user['id']) ? (string) $user['id'] : null;
        $this->write('user', $user);
    }

    public function setRegisteredUser(array $created): void
    {
        $this->setAuthUser($created);
    }

    public function getUserId(): ?string
    {
        if ($this->userId !== null) {
            return $this->userId;
        }
        $user = $this->read('user');

        if (isset($user['id'])) {
            return (string) $user['id'];
        }

        // Web oturumu (auth.coremusic.net SessionManager) MM_* anahtarları yazar;
        // aynı cookie/paylaşılan save path ile API de o oturumu tanımalı (Faz 1b).
        $legacy = $this->read('MM_UserID');

        return (is_string($legacy) && $legacy !== '') ? $legacy : null;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated
            || $this->read('user') !== null
            || $this->getUserId() !== null;
    }

    public function destroy(): void
    {
        $this->store = [];
        $this->authenticated = false;
        $this->userId = null;
        if ($this->sessionActive()) {
            $_SESSION = [];
        }
    }

    public function regenerateId(): void
    {
        if ($this->sessionActive() && !headers_sent()) {
            session_regenerate_id(true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->read($key);

        return $value ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->write($key, $value);
    }

    public function remove(string $key): void
    {
        unset($this->store[$key]);
        if ($this->sessionActive()) {
            unset($_SESSION[$key]);
        }
    }

    public function setGender(string $gender): void
    {
        $this->write('gender', $gender);
    }

    public function getGender(): string
    {
        return (string) ($this->read('gender') ?? '');
    }

    public function setPendingRedirect(string $uri): void
    {
        $this->write('pending_redirect', $uri);
    }

    public function consumePendingRedirect(): ?string
    {
        $uri = $this->read('pending_redirect');
        if ($uri === null) {
            return null;
        }
        $this->remove('pending_redirect');

        return (string) $uri;
    }

    public function regenerateCspNonce(): string
    {
        $this->cspNonce = bin2hex(random_bytes(16));
        $this->write('csp_nonce', $this->cspNonce);

        return $this->cspNonce;
    }

    public function getCspNonce(): string
    {
        if ($this->cspNonce !== '') {
            return $this->cspNonce;
        }
        $stored = $this->read('csp_nonce');
        if (is_string($stored) && $stored !== '') {
            $this->cspNonce = $stored;

            return $this->cspNonce;
        }

        return $this->regenerateCspNonce();
    }

    public function isIdleExpired(int $timeoutSeconds): bool
    {
        // API istekleri stateless'tır; idle timeout web oturumunun sorumluluğundadır.
        return false;
    }

    public function touch(): void
    {
        // no-op: session_start() yapılmaz.
    }

    public function rotateIfNeeded(int $intervalSeconds): bool
    {
        return false;
    }

    public function clearDisplayCookies(): void
    {
        // no-op: API yanıtı Set-Cookie üretmez.
    }

    public function getCookieParams(): array
    {
        return $this->sessionActive() ? session_get_cookie_params() : [];
    }

    public function all(): array
    {
        if ($this->sessionActive()) {
            return $_SESSION;
        }

        return $this->store;
    }

    private function sessionActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function read(string $key): mixed
    {
        if ($this->sessionActive() && array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $this->store[$key] ?? null;
    }

    private function write(string $key, mixed $value): void
    {
        if ($this->sessionActive()) {
            $_SESSION[$key] = $value;

            return;
        }
        $this->store[$key] = $value;
    }
}
