---
node_id: K0100-identitiy/identity-master
node_type: subdomain
status: draft
parent_id: K0100-identitiy
children: []
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "shared/src/Security/JwtService.php L24 ALGORITHM='RS256' — 2026-10-13"
  - "shared/src/Session/SessionConfig.php L16-20 sabitler (MAX_LIFETIME=1800 < IDLE_TIMEOUT=3600) — 2026-10-13"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php L33-38 · L83-100 · L103-107 — 2026-10-13"
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php L38-49 Argon2id + HMAC pepper — 2026-10-13"
  - ".ai/.sql/mysql/coremusic_auth.sql L270-296 (users) — 2026-10-13"
validation:
  - "identity-dogrulama.md §B (kod checklist) — satır numaraları bu revizyonda yeniden ölçüldü"
  - "identity-web-arastirma.md §A (12 tekil URL, tarih 2026-10-13)"
open_questions:
  - "MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600 paradoxu kasıtlı mı?"
  - "Refresh token rotasyonu repo'da kanıtlanmadı"
  - "MFA / cihaz sertifikası implementasyonu repo'da yok (PLANNED)"
group: mimari
---

# K0100-identitiy — identity-master (bütünleştirici mimari anatomi)

- **Kaynak:** v2 §2.10 · ADR-097 (grup ekseni) · gerçek repo kodu (ölçüm 2026-10-13)
- **Yazım kilidi:** `identitiy` yazımı korunur — bu dosyanın hiçbir bölümünde düzeltilmez
- **Kanıt disiplini:** her kod iddiası `Dosya:`/`Satır:` çifti ile bağlanır; kanıt yoksa `⚠️ VERIFICATION REQUIRED` · bilinmeyen değer `UNKNOWN`
- **Şema:** §A Modül Ağacı · §B Kararlar · §C Çapraz Modül Kuralları · §D Yol Haritası

## §A Modül Ağacı

### §A.1 Düğüm kimliği

| Alan | Değer |
|---|---|
| node_id | `K0100-identitiy/identity-master` |
| node_type | `subdomain` |
| status | `draft` |
| Roller | kimlik modeli · kullanıcı · rol · yetki · oturum · token · credential · SSO · MFA |
| Sınır (içerir) | kimlik doğrulama *akışları*, session yaşam döngüsü, token issuance/revocation, RBAC kararı |
| Sınır (içermez) | rate limit politikası, CSRF/CSP başlık politikası, encryption altyapı seçimi → K009-security |
| Grup | `mimari` |
| Ölçüm | 2026-10-13 (satır sayıları ve alıntılar yeniden doğrulandı) |

### §A.2 Ağaç (kök → kademe-2 → kademe-3)

```text
K0100-identitiy (kök)
├── kullanici-modeli      kimlik anahtarı + profil alanları
├── rol-ve-yetki          rol modeli + permission matrisi
├── oturum-yonetimi       session yaşam döngüsü + API köprüsü
├── dogrulama-servisleri  login/register/reset use-case'leri
├── sso-entegrasyonu      auth.coremusic.net redirect + 10 provider
├── mfa-ve-sertifika      TOTP / passkey / cihaz sertifikası (PLANNED)
├── oidc/                 kademe-3 — OIDC anayasası
├── oauth2/               kademe-3 — Authorization Code + PKCE
└── jwt/                  kademe-3 — RS256 + jti revocation
```

Kademe-2 klasörleri bu revizyonda yalnız `account/` ve `credentials/` altındaki
role dosyalarını besler; `oidc/`, `oauth2/`, `jwt/` klasörleri §D yol haritasında
planlanır ve bu revizyon **üretmez** (kapsam dışı — bkz §D.4).

### §A.3 Katman → bileşen → dosya (ölçülmüş satır sayıları)

