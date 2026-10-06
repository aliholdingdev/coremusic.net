---
title: "I2S ve TDM Arayüzü - k012-dijital-arayuz"
type: architecture
category: mimari
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı"
updated: 2026-10-06
---

# I2S / TDM Arayüzü

- **Amaç:** I2S bus protokolu, timing, master/slave rol dağılımı, 8-kanal TDM multiplexing, impedans/drive kuralları ve PCM3168A I2S konfigürasyonu.
- **Persona:** `audio-hardware-engineer`
- **Wiki-link:** [[index.md]]
- **Çapraz referanslar:** [[../k011-guc-koruma-termal/index]] · [[../k013-pcb-hoparlor/index]]

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | tüm başlıklar (H1 + ##/###) | L8-L186 | verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | 3. PCM3168A DAC Detayı (3.1-3.3) | L94-L135 | verbatim |

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md - L8-L186

# I2S Bus Protocol

## Genel Bakış

I2S (Inter-IC Sound), dijital ses verilerini IC'ler arasında aktarmak için geliştirilmiş seri bir haberleşme protokolüdür. Philips Standard formatında çalışır. XMOS XU316'dan DAC (AK4458) ve ADC'ye (PCM3168A) yüksek çözünürlüklü ses verisi taşır.

## Teknik Spesifikasyonlar

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

## I2S Sinyalleri

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

## I2S Timing Diagram

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

## Master/Slave Mode

### XMOS as Master

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

### DAC/ADC as Slave

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

## Multi-Channel Configuration

### 8-Channel TDM (Time Division Multiplexing)

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

## Impedans ve Drive

### Source Impedans

```
XMOS Output Stage:
- Output impedance: 50Ω (typical)
- Drive capability: ±8mA
- Rise/Fall time: < 5ns

Trace impedance:
- Characteristic impedance: 90Ω differential
- Termination: 100Ω parallel (optional)
```

### Load Impedans

```
DAC/ADC Input:
- Input impedance: > 100kΩ (digital)
- Input capacitance: 5pF (typical)
- No termination required (high-Z)
```

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

## Durum: Implementasyon

**Durum**: 🟢 Hazır

- Protocol: Philips I2S standard
- Timing: All specifications verified in simulation
- PCB routing: Length-matched traces (±1mm)
- Ferrite beads: Selected and verified
- Crystal: 22.5792MHz + 24.576MHz dual
- Multi-channel: TDM mode for 8-channel support

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md - L94-L135

## 3. PCM3168A DAC Detayı

### 3.1 Pin Out

```
PCM3168A Pin Configuration:
  VDD1: +3.3V (Digital)
  VDD2: +5V (Analog)
  VSS:  -5V (Analog)
  AGND: Analog Ground
  DGND: Digital Ground

  I2S Input:
    BCK:  Bit Clock (64fs)
    LRCK: Left/Right Clock (fs)
    DIN:  Data In
    SCKI: System Clock (256fs or 512fs)

  Analog Output:
    OUTL1-OUTL3: Left channels (3 output)
    OUTR1-OUTR3: Right channels (3 output)
```

### 3.2 I2S Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| Sample Rate | 48kHz (default) |
| Bit Depth | 24-bit |
| I2S Mode | Standard I2S |
| System Clock | 256fs = 12.288MHz |
| BCK | 64fs = 3.072MHz |
| LRCK | 48kHz |

### 3.3 Analogy Output Devresi

```
PCM3168A OUTL1 → I/V Resistor (1kΩ) → Low-Pass Filter (20kHz) → Differential Driver → Amplifier Input
```

---


