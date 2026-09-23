---
title: "CoreMusic — Auth Bypass Security Audit"
type: security-audit
category: auth-bypass
date: 2026-09-23
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Auth Bypass Security Audit

**Tarama Tarihi:** 2026-09-23
**Kapsam:** BypassAuthMiddleware, SecurityHelper, auth.coremusic.net/index.php, home.coremusic.net constants/bootstrap, .env dosyalari
**Metod:** Statik kod analizi + config dogrulama + akis izleme

---

## OZET

| Metrik | Deger |
|--------|-------|
| Toplam bulgu | 10 |
| CRITICAL | 3 |
| HIGH | 2 |
| MEDIUM | 3 |
| LOW | 2 |
| Taranan dosya | 8 |
| Etkilenen servis | 3 (shared, auth, home) |

---

## CRITICAL Bulgular

### C1: auth.coremusic.net .env'de FORCE_AUTH_BYPASS=true (Production'da Aktif)

- **Dosya:** `auth.coremusic.net/config/.env` Satir 22
- **Tur:** Security (Misconfiguration)
- **Etki:** auth.coremusic.net root'a her istek gonderdiginde bypass redirect zinciri baslatir. Production'da bypass aktif.
- **Kanit:**
  ```
  APP_ENV_MODE=production   (Satir 1)
  FORCE_AUTH_BYPASS=true    (Satir 22)
  ```
- **Akis:**
  1. Kullanici `http://auth.coremusic.net/` gider
  2. `index.php` Satir 96: `if (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS)` -> true
  3. `hash_hmac('sha256', 'bypass_2026-09-23', APP_PEPPER)` uretilir
  4. `http://home.coremusic.net:81/auth/callback?auth_key=<bypassKey>`'e redirect
  5. home.coremusic.net auth_key'i auth.coremusic.net/validate-key'de dogrulamaya calisir
  6. validateSessionKey() DB'de bu key'i bulamaz -> BASARISIZ -> /login?error=invalid_key
- **Sonuc:** Bypass redirect her zaman basarisiz olur ama bypass mekanizmasi production'da aktif ve her istekte gereksiz DB sorgusu + redirect döngüsü olusturur.
- **Onerilen cozum:** `FORCE_AUTH_BYPASS=false` olarak degistir veya production'da bu .env degerini tamamen kaldir.
- **Oncelik:** CRITICAL

---

### C2: Bypass auth_key DB'de Kayitli Degil -> Bypass Her Zaman Basarisiz

- **Dosya:** `auth.coremusic.net/index.php` Satir 97 + `auth.coremusic.net/include/Repository/UserRepository.php` Satir 236-255
- **Tur:** Logic Error
- **Etki:** Bypass redirect uretilen auth_key'i DB'de kayitli degil. validateSessionKey() yalnizca `user_tokens` tablosunda `token_type='api_key'` olanlari arar. Bypass key'i asla bu tabloya yazilmaz.
- **Kanit:**
  ```php
  // auth.coremusic.net/index.php:97 - uretim
  $bypassKey = hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');

  // UserRepository.php:238 - dogrulama
  $tokenHash = hash('sha256', $authKey);
  $sql = "SELECT ... FROM user_tokens ut ... WHERE ut.token_hash = :token_hash AND ut.token_type = 'api_key' ...";
  ```
- **Sorun:** Uretilen bypass key DB'de hicbir `user_tokens` kaydiyla eslesmez. Bypass redirect her zaman `invalid_key` hatasiyla sonuclanir.
- **Onerilen cozum:** Bypass icin ozel bir dogrulama yolu olustur. validateSessionKey() icerisinde bypass key'leri icin ayri bir kontrol ekle veya bypass redirect'i dogrudan home.coremusic.net'de BypassAuthMiddleware uzerinden yap.
- **Oncelik:** CRITICAL

---

### C3: .env Dosyalarinda Plaintext Veritabani Sifreleri

