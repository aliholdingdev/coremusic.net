---
node_id: K005-firmware/device-control/device-control-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/boot
evidence:
  - K005-firmware/device-control/index.md
  - "repo: shared/src/AI/AIOrchestrator.php satır 36 — 'dsp-fw' agent routing (firmware != PHP host)"
validation: []
open_questions:
  - "Hedef MCU peripheral set'i bilinmiyor (UNKNOWN) — I2C/SPI/GPIO sayısı kesinleşmedi"
  - "PCM3168A register haritası doğrulanmış kaynaktan alınacak (ADR-038 H001)"
  - "Cihaz keşfi devicetree üzerinden mi, sabit tablo mu — mimari karar"
group: mimari
---

# Device-Control Alt-Modülü — Kapsam (K005-firmware/device-control/device-control-kapsam.md)

> Bu dosya, `K005-firmware/device-control/` alt-modülünün **kapsam** dokümanıdır.
> Repo'da derlenebilir C/C++ firmware kodu BULUNMAMAKTADIR (2026-10-13 keşif:
> 0 dosya) ve hedef MCU bilinmiyor (UNKNOWN) — bileşen listesi ve arayüzler
> **mimari taslaktır**; kesin register/kod referansı verilmez.

## §A Kapsam

### §A.1 Kapsar

- I2C/SPI/GPIO/UART/PWM/counter sürücü soyutlaması (tek API altında)
- Cihaz keşfi ve kayıt modeli (devicetree/dts veya sabit tablo — UNKNOWN)
- PCM3168A DAC register erişimi (audio-control adına; ADR-038 H001)
- Röle/DC koruma GPIO çıkışı (power-control adına)
- Boot sonrası cihaz başlatma sırası (boot modülüne bağımlı)
- Zamanlayıcı tabanlı tarama (polling) ve olay (interrupt) modu
- Ring-buffer üzerinden runtime telemetri köprüsü
- Hızlı yanıt: cihaz olayları ≤ 1 tampon (10,67 ms) içinde işlenir

### §A.2 KAPSAMAZ

- DSP/EQ/kompresör işleme mantığı (audio-control alt-modülü)
- DC offset izleme algoritması (audio-control alt-modülü)
- Güç amplifikatörü/termal tasarım (`.ai/electronics/`)
- OTA güncelleme/paketleme (update alt-modülü)
- Firmware kripto/secure boot (security alt-modülü)
- Telemetri saklama/raporlama (diagnostics alt-modülü)

### §A.3 Kabul Kriterleri

| # | Kriter | Ölçüm | Doğrulama |
|---|---|---|---|
| DC-1 | I2C/SPI/GPIO arayüzleri tek API ile erişilebilir | Birim test | device-control-dogrulama.md §A |
| DC-2 | PCM3168A register yazımı doğrulanmış kaynaktan | Register doğrulama | ADR-038 H001 |
| DC-3 | Cihaz olayı ≤ 1 tampon (10,67 ms) içinde işlenir | Zamanlama ölçümü | device-control-dogrulama.md §C |
| DC-4 | Boot sonrası cihaz başlatma sırası deterministik | Boot entegrasyon | boot-dogrulama.md B-12 köprüsü |
| DC-5 | Tahsis yok, lock-free RT erişim | Statik analiz | cppcheck/Clang-Tidy |
| DC-6 | Röle GPIO çıkışı DC koruma tetikler | Donanım test | audio-control AV-5 köprüsü |

### §A.4 Bağımlılıklar

| Bağımlılık | Tür | Yön | Not |
|---|---|---|---|
| `K005-firmware/boot` | Modül | device-control → boot | Başlatma sırası |
| `K005-firmware/audio-control` | Modül | audio-control → device-control | DAC register erişimi |
| `K005-firmware/power-control` | Modül | device-control ↔ power-control | Röle GPIO |
| `K005-firmware/runtime` | Modül | device-control → runtime | Ring-buffer telemetri |
| `K005-firmware/diagnostics` | Modül | device-control → diagnostics | Hata olayları |
| PCM3168A datasheet | Donanım | Harici | Register haritası doğrulanmış kaynaktan |
| Hedef MCU HAL | Donanım | Harici | UNKNOWN (hedef bilinmiyor) |

## §B Bileşen Listesi

| # | Bileşen | Sorumluluk | Arayüz | Tahsis |
|---|---|---|---|---|
| B1 | Bus soyutlama | I2C/SPI/GPIO/UART/PWM/counter tek API | MCU HAL | Statik |
| B2 | Cihaz kayıt modeli | Cihaz listesi, başlatma sırası | Statik tablo/dts | Statik |
| B3 | DAC register erişimi | PCM3168A yapılandırması | I2C (B1 üzerinden) | Statik |
| B4 | Röle/DC GPIO | Koruma rölesi tetikleme | GPIO (B1) | Statik |
| B5 | Zamanlayıcı/tarama | Periyodik cihaz tarama | Zamanlayıcı | Statik |
| B6 | Olay işleyici | Interrupt/olay dispatcı | ISR/thread | Statik |
| B7 | Telemetri köprüsü | Cihaz olaylarını runtime'a | Ring-buffer | Statik |

## §C Arayüzler

### §C.1 Dahili arayüzler (taslak imzalar)

```cpp
// Kaynak: bus soyutlama taslağı — repo'da karşılığı dosya yok; Dosya:/Satır: verilmez.
// Statik bellek, tahsis yasağına uygun (kural 1).

enum class BusType : uint32_t
{
    I2c = 0,
    Spi = 1,
    Gpio = 2,
    Uart = 3,
    Pwm = 4,
    Counter = 5
};

struct DeviceDescriptor final
{
    BusType  bus;
    uint32_t address;        /* I2C/SPI — register adresleri doğrulanmış kaynaktan */
    uint32_t gpio_pin;       /* GPIO/PWM */
    bool     active_low;
};

class DeviceBus final
{
public:
    static DeviceBus& instance() noexcept;
    int write(BusType bus, uint32_t addr, const uint8_t* data, uint32_t len) noexcept;
    int read(BusType bus, uint32_t addr, uint8_t* buf, uint32_t len) noexcept;
    int gpio_set(uint32_t pin, bool level) noexcept;
};
```

### §C.2 Dış arayüzler

| Arayüz | Taraf | Tür | Not |
|---|---|---|---|
| I2C/SPI | device ↔ MCU | Donanım | DAC register (PCM3168A) |
| GPIO | device ↔ röle | Donanım | DC koruma (power-control) |
| PWM | device ↔ sürücü | Donanım | Boyut/parlaklık (UNKNOWN) |
| Counter | device ↔ ölçüm | Donanım | Sayaç (telemetri) |
| Ring-buffer | device → runtime | Yazılım | Telemetri olayları |

### §C.3 Gerçek zamanlı kısıtlar

| Kural | Kaynak |
|---|---|
| `malloc`/`new`/`delete` yasak | Kural 1 |
| Mutex/lock_guard yasak (RT thread) | Kural 2 |
| `noexcept` callback | Kural 3 |
| `constexpr` sabit | Kural 4 |
| `alignas(64)` önbellek hizası | Kural 5 |

## §D Bağımlılık Grafiği

```text
                    +---------------------------+
                    |        K005-firmware      |
                    +-------------+-------------+
                                  |
        +------------+------------+-------------+-------------+
        |            |            |             |             |
   +----v-----+ +----v------+ +---v------+ +----v------+ +----v-------+
   |   boot   | | runtime   | | power-   | | diagnostics| |  security  |
   |          | |           | | control  | |            | |            |
   +----+-----+ +-----+-----+ +----+-----+ +-----+------+ +-----+------+
        |            |            |             |             |
        +------+-----+------+-----+------+------+-----+-------+
               |            |            |             |
          +----v------------v------------v-------------v----+
          |             device-control                       |
          |  (B1 bus · B2 kayıt · B3 DAC · B4 röle          |
          |   B5 tarama · B6 olay · B7 telemetri)           |
          +------------------------+-------------------------+
                                   |
                     +-------------v--------------+
                     |  I2C/SPI/GPIO/PWM/Counter  |
                     |  (PCM3168A, röle, sürücü)  |
                     +-----------------------------+

audio-control → device-control (B3 DAC register erişimi)
```

