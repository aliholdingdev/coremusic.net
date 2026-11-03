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
 *                       (404 DEĞIL); Faz 1b'de 6 POST ucu true yapıldı
 *   validation        : RequestValidationMiddleware kural seti (422 + error.fields)
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
 *   cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control
 *
 * Faz 1b sözleşmesi (Faz 3a UI — DEĞİŞTİRİLEMEZ):
 *   POST /v1/auth/login|register|set-gender|forgot-password|reset-password|logout
 *   GET  /v1/auth/me
 *
 * GET'ler POST-only uçlardır → 405 + Allow: POST, OPTIONS (yalnızca GET /v1/auth/me 200/401).
 *
 * NOT: Versiyon prefix'i `{version}` placeholder olarak tanımlanır.
 * index.php runtime'da `{version}` → `v1` veya `v2` ile değiştirir.
 */
return [
    'GET' => [
        // Faz 2 hardcoded GET prefix (koleksiyon) kayıtları — action'ı OLMAYAN uçlar.
        // implemented=false → RouteTable::match 405 + Allow (RFC 9110 §15.5.6);
        // implemented=true kalırsa Gateway::invokeHandler action'sız route için
        // 200 "Handler not implemented yet" placeholder döndürüyordu (F2 bulgusu).
        // Not: '/api/{version}/playlist' anahtarı aşağıdaki exact GET kaydıyla
        // ÇAKIŞIR — PHP'de son anahtar kazanır, bu satır fiilen ölüdür (action: index).
        '/api/{version}/auth'     => ['service' => 'auth',     'handler' => 'authController',     'implemented' => false],
        '/api/{version}/user'     => ['service' => 'user',     'handler' => 'userController',     'implemented' => false],
        '/api/{version}/music'    => ['service' => 'music',    'handler' => 'musicController',    'implemented' => false],
        '/api/{version}/playlist' => ['service' => 'playlist', 'handler' => 'playlistController', 'implemented' => false],

        // Public auth uçları — exact kayıt, prefix ile çakışmasın
        // Bu 4 uç POST-only'dir: GET implemented=false → 405 + Allow: POST, OPTIONS
        '/api/{version}/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/{version}/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/{version}/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/{version}/auth/reset-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],

        // Faz 1b: oturumdaki kullanıcıyı döndürür (200 {data:{user}} | 401)
        '/api/{version}/auth/me' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'me',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Faz 4: müzik uçları (MusicService bağlı — implemented:true) ───
        '/api/{version}/music/tracks' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'tracks',
            'public'      => true,
            'implemented' => true,
            'cacheable'   => true,
            'cacheTtl'    => 60,
        ],
        '/api/{version}/music/search' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'search',
            'public'      => true,
            'implemented' => true,
            'cacheable'   => true,
            'cacheTtl'    => 60,
        ],
        '/api/{version}/music/track' => [
            'service'     => 'music',
            'handler'     => 'musicController',
            'action'      => 'show',
            'public'      => true,
            'implemented' => true,
            'cacheable'   => true,
            'cacheTtl'    => 60,
            // F16: id yoksa controller sessizce 404 döndürüyordu → 422 + error.fields
            // (GET olduğu için kural RequestValidationMiddleware'de QUERY'den okunur).
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],

        // ─── Faz 4: kullanıcı listeleri (exact — prefix fallback korunur) ───
        '/api/{version}/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'index',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Adım 07: playlist detay (RouteTable exact-path → id query'de) ───
        '/api/{version}/playlist/detail' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'show',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],

        // ─── Faz 4: kullanıcı profili (UserService bağlı) ───
        '/api/{version}/user/profile' => [
            'service'     => 'user',
            'handler'     => 'userController',
            'action'      => 'profile',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Backend Yeniden Yapılandırma Uçları ───
        '/api/{version}/music/recent' => [
            'service'     => 'music',
            'handler'     => 'historyController',
            'action'      => 'recent',
            'public'      => false,
            'implemented' => true,
        ],
        '/api/{version}/music/history' => [
            'service'     => 'music',
            'handler'     => 'historyController',
            'action'      => 'list',
            'public'      => false,
            'implemented' => true,
        ],
        '/api/{version}/user/header-data' => [
            'service'     => 'user',
            'handler'     => 'userHeaderController',
            'action'      => 'headerData',
            'public'      => true,
            'implemented' => true,
            'cacheable'   => true,
            'cacheTtl'    => 60,
        ],

        // ─── F8: ses/video stream kaydı (contract-first) ───
        // Byte-path index.php §1.1'dedir: `#^(?:/api/v1)?/stream/([a-f0-9]{32})$#`
        // isteği GATEWAY'DEN ÖNCE yakalar (RateLimit → auth → StreamController) ve
        // sırayı bu kayıt DEĞİŞTİRMEZ. RouteTable exact-match yaptığı için gerçek
        // id bu satıra hiç düşmez; kayıt sözleşmenin tek noktadan ilanıdır
        // (openapi.yaml /stream ile hizalı).
        '/api/{version}/stream/{id}' => [
            'service'     => 'music',
            'handler'     => 'streamController',
            'action'      => 'stream',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Billing: abonelik/plan özeti + faturalar (kullanıcının KENDİ verisi) ───
        // F13: requiredRole/requiredPermission YOK — üye/own-data ucu; RBAC alanı
        // yalnız admin/privilege uçlarında doldurulur (routes.php'de öyle uç yok —
        // RouteConfigTest::testNoRouteDeclaresRbacRoleOrPermissionFields kilitler).
        // Zarf: framework {data,meta} (ApiResponse::ok / ApiResponse::paginated) —
        // openapi.yaml BillingPlanResponse / BillingInvoicesResponse ile hizalı.
        '/api/{version}/billing/plan' => [
            'service'     => 'billing',
            'handler'     => 'billingController',
            'action'      => 'plan',
            'public'      => false,
            'implemented' => true,
        ],
        '/api/{version}/billing/invoices' => [
            'service'     => 'billing',
            'handler'     => 'billingController',
            'action'      => 'invoices',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                // Yalnız gateway TİP kontrolü (non-int → 422). Aralık (page≥1,
                // pageSize 1..100) BillingController'da: Validator min/max
                // string değerde UZUNLUK ölçer → route'a min/max konursa
                // page=0'ı geçirirdi (bkz. BillingControllerTest 422 testleri).
                'page'     => ['type' => 'int'],
                'pageSize' => ['type' => 'int'],
            ],
        ],
    ],

    'POST' => [
        // Faz 1b: 6 uç implemented=true + AuthController action'ı + doğrulama kuralları
        '/api/{version}/auth/login' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'login',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                // F3: identity = e-posta VEYA kullanıcı adı (AuthController::login
                // $body['email'] ?? ['identity'] ?? ['username']). email required
                // OLUNCA username-only login 422 ile gateway'de reddediliyordu →
                // zorunluluk controller/DTO katmanına bırakıldı (route'da opsiyonel).
                // Boş identity AuthService::login'de ValidationException::emptyFields()
                // → 422 (identity-OR kaybı YOK).
                'email'    => ['type' => 'string'],
                'password' => ['required' => true, 'type' => 'string', 'min' => 8],
                'gender'   => ['in' => ['male', 'female', 'neutral']],
            ],
        ],
        '/api/{version}/auth/register' => [
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
        '/api/{version}/auth/forgot-password' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'forgotPassword',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                'email' => ['required' => true, 'type' => 'email'],
            ],
        ],
        '/api/{version}/auth/reset-password' => [
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
        '/api/{version}/auth/set-gender' => [
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
        '/api/{version}/auth/logout' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'logout',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Faz 4: oynatma listesi (PlaylistService bağlı — implemented:true) ───
        '/api/{version}/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'create',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'name' => ['required' => true, 'type' => 'string', 'min' => 1, 'max' => 100],
            ],
        ],
        '/api/{version}/playlist/tracks' => [
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

        '/api/{version}/music/history' => [
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

    // ─── Faz 4: profil güncelleme (UserService bağlı — implemented:true) ───
    'PUT' => [
        '/api/{version}/user/profile' => [
            'service'     => 'user',
            'handler'     => 'userController',
            'action'      => 'updateProfile',
            'public'      => false,
            'implemented' => true,
        ],

        // ─── Adım 07: playlist güncelleme (meta) + sıralama ───
        '/api/{version}/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'update',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],
        '/api/{version}/playlist/order' => [
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

    // ─── Adım 07: playlist silme (soft) + şarkı çıkarma ───
    // DELETE body methodu DEĞİL (RequestValidationMiddleware BODY_METHODS =
    // POST/PUT/PATCH) → doğrulama query'den okunur: ?id=... / ?playlist_id=...
    'DELETE' => [
        '/api/{version}/playlist' => [
            'service'     => 'playlist',
            'handler'     => 'playlistController',
            'action'      => 'delete',
            'public'      => false,
            'implemented' => true,
            'validation'  => [
                'id' => ['required' => true, 'type' => 'string'],
            ],
        ],
        '/api/{version}/playlist/tracks' => [
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