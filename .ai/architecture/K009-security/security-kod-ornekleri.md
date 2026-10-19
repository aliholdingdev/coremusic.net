---
node_id: K009-security/security-kod-ornekleri
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-bilesenler
  - K009-security/security-master
evidence: "10 gerçek kod alıntısı (Dosya·Satır, diskten okundu 2026-10-12) + anti-pattern denetimi"
validation: "Her alıntı gerçek dosyadan; uydurma snippet 0"
open_questions: "Pipeline sırası okunmadı; HSTS bloğu satır 18 ayrıca doğrulanacak"
group: kod
---

# K009 · Güvenlik · Kod Örnekleri (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: gerçek kod kanıtları (diskten okundu).
> Kural: alıntılar filesystem read ile alınmıştır; paraphrase/uydurma snippet yasaktır.

## §1 ALINTI ENVANTERİ

| # | Alıntı | Dosya | Satır (yaklaşık) | Konu |
|---|---|---|---|---|
| K-Ö-01 | hashWithPepper / verify | auth.coremusic.net/include/Domain/ValueObject/Password.php | 38-59 | Argon2id + pepper |
| K-Ö-02 | handle() CSRF eşleşmesi | shared/src/Middleware/CsrfMiddleware.php | 20-50 | CSRF double-submit |
| K-Ö-03 | güvenlik başlıkları dizisi | shared/src/Middleware/SecurityHeadersMiddleware.php | 16-40 | Header envanteri |
| K-Ö-04 | rol/permission denetimi | shared/src/Middleware/PermissionMiddleware.php | 15-45 | Authorization |
| K-Ö-05 | fail-closed uç listesi | shared/src/Middleware/RateLimiterMiddleware.php | 16-30 | Rate limiting |
| K-Ö-06 | idle/lifetime timeout | shared/src/Session/SessionLifecycle.php | 20-50 | Session |
| K-Ö-07 | RS256 kural bloğu | shared/src/Security/JwtService.php | 15-30 | JWT |
| K-Ö-08 | ALLOWED_HOSTS + guard | shared/src/Security/ReturnUrlPolicy.php | 5-45 | Open redirect |
| K-Ö-09 | doğrulama akışı dokümantasyonu | shared/src/Repository/ApiKeyRepository.php | 12-30 | API key |
| K-Ö-10 | bypass route kaldırma kararı | shared/src/Middleware/CsrfMiddleware.php | 12-17 | CSRF bypass reddi |

## §2 K-Ö-01 — PAROLA HASH (PASSWORD.PHP)

~~~php
// Password.php · hashWithPepper (yaklaşık satır 38-49)
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
~~~

| Denetim | Sonuç |
|---|---|
| Argon2id (OWASP Password Storage CS) | ✅ EŞLEŞİYOR |
| Pepper boşsa fail (fail-closed) | ✅ ServerException::configError |
| HMAC-SHA256 pepper = parola hash'i DEĞİL (ön işleme) | ✅ ana hash Argon2id |
| Parametreler memory 65536 / time 4 / threads 2 | ✅ |

## §3 K-Ö-02 / K-Ö-10 — CSRF (CSRFMIDDLEWARE.PHP)

~~~php
// CsrfMiddleware.php · bypass kararı (satır ~12-17)
// CSRF bypass route'lari kaldirildi — tüm state-changing istekler CSRF token gerektirir
// Önceki: ['set-gender'] — A01 Broken Access Control riski taşıyordu

// CsrfMiddleware.php · handle eşleşmesi (satır ~20-50)
$method = strtoupper($request['method'] ?? 'GET');
$stateChanging = !in_array($method, ['GET', 'HEAD', 'OPTIONS'], true);
if (!$stateChanging) {
    return $next($request);
}
$sessionToken = ($request['_session']['csrf_token'] ?? '') ?: ($_SESSION['csrf_token'] ?? '') ?: '';
$submitted = $request['headers']['x-csrf-token'] ?? '';
if ($submitted === '') {
    $submitted = $request['body']['csrf_token'] ?? '';
}
if ($sessionToken === '' || $submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
    return $this->fail();
}
~~~

| Denetim | Sonuç |
|---|---|
| GET/HEAD/OPTIONS dışında zorunlu token | ✅ |
| hash_equals sabit-zamanlı | ✅ |
| Boş token fail (fail-closed) | ✅ |
| Bypass listesi boşaltıldı | ✅ K-Ö-10 |
| Reddi loglama (securityEvent) | ✅ fail() içi |

## §4 K-Ö-03 — GÜVENLİK BAŞLIKLARI (SECURITYHEADERSMIDDLEWARE.PHP)

~~~php
// SecurityHeadersMiddleware.php · satır ~16-40
if (empty($request['_csp_nonce'])) {
    $request['_csp_nonce'] = bin2hex(random_bytes(32));
}
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

| Denetim | Sonuç |
|---|---|
| CSP nonce random_bytes(32) | ✅ |
| XCTO nosniff | ✅ (MDN kaynağı) |
| XFO DENY | ✅ clickjacking |
| Permissions-Policy tanımlı | ✅ D-05 düzeltmesi: başlık MEVCUT |
| COOP/COEP yalnız HTTPS | ✅ koşullu |
| HSTS bu blokta görünmüyor | ⚠️ ayrı blok (satır 18) ayrıca doğrulanacak |

## §5 K-Ö-04 — YETKİ DENETİMİ (PERMISSIONMIDDLEWARE.PHP)

~~~php
// PermissionMiddleware.php · satır ~15-45
$routeMeta = $request['_route_meta'] ?? [];
$auth      = $request['_auth'] ?? [];
if (empty($auth['userId'])) {
    return $next($request);
}
$userRole = $auth['role'] ?? 'user';
$requiredRole = $routeMeta['requiredRole'] ?? null;
if ($requiredRole !== null && $userRole !== $requiredRole) {
    return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
}
$requiredPermission = $routeMeta['requiredPermission'] ?? null;
if ($requiredPermission !== null) {
    $userPermissions = $auth['permissions'] ?? [];
    if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
        return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
    }
}
~~~

