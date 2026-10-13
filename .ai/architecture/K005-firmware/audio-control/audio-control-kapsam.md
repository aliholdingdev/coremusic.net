---
node_id: K005-firmware/audio-control/audio-control-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/boot
evidence:
  - K005-firmware/audio-control/index.md
  - "repo: shared/src/AI/AIOrchestrator.php satır 36 — 'dsp-fw' agent routing (firmware != PHP host)"
validation: []
open_questions:
  - "8.1 surround kanal öncelik sırası ve TDM slot eşlemesi kesinleşmedi (UNKNOWN)"
  - "Hedef I2S/TDM veri yolu donanım topolojisi .ai/electronics/ altındadır (JTAG/SWD köprüsü gibi)"
  - "DSP zinciri sırası (EQ → kompresör → gain) ve parametre aralıkları mimari karar bekliyor"
group: mimari
---

# Audio-Control Alt-Modülü — Kapsam (K005-firmware/audio-control/audio-control-kapsam.md)

> Bu dosya, `K005-firmware/audio-control/` alt-modülünün **kapsam** dokümanıdır.
> `index.md` yalnız grup indeksidir; kapsamlı tanım buradadır. Repo'da
> derlenebilir C/C++ firmware kodu BULUNMAMAKTADIR (2026-10-13 keşif: 0 dosya)
> ve hedef MCU bilinmiyor (UNKNOWN) — bu nedenle bileşen listesi ve arayüzler
> **mimari taslaktır**; kesin register/kod referansı verilmez.

## §A Kapsam

### §A.1 Kapsar

