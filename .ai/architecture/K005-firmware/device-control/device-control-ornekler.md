---
node_id: K005-firmware/device-control/device-control-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/device-control
evidence:
  - K005-firmware/device-control/index.md
  - K005-firmware/device-control/device-control-kapsam.md
  - K005-firmware/device-control/device-control-dogrulama.md
validation: []
open_questions:
  - "Hedef MCU peripheral register addresses bilinmiyor (UNKNOWN) — örnekler taslaktır"
  - "PCM3168A register haritası doğrulanmış kaynaktan alınacak (ADR-038 H001)"
group: kod
---

# Device-Control Alt-Modülü — Örnekler (K005-firmware/device-control/device-control-ornekler.md)

> Bu dosya, `K005-firmware/device-control/` alt-modülünün **örnekler** dokümanıdır.
> Repo'da derlenebilir firmware kaynak kodu BULUNMAMAKTADIR (2026-10-13 keşif: 0
> dosya) ve hedef MCU bilinmiyor (UNKNOWN). Aşağıdaki C/C++ parçaları **taslak
> referanstır**; her parçanın başlığında `Kaynak:` etiketi taşır ve kesin dosya
> konumu iddia edilmez. `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ.

## §A Örnek Yapılandırmalar

### §A.1 I2C dts yapılandırması (taslak)

```dts
/* Kaynak: Zephyr I2C peripheraller — https://docs.zephyrproject.org/latest/hardware/peripherals/i2c.html
 * (2026-10-13 doğrulandı). PCM3168A register erişimi (ADR-038 H001). */

&i2c {
    status = "okay";
    clock-frequency = <I2C_BITRATE_STANDARD>;
    /* PCM3168A register adresleri — doğrulanmış kaynaktan (UNKNOWN) */
};
```

### §A.2 GPIO dts yapılandırması (taslak — röle)

```dts
/* Kaynak: Zephyr GPIO peripheraller — https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html
 * (2026-10-13 doğrulandı). DC koruma rölesi (kural 10). */

/ {
    leds {
        compatible = "gpio-leds";
        relay_dc {
            gpios = <&gpio0 13 GPIO_ACTIVE_LOW>;
            /* DC >0,5 V → röle tetik (kural 10) */
        };
    };
};
```

### §A.3 PWM dts yapılandırması (taslak)

```dts
/* Kaynak: Zephyr PWM peripheraller — https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html
 * (2026-10-13 doğrulandı). */

&pwm {
    status = "okay";
    /* Boyut/sürücü — UNKNOWN kesin duty/frekans */
};
```

### §A.4 Counter dts yapılandırması (taslak)

```dts
/* Kaynak: Zephyr Counter peripheraller — https://docs.zephyrproject.org/latest/hardware/peripherals/counter.html
 * (2026-10-13 doğrulandı). */

&counter {
    status = "okay";
    /* Sayaç ölçümü — UNKNOWN kesin zamanlayıcı */
};
```

## §B Kod Örnekleri

### §B.1 Bus soyutlama (taslak — I2C/SPI/GPIO tek API)

```cpp
/* Kaynak: bus soyutlama taslağı — device-control-kapsam.md §B (B1). Kural 1/2/3. */
#include <cstdint>

enum class BusType : uint32_t { I2c = 0, Spi = 1, Gpio = 2, Uart = 3, Pwm = 4, Counter = 5 };

class DeviceBus final
{
public:
    static DeviceBus& instance() noexcept
    {
        static DeviceBus bus;
        return bus;
    }

    int write(BusType bus, uint32_t addr, const uint8_t* data, uint32_t len) noexcept
    {
        /* MCU HAL write — hedef MCU UNKNOWN */
        (void)bus; (void)addr; (void)data; (void)len;
        return 0;
    }

    int read(BusType bus, uint32_t addr, uint8_t* buf, uint32_t len) noexcept
    {
        /* MCU HAL read — hedef MCU UNKNOWN */
        (void)bus; (void)addr; (void)buf; (void)len;
        return 0;
    }

