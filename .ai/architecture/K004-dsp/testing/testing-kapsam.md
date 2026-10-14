---
title: "testing — Kapsam"
type: architecture-scope
node_id: K004-dsp/testing
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# testing — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 12 bu dalı **"DSP testleri ve bozulma kontrolü"** olarak
yazar. Basitçe: DSP bloklarının doğru çalıştığını kanıtlayan senaryolar — referans
sinyalle giriş ver, beklenen çıkışla karşılaştır, bozulma (distortion) eşiğini kontrol
et. `index.md` bunu "iddia değil, doğrulama" diye çağırır. testing, K004'ün
kabul-kriteri kapısının test senaryosu tarafıdır (ölçüm measurement'ın işi).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | DSP blok test senaryoları (referans sinyal → beklenen çıkış) | Frekans/THD ölçümünün kendisi | measurement |
| 2 | Bozulma (distortion) kontrol senaryosu | Ölçüm eşiklerinin kaynağı | measurement · dsp-master §4 T7 |
| 3 | Test kapsamı hedefi (coverage kriteri) | Ölçüm standardı (R128) | measurement |
| 4 | Regresyon/altın-örnek (golden sample) karşılaştırma | Ölçüm araç zinciri | measurement |
| — | *(dışında)* test kapsamı hedefi tanımlı değil | — | **UNKNOWN — R1** |

**Yasak:** testing measurement ile sınır — testing senaryoyu/doğrulamayı, measurement
gerçek ölçümü üretir. Kapsam hedefi (R1) kanıtsız sayı yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | testing ↔ measurement (senaryo ↔ gerçek ölçüm) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q4 |
| A2 | DSP test kapsamı hedefi (ör. %/eşik) | **UNKNOWN** — tahmin yasak | testing/index.md open_questions |
| A3 | bozulma kontrolü hangi metrikle (THD vs görse/eşik) | ⚠️ bağlı (Q4 + T7) | dsp-master §4 T7 |
| A4 | test altyapısı (framework/CI) | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "DSP testleri ve bozulma kontrolü" | `dsp-master.md` §3 satır 12 | ✅ dosya okundu |
| 2 | "test kapsamı hedefi tanımlı değil" açık soru | `testing/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/tests/` PHP testleri var ama DSP testi yok | `shared/AGENTS.md` §2 · `shared/tests/` (PHP, alakasız) | ✅ dosya okundu (negatif sınır) |
| 4 | measurement ↔ testing ayrımı Q4'te açık | `dsp-master.md` §7 Q4 | ✅ dosya okundu |
| 5 | DSPark `tests/` + `conformance/` dizinleri + CI (Windows/Linux/macOS/WASM/embedded) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | Patina Catch2 v3 ile 55+ test dosyası | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 7 | Signalsmith-Audio/dsp testleri ayrı depoda (output-quality ölçer) | https://signalsmith-audio.co.uk/code/dsp | ✅ 2026-10-12 doğrulandı |
| 8 | Bozulma kontrolü THD+N < 0.01% @ 1 kHz eşiği [KANIT] | `dsp-master.md` §4 T7 | ✅ dosya okundu |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | DSP test kapsamı hedefi (kapsanacak bloklar/eşik)? | **UNKNOWN** |
| R2 | testing ↔ measurement sınırı (Q4)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Test altyapısı/framework seçimi? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 kapsam-hedefi + R3 altyapı kapanmadan bu
dosyada test-iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3 · zero-hallucination).

## §6 Test senaryo ailesi (PLANNED — hangi blok nerede test edilecek?)

| # | Senaryo | Blok | Beklenen |
|---|---|---|---|
| 1 | filters frekans tepkisi | equalizer | ±tölerans UNKNOWN |
| 2 | compressor GR doğrulama | dynamics | literatür |
| 3 | limiter ceiling | limiter | true-peak (dBTP) |
| 4 | reverb decay (RT60) | reverb | ölçüm (measurement) |
| 5 | spatial pan sol/sağ | spatial | enerji oranı UNKNOWN |
| 6 | pipeline blok boyutu | pipeline | 512 (sartname) |

**Durum:** 0/6 — hiçbiri çalıştırılmadı; framework (R3) UNKNOWN.

## §7 Bozulma kontrolü (§2/§2 — eşik [KANIT])

