---
title: "filters — Örnekler"
type: architecture-examples
node_id: K004-dsp/filters
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# filters — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında alçak/yüksek/bant geçiren filtre çekirdeği,
biquad veya katsayı hesabı bulunmamaktadır (grep taraması negatif — dogrulama §1/3).
Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Filters and equalization | Biquad, StateVariableFilter, LadderFilter, FilterEngine, CrossoverFilter
```

**Ne gösterdiği:** hazır filtre çekirdekleri (biquad temel; SVF/ladder daha zengin topoloji).
CoreMusic C++ motoru bu sınıflardan birini temel alabilir veya sıfırdan biquad yazabilir.

**Kaynak:** https://github.com/Signalsmith-Audio/dsp (repo dosya listesi, 2026-10-12 ✅)

```
filters.h  delay.h  envelopes.h  fft.h  mix.h  rates.h  curves.h
```

**Ne gösteriği:** `filters.h` başlığı — MIT, C++11 header-only; hafif filtre referansı.

**Kaynak:** https://github.com/ShmKnd/Patina (Circuit Modules, 2026-10-12 ✅)

```
OtaSKFilter        — OTA Sallen-Key, TPT SVF çözücü ile stabilize
LadderFilter       — 4-pole transistor ladder, ZDF/TPT
AllPassFilter      — cascadable 1st-order TPT all-pass
```

**Ne gösterdiği:** analog-modeling filtre devreleri; `filters` dalının topolojik
referansı (zaman-variant/minimum-phase seçimini besler).

## §3 Uygulama notu

Filtre tipi kararı (R1) verilmeden kütüphane bağımlılığı kilitlenmez. Mevcut adayların
hepsi MIT (Signalsmith, DSPark, Patina) → lisans riski düşük; sürüm/entegrasyon
dsp-bagimliliklar §5.1 kapısından geçer.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[filters-kapsam]]` | sınır ve açık sorular |
| `[[filters-dogrulama]]` | iddia kanıtları |
| `../equalizer/equalizer-kapsam.md` | çekirdek ↔ bant seviyesi sınırı (R2) |
| `../crossover/crossover-kapsam.md` | filtre malzemesi ↔ dağıtım sınırı (R3) |

---

## §5 Literatür kod bloğu A — RBJ katsayı üretici (PLANNED — repo'da YOK)

> **Etiket:** [PLANNED] — bu kod CoreMusic deposunda **bulunmaz** (grep negatif,
> dogrulama §12). Kaynak: RBJ Audio EQ Cookbook (W3C iş notu, HEAD 200 ✅).
> Kanıt olarak kullanılamaz; yalnız tasarım referansıdır.

