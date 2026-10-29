<?php declare(strict_types=1);

/**
 * CoreMusic API v2 — Route Kayıtları (ADR-084 Contract First · ADR-020 §1.1-B.1)
 *
 * v1 (../config/routes.php) tablosunun /api/v2/ ön ekli AYNASI. v1 dosyasına
 * 0 satır dokunulur; iki tablo bağımsız evrilir, alan semantiği ADR-084 ile
 * sabittir.
 *
 * Tablo: `shared/src/Api/Routing/RouteTable`.
 *
 * Alanlar:
 *   service / handler : çözümleme anahtarı (V2Container → ApiContainer dispatch)
 *   action            : Controller::handle() dispatch anahtarı
 *   public            : auth'sız erişim (Authentication/Authorization bunu okur)
 *   implemented       : false → route TANIMLI ama controller yok → 405 + Allow
 *                       (404 DEĞİL)
 *   validation        : RequestValidationMiddleware kural seti (422 + error.fields)
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
 *   cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control
 *
 * v2 sözleşmesi (ADR-084 zarf): v1 ile AYNI uç listesi + aynı doğrulama;
 * v1'den kasıtlı fark zarf alanındadır ({success:true} YOK) — route
 * tablosunda tesirsizdir.
 *
 * Uç sözleşmesi (v1 aynası — DEĞİŞTİRİLEMEZ):
 *   POST /v2/auth/login|register|set-gender|forgot-password|reset-password|logout
 *   GET  /v2/auth/me
 *
 * GET'ler POST-only uçlardır → 405 + Allow: POST, OPTIONS (yalnızca GET /v2/auth/me 200/401).
 */
return [
    'GET' => [
        // Prefix kayıtları — v1 aynası (davranış değişmez)
        '/api/v2/auth'     => ['service' => 'auth',     'handler' => 'authController'],
        '/api/v2/user'     => ['service' => 'user',     'handler' => 'userController'],
        '/api/v2/music'    => ['service' => 'music',    'handler' => 'musicController'],
        '/api/v2/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],

        // Public auth uçları — exact kayıt, prefix `/api/v2/auth` ile çakışmasın
        // Bu 4 uç POST-only'dir: GET implemented=false → 405 + Allow: POST, OPTIONS
        '/api/v2/auth/login'           => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v2/auth/register'        => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v2/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v2/auth/reset-password'  => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],

        // Oturumdaki kullanıcıyı döndürür (200 {data:{user}} | 401)
        '/api/v2/auth/me' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'me',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Müzik uçları (MusicService bağlı — implemented:true) ───
        '/api/v2/music/tracks' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'tracks',
            'public'      => true,
            'implemented' => true,
        ],
        '/api/v2/music/search' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'search',
            'public'      => true,
            'implemented' => true,
        ],
        '/api/v2/music/track' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'show',
            'public'      => true,
            'implemented' => true,
        ],

        // ─── Kullanıcı listeleri (exact — prefix fallback korunur) ───
        '/api/v2/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'index',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Playlist detay (RouteTable exact-path → id query'de) ───
        '/api/v2/playlist/detail' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'show',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],

        // ─── Kullanıcı profili (UserService bağlı) ───
        '/api/v2/user/profile' => [
            'service'     => 'user',
            'handler'     => 'userController',
            'action'      => 'profile',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Backend Yeniden Yapılandırma Uçları ───
        '/api/v2/music/recent' => [
            'service'     => 'music',
            'handler'     => 'historyController',
            'action'      => 'recent',
            'public'      => false,
            'implemented' => true,
        ],
        '/api/v2/music/history' => [
            'service'     => 'music',
            'handler'     => 'historyController',
            'action'      => 'list',
            'public'      => false,
            'implemented' => true,
        ],
        '/api/v2/user/header-data' => [
            'service'     => 'user',
            'handler'     => 'userHeaderController',
            'action'      => 'headerData',
            'public'      => true,
            'implemented' => true,
        ],
    ],

    'POST' => [
        // Auth uçları: action + doğrulama kuralları (v1 aynası)
        '/api/v2/auth/login' => [
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
        '/api/v2/auth/register' => [
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
        '/api/v2/auth/forgot-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'forgotPassword',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'email' => ['required' => true, 'type' => 'email'],
            ],
        ],
        '/api/v2/auth/reset-password' => [
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
        '/api/v2/auth/set-gender' => [
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
        '/api/v2/auth/logout' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'logout',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Oynatma listesi (PlaylistService bağlı — implemented:true) ───
        '/api/v2/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'create',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'name' => ['required' => true, 'type' => 'string', 'min' => 1, 'max' => 100],
            ],
        ],
        '/api/v2/playlist/tracks' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'addTrack',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'playlist_id' => ['required' => true, 'type' => 'string'],
                'track_id'    => ['required' => true, 'type' => 'string'],
            ],
        ],

        '/api/v2/music/history' => [
            'service'     => 'music',
            'handler'     => 'historyController',
            'action'      => 'record',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'music_id' => ['required' => true, 'type' => 'string'],
            ],
        ],
    ],

    // ─── Profil güncelleme (UserService bağlı — implemented:true) ───
    'PUT' => [
        '/api/v2/user/profile' => [
            'service'     => 'user',
            'handler'     => 'userController',
            'action'      => 'updateProfile',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Playlist güncelleme (meta) + sıralama ───
        '/api/v2/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'update',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],
        '/api/v2/playlist/order' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'reorder',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'playlist_id' => ['required' => true, 'type' => 'string'],
                'track_ids'   => ['required' => true, 'type' => 'array'],
            ],
        ],
    ],

    // ─── Playlist silme (soft) + şarkı çıkarma ───
    // DELETE body methodu DEĞİL (RequestValidationMiddleware BODY_METHODS =
    // POST/PUT/PATCH) → doğrulama query'den okunur: ?id=... / ?playlist_id=...
    'DELETE' => [
        '/api/v2/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'delete',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],
        '/api/v2/playlist/tracks' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'removeTrack',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'playlist_id' => ['required' => true, 'type' => 'string'],
                'track_id'    => ['required' => true, 'type' => 'string'],
            ],
        ],
    ],
];