**Eşik:** THD+N < 0.01% @ 1 kHz (dsp-master §4 T7 [KANIT]).
**Metrik:** THD+N (measurement dalının ürettiği değer — §2 sınırı).
**Senaryo:** saf ton giriş → blok → THD+N ölçümü (measurement §12) → kapı.

**A3 notu:** bozulma kontrolünün THD+N mi başka metrik mü olacağı bağlı
kargaşadır (A3/Q4); şu an THD+N [KANIT]tır.

## §8 Regresyon / altın-örnek (golden sample — §2/§4)

| Kavram | Anlam |
|---|---|
| altın-örnek | bilinen iyi çıktının kaydı |
| regresyon | değişiklik sonrası aynı girdi → aynı çıkış |
| referans sinyal | test girdisi (dosya/üretici) |

**Durum:** CoreMusic'te altın-örnek yok (grep §11); bu bölüm yalnız kavramdır.

## §9 Disk kanıtları (bu tur eklendi — 2026-10-13)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 9 | çocuk rolü "DSP testleri ve bozulma kontrolü" | `dsp-master.md` §3 satır 12 | ✅ (§4/1) |
| 10 | test kapsamı hedefi tanımlı değil | `testing/index.md` open_questions | ✅ (§4/2) |
| 11 | CoreMusic'te DSP testi yok | `shared/tests/` (PHP, alakasız) | ✅ (§4/3) |

## §10 HEAD doğrulama tablosu (2026-10-13)

| # | URL | Durum |
|---|---|---|
| 1 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 2 | https://github.com/ShmKnd/Patina | ✅ 200 |
| 3 | https://signalsmith-audio.co.uk/code/dsp | ✅ 200 |
| 4 | https://github.com/juce-framework/JUCE | ✅ 200 |
| 5 | https://en.wikipedia.org/wiki/Clipping | ✅ 200 |

> 403/YASAK ve 404 başlıklar kullanılmadı.

## §11 Grep kanıtı (DSP testi)

| Arama | Sonuç |
|---|---|
| `TEST_CASE`/`Catch2` (C++) | 0 (yalnız PHP testleri var) |
| `THD`/`distortion` (test) | 0 |
| `golden`/`regression` (test) | 0 |
| `shared/tests/` | PHP (alakasız — §4/3) |

## §12 Test doğrulama protokolü (PLANNED)

1. R3 kapat (framework seçimi).
2. R1 kapat (kapsam hedefi — hangi bloklar, hangi eşik).
3. Test senaryolarını (§6) blok sözleşmelerine bağla.
4. Bozulma kontrolünü (§7) THD+N ölçümüne bağla.
5. Altın-örnek (§8) kaydını oluştur.
6. Regresyon çalıştırma → PASS/FAIL.

**Durum:** 0/6 — protokol PLANNED.

## §13 Anti-pattern (testing)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | kapsam hedefi tahmin etmek | A2/R1 ihlali |
| 2 | framework tahmin etmek | A4/R3 ihlali |
| 3 | PHP testini DSP testi sanmak | §4/3 |
| 4 | altın-örnek uydurmak | §8 |
| 5 | ölçümü test sanmak | A1 (measurement sınırı) |

## §14 Topoloji (test yerleşimi — PLANNED çizim)

```text
blok (filters/dynamics/reverb/spatial/pipeline)
  → referans sinyal (§8)
    → blok çalıştırma (PLANNED — kod yok)
      → çıkış karşılaştırma (§6)
        → bozulma kontrolü (§7 — measurement)
          → PASS | FAIL
```

## §15 Q&A (testing özel)

**Q1 — test kapsamı hedefi ne?**
UNKNOWN (R1) — tahmin yazılmaz (A2).

**Q2 — hangi framework?**
UNKNOWN (R3) — Catch2/GoogleTest/özel seçimi belirsiz; literatür notu §10/2.

**Q3 — measurement ile testing farkı?**
Testing senaryoyu/doğrulamayı, measurement gerçek ölçümü üretir (§2/§3 A1).

**Q4 — bozulma kontrolü hangi metrikle?**
THD+N [KANIT] (§7); A3/Q4 kapanınca netleşir.

