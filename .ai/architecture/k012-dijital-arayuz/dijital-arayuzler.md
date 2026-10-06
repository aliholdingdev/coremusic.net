---
title: "K012 Dijital Arayüzler (I2S · TDM)"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K012 — Dijital Arayüzler (I2S · TDM)

> **K numarası:** K012 · **Klasör:** `k012-dijital-arayuz` · **Dosya:** `dijital-arayuzler`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `data-engineer` (ikincil)
> **Klasör amacı:** I2S/TDM saat–veri hattının, USB-audio veri yolunun ve fiziksel konnektör/pin taşıyıcılarının arayüz tanımlarını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **Dijital Arayüzler (I2S · TDM)** konusunu ele alır. Kapsamı: I2S/TDM sinyal seti, saat hiyerarşisi (MCLK/BCLK/WS), master/slave rolleri ve hattaki filtreler.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Host (USB) ]
            │
            ▼
      [ USB-audio (isochronous) ]
            │
            ▼
      [ XMOS köprü ]
            │
            ▼
      [ I2S / TDM (BCLK · WS · MCLK) ]
            │
            ▼
      [ DAC / ADC ]
            │
            ▼
      [ Konnektör / pinout ]
```

**Akış notları:**

1. **Host (USB)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **USB-audio (isochronous)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **XMOS köprü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **I2S / TDM (BCLK · WS · MCLK)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **DAC / ADC** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **Konnektör / pinout** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 187 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | 191 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` | 194 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/i2s-interface.md`

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

### 4.2 · `k1-donanim/i2s-interface.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

### 4.3 · `k1-donanim/i2s-interface.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

### 4.4 · `k1-donanim/usb-audio.md`

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

### 4.5 · `k1-donanim/usb-audio.md`

| Parametre | Değer |
|-----------|-------|
| Frame Size | 1ms (USB 2.0) |
| Packet Size | 44.1 samples @ 44.1kHz |
| Max Packet Size | 1024 bytes (High-Speed) |
| Bandwidth | 480Mbps / 8 = 60MB/s |
| Audio Bandwidth | 8ch × 32bit × 192kHz = 49.152Mbps |
| Utilization | 81.9% (max) |

### 4.6 · `k1-donanim/usb-audio.md`

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

### 4.7 · `k1-donanim/usb-audio.md`

| OS | Driver | Status |
|----|--------|--------|
| Windows 10/11 | UAC2 class-compliant | ✅ Native |
| macOS 10.9+ | UAC2 class-compliant | ✅ Native |
| Linux 4.x+ | ALSA UAC2 | ✅ Native |
| Android 5.0+ | UAC2 class-compliant | ✅ Native |
| iOS 11.0+ | UAC2 class-compliant | ✅ Native |

### 4.8 · `k1-donanim/konnektorler.md`

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

