<?php declare(strict_types=1);

/**
 * CoreMusic Auth Service — OAuth Route Definitions
 *
 * ADR-088 compliant — Gender-based social OAuth routes.
 * auth.coremusic.net üzerinde çalışır.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */

use CoreMusic\PageRouter\SpaRoute;

return [
    // OAuth başlat (kullanıcı platform seçer)
    'oauth-connect' => new SpaRoute(
        page: 'oauth-connect',
        requiresAuth: true,
        title: 'Sosyal Medya Bağla',
        handler: 'oauth_post',
        meta: ['oauth_page' => true],
    ),

    // OAuth callback (platform'dan dönüş)
    'oauth-callback' => new SpaRoute(
        page: 'oauth-callback',
        requiresAuth: true,
        title: 'OAuth Callback',
        handler: 'oauth_callback',
        meta: ['oauth_page' => true, 'skip_csrf' => false],
    ),

    // Bağlantıları listele
    'oauth-connections' => new SpaRoute(
        page: 'oauth-connections',
        requiresAuth: true,
        title: 'Bağlı Hesaplar',
        handler: 'oauth_get',
        meta: ['oauth_page' => true],
    ),

    // Bağlantıyı kes
    'oauth-disconnect' => new SpaRoute(
        page: 'oauth-disconnect',
        requiresAuth: true,
        title: 'Bağlantıyı Kes',
        handler: 'oauth_post',
        meta: ['oauth_page' => true],
    ),
];
