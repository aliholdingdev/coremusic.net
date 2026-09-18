---
type: server-config
category: infrastructure
title: "Sunucu YapÄ±landÄ±rmasÄ± â€” Windows + Apache"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu YapÄ±landÄ±rmasÄ± â€” Windows + Apache

**Ä°lgili Katmanlar:** [[architecture/k0-k5-software/k0-os-layer]] Â· [[architecture/k10-k15-application/k14-network]]
**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

Bu dokÃ¼man, CoreMusic projesinin XAMPP veya baÄŸÄ±msÄ±z Apache HTTP Server Ã¼zerinden Windows ortamÄ±nda geliÅŸtirme ve test amaÃ§lÄ± (veya belirli Ã¼retim senaryolarÄ±) nasÄ±l yapÄ±landÄ±rÄ±lacaÄŸÄ±nÄ±, `.htaccess` kurallarÄ±nÄ± ve FastCGI ayarlarÄ±nÄ± tanÄ±mlar.

## 2. Mimari Hedefler

- **GeliÅŸtirme OrtamÄ± UyumluluÄŸu:** Windows kullanÄ±cÄ±larÄ± iÃ§in kolay kurulum.
- **Routing:** Apache mod_rewrite kullanÄ±larak gelen tÃ¼m isteklerin `public/index.php`'ye yÃ¶nlendirilmesi.
- **GÃ¼venlik:** `.ai`, `.env`, `.git` gibi hassas dizinlere eriÅŸimin engellenmesi.

## 3. Kurulum ve Gereksinimler

- **OS:** Windows 10/11 veya Windows Server 2022
- **Apache:** 2.4+ (XAMPP Ã¶nerilir)
- **PHP:** 8.4+ (Thread Safe sÃ¼rÃ¼mÃ¼, mod_php veya FastCGI)
- **ModÃ¼ller:** `mod_rewrite`, `mod_ssl`, `mod_headers` aktif olmalÄ±dÄ±r.

## 4. Temel Apache YapÄ±landÄ±rmasÄ± (`httpd.conf`)

### 4.1 ModÃ¼l AktifleÅŸtirme

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule headers_module modules/mod_headers.so
LoadModule ssl_module modules/mod_ssl.so
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so
```

### 4.2 Virtual Host YapÄ±landÄ±rmasÄ± (`httpd-vhosts.conf`)

CoreMusic'i Ã§alÄ±ÅŸtÄ±rmak iÃ§in document root klasÃ¶rÃ¼nÃ¼n `public` olmasÄ± gerekir.

```apache
<VirtualHost *:80>
    ServerName coremusic.local
    ServerAlias music.coremusic.local admin.coremusic.local
    DocumentRoot "C:/www/coremusic.net/public"
    
    <Directory "C:/www/coremusic.net/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/coremusic-error.log"
    CustomLog "logs/coremusic-access.log" common
</VirtualHost>
```

## 5. Dizin YÃ¶nlendirmeleri (`.htaccess`)

TÃ¼m HTTP isteklerinin `public/index.php` dosyasÄ±na yÃ¶nlendirilmesi (Front Controller pattern) iÃ§in `public/.htaccess` dosyasÄ± ÅŸu ÅŸekilde olmalÄ±dÄ±r:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Trailing slash kaldÄ±rma (SEO ve Routing iÃ§in)
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)/$ /$1 [L,R=301]
    
    # Dosya ve Dizin kontrolleri
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 5.1 GÃ¼venlik KorumalarÄ± (KÃ¶k Dizin `.htaccess` Opsiyonel)

EÄŸer Document Root yanlÄ±ÅŸlÄ±kla ana dizine ayarlanÄ±rsa, hassas dosyalarÄ±n korunmasÄ± iÃ§in ana dizindeki koruma:

```apache
# Hassas klasÃ¶rleri eriÅŸime kapat
<MatchMatch "^\.ai|^\.git|^tests|^scripts">
    Require all denied
</MatchMatch>

# .env ve diÄŸer config dosyalarÄ±nÄ± kapat
<FilesMatch "^\.env|composer\.json|composer\.lock|README\.md|CLAUDE\.md|WORKFLOW\.md">
    Require all denied
</FilesMatch>
```

## 6. GÃ¼venlik BaÅŸlÄ±klarÄ± (Security Headers)

```apache
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

## 7. Subdomain Proxy (Download Service Node.js)

Apache kullanarak 3001 portundaki Node.js servisine reverse proxy yapmak:

```apache
<VirtualHost *:80>
    ServerName download.coremusic.local
    
    ProxyRequests Off
    ProxyPreserveHost On
    
    <Proxy *>
        Require all granted
    </Proxy>
    
    ProxyPass / http://127.0.0.1:3001/
    ProxyPassReverse / http://127.0.0.1:3001/
</VirtualHost>
```

## 8. IMPLEMENTED / PLANNED Matrisi

| KonfigÃ¼rasyon | Durum | AÃ§Ä±klama |
|---------------|-------|----------|
| Apache Virtual Host | **IMPLEMENTED** | XAMPP/Local geliÅŸtirmeler iÃ§in Ã§alÄ±ÅŸÄ±r durumda. |
| mod_rewrite KurallarÄ± | **IMPLEMENTED** | SPA ve PageRouter iÃ§in `index.php`'ye yÃ¶nlendirme devrede. |
| Ters Vekil (Node.js) | **PLANNED** | Ãœretimde veya yerel testte gerekli olduÄŸunda proxy_http_module kullanÄ±lacak. |
| Security Headers | **IMPLEMENTED** | Temel baÅŸlÄ±klar middleware seviyesinde PHP tarafÄ±ndan da saÄŸlanmaktadÄ±r. |

## 9. Sorun Giderme (Troubleshooting)

- **404 Not Found (Rotalar Ã‡alÄ±ÅŸmÄ±yor):** `mod_rewrite`'Ä±n aÃ§Ä±k olduÄŸundan ve `<Directory>` bloÄŸunda `AllowOverride All` yazÄ±ldÄ±ÄŸÄ±ndan emin olun.
- **500 Internal Server Error:** `.htaccess` dosyasÄ±ndaki geÃ§ersiz bir kural veya yÃ¼klenmemiÅŸ bir modÃ¼l (Ã¶rn. `mod_headers`) kaynaklÄ±dÄ±r. Apache `error.log`'unu kontrol edin.
- **Node.js Proxy HatasÄ± (503):** Apache Ã¼zerinden `download.coremusic.net` yÃ¶nlendirilmesinde `mod_proxy` ve `mod_proxy_http` modÃ¼llerinin aktif olduÄŸunu doÄŸrulayÄ±n.

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: GerÃƒÂ§ek Config KanÃ„Â±tÃ„Â±

YukarÃ„Â±daki konfigÃƒÂ¼rasyon bloklarÃ„Â±, engine.md Ã‚Â§12.2 Faz 3 kanÃ„Â±t zorunluluÃ„Å¸unu karÃ…Å¸Ã„Â±lamaktadÃ„Â±r. Gerekli router, rewrite ve security tanÃ„Â±mlamalarÃ„Â± mevcuttur.