Açıklama: `device-control`, bus erişimini soyutlar; audio-control adına DAC
register'larını yazar; power-control adına röle GPIO'sini sürer; runtime'a
telemetri olayları üretir. Boot sonrası deterministik başlatma sırası uygular.

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| I2C peripheral donanım arayüzü Zephyr peripherallerinde tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/i2c.html | 2026-10-13 | HTTP 200 |
| SPI peripheral donanım arayüzü Zephyr peripherallerinde tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/spi.html | 2026-10-13 | HTTP 200 |
| GPIO peripheral röle/sürücü çıkışı için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html | 2026-10-13 | HTTP 200 |
| PWM peripheral boyut/sürücü zamanlaması için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html | 2026-10-13 | HTTP 200 |
| Counter peripheral sayaç ölçümü için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/counter.html | 2026-10-13 | HTTP 200 |
| Board tanımları dts binding tabanlıdır | https://docs.zephyrproject.org/latest/boards/index.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Arayüz Örnekleri (Taslak)

### §F.1 Cihaz başlatma durum makinesi

```cpp
// Kaynak: cihaz durum makinesi taslağı — device-control-kapsam.md §C.1.
enum class DeviceInitState : uint32_t
{
    Uninitialized = 0,
    Initializing  = 1,
    Ready         = 2,
    Failed        = 3
};

class DeviceLifecycle final
{
public:
    static DeviceLifecycle& instance() noexcept
    {
        static DeviceLifecycle d;
        return d;
    }

    DeviceInitState state_of(uint32_t idx) const noexcept
    {
        return (idx < kDeviceCount) ? states_[idx] : DeviceInitState::Failed;
    }

    void set_state(uint32_t idx, DeviceInitState s) noexcept
    {
        if (idx < kDeviceCount) { states_[idx] = s; }
    }

private:
    DeviceLifecycle() noexcept = default;
    DeviceInitState states_[kDeviceCount] = {};
};
```

### §F.2 Boot ile başlatma sırası entegrasyonu

| Boot olayı | device-control tepkisi |
|---|---|
| BOOT_OK | `device_init_all()` çağrılır (deterministik sıra) |
| BOOT_ERR_INTEGRITY | Cihaz başlamaz, durum Uninitialized |
| BOOT_ERR_ROLLBACK | Cihaz başlamaz, durum Uninitialized |
| BOOT_ERR_HW_INIT | Cihaz başlamaz, durum Failed |

### §F.3 Bus kayıt modeli tablosu (taslak)

| # | Bus | Adres/Pin | Tahsis | Not |
|---|---|---|---|---|
| 1 | I2C | UNKNOWN (PCM3168A) | Statik | DAC register (ADR-038 H001) |
| 2 | SPI | UNKNOWN | Statik | Genişletme |
| 3 | GPIO | 13 (örnek) | Statik | Röle (kural 10) |
| 4 | PWM | UNKNOWN | Statik | Boyut/sürücü |
| 5 | Counter | UNKNOWN | Statik | Sayaç ölçümü |
| 6 | UART | UNKNOWN | Statik | Telemetri köprüsü |

### §F.4 Bellek bütçesi (taslak)

| Bölge | Taslak boyut | Not |
|---|---|---|
| Cihaz kayıt tablosu | 6 × 24 B ≈ 144 B | Statik |
| Bus soyutlama durumu | 32 B | Statik |
| Telemetri ring-buffer | 256 B | Baştan ayrılmış |
| sayaçlar | 16 B | Atomik |

## §G Boot ile Arayüz (Bağımlılık Detayı)

`device-control`, boot tamamlandıktan sonra `device_init_all()` çağırır. Boot
başarısızsa (BOOT_E001..E005) cihaz hiç başlamaz — fail-safe. Başlatma sırası
deterministiktir (boot B-12 köprüsü).

## §H Sartname Bağlantı Tablosu

| Sartname sabiti | device-control kullanımı |
|---|---|
| 512 örnek / 48 kHz | Olay gecikmesi eşiği (≤ 1 tampon = 10,67 ms) |
| PCM3168A (ADR-038 H001) | DAC hedefi; PCM5122 YASAK |
| DC >0,5 V → röle | Röle GPIO (B4) |
| ASIO Exclusive Lock | Durum raporu (diagnostics köprüsü) |

## §I CI Köprüsü Notu

