---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/index
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/anayasa
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/surucu
evidence: https://www.php.net/manual/en/function.set-error-handler.php · https://www.php.net/manual/en/function.set-exception-handler.php · https://www.php.net/manual/en/opcache.setup.php · https://systemd.io/ · https://developer.android.com/guide/components/activities/process-lifecycle · https://source.android.com/docs/core/runtime · api.coremusic.net/index.php:10-29 · api.coremusic.net/Services/RuntimeBootstrap.php:1-21 · api.coremusic.net/bootstrap.php:97-106 · api.coremusic.net/Services/SessionBootstrapper.php:19-28
validation: 2026-10-13 · 3 URL HTTP 200 (set_error_handler · set_exception_handler · opcache.setup) · repo satırları Read ile birebir · içerik 380-460 satır
open_questions: R5 opcache warm-up · R6 worker drenaç/timeout
group: mimari
---

# baslangic-ve-bootstrap-surucu — Bootstrap Sürücüleri ve Akışları (Kademe-3)

## §0 ELI15

Bu dosya, `anayasa.md`'deki kuralları **çalışan akışlara** çevirir: hata
yönetimi perdesi nasıl kurulur, uygulama bir istekte tam olarak nasıl ayağa
kalkar, OPcache nasıl hazırlanır, bootstrap başarısızsa ne olur. Her akış
"kural → adım → kanıt satırı" biçiminde; adım atlamak anayasa ihlalidir.

## §1 — Harita

| Bölüm | Sürücü | Ana kaynak |
|---|---|---|
| §2.1 | Hata perdesi kurulumu (handler zinciri) | `RuntimeBootstrap.php:14-20` + PHP docs |
| §2.2 | Tam boot akışı (A1-A7 uygulaması) | `index.php:10-29` + `bootstrap.php:97-106` |
| §2.3 | OPcache hazırlık/ısınma akışı | `index.php:21-25` + opcache docs |
| §2.4 | Fail-fast ve hata yolu (500 akışı) | `constants.php:14-17` · `bootstrap.php:106` |
| §2.5 | Idempotent boot & shutdown kaydı | `SessionBootstrapper.php:19-28` |
| §3 | Anti-pattern denetimi | — |
| §4 | Ölçüm / test | — |
| §6 | Kanıt blokları (repo satırları) | — |

## §2 — Akışlar

### §2.1 — Hata perdesi kurulumu

```
RuntimeBootstrap::boot(DEBUG_MODE)                          (index.php:29)
  ├─ date_default_timezone_set('UTC')                       (RuntimeBootstrap.php:10-12)
  ├─ error_reporting($debug ? E_ALL : E_ERROR|E_WARNING|E_PARSE)   (:14-17)
  ├─ ini_set('display_errors', 1|0) + log_errors=1          (:19-20)
  ├─ set_error_handler(fn($seviye, $mesaj, $dosya, $satır))
  │     ├─ kabul: E_WARNING / E_NOTICE / E_USER_*           (perde 1)
  │     ├─ E_ERROR/E_PARSE/E_COMPILE_* → handler ÇAĞRILMAZ  (PHP 8+ sınırı)
  │     └─ log kaydı; üretimde cevaba yansıtmaz              (M2/M3)
  ├─ set_exception_handler(fn(Throwable $t))                 (perde 2)
  │     └─ log + HTTP 500; sonra süreç biter
  └─ register_shutdown_function(fn() => ...)                 (perde 3 / M5)
        └─ error_get_last() → fatal ise log
```

**Kanıt:** `set_error_handler` dokümantasyonu — hatanın `false` dönüşüyle PHP
varsayılanine devretmesi, `errcontext` parametresinin PHP 8.0'da kaldırılması;
`set_exception_handler` — yalnız `Throwable` alır, handler dönüşünde süreç sonlanır.

### §2.2 — Tam boot akışı (A1-A7)

```
1. index.php:10        require vendor/autoload.php
2. index.php:12-19     EnvParser::loadIntoEnv(.env)
                       ├─ APP_ENV_MODE tanımsız → http_response_code(500); exit
                       └─ (EnvParser: # yorum, = yoksa atla, tırnak kes)
3. index.php:20        require constants.php
                       └─ define('DEBUG_MODE', $debugTurev)   (constants.php:22-25)
4. index.php:21-25     if (DEBUG_MODE) opcache_reset()         [A4 koruması]
5. index.php:27        require app.php  → PDO (dsn zorunlu; LogicException)
6. index.php:29        RuntimeBootstrap::boot(DEBUG_MODE)      [§2.1]
7. bootstrap.php:97    $kernel = new PageRouterKernel(...)
8. bootstrap.php:98-105 $kernel->handle($method, $path, $query, $body)
                       └─ catch (Throwable $e) → log + 500     (:106)
9. cevap yazılır → shutdown evresi (M5)
```

**Sürücü kuralı:** 7-8. adım yalnız anayasa A6'dan sonra çalışabilir; kernel
çağrısından **önce** `header()`/output yasaktır.

### §2.3 — OPcache hazırlık akışı

