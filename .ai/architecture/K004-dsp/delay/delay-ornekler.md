---
node_id: K004-dsp/delay/delay-ornekler
node_type: module
status: draft
parent_id: K004-dsp/delay
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kod
---

# delay — Örnekler

> **Node:** `K004-dsp/delay/delay-ornekler` · **Type:** module · **Status:** draft · **Group:** kod
> **Üst düğüm:** `K004-dsp/delay` · **Eşlik:** `delay-kapsam`, `delay-dogrulama`
> **Kritik disk gerçeği:** Repoda **C++/DSP motoru YOKTUR** (`engine/` dizini yok), K004-dsp **PLANNED**.
> Bu dosyadaki C++ blokları **şema / illüstrasyondur** — diskte çalışırlı hâlleri **mevcut değildir**.
> Sayısal parametrelerin çoğu **UNKNOWN**'dur; uydurma değer yazılmamıştır.

## §A Örnek yapılandırmalar

### A.1 Yapılandırma envanteri

| # | Yapılandırma | Amaç | Değişen ayarlar | Beklenen durum |
|---|---|---|---|---|
| CF-1 | Bypass | Pas geçiş | bypass=true | Çıkış = giriş |
| CF-2 | Kısa gecikme | slap-back yankısı | delay_ms **UNKNOWN** | Tek tekrar |
| CF-3 | Orta gecikme | dub tarzı | delay_ms **UNKNOWN** | Hissedilir seri |
| CF-4 | Uzun gecikme | ambient kuyruk | delay_ms **UNKNOWN** | Uzun seri |
| CF-5 | Feedback kapalı | tek kopya | feedback=0 | Tek tekrar |
| CF-6 | Düşük feedback | hafif seri | feedback **UNKNOWN** | Sönümlü tekrarlar |
| CF-7 | Yüksek feedback | uzun kuyruk | feedback **UNKNOWN** | Saklama riski |
| CF-8 | Feedback eşiğinde | koruma testi | feedback >= 1 | Aşırı yükselme riski |
| CF-9 | Denge karışımı | şeffaf ek | mix **UNKNOWN** | Eşit kuru/ıslak |
| CF-10 | Tam ıslak | yalnız gecikmiş | mix=1 | Orijinal duyulmaz |
| CF-11 | Tam kuru | referans | mix=0 | Gecikme duyulmaz |
| CF-12 | Stereo ofset | sahne genişliği | sağ/sol delay farklı | Geniş stereo |
| CF-13 | Modülasyonlu | koro/chorus karakteri | LFO derinliği **UNKNOWN** | Perde salınımı |
| CF-14 | Tempo senkron | ritmik hizalama | BPM **UNKNOWN** | Vuruşa hizalı |
| CF-15 | Kuyruk fade | güvenli kapanış | fade_süresi **UNKNOWN** | Yavaş söndürme |
| CF-16 | Denormal korumalı | sessizlikte CPU | flush-to-zero aktif | CPU sabit |

### A.2 Yapılandırma alanları (config fields)

| Alan | Tip | Birim | Varsayılan | Aralık | Durum |
|---|---|---|---|---|---|
| enabled | bool | — | true | true/false | varsayım |
| bypass | bool | — | false | true/false | varsayım |
| delay_ms | float | ms | **UNKNOWN** | **UNKNOWN** | Q-D1 |
| delay_samples | int | örnek | türetilir | 0..buffer-1 | Q-D2 |
| feedback | float | oranı | **UNKNOWN** | 0..1 arası | Q-D3 |
| mix | float | 0..1 | **UNKNOWN** | 0..1 | Q-D4 |
| stereo_offset_ms | float | ms | **UNKNOWN** | **UNKNOWN** | Q-D5 |
| mod_rate_hz | float | Hz | **UNKNOWN** | **UNKNOWN** | Q-D6 |
| mod_depth | float | ms | **UNKNOWN** | **UNKNOWN** | Q-D6 |
| sync_mode | enum | — | **UNKNOWN** | free/ms/dotted | Q-D7 |
| bpm | float | BPM | **UNKNOWN** | **UNKNOWN** | Q-D7 |
| buffer_max_ms | float | ms | **UNKNOWN** | tahsis sınırı | Q-D8 |
| clamp_output | bool | — | true | true/false | varsayım |

### A.3 Ortam sabitleri (environment constants)