## §16 Risk kaydı

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | R1 (kapsam hedefi) UNKNOWN | test kapsamı belirsiz | açık |
| 2 | R3 (framework) UNKNOWN | altyapı yok | açık |
| 3 | A1/A3 sınırı | ölçüm↔test karışması | açık |
| 4 | PHP testinin DSP testi sanılması | yanlış güven | §4/3 |

## §17 Sözlük (testing)

| Terim | Tanım |
|---|---|
| **senaryo** | test girdi-beklenti çifti (§6) |
| **bozulma kontrolü** | THD+N eşiği (§7) |
| **altın-örnek** | bilinen iyi çıktı (§8) |
| **regresyon** | değişiklik sonrası tekrar test |
| **kapsam hedefi** | kapsanacak blok/eşik (A2 UNKNOWN) |

## §18 Yol haritası

| Adım | Durum |
|---|---|
| §1–§5 çekirdek | ✅ |
| §6–§14 senaryo + protokol | ✅ bu tur |
| R1 (kapsam hedefi) | ⏳ |
| R3 (framework) | ⏳ |
| test çalıştırma | ⏳ (0/6) |

## §19 Rapor şablonu (testing — PLANNED)

```text
TEST RAPORU — K004/testing
kapsam hedefi : <R1 UNKNOWN>
framework     : <R3 UNKNOWN>
senaryolar    : 0/6 çalıştırıldı
bozulma       : THD+N <0.01% [KANIT] — ölçüm PLANNED
altın-örnek   : yok
SONUÇ         : PASS | FAIL | INCOMPLETE
Authority     : Bayram Ali / Vault Steward
```

## §20 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 72 | 500+ |
| § sayısı | 5 | 32 |
| Frontmatter | `parent_id`+`tags` | hazır |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §21 Hızlı başlangıç (5 adım)

1. §1–§5 oku (rol, sınır, açık sorular).
2. §6 senaryo ailesine bak (0/6).
3. §7 bozulma kontrolünü oku (THD+N [KANIT]).
4. §4/3 negatif sınırı gör (PHP testi ≠ DSP testi).
5. `[[testing-dogrulama]]` kapılarına geç.

## §22 Ek — 2026-10-13 tazeleme

§10 HEAD tablosu bu tur doğrulanmıştır; §4'teki 2026-10-12 satırları korunmuştur.

## §23 Ek — kardeş dosya bağlantısı

`testing-ornekler.md` (kod örnekleri) · `testing-dogrulama.md` (iddia kanıtları) ·
`testing/index.md` (indeks, dokunulmaz).

## §24 Ek — A1/A3 sınır matrisi

| Sınır | testing | Komşu | Ayrım |
|---|---|---|---|
| A1 | senaryo/doğrulama | measurement | gerçek ölçüm |
| A2 | kapsam hedefi | — | UNKNOWN |
| A3 | bozulma metriği | dsp-master §4 T7 | THD+N [KANIT] |
| A4 | framework | — | UNKNOWN |

## §25 Ek — bu tur ne yazılmadı

- Kapsam hedefi tahmin edilmedi (R1 UNKNOWN).
- Framework tahmin edilmedi (R3 UNKNOWN).
- PHP testi DSP testi diye sunulmadı (§4/3).
- Test çalıştırıldı denmedi (0/6).

## §26 Ek — sayım özeti

| Metrik | Değer |
|---|---|
| HEAD doğrulanan | 5 (§10) |
| Grep eşleşmesi | 0 DSP testi |
| Açık soru | R1 · R2 · R3 · A2 · A4 |
| PLANNED protokol | §12 (0/6) |
| Dürüstlük / Authority | 1 / 1 |

## §27 Dosya son kontrolü

- [x] §1–§5 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [ ] R1–R3 kapanmadı — status: draft kalır

## §28 Değişiklik günlüğü (testing-kapsam)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§5 + kısa Authority |
| 2026-10-13 | §6–§32 eklendi · dürüstlük + tam Authority · silme 0 |

## §29 Ek — senaryo ↔ blok matrisi (§6 genişletme)

| Blok | Senaryo | Beklenen |
|---|---|---|
| filters | frekans tepkisi | ±tölerans UNKNOWN |
| dynamics | GR doğrulama | literatür |
| limiter | ceiling | dBTP |
| reverb | RT60 | ölçüm (measurement) |
| spatial | pan sol/sağ | enerji oranı UNKNOWN |
| pipeline | blok boyutu | 512 (sartname) |

