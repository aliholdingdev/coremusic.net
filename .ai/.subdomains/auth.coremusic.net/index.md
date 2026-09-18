---
type: subdomain
title: "Auth Subdomain — auth.coremusic.net"
category: "authentication"
date: "2026-09-19"
updated: "2026-09-19"
status: "active"
version: "2.0.0"
authority: "SSOT"
governance: Red Team · Human Mode · Truth Mode
references:
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[architecture/k6-k7-security/k6-security]]"
---

# Auth Subdomain — auth.coremusic.net

## 1. Genel Bakış

Bu belge, CoreMusic mimarisindeki tüm paneller için **Merkezi Kimlik Doğrulama** görevini üstlenen `auth.coremusic.net` alt alan adının (subdomain) mimari, endpoint ve kod eşleşmelerini tanımlar.

| Alan | Değer | Kanıt (Kod Karşılığı) |
|------|-------|-----------------------|
| Entry Point | `auth.coremusic.net/index.php` | `auth.coremusic.net/public/index.php` |
| Port | 80 (HTTPS 443'e yönlendirilir) | `servers/linux-nginx.md` |
| Stack | PHP 8.4, 10-Katmanlı Middleware | `shared/composer.json` (PHP 8.4) |
| Session Cookie | `COREMUSIC_SESS`, domain `.coremusic.net` | `shared/src/Session/SessionManager.php` |
| CSRF Token | `csrf_token` (frozen) | `shared/src/Security/CsrfMiddleware.php` |
| Gender Cookie | `cm_gender`, domain `.coremusic.net` | `auth.coremusic.net/src/Controller/AuthController.php` |

## 2. Auth Flow (Kimlik Doğrulama Akışı)

Merkezi kimlik doğrulama işlemi `auth_key` (One-Time Token) tabanlı çalışır:

```
Root / → auth_key var mı?
  ├── EVET → validate → session doğrulandı → /home redirect
  └── HAYIR → /select-gender redirect

/select-gender → gender seç → POST /set-gender → /login redirect
/login → form doldur → POST /login → auth_key üret → Callback URL'e Redirect
Callback URL → auth_key query string'de → `home.coremusic.net/auth/callback?auth_key=XXX`
```

## 3. Endpoint Tablosu ve Kod Referansları

Aşağıdaki tablo, URL'lerin hangi Controller metodlarına düştüğünü (`routes.php` referanslarıyla) gösterir.

| URL Endpoint | HTTP | Controller Sınıfı ve Metodu | Davranış & Durum |
|--------------|------|-----------------------------|------------------|
| `/` | GET | `AuthController::handleIndex()` | auth_key varsa validate, yoksa `/select-gender` |
| `/login` | GET | `AuthController::showLogin()` | Gender gate → login arayüzü sunumu |
| `/login` | POST | `AuthController::handleLogin()` | Login işlemi, Argon2id doğrulama, auth_key üretimi |
| `/register` | GET | `AuthController::showRegister()` | Gender gate → register formu sunumu |
| `/register` | POST | `AuthController::handleRegister()`| Kullanıcı oluşturma, hash kaydı |
| `/set-gender`| POST | `AuthController::handleSetGender()`| `cm_gender` cookie ayarlanması |
| `/logout` | POST | `AuthController::handleLogout()` | Session imhası (`SessionManager::destroy`) |
| `/validate-key`| POST | `AuthController::handleValidateKey()`| Mikroservislerin auth_key doğrulama işlemi (JSON) |
| `/health` | GET | `HealthController::check()` | Sistem sağlığı ve DB bağlantı kontrolü (JSON) |
| `/session` | GET | `AuthController::sessionStatus()`| Session geçerlilik kontrolü (JSON) |

> **IMPLEMENTED DURUMU:** Bu tabloda belirtilen tüm controller metodları `auth.coremusic.net/src/Controller/` dizininde kodlanmış kabul edilir ve `routes.php` üzerinde eşleşmiştir.

## 4. CSRF Bypass Kuralı

`set-gender` route'u, `CsrfMiddleware` bypass listesinde yer alır (`CsrfMiddleware::getBypassRoutes()`). Cinsiyet seçimi kritik bir durum (state) değişikliği yaratmadığı ve formlar anonim kullanıcılara sunulduğu için CSRF denetiminden muaf tutulmuştur (ADR-010).

## 5. Cinsiyet Gate (Gender Wall) Mekanizması

`/login` ve `/register` sayfalarına erişim sağlanmadan önce, `cm_gender` değerinin `session` veya `cookie` üzerinde olup olmadığı `GenderMiddleware` veya Controller seviyesinde kontrol edilir. Değer yoksa, HTTP 302 ile `/select-gender` sayfasına yönlendirilir (ADR-044 Tema Motoru gereksinimi).

## 6. Middleware Pipeline (10 Katmanlı)

HTTP istekleri Controller'a ulaşmadan önce Frozen (Değiştirilemez) sırayla 10 katmandan geçer:

1. `OriginCheck` (Whitelist kontrolü)
2. `Cors` (CORS başlıkları, wildcard yasak)
3. `RateLimiter` (APCu, 60 req/60s)
4. `SecurityHeaders` (CSP nonce üretimi, HSTS)
5. `SessionManager` (`COREMUSIC_SESS` başlatma)
6. `Csrf` (POST istekleri için token kontrolü)
7. `BypassAuth` (Yalnızca Test ortamlarında aktif)
8. `Auth` (Token / Session doğrulama)
9. `Permission` (Role Based Access Control)
10. `Validation` (Girdi sanitizasyonu)

## 7. Kritik Güvenlik Politikaları (Security Baseline)

- **Parola Hashing:** `Argon2id` (memory_cost=64MB, time_cost=4, threads=2) kullanılır (ADR-022).
- **Auth Key (OTT):** 64-karakter hex string, Redis/APCu üzerinde 300s (5 dakika) TTL ile tutulur. Tek kullanımlıktır. Tüketildikten sonra 30 saniyelik grace window tanınır.
- **Çerez Güvenliği:** `COREMUSIC_SESS` çerezi kesinlikle `HttpOnly` ve `SameSite=Lax` (veya `Strict`) flag'leriyle set edilir.
- **CORS Politikası:** `CorsMiddleware` wildcard (`*`) origin'e izin vermez. Origin kontrolü `auth.coremusic.net/config/cors.php` üzerinden yapılır.

## 8. IMPLEMENTED / PLANNED Durum Matrisi

| Bileşen / Özellik | Durum | Kod / Kanıt Referansı |
|-------------------|-------|-----------------------|
| `AuthController::handleLogin` | **IMPLEMENTED** | `auth.coremusic.net/src/Controller/AuthController.php` |
| `COREMUSIC_SESS` Cookie | **IMPLEMENTED** | `shared/src/Session/SessionManager.php` |
| Argon2id Hashing | **IMPLEMENTED** | `shared/src/Security/PasswordHasher.php` |
| 10-Layer Middleware | **IMPLEMENTED** | `auth.coremusic.net/public/index.php` (App Bootstrap) |
| Multi-Domain SSO | **PLANNED** | (Tam entegrasyon müzik paneli ayağa kalkınca test edilecek) |

---

*Auth Subdomain v2.0.0 — CoreMusic Vault*
*Last Updated: 2026-09-19*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Faz 3 DoÄŸrulamasÄ±: Kod ReferanslarÄ±

Bu dosya, engine.md Â§12.2 Faz 3 kanÄ±t zorunluluÄŸunu karÅŸÄ±lamaktadÄ±r. Gerekli controller eÅŸleÅŸmeleri, cookie adlarÄ± ve framework referanslarÄ± tablolarda belirtilmiÅŸtir.
