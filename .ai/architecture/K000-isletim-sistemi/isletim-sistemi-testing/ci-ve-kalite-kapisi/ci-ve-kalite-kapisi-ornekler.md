---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu
evidence:
  - ".github/workflows/ci.yml:8-203"
  - ".github/workflows/secret-scan.yml:5-31"
  - "shared/composer.json:46-49"
  - "package.json:1-18"
validation:
  - "2026-10-13 · her YAML/JSON bloğu Read ile satır numarasından kopyalandı; blok sonları §12 tablosuyla doğrulandı"
  - "2026-10-13 · secret-scan config gerçeği (.gitleaks.toml YOK) dosya notundan alındı"
open_questions:
  - "T1: OS test job'ı (kselftest/KUnit) bu workflow'a ne zaman eklenir? [UNKNOWN]"
  - "T2: Kapsama job'ı PHP tarafında yok — eşiği T2 kararı belirler [UNKNOWN]"
group: kod
---

# CI ve Kalite Kapısı Örnekleri — Gerçek Workflow Kodu (Satır Referanslı)

## §0 ELI15

Bu dosya, bu depodaki **gerçek** CI dosyalarından blok alıntılar: `ci.yml`
(203 satır), `secret-scan.yml` (31 satır), composer/package scriptleri. Amaç,
kapıların **kağıt üzerinde değil, dosyada** nasıl durduğunu göstermek. Her blok
dosya + satır aralığı ile gelir; kardeş `-anayasa.md` ilkeleri, `-surucu.md`
iş akışını anlatır — burada yalnız **kod** taşınır. `ci.yml`'in iskeleti kardeş
`isletim-sistemi-testing-ornekler.md` §2-§4'te özetlenmiştir; bu dosya oradaki
tekrarın üstüne çıkar: tam job blokları, secret-scan kodu ve script eşlemesi.

## §1 Dosya envanteri (bu dosyada işlenen)

| Dosya | Satır | İçerik |
|---|---|---|
| `.github/workflows/ci.yml` | 203 | 6 job (php-lint · php-test · composer-audit · js-test · js-coverage · js-e2e) |
| `.github/workflows/secret-scan.yml` | 31 | 1 job (gitleaks) — ayrı workflow |
| `shared/composer.json` | 50 | `test` + `stan` scriptleri (L46-49) |
| `package.json` | 18 | `test:js` · `test:js:coverage` · `test:e2e` (ci.yml L6 notu) |

## §2 Workflow kök ayarları — tetikleyici, yetki, eşzamanlılık

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

**Ne kanıtlar:** (1) iki tetikleyici (`push` + `pull_request`), (2) **minimum
yetki** (`contents: read` — token yazma yetkisi almaz), (3) aynı ref için eski
koşu iptali, (4) PHP sürümü tek env kaynağında. Bu dördü anayasa determinizm
ilkesinin (ci-ve-kalite-kapisi-anayasa §5) kod karşılığıdır.

## §3 Sert kapı — PHPStan job'ı (lint → statik analiz)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 60-64

```yaml
      # Disk kanıtı: shared/composer.json scripts.stan = "phpstan analyse src --level=5"
      # (phpstan.neon YOK -> composer scripti esas; plan level=8 değil, disk kazanir)
      - name: PHPStan (composer stan)
        run: composer stan
        working-directory: shared
```

**Ne kanıtlar:** Komutun **sahibi composer scripti**dir, yml'de tekrar yazılmaz
(SSOT: `shared/composer.json:48`). Yorum satırı iki disk gerçeğini belgeler:
`phpstan.neon` yok ve seviye **5**'tir (plan 8 değil — disk kazanır kuralı).

## §4 Matrix job — iki proje, farklı suite args

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 66-80 (job başlığı + matrix)

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

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 104-108 (komut satırı)

