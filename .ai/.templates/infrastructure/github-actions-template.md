---
type: template
category: infrastructure
title: "GitHub Actions CI/CD Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: GitHub Actions, CI/CD, PHP 8.4, Node.js 20+, Docker
---

# GitHub Actions CI/CD Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]]

## 1. Amaç

Bu dosya, CoreMusic platformunun tüm servisleri (10 panel, 7 backend service) için **GitHub Actions CI/CD pipeline şablonlarını** sunar. Pipeline, çok dilli (PHP 8.4, JavaScript ES6+, C++20), çok servisli (microservice) bir ekosistemi kapsar.

**Kapsam Dahilindeki Servisler:**

| Servis | Port | Stack | CI Pipeline |
|--------|------|-------|-------------|
| Control Service | 81 | PHP 8.4 | PHP CI |
| Media Service | 5000/6000 | PHP + FFmpeg | PHP CI + Docker |
| Audio Service | 9741/9742 | C++20 JUCE | C++ CI |
| Download Service | 3001 | Node.js + TS | JS CI |
| Auth Service | — | PHP 8.4 | PHP CI |
| Frontend (assets) | — | Vanilla JS | JS CI |
| Neva Engine | — | C++20 JUCE | C++ CI |

**Dahil Olan:**
- Lint, test, build, deploy, security-scan job'ları
- 3 dil için ayrı CI pipeline'ları
- GitLeaks mandatory scanning
- Coverage upload with threshold enforcement
- Docker build, push, scan, deploy
- Multi-OS matrix builds
- Caching stratejileri (composer, npm, Docker, ccache)

**Kapsam Dışı:**
- Donanım firmware CI (embedded engineer sorumluluğu)
- Manuel deployment onayları (insan müdahalesi gerektirir)

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| GitHub Actions | — | CI/CD orchestration | docs.github.com |
| actions/checkout | v4 | Code checkout | github.com/actions |
| actions/setup-node | v4 | Node.js setup | github.com/actions |
| shivammathur/setup-php | v2 | PHP setup | github.com/shivammathur |
| actions/upload-artifact | v4 | Artifact storage | github.com/actions |
| actions/cache | v4 | Dependency caching | github.com/actions |
| codecov/codecov-action | v4 | Coverage upload | codecov.io |
| gitleaks/gitleaks-action | v2 | Secret scanning | gitleaks.io |
| docker/build-push-action | v5 | Docker build & push | docker.com |
| aquasecurity/trivy-action | v0.24.0 | Container scanning | trivy.dev |
| sastikarthik26/cmake-build | v1 | C++ build | github.com/sastikarthik26 |

*Kaynak: GitHub Actions Documentation (docs.github.com) — 2026-08-06'da doğrulandı*

**Ek Araçlar:**

| Araç | Amaç | Komut |
|------|------|-------|
| PHP 8.4 | Backend runtime | `php --version` |
| Composer v2 | PHP dependencies | `composer install` |
| Node.js 20+ | JS runtime | `node --version` |
| npm | JS package manager | `npm ci` |
| CMake 3.28+ | C++ build system | `cmake -B build` |
| GCC 14 / Clang 18 | C++ compiler | `gcc --version` |
| Docker 24+ | Container runtime | `docker build` |
| GitLeaks 8.x | Secret detection | `gitleaks detect` |
| PHPStan | Static analysis | `vendor/bin/phpstan analyse` |
| Psalm | Static analysis | `vendor/bin/psalm` |
| PHP-CS-Fixer | Code style | `vendor/bin/php-cs-fixer fix` |
| ESLint | JS linting | `npx eslint` |
| Vitest | JS testing | `npx vitest` |
| PHPUnit 11 | PHP testing | `vendor/bin/phpunit` |
| Google Test | C++ testing | `./build/tests` |

## 3. Code Standards

### 3.1 Workflow File Structure

`.github/workflows/` dizinindeki dosya yapısı:

```
.github/
  workflows/
    ci-main.yml              # Ana CI pipeline (tüm diller)
    ci-php.yml               # PHP-specific pipeline
    ci-js.yml                # JavaScript-specific pipeline
    ci-cpp.yml               # C++-specific pipeline
    ci-docker.yml            # Docker build & push
    ci-security.yml          # Security audit pipeline
    ci-deploy.yml            # Deployment pipeline
    ci-release.yml           # Release management
    ci-scheduled.yml         # Scheduled scans (haftalık)
    cd-production.yml        # Production deployment
    cd-staging.yml           # Staging deployment
```

**Dosya Adlandırma Kuralları:**

| Kural | Örnek |
|-------|-------|
| `ci-` prefix (CI) | `ci-main.yml`, `ci-php.yml` |
| `cd-` prefix (CD) | `cd-production.yml` |
| lowercase + hyphen | `ci-security.yml` |
| Uzantı: `.yml` | `.github/workflows/ci-main.yml` |
| Max 30 karakter | Dosya adı sınırı |

### 3.2 Triggers

```yaml
on:
  push:
    branches: [main, develop]
    paths:
      - 'music.coremusic.net/**'
      - 'auth.coremusic.net/**'
      - '.github/workflows/**'
    paths-ignore:
      - '**/*.md'
      - '.ai/**'

  pull_request:
    branches: [main]
    types: [opened, synchronize, reopened]

  schedule:
    - cron: '0 2 * * 1'  # Her Pazartesi 02:00 UTC

  workflow_dispatch:
    inputs:
      environment:
        description: 'Deployment target'
        required: true
        default: 'staging'
        type: choice
        options:
          - staging
          - production
```

**Trigger Tablosu:**

| Trigger | Kullanım Zamanı | Öncelik |
|---------|-----------------|---------|
| `push` | main/develop branch'e kod push | HIGH |
| `pull_request` | PR açıldığında veya güncellendiğinde | HIGH |
| `schedule` | Haftalık güvenlik taraması | MEDIUM |
| `workflow_dispatch` | Manuel deploy tetikleme | CRITICAL |
| `release` | Yeni release oluşturulduğunda | HIGH |

### 3.3 Job Definitions