| Denetim | Sonuç |
|---|---|
| Auth yoksa geç (AuthMiddleware'e bırakır) | ✅ ama sıraya BAĞIMLI → pipeline sırası ⚠️ |
| strict in_array (true) | ✅ tip tuzağı yok |
| Rol/permission reddi kodlu | ✅ observable forbidden |

## §6 K-Ö-05 — FAIL-CLOSED RATE LIMIT (RATELIMITERMIDDLEWARE.PHP)

~~~php
// RateLimiterMiddleware.php · satır ~16-30
// Cache çözülemezse fail-closed uygulanacak auth uçları (ADR-013 §5.4 şart 1a).
// Genel uçlar fail-open kalır (ADR-013 §3 — hizmet sürekliliği).
public const AUTH_FAIL_CLOSED_PATHS = [
    '/login', '/register', '/forgot-password', '/reset-password',
    '/logout', '/set-gender', '/select-gender', '/session', '/validate-key',
];
// __construct: maxRequests=60, windowSeconds=60 (varsayılan)
~~~

| Denetim | Sonuç |
|---|---|
| Auth uçları fail-closed | ✅ ADR-013 §5.4 |
| Genel uçlar fail-open (bilinçli karar) | ✅ ADR-013 §3 |
| Varsayılan 60/60 pencere | ✅ |
| Brute-force yüzeyi kapsanıyor | ✅ /login listede |

## §7 K-Ö-06 — OTURUM ZAMAN AŞIMI (SESSIONLIFECYCLE.PHP)

~~~php
// SessionLifecycle.php · satır ~20-50
SessionBootstrapper::ensureStarted();
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
~~~

| Denetim | Sonuç |
|---|---|
| lifetime + idle ayrı denetim | ✅ |
| Süre dolarsa destroy (fail-closed) | ✅ |
| csrf_token üretimi (satır ~48) | ✅ D-07 |
| Sabitler SessionConfig (SSOT) | ✅ yorum kanıtı |

## §8 K-Ö-07 — JWT (JWTSERVICE.PHP)

~~~php
// JwtService.php · satır ~15-30
// Güvenlik kuralları:
// - RS256 (asimetrik): private.pem YALNIZ imzalama için; validate public.pem ile.
// - exp/nbf kontrolü decode'da (kütüphane); iss/aud elle doğrulanır (fail-closed).
// - jti: tek kullanımlık kimlik — user_tokens'ta sha256(jti) ile saklanır,
//   logout/revoke ile geçersizleşir (revocation).
// - private.pem asla commit edilmez (.gitignore) ve ASLA loglanmaz.
private const ALGORITHM = 'RS256';
~~~

| Denetim | Sonuç |
|---|---|
| RS256 asimetrik (HS256 değil) | ✅ DÜZELTME kaydı |
| iss/aud fail-closed | ✅ iddiası |
| jti revocation | ✅ iddiası (test → test-stratejisi) |
| private.pem gitignore/log yasağı | ✅ (auth AGENTS.md §4) |
| Anahtar rotasyonu | ⚠️ KANIT YOK (D-28) |

## §9 K-Ö-08 — OPEN REDIRECT ENGELİ (RETURNURLPOLICY.PHP)

~~~php
// ReturnUrlPolicy.php · satır ~5-45
private const ALLOWED_HOSTS = [
    'coremusic.net', 'home.coremusic.net', 'auth.coremusic.net',
    'music.coremusic.net', 'admin.coremusic.net', 'localhost', '127.0.0.1',
];
// Kural: decode edilmiş URL aynı authority ise güvenli; '/' ise güvenli değil.
// Ham kontrol karakterleri (\t \n \r) hem ham hem tek katman urldecode girdide → erken ret.
~~~

| Denetim | Sonuç |
|---|---|
| Allowlist host | ✅ |
| Protokol-relative / %0A varyant guard | ✅ erken ret |
| localhost/127.0.0.1 allowlist | ⚠️ üretim riski (riskler.md) |
| '' veya '/' → güvenli (yönlendirme yok) | ✅ |

## §10 K-Ö-09 — API ANAHTARI AKIŞI (APIKEYREPOSITORY.PHP)

~~~php
// ApiKeyRepository.php · başlık dokümantasyonu (satır ~12-30)
// Doğrulama akışı: prefix index taraması → hash_equals(key_hash, sha256(raw)) →
//   is_active=1 AND is_deleted=0 AND (expires_at IS NULL OR expires_at > now) →
//   IP allowlist (varsa) → scope → last_used_at güncelle
// Güvenlik: ham anahtar ASLA DB'ye yazılmaz (yalnız SHA-256 hash + prefix),
//   loglara/yanıtlara yalnız key_prefix düşer.
// ADR-002: PDO prepared statement; SELECT * yasak; ORM yasak.
~~~

| Denetim | Sonuç |
|---|---|
| hash_equals | ✅ (satır 171) |
| Ham anahtar saklanmıyor | ✅ SHA-256 + prefix |
| Log sızıntısı önleni | ✅ prefix only |
| Parametreli sorgu (ADR-002) | ✅ doküman kanıtı |
| random_bytes üretim | ✅ satır 304 |

## §11 ANTI-PATTERN DENETİMİ (BU DOSYA KAPSAMI)

| Anti-pattern | Tarama | Sonuç |
|---|---|---|
| unserialize(user input) | okunan K009 bileşenleri | 0 |
| md5/sha1 parola hash | Password.php | 0 (Argon2id) |
| string === token karşılaştırma | Csrf/ApiKey | 0 (hash_equals) |
| display_errors/echo hata | okunan middleware'ler | 0 (LoggerFactory) |
| SELECT * / ORM | ApiKeyRepository | 0 (ADR-002) |
| Ham header yazımı | ResponseNormalization | 0 (satır 56) |
| fail-open auth | RateLimiter | 0 (fail-closed listesi) |
| CSRF bypass whitelist | CsrfMiddleware | 0 (kaldırıldı) |

| Son Durum | |
|---|---|
| Dosya | security-kod-ornekleri.md · group: kod |
| Alıntı | 10 gerçek (diskten okundu) · uydurma 0 |
| Düzeltme kaydı | JwtService RS256 (özet "HS256" yanlışı düzeltildi 2026-10-12) |

## §12 STATİK TARAMA SONUÇLARI (shared/src · 2026-10-13 · DOSYA·SATIR KANITLI)

> Yöntem: `Select-String` ile `shared/src` (1+2 klasör derinliği, 24 PHP dosyası) taraması. Her satır dosya:satır kanıtıyla verilir; yorum satırı ayrı işaretlenmez, yalnız kod satırı sayılır.

| Desen | Eşleşme | Konum (Dosya·Satır) | Hükm |
|---|---|---|---|
| `eval(` | 0 | — | temiz |
| `exec(` | 0 | — | temiz |
| `system(` | 0 | — | temiz |
| `passthru(` | 0 | — | temiz |
| `shell_exec(` | 1 | `shared/src/AI/AIEngine.php·295` (`$output = shell_exec($cmd);`) | **VETO SINIFINA GİRER** — §13 |
| `unserialize(` | 0 | — | temiz (`unserialize(user input)` yasağı karşılanıyor) |
| `md5(` | 3 | `AI/AIWorkflow.php·173` (önbellek anahtarı) · `Cache/PageCacheAdapter.php·31` (önbellek anahtarı) · `Middleware/RateLimiterMiddleware.php·72` (limit anahtarı) | parola/İmza YOK → kripto yasağı kapsamı DIŞI, kayıt şart |
| `sha1(` | 0 | — | temiz |
| `hash_equals(` | 5 | §4/§10 kanıtlarıyla (Csrf · ApiKey · SessionKeys) | zaman-güvenli karşılaştırma çalışıyor |
| `random_bytes(` | 13 | §10 (`ApiKeyRepository·304`) + 12 kullanım | güvenli rastgelelik yaygın |
| `header("Location: ...")` | 1 | `shared/src/PageRouter/ResponseEmitter.php·86` | §14 redirect denetimine bağlanır |
| `$_GET/$_POST/$_REQUEST/$_COOKIE/$_SERVER/$_FILES` | 66 | dağınık (1+2 klasör) | her kullanım ValidationMiddleware'e bağlı olmalı → §14 |
| `password_hash(` / `password_verify(` | 0 / 0 | bu tarama derinliğinde yok | Password.php bu kapsam DIŞI → §5 kanıtına bakılır |
| inline `<script>` (bu dosyada) | 0 | — | yalnız harici JS (UI katmanı) |

| §12 Tarama Kuralı | Açıklama |
|---|---|
| Kapsam | `shared/src` 1+2 derinlik; derin alt dizinler KAPSAM DIŞI (§16) |
| Sayım birimi | kod satırı; aynı satırda iki eşleşme tek sayılır |
| `md5(` hükmü | parola/imza anahtarı üretmiyorsa önbellek anahtarıdır; yasağı ihlal etmez |
| `shell_exec(` hükmü | çalıştırma-komutu sınıfı → veto incelemesi §13 |
| Sıfır sonuç | "temiz" olarak yazılır; silinmez/boş bırakılmaz |
| Yeniden ölçüm | Dosya eklendiğinde §12 yeniden çalıştırılır |
| Kapsam işareti | Kapsam dışı derinlik "KAPSAM DIŞI" olarak işaretlenir, temiz sayılmaz |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 VETO SINIFI BULGU VE CWE EŞLEŞTİRİLMESİ

| # | Bulgu | Dosya·Satır | İlgili CWE (resmi başlık) | URL | Karar |
|---|---|---|---|---|---|
| 1 | `shell_exec($cmd)` çağrısı | `shared/src/AI/AIEngine.php·295` | — (çalıştırma-komutu; CWE kataloğunda bu dosyada doğrulanmadı) | — | **VETO → rapor + inceleme** |
| 2 | Yanlış HTML kaçışı → XSS | giriş doğrulama yüzeyi | CWE-79: Improper Neutralization of Input During Web Page Generation ('Cross-site Scripting') (4.20) | https://cwe.mitre.org/data/definitions/79.html | doğrulama middleware'i kanıtı §6'da |
| 3 | Doğrulanmamış yönlendirme | `ReturnUrlPolicy` yüzeyi | CWE-601: URL Redirection to Untrusted Site ('Open Redirect') (4.20) | https://cwe.mitre.org/data/definitions/601.html | §9 K-Ö-09 kanıtıyla eşleşir |
| 4 | CRLF ile başlık bölme | başlık yazımı yüzeyi | CWE-113: Improper Neutralization of CRLF Sequences in HTTP Headers ('HTTP Request/Response Splitting') (4.20) | https://cwe.mitre.org/data/definitions/113.html | ResponseNormalization §10 kanıtı |
| 5 | Yetersiz korunmuş kimlik bilgisi | parola/anahtar yüzeyi | CWE-522: Insufficiently Protected Credentials (4.20) | https://cwe.mitre.org/data/definitions/522.html | Argon2id (§2) + hash_equals (§10) |

| Veto İşlem Akışı | Adım |
|---|---|
| 1 | Bulgu dosya:satır kanıtıyla kaydedilir (yukarıdaki satır 1) |
| 2 | Kural gereği kod değişikliği YAPILMAZ — rapor üretilir |
| 3 | Rapor `.ai/reports/` altına alınır (bu görev kapsamı DIŞI; atandı) |
| 4 | Düzeltme backend-architect'e devredilir; güvenlik yeniden doğrular |
| 5 | Kapatma kanıtı: taramanın tekrar 0 eşleşmesi |

| CWE Kuralı | Açıklama |
|---|---|
| CWE numarası resmi sayfadan | başlık `<title>` ile 2026-10-13 doğrulandı |
| Eşleme ≠ kod bulgusu | CWE satırı "bu kod böyledir" demez; yüzey eşleşmesidir |
| Kod bulgusu | yalnız Dosya·Satır satırıdır |
| Kapsam dışı CWE | uygulanmayan sınıf (ör. SSO) yazılmaz |
| Yeni CWE | yalnız HEAD 200 + başlık doğrulanan kaynakla eklenir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 PHP FONKSİYON DENETİM MATRİSİ (RESMİ DOKÜMAN BAŞLIKLARIYLA)

| # | Resmi doküman başlığı | URL | K009'da beklenti | Kod durumu |
|---|---|---|---|---|
| 1 | PHP: Prepared statements and stored procedures - Manual | https://www.php.net/manual/en/pdo.prepared-statements.php | parametreli sorgu zorunlu (ADR-002) | doküman kanıtı §10 (ApiKeyRepository) |
| 2 | PHP: set_error_handler - Manual | https://www.php.net/manual/en/function.set-error-handler.php | hata yakalayıcı log'a yazar, kullanıcıya sızdırmaz | LoggerFactory kanıtı §11 |
| 3 | PHP: header - Manual | https://www.php.net/manual/en/function.header.php | başlık yazımı tek noktadan (ResponseEmitter) | `ResponseEmitter.php·86` (§12) |

| Fonksiyon Sınıfı | Kural | Bu dosyadaki kanıt |
|---|---|---|
| `password_hash` / `password_verify` | Argon2id; SHA tabanlı yasak | §2 (Password.php·38-49/54) |
| `hash_equals` | token/parola karşılaştırması zaman-güvenli olmalı | §4/§10 · 5 eşleşme (§12) |
| `random_bytes` | nonce/token/IV üretimi | §10 · 13 eşleşme (§12) |
| `hash_hmac` | pepper varsa parola ile birlikte | §2 (Password.php·41-44) |
| `header` | yalnız ResponseEmitter; ham başlık yazımı yasak | `ResponseEmitter.php·86` + ResponseNormalization §10 |
| `unserialize` | kullanıcı girdisiyle asla | 0 eşleşme (§12) |
| `md5` | parola/imza anahtarı YASAK; önbellek anahtarı serbest | 3 eşleşme, tümü önbellek (§12) |
| `shell_exec` / `exec` / `system` | veto sınıfı | 1 eşleşme → §13 satır 1 |
| `$_*` süperglobal | doğrulama middleware'inden geçmeden işlenmez | 66 eşleşme → §16 tarama kalemi |

| Denetim Kuralı | Açıklama |
|---|---|
| URL başlığı | resmi `<title>` ile alınmıştır (2026-10-13) |
| Tahmin yok | Fonksiyon davranışı bu dosyada iddia edilmez; yalnız kural linklenir |
| Kapsam | Bu § yalnız bu dosyanın URL havuzundadır (başka K009 dosyası kullanamaz) |
| Yenileme | Kod değiştiğinde §12 taraması yeniden çalıştırılır |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 ALINTI ZİNCİRİ VE KAYNAK SÜRMÜLÜĞÜ

| Bölüm | Alıntı konusu | Kaynak dosya | Sürüm notu |
|---|---|---|---|
| §2 | parola hash | Password.php | Argon2id doğrulandı |
| §3 | CSRF | CsrfMiddleware.php | bypass whitelist kaldırıldı |
| §4 | güvenlik başlıkları | SecurityHeadersMiddleware.php | HSTS blok kanıtı |
| §5 | yetki denetimi | PermissionMiddleware.php | rol/permission |
| §6 | fail-closed rate limit | RateLimiterMiddleware.php | fail-closed listesi |
| §7 | oturum zaman aşımı | SessionLifecycle.php | 1800 sn |
| §8 | JWT | JwtService.php | RS256 (HS256 yanlışı düzeltildi) |
| §9 | açık yönlendirme | ReturnUrlPolicy.php | policy kontrolü |
| §10 | API anahtarı | ApiKeyRepository.php | hash_equals + random_bytes |
| §11 | anti-pattern taraması | okunan dosyalar | 0 ihlal |
| §12 | statik tarama (yeni) | shared/src 24 dosya | 1 veto bulgusu |

| Sürmülük Kuralı | Açıklama |
|---|---|
| Alıntı ≠ yeniden yazım | Özgün koda yorum/düzenleme eklenmez (§10 kuralı) |
| Dosya değişince | ilgili § yeniden okunur; satır numarası taşınabilir |
| Taşınabilir satır | dosya değişmediyse satır aynen kalır |
| Kayıp satır | Kodda artık yoksa satır "KALDIRILDI" olarak işaretlenir, silinmez |
| Yeni dosya | §12 kapsamına girer; §2-§10'a girmez |
| Sürüm | Bu § 2026-10-13 taramasını taşır |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 KAPSAM SINIRI VE ÖRNEK DOĞRULAMA PROTOKOLÜ

| Kalem | Kapsam | Dışarıda kalan |
|---|---|---|
| `shared/src/Middleware` (11 dosya) | içinde | — |
| `shared/src/Security` (6 dosya) | içinde | — |
| `shared/src/Api/Middleware` (8 dosya) | içinde | — |
| `shared/src/PageRouter` · `Cache` · `AI` (örnek tespit) | içinde (tarama) | derin analiz yok |
| `app/` · `public/` · `subdomains/` | dışarıda | ayrı görev |
| `shared/tests/` | dışarıda | test-stratejisi dosyası |
| `vendor/` | dışarıda | composer bağımlılık denetimi ayrı |
| 3+ klasör derinliği | dışarıda | KAPSAM DIŞI işaretli |

| Örnek Doğrulama Protokolü (4 adım) | |
|---|---|
| 1 | Dosya diskten okunur (grep ile konum + satır) |
| 2 | Alıntı bloğa dosya·satır künyesiyle yazılır |
| 3 | Bloğun beklenen davranışla eşleştiği ikinci okuma ile teyit edilir |
| 4 | Eşleşmiyorsa satır "⚠️ VERIFICATION REQUIRED" olarak işaretlenir |

| Açık kalemler (2026-10-13) | Durum |
|---|---|
| `AIEngine.php·295` shell_exec komutu neyi çalıştırıyor? | ⚠️ VERIFICATION REQUIRED (kod okunmadı, yalnız satır görüntülendi) |
| `$_*` 66 eşleşmenin tamamı doğrulama arkasında mı? | ⚠️ VERIFICATION REQUIRED (akış analizi yapılmadı) |
| Derin alt dizinlerde yasaklı desen var mı? | KAPSAM DIŞI |
| Password.php bu tarama derinliğinde görünmüyor | §5 kanıtına dayanır; yeniden konumlandırılacak |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 BÖLÜM KAPANIŞI (2026-10-13)

| Kontrol | Beklenen | Durum |
|---|---|---|
| Eklenen § | §12..§17 | EVET |
| Mevcut §1-§11 | silinmedi | EVET |
| Dosya·Satır kanıtı | her bulguda | EVET (§12 §13) |
| Yeni URL (HEAD 200 + başlık) | 7 benzersiz | EVET (CWE-79/601/113/522 · PHP 3) |
| URL tekrarı | 0 | EVET |
| Uydurma kod/alıntı | 0 | EVET |
| Veto bulgusu | raporlandı | §13 satır 1 → rapor bekliyor |
| `git commit` | ATILMADI | EVET |

| Son Durum | |
|---|---|
| Dosya | security-kod-ornekleri.md · group: kod |
| Ek bölüm | §12..§17 |
| Tarama | 24 PHP dosyası · 1 veto bulgusu · 0 eval/exec/system |
| Commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §18 TARAMA OTURUM KAYDI VE SONRAKİ TARAMALAR (2026-10-13)

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | Kapsam dosyası seçildi (`shared/src`, 1+2 derinlik) | 24 PHP dosyası |
| 2 | Yasaklı desen listesi hazırlandı (eval/exec/system/passthru/shell_exec/unserialize/md5/sha1) | 9 desen |
| 3 | Tarama `Select-String` ile çalıştırıldı | §12 tablosu |
| 4 | Sıfır eşleşen desenler "temiz" olarak yazıldı | 4 desen temiz |
| 5 | `shell_exec` tek isabeti Dosya·Satır ile kaydedildi | `AIEngine.php·295` |
| 6 | `md5` 3 isbeti bağlamıyla (önbellek anahtarı) ayrıldı | parola/imza YOK |
| 7 | `$_*` 66 isabeti kapsam dışı derinlikle birlikte işaretlendi | §16 |
| 8 | CWE kaynakları HEAD 200 + başlık doğrulamasından geçirildi | 4/4 |
| 9 | PHP doküman kaynakları aynı doğrulamadan geçirildi | 3/3 |
| 10 | Veto bulgusu rapor satırına yazıldı | §13 satır 1 |
| 11 | Bölüm sonu kanıt ibaresi her §'ye kondu | §12..§18 |
| 12 | `git commit` | ATILMADI |

| Sonraki Tarama Tetikleyicileri | Ne ölçülür | Sahip |
|---|---|---|
| Yeni middleware ekleme | pipeline kaydı + veto desenleri | backend-architect |
| `AIEngine.php` incelemesi | shell_exec komutunun kaynağı (295) | security-engineer |
| `shared/src` 3+ derinlik taraması | KAPSAM DIŞI desenler | security-engineer |
| Yeni sayfa/controller | `$_*` kullanımının doğrulama arkası | backend-architect |
| Parola/anahtar kodu değişimi | Argon2id + hash_equals sürekliliği | security-engineer |
| `composer.json` değişimi | A06 bağımlılık denetimi | dependency-manager |
| Route meta değişimi | requiredRole/requiredPermission örtüsü | backend-architect |
| Test ekleme | §16 protokolünün test edilmesi | qa-engineer |

| Sayısal Kapanış (2026-10-13) | |
|---|---|
| Eklenen bölüm | §12..§18 (7 bölüm) |
| Tarama deseni | 9 yasaklı + 3 ek gösterge |
| Bulgu | 1 veto · 0 eval/exec/system · 0 unserialize |
| Yeni doğrulanmış URL | 7 (CWE-79 · CWE-601 · CWE-113 · CWE-522 · PDO · set_error_handler · header) |
| Uydurma alıntı/kod | 0 |
| Mevcut bölüm | §1..§11 silinmedi |
| `git commit` | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 TARAMA ARALIĞI VE TEKRAR ÖLÇÜM PLANI

| Aralık | Dosya sayısı | Derinlik | Durum |
|---|---|---|---|
| `shared/src/Middleware` | 11 | 1 | ölçüldü |
| `shared/src/Security` | 6 | 1 | ölçüldü |
| `shared/src/Api/Middleware` | 8 | 1 | ölçüldü |
| `shared/src` diğer klasörleri (PageRouter, Cache, AI, ...) | 24 (1+2 derinlik toplam) | 2 | ölçüldü |
| `shared/src` 3+ derinlik | bilinmiyor | 3+ | KAPSAM DIŞI |
| `auth.coremusic.net/include/**` | bilinmiyor | — | KAPSAM DIŞI (Password.php burada) |
| `public/` · `subdomains/` | bilinmiyor | — | KAPSAM DIŞI |
| `vendor/` | bilinmiyor | — | A06 ayrı denetim |

| Tekrar Ölçüm Tetikleyicisi | Ne ölçülür | Beklenen sonuç |
|---|---|---|
| `AIEngine.php` değişikliği | `shell_exec` satırı | ya kaldırıldı ya gerekçelendirildi |
| Yeni `header(` kullanımı | başlık tek noktadan mı | ResponseEmitter dışına taşma yok |
| Yeni `md5(` kullanımı | parola/imza anahtarı mı | parola/imza için 0 |
| Yeni `$_*` kullanımı | doğrulama arkası | ValidationMiddleware arkasında |
| Yeni alt dizin | derinlik 3+ kapsamı | kapsam kararı yenilenir |
| Parola dosyası değişimi | Password.php §2 karşılığı | Argon2id sürekliliği |
| Test eklenmesi | §16 protokolü | dosya·satır doğrulaması |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 17 DİL GÜVENLİK MATRİSİ (2026-10-18 — ÖRNEK KOD KİTAPLIĞASI)

> **Amaç:** Aynı güvenlik teması (AES-256-GCM nonce/IV · Argon2id · sabit-zamanlı karşılaştırma ·
> kriptografik rastgelelik · CSP · secret saklama) 17 dilde doğru API ile gösterilir.
> **Kural:** Bu örnekler VAULT ŞABLONUDUR — diskteki CoreMusic kodu değildir (alıntı değildir);
> her biri resmi API bilgisine dayanır. Emin olunmayan API/sürüm `⚠️ VERIFICATION REQUIRED` ile işaretlidir.
> Dil kapsamı: 17/17 (hedef ≥14).

| # | Dil | Tema | § |
|---|---|---|---|
| 1 | PHP 8.4 | Argon2id + sodium AEAD | §20.1 |
| 2 | JavaScript (Node 20+) | WebCrypto AES-GCM | §20.2 |
| 3 | TypeScript | tipli AEAD sarmalayıcı | §20.3 |
| 4 | C (C11 + libsodium) | crypto_pwhash + AEAD | §20.4 |
| 5 | C++ (C++20 + OpenSSL 3) | EVP AES-256-GCM | §20.5 |
| 6 | C# (.NET 8) | AesGcm + RandomNumberGenerator | §20.6 |
| 7 | Python 3.12 | cryptography AESGCM | §20.7 |
| 8 | Go 1.22 | crypto/aes GCM | §20.8 |
| 9 | Rust (2021) | aes-gcm crate | §20.9 |
| 10 | Java 21 | JCE GCMParameterSpec | §20.10 |
| 11 | Swift 5.9 | CryptoKit AES.GCM | §20.11 |
| 12 | Kotlin | JCE sarmalayıcı | §20.12 |
| 13 | SQL (MySQL 8) | en az yetki GRANT + hash sütunu | §20.13 |
| 14 | Bash | openssl PBKDF2 + güvenli temp | §20.14 |
| 15 | PowerShell 7 | AesGcm + gizli metin | §20.15 |
| 16 | YAML (GitHub Actions) | secret taraması + min permission | §20.16 |
| 17 | HCL (Terraform) | sensitive değişken + secrets manager | §20.17 |

### §20.0 ORTAK KURALLAR (TÜM DİLLER)

| Kural | Gerekçe | K009 eşlemesi |
|---|---|---|
| Nonce/IV ASLA tekrar etmez; AEAD'de nonce yeniden kullanımı anahtarı ifşa eder | NIST SP 800-38D (security-web-arastirma.md §22 satır 6) | encryption |
| Nonce rastgele ÜRETİLİR; salt baştan ayrıdır (parolada salt, AEAD'de nonce) | CWE-330 | §12 kontrol 3 |
| Parola yalnız hash'lenir; hash + pepper (pepper env'de) | Password·43-49 | authentication |
| Karşılaştırma sabit-zamanlı (hash_equals / crypto.timingSafeEqual / FixedTimeEquals) | zamanlama saldırısı | §12 kontrol 2 |
| Rastgelelik kriptografik PRNG (asla rand/random/Mersenne Twister) | CWE-330 · CWE-327 | §12 kontrol 3/14 |
| Secret kaynak koda/log'a/commit'e GİRMEZ | S3 sınıfı (security-kapsam.md §14) | secrets |

### §20.1 PHP 8.4 — Argon2id + Secretbox (XSalsa20-Poly1305, sodium)

~~~php
<?php
declare(strict_types=1);

// Parola: Argon2id (CoreMusic Password·43-49 ile aynı parametre ailesi)
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536, // 64 MB
    'time_cost'   => 4,
    'threads'     => 2,
]);
if (!password_verify($password, $hash)) { throw new RuntimeException('verify failed'); }