```yaml
      - name: PHPUnit
        run: vendor/bin/phpunit "$PHPUNIT_ARGS"
        env:
          PHPUNIT_ARGS: ${{ matrix.args }}
        working-directory: ${{ matrix.project }}
```

**Ne kanıtlar:** (1) `needs: php-lint` — kapı sırası, (2) `fail-fast: false` —
bir proje düşerken diğeri koşmaya devam eder, (3) args **env üzerinden** binary'ye
geçer (`"$PHPUNIT_ARGS"`), (4) `--testsuite Unit` kararı **disk kanıtına**
(tests/Integration yok) dayanır. Suite adının XML'deki karşılığı
`auth.coremusic.net/phpunit.xml` testsuite bloğudur.

## §5 Soft kapılar — coverage ve e2e (kod + gerekçe satırı)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 160-176 (js-coverage)

```yaml
  js-coverage:
    name: JS Coverage (gate — soft, A7)
    needs: js-test
    runs-on: ubuntu-latest
    timeout-minutes: 15
    # A7 kararı: %80 eşiği şimdilik erişilemez (12.9k satır / <100 test) —
    # job GÖRÜNÜR-KIRMIZI olsun ama bloklamasın; eşikler büyüyünce kapatılır.
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

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 178-183 (js-e2e başlığı)

```yaml
  js-e2e:
    name: JS E2E (Playwright — soft, A6)
    runs-on: ubuntu-latest
    timeout-minutes: 20
    # A6: e2e lock'a alındı; ilk koşular bilinen-riskli → soft job.
    continue-on-error: true
```

**Ne kanıtlar:** İki soft kapının **gerekçesi koddur** (yorum satırları A6/A7
karar numaralarını taşır). "%80 eşiği" yalnız bu yorumda geçer — bir **hedef
değil, erişilemezlik kaydıdır**; T2'ye dokunmaz (eşik kararı yok, kayıp var).
`continue-on-error: true` satırı, anayasa soft-kapı ilkesinin tek satırlık kodu.

## §6 Secret tarama — tam job (ayrı workflow)

**Dosya:** `.github/workflows/secret-scan.yml` · **Satır:** 5-31

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

**Ne kanıtlar:** (1) tarama **ci.yml'e gömülmemiş** — ayrı workflow (anayasa D-4),
(2) `fetch-depth: 0` **zorunlu** (dosya L4 notu: aksi halde "no commits found"),
(3) config dosyası **YOK** → varsayılan gitleaks kuralları (L4 notu) — `.gitleaks.toml`
eklenmesi ADR + Security onayı ister (`.github/AGENTS.md` §3.1). `GITHUB_TOKEN`
**secret reference**'tır; gerçek değer hiçbir `.md`/`.log`'a yazılmaz.

## §7 Composer scriptleri (PHP kapılarının komut sahibi)

**Dosya:** `shared/composer.json` · **Satır:** 46-49

```json
    "scripts": {
        "test": "phpunit",
        "stan": "phpstan analyse src --level=5"
    },
```

**Eşleme:**

| Script | Yerel komut | CI çağrısı | Satır |
|---|---|---|---|
| `test` | `composer test` | `vendor/bin/phpunit "$PHPUNIT_ARGS"` (binary çağrısı) | `ci.yml:105` |
| `stan` | `composer stan` | `composer stan` (script çağrısı) | `ci.yml:63` |

> Fark bilinçli: PHPStan **script** ile, PHPUnit **binary** ile çağrılır. İkisi de
> aynı kapıları açar; sürüklenme yoktur (surucu §1 notu).

## §8 Bağımlılık kurulumu ve audit blokları

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 123-127 (audit — shared)

```yaml
      - name: Composer install + audit (shared)
        run: |
          composer install --no-progress --prefer-dist
          composer audit
        working-directory: shared
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 40-46 (cache)

```yaml
      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.cache/composer
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-
```

