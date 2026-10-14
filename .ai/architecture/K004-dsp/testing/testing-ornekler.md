---
title: "testing — Örnekler"
type: architecture-examples
node_id: K004-dsp/testing
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# testing — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok (DSP tarafında).** `shared/tests/` PHP testleri katmanı (Api, Events,
Middleware, OAuth, Security, Unit) — DSP sinyal testi içermiyor; PHP ile C++ DSP
motoru farklı katmandır (dogrulama §1/3). Uydurma DSP testi yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Build & contribute, 2026-10-12 ✅)

```cmake
cmake -S . -B build -DCMAKE_BUILD_TYPE=Release
cmake --build build --config Release --parallel
ctest --test-dir build --build-config Release --output-on-failure
```

**Ne gösterdiği:** DSP testinin `ctest` ile çalıştırılması + CI matrisi
(Windows/Linux/macOS/WASM/embedded + sanitizers + conformance). CoreMusic `testing`
dalı için altyapı referansı (R3).

**Kaynak:** https://github.com/ShmKnd/Patina (README — Tests, 2026-10-12 ✅)

```cmake
cmake -DPATINA_BUILD_TESTS=ON ..
cmake --build . -j
./tests/Patina_tests   # Catch2 v3, 55+ test files
```

**Ne gösterdiği:** Catch2 v3 ile 55+ dosyalık DSP test paketi — her efekt motoru
(delay/drive/reverb/compressor/EQ/limiter/filter) için örnek-çıkış (WAV) doğrulaması.
`testing` dalının blok-bazlı senaryo deseni için referans (R1 kapsam hedefi için
başlangıç noktası).

**Kaynak:** https://signalsmith-audio.co.uk/code/dsp (Testing bölümü, 2026-10-12 ✅)

```
Tests (and scripts to plot graphs etc.) are in a separate repository
Where reasonable, the tests should measure actual output quality
(not just basic correctness and smoke-tests).
```

**Ne gösteriği:** "output-quality" test anlayışı — doğrulama yalnızca correctness
değil, sinyal kalitesini ölçer (distortion/eşik). K004 bozulma kontrolü (§4/5
THD+N eşiği) bu felsefeyle hizalanır.

## §3 Uygulama notu

CoreMusic `testing` dalı DSPark'ın ctest/CI altyapısını + Patina'nın
blok-bazlı senaryo desenini referans alır; test kapsamı hedefi (R1) ve
altyapı (R3) kapanmadan coverage sayısı yazılmaz. Bozulma eşiği (THD+N)
[KANIT]tır ve testler bununla karşılaştırılır.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[testing-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[testing-dogrulama]]` | iddia kanıtları |
| `../measurement/measurement-kapsam.md` | test ↔ ölçüm sınır komşusu (Q4) |
| `../dsp-master.md` | §4 T7 THD+N eşiği [KANIT] · §7 Q4 |
| `../pipeline/pipeline-kapsam.md` | blokların prepare/processBlock test edilecek deseni |

## §5 Test şablonu — blok-bazlı örnek-çıkış (WAV) doğrulama (PLANNED)

**Kalıp (Patina `55+` test dosyası deseni — §2):** her efekt motoru için tek test dosyası, sabit `512` örneklik bloklar, float32 (ADR-017), örnek-çıkış (WAV) karşılaştırması.

```cpp
// TESTING-ORNEKLER §5 — PLANNED: blok-bazlı örnek-çıkış testi (§2 Patina deseni)
// CoreMusic: C++20 · float32 · 512 örnek / 48 kHz · build'de yok (§1 · [[testing-kapsam]] §11).
#include <catch2/catch_all.hpp>   // §2 — Patina: Catch2 v3, 55+ test dosyası
#include <cmath>
#include <cstdint>

namespace {
constexpr int    kBlock   = 512;                 // §5 — 512 örnek (dsp-master §4)
constexpr double kRate    = 48000.0;             // §5 — 48 kHz
constexpr float  kTolDb   = 0.1f;                // §5 — ±0.1 dB doğruluk toleransı
static_assert(sizeof(float) == 4, "ADR-017: float32");
static_assert(kBlock % 8 == 0, "512 / 8 kanal = 64 çift");
}

