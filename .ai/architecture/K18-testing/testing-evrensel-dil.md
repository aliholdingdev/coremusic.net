---
node_id: K18-testing-evrensel-dil
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops]
evidence:
  - ".ai/architecture/K18-testing/testing-master.md §2-§4.2 (terimlerin bu domain kullanımı — 2026-10-18)"
  - "5 URL doğrulaması: martinfowler practical-test-pyramid · TestPyramid bliki · test-shapes · testrail · smashingmagazine (2026-10-18 — terim tanımları okunan kaynaklardan)"
validation: "≥15 EN/TR çifti; her terim tek bir K18 anlamına bağlı; çok anlamlı terimler (mock/stub, unit/integration) Fowler 2021 belirsizlik notuyla işaretlenir."
open_questions:
  - "⚠️ 'Unit test' tanımı alan-değişken (sociable/solitary — Fowler 2021); CoreMusic'te hangi stil bağlayıcı? ADR'siz varsayılmaz"
group: kanit
---

# Testing — Evrensel Dil (EN/TR)

> K18-testing'in İngilizce ve Türkçe terim çiftleri. Terimler bu domainin
> gerçek kullanımına bağlıdır; çok anlamlı olanlar açıkça işaretlenir.

## §1. ELI15 — Neden ortak dil?

Test dünyası İngilizce terimlerle çalışır (PHPUnit, "unit test", "coverage");
Türkçe ekip ise aynı kavramı farklı kelimelerle söyleyebilir. Bu sözlük, aynı
kavramın iki dilde de **tek bir anlamı** olmasını sağlar — tartışmalar terim
kavgasına değil, konuya gitsin.

## §2. Terim Tablosu (22 çift)

| # | English | Türkçe | K18-testing anlamı (tek anlam) |
|---|---|---|---|
| 1 | Test Pyramid | Test Piramidi | Dağılım rehneri: taban=unit (çok), orta=integration (az-orta), tepe=e2e (az) — `testing-test-stratejisi.md` §2 |
| 2 | Unit Test | Birim Testi | Tek birimin (sınıf/metot) izole davranışı; hızlı, bağımsız — disk: 55 PHP dosyasının büyük kısmı |
| 3 | Integration Test | Entegrasyon Testi | Birimler arası/DB/middleware birlikte davranışı — disk: tests/Api · Middleware · Repository · Events |
| 4 | Contract Test | Sözleşme Testi | API request/response şeklinin uyumu — disk: ApiResponseTest · VersioningTest |
| 5 | E2E Test | Uçtan Uca Test | Tarayıcı akışının gerçek ortamda koşulması — disk: 1 Playwright spec (kapsam dar ⚠️) |
| 6 | Regression Test | Regresyon Testi | Daha önce çalışanın bozulmadığını doğrulama — disk karşılığı YOK ⚠️ (kanıt bekliyor) |
| 7 | Performance Test | Performans Testi | Yük/gecikme/bellek ölçümü — disk karşılığı YOK ⚠️ |
| 8 | Coverage | Kod Kapsama | Testlerin kodun ne kadarını dokunduğu — hedef ≥80% vault beyanı (S3); kapı soft ⚠️ |
| 9 | Line Coverage | Satır Kapsama | Çalıştırılan satır oranı — ADR-023 %90 hedefi |
| 10 | Branch Coverage | Branş Kapsama | Koşul dallarının test edilme oranı — ADR-023 kritik yollar %100 |
| 11 | CI (Continuous Integration) | Sürekli Entegrasyon | Her push'ta otomatik kapı (lint/test/audit) — ci.yml; koşan taraf K17-devops |
| 12 | CI Gate | CI Kapısı | Job'un geçmesi zorunlu eşiği — sert vs **soft (continue-on-error)** ayrımı: coverage/E2E soft ⚠️ |
| 13 | Soft Gate | Yumuşak Kapı | Başarısız olsa bile geçmişi düşürmeyen job (`continue-on-error: true`) — R1/R2 riski |
| 14 | Test Suite | Test Takımı | Birlikte çalıştırılan test kümesi — `--testsuite Unit` (ci.yml L66) |
| 15 | Test Fixture | Test Sabit Verisi | Testin ihtiyacı olan hazır veri — shared/tests/Fixtures/ |
| 16 | Flaky Test | Kararsız Test | Aynı kodda bazen geçen/bazen geçen test — retry kanıtlı (retry:2); karantina YOK ⚠️ |
| 17 | Retry | Yeniden Deneme | Testi tekrar koşma — playwright.config.ts retry:2 (kanıtlı) |
| 18 | Quarantine | Karantina | Flaky testi geçici olarak kapıdan düşürme — repo'da mekanizma YOK ⚠️ |
| 19 | Static Analysis | Statik Analiz | Kodu çalıştırmadan inceleme — phpstan ^1.10 yalnız shared'de ⚠️ |
| 20 | Lint | Sözdizimi Denetimi | Dil kuralları taraması — php-lint job (ci.yml L25, sert kapı) |
| 21 | Secret Scanning | Sır Taraması | Kodda anahtar/parola taraması — .gitleaks.toml + secret-scan.yml |
| 22 | Assertion | Doğrulama İddiası | Testin beklediği gerçek (`expect`/`assert*`) — Arrange-Act-Assert'in A'sı |

## §3. Çok Anlamlı Terim Uyarıları (okunmadan varsayılmaz)

| Terim | Belirsizlik | Kural |
|---|---|---|
| **Unit test** | Fowler 2021: "unit" tanımı belirsizdir — sociable (gerçek komşularla) vs solitary (mock'lu). 24 farklı tanım olduğu söylenir (test-shapes, 2026-10-18 okundu) | CoreMusic'te hangi stil bağlayıcı? **ADR'siz varsayılmaz** — mevcut disk kanıtı yalnız dosya sayısıdır, stil değil |
| **Mock / Stub / Spy** | Hepsi "Test Double" ailesindendir; mock expectation doğrular, stub sadece cevap verir | Fowler 2021: honeycomb savunucuları mock yoğunluğunu eleştirir — mock sayısı hedef değildir |
| **Integration test** | Tanımı da bulanık (test-shapes): birilerinin "integration" dediği başkasının "sociable unit" olabilir | Bu domainde sınır = **gerçek bağımlılık dokunumu** (DB/middleware); mock'suz çalışır |
| **Soft gate** | "Gate" dendiğinde akla sert kapı gelir | K18'de 2 job soft'tur (js-coverage, js-e2e) — "kapı var" ≠ "kapı sert" |

## §4. Kısaltmalar

| Kısaltma | Açılım |
|---|---|
| AAA | Arrange-Act-Assert (test yerleşim deseni) |
| CDC | Contract-Driven Development / Consumer-Driven Contracts (Pact) |
| ENOENT | Error NO ENTity — dosya/dizin bulunamadı ("CI geçti" doğrulanamazlığının kanıt izi) |
| S1-S4 | Kanıt sınıfları: S1 dosya okuma · S2 glob/grep · S3 vault beyanı · S4 kaynaksız (kullanılamaz) |

## §5. Kaynaklar

- `testing-master.md` §2-§4.2 — domain kullanımı (2026-10-18)
- martinfowler test-shapes (2026-10-18, HTTP 200) — unit/integration belirsizliği, Test Double
- martinfowler practical-test-pyramid (2026-10-18, HTTP 200) — katlar, AAA
- `testing-dogrulama.md` §1 (S1-S4 sınıfları) — 2026-10-18

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
