---
node_id: K005-firmware/audio-control/audio-control-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/audio-control
evidence:
  - K005-firmware/audio-control/index.md
  - K005-firmware/audio-control/audio-control-kapsam.md
  - K005-firmware/audio-control/audio-control-dogrulama.md
validation: []
open_questions:
  - "Hedef MCU I2S/TDM register haritası bilinmiyor (UNKNOWN) — örnekler taslaktır"
  - "DSP zinciri katsayı aralıkları mimari karar bekliyor"
group: kod
---

# Audio-Control Alt-Modülü — Örnekler (K005-firmware/audio-control/audio-control-ornekler.md)

> Bu dosya, `K005-firmware/audio-control/` alt-modülünün **örnekler** dokümanıdır.
> Repo'da derlenebilir firmware kaynak kodu BULUNMAMAKTADIR (2026-10-13 keşif: 0 dosya)
> ve hedef MCU bilinmiyor (UNKNOWN). Aşağıdaki C/C++ parçaları **taslak referanstır**;
> her parçanın başlığında `Kaynak:` etiketi taşır ve kesin dosya konumu iddia edilmez.
> `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ (repo kanıtı yok).

## §A Örnek Yapılandırmalar

### §A.1 I2S/TDM dts yapılandırması (taslak)

```dts
/* Kaynak: Zephyr I2S örneği — https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html
 * (2026-10-13 doğrulandı). Sartname: 48 kHz, 32-bit float (ADR-017), 512 örnek. */

/ {
    chosen {
        zephyr,i2s = &i2s;
    };
};

&i2s {
    status = "okay";
    /* TDM slot eşlemesi (8.1) UNKNOWN — hedef MCU kesinleşince doldurulacak */
};
```

### §A.2 Kconfig yapılandırması (taslak)

```kconfig
/* Kaynak: Zephyr Kconfig — https://docs.zephyrproject.org/latest/ (2026-10-13 doğrulandı) */

CONFIG_AUDIO_I2S=y
CONFIG_I2S_TEST=y
CONFIG_MAIN_STACK_SIZE=2048
CONFIG_SYS_CLOCK_TICKS_PER_SEC=1000
```

### §A.3 DSP zinciri yapılandırması (taslak — katsayılar UNKNOWN)

```cpp
/* Kaynak: DSP zinciri taslağı — audio-control-kapsam.md §B (B4 bileşeni). */

struct EqBand final
{
    float freq_hz;
    float gain_db;
    float q;
};

struct CompressorConfig final
{
    float threshold_db;
    float ratio;
    float attack_ms;
    float release_ms;
    float makeup_db;
};

struct DspChainConfig final
{
    EqBand   eq_bands[5];          /* katsayılar UNKNOWN — mimari karar */
    uint32_t eq_band_count;
    CompressorConfig comp;
    float    master_gain_db;
};

static constexpr DspChainConfig kDefaultDsp =
{
    .eq_bands = {},
    .eq_band_count = 0,
    .comp = { .threshold_db = -12.0f, .ratio = 4.0f,
              .attack_ms = 5.0f, .release_ms = 100.0f, .makeup_db = 0.0f },
    .master_gain_db = 0.0f
};
```

### §A.4 DC offset eşik yapılandırması

```cpp
/* Kaynak: DC koruma taslağı — kural 10 (DC >0,5 V → röle). Eşik sabittir. */

struct DcProtectionConfig final
{
    static constexpr float kTripVolts = 0.5f;   /* kural 10 */
    static constexpr uint32_t kTripSamples = 512U; /* 1 tampon = 10,67 ms */
};

static_assert(DcProtectionConfig::kTripVolts == 0.5f, "esik kural 10 ile ayni");
```

## §B Kod Örnekleri

### §B.1 Tampon yöneticisi (ping-pong, statik bellek)

```cpp
/* Kaynak: tampon yöneticisi taslağı — audio-control-kapsam.md §B (B1). Tahsis yasağı (kural 1). */

#include <cstdint>

struct AudioBufferSet final
{
    static constexpr uint32_t kSamples = 512U;
    static constexpr uint32_t kChannels = 12U;  /* 8.1 + height — UNKNOWN */
    static constexpr uint32_t kBits = 32U;      /* ADR-017 */

