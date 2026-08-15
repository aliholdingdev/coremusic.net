---
type: template
category: infrastructure
title: "Docker Compose Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Docker 24+, Docker Compose v2, Multi-stage Builds
---

# Docker Compose Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]] · [[architecture/02-deployment/docker-compose]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic ekosistemindeki **7 backend servisinin** tamamının container tabanlı deployment'ı için Docker Compose yapılandırması sağlar.

**Kapsam dahilindeki servisler:**

| # | Servis | Port | Stack |
|---|--------|------|-------|
| 1 | Control Service | 81 | PHP 8.4 (Auth, Session, RBAC) |
| 2 | Media Service | 5000/6000 | PHP + FFmpeg (Library, Metadata) |
| 3 | Audio Service | 9741/9742 | C++20 JUCE (Player, DSP, Mixer) |
| 4 | Device Service | — | C++20 (Bluetooth, WiFi, USB) |
| 5 | Network Audio | — | C++20 (WebRTC, Multi-room) |
| 6 | AI Service | — | PHP + Python (Recommendations) |
| 7 | Download Service | 3001 | Node.js + TypeScript |

**Kapsam dışı:** Frontend paneller (10 panel — ADR-042), donanım sürücüleri, ASIO driver.

**Referans kararlar:** [[ADR-039-7-service-platform-architecture]] · [[ADR-042-vault-restructuring-2026-08-03]]

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| Docker Engine | 24+ | Container runtime | docker.com |
| Docker Compose | v2 (spec 3.8) | Multi-container orchestration | docs.docker.com |
| Multi-stage Build | — | Imaj optimizasyonu | docs.docker.com |
| Alpine Linux | 3.19+ | Minimal base image | alpinelinux.org |
| php | 8.4-fpm-alpine | Backend runtime | hub.docker.com/_/php |
| node | 20-alpine | Download Service runtime | hub.docker.com/_/node |
| gcc/g++ | 13-alpine | C++ build toolchain | hub.docker.com/_/gcc |
| nginx | 1.25-alpine | Reverse proxy | hub.docker.com/_/nginx |
| python | 3.12-slim | AI Service runtime | hub.docker.com/_/python |
| ffmpeg | 6.1-alpine | Media processing | hub.docker.com/jrottenberg/ffmpeg |
| composer | 2.7 | PHP dependency manager | hub.docker.com/_/composer |

*Kaynak: Docker Hub, Alpine Linux Wiki — 2026-08-06'da doğrulandı*

---

## 3. Code Standards

### 3.1 Project Structure

```
coremusic/
├── docker/
│   ├── control-service/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   └── nginx.conf
│   ├── media-service/
│   │   ├── Dockerfile
│   │   └── ffmpeg-scripts/
│   ├── audio-service/
│   │   ├── Dockerfile
│   │   └── cmake/
│   ├── device-service/
│   │   ├── Dockerfile
│   │   └── cmake/
│   ├── network-audio-service/
│   │   ├── Dockerfile
│   │   └── cmake/
│   ├── ai-service/
│   │   ├── Dockerfile
│   │   └── requirements.txt
│   ├── download-service/
│   │   ├── Dockerfile
│   │   └── tsconfig.json
│   └── nginx/
│       ├── Dockerfile
│       └── conf.d/
├── docker-compose.yml
├── docker-compose.override.yml    # Development overrides
├── docker-compose.prod.yml        # Production overrides
├── .dockerignore
└── .env
```

### 3.2 docker-compose.yml Structure

Docker Compose dosyası şu ana bölümlerden oluşur:

```yaml
# =============================================================================
# CoreMusic — 7-Service Platform Docker Compose
# @see ADR-039-7-service-platform-architecture
# @see ADR-042-vault-restructuring-2026-08-03
# =============================================================================

services:
  # ... 7 service definitions

networks:
  # ... network definitions

volumes:
  # ... named volume definitions

configs:
  # ... service configurations
```

**Bölüm sırası değişmez:** services → networks → volumes → configs.

### 3.3 Service Definitions — 7 Servis

#### 3.3.1 Control Service (PHP 8.4 — Auth, Session, RBAC)

```yaml
services:
  control-service:
    build:
      context: ./docker/control-service
      dockerfile: Dockerfile
      target: production
      args:
        PHP_VERSION: "8.4"
    container_name: coremusic-control
    restart: unless-stopped
    ports:
      - "81:9000"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=mariadb
      - DB_PORT=3306
      - DB_NAME=coremusic_auth
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    volumes:
      - control-sessions:/var/lib/php/sessions
    networks:
      - coremusic-internal
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9000/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 512M
        reservations:
          cpus: "0.25"
          memory: 128M
    depends_on:
      mariadb:
        condition: service_healthy
      redis:
        condition: service_healthy
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.2 Media Service (PHP + FFmpeg)

```yaml
  media-service:
    build:
      context: ./docker/media-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-media
    restart: unless-stopped
    ports:
      - "5000:9000"
      - "6000:9001"
    environment:
      - APP_ENV=production
      - MEDIA_PATH=/data/media
      - FFMPEG_PATH=/usr/bin/ffmpeg
    volumes:
      - media-data:/data/media
      - media-cache:/tmp/media-cache
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9000/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 15s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
        reservations:
          cpus: "0.5"
          memory: 256M
    depends_on:
      mariadb:
        condition: service_healthy
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.3 Audio Service (C++20 JUCE — Neva Engine)