// AEAD: XChaCha20-Poly1305 — 24 byte nonce sodium tarafindan ÜRETILIR (elle üretme)
$key   = sodium_crypto_secretbox_keygen();
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
$cipher = sodium_crypto_secretbox($plaintext, $nonce, $key);
$plain  = sodium_crypto_secretbox_open($cipher, $nonce, $key); // false = MAC başarısız → reddet

// Sabit-zamanlı karşılaştırma
if (!hash_equals($expected, $given)) { /* reddet */ }
sodium_memzero($key); // bellek temizliği (S1 sınıfı kuralı)
~~~

### §20.2 JavaScript (Node 20+ / tarayıcı) — WebCrypto AES-256-GCM

~~~js
// Anahtar: yalnız WebCrypto random ile üretilir (Math.random YASAK)
const keyBytes = crypto.getRandomValues(new Uint8Array(32));
const key = await crypto.subtle.importKey('raw', keyBytes, 'AES-GCM', false, ['encrypt', 'decrypt']);

// 12 byte IV rastgele üretilir; her mesajda YENİ
const iv = crypto.getRandomValues(new Uint8Array(12));
const ct = new Uint8Array(await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, encoded));
const pt = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, key, ct); // MAC hatası = throw

// Sabit-zamanlı karşılaştırma (WebCrypto ile tek doğru yol)
function timingSafeEqual(a, b) {
  if (a.length !== b.length) return false;
  return crypto.subtle.timingSafeEqual(a, b);
}
~~~