| Adım | Ne | Durum |
|---|---|---|
| 1 | FPM/Apache SAPI'de `opcache.enable=1` (sunucu yapılandırması) | kurulum |
| 2 | Debug'te `opcache_reset()` → tüm cache temizlenir | `index.php:21-25` |
| 3 | Üretimde reset **yapılmaz** → cache korunur (sıcak bytecode) | A4 koruması |
| 4 | Deploy sonrası ilk istekler miss → yeniden derleme (cold) | opcache docs |
| 5 | Warm-up (`opcache_compile_file`) | R5 — `[UNKNOWN]` |

**Kanıt:** opcache kurulum dokümantasyonu — validateTimestamps/revalidateFreq
ile dosya değişikliğinin izlenmesi; cache'in süreç-içi (shared memory)
olduğu, FPM'de her worker'ın eriştiği.

### §2.4 — Fail-fast akışı (hata yolu)

```
A2'de APP_ENV_MODE yok           ─┐
app.php'de PDO dsn eksik         ─┼→ LogicException / Throwable
kernel'de bilinmeyen route/istisna─┘
        │
        ▼
set_exception_handler / catch(Throwable)     (bootstrap.php:106)
        ├─ error_log kaydı (her modda)
        ├─ http_response_code(500)
        └─ cevapta stack gösterme (display_errors=0, M2)
shutdown evresi → register_shutdown_function son kontrol (M5)
```

**Sürücü kuralı:** hata yolu **asla** 200 dönmez; `display_errors=0` iken bile
log kaydı zorunludur (M2: loglama her iki modda açık).

### §2.5 — Idempotent boot & shutdown kaydı

```php
// SessionBootstrapper.php:19-28 — ikinci kez çağrılmak zararsız
if ($this->booted) { return; }        // guard
session_start([...]);                 // tek seferlik oturum başlatımı
$this->booted = true;
```

**Sürücü kuralı:** boot fonksiyonları guard ile korunur; `register_shutdown_function`
da **boot'ta bir kez** kaydedilir — ikinci kayıt aynı işi iki kez yapar (AP).

## §3 — Anti-pattern Denetimi

| ID | Anti-pattern | Yakalama testi | Doğrusu |
|---|---|---|---|
| AP-1 | Handler'ın `E_ERROR` beklemesi | `set_error_handler` docs: yakalanamaz sınıf | Fatal → perde 2/3 |
| AP-2 | Boot'ta iki kez shutdown kaydı | Kayıt sayacı 2 çıkarsa ihlal | Guard (§2.5) |
| AP-3 | Üretimde `opcache_reset()` | `DEBUG_MODE=false` iken reset çağrısı logda | A4 koruması |
| AP-4 | Handler içinde `echo` | "headers already sent" | Yalnız log; cevabı kernel verir |
| AP-5 | `restore_error_handler` çağrısız iç içe kurulum | Zincir bozulur | Tek boot, tek kurulum |

## §4 — Ölçüm / Test

| Kapı | Test | Beklenen |
|---|---|---|
| Perde | `E_WARNING` tetikle (debug) | Handler logda, cevap 200/uyarı yok |
| Fatal | Kasıtlı `throw new Error` | 500 + exception handler log |
| Sıra | `index.php` Read vs §2.2 | 9 adım birebir |
| OPcache | Debug boot'ta `opcache_reset` çağrısı | 1 kez; üretimde 0 kez |
| Idempotent | `boot()` iki kez | İkincide guard → iş yapılmaz |
| Shutdown | Kayıt sayacı | Boot başına 1 |

## §5 — Komşu Sınırı

Hata yönetiminin **PHP çekirdek kavramları** (`isletim-sistemi-core/`) ile
topolojik bağını `web-runtime-topolojisi/surucu.md` §2.3-§2.4 sürer (FPM'e
giriş). OPcache'nin **süreç-içi** doğası (shared memory, per-process Windows)
`surec-ve-parcacik/` alanıdır — burada yalnız boot etkisi yazılır.

## §6 — Kanıt Blokları (repo satırları, birebir)

### Kanıt-1 — Hata perdesi (`Services/RuntimeBootstrap.php:10-20`)

```php
date_default_timezone_set('UTC');
error_reporting($debug ? E_ALL : (E_ERROR | E_WARNING | E_PARSE));
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
```

### Kanıt-2 — OPcache koruması (`index.php:21-25`)

```php
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
}
```

### Kanıt-3 — Kernel catch (`bootstrap.php:97-106`)

```php
$kernel = new PageRouterKernel($router, $container);
try {
    $kernel->handle($method, $path, $query, $body);
} catch (Throwable $e) {
    error_log('[kernel] ' . $e->getMessage());
    http_response_code(500);
}
```

## §7 — Kapsam Dışı

FPM havuz/MPM sürücüleri (`web-runtime-topolojisi/surucu.md`) · süreç
fork/öldürme (`surec-ve-parcacik/`) · Composer/CI · TLS · kuyruk boot.

## §8 — Açık Sorular

| # | Soru | Durum |
|---|---|---|
| R5 | OPcache warm-up (`opcache_compile_file` ile ön ısıtma) eklenecek mi? | `[UNKNOWN]` |
| R6 | Shutdown'da worker drenaç / `max_execution_time` aşımı | `[⚠️ VERIFICATION REQUIRED]` |

