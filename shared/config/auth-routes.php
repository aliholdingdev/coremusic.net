<?php declare(strict_types=1);

/**
 * CoreMusic Auth Service — SPA Route Definitions
 *
 * Shared SPA Router tarafından kullanılır.
 * POST handler'lar AuthPostHandler tarafından işlenir.
 */

use CoreMusic\PageRouter\SpaRoute;

return [
    'login' => new SpaRoute(
        page: 'login',
        requiresAuth: false,
        title: 'Giriş',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'register' => new SpaRoute(
        page: 'register',
        requiresAuth: false,
        title: 'Kayıt',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'select-gender' => new SpaRoute(
        page: 'select-gender',
        requiresAuth: false,
        title: 'Cinsiyet Seçimi',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'forgot-password' => new SpaRoute(
        page: 'forgot-password',
        requiresAuth: false,
        title: 'Şifremi Unuttum',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'reset-password' => new SpaRoute(
        page: 'reset-password',
        requiresAuth: false,
        title: 'Şifre Sıfırlama',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'logout' => new SpaRoute(
        page: 'logout',
        requiresAuth: false,
        title: 'Çıkış',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
    'set-gender' => new SpaRoute(
        page: 'set-gender',
        requiresAuth: false,
        title: 'Cinsiyet Ayarla',
        handler: 'auth_post',
        meta: ['auth_page' => true],
    ),
];