Firmware build'i depoda tanımlı değil (derlenebilir kaynak yok). CI köprüsü
kurulunca: firmware build + statik analiz + birim test adımları eklenecek
(onay + ADR gerektirir). ⚠️ Mevcut CI (`ci.yml`, `secret-scan.yml`) DEĞİŞTİRİLMEZ.

## §J Sık Sorulanlar

**S: Register adresleri neden yok?**
Uydurma register yasağı; yalnız doğrulanmış kaynaktan (Zephyr dts binding / datasheet) `Kaynak:` etiketli verilir (ADR-038 H001).

**S: Cihaz keşfi dts mi, sabit tablo mu?**
UNKNOWN (mimari karar). Taslak sabit tablo (§B.2); dts binding Zephyr'de tanımlıdır.

**S: PCM5122 kullanılabilir mi?**
Hayır. ADR-038 H001 kuralı gereği PCM5122 ABSOLUTELY FORBIDDEN; yalnız PCM3168A.

**S: Röle GPIO neden aktif-düşük?**
Taslak §A.2 dts; gerçek polarity hedef MCU'ya göre değişebilir (UNKNOWN).

**S: Olay gecikmesi neden ≤ 10,67 ms?**
1 tampon (512/48000); audio-control zamanlama zinciriyle uyumlu.

## §K Test Matrisi (özet köprü)

| Test | Yöntem | Doğrulama senaryosu |
|---|---|---|
| Bus yazma/okuma | Birim (mock bus) + entegrasyon | DV-1, DV-2 |
| GPIO/röle | Donanım | DV-3, DC koruma (AV-5 köprüsü) |
| DAC register | Entegrasyon (J-Link) | DV-6 |
| Başlatma sırası | Boot entegrasyon | DV-7 (B-12 köprüsü) |
| Olay gecikmesi | Zaman işaretçisi | DV-8 |
| Tahsis/lock-free | Statik analiz | DV-9, DV-10 |

## §L Bileşen Detay Kartları (Genişletme)

### §L.1 B1 — Bus soyutlama detayı

| Alan | Değer | Not |
|---|---|---|
| Buslar | I2C/SPI/GPIO/UART/PWM/counter | Tek API |
| Tahsis | Statik | Kural 1 |
| Erişim | Lock-free (atomik) | Kural 2 |
| Hata | NACK/timeout kodu | DV-12 |

### §L.2 B2 — Cihaz kayıt modeli detayı

| Alan | Değer | Not |
|---|---|---|
| Tablo | Statik dizi (§B.2) | Deterministik sıra |
| Sıra | `init_order` artan | Boot B-12 köprüsü |
| Sayı | UNKNOWN (hedef MCU) | ⚠️ VERIFICATION REQUIRED |
| Keşfi | dts veya sabit tablo | Mimari karar |

### §L.3 B3 — DAC register detayı

| Alan | Değer | Not |
|---|---|---|
| DAC | PCM3168A | ADR-038 H001; PCM5122 YASAK |
| Bus | I2C | Register adresleri doğrulanmış kaynaktan |
| Kullanıcı | audio-control | B3 erişimi |
| Hata | NACK kodu | DV-12 |

### §L.4 B4 — Röle/DC GPIO detayı

| Alan | Değer | Not |
|---|---|---|
| Pin | 13 (örnek) | Hedef MCU UNKNOWN |
| Polarity | Aktif-düşük (taslak) | §A.2 dts |
| Kullanıcı | power-control | DC koruma (kural 10) |
| Tetik | ≤ 1 tampon (10,67 ms) | DC-3 |

## §M Arayüz Sözleşmeleri (Detay)

| Arayüz | Veri | Yön | Zamanlama | Hata |
|---|---|---|---|---|
| B1 ↔ MCU | Bus erişim | device ↔ donanım | Her erişim | NACK/timeout |
| B3 ↔ PCM3168A | Register | device ↔ DAC | Boot + değişiklik | ErrDacInit |
| B4 ↔ röle | GPIO | device ↔ power-control | ≤ 1 tampon | — |
| B5 ↔ zamanlayıcı | Tick | device ↔ runtime | Periyodik | — |
| B6 ↔ interrupt | Olay | device ↔ runtime | ≤ 1 tampon | Hata kodu |
| B7 ↔ diagnostics | Ring-buffer | device → telemetri | Periyodik | — |

