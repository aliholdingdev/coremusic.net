<?php declare(strict_types=1);

namespace CoreMusic\Config;

final class DomainConfig
{
    private array $config;
    private array $subdomains;
    private string $runtimeScheme = '';
    private string $runtimeHost = '';
    private int $runtimePort = 0;

    public function __construct(?string $domainConfigPath = null)
    {
        if ($domainConfigPath === null) {
            $candidates = [
                dirname(__DIR__, 3) . '/config/domain.php',
                dirname(__DIR__, 4) . '/config/domain.php',
            ];
            foreach ($candidates as $path) {
                if (file_exists($path)) {
                    $domainConfigPath = $path;
                    break;
                }
            }
        }

        if ($domainConfigPath === null || !file_exists($domainConfigPath)) {
            $this->config = [
                'primary' => 'coremusic.net',
                'primary_port' => 80,
                'subdomain_port' => 80,
                'subdomains' => [
                    'auth'   => 'auth.coremusic.net',
                    'home'   => 'home.coremusic.net',
                    'assets' => 'assets.coremusic.net',
                    'media'  => 'media.coremusic.net',
                    'api'    => 'api.coremusic.net',
                ],
            ];
        } else {
            $this->config = require $domainConfigPath;
        }
        $this->subdomains = $this->config['subdomains'] ?? [];
    }

    public function setOverrides(string $scheme, string $host, int $port): void
    {
        $this->runtimeScheme = $scheme;
        $this->runtimeHost = $host;
        $this->runtimePort = $port;
    }

    public function getScheme(): string
    {
        return $this->runtimeScheme ?: ($this->config['scheme'] ?? 'http');
    }

    public function getPrimaryPort(): int
    {
        return $this->runtimePort > 0 ? $this->runtimePort : ($this->config['primary_port'] ?? 80);
    }

    public function getSubdomainPort(): int
    {
        return $this->config['subdomain_port'] ?? 80;
    }

    public function getPrimaryHost(): string
    {
        return $this->config['primary'] ?? 'coremusic.net';
    }

    public function getPrimaryUrl(): string
    {
        return $this->buildUrl($this->getScheme(), $this->getPrimaryHost(), $this->getPrimaryPort());
    }

    public function getSubdomainHost(string $name): ?string
    {
        return $this->subdomains[$name] ?? null;
    }

    public function getUrl(string $name): string
    {
        $host = $this->subdomains[$name] ?? null;
        if ($host === null) {
            return $this->getPrimaryUrl();
        }
        return $this->buildUrl($this->getScheme(), $host, $this->getSubdomainPort());
    }

    public function getHost(): string
    {
        return $this->runtimeHost ?: $this->getPrimaryHost();
    }

    public function getPort(): int
    {
        return $this->runtimePort > 0 ? $this->runtimePort : $this->getPrimaryPort();
    }

    public function isHttps(): bool
    {
        return $this->getScheme() === 'https' || $this->getPort() === 443;
    }

    public function isDevelopment(): bool
    {
        $port = $this->getPrimaryPort();
        return $port !== 80 && $port !== 443;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function buildUrl(string $scheme, string $host, int $port): string
    {
        $url = $scheme . '://' . $host;
        if ($port !== 80 && $port !== 443) {
            $url .= ':' . $port;
        }
        return $url;
    }
}