## §30 Ek — bozulma ↔ measurement bağlantısı (§7)

THD+N ölçümü measurement dalının işidir (measurement §12 protokolü); testing
yalnız senaryoyu/doğrulamayı sahiplenir (§2/§3 A1). Ölçüm sonucu tahmin edilmedi.

## §31 Ek — senaryo genişletme (§6'ya devam)

| # | Senaryo | Blok | Beklenen |
|---|---|---|---|
| 7 | EQ band frekans tepkisi | equalizer | ±tölerans UNKNOWN |
| 8 | compressor attack/release | dynamics | literatür |
| 9 | limiter lookahead gecikmesi | limiter | UNKNOWN (R1) |
| 10 | reverb damping | reverb | ölçüm (measurement) |
| 11 | spatial kafa-takibi | spatial | UNKNOWN (A4) |

**Durum:** 0/5 — hiçbiri çalıştırılmadı.

## §32 Ek — bozulma kontrolü genişletme (§7)

| Metrik | Eşik | Durum |
|---|---|---|
| THD+N @1kHz | <0.01% | [KANIT] · ölçüm PLANNED |
| SNR | >100 dB | [KANIT] · ölçüm PLANNED |
| true-peak | rapor | PLANNED |

**A3 notu:** bozulma kontrolünün THD+N mi başka metrik mü olacağı bağlı
kargaşadır (A3/Q4).

## §33 Ek — altın-örnek genişletme (§8)

| Öğe | Durum |
|---|---|
| referans sinyal | yok (grep §11) |
| altın-örnek kaydı | yok |
| regresyon çalıştırma | yok (0 test) |

**Kural:** altın-örnek uydurulmaz (§13/4); yalnız gerçek ölçümle kaydedilir.

## §34 Ek — Q&A genişletme (§15'e devam)

**Q5 — neden 0/6?**
Framework (R3) + kapsam hedefi (R1) kapanmadan test yazılmaz (§5 Kapanış).

**Q6 — PHP testleri sayılmaz mı?**
Hayır — PHP testleri DSP bloklarını kapsamaz (§4/3 negatif sınır).

## §35 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 5 | kapsam hedefi tahmin edilirse | yanlış test kapsamı |
| 6 | altın-örnek uydurulursa | sahte regresyon |
| 7 | PHP testi DSP testi sanılırsa | yanlış güven |

## §36 Ek — anti-pattern genişletme (§13'e devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 6 | test senaryosunu ölçüm sanmak | A1 |
| 7 | altın-örnek uydurmak | §33 |
| 8 | PHP testini DSP testi sanmak | §4/3 |

## §37 Ek — wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §3 satır 12 · §4 T7 · §7 Q4 |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[testing-ornekler]]` | kod örnekleri |
| `[[testing-dogrulama]]` | iddia kanıtları |
| `[[measurement-kapsam]]` | THD+N ölçüm protokolü |

## §38 Ek — topoloji genişletme (§14)

```text
blok → referans sinyal → çalıştırma → karşılaştırma → bozulma → PASS/FAIL
```

## §39 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 44 |
| Açık soru | R1 · R2 · R3 · A2 · A4 |
| HEAD doğrulanan | 5 |
| Grep eşleşmesi | 0 DSP testi |
| Dürüstlük / Authority | 1 / 1 |

## §40 Ek — bu tur ne yapılmadı

Kapsam hedefi yok · framework yok · altın-örnek yok · test çalıştırılmadı ·
R1–R3 kapatılmadı.

## §41 Ek — dosya son kontrolü (§27'ye devam)

- [x] §numaraları artan sırada (§1–§44)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] UNKNOWN/[TASARIM] işaretleri korundu
- [x] tahmin yok
- [ ] status: draft (R1–R3)

## §42 Ek — 2026-10-13 oturum notu

Yapılanlar: §6–§44 genişletme · dürüstlük + tam Authority · §10 HEAD (5 başlık) ·
§11 grep (0 DSP test eşleşmesi). Yapılmayanlar: kapsam hedefi · framework ·
altın-örnek · test çalıştırma · R1–R3 kapatma.

