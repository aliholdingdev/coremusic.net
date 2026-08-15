---
type: electronic
category: thermal-management
title: "CoreMusic — Amplifier Thermal Management"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Thermal Management

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/thermal-analysis]]

---

## 1. Amaç

Thermal Management, CoreMusic amfi termal yönetim ve soğutma tasarımını tanımlar.

---

## 2. Isı Üretimi

| Bileşen | Isı Üretimi | Soğutma |
|---------|-------------|---------|
| Output Transistors | ~50W (Class AB) | heatsink |
| Driver Transistors | ~5W | heatsink |
| Power Supply | ~20W | havalandırma |
| DSP Board | ~10W | heatsink |

---

## 3. Heatsink Tasarımı

| Parametre | Değer |
|-----------|-------|
| Malzeme | Alüminyum |
| Yüzey Alanı | >500cm² |
| Termal Direnç | <1°C/W |
| Fan | 80mm, 12V DC |
| Sıcaklık Eşiği | 60°C'de fan başlar |

---

## 4. Termal Koruması

| Sıcaklık | Aksiyon |
|----------|---------|
| <50°C | Normal çalışma |
| 50-60°C | Uyarı |
| 60-70°C | Fan hızı artır |
| >70°C | Güç azaltma |
| >80°C | Kapanma |

---

## 5. Termal Akış Diyagramı

```
Output Transistors ──▶ Heatsink ──▶ Fan ──▶ Hava Akışı

Termal Sensör:
    <60°C ──▶ Fan Kapalı
    >60°C ──▶ Fan Açık
```

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
