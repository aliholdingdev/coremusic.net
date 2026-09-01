<?php declare(strict_types=1);

/**
 * CoreMusic Home — Entry Point
 *
 * Shared SPA Router (PageRouterKernel) kullanır.
 * Auth entegrasyonu: include/ dizininde.
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Bootstrap\RuntimeBootstrap;

/* ─── Config (constants + app) ─── */
require_once __DIR__ . '/config/constants.php';
$appConfig = require __DIR__ . '/config/app.php';

RuntimeBootstrap::boot(DEBUG_MODE);

/* ─── Config Objects ─── */
require_once __DIR__ . '/config/config.php';

/* ─── Application Bootstrap & Routing ─── */
require_once __DIR__ . '/config/bootstrap.php';