    int gpio_set(uint32_t pin, bool level) noexcept
    {
        /* MCU HAL GPIO — hedef MCU UNKNOWN */
        (void)pin; (void)level;
        return 0;
    }

private:
    DeviceBus() noexcept = default;
};
```

### §B.2 Cihaz kayıt modeli (taslak — deterministik sıra)

```cpp
/* Kaynak: cihaz kayıt taslağı — device-control-kapsam.md §B (B2). Boot B-12 köprüsü. */
#include <cstdint>

struct DeviceEntry final
{
    const char* name;
    uint32_t    init_order;
};

static const DeviceEntry kDeviceTable[] =
{
    { "i2c_bus",  0U },
    { "spi_bus",  1U },
    { "dac",      2U },   /* PCM3168A — ADR-038 H001 */
    { "relay",    3U },   /* DC koruma (kural 10) */
    { "pwm",      4U },
    { "counter",  5U }
};

static constexpr uint32_t kDeviceCount =
    static_cast<uint32_t>(sizeof(kDeviceTable) / sizeof(kDeviceTable[0]));

int device_init_all(void) noexcept
{
    for (uint32_t i = 0; i < kDeviceCount; ++i)
    {
        /* deterministik sıra: init_order artan */
    }
    return 0;
}
```

### §B.3 DAC register yazımı (taslak — PCM3168A)

```cpp
/* Kaynak: DAC register taslağı — device-control-kapsam.md §B (B3). ADR-038 H001 (PCM5122 YASAK).
 * Register adresleri doğrulanmış kaynaktan (UNKNOWN). */

int dac_configure(void) noexcept
{
    /* I2C write: PCM3168A register yapılandırması (UNKNOWN register adresleri) */
    return DeviceBus::instance().write(BusType::I2c, 0U, nullptr, 0U);
}
```

### §B.4 Röle/DC GPIO (taslak — kural 10)

```cpp
/* Kaynak: röle GPIO taslağı — device-control-kapsam.md §B (B4). Kural 10: DC >0,5 V → röle. */

constexpr uint32_t kRelayGpioPin = 13U;  /* örnek — hedef MCU UNKNOWN */

void relay_set(bool engage) noexcept
{
    /* engage=true → röle çek (DC koruma); aktif-düşük (§A.2 dts) */
    DeviceBus::instance().gpio_set(kRelayGpioPin, !engage);
}
```

### §B.5 Zamanlayıcı tabanlı tarama (taslak — lock-free)

```cpp
/* Kaynak: tarama taslağı — device-control-kapsam.md §B (B5). Lock-free (kural 2). */
#include <atomic>

struct ScanState final
{
    std::atomic<uint32_t> tick{0U};
};

ScanState& scan_state() noexcept
{
    static ScanState s;
    return s;
}

void scan_tick(void) noexcept
{
    scan_state().tick.fetch_add(1U, std::memory_order_relaxed);
}
```

### §B.6 Python tarama/olay doğrulama betiği (taslak)

```python
# Kaynak: tarama doğrulama betiği taslağı — device-control-dogrulama.md §C.1.
# Repo betik yok; taslak.
MAX_LATENCY_MS = 10.67

def event_latency_ok(olay_zamani_s, islenme_zamani_s):
    gecikme_ms = (islenme_zamani_s - olay_zamani_s) * 1000.0
    return gecikme_ms <= MAX_LATENCY_MS

if __name__ == "__main__":
    print("cihaz olayi gecikmesi <= 10.67 ms (1 tampon)")