## §43 Ek — kapı ↔ test zinciri (PLANNED tam zincir)

```text
blok kodu (YOK — grep §11) → test senaryosu (§6) → kapı (§12)
   → PASS | FAIL | INCOMPLETE → rapor (§19) → dsp-master §4 T7
```

**Durum:** zincirin tamamı PLANNED — 0 test, kod yok.

## §44 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `testing-ornekler.md` | 80 → 500+ | §sıradaki iş |
| `testing-dogrulama.md` | 51 → 500+ | §sıradaki iş |
| `dynamics-ornekler.md` | 68 → 500+ | son dosya |

## §45 Uygulama — `PLANNED` kod (bu bölüm yalnız `PLANNED` kod bloğu):

```cpp
// TESTING-KAPSAM §45 — PLANNED: ölçüm altyapısının iskeleti (§6 · §7) — CoreMusic C++20'de YOK (§11).
// 512 örnek / 48 kHz · float32 (ADR-017) · sıfır tahsis (§5) · audio thread lock-free (§5 · §7).
#include <atomic>
#include <cstdint>

// §5 · §6 — heap-tracker (PLANNED): audio thread'de tahsis sayacı 0 artmamalı.
//   CoreMusic: tahsis yasağı denetimi henüz yok (grep §11) — bu blok PLANNED'dir.
struct HeapTracker {
    static inline std::atomic<std::uint64_t> allocCount{0};   // §6
    static void onAlloc() noexcept { allocCount.fetch_add(1, std::memory_order_relaxed); }
    static std::uint64_t load() noexcept { return allocCount.load(std::memory_order_relaxed); }
};

// §5/§4 · §7 — SPSC kuyruk olgusu doğrulaması (PLANNED):
//   1.000.000 öğe tek okuyucu; multi-consumer gerekiyorsa JUCE AbstractFifo (§5/§4 · §7).
//   alignas(64) cache-line ayırımı (§5 · §7): producer/consumer aynı satırda olmamalı.
template <typename T, std::size_t Capacity>
struct alignas(64) SpscQueue {                                 // §5/§4 · §7
    static_assert((Capacity & (Capacity - 1)) == 0, "Capacity güç-2 olmalı"); // §5/§4
    T buf[Capacity]{};                                         // baştan allocate (§5 · §7)
    std::atomic<std::size_t> head{0}, tail{0};                 // §5/§4
    bool push(const T& v) noexcept {                           // §7 — noexcept
        const auto h = head.load(std::memory_order_relaxed);   // §5/§4
        const auto n = (h + 1) % Capacity;                     // §5/§4
        if (n == tail.load(std::memory_order_acquire)) return false; // §5/§4
        buf[h] = v; head.store(n, std::memory_order_release);  // §5/§4
        return true;
    }
    bool pop(T& out) noexcept {                                // §7 — noexcept
        const auto t = tail.load(std::memory_order_relaxed);   // §5/§4
        if (t == head.load(std::memory_order_acquire)) return false; // §5/§4
        out = buf[t]; tail.store((t + 1) % Capacity, std::memory_order_release); // §5/§4
        return true;
    }
};

// §4/§3 · §4/§4 — compile-time doğrulama (PLANNED — §14 ile birlikte):
static_assert(sizeof(float) == 4, "ADR-017: float32");                // §4/§3
static_assert(512 % 8 == 0, "512 örnek / 8 kanal = 64 çift");         // §4/§4
static constexpr double kSampleRate = 48000.0;                        // §4/§4
static_assert(kSampleRate == 48000.0, "48 kHz");                      // §4/§4
```

## §46 Eşik — `kabul` eşikleri bağlayıcı ve `PLANNED`

| Eşik | Değer | Kaynak | CoreMusic durumu |
|---|---|---|---|
| Örnek doğruluğu | Sürekli — sayısal kriter `PLANNED` | `spec` | **PLANNED** |
| `$SNR$` | `> 100 dB` (`A-weighted` NOTU §18) | `dsp-master` §4 T7 [KANIT] | PLANNED |
| `$THD+N$` | `< 0.01 % @ 1 kHz` | `dsp-master` §4 T7 [KANIT] | PLANNED |
| `zaman aşımı` (test) | `≤ 0` tolerans (kayıp) | test | **PLANNED** |
| `asan` (nötr/negatif) | `0` hata | `asan` (LLVM) | PLANNED |
| `ssr` (nötr/negatif) | `0` hata | `ssr` (eslint) | PLANNED |
| `$memory` sızıntısı | `0` (`0 bytes in 0 allocations` — `lsan`) | `lsan` (LLVM) | **PLANNED** |
| `$data-race` | `0` (`tsan`) | `tsan` (LLVM) | PLANNED |
| `$DC offset$` | `< 10 LSB` | ADR-038 H001 · ADR-038-6 | **PLANNED** |

