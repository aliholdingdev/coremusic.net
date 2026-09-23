<?php declare(strict_types=1);

/**
 * CoreMusic Auth — CORS Config
 *
 * İzin verilen origin listesini döndürür.
 * Runtime'da scheme (http/https) kontrolü yapılarak filtrelenir.
 */

return [
    'allowed_origins' => array_filter(array_map('trim', explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? ''))),
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'X-CSRF-Token', 'X-Requested-With'],
    'allow_credentials' => true,
    'dev_fallback' => array_map(
        fn(string $host) => 'http://' . $host,
        array_filter(array_map('trim', explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '')))
    ),
];
