<?php declare(strict_types=1);

/**
 * CoreMusic Auth — CORS Config
 *
 * İzin verilen origin listesini döndürür.
 * Runtime'da scheme (http/https) kontrolü yapılarak filtrelenir.
 */

return [
    'allowed_origins' => [
        'home'   => 'home.coremusic.net',
        'music'  => 'music.coremusic.net',
        'admin'  => 'admin.coremusic.net',
        'coremusic' => 'coremusic.net',
    ],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'X-CSRF-Token', 'X-Requested-With'],
    'allow_credentials' => true,
    'dev_fallback' => [
        'http://home.coremusic.net',
        'http://music.coremusic.net',
    ],
];
