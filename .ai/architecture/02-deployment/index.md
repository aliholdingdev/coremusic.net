---
type: architecture
category: deployment
title: "Deployment Architecture — CoreMusic Deployment Mimarisi"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Deployment Architecture — CoreMusic Deployment Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-004-multi-domain-spa]] · [[decisions/accepted/ADR-027-dual-mode-storage-strategy]]

---

## 1. Amaç

CoreMusic'in deployment stratejisi, CI/CD pipeline, container yönetimi ve monitoring altyapısını tanımlar.

---

## 2. Deployment Modları

| Mod | Platform | Donanım | Servis |
|-----|----------|---------|--------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop | Tüm servisler |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 | Audio + Media |
| Professional Studio | Windows | 8.1 Surround | Audio + Control |
| NAS Audio Server | Linux | Synology/QNAP | Media + Download |
| DAC Control System | Windows/Linux | XMOS XU316 | Audio |

---

## 3. CI/CD Pipeline

| Aşama | Araç | Amaç |
|-------|------|------|
| Code | Git | Versiyon kontrolü |
| Lint | PHPStan, ESLint | Kod kalitesi |
| Test | PHPUnit, Vitest | Unit test |
| Security | GitLeaks | Secret tarama |
| Build | Composer, npm | Bağımlılık |
| Deploy | PowerShell/Bash | Otomatik deploy |
| Health | Health check | Doğrulama |

---

## 4. Container Stratejisi

| Servis | Container | Image |
|--------|-----------|-------|
| Download Service | Server | node:lts |
| Media Service | Server | php:8.4-apache |
| MySQL | Server | mysql:9 |
| Redis | Server | redis:alpine |

---

## 5. Monitoring

| Metrik | Hedef | Kaynak |
|--------|-------|--------|
| Uptime | >%99.9 | Health check |
| TTFB | <200ms | APM |
| Error rate | <%1 | Log analizi |
| CPU | <%80 | Sistem monitor |
| Disk | <%80 | Sistem monitor |

---

## 6. Cross References

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Deployment kuralları |
| [[architecture/00-overview]] | Genel bakış |
| [[architecture/06-audio]] | Servis detayları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode