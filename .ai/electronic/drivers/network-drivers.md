---
type: electronic
category: network-drivers
title: "CoreMusic — Network Audio Drivers"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Network Audio Drivers

**See also:** [[electronic/drivers/index]] · [[architecture/10-network/index]]

---

## 1. Amaç

Network Audio Drivers, CoreMusic platformunun Ethernet ve network ses protokollerini tanımlar.

---

## 2. Network Protocol Desteği

| Protokol | Kullanım | Latency |
|----------|----------|---------|
| mDNS | Cihaz keşfi | <100ms |
| DLNA/UPnP | Medya paylaşımı | 100-500ms |
| AirPlay | Apple cihazlar | ~2s |
| Roon | Profesyonel | <100ms |
| WebRTC | Real-time | <50ms |

---

## 3. Multi-Room Streaming

| Özellik | Değer |
|---------|-------|
| Max Oda | 32 |
| Senkronizasyon | <1ms |
| Protokol | P2P veya Hub |
| Latency | 10-50ms |

---

## 4. Network Driver Akışı

```
mDNS Discovery ──▶ Oda Seç ──▶ TCP Bağlantı ──▶ Kimlik Doğrula ──▶ Senkronizasyon ──▶ Stream Başlat ──▶ İzleme
```

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-026-download-service-architecture]] | Network mimarisi |
| [[ADR-037-wirelessconnect-integration]] | Kablosuz entegrasyon |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
