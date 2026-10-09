---
title: "K013 CI/CD «PUSULA» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K013-cicd/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: cicd
ssot: true
risk: medium
owner: devops
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K013 CI/CD «PUSULA» — Katman Index

> **Authority:** Bu dosya K013 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K013 kartı` > `.ai/CLAUDE.md §5/§12` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2`. **Durum:** `draft` — bant onayı Kapı 10'da 👤.
> Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K013 |
| Kanonik Ad | CI/CD |
| Teatral Epitet | «PUSULA» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K013-cicd/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / cicd |
| Owner (`.ai/AGENTS.md` §4 registry) | devops |
| Risk | medium — tedarik zinciri/secret tarama yüzeyi; K006 dışı high yok |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-023 · ADR-082 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K013 CI/CD «PUSULA», kodun güvenli ve tekrarlanabilir şekilde üretilip dağıtılmasının pusulasıdır:
Source Control · Build · Static Analysis · Unit/Integration/E2E · Security Scanning · Artifacts ·
Rollback (anayasa §A.1 K013) + GitHub Actions, Playwright, Vitest, Docker, K8s (anayasa §5 K13 —
**35 bileşen, "Build ve Deployment otomasyonu"**). Repo kanıtı güçlü ve ölçüldü (ls 2026-10-08):
`.github/workflows/ci.yml` (203 satır · 6 job) + `secret-scan.yml` (31 satır · GitLeaks job) +
phpunit.xml ×4 + vitest/playwright configs + npm/composer test script'leri. Docker/K8s diskte
GÖZLENMEDİ → container/orkestrasyon ayağı PLANNED (H1).
Kapsam dışı: runtime davranışı (K000-K012) · güvenlik kararları (K006) · iş verisi (K008/K005) ·
izinli olmayan commit/deploy (H6/R10) — bu katman yalnız **derleme, doğrulama ve tedarik** ile
yükümlüdür; testleri ÇALIŞTIRIR ama testin konusunu (kapsam hedefi) K007-K012 kartları tanımlar.

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K013 | CI/CD | «PUSULA» | cicd | GitHub Actions runner (ubuntu-latest) · PHP 8.4 + Composer · Node.js LTS (Vitest/Playwright) · Docker 24+/K8s (hedef — PLANNED) | Source Control · Build · Static Analysis (PHPStan L5) · Unit/Integration/E2E · Security Scanning (GitLeaks) · Artifacts · Rollback — 35 bileşen (anayasa §5 K13) | git push/PR olayları · kaynak kod · test komutları · yapılandırma (workflow) · secret (yalnız GitHub Secrets) | geçerlilik kararı (pass/fail) · test/rapor çıktıları · artefakt (hedef) · deploy (hedef) · rollback (hedef) | K000-K012 (alt katmanlar) · GitHub Actions (dış platform) · kaynak ağacı | K013 → katmanlara runtime geri çağrısı (H20) · deploy'u K012'nin işi yapması · plaintext secret (§23 #3) · onaysız commit (H6) · H19 | yalnız build-time veri: kod + artefakt + CI logu; runtime verisi/DB YOK | GitLeaks secret tarama · `permissions: contents: read` (minimal) · CI secret'ları yalnız GitHub Secrets · onaysız deploy yasağı (H6/R10) | workflow başarısız → build kırmızı (deploy durur) · runner kaybı → kuyruk · rollback (EK A) · branch koruması tanımsız ⚠️ | CI job süreleri · test sonuçları · artefakt imzalama (hedef ⚠️) · K012'ye build sinyali | CI = test çalıştırıcısı: PHPUnit ×4 · Vitest · Playwright + PHPStan L5 (repo: workflow içinde) · hedef ≥80% (§17) | repo: `.github/workflows/ci.yml` (203 satır: php-lint · php-test · composer-audit · js-test · js-coverage · js-e2e) · `.github/workflows/secret-scan.yml` (31 satır: gitleaks) · `package.json` (test:js/coverage/e2e) · `shared/composer.json` (scripts: test, stan L5) · phpunit.xml ×4 · vitest/playwright configs (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K13/§12/§17` + `00-kspace-anayasa.md §A.1 K013` · ADR: ADR-023/082 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt (H10): "35 bileşen · Docker/K8s" anayasa
hedefidir; "2 workflow dosyası (203+31 satır) · 6 job · 4 phpunit" repo kanıtıdır — birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K013 |
| 2 | KANONİK_AD | CI/CD |
| 3 | TEATRAL_EPİTET | «PUSULA» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | cicd |
| 5 | SUBDOMAIN | source-control · build · static-analysis · unit-integration-e2e · security-scanning · artifacts · rollback · deployment |
| 6 | BOUNDED_CONTEXT | Teslimat & Doğrulama — kodu derler/test eder/tarama yapar; runtime davranışı üretmez (runtime K000-K012'de) |
| 7 | RUNTIME | GitHub Actions (ubuntu-latest · `permissions: contents: read`) · PHP 8.4 + Composer · Node.js LTS (Vitest/Playwright/Chromium) · Docker 24+/K8s (hedef — repo'da YOK) |
| 8 | SORUMLULUK | Source Control (push/PR tetikli) · Build (composer install ×3 + npm ci) · Static Analysis (PHPStan `--level=5`) · Unit (PHPUnit/Vitest) · Integration/E2E (Playwright — soft gate) · Security Scanning (GitLeaks) · Artifacts (hedef ⚠️) · Rollback (hedef ⚠️) |
| 9 | GIRDI | git push/PR olayı · kaynak kod · workflow tanımları · composer.lock/package-lock · test komut script'leri · GitHub Secrets (yalnız oradan) |
| 10 | CIKTI | job pass/fail (kapi görevi) · PHPUnit/Vitest/PHPStan/Playwright raporları · coverage raporu (soft) · artefakt/deploy (hedef PLANNED) |
| 11 | IZINLI_BAGIMLILIK | K000-K012 (alt katmanlar) · GitHub Actions platformu (dış) · Composer/npm (paket yöneticileri) · PHPStan/Vitest/Playwright/GitLeaks (dış araçlar) |
| 12 | YASAK_BAGIMLILIK | K013 → katmanlara runtime geri çağrısı (H20) · K012'nin işine (alert→deploy senkron komutu) · plaintext secret/`.env` commit'i (§21/§23 #3) · onaysız commit/deploy (H6 · R10) · H19 veri paylaşımı |
| 13 | DATA_BOUNDARY | yalnız build-time veri (kod, lock dosyaları, CI logları, artefakt); runtime veritabanı/iş verisi YOK; secret yalnız GitHub Secrets + `.env` (kodda yok — §21) |
| 14 | SECURITY_BOUNDARY | secret tarama (GitLeaks job) · minimal token (`permissions: contents: read`) · composer-audit (bağımlılık denetimi) · deploy onayı = 👤 (R10) |
| 15 | FAILURE_MODE | job başarız → build kırmızı → merge/deploy durur (fail-safe teslimat) · runner kuyruğu · geçici ağ/paket hatası → retry (workflow concurrency `cancel-in-progress`) · rollback hedefi ⚠️ (PLANNED) |
| 16 | OBSERVABILITY | job duration/sonuçları · coverage (soft gate A7) · E2E (soft gate A6) · secret-scan sonucu · K012'ye build sinyali (GitHub logları) |
| 17 | TEST | K013'ün KENDİSİ test çalıştırıcısı: PHPUnit 11 (phpunit.xml ×4: api, auth, home, shared) · Vitest (`test:js`) · Playwright (`test:e2e`) · PHPStan L5 (`stan`) — hepsi repo'da (ls 2026-10-08); hedef ≥80% (§17) |
| 18 | KANIT | repo: `.github/workflows/ci.yml` (ls 2026-10-08; 203 satır; job: php-lint/php-test/composer-audit/js-test/js-coverage/js-e2e) · `.github/workflows/secret-scan.yml` (31 satır; gitleaks; `.gitleaks.toml` header notu: YOK) · `package.json` scripts (test:js · test:js:coverage · test:e2e) · `shared/composer.json` scripts (test · stan `--level=5`) · `phpunit.xml` (api/auth/home/shared) · `assets.coremusic.net/{vitest.config.js,playwright.config.ts}` · vault: `.ai/CLAUDE.md §5 K13 · §12 · §17` + `00-kspace-anayasa.md §A.1 K013` · ADR: ADR-023/082 (accepted/ ls) · ADR-096 · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «PUSULA» — yön gösteren, yol ayrımında yol gösteren araç metaforu (doğru yolu = geçerli build); EK A §A.1 anahtar satırı: `K013 · CI/CD · «PUSULA» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ · (c) KANIT 3'lü ✓ (web ayağı ⚠️) ·
(d) veri sınırı: build-time veri K013'te; runtime verisi K008/K005'te ✓.

### §3.1 Özet Satır ↔ EK C Tutarlılık Kontrolü (R4.4a)

| Özet alanı (§2) | EK C karşılığı (§3) | Tutarlı? |
|---|---|---|
| K-ID / KANONİK_AD / TEATRAL_EPİTET | alan 1/2/3 | ✓ |
| DOMAIN | alan 4 | ✓ |
| RUNTIME | alan 7 | ✓ |
| SORUMLULUK | alan 8 | ✓ |
| GİRDİ / ÇIKTI | alan 9/10 | ✓ |
| İZİNLİ / YASAK | alan 11/12 | ✓ (kümeler disjoint) |
| DATA / SECURITY BOUNDARY | alan 13/14 | ✓ |
| FAILURE_MODE | alan 15 | ✓ |
| OBSERVABILITY | alan 16 | ✓ |
| TEST | alan 17 | ✓ |
| KANIT | alan 18/19/20 | ✓ (3'lü + tarih + epitet notu) |

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K013 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Source Control · Build · Static Analysis ·
Unit/Integration/E2E · Security Scanning · Artifacts · Rollback".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Source Control | git tabanlı kaynak kontrolü; push/PR tetikli workflow | IMPLEMENTED | `.github/workflows/ci.yml` `on: push + pull_request` (ls 2026-10-08) |
| Build | composer install ×3 (shared · auth · home) + npm ci | IMPLEMENTED | ci.yml job adımları (ls: "Composer install (shared/auth/home)" + "npm ci") · `composer.lock`/`package-lock.json` (kök ls) |
| Static Analysis | PHPStan `--level=5` | IMPLEMENTED | ci.yml job `php-lint` ("PHPStan (composer stan)") + `shared/composer.json` scripts.stan (ls) |
| Unit/Integration/E2E | PHPUnit (matrix) · Vitest · Playwright | IMPLEMENTED (E2E soft gate) | ci.yml `php-test · js-test · js-coverage · js-e2e` (ls) · `package.json` test:* scripts |
| Security Scanning | GitLeaks secret taraması | IMPLEMENTED (workflow VAR) · `.gitleaks.toml` YOK (varsayılan config) | `secret-scan.yml` (31 satır · header notu ls) |
| Artifacts | build çıktısı/imzalama/arşiv (hedef) | PLANNED ⚠️ — workflow'da artefakt adımı gözlenmedi | anayasa §A.1 · ⚠️ |
| Rollback | dağıtım geri alma (hedef) | PLANNED ⚠️ — deploy/rollback workflow yok | anayasa §A.1 · repo: deploy workflow YOK |

### §4.2 Anayasa §5 K-Matrix Satırı (K13) — 35 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K13 CI/CD & Deploy | GitHub Actions, Playwright, Vitest, Docker,
K8s | 35 | Build ve Deployment otomasyonu.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| GitHub Actions | CI workflow motoru | IMPLEMENTED (2 workflow) | `.github/workflows/{ci.yml,secret-scan.yml}` (ls) |
| Playwright | E2E test | IMPLEMENTED (soft gate A6) | ci.yml `js-e2e` + `assets.coremusic.net/playwright.config.ts` (ls) · `package.json test:e2e` |
| Vitest | JS unit test + coverage | IMPLEMENTED (coverage soft gate A7) | ci.yml `js-test/js-coverage` + `vitest.config.js` (ls) · `package.json test:js*` |
| Docker | konteynerleştirme (build/deploy) | PLANNED ⚠️ — Dockerfile/docker-compose kökte YOK (ls 2026-10-08) | `.ai/CLAUDE.md §12 (Docker 24+)` · repo: YOK |
| K8s | orkestrasyon | PLANNED ⚠️ — manifest YOK | §5 K13 · repo: YOK |
| "35 bileşen" | K13 envanter toplamı | HEDEF (H10) | `.ai/CLAUDE.md §5 K13` |
| Hard guardrail | "Build ve Deployment otomasyonu" | BAĞLAYICI | `.ai/CLAUDE.md §5 K13` |

### §4.3 `ci.yml` İş Akışı Detayı (repo — 203 satır, ls 2026-10-08)

| # | Job | Adımlar (özet) | Kapı niteliği | Durum |
|---|---|---|---|---|
| 1 | `php-lint` | PHP 8.4 setup (apcu, mbstring, pdo_mysql) · Composer cache · composer install ×3 · PHPStan (`composer stan`) | statik analiz kapısı | IMPLEMENTED |
| 2 | `php-test` | matrix (project bazlı) · composer install · PHPUnit | unit kapısı | IMPLEMENTED |
| 3 | `composer-audit` | composer install + `composer audit` ×3 (shared/auth/home) | bağımlılık güvenlik taraması | IMPLEMENTED |
| 4 | `js-test` | npm ci · Vitest unit | JS unit kapısı | IMPLEMENTED |
| 5 | `js-coverage` | Vitest coverage — **soft gate (A7)** | coverage (esnek — Soft Constraint #1 ile uyum) | IMPLEMENTED (soft) |
| 6 | `js-e2e` | PHP setup · Playwright chromium install · Playwright e2e — **soft gate (A6)** | E2E | IMPLEMENTED (soft) |
| — | tetikleyici | `on: push` + `pull_request` · concurrency: `cancel-in-progress` · `permissions: contents: read` | — | IMPLEMENTED |
| — | eski rejim izi | header: `Plan: .ai/architecture/k13-cicd/README.md` (K13.1/K13.2) — bu eski rejim yolu çalışma ağacında YOK (D=95 rejim değişikliği) | BORÇ → güncellenmeli ⚠️ | stale referans |

### §4.4 `secret-scan.yml` Detayı (repo — 31 satır, ls 2026-10-08)

| Öğe | Değer | Durum |
|---|---|---|
| Job | `gitleaks` (ubuntu-latest · timeout 10dk) | VAR |
| Tetik | push + pull_request · concurrency cancel-in-progress | VAR |
| Token | `permissions: contents: read` (minimal — iyi uygulama) | VAR |
| Config | `.gitleaks.toml` **YOK** → varsayılan gitleaks config (header notu) | EKSİK → ⚠️ |
| Şablon kaynağı | `.ai/.templates/infrastructure/github-actions-template.md §3.2` (Guardrail #16 — header'da kayıtlı) | VAR |
| Eski rejim izi | header: `Plan: .ai/architecture/k13-cicd/security-scanning.md` — çalışma ağacında YOK | BORÇ ⚠️ |

### §4.5 Test Alt Yapısı Envanteri (repo — ls 2026-10-08)

| # | Varlık | Kapsam | Durum |
|---|---|---|---|
| 1 | `phpunit.xml` — `api.coremusic.net/` | gateway unit | VAR |
| 2 | `phpunit.xml` — `auth.coremusic.net/` | auth paneli | VAR |
| 3 | `phpunit.xml` — `home.coremusic.net/` | home paneli | VAR |
| 4 | `phpunit.xml` — `shared/` | paylaşılan kütüphane (Api · Events · Repository · Security · Unit · Middleware · OAuth · Component · Fixtures) | VAR |
| 5 | `assets.coremusic.net/vitest.config.js` + `js/core/breakpoints.spec.js` | JS unit | VAR |
| 6 | `assets.coremusic.net/playwright.config.ts` | E2E | VAR |
| 7 | `package.json` scripts | `test:js` · `test:js:watch` · `test:js:coverage` · `test:e2e` | VAR |
| 8 | `shared/composer.json` scripts | `test` (phpunit) · `stan` (phpstan L5) | VAR |
| 9 | `media.coremusic.net/phpunit.xml` | media panel testi | GÖZLENMEDİ → ⚠️ |
| 10 | Download (Vitest) · Audio (Google Test) altyapıları | §17 hedefleri | PLANNED (servis dizinleri yok — K008 §4.3.3/.7) |

### §4.6 Kapı (Gate) Haritası — F1 On Kapı ile eşleşme (R11)

| F1 Kapı | K013 karşılığı | Durum | Kanıt |
|---|---|---|---|
| KAPI 7 (research ≥85) | — (K013 dışı; vault süreci) | süreç kapısı | `rules.md R11.1` |
| KAPI 9 (hallucination) | — (K013 dışı) | süreç kapısı | `rules.md R11.1` |
| Test kapısı (§17 ≥80%) | php-test + js-test + coverage (soft) | IMPLEMENTED (soft) | ci.yml jobs |
| Statik analiz | PHPStan L5 | IMPLEMENTED | ci.yml `php-lint` |
| Secret kapısı | GitLeaks | IMPLEMENTED · config eksik | `secret-scan.yml` |
| Coverage politikası | soft gate (A7) — Soft Constraint #1: %80 hedef, geçici %75 | IMPLEMENTED (esnek) | ci.yml "soft, A7" · `.ai/CLAUDE.md §8 #1` |
| Timeout politikası | job timeout'ları (lint 15dk · gitleaks 10dk) | IMPLEMENTED · 30s/60s request limiti K010/K008'de | ci.yml/secret-scan.yml · §8 #2 |
| Deploy onayı | 👤 (R10 · H6) — deploy workflow henüz YOK | PLANNED ⚠️ | `rules.md R10` |

### §4.6.1 Gate Zinciri (ci.yml 203 satır · secret-scan.yml 31 satır — 2026-10-08 kanıtı)

```text
push / pull_request
  → php-lint        PHP Lint + PHPStan L5            [zorunlu]
  → php-test        PHPUnit · matrix.project         [zorunlu · §17 %80 hedef]
  → composer-audit  Composer Audit (shared)          [zorunlu · A03 tedarik]
  → js-test         Vitest (package script)          [zorunlu]
  → js-coverage     soft — A7 geçici %75             [soft]
  → js-e2e          Playwright · soft                [soft]
  → gitleaks        secret-scan.yml · timeout 10dk   [zorunlu · config varsayılan ⚠️]
  → deploy/rollback                                   [PLANNED — workflow YOK ⚠️]
```

| # | Gate | Job id | Durum | Sınır / not |
|---|------|--------|-------|-------------|
| 1 | Statik PHP | `php-lint` | IMPLEMENTED | PHPStan level 5 (composer stan) |
| 2 | Test PHP | `php-test` | IMPLEMENTED | matrix.project (shared · auth · home · media) |
| 3 | Bağımlılık denetimi | `composer-audit` | IMPLEMENTED | A03 supply-chain (§9.1) |
| 4 | Test JS | `js-test` | IMPLEMENTED | Vitest · WP2 altyapısı |
| 5 | Coverage JS | `js-coverage` | SOFT | §8 #1: %80 hedef · geçici %75 |
| 6 | E2E JS | `js-e2e` | SOFT | Playwright · vhost baseURL |
| 7 | Secret | `gitleaks` | IMPLEMENTED | `.gitleaks.toml` YOK → varsayılan ⚠️ |
| 8 | Deploy | — | PLANNED ⚠️ | deploy workflow + rollback kanıtı yok |

### §4.7 K013 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K013 etkisi |
|---|---|---|
| ADR-082-dev-environment | geliştirme ortamı kararı | build/tetik ortamı temeli |
| ADR-023-persona-driven-testing | persona bazlı test yaklaşımı | test stratejisi (§4.5) |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | format/bağımlılık kaynağı |

### §4.8 K013 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K013'ün verdiği | Beklenen | Kanıt |
|---|---|---|---|---|
| K007-K012 (test üreten katmanlar) | test çalıştırma | pass/fail + rapor | test komutları (`test`/`stan`) | `composer.json`/`package.json` scripts (ls) |
| K012 OBSERVABILITY | build sinyali | CI log/sonuç akışı | alert (yukarı) | R6.2 · K012 §4.9 |
| K006 SECURITY | secret tarama | GitLeaks sonucu | secret politikası | `secret-scan.yml` · §23 #3 |
| K005 | migration/şema (varsa) | şema değişikliği workflow'ları | PLANNED ⚠️ | ADR-014 (accepted/ ls) · ⚠️ |
| Git platformu | push/PR tetikleyici | workflow çalıştırma | branch koruması ⚠️ | ci.yml `on:` |

### §4.9 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K13 hedefi | 35 bileşen · GitHub Actions/Playwright/Vitest/Docker/K8s (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | 2 workflow (203+31 satır · 7 job) · 4 phpunit · vitest+playwright configs · npm/composer test script'leri |
| Kapsam | CI ≈ IMPLEMENTED (6 job + secret scan) · Docker/K8s/Artefakt/Rollback/Deploy = PLANNED |
| Eksik config | `.gitleaks.toml` YOK · stale eski-rejim header referansları (k13-cicd) |
| Test altyapısı | 4/5 panel phpunit (media eksik) · Download/Audio test altyapıları PLANNED |

### §4.10 Dağıtım Yol Haritası (hedef → durum — H10 ayrı)

| # | Adım (hedef) | Durum | Kanıt/eksik |
|---|---|---|---|
| 1 | CI kapıları (lint/test/audit/secret) | IMPLEMENTED | ci.yml · secret-scan.yml (ls) |
| 2 | `.gitleaks.toml` config | EKSİK ⚠️ | secret-scan.yml header notu |
| 3 | Coverage gate'i sertleştirme (A7 soft → hard) | KARAR GEREKİR 👤 | Soft Constraint #1 · ci.yml "soft, A7" |
| 4 | Artefakt üretimi/imzalama | PLANNED ⚠️ | anayasa §A.1 · workflow'da yok |
| 5 | Docker image build | PLANNED ⚠️ | §12 (Docker 24+) · Dockerfile YOK |
| 6 | K8s manifest/orkestrasyon | PLANNED ⚠️ | §5 K13 · manifest YOK |
| 7 | Deploy workflow + rollback | PLANNED ⚠️ | §A.1 Rollback · deploy workflow YOK |
| 8 | Branch koruma/review kuralı | TEYİTSİZ ⚠️ | GitHub settings grep'i yapılmadı |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K012 (OS → OBSERVABILITY) | aşağı | anayasa §A.1 K013 "izinli=K000-K012" | `00-kspace-anayasa.md §A.1 K013` |
| GitHub Actions | dış platform | CI motoru (hedef §5 K13) | `.github/workflows/` (ls) |
| Composer · npm | dış araç | bağımlılık kurulumu/build | `composer.lock` · `package-lock.json` (ls) |
| PHPStan · Vitest · Playwright · GitLeaks | dış araç | analiz/test/tarama | ci.yml/secret-scan.yml (ls) |
| port/adapter | yan | EK A istisnası (R6.3) | anayasa §A.1 |
| Docker · K8s | dış platform (hedef) | konteyner/orkestrasyon | §5 K13 · repo: YOK → PLANNED |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K013 → katmanlara runtime geri çağrısı (H20) | build katmanı runtime'a senkron geri dönmez | `rules.md R6.1` · ADR-096 §2 |
| K012 alert'ini deploy komutuna çevirmek (senkron) | alert olaydır (R6.2); deploy ayrı onay (R10/H6) | `rules.md R6.2/R10` |
| Plaintext secret / `.env` commit'i | §21 hardcoded secrets yasak · §23 #3 | `.ai/CLAUDE.md §21/§23` |
| Onaysız commit | H6 — commit = ayrı açık insan onayı | `rules.md R10` · F1 H6 |
| Frozen ADR düzenleme | R17 mutlak yasak | `rules.md R17` |
| H19 doğrudan veri paylaşımı | veri sınırı | `rules.md R6.1` |
| Vault'a onaysız yazma (bu üretim dahil) | H5 yalnız `vault-utf8-writer.mjs` + onay; bu dosya staging | ADR-096 §1.4 |

### §5.3 Boundary Matrisi

| Boundary | K013 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | build-time veri (kod/lock/CI log/artefakt); runtime verisi/DB YOK | K005/K008 |
| SECURITY_BOUNDARY | GitLeaks + composer-audit + minimal token + GitHub Secrets; deploy onayı 👤 | K006 (politika) · kullanıcı (onay) |
| FAILURE_MODE | job fail → teslimat durar (fail-safe) · retry (concurrency) · rollback PLANNED | K012 (gözlem) |
| RUNTIME boundary | runner süreçleri (PHP/Node/Chromium) — uygulama runtime'ı DEĞİL | K000 |
| CONTRACT boundary | workflow tanımları (kod olarak, sürüm kontrollü) | K013 |
| Olay yukarı serbest | build sonucu → K012/yönetim; geri senkron komut yasak | K012 |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı (R6.2)

```text
K013 CI/CD «PUSULA»
   │  push/PR (aşağı tetik)
   ▼
kaynak ağacı → build/test/tarama → pass/fail (gate)
   │                                    │
   │ (artefakt/deploy — PLANNED ⚠️)     │ (sonuç sinyali, yukarı)
   ▼                                    ▼
runtime katmanları (K000-K012)     K012 OBSERVABILITY + 👤 raporu
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| push/PR → workflow | tetik (dış) | `on: push/pull_request` | ci.yml (ls) |
| workflow → test araçları | aşağı | PHPStan/PHPUnit/Vitest/Playwright | ci.yml (ls) |
| sonucu → K012 | yukarı sinyal | R6.2 | K012 §4.9 |
| sonucu → 👤 | onay kapısı | Kapı 10 / R10 | `rules.md R10` |
| geri runtime'a senkron komut | — | YASAK (H20) | `rules.md R6.1` |

### §5.5 Kardeş İlişki Kuralı (R6.4)

Kardeş KNNN'lerle ilişki yalnız `refers-to`; `depends-on` yalnız alt katman yönünde (K013 tüm
band-1 kardeşlerinin test/tedarik kapısıdır — ama onlara runtime bağımlı DEĞİLDİR). Yatay
`depends-on` → `dep-check` exit 1 (R6.6).

### §5.6 Branch / Commit / Onay Politikası (H6 · R10 — bağlayıcı)

| Kural | Değer | Kaynak |
|---|---|---|
| Commit onayı | `git commit` = 👤 ayrı açık onayı (plan onayı commit değildir) | `rules.md R10` (H6) |
| Durum geçişi | PROPOSED → ACTIVE = 👤 + kanıt | `rules.md R10` |
| Deployment onayı | deploy workflow henüz yok; üretilince 👤 kapısı | `rules.md R10` · §4.10 |
| Secret | yalnız GitHub Secrets / `.env`; kodda/log'da yasak | `.ai/CLAUDE.md §21/§23 #3` |
| ADR değişikliği | Frozen ADR düzenlenemez; yeni karar = yeni ADR (H08) | `rules.md R17` |
| Workflow değişikliği | `.github/workflows/**` düzenlenmesi MODIFY kapsamı → 👤 | `rules.md R15` · Guardrail #1 (plan) |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı | Not |
|---|---|---|---|
| Tetik | push/PR | workflow kuyruğu | concurrency: cancel-in-progress |
| Kurulum | lock dosyaları | bağımlılık ağacı | composer ×3 + npm ci |
| Statik analiz | kaynak | PHPStan raporu (L5) | fail → job kırmızı |
| Unit test | kaynak + testler | PHPUnit/Vitest sonuçları | ≥80% hedef (§17) |
| E2E | Playwright spec | tarayıcı test sonucu | soft gate A6 |
| Secret tarama | git geçmişi | GitLeaks sonucu | config eksik ⚠️ |
| Artefakt/deploy (hedef) | build çıktığı | imzalı artefakt / sürüm | PLANNED ⚠️ |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| CI platformu | GitHub Actions · ubuntu-latest | ci.yml (ls) |
| PHP | 8.4 (`env.PHP_VERSION`) + apcu/mbstring/pdo_mysql eklentileri | ci.yml (ls) |
| Node | npm ci · Vitest · Playwright chromium | ci.yml (ls) · package.json |
| Composer | cache (`actions/cache`) + install ×3 | ci.yml (ls) |
| Token | `permissions: contents: read` | ci.yml + secret-scan.yml (ls) |
| Docker/K8s | hedef (§12: Docker 24+) · repo YOK | §12 · ls |
| Runner timeout | lint 15dk · gitleaks 10dk | ci.yml/secret-scan.yml (ls) |

### §6.3 Observability (CI gözlemlenebilirliği)

| Sinyal | Kaynak | Durum |
|---|---|---|
| Job sonucu/süresi | GitHub Actions UI/log | IMPLEMENTED |
| Coverage raporu | Vitest coverage (soft A7) | IMPLEMENTED (soft) · eşik yorumu §4.6 |
| Test raporları | PHPUnit/Playwright çıktıları | IMPLEMENTED (çıktı formatı ⚠️) |
| Secret-scan sonucu | GitLeaks job | IMPLEMENTED · `.gitleaks.toml` eksik |
| Artefakt imzalama/provenance | hedef | PLANNED ⚠️ |
| K012'ye build sinyali | GitHub → izleme aktarımı | PLANNED ⚠️ (entegrasyon yok) |

### §6.4 Test (K013 = test çalıştırıcısı · §17 hedefleri)

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| Backend PHP | PHPUnit 11 | phpunit.xml ×4 + ci.yml `php-test` (ls) | ≥80% / ≥90% (§17) |
| Frontend JS | Vitest | `vitest.config.js` + `test:js` + ci.yml `js-test` (ls) | ≥80% / ≥90% (§17) |
| Download | Vitest | hedef (§17) · servis yok → PLANNED | ≥80% |
| Audio (C++) | Google Test | hedef (§17) · C++ yok → PLANNED | ≥80% |
| E2E | Playwright | `playwright.config.ts` + ci.yml `js-e2e` (ls) | Kapı 11 |
| Statik analiz | PHPStan L5 | `composer.json scripts.stan` + ci.yml (ls) | 0 hata beklenir |

### §6.5 Failure Mode Senaryoları

| # | Senaryo | K013 davranışı | Kanıt |
|---|---|---|---|
| 1 | Test başarısız | job kırmızı → PR/deploy durur (fail-safe) | ci.yml tasarım · §4.3 |
| 2 | PHPStan L5 hatası | `php-lint` job başarısız | ci.yml |
| 3 | Secret tespiti | gitleaks job fail → inceleme | secret-scan.yml |
| 4 | Aynı ref'e çoklu push | `cancel-in-progress` eski işi iptal eder | ci.yml (ls) |
| 5 | `.gitleaks.toml` yok | varsayılan config — kapsam daralabilir | secret-scan.yml header notu |
| 6 | Deploy hattı yok | rollback imkânsız (PLANNED — hedef eksik) | §4.1 Artifacts/Rollback |
| 7 | Onaysız commit | H6 ihlali → revert (süreç kuralı) | `rules.md R10` |
| 8 | Runner kaybı/kuyruk | build gecikmesi; fail-safe korunur | GitHub Actions davranışı (dış) ⚠️ |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K013 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §5/§12/§17 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1 |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| R11.3 (validate --check) | bu üretim KAPSAM DIŞI — staging dosyaları vault'a yazılmadı (görev yasağı) |
| H6 (onaysız commit yasak) | bu üretimde commit YOK |
| Soft Constraint #1 (coverage esneklik) | ci.yml soft gate A7 ile uyumlu |

### §6.6.1 Uyum Matrisi — Band-1 Kardeş ↔ Test Kapısı Eşlemesi

| Kardeş | Kapsanan sinyal | Test kapısı (ci.yml) | Kanıt (2026-10-08) |
|---|---|---|---|
| K007-middleware | middleware hattı davranışları | `php-test` | `shared/tests/Middleware/` (9 test dosyası) |
| K008-servisler | servis/seans/log birimleri | `php-test` | `shared/src/{Session,Log}` + repository testleri |
| K009-api | routing/BFF/version | `php-test` | `shared/src/Api/{Routing,Bff,Versioning}` + testleri |
| K010-uygulama | auth/home panel akışları | `php-test` + `js-test` | `auth/` · `home/` panel + vitest |
| K011-ux | CSS/JS bileşen + a11y | `js-test` + `js-coverage` (soft) + `js-e2e` (soft) | `assets.coremusic.net` js · axe e2e |
| K012-izleme | Log yazıcıları | `php-test` | `shared/src/Log/` + `coremusic_logs` şeması |

> Eşleme kuralı: her kardeş katman `ACTIVE` (R16.2) olmak için bu kapıdan en az bir zorunlu job ile sinyal vermek zorundadır; `SOFT` job'lar tek başına yeterli değildir.

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | 7 EK A kalemi + §5 K13 kalemleri | her kalem Durum+Kanıt | ✓ (§4.1/§4.2) · 3 PLANNED işaretli |
| K4 | CI kapsamı | 6 job + secret scan belgeli | ✓ (§4.3/§4.4) |
| K5 | Test alt yapısı | 4 phpunit + vitest + playwright + script'ler | ✓ (§4.5) |
| K6 | Web research (R9 3'lü) | ≥%80 kaynaklı | ✗ → ⚠️ (§7.1) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K013 - CI/CD «PUSULA»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K13` · `§12` · `§17` · `§8` (soft constraints) | dosya yolu (vault read) | K-matrix + hedefler |
| 3 | `.github/workflows/ci.yml` (203 satır) · `secret-scan.yml` (31 satır) · `package.json` · `shared/composer.json` · `phpunit.xml` ×4 · `vitest.config.js` · `playwright.config.ts` (ls 2026-10-08) | repo ls/grep | IMPLEMENTED durumları |
| 4 | `.ai/.decisions/accepted/` ls: ADR-023/082/096 (+ ADR-014 — migration workflow sınırı) | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9/R10/R11` · `ADR-096 §2` | dosya yolu | format + yön + kapılar |
| 6 | F1 EK B (37 URL) | URL (vault arşivi) | research üssü |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | Docker/K8s/Artefakt/Rollback/Deploy yok (hedef) | §4.1/§4.2 | hedef→plan: Dockerfile + deploy workflow üretimi (👤 onaylı) |
| G2 | `.gitleaks.toml` yok (varsayılan config) | §4.4 | config üretimi (şablon §3.2) |
| G3 | Eski rejim stale header referansları (`.ai/architecture/k13-cicd/*`) | §4.3/§4.4 borç satırları | workflow header düzeltmesi (dosya düzenlenmesi = MODIFY onayı) |
| G4 | `media.coremusic.net/phpunit.xml` gözlenmedi | §4.5 #9 | medya panel test altyapısı |
| G5 | Branch koruma/review kuralı kanıtı yok | §4.6 | GitHub repo settings teyidi |
| G6 | Artefakt imzalama/provenance | §6.3 | hedef tasarım (PLANNED) |
| G7 | Web kanıtı (URL+tarih) | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G8 | Coverage eşiklerinin ci.yml'de kilitli olup olmadığı (soft A7) | §6.4 | workflow coverage gate incelemesi |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

### §7.2 Kanıt Haritası (EK C alan → birincil kanıt)

| EK C alan | Birincil kanıt | İkincil kanıt | Üçüncül |
|---|---|---|---|
| SORUMLULUK | anayasa §A.1 K013 | `.ai/CLAUDE.md §5 K13` | repo §4.1-§4.5 |
| RUNTIME | ci.yml/secret-scan.yml (ls) | package/composer lock (ls) | ⚠️ (Docker/K8s) |
| GIRDI/CIKTI | ci.yml on:/jobs | script'ler (ls) | ⚠️ (artefakt) |
| IZINLI/YASAK | anayasa §A.1 + `rules.md R6/R10` | ADR-096 §2 | ⚠️ |
| DATA_BOUNDARY | §21 secret yasağı · lock dosyaları | repo grep (.env commit) ⚠️ | ⚠️ |
| SECURITY_BOUNDARY | secret-scan.yml · composer-audit job | minimal permissions (ls) | ⚠️ |
| FAILURE_MODE | ci.yml gate davranışı | §6.5 senaryoları | ⚠️ (rollback) |
| OBSERVABILITY | GitHub job logları (dış platform) | soft gates A6/A7 | ⚠️ (K012 entegrasyonu) |
| TEST | §4.5 envanter (ls) | §17 hedefleri | ⚠️ |

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» (üretilen band-1 seti — K007..K012 kardeşleri).
Üst/yasak yön (H20): K014 NETWORK «ÇARK» · K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» ·
K017 POWER «KANTAR» · K018 THERMAL «MEZİT» · K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL».
Kardeşler arası ilişki yalnız `refers-to` (R6.4).

**Kapı notu:** K013, band-1 setinin **test/tedarik kapısıdır** — K007-K012 kartlarındaki tüm
`≥80%` hedefleri (§17) bu katmanın pipeline'ında ölçülür. KAPI 9 (hallucination) ve KAPI 10 (👤)
süreç kapıları ise bu dosyanın kapsamı değil, R11'dir.

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):** b1-K007-middleware.md (K007) ·
b1-K008-servisler.md (K008) · b1-K009-api.md (K009) · b1-K010-uygulama.md (K010) ·
b1-K011-ux.md (K011) · b1-K012-izleme.md (K012) · b1-K013-cicd.md (K013 — bu dosya).

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.

**Not (Kapı 10):** Bu dosya `draft`'tır; band-1 seti (K007-K013) birlikte 👤 onayına gider. §7.1
boşlukları research kapısında kapatılmalıdır (R4.4c · R16.2 · R11 KAPI 9). Vault yazımı ve
`git commit` bu üretim kapsamında DEĞİLDİR (H5/H6).

**Kaynak bağımlılığı:** `depends-on: .ai/architecture/00-kspace-anayasa.md` — anayasa güncellenirse
bu kart §A.1 K013 satırıyla birlikte yeniden gözden geçirilir (R8.1 zinciri).