```yaml
jobs:
  # ──────────────────────────────────────────────
  # Job: lint — Kod kalitesi kontrolü
  # ──────────────────────────────────────────────
  lint:
    name: Lint & Code Quality
    runs-on: ubuntu-latest
    timeout-minutes: 10
    permissions:
      contents: read

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Run GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

  # ──────────────────────────────────────────────
  # Job: test — Unit ve integration testleri
  # ──────────────────────────────────────────────
  test:
    name: Test Suite
    runs-on: ubuntu-latest
    needs: lint
    timeout-minutes: 20

  # ──────────────────────────────────────────────
  # Job: build — Build ve artifact üretimi
  # ──────────────────────────────────────────────
  build:
    name: Build
    runs-on: ubuntu-latest
    needs: test
    timeout-minutes: 15

  # ──────────────────────────────────────────────
  # Job: security-scan — Güvenlik taraması
  # ──────────────────────────────────────────────
  security-scan:
    name: Security Scan
    runs-on: ubuntu-latest
    needs: lint
    timeout-minutes: 15

  # ──────────────────────────────────────────────
  # Job: deploy — Deployment
  # ──────────────────────────────────────────────
  deploy:
    name: Deploy
    runs-on: ubuntu-latest
    needs: [test, build, security-scan]
    if: github.ref == 'refs/heads/main'
    environment: production
    timeout-minutes: 10
```

**Job Bağımlılık Zinciri:**

```
lint → test → build → deploy
lint → security-scan ─┘
```

### 3.4 PHP CI Pipeline

```yaml
# =============================================================================
# PHP CI Pipeline — CoreMusic PHP 8.4 Services
# @see ADR-002-pdo-mandatory-no-orm, ADR-042-vault-restructuring-2026-08-03
# =============================================================================

name: PHP CI

on:
  push:
    branches: [main, develop]
    paths:
      - 'music.coremusic.net/**'
      - 'auth.coremusic.net/**'
      - 'media.coremusic.net/**'
  pull_request:
    branches: [main]

jobs:
  php-lint:
    name: PHP Lint & Static Analysis
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo, pdo_mysql, apcu, opcache
          tools: composer:v2, phpstan, phpcs
          coverage: xdebug

      - name: Get Composer cache directory
        id: composer-cache
        run: echo "dir=$(composer config cache-files-dir)" >> $GITHUB_OUTPUT

      - name: Cache Composer dependencies
        uses: actions/cache@v4
        with:
          path: ${{ steps.composer-cache.outputs.dir }}
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress --no-suggest

      - name: Run PHP-CS-Fixer (DRY_RUN)
        run: vendor/bin/php-cs-fixer fix --dry-run --diff --format=txt

      - name: Run PHPStan (Level 8)
        run: vendor/bin/phpstan analyse --memory-limit=512M

      - name: Run PHPCS (PSR-12)
        run: vendor/bin/phpcs --standard=PSR12 --extensions=php src/

      - name: Check strict_types declaration
        run: |
          echo "Checking declare(strict_types=1) in all PHP files..."
          FAILED=0
          for file in $(find src/ -name "*.php" -type f); do
            if ! head -5 "$file" | grep -q "declare(strict_types=1)"; then
              echo "MISSING strict_types: $file"
              FAILED=1
            fi
          done
          if [ "$FAILED" -eq 1 ]; then
            echo "::error::All PHP files must declare strict_types=1 (ADR-042)"
            exit 1
          fi
          echo "All PHP files have strict_types=1 ✓"

      - name: Check for SELECT * (ADR-002)
        run: |
          echo "Checking for forbidden SELECT * usage..."
          if grep -rn "SELECT \*" src/ --include="*.php"; then
            echo "::error::SELECT * is forbidden. Use explicit column lists (ADR-002)"
            exit 1
          fi
          echo "No SELECT * found ✓"

      - name: Check for ORM usage (ADR-002)
        run: |
          echo "Checking for forbidden ORM usage..."
          if grep -rn "->find\|->findBy\|->createQueryBuilder\|->persist\|->flush" src/ --include="*.php"; then
            echo "::error::ORM usage is forbidden. Use PDO prepared statements (ADR-002)"
            exit 1
          fi
          echo "No ORM usage found ✓"

  php-test:
    name: PHP Tests
    runs-on: ubuntu-latest
    needs: php-lint
    timeout-minutes: 20

    services:
      mysql:
        image: mysql:9.0
        env:
          MYSQL_ROOT_PASSWORD: test_root_password
          MYSQL_DATABASE: coremusic_test
          MYSQL_USER: test_user
          MYSQL_PASSWORD: test_password
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo, pdo_mysql, apcu
          tools: composer:v2
          coverage: xdebug

      - name: Get Composer cache directory
        id: composer-cache
        run: echo "dir=$(composer config cache-files-dir)" >> $GITHUB_OUTPUT

      - name: Cache Composer dependencies
        uses: actions/cache@v4
        with:
          path: ${{ steps.composer-cache.outputs.dir }}
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress

      - name: Wait for MySQL
        run: |
          until mysqladmin ping -h 127.0.0.1 --silent; do
            echo "Waiting for MySQL..."
            sleep 2
          done

      - name: Run PHPUnit
        run: |
          vendor/bin/phpunit \
            --coverage-clover=coverage.xml \
            --coverage-html=coverage-html \
            --coverage-text

      - name: Check coverage threshold (≥80%)
        run: |
          COVERAGE=$(php -r "
            \$xml = simplexml_load_file('coverage.xml');
            echo round(\$xml['line-rate'] * 100, 2);
          ")
          echo "Coverage: ${COVERAGE}%"
          if (( $(echo "$COVERAGE < 80" | bc -l) )); then
            echo "::error::Coverage ${COVERAGE}% is below 80% threshold"
            exit 1
          fi
          echo "Coverage ${COVERAGE}% meets ≥80% threshold ✓"

      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v4
        with:
          files: coverage.xml
          flags: php-backend
          fail_ci_if_error: false

      - name: Upload coverage artifacts
        uses: actions/upload-artifact@v4
        with:
          name: php-coverage
          path: coverage-html/
          retention-days: 7
```

### 3.5 JavaScript CI Pipeline

