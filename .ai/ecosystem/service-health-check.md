---
type: ecosystem
category: health-check
title: "Service Health Check — CoreMusic Sağlık Kontrolü"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/service-health-check.md"
  adr:
    - "decisions/accepted/ADR-039-7-service-platform-architecture"
---

# Service Health Check — CoreMusic Sağlık Kontrolü

**İlgili ADR:** [[decisions/accepted/ADR-039-7-service-platform-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[architecture/00-overview/architecture-master]]

---

## 1. Amaç

7 servisin sağlık durumunu izleme, bağımlılık zincirlerini doğrulama ve başlatma sırasını yöneten protokolü tanımlar.

---

## 2. Health Check Endpoint'leri

| # | Servis | Endpoint | Port | Sıklık | Timeout |
|---|--------|----------|------|--------|---------|
| 1 | Control Service | `/health` | 81 | 10s | 5s |
| 2 | Media Service | `/health` | 5000 | 10s | 5s |
| 3 | Audio Service | `/health` | 9741 | 10s | 5s |
| 4 | Device Service | `/health` | — | 30s | 10s |
| 5 | Network Audio | `/health` | — | 30s | 10s |
| 6 | AI Service | `/health` | — | 60s | 15s |
| 7 | Download Service | `/health` | 3001 | 30s | 10s |

---

## 3. Health Check Response Formatı

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

## 4. Sağlık Durumları

| Durum | Kod | Anlam | Aksiyon |
|-------|-----|-------|---------|
| **Healthy** | 200 | Servis tam çalışıyor | Devam |
| **Degraded** | 207 | Yavaş yanıt (>1s) veya kısmi hata | Uyar, devam |
| **Unhealthy** | 503 | Servis çalışmıyor | Retry → Fallback → Escalation |

---

## 5. Servis Başlatma Sırası (Boot Order)

```
┌─────────────────────────────────────────────────────────────┐
│ PHASE 1: Altyapı (0-5s)                                    │
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                 │
│  │ MySQL 9  │  │ Redis    │  │ APCu     │                 │
│  │ :3306    │  │ :6379    │  │ (PHP)    │                 │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘                 │
│       │              │              │                       │
└───────┼──────────────┼──────────────┼───────────────────────┘
        │              │              │
┌───────┼──────────────┼──────────────┼───────────────────────┐
│ PHASE 2: Core Servisler (5-15s)                            │
│       │              │              │                       │
│  ┌────▼─────┐        │              │                       │
│  │ Control  │◄───────┘              │                       │
│  │ Service  │                       │                       │
│  │ :81      │                       │                       │
│  └────┬─────┘                       │                       │
│       │                             │                       │
└───────┼─────────────────────────────┼───────────────────────┘
        │                             │
┌───────┼─────────────────────────────┼───────────────────────┐
│ PHASE 3: Medya Servisleri (15-30s)                         │
│       │                             │                       │
│  ┌────▼─────┐  ┌──────────┐        │                       │
│  │ Media    │  │ Download │        │                       │
│  │ Service  │  │ Service  │        │                       │
│  │ :5000    │  │ :3001    │        │                       │
│  └────┬─────┘  └────┬─────┘        │                       │
│       │              │              │                       │
└───────┼──────────────┼──────────────┼───────────────────────┘
        │              │              │
┌───────┼──────────────┼──────────────┼───────────────────────┐
│ PHASE 4: Ses & AI (30-45s)                                 │
│       │              │              │                       │
│  ┌────▼─────┐  ┌────▼─────┐  ┌────▼─────┐                 │
│  │ Audio    │  │ AI       │  │ Device   │                 │
│  │ Service  │  │ Service  │  │ Service  │                 │
│  │ :9741    │  │ (int)    │  │ (BLE)    │                 │
│  └──────────┘  └──────────┘  └──────────┘                 │
│                                                             │
│  ┌──────────┐                                              │
│  │ Network  │                                              │
│  │ Audio    │                                              │
│  │ (P2P)    │                                              │
│  └──────────┘                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. Health Check Akışı

```
Timer tetiklendi (her 10s)
  → Servise GET /health isteği
    → Yanıt kontrolü
      → 200 OK → Healthy → Devam
      → 207 Degraded → Uyarı logu → Devam
      → Timeout/503 → Unhealthy → Retry (max 3)
        → Retry başarısız → Fallback zincirini tetikle
          → Fallback başarısız → Escalation (L1 → L2 → L3)
```

---

## 7. Bağımlılık Kontrolü

Her servis kendi bağımlılıklarını health check'e dahil eder:

| Servis | Kontrol Edilen Bağımlılıklar |
|--------|------------------------------|
| Control | MySQL, APCu, Session store |
| Media | MySQL, FFmpeg, Dosya sistemi |
| Audio | ASIO/WASAPI driver, Neva Engine |
| Device | BLE stack, WiFi, USB |
| Network Audio | WebRTC, P2P mesh |
| AI | MySQL, Model dosyaları |
| Download | MySQL, Node.js process, Disk alanı |

---

## 8. Circuit Breaker

| Senaryo | Eşik | Aksiyon | Recovery |
|---------|------|---------|----------|
| Servis 3 kez art arda 503 döndürürse | 3 failure | Circuit OPEN (30s) | Half-open after 30s |
| Circuit OPEN iken istek gelirse | — | Fallback kullan | — |
| Half-open'da 1 başarılı istek | 1 success | Circuit CLOSED | Normal devam |

---

## 9. Retry Stratejisi

| Parametre | Değer |
|-----------|-------|
| Max Retry | 3 |
| Initial Delay | 100ms |
| Max Delay | 5000ms |
| Backoff | Exponential (x2) |
| Jitter | ±20% |

---

## 10. Monitoring & Alerting

| Metrik | Eşik | Alert |
|--------|------|-------|
| Servis availability | <99.9% | CRITICAL |
| Yanıt süresi (p95) | >1s | WARN |
| Yanıt süresi (p99) | >5s | ERROR |
| Hata oranı | >1% | ERROR |
| Circuit breaker OPEN | Herhangi | CRITICAL |
| DB bağlantı havuzu dolu | >90% | WARN |

---

## 11. Cross References

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/error-recovery]] | Hata kurtarma |
| [[ecosystem/service-communication]] | İletişim protokolleri |
| [[architecture/03-services]] | Servis detayları |
| [[architecture/00-overview/architecture-master]] | Canonical counts |

---

## 12. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Service Count** | 7 |
| **Health States** | 3 (healthy, degraded, unhealthy) |
| **Circuit Breaker** | ✅ |
| **Retry Strategy** | ✅ Exponential backoff |
| **Boot Phases** | 4 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
