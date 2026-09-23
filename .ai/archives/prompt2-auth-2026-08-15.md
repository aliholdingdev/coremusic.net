---
title: "CoreMusic — Prompt 2: Authentication Sistemi"
type: prompt
category: security
date: 2026-08-15
---
> **⚠️ ARŞİV — Bu dosya tarihsel bir belgedir.** Middleware pipeline ve nonce akışı hakkında güncel bilgi için [[.ai/CLAUDE.md]] §6 ve [[.ai/brain.md]] §6 okunur.
updated: 2026-08-15
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
token_limit: 5000
layers:
  primary: L1-Security
  secondary: L0-Infrastructure
reference:
  authority: ".ai/CLAUDE.md"
  shared_base: ".ai/archives/prompt-shared-base.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/brain.md"
  architecture:
    - ".ai/architecture/l1-security/"
    - ".ai/architecture/l0-infrastructure/"
    - ".ai/architecture/08-auth/"
  adr:
    - ".ai/decisions/accepted/ADR-008-bypass-auth-middleware.md"
    - ".ai/decisions/accepted/ADR-010-csrf-protection-strategy.md"
    - ".ai/decisions/accepted/ADR-011-session-management.md"
    - ".ai/decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md"
    - ".ai/decisions/accepted/ADR-022-database-hardened-security.md"
    - ".ai/decisions/accepted/ADR-034-credential-vault-normalization.md"
    - ".ai/decisions/accepted/ADR-043-auth-subdomain-consolidation.md"
    - ".ai/decisions/accepted/ADR-047-login-redirect-session-bridge.md"
    - ".ai/decisions/accepted/ADR-056-auth-module-implementation.md"
  prompts:
    - ".ai/archives/prompt-shared-base.md"
changelog:
  - version: 2.0.0
    date: 2026-08-15
    changes:
      - Tamamen yeniden yazım — SOLID, Clean Code, L0-L6 uyumlu
      - L1 Security katmanı odaklı tasarım
      - Vault cross-reference eklendi
---

# CoreMusic — Prompt 2: Authentication Sistemi

**Ortak Temel:** [[prompt-shared-base]] (ROLE, sistem tanımı, L0-L6, SOLID, Clean Code — bu dosyada tekrar edilmez)

**Zorunlu Bağlantılar:** [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../brain.md]] · [[../../architecture/l1-security/index]] · [[../../architecture/08-auth/index]]

**Kullanım Anı:** Auth middleware, session, JWT, CORS, RBAC geliştirme görevleri
**Sorumlu Agent'lar:** Security Engineer (L1), Backend Architect (L2)

---

## 1. Temel Referans

Bu prompt, CoreMusic Authentication sistemini **L1 Security** katmanı düzeyinde tanımlar. Merkezi otorite (auth.coremusic.net), hybrid JWT+session, RBAC ve middleware pipeline detaylarını kapsar.

---

## 2. Auth Vizyonu

*Detaylı metadata: [[../../architecture/l1-security/auth]]*

### 2.1 Merkezi Otorite İlkesi

auth.coremusic.net merkezi kimlik servisidir. Diğer bütün subdomainler kendi içinde kullanıcı doğrulama sistemi taşımaz.

```
Browser
  → auth.coremusic.net (merkezi otorite)
    → Session Cookie (HTTPOnly, Secure, SameSite)
      → Protected Services (home, studio, pro, car, admin, api)
```

### 2.2 L1 Security Katmanı

Auth sistemi L1 Security katmanında yer alır:

- **Görev:** Kimlik doğrulama, yetkilendirme, session yönetimi
- **Bağımlılık:** L0 Infrastructure (PDO, Redis, credential vault)
- **Kullanıcı:** L2 Routing (middleware pipeline) tarafından kullanılır
- **Kurallar:** L2/L3'e bağımlı olamaz, frontend'den bağımsız

### 2.3 CoreMusic AUTH Nedir?

CoreMusic AUTH, sıradan bir giriş ekranı veya basit bir üyelik tablosu değil; tüm ekosistemin güvenliğini, kimlik yönetimini ve yetkilendirme süreçlerini tek noktadan kontrol eden, kurumsal (enterprise) standartlarda kurgulanmış merkezi bir **Security Gateway** ve **Identity Provider** platformudur.

