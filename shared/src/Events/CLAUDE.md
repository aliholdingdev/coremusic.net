---
title: "CoreMusic — shared/src/Events Bağlam"
type: context
folder: "shared/src/Events"
category: layer2-routing
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Events — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k8-servis]]

---

## 1. Bağlam

Olay tabanlı mimari (ADR-086). PSR-14 EventDispatcher ile servisler arası iletişim. Servisler birbirini doğrudan çağırmaz, event yayınlar.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 3 PHP dosyası (Events/, Events/Domain/, Events/Integration/) |
| Domain event | 9 |
| Integration event | 3 |
| ADR | ADR-086 (Event Driven Architecture) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `EventDispatcher.php` | PSR-14 EventDispatcher wrapper |
| `Domain/*.php` | 9 domain event |
| `Integration/*.php` | 3 integration event |

---

## 3. Event Akışı

```
Service A → Event Bus (PSR-14) → Service B, C, D
```

**Servisler birbirini doğrudan çağırmaz, event yayınlar.**

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Contracts/Events/CLAUDE.md]] | Event sözleşmeleri |
| Referans | [[../../.ai/architecture/k8-servis]] | Servis mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-086-event-driven-architecture]] | Event Driven |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