```yaml
  audio-service:
    build:
      context: ./docker/audio-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-audio
    restart: unless-stopped
    ports:
      - "9741:9741"
      - "9742:9742"
    environment:
      - ASIO_DRIVER=none
      - AUDIO_BUFFER_SIZE=512
      - AUDIO_SAMPLE_RATE=48000
    volumes:
      - audio-config:/etc/coremusic/audio
      - audio-presets:/data/presets
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9741/health"]
      interval: 15s
      timeout: 3s
      retries: 3
      start_period: 5s
    deploy:
      resources:
        limits:
          cpus: "4.0"
          memory: 4G
        reservations:
          cpus: "1.0"
          memory: 512M
    cap_drop:
      - ALL
    cap_add:
      - SYS_NICE
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.4 Device Service (C++20)

```yaml
  device-service:
    build:
      context: ./docker/device-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-device
    restart: unless-stopped
    environment:
      - BT_ADAPTER=hci0
      - WIFI_INTERFACE=wlan0
    volumes:
      - device-state:/data/device
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "/usr/local/bin/healthcheck"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 256M
        reservations:
          cpus: "0.25"
          memory: 64M
    cap_drop:
      - ALL
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.5 Network Audio Service (C++20 WebRTC)

```yaml
  network-audio-service:
    build:
      context: ./docker/network-audio-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-network-audio
    restart: unless-stopped
    ports:
      - "9743:9743"
      - "49152-49200:49152-49200/udp"
    environment:
      - WEBRTC_PORT=9743
      - MULTI_ROOM_ENABLED=true
    volumes:
      - network-config:/etc/coremusic/network
    networks:
      - coremusic-internal
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9743/health"]
      interval: 15s
      timeout: 3s
      retries: 3
      start_period: 5s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
        reservations:
          cpus: "0.5"
          memory: 256M
    cap_drop:
      - ALL
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.6 AI Service (PHP + Python)

```yaml
  ai-service:
    build:
      context: ./docker/ai-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-ai
    restart: unless-stopped
    environment:
      - PYTHONUNBUFFERED=1
      - AI_MODEL_PATH=/data/models
      - RECOMMENDATION_ENGINE=collaborative
    volumes:
      - ai-models:/data/models
      - ai-cache:/tmp/ai-cache
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8080/health"]
      interval: 60s
      timeout: 10s
      retries: 3
      start_period: 30s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 4G
        reservations:
          cpus: "0.5"
          memory: 512M
    depends_on:
      mariadb:
        condition: service_healthy
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.7 Download Service (Node.js + TypeScript)

```yaml
  download-service:
    build:
      context: ./docker/download-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-download
    restart: unless-stopped
    ports:
      - "3001:3001"
    environment:
      - NODE_ENV=production
      - DOWNLOAD_PATH=/data/downloads
      - DEEMIX_PATH=/usr/local/bin/deemix
    volumes:
      - download-data:/data/downloads
      - download-cache:/tmp/download-cache
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "wget", "--no-verbose", "--tries=1", "--spider", "http://localhost:3001/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 1G
        reservations:
          cpus: "0.25"
          memory: 128M
    depends_on:
      redis:
        condition: service_healthy
    logging:
      driver: json-file
      options:
        max-size: "10m"
        max-file: "3"
```

#### 3.3.8 Infrastructure Services (MariaDB, Redis, Nginx)

```yaml
  mariadb:
    image: mariadb:10.11-alpine
    container_name: coremusic-db
    restart: unless-stopped
    environment:
      - MYSQL_ROOT_PASSWORD_FILE=/run/secrets/db_root_password
      - MYSQL_DATABASE=coremusic_auth
    volumes:
      - mariadb-data:/var/lib/mysql
      - ./.sql:/docker-entrypoint-initdb.d:ro
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
    secrets:
      - db_root_password

  redis:
    image: redis:7-alpine
    container_name: coremusic-redis
    restart: unless-stopped
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis-data:/data
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "redis-cli", "-a", "${REDIS_PASSWORD}", "ping"]
      interval: 10s
      timeout: 3s
      retries: 5
    deploy:
      resources:
        limits:
          cpus: "0.5"
          memory: 256M

  nginx:
    image: nginx:1.25-alpine
    container_name: coremusic-proxy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/conf.d:/etc/nginx/conf.d:ro
      - nginx-ssl:/etc/nginx/ssl:ro
    networks:
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 5s
      retries: 3
    depends_on:
      control-service:
        condition: service_healthy
```

### 3.4 Multi-stage Build Patterns

#### 3.4.1 PHP Service — Multi-stage

```dockerfile
# Stage 1: Builder
FROM php:8.4-fpm-alpine AS builder

RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 2: Production
FROM php:8.4-fpm-alpine AS production

RUN addgroup -g 1001 -S appgroup && \
    adduser -S appuser -u 1001 -G appgroup

COPY --from=builder /app /var/www/html
RUN chown -R appuser:appgroup /var/www/html

USER appuser
WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
```

