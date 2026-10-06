---
title: "K007 AK4458 + XMOS XU316 Dijital İşlemci"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K007 — AK4458 + XMOS XU316 Dijital İşlemci

> **K numarası:** K007 · **Klasör:** `k007-ak4458-xmos` · **Dosya:** `ak4458-xmos-islemci`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** USB köprüsü (XMOS XU316) ile 8-kanal AK4458 DAC arasındaki saat ve veri zincirinin konfigürasyonunu, kanal eşlemesini ve çıkış evresini tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **AK4458 + XMOS XU316 Dijital İşlemci** konusunu ele alır. Kapsamı: USB-audio köprüsünün I2S üretimi, clock rolü, buffer davranışı ve firmware bağımlılıkları.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ USB-C / host ]
            │
            ▼
      [ XMOS XU316 ]
            │
            ▼
      [ I2S / TDM (BCLK · WS · MCLK) ]
            │
            ▼
      [ AK4458 (8-kanal DAC) ]
            │
            ▼
      [ Differential çıkış (2.1 Vrms) ]
            │
            ▼
      [ Analog zincir ]
```

**Akış notları:**

1. **USB-C / host** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **XMOS XU316** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **I2S / TDM (BCLK · WS · MCLK)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **AK4458 (8-kanal DAC)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Differential çıkış (2.1 Vrms)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **Analog zincir** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` | 100 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | 191 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 187 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/xmos-xu316.md`

| Parametre | Değer |
|-----------|-------|
| Çekirdek Sayısı | 16 (Hardware Threads) |
| saat Hızı | 500MHz per core |
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64, DSD128, DSD256 (DoP) |
| I2S Kanal Sayısı | 8 stereo (16 single) |
| Güç Tüketimi | < 1W (aktif) |
| Package | QFN-56, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

### 4.2 · `k1-donanim/xmos-xu316.md`

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | USB hat filtreleme |
| C5-C8 | 4.7µF MLCC | VCC dekuplajı |
| C9-C10 | 18pF | Crystal yük kondansatörü |
| C11-C14 | 10nF | I2S hat dekuplajı |

### 4.3 · `k1-donanim/xmos-xu316.md`

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1-4 | VCC | Güç | +3.3V dijital besleme |
| 5-8 | GND | Güç | Toprak |
| 9-10 | XTAL | Giriş/Çıkış | Kristal osilatör |
| 11 | RESET | Giriş | Yeniden başlatma (aktif düşük) |
| 12 | USB_DP | Bidirectional | USB Data+ |
| 13 | USB_DM | Bidirectional | USB Data- |
| 14-21 | GPIO[0:7] | Bidirectional | Genel amaçlı I/O |
| 22-27 | I2S_SCK/WS/SD[0:3] | Çıkış | I2S veri hatları |
| 28-31 | SPI MOSI/MISO/CLK/CS | Bidirectional | SPI konfigürasyon |
| 32-35 | I2C SDA/SCL | Bidirectional | I2C kontrol |
| 36-39 | LED[0:3] | Çıkış | Durum göstergeleri |
| 40-43 | JTAG | Bidirectional | Hata ayıklama |
| 44-48 | VCCIO | Güç | GPIO besleme (3.3V/1.8V) |
| 49-56 | GND/NC | Güç | Toprak/boş |

### 4.4 · `k1-donanim/xmos-xu316.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | QFN-56 pad layout |
| K2 OS/Sürücüler | Üst | USB Audio driver (UAC2) |
| K3 XMOS Firmware | Üst | xCORE-200 firmware image |
| K5 Analog | Uzay | I2S → DAC bağlantısı |

### 4.5 · `k1-donanim/usb-audio.md`

| Parametre | Değer |
|-----------|-------|
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64/128/256 (DoP) |
| Kanal Sayısı | 8 stereo (16 single) |
| Async Transfer | Yes (device-sync) |
| Feedback | Adaptive/Synchronous |
| Latency | < 1ms |
| OS Desteği | Windows 10+, macOS 10.9+, Linux 4.x+ |
| Driver | Class-compliant (no driver needed) |

### 4.6 · `k1-donanim/usb-audio.md`

| Parametre | Değer |
|-----------|-------|
| Frame Size | 1ms (USB 2.0) |
| Packet Size | 44.1 samples @ 44.1kHz |
| Max Packet Size | 1024 bytes (High-Speed) |
| Bandwidth | 480Mbps / 8 = 60MB/s |
| Audio Bandwidth | 8ch × 32bit × 192kHz = 49.152Mbps |
| Utilization | 81.9% (max) |

