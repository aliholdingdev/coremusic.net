---
node_id: K18-testing-test-stratejisi
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K009-security, K007-data, K17-devops, K003-audio, K004-dsp]
evidence:
  - ".ai/architecture/K18-testing/testing-master.md §2.1 (pyramid: diskten sayılan dağılım — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md §3 (11 çocuk = strateji senaryolarının evi — 2026-10-18)"
  - ".ai/.decisions/accepted/ADR-023-persona-driven-testing.md satır 18 (20 persona matrisi · happy/error/boundary · kritik yol %100 branch — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-dogrulama.md §4 (PHPUnit --testsuite · web kanıtları — 2026-10-18)"
validation: "Pyramid diskten sayılan dağılıma bağlı; senaryolar 11 çocukla eşleşir; kabul kriterleri ölçülebilir ve kanıt sınıfı taşır; eşik uydurulmaz (≥80% = vault beyanı S3)."
open_questions:
  - "⚠️ ADR-023'ün 20 persona matrisi ile çocukların birebir bağlanışı henüz yapılmadı (PLANNED — ADR-023 satır 63)"
  - "⚠️ regression/performance/audio-quality/hardware-validation senaryoları = TASLAK (kanıt yok)"
group: kod
---

# Testing — Test Stratejisi

> K18-testing'in pyramid yorumu, kat bazlı senaryoları ve kabul kriterlerini
> tanımlar. Pyramid diskten sayılan test dosyalarından türetilmiştir
> (vault-yorum DEĞİL — `testing-master.md` §2.1).

## §1. ELI15 — Test stratejisi neden gerekli?

"Test yazalım" demek yetmez; **hangi testten ne kadar** yazacağımız gerekir.
Çok fazla yavaş (her commit 1 saat sürer), çok az güvensiz (bozulma kaçar).
Strateji = pyramid (dağılım) + senaryolar (ne test edilir) + kabul kriterleri
(nasıl "bitti" deriz). Bu üçü bir arada olmazsa test, üretime kadar giden
yolda bir kapı değil, sadece bir alışkanlık olur.

## §2. Test Pyramid (diskten sayılan dağılım — `testing-master.md` §2.1)

| Kat | Test türü | Diskteki karşılığı | Sayı (2026-10-12) |
|---|---|---|---|
| Taban (çok) | **Unit** | shared/tests/Unit (15) · Component · OAuth · Security birim altı + auth 6 + api 3 + home 6 | 55 PHP dosyasının büyük kısmı |
| Orta | **Integration** | tests/Api (8) · tests/Middleware (9) · tests/Repository (2) · tests/Events (2) | ~21 dosya |
| Tepe (az) | **E2E (Playwright)** | assets.coremusic.net/tests/e2e/ — 1 spec + config + report izi | **1 spec** (kapsam dar ⚠️) |

**Yorum kuralı:** pyramid bu turda **bağımsız ölçülmedi**; dağılım dosya
sayımından türetildi. Hedef oran (coverage ≥80%) vault beyanıdır (S3).

## §3. Kat Bazlı Senaryolar (11 çocukla eşleşir)