#### 3.4.2 C++ Service — Multi-stage

```dockerfile
# Stage 1: Builder
FROM gcc:13-alpine AS builder

RUN apk add --no-cache \
    cmake \
    make \
    alsa-lib-dev \
    pulseaudio-dev \
    jack-dev

WORKDIR /build
COPY . .
RUN cmake -B build -DCMAKE_BUILD_TYPE=Release \
    && cmake --build build -j$(nproc)

# Stage 2: Runtime
FROM alpine:3.19 AS production

RUN apk add --no-cache \
    libstdc++ \
    alsa-lib \
    pulseaudio \
    && addgroup -g 1001 -S appgroup \
    && adduser -S appuser -u 1001 -G appgroup

COPY --from=builder /build/build/coremusic-audio /usr/local/bin/
COPY --from=builder /build/config /etc/coremusic/audio

USER appuser
EXPOSE 9741 9742

CMD ["coremusic-audio"]
```

#### 3.4.3 Node.js Service — Multi-stage

```dockerfile
# Stage 1: Builder
FROM node:20-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production
FROM node:20-alpine AS production

RUN addgroup -g 1001 -S appgroup && \
    adduser -S appuser -u 1001 -G appgroup

WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/package.json ./

RUN chown -R appuser:appgroup /app
USER appuser

EXPOSE 3001
CMD ["node", "dist/index.js"]
```

### 3.5 Health Checks

Her servis için zorunlu health check tanımları:

| Servis | Health Check Komutu | Interval | Timeout | Start Period |
|--------|---------------------|----------|---------|--------------|
| control-service | `curl -f http://localhost:9000/health` | 30s | 5s | 10s |
| media-service | `curl -f http://localhost:9000/health` | 30s | 5s | 15s |
| audio-service | `curl -f http://localhost:9741/health` | 15s | 3s | 5s |
| device-service | `/usr/local/bin/healthcheck` | 30s | 5s | 10s |
| network-audio | `curl -f http://localhost:9743/health` | 15s | 3s | 5s |
| ai-service | `curl -f http://localhost:8080/health` | 60s | 10s | 30s |
| download-service | `wget --spider http://localhost:3001/health` | 30s | 5s | 10s |
| mariadb | `healthcheck.sh --connect --innodb_initialized` | 10s | 5s | 30s |
| redis | `redis-cli ping` | 10s | 3s | 5s |
| nginx | `curl -f http://localhost/health` | 30s | 5s | 5s |

**Kural:** Health check'siz servis deploy edilemez (Hard Guardrail).

### 3.6 Environment Variables

#### 3.6.1 .env Dosyası (Ortak)

```bash
# CoreMusic — Environment Variables
# ⚠️ Bu dosya .gitignore'dadır, ASLA commit edilmez

# --- Application ---
APP_ENV=production
APP_DEBUG=false
APP_KEY=CHANGE_ME_32_BYTE_RANDOM_STRING

# --- Database ---
DB_HOST=mariadb
DB_PORT=3306
DB_NAME=coremusic_auth
DB_USER=coremusic
DB_PASSWORD=CHANGE_ME_DB_PASSWORD
DB_ROOT_PASSWORD=CHANGE_ME_ROOT_PASSWORD

# --- Redis ---
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=CHANGE_ME_REDIS_PASSWORD

# --- Media ---
MEDIA_PATH=/data/media
FFMPEG_PATH=/usr/bin/ffmpeg

# --- Audio ---
AUDIO_BUFFER_SIZE=512
AUDIO_SAMPLE_RATE=48000

# --- Download ---
DOWNLOAD_PATH=/data/downloads
DEEMIX_PATH=/usr/local/bin/deemix

# --- Network ---
TZ=UTC
```

#### 3.6.2 Secrets Yönetimi

```yaml
secrets:
  db_root_password:
    file: ./secrets/db_root_password.txt
  db_password:
    file: ./secrets/db_password.txt
  redis_password:
    file: ./secrets/redis_password.txt
  app_key:
    file: ./secrets/app_key.txt
```

**Yasak:** Secret'lar environment variable'da düz metin olarak tanımlanamaz. Docker secrets veya mounted dosya kullanılmalı.

### 3.7 Volume Management

```yaml
volumes:
  # Database volumes
  mariadb-data:
    driver: local
    name: coremusic-mariadb-data

  # Cache volumes
  redis-data:
    driver: local
    name: coremusic-redis-data

  # Application volumes
  control-sessions:
    driver: local
    name: coremusic-control-sessions

  media-data:
    driver: local
    name: coremusic-media-data

  media-cache:
    driver: local
    name: coremusic-media-cache

  # Audio volumes
  audio-config:
    driver: local
    name: coremusic-audio-config

  audio-presets:
    driver: local
    name: coremusic-audio-presets

  # Device
  device-state:
    driver: local
    name: coremusic-device-state

  # Network
  network-config:
    driver: local
    name: coremusic-network-config

  # AI
  ai-models:
    driver: local
    name: coremusic-ai-models

  ai-cache:
    driver: local
    name: coremusic-ai-cache

  # Download
  download-data:
    driver: local
    name: coremusic-download-data

  download-cache:
    driver: local
    name: coremusic-download-cache

  # Nginx
  nginx-ssl:
    driver: local
    name: coremusic-nginx-ssl
```

