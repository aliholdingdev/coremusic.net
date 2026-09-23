---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K1 Donanım Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K1: Donanım Layer

**Katman:** K1 (Donanım Altyapısı)
**Kapsam:** XMOS, DAC, Amplifikatör, Hoparlör, Güç kaynağı
**Sorumlu Agent:** Audio Hardware Engineer
**Bileşen Sayısı:** 120

---

## 1. Genel Bakış

K1 katmanı, CoreMusic'in fiziksel donanım bileşenlerini içerir. Bu katman, dijital sinyali analog sinyale dönüştüren ve hoparlörlere ileten tüm donanım alt sistemlerini kapsar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Bit-Perfect** | Sinyal zincirinde kayıp yok |
| **Low THD** | Toplam Harmonik Bozulma <0.005% |
| **High SNR** | Sinyal-Gürültü Oranı >100dB |
| **8.1 Surround** | 8 kanal + 1 LFE |
| **DC-Only** | Güç kaynağı DC Only |

---

## 2. Bileşen Haritası

### 2.1 USB Audio Interface

| Bileşen | Model | Özellik |
|---------|-------|---------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0, 32-bit |
| USB Interface | USB-C | 24-pin, USB 2.0/3.0 |
| Clock | 22.5792 MHz | 44.1kHz family |
| Clock | 24.576 MHz | 48kHz family |

### 2.2 DAC (Digital-to-Analog Converter)

| Bileşen | Model | Kanal | Bit | Sample Rate |
|---------|-------|-------|-----|-------------|
| Ana DAC | PCM3168A | 6-in/8-out | 24-bit | 192kHz |
| Opsiyonel DAC | AK4458 | 8-kanal | 32-bit | 768kHz |
| REDDEDİLMİŞ | PCM5122 | 2-kanal | 32-bit | — |

**⚠️ Uyarı:** PCM5122 8.1 surround için yetersizdir (ADR-038). Sadece 2 kanal destekler.

### 2.3 Amplifikatör

| Bileşen | Model | Topoloji | Güç | THD |
|---------|-------|----------|-----|-----|
| NPN Output | MJL21194 | Class AB Darlington | 50W/kanal | <0.005% |
| PNP Output | MJL21193 | Class AB Darlington | 50W/kanal | <0.005% |

### 2.4 Güç Kaynağı

| Bileşen | Model | Giriş | Çıkış | Verim |
|---------|-------|-------|-------|-------|
| Boost Converter | LM5122 | 22.2V (6S LiPo) | ±35V | %96 |
| Batarya | 6S LiPo | 22.2V nominal | — | — |
| DC Adapter | 19-24V | AC/DC | — | — |

### 2.5 Hoparlör Matrisi (8.1 Surround)

| Kanal | Hoparlör | Frekans | Konum |
|-------|----------|---------|-------|
| CH1 | Front Left | 20Hz-20kHz | Ön sol |
| CH2 | Front Right | 20Hz-20kHz | Ön sağ |
| CH3 | Center | 100Hz-8kHz | Merkez |
| CH4 | LFE (Sub) | 20Hz-120Hz | Subwoofer |
| CH5 | Surround Left | 100Hz-16kHz | Arka sol |
| CH6 | Surround Right | 100Hz-16kHz | Arka sağ |
| CH7 | Rear Left | 100Hz-16kHz | Arka sol |
| CH8 | Rear Right | 100Hz-16kHz | Arka sağ |

---

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

## 4. Class AB Amplifikatör Detayı

### 4.1 Tek Kanal Devre Şeması

```
                    +35V (PVDD)
                     │
                ┌────┴────┐
                │  Q15     │ MJL21194 (NPN Output)
                │  NPN     │
     Input ─────┤  Q16     ├──── Output → Hoparlör
     (Diff)     │  BD139   │
                │  VAS     │
                │  Q17     │ MJL21193 (PNP Output)
                │  PNP     │
                └────┬────┘
                     │
                    -35V (PVSS)

  Bias Network:
    Q1 (BC546B): Diferansiyel çift giriş
    Q2 (BC546B): Diferansiyel çift giriş
    Q5 (BC556B): Akım havuzu
    Q9 (KSC3503): VAS (Voltage Amplifier Stage)
    Q10 (BD139): Vbe çarpımı (bias spreader)
```

