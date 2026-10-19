<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Entry Point
 *
 * Shared SPA Router (PageRouterKernel) kullanır.
 * Admin-specific kodlar: include/ dizininde.
 * Admin-specific sayfalar: pages/ dizininde.
 */

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/constants.php';

use CoreMusic\Bootstrap\RuntimeBootstrap;

/* --- Opcache temizleme (sadece geliştirme ortamı için) --- */
if (defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')) {
    opcache_reset();
}

RuntimeBootstrap::boot(DEBUG_MODE);

/* --- Application Bootstrap & Routing --- */
require_once __DIR__ . '/config/bootstrap.php';