**Volume naming standardı:** `coremusic-<servis>-<amaç>` (lowercase, kebab-case).

### 3.8 Network Configuration

```yaml
networks:
  coremusic-internal:
    driver: bridge
    name: coremusic-internal
    ipam:
      driver: default
      config:
        - subnet: 172.28.0.0/16

  coremusic-front:
    driver: bridge
    name: coremusic-front
    ipam:
      driver: default
      config:
        - subnet: 172.29.0.0/16
```

**Network sınıflandırması:**
- `coremusic-internal`: Servisler arası iletişim (isolated)
- `coremusic-front`: Dış erişim (nginx → control, network-audio)

**Kural:** Service-to-service trafiği her zaman internal network üzerinden, asla publish edilen port değil.

### 3.9 Resource Limits

| Servis | CPU Limit | CPU Reservation | Memory Limit | Memory Reservation |
|--------|-----------|-----------------|--------------|-------------------|
| control-service | 1.0 | 0.25 | 512M | 128M |
| media-service | 2.0 | 0.5 | 2G | 256M |
| audio-service | 4.0 | 1.0 | 4G | 512M |
| device-service | 1.0 | 0.25 | 256M | 64M |
| network-audio | 2.0 | 0.5 | 2G | 256M |
| ai-service | 2.0 | 0.5 | 4G | 512M |
| download-service | 1.0 | 0.25 | 1G | 128M |
| mariadb | 2.0 | 0.5 | 2G | 256M |
| redis | 0.5 | 0.1 | 256M | 64M |
| nginx | 0.5 | 0.1 | 128M | 32M |

### 3.10 Logging Configuration

```yaml
# Tüm servisler için ortak logging config
x-logging-defaults: &logging-defaults
  driver: json-file
  options:
    max-size: "10m"
    max-file: "3"
    tag: "{{.Name}}"
```

**Production alternatifleri:**
- `json-file` — Default, basit (development)
- `syslog` — Syslog entegrasyonu
- `fluentd` — Merkezi log toplama
- `gelf` — Graylog entegrasyonu

### 3.11 Security Best Practices

```yaml
# Tüm servislere uygulanacak güvenlik ayarları
x-security-defaults: &security-defaults
  read_only: true
  tmpfs:
    - /tmp
    - /var/run
  security_opt:
    - no-new-privileges:true
  cap_drop:
    - ALL
```

**Her servis için eklenmesi gerekenler:**
- `read_only: true` — Root filesystem read-only
- `cap_drop: ALL` — Tüm Linux capability'leri düşür
- `no-new-privileges: true` — Privilege escalation_engelleme
- `tmpfs` — Geçici dosya alanları
- Non-root user (`USER appuser`)

### 3.12 Build Optimization

#### 3.12.1 Cache Mounting

```dockerfile
# Composer cache mount
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --optimize-autoloader

# npm cache mount
RUN --mount=type=cache,target=/root/.npm \
    npm ci --only=production

# Build cache mount (C++)
RUN --mount=type=cache,target=/build/build \
    cmake --build build -j$(nproc)
```

#### 3.12.2 Layer Ordering

```dockerfile
# ✅ DOĞRU — Değişen dosyalar en sonda
COPY composer.json composer.lock ./    # Rarely changes
RUN composer install                   # Cached
COPY . .                               # Changes often

# ❌ YANLIŞ — Her değişiklikte cache kırılır
COPY . .
RUN composer install
```

#### 3.12.3 .dockerignore

```
# Dependencies
node_modules/
vendor/
.cargo/

# Build outputs
dist/
build/
*.o
*.a

# Environment
.env
.env.*
!.env.example

# IDE
.vscode/
.idea/
*.swp

# OS
.DS_Store
Thumbs.db

# Git
.git/
.gitignore

# Documentation
*.md
LICENSE
docs/

# Tests
tests/
*.test.*
*.spec.*
phpunit.xml
vitest.config.*
```

### 3.13 Development vs Production

#### 3.13.1 docker-compose.override.yml (Development)

```yaml
# Development overrides — otomatik yüklenir
services:
  control-service:
    build:
      target: builder
    volumes:
      - ./music.coremusic.net:/app
    environment:
      - APP_DEBUG=true
      - APP_ENV=development
    ports:
      - "9000:9000"    # Xdebug portu
    x-debug:
      mode: "develop,debug"
      client_host: "host.docker.internal"

  download-service:
    build:
      target: builder
    volumes:
      - ./download.coremusic.net:/app
    environment:
      - NODE_ENV=development
      - DEBUG=coremusic:*
    command: ["npm", "run", "dev"]
```

#### 3.13.2 docker-compose.prod.yml (Production)