```cpp
// K004-dsp filters — TASARIM ESKİZİ (PLANNED, kanıt değil)
// Kaynak: w3.org/2011/audio/audio-eq-cookbook.html (HEAD 200, 2026-10-13)
#include <cmath>

enum class FilterType { Lowpass, Highpass, Bandpass, Notch, Allpass, Peaking };

struct BiquadCoeffs { float b0, b1, b2, a1, a2; };   // a0 = 1 normalize

// prepare() anında çağrılır — audio thread'de asla (dogrulama §7/1)
BiquadCoeffs makeRBJ(FilterType t, float fs, float f0, float q,
                     float gainDb = 0.0f) noexcept {
    const float w0    = 2.0f * 3.14159265358979f * f0 / fs;
    const float cw0   = std::cos(w0);
    const float sw0   = std::sin(w0);
    const float alpha = sw0 / (2.0f * q);
    const float A     = std::pow(10.0f, gainDb / 40.0f);

    float b0 = 1.0f, b1 = 0.0f, b2 = 0.0f, a0 = 1.0f, a1 = 0.0f, a2 = 0.0f;
    switch (t) {
        case FilterType::Lowpass:
            b0 = (1.0f - cw0) * 0.5f; b1 = 1.0f - cw0; b2 = b0;
            a0 = 1.0f + alpha; a1 = -2.0f * cw0; a2 = 1.0f - alpha;
            break;
        case FilterType::Highpass:
            b0 = (1.0f + cw0) * 0.5f; b1 = -(1.0f + cw0); b2 = b0;
            a0 = 1.0f + alpha; a1 = -2.0f * cw0; a2 = 1.0f - alpha;
            break;
        case FilterType::Bandpass:              // constant 0 dB peak gain
            b0 = alpha; b1 = 0.0f; b2 = -alpha;
            a0 = 1.0f + alpha; a1 = -2.0f * cw0; a2 = 1.0f - alpha;
            break;
        case FilterType::Notch:
            b0 = 1.0f; b1 = -2.0f * cw0; b2 = 1.0f;
            a0 = 1.0f + alpha; a1 = -2.0f * cw0; a2 = 1.0f - alpha;
            break;
        case FilterType::Allpass:
            b0 = 1.0f - alpha; b1 = -2.0f * cw0; b2 = 1.0f + alpha;
            a0 = 1.0f + alpha; a1 = -2.0f * cw0; a2 = 1.0f - alpha;
            break;
        case FilterType::Peaking:
            b0 = 1.0f + alpha * A; b1 = -2.0f * cw0; b2 = 1.0f - alpha * A;
            a0 = 1.0f + alpha / A; a1 = -2.0f * cw0; a2 = 1.0f - alpha / A;
            break;
    }
    return { b0 / a0, b1 / a0, b2 / a0, a1 / a0, a2 / a0 };
}
```

**Doğrulama kapısı:** üretim kodu bu bloktan türerse `filters-dogrulama` §13'ün
20 adımı işletilir (SNR > 100 dB · THD+N < 0.01% — dsp-master §4 T7).

## §6 Literatür kod bloğu B — Direct Form I işlek döngüsü (PLANNED)

> **Etiket:** [PLANNED] — tahsis yok, lock yok, noexcept (AGENTS.md §1).
> Kaynak formülü: JOS filters (ccrma.stanford.edu, HEAD 200 ✅).

```cpp
struct BiquadStateDF1 { float x1 = 0.f, x2 = 0.f, y1 = 0.f, y2 = 0.f; };

// 512 örnek / 48 kHz blok — sartname (ölçüm PLANNED)
void processDF1(const BiquadCoeffs& c, BiquadStateDF1& s,
                float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float x = buf[i];
        const float y = c.b0 * x + c.b1 * s.x1 + c.b2 * s.x2
                      -        c.a1 * s.y1 -        c.a2 * s.y2;
        s.x2 = s.x1; s.x1 = x;
        s.y2 = s.y1; s.y1 = y;
        buf[i] = y;                       // NaN/Inf kapısı: §6/10 testi
    }
}
```

**Not (float32 — dogrulama §22.2):** DF1 durum değişkenleri yüksek sinyal
seviyesinde `y1/y2` taşar mı; TDF2 alternatifi §7'de.

## §7 Literatür kod bloğu C — Transposed Direct Form II (PLANNED)

```cpp
struct BiquadStateTDF2 { float z1 = 0.f, z2 = 0.f; };   // alignas(64) önerilir

void processTDF2(const BiquadCoeffs& c, BiquadStateTDF2& s,
                 float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float y = c.b0 * buf[i] + s.z1;   // doğrusal-olmayan yayılım yok
        s.z1 = c.b1 * buf[i] - c.a1 * y + s.z2;
        s.z2 = c.b2 * buf[i] - c.a2 * y;
        buf[i] = y;
    }
}
```

**Seçim kapısı:** DF1 mi TDF2 mi — **UNKNOWN** (R1 topoloji kararıyla birlikte
değerlendirilir; kanıt: dogrulama §1/9).

## §8 Literatür kod bloğu D — State Variable Filter işlek döngüsü (PLANNED)

