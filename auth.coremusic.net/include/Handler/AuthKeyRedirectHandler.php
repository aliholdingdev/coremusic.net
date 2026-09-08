<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Handler;

use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Auth\Container\AuthContainer;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Session\SessionBootstrapper;

/**
 * AuthKeyRedirectHandler — Root auth_key validation and redirect.
 *
 * Extracted from index.php for SRP compliance.
 */
final class AuthKeyRedirectHandler
{
    public function __construct(
        private readonly ConfigManager $config,
        private readonly DomainConfig $domainConfig,
    ) {}

    /**
     * Handle root auth_key callback. Returns true if redirect happened.
     */
    public function handle(): bool
    {
        if (empty($_GET['auth_key'])) {
            return false;
        }

        $logger = LoggerFactory::getInstance();

        $logger->debug('Root auth_key validation triggered', [
            'auth_key_prefix' => substr($_GET['auth_key'], 0, 8) . '...',
        ]);

        SessionBootstrapper::ensureStarted();

        $container  = AuthContainer::getInstance($this->config, $this->domainConfig);
        $controller = $container->get(AuthController::class);
        $result = $controller->handleValidateKey([
            'query_params' => $_GET,
            'body'         => ['auth_key' => $_GET['auth_key']],
            'server'       => $_SERVER,
        ]);

        if (($result['httpStatus'] ?? 0) === 200 && !empty($result['body']['success'])) {
            $user = $result['body']['user'];
            $session = $container->get(\CoreMusic\Interfaces\Auth\ISessionManager::class);
            $session->setAuthUser($user);
            if (!empty($user['gender'])) {
                $session->setGender($user['gender']);
            }

            $logger->debug('Root auth_key validated, redirecting to /home', [
                'user_id' => $user['id'] ?? '-',
            ]);

            header('Location: /home', true, 302);
            exit;
        }

        $logger->warning('Root auth_key validation failed', [
            'http_status' => $result['httpStatus'] ?? 0,
        ]);

        header('Location: /login?error=invalid_key', true, 302);
        exit;
    }
}
