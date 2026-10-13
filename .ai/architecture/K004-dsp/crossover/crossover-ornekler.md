---
node_id: K004-dsp/crossover/crossover-ornekler
node_type: module
status: draft
parent_id: K004-dsp/crossover
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kod
---

# crossover — Örnekler

> **Node:** `K004-dsp/crossover/crossover-ornekler` · **Type:** module · **Status:** draft · **Group:** kod
> **Üst düğüm:** `K004-dsp/crossover` · **Eşlik:** `crossover-kapsam`, `crossover-dogrulama`
> **Kritik disk gerçeği:** Repoda **C++/DSP motoru YOKTUR** (`engine/` dizini yok), K004-dsp **PLANNED**.
> Bu dosyadaki C++ blokları **şema / illüstrasyondur** — diskte çalışırlı hâlleri **mevcut değildir**.
> Bölme frekansı, filtre tipi, derece, bant sayısı ve kanal eşlemesi **UNKNOWN**'dur; uydurma değer yazılmamıştır.

## §A Örnek yapılandırmalar

### A.1 Yapılandırma envanteri

| # | Yapılandırma | Amaç | Değişen ayarlar | Beklenen durum |
|---|---|---|---|---|
| XF-1 | Bypass | Pas geçiş | bypass=true | Çıkış = giriş |
| XF-2 | 2 yollu basit | Bass/treble ayırma | yol=2, fc **UNKNOWN** | İki bant |
| XF-3 | 2 yollu LR | Faz iptalli birleşim | tip **UNKNOWN** | Düz tepki |
| XF-4 | 3 yollu | Bass/mid/treble | yol=3, fc **UNKNOWN** | Üç bant |
| XF-5 | Bass management | Sub yönlendirme | kural **UNKNOWN** | Sub kanalı |
| XF-6 | Faz eşitli | Grup gecikmesi düzeltme | algoritma **UNKNOWN** | Faz uyumu |
| XF-7 | Faz eşitsiz (kontrol) | Referans | düzeltme kapalı | Beklenen fark **UNKNOWN** |
| XF-8 | Kanal eşlemeli | 8.1 çıkış | harita **UNKNOWN** | Doğru kanallar |
| XF-9 | Bant sayısını değiştirme | Deneysel | yol=4 | Kanal eşlemesi bilinmiyor |
| XF-10 | Örnekleme dışı | Uyarı senaryosu | fs≠48 kHz | Katsayı kayması riski |
| XF-11 | Derece artırma | Ayrım testi | order **UNKNOWN** | Eğim artar |
| XF-12 | Derece azaltma | Faz testi | order **UNKNOWN** | Faz yumuşar |

### A.2 Yapılandırma alanları (config fields)

| Alan | Tip | Birim | Varsayılan | Aralık | Durum |
|---|---|---|---|---|---|
| enabled | bool | — | true | true/false | varsayım |
| bypass | bool | — | false | true/false | varsayım |
| crossover_freq | float | Hz | **UNKNOWN** | **UNKNOWN** | Q-X2 |
| filter_type | enum | — | **UNKNOWN** | LR/BW/Bessel | Q-X3 |
| order | int | — | **UNKNOWN** | **UNKNOWN** | Q-X3 |
| band_count | int | — | **UNKNOWN** | 2/3/4 | Q-X1 |
| bass_management | bool | — | **UNKNOWN** | true/false | Q-X4 |
| channel_map | int[] | — | **UNKNOWN** | **UNKNOWN** | Q-X5 |
| bypass_mode | enum | — | **UNKNOWN** | hard/soft | Q-X6 |
| phase_alignment | bool | — | **UNKNOWN** | true/false | Q-X7 |
| lfe_handling | enum | — | **UNKNOWN** | full/none/unknown | Q-X8 |
| meter_enable | bool | — | true | true/false | varsayım |

### A.3 Ortam sabitleri (environment constants)

| Sabit | Değer | Kaynak | Kullanım |
|---|---|---|---|
| sample_rate | 48000 | sartname | Katsayı üretimi |
| block_size | 512 | sartname | Blok işleme |
| sample_format | float32 | ADR-017 | Örnek temsili |
| alignment | 64 | kök AGENTS.md §5 | Durum yapısı hizalama |
| dac | PCM3168A | ADR-038 H001 | Çıkış aşaması |
| dc_relay_threshold | 0.5 V | kök AGENTS.md §10 | Donanım koruması |
| cpp_standard | C++20 | kök AGENTS.md §5 | Dil düzeyi |
| juce_version | "9" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| asio_sdk_version | "2.3.4" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| output_layout | 8.1 (iddia) | proje kapsamı | **UNKNOWN** — kanıt yok |

### A.4 Örnek akış tablosu

| Aşama | Girdi | İşlem | Çıktı | Hata modu |
|---|---|---|---|---|
| F-1 | Blok (512) | Parametre okuma | Atomik anlık görüntü | Torn read |
| F-2 | Sinyal | Bant ayırma | N yol | Genlik aşımı |
| F-3 | fs + fc + order | Katsayı üretimi | Katsayı seti | Kutup kayması |
| F-4 | Yol 1 | LPF uygulama | Alt bant | Kararsızlık |
| F-5 | Yol 2 | HPF uygulama | Üst bant | Kararsızlık |
| F-6 | Bantlar | Faz eşitleme | Hizalı bantlar | Algoritma **UNKNOWN** |
| F-7 | Bantlar | Toplama | Birleşik sinyal | Faz iptali |
| F-8 | Bass bant | Sub yönlendirme | Sub çıkışı | Kural **UNKNOWN** |
| F-9 | Birleşik | Kanal eşleme | 8.1 çıkışlar | Harita **UNKNOWN** |
| F-10 | Çıkış | bypass kontrolü | Son sinyal | Sert geçiş |

### A.5 Gerçek repo örnekleri (disk)

