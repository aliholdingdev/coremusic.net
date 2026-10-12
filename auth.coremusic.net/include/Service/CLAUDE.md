---
title: "CoreMusic — auth.coremusic.net/include/Service Bağlam"
type: context
folder: "auth.coremusic.net/include/Service"
category: layer5-services
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Service — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../shared/CLAUDE.md]]

---

## 1. Bağlam

İş mantığı servisleri. AuthService login/register/logout işlemlerini, SessionManager session lifecycle'ı yönetir.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 2 PHP dosyası |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `AuthService.php` | Login, register, logout iş mantığı |
| `SessionManager.php` | Session lifecycle yönetimi |

---

## 3. AuthService Detayları

### 3.1 Metotlar

| Metot | Görev |
|-------|-------|
| `login(LoginRequest)` | Email+şifre doğrulama, session başlatma |
| `register(RegisterRequest)` | Yeni kullanıcı oluşturma, Argon2id hash |
| `logout()` | Session sonlandırma |
| `getCurrentUser()` | Mevcut kullanıcı bilgisi |

### 3.2 Login Akışı

```
AuthService::login(LoginRequest)
 → UserRepository::findByEmail()
 → Password::verify() (Argon2id)
 → SessionManager::start()
 → AuthResponse döndür
```

### 3.3 Register Akışı

```
AuthService::register(RegisterRequest)
 → Password::hash() (Argon2id)
 → UserRepository::create()
 → SessionManager::start()
 → AuthResponse döndür
```

---

## 4. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Service'den HTTP response döndürmek | Katman ihlali |
| 2 | Service'den direkt session yönetimi | SessionManager kullanılır |
| 3 | Password hash'i service dışında üretmek | Tek nokta |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
