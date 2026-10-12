---
title: "CoreMusic — shared/src/Session Bağlam"
type: context
folder: "shared/src/Session"
category: layer1-security
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Session — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k6-guvenlik]]

---

## 1. Bağlam

Oturum lifecycle yönetimi (ADR-011). Session başlatma, sürdürme, sonlandırma ve timeout işlemleri. CSP nonce'u session'a kaydeder.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 4 PHP dosyası |
| ADR | ADR-011 (session management) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `SessionBootstrapper.php` | Session başlatma ve yapılandırma |
| `SessionConfig.php` | Session ayarları (timeout, cookie, name) |
| `SessionInitializer.php` | Session ilk değerlerini ayarlama |
| `SessionLifecycle.php` | Session yaşam döngüsü yönetimi |

---

## 3. Session Yapılandırması

| Parametre | Değer |
|-----------|-------|
| Session name | `COREMUSIC_SESS` |
| Timeout | 3600s idle (1 saat) |
| Cookie | HTTPOnly, Secure, SameSite=Lax |
| Storage | File-based (başlangıç) → DB (planlanan) |
| CSP nonce | `$_SESSION['csp_nonce']` |

---

## 4. Session Lifecycle

```
1. Request gelir → SessionManagerMiddleware
2. Session başlat (yoksa) veya devam et (varsa)
3. CSP nonce üret → session'a kaydet
4. Request işlenir
5. Response gönderilir
6. Session kapat (idle timeout kontrolü)
```

---

## 5. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Middleware/CLAUDE.md]] | SessionManagerMiddleware |
| Kullanıcı | [[../Security/CLAUDE.md]] | Session key yönetimi |
| Referans | [[../../.ai/architecture/k6-guvenlik]] | Güvenlik mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-011-session-management]] | Session yönetimi |

---

## 6. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Session'dan auth bilgisini log'a yazmak | Gizlilik |
| 2 | Session timeout'u kaldırma | Session hijacking |
| 3 | Cookie flag'lerini kaldırmak | Güvenlik |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
