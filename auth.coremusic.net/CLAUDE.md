---
title: "CoreMusic — auth.coremusic.net Bağlam"
type: context
folder: "auth.coremusic.net"
category: domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
 authority: "auth.coremusic.net/CLAUDE.md"
 source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md · .ai/WORKFLOW.md"
---

# auth.coremusic.net — CLAUDE.md (Detaylı Versiyon)

**Zorunlu Bağlantılar:** · [[../.ai/architecture/k6-guvenlik]] · [[../.ai/architecture/k8-servis]] · [[../shared/CLAUDE.md]]

---

## 1. Bağlam & Amaç

**Tek giriş noktası**: tüm subdomainler kimlik doğrulamayı bu servise delege eder (ADR-043: Auth Subdomain Consolidation). `home.coremusic.net` `HomeAuthBridge` üzerinden bağlanır. Oturum durumu ADR-011'e göre taşınır.

```
Tüm Subdomainler → auth.coremusic.net (Merkezi Auth)
 ↓
 HomeAuthBridge → home.coremusic.net
 SessionBridge → diğer subdomainler
```

**Hexagonal/DDD Katmanlı Yapı:**
```
Controller → Handler → Service → Repository ↔ Domain
 ↓ ↓ ↓ ↓
HTTP girişi İş akışı İş mantığı Kalıcılık (PDO)
```

---

## 2. Mevcut Durum (Detaylı)

