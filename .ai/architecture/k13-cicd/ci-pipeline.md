---
title: "Continuous Integration Pipeline"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Continuous Integration Pipeline

## Genel Bakış

COREMUSIC Continuous Integration pipeline'ı, her kod push'unda ve pull request'te otomatik olarak çalışan kalite kontrol süreçlerini yönetir. Lint, test, security scan ve build adımları paralel ve seri olarak çalışır. Pipeline, fail-fast stratejisi ile erken hata tespiti sağlar ve developer feedback süresini minimize eder.

## Pipeline Akışı

```
Push/PR → Parallel Jobs → [Lint, Unit Test, Security Scan] → Gate Check → Build Docker Image → Cache Artifacts → Report
```

## Teknik Detaylar

### CI Pipeline Stages

```yaml
# .github/workflows/ci.yml
name: CI Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

env:
  PHP_VERSION: '8.3'
  NODE_VERSION: '20'

jobs:
  # Stage 1: Code Quality
  lint:
    name: Code Linting
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          tools: phpcs, phpstan, psalm
      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress
      - name: Run PHPCS (PSR-12)
        run: vendor/bin/phpcs --standard=PSR12 --report=checkstyle src/ || true
      - name: Run PHPStan (Level 8)
        run: vendor/bin/phpstan analyse --memory-limit=2G
      - name: Run Psalm
        run: vendor/bin/psalm --show-info=true

  # Stage 2: Unit Tests
  unit-test:
    name: Unit Tests (PHP ${{ matrix.php-version }})
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php-version: ['8.2', '8.3']
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          coverage: xdebug
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
      - name: Run PHPUnit
        run: |
          vendor/bin/phpunit --testsuite=Unit \
            --coverage-clover=coverage-unit.xml \
            --coverage-text
      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: coverage-unit.xml
          flags: unit-${{ matrix.php-version }}

  # Stage 3: Integration Tests
  integration-test:
    name: Integration Tests
    runs-on: ubuntu-latest
    needs: unit-test
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: testing
          MYSQL_DATABASE: coremusic_test
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=5
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          extensions: mbstring, xml, ctype, json, bcmath, pdo_mysql
          coverage: xdebug
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
      - name: Run migrations
        run: php artisan migrate --force
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_DATABASE: coremusic_test
          DB_USERNAME: root
          DB_PASSWORD: testing
      - name: Run Integration tests
        run: |
          vendor/bin/phpunit --testsuite=Integration \
            --coverage-clover=coverage-integration.xml
      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: coverage-integration.xml
          flags: integration

  # Stage 4: Frontend Build
  frontend:
    name: Frontend Build
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: ${{ env.NODE_VERSION }}
          cache: 'npm'
      - name: Install dependencies
        run: npm ci
      - name: Run ESLint
        run: npm run lint
      - name: Run type check
        run: npm run typecheck
      - name: Build assets
        run: npm run build
      - name: Upload build artifacts
        uses: actions/upload-artifact@v3
        with:
          name: frontend-build
          path: public/build/

  # Stage 5: Security Scan
  security:
    name: Security Scan
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Run Composer Audit
        run: composer audit --format=json > composer-audit.json || true
      - name: Run Semgrep
        uses: returntocorp/semgrep-action@v1
        with:
          config: .semgrep.yml
      - name: Run Gitleaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

  # Stage 6: Build Docker Image
  build:
    name: Build Docker Image
    runs-on: ubuntu-latest
    needs: [lint, unit-test, integration-test, frontend, security]
    steps:
      - uses: actions/checkout@v4
      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3
      - name: Login to GHCR
        uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}
      - name: Build and push
        uses: docker/build-push-action@v5
        with:
          context: .
          push: true
          tags: |
            ghcr.io/coremusic/coremusic:${{ github.sha }}
            ghcr.io/coremusic/coremusic:ci-${{ github.run_id }}
          cache-from: type=gha
          cache-to: type=gha,mode=max
          build-args: |
            PHP_VERSION=${{ env.PHP_VERSION }}
            NODE_VERSION=${{ env.NODE_VERSION }}
```

### Quality Gates

```yaml
# quality-gate.yml
gates:
  lint:
    required: true
    tools:
      phpcs:
        standard: PSR12
        max_errors: 0
      phpstan:
        level: 8
        max_errors: 0

  test:
    required: true
    coverage:
      unit: 90
      integration: 70
      overall: 80
    min_tests: 100

  security:
    required: true
    composer_audit:
      max_critical: 0
      max_high: 0
    semgrep:
      max_critical: 0
      max_high: 5
```

### Caching Strategy

```yaml
# Multi-layer caching
- name: Cache Composer
  uses: actions/cache@v3
  with:
    path: vendor
    key: composer-${{ hashFiles('composer.lock') }}
    restore-keys: composer-

- name: Cache Node modules
  uses: actions/cache@v3
  with:
    path: node_modules
    key: npm-${{ hashFiles('package-lock.json') }}
    restore-keys: npm-

- name: Cache Docker layers
  uses: actions/cache@v3
  with:
    path: /tmp/.buildx-cache
    key: docker-${{ hashFiles('Dockerfile') }}
    restore-keys: docker-
```

### CI Metrics Collection

```yaml
# Track CI performance
- name: Track CI metrics
  uses: actions/github-script@v6
  with:
    script: |
      const duration = Date.now() - context.payload.before;
      core.setOutput('ci_duration', duration);

      // Post to metrics endpoint
      await fetch('https://metrics.coremusic.example.com/ci', {
        method: 'POST',
        body: JSON.stringify({
          run_id: context.runId,
          duration: duration,
          status: context.payload.action,
          commit: context.sha
        })
      });
```

## Konfigürasyon

### PR Check Requirements

```yaml
# Branch protection - required checks
checks:
  - "lint"
  - "unit-test (8.3)"
  - "integration-test"
  - "frontend"
  - "security"
  - "build"
```

### CI Timeout Configuration

```yaml
timeouts:
  lint: 5m
  unit-test: 10m
  integration-test: 15m
  frontend: 10m
  security: 10m
  build: 20m
  total: 60m
```

## Bağımlılıklar

- `actions/checkout@v4`: Repository checkout
- `shivammathur/setup-php@v2`: PHP setup
- `actions/setup-node@v4`: Node.js setup
- `docker/build-push-action@v5`: Docker build
- `codecov/codecov-action@v3`: Coverage reporting

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Lint Pipeline        | Hazır       | 2026-09-20      |
| Unit Test Pipeline   | Hazır       | 2026-09-20      |
| Integration Pipeline | Hazır       | 2026-09-20      |
| Frontend Pipeline    | Hazır       | 2026-09-20      |
| Security Pipeline    | Hazır       | 2026-09-20      |
| Docker Build         | Hazır       | 2026-09-20      |
| Quality Gates        | Hazır       | 2026-09-20      |
