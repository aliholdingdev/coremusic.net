---
title: "K13 — CI/CD & Deployment Katmanı"
type: architecture
category: cicd
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
reference:
  adr: "N/A — Cross-cutting concern"
  github:
    - name: "GitHub Actions"
      url: "https://github.com/features/actions"
    - name: "Docker"
      url: "https://github.com/docker"
    - name: "Kubernetes"
      url: "https://github.com/kubernetes/kubernetes"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K13 — CI/CD & Deployment Katmanı

CoreMusic ekosistemi için sürekli entegrasyon, sürekli dağıtım ve altyapı otomasyon katmanı. 35 bileşen.

## Genel Bakış

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                        K13 — CI/CD & DEPLOYMENT                              │
├──────────────┬──────────────┬──────────────┬────────────────────────────────┤
│   SOURCE     │   BUILD      │   TEST       │   DEPLOY                      │
│              │              │              │                                │
│  Git Push    │  PHPStan     │  PHPUnit     │  Docker Build                  │
│  Pre-commit  │  PHP CS Fix  │  Vitest      │  Docker Compose                │
│  GitLeaks    │  Rector      │  Playwright  │  Docker Registry               │
│  Branch Prot │  Deptrac     │  Visual Test │  Kubernetes Deploy             │
│              │  Comp Audit  │  NPM Audit   │  K8s Scale                     │
│              │              │              │  Blue-Green Deploy              │
│              │              │              │  Canary Deploy                  │
│              │              │              │  Rollback                       │
│              │              │              │  IaC (Terraform)               │
└──────────────┴──────────────┴──────────────┴────────────────────────────────┘
```

## Bileşen Listesi (35)

| # | Bileşen | Alt Bileşenler | Teknoloji | Kapsam |
|---|---------|---------------|-----------|--------|
| 1 | GitHub Actions CI/CD | Workflow YAML, matrix builds, caching, artifacts | GitHub Actions | Ana CI/CD |
| 2 | GitLeaks Pre-commit | Secret scanning, entropy check, pattern match | GitLeaks | Güvenlik |
| 3 | PHPStan Static Analysis | Level 0-10, baseline, ignore errors, extensions | PHPStan | Kod kalitesi |
| 4 | PHP CS Fixer | PSR-12, custom rules, dry-run, auto-fix | PHP CS Fixer | Kod formatlama |
| 5 | Rector Refactor | PHP 8.4 upgrade, dead code removal, naming | Rector | Kod modernizasyonu |
| 6 | PHPUnit Unit Tests | Unit tests, mocks, data providers, coverage | PHPUnit 11 | Birim testi |
| 7 | PHPUnit Integration | DB tests, API tests, service tests | PHPUnit 11, TestDB | Entegrasyon testi |
| 8 | Vitest Unit Tests | JS unit tests, mocking, snapshots | Vitest | JS birim testi |
| 9 | Vitest Component | Component testing, DOM interaction | Vitest + jsdom | Bileşen testi |
| 10 | Playwright E2E | Browser automation, multi-browser, screenshots | Playwright | Uçtan uca test |
| 11 | Playwright Visual | Visual regression, screenshot comparison | Playwright | Görsel test |
| 12 | Deptrac | Dependency architecture, layer rules, cycles | Deptrac | Mimari koruma |
| 13 | Composer Audit | Dependency vulnerabilities, advisories | Composer | Güvenlik denetimi |
| 14 | NPM Audit | JS dependency vulnerabilities | NPM | Güvenlik denetimi |
| 15 | Docker Build | Multi-stage, layer caching, slim images | Docker 24+ | Konteyner oluşturma |
| 16 | Docker Compose | Multi-service, volumes, networks, env | Docker Compose v2 | Lokal geliştirme |
| 17 | Docker Registry | Container registry, image versioning, cleanup | GHCR / Docker Hub | Konteyner depolama |
| 18 | Kubernetes Deploy | Deployment, Service, Ingress, ConfigMap | Kubernetes | Orkestrasyon |
| 19 | Kubernetes Scale | HPA, VPA, cluster autoscaler, replicas | Kubernetes | Ölçekleme |
| 20 | Nginx Config | Reverse proxy, SSL, caching, rate limiting | Nginx | Web sunucusu |
| 21 | PHP-FPM Config | Pool tuning, opcache, process manager | PHP-FPM | PHP çalıştırma |
| 22 | MySQL Config | InnoDB tuning, replication, backup | MySQL 9 | Veritabanı |
| 23 | Redis Config | Persistence, eviction, replication, cluster | Redis | Önbellek |
| 24 | Blue-Green Deploy | Traffic switch, zero-downtime, health check | Custom + K8s | Deployment stratejisi |
| 25 | Canary Deploy | Gradual rollout, metric monitoring, rollback | Custom + K8s | Deployment stratejisi |
| 26 | Rollback | Instant rollback, database migration rollback | Custom + K8s | Hata kurtarma |
| 27 | Infrastructure as Code | Terraform, Pulumi, Ansible | Terraform | Altyapı yönetimi |
| 28 | Secret Management | Vault, sealed secrets, env injection | HashiCorp Vault | Sır yönetimi |
| 29 | Artifact Management | Build artifacts, binary cache, release packages | GitHub Releases | Artefact yönetimi |
| 30 | Release Management | Semantic versioning, changelog, release notes | Conventional Commits | Sürüm yönetimi |
| 31 | Branch Protection | Required reviews, status checks, signed commits | GitHub Settings | Dal koruma |
| 32 | Code Review Automation | PR templates, auto-labeling, review assignments | GitHub Actions | Kod inceleme |
| 33 | Deployment Notification | Slack/Email deploy status, rollback alerts | Custom + Slack | Bildirim |
| 34 | Environment Management | Dev/staging/prod, feature flags, env vars | Custom + K8s | Ortam yönetimi |
| 35 | Post-deploy Validation | Smoke tests, health checks, smoke metrics | Playwright + Custom | Doğrulama |

## Pipeline Akışı

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                         CI/CD PIPELINE FLOW                                  │
│                                                                              │
│  ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    │
│  │  PUSH   │───▶│  BUILD  │───▶│  TEST   │───▶│ DEPLOY  │───▶│VALIDATE │    │
│  │         │    │         │    │         │    │         │    │         │    │
│  │ GitPush │    │ Compose │    │ PHPUnit │    │ Docker  │    │ Smoke   │    │
│  │ Pre-comm│    │ PHPStan │    │ Vitest  │    │ K8s     │    │ Health  │    │
│  │ GitLeaks│    │ CS Fix  │    │ Playwr. │    │ Nginx   │    │ Monitor │    │
│  └─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘    │
│       │              │              │              │              │            │
│       ▼              ▼              ▼              ▼              ▼            │
│  ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    │
│  │Secret   │    │Artifact │    │Coverage │    │Blue-    │    │Rollback │    │
│  │Check    │    │Cache    │    │Report   │    │Green    │    │if Fail  │    │
│  └─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘    │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Deployment Stratejileri

### Blue-Green Deployment

```
┌─────────────────────────────────────────────────────┐
│                  BLUE-GREEN DEPLOY                   │
│                                                      │
│  ┌──────────┐         ┌──────────┐                   │
│  │   BLUE   │◀─100%──│  Router  │                   │
│  │ (Current)│         │  (Nginx) │                   │
│  └──────────┘         └────┬─────┘                   │
│                            │                          │
│  ┌──────────┐              │                          │
│  │  GREEN   │◀──── 0% ────┘                          │
│  │  (New)   │                                        │
│  └──────────┘                                        │
│                                                      │
│  1. Deploy Green (0% traffic)                        │
│  2. Run smoke tests on Green                         │
│  3. Switch traffic: Blue → Green (100%)              │
│  4. Keep Blue as rollback target (24h)               │
│  5. Cleanup Blue after validation                    │
└─────────────────────────────────────────────────────┘
```

### Canary Deployment

```
┌─────────────────────────────────────────────────────┐
│                 CANARY DEPLOYMENT                    │
│                                                      │
│  ┌──────────┐         ┌──────────┐                   │
│  │  Stable  │◀──90%───│  Router  │                   │
│  │  (v1.0)  │         │  (Nginx) │                   │
│  └──────────┘         └────┬─────┘                   │
│                            │                          │
│  ┌──────────┐              │                          │
│  │  Canary  │◀──10% ──────┘                          │
│  │  (v1.1)  │                                        │
│  └──────────┘                                        │
│                                                      │
│  Aşama 1: 10% traffic → Canary (1h monitor)         │
│  Aşama 2: 25% traffic → Canary (2h monitor)         │
│  Aşama 3: 50% traffic → Canary (4h monitor)         │
│  Aşama 4: 100% traffic → Canary (promote to stable) │
│  Hata:    Rollback to stable (instant)               │
└─────────────────────────────────────────────────────┘
```

## GitHub Actions Workflow

```yaml
# .github/workflows/ci-cd.yml
name: CoreMusic CI/CD

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  # ─── FASE 1: Kaynak Kontrolü ───
  pre-commit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: GitLeaks Scan
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

  # ─── FASE 2: Build ───
  build:
    needs: pre-commit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, intl, sodium
          coverage: xdebug
      - name: Composer Install
        run: composer install --prefer-dist --no-progress
      - name: PHPStan Analysis
        run: vendor/bin/phpstan analyse --level=8 src/
      - name: PHP CS Fixer
        run: vendor/bin/php-cs-fixer fix --dry-run --diff

  # ─── FASE 3: Test ───
  test:
    needs: build
    runs-on: ubuntu-latest
    steps:
      - name: PHPUnit Tests
        run: vendor/bin/phpunit --coverage-clover=coverage.xml
      - name: Vitest Tests
        run: npx vitest run --coverage
      - name: Upload Coverage
        uses: codecov/codecov-action@v4

  # ─── FASE 4: Deploy ───
  deploy:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Docker Build
        run: docker build -t coremusic:${{ github.sha }} .
      - name: Docker Push
        run: docker push ghcr.io/coremusic/coremusic:${{ github.sha }}
      - name: Kubernetes Deploy
        run: kubectl set image deployment/coremusic coremusic=coremusic:${{ github.sha }}