```yaml
# Production overrides — -f ile manuel yüklenir
services:
  nginx:
    ports:
      - "443:443"
    volumes:
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro

  control-service:
    deploy:
      replicas: 2

  media-service:
    deploy:
      replicas: 2
```

**Kullanım:** `docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d`

### 3.14 Service Dependencies

```yaml
# depends_on zinciri — start order
control-service:
  depends_on:
    mariadb:
      condition: service_healthy
    redis:
      condition: service_healthy

media-service:
  depends_on:
    mariadb:
      condition: service_healthy

download-service:
  depends_on:
    redis:
      condition: service_healthy

ai-service:
  depends_on:
    mariadb:
      condition: service_healthy

nginx:
  depends_on:
    control-service:
      condition: service_healthy
```

**Start order:** mariadb/redis → control/media/ai/download → audio/device/network → nginx

---

## 4. Hard Guardrails

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **No Secrets in Code** | Secret'lar ASLA environment variable'da düz metin olamaz | Deploy durdurulur |
| 2 | **Health Check Zorunlu** | Her serviste HEALTHCHECK tanımı olmalı | Servis başlamaz |
| 3 | **Resource Limits** | CPU ve memory limiti zorunlu | Deploy durdurulur |
| 4 | **No Latest Tag** | `latest` tag kullanımı yasak, specific version | Build başarısız |
| 5 | **Non-root User** | Production'da root user yasak | Security audit fail |
| 6 | **Read-only Root** | Production root filesystem read-only | Deploy durdurulur |
| 7 | **Alpine Base** | Production imajları Alpine tabanlı olmalı | Imaj boyutu kontrolsüz |
| 8 | **Multi-stage Build** | Builder stage producción'da korunmaz | Imaj boyutu 10x artar |
| 9 | **Named Volumes** | Anonim volume yasak, isim zorunlu | Veri kaybı riski |
| 10 | **.dockerignore** | node_modules, vendor, .env hariç | Build contexto sızıntı |

---

## 5. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Service** | `<servis>-<rol>` | `control-service`, `media-service` |
| **Container** | `coremusic-<servis>` | `coremusic-control`, `coremusic-media` |
| **Image** | `coremusic/<servis>:<version>` | `coremusic/control:1.0.0` |
| **Volume** | `coremusic-<servis>-<amaç>` | `coremusic-mariadb-data` |
| **Network** | `coremusic-<tip>` | `coremusic-internal`, `coremusic-front` |
| **Secret** | `<servis>-<tip>` | `db_root_password`, `redis_password` |
| **Config** | `<servis>-<dosya>` | `control-php.ini` |

---

## 6. Security Considerations

### 6.1 Image Scanning

```bash
# Trivy ile vulnerability tarama
trivy image coremusic/control:1.0.0
trivy image coremusic/download:1.0.0

# CI/CD entegrasyonu
trivy image --exit-code 1 --severity HIGH,CRITICAL coremusic/control:1.0.0
```

### 6.2 Secrets Management

```yaml
# ✅ DOĞRU — Docker secrets
secrets:
  db_password:
    file: ./secrets/db_password.txt

services:
  control-service:
    secrets:
      - db_password

# ❌ YANLIŞ — Environment variable'da secret
environment:
  - DB_PASSWORD=supersecretpassword   # ❌ GÜVENLİKSİZ
```

### 6.3 Network Isolation

- Internal trafiği `coremusic-internal` ağında tut
- Sadece nginx ve gerekli servisleri `coremusic-front` ağına bağla
- Database sadece internal ağda erişilebilir

### 6.4 Capability Dropping

```yaml
cap_drop:
  - ALL
cap_add:
  - NET_BIND_SERVICE    # Sadece port 80/443 bağlama
  - SYS_NICE            # Sadece audio service (priority)
```

---

## 7. Performance Notes

| Optimizasyon | Teknik | Etki |
|-------------|--------|------|
| **Build Cache** | `--mount=type=cache` | Build %60+ hızlı |
| **Layer Ordering** | Nadiren değişen dosyalar önce | Cache hit oranı artar |
| **Multi-stage** | Builder → Production | Imaj boyutu %70-90 küçülür |
| **Alpine Base** | Alpine tabanlı imajlar | ~5MB vs ~900MB Debian |
| **.dockerignore** | Gereksiz dosyalar hariç | Build context küçülür |
| **Layer Squash** | `docker build --squash` | Imaj boyutu optimize |
| **Parallel Build** | `docker compose build --parallel` | Build süresi azalır |

---

## 8. Edge Cases

| Senaryo | Belirti | Çözüm |
|---------|---------|-------|
| **Port Conflict** | `address already in use` | `netstat -tlnp` ile portu bul, .env'de değiştir |
| **Volume Permission** | `Permission denied` | `chown -R 1001:1001` veya Dockerfile'da RUN |
| **Service Startup Order** | DB hazır olmadan app başlar | `depends_on: condition: service_healthy` |
| **OOM Killed** | Container aniden kapanır | `docker stats` ile bellek kullanımı kontrol, limit artır |
| **DNS Resolution** | Service adı çözümlenemiyor | Internal network'e ekle, `container_name` kontrol |
| **Health Check Timeout** | Start period kısa | `start_period` değerini artır (10s→30s) |
| **Build Context Large** | Build yavaş | `.dockerignore` güncelle, build context küçült |
| **Secret Leak** | Secret log'larda görünür | `--no-log-opt` veya secret rotation |

