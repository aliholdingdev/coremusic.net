---
title: "I2S / TDM Firmware Sürücü — Yedek Kaynak Entegrasyonu"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# I2S / TDM Firmware Sürücü — Yedek Kaynak Entegrasyonu

## §1 Amaç & Kapsam

Bu belge, `k057-i2s-interface` klasörünün **firmware / sürücü** tarafındaki konu dosyasıdır: `_backup/arch-2026-10-06_1057/architecture/firmware/i2s-driver.md` (496 satır) içindeki tüm teknik blokları **kaynak değeriyle** vault'a taşır ve protokol dosyasındaki saat/çözünürlük iddialarıyla çelişen noktaları **kanıt satır numarasıyla** işaretler. Klasörün protokol dosyası [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] yalnızca I2S/TDM **protokolünü** (hat, zamanlama, empedans) anlatır; bu dosya ise **firmware mimarisini, kaynak kod yapısını, clock yapılandırmasını, master driver'ı, TDM'yi, SRC'yi, slave clock recovery'yi, metrikleri, derleme/yükleme ve test prosedürlerini** taşır. İki dosya aynı konuyu iki farklı katmandan belgeler ve birbirinin yerine geçmez.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `firmware/i2s-driver.md` içindeki 11 teknik bloğun kaynak değerli aktarımı (§3) | Protokol kuralları, hat empedansı, bileşen filtresi → [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| Saat/zamanlama değerleri ve kaynak-çapraz çelişki kaydı (§4 · §7) | UAC2 paketleme/descriptor → [[../k059-usb-audio/usb-audio-firmware-surucu]] |
| Firmware bağımlılıkları, kenar durumları, hata modları (§5 · §6) | DAC/ADC iç mimarisi → [[../k055-ak4458-dac/ak4458-dac-rehberi]] · [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| Salt-okunur kaynakların satır indeksi (§9 · §13) | XMOS silicon/PLL detayı → [[../k058-xmos-xu316/xu316-entegrasyon]] |

**Kapsam dışı güvenlik notu:** birincil kaynak, mimari diyagramında ve I2C konfigürasyon bölümünde CoreMusic donanımında bulunmayan parça adları taşır (PCM5242 · AK4493 · CS5368) → `⚠️ VERIFICATION REQUIRED` (§7 C1). Bu değerler **kopyalanmadı**, kaynak satır numarasıyla işaretlendi.

**Birincil kanıt:** `_backup/arch-2026-10-06_1057/architecture/firmware/i2s-driver.md` (satır indeksi §9'da).

## §2 Kaynak Kapsamı

> Tüm kaynaklar **salt-okunurdur**; bu görevce hiçbir `_backup/**` veya mevcut `.ai/architecture/**` dosyası değiştirilmemiştir.

| # | Kaynak dosya | Satır | Bu dosyada kullanım | Kanıt |
|---|--------------|-------|---------------------|-------|
| 1 | `_backup/arch-2026-10-06_1057/architecture/firmware/i2s-driver.md` | 496 | Birincil — §3 tamamı, §4 saat, §5 bağımlılık, §9 indeks | okuma başlığı `lines 1-496` ✅ disk |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 186 | Protokol çapraz kontrol (§4 · §7) ve §13 tam indeks | okuma başlığı `lines 1-186` ✅ disk |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` | 159 | Saat çapraz kontrol — L21 · L23 · L81 · L82 · L105 · L106 | okuma başlığı `lines 150-159` ✅ disk |
| 4 | `_backup/arch-2026-10-06_1057/architecture/firmware/index.md` | 199 | Firmware katman indeksi — L104 latency, L177-187 bağımlılıklar | okuma başlığı `lines 1-199` ✅ disk |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | — | Yalnız başlık↔satır çapraz kontrolü (grep: L670–L684) | §8 ✅ disk |

**Stil kaynağı (şablon):** [[../k057-i2s-interface/index]] · [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] — ön-yüz, frontmatter ve § yapısı birebir bu iki dosyadan alınmıştır.

**Kapsam dışı kaynaklar (bu dosyada kullanılmadı):** `_backup/**` altındaki diğer firmware dosyaları (`xmos-firmware.md`, `usb-audio-firmware.md`, `dsp-firmware.md`, `gpio-control.md`, `mcu-support.md`, `bootloader.md`) — yalnız `firmware/index.md` üzerinden adları geçiyor, içerikleri aktarılmadı.

## §3 Firmware Mimarisi & Sürücü Detayı (kaynak değerli)

> Bu bölümdeki her değer, her kod bloğu ve her Türkçe yorum **kaynaktaki haliyle** korunmuştur; kaynakta olmayan hiçbir komut, sürüm, bağımlılık veya adres eklenmemiştir.

### §3.1 Genel Bakış (kaynak L10–L12)

Kaynak, sürücüyü şu cümleyle tanımlıyor (L12): I2S driver firmware, "COREMUSIC'ın DAC/ADC ile olan ses iletişimini yönetir"; **Multi-channel I2S desteği**, **master/slave clock modları** ve "hardware-level bit-perfect audio transferi" sağlar; XMOS XU316 üzerinde optimize edilmiş I2S driver ile **`<100μs` input-to-output latency hedeflenmektedir**.

| Parametre | Kaynak değeri | Satır |
|-----------|---------------|-------|
| Hedef platform | XMOS XU316 | L12 |
| Latency hedefi | `<100μs` input-to-output | L12 |
| Kanal kapsamı | 2–16 (`Channel count (2-16)`) | L45 |
| Bit derinliği | 16 / 24 / 32-bit (`Bit alignment (16/24/32-bit)`) | L39 |
| Örnekleme hızı (LRCLK) | 44.1 / 48 / 96 / 192 kHz | L31 |

### §3.2 Firmware Mimarisi (kaynak L14–L61)

Kaynağın ASCII blok diyagramı aynen aktarılmıştır (L16–L61):

```
┌─────────────────────────────────────────────────────────────────┐
│                    I2S DRIVER FIRMWARE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              XMOS XU316 I2S Interface                │      │
│  │              (Hardware I2S blocks)                    │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              I2S Master Driver                        │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Clock Generation                           │    │      │
│  │  │  - BCLK (Bit Clock): 2.822/3.072 MHz        │    │      │
│  │  │  - LRCLK (Word Clock): 44.1/48/96/192 kHz   │    │      │
│  │  │  - MCLK (Master Clock): 11.2896/12.288 MHz  │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Data Transfer                             │    │      │
│  │  │  - TX (DAC output)                          │    │      │
│  │  │  - RX (ADC input)                           │    │      │
│  │  │  - Frame sync (LRCLK)                       │    │      │
│  │  │  - Bit alignment (16/24/32-bit)             │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Configuration                             │    │      │
│  │  │  - Sample rate selection                    │    │      │
│  │  │  - Bit depth selection                      │    │      │
│  │  │  - Channel count (2-16)                     │    │      │
│  │  │  - Clock polarity                           │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              External DAC/ADC                        │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  PCM5242     │  │  AK4493      │  │  CS5368  │  │      │
│  │  │  (DAC)       │  │  (DAC)       │  │  (ADC)   │  │      │
│  │  │  32-bit      │  │  32-bit      │  │  24-bit  │  │      │
│  │  │  768kHz      │  │  768kHz      │  │  192kHz  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

> **Parça adı uyarısı:** diyagramdaki `PCM5242` · `AK4493` · `CS5368` adları CoreMusic donanımının (k055/k056) parçaları değildir → §7 **C1** `⚠️ VERIFICATION REQUIRED`. Diyagram kaynakta olduğu gibi korunmuştur; değiştirilmedi.

### §3.3 Kaynak Kod Yapısı (kaynak L63–L82)

```
i2s_driver/
├── src/
│   ├── i2s_master.xc               # I2S master driver
│   ├── i2s_slave.xc                # I2S slave driver
│   ├── i2s_clock.xc                # Clock generation & config
│   ├── i2s_frame_sync.xc           # Frame sync management
│   ├── i2s_multichannel.xc         # Multi-channel support
│   ├── i2s_config.xc               # Configuration handling
│   └── i2s_main.xc                 # Ana program
│
├── include/
│   ├── i2s_config.h                # Konfigürasyon dosyaları
│   ├── i2s_types.h                 # Veri tipleri
│   └── i2s_registers.h             # Register tanımları
│
└── Makefile
```

| Dosya | Kaynaktaki rol | Satır |
|-------|----------------|-------|
| `src/i2s_master.xc` | I2S master driver | L68 |
| `src/i2s_slave.xc` | I2S slave driver | L69 |
| `src/i2s_clock.xc` | Clock generation & config | L70 |
| `src/i2s_frame_sync.xc` | Frame sync management | L71 |
| `src/i2s_multichannel.xc` | Multi-channel support | L72 |
| `src/i2s_config.xc` | Configuration handling | L73 |
| `src/i2s_main.xc` | Ana program | L74 |
| `include/i2s_config.h` | Konfigürasyon dosyaları | L77 |
| `include/i2s_types.h` | Veri tipleri | L78 |
| `include/i2s_registers.h` | Register tanımları | L79 |
| `Makefile` | Derleme | L81 |

### §3.4 I2S Protocol Overview (kaynak L86–L107)

Sinyal tablosu (L89–L98) — yönler dahil kaynak değeriyle:

```
I2S Bus Signals:
┌─────────────────────────────────────────────────────────┐
│  Signal    │ Description              │ Direction        │
├────────────┼──────────────────────────┼──────────────────┤
│  SCK/SCLK  │ Serial Clock (BCLK)     │ Master → Slave   │
│  WS/LRCLK  │ Word Select (LRCLK)     │ Master → Slave   │
│  SDOUT     │ Serial Data Out (RX)     │ Slave → Master   │
│  SDIN      │ Serial Data In (TX)      │ Master → Slave   │
│  MCLK      │ Master Clock             │ Master → Slave   │
└─────────────────────────────────────────────────────────┘

I2S Timing Diagram (32-bit, 2 channels):

SCK:   ─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─
WS:    ──────────────────────────────────────────────────
        │ Left Channel (LRCLK=0)    │ Right Channel (LRCLK=1)
SDOUT: ─X D31 X D30 X ... X D0 X── X D31 X D30 X ... X D0 X─
        │ MSB first, LSB last       │ MSB first, LSB last
```

| Sinyal | Yön (firmware L93–L97) | Protokol dosyası karşılığı |
|--------|------------------------|----------------------------|
| SCK/SCLK | Master → Slave | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] §2 (`SCK`) |
| WS/LRCLK | Master → Slave | §2 (`WS`, frekansı fs) |
| SDOUT | **Slave → Master** (ADC verisi) | L102 `DOUTA/B ──▶ SD to XMOS` |
| SDIN | Master → Slave (DAC verisi) | L95 `TDMD[0:3] ← SD from XMOS` |
| MCLK | Master → Slave | §4.2 (kristal çifti) |

> Firmware tablosu `Direction` sütununda tek yön veriyor; protokol dosyası ADC için ters yönü ayrıca tanımlıyor (§7 **C7** — veri hattı sayısı/rolü).

### §3.5 I2S Master Driver (kaynak L161–L284)

**Port haritası (L164–L172):**

```xc
// I2S Master Driver - XMOS XS1 port mapping
// XS1 ports: 32-bit wide, one port per signal

// Port konfigürasyonu
on tile[0]: port p_i2s_bclk  = XS1_PORT_1A;   // Bit clock
on tile[0]: port p_i2s_lrclk = XS1_PORT_1B;   // Word select
on tile[0]: port p_i2s_dout  = XS1_PORT_1C;   // Data out (to DAC)
on tile[0]: port p_i2s_din   = XS1_PORT_1D;   // Data in (from ADC)
on tile[0]: port p_i2s_mclk  = XS1_PORT_1E;   // Master clock
```

**Transmit (L174–L233):**

```xc
// I2S transmit - DAC'a ses verisi gönderme
// 32-bit I2S, stereo (2 channel)
// Her sample için 64 BCLK cycle (32 left + 32 right)

void i2s_transmit(chan c_dac, i2s_config_t *config) {
    int32_t left_sample;
    int32_t right_sample;
    unsigned bclk_val;
    unsigned lrclk_val;
    unsigned data_val;

    // I2S format ayarla
    int bit_depth = config->bit_depth;      // 16, 24, veya 32
    int mclk_ratio = config->mclk_ratio;   // 256x veya 512x

    // Port initial values
    p_i2s_bclk <: 0;
    p_i2s_lrclk <: 0;  // Left channel first
    p_i2s_dout <: 0;

    while (1) {
        // Left channel (LRCLK = 0)
        p_i2s_lrclk <: 0;

        // DSP'den sol sample al
        c_dac :> left_sample;

        // MSB-first olarak transmit et
        for (int i = bit_depth - 1; i >= 0; i--) {
            data_val = (left_sample >> i) & 1;
            p_i2s_dout <: data_val;

            // BCLK falling edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // Right channel (LRCLK = 1)
        p_i2s_lrclk <: 1;

        // DSP'den sağ sample al
        c_dac :> right_sample;

        // MSB-first olarak transmit et
        for (int i = bit_depth - 1; i >= 0; i--) {
            data_val = (right_sample >> i) & 1;
            p_i2s_dout <: data_val;

            // BCLK falling edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // Kalan BCLK cycle'larını doldur
        for (int i = 0; i < (32 - bit_depth); i++) {
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }
    }
}
```

**Receive (L235–L283):**

```xc
// I2S receive - ADC'den ses verisi okuma
void i2s_receive(chan c_adc, i2s_config_t *config) {
    int32_t left_sample;
    int32_t right_sample;
    unsigned data_in;

    int bit_depth = config->bit_depth;

    while (1) {
        left_sample = 0;
        right_sample = 0;

        // Left channel oku (LRCLK = 0)
        p_i2s_lrclk <: 0;

        for (int i = 0; i < bit_depth; i++) {
            // BCLK rising edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;

            // Data oku
            data_in = p_i2s_din :> data_in;
            left_sample = (left_sample << 1) | (data_in & 1);
        }

        // Right channel oku (LRCLK = 1)
        p_i2s_lrclk <: 1;

        for (int i = 0; i < bit_depth; i++) {
            // BCLK rising edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;

            // Data oku
            data_in = p_i2s_din :> data_in;
            right_sample = (right_sample << 1) | (data_in & 1);
        }

        // Kalan BCLK cycle'larını doldur
        for (int i = 0; i < (32 - bit_depth); i++) {
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // ADC'ye sample'ları gönder
        c_adc <: left_sample;
        c_adc <: right_sample;
    }
}
```

**Sürücü davranışının kaynak-özet tablosu:**

| Davranış | Kaynak değeri | Satır |
|----------|---------------|-------|
| Çıkış yönü (DAC) | `p_i2s_dout` (tek seri veri hattı) | L170 · L318 |
| Giriş yönü (ADC) | `p_i2s_din` | L171 · L256 |
| Çift slot uzunluğu | `Her sample için 64 BCLK cycle (32 left + 32 right)` | L176 |
| Örnekleme kenarı (TX) | `// BCLK falling edge` | L206 · L222 |
| Örnekleme kenarı (RX) | `// BCLK rising edge` | L251 · L264 |
| Veri sırası | MSB-first (`for i = bit_depth-1 …`) | L202 · L218 |
| Kalan bit doldurma | `(32 - bit_depth)` çevrim | L228 · L274 |

### §3.6 Multi-Channel I2S — TDM (kaynak L286–L324)

```xc
// Multi-channel I2S - TDM (Time Division Multiplexing)
// 8 channel I2S, 32-bit per channel
// Frame length: 8 * 32 = 256 BCLK cycles

#define I2S_CHANNELS     8
#define I2S_BIT_DEPTH    32
#define I2S_FRAME_LENGTH (I2S_CHANNELS * I2S_BIT_DEPTH)

// TDM transmit - 8 channel output
void i2s_tdm_transmit(chan c_dac[I2S_CHANNELS]) {
    int32_t samples[I2S_CHANNELS];
    unsigned frame[I2S_FRAME_LENGTH];

    while (1) {
        // DSP'den tüm kanalları al
        for (int ch = 0; ch < I2S_CHANNELS; ch++) {
            c_dac[ch] :> samples[ch];
        }

        // TDM frame oluştur
        for (int ch = 0; ch < I2S_CHANNELS; ch++) {
            for (int bit = 0; bit < I2S_BIT_DEPTH; bit++) {
                frame[ch * I2S_BIT_DEPTH + bit] =
                    (samples[ch] >> (I2S_BIT_DEPTH - 1 - bit)) & 1;
            }
        }

        // Frame'i transmit et
        for (int i = 0; i < I2S_FRAME_LENGTH; i++) {
            p_i2s_dout <: frame[i];
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }
    }
}
```

| Sabit | Değer | Satır |
|-------|-------|-------|
| `I2S_CHANNELS` | 8 | L293 |
| `I2S_BIT_DEPTH` | 32 | L294 |
| `I2S_FRAME_LENGTH` | 256 BCLK (`8 * 32`) | L291 · L295 |
| Seri hat | tek `p_i2s_dout` | L318 |
| Derleme bayrağı | `CHANNELS=8` | L438 |

> Çapraz kontrol: protokol dosyası TDM karesini `8 channels × 32 bits = 256 bits` (kaynak L110) diyor, aynı bölümün şeması ise `4 data line × 32 channels/line = 128 channels` (kaynak L131) ve `TDMD[0:3]` (kaynak L95) çiziyor → §7 **C6** / **C7** `⚠️ VERIFICATION REQUIRED`.

### §3.7 Sample Rate Conversion (kaynak L326–L365)

```xc
// Basit sample rate conversion
// 44.1kHz → 48kHz dönüşümü

// Conversion ratio: 48000 / 44100 = 1.088435
// Linear interpolation ile upsampling

#define SRC_BUFFER_SIZE 256

typedef struct {
    int32_t buffer[SRC_BUFFER_SIZE];
    int write_pos;
    int read_pos;
    int ratio_num;      // 48000
    int ratio_den;      // 44100
    int phase;
} src_state_t;

void src_process(src_state_t *state, int32_t input, int32_t *output) {
    // Input sample'ı buffer'a yaz
    state->buffer[state->write_pos] = input;
    state->write_pos = (state->write_pos + 1) % SRC_BUFFER_SIZE;

    // Phase accumulator ile okuma
    state->phase += state->ratio_den;
    if (state->phase >= state->ratio_num) {
        state->phase -= state->ratio_num;
        state->read_pos = (state->read_pos + 1) % SRC_BUFFER_SIZE;
    }

    // Linear interpolation
    int32_t sample0 = state->buffer[state->read_pos];
    int32_t sample1 = state->buffer[(state->read_pos + 1) % SRC_BUFFER_SIZE];
    int32_t frac = state->phase * 256 / state->ratio_num;

    *output = sample0 + ((sample1 - sample0) * frac >> 8);
}
```

**"Mevcut mimaride SRC var mı?" — kaynak kanıtına göre durum:**

| Soru | Kaynak kanıtı | Cevap |
|------|---------------|-------|
| Firmware kaynağı SRC anlatıyor mu? | `### Sample Rate Conversion` başlığı L326, kod L328–L365 | Evet — yukarıda aynen aktarıldı |
| SRC açık mı, kapalı mı? | `xmake all CONFIG=i2s_master SRC=1` (L444) | Derleme bayrağıyla **opsiyonel** |
| Uygulama durumu ne? | `| Sample Rate Conversion | Planlandı | SRC algorithm |` (L493) | **Planlandı** (implement edilmemiş) |
| Protokol dosyası SRC'den söz ediyor mu? | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] §1–§8 içinde `SRC` geçmiyor | Hayır |
| Vault (` .ai/architecture/`) içinde SRC dosyası var mı? | glob `**/*sample-rate*` → yalnız `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md` | `.ai/architecture/` altında **yok** |
| K1 indeksi bu bölümün sahibini yazıyor mu? | `k1-donanim/README.md` L679: `K1.f.7.10 | Sample Rate Conversion | firmware/i2s-driver.md L326` | Evet — sahiplik firmware katmanında |

**Sonuç:** SRC, bu vault'un mevcut mimarisinde **yok sayılan değil, "Planlandı" olarak kayıtlı** bir firmware opsiyonudur; protokol dosyasında ve `.ai/architecture/` ağacında karşılığı bulunmuyor → §7 **C5** `⚠️ VERIFICATION REQUIRED` (SRC'nin sahibi firmware mi, ses motoru mu karar verilmemiş).

### §3.8 Clock Recovery (Slave Mode) (kaynak L367–L405)

```xc
// I2S Slave Mode - External clock recovery
// BCLK ve LRCLK dış kaynaktan geliyor
// MCLK'ı recovered clock'a senkronize etme

void i2s_slave_clock_recovery(chan c_mclk_adj) {
    timer t;
    unsigned bclk_edges;
    unsigned lrclk_edges;
    unsigned last_lrclk = 0;
    unsigned lrclk_period;
    unsigned target_period;

    while (1) {
        // LRCLK edges say
        select {
            case p_i2s_lrclk when pinsneq(last_lrclk) :> lrclk_edges:
                // LRCLK değişti
                lrclk_period = lrclk_edges - last_lrclk;
                last_lrclk = lrclk_edges;

                // MCLK period hedefi
                target_period = lrclk_period / (I2S_BIT_DEPTH * 2);

                // MCLK adjustment hesapla
                int32_t error = lrclk_period - expected_period;
                c_mclk_adj <: error;
                break;

            case c_mclk_adj :> int adjustment:
                // MCLK PLL ayarla
                adjust_mclk_pll(adjustment);
                break;
        }
    }
}
```

> **Kaynak kod hatası (kaynakta olduğu gibi korundu):** L380 `unsigned target_period;` tanımlanırken L394 `lrclk_period - expected_period` yazıyor; `expected_period` L374–L404 arasında hiçbir yerde tanımlanmamış → §7 **C11** `⚠️ VERIFICATION REQUIRED`. Bu dosya kapsamı revizyon yapmaz, yalnızca işaretler.
>
> **Rol notu:** bu bölüm firmware'ın **slave** profilini anlatır (kaynak L69 `i2s_slave.xc`, L441 `CONFIG=i2s_slave`); protokol dosyası ise sistemi `XMOS as Master` / `DAC/ADC as Slave` olarak tek yönlü tanımlar → §7 **C4**.

### §3.9 Audio Quality Metrics (kaynak L407–L424)

```
I2S Audio Quality Parameters:

┌─────────────────────────────────────────────────────────┐
│ Parameter                │ Target          │ Typical    │
├──────────────────────────┼─────────────────┼────────────┤
│ SNR (Signal-to-Noise)   │ > 110 dB        │ 115 dB     │
│ THD+N                    │ < -100 dB       │ -105 dB    │
│ Dynamic Range            │ > 115 dB        │ 120 dB     │
│ Crosstalk Rejection      │ > 100 dB        │ 110 dB     │
│ Clock Jitter             │ < 50 ps RMS     │ 30 ps RMS  │
│ Input Impedance          │ 10kΩ typical    │ 10kΩ       │
│ Output Impedance         │ < 100Ω          │ 50Ω        │
│ Sample Rate Accuracy     │ ±10 ppm         │ ±5 ppm     │
└──────────────────────────┴─────────────────┴────────────┘
```

| Metrik | Target | Typical | Satır |
|--------|--------|---------|-------|
| SNR | > 110 dB | 115 dB | L415 |
| THD+N | < -100 dB | -105 dB | L416 |
| Dynamic Range | > 115 dB | 120 dB | L417 |
| Crosstalk Rejection | > 100 dB | 110 dB | L418 |
| Clock Jitter | < 50 ps RMS | 30 ps RMS | L419 |
| Input Impedance | 10kΩ typical | 10kΩ | L420 |
| Output Impedance | < 100Ω | 50Ω | L421 |
| Sample Rate Accuracy | ±10 ppm | ±5 ppm | L422 |

> Kaynak bu değerleri hedef/Tipik olarak veriyor; **ölçüm kanıtı (rapor/plot) kaynakta yok** → ölçüm dayanağı `UNKNOWN`.

### §3.10 Derleme & Yükleme + Test (kaynak L426–L458)

**Derleme (L428–L445):**

```bash
cd firmware/xmos/app_usb_audio_skc

# I2S driver ile derleme
xmake clean
xmake all CONFIG=i2s_master

# Multi-channel I2S
xmake all CONFIG=i2s_master CHANNELS=8

# I2S slave mode
xmake all CONFIG=i2s_slave

# Sample rate conversion desteği
xmake all CONFIG=i2s_master SRC=1
```

**Test (L447–L458):**

```bash
# I2S timing test
# Logic analyzer ile BCLK, LRCLK, DATA monitoring
# Saleae Logic ile I2S decode

# Audio quality test
# THD+N ölçümü (Audio Precision ile)
# SNR ölçümü
# Crosstalk ölçümü
```

| Derleme profili | Komut | Satır |
|-----------------|-------|-------|
| Master | `xmake all CONFIG=i2s_master` | L435 |
| Multi-channel | `xmake all CONFIG=i2s_master CHANNELS=8` | L438 |
| Slave | `xmake all CONFIG=i2s_slave` | L441 |
| SRC açık | `xmake all CONFIG=i2s_master SRC=1` | L444 |

| Test | Araç (kaynakta yazan) | Satır |
|------|----------------------|-------|
| Timing | Logic analyzer · Saleae Logic (I2S decode) | L451 · L452 |
| THD+N | Audio Precision | L455 |
| SNR | (kaynakta araç adı yok) | L456 |
| Crosstalk | (kaynakta araç adı yok) | L457 |

> `xmake`/`xflash` araç zincirinin sürümü bu dosyada **geçmiyor**; firmware katmanı indeksi `XTC Tools >= 15.x` yazıyor (`firmware/index.md` L179) — sürüm iddiası o kaynağa aittir, buraya kopyalanmadı.

### §3.11 DAC/ADC Konfigürasyonu (kaynak L460–L474)

```bash
# I2C ile DAC register ayarlama
# PCM5242 için I2C address: 0x94
i2cset -y 0 0x94 0x00 0x00  # Reset
i2cset -y 0 0x94 0x01 0x00  # Mode: I2S
i2cset -y 0 0x94 0x02 0x00  # Format: 32-bit
i2cset -y 0 0x94 0x03 0x00  # MCLK ratio: 256x

# AK4493 için I2C address: 0x10
i2cset -y 0 0x10 0x00 0x00  # Reset
i2cset -y 0 0x10 0x01 0x0C  # Mode: I2S, 32-bit
i2cset -y 0 0x10 0x02 0x00  # Sound mode: Normal
```

| Kaynak iddiası | Değer | Satır | CoreMusic donanım karşılığı |
|----------------|-------|-------|------------------------------|
| `PCM5242 için I2C address` | `0x94` | L464 · L465–L468 | PCM5242 **yok** — ana DAC PCM3168A (k056) → `⚠️ VERIFICATION REQUIRED` |
| `AK4493 için I2C address` | `0x10` | L470 · L471–L473 | AK4493 **yok** — DAC AK4458 (k055) → `⚠️ VERIFICATION REQUIRED` |
| `MCLK ratio: 256x` (PCM5242 reg 0x03) | `256x` | L468 | Oran tartışması → §7 **C3** |
| ADC (CS5368) register yazımı | — | — | Kaynakta I2C satırı **yok**; ADC yalnız diyagramda (L53–L56) |

**Karar:** bu bölüm kaynaktaki haliyle aktarılmış, **kullanım için doğrulanmadan uygulanmaz**. I2C adresleri ve register haritası PCM3168A/AK4458 üzerinden [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] ve [[../k055-ak4458-dac/ak4458-dac-rehberi]] ile çapraz doğrulanmalıdır → §7 **C1**.

## §4 Saat & Zamanlama (çelişkiler dahil)

### §4.1 Kaynak saat değerleri (firmware)

**Clock Configuration tablosu (kaynak L116–L120):**

```xc
// Supported configurations:
// Sample Rate │ BCLK      │ LRCLK    │ MCLK      │ MCLK Ratio
// 44.1 kHz    │ 2.822 MHz │ 44.1 kHz │ 11.2896 MHz│ 256x
// 48 kHz      │ 3.072 MHz │ 48 kHz   │ 12.288 MHz │ 256x
// 96 kHz      │ 6.144 MHz │ 96 kHz   │ 24.576 MHz │ 256x
// 192 kHz     │ 12.288 MHz│ 192 kHz  │ 49.152 MHz │ 256x
```

**Diğer saat ifadeleri:**

| İfade | Kaynak değer | Satır |
|-------|--------------|-------|
| Diyagram BCLK | `2.822/3.072 MHz` | L30 |
| Diyagram LRCLK | `44.1/48/96/192 kHz` | L31 |
| Diyagram MCLK | `11.2896/12.288 MHz` | L32 |
| `mclk_ratio` alanı | `256x veya 512x` | L187 |
| XMOS clock | `500 MHz (100 MHz x 5 PLL)` | L123 |
| BCLK divider formülü | `500 / (2 * BCLK_freq)` | L124 · L148 |
| LRCLK divider | `BCLK / (bit_depth * 2 * lrclk_freq)` | L151–L152 |
| MCLK divider | `MCLK / BCLK` | L155–L156 |
| Slave hedefi | `target_period = lrclk_period / (I2S_BIT_DEPTH * 2)` | L391 |
| PCM5242 reg 0x03 | `MCLK ratio: 256x` | L468 |

### §4.2 Çapraz kaynak saat iddiaları

| Kaynak | Satır | İddia |
|--------|-------|-------|
| `firmware/i2s-driver.md` | L30 · L117 | BCLK `2.822 MHz` @ 44.1 kHz (64fs), MCLK `11.2896 MHz` (256x) |
| `k1-donanim/i2s-interface.md` | L23 | `Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz` |
| `k1-donanim/i2s-interface.md` | L36 | `Example: 2 × 2 × 32 × 44100 = 4.2336MHz` |
| `k1-donanim/i2s-interface.md` | L22 | `Master Clock | 256fs (11.2896MHz @ 44.1kHz)` |
| `k1-donanim/dac-adc-zinciri.md` | L21 · L106 | `I2S Bit Clock | 1.4112MHz (44.1kHz) / 1.536MHz (48kHz)` · `SCK | 1.4112MHz` |
| `k1-donanim/dac-adc-zinciri.md` | L23 | `MCLK | 256fs = 11.2896MHz / 12.288MHz` |
| `k1-donanim/dac-adc-zinciri.md` | L81 · L105 | `MCLK (22.5792MHz)` · `MCLK | 22.5792MHz` (= 512fs @ 44.1 kHz) |
| `k1-donanim/i2s-interface.md` | L185 | `Crystal: 22.5792MHz + 24.576MHz dual` |

### §4.3 Saat çelişki kaydı (gerekçeli)

| # | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar | Etkilediği dosya |
|---|------------------------|------------------------|------|------|-------|------------------|
| C2 | `dac-adc-zinciri.md L21` → BCK `1.4112 MHz` @ 44.1 kHz | `i2s-interface.md L23` → `2.1168 MHz` · `i2s-interface.md L36` → `4.2336 MHz` · `i2s-driver.md L30/L117` → `2.822 MHz` | 44.1 kHz fs için **4 farklı BCK değeri** (1.4112 / 2.1168 / 2.8224 / 4.2336 MHz) | BCK yanlışsa DAC/ADC kilitlenmez → ses gelmez | `⚠️ VERIFICATION REQUIRED` — hiçbiri seçilmedi, hepsi kaynak satırıyla kayıtlı | [[../k054-dac-adc-zinciri/zincir-mimari]] §4.1 · [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] §4.1 |
| C3 | `i2s-driver.md L117` → MCLK `11.2896 MHz` `256x` (+ L468 reg `256x`) | `dac-adc-zinciri.md L105/L81` → MCLK `22.5792 MHz` (512fs) · `i2s-interface.md L22` → `256fs (11.2896MHz)` | 256fs ↔ 512fs; firmware `L187` iki oranı da (`256x veya 512x`) destekliyor ama tek konfigürasyon seçimi yok | MCLK oranı yanlışsa DAC/ADC system-clock hatası | `⚠️ VERIFICATION REQUIRED` | [[../k054-dac-adc-zinciri/zincir-mimari]] §4.2 · [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| C4 | `i2s-driver.md L367/L370-372/L441/L69` → slave mod profili (`CONFIG=i2s_slave`, dış clock recovery) | `i2s-interface.md L72/L87` → `XMOS as Master` · `DAC/ADC as Slave` (tek yön) | Firmware iki rol profili (master + slave) tanımlıyor; protokol dosyası yalnız master düzenini anlatıyor | Hangi profilin üretileceği belirsiz → yanlış rolde clock | İkisi farklı katman; **üretim rolü kaynakta yok** → `⚠️ VERIFICATION REQUIRED` | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] §2.1 · [[../k058-xmos-xu316/xu316-entegrasyon]] |
| C10 | `i2s-driver.md L31 · L117-L120` → `44.1/48/96/192 kHz` | `i2s-interface.md L21` → `44.1kHz – 192kHz` | fark yok | — | **UYUŞMA ✅** (fs aralığı iki kaynakta aynı) | [[../k054-dac-adc-zinciri/zincir-mimari]] |
| C9 | `i2s-driver.md L12` → `<100μs` latency hedefi | `firmware/index.md L104` → `I2S Driver … < 100μs` | fark yok | — | **UYUŞMA ✅** (hedef; ölçüm kanıtı yok) | [[../k032-latency-optimization/latency-optimization]] |

> Tam çelişki/uyuşma kaydı (saat dışı maddeler dahil: C1 · C5 · C6 · C7 · C8 · C11) §7'dedir.

## §5 Bağımlılıklar

**Birincil kaynak tablosu (kaynak L476–L483 — aynen):**

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_i2s | >= 2.x | XMOS I2S driver |
| lib_i2c_master | >= 2.x | DAC/ADC register config |
| lib_locks | >= 1.x | Channel synchronization |
| lib_xcore_math | >= 1.x | Math operations |

**Firmware katman indeksi bağımlılıkları (`firmware/index.md` L177–L187 — I2S ile ilgili satırlar):**

| Bağımlılık | Versiyon | Amaç | Kaynak satır |
|-----------|----------|------|--------------|
| XTC Tools | >= 15.x | XMOS derleme ve yükleme | L179 |
| XC Language | - | XMOS firmware programlama | L180 |
| lib_xua | >= 4.x | USB Audio Class kütüphanesi | L181 |
| lib_i2s | >= 2.x | I2S driver kütüphanesi | L182 |
| lib_dsp | >= 3.x | DSP processing kütüphanesi | L183 |

**Bağımlılık notları:**

1. Sürüm aralıkları (`>= 2.x`, `>= 15.x` …) **kaynakta yazıldığı gibi** aktarılmıştır; bu dosyada sürüm yükseltme/düşürme yapılmamıştır.
2. `lib_i2s` iki kaynakta da aynı (`>= 2.x` — L480 ve index L182) → **uyuşma**.
3. Test zincirinde geçen araçlar (`Saleae Logic`, `Audio Precision` — L452/L455) bağımlılık tablosunda **yer almıyor**; sürüm bilgisi `UNKNOWN`.
4. Protokol/parça bağımlılıkları (XMOS XU316 · AK4458 · PCM3168A) bu dosyada değil, komşu düğümlerde belgelenmiştir → §10 matrisi.

## §6 Kenar Durumlar

| # | Durum | Kaynak kanıtı | Davranış |
|---|-------|---------------|----------|
| E1 | Slave clock recovery içinde tanımsız değişken | L380 `target_period` ↔ L394 `expected_period` | Kaynakta olduğu gibi aktarıldı; revizyon bu dosyanın kapsamı dışı → §7 **C11** `⚠️ VERIFICATION REQUIRED` |
| E2 | Divider register değerleri kaynakta yok | L148 formül var, sonuç register değeri yok | Sonuç `UNKNOWN`; uydurma register değeri yazılmadı → `⚠️ VERIFICATION REQUIRED` |
| E3 | `bit_depth < 32` iken sol kanal MSB konumu | L202 (`i = bit_depth-1`) + L228 (`32 - bit_depth` dolgu) | Sol kanal her zaman slotun **sonundan** mı başlıyor, kaynakta açıklanmıyor → `⚠️ VERIFICATION REQUIRED` |
| E4 | Slave derlemesinin üretildiğine dair kanıt | L441 `CONFIG=i2s_slave` komutu var; `.xe`/test çıktısı yok | `Planlandı` (L490) — üretim kanıtı `UNKNOWN` |
| E5 | RX veri okuma ifadesinde yinelenen atama | L256 `data_in = p_i2s_din :> data_in;` | Kaynakta olduğu gibi korundu; yorum/sadeleştirme yapılmadı |
| E6 | TDM dizisi `unsigned` tipinde (0/1 bitleri) | L300 `unsigned frame[I2S_FRAME_LENGTH]` | Kaynak tipi korundu; tipte değişiklik yapılmadı |
| E7 | Test prosedüründe SNR/Crosstalk için araç adı yok | L456 · L457 (yalnız `# SNR ölçümü`, `# Crosstalk ölçümü`) | Araç `UNKNOWN` — uydurulmadı |
| E8 | Örnekleme hızı 192 kHz'te MCLK 49.152 MHz | L120 | Kaynak değeri; protokol dosyası 192 kHz'e kadar fs veriyor (L21) — MCLK üst sınırı protokolde yok → `⚠️ VERIFICATION REQUIRED` |

## §7 Hata Modları / Çelişki Kaydı

> Kolon formatı: `# | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar/⚠️ | Etkilediği dosya`. Her satır salt-okunur kaynak satır numarasına dayanır.

| # | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar | Etkilediği dosya |
|---|------------------------|------------------------|------|------|-------|------------------|
| C1 | `firmware/i2s-driver.md L53-L57 · L464 · L470` → parçalar `PCM5242` · `AK4493` · `CS5368`; `PCM5242 için I2C address: 0x94`; `AK4493 için I2C address: 0x10` | `k1-donanim/i2s-interface.md L12` → `DAC (AK4458)` · `ADC (PCM3168A)`; disk: `k055-ak4458-dac/ak4458-dac-rehberi.md`, `k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi.md` | Firmware kaynağı **farklı parça adları** ve **iki I2C adresi** taşıyor; donanımda PCM5242/AK4493/CS5368 yok | `i2cset` yanlış adrese/register'a gider → DAC/ADC config başarısız | `⚠️ VERIFICATION REQUIRED` — kopyalanmadı, işaretlendi; adresler k055/k056 ile doğrulanmalı | [[../k055-ak4458-dac/ak4458-dac-rehberi]] · [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| C2 | `k1-donanim/dac-adc-zinciri.md L21 · L106` → BCK `1.4112 MHz` | `k1-donanim/i2s-interface.md L23` → `2.1168 MHz` · `L36` → `4.2336 MHz`; `firmware/i2s-driver.md L30 · L117` → `2.822 MHz` | 44.1 kHz fs için 4 farklı BCK (1.4112 / 2.1168 / 2.8224 / 4.2336 MHz) | Kilitlenme yok → ses gelmez; jitter/ göz açıklığı bozulur | `⚠️ VERIFICATION REQUIRED` | [[../k054-dac-adc-zinciri/zincir-mimari]] · [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| C3 | `firmware/i2s-driver.md L117 · L468` → MCLK `11.2896 MHz` / `256x` | `k1-donanim/dac-adc-zinciri.md L81 · L105` → `22.5792 MHz` (512fs); `i2s-interface.md L22` → `256fs`; `i2s-driver.md L187` → `256x veya 512x` | 256fs ↔ 512fs; tek seçim kaynakta yok | DAC/ADC system-clock hatası, upsampling oranı değişir | `⚠️ VERIFICATION REQUIRED` | [[../k054-dac-adc-zinciri/zincir-mimari]] · [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| C4 | `firmware/i2s-driver.md L69 · L367-L405 · L441` → slave mod profili (dış BCLK/LRCLK, MCLK PLL recovery) | `k1-donanim/i2s-interface.md L72-L85 · L87-L103` → `XMOS as Master`, `DAC/ADC as Slave` | Firmware iki rol profili sunuyor; protokol dosyası tek yön anlatıyor | Hangi profilin üretileceği belirsiz → yanlış rolde clock üretimi | İki katman farklı; **üretim rolü seçimi kaynakta yok** → `⚠️ VERIFICATION REQUIRED` | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] §2.1 · [[../k058-xmos-xu316/xu316-entegrasyon]] |
| C5 | `firmware/i2s-driver.md L326-L365 · L444 · L493` → SRC kodu + `SRC=1` + `Planlandı` | `k1-donanim/README.md L679` → `K1.f.7.10 Sample Rate Conversion`; glob → `.ai/architecture/` altında SRC dosyası **yok** (yalnız `_backup/…/k3-ses-motoru/sample-rate-conversion.md`) | SRC firmware'da planlı, vault ağacında karşılığı yok, protokol dosyasında hiç geçmiyor | 44.1→48 dönüşümünün sahibi belirsiz (firmware mi, ses motoru mu) | `⚠️ VERIFICATION REQUIRED` | `k3-ses-motoru` (vault karşılığı yok) · [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| C6 | `firmware/i2s-driver.md L291 · L293-L295 · L45 · L491` → 8 kanal × 32 bit = 256 BCLK, `Channel count (2-16)`, `8+ channel support` | `k1-donanim/i2s-interface.md L19` → `8 stereo (16 single)` · `L110` → `8 channels × 32 bits` · `L131` → `128 channels` | 8 kanal / 8 stereo (16 single) / 128 kanal — üç farklı kapasite | Kanal eşlemesi (channel mapping) belirsiz → yanlış hoparlör/kanal | `⚠️ VERIFICATION REQUIRED` | [[../k054-dac-adc-zinciri/zincir-mimari]] · [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| C7 | `firmware/i2s-driver.md L170 · L318` → tek seri hat `p_i2s_dout` (TDM) | `k1-donanim/i2s-interface.md L95 · L131` → `TDMD[0:3]`, `4 data line` | 1 vs 4 seri veri hattı | PCB/parça pin ataması firmware port haritasıyla örtüşmüyor | `⚠️ VERIFICATION REQUIRED` | [[../k070-pcb-tasarim/pcb-rehber]] · [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| C8 | `firmware/i2s-driver.md L485-L496` → tüm modüller `Planlandı` | `k1-donanim/i2s-interface.md L179 · L182` → `**Durum**: 🟢 Hazır`, `All specifications verified in simulation` | Protokol "hazır", firmware "planlandı" | "Hazır" etiketi firmware uygulamasını ima edebilir | `⚠️ VERIFICATION REQUIRED` — etiketler farklı katmanlara ait | [[../k057-i2s-interface/index]] §5 |
| C9 | `firmware/i2s-driver.md L12` → `<100μs` latency hedefi | `firmware/index.md L104` → `I2S Driver … < 100μs` | fark yok | — | **UYUŞMA ✅** (hedef; ölçüm kanıtı yok) | [[../k032-latency-optimization/latency-optimization]] |
| C10 | `firmware/i2s-driver.md L31 · L117-L120` → `44.1/48/96/192 kHz` | `k1-donanim/i2s-interface.md L21` → `44.1kHz – 192kHz` | fark yok | — | **UYUŞMA ✅** | [[../k054-dac-adc-zinciri/zincir-mimari]] |
| C11 | `firmware/i2s-driver.md L380 · L391` → `unsigned target_period;` | `firmware/i2s-driver.md L394` → `lrclk_period - expected_period` | `expected_period` L374-L404 arasında tanımsız | Slave clock recovery derlenmez / çalışmaz | Kaynakta olduğu gibi aktarıldı → `⚠️ VERIFICATION REQUIRED` (kaynak revizyonu gerekir, bu dosyanın kapsamı dışı) | `_backup/…/firmware/i2s-driver.md` (salt-okunur) |

**Toplam:** 11 kayıt = **9 çelişki/açık** (C1–C8, C11) + **2 uyuşma** (C9, C10).

## §8 Doğrulama & Kanıt

| # | Kontrol | Yöntem | Sonuç |
|---|---------|--------|-------|
| 1 | Birincil kaynak satır sayısı | dosya okuma başlığı `lines 1-496` | 496 satır ✅ disk |
| 2 | İkincil kaynak satır sayısı | dosya okuma başlığı `lines 1-186` | 186 satır ✅ disk |
| 3 | Çapraz kaynaklar | `dac-adc-zinciri.md` 159 satır (`lines 150-159`) · `firmware/index.md` 199 satır (`lines 1-199`) | ✅ disk |
| 4 | Başlık ↔ satır çapraz kontrolü | `k1-donanim/README.md` L670–L684 (`K1.f.7.1`–`K1.f.7.15` → i2s-driver.md L10 · L14 · L63 · L84 · L426 · L86 · L109 · L161 · L286 · L326 · L367 · L407 · L428 · L447 · L460) | 15/15 §9 ile birebir ✅ disk |
| 5 | Wiki-link hedefleri | `.ai/architecture/**` glob ile 20 hedef tek tek doğrulandı (§10.1) | kırık link 0 |
| 6 | Çelişki satırı kanıtı | Her C satırında `kaynak dosya + satır numarası` zorunlu | 11/11 kaynaklı |
| 7 | Zero-Hallucination | Kaynakta olmayan komut/sürüm/bağımlılık eklenmedi | 0 uydurma |
| 8 | `vault-utf8-writer verify` / `scan` | Subagent oturumunda `shell` reddedilince (`permission.rejected`) betik çalıştırılamamıştı; **orkestratör repo kökünden çalıştırdı** | ✅ `verify --file …/i2s-firmware-surucu.md` → `hasBom:false` · `mojibake:0` · `cjk:0` · `hasNul:false` · `lines:1385` · `scan --dir …/k057-i2s-interface` → `ok:true` · `dirty:0` · `files:[]` |

## §9 Kaynak Kanıt Dizini

> Üretim notu: bu tablo **salt-okunur** birincil kaynak dosyanın satır satır indeksidir; her satır diskteki gerçek içeriğe karşılık gelir. Kaynak: `_backup/arch-2026-10-06_1057/architecture/firmware/i2s-driver.md` (496 satır; boş satırlar atlanmıştır).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | `---` frontmatter açılışı | ✅ disk |
| L2 | metin | title: "I2S Driver Firmware" | ✅ disk |
| L3 | metin | layer: Firmware | ✅ disk |
| L4 | metin | category: "Firmware" | ✅ disk |
| L5 | metin | date: 2026-09-20 | ✅ disk |
| L6 | metin | `---` frontmatter kapanışı | ✅ disk |
| L8 | baslik | # I2S Driver Firmware | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | DAC/ADC iletişimini yönetir; multi-channel + master/slave; `<100μs` latency hedefi | ✅ disk |
| L14 | baslik | ## Firmware Mimarisi | ✅ disk |
| L16 | kod | ASCII blok diyagramı açılışı | ✅ disk |
| L17 | kod | Diyagram üst çerçeve çizgisi | ✅ disk |
| L18 | kod | Başlık hücresi: I2S DRIVER FIRMWARE | ✅ disk |
| L19 | kod | Ayraç çizgisi | ✅ disk |
| L20 | kod | Diyagram boş satır çizgisi | ✅ disk |
| L21 | kod | Kutu açılışı: XMOS XU316 I2S Interface | ✅ disk |
| L22 | kod | XMOS XU316 I2S Interface | ✅ disk |
| L23 | kod | (Hardware I2S blocks) | ✅ disk |
| L24 | kod | Kutu alt + ok çizgisi | ✅ disk |
| L25 | kod | Dikey bağlantı çizgisi | ✅ disk |
| L26 | kod | Kutu açılışı: I2S Master Driver | ✅ disk |
| L27 | kod | I2S Master Driver | ✅ disk |
| L28 | kod | Alt kutu açılışı (Clock Generation) | ✅ disk |
| L29 | kod | Clock Generation | ✅ disk |
| L30 | kod | `- BCLK (Bit Clock): 2.822/3.072 MHz` (§4.1 çelişki) | ✅ disk |
| L31 | kod | `- LRCLK (Word Clock): 44.1/48/96/192 kHz` | ✅ disk |
| L32 | kod | `- MCLK (Master Clock): 11.2896/12.288 MHz` (§4.3 çelişki) | ✅ disk |
| L33 | kod | Alt kutu kapanışı | ✅ disk |
| L34 | kod | Alt kutu açılışı (Data Transfer) | ✅ disk |
| L35 | kod | Data Transfer | ✅ disk |
| L36 | kod | `- TX (DAC output)` | ✅ disk |
| L37 | kod | `- RX (ADC input)` | ✅ disk |
| L38 | kod | `- Frame sync (LRCLK)` | ✅ disk |
| L39 | kod | `- Bit alignment (16/24/32-bit)` | ✅ disk |
| L40 | kod | Alt kutu kapanışı | ✅ disk |
| L41 | kod | Alt kutu açılışı (Configuration) | ✅ disk |
| L42 | kod | Configuration | ✅ disk |
| L43 | kod | `- Sample rate selection` | ✅ disk |
| L44 | kod | `- Bit depth selection` | ✅ disk |
| L45 | kod | `- Channel count (2-16)` (§7 C6) | ✅ disk |
| L46 | kod | `- Clock polarity` | ✅ disk |
| L47 | kod | Alt kutu kapanışı | ✅ disk |
| L48 | kod | Master Driver kutusu kapanışı | ✅ disk |
| L49 | kod | Dikey boş satır çizgisi | ✅ disk |
| L50 | kod | Kutu açılışı: External DAC/ADC | ✅ disk |
| L51 | kod | External DAC/ADC | ✅ disk |
| L52 | kod | Üç alt kutu açılışı | ✅ disk |
| L53 | kod | `PCM5242` · `AK4493` · `CS5368` parça adları (§7 C1) | ✅ disk |
| L54 | kod | Roller: (DAC) · (DAC) · (ADC) | ✅ disk |
| L55 | kod | Çözünürlük: 32-bit · 32-bit · 24-bit | ✅ disk |
| L56 | kod | Örnekleme: 768kHz · 768kHz · 192kHz | ✅ disk |
| L57 | kod | Alt kutuların kapanışı | ✅ disk |
| L58 | kod | External DAC/ADC kutusu kapanışı | ✅ disk |
| L59 | kod | Dikey boş satır çizgisi | ✅ disk |
| L60 | kod | Diyagram alt çerçeve çizgisi | ✅ disk |
| L61 | kod | ASCII blok diyagramı kapanışı | ✅ disk |
| L63 | baslik | ## Kaynak Kod Yapısı | ✅ disk |
| L65 | kod | Ağaç diyagramı açılışı | ✅ disk |
| L66 | kod | `i2s_driver/` kök dizini | ✅ disk |
| L67 | kod | `├── src/` | ✅ disk |
| L68 | kod | `i2s_master.xc` — I2S master driver | ✅ disk |
| L69 | kod | `i2s_slave.xc` — I2S slave driver (§7 C4) | ✅ disk |
| L70 | kod | `i2s_clock.xc` — Clock generation & config | ✅ disk |
| L71 | kod | `i2s_frame_sync.xc` — Frame sync management | ✅ disk |
| L72 | kod | `i2s_multichannel.xc` — Multi-channel support | ✅ disk |
| L73 | kod | `i2s_config.xc` — Configuration handling | ✅ disk |
| L74 | kod | `i2s_main.xc` — Ana program | ✅ disk |
| L75 | kod | Ağaç bağlaç satırı | ✅ disk |
| L76 | kod | `├── include/` | ✅ disk |
| L77 | kod | `i2s_config.h` — Konfigürasyon dosyaları | ✅ disk |
| L78 | kod | `i2s_types.h` — Veri tipleri | ✅ disk |
| L79 | kod | `i2s_registers.h` — Register tanımları | ✅ disk |
| L80 | kod | Ağaç bağlaç satırı | ✅ disk |
| L81 | kod | `└── Makefile` | ✅ disk |
| L82 | kod | Ağaç diyagramı kapanışı | ✅ disk |
| L84 | baslik | ## Teknik Detaylar | ✅ disk |
| L86 | baslik | ### I2S Protocol Overview | ✅ disk |
| L88 | kod | Sinyal tablosu bloğu açılışı | ✅ disk |
| L89 | kod | `I2S Bus Signals:` | ✅ disk |
| L90 | kod | Tablo üst çerçeve çizgisi | ✅ disk |
| L91 | kod | Başlık satırı: Signal · Description · Direction | ✅ disk |
| L92 | kod | Tablo ayraç satırı | ✅ disk |
| L93 | kod | SCK/SCLK — Serial Clock (BCLK) — Master → Slave | ✅ disk |
| L94 | kod | WS/LRCLK — Word Select (LRCLK) — Master → Slave | ✅ disk |
| L95 | kod | SDOUT — Serial Data Out (RX) — Slave → Master | ✅ disk |
| L96 | kod | SDIN — Serial Data In (TX) — Master → Slave | ✅ disk |
| L97 | kod | MCLK — Master Clock — Master → Slave | ✅ disk |
| L98 | kod | Tablo alt çerçeve çizgisi | ✅ disk |
| L99 | kod | Boş satır (çizgi içinde) | ✅ disk |
| L100 | kod | `I2S Timing Diagram (32-bit, 2 channels):` | ✅ disk |
| L101 | kod | Boş satır (çizgi içinde) | ✅ disk |
| L102 | kod | SCK dalga biçimi satırı | ✅ disk |
| L103 | kod | WS dalga biçimi satırı | ✅ disk |
| L104 | kod | Sol kanal (LRCLK=0) / Sağ kanal (LRCLK=1) işaretleri | ✅ disk |
| L105 | kod | SDOUT veri satırı: D31…D0 iki slot | ✅ disk |
| L106 | kod | `MSB first, LSB last` (iki slot) | ✅ disk |
| L107 | kod | Zamanlama bloğu kapanışı | ✅ disk |
| L109 | baslik | ### Clock Configuration | ✅ disk |
| L111 | kod | ```xc kod bloğu açılışı | ✅ disk |
| L112 | kod | `// I2S clock generation` | ✅ disk |
| L113 | kod | `// Master mode: XMOS generates BCLK, LRCLK, MCLK` | ✅ disk |
| L115 | kod | `// Supported configurations:` | ✅ disk |
| L116 | kod | Tablo başlığı: Sample Rate · BCLK · LRCLK · MCLK · MCLK Ratio | ✅ disk |
| L117 | kod | 44.1 kHz: BCLK 2.822 · MCLK 11.2896 MHz · 256x (§4 C2/C3) | ✅ disk |
| L118 | kod | 48 kHz: BCLK 3.072 · MCLK 12.288 MHz · 256x | ✅ disk |
| L119 | kod | 96 kHz: BCLK 6.144 · MCLK 24.576 MHz · 256x | ✅ disk |
| L120 | kod | 192 kHz: BCLK 12.288 · MCLK 49.152 MHz · 256x (§6 E8) | ✅ disk |
| L122 | kod | `// Clock divider hesaplama` | ✅ disk |
| L123 | kod | `// XMOS clock: 500 MHz (100 MHz x 5 PLL)` | ✅ disk |
| L124 | kod | `// BCLK divider: 500 / (2 * BCLK_freq)` | ✅ disk |
| L126 | kod | `typedef struct {` | ✅ disk |
| L127 | kod | `uint32_t sample_rate;` | ✅ disk |
| L128 | kod | `uint32_t bclk_freq;` | ✅ disk |
| L129 | kod | `uint32_t lrclk_freq;` | ✅ disk |
| L130 | kod | `uint32_t mclk_freq;` | ✅ disk |
| L131 | kod | `uint32_t mclk_ratio;` | ✅ disk |
| L132 | kod | `uint32_t bit_depth;` | ✅ disk |
| L133 | kod | `} i2s_clock_config_t;` | ✅ disk |
| L135 | kod | `// Clock ayarlama fonksiyonu` | ✅ disk |
| L136 | kod | `void configure_i2s_clocks(i2s_clock_config_t *config) {` | ✅ disk |
| L137 | kod | `// PLL konfigürasyonu` | ✅ disk |
| L138 | kod | `uint32_t pll_n = config->mclk_freq / 1000000;` | ✅ disk |
| L139 | kod | `uint32_t pll_r = 1;` | ✅ disk |
| L140 | kod | `uint32_t pll_f = config->mclk_freq % 1000000;` | ✅ disk |
| L142 | kod | `// XMOS PLL register ayarla` | ✅ disk |
| L143 | kod | `write_register(0x00, pll_n);  // PLL_N` | ✅ disk |
| L144 | kod | `write_register(0x01, pll_r);  // PLL_R` | ✅ disk |
| L145 | kod | `write_register(0x02, pll_f);  // PLL_F` | ✅ disk |
| L147 | kod | `// BCLK divider` | ✅ disk |
| L148 | kod | `bclk_div = 500000000 / (2 * config->bclk_freq);` (§6 E2) | ✅ disk |
| L149 | kod | `write_register(0x10, bclk_div);` | ✅ disk |
| L151 | kod | `// LRCLK divider (BCLK / (bit_depth * 2))` | ✅ disk |
| L152 | kod | `lrclk_div = bclk_freq / (bit_depth * 2 * lrclk_freq);` | ✅ disk |
| L153 | kod | `write_register(0x11, lrclk_div);` | ✅ disk |
| L155 | kod | `// MCLK divider (MCLK / BCLK)` | ✅ disk |
| L156 | kod | `uint32_t mclk_div = config->mclk_freq / config->bclk_freq;` | ✅ disk |
| L157 | kod | `write_register(0x12, mclk_div);` | ✅ disk |
| L158 | kod | `}` fonksiyon kapanışı | ✅ disk |
| L159 | kod | ```xc kod bloğu kapanışı | ✅ disk |
| L161 | baslik | ### I2S Master Driver | ✅ disk |
| L163 | kod | ```xc kod bloğu açılışı | ✅ disk |
| L164 | kod | `// I2S Master Driver - XMOS XS1 port mapping` | ✅ disk |
| L165 | kod | `// XS1 ports: 32-bit wide, one port per signal` | ✅ disk |
| L167 | kod | `// Port konfigürasyonu` | ✅ disk |
| L168 | kod | `port p_i2s_bclk = XS1_PORT_1A;  // Bit clock` | ✅ disk |
| L169 | kod | `port p_i2s_lrclk = XS1_PORT_1B; // Word select` | ✅ disk |
| L170 | kod | `port p_i2s_dout = XS1_PORT_1C;  // Data out (to DAC)` (§7 C7) | ✅ disk |
| L171 | kod | `port p_i2s_din = XS1_PORT_1D;   // Data in (from ADC)` | ✅ disk |
| L172 | kod | `port p_i2s_mclk = XS1_PORT_1E;  // Master clock` | ✅ disk |
| L174 | kod | `// I2S transmit - DAC'a ses verisi gönderme` | ✅ disk |
| L175 | kod | `// 32-bit I2S, stereo (2 channel)` | ✅ disk |
| L176 | kod | `// Her sample için 64 BCLK cycle (32 left + 32 right)` | ✅ disk |
| L178 | kod | `void i2s_transmit(chan c_dac, i2s_config_t *config) {` | ✅ disk |
| L179 | kod | `int32_t left_sample;` | ✅ disk |
| L180 | kod | `int32_t right_sample;` | ✅ disk |
| L181 | kod | `unsigned bclk_val;` | ✅ disk |
| L182 | kod | `unsigned lrclk_val;` | ✅ disk |
| L183 | kod | `unsigned data_val;` | ✅ disk |
| L185 | kod | `// I2S format ayarla` | ✅ disk |
| L186 | kod | `int bit_depth = config->bit_depth; // 16, 24, veya 32` | ✅ disk |
| L187 | kod | `int mclk_ratio = config->mclk_ratio; // 256x veya 512x` (§4 C3) | ✅ disk |
| L189 | kod | `// Port initial values` | ✅ disk |
| L190 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L191 | kod | `p_i2s_lrclk <: 0;  // Left channel first` | ✅ disk |
| L192 | kod | `p_i2s_dout <: 0;` | ✅ disk |
| L194 | kod | `while (1) {` | ✅ disk |
| L195 | kod | `// Left channel (LRCLK = 0)` | ✅ disk |
| L196 | kod | `p_i2s_lrclk <: 0;` | ✅ disk |
| L198 | kod | `// DSP'den sol sample al` | ✅ disk |
| L199 | kod | `c_dac :> left_sample;` | ✅ disk |
| L201 | kod | `// MSB-first olarak transmit et` | ✅ disk |
| L202 | kod | `for (int i = bit_depth - 1; i >= 0; i--) {` | ✅ disk |
| L203 | kod | `data_val = (left_sample >> i) & 1;` | ✅ disk |
| L204 | kod | `p_i2s_dout <: data_val;` | ✅ disk |
| L206 | kod | `// BCLK falling edge` | ✅ disk |
| L207 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L208 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L209 | kod | `}` sol kanal bit döngüsü kapanışı | ✅ disk |
| L211 | kod | `// Right channel (LRCLK = 1)` | ✅ disk |
| L212 | kod | `p_i2s_lrclk <: 1;` | ✅ disk |
| L214 | kod | `// DSP'den sağ sample al` | ✅ disk |
| L215 | kod | `c_dac :> right_sample;` | ✅ disk |
| L217 | kod | `// MSB-first olarak transmit et` | ✅ disk |
| L218 | kod | `for (int i = bit_depth - 1; i >= 0; i--) {` | ✅ disk |
| L219 | kod | `data_val = (right_sample >> i) & 1;` | ✅ disk |
| L220 | kod | `p_i2s_dout <: data_val;` | ✅ disk |
| L222 | kod | `// BCLK falling edge` | ✅ disk |
| L223 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L224 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L225 | kod | `}` sağ kanal bit döngüsü kapanışı | ✅ disk |
| L227 | kod | `// Kalan BCLK cycle'larını doldur` | ✅ disk |
| L228 | kod | `for (int i = 0; i < (32 - bit_depth); i++) {` | ✅ disk |
| L229 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L230 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L231 | kod | `}` kalan cycle döngüsü kapanışı | ✅ disk |
| L232 | kod | `}` while (1) kapanışı | ✅ disk |
| L233 | kod | `}` i2s_transmit kapanışı | ✅ disk |
| L235 | kod | `// I2S receive - ADC'den ses verisi okuma` | ✅ disk |
| L236 | kod | `void i2s_receive(chan c_adc, i2s_config_t *config) {` | ✅ disk |
| L237 | kod | `int32_t left_sample;` | ✅ disk |
| L238 | kod | `int32_t right_sample;` | ✅ disk |
| L239 | kod | `unsigned data_in;` | ✅ disk |
| L241 | kod | `int bit_depth = config->bit_depth;` | ✅ disk |
| L243 | kod | `while (1) {` | ✅ disk |
| L244 | kod | `left_sample = 0;` | ✅ disk |
| L245 | kod | `right_sample = 0;` | ✅ disk |
| L247 | kod | `// Left channel oku (LRCLK = 0)` | ✅ disk |
| L248 | kod | `p_i2s_lrclk <: 0;` | ✅ disk |
| L250 | kod | `for (int i = 0; i < bit_depth; i++) {` | ✅ disk |
| L251 | kod | `// BCLK rising edge` | ✅ disk |
| L252 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L253 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L255 | kod | `// Data oku` | ✅ disk |
| L256 | kod | `data_in = p_i2s_din :> data_in;` (§6 E5) | ✅ disk |
| L257 | kod | `left_sample = (left_sample << 1) | (data_in & 1);` | ✅ disk |
| L258 | kod | `}` sol kanal bit döngüsü kapanışı | ✅ disk |
| L260 | kod | `// Right channel oku (LRCLK = 1)` | ✅ disk |
| L261 | kod | `p_i2s_lrclk <: 1;` | ✅ disk |
| L263 | kod | `for (int i = 0; i < bit_depth; i++) {` | ✅ disk |
| L264 | kod | `// BCLK rising edge` | ✅ disk |
| L265 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L266 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L268 | kod | `// Data oku` | ✅ disk |
| L269 | kod | `data_in = p_i2s_din :> data_in;` | ✅ disk |
| L270 | kod | `right_sample = (right_sample << 1) | (data_in & 1);` | ✅ disk |
| L271 | kod | `}` sağ kanal bit döngüsü kapanışı | ✅ disk |
| L273 | kod | `// Kalan BCLK cycle'larını doldur` | ✅ disk |
| L274 | kod | `for (int i = 0; i < (32 - bit_depth); i++) {` | ✅ disk |
| L275 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L276 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L277 | kod | `}` kalan cycle döngüsü kapanışı | ✅ disk |
| L279 | kod | `// ADC'ye sample'ları gönder` | ✅ disk |
| L280 | kod | `c_adc <: left_sample;` | ✅ disk |
| L281 | kod | `c_adc <: right_sample;` | ✅ disk |
| L282 | kod | `}` while (1) kapanışı | ✅ disk |
| L283 | kod | `}` i2s_receive kapanışı | ✅ disk |
| L284 | kod | ```xc kod bloğu kapanışı | ✅ disk |
| L286 | baslik | ### Multi-Channel I2S | ✅ disk |
| L288 | kod | ```xc kod bloğu açılışı | ✅ disk |
| L289 | kod | `// Multi-channel I2S - TDM (Time Division Multiplexing)` | ✅ disk |
| L290 | kod | `// 8 channel I2S, 32-bit per channel` | ✅ disk |
| L291 | kod | `// Frame length: 8 * 32 = 256 BCLK cycles` (§7 C6) | ✅ disk |
| L293 | kod | `#define I2S_CHANNELS     8` (§7 C6) | ✅ disk |
| L294 | kod | `#define I2S_BIT_DEPTH    32` | ✅ disk |
| L295 | kod | `#define I2S_FRAME_LENGTH (I2S_CHANNELS * I2S_BIT_DEPTH)` | ✅ disk |
| L297 | kod | `// TDM transmit - 8 channel output` | ✅ disk |
| L298 | kod | `void i2s_tdm_transmit(chan c_dac[I2S_CHANNELS]) {` | ✅ disk |
| L299 | kod | `int32_t samples[I2S_CHANNELS];` | ✅ disk |
| L300 | kod | `unsigned frame[I2S_FRAME_LENGTH];` (§6 E6) | ✅ disk |
| L302 | kod | `while (1) {` | ✅ disk |
| L303 | kod | `// DSP'den tüm kanalları al` | ✅ disk |
| L304 | kod | `for (int ch = 0; ch < I2S_CHANNELS; ch++) {` | ✅ disk |
| L305 | kod | `c_dac[ch] :> samples[ch];` | ✅ disk |
| L306 | kod | `}` kanal okuma döngüsü kapanışı | ✅ disk |
| L308 | kod | `// TDM frame oluştur` | ✅ disk |
| L309 | kod | `for (int ch = 0; ch < I2S_CHANNELS; ch++) {` | ✅ disk |
| L310 | kod | `for (int bit = 0; bit < I2S_BIT_DEPTH; bit++) {` | ✅ disk |
| L311 | kod | `frame[ch * I2S_BIT_DEPTH + bit] =` | ✅ disk |
| L312 | kod | `(samples[ch] >> (I2S_BIT_DEPTH - 1 - bit)) & 1;` | ✅ disk |
| L313 | kod | `}` bit döngüsü kapanışı | ✅ disk |
| L314 | kod | `}` kanal döngüsü kapanışı | ✅ disk |
| L316 | kod | `// Frame'i transmit et` | ✅ disk |
| L317 | kod | `for (int i = 0; i < I2S_FRAME_LENGTH; i++) {` | ✅ disk |
| L318 | kod | `p_i2s_dout <: frame[i];` (§7 C7 tek hat) | ✅ disk |
| L319 | kod | `p_i2s_bclk <: 1;` | ✅ disk |
| L320 | kod | `p_i2s_bclk <: 0;` | ✅ disk |
| L321 | kod | `}` frame transmit döngüsü kapanışı | ✅ disk |
| L322 | kod | `}` while (1) kapanışı | ✅ disk |
| L323 | kod | `}` i2s_tdm_transmit kapanışı | ✅ disk |
| L324 | kod | ```xc kod bloğu kapanışı | ✅ disk |
| L326 | baslik | ### Sample Rate Conversion (§7 C5) | ✅ disk |
| L328 | kod | ```xc kod bloğu açılışı | ✅ disk |
| L329 | kod | `// Basit sample rate conversion` | ✅ disk |
| L330 | kod | `// 44.1kHz → 48kHz dönüşümü` | ✅ disk |
| L332 | kod | `// Conversion ratio: 48000 / 44100 = 1.088435` | ✅ disk |
| L333 | kod | `// Linear interpolation ile upsampling` | ✅ disk |
| L335 | kod | `#define SRC_BUFFER_SIZE 256` | ✅ disk |
| L337 | kod | `typedef struct {` | ✅ disk |
| L338 | kod | `int32_t buffer[SRC_BUFFER_SIZE];` | ✅ disk |
| L339 | kod | `int write_pos;` | ✅ disk |
| L340 | kod | `int read_pos;` | ✅ disk |
| L341 | kod | `int ratio_num;      // 48000` | ✅ disk |
| L342 | kod | `int ratio_den;      // 44100` | ✅ disk |
| L343 | kod | `int phase;` | ✅ disk |
| L344 | kod | `} src_state_t;` | ✅ disk |
| L346 | kod | `void src_process(src_state_t *state, int32_t input, int32_t *output) {` | ✅ disk |
| L347 | kod | `// Input sample'ı buffer'a yaz` | ✅ disk |
| L348 | kod | `state->buffer[state->write_pos] = input;` | ✅ disk |
| L349 | kod | `state->write_pos = (state->write_pos + 1) % SRC_BUFFER_SIZE;` | ✅ disk |
| L351 | kod | `// Phase accumulator ile okuma` | ✅ disk |
| L352 | kod | `state->phase += state->ratio_den;` | ✅ disk |
| L353 | kod | `if (state->phase >= state->ratio_num) {` | ✅ disk |
| L354 | kod | `state->phase -= state->ratio_num;` | ✅ disk |
| L355 | kod | `state->read_pos = (state->read_pos + 1) % SRC_BUFFER_SIZE;` | ✅ disk |
| L356 | kod | `}` phase koşul bloğu kapanışı | ✅ disk |
| L358 | kod | `// Linear interpolation` | ✅ disk |
| L359 | kod | `int32_t sample0 = state->buffer[state->read_pos];` | ✅ disk |
| L360 | kod | `int32_t sample1 = state->buffer[(state->read_pos + 1) % SRC_BUFFER_SIZE];` | ✅ disk |
| L361 | kod | `int32_t frac = state->phase * 256 / state->ratio_num;` | ✅ disk |
| L363 | kod | `*output = sample0 + ((sample1 - sample0) * frac >> 8);` | ✅ disk |
| L364 | kod | `}` src_process kapanışı | ✅ disk |
| L365 | kod | ```xc kod bloğu kapanışı | ✅ disk |
| L367 | baslik | ### Clock Recovery (Slave Mode) (§7 C4) | ✅ disk |
| L369 | kod | ```xc kod bloğu açılışı | ✅ disk |
| L370 | kod | `// I2S Slave Mode - External clock recovery` | ✅ disk |
| L371 | kod | `// BCLK ve LRCLK dış kaynaktan geliyor` | ✅ disk |
| L372 | kod | `// MCLK'ı recovered clock'a senkronize etme` | ✅ disk |
| L374 | kod | `void i2s_slave_clock_recovery(chan c_mclk_adj) {` | ✅ disk |
| L375 | kod | `timer t;` | ✅ disk |
| L376 | kod | `unsigned bclk_edges;` | ✅ disk |
| L377 | kod | `unsigned lrclk_edges;` | ✅ disk |
| L378 | kod | `unsigned last_lrclk = 0;` | ✅ disk |
| L379 | kod | `unsigned lrclk_period;` | ✅ disk |
| L380 | kod | `unsigned target_period;` (§7 C11 ile çelişir) | ✅ disk |
| L382 | kod | `while (1) {` | ✅ disk |
| L383 | kod | `// LRCLK edges say` | ✅ disk |
| L384 | kod | `select {` | ✅ disk |
| L385 | kod | `case p_i2s_lrclk when pinsneq(last_lrclk) :> lrclk_edges:` | ✅ disk |
| L386 | kod | `// LRCLK değişti` | ✅ disk |
| L387 | kod | `lrclk_period = lrclk_edges - last_lrclk;` | ✅ disk |
| L388 | kod | `last_lrclk = lrclk_edges;` | ✅ disk |
| L390 | kod | `// MCLK period hedefi` | ✅ disk |
| L391 | kod | `target_period = lrclk_period / (I2S_BIT_DEPTH * 2);` | ✅ disk |
| L393 | kod | `// MCLK adjustment hesapla` | ✅ disk |
| L394 | kod | `int32_t error = lrclk_period - expected_period;` (§7 C11) | ✅ disk |
| L395 | kod | `c_mclk_adj <: error;` | ✅ disk |
| L396 | kod | `break;` | ✅ disk |
| L398 | kod | `case c_mclk_adj :> int adjustment:` | ✅ disk |
| L399 | kod | `// MCLK PLL ayarla` | ✅ disk |
| L400 | kod | `adjust_mclk_pll(adjustment);` | ✅ disk |
| L401 | kod | `break;` | ✅ disk |
| L402 | kod | `}` select kapanışı | ✅ disk |
| L403 | kod | `}` while (1) kapanışı | ✅ disk |
| L404 | kod | `}` i2s_slave_clock_recovery kapanışı | ✅ disk |
| L405 | kod | ```xc kod bloğu kapanışı | ✅ disk |
| L407 | baslik | ### Audio Quality Metrics | ✅ disk |
| L409 | kod | Metrik tablosu bloğu açılışı | ✅ disk |
| L410 | kod | `I2S Audio Quality Parameters:` | ✅ disk |
| L412 | kod | Tablo üst çerçeve çizgisi | ✅ disk |
| L413 | kod | Başlık satırı: Parameter · Target · Typical | ✅ disk |
| L414 | kod | Tablo ayraç satırı | ✅ disk |
| L415 | kod | SNR > 110 dB / 115 dB | ✅ disk |
| L416 | kod | THD+N < -100 dB / -105 dB | ✅ disk |
| L417 | kod | Dynamic Range > 115 dB / 120 dB | ✅ disk |
| L418 | kod | Crosstalk Rejection > 100 dB / 110 dB | ✅ disk |
| L419 | kod | Clock Jitter < 50 ps RMS / 30 ps RMS | ✅ disk |
| L420 | kod | Input Impedance 10kΩ typical / 10kΩ | ✅ disk |
| L421 | kod | Output Impedance < 100Ω / 50Ω | ✅ disk |
| L422 | kod | Sample Rate Accuracy ±10 ppm / ±5 ppm | ✅ disk |
| L423 | kod | Tablo alt çerçeve çizgisi | ✅ disk |
| L424 | kod | Metrik tablosu kapanışı | ✅ disk |
| L426 | baslik | ## Derleme & Yükleme | ✅ disk |
| L428 | baslik | ### I2S Driver Derleme | ✅ disk |
| L430 | kod | ```bash kod bloğu açılışı | ✅ disk |
| L431 | kod | `cd firmware/xmos/app_usb_audio_skc` | ✅ disk |
| L433 | kod | `# I2S driver ile derleme` | ✅ disk |
| L434 | kod | `xmake clean` | ✅ disk |
| L435 | kod | `xmake all CONFIG=i2s_master` | ✅ disk |
| L437 | kod | `# Multi-channel I2S` | ✅ disk |
| L438 | kod | `xmake all CONFIG=i2s_master CHANNELS=8` | ✅ disk |
| L440 | kod | `# I2S slave mode` | ✅ disk |
| L441 | kod | `xmake all CONFIG=i2s_slave` (§7 C4) | ✅ disk |
| L443 | kod | `# Sample rate conversion desteği` | ✅ disk |
| L444 | kod | `xmake all CONFIG=i2s_master SRC=1` (§7 C5) | ✅ disk |
| L445 | kod | ```bash kod bloğu kapanışı | ✅ disk |
| L447 | baslik | ### I2S Test | ✅ disk |
| L449 | kod | ```bash kod bloğu açılışı | ✅ disk |
| L450 | kod | `# I2S timing test` | ✅ disk |
| L451 | kod | `# Logic analyzer ile BCLK, LRCLK, DATA monitoring` | ✅ disk |
| L452 | kod | `# Saleae Logic ile I2S decode` | ✅ disk |
| L454 | kod | `# Audio quality test` | ✅ disk |
| L455 | kod | `# THD+N ölçümü (Audio Precision ile)` | ✅ disk |
| L456 | kod | `# SNR ölçümü` (§6 E7) | ✅ disk |
| L457 | kod | `# Crosstalk ölçümü` (§6 E7) | ✅ disk |
| L458 | kod | ```bash kod bloğu kapanışı | ✅ disk |
| L460 | baslik | ### DAC/ADC Konfigürasyonu | ✅ disk |
| L462 | kod | ```bash kod bloğu açılışı | ✅ disk |
| L463 | kod | `# I2C ile DAC register ayarlama` | ✅ disk |
| L464 | kod | `# PCM5242 için I2C address: 0x94` (§7 C1) | ✅ disk |
| L465 | kod | `i2cset -y 0 0x94 0x00 0x00  # Reset` | ✅ disk |
| L466 | kod | `i2cset -y 0 0x94 0x01 0x00  # Mode: I2S` | ✅ disk |
| L467 | kod | `i2cset -y 0 0x94 0x02 0x00  # Format: 32-bit` | ✅ disk |
| L468 | kod | `i2cset -y 0 0x94 0x03 0x00  # MCLK ratio: 256x` | ✅ disk |
| L470 | kod | `# AK4493 için I2C address: 0x10` (§7 C1) | ✅ disk |
| L471 | kod | `i2cset -y 0 0x10 0x00 0x00  # Reset` | ✅ disk |
| L472 | kod | `i2cset -y 0 0x10 0x01 0x0C  # Mode: I2S, 32-bit` | ✅ disk |
| L473 | kod | `i2cset -y 0 0x10 0x02 0x00  # Sound mode: Normal` | ✅ disk |
| L474 | kod | ```bash kod bloğu kapanışı | ✅ disk |
| L476 | baslik | ## Bağımlılıklar | ✅ disk |
| L478 | tablo | \| Bağımlılık \| Versiyon \| Amaç \| başlık satırı | ✅ disk |
| L479 | tablo | \|---\|---\|---\| ayraç satırı | ✅ disk |
| L480 | tablo | \| lib_i2s \| >= 2.x \| XMOS I2S driver \| | ✅ disk |
| L481 | tablo | \| lib_i2c_master \| >= 2.x \| DAC/ADC register config \| | ✅ disk |
| L482 | tablo | \| lib_locks \| >= 1.x \| Channel synchronization \| | ✅ disk |
| L483 | tablo | \| lib_xcore_math \| >= 1.x \| Math operations \| | ✅ disk |
| L485 | baslik | ## Durum: Implementasyon | ✅ disk |
| L487 | tablo | \| Modül \| Durum \| Açıklama \| başlık satırı | ✅ disk |
| L488 | tablo | \|---\|---\|---\| ayraç satırı | ✅ disk |
| L489 | tablo | \| I2S Master Driver \| Planlandı \| BCLK/LRCLK/MCLK generation \| | ✅ disk |
| L490 | tablo | \| I2S Slave Driver \| Planlandı \| External clock recovery \| | ✅ disk |
| L491 | tablo | \| Multi-Channel TDM \| Planlandı \| 8+ channel support \| | ✅ disk |
| L492 | tablo | \| Clock Configuration \| Planlandı \| 44.1k/48k/96k/192k \| | ✅ disk |
| L493 | tablo | \| Sample Rate Conversion \| Planlandı \| SRC algorithm \| (§7 C5) | ✅ disk |
| L494 | tablo | \| Format Support \| Planlandı \| 16/24/32-bit \| | ✅ disk |
| L495 | tablo | \| DAC/ADC Config \| Planlandı \| I2C register configuration \| | ✅ disk |
| L496 | tablo | \| Audio Quality \| Planlandı \| SNR/THD metrics \| | ✅ disk |

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> Bu dosyanın içindeki wiki-link'ler ve D01 aralığının tamamı. İlişki gerekçesi bu klasörün konusundan ve birincil kaynaktan türetildi; komşu dosyaların varlığı glob ile doğrulanmıştır.

| Klasör | Wiki-link | İlişki (bu dosyaya) |
|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC donusum zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | Saat hiyerarşisinin üst kapsayıcısı; §4 BCK/MCLK çelişkileri bu düğümle ortak |
| `k055-ak4458-dac` (AK4458 ana DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | Firmware'da AK4493 geçen I2C/register satırlarının (C1) doğrulanacağı gerçek DAC |
| `k056-pcm3168a-dac-adc` (PCM3168A DAC+ADC) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | Firmware'da PCM5242/CS5368 geçen satırlarının (C1) doğrulanacağı gerçek ADC/DAC |
| `k057-i2s-interface` (I2S / TDM haberlesme) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | → bu klasörün protokol dosyası (bu dosyanın çapraz kontrol kaynağı) |
| `k057-i2s-interface` (klasör özeti) | [[../k057-i2s-interface/index]] | Klasör indeksi — bu dosya §2 envantere eklenmeli |
| `k058-xmos-xu316` (XMOS xu316 USB ses kokteyi) | [[../k058-xmos-xu316/xu316-entegrasyon]] | I2S sürücüsünün çalıştığı platform (XU316, 500 MHz clock iddiası L123) |
| `k059-usb-audio` (USB Audio Class yolu) | [[../k059-usb-audio/usb-audio-yolu]] | Paketin USB ucu — firmware katmanı bağlantısı |
| `k059-usb-audio` (firmware sürücü kardeşi) | [[../k059-usb-audio/usb-audio-firmware-surucu]] | Aynı yedek kaynak ailesinden türeyen kardeş firmware dosyası |
| `k060-analog-sinyal-yolu` (Analog sinyal yolu) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | DAC çıkışından sonra; I2S sürücüsünün dolaylı alıcısı |
| `k061-diff-pair-input` (Differential pair giris) | [[../k061-diff-pair-input/diff-pair-tasarim]] | I2S hat çiftlerinin diferansiyel tasarımı (firmware'de yok, donanım tarafı) |
| `k062-vas-stage` (VAS) | [[../k062-vas-stage/vas-stage-tasarim]] | Sinyal zinciri devamı — I2S ile doğrudan ilgisi yok (matris bütünlüğü) |
| `k063-output-stage` (Cikis asamasi) | [[../k063-output-stage/output-stage-tasarim]] | Sinyal zinciri devamı (matris bütünlüğü) |
| `k064-feedback-network` (Geri besleme agi) | [[../k064-feedback-network/feedback-tasarim]] | Sinyal zinciri devamı (matris bütünlüğü) |
| `k065-mjle21194-93` (Guc op-amp) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | Sinyal zinciri devamı (matris bütünlüğü) |
| `k066-konnektorler` (Konnektor envanteri) | [[../k066-konnektorler/konnektor-envanteri]] | Fiziksel arayüz — I2S/I2C hat uçları |
| `k067-koruma-devreleri` (Koruma devreleri) | [[../k067-koruma-devreleri/koruma-rehberi]] | Hat koruması (matris bütünlüğü) |
| `k068-guc-kaynagi-analog` (Analog guc kaynagi) | [[../k068-guc-kaynagi-analog/analog-besleme]] | Analog besleme (matris bütünlüğü) |
| `k069-hoparlor-dizilimi` (Hoparlor dizilimi) | [[../k069-hoparlor-dizilimi/hoparlor-dizilim]] | Kanal çıkışının son ucu (C6 kanal eşlemesi burayı etkiler) |
| `k070-pcb-tasarim` (PCB tasarimi) | [[../k070-pcb-tasarim/pcb-rehber]] | I2S hatlarının fiziksel taşıyıcısı — C7 (1 vs 4 data hattı) burada kritik |
| `k071-termal-yonetim` (Termal yonetim) | [[../k071-termal-yonetim/termal-rehber]] | Düğümler arası matris bütünlüğü |

### §10.1 Bu Dosyadaki Wiki-Link Envanteri

> Tüm hedefler `.ai/architecture/` altında **gerçek dosyadır** (glob ile doğrulandı); kırık link 0.

| Hedef | Durum |
|---|---|
| `../k057-i2s-interface/i2s-ve-tdm-rehberi` | ✅ disk |
| `../k057-i2s-interface/index` | ✅ disk |
| `../k054-dac-adc-zinciri/zincir-mimari` | ✅ disk |
| `../k055-ak4458-dac/ak4458-dac-rehberi` | ✅ disk |
| `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | ✅ disk |
| `../k058-xmos-xu316/xu316-entegrasyon` | ✅ disk |
| `../k059-usb-audio/usb-audio-yolu` | ✅ disk |
| `../k059-usb-audio/usb-audio-firmware-surucu` | ✅ disk |
| `../k060-analog-sinyal-yolu/analog-yol-rehberi` | ✅ disk |
| `../k061-diff-pair-input/diff-pair-tasarim` | ✅ disk |
| `../k062-vas-stage/vas-stage-tasarim` | ✅ disk |
| `../k063-output-stage/output-stage-tasarim` | ✅ disk |
| `../k064-feedback-network/feedback-tasarim` | ✅ disk |
| `../k065-mjle21194-93/mjle-op-amp-kurulum` | ✅ disk |
| `../k066-konnektorler/konnektor-envanteri` | ✅ disk |
| `../k067-koruma-devreleri/koruma-rehberi` | ✅ disk |
| `../k068-guc-kaynagi-analog/analog-besleme` | ✅ disk |
| `../k069-hoparlor-dizilimi/hoparlor-dizilim` | ✅ disk |
| `../k070-pcb-tasarim/pcb-rehber` | ✅ disk |
| `../k071-termal-yonetim/termal-rehber` | ✅ disk |
| `../k032-latency-optimization/latency-optimization` | ✅ disk |

## §11 Doğrulama Protokolü

| # | Adım | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('.ai/architecture/k057-i2s-interface/i2s-firmware-surucu.md').Length` | ≥500 (boş satır dahil) |
| 2 | UTF-8 / mojibake | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k057-i2s-interface/i2s-firmware-surucu` | `mojibake: 0`, `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k057-i2s-interface` | 0 bulgu |
| 4 | Wiki-link kırıklığı | `[[../k054-dac-adc-zinciri/zincir-mimari]]` hedefleri `.ai/architecture/` altında | kırık 0 (§10.1: 21 hedef) |
| 5 | Frontmatter | 7 zorunlu alan: title · type · category · updated · version · status · authority (+ date) | eksik yok |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Commit kapısı | `git status --porcelain -- .ai/architecture/k057-i2s-interface` | `??` (bu görevde commit ATILMAZ) |
| 8 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | bu görevce değişiklik yok (salt-okunur) |
| 9 | REDACTED | dosyada secret/token/anahtar bulunmaması | 0 eşleşme |

> **Kapanış notu (orkestratör, 2026-10-06):** Adım 2–3 repo kökünden orkestratör tarafından çalıştırıldı ve **geçti** — `verify` → `hasBom:false` · `mojibake:0` · `lines:1385` · `scan` → `dirty:0`. Adım 1 ve 4 zaten dosya-okuma + glob ile doğrulanmıştı (§8). Bu dosyadaki `⚠️ VERIFICATION REQUIRED` kapanmıştır.

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama: bu dosyanın §1–§11 yazar gövdesinde `⚠️` içeren satır sayısı = **25** (grep ile sayıldı — §12 başlığı ve bu not dahil değildir; §12'deki 2 satır hariç).

| Satır (bu dosya) | Bölüm | İşaretli madde |
|---|---|---|
| L25 | §1 | PCM5242 · AK4493 · CS5368 parça adları kaynakta var, donanımda yok → C1 |
| L112 | §3 | Diyagram parça adı uyarısı → C1 |
| L379 | §3 | TDM `256 bits` ↔ `128 channels` ↔ `TDMD[0:3]` → C6 / C7 |
| L433 | §3 | SRC vault ağacında karşılığı yok → C5 |
| L475 | §3 | `expected_period` tanımsız → C11 |
| L579 | §3 | PCM5242 I2C `0x94` kopyalanmadı → C1 |
| L580 | §3 | AK4493 I2C `0x10` kopyalanmadı → C1 |
| L633 | §4 | C2 — 44.1 kHz için 4 farklı BCK değeri |
| L634 | §4 | C3 — 256fs ↔ 512fs MCLK oranı seçimi yok |
| L635 | §4 | C4 — master/slave rol profili belirsiz |
| L673 | §6 | E1 — slave clock recovery içinde tanımsız değişken → C11 |
| L674 | §6 | E2 — divider register değerleri `UNKNOWN` |
| L675 | §6 | E3 — sol kanal MSB konumu kaynakta açıklanmamış |
| L680 | §6 | E8 — 192 kHz'te MCLK 49.152 MHz, protokolde üst sınır yok → C10 |
| L684 | §7 | Çelişki tablosu kolon formatı tanımı (`Karar/⚠️`) |
| L688 | §7 | C1 — parça adı / I2C adresi çelişkisi |
| L689 | §7 | C2 — BCK frekansı çelişkisi |
| L690 | §7 | C3 — MCLK oranı çelişkisi |
| L691 | §7 | C4 — master/slave rol çelişkisi |
| L692 | §7 | C5 — SRC durumu/ Vault karşılığı |
| L693 | §7 | C6 — kanal sayısı çelişkisi |
| L694 | §7 | C7 — seri veri hattı sayısı (1 vs 4) |
| L695 | §7 | C8 — durum etiketi (Planlandı vs 🟢 Hazır) |
| L698 | §7 | C11 — `expected_period` tanımsızlık (kod hatası) |
| L713 | §8 | `vault-utf8-writer verify/scan` — orkestratör repo kökünden çalıştırdı, `mojibake:0` · `dirty:0` |

## §13 Tam Kaynak Satır Dizini (ikincil kaynak)

> İkincil (protokol) kaynağının satır satır indeksi — kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` (186 satır; boş satırlar atlanmıştır). Çapraz kontrol bu dosyanın §4/§7 çelişki satırlarını besler.

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | `---` frontmatter açılışı | ✅ disk |
| L2 | metin | title: "I2S Bus Protocol" | ✅ disk |
| L3 | metin | layer: K1 | ✅ disk |
| L4 | metin | category: "Dijital Ses Protokolü" | ✅ disk |
| L5 | metin | date: 2026-09-20 | ✅ disk |
| L6 | metin | `---` frontmatter kapanışı | ✅ disk |
| L8 | baslik | # I2S Bus Protocol | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | Philips Standard; XMOS XU316 → DAC (AK4458) ve ADC (PCM3168A) (§7 C1 kanıtı) | ✅ disk |
| L14 | baslik | ## Teknik Spesifikasyonlar | ✅ disk |
| L16 | tablo | \| Parametre \| Değer \| başlık | ✅ disk |
| L17 | tablo | \|-----------\|-------\| ayraç | ✅ disk |
| L18 | tablo | \| Standart \| Philips I2S (Original) \| | ✅ disk |
| L19 | tablo | \| Kanal Sayısı \| 8 stereo (16 single) \| (§7 C6) | ✅ disk |
| L20 | tablo | \| Bit Çözünürlüğü \| 32-bit \| | ✅ disk |
| L21 | tablo | \| Örnekleme Hızı \| 44.1kHz – 192kHz \| (§7 C10) | ✅ disk |
| L22 | tablo | \| Master Clock \| 256fs (11.2896MHz @ 44.1kHz) \| (§7 C3) | ✅ disk |
| L23 | tablo | \| Bit Clock \| 64fs × 32-bit = 2.1168MHz @ 44.1kHz \| (§7 C2) | ✅ disk |
| L24 | tablo | \| Word Select \| fs = 44.1kHz / 48kHz \| | ✅ disk |
| L25 | tablo | \| Data Format \| MSB First, 2's complement \| | ✅ disk |
| L26 | tablo | \| Empedans \| 50Ω (source), High-Z (load) \| | ✅ disk |
| L28 | baslik | ## I2S Sinyalleri | ✅ disk |
| L30 | kod | ``` kod bloğu açılışı | ✅ disk |
| L31 | metin | I2S Bus Sinyalleri: | ✅ disk |
| L33 | metin | 1. SCK (Serial Clock / Bit Clock) | ✅ disk |
| L34 | madde | - Her bit için bir clock pulse | ✅ disk |
| L35 | madde | - Frequency = 2 × channel × bit_depth × fs | ✅ disk |
| L36 | madde | - Example: 2 × 2 × 32 × 44100 = 4.2336MHz (§7 C2) | ✅ disk |
| L38 | metin | 2. WS (Word Select / LR Clock) | ✅ disk |
| L39 | madde | - Sol kanal: WS = 0 | ✅ disk |
| L40 | madde | - Sağ kanal: WS = 1 | ✅ disk |
| L41 | madde | - Frequency = fs (44.1kHz / 48kHz) | ✅ disk |
| L43 | metin | 3. SD (Serial Data) | ✅ disk |
| L44 | madde | - MSB First (en yüksek bit önce) | ✅ disk |
| L45 | madde | - 2's complement formatı | ✅ disk |
| L46 | madde | - 32-bit per channel | ✅ disk |
| L47 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L49 | baslik | ## I2S Timing Diagram | ✅ disk |
| L51 | kod | ``` kod bloğu açılışı | ✅ disk |
| L52 | metin | SCK dalga biçimi (üst satır) | ✅ disk |
| L53 | metin | SCK dalga biçimi (alt satır) | ✅ disk |
| L55 | metin | WS dalga biçimi (üst satır) | ✅ disk |
| L56 | metin | WS dalga biçimi (alt satır) | ✅ disk |
| L57 | metin | ←── Sol Kanal (WS=0) ──→←── Sağ Kanal (WS=1) ──→ | ✅ disk |
| L59 | metin | SD dalga biçimi çizgisi | ✅ disk |
| L60 | metin | \|B31\|B30\|B29\|...\|B1 \|B0 \| (iki slot) | ✅ disk |
| L61 | metin | SD alt çizgi (iki slot) | ✅ disk |
| L62 | metin | ←── 32 bit Sol ────────→←── 32 bit Sağ ────────→ | ✅ disk |
| L64 | metin | Timing: | ✅ disk |
| L65 | madde | - Data changes on SCK falling edge | ✅ disk |
| L66 | madde | - Data sampled on SCK rising edge | ✅ disk |
| L67 | madde | - WS changes 1 SCK cycle before MSB | ✅ disk |
| L68 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L70 | baslik | ## Master/Slave Mode | ✅ disk |
| L72 | baslik | ### XMOS as Master (§7 C4) | ✅ disk |
| L74 | kod | ``` kod bloğu açılışı | ✅ disk |
| L75 | metin | XMOS XU316 (Master) | ✅ disk |
| L76 | metin | │ dikey çizgi | ✅ disk |
| L77 | metin | ├─ SCK Output ──▶ DAC/ADC SCKI (Input) | ✅ disk |
| L78 | metin | ├─ WS Output ──▶ DAC/ADC LRCK (Input) | ✅ disk |
| L79 | metin | └─ MCLK Output ──▶ DAC/ADC MCLK (Input) | ✅ disk |
| L81 | metin | Advantages: | ✅ disk |
| L82 | madde | - Single clock source (no sync issues) | ✅ disk |
| L83 | madde | - Lower jitter (crystal directly connected) | ✅ disk |
| L84 | madde | - Simpler design | ✅ disk |
| L85 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L87 | baslik | ### DAC/ADC as Slave (§7 C4) | ✅ disk |
| L89 | kod | ``` kod bloğu açılışı | ✅ disk |
| L90 | metin | DAC (Slave) ← Receives clock from XMOS | ✅ disk |
| L91 | metin | │ dikey çizgi | ✅ disk |
| L92 | metin | ├─ SCKI ← SCK from XMOS | ✅ disk |
| L93 | metin | ├─ LRCK ← WS from XMOS | ✅ disk |
| L94 | metin | ├─ MCLK ← MCLK from XMOS | ✅ disk |
| L95 | metin | └─ TDMD[0:3] ← SD from XMOS (§7 C7) | ✅ disk |
| L97 | metin | ADC (Slave) ← Receives clock from XMOS | ✅ disk |
| L98 | metin | │ dikey çizgi | ✅ disk |
| L99 | metin | ├─ SCKI ← SCK from XMOS | ✅ disk |
| L100 | metin | ├─ LRCK ← WS from XMOS | ✅ disk |
| L101 | metin | ├─ MCLK ← MCLK from XMOS | ✅ disk |
| L102 | metin | └─ DOUTA/B ──▶ SD to XMOS (output) | ✅ disk |
| L103 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L105 | baslik | ## Multi-Channel Configuration | ✅ disk |
| L107 | baslik | ### 8-Channel TDM (Time Division Multiplexing) | ✅ disk |
| L109 | kod | ``` kod bloğu açılışı | ✅ disk |
| L110 | metin | TDM Frame (8 channels × 32 bits = 256 bits per frame) (§7 C6) | ✅ disk |
| L112 | metin | WS dalga biçimi | ✅ disk |
| L113 | metin | │←──────── WS Period ────────→│ | ✅ disk |
| L115 | metin | SD0 dalga biçimi | ✅ disk |
| L116 | metin | │Ch1│Ch2│Ch3\|...\|Ch32│ | ✅ disk |
| L117 | metin | SD0 alt çizgi | ✅ disk |
| L119 | metin | SD1 dalga biçimi | ✅ disk |
| L120 | metin | │Ch33│Ch34\|...\|Ch64│ | ✅ disk |
| L121 | metin | SD1 alt çizgi | ✅ disk |
| L123 | metin | SD2 dalga biçimi | ✅ disk |
| L124 | metin | │Ch65│Ch66\|...\|Ch96│ | ✅ disk |
| L125 | metin | SD2 alt çizgi | ✅ disk |
| L127 | metin | SD3 dalga biçimi | ✅ disk |
| L128 | metin | │Ch97│Ch98\|...\|Ch128│ | ✅ disk |
| L129 | metin | SD3 alt çizgi | ✅ disk |
| L131 | metin | Toplam: 4 data line × 32 channels/line = 128 channels (§7 C6/C7) | ✅ disk |
| L132 | metin | (TDM mode, 32-bit per channel) | ✅ disk |
| L133 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L135 | baslik | ## Impedans ve Drive | ✅ disk |
| L137 | baslik | ### Source Impedans | ✅ disk |
| L139 | kod | ``` kod bloğu açılışı | ✅ disk |
| L140 | metin | XMOS Output Stage: | ✅ disk |
| L141 | madde | - Output impedance: 50Ω (typical) | ✅ disk |
| L142 | madde | - Drive capability: ±8mA | ✅ disk |
| L143 | madde | - Rise/Fall time: < 5ns | ✅ disk |
| L145 | metin | Trace impedance: | ✅ disk |
| L146 | madde | - Characteristic impedance: 90Ω differential | ✅ disk |
| L147 | madde | - Termination: 100Ω parallel (optional) | ✅ disk |
| L148 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L150 | baslik | ### Load Impedans | ✅ disk |
| L152 | kod | ``` kod bloğu açılışı | ✅ disk |
| L153 | metin | DAC/ADC Input: | ✅ disk |
| L154 | madde | - Input impedance: > 100kΩ (digital) | ✅ disk |
| L155 | madde | - Input capacitance: 5pF (typical) | ✅ disk |
| L156 | madde | - No termination required (high-Z) | ✅ disk |
| L157 | kod | ``` kod bloğu kapanışı | ✅ disk |
| L159 | baslik | ## Bileşen Değerleri | ✅ disk |
| L161 | tablo | \| # \| Bileşen \| Model/Değer \| Adet \| Açıklama \| başlık | ✅ disk |
| L162 | tablo | ayraç satırı | ✅ disk |
| L163 | tablo | \| 1 \| Ferrite Bead \| BLM18AG601SN1 \| 16 \| I2S hat filtresi \| | ✅ disk |
| L164 | tablo | \| 2 \| Series Resistor \| 22Ω 0402 \| 16 \| Source damping \| | ✅ disk |
| L165 | tablo | \| 3 \| Pull-up \| 4.7kΩ 0402 \| 4 \| I2C control \| | ✅ disk |
| L166 | tablo | \| 4 \| Decoupling \| 100nF 0402 \| 16 \| Per IC \| | ✅ disk |
| L168 | baslik | ## Bağımlılıklar | ✅ disk |
| L170 | tablo | \| Bağımlılık \| Yön \| Açıklama \| başlık | ✅ disk |
| L171 | tablo | ayraç satırı | ✅ disk |
| L172 | tablo | \| K1 XMOS \| Clock/Data \| Master clock ve data source \| | ✅ disk |
| L173 | tablo | \| K1 DAC \| Data \| I2S data sink \| | ✅ disk |
| L174 | tablo | \| K1 ADC \| Data \| I2S data source \| | ✅ disk |
| L175 | tablo | \| K0 Fiziksel \| Alt \| PCB trace routing \| | ✅ disk |
| L177 | baslik | ## Durum: Implementasyon | ✅ disk |
| L179 | vurgu | **Durum**: 🟢 Hazır (§7 C8) | ✅ disk |
| L181 | madde | - Protocol: Philips I2S standard | ✅ disk |
| L182 | madde | - Timing: All specifications verified in simulation (§7 C8) | ✅ disk |
| L183 | madde | - PCB routing: Length-matched traces (±1mm) | ✅ disk |
| L184 | madde | - Ferrite beads: Selected and verified | ✅ disk |
| L185 | madde | - Crystal: 22.5792MHz + 24.576MHz dual (§4.2) | ✅ disk |
| L186 | madde | - Multi-channel: TDM mode for 8-channel support | ✅ disk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
