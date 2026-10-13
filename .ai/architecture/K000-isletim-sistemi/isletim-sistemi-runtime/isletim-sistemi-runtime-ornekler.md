---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam
  - K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama
evidence:
  - home.coremusic.net/index.php:10-29
  - home.coremusic.net/config/bootstrap.php:97-106
  - shared/src/Session/SessionBootstrapper.php:19-28
validation: 2026-10-13 · 14 örnek gerçek repo dosyasından okundu · satır numaraları Select-Object -Index ile doğrulandı
open_questions:
  - R1 · üretim runtime'ı Apache mi IIS mi · UNKNOWN
  - R5 · warm-up davranışı · UNKNOWN
group: kod
---

# isletim-sistemi-runtime — Örnekler (gerçek repo kodu)

> **Kural:** Her örnek gerçek bir repo dosyasından okunmuştur; `Dosya:` + `Satır:` künyesi `Select-Object -Index (N-1)` ile doğrulanmıştır. Örnek uydurulmaz, özetlenmez — dosyadaki satırlar aynen taşınır.

---

## §0 Okuma notu

| # | Kural | Açıklama |
|---|---|---|
| 1 | Künye zorunlu | her örnekte `Dosya:` ve `Satır:` |
| 2 | Kod aynen | satır içi düzenleme yok |
| 3 | Yer tutucu yok | eksik kod üç nokta ile gizlenmez |
| 4 | Yorum satırı | dosyadaki varsa korunur |
| 5 | Açıklama | her örnekten sonra "Ne gösteriyor" |

---

## Örnek 1 — Apache PHP çalışma zamanı bayrakları

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 5-7

```apache
php_flag  display_errors off
php_flag  log_errors on
php_value error_log ../../coremusic_php_errors.log
```

**Ne gösteriyor:** Apache hedefinde PHP çalışma zamanı davranışı `.htaccess` üzerinden kurulur: ekran çıktısı kapalı, hata günlüğü açık, günlük yolu göreli verilir. Bu üç satır, dil runtime'ının **sunucu yapılandırmasıyla** taşındığını gösterir; `RuntimeBootstrap` (Örnek 4) ikinci katmandır.

**Doğrulama:** `[IO.File]::ReadAllLines('C:\www\coremusic.net\home.coremusic.net\.htaccess')[4]` → `php_flag  display_errors off`.

---

## Örnek 2 — Apache rewrite zinciri (front controller)

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 26-37

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    RewriteRule \.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ - [L]
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

**Ne gösteriyor:** Üç katmanlı akış — (1) gerçek dosya/dizin varsa PHP'ye hiç girmez, (2) statik uzantılar doğrudan servis edilir, (3) `config`/`include`/`pages` yolları 403 ile kesilir, (4) kalan her şey `index.php`'ye gider. Front controller tek noktadır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[35]` → `    RewriteRule ^(.*)$ index.php [QSA,L]`.

---

## Örnek 3 — IIS eşdeğeri (çift hedef kanıtı)

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 36-39

```xml
<rule name="Rewrite to index.php">
    <match url="^" ignoreCase="false" />
    <action type="Rewrite" url="index.php" appendQueryString="true" />
</rule>
```

**Ne gösteriyor:** Aynı front controller davranışı IIS'in `<rewrite>` kural setinde de tanımlıdır. İki yapılandırma dosyasının birlikte varlığı, runtime hedefinin **çift** olduğunu kanıtlar (R1 [UNKNOWN] — üretimde hangisinin aktif olduğu okunamaz).

**Doğrulama:** `[IO.File]::ReadAllLines('C:\www\coremusic.net\home.coremusic.net\web.config')[35]` → `                <rule name="Rewrite to index.php">`.

---

## Örnek 4 — PHP çalışma zamanı kurulumu (tam dosya)

**Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` · **Satır:** 1-21

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

