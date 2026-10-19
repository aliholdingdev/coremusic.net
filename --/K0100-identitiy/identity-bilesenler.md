---
node_id: K0100-identitiy/identity-bilesenler
node_type: domain
status: verify-required
parent_id: K0100-identitiy
children: []
depends_on:
  - K009-security
  - K007-data
evidence:
  - "shared/src/Api/Registry/ServiceRegistry.php (122 satır) — 2026-10-12"
  - "shared/src/OAuth/Provider/BaseOAuthProvider.php (122 satır) · IOAuthProvider.php — 2026-10-12"
  - "shared/src/Api/Bff/ (5 BFF dosyası) — 2026-10-12"
validation: "identity-dogrulama.md §2 · identity-kod-ornekleri.md (bileşen alıntıları)"
group: mimari
open_questions:
  - "ServiceRegistry'de hangi identity servisleri kayıtlı? (122 satır okunmadı — kanıt paketi K24)"
  - "BaseOAuthProvider soyutlaması hangi 10 provider'ı somutlaştırıyor? (K20)"
---

# K0100-identitiy — bileşenler (components) kataloğu

## §1 Bileşen Haritası (katman → bileşen → dosya)

| Katman | Bileşen | Dosya | Durum |
|---|---|---|---|
| Presentation (HTTP) | `AuthMiddleware` | `shared\src\Middleware\AuthMiddleware.php` (28 satır) | IMPLEMENTED |
| Presentation (HTTP) | `SessionManagerMiddleware` | `shared\src\Middleware\SessionManagerMiddleware.php` (19 satır) | IMPLEMENTED |
| Presentation (HTTP) | `PermissionMiddleware` | `shared\src\Middleware\PermissionMiddleware.php` (64 satır) | IMPLEMENTED |
| Presentation (API) | `AuthenticationMiddleware` | `shared\src\Api\Middleware\AuthenticationMiddleware.php` (119 satır) | IMPLEMENTED |
| Presentation (API) | `AuthorizationMiddleware` | `shared\src\Api\Middleware\AuthorizationMiddleware.php` (97 satır) | IMPLEMENTED |
| Presentation (Router) | `AuthGuard` | `shared\src\PageRouter\AuthGuard.php` (80 satır) | IMPLEMENTED |
| Application | `IAuthService` sözleşmesi | `shared\src\Contracts\Auth\IAuthService.php` (13 satır) | IMPLEMENTED (sözleşme) |
| Application | `LoginRequest` · `RegisterRequest` DTO | `shared\src\Api\Dto\Request\` (64 · 84 satır) | IMPLEMENTED |
| Application | `ServiceRegistry` | `shared\src\Api\Registry\ServiceRegistry.php` (122 satır) | IMPLEMENTED (içerik okunmadı) |
| Domain | `ISessionManager` (21 metot) | `shared\src\Contracts\Auth\ISessionManager.php` (28 satır) | IMPLEMENTED |
| Domain | `IUserRepository` (16 metot) | `shared\src\Contracts\Auth\IUserRepository.php` (51 satır) | IMPLEMENTED |
| Domain | 5 domain event + 2 integration event | `shared\src\Events\Domain\` · `Events\Integration\` | IMPLEMENTED (dosyalar var) |
| Infrastructure | `JwtService` | `shared\src\Security\JwtService.php` (137 satır) | IMPLEMENTED |
| Infrastructure | `SessionLifecycle` · `SessionConfig` · `SessionBootstrapper` · `SessionInitializer` | `shared\src\Session\` (128 · 63 · 68 · 43 satır) | IMPLEMENTED |
| Infrastructure | `ApiSessionManager` | `shared\src\Api\Auth\ApiSessionManager.php` (216 satır) | IMPLEMENTED |
| Infrastructure | `OAuthManager` (262) · `OAuthRepository` (120) · `BaseOAuthProvider` (122) · `IOAuthProvider` | `shared\src\OAuth\` | IMPLEMENTED |
| Infrastructure | `SessionKeys` | `shared\src\Security\SessionKeys.php` (28 satır) | IMPLEMENTED |
| Infrastructure | `ReturnUrlPolicy` (160) · `SecurityHelper` (50) · `CacheRateLimiter` · `ApiKeyRepository` | `shared\src\Security\` (K009 ortak yüzey) | IMPLEMENTED |

## §2 Bileşen Sorumluluk Matrisi (RACI özeti)

| Bileşen | Kimlik kararı verir mi? | Session state tutar mı? | Token üretir mi? | Log yazar mı? |
|---|---|---|---|---|
| `AuthenticationMiddleware` | evet (401/fail-closed) | hayır (okur) | hayır | hayır (K009'a devreder) |
| `PermissionMiddleware` | evet (403 role/permission) | hayır | hayır | evet (`authz_denied`) |
| `AuthGuard` | evet (redirect/forbidden) | hayır | hayır | hayır |
| `SessionLifecycle` | hayır | evet (csrf/csp/rotation) | hayır | hayır |
| `JwtService` | hayır (validate yalnız) | hayır | evet (issue) | hayır |
| `ApiSessionManager` | evet (isAuthenticated) | hayır (stateless köprü) | hayır | hayır |
| `OAuthManager` | evet (state/ bağlantı) | evet (state → DB) | hayır (exchange provider) | hayır |

## §3 Bileşen Bağımlılık Grafiği

```text
HTTP Request
  → AuthMiddleware (28) → SessionManagerMiddleware (19) → PermissionMiddleware (64)
       │                        │                              │
       │                        ▼                              ▼
       │                 ISessionManager(28)            IUserRepository(51)
       │                        ▲                    findRolesForUser → RBAC