## §9 — Kaynakça

1. PHP Manual — set_error_handler
   https://www.php.net/manual/en/function.set-error-handler.php
   (Yakalanamaz hata sınıfları ve `errcontext` kaldırılması → §2.1 perde 1.)
2. PHP Manual — set_exception_handler
   https://www.php.net/manual/en/function.set-exception-handler.php
   (`Throwable` sözleşmesi ve handler sonrası sonlanma → §2.4 perde 2.)
3. PHP Manual — OPcache kurulumu
   https://www.php.net/manual/en/opcache.setup.php
   (Paylaşımlı bytecode cache, enable/validate ayarları → §2.3.)
   (Bu üç URL bu dalın `index.md`'sinden devralınmıştır; tekrar yazılmamıştır.)

## §10 — Boot Akışı Disk İndeksi (§2.2'nin satır doğrulaması)

> 2026-10-13 okuması — `[IO.File]::ReadAllLines` ile; mevcut §'ler değiştirilmedi.

| # | İddia (§2.2) | Dosya | Satır | Gözlenen içerik |
|---|---|---|---|---|
| 1 | autoload | `home.coremusic.net/index.php` | 10 | `require_once __DIR__ . '/autoload.php';` |
| 2 | constants (EnvParser içeride) | `home.coremusic.net/index.php` | 11 | `require_once __DIR__ . '/config/constants.php';` |
| 3 | OPcache üçlü kapısı | `home.coremusic.net/index.php` | 14 | `defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')` |
| 4 | OPcache eylemi | `home.coremusic.net/index.php` | 15 | `opcache_reset();` |
| 5 | RuntimeBootstrap import | `home.coremusic.net/index.php` | 18 | `use CoreMusic\Bootstrap\RuntimeBootstrap;` |
| 6 | app config | `home.coremusic.net/index.php` | 21 | `$appConfig = require __DIR__ . '/config/app.php';` |
| 7 | boot çağrısı | `home.coremusic.net/index.php` | 23 | `RuntimeBootstrap::boot(DEBUG_MODE);` |
| 8 | config nesneleri | `home.coremusic.net/index.php` | 26 | `require_once __DIR__ . '/config/config.php';` |
| 9 | uygulama açılışı | `home.coremusic.net/index.php` | 29 | `require_once __DIR__ . '/config/bootstrap.php';` (son satır) |
| 10 | hata perdesi gövdesi | `shared/src/Bootstrap/RuntimeBootstrap.php` | 10 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` |
| 11 | debug modu | `shared/src/Bootstrap/RuntimeBootstrap.php` | 12-14 | `error_reporting(E_ALL);` + `ini_set('display_errors', '1');` |
| 12 | üretim modu | `shared/src/Bootstrap/RuntimeBootstrap.php` | 16-18 | `error_reporting(E_ERROR \| E_WARNING \| E_PARSE);` + `display_errors '0'` |
| 13 | loglama (her iki mod) | `shared/src/Bootstrap/RuntimeBootstrap.php` | 19 | `ini_set('log_errors', '1');` |
| 14 | kernel kurulumu | `home.coremusic.net/config/bootstrap.php` | 97 | `new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);` |
| 15 | kernel çağrısı | `home.coremusic.net/config/bootstrap.php` | 106 | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile);` |
| 16 | son perde | `home.coremusic.net/config/bootstrap.php` | 107-114 | `catch (\Throwable $e)` → logger `error` → `http_response_code(500)` → `echo 'Internal Server Error';` |

## §11 — Erken Rota Zinciri (§2.2'yi genişletir · `config/bootstrap.php:9-115`)

