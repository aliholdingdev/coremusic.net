---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Hardcoded Config Audit Report"
type: report
category: security-audit
date: 2026-09-23
updated: 2026-09-23
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/reports/hardcoded-config-audit.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/WORKFLOW.md · .ai/brain.md · .ai/index.md"
---

# CoreMusic — Hardcoded Config Audit Report

> Tum PHP dosyalarinda hardcoded config degerleri icin kapsamli tarama.
> Tarama Alanlari: home.coremusic.net/config/, auth.coremusic.net/config/, shared/src/, auth.coremusic.net/include/, home.coremusic.net/include/

---

## 1. OZET

| Metrik | Deger |
|--------|-------|
| Toplam Bulgu | 72 |
| CRITICAL | 8 |
| HIGH | 18 |
| MEDIUM | 28 |
| LOW | 18 |
| Taranan Dosya | 40+ PHP dosyasi |
| Taranan Dizin | 5 (home/config, auth/config, shared/src, auth/include, home/include) |

### Oncelik Dagilimi

| Oncelik | Sayi | Oran |
|---------|------|------|
| CRITICAL | 8 | %11 |
| HIGH | 18 | %25 |
| MEDIUM | 28 | %39 |
| LOW | 18 | %25 |

---

## 2. CRITICAL BULGULAR (8)

Bu bulgular acil duzeltilmelidir — guvenlik acigi veya uretim riski tasir.

### C-01: CORS Whitelist Tamamen Hardcoded (auth.coremusic.net)
- **Dosya:** `auth.coremusic.net/config/cors.php` Satir 11-23
- **Mevcut Deger:** Tum allowed_origins, allowed_methods, allowed_headers, dev_fallback hardcoded
- **Ornek:**
  ```php
  'allowed_origins' => [
      'home' => 'home.coremusic.net',
      'music' => 'music.coremusic.net',
      // ...
  ],
  'dev_fallback' => [
      'http://home.coremusic.net',
      'http://music.coremusic.net',
  ],
  ```
- **Onerilen .env:** `CORS_ALLOWED_ORIGINS=home.coremusic.net,music.coremusic.net,...`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env
- **Neden CRITICAL:** CORS whitelist'i kodda degistirmek deploy gerektirir. Yeni subdomain eklemek icin kod degisikligi gerekir. Dev fallback'ler production'da tehlikeli olabilir.

### C-02: Origin Check Whitelist Hardcoded (auth.coremusic.net)
- **Dosya:** `auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php` Satir 13-24
- **Mevcut Deger:** `ALLOWED_ORIGINS` sabiti ile 10 host hardcoded
- **Ornek:**
  ```php
  private const ALLOWED_ORIGINS = [
      'auth.coremusic.net', 'music.coremusic.net', 'admin.coremusic.net',
      'home.coremusic.net', 'api.coremusic.net', 'media.coremusic.net',
      'download.coremusic.net', 'coremusic.net', 'localhost', '127.0.0.1',
  ];
  ```
- **Onerilen .env:** `ORIGIN_ALLOWED_HOSTS=auth.coremusic.net,music.coremusic.net,...`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env
- **Neden CRITICAL:** Guvenlik duvari (firewall) kurali kodda sabitlenmis. Yeni subdomain eklemek icin kod degisikligi + deploy gerekir.

### C-03: Trusted Proxies Hardcoded (home + auth constants.php)
- **Dosya:** `home.coremusic.net/config/constants.php` Satir 96, `auth.coremusic.net/config/constants.php` Satir 82
- **Mevcut Deger:** `['127.0.0.1', '::1']` — .env'den okunmuyor, direkt define()
- **Onerilen .env:** `TRUSTED_PROXIES=127.0.0.1,::1`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env
- **Neden CRITICAL:** Production'da cloud load balancer IP'leri (AWS ALB, Cloudflare, vb.) trusted proxy listesine eklenmelidir. Hardcoded oldugunda X-Forwarded-For header'lari dikkate alinmaz ve gercek client IP kaybolur.

### C-04: Cookie Domain Hardcoded 3 Farkli Yerde
- **Dosya 1:** `shared/src/Session/SessionConfig.php` Satir 45 — `'.coremusic.net'`
- **Dosya 2:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 71 — `'.coremusic.net'`
- **Dosya 3:** `auth.coremusic.net/include/Controller/AuthController.php` Satir 247 — `'.coremusic.net'`
- **Dosya 4:** `home.coremusic.net/include/Container/HomeContainer.php` Satir 38 — `'.coremusic.net'`
- **Onerilen .env:** `SESSION_COOKIE_DOMAIN=.coremusic.net`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env
- **Neden CRITICAL:** Cross-domain session paylasimi icin cookie domain hayati onem tasir. 4 farkli yerde hardcoded olmasi tutarsizlik riski tasir. Local dev'de `.localhost` gibi farkli degerler gerekir.

