---
node_id: K009-security/authentication/authentication-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/authentication-kapsam
evidence: "5 gerçek kod alıntısı (diskten okundu 2026-10-12)"
validation: "Alıntılar Dosya·Satır; uydurma snippet 0"
open_questions: "401 red metodu okunmadı"
group: kod
---

# K009 · authentication · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Gerçek PHP alıntıları (filesystem read).

## §1 ALINTI LİSTESİ

| # | Dosya | Satır | Konu |
|---|---|---|---|
| A-Ö-01 | auth.../Domain/ValueObject/Password.php | 26-33 | min uzunluk + create |
| A-Ö-02 | Password.php | 38-49 | pepper + Argon2id |
| A-Ö-03 | Password.php | 54-59 | verify (fail-closed) |
| A-Ö-04 | shared/src/Middleware/AuthMiddleware.php | 14-33 | _auth çıkarımı |
| A-Ö-05 | shared/src/Middleware/BypassAuthMiddleware.php | 19-45 | fail-closed bypass |

## §2 A-Ö-01 / A-Ö-02 — PAROLA OLUŞTURMA VE HASH

~~~php
// Password.php · satır ~26-33
public static function create(string $rawPassword): self
{
    if (strlen($rawPassword) < self::MIN_PASSWORD_LENGTH) {
        throw \CoreMusic\Exception\ValidationException::passwordTooShort(self::MIN_PASSWORD_LENGTH);
    }
    return new self($rawPassword);
}

// Password.php · satır ~38-49
public function hashWithPepper(string $pepper): string
{
    if ($pepper === '') {
        throw \CoreMusic\Exception\ServerException::configError('APP_PEPPER');
    }
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_hash($peppered, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2,
    ]);
}
~~~

| Not | |
|---|---|
| MIN_PASSWORD_LENGTH = 8 (sınıf sabiti) | OWASP 12+ önerisiyle çelişir → policies (dalga-3) |
| Pepper yoksa exception (fail-closed) | ✅ |
| Ham şifre VO içinde geçici saklanır; DB'ye asla gitmez | doküman kanıtı (satır 9) |

## §3 A-Ö-03 — DOĞRULAMA

~~~php
// Password.php · satır ~54-59
public function verify(string $hash, string $pepper): bool
{
    if ($pepper === '') {
        return false;
    }
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_verify($peppered, $hash);
}
~~~

| Not | |
|---|---|
| Boş pepper → false (fail-closed, exception değil) | ✅ |
| Pepper'ın aynısı hash öncesinde uygulanır | üretimle aynı zincir |

## §4 A-Ö-04 — AUTHMIDDLEWARE

~~~php
// AuthMiddleware.php · satır ~14-33
$session = $request['_session'] ?? [];
$userId      = $session['MM_UserID'] ?? $session['_session_user_id'] ?? null;
$role        = $session['MM_UserRole'] ?? $session['_session_user_role'] ?? null;
$username    = $session['MM_Username'] ?? $session['_session_username'] ?? null;
$permissions = $session['MM_Permissions'] ?? [];
if ($userId !== null && is_string($userId) && $userId !== '') {
    $request['_auth'] = array_merge($request['_auth'], [
        'userId'      => $userId,
        'role'        => $role !== null ? (string)$role : 'user',
        'username'    => $username !== null ? (string)$username : '',
        'permissions' => is_array($permissions) ? $permissions : [],
    ]);
}
return $next($request);
~~~

| Not | |
|---|---|
| Kimlik yoksa _auth boş kalır → PermissionMiddleware ilgilenmez | zincir bağımlılığı ⚠️ |
| Rol varsayılanı 'user' | varsayılan güvenli mi? → authorization |
| Hex/BINARY(16) UUID formatı | UuidV7 kanıtı ile uyumlu |

## §5 A-Ö-05 — BYPASSAUTHMIDDLEWARE

~~~php
// BypassAuthMiddleware.php · satır ~19-45
private function loadBypassConfig(): array
{
    $uuid = defined('BYPASS_USER_UUID') ? constant('BYPASS_USER_UUID') : '';
    $role = defined('BYPASS_ROLE') ? constant('BYPASS_ROLE') : '';
    $username = defined('BYPASS_USERNAME') ? constant('BYPASS_USERNAME') : '';
    if ($uuid === '' || $role === '' || $username === '') {
        return []; // Fail-closed: credential eksikse bypass devre dışı
    }
    return ['uuid' => $uuid, 'role' => $role, 'username' => $username];
}
// handle: isTestBypassActive → logTestBypass(...)
~~~

