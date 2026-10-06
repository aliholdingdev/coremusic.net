---
title: "Vault Kaynağı: raw/servers (sunucu yapılandırma — 3 platform)"
type: kaynak
raw_path: raw/servers/
created: 2026-10-06
updated: 2026-10-06
sources: [servers-deployment]
tags: [sunucu, altyapi, nginx, apache, iis, deploy]
---

# Kaynak Özeti — servers/ kök (3 dosya)

## Genel Özet

`raw/servers/` kökündeki 3 sunucu yapılandırma dokümanı (eski vault `servers/` alt ağacından birebir taşındı): Linux + Nginx, Windows + Apache, Windows + IIS. Üçü de aynı mimari sözleşmeyi paylaşıyor: tekil giriş noktası `public/index.php` (Front Controller), Download Service için 3001 portuna reverse proxy, güvenlik başlıkları ve IMPLEMENTED/PLANNED matrisi. Nginx ve IIS dokümanları tüm kalemleri **PLANNED**; Apache dokümanı vhost/mod_rewrite/security headers'ı **IMPLEMENTED** yazıyor.

## Ana Fikirler

1. `linux-nginx.md` (177 satır) — Ubuntu 24.04 LTS / Debian 12 + Nginx 1.26+ + PHP-FPM 8.4+; event-driven worker ayarları (`worker_connections 8192`, `epoll`), FastCGI buffer optimizasyonu, TLS 1.3 + HSTS, gzip/brotli ve statik dosya cache'i. `music/auth/admin.coremusic.net` PHP-FPM, `download.coremusic.net` → `127.0.0.1:3001`.
2. `windows-apache.md` (154 satır) — Windows 10/11 veya Server 2022 + Apache 2.4+ (XAMPP) + PHP 8.4 Thread Safe; `httpd-vhosts.conf` DocumentRoot `C:/www/coremusic.net/public`, `.htaccess` mod_rewrite Front Controller + kök dizinde `.ai/.git/tests/scripts` ve `.env/composer.json` reddi.
3. `windows-iis.md` (136 satır) — Windows Server 2022 / Windows 11 + IIS 10 + URL Rewrite 2.1 + FastCGI (PHP 8.4 **NTS**); `web.config` içinde trailing-slash kuralı + Front Controller kuralı + `hiddenSegments` (`.ai`, `.env`, `.git`, `tests`) + `customHeaders`; Download Service için ARR reverse proxy kuralı.
4. Üç dokümanda da CSP notu ortak: CSP dinamik `nonce` istediği için web sunucusu seviyesinde DEĞİL, PHP middleware seviyesinde basılır (Nginx dokümanı §5).
5. Üç dokümanın sonunda da aynı "Faz 3 Doğrulaması: Gerçek Config Kanıtı" kapanış bloğu var (engine §12.2'ye atıf) — bu bloklar kaynak dosyalarda **mojibake** (bozuk UTF-8) satır karakterleri içeriyor; raw kopyada olduğu gibi bırakıldı.

## Önemli Alıntılar/Veriler

- IMPLEMENTED / PLANNED dağılımı: **Apache** → Virtual Host, mod_rewrite, Security Headers IMPLEMENTED · Ters vekil (Node.js) PLANNED. **Nginx** → 4/4 kalem PLANNED (reverse proxy, PHP-FPM socket, TLS 1.3, asset cache). **IIS** → 4/4 kalem PLANNED (FastCGI, URL Rewrite, ARR, güvenlik başlıkları).
- Download Service her üç senaryoda da `127.0.0.1:3001` (Node.js) olarak proxy'lenir.
- Dosya frontmatter'ları: `type: server-config`, `category: infrastructure`, `date: 2026-09-19`, `updated: 2026-09-29`, `version: 1.0.1`, `status: active`.
- Okuma kapsamı: 3 dosyanın tamamı baştan sona okundu (2026-10-06, 177+154+136 = 467 satır). Tam metin `raw/servers/` altında.

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[servers-deployment]] (yeni)