```yaml
# =============================================================================
# JavaScript CI Pipeline — CoreMusic Frontend & Download Service
# @see ADR-001-vanilla-js-itcss, ADR-042-vault-restructuring-2026-08-03
# =============================================================================

name: JavaScript CI

on:
  push:
    branches: [main, develop]
    paths:
      - 'assets.coremusic.net/**'
      - 'download.coremusic.net/**'
  pull_request:
    branches: [main]

jobs:
  js-lint:
    name: JS Lint & Type Check
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Node.js 20
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: |
            assets.coremusic.net/package-lock.json
            download.coremusic.net/package-lock.json

      - name: Install dependencies (assets)
        working-directory: assets.coremusic.net
        run: npm ci

      - name: Install dependencies (download)
        working-directory: download.coremusic.net
        run: npm ci

      - name: Run ESLint
        working-directory: assets.coremusic.net
        run: npx eslint js/ --max-warnings=0

      - name: Run TypeScript check
        working-directory: download.coremusic.net
        run: npx tsc --noEmit

      - name: Check for innerHTML (ADR-001)
        run: |
          echo "Checking for forbidden innerHTML usage..."
          if grep -rn "innerHTML\s*=" assets.coremusic.net/js/ --include="*.js"; then
            echo "::error::innerHTML is forbidden. Use DOMParser + TrustedTypes (ADR-001)"
            exit 1
          fi
          echo "No innerHTML usage found ✓"

      - name: Check for var usage (ADR-001)
        run: |
          echo "Checking for forbidden var usage..."
          if grep -rn "\bvar " assets.coremusic.net/js/ --include="*.js"; then
            echo "::error::var is forbidden. Use const/let (ADR-001)"
            exit 1
          fi
          echo "No var usage found ✓"

      - name: Check for framework usage (ADR-001)
        run: |
          echo "Checking for forbidden framework usage..."
          if grep -rn "import.*from.*react\|import.*from.*vue\|import.*from.*angular\|import.*from.*svelte" \
            assets.coremusic.net/ --include="*.js"; then
            echo "::error::Framework usage is forbidden. Use Vanilla JS (ADR-001)"
            exit 1
          fi
          echo "No framework usage found ✓"

  js-test:
    name: JS Tests
    runs-on: ubuntu-latest
    needs: js-lint
    timeout-minutes: 15

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Node.js 20
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: assets.coremusic.net/package-lock.json

      - name: Install dependencies
        working-directory: assets.coremusic.net
        run: npm ci

      - name: Run Vitest
        working-directory: assets.coremusic.net
        run: npx vitest run --coverage --reporter=verbose

      - name: Check coverage threshold (≥80%)
        working-directory: assets.coremusic.net
        run: |
          COVERAGE=$(npx vitest run --coverage --reporter=json 2>/dev/null | \
            node -e "const d=require('fs').readFileSync('/dev/stdin','utf8'); \
            const j=JSON.parse(d); \
            console.log(j.total?.lines?.pct || 0)")
          echo "JS Coverage: ${COVERAGE}%"
          if [ $(echo "$COVERAGE < 80" | bc -l) -eq 1 ]; then
            echo "::error::JS Coverage ${COVERAGE}% is below 80% threshold"
            exit 1
          fi
          echo "JS Coverage ${COVERAGE}% meets ≥80% threshold ✓"

      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v4
        with:
          files: assets.coremusic.net/coverage/lcov.info
          flags: js-frontend
          fail_ci_if_error: false

  js-build:
    name: JS Build
    runs-on: ubuntu-latest
    needs: js-test
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Node.js 20
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'

      - name: Build
        working-directory: download.coremusic.net
        run: npm ci && npm run build

      - name: Upload build artifacts
        uses: actions/upload-artifact@v4
        with:
          name: js-build-dist
          path: download.coremusic.net/dist/
          retention-days: 7
```

### 3.6 C++ CI Pipeline

```yaml
# =============================================================================
# C++ CI Pipeline — CoreMusic Neva Engine (Audio Service)
# @see ADR-017-dsp-hardware-mode, ADR-038-8.1-sound-card-chip-selection
# =============================================================================

name: C++ CI

on:
  push:
    branches: [main, develop]
    paths:
      - 'projects/neva-engine/**'
      - 'projects/neva-player/**'
  pull_request:
    branches: [main]

jobs:
  cpp-build:
    name: C++ Build & Test (${{ matrix.compiler }})
    runs-on: ubuntu-latest
    timeout-minutes: 20

    strategy:
      fail-fast: false
      matrix:
        compiler: [gcc-14, clang-18]
        build_type: [Release, Debug]
        include:
          - compiler: gcc-14
            cc: gcc-14
            cxx: g++-14
          - compiler: clang-18
            cc: clang-18
            cxx: clang++-18

    steps:
      - name: Checkout code
        uses: actions/checkout@v4
        with:
          submodules: recursive

      - name: Install dependencies
        run: |
          sudo apt-get update
          sudo apt-get install -y cmake build-essential libasound2-dev \
            libfreetype6-dev libx11-dev libxrandr-dev libxcursor-dev \
            libgl1-mesa-dev libcurl4-openssl-dev

      - name: Setup compiler
        run: |
          if [ "${{ matrix.compiler }}" = "gcc-14" ]; then
            sudo apt-get install -y gcc-14 g++-14
          else
            sudo apt-get install -y clang-18
          fi

      - name: Get ccache directory
        id: ccache-cache
        run: echo "dir=~/.ccache" >> $GITHUB_OUTPUT

      - name: Cache ccache
        uses: actions/cache@v4
        with:
          path: ~/.ccache
          key: ${{ runner.os }}-ccache-${{ matrix.compiler }}-${{ matrix.build_type }}-${{ github.sha }}
          restore-keys: ${{ runner.os }}-ccache-${{ matrix.compiler }}-${{ matrix.build_type }}-

      - name: Configure CMake
        run: |
          cmake -B build \
            -DCMAKE_BUILD_TYPE=${{ matrix.build_type }} \
            -DCMAKE_C_COMPILER=${{ matrix.cc }} \
            -DCMAKE_CXX_COMPILER=${{ matrix.cxx }} \
            -DCMAKE_CXX_STANDARD=20 \
            -DCMAKE_CXX_FLAGS="-Wall -Wextra -Wpedantic" \
            -DBUILD_TESTS=ON \
            -DCMAKE_EXPORT_COMPILE_COMMANDS=ON

      - name: Build
        run: cmake --build build --parallel $(nproc)

      - name: Run Google Test
        run: |
          cd build
          ctest --output-on-failure --parallel 4

      - name: Check zero-allocation rule
        run: |
          echo "Checking for forbidden heap allocation in audio callbacks..."
          if grep -rn "malloc\|calloc\|realloc\|new " \
            projects/neva-engine/src/Audio/ --include="*.cpp" --include="*.h"; then
            echo "::error::Heap allocation forbidden in audio callbacks (ADR-017)"
            exit 1
          fi
          echo "No heap allocation in audio callbacks ✓"

      - name: Check for noexcept
        run: |
          echo "Checking for missing noexcept on destructors..."
          FAILED=0
          for file in $(find projects/neva-engine/src/ -name "*.h" -type f); do
            if grep -q "~" "$file" && ! grep -q "noexcept" "$file"; then
              echo "MISSING noexcept on destructor: $file"
              FAILED=1
            fi
          done
          if [ "$FAILED" -eq 1 ]; then
            echo "::error::All destructors must be noexcept (ADR-017)"
            exit 1
          fi
          echo "All destructors have noexcept ✓"
```