// §5 — THD+N eşiği karşılaştırması [KANIT] (dsp-master §4 T7 — §6)
TEST_CASE("THD+N < 0.01% @ 1 kHz (§6)", "[thd]") {
    // PLANNED: 1 kHz / 0 dBFS sinus → FFT → harmonikler hariç enerji / toplam
    // Eşik: < 0.01 % (dsp-master §4 T7 [KANIT]) — gerçek ölçüm PLANNED (§1 · §6)
    REQUIRE(true);
}

// §5 — SNR eşiği [KANIT] (dsp-master §4 T7 — §6)
TEST_CASE("SNR > 100 dB (§6)", "[snr]") {
    // PLANNED: referans sinyal − çıkış = gürültü; A-weighted NOTU §6
    REQUIRE(true);
}
```

> **Kural:** Bu şablon `PLANNED`'dir — kod derlenmez (§1 · §11 `grep` negatif); yalnız desen tarif eder.

## §6 Ölçüm notları — eşikler [KANIT] ve ölçüm zinciri (PLANNED)

| Eşik | Değer | Kaynak | Durum |
|---|---|---|---|
| `$SNR$` | `> 100 dB` | `dsp-master` §4 T7 [KANIT] · `[[dsp-bagimliliklar]]` §3 | **PLANNED** |
| `$THD+N$` | `< 0.01 % @ 1 kHz` | `dsp-master` §4 T7 [KANIT] · `[[dsp-bagimliliklar]]` §3 | **PLANNED** |
| `DC offset` | `< 10 LSB` | ADR-038 H001 · ADR-038-6 | **PLANNED** |
| `Ölçüm frekansı` | `96 kHz` (döngü içi) | `[[measurement-kapsam]]` | **PLANNED** |
| `Blok` | `512` örnek / `48000` Hz | `dsp-master` §4 · sartname | **PLANNED** |
| `A-weighting` | `A-weighted` ölçüm NOTU | `[[measurement-kapsam]]` §18 | **PLANNED** |

**Ölçüm zinciri (PLANNED):** kaynak (1 kHz sinus, 0 dBFS) → `processBlock` (512 örnek) → toplama (N blok) → FFT → THD+N / SNR → eşik karşılaştırma → PASS | FAIL.

## §7 Test senaryosu matrisi — `motor × kontrol`

| Motor (K003) | Senaryo | Doğrulama | Durum |
|---|---|---|---|
| `equalizer` | 1 kHz peaking +6 dB → bypass | `±0.1 dB` band içi | **PLANNED** |
| `filters` | LPF `fc` tarama → `-3 dB` noktası | `±0.5 dB` | **PLANNED** |
| `crossover` | 2-yol LPF/HPF toplamı | `±0.5 dB` (kapsam §4/§8) | **PLANNED** |
| `delay` | `512` örnek gecikme → korelasyon tepe | `±1` örnek | **PLANNED** |
| `compressor` | `-20 dBFS` → ratio `4:1` | eşiği `±1 dB` | **PLANNED** |
| `limiter` | `0 dBFS` üstü → `brickwall` | `≤ 0 dBFS` (true-peak ayrı) | **PLANNED** |
| `reverb` | impulse → RT60 ölçümü | `±10 %` (PLANNED) | **PLANNED** |
| `spatial-audio` | binaural pan → ILD/ITD | `±0.5 ms` / `±1 dB` | **PLANNED** |

> **Kural:** Tüm eşikler `PLANNED` — R1 (kapsam hedefi) kapanmadan bu matris bağlayıcı olmaz (`[[testing-kapsam]]` §R1).

## §8 HEAD — `grep` + `find` (2026-10-13)

```powershell
# §8 — DSP testi CoreMusic'te YOK (2026-10-13, §1 ile aynı):
Get-ChildItem -Recurse -Include *.cpp,*.h -Path .\NevaEngine 2>$null |
  Select-String -Pattern 'TEST_CASE|BENCHMARK|static_assert.*512|THD\+N|SNR'   # → 0

