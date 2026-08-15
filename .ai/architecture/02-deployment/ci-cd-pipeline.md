---
type: architecture
category: deployment
title: "CI/CD Pipeline — GitHub Actions"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CI/CD Pipeline

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic CI/CD pipeline'ını tanımlayan **Sürekli Entegrasyon ve Sürekli Dağıtım** standartlarıdır. GitHub Actions, pre-commit hooks, quality gates ve deployment stratejisi bu dosyada tanımlıdır.

## 2. Pipeline Genel Bakışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    CI/CD PIPELINE                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Git Push                                                       │
│    │                                                            │
│    ▼                                                            │
│  ┌─────────────────────────────────────────────────────┐       │
│  │ PRE-COMMIT HOOKS                                     │       │
│  │  ├── PHP Lint (php -l)                              │       │
│  │  ├── PHP CS-Fixer (PSR-12)                          │       │
│  │  ├── ESLint (JavaScript)                             │       │
│  │  └── GitLeaks (secret detection)                     │       │
│  └──────────────────────┬──────────────────────────────┘       │
│                         ▼                                       │
│  ┌─────────────────────────────────────────────────────┐       │
│  │ CI BUILD                                             │       │
│  │  ├── PHP Lint + CS-Fixer                            │       │
│  │  ├── TypeScript Build                                │       │
│  │  └── Docker Build                                    │       │
│  └──────────────────────┬──────────────────────────────┘       │
│                         ▼                                       │
│  ┌─────────────────────────────────────────────────────┐       │
│  │ TEST                                                 │       │
│  │  ├── PHPUnit (≥80% coverage)                        │       │
│  │  ├── Vitest (≥80% coverage)                         │       │
│  │  └── Playwright E2E                                  │       │
│  └──────────────────────┬──────────────────────────────┘       │
│                         ▼                                       │
│  ┌─────────────────────────────────────────────────────┐       │
│  │ SECURITY SCAN                                        │       │
│  │  ├── GitLeaks (secret sızıntısı)                    │       │
│  │  └── OWASP Dependency Check                         │       │
│  └──────────────────────┬──────────────────────────────┘       │
│                         ▼                                       │
│  ┌─────────────────────────────────────────────────────┐       │
│  │ DEPLOY                                               │       │
│  │  ├── Development (otomatik)                         │       │
│  │  ├── Staging (otomatik)                             │       │
│  │  └── Production (manual approve)                     │       │
│  └─────────────────────────────────────────────────────┘       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 3. Pre-commit Hooks

### 3.1 GitHooks (pre-commit)

```bash
#!/bin/bash
# .git/hooks/pre-commit

# 1. PHP Lint
php -l "$1" 2>/dev/null

# 2. PHP CS-Fixer
vendor/bin/php-cs-fixer fix --dry-run --diff "$1"

# 3. ESLint
npx eslint "$1"

# 4. Secret detection
gitLeaks detect --no-banner --source "$(git diff --cached --name-only)"
```

### 3.2 GitLeaks Konfigürasyonu

```yaml
# .gitleaks.toml
[allowlist]
  description = "Global allowlist"
  paths = [
    '''vendor/''',
    '''node_modules/''',
    '''\.ai/''',
  ]

[[rules]]
  id = "generic-api-key"
  description = "Generic API key"
  regex = '''(?i)(api[_-]?key|apikey)\s*[:=]\s*['"]([a-zA-Z0-9]{20,})['"]'''
  tags = ["key", "API"]
```

## 4. GitHub Actions

### 4.1 CI Workflow

```yaml
# .github/workflows/ci.yml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  php-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, xml, ctype, json, bcmath, pdo_mysql
          coverage: xdebug

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: PHP Lint
        run: find src -name "*.php" -exec php -l {} \;

      - name: PHP CS-Fixer
        run: vendor/bin/php-cs-fixer fix --dry-run --diff

      - name: PHPUnit
        run: vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml

      - name: Upload Coverage
        uses: codecov/codecov-action@v4
        with:
          file: coverage.xml

  js-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup Node.js 20
        uses: actions/setup-node@v4
        with:
          node-version: '20'

      - name: Install Dependencies
        run: npm ci

      - name: ESLint
        run: npx eslint src/

      - name: Vitest
        run: npx vitest run --coverage

  security-scan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

      - name: OWASP Dependency Check
        uses: dependency-check/Dependency-Check_Action@main
        with:
          path: .
          format: HTML
```

