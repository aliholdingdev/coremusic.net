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

    // --- Admin Auth Rotaları (Light Theme SSOT) ---
    'admin/login' => new SpaRoute(
        page: 'admin/login',
        requiresAuth: false,
        title: 'Yönetici Girişi',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/register' => new SpaRoute(
        page: 'admin/register',
        requiresAuth: false,
        title: 'Yönetici Kaydı',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/forgot-password' => new SpaRoute(
        page: 'admin/forgot-password',
        requiresAuth: false,
        title: 'Yönetici Şifre Sıfırlama',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/reset-password' => new SpaRoute(
        page: 'admin/reset-password',
        requiresAuth: false,
        title: 'Yönetici Yeni Şifre',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/select-gender' => new SpaRoute(
        page: 'admin/select-gender',
        requiresAuth: false,
        title: 'Yönetici Profil Seçimi',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/set-gender' => new SpaRoute(
        page: 'admin/set-gender',
        requiresAuth: false,
        title: 'Yönetici Profil Ayarla',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
    'admin/logout' => new SpaRoute(
        page: 'admin/logout',
        requiresAuth: false,
        title: 'Yönetici Çıkış',
        handler: 'auth_post',
        meta: ['auth_page' => true, 'admin' => true, 'theme' => 'light'],
    ),
];
