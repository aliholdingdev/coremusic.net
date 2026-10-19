---
title: "K18-testing — Bağımlılıklar"
node_id: K18-testing
node_type: domain-dependencies
status: draft
updated: 2026-10-17
version: 1.1.0
group: mimari
---


# K019-testing — testing-bagimliliklar.md

> **Etiketler:** kanıtlı = dosya/okuma/glob gösterildi · planlanan = mimari yargı (disk kanıtı yok) · vault beyanı = bağımsız ölçüm yok.

## §1 Amaç

K019-testing'in depends_on/used_by haritası, entegrasyon sınırları ve riskli bağımlılıkları.
Kanıt temeli: tests/ ağaçları + composer.json + package.json + ci.yml glob/okuma ölçümü (2026-10-12).
Kanıtsız bağımlılık uydurulmaz — §5/§6'da `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K17-devops | kanıtlı | Testler CI job'larında koşulur; senaryo burada, koşum orada | ci.yml php-test (L66, `--testsuite Unit`) · js-test (L142) · js-e2e (L178) — okundu 2026-10-12 |
| 2 | K009-security | kanıtlı | Security testi auth/middleware yüzeyini yoklar; o yüzey K009'un politikasıdır | shared/tests/Security = 2 · tests/Middleware = 9 · shared/tests/Api/ApiPipelineSecurityTest.php — glob 2026-10-12 |
| 3 | K007-data | kanıtlı | Integration test DB/Repository katmanına dokunur | shared/tests/Repository = 2 dosya (ApiKeyRepositoryTest · ApiKeyCreateCommandTest) — glob 2026-10-12 |
| 4 | K003-audio | planlanan | audio-quality/ ölçtüğü ses sisteminin kendisine bağımlı (master §5) | ⚠️ VERIFICATION REQUIRED — ölçüm dosyası/kanıtı bu turda YOK |
| 5 | K004-dsp | planlanan | hardware-validation/ ASIO/DSP zincirini fiziksel/ölçekli doğrular (master §5) | ⚠️ VERIFICATION REQUIRED — fiziksel test kanıtı YOK |
| 6 | PHP toolchain (phpunit ^10.5) | kanıtlı | PHP test çalıştırıcısı | shared · auth · api · home composer.json `phpunit/phpunit: ^10.5` — grep 2026-10-12 |
| 7 | JS toolchain (vitest ^5.0.3 · @playwright/test ^1.63.0) | kanıtlı | JS unit + E2E çalıştırıcısı | root package.json L11 · L16 — grep 2026-10-12 |
| 8 | PHPStan ^1.10 (static analysis) | kanıtlı | build-verification/ tip denetimi | shared/composer.json L25 (`phpstan/phpstan: ^1.10`) + script `stan --level=5` (L48) — yalnız shared'de |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | K17-devops (CI kapıları) | kanıtlı | ci.yml job'ları bu domainin senaryolarını koşar (php-test/js-test/js-e2e) |
| 2 | Tüm üretim domainleri (K003–K015) | kanıtlı | 55 PHP *Test.php (shared 40 + auth 6 + home 6 + api 3) + 10 JS spec bu domainin senaryolarıdır — glob 2026-10-12. ✅ Master §4.1 2026-10-17'de 55'e düzeltildi (testing-dogrulama.md §3-ç1 kapandı) |
| 3 | K015-AI/agent-testing | planlanan | ajan davranışı testi bu stratejinin kademe-3 uzantısı (master §5) — disk kanıtı YOK |
| 4 | QA Engineer (agent) | vault beyanı | .ai/AGENTS.md §4/§16: QA sahipliği (coverage ≥80%, flaky %0) — hedef vault beyanıdır, ölçüm YOK |

## §4 Entegrasyon Sınırları