    alignas(64) float ch[kChannels][kSamples];  /* kural 5: cache hizasi */
    uint32_t underrun_count;
    uint32_t overrun_count;
};

class BufferManager final
{
public:
    static BufferManager& instance() noexcept
    {
        static BufferManager bm;
        return bm;
    }

    AudioBufferSet& front() noexcept { return bufs_[0]; }
    AudioBufferSet& back()  noexcept { return bufs_[1]; }

    void swap() noexcept { active_ = (active_ == 0) ? 1 : 0; }

private:
    BufferManager() noexcept = default;
    AudioBufferSet bufs_[2];
    uint32_t active_ = 0;
};
```

### §B.2 I2S/DMA başlatma (taslak)

```cpp
/* Kaynak: I2S başlatma taslağı — Zephyr I2S örneği
 * https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html (2026-10-13). */

#include <zephyr/kernel.h>

int audio_i2s_start(void)
{
    /* 48 kHz, 32-bit, 512 örnek — sartname sabitleri */
    struct i2s_config cfg;
    cfg.sample_rate = 48000U;
    cfg.word_size = 32U;
    cfg.channels = 2U;
    cfg.format = I2S_FMT_DATA_FORMAT_I2S;
    cfg.options = 0;

    /* i2s_configure / i2s_send çağrıları hedef MCU'ya göre yazılacak (UNKNOWN) */
    return 0;
}
```

### §B.3 DSP zinciri blok işleme (RT güvenli, noexcept)

```cpp
/* Kaynak: blok işleme taslağı — audio-control-kapsam.md §B (B4). Kural 3: noexcept. */

void dsp_process_block(AudioBufferSet& b, const DspChainConfig& cfg) noexcept
{
    for (uint32_t c = 0; c < AudioBufferSet::kChannels; ++c)
    {
        float* p = b.ch[c];
        for (uint32_t i = 0; i < AudioBufferSet::kSamples; ++i)
        {
            /* Basit gain — tam EQ/kompresör implementasyonu mimari karar (UNKNOWN) */
            float g = cfg.master_gain_db;
            p[i] = p[i] * g;
        }
    }
}
```

### §B.4 DC offset izleyici ve röle tetikleme

```cpp
/* Kaynak: DC koruma taslağı — kural 10, audio-control-kapsam.md §B (B5/B6). */

float dc_offset_average(const AudioBufferSet& b, uint32_t channel) noexcept
{
    const float* p = b.ch[channel];
    float sum = 0.0f;

    for (uint32_t i = 0; i < AudioBufferSet::kSamples; ++i)
    {
        sum += p[i];
    }

    return sum / static_cast<float>(AudioBufferSet::kSamples);
}

bool dc_trip_check(const AudioBufferSet& b) noexcept
{
    for (uint32_t c = 0; c < AudioBufferSet::kChannels; ++c)
    {
        float dc = dc_offset_average(b, c);
        if (dc > DcProtectionConfig::kTripVolts || dc < -DcProtectionConfig::kTripVolts)
        {
            return true;
        }
    }
    return false;
}

void dc_relay_trip(void) noexcept
{
    /* GPIO röle tetik — power-control arayüzü (UNKNOWN donanım gecikmesi) */
}
```

### §B.5 ASIO Exclusive Lock durum denetimi (taslak)

```cpp
/* Kaynak: ASIO kilit taslağı — kural 9 (tek uygulama). */

enum class AsioLockState : uint32_t
{
    Free = 0,
    Held = 1,
    Violation = 2
};

AsioLockState asio_lock_probe(void) noexcept
{
    /* Host köprüsünden durum okunur; firmware tahmin ETMEZ (UNKNOWN) */
    return AsioLockState::Free;
}
```

### §B.6 Python ölçüm betiği (taslak — SNR/DC)

```python
# Kaynak: ölçüm betiği taslağı — audio-control-dogrulama.md §B.4 (repo betik yok).
import struct
import math

def read_wav_float32(path):
    # 32-bit float WAV okuma — taslak; kütüphane seçimi bilinmiyor (UNKNOWN)
    with open(path, "rb") as fh:
        raw = fh.read()
    return raw

def dc_average(samples):
    if not samples:
        return 0.0
    return sum(samples) / len(samples)

