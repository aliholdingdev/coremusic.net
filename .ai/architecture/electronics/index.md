---
type: index
category: electronics
title: "Elektronik Katmanları İndeksi (K16-K20)"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Elektronik Katmanları İndeksi (K16-K20)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ELEKTRONİK KATMANLARI                             │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K20: BOM & ÜRETİM — 1020 bileşen • ~$682                  │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K19: PCB TASARIM — 6-layer • Impedance • Thermal Via       │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K18: TERMAL — Fischer SK53 • KSD301 • Fan PWM              │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K17: GÜÇ ±35V — LM5122 ×2 • 6S LiPo • OR-ing             │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K16: CLASS AB — MJL21194/93 • Darlington • 50W/kanal      │   │
│  └─────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

## Dosya İndeksi

| # | Dosya | Boyut | İçerik |
|---|-------|-------|--------|
| 1 | [[amplifier-classab-circuit]] | 30KB | Class AB devre şeması (Diff Pair → VAS → Vbe → Darlington) |
| 2 | [[power-supply-classab]] | 45KB | ±35V boost güç kaynağı (LM5122 × 2) |
| 3 | [[thermal-design-classab]] | 17KB | Heatsink hesaplaması (Fischer SK53) |
| 4 | [[pcb-classab]] | 8KB | 6-layer PCB kuralları |
| 5 | [[bom-classab]] | 11KB | 8 kanal BOM listesi |
| 6 | [[8ch-integration]] | 35KB | 8 kanal entegrasyon |
| 7 | [[test-fixture]] | 25KB | Test fixture |
| 8 | [[test-protocol]] | 26KB | Test protokolü |
| 9 | [[validation-report]] | 8KB | ADR-089 doğrulama |

## ADR Uyumluluğu

| ADR | Konu | Durum |
|-----|------|-------|
| ADR-038 | 8.1 Sound Card | Active |
| ADR-061 | Electronics | Active |
| ADR-089 | Class AB + 6S LiPo | Draft |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
