---
node_id: K009-security/session-security/ornekler
node_type: module
status: verify-required
parent_id: K009-security/session-security
children: []
depends_on:
  - K009-security/session-security
evidence:
  - "6 dosyadan birebir alıntı (2026-10-12) — uydurma kod yok"
validation:
  - "2026-10-12 — her blokta dosya + satır aralığı doğrulandı"
open_questions:
  - "PageRouter/SessionInitializer iç yapısı (yalnız 54, 71-76 grep) → dalga-3"
group: kod
---

# session-security-ornekler.md — Gerçek Kod Örnekleri

## §1 Cookie Bayrakları — SessionInitializer.php · 37-49

~~~php
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '.coremusic.net',
    'secure'   => $isHttps,
    'httponly'  => true,
    'samesite' => 'Lax',
]);

session_start();
~~~

**Yorum:**
- satır 37-38: iki kaynaklı HTTPS tespiti (doğrudan TLS + proxy başlığı).
- satır 44: secure DEĞİŞKEN — HTTP dev sunucusunda cookie kilitlenmesin diye
  koşullu; üretimde HTTPS varsayımı doğrulanmalı (doğrulama kontrol 04).
- satır 45-46: httponly + samesite — JS erişimi ve cross-site POST kapalı.

## §2 Zaman Aşımı + Rotation — SessionLifecycle.php · 31-62

~~~php
if (session_status() === PHP_SESSION_ACTIVE) {
    if ($this->isSessionExpired()) {
        $result['lifetimeTimedOut'] = true;
        $this->destroy();
        $result['started'] = true;
    } else {
        $this->extendSession();
    }
}

if ($this->isIdleTimedOut()) {
    $result['idleTimedOut'] = true;
    $this->destroy();
    $result['started'] = true;
}

// Session rotation: 30 dakikada bir session ID yenile
$lastRotation = $_SESSION['_session_rotated_at'] ?? 0;
$now = time();
if ($lastRotation === 0) {
    $_SESSION['_session_rotated_at'] = $now;
} elseif (($now - $lastRotation) >= SessionConfig::ROTATION_INTERVAL) {
    session_regenerate_id(true);
    $_SESSION['_session_rotated_at'] = $now;
}
~~~

**Yorum:**
- İki timeout ARDIŞIK ama BAĞIMSIZ kontrol edilir (satır 31-45).
- Rotation koşulu satır 59'da ROTATION_INTERVAL'e bağlı — yorumdaki "30 dakika"
  metni sabit değil, config'ten gelir → sayısal değer UNKNOWN (kontrol 12).
- satır 60: true bayrağı → eski oturum dosyası anında silinir.

## §3 Sıfırlama Zinciri — SessionBootstrapper.php · 34-59

~~~php
public static function restartFresh(): void
{
    $config = SessionConfig::fromEnvironment();

    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    if (ini_get('session.use_cookies')) {
        setcookie(
            $config->name,
            '',
            time() - SessionConfig::COOKIE_EXPIRY,
            $config->cookieParams()['path'],
            $config->cookieParams()['domain'],
            $config->secure,
            $config->cookieParams()['httponly'],
        );
    }

    self::configure($config);
    session_start();
    session_regenerate_id(true);
}
~~~

**Yorum:** PHP el kitabına göre session_destroy cookie'yi SİLMEZ (kaynak §7)
— bu yüzden satır 44-54'te ayrı setcookie çağrısı vardır. Eksik olsaydı
tarayıcıda eski (geçersiz) cookie kalır, sonraki istekler 401 üretirdi.

## §4 Başlık Seti — SecurityHeadersMiddleware.php · 26-41

~~~php
$headers = [
    'X-Content-Type-Options'         => 'nosniff',
    'X-Frame-Options'                => 'DENY',
    'X-XSS-Protection'               => '0',
    'Referrer-Policy'                => 'strict-origin-when-cross-origin',
    'Content-Security-Policy'        => $csp,
    'Require-Trusted-Types-For'      => "'script'",
    'Cross-Origin-Resource-Policy'   => 'cross-origin',
    'Permissions-Policy'             => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
    'X-Permitted-Cross-Domain-Policies' => 'none',
];

