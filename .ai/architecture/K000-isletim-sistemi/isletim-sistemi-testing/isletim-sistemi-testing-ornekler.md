---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kapsam
evidence:
  - "shared/phpunit.xml:1-39"
  - ".github/workflows/ci.yml:8-108"
  - ".github/workflows/secret-scan.yml:5-30"
  - "shared/composer.json:46-49"
  - "package.json:4-9"
  - "auth.coremusic.net/phpunit.xml:13-32"
  - "shared/tests/Unit/PageRouter/RouteRegistryTest.php:1-64"
  - "shared/tests/Fixtures/chrome/header.php:1"
validation:
  - "2026-10-13 · her kod bloğu dosya + satır numarası ile diskten okundu; indeks eşlemesi §9'da"
open_questions:
  - "T1: OS/çekirdek test kapsamı [UNKNOWN]"
  - "T2: Coverage hedefi [UNKNOWN]"
group: kod
---

# isletim-sistemi-testing — Örnekler (gerçek repo kodu)

> **Okuma kuralı:** her örnek `Dosya:` + `Satır:` ile başlar ve `Select-Object -Index (N-1)`
> ile doğrulanır. Satır numarası **uydurulmaz** — §9 indeks tablosu kesişmeyi kanıtlar.
> Bloklar dosyadan **olduğu gibi** alınmıştır; yorum satırlarında bozuk kodlaması olan
> satırlar alıntılmamıştır (mojibake yasağı).

## §0 Alıntı kuralı

| Kural | Uygulama |
|---|---|
| Kaynak | yalnız repo diski |
| Doğrulama | `Get-Content -Encoding UTF8` + `Select-Object -Index (N-1)` |
| Kırpma | satır aralığı verilir, içerik değiştirilmez |
| Temizlik | bozuk kodlamalı yorum satırları alıntılmaz |
| Yer tutucu | `TBD`, `Yakında`, `// ...`, `{...}` kullanılmaz |
| Eksik hücre | tablo hücresi boş bırakılmaz |

## §1 — `shared/phpunit.xml` tam dosya (8 testsuite)

**Dosya:** `shared/phpunit.xml` · **Satır:** 1-39 · **Satır sayısı:** 39

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Api">
            <directory>tests/Api</directory>
        </testsuite>
        <testsuite name="Repository">
            <directory>tests/Repository</directory>
        </testsuite>
        <testsuite name="Events">
            <directory>tests/Events</directory>
        </testsuite>
        <testsuite name="Security">
            <directory>tests/Security</directory>
        </testsuite>
        <testsuite name="Middleware">
            <directory>tests/Middleware</directory>
        </testsuite>
        <testsuite name="Component">
            <directory>tests/Component</directory>
        </testsuite>
        <testsuite name="OAuth">
            <directory>tests/OAuth</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </source>
</phpunit>
```

**Ne gösteriyor:**

| Gözlem | Değer | Satır |
|---|---|---|
| Test suite sayısı | 8 | 9-32 |
| Bootstrap | `vendor/autoload.php` | 4 |
| Hata durdurma | `stopOnFailure="false"` — ilk hatada durmaz | 6 |
| Önbellek | `.phpunit.cache` | 7 |
| Coverage kaynak dizini | `src`, son ek `.php` | 34-38 |
| Coverage eşiği | dosyada yok (T2 kanıtı) | 1-39 tam okuma |

## §2 — `.github/workflows/ci.yml` pipeline iskeleti

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 8-22

```yaml
name: CI Pipeline

on:
  push:
  pull_request:

permissions:
  contents: read

concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true

env:
  PHP_VERSION: '8.4'
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 25-30

```yaml
  php-lint:
    name: PHP Lint
    runs-on: ubuntu-latest
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 60-64

```yaml
      # Disk kaniti: shared/composer.json scripts.stan = "phpstan analyse src --level=5"
      # (phpstan.neon YOK -> composer scripti esas; plan level=8 degil, disk kazanir)
      - name: PHPStan (composer stan)
        run: composer stan
        working-directory: shared
```

**Ne gösteriyor:** tetikleyici `push` + `pull_request`, izin `contents: read` (asgari yetki),
eşzamanlılık `cancel-in-progress: true` (aynı ref için eski koşu iptal edilir), statik analiz
`composer stan` ile `shared` altında çalışır.

## §3 — `.github/workflows/ci.yml` test matrisi

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 66-81

```yaml
  php-test:
    name: PHP Tests (${{ matrix.project }})
    needs: php-lint
    runs-on: ubuntu-latest
    timeout-minutes: 15
    strategy:
      fail-fast: false
      matrix:
        include:
          - project: shared
            args: ''
          # auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK
          # (glob kaniti) -> yalnizca Unit kosulur, failOnWarning kirilmasin.
          - project: auth.coremusic.net
            args: '--testsuite Unit'
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 104-108