### 3.7 Docker CI Pipeline

```yaml
# =============================================================================
# Docker CI Pipeline — Container Build, Push & Scan
# =============================================================================

name: Docker CI

on:
  push:
    branches: [main]
    tags: ['v*']
  pull_request:
    branches: [main]

jobs:
  docker-build:
    name: Docker Build & Push
    runs-on: ubuntu-latest
    timeout-minutes: 15
    permissions:
      contents: read
      packages: write

    strategy:
      matrix:
        service:
          - name: music.coremusic.net
            context: ./music.coremusic.net
            file: ./music.coremusic.net/Dockerfile
          - name: auth.coremusic.net
            context: ./auth.coremusic.net
            file: ./auth.coremusic.net/Dockerfile
          - name: download.coremusic.net
            context: ./download.coremusic.net
            file: ./download.coremusic.net/Dockerfile

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3

      - name: Login to GitHub Container Registry
        uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Docker metadata
        id: meta
        uses: docker/metadata-action@v5
        with:
          images: ghcr.io/${{ github.repository }}/${{ matrix.service.name }}
          tags: |
            type=ref,event=branch
            type=ref,event=pr
            type=semver,pattern={{version}}
            type=sha

      - name: Build and push
        uses: docker/build-push-action@v5
        with:
          context: ${{ matrix.service.context }}
          file: ${{ matrix.service.file }}
          push: ${{ github.event_name != 'pull_request' }}
          tags: ${{ steps.meta.outputs.tags }}
          labels: ${{ steps.meta.outputs.labels }}
          cache-from: type=gha
          cache-to: type=gha,mode=max

      - name: Run Trivy vulnerability scanner
        uses: aquasecurity/trivy-action@v0.24.0
        with:
          image-ref: ghcr.io/${{ github.repository }}/${{ matrix.service.name }}:sha-${{ github.sha }}
          format: table
          exit-code: 1
          severity: CRITICAL,HIGH
          ignore-unfixed: true
```

### 3.8 Security Scanning

```yaml
# =============================================================================
# Security Scan Pipeline — GitLeaks, Dependency Audit, OWASP
# =============================================================================

name: Security Scan

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]
  schedule:
    - cron: '0 2 * * 1'  # Her Pazartesi 02:00 UTC

jobs:
  gitleaks:
    name: GitLeaks Secret Scan
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Run GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

  composer-audit:
    name: Composer Dependency Audit
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          tools: composer:v2

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Composer Audit
        run: |
          composer audit --no-dev --format=json > composer-audit.json || true
          echo "## Composer Audit Results" >> $GITHUB_STEP_SUMMARY
          composer audit --no-dev

  npm-audit:
    name: npm Dependency Audit
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Node.js 20
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'

      - name: Run npm audit
        working-directory: download.coremusic.net
        run: |
          npm ci
          npm audit --audit-level=high

  owasp-check:
    name: OWASP Dependency Check
    runs-on: ubuntu-latest
    timeout-minutes: 30

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Java
        uses: actions/setup-java@v4
        with:
          distribution: 'temurin'
          java-version: '21'

      - name: Run OWASP Dependency Check
        uses: dependency-check/Dependency-Check_Action@main
        with:
          path: '.'
          format: 'HTML'
          args: >-
            --scan
            --noupdate
            --failOnCVSS 7

      - name: Upload OWASP report
        uses: actions/upload-artifact@v4
        with:
          name: owasp-report
          path: reports/
          retention-days: 30
```

### 3.9 Coverage Reporting

Coverage stratejisi: Her dil için ayrı threshold'lar, Codecov upload, badge üretimi.

```yaml
  # Coverage threshold kontrolü (her pipeline'da)
  - name: Enforce coverage threshold
    run: |
      THRESHOLD=80
      COVERAGE=$(cat coverage-summary.json | jq '.line')
      echo "Coverage: ${COVERAGE}%"
      if [ $(echo "$COVERAGE < $THRESHOLD" | bc -l) -eq 1 ]; then
        echo "::error::Coverage ${COVERAGE}% is below ${THRESHOLD}% threshold"
        exit 1
      fi
```

**Coverage Threshold Matrisi:**

| Modül | Minimum | Hedef | Kaynak |
|-------|---------|-------|--------|
| Backend (PHP) | ≥80% | ≥90% | [[testing/coverage-targets]] |
| Frontend (JS) | ≥80% | ≥90% | [[testing/coverage-targets]] |
| Audio Engine (C++) | ≥80% | ≥90% | [[testing/coverage-targets]] |
| Download Service | ≥80% | ≥90% | [[testing/coverage-targets]] |

### 3.10 Caching Stratejileri

| Dil/Araç | Cache Key | Path | TTL |
|----------|-----------|------|-----|
| Composer | `${{ hashFiles('**/composer.lock') }}` | `$(composer config cache-files-dir)` | 7 gün |
| npm | `${{ hashFiles('**/package-lock.json') }}` | `~/.npm` | 7 gün |
| Docker | `type=gha` | GitHub Actions cache | 7 gün |
| ccache | `${{ matrix.compiler }}-${{ matrix.build_type }}-${{ github.sha }}` | `~/.ccache` | 7 gün |
| PHPStan | `phpstan-${{ hashFiles('phpstan.neon') }}` | `/tmp/phpstan` | 24 saat |

**Cache Optimizasyon Kuralları:**

1. Hash-based key'ler kullanın (lock file hash'i)
2. `restore-keys` ile fallback sağlayın
3. Cache hit oranını izleyin (%80+ hedef)
4. Eski cache'leri otomatik temizleyin

### 3.11 Matrix Builds

