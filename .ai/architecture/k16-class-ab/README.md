---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K16-K20 Elektronik Layer"
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

# K16-K20: Elektronik Layer

**Katman:** K16-K20 (Elektronik)
**Kapsam:** Class AB Amplifikatör, Güç Kaynağı, Termal, PCB, BOM
**Sorumlu Agent:** Audio Hardware Engineer
**Bileşen Sayısı:** 370

---

## 1. K16: Class AB Amplifikatör

### 1.1 Teknik Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class AB Darlington |
| Output Transistör | MJL21194 (NPN) / MJL21193 (PNP) |
| Güç | 50W/kanal @ 8Ω |
| THD+N | <0.005% @ 1W |
| SNR | >100dB |
| Kanal | 8 (modüler) |
| Gain | 27dB |
| Input Impedans | 47kΩ |
| Frequency Response | 20Hz-20kHz ±0.5dB |

### 1.2 Devre Topolojisi

```
Input Stage: Diferansiyel çift (BC546B/BC556B)
VAS: Voltage Amplifier Stage (KSC5026)
Output: Darlington (MJL21194/MJL21193)
Bias: Vbe multiplier (BD139)
Protection: DC offset, thermal, short circuit
```

---

## 2. K17: Güç Kaynağı ±35V

### 2.1 Teknik Özellikler

| Parametre | Değer |
|-----------|-------|
| Giriş | 22.2V (6S LiPo) veya 19-24V DC |
| Çıkış | ±35V simetrik |
| Topoloji | Interleaved Dual Boost |
| Controller | LM5122 × 2 |
| Verimlilik | %96 |
| Ripple | <50mV p-p |
| Koruma | UVP, OVP, OCP, OTP |

### 2.2 Güç Akışı

```
6S LiPo (22.2V) → LM5122 Boost → +35V → Class AB (NPN)
                 → LM5122 Invert → -35V → Class AB (PNP)

Battery Management:
  - UVP: 3.0V/cell (18V total)
  - OVP: 4.2V/cell (25.2V total)
  - OCP: 10A per channel
  - OTP: 60°C
```

---

## 3. K18: Termal Tasarım

### 3.1 Termal Parametreler

| Parametre | Değer |
|-----------|-------|
| Max Sıcaklık | 60°C (full load) |
| Heatsink | Fischer SK53-100-SA |
| Heatsink Boyutu | 300×75×49mm |
| Thermal Resistance | 0.3°C/W |
| Fan | 80mm PWM (Noctua NF-A8) |
| Fan Hızı | Sıcaklık kontrollü |
| Thermal Cutoff | KSD301 (72°C) |

### 3.2 Fan Kontrol Profili

```
<40°C: Fan yok (passive)
40-50°C: Fan %25
50-60°C: Fan %50
>60°C: Fan %100
>72°C: Thermal cutoff (KSD301)
```

---

## 4. K19: PCB Tasarımı

### 4.1 Stackup

```
Layer 1: Signal (top) — Components, high-speed traces
Layer 2: Ground — Continuous ground plane
Layer 3: Signal — I2S, control signals
Layer 4: Power — +35V, -35V, +3.3V, +5V
Layer 5: Ground — Continuous ground plane
Layer 6: Signal (bottom) — Components, low-speed traces
```

### 4.2 Impedans Kontrolü

| Sinyal | Impedans | Tolerance |
|--------|----------|-----------|
| USB 2.0 | 90Ω differential | ±10% |
| I2S | 50Ω single-ended | ±10% |
| Clock | 50Ω single-ended | ±10% |
| Power | Low impedance | — |

---

## 5. K20: BOM & Üretim

### 5.1 BOM Özeti

| Kategori | Bileşen | Adet | Birim | Toplam |
|----------|---------|------|-------|--------|
| Amplifikatör | MJL21194 | 8 | $3.50 | $28 |
| Amplifikatör | MJL21193 | 8 | $3.50 | $28 |
| DAC | PCM3168A | 1 | $8.50 | $8.50 |
| USB | XMOS XU316 | 1 | $12.00 | $12 |
| Boost | LM5122 | 2 | $4.50 | $9 |
| Pasif | Çeşitli | 800+ | ~$0.10 | ~$180 |
| PCB | 6-layer | 1 | $50 | $50 |
| **TOPLAM** | | **~1000** | | **~$430** |

### 5.2 Tedarikçiler

| Tedarikçi | Kullanım |
|-----------|----------|
| Mouser | Ana tedarikçi |
| Digikey | Alternatif |
| LCSC | Uygun fiyatlı |
| JLCPCB | PCB üretimi |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

*K16-K20 Elektronik Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
