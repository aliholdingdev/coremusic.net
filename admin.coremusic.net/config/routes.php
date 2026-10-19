<?php declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\DomainConfig;

/**
 * CoreMusic Admin — SPA Route Definitions (admin-local)
 *
 * Bu dosya admin.coremusic.net'e ÖZELDIR; shared/config/routes.php DEĞİŞTİRİLMEZ.
 * Tüm URL'ler DomainConfig'den dinamik üretilir. Hardcoded domain KULLANILMAZ.
 */

// Domain config'den URL'leri üret (fallback: fallback domain'ler)
$domainFile = dirname(__DIR__, 2) . '/shared/config/domain.php';
$domainConfig = new DomainConfig(file_exists($domainFile) ? $domainFile : null);

$scheme = 'http'; // Local dev — production'da DomainConfig.scheme kullanılır
$authDomain  = AuthRouteConfig::getAuthUrl($scheme, $domainConfig);
$adminHost   = $domainConfig->getSubdomainHost('admin') ?? 'admin.coremusic.net';
$adminDomain = $scheme . '://' . $adminHost;
$clientId    = 'coremusic-web';
$callbackUrl = $adminDomain . '/auth/callback';

return [
    // Auth redirects (admin'dan auth'a yönlendirme — callback admin'e döner)
    'login' => new SpaRoute(
        page: 'login',
        requiresAuth: false,
        title: 'Yönetici Girişi',
        meta: ['redirect_to' => "$authDomain/login?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),
    'register' => new SpaRoute(
        page: 'register',
        requiresAuth: false,
        title: 'Kayıt',
        meta: ['redirect_to' => "$authDomain/register?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),

    // Görev D: admin'in KENDİ formları (figma) — yönlendirme DEĞİL; form
    // fetch ile doğrudan auth API'sine (POST /v1/auth/login|register) gider.
    // CSP form-action 'self' nedeniyle çapraz-origin native POST engellenir.
    'admin-login' => new SpaRoute(
        page: 'admin-login',
        requiresAuth: false,
        title: 'Yönetici Girişi',
        cacheable: false,
    ),
    'admin-register' => new SpaRoute(
        page: 'admin-register',
        requiresAuth: false,
        title: 'Yönetici Kaydı',
        cacheable: false,
    ),

    // Auth callback (cross-domain session transfer — bootstrap'te kernel öncesi işlenir)
    'auth/callback' => new SpaRoute(
        page: 'login',
        requiresAuth: false,
        title: 'Oturum Doğrulanıyor...',
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
