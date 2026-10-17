---
title: "K019-testing — Domain Kaydı (K-Space V3)"
node_id: K019-testing
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - unit
  - integration
  - contract
  - e2e
  - flaky
  - regression
  - security
  - performance
  - audio-quality
  - hardware-validation
  - build-verification
depends_on:
  - K018-devops       # testler ci.yml joblarında koşulur — kanıtlı (ci.yml php-test/js-test/js-e2e, okundu 2026-10-12)
  - K009-security     # security testi auth/middleware yüzeyini yoklar — kanıtlı (tests/Security · tests/Middleware · ApiPipelineSecurityTest)
  - K007-data         # integration test DB/Repository katmanına dokunur — kanıtlı (tests/Repository ×2 dosya)
  - K003-audio        # audio-quality/hardware-validation ölçtüğü sisteme bağımlı — planlanan (ölçüm kanıtı YOK)
  - K004-dsp          # hardware-validation (ASIO/DSP) hedefi — planlanan (fiziksel test kanıtı YOK)
evidence:
  - "shared/tests = 40 *Test.php (Unit 15 · Api 8 · Middleware 9 · Repository 2 · Events 2 · Security 2 · OAuth 1 · Component 1) — glob 2026-10-12"
  - "alt alan testleri = 15 *Test.php (auth 6 · home 6 · api 3) — glob 2026-10-12; master §4.1 envanteri home 6'yı SAYMIYOR (toplam 49) → çelişki testing-dogrulama.md §3'te tescilli → DÜZELTİLDİ 2026-10-17 (master §4.1 toplam 55)"
  - "K18-testing/ alt dizin = 11 (2026-10-17 glob): e2e/ + flaky/ children'a eklendi; end-to-end/ e2e/'ye birleştirildi (disk kanıtı: K019-testing/e2e node_id'sine 5 kardeş + 1 dış dosya referansı)"
  - "JS spec = 10 dosya (9 .spec.js + 1 .spec.ts — assets.coremusic.net) — glob 2026-10-12"
  - "E2E: assets.coremusic.net/playwright.config.ts (testDir ./tests/e2e · baseURL home.coremusic.net:81 · webServer php -S) + 1 gerçek Playwright spec (tests/e2e/player-info.spec.ts, @playwright/test + axe-playwright import) — okuma 2026-10-12; kök e2e/ = 0 dosya"
  - "ci.yml 6 job: php-lint · php-test [--testsuite Unit] · composer-audit · js-test · js-coverage (soft) · js-e2e (Playwright, soft) — dosya okundu 2026-10-12"
  - "phpstan ^1.10 yalnız shared/composer.json — grep 2026-10-12; phpunit.xml ×4 (shared · auth · api · home) — glob 2026-10-12"
validation:
  - "glob + grep + dosya okuma 2026-10-12 (v2 spec §2.19 çocuk listesi birebir alındı — v2 spec dosyası diskte YOK, bkz k001-celiski-raporu C6)"
  - "web doğrulama 2026-10-17 turunda YAPILDI (3 bekleyen iddia → testing-dogrulama.md §4)"
  - "yapı birleştirme 2026-10-17: children 10 → 11 (e2e + flaky); end-to-end/index.md e2e/index.md'ye taşındı, end-to-end/ kapatıldı — kullanıcı onaylı"
open_questions:
  - "coverage gate neden soft (ci.yml js-coverage continue-on-error)? ≥80% hedefi (vault .ai/AGENTS.md §16) bağlayıcı mı? (⚠️ VERIFICATION REQUIRED)"
  - "E2E kapsamı 1 spec ile mi sınırlandırılacak, spec ailesi büyütülecek mi? (spec konumu kapatıldı 2026-10-17: assets.coremusic.net/tests/e2e/player-info.spec.ts)"
  - "regression · performance · audio-quality · hardware-validation için disk kanıtı YOK (⚠️ VERIFICATION REQUIRED)"
  - "phpstan yalnız shared'de — subdomainler static analysis dışında mı? (⚠️ VERIFICATION REQUIRED)"
  - "media.coremusic.net neden phpunit.xml'siz? tests/ = 0 dosya (doğrulandı) — test kapsamı dışı mı?"
group: mimari
---