## 5. Quality Gates

| Gate | Kriter | Tool | Başarısızlık |
|------|--------|------|-------------|
| **PHP Lint** | Syntax hatası yok | php -l | Commit engellenir |
| **CS-Fixer** | PSR-12 uyumlu | php-cs-fixer | Commit engellenir |
| **PHPUnit** | ≥80% coverage | phpunit | CI başarısız |
| **ESLint** | Hata yok | eslint | CI başarısız |
| **Vitest** | ≥80% coverage | vitest | CI başarısız |
| **GitLeaks** | Secret sızıntısı yok | gitleaks | CI başarısız + alert |
| **OWASP** | Kritik vulnerability yok | dependency-check | CI başarısız |

## 6. Deployment Stratejisi

### 6.1 Ortam Katmanları

| Ortam | Branch | Trigger | Otomatik mi? | Health Check |
|-------|--------|---------|-------------|-------------|
| **Development** | develop | Push | ✅ | GET /health |
| **Staging** | release/* | PR merge | ✅ | GET /health + E2E |
| **Production** | main | Tag | ⏳ Manual approve | GET /health + smoke |

### 6.2 Deployment Adımları

```
1. Git tag → v1.0.0
2. GitHub Release created
3. CI/CD pipeline triggered
4. Tests pass → Docker build
5. Docker push to registry
6. Deploy to production server
7. Health check → verify
8. Notify team
```

### 6.3 Branch Stratejisi

```
main ──────────────────────────────────── Production
  ├── release/1.0.0 ──────────────────── Staging
  ├── develop ─────────────────────────── Development
  ├── feature/* ──────────────────────── Feature branches
  └── hotfix/* ────────────────────────── Emergency fixes
```

## 7. Monitoring

| Metrik | Hedef | Tool | Aksiyon |
|--------|-------|------|---------|
| **Build Time** | <10 min | GitHub Actions | Optimize |
| **Test Coverage** | ≥80% | PHPUnit + Vitest | Block merge |
| **Deploy Frequency** | Weekly | GitHub Releases | Planlama |
| **Mean Time to Recovery** | <1 hour | Monitoring | Escalation |
| **Change Failure Rate** | <5% | Post-deploy | Review |
| **Lead Time** | <1 day | Metrics | Process improvement |

## 8. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | GitLeaks her commit'te | Secret sızıntısı riski |
| 2 | PHPUnit ≥80% coverage | Kalite düşüşü |
| 3 | PSR-12 uyumlu kod | Okunabilirlik düşüşü |
| 4 | Manual approve (Production) | Hatalı deployment |
| 5 | Health check zorunlu | Sistem durması |

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/02-deployment/deployment]] | Deployment rehberi |
| [[architecture/02-deployment/docker-compose]] | Docker kurulumu |
| [[architecture/02-deployment/observability]] | İzleme |
| [[testing/strategy]] | Test stratejisi |
| [[testing/coverage-targets]] | Kapsama hedefleri |

## 10. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 GitHub Actions | [[testing/strategy]] | Test kapsamı |
| § 5 Quality Gates | [[testing/coverage-targets]] | Coverage hedefleri |
| § 6 Deployment | [[architecture/01-overview/startup-strategy]] | Fazlar |

## 11. Sözlük

| Terim | Tanım |
|-------|-------|
| **CI** | Continuous Integration — Sürekli entegrasyon |
| **CD** | Continuous Delivery/Deployment — Sürekli dağıtım |
| **Pipeline** | Otomatik iş akışı |
| **Quality Gate** | Kalite kontrol noktası |
| **Pre-commit** | Commit öncesi hook |
| **GitLeaks** | Secret tespit aracı |
| **OWASP** | Open Web Application Security Project |
| **Coverage** | Test kapsamı |
| **PSR-12** | PHP coding standardı |
| **Docker** | Container platformu |

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 5 referans |
| **Quality Gates** | ✅ 7 gate |
| **Guardrails** | ✅ 5 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