## §N Sartname Bağlantı Tablosu (Genişletme)

| Sartname sabiti | Kapsam maddesi | Bileşen |
|---|---|---|
| 512 örnek / 48 kHz | Olay gecikmesi ≤ 1 tampon | B6 |
| PCM3168A (ADR-038 H001) | DAC register | B3 |
| DC >0,5 V → röle | Röle GPIO | B4 |
| Boot deterministik sıra | Başlatma | B2 |

## §O CI Köprüsü Notu (Genişletme)

CI köprüsü kurulunca: firmware build + statik analiz (cppcheck/Clang-Tidy) +
birim test (Unity/ceedling) + secret-scan (firmware ikili dosyaları). ⚠️ Mevcut
CI DEĞİŞTİRİLMEZ; yalnız plan.

## §P Sık Sorulanlar (Genişletme)

**S: Bus çakışması nasıl çözülür?**
UNKNOWN (bus topolojisi bilinmiyor); taslakta tek erişim thread'i varsayılır. DV-11 senaryosu bunu yakalar.

**S: Cihaz tarama frekansı neden UNKNOWN?**
Mimari karar; uydurma frekans yazılmaz (DD-7).

**S: Boot başarısızsa cihaz ne yapar?**
Hiç başlamaz (fail-safe; §F.2 tablosu).

**S: Register adresleri neden sabit değil?**
Uydurma yasağı; yalnız doğrulanmış kaynaktan (ADR-038 H001).

**S: PCM5122 örneği var mı?**
Hayır; ADR-038 H001 yasağı.

## §Q Risk Matrisi (Genişletme)

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| R1 | Bus NACK → DAC yapılandırma başarısız | Orta | Yüksek | Hata kodu + telemetri (DV-12) |
| R2 | Başlatma sırası bozuk → boot gecikmesi | Düşük | Yüksek | Deterministik sıra (DV-7) |
| R3 | Olay gecikmesi > 10,67 ms | Orta | Yüksek | Zamanlama ölçümü (DV-8) |
| R4 | PCM5122 yanlış montaj | Düşük | Kritik | ADR-038 H001 denetimi |
| R5 | Tahsis/lock-free ihlali | Düşük | Kritik | Statik analiz (DV-9, DV-10) |

## §R Referanslar (Genişletme — Son)

- ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13 (RT, tahsis, noexcept, constexpr, alignas, koruma, ASIO)
- `K005-firmware/boot/boot-kapsam.md` (boot bağımlılığı)
- `K005-firmware/device-control/index.md` (grup indeksi — dokunulmadı)

## §S Senaryo Tetikleyici Matrisi (Kapsam — Son)

| Senaryo | Tetikleyici | Öncelik | Beklenen sonuç |
|---|---|---|---|
| DV-1 | I2C register yazma | Kritik | Doğru değer, NACK raporu |
| DV-2 | SPI register yazma | Kritik | Doğru değer |
| DV-3 | Röle GPIO tetik | Kritik | DC koruma (audio AV-5) |
| DV-4 | PWM duty değişimi | Yüksek | Doğru duty/frekans |
| DV-5 | Counter artışı | Yüksek | Doğru artış |
| DV-6 | DAC yapılandırma | Kritik | PCM3168A register doğrulama |
| DV-7 | Boot sonrası başlatma | Kritik | Deterministik sıra (B-12) |
| DV-8 | Cihaz olayı | Yüksek | ≤ 10,67 ms gecikme |
| DV-9 | RT erişim | Kritik | Tahsis yok (kural 1) |
| DV-10 | RT erişim | Kritik | Mutex yok (kural 2) |
| DV-11 | Eşzamanlı bus erişimi | Orta | Arbitraj (UNKNOWN topoloji) |
| DV-12 | NACK/timeout | Yüksek | Hata kodu + telemetri |

## §T Bağımlılık Matrisi (Kapsam — Son)

| Bileşen | boot | audio-control | power-control | runtime | diagnostics |
|---|---|---|---|---|---|
| B1 Bus soyutlama | — | — | — | — | — |
| B2 Cihaz kayıt | başlatma | — | — | — | — |
| B3 DAC register | — | istemci | — | — | — |
| B4 Röle GPIO | — | — | sürücü | — | — |
| B5 Tarama | — | — | — | tick | — |
| B6 Olay işleyici | — | — | — | olay | — |
| B7 Telemetri köprüsü | — | — | — | ring-buffer | tüketici |

## §U Risk Matrisi (Kapsam — Son)

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| R6 | Register adresi hatalı (uydurma) | Düşük | Kritik | Doğrulanmış kaynak (ADR-038 H001) |
| R7 | Olay queue taşması | Orta | Yüksek | Ring-buffer boyutu (UNKNOWN) |
| R8 | Bus timeout → sistem kilidi | Düşük | Kritik | Timeout + hata kodu (DV-12) |

## §V Sartname Bağlantı Tablosu (Kapsam — Son)

| Sartname sabiti | Kapsam maddesi |
|---|---|
| 512 örnek / 48 kHz | DC-3 (olay gecikmesi) |
| PCM3168A (ADR-038 H001) | DC-2 (register doğrulama) |
| DC >0,5 V → röle | DC-6 (röle tetik) |
| Boot deterministik sıra | DC-4 (B-12 köprüsü) |
| Tahsis yok (kural 1) | DC-5 |
| Lock-free (kural 2) | DC-5 |

## §W Doğrulanmış Web Kaynakları (Kapsam — Son)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| I2C peripheraller register erişimi tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/i2c.html | 2026-10-13 | HTTP 200 |
| GPIO peripheraller röle çıkışı tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html | 2026-10-13 | HTTP 200 |
| Board tanımları dts binding tabanlıdır | https://docs.zephyrproject.org/latest/boards/index.html | 2026-10-13 | HTTP 200 |

## §X Kapsam Dışı Bırakılan Konular (Son)

| Konu | Neden kapsam dışı | Sorumlu alan |
|---|---|---|
| DSP/EQ/kompresör | İşleme mantığı | `audio-control` |
| DC offset izleme | Algoritma | `audio-control` |
| Termal/güç amplifikatörü | Donanım | `.ai/electronics/` |
| OTA güncelleme | Güncelleme | `update` |
| Kripto/secure boot | Güvenlik | `security` |

## §Y Bileşen Bazlı Kabul Kriteri Matrisi (Kapsam — Son Ek)

| Bileşen | Kabul kriteri | Doğrulama |
|---|---|---|
| B1 Bus soyutlama | Tek API, tahsis yok | DC-1, DC-5, DV-1..DV-5 |
| B2 Cihaz kayıt | Deterministik sıra | DC-4, DV-7 (B-12) |
| B3 DAC register | Doğrulanmış kaynak | DC-2, DV-6 (ADR-038 H001) |
| B4 Röle GPIO | DC koruma tetik | DC-6, DV-3 (audio AV-5) |
| B5 Tarama | Lock-free tick | DC-5, DV-10 |
| B6 Olay işleyici | ≤ 1 tampon gecikme | DC-3, DV-8 |
| B7 Telemetri köprüsü | Ring-buffer olay | DV-12 |

## §Z Son Not ve Kapsam Dışı (Kapsam)

`device-control`, bus erişimini soyutlar ve boot sonrası deterministik
başlatma uygular. Register adresleri ve bus topolojisi UNKNOWN — uydurma
yasağı gereği yalnız doğrulanmış kaynaktan `Kaynak:` etiketli verilir
(ADR-038 H001). PCM5122 ASLA kullanılmaz.

## §AA Kapsam Sonu Not (Kapsam — Son Ek)

`device-control` bus erişimini soyutlar; boot sonrası deterministik başlatma
uygular; audio-control adına PCM3168A register'larını yazar (ADR-038 H001,
PCM5122 YASAK); power-control adına röle GPIO'sini sürer (kural 10); runtime'a
telemetri olayları üretir. Register adresleri/bus topolojisi UNKNOWN — uydurma
yasağı.

## §AB Bileşen Bağımlılık Grafiği (Kapsam — Son Ek)

```text
                     boot
                      |
                      v
  power-control --> device-control --> runtime
                      ^   |
                      |   v
               audio-control   diagnostics
```

Açıklama: `device-control`, boot sonrası deterministik başlatma uygular;
audio-control adına DAC register'larını yazar; power-control adına röle
GPIO'sini sürer; runtime'a telemetri olayları üretir; diagnostics hata
olaylarını tüketir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