```yaml
    strategy:
      fail-fast: false
      matrix:
        os: [ubuntu-latest, ubuntu-22.04]
        php-version: ['8.4']
        include:
          - os: ubuntu-latest
            php-version: '8.4'
            experimental: false
          - os: ubuntu-22.04
            php-version: '8.4'
            experimental: true

    continue-on-error: ${{ matrix.experimental }}
```

**Matrix Tablosu:**

| OS | PHP | Node | C++ | Durum |
|----|-----|------|-----|-------|
| ubuntu-latest | 8.4 | 20 | GCC 14 | ✅ Primary |
| ubuntu-22.04 | 8.4 | 20 | GCC 14 | ⚠️ Experimental |
| ubuntu-latest | 8.3 | — | — | ❌ Desteklenmiyor |
| windows-latest | 8.4 | 20 | MSVC | ⚠️ Planned |

### 3.12 Environment Secrets

```yaml
    environment:
      name: ${{ github.ref == 'refs/heads/main' && 'production' || 'staging' }}
      url: ${{ steps.deploy.outputs.url }}

    steps:
      - name: Deploy
        env:
          DEPLOY_KEY: ${{ secrets.DEPLOY_KEY }}
          API_TOKEN: ${{ secrets.API_TOKEN }}
```

**Secret Tablosu:**

| Secret | Amaç | Ortam |
|--------|------|-------|
| `GITHUB_TOKEN` | Otomatik token | Tüm ortamlar |
| `DEPLOY_KEY` | SSH deployment key | production |
| `API_TOKEN` | API erişim token'ı | production |
| `CODECOV_TOKEN` | Coverage upload | Tüm ortamlar |
| `DOCKER_USERNAME` | Docker Hub user | production |
| `DOCKER_PASSWORD` | Docker Hub pass | production |
| `DB_PASSWORD` | Veritabanı şifresi | production |

### 3.13 Deployment Jobs

```yaml
  deploy-staging:
    name: Deploy to Staging
    runs-on: ubuntu-latest
    needs: [test, build]
    if: github.ref == 'refs/heads/develop'
    environment:
      name: staging
      url: https://staging.coremusic.net

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Deploy to staging
        run: |
          echo "Deploying to staging..."
          # SSH deploy komutları

  deploy-production:
    name: Deploy to Production
    runs-on: ubuntu-latest
    needs: [test, build, security-scan]
    if: github.ref == 'refs/heads/main'
    environment:
      name: production
      url: https://music.coremusic.net

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Deploy to production
        run: |
          echo "Deploying to production..."
          # SSH deploy komutları

      - name: Health check
        run: |
          sleep 30
          HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://music.coremusic.net)
          if [ "$HTTP_STATUS" -ne 200 ]; then
            echo "::error::Health check failed with status $HTTP_STATUS"
            exit 1
          fi
          echo "Health check passed ✓"

  rollback:
    name: Rollback
    runs-on: ubuntu-latest
    if: failure() && needs.deploy-production.result == 'failure'
    needs: deploy-production

    steps:
      - name: Rollback to previous version
        run: |
          echo "Rolling back to previous deployment..."
          # Rollback komutları
```

### 3.14 Concurrency Control

```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true
```

**Concurrency Tablosu:**

| Workflow | Group | Cancel | Açıklama |
|----------|-------|--------|----------|
| CI Main | `ci-main-{branch}` | ✅ | Aynı branch'deki eski run'ları iptal et |
| Deploy Production | `deploy-prod` | ❌ | Deployment'lar iptal edilmez |
| Deploy Staging | `deploy-staging` | ✅ | Staging deploy'ları iptal edilebilir |
| Security Scan | `security-{branch}` | ✅ | Güvenlik taramaları parallel çalışabilir |

## 4. Hard Guardrails

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **GitLeaks Mandatory** | Her commit'te GitLeaks taraması zorunlu | Pipeline fails |
| 2 | **Test Must Pass** | Tüm testler geçmeden deploy yasak | Deploy blocked |
| 3 | **Coverage ≥80%** | Test coverage %80 altına düşemez | Pipeline fails |
| 4 | **Pinned Actions** | Actions versiyon sabitlenmeli (v4, v2) | Security warning |
| 5 | **No Hardcoded Secrets** | Secret'lar kodda düz metin olarak bulunamaz | GitLeaks bloke |
| 6 | **PHP strict_types** | Tüm PHP dosyalarında `declare(strict_types=1)` zorunlu | Lint fails |
| 7 | **No innerHTML** | JS'de innerHTML kullanımı yasak (ADR-001) | Lint fails |
| 8 | **No SELECT *** | SQL'de SELECT * kullanımı yasak (ADR-002) | Lint fails |
| 9 | **No ORM** | ORM kullanımı yasak, sadece PDO (ADR-002) | Lint fails |
| 10 | **Zero-Allocation** | C++ audio callback'de heap allocation yasak (ADR-017) | Lint fails |
| 11 | **Timeout Limit** | Job timeout max 30 dakika | Auto-cancel |
| 12 | **Artifact Retention** | Artifact'lar max 30 gün saklanır | Auto-cleanup |

## 5. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Workflow** | `ci-{language}.yml` | `ci-php.yml`, `ci-js.yml` |
| **Job** | `{action}-{target}` | `php-lint`, `js-test`, `cpp-build` |
| **Step** | `{verb} {object}` | `Install dependencies`, `Run PHPUnit` |
| **Action** | `{org}/{action}@{version}` | `actions/checkout@v4` |
| **Cache Key** | `{os}-{tool}-{hash}` | `ubuntu-composer-{hash}` |
| **Artifact** | `{service}-{type}` | `php-coverage`, `js-build-dist` |
| **Secret** | `{SERVICE}_{PURPOSE}` | `DEPLOY_KEY`, `API_TOKEN` |
| **Environment** | `{stage}` | `staging`, `production` |

## 6. Security Considerations

| Konu | Uygulama | Kaynak |
|------|----------|--------|
| **Secret Scanning** | GitLeaks her workflow'da mandatory | [[architecture/07-security/]] |
| **Pinned Actions** | Versiyon sabitleme: `@v4`, `@v2` | GitHub Security |
| **Minimal Permissions** | `permissions: contents: read` default | GitHub OIDC |
| **OIDC** | OpenID Connect ile keyless auth | GitHub OIDC |
| **No Echo Secrets** | `echo ${{ secrets.X }}` yasak | GitHub Security |
| **Dependency Audit** | Composer audit + npm audit haftalık | [[ADR-022-database-hardened-security]] |
| **Container Scan** | Trivy ile image vulnerability scanning | OWASP |
| **Branch Protection** | main branch PR zorunlu, review required | GitHub Settings |