| Sınır | Kural | Kaynak |
|---|---|---|
| K019 ↔ K018 | Testi **tasarlar/ölçer** K019; pipeline'da **koşar** K018 — kapsam kayması sessiz taşınmaz | testing-master.md §2 (registry §6 tescilli ayrım) |
| K019 ↔ K009 | Politika K009'da; burada yalnız o politikanın **sınanması** | testing-master.md §2 |
| K019 ↔ K003/K004 | Ölçüm K019'da; DSP/donanım kendisi K003/K004'te | testing-master.md §2 |
| K019 ↔ K016 | Ajan davranışının testi kademe-3 (bu fazda AÇILMADI) | master §5 (planlanan) |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Coverage gate soft** (ci.yml js-coverage L167 `continue-on-error: true`) | ≥80% hedefi bağlayıcı değil; coverage düşebilir | eşik büyüyünce hard gate (A6/A7 desenli — K018 risk 3 ile aynı) |
| 2 | **E2E gate soft + kapsam dar** (ci.yml js-e2e L183 continue-on-error; kök e2e/ = 0 dosya ama assets.coremusic.net/tests/e2e/player-info.spec.ts = 1 gerçek Playwright spec + playwright.config.ts) | E2E kapısı yalnız tek bileşen/spec ile geçilebilir — ürün akışları (login/play/settings) E2E'de sınanmıyor | spec ailesi büyütülecek — master §7 açık soru 2 bu turda büyük ölçüde kapandı |
| 3 | **phpstan yalnız shared'de** (subdomain composer.json'larında YOK) | auth/api/home static analysis dışında | subdomainlere phpstan ekleme kararı (ADR gerekli) |
| 4 | **media.coremusic.net test kapsamı dışında** (phpunit.xml YOK · tests/ = 0 dosya) | bu alt alanın regresyonu kanıtsız | kapsam kararı: test ekle ya da istisnayı tescille |
| 5 | ~~Master–disk sayım çelişkisi~~ (master §4.1: 49 PHP test; disk: 55) | envanter güvenilirliği zedelenir | ✅ KAPANDI 2026-10-17 (master §4.1 home satırı eklendi, toplam 55 — testing-dogrulama.md §3-ç1) |
| 6 | **"CI geçti" kanıtsız** (vault §25.2; GitHub run geçmişi diskte değil) | yeşil kapı sanılan şey kırmızı olabilir | GitHub arayüz kanıtı sonraki turda |
| 7 | **Regression/performance/audio/hardware kanıtsız + flaky karantina YOK** | 4/11 çocuk boş, flaky kapıları eksik — domain verified olamaz | kanıt toplama ya da kapsam daraltma ADR'si |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. K003/K004 bağımlılıkları hangi ölçüm/arayüzle kanıtlanacak? (şu an yalnız mimari yargı)
2. K016/agent-testing kademe-3 ne zaman açılıyor? (bu fazda children boş)
3. Coverage ≥80% hedefi hangi kapıda bağlayıcı olacak (ci.yml hard gate mi, vault denetimi mi)?
4. ~~home.coremusic.net/tests (6 dosya) master envanterine neden girmedi?~~ KAPANDI (2026-10-17): sayım hatasıydı; master §4.1'e 6 satırı eklendi, toplam 55.

## §7 Kaynaklar

- testing-master.md §5 (bağımlılık özeti — bu dosyanın kaynağı) · v2 spec §2.19 · ADR-097 §2/§5
- .ai/architecture/index.md §5 satır 19 (K019-testing · testing-master.md · draft)
- Disk (2026-10-12): tests/ ağaçları glob · composer.json ×4 grep · package.json grep · ci.yml okuma
- Disk (2026-10-12): `assets.coremusic.net/tests/e2e/player-info.spec.ts` — `@playwright/test` import'lu tek gerçek E2E spec; kök `e2e/` 0 dosya (glob okuma ile iki taraflı ölçüldü)
- Disk (2026-10-12): `.github/workflows/ci.yml` iş envanteri — `php-test` (L66 `--testsuite Unit`) · `js-test` (L142) · `js-e2e` (L178) · `js-coverage` (L167 `continue-on-error: true`) — §2/§5 satırlarının birincil kanıtı

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 (ilk) → 2026-10-17 (rev. 1.1.0: risk 5 + §6-4 kapandı)