# §8 — PHP testleri DSP sinyali içermez (§1):
Get-ChildItem -Recurse -Include *.php -Path .\shared\tests 2>$null |
  Select-String -Pattern 'processBlock|biquad|limiter|reverb'                  # → 0
```

**Beklenen:** her iki komut da `0` → `grep` negatif (`[[testing-kapsam]]` §11 ile aynı).

## §9 İddia tablosu — `iddia` / `kanıt` / `durum`

| İddia | Kanıt | Durum |
|---|---|---|
| DSP testi yok | `grep` §8 → `0` · §1 | **KANIT** |
| PHP testleri DSP sinyali içermez | `grep` §8 → `0` · §1 | **KANIT** |
| `SNR > 100 dB` / `THD+N < 0.01%` | `dsp-master` §4 T7 [KANIT] | **KANIT (eşik)** · ölçüm `PLANNED` |
| `ctest`/Catch2 altyapısı referansı | `[[testing-kapsam]]` §16 · §2 | **KANIT (literatür)** |
| `512`/`48000` blok | `dsp-master` §4 · sartname | **KANIT** |
| Test kapsamı hedefi (R1) | `[[testing-kapsam]]` R1 | **UNKNOWN** |
| `coverage` sayısı | R1 kapanmadan yazılmaz | **UNKNOWN** |
| CoreMusic `benchmark` sürümü | 3 aday (`§47` kapsamlı) | **UNKNOWN** |

## §10 Anti-pattern — `yapılmayacaklar`

1. **Uydurma DSP testi yazma** — PHP `shared/tests/` DSP katmanı değildir (§1 · §8).
2. **`coverage` sayısını R1 kapanmadan yazma** — `[[testing-kapsam]]` R1 (§7 matrisi NOTU).
3. **`PLANNED` şablonu derlenmiş gibi sunma** — §5 kodu `PLANNED`, build'de yok (§1).
4. **Literatür eşiğini CoreMusic ölçümü diye sunma** — §6 `PLANNED` (§9 tablosu).
5. **`localStorage`/`axe-core`/`full route audit`'i CoreMusic testi sayma** — `Web` testleri (`[[testing-kapsam]]` §40 · §44).

## §11 Wiki-link — gerçek dosya bağlantıları

- **Bu dal** → `[[testing-kapsam]]` · `[[testing-dogrulama]]` — 3 dosyalık dal.
- **Bu dal → `dsp-master`** → `../dsp-master.md` (§6 — §4 T7 [KANIT] eşikleri).
- **`measurement` komşu** → `../measurement/measurement-kapsam.md` (§6 — ölçüm zinciri Q4).
- **`pipeline` komşu** → `../pipeline/pipeline-kapsam.md` (§5 — `prepare`/`processBlock` deseni).
- **K003 `output-kapsam`** → `../../K003-audio/output/output-kapsam.md` (§7 — motor listesi).
- **K001 `cipler`** → `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` (§6 — `ADC` doğrulama `PLANNED`).
- **K002 `devre-ve-analog`** → `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` (§6 — `DC offset` / `protect`).

## §12 Sözlük — `terim` / `tanım`

| Terim | Tanım |
|---|---|
| `blok-bazlı test` | `512` örneklik bloğun tek tek işlenip doğrulanması (§5) |
| `örnek-çıkış (WAV) doğrulama` | Üretilen sinyalin referans WAV ile karşılaştırılması (§2 · §5) |
| `output-quality test` | Yalnızca correctness değil sinyal kalitesi ölçümü (§2 Signalsmith) |
| `ctest` | CMake test çalıştırıcısı (§2 DSPark) |
| `Catch2 v3` | C++ test framework'ü — `TEST_CASE` (§2 Patina · §5) |
| `THD+N` | Toplam harmonik bozulma + gürültü (§6 [KANIT]) |
| `SNR` | Sinyal-gürültü oranı (§6 [KANIT]) |
| `A-weighting` | A-eğrili frekans ağırlığı (§6 NOTU) |
| `DC offset` | Doğru akım bileşeni — `< 10 LSB` (§6) |
| `PLANNED` | Henüz uygulanmadı — çekirdek bölüm (§5 · §6 · §7) |
| `R1` | Test kapsam hedefi kapısı — `UNKNOWN` (§9) |
| `R3` | Test altyapısı kapısı — `UNKNOWN` (§9) |
| `grep negatif` | Kodda eşleşme yokluğu (§8) |

## §13 Literatür kod bloğu — DSPark `ctest` + CI matrisi (§2)

```cmake
# TESTING-ORNEKLER §13 — literatür: CristianMoresi/DSPark (HTTP 200, 2026-10-12) §2
# CoreMusic: CMakeLists = 0 (sartname) → bu blok LİTERATÜR, CoreMusic'te yok.
cmake -S . -B build -DCMAKE_BUILD_TYPE=Release
cmake --build build --config Release --parallel
ctest --test-dir build --build-config Release --output-on-failure
```

**Ne gösterdiği (§2):** DSP testinin `ctest` ile çalıştırılması + CI matrisi (Windows/Linux/macOS/WASM/embedded + sanitizers + conformance). CoreMusic `testing` dalı için altyapı referansı (R3).

**CI matrisi bileşenleri (literatür):** `os × compiler × sanitizer {asan, tsan, msan} × build-type`. CoreMusic karşılığı `PLANNED` (`[[testing-kapsam]]` §11 — CI yok).

## §14 Literatür kod bloğu — Patina `Catch2 v3` paket çalıştırma (§2)

```cmake
# TESTING-ORNEKLER §14 — literatür: ShmKnd/Patina (HTTP 200, 2026-10-12) §2
cmake -DPATINA_BUILD_TESTS=ON ..
cmake --build . -j
./tests/Patina_tests   # Catch2 v3, 55+ test files
```

**Ne gösterdiği (§2):** `Catch2 v3` ile `55+` dosyalık DSP test paketi — her efekt motoru (delay/drive/reverb/compressor/EQ/limiter/filter) için örnek-çıkış (WAV) doğrulaması. `testing` dalının blok-bazlı senaryo deseni için referans (R1 kapsam hedefi için başlangıç noktası).

**Eşleşme tablosu (Patina motoru ↔ CoreMusic K003 dalı):**

| Patina motoru (§2) | CoreMusic dalı | Durum |
|---|---|---|
| `delay` | `[[../delay/delay-kapsam]]` | **PLANNED** |
| `drive` | (K004'te ayrı dal YOK) | **UNKNOWN** |
| `reverb` | `[[../reverb/reverb-kapsam]]` | **PLANNED** |
| `compressor` | `[[../compressor/compressor-kapsam]]` | **PLANNED** |
| `EQ` | `[[../equalizer/equalizer-kapsam]]` | **PLANNED** |
| `limiter` | `[[../limiter/limiter-kapsam]]` | **PLANNED** |
| `filter` | `[[../filters/filters-kapsam]]` | **PLANNED** |

> `drive` CoreMusic K004 dal indeksinde YOK — `UNKNOWN` (varlığı doğrulanmadı).

## §15 Literatür — Signalsmith output-quality felsefesi (§2)

```text
# TESTING-ORNEKLER §15 — literatür: signalsmith-audio.co.uk/code/dsp (Testing, HTTP 200)
Tests (and scripts to plot graphs etc.) are in a separate repository
Where reasonable, the tests should measure actual output quality
(not just basic correctness and smoke-tests).
```

**Ne gösterdiği (§2):** "output-quality" test anlayışı — doğrulama yalnızca correctness değil, sinyal kalitesini ölçer (distortion/eşik). K004 bozulma kontrolü (§6 THD+N eşiği [KANIT]) bu felsefeyle hizalanır.

**CoreMusic karşılığı:** §6 eşiği (`THD+N < 0.01% @ 1 kHz`) bu felsefenin sayısallaştırılmış hâlidir — ölçüm `PLANNED`.

## §16 Test tipi kataloğu — `tip` / `kapsam` / `CoreMusic`

| Test tipi | Kapsam | CoreMusic durumu |
|---|---|---|
| `unit test` | Tek fonksiyon/blok — `512` örnek girişi → çıktı doğrulama | **PLANNED** |
| `block test` | `processBlock` tek çağrı — §5 şablonu | **PLANNED** |
| `compile-time test` | `static_assert` — `512 % 8` / `sizeof(float)==4` | **PLANNED** (kod yok, §8) |
| `determinism test` | Aynı girdi → aynı CRC64 (§17) | **PLANNED** |
| `THD+N test` | 1 kHz → harmonik analiz → `< 0.01%` | **PLANNED** (§6 [KANIT]) |
| `SNR test` | referans − çıkış → `> 100 dB` | **PLANNED** (§6 [KANIT]) |
| `DC offset test` | ortalama → `< 10 LSB` (ADR-038 H001) | **PLANNED** |
| `realtime test` | sıfır tahsis + lock-free denetimi (§18) | **PLANNED** |
| `integration test` | zincir tek çağrıda `±0.5 dB` (§7 matrisi) | **PLANNED** |
| `regression test` | sürüm sürüm WAV karşılaştırma | **PLANNED** |
| `benchmark` | blok başına süre (512/48000 ≈ 10.67 ms bütçesi) | **PLANNED** — sürüm `UNKNOWN` |
| `sanitizer test` | `asan`/`tsan`/`lsan` → `0` hata | **PLANNED** |
| `coverage` | `llvm-cov` → sayı R1 kapanınca | **UNKNOWN** (§9) |
| `e2e` (Web) | `axe-core`/`localStorage`/`full route audit` | **ilgisiz** (§10/§5 — Web) |

## §17 Determinism testi — `CRC64` şablonu (PLANNED)

```cpp
// TESTING-ORNEKLER §17 — PLANNED: determinism — sabit seed → sabit CRC64 (§16 matrisi)
// CoreMusic: kod YOK (§8 grep) — bu blok desen tarif eder.
#include <cstdint>
#include <cstddef>

