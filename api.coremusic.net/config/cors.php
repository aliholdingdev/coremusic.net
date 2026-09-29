<?php declare(strict_types=1);

/**
 * CoreMusic API — CORS Config
 *
 * İzin verilen origin listesini döndürür (ADR-020 §2.2E).
 * Varsayılan: auth.coremusic.net (prod https + dev http).
 * CORS_ALLOWED_ORIGINS ortam değişkeni verilirse onu geçer.
 */

$envOrigins = array_filter(array_map('trim', explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '')));

return [
    'allowed_origins' => $envOrigins !== [] ? array_values($envOrigins) : [
        'https://auth.coremusic.net',
        'http://auth.coremusic.net',
    ],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => [
        'Authorization',
        'X-Api-Key',
        'Content-Type',
        'X-CSRF-Token',
        'X-Requested-With',
    ],
    'allow_credentials' => true,
    'dev_fallback' => ['http://auth.coremusic.net'],
];