---

## 3. Merkezi Auth

*Detaylı metadata: [[ADR-043]], [[ADR-056]]*

### 3.1 Subdomain Auth Yapısı

| Subdomain | Auth Durumu | Açıklama |
|-----------|-------------|----------|
| auth.coremusic.net | **Merkezi Otorite** | Tüm auth işlemleri burada |
| home.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| studio.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| pro.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| car.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| admin.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| api.coremusic.net | Auth'a bağımlı | JWT + API Key |
| music.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| download.coremusic.net | Auth'a bağımlı | Cookie ile auth check |
| media.coremusic.net | Auth'a bağımlı | Cookie ile auth check |

### 3.2 Auth Akışı

```
Kullanıcı → Login formu → POST /auth/login
  → Password hash kontrolü (Argon2id)
    → Başarılı → Session oluştur
      → HTTPOnly Cookie → Redirect
        → Tüm subdomainler bu cookie'yi kullanır
```

### 3.3 SSO (Single Sign-On)

Kullanıcı bir kez giriş yaptığında:
1. auth.coremusic.net'de session oluşturulur
2. Güvenli cookie tüm subdomainlerle paylaşılır
3. Kullanıcı diğer subdomainlere geçtiğinde tekrar giriş yapmaz
4. Session süresi dolduğunda auth'a redirect edilir

---

## 4. Hybrid JWT+Session

*Detaylı metadata: [[ADR-011]], [[ADR-010]]*

### 4.1 Hybrid Mimari

CoreMusic, hem JWT hem session kullanır:

| Mekanizma | Kullanım Alanı | Ömür |
|-----------|---------------|------|
| **Session Cookie** | Browser-based SPA | 3600s idle timeout |
| **Access JWT** | API istekleri | 15 dakika |
| **Refresh JWT** | Token yenileme | Long-lived |

### 4.2 Session Cookie Özellikleri

| Özellik | Değer | ADR |
|---------|-------|-----|
| Name | `COREMUSIC_SESS` | ADR-011 |
| HttpOnly | `true` | ADR-011 |
| Secure | `true` (HTTPS zorunlu) | ADR-011 |
| SameSite | `Lax` | ADR-010 |
| Path | `/` | ADR-011 |
| Max Age | 3600s (idle timeout) | ADR-011 |

### 4.3 JWT Özellikleri

| Özellik | Değer | ADR |
|---------|-------|-----|
| Algorithm | RS256 | ADR-059 |
| Access Token | 15 dakika | ADR-011 |
| Refresh Token | Long-lived | ADR-011 |
| Issuer | auth.coremusic.net | ADR-043 |
| Audience | *.coremusic.net | ADR-043 |

### 4.4 Frontend Güvenlik Kuralı

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| localStorage'da token | Session cookie |
| sessionStorage'da token | Session cookie |
| JavaScript'de JWT saklama | HTTPOnly cookie |
| `document.cookie` okuma | Sunucu tarafı session |

---

## 5. RBAC Sistemi

*Detaylı metadata: [[ADR-056]], [[../../architecture/08-auth/]]*

### 5.1 Rol Hiyerarşisi

| Rol | Yetki Seviyesi | Erişim |
|-----|----------------|--------|
| **admin** | 1000-1999 | Tam sistem yönetimi |
| **system** | 1900-1999 | Sistem servisleri |
| **studio** | 800-899 | Stüdyo modu, 8.1 surround |
| **premium** | 700-799 | Yüksek kalite, offline |
| **car** | 500-599 | Araç içi mod, touch-optimized |
| **regular** | 100-199 | Temel erişim |
| **guest** | 0 | Sadece genel |

### 5.2 Permission Matrisi

| İşlem | guest | regular | premium | car | studio | admin |
|-------|-------|---------|---------|-----|--------|-------|
| Müzik dinle | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Albüm oluştur | ❌ | ✅ | ✅ | ❌ | ✅ | ✅ |
| İndirme yap | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ |
| Stüdyo modu | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Admin panel | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| API erişimi | ❌ | ❌ | ✅ | ❌ | ✅ | ✅ |
| Medya yönetimi | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

