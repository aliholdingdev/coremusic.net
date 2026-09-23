---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Hardware Design Template"
type: hardware-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Kategori:** {{HARDWARE_CATEGORY}}
**Katman:** K1 (Donanım) / K16-K20
**Sorumlu Agent:** Audio Hardware Engineer

---

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PCM5122 ile 8.1 surround YAPILAMAZ (ADR-038) | Yanlış donanım |
| 2 | DC-Only güç kaynağı zorunlu | Sistem hatası |
| 3 | Class AB amplifikatör zorunlu (ADR-089) | Yanlış topoloji |
| 4 | 6S LiPo (22.2V) veya 19-24V DC adapter | Güç hatası |
| 5 | Impedans eşleştirme zorunlu | Sinyal kaybı |
| 6 | Thermal hesaplama zorunlu | Aşırı ısınma |

---

## 2. Bileşen Seçim Tablosu

| Bileşen | Model | Özellik | Neden |
|---------|-------|---------|-------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0 | Hi-Res destek |
| DAC | PCM3168A | 6-in/8-out, 24-bit | 8.1 surround |
| DAC (opsiyonel) | AK4458 | 8-kanal, 32-bit | High-end |
| Amplifikatör | MJL21194/MJL21193 | Class AB Darlington | 50W/kanal |
| Boost | LM5122 | ±35V boost converter | Güç kaynağı |
| Batarya | 6S LiPo | 22.2V nominal | Taşınabilir |

---

## 3. Devre Şeması Notları

### 3.1 Güç Kaynağı

```
6S LiPo (22.2V) → LM5122 Boost → ±35V Simetrik
                                    ├→ +35V → Class AB (NPN tarafı)
                                    └→ -35V → Class AB (PNP tarafı)
```

### 3.2 Sinyal Zinciri

```
USB → XMOS XU316 → I2S → PCM3168A → Analog Out → Class AB → Hoparlör
                                                        
PCM3168A Kanalları:
  CH1: Front Left    → Amp 1 → Hoparlör 1
  CH2: Front Right   → Amp 2 → Hoparlör 2
  CH3: Center        → Amp 3 → Hoparlör 3
  CH4: LFE (Sub)     → Amp 4 → Subwoofer
  CH5: Surround Left → Amp 5 → Hoparlör 5
  CH6: Surround Right→ Amp 6 → Hoparlör 6
  CH7: Rear Left     → Amp 7 → Hoparlör 7
  CH8: Rear Right    → Amp 8 → Hoparlör 8
```

### 3.3 Amplifikatör Devresi (Tek Kanal)

```
                    +35V
                     │
                ┌────┴────┐
                │  MJL21194│ (NPN Output)
                │  (NPN)   │
     Input ─────┤         ├──── Output → Hoparlör
                │  MJL21193│
                │  (PNP)   │
                └────┬────┘
                     │
                    -35V

THD+N < 0.005% @ 1W
Güç: 50W/kanal @ 8Ω
```

---

## 4. PCB Tasarım Kuralları

| Parametre | Değer |
|-----------|-------|
| Layer | 6-layer stackup |
| Copper | 2oz (top/bottom), 1oz (inner) |
| Finish | ENIG |
| Impedans | 90Ω USB, 50Ω I2S |
| Thermal | Thermal vias under power components |
| Ground | Star ground topology |
| Size | 200×100mm (max) |

---

## 5. BOM Maliyet Analizi

| Kategori | Bileşen | Adet | Birim Fiyat | Toplam |
|----------|---------|------|------------|--------|
| DAC | PCM3168A | 1 | $8.50 | $8.50 |
| USB | XMOS XU316 | 1 | $12.00 | $12.00 |
| Amp (kanal) | MJL21194 | 8 | $3.50 | $28.00 |
| Amp (kanal) | MJL21193 | 8 | $3.50 | $28.00 |
| Boost | LM5122 | 2 | $4.50 | $9.00 |
| Pasif | Çeşitli | ~200 | ~$0.10 | ~$20.00 |
| PCB | 6-layer | 1 | $50.00 | $50.00 |
| **TOPLAM** | | | | **~$155** |

---

## 6. Test Protokolü

| Test | Yöntem | Kriter |
|------|--------|--------|
| Güç | Multimetre | ±35V ±%5 |
| THD | Audio Analyzer | <0.005% @ 1W |
| SNR | Audio Analyzer | >100dB |
| Frekans | Sine sweep | 20Hz-20kHz ±0.5dB |
| Termal | Termal kamera | <60°C @ full power |
| DC Offset | Multimetre | <0.5V DC |

---

*Hardware Design Template v1.0.0 — CoreMusic Hardware Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