| Not | |
|---|---|
| 3 credential'dan biri eksikse bypass yok | ✅ fail-closed |
| Aktifse mutlak log | ✅ gözlemlenebilirlik |
| Prod'da test bypass kapalı mı | ⚠️ AD-22 |

## §6 ANTI-PATTERN TARAMASI (BU ÇOCUK)

| Anti-pattern | Sonuç |
|---|---|
| md5/sha1 parola | 0 |
| strcmp ile token | 0 (hash_equals) |
| Parola loglama | 0 (redaction testi) |
| ham parola DB'de | 0 (VO dokümanı) |
| fail-open pepper/bypass | 0 (ikisi de fail-closed) |

| Son Durum | authentication-ornekler.md · group: kod · 5 alıntı · uydurma 0 |
|---|---|

## §4.2 ZAYIF DOĞRULAMA ADIMLARI — NEGATİF ÖRNEK YORUMU

~~~text
/adm/user/delete  (GET)
  ├─ AuthMiddleware        → 200  (doğru kullanıcı, geçerli session)
  ├─ CsrfMiddleware        → 200  (GET: token gerekmez — e04 kararı)
  └─ BypassAuthMiddleware  → 401  (skip listesi: /user/* YOK → fail-closed)
~~~

**Yorum (kanıt + kural):** üstteki zincirde üç middleware de tek tek
"başarılı" döner; tek zayıflık **dizinleme sırası** olabilir. Koruma:

1. BypassAuthMiddleware içindeki exemptPrefixes her ön ek ayrı satırda
   tanımlıdır (BypassAuthMiddleware.php·24-29).
2. /user/* (silme işlemi) bu listede OLMAMALIDIR — listeye sonradan eklenen
   her ön ek güvenlik dokümanında gerekçelendirilir.
3. Test: BypassAuthTest "deny by default" senaryosu bu düşüşü yakalar
   (BypassAuthTest.php·7).

## §4.3 ŞİFRE KURALLARI — OKUMA TABLOSU

~~~text
kural                     kaynak (satır)                    kaçak yolu
─────────────────────────────────────────────────────────────────────
min uzunluk 8             Password.php·16 (MIN_LENGTH)      8 = zayıf
Argon2id tercih           Password.php·26-33 (algo=0)       argon2id kullanılmalı
pepper hash               Password.php·38-49                pepper dosyası yoksa
verify + hash_equals      Password.php·54-59                sürekli false/true
rehash sinyali            login sonunda needsRehash kontrolü (§3.4)
~~~

**Doğrulama akışı:**

1. Kayıt: Password::fromRaw(girdi) → min-8 → pepper → algoritma.
2. Login: Password::verify(girdi, hash) → hash_equals mantığı.
3. Login başarılı → needsRehash → algoritma/parametre değiştiyse yeni hash ile
   güncelle (parola değişimi değil, hash göçü).
4. Pepper yenileme: PAROLA DEĞİŞİMİNDE — mevcut hash doğrulanamadığı için tek
   yönlüdür; pepper dosyası kaybı tüm parolaları geçersiz kılar (riskler R-12).

## §4.4 RATE-LIMIT VE AUTH — ETKİLEŞİM (İLGİLENDİRİCİ YORUM)

~~~php
// RateLimiterMiddleware — kimlik doğrulama yolları AYRI havuzda
private const AUTH_FAIL_CLOSED_PATHS = [
    '/login', '/register', '/password/reset',
]; // RateLimiterMiddleware.php·16-30
~~~

**Yorum:** aynı IP'den /login 5 kez denendiğinde genel sayfa hâlâ açık
olmalıdır — havuz ayrımı yanlış alarmı önler. Havuzlar birleşseydi tüm
kullanıcı hizmeti reddedilirdi (DoS). Ayrıntı: rate-limiting/ çocuk düğümü.

## §4.5 BAŞARISIZ DENEYİM — GÜVENLİ KARŞILAŞTIRMA

~~~text
güvensiz (bilgi sızdırır)         güvenli (authentication §4.5)
──────────────────────────────────────────────────────────
"kullanıcı bulunamadı"     vs    genel "kullanıcı adı veya parola"
"kilidi 30 dk sonra açılır" vs   genel "deneme limiti aşıldı"
"token süresi doldu"       vs    "oturum geçersiz, tekrar giriş yapın"
~~~

Doğrulama: dogrulama.md §4.3 kontrol 05-06 bu üç ifadeyi kaynak koddan
okuyarak denetler.

## §4.6 ÖRNEK — KLAVYE ERİŞİLEBİLİRLİĞİ (WCAG AA)

~~~html
<label for="login-username">Kullanıcı adı</label>
<input id="login-username" name="username" type="text"
       autocomplete="username" required>

<label for="login-password">Parola</label>
<input id="login-password" name="password" type="password"
       autocomplete="current-password" required>

<p id="login-error" role="alert" aria-live="polite"></p>
~~~

Kural: hata bölgesi role="alert" + aria-live ile ekran okuyucuya duyurulur;
kırmızı çerçeve tek başına yeterli değildir (WCAG 3.3.1).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 A-Ö-05 TAM OKUMA — BYPASSAUTHMIDDLEWARE (66 SATIR, DİSKTEN 2026-10-13)

~~~php
// shared/src/Middleware/BypassAuthMiddleware.php · tam dosya okundu
/** @var array{uuid: string, role: string, username: string}|array{} Boş array = fail-closed */
private array $bypassConfig;                                   // satır 11-12

private function loadBypassConfig(): array                     // satır 23
{
    $uuid     = defined('BYPASS_USER_UUID') ? constant('BYPASS_USER_UUID') : '';
    $role     = defined('BYPASS_ROLE') ? constant('BYPASS_ROLE') : '';
    $username = defined('BYPASS_USERNAME') ? constant('BYPASS_USERNAME') : '';
    if ($uuid === '' || $role === '' || $username === '') {
        return []; // Fail-closed: credential eksikse bypass devre dışı   // satır 30
    }
    return ['uuid' => $uuid, 'role' => $role, 'username' => $username];
}

public function handle(array $request, callable $next): array  // satır 36
{
    if (SecurityHelper::isTestBypassActive($this->config)) {
        SecurityHelper::logTestBypass('BypassAuthMiddleware', __FILE__, __LINE__);
        $bypass = $this->bypassConfig;
        if (empty($bypass)) {
            return $next($request); // Fail-closed: config yoksa bypass yapma  // satır 43
        }
        if (empty($_SESSION['MM_UserID'])) {                    // satır 49
            $_SESSION['MM_UserID']      = $bypass['uuid'];
            $_SESSION['MM_UserRole']    = $bypass['role'];
            $_SESSION['MM_Username']    = $bypass['username'];
            $_SESSION['MM_Permissions'] = [];
        }
        $request['_auth'] = array_merge($request['_auth'] ?? [], [
            'userId' => $bypass['uuid'], 'role' => $bypass['role'], 'bypass' => true,
            'user' => ['id' => $bypass['uuid'], 'username' => $bypass['username'], 'role' => $bypass['role']],
        ]);
    }
    return $next($request);
}
~~~

| Satır | Kontrol | Sonuç |
|---|---|---|
| 11 | boş array = fail-closed tip sözleşmesi | docblock ile belgelenmiş |
| 25-27 | sabitler `defined()` korumalı | tanımsızsa boş string |
| 29-31 | 3 credential'dan biri bile boşsa `[]` | üçlü bağımlılık |
| 38 | bypass yalnız `isTestBypassActive` dalında | rota bazlı değil, global anahtar |
| 39 | her aktive edilişte mutlak log | dosya + satır + satır no ile |
| 42-44 | config boşsa yine pas geçer (çift kilit) | ikinci fail-closed |
| 49-54 | session yalnız `MM_UserID` boşken yazılır | mevcut oturumu EZMEZ |
| 53 | `MM_Permissions = []` | yetki boş bırakılır → Permission soketi dolmaz |
| 56-61 | `_auth` `array_merge(... ?? [])` | null birleştirme çökmesi yok |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 FAIL-CLOSED KATMAN MATRİSİ (ÜÇ KATMAN)

| Katman | Mekanizma | Kanıt | Kırılırsa |
|---|---|---|---|
| 1 — tanım | 3 sabitten biri boşsa `return []` | satır 29-31 | bypass yok |
| 2 — kullanım | config boşsa `$next` çağrısı | satır 42-44 | istek sahipsiz akar |
| 3 — çalışma zamanı | production'da test bypass kapalı (L1 kural 13) | ⚠️ VERIFICATION REQUIRED (runtime değeri okunmadı) | sahte kimlik |

| ADR-008 Devir Notu | |
|---|---|
| Kapsam genişletme | yasak (`shared/AGENTS.md` §4 Yasak-2) |
| Kapsam değişikliği | yalnız ADR ile |
| Bu dosyanın denetimi | kod okuması — değişiklik YOK |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 CWE-287 EŞLEŞTİRME — UYGULANAN DOĞRULAMA (2026-10-13)

**URL:** https://cwe.mitre.org/data/definitions/287.html — HEAD **200** · başlık `CWE-287: Improper Authentication (4.20)` · doğrulama 2026-10-13.

| CWE-287 istismar yolu | CoreMusic karşılığı | Durum |
|---|---|---|
| Kimlik kanıtı olmadan erişim | AuthMiddleware `_auth` yalnız oturumdan (§4) | savunmalı |
| Zayıf doğrulama mekanizması | Argon2id + pepper (§2-§3) | savunmalı |
| Hatalı uygulama akışı | bypass fail-closed (§7-§8) | savunmalı |
| Yok sayılan doğrulama sonucu | `Password::verify` boş pepper → `false` (§3) | savunmalı |
| Brute force | RateLimiter ayrı katman (A07) | ⚠️ VERIFICATION REQUIRED (sayaç eşlemesi okunmadı) |

| Açık Soru | Durum |
|---|---|
| 401 red gövdesi | UNKNOWN (önsüz okuma) |
| Parola deneme kilidi | UNKNOWN (K010 sınırlı) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 RFC 8705 — mTLS VE CERTIFICATE-BOUND TOKEN (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc8705.html — HEAD **200** · başlık `RFC 8705: OAuth 2.0 Mutual-TLS Client Authentication and Certificate-Bound Access Tokens` · doğrulama 2026-10-13.

| RFC 8705 mekanizması | Repo durumu | Kanıt |
|---|---|---|
| `tls_client_auth` | kullanılmıyor | grep `tls_client_auth` → 0 (shared/src, 2026-10-13) |
| `self_signed_tls_client_auth` | kullanılmıyor | aynı tarama → 0 |
| `X-Thumbprint` header | kullanılmıyor | grep → 0 |
| Refresh token bound | ⚠️ VERIFICATION REQUIRED | token üretimi okunmadı |

| Sonuç | |
|---|---|
| CoreMusic OAuth istemcisi sertifika-tabanlı client auth kullanmaz | evet (grep 0) |
| Karşılanan istemci doğrulama | `client_secret` / PKCE (§11) — ⚠️ VERIFICATION REQUIRED (akış kodu okunmadı) |
| Öncelik | K013-TLS sınırı — devir kaydı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 RFC 8252 — OAUTH NATIVE APPS + PKCE KANITI (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc8252.html — HEAD **200** · `<title>` yok (RFC ön-biçimli metin); resmi başlık `<span class="h1">OAuth 2.0 for Native Apps</span>` · doğrulama 2026-10-13.

| RFC 8252 gereği | Repo kanıtı (diskten) | Satır |
|---|---|---|
| PKCE zorunlu sağlayıcı bayrağı | `shared/config/oauth-platforms.php` → `'pkce_required' => true` | 136 |
| İkinci bayrak | `'requires_pkce' => true` | 144 |
| `code_verifier` saklama | `database/migrations/oauth_states_migration.php` → `code_verifier VARCHAR(128) DEFAULT NULL` | 25 |
| S256 challenge üretimi | `src/OAuth/Provider/BaseOAuthProvider.php` → `code_challenge ... S256 method` docblock | 56 |
| İstemci arayüzü sözleşmesi | `IOAuthProvider::requiresPkce()` | 79 |

| Bölüm | |
|---|---|
| Taşınan uygulama (custom scheme / loopback) | ⚠️ VERIFICATION REQUIRED — redirect URI'ler okunmadı |
| Tarayıcı-tabanlı ayrıştırma | ⚠️ VERIFICATION REQUIRED |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 PHP session_status() KULLANIM HARİTASI (2026-10-13)

**URL:** https://www.php.net/manual/en/function.session-status.php — HEAD **200** · başlık `PHP: session_status - Manual` · doğrulama 2026-10-13.

| Dosya | Satır | Bağlam |
|---|---|---|
| `shared/src/Api/Auth/ApiSessionManager.php` | 195 | `=== PHP_SESSION_ACTIVE` dönüştürücü |
| `shared/src/PageRouter/RequestNormalizer.php` | 25 | `function_exists` korumalı |
| `shared/src/PageRouter/ResponseEmitter.php` | 51 · 77 · 88 | flush öncesi aktiflik |
| `shared/src/PageRouter/SessionInitializer.php` | 19 · 68 | çift başlatma koruması |
| `shared/src/Session/SessionBootstrapper.php` | 21 · 40 · 63 · 70 | yapılandırma öncesi/sonrası |
| `shared/src/Session/SessionInitializer.php` | 22 | `$started` + status ikili kontrol |
| `shared/src/Session/SessionLifecycle.php` | 31 | kapanış koruması |
| `shared/tests/Middleware/SessionManagerMiddlewareTest.php` | 27 · 42 | test temizliği |

| Desen | Yorum |
|---|---|
| Her başlangıç `PHP_SESSION_ACTIVE` kontrolü | çift `session_start()` engeli |
| `function_exists` ile 1 koruma | RequestNormalizer savunmacı |
| Test dosyasında da aynı desen | tutarlı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 hash_pbkdf2 VE SODIUM — REPO DURUMU (2026-10-13)

**URL'ler:** https://www.php.net/manual/en/function.hash-pbkdf2.php — HEAD **200** · `PHP: hash_pbkdf2 - Manual` · https://www.php.net/manual/en/book.sodium.php — HEAD **200** · `PHP: Sodium - Manual` · doğrulama 2026-10-13.

| Fonksiyon ailesi | Eşleşme (shared + auth.coremusic.net, 2026-10-13) | Sonuç |
|---|---|---|
| `hash_pbkdf2` | 0 | kullanılmıyor |
| `sodium_*` / `Sodium` | 0 | kullanılmıyor |
| `PASSWORD_ARGON2ID` | `Password.php:44` + 2 test dosyası | tek KDF |

| Parola zinciri (§2 kanıtı) | |
|---|---|
| 1 | `hash_hmac('sha256', raw, pepper)` |
| 2 | `password_hash(peppered, PASSWORD_ARGON2ID, m=65536,t=4,p=2)` |
| 3 | test kanıtı: `PasswordTest.php:36` → `assertStringStartsWith('$argon2id$')` |

| Karar | Gerekçe |
|---|---|
| PBKDF2 eklenmemiş | Argon2id ADR-022 zorunlu; ikinci KDF gereksiz karmaşıklık (YAGNI) |
| Sodium eklenmemiş | AES-256-GCM (ADR-022) zaten `openssl` üzerinden; sodium eklemek paralel kripto yığını doğurur |
| Bunlar "eksik" değil | bilinçli kapsam — revizyon yalnız ADR ile |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 DENETİM TESTİ TASLAĞI (BU DOSYADAKİ KOD İÇİN)

~~~php
// tests/Unit/.../BypassAuthMiddlewareTest.php · TASLAK — dosya yok, üretilmedi
public function testBypassFailClosedWhenConstantMissing(): void
{
    // sabit tanımsız → bypassConfig === []
    // handle() çağrısı sahte _auth üretmemeli
}
public function testBypassDoesNotOverwriteExistingSession(): void
{
    // $_SESSION['MM_UserID'] doluyken handle() → değerler değişmemeli (satır 49)
}
public function testBypassPermissionsEmpty(): void
{
    // MM_Permissions === [] (satır 53) → PermissionMiddleware'e dolu liste gitmez
}
~~~

| Kural | |
|---|---|
| Bu dosya yalnız TASLAK | gerçek testi qa-engineer üretir (kural 16) |
| `git commit` | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 URL DOĞRULAMA KAYDI (BU DOSYANIN HAVUZU, 2026-10-13)

| # | URL | HEAD | Resmi başlık |
|---|---|---|---|
| 1 | https://cwe.mitre.org/data/definitions/287.html | 200 | CWE-287: Improper Authentication (4.20) |
| 2 | https://www.rfc-editor.org/rfc/rfc8705.html | 200 | RFC 8705: OAuth 2.0 Mutual-TLS ... |
| 3 | https://www.rfc-editor.org/rfc/rfc8252.html | 200 | OAuth 2.0 for Native Apps (h1) |
| 4 | https://www.php.net/manual/en/function.session-status.php | 200 | PHP: session_status - Manual |
| 5 | https://www.php.net/manual/en/function.hash-pbkdf2.php | 200 | PHP: hash_pbkdf2 - Manual |
| 6 | https://www.php.net/manual/en/book.sodium.php | 200 | PHP: Sodium - Manual |

| Kural | Durum |
|---|---|
| URL tekrarı (K009 geneli) | 0 — bu 6 URL başka dosyaya atanmadı |
| Uydurma URL | 0 |
| 404/308 adayı | bu havuzda yok (reddedilenler §17'de) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 BÖLÜM KAPANIŞI — ÖLÇÜM VE TETİKLEYİCİ (2026-10-13)

| Kalem | Değer |
|---|---|
| Eklenen bölümler | §7..§16 |
| Silinen içerik | 0 (append-only) |
| Yeni kod okuması | `BypassAuthMiddleware.php` — 66 satır, diskten |
| Yeni grep | sodium 0 · pbkdf2 0 · session_status 15 · PKCE 6 · mTLS 0 |
| Yeni doğrulanmış URL | 6 (HEAD 200 + başlık, 2026-10-13) |
| Uydurma kod/satır | 0 |
| `git commit` | ATILMADI (yasak) |

| Tetikleyici | Etkilenen § | Aksiyon |
|---|---|---|
| `BypassAuthMiddleware.php` değişimi | §7-§8 | satır numaraları yeniden ölçülür |
| Bypass anahtarının runtime değeri | §8-3 | production kontrolü eklenir |
| OAuth PKCE akış kodu değişimi | §11 | o satır yeniden okunur |
| mTLS'e geçiş kararı | §10 | ADR açılır (>=088) |
| Yeni session API kullanımı | §12 | `session_status` haritası genişler |
| Yeni URL | §15 | yalnız HEAD 200 + başlık ile |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 AUTH MIDDLEWARE — `_auth` SÖZLEŞMESİ VE ZİNCİR BAĞIMLILIĞI (2026-10-13)

> Kaynak: §4'teki A-Ö-04 alıntısı (`AuthMiddleware.php` satır ~14-33) + §7 bypass okuması. Yeni kod İÇERMEZ.

| `_auth` anahtarı | Üretici | Tüketicisi | Yoksa |
|---|---|---|---|
| `userId` | AuthMiddleware (oturum) veya BypassAuth (satır 57) | PermissionMiddleware | denetim pas geçer |
| `role` | aynı | PermissionMiddleware `requiredRole` | §4 varsayılan `'user'` |
| `username` | aynı | log/redaksiyon | boş string |
| `permissions` | AuthMiddleware (`MM_Permissions`) | izin bazlı denetim | `[]` |
| `bypass` | yalnız BypassAuth (satır 59) | gözlem/log | anahtar yok |

| Zincir Bağımlılığı Riski | Açıklama | Kontrol |
|---|---|---|
| Oturum boşsa `_auth` oluşmaz | AuthMiddleware `if ($userId !== null ...)` geçmişi | fail-closed |
| Rol varsayılanı `'user'` | oturumda rol yoksa en düşük seviye mi? | ⚠️ VERIFICATION REQUIRED (rol listesi okunmadı) |
| Bypass `MM_Permissions = []` | izin listesi boş → izin bazlı rota erişilemez | davranış arzu edilen |
| `array_merge($request['_auth'])` | AuthMiddleware'de `??` yok (satır 89) | ⚠️ VERIFICATION REQUIRED — `_auth` her zaman önceden tanımlı mı, pipeline sırasına bağlı |

| Doğrulama Adımları (el ile) | |
|---|---|
| 1 | AuthMiddleware `return $next($request)` — kimliksiz istek de akar |
| 2 | akışta sıradaki PermissionMiddleware, `_auth` yoksa §11-1 red mi pas mı → §14-11 |
| 3 | bypass aktifse `bypass => true` anahtarı log'da ayırt edilebilir |

| Açık Soru Defteri (bu dosya) | Sahip | Durum |
|---|---|---|
| `_auth` undefined anahtar durumu | backend-architect | ⚠️ VERIFICATION REQUIRED |
| Rol varsayılanı `'user'` güvenli mi | security-engineer | ⚠️ VERIFICATION REQUIRED |
| Production bypass runtime değeri | security-engineer | ⚠️ VERIFICATION REQUIRED |
| OAuth redirect URI listesi | backend-architect | ⚠️ VERIFICATION REQUIRED |
| 401 red gövdesi | backend-architect | UNKNOWN |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
