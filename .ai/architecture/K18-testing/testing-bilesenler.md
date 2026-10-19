---
node_id: K18-testing-bilesenler
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops, K009-security, K007-data, K015-AI]
evidence:
  - ".ai/architecture/K18-testing/testing-master.md §3 (11 çocuk tablosu — 4 kanıtlı · 2 kısmi · 5 kanıt bekliyor — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md §4-§4.2 (toolchain envanteri + dizin sayımı + CI job eşlemesi — 2026-10-12 ölçümü)"
  - ".ai/architecture/K18-testing/testing-dogrulama.md §2 (S1-S4 kanıt sınıfları — 2026-10-18)"
validation: "Her bileşen ya disk kanıtı (S1 satır / S2 glob-grep) ya da ⚠️ kanıt bekliyor etiketiyle listelenir; sayılar 2026-10-12 sayımına bağlıdır."
open_questions:
  - "⚠️ flaky/ karantina mekanizması diskte YOK (retry kanıtı var: playwright.config.ts retry:2)"
  - "⚠️ regression/ · performance/ · audio-quality/ · hardware-validation/ bileşenlerinin disk karşılığı 0 isabet (glob 2026-10-12)"
  - "⚠️ phpstan ^1.10 yalnız shared'de — auth/api/home static analysis dışında"
group: kod
---

# Testing — Bileşenler

> K18-testing'in 11 kademe-2 çocuğu, test toolchain'i ve CI kapısı eşlemesini
> bileşen bileşen listeler. Kanıt: `testing-master.md` §3/§4/§4.2 (2026-10-12
> sayımı; master rev. 1.1.0 = 2026-10-17).

## §1. ELI15 — Bileşenler ne parçalar?

