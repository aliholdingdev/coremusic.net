---
type: ecosystem
category: network-architecture
title: "Network Architecture â€” CoreMusic AÄŸ Mimarisi"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/network-architecture.md"
  adr:
    - "decisions/accepted/ADR-020-api-public-security"
    - "decisions/accepted/ADR-022-database-hardened-security"
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
---

# Network Architecture â€” CoreMusic AÄŸ Mimarisi

**Ä°lgili ADR:** [[decisions/accepted/ADR-020-api-public-security]] Â· [[decisions/accepted/ADR-022-database-hardened-security]] Â· [[decisions/accepted/ADR-032-ipc-contract-versioning]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[ecosystem/7-service-integration]] Â· [[architecture/10-network]]

---

## 1. AmaÃ§

CoreMusic platformundaki tÃ¼m aÄŸ topolojisini, port haritasÄ±nÄ±, gÃ¼venlik katmanlarÄ±nÄ± ve servisler arasÄ± iletiÅŸim yollarÄ±nÄ± tanÄ±mlar.

---

## 2. AÄŸ Topolojisi

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ INTERNET                                                    â”‚
â”‚                                                             â”‚
â”‚  KullanÄ±cÄ± (Browser / Mobile / Embedded)                    â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                          â”‚ HTTPS (TLS 1.3)
                          â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ API GATEWAY (api.coremusic.net)                            â”‚
â”‚                                                             â”‚
â”‚  Port 80/443 â†’ Routing â†’ Auth â†’ Rate Limit â†’ CORS          â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
         â”‚               â”‚               â”‚
         â–¼               â–¼               â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ Control :81    â”‚ â”‚ Media :5000  â”‚ â”‚ Download     â”‚
â”‚ (PHP 8.4)     â”‚ â”‚ (PHP+FFmpeg) â”‚ â”‚ :3001 (Node) â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”˜
         â”‚                â”‚                â”‚
         â–¼                â–¼                â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ INTERNAL NETWORK                                            â”‚
â”‚                                                             â”‚
â”‚  MySQL :3306    Redis :6379    APCu (in-memory)             â”‚
â”‚  Audio :9741    Audio WS :9742                              â”‚
â”‚  Device (BLE)   Network Audio (WebRTC)                      â”‚
â”‚  AI Service (internal)                                     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 3. Port HaritasÄ±

### 3.1 Public Portlar

| Port | Servis | Protokol | Firewall |
|------|--------|----------|----------|
| 80 | admin.coremusic.net | HTTP â†’ HTTPS redirect | âœ… |
| 443 | api.coremusic.net | HTTPS (TLS 1.3) | âœ… |
| 81 | music.coremusic.net | HTTP (Control Service) | âœ… |
| 3001 | download.coremusic.net | HTTP/WS | âœ… |

### 3.2 Internal Portlar

| Port | Servis | Protokol | EriÅŸim |
|------|--------|----------|--------|
| 3306 | MySQL 9 | TCP | Internal only |
| 5000 | Media Service | HTTP | Internal only |
| 6000 | Media WebSocket | WS | Internal only |
| 9741 | Audio Service REST | HTTP | Internal only |
| 9742 | Audio Service WS | WS | Internal only |

### 3.3 Embedded Portlar (RPi5)

| Port | Servis | Protokol | EriÅŸim |
|------|--------|----------|--------|
| 81 | Local Web Server | HTTP | Local only |
| 8080 | Admin Interface | HTTP | Local only |

---

## 4. DNS YapÄ±sÄ±

| Subdomain | Hedef | TTL |
|-----------|-------|-----|
| `coremusic.net` | Landing page (static) | 3600s |
| `music.coremusic.net` | Control Service (:81) | 3600s |
| `admin.coremusic.net` | Admin panel (:80) | 3600s |
| `auth.coremusic.net` | Auth Service | 3600s |
| `api.coremusic.net` | API Gateway | 3600s |
| `media.coremusic.net` | Media Service (:5000) | 3600s |
| `download.coremusic.net` | Download Service (:3001) | 3600s |
| `home.coremusic.net` | RPi5 Home | Local DNS |
| `car.coremusic.net` | RPi5 Car | Local DNS |
| `studio.coremusic.net` | RPi5 Studio | Local DNS |
| `pro.coremusic.net` | RPi5 Pro | Local DNS |

---

## 5. TLS & GÃ¼venlik

