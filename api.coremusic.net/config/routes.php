<?php declare(strict_types=1);

/**
 * CoreMusic API — Route Kayıtları (ADR-084 Contract First · ADR-020 §1.1-B.1)
 *
 * Method-aware tablo: `shared/src/Api/Routing/RouteTable`.
 *
 * Alanlar:
 *   service / handler : çözümleme anahtarı (Faz 1b AuthController buraya bağlanır)
 *   public            : auth'sız erişim (Authentication/Authorization bunu okur)
 *   implemented       : false → route TANIMLI ama controller yok → 405 + Allow
 *                       (404 DEĞİL); Faz 1b'de true yapılarak handler'a geçilir
 *   validation        : RequestValidationMiddleware kural seti (422)
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
 *   cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control
 */
return [
    'GET' => [
        // Faz 2 hardcodedi GET prefix kayıtları (davranış değişmez)
        '/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],
        '/api/v1/user'     => ['service' => 'user',     'handler' => 'userController'],
        '/api/v1/music'    => ['service' => 'music',    'handler' => 'musicController'],
        '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
        '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
        '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],

        // Public auth uçları — exact kayıt, prefix `/api/v1/auth` ile çakışmasın
        '/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
        '/api/v1/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
        '/api/v1/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
        '/api/v1/auth/reset-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
    ],

    'POST' => [
        // Faz 1a: route'lar TANIMLI, controller Faz 1b'de gelecek → 405 + Allow
        '/api/v1/auth/login' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => true,
            'implemented' => false,
        ],
        '/api/v1/auth/register' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => true,
            'implemented' => false,
        ],
        '/api/v1/auth/forgot-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => true,
            'implemented' => false,
        ],
        '/api/v1/auth/reset-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => true,
            'implemented' => false,
        ],
        '/api/v1/auth/set-gender' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => false,
            'implemented' => false,
        ],
        '/api/v1/auth/logout' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'public'      => false,
            'implemented' => false,
        ],
    ],
];