Bir test sistemi üç şeyden oluşur: **(1) testleri yazdığımız yer** (11 çocuk
dizini), **(2) testleri koşturan araçlar** (PHPUnit, Vitest, Playwright,
PHPStan), **(3) bunları kapıya bağlayan CI işleri** (ci.yml job'ları). Bu
dosya üçünü de "kaç dosya var, neredeler, gerçekten çalışıyor mu" sorusuyla
listeler. Kanıt yoksa o bileşen **"var" sayılmaz** — sadece "tasarlanmış"
sayılır.

## §2. Kademe-2 Çocuklar (11 — `testing-master.md` §3)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu (disk) |
|---|---|---|---|
| 1 | `unit/` | Tek bir birimin (sınıf/metot) izole davranışı | ✅ kanıtlı — 55 PHP dosyasının büyük kısmı (shared/tests/Unit 15 · tests/Component · tests/OAuth · tests/Security birim altı + auth 6 + api 3 + home 6) |
| 2 | `integration/` | Birimler arası / DB / middleware birlikte davranışı | ✅ kanıtlı — tests/Api 8 · tests/Middleware 9 · tests/Repository 2 · tests/Events 2 |
| 3 | `contract/` | API sözleşmesi (request/response şekli) uyumu | ✅ kanıtlı — ApiResponseTest · RouteTableTest · VersioningTest (shared/tests/Api) |
| 4 | `e2e/` | Tarayıcı akışı (Playwright) | 🔶 kısmi — 1 spec (`assets.coremusic.net/tests/e2e/player-info.spec.ts`) + `playwright.config.ts` + report izi (513 satır modül dokümanı) |
| 5 | `flaky/` | Kararsız test tespiti, retry, karantina | ⚠️ kısmi — retry kanıtı var (`playwright.config.ts` retry:2), **karantina/flaky ölçümü YOK** (603 satır modül dokümanı) |
| 6 | `regression/` | Daha önce çalışanın bozulmadığını doğrulama | ⚠️ kanıt bekliyor — ayrı regression suite glob = 0 isabet (`testing-dogrulama.md` §2 satır 15) |
| 7 | `security/` | Güvenlik testi (auth bypass, injection, middleware) | ✅ kanıtlı — shared/tests/Security 2 · Unit/Security 2 · Middleware 9 · ApiPipelineSecurityTest |
| 8 | `performance/` | Yük/gecikme/bellek ölçümü | ⚠️ kanıt bekliyor — k6/locust/jmeter config'i de YOK (glob 0) |
| 9 | `audio-quality/` | Ses kalitesi ölçümü (SNR/THD/latency) | ⚠️ kanıt bekliyor — ölçüm dosyası YOK; hedef `.ai/AGENTS.md` §16 vault beyanı |
| 10 | `hardware-validation/` | Donanım üzerinde doğrulama (DAC/ADC/ASIO) | ⚠️ kanıt bekliyor — fiziksel test kanıtı YOK |
| 11 | `build-verification/` | Derleme + test bütünlüğü (lint/type/coverage) | ✅ kanıtlı — ci.yml php-lint · phpstan ^1.10 · js-coverage |

## §3. Test Dosyası Envanteri (dizin bazlı — 2026-10-12 sayımı)

| Test dizini | Dosya uzantısı | Sayı | Not |
|---|---|---|---|
| `shared/tests/Api` | `*Test.php` | 8 | ApiResponse · RouteTable · Versioning · GatewayMethodRouting · RequestValidationMiddleware · ResponseNormalizationMiddleware · ApiPipelineSecurity · Dto |
| `shared/tests/Component` | `*Test.php` | (40 içinde) | birim/bileşen |
| `shared/tests/Events` | `*Test.php` | (40 içinde) | event |
| `shared/tests/Fixtures` | (fixture) | — | test veri kütüphanesi |
| `shared/tests/Middleware` | `*Test.php` | (40 içinde) | middleware entegrasyonu |
| `shared/tests/OAuth` | `*Test.php` | (40 içinde) | OAuth akışı |
| `shared/tests/Repository` | `*Test.php` | (40 içinde) | DB repository |
| `shared/tests/Security` | `*Test.php` | (40 içinde) | güvenlik |
| `shared/tests/Unit` | `*Test.php` | 15 | saf birim |
| `auth.coremusic.net/tests` | `*Test.php` | 6 | auth akış testi |
| `api.coremusic.net/tests` | `*Test.php` | 3 | api testi |
| `home.coremusic.net/tests` | `*Test.php` | 6 | home akış testi (2026-10-17 eklendi — `testing-dogrulama.md` §3-ç1) |
| `assets.coremusic.net` | `.spec.js` / `.spec.ts` | 10 | 9 .spec.js (tests/components ×2 · js/core ×1 · js/components/primitives ×6) + 1 .spec.ts (tests/e2e/player-info.spec.ts) |
| **Toplam PHP** | `*Test.php` | **55** | shared 40 + auth 6 + home 6 + api 3 |
| **Toplam JS** | `.spec.*` | **10** | assets 9 + 1 |

## §4. Toolchain (araç envanteri — `testing-master.md` §4)

| Ekosistem | Kanıt (gerçek dosya yolu) | Durum |
|---|---|---|
| PHP test runner | phpunit **^10.5** — 4 composer.json: shared · auth · api · home | ✅ IMPLEMENTED |
| PHP static analysis | phpstan **^1.10** — yalnız `shared/composer.json` L25 + script `stan --level=5` L48; subdomainlerde YOK; güncel PHPStan 2.x (2.0: 2024-11-11) | 🔶 kısmi (sürüm geride + kapsam dar) |
| PHPUnit yapılandırma | phpunit.xml — 4 dosya: shared · auth · api · home (**media.coremusic.net'te YOK**) | ✅ IMPLEMENTED |
| JS test runner | vitest **^5.0.3** + @vitest/coverage-v8 ^5.0.3 + jsdom ^30.1.1 — root package.json | ✅ IMPLEMENTED |
| E2E test | @playwright/test **^1.63.0** + playwright ^1.63.0 + axe-playwright ^1.0.0 — root package.json L11/L16 | ✅ IMPLEMENTED (araç) |
| E2E config | `assets.coremusic.net/playwright.config.ts` — testDir ./tests/e2e · baseURL home.coremusic.net:81 · webServer php -S; package.json `test:e2e` | ✅ kanıtlı (dosya okundu — `testing-dogrulama.md` §2 satır 8b) |
| E2E kanıt izi | `playwright-report/index.html` + `test-results/.last-run.json` | 🔶 iz var |
| Secret tarama | `.gitleaks.toml` (kök) + `.github/workflows/secret-scan.yml` (gitleaks-action@v2) | ✅ IMPLEMENTED |

## §5. CI Kapı → Test Türü Eşlemesi (`.github/workflows/ci.yml` — disk okuma)

| CI job | Test türü (K18 karşılığı) | Kapı sertliği |
|---|---|---|
| php-lint (L25) | build-verification (sözdizimi) | sert |
| php-test [Unit] (L66) | unit (+ integration alt kümesi) | sert |
| composer-audit (L110) | security (bağımlılık açığı) | sert |
| js-test (L142) | unit (vitest) | sert |
| js-coverage (L167) | build-verification (coverage) | **soft** (`continue-on-error: true`) ⚠️ |
| js-e2e (L178/L183) | e2e (Playwright) | **soft** (`continue-on-error: true`) ⚠️ |
| secret-scan.yml | security (sır sızıntısı) | ayrı workflow |

> **Kritik gözlem:** 11 çocuktan 4'ü (regression · performance · audio-quality ·
> hardware-validation) için **hiçbir** disk kanıtı yok; bu dördü bu turda
> "boş bırakıldı" — uydurma yok (`testing-master.md` §4.2 not + §6).

## §6. Bağlantılar (özet — detay: testing-bagimliliklar.md)

| Yön | Hedef | Etiket |
|---|---|---|
| depends_on | K17-devops (testler ci.yml joblarında koşulur) | kanıtlı (kayıt: master "K018-devops" yazar — ölü ad, gerçek K17-devops) |
| depends_on | K009-security (security testi auth/middleware yüzeyini yoklar) | kanıtlı |
| depends_on | K007-data (integration test DB/Repository katmanına dokunur) | kanıtlı |
| depends_on | K015-AI/agent-testing (ajan davranışı testi — kademe-3) | planlanan ⚠️ |
| used_by | tüm üretim domainleri (K003-K015) — 55 PHP + 10 JS spec | kanıtlı |

## §7. Kaynaklar

- `testing-master.md` §3 (11 çocuk) · §4/§4.1 (envanter) · §4.2 (CI eşlemesi) — 2026-10-18 okundu
- `testing-dogrulama.md` §2 (S1-S4 kanıt satırları — 18 iddia) — 2026-10-18 okundu
- `.github/workflows/ci.yml` satır kanıtları (L25/L66/L110/L142/L167/L178) — `testing-bagimliliklar.md` §7 aktarımı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
