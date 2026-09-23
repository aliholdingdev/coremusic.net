---
title: "CoreMusic — shared/src/Interfaces Bağlam"
type: context
folder: "shared/src/Interfaces"
category: layer4-domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Interfaces — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]]

---

## 1. Bağlam

Katmanlar arası arayüz tanımları. Auth, Config, Database, Middleware, Security interface'leri.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Alt klasör | 5 (Auth, Config, Database, Middleware, Security) |

| Klasör | Amaç |
|--------|------|
| `Interfaces/Auth/` | Auth interface'leri |
| `Interfaces/Config/` | Config interface'leri |
| `Interfaces/Database/` | Database interface'leri |
| `Interfaces/Middleware/` | Middleware interface'leri |
| `Interfaces/Security/` | Security interface'leri |

---

## 3. Interface Kuralları

| Kural | Detay |
|-------|-------|
| Tek sorumluluk | Her interface tek bir amaç |
| Geriye uyumluluk | Interface değişimi breaking change |
| Dependency injection | Constructor ile enjekte |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
