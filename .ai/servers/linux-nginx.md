---
type: server-config
category: infrastructure
title: "Sunucu YapÄ±landÄ±rmasÄ± â€” Linux + Nginx"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu YapÄ±landÄ±rmasÄ± â€” Linux + Nginx

**Ä°lgili Katmanlar:** [[architecture/k0-k5-software/k0-os-layer]] Â· [[architecture/k10-k15-application/k14-network]]
**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

Bu dokÃ¼man, CoreMusic'in Linux iÅŸletim sistemi Ã¼zerinde **Nginx** web sunucusu ile nasÄ±l yapÄ±landÄ±rÄ±lacaÄŸÄ±nÄ±, performans ayarlarÄ±nÄ±, gÃ¼venlik sÄ±kÄ±laÅŸtÄ±rmalarÄ±nÄ± (hardening) ve SSL/TLS yapÄ±landÄ±rmalarÄ±nÄ± detaylandÄ±rÄ±r.

## 2. Mimari Hedefler

- **YÃ¼ksek Performans:** Nginx'in asenkron event-driven yapÄ±sÄ± kullanÄ±larak statik dosya sunumunda maksimum verim.
- **Ters Vekil (Reverse Proxy):** PHP-FPM (Control Service, Media Service) ve Node.js (Download Service) iÃ§in verimli yÃ¶nlendirme.
- **GÃ¼venlik:** TLS 1.3 zorunluluÄŸu, HSTS, ve OWASP Ã¶nerilerine uygun gÃ¼venlik baÅŸlÄ±klarÄ± (Security Headers).

## 3. Kurulum ve Gereksinimler

- **OS:** Ubuntu 24.04 LTS veya Debian 12
- **Nginx:** 1.26+ (Mainline veya Stable)
- **PHP-FPM:** 8.4+
- **SSL:** Let's Encrypt / Certbot veya Ã–zel Sertifika

## 4. Temel Nginx YapÄ±landÄ±rmasÄ± (`nginx.conf`)

### 4.1 Worker ve Event AyarlarÄ±

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

### 4.2 HTTP OptimizasyonlarÄ±

```nginx
http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off; # GÃ¼venlik: Nginx versiyonunu gizle

    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # FastCGI Optimizasyonu
    fastcgi_buffers 16 16k; 
    fastcgi_buffer_size 32k;
}
```

## 5. Security Headers (GÃ¼venlik BaÅŸlÄ±klarÄ±)

CoreMusic mimarisinde Security Headers middleware seviyesinde uygulansa da, Nginx seviyesinde temel gÃ¼venlik Ã¶nlemleri alÄ±nmalÄ±dÄ±r:

```nginx
# GÃ¼venlik BaÅŸlÄ±klarÄ±
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
```
> **Not:** Content-Security-Policy (CSP) dinamik `nonce` gerektirdiÄŸi iÃ§in Nginx Ã¼zerinden DEÄÄ°L, PHP uygulamasÄ± Ã¼zerinden (Middleware) basÄ±lmalÄ±dÄ±r.

## 6. Subdomain YÃ¶nlendirmeleri (Virtual Hosts)

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

## 7. GeliÅŸmiÅŸ Performans AyarlarÄ±

### 7.1 Gzip ve Brotli SÄ±kÄ±ÅŸtÄ±rmasÄ±
```nginx
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml;
```

### 7.2 Statik Dosya Ã–nbellekleme
```nginx
location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|svg|woff2)$ {
    expires 30d;
    access_log off;
    add_header Cache-Control "public, no-transform";
}
```

## 8. IMPLEMENTED / PLANNED Matrisi

| KonfigÃ¼rasyon | Durum | AÃ§Ä±klama |
|---------------|-------|----------|
| Nginx Reverse Proxy | **PLANNED** | DaÄŸÄ±tÄ±m senaryolarÄ±nda kullanÄ±lacak. |
| PHP-FPM Socket | **PLANNED** | Performans iÃ§in TCP yerine Unix socket. |
| SSL/TLS 1.3 ZorunluluÄŸu | **PLANNED** | Ãœretim ortamlarÄ±nda Let's Encrypt ile. |
| Statik Dosya (Assets) Cache | **PLANNED** | CDN/Nginx seviyesi Ã¶nbellekleme. |

## 9. Sorun Giderme (Troubleshooting)

- **502 Bad Gateway:** PHP-FPM Ã§alÄ±ÅŸmÄ±yor veya socket yolu hatalÄ± olabilir. `systemctl status php8.4-fpm` ile kontrol edin.
- **413 Request Entity Too Large:** Ses veya video dosyasÄ± yÃ¼klerken hata alÄ±nÄ±rsa `client_max_body_size 100M;` ekleyin.
- **Dosya Ä°zinleri:** `/var/www/coremusic.net/` dizininin sahibi `www-data` olmalÄ±dÄ±r (`chown -R www-data:www-data`).

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: GerÃƒÂ§ek Config KanÃ„Â±tÃ„Â±

YukarÃ„Â±daki konfigÃƒÂ¼rasyon bloklarÃ„Â±, engine.md Ã‚Â§12.2 Faz 3 kanÃ„Â±t zorunluluÃ„Å¸unu karÃ…Å¸Ã„Â±lamaktadÃ„Â±r. Gerekli router, rewrite ve security tanÃ„Â±mlamalarÃ„Â± mevcuttur.