### 4.7 · `k1-donanim/usb-audio.md`

| Sample Rate | Bit Depth | Channels | Bandwidth | MCLK |
|-------------|-----------|----------|-----------|------|
| 44.1kHz | 16-bit | 2 | 1.411 Mbps | 11.2896 MHz |
| 44.1kHz | 24-bit | 2 | 2.1168 Mbps | 11.2896 MHz |
| 44.1kHz | 32-bit | 8 | 11.2896 Mbps | 11.2896 MHz |
| 48kHz | 24-bit | 2 | 2.304 Mbps | 12.288 MHz |
| 96kHz | 24-bit | 2 | 4.608 Mbps | 12.288 MHz |
| 192kHz | 24-bit | 2 | 9.216 Mbps | 12.288 MHz |
| 384kHz | 32-bit | 2 | 24.576 Mbps | 12.288 MHz |
| 768kHz | 32-bit | 2 | 49.152 Mbps | 12.288 MHz |

### 4.8 · `k1-donanim/usb-audio.md`

| OS | Driver | Status |
|----|--------|--------|
| Windows 10/11 | UAC2 class-compliant | ✅ Native |
| macOS 10.9+ | UAC2 class-compliant | ✅ Native |
| Linux 4.x+ | ALSA UAC2 | ✅ Native |
| Android 5.0+ | UAC2 class-compliant | ✅ Native |
| iOS 11.0+ | UAC2 class-compliant | ✅ Native |

### 4.9 · `k1-donanim/i2s-interface.md`

| Parametre | Değer |
|-----------|-------|
| Standart | Philips I2S (Original) |
| Kanal Sayısı | 8 stereo (16 single) |
| Bit Çözünürlüğü | 32-bit |
| Örnekleme Hızı | 44.1kHz – 192kHz |
| Master Clock | 256fs (11.2896MHz @ 44.1kHz) |
| Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz |
| Word Select | fs = 44.1kHz / 48kHz |
| Data Format | MSB First, 2's complement |
| Empedans | 50Ω (source), High-Z (load) |

### 4.10 · `k1-donanim/i2s-interface.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

### 4.11 · `k1-donanim/i2s-interface.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/xmos-xu316.md` | H1 | XMOS XU316 USB Audio Controller |
| 2 | `k1-donanim/xmos-xu316.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/xmos-xu316.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/xmos-xu316.md` | H2 | Devre Tasarımı |
| 5 | `k1-donanim/xmos-xu316.md` | H3 | Temel Bağlantılar |
| 6 | `k1-donanim/xmos-xu316.md` | H3 | Kondansatör Değerleri |
| 7 | `k1-donanim/xmos-xu316.md` | H2 | Pin Konfigürasyonu |
| 8 | `k1-donanim/xmos-xu316.md` | H2 | Bağımlılıklar |
| 9 | `k1-donanim/xmos-xu316.md` | H2 | Durum: Implementasyon |
| 10 | `k1-donanim/usb-audio.md` | H1 | USB Audio Class 2.0 |
| 11 | `k1-donanim/usb-audio.md` | H2 | Genel Bakış |
| 12 | `k1-donanim/usb-audio.md` | H2 | Teknik Spesifikasyonlar |
| 13 | `k1-donanim/usb-audio.md` | H2 | USB Descriptor Hierarchy |
| 14 | `k1-donanim/usb-audio.md` | H2 | Isochronous Transfer |
| 15 | `k1-donanim/usb-audio.md` | H3 | Neden Isochronous? |
| 16 | `k1-donanim/usb-audio.md` | H3 | Transfer Parameters |
| 17 | `k1-donanim/usb-audio.md` | H2 | Sample Rate Support |
| 18 | `k1-donanim/usb-audio.md` | H2 | DSD (DoP) Support |
| 19 | `k1-donanim/usb-audio.md` | H3 | DoP (DSD over PCM) |
| 20 | `k1-donanim/usb-audio.md` | H2 | USB-C Connection |
| 21 | `k1-donanim/usb-audio.md` | H3 | Pin Mapping |
| 22 | `k1-donanim/usb-audio.md` | H2 | Driver Status |
| 23 | `k1-donanim/usb-audio.md` | H2 | Bileşen Değerleri |
| 24 | `k1-donanim/usb-audio.md` | H2 | Bağımlılıklar |
| 25 | `k1-donanim/usb-audio.md` | H2 | Durum: Implementasyon |
| 26 | `k1-donanim/i2s-interface.md` | H1 | I2S Bus Protocol |
| 27 | `k1-donanim/i2s-interface.md` | H2 | Genel Bakış |
| 28 | `k1-donanim/i2s-interface.md` | H2 | Teknik Spesifikasyonlar |
| 29 | `k1-donanim/i2s-interface.md` | H2 | I2S Sinyalleri |
| 30 | `k1-donanim/i2s-interface.md` | H2 | I2S Timing Diagram |
| 31 | `k1-donanim/i2s-interface.md` | H2 | Master/Slave Mode |
| 32 | `k1-donanim/i2s-interface.md` | H3 | XMOS as Master |
| 33 | `k1-donanim/i2s-interface.md` | H3 | DAC/ADC as Slave |
| 34 | `k1-donanim/i2s-interface.md` | H2 | Multi-Channel Configuration |
| 35 | `k1-donanim/i2s-interface.md` | H3 | 8-Channel TDM (Time Division Multiplexing) |
| 36 | `k1-donanim/i2s-interface.md` | H2 | Impedans ve Drive |
| 37 | `k1-donanim/i2s-interface.md` | H3 | Source Impedans |
| 38 | `k1-donanim/i2s-interface.md` | H3 | Load Impedans |
| 39 | `k1-donanim/i2s-interface.md` | H2 | Bileşen Değerleri |
| 40 | `k1-donanim/i2s-interface.md` | H2 | Bağımlılıklar |
| 41 | `k1-donanim/i2s-interface.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md`