| Katman | Bileşen | Dosya | Satır |
|---|---|---|---|
| Presentation (API) | `AuthenticationMiddleware` | `shared\src\Api\Middleware\AuthenticationMiddleware.php` | 134 |
| Presentation (API) | `AuthorizationMiddleware` | `shared\src\Api\Middleware\AuthorizationMiddleware.php` | 111 |
| Presentation (HTTP) | `AuthMiddleware` | `shared\src\Middleware\AuthMiddleware.php` | 35 |
| Presentation (HTTP) | `SessionManagerMiddleware` | `shared\src\Middleware\SessionManagerMiddleware.php` | 25 |
| Presentation (HTTP) | `PermissionMiddleware` | `shared\src\Middleware\PermissionMiddleware.php` | 64 |
| Presentation (HTTP) | `CsrfMiddleware` | `shared\src\Middleware\CsrfMiddleware.php` | 65 |
| Presentation (HTTP) | `RateLimiterMiddleware` | `shared\src\Middleware\RateLimiterMiddleware.php` | 146 |
| Presentation (HTTP) | `OriginCheckMiddleware` | `shared\src\Middleware\OriginCheckMiddleware.php` | 122 |
| Presentation (Router) | `AuthGuard` | `shared\src\PageRouter\AuthGuard.php` | 80 |
| Application | `IAuthService` (8 metot) | `shared\src\Contracts\Auth\IAuthService.php` | 15 |
| Application | `LoginRequest` · `RegisterRequest` | `shared\src\Api\Dto\Request\` | 72 · 94 |
| Application | `ServiceRegistry` | `shared\src\Api\Registry\ServiceRegistry.php` | 132 |
| Domain | `ISessionManager` (21 metot) | `shared\src\Contracts\Auth\ISessionManager.php` | 28 |
| Domain | `IUserRepository` (20 metot) | `shared\src\Contracts\Auth\IUserRepository.php` | 51 |
| Domain | identity domain event (5 dosya) | `shared\src\Events\Domain\` | 9 PHP içinde 5'i |
| Domain | identity integration event (2 dosya) | `shared\src\Events\Integration\` | 3 PHP içinde 2'si |
| Infrastructure | `JwtService` | `shared\src\Security\JwtService.php` | 137 |
| Infrastructure | `SessionLifecycle` | `shared\src\Session\SessionLifecycle.php` | 128 |
| Infrastructure | `SessionConfig` | `shared\src\Session\SessionConfig.php` | 63 |
| Infrastructure | `ApiSessionManager` | `shared\src\Api\Auth\ApiSessionManager.php` | 216 |
| Infrastructure | `OAuthManager` | `shared\src\OAuth\OAuthManager.php` | 262 |
| Infrastructure | `OAuthRepository` | `shared\src\OAuth\OAuthRepository.php` | 120 |
| Infrastructure | `BaseOAuthProvider` | `shared\src\OAuth\Provider\BaseOAuthProvider.php` | 142 |
| Infrastructure | `AuthRouteConfig` | `shared\src\Config\AuthRouteConfig.php` | 71 |
| Infrastructure | `SessionKeys` | `shared\src\Security\SessionKeys.php` | 28 |
| Auth domain (alt servis) | `AuthService` | `auth.coremusic.net\include\Service\AuthService.php` | 349 |
| Auth domain (alt servis) | `UserRepository` | `auth.coremusic.net\include\Repository\UserRepository.php` | 349 |
| Auth domain (değer nesnesi) | `Password` (Argon2id + pepper) | `auth.coremusic.net\include\Domain\ValueObject\Password.php` | 67 |
| API rota tablosu | auth POST uçları | `api.coremusic.net\config\routes.php` | 120 |

Ölçüm notu: `shared\src\Middleware\` altında **11** PHP dosyası, `shared\src\Api\Middleware\`
altında **7** PHP dosyası, `shared\src\Api\Bff\` altında **5** PHP dosyası vardır (2026-10-13 sayımı).

### §A.4 Katman kuralı (tek yön bağımlılık)

```text
Presentation  : middleware + route + guard  — kimlik kararı üretmez, yalnız okur
Application   : service + DTO + sözleşme    — framework bağımsız
Domain        : IUserRepository + event'ler — dış bağımlılık taşımaz
Infrastructure: JwtService + repository     — PDO / OpenSSL / dosya erişimi burada
```

Kural ihlali örneği (yapılmaması gereken): `PermissionMiddleware` rol kararını
`$request['_auth']` üzerinden okur, `$routeMeta` üzerinden karşılaştırır ve sonucu
kendi içinde üretir; veritabanına gitmez. Karar üretimi Presentation'da kaldığı için
katman ihlali yoktur.

### §A.5 Kod kanıtı — guard zinciri

```php
// Dosya: shared\src\PageRouter\AuthGuard.php / Satır: 15-24
public function check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
{
    return $this->checkAuthRequired($uri, $route, $isSpaRequest)
        ?? $this->checkRole($uri, $route)
        ?? $this->checkPermission($uri, $route)
        ?? $this->checkAuthRedirectRoute($uri, $isSpaRequest)
        ?? $this->checkAuthenticatedUserOnAuthPage($uri, $isSpaRequest)
        ?? $this->checkLogout($uri, $isSpaRequest)
        ?? null;
}
```

`null-coalescing` zinciri sıralıdır: ilk `null` dönmeyen adım kararı verir. Zincir
`checkAuthRequired` ile başlar (yönlendirme), `checkRole`/`checkPermission` ile devam
eder (403), `checkLogout` ile biter — sıralama, "önce girişe yönlendir, sonra yetkiyi
denetle" mantığını zorlar.

### §A.6 Kimlik modeli bileşenleri

| Bileşen | Tanım | Repo kanıtı |
|---|---|---|
| User | `MM_UserID` · `MM_Username` · `MM_Email` · `MM_DisplayName` · `MM_AccountType` · `MM_Image` · `cm_gender` | `shared\src\Security\SessionKeys.php` L7-15 |
| Account (legacy kök) | kayıt/giriş/reset akışlarının kullanıcı yüzeyi | `shared\src\Api\Dto\Request\LoginRequest.php` (72 satır) |
| Role | rol adı + `permissions[]` — `findRolesForUser` | `shared\src\Contracts\Auth\IUserRepository.php` L33 |
| Permission | string listesi; `in_array(..., true)` strict eşleşme | `shared\src\Middleware\PermissionMiddleware.php` L36-42 |
| Session | `csrf_token` · `csp_nonce` · `_session_last_active` · `_session_rotated_at` | `shared\src\Session\SessionLifecycle.php` L47-66 |
| Token (JWT) | claims: `iss/aud/sub/iat/nbf/exp/jti` | `shared\src\Security\JwtService.php` L44-72 |
| Reset token | `saveResetToken` · `findValidResetToken` · `markResetTokenUsed` | `shared\src\Contracts\Auth\IUserRepository.php` L15-18 |
| Auth key | `saveAuthKey` · `findValidAuthKey` · `markAuthKeyUsed` · `revokeAuthKeysForUser` | `shared\src\Contracts\Auth\IUserRepository.php` L19-26 |
| Access token kaydı | `saveAccessToken` · `isValidAccessToken` · `revokeAccessTokensForUser` | `shared\src\Contracts\Auth\IUserRepository.php` L40-50 |
| OAuth connection | provider · provider_user_id · encrypted token · scopes · profile | `shared\src\OAuth\OAuthRepository.php` L27-44 |
| OAuth state | sha256(state) · provider · user_id · code_verifier · 10 dk TTL | `shared\src\OAuth\OAuthRepository.php` L85-92 |
| Credential | `users.password_hash` (Argon2id) + vault tabloları | `auth.coremusic.net\...\Password.php` L38-49 · `coremusic_auth.sql` L144 |
| Device / MFA | cihaz sertifikası, TOTP, passkey | repo'da YOK → `mfa-ve-sertifika/` PLANNED |

`ISessionManager` tek oturum arayüzüdür (21 metot, `Dosya: shared\src\Contracts\Auth\ISessionManager.php / Satır: 7-27`):
`setAuthUser` · `setRegisteredUser` · `getUserId` · `isAuthenticated` · `destroy` ·
`regenerateId` · `get/set/remove` · `setGender/getGender` · `setPendingRedirect/consumePendingRedirect` ·
`regenerateCspNonce/getCspNonce` · `isIdleExpired` · `touch` · `rotateIfNeeded` ·
`clearDisplayCookies` · `getCookieParams` · `all`.

### §A.7 Envanter (kanıt paketi K1-K13, sayım 2026-10-13)

| # | Dosya | Satır | K010 rolü |
|---|---|---|---|
| K1 | `shared\src\Security\JwtService.php` | 137 | token issuance + validation + jti |
| K2 | `shared\src\Session\SessionConfig.php` | 63 | oturum sabitleri + cookie |
| K3 | `shared\src\Session\SessionLifecycle.php` | 128 | oturum yaşam döngüsü |
| K4 | `shared\src\Api\Auth\ApiSessionManager.php` | 216 | API stateless köprü |
| K5 | `shared\src\Contracts\Auth\ISessionManager.php` | 28 | oturum sözleşmesi (21 metot) |
| K6 | `shared\src\Contracts\Auth\IUserRepository.php` | 51 | kullanıcı/rol/token repository sözleşmesi |
| K7 | `shared\src\Api\Middleware\AuthenticationMiddleware.php` | 134 | API auth girişi |
| K8 | `shared\src\OAuth\OAuthManager.php` | 262 | OAuth provider + state + şifreleme |
| K9 | `shared\src\OAuth\OAuthRepository.php` | 120 | bağlantı + state persistence |
| K10 | `shared\src\PageRouter\AuthGuard.php` | 80 | sayfa guard zinciri |
| K11 | `shared\src\Config\AuthRouteConfig.php` | 71 | auth route + redirect |
| K12 | `shared\src\Middleware\PermissionMiddleware.php` | 64 | RBAC kararı |
| K13 | `shared\src\Security\SessionKeys.php` | 28 | oturum anahtar sabitleri |

## §B Kararlar

### §B.1 Karar defteri

| # | Karar | Gerekçe | Kanıt | Durum |
|---|---|---|---|---|
| D-01 | JWT algoritması **RS256** (asimetrik) | `alg:none` ve HS256 downgrade kapalı; public key doğrulama yeterli | `JwtService.php` L24 | kanıtlı |
| D-02 | Token doğrulama **fail-closed** (`catch (\Throwable)` → `null`) | imza/süre/format hatası ayrıntı sızmaz, istek reddedilir | `JwtService.php` L83-109 | kanıtlı |
| D-03 | `jti` + `user_tokens` kaydı ile **revocation** | logout/şifre değişimi anında geçersizlik | `AuthenticationMiddleware.php` L88-89 · `IUserRepository.php` L40-50 | kanıtlı |
| D-04 | Session ID **rotation** 1800 sn'de bir | session fixation azaltımı | `SessionLifecycle.php` L54-62 | kanıtlı |
| D-05 | Cookie `httponly=true` + `samesite=Lax` + `lifetime=0` | XSS'e karşı okunmazlık, CSRF yüzeyi küçültme, tarayıcı oturum cookie'si | `SessionConfig.php` L51-62 | kanıtlı |
| D-06 | OAuth `state` **sha256 + tek kullanımlık silme** | CSRF + replay engeli | `OAuthManager.php` L157-183 | kanıtlı |
| D-07 | OAuth token **AES-256-GCM** ile saklanır | at-rest koruması, authenticated encryption | `OAuthManager.php` L188-205 | kanıtlı |
| D-08 | Parola **Argon2id (m=65536, t=4, p=2) + HMAC-SHA256 pepper** | memory-hard hash + pepper ile DB sızıntısında pe'er koruması | `Password.php` L38-49 | kanıtlı |
| D-09 | API köprüsü **stateless** (`ApiSessionManager`) | API isteklerinde PHP session state'i tutulmaz | `ApiSessionManager.php` L158-172 | kanıtlı |
| D-10 | Public route **allowlist** ile tanımlı | login/register/forgot-password auth'suz çağrılır | `AuthenticationMiddleware.php` L33-38 | kanıtlı |

### §B.2 Paradox defteri (karar bekleyenler)

| # | Paradox | Kanıt | Etki | Durum |
|---|---|---|---|---|
| P-01 | `MAX_LIFETIME` (1800) < `IDLE_TIMEOUT` (3600) | `SessionConfig.php` L17-18 | Mutlak sınır idle'dan önce dolar; idle timeout kodu asla tetiklenmez | `⚠️ VERIFICATION REQUIRED` |
| P-02 | `COOKIE_EXPIRY` (42000) > `MAX_LIFETIME` (1800) | `SessionConfig.php` L16-17 | Cookie hayatta iken sunucu oturumu çoktan ölmüş olur | `⚠️ VERIFICATION REQUIRED` |
| P-03 | `ROTATION_INTERVAL` (1800) = `MAX_LIFETIME` (1800) | `SessionConfig.php` L17-19 | Rotation, oturumun son anında tetiklenir; pratikte tek rotation | `⚠️ VERIFICATION REQUIRED` |
| P-04 | `response_type='session'` OIDC/OAuth2 standart değeri değil | `AuthRouteConfig.php` L61-67 | Özel SSO protokolü — `code`/`id_token`/`token` ile eşleşmiyor | `⚠️ VERIFICATION REQUIRED` |
| P-05 | API köprüsü `rotateIfNeeded()` ve `isIdleExpired()` hep `false` | `ApiSessionManager.php` L158-172 | API oturumlarında rotation + idle timeout yok (stateless tasarım mı, eksiklik mi?) | `⚠️ VERIFICATION REQUIRED` |
| P-06 | `api.coremusic.net` GET `/api/v1/auth/login` `implemented=false` | `api.coremusic.net\config\routes.php` L36-39 | GET akışı 405'e düşer; POST akışı `implemented=true` (L53-58) | kanıtlı (davranış), kasıt `UNKNOWN` |

### §B.3 Karar kanıtı — fail-closed validate

```php
// Dosya: shared\src\Security\JwtService.php / Satır: 83-109
public function validate(string $token): ?array
{
    if ($token === '') {
        return null;
    }

    try {
        $claims = JWT::decode($token, new Key($this->publicKey(), self::ALGORITHM));
    } catch (\Throwable) {
        return null;
    }

    if (($claims->iss ?? null) !== $this->issuer) {
        return null;
    }
    if (($claims->aud ?? null) !== $this->audience) {
        return null;
    }
    if (!isset($claims->sub, $claims->jti) || $claims->sub === '' || $claims->jti === '') {
        return null;
    }

    $arr = (array) $claims;
    return $arr;
}
```

Üç zorunlu katman: (1) algoritma sabit (`self::ALGORITHM`), (2) `iss`/`aud` eşleşmesi,
(3) `sub` + `jti` doluluğu. Herhangi biri başarısızsa `null` → middleware 401 üretir.

### §B.4 Karar kanıtı — oturum sabitleri

```php
// Dosya: shared\src\Session\SessionConfig.php / Satır: 16-20
public const COOKIE_EXPIRY = 42000;
public const MAX_LIFETIME = 1800;
public const IDLE_TIMEOUT = 3600;
public const ROTATION_INTERVAL = 1800;
public const DIR_PERMISSIONS = 0750;
```

Bu beş sabit tek kaynaktır (SSOT) ve P-01/P-02/P-03 paradoxlarının kaynağıdır;
paradoxlar ADR-097'ye devredilmiştir, bu dosya tarafından **çözülmez**.

### §B.5 Karar kanıtı — parola hash (auth domain)

```php
// Dosya: auth.coremusic.net\include\Domain\ValueObject\Password.php / Satır: 38-49
public function hashWithPepper(string $pepper): string
{
    if ($pepper === '') {
        throw \CoreMusic\Exception\ServerException::configError('APP_PEPPER');
    }
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_hash($peppered, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost'   => 4,
        'threads'     => 2,
    ]);
}
```

Boş pepper durumunda istisna fırlatılır (fail-closed). Doğrulama tarafı aynı dosya
L54-61'de boş pepper'da doğrudan `false` döner. Test kanıtı:
`auth.coremusic.net\tests\Unit\Domain\ValueObject\PasswordTest.php` L36
(`assertStringStartsWith('$argon2id$', $hash)`).

## §C Çapraz Modül Kuralları

### §C.1 Kural tablosu

| # | Kural | Bağlandığı düğüm | Kanıt |
|---|---|---|---|
| C-01 | Kimlik kararı veren tek middleware `AuthenticationMiddleware` (401) ve `PermissionMiddleware` (403) | K010 içi | `AuthenticationMiddleware.php` L103-107 · `PermissionMiddleware.php` L56-63 |
| C-02 | Rate limit **K009**'dadır; kimlik akışı yalnız fail-closed path listesi ile farkındadır | K009 sınırı | `RateLimiterMiddleware.php` L20-30 |
| C-03 | CSRF token'ı K010 üretir (session), doğrulama K009'dadır (middleware) | K009 sınırı | `SessionLifecycle.php` L47-49 · `CsrfMiddleware.php` L42-44 |
| C-04 | CSP nonce'u K010 üretir (session), başlık K009 politikasıdır | K009 sınırı | `SessionLifecycle.php` L51-52 |
| C-05 | Domain event yayılımı K008'e aittir; K010 yalnız üretir | K008 sınırı | `shared\src\Events\Domain\` (5 identity event'i) |
| C-06 | Fiziksel şema (BCNF, migration) K007'ye aittir | K007 sınırı | `.ai\.sql\mysql\coremusic_auth.sql` (298 satır) |
| C-07 | Token özel anahtarı dosyadan okunur, commit edilmez, loglanmaz | K009 + K010 ortak | `JwtService.php` L111-124 (L20 yorumu) |
| C-08 | `in_array(..., true)` strict eşleşme zorunlu — tip zehirlenmesi engeli | K010 içi | `PermissionMiddleware.php` L39 · `AuthorizationMiddleware.php` L64 · L77 |
| C-09 | Her fail-closed yol `securityEvent` yazar; sessiz 403/401 yok | K009 log sözleşmesi | `PermissionMiddleware.php` L50-54 · `CsrfMiddleware.php` L52-55 |
| C-10 | Public route tanımı iki yerdedir: RouteTable `public=true` + `PUBLIC_ROUTE_PATTERNS` | K010 içi | `AuthorizationMiddleware.php` L94-101 · `AuthenticationMiddleware.php` L33-38 |

### §C.2 Kural kanıtı — yetki reddi gözlemlenebilirliği

```php
// Dosya: shared\src\Middleware\PermissionMiddleware.php / Satır: 47-62
private function forbidden(string $code, string $message): array
{
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('authz_denied', [
        'reason' => $code,
        'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
    ]);

    return [
        'httpStatus' => 403,
        'type'       => 'json',
        'body'       => ['error' => $code, 'message' => $message],
        'headers'    => [],
        'halt'       => true,
    ];
}
```

`halt=true` isteği zincirin devamına taşımaz. Aynı desen `CsrfMiddleware::fail()`
(L49-63) ve `RateLimiterMiddleware` 429 dalında (L88-99) tekrarlanır → K010'un
K009'a devrettiği tek gözlem yüzeyi `LoggerFactory::securityEvent`'tir.

### §C.3 Kural kanıtı — fail-closed yollar (kimlik ↔ rate limit sınırı)

```php
// Dosya: shared\src\Middleware\RateLimiterMiddleware.php / Satır: 20-30
public const AUTH_FAIL_CLOSED_PATHS = [
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/logout',
    '/set-gender',
    '/select-gender',
    '/session',
    '/validate-key',
];
```

Bu liste K010'un kimlik akışlarının K009 rate limit'ine verdiği **sabit** sınırdır:
cache kullanılamazsa bu yollar 429 ile reddedilir, genel yollar ise gözlemlenerek
`fail-open`'a düşer (`RateLimiterMiddleware.php` L66-68). Kural: kimlik akışı asla
fail-open değildir.

### §C.4 Kural kanıtı — API girişinin iki kolu

```php
// Dosya: shared\src\Api\Middleware\AuthenticationMiddleware.php / Satır: 83-97
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (str_starts_with($authHeader, 'Bearer ') && $this->jwt !== null) {
    $claims = $this->jwt->validate(substr($authHeader, 7));

    if ($claims !== null) {
        $revoked = $this->accessTokenValidator !== null
            && !($this->accessTokenValidator)((string) $claims['jti']);
        if (!$revoked) {
            $request['_auth_user'] = [
                'id'           => (string) $claims['sub'],
                'authenticated' => true,
                'method'       => 'jwt',
            ];
            return $next($request);
        }
    }
}
```

İki koşul birlikte sağlanmalıdır: imza/geçerlilik (`validate`) **ve** jti revocation
kontrolü. `accessTokenValidator` null ise revocation kontrolü atlanır → bağımlılık
kayıdı C-11: `accessTokenValidator` DI ile bağlanmazsa revocation devre dışı kalır
(bağlantı noktası `⚠️ VERIFICATION REQUIRED`).

### §C.5 Kural kanıtı — rotalar (API auth yüzeyi)

```php
// Dosya: api.coremusic.net\config\routes.php / Satır: 53-65
'/api/v1/auth/login' => [
    'service'     => 'auth',
    'handler'     => 'authController',
    'action'      => 'login',
    'public'      => true,
    'implemented' => true,
    'validation'  => [
        'email'    => ['required' => true, 'type' => 'string'],
        'password' => ['required' => true, 'type' => 'string', 'min' => 8],
        'gender'   => ['in' => ['male', 'female', 'neutral']],
    ],
],
```

Doğrulama kuralları rota tablosunda deklare edilir (boundary validation); service
katmanı bunu tekrarlamaz. `public=true` `AuthorizationMiddleware::isPublicRoute`
(L94-101) tarafından okunur.

### §C.6 Çapraz tablo — düğüm ↔ sorumluluk

| Konu | K010 sahibi | Komşu sahip | Tekrarlı üretim riski |
|---|---|---|---|
| Session cookie parametreleri | `SessionConfig::cookieParams` | — | yok |
| CSRF üretimi | `SessionLifecycle` L47-49 | doğrulama `CsrfMiddleware` (K009) | üretim/doğrulama ayrımı korunmalı |
| Login deneme limiti | yalnız fail-closed liste | `CacheRateLimiter` (K009) | çifte limit üretilmemeli |
| Parola hash | `Password` VO (auth domain) | `credential_vault` sahipliği belirsiz | ADR-097 K4 bekliyor |
| Token imzası | `JwtService` (RS256) | Apple MusicKit token'ı (K013 iddiası) | `⚠️ VERIFICATION REQUIRED` |
| Rate limit anahtarı | — | `SecurityHelper::getRateLimitKey` (K009) | K010 key üretmez |

### §C.7 Yasaklar (bu node'un yapmayacakları)

| # | Yasak | Gerekçe |
|---|---|---|
| Y-01 | `SELECT *` yazmak | ADR-002 (`ApiKeyRepository.php` L29 docblock) |
| Y-02 | ORM kullanmak | ADR-002 (aynı docblock) |
| Y-03 | Özel anahtarı metin/JSON/log'a yazmak | `JwtService.php` L20 yorumu |
| Y-04 | Kimlik akışını fail-open bırakmak | C-03 · `RateLimiterMiddleware.php` L66-68 (yalnız genel yollar) |
| Y-05 | `identitiy` yazımını düzeltmek | kullanıcı kuralı — dizin adı ve tüm atıflar değişmez |
| Y-06 | Kanıtsız iddia üretmek (`TBD`, uydurma sürüm/CVE/API) | zero-hallucination (AGENTS.md §3) |

## §D Yol Haritası

### §D.1 Kademe-3 dosya planı

| Klasör | Dosya (10 rol) | Ağırlık |
|---|---|---|
| `oidc/` | `oidc-anayasa` · `oidc-arayuzler` · `oidc-token-modeli` · `oidc-akislari` · `oidc-kod-ornekleri` · `oidc-web-arastirma` · `oidc-dogrulama` · `oidc-riskler` · `oidc-test-stratejisi` · `oidc-evrensel-dil` (+ `index.md`) | her rol dosyası ≥500 satır |
| `oauth2/` | aynı 10 rol adı `oauth2-` önekiyle (+ `index.md`) | her rol dosyası ≥500 satır |
| `jwt/` | aynı 10 rol adı `jwt-` önekiyle (+ `index.md`) | her rol dosyası ≥500 satır |

### §D.2 Faz tablosu

| Faz | Kapsam | Bağımlılık | Durum |
|---|---|---|---|
| F-0 | Kök 7 rol dosyası + `account/` + `credentials/` (13 dosya ≥500) | — | bu revizyon |
| F-1 | Root `index.md` 40-100 satıra indirme | F-0 | bu revizyon |
| F-2 | `oidc/` 10 dosya | ADR-097 (P-04 response_type kararı) | PLANNED |
| F-3 | `oauth2/` 10 dosya | F-2 (PKCE + provider kataloğu) | PLANNED |
| F-4 | `jwt/` 10 dosya | D-01/D-02/D-03 karar onayı | PLANNED |
| F-5 | Paradox kapanışı (P-01…P-05) | ADR-097 | PLANNED |
| F-6 | `mfa-ve-sertifika/` tasarımı | ürün kararı (MFA zorunluluğu) | PLANNED |

### §D.3 Çıkış kriterleri

| # | Kriter | Ölçüm |
|---|---|---|
| E-1 | 13 rol dosyası ≥500 satır | dosya-satır sayımı |
| E-2 | Root `index.md` 40-100 satır | dosya-satır sayımı |
| E-3 | Yeni URL'ler 1653'lük envanterle kesişmez | kesişim = 0 |
| E-4 | Her dosyada ≥1 doğrulanmış `Dosya:`/`Satır:` kanıtı | satır-bazlı okuma |
| E-5 | Mojibake / dolgu / `TBD` / `parent:` anahtarı = 0 | metin taraması |
| E-6 | `git commit` ATILMAZ | subagent kuralı |

### §D.4 Kapsam dışı bu turda

| Kalem | Neden |
|---|---|
| `oidc/` · `oauth2/` · `jwt/` klasörleri | F-2…F-4 fazları; ADR-097 kararı bekliyor |
| `.ai/architecture/index.md` | orkestratöre ait (yazma yasağı) |
| `K011-application` · `K001` · `K005` · `K000-isletim-sistemi` · `K003` · `K004` · `K013` · `K017-K019` | beyaz liste dışı |
| `*.php` · `*.js` · `*.css` · `*.sql` kaynak kod | salt-okunur kanıt yüzeyi |
| Kademe-2 `authorization/` · `mfa/` · `password/` · `session/` dosyaları | manifestte 13 hedef dışında (≥500 zaten) |

### §D.5 Açık sorular defteri

| # | Soru | Etki | Sahip |
|---|---|---|---|
| Q1 | `MAX_LIFETIME` < `IDLE_TIMEOUT` paradoxu kasıtlı mı? | oturum güvenliği + UX | ADR-097 |
| Q2 | Refresh token rotasyonu var mı (repo'da kanıt yok)? | token güvenliği | `jwt/` fazı |
| Q3 | `response_type='session'` standart mı, özel mi? | SSO interoperability | `oidc/` fazı |
| Q4 | `revokeAuthKeysForUser` hangi olayda çağrılıyor? | hesap kurtarma güvenliği | kod incelemesi |
| Q5 | `cm_gender` oturum anahtarı kişisel veri midir (KVKK)? | uyumluluk | ADR-097 K4 |
| Q6 | `accessTokenValidator` bağımlılığı DI'da bağlı mı? | revocation etkinliği | kod incelemesi |

## Web Kaynakları (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OAuth 2.0 Authorization Framework, Authorization Code + `state` tanımı bu dokümanda yapılır; CoreMusic'in SSO sınırı bu çerçevenin istemci tarafıdır | https://www.rfc-editor.org/info/rfc6749 | 2026-10-13 | HEAD 200 doğrulandı |
| Refresh token, erişim token'ının ömründen ayrı bir kavramdır; CoreMusic kök katmanında refresh döngüsü kanıtlanmamıştır (§B.1 D-03, §D.5 Q2) | https://oauth.net/2/refresh-tokens/ | 2026-10-13 | HEAD 200 doğrulandı |

## §E.6 OAuth 2.0 keşif-metadata kapı eşlemesi (RFC 8414 çapraz)

| # | Kapı | Standart karşılığı | CoreMusic karşılığı | Durum |
|---|---|---|---|---|
| E-6.1 | Authorization server metadata belgesi yayımlanıyor mu | RFC 8414 `/​.well-known/oauth-authorization-server` | `AuthRouteConfig` üzerinden yayınlanan SSO kökü | ⚠️ VERIFICATION REQUIRED — repo'da uç nokta kanıtı yok |
| E-6.2 | `issuer` alanı birebir adresle mi | RFC 8414 `issuer` | `getAuthUrl()` değerinin kökü | UNKNOWN — doğrulanmadı |
| E-6.3 | `response_types_supported` gerçekten desteklenen değerlerle mi | RFC 8414 alanı | kodda `response_type='session'` (§D.5 Q3) — standart dışıysa metadata'ya yazılamaz | açık (ADR-097) |
| E-6.4 | `grant_types_supported` kod akışıyla tutarlı mı | RFC 8414 alanı | refresh döngüsü kanıtlanmamış (§B.1 D-03) | açık |

**Standart künyesi:** RFC 8414 — *Authorization Server Metadata*. Bu kapılar metadata BELGESİNİ değil, metadata'nın iddia edebileceği alanların ne olduğunu tanımlar; metadata yoksa satır yine de `⚠️` kalır.

## §E.7 Kaynak göstergeleri ve kimlik seviyeleri (RFC 8707 · NIST SP 800-63-3 çapraz)

| # | Kapı | Kaynak | CoreMusic karşılığı | Durum |
|---|---|---|---|---|
| E-7.1 | Token hangi resource için istendi | RFC 8707 `resource` parametresi | `accessTokenValidator` kapsamı | UNKNOWN — validator okunmadı |
| E-7.2 | Kimlik en az AAL1'de mi | NIST SP 800-63-3 AAL ayrımı | `cm_gender` oturum anahtarı dahil oturum açma | ⚠️ VERIFICATION REQUIRED (KVKK: §D.5 Q5) |
| E-7.3 | Oturum yenileme devri ASM'ye bağlı mı | NIST SP 800-63-3 AAL2/AAL3 oturum yenileme kuralı | refresh token rotasyonu kanıtlanmadı (§D.5 Q2) | açık |

**Doğrulama notu:** AAL seviyesi ataması bir ÜRÜN kararıdır; bu dosya onu atamaz, yalnız hangi kapıda beklediğini yazar.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.** — Eklenen iki standart çaprazı yalnız kapı tanımını genişletir; repo kanıtı okunmadı, dolayısıyla dosyanın doğrulanmış iddia sayısı değişmez.

## §E.8 Web Kaynakları (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Authorization server metadata alanları RFC 8414'te tanımlanmıştır | https://datatracker.ietf.org/doc/html/rfc8414 | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |
| Token'ın hedef kaynağı `resource` parametresiyle bildirilir (RFC 8707) | https://datatracker.ietf.org/doc/html/rfc8707 | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |
| Dijital kimlik doğrulama seviyeleri NIST SP 800-63-3'te tanımlıdır | https://pages.nist.gov/800-63-3/ | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode