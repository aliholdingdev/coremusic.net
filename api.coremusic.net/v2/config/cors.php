<?php declare(strict_types=1);

/**
 * CoreMusic API v2 — CORS Config (v1 Security-owned allowlist'ini yeniden kullanır).
 *
 * v2 KENDİ origin listesini UYDIRMAZ: `api.coremusic.net/config/cors.php`
 * (Security Engineer sahipliğinde) salt-okunur olarak require edilir — tek
 * kaynak, drift yok. Değişiklik isteği Security handover'idir (AGENTS.md §3.3).
 */
return require dirname(__DIR__, 2) . '/config/cors.php';
