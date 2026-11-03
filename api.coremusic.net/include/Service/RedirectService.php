<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Security\SecurityHelper;

/**
 * RedirectService — Güvenli redirect URL çözümleme.
 *
 * AuthController'dan redirect mantığını ayırarak SRP sağlar.
 * Open redirect koruması ve auth_key ekleme işlemlerini yönetir.
 */
final class RedirectService implements IRedirectService
{
    public function __construct(
        private readonly string $defaultFallback,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function resolveRedirect(array $requestBody, string $authKey = ''): string
    {
        $candidate = (string) ($requestBody['redirect_uri'] ?? '');
        if ($candidate === '') {
            $candidate = (string) ($requestBody['redirect'] ?? '');
        }
        if ($candidate === '') {
            $candidate = (string) ($_GET['redirect_uri'] ?? $_GET['redirect'] ?? '');
        }

        return $this->safeRedirect($candidate, $this->defaultFallback, $authKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultFallback(): string
    {
        return $this->defaultFallback;
    }

    /**
     * Open-redirect koruması: ReturnUrlPolicy → SecurityHelper.
     */
    private function safeRedirect(string $candidate, string $fallback, string $authKey = ''): string
    {
        $target = ($candidate !== '' && SecurityHelper::isRedirectUriSafe($candidate))
            ? $candidate
            : $fallback;

        if ($authKey !== '') {
            $separator = str_contains($target, '?') ? '&' : '?';
            $target   .= $separator . 'auth_key=' . urlencode($authKey);
        }

        return $target;
    }
}