# K019-testing — Kodun Çalıştığını Kanıtlayan Alan (Domain)

> **ELI15:** Bu domain, "yazayım da çalışır herhalde" inancının yeridir: kod yazıldıktan sonra
> çalıştığını, bozulmadığını, güvenli olduğunu ve ses kalitesini **tekrarlanabilir gözlemle** kanıtlar.
> Bu alan bir özellik değil, **güven kapısıdır** — CI (K018) burada ölçüleni kapıya uygular.

## 1. Kapsam Sınırı (kısa)

- **Burada:** test stratejisi + senaryo (neyi, nasıl ölçtüğümüz). Test pyramid · unit/integration/contract/E2E · regression/güvenlik/performans · audio-quality/hardware-validation · build-verification.
- **DEĞİL:** testlerin hangi pipeline'da **koşturulduğu** → K018-devops (registry §6 tescilli ayrım: K018 koşar, K019 ölçer) · ajan davranışının testi → K015-AI/agent-testing (kademe-3) · güvenlik **politikasının kendisi** → K009-security (burada: o politikanın sınanması) · ses motorunun kendisi → K003-audio / K004-dsp (burada: ölçülen taraf).

## 2. Kademe-2 Çocuk Kataloğu (11 çocuk — 2026-10-17; v2 spec diskte YOK, kaynak doğrulanamaz ⚠️)

| # | Çocuk | Ne işe yarar (ELI15) | Kanıt durumu (2026-10-17) |
|---|---|---|---|
| 1 | unit/ | Tek bir birimin (sınıf/metot) izole davranışı | ✅ kanıtlı — shared/tests/Unit 15 + alt alan Unit 15 |
| 2 | integration/ | Birimler arası / DB / middleware birlikte davranışı | ✅ kanıtlı — Api 8 · Middleware 9 · Repository 2 · Events 2 |
| 3 | contract/ | API sözleşmesi (request/response şekli) uyumu | ✅ kanıtlı — ApiResponseTest · RouteTableTest · VersioningTest |
| 4 | e2e/ | Tarayıcı akışı (Playwright) | 🔶 kısmi — config + 1 spec (player-info) + report izi + modül dokümanı; repo-kök e2e/ = 0 |
| 5 | flaky/ | Kararsız test tespiti ve karantina | ⚠️ kanıt bekliyor — retry kanıtı var (playwright.config.ts retry:2), karantina mekanizması YOK |
| 6 | regression/ | Daha önce çalışanın bozulmadığını doğrulama | ⚠️ kanıt bekliyor (ayrı suite glob = 0 isabet) |
| 7 | security/ | Güvenlik testi (auth bypass, injection, middleware) | ✅ kanıtlı — tests/Security · Middleware · ApiPipelineSecurityTest |
| 8 | performance/ | Yük/gecikme/bellek ölçümü | ⚠️ kanıt bekliyor (perf testi glob = 0 isabet) |
| 9 | audio-quality/ | Ses kalitesi ölçümü (SNR/THD/latency) | ⚠️ kanıt bekliyor (ölçüm dosyası YOK) |
| 10 | hardware-validation/ | Donanım üzerinde doğrulama (DAC/ADC/ASIO) | ⚠️ kanıt bekliyor (fiziksel test YOK) |
| 11 | build-verification/ | Derleme + test bütünlüğü (lint/type/coverage) | ✅ kanıtlı — ci.yml php-lint · phpstan ^1.10 · js-coverage |

## 3. Bağlı Dosyalar (3 zorunlu MD)

| Dosya | İçerik |
|---|---|
| testing-master.md | Domain anayasası: rol · kapsam · test pyramid · çocuk tablosu · teknoloji envanteri (§4) |
| testing-bagimliliklar.md | depends_on/used_by haritası · entegrasyon sınırları · riskli bağımlılıklar |
| testing-dogrulama.md | İddia → kanıt tablosu · master–disk çelişkileri · web-kanıt iskeleti |

## 4. Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 satır 19) |
| Kanıt dengesi | 5/11 çocuk doğrudan kanıtlı (unit · integration · contract · security · build-verification) + 1 kısmi (e2e) + 5 kanıt bekliyor (flaky · regression · performance · audio-quality · hardware-validation) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `testing-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `testing-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `testing-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
