---
title: "Docker Multi-Stage Build"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Docker Multi-Stage Build

## Genel Bakış

COREMUSIC projesi, Docker multi-stage build stratejisi kullanarak minimum boyutta production-ready container image'lar oluşturur. Multi-stage build, build-time bağımlılıkları runtime image'dan ayırarak attack surface'ı küçültür ve image boyutunu optimize eder. Her stage ayrı bir amaç için optimize edilmiştir.

## Pipeline Akışı

```
Dockerfile → Stage 1 (Composer Install) → Stage 2 (Node Build) → Stage 3 (PHP Runtime) → Final Image → Registry Push
```

## Teknik Detaylar

### Multi-Stage Build Yapısı

```dockerfile
# Stage 1: Dependencies
FROM composer:2.7 AS composer-deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Stage 2: Frontend Build
FROM node:20-alpine AS frontend-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --production=false
COPY resources/ ./resources/
RUN npm run build

# Stage 3: PHP Extensions
FROM php:8.3-fpm-alpine AS php-extensions
RUN apk add --no-cache \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install zip mbstring opcache pcntl

# Stage 4: Production Runtime
FROM php:8.3-fpm-alpine AS production
LABEL maintainer="COREMUSIC Team"
LABEL org.opencontainers.image.source="https://github.com/coremusic/coremusic"

# Security: Non-root user
RUN addgroup -g 1000 -S coremusic && \
    adduser -u 1000 -S coremusic -G coremusic

WORKDIR /var/www/html

# Copy only what's needed
COPY --from=composer-deps /app/vendor ./vendor
COPY --from=frontend-build /app/public/build ./public/build
COPY --from=php-extensions /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

COPY . .

RUN chown -R coremusic:coremusic /var/www/html

USER coremusic

EXPOSE 9000
CMD ["php-fpm"]
```

### Image Optimization Teknikleri

1. **Alpine Base**: `php:8.3-fpm-alpine` ~50MB vs ~500MB Debian-based
2. **Layer Caching**: Değişmeyen katmanlar üstte, sık değişen katmanlar altta
3. **Multi-stage**: Build bağımlılıkları runtime'da yok
4. **`.dockerignore`**: Gereksiz dosyaları exclude etme
5. **OPcache**: PHP bytecode caching ile startup süresini azaltma

### Docker Build Arguments

```dockerfile
ARG PHP_VERSION=8.3
ARG NODE_VERSION=20
ARG COMPOSER_VERSION=2.7

FROM php:${PHP_VERSION}-fpm-alpine AS production
```

### Health Check Tanımı

```dockerfile
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:9000/health || exit 1
```

### BuildKit Optimizasyonları

```bash
# Cache mount ile dependency install hızlandırma
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --optimize-autoloader

# Secret mount ile credential koruma
RUN --mount=type=secret,id=composer_auth \
    composer install --auth "$(cat /run/secrets/composer_auth)"
```

### Image Boyut Karşılaştırması

| Image Strategy          | Boyut     | Attack Surface |
|------------------------|-----------|----------------|
| Single-stage Debian    | ~850MB    | Yüksek         |
| Single-stage Alpine    | ~350MB    | Yüksek         |
| Multi-stage Alpine     | ~180MB    | Düşük          |
| Multi-stage + Distroless | ~150MB | Minimum        |

### Container Runtime Optimizasyonları

```ini
# php.ini optimization for container
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0

# PM configuration
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
```

### Security Hardening

```dockerfile
# Read-only filesystem
RUN chmod -R 555 /var/www/html

# Remove unnecessary packages
RUN apk del --purge \
    && rm -rf /var/cache/apk/* /tmp/*

# Non-root execution
USER 1000:1000

# No new privileges
SecurityOpt:
  - no-new-privileges:true
```

## Konfigürasyon

### .dockerignore

```gitignore
.git
.github
.vscode
.idea
node_modules
vendor
tests
*.md
docker-compose*.yml
.env*
Dockerfile*
```

### docker-compose.yml (Development)

```yaml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
      target: production
    ports:
      - "8000:9000"
    volumes:
      - .:/var/www/html
      - vendor-data:/var/www/html/vendor
    environment:
      - APP_ENV=local
      - APP_DEBUG=true

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: coremusic
      MYSQL_ROOT_PASSWORD: secret
    volumes:
      - mysql-data:/var/lib/mysql

volumes:
  vendor-data:
  mysql-data:
```

### Build Pipeline Integration

```yaml
# GitHub Actions Docker Build
- name: Build and push Docker image
  uses: docker/build-push-action@v5
  with:
    context: .
    file: ./Dockerfile
    push: true
    tags: |
      ghcr.io/coremusic/coremusic:${{ github.sha }}
      ghcr.io/coremusic/coremusic:latest
    cache-from: type=gha
    cache-to: type=gha,mode=max
    build-args: |
      PHP_VERSION=8.3
      NODE_VERSION=20
```

## Bağımlılıklar

- `docker/buildx`: Multi-platform build desteği
- `docker/compose`: Local development orchestration
- `ghcr.io`: Container registry
- `docker/scout`: Image vulnerability scanning

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Multi-Stage Build    | Hazır       | 2026-09-20      |
| Alpine Optimization  | Hazır       | 2026-09-20      |
| Health Check         | Hazır       | 2026-09-20      |
| Security Hardening   | Hazır       | 2026-09-20      |
| Cache Optimization   | Hazır       | 2026-09-20      |