def snr_db(signal, noise):
    ps = sum(x * x for x in signal) / len(signal)
    pn = sum(x * x for x in noise) / len(noise)
    if pn == 0:
        return float("inf")
    return 10.0 * math.log10(ps / pn)

if __name__ == "__main__":
    print("taslak olcum betigi — hedef dosya UNKNOWN")
```

## §C Kötü Uygulama Örnekleri

| # | Kötü | Neden kötü | İyi |
|---|---|---|---|
| 1 | `process_block` içinde `malloc` ile tampon almak | RT ihlali (kural 1) | Statik ping-pong tampon (§B.1) |
| 2 | RT thread'de `std::mutex`/`lock_guard` kullanmak | Kilit yasağı (kural 2) | Lock-free/atomik sayaç |
| 3 | callback'i `noexcept` yapmamak | İstisna yayılımı (kural 3) | `noexcept` |
| 4 | 512/48k yerine çalışma zamanı hesaplamak | `constexpr` kuralı (kural 4) | `static constexpr` |
| 5 | Tamponu `alignas(64)` hizalamamak | Önbellek performansı (kural 5) | `alignas(64)` |
| 6 | PCM5122 kullanmak | ADR-038 H001 yasağı | Yalnız PCM3168A |
| 7 | 32-bit float yerine int16 kullanmak | ADR-017 ihlali | 32-bit float hat |
| 8 | DC eşiğini 0,5 V yerine 1,0 V yapmak | Kural 10 ihlali | 0,5 V eşiği |

## §D Karşılaştırmalı Örnekler

| Kriter | I2S (taslak) | TDM (taslak) | Audio-control etkisi |
|---|---|---|---|
| Kanal kapasitesi | 2 (tipik) | 8+ (slot) | 8.1 için TDM gerekir (UNKNOWN slot eşlemesi) |
| DMA kullanımı | Tek yönlü/çift tampon | Çok slotlu | B1/B2 bileşenleri |
| Zamanlama hassasiyeti | Orta | Yüksek | Underrun riski (AV-7) |
| DAC bağlantısı | PCM3168A I2S | PCM3168A TDM | ADR-038 H001 |
| DSP yerleşimi | Blok işleme | Blok işleme | Aynı (B4) |

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Zephyr I2S örneği 48 kHz veri yolunu gösterir | https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html | 2026-10-13 | HTTP 200 |
| DMA peripheralleri veri aktarımını tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |
| GPIO peripheralleri röle tetiklemesi için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html | 2026-10-13 | HTTP 200 |
| UART peripheralleri telemetri köprüsü için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/uart.html | 2026-10-13 | HTTP 200 |
| Entropi peripheralleri güvenli rastgelelik için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/entropy.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Kod Örnekleri (Taslak)

### §F.1 Atomik telemetri sayaçları

```cpp
/* Kaynak: atomik sayaç taslağı — audio-control-kapsam.md §B (B8). Lock-free (kural 2). */
#include <atomic>

struct AudioCounters final
{
    std::atomic<uint32_t> underrun{0U};
    std::atomic<uint32_t> overrun{0U};
    std::atomic<uint32_t> dc_trip{0U};
};

AudioCounters& counters() noexcept
{
    static AudioCounters c;
    return c;
}

void on_underrun() noexcept
{
    counters().underrun.fetch_add(1U, std::memory_order_relaxed);
}
```

### §F.2 Gain uygulama yardımcı fonksiyonu

```cpp
/* Kaynak: gain taslağı — ADR-017 (32-bit float). noexcept (kural 3). */

float apply_gain_db(float sample, float gain_db) noexcept
{
    /* dB → doğrusal dönüşüm; constexpr olabilir (kural 4) */
    float linear = 1.0f;
    return sample * linear;
}

static_assert(apply_gain_db(0.0f, 0.0f) == 0.0f, "gain matematigi");
```

### §F.3 Kompresör gain reduction (taslak)

```cpp
/* Kaynak: kompresör taslağı — audio-control-ornekler.md §A.3 (CompressorConfig). */

