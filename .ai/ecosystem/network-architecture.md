---
type: ecosystem
category: network-architecture
title: "Network Architecture — CoreMusic Ağ Mimarisi"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/network-architecture.md"
  adr:
    - "decisions/accepted/ADR-020-api-public-security"
    - "decisions/accepted/ADR-022-database-hardened-security"
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
---

# Network Architecture — CoreMusic Ağ Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-020-api-public-security]] · [[decisions/accepted/ADR-022-database-hardened-security]] · [[decisions/accepted/ADR-032-ipc-contract-versioning]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[architecture/10-network]]

---

## 1. Amaç

CoreMusic platformundaki tüm ağ topolojisini, port haritasını, güvenlik katmanlarını ve servisler arası iletişim yollarını tanımlar.

---

## 2. Ağ Topolojisi

```
┌─────────────────────────────────────────────────────────────┐
│ INTERNET                                                    │
│                                                             │
│  Kullanıcı (Browser / Mobile / Embedded)                    │
└─────────────────────────┬───────────────────────────────────┘
                          │ HTTPS (TLS 1.3)
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ API GATEWAY (api.coremusic.net)                            │
│                                                             │
│  Port 80/443 → Routing → Auth → Rate Limit → CORS          │
└────────┬───────────────┬───────────────┬───────────────────┘
         │               │               │
         ▼               ▼               ▼
┌────────────────┐ ┌──────────────┐ ┌──────────────┐
│ Control :81    │ │ Media :5000  │ │ Download     │
│ (PHP 8.4)     │ │ (PHP+FFmpeg) │ │ :3001 (Node) │
└────────┬───────┘ └──────┬───────┘ └──────┬───────┘
         │                │                │
         ▼                ▼                ▼
┌─────────────────────────────────────────────────────────────┐
│ INTERNAL NETWORK                                            │
│                                                             │
│  MySQL :3306    Redis :6379    APCu (in-memory)             │
│  Audio :9741    Audio WS :9742                              │
│  Device (BLE)   Network Audio (WebRTC)                      │
│  AI Service (internal)                                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. Port Haritası

### 3.1 Public Portlar

| Port | Servis | Protokol | Firewall |
|------|--------|----------|----------|
| 80 | admin.coremusic.net | HTTP → HTTPS redirect | ✅ |
| 443 | api.coremusic.net | HTTPS (TLS 1.3) | ✅ |
| 81 | music.coremusic.net | HTTP (Control Service) | ✅ |
| 3001 | download.coremusic.net | HTTP/WS | ✅ |

### 3.2 Internal Portlar

| Port | Servis | Protokol | Erişim |
|------|--------|----------|--------|
| 3306 | MySQL 9 | TCP | Internal only |
| 5000 | Media Service | HTTP | Internal only |
| 6000 | Media WebSocket | WS | Internal only |
| 9741 | Audio Service REST | HTTP | Internal only |
| 9742 | Audio Service WS | WS | Internal only |

### 3.3 Embedded Portlar (RPi5)

| Port | Servis | Protokol | Erişim |
|------|--------|----------|--------|
| 81 | Local Web Server | HTTP | Local only |
| 8080 | Admin Interface | HTTP | Local only |

---

## 4. DNS Yapısı

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

## 5. TLS & Güvenlik

### 5.1 TLS Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| TLS Version | 1.3 (min 1.2) |
| Cipher Suites | TLS_AES_256_GCM_SHA384, TLS_CHACHA20_POLY1305_SHA256 |
| HSTS | max-age=31536000; includeSubDomains |
| OCSP Stapling | Aktif |
| Certificate | Let's Encrypt (auto-renew) |

### 5.2 Firewall Kuralları

| Kural | Port | Kaynak | Hedef | Aksiyon |
|-------|------|--------|-------|---------|
| HTTP | 80 | Any | Server | ALLOW (→ HTTPS redirect) |
| HTTPS | 443 | Any | Server | ALLOW |
| Control | 81 | Internal | Server | ALLOW |
| Download | 3001 | Internal | Server | ALLOW |
| MySQL | 3306 | Internal | Server | ALLOW (internal only) |
| SSH | 22 | Admin IP | Server | ALLOW |
| Default | — | Any | Any | DENY |

---

## 6. Servisler Arası İletişim

| Kaynak → Hedef | Protokol | Port | Encryption |
|-----------------|----------|------|------------|
| Frontend → API Gateway | HTTPS | 443 | TLS 1.3 |
| API Gateway → Control | HTTP | 81 | Internal |
| API Gateway → Media | HTTP | 5000 | Internal |
| API Gateway → Download | HTTP | 3001 | Internal |
| Control → MySQL | TCP | 3306 | Internal |
| Media → MySQL | TCP | 3306 | Internal |
| Audio → Device | BLE/WiFi | — | Internal |
| Audio → Network Audio | WebRTC | 49152+ | DTLS |

---

## 7. CDN & Caching

| Katman | Teknoloji | Kullanım |
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

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-communication]] | İletişim protokolleri |
| [[architecture/10-network]] | Ağ detayları |
| [[architecture/07-security]] | Güvenlik detayı |
| [[architecture/00-overview/architecture-master]] | Canonical counts |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
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
**Mode:** Red Team · Human Mode · Truth Mode
