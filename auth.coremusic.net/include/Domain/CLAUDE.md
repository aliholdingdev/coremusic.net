---
title: "CoreMusic — auth.coremusic.net/include/Domain Bağlam"
type: context
folder: "auth.coremusic.net/include/Domain"
category: layer4-domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Domain — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../shared/CLAUDE.md]]

---

## 1. Bağlam

Domain katmanı (DDD). Entity, Value Object ve DTO tanımları. Framework bağımsız, saf iş mantığı.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Alt klasör | 3 (DTO, Entity, ValueObject) |

### 2.1 Alt Klasör Yapısı

```
Domain/
├── DTO/
│ ├── AuthResponse.php ← Auth yanıt DTO'su
│ ├── LoginRequest.php ← Login istek DTO'su
│ └── RegisterRequest.php ← Register istek DTO'su
├── Entity/
│ └── User.php ← User aggregate root
└── ValueObject/
 ├── Email.php ← Email value object (immutable)
 ├── Gender.php ← Gender value object (female/male/neutral)
 ├── Password.php ← Password value object (Argon2id hash)
 └── UserId.php ← UserId value object (UUID v7)
```

---

## 3. Domain Kuralları

| Kural | Detay |
|-------|-------|
| Value Object immutable | Değişmez nesneler |
| Entity identity-based | User ID ile eşitlik |
| DTO data transfer | Layer'lar arası veri taşıma |
| Framework bağımsız | Domain'den framework import edilmez |

---

## 4. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Domain'den superglobal erişimi | Katman ihlali |
| 2 | Domain'den DB import | Bağımlılık |
| 3 | Value Object'de mutation | Immutability ihlali |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