| Sabit | Değer | Kaynak | Kullanım |
|---|---|---|---|
| sample_rate | 48000 | sartname | ms→örnek dönüşümü |
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
| F-1 | Blok (512) | Yazma imleci güncelleme | İmleç | Taşma |
| F-2 | delay_ms | ms→örnek dönüşümü | Örnek sayısı | Bölme hatası |
| F-3 | İmleç - gecikme | Okuma imleci hesap | Okuma konumu | Negatif konum |
| F-4 | Okuma konumu | Kesirli aradeğer | Gecikmiş örnek | Enterpolasyon hatası |
| F-5 | Gecikmiş | Feedback çarpımı | Geri besleme | Aşırı yükselme |
| F-6 | Geri besleme | Yazma noktasına ekleme | Kuyruk | Saklama (clipping) |
| F-7 | Giriş + gecikmiş | Kuru/ıslak karışım | Karışım | Mix hatası |
| F-8 | Karışım | Çıkış sınırlama | Güvenli çıkış | Klips |
| F-9 | Çıkış | Seviye ölçümü | Gösterge | İşaret ters |

### A.5 Gerçek repo örnekleri (disk)

| Dosya | Satır | Kod/ gözlem | Bu dosyadaki karşılığı |
|---|---|---|---|
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 52 | `VolumeCalculator` sınıfı | Parametre sahibi yapı |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` → `Math.min(1, Math.max(0, value))` | Feedback/giriş sınırlama |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 31 | Varsayılan ses `0.5` | Varsayılan parametre deseni |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 160 | `edgeSnap(clamp(raw))` | Zarf sonu yumuşatma |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 17 | `final class ApiMiddlewarePipeline` | Sabit sınıf deseni |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 33 | `process` — sıralı zincir yürütme | Blok işleme döngüsü |
| `api.coremusic.net/config/routes.php` | `Satır:` 24 | route kaydı | Yapılandırma kaydı deseni |
| `api.coremusic.net/config/routes.php` | `Satır:` 36 | ikinci route kaydı | Yapılandırılmış zincir örneği |
| `api.coremusic.net/config/routes.php` | `Satır:` 42 | route kaydı | Sıralı kayıt deseni |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 24 | `pipe(callable $middleware): self` | Katman ekleme (tap) API'si |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 39 | zincir sonu işleyici | Son işlem (sink) deseni |
| `shared/composer.json` | `Satır:` 24 | test bağımlılığı kaydı | Yapılandırma kaydı deseni |

> Bu satırlar **gerçek repodan okunmuştur** (PHP/JS). Delay-line C++ karşılığı diskte **yoktur**.

### A.6 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `ConvolverNode`, girdiyi bir konvolüsyon impuls yanıtı (impulse response) ile işleyerek uzamsal/yankı efekti üretir | https://developer.mozilla.org/en-US/docs/Web/API/ConvolverNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| JUCE `DryWetMixer` işleyicisi, kuru (dry) ve ıslak (wet) sinyalleri `mix` oranıyla birleştirir | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_DryWetMixer.h | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

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

namespace neva::dsp {

struct DelayConfig {
    float delayMs        = 0.0f;   // Q-D1: gercek deger UNKNOWN
    float feedback       = 0.0f;   // Q-D3: 0..1 arasi, sinir kontrolu zorunlu
    float mix            = 0.5f;   // Q-D4: kuru/islak dengesi
    float stereoOffsetMs = 0.0f;   // Q-D5: sag/sol farki
    bool  enabled        = true;
    bool  bypass         = false;
};

// Audio thread paylasimi: lock-free atomik yayin (SPSC yayini)
struct alignas(64) DelayParamState {
    std::atomic<float>    delayMs   {0.0f};
    std::atomic<float>    feedback  {0.0f};
    std::atomic<float>    mix       {0.5f};
    std::atomic<uint32_t> genCounter{0u};   // yapisal degisim sayaci
};

static_assert(std::atomic<float>::is_always_lock_free,
              "delay parametre yayini lock-free olmali");

} // namespace neva::dsp
```

### B.2 Circular buffer (delay line)