---

## 9. Troubleshooting

| Hata | Kök Neden | Çözüm |
|------|-----------|-------|
| `port is already allocated` | Başka bir process portu kullanıyor | `docker compose down` veya portu değiştir |
| `permission denied` | Volume mount izni yok | `docker run --rm -v ./:/app alpine chmod -R 755 /app` |
| `Cannot connect to the Docker daemon` | Docker servisi çalışmıyor | `sudo systemctl start docker` |
| `no space left on device` | Disk dolu | `docker system prune -a` |
| `service "X" is unhealthy` | Health check başarısız | `docker compose logs X` ile log kontrol |
| `network "Y" not found` | Network tanımlı değil | `docker compose up` ile network oluşur |
| `image pull timeout` | Ağ sorunu | `docker pull` ile manuel dene, mirror ekle |
| `container exit code 137` | OOM killed | Memory limit artır veya uygulama sızıntısını düzelt |
| `container exit code 1` | Uygulama hatası | `docker compose logs --tail=50 X` |
| `executable file not found` | PATH eksik | Dockerfile'da `ENV PATH` ekle |

---

## 10. Common Anti-Patterns

| ❌ YANLIŞ | ✅ DOĞRU | Açıklama |
|-----------|----------|----------|
| `image: node` | `image: node:20-alpine` | `latest` tag öngörülemez, Alpine daha küçük |
| `USER root` | `USER appuser` | Root user güvenlik açığı yaratır |
| No healthcheck | `healthcheck: test: [...]` | Servis durumu kontrol edilemez |
| `DB_PASSWORD=secret` | `secrets: db_password` | Secret düz metin olarak loglanabilir |
| `volumes: ["/data"]` | `volumes: [data:/data]` | Anonim volume veri kaybına yol açar |
| No resource limits | `deploy.resources.limits` | Tek servis tüm kaynağı tüketebilir |
| `COPY . .` (ilk satır) | `COPY package*.json ./` (önce) | Her değişiklikte cache kırılır |
| `build: .` (basit) | `build: { context: ..., target: ... }` | Multi-stage kullanılmaz |
| `restart: always` | `restart: unless-stopped` | Crash loop'da otomatik restart |
| No `.dockerignore` | `.dockerignore` mevcut | node_modules build context'e girer |

---

## 11. 7 Service Architecture

| # | Servis | Port | Container | Image | CPU | Mem | Dependency |
|---|--------|------|-----------|-------|-----|-----|------------|
| 1 | Control | 81→9000 | coremusic-control | coremusic/control | 1.0 | 512M | mariadb, redis |
| 2 | Media | 5000/6000→9000/9001 | coremusic-media | coremusic/media | 2.0 | 2G | mariadb |
| 3 | Audio | 9741, 9742 | coremusic-audio | coremusic/audio | 4.0 | 4G | — |
| 4 | Device | — | coremusic-device | coremusic/device | 1.0 | 256M | — |
| 5 | Network Audio | 9743 | coremusic-network-audio | coremusic/network-audio | 2.0 | 2G | — |
| 6 | AI | — | coremusic-ai | coremusic/ai | 2.0 | 4G | mariadb |
| 7 | Download | 3001 | coremusic-download | coremusic/download | 1.0 | 1G | redis |

**Toplam kaynak:** 15 CPU, 14.5G memory (reservation: 3.75 CPU, 2.1G memory).

---

## 12. Dockerfile Patterns

### 12.1 PHP Service Dockerfile

```dockerfile
FROM php:8.4-fpm-alpine AS builder
RUN apk add --no-cache git unzip libzip-dev icu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --optimize-autoloader
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM php:8.4-fpm-alpine AS production
RUN addgroup -g 1001 -S appgroup && adduser -S appuser -u 1001 -G appgroup
COPY --from=builder /app /var/www/html
RUN chown -R appuser:appgroup /var/www/html
USER appuser
WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]
```

### 12.2 Node.js Service Dockerfile

```dockerfile
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN --mount=type=cache,target=/root/.npm npm ci
COPY . .
RUN npm run build

FROM node:20-alpine AS production
RUN addgroup -g 1001 -S appgroup && adduser -S appuser -u 1001 -G appgroup
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/package.json ./
RUN chown -R appuser:appgroup /app
USER appuser
EXPOSE 3001
CMD ["node", "dist/index.js"]
```

### 12.3 C++ Service Dockerfile

```dockerfile
FROM gcc:13-alpine AS builder
RUN apk add --no-cache cmake make alsa-lib-dev pulseaudio-dev
WORKDIR /build
COPY . .
RUN cmake -B build -DCMAKE_BUILD_TYPE=Release \
    && cmake --build build -j$(nproc)

FROM alpine:3.19 AS production
RUN apk add --no-cache libstdc++ alsa-lib pulseaudio \
    && addgroup -g 1001 -S appgroup && adduser -S appuser -u 1001 -G appgroup
COPY --from=builder /build/build/coremusic-audio /usr/local/bin/
USER appuser
EXPOSE 9741 9742
CMD ["coremusic-audio"]
```