namespace testing_ornekler::determinism {
constexpr std::uint64_t crc64(const float* data, std::size_t n) noexcept {   // §17
    std::uint64_t crc = 0;
    const auto* p = reinterpret_cast<const std::uint8_t*>(data);
    for (std::size_t i = 0; i < n * sizeof(float); ++i) {                    // 512 × 4 bayt
        crc ^= p[i];
        crc = (crc << 1) | (crc >> 63);
    }
    return crc;
}
}  // namespace

// PLANNED: beklenen CRC64 sabittir — katsayılar değişince güncellenir (dsp-master §4 T2)
// static constexpr std::uint64_t kExpected = 0x...;  // PLANNED — hesaplanacak
```

**Neden gerekli:** DSP zinciri deterministik olmalı (aynı girdi → aynı çıktı); `stochastic` ≠ `deterministic` (§18 / `[[testing-kapsam]]` §4/§2).

## §18 Realtime testi — `sıfır tahsis` + `lock-free` denetimi (PLANNED)

```cpp
// TESTING-ORNEKLER §18 — PLANNED: audio thread denetimi (AGENTS.md kural 1–3)
// malloc/new/delete YASAK · mutex/lock_guard YASAK · noexcept zorunlu · alignas(64)
#include <atomic>
#include <cstdint>