### 5.3 Role Guardian

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

final readonly class PermissionMiddleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $user = $request->getAttribute('user');
        $requiredRole = $request->getAttribute('required_role');
        
        if ($user === null || $user->getLevel() < $requiredRole->getLevel()) {
            return new Response(403, [], 'Forbidden');
        }
        
        return $handler->handle($request);
    }
}
```

---

## 6. Middleware Pipeline (L1 Detayı)

*Detaylı metadata: [[../../architecture/l1-security/middleware]]*

### 6.1 Pipeline Sırası (Frozen — 10 Katman)

```
1. OriginCheckMiddleware()      — Köken doğrulama (whitelist CORS)
2. CorsMiddleware()             — CORS header yönetimi
3. RateLimiterMiddleware()      — APCu: 60 req/60s
4. SecurityHeadersMiddleware()  — CSP strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware()   — Session başlat, CSP nonce üret
6. CsrfMiddleware()             — csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware()       — Test bypass (?_bypass=1)
8. AuthMiddleware()             — Auth bilgisi inject (JWT + Session)
9. PermissionMiddleware()       — RBAC yetki kontrolü
10. ValidationMiddleware()       — Request/DTO validasyonu
→ Controller
```

### 6.2 Her Middleware'in Sorumluluğu

| # | Middleware | Sorumluluk | ADR |
|---|-----------|------------|-----|
| 1 | OriginCheck | Whitelist CORS kontrolü | ADR-010 |
| 2 | Cors | CORS header yönetimi | ADR-010 |
| 3 | RateLimiter | APCu rate limit (60/60s) | ADR-013 |
| 4 | SecurityHeaders | CSP, HSTS, X-Frame | ADR-012 |
| 5 | SessionManager | Session başlat, nonce üret | ADR-011 |
| 6 | Csrf | csrf_token doğrulama | ADR-010 |
| 7 | BypassAuth | Test ortamı bypass | ADR-008 |
| 8 | Auth | Auth bilgisi inject | ADR-011 |
| 9 | Permission | RBAC yetki kontrolü | ADR-056 |
| 10 | Validation | Request validasyonu | — |

### 6.3 CSP Nonce Üretimi

**Kritik:** CSP nonce üretimi SessionManager içindedir. Sıra değiştirilirse CSP bozulur.

```php
<?php

declare(strict_types=1);

final readonly class SessionManagerMiddleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $nonce = base64_encode(random_bytes(32));
        
        $request = $request
            ->withAttribute('csp_nonce', $nonce)
            ->withAttribute('session', $this->session->start());
        
        $response = $handler->handle($request);
        
        return $response
            ->withHeader('Content-Security-Policy', 
                "script-src 'nonce-{$nonce}' 'strict-dynamic'");
    }
}
```

---

## 7. Session Yönetimi

*Detaylı metadata: [[ADR-011]], [[../../architecture/l1-security/session]]*

### 7.1 Session Lifecycle

```
Login → Session oluştur → Cookie set et → 3600s idle timeout
  → Aktiflik → Timeout sıfırlanır
    → Idle → 3600s sonra session sonlanır
      → Logout → Session silinir → Cookie silinir
```

### 7.2 Session Storage

| Depolama | Kullanım | ADR |
|----------|----------|-----|
| File-based (başlangıç) | Development | ADR-011 |
| MySQL (geçiş planı) | Production | ADR-011 |
| Redis (gelecek) | High-traffic | ADR-011 |

### 7.3 Session Güvenliği

| Özellik | Değer |
|---------|-------|
| Idle Timeout | 3600s |
| Absolute Timeout | 86400s (24 saat) |
| Rotation | Login'de yeniden üretim |
| Regeneration | Privilege escalation koruması |
| Destruction | Logout'da tam silme |

---

## 8. CSRF Koruması

*Detaylı metadata: [[ADR-010]], [[../../architecture/l1-security/csrf]]*

### 8.1 CSRF Token Kuralları

| Kural | Değer | ADR |
|-------|-------|-----|
| Token Key | `csrf_token` | ADR-010 |
| Yasaklı Key | `_csrf_token` | ADR-010 |
| Doğrulama | `hash_equals()` (timing-safe) | ADR-010 |
| Üretim | `random_bytes(32)` | ADR-010 |
| Saklama | Session-bound | ADR-010 |
| Kullanım Alanı | POST, PUT, DELETE | ADR-010 |

### 8.2 CSRF Form Entegrasyonu

```php
<?php