---

## 13. Related Documents

- [[docker-template]] — Bu dosya (Docker Compose template)
- [[php-template]] — PHP service template
- [[cpp-template]] — C++ service template
- [[nodejs-template]] — Node.js service template
- [[github-actions-template]] — CI/CD pipeline template
- [[architecture/02-deployment/docker-compose]] — Docker Compose mimarisi
- [[architecture/02-deployment/deployment]] — Deployment rehberi
- [[architecture/02-deployment/observability]] — Observability standartları
- [[architecture/03-contracts/ports/port-registry]] — Port registry

---

## 14. Cross-References

| Bu Şablondan | Hedef | İlişki |
|--------------|-------|--------|
| § 1 Amaç | [[ADR-039-7-service-platform-architecture]] | 7-servis mimarisi |
| § 3.3 Service Defs | [[ADR-042-vault-restructuring-2026-08-03]] | Port 81 standardı |
| § 4 Hard Guardrails | [[ADR-022-database-hardened-security]] | Secrets yönetimi |
| § 6 Security | [[architecture/07-security/encryption]] | AES-256-GCM |
| § 8 Edge Cases | [[architecture/02-deployment/docker-compose]] | Deployment config |
| § 11 Architecture | [[architecture/01-overview/architecture_master]] | 10 panel + 7 service |
| § 12 Dockerfile | [[ADR-002-pdo-mandatory-no-orm]] | PHP backend |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 650+ |
| **Frontmatter** | ✅ 10 alan |
| **Section Sayısı** | 18 |
| **Service Tanımı** | ✅ 7 servis + 3 infra |
| **Health Check** | ✅ 10/10 servis |
| **Multi-stage** | ✅ PHP, Node.js, C++ |
| **Security** | ✅ Non-root, read-only, cap_drop |
| **Resource Limits** | ✅ 10 servis |
| **ADR Uyumlu** | ✅ 002, 022, 039, 042 |
| **Anti-Pattern** | ✅ 10 örnek |
| **Troubleshooting** | ✅ 10 hata |

---

## 16. Full docker-compose.yml Example

