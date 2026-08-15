---
type: electronic
category: usb-drivers
title: "CoreMusic — USB Audio Drivers (UAC 2.0)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — USB Audio Drivers

**See also:** [[electronic/drivers/index]] · [[electronic/hardware/index]]

---

## 1. Amaç

USB Audio Drivers, CoreMusic platformunun USB ses cihazlarıyla (XMOS XU316, PCM3168A) iletişimini tanımlar.

---

## 2. USB Audio Class 2.0

| Özellik | Değer |
|---------|-------|
| Sürüm | UAC 2.0 |
| Max Kanal | 8 (7.1 surround) |
| Bit Derinliği | 32-bit float |
| Örnekleme | 44.1kHz - 192kHz |
| Isochronous | Evet |
| Latency | <5ms |

---

## 3. XMOS XU316 USB

| Özellik | Değer |
|---------|-------|
| Chip | XMOS XU316 |
| USB | USB 2.0 High-Speed |
| Kanal | 8-kanal I2S |
| DSP | Zero-latency DSP |
| Sürücü | Sınıf-yönetici (class-compliant) |

---

## 4. USB Hot-Plug

| Olay | Davranış |
|------|----------|
| Takma | Otomatik algılama, stream başlat |
| Çıkarma | Graceful stop, WASAPI fallback |
| Yeniden takma | Otomatik reconnect |

---

## 5. USB Driver Akışı

```
USB Tak ──▶ USB Enumeration ──▶ {Cihaz Tanımla}
                                  │
                     XMOS ▼        ▼ Standart
              XMOS Driver    UAC 2.0 Driver
                     │              │
                     └──────┬───────┘
                            ▼
                   Kanal/Format Ayarla
                            │
                            ▼
                   Stream Başlat
                            │
                            ▼
                   Çıkarma ──▶ Temizle
```

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | XMOS XU316, PCM3168A |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