**Ne kanıtlar:** Audit **üç projede tekrarlanır** (`:123-127`, `:129-133`,
`:135-139`) — yani supply chain kapısı tüm composer yüzeyini tarar. Cache anahtarı
`composer.lock` hash'ine bağlıdır: kilit değişince önbellek düşer (kardeş R-10/W-31
dokümanıyla eşleşen davranış).

## §9 JS kapı adımları (kurulum → test)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 149-158 (js-test)

```yaml
      - uses: actions/setup-node@v4
        with:
          node-version: '26'
          cache: npm

      - name: npm ci
        run: npm ci

      - name: Vitest (unit)
        run: npm run test:js
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 199-203 (e2e kurulumu)

```yaml
      - name: Playwright install chromium
        run: npx playwright install --with-deps chromium

      - name: Playwright e2e
        run: npm run test:e2e
```

**Ne kanıtlar:** Node sürümü sabittir (`'26'` — L151 notu: vitest/jsdom engines
aralığı ile uyumlu). e2e'de tarayıcı kurulumu `--with-deps` ile sisteme bağımlılıkları
da kurar — lokal koşturma ile CI koşturma arasındaki farkı kapatır.

## §10 Komut × job matrisi (tam)

| Komut | Job | Sertlik | Satır |
|---|---|---|---|
| `composer install --no-progress --prefer-dist` | php-lint · php-test · composer-audit | Kurulum | `ci.yml:49,53,57,101,125,131,137` |
| `composer stan` | php-lint | Sert | `ci.yml:63` |
| `vendor/bin/phpunit "$PHPUNIT_ARGS"` | php-test | Sert | `ci.yml:105` |
| `composer audit` | composer-audit | Sert | `ci.yml:126,132,138` |
| `npm ci` | js-test · js-coverage · js-e2e | Kurulum | `ci.yml:155,174,197` |
| `npm run test:js` | js-test | Sert | `ci.yml:158` |
| `npm run test:js:coverage` | js-coverage | Soft | `ci.yml:176` |
| `npx playwright install --with-deps chromium` | js-e2e | Kurulum | `ci.yml:200` |
| `npm run test:e2e` | js-e2e | Soft | `ci.yml:203` |
| `gitleaks/gitleaks-action@v2` | secret-scan (ayrı wf) | Sert | `secret-scan.yml:29` |

## §11 Blok × kaynak doğrulama

| # | Blok | Dosya | Aralık | Blok satırı |
|---|---|---|---|---|
| B-1 | Workflow kök | `ci.yml` | 8-22 | 15 |
| B-2 | PHPStan adımı | `ci.yml` | 60-64 | 5 |
| B-3 | php-test matrix | `ci.yml` | 66-80 | 15 |
| B-4 | PHPUnit komutu | `ci.yml` | 104-108 | 5 |
| B-5 | js-coverage job | `ci.yml` | 160-176 | 17 |
| B-6 | js-e2e başlık | `ci.yml` | 178-183 | 6 |
| B-7 | Secret scan tam | `secret-scan.yml` | 5-31 | 27 |
| B-8 | Composer scripts | `shared/composer.json` | 46-49 | 4 |
| B-9 | Audit adımı | `ci.yml` | 123-127 | 5 |
| B-10 | Cache adımı | `ci.yml` | 40-46 | 7 |
| B-11 | setup-node + npm ci + vitest | `ci.yml` | 149-158 | 10 |
| B-12 | Playwright kurulum + e2e | `ci.yml` | 199-203 | 5 |

## §12 Blok son satırı indeksi

| Blok | Son satır içeriği | Beklenen |
|---|---|---|
| B-1 | `  PHP_VERSION: '8.4'` | 22. satır |
| B-2 | `        working-directory: shared` | 64. satır |
| B-3 | `            args: '--testsuite Unit'` | 80. satır |
| B-4 | `        working-directory: ${{ matrix.project }}` | 108. satır |
| B-5 | `        run: npm run test:js:coverage` | 176. satır |
| B-6 | `    continue-on-error: true` | 183. satır |
| B-7 | `          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}` | 31. satır |
| B-8 | `    },` | 49. satır |
| B-9 | `        working-directory: shared` | 127. satır |
| B-10 | `            ${{ runner.os }}-composer-` | 46. satır |
| B-11 | `        run: npm run test:js` | 158. satır |
| B-12 | `        run: npm run test:e2e` | 203. satır |

## §13 Negatif kanıt (bu workflow'larda OLMAYANlar)

| Arama | Sonuç | Anlam |
|---|---|---|
| `--coverage-*` (PHP tarafı) | 0 isabet | PHP kapsama komutu hiç koşmaz (T2 ile ilgili) |
| `continue-on-error` | 2 isabet (`:167`, `:183`) | Yalnız iki soft kapı; gerisi sert |
| `os:` / `runs-on` çeşitliliği | 0 isabet (tamamı `ubuntu-latest`) | Platform matrisi yok |
| `kunit` / `kselftest` | 0 isabet | OS test job'ı yok (T1) |
| `.gitleaks.toml` | 0 dosya | Varsayılan config (secret-scan.yml L4 notu) |
| deploy adım | 0 isabet | Deploy bu workflow'da yok (K018 sınırı) |

## §14 Kapsam dışı

| Konu | Sınır |
|---|---|
| Job ekleme/çıkarma | `.github/AGENTS.md` §3.3 sınır 4 — kullanıcı onayı |
| `.gitleaks.toml` yazımı | Security + ADR onayı |
| Deploy workflow'u | `K018-devops` |
| K019 workflow örnekleri | Komşu dal |

## §15 Eke — açık sorular

| # | Soru | Kapanış |
|---|---|---|
| T1 | OS test job'ı bu workflow'a girer mi? | Kullanıcı kararı + çekirdek kod varlığı |
| T2 | PHP kapsama job'ı eşiğe bağlı — eşiğe karar verilmeden eklenmez | T2 kararı |

## §16 Test yapılandırma kodu — `shared/phpunit.xml` testsuites + source

**Dosya:** `shared/phpunit.xml` · **Satır:** 8-38

```xml
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
```

**Ne kanıtlar:** (1) **8 testsuite** — §4'teki matrix args'inin hedefi budur
(`shared` satırında args boş → hepsi koşulur), (2) her suite bir `<directory>`
child'ı ile birebir dizin hedefler, (3) `<source><include>` **yalnız `src`**'yi
kapsam/uyarı kapsamına alır — test dosyaları kapsam dışıdır (kardeş N-8 iddiası),
(4) `<coverage>` bloğu **bu dosyada yoktur** (39. satır `</phpunit>` — §13 negatif
kanıtla aynı gerçek).

## §17 Test yapılandırma kodu — `auth.coremusic.net/phpunit.xml` sertlik + kaldırılmış suite

**Dosya:** `auth.coremusic.net/phpunit.xml` · **Satır:** 4-17

```xml
         bootstrap="tests/bootstrap.php"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
         failOnPhpunitWarning="false">

    <!-- Integration suite KALDIRILDI (2026-09-29): tests/Integration/ dizini diskte YOK →
         PHPUnit 10 "Test directory not found" + exit 2 ile TÜM testleri başlatmıyordu.
         Yeni integration testleri eklendiğinde bu suite geri eklenebilir. -->
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
```

**Dosya:** `auth.coremusic.net/phpunit.xml` · **Satır:** 26-32

```xml
    <!-- Rapor tanımı sürücü (Xdebug/PCOV) olmadan da zararsızdır; kapsam yalnız
         "coverage" bayraklarıyla toplanır. -->
    <coverage>
        <report>
            <text outputFile="php://stdout"/>
        </report>
    </coverage>
