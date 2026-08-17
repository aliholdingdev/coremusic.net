<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\DomainConfig;

final class AuthUrlBuilder
{
    public function __construct(
        private readonly DomainConfig $domainConfig,
        private readonly PageRouterHelper $authHelper,
    ) {}

    public function buildAuthRedirect(string $path, ?string $returnPath = null): array
    {
        $url = $this->buildAuthUrl($path, $returnPath);
        return [
            'url'     => $url,
            'headers' => $this->buildAuthHeaders(),
        ];
    }

    public function redirectAuth(string $path, ?string $returnPath, bool $isSpaRequest): array
    {
        $target = $this->buildAuthUrl($path, $returnPath);
        $this->setAuthHeaders();
        if ($isSpaRequest) {
            return RouteResult::forbidden($target);
        }
        return RouteResult::redirect($target);
    }

    private function buildAuthUrl(string $path, ?string $returnPath = null): string
    {
        $scheme      = $this->domainConfig->isHttps() ? 'https' : 'http';
        $currentHost = $this->domainConfig->getHost() ?? 'home.coremusic.net';
        $currentPort = $this->domainConfig->getPort();
        $portSuffix  = ($currentPort !== 80 && $currentPort !== 443) ? ':' . $currentPort : '';
        $callbackDomain = $scheme . '://' . $currentHost . $portSuffix;

        if ($path === 'logout') {
            $homeDomain = $scheme . '://' . $currentHost . $portSuffix;
            $auth = AuthRouteConfig::getAuthUrl($scheme);
            return $auth . '/logout?' . http_build_query(['redirect' => $homeDomain . '/'], '', '&', PHP_QUERY_RFC3986);
        }

        return AuthRouteConfig::buildAuthRedirectUrl(
            $path,
            $returnPath ?? '',
            authDomain: AuthRouteConfig::getAuthUrl($scheme),
            callbackDomain: $callbackDomain,
        );
    }

    private function setAuthHeaders(string $status = 'unauthenticated'): void
    {
        header('X-Auth-Required: true');
        header('X-Auth-Status: ' . $status);
    }

    private function buildAuthHeaders(): array
    {
        return [
            'X-Auth-Required' => 'true',
            'X-Auth-Status'   => 'unauthenticated',
        ];
    }
}