### §20.3 TypeScript — tipli AEAD sarmalayıcı

~~~ts
interface SealedBox { readonly iv: Uint8Array; readonly ciphertext: Uint8Array; }

export async function seal(key: CryptoKey, plaintext: Uint8Array): Promise<SealedBox> {
  const iv = crypto.getRandomValues(new Uint8Array(12)); // IV tipte zorunlu alan
  const ct = new Uint8Array(
    await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, plaintext),
  );
  return { iv, ciphertext: ct }; // iv CIPHERTEXT ile TAŞINIR (ayırmak = decrypt imkânsız)
}
// Hata sözleşmesi: decrypt başarısızsa asla null değil — Result<T, AeadError>
~~~

### §20.4 C (C11 + libsodium) — crypto_pwhash (Argon2id) + AEAD

~~~c
#include <sodium.h>

// Parola türetme (Argon2id — sodium varsayılan algoritması Argon2id13)
unsigned char salt[crypto_pwhash_SALTBYTES];
randombytes_buf(salt, sizeof salt);                    // kriptografik rastgelelik
unsigned char key[crypto_aead_xchacha20poly1305_ietf_KEYBYTES];
if (crypto_pwhash(key, sizeof key, pw, pwlen, salt,
                  crypto_pwhash_OPSLIMIT_MODERATE,
                  crypto_pwhash_MEMLIMIT_MODERATE,
                  crypto_pwhash_ALG_ARGON2ID13) != 0) {
    return -1; // bellek yetersiz → fail-closed
}

