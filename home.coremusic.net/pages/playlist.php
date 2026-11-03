<?php declare(strict_types=1);
/**
 * pages/playlist.php — Playlist / Çalma Listesi sayfası (v1.0.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_player.css, p-playlist.css)
 * SSOT     : Figma node 3155:19834 (Linux 1920 - Playlist Page)
 *            + node 2831:9443 (Linux 1024 - Playlist Page)
 *            + node 3175:13502 (4K T20 ölçek uyarlaması)
 * Route    : /playlist (SpaRoute: 'playlist')
 * ------------------------------------------------------------------
 */

if (isset($_SERVER['REQUEST_URI']) && str_contains((string)$_SERVER['REQUEST_URI'], '/playlist/video')) {
    $_GET['mode'] = 'video';
}

require __DIR__ . '/nowplaying.php';
