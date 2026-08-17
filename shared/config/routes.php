<?php declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;

$authDomain  = 'https://auth.coremusic.net';
$homeDomain  = 'https://home.coremusic.net';
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';

return [
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
    'auth/callback' => new SpaRoute(
        page: 'auth_callback',
        requiresAuth: false,
        title: 'Oturum Doğrulanıyor...',
    ),
    'home' => new SpaRoute(
        page: 'home',
        requiresAuth: true,
        title: 'Ana Sayfa',
        cacheable: true,
        meta: ['ttlType' => 'user'],
    ),
];
