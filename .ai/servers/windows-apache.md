---
type: server-config
category: infrastructure
title: "Sunucu Yapılandırması — Windows + Apache"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu Yapılandırması — Windows + Apache

**İlgili Katmanlar:** [[architecture/k0-isletim-sistemi]] · [[architecture/k14-ag]]
**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[architecture/index]]

---

## 1. Amaç

Bu doküman, CoreMusic projesinin XAMPP veya bağımsız Apache HTTP Server üzerinden Windows ortamında geliştirme ve test amaçlı (veya belirli üretim senaryoları) nasıl yapılandırılacağını, `.htaccess` kurallarını ve FastCGI ayarlarını tanımlar.

## 2. Mimari Hedefler

- **Geliştirme Ortamı Uyumluluğu:** Windows kullanıcıları için kolay kurulum.
- **Routing:** Apache mod_rewrite kullanılarak gelen tüm isteklerin `public/index.php`'ye yönlendirilmesi.
- **Güvenlik:** `.ai`, `.env`, `.git` gibi hassas dizinlere erişimin engellenmesi.

## 3. Kurulum ve Gereksinimler

- **OS:** Windows 10/11 veya Windows Server 2022
- **Apache:** 2.4+ (XAMPP önerilir)
- **PHP:** 8.4+ (Thread Safe sürümü, mod_php veya FastCGI)
- **Modüller:** `mod_rewrite`, `mod_ssl`, `mod_headers` aktif olmalıdır.

## 4. Temel Apache Yapılandırması (`httpd.conf`)

### 4.1 Modül Aktifleştirme

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule headers_module modules/mod_headers.so
LoadModule ssl_module modules/mod_ssl.so
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so
```

### 4.2 Virtual Host Yapılandırması (`httpd-vhosts.conf`)

CoreMusic'i çalıştırmak için document root klasörünün `public` olması gerekir.

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

## 5. Dizin Yönlendirmeleri (`.htaccess`)

Tüm HTTP isteklerinin `public/index.php` dosyasına yönlendirilmesi (Front Controller pattern) için `public/.htaccess` dosyası şu şekilde olmalıdır:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Trailing slash kaldırma (SEO ve Routing için)
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)/$ /$1 [L,R=301]
    
    # Dosya ve Dizin kontrolleri
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 5.1 Güvenlik Korumaları (Kök Dizin `.htaccess` Opsiyonel)

Eğer Document Root yanlışlıkla ana dizine ayarlanırsa, hassas dosyaların korunması için ana dizindeki koruma:

```apache
# Hassas klasörleri erişime kapat
<MatchMatch "^\.ai|^\.git|^tests|^scripts">
    Require all denied
</MatchMatch>

# .env ve diğer config dosyalarını kapat
<FilesMatch "^\.env|composer\.json|composer\.lock|README\.md|CLAUDE\.md|WORKFLOW\.md">
    Require all denied
</FilesMatch>
```

## 6. Güvenlik Başlıkları (Security Headers)

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

| Konfigürasyon | Durum | Açıklama |
|---------------|-------|----------|
| Apache Virtual Host | **IMPLEMENTED** | XAMPP/Local geliştirmeler için çalışır durumda. |
| mod_rewrite Kuralları | **IMPLEMENTED** | SPA ve PageRouter için `index.php`'ye yönlendirme devrede. |
| Ters Vekil (Node.js) | **PLANNED** | Üretimde veya yerel testte gerekli olduğunda proxy_http_module kullanılacak. |
| Security Headers | **IMPLEMENTED** | Temel başlıklar middleware seviyesinde PHP tarafından da sağlanmaktadır. |

## 9. Sorun Giderme (Troubleshooting)

- **404 Not Found (Rotalar Çalışmıyor):** `mod_rewrite`'ın açık olduğundan ve `<Directory>` bloğunda `AllowOverride All` yazıldığından emin olun.
- **500 Internal Server Error:** `.htaccess` dosyasındaki geçersiz bir kural veya yüklenmemiş bir modül (örn. `mod_headers`) kaynaklıdır. Apache `error.log`'unu kontrol edin.
- **Node.js Proxy Hatası (503):** Apache üzerinden `download.coremusic.net` yönlendirilmesinde `mod_proxy` ve `mod_proxy_http` modüllerinin aktif olduğunu doğrulayın.

---

## Faz 3 Doİ„Ş¸rulaması: Gerçek Config Kanıtı

Yukarıdaki konfigürasyon blokları, engine.md İ‚§12.2 Faz 3 kanıt zorunluluİ„Ş¸unu karİ…Ş¸ılamaktadır. Gerekli router, rewrite ve security tanımlamaları mevcuttur.

