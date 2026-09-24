---
title: "CoreMusic — K16-K20 Elektronik CLAUDE.md"
type: layer-guide
folder: "architecture/k16-class-ab"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K16-K20 Elektronik — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Class AB zorunlu (ADR-089) | Yanlış topoloji |
| 2 | ±35V simetrik güç | Dengesiz çıkış |
| 3 | 6-layer PCB | EMI sorunu |
| 4 | Thermal hesaplama zorunlu | Aşırı ısınma |
| 5 | THD+N <0.005% | Kalite düşüşü |

## 2. Amplifikatör Kısıtları

| Parametre | Değer | Min/Max |
|-----------|-------|---------|
| Topoloji | Class AB Darlington | Sabit |
| Output | MJL21194/MJL21193 | Sabit |
| Güç | 50W/kanal @ 8Ω | 25-75W |
| THD+N | <0.005% | <0.01% |
| SNR | >100dB | >95dB |

## 3. PCB Kısıtları

| Parametre | Değer |
|-----------|-------|
| Layer | 6 |
| Copper | 2oz top/bottom |
| Finish | ENIG |
| USB impedance | 90Ω |
| I2S impedance | 50Ω |
| Min trace | 4mil |
| Min via | 8mil |

## 4. Termal Kısıtlar

| Parametre | Değer |
|-----------|-------|
| Max sıcaklık | 60°C |
| Heatsink | Fischer SK53-100-SA |
| Fan | 80mm PWM |
| Thermal cutoff | KSD301 (72°C) |

## 5. BOM Maliyet Hedefi

| Kategori | Hedef |
|----------|-------|
| Amplifikatör | <$90 |
| DAC + USB | <$25 |
| Güç kaynağı | <$50 |
| Pasif | <$180 |
| PCB | <$50 |
| **Toplam** | **<$430** |

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

*K16-K20 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