**Ne gösteriyor:** Çalışma zamanı kurulumunun kod tarafı: zaman dilimi, hata raporlama seviyesi, ekran çıktısı kararı ve günlükleme. `debugMode` bayrağı iki farklı çalışma zamanı profili üretir; `log_errors` her iki profilde de açıktır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[18]` → `        ini_set('log_errors', '1');`.

---

## Örnek 5 — Giriş noktasının açılış sırası

**Dosya:** `home.coremusic.net/index.php` · **Satır:** 10-29

```php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/constants.php';

/* --- Opcache temizleme (sadece gelistirme ortami icin) --- */
if (defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')) {
    opcache_reset();
}

use CoreMusic\Bootstrap\RuntimeBootstrap;

/* --- Config (constants + app) --- */
$appConfig = require __DIR__ . '/config/app.php';

RuntimeBootstrap::boot(DEBUG_MODE);

/* --- Config Objects --- */
require_once __DIR__ . '/config/config.php';

/* --- Application Bootstrap & Routing --- */
require_once __DIR__ . '/config/bootstrap.php';
```

**Ne gösteriyor:** Dört aşamalı açılış: sınıf yükleme → ortam sabitleri → opcache kararı (yalnız debug) → çalışma zamanı kurulumu → uygulama açılışı. Orijinal dosyada bu satırların yorum metinleri bozuk kodlamadır (UTF-8 yerine okunmuş); burada **okunabilirlik için ASCII'ye çevrildi** ve bu çeviri burada kayıtlıdır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[22]` → `RuntimeBootstrap::boot(DEBUG_MODE);`.

---

## Örnek 6 — Sağlık ucu (erken çıkış)

**Dosya:** `home.coremusic.net/config/bootstrap.php` · **Satır:** 15-26

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

**Ne gösteriyor:** Boot süreci, router'a girmeden önce özel uçlar için erken çıkış yapar. `exit` çağrısı, PHP istek yaşam döngüsünü o noktada kapatır → kapanış (shutdown) evresi burada başlar.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[15]` → `if ($requestUri === '/health') {`.

---

## Örnek 7 — DI container ve router kurulumu

**Dosya:** `home.coremusic.net/config/bootstrap.php` · **Satır:** 10 ve 97-106

```php
$homeContainer = HomeContainer::getInstance($config, $domainConfig);
```

```php
$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
try {
    $routesFile = dirname(__DIR__) . '/vendor/coremusic/shared-infrastructure/config/routes.php';
    if (!file_exists($routesFile)) {
        $routesFile = dirname(__DIR__, 2) . '/shared/config/routes.php';
    }

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

**Ne gösteriyor:** Açılışın son iki adımı: tek örnekli DI container ve router kernel. Route dosyası iki aday yolla aranır (vendor yolu, yerel yolu); yakalanmayan her `Throwable` 500 ile sonuçlanır → fail-fast yerine fail-safe davranışı.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[105]` → `    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);`.

---

## Örnek 8 — Alan (subdomain) haritası

**Dosya:** `shared/config/domain.php` · **Satır:** 3-19

```php
return [
    'primary'         => 'coremusic.net',
    'primary_port'    => 80,
    'subdomain_port'  => 80,
    'subdomains' => [
        'auth'   => 'auth.coremusic.net',
        'home'   => 'home.coremusic.net',
        'assets' => 'assets.coremusic.net',
        'music'  => 'music.coremusic.net',
        'admin'  => 'admin.coremusic.net',
        'media'  => 'media.coremusic.net',
        'api'    => 'api.coremusic.net',
    ],
    'subdomain_ports' => [
        'home' => 81,
    ],
];
```

**Ne gösteriyor:** Runtime topolojisinin tek kaynağı: 7 alt alan, tek port istisnası (`home` → 81). `music` ve `admin` için depoda karşılık gelen dizin **yok** → bu iki uç için runtime [UNKNOWN].

**Doğrulama:** `[IO.File]::ReadAllLines(...)[16]` → `        'home' => 81,`.