### 4.9 · `k1-donanim/konnektorler.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/i2s-interface.md` | H1 | I2S Bus Protocol |
| 2 | `k1-donanim/i2s-interface.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/i2s-interface.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/i2s-interface.md` | H2 | I2S Sinyalleri |
| 5 | `k1-donanim/i2s-interface.md` | H2 | I2S Timing Diagram |
| 6 | `k1-donanim/i2s-interface.md` | H2 | Master/Slave Mode |
| 7 | `k1-donanim/i2s-interface.md` | H3 | XMOS as Master |
| 8 | `k1-donanim/i2s-interface.md` | H3 | DAC/ADC as Slave |
| 9 | `k1-donanim/i2s-interface.md` | H2 | Multi-Channel Configuration |
| 10 | `k1-donanim/i2s-interface.md` | H3 | 8-Channel TDM (Time Division Multiplexing) |
| 11 | `k1-donanim/i2s-interface.md` | H2 | Impedans ve Drive |
| 12 | `k1-donanim/i2s-interface.md` | H3 | Source Impedans |
| 13 | `k1-donanim/i2s-interface.md` | H3 | Load Impedans |
| 14 | `k1-donanim/i2s-interface.md` | H2 | Bileşen Değerleri |
| 15 | `k1-donanim/i2s-interface.md` | H2 | Bağımlılıklar |
| 16 | `k1-donanim/i2s-interface.md` | H2 | Durum: Implementasyon |
| 17 | `k1-donanim/usb-audio.md` | H1 | USB Audio Class 2.0 |
| 18 | `k1-donanim/usb-audio.md` | H2 | Genel Bakış |
| 19 | `k1-donanim/usb-audio.md` | H2 | Teknik Spesifikasyonlar |
| 20 | `k1-donanim/usb-audio.md` | H2 | USB Descriptor Hierarchy |
| 21 | `k1-donanim/usb-audio.md` | H2 | Isochronous Transfer |
| 22 | `k1-donanim/usb-audio.md` | H3 | Neden Isochronous? |
| 23 | `k1-donanim/usb-audio.md` | H3 | Transfer Parameters |
| 24 | `k1-donanim/usb-audio.md` | H2 | Sample Rate Support |
| 25 | `k1-donanim/usb-audio.md` | H2 | DSD (DoP) Support |
| 26 | `k1-donanim/usb-audio.md` | H3 | DoP (DSD over PCM) |
| 27 | `k1-donanim/usb-audio.md` | H2 | USB-C Connection |
| 28 | `k1-donanim/usb-audio.md` | H3 | Pin Mapping |
| 29 | `k1-donanim/usb-audio.md` | H2 | Driver Status |
| 30 | `k1-donanim/usb-audio.md` | H2 | Bileşen Değerleri |
| 31 | `k1-donanim/usb-audio.md` | H2 | Bağımlılıklar |
| 32 | `k1-donanim/usb-audio.md` | H2 | Durum: Implementasyon |
| 33 | `k1-donanim/konnektorler.md` | H1 | Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC |
| 34 | `k1-donanim/konnektorler.md` | H2 | Genel Bakış |
| 35 | `k1-donanim/konnektorler.md` | H2 | Teknik Spesifikasyonlar |
| 36 | `k1-donanim/konnektorler.md` | H2 | XLR Konnektörü |
| 37 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 38 | `k1-donanim/konnektorler.md` | H3 | XLR Devre Bağlantısı |
| 39 | `k1-donanim/konnektorler.md` | H2 | RCA Konnektörü |
| 40 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 41 | `k1-donanim/konnektorler.md` | H2 | USB-C Konnektörü |
| 42 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 43 | `k1-donanim/konnektorler.md` | H2 | Optical (Toslink) |
| 44 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 45 | `k1-donanim/konnektorler.md` | H2 | HDMI ARC |
| 46 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 47 | `k1-donanim/konnektorler.md` | H2 | Binding Posts (Hoparlör Çıkışları) |
| 48 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 49 | `k1-donanim/konnektorler.md` | H2 | Panel Düzeni |
| 50 | `k1-donanim/konnektorler.md` | H2 | Bağımlılıklar |
| 51 | `k1-donanim/konnektorler.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md`


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


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md`


### Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC

#### Genel Bakış

COREMUSIC konnektör sistemi, çeşitli ses ve kontrol arabirimleri için endüstri standartlarında bağlantı noktaları sağlar. Balanced XLR, unbalanced RCA, dijital USB-C, Optical (Toslink) ve HDMI ARC konnektörleri kullanılır.

#### Teknik Spesifikasyonlar

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

#### XLR Konnektörü

##### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    XLR Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ 1 │       │  Pin 1: Ground (Shield)
    │    /│   │\      │  Pin 2: Hot (+, Non-inverting)
    │   / │ 2 │ \     │  Pin 3: Cold (-, Inverting)
    │  /  │   │  \    │
    │ / 3 │   │   \   │
    │/    └───┘    \  │
    │               │ │
    └─────────────────┘
```

##### XLR Devre Bağlantısı

```
XLR Input (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──▶ 100Ω ──▶ ADC AINL+ (PCM3168A)
     └─ Pin 3 (Cold) ─▶ 100Ω ──▶ ADC AINL- (PCM3168A)

XLR Output (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──◀ DAC OUTL+ (AK4458)
     └─ Pin 3 (Cold) ─◀ DAC OUTL- (AK4458)
```

#### RCA Konnektörü

##### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    RCA Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ + │       │  Center Pin: Signal (+)
    │     └───┘       │  Outer Ring: Ground (Shield)
    │    ╱     ╲      │
    │   ╱       ╲     │
    │  ╱─────────╲    │
    │                 │
    └─────────────────┘
```

#### USB-C Konnektörü

##### Pin Konfigürasyonu

```
USB-C 16-Pin Connector
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V (VBUS)
     ├─ GND (A1, A12, B1, B12) ──▶ DGND
     ├─ D+ (A6) ──▶ XU316 USB_DP
     ├─ D- (A7) ──▶ XU316 USB_DM
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink)
     ├─ SBU1 (A8) ──▶ NC (not used)
     └─ SBU2 (B8) ──▶ NC (not used)
```

#### Optical (Toslink)

##### Pin Konfigürasyonu

```
Toslink Connector
     │
     ├─ TX ──▶ LED Transmitter (TOS1103)
     ├─ RX ──▶ Photodiode Receiver
     └─ GND ──▶ Shield

Not: Optik izolasyon sağlar, ground loop engeller.
```

#### HDMI ARC

##### Pin Konfigürasyonu

```
HDMI ARC (Audio Return Channel)
     │
     ├─ TMDS Data0+ ──▶ LVDS Receiver
     ├─ TMDS Data0- ──▶ LVDS Receiver
     ├─ TMDS Data1+ ──▶ LVDS Receiver
     ├─ TMDS Data1- ──▶ LVDS Receiver
     ├─ TMDS Data2+ ──▶ LVDS Receiver
     ├─ TMDS Data2- ──▶ LVDS Receiver
     ├─ TMDS Clock+ ──▶ LVDS Receiver
     ├─ TMDS Clock- ──▶ LVDS Receiver
     ├─ CEC ──▶ CEC Controller (I2C)
     ├─ SCL ──▶ EDID I2C Clock
     ├─ SDA ──▶ EDID I2C Data
     ├─ +5V ──▶ Power
     ├─ HPD ──▶ Hot Plug Detect
     └─ GND ──▶ Shield

ARC Modu: TV'den ses sinyalini HDMI kablo üzerinden alır.
```

#### Binding Posts (Hoparlör Çıkışları)

##### Pin Konfigürasyonu

```
Binding Post (4mm Banana Compatible)
     │
     ├─ Red (+) ──▶ Amplifikatör Output (+)
     ├─ Black (-) ──▶ Amplifikatör Output (-)
     └─ Banana Plug / Spade / Bare Wire uyumlu

Torque: 1.5 Nm (tightening)
Maks. Kablo Kesiti: 4 AWG (21.2mm²)
```

#### Panel Düzeni

```
┌─────────────────────────────────────────────────────────┐
│                     ARKA PANEL                          │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 1│ │XLR 2│ │XLR 3│ │XLR 4│ │XLR 5│ │XLR 6│      │
│  │FL   │ │FR   │ │C    │ │SL   │ │SR   │ │SBL  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 7│ │RCA 1│ │RCA 2│ │USB-C│ │TosLk│ │HDMI │      │
│  │SBR  │ │L    │ │R    │ │In   │ │In   │ │ARC  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS                       │    │
│  │  (+FL) (-FL) (+FR) (-FR) (+C) (-C) (+SL) (-SL) │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS (Devam)               │    │
│  │  (+SR) (-SR) (+SBL)(-SBL)(+SBR)(-SBR)(+LFE)(-LFE)│   │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐                               │
│  │IEC │ │Fuse │ │SWITCH│                               │
│  │Inlet│ │     │ │     │                               │
│  └─────┘ └─────┘ └─────┘                               │
└─────────────────────────────────────────────────────────┘
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

#### Durum: Implementasyon

## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | MCLK 256fs değerleri kaynakta var; gerçek ölçüm/trace yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Hattaki seri direnç/EMI filtresi değerleri doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. WS/BCLK hattında gecikme → kanal swap'ı (kaynak: `i2s-interface`).
2. MCLK ailesi karıştırıldığında örnekleme oranının yanlış olması (kaynak: `dac-adc-zinciri` §Clock).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k007-ak4458-xmos/index]]` | ↑ | Köprü ve DAC bu arayüzü kullanır | `.ai/architecture/k007-ak4458-xmos/index.md` |
| `[[../k006-dac-adc-zinciri/index]]` | ↑ | Zincirin saat şartları | `.ai/architecture/k006-dac-adc-zinciri/index.md` |
| `[[../k013-pcb-hoparlor/index]]` | ↓ | Fiziksel taşıyıcı/PCB yerleşimi | `.ai/architecture/k013-pcb-hoparlor/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↓ | Sürücü tarafı paket/buffer arayüzü | `.ai/architecture/k014-surucu-yigin/index.md` |

Yerel dosyalar:

- `[[dijital-arayuzler]]` — Dijital Arayüzler (I2S · TDM)
- `[[usb-audio-ve-konnektor-arayuzleri]]` — USB-Audio ve Konnektör Arayüzleri

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K012 · Dijital Arayüzler (I2S · TDM) — SSOT: `.ai/architecture/k012-dijital-arayuz/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