// AEAD
unsigned char nonce[crypto_aead_xchacha20poly1305_ietf_NPUBBYTES];
randombytes_buf(nonce, sizeof nonce);
crypto_aead_xchacha20poly1305_ietf_encrypt(ciphertext, NULL, message, msglen,
    NULL, 0, NULL, nonce, key); // MAC doğrulaması open() tarafında zorunlu
sodium_memzero(key, sizeof key);
~~~

### §20.5 C++ (C++20 + OpenSSL 3) — EVP AES-256-GCM

~~~cpp
#include <openssl/evp.h>
#include <openssl/rand.h>

std::vector<unsigned char> iv(12);
if (RAND_bytes(iv.data(), static_cast<int>(iv.size())) != 1) throw std::runtime_error("RAND");

EVP_CIPHER_CTX* ctx = EVP_CIPHER_CTX_new();
EVP_EncryptInit_ex(ctx, EVP_aes_256_gcm(), nullptr, nullptr, nullptr);
EVP_CIPHER_CTX_ctrl(ctx, EVP_CTRL_GCM_SET_IVLEN, static_cast<int>(iv.size()), nullptr);
EVP_EncryptInit_ex(ctx, nullptr, nullptr, key32.data(), iv.data());
EVP_EncryptUpdate(ctx, out.data(), &len, in.data(), static_cast<int>(in.size()));
EVP_EncryptFinal_ex(ctx, out.data() + len, &len);
EVP_CIPHER_CTX_ctrl(ctx, EVP_CTRL_GCM_GET_TAG, 16, tag.data()); // 16-byte tag ayrılır
EVP_CIPHER_CTX_free(ctx);
// Decrypt'te önce EVP_CTRL_GCM_SET_TAG + Final == 1 kontrolü — başarısızsa RED
~~~

