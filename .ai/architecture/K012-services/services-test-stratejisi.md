---
node_id: K012-services/services-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K013-integraion
  - K18-testing
evidence:
  - "K012-services/services-master.md §9–§16 · §22 — okundu 2026-10-18"
  - "K012-services/services-dogrulama.md §6 (S1 kanıtları) — 2026-10-18"
  - ".ai/AGENTS.md §16 (QA coverage ≥80%) · §25.2 (PHPUnit ^10.5/^11.0 · Vitest · Playwright · ci.yml/secret-scan.yml) — okundu 2026-10-18"
  - "K005-firmware/firmware-test-stratejisi.md §2 piramit yapısı — kanonik referans 2026-10-18"
validation:
  - "2026-10-18 — piramit 3 katman (unit/integration/e2e) + K012'ye özgü 10 senaryo"
  - "2026-10-18 — senaryolarda yalnız S1 kanıtlı eşikler sayısal (60/60s · 429 · Retry-After)"
  - "2026-10-18 — her senaryonun kabul kriteri ölçülebilir (girdi → beklenen çıktı)"
open_questions:
  - "Test altyapısının sahibi K18-testing mi, K012 kendi testini mi koordine eder (master §5.2/§23/7) (⚠️ VERIFICATION REQUIRED)"
  - "timeout birimi (5 — saniye mi) test edilemeden süre senaryoları yazılamaz (master §14) (⚠️ VERIFICATION REQUIRED)"
  - "background-jobs motoru seçilmeden iş-testi aracı belirlenemez (Q5) (⚠️ UNKNOWN)"
group: kod
---

# services-test-stratejisi — Test Stratejisi

> Dayanak: `services-master.md` §9–§16 · §22 · S1 kanıtları ·
> `.ai/AGENTS.md` §16/§25.2 (gerçek test araç envanteri). Eşikler yalnız
> **kanıtlanabilir** değerlerdir; doğrulanmamış eşik uydurulmaz.

## §1 ELI15

> **Test stratejisi "ne zaman neyi deneyeceğimizin" planıdır: en altta
> küçük parçalar, ortada servis birleşimi, en üstte uçtan uca akış denenir.**

K012'de test üç katmanda koşar: her bileşenin **birimi** (defter · oran ·
sürüm · sağlık), servis birleşimlerinin **entegrasyonu** (429 kapısı ·
sürüm başlığı · K013 kontrat sahte sunucusu) ve K011'den gelen **uçtan uca
akış** (çalma/katalog senaryosu). Alt katman çok, üst katman azdır.

## §2 Test piramidi (katmanlar)

| Katman | Ne kapsar | Oran (hedef) | K012 karşılığı | Etiket |
|---|---|---|---|---|
| Unit | tek bileşen saf davranış | ~%70 | defter register/discover · oran sayacı · sürüm çözümleme · sağlık toplama | [TASARIM] |
| Integration | bileşen + komşu (gerçek/sahte) | ~%20 | middleware zinciri · 429+Retry-After · K013 kontrat sahte sunucu | [TASARIM] |
| E2E | K011 → K012 → sonuç | ~%10 | katalog araması · çalma durumu akışı | [TASARIM] |

**Coverage hedefi:** ≥ %80 (`.ai/AGENTS.md` §16 QA standardı — bağlayıcı).

## §2.1 Katman ↔ araç eşlemesi (envanter — §25.2 kanıtlı)

| Araç | Kanıt | K012 katmanı | Etiket |
|---|---|---|---|
| PHPUnit ^10.5/^11.0 | composer.json require-dev (§25.2) | unit + integration (PHP) | ✅ envanter |
| Vitest | §25.2 | unit (JS/TS servis çağrıları) | ✅ envanter |
| Playwright | §25.2 | e2e (K011 akışı üzerinden) | ✅ envanter |
| PHPStan | §25.2 (require-dev) | statik denetim (kapı) | ✅ envanter |
| CI: `ci.yml` · `secret-scan.yml` | `.github/workflows/` 2 dosya (2026-09-27 ölçümü) | gate | ✅ envanter |

