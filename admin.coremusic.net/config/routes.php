<?php declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;

/**
 * CoreMusic Admin — SPA Route Definitions (admin-local)
 *
 * Bu dosya admin.coremusic.net'e ÖZELDIR; shared/config/routes.php DEĞİŞTİRİLMEZ.
 */

return [
    // G2 (2026-10-19): /login ve /register = admin'in KENDİ Figma formlarının
    // takma adları — auth domain redirect YOK. 302 hedefi config/bootstrap.php'de
    // kernel ÖNCESİ uygulanır (AuthGuard shared AuthRouteConfig ile bu URI'leri
    // auth.coremusic.net'e atardı). Canonical kayıtlar: admin-login/admin-register

    // Auth callback (cross-domain session transfer — bootstrap'te kernel ÖNCESİ
    // işlenir: doğrulama + session oluşturup302 ile /admin-dashboard'a atar,
    // sayfa render EDİLMEZ). page: fallback değerdir, silinen stub dosyaya bakmaz.
    'auth/callback' => new SpaRoute(
        page: 'admin-dashboard',
        requiresAuth: false,
        title: 'Oturum Doğrulanıyor...',
        cacheable: false,
    ),

    // Admin sayfaları (auth required)
    // D2: adlandırma kuralı admin-{pagename} — ViewModeManager route prefix
    // '/admin*' eşleşmesi ve sayfa dosyaları (pages/admin-*.php) ile senkron.
    'admin-dashboard' => new SpaRoute(
        page: 'admin-dashboard',
        requiresAuth: true,
        title: 'Kontrol Paneli',
        cacheable: false,
    ),
    'admin-songs' => new SpaRoute(
        page: 'admin-songs',
        requiresAuth: true,
        title: 'Şarkılar',
        cacheable: false,
    ),
    'admin-song-add' => new SpaRoute(
        page: 'admin-song-add',
        requiresAuth: true,
        title: 'Şarkı Ekle',
        cacheable: false,
    ),
    'admin-playlist' => new SpaRoute(
        page: 'admin-playlist',
        requiresAuth: true,
        title: 'Playlistler',
        cacheable: false,
    ),
    'admin-settings' => new SpaRoute(
        page: 'admin-settings',
        requiresAuth: true,
        title: 'Ayarlar',
        cacheable: false,
    ),
];