> **Etiket:** [PLANNED] — Papiero/Chamberlin topolojisi; DSPark `StateVariableFilter`
> (§2) sınıfına referans. Rezonans/Q davranışı biquad'tan farklıdır.

```cpp
struct SVFState { float ic1eq = 0.f, ic2eq = 0.f; };    // TPT integrator state

void processSVF(float g, float k, SVFState& s,
                float* buf, int n) noexcept {            // g = tan(pi*fc/fs)
    const float a1 = 1.0f / (1.0f + g * (g + k));
    const float a2 = g * a1;
    const float a3 = g * a2;
    for (int i = 0; i < n; ++i) {
        const float v3 = buf[i] - s.ic2eq;
        const float v1 = a1 * s.ic1eq + a2 * v3;
        const float v2 = s.ic2eq + a2 * s.ic1eq + a3 * v3;
        s.ic1eq = 2.0f * v1 - s.ic1eq;
        s.ic2eq = 2.0f * v2 - s.ic2eq;
        buf[i] = v2;                                   // lowpass çıkışı
    }
}
```

**Uyarı:** `g = tan(π·fc/fs)` içindeki `tan` prepare() dışına yazılmaz
(dogrulama §7/1).

## §9 Literatür kod bloğu E — Linkwitz-Riley crossover kademesi (PLANNED)

> **Etiket:** [PLANNED] — LR = iki aynı-sıra Butterworth cascade (literatür:
> Wikipedia Linkwitz–Riley_filter, HEAD 200 ✅). Hangi sıranın kullanılacağı
> **UNKNOWN** (R1).

```cpp
// LR4 = 4. sıra = 2× (2. sıra Butterworth cascade, Q = 1/√2)
// Bu blok yalnız taslaktır; crossover dalı (../crossover/) ile sınır Q3/R3.
struct LR4 {
    BiquadCoeffs lowA, lowB, highA, highB;
    BiquadStateTDF2 sLA, sLB, sHA, sHB;

    void prepare(float fs, float fc) noexcept {          // audio thread DIŞI
        constexpr float Q_BW2 = 0.70710678f;             // 1/√2 (literatür)
        const auto lp = makeRBJ(FilterType::Lowpass,  fs, fc, Q_BW2);
        const auto hp = makeRBJ(FilterType::Highpass, fs, fc, Q_BW2);
        lowA = lowB = lp;                                // iki kademe aynı
        highA = highB = hp;
    }

    void processSplit(float* lo, float* hi, int n) noexcept {
        processTDF2(lowA,  sLA, lo, n);
        processTDF2(lowB,  sLB, lo, n);
        processTDF2(highA, sHA, hi, n);
        processTDF2(highB, sHB, hi, n);
    }
};
```

**Doğrulama:** `filters-dogrulama` §6/5 — LR2 L+R toplamı düz genlik vermelidir.

## §10 Literatür kod bloğu F — filtre zinciri örneği (PLANNED)

> **Etiket:** [PLANNED] — zincir sırası (DC block → EQ → filtre → limiter) bir
> **tasarım önerisidir**; pipeline Q3 kapısı **UNKNOWN**.

```cpp
// Zincir şeması — SIRASI TARTIŞMALI (pipeline Q3), kanıt değildir
struct FilterChain {
    // 1) DC-blocking (highpass) — K001 donanım kuralı >0.5V relay ayrı katman
    // 2) parametrik EQ (eş dal)  — çekirdek sahipliği R2 (equalizer § sınırı)
    // 3) bant filtreleri         — bu dal
    // 4) limiter                 — dynamics dalı
    BiquadCoeffs stages[8];                  // sabit kapasite — tahsis yok
    BiquadStateTDF2 state[8];
    int count = 0;

    void prepare() noexcept { /* katsayıları burada hesapla (§5) */ }

    void process(float* buf, int n) noexcept {          // noexcept sözleşme
        for (int s = 0; s < count; ++s)
            processTDF2(stages[s], state[s], buf, n);
    }
};
```

