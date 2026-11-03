<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Security\SecurityHelper;

/**
 * RedirectService — Güvenli redirect URL çözümleme.
 *
 * AuthController'dan redirect mantığını ayırarak SRP sağlar.
 * Open redirect koruması ve auth_key ekleme işlemlerini yönetir.
 */
interface IRedirectService
{
    /**
     * Redirect URL'i çözümle ve güvenli hale getir.
     *
     * @param array<string, mixed> $requestBody İstek gövdesi (redirect_uri, redirect)
     * @param string $authKey Auth key (login/register sonrası)
     * @return string Güvenli redirect URL'si
     */
    public function resolveRedirect(array $requestBody, string $authKey = ''): string;

    /**
     * Default fallback URL belirle.
     */
    public function getDefaultFallback(): string;
}