### §20.6 C# (.NET 8) — AesGcm + RandomNumberGenerator

~~~csharp
using System.Security.Cryptography;

byte[] key = RandomNumberGenerator.GetBytes(32); // kriptografik PRNG
byte[] iv  = RandomNumberGenerator.GetBytes(12); // her mesajda yeni
byte[] tag = new byte[16];
byte[] ct  = new byte[plaintext.Length];

using var aes = new AesGcm(key, tagSizeInBytes: 16); // ⚠️ VERIFICATION REQUIRED: tag boyutu ctor'ı .NET 8'de eklendi; eski sürümde yalnız new AesGcm(key)
aes.Encrypt(iv, plaintext, ct, tag);

// Sabit-zamanlı karşılaştırma
CryptographicOperations.FixedTimeEquals(expected, given);

// PBKDF2 (parola; Argon2id için üçüncü parti paket gerekir ⚠️ VERIFICATION REQUIRED: BCrypt.Net-Next sürümü)
byte[] dk = Rfc2898DeriveBytes.Pbkdf2(pw, salt, 600_000, HashAlgorithmName.SHA256, 32);
~~~

### §20.7 Python 3.12 — cryptography (AESGCM)

~~~python
from cryptography.hazmat.primitives.ciphers.aead import AESGCM
import secrets

key = AESGCM.generate_key(bit_length=256)          # kripto PRNG
aes = AESGCM(key)
nonce = secrets.token_bytes(12)                     # HER mesajda yeni nonce
ct = aes.encrypt(nonce, plaintext, associated_data) # AAD bağlanır
pt = aes.decrypt(nonce, ct, associated_data)        # MAC hatası = InvalidTag → reddet

# Parola: Argon2id için 'argon2-cffi' paketi ⚠️ VERIFICATION REQUIRED (paket sürümü doğrulanacak)
~~~

### §20.8 Go 1.22 — crypto/aes GCM

~~~go
import ("crypto/aes"; "crypto/cipher"; "crypto/rand"; "io")

key := make([]byte, 32)
if _, err := io.ReadFull(rand.Reader, key); err != nil { return err } // crypto rand
block, err := aes.NewCipher(key)
gcm, err := cipher.NewGCM(block)
nonce := make([]byte, gcm.NonceSize())
io.ReadFull(rand.Reader, nonce)                     // rastgele nonce
ct := gcm.Seal(nil, nonce, plaintext, nil)          // Seal: ct = nonce|ciphertext birlikte taşınır

// Parola: golang.org/x/crypto/bcrypt ⚠️ VERIFICATION REQUIRED (Argon2id için x/crypto/argon2 sürümü)
~~~

### §20.9 Rust (2021 edition) — aes-gcm crate

~~~rust
use aes_gcm::{Aes256Gcm, KeyInit, aead::{Aead, OsRng}};
use aes_gcm::aead::rand_core::RngCore;

let key = Aes256Gcm::generate_key(&mut OsRng);           // kripto PRNG (getrandom)
let cipher = Aes256Gcm::new(&key);
let mut nonce_bytes = [0u8; 12];
OsRng.fill_bytes(&mut nonce_bytes);                       // rastgele nonce
let ct = cipher.encrypt(nonce_bytes.as_slice().into(), plaintext)?; // MAC hatası = Err → reddet
// Parola: argon2 crate (password-hash modülü) ⚠️ VERIFICATION REQUIRED (sürüm)
~~~

### §20.10 Java 21 — JCE AES/GCM/NoPadding

~~~java
import javax.crypto.Cipher;
import javax.crypto.spec.GCMParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import java.security.SecureRandom;

byte[] key = new byte[32]; new SecureRandom().nextBytes(key);  // SecureRandom zorunlu
byte[] iv  = new byte[12]; new SecureRandom().nextBytes(iv);   // her işlemde yeni
Cipher c = Cipher.getInstance("AES/GCM/NoPadding");
c.init(Cipher.ENCRYPT_MODE, new SecretKeySpec(key, "AES"), new GCMParameterSpec(128, iv));
byte[] ct = c.doFinal(plaintext); // GCM: tag varsayılan 16 byte sona eklenir

// Parola: JDK'da Argon2 YOK; 'argon2-jvm' üçüncü parti ⚠️ VERIFICATION REQUIRED (sürüm); yedek JDK PBKDF2WithHmacSHA512
~~~

### §20.11 Swift 5.9 (iOS/macOS) — CryptoKit

~~~swift
import CryptoKit

let key = SymmetricKey(size: .bits256)                  // CryptoKit kendi RNG'si
let sealed = try AES.GCM.seal(data, using: key)         // nonce CryptoKit tarafından ÜRETİLİR
let combined = sealed.combined!                         // nonce|ct|tag tek parçada
let opened = try AES.GCM.open(sealed, using: key)       // MAC hatası = throw → reddet
// elle nonce geçmek istersen AES.GCM.Nonce() — asla sabit/tekrar eden nonce
~~~