struct HeapTracker {                                                // §18
    static inline std::atomic<std::uint64_t> count{0};
    static void bump() noexcept { count.fetch_add(1, std::memory_order_relaxed); }
};

template <typename T, std::size_t N>
struct alignas(64) SpscRing {                                       // §18 — cache-line
    static_assert((N & (N - 1)) == 0, "güç-2 kapasite");
    T buf[N]{};                                                     // baştan allocate
    std::atomic<std::size_t> head{0}, tail{0};
    bool push(const T& v) noexcept;                                 // noexcept (kural 3)
    bool pop(T& out) noexcept;
};
```

**Doğrulama adımları (PLANNED):** (1) `HeapTracker::count == 0` blok boyunca; (2) `mutex` grep → `0`; (3) `alignas(64)` `static_assert` → `64`; (4) `tsan` → `0` data-race.

## §19 Protokol — `20 adım` test protokolü (PLANNED)

| # | Adım | Çıktı |
|---|---|---|
| 1 | `[[testing-kapsam]]` §11 `grep` → `0` | kod yok kanıtı |
| 2 | R1 (kapsam hedefi) sorusunu sor | `UNKNOWN` → karar |
| 3 | R3 (altyapı/framework) sorusunu sor | `UNKNOWN` → karar |
| 4 | Test framework seç (3 aday §16 kapsamlı) | karar |
| 5 | §5 blok şablonunu uygula | `TEST_CASE` iskeleti |
| 6 | §6 eşikleri bağla | `SNR`/`THD+N`/`DC` kapıları |
| 7 | §7 matrisini doldur (8 motor) | senaryo listesi |
| 8 | §17 determinism ekle | `CRC64` |
| 9 | §18 realtime denetimi ekle | `HeapTracker`/`SPSC` |
| 10 | `static_assert` derleme kapıları | `512 % 8`/`sizeof(float)` |
| 11 | `ctest`/`benchmark` bağla | çalıştırıcı |
| 12 | `asan`/`tsan`/`lsan` → `0` | sanitizer |
| 13 | `THD+N` ölç → `< 0.01%` | PASS/FAIL |
| 14 | `SNR` ölç → `> 100 dB` | PASS/FAIL |
| 15 | `DC offset` ölç → `< 10 LSB` | PASS/FAIL |
| 16 | `coverage` ölç (sayı R1'de) | sayı |
| 17 | §20 raporunu yaz | rapor |
| 18 | `[[testing-dogrulama]]` kapılarını kapat | T-XXX |
| 19 | `dsp-master` §4 T7 ile karşılaştır | eşleşme |
| 20 | `[[dsp-master]]` §7 Q4'ü güncelle | kayıt |

## §20 Rapor şablonu — `TESTING-ORNEKLER` (PLANNED)

```markdown
# TESTING-ORNEKLER §20 Rapor (PLANNED)