```

## §C Kötü Uygulama Örnekleri

| # | Kötü | Neden kötü | İyi |
|---|---|---|---|
| 1 | `malloc` ile cihaz listesi oluşturmak | RT ihlali (kural 1) | Statik tablo (§B.2) |
| 2 | Bus yazımında `std::mutex` | Kilit yasağı (kural 2) | Lock-free/atomik |
| 3 | Bus callback'ini `noexcept` yapmamak | İstisna yayılımı (kural 3) | `noexcept` |
| 4 | Cihaz adresini çalışma zamanı hesaplamak | `constexpr` kuralı (kural 4) | `constexpr` |
| 5 | `alignas(64)` yok | Önbellek (kural 5) | `alignas(64)` |
| 6 | PCM5122 kullanmak | ADR-038 H001 yasağı | Yalnız PCM3168A |
| 7 | Başlatma sırasını rastgele yapmak | Boot B-12 ihlali | Deterministik sıra (§B.2) |
| 8 | Tahsis yapan ISR | RT ihlali | Statik tampon |

## §D Karşılaştırmalı Örnekler

| Kriter | I2C (taslak) | SPI (taslak) | device-control etkisi |
|---|---|---|---|
| Hız | Orta (100–400 kHz) | Yüksek | DAC register için I2C yeterli |
| Kanal | Çok cihaz (adres) | Tek cihaz (CS) | PCM3168A I2C adresi (UNKNOWN) |
| Register erişimi | Yavaş/uzun | Hızlı/kısa | B3 bileşeni |
| Zamanlama hassasiyeti | Orta | Yüksek | Olay gecikmesi (DV-8) |
| DMA | Sınırlı | Mümkün | Telemetri (B7) |

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| I2C peripheral register erişimi peripherallerdedir | https://docs.zephyrproject.org/latest/hardware/peripherals/i2c.html | 2026-10-13 | HTTP 200 |
| SPI peripheral register erişimi peripherallerdedir | https://docs.zephyrproject.org/latest/hardware/peripherals/spi.html | 2026-10-13 | HTTP 200 |
| GPIO peripheral röle çıkışı için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html | 2026-10-13 | HTTP 200 |
| PWM peripheral sürücü zamanlaması için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html | 2026-10-13 | HTTP 200 |
| Counter peripheral sayaç ölçümü için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/counter.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Kod Örnekleri (Taslak)

### §F.1 Atomik olay sayaçları

```cpp
/* Kaynak: atomik sayaç taslağı — device-control-kapsam.md §B (B7). Lock-free (kural 2). */
#include <atomic>

struct DeviceCounters final
{
    std::atomic<uint32_t> nack{0U};
    std::atomic<uint32_t> timeout{0U};
    std::atomic<uint32_t> events{0U};
};

DeviceCounters& device_counters() noexcept
{
    static DeviceCounters c;
    return c;
}

void on_bus_nack() noexcept
{
    device_counters().nack.fetch_add(1U, std::memory_order_relaxed);
}
```

### §F.2 Register adresi constexpr tanımı (taslak)

```cpp
/* Kaynak: register constexpr taslağı — kural 4 (constexpr). ADR-038 H001 (PCM5122 YASAK).
 * Register adresleri doğrulanmış kaynaktan (UNKNOWN). */

constexpr uint32_t kPcm3168aI2cAddress = 0x00U;  /* UNKNOWN — doğrulanacak */

static_assert(kPcm3168aI2cAddress < 0x80U, "I2C 7-bit adres araligi");
```

### §F.3 64 bayt hizalama doğrulama (taslak)

```cpp
/* Kaynak: alignas(64) doğrulama — kural 5. */
#include <cstdint>

alignas(64) static uint8_t g_device_ring_buffer[256];

static_assert(reinterpret_cast<uintptr_t>(g_device_ring_buffer) % 64U == 0U,
              "64-byte hizalama gerekli (kural 5)");
```

### §F.4 noThrow/bus yardımcı (taslak)

```cpp
/* Kaynak: bus yardımcı taslağı — device-control-kapsam.md §C.1. noexcept (kural 3). */

int device_write_pcm3168a(const uint8_t* data, uint32_t len) noexcept
{
    return DeviceBus::instance().write(BusType::I2c, kPcm3168aI2cAddress, data, len);
}
```

### §F.5 C şıklık başlatma sırası doğrulama betiği (boot köprüsü)

```python
# Kaynak: başlatma sırası köprüsü taslağı — device-control-dogrulama.md DV-7.
def init_order_deterministic(sira_listesi):
    return sira_listesi == sorted(sira_listesi)