- **Dosya:** `auth.coremusic.net/config/.env` Satir 9 + `home.coremusic.net/config/.env` Satir 20
- **Tur:** Security (Credential Exposure)
- **Etki:** Her iki .env dosyasinda da DB sifresi duz metin olarak mevcut. .gitignore'da olsa bile, production sunucusunda dosya erisilebilir olabilir.
- **Kanit:**
  ```
  auth.coremusic.net/config/.env Satir 9:  DB_PASSWORD=ali**
  home.coremusic.net/config/.env Satir 20: DB_PASSWORD=ali**
  ```
- **Onerilen cozum:** .env dosyalari versiyon kontrol disinda tutulmali (.gitignore). Production'da environment variable veya credential vault kullanilmali.
- **Oncelik:** CRITICAL

---

## HIGH Bulgular

### H1: /bypass-status Endpoint'i Authenticated Erisime Acik

- **Dosya:** `auth.coremusic.net/index.php` Satir 65, 79-83
- **Tur:** Security (Information Disclosure)
- **Etki:** Herkes `/bypass-status` endpoint'ine GET istegi atarak FORCE_AUTH_BYPASS ve TEST_MODE degerlerini ogrenebilir. Auth veya session gerekmez.
- **Kanit:**
  ```php
  // auth.coremusic.net/index.php:65
  if ($requestUri === '/health' || $requestUri === '/session' || $requestUri === '/validate-key' || $requestUri === '/bypass-status') {
      // ... auth yok
  }
  // Satir 79-83:
  '/bypass-status' => [
      'httpStatus' => 200,
      'force_auth_bypass' => defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS,
      'test_mode' => defined('TEST_MODE') && TEST_MODE,
  ],
  ```
- **Onerilen cozum:** `/bypass-status` icin authentication zorunlu kil veya sadece localhost/127.0.0.1'den erisilebilir yap.
- **Oncelik:** HIGH

---

### H2: home.coremusic.net SSRF (Server-Side Request Forgery) Riski

- **Dosya:** `home.coremusic.net/config/constants.php` Satir 41-53
- **Tur:** Security (SSRF)
- **Etki:** home.coremusic.net, auth.coremusic.net'e HTTP istegi atarak FORCE_AUTH_BYPASS degerini ogreniyor. Allowlist `localhost` ve `127.0.0.1` de iceriyor. DNS poisoning veya local service exploitation ile bypass degeri degistirilebilir.
- **Kanit:**
  ```php
  // home.coremusic.net/config/constants.php:41-53
  $authUrl = defined('AUTH_URL') ? AUTH_URL : 'http://auth.coremusic.net';
  $authHost = parse_url($authUrl, PHP_URL_HOST);
  $allowedAuthHosts = ['auth.coremusic.net', 'localhost', '127.0.0.1'];
  if (in_array($authScheme, ['http', 'https'], true) && in_array($authHost, $allowedAuthHosts, true)) {
      $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
      $authConfigResponse = @file_get_contents($authUrl . '/bypass-status', false, $ctx);
  ```
- **Onerilen cozum:**
  1. `localhost` ve `127.0.0.1` allowlist'ten kaldir
  2. DNS rebinding korumasi ekle (host header dogrulama)
  3. Yerine: local .env'den FORCE_AUTH_BYPASS oku
- **Oncelik:** HIGH

---

## MEDIUM Bulgular

### M1: BypassAuthMiddleware loadBypassConfig() Eksik Config Key'leri

- **Dosya:** `shared/src/Middleware/BypassAuthMiddleware.php` Satir 25-27
- **Tur:** Logic (Config Mismatch)
- **Etki:** Middleware `auth.bypass_uuid`, `auth.bypass_role`, `auth.bypass_username` config key'lerini ariyor. Ancak ne `home.coremusic.net/config/app.php` ne de `auth.coremusic.net/config/app.php` bu key'leri iceriyor. Yalnizca `$_ENV` fallback'i calisir.
- **Kanit:**
  ```php
  // BypassAuthMiddleware.php:25-27
  $uuid     = $this->config->get('auth.bypass_uuid', $_ENV['BYPASS_USER_UUID'] ?? '');
  $role     = $this->config->get('auth.bypass_role', $_ENV['BYPASS_ROLE'] ?? '');
  $username = $this->config->get('auth.bypass_username', $_ENV['BYPASS_USERNAME'] ?? '');
  ```
  ```
  // home.coremusic.net/config/app.php - auth.bypass_uuid YOK
  // auth.coremusic.net/config/app.php  - auth.bypass_uuid YOK
  ```
- **Sonuc:** `$this->config->get('auth.bypass_uuid', ...)` her zaman default degeri dondurur. Calisma zamani `$this->config->get()` fallback'i `$_ENV['BYPASS_USER_UUID']` uzerinden calisir. Bu bir config yanilsamasi yaratir.
- **Onerilen cozum:** ConfigManager'a `auth.bypass_uuid`, `auth.bypass_role`, `auth.bypass_username` key'lerini ekle veya direkt `$_ENV` kullanmaktan vaz gec (daha temiz).
- **Oncelik:** MEDIUM

---

### M2: home.coremusic.net .env'de FORCE_AUTH_BYPASS Tanimli Degil

- **Dosya:** `home.coremusic.net/config/.env`
- **Tur:** Logic (Missing Config)
- **Etki:** home.coremusic.net .env dosyasinda FORCE_AUTH_BYPASS tanimli degil. Bypass degeri yalnizca auth.coremusic.net'den HTTP ile okunarak belirleniyor. Eger auth.coremusic.net erisilemezse, bypass degeri `false` olarak kalir (fail-closed dogru).
- **Kanit:**
  ```
  // home.coremusic.net/config/.env - FORCE_AUTH_BYPASS YOK
  // Tum bypass degeri constants.php:47-53'teki HTTP isteginden gelir
  ```
- **Onerilen cozum:** home.coremusic.net/.env'ye `FORCE_AUTH_BYPASS=false` ekle. HTTP'ye bagimliligi azalt.
- **Oncelik:** MEDIUM

---

### M3: auth.coremusic.net Hardcoded Fallback Pepper

- **Dosya:** `auth.coremusic.net/index.php` Satir 97
- **Tur:** Security (Hardcoded Secret)
- **Etki:** Bypass key uretiminde `APP_PEPPER` tanimli degilse `'coremusic-bypass'` fallback olarak kullanilir. Bu durum constants.php'de oneleyen bir `exit` varsa da, teorik olarak fallback pepper bilinir bir degerdir.
- **Kanit:**
  ```php
  // auth.coremusic.net/index.php:97
  $bypassKey = hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');
  ```
- **Onerilen cozum:** Fallback'i kaldir. APP_PEPPER tanimli degilse bypass redirect'i calistirma.
- **Oncelik:** MEDIUM

---

## LOW Bulgular

### L1: MAX_REQUEST_BODY_SIZE Tanimlanmis Ama Kullanilmiyor

- **Dosya:** `auth.coremusic.net/index.php` Satir 25
- **Tur:** Dead Code
- **Etki:** `const MAX_REQUEST_BODY_SIZE = 8192;` tanimlanmis ancak dosyada hicbir yerde kullanilmiyor.
- **Onerilen cozum:** Kullanilmiyorsa kaldir veya ilgili middleware'de kullan.
- **Oncelik:** LOW

---

### L2: SecurityHelper::logTestBypass() Hata Basini Bastiriyor

- **Dosya:** `shared/src/Security/SecurityHelper.php` Satir 52
- **Tur:** Code Quality
- **Etki:** `@file_put_contents()` ifadesi `@` operatoru ile hatalari bastirir. Dosya yazma basarisiz olursa bypass log kaybolur.
- **Kanit:**
  ```php
  // SecurityHelper.php:52
  @file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
  ```
- **Onerilen cozum:** `@` kaldir, try-catch veya error_log ile hata yonet.
- **Oncelik:** LOW

---

## AKIS DIYAGRAMI: Auth Bypass Tam Zincir

```
[1] Kullanici -> http://auth.coremusic.net/
     |
[2] auth/index.php:96 -- FORCE_AUTH_BYPASS=true mi? --> EVET (C1)
     |
[3] auth/index.php:97 -- bypassKey = hash_hmac('sha256', 'bypass_YYYY-MM-DD', APP_PEPPER)
     |
[4] auth/index.php:98-101 -- 302 Redirect -> home:81/auth/callback?auth_key=<bypassKey>
     |
[5] home/bootstrap.php:28 -- /auth/callback yakalandi
     |
[6] home/bootstrap.php:47 -- HomeAuthBridge::validateAndCreateSession(bypassKey)
     |
[7] HomeAuthBridge.php:128 -- POST auth:80/validate-key {auth_key: bypassKey}
     |
[8] auth/index.php:74 -- AuthController::handleValidateKey()
     |
[9] AuthService.php:281 -- validateSessionKey(bypassKey)
     |
[10] UserRepository.php:238 -- hash('sha256', bypassKey) -> token_hash arama
      |
[11] UserRepository.php:242 -- SELECT ... WHERE token_hash=:hash AND token_type='api_key'
      |
[12] DB'de eslesme YOK -> null dondur (C2)
      |
[13] AuthService.php:292 -- throw AuthenticationException
      |
[14] auth/index.php:87-89 -- 401 JSON response
      |
[15] HomeAuthBridge.php:160-162 -- curl error veya 401 -> null dondur
      |
[16] home/bootstrap.php:62-65 -- Basarisiz -> /login?error=invalid_key
```

**Sonuc:** Bypass redirect her zaman basarisiz olur. Kullanici login sayfasina yonlendirilir.

---

## SECURITYHELPER ISACTIVE ANALIZI

```
SecurityHelper::isTestBypassActive($config):
  1. $config->get('app.env') === 'production'? --> auth.coremusic.net: EVET (production)
     -> return false (bypass devre disi)
  2. auth.coremusic.net'de bypass middleware bypass status'den bagimsiz calisir
     -> ama /bypass-status herkes acik (H1)

home.coremusic.net'de:
  1. $config->get('app.env') === 'development'
     -> production kontrolu gecilir
  2. $config->get('app.test_mode', false) -> false (.env'de false)
  3. $config->get('app.force_auth_bypass', false) -> FORCE_AUTH_BYPASS degeri
     -> Bu deger auth.coremusic.net HTTP'inden okunur (M2, H2)
```

---

## ONCELIK SIRASI

| Sira | Bulgu | Oncelik | Etki |
|------|-------|---------|------|
| 1 | C1: auth .env FORCE_AUTH_BYPASS=true | CRITICAL | Production'da bypass aktif |
| 2 | C2: Bypass key DB'de kayitli degil | CRITICAL | Bypass her zaman basarisiz |
| 3 | C3: .env'de plaintext DB sifreleri | CRITICAL | Veri sizintisi riski |
| 4 | H1: /bypass-status auth'siz acik | HIGH | Bilgi sizintisi |
| 5 | H2: home SSRF riski | HIGH | Config manipulasyonu |
| 6 | M1: Eksik config key'leri | MEDIUM | Config yanilsamasi |
| 7 | M2: home .env'de FORCE_AUTH_BYPASS yok | MEDIUM | HTTP bagimliligi |
| 8 | M3: Hardcoded fallback pepper | MEDIUM | Bilinen secret |
| 9 | L1: Olum kod (MAX_REQUEST_BODY_SIZE) | LOW | Kod kalabaligi |
| 10 | L2: Log hata basimi | LOW | Log kaybi |

---

## ONERILEN AKSIYONLAR

### Hemen Yapilacak (Bug Fix)
1. `auth.coremusic.net/config/.env`: `FORCE_AUTH_BYPASS=false` yap
2. `auth.coremusic.net/index.php` Satir 97: Fallback pepper'i kaldir, APP_PEPPER yoksa redirect yapma
3. `/bypass-status` icin localhost harici erisimi engelle

### Bu Sprint Yapilacak
4. Bypass akisini yeniden tasarla: auth.coremusic.net bypass redirect'i kaldir, bypass'i sadece BypassAuthMiddleware uzerinden yap
5. home.coremusic.net/.env'ye `FORCE_AUTH_BYPASS=false` ekle
6. BypassAuthMiddleware config key'lerini app.php'ye ekle

### Sonraki Sprint
7. SSRF korumasini guclendir (DNS rebinding, host validation)
8. Credential management'i credential vault'a tasi
9. logTestBypass() hata yonetimini iyilestir
10. MAX_REQUEST_BODY_SIZE'i kullan veya kaldir

---

**Audit by:** CoreMusic Security Scan
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