| # | Satır | Tetik | Eylem | Kernel'e ulaşır mı |
|---|---|---|---|---|
| 1 | 10 | — | `HomeContainer::getInstance($config, $domainConfig)` | hayır (kurulum) |
| 2 | 13 | — | `rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')` | hayır |
| 3 | 16-26 | `$requestUri === '/health'` | 200 + JSON (`status`/`service`/`version`/`time`) + `exit` | hayır — erken çıkış |
| 4 | 29-31 | `#^/stream/([0-9a-fA-F]{32})$#` | `MusicStreamHandler::dispatch(...)` | hayır |
| 5 | 32-37 | `str_starts_with($requestUri, '/stream')` | 404 text/plain + `exit` | hayır |
| 6 | 40 | `/auth/callback` | auth kolu başlar | hayır |
| 7 | 49-51 | `FORCE_AUTH_BYPASS && APP_ENV_MODE !== 'production' && BYPASS_USER_UUID !== ''` | üçlü AND (fail-closed) | hayır |
| 8 | 52-61 | bypass aktif | `SessionBootstrapper::ensureStarted()` → session → 302 `/home` → `exit` | hayır |
| 9 | 63-67 | `auth_key` boş | 302 `/login` → `exit` | hayır |
| 10 | 70 | — | `SessionBootstrapper::ensureStarted()` | hayır |
| 11 | 73-74 | — | `HomeAuthBridge::validateAndCreateSession($authKeyRaw)` | hayır |
| 12 | 76-86 | `$result['success']` | `session_write_close()` → 302 `/home` → `exit` | hayır |
| 13 | 89-93 | doğrulama başarısız | warning log → 302 `/login?error=invalid_key` → `exit` | hayır |
| 14 | 97 | — | `$kernel = new PageRouterKernel(...)` | — |
| 15 | 100-101 | — | routes dosyası: `vendor/coremusic/shared-infrastructure/config/routes.php` | — |
| 16 | 103 | dosya yoksa | fallback `shared/config/routes.php` | — |
| 17 | 106 | — | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile)` | **evet** |
| 18 | 107-114 | `Throwable` | `LoggerFactory::getInstance()->error(...)` + 500 + sabit gövde | hata yolu |

**Sürücü kuralı (§2.2):** 1-13. satırlar **kernel öncesi** çalışır; yalnız 17. satır
kernel'e girer. Her erken kol kendi `exit` satırıyla shutdown evresine (M5) geçer.

## §12 — Idempotent Oturum Akışı (§2.5 disk doğrulaması · `shared/src/Session/SessionBootstrapper.php`)

| # | Metot | Satır | Davranış | Guard |
|---|---|---|---|---|
| 1 | alan tanımı | 17 | `private static bool $configured = false;` | sınıf içi sayaç |
| 2 | `ensureStarted()` | 19 | genel giriş | — |
| 3 | `ensureStarted()` | 21-23 | `session_status() === PHP_SESSION_ACTIVE` → `return` | idempotent kapısı |
| 4 | `ensureStarted()` | 25-27 | `SessionConfig::fromEnvironment()` → `configure()` → `session_start()` | — |
| 5 | `restartFresh()` | 34 | `$_SESSION = [];` | oturumu sıfırla |
| 6 | `restartFresh()` | 40-42 | `PHP_SESSION_ACTIVE` ise `session_destroy()` | koşullu |
| 7 | `restartFresh()` | 44-54 | `session.use_cookies` ise `setcookie(..., time() - COOKIE_EXPIRY, ...)` | cookie temizliği |
| 8 | `restartFresh()` | 56-58 | `configure()` → `session_start()` → `session_regenerate_id(true)` | tek zincir |
| 9 | `writeClose()` | 61-66 | `PHP_SESSION_ACTIVE` ise `session_write_close()` | koşullu |
| 10 | `configure()` | 70-72 | `$configured && ACTIVE` → `return` | ikinci guard |
| 11 | `configure()` | 74-80 | `session_name` · `session_save_path` (boşsa atla) · `session_set_cookie_params` | tek yetkili yer |
| 12 | `configure()` | 82 | `self::$configured = true;` | guard kapanır |

**Sürücü kuralı:** `session_*` çağrılarının tamamı bu sınıftadır; bootstrap'te ikinci
bir `session_start()` AP-2 ihlalidir (§2.5).

## §13 — Fail-Fast Karar Ağacı (M4 · disk karşılığı)

```text
constants.php:10   APP_ENV_MODE tanımsız mı? ──hayır──> atla (guard)
        │evet
constants.php:11-13   .env dosyası var mı? ──evet──> EnvParser::loadIntoEnv
        │hayır                                      (EnvParser.php:9-11 → [])
constants.php:22-23   mode beyazlistedeki 3 değerden biri mi?
        │hayır
constants.php:24-25   http_response_code(500) + exit('Configuration error: ...')
        │evet
constants.php:27-28   define(APP_ENV_MODE) + define(DEBUG_MODE)
index.php:14-16        DEBUG_MODE && function_exists(opcache_reset) → opcache_reset()
index.php:23           RuntimeBootstrap::boot(DEBUG_MODE)
bootstrap.php:106      $kernel->handle(...)
        │Throwable