declare(strict_types=1);

// Form içinde
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';

// Controller'da doğrulama
if (!hash_equals($sessionCsrfToken, $requestCsrfToken)) {
    throw new CsrfTokenMismatchException();
}
```

---

## 9. CSP (Content Security Policy)

*Detaylı metadata: [[ADR-012]], [[../../architecture/l1-security/csp]]*

### 9.1 CSP Directives

| Directive | Değer | ADR |
|-----------|-------|-----|
| `default-src` | `'self'` | ADR-012 |
| `script-src` | `'nonce-{random}' 'strict-dynamic'` | ADR-012 |
| `style-src` | `'self' 'unsafe-inline'` | ADR-012 |
| `img-src` | `'self' data: https:` | ADR-012 |
| `font-src` | `'self'` | ADR-012 |
| `connect-src` | `'self'` | ADR-012 |
| `frame-ancestors` | `'none'` | ADR-012 |

### 9.2 CSP Nonce Kullanımı

```html
<!-- nonce-based script yükleme -->
<script nonce="<?php echo $cspNonce; ?>" src="/js/app.js"></script>

<!-- inline script için nonce zorunlu -->
<script nonce="<?php echo $cspNonce; ?>">
    // Uygulama kodu
</script>
```

---

## 10. SOLID Uygulaması (Auth İçin)

### 10.1 Single Responsibility (SRP)

| Sınıf | Sorumluluk |
|-------|------------|
| `LoginUseCase` | Sadece login mantığı |
| `LogoutUseCase` | Sadece logout mantığı |
| `RegisterUseCase` | Sadece kayıt mantığı |
| `SessionManager` | Sadece session yönetimi |
| `TokenManager` | Sadece JWT yönetimi |
| `PasswordHasher` | Sadece şifre hashleme |

### 10.2 Open/Closed (OCP)

Yeni auth metodu eklemek mevcut kodu değiştirmez:

```php
// Yeni auth metodu: Biyometrik
final class BiometricAuthUseCase implements AuthUseCaseInterface
{
    public function authenticate(ServerRequestInterface $request): AuthResult
    {
        // Biyometrik doğrulama mantığı
    }
}
```

### 10.3 Liskov Substitution (LSP)

Tüm auth use case'leri `AuthUseCaseInterface`'i uygular:

```php
interface AuthUseCaseInterface
{
    public function authenticate(ServerRequestInterface $request): AuthResult;
}
```

### 10.4 Interface Segregation (ISP)

İnce arayüzler:

```php
interface PasswordHasherInterface
{
    public function hash(string $password): string;
    public function verify(string $password, string $hash): bool;
}

interface TokenGeneratorInterface
{
    public function generate(User $user): TokenPair;
    public function validate(string $token): ?User;
}
```

### 10.5 Dependency Inversion (DIP)

Auth use case'leri interface'lere bağımlıdır:

```php
final class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly SessionManagerInterface $sessionManager,
        private readonly TokenGeneratorInterface $tokenGenerator,
    ) {}
}
```

---

## 11. Clean Code (PHP 8.4)

*Detaylı metadata: [[../../brain.md]] §18*

### 11.1 PHP Auth Standartları

| Kural | Açıklama |
|-------|----------|
| `declare(strict_types=1)` | Her dosyada zorunlu |
| PSR-12 | Kod stili |
| Constructor injection | Bağımlılıklar constructor'dan |
| Final classes | Mümkün olduğunca final |
| Named arguments | 3+ parametreli call'larda |
| snake_case | Variable/function isimleri |
| PascalCase | Class isimleri |
| Explicit column list | SELECT * yasak |
| Prepared statement | PDO prepared |

### 11.2 Auth Entity Örneği

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Entity;

final readonly class User
{
    private function __construct(
        public readonly UserId $id,
        public readonly Email $email,
        public readonly UserRole $role,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(Email $email, UserRole $role): self
    {
        return new self(
            UserId::generate(),
            $email,
            $role,
            new \DateTimeImmutable('now')
        );
    }

    public function getLevel(): int
    {
        return $this->role->getLevel();
    }
}
```