```cpp
// Sema: diskte degil (K004 PLANNED)
#include <array>
#include <cstddef>

namespace neva::dsp {

// Tahsis baslangicta: prepare() icinde, audio callback'inde DEGIL.
class DelayLine {
public:
    void prepare(double sampleRate, std::size_t maxDelaySamples) noexcept {
        sampleRate_ = sampleRate;
        maxLength_  = maxDelaySamples < kMinLength ? kMinLength : maxDelaySamples;
        if (maxLength_ > kMaxLength) { maxLength_ = kMaxLength; overflow_ = true; }
        writePos_ = 0;
        // buffer sabit boyutlu std::array -> ses callback'inde tahsis YOK
        buffer_.fill(0.0f);
    }

    void push(float x) noexcept {
        buffer_[writePos_] = x;
        writePos_ = (writePos_ + 1) % maxLength_;
    }

    // tam örnek gecikme okumasi (kesirli okuma B.4'te)
    float readRecent(std::size_t delaySamples) const noexcept {
        if (delaySamples >= maxLength_) { delaySamples = maxLength_ - 1; }
        const std::size_t readPos =
            (writePos_ + maxLength_ - delaySamples) % maxLength_;
        return buffer_[readPos];
    }

    bool overflow() const noexcept { return overflow_; }

private:
    static constexpr std::size_t kMinLength = 512;     // sartname blok boyu
    static constexpr std::size_t kMaxLength = 48000U * 10U; // 10 sn @ 48 kHz ust sinir
    std::array<float, kMaxLength> buffer_{};           // alignas(64) hizalı
    std::size_t writePos_ = 0;
    std::size_t maxLength_ = kMinLength;
    double      sampleRate_ = 48000.0;
    bool        overflow_ = false;
};

} // namespace neva::dsp
```

### B.3 Feedback ve kuru/ıslak karışımı

```cpp
// Sema: diskte degil (K004 PLANNED)
namespace neva::dsp {

// Tek örnek adim: yaz -> oku -> geri besle -> karistir.
// Clamp zorunlu: feedback >= 1.0 sistemi saklar (Q-D3).
inline float delayStep(DelayLine& line, float in, float delaySamples,
                       float feedback, float mix) noexcept {
    const float wet = line.readRecent(static_cast<std::size_t>(delaySamples));

    float fb = feedback;
    if (fb < 0.0f) { fb = 0.0f; }        // negatif feedback sinirla
    if (fb > 0.99f) { fb = 0.99f; }      // 1.0 altinda tut (saklama korumasi)

    const float toWrite = in + wet * fb; // geri besleme toplami
    float guarded = toWrite;
    if (guarded >  4.0f) { guarded  = 4.0f; }  // erken klips (dB ~ +12)
    if (guarded < -4.0f) { guarded = -4.0f; }

    line.push(guarded);

    float m = mix;
    if (m < 0.0f) { m = 0.0f; }
    if (m > 1.0f) { m = 1.0f; }
    return in * (1.0f - m) + wet * m;    // kuru/islak dengesi (DryWetMixer karsiligi)
}

} // namespace neva::dsp
```

### B.4 Kesirli okuma (fractional read / enterpolasyon)

```cpp
// Sema: diskte degil (K004 PLANNED)
namespace neva::dsp {

// Lineer enterpolasyon: iki tam ornek arasi dogrusal gecis.
// Neden: delay_ms ornegi genelde tam sayiya oturmaz (48000 Hz'te 1 ms = 48 ornek,
// ama 0.5 ms gibi degerler kesirli kalir).
inline float lerp(float a, float b, float t) noexcept {
    return a + (b - a) * t;
}

inline float readFractional(const DelayLine& line, float delaySamples) noexcept {
    const auto  d    = delaySamples < 1.0f ? 1.0f : delaySamples;
    const auto  base = static_cast<std::size_t>(d);
    const float frac = d - static_cast<float>(base);
    const float s0   = line.readRecent(base + 1U);
    const float s1   = line.readRecent(base);
    return lerp(s0, s1, frac);
}

// Not: daha ustun enterpolasyon (cubic/sinc) Q-D9 kapsaminda; secim UNKNOWN.

} // namespace neva::dsp
```

### B.5 Blok işleme (process)