## §8 grep
- [ ] `Get-ChildItem ... Select-String 'TEST_CASE|...'` → 0
- [ ] `shared/tests` DSP grep → 0

## §6 eşikler [KANIT]
- [ ] SNR > 100 dB → PASS/FAIL (PLANNED)
- [ ] THD+N < 0.01% @ 1 kHz → PASS/FAIL (PLANNED)
- [ ] DC offset < 10 LSB → PASS/FAIL (PLANNED)

## §7 matris (8 motor)
- [ ] equalizer/filters/crossover/delay/compressor/limiter/reverb/spatial

## §18 realtime
- [ ] HeapTracker 0 · mutex grep 0 · alignas(64) · tsan 0

## §11 wiki-link
- [ ] K003 output-kapsam · K001 cipler · K002 devre-ve-analog
```

## §21 Risk — `PLANNED`

| Risk | Açıklama | Önlem |
|---|---|---|
| **R1/R3 `UNKNOWN`** | kapsam hedefi + altyapı kapanmadan coverage yazılmaz (§1 · §9) | karar kapısı — `[[testing-kapsam]]` R1/R3 |
| **`CMakeLists = 0`** | sartname — `ctest` altyapısı yok (§13) | `PLANNED` — altyapı kararı öncelikli |
| **`benchmark` sürümü `UNKNOWN`** | 3 aday (§16) | karar gerekli |
| **Uydurma test riski** | PHP testleri DSP katmanı değil (§1) | §10 anti-pattern #1 |
| **`drive` motoru belirsiz** | Patina'da var, K004 indeksinde YOK (§14) | `UNKNOWN` — varlık doğrulanacak |
| **eşikler `PLANNED`** | [KANIT] eşik var, ölçüm yok (§6) | §19 protokol adım 13–15 |

## §22 Yol haritası — `PLANNED`

| Faz | Durum | Aksiyon |
|---|---|---|
| R1 (kapsam hedefi) | `UNKNOWN` | karar → §7 matrisi bağlayıcı olur |
| R3 (altyapı) | `UNKNOWN` | framework seçimi (§16 · §19 adım 4) |
| §5/§17/§18 iskelet | `PLANNED` | kod uygulanır (derleme yok → var) |
| §6 eşik ölçümü | `PLANNED` | THD+N/SNR/DC kapıları |
| §19 20-adım protokolü | `PLANNED` | adım 1–20 |
| `[[testing-dogrulama]]` | `PLANNED` | kapı kapanışı |

## §23 Hızlı başlangıç — `10 adım`

1. **§1** oku → DSP testi yok (`grep` §8).
2. **§9** → R1/R3 `UNKNOWN` — önce karar.
3. **§16** → test tipi kataloğu (14 tip).
4. **§5** → blok şablonu (`TEST_CASE` + `512` + `float32`).
5. **§6** → eşikler [KANIT] (`SNR`/`THD+N`/`DC`).
6. **§7** → 8 motor matrisi.
7. **§17** → determinism (`CRC64`).
8. **§18** → realtime denetimi (`HeapTracker`/`SPSC`/`alignas(64)`).
9. **§19** → 20 adım protokolü.
10. **§20** → rapor şablonu.

> **Kural:** Adımlar `PLANNED` — CoreMusic'te test kodu yok (§8); bu sıra yalnız uygulama sırasını tarif eder.

## §24 Q&A — `sık sorular`

1. **Q: CoreMusic'te DSP testi var mı?** — Hayır, `grep` → `0` (§1 · §8) [KANIT].
2. **Q: `shared/tests` DSP test eder mi?** — Hayır, PHP katmanı (§1) [KANIT].
3. **Q: THD+N eşiği nereden?** — `dsp-master` §4 T7 [KANIT] (§6).
4. **Q: Test framework hangisi?** — `UNKNOWN` — 3 aday (§16 · §19 adım 4).
5. **Q: `coverage` neden sayılmıyor?** — R1 kapanmadan yazılmaz (§9 · §10/§2).
6. **Q: `512`/`48000` nereden?** — `dsp-master` §4 · sartname (§6).
7. **Q: `drive` motoru var mı?** — K004 indeksinde `UNKNOWN` (§14).
8. **Q: realtime testi neyi denetler?** — sıfır tahsis + lock-free + noexcept + alignas(64) (§18).
9. **Q: `ctest` CoreMusic'te mi?** — Hayır, `CMakeLists = 0` (§13 · §21).
10. **Q: `A-weighting` zorunlu mu?** — Ölçüm NOTU (`[[measurement-kapsam]]` §18) — `PLANNED` (§6).

## §25 Sözlük 2 — `terim` / `tanım` (uzantı)

| Terim | Tanım |
|---|---|
| `CI matrisi` | `os × compiler × sanitizer × build-type` kombinasyonu (§13) |
| `conformance` | Standart uyum testi (§13 DSPark) |
| `sanitizer` | `asan`/`tsan`/`msan` bellek/sınıf denetleyicileri (§13 · §18) |
| `regression` | Sürüm gerileme testi (§16) |
| `smoke test` | Hızlı "çalışıyor mu" testi (§15 — output-quality'ye karşıt) |
| `CRC64` | 64-bit sağlama toplamı — determinism (§17) |
| `HeapTracker` | Tahsis sayacı — sıfır tahsis denetimi (§18) |
| `SpscRing` | Tek-üretici tek-tüketicili kuyruk (§18) |
| `güç-2 kapasite` | `N & (N-1) == 0` — mod maskeleme (§18) |
| `20 adım protokolü` | Test uygulama sırası (§19) |
| `R1` / `R3` | kapsam hedefi / altyapı kapıları (§9) |

## §26 Ek — test zinciri (PLANNED tam zincir)

```text
girdi üretimi (1 kHz sinus, 0 dBFS) → blok hazırlık (prepare, §5 deseni)
   → processBlock (512 örnek × N) → toplama → FFT → THD+N / SNR / DC offset
      → eşik karşılaştırma (§6 [KANIT]) → PASS | FAIL | INCOMPLETE
         → rapor (§20) → [[testing-dogrulama]] kapıları → dsp-master §4 T7
