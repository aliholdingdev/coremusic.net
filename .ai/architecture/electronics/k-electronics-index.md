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

## Genel Bakış

CoreMusic elektronik katmanları, fiziksel donanım ve devre tasarımını kapsar.

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ELEKTRONİK KATMANLARI                             │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K20: BOM & ÜRETİM                                          │   │
│  │ 1020 bileşen • Mouser/Digikey • ~$682 sistem               │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K19: PCB TASARIM                                           │   │
│  │ 6-layer • Impedance • Thermal Via • Star Ground            │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K18: TERMAL TASARIM                                        │   │
│  │ Fischer SK53 • KSD301 • Fan PWM • 41W/kanal               │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K17: GÜÇ KAYNAĞI ±35V                                     │   │
│  │ LM5122 ×2 • 6S LiPo • OR-ing • %96 verim                  │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K16: CLASS AB AMPLİFİKATÖR                                 │   │
│  │ MJL21194/93 • Darlington • 50W/kanal • THD <0.005%        │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  Toplam: 340 bileşen • ~$682                                       │
└─────────────────────────────────────────────────────────────────────┘
```

## Dosya İndeksi

| Katman | Dosya | Boyut | İçerik |
|--------|-------|-------|--------|
| K16 | amplifier-classab-circuit.md | 30KB | Class AB devre şeması |
| K16 | 8ch-integration.md | 35KB | 8 kanal entegrasyon |
| K16 | test-fixture.md | 25KB | Test fixture |
| K16 | test-protocol.md | 26KB | Test protokolü |
| K17 | power-supply-classab.md | 45KB | ±35V güç kaynağı |
| K18 | thermal-design-classab.md | 17KB | Termal tasarım |
| K19 | pcb-classab.md | 8KB | PCB kuralları |
| K20 | bom-classab.md | 11KB | BOM listesi |
| — | validation-report.md | 8KB | ADR-089 doğrulama |

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

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
