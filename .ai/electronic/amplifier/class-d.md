---
type: electronic
category: amplifier-class-d
title: "CoreMusic — Class D Amplifier Topology"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Class D Amplifier

**See also:** [[electronic/amplifier/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Class D Amplifier, CoreMusic ELECTRONICS platformunun yüksek verimli, düşük güç tüketimli amfi topolojisini tanımlar.

---

## 2. Temel Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class D (PWM) |
| Verimlilik | >90% |
| Çıkış Gücü | 100W @ 8Ω, 200W @ 4Ω |
| THD+N | <0.01% (1kHz, 1W) |
| SNR | >100dB (A-wtd) |
| Frekans Yanıtı | 20Hz - 20kHz (±0.5dB) |
| Damping Factor | >200 (8Ω) |

---

## 3. PWM Sinyal İşleme

```
Analog Input → Sigma-Delta Modulator → PWM → Output Filter → Hoparlör
                              │
                         Self-Oscillating
                         or External Clock
```

---

## 4. Çıkış Filtresi

| Parametre | Değer |
|-----------|-------|
| Filtre Tipi | 2. basamak LC low-pass |
| Kesme Frekansı | 50kHz |
| Indüktör | 10µH, high-current |
| Kapasitör | 0.47µF, film |

---

## 5. Class AB vs Class D

| Kriter | Class AB | Class D |
|--------|----------|---------|
| Verimlilik | ~50% | >90% |
| Isı | Yüksek | Düşük |
| Boyut | Büyük | Küçük |
| Ses Kalitesi | Mükemmel | Çok iyi |
| Maliyet | Yüksek | Orta |
| Kullanım | Stüdyo | Taşınabilir |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
