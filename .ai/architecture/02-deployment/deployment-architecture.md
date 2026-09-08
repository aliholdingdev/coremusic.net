---
title: "CoreMusic — Deployment Architecture"
type: architecture
category: deployment
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Deployment Architecture

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[ADR-039-7-service-platform-architecture]]

---

## 1. Amaç

Deployment altyapısını ve stratejilerini tanımlar. CI/CD, monitoring ve rollback.

---

## 2. Deployment Modes

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 |

---

## 3. Servis Mimarisi

```yaml
services:
  control-service:
    port: 81
  
  media-service:
    port: 5000/6000
  
  download-service:
    port: 3001
  
  mysql:
    port: 3306
```

---

## 4. CI/CD Pipeline

```
Push → Lint → Test → Build → Deploy → Health Check → Monitor
 ↓        ↓       ↓       ↓         ↓            ↓          ↓
Git    PHP lint  PHPUnit Build   SSH/CD    Endpoint    Alerting
```

---

## 5. Health Checks

| Servis | Endpoint | Interval |
|--------|----------|----------|
| Control | /health | 30s |
| Media | /health | 30s |
| Download | /health | 30s |
| MySQL | /health | 60s |

---

## 6. Rollback Strategy

| Durum | Aksiyon |
|-------|---------|
| Health check fail | Auto rollback |
| Error rate >5% | Manual rollback |
| Data corruption | Git restore |

---

## 7. Monitoring

| Metrik | Hedef | Alarm |
|--------|-------|-------|
| Uptime | >99.9% | <99% |
| Response time | <200ms | >500ms |
| Error rate | <1% | >5% |
| CPU usage | <80% | >90% |

---

## 8. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Modes | [[ADR-039-7-service-platform-architecture]] | 7 servis |
| § 4 CI/CD | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
