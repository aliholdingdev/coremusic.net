---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — GitHub Actions CI/CD Template"
type: cicd-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — GitHub Actions CI/CD Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic CI/CD pipeline'ını standartlaştırmaktır: CI pipeline (PHP lint/test, JS lint/test, security audit), CD pipeline (deploy), branch protection ayarları ve secrets yönetimi için hazır YAML iskeletleri sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.github/workflows/*.yml` CI/CD pipeline tanımları | Uygulama kodu (`.php`, `.js`) |
| `.github/settings.yml` branch protection | Veritabanı migration içeriği (bkz. migration-template) |
| Secrets envanteri ve deploy adımları | Donanım/firmware (bkz. hardware-template) |

- **Dosya tipi:** YAML workflow + Markdown doküman
- **Kullanan agent:** DevOps Engineer (birincil · AGENTS.md §6), QA Engineer (ikincil — test job'ları)
- **Branch:** main, develop · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); YAML `on:` / `jobs:` / `steps:` blokları ve tüm `${{ ... }}` GitHub ifadeleri **bozulmadan** korunmuştur. YAML içindeki `{{...}}` ifadeleri GitHub expression'ıdır — doldurulacak şablon placeholder'ı değildir. Secrets kuralları §4.1'dedir.

### {{TITLE}}

**Platform:** GitHub Actions
**Kapsam:** CI/CD Pipeline
**Branch:** main, develop

---

#### 3.1 CI Pipeline (.github/workflows/ci.yml)

```yaml
name: CI Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

env:
  PHP_VERSION: '8.4'
  NODE_VERSION: '20'

jobs:
  php-lint:
    name: PHP Lint
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          tools: php-cs-fixer, phpstan
      - name: Run PHP CS Fixer
        run: php-cs-fixer fix --dry-run --diff
      - name: Run PHPStan
        run: phpstan analyse src --level=8

  php-test:
    name: PHP Tests
    runs-on: ubuntu-latest
    needs: php-lint
    services:
      mysql:
        image: mysql:9
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: coremusic_test
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          extensions: pdo, pdo_mysql
          coverage: xdebug
      - name: Install Dependencies
        run: composer install --no-progress --prefer-dist
      - name: Run Tests
        run: vendor/bin/phpunit --coverage-clover=coverage.xml
      - name: Upload Coverage
        uses: codecov/codecov-action@v4
        with:
          file: coverage.xml

  js-lint:
    name: JavaScript Lint
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: ${{ env.NODE_VERSION }}
          cache: 'npm'
      - run: npm ci
      - run: npm run lint

  js-test:
    name: JavaScript Tests
    runs-on: ubuntu-latest
    needs: js-lint
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: ${{ env.NODE_VERSION }}
          cache: 'npm'
      - run: npm ci
      - run: npm run test:coverage

  security:
    name: Security Audit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
      - name: Composer Audit
        run: composer audit
      - name: npm Audit
        run: npm audit --audit-level=high
      - name: GitLeaks Scan
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

---

#### 3.2 CD Pipeline (.github/workflows/deploy.yml)

```yaml
name: Deploy

on:
  push:
    branches: [main]
  workflow_dispatch:

env:
  DEPLOY_PATH: /var/www/coremusic.net

jobs:
  deploy:
    name: Deploy to Production
    runs-on: ubuntu-latest
    environment: production
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'

      - name: Install Dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Run Migrations
        run: vendor/bin/phinx migrate --environment production

      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd ${{ env.DEPLOY_PATH }}
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan cache:clear
            php artisan config:cache
```

---

#### 3.3 Branch Protection

```yaml
# .github/settings.yml (Probot)
branches:
  main:
    protection:
      required_status_checks:
        strict: true
        contexts:
          - PHP Lint
          - PHP Tests
          - JavaScript Lint
          - JavaScript Tests
          - Security Audit
      required_pull_request_reviews:
        required_approving_review_count: 1
      restrictions: null
      enforce_admins: false
```

---

## 4. Kurallar

Zorunlu / yasak kurallar ve kod standartları:

- **Zorunlu:** YAML `on:`, `jobs:`, `steps:` blokları kopyalanırken **bozulmaz**; girinti ve `uses:`/`run:` sırası korunur.
- **Zorunlu:** YAML içindeki `${{ env.* }}` / `${{ secrets.* }}` ifadeleri GitHub expression'ıdır; `{{PLACEHOLDER}}` gibi doldurulmaz, aynen kalır.
- **Zorunlu:** §3.3 branch protection `contexts` listesi §3.1'deki job adlarıyla (PHP Lint, PHP Tests, JavaScript Lint, JavaScript Tests, Security Audit) birebir eşleşir.
- **Zorunlu:** test coverage işi `vendor/bin/phpunit --coverage-clover=coverage.xml` üretir; hedef coverage ≥80% (AGENTS.md §16).
- **Yasak:** secret değerleri YAML'e gömülmez, log'a yazılmaz — yalnızca §4.1 tablosundaki adlar `secrets.*` üzerinden kullanılır.
- **Yasak:** `production` environment'sız deploy job'ı yok; `environment: production` korunur.
- **Uyarı:** SSH script'indeki `php artisan migrate/cache:clear/config:cache` adımları özgün şablondadır; bu proje Phinx kullandığından proje gerçeğiyle uyumu **⚠️ VERIFICATION REQUIRED**.

#### 4.1 Secrets Yönetimi

| Secret | Amaç |
|--------|------|
| `SERVER_HOST` | Production sunucu |
| `SERVER_USER` | SSH kullanıcı |
| `SSH_KEY` | SSH private key |
| `DB_PASSWORD` | Veritabanı şifresi |
| `CSRF_SECRET` | CSRF token secret |
| `JWT_SECRET` | JWT signing key |

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/infrastructure/github-actions-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1 → `.github/workflows/ci.yml`, §3.2 → `.github/workflows/deploy.yml`, §3.3 → `.github/settings.yml`.
3. **{{PLACEHOLDER}} DOLDUR:** yalnızca `{{TITLE}}` (§ başlığı) ve varsa gerçek proje değerleri; `${{ ... }}` expression'larına dokunma.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + YAML syntax doğrulandı (`on:`/`jobs:`/`steps:` bozulmadı) + §4.1 secret adları kayıtlı.
5. **COMMIT:** workflow dosyalarını commit et; pipeline ilk çalıştırmada GitLeaks + `composer audit` + `npm audit` temizliğini doğrula, `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] YAML `on:`/`jobs:`/`steps:` + `${{ }}` ifadeleri bozulmadı; branch protection contexts job adlarıyla eşleşiyor

**REFACTOR REPORT:** FILE: github-actions-template.md · PURPOSE: GitHub Actions CI/CD Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: CI/CD → DevOps Engineer, ikincil QA Engineer), kalite standardı §16 (CI/CD success ≥95%, GitLeaks clean)
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*GitHub Actions CI/CD Template v2.0.0 — CoreMusic DevOps Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