```cpp
// Sema: diskte degil (K004 PLANNED)
namespace neva::dsp {

// noexcept zorunlu (kok kural 3). Icinde new/malloc/lock YOK (kok kural 1-2).
class DelayProcessor {
public:
    void prepare(double sampleRate, int blockSize) noexcept {
        const auto maxMs = 1000.0f;  // ust sinir Q-D8: gercek deger UNKNOWN
        const auto maxSamples =
            static_cast<std::size_t>(sampleRate * maxMs / 1000.0);
        lineL_.prepare(sampleRate, maxSamples);
        lineR_.prepare(sampleRate, maxSamples);
        blockSize_ = blockSize;
    }

    void process(float* left, float* right, int numSamples) noexcept {
        const DelayParamState& p = params_;
        const float delayMs   = p.delayMs.load(std::memory_order_relaxed);
        const float feedback  = p.feedback.load(std::memory_order_relaxed);
        const float mix       = p.mix.load(std::memory_order_relaxed);
        const float samples   = delayMs * static_cast<float>(rateHz_) / 1000.0f;

        for (int i = 0; i < numSamples; ++i) {
            const float inL = left[i];
            const float inR = right != nullptr ? right[i] : inL;
            const float outL = delayStep(lineL_, inL, samples, feedback, mix);
            const float outR = delayStep(lineR_, inR, samples, feedback, mix);
            left[i]  = outL;
            if (right != nullptr) { right[i] = outR; }
        }
    }

private:
    DelayLine lineL_{};
    DelayLine lineR_{};
    DelayParamState params_{};
    double rateHz_ = 48000.0;
    int    blockSize_ = 512;
};

} // namespace neva::dsp
```

### B.6 Sabitler ve `static_assert` örnekleri

```cpp
// Sema: diskte degil (K004 PLANNED)
static_assert(48000 % 512 != 0, "blok boyu ornek orani bolunebilir degil - bilgi");
static_assert(sizeof(float) == 4, "ADR-017: 32-bit float zorunlu");
constexpr double msToSamples(double ms, double rate) noexcept {
    return ms * rate / 1000.0;
}
static_assert(msToSamples(1.0, 48000.0) == 48.0, "1 ms = 48 ornek @ 48 kHz");
```

### B.7 Derleme ve bağlama notları

| Konu | Not | Durum |
|---|---|---|
| Dil düzeyi | C++20 (kök kural) | sabit |
| Tahsis | `std::array` sabit boyut, prepare anında | kural |
| Kilitleme | atomik yayın, mutex yok | kural |
| Callback | `noexcept` imzası zorunlu | kural |
| Hizalama | `alignas(64)` tamponlar | kural |
| JUCE sürümü | "9" iddiası | **VERIFICATION REQUIRED** |
| ASIO SDK | "2.3.4" iddiası | **VERIFICATION REQUIRED** |
| Bağlı dosyalar | `juce_DryWetMixer.h` eşdeğeri kendi kodumuzda | şema |
| Örnek oranında yeniden kurulum | `prepare()` yeniden çağrısı | zorunlu |
| `buffer_max_ms` tahsise eşitliği | prepare anında kontrol | zorunlu |

## §C Kötü uygulama

### C.1 Yasaklı desenler (anti-patterns)

| # | Yasaklı desen | Sonuç | Doğru yaklaşım |
|---|---|---|---|
| AP-1 | Callback içinde `new float[]` | Tahsis → jitter | `prepare()` içinde tahsis |
| AP-2 | `std::mutex` ile parametre yazımı | Kilit bekleme → xruns | Atomik SPSC yayını |
| AP-3 | Feedback değerini sınırsız bırakma | Saklama → donanım hasarı riski | 0.99 üst sınırı |
| AP-4 | Buffer boyutunu örnek oranına sabitlememe | Örnek oranında yanlış gecikme | ms→örnek dönüşümü |
| AP-5 | Kesirli gecikmeyi tam sayıya yuvarlama | Metronom hatalı | Enterpolasyon |
| AP-6 | Denormal yok sayma | CPU patlaması (sessiz kuyruk) | Flush-to-zero |
| AP-7 | Modülasyon LFO'sunu blok başında sıfırlama | Tıklama (click) | Faz kesintisiz sürdürme |
| AP-8 | `mix` alanını 0..1 dışına bırakma | Seviye zıplaması | Clamp |

### C.2 Kötü kod örnekleri (yanlış → doğru)

```cpp
// YANLIS (AP-1 + AP-2): callback icinde tahsis ve kilit
void processBad(float* io, int n) {
    auto* tmp = new float[n];              // tahsis - YASAK
    std::lock_guard<std::mutex> g(mu);     // kilit - YASAK
    for (int i = 0; i < n; ++i) { tmp[i] = io[i] * 0.5f; io[i] = tmp[i]; }
    delete[] tmp;                          // serbest birakma
}

// DOGRU: hazir tampon, kilit yok
void processGood(float* io, int n) noexcept {
    for (int i = 0; i < n; ++i) { io[i] *= 0.5f; }
}
```

