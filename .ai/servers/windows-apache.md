---
title: "windows-apache"
type: reference
folder: ".ai/servers"
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team - Human Mode - Truth Mode
---

# Windows Apache (XAMPP) Yapılandırması

**Ortam:** Geliştirme (Local)
**Port:** 81 (HTTP)
**PHP:** Dahili PHP 8.x

## 1. XAMPP Kurulumu

### Gerekli Bileşenler
- Apache 2.4+
- PHP 8.3+ (XAMPP dahili)
- MySQL 9.0+ (XAMPP dahili)
- phpMyAdmin (opsiyonel)

### Kurulum Dizini
```
C:\xampp\
├── apache\                 # Apache sunucusu
│   └── conf\httpd.conf     # Ana yapılandırma
├── php\                    # PHPruntime
│   └── php.ini             # PHP yapılandırması
├── mysql\                  # MySQL sunucusu
└── htdos\                  # Varsayılan DocumentRoot
```

## 2. Apache httpd.conf Yapılandırması

### Port Değişikliği
```apache
# Varsayılan 80 yerine 81 kullan
Listen 81
ServerName localhost:81
```

### VirtualHost Tanımı
```apache
# Ana domain
<VirtualHost *:81>
    DocumentRoot "C:/www/coremusic.net/public"
    ServerName coremusic.net
    ServerAlias www.coremusic.net
    
    <Directory "C:/www/coremusic.net/public">
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
    
    # PHP-FPM (eğer ayrı çalışıyorsa)
    <FilesMatch \.php$>
        SetHandler "proxy:fcgi://127.0.0.1:9001"
    </FilesMatch>
</VirtualHost>

# Auth subdomain
<VirtualHost *:81>
    DocumentRoot "C:/www/coremusic.net/public"
    ServerName auth.coremusic.net
    
    <Directory "C:/www/coremusic.net/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Home subdomain
<VirtualHost *:81>
    DocumentRoot "C:/www/coremusic.net/public"
    ServerName home.coremusic.net
    
    <Directory "C:/www/coremusic.net/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# API subdomain
<VirtualHost *:81>
    DocumentRoot "C:/www/coremusic.net/public"
    ServerName api.coremusic.net
    
    <Directory "C:/www/coremusic.net/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 3. .htaccess (Apache URL Rewrite)

```apache
# public/.htaccess

RewriteEngine On
RewriteBase /

# Env dosyasını koru
RewriteRule ^\.env$ - [F,L]

# Vendor dizinini koru
RewriteRule ^vendor/ - [F,L]

# Public dizinini koru
RewriteRule ^packages/ - [F,L]

# Tüm istekleri index.php'ye yönlendir
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 4. PHP Yapılandırması (php.ini)

```ini
; Hata Ayıklama
display_errors = On
error_reporting = E_ALL
log_errors = On
error_log = "C:\xampp\php\logs\php_errors.log"

; Bellek
memory_limit = 256M

; Çalışma Zamanı
max_execution_time = 30
max_input_time = 60

; Yükleme
upload_max_filesize = 50M
post_max_size = 50M

; Oturum
session.save_handler = files
session.save_path = "C:\xampp\tmp"

; OPcache (geliştirme için kapalı)
opcache.enable = 0

; Xdebug (varsa)
xdebug.mode = debug
xdebug.start_with_request = yes
xdebug.client_port = 9003
```

## 5. Hosts Dosyası

```
# C:\Windows\System32\drivers\etc\hosts
127.0.0.1    coremusic.net
127.0.0.1    www.coremusic.net
127.0.0.1    auth.coremusic.net
127.0.0.1    home.coremusic.net
127.0.0.1    pro.coremusic.net
127.0.0.1    studio.coremusic.net
127.0.0.1    car.coremusic.net
127.0.0.1    admin.coremusic.net
127.0.0.1    api.coremusic.net
127.0.0.1    media.coremusic.net
127.0.0.1    download.coremusic.net
127.0.0.1    music.coremusic.net
```

## 6. Erişim Noktaları

| URL | Port | Açıklama |
|-----|------|----------|
| http://coremusic.net:81 | 81 | Ana domain |
| http://auth.coremusic.net:81 | 81 | Auth servisi |
| http://home.coremusic.net:81 | 81 | Home panel |
| http://api.coremusic.net:81 | 81 | API Gateway |
| http://localhost:81 | 81 | Doğrudan erişim |

## 7. Apache Servis Yönetimi

```powershell
# Apache'i başlat
& "C:\xampp\apache\bin\httpd.exe" -k start

# Apache'i durdur
& "C:\xampp\apache\bin\httpd.exe" -k stop

# Apache'i yeniden başlat
& "C:\xampp\apache\bin\httpd.exe" -k restart

# Apache durumunu kontrol et
Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
```

## 8. Sorun Giderme

### Port Çakışması
```powershell
# 81 portunu kullanan süreci bul
netstat -ano | findstr :81

# Süreci sonlandır (PID ile)
taskkill /PID <PID> /F
```

### Apache Başlatılamıyor
```powershell
# Hata logunu kontrol et
Get-Content "C:\xampp\apache\logs\error.log" -Tail 50

# Yapılandırma testi
& "C:\xampp\apache\bin\httpd.exe" -t
```

### PHP Hataları
```powershell
# PHP hata logunu kontrol et
Get-Content "C:\xampp\php\logs\php_errors.log" -Tail 50
```
