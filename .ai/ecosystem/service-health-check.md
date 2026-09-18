---
type: ecosystem
category: health-check
title: "Service Health Check â€” CoreMusic SaÄŸlÄ±k KontrolÃ¼"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/service-health-check.md"
  adr:
    - "decisions/accepted/ADR-039-7-service-platform-architecture"
---

# Service Health Check â€” CoreMusic SaÄŸlÄ±k KontrolÃ¼

**Ä°lgili ADR:** [[decisions/accepted/ADR-039-7-service-platform-architecture]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[ecosystem/7-service-integration]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

7 servisin saÄŸlÄ±k durumunu izleme, baÄŸÄ±mlÄ±lÄ±k zincirlerini doÄŸrulama ve baÅŸlatma sÄ±rasÄ±nÄ± yÃ¶neten protokolÃ¼ tanÄ±mlar.

---

## 2. Health Check Endpoint'leri

| # | Servis | Endpoint | Port | SÄ±klÄ±k | Timeout |
|---|--------|----------|------|--------|---------|
| 1 | Control Service | `/health` | 81 | 10s | 5s |
| 2 | Media Service | `/health` | 5000 | 10s | 5s |
| 3 | Audio Service | `/health` | 9741 | 10s | 5s |
| 4 | Device Service | `/health` | â€” | 30s | 10s |
| 5 | Network Audio | `/health` | â€” | 30s | 10s |
| 6 | AI Service | `/health` | â€” | 60s | 15s |
| 7 | Download Service | `/health` | 3001 | 30s | 10s |

---

## 3. Health Check Response FormatÄ±

```json
{
  "status": "healthy|degraded|unhealthy",
  "service": "control-service",
  "version": "1.0.0",
  "uptime": 86400,
  "checks": {
    "database": { "status": "up", "latency_ms": 2 },
    "cache": { "status": "up", "latency_ms": 1 },
    "dependencies": { "status": "up", "latency_ms": 15 }
  }
}
```

---

## 4. SaÄŸlÄ±k DurumlarÄ±

> Detay: [[ecosystem/state-machines]] Â§4

---

## 5. Servis BaÅŸlatma SÄ±rasÄ± (Boot Order)

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ PHASE 1: AltyapÄ± (0-5s)                                    â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                 â”‚
â”‚  â”‚ MySQL 9  â”‚  â”‚ Redis    â”‚  â”‚ APCu     â”‚                 â”‚
â”‚  â”‚ :3306    â”‚  â”‚ :6379    â”‚  â”‚ (PHP)    â”‚                 â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜                 â”‚
â”‚       â”‚              â”‚              â”‚                       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
        â”‚              â”‚              â”‚
â”Œâ”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ PHASE 2: Core Servisler (5-15s)                            â”‚
â”‚       â”‚              â”‚              â”‚                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”        â”‚              â”‚                       â”‚
â”‚  â”‚ Control  â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”˜              â”‚                       â”‚
â”‚  â”‚ Service  â”‚                       â”‚                       â”‚
â”‚  â”‚ :81      â”‚                       â”‚                       â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜                       â”‚                       â”‚
â”‚       â”‚                             â”‚                       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
        â”‚                             â”‚
â”Œâ”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ PHASE 3: Medya Servisleri (15-30s)                         â”‚
â”‚       â”‚                             â”‚                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”        â”‚                       â”‚
â”‚  â”‚ Media    â”‚  â”‚ Download â”‚        â”‚                       â”‚
â”‚  â”‚ Service  â”‚  â”‚ Service  â”‚        â”‚                       â”‚
â”‚  â”‚ :5000    â”‚  â”‚ :3001    â”‚        â”‚                       â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜        â”‚                       â”‚
â”‚       â”‚              â”‚              â”‚                       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
        â”‚              â”‚              â”‚
â”Œâ”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ PHASE 4: Ses & AI (30-45s)                                 â”‚
â”‚       â”‚              â”‚              â”‚                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”                 â”‚
â”‚  â”‚ Audio    â”‚  â”‚ AI       â”‚  â”‚ Device   â”‚                 â”‚
â”‚  â”‚ Service  â”‚  â”‚ Service  â”‚  â”‚ Service  â”‚                 â”‚
â”‚  â”‚ :9741    â”‚  â”‚ (int)    â”‚  â”‚ (BLE)    â”‚                 â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                 â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                                              â”‚
â”‚  â”‚ Network  â”‚                                              â”‚
â”‚  â”‚ Audio    â”‚                                              â”‚
â”‚  â”‚ (P2P)    â”‚                                              â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                                              â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 6. Health Check AkÄ±ÅŸÄ±

```
Timer tetiklendi (her 10s)
  â†’ Servise GET /health isteÄŸi
    â†’ YanÄ±t kontrolÃ¼
      â†’ 200 OK â†’ Healthy â†’ Devam
      â†’ 207 Degraded â†’ UyarÄ± logu â†’ Devam
      â†’ Timeout/503 â†’ Unhealthy â†’ Retry (max 3)
        â†’ Retry baÅŸarÄ±sÄ±z â†’ Fallback zincirini tetikle
          â†’ Fallback baÅŸarÄ±sÄ±z â†’ Escalation (L1 â†’ L2 â†’ L3)
```

---

## 7. BaÄŸÄ±mlÄ±lÄ±k KontrolÃ¼

Her servis kendi baÄŸÄ±mlÄ±lÄ±klarÄ±nÄ± health check'e dahil eder:

| Servis | Kontrol Edilen BaÄŸÄ±mlÄ±lÄ±klar |
|--------|------------------------------|
| Control | MySQL, APCu, Session store |
| Media | MySQL, FFmpeg, Dosya sistemi |
| Audio | ASIO/WASAPI driver, Neva Engine |
| Device | BLE stack, WiFi, USB |
| Network Audio | WebRTC, P2P mesh |
| AI | MySQL, Model dosyalarÄ± |
| Download | MySQL, Node.js process, Disk alanÄ± |

---

## 8. Circuit Breaker

| Senaryo | EÅŸik | Aksiyon | Recovery |
|---------|------|---------|----------|
| Servis 3 kez art arda 503 dÃ¶ndÃ¼rÃ¼rse | 3 failure | Circuit OPEN (30s) | Half-open after 30s |
| Circuit OPEN iken istek gelirse | â€” | Fallback kullan | â€” |
| Half-open'da 1 baÅŸarÄ±lÄ± istek | 1 success | Circuit CLOSED | Normal devam |

---

## 9. Retry Stratejisi

> Detay: [[ecosystem/service-communication]] Â§7

---

## 10. Monitoring & Alerting

| Metrik | EÅŸik | Alert |
|--------|------|-------|
| Servis availability | <99.9% | CRITICAL |
| YanÄ±t sÃ¼resi (p95) | >1s | WARN |
| YanÄ±t sÃ¼resi (p99) | >5s | ERROR |
| Hata oranÄ± | >1% | ERROR |
| Circuit breaker OPEN | Herhangi | CRITICAL |
| DB baÄŸlantÄ± havuzu dolu | >90% | WARN |

---

## 11. Cross References

| Dosya | AmaÃ§ |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/error-recovery]] | Hata kurtarma |
| [[ecosystem/service-communication]] | Ä°letiÅŸim protokolleri |
| [[architecture/06-audio]] | Servis detaylarÄ± |
| [[architecture/master-architecture-index]] | Canonical counts |

---

## 12. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team Â· Human Mode Â· Truth Mode verified |
| **Service Count** | 7 |
| **Health States** | 3 (healthy, degraded, unhealthy) |
| **Circuit Breaker** | âœ… |
| **Retry Strategy** | âœ… Exponential backoff |
| **Boot Phases** | 4 |

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