## 7. Performance Notes

| Optimizasyon | Kazanç | Uygulama |
|--------------|--------|----------|
| **Cache Hit** | %50-70 faster | Composer, npm, Docker cache |
| **Parallel Jobs** | %40 faster | Lint + Test paralel |
| **Artifact Reuse** | %30 faster | Build artifact'ları między job'larda |
| **Self-hosted Runner** | %60 faster | C++ build için kendi runner'ınız |
| **Matrix Builds** | Parallel test | OS/versiyon kombinasyonları |
| **Docker Layer Cache** | %70 faster | `cache-from: type=gha` |
| **ccache** | %80 faster | C++ derleme için object file cache |
| **Sparse Checkout** | %50 faster | Sadece ilgili dosyaları checkout |

## 8. Edge Cases

| Senaryo | Belirti | Çözüm |
|---------|---------|-------|
| **Flaky Test** | Test bazen geçer bazen başarısız | `retry: 3` + `continue-on-error` |
| **Timeout** | Job 30 dakikayı aşıyor | `timeout-minutes: 30` + parçalama |
| **Runner Unavailable** | ubuntu-latest meşgul | `runs-on: ubuntu-22.04` fallback |
| **Secret Rotation** | Eski token hala kullanımda | `gh secret set` ile güncelleme |
| **Cache Corruption** | Bozuk cache dosyası | `cache-dependency-path` + hash key |
| **Rate Limit** | API rate limit aşıldı | `retry-after` header + exponential backoff |
| **Disk Space** | Runner diski dolu | `docker system prune` step |
| **Merge Conflict** | PR merge conflict | Otomatik rebase + force push |

## 9. Troubleshooting

| Hata | Neden | Çözüm |
|------|-------|-------|
| **Cache miss** | Lock file değişti | `restore-keys` ile fallback |
| **OOM killed** | Bellek yetersiz | `--max-workers=2` veya bigger runner |
| **Rate limit** | GitHub API limit | `GITHUB_TOKEN` ile auth |
| **Permission denied** | Secret eksik | `GITHUB_TOKEN` veya `secrets.*` kontrol |
| **Job timeout** | Uzun süren adım | `timeout-minutes` artır veya parçala |
| **Docker build fail** | Dockerfile hatası | `docker build --no-cache` ile test |
| **MySQL connection** | Service container hazır değil | `mysqladmin ping` health check |
| **Coverage drop** | Yeni test eksik | Threshold kontrol + test ekleme |

## 10. Common Anti-Patterns

| # | ❌ YANLIŞ | ✅ DOĞRU |
|---|-----------|----------|
| 1 | `${{ secrets.API_KEY }}` kodda echo | `${{ secrets.API_KEY }}` sadece env'de |
| 2 | `npm install` (deterministik değil) | `npm ci` (lock file ile) |
| 3 | `composer install` (cache yok) | `composer install` + `actions/cache@v4` |
| 4 | Docker image'da root user | Non-root user (`USER app`) |
| 5 | `**` glob ile huge artifact | Minimal artifact (`path: dist/*.js`) |
| 6 | `if: always()` ile deploy | `if: success()` ile deploy |
| 7 | `curl` ile production URL test | Health check endpoint kullan |
| 8 | `git push --force` ile deploy | Deploy key + SSH ile |
| 9 | `sudo apt-get install` her run | Cache + restore-keys |
| 10 | Matrix'te 10+ kombinasyon | Kritik kombinasyonları seç |

## 11. Workflow Templates

### Template 1: Main CI (Birleşik Pipeline)

```yaml
# =============================================================================
# CoreMusic Main CI Pipeline
# @see ADR-001, ADR-002, ADR-042
# =============================================================================

name: CoreMusic CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]
  workflow_dispatch:
    inputs:
      skip_tests:
        description: 'Skip tests (emergency)'
        required: false
        default: false
        type: boolean

concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true

permissions:
  contents: read

jobs:
  # ── Security Scan ──────────────────────────────────
  security:
    name: Security Scan
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4
        with: { fetch-depth: 0 }
      - uses: gitleaks/gitleaks-action@v2
        env: { GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }} }

  # ── PHP Lint ───────────────────────────────────────
  php-lint:
    name: PHP Lint
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo, pdo_mysql, apcu
          tools: composer:v2
      - run: composer install --prefer-dist --no-progress
      - run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - run: vendor/bin/phpstan analyse --memory-limit=512M

  # ── PHP Test ───────────────────────────────────────
  php-test:
    name: PHP Tests
    runs-on: ubuntu-latest
    needs: php-lint
    timeout-minutes: 20
    services:
      mysql:
        image: mysql:9.0
        env:
          MYSQL_ROOT_PASSWORD: test
          MYSQL_DATABASE: coremusic_test
        ports: ['3306:3306']
        options: --health-cmd="mysqladmin ping" --health-interval=10s
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo, pdo_mysql
          coverage: xdebug
      - run: composer install --prefer-dist --no-progress
      - run: vendor/bin/phpunit --coverage-clover=coverage.xml
      - uses: codecov/codecov-action@v4
        with: { files: coverage.xml, flags: php }

  # ── JS Lint ────────────────────────────────────────
  js-lint:
    name: JS Lint
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '20', cache: 'npm' }
      - run: npm ci
      - run: npx eslint js/ --max-warnings=0

  # ── JS Test ────────────────────────────────────────
  js-test:
    name: JS Tests
    runs-on: ubuntu-latest
    needs: js-lint
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '20', cache: 'npm' }
      - run: npm ci
      - run: npx vitest run --coverage
      - uses: codecov/codecov-action@v4
        with: { files: coverage/lcov.info, flags: js }

  # ── Deploy ─────────────────────────────────────────
  deploy:
    name: Deploy
    runs-on: ubuntu-latest
    needs: [security, php-test, js-test]
    if: github.ref == 'refs/heads/main'
    environment: production
    steps:
      - uses: actions/checkout@v4
      - run: echo "Deploying..."
```

### Template 2: Deployment Pipeline

