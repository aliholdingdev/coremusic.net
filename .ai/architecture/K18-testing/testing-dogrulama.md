---
title: "K18-testing — Doğrulama"
node_id: K18-testing
node_type: domain-validation
status: draft
updated: 2026-10-17
version: 1.1.0
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

## §3 Master–Disk Çelişkileri (2026-10-12 tescil · master 2026-10-17 turunda DÜZELTİLDİ)

| # | Çelişki | Master beyanı | Disk ölçümü (2026-10-12) | Sınıf | Durum (2026-10-17) |
|---|---|---|---|---|---|
| ç1 | PHP test toplamı | master §4.1: "Toplam PHP 49 (shared 40 + auth 6 + api 3)" | home.coremusic.net/tests = **6** *Test.php daha → gerçek toplam **55** | S2 | ✅ DÜZELTİLDİ (master §4.1 home satırı eklendi, toplam 55; §5 used_by 55) |
| ç2 | phpstan kapsamı | master §4: "yalnız shared" | doğrulandı (grep 2/4 composer.json) — çelişki yok, **kısmi** durumu tescilli | S2 | ✅ tescilli (düzeltme gerekmedi) |
| ç3 | E2E "0 spec" | master §2.1/§3: "e2e/ altında 0 spec" · açık soru 2 "E2E spec dosyaları nerede?" | kök e2e/ = 0 doğrulandı; **ancak** assets.coremusic.net/tests/e2e/player-info.spec.ts = 1 GERÇEK Playwright spec (@playwright/test import) + playwright.config.ts (testDir ./tests/e2e) → master'ın "spec YOK" izlenimi eksik ölçüdür | S1/S2 | ✅ KAPANDI (master §2.1 pyramid + §3 + §7-2 güncellendi; açık kalan: kapsam dar — spec ailesi kararı) |

### §3.5 2026-10-17 Turu Düzeltmeleri (bu turda yapılan yapısal/veri değişiklikleri)

