<?php

declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * SessionInitializer — Centralized session startup.
 *
 * Replaces duplicate cm_session_start() calls across entry points.
 * Single source of truth for session configuration.
 */
final class SessionInitializer
{
    private static bool $started = false;

    /**
     * Start session if not already active. Safe to call multiple times.
     */
    public static function ensureStarted(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionName = defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS';
        session_name($sessionName);

        $savePath = ini_get('session.save_path') ?: 'C:\\temp';
        if ($savePath !== '' && !is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
        }
        if ($savePath !== '') {
            session_save_path($savePath);
        }

        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '.coremusic.net',
            'secure'   => $isHttps,
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;
    }
}