```

## Yapılandırma Kalıpları

### Nginx Reverse Proxy

```nginx
# /etc/nginx/sites-available/coremusic.conf
upstream php_fpm {
    server unix:/var/run/php/php8.4-fpm.sock;
}

server {
    listen 443 ssl http2;
    server_name music.coremusic.net;

    ssl_certificate /etc/letsencrypt/live/coremusic.net/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/coremusic.net/privkey.pem;
    ssl_protocols TLSv1.3;

    # Security Headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Strict-Transport-Security "max-age=31536000" always;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php_fpm;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Docker Multi-Stage Build

```dockerfile
# Stage 1: Dependencies
FROM php:8.4-fpm-alpine AS deps
RUN docker-php-ext-install pdo pdo_mysql mbstring
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Production
FROM php:8.4-fpm-alpine AS production
COPY --from=deps /app/vendor /app/vendor
COPY . /app
RUN chown -R www-data:www-data /app
EXPOSE 9000
CMD ["php-fpm"]
```

### Kubernetes Deployment

```yaml
# k8s/deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-music
  labels:
    app: coremusic
    tier: frontend
spec:
  replicas: 3
  selector:
    matchLabels:
      app: coremusic-music
  template:
    metadata:
      labels:
        app: coremusic-music
    spec:
      containers:
      - name: music
        image: ghcr.io/coremusic/music:latest
        ports:
        - containerPort: 81
        resources:
          requests:
            memory: "128Mi"
            cpu: "100m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 81
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /ready
            port: 81
          initialDelaySeconds: 5
          periodSeconds: 5
```

## GitHub Referansları

| Proje | Amaç | Lisans |
|-------|------|--------|
| [features/actions](https://github.com/features/actions) | CI/CD otomasyon platformu | — |
| [docker/docker](https://github.com/docker) | Konteyner platformu | Apache-2.0 |
| [kubernetes/kubernetes](https://github.com/kubernetes/kubernetes) | Container orkestrasyonu | Apache-2.0 |

## İlişkili Katmanlar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K01 | Build hedefi | Uygulama kodu build edilir |
| K03 | Container hedefi | Uygulama konteynere paketlenir |
| K06 | Deployment hedefi | Container K8s'e deploy edilir |
| K12 | Log kaynağı | CI/CD logları K12'ye akar |
| K14 | Network kaynağı | Servisler arası ağ K14 tarafından yönetilir |

## Doğrulama

- [ ] GitHub Actions workflow'ları çalışıyor
- [ ] GitLeaks pre-commit hook çalışıyor
- [ ] PHPStan level 8 hatasız
- [ ] PHP CS Fixer dry-run temiz
- [ ] Rector upgrade kuralları uygulanmış
- [ ] PHPUnit coverage >= %80
- [ ] Vitest tüm testleri geçiriyor
- [ ] Playwright E2E testleri çalışıyor
- [ ] Deptrac layer kuralları korunuyor
- [ ] Composer audit temiz
- [ ] NPM audit temiz
- [ ] Docker image başarıyla build ediliyor
- [ ] Kubernetes deployment sağlıklı
- [ ] Blue-Green rollback çalışıyor
- [ ] Canary metric monitoring aktif

---

## Class AB CI/CD Entegrasyonu

- [[electronics/pcb-classab]] — PCB üretim pipeline'ı
- [[electronics/bom-classab]] — BOM tedarik zinciri

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