| # | Değişiklik | Gerekçe (disk kanıtı) | Onay |
|---|---|---|---|
| d1 | `end-to-end/index.md` → `e2e/index.md` taşındı; `end-to-end/` kapatıldı; children 10 → 11 (e2e + flaky) | Modül dokümanı `node_id: K019-testing/e2e` + 5 kardeş + 1 dış alan dosyası (K000-isletim-sistemi) bu node_id'ye referans veriyor; `end-to-end` yalnız K18 içinde geçiyordu (grep 2026-10-17) | kullanıcı onayı 2026-10-17 |
| d2 | `flaky/index.md` üretildi (Kademe-2 kaydı — 22. dosya) | flaky/ çocuk kaydı ve index'i yoktu; modül dokümanı 603 satır + 3 dosya referansı var | kullanıcı onayı 2026-10-17 |
| d3 | contract + flaky modül dokümanı `depends_on`'a parent satırı eklendi (K019-testing/contract · K019-testing/flaky) + `group: mimari → kod` | Sibling modül dokümanlarının (unit/integration/e2e/performance/regression) depends_on'u 2 satır; contract+flaky 1 satırdı. group tutarlılığı: 5 modül dokümanı `kod`, contract+flaky `mimari` idi → hepsi `kod`, subdomain index'leri `mimari` | — |
| d4 | 7 modül dokümanına **çok-dilli karşılıklar** bölümü eklendi: unit §9.6 · integration §9.5 · contract §10.6 · e2e §9.6 · flaky §11.6 · performance §9.5 · regression §10.6 | 12 dil geniş set (PHP · TS · Python · C# · Java · Go · C++ · Rust · Ruby · Kotlin · Swift · C) — her bölüm dil→çalıştırıcı→komut tablosu + modül temasına göre 12 kısa örnek | kullanıcı isteği 2026-10-17 |

## §4 Web-Kanıt Kayıtları (2026-10-17 turunda DOLDURULDU)

> Web araştırması 2026-10-17'de yapıldı. URL'siz hiçbir dış iddia bu domainde S1–S2 sınıfına geçmez (S4 = kullanılamaz).

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | PHPUnit `--testsuite` XML'de tanımlı suite'leri çalıştırır (ci.yml php-test job'unun temeli doğru) | https://docs.phpunit.de/en/12.5/textui.html ("When you have multiple test suites defined in your XML configuration file, you can use the --testsuite option") | 2026-10-17 | ✅ verified (sayfa okundu) |
| 2 | PHPUnit 11+: bir test sınıfı birden fazla suite'e ait OLAMAZ (eski davranış kırıldı) | https://core.trac.wordpress.org/ticket/62004 ("PHPUnit 11 change: A test can no longer be part of multiple test suites that are configured in the XML configuration file") | 2026-10-17 | ✅ verified (ikincil kaynak — resmi migration sayfası 404 verdi: docs.phpunit.de/en/11.5/migration11.html → ⚠️ resmi URL sonraki turda) |
| 3 | PHPStan 2.0 yayınlandı (2024-11-11), seviye 10 dahil; repo'daki ^1.10 iki major geride | https://phpstan.org/blog/phpstan-2-0-released-level-10-elephpants ("November 11, 2024 · PHPStan 2.0 Released With Level 10 and Elephpants!") · https://phpstan.org/blog/upgrading-from-phpstan-1-to-2 | 2026-10-17 | ✅ verified (sayfa okundu) |
| 4 | Playwright 1.63 güncel kararlı sürüm (Eylül 2026 — test locks, Chrome for Testing indirmesi); repo ^1.63.0 güncel | https://playwright.dev/docs/release-notes ("Version 1.63 — Test locks ... Playwright now downloads the Chrome for Testing") | 2026-10-17 | ✅ verified (sayfa okundu) |
| 5 | Soft-gate (continue-on-error) bir güvenlik/kalite kapısı DEĞİLDİR; production kapıları required check olmalı | ⏳ bekliyor — GitHub Actions resmi docs'u bu turda okunmadı | — | ⏳ pending (S4 — kullanılamaz, iddia şimdilik yalnız repo gözlemi: ci.yml L167/L183 continue-on-error) |

**Kapanan bekleyen iddialar:** PHPUnit --testsuite (1·2) · PHPStan 2.x (3) · Playwright sürümü (4).
**Kalan:** soft-gate best practice (5) — sonraki tur.

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | Coverage gate (ci.yml L167 continue-on-error) hard olacak mı? ≥80% bağlayıcı mı? | build-verification/ |
| 2 | ~~E2E spec'leri nerede?~~ KAPANDI (2026-10-17): assets.coremusic.net/tests/e2e/player-info.spec.ts — açık kalan: kapsam dar, spec ailesi büyüyecek mi? | e2e/ |
| 3 | Regression kapsamı mevcut unit/integration mı, ayrı suite mi? | regression/ |
| 4 | Performance/audio-quality/hardware hedef metrikleri ve rig'i tanımlı mı? | performance/ · audio-quality/ · hardware-validation/ |
| 5 | ~~home.coremusic.net/tests (6 dosya) master envanterine eklenecek mi?~~ KAPANDI (2026-10-17): eklendi, toplam 55 | unit/ · master §4.1 |
| 6 | Son CI run'ı geçti mi? (arayüz kanıtı gerekli) | tüm domain |
| 7 | flaky karantina mekanizması ne zaman kurulacak (repo'da YOK)? | flaky/ |

## §6 Kapanış Kuralı

Domain `verified` olmak için: §2'deki ⚠️ satırı (S4 iddia) kapanmalı · §2 ❌ negatif satırlar
(regression/performance/audio/hardware) ya kanıtla ya **ADR ile kapsam dışı** tescillenmeli ·
§3-ç1 sayım çelişkisi master revizyonuyla (sonraki tur) düzeltilmeli.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 (ilk) → 2026-10-17 (rev. 1.1.0: §3.5 + §4 dolduruldu) · Mode: Truth Mode