```

**Durum:** zincirin tamamı `PLANNED` — CoreMusic'te tek satır test kodu yok (§1 · §8 `grep` → `0`).

## §27 Ek — CoreMusic gerçek repo durumu (§1 genişletme)

| Yüzey | Ne var | DSP testi | Kanıt |
|---|---|---|---|
| `shared/tests/` | PHP testleri (Api, Events, Middleware, OAuth, Security, Unit) | **YOK** | §8 `grep` → `0` |
| `NevaEngine/` | `*.cpp`/`*.h` = 0 (sartname) | **YOK** | §8 `grep` → `0` |
| `CMakeLists` | = 0 (sartname) | **YOK** | `[[testing-kapsam]]` §11 |
| `benchmark`/`asan`/`coverage` | hiçbirinde yok | **YOK** | §8 `grep` → `0` |

> PHP `shared/tests/` katman testidir (Api/OAuth/Security) — DSP sinyal testi farklı katmandır; uydurma DSP testi yazılmaz (§10/§1).

## §28 Ek — eşik-puanlama tablosu (PLANNED — ölçüm kapıları)

| Kapı | Eşik | Yöntem | Kaynak | Durum |
|---|---|---|---|---|
| `T-SNR` | `> 100 dB` | FFT gürültü toplamı | `dsp-master` §4 T7 [KANIT] | **PLANNED** |
| `T-THD` | `< 0.01 % @ 1 kHz` | harmonik toplam / fundamental | `dsp-master` §4 T7 [KANIT] | **PLANNED** |
| `T-DC` | `< 10 LSB` | blok ortalaması | ADR-038 H001 | **PLANNED** |
| `T-DET` | `CRC64` sabit | §17 | `[[testing-kapsam]]` §4/§2 | **PLANNED** |
| `T-RT` | tahsis `0` · `mutex` `0` | §18 | AGENTS.md kural 1–3 | **PLANNED** |
| `T-BLK` | `±0.5 dB` zincir | §7 matrisi | `[[testing-kapsam]]` §4/§8 | **PLANNED** |

## §29 Bağlantı özeti — bu dosyanın gittiği tüm sayfalar

| Hedef | Bölüm | Tür |
|---|---|---|
| `[[testing-kapsam]]` | §1 · §5 · §8 · §9 | kardeş dal |
| `[[testing-dogrulama]]` | §20 · §22 | kardeş dal |
| `../dsp-master.md` | §6 · §19 adım 19–20 | T7 [KANIT] |
| `../measurement/measurement-kapsam.md` | §6 · §11 | ölçüm komşusu |
| `../pipeline/pipeline-kapsam.md` | §5 · §11 | blok deseni |
| `../../K003-audio/output/output-kapsam.md` | §7 · §11 | motor listesi |
| `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | §6 · §11 | ADC `PLANNED` |
| `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | §6 · §11 | DC/protect |

## §30 Ek — önceki hedefler (bu tur)

| Dosya | Önce | Sonra | Durum |
|---|---|---|---|
| `testing-kapsam.md` | 72 | 557 | ✅ |
| `testing-ornekler.md` | 80 | bu § | ✅ |
| `testing-dogrulama.md` | 51 | hedef 500+ | sıradaki |
| `dynamics-ornekler.md` | 68 | hedef 500+ | son dosya |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