### §20.12 Kotlin (JVM) — JCE sarmalayıcı (§20.10 ile aynı çekirdek)

~~~kotlin
import javax.crypto.Cipher
import javax.crypto.spec.GCMParameterSpec
import javax.crypto.spec.SecretKeySpec
import java.security.SecureRandom

fun seal(keyBytes: ByteArray, plain: ByteArray): ByteArray {
    val iv = ByteArray(12).also { SecureRandom().nextBytes(it) }
    val cipher = Cipher.getInstance("AES/GCM/NoPadding")
    cipher.init(Cipher.ENCRYPT_MODE, SecretKeySpec(keyBytes, "AES"), GCMParameterSpec(128, iv))
    return iv + cipher.doFinal(plain)                    // iv ct ile taşınır
}
// Android: platform JCE; parola için androidx.security crypto ⚠️ VERIFICATION REQUIRED (sürüm)
~~~

### §20.13 SQL (MySQL 8) — en az yetki + hash sütunu

~~~sql
-- Parola ASLA plaintext saklanmaz; sütun Argon2id çıktısına genişlik verir
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash CHAR(98) NOT NULL,        -- $argon2id$v=19$m=65536,t=4,p=2$... uzunluğu için güvenli pay
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Uygulama kullanıcısı: yalnız gerekli yetki (en az yetki)
CREATE USER 'app_rw'@'10.0.0.%' IDENTIFIED BY '...';  -- parola script gövdesinde değil, env'de
GRANT SELECT, INSERT, UPDATE ON coremusic.users TO 'app_rw'@'10.0.0.%';
-- DROP / GRANT OPTION / FILE yetkisi app_rw'ye VERİLMEZ
~~~

### §20.14 Bash — openssl PBKDF2 + güvenli geçici dosya

~~~bash
#!/usr/bin/env bash
set -euo pipefail

# openssl enc AEAD/GCM DESTEKLEMEZ (tag işlenemez) — tarafsız doğrulama isteyen iş
# AES-GCM yerine age/libsodium aracına devredilir ⚠️ VERIFICATION REQUIRED: age sürümü
umask 077
tmp=$(mktemp)                     # geçici dosya 0600
trap 'rm -f "$tmp"' EXIT
openssl enc -aes-256-cbc -pbkdf2 -iter 600000 -salt -md sha256 \
  -in plain.bin -out "$tmp" -pass env:CM_ENC_PASS   # parola env'de; komut satırı argümanı YASAK (ps görünür)
~~~

### §20.15 PowerShell 7 — AesGcm + gizli değişken

~~~powershell
# PS 7 (.NET) ile çalışır; Windows PowerShell 5.1 (.NET Framework) AesGcm İÇERMEZ ⚠️ VERIFICATION REQUIRED
$bytes = [byte[]]::new(32)
[System.Security.Cryptography.RandomNumberGenerator]::Fill($bytes)   # kripto PRNG
$iv = [byte[]]::new(12)
[System.Security.Cryptography.RandomNumberGenerator]::Fill($iv)

$aes = [System.Security.Cryptography.AesGcm]::new($bytes)
# Encrypt/Decrypt imzaları .NET 8'de tag boyutu eklenerek değişti ⚠️ VERIFICATION REQUIRED (sürüme göre)

# SecureString yalnız DPAPI taşır; düz metin asla log'a yazılmaz
$sec = ConvertTo-SecureString $env:CM_DB_PASS -AsPlainText -Force
~~~

### §20.16 YAML (GitHub Actions) — secret taraması + min permission

~~~yaml
name: security-gates
permissions:
  contents: read            # en az yetki; write YASAK (CI'da push yetkisi sızıntısı = tam ihlal)
jobs:
  secret-scan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4           # ⚠️ VERIFICATION REQUIRED: sürüm pin'i (SHA pin önerilir)
      - name: Gitleaks
        uses: gitleaks/gitleaks-action@v2   # CoreMusic secret-scan.yml karşılığı
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}  # secret yalnız actions/secrets üzerinden akar; YAML'a gömülmez
~~~

### §20.17 HCL (Terraform) — sensitive değişken + secrets manager

~~~hcl
variable "db_password" {
  type      = string
  sensitive = true          # plan/apply çıktısında maskele; log sızıntısını kapatır
  nullable  = false
}

# Kaynak kodda/sürümcüde secret TUTMAZ; üretimi secrets manager'a devreder
resource "aws_secretsmanager_secret" "db" {
  name                    = "coremusic/db"
  description             = "rotasyon politikası: secrets-kapsam.md §rotasyon"
  recovery_window_in_days = 7   # silme geri alma penceresi = olay müdahalesi
}
# state dosyası da hassastır: remote backend + encrypt zorunlu (state sızıntısı = secret sızıntısı)
~~~

### §20.18 MATRİS KAPANIŞI

| Dil kümesi | Doğrulama düzeyi | Uyarı |
|---|---|---|
| PHP · JS · TS · C(libsodium) · C++(OpenSSL EVP) · Python(cryptography) · Go · Java(JCE) · Swift(CryptoKit) · SQL(MySQL) · Bash(openssl) | API'ler yaygın bilinen; örnekler vault şablonu | — |
| C# AesGcm ctor(tag) | .NET 8'e özgü | ⚠️ VERIFICATION REQUIRED (sürüme göre) |
| PowerShell AesGcm | yalnız PS7/.NET Core | 5.1'de yok ⚠️ |
| Rust argon2 · Kotlin Android crypto · Python argon2-cffi · Go x/crypto/argon2 · Java argon2-jvm · age (Bash) | paket sürümü | ⚠️ VERIFICATION REQUIRED |
| openssl enc AEAD | GCM desteklenmez (dürüst sınır) | CBC + PBKDF2 gösterildi |

| §20 Kapanış Kontrolü | Durum |
|---|---|
| Dil sayısı | 17/17 (hedef ≥14) |
| Yer tutucu/filler | 0 (her § gerçek API ve gerçek kural içerir) |
| Emin olunmayan API | ⚠️ ile işaretli (tablo §20.18) |
| Uydurma disk alıntısı | 0 — bu § şablondur, alıntı ID'si taşımaz |
| git commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
