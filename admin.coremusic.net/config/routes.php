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

    // Auth callback (cross-domain session transfer — bootstrap'te kernel öncesi işlenir)
    'auth/callback' => new SpaRoute(
        page: 'login',
        requiresAuth: false,
        title: 'Oturum Doğrulanıyor...',
    ),

    // Admin sayfaları (auth required)
    'dashboard' => new SpaRoute(
        page: 'dashboard',
        requiresAuth: true,
        title: 'Kontrol Paneli',
        cacheable: false,
    ),
    'songs' => new SpaRoute(
        page: 'songs',
        requiresAuth: true,
        title: 'Şarkılar',
        cacheable: false,
    ),
    'song-add' => new SpaRoute(
        page: 'song-add',
        requiresAuth: true,
        title: 'Şarkı Ekle',
        cacheable: false,
    ),
    'playlist' => new SpaRoute(
        page: 'playlist',
        requiresAuth: true,
        title: 'Playlistler',
        cacheable: false,
    ),
    'settings' => new SpaRoute(
        page: 'settings',
        requiresAuth: true,
        title: 'Ayarlar',
        cacheable: false,
    ),
];