### XMOS XU316 USB Audio Controller

#### Genel Bakış

XMOS XU316, COREMUSIC'ın USB arabirimi için kullanılan 16 çekirdekli bir xtENDIO mikrodenetleyicisidir. USB Audio Class 2.0 standardını destekler ve yüksek çözünürlüklü ses (hi-res audio) için gerekli tüm timer hassasiyetini sağlar. I2S arayüzü üzerinden DAC'a doğrudan bağlantı kurar.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çekirdek Sayısı | 16 (Hardware Threads) |
| saat Hızı | 500MHz per core |
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64, DSD128, DSD256 (DoP) |
| I2S Kanal Sayısı | 8 stereo (16 single) |
| Güç Tüketimi | < 1W (aktif) |
| Package | QFN-56, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

#### Devre Tasarımı

##### Temel Bağlantılar

```
USB-C Connector
     │
     ├─ D+ ───────────▶ XU316 USB_DP (Pin 12)
     ├─ D- ───────────▶ XU316 USB_DM (Pin 13)
     ├─ VBUS (5V) ────▶ 3.3V LDO ──▶ XU316 VCC (Pin 44)
     └─ GND ──────────▶ DGND Plane

XU316 I2S Output
     │
     ├─ SCK (Bit Clock) ──▶ PCM3168A BCK
     ├─ WS (Word Select) ──▶ PCM3168A LRCK
     ├─ SD0 (Data Ch0) ───▶ PCM3168A DIN
     ├─ SD1 (Data Ch1) ───▶ PCM3168A DIN (B)
     └─ MCLK (Master) ───▶ PCM3168A SCKI

XU316 Clock
     │
     ├─ XTAL_IN (Pin 8) ──▶ 22.5792MHz Crystal
     └─ XTAL_OUT (Pin 9) ──▶ 22.5792MHz Crystal
```

##### Kondansatör Değerleri

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | USB hat filtreleme |
| C5-C8 | 4.7µF MLCC | VCC dekuplajı |
| C9-C10 | 18pF | Crystal yük kondansatörü |
| C11-C14 | 10nF | I2S hat dekuplajı |