| # | Senaryo | Kat/Çocuk | Tip | Kanıt durumu |
|---|---|---|---|---|
| T1 | Route table'a kayıt + mükerrer kayıt boundary (`LogicException`) | unit/ | happy + boundary | ✅ şablon (Blok 1) |
| T2 | Süre biçitleyici — normal, negatif clamp | unit/ | happy + boundary | ✅ şablon (Blok 2) |
| T3 | Store + formatter birlikte (sociable, mock'suz) | integration/ | happy | ✅ şablon (Blok 3) |
| T4 | API envelope sözleşmesi `{ data, meta }` + version alanı | contract/ | happy | ✅ repo karşılığı: ApiResponseTest · VersioningTest |
| T5 | API pipeline security (auth yokken 401/403) | security/ | error | ✅ repo: ApiPipelineSecurityTest · tests/Security ×2 |
| T6 | Middleware 9 senaryosu (CSRF/CSP yüzeyi — politika K009'da) | security/ + integration/ | error | ✅ repo: tests/Middleware 9 |
| T7 | Repository API anahtarı oluşturma + okuma | integration/ | happy | ✅ repo: ApiKeyRepositoryTest |
| T8 | php lint + composer audit kapısı (sözdizimi/bağımlılık açığı) | build-verification/ | guard | ✅ ci.yml sert kapılar |
| T9 | Playwright player-info akışı (1 spec) | e2e/ | happy | 🔶 repo: player-info.spec.ts — **kapsam dar** |
| T10 | Retry=2 ile flaky davranışı | flaky/ | guard | 🔶 config kanıtlı; **karantina YOK** ⚠️ |
| T11 | Regression: önceki sürüm davranışı | regression/ | guard | ⚠️ TASLAK — ayrı suite yok |
| T12 | Yük altında yanıt süresi | performance/ | guard | ⚠️ TASLAK — araç/ekipman yok |
| T13 | SNR/THD+N ölçümü | audio-quality/ | guard | ⚠️ TASLAK — hedef tanımı bekliyor |
| T14 | DAC/ADC/ASIO fiziksel doğrulama | hardware-validation/ | guard | ⚠️ TASLAK — rig tanımı bekliyor |

## §4. Kabul Kriterleri (ölçülebilir — eşik uydurulmaz)

| # | Kriter | Ölçüm (kanıt sınıfı) | Durum |
|---|---|---|---|
| K1 | 55 PHP + 10 JS dosyası yeşil koşuyor | ci.yml php-test + js-test job çıktısı | kapı sert; **run kanıtı R3** (CI geçti doğrulanmadı ⚠️) |
| K2 | Coverage ≥%80 | `.ai/AGENTS.md` §16 hedefi — **vault beyanı (S3)**; ci.yml js-coverage soft ⚠️ | ölçülmüyor (rapor yok) |
| K3 | Kritik yollar (auth/API) %100 branch | ADR-023 madde (c) — accepted | ⚠️ ADR metninde; PR gate henüz soft |
| K4 | E2E'de login/play/settings akışları | spec sayısı/artışı | ❌ yok — tek spec ⚠️ |
| K5 | flaky %0 | `.ai/AGENTS.md` §16 hedefi; karantina mekanizması YOK | ölçülmüyor |
| K6 | secret-scan temiz | secret-scan.yml + .gitleaks.toml | ✅ kapı var (geçiş kanıtı GitHub'da) |
| K7 | Regression suite kapsamı tanımlı | ADR kararı | ⚠️ tanımsız |
| K8 | media.* kapsam kararı tescilli | ADR | ⚠️ bekliyor |

## §5. Test Verisi & İzolasyon Kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | DB testleri transaction içinde + rollback (fixture `shared/tests/Fixtures/`) | `testing-master.md` §4.1 (Fixtures dizini kanıtlı) |
| 2 | PHP testleri `--testsuite` ile çalışır; PHPUnit 11+ kuralı: bir sınıf birden fazla suite'e ait OLAMAZ | `testing-dogrulama.md` §4/1-2 (web doğrulama 2026-10-17) |
| 3 | E2E config: `assets.coremusic.net/playwright.config.ts` — baseURL home.coremusic.net:81, webServer php -S, retry:2 | dosya okundu (`testing-dogrulama.md` §2/8b) |
| 4 | ORM yok; testler raw PDO sorgu yazabilir mi? → ADR-002 (PDO mandatory) bağlayıcı | `.ai/.decisions` (ADR-002) |
| 5 | Testlerde secret: `.env` okunur, koda gömülmez (gitleaks kapısı bunu da korur) | registry §5 + .gitleaks.toml |

## §6. Strateji Kapıları → CI Eşlemesi

| Strateji katı | CI job | Sertlik |
|---|---|---|
| Unit | php-test [Unit] (L66) · js-test (L142) | sert ✅ |
| Integration (alt küme) | php-test [Unit] (suite içinde) | sert ✅ |
| Security | composer-audit (L110) · secret-scan.yml · ApiPipelineSecurity | sert ✅ |
| Build-verification | php-lint (L25) · js-coverage (L167) | lint sert · **coverage soft ⚠️** |
| E2E | js-e2e (L178/L183) | **soft ⚠️** |
| Regression/performance/audio/hardware | — (hiçbir job yok) | **kapı YOK ⚠️** |

## §7. Kaynaklar

- `testing-master.md` §2.1 (pyramid) · §3 (çocuklar) · §4.2 (CI eşlemesi) — 2026-10-18
- `testing-dogrulama.md` §2 (kanıt satırları) · §4 (web kanıtları) — 2026-10-18
- ADR-023 satır 18 (persona matrisi · kabul maddeleri) — 2026-10-18
- martinfowler.com/articles/practical-test-pyramid.html (2026-10-18, HTTP 200 — kat tanımları)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
