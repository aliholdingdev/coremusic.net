---
title: "CoreMusic — auth.coremusic.net/include/Controller Bağlam"
type: context
folder: "auth.coremusic.net/include/Controller"
category: layer2-routing
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Controller — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../shared/CLAUDE.md]]

---

## 1. Bağlam

HTTP kontrolörleri. AuthController tüm auth route'larını yönetir. Hexagonal mimaride Controller → Handler → Service → Repository akışı.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 1 PHP dosyası |
| Controller | AuthController.php |
| Pattern | Single controller, all routes |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `AuthController.php` | Tüm auth HTTP isteklerini yönetir (login, register, logout, password reset, gender) |

---

## 3. AuthController Detayları

### 3.1 Yönetilen Route'lar

| Method | URL | Handler |
|--------|-----|---------|
| GET | /login | AuthKeyRedirect |
| POST | /login | AuthPost |
| GET | /register | AuthKeyRedirect |
| POST | /register | AuthPost |
| GET | /forgot-password | AuthKeyRedirect |
| POST | /forgot-password | AuthPost |
| GET | /reset-password | AuthKeyRedirect |
| POST | /reset-password | AuthPost |
| GET | /select-gender | AuthKeyRedirect |
| POST | /set-gender | AuthPost |
| GET | /logout | AutoRedirect |

### 3.2 Request Akışı

```
HTTP Request → AuthController::handle()
 → Route eşleştir
 → Handler seç (AuthKeyRedirect / AuthPost / AutoRedirect)
 → Handler::process()
 → AuthService::login() / register() / logout()
 → Response döndür
```

---

## 4. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Controller'a iş mantığı gömmek | SRP ihlali (mantık Service'te) |
| 2 | Controller'dan direkt DB erişimi | Katman ihlali |
| 3 | Response'a hardcoded header | Config-based |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
