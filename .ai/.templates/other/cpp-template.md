---
title: "C++ / Neva Audio Engine Template — DSP Çekirdek Şablonu"
type: template
category: other
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# C++ / Neva Audio Engine Template — DSP Çekirdek Şablonu

**Zorunlu Bağlantılar:** [[.ai/index]] · [[.ai/brain]] · [[.ai/.templates/index]] · [[.ai/adr/adr-012]] · [[.ai/adr/adr-013]]

---

## §1 Amaç ve Honest Scope

| Gerçek (glob kanıtı) | Durum |
|---|---|
| `*.cpp` / `*.h` dosyaları | ❌ deposofta YOK |
| Neva Engine | hedef mimari (ADR-012/013) |
| Kaynak | `neva.io` / `docs.neva.io` (network doğrulaması gerekir — VERIFICATION REQUIRED) |
| Şablon amacı | yeni DSP kaynağı **doğru iskeletle** açmak |

```bash
# kanıt
Get-ChildItem -Recurse -Include *.cpp,*.h | Measure-Object   # 0
```

---

## §2 Frontmatter / Değişkenler

| Değişken | Açıklama | Örnek |
|---|---|---|
| `{{CLASS_NAME}}` | PascalCase sınıf | `GainProcessor` |
| `{{MODULE_NAME}}` | modül adı | `dsp` |
| `{{PARAM_ID}}` | parametre kimliği | `gain_db` |
| `{{BUFFER_SIZE}}` | örnekleme tamponu | `512` |
| `{{SAMPLE_RATE}}` | örnek hızı | `48000` |

---

## §3 Kaynak Dosya İskeleti

### §3.1 Header (`.h`)

```cpp
#pragma once
// {{CLASS_NAME}}.h — Neva DSP modülü ({{MODULE_NAME}})
#include <cstdint>
#include <array>

namespace CoreMusic::Dsp {

class {{CLASS_NAME}} final {
public:
    {{CLASS_NAME}}() = default;
    ~{{CLASS_NAME}}() = default;

    {{CLASS_NAME}}(const {{CLASS_NAME}}&) = delete;
    {{CLASS_NAME}}& operator=(const {{CLASS_NAME}}&) = delete;

    void prepare(double sampleRate, std::uint32_t maxBlockSize) noexcept;
    void reset() noexcept;
    void process(float* const* io, std::uint32_t numChannels, std::uint32_t numSamples) noexcept;

    void setParameter(std::uint32_t id, float normalized) noexcept;   // 0..1
    float getParameter(std::uint32_t id) const noexcept;

private:
    static constexpr std::uint32_t kMaxBlock = 4096;
    double sampleRate_ = 48000.0;
    float gain_ = 1.0f;                       // linear
    std::array<float, kMaxBlock> envelope_{};  // yardımcı durum
};

}  // namespace CoreMusic::Dsp
```

### §3.2 Kaynak (`.cpp`)

```cpp
#include "{{CLASS_NAME}}.h"
#include <algorithm>
#include <cmath>

namespace CoreMusic::Dsp {

void {{CLASS_NAME}}::prepare(double sampleRate, std::uint32_t maxBlockSize) noexcept
{
    sampleRate_ = sampleRate;
    reset();
    (void)maxBlockSize;
}

void {{CLASS_NAME}}::reset() noexcept
{
    gain_ = 1.0f;
    envelope_.fill(0.0f);
}

void {{CLASS_NAME}}::setParameter(std::uint32_t id, float normalized) noexcept
{
    normalized = std::clamp(normalized, 0.0f, 1.0f);
    if (id == 0 /* {{PARAM_ID}} */) {
        gain_ = std::pow(10.0f, (normalized * 60.0f - 60.0f) / 20.0f);  // -60..0 dB
    }
}

void {{CLASS_NAME}}::process(float* const* io, std::uint32_t numChannels, std::uint32_t numSamples) noexcept
{
    for (std::uint32_t ch = 0; ch < numChannels; ++ch) {
        float* data = io[ch];
        for (std::uint32_t i = 0; i < numSamples; ++i) {
            data[i] *= gain_;
        }
    }
}

}  // namespace CoreMusic::Dsp
```

---

## §4 DSP Kural Tablosu

| Kural | Neden |
|---|---|
| `noexcept` real-time yol | audio thread'te exception yasak |
| `final` + silinen kopya | sahiplik netliği |
| Örnekleme başına döngü, şube tahmini | cache dostu (ADR-013 zero-copy ruhu) |
| Parametre 0..1 normalize + kilitli eğri | host uyumu |
| `prepare/reset` ayrımı | bağımlı durum tek yerde |
| Bellek ayrımı yalnız `prepare`'da | runtime `new` yasak |
| `float` örnekler, `double` katsayı | sektör standardı |

---

## §5 Test ve Ölçüm

| Alet | Amaç | Durum |
|---|---|---|
| Unit (Catch2/GoogleTest — seçim ADR-086) | procesör doğruluğu | kurulacak (YOK) |
| RTA / spectrum | frekans yanıtı | harici |
| A/B render | regresyon | `.mp3` karşılaştırma (neva) |

```cpp
TEST({{CLASS_NAME}}, GainMinus6dB) {
    {{CLASS_NAME}} g;
    g.prepare(48000.0, 512);
    g.setParameter(0, 0.0f);              // -60dB... test senaryosuna göre
    float buf[2][512];
    // ... doldur, process, assert
}
```

---

## §6 Doğrulama & Hata Masası

| # | Adım | Komut / Aksiyon |
|---|---|---|
| 1 | Derleme | proje build (CMake — kurulacak) |
| 2 | Uyarı sıfır | `-Wall -Wextra -Werror` |
| 3 | Sanitizer | ASan/UBSan test koşusu |
| 4 | RT güvence | kilit (try-lock) / alokasyon yok |
| 5 | Kaynak doğruluğu | neva.io dokümanı (network) |

| Hata | Neden | Çözüm |
|---|---|---|
| Click/pop | prepare/resestsiz geçiş | reset() çağrı |
| Xrun | realtime yolda alokasyon/lock | prepare'a taşı |
| Denormal | sabit küçük değer | DAZZLE/flush-to-zero |
| Kanal paterni hatası | stride ihlali | `io[ch]` her kanal ayrı |

---

## §7 Hazır DSP Bileşenleri

### §7.1 Biquad (RBJ) Filtre — tek parça

```cpp
// {{CLASS_NAME}}.h içine
class Biquad final {
public:
    enum class Type { LowPass, HighPass, Peak };

    void setCoefficients(Type t, double freq, double q, double sampleRate) noexcept;
    void reset() noexcept { z1_ = z2_ = 0.0f; }
    float processSample(float x) noexcept {
        const float y = b0_ * x + z1_;
        z1_ = b1_ * x - a1_ * y + z2_;
        z2_ = b2_ * x - a2_ * y;
        return y;
    }
private:
    float b0_ = 1.0f, b1_ = 0.0f, b2_ = 0.0f;
    float a1_ = 0.0f, a2_ = 0.0f;
    float z1_ = 0.0f, z2_ = 0.0f;
};
```

```cpp
// RBJ low-pass katsayı hesabı
void Biquad::setCoefficients(Type t, double freq, double q, double sr) noexcept
{
    const double w0 = 2.0 * 3.14159265358979323846 * freq / sr;
    const double cw = std::cos(w0), sw = std::sin(w0);
    const double alpha = sw / (2.0 * q);
    double b0, b1, b2, a0, a1, a2;
    if (t == Type::LowPass) {
        b0 = (1 - cw) / 2; b1 = 1 - cw; b2 = (1 - cw) / 2;
        a0 = 1 + alpha; a1 = -2 * cw; a2 = 1 - alpha;
    } else if (t == Type::HighPass) {
        b0 = (1 + cw) / 2; b1 = -(1 + cw); b2 = (1 + cw) / 2;
        a0 = 1 + alpha; a1 = -2 * cw; a2 = 1 - alpha;
    } else { // Peak
        b0 = 1 + alpha; b1 = -2 * cw; b2 = 1 - alpha;
        a0 = 1 + alpha / 10; a1 = -2 * cw; a2 = 1 - alpha / 10;
    }
    b0_ = static_cast<float>(b0 / a0);
    b1_ = static_cast<float>(b1 / a0);
    b2_ = static_cast<float>(b2 / a0);
    a1_ = static_cast<float>(a1 / a0);
    a2_ = static_cast<float>(a2 / a0);
}
```

### §7.2 Kırıcı (Limiter) — tek örneklik lookahead'siz

```cpp
float limiterProcess(float x, float& env, float threshold, float attackMs, float releaseMs, double sr) noexcept
{
    const float ax = std::fabs(x);
    const float atkCoef = std::exp(-1.0f / (attackMs * 0.001f * static_cast<float>(sr)));
    const float relCoef = std::exp(-1.0f / (releaseMs * 0.001f * static_cast<float>(sr)));
    env = (ax > env) ? atkCoef * env + (1 - atkCoef) * ax
                     : relCoef * env + (1 - relCoef) * ax;
    const float gain = (env > threshold) ? threshold / env : 1.0f;
    return x * gain;
}
```

### §7.3 Yardımcılar

```cpp
inline float dbToLinear(float db) noexcept { return std::pow(10.0f, db / 20.0f); }
inline float linearToDb(float lin) noexcept { return 20.0f * std::log10(std::max(lin, 1e-9f)); }
inline float clamp01(float v) noexcept { return std::clamp(v, 0.0f, 1.0f); }

// Denormal koruması (x86: MXCSR FTZ/DAZ) — kritik ses yolunda
#if defined(__SSE2__)
#include <xmmintrin.h>
inline void enableFlushDenormals() noexcept {
    _mm_setcsr(_mm_getcsr() | 0x8040);
}
#endif
```

---

## §8 Build & Dizin Düzeni (hedef)

```
neva/
├── CMakeLists.txt
├── include/coremusic/dsp/{{CLASS_NAME}}.h
├── src/{{CLASS_NAME}}.cpp
└── tests/{{CLASS_NAME}}_test.cpp
```

```cmake
# CMakeLists.txt (iskelet)
cmake_minimum_required(VERSION 3.20)
project(NevaDsp LANGUAGES CXX)
set(CMAKE_CXX_STANDARD 20)
add_library(dsp STATIC src/{{CLASS_NAME}}.cpp)
target_include_directories(dsp PUBLIC include)
target_compile_options(dsp PRIVATE -Wall -Wextra -Werror)
```

| Komut | Sonuç |
|---|---|
| `cmake -S . -B build` | yapılandırma |
| `cmake --build build` | derleme |
| `ctest --test-dir build` | testler |

> ADR-086 "ek paket yasağı" nedeniyle test framework seçimi ayrıca karar gerektirir (Catch2 header-only adayı — `VERIFICATION REQUIRED`).

---

## §9 Performans & Ölçüm

| Metrik | Hedef | Araç |
|---|---|---|
| CPU / blok | < %10 tek instance | perf / Instruments |
| Block latency | buffer boyunca sabit | Tracy/catapult |
| Allocation (RT) | 0 | ASan + kod denetimi |
| Bit doğruluğu | referans render ≤ -120 dB | A/B render |

---

## §10 Doğrulama Checklist (uzantı)

| # | Adım | Beklenen |
|---|---|---|
| 6 | `clang-tidy` | modernize/hicpp temiz |
| 7 | RT patikası | `noexcept` + yok alokasyon + yok lock |
| 8 | Katsayı doğruluğu | biquad frekans tepisi referansla eşleşmeli |
| 9 | Denormal | FTZ/DAZ açık (§7.3) |
| 10 | Kaynak | neva.io dokümanı indirildi/okundu |

---

## §11 Ek DSP İskeletleri

### §11.1 Stereo Denge (Pan) — eşit enerji

```cpp
// -1.0 (sol) .. +1.0 (sağ)
void panProcess(float* left, float* right, float pos, std::uint32_t n) noexcept
{
    const float angle = (pos + 1.0f) * 0.25f * 3.14159265f;   // 0..pi/2
    const float gl = std::cos(angle);
    const float gr = std::sin(angle);
    for (std::uint32_t i = 0; i < n; ++i) {
        left[i]  *= gl;
        right[i] *= gr;
    }
}
```

### §11.2 Basit Delay Çizgisi

```cpp
class DelayLine final {
public:
    void prepare(std::uint32_t maxSamples) {
        buf_.assign(maxSamples, 0.0f);
        w_ = 0;
    }
    void write(float x) noexcept { buf_[w_] = x; w_ = (w_ + 1) % buf_.size(); }
    float read(float delaySamples) const noexcept {
        const auto size = static_cast<float>(buf_.size());
        float r = static_cast<float>(w_) - delaySamples;
        while (r < 0.0f) r += size;
        const auto i0 = static_cast<std::size_t>(r) % buf_.size();
        const auto i1 = (i0 + 1) % buf_.size();
        const float frac = r - std::floor(r);
        return buf_[i0] * (1.0f - frac) + buf_[i1] * frac;   // lineer enterpolasyon
    }
private:
    std::vector<float> buf_;
    std::size_t w_ = 0;
};
```

### §11.3 Reverb basit feedback-delay ağı (iskelet)

```text
input ──►[D1]──┬──►[D2]──┬──►[D3]──┬──► sum ──► damp ──► out
               ▲          ▲          ▲
             g1│        g2│        g3│   (g = 0.5..0.8)
```

| Param | Aralık | Not |
|---|---|---|
| decay (g) | 0.0..0.95 | >0.95 riskli (unbounded) |
| damp | 0.0..1.0 | high-cut her geri beslemede |
| predelay | 0..100 ms | erken refleks ayırma |

### §11.4 DC Offset Filtresi (yaygın ihlal)

```cpp
// birinci derece high-pass, ~20 Hz
class DcBlocker final {
public:
    void prepare(double sr) noexcept { const double w = 2*3.14159265*20.0/sr; a_ = w/(w+1.0); }
    float process(float x) noexcept {
        y_ = static_cast<float>(x - xPrev_ + a_ * y_);
        xPrev_ = x;
        return y_;
    }
private:
    double a_ = 0.99;
    float xPrev_ = 0.0f, y_ = 0.0f;
};
```

---

## §12 Kod Stil & Denetim

| Kural | Değer |
|---|---|
| Dil standardı | C++20 (`ADR-086` “en modern sürüm”) |
| Uyarı | `-Wall -Wextra -Werror` (§6) |
| Format | clang-format (proje köküne `.clang-format`) |
| Statik denetim | clang-tidy `bugprone-,performance-` paketleri |
| İsimlendirme | sınıf `PascalCase`, üye `x_`, sabit `kFoo` |
| Dosya | `.h/.cpp` eşleşmeli (`{{CLASS_NAME}}.h/.cpp`) |

```bash
# denetim komutları (hedef)
clang-format -i include/coremusic/dsp/*.h src/*.cpp
clang-tidy src/*.cpp -- -std=c++20
```

---

## §13 Test Matrisi (DSP doğrulama)

