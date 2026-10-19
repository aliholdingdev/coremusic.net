<?php declare(strict_types=1);

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;

/* ─── HTTPS Detection ─── */
$isHttps = $appConfig['domain']['isHttps'];
$currentHost = $appConfig['domain']['host'];
$currentPort = $appConfig['domain']['port'];

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

/* ─── Config Objects ─── */
$domainConfig = new DomainConfig(dirname(__DIR__) . '/vendor/coremusic/shared-infrastructure/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$config = new ConfigManager($appConfig);
