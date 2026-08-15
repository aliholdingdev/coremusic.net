---
type: electronic
category: wifi-drivers
title: "CoreMusic — WiFi Audio Drivers (DLNA, AirPlay, Roon)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — WiFi Audio Drivers

**See also:** [[electronic/drivers/index]] · [[architecture/10-network/index]]

---

## 1. Amaç

WiFi Audio Drivers, CoreMusic platformunun WiFi üzerinden ses akışı protokollerini (DLNA, AirPlay, Roon, UPnP) tanımlar.

---

## 2. DLNA / UPnP

| Özellik | Değer |
|---------|-------|
| Protokol | DLNA 1.5 / UPnP AV |
| Transport | HTTP, TCP |
| Format | FLAC, WAV, MP3, AAC |
| Max Resolution | 24-bit/192kHz |
| Discovery | SSDP multicast |

---

## 3. AirPlay

| Özellik | Değer |
|---------|-------|
| Protokol | AirPlay 2 |
| Transport | HTTP, mDNS |
| Format | ALAC, AAC |
| Max Resolution | 24-bit/48kHz |
| Latency | 2s (varsayılan) |
| Multi-room | Destekli |

---

## 4. Roon

| Özellik | Değer |
|---------|-------|
| Protokol | Roon Ready |
| Transport | RAIS |
| Format | FLAC, WAV, DSD |
| Max Resolution | 32-bit/384kHz, DSD512 |
| DSP | Roon DSP Engine |
| Multi-room | Destekli |

---

## 5. WiFi Driver Akışı

```
Başla ──▶ {Cihaz Keşfet}
              │
     DLNA ▼   ▼ AirPlay   ▼ Roon
  UPnP Discovery  mDNS Discovery  RAIS Discovery
        │              │              │
        └──────────────┴──────────────┘
                       │
                Cihaz Seç
                       │
                Bağlantı Kur
                       │
                Stream Başlat
```

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-026-download-service-architecture]] | Network mimarisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
