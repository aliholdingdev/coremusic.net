<?php declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\DomainConfig;

/**
 * CoreMusic Home — SPA Route Definitions
 *
 * Tüm URL'ler DomainConfig'den dinamik üretilir.
 * Hardcoded domain KULLANILMAZ.
 */

// Domain config'den URL'leri üret (fallback: fallback domain'ler)
$domainFile = dirname(__DIR__, 2) . '/shared/config/domain.php';
$domainConfig = new DomainConfig(file_exists($domainFile) ? $domainFile : null);

$scheme = 'http'; // Local dev — production'da DomainConfig.scheme kullanılır
$authDomain  = AuthRouteConfig::getAuthUrl($scheme, $domainConfig);
$homeDomain  = AuthRouteConfig::getHomeUrl($scheme, $domainConfig);
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';

return [
    // Auth redirects (home'dan auth'a yönlendirme)
    'login' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Giriş',
        meta: ['redirect_to' => "$authDomain/login?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),
    'register' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Kayıt',
        meta: ['redirect_to' => "$authDomain/register?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),
    'logout' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Çıkış',
        meta: ['redirect_to' => "$authDomain/logout?redirect=$homeDomain/"],
    ),
    'select-gender' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Cinsiyet Seçimi',
        meta: ['redirect_to' => "$authDomain/select-gender?redirect_uri=$callbackUrl"],
    ),
    'forgot-password' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Şifremi Unuttum',
        meta: ['redirect_to' => "$authDomain/forgot-password?redirect_uri=$homeDomain/login"],
    ),
    'reset-password' => new SpaRoute(
        page: 'redirect',
        requiresAuth: false,
        title: 'Şifre Sıfırlama',
        meta: ['redirect_to' => "$authDomain/reset-password?redirect_uri=$homeDomain/login"],
    ),

    // Auth callback (cross-domain session transfer)
    'auth/callback' => new SpaRoute(
        page: 'auth_callback',
        requiresAuth: false,
        title: 'Oturum Doğrulanıyor...',
    ),

    // Ana sayfa (auth required)
    'home' => new SpaRoute(
        page: 'home',
        requiresAuth: true,
        title: 'Ana Sayfa',
        cacheable: true,
        meta: ['ttlType' => 'user'],
    ),

    // Placeholder rotalar (yakında implemente edilecek)
    'kesfet' => new SpaRoute(
        page: 'home',
        requiresAuth: true,
        title: 'Keşfet',
        cacheable: true,
    ),
    'albumler' => new SpaRoute(
        page: 'home',
        requiresAuth: true,
        title: 'Albümler',
        cacheable: true,
    ),
    'ayarlar' => new SpaRoute(
        page: 'home',
        requiresAuth: true,
        title: 'Ayarlar',
    ),
];