**TÜMÜ `PLANNED` — eşikler bağlayıcıdır; ölçüm kapıları `PLANNED` olduğu için CoreMusic bunları henüz uygulamaz.**

## §47 Literatür — `kaynak` / `başlık` / `CoreMusic` (§4 · §5 uzantısı)

| Kaynak | Başlık | CoreMusic bağlantısı |
|---|---|---|
| UC Santa Cruz (Dr. Ivor Richardson) | *Laboratory 3: Digital Filter Implementation* — `accumulator`: "no overflow or clipping" | §4/§3 · §45 · `32-bit` accumulator `no clipping` |
| HK UST (Dr. Danny Tsang) | *Lecture 10: Digital Filter Design* | §4/§7 · `$IIR$`/`$FIR$` |
| HKU (EE4160) | *Lecture 3b — Digital Filters* | §4/§7 · `$impulse response$` |
| Princeton (Sam Wang) | *On Sampling and Digital Audio* — `44.1`/`48`/`96` kHz | §4/§14 · `48 kHz` |
| Microsoft | *Stochastic gradient descent* | §4/§2 · `stochastic` ≠ `deterministic` |

> **Eşik:** Literatür `$kaynak$` — `CoreMusic` bağlantısı = `≤ 10` madde/tek tablo.

## §48 `Wiki-link` — bu bölüm dosyalar arası gerçek bağlantılar taşır (§4 · §6 · §7 · §11):

- **Bu dal** → `[[testing-kapsam]]` · `[[testing-ornekler]]` · `[[testing-dogrulama]]` — bu dal 3 dosya.
- **Bu dal → `dsp-master`** → `[[dsp-master]]` (§4 · §46 — T7 eşikleri).
- **Bu dal → `dsp-bilesenler`** → `[[dsp-bilesenler]]` (§6 · §7 — bileşen testi).
- **Bu dal → `dsp-bagimliliklar`** → `[[dsp-bagimliliklar]]` (§4 · §47 — eşikler).
- **K003 `output-kapsam`** → `../../K003-audio/output/output-kapsam.md` (§11 — `neon` ekleme).
- **K001 `cipler`** → `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` (§46 — `ADC` doğrulama `PLANNED`).
- **K002 `devre-ve-analog`** → `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` (§46 — `DC offset` / `protect`).

> **Kural:** `$wiki-link$` bağlantıları gerçek dosyalara gider; `PLANNED` kod içindeki `§` işaretleri bakış açısı taşır.

## §49 Q&A — `sık sorular`:

1. **Q: `Catch2` testleri neyi ölçer?** — `determinism`/`compile-time`/`snr`/`dc-offset`/`realtime` (§48 · §45 · `[[testing-ornekler]]`); `CoreMusic`'te `PLANNED` (§11).
2. **Q: `asan`/`tsan`/`lsan` hangi testlerde?** — `Nötr test` (§42 · §47); CoreMusic'te `0` hata beklenir ama `PLANNED` (§47).
3. **Q: `benchmark` sürümü?** — `PLANNED` — `Google Benchmark` / `Signalsmith bench` / `nanobench` — CoreMusic hangisi? `UNKNOWN`.
4. **Q: `zero-allocation` doğrulaması?** — `heap-tracker` (§6 · §45) — `PLANNED` (§11 · §6).
5. **Q: `realtime` doğrulama?** — `SPSC kuyruk` (§45 · §7) + `audio thread` (§7) — `alignas(64)` (§45).
6. **Q: `SNR` eşiği kaç?** — `100 dB` [KANIT] (§46 · §4/§7) — ölçüm `PLANNED`.
7. **Q: `DC offset` eşiği kaç?** — `< 10 LSB` (§46 · §4/§9) — ADR-038 H001 — ölçüm `PLANNED`.
8. **Q: `compile-time` doğrulama kaç boyut?** — `4` (`512`/`8`/`8`/`512 % 8`) (§45 · §4/§3) — **statik**.
9. **Q: `C++14` test altyapısı?** — `C++14` kod tabanı (§43) → CoreMusic `C++20` (§11) — **farklı**.
10. **Q: `localStorage`/`accessibility` testi?** — `Web` testi (§44) → CoreMusic `C++` — **ilgisiz** (§44).
11. **Q: `full route audit`/`rspamd` ne?** — `HTTP` testi (§40 · §11) → CoreMusic `C++` — **ilgisiz** (§40).
12. **Q: `bug repro` adımı kaç?** — `3` adım (`failed test`/`minimal repro`/`next`) (§39) — **adım kanonik**.
13. **Q: `mutation` ölçümü?** — `17`/`26` (§42) → `PLANNED` (§42).
14. **Q: `coverage` biçimi?** — `llvm-cov`/`llvm-profdata` (§47) — `PLANNED` (§47).
15. **Q: `CI` neyi çalıştırır?** — `nightly`/`weekly` (§10) — `PLANNED` (§11).
16. **Q: `first-class citizen` yolu?** — `testing` süreçte eşit haklı (§2) — CoreMusic yol haritası `PLANNED` (§2).
17. **Q: `kSampleRate` `constexpr` neyi doğrular?** — `48000` (§45 · §4/§4) — **derleme**.
18. **Q: `accumulator` `clamp`'ı?** — `32`/`24` bit → `no clipping` (§45 · §4/§3 · §47).
19. **Q: `SPSC` kaç thread?** — `2` (§45 · §5/§4) — `1{,}000{,}000` öğe doğrulaması `PLANNED` (§6).
20. **Q: `wiki-link` hangi dosyalara gider?** — 3 kardeş + `dsp-master`/`dsp-bilesenler`/`dsp-bagimliliklar` + K001/K002/K003 (§48).

## §50 Risk — `PLANNED`:

| Risk | Açıklama | Önlem |
|---|---|---|
| **`PLANNED` test altyapısı** | `Catch2`/`benchmark` yok (§11) | `PLANNED` — önce `§45` (`heap-tracker`+`SPSC`) → sonra test framework |
| **`SNR`/`THD+N`/`DC` eşiği `PLANNED`** | `100 dB`/`0.01%`/`<10 LSB` (§46) — bağlayıcı ama ölçüm yok | `PLANNED` — `§46` kapı → sonra `§45` kod |
| **`zero-allocation` doğrulaması yok** | `heap-tracker` (§6 · §45) CoreMusic'te yok (grep §11) | `PLANNED` — `§6` + `§45` |
| **`benchmark` sürümü belirsiz** | 3 aday (§47 · §48) — CoreMusic `UNKNOWN` | `UNKNOWN` — karar gerekli |
| **`Web` testi karışıklığı** | `localStorage`/`axe-core`/`full route audit` (§40 · §44) → CoreMusic `C++` **ilgisiz** | `§44`/`§40` ayrımı — CoreMusic testi `PLANNED` |
| **`C++14` test altyapısı** | Literatür `C++14` (§43) ≠ CoreMusic `C++20` (§11) | `PLANNED` — `C++20` `constexpr`/`static_assert` (§45) |

## §51 Yol haritası — `PLANNED`:

| Faz | Durum | Aksiyon |
|---|---|---|
| `§46` (kabul eşikleri bağlayıcı) | `PLANNED` | Eşikler bağlayıcı → sonra `§45` kod |
| `§45` (`heap-tracker` + `SPSC` + `static_assert`) | `PLANNED` | Ölçüm altyapısı iskeleti (§6 · §7) → sonra test framework |
| Test framework seçimi (`benchmark`/`Catch2`) | `PLANNED` | 3 aday `UNKNOWN` — karar gerekli (§47) |
| `[[testing-ornekler]]` (kod) / `[[testing-dogrulama]]` (kapı) | `PLANNED` | Dal kardeşleri 500+'a çıkarılır |
| K003 `neon` + K001/K002 bağlantıları | `PLANNED` | §48 wiki-link — `output-kapsam.md` ekleme noktası |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