---

## 12. Domain Entity

*Detaylı metadata: [[ADR-056]]*

### 12.1 Auth Entity Haritası

```
Auth Domain/
├── Entity/
│   ├── User.php                 ← Kullanıcı entity
│   ├── Role.php                 ← Rol entity
│   ├── Session.php              ← Session entity
│   └── Token.php                ← Token entity
├── ValueObject/
│   ├── Email.php                ← Email value object
│   ├── Password.php             ← Password value object
│   ├── UserId.php               ← UserId value object
│   └── UserRole.php             ← UserRole value object
├── Repository/
│   ├── UserRepositoryInterface.php
│   ├── SessionRepositoryInterface.php
│   └── TokenRepositoryInterface.php
└── Event/
    ├── UserRegisteredEvent.php
    ├── UserLoggedInEvent.php
    └── UserLoggedOutEvent.php
```

### 12.2 Value Object Örneği

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObject;

final readonly class Email
{
    public function __construct(
        private readonly string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($value);
        }
    }

    public function toString(): string
    {
        return $this->value;
    }
}
```

---

## 13. Kod Örnekleri

### 13.1 Login Use Case

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application;

final class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly SessionManagerInterface $sessionManager,
    ) {}

    public function execute(LoginRequest $request): LoginResponse
    {
        $user = $this->userRepository->findByEmail($request->email);
        
        if ($user === null || !$this->passwordHasher->verify(
            $request->password, 
            $user->getPasswordHash()
        )) {
            throw new AuthenticationFailedException();
        }

        $session = $this->sessionManager->create($user);
        
        return new LoginResponse(
            sessionId: $session->getId(),
            user: $user,
        );
    }
}
```

### 13.2 Password Hasher

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

final class Argon2idPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,  // 64MB
            'time_cost' => 4,
            'threads' => 2,
        ]);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
```

---

## 14. Yasak Örüntüler (Auth)

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `_csrf_token` | `csrf_token` | ADR-010 |
| localStorage'da token | Session cookie | ADR-011 |
| sessionStorage'da token | Session cookie | ADR-011 |
| MD5/SHA1 hash | Argon2id | ADR-022 |
| `mysql_*` | PDO prepared | ADR-002 |
| ORM | Raw PDO | ADR-002 |
| Firebase JWT | lcobucci/jwt | ADR-059 |
| Plain password log | `[REDACTED]` | ADR-022 |
| Hardcoded secret | `.env` / vault | ADR-034 |
| BypassAuth prod'da | Devre dışı | ADR-008 |

---

## 15. Cross References

| Bölüm | Hedef Vault Dosyası | İlişki |
|-------|---------------------|--------|
| §2 Vizyon | [[../../architecture/l1-security/auth]] | L1 Security |
| §3 Merkezi Auth | [[ADR-043]] | Auth konsolidasyonu |
| §4 JWT+Session | [[ADR-011]], [[ADR-010]] | Hybrid auth |
| §5 RBAC | [[ADR-056]] | Rol sistemi |
| §6 Middleware | [[../../architecture/l1-security/middleware]] | Pipeline |
| §7 Session | [[ADR-011]], [[../../architecture/l1-security/session]] | Session |
| §8 CSRF | [[ADR-010]], [[../../architecture/l1-security/csrf]] | Korumа |
| §9 CSP | [[ADR-012]], [[../../architecture/l1-security/csp]] | Policy |
| §10 SOLID | [[prompt-shared-base]] §4 | Prensipler |
| §11 Clean Code | [[../../brain.md]] §18 | Standartlar |
| §12 Entity | [[ADR-056]] | Domain |
| §14 Yasaklar | [[ADR-010]], [[ADR-011]], [[ADR-022]] | Forbidden |

---

*Prompt 2: Authentication Sistemi v2.0.0 — CoreMusic Prompt System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
