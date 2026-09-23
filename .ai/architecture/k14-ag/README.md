---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K14 Ağ & İletişim Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K14: Ağ & İletişim Layer

**Katman:** K14 (Ağ & İletişim)
**Kapsam:** HTTP/2, WebSocket, mDNS, DLNA, AirPlay, WebRTC, DNS, VPN
**Sorumlu Agent:** Network Engineer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K14 katmanı, CoreMusic'in tüm ağ iletişim protokollerini ve servis keşif mekanizmalarını içerir.

---

## 2. Protokol Haritası

| # | Protokol | Kullanım | Port |
|---|----------|----------|------|
| K14-01 | HTTP/2 | API iletişimi | 443 |
| K14-02 | WebSocket | Real-time iletişim | 443 |
| K14-03 | mDNS | Ağ keşfi | 5353 |
| K14-04 | DLNA/UPnP | Medya paylaşımı | 1900 |
| K14-05 | AirPlay | Apple streaming | 7000 |
| K14-06 | WebRTC | P2P ses aktarımı | 10000-20000 |
| K14-07 | DNS | Alan adı çözümleme | 53 |
| K14-08 | VPN | Güvenli uzaktan erişim | 1194 |

---

## 3. DLNA/UPnP

### 3.1 DLNA Akışı

```
Media Server → DLNA Control Point → DLNA Renderer

Device Discovery: SSDP (Simple Service Discovery Protocol)
Content Transfer: HTTP GET with Range
Format: DLNA compatible (MP3, FLAC, WAV)
```

---

## 4. AirPlay

### 4.1 AirPlay Akışı

```
Source (CoreMusic) → AirPlay Protocol → Sink (Apple TV, HomePod)

Audio: ALAC (Apple Lossless)
Sync: NTP-based clock sync
Buffer: 2-5 seconds
```

---

## 5. WebRTC P2P

### 5.1 P2P Ses Aktarımı

```
Client A ←→ STUN/TURN ←→ Client B

Use Cases:
  - Multi-room audio sync
  - Collaborative listening
  - Remote DJ
```

---

## 6. mDNS

### 6.1 Servis Kaydı

```
Service: _coremusic._tcp.local
Port: 81
TXT Records:
  version=1.0.0
  type=music-server
  device=rpi5
```

---

*K14 Ağ & İletişim Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
