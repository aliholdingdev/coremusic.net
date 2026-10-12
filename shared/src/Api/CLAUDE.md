---
title: "CoreMusic — shared/src/Api Bağlam"
type: context
folder: "shared/src/Api"
category: layer2-routing
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Api — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k9-api-routing]]

---

## 1. Bağlam

API Gateway katmanı (ADR-084). BFF×6, DTO, Middleware×6, Registry ve Versioning bileşenleri. Tüm istemciler API Gateway üzerinden bağlanır.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 10+ PHP dosyası (Api/, Api/Bff/, Api/Dto/, Api/Middleware/, Api/Registry/, Api/Versioning/) |
| BFF | 6 (Desktop, Embedded, Mobile, Spa, Admin, Car) |
| API Middleware | 6 (Auth, Authorization, RateLimit, Validation, CorrelationId, Logging) |
| ADR | ADR-084 (API Gateway Architecture) |

---

## 3. API-First Kuralı

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

**Kod hiçbir zaman sözleşmeden önce yazılmaz.**

---

## 4. BFF Haritası

| BFF | Hedef İstemci | Response |
|-----|---------------|----------|
| SpaBff | SPA (React/Vanilla) | Tam veri |
| MobileBff | Mobil uygulama | Minimal |
| EmbeddedBff | RPi5 (embedded) | Ultra-minimal, gzip |
| DesktopBff | Masaüstü uygulama | Orta boy |
| AdminBff | Admin paneli | Full + audit |
| CarBff | Araç içi | Touch-optimized |

---

## 5. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../home.coremusic.net/CLAUDE.md]] | SPA API kullanımı |
| Referans | [[../../.ai/architecture/k9-api-routing]] | API/Router mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-084-api-gateway-architecture]] | API Gateway |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