| Dosya | Satır | Kod/ gözlem | Bu dosyadaki karşılığı |
|---|---|---|---|
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` → `Math.min(1, Math.max(0, value))` | Parametre sınırlama deseni |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 160 | `edgeSnap(clamp(raw))` | Sınır yakını davranışı |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 57-59 | `edgeSnap` eşiği 0.98 / 0.02 | Kenar eşiği deseni |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 17 | `final class ApiMiddlewarePipeline` | Katmanlı zincir deseni |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 24 | `pipe(callable $middleware): self` | Katman ekleme API'si |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 33 | `process(array $request, callable $handler): array` | Zincir yürütme |
| `api.coremusic.net/config/routes.php` | `Satır:` 24 | `return [` — rota tablosu başlangıcı | Yapılandırma tablosu deseni |
| `home.coremusic.net/pages/components/widget-grid.php` | `Satır:` 59-60 | EQ widget'ı bant/kanal sunumu | Bant–kanal eşlemesinin UI kanıtı |

> Bu satırlar **gerçek repodan okunmuştur** (PHP/JS). Crossover'ın C++ karşılığı diskte **yoktur**.

### A.6 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| JUCE `juce_dsp` işlemciler grubu, filtre/pitch/oversampling gibi ses işleme bileşenlerini listeler | https://docs.juce.com/group__juce__dsp__processors.html | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| Web Audio API `StereoPannerNode`, stereo girdiyi azimut tabanlı konumlandırma ile dengeler | https://developer.mozilla.org/en-US/docs/Web/API/StereoPannerNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> Bu kayıtlar yalnızca **erişilebilirlik / sürüm doğrulaması** içindir; K004-dsp mimari kanıtı sayılmaz.
> JUCE sürümü ("9") bu sayfalardan **doğrulanamaz** → **VERIFICATION REQUIRED**.

## §B Kod örnekleri (C++)

> **Uyarı:** Aşağıdaki bloklar **şemadır**; diskte karşılığı **yoktur** (motor PLANNED).
> `UNKNOWN` işaretli sabitler uydurulmamıştır. Bloklar `constexpr` / `alignas(64)` /
> `noexcept` / sıfır tahsis kurallarına uygun yazılmıştır.

### B.1 Parametre yapısı

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <cstdint>

namespace coremusic::dsp {

struct CrossoverSpec {
    static constexpr std::uint32_t kBlockSize  = 512;   // sartname
    static constexpr std::uint32_t kSampleRate = 48000; // sartname
    static constexpr std::size_t   kAlign      = 64;    // kural #5
    static_assert(kBlockSize == 512, "sartname 512 ornek");
    static_assert(kSampleRate == 48000, "sartname 48 kHz");
};

struct CrossoverParams {
    float fc_hz          = 0.0F;   // UNKNOWN (Q-X2): karar bekliyor
    int   order          = 0;      // UNKNOWN (Q-X3): karar bekliyor
    int   band_count     = 2;      // UNKNOWN (Q-X1): varsayim 2 yol
    bool  bypass         = false;
    bool  bass_management = false; // UNKNOWN (Q-X4)
    bool  phase_align    = false;  // UNKNOWN (Q-X7)
};

} // namespace coremusic::dsp
```

### B.2 Atomic parametre paylaşımı (lock-free)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <atomic>
#include "CrossoverSpec.h"

namespace coremusic::dsp {

class alignas(CrossoverSpec::kAlign) XoverParamBlock {
public:
    void publish(const CrossoverParams& p) noexcept {
        fc_.store(p.fc_hz, std::memory_order_release);
        order_.store(static_cast<float>(p.order), std::memory_order_release);
        bands_.store(static_cast<float>(p.band_count), std::memory_order_release);
        flags_.store(pack(p), std::memory_order_release);
    }

    [[nodiscard]] CrossoverParams snapshot() const noexcept {
        CrossoverParams p;
        p.fc_hz      = fc_.load(std::memory_order_acquire);
        p.order      = static_cast<int>(order_.load(std::memory_order_acquire));
        p.band_count = static_cast<int>(bands_.load(std::memory_order_acquire));
        unpack(flags_.load(std::memory_order_acquire), p);
        return p;
    }

private:
    static std::uint32_t pack(const CrossoverParams& p) noexcept {
        std::uint32_t f = 0u;
        if (p.bypass)          f |= 1u;
        if (p.bass_management) f |= 2u;
        if (p.phase_align)     f |= 4u;
        return f;
    }

    static void unpack(std::uint32_t f, CrossoverParams& p) noexcept {
        p.bypass          = (f & 1u) != 0u;
        p.bass_management = (f & 2u) != 0u;
        p.phase_align     = (f & 4u) != 0u;
    }

    std::atomic<float>         fc_{0.0F};
    std::atomic<float>         order_{0.0F};
    std::atomic<float>         bands_{2.0F};
    std::atomic<std::uint32_t> flags_{0u};
};

} // namespace coremusic::dsp
```

### B.3 Katsayı üretici (biquad, `constexpr` dostu)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include <cmath>

namespace coremusic::dsp {

struct Biquad {
    float b0, b1, b2, a1, a2;   // normalizes a0 = 1
};

// fc_hz / order UNKNOWN (Q-X2, Q-X3): asagidaki tip yalnizca SEMADIR.
// Kutup birimi daire icinde kalmalidir (V-21 / TS-2).
[[nodiscard]] inline Biquad makeLowpass(float fc_hz,
                                        float fs,
                                        float q) noexcept {
    const float w0 = 2.0F * 3.14159265358979323846F * fc_hz / fs;
    const float c  = std::cos(w0);
    const float s  = std::sin(w0);
    const float alpha = s / (2.0F * q);

    const float a0 = 1.0F + alpha;
    Biquad k;
    k.b0 = ((1.0F - c) * 0.5F) / a0;
    k.b1 = (1.0F - c) / a0;
    k.b2 = ((1.0F - c) * 0.5F) / a0;
    k.a1 = (-2.0F * c) / a0;
    k.a2 = (1.0F - alpha) / a0;
    return k;
}

[[nodiscard]] inline Biquad makeHighpass(float fc_hz,
                                         float fs,
                                         float q) noexcept {
    const float w0 = 2.0F * 3.14159265358979323846F * fc_hz / fs;
    const float c  = std::cos(w0);
    const float s  = std::sin(w0);
    const float alpha = s / (2.0F * q);

    const float a0 = 1.0F + alpha;
    Biquad k;
    k.b0 = ((1.0F + c) * 0.5F) / a0;
    k.b1 = (-(1.0F + c)) / a0;
    k.b2 = ((1.0F + c) * 0.5F) / a0;
    k.a1 = (-2.0F * c) / a0;
    k.a2 = (1.0F - alpha) / a0;
    return k;
}

} // namespace coremusic::dsp
```

### B.4 Diferansiyel denklem uygulayıcı (biquad çekirdeği)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include "CrossoverSpec.h"

namespace coremusic::dsp {

class alignas(CrossoverSpec::kAlign) BiquadCore {
public:
    void reset() noexcept { x1_ = x2_ = y1_ = y2_ = 0.0F; }

    void setCoeffs(const Biquad& k) noexcept { k_ = k; }

    [[nodiscard]] float process(float x) noexcept {
        const float y = k_.b0 * x + k_.b1 * x1_ + k_.b2 * x2_
                      - k_.a1 * y1_ - k_.a2 * y2_;
        x2_ = x1_; x1_ = x;
        y2_ = y1_; y1_ = y;
        return y;
    }

private:
    Biquad k_{};
    float x1_ = 0.0F, x2_ = 0.0F;
    float y1_ = 0.0F, y2_ = 0.0F;
};

} // namespace coremusic::dsp
```

### B.5 Zincir yürütme (process)

```cpp
// Sema: diskte degil (K004 PLANNED)
#pragma once
#include "CrossoverSpec.h"
#include "BiquadCore.h"

namespace coremusic::dsp {

class Crossover {
public:
    // Audio callback: noexcept, tahsis yok, kilit yok.
    void process(float* left, float* right, std::size_t n) noexcept {
        const CrossoverParams p = params_.snapshot();
        if (p.bypass) { return; }

        for (std::size_t i = 0; i < n; ++i) {
            const float in_l = left[i];
            const float in_r = (right != nullptr) ? right[i] : in_l;

            // Her yol ayri cekirdekten gecer (sayi: band_count UNKNOWN).
            const float low_l  = lp_l_.process(in_l);
            const float high_l = hp_l_.process(in_l);
            const float low_r  = lp_r_.process(in_r);
            const float high_r = hp_r_.process(in_r);

            // Birlestirme katsayisi UNKNOWN (Q-X3): toplam ≈ 1.0 hedeftir.
            left[i]  = low_l + high_l;
            if (right != nullptr) { right[i] = low_r + high_r; }
        }
    }

    [[nodiscard]] XoverParamBlock& params() noexcept { return params_; }

private:
    XoverParamBlock params_{};      // alignas(64)
    BiquadCore      lp_l_{}, hp_l_{};
    BiquadCore      lp_r_{}, hp_r_{};
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
| B6-5 | `fc_hz` | 0.0 | Q-X2 | **UNKNOWN** — karar yok |
| B6-6 | `order` | 0 | Q-X3 | **UNKNOWN** — karar yok |
| B6-7 | `band_count` | 2 | Q-X1 | **UNKNOWN** — varsayım |
| B6-8 | birleşim katsayısı | — | Q-X3 | **UNKNOWN** |

### B.7 Derleme ve bağlama notları

| Öğe | Beklenen | Kanıt | Durum |
|---|---|---|---|
| Dil düzeyi | C++20 | kök AGENTS.md §5 | **VERIFICATION REQUIRED** |
| JUCE modülü | `juce_dsp` | Belge yok | **VERIFICATION REQUIRED** |
| JUCE sürümü | "9" | Belge yok | **VERIFICATION REQUIRED** |
| ASIO SDK | "2.3.4" | Belge yok | **VERIFICATION REQUIRED** |
| Derleyici | — | Proje dosyası yok | **UNKNOWN** |
| Hedef platform | Windows (ASIO) | kök AGENTS.md | varsayım |
| Birim testi çerçevesi | — | C++ tarafı yok | **UNKNOWN** |
| Linkwitz-Riley sınıfı | — | Diskte yok | **UNKNOWN** |

## §C Kötü uygulama

### C.1 Yasaklı desenler (anti-patterns)

| # | Kötü desen | Sonuç | Kural | Doğrusu |
|---|---|---|---|---|
| AP-1 | Callback içinde `new` | Xrun | kural #1 | Önceden ayırma |
| AP-2 | `std::mutex` ile parametre | Prioritevi inversion | kural #2 | `std::atomic` |
| AP-3 | `process` içinde `throw` | Terminate | kural #3 | `noexcept` + dönüş değeri |
| AP-4 | Runtime `pow` ile katsayı | CPU şişmesi | kural #4 | `constexpr` ön hesap |
| AP-5 | Hizasız durum yapısı | Cache cezası | kural #5 | `alignas(64)` |
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
| X-3 | `throw std::runtime_error("x");` | `noexcept` ihlali | Hata bayrağı |
| X-4 | `std::pow` döngü içinde katsayı için | Çalışma zamanı maliyeti | Önceden hesap (kural #4) |
| X-5 | `alignas(1)` durum yapısı | Cache satırı çakışması | `alignas(64)` |
| X-6 | `double` ile işleme | ADR-017 ihlali | `float` |
| X-7 | `blockSize = callbackSize` | Değişken gecikme | 512 sabit doğrulama |
| X-8 | İkinci `create()` çağrısı | Exclusive Lock ihlali | Kilidi sorgula |
| X-9 | Offset'i filtrelememek | 0.5 V röle tetiklenmesi | DC ölçüm + koruma |
| X-10 | Sabit bölme frekansı yazmak | Q-X2 kararı yok | `UNKNOWN` işaretle |

### C.3 Hatalı crossover davranışı

| Senaryo | Hatalı davranış | Belirti | Düzeltme |
|---|---|---|---|
| Faz iptali | İki yol ters fazda | Bass kaybı | Faz eşitleme (**UNKNOWN**) |
| Birleşim ≠ 1.0 | Bölme noktasında sıçrama | Tepki çukuru/tipi | Katsayı kararı (Q-X3) |
| fs yanlış | Katsayılar kayar | Kararsızlık / eğim kayması | fs ile yeniden türetme |
| Kutup birimi dışı | Filtre ısınır | CPU patlar | Katsayı doğrulama |
| Sert bypass | Kip ani değişir | Tıklama | Yumuşatma (Q-X6) |
| Denormal | Sessizlikte CPU şişer | Xrun | `FTZ/DAZ` |
| Torn read | Yarım parametre | Anlık bozulma | Atomik okuma/yazma |
| Kanal eşlemesi yanlış | Hoparlörde yanlış ses | Yanlış yerde bas | Harita kararı (Q-X5) |

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
| `crossover_freq = 0` | Sıfır bölme | Matematiksel singularite | Aralık denetimi |
| `crossover_freq > fs/2` | Nyquist üstü | Aliasing / kararsızlık | Sınır denetimi |
| `order = 0` | Derecesiz filtre | İşlem tanımsız | Sınır denetimi |
| `band_count = 1` | Tek bant | Bölme anlamsız | En az 2 |
| `channel_map` boş | Eşleme yok | Çıkış sessiz | Harita zorunlu |
| `bass_management` + ters faz | Faz iptali | Bas kaybı | Faz kontrolü |
| fs girilmemiş | Varsayılan fs | Katsayı kayması | fs zorunlu |
| bypass yok | Kapatma mümkün değil | Teşhis zor | Bayrak zorunlu |

## §D Karşılaştırmalı

### D.1 Yaklaşım karşılaştırması

| Ölçüt | 2 yollu | 3 yollu | 4 yollu |
|---|---|---|---|
| Bant sayısı | 2 | 3 | 4 |
| Karmaşıklık | Düşük | Orta | Yüksek |
| Gecikme riski | Düşük | Orta | Yüksek |
| Faz hizalama yükü | Az | Çok | Çok |
| Kanal eşlemesi | Basit | Orta | Karmaşık |
| Seçim | — | — | **UNKNOWN** (Q-X1) |
| Ölçüm eşi | 1 | 2 | 3 |

### D.2 Filtre tipi karşılaştırması (literatür terimi — karar yok)

| Ölçüt | Butterworth | Linkwitz-Riley | Bessel |
|---|---|---|---|
| Tepki | Düz (max-flat) | Düz (kare tepki) | Düz faz |
| Faz doğrusallığı | Orta | İyi (çift kademeli) | En iyi |
| Bölme noktasında genlik | −3 dB | −6 dB | **UNKNOWN** |
| Faz iptali direnci | Düşük | Yüksek (hedeflenen) | Orta |
| Kullanım niyeti | — | — | — |
| Karar durumu | **UNKNOWN** (Q-X3) | **UNKNOWN** (Q-X3) | **UNKNOWN** (Q-X3) |

> Bu tablo **literatür karşılaştırmasıdır**; CoreMusic tercihi **kaydedilmemiştir**. Uydurma tercih yazılmaz.

### D.3 Crossover vs komşu modüller

| Özellik | Crossover | Filters | Equalizer |
|---|---|---|---|
| Amaç | Frekansa göre ayrım | Filtre tasarımı | Frekans düzeltmesi |
| Girdi | Tek sinyal | Tasarım parametresi | Sinyal + bant ayarı |
| Çıktı | Bantlar | Katsayılar | Düzeltilmiş sinyal |
| Sıra | Bölme | Tasarım | Düzeltme |
| Modül dosyası | Bu dosya | `K004-dsp/filters` | `K004-dsp/equalizer` |
| Sınır | Q-X2/Q-X3 kararı | R1 (filters) | Düzeltme katsayısı |
| Ölçüm | M-x (PLANNED) | **UNKNOWN** | **UNKNOWN** |

### D.4 Faz eşitleme seçenekleri

| Seçenek | Açıklama | Gecikme | Durum |
|---|---|---|---|
| Eşitleme yok | Ham bantlar | En düşük | **UNKNOWN** tercih |
| Gecikme hizalama | Gecikmeli yol | Ek örnek | **UNKNOWN** |
| Filtre faz düzeltmesi | Ters faz filtresi | Ek karmaşıklık | **UNKNOWN** |
| Offline derleme | Bantları peşinen işleme | Blok dışı | **UNKNOWN** |
| Bileşen | C-9 | — | algoritma **UNKNOWN** (Q-X7) |

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
| Q-X1 | Yol sayısı | §A.1 / §D.1 | **UNKNOWN** |
| Q-X2 | Bölme frekansı | §B.1 / §A.2 | **UNKNOWN** |
| Q-X3 | Filtre tipi + derece + birleşim | §B.3 / §D.2 | **UNKNOWN** |
| Q-X4 | Bass management kuralı | §A.2 / §C.3 | **UNKNOWN** |
| Q-X5 | Kanal eşleme haritası | §A.2 / §C.5 | **UNKNOWN** |
| Q-X6 | Bypass yumuşatma | §C.3 | **UNKNOWN** |
| Q-X7 | Faz eşitleme algoritması | §D.4 | **UNKNOWN** |
| Q-X8 | 8.1 çıkış düzeni | §A.3 | **UNKNOWN** |

### D.7 Dosya sözleşmesi (contract)

| Alan | Değer |
|---|---|
| Node | `K004-dsp/crossover/crossover-ornekler` |
| Type | module |
| Status | draft |
| Parent | `K004-dsp/crossover` |
| Group | kod |
| Bağımlılık | `K004-dsp` |
| Eşlik dosyalar | `crossover-kapsam.md`, `crossover-dogrulama.md` |
| Disk kodu | **YOK** (K004 PLANNED) |
| Sürüm kanıtı | **VERIFICATION REQUIRED** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
