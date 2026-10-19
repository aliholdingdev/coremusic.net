---
title: "CoreMusic — shared/src/Exception Bağlam"
type: context
folder: "shared/src/Exception"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Exception — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]]

---

## 1. Bağlam

Exception hiyerarşisi. BaseCoreMusicException'dan türeyen 8 özel exception.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 8 PHP dosyası |

### 2.1 Exception Hiyerarşisi

```
BaseCoreMusicException
├── AuthenticationException ← Auth hataları
├── AuthorizationException ← Yetki hataları
├── ValidationException ← Validasyon hataları
├── NotFoundException ← Bulunamayan kaynaklar
├── ConflictException ← Çakışma hataları
├── RateLimitException ← Rate limit aşımı
├── DatabaseException ← DB hataları
└── ExternalServiceException ← Dış servis hataları
```

---

## 3. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Generic Exception fırlatmak | Tanımsız hata |
| 2 | Exception'da secret log'lamak | Güvenlik |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