| # | Senaryo | Girdi | Beklenen |
|---|---|---|---|
| 1 | Unity gain | x = sin 1kHz | çıkış == giriş (±1 LSB) |
| 2 | -6 dB | param 50% eğrisi | genlik × 0.5 |
| 3 | Sessizlik | x = 0 | y = 0, NaN yok |
| 4 | Kırpma (clip) | x = 2.0 | |y| ≤ 1.0 |
| 5 | DC giriş | x = 0.1 sabit | §11.4 sonrası → 0'a yakınsar |
| 6 | Kanal izolasyonu | sol dolu / sağ boş | sağ çıkış boş kalır |
| 7 | Block bütünlüğü | 1 blok vs N blok | bit benzeri sonuç |
| 8 | Reset | çalışma sonrası reset | sıfır durum |

---

## §14 Hata Masası (DSP)

| Hata | Neden | Çözüm |
|---|---|---|
| NaN/Inf yayıldı | bölme/sqrt domain | `std::isfinite` guard, denormal önleme (§7.3) |
| DC drift | high-pass eksik | DcBlocker (§11.4) |
| Stereoda enerji kaybı | bağımsız pan kazancı | eşit enerji cos/sin (§11.1) |
| Delay zıplaması | enterpolasyon yok | lineer enterp. (§11.2) |
| Blok boyunca tıklama | blok sınırında katsayı sıçraması | param smoothing (LP 10 ms) |
| Tail unbounded | reverb g > 1 | g clamp 0.95 (§11.3) |

| Param smoothing kalıbı | Kod |
|---|---|
| 10 ms eğri | `cur += 0.001f * (target - cur)` (blok içi) |

---

## §15 Konu Listesi (modül şablonları)

| # | Modül | Ana sınıf/şablon | Kritik kısıt |
|---|---|---|---|
| 1 | Gain / pan | `{{CLASS_NAME}}` (§3) | no-alloc, smooth |
| 2 | Biquad filtre | `Biquad` (§7.1) | katsayı yeniden hesap blok dışında |
| 3 | Limiter | `limiterProcess` (§7.2) | envelope durumu süreklilik |
| 4 | Delay/reverb | `DelayLine` (§11.2/11.3) | `g<0.95`, buffer hazır |
| 5 | DC blocker | `DcBlocker` (§11.4) | her kanal ayrı state |
| 6 | Resampler | (planlanan) | ADR-013 zero-copy uyumu |
| 7 | Gözlem/metre | RMS/peak (env) | 30 ms kuyruk |

```cpp
// çok kanallı işleme deseni (her kanal ayrı durum)
for (std::uint32_t ch = 0; ch < numChannels; ++ch) {
    auto& state = channelState_[ch];           // prepare'da numChannels'e göre doldurulur
    float* data = io[ch];
    for (std::uint32_t i = 0; i < numSamples; ++i) {
        data[i] = state.process(data[i]);
    }
}
```

---

## §16 ADR Bağlantıları

| ADR | Karar | Bu şablona etkisi |
|---|---|---|
| ADR-012 | Neva Engine — hedef mimari | §1 honest scope |
| ADR-013 | zero-copy veri yolu | blok işleme desenleri (§3.2) |
| ADR-086 | teknoloji seçimi (en modern) | C++20 (§12) |
| ADR-088+ | yeni ADR'ler | DSP kararları buradan |

⚠️ `neva.io` kaynakları network ile indirilmeden (VERIFICATION REQUIRED) §7/§11 kodları **hazır kalıp**tır; gerçek API imzaları doğrulanarak uyarlanır.

---

## §17 Son Kabul Listesi (definition of done)

| # | Kriter | İşaret |
|---|---|---|
| 1 | `.h/.cpp` çifti derleniyor (`-Werror`) | ☐ |
| 2 | Unit matrisi (§13) geçiyor | ☐ |
| 3 | RT kısıt: no new/lock/exception | ☐ |
| 4 | Denormal koruması (§7.3) uygulandı | ☐ |
| 5 | clang-format/clang-tidy temiz | ☐ |
| 6 | Ses A/B kontrolü (klik yok) | ☐ |
| 7 | neva.io API doğrulaması yapıldı | ☐ |
| 8 | `.ai/.templates/registry` (envanter) girişi parent'ça | ☐ |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
