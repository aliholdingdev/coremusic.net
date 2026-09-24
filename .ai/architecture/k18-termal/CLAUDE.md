---
title: "CoreMusic — K18 Termal CLAUDE.md"
type: layer-guide
folder: "architecture/k18-termal"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K18 Termal — CLAUDE.md

## 1. Termal Kısıtlar

| Parametre | Değer |
|-----------|-------|
| Max sıcaklık | 60°C |
| Heatsink | Fischer SK53-100-SA |
| Thermal R | 0.3°C/W |
| Fan | 80mm PWM |
| Cutoff | KSD301 (72°C) |

## 2. Fan Profili

```
<40°C: Pasif
40-50°C: %25
50-60°C: %50
>60°C: %100
>72°C: Cutoff
```

---

*K18 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
