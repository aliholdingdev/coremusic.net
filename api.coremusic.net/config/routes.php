<?php declare(strict_types=1);

/**
 * CoreMusic API — Route Kayıtları (ADR-084 Contract First · ADR-020 §1.1-B.1)
 *
 * Method-aware tablo: `shared/src/Api/Routing/RouteTable`.
 *
 * Alanlar:
 *   service / handler : çözümleme anahtarı (Faz 1b AuthController — container resolver)
 *   action            : AuthController::handle() dispatch anahtarı (Faz 1b)
 *   public            : auth'sız erişim (Authentication/Authorization bunu okur)
 *   implemented       : false → route TANIMLI ama controller yok → 405 + Allow
 *                       (404 DEĞİL); Faz 1b'de 6 POST ucu true yapıldı
 *   validation        : RequestValidationMiddleware kural seti (422 + error.fields)
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
 *   cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control
 *
 * Faz 1b sözleşmesi (Faz 3a UI — DEĞİŞTİRİLEMEZ):
 *   POST /v1/auth/login|register|set-gender|forgot-password|reset-password|logout
 *   GET  /v1/auth/me
 *
 * GET'ler POST-only uçlardır → 405 + Allow: POST, OPTIONS (yalnızca GET /v1/auth/me 200/401).
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
        // Bu 4 uç POST-only'dir: GET implemented=false → 405 + Allow: POST, OPTIONS
        '/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/reset-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],

        // Faz 1b: oturumdaki kullanıcıyı döndürür (200 {data:{user}} | 401)
        '/api/v1/auth/me' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'me',
            'public'      => false,
            'implemented' => true,
        ],
    ],

    'POST' => [
        // Faz 1b: 6 uç implemented=true + AuthController action'ı + doğrulama kuralları
        '/api/v1/auth/login' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'login',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                // identity: e-posta VEYA kullanıcı adı (LoginRequest::fromArray)
                'email'    => ['required' => true, 'type' => 'string'],
                'password' => ['required' => true, 'type' => 'string', 'min' => 8],
                'gender'   => ['in' => ['male', 'female', 'neutral']],
            ],
        ],
        '/api/v1/auth/register' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'register',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'username'    => ['required' => true, 'type' => 'string', 'regex' => '/^[a-zA-Z0-9_]{3,30}$/'],
                'email'       => ['required' => true, 'type' => 'email'],
                'password'    => ['required' => true, 'type' => 'string', 'min' => 8],
                'gender'      => ['required' => true, 'in' => ['male', 'female', 'neutral']],
                'agree_terms' => ['required' => true, 'type' => 'bool'],
            ],
        ],
        '/api/v1/auth/forgot-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'forgotPassword',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'email' => ['required' => true, 'type' => 'email'],
            ],
        ],
        '/api/v1/auth/reset-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'resetPassword',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'token'    => ['required' => true, 'type' => 'string'],
                'password' => ['required' => true, 'type' => 'string', 'min' => 8],
            ],
        ],
        '/api/v1/auth/set-gender' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'setGender',
            // Gender gate girişten ÖNCE çalışır (auth/index.php → /select-gender)
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'gender' => ['required' => true, 'in' => ['male', 'female', 'neutral']],
            ],
        ],
        '/api/v1/auth/logout' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'logout',
            'public'      => false,
            'implemented' => true,
        ],
    ],
];
