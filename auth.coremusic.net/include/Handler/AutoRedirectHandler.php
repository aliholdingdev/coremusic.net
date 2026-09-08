<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Handler;

use CoreMusic\Auth\Container\AuthContainer;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Session\SessionBootstrapper;

/**
 * AutoRedirectHandler — Authenticated user auto-redirect.
 *
 * When an authenticated user visits /login or /register with redirect_uri,
 * generate an auth_key and redirect to the callback URL.
 * Prevents redirect loops on home.coremusic.net.
 *
 * Extracted from index.php for SRP compliance.
 */
final class AutoRedirectHandler
{
    public function __construct(
        private readonly ConfigManager $config,
        private readonly DomainConfig $domainConfig,
    ) {}

    /**
     * Handle auto-redirect for authenticated users. Returns true if redirect happened.
     */
    public function handle(string $pageName, string $method): bool
    {
        if ($method === 'POST') {
            return false;
        }
        if (!in_array($pageName, ['login', 'register'], true)) {
            return false;
        }
        if (empty($_GET['redirect_uri'])) {
            return false;
        }

        SessionBootstrapper::ensureStarted();

        $authHelper = new \CoreMusic\PageRouter\PageRouterHelper();
        if (!$authHelper->checkAuthenticated()) {
            return false;
        }

        $userId = $_SESSION['MM_UserID'] ?? null;
        if ($userId === null || !is_string($userId) || $userId === '') {
            return false;
        }

        $container = AuthContainer::getInstance($this->config, $this->domainConfig);
        $repo      = $container->get(\CoreMusic\Interfaces\Auth\IUserRepository::class);
        $clientIp  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $authKey   = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 300);
        $repo->saveAuthKey($userId, $authKey, $expiresAt, $clientIp);

        $redirectUri = $_GET['redirect_uri'];

        if (!\CoreMusic\Security\SecurityHelper::isRedirectUriSafe($redirectUri)) {
            LoggerFactory::getInstance()->warning('Unsafe redirect_uri blocked in auto-redirect', [
                'redirect_uri' => $redirectUri,
            ]);
            $redirectUri = '/';
        }

        $separator   = str_contains($redirectUri, '?') ? '&' : '?';
        $callbackUrl = $redirectUri . $separator . 'auth_key=' . urlencode($authKey);

        LoggerFactory::getInstance()->debug('Authenticated user auto-redirect', [
            'user_id'      => $userId,
            'redirect_uri' => $redirectUri,
        ]);

        header('Location: ' . $callbackUrl, true, 302);
        exit;
    }
}