```

**Ne kanıtlar:** (1) bu projede `failOnRisky`/`failOnWarning` **true** — `shared`
ile yapılandırma **farklıdır** (§18 matrisi), (2) yorum satırı matrix args kararının
**disk gerekçesidir**: `tests/Integration` yok → `exit 2` → `--testsuite Unit`
(`ci.yml:77-80` ile birebir aynı gerekçe), (3) `<coverage><report>` **vardır** ama
`ci.yml`'de `--coverage-*` bayrağı **koşmaz** → rapor bloğu kendi başına ölçüm
üretmez (PHP sürücü gerektirir — yorumun kendi ifadesi).

## §18 JS kapı scriptleri — `package.json`

**Dosya:** `package.json` · **Satır:** 4-9

```json
  "scripts": {
    "test:js": "vitest run --root assets.coremusic.net",
    "test:js:watch": "vitest --root assets.coremusic.net",
    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",
    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"
  },
```

**Eşleme:**

| Script | CI çağrısı | Job | Sertlik |
|---|---|---|---|
| `test:js` | `npm run test:js` (`ci.yml:158`) | `js-test` | Sert |
| `test:js:coverage` | `npm run test:js:coverage` (`ci.yml:176`) | `js-coverage` | Soft |
| `test:e2e` | `npm run test:e2e` (`ci.yml:203`) | `js-e2e` | Soft |
| `test:js:watch` | Yok (izleme modu) | — | CI'da çağrılmaz |

## §19 PHP test bağımlılığı — `shared/composer.json` require-dev

**Dosya:** `shared/composer.json` · **Satır:** 24-27

```json
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "phpstan/phpstan": "^1.10"
    },