### C-05: Session Name Hardcoded 5 Farkli Yerde
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 80
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 46
- **Dosya 3:** `shared/src/Session/SessionConfig.php` Satir 31 — fallback `'COREMUSIC_SESS'`
- **Dosya 4:** `shared/src/Session/SessionInitializer.php` Satir 26 — fallback `'COREMUSIC_SESS'`
- **Dosya 5:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 59 — fallback `'COREMUSIC_SESS'`
- **Onerilen .env:** `SESSION_NAME=COREMUSIC_SESS`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env
- **Neden CRITICAL:** Session name degistirildiginde tum aktif session'lar invalid olur. 5 farkli yerde fallback olmasi, dogru degerin kullanilip kullanilmadigini kontrol etmeyi imkansiz hale getirir.

### C-06: CSRF Token Length, Rate Limit Max/Window Hardcoded 2+ Yerde
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 81-83
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 55-57
- **Dosya 3:** `shared/src/Api/Middleware/RateLimitMiddleware.php` Satir 22-23 — `DEFAULT_MAX_REQUESTS = 60`, `DEFAULT_WINDOW_SECONDS = 60`
- **Onerilen .env:** `CSRF_TOKEN_LENGTH=32`, `RATE_LIMIT_MAX=60`, `RATE_LIMIT_WINDOW=60`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env
- **Neden CRITICAL:** Guvenlik parametreleri merkezi olarak yonetilmeli. Farkli subdomain'lerde farkli rate limit degerleri olmasi guvenlik acigi yaratabilir.

### C-07: SessionMiddleware Windows-Specific Save Path + 0777 Perms
- **Dosya:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 61-64
- **Mevcut Deger:** `$savePath = ini_get('session.save_path') ?: 'C:\temp'; @mkdir($savePath, 0777, true);`
- **Onerilen .env:** `SESSION_SAVE_PATH=/var/lib/coremusic/sessions` (Linux) veya `%TEMP%\coremusic_sessions` (Windows)
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env
- **Neden CRITICAL:** (1) `'C:\temp'` Windows-specific, production Linux'ta calismaz. (2) `0777` izni cok genis — her kullanici session dosyasini okuyabilir/silebilir. (3) Bu deger .env'den okunmuyor.

### C-08: DatabaseConfig Default 'root' Kullanici
- **Dosya:** `shared/src/Database/Config/DatabaseConfig.php` Satir 10
- **Mevcut Deger:** `public readonly string $user = 'root'`
- **Onerilen .env:** `DB_USER=` (bos birakilmali)
- **Tasinmasi Gereken Yer:** shared/config/domain.php veya ilgili .env
- **Neden CRITICAL:** Root kullanicisi ile baglanti acmak guvenlik acigi. Default deger olarak root olmamali.

---

## 3. HIGH BULGULAR (18)

Hizli duzeltilmeli — konfigurasyon bakimi ve tutarsizlik riski tasir.

### H-01: APP_VERSION Hardcoded Default '2.3.0' (2 Dosyada)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 30
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 30
- **Mevcut Deger:** `$env('APP_VERSION', '2.3.0')`
- **Onerilen .env:** `APP_VERSION=2.3.0`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env

### H-02: APP_TIMEZONE Hardcoded Default 'Europe/Istanbul' (3 Dosyada)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 31
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 31
- **Dosya 3:** `shared/src/Bootstrap/RuntimeBootstrap.php` Satir 9 — `$_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul'`
- **Mevcut Deger:** `'Europe/Istanbul'`
- **Onerilen .env:** `APP_TIMEZONE=Europe/Istanbul`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env

### H-03: SESSION_LIFETIME Hardcoded 7200 (auth) / 3600 (shared) — Tutarsiz
- **Dosya 1:** `auth.coremusic.net/config/constants.php` Satir 47 — `7200` (2 saat)
- **Dosya 2:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 13 — `3600` (1 saat)
- **Dosya 3:** `shared/src/Session/SessionConfig.php` Satir 18 — `3600` (1 saat)
- **Mevcut Deger:** Farkli yerlerde farkli degerler (7200 vs 3600)
- **Onerilen .env:** `SESSION_LIFETIME=3600` (tek kaynak)
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env + auth.coremusic.net/config/.env
- **Neden HIGH:** Session suresi 2 farkli degerde tanimli — 7200 (constants.php) ve 3600 (SessionMiddleware). Hangisinin gecerli oldugu belirsiz.

### H-04: ReturnUrlPolicy ALLOWED_HOSTS Hardcoded
- **Dosya:** `shared/src/Security/ReturnUrlPolicy.php` Satir 7-15
- **Mevcut Deger:** 7 host hardcoded
- **Onerilen .env:** `RETURN_URL_ALLOWED_HOSTS=coremusic.net,home.coremusic.net,...`
- **Tasinmasi Gereken Yer:** shared/config/domain.php veya .env

