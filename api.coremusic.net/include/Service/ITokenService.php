<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Security\JwtService;

/**
 * TokenService — JWT access token yönetimi (issue + persist + revoke).
 *
 * AuthController'dan token mantığını ayırarak SRP sağlar.
 * Sadece login/register sonrası token üretimi ve kaydetme sorumluluğu vardır.
 */
interface ITokenService
{
    /**
     * Login/register sonrası auth payload'ına token ekler.
     *
     * @param array<string, mixed> $authResult AuthService::login/register sonucu
     * @param array<string, mixed> $requestBody İstek gövdesi (gender, redirect_uri vb.)
     * @return array<string, mixed> authPayload'a eklenecek token verisi
     */
    public function buildAuthPayload(array $authResult, array $requestBody): array;

    /**
     * Access token'ı revoke et (logout / password change).
     *
     * @param string $userId 32-char hex UUID
     */
    public function revokeUserTokens(string $userId): void;

    /**
     * Access token'ın geçerli olup olmadığını kontrol et.
     */
    public function isValidAccessToken(string $jti): bool;
}