API Request
  → AuthenticationMiddleware (119) ──session dalı──→ ApiSessionManager (216)
       │                                   └──Bearer dalı──→ JwtService (137) → user_tokens (sha256(jti))
       ▼
  AuthorizationMiddleware (97)

Page Request
  → PageRouter → AuthGuard (80) → authHelper → ISessionManager

OAuth
  → OAuthManager (262) → BaseOAuthProvider(122)/IOAuthProvider → provider API
                       → OAuthRepository (120) → oauth_connections / oauth_states
```

## §4 Bileşen→Kademe-3 Planı

| Bileşen | Derinleştiği kademe-3 klasörü | Ne taşınır |
|---|---|---|
| `AuthRouteConfig` redirect (`response_type='session'`) | `oidc/oidc-arayuzler.md` · `oidc/oidc-akislari.md` | RP/OP rolü, redirect_uri, state, nonce |
| `OAuthManager` (state, provider, AES-256-GCM) | `oauth2/oauth2-anayasa.md` · `oauth2/oauth2-akislari.md` · `oauth2/oauth2-kod-ornekleri.md` | Authorization Code + PKCE, provider kataloğu |
| `JwtService` (RS256, jti) | `jwt/jwt-anayasa.md` · `jwt/jwt-token-modeli.md` · `jwt/jwt-kod-ornekleri.md` | JOSE yığını, claim modeli, revocation |
| `ApiSessionManager` (stateless köprü) | `oturum-yonetimi/` (kademe-2) | API oturum köprüsü sınırları |
| `PermissionMiddleware` (RBAC) | `rol-ve-yetki/` (kademe-2) | rol/permission kararı |

## §5 Yeni Bileşen İhtiyaçları (PLANNED — implementasyon yok)

| # | Bileşen | Gerekçe | Bağımlılık | Status |
|---|---|---|---|---|
| 1 | `MfaService` (TOTP/WebAuthn) | repo'da MFA kodu yok | `mfa-ve-sertifika/` | PLANNED |
| 2 | `DeviceCertificateStore` | cihaz sertifikası yok | `mfa-ve-sertifika/` | PLANNED |
| 3 | `RefreshTokenService` | `JwtService` refresh döngüsü içermiyor | `jwt/jwt-token-modeli.md` | PLANNED (`⚠️ VERIFICATION REQUIRED`) |
| 4 | `SessionParadoxResolver` | `MAX_LIFETIME` < `IDLE_TIMEOUT` kararı | ADR-097 | PLANNED (karar bekleniyor) |
| 5 | OIDC discovery metadata (`.well-known`) | OP metadata'sı yok | `oidc/oidc-anayasa.md` | PLANNED |

## §6 — PSR-15 (HTTP Message Handlers & Middleware) ile bileşen sözleşmesi eşlemesi

> **Kaynak:** https://www.php-fig.org/psr/psr-15/ · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PSR-15, PHP ekosisteminde middleware ve handler'ların imzasını standartlaştırır.
K010 bileşen haritası (§1) altı HTTP katmanlı bileşen sayar; bu bölüm her birinin
PSR-15 sözleşmesiyle uyumunu **iddia** düzeyinde eşler (interface kanıtı bu
revizyonla okunmadı → ⚠️).

### §6.1 — PSR-15 arayüzleri ↔ K010 middleware bileşenleri

| # | PSR-15 arayüzü | İmza | K010 bileşeni | Kanıt | Durum |
|---|---|---|---|---|---|
| 1 | `MiddlewareInterface` | `process(ServerRequestInterface, RequestHandlerInterface): ServerResponseInterface` | 11 middleware (K009 yüzeyi) | `shared\src\Middleware\` (11 dosya) | ⚠️ VR (interface imzası okunmadı) |
| 2 | `RequestHandlerInterface` | `handle(ServerRequestInterface): ServerResponseInterface` | pipeline devam zinciri | — | ⚠️ VR |
| 3 | HTTP katman middleware'i | — | `AuthMiddleware` (28 satır) | §1 | ⚠️ VR (PSR uyumu) |
| 4 | HTTP katman middleware'i | — | `SessionManagerMiddleware` (19 satır) | §1 | ⚠️ VR (PSR uyumu) |
| 5 | HTTP katman middleware'i | — | `PermissionMiddleware` (64 satır) | §1 | ⚠️ VR (PSR uyumu) |
| 6 | API katman middleware'i | — | `AuthenticationMiddleware` (119 satır) | §1 | ⚠️ VR (PSR uyumu) |
| 7 | API katman middleware'i | — | `AuthorizationMiddleware` (97 satır) | §1 | ⚠️ VR (PSR uyumu) |
| 8 | Handler (router sonu) | — | `AuthGuard` (80 satır) | §1 | ⚠️ VR (PSR uyumu) |

### §6.2 — Sıra ve kısa devre davranışı ↔ pipeline kanıtı

| # | PSR-15 beklentisi | CoreMusic pipeline'ı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Middleware sırası kaydedilmelidir | `Auth → SessionManager → Permission` | `identity-bilesenler.md` §3 grafiği | ✅ (bu vault) |
| 2 | Kısa devre: erken `return` | 401 fail-closed | `AuthenticationMiddleware.php` L103-107 | ✅ kanıtlı |
| 3 | 403 kısa devre | role/permission reddi | `PermissionMiddleware.php` L30-42 | ✅ kanıtlı |
| 4 | İstek değiştirme (attribute ekleme) | session/claim taşıma | `AuthenticationMiddleware.php` L83-100 | ⚠️ VR (attribute kanıtlanmadı) |
| 5 | Yanıt değiştirme (header) | K009 politikasına devir | §2 #1 (log) | ⚠️ VR |

### §6.3 — Bağımlılık etkisi

| # | Uyum kanıtı eksikse | Etki | Öneri |
|---|---|---|---|
| 1 | Pipeline değişimi PSR'ye aykırı olabilir | taşınabilirlik kaybı | middleware imzaları A4 incelemesinde okunmalı |
| 2 | Test double yazımı zorlaşır | QA etkisi | PSR-15 mock'ları ile test |
| 3 | PSR-11 (container) bağımlılığı | kompozisyon | ⚠️ VR (container kanıtı yok) |

**Eşleme sonucu:** PSR-15, K010 middleware bileşenlerinin **sözleşme dili**dir;
bu vault'ta bileşen dosyaları ve davranış kanıtları mevcuttur ancak interface
imzaları okunmadığı için PSR-15 uyumu `⚠️ VERIFICATION REQUIRED` olarak işaretli
kalır (A4 incelemesine taşınır).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — PSR-14 (Event Dispatching) ile domain event bileşen eşlemesi

> **Kaynak:** https://www.php-fig.org/psr/psr-14/ · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PSR-14, olay yayılımının imzasını standartlaştırır: `dispatch(object $event)`,
`getListenersForEvent(object): iterable`. K010'da 5 domain event + 2 integration
event (§1) bulunur; yayılım zinciri **okunmadı** (§5/§3 #4 aynı boşluğu taşır).

### §7.1 — PSR-14 arayüzleri ↔ CoreMusic event bileşenleri

| # | PSR-14 öğesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `EventDispatcherInterface::dispatch(object)` | 5 domain event yayılımı | `shared\src\Events\Domain\` (5 dosya) | ✅ dosya var · ⚠️ yayıcı okunmadı |
| 2 | `ListenerProviderInterface` | dinleyici kaydı | — | ⚠️ VR |
| 3 | Event bir `object`'tir (sınırlama yok) | `final class … implements DomainEventInterface` | account-kapsam §4 #8 | ✅ kanıtlı (arayüz) |
| 4 | Yayılım sırası dinleyici kaydına bağlı | — | — | ⚠️ VR |
| 5 | Integration event ayrımı | `Events\Integration\` (2 dosya) | §1 | ✅ dosya var |

### §7.2 — Event bileşen kataloğu ↔ K010 sahipliği

| # | Event | Kaynak (bu vault) | K010 sahipliği |
|---|---|---|---|
| 1 | `UserRegisteredEvent` | `Events\Domain\` (K22) | account (`account-kapsam.md` §4 #8) |
| 2 | `UserLoggedInEvent` | `Events\Domain\` (K22) | account / credentials |
| 3 | `UserLoggedOutEvent` | `Events\Domain\` (K22) | session (kademe-2) |
| 4 | `PasswordResetRequestedEvent` | `Events\Domain\` (K22) | credentials |
| 5 | `GenderSetEvent` | `Events\Domain\` (K22) | user-identity |
| 6 | `SessionCreatedEvent` | `Events\Integration\` (K22) | session |
| 7 | `AuthValidatedEvent` | `Events\Integration\` (K22) | credentials |

### §7.3 — Yayılım zinciri boşluğu (K008 bağımlılığı ile kesişim)

| # | Boşluk | İlişkili bağımlılık | Status |
|---|---|---|---|
| 1 | Event'i kim ne zaman yayıyor? (yayıncı kodu) | §1 #4 | ⚠️ VR |
| 2 | Dinleyici kaydı hangi katmanda? | §1 #5 | ⚠️ VR |
| 3 | Event adlarının K008 ile eşleşmesi | K008-domain | ✅ dosya var |
| 4 | Transaction boundary (yayılım commit sonrası mı?) | K008 | ⚠️ VR |

**Eşleme sonucu:** PSR-14, CoreMusic event bileşenlerinin **sözleşme dilidir**;
dosyalar var ve arayüz tabanlı oldukları kanıtlanmıştır (`DomainEventInterface`),
ancak yayıncı/dinleyici zinciri okunmadığı için yayılım davranışı `⚠️ VR`
kalır — K008 bağımlılık defteriyle (§1 #4-#5) aynı boşluktur, yeni ok eklenmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — PHP `random_bytes` resmi kılavuzu ile rastgelelik bileşeni eşlemesi

> **Kaynak:** https://www.php.net/manual/en/function.random-bytes.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`random_bytes()`, kriptografik olarak güvenli rastgele bayt üretir. K010'da üç
rastgelelik kaynağı kanıtlanmıştır (csrf token, OAuth state, JWT jti); bu
bölüm her birini kılavuz maddeleriyle eşler.

### §8.1 — Kılavuz maddeleri ↔ CoreMusic rastgelelik bileşenleri

| # | Kılavuz maddesi | CoreMusic kullanımı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Kriptografik olarak güvenli bayt üretir | `csrf_token = bin2hex(random_bytes(32))` | `SessionLifecycle.php` L47-49 | ✅ kanıtlı |
| 2 | Uzunluk bayt cinsinden belirlenir | 32 bayt (256 bit entropi) | aynı satır | ✅ kanıtlı |
| 3 | Hata durumunda `Exception` fırlatır | — | — | ⚠️ VR (hata yakalama kanıtlanmadı) |
| 4 | `random_int`'in bayt eşdeğeri | — | — | bilgi |
| 5 | Eski `openssl_random_pseudo_bytes` yerine önerilir | — | — | bilgi |
| 6 | OAuth state üretimi | provider state kaynağı | `OAuthManager.php` L160 (hash) | ⚠️ VR (üretim satırı okunmadı — §5 karşılaştırmada aynı) |
| 7 | JWT `jti` üretimi | token tekili | `JwtService.php` L51 | ⚠️ VR (üretim satırı okunmadı) |
| 8 | Token hash (`sha256(jti)`) | `user_tokens.token_hash` | `IUserRepository.php` L40/45/50 | ✅ kanıtlı (dönüştürme) |

### §8.2 — Entropi bütçesi matrisi

| # | Kaynak | Entropi (kanıt) | Kullanım | Durum |
|---|---|---|---|---|
| 1 | `random_bytes(32)` | 256 bit | csrf_token | ✅ kanıtlı |
| 2 | state üretici | bilinmiyor | OAuth state | ⚠️ VR |
| 3 | `jti` üretici | bilinmiyor | JWT tekil | ⚠️ VR |
| 4 | `session_id()` (PHP çekirdeği) | platform | oturum kimliği | ✅ platform |
| 5 | `bin2hex` dönüşümü | 1:1 (hex) | token taşınma biçimi | ✅ kanıtlı (L47-49) |

### §8.3 — Bağımlılık defterine etkisi

| # | Bağımlılık | Yön | Status |
|---|---|---|---|
| 1 | `random_bytes` (PHP çekirdeği) | K010 → platform | ✅ kanıtlı |
| 2 | `Random\Randomizer` (PHP 8.2+, kütüphane) | K010 → platform | ⚠️ VR (kullanım yok) |
| 3 | Donanım RNG başarısızlığı | — | ⚠️ VR (davranış kanıtlanmadı) |

**Eşleme sonucu:** `csrf_token` üretimi `random_bytes(32)` ile kriptografik
rastgeleliğe bağlanmıştır (tek satır-içi kanıt); OAuth state ve jti üreticileri
bu revizyonla okunmadı → rastgelelik bileşeni kataloğunda iki satır `⚠️ VR`
kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — RFC 7517 (JWK) ile anahtar bileşeni eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7517 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7517, JSON Web Key formatını tanımlar (`kty` · `use` · `kid` · algoritma
parametreleri). K010'da `JwtService` RS256 için **dosyadan okunan private key**
kullanır (§1 Infrastructure satırı) — bu bölüm anahtar bileşenini RFC 7517
diline bağlar.

### §9.1 — RFC 7517 anahtar parametreleri ↔ JwtService anahtar modeli

| # | RFC 7517 parametresi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `kty: RSA` | RS256 → RSA anahtarı | `JwtService.php` L24 | ✅ kanıtlı (sonuç) |
| 2 | `use: sig` | imzalama anahtarı | — | ⚠️ VR (alan yok) |
| 3 | `kid` (anahtar kimliği) | key rotasyonu kimliği | — | ⚠️ VR |
| 4 | `n` · `e` (public) | public key dağıtımı (JWKS) | — | ❌ (jwks_uri yok — `identity-kapsam.md` §8) |
| 5 | `d` · `p` · `q` (private) | private key dosyadan okunur | `JwtService.php` L20 · L111-124 | ✅ kanıtlı (dosya yöntemi) |
| 6 | Algoritma kısıtı (`alg`) | RS256 sabit | `JwtService.php` L24 | ✅ kanıtlı |
| 7 | Anahtar rotasyonu (kılavuz) | — | — | ⚠️ VR |

### §9.2 — Anahtar saklama modeli karşılaştırması

| # | Model | CoreMusic | Güvenlik notu | Durum |
|---|---|---|---|---|
| 1 | JWK (JSON) | kullanılmıyor | — | bilgi |
| 2 | JWK Set (JWKS) | kullanılmıyor | keşif endpoint'i yok | ❌ (§8) |
| 3 | PEM dosyası | **kullanılan model** | dosya sistemi erişimi yeterli | ✅ kanıtlı (L111-124) |
| 4 | HSM/KMS | kullanılmıyor | — | ⚠️ VR (ops kanıtı yok) |
| 5 | Vault (credential_keys) | key registry hash-only | parola anahtarı yüzeyi | ✅ kanıtlı (credentials §4 #7) |

### §9.3 — JWKS eksikliğinin bağımlılık etkisi

| # | Eksiklik | Etkilenen bağımlılık | Status |
|---|---|---|---|
| 1 | `jwks_uri` yok → doğrulayıcı public key dağıtılamaz | dış API istemcileri | ⚠️ VR |
| 2 | `kid` yok → çok-anahtar dönemde belirsizlik | key rotasyonu | ⚠️ VR |
| 3 | Key rotation süreci kanıtlanmadı | K009 (key yönetimi) | ⚠️ VR |
| 4 | OAuth provider JWKS doğrulaması | 10 provider | ⚠️ VR |

**Eşleme sonucu:** RFC 7517, `JwtService` anahtar bileşeninin standart dilidir;
CoreMusic PEM dosyası modeliyle kanıtlanmıştır, JWK/JWKS yüzeyi **repo'da
kanıtlanmamıştır**. Anahtar rotasyonu ve `kid` yönetimi ADR-097 kapsamında
değerlendirilecek açık maddelerdir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
