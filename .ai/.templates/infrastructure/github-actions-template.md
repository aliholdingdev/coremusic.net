---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — GitHub Actions CI/CD Template"
type: cicd-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Platform:** GitHub Actions
**Kapsam:** CI/CD Pipeline
**Branch:** main, develop

---

## 1. CI Pipeline (.github/workflows/ci.yml)

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

## 2. CD Pipeline (.github/workflows/deploy.yml)

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

## 3. Branch Protection

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

## 4. Secrets Yönetimi

| Secret | Amaç |
|--------|------|
| `SERVER_HOST` | Production sunucu |
| `SERVER_USER` | SSH kullanıcı |
| `SSH_KEY` | SSH private key |
| `DB_PASSWORD` | Veritabanı şifresi |
| `CSRF_SECRET` | CSRF token secret |
| `JWT_SECRET` | JWT signing key |

---

*GitHub Actions CI/CD Template v1.0.0 — CoreMusic DevOps Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