if __name__ == "__main__":
    print("baslatma sirasi deterministik (boot B-12 köprüsü)")
```

## §G Karşılaştırmalı Kötü/İyi (Derinlemesine — Genişletme)

| Durum | Kötü örnek | İyi örnek | Kural |
|---|---|---|---|
| Bus listesi | `new DeviceEntry[n]` | Statik dizi (§B.2) | Kural 1 |
| Kilit | `std::lock_guard` RT thread | `std::atomic` sayaç | Kural 2 |
| İstisna | `write()` throw edebilir | `noexcept` | Kural 3 |
| Sabit | `const int addr = 0x40` runtime | `constexpr` | Kural 4 |
| Hizalama | `uint8_t buf[256]` | `alignas(64) uint8_t` | Kural 5 |
| DAC | PCM5122 | PCM3168A | ADR-038 H001 |
| Sıra | Rastgele init | Deterministik sıra | Boot B-12 |

## §H Sartname Bağlantı Tablosu (Genişletme — Örnekler)

| Sartname sabiti | Örnek referansı |
|---|---|
| 512 örnek / 48 kHz | §B.5 tarama |
| PCM3168A (ADR-038 H001) | §A.1 dts, §B.3, §F.2 |
| DC >0,5 V → röle | §A.2 dts, §B.4 |
| Boot deterministik sıra | §B.2, §F.5 |
| `constexpr` | §F.2 |
| `alignas(64)` | §F.3 |
| Tahsis yok (kural 1) | §B.2 |

## §I CI Köprüsü Notu (Genişletme — Örnekler)

Örnekler (§A–§F) firmware build'ine takılacak; statik analiz + birim test CI
adımı olacak (onay + ADR gerektirir). ⚠️ Mevcut CI DEĞİŞTİRİLMEZ.

## §J Sık Sorulanlar (Genişletme — Örnekler)

**S: Örnekler neden taslak?**
Repo'da firmware kaynağı yok (2026-10-13: 0 dosya); `Kaynak:` etiketli taslaklar dayanak gösterir.

**S: Register adresleri neden 0x00?**
Uydurma yasağı; `constexpr` placeholder (UNKNOWN) — doğrulanmış kaynaktan güncellenecek (ADR-038 H001).

**S: Röle GPIO neden aktif-düşük?**
§A.2 dts taslağı; gerçek polarity hedef MCU'ya göre (UNKNOWN).

**S: sayaçlar neden atomik?**
Lock-free (kural 2): RT thread + DMA/paylaşım.

**S: PCM5122 örneği eklenir mi?**
Hayır; ADR-038 H001 yasağı (PCM5122 ABSOLUTELY FORBIDDEN).

## §K Örnek Test Matrisi (Genişletme — Son)

| Örnek | Test | Doğrulama |
|---|---|---|
| §B.1 bus | Birim (mock) | DV-1, DV-2 |
| §B.2 tablo | Birim (sıra) | DV-7 |
| §B.3 DAC register | Entegrasyon | DV-6 |
| §B.4 röle | Donanım | DV-3 |
| §F.1 sayaçlar | Birim (atomik) | DV-12 |
| §F.3 hizalama | Statik assert | Kural 5 |

## §L Referanslar (Örnekler — Son)

- ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (bileşen listesi)
- `K005-firmware/device-control/device-control-dogrulama.md` (ölçüm)

## §M Uzatılmış Örnek Referans Kartları (Genişletme)

| Örnek | Bileşen | Sartname bağlantısı |
|---|---|---|
| §A.1 I2C dts | B1/B3 | PCM3168A (ADR-038 H001) |
| §A.2 GPIO dts | B4 | DC koruma rölesi (kural 10) |
| §A.3 PWM dts | B1 | Boyut/sürücü |
| §A.4 Counter dts | B1 | Sayaç ölçümü |
| §B.1 bus | B1 | Tahsis yok (kural 1) |
| §B.2 tablo | B2 | Deterministik sıra (boot B-12) |
| §B.3 DAC register | B3 | PCM3168A |
| §B.4 röle | B4 | DC koruma |
| §B.5 tarama | B5 | Lock-free (kural 2) |
| §F.1 sayaçlar | B7 | atomik |
| §F.2 register | B3 | constexpr (kural 4) |
| §F.3 hizalama | — | alignas(64) (kural 5) |

## §N Kötü Uygulama (Genişletme — Ek)

| # | Kötü | İyi |
|---|---|---|
| 9 | Bus timeout'unda sonsuz döngü | Timeout + hata kodu |
| 10 | Register adresini string'te tutmak | `constexpr` uint32_t |
| 11 | Olay queue'sunu dinamik büyütmek | Statik ring-buffer |
| 12 | Başlatma hatasını yutmak | Hata kodu + telemetri |

## §O CI Köprüsü Notu (Genişletme — Son)

Örnekler firmware build'ine takılacak; statik analiz + birim test CI adımı
olacak (onay + ADR gerektirir). ⚠️ Mevcut CI DEĞİŞTİRİLMEZ.

## §P Sartname Bağlantı Tablosu (Genişletme — Son)

| Sartname sabiti | Örnek referansı |
|---|---|
| PCM3168A (ADR-038 H001) | §F.2, §F.4 |
| DC >0,5 V → röle | §B.4 |
| Boot deterministik sıra | §F.5 |
| `noexcept` (kural 3) | §F.4 |
| Lock-free (kural 2) | §F.1, §B.5 |

## §Q Sık Sorulanlar (Genişletme — Son)

**S: Register adresi neden placeholder?**
Uydurma yasağı; doğrulanmış kaynaktan (ADR-038 H001) `constexpr` güncellenecek.

**S: Olay queue boyutu neden UNKNOWN?**
Donanım/bağlılık zincirine bağlı; uydurma boyut yazılmaz.

**S: Boot başarısızsa cihaz ne yapar?**
Hiç başlamaz (fail-safe).

**S: PCM5122 örneği eklenir mi?**
Hayır; ADR-038 H001 yasağı.

## §R Örnek Test Matrisi (Genişletme — Son Ek)

| Örnek | Test | Doğrulama |
|---|---|---|
| §F.4 bus yardımcı | Birim (mock) | DV-1 |
| §F.5 sıra | Birim | DV-7 |
| §A.1 I2C dts | Entegrasyon | DV-6 |
| §A.2 GPIO dts | Donanım | DV-3 |

## §S Referanslar (Örnekler — Son Ek)

- ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (bileşen listesi)
- `K005-firmware/device-control/device-control-dogrulama.md` (ölçüm)

## §T Örnek Sonu Not (Genişletme — Son Ek)

Örnekler `Kaynak:` etiketli taslaktır (repo'da firmware kaynağı yok). Register
adresleri placeholder (UNKNOWN) — doğrulanmış kaynaktan güncellenecek (ADR-038
H001). PCM5122 ASLA kullanılmaz. Tahsis/lock-free/noexcept/constexpr/alignas
kuralları (1–5) örneklerde uygulanır.

## §U Örnek Sartname Bağlantı Grafiği (Örnekler — Son Ek)

```text
  sartname.md/ADR
        |
        +-- 512 örnek / 48 kHz --> §B.5 tarama (tick)
        +-- PCM3168A (ADR-038)  --> §B.3 / §F.2 / §F.4 (DAC register)
        +-- DC >0,5 V           --> §B.4 / §A.2 dts (röle)
        +-- Boot deterministik  --> §B.2 / §F.5 (başlatma sırası)
        +-- kural 1 (tahsis)    --> §B.2 statik tablo
        +-- kural 2 (lock-free) --> §F.1 atomik sayaç
        +-- kural 4 (constexpr) --> §F.2 register
        +-- kural 5 (alignas)   --> §F.3 hizalama
```

## §V Örnek Sonu Referanslar (Örnekler — Son Ek)

- ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (bileşen listesi B1..B7)
- `K005-firmware/device-control/device-control-dogrulama.md` (ölçüm DV-1..DV-12)
- `K005-firmware/boot/boot-kapsam.md` (boot bağımlılığı)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
