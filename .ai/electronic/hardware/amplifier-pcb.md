---
type: electronic
category: amplifier-pcb
title: "CoreMusic — Amplifier PCB Layout"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Amplifier PCB Layout

**See also:** [[electronic/hardware/index]] · [[electronic/amplifier/index]]

---

## 1. Amaç

Amplifier PCB Layout, CoreMusic amfi PCB yerleşim kurallarını tanımlar.

---

## 2. Yerleşim Stratejisi

```
[Giriş] → [Diferansiyel] → [VAS] → [Driver] → [Output] → [Filtre] → [Çıkış]
    │                                                              │
[Bias]                                                          [Koruma]
    │                                                              │
[Güç]                                                          [Röle]
```

---

## 3. Güç Yolu Kuralları

| Kural | Açıklama |
|-------|----------|
| Wide traces | ±42V için 100mil+ |
| Short paths | Güç kaynağı → transistör |
| Kelvin connection | Sensing için |
| Star ground | Tek toprak noktası |

---

## 4. Termal Yerleşim

| Kural | Açıklama |
|-------|----------|
| Output transistors | Heatsink kenarında |
| Thermal vias | Isı dağıtımı için |
| Air gap | Sıcak → soğuk akış |
| Sensor placement | En sıcak nokta |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
