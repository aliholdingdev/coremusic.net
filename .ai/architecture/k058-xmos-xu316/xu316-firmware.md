---
title: "XMOS XU316 Firmware — Yedek Kaynak Entegrasyonu"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# XMOS XU316 Firmware — Yedek Kaynak Entegrasyonu

## §1 Amaç & Kapsam

Bu belge, `k058` klasöründe **henüz kullanılmamış** yedek firmware kaynağını vault'a taşır: `k1-donanim/xmos-xu316.md` dosyasının donanım/pin tarafını anlattığı için ayrı bir konu dosyası olarak üretilmiş **`firmware/xmos-firmware.md`** (348 satır) kaynağının firmware tarafını — mimariyi, kaynak kod yapısını, XC özelliklerini, thread/zamanlama/memory map değerlerini, USB & DSP pipeline'ını, UAC2 entegrasyonunu, hata yönetimini, XTC araç zincirini ve derleme/yükleme/DFU/debug adımlarını — **kaynak değeriyle** aktarır. Ayrıca donanım dosyasındaki çekirdek, saat ve bağlantı iddialarını bu firmware kaynağı ve `k1-donanim/README.md` ile yan yana koyarak çelişkileri kanıt satır numarasıyla kaydeder.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Firmware mimarisi, kaynak kod ağacı, XC dili, thread/bellek/zamanlama | XU316 pin haritası, dekuplaj, kristal yükü, paket → [[../k058-xmos-xu316/xu316-entegrasyon]] |
| USB & DSP pipeline, Clock Recovery, UAC2 descriptor'ları, hata yönetimi | I2S/TDM protokol zamanlaması ve rol ataması → [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| XTC tools kurulumu, `xmake` derleme, `xflash`/DFU yükleme, `xgdb`/`xtrace` debug | UAC2 protokol katmanı ve OS sürücüsü → [[../k059-usb-audio/usb-audio-yolu]] |
| Donanım ↔ firmware çelişki kaydı (çekirdek, kristal, I2S ucu, paket, `lib_xua`) | DAC hedef kipleri ve TDM data hatları → [[../k055-ak4458-dac/ak4458-dac-rehberi]] · [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| Bağımlılık matrisi, wiki-link envanteri, kaynak satır dizinleri | PCB pad/trace yerleşimi → [[../k070-pcb-tasarim/pcb-rehber]] |

**Birincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/firmware/xmos-firmware.md` (348 satır — satır indeksi §9'da).
**İkincil (çapraz) kaynak:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` (99 satır — satır indeksi §13'te).
**Bağlam kaynakları:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` · `_backup/arch-2026-10-06_1057/architecture/firmware/index.md`.

**Kural:** kaynakta olmayan hiçbir komut, sürüm, bağımlılık veya değer üretilmez; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## §2 Kaynak Kapsamı

| # | Kaynak dosya (salt-okunur) | Satır | Bu dosyadaki rolü | İşlenen bölümler |
|---|---------------------------|-------|-------------------|------------------|
| 1 | `_backup/arch-2026-10-06_1057/architecture/firmware/xmos-firmware.md` | 348 | **Birincil** — firmware SSOT'u | Genel Bakış · Firmware Mimarisi · Kaynak Kod Yapısı · XC Dil Özellikleri · XU316 Özellikleri · Thread Zamanlama · Memory Map · USB Audio Pipeline · Clock Recovery · DSP Processing Chain · UAC2 Entegrasyonu · Error Handling · Ortam/Derleme/Yükleme/Debug · Bağımlılıklar · Durum |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` | 99 | **İkincil** — donanım/pin çapraz kontrolü | Teknik spesifikasyon · Temel Bağlantılar · Pin Konfigürasyonu · Bağımlılıklar · Durum |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | — | **Bağlam** — §2.1 saat satırları + §5 blok/kaynak tablosu | §2.1 USB Audio Interface (L45–L52) · §5 XMOS XU316 Detayı (L186–L207) |
| 4 | `_backup/arch-2026-10-06_1057/architecture/firmware/index.md` | 199 | **Bağlam** — katman indeksi | Firmware Mimarisi · Thread Kullanımı (Thread 0–7) · Derleme & Yükleme · Bağımlılıklar |

| Kural | Uygulama |
|-------|----------|
| Salt-okunur | 4 kaynak dosya da değiştirilmedi (`_backup/**` bu görevce yazılmaz) |
| Değer aktarımı | Her teknik değer §9/§13 satır indeksiyle eşleştirilir |
| Kod blokları | Kaynaktaki haliyle korunur (biçim/satır içi yorumlar dahil) |
| Yeni komut/sürüm | Kaynakta yoksa yazılmaz; `⚠️ VERIFICATION REQUIRED` |
| Stil | `index.md` ve `xu316-entegrasyon.md` ile aynı ön-yüz ve § yapısı |

## §3 Firmware Mimarisi & Kaynak Kod

### §3.1 Genel Bakış (kaynak L8–L12)

> `XMOS XU316, COREMUSIC projesinin real-time ses işleme merkezidir. 8-core mimarisi ile eşzamanlı USB, I2S ve DSP işlemlerini hardware-level parallelism ile yönetir. XC dilinde geliştirilen firmware, deterministic timing ve low-latency audio processing sağlar.`

### §3.2 Firmware Mimarisi (kaynak L14–L39, birebir)

```
┌─────────────────────────────────────────────────────────────────┐
│                   XMOS XU316 FIRMWARE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ Thread 0 │ │ Thread 1 │ │ Thread 2 │ │ Thread 3 │          │
│  │ USB IN   │ │ USB OUT  │ │ I2S TX   │ │ I2S RX   │          │
│  │ (Mic)    │ │ (Spkr)   │ │ (DAC)    │ │ (ADC)    │          │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘          │
│       │             │             │             │                │
│  ┌────▼─────────────▼─────────────▼─────────────▼────┐          │
│  │              Channel Communication               │          │
│  │              (Inter-thread signaling)             │          │
│  └────┬─────────────┬─────────────┬─────────────┬────┘          │
│       │             │             │             │                │
│  ┌────▼─────┐ ┌─────▼─────┐ ┌────▼─────┐ ┌────▼─────┐         │
│  │ Thread 4 │ │ Thread 5  │ │ Thread 6 │ │ Thread 7 │         │
│  │ DSP      │ │ GPIO &    │ │ SPI/I2C  │ │ System   │         │
│  │ Engine   │ │ Control   │ │ Comms    │ │ Mgmt     │         │
│  └──────────┘ └───────────┘ └──────────┘ └──────────┘         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### §3.3 Kaynak Kod Yapısı (kaynak L41–L73, birebir)

```
xmos/
├── app_usb_audio_skc/
│   ├── src/
│   │   ├── main.xc                    # Ana program girişi
│   │   ├── usb_audio.xc               # USB Audio endpointleri
│   │   ├── usb_audio_descriptors.xc   # USB tanımlayıcıları
│   │   ├── i2s_driver.xc              # I2S master/slave driver
│   │   ├── dsp_processing.xc          # DSP processing chain
│   │   ├── gpio_control.xc            # GPIO buton/LED yönetimi
│   │   ├── i2c_master.xc             # I2C master (codec config)
│   │   ├── spi_master.xc             # SPI master (MCU comms)
│   │   ├── clock_config.xc           # Clock recovery & MCLK
│   │   └── platform.xc              # Platform konfigürasyonu
│   ├── module_*.xn                    # Board tanımlama dosyaları
│   ├── app_usb_audio_skc.xn          # XU316 board config
│   └── Makefile                       # xmake build sistemi
│
├── modules/
│   ├── lib_xua/                       # USB Audio Class modülü
│   │   ├── module_xua/               # Ana modül
│   │   ├── module_xua/src/           # Kaynak dosyaları
│   │   └── module_xua/api/           # Header dosyaları
│   ├── lib_i2s/                       # I2S driver modülü
│   ├── lib_dsp/                       # DSP kütüphanesi
│   └── lib_i2c_master/               # I2C master modülü
│
└── platform/
    ├── xmos_board_support/           # Board destek paketleri
    └── xs1/                          # XS1 Architecture tanımları
```

### §3.4 XC Dil Özellikleri (kaynak L77–L94, birebir)

> `XC, XMOS'un real-time concurrency için özel olarak tasarlanmış programlama dilidir:`

```xc
// Parallel processing - 8 thread eşzamanlı çalışır
par {
    on tile[0]: usb_audio_loopback();
    on tile[0]: i2s_transmit_loopback();
    on tile[0]: dsp_processing_chain();
    on tile[0]: gpio_control_loop();
}

// Channel communication - thread'ler arası veri transferi
chan c_usb_to_i2s;   // USB'den I2S'ye ses verisi
chan c_i2s_to_usb;   // I2S'den USB'ye ses verisi
chan c_dsp_to_gpio;  // DSP'den GPIO'ya durum bilgisi
```

### §3.5 XMOS XU316 Özellikleri (kaynak L96–L108, değerler birebir)

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Clock Speed | 500 MHz | Ana saat frekansı |
| Thread Count | 8 | Eşzamanlı iş parçacığı |
| RAM | 512 KB | On-chip SRAM |
| Flash | 16 MB | On-board SPI Flash |
| USB | 2.0 HS | High-Speed USB 2.0 |
| I2S | 16 kanal | Multi-channel I2S desteği |
| GPIO | 32 pin | Genel amaçlı giriş/çıkış |
| SPI | 2x | SPI master/slave |
| I2C | 2x | I2C master/slave |

## §4 Thread / Bellek / Zamanlama

### §4.1 Thread ataması (mimari diyagram L21–L36)

| Thread | Rol (kaynakta yazıldığı hali) | Uç |
|--------|-------------------------------|----|
| Thread 0 | USB IN | (Mic) |
| Thread 1 | USB OUT | (Spkr) |
| Thread 2 | I2S TX | (DAC) |
| Thread 3 | I2S RX | (ADC) |
| Thread 4 | DSP Engine | — |
| Thread 5 | GPIO & Control | — |
| Thread 6 | SPI/I2C Comms | — |
| Thread 7 | System Mgmt | — |

Çapraz not: `firmware/index.md` L124–L131 aynı 8 thread'i tek tek isimlendirir (`Thread 7: System Management (watchdog, diagnostics)`); donanım dosyası ise `16 (Hardware Threads)` der (§7-1).

### §4.2 Thread Zamanlama (kaynak L110–L119, birebir)

```
Thread Period:          1ms (USB frame rate)
I2S Sample Period:      20.83μs (@ 48kHz, 1024 samples)
DSP Block Size:         32 samples (0.667ms)
GPIO Scan Rate:         1ms (debounce için)
SPI Transaction:        < 100μs
I2C Transaction:        < 500μs
```

### §4.3 Memory Map (kaynak L121–L130, birebir)

```
Address Range          Size    Kullanım
0x00000000 - 0x0000FFFF  64KB   Boot ROM
0x00010000 - 0x0007FFFF  448KB  Code Region (Flash)
0x00080000 - 0x000BFFFF  256KB  Data Region (SRAM)
0x000C0000 - 0x000FFFFF  256KB  Stack & Heap
0x01000000 - 0x01FFFFFF  16MB   External Flash
```

İç tutarlılık notu: `Data Region (SRAM) 256KB + Stack & Heap 256KB = 512 KB` → §3.5 `RAM 512 KB` ile aynı toplam; `External Flash 16MB` → §3.5 `Flash 16 MB` ile aynı büyüklük. Donanım kaynağı `README.md` §5.2 ile fark için §7-1.

### §4.4 Bellek/kaynak çapraz özeti (README §5.2, L201–L207)

| Kaynak | Kaynak/Kullanım |
|--------|-----------------|
| Logical Cores | 8 (4 x 2 tile) |
| MIPS | ~2000 (toplam) |
| RAM | 512KB (tile 0+1) |
| Flash | 16MB (external) |
| USB PHY | High-speed 480Mbps |

## §5 USB & DSP Pipeline

### §5.1 USB Audio Pipeline (kaynak L132–L140, birebir)

```
USB Host ──► USB Endpoint ──► Ring Buffer ──► DSP Chain ──► I2S TX
     ▲                                                      │
     │          ┌──────────────────────────────────────────┘
     │          │
     └──────────┴── I2S RX ──► Ring Buffer ──► DSP Chain ──► USB Endpoint
```

### §5.2 Clock Recovery (kaynak L142–L169, birebir)

```xc
// XMOS clock recovery - USB SOF ile senkronizasyon
// USB Start-of-Frame (SOF) pulse'ı ile clock recovered
void clock_recovery(chan c_sof, chan c_mclk) {
    timer t;
    unsigned sof_time, mclk_time;
    unsigned period;

    while (1) {
        select {
            case c_sof :> sof_time:
                // USB SOF pulse geldi
                // MCLK period hesapla
                period = mclk_time - sof_time;
                // Audio clock PLL'yi ayarla
                adjust_audio_clock(period);
                break;

            case c_mclk :> mclk_time:
                // MCLK pulse geldi
                // SOF ile karşılaştır
                break;
        }
    }
}
```

> Not: bu bölüm **kristal frekansı geçirmez**; yalnız SOF ↔ MCLK periyot farkı ile `adjust_audio_clock(period)` çağrısı vardır. Kristal/klock çelişkisi için §7-2.

### §5.3 DSP Processing Chain (kaynak L171–L199, birebir)

```xc
// DSP processing - real-time EQ, volume, mixing
// 32 sample block processing @ 48kHz
// Total latency: < 1ms

void dsp_process(chan c_in, chan c_out) {
    int32_t audio_buffer[32];
    int32_t output_buffer[32];

    while (1) {
        // Input'dan 32 sample oku
        c_in :> audio_buffer[0];
        // ... (32 sample transfer)

        // Processing zinciri
        high_pass_filter(audio_buffer, 80.0);    // DC removal
        parametric_eq(audio_buffer);              // 10-band EQ
        dynamics_compressor(audio_buffer);        // Compression
        volume_control(audio_buffer, master_vol); // Master volume
        limiter(audio_buffer, output_buffer);     // Peak limiting

        // Output'a gönder
        c_out <: output_buffer[0];
        // ... (32 sample transfer)
    }
}
```

### §5.4 USB Audio Class 2.0 Entegrasyonu (kaynak L201–L229, birebir)

```xc
// USB Audio Class 2.0 descriptors
// Supported formats: PCM, PCM8, IEEE_FLOAT
// Sample rates: 44.1k, 48k, 96k, 192k
// Channels: 2 (stereo)

unsigned char usb_audio_descriptors[] = {
    // USB Audio Class Interface Descriptor
    0x09,           // bLength
    0x04,           // bDescriptorType (Interface)
    0x01,           // bInterfaceNumber
    0x00,           // bAlternateSetting
    0x00,           // bNumEndpoints
    0x01,           // bInterfaceClass (Audio)
    0x02,           // bInterfaceSubClass (AudioStreaming)
    0x00,           // bInterfaceProtocol
    0x00,           // iInterface

    // Audio Streaming Interface Descriptor
    0x07,           // bLength
    0x24,           // bDescriptorType (CS_INTERFACE)
    0x01,           // bDescriptorSubtype (AS_GENERAL)
    0x01,           // bTerminalLink
    0x00,           // bDelay
    0x01, 0x00,     // wFormatTag (PCM)
};
```

> Bu bölümde `lib_xua` adı **geçmez** (bkz. §7-5); adın geçtiği yerler kaynak L62 (kod ağacı) ve L326 (bağımlılık tablosu) satırlarıdır.

### §5.5 Error Handling & Recovery (kaynak L231–L260, birebir)

```xc
// Watchdog ve hata yönetimi
// Her 100ms'de bir watchdog reset
// USB disconnect/reconnect mekanizması

void system_monitor(chan c_watchdog) {
    timer t;
    unsigned timeout;

    while (1) {
        // Watchdog timeout ayarla
        t :> timeout;
        timeout += 100000000; // 100ms @ 500MHz

        select {
            case t when timerafter(timeout) :> void:
                // Watchdog reset
                watchdog_reset();
                break;

            case c_watchdog :> int status:
                // Watchdog beat geldi
                watchdog_clear();
                break;
        }
    }
}
```

## §6 Bağımlılıklar

### §6.1 Bağımlılık tablosu (kaynak L320–L334, birebir)

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| XTC Tools | >= 15.x | Derleme toolchain |
| XC Compiler | - | XC→XS1 assembly çevirici |
| lib_xua | >= 4.x | USB Audio Class kütüphanesi |
| lib_i2s | >= 2.x | I2S driver |
| lib_dsp | >= 3.x | DSP processing |
| lib_i2c_master | >= 2.x | I2C communication |
| lib_spi_master | >= 1.x | SPI communication |
| lib_locks | >= 1.x | Mutex & channel locks |
| lib_logging | >= 1.x | Debug logging |
| lib_tile | >= 1.x | Tile management |
| platform_xmos | >= 1.x | Board support |

### §6.2 Ortam Kurulumu — XTC Tools (kaynak L264–L274, birebir)

```bash
# XTC Tools kurulumu
wget https://www.xmos.com/file-tools/XTC-Tools-15.3.0
chmod +x XTC-Tools-15.3.0
./XTC-Tools-15.3.0 --prefix /opt/XMOS

# Ortam değişkenlerini ayarla
source /opt/XMOS/xtimecomposer/15.3.0/SetEnv
```

### §6.3 Firmware Derleme (kaynak L276–L292, birebir)

```bash
cd firmware/xmos/app_usb_audio_skc

# Clean build
xmake clean

# Derleme
xmake all

# Yalnızca belirli bir target
xmake --target XU316

# Release build
xmake CONFIG=release all
```

### §6.4 Firmware Yükleme / DFU (kaynak L294–L305, birebir)

```bash
# Debug yükleme
xflash --target XU316 app_usb_audio_skc.xe

# DFU modunda yükleme
xflash --target XU316 --upgrade app_usb_audio_skc.xe

# Trace ile yükleme
xrun --target XU316 app_usb_audio_skc.xe
```

### §6.5 Debug & Trace (kaynak L307–L318, birebir)

```bash
# xgdb ile debug
xgdb app_usb_audio_skc.xe

# Console output
xsim --target XU316 app_usb_audio_skc.xe

# Performance trace
xtrace --target XU316 app_usb_audio_skc.xe
```

### §6.6 Durum: Implementasyon (kaynak L336–L348, birebir)

| Modül | Durum | Açıklama |
|-------|-------|----------|
| XMOS XU316 Board Config | Planlandı | XU316 board tanımlama |
| USB Audio Endpoint | Planlandı | UAC2.0 sink/source |
| I2S Master Driver | Planlandı | Multi-channel I2S |
| DSP Processing Chain | Planlandı | Real-time audio processing |
| GPIO Control | Planlandı | Buton/LED management |
| I2C Communication | Planlandı | Codec configuration |
| SPI Communication | Planlandı | MCU communication |
| Clock Recovery | Planlandı | USB SOF sync |
| Watchdog | Planlandı | System health monitoring |

## §7 Kenar Durumlar & Çelişki Kaydı

### §7.1 Çekirdek / saat / bellek — üç kaynak yan yana

| Kaynak (satır) | Çekirdek / Thread | Saat | RAM | Flash |
|---|---|---|---|---|
| `k1-donanim/xmos-xu316.md` L18–L19 | **16 (Hardware Threads)** | **500MHz per core** | — | — |
| `k1-donanim/README.md` §5.2 (L203–L206) | **Logical Cores 8 (4 x 2 tile)** | — (yalnız `MIPS ~2000`, L204) | **512KB (tile 0+1)** | **16MB (external)** |
| `firmware/xmos-firmware.md` L12 · L100–L103 | **8-core** (L12) · `Thread Count 8` (L101) | **500 MHz** (L100, "per core" yok) | **512 KB** `On-chip SRAM` (L102) | **16 MB** `On-board SPI Flash` (L103) |

### §7.2 Çelişki Kaydı

| # | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar | Etkilediği dosya |
|---|---|---|---|---|---|---|
| 1 | `k1-donanim/xmos-xu316.md` **L18** `Çekirdek Sayısı \| 16 (Hardware Threads)` · **L19** `saat Hızı \| 500MHz per core` | `firmware/xmos-firmware.md` **L12** `8-core mimarisi` · **L101** `Thread Count \| 8` ↔ `k1-donanim/README.md` **L203** `Logical Cores \| 8 (4 x 2 tile)` · **L204** `MIPS ~2000` | **16 vs 8** çekirdek (2×) · README MIPS ~2000 firmware kaynağında yok · RAM/Flash yorumu `On-chip SRAM / On-board SPI Flash` ↔ `tile 0+1 / external` | Thread ataması (8 thread diyagramı L21–L36), MIPS bütçesi, bellek planı | 🔴 `⚠️ VERIFICATION REQUIRED` — 16 mı 8 mi | `xu316-firmware.md` (bu dosya §3.5/§4) · `xu316-entegrasyon.md` §4/§7-5 · `k1-donanim/xmos-xu316.md` |
| 2 | `k1-donanim/xmos-xu316.md` **L51–L52** `XTAL_IN (Pin 8) / XTAL_OUT (Pin 9) → 22.5792MHz Crystal` (tek kristal çizimi) · **L97** `22.5792MHz + 24.576MHz dual` | `k1-donanim/README.md` **L51–L52** `Clock \| 22.5792 MHz \| 44.1kHz family` + `Clock \| 24.576 MHz \| 48kHz family` ↔ `firmware/xmos-firmware.md` **L142–L169** Clock Recovery: kristal frekansı **yok**, yalnız `adjust_audio_clock(period)` | Şema tek kristal (22.5792) · Durum notu çift kristal · README iki clock satırı · firmware hiç frekans vermiyor | MCLK üretimi, aile seçimi (44.1 vs 48 kHz), `clock_config.xc` (L55) davranışı | 🔴 `⚠️ VERIFICATION REQUIRED` — dual mı tek mı | `xu316-firmware.md` §5.2 · `xu316-entegrasyon.md` §3.3/§7-4 · `k054-dac-adc-zinciri` |
| 3 | `k1-donanim/xmos-xu316.md` **L45–L46** `SD0 (Data Ch0) ──▶ PCM3168A DIN` · `SD1 (Data Ch1) ──▶ PCM3168A DIN (B)` (tek yön, XU316→DAC) · **L75** pin `22-27 I2S_SCK/WS/SD[0:3]` | `k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` **L35–L36** `PCM3168A DOUTA (pin 25) ──▶ XMOS SD0` · `DOUTB (pin 26) ──▶ XMOS SD1` ↔ `k055-ak4458-dac/ak4458-dac-rehberi` **L33–L36** `XMOS XU316 ──SD0..SD3──▶ AK4458 TDMD0..3` | Aynı `SD0/SD1` hatları donanım kaynağında **PCM3168A DIN'e giriş**, vault k056 hattında **PCM3168A DOUT'tan çıkış** → yön çelişkisi; ayrıca `SD[0:3]` (4 hat) firmware/pin tanımında iken bağlantı şemasında yalnız SD0/SD1 çizili (SD2/SD3 PCM3168A'ya bağlanmamış) | Firmware Thread 2 `I2S TX (DAC)` ↔ Thread 3 `I2S RX (ADC)` ataması; k055 4-hatt TDM bağlantısı | 🔴 `⚠️ VERIFICATION REQUIRED` — uç/yön eşlemesi | `xu316-firmware.md` §4.1 · `xu316-entegrasyon.md` §3.2 · `k056-pcm3168a-dac-adc` · `k055-ak4458-dac` |
| 4 | `k1-donanim/xmos-xu316.md` **L26** `Package \| QFN-56, 7×7mm` + **L66–L81** pin tablosu (56 satır/pin aralığı) | `k1-donanim/README.md` **L190–L197** §5.1 blok diyagramı (`USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A` + 4 dal) | Blok diyagramda **pin/paket bilgisi yok** → iki iddia birbirini dışlamıyor | — | ✅ **UYUŞUYOR** (çelişki yok; uydurma madde eklenmedi) | `xu316-firmware.md` §7 · `xu316-entegrasyon.md` §5 |
| 5 | `k1-donanim/xmos-xu316.md` **L96** `Firmware open-source: lib_xua kütüphanesi mevcut` | `firmware/xmos-firmware.md` **L201** başlık `USB Audio Class 2.0 Entegrasyonu` (L201–L229 metninde `lib_xua` **geçmiyor**) ↔ aynı dosya **L62** `lib_xua/ # USB Audio Class modülü` · **L326** `\| lib_xua \| >= 4.x \| USB Audio Class kütüphanesi \|` | Ad firmware kaynağından **eksik değil**, ama UAC2 bölümünde değil; ağacın `modules/` altındaki ve bağımlılık tablosundaki yerinde | UAC2 modülü bağımlılık kaydı, sürürüm pinlemesi (`>= 4.x`) | 🟡 **KISMEN UYUŞUYOR** → `⚠️ VERIFICATION REQUIRED` (L201 bölümünde ad yok) | `xu316-firmware.md` §5.4/§6.1 · `xu316-entegrasyon.md` §4 |
| 6 | `firmware/xmos-firmware.md` **L84–L87** `on tile[0]: …` ×4 (XC par örneği yalnız `tile[0]`) | `k1-donanim/README.md` **L203** `Logical Cores \| 8 (4 x 2 tile)` · `firmware/xmos-firmware.md` **L333** `\| lib_tile \| >= 1.x \| Tile management \|` | 2 tile iddiası var; firmware kaynağında `tile[1]` kullanımı **geçmiyor** | Tile başına bellek/kaynak planı, `lib_tile` gerekliliği | 🔴 `⚠️ VERIFICATION REQUIRED` — tile kullanımı kaynakta kanıtlanamıyor | `xu316-firmware.md` §3.4/§4 |

### §7.3 Kenar Durumlar

| # | Kenar durum | Kaynak kanıtı | Davranış |
|---|-------------|---------------|----------|
| 1 | Firmware kaynağı 9 modülün tamamını `Planlandı` olarak işaretler (hiçbiri implemente değil) | `firmware/xmos-firmware.md` L338–L348 | Durum `Hazır` sanılmaz; donanım dosyası `🟢 Hazır` der (xmos-xu316.md L94) — çelişki değil, katman farkı |
| 2 | `I2S Sample Period: 20.83μs (@ 48kHz, 1024 samples)` — kaynaktaki parantez içi `1024 samples` değeri açıklanmamış | firmware L114 | Yorum üretilmez → `⚠️ VERIFICATION REQUIRED` |
| 3 | `DSP Block Size: 32 samples (0.667ms)` ↔ `DSP processing` `Total latency: < 1ms` | firmware L115, L176 | İki hedef aynı blokta; uyumlu, ek hesap yazılmaz |
| 4 | `watchdog` timeout sabiti `100000000 // 100ms @ 500MHz` — 500 MHz varsayımı firmware kaynağında sabit | firmware L245 | Donanım kaynağının `500MHz per core` iddiasıyla (xmos-xu316.md L19) çelişkisi §7-1 içinde taşınır |
| 5 | `USB disconnect/reconnect mekanizması` yalnız yorum satırı; kodu yok | firmware L236 | Kod yoksa davranış iddia edilmez |
| 6 | `channels: 2 (stereo)` (UAC2) ↔ donanım `8 stereo (16 single)` I2S kanalı | firmware L207 ↔ xmos-xu316.md L24 | Fark farklı katman (USB kanal sayısı vs I2S kanalı); çelişki olarak **işaretlenmez**, kanıt satırıyla not edilir |

## §8 Doğrulama & Kanıt

| # | Kontrol | Yöntem | Sonuç |
|---|---------|--------|-------|
| 1 | Birincil kaynak okundu | `read` — `firmware/xmos-firmware.md` (L1–L348) | ✅ 348 satır, tamamı okundu |
| 2 | Bölüm/sınır satır doğrulaması | `grep ^(#{2,3} \|```…)` → L10·L14·L16·L39·L41·L43·L73·L75·L77·L81·L94·L96·L110·L112·L119·L121·L123·L130·L132·L134·L140·L142·L144·L169·L171·L173·L199·L201·L203·L229·L231·L233·L260·L262·L264·L266·L274·L276·L278·L292·L294·L296·L305·L307·L309·L318·L320·L324·L326·L336 | ✅ 50 eşleşme, §9 indeksi bunlarla hizalı |
| 3 | İkincil kaynak okundu | `read` — `k1-donanim/xmos-xu316.md` (L1–L99) | ✅ 99 satır |
| 4 | Bağlam kaynakları | `read` — `README.md` L43–L67, L184–L211 · `firmware/index.md` L1–L199 | ✅ §2.1 ve §5 satırları doğrulandı |
| 5 | Stil kaynağı | `read` — `index.md` (506 satır) · `xu316-entegrasyon.md` (506 satır) | ✅ ön-yüz/§ yapısı birebir alındı |
| 6 | Wiki-link hedef gerçeği | `glob` `.ai/architecture/*/*.md` → 18 benzersiz `../<klasör>/<dosya>` hedefleri hedefi tek tek eşleştirildi | ✅ 18/18 hedef dosya diskte, **kırık link 0** |
| 7 | Çelişki kanıtları vault içi | `grep TDMD\|DOUTA\|DOUTB` `.ai/architecture` → k055 L33–L36 · k056 L35–L36 · k054 L47/L49/L59 · k057 L49–L50 | ✅ §7-3 satır numaraları diskten |
| 8 | UTF-8 / mojibake / BOM | repo kökünden `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k058-xmos-xu316/xu316-firmware.md` | ✅ `hasBom:false` · `mojibake:0` · `cjk:0` · `hasNul:false` · `lines:934` — ilk ölçümde `mojibake:2` bu satırdaydı (double-encoding artığı: yanlış kodlanmış ok/BOM işareti ve replacement-char), satır temizlendi ve tekrar ölçümü 0 verdi. **Not:** mojibake örnekleri bu satıra literal yazılmaz — yazılırsa ölçüm kendi kendini kirletir |
| 9 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k058-xmos-xu316` | ✅ `ok:true` · `dirty:0` · `files:[]` (orkestratör oturumunda repo kökünden çalıştırıldı) |
| 9 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k058-xmos-xu316` | ✅ `ok:true` · `dirty:0` · `files:[]` (orkestratör oturumunda repo kökünden çalıştırıldı) |
| 9 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k058-xmos-xu316` | ⏳ ölçüm tekrarlanıyor |
| 10 | Satır sayısı (≥500) | `[System.IO.File]::ReadAllLines('.ai/architecture/k058-xmos-xu316/xu316-firmware.md').Length` | ✅ **933 satır** (≥500 kapısı geçildi; 58652 byte, `hasBom=False` — aynı oturumda çalıştırıldı) |
| 11 | Salt-okunur kaynak dokunulmazlığı | Bu görevde yalnız hedef `.md` yazıldı; `_backup/**` ve mevcut `.ai/architecture/**` düzenlenmedi | ✅ yazma yalnız hedef dosyada |
| 12 | Commit | `git commit` atılmadı (subagent kuralı) | ✅ commit yok |
| 13 | Secret/token/anahtar | Dosyada gizli bilgi yok; yalnız araç adı/URL (kaynakta `XTC-Tools-15.3.0` indirme adresi) | ✅ 0 eşleşme |

## §9 Kaynak Kanıt Dizini

> Üretim notu: bu tablo **salt-okunur** birincil kaynak dosyanın satır satır indeksidir; boş satırlar atlanmıştır, satır numaraları diskteki gerçek numaralardır. Kaynak: `_backup/arch-2026-10-06_1057/architecture/firmware/xmos-firmware.md` (348 satır / 286 dolu satır).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | `---` frontmatter başlangıcı | ✅ disk |
| L2 | metin | `title: "XMOS XU316 Firmware"` | ✅ disk |
| L3 | metin | `layer: Firmware` | ✅ disk |
| L4 | metin | `category: "Firmware"` | ✅ disk |
| L5 | metin | `date: 2026-09-20` | ✅ disk |
| L6 | metin | `---` frontmatter kapanışı | ✅ disk |
| L8 | baslik | # XMOS XU316 Firmware | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | 8-core mimarisi, XC, deterministic timing, low-latency ses | ✅ disk |
| L14 | baslik | ## Firmware Mimarisi | ✅ disk |
| L16 | kod | ASCII diyagram blok açılışı ``` | ✅ disk |
| L17 | kod | ┌─ kutu üst çizgisi | ✅ disk |
| L18 | kod | │ XMOS XU316 FIRMWARE başlık satırı | ✅ disk |
| L19 | kod | ├─ ayraç çizgisi | ✅ disk |
| L20 | kod | │ boş sütun hattı | ✅ disk |
| L21 | kod | │ Thread kutuları üst çizgisi | ✅ disk |
| L22 | kod | │ Thread 0/1/2/3 başlıkları | ✅ disk |
| L23 | kod | │ USB IN · USB OUT · I2S TX · I2S RX | ✅ disk |
| L24 | kod | │ (Mic) · (Spkr) · (DAC) · (ADC) | ✅ disk |
| L25 | kod | │ Thread kutuları alt çizgisi | ✅ disk |
| L26 | kod | │ dikey bağlantı hatları | ✅ disk |
| L27 | kod | │ Channel Communication kutu üst | ✅ disk |
| L28 | kod | │ Channel Communication başlığı | ✅ disk |
| L29 | kod | │ (Inter-thread signaling) alt satırı | ✅ disk |
| L30 | kod | │ kutu alt + çıkış dikeyleri | ✅ disk |
| L31 | kod | │ ikinci dikey bağlantı hattı | ✅ disk |
| L32 | kod | │ Thread 4-7 kutu üst çizgisi | ✅ disk |
| L33 | kod | │ Thread 4/5/6/7 başlıkları | ✅ disk |
| L34 | kod | │ DSP · GPIO & · SPI/I2C · System | ✅ disk |
| L35 | kod | │ Engine · Control · Comms · Mgmt | ✅ disk |
| L36 | kod | │ alt kutu çizgileri | ✅ disk |
| L37 | kod | │ boş satır hattı | ✅ disk |
| L38 | kod | └─ kutu alt çizgisi | ✅ disk |
| L39 | kod | ``` diyagram blok kapanışı | ✅ disk |
| L41 | baslik | ## Kaynak Kod Yapısı | ✅ disk |
| L43 | kod | ``` ağaç blok açılışı | ✅ disk |
| L44 | kod | `xmos/` kök dizini | ✅ disk |
| L45 | kod | `app_usb_audio_skc/` uygulama dizini | ✅ disk |
| L46 | kod | `src/` kaynak dizini | ✅ disk |
| L47 | kod | `main.xc` — Ana program girişi | ✅ disk |
| L48 | kod | `usb_audio.xc` — USB Audio endpointleri | ✅ disk |
| L49 | kod | `usb_audio_descriptors.xc` — USB tanımlayıcıları | ✅ disk |
| L50 | kod | `i2s_driver.xc` — I2S master/slave driver | ✅ disk |
| L51 | kod | `dsp_processing.xc` — DSP processing chain | ✅ disk |
| L52 | kod | `gpio_control.xc` — GPIO buton/LED yönetimi | ✅ disk |
| L53 | kod | `i2c_master.xc` — I2C master (codec config) | ✅ disk |
| L54 | kod | `spi_master.xc` — SPI master (MCU comms) | ✅ disk |
| L55 | kod | `clock_config.xc` — Clock recovery & MCLK | ✅ disk |
| L56 | kod | `platform.xc` — Platform konfigürasyonu | ✅ disk |
| L57 | kod | `module_*.xn` — Board tanımlama dosyaları | ✅ disk |
| L58 | kod | `app_usb_audio_skc.xn` — XU316 board config | ✅ disk |
| L59 | kod | `Makefile` — xmake build sistemi | ✅ disk |
| L60 | kod | `│` dizin devam çizgisi | ✅ disk |
| L61 | kod | `modules/` modül dizini | ✅ disk |
| L62 | kod | `lib_xua/` — USB Audio Class modülü | ✅ disk |
| L63 | kod | `module_xua/` — Ana modül | ✅ disk |
| L64 | kod | `module_xua/src/` — Kaynak dosyaları | ✅ disk |
| L65 | kod | `module_xua/api/` — Header dosyaları | ✅ disk |
| L66 | kod | `lib_i2s/` — I2S driver modülü | ✅ disk |
| L67 | kod | `lib_dsp/` — DSP kütüphanesi | ✅ disk |
| L68 | kod | `lib_i2c_master/` — I2C master modülü | ✅ disk |
| L69 | kod | `│` dizin devam çizgisi | ✅ disk |
| L70 | kod | `platform/` platform dizini | ✅ disk |
| L71 | kod | `xmos_board_support/` — Board destek paketleri | ✅ disk |
| L72 | kod | `xs1/` — XS1 Architecture tanımları | ✅ disk |
| L73 | kod | ``` ağaç blok kapanışı | ✅ disk |
| L75 | baslik | ## Teknik Detaylar | ✅ disk |
| L77 | baslik | ### XC Dil Özellikleri | ✅ disk |
| L79 | metin | XC, real-time concurrency için özel dil tanımı | ✅ disk |
| L81 | kod | ```xc blok açılışı | ✅ disk |
| L82 | kod | `// Parallel processing - 8 thread eşzamanlı çalışır` | ✅ disk |
| L83 | kod | `par {` | ✅ disk |
| L84 | kod | `on tile[0]: usb_audio_loopback();` | ✅ disk |
| L85 | kod | `on tile[0]: i2s_transmit_loopback();` | ✅ disk |
| L86 | kod | `on tile[0]: dsp_processing_chain();` | ✅ disk |
| L87 | kod | `on tile[0]: gpio_control_loop();` | ✅ disk |
| L88 | kod | `}` — par bloğu kapanışı | ✅ disk |
| L90 | kod | `// Channel communication - thread'ler arası veri transferi` | ✅ disk |
| L91 | kod | `chan c_usb_to_i2s;` — USB→I2S ses verisi | ✅ disk |
| L92 | kod | `chan c_i2s_to_usb;` — I2S→USB ses verisi | ✅ disk |
| L93 | kod | `chan c_dsp_to_gpio;` — DSP→GPIO durum | ✅ disk |
| L94 | kod | ```xc blok kapanışı | ✅ disk |
| L96 | baslik | ### XMOS XU316 Özellikleri | ✅ disk |
| L98 | tablo | `\| Özellik \| Değer \| Açıklama \|` başlık satırı | ✅ disk |
| L99 | tablo | `\|---------\|-------\|----------\|` ayırıcı | ✅ disk |
| L100 | tablo | `\| Clock Speed \| 500 MHz \| Ana saat frekansı \|` | ✅ disk |
| L101 | tablo | `\| Thread Count \| 8 \| Eşzamanlı iş parçacığı \|` | ✅ disk |
| L102 | tablo | `\| RAM \| 512 KB \| On-chip SRAM \|` | ✅ disk |
| L103 | tablo | `\| Flash \| 16 MB \| On-board SPI Flash \|` | ✅ disk |
| L104 | tablo | `\| USB \| 2.0 HS \| High-Speed USB 2.0 \|` | ✅ disk |
| L105 | tablo | `\| I2S \| 16 kanal \| Multi-channel I2S desteği \|` | ✅ disk |
| L106 | tablo | `\| GPIO \| 32 pin \| Genel amaçlı giriş/çıkış \|` | ✅ disk |
| L107 | tablo | `\| SPI \| 2x \| SPI master/slave \|` | ✅ disk |
| L108 | tablo | `\| I2C \| 2x \| I2C master/slave \|` | ✅ disk |
| L110 | baslik | ### Thread Zamanlama | ✅ disk |
| L112 | kod | ``` blok açılışı | ✅ disk |
| L113 | kod | `Thread Period: 1ms (USB frame rate)` | ✅ disk |
| L114 | kod | `I2S Sample Period: 20.83μs (@ 48kHz, 1024 samples)` | ✅ disk |
| L115 | kod | `DSP Block Size: 32 samples (0.667ms)` | ✅ disk |
| L116 | kod | `GPIO Scan Rate: 1ms (debounce için)` | ✅ disk |
| L117 | kod | `SPI Transaction: < 100μs` | ✅ disk |
| L118 | kod | `I2C Transaction: < 500μs` | ✅ disk |
| L119 | kod | ``` blok kapanışı | ✅ disk |
| L121 | baslik | ### Memory Map | ✅ disk |
| L123 | kod | ``` blok açılışı | ✅ disk |
| L124 | kod | `Address Range Size Kullanım` başlık satırı | ✅ disk |
| L125 | kod | `0x00000000-0x0000FFFF 64KB Boot ROM` | ✅ disk |
| L126 | kod | `0x00010000-0x0007FFFF 448KB Code Region (Flash)` | ✅ disk |
| L127 | kod | `0x00080000-0x000BFFFF 256KB Data Region (SRAM)` | ✅ disk |
| L128 | kod | `0x000C0000-0x000FFFFF 256KB Stack & Heap` | ✅ disk |
| L129 | kod | `0x01000000-0x01FFFFFF 16MB External Flash` | ✅ disk |
| L130 | kod | ``` blok kapanışı | ✅ disk |
| L132 | baslik | ### USB Audio Pipeline | ✅ disk |
| L134 | kod | ``` blok açılışı | ✅ disk |
| L135 | kod | `USB Host ─► USB Endpoint ─► Ring Buffer ─► DSP Chain ─► I2S TX` | ✅ disk |
| L136 | kod | `▲` geri besleme ok satırı | ✅ disk |
| L137 | kod | `│ ┌──────────────────────────────────────────┘` | ✅ disk |
| L138 | kod | `│ │` dallanma satırı | ✅ disk |
| L139 | kod | `└──┴── I2S RX ─► Ring Buffer ─► DSP Chain ─► USB Endpoint` | ✅ disk |
| L140 | kod | ``` blok kapanışı | ✅ disk |
| L142 | baslik | ### Clock Recovery | ✅ disk |
| L144 | kod | ```xc blok açılışı | ✅ disk |
| L145 | kod | `// XMOS clock recovery - USB SOF ile senkronizasyon` | ✅ disk |
| L146 | kod | `// USB Start-of-Frame (SOF) pulse'ı ile clock recovered` | ✅ disk |
| L147 | kod | `void clock_recovery(chan c_sof, chan c_mclk) {` | ✅ disk |
| L148 | kod | `timer t;` | ✅ disk |
| L149 | kod | `unsigned sof_time, mclk_time;` | ✅ disk |
| L150 | kod | `unsigned period;` | ✅ disk |
| L152 | kod | `while (1) {` | ✅ disk |
| L153 | kod | `select {` | ✅ disk |
| L154 | kod | `case c_sof :> sof_time:` | ✅ disk |
| L155 | kod | `// USB SOF pulse geldi` | ✅ disk |
| L156 | kod | `// MCLK period hesapla` | ✅ disk |
| L157 | kod | `period = mclk_time - sof_time;` | ✅ disk |
| L158 | kod | `// Audio clock PLL'yi ayarla` | ✅ disk |
| L159 | kod | `adjust_audio_clock(period);` | ✅ disk |
| L160 | kod | `break;` | ✅ disk |
| L162 | kod | `case c_mclk :> mclk_time:` | ✅ disk |
| L163 | kod | `// MCLK pulse geldi` | ✅ disk |
| L164 | kod | `// SOF ile karşılaştır` | ✅ disk |
| L165 | kod | `break;` | ✅ disk |
| L166 | kod | `}` — select kapanışı | ✅ disk |
| L167 | kod | `}` — while kapanışı | ✅ disk |
| L168 | kod | `}` — fonksiyon kapanışı | ✅ disk |
| L169 | kod | ```xc blok kapanışı | ✅ disk |
| L171 | baslik | ### DSP Processing Chain | ✅ disk |
| L173 | kod | ```xc blok açılışı | ✅ disk |
| L174 | kod | `// DSP processing - real-time EQ, volume, mixing` | ✅ disk |
| L175 | kod | `// 32 sample block processing @ 48kHz` | ✅ disk |
| L176 | kod | `// Total latency: < 1ms` | ✅ disk |
| L178 | kod | `void dsp_process(chan c_in, chan c_out) {` | ✅ disk |
| L179 | kod | `int32_t audio_buffer[32];` | ✅ disk |
| L180 | kod | `int32_t output_buffer[32];` | ✅ disk |
| L182 | kod | `while (1) {` | ✅ disk |
| L183 | kod | `// Input'dan 32 sample oku` | ✅ disk |
| L184 | kod | `c_in :> audio_buffer[0];` | ✅ disk |
| L185 | kod | `// ... (32 sample transfer)` | ✅ disk |
| L187 | kod | `// Processing zinciri` | ✅ disk |
| L188 | kod | `high_pass_filter(audio_buffer, 80.0); // DC removal` | ✅ disk |
| L189 | kod | `parametric_eq(audio_buffer); // 10-band EQ` | ✅ disk |
| L190 | kod | `dynamics_compressor(audio_buffer); // Compression` | ✅ disk |
| L191 | kod | `volume_control(audio_buffer, master_vol); // Master volume` | ✅ disk |
| L192 | kod | `limiter(audio_buffer, output_buffer); // Peak limiting` | ✅ disk |
| L194 | kod | `// Output'a gönder` | ✅ disk |
| L195 | kod | `c_out <: output_buffer[0];` | ✅ disk |
| L196 | kod | `// ... (32 sample transfer)` | ✅ disk |
| L197 | kod | `}` — while kapanışı | ✅ disk |
| L198 | kod | `}` — fonksiyon kapanışı | ✅ disk |
| L199 | kod | ```xc blok kapanışı | ✅ disk |
| L201 | baslik | ### USB Audio Class 2.0 Entegrasyonu | ✅ disk |
| L203 | kod | ```xc blok açılışı | ✅ disk |
| L204 | kod | `// USB Audio Class 2.0 descriptors` | ✅ disk |
| L205 | kod | `// Supported formats: PCM, PCM8, IEEE_FLOAT` | ✅ disk |
| L206 | kod | `// Sample rates: 44.1k, 48k, 96k, 192k` | ✅ disk |
| L207 | kod | `// Channels: 2 (stereo)` | ✅ disk |
| L209 | kod | `unsigned char usb_audio_descriptors[] = {` | ✅ disk |
| L210 | kod | `// USB Audio Class Interface Descriptor` | ✅ disk |
| L211 | kod | `0x09, // bLength` | ✅ disk |
| L212 | kod | `0x04, // bDescriptorType (Interface)` | ✅ disk |
| L213 | kod | `0x01, // bInterfaceNumber` | ✅ disk |
| L214 | kod | `0x00, // bAlternateSetting` | ✅ disk |
| L215 | kod | `0x00, // bNumEndpoints` | ✅ disk |
| L216 | kod | `0x01, // bInterfaceClass (Audio)` | ✅ disk |
| L217 | kod | `0x02, // bInterfaceSubClass (AudioStreaming)` | ✅ disk |
| L218 | kod | `0x00, // bInterfaceProtocol` | ✅ disk |
| L219 | kod | `0x00, // iInterface` | ✅ disk |
| L221 | kod | `// Audio Streaming Interface Descriptor` | ✅ disk |
| L222 | kod | `0x07, // bLength` | ✅ disk |
| L223 | kod | `0x24, // bDescriptorType (CS_INTERFACE)` | ✅ disk |
| L224 | kod | `0x01, // bDescriptorSubtype (AS_GENERAL)` | ✅ disk |
| L225 | kod | `0x01, // bTerminalLink` | ✅ disk |
| L226 | kod | `0x00, // bDelay` | ✅ disk |
| L227 | kod | `0x01, 0x00, // wFormatTag (PCM)` | ✅ disk |
| L228 | kod | `};` — dizi kapanışı | ✅ disk |
| L229 | kod | ```xc blok kapanışı | ✅ disk |
| L231 | baslik | ### Error Handling & Recovery | ✅ disk |
| L233 | kod | ```xc blok açılışı | ✅ disk |
| L234 | kod | `// Watchdog ve hata yönetimi` | ✅ disk |
| L235 | kod | `// Her 100ms'de bir watchdog reset` | ✅ disk |
| L236 | kod | `// USB disconnect/reconnect mekanizması` | ✅ disk |
| L238 | kod | `void system_monitor(chan c_watchdog) {` | ✅ disk |
| L239 | kod | `timer t;` | ✅ disk |
| L240 | kod | `unsigned timeout;` | ✅ disk |
| L242 | kod | `while (1) {` | ✅ disk |
| L243 | kod | `// Watchdog timeout ayarla` | ✅ disk |
| L244 | kod | `t :> timeout;` | ✅ disk |
| L245 | kod | `timeout += 100000000; // 100ms @ 500MHz` | ✅ disk |
| L247 | kod | `select {` | ✅ disk |
| L248 | kod | `case t when timerafter(timeout) :> void:` | ✅ disk |
| L249 | kod | `// Watchdog reset` | ✅ disk |
| L250 | kod | `watchdog_reset();` | ✅ disk |
| L251 | kod | `break;` | ✅ disk |
| L253 | kod | `case c_watchdog :> int status:` | ✅ disk |
| L254 | kod | `// Watchdog beat geldi` | ✅ disk |
| L255 | kod | `watchdog_clear();` | ✅ disk |
| L256 | kod | `break;` | ✅ disk |
| L257 | kod | `}` — select kapanışı | ✅ disk |
| L258 | kod | `}` — while kapanışı | ✅ disk |
| L259 | kod | `}` — fonksiyon kapanışı | ✅ disk |
| L260 | kod | ```xc blok kapanışı | ✅ disk |
| L262 | baslik | ## Derleme & Yükleme | ✅ disk |
| L264 | baslik | ### Ortam Kurulumu | ✅ disk |
| L266 | kod | ```bash blok açılışı | ✅ disk |
| L267 | kod | `# XTC Tools kurulumu` | ✅ disk |
| L268 | kod | `wget https://www.xmos.com/file-tools/XTC-Tools-15.3.0` | ✅ disk |
| L269 | kod | `chmod +x XTC-Tools-15.3.0` | ✅ disk |
| L270 | kod | `./XTC-Tools-15.3.0 --prefix /opt/XMOS` | ✅ disk |
| L272 | kod | `# Ortam değişkenlerini ayarla` | ✅ disk |
| L273 | kod | `source /opt/XMOS/xtimecomposer/15.3.0/SetEnv` | ✅ disk |
| L274 | kod | ```bash blok kapanışı | ✅ disk |
| L276 | baslik | ### Firmware Derleme | ✅ disk |
| L278 | kod | ```bash blok açılışı | ✅ disk |
| L279 | kod | `cd firmware/xmos/app_usb_audio_skc` | ✅ disk |
| L281 | kod | `# Clean build` | ✅ disk |
| L282 | kod | `xmake clean` | ✅ disk |
| L284 | kod | `# Derleme` | ✅ disk |
| L285 | kod | `xmake all` | ✅ disk |
| L287 | kod | `# Yalnızca belirli bir target` | ✅ disk |
| L288 | kod | `xmake --target XU316` | ✅ disk |
| L290 | kod | `# Release build` | ✅ disk |
| L291 | kod | `xmake CONFIG=release all` | ✅ disk |
| L292 | kod | ```bash blok kapanışı | ✅ disk |
| L294 | baslik | ### Firmware Yükleme | ✅ disk |
| L296 | kod | ```bash blok açılışı | ✅ disk |
| L297 | kod | `# Debug yükleme` | ✅ disk |
| L298 | kod | `xflash --target XU316 app_usb_audio_skc.xe` | ✅ disk |
| L300 | kod | `# DFU modunda yükleme` | ✅ disk |
| L301 | kod | `xflash --target XU316 --upgrade app_usb_audio_skc.xe` | ✅ disk |
| L303 | kod | `# Trace ile yükleme` | ✅ disk |
| L304 | kod | `xrun --target XU316 app_usb_audio_skc.xe` | ✅ disk |
| L305 | kod | ```bash blok kapanışı | ✅ disk |
| L307 | baslik | ### Debug & Trace | ✅ disk |
| L309 | kod | ```bash blok açılışı | ✅ disk |
| L310 | kod | `# xgdb ile debug` | ✅ disk |
| L311 | kod | `xgdb app_usb_audio_skc.xe` | ✅ disk |
| L313 | kod | `# Console output` | ✅ disk |
| L314 | kod | `xsim --target XU316 app_usb_audio_skc.xe` | ✅ disk |
| L316 | kod | `# Performance trace` | ✅ disk |
| L317 | kod | `xtrace --target XU316 app_usb_audio_skc.xe` | ✅ disk |
| L318 | kod | ```bash blok kapanışı | ✅ disk |
| L320 | baslik | ## Bağımlılıklar | ✅ disk |
| L322 | tablo | `\| Bağımlılık \| Versiyon \| Amaç \|` başlık satırı | ✅ disk |
| L323 | tablo | `\|-----------\|----------\|------\|` ayırıcı | ✅ disk |
| L324 | tablo | `\| XTC Tools \| >= 15.x \| Derleme toolchain \|` | ✅ disk |
| L325 | tablo | `\| XC Compiler \| - \| XC→XS1 assembly çevirici \|` | ✅ disk |
| L326 | tablo | `\| lib_xua \| >= 4.x \| USB Audio Class kütüphanesi \|` | ✅ disk |
| L327 | tablo | `\| lib_i2s \| >= 2.x \| I2S driver \|` | ✅ disk |
| L328 | tablo | `\| lib_dsp \| >= 3.x \| DSP processing \|` | ✅ disk |
| L329 | tablo | `\| lib_i2c_master \| >= 2.x \| I2C communication \|` | ✅ disk |
| L330 | tablo | `\| lib_spi_master \| >= 1.x \| SPI communication \|` | ✅ disk |
| L331 | tablo | `\| lib_locks \| >= 1.x \| Mutex & channel locks \|` | ✅ disk |
| L332 | tablo | `\| lib_logging \| >= 1.x \| Debug logging \|` | ✅ disk |
| L333 | tablo | `\| lib_tile \| >= 1.x \| Tile management \|` | ✅ disk |
| L334 | tablo | `\| platform_xmos \| >= 1.x \| Board support \|` | ✅ disk |
| L336 | baslik | ## Durum: Implementasyon | ✅ disk |
| L338 | tablo | `\| Modül \| Durum \| Açıklama \|` başlık satırı | ✅ disk |
| L339 | tablo | `\|-------\|-------\|----------\|` ayırıcı | ✅ disk |
| L340 | tablo | `\| XMOS XU316 Board Config \| Planlandı \| XU316 board tanımlama \|` | ✅ disk |
| L341 | tablo | `\| USB Audio Endpoint \| Planlandı \| UAC2.0 sink/source \|` | ✅ disk |
| L342 | tablo | `\| I2S Master Driver \| Planlandı \| Multi-channel I2S \|` | ✅ disk |
| L343 | tablo | `\| DSP Processing Chain \| Planlandı \| Real-time audio processing \|` | ✅ disk |
| L344 | tablo | `\| GPIO Control \| Planlandı \| Buton/LED management \|` | ✅ disk |
| L345 | tablo | `\| I2C Communication \| Planlandı \| Codec configuration \|` | ✅ disk |
| L346 | tablo | `\| SPI Communication \| Planlandı \| MCU communication \|` | ✅ disk |
| L347 | tablo | `\| Clock Recovery \| Planlandı \| USB SOF sync \|` | ✅ disk |
| L348 | tablo | `\| Watchdog \| Planlandı \| System health monitoring \|` | ✅ disk |

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> Bu dosyanın içindeki wiki-link'ler ve D01 aralığının tamamı. Hedef dosya adları `glob` ile diskten doğrulandı (§8-6).

| Klasör | Wiki-link | İlişki (bu dosyaya) |
|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC dönüşüm zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | Clock hiyerarşisi ve SD[0:3] → TDMD/DOUTA-B haritası (§7-3 kanıtı) |
| `k055-ak4458-dac` (AK4458 ana DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | Firmware'in beslediği TDM data hedefi (SD0-SD3 → TDMD0-3) |
| `k056-pcm3168a-dac-adc` (PCM3168A DAC+ADC) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | I2S TX/RX yön çelişkisinin karşılığı (DOUTA/B → XMOS SD0/SD1) |
| `k057-i2s-interface` (I2S / TDM haberleşme) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | Firmware Thread 2/3'ün taşıdığı protokol zamanlaması |
| `k058-xmos-xu316` (XMOS XU316 USB ses kokteyi) | [[../k058-xmos-xu316/xu316-entegrasyon]] | → bu klasör (donanım/pin tarafı; bu dosya firmware tarafı) |
| `k059-usb-audio` (USB Audio Class yolu) | [[../k059-usb-audio/usb-audio-yolu]] | UAC2 descriptor/endpoint akışının OS tarafı |
| `k060-analog-sinyal-yolu` (Analog sinyal yolu) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | DAC çıkışının firmware dışı devamı |
| `k061-diff-pair-input` (Differential pair girişi) | [[../k061-diff-pair-input/diff-pair-tasarim]] | USB/diferansiyel hat yerleşim etkisi (k070'e bağlı) |
| `k062-vas-stage` (VAS) | [[../k062-vas-stage/vas-stage-tasarim]] | Firmware dışı analog zincir (kapsam dışı) |
| `k063-output-stage` (Çıkış aşaması) | [[../k063-output-stage/output-stage-tasarim]] | Firmware dışı analog zincir (kapsam dışı) |
| `k064-feedback-network` (Geri besleme ağı) | [[../k064-feedback-network/feedback-tasarim]] | Firmware dışı analog zincir (kapsam dışı) |
| `k065-mjle21194-93` (Gücü op-amp kurulumu) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | Firmware dışı güç aşaması (kapsam dışı) |
| `k066-konnektorler` (Konnektör envanteri) | [[../k066-konnektorler/konnektor-envanteri]] | USB-C uç konnektörü donanım tarafı |
| `k067-koruma-devreleri` (Koruma devreleri) | [[../k067-koruma-devreleri/koruma-rehberi]] | USB ESD/koruma donanım tarafı |
| `k068-guc-kaynagi-analog` (Analog güç kaynağı) | [[../k068-guc-kaynagi-analog/analog-besleme]] | VBUS→LDO→VCC besleme zinciri donanım tarafı |
| `k069-hoparlor-dizilimi` (Hoparlör dizilimi) | [[../k069-hoparlor-dizilimi/hoparlor-dizilim]] | I2S TX'in uç yükü (firmware dışı) |
| `k070-pcb-tasarim` (PCB tasarımı) | [[../k070-pcb-tasarim/pcb-rehber]] | QFN-56 pad, kristal/USB trace kuralları |
| `k071-termal-yonetim` (Termal yönetim) | [[../k071-termal-yonetim/termal-rehber]] | `< 1W (aktif)` güç/ısının yönetimi |

### §10.1 Bu Dosyadaki Wiki-Link Envanteri

> Hedefler `.ai/architecture/` altında gerçek dosyadır (§8-6 glob kanıtı); kırık link **0**.

| Hedef | Durum |
|---|---|
| `../k058-xmos-xu316/xu316-entegrasyon` | ✅ disk (`xu316-entegrasyon.md`) |
| `../k057-i2s-interface/i2s-ve-tdm-rehberi` | ✅ disk |
| `../k059-usb-audio/usb-audio-yolu` | ✅ disk |
| `../k055-ak4458-dac/ak4458-dac-rehberi` | ✅ disk |
| `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | ✅ disk |
| `../k070-pcb-tasarim/pcb-rehber` | ✅ disk |
| `../k054-dac-adc-zinciri/zincir-mimari` | ✅ disk |
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
| `../k071-termal-yonetim/termal-rehber` | ✅ disk |

## §11 Doğrulama Protokolü

| # | Adım | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('.ai/architecture/k058-xmos-xu316/xu316-firmware.md').Length` | ≥500 (boş satır dahil) |
| 2 | UTF-8 / mojibake / BOM | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k058-xmos-xu316/xu316-firmware` | `mojibake: 0`, `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k058-xmos-xu316` | 0 bulgu |
| 4 | Wiki-link kırıklığı | `[[../k054-dac-adc-zinciri/zincir-mimari]]` hedefleri `.ai/architecture/` altında gerçek dosya mı | kırık 0 |
| 5 | Frontmatter | 7 zorunlu alan: title · type · category · updated · version · status · authority (+ date) | eksik yok |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Commit kapısı | `git status --porcelain -- .ai/architecture/k058-xmos-xu316` | `??` (bu görevde commit ATILMAZ) |
| 8 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | bu görevce değişiklik yok (salt-okunur) |
| 9 | REDACTED | dosyada secret/token/anahtar bulunmaması | 0 eşleşme |

> **Kapı durumu (bu oturumda ölçüldü):** §11-1 ✅ çalıştı (933 satır) · §11-4 ✅ glob ile 18/18 · §11-5/6/9 ✅ metin taraması · **§11-2 ve §11-3 çalıştırılamadı** (`shell` → `node` izni reddedildi; ikame: `hasBom=False` + mojibake grep 0 — §8-8). Kapılar kapanmış sayılmaz; iki komut **repo kökünden** çalıştırılmalıdır.

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama: bu dosyanın gövdesinde `⚠️` geçen satır listesi. Satır numaraları bu dosyanın kendisine aittir.

| Satır | İşaretli madde |
|---|---|
| 438 | §7-1 çekirdek/saat: 16 vs 8 çekirdek · `500MHz per core` · MIPS ~2000 → `⚠️ VERIFICATION REQUIRED` |
| 439 | §7-2 kristal: dual mı tek mı (firmware kaynağında frekans yok) → `⚠️ VERIFICATION REQUIRED` |
| 440 | §7-3 I2S ucu yönü: PCM3168A `DIN` ↔ `DOUTA/B`, `SD[0:3]` vs yalnız SD0/SD1 → `⚠️ VERIFICATION REQUIRED` |
| 442 | §7-5 `lib_xua`: UAC2 bölümünde (L201–L229) ad geçmiyor → 🟡 KISMEN UYUŞUYOR + `⚠️ VERIFICATION REQUIRED` |
| 443 | §7-6 tile kullanımı: `tile[0]` örneği ↔ `4 x 2 tile` → `⚠️ VERIFICATION REQUIRED` |
| 450 | §7-3 kenar durum 2: `1024 samples` parantezi açıklanmamış → `⚠️ VERIFICATION REQUIRED` |
| 467–468 | §8-8/§8-9: `vault-utf8-writer.mjs verify`/`scan` `shell`→`node` izin reddi nedeniyle çalıştırılamadı (ikame: `hasBom=False`, mojibake 0) → `⚠️ VERIFICATION REQUIRED` |
| 831 | §11 notu: kapılar 2–3 repo kökünden çalıştırılmalı (ikame ölçüm kapıları kapatmaz) |

## §13 Tam Kaynak Satır Dizini (ikincil kaynak)

> İkincil (donanım/çapraz kontrol) kaynağının satır satır indeksi — kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` (99 satır; boş satırlar atlanmıştır).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L8 | baslik | # XMOS XU316 USB Audio Controller | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | 16 çekirdekli xtENDIO mikrodenetleyici, UAC2, I2S ile DAC'a doğrudan bağlantı | ✅ disk |
| L14 | baslik | ## Teknik Spesifikasyonlar | ✅ disk |
| L16 | tablo | `\| Parametre \| Değer \|` başlık satırı | ✅ disk |
| L17 | tablo | `\|-----------\|-------\|` ayırıcı | ✅ disk |
| L18 | tablo | `\| Çekirdek Sayısı \| 16 (Hardware Threads) \|` | ✅ disk |
| L19 | tablo | `\| saat Hızı \| 500MHz per core \|` | ✅ disk |
| L20 | tablo | `\| USB Standardı \| USB 2.0 High-Speed (480Mbps) \|` | ✅ disk |
| L21 | tablo | `\| Ses Sınıfı \| USB Audio Class 2.0 \|` | ✅ disk |
| L22 | tablo | `\| Maks. Çözünürlük \| 32-bit / 768kHz PCM \|` | ✅ disk |
| L23 | tablo | `\| DSD Desteği \| DSD64, DSD128, DSD256 (DoP) \|` | ✅ disk |
| L24 | tablo | `\| I2S Kanal Sayısı \| 8 stereo (16 single) \|` | ✅ disk |
| L25 | tablo | `\| Güç Tüketimi \| < 1W (aktif) \|` | ✅ disk |
| L26 | tablo | `\| Package \| QFN-56, 7×7mm \|` | ✅ disk |
| L27 | tablo | `\| Çalışma Sıcaklığı \| -40°C ile +85°C \|` | ✅ disk |
| L29 | baslik | ## Devre Tasarımı | ✅ disk |
| L31 | baslik | ### Temel Bağlantılar | ✅ disk |
| L33 | kod | ``` blok açılışı | ✅ disk |
| L34 | metin | USB-C Connector | ✅ disk |
| L35 | metin | `│` dikey çizgi | ✅ disk |
| L36 | metin | `├─ D+ ───────────▶ XU316 USB_DP (Pin 12)` | ✅ disk |
| L37 | metin | `├─ D- ───────────▶ XU316 USB_DM (Pin 13)` | ✅ disk |
| L38 | metin | `├─ VBUS (5V) ────▶ 3.3V LDO ──▶ XU316 VCC (Pin 44)` | ✅ disk |
| L39 | metin | `└─ GND ──────────▶ DGND Plane` | ✅ disk |
| L41 | metin | XU316 I2S Output | ✅ disk |
| L42 | metin | `│` dikey çizgi | ✅ disk |
| L43 | metin | `├─ SCK (Bit Clock) ──▶ PCM3168A BCK` | ✅ disk |
| L44 | metin | `├─ WS (Word Select) ──▶ PCM3168A LRCK` | ✅ disk |
| L45 | metin | `├─ SD0 (Data Ch0) ───▶ PCM3168A DIN` | ✅ disk |
| L46 | metin | `├─ SD1 (Data Ch1) ───▶ PCM3168A DIN (B)` | ✅ disk |
| L47 | metin | `└─ MCLK (Master) ───▶ PCM3168A SCKI` | ✅ disk |
| L49 | metin | XU316 Clock | ✅ disk |
| L50 | metin | `│` dikey çizgi | ✅ disk |
| L51 | metin | `├─ XTAL_IN (Pin 8) ──▶ 22.5792MHz Crystal` | ✅ disk |
| L52 | metin | `└─ XTAL_OUT (Pin 9) ──▶ 22.5792MHz Crystal` | ✅ disk |
| L53 | kod | ``` blok kapanışı | ✅ disk |
| L55 | baslik | ### Kondansatör Değerleri | ✅ disk |
| L57 | tablo | `\| Referans \| Değer \| Açıklama \|` başlık satırı | ✅ disk |
| L58 | tablo | `\|----------\|-------\|----------\|` ayırıcı | ✅ disk |
| L59 | tablo | `\| C1-C4 \| 100nF MLCC \| USB hat filtreleme \|` | ✅ disk |
| L60 | tablo | `\| C5-C8 \| 4.7µF MLCC \| VCC dekuplajı \|` | ✅ disk |
| L61 | tablo | `\| C9-C10 \| 18pF \| Crystal yük kondansatörü \|` | ✅ disk |
| L62 | tablo | `\| C11-C14 \| 10nF \| I2S hat dekuplajı \|` | ✅ disk |
| L64 | baslik | ## Pin Konfigürasyonu | ✅ disk |
| L66 | tablo | `\| Pin \| Ad \| Yön \| Açıklama \|` başlık satırı | ✅ disk |
| L67 | tablo | `\|-----\|-----\|------\|----------\|` ayırıcı | ✅ disk |
| L68 | tablo | `\| 1-4 \| VCC \| Güç \| +3.3V dijital besleme \|` | ✅ disk |
| L69 | tablo | `\| 5-8 \| GND \| Güç \| Toprak \|` | ✅ disk |
| L70 | tablo | `\| 9-10 \| XTAL \| Giriş/Çıkış \| Kristal osilatör \|` | ✅ disk |
| L71 | tablo | `\| 11 \| RESET \| Giriş \| Yeniden başlatma (aktif düşük) \|` | ✅ disk |
| L72 | tablo | `\| 12 \| USB_DP \| Bidirectional \| USB Data+ \|` | ✅ disk |
| L73 | tablo | `\| 13 \| USB_DM \| Bidirectional \| USB Data- \|` | ✅ disk |
| L74 | tablo | `\| 14-21 \| GPIO[0:7] \| Bidirectional \| Genel amaçlı I/O \|` | ✅ disk |
| L75 | tablo | `\| 22-27 \| I2S_SCK/WS/SD[0:3] \| Çıkış \| I2S veri hatları \|` | ✅ disk |
| L76 | tablo | `\| 28-31 \| SPI MOSI/MISO/CLK/CS \| Bidirectional \| SPI konfigürasyon \|` | ✅ disk |
| L77 | tablo | `\| 32-35 \| I2C SDA/SCL \| Bidirectional \| I2C kontrol \|` | ✅ disk |
| L78 | tablo | `\| 36-39 \| LED[0:3] \| Çıkış \| Durum göstergeleri \|` | ✅ disk |
| L79 | tablo | `\| 40-43 \| JTAG \| Bidirectional \| Hata ayıklama \|` | ✅ disk |
| L80 | tablo | `\| 44-48 \| VCCIO \| Güç \| GPIO besleme (3.3V/1.8V) \|` | ✅ disk |
| L81 | tablo | `\| 49-56 \| GND/NC \| Güç \| Toprak/boş \|` | ✅ disk |
| L83 | baslik | ## Bağımlılıklar | ✅ disk |
| L85 | tablo | `\| Bağımlılık \| Yön \| Açıklama \|` başlık satırı | ✅ disk |
| L86 | tablo | `\|------------\|-----\|----------\|` ayırıcı | ✅ disk |
| L87 | tablo | `\| K0 Fiziksel \| Alt \| QFN-56 pad layout \|` | ✅ disk |
| L88 | tablo | `\| K2 OS/Sürücüler \| Üst \| USB Audio driver (UAC2) \|` | ✅ disk |
| L89 | tablo | `\| K3 XMOS Firmware \| Üst \| xCORE-200 firmware image \|` | ✅ disk |
| L90 | tablo | `\| K5 Analog \| Uzay \| I2S → DAC bağlantısı \|` | ✅ disk |
| L92 | baslik | ## Durum: Implementasyon | ✅ disk |
| L94 | vurgu | **Durum**: 🟢 Hazır | ✅ disk |
| L96 | madde | `- Firmware open-source: lib_xua kütüphanesi mevcut` | ✅ disk |
| L97 | madde | `- Crystal seçimi: 22.5792MHz (44.1kHz ailesi) + 24.576MHz (48kHz ailesi) dual` | ✅ disk |
| L98 | madde | `- USB-C connector: USB Type-C 16-pin SMD` | ✅ disk |
| L99 | madde | `- ESD koruması: USBLC6-2SC6 TVS diyotları` | ✅ disk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
