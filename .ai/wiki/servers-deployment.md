---
title: "vault: servers — sunucu yapılandırma (3 dosya)"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [servers-deployment]
tags: [sunucu, altyapi, nginx, apache, iis, deploy]
---

# Sunucu Yapılandırması — 3 Platform

`raw/servers/` — Linux+Nginx, Windows+Apache, Windows+IIS kurulum ve yapılandırma dokümanları.

| Dosya | Ortam | Durum özeti |
|---|---|---|
| linux-nginx.md | Ubuntu 24.04 / Debian 12 · Nginx 1.26+ · PHP-FPM 8.4+ | 4/4 kalem **PLANNED** |
| windows-apache.md | Windows 10/11 · Apache 2.4+ (XAMPP) · PHP 8.4 TS | vhost · mod_rewrite · security headers **IMPLEMENTED**; proxy PLANNED |
| windows-iis.md | Windows Server 2022 / 11 · IIS 10 · PHP 8.4 NTS (FastCGI) | 4/4 kalem **PLANNED** |

Ortak sözleşmeler:

- Tekil giriş noktası: `public/index.php` (Front Controller) — Nginx `try_files`, Apache `RewriteRule`, IIS URL Rewrite ile aynı işi yapar.
- Download Service (Node.js) üç senaryoda da `127.0.0.1:3001`'e reverse proxy.
- Hassas dizinler kapatılır: `.ai`, `.env`, `.git`, `tests` (Apache `.htaccess` FilesMatch/MatchMatch · IIS `hiddenSegments`).
- CSP web sunucusundan **değil**, PHP middleware'den basılır (nonce zorunluluğu).
- Dosya sonlarındaki "Faz 3 Doğrulaması" bloğu mojibake satır karakterleri içerir — raw kopyada korundu.

## İlgili Sayfalar

- [[ecosystem]] - 7 servis / 10 panel / 11 subdomain haritası, sunucu dokümanlarının hedefi
- [[servers]] - kardeş ingest sayfası (raw/servers 3 dosya, 20:28)
- [[coremusic-platform]] — 6 modüllü tek repo, `public/` front controller zorunluluğu
- [[vault-workflow]] — Faz 3 (ecosystem, servers, subdomains, scripts) sürecinin kaynağı