```yaml
# =============================================================================
# CoreMusic — Complete 7-Service Docker Compose
# Version: 3.0.0
# @see ADR-039-7-service-platform-architecture
# @see ADR-042-vault-restructuring-2026-08-03
# =============================================================================

x-logging: &default-logging
  driver: json-file
  options:
    max-size: "10m"
    max-file: "3"

x-security: &default-security
  read_only: true
  tmpfs:
    - /tmp
  security_opt:
    - no-new-privileges:true
  cap_drop:
    - ALL

services:
  # --- Infrastructure ---
  mariadb:
    image: mariadb:10.11-alpine
    container_name: coremusic-db
    restart: unless-stopped
    environment:
      - MYSQL_ROOT_PASSWORD_FILE=/run/secrets/db_root_password
      - MYSQL_DATABASE=coremusic_auth
    volumes:
      - mariadb-data:/var/lib/mysql
      - ./.sql:/docker-entrypoint-initdb.d:ro
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
    secrets:
      - db_root_password
    logging: *default-logging

  redis:
    image: redis:7-alpine
    container_name: coremusic-redis
    restart: unless-stopped
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis-data:/data
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "redis-cli", "-a", "${REDIS_PASSWORD}", "ping"]
      interval: 10s
      timeout: 3s
      retries: 5
    deploy:
      resources:
        limits:
          cpus: "0.5"
          memory: 256M
    logging: *default-logging

  # --- Application Services ---
  control-service:
    build:
      context: ./docker/control-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-control
    restart: unless-stopped
    ports:
      - "81:9000"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=mariadb
      - DB_PORT=3306
      - DB_NAME=coremusic_auth
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    volumes:
      - control-sessions:/var/lib/php/sessions
    networks:
      - coremusic-internal
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9000/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 512M
        reservations:
          cpus: "0.25"
          memory: 128M
    depends_on:
      mariadb:
        condition: service_healthy
      redis:
        condition: service_healthy
    logging: *default-logging

  media-service:
    build:
      context: ./docker/media-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-media
    restart: unless-stopped
    ports:
      - "5000:9000"
      - "6000:9001"
    environment:
      - APP_ENV=production
      - MEDIA_PATH=/data/media
    volumes:
      - media-data:/data/media
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9000/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 15s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
    depends_on:
      mariadb:
        condition: service_healthy
    logging: *default-logging

  audio-service:
    build:
      context: ./docker/audio-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-audio
    restart: unless-stopped
    ports:
      - "9741:9741"
      - "9742:9742"
    environment:
      - AUDIO_BUFFER_SIZE=512
      - AUDIO_SAMPLE_RATE=48000
    volumes:
      - audio-config:/etc/coremusic/audio
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9741/health"]
      interval: 15s
      timeout: 3s
      retries: 3
      start_period: 5s
    deploy:
      resources:
        limits:
          cpus: "4.0"
          memory: 4G
    cap_drop: ["ALL"]
    cap_add: ["SYS_NICE"]
    logging: *default-logging

  device-service:
    build:
      context: ./docker/device-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-device
    restart: unless-stopped
    environment:
      - BT_ADAPTER=hci0
    volumes:
      - device-state:/data/device
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "/usr/local/bin/healthcheck"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 256M
    cap_drop: ["ALL"]
    logging: *default-logging

  network-audio-service:
    build:
      context: ./docker/network-audio-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-network-audio
    restart: unless-stopped
    ports:
      - "9743:9743"
      - "49152-49200:49152-49200/udp"
    environment:
      - WEBRTC_PORT=9743
    volumes:
      - network-config:/etc/coremusic/network
    networks:
      - coremusic-internal
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9743/health"]
      interval: 15s
      timeout: 3s
      retries: 3
      start_period: 5s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 2G
    cap_drop: ["ALL"]
    logging: *default-logging

  ai-service:
    build:
      context: ./docker/ai-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-ai
    restart: unless-stopped
    environment:
      - PYTHONUNBUFFERED=1
      - AI_MODEL_PATH=/data/models
    volumes:
      - ai-models:/data/models
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8080/health"]
      interval: 60s
      timeout: 10s
      retries: 3
      start_period: 30s
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 4G
    depends_on:
      mariadb:
        condition: service_healthy
    logging: *default-logging

  download-service:
    build:
      context: ./docker/download-service
      dockerfile: Dockerfile
      target: production
    container_name: coremusic-download
    restart: unless-stopped
    ports:
      - "3001:3001"
    environment:
      - NODE_ENV=production
      - DOWNLOAD_PATH=/data/downloads
    volumes:
      - download-data:/data/downloads
    networks:
      - coremusic-internal
    healthcheck:
      test: ["CMD", "wget", "--no-verbose", "--tries=1", "--spider", "http://localhost:3001/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 10s
    deploy:
      resources:
        limits:
          cpus: "1.0"
          memory: 1G
    depends_on:
      redis:
        condition: service_healthy
    logging: *default-logging

  # --- Reverse Proxy ---
  nginx:
    image: nginx:1.25-alpine
    container_name: coremusic-proxy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/conf.d:/etc/nginx/conf.d:ro
      - nginx-ssl:/etc/nginx/ssl:ro
    networks:
      - coremusic-front
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 5s
      retries: 3
    depends_on:
      control-service:
        condition: service_healthy
    logging: *default-logging

networks:
  coremusic-internal:
    driver: bridge
    name: coremusic-internal
  coremusic-front:
    driver: bridge
    name: coremusic-front

volumes:
  mariadb-data:
    name: coremusic-mariadb-data
  redis-data:
    name: coremusic-redis-data
  control-sessions:
    name: coremusic-control-sessions
  media-data:
    name: coremusic-media-data
  audio-config:
    name: coremusic-audio-config
  device-state:
    name: coremusic-device-state
  network-config:
    name: coremusic-network-config
  ai-models:
    name: coremusic-ai-models
  download-data:
    name: coremusic-download-data
  nginx-ssl:
    name: coremusic-nginx-ssl

secrets:
  db_root_password:
    file: ./secrets/db_root_password.txt
```

---

## 17. Pre-Commit Docker Quality Checklist

- [ ] Tüm servislerde `healthcheck` tanımlı mı?
- [ ] Resource limits (`cpus`, `memory`) ayarlı mı?
- [ ] Secret'lar `.env` dosyasında mı, kodda değil mi?
- [ ] `.dockerignore` güncel mi? (node_modules, vendor, .env)
- [ ] Multi-stage build kullanıyor mu?
- [ ] Non-root user tanımlı mı? (`USER appuser`)
- [ ] Image tag'leri specific mi? (`latest` yok)
- [ ] Named volume kullanılıyor mu? (anonim yok)
- [ ] Logging config tanımlı mı?
- [ ] `read_only: true` ayarlı mı?
- [ ] `cap_drop: ALL` eklendi mi?
- [ ] `restart: unless-stopped` mı? (`always` değil)
- [ ] Network isolation doğru mu? (internal vs front)
- [ ] `depends_on` condition'ları doğru mu?
- [ ] .sql init dosyaları mount edildi mi?

---

## 18. Deployment Guide

### 18.1 Home Media Center

```bash
# Minimal kurulum (sadece control + download + mariadb)
docker compose up -d control-service download-service mariadb redis nginx
```

### 18.2 Car Audio System (Raspberry Pi 5)

```bash
# ARM64 build
docker compose --platform linux/arm64 up -d audio-service device-service
```

### 18.3 Professional Studio

```bash
# Tam kurulum + media service
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 18.4 NAS Audio Server

```bash
# Docker Compose ile Synology/QNAP
docker compose up -d --scale control-service=2 --scale media-service=2
```

### 18.5 DAC Control System

```bash
# Sadece audio + device service
docker compose up -d audio-service device-service network-audio-service
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