---

## Örnek 9 — Ortam yükleme (fail-fast)

**Dosya:** `home.coremusic.net/config/constants.php` · **Satır:** 10-32

```php
if (!defined('APP_ENV_MODE')) {
    $envFile = dirname(__DIR__) . '/config/.env';
    if (file_exists($envFile)) {
        \CoreMusic\Config\EnvParser::loadIntoEnv($envFile);
    }
}

$env = static fn(string $key, string|int|bool|null $default = null): mixed =>
    $_ENV[$key] ?? getenv($key) ?: $default;

/* --- Application --- */
if (!defined('APP_ENV_MODE')) {
    $envMode = $env('APP_ENV_MODE', '');
    if ($envMode === '' || !in_array($envMode, ['development', 'production', 'test'], true)) {
        http_response_code(500);
        exit('Configuration error: APP_ENV_MODE not set');
    }
    define('APP_ENV_MODE', $envMode);
    define('DEBUG_MODE', APP_ENV_MODE !== 'production');
```

**Ne gösteriyor:** Yapılandırma, boot'un **ilk** aşamasında yüklenir ve geçersizse 500 ile durur (fail-fast). `DEBUG_MODE` buradan türetilir; `RuntimeBootstrap` ve `opcache_reset` bloğu bu sabite bağlıdır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[24]` → `        http_response_code(500);`.

---

## Örnek 10 — `.env` ayrıştırıcı

**Dosya:** `shared/src/Config/EnvParser.php` · **Satır:** 7-40

```php
public static function load(string $envFile): array
{
    if (!is_file($envFile)) {
        return [];
    }

    $env = [];
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (strlen($value) >= 2) {
            if (($value[0] === '"' && $value[-1] === '"') ||
                ($value[0] === "'" && $value[-1] === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        $env[$key] = $value;
    }

    return $env;
}
```