| Durum | Değer |
|-------|-------|
| Katman mimarisi | Hexagonal (Controller/Handler → Service → Repository ↔ Domain) |
| Middleware sayısı | 5 aktif + interface |
| Controller | AuthController.php (tek controller, tüm route'lar) |
| Handler | AuthKeyRedirect, AuthPost, AutoRedirect handler'ları |
| Service | AuthService (login, register, logout), SessionManager |
| Repository | UserRepository.php (PDO prepared statement) |
| Domain Entity | User.php (aggregate root) |
| Domain ValueObject | Email, Gender, Password, UserId |
| DTO | AuthResponse, LoginRequest, RegisterRequest |
| Sayfa sayısı | 7 (login, register, forgot/reset password, gender, logout) |
| Container | AuthContainer.php (DI) |
| Test kapsamı | ValueObject + DTO (Email, Gender, Password, User, LoginRequest) |
| Bilinen risk | Test kapsamı Service/Handler katmanını kapsamıyor |

---

## 3. Dosya Yapısı (Detaylı)

```
auth.coremusic.net/
├── index.php ← Front controller
├── autoload.php ← PSR-4 autoloading
├── composer.json / composer.lock ← Bağımlılıklar
├── phpunit.xml ← Test yapılandırması
├── .htaccess / web.config ← Apache/IIS rewrite
├── config/
│ ├── .env.example ← Ortam değişkeni şablonu
│ ├── app.php ← Uygulama ayarları
│ ├── constants.php ← Sabit tanımları
│ └── cors.php ← CORS yapılandırması
├── include/
│ ├── Container/
│ │ └── AuthContainer.php ← DI konteyneri
│ ├── Controller/
│ │ └── AuthController.php ← HTTP girişi (tüm route'lar)
│ ├── Domain/
│ │ ├── DTO/
│ │ │ ├── AuthResponse.php ← Auth yanıt DTO'su
│ │ │ ├── LoginRequest.php ← Login istek DTO'su
│ │ │ └── RegisterRequest.php ← Register istek DTO'su
│ │ ├── Entity/
│ │ │ └── User.php ← User aggregate root
│ │ └── ValueObject/
│ │ ├── Email.php ← Email value object
│ │ ├── Gender.php ← Gender value object
│ │ ├── Password.php ← Password value object (Argon2id)
│ │ └── UserId.php ← UserId value object
│ ├── Handler/
│ │ ├── AuthKeyRedirect.php ← Key-based redirect handler
│ │ ├── AuthPost.php ← POST auth handler
│ │ └── AutoRedirect.php ← Auto redirect handler
│ ├── Middleware/
│ │ ├── Pipeline.php ← Middleware pipeline
│ │ ├── OriginCheck.php ← CORS origin kontrolü
│ │ ├── RateLimit.php ← Rate limiting (ADR-013)
│ │ ├── SecurityHeaders.php ← CSP, HSTS header'ları
│ │ ├── Session.php ← Session yönetimi (ADR-011)
│ │ └── MiddlewareInterface.php ← Middleware interface
│ ├── Repository/
│ │ └── UserRepository.php ← PDO prepared statement
│ └── Service/
│ ├── AuthService.php ← Auth iş mantığı
│ └── SessionManager.php ← Session lifecycle
├── pages/
│ ├── login.php ← Login sayfası
│ ├── register.php ← Register sayfası
│ ├── forgot-password.php ← Şifre sıfırlama
│ ├── reset-password.php ← Şifre yenileme
│ ├── select-gender.php ← Cinsiyet seçimi
│ ├── set-gender.php ← Cinsiyet kaydetme
│ └── logout.php ← Çıkış
└── tests/
 ├── bootstrap.php ← Test bootstrap
 └── Unit/
 ├── Domain/
 │ ├── DTO/ ← DTO testleri
 │ ├── Entity/ ← Entity testleri
 │ └── ValueObject/ ← ValueObject testleri
 └── Service/ ← Service testleri (eksik)
```

---

## 4. Auth Akışı

### 4.1 Login Akışı

```
1. Kullanıcı → login.php (GET)
2. AuthController → AuthKeyRedirect handler
3. Form submit → AuthPost handler (POST)
4. AuthService::login() → UserRepository::findByEmail()
5. Password::verify() (Argon2id)
6. SessionManager::start() → Session oluştur
7. AuthResponse döndür → Redirect (home.coremusic.net)
```

### 4.2 Register Akışı

```
1. Kullanıcı → register.php (GET)
2. AuthController → AuthPost handler (POST)
3. AuthService::register() → UserRepository::create()
4. Password::hash() (Argon2id)
5. SessionManager::start() → Session oluştur
6. Select-gender sayfasına yönlendir
7. Gender seçimi → set-gender.php → User::setGender()
```

### 4.3 Session Taşıma (ADR-011)

```
auth.coremusic.net → Session Cookie → home.coremusic.net
 ↓
 HomeAuthBridge → Session doğrulama
 ↓
 Kullanıcı bilgileri inject
```

---

## 5. Güvenlik Kuralları

| Kural | Detay |
|-------|-------|
| CSRF | `csrf_token` zorunlu (ADR-010) |
| Session | HTTPOnly cookie, Secure flag (ADR-011) |
| Rate Limit | APCu tabanlı (ADR-013) |
| Password | Argon2id hash (ADR-022) |
| CORS | Whitelist-based (ADR-012) |
| CSP | nonce-based, strict-dynamic (ADR-012) |
| Secret | `.env` dosyasında, kodda/log'da yok |

---

## 6. Komşu İlişkiler (Detaylı)

| Yön | Hedef | İlişki | Etki |
|-----|-------|--------|------|
| Parent | [[../AGENTS.md]] | Kök registry | — |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | HomeAuthBridge + auth_callback.php | Yüksek (auth akışı) |
| Asset sağlanan | [[../assets.coremusic.net/CLAUDE.md]] | login/register CSS (d-auth-*) ve JS (js/auth/*) | Orta (görsel) |
| Paylaşılan altyapı | [[../shared/CLAUDE.md]] | Middleware, Session, Security bileşenleri | Yüksek (altyapı) |
| Vault referansı | [[../.ai/.subdomains/auth.coremusic.net/index.md]] | Subdomain vault kaydı | Düşük |
| DB | `coremusic_auth` | [[../.ai/.sql/mysql/coremusic_auth.sql]] | Yüksek (13 tablo) |

---

## 7. Değişiklik Protokolü (Detaylı)

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | Auth akışı değişikliği | Önce `auth-flow.md` okunur |
| 2 | Uyumsuzluk tespiti | DUR + ADR önerisi |
| 3 | Yeni endpoint | DTO + Service + Handler + test birlikte |
| 4 | Güvenlik etkisi | security-audit workflow tetiklenir |
| 5 | Audit | `log.md`'ye yazılır |

---

## 8. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | `.env` içeriğini log'a yazmak | Secret sızıntısı |
| 2 | Domain katmanından superglobal erişimi | Katman ihlali |
| 3 | Session/cookie değerlerini log'a yazmak | Gizlilik |
| 4 | `pages/*.php` içine iş mantığı | SRP ihlali |
| 5 | ORM kullanımı | ADR-002 yasağı |
| 6 | `var`, `eval` | Güvenlik |

---

## 9. İlgili Kaynaklar

| Kaynak | Yol | İçerik |
|--------|-----|--------|
| Auth mimarisi | `.ai/architecture/k6-guvenlik/` | Güvenlik katmanı |
| Auth akışı | `.ai/.subdomains/auth.coremusic.net/` | Subdomain vault |
| Ortak middleware | `shared/src/Middleware/` | 10+ middleware |
| PHP şablonu | `.ai/.templates/backend/php-template.md` | Yeni PHP dosyası |
| DB şema | `.ai/.sql/mysql/coremusic_auth.sql` | 13 tablo |
| ADR-043 | `.ai/decisions/accepted/ADR-043-auth-subdomain-consolidation.md` | Auth konsolidasyonu |
| ADR-011 | `.ai/decisions/accepted/ADR-011-session-management.md` | Session yönetimi |
| ADR-010 | `.ai/decisions/accepted/ADR-010-csrf-protection-strategy.md` | CSRF koruması |

---

## 10. Test Yapısı

| Test | Konum | Kapsam |
|------|-------|--------|
| EmailTest | `tests/Unit/Domain/ValueObject/` | Email validasyonu |
| GenderTest | `tests/Unit/Domain/ValueObject/` | Gender validasyonu |
| PasswordTest | `tests/Unit/Domain/ValueObject/` | Password hash/verify |
| UserIdTest | `tests/Unit/Domain/ValueObject/` | UserId üretimi |
| UserTest | `tests/Unit/Domain/Entity/` | User entity |
| LoginRequestTest | `tests/Unit/Domain/DTO/` | Login DTO |
| **Eksik** | `tests/Unit/Service/` | AuthService testleri |
| **Eksik** | `tests/Unit/Handler/` | Handler testleri |
| **Eksik** | `tests/Unit/Repository/` | Repository testleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