### 5.1 TLS KonfigÃ¼rasyonu

| Parametre | DeÄŸer |
|-----------|-------|
| TLS Version | 1.3 (min 1.2) |
| Cipher Suites | TLS_AES_256_GCM_SHA384, TLS_CHACHA20_POLY1305_SHA256 |
| HSTS | max-age=31536000; includeSubDomains |
| OCSP Stapling | Aktif |
| Certificate | Let's Encrypt (auto-renew) |

### 5.2 Firewall KurallarÄ±

| Kural | Port | Kaynak | Hedef | Aksiyon |
|-------|------|--------|-------|---------|
| HTTP | 80 | Any | Server | ALLOW (â†’ HTTPS redirect) |
| HTTPS | 443 | Any | Server | ALLOW |
| Control | 81 | Internal | Server | ALLOW |
| Download | 3001 | Internal | Server | ALLOW |
| MySQL | 3306 | Internal | Server | ALLOW (internal only) |
| SSH | 22 | Admin IP | Server | ALLOW |
| Default | â€” | Any | Any | DENY |

---

## 6. Servisler ArasÄ± Ä°letiÅŸim

| Kaynak â†’ Hedef | Protokol | Port | Encryption |
|-----------------|----------|------|------------|
| Frontend â†’ API Gateway | HTTPS | 443 | TLS 1.3 |
| API Gateway â†’ Control | HTTP | 81 | Internal |
| API Gateway â†’ Media | HTTP | 5000 | Internal |
| API Gateway â†’ Download | HTTP | 3001 | Internal |
| Control â†’ MySQL | TCP | 3306 | Internal |
| Media â†’ MySQL | TCP | 3306 | Internal |
| Audio â†’ Device | BLE/WiFi | â€” | Internal |
| Audio â†’ Network Audio | WebRTC | 49152+ | DTLS |

---

## 7. CDN & Caching

| Katman | Teknoloji | KullanÄ±m |
|--------|-----------|----------|
| **Edge CDN** | Cloudflare | Static assets, Landing page |
| **Reverse Proxy** | Nginx | Load balancing, SSL termination |
| **Application Cache** | APCu | L1 hot cache (10s TTL) |
| **Distributed Cache** | Redis | L2 warm cache (60s TTL) |
| **Database Cache** | MySQL Query Cache | L3 cold cache |

---

## 8. Bandwidth & Throughput

| Metrik | Hedef | Minimum |
|--------|-------|---------|
| Internet Upload | 100 Mbps | 10 Mbps |
| Internet Download | 1 Gbps | 100 Mbps |
| Internal Throughput | 10 Gbps | 1 Gbps |
| WiFi Throughput | 802.11ac | 802.11n |
| BLE Throughput | BLE 5.0 | BLE 4.2 |

---

## 9. Cross References

| Dosya | AmaÃ§ |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-communication]] | Ä°letiÅŸim protokolleri |
| [[architecture/10-network]] | AÄŸ detaylarÄ± |
| [[architecture/07-security]] | GÃ¼venlik detayÄ± |
| [[architecture/master-architecture-index]] | Canonical counts |

---

## 10. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team Â· Human Mode Â· Truth Mode verified |
| **Public Ports** | 4 |
| **Internal Ports** | 5 |
| **Embedded Ports** | 2 |
| **DNS Records** | 11 |
| **TLS Version** | 1.3 |
| **Firewall Rules** | 7 |
| **ADR Coverage** | 020, 022, 032 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team Â· Human Mode Â· Truth Mode

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: Servis Durum Matrisi

| Servis | Entegrasyon Durumu | KanÃ„Â±t / AÃƒÂ§Ã„Â±klama |
|--------|--------------------|------------------|
| Control Service | **IMPLEMENTED** | shared/src/, uth.coremusic.net/ aktif |
| Media Service | **PLANNED** | TasarÃ„Â±m aÃ…Å¸amasÃ„Â±nda |
| Audio Service | **PLANNED** | C++ NevaEngine taslak |
| Device Service | **PLANNED** | DonanÃ„Â±m (I2S/BLE) beklemede |
| Network Audio | **PLANNED** | WebRTC mimarisi ÃƒÂ§izildi |
| AI Service | **PLANNED** | Python entegrasyonu planlandÃ„Â± |
| Download Service | **PLANNED** | Node.js servis klasÃƒÂ¶rÃƒÂ¼ yok |