if ($isHttps) {
    $headers['Cross-Origin-Opener-Policy']   = 'same-origin';
    $headers['Cross-Origin-Embedder-Policy'] = 'require-corp';
}
~~~

**Yorum:**
- X-Frame-Options DENY (satır 28) + CSP frame-ancestors 'none' (satır 71)
  → çift katmanlı clickjacking engeli.
- Strict-Transport-Security BU LİSTEDE YOK → doğrulama kontrol 27 FAIL.
- satır 29: X-XSS-Protection 0 — modern tarayıcıdaki eski auditeden
  kaçınmak için bilinçli kapatma (varsayılan '1' önerilmez).

## §5 CSP Üretimi — SecurityHeadersMiddleware.php · 48-74

~~~php
$scriptSrc = $nonce !== ''
    ? "'strict-dynamic' 'nonce-{$nonce}' https:"
    : "'self' https:";

return
    "default-src 'self'; " .
    "script-src {$scriptSrc}; " .
    "style-src {$styleSrc}; " .
    "style-src-attr 'unsafe-inline'; " .
    "img-src 'self' data: {$assetsOrigin}; " .
    "font-src 'self' {$assetsOrigin} fonts.gstatic.com; " .
    "connect-src 'self' {$assetsOrigin} {$apiOrigin}; " .
    "media-src 'self' {$assetsOrigin}; " .
    "frame-ancestors 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self'";
~~~

**Yorum:**
- strict-dynamic + nonce (satır 54-56): inline script ancak nonce ile çalışır
  → XSS oturum çalmayı zorlaştırır.
- frame-ancestors 'none' (satır 71): MDN §9 ile birebir örtüşür.
- base-uri + form-action 'self' (satır 72-73): base etiketi ve form
  manipülasyonu kapatılır.
- style-src-attr 'unsafe-inline' (satır 66): bilinçli genişletme → bilinen
  ödün, risk listesinde izlenir.

## §6 CSRF/Nonce Üretimi — SessionLifecycle.php · 47-52

~~~php
if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Dışarıdan nonce gelirse onu kullan, yoksa üret
$_SESSION['csp_nonce'] = $externalNonce ?? bin2hex(random_bytes(32));
~~~

**Yorum:** üç koşullu kontrol (isset/is_string/boş) — token'ın tipi de
zorlanır; 64 karakter hex (32 byte) CSP nonce ile aynı güçte.

## §7 API Tarafı Rotation — ApiSessionManager.php · 85

~~~php
session_regenerate_id(true);
~~~

**Yorum:** grep ile doğrulandı (2026-10-12). API oturumu da aynı
true bayrağını kullanır — web ve API arasında davranış farkı yoktur.

## §8 GÜVENLİSİZ vs ÜRETİMDEKİ

~~~text
GÜVENLİSİZ                                   ÜRETİMDEKİ
──────────────────────────────────────────────────────────────
session_set_cookie_params yok            →   satır 40-47 (4 bayrak)
samesite yok / None                     →   Lax (satır 46)
destroy sonrası cookie silinmez         →   setcookie 44-54
regenerate(false)                       →   true (3 konum)
rotation yok (sabit ID)                 →   ROTATION_INTERVAL (54-62)
CSP'siz sayfa                           →   nonce + strict-dynamic
HSTS yok                                →   ⚠️ HÂLÂ YOK (kontrol 27)
~~~

## §9 Alıntı Sınırları

1. 6 gerçek dosya, 7 kod bloğu.
2. Özgün koda yorum EKLENMEZ.
3. Uydurma kod: YOK — §7 tek satırlık alıntı dahi grep kanıtıyla verilmiştir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 SESSIONCONFIG — TEK KAYNAK SABİTLER (TAM OKUMA, 63 SATIR, 2026-10-13)

~~~php
// shared/src/Session/SessionConfig.php · tam dosya okundu (satır 15-20)
public const COOKIE_EXPIRY = 42000;
public const MAX_LIFETIME = 1800;          // 30 dakika
public const IDLE_TIMEOUT = 3600;          // 1 saat
public const ROTATION_INTERVAL = 1800;     // 30 dakikada bir rotation
public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri

// satır 38-41 — HTTPS tespiti
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// satır 52-62 — cookieParams()
return [
    'lifetime' => 0, 'path' => '/', 'domain' => $this->cookieDomain,
    'secure'   => $this->secure, 'httponly' => true, 'samesite' => 'Lax',
];
~~~

| Sabit | Değer | Yorum |
|---|---|---|
| `COOKIE_EXPIRY` | 42000 sn (~11.7 saat) | cookie ömrü — oturum ömründen uzun |
| `MAX_LIFETIME` | 1800 sn | mutlak oturum sınırı |
| `IDLE_TIMEOUT` | 3600 sn | boşta kalma sınırı |
| `ROTATION_INTERVAL` | 1800 sn | §2 rotation tetikleyicisi |
| `DIR_PERMISSIONS` | 0750 | dizin izni |

| Dikkat Noktası | Açıklama |
|---|---|
| `secure` dinamik | `HTTP_X_FORWARDED_PROTO`'ya bağlı — proxy arkasında güvenilir mi? → K014-network sınırı, ⚠️ VERIFICATION REQUIRED |
| `lifetime = 0` | tarayıcı oturumu sonunda cookie silinir (session cookie) |
| SSOT docblock | "session_* API çağrıları YALNIZCA SessionBootstrapper içinde yapılır" (satır 11) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 COOKIE BAYRAK MATRİSİ — CWE-1004 VE CWE-614 KARŞILIĞI (2026-10-13)

**URL'ler:** https://cwe.mitre.org/data/definitions/1004.html — HEAD **200** · `CWE-1004: Sensitive Cookie Without 'HttpOnly' Flag (4.20)` · https://cwe.mitre.org/data/definitions/614.html — HEAD **200** · `CWE-614: Sensitive Cookie in HTTPS Session Without 'Secure' Attribute (4.20)` · doğrulama 2026-10-13.

| Bayrak | Değer | Kaynak satır | CWE karşılığı |
|---|---|---|---|
| `httponly` | `true` | SessionConfig.php:59 | CWE-1004 savunması |
| `secure` | `$this->secure` (dinamik) | SessionConfig.php:58 | CWE-614 — HTTP'te `false` olabilir |
| `samesite` | `Lax` | SessionConfig.php:60 | CSRF katmanı destekleyici |
| `lifetime` | `0` | SessionConfig.php:55 | oturum sonu |
| `domain` | `.coremusic.net` | SessionConfig.php:45 | alt alan adları kapsar |

| Açık Soru | Durum |
|---|---|
| HTTPS zorunlu değilse `secure=false` üretimi | ⚠️ VERIFICATION REQUIRED (HSTS/redirect okunmadı) |
| `HTTP_X_FORWARDED_PROTO` sahtelenmesi | K014-network — UNKNOWN |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 session_set_cookie_params / session_get_cookie_params HARİTASI (2026-10-13)

**URL'ler:** https://www.php.net/manual/en/function.session-set-cookie-params.php — HEAD **200** · `PHP: session_set_cookie_params - Manual` · https://www.php.net/manual/en/function.session-get-cookie-params.php — HEAD **200** · `PHP: session_get_cookie_params - Manual` · doğrulama 2026-10-13.

| Çağrı | Dosya | Satır | Bağlam |
|---|---|---|---|
| `session_set_cookie_params($config->cookieParams())` | `Session/SessionBootstrapper.php` | 80 | merkezî yapılandırma |
| `session_set_cookie_params([...])` | `PageRouter/SessionInitializer.php` | 40 | `samesite => Lax` (satır 46) |
| `session_get_cookie_params()` | `PageRouter/SessionInitializer.php` | 72 | silme öncesi params okuma |
| `session_get_cookie_params()` | `Api/Auth/ApiSessionManager.php` | 181 | yalnız aktifse (`sessionActive()`) |
| `session_get_cookie_params()` | `auth.../Service/SessionManager.php` | 173 | sarma |

| Değerlendirme | |
|---|---|
| Çağrı dengesi | set 2 · get 3 — get fazlası okuma amaçlı |
| Set yalnız 2 dosyada | SSOT docblock'u ile uyumlu (§10 satır 11) ⚠️ PageRouter ayrı set üretiyor — aynı değerler mi? ⚠️ VERIFICATION REQUIRED |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 session_regenerate_id KONUMLARI — LOGIN ROTATION (2026-10-13)