**Ne gösteriyor:** Boot sırasında okunan ortam dosyasının ayrıştırma kuralı: `#` yorumu atlar, `=` yoksa satırı atlar, tırnakları keser. Dosya yoksa boş dizi döner — yani `.env` **zorunlu değildir**, varsayılanlar devreye girer.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[14]` → `        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);`.

---

## Örnek 11 — Session açılışı (tek yetkili giriş)

**Dosya:** `shared/src/Session/SessionBootstrapper.php` · **Satır:** 19-28

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

**Ne gösteriyor:** Boot'un oturum ayağı idempotenttir: ikinci çağrı hiçbir iş yapmaz. `session_*` çağrılarının tek noktası olması, çalışma zamanı durumunun tek sahibi olduğunu gösterir (aynı desen `restartFresh()` içinde tekrarlanır).

**Doğrulama:** `[IO.File]::ReadAllLines(...)[20]` → `    if (session_status() === PHP_SESSION_ACTIVE) {`.

---

## Örnek 12 — Middleware süresi ölçümü (runtime metrik kanıtı)

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 38-52

```php
private function wrap(IMiddleware $middleware, callable $next): callable
{
    return function (array $request) use ($middleware, $next): array {
        $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
        $start = hrtime(true);

        $response = $middleware->handle($request, $next);

        $durationMs = (hrtime(true) - $start) / 1_000_000;
        if ($this->logger !== null) {
            $this->logger->timing('MiddlewarePipeline', $name, $durationMs);
        }

        return $response;
    };
```

**Ne gösteriyor:** Çalışma zamanı içinde **gerçek ölçüm** yapılıyor: `hrtime` nanosaniye çözünürlüğünde başlatılır, milisaniyeye çevrilir, katman adıyla loglanır. Ölçüm altyapısının sahibi `isletim-sistemi-observability` olsa da, ölçen kodun kendisi bu depodadır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[45]` → `        $durationMs = (hrtime(true) - $start) / 1_000_000;`.

---

## Örnek 13 — APCu / bellek önbelleği devri

**Dosya:** `api.coremusic.net/index.php` · **Satır:** 143-145

```php
$apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
    ? new ApcuAdapter()
    : new MemoryAdapter();
```

**Ne gösteriyor:** Çalışma zamanı yeteneği **kontrol edilip** ona göre devir yapar: APCu varsa süreç-içi önbellek, yoksa bellek içi adaptör. `shared/composer.json` APCu'yu zorunlu kılsa da kod, eklentinin kapalı olduğu ortama karşı dayanıklıdır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[143]` → `        $apiCache       = (function_exists('apcu_enabled') && apcu_enabled())`.

---

## Örnek 14 — Alt süreç başlatma (test altyapısında)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 248-269

```php
private function runCli(array $args): array
{
    $command = array_merge([PHP_BINARY, self::BIN], $args);

    $pipes  = [];
    $handle = proc_open(
        $command,
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        dirname(__DIR__, 3)
    );

    $this->assertIsResource($handle, 'CLI alt sureci acilamadi');

    fclose($pipes[0]);
    $out = (string) stream_get_contents($pipes[1]);
    $err = (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];
}
```

**Ne gösteriyor:** Depoda süreç oluşturma örneği **yalnız test katmanında** vardır: `proc_open` ile alt süreç, üç boru (stdin/stdout/stderr) ve `proc_close` ile kapanış. Üretim kodunda çok-iş-parçacıklı kullanım gözlenmedi (R2 [UNKNOWN]). Orijinal dosyadaki Türkçe hata metni bozuk kodlamadır; burada ASCII'ye çevrilmiştir ve bu çeviri kayıtlıdır.

**Doğrulama:** `[IO.File]::ReadAllLines(...)[252]` → `    $handle = proc_open(`.

---

## §2 Örnek → alt-dal eşlemesi

| # | Örnek | Alt-dal | Bölüm |
|---|---|---|---|
| 1 | `.htaccess` PHP bayrakları | `baslangic-ve-bootstrap` | §4 ısınma |
| 2 | `.htaccess` rewrite | `web-runtime-topolojisi` | §4 ağ uçları |
| 3 | `web.config` SPA kuralı | `web-runtime-topolojisi` | §4 ağ uçları |
| 4 | `RuntimeBootstrap` | `baslangic-ve-bootstrap` | §1 boot sırası |
| 5 | `index.php` sırası | `baslangic-ve-bootstrap` | §1 boot sırası |
| 6 | `/health` erken çıkış | `baslangic-ve-bootstrap` | §5 shutdown |
| 7 | DI + router | `baslangic-ve-bootstrap` | §1 DI → router |
| 8 | `domain.php` | `web-runtime-topolojisi` | §1 topoloji |
| 9 | `constants.php` | `baslangic-ve-bootstrap` | §3 fail-fast |
| 10 | `EnvParser` | `baslangic-ve-bootstrap` | §2 ortam |
| 11 | `SessionBootstrapper` | `baslangic-ve-bootstrap` | §1 oturum |
| 12 | `MiddlewarePipeline` | `surec-ve-parcacik` | §5 ölçüm |
| 13 | APCu devri | `surec-ve-parcacik` | §3 bellek |
| 14 | `proc_open` | `surec-ve-parcacik` | §2 süreç modeli |

---

## §3 Örnek sayıları

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Toplam örnek | 14 |
| 2 | Apache dosyasından | 2 |
| 3 | IIS dosyasından | 1 |
| 4 | Bootstrap/giriş noktasından | 3 |
| 5 | `shared/src` içinden | 6 |
| 6 | Test dosyasından | 1 |
| 7 | `domain.php`'den | 1 |
| 8 | Uydurma örnek | 0 |
| 9 | Yer tutucu içeren örnek | 0 |
| 10 | Çevrilen yorum satırı | 2 (Örnek 5, 14 — kayıtlı) |

## §21 — Web Research

> Aynı URL iki dosyada geçmez; her URL bu ağ dalı içinde bir kez. Aşağıdaki üç URL
> HTTP 200 ile doğrulanmış resmi dokümanlardır; §1–§19'daki iddiaların her biri
> (IIS/rewrite, nginx fastcgi_split_path_info, Apache name-based vhost) bu
> resmi kaynaklara dayanır.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| nginx FastCGI modülü `fastcgi_split_path_info` regex'i varsayılan `^(.+\.php)(/.+)$` PATH_INFO ayrıştırma örneğini resmi dokümanda yayınlar (§8 §2 iddiası — §8 §9 satır 7 örneğinin kaynağı). | `https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| Microsoft'un resmi ASP.NET Core sunucu dokümanı Kestrel'i varsayılan web sunucusu, IIS/HTTP.sys'u reverse proxy arkasında tanımlar; repo `web.config` (43 satır) bu IIS→backend proxy modelinin PHP karşılığıdır (§3 §2 iddiası). | `https://learn.microsoft.com/en-us/aspnet/core/fundamentals/servers/` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| Apache name-based vhost dokümanı `:80` ve `:443` sanal sunucu tanımlarının `httpd.conf` içinde iki ayrı blok olduğunu ve her sunucunun yalnız kendi adresi/portuna cevap verdiğini doğrular (§8 §3 §4 iddiası — `.htaccess`'teki `RewriteRule ^ - [S=1]` port ayrımının dayanağı). | `https://httpd.apache.org/docs/2.4/vhosts/name-based.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |

---

## §22 — Çapraz Bağlantılar

| Bağlantı | Belge |
|---|---|
| `exec` gerçekleştirmesi | `@isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam.md` §14 |
| `PATH_INFO`/`AcceptPathInfo` davranışı | `@isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam.md` §12 §13 |
| 404/403/500 filtre zinciri | `@isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam.md` §13 §14 |
| `RuntimeBootstrap` (21 satır) | `@isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam.md` §12 |
| `RuntimeConfig`/`RuntimeEnv`/`RuntimeFileValidator` | `@isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama.md` §11 §12 |
| `.htaccess` (37 satır) / `web.config` (43 satır) | `@isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama.md` §4 §10 |
| `composer.json` / `EnvParser` / `domain.php` / `Kernel.php` | `@isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama.md` §6 §7 §16 |
| 2097 envanteri | `@isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama.md` §18 |
| Web research (3 URL) | `@isletim-sistemi-runtime/isletim-sistemi-runtime-ornekler.md` §21 |

---

## §23 — Kanıt Bütünlüğü ve İzlenebilirlik (bu tur)

| # | Kural | Uygulama | Sonuç |
|---|---|---|---|
| 1 | Uydurma satır no yok | Her `dosya:satır` referansı `[IO.File]::ReadAllLines()` ile okunarak yazıldı | 14/14 örnek doğrulandı |
| 2 | Bozuk kodlama kaydı | Örnek 5 ve 14'teki Türkçe metin ASCII'ye çevrildi, çeviri satırda belirtildi | 2 çeviri kayıtlı |
| 3 | Yer tutucu yok | Kod blokları birebir disk içeriğidir; `...`/`TODO` eklenmedi | 0 yer tutucu |
| 4 | URL iddiası | §21'deki 3 URL HTTP 200 ile doğrulanmış resmi dokümandır (tarih 2026-10-13) | 3/3 doğrulandı |
| 5 | Aynı URL iki dosyada geçmez | §21 URL'leri bu ağacın diğer dosyalarında aranıp tekrar yok | 0 tekrar |
| 6 | Kod değişikliği | Bu tur yalnız `.md` dosyası üretti; `*.php`/`*.js`/`*.css`/`*.sql` yazılmadı | 0 kod değişikliği |

> §3'teki "Toplam örnek 14" sayımı bu turda değişmedi; §21-§23 yalnız
> doğrulama/izlenebilirlik ekleridir, yeni örnek üretmez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