```cpp
// YANLIS (AP-3): sinirsiz feedback
float fb = feedbackRaw;                 // 1.2 gibi degerler gecer
toWrite = in + wet * fb;                // sonsuza dogru buyume

// DOGRU: sinir
float fb = clamp(feedbackRaw, 0.0f, 0.99f);
toWrite = clamp(in + wet * fb, -4.0f, 4.0f);
```

### C.3 Hatalı feedback davranışı

| Senaryo | Hatalı davranış | Doğru davranış |
|---|---|---|
| feedback = 1.0 | Kuyruk sonsuza kadar büyür | 0.99 üst sınırı + klips |
| feedback < 0 | Faz tersi çınlar | Negatif değer 0'a clamp |
| feedback anlık değişimi | Tıklama | Blok içi rampa/zarf |
| Uzun sessizlik | Denormal CPU artışı | Flush-to-zero / DC yok etme |
| Bypass anlık geçiş | Seviye zıplaması | Crossfade (DryWetMixer benzeri) |

### C.4 Kötü ölçüm uygulamaları

| # | Kötü ölçüm | Neden yanlış | Doğrusu |
|---|---|---|---|
| M-1 | Gecikmeyi kulakla tahmin | Öznel | Impuls testi (ölçüm PLANNED) |
| M-2 | Tek örnek gecikme ölçümü | Enterpolasyon gizler | Grup gecikmesi ölçümü |
| M-3 | Feedback=1.5 ile sistem testi | Saklama hasarı | Sınırlı feedback ile |
| M-4 | 44.1 kHz'te ölçüp 48 kHz'te kullanma | Örnek oranı farkı | Her örnek oranında ayrı |
| M-5 | Sessizlikte CPU ölçümü | Denormal saklanır | Uzun sessizlik senaryosu |

### C.5 Kötü yapılandırma örnekleri

| # | Kötü yapılandırma | Etki | Düzeltme |
|---|---|---|---|
| CF-B1 | `delay_ms` negatif | Okuma imleci taşar | Alt sınır 0 |
| CF-B2 | `buffer_max_ms` tahsisten büyük | Taşma yazımı | prepare'da kırp |
| CF-B3 | Stereo ofset = tam gecikme | Sahne kayması aşırı | Küçük ofset, Q-D5 |
| CF-B4 | BPM = 0 ile senkron | Bölme hatası | BPM alt sınırı |
| CF-B5 | `mix` uygulanmadan çıkış | Yalnız kuru sinyal | Karışım zorunlu |
| CF-B6 | Örnek oranı değişince yeniden kurulmamış | Yanlış ms karşılığı | prepare tekrar çağır |

## §D Karşılaştırmalı

### D.1 Yaklaşım karşılaştırması

| Yaklaşım | Artı | Eksi | Uygulama durumu |
|---|---|---|---|
| Circular buffer + lineer | Basit, tahsissiz | Kesirli gecikmede hafif kayıp | Önerilen şema |
| Dual-tap enterpolasyon | Daha pürüzsüz | İki okuma/örnek | Alternatif |
| Sinc enterpolasyon | En iyi frekans yanıtı | Yüksek maliyet | Q-D9 açık |
| Konvolüsyon (impuls) | Gerçek mekân | CPU ağır, buffer büyük | komşu ölçüm modülü |
| JUCE `DelayLine` sarmalı | Hazır bileşen | Sürüm kanıtı yok | **VERIFICATION REQUIRED** |

### D.2 Gecikme okuma yöntemleri karşılaştırması

| Yöntem | Doğruluk | Maliyet | Uygun olduğu yer |
|---|---|---|---|
| Tam örnek okuma | Düşük (yuvarlama) | En düşük | Ritmik/ofset gecikme |
| Lineer enterpolasyon | Orta | Düşük | Genel kullanım (şema) |
| Cubic enterpolasyon | Yüksek | Orta | Modülasyonlu gecikme |
| Sinc enterpolasyon | En yüksek | Yüksek | Yayıncılık kalitesi (Q-D9) |

### D.3 Delay vs komşu modüller

| Özellik | delay | reverb | crossover |
|---|---|---|---|
| Temel yapı | Tek hatlı buffer | Çoklu yankı ağı | Bant ayrımı |
| Feedback var mı | Evet (Q-D3) | Dolaylı | Hayır |
| Buffer büyüklüğü | Gecikme kadar | Odası kadar | Çok küçük |
| Filtre içerir | Opsiyonel ton | Var | Ana işlev |
| Öncelik | Geçici konum | Uzamsal alan | Bant yönetimi |

