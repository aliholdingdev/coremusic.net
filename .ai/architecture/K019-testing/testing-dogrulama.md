---
title: "K019-testing — Doğrulama"
node_id: K019-testing
node_type: domain-validation
status: draft
updated: 2026-10-12
version: 1.0.0
group: kanit
---


# K019-testing — testing-dogrulama.md

## §1 Amaç

K019-testing iddialarını **dosya okuma + glob + grep** ölçümüyle doğrular (2026-10-12).
Kanıt yoksa UNKNOWN / ⚠️ VERIFICATION REQUIRED — uydurma yok.

**Kanıt sınıfları:** **S1** = dosya okuma (satır kanıtı) · **S2** = glob/grep ölçümü ·
**S3** = vault beyanı (bağımsız ölçüm yok) · **S4** = URL'siz/kaynaksız beyan → kullanılamaz.

## §2 Disk İddiaları → Durum

| # | İddia | Kanıt (gerçek yol / satır) | Sınıf | Tarih | Durum |
|---|---|---|---|---|---|
| 1 | shared PHP unit testleri var | shared/tests/**/*Test.php = 40 dosya (Unit 15 · Api 8 · Middleware 9 · Repository 2 · Events 2 · Security 2 · OAuth 1 · Component 1) | S2 | 2026-10-12 | ✅ verified |
| 2 | Api testleri = 8 | shared/tests/Api/ = 8 *Test.php (ApiResponse · RouteTable · Versioning · GatewayMethodRouting · RequestValidation · ResponseNormalization · ApiPipelineSecurity · Dto) | S2 | 2026-10-12 | ✅ verified |
| 3 | Middleware/Security testleri var | tests/Middleware = 9 · shared/tests/Security = 2 · Unit/Security = 2 — glob | S2 | 2026-10-12 | ✅ verified |
| 4 | Alt alan testleri var | auth 6 · api 3 · **home 6** = 15 *Test.php (`*.coremusic.net/tests/**`) | S2 | 2026-10-12 | ✅ verified (⚠️ bkz §3-ç1) |
| 5 | JS spec = 10 dosya | assets.coremusic.net = 9 .spec.js + 1 .spec.ts (tests/e2e/player-info.spec.ts · tests/components ×2 · js/core ×1 · js/components/primitives ×6) | S2 | 2026-10-12 | ✅ verified |
| 6 | phpstan ^1.10 var | shared/composer.json L25 + script `stan --level=5` L48; **auth/api/home composer.json'larında YOK** | S1 | 2026-10-12 | ✅ verified (kısmi — bkz §3-ç2) |
| 7 | phpunit ^10.5 / phpunit.xml | phpunit ^10.5 ×4 composer.json (grep); phpunit.xml ×4 (shared · auth · api · home); media.coremusic.net'te YOK | S1/S2 | 2026-10-12 | ✅ verified |
| 8 | Playwright E2E izi var | playwright-report/index.html (var) + test-results/.last-run.json (var) — glob | S2 | 2026-10-12 | 🔶 partial (iz var) |
| 8a | E2E spec dosyası var | assets.coremusic.net/tests/e2e/player-info.spec.ts — okundu: `import { test, expect } from '@playwright/test'` + axe-playwright (GERÇEK Playwright spec) | S1 | 2026-10-12 | ✅ verified (1 spec — kapsam dar) |
| 8b | E2E config var | assets.coremusic.net/playwright.config.ts — okundu: testDir ./tests/e2e · baseURL home.coremusic.net:81 · webServer php -S; package.json `test:e2e` bu config'i çağırır | S1 | 2026-10-12 | ✅ verified |
| 9 | **Kök** e2e/ altında 0 spec | `e2e/**/*` glob = 0 isabet (master §3 '0 spec' beyanı yalnız bu kök dizin için geçerli — assets altında 1 spec var, bkz 8a) | S2 | 2026-10-12 | ❌ verified NEGATIF (yalnız kök e2e/ için) |
| 10 | CI test kapıları dosyası var | ci.yml: php-lint L25 · php-test L66 (--testsuite Unit) · composer-audit L110 · js-test L142 · js-coverage L167 (soft) · js-e2e L178/L183 (soft) | S1 | 2026-10-12 | ✅ verified (dosya; geçiş kanıtsız) |
| 11 | vitest ^5.0.3 + @playwright/test ^1.63.0 | root package.json L16 · L11 | S1 | 2026-10-12 | ✅ verified |
| 12 | Secret tarama = security testi komşusu | .gitleaks.toml (kök, glob) + .github/workflows/secret-scan.yml (K018 kanıtı) | S2/S1 | 2026-10-12 | ✅ verified |
| 13 | Coverage hedefi ≥80% | .ai/AGENTS.md §16 "Test coverage ≥80%, flaky test %0" | S3 | 2026-10-12 | 🔶 VAULT BEYANI (ci.yml'da soft gate — §5-1) |
| 14 | CI GERÇEKTEN geçti | GitHub run kanıtı diskte YOK; vault §25.2 "geçtiği doğrulanmadı" | S4 | 2026-10-12 | ⚠️ verify-required |
| 15 | regression suite var | `**/{regression,...}/**` glob = 0 isabet | S2 | 2026-10-12 | ❌ verified NEGATIF (ayrı suite yok) |
| 16 | performance testi var | aynı glob = 0 isabet (k6/locust/jmeter config'i de YOK) | S2 | 2026-10-12 | ❌ verified NEGATIF |
| 17 | audio-quality ölçümü var | SNR/THD/latency ölçüm dosyası YOK (glob 0 isabet) | S2 | 2026-10-12 | ❌ verified NEGATIF |
| 18 | hardware-validation var | fiziksel doğrulama kanıtı YOK (glob 0 isabet) | S2 | 2026-10-12 | ❌ verified NEGATIF |

## §3 Master–Disk Çelişkileri (master bu turda DEĞİŞTİRİLEMEZ — burada tescil)

| # | Çelişki | Master beyanı | Disk ölçümü (2026-10-12) | Sınıf |
|---|---|---|---|---|
| ç1 | PHP test toplamı | master §4.1: "Toplam PHP 49 (shared 40 + auth 6 + api 3)" | home.coremusic.net/tests = **6** *Test.php daha → gerçek toplam **55** | S2 |
| ç2 | phpstan kapsamı | master §4: "yalnız shared" | doğrulandı (grep 2/4 composer.json) — çelişki yok, **kısmi** durumu tescilli | S2 |
| ç3 | E2E "0 spec" | master §2.1/§3: "e2e/ altında 0 spec" · açık soru 2 "E2E spec dosyaları nerede?" | kök e2e/ = 0 doğrulandı; **ancak** assets.coremusic.net/tests/e2e/player-info.spec.ts = 1 GERÇEK Playwright spec (@playwright/test import) + playwright.config.ts (testDir ./tests/e2e) → master'ın "spec YOK" izlenimi eksik ölçüdür; açık soru 2 bu turda büyük ölçüde kapandı | S1/S2 |

## §4 Web-Kanıt Kayıtları

> **Bu turda web araştırması YAPILMADI** (A4 payı disk-öncelikli kanıt planı). Tablo iskelettir;
> URL'siz hiçbir dış iddia bu domainde S1–S2 sınıfına geçmez (S4 = kullanılamaz).

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| — | (boş — kanıt bekliyor) | — | — | ⏳ |

**Bekleyen web-iddiaları (sonraki tur):**

1. PHPUnit 10 vs 11 farkları ve `--testsuite` davranışı (ci.yml php-test job doğruluğu için).
2. PHPStan 1.10 → 2.x geçiş durumu (build-verification/ kararını etkiler).
3. Playwright 1.63 güncel sürümü + soft-gate best practice (end-to-end/ revizyonu için).

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | Coverage gate (ci.yml L167 continue-on-error) hard olacak mı? ≥80% bağlayıcı mı? | build-verification/ |
| 2 | E2E spec'leri nerede (report izi var, kök e2e/ boş)? | end-to-end/ |
| 3 | Regression kapsamı mevcut unit/integration mı, ayrı suite mi? | regression/ |
| 4 | Performance/audio-quality/hardware hedef metrikleri ve rig'i tanımlı mı? | performance/ · audio-quality/ · hardware-validation/ |
| 5 | home.coremusic.net/tests (6 dosya) master envanterine eklenecek mi? | unit/ · master §4.1 |
| 6 | Son CI run'ı geçti mi? (arayüz kanıtı gerekli) | tüm domain |

## §6 Kapanış Kuralı

Domain `verified` olmak için: §2'deki ⚠️ satırı (S4 iddia) kapanmalı · §2 ❌ negatif satırlar
(regression/performance/audio/hardware) ya kanıtla ya **ADR ile kapsam dışı** tescillenmeli ·
§3-ç1 sayım çelişkisi master revizyonuyla (sonraki tur) düzeltilmeli.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 · Mode: Truth Mode