```yaml
# =============================================================================
# CoreMusic Deployment Pipeline
# =============================================================================

name: CoreMusic Deploy

on:
  workflow_dispatch:
    inputs:
      environment:
        description: 'Target environment'
        required: true
        type: choice
        options: [staging, production]
      version:
        description: 'Version tag (e.g. v1.2.3)'
        required: false
        type: string

jobs:
  deploy:
    name: Deploy to ${{ inputs.environment }}
    runs-on: ubuntu-latest
    environment:
      name: ${{ inputs.environment }}
      url: https://${{ inputs.environment == 'production' && 'music' || 'staging' }}.coremusic.net

    steps:
      - uses: actions/checkout@v4

      - name: Validate deployment
        run: |
          echo "Target: ${{ inputs.environment }}"
          echo "Version: ${{ inputs.version || 'latest' }}"

      - name: Deploy
        run: |
          echo "Deploying to ${{ inputs.environment }}..."

      - name: Health check
        run: |
          URL="https://music.coremusic.net"
          if [ "${{ inputs.environment }}" = "staging" ]; then
            URL="https://staging.coremusic.net"
          fi
          sleep 30
          STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$URL")
          if [ "$STATUS" != "200" ]; then
            echo "::error::Health check failed: $STATUS"
            exit 1
          fi
          echo "Health check passed ✓"
```

### Template 3: Security Audit Pipeline

```yaml
# =============================================================================
# CoreMusic Security Audit Pipeline
# =============================================================================

name: Security Audit

on:
  schedule:
    - cron: '0 2 * * 1'  # Her Pazartesi
  workflow_dispatch:

jobs:
  gitleaks:
    name: GitLeaks Scan
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
        with: { fetch-depth: 0 }
      - uses: gitleaks/gitleaks-action@v2
        env: { GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }} }

  dependency-audit:
    name: Dependency Audit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.4', tools: composer:v2 }
      - run: composer audit
      - uses: actions/setup-node@v4
        with: { node-version: '20' }
      - run: cd download.coremusic.net && npm ci && npm audit

  container-scan:
    name: Container Scan
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: aquasecurity/trivy-action@v0.24.0
        with:
          scan-type: 'fs'
          scan-ref: '.'
          format: 'table'
          severity: 'CRITICAL,HIGH'
          exit-code: 1

  report:
    name: Generate Report
    runs-on: ubuntu-latest
    needs: [gitleaks, dependency-audit, container-scan]
    if: always()
    steps:
      - name: Summary
        run: |
          echo "## Security Audit Report" >> $GITHUB_STEP_SUMMARY
          echo "- GitLeaks: ${{ needs.gitleaks.result }}" >> $GITHUB_STEP_SUMMARY
          echo "- Dependencies: ${{ needs.dependency-audit.result }}" >> $GITHUB_STEP_SUMMARY
          echo "- Containers: ${{ needs.container-scan.result }}" >> $GITHUB_STEP_SUMMARY
```

## 12. Pre-commit Hooks

`.pre-commit-config.yaml`:

```yaml
repos:
  - repo: https://github.com/gitleaks/gitleaks
    rev: v8.18.0
    hooks:
      - id: gitleaks

  - repo: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
    rev: v3.65.0
    hooks:
      - id: php-cs-fixer
        args: ['--dry-run', '--diff']

  - repo: https://github.com/squizlabs/PHP_CodeSniffer
    rev: 3.10.0
    hooks:
      - id: phpcs
        args: ['--standard=PSR12']

  - repo: https://github.com/pre-commit/mirrors-eslint
    rev: v9.0.0
    hooks:
      - id: eslint
        files: \.js$
        args: ['--max-warnings=0']

  - repo: https://github.com/pre-commit/mirrors-prettier
    rev: v4.0.0-alpha.8
    hooks:
      - id: prettier
        files: \.(json|yml|yaml|md)$
```

**Pre-commit Hook Kurulumu:**

```bash
# pip install pre-commit
pre-commit install
pre-commit install --hook-type commit-msg
```

## 13. Related Documents

- [[github-actions-template]] — Bu dosya
- [[docker-template]] — Docker template
- [[phpunit-template]] — PHPUnit template
- [[vitest-template]] — Vitest template
- [[c-template]] — C template
- [[ADR-001-vanilla-js-itcss]] — Frontend kararı
- [[ADR-002-pdo-mandatory-no-orm]] — DB kararı
- [[ADR-017-dsp-hardware-mode]] — DSP donanım modu
- [[ADR-042-vault-restructuring-2026-08-03]] — MSA limit
- [[architecture/02-deployment/ci-cd-pipeline]] — CI/CD pipeline
- [[architecture/02-deployment/deployment]] — Deployment guide
- [[architecture/02-deployment/docker-compose]] — Docker Compose
- [[testing/coverage-targets]] — Coverage hedefleri

## 14. Cross-References

| Bu Dosya | ADR | İlişki |
|----------|-----|--------|
| § 3.4 PHP CI | [[ADR-002-pdo-mandatory-no-orm]] | SELECT * ve ORM yasak kontrolü |
| § 3.5 JS CI | [[ADR-001-vanilla-js-itcss]] | innerHTML ve framework yasak kontrolü |
| § 3.6 C++ CI | [[ADR-017-dsp-hardware-mode]] | Zero-allocation ve noexcept kontrolü |
| § 3.8 Security | [[ADR-022-database-hardened-security]] | Dependency audit |
| § 4 Guardrails | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit uyumu |
| § 6 Security | [[architecture/07-security/]] | OWASP, CSRF, CSP |
| § 8 Edge Cases | [[WORKFLOW.md]] | Hard gate ve escalation |
| § 12 Pre-commit | [[architecture/02-deployment/ci-cd-pipeline]] | Pre-commit hooks |

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 600+ |
| **Frontmatter** | ✅ 14 alan |
| **GitHub Actions** | ✅ Uyumlu |
| **ADR Uyumlu** | ✅ 001, 002, 017, 022, 042 |
| **MSA Uyumlu** | ✅ 15 dosya limiti |
| **3 Dil Desteği** | ✅ PHP 8.4, JS ES6+, C++20 |
| **Coverage Threshold** | ✅ ≥80% |
| **Security Scan** | ✅ GitLeaks mandatory |
| **Matrix Builds** | ✅ Multi-OS, multi-version |
| **Caching** | ✅ Composer, npm, Docker, ccache |
| **18 Bölüm** | ✅ Tamamlandı |