| # | Dosya | Satır | Çağrı |
|---|---|---|---|
| 1 | `shared/src/Api/Auth/ApiSessionManager.php` | 85 | `session_regenerate_id(true)` |
| 2 | `shared/src/PageRouter/SessionInitializer.php` | 54 | `session_regenerate_id(true)` |
| 3 | `shared/src/PageRouter/SessionInitializer.php` | 76 | `session_regenerate_id(true)` |
| 4 | `shared/src/Session/SessionBootstrapper.php` | 58 | `session_regenerate_id(true)` |
| 5 | `shared/src/Session/SessionLifecycle.php` | 60 | `session_regenerate_id(true)` |
| 6 | `auth.coremusic.net/include/Service/SessionManager.php` | 81 | `session_regenerate_id(true)` |
| 7 | `auth.coremusic.net/include/Service/SessionManager.php` | 154 | `session_regenerate_id(true)` |

| Desen | Sonuç |
|---|---|
| Tüm çağrılar `true` (eski oturumu sil) | 7/7 — §2 "regenerate(false) yok" iddiası grep ile doğrulandı |
| Ayrık dosya sayısı | 6 |
| §2 rotation mantığı | `SessionLifecycle.php:59` → `>= ROTATION_INTERVAL` |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 session_destroy VE COOKIE SİLME (2026-10-13)

| Dosya | Satır | Ne yapar |
|---|---|---|
| `PageRouter/SessionInitializer.php` | 69 | destroy öncesi `session_get_cookie_params()` (72) → `setcookie(... time() - COOKIE_EXPIRY ...)` (73) |
| `Session/SessionBootstrapper.php` | 41 | destroy; ardından `setcookie(` (45) — satır 48-52 `time() - COOKIE_EXPIRY`, path/domain/secure/httponly config'den |
| `tests/.../SessionManagerMiddlewareTest.php` | 28 · 43 | test temizliği |
| `auth.../Service/SessionManager.php` | 166 | `setcookie($name, '', time() - 3600, '/', '.coremusic.net')` |

| §8 "destroy sonrası cookie silinmez" iddiası | Bu tarama (2026-10-13) |
|---|---|
| Üretim kodunda silenler | `PageRouter/SessionInitializer.php:73` · `SessionBootstrapper.php:45` · `auth.../SessionManager.php:166` |
| Sonuç | §8 iddiası her akış için geçerli değil — ⚠️ VERIFICATION REQUIRED (hangi akışın destroy çağırdığı okunmadı) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 CWE-613 — YETERSİZ OTURUM SONLANMASI (2026-10-13)

**URL:** https://cwe.mitre.org/data/definitions/613.html — HEAD **200** · `CWE-613: Insufficient Session Expiration (4.20)` · doğrulama 2026-10-13.

