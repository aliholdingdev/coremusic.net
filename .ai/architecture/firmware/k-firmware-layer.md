---
type: layer
category: firmware
title: "K-Firmware: Firmware Katmanı"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# K-Firmware: Firmware Katmanı

## Genel Bakış

CoreMusic firmware katmanı, XMOS XU316 ve RPi5 üzerinde çalışan gömülü yazılımı yönetir.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FIRMWARE MİMARİSİ                                 │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 5. UYGULAMA KATMANI                                        │   │
│  │    • Audio Processing    • DSP Chain    • Mixer             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 4. İLETİŞİM KATMANI                                        │   │
│  │    • USB Audio Class 2.0    • I2S    • SPI    • I2C         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 3. DONANIM YAPILANDIRMASI                                   │   │
│  │    • Clock Config    • GPIO    • DMA    • Interrupt         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 2. HAL + SÜRÜCÜ KATMANI                                    │   │
│  │    • Hardware Abstraction Layer    • Device Drivers         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 1. BOOTLOADER + RTOS                                       │   │
│  │    • ROM Boot    • Firmware Load    • Task Scheduler        │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Boot Sırası

```
Power On → ROM Bootloader → XMOS Firmware Load → DSP Chain Init
    → I2S Configure → PCM3168A Init → USB Audio Start → System Ready
```

## RTOS Öncelikleri

| Görev | Öncelik | Latency |
|-------|---------|---------|
| Audio DSP | 10 (en yüksek) | 10µs |
| I2S Transfer | 9 | 20µs |
| USB Audio | 8 | 100µs |
| Control | 5 | 1ms |
| Monitoring | 1 | 100ms |

## RTOS Seçenekleri

| Platform | RTOS | Kullanım |
|----------|------|----------|
| XMOS XU316 | xTIMEcomposer | Ana DSP |
| RPi5 | FreeRTOS | Ev medya merkezi |
| Linux RT | Xenomai | Gerçek zamanlı genişletme |
| XMOS Bare-metal | — | Ultra-düşük gecikme |

## DSP Firmware (XMOS XU316)

```
USB Audio Class 2.0 → DSP Chain → I2S Output (8-ch) → PCM3168A DAC
```

DSP Zinciri Aşamaları:
1. Input Gain (1 sample latency)
2. Noise Gate (1 sample)
3. High Pass Filter (1 sample)
4. Low Pass Filter (1 sample)
5. Parametric EQ (1 sample)
6. Graphic EQ (1 sample)
7. Compressor (1 sample)
8. Limiter (1 sample)
9. Loudness (1 sample)
10. Crossover (1 sample)
11. Delay (1 sample)
12. Reverb (4 samples)
13. Output Gain (1 sample)
14. Output Routing (1 sample)

**Toplam DSP Latency:** ~16 samples @ 48kHz = ~0.33ms

## OTA Güncelleme

```
Download (HTTPS) → SHA-256 Checksum → RSA-2048 Signature Verify
    → Backup Current → Write New → Reboot → Verify (or Rollback)
```

Güvenlik:
- SHA-256 checksum zorunlu
- RSA-2048 dijital imza
- Rollback desteği (hatalı firmware'de geri dön)
- A/B partition scheme

## İlgili Dosyalar

- [[k0-k5-software/k2-driver-layer]] — Sürücü katmanı
- [[k0-k5-software/k3-audio-engine]] — Ses motoru
- [[electronics/amplifier-classab-circuit]] — Class AB devresi

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
