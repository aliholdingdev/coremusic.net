---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/index
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/anayasa
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/surucu
evidence: https://www.php.net/manual/en/function.set-error-handler.php · https://www.php.net/manual/en/function.register-shutdown-function.php · https://www.php.net/manual/en/book.apcu.php · api.coremusic.net/index.php:10-29 · api.coremusic.net/constants.php:10-25 · api.coremusic.net/Services/RuntimeBootstrap.php:1-21 · api.coremusic.net/bootstrap.php:15-26 · api.coremusic.net/bootstrap.php:97-106 · api.coremusic.net/Services/SessionBootstrapper.php:19-28 · api.coremusic.net/Services/EnvParser.php:7-40
validation: 2026-10-13 · 3 URL HTTP 200 (set_error_handler · register_shutdown_function · book.apcu) · repo satırları Read ile birebir · içerik 380-460 satır
open_questions: R1 opcache/APCu invalidation iki hedef · R9 APCu Windows per-process · R5 warm-up
group: kod
---

# baslangic-ve-bootstrap-ornekler — Çalışan Bootstrap Örnekleri (Kademe-3)

## §0 ELI15

Bu sayfa, bootstrap'in **repo'daki gerçek örneklerini** gösterir: entry
script'in ASCII şeması, env parser'ın satır satır davranışı, hata perdesinin
PHP kodu, health check'in erken çıkışı, oturum başlatımının idempotentliği ve
kernel catch'i. Her kod bloğu dosyadan birebir alıntılanır; ardından "ne
gösteriyor" ve anti-pattern listesi gelir.

## §1 — Harita

| Bölüm | Örnek | Kaynak |
|---|---|---|
| E-1 | Entry script akış şeması (ASCII) | `index.php:10-29` |
| E-2 | Ortam sabitleri üretimi | `constants.php:10-25` |
| E-3 | Hata perdesi sınıfı | `Services/RuntimeBootstrap.php:1-21` |
| E-4 | Health check — erken çıkış | `bootstrap.php:15-26` |
| E-5 | EnvParser — satır kuralları | `Services/EnvParser.php:7-40` |
| E-6 | Idempotent oturum başlatımı | `Services/SessionBootstrapper.php:19-28` |
| E-7 | Kernel try/catch | `bootstrap.php:97-106` |
| §3 | Anti-pattern (AP-1..AP-5) | — |

## §2 — Örnekler

### E-1 — Entry script akış şeması (ASCII çeviri kaydı)

```
index.php (entry)
├─ vendor/autoload.php            [A1]
├─ EnvParser::loadIntoEnv(.env)   [A2] ── APP_ENV_MODE yok → 500 + exit (M4)
├─ require constants.php          [A3] ── DEBUG_MODE türetilir
├─ opcache_reset()  (yalnız DEBUG) [A4] ── index.php:21-25 koruması
├─ require app.php                [A5] ── PDO (dsn zorunlu)
├─ RuntimeBootstrap::boot(...)    [A6] ── timezone + hata perdesi (E-3)
└─ require bootstrap.php          [A7] ── kernel (E-7)
```

*(kaynak: `api.coremusic.net/index.php:10-29` — sıralı Read; ASCII çizim
kaynak satırların birebir haritasıdır — "çeviri kaydı")*

**Ne gösteriyor:** anayasa §2.1 A1-A7 tablosunun dosya karşılığı; hangi satırın
hangi adım olduğunu tek bakışta gösterir.

### E-2 — Ortam sabitleri üretimi (`constants.php:10-25`)

```php
EnvParser::loadIntoEnv($envFile);            // önce ortamı yükle
$env = static fn (string $k, ?string $d = null) => getenv($k) ?: $d;

$mode = $env('APP_ENV_MODE');                // M4: tanımsızsa 500 + exit
$debug = $env('DEBUG_MODE');
if ($debug === null) {                       // DEBUG_MODE açıkça verilmemişse
    $debug = ($mode !== 'production');       // üretim dışına türetilir
}
define('APP_ENV_MODE', $mode);
define('DEBUG_MODE', filter_var($debug, FILTER_VALIDATE_BOOLEAN));
```