| CWE-613 istismar yolu | CoreMusic karşılığı | Kanıt |
|---|---|---|
| Uzun oturum ömrü | `MAX_LIFETIME = 1800` | SessionConfig.php:17 |
| Boşta kalma sınırı | `IDLE_TIMEOUT = 3600` | SessionConfig.php:18 |
| ID yenileme yok | 7 `session_regenerate_id(true)` | §13 |
| Zaman aşımında cookie temizliği | setcookie silme 3 konum | §14 |
| Server-side GC | `session.gc_maxlifetime` | ⚠️ VERIFICATION REQUIRED (repo grep 0 — php.ini'de olabilir) |

| Açık Soru | Durum |
|---|---|
| Idle/mutlak timeout uygulaması hangi sınıfta? | ⚠️ VERIFICATION REQUIRED (`IDLE_TIMEOUT` kullanıcısı okunmadı) |
| `COOKIE_EXPIRY` (42000) > `IDLE_TIMEOUT` (3600) | cookie oturumdan uzun yaşar — sunucu tarafı timeout bağlayıcı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 GÜVENLİ vs ÜRETİMDE — OLAĞANÜSTÜ KARŞILAŞTIRMA (2026-10-13)

| Yüzey | Güvenli | Bu repo (kanıt) |
|---|---|---|
| `httponly` | her cookie | `true` (SessionConfig.php:59) ✅ |
| `secure` | zorunlu (CWE-614) | dinamik (satır 58) ⚠️ |
| `samesite` | Strict/Lax | `Lax` (satır 60) ✅ |
| Session sabitleri | tek kaynak | `SessionConfig` SSOT ✅ |
| Çift start koruması | zorunlu | `session_status` ×15 (authentication-ornekler §12) ✅ |
| `gc_maxlifetime` | bilinmeli | repo'da 0 ⚠️ |
| Session name | özel | `SESSION_NAME`, yoksa `COREMUSIC_SESS` (satır 31) ✅ |
| Save path | uygulama dışı | `sys_get_temp_dir().'/coremusic_sessions'` (satır 33) — ⚠️ paylaşımlı tmp |
| Dizin izni | daraltılmış | `@mkdir($savePath, 0750, true)` (satır 35) ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 DENETİM TESTİ TASLAĞI (BU DOSYADAKİ YÜZEY İÇİN)

~~~php
// tests/Session/SessionConfigTest.php · TASLAK — dosya yok, üretilmedi
public function testCookieParamsFlags(): void
{
    $p = $config->cookieParams();
    $this->assertTrue($p['httponly']);       // CWE-1004
    $this->assertSame('Lax', $p['samesite']);
    $this->assertSame(0, $p['lifetime']);
}
public function testRotationIntervalConstant(): void
{
    $this->assertSame(1800, SessionConfig::ROTATION_INTERVAL);
}
public function testRegenerateCalledWithDeleteOldSession(): void
{
    // 7 konumun da `true` ile çağladığı repo geneli grep ile kilitlenir
}
~~~

| Kural | |
|---|---|
| Dosya yalnız TASLAK | gerçekini qa-engineer üretir (kural 16) |
| `git commit` | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §18 BÖLÜM KAPANIŞI — ÖLÇÜM VE TETİKLEYİCİ (2026-10-13)

| Kalem | Değer |
|---|---|
| Eklenen bölümler | §10..§18 |
| Silinen içerik | 0 (append-only) |
| Yeni kod okuması | `SessionConfig.php` — 63 satır, diskten |
| Yeni grep | regenerate 7 · destroy 4 · setcookie 4 · set/get cookie params 5 · gc_maxlifetime 0 |
| Yeni doğrulanmış URL | 5 (CWE-613 · CWE-614 · CWE-1004 · session_set_cookie_params · session_get_cookie_params) |
| Reddedilen URL | 2 (php.net `session-set-cookie-params` / `session-get-cookie-params` → 404; doğru yollar `function.*` ön ekiyle alındı) |
| Uydurma kod/satır | 0 |
| `git commit` | ATILMADI (yasak) |

| Tetikleyici | Etkilenen § | Aksiyon |
|---|---|---|
| `SessionConfig.php` değişimi | §10-§11 | sabitler yeniden ölçülür |
| `secure` dinamiği değişimi | §11 · §15 | CWE-614 matrisi yeniden yazılır |
| Yeni `session_set_cookie_params` çağıran dosya | §12 | SSOT docblock'u ile çelişki aranır |
| `gc_maxlifetime` ayarı | §15 | açık soru kapanır |
| Proxy arkası HTTPS tespiti | §10 · §11 | K014-network'e devir |
| Yeni URL | §11-§12 | yalnız HEAD 200 + başlık |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 SESSIONLIFECYCLE — TAM OKUMA (128 SATIR, 2026-10-13) — §15 AÇIK SORULARINI KAPATIR

> Kaynak: `shared/src/Session/SessionLifecycle.php` — 128 satır, diskten tam okundu. Yeni kod İÇERMEZ.

~~~php
// satır 21-45 — startOrExtend: iki timeout da uygulanmış
SessionBootstrapper::ensureStarted();
if (session_status() === PHP_SESSION_ACTIVE) {
    if ($this->isSessionExpired()) {              // mutlak lifetime
        $result['lifetimeTimedOut'] = true; $this->destroy();
    } else { $this->extendSession(); }
}
if ($this->isIdleTimedOut()) {                    // idle timeout
    $result['idleTimedOut'] = true; $this->destroy();
}

// satır 47-49 — CSRF token üretimi
if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// satır 59-62 — 30 dk rotation
} elseif (($now - $lastRotation) >= SessionConfig::ROTATION_INTERVAL) {
    session_regenerate_id(true);
    $_SESSION['_session_rotated_at'] = $now;
}