```

**Ne kanıtlar:** Kapıların iki PHP aracı da **geliştirme bağımlılığıdır**
(`require-dev`), üretim `require`'unda değildir (L9-22). Sürücü
`phpunit/phpunit ^10.5` — doküman iddialarının sürüm sınırı budur (kardeş N-7..N-11
11.5 sayfalarından okundu; sürüm farkı notu `-web-arastirma.md` §2'dedir).

## §20 CI matrix bağımlılık katmanı — cache anahtarı proje ayrımı

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 92-102

```yaml
      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.cache/composer
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}-${{ matrix.project }}
          restore-keys: |
            ${{ runner.os }}-composer-

      - name: Composer install
        run: composer install --no-progress --prefer-dist
        working-directory: ${{ matrix.project }}
```

**Ne kanıtlar:** php-test job'ının cache anahtarı **matrix projesini de içerir**
(`-${{ matrix.project }}`) — `ci.yml:44`'teki `php-lint` anahtarından **farklıdır**;
iki matrix kolonu birbirinin önbelleğini kirletemez. `working-directory` da aynı
değişkenden gelir: install ve PHPUnit **aynı dizinde** koşar.

## §21 Negatif kanıt — CI'a girmeyen iki phpunit.xml

| Dosya | Satır | Suite | `ci.yml` matrix'inde? |
|---|---|---|---|
| `shared/phpunit.xml` | 39 | 8 (Unit…OAuth) | Evet — `args: ''` (`ci.yml:75-76`) |
| `auth.coremusic.net/phpunit.xml` | 34 | 1 (Unit) | Evet — `--testsuite Unit` (`ci.yml:79-80`) |
| `home.coremusic.net/phpunit.xml` | 18 | 1 (Unit) | **Hayır** — matrix 2 satır (`ci.yml:74-80`) |
| `api.coremusic.net/phpunit.xml` | 29 | 1 (Unit) | **Hayır** — aynı sebep |

`home`/`api` matrix'te olmadığı için bu iki dosyanın testleri **bu workflow'da
koşmaz**; "4 proje test ediliyor" **denemez**. (`ci.yml:4-5` yorumu 3 composer.json
+ 2 phpunit.xml envanteri yazar — matrix yine 2 satırdır; çelişki değil, kurulum
ile test kapsamının farklı olmasıdır.)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