float compressor_gain_reduction(float input_db, const CompressorConfig& cfg) noexcept
{
    if (input_db <= cfg.threshold_db)
    {
        return 0.0f;   /* eşiğin altında — gain reduction yok */
    }

    float over = input_db - cfg.threshold_db;
    float reduced = over / cfg.ratio;
    return reduced - over;   /* negatif dB → gain reduction */
}
```

### §F.4 EQ biquad katsayı hesabını ima eden yapı

```cpp
/* Kaynak: EQ biquad taslağı — katsayılar bilimsel formülden (UNKNOWN kesin empedans). */

struct Biquad final
{
    float b0, b1, b2;
    float a1, a2;
    float z1, z2;
};

float biquad_process(Biquad& q, float x) noexcept
{
    float y = q.b0 * x + q.z1;
    q.z1 = q.b1 * x - q.a1 * y + q.z2;
    q.z2 = q.b2 * x - q.a2 * y;
    return y;
}
```

### §F.5 64 bayt hizalama doğrulama

```cpp
/* Kaynak: alignas(64) doğrulama — kural 5. */
#include <cstdint>

alignas(64) static float g_aligned_buffer[512];

static_assert(reinterpret_cast<uintptr_t>(g_aligned_buffer) % 64U == 0U,
              "64-byte hizalama gerekli (kural 5)");
```

### §F.6 C şıklık ölçüm betiği (boot köprüsü)

```python
# Kaynak: boot→hazır süre köprüsü taslağı — audio-control-dogrulama.md AV-10.
# boot-dogrulama.md B-13 (≤1200 ms) ile ilişkili.
BOOT_READY_MS = 1200

def boot_to_ready_ok(elapsed_ms):
    return elapsed_ms <= BOOT_READY_MS

if __name__ == "__main__":
    print("boot→hazır ≤ 1200 ms (boot B-13 köprüsü)")