### H-05: Allowed Auth Hosts Hardcoded (home constants.php)
- **Dosya:** `home.coremusic.net/config/constants.php` Satir 47
- **Mevcut Deger:** `$allowedAuthHosts = ['auth.coremusic.net', 'localhost', '127.0.0.1']`
- **Onerilen .env:** `ALLOWED_AUTH_HOSTS=auth.coremusic.net,localhost,127.0.0.1`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env

### H-06: AuthService Rate Limit Constants Hardcoded
- **Dosya:** `auth.coremusic.net/include/Service/AuthService.php` Satir 30-41
- **Mevcut Deger:**
  ```
  MIN_PASSWORD_LENGTH = 8
  MAX_LOGIN_ATTEMPTS = 5
  LOGIN_WINDOW_SECONDS = 900
  MAX_REGISTER_ATTEMPTS = 3
  REGISTER_WINDOW_SECONDS = 3600
  AUTH_KEY_TTL = 300
  PASSWORD_RESET_MAX_ATTEMPTS = 3
  PASSWORD_RESET_WINDOW_SECONDS = 3600
  ```
- **Onerilen .env:** `AUTH_MAX_LOGIN_ATTEMPTS=5`, `AUTH_LOGIN_WINDOW=900`, `AUTH_KEY_TTL=300`, `AUTH_MAX_REGISTER_ATTEMPTS=3`, `AUTH_PASSWORD_RESET_MAX=3`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env

### H-07: Auth Service Default IP '127.0.0.1' (4+ Dosyada)
- **Dosya 1:** `shared/src/Interfaces/Auth/IAuthService.php` Satir 7-12 — default parametre
- **Dosya 2:** `auth.coremusic.net/include/Service/AuthService.php` Satir 188, 194, 225
- **Dosya 3:** `auth.coremusic.net/include/Controller/AuthController.php` Satir 173, 278
- **Dosya 4:** `auth.coremusic.net/include/Handler/AutoRedirectHandler.php` Satir 58
- **Mevcut Deger:** `string $clientIp = '127.0.0.1'`
- **Onerilen .env:** `DEFAULT_CLIENT_IP=0.0.0.0` (fallback olarak)
- **Neden HIGH:** Default IP olarak localhost kullanilirsa, gercek IP alinamadiginda log'larda yaniltici bilgi olusur.

### H-08: CORS Max-Age Hardcoded '86400'
- **Dosya:** `shared/src/Middleware/CorsMiddleware.php` Satir 52
- **Mevcut Deger:** `'Access-Control-Max-Age' => '86400'`
- **Onerilen .env:** `CORS_MAX_AGE=86400`
- **Tasinmasi Gereken Yer:** shared/config/.env

### H-09: HSTS max-age Hardcoded 31536000
- **Dosya:** `auth.coremusic.net/include/Middleware/SecurityHeadersMiddleware.php` Satir 25
- **Mevcut Deger:** `header('Strict-Transport-Security: max-age=31536000; includeSubDomains')`
- **Onerilen .env:** `HSTS_MAX_AGE=31536000`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env

### H-10: Session Cookie Secure/SameSite Hardcoded (auth app.php)
- **Dosya:** `auth.coremusic.net/config/app.php` Satir 24-25
- **Mevcut Deger:** `'cookie_secure' => true`, `'cookie_samesite' => 'Lax'`
- **Onerilen .env:** `SESSION_COOKIE_SECURE=true`, `SESSION_COOKIE_SAMESITE=Lax`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env

### H-11: Gender Cookie Expiry Hardcoded (auth AuthController)
- **Dosya:** `auth.coremusic.net/include/Controller/AuthController.php` Satir 245
- **Mevcut Deger:** `time() + (86400 * 30)` (30 gun)
- **Onerilen .env:** `GENDER_COOKIE_EXPIRY=2592000`
- **Tasinmasi Gereken Yer:** auth.coremusic.net/config/.env

### H-12: Domain Port Hardcoded 80/81
- **Dosya:** `shared/config/domain.php` Satir 5-6, 17
- **Mevcut Deger:** `'primary_port' => 80`, `'subdomain_port' => 80`, `'home' => 81`
- **Onerilen .env:** `PRIMARY_PORT=80`, `HOME_PORT=81`
- **Tasinmasi Gereken Yer:** shared/config/.env

### H-13: Subdomain Port Hardcoded in app.php
- **Dosya:** `home.coremusic.net/config/app.php` Satir 28
- **Mevcut Deger:** `'subdomainPort' => 80`
- **Onerilen .env:** `SUBDOMAIN_PORT=80`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env

### H-14: Session Lifetime Hardcoded in app.php
- **Dosya:** `home.coremusic.net/config/app.php` Satir 32
- **Mevcut Deger:** `'lifetime' => 7200`
- **Onerilen .env:** `SESSION_LIFETIME=7200`
- **Tasinmasi Gereken Yer:** home.coremusic.net/config/.env

