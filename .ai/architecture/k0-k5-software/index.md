---
type: index
category: software
title: "Yazılım Altyapı Katmanları (K0-K5)"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Yazılım Altyapı Katmanları (K0-K5)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    YAZILIM ALTYAPI KATMANLARI                        │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K5: VERİ — MySQL 18DB • Redis • APCu • SQLite • Backup     │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K4: YAPAY ZEKA — Analysis • Rec • Auto EQ • Voice • ML     │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K3: SES — Neva Engine • DSP • Mixer • EQ • Reverb          │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K2: SÜRÜCÜ — ASIO • WASAPI • ALSA • PipeWire • CoreAudio   │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K1: DONANIM — XMOS • PCM3168A • AK4458 • Class AB ×8       │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K0: OS — Windows • Linux • macOS • RPi5 • Docker            │   │
│  └─────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

## Dosya İndeksi

| # | Dosya | Boyut | İçerik |
|---|-------|-------|--------|
| 1 | [[k0-os-layer]] | 22KB | İşletim sistemi katmanı (35 bileşen) |
| 2 | [[k1-hardware-layer]] | 27KB | Donanım altyapısı (42 bileşen) |
| 3 | [[k2-driver-layer]] | 26KB | Sürücü katmanı (32 bileşen) |
| 4 | [[k3-audio-engine]] | 27KB | Ses işleme motoru (45 bileşen) |
| 5 | [[k4-ai-layer]] | 26KB | Yapay zeka katmanı (38 bileşen) |
| 6 | [[k5-data-layer]] | 28KB | Veri yönetimi (40 bileşen) |
| 7 | [[k03-audio-detail]] | 15KB | 6 ses servisi, media pipeline |
| 8 | [[k05-data-detail]] | 15KB | 18 BCNF veritabanı detayı |

## Toplam Bileşen: 222

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