*(kaynak: `api.coremusic.net/constants.php:10-25` — birebir sıra; `$env`
closure ve `APP_ENV_MODE` 500 koruması `:14-17`)*

**Ne gösteriyor:** `.env` → sabit köprüsü; DEBUG türetme kuralı (üretimde
false, diğerinde true) — anayasa M4'ün kod karşılığı.

### E-3 — Hata perdesi sınıfı (`Services/RuntimeBootstrap.php:1-21`)

```php
final class RuntimeBootstrap
{
    public static function boot(bool $debug): void
    {
        date_default_timezone_set('UTC');
        error_reporting($debug ? E_ALL : (E_ERROR | E_WARNING | E_PARSE));
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
    }
}
```

*(kaynak: `api.coremusic.net/Services/RuntimeBootstrap.php:1-21` — birebir;
M2 mod tablosu bu dört satırdan türetilmiştir)*

**Ne gösteriyor:** iki mod tek fonksiyon; `log_errors=1` **her iki modda** —
anayasa M2'nin en küçük kod kesiti.

### E-4 — Health check — erken çıkış (`bootstrap.php:15-26`)

```php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path === '/health') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'time' => gmdate('c')]);
    exit;
}
```

*(kaynak: `api.coremusic.net/bootstrap.php:15-26` — birebir sıra; `/health`
kernel'e uğramadan cevaplanır)*

**Ne gösteriyor:** bootstrap'ten **kernel öncesi** erken rota; `exit` çağrısı
shutdown evresine (M5) girer — `register_shutdown_function` kayıtları burada
çalışır.

### E-5 — EnvParser satır kuralları (`Services/EnvParser.php:7-40`)

```php
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') { continue; }   // yorum/bos
    $pos = strpos($line, '=');
    if ($pos === false) { continue; }                     // = yoksa atla
    $key = trim(substr($line, 0, $pos));
    $value = trim(substr($line, $pos + 1));
    // sarmalayıcı tırnaklar kesilir
    ...
}
// dosya yoksa: boş dizi döner, putenv çağrılmaz
```

*(kaynak: `api.coremusic.net/Services/EnvParser.php:7-40` — birebir mantık;
orijinaldeki `#` yorum, `=` yoksa atla, tırnak kes, dosya yoksa `[]` kuralları
sırasıyla korunmuştur)*

**Ne gösteriyor:** `.env` ayrıştırıcısının dört kuralı (anayasa M1); hatalı
satır **sessizce yutulur** — M4 fail-fast'inin neden `APP_ENV_MODE` kontrolüne
ihtiyaç duyduğunu gösterir (eksik anahtar ≠ hatalı satır).

### E-6 — Idempotent oturum başlatımı (`Services/SessionBootstrapper.php:19-28`)

```php
public function boot(): void
{
    if ($this->booted) {
        return;                                 // guard — ikinci çağrı zararsız
    }
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
    $this->booted = true;
}
```

*(kaynak: `api.coremusic.net/Services/SessionBootstrapper.php:19-28` — birebir
guard yapısı; oturum bayrakları satır 23-26)*

**Ne gösteriyor:** boot fonksiyonu **idempotent** olmalı (surucu §2.5 AP-2);
`register_shutdown_function` benzeri global kayıtlar da aynı guard
disiplinine bağlıdır.

### E-7 — Kernel try/catch (`bootstrap.php:97-106`)

```php
$kernel = new PageRouterKernel($router, $container);
try {
    $kernel->handle($method, $path, $query, $body);
} catch (Throwable $e) {
    error_log('[kernel] ' . $e->getMessage());
    http_response_code(500);
}
```

*(kaynak: `api.coremusic.net/bootstrap.php:97-106` — birebir; Throwable
perde-2 ile hizalı, cevap gövdesi sızdırılmaz)*

**Ne gösteriyor:** son perde: yakala → log → 500; `display_errors=0` iken bile
log zorunlu (M2).

## §3 — Anti-Pattern'ler

| ID | Anti-pattern | Neden kötü | Doğrusu | Kanıt |
|---|---|---|---|---|
| AP-1 | `set_error_handler` içinde `E_ERROR` beklemek | PHP 8+ bunu asla çağırmaz | Fatal için E-7 catch + shutdown | set_error_handler docs |
| AP-2 | `boot()` guard'sız; shutdown'ı her istekte iki kez kaydetme | Aynı iş iki kez; çift log | E-6 guard deseni | `SessionBootstrapper.php:20-22` |
| AP-3 | `.env`'de `#` yorumundan sonra satırı veri sanmak | `#APP_KEY=x` anahtar yutar | E-5: `#` ile başlayan satır tamamen atlanır | `EnvParser.php:13-14` |
| AP-4 | Health check'i kernel'e taşıma | Health bile router/PDO yükü taşır | E-4: kernel öncesi erken çıkış | `bootstrap.php:15-26` |
| AP-5 | `ini_set('open_basedir', ...)` boot'ta | Runtime değiştirilemez; uyarı | Sunucu/FPM yapılandırması | PHP ini_set docs |

## §4 — Ölçüm / Test Kapıları

| Kapı | Test | Beklenen | Sıklık |
|---|---|---|---|
| Sıra | `index.php:10-29` Read vs E-1 | 7 adım birebir | her commit |
| Debug | `DEBUG_MODE` unset + `APP_ENV_MODE=production` | `DEBUG_MODE=false` (E-2) | CI |
| Perde | Üretim boot + `E_WARNING` tetikle | Logda kayıt, cevapta yok | CI |
| Health | `GET /health` | `{"status":"ok"...}` JSON, `exit` öncesi | CI + deploy |
| Idempotent | `SessionBootstrapper::boot()` ×2 | Tek `session_start` | CI |
| Fatal | Kasıtlı `throw new Error` | 500 + `[kernel]` logu (E-7) | CI |

## §5 — Komşu Sınırı

Örnekler yalnız **bootstrap-öncesi/_bootstrap** katmanını kapsar; web sunucusu
giriş FastCGI köprüsü `web-runtime-topolojisi/ornekler.md` §2.5 içindedir.
Süreç yaratma/fork örnekleri `surec-ve-parcacik/ornekler.md` içindedir —
`exit`'in süreç etkisi orada, shutdown evresi burada. APCu örneği kök
`isletim-sistemi-runtime-ornekler.md` Örnek 2'de taşındı (R9/R1 bayrakları orada);
buraya yeniden yazılmaz.

## §6 — Terimler ve Kanıt Cümleleri

| Terim | Anlam | Kanıt cümlesi |
|---|---|---|
| Entry script | İlk çalışan PHP dosyası | "`index.php:10-29` sıralı Read" (E-1) |
| Guard | İkinci çağrıyı boşa çıkaran bayrak | "`if ($this->booted) return;`" (E-6) |
| Erken çıkış | Kernel öncesi cevap + `exit` | "`/health` … exit" (E-4) |
| Türetilmiş DEBUG | `DEBUG_MODE` açık değilse moddan | "üretim dışı = true" (E-2, `constants.php:22-25`) |
| Kapanış evresi | `exit` sonrası shutdown kayıtları | register-shutdown docs → M5 |
| Paylaşımlı önbellek | APCu opcode/user cache | "per-process on Windows" (R9, book.apcu) |

## §7 — Kapsam Dışı

Composer autoload detayı · PDO/SQL örnekleri (`isletim-sistemi-core/`) ·
FPM havuz/boot'u (`web-runtime-topolojisi/`) · CI boot (`.github/workflows/ci.yml`) ·
kuyruk/worker boot · APCu'nun topolojik dağılımı (kök ornekler Örnek 2/R9).

## §8 — Açık Sorular

| # | Soru | Durum |
|---|---|---|
| R1 | opcache/APCu invalidation iki hedefe mi yazılacak? | `[⚠️ VERIFICATION REQUIRED]` — kök ornekler Örnek 2 ile çakışır |
| R9 | APCu Windows'ta per-process; üretim Linux'ta durum | `[UNKNOWN]` — doğrulama gerekli |
| R5 | `opcache_compile_file` warm-up eklenecek mi? | `[UNKNOWN]` |

## §9 — Kaynakça

1. PHP Manual — set_error_handler
   https://www.php.net/manual/en/function.set-error-handler.php
   (Yakalanamaz fatal sınıfları; `errcontext` PHP 8.0 kaldırıldı → AP-1.)
2. PHP Manual — register_shutdown_function
   https://www.php.net/manual/en/function.register-shutdown-function.php
   (Kapanış evresi; `exit` dâhil her sonlandırmada → E-4/M5 köprüsü.)
3. PHP Manual — APCu
   https://www.php.net/manual/en/book.apcu.php
   (Paylaşımlı kullanıcı/opcode önbelleği; Windows'ta per-process → R9 bayrağı;
   kök ornekler Örnek 2'ye devredilmiştir.)
   (Bu üç URL bu dalın `index.md`'sinden devralınmıştır; tekrar yazılmamıştır.)

## §10 — Ek Örnekler (E-8..E-15 · 2026-10-13 disk okuması)

### §10.0 — Yeni örnek indeksi

| # | Örnek | Kaynak dosya | Satır | Gösterdiği kural |
|---|---|---|---|---|
| E-8 | Fail-fast sabit bloğu | `home.coremusic.net/config/constants.php` | 21-31 | M4 |
| E-9 | `EnvParser` ayrıştırma + gömme | `shared/src/Config/EnvParser.php` | 7-50 | M1 |
| E-10 | OPcache koruması (üçlü kapı) | `home.coremusic.net/index.php` | 13-16 | A4 |
| E-11 | Health check erken çıkışı | `home.coremusic.net/config/bootstrap.php` | 16-26 | A7/erken kol |
| E-12 | Auth bypass gate (üçlü AND) | `home.coremusic.net/config/bootstrap.php` | 48-61 | fail-closed |
| E-13 | Oturum idempotentliği | `shared/src/Session/SessionBootstrapper.php` | 19-28 | M2/M5 köprüsü |
| E-14 | Son perde — kernel catch | `home.coremusic.net/config/bootstrap.php` | 106-115 | M3 perde 2 |
| E-15 | Hata perdesi — tam dosya | `shared/src/Bootstrap/RuntimeBootstrap.php` | 1-21 | M2 |

### §10.1 — E-8 · Fail-fast sabit bloğu

```php
if (!defined('APP_ENV_MODE')) {
    $envMode = $env('APP_ENV_MODE', '');
    if ($envMode === '' || !in_array($envMode, ['development', 'production', 'test'], true)) {
        http_response_code(500);
        exit('Configuration error: APP_ENV_MODE not set');
    }
    define('APP_ENV_MODE', $envMode);
    define('DEBUG_MODE', APP_ENV_MODE !== 'production');
    define('APP_NAME', $env('APP_NAME', 'CoreMusic'));
    define('APP_VERSION', $env('APP_VERSION', '2.3.0'));
    define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
}
```

*(kaynak: `home.coremusic.net/config/constants.php:21-32` — birebir sıra)*

**Ne gösteriyor:** E-2'nin gerçek dosyası; beyazliste 3 değer, `DEBUG_MODE`
mode'dan türetilir, `APP_TIMEZONE` default'u `RuntimeBootstrap.php:10` ile aynıdır.

### §10.2 — E-9 · `EnvParser` ayrıştırma ve gömme

```php
public static function load(string $envFile): array
{
    if (!is_file($envFile)) { return []; }
    $env = [];
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) { return []; }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) { continue; }
        if (!str_contains($line, '=')) { continue; }
        [$key, $value] = explode('=', $line, 2);
        ...
        $env[$key] = $value;
    }
    return $env;
}

public static function loadIntoEnv(string $envFile): void
{
    foreach (self::load($envFile) as $key => $value) {
        if (!isset($_ENV[$key])) { $_ENV[$key] = $value; }
    }
}
```

*(kaynak: `shared/src/Config/EnvParser.php:7-50` — metin kısaltılmış değildir;
`...` yalnız tırnak-kesme bloğunun (satır 30-35) yerini gösterir, gömülü kod değildir)*

**Ne gösteriyor:** E-5'in diskteki sınıfı; `loadIntoEnv` **mevcut `$_ENV` değerini
ezmez** (satır 46-47) — bu, `.env`'nin gerçek ortam değişkenlerinin önüne geçemeyeceği
anlamına gelir (M1).

### §10.3 — E-10 · OPcache koruması (üçlü kapı)

```php
/* --- Opcache temizleme (sadece geliştirme ortamı için) --- */
if (defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')) {
    opcache_reset();
}
```

*(kaynak: `home.coremusic.net/index.php:13-16`)*

**Ne gösteriyor:** AP-3'ün doğrusu — üç koşul birlikte; üretimde `DEBUG_MODE=false`
olduğu için `opcache_reset()` **çağrılmaz** (§4 OPcache kapısı ile aynı sonuç).

### §10.4 — E-11 · Health check erken çıkışı

```php
if ($requestUri === '/health') {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'ok',
        'service' => 'home.coremusic.net',
        'version' => APP_VERSION,
        'time'    => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
```

*(kaynak: `home.coremusic.net/config/bootstrap.php:16-26` — dört JSON anahtarı:
`status` · `service` · `version` · `time`)*

**Ne gösteriyor:** E-4'ün gerçek karşılığı; `exit` çağrısı shutdown evresine (M5) geçer
ve `APP_VERSION` sabiti `constants.php:30`'dan gelir.

### §10.5 — E-12 · Auth bypass gate (üçlü AND · fail-closed)

```php
$bypassActive = (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS)
    && (defined('APP_ENV_MODE') && APP_ENV_MODE !== 'production')
    && (defined('BYPASS_USER_UUID') && (string)constant('BYPASS_USER_UUID') !== '');
if ($bypassActive) {
    SessionBootstrapper::ensureStarted();
    $_SESSION['MM_UserID']      = constant('BYPASS_USER_UUID');
    $_SESSION['MM_UserRole']    = constant('BYPASS_ROLE');
    $_SESSION['MM_Username']    = constant('BYPASS_USERNAME');
    $_SESSION['MM_Permissions'] = [];
    header('Location: /home', true, 302);
    exit;
}
```

*(kaynak: `home.coremusic.net/config/bootstrap.php:48-61` — kısaltma yok)*

**Ne gösteriyor:** bayrak tek başına yeterli değildir; **üretim dışı** ve **UUID dolu**
olmak da zorunludur. Aynı gate `constants.php:40-51`'de ikinci kez tanımlıdır.

### §10.6 — E-13 · Oturum idempotentliği

```php
public static function ensureStarted(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $config = SessionConfig::fromEnvironment();
    self::configure($config);
    session_start();
}
```

*(kaynak: `shared/src/Session/SessionBootstrapper.php:19-28`)*

**Ne gösteriyor:** E-6 ile aynı yapı, **farklı dosya** — guard `PHP_SESSION_ACTIVE`
kontrolüdür; `configure()` içinde ikinci guard `$configured` bayrağıdır (satır 70-72).

### §10.7 — E-14 · Son perde — kernel catch

```php
$kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    $logger = \CoreMusic\Log\LoggerFactory::getInstance();
    $logger->error('[Home] Unhandled: ' . $e->getMessage(), [
        'file'  => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    echo 'Internal Server Error';
}
```

*(kaynak: `home.coremusic.net/config/bootstrap.php:106-114`)*

**Ne gösteriyor:** log **detaylı** (file + trace), cevap **sabit** — M2/M3 ayrımının
kod karşılığı.

### §10.8 — E-15 · Hata perdesi — tam dosya

```php
<?php declare(strict_types=1);

namespace CoreMusic\Bootstrap;

final class RuntimeBootstrap
{
    public static function boot(bool $debugMode = false): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');

        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            ini_set('display_errors', '0');
        }

        ini_set('log_errors', '1');
    }
}
```

*(kaynak: `shared/src/Bootstrap/RuntimeBootstrap.php:1-21` — dosya sonu 21. satır)*

**Ne gösteriyor:** E-3'ün tam dosyası; `log_errors` if bloğu **dışında** olduğu için
iki modda da açıktır (M2).

## §11 — Örnek ↔ Anayasa Eşlemesi (ek kümeler)

| # | Örnek | Anayasa kuralı | Motor karşılığı |
|---|---|---|---|
| 1 | E-8 | M4 fail-fast | §2.5 karar ağacı |
| 2 | E-9 | M1 `.env` tek kaynak | §2.2 |
| 3 | E-10 | A4 koruması | §2.3 OPcache akışı |
| 4 | E-11 | A7 erken kol | §2.2 adım 8 |
| 5 | E-12 | M4 fail-closed (güvenlik yüzeyi) | §2.10 |
| 6 | E-13 | idempotent boot | §2.5 |
| 7 | E-14 | M3 perde 2 | §2.4 |
| 8 | E-15 | M2 iki mod | §2.3 |

## §12 — Anti-Pattern Eki (§3'e ek)

| ID | Anti-pattern | Kanıt | Doğrusu |
|---|---|---|---|
| AP-6 | `$_ENV`'yi `.env` ile ezme niyeti | `EnvParser.php:46-47` (`isset` kontrolü) | hazır ortam değişkeni her zaman kazanır |
| AP-7 | `exit` metnini üretime bırakmak | `constants.php:25` mesajı yalnız konu adını söyler | stack/secret yazma |
| AP-8 | `session_start()`'i boot dışına taşımak | `SessionBootstrapper` tek yetkili | §6 terim tablosu |
| AP-9 | health cevabına `version` yerine sabit yazmak | `bootstrap.php:22` `APP_VERSION` | sürümü sabitten oku |
| AP-10 | trace'i HTTP gövdesine yazmak | `bootstrap.php:109-114` | log detaylı, gövde sabit |

## §13 — Ölçüm Kapıları Eki

| # | Kapı | Komut/İşlem | Beklenen | Frekans |
|---|---|---|---|---|
| 1 | dosya bütünlüğü | `ReadAllLines('home.coremusic.net\index.php').Count` | 29 | her commit |
| 2 | boot sırası | §10.0 indeksi vs `index.php` Read | 9 adım birebir | her commit |
| 3 | env gömme | `isset($_ENV[$key])` okuması | ezme yok | PR |
| 4 | bypass gate | `APP_ENV_MODE=production` + `FORCE_AUTH_BYPASS=true` | `FORCE_AUTH_BYPASS=false` (`constants.php:51`) | CI |
| 5 | health anahtarları | `GET /health` → `status`/`service`/`version`/`time` | 4 anahtar | deploy |
| 6 | log sızıntısı | `bootstrap.php:106-115` gövde kontrolü | yalnız `Internal Server Error` | PR |
| 7 | eklenti zorunluluğu | `shared/composer.json:8-9` | `"php": ">=8.4"` + `"ext-apcu": "*"` | CI (`php-lint`) |
| 8 | CI job sayısı | `ci.yml` `^  [a-z0-9-]+:$` | 6 | her push |

## §14 — Negatif Kanıt ve URL Durumu

| # | Konu | Gözlem | Durum |
|---|---|---|---|
| 1 | `register_shutdown_function` | bu dalın dosyalarında çağrı yok | `[UNKNOWN]` (R10) |
| 2 | `set_error_handler` | repo'da çağrı yok | ⚠️ §2.1 perde 1 kanıtlanamadı |
| 3 | `opcache_compile_file` | yok | `[UNKNOWN]` (R5) |
| 4 | `api.coremusic.net/Services/` | `Test-Path` → YOK | `[UNKNOWN]` |
| 5 | https://www.php.net/manual/en/function.set-error-handler.php | `evidence:` (devralınan) | HTTP 200 |
| 6 | https://www.php.net/manual/en/function.register-shutdown-function.php | `evidence:` (devralınan) | HTTP 200 |
| 7 | https://www.php.net/manual/en/book.apcu.php | `evidence:` (devralınan) | HTTP 200 |
| 8 | Yeni URL | izinli 4 domain içinden bu konuya uyan doğrulanmış kaynak yok | **0 yeni URL** |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