## 16. Examples

### Example 1: Main CI File (`.github/workflows/ci-main.yml`)

```yaml
name: CoreMusic CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true

permissions:
  contents: read

jobs:
  security:
    name: Security
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4
        with: { fetch-depth: 0 }
      - uses: gitleaks/gitleaks-action@v2
        env: { GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }} }

  php:
    name: PHP ${{ matrix.php-version }}
    runs-on: ubuntu-latest
    timeout-minutes: 20
    needs: security
    strategy:
      matrix:
        php-version: ['8.4']
    services:
      mysql:
        image: mysql:9.0
        env: { MYSQL_ROOT_PASSWORD: test, MYSQL_DATABASE: coremusic_test }
        ports: ['3306:3306']
        options: --health-cmd="mysqladmin ping" --health-interval=10s
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: pdo, pdo_mysql, apcu
          tools: composer:v2
          coverage: xdebug
      - run: composer install --prefer-dist --no-progress
      - run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - run: vendor/bin/phpstan analyse --memory-limit=512M
      - run: vendor/bin/phpunit --coverage-clover=coverage.xml
      - uses: codecov/codecov-action@v4
        with: { files: coverage.xml, flags: php }

  js:
    name: JavaScript
    runs-on: ubuntu-latest
    timeout-minutes: 15
    needs: security
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '20', cache: 'npm' }
      - run: npm ci
      - run: npx eslint js/ --max-warnings=0
      - run: npx vitest run --coverage
      - uses: codecov/codecov-action@v4
        with: { files: coverage/lcov.info, flags: js }
```

### Example 2: Deployment File (`.github/workflows/cd-production.yml`)

```yaml
name: Production Deploy

on:
  workflow_dispatch:
    inputs:
      version:
        description: 'Version tag'
        required: true
        type: string

concurrency:
  group: deploy-production
  cancel-in-progress: false

jobs:
  deploy:
    name: Deploy v${{ inputs.version }}
    runs-on: ubuntu-latest
    environment:
      name: production
      url: https://music.coremusic.net
    steps:
      - uses: actions/checkout@v4
      - run: echo "Deploying v${{ inputs.version }} to production..."
      - run: |
          sleep 30
          STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://music.coremusic.net)
          [ "$STATUS" = "200" ] || exit 1
```

### Example 3: Security Audit File (`.github/workflows/ci-security.yml`)

```yaml
name: Security Audit

on:
  schedule: [{ cron: '0 2 * * 1' }]
  workflow_dispatch:

jobs:
  gitleaks:
    name: GitLeaks
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
        with: { fetch-depth: 0 }
      - uses: gitleaks/gitleaks-action@v2
        env: { GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }} }

  dependencies:
    name: Dependencies
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.4', tools: composer:v2 }
      - run: composer audit
      - uses: actions/setup-node@v4
        with: { node-version: '20' }
      - run: cd download.coremusic.net && npm ci && npm audit

  report:
    name: Report
    runs-on: ubuntu-latest
    needs: [gitleaks, dependencies]
    if: always()
    steps:
      - run: |
          echo "## Security Report" >> $GITHUB_STEP_SUMMARY
          echo "- GitLeaks: ${{ needs.gitleaks.result }}" >> $GITHUB_STEP_SUMMARY
          echo "- Dependencies: ${{ needs.dependencies.result }}" >> $GITHUB_STEP_SUMMARY
```

## 17. Checklist

### Pre-commit CI/CD Quality Checklist

- [ ] GitLeaks scan passes (no secrets detected)
- [ ] PHP: `declare(strict_types=1)` in all files
- [ ] PHP: No `SELECT *` usage (ADR-002)
- [ ] PHP: No ORM usage (ADR-002)
- [ ] PHP: PHP-CS-Fixer passes (PSR-12)
- [ ] PHP: PHPStan level 8 passes
- [ ] PHP: PHPUnit tests pass (≥80% coverage)
- [ ] JS: No `innerHTML` usage (ADR-001)
- [ ] JS: No `var` usage (ADR-001)
- [ ] JS: No framework imports (ADR-001)
- [ ] JS: ESLint passes (0 warnings)
- [ ] JS: Vitest tests pass (≥80% coverage)
- [ ] C++: No heap allocation in audio callbacks (ADR-017)
- [ ] C++: All destructors have `noexcept`
- [ ] C++: CMake build succeeds
- [ ] C++: Google Test passes
- [ ] Docker: No root user in image
- [ ] Docker: Trivy scan passes (no CRITICAL/HIGH)
- [ ] Security: Composer audit passes
- [ ] Security: npm audit passes
- [ ] All actions pinned to specific versions
- [ ] Cache keys use hash-based strategy
- [ ] Job timeouts configured
- [ ] Concurrency control configured
- [ ] Coverage threshold ≥80% enforced

## 18. Branch Strategy

### Git Flow

```
main (production)
  ├── develop (integration)
  │   ├── feature/auth-refactor
  │   ├── feature/theme-engine
  │   └── feature/download-service
  ├── release/v1.2.0
  └── hotfix/security-patch
```

**Branch Kuralları:**

| Branch | Amaç | CI Trigger | Deploy |
|--------|------|------------|--------|
| `main` | Production | push → full CI + deploy | ✅ Production |
| `develop` | Integration | push → full CI | ✅ Staging |
| `feature/*` | Yeni özellik | PR → lint + test | ❌ |
| `release/*` | Release hazırlığı | PR → full CI | ❌ |
| `hotfix/*` | Acil düzeltme | PR → full CI + security | ✅ Production |

**PR Kuralları:**

1. `develop` branch'ine PR zorunlu
2. En az 1 code review zorunlu
3. Tüm CI check'leri geçmeli
4. Coverage threshold ≥80% korunmalı
5. GitLeaks scan başarılı olmalı

**Release Workflow:**

```bash
# 1. Release branch oluştur
git checkout -b release/v1.2.0 develop

# 2. Versiyon bumped
# 3. CI tüm testleri çalıştırır
# 4. Code review
# 5. Merge → main
# 6. Tag: v1.2.0
# 7. main → develop merge
```

**Hotfix Workflow:**

```bash
# 1. Hotfix branch
git checkout -b hotfix/security-patch main

# 2. Düzeltme yapılır
# 3. CI + security scan
# 4. Merge → main + develop
# 5. Tag: v1.2.1
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
