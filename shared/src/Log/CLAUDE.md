---
title: "CoreMusic — shared/src/Log Bağlam"
type: context
folder: "shared/src/Log"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Log — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k12-izleme]]

---

## 1. Bağlam

Loglama altyapısı. PSR-3 uyumlu structured logging. Hata, audit trail ve performans logları.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 2 PHP dosyası |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `LoggerFactory.php` | Logger üretim fabrikası |
| `FileHandler.php` | Dosya tabanlı log handler |

---

## 3. Log Seviyeleri

| Seviye | Kullanım |
|--------|----------|
| DEBUG | Geliştirme bilgileri |
| INFO | İşlem bilgileri |
| WARNING | Uyarılar |
| ERROR | Hatalar |
| CRITICAL | Kritik hatalar (security, layer violation) |

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Middleware/CLAUDE.md]] | Middleware logging |
| Referans | [[../../.ai/architecture/k12-izleme]] | İzleme mimarisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
