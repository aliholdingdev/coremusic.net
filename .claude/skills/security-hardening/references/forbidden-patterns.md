# Yasaklı Desenler (Forbidden Patterns) — Veto Listesi

> Skill: `security-hardening` v4.0.0 · CoreMusic · Güncel: 2026-10-21
>
> Bu dosya: `.ai/CLAUDE.md` §6 pipeline yasağı + §7 guardrails + §21 forbidden
> patterns'ın skill-genişletilmiş halidir. Veto yetkisi: **security-engineer**.
> Veto → STAP + rapor; düzeltme backend-architect'in işidir, doğrulama
> security'nin (rule 16).

---

## 1. Mutlak Yasak (Veto — anında STOP)

### 1.1 Kod çalıştırma (code execution)

```php
// YASAK — A03:2025 / A01:2025 — kullanıcı verisi asla koda dönüşmez
eval($input);                          // ADR-002 §1, ADR-021
exec($input, $out);                    // ADR-002 §1
shell_exec($cmd);                      // ADR-002 §1
system($cmd);                          // ADR-002 §1 — shell_exec'in kardeşi
passthru($cmd);                        // ADR-002 §1
popen($cmd, 'r');                      // ADR-002 §1
proc_open($cmd, ...);                  // ADR-002 §1
preg_replace('/.../e', ...);           // PHP 7+'te kaldırıldı; aramada geçerse hata
assert($stringExpr);                   // PHP 7+ assert artık string çalıştırmaz ama desen yasak
```

```js
// YASAK — A03:2025 — dinamik kod çalıştırma JS'te de yasak
new Function(userInput)();             // ADR-002 §1 — "eval benzeri"
setTimeout(userInput, 0);              // string argüman = eval
eval(userInput);                       // ADR-002 §1
```

**Doğrusu:** desen-tabanlı yönlendirme (allowlist map), saf fonksiyon çağrısı:

```php
// DOĞRU — koda değil, seçiciliğe bakılır
private const HANDLERS = [
    'codec' => [MediaController::class, 'transcode'],
    'thumb' => [MediaController::class, 'thumbnail'],
];
$action = $_POST['action'] ?? '';
if (!isset(self::HANDLERS[$action])) {
    http_response_code(400);
    exit('Unknown action');
}
[$class, $method] = self::HANDLERS[$action];
$class::$method($id);                  // sadece allowlist'ten gelen isim
```

### 1.2 Şifreli/süreli tokenlarda hash

```php
// YASAK — ADR-020 §1 — token hash'lenemez (DB'de tek kullanım için geri
// dönülemez olmalı; hash'lenirse doğrulama imkânsız)
$tokenHash = hash('sha256', $token);   // YASAK
$sent = substr(hash('sha256', $raw), 0, 6);   // YASAK — doğrulama geri dönülemez

// DOĞRU — ADR-020 §2: email doğrulama tokenı SALT-OKUNUR saklanır
$token = bin2hex(random_bytes(32));    // 64 hex karakter
$this->db->prepare(
    'INSERT INTO user_verification_tokens (user_id, token, expires_at)
     VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
)->execute([$userId, $token]);
// Mailde: token doğrudan gider (ADT-009 S9)
// Süre: 1 saat — S9'da "1 saat" olarak üretilir (ADR-020 §4 zamanaşımı: mailde S9)

// DOĞRU — psikolojik direnç tokenı (süreli/sayaçlı) hash'lenir: ADR-020 §2
$token = bin2hex(random_bytes(32));
$hash  = hash('sha256', $token);       // burada hash OK — doğrulama hash üzerinden
```

**Kural neticesi:** `email verify` = salt-okunur, `rate-limit/psych resistance` =
hash'li. Hangisi olduğunu ADR-020 §2 belirler; karar kodda yorum satırıyla
belgelenir.

### 1.3 Hardcoded secret

```php
// YASAK — ADR-007 §1, ADR-019 §1, kural 6
private const JWT_SECRET = '<ANAHTAR>';           // asla — gerçek değer .env/vault'ta
$dsn = 'mysql:host=...;password=Abc123...';          // asla (ADT-016: DSN'de password)

// DOĞRU — ADR-007 §2: env substitution + gitignored .env
$key = getenv('APP_ENCRYPTION_KEY');                  // .env (gitignore:69/.gitignore)
if ($key === false || $key === '') {
    throw new RuntimeException('APP_ENCRYPTION_KEY missing');   // fail-closed
}
```

Kod içinde `secret`/`key`/`token`/`password` literal dizesi geçiyorsa **veto**;
`.env` / credential vault (ADT-006) dışına değer yazılmaz. `.md` / `.json` /
`.log`'a secret yazımı yasak (global kural §5 Security).

### 1.4 Fail-open (güvenlik kapatma yönlü hata)

```php
// YASAK — açık-güvenlik hatası; kural: fail-safe / fail-closed
try {
    $this->csrf->validate($request);
} catch (Throwable $e) {
    // YASAK — sessiz geç: CSRF'i by-pass etmiş olursun
}
// YASAK — RateLimiter APCu yoksa herkesi geçir (rule 5 ihlali)
if (!function_exists('apcu_inc')) { return; }
// YASAK — Origin yoksa origin check'i atla (kural 15 ihlali)
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
if ($origin === null) { return; }      // Origin header'ı ZORUNLU olmayan istekte
                                       // atlanabilir — bkz. §3.4 aşağıda

// DOĞRU — hatada güvenliği daralt, geçişi kes
try {
    $this->csrf->validate($request);
} catch (CsrfException $e) {
    $this->log->warning('csrf_rejected', ['reason' => $e->getMessage()]);
    $response = new Response('CSRF validation failed', 419);
    throw new MiddlewareHalt($response);      // pipeline'ı durdur
}
```

**İstisna (doğru fail-open ölçütü):** `Origin` header'ı tarayıcı tarafından
gönderilmesi zorunlu OLMAYAN isteklerde (eski tarayıcı, non-browser API
istemcisi) yokluğu **403'e çekmek legit istekleri kırar**. Zaman çizelgesi:
`Origin var ve farklıysa reddet (net saldırgan)` → `Origin yoksa: CORS +
CSRF token + Fetch Metadata ile ikincil doğrula` → yalnız Origin'siz + tokensiz
+ metadata'siz POST'ta reddet. Yani "yok" reddi, **diğer iki kanal da
kapalıysa** devreye girer — bu fail-*closed*, fail-open değil. Karar noktası
`OriginCheck::handle()` — ADR-035 uygulamasında Fetch Metadata kullanımı
**⚠️ VERIFICATION REQUIRED** (vault'ta kanıt yok).

### 1.5 `password_hash` deseni ihlali (ADR-022)

```php
// YASAK
md5($password); sha1($password);                    // ADR-022 §1 — kırılabilir
password_hash($password, PASSWORD_DEFAULT);         // ADR-022 §1 — Argon2id değil
                                                     // (PHP 8.4'te default = bcrypt)

// DOĞRU — ADR-022 §2
$hash = password_hash(
    $password,
    PASSWORD_ARGON2ID,
    ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2]   // 64MB/t=4/p=2
);
password_verify($password, $hash);
```

### 1.6 AES modu (ADR-022)

```php
// YASAK — ADR-022 §1
openssl_encrypt($data, 'aes-256-cbc', $key, ...);   // padding + mod = A02 risk
openssl_encrypt($data, 'aes-256-ecb', $key, ...);   // ECB — örüntü sızıntısı
                                                     // (AES-ECB yasağı: ADT-006)

// DOĞRU — ADR-022 §2: AES-256-GCM (96-bit IV, 16-byte tag)
$iv   = random_bytes(12);                            // 96-bit = 12 byte
$tag  = '';                                          // 16-byte auth tag
$cipher = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
$blob = $iv . $tag . $cipher;                        // kayıt formatı
// Alternatif: XChaCha20-Poly1305 (ADT-006 — tag 128-bit)
```

### 1.7 Regex DoS (ReDoS — A01:2025)

```php
// YASAK — geriye bakışlı tekrar + alternatif = üstel yutma
preg_match('/^(a+)+$/', $input);
preg_match('/(\w+\s?)*$/', $input);

// DOĞRU — doğrulamada doğrudan filtre; regex'e gereksinim yoksa kullanma
filter_var($email, FILTER_VALIDATE_EMAIL);
```

Regex zorunluysa: `preg_match` sonuç `false` değil, `=== 0/1`; girdi boyutu
üst sınırı (ör. 255) enjeksiyondan ÖNCE uygulanır.

---

## 2. Yönlü Yasak (context — kural ihlali = düzeltme)

### 2.1 CSRF token adı: `csrf_token`, `_csrf_token` DEĞİL (ADR-010)

```php
// YASAK — ADR-010 §1.1 — 2026-10-14 düzeltmesi (Eski: _csrf_token → csrf_token)
// YASAK — read-only doğrulamadan önce regenerate (ADT-012: -1 output)
$this->session->regenerate();        // GET okuma ÖNCESİ — YASAK

// DOĞRU — ADR-010 §2: input read-only olabilir, output'ta auto-approve yok
private function readToken(): ?string {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    return is_string($token) ? $token : null;
}
private function writeToken(): void {
    if (!isset($_SESSION['csrf_token'])) {                 // idempotent
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // 256-bit
    }
}
// ADR-010 §2 doğrulama akışı:
// 1. Missing → 400 (kural 15)
// 2. Hash-equals başarısız → 403 + bootstrap_engineering.security.<event> log
// 3. Süre ≤15dk → sil + 403
// 4. Hash-equals BAŞARILI → output val. — regenerate YASAK
```

Aynı isim hem POST body'de (`csrf_token`) hem header'da (`X-CSRF-Token`)
olabilir; ikisi de **aynı session token'ı** ile `hash_equals()` karşılaştırılır
(ADR-010 §2, S44: 2026-10-14 çift-kanal doğrulama).

### 2.2 Session token'lar (ADR-011)

```php
// YASAK — ADR-011 §1: sabit session token (farklı kullanıcılar aynı token)
// YASAK — ADR-011 §1: bearer token'tan session türetme (session fixation)
$_SESSION['token'] = 'fixed-value';
$sess = $request->bearerToken();       // oturum kaynağı JWT OLMAYACAK

// DOĞRU — ADR-011 §2: lazy-start + per-user rand + rol/jti + timeout val
private function resolveSession(string $jwt): array {
    $claims = $this->auth->validateJwtToken($jwt);   // ⚠️ VERIFICATION REQUIRED
    if ($claims === null) {                          // ADR-010 §1.1 stub referansı
        return ['authenticated' => false, 'redirect' => '/login'];
    }
    if (!isset($_SESSION['__boot'])) {
        session_regenerate_id(true);                 // yalnız oturum İLK kurulumda
        $_SESSION['__boot'] = bin2hex(random_bytes(32));   // per-user random
    }
    // Timeout val (ADR-011 §2): idle + absolute — kesin değerler ⚠️ VERIFICATION
    // REQUIRED (ADR-011, .ai/CLAUDE.md §6 3600s çelişkisi)
    return ['authenticated' => true, 'role' => $claims['role'] ?? 'user'];
}
```

### 2.3 Rate limit (ADR-013 — kural 5)

```php
// YASAK — ADR-013 §1: rate limit'i frontend'e bırakma (JS ile sayaç = by-pass)
// YASAK — ADR-013 §1: token bucket (fail-open davranışı riski)
// YASAK — global → signed URL → endpoint sırasını ters çevirme (ADT-014)
// YASAK — düzleyici olarak Redis (read-after-write race: ADT-014)

// DOĞRU — ADR-013 §2: APCu fixed window, 60 req/60s
$key = $this->identity();                        // signed URL → user_id → client_id → ip
$count = apcu_inc($key, 1, $success);            // atomik
if (!$success || $count > 60) {                  // fail-closed: sayaç yoksa reddet
    $response = new Response('Too Many Requests', 429);
    $response->headers->set('Retry-After', '60');
    throw new MiddlewareHalt($response);
}
```

Mekanik nüanslar (§3.3 ayrıntı): fixed window kayması (boundary 60+60=120 istek),
`apcu_inc` atomic ama pencere başlangıcı `apcu_add`'e bağlı (race — eksik reset
denetlenmeli), interval ≥ pencere boyu (5/sn × 60 = 300/60s).

### 2.4 CORS (ADR-009)

```php
// YASAK — wildcard + credentialed birlikte (ADR-009 §1): * + Access-Control-Allow-Credentials: true
header('Access-Control-Allow-Origin: *');                       // ADR-009 §1
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']); // yansıma = whitelist DEĞİL

// DOĞRU — ADR-009 §2: exact-match whitelist + OPTIONS preflight
private const ALLOWED = ['https://coremusic.net', 'https://auth.coremusic.net'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, self::ALLOWED, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);          // yansıtılan = whitelist'ten
    header('Vary: Origin');                                     // cache ayrıştırma
    header('Access-Control-Allow-Credentials: true');           // origin value ile
}
```

`*, null, data:, file:` — hiçbir whitelist değeri değildir (ADR-009 §1).

### 2.5 BypassAuth (ADR-055 — kural 13)

```php
// YASAK — kural 13: BypassAuth production'da HER ZAMAN devre dışı
if ($_SERVER['APP_ENV'] !== 'production') { ... }    // yeterli DEĞİL — amaç
                                                     // görünmezlik, doğrulama değil

// DOĞRU — ADR-055 §2: baştan engelle + istisna isteği = 403 + dashboard girişi
if (in_array($this->path, self::BYPASS_ROUTES, true) && $this->isProduction()) {
    $this->log->warning('bypass_attempt_production', ['path' => $this->path]);
    throw new MiddlewareHalt(new Response('Forbidden', 403));    // sil/403 + dashboard log
}
```

Test: production'da `bypass=true` + prod origin → **403 + dashboard'a log satırı**
(ADR-055 §2 doğrulama).

### 2.6 Session fixation & yeniden-yaratım (kural 7)

```php
// YASAK — kural 7: giriş ÖNCESİ regenerate (saldırgan sabitlediği session'ı
// kullanıcının kimliğiyle doldurur)
session_regenerate_id();             // login formu işlenmeden önce — YASAK

// DOĞRU — kural 7: SADECE doğrulama BAŞARILI olduktan sonra
if (password_verify($password, $user->password_hash) && $mfaOk) {
    session_regenerate_id(true);     // + eski session verisi taşınmaz
    $_SESSION['user_id'] = $user->id;
}
```

### 2.7 MFA — TOTP yalnız (ADR-059)

```php
// YASAK — ADR-059 §1: SMS/e-mail ile TOTP taşıma (interception)
// YASAK — ADR-059 §1: TOTP doğrulamasını frontend'e taşıma (rate limit by-pass)

// DOĞRU — ADR-059 §2: pragmarx/google2fa + hash_equals
$ga = new \PragmaRX\Google2FA\Google2FA();
$valid = $ga->verifyKey($user->totp_secret_encrypted, $code, $window = 1);
if (!hash_equals('1', $valid ? '1' : '0')) {         // timing-safe (kural 11)
    return $this->mfaFailure($userId);
}
```

`hash_equals()` kullanımı (kural 11): token/OTP/CSRF/secret karşılaştırmalarının
**tamamı**. `===` yerine `hash_equals` (aynı uzunlukta secret'lar için).

### 2.8 XSS (A03:2025) — innerHTML / inline script

```js
// YASAK — ADR-001 §1, kural 15 (inline JS veto)
el.innerHTML = userContent;                    // HTML enjeksiyonu
document.write(html);                          // ADR-001 §1
eval(rendered);                                // ADR-001 §1
<script>...</script>                           // inline — ADR-012 §1 (nonce yok)

// DOĞRU — ADR-001 §2 + ADR-012 §2: textContent + nonce'lu script
el.textContent = userContent;                  // varsayılan
// HTML gerekiyorsa: sanitization (DOMPurify benzeri — bağımlılık seçimi
// ⚠️ VERIFICATION REQUIRED, vault'ta onay yok) → sonra innerHTML
// Inline script: <script nonce="<?= $cspNonce ?>"> — ADR-012 §2
```

---

## 3. Mimari yerleşim yasağı (pipeline — `.ai/CLAUDE.md` §6)

### 3.1 Pipeline dışı authentication/yetki/rate-limit/CORS

Kural: **sadece pipeline üzerinden**. Dosyada adı geçen her yerin
`shared/src/Middleware/MiddlewarePipeline.php` içindeki 10 sıradan birine
karşılık gelmesi gerekir.

### 3.2 Controller katmanında auth/permission/rate-limit (validation ÖNCESİ kural ihlali)

```
YASAK:  Controller::handle() içinde ilk satırda $this->auth->check()  → kural 26
DOĞRU:  Pipeline: ... → BypassAuth → Auth → Permission → Validation → Controller
```

### 3.3 CSP nonce zinciri kural ihlali (ADR-012)

```
YASAK:  controller'da $nonce = bin2hex(random_bytes(32))   → her katman farklı nonce
DOĞRU:  SecurityHeaders (4/10) üretir → Session (6) $_SESSION['csp_nonce']
        → CSRF (7) hidden input'a basar → Controller View'a $nonce → response'a
        2 kez yazılmaz (ADR-012 §2)
```

### 3.4 API Gateway pipeline dışı routing (ADR-035)

```
YASAK:  apicoremusic.net router'ında auth/rate-limit/CORS kendi başına
DOĞRU:  API Gateway → shared/MiddlewarePipeline (ADT-025 doğrulanmış — ⚠️
        VERIFICATION REQUIRED: gateway kodu bu oturumda okunmadı)
```

### 3.5 Middleware değişimi yerine silme (subagent ADR-056)

Subagent göreve başlarken **pipeline'dan silme yok** — middleware'i silmek,
onun koruduğu tehdidi kaldırmaz. Silme = yeni ADR (≥088) + security veto review.

---

## 4. Veto akışı (rule 15 + rule 16)

1. **Bul:** `path:line` kanıtı ile §1/§2/§3 desenini tespit et (araçla:
   `grep` — `eval(`, `shell_exec(`, `PASSWORD_DEFAULT`, `Access-Control-Allow-Origin: *`,
   `_csrf_token`, `password_hash($p, PASSWORD_DEFAULT)`, `md5(`).
2. **STOP:** kodu **DEĞİŞTİRME** (rule 16 — audit read-only).
3. **Raporla:** `.ai/reports/security/[tarih]-security-audit.md` — §bulgu,
   severity, fix owner (`backend-architect`).
4. **Uygula:** backend-architect düzeltir.
5. **Doğrula:** security-engineer yeniden denetler (`php -l` + hedef test).

**Veto = düzeltme değil, durdurma.** Yetki genişleten hiçbir düzenleme
(permission/MCP/allow-list) bu skill'de üretilemez (N- authority).

---

## 5. Sahte-hassasiyet (critical değil)

Veto listesinde olup **critical değil** — raporla, öncelik sırasına diz:

- Regex'e ihtiyaç yokken regex kullanımı (filtre yeterli)
- `switch`'te `default` olmayışı (kural — default zorunlu, `.ai/CLAUDE.md` LINT)
- inline stilde sabit hex/px (ITCSS token kuralı)
- `SELECT *` (kural 8 — veritabanı katmanı, güvenlik dışı ama ihlal)
- N+1 query (güvenlik değil, performans; ama mass-assignment yeri için
  explicit column listesi = güvenlik getirisi)

Kapak: kalan ihlaller `references/owasp-mapping.md` §7 OWASP karşılıkları +
`references/changelog.md`.