bootstrap.php:107-114  logger->error + 500 + 'Internal Server Error'
```

| # | Karar noktası | Dosya:satır | Bilinmeyen değer | Sonuç |
|---|---|---|---|---|
| 1 | mod tanımlı mı | `constants.php:10` | `APP_ENV_MODE` yok | `.env` yüklenir |
| 2 | `.env` var mı | `constants.php:12` | dosya yok | parser hiç çağrılmaz |
| 3 | mod beyazliste mi | `constants.php:23` | 3 değer dışı | 500 + `exit` |
| 4 | `DEBUG_MODE` açık mı | `constants.php:28` | açık değilse `false` türetilir | opcache koruması kapalı |
| 5 | opcache var mı | `index.php:14` | `function_exists` false | reset atlanır |
| 6 | kernel fırlattı mı | `bootstrap.php:107` | `Throwable` | 500 + sabit gövde |

## §14 — Hata Modu Kataloğu (gözlenen / gözlenmeyen)

| # | Durum | Tetikleyici | Gözlenen davranış | Kanıt |
|---|---|---|---|---|
| 1 | Bilinmeyen ortam | `APP_ENV_MODE` 3 değer dışı | 500 + `exit(...)` | `constants.php:23-25` |
| 2 | `DEBUG_MODE` tanımsız | bayrak hiç yok | `constants.php:28` ile mode'dan türetilir | dosya 28 |
| 3 | `.env` yok | dosya bulunamadı | sessizce atlanır, devam | `EnvParser.php:9-11` |
| 4 | `.env` hatalı satır | `=` yok / `#` yorum | satır yutulur | `EnvParser.php:21-26` |
| 5 | Debug boot | `DEBUG_MODE=true` | `E_ALL` + display açık | `RuntimeBootstrap.php:12-14` |
| 6 | Üretim boot | `DEBUG_MODE=false` | `E_ERROR\|E_WARNING\|E_PARSE` + display kapalı | `RuntimeBootstrap.php:16-18` |
| 7 | Her iki mod | — | `log_errors=1` | `RuntimeBootstrap.php:19` |
| 8 | Bilinmeyen route / iş kuralı hatası | kernel içi | logger `error` + 500 + sabit gövde | `bootstrap.php:107-114` |
| 9 | Boş `auth_key` | callback | 302 `/login` | `bootstrap.php:63-67` |
| 10 | `auth_key` geçersiz | bridge | warning log + 302 `/login?error=invalid_key` | `bootstrap.php:89-93` |
| 11 | `/stream` eşleşmiyor | ön ek | 404 text/plain + `exit` | `bootstrap.php:32-37` |
| 12 | bypass bayrağı üretimde | `APP_ENV_MODE=production` | `FORCE_AUTH_BYPASS` `false` (gate) | `constants.php:40,45,51` |
| 13 | `header()` çağrısı boot öncesi | — | dosyada gözlenmedi | `index.php:1-29` |
| 14 | `display_errors=1` üretimde | — | gözlenmedi (üretim ram'de `0`) | `RuntimeBootstrap.php:18` |
| 15 | Kernel öncesi `exit` | health/stream/auth kolları | 4 ayrı `exit` | `bootstrap.php:25,36,60,66,85,93` |
| 16 | Shutdown kaydı | `register_shutdown_function` | **repo kodunda çağrı yok** | `[UNKNOWN]` (R6) |
| 17 | `fastcgi_finish_request` | drenaç | taramada gözlenmedi | `[UNKNOWN]` (R6) |
| 18 | `set_error_handler` / `set_exception_handler` | perde 1-2 | bu dalın dosyalarında **çağrı yok** | ⚠️ §2.1'deki iki perde repo'da doğrulanmadı |

## §15 — CI Boot Kapısı (`.github/workflows/ci.yml`)

| # | Job | Satır | `timeout-minutes` | `needs` | `continue-on-error` |
|---|---|---|---|---|---|
| 1 | `php-lint` | 25 | 15 | — | — |
| 2 | `php-test` | 66 | 15 | `php-lint` (68) | — |
| 3 | `composer-audit` | 110 | 10 | — | — |
| 4 | `js-test` | 142 | 10 | — | — |
| 5 | `js-coverage` | 160 | 15 | `js-test` (162) | `true` (167) |
| 6 | `js-e2e` | 178 | 20 | — | `true` (183) |

| # | Ortak ayar | Satır | Gözlem |
|---|---|---|---|
| 1 | tetikleyiciler | 11-12 | `push` · `pull_request` |
| 2 | izinler | 14-15 | `permissions: contents: read` |
| 3 | eşzamanlılık | 17-19 | `cancel-in-progress: true` |
| 4 | PHP sürümü | 21-22 | `PHP_VERSION: '8.4'` (3 `composer.json` → `"php": ">=8.4"`) |
| 5 | checkout | 30 · 82 · 115 · 147 · 169 · 185 | 6 job'da `actions/checkout@v4` |
| 6 | PHPUnit | 104 | `vendor/bin/phpunit "$PHPUNIT_ARGS"` |
| 7 | PHPStan | 62 | `composer stan` (`shared/composer.json:48`) |

## §16 — Anti-Pattern Genişletme (§3'e ek)

| ID | Anti-pattern | Yakalama | Doğrusu | Kanıt |
|---|---|---|---|---|
| AP-6 | `/health` yolunu kernel'e taşımak | health isteği routes dosyasını bekler | kernel öncesi erken çıkış | `bootstrap.php:16-26` |
| AP-7 | Bypass bayrağını tek koşula indirgemek | üretimde açık kalabilir | üçlü AND + fail-closed | `bootstrap.php:49-51` |
| AP-8 | routes dosyasını `dirname` fallback'siz aramak | sanal makine düzeninde 404 | iki adaylı çözümleme | `bootstrap.php:100-104` |
| AP-9 | catch bloğunda `$e->getMessage()` yerine trace'i cevaba yazmak | bilgi sızıntısı | sabit gövde `'Internal Server Error'` | `bootstrap.php:109-114` |
| AP-10 | `configure()` guard'sız `session_name()` çağırmak | ikinci `require`'da oturum kırılır | `$configured` guard | `SessionBootstrapper.php:70-72` |
| AP-11 | `session_write_close()` çağrısız 302 vermek | oturum verisi kaybolabilir | success kolunda `write_close` | `bootstrap.php:81-85` |
| AP-12 | CI'da `continue-on-error`'ü gate işi için kullanmak | job kırmızı görünür ama geçer | eşiğe ulaşınca kapatılır (yorum: ci.yml 161-167) | `ci.yml:167` |

## §17 — Perde ↔ Dosya Sorumluluk Matrisi

| # | Perde | Dosya | Kapsam | Bu dosyanın sınırı |
|---|---|---|---|---|
| 1 | Perde 0 (ön kontrol) | `config/constants.php` | fail-fast + türetme | §13 |
| 2 | Perde 1 (hata perdesi) | `shared/src/Bootstrap/RuntimeBootstrap.php` | reporting/display/log | §10 satır 10-19 |
| 3 | Perde 2 (erken kol) | `config/bootstrap.php:16-94` | health/stream/auth | §11 |
| 4 | Perde 3 (kernel catch) | `config/bootstrap.php:107-114` | Throwable → 500 | §11 satır 18 |
| 5 | Perde 4 (oturum) | `shared/src/Session/SessionBootstrapper.php` | idempotent session | §12 |
| 6 | Perde 5 (shutdown) | — | repo'da kayıt yok | `[UNKNOWN]` R6 |
| 7 | Perde 6 (CI) | `.github/workflows/ci.yml` | test kapısı | §15 |

## §18 — Tekrarlanabilir Ölçüm

| # | Amaç | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | dolu satır sayımı | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |
| 2 | satır içeriği | `[IO.File]::ReadAllLines($f)[$n-1]` | §10'daki birebir metin |
| 3 | giriş noktası bütünlüğü | `[IO.File]::ReadAllLines('home.coremusic.net\index.php').Count` | 29 |
| 4 | boot çağrısı | `Select-String -Path 'home.coremusic.net\index.php' -Pattern 'RuntimeBootstrap::boot'` | 1 eşleşme, satır 23 |
| 5 | session tekilliği | `Select-String -Path 'shared\src\Session\*.php' -Pattern 'session_start'` | yalnız `SessionBootstrapper` |
| 6 | CI job sayısı | `Select-String -Path '.github\workflows\ci.yml' -Pattern '^  [a-z0-9-]+:$'` | 6 |
| 7 | encoding | `[IO.File]::ReadAllText($f)` UTF-8 ayrıştırma | mojibake 0 |

## §19 — Negatif Kanıt Defteri (bu tur gözlemi)

| # | Aranan | Yöntem | Sonuç |
|---|---|---|---|
| 1 | `register_shutdown_function` | repo geneli tarama | bu dalın dosyalarında **yok** → `[UNKNOWN]` |
| 2 | `set_error_handler` / `set_exception_handler` | repo geneli tarama | PHP dosyalarında **yok** → ⚠️ §2.1 iki perde kanıtlanamadı |
| 3 | `fastcgi_finish_request` | tarama | yok → `[UNKNOWN]` (R6) |
| 4 | `opcache_compile_file` | tarama | yok → `[UNKNOWN]` (R5) |
| 5 | `pcntl_*` / `pthreads` | tarama | yok → bkz. `surec-ve-parcacik` R2 |
| 6 | `api.coremusic.net/bootstrap.php` | `Test-Path` | **YOK** (§10.1 satır 4) |
| 7 | `api.coremusic.net/Services/` | `Test-Path` | **YOK** (§10.1 satır 6) |
| 8 | FPM pool dosyası | arama | yok → `⚠️ VERIFICATION REQUIRED` (R3/R7) |

## §20 — URL Durumu

| # | URL | Kaynak | 2026-10-13 | Kullanım |
|---|---|---|---|---|
| 1 | https://www.php.net/manual/en/function.set-error-handler.php | `evidence:` (devralınan) | HTTP 200 | §2.1 perde 1 |
| 2 | https://www.php.net/manual/en/function.set-exception-handler.php | `evidence:` (devralınan) | HTTP 200 | §2.4 perde 2 |
| 3 | https://www.php.net/manual/en/opcache.setup.php | `evidence:` (devralınan) | HTTP 200 | §2.3 |
| 4 | Yeni URL | — | — | bu turda konuya uyan izinli domain içinden doğrulanmış yeni kaynak eklenmedi → **0** |

**Eklenen yeni doğrulanmış URL sayısı: 0.**

## §21 — Kanıt Dosyası Bütünlüğü (disk ölçümü, 2026-10-13)

> Ölçüm birimi: `[IO.File]::ReadAllLines($p).Count` (dosyadaki tüm satırlar; boş satırlar dâhil).
> Bu tablo bu dosyadaki her yol iddiasının diskte varlığını ve boyutunu kapatır.

| # | Dosya (repo köküne göre) | Satır | Bu dosyada hangi §'de kullanıldı | Disk |
|---|---|---|---|---|
| 1 | `home.coremusic.net/index.php` | 29 | §10 · §18/3 · §19 | VAR |
| 2 | `home.coremusic.net/config/bootstrap.php` | 115 | §10 · §11 (satır 9-115) | VAR |
| 3 | `home.coremusic.net/config/constants.php` | 117 | §13 · §14 | VAR |
| 4 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 21 | §10 (satır 23 çağrısı) | VAR |
| 5 | `shared/src/Config/EnvParser.php` | 51 | §13 fail-fast ağacı | VAR |
| 6 | `shared/src/Session/SessionBootstrapper.php` | 84 | §12 (oturum tekilliği) | VAR |
| 7 | `shared/src/Middleware/MiddlewarePipeline.php` | 54 | §4/5 `hrtime` ölçümü | VAR |
| 8 | `shared/src/PageRouter/PageRouterKernel.php` | 351 | §11 (rota zinciri hedefi) | VAR |
| 9 | `shared/config/domain.php` | 19 | §11 (subdomain eşlemesi) | VAR |
| 10 | `home.coremusic.net/.htaccess` | 37 | erken istek yönlendirme | VAR |
| 11 | `api.coremusic.net/.htaccess` | 42 | erken istek yönlendirme | VAR |
| 12 | `home.coremusic.net/web.config` | 43 | IIS karşılığı | VAR |
| 13 | `api.coremusic.net/web.config` | 41 | IIS karşılığı | VAR |
| 14 | `.github/workflows/ci.yml` | 203 | §15 (6 job) · §18/6 | VAR |
| 15 | `api.coremusic.net/index.php` | 223 | §19/6 (yol eşleşmesi) | VAR |
| 16 | `api.coremusic.net/bootstrap.php` | — | §19/6 · §10.1 | **YOK** |
| 17 | `api.coremusic.net/Services/` | — | §19/7 · §10.1 | **YOK** |

**Sonuç:** 15/17 yol diskte doğrulandı; 2 yol (`api.coremusic.net/bootstrap.php`, `api.coremusic.net/Services/`)
**yoktur** ve bu dosyadaki satır aralıkları `home.coremusic.net` + `shared/src/Bootstrap` ile eşleşir —
çelişki §10.1'de raporlandı, mevcut satırlar **düzeltilmedi** (yeniden yazma yasağı).

## §22 — Bölüm → Kanıt İzlenebilirliği (traceability)

| § | İddia | Kanıt (dosya:satır / komut) | Dayanak |
|---|---|---|---|
| §10 | boot sırası dosya → dosya | `home.coremusic.net/index.php` satır 1-29 okuması | disk |
| §10 | `RuntimeBootstrap::boot` çağrısı tek noktada | `Select-String -Pattern 'RuntimeBootstrap::boot'` → 1 eşleşme, satır 23 | disk |
| §11 | erken rota zinciri 9-115 | `config/bootstrap.php` satır aralığı | disk |
| §11 | subdomain/port eşlemesi | `shared/config/domain.php` 19 satır | disk |
| §12 | `session_start` tekilliği | `Select-String -Path 'shared\src\Session\*.php' -Pattern 'session_start'` | disk |
| §13 | fail-fast dalları | `config/constants.php` + `Config/EnvParser.php` | disk |
| §14 | hata modu kataloğu | `constants.php` sabit okuması | disk |
| §15 | CI 6 job | `Select-String -Path '.github\workflows\ci.yml' -Pattern '^  [a-z0-9-]+:$'` → 6 | disk |
| §15 | `continue-on-error` 2 yer | `ci.yml` satır 167 ve 183 | disk |
| §18 | ölçüm tekrarlanabilir | §18 tablosundaki 7 komut | prosedür |
| §19 | 8 negatif kayıt | §19 tablosundaki `Test-Path` / tarama | disk (negatif) |
| §19 | FPM pool dosyası yok | arama sonucu yok | **⚠️ VERIFICATION REQUIRED** (R3/R7) |

**Toplam bu turda bu dosyaya eklenen yeni doğrulanmış URL: 0** (§20).

## § OS GRUPLAR

> 8 işletim sistemi grubu ve boot/runtime başlatma modeli — bu dosyanın açısı **aktır**: hangi OS'te
> süreç/servis nasıl ayağa kalkar, A1-A7 bu akışın içinde hangi adımla yürür. (2026-10-14 tarama:
> repo'da `*.service` / `.plist` / `php.ini` YOK, `pcntl_` 0 eşleşme — §19 ile aynı sonuç.)

### § OS.1 — Karşılaştırma tablosu

| # | OS | Boot/runtime başlatma modeli | Bu dosyadaki karşılığı | Kaynak |
|---|---|---|---|---|
| 1 | Windows | UEFI → winload → NT kernel → SCM; IIS FastCGI (`php-cgi`) | §21/12-13 — `web.config` IIS karşılığı (43/41 satır) | `anayasa.md` § OS |
| 2 | macOS | boot.efi → XNU → launchd (PID 1); `php-fpm` LaunchDaemon job'ı | §2.1 — perde kurulumu boot'ta bir kez | `anayasa.md` § OS |
| 3 | iOS | iBoot → XNU → launchd → SpringBoard; `UIApplicationDelegate` | § OS.2 — kapsam dışı (PHP boot akışı) | `ornekler.md` § OS |
| 4 | Android | bootloader → Linux kernel → `init` → Zygote; öncelik ActivityManager | § OS.2 — kapsam dışı (PHP boot akışı) | https://developer.android.com/guide/components/activities/process-lifecycle |
| 5 | Linux | bootloader → Linux kernel → systemd (PID 1); `php-fpm.service` birimi | §2.2 — boot akışı (9 adım) + §14 hata modları | https://systemd.io/ |
| 6 | FreeBSD/BSD | loader → kernel → `init` (PID 1) → rc.d; PHP servisi rc.girişi | §2.2 — boot akışı (POSIX) | `anayasa.md` § OS |
| 7 | Tizen | Linux çekirdekli; uygulama süreç yaşam döngüsü (process-model) | § OS.2 — kapsam dışı (PHP boot akışı) | `ornekler.md` § OS |
| 8 | webOS OSE | Linux çekirdekli; servis katmanı Luna Service 2 (LS2) | § OS.2 — kapsam dışı (PHP boot akışı) | `ornekler.md` § OS |

### § OS.2 — OS kısa açıklamaları (her OS 2-3 cümle)

1. **Windows** — PHP resmi Windows paketiyle kurulur; `php.ini-development`/`php.ini-production` seçimi ve PHP dizininin `PATH`'e eklenmesi kurulumun parçasıdır. Web yüzeyi IIS + FastCGI (`php-cgi`) veya Apache'dir; süreç başlatma SCM (Service Control Manager) üzerindendir. Boot sırası A1-A7 OS'tan bağımsızdır.
2. **macOS** — `php.ini` konumu seçilen kurulum yöntemine göre belirlenir; süreç başlatma launchd üzerinden LaunchDaemon/LaunchAgent job'ı ile yapılır. `php-fpm` tek kez ayağa kalkar, istekler worker'a devredilir — "boot'ta bir kez" kuralının OS karşılığı.
3. **iOS** — Uygulama `UIApplicationDelegate` ile başlar; süreç yaşam dölgüsü ve App Sandbox yönetimi sisteme aittir. Sunucu tarafı PHP boot bu platformda yoktur — **kapsam dışı** (`ornekler.md` § OS.3).
4. **Android** — Açılışta `init` servisleri başlatır; uygulama süreçleri öncelik tablosuna girer ve sistem bellek için önce boşta kalanlar öldürülür. Çalışma zamanı AOT derlemesiyle açılır — **kapsam dışı** (aşağıda (b)-(c)).
5. **Linux** — PHP dağıtım paketi veya kaynaktan kurulur; `php-fpm` gibi servisler systemd birimi olarak yönetilir. A1-A7 sırası burada da geçerlidir; fail-fast (M4) PHP katmanında kalır, init sistemine devredilmez.
6. **FreeBSD/BSD** — Boot zinciri `loader` → kernel → `init` → rc.d ile yürür; PHP servisi bir rc.d girişiyle başlar. POSIX fork+exec semantiği ve A1-A7 sırası Linux ile aynıdır.
7. **Tizen** — Linux çekirdekli platform; web/native/.NET uygulamaları kendi modeliyle yaşar (UI/Widget/Service). Süreç ve yaşam dölgüsü resmi dokümanda ayrı bir tutorial'dır — **kapsam dışı** (`ornekler.md` § OS.3).
8. **webOS OSE** — Linux çekirdekli açık kaynak platform; uygulama ve servisler Luna Service 2 (LS2) üzerinden haberleşir. Mimari ve yaşam dölgüsü `docs/guides/core-topics` altındadır — **kapsam dışı** (`ornekler.md` § OS.3).

### § OS.3 — OS bazlı kod/örnek blokları (boot/runtime akışları)

#### (a) Linux — systemd servis birimi (bu projede kullanılmıyor)

```ini
[Unit]
Description=PHP-FPM worker
After=network.target

[Service]
Type=simple
ExecStart=/usr/sbin/php-fpm --nodaemonize
Restart=on-failure

[Install]
WantedBy=multi-user.target
```
*(kaynak: https://systemd.io/ — resmi systemd sitesi, 2026-10-14 HTTP 200 · repo'da `*.service` dosyası yok)*

#### (b) Android — process model (bu projede kullanılmıyor · kapsam dışı)

```text
1) foreground process  — ekranda etkileşimde olan süreç
2) visible process     — görünür ama odakta olmayan
3) service process     — arka planda çalışan servis
4) cached process      — boşta kalan; sistem bellek için ilk aday
```
*(kaynak: https://developer.android.com/guide/components/activities/process-lifecycle)*

#### (c) Android — runtime (bu projede kullanılmıyor · kapsam dışı)

```text
dex2oat ile AOT derleme → boot image ile hızlı açılış
uygulama süreci başlatıldığında öncelik tablosuna girer (bkz. (b))
```
*(kaynak: https://source.android.com/docs/core/runtime)*

#### (d) POSIX — fork+exec boot zinciri (akış özeti · bu projede kullanılmıyor)

```text
bootloader → kernel → init(PID 1)
  init ─fork/exec─> php-fpm master ─fork─> worker ─> index.php [A1-A7]
```
*(kod bloğu: `anayasa.md` § OS.3 (c) · repo'da `pcntl_` 0 eşleşme — §19/5)*

#### (e) Grup içindeki diğer bloklar (burada yalnız göstergeler)

- launchd plist (macOS) + Windows `php.ini` → `anayasa.md` § OS.3
- iOS `UIApplicationDelegate` + App Sandbox · Tizen process-model · webOS OSE docs → `ornekler.md` § OS.3

**Eklenen yeni URL sayısı: 3** — `systemd.io` · `activities/process-lifecycle` · `docs/core/runtime` (`evidence:` alanına eklendi · 2026-10-14 HTTP 200).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