**Yasak:** `stages[8]` çalışma anında `new`/`push_back` ile büyüyemez (sıfır
tahsis kuralı).

## §11 Test kodu şablonu — Catch2 (PLANNED, repo'da test YOK)

> **Etiket:** [PLANNED] — `filters` dalında test dosyası yoktur; şablon yalnız
> §6 test senaryolarını (doğrulama §6) kod diline çevirir.

```cpp
// tests/filters/test_biquad.cpp — ŞABLON (PLANNED, kanıt değil)
#include <catch2/catch_all.hpp>
#include "filters.hpp"

TEST_CASE("lowpass -3 dB @ f0 (RBJ)", "[filters][dsp]") {
    const float fs = 48000.0f, f0 = 1000.0f, q = 0.70710678f;
    auto c = makeRBJ(FilterType::Lowpass, fs, f0, q);
    // |H(e^jw0)| beklenen ≈ 0.7071 → doğrulama §22.3 worked example
    const float mag = biquadMagnitude(c, f0, fs);
    REQUIRE(mag == Catch::Approx(0.7071f).margin(0.01f));
}

TEST_CASE("kutuplar kararlı", "[filters][dsp]") {
    auto c = makeRBJ(FilterType::Lowpass, 48000.0f, 1000.0f, 0.70710678f);
    // |z| < 1 koşulu: a2 < 1 && a2 > -1 && ... (2. sıra disk koşulu)
    REQUIRE(std::abs(c.a2) < 1.0f);
}

TEST_CASE("blok işleme NaN üretmez", "[filters][rt]") {
    alignas(64) float buf[512];                 // 512 örnek / 48 kHz
    for (auto& x : buf) x = 0.0f;
    BiquadCoeffs c = makeRBJ(FilterType::Highpass, 48000.0f, 20.0f, 0.70710678f);
    BiquadStateTDF2 s{};
    processTDF2(c, s, buf, 512);
    for (auto x : buf) REQUIRE_FALSE(std::isnan(x));
}
```

**Çalıştırma durumu:** 3/3 **PLANNED** — test yok, çalıştırılmadı (CMake/ctest
entegrasyonu bağımlılık kapısından geçer).

## §12 Test kodu şablonu — ctest/CMake iskeleti (PLANNED)

```cmake
# CMakeLists.txt parçası — ŞABLON (PLANNED, repo'da CMakeLists = 0)
enable_testing()
add_executable(filters_tests tests/filters/test_biquad.cpp)
target_link_libraries(filters_tests PRIVATE Catch2::Catch2WithMain neva_dsp)
add_test(NAME filters_dogrulama_6 COMMAND filters_tests "[dsp]")
```

**Disk gerçeği:** repo kökünde/kaynakta C++ test hedefi **YOK** (§1 + dogrulama
§12 grep); bu blok iskelettir.

## §13 Anti-pattern örnekleri (YAPILMAYACAKLAR)

| # | Anti-pattern | Neden yasak | Doğru yaklaşım |
|---|---|---|---|
| 1 | `process()` içinde `std::vector`/`new` | sıfır tahsis (AGENTS.md §1) | sabit `stages[8]` (§10) |
| 2 | `mutex` ile parametre koruma | lock-free (AGENTS.md §2) | atomic snapshot / smoothing (§7) |
| 3 | işlek döngüsünde `std::sin`/`tan` | jitter; prepare() dışı hesap yasağı | katsayıları `prepare()`'da üret (§5) |
| 4 | `throw` / istisna fırlatma filtrede | noexcept (AGENTS.md §3) | hata: NaN kapısı + log (non-RT) |
| 5 | katsayıları her blokta yeniden hesapla | CPU israfı (§7/7 UNKNOWN bütçe) | yalnız fs/f0/Q değişince |
| 6 | filtre çekirdeğini PHP'ye taşımak | katman ihlali (A0/A1) | C++ Neva Engine (PLANNED) |
| 7 | PCM5122 kullanımı | ADR-038 H001 yasağı | PCM3168A / AK4458 |