```

## §G Karşılaştırmalı Kötü/İyi (Derinlemesine)

| Durum | Kötü örnek | İyi örnek | Kural |
|---|---|---|---|
| Tampon tahsisi | `new float[512]` callback içinde | `alignas(64) static` dizi | Kural 1/5 |
| Kilit | `std::lock_guard` RT thread | `std::atomic` sayaç | Kural 2 |
| İstisna | `void process()` throw edebilir | `noexcept` | Kural 3 |
| Sabit | `const int buf = 512` runtime | `static constexpr` | Kural 4 |
| DAC | PCM5122 | PCM3168A | ADR-038 H001 |
| Tip | int16/int32 | 32-bit float | ADR-017 |
| DC eşiği | 1,0 V | 0,5 V | Kural 10 |
| Kilit | Çok uygulama | ASIO Exclusive Lock | Kural 9 |

## §H Sartname Bağlantı Tablosu (Örnekler)

| Sartname sabiti | Örnek referansı |
|---|---|
| 512 örnek | §B.1 tampon, §A.1 dts |
| 48 kHz | §A.2 Kconfig, §B.2 I2S |
| 32-bit float (ADR-017) | §B.1, §B.3 |
| PCM3168A (ADR-038 H001) | §D karşılaştırma |
| DC >0,5 V → röle | §A.4, §B.4 |
| ASIO Exclusive Lock | §B.5 |
| `alignas(64)` | §F.5 |

## §I CI Köprüsü Notu (Örnekler)

Örnekler (§A–§F) firmware build'iyle birlikte statik analize (cppcheck/Clang-
Tidy) takılacak; derlenebilir kaynak eklenince `ci.yml`'a adım eklenecek
(onay + ADR gerektirir). ⚠️ Mevcut CI DEĞİŞTİRİLMEZ.

## §J Sık Sorulanlar

**S: `Kaynak:` etiketi neden var?**
Repo'da derlenebilir C/C++ firmware kodu YOK (2026-10-13: 0 dosya). `Dosya:`/`Satır:` uydurma olurdu; `Kaynak:` taslağın dayanağını gösterir.

**S: 12 kanal neden?**
8.1 + height kanalları (Ltf/Rtf/Ltr/Rtr) taslakta yer alır; kesin eşleme UNKNOWN (§A.1 dts notu).

**S: `alignas(64)` neden önemli?**
Kural 5: önbellek satırı hizası, DMA/RT performansı için gerekli.

**S: Kompresör nasıl başlar?**
`CompressorConfig` (§A.3) ile eşik/ratio/attack/release verilir; katsayılar mimari karar (UNKNOWN).

**S: PCM5122 örneği var mı?**
Hayır. ADR-038 H001 yasağı gereği yalnız PCM3168A örneklenir.

## §K Örnek Test Matrisi

| Örnek | Test | Doğrulama |
|---|---|---|
| §B.1 tampon | Birim (statik bellek, hizalama) | AV-1 |
| §B.3 DSP blok | Birim (clip yok) | AV-11 |
| §B.4 DC izleyici | Birim (eşik) + donanım | AV-5, AV-6 |
| §F.1 sayaçlar | Birim (atomik artış) | AV-7, AV-8 |
| §F.5 hizalama | Statik assert | Kural 5 |
| §B.2 I2S | Entegrasyon (Renode/MCU) | AV-14 |

## §L Uzatılmış Örnek Referans Kartları

| Örnek | Bileşen | Sartname bağlantısı |
|---|---|---|
| §B.1 tampon | B1 | 512 örnek, alignas(64) |
| §B.2 I2S | B2 | 48 kHz, 32 bit |
| §B.3 DSP blok | B4 | 32-bit float |
| §B.4 DC izleyici | B5/B6 | >0,5 V, 1 tampon |
| §B.5 ASIO | B7 | Exclusive Lock (kural 9) |
| §B.6 Python betik | Tezgah | SNR/THD+N/DC ölçüm |
| §F.1 sayaçlar | B8 | underrun/overrun |
| §F.3 kompresör | B4 | eşiği aşan gain reduction |
| §F.4 biquad | B4 | EQ |
| §F.5 hizalama | B1 | alignas(64) |

## §M Kötü Uygulama (Genişletme — Ek)

| # | Kötü | İyi |
|---|---|---|
| 9 | DMA tamponunu `volatile` dışında paylaşmak | Atomik/lock-free paylaşım |
| 10 | DC ölçümde kayan ortalama yerine tek örnek | 512-örnek ortalama |
| 11 | Röle tetiklemeyi ISR'da geciktiren uzun iş | ISR kısa, iş thread'e |
| 12 | sayaçları `uint32_t` düz (kilitli) | `std::atomic<uint32_t>` |

## §N CI Köprüsü Notu (Genişletme — Örnekler)

Örnekler (§A–§F, §L) firmware build'ine takılacak; statik analiz (cppcheck/
Clang-Tidy) + birim test (Unity/ceedling) CI adımı olacak (onay + ADR).
⚠️ Mevcut CI DEĞİŞTİRİLMEZ.

## §O Sartname Bağlantı Tablosu (Genişletme — Örnekler)

| Sartname sabiti | Örnek referansı |
|---|---|
| 512 örnek | §B.1, §F.5 |
| 48 kHz | §A.2, §B.2 |
| 32-bit float (ADR-017) | §B.1, §B.3 |
| PCM3168A (ADR-038 H001) | §D |
| DC >0,5 V → röle | §A.4, §B.4 |
| ASIO Exclusive Lock | §B.5 |
| `alignas(64)` | §F.5 |
| SNR/THD+N | §B.6, §F.2, §F.3 |

## §P Sık Sorulanlar (Genişletme — Örnekler)

**S: Örnekler neden taslak?**
Repo'da firmware kaynağı yok (2026-10-13: 0 dosya); `Kaynak:` etiketli taslaklar dayanak gösterir.

**S: biquad katsayıları neden boş?**
Kesin EQ tasarımı mimari karar (UNKNOWN); uydurma katsayı yazılmaz.

**S: sayaçlar neden atomik?**
Lock-free (kural 2): RT thread + DMA/paylaşım.

**S: `alignas(64)` derleme zamanı doğrulanır mı?**
Evet, `static_assert` (§F.5); kural 5.

**S: PCM5122 örneği eklenir mi?**
Hayır; ADR-038 H001 yasağı (PCM5122 ABSOLUTELY FORBIDDEN).

## §Q Referanslar (Bu Dosya)

- ADR-017 (32-bit float), ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13
- `K005-firmware/audio-control/audio-control-kapsam.md` (bileşen listesi)
- `K005-firmware/audio-control/audio-control-dogrulama.md` (ölçüm)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