```yaml
      - name: PHPUnit
        run: vendor/bin/phpunit "$PHPUNIT_ARGS"
        env:
          PHPUNIT_ARGS: ${{ matrix.args }}
        working-directory: ${{ matrix.project }}
```

**Ne gösteriyor:**

| Gözlem | Değer | Satır |
|---|---|---|
| Kapı bağımlılığı | `needs: php-lint` — lint geçmeden test koşmaz | 68 |
| Matris kırılma davranışı | `fail-fast: false` — bir proje düşerse diğeri koşar | 72 |
| Matris girdisi 1 | `shared` · boş args · tüm suite'ler | 75-76 |
| Matris girdisi 2 | `auth.coremusic.net` · `--testsuite Unit` | 79-80 |
| Argüman aktarımı | `PHPUNIT_ARGS` ortam değişkeni | 106-107 |
| Çalışma dizini | matristeki proje | 108 |

## §4 — `.github/workflows/ci.yml` yumuşak kapılar

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 160-176

```yaml
  js-coverage:
    name: JS Coverage (gate - soft, A7)
    needs: js-test
    runs-on: ubuntu-latest
    timeout-minutes: 15
    continue-on-error: true
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '26'
          cache: npm
      - run: npm ci
      - name: Vitest coverage
        run: npm run test:js:coverage
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 178-203

```yaml
  js-e2e:
    name: JS E2E (Playwright - soft, A6)
    runs-on: ubuntu-latest
    timeout-minutes: 20
    continue-on-error: true
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
      - uses: actions/setup-node@v4
        with:
          node-version: '26'
          cache: npm
      - run: npm ci
      - name: Playwright install chromium
        run: npx playwright install --with-deps chromium
      - name: Playwright e2e
        run: npm run test:e2e
```

**Ne gösteriyor:** `continue-on-error: true` bu iki işi **yumuşak** kapı yapar — iş kırmızı
görünür ama koşuyu engellemez. Sert kapılar (`php-lint`, `php-test`, `composer-audit`,
`js-test`) bu bayraktan yoksundur.

## §5 — `.github/workflows/secret-scan.yml` tam job

**Dosya:** `.github/workflows/secret-scan.yml` · **Satır:** 5-30

```yaml
name: Secret Scan

on:
  push:
  pull_request:

permissions:
  contents: read

concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true

jobs:
  gitleaks:
    name: GitLeaks
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Run GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

**Ne gösteriyor:** secret taraması ayrı bir workflow'dur; `fetch-depth: 0` ile tüm geçmiş
taranır; `GITHUB_TOKEN` harici bir secret tanımlı değildir.

## §6 — composer ve package scriptleri

**Dosya:** `shared/composer.json` · **Satır:** 23-26

```json
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "phpstan/phpstan": "^1.10"
    },
```

**Dosya:** `shared/composer.json` · **Satır:** 46-49

```json
    "scripts": {
        "test": "phpunit",
        "stan": "phpstan analyse src --level=5"
    }
```

**Dosya:** `package.json` · **Satır:** 4-9

```json
  "scripts": {
    "test:js": "vitest run --root assets.coremusic.net",
    "test:js:watch": "vitest --root assets.coremusic.net",
    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",
    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"
  },
```

**Ne gösteriyor:** CI adımları bağımsız komut yazmaz, repo scriptlerine çağırır — lokal ve CI
aynı komutu koşar.

## §7 — `auth.coremusic.net/phpunit.xml` karşılaştırma örneği

**Dosya:** `auth.coremusic.net/phpunit.xml` · **Satır:** 1-8

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
         failOnPhpunitWarning="false">
```

**Dosya:** `auth.coremusic.net/phpunit.xml` · **Satır:** 13-32

```xml
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>include</directory>
        </include>
    </source>

    <coverage>
        <report>
            <text outputFile="php://stdout"/>
        </report>
    </coverage>
```

**Ne gösteriyor:**

| Fark | `shared/phpunit.xml` | `auth.coremusic.net/phpunit.xml` |
|---|---|---|
| Suite sayısı | 8 | 1 (`Unit`) |
| Bootstrap | `vendor/autoload.php` | `tests/bootstrap.php` |
| `failOnRisky` | tanım yok | `true` |
| `failOnWarning` | tanım yok | `true` |
| `<coverage>` raporu | yok | `php://stdout` |
| Coverage eşiği | yok | yok |
| Kaynak dizini | `src` | `include` |

## §8 — gerçek test dosyası örneği

**Dosya:** `shared/tests/Unit/PageRouter/RouteRegistryTest.php` · **Satır:** 1-20

```php
<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\PageRouter\RouteRegistry;

final class RouteRegistryTest extends TestCase
{
    public function testResolveStaticRoute(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'home', path: 'home');
        $registry->register($route);

        $resolved = $registry->resolve('home');
        $this->assertNotNull($resolved);
        $this->assertSame('home', $resolved->page);
    }
```

**Dosya:** `shared/tests/Unit/PageRouter/RouteRegistryTest.php` · **Satır:** 43-63

```php
    public function testResolveParameterizedRoute(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'album', path: 'album/{id}');
        $registry->register($route);

        $resolved = $registry->resolve('album/42');
        $this->assertNotNull($resolved);
        $this->assertSame('album', $resolved->page);
    }

    public function testGetProtectedRouteKeys(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(page: 'home', requiresAuth: true, path: 'home'));
        $registry->register(new SpaRoute(page: 'login', requiresAuth: false, path: 'login'));

        $keys = $registry->getProtectedRouteKeys();
        $this->assertContains('home', $keys);
        $this->assertNotContains('login', $keys);
    }
}
```

**Ne gösteriyor:** birim testi `tests/Unit` altında, `declare(strict_types=1)`, `final class`,
her test tek davranışı doğrular ve `assertSame`/`assertNull` gibi **değer** assertifleri kullanır.
Bu dosya ASCII temizdir (non-ASCII satır 0) — bu yüzden alıntılmıştır.

## §9 — fixture dosyaları

**Dosya:** `shared/tests/Fixtures/chrome/header.php` · **Satır:** 1

```php
<?php echo '<header id="chrome-fixture-header">HEADER-CHROME</header>';
```

**Dosya:** `shared/tests/Fixtures/chrome/footer.php` · **Satır:** 1

```php
<?php echo '<footer id="chrome-fixture-footer">FOOTER-CHROME</footer>';
```

**Dosya:** `shared/tests/Fixtures/jwt/` · dosya listesi (4 dosya)

| Dosya | Boyut |
|---|---|
| `test-private.pem` | 1732 bayt |
| `test-public.pem` | 460 bayt |
| `test2-private.pem` | 1732 bayt |
| `test2-public.pem` | 460 bayt |

**Ne gösteriyor:** test verisi sabittir ve repo içindedir — testler dış servise bağımlı değildir.

## §10 — OS test araçlarının yokluğu (olumsuz örnek)

| Aranan | Kapsam | Sonuç | Yorum |
|---|---|---|---|
| `kunit` | yazılım kodu | 0 isabet | Çekirdek birim testi repo'da yok |
| `kselftest` | yazılım kodu | 0 isabet | Kullanıcı uzayı testi yok |
| `qemu` | workflow + script | 0 isabet | Emülatör testi CI'da yok |
| `kexec` | yazılım kodu | 0 isabet | Boot testi yok |
| `<coverage min=` | tüm `phpunit.xml` | 0 isabet | Eşik yok |
| `upload-artifact` | tüm workflow | 0 isabet | Artifact saklama yok |
| `workflow_dispatch` | tüm workflow | 0 | Elle tetikleme yok |

**Ne gösteriyor:** bu dalın `cekirdek-test` kolu **hedef referanstır**; repo'da karşılığı yoktur
(T1 `[UNKNOWN]`).

## §11 — satır indeksi doğrulama tablosu

| # | Dosya | Satır | İndeks (N-1) | Beklenen ilk token | Sonuç |
|---|---|---|---|---|---|
| 11-01 | `shared/phpunit.xml` | 1 | 0 | `<?xml version="1.0"` | ✅ |
| 11-02 | `shared/phpunit.xml` | 4 | 3 | `bootstrap="vendor/autoload.php"` | ✅ |
| 11-03 | `shared/phpunit.xml` | 7 | 6 | `cacheDirectory=".phpunit.cache"` | ✅ |
| 11-04 | `shared/phpunit.xml` | 8 | 7 | `<testsuites>` | ✅ |
| 11-05 | `shared/phpunit.xml` | 9 | 8 | `<testsuite name="Unit">` | ✅ |
| 11-06 | `shared/phpunit.xml` | 30 | 29 | `<testsuite name="OAuth">` | ✅ |
| 11-07 | `shared/phpunit.xml` | 33 | 32 | `</testsuites>` | ✅ |
| 11-08 | `shared/phpunit.xml` | 34 | 33 | `<source>` | ✅ |
| 11-09 | `shared/phpunit.xml` | 39 | 38 | `</phpunit>` | ✅ |
| 11-10 | `.github/workflows/ci.yml` | 8 | 7 | `name: CI Pipeline` | ✅ |
| 11-11 | `.github/workflows/ci.yml` | 14 | 13 | `permissions:` | ✅ |
| 11-12 | `.github/workflows/ci.yml` | 17 | 16 | `concurrency:` | ✅ |
| 11-13 | `.github/workflows/ci.yml` | 22 | 21 | `PHP_VERSION: '8.4'` | ✅ |
| 11-14 | `.github/workflows/ci.yml` | 25 | 24 | `php-lint:` | ✅ |
| 11-15 | `.github/workflows/ci.yml` | 62 | 61 | `- name: PHPStan (composer stan)` | ✅ |
| 11-16 | `.github/workflows/ci.yml` | 66 | 65 | `php-test:` | ✅ |
| 11-17 | `.github/workflows/ci.yml` | 68 | 67 | `needs: php-lint` | ✅ |
| 11-18 | `.github/workflows/ci.yml` | 72 | 71 | `fail-fast: false` | ✅ |
| 11-19 | `.github/workflows/ci.yml` | 75 | 74 | `- project: shared` | ✅ |
| 11-20 | `.github/workflows/ci.yml` | 80 | 79 | `args: '--testsuite Unit'` | ✅ |
| 11-21 | `.github/workflows/ci.yml` | 104 | 103 | `- name: PHPUnit` | ✅ |
| 11-22 | `.github/workflows/ci.yml` | 110 | 109 | `composer-audit:` | ✅ |
| 11-23 | `.github/workflows/ci.yml` | 142 | 141 | `js-test:` | ✅ |
| 11-24 | `.github/workflows/ci.yml` | 160 | 159 | `js-coverage:` | ✅ |
| 11-25 | `.github/workflows/ci.yml` | 167 | 166 | `continue-on-error: true` | ✅ |
| 11-26 | `.github/workflows/ci.yml` | 178 | 177 | `js-e2e:` | ✅ |
| 11-27 | `.github/workflows/ci.yml` | 183 | 182 | `continue-on-error: true` | ✅ |
| 11-28 | `.github/workflows/ci.yml` | 203 | 202 | `run: npm run test:e2e` | ✅ |
| 11-29 | `.github/workflows/secret-scan.yml` | 5 | 4 | `name: Secret Scan` | ✅ |
| 11-30 | `.github/workflows/secret-scan.yml` | 19 | 18 | `gitleaks:` | ✅ |
| 11-31 | `.github/workflows/secret-scan.yml` | 26 | 25 | `fetch-depth: 0` | ✅ |
| 11-32 | `.github/workflows/secret-scan.yml` | 29 | 28 | `uses: gitleaks/gitleaks-action@v2` | ✅ |
| 11-33 | `shared/composer.json` | 24 | 23 | `"phpunit/phpunit": "^10.5"` | ✅ |
| 11-34 | `shared/composer.json` | 47 | 46 | `"test": "phpunit"` | ✅ |
| 11-35 | `shared/composer.json` | 48 | 47 | `"phpstan analyse src --level=5"` | ✅ |
| 11-36 | `package.json` | 5 | 4 | `"test:js"` | ✅ |
| 11-37 | `package.json` | 7 | 6 | `"test:js:coverage"` | ✅ |
| 11-38 | `package.json` | 8 | 7 | `"test:e2e"` | ✅ |
| 11-39 | `auth.coremusic.net/phpunit.xml` | 6 | 5 | `failOnRisky="true"` | ✅ |
| 11-40 | `auth.coremusic.net/phpunit.xml` | 14 | 13 | `<testsuite name="Unit">` | ✅ |
| 11-41 | `auth.coremusic.net/phpunit.xml` | 30 | 29 | `<text outputFile="php://stdout"/>` | ✅ |
| 11-42 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 1 | 0 | `<?php declare(strict_types=1);` | ✅ |
| 11-43 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 9 | 8 | `final class RouteRegistryTest` | ✅ |
| 11-44 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 46 | 45 | `$route = new SpaRoute(page: 'album'` | ✅ |
| 11-45 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 64 | 63 | `}` | ✅ |
| 11-46 | `shared/tests/Fixtures/chrome/header.php` | 1 | 0 | `<?php echo '<header` | ✅ |
| 11-47 | `shared/tests/Fixtures/chrome/footer.php` | 1 | 0 | `<?php echo '<footer` | ✅ |

## §12 — bu dosyanın sınırları

| Sınır | Karar |
|---|---|
| `phpunit.xml` değişikliği | yapılmaz — yalnız okunur |
| `ci.yml` değişikliği | yapılmaz — yalnız okunur |
| Test yazımı | yapılmaz — test sahibi QA Engineer |
| K18-testing dosyaları | girilmez |
| OS test kodu (KUnit/kselftest) | repo'da yok, yazılmaz |
| Satır numarası | uydurulmaz, indeks ile doğrulanır |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