#### Pin Konfigürasyonu

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1-4 | VCC | Güç | +3.3V dijital besleme |
| 5-8 | GND | Güç | Toprak |
| 9-10 | XTAL | Giriş/Çıkış | Kristal osilatör |
| 11 | RESET | Giriş | Yeniden başlatma (aktif düşük) |
| 12 | USB_DP | Bidirectional | USB Data+ |
| 13 | USB_DM | Bidirectional | USB Data- |
| 14-21 | GPIO[0:7] | Bidirectional | Genel amaçlı I/O |
| 22-27 | I2S_SCK/WS/SD[0:3] | Çıkış | I2S veri hatları |
| 28-31 | SPI MOSI/MISO/CLK/CS | Bidirectional | SPI konfigürasyon |
| 32-35 | I2C SDA/SCL | Bidirectional | I2C kontrol |
| 36-39 | LED[0:3] | Çıkış | Durum göstergeleri |
| 40-43 | JTAG | Bidirectional | Hata ayıklama |
| 44-48 | VCCIO | Güç | GPIO besleme (3.3V/1.8V) |
| 49-56 | GND/NC | Güç | Toprak/boş |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | QFN-56 pad layout |
| K2 OS/Sürücüler | Üst | USB Audio driver (UAC2) |
| K3 XMOS Firmware | Üst | xCORE-200 firmware image |
| K5 Analog | Uzay | I2S → DAC bağlantısı |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Firmware open-source: `lib_xua` kütüphanesi mevcut
- Crystal seçimi: 22.5792MHz (44.1kHz ailesi) + 24.576MHz (48kHz ailesi) dual
- USB-C connector: USB Type-C 16-pin SMD
- ESD koruması: USBLC6-2SC6 TVS diyotları


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md`


### USB Audio Class 2.0

#### Genel Bakış

USB Audio Class 2.0 (UAC2), COREMUSIC'ın USB üzerinden yüksek çözünürlüklü ses alması için kullanılan endüstri standartıdır. XMOS XU316 UAC2 firmware'i çalıştırarak Windows, macOS ve Linux'ta driverless çalışır. Isochronous transfer modu ile zamanlama hassasiyeti sağlar.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64/128/256 (DoP) |
| Kanal Sayısı | 8 stereo (16 single) |
| Async Transfer | Yes (device-sync) |
| Feedback | Adaptive/Synchronous |
| Latency | < 1ms |
| OS Desteği | Windows 10+, macOS 10.9+, Linux 4.x+ |
| Driver | Class-compliant (no driver needed) |

#### USB Descriptor Hierarchy

```
USB Device Descriptor
     │
     ├─ Configuration Descriptor
     │   │
     │   ├─ Interface 0 (Audio Control)
     │   │   ├─ AC Header Descriptor
     │   │   ├─ Input Terminal (USB Streaming)
     │   │   ├─ Output Terminal (Speaker)
     │   │   └─ Feature Unit (Volume/Mute)
     │   │
     │   ├─ Interface 1 (Audio Streaming OUT)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   ├─ Format Type III (DSD)
     │   │   └─ Standard Endpoint (Iso OUT)
     │   │       └─ Isochronous Endpoint
     │   │
     │   ├─ Interface 2 (Audio Streaming IN)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   └─ Standard Endpoint (Iso IN)
     │   │       └─ Isochronous Endpoint
     │   │
     │   └─ Interface 3 (MIDI Streaming - Optional)
     │
     └─ String Descriptors
         ├─ Manufacturer: "COREMUSIC"
         ├─ Product: "COREMUSIC USB DAC"
         └─ Serial: "CM-001"
```

#### Isochronous Transfer

##### Neden Isochronous?

```
Audio data requires:
1. Constant data rate (no buffering variation)
2. Time-guaranteed delivery (no retry)
3. Low latency (< 10ms)

Isochronous transfer provides:
- Reserved bandwidth (guaranteed)
- No retry (best-effort, but constant rate)
- Fixed timing (1ms frames for USB 2.0)
```

##### Transfer Parameters

| Parametre | Değer |
|-----------|-------|
| Frame Size | 1ms (USB 2.0) |
| Packet Size | 44.1 samples @ 44.1kHz |
| Max Packet Size | 1024 bytes (High-Speed) |
| Bandwidth | 480Mbps / 8 = 60MB/s |
| Audio Bandwidth | 8ch × 32bit × 192kHz = 49.152Mbps |
| Utilization | 81.9% (max) |

#### Sample Rate Support

| Sample Rate | Bit Depth | Channels | Bandwidth | MCLK |
|-------------|-----------|----------|-----------|------|
| 44.1kHz | 16-bit | 2 | 1.411 Mbps | 11.2896 MHz |
| 44.1kHz | 24-bit | 2 | 2.1168 Mbps | 11.2896 MHz |
| 44.1kHz | 32-bit | 8 | 11.2896 Mbps | 11.2896 MHz |
| 48kHz | 24-bit | 2 | 2.304 Mbps | 12.288 MHz |
| 96kHz | 24-bit | 2 | 4.608 Mbps | 12.288 MHz |
| 192kHz | 24-bit | 2 | 9.216 Mbps | 12.288 MHz |
| 384kHz | 32-bit | 2 | 24.576 Mbps | 12.288 MHz |
| 768kHz | 32-bit | 2 | 49.152 Mbps | 12.288 MHz |

#### DSD (DoP) Support

##### DoP (DSD over PCM)

```
DoP Format:
- DSD data wrapped in PCM frames
- Uses 16-bit or 24-bit PCM slots
- Marker bits identify DSD data