### D.4 JS zarf deseni vs C++ delay eşdeğeri

| JS örneği (diskte) | C++ gecikme karşılığı | Not |
|---|---|---|
| `clamp` (`Satır:` 54) | Feedback/giriş sınırlama | Aynı amaç |
| `edgeSnap` eşiği (`Satır:` 57-59) | Kuyruk sonu yumuşama | Kenar davranışı |
| `VolumeCalculator` (`Satır:` 52) | `DelayParamState` | Parametre sahibi |
| Aşamalı ray (ramp) | Parametre geçiş rampası | Tıklama önleme |
| `Math.min(1, Math.max(0, value))` (`Satır:` 54) | `clamp(0, 0.99)` feedback | Sınır deseni |
| Varsayılan `0.5` (`Satır:` 31) | `mix = 0.5f` varsayılanı | Varsayılan deseni |

### D.5 Sürüm ve kanıt karşılaştırması

| İddia | Kaynak | Doğrulama yolu | Durum |
|---|---|---|---|
| JUCE 9 | kök AGENTS.md §12 | Sürüm kanıtı yok | **VERIFICATION REQUIRED** |
| ASIO SDK 2.3.4 | kök AGENTS.md §12 | Sürüm kanıtı yok | **VERIFICATION REQUIRED** |
| C++20 | kök AGENTS.md §5 | Derleyici bayrakları | sabit |
| `DryWetMixer` `mix` oranı | raw JUCE header (A.6) | URL 200 doğrulandı | envanter dışı |
| ConvolverNode konvolüsyon | MDN (A.6) | URL 200 doğrulandı | envanter dışı |
| Ölçüm 512 / 48 kHz | sartname | Ölçüm PLANNED | doğrulanmadı |
| MDN `ConvolverNode` sayfası erişilebilir | web (A.6) | HTTP 200 | doğrulandı (envanter dışı) |
| JUCE `DryWetMixer.h` kaynağı erişilebilir | web (A.6) | HTTP 200 | doğrulandı (envanter dışı) |

### D.6 Açık sorular (open questions)

| ID | Soru | Etki | Durum |
|---|---|---|---|
| Q-D1 | Varsayılan `delay_ms` nedir? | Yapılandırma | AÇIK |
| Q-D2 | Maksimum gecikme tampon sınırı nedir? | Bellek | AÇIK |
| Q-D3 | Feedback üst sınırı politikası? | Koruma | AÇIK |
| Q-D4 | Varsayılan `mix` oranı? | Ton | AÇIK |
| Q-D5 | Stereo ofset aralığı? | Sahne | AÇIK |
| Q-D6 | Modülasyon LFO parametreleri? | Karakter | AÇIK |
| Q-D7 | Tempo senkron modu (serbest/ms/noktalı)? | Ritim | AÇIK |
| Q-D8 | Tampon tahsis üst sınırı? | Bellek | AÇIK |
| Q-D9 | Enterpolasyon yöntemi seçimi? | Kalite/maliyet | AÇIK |
| Q-D10 | Kuyruk fade süresi ve eğrisi? | Güvenli kapanış | AÇIK |
| Q-D11 | Çıkış sınırlayıcı eşiği (dBFS)? | Klips koruması | AÇIK |
| Q-D12 | Denormal koruma yöntemi (FTZ/DC yok etme)? | CPU | AÇIK |
| Q-D13 | Mod bypass crossfade süresi? | Tıklama önleme | AÇIK |

### D.7 Dosya sözleşmesi (contract)

| Alan | Değer |
|---|---|
| Dosya | `delay-ornekler.md` |
| Node | `K004-dsp/delay/delay-ornekler` |
| Üst düğüm | `K004-dsp/delay` |
| Grup | `kod` |
| Eşlik dosyaları | `delay-kapsam.md`, `delay-dogrulama.md` |
| Eşlik eden | `delay-kapsam` (kapsam), `delay-dogrulama` (test) |
| Repo kanıtı | `coreplayer.volume.js` (`Satır:` 31/52/54/160) · `ApiMiddlewarePipeline.php` (`Satır:` 17/33) |
| Web kanıtı | MDN `ConvolverNode` · raw JUCE `juce_DryWetMixer.h` (A.6) |
| Disk durumu | C++ motoru YOK — şema/illüstrasyon |
| Ölçüm | 512 örnek / 48 kHz (sartname; ölçüm PLANNED) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