### H-15: DatabaseConfig Default DB Name 'coremusic'
- **Dosya:** `shared/src/Database/Config/DatabaseConfig.php` Satir 9
- **Mevcut Deger:** `public readonly string $dbName = 'coremusic'`
- **Onerilen .env:** `DB_NAME=coremusic` (veya kaldirilmali)
- **Neden HIGH:** Yanlis DB'ye baglanti acma riski.

### H-16: RateLimiter Fallback Trusted Proxies
- **Dosya:** `shared/src/Middleware/RateLimiterMiddleware.php` Satir 24
- **Mevcut Deger:** `['127.0.0.1', '::1']` (fallback)
- **Onerilen .env:** Zaten `TRUSTED_PROXIES` olarak tasinmali

### H-17: Api RateLimitMiddleware Default 60/60
- **Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` Satir 22-23
- **Mevcut Deger:** `DEFAULT_MAX_REQUESTS = 60`, `DEFAULT_WINDOW_SECONDS = 60`
- **Onerilen .env:** `API_RATE_LIMIT_MAX=60`, `API_RATE_LIMIT_WINDOW=60`
- **Tasinmasi Gereken Yer:** shared/config/.env

### H-18: Api Default IP '0.0.0.0'
- **Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` Satir 66
- **Mevcut Deger:** `$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'`
- **Onerilen .env:** N/A (fallback mantikli, ama tutarsiz — RateLimiterMiddleware'de `'0.0.0.0'` ayni)

---

## 4. MEDIUM BULGULAR (28)

Duzeltilmeli — konfigurasyon kalitesi ve bakim kolayligi icin onemlidir.

### M-01: DB_HOST Default 'localhost' (2 Dosya + 1 Config)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 117
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 36
- **Dosya 3:** `shared/src/Database/Config/DatabaseConfig.php` Satir 8
- **Onerilen .env:** `DB_HOST=localhost` (zaten .env'de tanimli, default'lar kaldirilmali)

### M-02: DB_PORT Default 3306 (3 Dosya)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 121
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 40
- **Dosya 3:** `shared/src/Database/Config/DatabaseConfig.php` Satir 12
- **Onerilen .env:** `DB_PORT=3306`

### M-03: DB_CHARSET Default 'utf8mb4' (3 Dosya)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 122
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 41
- **Dosya 3:** `shared/src/Database/Config/DatabaseConfig.php` Satir 13
- **Onerilen .env:** `DB_CHARSET=utf8mb4`

### M-04: Default Page 'home' Hardcoded
- **Dosya:** `home.coremusic.net/config/constants.php` Satir 75
- **Mevcut Deger:** `$env('DEFAULT_PAGE', 'home')`
- **Onerilen .env:** `DEFAULT_PAGE=home`

### M-05: Cookie SameSite 'Lax' Hardcoded 4+ Yerde
- **Dosya 1:** `shared/src/Session/SessionConfig.php` Satir 60
- **Dosya 2:** `shared/src/Session/SessionInitializer.php` Satir 46
- **Dosya 3:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 74
- **Dosya 4:** `auth.coremusic.net/include/Controller/AuthController.php` Satir 250
- **Onerilen .env:** `SESSION_COOKIE_SAMESITE=Lax`

### M-06: Cookie HttpOnly Hardcoded true (3 Dosya)
- **Dosya 1:** `shared/src/Session/SessionConfig.php` Satir 59
- **Dosya 2:** `shared/src/Session/SessionInitializer.php` Satir 45
- **Dosya 3:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 73
- **Onerilen .env:** `SESSION_COOKIE_HTTPONLY=true`

### M-07: Session Config COOKIE_EXPIRY 42000 Hardcoded
- **Dosya:** `shared/src/Session/SessionConfig.php` Satir 16
- **Mevcut Deger:** `public const COOKIE_EXPIRY = 42000`
- **Onerilen .env:** `SESSION_COOKIE_EXPIRY=42000`

### M-08: Session Config MAX_LIFETIME 1800 Hardcoded
- **Dosya:** `shared/src/Session/SessionConfig.php` Satir 17
- **Mevcut Deger:** `public const MAX_LIFETIME = 1800`
- **Onerilen .env:** `SESSION_MAX_LIFETIME=1800`

### M-09: Session Config IDLE_TIMEOUT 3600 Hardcoded
- **Dosya:** `shared/src/Session/SessionConfig.php` Satir 18
- **Mevcut Deger:** `public const IDLE_TIMEOUT = 3600`
- **Onerilen .env:** `SESSION_IDLE_TIMEOUT=3600`

### M-10: Session Config ROTATION_INTERVAL 1800 Hardcoded
- **Dosya:** `shared/src/Session/SessionConfig.php` Satir 19
- **Mevcut Deger:** `public const ROTATION_INTERVAL = 1800`
- **Onerilen .env:** `SESSION_ROTATION_INTERVAL=1800`

### M-11: Session Config DIR_PERMISSIONS 0750 Hardcoded
- **Dosya:** `shared/src/Session/SessionConfig.php` Satir 20
- **Mevcut Deger:** `public const DIR_PERMISSIONS = 0750`
- **Onerilen .env:** `SESSION_DIR_PERMISSIONS=0750`

### M-12: SessionMiddleware ROTATION_INTERVAL 900 (farkli deger)
- **Dosya:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` Satir 14
- **Mevcut Deger:** `private const ROTATION_INTERVAL = 900` (15 dk) vs SessionConfig 1800 (30 dk)
- **Onerilen .env:** `SESSION_ROTATION_INTERVAL=900` (tek kaynak — hangisi dogru?)

### M-13: Auth Container Fallback SESSION_NAME/Cookie Domain
- **Dosya:** `auth.coremusic.net/include/Container/AuthContainer.php` Satir 54-55
- **Mevcut Deger:** `SESSION_NAME ?? 'COREMUSIC_SESS'`, `SESSION_COOKIE_DOMAIN ?? '.coremusic.net'`
- **Onerilen .env:** Zaten tanimli, fallback'ler kaldirilmali

### M-14: CSRF Token Length Default 32 (2 Dosya)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 81
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 55
- **Onerilen .env:** `CSRF_TOKEN_LENGTH=32`

### M-15: Rate Limit Max Default 60 (2 Dosya + 1 Middleware)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 82
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 56
- **Dosya 3:** `shared/src/Middleware/RateLimiterMiddleware.php` Satir 18 — constructor default
- **Onerilen .env:** `RATE_LIMIT_MAX=60`

### M-16: Rate Limit Window Default 60 (2 Dosya + 1 Middleware)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 83
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 57
- **Dosya 3:** `shared/src/Middleware/RateLimiterMiddleware.php` Satir 19 — constructor default
- **Onerilen .env:** `RATE_LIMIT_WINDOW=60`

### M-17: AUTH_URL Default Hardcoded
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 110
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 104
- **Onerilen .env:** `AUTH_URL=http://auth.coremusic.net`

### M-18: MUSIC_URL Default Hardcoded
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 111
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 105
- **Onerilen .env:** `MUSIC_URL=http://home.coremusic.net`

### M-19: ASSETS_URL Default Hardcoded
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 112
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 106
- **Onerilen .env:** `ASSETS_URL=http://assets.coremusic.net`

### M-20: SecurityHeadersMiddleware Assets Origin Fallback
- **Dosya:** `shared/src/Middleware/SecurityHeadersMiddleware.php` Satir 86
- **Mevcut Deger:** `'https://assets.coremusic.net'` (fallback)
- **Onerilen .env:** `ASSETS_ORIGIN=https://assets.coremusic.net`

### M-21: CSP Nonce Hardcoded Domain References
- **Dosya:** `shared/src/Middleware/SecurityHeadersMiddleware.php` Satir 58-66
- **Mevcut Deger:** `fonts.googleapis.com`, `fonts.gstatic.com` hardcoded CSP'de
- **Onerilen .env:** `CSP_FONT_SOURCES=fonts.googleapis.com,fonts.gstatic.com`

### M-22: MemorySystem Cache TTL Hardcoded
- **Dosya:** `shared/src/AI/MemorySystem.php` Satir 33-37
- **Mevcut Deger:** `'l1' => 0`, `'l2' => 3600`, `'l3' => 86400`
- **Onerilen .env:** `AI_CACHE_TTL_L2=3600`, `AI_CACHE_TTL_L3=86400`

### M-23: OAuth Curl Timeout Hardcoded
- **Dosya:** `shared/src/OAuth/Provider/BaseOAuthProvider.php` Satir 80, 110
- **Mevcut Deger:** `CURLOPT_TIMEOUT => 30`
- **Onerilen .env:** `OAUTH_CURL_TIMEOUT=30`

### M-24: Bypass Default Values Hardcoded
- **Dosya:** `auth.coremusic.net/config/constants.php` Satir 75-77
- **Mevcut Deger:** `BYPASS_USER_UUID = '00000000000000000000000000000001'`, `BYPASS_ROLE = 'admin'`, `BYPASS_USERNAME = 'test_user'`
- **Onerilen .env:** Zaten .env'den okunuyor ama default'lar guvensiz

### M-25: AbstractComponent Assets URL Fallback
- **Dosya:** `home.coremusic.net/include/Class/AbstractComponent.php` Satir 60
- **Mevcut Deger:** `'http://assets.coremusic.net'` (fallback)
- **Onerilen .env:** `ASSETS_URL` zaten mevcut

### M-26: HomeSongButton Assets URL Fallback
- **Dosya:** `home.coremusic.net/include/Component/HomeSongButton.php` Satir 23
- **Mevcut Deger:** `'http://assets.coremusic.net'` (fallback)
- **Onerilen .env:** `ASSETS_URL` zaten mevcut

### M-27: SessionInitializer Fallback Cookie Domain
- **Dosya:** `shared/src/Session/SessionInitializer.php` (inline)
- **Mevcut Deger:** Fallback cookie domain hardcoded
- **Onerilen .env:** `SESSION_COOKIE_DOMAIN=.coremusic.net`

### M-28: AuthService Default Username Pattern Hardcoded
- **Dosya:** `auth.coremusic.net/include/Service/AuthService.php` Satir 36
- **Mevcut Deger:** `USERNAME_PATTERN = '/^[a-zA-Z0-9_]{3,30}$/'`
- **Onerilen .env:** `AUTH_USERNAME_PATTERN=^[a-zA-Z0-9_]{3,30}$`

---

## 5. LOW BULGULAR (18)

Iyilestirme onerileri — bakim kolayligi icin faydali.

### L-01: Root Path Hardcoded (2 Dosya)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 71
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 87
- **Mevcut Deger:** `dirname(__DIR__)`
- **Not:** Bu zorunlu bir hardcoded — PHP'de __DIR__ kullanilmali. Dikkat.

### L-02: Pages/Header/Footer Path Hardcoded (2 Dosya)
- **Dosya 1:** `home.coremusic.net/config/constants.php` Satir 72-74
- **Dosya 2:** `auth.coremusic.net/config/constants.php` Satir 88-90
- **Mevcut Deger:** `ROOT_PATH . '/pages'`, `ROOT_PATH . '/header.php'`, vb.
- **Not:** Zorunlu hardcoded — ROOT_PATH'e bagimli. Dikkat.

### L-03: Include/Config Path Hardcoded
- **Dosya:** `auth.coremusic.net/config/constants.php` Satir 89-90
- **Mevcut Deger:** `ROOT_PATH . '/include'`, `ROOT_PATH . '/config'`
- **Not:** Zorunlu hardcoded.

### L-04: Health Check Service Name Hardcoded
- **Dosya:** `home.coremusic.net/config/bootstrap.php` Satir 21
- **Mevcut Deger:** `'service' => 'home.coremusic.net'`
- **Onerilen .env:** `SERVICE_NAME=home.coremusic.net`

### L-05: Health Check Service Name Hardcoded (auth)
- **Dosya:** `auth.coremusic.net/include/Controller/AuthController.php` Satir 95
- **Mevcut Deger:** `'service' => 'auth.coremusic.net'`
- **Onerilen .env:** `SERVICE_NAME=auth.coremusic.net`

### L-06: RequestNormalizer Fallback 'localhost'
- **Dosya:** `shared/src/PageRouter/RequestNormalizer.php` Satir 24
- **Mevcut Deger:** `?: 'localhost'`
- **Not:** Fallback olarak 'localhost' makul.

### L-07: RequestNormalizer Script Name Hardcoded
- **Dosya:** `shared/src/PageRouter/RequestNormalizer.php` Satir 86
- **Mevcut Deger:** `'/index.php'`
- **Not:** Front controller pattern icin zorunlu.

### L-08: UserRepository DB_KEY Hardcoded
- **Dosya:** `auth.coremusic.net/include/Repository/UserRepository.php` Satir 19
- **Mevcut Deger:** `private const DB_KEY = 'auth'`
- **Onerilen .env:** `AUTH_DB_KEY=auth`

### L-09: UserRepository ACTIVE_USER_WHERE Hardcoded
- **Dosya:** `auth.coremusic.net/include/Repository/UserRepository.php` Satir 21
- **Mevcut Deger:** `'AND is_active = 1 AND is_deleted = 0'`
- **Not:** Soft delete kurali icin zorunlu hardcoded.

### L-10: Gender ALLOWED Values Hardcoded
- **Dosya:** `auth.coremusic.net/include/Domain/ValueObject/Gender.php` Satir 12
- **Mevcut Deger:** `['male', 'female', 'neutral']`
- **Onerilen .env:** `GENDER_VALUES=male,female,neutral`

### L-11: Error Reporting E_ALL Hardcoded
- **Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` Satir 12-16
- **Mevcut Deger:** `error_reporting(E_ALL)` / `error_reporting(E_ERROR | E_WARNING | E_PARSE)`
- **Not:** Debug mode'a bagimli, makul.

### L-12: CSP Inline Script Hardcoded Hash/Nonce Pattern
- **Dosya:** `shared/src/PageRouter/HtmlShellRenderer.php` Satir 114-157
- **Mevcut Deger:** Inline script ile window.CoreMusic tanimi
- **Not:** CSP nonce ile korunuyor, risk dusuk.

### L-13: Auth Page Lang Hardcoded 'tr'
- **Dosya:** `shared/src/PageRouter/HtmlShellRenderer.php` Satir 112
- **Mevcut Deger:** `<html lang="tr">`
- **Onerilen .env:** `APP_LANG=tr`

### L-14: CSRF Global Input Name Hardcoded
- **Dosya:** `shared/src/PageRouter/HtmlShellRenderer.php` Satir 129
- **Mevcut Deger:** `name="csrf_token" id="csrf-global"`
- **Not:** ADR-010 ile sabitlenmis, hardcoded olmali.

### L-15: Cache Key Prefix 'rl:' Hardcoded (2 Dosya)
- **Dosya 1:** `shared/src/Middleware/RateLimiterMiddleware.php` Satir 11
- **Dosya 2:** `shared/src/Security/CacheRateLimiter.php` Satir 10
- **Mevcut Deger:** `private const CACHE_KEY_PREFIX = 'rl:'`
- **Onerilen .env:** `RATE_LIMIT_KEY_PREFIX=rl:`

### L-16: UserRepository USER_COLUMNS Hardcoded
- **Dosya:** `auth.coremusic.net/include/Repository/UserRepository.php` Satir 20
- **Mevcut Deger:** Tum kolon listesi hardcoded
- **Not:** SELECT * yasagi icin zorunlu hardcoded.

### L-17: DeviceType Valid Values Hardcoded
- **Dosya:** `shared/src/PageRouter/HtmlShellRenderer.php` Satir 55
- **Mevcut Deger:** `['phone','tablet','embedded','laptop','desktop','4k-tv','4k-monitor']`
- **Onerilen .env:** `DEVICE_TYPES=phone,tablet,embedded,laptop,desktop,4k-tv,4k-monitor`

### L-18: Error Handler Language Hardcoded 'tr'
- **Dosya:** `shared/src/PageRouter/ErrorHandler.php` Satir 60
- **Mevcut Deger:** `<html lang="tr">`
- **Onerilen .env:** `APP_LANG=tr`

---

## 6. TEKRARLANAN DEGERLER (Dedup Analizi)

Ayni deger birden fazla dosyada hardcoded — tek kaynaga dusurulmeli.

| Deger | Tekrar Sayisi | Dosyalar |
|-------|---------------|----------|
| `'.coremusic.net'` (cookie domain) | 5+ | SessionConfig, SessionMiddleware, AuthController, HomeContainer, AuthContainer |
| `'COREMUSIC_SESS'` (session name) | 5 | constants.php x2, SessionConfig, SessionInitializer, SessionMiddleware |
| `'Europe/Istanbul'` (timezone) | 3 | constants.php x2, RuntimeBootstrap |
| `'2.3.0'` (app version) | 2 | constants.php x2 |
| `'127.0.0.1'` (localhost fallback) | 8+ | IAuthService, AuthService, AuthController, AutoRedirectHandler, RateLimiter, ApiRateLimit, UserRepository, RegisterRequest |
| `'utf8mb4'` (charset) | 3 | constants.php x2, DatabaseConfig |
| `3306` (DB port) | 3 | constants.php x2, DatabaseConfig |
| `'localhost'` (DB host) | 3 | constants.php x2, DatabaseConfig |
| `60` (rate limit max) | 3 | constants.php x2, ApiRateLimitMiddleware |
| `60` (rate limit window) | 3 | constants.php x2, ApiRateLimitMiddleware |
| `3600` (session/rate window) | 6+ | constants.php x2, SessionConfig, SessionMiddleware, AuthService x2 |
| `true` (httponly) | 4 | SessionConfig, SessionInitializer, SessionMiddleware, AuthController |
| `'Lax'` (samesite) | 4 | SessionConfig, SessionInitializer, SessionMiddleware, AuthController |
| `86400` (CORS max-age / cache TTL) | 3 | CorsMiddleware, MemorySystem, AuthService |
| `300` (auth key TTL) | 1 | AuthService |

---

## 7. ONERILER

### 7.1 Acil Aksiyonlar (1-2 Hafta)

1. **CORS/Origin whitelist'i .env'ye tasi** (C-01, C-02) — en yuksek oncelik
2. **Trusted Proxies'i .env'ye tasi** (C-03) — production icin zorunlu
3. **Cookie domain'i .env'ye tasi ve tek kaynak yap** (C-04) — 4 dosyadaki tekrari kaldir
4. **Session name'i .env'ye tasi ve tek kaynak yap** (C-05) — 5 dosyadaki tekrari kaldir
5. **SessionMiddleware'deki C:\temp ve 0777 iznini duzelt** (C-07) — acil guvenlik duzeltmesi
6. **DatabaseConfig'den 'root' default'ini kaldir** (C-08) — guvenlik acigi

### 7.2 Kisa Vadeli (2-4 Hafta)

7. **Session lifetime tutarsizligini coz** (H-03) — 7200 vs 3600
8. **AuthService rate limit sabitlerini .env'ye tasi** (H-06)
9. **ReturnUrlPolicy ALLOWED_HOSTS'i .env'ye tasi** (H-04)
10. **Domain port degerlerini .env'ye tasi** (H-12)
11. **HSTS/CORS Max-Age degerlerini .env'ye tasi** (H-08, H-09)

### 7.3 Orta Vadeli (1-2 Ay)

12. **SessionConfig sabitlerini .env'ye tasi** (M-07 — M-11)
13. **Cookie parametrelerini merkezlestir** (M-05, M-06)
14. **URL default'larini .env'ye tasi** (M-17 — M-19)
15. **MemorySystem TTL degerlerini .env'ye tasi** (M-22)
16. **CSP font source'larini .env'ye tasi** (M-21)

### 7.4 Mimari Oneri

Tek bir `shared/config/env-defaults.php` dosyasi olustur:
- Tum default degerler tek dosyada tanimli
- constants.php'ler bu dosyadan default'lari okur
- Yeni subdomain eklerken default'lar otomatik olarak paylasilir
- .env dosyalari sadece override degerleri icerir

---

## 8. DOSYA BAZLI OZET

### home.coremusic.net/config/

| Dosya | Bulgu Sayisi | CRITICAL | HIGH | MEDIUM | LOW |
|-------|-------------|----------|------|--------|-----|
| constants.php | 14 | 2 | 2 | 10 | 0 |
| app.php | 3 | 0 | 3 | 0 | 0 |
| bootstrap.php | 1 | 0 | 0 | 0 | 1 |
| config.php | 0 | 0 | 0 | 0 | 0 |

### auth.coremusic.net/config/

| Dosya | Bulgu Sayisi | CRITICAL | HIGH | MEDIUM | LOW |
|-------|-------------|----------|------|--------|-----|
| constants.php | 12 | 2 | 2 | 8 | 0 |
| app.php | 3 | 0 | 2 | 1 | 0 |
| cors.php | 1 | 1 | 0 | 0 | 0 |

### shared/src/

| Dosya/Dizin | Bulgu Sayisi | CRITICAL | HIGH | MEDIUM | LOW |
|-------------|-------------|----------|------|--------|-----|
| Session/SessionConfig.php | 6 | 0 | 0 | 6 | 0 |
| Middleware/RateLimiterMiddleware.php | 2 | 0 | 2 | 0 | 0 |
| Middleware/CorsMiddleware.php | 1 | 0 | 1 | 0 | 0 |
| Middleware/SecurityHeadersMiddleware.php | 2 | 0 | 0 | 2 | 0 |
| Database/Config/DatabaseConfig.php | 3 | 1 | 0 | 2 | 0 |
| Security/ReturnUrlPolicy.php | 1 | 0 | 1 | 0 | 0 |
| Bootstrap/RuntimeBootstrap.php | 1 | 0 | 1 | 0 | 0 |
| Interfaces/Auth/IAuthService.php | 1 | 0 | 1 | 0 | 0 |
| Api/Middleware/RateLimitMiddleware.php | 2 | 0 | 2 | 0 | 0 |
| AI/MemorySystem.php | 1 | 0 | 0 | 1 | 0 |
| OAuth/Provider/BaseOAuthProvider.php | 1 | 0 | 0 | 1 | 0 |
| PageRouter/RequestNormalizer.php | 1 | 0 | 0 | 0 | 1 |
| PageRouter/HtmlShellRenderer.php | 3 | 0 | 0 | 0 | 3 |

### auth.coremusic.net/include/

| Dosya | Bulgu Sayisi | CRITICAL | HIGH | MEDIUM | LOW |
|-------|-------------|----------|------|--------|-----|
| Middleware/OriginCheckMiddleware.php | 1 | 1 | 0 | 0 | 0 |
| Middleware/SessionMiddleware.php | 4 | 1 | 1 | 2 | 0 |
| Service/AuthService.php | 2 | 0 | 1 | 1 | 0 |
| Service/SessionManager.php | 1 | 0 | 0 | 1 | 0 |
| Controller/AuthController.php | 3 | 0 | 1 | 1 | 1 |
| Container/AuthContainer.php | 1 | 0 | 0 | 1 | 0 |
| Repository/UserRepository.php | 2 | 0 | 0 | 0 | 2 |

### home.coremusic.net/include/

| Dosya | Bulgu Sayisi | CRITICAL | HIGH | MEDIUM | LOW |
|-------|-------------|----------|------|--------|-----|
| Container/HomeContainer.php | 1 | 0 | 1 | 0 | 0 |
| Session/HomeSessionManager.php | 1 | 0 | 0 | 1 | 0 |
| Class/AbstractComponent.php | 1 | 0 | 0 | 1 | 0 |
| Component/HomeSongButton.php | 1 | 0 | 0 | 1 | 0 |

---

## 9. NOTLAR

1. **Default degerler .env'den okunuyor** — constants.php dosyalari zaten `$env('KEY', 'default')` pattern'i kullaniyor. Sorun, default degerlerin hardcoded olmasi ve bazi degerlerin hic .env'den okunmamasidir.
2. **Tekrarlanan degerler en buyuk risk** — Ayni deger 5+ yerde hardcoded oldugunda, bir yeri guncellemek digerlerini unutma riski tasir.
3. **SessionMiddleware'deki C:\temp** — Bu en kritik bulgudur. Production Linux ortaminda calismaz.
4. **auth.coremusic.net/config/cors.php** — Tamamen hardcoded, hicbir .env degiskeni kullanmiyor.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