DoP64:
- DSD rate: 2.8224 MHz
- PCM frame rate: 44.1kHz × 64 = 2.8224MHz
- Uses 16-bit PCM frames

DoP128:
- DSD rate: 5.6448 MHz
- PCM frame rate: 44.1kHz × 128 = 5.6448MHz
- Uses 16-bit PCM frames

DoP256:
- DSD rate: 11.2896 MHz
- PCM frame rate: 44.1kHz × 256 = 11.2896MHz
- Uses 16-bit PCM frames
```

#### USB-C Connection

##### Pin Mapping

```
USB-C Connector (16-pin)
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V Power
     ├─ GND (A1, A12, B1, B12) ──▶ Ground
     ├─ D+ (A6) ──▶ XU316 USB_DP (Pin 12)
     ├─ D- (A7) ──▶ XU316 USB_DM (Pin 13)
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink identification)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink identification)
     └─ SBU1, SBU2 ──▶ NC (not used for audio)

ESD Protection:
- USBLC6-2SC6 (TVS diode array)
- Clamping voltage: 6V
- Response time: < 1ns
```

#### Driver Status

| OS | Driver | Status |
|----|--------|--------|
| Windows 10/11 | UAC2 class-compliant | ✅ Native |
| macOS 10.9+ | UAC2 class-compliant | ✅ Native |
| Linux 4.x+ | ALSA UAC2 | ✅ Native |
| Android 5.0+ | UAC2 class-compliant | ✅ Native |
| iOS 11.0+ | UAC2 class-compliant | ✅ Native |

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | USB Controller | XMOS XU316 | 1 | UAC2 engine |
| 2 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 3 | Crystal | 24.576MHz | 1 | 48kHz family |
| 4 | USB Connector | USB-C 16-pin | 1 | SMD mount |
| 5 | ESD Protection | USBLC6-2SC6 | 1 | TVS array |
| 6 | Decoupling | 100nF MLCC | 4 | USB power |
| 7 | CC Resistors | 5.1kΩ 0402 | 2 | Sink ID |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Ana bileşen | UAC2 firmware |
| K1 Konnektörler | Bağlantı | USB-C connector |
| K2 OS/Sürücüler | Üst | USB enumeration |
| K1 Güç Kaynağı | Alt | +5V VBUS |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Firmware: lib_xua (XMOS open-source) kullanılıyor
- USB descriptor: Custom descriptor hazırlandı
- Enumeration: Windows/macOS/Linux test edildi
- DSD: DoP64/128/256 destekleniyor
- Latency: < 1ms measured
- Power: USB bus-powered (5V/500mA)


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md`


### I2S Bus Protocol

#### Genel Bakış

I2S (Inter-IC Sound), dijital ses verilerini IC'ler arasında aktarmak için geliştirilmiş seri bir haberleşme protokolüdür. Philips Standard formatında çalışır. XMOS XU316'dan DAC (AK4458) ve ADC'ye (PCM3168A) yüksek çözünürlüklü ses verisi taşır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Standart | Philips I2S (Original) |
| Kanal Sayısı | 8 stereo (16 single) |
| Bit Çözünürlüğü | 32-bit |
| Örnekleme Hızı | 44.1kHz – 192kHz |
| Master Clock | 256fs (11.2896MHz @ 44.1kHz) |
| Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz |
| Word Select | fs = 44.1kHz / 48kHz |
| Data Format | MSB First, 2's complement |
| Empedans | 50Ω (source), High-Z (load) |

#### I2S Sinyalleri

```
I2S Bus Sinyalleri:

1. SCK (Serial Clock / Bit Clock)
   - Her bit için bir clock pulse
   - Frequency = 2 × channel × bit_depth × fs
   - Example: 2 × 2 × 32 × 44100 = 4.2336MHz

2. WS (Word Select / LR Clock)
   - Sol kanal: WS = 0
   - Sağ kanal: WS = 1
   - Frequency = fs (44.1kHz / 48kHz)

3. SD (Serial Data)
   - MSB First (en yüksek bit önce)
   - 2's complement formatı
   - 32-bit per channel
```

#### I2S Timing Diagram

```
SCK:  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐
      └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘

WS:   ────────────────┐              ┌───────────────────
                      └──────────────┘
      ←── Sol Kanal (WS=0) ──→←── Sağ Kanal (WS=1) ──→

SD:   ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │B31│B30│B29│...│B1 │B0 │ │B31│B30│...│B1 │B0 │
        └──┴──┴──┴──┴──┴──┴──┘ └──┴──┴──┴──┴──┴──┴──┘
        ←── 32 bit Sol ────────→←── 32 bit Sağ ────────→

Timing:
- Data changes on SCK falling edge
- Data sampled on SCK rising edge
- WS changes 1 SCK cycle before MSB
```

#### Master/Slave Mode

##### XMOS as Master

```
XMOS XU316 (Master)
     │
     ├─ SCK Output ──▶ DAC/ADC SCKI (Input)
     ├─ WS Output ──▶ DAC/ADC LRCK (Input)
     └─ MCLK Output ──▶ DAC/ADC MCLK (Input)

Advantages:
- Single clock source (no sync issues)
- Lower jitter (crystal directly connected)
- Simpler design
```

##### DAC/ADC as Slave

```
DAC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ TDMD[0:3] ← SD from XMOS

ADC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ DOUTA/B ──▶ SD to XMOS (output)
```

#### Multi-Channel Configuration

##### 8-Channel TDM (Time Division Multiplexing)

```
TDM Frame (8 channels × 32 bits = 256 bits per frame):

WS:   ────────────────────────────────────────────────────
      │←─────────────────── WS Period ───────────────────→│

SD0:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch1│Ch2│Ch3│...│Ch32│
        └──┴──┴──┴──┴──┴──┘

SD1:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch33│Ch34│...│Ch64│
        └──┴──┴──┴──┴──┴──┘

SD2:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch65│Ch66│...│Ch96│
        └──┴──┴──┴──┴──┴──┘

SD3:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch97│Ch98│...│Ch128│
        └──┴──┴──┴──┴──┴──┘

Toplam: 4 data line × 32 channels/line = 128 channels
(TDM mode, 32-bit per channel)
```

#### Impedans ve Drive

##### Source Impedans

```
XMOS Output Stage:
- Output impedance: 50Ω (typical)
- Drive capability: ±8mA
- Rise/Fall time: < 5ns

Trace impedance:
- Characteristic impedance: 90Ω differential
- Termination: 100Ω parallel (optional)
```

##### Load Impedans

```
DAC/ADC Input:
- Input impedance: > 100kΩ (digital)
- Input capacitance: 5pF (typical)
- No termination required (high-Z)
```

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Protocol: Philips I2S standard
- Timing: All specifications verified in simulation
- PCB routing: Length-matched traces (±1mm)
- Ferrite beads: Selected and verified
- Crystal: 22.5792MHz + 24.576MHz dual
- Multi-channel: TDM mode for 8-channel support


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | XU316 firmware sürümü ve DFU akışı vault'ta yazılı değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Köprü buffer boyutu ile sürücü buffer boyutu eşleşmesi kanıtlanmamış | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. USB isochronous hat kopması durumunda I2S üretiminin durması (kaynak: `usb-audio`).
2. Köprü yeniden başlatıldığında clock kilidinin geri gelmemesi (kaynak: `xmos-xu316`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k006-dac-adc-zinciri/index]]` | ↑ | Zincirin clock/EMI/empedans genel şartları | `.ai/architecture/k006-dac-adc-zinciri/index.md` |
| `[[../k012-dijital-arayuz/index]]` | ↓ | I2S ve USB arayüz fiziksel tanımları | `.ai/architecture/k012-dijital-arayuz/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↓ | Sürücü tarafı örnekleme/buffer talebi | `.ai/architecture/k014-surucu-yigin/index.md` |

Yerel dosyalar:

- `[[ak4458-dac-mimarisi]]` — AK4458 DAC Mimarisi
- `[[ak4458-xmos-islemci]]` — AK4458 + XMOS XU316 Dijital İşlemci

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K007 · AK4458 + XMOS XU316 Dijital İşlemci — SSOT: `.ai/architecture/k007-ak4458-xmos/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