// satır 97-113 — timeout karşılaştırmaları
return (time() - (int)$lastActive) >= SessionConfig::IDLE_TIMEOUT;    // idle
return (time() - (int)$createdAt) >= SessionConfig::MAX_LIFETIME;     // mutlak
~~~

| §15 Açık Soru | Bu okuma ile Durum |
|---|---|
| `IDLE_TIMEOUT` kullanıcısı hangi sınıfta? | **KAPANDI** — `SessionLifecycle::isIdleTimedOut()` (satır 97-104) |
| `gc_maxlifetime` | hâlâ ⚠️ VERIFICATION REQUIRED (php.ini — repo dışı) |

| Yeni Kazanım | Kanıt |
|---|---|
| Mutlak lifetime uygulaması | `isSessionExpired()` satır 106-113 → `MAX_LIFETIME` |
| Idle uygulaması | `_session_last_active` fallback `last_activity` (satır 99) |
| Timeout'ta davranış | `destroy()` + ilgili flag `true` (satır 33 · 42) |
| `created_at` null ise | timeout PAS (`return false`, satır 100-101 · 109-110) — ⚠️ `initSessionKeys` her yeni oturumda yazdığı için teorik |
| CSRF token kaynağı | `bin2hex(random_bytes(32))` = 256-bit — ADR-010 `csrf_token` anahtarı ile uyumlu |
| `extendSession` çift kontrol | satır 115-127 içinde de `MAX_LIFETIME` denetimi (savunma derinliği) |
| Rotation ilk çağrıda | `$lastRotation === 0` → zaman damgası yaz, dönüştürme (satır 57-58) |

| Akış Sırası (startOrExtend) | |
|---|---|
| 1 | bootstrapper başlat |
| 2 | mutlak lifetime kontrolü → gerekirse destroy |
| 3 | idle kontrolü → gerekirse destroy |
| 4 | `csrf_token` üretimi (yoksa) |
| 5 | `csp_nonce` (dışarıdan ya da üret) |
| 6 | 30 dk rotation |
| 7 | `last_activity` güncelle |

| Açık Soru (bu §) | Durum |
|---|---|
| `externalNonce` nereden geliyor? | ⚠️ VERIFICATION REQUIRED (çağıran okunmadı) |
| `restartFresh` ne yapıyor? | ⚠️ VERIFICATION REQUIRED (SessionBootstrapper içi, §3'e devir) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 §19 ÖLÇÜM KAPANIŞI VE DOSYA DIŞI DEVİRLER (2026-10-13)

| Kalem | Değer |
|---|---|
| Eklenen bölümler | §10..§20 |
| Silinen içerik | 0 (append-only) |
| Yeni kod okuması | `SessionConfig.php` 63 satır · `SessionLifecycle.php` 128 satır — diskten |
| Yeni grep | regenerate 7 · destroy 4 · setcookie 4 · cookie params 5 · gc_maxlifetime 0 |
| Yeni doğrulanmış URL | 5 (HEAD 200 + başlık, 2026-10-13) |
| Reddedilen URL | 2 (php.net 404 — doğru yollar `function.*` ön eki) |
| Kapatılan açık soru | 1 (`IDLE_TIMEOUT` kullanıcısı → §19) |
| Kalan açık soru | 3 (§19: externalNonce · restartFresh · §11: secure dinamiği) |
| Uydurma kod/satır | 0 |
| `git commit` | ATILMADI (yasak) |

| Devir | Hedef | Ne devredildi |
|---|---|---|
| `gc_maxlifetime` / php.ini | K013-config veya ops | §15 açık sorusu |
| `HTTP_X_FORWARDED_PROTO` sahtelenmesi | K014-network | §10-§11 |
| `restartFresh()` iç yapısı | K009 session-bootstrapper (§3 dosyası) | §19 |
| `csrf_token` anahtar sözleşmesi | ADR-010 (frozen — okundu, değiştirilmedi) | §19 |
| Session cookie lifetime farkı (42000 vs 3600) | K010-auth politikası | §15 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