### 4.2 Bias Ayar Prosedürü

| Adım | İşlem | Değer |
|------|-------|-------|
| 1 | Güç kaynağı ayarla | ±35V DC |
| 2 | Multimetre çıkışa bağla | DC offset ölç |
| 3 | Bias potansiyometresi ayarla | 0V DC offset hedefle |
| 4 | Sıcaklık stabilizasyonu | 5-10 dk bekle |
| 5 | Son kontrol | <0.5V DC offset |

### 4.3 Termal Hesaplama

| Parametre | Değer |
|-----------|-------|
| Güç (kanal başına) | 50W @ 8Ω |
| Verimlilik | ~%65 (Class AB) |
| Isı (kanal başına) | ~17.5W |
| Toplam ısı (8 kanal) | ~140W |
| Heatsink gereksinimi | >140W/C° thermal resistance |
| Fan gereksinimi | 80mm PWM, >50 CFM |

---

## 5. XMOS XU316 Detayı

### 5.1 Blok Diyagramı

```
USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A
              │
              ├→ Clock Generator
              ├→ USB Audio Class 2.0
              ├→ DSP Processing
              └→ Control Interface
```

### 5.2 XMOS Kaynak Kullanımı

| Kaynak | Kullanım |
|--------|----------|
| Logical Cores | 8 (4 x 2 tile) |
| MIPS | ~2000 (toplam) |
| RAM | 512KB (tile 0+1) |
| Flash | 16MB (external) |
| USB PHY | High-speed 480Mbps |

---

## 6. PCB Tasarım Kuralları

| Parametre | Değer |
|-----------|-------|
| Layer | 6-layer stackup |
| Copper (top/bottom) | 2oz |
| Copper (inner) | 1oz |
| Finish | ENIG |
| Min trace | 4mil |
| Min via | 8mil drill, 16mil pad |
| USB Impedans | 90Ω differential |
| I2S Impedans | 50Ω single-ended |
| Ground | Star ground topology |
| Thermal | Thermal vias under power components |

### 6.1 Stackup

```
Layer 1: Signal (top) — Components, traces
Layer 2: Ground — Continuous ground plane
Layer 3: Signal — I2S, control signals
Layer 4: Power — +35V, -35V, +3.3V, +5V
Layer 5: Ground — Continuous ground plane
Layer 6: Signal (bottom) — Components, traces
```

---

## 7. BOM Maliyet Analizi

| Kategori | Bileşen Sayısı | Toplam Maliyet |
|----------|---------------|---------------|
| USB Audio (XMOS) | 3 | $15.00 |
| DAC (PCM3168A) | 15 | $25.00 |
| Amplifikatör (8 kanal) | 120 | $85.00 |
| Güç Kaynağı | 35 | $45.00 |
| Pasif Bileşenler | 800+ | $180.00 |
| Konnektörler | 25 | $30.00 |
| PCB (6-layer) | 1 | $50.00 |
| **TOPLAM** | **~1,000** | **~$430** |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k1-donanim/README.md` | Bu dosya |
| `architecture/k1-donanim/xmos-xu316.md` | XMOS detayı |
| `architecture/k1-donanim/pcm3168a.md` | DAC detayı |
| `architecture/k1-donanim/ak4458.md` | High-end DAC |
| `architecture/k1-donanim/class-ab-amplifier.md` | Amplifikatör devresi |
| `architecture/k1-donanim/speaker-matrix.md` | Hoparlör konfigürasyonu |
| `architecture/k1-donanim/bom-cost.md` | BOM maliyet |
| `architecture/k16-class-ab/README.md` | K16 Class AB |
| `architecture/k17-guc-kaynagi/README.md` | K17 Güç kaynağı |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

*K1 Donanım Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
