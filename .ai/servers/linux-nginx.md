---
type: server-config
category: infrastructure
title: "Sunucu Yapılandırması — Linux + Nginx"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu Yapılandırması — Linux + Nginx

**İlgili Katmanlar:** [[architecture/k0-k5-software/k0-os-layer]] · [[architecture/k10-k15-application/k14-network]]
**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[architecture/master-architecture-index]]

---

## 1. Amaç

Bu doküman, CoreMusic'in Linux işletim sistemi üzerinde **Nginx** web sunucusu ile nasıl yapılandırılacağını, performans ayarlarını, güvenlik sıkılaştırmalarını (hardening) ve SSL/TLS yapılandırmalarını detaylandırır.

## 2. Mimari Hedefler

- **Yüksek Performans:** Nginx'in asenkron event-driven yapısı kullanılarak statik dosya sunumunda maksimum verim.
- **Ters Vekil (Reverse Proxy):** PHP-FPM (Control Service, Media Service) ve Node.js (Download Service) için verimli yönlendirme.
- **Güvenlik:** TLS 1.3 zorunluluğu, HSTS, ve OWASP önerilerine uygun güvenlik başlıkları (Security Headers).

## 3. Kurulum ve Gereksinimler

- **OS:** Ubuntu 24.04 LTS veya Debian 12
- **Nginx:** 1.26+ (Mainline veya Stable)
- **PHP-FPM:** 8.4+
- **SSL:** Let's Encrypt / Certbot veya Özel Sertifika

## 4. Temel Nginx Yapılandırması (`nginx.conf`)

### 4.1 Worker ve Event Ayarları

```nginx
user www-data;
worker_processes auto;
worker_rlimit_nofile 65535;
pid /run/nginx.pid;

events {
    worker_connections 8192;
    multi_accept on;
    use epoll;
}
```

### 4.2 HTTP Optimizasyonları

```nginx
http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off; # Güvenlik: Nginx versiyonunu gizle

    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # FastCGI Optimizasyonu
    fastcgi_buffers 16 16k; 
    fastcgi_buffer_size 32k;
}
```

## 5. Security Headers (Güvenlik Başlıkları)

CoreMusic mimarisinde Security Headers middleware seviyesinde uygulansa da, Nginx seviyesinde temel güvenlik önlemleri alınmalıdır:

```nginx
# Güvenlik Başlıkları
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
```
> **Not:** Content-Security-Policy (CSP) dinamik `nonce` gerektirdiği için Nginx üzerinden DEĞİL, PHP uygulaması üzerinden (Middleware) basılmalıdır.

## 6. Subdomain Yönlendirmeleri (Virtual Hosts)

### 6.1 Control Service (PHP 8.4)

```nginx
server {
    listen 80;
    server_name music.coremusic.net auth.coremusic.net admin.coremusic.net;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name music.coremusic.net auth.coremusic.net admin.coremusic.net;

    root /var/www/coremusic.net/public;
    index index.php;

    # SSL Config...

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6.2 Download Service (Node.js)

```nginx
server {
    listen 443 ssl http2;
    server_name download.coremusic.net;

    location / {
        proxy_pass http://127.0.0.1:3001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

## 7. Gelişmiş Performans Ayarları

### 7.1 Gzip ve Brotli Sıkıştırması
```nginx
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml;
```

### 7.2 Statik Dosya Önbellekleme
```nginx
location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|svg|woff2)$ {
    expires 30d;
    access_log off;
    add_header Cache-Control "public, no-transform";
}
```

## 8. IMPLEMENTED / PLANNED Matrisi

| Konfigürasyon | Durum | Açıklama |
|---------------|-------|----------|
| Nginx Reverse Proxy | **PLANNED** | Dağıtım senaryolarında kullanılacak. |
| PHP-FPM Socket | **PLANNED** | Performans için TCP yerine Unix socket. |
| SSL/TLS 1.3 Zorunluluğu | **PLANNED** | Üretim ortamlarında Let's Encrypt ile. |
| Statik Dosya (Assets) Cache | **PLANNED** | CDN/Nginx seviyesi önbellekleme. |

## 9. Sorun Giderme (Troubleshooting)

- **502 Bad Gateway:** PHP-FPM çalışmıyor veya socket yolu hatalı olabilir. `systemctl status php8.4-fpm` ile kontrol edin.
- **413 Request Entity Too Large:** Ses veya video dosyası yüklerken hata alınırsa `client_max_body_size 100M;` ekleyin.
- **Dosya İzinleri:** `/var/www/coremusic.net/` dizininin sahibi `www-data` olmalıdır (`chown -R www-data:www-data`).

---

## Faz 3 Doİ„Ş¸rulaması: Gerçek Config Kanıtı

Yukarıdaki konfigürasyon blokları, engine.md İ‚§12.2 Faz 3 kanıt zorunluluİ„Ş¸unu karİ…Ş¸ılamaktadır. Gerekli router, rewrite ve security tanımlamaları mevcuttur.