> Araç **seçimi yapılıp yapılmadığı** (K012'ye özel runner) ⚠️ UNKNOWN —
> envanter var, bu domain'e uyarlanması onay ister.

## §3 Senaryo çekirdekleri (10 senaryo — K012'ye özgü)

| # | Senaryo | Katman | Girdi | Beklenen | Kanıt/Eşik |
|---|---|---|---|---|---|
| T1 | servis kaydı + keşif | unit | `register()` → `discover('music-service')` | tanım döner, yoksa null | S1:26/38 |
| T2 | oran penceresi aşımı | unit/int | 61 istek / 60 sn | 61. istek **429** + `Retry-After` | S1:34 |
| T3 | oran penceresi sıfırlama | unit | 60 sn bekle → istek | 200 (pencere yenilendi) | S1:34 (pencere) |
| T4 | sürüm başlığı okuma | integration | `X-API-Version: 1.0.0` | sürüm kabul edilir; yanlış sürüm → davranış ⚠️ | S1:57 |
| T5 | sağlık toplama | unit | 1 servis sağlıksız raporla | `hasFailures()=true` · `overallStatus≠healthy` | S1:34/44 |
| T6 | defter temizleme | unit | `clear()` | sonraki discover null döner | S1:97 |
| T7 | kontrat sahtesi (K013) | integration | sahte dış API 500 | hatayı sınıflandır, retry **yalnız** idempotent işte | ADR-101 + master §22.3 |
| T8 | arka plan işi başarısızlığı | unit | iş 3 kez hata atar (öneri) | FAILED + DLQ kaydı (motor ⚠️ Q5) | [TASARIM] + ⚠️ |
| T9 | katalog araması (e2e) | e2e | K011'den "ara" | music-service sonucu listeler | master §22.2 |
| T10 | çalma durumu akışı (e2e) | e2e | oynat → duraklat | sıra/konum durumu doğru | master §21.3 + ⚠️ Q2 |

> **Eşik dürüstlüğü:** T2/T3'teki tek sayısal eşik **60/60s** (S1 kanıtlı).
> T8'deki "3 deneme" bir **tasarım önerisidir** — gerçek değer ⚠️ UNKNOWN
> (ADR-101 retry sınırı okunmadı; `.ai/.decisions/index.md` yalnız başlıktır).

## §4 Kapılar (gate'ler — CI ile bağlantı)

| # | Kapı | Koşul | Kaynak |
|---|---|---|---|
| G1 | statik analiz | PHPStan temiz | §25.2 envanter |
| G2 | unit | tüm unit yeşil | strateji |
| G3 | oran kapısı | T2 → 429 kanıtı | S1:34 |
| G4 | sürüm kapısı | T4 başlık kanıtı | S1:57 |
| G5 | secret taraması | `secret-scan.yml` temiz | workflow kanıtı (2026-09-27) |
| G6 | coverage | ≥ %80 | `.ai/AGENTS.md` §16 |

## §5 Kabul kriterleri (ölçülebilir)

| # | Kriter | Ölçüm |
|---|---|---|
| K1 | 61. istek 429 + Retry-After taşır | otomatik test sayacı |
| K2 | `discover()` bilinmeyen id için null (hata fırlatmaz) | unit assert |
| K3 | `hasFailures()` tek sağlıksız kayıtta true | unit assert |
| K4 | `clear()` sonrası defter boş | unit assert |
| K5 | 429 dışında 5xx'te retry yalnız idempotent işte | entegrasyon assert |
| K6 | coverage ≥ %80 | CI raporu |
| K7 | flaky test oranı %0 (tekrar koşuda aynı sonuç) | CI tekrar koşusu |

## §6 Test kuralları

1. Sayısal eşik yalnız **S1 kanıtlı** değerlerle yazılır; kalanı ⚠️/TASARIM.
2. Dış API testleri **canlı** yapılmaz — K013 kontratı sahte sunucuyla denenir
   (gerçek anahtar/.env asla test verisine girmez).
3. Test, kural **üretmez**: beklenen davranış master §'sinden okunur.
4. Süreç-çöküşü/yedek senaryoları (master §16 ⚠️) kapsam dışıdır — sahiplik
   belirlenmeden yazılan test uydurma olur.

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | piramit 3 katman + oran etiketi | sayım | ✅ 2026-10-18 |
| C2 | 10 senaryo K012'ye özgü | sayım | ✅ 2026-10-18 |
| C3 | her senaryonun ölçülebilir kabul kriteri var | §5 çaprazı | ✅ 2026-10-18 |
| C4 | araç adları envanter kanıtlı | `.ai/AGENTS.md` §25.2 | ✅ 2026-10-18 |
| C5 | timeout birimi gerekli testlerde | — | ❌ ⚠️ (açık) |

## §8 Açık sorular

1. Test altyapısı sahipliği: K18-testing mi K012 mi (master §23/7)? → ⚠️ VERIFICATION REQUIRED
2. timeout=5 birimi nedir (süre testi için zorunlu)? → ⚠️ VERIFICATION REQUIRED
3. background-jobs motoru (Q5) seçilmeden T8 kalıcı olmaz → ⚠️ UNKNOWN
4. playback sahipliği (Q2) çözülmeden T10 iki domain arasında kalır → ⚠️ VERIFICATION REQUIRED

## §9 Senaryo ↔ risk bağı (`services-master.md` §7 ile çapraz)

| Senaryo | Bağlı açık soru | Risk yüzeyi |
|---|---|---|
| T7 | Q9 taşıyıcı | sözleşmesel uyumsuzluk |
| T8 | Q5 motor | kayıp iş |
| T10 | Q2 playback | sahiplik çakışması |
| T5 | sağlık periyodu ⚠️ | geç tespit |

## §10 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `services-master.md` §9–§16 · §22 | senaryo kaynağı |
| `services-dogrulama.md` §6 | S1 eşik kanıtları |
| `integraion-test-stratejisi.md` | K013 kontrat testinin öbür yüzü |
| `K18-testing/` | test altyapısı sahipliği (⚠️ soru 1) |

## §11 Kapanış kontrol listesi

- [x] §1 ELI15
- [x] piramit (unit/integration/e2e)
- [x] K012'ye özgü ≥10 senaryo
- [x] ölçülebilir kabul kriterleri
- [x] open_questions ⚠️ işaretli
- [x] ≥120 satır

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-18
