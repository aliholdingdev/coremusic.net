---
node_id: K004-dsp/compressor/compressor-ornekler
node_type: module
status: draft
parent_id: K004-dsp/compressor
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kod
---

# compressor — Örnekler

> **Node:** `K004-dsp/compressor/compressor-ornekler` · **Type:** module · **Status:** draft · **Group:** kod
> **Üst düğüm:** `K004-dsp/compressor` · **Eşlik:** `compressor-kapsam`, `compressor-dogrulama`
> **Kritik disk gerçeği:** Repoda **C++/DSP motoru YOKTUR** (`engine/` dizini yok), K004-dsp **PLANNED**.
> Bu dosyadaki C++ blokları **şema / illüstrasyondur** — diskte çalışırlı hâlleri **mevcut değildir**.
> Sayısal parametrelerin çoğu **UNKNOWN**'dur; uydurma değer yazılmamıştır.

## §A Örnek yapılandırmalar

### A.1 Yapılandırma envanteri

| # | Yapılandırma | Amaç | Değişen ayarlar | Beklenen durum |
|---|---|---|---|---|
| CF-1 | Bypass | Pas geçiş | bypass=true | Çıkış = giriş |
| CF-2 | Hafif sıkıştırma | Sadece tepe kontrolü | ratio **UNKNOWN** | Düşük GR |
| CF-3 | Orta sıkıştırma | Genel kullanım | ratio **UNKNOWN** | Orta GR |
| CF-4 | Ağır sıkıştırma | Karakter | ratio **UNKNOWN** | Yüksek GR |
| CF-5 | Yavaş attack | Transient koruma | attack **UNKNOWN** | Tepe geçer |
| CF-6 | Hızlı attack | Tepe yakalama | attack **UNKNOWN** | Tepe kesilir |
| CF-7 | Uzun release | Düz zarf | release **UNKNOWN** | Yavaş toparlanma |
| CF-8 | Kısa release | Hızlı toparlanma | release **UNKNOWN** | Pompa riski |
| CF-9 | Hard knee | Net eşik | knee=0 | Keskin geçiş |
| CF-10 | Soft knee | Yumuşak geçiş | knee **UNKNOWN** | Düzgün geçiş |
| CF-11 | Stereo linked | Sahne koruma | link=true | Ortak kazanç |
| CF-12 | Stereo bağımsız | Kanal ayrımı | link=false | Farklı kazanç |
| CF-13 | Makeup açık | Seviye geri kazanım | makeup **UNKNOWN** | Ortalama yükselir |
| CF-14 | Makeup kapalı | Ham seviye | makeup=0 | Çıkış düşer |

### A.2 Yapılandırma alanları (config fields)

| Alan | Tip | Birim | Varsayılan | Aralık | Durum |
|---|---|---|---|---|---|
| enabled | bool | — | true | true/false | varsayım |
| bypass | bool | — | false | true/false | varsayım |
| threshold | float | dBFS | **UNKNOWN** | **UNKNOWN** | Q-C1 |
| ratio | float | N:1 | **UNKNOWN** | **UNKNOWN** | Q-C1 |
| attack | float | ms | **UNKNOWN** | **UNKNOWN** | Q-C1 |
| release | float | ms | **UNKNOWN** | **UNKNOWN** | Q-C1 |
| knee | float | dB | **UNKNOWN** | **UNKNOWN** | Q-C3 |
| makeup | float | dB | **UNKNOWN** | **UNKNOWN** | Q-C4 |
| detector | enum | — | **UNKNOWN** | peak/RMS/hybrid | Q-C2 |
| stereo_link | bool | — | **UNKNOWN** | true/false | Q-C5 |
| gr_meter | bool | — | true | true/false | varsayım |
| lookahead | bool | — | **UNKNOWN** | true/false | — |

### A.3 Ortam sabitleri (environment constants)

| Sabit | Değer | Kaynak | Kullanım |
|---|---|---|---|
| sample_rate | 48000 | sartname | Zaman sabiti dönüşümü |
| block_size | 512 | sartname | Blok işleme |
| sample_format | float32 | ADR-017 | Örnek temsili |
| alignment | 64 | kök AGENTS.md §5 | Tampon hizalama |
| dac | PCM3168A | ADR-038 H001 | Çıkış aşaması |
| dc_relay_threshold | 0.5 V | kök AGENTS.md §10 | Donanım koruması |
| cpp_standard | C++20 | kök AGENTS.md §5 | Dil düzeyi |
| juce_version | "9" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| asio_sdk_version | "2.3.4" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |

### A.4 Örnek akış tablosu

| Aşama | Girdi | İşlem | Çıktı | Hata modu |
|---|---|---|---|---|
| F-1 | Blok (512) | Detector okuma | Seviye | Ölçek hatası |
| F-2 | Seviye | dB dönüşümü | dB değeri | Log tabanı |
| F-3 | dB | Eşik karşılaştırma | Aşım | Chattering |
| F-4 | Aşım | Ratio uygulama | Hedef kazanç | Eğim hatası |
| F-5 | Hedef | Attack süzgeci | Geçici kazanç | Aşırı yavaş |
| F-6 | Geçici | Release süzgeci | Zarf | Aşırı hızlı |
| F-7 | Zarf | Sinyale çarpmak | Sıkıştırılmış | Denormal |
| F-8 | Sıkıştırılmış | Makeup ekleme | Çıkış | Klips |
| F-9 | Çıkış | GR ölçümü | GR değeri | İşaret ters |

### A.5 Gerçek repo örnekleri (disk)

| Dosya | Satır | Kod/ gözlem | Bu dosyadaki karşılığı |
|---|---|---|---|
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` → `Math.min(1, Math.max(0, value))` | Çıkış sınırlama deseni |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 160 | `edgeSnap(clamp(raw))` | Sınır yakını yapışma |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 57-59 | `edgeSnap` eşiği 0.98 / 0.02 | Kenar davranışı |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 52 | `VolumeCalculator` | Kazanç hesap sınıfı |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 31 | Varsayılan ses `0.5` | Varsayılan değere örnek |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 17 | `final class ApiMiddlewarePipeline` | Zincir/mixer deseni |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 24 | `pipe(callable $middleware): self` | Katman ekleme API'si |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 33 | `process(array $request, callable $handler): array` | Zincir yürütme |

> Bu satırlar **gerçek repodan okunmuştur** (PHP/JS). Compressor'ın C++ karşılığı diskte **yoktur**.

### A.6 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `DynamicsCompressorNode.attack`, kazancın düşme süresini milisaniye cinsinden ayarlar (varsayılan 0) | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/attack | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| `WaveShaperNode`, girdiyi aktarımlı eğri (transfer curve) ile doğrusal olmayan biçimde bozar | https://developer.mozilla.org/en-US/docs/Web/API/WaveShaperNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> Bu kayıtlar yalnızca **erişilebilirlik / sürüm doğrulaması** içindir; K004-dsp mimari kanıtı sayılmaz.

## §B Kod örnekleri (C++)

> **Uyarı:** Aşağıdaki bloklar **şemadır**; diskte karşılığı **yoktur** (motor PLANNED).
> `UNKNOWN` işaretli sabitler uydurulmamıştır. Bloklar `constexpr` / `alignas(64)` /
> `noexcept` / sıfır tahsis kurallarına uygun yazılmıştır.

### B.1 Parametre yapısı

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <atomic>
#include <cstdint>

namespace coremusic::dsp {

struct CompressorSpec {
    static constexpr std::uint32_t kBlockSize = 512;   // sartname
    static constexpr std::uint32_t kSampleRate = 48000; // sartname
    static constexpr std::size_t   kAlign = 64;         // kural #5
    static_assert(kBlockSize == 512, "sartname 512 ornek");
    static_assert(kSampleRate == 48000, "sartname 48 kHz");
};

struct CompressorParams {
    float threshold_db = 0.0F;   // UNKNOWN (Q-C1): varsayilan 0 dBFS, karar bekliyor
    float ratio        = 1.0F;   // UNKNOWN (Q-C1): 1.0 = pasif, sinir durumu
    float attack_ms    = 0.0F;   // UNKNOWN (Q-C1): 0 = aninda
    float release_ms   = 0.0F;   // UNKNOWN (Q-C1): 0 = aninda
    float knee_db      = 0.0F;   // UNKNOWN (Q-C3): 0 = hard knee
    float makeup_db    = 0.0F;   // UNKNOWN (Q-C4): 0 = kapali
    bool  stereo_link  = true;   // UNKNOWN (Q-C5)
    bool  bypass       = false;
};

} // namespace coremusic::dsp
```

### B.2 Atomic parametre paylaşımı (lock-free)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <atomic>
#include "CompressorSpec.h"

namespace coremusic::dsp {

class alignas(CompressorSpec::kAlign) ParamBlock {
public:
    void publish(const CompressorParams& p) noexcept {
        threshold_.store(p.threshold_db, std::memory_order_release);
        ratio_.store(p.ratio, std::memory_order_release);
        attack_.store(p.attack_ms, std::memory_order_release);
        release_.store(p.release_ms, std::memory_order_release);
        knee_.store(p.knee_db, std::memory_order_release);
        makeup_.store(p.makeup_db, std::memory_order_release);
        flags_.store(pack(p), std::memory_order_release);
    }

    [[nodiscard]] CompressorParams snapshot() const noexcept {
        CompressorParams p;
        p.threshold_db = threshold_.load(std::memory_order_acquire);
        p.ratio        = ratio_.load(std::memory_order_acquire);
        p.attack_ms    = attack_.load(std::memory_order_acquire);
        p.release_ms   = release_.load(std::memory_order_acquire);
        p.knee_db      = knee_.load(std::memory_order_acquire);
        p.makeup_db    = makeup_.load(std::memory_order_acquire);
        unpack(flags_.load(std::memory_order_acquire), p);
        return p;
    }

private:
    static std::uint32_t pack(const CompressorParams& p) noexcept {
        std::uint32_t f = 0u;
        if (p.stereo_link) f |= 1u;
        if (p.bypass)      f |= 2u;
        return f;
    }

    static void unpack(std::uint32_t f, CompressorParams& p) noexcept {
        p.stereo_link = (f & 1u) != 0u;
        p.bypass      = (f & 2u) != 0u;
    }

    std::atomic<float>        threshold_{0.0F};
    std::atomic<float>        ratio_{1.0F};
    std::atomic<float>        attack_{0.0F};
    std::atomic<float>        release_{0.0F};
    std::atomic<float>        knee_{0.0F};
    std::atomic<float>        makeup_{0.0F};
    std::atomic<std::uint32_t> flags_{0u};
};

} // namespace coremusic::dsp
```

### B.3 Eşik karşılaştırıcı (comparator)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once

namespace coremusic::dsp {

// Hard-knee ve soft-knee arasindaki gecis; knee_db UNKNOWN (Q-C3).
[[nodiscard]] inline float excessDb(float level_db,
                                    float threshold_db,
                                    float knee_db) noexcept {
    const float diff = level_db - threshold_db;
    if (knee_db <= 0.0F) {                 // hard knee
        return diff > 0.0F ? diff : 0.0F;
    }
    const float half = knee_db * 0.5F;
    if (diff <= -half) { return 0.0F; }    // esigin altinda
    if (diff >= half)  { return diff; }    // esigin ustunde
    const float x = diff + half;           // knee bolgesi
    return (x * x) / (2.0F * knee_db);     // quadrik gecis
}

// ratio = 1.0 sinir durumunda kazanc 0 dB (TS-8).
[[nodiscard]] inline float targetGainDb(float excess, float ratio) noexcept {
    if (ratio <= 1.0F) { return 0.0F; }
    const float slope = 1.0F - (1.0F / ratio);
    return -excess * slope;
}

} // namespace coremusic::dsp
```

### B.4 Attack / release zarfı

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <cmath>

namespace coremusic::dsp {

class GainEnvelope {
public:
    void prepare(float sample_rate) noexcept {
        sr_ = sample_rate;
        attack_coeff_  = coeff(attack_ms_);
        release_coeff_ = coeff(release_ms_);
        state_db_ = 0.0F;
    }

    void setTimes(float attack_ms, float release_ms) noexcept {
        attack_ms_  = attack_ms;
        release_ms_ = release_ms;
        attack_coeff_  = coeff(attack_ms);
        release_coeff_ = coeff(release_ms);
    }

    // Hedefe dogru tek yonde ilerleme (V-4 / V-5 monotonluk).
    [[nodiscard]] float process(float target_db) noexcept {
        const float coeff_v = (target_db < state_db_) ? attack_coeff_
                                                      : release_coeff_;
        state_db_ = coeff_v * state_db_ + (1.0F - coeff_v) * target_db;
        return state_db_;
    }

    [[nodiscard]] float state() const noexcept { return state_db_; }

private:
    [[nodiscard]] float coeff(float ms) const noexcept {
        if (ms <= 0.0F) { return 0.0F; }
        const float samples = (ms * 0.001F) * sr_;
        return std::exp(-1.0F / samples);
    }

    float sr_            = 48000.0F;  // sartname
    float attack_ms_     = 0.0F;      // UNKNOWN (Q-C1)
    float release_ms_    = 0.0F;      // UNKNOWN (Q-C1)
    float attack_coeff_  = 1.0F;
    float release_coeff_ = 1.0F;
    float state_db_      = 0.0F;
};

} // namespace coremusic::dsp
```

### B.5 Zincir yürütme (process)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include "CompressorSpec.h"
#include "ParamBlock.h"
#include "CompressorMath.h"
#include "GainEnvelope.h"

namespace coremusic::dsp {

class Compressor {
public:
    void prepare(float sample_rate) noexcept {
        envelope_.prepare(sample_rate);
    }

    // Audio callback: noexcept, tahsis yok, kilit yok.
    void process(float* left, float* right,
                 std::size_t n) noexcept {
        const CompressorParams p = params_.snapshot();
        if (p.bypass) { return; }

        for (std::size_t i = 0; i < n; ++i) {
            const float in_l = left[i];
            const float in_r = (right != nullptr) ? right[i] : in_l;

            const float level = p.stereo_link ? peak(in_l, in_r)
                                              : peak(in_l, in_l);
            const float lvl_db = toDb(level);
            const float exc    = excessDb(lvl_db, p.threshold_db, p.knee_db);
            const float h_tg   = targetGainDb(exc, p.ratio);

            const float g_db = envelope_.process(h_tg) + p.makeup_db;
            const float g    = fromDb(g_db);

            left[i] = in_l * g;
            if (right != nullptr) { right[i] = in_r * g; }
        }
    }

    [[nodiscard]] ParamBlock& params() noexcept { return params_; }

private:
    [[nodiscard]] static float peak(float a, float b) noexcept {
        const float al = (a < 0.0F) ? -a : a;
        const float bl = (b < 0.0F) ? -b : b;
        return (al > bl) ? al : bl;
    }

    ParamBlock    params_{};    // alignas(64)
    GainEnvelope  envelope_{};
    float         gr_db_ = 0.0F;
};

} // namespace coremusic::dsp
```

### B.6 Sabitler ve `static_assert` örnekleri

| Satır | Sabit | Değer | Kaynak | Amaç |
|---|---|---|---|---|
| B6-1 | `kBlockSize` | 512 | sartname | Blok boyu kilidi |
| B6-2 | `kSampleRate` | 48000 | sartname | Frekans kilidi |
| B6-3 | `kAlign` | 64 | kural #5 | Hizalama |
| B6-4 | `kFormat` | float32 | ADR-017 | Biçim kilidi |
| B6-5 | `ratio` sınırı | 1.0 | mantık | Pasif sınır |
| B6-6 | `knee` | 0.0 | Q-C3 | Hard knee varsayılanı |
| B6-7 | `makeup` | 0.0 | Q-C4 | Kapalı varsayılanı |
| B6-8 | `threshold` | 0.0 | Q-C1 | **UNKNOWN** — karar yok |

### B.7 Derleme ve bağlama notları

| Öğe | Beklenen | Kanıt | Durum |
|---|---|---|---|
| Dil düzeyi | C++20 | kök AGENTS.md §5 | **VERIFICATION REQUIRED** |
| JUCE modülü | `juce_dsp` | Belge yok | **VERIFICATION REQUIRED** |
| JUCE sürümü | "9" | Belge yok | **VERIFICATION REQUIRED** |
| ASIO SDK | "2.3.4" | Belge yok | **VERIFICATION REQUIRED** |
| Derleyici | — | Proje dosyası yok | **UNKNOWN** |
| Hedef platform | Windows (ASIO) | kök AGENTS.md | varsayım |
| Uyarılar | /W4 eşdeğeri | — | **UNKNOWN** |
| Birim testi çerçevesi | — | C++ tarafı yok | **UNKNOWN** |

## §C Kötü uygulama

### C.1 Yasaklı desenler (anti-patterns)

| # | Kötü desen | Sonuç | Kural | Doğrusu |
|---|---|---|---|---|
| AP-1 | Callback içinde `new` | Xrun | kural #1 | Önceden ayırma |
| AP-2 | `std::mutex` ile parametre | Prioritevi inversion | kural #2 | `std::atomic` |
| AP-3 | `process` içinde `throw` | Terminate | kural #3 | `noexcept` + dönüş değeri |
| AP-4 | Runtime `pow` ile log | CPU şişmesi | kural #4 | `constexpr` ön hesap |
| AP-5 | Tamponsuz örnekleme | Cache cezası | kural #5 | `alignas(64)` |
| AP-6 | `double` zincir | SIMD uyumsuzluğu | ADR-017 | `float` |
| AP-7 | Değişken blok boyu | Belirsiz gecikme | sartname | 512 sabit |
| AP-8 | Kilitli cihaz açılışı | İkinci süreç çakışması | kural #9 | Exclusive Lock yönetimi |
| AP-9 | DC offset görmezden gelme | Hoparlör hasarı | kural #10 | 0.5 V röle |
| AP-10 | `PCM5122` kullanımı | ADR ihlali | ADR-038 H001 | Yalnız `PCM3168A` |

### C.2 Kötü kod örnekleri (yanlış → doğru)

| # | Yanlış | Neden yanlış | Doğru |
|---|---|---|---|
| X-1 | `float* buf = new float[n];` | Tahsis (kural #1) | Önceden ayrılmış tampon |
| X-2 | `std::lock_guard<std::mutex> g(m);` | Kilit (kural #2) | `std::atomic<float>` |
| AP-3 yerine | `throw std::runtime_error("x");` | `noexcept` ihlali | Hata bayrağı |
| X-4 | `std::pow(10.0F, x/20.0F)` döngüde | Çalışma zamanı maliyeti | Önceden hesaplanmış katsayı |
| X-5 | `alignas(1)` tampon | Cache satırı çakışması | `alignas(64)` |
| X-6 | `double` ile işleme | ADR-017 ihlali | `float` |
| X-7 | `blockSize = callbackSize` | Değişken gecikme | 512 sabit doğrulama |
| X-8 | İkinci `create()` çağrısı | Exclusive Lock ihlali | Kilidi sorgula |
| X-9 | Offset'i filtrelememek | 0.5 V röle tetiklenmesi | DC ölçüm + koruma |
| X-10 | `#define K_SENS 31` | PCM5122 hatası | `PCM3168A` |

### C.3 Hatalı zarf davranışı

| Senaryo | Hatalı davranış | Belirti | Düzeltme |
|---|---|---|---|
| Attack 0 ms | Kazanç anında kapanır | Distorsiyon | Sınır koyma (**UNKNOWN**) |
| Release 0 ms | Kazanç anında açılır | Titreme | Sınır koyma (**UNKNOWN**) |
| Monotonluk ihlali | Zarf geri döner | Tıklama | Tek yönlü güncelleme |
| Overshoot | Hedef aşılır | Pompa | Yumuşatma |
| Mode switch sert | Kip değişimi ani | Kopma | Çapraz geçiş |
| Denormal | Sessizlikte alt-orta sayı | CPU şişmesi | `FTZ/DAZ` |
| Torn read | Yarım parametre | Anormal GR | Atomik okuma/yazma |

### C.4 Kötü ölçüm uygulamaları

| # | Kötü uygulama | Sonuç | Doğrusu |
|---|---|---|---|
| MM-1 | Ölçüm ortamını yazmamak | Tekrarlanamaz | Ortam kaydı |
| MM-2 | Sürümü kaydetmemek | Karşılaştırılamaz | Sürüm zorunlu |
| MM-3 | Tek ölçümle karar | İstatistiksiz | Tekrar |
| MM-4 | Toleranssız PASS | Sahte kesinlik | Tolerans tablosu |
| MM-5 | Ölçümü "PLAN" ile karıştırmak | Yanıltıcı rapor | Durum sütunu |
| MM-6 | Bilinmeyeni sayıyla doldurmak | Uydurma | `UNKNOWN` |

### C.5 Kötü yapılandırma örnekleri

| Yapılandırma | Hata | Etki | Önlem |
|---|---|---|---|
| `threshold = +20 dBFS` | Anlamsız eşik | Hiç sıkıştırma | Aralık denetimi |
| `ratio = 0` | Sıfır eğim | Sinyal kaybı | `ratio ≥ 1` |
| `ratio = -1` | Pozitif eğim | Sinyal şişer | Aralık denetimi |
| `release = -5` | Negatif süre | Salınım | `≥ 0` |
| `knee > 100 dB` | Aşırı geniş knee | Kontrolsüz geçiş | Üst sınır (**UNKNOWN**) |
| `makeup = 60 dB` | Aşırı kazanç | Klips | Sınır (**UNKNOWN**) |
| `bypass` yok | Kapatma mümkün değil | Teşhis zor | Bayrak zorunlu |
| Linked kapalı + asimetrik | Sahne kayması | Merkez kayması | Bağlantı seçeneği |

## §D Karşılaştırmalı

### D.1 Yaklaşım karşılaştırması

| Ölçüt | Hard knee | Soft knee |
|---|---|---|
| Geçiş | Ani | Kademeli |
| Türev | Süresiz (eşikte) | Sürekli |
| Duyma | Belirgin başlangıç | Yumuşak başlangıç |
| Hesap maliyeti | Daha düşük | Daha yüksek |
| Parametre | Yok | `knee_db` (**UNKNOWN**) |
| Varsayılan | Bu dosyada 0.0 (Q-C3) | Karar yok |
| Uygulama zorluğu | Düşük | Orta |

### D.2 Detector karşılaştırması

| Ölçüt | Peak | RMS | Hybrid |
|---|---|---|---|
| Hız | Hızlı | Yavaş | Orta |
| Hassasiyet | Tepeye duyarlı | Ortalamaya duyarlı | Dengeli |
| Pumping riski | Yüksek | Düşük | Orta |
| Hesap maliyeti | Düşük | Orta | Yüksek |
| Seçim | — | — | **UNKNOWN** (Q-C2) |

### D.3 Sıkıştırma vs komşu modüller

| Özellik | Compressor | Limiter | Dynamics (genel) |
|---|---|---|---|
| Amaç | Seviye eşitleme | Sınır koruma | Genel dinamik kontrol |
| Ratio | Orta (**UNKNOWN**) | Çok yüksek | Çeşitli |
| Gecikme | Düşük | Düşük/ölçülü | Değişir |
| Zincir yeri | Orta | Genelde son | Çatı |
| Modül dosyası | Bu dosya | `K004-dsp/limiter` | `K004-dsp/dynamics` |
| Hiyerarşi | Q3 **AÇIK** | Q3 **AÇIK** | Q3 **AÇIK** |
| Ölçüm | M-x (PLANNED) | **UNKNOWN** | **UNKNOWN** |

### D.4 JS clamp deseni vs C++ eşdeğeri

| Özellik | JS (`clamp`) | C++ eşdeğeri | Not |
|---|---|---|---|
| Kaynak | `coreplayer.volume.js` `Satır:` 54 | Bu dosya §B | Farklı katman |
| Tür | JavaScript | C++20 | — |
| Sınır | 0 … 1 | dB aralığı (**UNKNOWN**) | Aralık sabitlenmedi |
| Uygulama | `Math.min/Math.max` | `if` karşılaştırma | Eşdeğer mantık |
| Konum | Sunum/istemci | DSP | Ayrık sorumluluk |

### D.5 Sürüm ve kanıt karşılaştırması

| İddia | Kaynak | Doğrulama yolu | Durum |
|---|---|---|---|
| JUCE 9 | kök AGENTS.md §12 | Belge + kök dosya | **VERIFICATION REQUIRED** |
| ASIO SDK 2.3.4 | kök AGENTS.md §12 | SDK başlık sürümü | **VERIFICATION REQUIRED** |
| C++20 | kök AGENTS.md §5 | Derleyici bayrağı | **VERIFICATION REQUIRED** |
| 512 / 48 kHz | sartname | Cihaz ölçümü | **UNKNOWN** (ölçüm PLANNED) |
| float32 | ADR-017 | Statik denetim | FROZEN |
| PCM3168A | ADR-038 H001 | Donanım envanteri | FROZEN |
| Motor varlığı | — | Disk ölçümü | **YOK** |

### D.6 Açık sorular (open questions)

| Q# | Soru | Bu dosyada etkisi | Durum |
|---|---|---|---|
| Q3 | dynamics ↔ compressor ↔ limiter hiyerarşisi | §D.3 sınırı belirsiz | **AÇIK** |
| Q4 | measurement ↔ testing sınırı | Ölçüm sorumluluğu belirsiz | **AÇIK** |
| Q-C1 | Varsayılan threshold/ratio/attack/release | §B sabitleri **UNKNOWN** | **UNKNOWN** |
| Q-C2 | Detector tipi | §D.2 seçimi | **UNKNOWN** |
| Q-C3 | Knee genişliği | §B.3 / §D.1 | **UNKNOWN** |
| Q-C4 | Makeup modu | §B.1 | **UNKNOWN** |
| Q-C5 | Stereo linked varsayılanı | §B.5 | **UNKNOWN** |
| Q-C6 | GR birimi ve penceresi | Gösterge | **UNKNOWN** |

### D.7 Dosya sözleşmesi (contract)

| Alan | Değer |
|---|---|
| Node | `K004-dsp/compressor/compressor-ornekler` |
| Type | module |
| Status | draft |
| Parent | `K004-dsp/compressor` |
| Group | kod |
| Bağımlılık | `K004-dsp` |
| Eşlik dosyalar | `compressor-kapsam.md`, `compressor-dogrulama.md` |
| Disk kodu | **YOK** (K004 PLANNED) |
| Sürüm kanıtı | **VERIFICATION REQUIRED** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