## §14 Komşu dal örnek sınırları

| Dal | Bu dosyadan alır | Bu dosyaya vermez |
|---|---|---|
| `equalizer/` | biquad katsayı üretici (§5) | bant EQ politikası (R2) |
| `crossover/` | LR kademesi (§9) | hoparlör yerleşimi (R3) |
| `dynamics/` | zincir sırası bilgisi (§10) | limiter eşiği |
| `measurement/` | §6 test senaryosu tarifi | ölçüm aracı seçimi |
| `spatial-audio/` | filtre malzemesi | HRTF/ambisonics parametresi |

## §15 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana plan | [[dsp-master]] |
| K004 bileşenler | [[dsp-bilesenler]] |
| K004 ana doğrulama | [[dsp-dogrulama]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal kapsam | `filters-kapsam.md` |
| Eş dal doğrulama | `filters-dogrulama.md` |
| equalizer örnekler | `../equalizer/equalizer-ornekler.md` |
| crossover kapsam | `../crossover/crossover-kapsam.md` |
| testing kapsam | `../testing/testing-kapsam.md` |
| K003 output | `../../K003-audio/output/output-kapsam.md` |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` |

## §16 Sözlük (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| **RBJ** | Bristow-Johnson Audio EQ Cookbook katsayı formülleri (§5) |
| **DF1 / TDF2** | Direct Form I / Transposed Direct Form II işlek yapıları (§6–§7) |
| **SVF** | State Variable Filter; TPT integratör tabanlı (§8) |
| **LR4** | Linkwitz-Riley 4. sıra = 2× Butterworth-2 cascade (§9) |
| **prepare()** | Blok işleme öncesi tek seferlik hesap fazı (audio thread dışı) |
| **[PLANNED]** | Repo'da olmayan, yalnız tasarım amaçlı kod bloğu |

## §17 Yol haritası (bu dosya için)

1. R1 topoloji kararı → §5–§9 bloklarından yalnız bir aile seçilir, diğerleri arşivlenir.
2. İlk gerçek C++ kodu yazıldığında §1/§12 durumu **[GERÇEK]**'e döner (grep tazelemesi).
3. CMake/ctest iskeleti (§12) bağımlılık kapısından sonra bağlanır.
4. §11 testleri çalıştırılırsa sonuçları `filters-dogrulama.md` §21 raporuna yaz.

## §18 Worked example — katsayı elle hesabı (literatür, repo ile karşılaştırma YOK)

> Bu örnektir; repo'da karşılığı yok (§1). Doğrulama: `filters-dogrulama` §22.3.

```text
Girdi : fs = 48000 Hz · f0 = 1000 Hz · Q = 0.70710678 (2. sıra Butterworth)
Çıktı : b0 = 0.00391615 · b1 = 0.00781430 · b2 = 0.00391615
        a1 = −1.81533755 · a2 = 0.83101138   (a0 = 1 normalize)
Kontrol: |H(e^jw0)| ≈ 0.7071 (−3 dB) — §11 Catch2 ilk testinin bekleneni
```

**Durum:** elle uygulama (RBJ formülü); kod ile eşleşme **PLANNED**.

## §19 Sık yapılan hatalar (checklist — kod öncesi)

1. `alpha` hesabında `Q` yerine `1/Q` yazmak → tepe/dip ters.
2. `a0` normalize'unu unutup `b0..a2` ham döndürmek → genlik kayması.
3. cutoff `fs/2` üstünde seçilirse pre-warping çözülemez → Nyquist kapısı.
4. f0 = 0 veya negatif → `tan`/`sin` bozulur; giriş doğrulaması şart.
5. zincirde DC offset'i highpass ile temizlemek **donanım katmanı** (K001) —
   yazılım highpass bunun yerine geçmez (kural: >0.5V → relay).
6. testte `==` ile float karşılaştırması → `Catch::Approx` (§11).
7. `alignas(64)` yalnız struct tanımında; örnek buffer da hizalanmalı.

## §20 Q&A (bu dosya için)

| Soru | Cevap |
|---|---|
| Repo'da filtre kodu var mı? | **Hayır** — §1 grep negatif (2026-10-13 tazelendi) |
| Hangi kütüphane seçildi? | Seçilmedi — 3 MIT aday (§2), seçim bağımlılık kapısı |
| DF1 mi TDF2 mi? | **UNKNOWN** (R1) — §6/§7 iki aday |
| Hangi sıra (2/4/8)? | **UNKNOWN** (R1) |
| Testler çalışıyor mu? | Hayır — §11–§12 şablon, 0 hedef (PLANNED) |
| Kod ne zaman kanıt olur? | İlk `*.cpp` yazıldığında grep tazelemesi + §11 çalıştırma |

## §21 Değişiklik günlüğü

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | İlk iskelet §1–§4 (69 satır) |
| 2026-10-13 | §5–§21 eklendi: 6 literatür kod bloğu (RBJ/DF1/TDF2/SVF/LR4/zincir), 2 test şablonu (Catch2/ctest), anti-pattern, sınır, wiki-link, sözlük, yol haritası, worked example, checklist, Q&A, günlük — hepsi [PLANNED] etiketli |

**Silme yok:** §1–§4 aynen korunmuştur.

## §22 Kapsam dışı (bu dosyanın üretmediği yüzeyler)

| Yüzey | Neden yok |
|---|---|
| Çalışan C++ derlemesi | repo'da `.cpp/.h` = 0 (disk durumu) |
| Ölçüm sonuçları (SNR/THD/CPU) | `measurement/` dalı — PLANNED |
| Kütüphane sürüm sabitleme | `dsp-bagimliliklar` §5.1 kapısı |
| Filtre topoloji kararı | R1 + ADR |
| index.md güncellemesi | beyazliste dışı — dokunulmadı |
| Git commit | subagent yasağı |

## §23 Kaynak doğrulama tablosu (bu dosyanın kod bloklarının dayanağı)

| # | Kaynak | HTTP (2026-10-13) | Kullanım |
|---|---|---|---|
| 1 | w3.org/2011/audio/audio-eq-cookbook.html | 200 ✅ | §5 RBJ formülleri |
| 2 | ccrma.stanford.edu/~jos/filters/ | 200 ✅ | §6 DF1 · §8 z-domain |
| 3 | github.com/CristianMoresi/DSPark | 200 ✅ | §2 · §8 SVF adayı |
| 4 | github.com/Signalsmith-Audio/dsp | 200 ✅ | §2 filters.h |
| 5 | github.com/ShmKnd/Patina | 200 ✅ | §2 ZDF/TPT devreleri |
| 6 | Wikipedia Linkwitz–Riley_filter | 200 ✅ | §9 LR tanımı |
| 7 | Wikipedia Biquad_filter | 200 ✅ | §6–§7 yapı referansı |

**403/YASAK (kullanılmadı):** aes.org · analog.com · st.com · acoustics.ulb.be ·
audiocooks.org · mathworks.com · hydrogenaud.io.

## §24 Realtime sözleşme özeti (§5–§10 bloklarının bağladığı kurallar)

| Kural | Kaynak | §5–§10'daki karşılığı |
|---|---|---|
| Sıfır tahsis | AGENTS.md §1 | sabit `stages[8]` (§10) |
| Lock-free | AGENTS.md §2 | atomic/smoothing (§13/2) |
| noexcept | AGENTS.md §3 | tüm `process*` fonksiyonları |
| alignas(64) | AGENTS.md §5 | `BiquadStateTDF2` (§7) |
| float32 (ADR-017) | ADR-017 | tüm `float` katsayı/durum |
| 512 örnek / 48 kHz | sartname (ölçüm PLANNED) | blok döngüleri `n = 512` |
| prepare() hesabı | dogrulama §7/1 | `makeRBJ` yalnız prepare'da (§5) |

## §25 Hızlı başlangıç (bu dosyanın okuyucusu için 5 adım)

1. §1'den başla: repo kodu yok → uydurma kod okuma.
2. §2'den aday kütüphaneleri gör (3 MIT); §3 bağımlılık kapısını hatırla.
3. Tasarım gerekirse §5–§10 bloklarını şablon al; **hepsi [PLANNED]**.
4. Test gerekirse §11–§12'yi bağla; çalıştırma sonucunu dogrulama §21'e yaz.
5. §19 checklist'ini kod öncesi uygula; §13 anti-pattern'lerden kaçın.

## §26 Örnek parametre matrisi — SEÇİLMEMİŞ değerler (tahmin YAZILMAZ)

> Bu tablo hangi parametrenin karar gerektiğini listeler; değer sütunu
> **UNKNOWN** doludur. Doldurulması R1/ADR kapılarıdır.

| Parametre | Olası değerler (literatür) | CoreMusic değeri |
|---|---|---|
| Filtre tipi | LP / HP / BP / notch / allpass / peaking (§5) | **UNKNOWN** |
| Sıra (n) | 2 / 4 / 8 / 12 (§9) | **UNKNOWN** |
| Cutoff / f0 | uygulamaya bağlı (Nyquist < fs/2) | **UNKNOWN** |
| Q | 0.7071 (BW2) / 0.5 (BW1) / LR Q=0.7071 (§9) | **UNKNOWN** |
| Topoloji ailesi | Butterworth / LR / Bessel / Chebyshev / Elliptical | **UNKNOWN** |
| İşlek yapı | DF1 / TDF2 / SVF (§6–§8) | **UNKNOWN** |
| Katsayı tipi | float32 / float64 (dogrulama §22.2/3) | **UNKNOWN** |
| Zincir sırası | DC→EQ→filtre→limiter (§10 önermesi) | **UNKNOWN** (pipeline Q3) |
| CPU bütçesi | < 10.67 ms blok (512/48k) | **UNKNOWN** (ölçüm PLANNED) |

**Kural:** bu tabloya tahmin değer yazılmaz; yalnız ADR/karar ile doldurulur.

## §27 Örnek test verisi (sentetik — repo'daki gerçek ölçüm DEĞİL)

```text
[tarıfsız] Aşağıdaki girdiler §11 test şablonu içindir; ölçüm SONUCU değildir.

girdi 1: 1 kHz sinüs, 0.5 amplitude, 48 kHz, 48000 örnek (1 s) → SNR/THD girdisi
girdi 2: 20 Hz–20 kHz log sweep (6 s)                     → genlik/faz süpürme
girdi 3: dirac (n=0: 1.0, gerisi 0)                        → impuls yanıtı
girdi 4: beyaz gürültü ±1.0 (seed sabit, PRNG)             → stres / NaN kapısı
girdi 5: DC = 0.6 sabit                                    → K001 sınır anısı
         (0.5 V üstü donanım relay'i — yazılım highpass mazeret DEĞİL)
```

**Etiket:** bu blok [PLANNED] test girdisidir; sonuç yazıldığında dogrulama
§21 raporu doldurulur.

## §28 Durum özeti (2026-10-13 ölçümü)

| Metrik | Değer |
|---|---|
| Repo filtre kodu | 0 dosya (grep §1 · taze §12) |
| Literatür kod bloğu | 6 (+2 test şablonu) — 8/8 [PLANNED] |
| Aday kütüphane (MIT) | 3 (Signalsmith · DSPark · Patina) |
| Çalışan test | 0 / 3 şablon |
| UNKNOWN parametre | 9 (§26) |
| HEAD doğrulanmış kaynak | 7 (§23) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
