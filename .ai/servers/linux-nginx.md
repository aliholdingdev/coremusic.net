---
title: "linux-nginx"
type: reference
folder: ".ai/servers"
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team - Human Mode - Truth Mode
---

# Linux Nginx Yapılandırması

**Ortam:** Production (Linux / Raspberry Pi 5)
**Port:** 80 (HTTP), 443 (HTTPS)
**PHP:** PHP-FPM 8.3+

## 1. Kurulum

### Paketler
```bash
# Nginx kurulumu
sudo apt update
sudo apt install nginx

# PHP-FPM kurulumu
sudo apt install php8.3-fpm php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml php8.3-curl php8.3-gd php8.3-intl

# Redis kurulumu
sudo apt install redis-server

# MySQL kurulumu
sudo apt install mysql-server-9.0

# Certbot kurulumu (SSL)
sudo apt install certbot python3-certbot-nginx
```

## 2. Nginx Ana Yapılandırması

### /etc/nginx/nginx.conf
```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 1024;
    multi_accept on;
}

http {
    # Genel Ayarlar
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    # MIME türleri
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Gzip sıkıştırma
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=auth:10m rate=5r/m;

    # Dosya boyutu limiti
    client_max_body_size 50M;

    # Include vHost dosyaları
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

## 3. Subdomain Yapılandırmaları

### /etc/nginx/sites-available/coremusic.net
```nginx
# Ana domain - Landing Page
server {
    listen 80;
    listen [::]:80;
    server_name coremusic.net www.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # Güvenlik başlıkları
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;

    # Statik dosyalar
    location /assets/ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # PHP yönlendirmesi
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # .env koruması
    location ~ /\.env {
        deny all;
    }

    # vendor koruması
    location ~ /vendor/ {
        deny all;
    }
}

# Auth servisi
server {
    listen 80;
    listen [::]:80;
    server_name auth.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # Auth için sıkı rate limiting
    limit_req zone=auth burst=3 nodelay;

    # Güvenlik başlıkları
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Home (RPi5 Embedded)
server {
    listen 80;
    listen [::]:80;
    server_name home.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # Embedded için optimize
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Pro
server {
    listen 80;
    listen [::]:80;
    server_name pro.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Studio
server {
    listen 80;
    listen [::]:80;
    server_name studio.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Car
server {
    listen 80;
    listen [::]:80;
    server_name car.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Admin
server {
    listen 80;
    listen [::]:80;
    server_name admin.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # Admin için IP kısıtlaması (opsiyonel)
    # allow 192.168.1.0/24;
    # deny all;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# API Gateway
server {
    listen 80;
    listen [::]:80;
    server_name api.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # API için rate limiting
    limit_req zone=api burst=20 nodelay;

    # API versioning
    location /v1/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTP_X_FORWARDED_FOR $proxy_add_x_forwarded_for;
        fastcgi_param HTTP_X_REAL_IP $remote_addr;
    }
}

# Media vault
server {
    listen 80;
    listen [::]:80;
    server_name media.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    # Media için 특별 koruma
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Download
server {
    listen 80;
    listen [::]:80;
    server_name download.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Music
server {
    listen 80;
    listen [::]:80;
    server_name music.coremusic.net;

    root /var/www/coremusic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 4. SSL Yapılandırması

### Let's Encrypt Sertifikası
```bash
# Tüm subdomain'ler için sertifika
sudo certbot --nginx -d coremusic.net -d *.coremusic.net

# Otomatik yenileme
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Yenileme testi
sudo certbot renew --dry-run
```

### HTTPS Yönlendirmesi
```bash
# HTTP'den HTTPS'e yönlendirme
sudo sed -i 's/listen 80;/listen 80;\n    listen 443 ssl http2;/' /etc/nginx/sites-available/coremusic.net
```

## 5. PHP-FPM Yapılandırması

### /etc/php/8.3/fpm/pool.d/www.conf
```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.max_requests = 500

; Hata logları
php_admin_value[error_log] = /var/log/php8.3-fpm/errors.log
php_admin_flag[log_errors] = on

; Oturum
php_value[session.save_handler] = redis
php_value[session.save_path] = "tcp://127.0.0.1:6379"
```

## 6. Servis Yönetimi

```bash
# Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl status nginx
sudo nginx -t                    # Yapılandırma testi
sudo systemctl reload nginx      # Yeniden yükleme

# PHP-FPM
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm
sudo systemctl status php8.3-fpm

# MySQL
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl status mysql

# Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
sudo systemctl status redis-server
```

## 7. RPi5 Özel Ayarlar

### Performans Optimizasyonu
```nginx
# /etc/nginx/conf.d/rpi5-optimized.conf

# RPi5 için optimize
worker_processes 2;          # RPi5 4 çekirdek
worker_connections 512;

# Bellek kullanımı
fastcgi_buffers 16 16k;
fastcgi_buffer_size 32k;
```

### Embedded CSS/JS Önbellek
```nginx
# /etc/nginx/conf.d/static-cache.conf

# CSS/JS önbellek
location ~* \.(css|js)$ {
    expires 7d;
    add_header Cache-Control "public, immutable";
}

# Resim önbellek
location ~* \.(jpg|jpeg|png|gif|ico|svg|webp)$ {
    expires 30d;
    add_header Cache-Control "public, immutable";
}

# Font önbellek
location ~* \.(woff|woff2|ttf|eot)$ {
    expires 365d;
    add_header Cache-Control "public, immutable";
}
```

## 8. Log Yönetimi

```bash
# Nginx logları
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PHP-FPM logları
tail -f /var/log/php8.3-fpm/errors.log

# MySQL logları
tail -f /var/log/mysql/error.log

# Log rotasyonu
sudo logrotate -f /etc/logrotate.d/nginx
```

## 9. Güvenlik Kontrol Listesi

- [ ] server_tokens off
- [ ] X-Frame-Options SAMEORIGIN
- [ ] X-Content-Type-Options nosniff
- [ ] X-XSS-Protection 1; mode=block
- [ ] Strict-Transport-Security (HTTPS)
- [ ] Content-Security-Policy
- [ ] Rate limiting aktif
- [ ] .env dosyası erişime kapalı
- [ ] vendor dizini erişime kapalı
- [ ] SSL sertifikası geçerli
- [ ] Otomatik yenileme aktif
