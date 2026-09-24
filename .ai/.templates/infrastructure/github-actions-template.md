---
title: "CoreMusic — GitHub Actions CI/CD Template"
type: cicd-template
category: infrastructure
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — GitHub Actions CI/CD Template

**Platform:** GitHub Actions · **Durum:** TANIM YOK (diskte `.github/workflows/` bulunmuyor) · **Sorumlu Agent:** DevOps Engineer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../testing/phpunit-template]] · [[../infrastructure/migration-template]]

---

## 1. Amaç

Bu şablon, CoreMusic CI/CD pipeline'ını standartlaştırmaktır: CI iş akışı (PHP lint/test, JS/EDR denetimi, güvenlik taraması), CD/deploy taslaığı, branch protection ve secrets envanteri için hazır YAML iskeletleri sunar. **Guardrail #16:** yeni workflow dosyası bu şablondan üretilmek ZORUNLUDUR.

| Karar | Kaynak | Şablona gömülü karşılığı |
|-------|--------|-------------------------- |
| CI/CD tanımı henüz yok — "vault dokümantasyon aşamasında" | `.github/CLAUDE.md` §2 | §3.1 disk kanıtı + §4.4 |
| Yeni workflow = DevOps taslağı + kullanıcı onayı | `.github/CLAUDE.md` §4 | §5 Workflow 1-2 |
| Secret'lar repo settings'te; vault'a yazılmaz | `.github/CLAUDE.md` §4.2 | §4.1 #6, §3.5 |
| CI/CD success ≥ %95, GitLeaks clean | AGENTS.md §16 | §6 #9 |
| Test komutları | `phpunit-template` / `vitest-template` | §3.2 job adları |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.github/workflows/*.yml` — CI ve CD iş akışı tanımları | Uygulama kodu (`.php`, `.js`, `.css`) |
| `.github/settings.yml` — branch protection (Probot) | Migration içeriği → `[[../infrastructure/migration-template]]` |
| Secrets envanteri (yalnız adlar; değer YOK) | Donanım/firmware → `[[../hardware/hardware-template]]` |
| `codeowners`, issue şablonları (mevcut: 1 adet) | Test yazımı → `[[../testing/phpunit-template]]` |

- **Kullananlar:** DevOps Engineer (birincil), QA Engineer (test job'ları), Security Engineer (tarama job'ları).
- **Dosya tipi:** YAML workflow + Markdown şablon dokümanı.
- **Ön koşul:** `.github/CLAUDE.md` §4.1 — workflow ekleme önce taslak, sonra kullanıcı onayı; onaysız commit YOK.

---

## 3. Mimari

Şablonun gövdesi: disk kanıtı, CI/CD YAML iskeletleri, branch protection ve secrets envanteri. YAML içindeki `${{ ... }}` ifadeleri GitHub expression'ıdır; `{{VARIABLE}}` placeholder'ları ile karıştırılmaz, doldurulmaz.

### 3.1 Disk Kanıtı — `.github/` Envanteri

| Yol (disk kanıtı) | Durum |
|-------------------|-------|
| `.github/CLAUDE.md` | Mevcut — "Workflow (Actions): Yok — CI/CD tanımı vault dokümantasyon aşamasında" |
| `.github/ISSUE_TEMPLATE/01-bug-report.md` | Mevcut — tek issue şablonu |
| `.github/ISSUE_TEMPLATE/CLAUDE.md` | Mevcut — klasör bağlamı |
| `.github/workflows/` | **YOK** — workflow dizini henüz oluşturulmadı |
| `.github/settings.yml` | **YOK** — branch protection tanımsız |
| `.github/CODEOWNERS` | **YOK** ⚠️ VERIFICATION REQUIRED |

**Kural:** bu şablon bir *hedef/tasarım* şablonudur; diskte varmış gibi sunulmaz. YAML'lar §3.2-§3.4'te iskelet olarak verilir ve kullanıcı onayıyla oluşturulur (§5).

### 3.2 CI Pipeline İskeleti (`.github/workflows/ci.yml`)

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
      - name: PHP CS Fixer
        run: php-cs-fixer fix --dry-run --diff
      - name: PHPStan
        run: phpstan analyse shared/src --level=8

  php-test:
    name: PHP Tests
    runs-on: ubuntu-latest
    needs: php-lint
    defaults:
      run:
        working-directory: shared
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          extensions: pdo, pdo_mysql
      - name: Install dependencies
        run: composer install --no-progress --prefer-dist
      - name: PHPUnit
        run: vendor/bin/phpunit

  js-check:
    name: JS Static Check
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: ${{ env.NODE_VERSION }}
      - name: Syntax kontrolü (node --check)
        run: |
          find assets.coremusic.net/js -name "*.js" -print0 |
            xargs -0 -n1 node --check

  security:
    name: Security Audit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Composer audit
        run: composer audit --working-dir=shared
      - name: npm audit
        run: npm audit --audit-level=high
      - name: GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

> **Not (disk kanıtı):** `package.json` içinde yalnız `playwright ^1.62.1` vardır; vitest script'leri (`npm run test:coverage`) mevcut DEĞİLDİR. JS test job'ı, `[[../testing/vitest-template]]` kurulumu tamamlanana kadar yalnız `node --check` / Playwright E2E ile sınırlıdır (§4.4).

### 3.3 CD / Deploy İskeleti (`.github/workflows/deploy.yml`)

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

      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader --working-dir=shared

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
```

> **⚠️ VERIFICATION REQUIRED:** deploy adımları (SSH komutları, `composer`/migration çalıştırma sırası) gerçek sunucu kurulumuyla doğrulanmadan üretime alınmaz; `php artisan` kalıpları bu projede KULLANILMAZ (Laravel yok).

### 3.4 Branch Protection İskeleti (`.github/settings.yml`)

```yaml
# Probot/settings — branch protection
branches:
  - name: main
    protection:
      required_status_checks:
        strict: true
        contexts:
          - PHP Lint
          - PHP Tests
          - JS Static Check
          - Security Audit
      required_pull_request_reviews:
        required_approving_review_count: 1
      enforce_admins: false
      restrictions: null
```

> `contexts` listesi §3.2'deki `name:` alanlarıyla birebir eşleşmelidir (§4.1 #5).

### 3.5 Secrets Envanteri (yalnız adlar — değer asla vault'a yazılmaz)

| Secret adı | Amaç | Kullanan job |
|------------|------|--------------|
| `GITHUB_TOKEN` | Otomatik token | security (GitLeaks) |
| `SERVER_HOST` | Sunucu adresi | deploy |
| `SERVER_USER` | SSH kullanıcısı | deploy |
| `SSH_KEY` | SSH private key | deploy |
| `DB_PASSWORD` | Veritabanı şifresi | deploy/migration |
| `CSRF_SECRET` | CSRF anahtarı | deploy |
| `JWT_SECRET` | JWT imzalama anahtarı | deploy |

**REDACTED:** tabloda yalnız adlar bulunur; değerler, `.env` içeriği ve log kayıtları vault'a KONMAZ (Guardrail REDACTED).

### 3.6 Job Bağımlılık Şeması

```
push / pull_request
        │
        ├──► php-lint ─────────────► php-test
        │
        ├──► js-check (node --check)
        │
        └──► security (composer audit + npm audit + GitLeaks)
                        │
                        └──► [main] deploy (environment: production)
```

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Workflow YAML'ında secret değeri yazmaz; yalnız `${{ secrets.* }}` | Sızıntı |
| 2 | `on:` / `jobs:` / `steps:` yapısı ve girinti bozulmaz | Workflow çalışmaz |
| 3 | `${{ ... }}` ifadeleri `{{VARIABLE}}` gibi doldurulmaz | YAML bozulması |
| 4 | `environment: production` korunur; onaysız deploy job'ı yok | Kontrolsüz yayım |
| 5 | `required_status_checks.contexts` job adlarıyla eşleşir | Branch protection kırılır |
| 6 | `.github/CLAUDE.md` §4.1 onay akışı zorunlu (taslak → onay) | Yetkisiz değişiklik |
| 7 | Diskte olmayan job/script adı iddia edilmez | Halüsinasyon |

### 4.2 Ek Kurallar

- **Zorunlu:** coverage işi varsa `--coverage-clover=coverage.xml` üretilir; hedef ≥ %80 (AGENTS.md §16, `[[../testing/phpunit-template]]`).
- **Zorunlu:** migration gerektiren deploy, `migration-template` §4.1 ile uyumlu (forward-only) çalıştırır.
- **Zorunlu:** tüm job adları §3.2'deki gibi Türkçe/İngilizce tutarlı ve tektir; branch protection listesi bu adları yansıtır.
- **Yasak:** vault dosyalarına (`.ai/**`) CI'dan yazma; CI yalnızca okur ve raporlar.
- **Yasak:** log çıktısına token/şifre basmak; ihlalde job durdurulur ve `[REDACTED]` ile maskelenir.
- **Uyarı:** `.github/workflows/` diskte yoktur — bu şablon "mevcut pipeline" değil, "oluşturulacak pipeline" tanımlar (§3.1).

### 4.3 İzinli / Yasak Komutlar

| Katman | İzinli | Yasak |
|--------|--------|-------|
| CI (okuma) | `composer install`, `phpunit`, `node --check`, `npm audit`, `gitleaks` | `git push --force`, `docker rm` |
| CI (test) | `vendor/bin/phpunit`, Playwright E2E | Test verisini üretim DB'ye yazmak |
| CD (deploy) | `git pull`, `composer install --no-dev` | Secrets'ı yazdırmak, `rm -rf` |
| Vault | Okuma | `.ai/**` içine CI yazması |

### 4.4 Bilinen Boşluklar (disk kanıtı)

| # | Boşluk | Durum | Eylem |
|---|--------|-------|-------|
| 1 | `.github/workflows/` yok | Tasarım aşaması | §3.2-§3.3 onayla → oluştur |
| 2 | `.github/settings.yml` yok | Branch protection kapalı | §3.4 |
| 3 | vitest yok (`package.json` yalnız playwright) | JS test job'ı kısıtlı | `[[../testing/vitest-template]]` |
| 4 | `CODEOWNERS` yok ⚠️ | Doğrulanmadı | Eklenip eklenmeyeceğine karar |
| 5 | `phpunit.xml` 2 kökte (shared, auth) | Suite dağılımı | Job working-directory ayrımı (§3.2) |

---

## 5. Workflow

```
İHTİYAÇ → ŞABLONU SEÇ → TASLAK YAML → KULLANICI ONAYI → .github/workflows/ OLUŞTUR → İLK ÇALIŞTIRMA → COMMIT
```

1. **İHTİYAÇ:** job gereksinimi (lint/test/tarama/deploy) netleştirilir.
2. **ŞABLONU SEÇ:** `.ai/.templates/infrastructure/github-actions-template.md` (Guardrail #16).
3. **TASLAK YAML:** §3.2-§3.4 iskeletlerini `{{VARIABLE}}` ile uyarla.
4. **KULLANICI ONAYI:** `.github/CLAUDE.md` §4.1 — onay alınmadan dosya oluşturulmaz (DUR noktası).
5. **OLUŞTUR:** `.github/workflows/{{file}}.yml` + gerekirse `.github/settings.yml`.
6. **İLK ÇALIŞTIRMA:** GitLeaks + `composer audit` + `npm audit` temiz; job adları §3.4 ile eşleşiyor.
7. **COMMIT:** `log.md` append'i parent yapar; secret değerleri vault'a yazılmaz.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan (title, type, category, date, updated, version, status, authority) |
| 2 | Bölüm yapısı | §1-§7 numaralı, en fazla 3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı; `${{ }}` expression'ları KORUNMUŞ |
| 4 | YAML | `on:`/`jobs:`/`steps:` girintisi ve girinti düzeni bozulmadı |
| 5 | Secret | Dosyada gerçek değer yok; yalnız `secrets.*` referansı |
| 6 | Eşleşme | §3.4 `contexts` ↔ §3.2 job `name:` alanları birebir |
| 7 | Onay | `.github/CLAUDE.md` §4.1 akışı uygulandı |
| 8 | Deploy | `environment: production` mevcut |
| 9 | Halüsinasyon | Diskte olmayan dosya/job "mevcut" gibi sunulmadı (§3.1) |
| 10 | Tarama | İlk çalıştırmada GitLeaks + audit temiz (AGENTS.md §16 ≥ %95) |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter (DRY) |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, ADR-042 |
| Agent registry | [[../../AGENTS.md]] | §6 yönlendirme (CI/CD → DevOps Engineer), §16 kalite |
| Klasör bağlamı | `.github/CLAUDE.md` | "Workflow yok" gerçeği + onay protokolü |
| Test şablonu | [[../testing/phpunit-template]] | `php-test` job komutları |
| Test şablonu (JS) | [[../testing/vitest-template]] | JS test job ön koşulu (kurulu değil) |
| Migration şablonu | [[../infrastructure/migration-template]] | Deploy sırasında forward-only migration |
| Disk kanıtı | `.github/` glob (3 dosya, workflows YOK) | §3.1 |
| Kalite standardı | AGENTS.md §16 | CI/CD ≥ %95, GitLeaks clean |

---

## 8. Kapsamlı Workflow Şablonu Galerisi

### §8.1 Gece Cron Job (Nightly Audit)

```yaml
name: Nightly Audit

on:
  schedule:
    - cron: '0 3 * * *'      # her gece 03:00 UTC
  workflow_dispatch:

jobs:
  dependency-audit:
    name: Dependency Audit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Composer audit
        run: composer audit --working-dir=shared --format=plain
      - name: npm audit (yalnız high/critical)
        run: npm audit --audit-level=critical || true
      - name: GitLeaks (tarih aralıksız)
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

### §8.2 E2E Test Job (Playwright — kurulu paket)

```yaml
  e2e:
    name: E2E Playwright
    runs-on: ubuntu-latest
    needs: php-test
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - name: Playwright install
        run: npx playwright install --with-deps chromium
      - name: E2E suite
        run: npx playwright test
        env:
          BASE_URL: https://staging.coremusic.net
```

> Disk kanıtı: `package.json` → `playwright ^1.62.1` mevcut; bu job ilk işlenebilir JS test katmanıdır.

### §8.3 Migration Guard Job

```yaml
  migration-guard:
    name: Migration Guard
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: migration dosyalarını listele
        run: ls -1 shared/database/migrations/*.php | tee migrations.txt
      - name: down() kontrolü
        run: |
          for f in shared/database/migrations/*.php; do
            grep -q "public function down()" "$f" || { echo "EKSİK down(): $f"; exit 1; }
          done
```

> `database/migrations/` kökte YOK — gerçek yol `shared/database/migrations/` (glob kanıtı).

### §8.4 Cache & Artefakt Desenleri

| Desen | Adım | Not |
|---|---|---|
| Composer cache | `actions/cache@v4` → `vendor/` key = hashFiles('shared/composer.lock') | lock yoksa cache devre dışı |
| npm cache | `setup-node` `cache: npm` | yalnız `package-lock.json` varsa |
| Coverage artefact | `actions/upload-artifact@v4` → `coverage.xml` | phpunit `--coverage-clover` |
| Test raporu | `actions/upload-artifact@v4` → Playwright `test-results/` | fail durumunda |
| Gibhub Job Summary | `echo >> $GITHUB_STEP_SUMMARY` | sonuç tablosu |

### §8.5 Matrix Stratejisi (PHP sürüm varyantı)

```yaml
  php-matrix:
    name: PHP ${{ matrix.php }}
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.3', '8.4']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      - run: composer install --working-dir=shared
      - run: vendor/bin/phpunit -c shared/phpunit.xml
```

---

## 9. Rollback & Onarım

| Senaryo | Tetik | Aksiyon |
|---|---|---|
| CI kırmızı (main) | push sonrası fail | hata düzelt → revert commit; force push YASAK |
| Deploy sonrası 500 | health check fail | sunucuda `git reset --hard HEAD~1` (SSOT deponun son yeşil SHA'sı) |
| Migration geri alınamaz | `down()` eksik | §8.3 guard → pre-merge'da yakalanır; elle SQL backup |
| Secret sızdı | GitLeaks fail | GitHub Settings → secret rotasyonu + [REDACTED] log temizliği |
| Cache zehirlendi | tuhav test fail | cache key bump / `actions/cache` restore-keys temizle |
| Workflow deploy'u durdur | branch protection | Settings → environments → required reviewers |

```bash
# Sunucu tarafı hızlı rollback (deploy job'ı içinde de çağrılabilir)
cd "$DEPLOY_PATH"
git fetch origin && git reset --hard "${PREVIOUS_SHA}"
composer install --no-dev --optimize-autoloader
```

⚠️ `PREVIOUS_SHA` bir önceki yeşil commit'tir; deployment öncesi job çıktısına yazılır (`VERIFICATION REQUIRED` — gerçek sunucu koşullarıyla doğrulanmalı).

---

## 10. Hata Masası (CI/CD)

| Hata | Konum | Neden | Çözüm |
|---|---|---|---|
| `ModuleNotFoundError` | php-test | `working-directory` yanlış | `defaults.run.working-directory: shared` |
| `permission denied (publickey)` | deploy | SSH_KEY eksik/yanlış | Settings → Secrets → `SSH_KEY` yenile |
| `gitleaks: no commits found` | security | fetch derinliği | `fetch-depth: 0` checkout'a ekle |
| Job takılmıyor, koşmuyor | onay eksik | §4.1 onayı verilmedi | kullanıcı onayı → commit |
| `contexts` eşleşmiyor | settings.yml | job `name:` değişti | §3.2 ↔ §3.4 senkronu |
| `npm ci` fail | js-check | lock yok | `npm install` veya lock oluştur |
| Coverage yok | php-test | phpunit coverage sürücüsü | `XDEBUG_MODE=coverage` + `--coverage-clover` |
| Cache hit yok | her job | key değişken | `hashFiles` lock dosyasına bağla |
| `environment: production` uyarısı | deploy | environment tanımsız | Repo → Environments → `production` oluştur |

---

## 11. İdame Checklist (Sprint Sonu)

| # | Kontrol | Sıklık |
|---|---|---|
| 1 | Actions pin sürümü (`@v4` → SHA pin önerilir) | aylık |
| 2 | Secret son kullanma / rotasyon | 90 gün |
| 3 | Job süreleri (runtime bütçesi) | sprint |
| 4 | `composer audit` / `npm audit` temizliği | her gece (§8.1) |
| 5 | Branch protection contexts = canlı job adları | job ekleme/çıkarma |
| 6 | Rollback provası (staging) | çeyreklik |

---

## 12. Ek Workflow Paternleri

### §12.1 İzin (Concurrency) & İptal

```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true     # aynı ref'te eski koşuyu iptal (CI maliyet tasarrufu)
```

| Ayar | Değer | Neden |
|---|---|---|
| `cancel-in-progress` (CI) | `true` | eski commit koşusu gereksiz |
| `cancel-in-progress` (deploy) | `false` | yarım deploy yasak |
| Job timeout | `timeout-minutes: 15` | takılan job bütçeyi yakmasın |

### §12.2 Manuel Tetik (workflow_dispatch) Girdileri

```yaml
on:
  workflow_dispatch:
    inputs:
      target:
        description: 'Ortam'
        required: true
        default: 'staging'
        type: choice
        options: [staging, production]
      skip_tests:
        description: 'Test atla (acil hotfix)'
        type: boolean
        default: false
```

### §12.3 Şartlı Adım Kalıpları

```yaml
      - name: Migration çalıştır (yalnız production)
        if: github.ref == 'refs/heads/main' && github.event_name == 'push'
        run: php shared/database/migrations/run.php
      - name: Test adımı
        if: ${{ !inputs.skip_tests }}
        run: vendor/bin/phpunit -c shared/phpunit.xml
```

### §12.4 PR Yorumu & Özet

```yaml
      - name: Job Summary
        if: always()
        run: |
          echo "### CI Sonucu" >> $GITHUB_STEP_SUMMARY
          echo "- PHP: \`${{ job.status }}\`" >> $GITHUB_STEP_SUMMARY
          echo "- Commit: \`${GITHUB_SHA::7}\`" >> $GITHUB_STEP_SUMMARY
```

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