- 512 örnek tampon @ 48 kHz (10,67 ms) işleme hattı — sartname sabiti
- 32-bit float ses veri tipi (ADR-017) — hattın tüm aşamalarında korunur
- I2S/TDM veri yolu ve DMA transfer zinciri (DAC'a kadar)
- PCM3168A DAC sürücü entegrasyonu (ADR-038 H001) — PCM5122 YASAK
- 8.1 surround kanal haritası ve TDM slot eşlemesi (kanal sayısı/öncelik kesinleşmedi → UNKNOWN)
- DSP zinciri: EQ → kompresör → gain (sıra mimari karar, taslak)
- DC offset izleme ve >0,5 V eşiğinde koruma rölesi tetikleme
- ASIO Exclusive Lock: tek uygulama kilidi (Windows tarafı; firmware tarafı durum raporu üretir)
- Boot sonrası ses hattı başlatma sırası (boot modülüne bağımlı)

### §A.2 KAPSAMAZ

- DAC analog ön yükeşi ve güç amplifikatörü devresi (`.ai/electronics/` alanıdır)
- JTAG/SWD fiziksel telemetri köprüsü donanım tasarımı (boot-dogrulama.md §A)
- PHP host katmanı, web API, kullanıcı arayüzü (AIOrchestrator'dan ayrı domain: `fw`)
- OTA güncelleme paketleme/dağıtma mantığı (update alt-modülü alanı)
- Cihaz keşfi/dts binding üretim döngüsü (device-control alt-modülü alanı)
- Firmware OTA, bağımlılık bump, refactor (bu dosya yalnız tasarım kapsamı — uygulama ayrı onay ister)

### §A.3 Kabul Kriterleri

| # | Kriter | Ölçüm | Doğrulama |
|---|---|---|---|
| AC-1 | Tampon boyutu 512 örnek, örnekleme 48 kHz | Sabit derleme zamanı kontrolü | boot-dogrulama.md B-13 köprüsü |
| AC-2 | Veri tipi hattın tamamında 32-bit float | Tip denetimi + SNR ölçümü | SNR >100 dB |
| AC-3 | THD+N < %0,01 @ 1 kHz | Spektrum analizi | Ölçüm metodolojisi — audio-control-dogrulama.md §C |
| AC-4 | DC offset >0,5 V → röle tetikleme gecikmesi ≤ 1 tampon (10,67 ms) | Zamanlama ölçümü | audio-control-dogrulama.md §A senaryosu |
| AC-5 | ASIO Exclusive Lock ihlali tespiti | Kilit denetimi | audio-control-dogrulama.md §A senaryosu |
| AC-6 | DMA underrun/overrun sayaçları okunabilir | Telemetri (diagnostics köprüsü) | audio-control-dogrulama.md §D |
| AC-7 | Boot'tan ses hattı hazır olana kadar geçen süre ≤ 1200 ms | Boot süresi ölçümü | boot-dogrulama.md B-13 |

### §A.4 Bağımlılıklar

| Bağımlılık | Tür | Yön | Not |
|---|---|---|---|
| `K005-firmware/boot` | Modül | audio-control → boot | Flaş/örtük başlatma sırası |
| `K005-firmware/device-control` | Modül | audio-control → device-control | I2C/SPI register erişimi, cihaz keşfi |
| `K005-firmware/diagnostics` | Modül | audio-control → diagnostics | Telemetri/hata olayları |
| `K005-firmware/runtime` | Modül | audio-control → runtime | Ring-buffer/veri geçişi, zamanlayıcı |
| `K005-firmware/power-control` | Modül | audio-control ↔ power-control | Röle/DC koruma, güç durumu |
| `K005-firmware/security` | Modül | audio-control → security | Ses oturumu kimlik doğrulama (belirsiz → UNKNOWN) |
| PCM3168A datasheet | Donanım | Harici | ADR-038 H001; register haritası doğrulanmış kaynaktan |
| 512/48k/32-bit float sartname | Kural | Sabit | ADR-017, sartname.md |

## §B Bileşen Listesi

| # | Bileşen | Sorumluluk | Arayüz | Tahsis |
|---|---|---|---|---|
| B1 | Tampon yöneticisi | 512-örnek çift tampon (ping-pong) dağıtımı | Statik bellek, tahsis yok | Baştan ayrılmış |
| B2 | I2S/TDM veri yolu | DMA kurulumu, kilit/zamanlama | MCU HAL + DMA | Statik |
| B3 | DAC sürücü (PCM3168A) | Register yapılandırması, mute/unmute | I2C (device-control üzerinden) | Statik |
| B4 | DSP zinciri | EQ, kompresör, gain işleme | Statik tamponlu, blok işleme | Statik |
| B5 | DC offset izleyici | Periyodik ortalama hesap, eşik karşılaştırma | ADC girişi (device-control) | Statik |
| B6 | Koruma rölesi denetleyici | >0,5 V → röle tetik + olay kaydı | GPIO/relay (power-control) | Statik |
| B7 | ASIO kilit durumu | Exclusive Lock durum bayrağı raporu | Host köprüsü (Windows) | Statik |
| B8 | Telemetri yayıncısı | Underrun/overrun, CPU, doluluk sayaçları | runtime ring-buffer → diagnostics | Statik |

## §C Arayüzler

### §C.1 Dahili arayüzler (taslak imzalar)

```cpp
// Kaynak: arayüz taslağı — repo'da karşılığı dosya yok; Dosya:/Satır: verilmez.
// Statik bellek, tahsis yasağına uygun.

struct AudioBufferSet final
{
    static constexpr uint32_t kSamples = 512U;   // sartname: 512 örnek
    static constexpr uint32_t kChannels = 12U;   // 8.1 + height kanalları — UNKNOWN (kesinleşmedi)
    static constexpr uint32_t kBits = 32U;       // ADR-017: 32-bit float

    float ch[kChannels][kSamples];               // hattın ana verisi (32-bit float)
    uint32_t underrun_count;                     // sayaç (diagnostics)
    uint32_t overrun_count;                      // sayaç (diagnostics)
};

enum class AudioStatus : int32_t
{
    Ok = 0,
    ErrDmaUnderrun = -1,
    ErrDacInit = -2,
    ErrDcTrip = -3,
    ErrAsioLock = -4
};

class AudioPipeline final
{
public:
    static AudioPipeline& instance() noexcept;
    AudioStatus prepare() noexcept;              // boot sonrası tek sefer
    AudioStatus process_block(AudioBufferSet& b) noexcept;  // RT güvenli
    void monitor_dc(AudioBufferSet& b) noexcept;            // RT güvenli
};
```

### §C.2 Dış arayüzler

| Arayüz | Taraf | Tür | Not |
|---|---|---|---|
| I2S/TDM + DMA | MCU ↔ PCM3168A | Donanım | 32-bit float hattı; TDM slot eşlemesi UNKNOWN |
| I2C register yazımı | audio-control → device-control | Yazılım | DAC yapılandırması; register adresleri doğrulanmış kaynaktan |
| Röle tetik | audio-control → power-control | Sinyal | DC >0,5 V → röle; latency ≤ 1 tampon |
| Telemetri olayı | audio-control → diagnostics | Yazılım | Ring-buffer; ISR'da tahsis/kilit yasak |
| Exclusive Lock durumu | host ↔ firmware | Köprü | Tek uygulama kuralı (ASIO) |

### §C.3 Gerçek zamanlı kısıtlar

| Kural | Kaynak |
|---|---|
| Audio callback `noexcept` | K005-firmware kuralı 3 |
| `malloc`/`new`/`delete` yasak | K005-firmware kuralı 1 |
| Mutex/lock_guard yasak (RT thread) | K005-firmware kuralı 2 |
| Buffer'lar baştan allocate | K005-firmware (audio & electronics standardı) |
| `constexpr` derleme zamanı hesap | K005-firmware kuralı 4 |
| `alignas(64)` önbellek hizası | K005-firmware kuralı 5 |

## §D Bağımlılık Grafiği

```text
                    +---------------------------+
                    |        K005-firmware      |
                    +-------------+-------------+
                                  |
        +------------+------------+-------------+-------------+
        |            |            |             |             |
   +----v-----+ +----v------+ +---v------+ +----v------+ +----v-------+
   |   boot   | |  device-  | | runtime  | | power-    | | diagnostics|
   |          | |  control  | |          | | control   | |            |
   +----+-----+ +----+------+ +---+------+ +----+------+ +----+-------+
        |            |            |             |             |
        +------+-----+------+-----+------+------+-----+-------+
               |            |            |             |
          +----v------------v------------v-------------v----+
          |              audio-control                      |
          |  (B1 tampon · B2 I2S/TDM · B3 DAC · B4 DSP     |
          |   B5 DC izleyici · B6 röle · B7 ASIO · B8 tel) |
          +------------------------+-------------------------+
                                   |
                            +------v------+
                            |  PCM3168A   |
                            | (ADR-038)   |
                            +-------------+
```

Açıklama: `audio-control`, boot sonrası hazır olan veri yolunu (device-control I2C/DMA) üzerinde çalışır; runtime ring-buffer'ına telemetri üretir; DC ihlalinde power-control rölesini tetikler; diagnostics sayaçları okur. Çıkış DAC'a (PCM3168A) gider. PCM5122 YASAK (ADR-038 H001).

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Zephyr I2S örneği 48 kHz veri yolunu gösterir | https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html | 2026-10-13 | HTTP 200 |
| DMA kontrolcüsü donanım arayüzü Zephyr peripherallerinde tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |
| ADC ölçüm arayüzü (DC offset izleme girdisi) peripherallerdedir | https://docs.zephyrproject.org/latest/hardware/peripherals/adc.html | 2026-10-13 | HTTP 200 |
| Pipe tabanlı veri geçişi Zephyr kernel servislerindedir | https://docs.zephyrproject.org/latest/kernel/services/data_passing/pipes.html | 2026-10-13 | HTTP 200 |
| Statik yığın (stack) yönetimi Zephyr veri geçişinde tanımlanır | https://docs.zephyrproject.org/latest/kernel/services/data_passing/stacks.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Arayüz Örnekleri (Taslak)

### §F.1 Oturum durumu makinesi (ses hattı)

```cpp
// Kaynak: oturum durum makinesi taslağı — audio-control-kapsam.md §C.1.
// Durumlar: Idle → Preparing → Ready → Streaming → Tripped(DC) → Recovering → Ready
enum class AudioSessionState : uint32_t
{
    Idle       = 0,
    Preparing  = 1,
    Ready      = 2,
    Streaming  = 3,
    Tripped    = 4,   // DC ihlali — röle tetiklendi
    Recovering = 5
};

class AudioSession final
{
public:
    static AudioSession& instance() noexcept
    {
        static AudioSession s;
        return s;
    }

    AudioSessionState state() const noexcept { return state_; }

    void on_dc_trip() noexcept
    {
        // Kural 10: DC >0,5 V → koruma rölesi zorunlu
        state_ = AudioSessionState::Tripped;
    }

    void on_recover_request() noexcept
    {
        if (state_ == AudioSessionState::Tripped)
        {
            state_ = AudioSessionState::Recovering;
        }
    }

    void on_recover_done() noexcept
    {
        if (state_ == AudioSessionState::Recovering)
        {
            state_ = AudioSessionState::Ready;
        }
    }

private:
    AudioSession() noexcept = default;
    AudioSessionState state_ = AudioSessionState::Idle;
};
```

### §F.2 Kanal haritası tablosu (8.1 surround — kesin slot eşlemesi UNKNOWN)

| # | Kanal adı | Kısaltma | TDM slotu | Not |
|---|---|---|---|---|
| 1 | Sol | L | UNKNOWN | Ön ana |
| 2 | Sağ | R | UNKNOWN | Ön ana |
| 3 | Merkez | C | UNKNOWN | Diyalog |
| 4 | LFE (subwoofer) | LFE | UNKNOWN | Düşük frekans |
| 5 | Sol surround | Ls | UNKNOWN | Yan |
| 6 | Sağ surround | Rs | UNKNOWN | Yan |
| 7 | Sol arka | Lb | UNKNOWN | Arka |
| 8 | Sağ arka | Rb | UNKNOWN | Arka |
| 9 | Sol üst height | Ltf | UNKNOWN | Height (belirsiz — 8.1'e dahil mi?) |
| 10 | Sağ üst height | Rtf | UNKNOWN | Height (belirsiz) |
| 11 | Sol arka üst | Ltr | UNKNOWN | Height (belirsiz) |
| 12 | Sağ arka üst | Rtr | UNKNOWN | Height (belirsiz) |

> **Açıklama:** "8.1" terimi 8 tam kanal + 1 LFE anlamına gelir; height kanalları
> (Ltf/Rtf/Ltr/Rtr) 9.1/7.1.4 konfigürasyonlarına aittir. `kChannels = 12` taslağı
> bu belirsizliği yansıtır; kesin eşleme donanım topolojisi kesinleşince sabitlenecek
> (⚠️ VERIFICATION REQUIRED — UNKNOWN).

### §F.3 Korumalı röle tetik akışı (adım adım)

| Adım | İşlem | Eşik/Süre | Kritiklik |
|---|---|---|---|
| 1 | DC ortalaması hesapla (tampon başına) | 512 örnek (10,67 ms) | Yüksek |
| 2 | |DC| > 0,5 V kontrolü | Kural 10 | Kritik |
| 3 | Röle GPIO tetikle | ≤ 1 tampon gecikme | Kritik |
| 4 | Durum → Tripped, olay kaydı | Sayaç artışı | Yüksek |
| 5 | Mute DAC (I2C) | device-control üzerinden | Yüksek |
| 6 | Telemetri olayı yayınla (diagnostics) | Ring-buffer | Orta |
| 7 | Kurtarma isteği (manual/auto) | UNKNOWN politika | Yüksek |
| 8 | Röle bırakma + unmute | Kullanıcı onayı gerekebilir (UNKNOWN) | Yüksek |

### §F.4 Bellek bütçesi (taslak)

| Bölge | Taslak boyut | Not |
|---|---|---|
| Ping-pong tampon (2 × 12 × 512 × 4 B) | ≈ 48 KiB | `alignas(64)` |
| DSP çalışma tamponu | ≤ 4 KiB | Blok işleme |
| sayaçlar (underrun/overrun/DC trip) | 32 bayt | Atomik |
| Oturum durumu | 4 bayt | Enum |

## §G Boot ile Arayüz (Bağımlılık Detayı)

`audio-control`, boot tamamlandıktan sonra `prepare()` çağırır. Boot süresi
eşiği (≤ 1200 ms, boot-dogrulama.md B-13) aşılmazsa ses hattı hazır olur.
Boot başarısızsa (BOOT_E001..E005) ses hattı hiç başlamaz — fail-safe.

| Boot olayı | audio-control tepkisi |
|---|---|
| BOOT_OK | `prepare()` çağrılır |
| BOOT_ERR_INTEGRITY | Ses hattı başlamaz, durum Idle |
| BOOT_ERR_ROLLBACK | Ses hattı başlamaz, durum Idle |
| BOOT_ERR_HW_INIT | Ses hattı başlamaz, durum Idle |

## §H Sartname Bağlantı Tablosu

| Sartname sabiti | audio-control kullanımı |
|---|---|
| 512 örnek | Tampon boyutu (B1), DC izleme penceresi (B5) |
| 48 kHz | Örnekleme (B2), tampon süresi 10,67 ms |
| 32-bit float (ADR-017) | Hattın tamamında veri tipi |
| PCM3168A (ADR-038 H001) | DAC hedefi; PCM5122 YASAK |
| DC >0,5 V → röle | Koruma zinciri (B6) |
| ASIO Exclusive Lock | Durum raporu (B7) |

## §I CI Köprüsü Notu

Firmware build'i bu depoda tanımlı DEĞİLDİR (derlenebilir kaynak yok). CI
köprüsü kurulacağı zaman `ci.yml`'a firmware build + audio-control statik
analiz adımı eklenecek (onay + ADR gerektirir). `secret-scan.yml` firmware
ikili dosyalarında da çalıştırılmalı. ⚠️ Bu bölüm mevcut CI'yı DEĞİŞTİRMEZ.

## §J Sık Sorulanlar

**S: 8.1 kaç kanal?**
8 tam kanal + 1 LFE. Height kanalları (Ltf/Rtf/Ltr/Rtr) dahil mi belirsiz (UNKNOWN); taslak `kChannels = 12` bunu yansıtır.

**S: PCM5122 kullanılabilir mi?**
Hayır. ADR-038 H001 kuralı gereği PCM5122 ABSOLUTELY FORBIDDEN; yalnız PCM3168A.

**S: Röle gecikmesi neden ≤ 1 tampon?**
DC koruması duyulabilir hasarı önlemek için 10,67 ms içinde tetiklenmeli (kural 10 + sartname).

**S: Bu dosyadaki C++ derlenebilir mi?**
Hayır. Repo'da firmware kaynağı yok (2026-10-13: 0 dosya); parçalar `Kaynak:` etiketli taslaktır.

**S: ASIO Exclusive Lock firmware'de nasıl denetlenir?**
Firmware yalnız durum raporu üretir (§B.5); kilit ihlali tespiti host köprüsündedir (kural 9).

## §K Test Matrisi (özet köprü)

| Test | Yöntem | Doğrulama senaryosu |
|---|---|---|
| SNR/THD+N | Donanım ölçüm (audio analizör) | AV-3, AV-4 |
| DC koruma | Eşik + röle gecikme ölçümü | AV-5, AV-6 |
| DMA bütünlüğü | Renode/gerçek MCU + J-Link | AV-7, AV-8 |
| ASIO kilidi | İkinci uygulama denemesi | AV-9 |
| Boot → hazır | Boot süresi ölçümü | AV-10 |
| TDM eşleme | Tek kanallı sinyal testi | AV-14 |

## §L Bileşen Detay Kartları (Genişletme)

### §L.1 B1 — Tampon yöneticisi detayı

| Alan | Değer | Not |
|---|---|---|
| Toplam tampon | 2 (ping-pong) | Biri yazılırken diğeri işlenir |
| Boyut/tampon | 12 × 512 × 4 B ≈ 24 KiB | 32-bit float (ADR-017) |
| Hizalama | `alignas(64)` | Kural 5 |
| Tahsis | Statik (baştan) | Kural 1 |
| Erişim | Tek yazıcı (DMA), tek okuyucu (DSP) | Lock-free (kural 2) |

### §L.2 B2 — I2S/TDM veri yolu detayı

| Alan | Değer | Not |
|---|---|---|
| Örnekleme | 48 kHz | Sartname |
| Kelime genişliği | 32 bit | ADR-017 |
| Kanal (bus) | UNKNOWN (TDM slot) | 8.1 + height belirsiz |
| DMA | Çift tampon / döngüsel | Underrun riski (AV-7) |
| Bağlantı | MCU ↔ PCM3168A | ADR-038 H001 |

### §L.3 B3 — DAC sürücü (PCM3168A) detayı

| Alan | Değer | Not |
|---|---|---|
| DAC | PCM3168A | ADR-038 H001; PCM5122 YASAK |
| Register erişimi | I2C (device-control) | Register adresleri doğrulanmış kaynaktan |
| Mute/unmute | Yazılım kontrolü | DC trip'te mute (§F.3) |
| Yapılandırma | Sessizlik, format | UNKNOWN kesin değer |

### §L.4 B5/B6 — DC izleyici + röle detayı

| Alan | Değer | Not |
|---|---|---|
| Pencere | 512 örnek (10,67 ms) | Tampon başına ortalama |
| Eşik | > 0,5 V (mutlak) | Kural 10 |
| Aksiyon | Röle tetik + mute + olay | ≤ 1 tampon gecikme |
| Kurtarma | Manuel/auto (UNKNOWN) | Politika mimari karar |

## §M Arayüz Sözleşmeleri (Detay)

| Arayüz | Veri | Yön | Zamanlama | Hata |
|---|---|---|---|---|
| B1 ↔ B2 | `AudioBufferSet` | DMA → tampon | Her tampon (10,67 ms) | underrun/overrun |
| B2 ↔ B3 | Register yazımı | audio → device-control | Boot + değişiklik | ErrDacInit |
| B4 ↔ B1 | float blok | tampon ↔ DSP | Blok başına | clip (UNKNOWN eşiği) |
| B5 ↔ B1 | DC ortalaması | tampon → DC izleyici | Tampon başına | — |
| B6 ↔ power-control | Röle sinyali | audio → power | ≤ 1 tampon | — |
| B8 ↔ diagnostics | sayaçlar | audio → telemetri | Periyodik | — |

## §N Sartname Bağlantı Tablosu (Genişletme)

| Sartname sabiti | Kapsam kullanımı | Bileşen |
|---|---|---|
| 512 örnek | Tampon boyutu, DC penceresi | B1, B5 |
| 48 kHz (10,67 ms) | Örnekleme, gecikme eşiği | B2, B6 |
| 32-bit float (ADR-017) | Veri tipi hattı | B1, B4 |
| PCM3168A (ADR-038 H001) | DAC hedefi | B3 |
| DC >0,5 V → röle | Koruma | B6 |
| ASIO Exclusive Lock | Durum raporu | B7 |
| SNR >100 dB / THD+N <0,01% | Kabul kriteri | AC-2, AC-3 |

## §O CI Köprüsü Notu (Genişletme)

Firmware build'i depoda tanımlı değil (derlenebilir kaynak yok). CI köprüsü
kurulunca: (1) firmware build adımı, (2) statik analiz (cppcheck/Clang-Tidy),
(3) birim test (Unity/ceedling), (4) secret-scan firmware ikili dosyalarında.
⚠️ Mevcut CI (`ci.yml`, `secret-scan.yml`) DEĞİŞTİRİLMEZ; yalnız plan.

## §P Sık Sorulanlar (Genişletme)

**S: 8.1 + height neden 12?**
Taslak, belirsiz height kanallarını (Ltf/Rtf/Ltr/Rtr) kapsar; kesin eşleme UNKNOWN (§F.2).

**S: Register adresleri neden yok?**
Uydurma register yasağı; yalnız doğrulanmış kaynaktan (Zephyr dts binding / datasheet) `Kaynak:` etiketli verilir.

**S: DMA döngüsel mi, tek seferlik mi?**
Döngüsel (akış); tek seferlik yazma boot yapılandırması içindir (UNKNOWN donanım).

**S: Röle nasıl tetiklenir?**
GPIO üzerinden power-control arayüzü; gecikme ≤ 1 tampon (AC-4).

**S: EXclusive Lock ihlali ne yapar?**
Durum raporu üretir (B7); firmware kilidi zorlamaz (kural 9 host kuralı).

## §Q Referanslar (Bu Dosya)

- ADR-017: 32-bit float
- ADR-038 H001: PCM3168A (PCM5122 yasak)
- K005-firmware kuralı 1–13 (RT, tahsis, noexcept, constexpr, alignas, koruma, ASIO)
- `K005-firmware/boot/boot-kapsam.md` (boot bağımlılığı)
- `K005-firmware/boot/boot-dogrulama.md` (B-13 köprüsü)

## §R Bileşen Bağımlılık Matrisi

| Bileşen | boot | device-control | runtime | power-control | diagnostics |
|---|---|---|---|---|---|
| B1 Tampon yöneticisi | — | — | veri geçişi | — | — |
| B2 I2S/TDM + DMA | hazır olma | DMA/counter | — | — | underrun sayacı |
| B3 DAC (PCM3168A) | — | I2C register | — | — | — |
| B4 DSP zinciri | — | — | — | — | — |
| B5 DC izleyici | — | ADC girdisi | — | — | olay |
| B6 Röle denetleyici | — | GPIO | — | röle sürücü | olay |
| B7 ASIO durumu | — | — | — | — | rapor |
| B8 Telemetri yayıncısı | — | — | ring-buffer | — | tüketici |

## §S Risk Matrisi (Kapsam)

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| R1 | DMA underrun → ses kesintisi | Orta | Yüksek | Çift tampon + sayaç (AV-7) |
| R2 | DC offset hasarı | Düşük | Kritik | Röle koruma (AV-5, kural 10) |
| R3 | ASIO kilidi ihlali | Düşük | Yüksek | Durum raporu (AV-9, kural 9) |
| R4 | Boot gecikmesi → hazır geç | Düşük | Orta | B-13 ≤ 1200 ms |
| R5 | TDM slot eşlemesi hatalı | Orta | Yüksek | AV-14 doğrulama |
| R6 | PCM5122 yanlış montaj | Düşük | Kritik | ADR-038 H001 denetimi |

## §T Sartname Bağlantı Tablosu (Son)

| Sartname sabiti | Kapsam maddesi |
|---|---|
| 512 örnek | AC-1, B1, B5 |
| 48 kHz | AC-1, B2 |
| 32-bit float (ADR-017) | AC-2, B1, B4 |
| PCM3168A (ADR-038 H001) | B3, AC-4 |
| DC >0,5 V → röle | AC-4, B6 |
| ASIO Exclusive Lock | AC-5, B7 |

## §U Referanslar (Kapsam — Son)

- ADR-017 (32-bit float), ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13
- `K005-firmware/boot/boot-kapsam.md` (boot bağımlılığı)
- `K005-firmware/audio-control/index.md` (grup indeksi — dokunulmadı)

## §V Kapsam Dışı Bırakılan Konular (Son Liste)

| Konu | Neden kapsam dışı | Sorumlu alan |
|---|---|---|
| Analog ön yükeş/güç amplifikatörü | Donanım tasarımı | `.ai/electronics/` |
| JTAG/SWD fiziksel köprü | Donanım tasarımı | `.ai/electronics/` (boot-dogrulama §A) |
| Web API / kullanıcı arayüzü | PHP host domain | AIOrchestrator (`fw` ayrı) |
| OTA paketleme/dağıtım | Güncelleme mantığı | `update` alt-modülü |
| dts binding üretim döngüsü | Cihaz keşfi | `device-control` alt-modülü |
| Firmware OTA, bağımlılık bump, refactor | Uygulama değişikliği | ayrı onay ister (kural 13) |

## §W Doğrulanmış Web Kaynakları (Kapsam)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Zephyr I2S örneği 48 kHz veri yolunu gösterir | https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html | 2026-10-13 | HTTP 200 |
| DMA peripheralleri veri aktarımını tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |
| Pipe tabanlı veri geçişi kernel servislerindedir | https://docs.zephyrproject.org/latest/kernel/services/data_passing/pipes.html | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
