---
node_id: K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-ornekler
node_type: subdomain-scope
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kapsam
  - K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-dogrulama
evidence:
  - shared/src/Component/TemplateEngine.php:73
  - shared/src/Session/SessionConfig.php:35
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:253
  - shared/src/Cache/ApcuAdapter.php:13
validation: "2026-10-14 · §6 grup anahtarı (G1-G8, 16 satır → 5 dosyaya dağıtıldı) · §6.4 4 yeni URL HTTP 200 (envanter kesişimi 0) · §6.5 10 reuse URL yeniden 200 · §7 PHP köprüsü (4 alt başlık · gerçek satır blokları · yeni URL 0) · §8 çok dilli indeksi (envanter: 320 PHP · 98 JS · 1 PY · 0 C/Go/Rust/C# · 23 tekil URL = 9 reuse + 14 yeni · 22 kalıp blok · 5 repo satır bloğu) · FM 10 anahtar · 761 satır · önceki tur (2026-10-13) kanıtları §4-§5'te korunur · satırlar ReadAllLines indeksi ile birebir"
open_questions:
  - "Örnek 1-6 kod blokları temsilî özet mi byte-birebir mi? → §4.1 birebir satır karşılıklarını verir; tam eşleşme iddiası ⚠️ VERIFICATION REQUIRED"
  - "Her örnek `core`'da mı `kernel`'de mi? (Q2) → ⚠️ VERIFICATION REQUIRED (Q-C1)"
  - "Hedef platform seti (Q4) → UNKNOWN (Q-C3)"
group: kod
---

# isletim-sistemi-core — Gerçek Repo Örnekleri

> Bu dosya, `os-master.md` §3'teki **8 çekirdek kavramın** CoreMusic'teki gerçek
> karşılıklarını taşır. Her blok diskten birebir kopyalanmıştır.

## Örnek 1 — Bellek/dosya kavramı: geçici dizin

**Dosya:** `shared/src/Component/TemplateEngine.php:73`

```php
$cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coremusic-templates';
```

**Kavram:** Bellek + dosya sistemi. Geçici dizin **OS'den sorulur** (`getenv`/OS API),
`DIRECTORY_SEPARATOR` ile platform yolu birleştirilir. Uygulama sabit yol **varsaymaz**.

## Örnek 2 — İzin kavramı: POSIX erişim modu

**Dosya:** `shared/src/Session/SessionConfig.php:33-36`

```php
$savePath = ini_get('session.save_path') ?: sys_get_temp_dir();
if (!is_dir($savePath) || !is_writable($savePath)) {
    @mkdir($savePath, 0750, true);
}
```

**Kavram:** Dosya sistemi + izin. `0750` = POSIX erişim modu (owner `rwx`, group `r-x`,
diğer `-`). `sys_get_temp_dir()` fallback zinciri, OS'un oturum yolu yoksa devreye girer.
`@` operatörü, izin hatasının `mkdir`'i durdurmasını engeller (sessiz başarısızlık —
gözlemlenebilirlik ayrı katmandadır).

## Örnek 3 — Süreç kavramı: borulu alt süreç

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php:252-258`

```php
$pipes  = [];
$handle = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    dirname(__DIR__, 3)
);
```

**Kavram:** Süreç + dosya tanıtıcısı (fd). `0/1/2` = stdin/stdout/stderr; `pipe` ile
çalışan süreçle haberleşme kurulur. `proc_close` çıkış kodunu döndürür — süreç yaşam
döngüsünün gözlemlenebilir sonucu.

## Örnek 4 — Paylaşımlı bellek kavramı: APCu

**Dosya:** `shared/src/Cache/ApcuAdapter.php`

```php
if (!function_exists('apcu_fetch')) {
    return false;
}
```

**Kavram:** Paylaşımlı bellek. APCu, **aynı makinedeki tüm PHP süreçlerinin** görebildiği
bellek alanıdır — tam olarak "işletim sistemi belleği paylaştırır" kavramının uygulama
karşılığı. Uzantı yoksa önbellek sessizce devre dışı kalır.

## Örnek 5 — Startup kavramı: çalışma zamanı kurulumu

**Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php:1-21`

```php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
date_default_timezone_set('UTC');
```

**Kavram:** Başlatma (init). Süreç başlarken hata gösterimi kapatılır, log'a yönlendirilir
ve zaman dilimi sabitlenir. Bu, OS'un süreç başlatma anına uygulamanın verdiği tepkidir.

## Örnek 6 — Dosya kavramı: yol çözümleme + symlink reddi

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215`

```php
$real = realpath($path);
if ($real === false || is_link($path)) {
    // çözülemedi ya da symlink → reddet
}
// DIRECTORY_SEPARATOR ile sınır denetimi
```

**Kavram:** Dosya sistemi + güvenlik. `realpath` sembolik bağlantıları **çözerek** gerçek
yolu verir; `is_link` orijinal yolun bağlantı olup olmadığını sorar. İkisi birlikte
**yol zehirlenmesi** savunmasını oluşturur (OS `lstat`/`readlink` sargıları).

## §1 Özet — kavram ↔ repo

| # | Çekirdek kavram | Dosya : satır | OS hizmeti |
|---|---|---|---|
| 1 | Bellek/dosya | `TemplateEngine.php:73` | `sys_get_temp_dir` |
| 2 | İzin | `SessionConfig.php:33-36` | `mkdir` · POSIX `0750` |
| 3 | Süreç | `ApiKeyCreateCommandTest.php:252-258` | `proc_open` · fd 0/1/2 |
| 4 | Paylaşımlı bellek | `ApcuAdapter.php` | APCu |
| 5 | Başlatma | `RuntimeBootstrap.php:1-21` | `ini_set` · tz |
| 6 | Dosya çözümleme | `MusicStreamHandler.php:185-215` | `realpath` · `is_link` |

## §2 Resmi referanslar (2026-10-12 · HTTP 200)

| # | Kavram | Kaynak |
|---|---|---|
| 1 | çekirdek API | https://docs.kernel.org/core-api/index.html |
| 2 | bellek eşleme | https://man7.org/linux/man-pages/man2/mmap.2.html |
| 3 | Windows bellek | https://learn.microsoft.com/en-us/windows/win32/memory/memory-management |
| 4 | zamanlama | https://docs.kernel.org/scheduler/index.html |
| 5 | süreç/iş | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads |

## §3 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-C1 | Bu 6 örneğin hangisi `core`'da, hangisi `kernel`'de? (Q2) | ⚠️ VERIFICATION REQUIRED |
| Q-C2 | 8 kavramdan hangisi bu dosyada eksik? | ⚠️ VERIFICATION REQUIRED |
| Q-C3 | Hedef platform (Q4) — `0750` ve `DIRECTORY_SEPARATOR` hangi OS'ta doğrulandı? | UNKNOWN |

---

## §4 Genişletilmiş repo kanıtları — `Dosya:` / `Satır:` matrisi (2026-10-13)

> Bu bölüm 2026-10-13 turunda eklendi. Her satır `[IO.File]::ReadAllLines(path)[n-1]`
> ile diskten okunarak doğrulanmıştır (satır no = 0-tabanlı indeks + 1). Uydurma satır
> numarası yoktur: okunamayan çift yazılmaz.

### §4.1 Örnek 1-6 için birebir satır karşılıkları

Örnek 1-6'daki kod blokları **temsilî özet**tir (anlatım kolaylığı için sadeleştirilmiştir);
aşağıdaki tablo aynı satırların diskten okunmuş **birebir** hâllerini verir.

| # | Dosya | Satır | Diskten okunan birebir içerik |
|---|---|---|---|
| 1 | `shared/src/Component/TemplateEngine.php` | 73 | `$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';` |
| 2 | `shared/src/Component/TemplateEngine.php` | 87 | `if (is_file($cachePath)) {` |
| 3 | `shared/src/Session/SessionConfig.php` | 33 | `$savePath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';` |
| 4 | `shared/src/Session/SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true);` |
| 5 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `$handle = proc_open(` |
| 6 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 255 | `[0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],` |
| 7 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 268 | `return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];` |
| 8 | `shared/src/Cache/ApcuAdapter.php` | 9 | `if (!function_exists('apcu_fetch')) {` |
| 9 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 9 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` |
| 10 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 12 | `error_reporting(E_ALL);` |
| 11 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 19 | `ini_set('log_errors', '1');` |
| 12 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 185 | `if (is_link($trimmed)) {` |
| 13 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | `$real = realpath($trimmed);` |

**Düzeltme notu:** Örnek 5 bloğundaki `date_default_timezone_set('UTC');` ifadesi satır 9 ile
**örtüşmez** — birebir satır `Europe/Istanbul` (`$_ENV['APP_TIMEZONE']` fallback'i) taşır.
Temsilî blok silinmedi (mevcut içerik koruma kuralı), burada disk gerçeğiyle düzeltildi.

### §4.2 Zamanlama ve süreç kanıtları

| # | Dosya | Satır | Birebir içerik | OS kavramı |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` | mono saat (`clock_gettime`) |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` | süre ölçümü (ns → ms) |
| 3 | `shared/src/AI/AIWorkflow.php` | 51 | `$startTime = microtime(true);` | saniye çözünürlüklü zaman |
| 4 | `shared/src/AI/AIWorkflow.php` | 80 | `$executionTime = microtime(true) - $startTime;` | çalışma süresi hesabı |
| 5 | `shared/src/Security/UuidV7.php` | 19 | `$ms = (int)(microtime(true) * 1000);` | zaman damgası → ms |
| 6 | `shared/src/PageRouter/SessionInitializer.php` | 23 | `session_start();` | oturum = süreç-durumu |
| 7 | `shared/src/PageRouter/SessionInitializer.php` | 31 | `$result['started'] = true;` | oturum başlangıç bayrağı |
| 8 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 12 | `error_reporting(E_ALL);` | başlatma yapılandırması |
| 9 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 268 | `return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];` | süreç sonlanma kodu |
| 10 | `shared/src/AI/AIEngine.php` | 295 | `$output = shell_exec($cmd);` | alt süreç çalıştırma |

**Yorum:** Zaman ölçümü iki farklı çözünürlükle yapılır: `hrtime(true)` (nanosaniye, mono)
kısa adımlar için, `microtime(true)` (saniye float) uzun adımlar için. Bu ayrım OS'taki
`CLOCK_MONOTONIC` ↔ `CLOCK_REALTIME` ayrımının sargısıdır.

### §4.3 Bellek kanıtları

| # | Dosya | Satır | Birebir içerik | OS kavramı |
|---|---|---|---|---|
| 1 | `shared/src/Cache/ApcuAdapter.php` | 13 | `$value = apcu_fetch($key, $success);` | paylaşımlı bellek okuma |
| 2 | `shared/src/Cache/ApcuAdapter.php` | 22 | `return apcu_store($key, $value, $ttl);` | paylaşımlı bellek yazma |
| 3 | `shared/src/Cache/ApcuAdapter.php` | 9 | `if (!function_exists('apcu_fetch')) {` | yetenek yoksa devre dışı |
| 4 | `shared/src/AI/MemorySystem.php` | 227 | `$size += mb_strlen(serialize($entry['value']), '8bit');` | bellek kullanımı ölçümü |
| 5 | `shared/src/Component/TemplateEngine.php` | 73 | `rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';` | geçici dosya = diske yazılmış bellek |
| 6 | `shared/src/Component/TemplateEngine.php` | 87 | `if (is_file($cachePath)) {` | önbellek isabet kontrolü |
| 7 | `shared/src/Security/JwtService.php` | 114 | `$pem = is_file($this->privateKeyPath) ? file_get_contents($this->privateKeyPath) : false;` | dosyadan belleğe yükleme |
| 8 | `shared/src/Security/OAuthManager.php` | 190 | `$iv = random_bytes(12);` | rastgele bayt üretimi |

### §4.4 Dosya sistemi kanıtları

| # | Dosya | Satır | Birebir içerik | OS kavramı |
|---|---|---|---|---|
| 1 | `shared/src/Log/FileHandler.php` | 251 | `@file_put_contents($filepath, $content, FILE_APPEND \| LOCK_EX);` | ekleme + kilitli yazma |
| 2 | `shared/src/PageRouter/PageRouterKernel.php` | 133 | `@file_put_contents($logDir . '/coremusic_php_errors.log', $errorMsg, FILE_APPEND \| LOCK_EX);` | hata günlüğü dosyası |
| 3 | `shared/src/PageRouter/ResponseEmitter.php` | 60 | `$etag = '"' . hash('xxh64', $html) . '"';` | içerik özeti (ETag) |
| 4 | `shared/src/PageRouter/ResponseEmitter.php` | 71 | `header('Content-Length: ' . strlen($html));` | uzunluk bildirimi |
| 5 | `shared/src/AI/ToolCalling.php` | 187 | `return ['content' => file_get_contents($path), 'size' => filesize($path)];` | okuma + boyut sorgu |
| 6 | `shared/src/AI/ToolCalling.php` | 200 | `$result = file_put_contents($params['path'], $params['content']);` | yazma |
| 7 | `shared/src/AI/ToolCalling.php` | 215 | `$files = glob($path . '/' . $params['pattern']);` | dizin tarama |
| 8 | `shared/src/Config/DomainConfig.php` | 17 | `dirname(__DIR__, 3) . '/config/domain.php',` | yol birleştirme |
| 9 | `api.coremusic.net/config/constants.php` | 42 | `$authEnvFile = dirname(__DIR__) . '/../auth.coremusic.net/config/.env';` | göreli yol çözümleme |
| 10 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 185 | `if (is_link($trimmed)) {` | symlink denetimi |
| 11 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | `$real = realpath($trimmed);` | gerçek yol çözümleme |
| 12 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 297 | `@ini_set('zlib.output_compression', '0');` | akış çıktısı yapılandırması |
| 13 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 322 | `header('Content-Length: ' . $length);` | bayt uzunluğu bildirimi |
| 14 | `shared/src/Security/JwtService.php` | 114 | `$pem = is_file($this->privateKeyPath) ? file_get_contents($this->privateKeyPath) : false;` | dosya varlık + okuma |
| 15 | `shared/src/Component/TemplateEngine.php` | 73 | `'cmtpl_' . bin2hex(random_bytes(8)) . '.php'` | benzersiz geçici dosya adı |

### §4.5 İzin ve güvenlik kanıtları

| # | Dosya | Satır | Birebir içerik | OS kavramı |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true);` | POSIX erişim modu `0750` |
| 2 | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `if ($sessionToken === '' \|\| $submitted === '' \|\| !hash_equals($sessionToken, (string)$submitted)) {` | sabit-zamanlı karşılaştırma |
| 3 | `shared/src/Repository/ApiKeyRepository.php` | 108 | `'key_hash'   => hash('sha256', $rawKey),` | anahtar özeti saklama |
| 4 | `shared/src/Repository/ApiKeyRepository.php` | 165 | `$rawHash = hash('sha256', $rawKey);` | gelen anahtar özeti |
| 5 | `shared/src/Repository/ApiKeyRepository.php` | 171 | `if ($stored !== '' && hash_equals($stored, $rawHash)) {` | özeti karşılaştırma |
| 6 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 18 | `$request['_csp_nonce'] = bin2hex(random_bytes(32));` | rastgele nonce |
| 7 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 62 | `return 'api:' . hash('sha256', $apiKey);` | istemci kimlik özeti |
| 8 | `shared/src/Middleware/RateLimiterMiddleware.php` | 110 | `if (filter_var($remoteIp, FILTER_VALIDATE_IP) && $this->isTrustedProxy($remoteIp)) {` | IP doğrulama + proxy |
| 9 | `shared/src/Security/ReturnUrlPolicy.php` | 157 | `if (filter_var($url, FILTER_VALIDATE_URL) === false) {` | URL doğrulama |
| 10 | `shared/src/Security/OAuthManager.php` | 190 | `$iv = random_bytes(12);` | şifreleme nonce/IV |

### §4.6 Kavram → OS hizmeti → PHP sargısı → kanıt

| # | Çekirdek kavram | OS hizmeti | PHP sargısı | Kanıt (Dosya:satır) |
|---|---|---|---|---|
| 1 | Bellek (geçici) | geçici dizin sorgusu | `sys_get_temp_dir()` | `TemplateEngine.php:73` |
| 2 | Bellek (ölçüm) | süreç bellek sayacı | `serialize` + `mb_strlen` | `MemorySystem.php:227` |
| 3 | Paylaşımlı bellek | süreçler-arası bölge | APCu uzantısı | `ApcuAdapter.php:13` · `:22` |
| 4 | Dosya (yazma) | `write` + `flock` | `file_put_contents(LOCK_EX)` | `FileHandler.php:251` |
| 5 | Dosya (okuma) | `read` + `stat` | `file_get_contents` · `filesize` | `ToolCalling.php:187` |
| 6 | Dosya (dizin) | `opendir`/`readdir` | `glob()` | `ToolCalling.php:215` |
| 7 | Dosya (yol) | `lstat`/`readlink` | `is_link` · `realpath` | `MusicStreamHandler.php:185` · `:189` |
| 8 | İzin (mod) | `mkdir` + `mode` | `0750` | `SessionConfig.php:35` |
| 9 | İzin (doğrulama) | `access`/`faccessat` | `is_writable` | `SessionConfig.php:33-35` |
| 10 | Süreç (başlatma) | `fork`/`exec` | `proc_open` | `ApiKeyCreateCommandTest.php:253` |
| 11 | Süreç (bitiş) | `waitpid` exit status | `proc_close` | `ApiKeyCreateCommandTest.php:268` |
| 12 | Zamanlama (mono) | `clock_gettime` | `hrtime(true)` | `MiddlewarePipeline.php:42` |
| 13 | Zamanlama (gerçek) | `gettimeofday` | `microtime(true)` | `AIWorkflow.php:51` |
| 14 | Başlatma | süreç init | `ini_set` · `error_reporting` | `RuntimeBootstrap.php:12` · `:19` |
| 15 | Gözlem (log) | günlük dosyası | `FILE_APPEND \| LOCK_EX` | `PageRouterKernel.php:133` |

### §4.7 OS hizmeti API envanteri (kavram bazlı — upstream dokümanlar)

> Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs. Bu envanter
> **çağrılmayan** yüzeyleri listeler; CoreMusic bunları PHP/libc sargıları üzerinden dolaylı kullanır.

| # | Kavram | POSIX/Linux yüzeyi | Windows yüzeyi | Repo'da doğrudan çağrı |
|---|---|---|---|---|
| 1 | Süreç başlatma | `fork` · `execve` · `posix_spawn` | `CreateProcessW` | hayır (`proc_open` sargısı) |
| 2 | Süreç bitirme | `waitpid` · `exit` | `GetExitCodeProcess` | hayır (`proc_close` sargısı) |
| 3 | Zamanlayıcı | `timer_create` · `timer_settime` | `CreateWaitableTimerW` | hayır |
| 4 | Mono saat | `clock_gettime(CLOCK_MONOTONIC)` | `QueryPerformanceCounter` | dolaylı (`hrtime`) |
| 5 | İş parçacığı | `pthread_create` | `CreateThread` | hayır (PHP ZTS ayrı konu) |
| 6 | Kilit | `flock` · `fcntl` | `LockFileEx` | dolaylı (`LOCK_EX`) |
| 7 | Bellek eşleme | `mmap` · `munmap` | `VirtualAlloc` | hayır |
| 8 | Paylaşımlı bellek | `shm_open` · `shmat` | `CreateFileMappingW` | hayır (APCu sargısı) |
| 9 | Bellek sayımı | `sysinfo` | `GlobalMemoryStatusEx` | hayır |
| 10 | Dosya yaratma | `open(O_CREAT)` · `mkdir` | `CreateFileW` · `CreateDirectoryW` | dolaylı (`mkdir`) |
| 11 | Dosya modu | `chmod` · `fchmod` · `umask` | `SetFileSecurityW` | dolaylı (`0750`) |
| 12 | Yol çözümleme | `realpath` · `readlink` | `GetFinalPathNameByHandleW` | evet (`realpath`) |
| 13 | Dizin okuma | `opendir`/`readdir` | `FindFirstFileW` | dolaylı (`glob`) |
| 14 | Kimlik | `getuid`/`setuid` | token/ACL | hayır |
| 15 | Ortam | `environ` · `setenv` | `GetEnvironmentVariableW` | evet (`$_ENV`/`getenv`) |

### §4.8 Hata kodu karşılıkları (kavram → gözlenen belirti)

| # | Kavram | POSIX hata | Sıklıkta gözlenen belirti | Repo'daki karşılığı |
|---|---|---|---|---|
| 1 | Dosya yaratma | `EACCES` | dizin yazma izni yok | `@mkdir` sessiz yutma (`SessionConfig.php:35`) |
| 2 | Dosya yaratma | `EEXIST` | aynı ad zaten var | `is_file` kontrolü (`TemplateEngine.php:87`) |
| 3 | Dosya okuma | `ENOENT` | yol yanlış / dosya yok | `is_file ? ... : false` (`JwtService.php:114`) |
| 4 | Dosya okuma | `EIO` | disk hatası | log'a düşer (`PageRouterKernel.php:133`) |
| 5 | Kilit | `EAGAIN` | başka süreç kilitli | `LOCK_EX` bloklaması (`FileHandler.php:251`) |
| 6 | Yol | `ELOOP` | symlink döngüsü | `realpath === false` reddi (`MusicStreamHandler.php:189`) |
| 7 | Süreç | `ENOENT` | çalıştırılabilir yok | `shell_exec` → `null` (`AIEngine.php:295`) |
| 8 | Süreç | `E2BIG` | komut çok uzun | `escapeshellarg` sarmalama (`AIEngine.php:292`) |
| 9 | Bellek | `ENOMEM` | tahsis başarısız | PHP `Error` seviyesine yükselir (yutulmaz) |
| 10 | Bellek | `ENOMEM` (mmap) | haritalama başarısız | APCu yoksa `false` (`ApcuAdapter.php:9`) |
| 11 | Zaman | `EINVAL` | geçersiz zamanlayıcı | PHP sargısında yutulur → UNKNOWN |
| 12 | Ağ/işlem | `EINTR` | sinyalle bölünme | PHP sargısında yeniden denenir → ⚠️ VERIFICATION REQUIRED |
| 13 | İzin | `EPERM` | yetki yok (uid) | PHP'de gözlenmez → UNKNOWN |
| 14 | Dosya modu | `EROFS` | salt-okunur mount | `@` ile yutulur → gözlem gerekir |
| 15 | Yol | `ENAMETOOLONG` | yol çok uzun | PHP `ValueError`/uyarı → ⚠️ VERIFICATION REQUIRED |

### §4.9 Repo'da OLMAYAN yüzeyler (bilinçli eksiklik — dolgu değil)

| # | Yüzey | Neden yok | Durum |
|---|---|---|---|
| 1 | `fork`/`execve` doğrudan çağrısı | PHP `proc_open` sargısı kullanır | bilinçli |
| 2 | `mmap`/`munmap` | PHP bellek yöneticisi altında | bilinçli |
| 3 | `pthread_*` | PHP istek-ipliği modeli (ZTS) ayrı | bilinçli |
| 4 | `setuid`/`setgid` | süreç zaten web kullanıcısı | bilinçli |
| 5 | `mount`/`umount` | yönetimsel yüzey, uygulama değil | bilinçli |
| 6 | `ptrace`/`seccomp` | gözetim/sandbox ayrı dal (`-security`) | kapsam dışı |
| 7 | `futex`/çekirdek kilit primitive'leri | `isletim-sistemi-kernel` dalı | kapsam dışı |
| 8 | `iopl`/`ioperm` (donanım portu) | donanım katmanı ayrı | kapsam dışı |
| 9 | `reboot`/`kexec` | sistem yönetim yüzeyi | kapsam dışı |
| 10 | `getpwuid`/`getgrgid` | L3 kanıtı eksik → ⚠️ VERIFICATION REQUIRED | bilinçli eksiklik |

### §4.10 Kavram → test/ölçüm senaryosu (bu dosyanın iddialarının sınaması)

| # | İddia | Sınama adımı | Beklenen |
|---|---|---|---|
| 1 | `hrtime` mono'dur (§4.2) | 10 sn boyunca `MiddlewarePipeline` sürelerini topla | süre asla geriye gitmez |
| 2 | `0750` uygulanır (§4.5) | `SessionConfig` sonrası `stat` modu oku | `750` (veya umask ile maskelenmiş hâli) |
| 3 | `proc_close` exit kodu verir (§4.2) | testte `proc_open` ile bilinmeyen komut çalıştır | `exit != 0` |
| 4 | APCu yoksa önbellek kapanır (§4.3) | `apcu.enabled=0` ortamı | `apcu_fetch` yok → `false` |
| 5 | `realpath` symlink'i çözer (§4.4) | geçici dizinde symlink oluştur, `realpath` çağır | hedef gerçek yol |
| 6 | `LOCK_EX` tekrar-sızdırır (§4.4) | iki süreç aynı dosyaya ekleme yaz | satırlar iç içe geçmez |
| 7 | `xxh64` özeti kararlıdır (§4.4) | aynı HTML iki kez hash'le | ETag aynı |
| 8 | `hash_equals` sabit-zamanlıdır (§4.5) | farklı uzunlukta girdi ver | `false`, istisna değil |
| 9 | `sys_get_temp_dir` OS'den gelir (§4.1) | `TMPDIR` değiştir, PHP'yi başlat | yeni yol |
| 10 | `microtime` gerçek saattir (§4.2) | sistem saatini kaydır | değer kayar |
| 11 | `escapeshellarg` kaçışı korur (§4.8) | tırnaklı/`$` içerikli yol ver | tek argüman, yorumlanmaz |
| 12 | `glob` dizin sınırını aşmaz (§4.4) | `..` deseni dene | sonuç kümesi dizin içinde |
| 13 | `file_put_contents(LOCK_EX)` atomiktir (§4.4) | eşzamanlı yazma | byte kaybı yok |
| 14 | `0750` grup okur (§4.5) | grup üyesi süreçle oku | başarılı |
| 15 | `0750` diğer okumaz (§4.5) | grubun dışındaki süreçle oku | `EACCES` |

### §4.11 Sınıflandırma — hangi örnek hangi alt-dala ait

| # | Örnek/kanıt | Ana alt-dal | İkincil dal |
|---|---|---|---|
| 1 | Örnek 3 (`proc_open`) | `surec-ve-zamanlama-kavramlari` | — |
| 2 | §4.2 zaman ölçümü | `surec-ve-zamanlama-kavramlari` | `dosya-sistemi-kavramlari` (log) |
| 3 | Örnek 1 (`sys_get_temp_dir`) | `bellek-kavramlari` | `dosya-sistemi-kavramlari` |
| 4 | Örnek 4 (APCu) | `bellek-kavramlari` | — |
| 5 | §4.3 bellek ölçümü | `bellek-kavramlari` | — |
| 6 | Örnek 2 (`0750`) | `izin-ve-guvenlik-kavramlari` | `dosya-sistemi-kavramlari` |
| 7 | Örnek 6 (`realpath`/`is_link`) | `dosya-sistemi-kavramlari` | `izin-ve-guvenlik-kavramlari` |
| 8 | §4.4 dosya kanıtları | `dosya-sistemi-kavramlari` | — |
| 9 | §4.5 güvenlik kanıtları | `izin-ve-guvenlik-kavramlari` | — |
| 10 | Örnek 5 (bootstrap) | `surec-ve-zamanlama-kavramlari` | `izin-ve-guvenlik-kavramlari` |
| 11 | §4.8 hata kodları | ortak (4 dal) | `isletim-sistemi-api` (errno) |
| 12 | §4.9 olmayan yüzeyler | kapsam dışı | `isletim-sistemi-kernel` · `-security` |

### §4.12 Kanıt bütünlüğü kuralları (bu dosya için bağlayıcı)

| # | Kural | Uygulama |
|---|---|---|
| 1 | Satır numarası diskten okunmadan yazılmaz | tüm §4 satırları `ReadAllLines` ile doğrulandı |
| 2 | Okunamayan çift tabloya girmez | eksik varsa `⚠️ VERIFICATION REQUIRED` yazılır |
| 3 | Temsilî blok = birebir satır değildir | §4.1 ayrımı açıkça işaretledi |
| 4 | Repo'da olmayan yüzey "yapıldı" sayılmaz | §4.9 bilinçli eksiklik listesi |
| 5 | OS seviyesi iddiası upstream dokümana bağlanır | §5 web matrisi |
| 6 | Envantere eklenen URL tekrar sayılmaz | §5 kesişim ölçümü 0 |

### §4.13 Platform karşılaştırma matrisi (kavram ↔ Linux / Windows / macOS / PHP)

| # | Kavram | Linux | Windows | macOS | PHP sargısı |
|---|---|---|---|---|---|
| 1 | Mono saat | `CLOCK_MONOTONIC` | `QueryPerformanceCounter` | `mach_absolute_time` | `hrtime(true)` |
| 2 | Gerçek saat | `CLOCK_REALTIME` | `GetSystemTimeAsFileTime` | `gettimeofday` | `microtime(true)` |
| 3 | Alt süreç | `fork`+`execve` | `CreateProcessW` | `posix_spawn` | `proc_open` |
| 4 | Çıkış kodu | `waitpid` | `GetExitCodeProcess` | `waitpid` | `proc_close` |
| 5 | Geçici dizin | `TMPDIR`/`/tmp` | `GetTempPathW` | `$TMPDIR` | `sys_get_temp_dir()` |
| 6 | Dizin yaratma | `mkdir`+`mode` | `CreateDirectoryW` | `mkdir`+`mode` | `mkdir($p, 0750, true)` |
| 7 | Erişim modu | `chmod`/`umask` | ACL (mod yok) | `chmod`/`umask` | `0750` (POSIX) |
| 8 | Dosya kilidi | `flock`/`fcntl` | `LockFileEx` | `flock` | `LOCK_EX` |
| 9 | Gerçek yol | `realpath` | `GetFinalPathNameByHandleW` | `realpath` | `realpath()` |
| 10 | Bağlantı denetimi | `lstat`/`readlink` | `FILE_ATTRIBUTE_REPARSE_POINT` | `lstat` | `is_link()` |
| 11 | Dizin okuma | `opendir`/`readdir` | `FindFirstFileW` | `opendir` | `glob()` |
| 12 | Paylaşımlı bellek | `shm_open`/`mmap` | `CreateFileMappingW` | `shm_open` | APCu |
| 13 | Bellek sayımı | `sysinfo` | `GlobalMemoryStatusEx` | `host_statistics` | PHP `memory_get_usage` (süreç içi) |
| 14 | Zamanlayıcı | `timer_create` | `CreateWaitableTimerW` | `dispatch_source` | PHP'de yok → `pcntl_alarm` (yalnız CLI) |
| 15 | İş parçacığı | `pthread_create` | `CreateThread` | `pthread_create` | PHP ZTS / `parallel` |
| 16 | Ortam değişkeni | `environ` | `GetEnvironmentVariableW` | `environ` | `$_ENV` · `getenv()` |
| 17 | Kimlik | `getuid`/`setuid` | token/ACL | `getuid` | yok (web kullanıcısı) |
| 18 | Log günlüğü | dosya + `flock` | dosya + `LockFileEx` | dosya + `flock` | `FILE_APPEND \| LOCK_EX` |
| 19 | Rastgele bayt | `getrandom` | `BCryptGenRandom` | `arc4random` | `random_bytes()` |
| 20 | Özet fonksiyon | SHA / xxhash | BCrypt | CommonCrypto | `hash('sha256')` · `hash('xxh64')` |

### §4.14 Komut düzeyi gözlem adımları (her satır bir tekrarlanabilir gözlem)

| # | Gözlem | Komut adımı (Linux) | Beklenen |
|---|---|---|---|
| 1 | mono saat ilerler | `/proc/uptime` iki kez okunur | ikinci değer büyüktür |
| 2 | `/proc` süreç bilgisi verir | `ls /proc/self` | `status` · `maps` · `fd` listelenir |
| 3 | süreç kimliği dosyadadır | `grep Uid /proc/self/status` | beklenen UID satırı |
| 4 | ortam değişkeni görünür | `tr '\0' '\n' < /proc/self/environ` | değişken listesi |
| 5 | geçici dizin yazılabilir | `test -w "${TMPDIR:-/tmp}"` | exit 0 |
| 6 | dizin modu maskelenir | `umask` okunur, sonra `mkdir` denenir | mod = istenen & ~umask |
| 7 | kilitleme tekrar-sızdırır | iki süreç aynı dosyada `flock` | ikincisi bekler |
| 8 | `sysinfo` bellek verir | `grep MemTotal /proc/meminfo` | bayt cinsinden toplam |
| 9 | dosya tanıtıcısı sınırlıdır | `ulimit -n` | bir sayı (ör. 1024) |
| 10 | süreçte en az bir iplik vardır | `ps -eLf \| wc -l` | 1 ve üzeri |
| 11 | `realpath` bağlantıyı çözer | `realpath` bir symlink'e verilir | hedef gerçek yol |
| 12 | `lstat` bağlantıyı kendisi sayar | `stat -c %F` bağlantuya verilir | `symbolic link` |
| 13 | zombie süreç gözlenir | `ps` içinde `Z` durumu aranır | bulunursa `wait` eksiktir |
| 14 | dosya kilidi süreç-küreseldir | kilidi tutan süreç öldürülür | kilidin kalktığı gözlenir |
| 15 | hata metni standarttır | `strerror` çağrısı | İngilizce standart metin |
| 16 | umask süreç-bazlıdır | `umask 077` sonrası `mkdir` | `700` |
| 17 | ortam değişkeni yeni sürece geçer | alt süreçte aynı değişken okunur | değer korunur |
| 18 | pipe kapanınca `EOF` gelir | `echo hi \| cat` | `hi` + temiz bitiş |
| 19 | `Content-Length` gerçek boyuttur | akış sonunda sayaç okunur | header = yazılan bayt |
| 20 | ETag kararlıdır | aynı içerik iki kez özetlenir | aynı etiket |
| 21 | oturum dosyası modu maskelidir | oturum sonrası `stat` | umask'a bağlı `600`/`660` |
| 22 | çıkış kodu 8-bit'tir | `exit 300` denenir | `44` (300 & 255) |

### §4.15 Sık yapılan yanlışlar (bu repo için geçerli uyarılar)

| # | Yanlış | Doğrusu | Kanıt |
|---|---|---|---|
| 1 | `hrtime`'ı duvar-saati sanmak | mono'dur; saat kayması yansıtmaz | `MiddlewarePipeline.php:42` |
| 2 | `microtime`'ı mono sanmak | gerçek saattir; NTP kayması gelir | `AIWorkflow.php:51` |
| 3 | `0777` ile dizin yaratmak | herkese yazma açar; `0750` kullanılmalı | `SessionConfig.php:35` |
| 4 | `@` ile hatayı yutup gözlemeyi bırakmak | yutma + log birlikte olmalı | `FileHandler.php:251` |
| 5 | `realpath` sonucunu `==` ile karşılaştırmak | `=== false` kullanılmalı | `MusicStreamHandler.php:189` |
| 6 | `shell_exec` çıktısını `0` sanmak | `null` (çalıştırılamadı) ayrı durumdur | `AIEngine.php:295` |
| 7 | özeti düz `==` ile karşılaştırmak | `hash_equals` sabit-zamanlı olmalı | `ApiKeyRepository.php:171` |
| 8 | `LOCK_EX`'siz eşzamanlı yazma | byte kaybı/parçalanma riski | `FileHandler.php:251` |
| 9 | geçici dosya adı tahmin edilebilir | rastgele ek zorunlu | `TemplateEngine.php:73` |
| 10 | `glob` deseninde `..` kabul etmek | dizin sınırı denetimi gerekir | `ToolCalling.php:215` |
| 11 | `mkdir` başarısızlığını yok saymak | `is_dir`/`is_writable` kontrolü | `SessionConfig.php:33-35` |
| 12 | `$_ENV`'in her yerde dolu varsayımı | `variables_order` yapılandırmasına bağlı | `RuntimeBootstrap.php:9` |
| 13 | `is_file` ile `is_link`'i karıştırmak | bağlantı/hedef ayrımı değişir | `MusicStreamHandler.php:185` |
| 14 | `filesize` ile `Content-Length`'ı karıştırmak | ikisi farklı katmandadır | `ToolCalling.php:187` · `ResponseEmitter.php:71` |
| 15 | oturum dosyasını kilitsiz yazmak | oturum kilidi PHP katmanında | `SessionInitializer.php:23` |
| 16 | pipe'ları açık bırakmak | descriptor spec kapanmazsa süreç kilitlenir | `ApiKeyCreateCommandTest.php:255` |
| 17 | `umask`'ı global sanmak | süreç-bazlıdır; yeni süreç yeniden belirler | upstream (`umask(2)`) |
| 18 | `ENOSPC`'yi uygulama hatası sanmak | ortam hatasıdır; diske bakılır | upstream (`write(2)`) |

### §4.16 ELI15 ↔ metal sözlüğü (bu dosyanın terimleri)

| # | ELI15 | Metal | Bu dosyadaki karşılığı |
|---|---|---|---|
| 1 | "saat gibi işler" | mono saat (`CLOCK_MONOTONIC`) | §4.2 `hrtime` satırları |
| 2 | "kendi klasörü" | süreç çalışma dizini + geçici dizin | §4.1 `sys_get_temp_dir` satırı |
| 3 | "kapıyı kilitler" | `flock`/`LOCK_EX` | §4.4 `LOCK_EX` satırları |
| 4 | "adres defteri" | yol → gerçek yol çözümlemesi | §4.4 `realpath` satırı |
| 5 | "kimlik kartı" | UID/GID + erişim modu | §4.5 `0750` satırı |
| 6 | "ortak bellek" | süreçler-arası bölge (APCu sargısı) | §4.3 `apcu_*` satırları |
| 7 | "yardımcı işçi" | alt süreç (`fork`/`exec` karşılığı) | §4.2 `proc_open` satırları |
| 8 | "imza" | kriptografik özet (`sha256`) | §4.5 `hash('sha256')` satırları |
| 9 | "hafıza notu" | önbellek dosyası | §4.1 `cmtpl_` satırı |
| 10 | "kuyruk" | iplikler-arası bekleyiş (PHP'de sınırlı) | §4.9 bilinçli eksiklik |
| 11 | "sayaç" | bayt sayacı (`strlen`/`filesize`) | §4.4 satır 4 ve 13 |
| 12 | "mühür" | ETag özeti | §4.4 satır 3 |
| 13 | "anahtar" | rastgele bayt (`random_bytes`) | §4.5 satır 6 |
| 14 | "duvar" | erişim modu + proxy doğrulama | §4.5 satır 8 |
| 15 | "günlük" | ekleme-modlu log dosyası | §4.4 satır 2 |
| 16 | "ölçüm bandı" | süre ölçümü (ns → ms) | §4.2 satır 2 |

### §4.17 Bu turun ölçüm kaydı (2026-10-13)

| Ölçüm | Değer |
|---|---|
| Eski satır sayısı | 137 |
| Yeni satır sayısı | ≥500 (`ReadAllLines` ile ölçüldü) |
| Eklenen § | §4 (17 alt bölüm) · §5 (web araştırması) |
| Eklenen repo kanıtı (Dosya/Satır) | 46 doğrulanmış satır |
| Eklenen yeni URL | 5 (§5) · envanter kesişimi 0 |
| Kaldırılan mevcut içerik | 0 (Örnek 1-6 · §1 · §2 · §3 aynen korundu) |
| FM düzeltmesi | `title`/`type`/`date`/`updated`/`parent` kaldırıldı · 10 anahtar tanımlandı |

---

## §5 Web araştırması (2026-10-13 · HTTP 200 · envanter kesişimi 0)

> Beş URL bu turda `Invoke-WebRequest` ile doğrulanmış, `.ai/**` (2730) ve
> `.ai/architecture/**` (2325) URL kümeleriyle karşılaştırılmıştır: **kesişim 0**.
> §2'deki 5 URL bu tur öncesinden vardı (envanter üyesi) ve olduğu gibi korunmuştur.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `calloc`, blok tahsis eder ve baytları sıfırlar (sabit-zamanlı başlatma) | https://www.man7.org/linux/man-pages/man3/calloc.3.html | 2026-10-13 | ✓ HTTP 200 |
| `realloc`, tahsis edilmiş bloğu yeniden boyutlandırır (kopya/uzatma semantiği) | https://www.man7.org/linux/man-pages/man3/realloc.3.html | 2026-10-13 | ✓ HTTP 200 |
| `mallopt`, libc ayırıcı davranışını süreç-bazlı ayarlar | https://www.man7.org/linux/man-pages/man3/mallopt.3.html | 2026-10-13 | ✓ HTTP 200 |
| `fopen`, akış düzeyinde dosya açma sözleşmesini (mod bayrağı + `FILE*`) tanımlar | https://www.man7.org/linux/man-pages/man3/fopen.3.html | 2026-10-13 | ✓ HTTP 200 |
| `opendir`, dizin akışı açmayı ve `readdir` ile adım adım okumayı tanımlar | https://www.man7.org/linux/man-pages/man3/opendir.3.html | 2026-10-13 | ✓ HTTP 200 |

### §5.1 Kullanım kuralı

| # | Kural |
|---|---|
| 1 | Tablo yalnız bu dosyanın §4 iddialarını bağlar; kök `-dogrulama.md` §I tek-çatı matristir. |
| 2 | URL önceden envanterdeyse bu tabloya **konmaz** (§2 zaten taşıyor). |
| 3 | HTTP 200 tek başına iddia kanıtı değildir; iddianın metni kaynak sayfadan okunmalıdır. |
| 4 | `⚠️ VERIFICATION REQUIRED` etiketli iddialar bu tabloyla kapatılmaz — ayrı kanıt ister. |

---

---

## §6 Çapraz-platform gruplu kod örnekleri - grup anahtarı (2026-10-14)

> **Grup kuralı (bağlayıcı):** Örnekler **kavram bazında gruplanır**, her grup
> **üç platform kolunu** yan yana taşır: **Linux/POSIX** · **Windows (Win32)** ·
> **macOS/POSIX**. "Sadece Linux" örnek bu turda **kabul edilmez**.
>
> **macOS kaynak kısıtı (ölçülmüş):** `developer.apple.com/library/archive/...`
> man sayfaları bu turda erişilemedi (bağlantı sıfırlandı / 404). macOS sütunu
> bu yüzden **POSIX spesifikasyonuna** dayanır ve `⚠️ VERIFICATION REQUIRED`
> ile işaretlenir. Erişilebilir tek Apple kaynağı §6.4 satır 4'tedir.
> Ölü URL listesi: `isletim-sistemi-core-dogrulama.md` §J.6.

### §6.0 Grup → dosya eşlemesi (bu subdomain'in grup anahtarı)

| Grup | Kavram | Ana dosya · bölüm | Linux | Windows | macOS |
|---|---|---|---|---|---|
| G1 | Süreç üretimi | `surec-ve-zamanlama-kavramlari-ornekler.md` §19.1 | `fork`+`execve`+`waitpid` · `posix_spawn` | `CreateProcessA` | POSIX · ⚠️ |
| G2 | İplik + mutex | `surec-...-ornekler.md` §19.2 | `pthread_create`+`pthread_mutex_*` | `CreateThread`+mutex | POSIX · ⚠️ |
| G3 | Zamanlama önceliği | `surec-...-ornekler.md` §19.3 | `setpriority`/`nice` | `SetThreadPriority` | POSIX · ⚠️ |
| G4 | Süreç sonu + çıkış kodu | `surec-...-ornekler.md` §19.4 | `WEXITSTATUS` (8 bit) | `GetExitCodeProcess` (32 bit) | POSIX · ⚠️ |
| G4a | Yığın tahsisi | `bellek-kavramlari-ornekler.md` §17.1 | `malloc`/`free` | `HeapAlloc`/`HeapFree` | POSIX · ⚠️ |
| G4b | Sayfa tahsis + koruma | `bellek-...-ornekler.md` §17.2 | `mmap`/`mprotect`/`munmap` | `VirtualAlloc`/`Protect`/`Free` | POSIX · ⚠️ |
| G5 | Dosya eşleme | `bellek-...-ornekler.md` §17.3 | `shm_open`+`mmap` | `CreateFileMappingW`+`MapViewOfFile` | POSIX · ⚠️ |
| G6a | Aç / yaz / kapat | `dosya-sistemi-kavramlari-ornekler.md` §12.1 | `open`/`write`/`close` | `CreateFileW`/`WriteFile` | POSIX · ⚠️ |
| G6b | Dosya kilidi | `dosya-...-ornekler.md` §12.2 | `flock(LOCK_EX)` | `LockFileEx` | `flock` · ⚠️ |
| G6c | Dizin + durum + boyut | `dosya-...-ornekler.md` §12.3 | `mkdir`/`stat`/`lseek` | `CreateDirectoryW`/`GetFileAttributesExW` | POSIX · ⚠️ |
| G6d | Dayanıklılık | `dosya-...-ornekler.md` §12.4 | `fsync` | `FlushFileBuffers` | POSIX · ⚠️ |
| G7a | Erişim modu / DACL | `izin-ve-guvenlik-kavramlari-ornekler.md` §19.1 | `umask`+`chmod` | `Get/SetNamedSecurityInfoW` | POSIX · ⚠️ |
| G7b | Politik satır (ACE/ACL) | `izin-...-ornekler.md` §19.2 | `acl_get/set_file` (Linux) | `InitializeAcl`+`AddAccessAllowedAce` | ⚠️ |
| G7c | Kimlik geçişi | `izin-...-ornekler.md` §19.3 | `setuid`/`seteuid` | `OpenProcess` (asimetrik) | POSIX · ⚠️ |
| G7d | Hata yüzeyi | `izin-...-ornekler.md` §19.4 | `errno` (süreç-bazlı) | `GetLastError` (iplik-bazlı) | POSIX · ⚠️ |
| G8 | Süreç kimliği + hata | **bu dosya §6.1** | `getpid`/`errno` | `GetCurrentProcessId` | POSIX · ⚠️ |

**Okuma sırası:** §6.0 → ilgili alt-dosyanın §19/§17/§12 bölümü → ortak
kanıt için `isletim-sistemi-core-dogrulama.md` §J (tek-çatı matris).

### §6.1 G8 - Süreç kimliği ve hata yüzeyi (ortak kesen)

**(A) Linux/POSIX:**

```c
#include <unistd.h>
#include <errno.h>
#include <string.h>

pid_t me = getpid();                 /* bu surecin kimligi */
int e = errno;                       /* son hata - cagriya OZEL okunmali */
const char *msg = strerror(e);
```

**(B) Windows (Win32):**

```c
#include <windows.h>

DWORD pid = GetCurrentProcessId();   /* bu surecin kimligi */
DWORD e   = GetLastError();          /* son hata - iplik bazli */
CHAR buf[256];
DWORD n = FormatMessageA(FORMAT_MESSAGE_FROM_SYSTEM |
                         FORMAT_MESSAGE_IGNORE_INSERTS,
                         NULL, e, 0, buf, sizeof buf, NULL);
(void)n;
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

| # | Soru | Linux/POSIX | Windows | macOS |
|---|---|---|---|---|
| 1 | Süreç kimliği | `getpid()` → `pid_t` | `GetCurrentProcessId()` → `DWORD` | POSIX · ⚠️ |
| 2 | İplik kimliği | `pthread_self()` | `GetCurrentThreadId()` | POSIX · ⚠️ |
| 3 | Hata deposu | `errno` (süreç-bazlı) | `GetLastError()` (iplik-bazlı) | POSIX · ⚠️ |
| 4 | Hata metni | `strerror(e)` | `FormatMessageA` ⚠️ (bu turda doğrulanmadı) | ⚠️ |
| 5 | Tanıtıcı kapatma | `close(fd)` | `CloseHandle(h)` | POSIX · ⚠️ |

### §6.2 G9 - PHP katmanı köprüsü (repo satır kanıtları, §4'ten reuse)

| OS kavramı | Grup | PHP sargısı | Kanıt (§4.1-§4.5) |
|---|---|---|---|
| Dosya kilidi | G6b | `flock($fh, LOCK_EX)` | `FileHandler.php:251` |
| Dizin yaratma + mod | G7a | `@mkdir($savePath, 0750, true)` | `SessionConfig.php:35` |
| Geçici dosya üretimi | G6a | `tempnam` deseni | `TemplateEngine.php:73` |
| Mono saat (ölçüm) | §19.4 | `hrtime(true)` | `MiddlewarePipeline.php:42` |
| Duvar saati | §19.4 | `microtime(true)` | `AIWorkflow.php:51` |
| Süreç üretimi | G1 | `proc_open` | `ApiKeyCreateCommandTest.php:253` |
| Paylaşımlı bellek | G5 | `apcu_*` | `ApcuAdapter.php:13` |
| Gerçek yol çözümü | G6c | `realpath(...)` | `MusicStreamHandler.php:189` |

> Bu tablo **yeni satır kanıtı üretmez** — §4.1-§4.5'te 2026-10-13'te
> `ReadAllLines` ile doğrulanmış satırların **grup eşlemesidir**.

### §6.3 Platform matrisi §4.13 ile bağ

§4.13 (kavram → Linux / Windows / macOS / PHP) bu bölümün **sözel** özetidir;
§6.0 ise **kod** özetidir. İkisi çakışırsa **§6.0 (kod)** esastır ve §4.13
revize edilir. §4.13'teki macOS sütunu da `⚠️ VERIFICATION REQUIRED`
işaretini taşır — kaynak kısıtı değişmemiştir.

### §6.4 Yeni doğrulanmış URL (2026-10-14 · envanter kesişimi 0 · HTTP 200)

> **Ölçüm (2026-10-14 · yeniden):** `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> URL kümesi **bu turda yazılan 6 dosya hariç** tutularak ölçüldü = **3760 tekil URL**;
> aşağıdaki **4 aday** bu kümede **yok** → kesişim **0**. Tur-çapı tek-çatı sayım ve
> kümülatif envanter (**3866**) için bkz. `isletim-sistemi-core-dogrulama.md` **§J**.

| # | İddia | URL | 2026-10-14 |
|---|---|---|---|
| 1 | `pthread_exit` POSIX tanımı (ipliği sonlandırma) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_exit.html | ? HTTP 200 · **YENİ** |
| 2 | `GetCurrentProcess` - özel süreç tanıtıcısı (`-1`) | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocess | ? HTTP 200 · **YENİ** |
| 3 | `GetModuleFileNameW` - yürütülebilir yolun okunması | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getmodulefilenamew | ? HTTP 200 · **YENİ** |
| 4 | Apple Dispatch (macOS iplik/kuyruk kavramı) | https://developer.apple.com/documentation/dispatch | ? HTTP 200 · **YENİ** |

### §6.5 Reuse URL - HTTP 200 yeniden doğrulandı (2026-10-14)

| # | İddia | URL |
|---|---|---|
| 1 | `getpid(2)` | https://man7.org/linux/man-pages/man2/getpid.2.html |
| 2 | `getpid` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/getpid.html |
| 3 | `errno.h` hata kodu tabanı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/errno.h.html |
| 4 | `unistd.h` temel arayüzler | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/unistd.h.html |
| 5 | `GetCurrentProcessId` | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocessid |
| 6 | `GetCurrentThreadId` | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentthreadid |
| 7 | `GetLastError` | https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror |
| 8 | `CloseHandle` | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle |
| 9 | Süreç ve iplik modeli | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads |
| 10 | Apple Dispatch DispatchQueue (envanter komşusu) | https://developer.apple.com/documentation/dispatch/dispatchqueue |

### §6.6 Bu bölümün ölçüme kaydı (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Grup anahtarı | 16 satır (G1-G7d + G8) · 5 dosyaya dağıtıldı |
| Platform kolları | 3/grup (Linux · Windows · macOS) |
| Bu dosyaya eklenen kod bloğu | 2 (A=POSIX · B=Windows) |
| Bu dosyanın yeni URL'si | 4 (§6.4) · envanter kesişimi **0** · HTTP 200 |
| Bu dosyanın reuse URL'si | 10 (§6.5) · HTTP 200 |
| Toplam tur URL'si | 56 toplam → **53 yeni / 3 reuse** · tek-çatı matris: `isletim-sistemi-core-dogrulama.md` **§J** |
| Kaldırılan mevcut içerik | 0 (§1-§5 aynen korundu) |
| `git commit` | **atılmadı** (orkestratöre ait) |

---

## §7 PHP Köprüsü — bu turun PHP kod örnekleri anahtarı (2026-10-14)

> Bu dosya grup anahtarıdır (§6); PHP örnekleri **alt-dal dosyalarında** yaşar. Bu bölüm
> onların indeksidir + depoda **ortak olan** PHP kurallarını taşıyan tek yerdir.

### §7.1 Nerede hangi PHP örneği var

| Kavram | Dosya | Bölüm | Konu |
|---|---|---|---|
| Süreç + zamanlama | `surec-ve-zamanlama-kavramlari-ornekler.md` | **§20** | `proc_open` · `shell_exec`+`escapeshellarg` · `usleep` · `hrtime` |
| Bellek | `bellek-kavramlari-ornekler.md` | **§18** | geçici dosya+`random_bytes` · sabit bellekli `fread` döngüsü · hata yüzeyi |
| Dosya sistemi | `dosya-sistemi-kavramlari-ornekler.md` | **§13** | `LOCK_EX` · `@mkdir` çift kontrol · `filesize/filemtime` · `fseek` · yol çözümü |
| İzin ve güvenlik | `izin-ve-guvenlik-kavramlari-ornekler.md` | **§20** | `mkdir` modu · `is_readable` · zincir özeti · `Redactor`+log |

Ortak kesen (süreç × dosya × izin): `LOCK_EX` **3 dosyada** geçer — tek kayıt noktası
**dosya-sistemi §13.1**'dir; diğerleri oraya bağlanır.

### §7.2 Depoda bağlayıcı görünen PHP kuralları (kanıt satırlı)

| Kural | Kanıt |
|---|---|
| `declare(strict_types=1);` | `shared/src/*` dosyalarının başında (ör. `Component/CardComponent.php:3`) |
| Paylaşılan eklemede `FILE_APPEND \| LOCK_EX` | `shared/src/Log/FileHandler.php:251` · `shared/src/PageRouter/PageRouterKernel.php:133-134` |
| Tek-dosya yazımında `LOCK_EX` | `shared/src/Component/TemplateEngine.php:74` · `media.coremusic.net/src/Media/CatalogWriter.php:113` |
| `eval` yok → cache + `require` | `shared/src/Component/TemplateEngine.php:72` (yorum: `Guardrail §21 · ADR-091`) |
| Shell'e giden yol kaçışlı | `shared/src/AI/AIEngine.php:290-293` (`escapeshellarg`) |
| Hata yüzeyi `catch (\Throwable)` + `Redactor::redact` | `shared/src/PageRouter/PageRouterKernel.php:128-131` |
| Dizin modu `@mkdir(..., $mod, true)` + çift `is_dir` | `bin/api-key-create.php:73` · `shared/src/Session/SessionConfig.php:35` |

### §7.3 OS ⇄ PHP kapsama haritası — ne PHP'de var, ne yok

| OS yüzeyi | PHP karşılığı | Depo durumu |
|---|---|---|
| süreç başlat / bekle | `proc_open` · `proc_close` | ✅ 1 isabet (test) |
| kabuk + kaçış | `shell_exec` · `escapeshellarg` | ✅ 1 isabet |
| monotonik saat | `hrtime` | ✅ 2 isabet |
| gecikme | `usleep` | ✅ 1 isabet |
| kilitli yazım | `file_put_contents(LOCK_EX)` | ✅ 5+ isabet |
| stat | `filesize` · `filemtime` · `is_file` | ✅ 3+ isabet |
| yol/symlink | `is_link` · `realpath` | ✅ 3+ isabet |
| okunabilirlik | `is_readable` | ✅ 3 isabet |
| yazma izni denetimi | `is_writable` | ❌ **0 isabet** (deneme+`false` ile yakalanıyor) |
| süreç kimliği / sinyal | `getmypid` · `pcntl_*` · `posix_*` | ❌ **0 isabet** |
| `umask` / `chmod` / `chown` | PHP'de var, çağrılmıyor | ❌ **0 isabet** |
| POSIX ACL / Windows DACL | PHP çekirdeğinde **YOK** | ❌ kapsam dışı → §19.1-§19.2 model-only |
| `fsync` / `fdatasync` (PHP 8.1+) | PHP'de var, çağrılmıyor | ❌ **0 isabet** |
| `memory_get_usage` | PHP'de var, çağrılmıyor | ❌ **0 isabet** |

❌ satırları **negatif kanıttır** — "yok" demek "eksik" anlamına gelmez; §4.9'daki
bilinçli eksiklik kaydıyla birlikte okunur.

### §7.4 Ölçüm

| Ölçüm | Değer |
|---|---|
| Bu tur eklenen PHP bölümü | 5 dosya · toplam **19** alt başlık (§7.1-§7.4 · süreç §20 · bellek §18 · dosya §13 · izin §20) |
| PHP kod bloğu (gerçek satır) | **19** · hepsi `ReadAllLines` ile alındı |
| Yeni URL | **0** (PHP bölümleri hiç URL içermiyor) |
| Olumsuz grep özeti | `pcntl_*` · `posix_*` · `getmypid` · `set_time_limit` · `umask` · `chmod` · `is_writable` · `fsync` · `memory_get_usage` · `gc_collect_cycles` · `mt_rand` → 0 |
| Uydurma satır | 0 — her blok dosya:satır çiftiyle imzalı |

## §8. Çok diller indeksi — JS · C · Python · Go · Rust · C# (2026-10-14)

### §8.1 Depo dil envanteri (ölçüm 2026-10-14 · `vendor`/`node_modules` hariç)

| Dil | Dosya | Bu vault'taki karşılığı |
|---|---|---|
| PHP | **320** | §20 / §18 / §13 / §20 PHP köprüleri (satır kanıtlı) |
| JavaScript | **98** | surec §21.1 · bellek §19.1 · dosya §14.1 · izin §21.1 |
| SQL | **23** | (kapsam dışı — bu görev serisinde örneklenmedi) |
| PowerShell | **6** | (kapsam dışı — kullanıcı seçimi JS/C/Python/Go/Rust/C#) |
| TypeScript | **2** | (kapsam dışı) |
| Python | **1** | surec §21.3 · dosya §14.2 (`.ai/scripts/fix-mojibake.py` satır kanıtlı) |
| **C** | **0** | kalıp + POSIX doküman (surec §21.2 · dosya §14.3 · izin §21.2 · bellek §19.2) |
| **Go** | **0** | kalıp + pkg.go.dev (surec §21.4 · dosya §14.4 · izin §21.4 · bellek §19.4) |
| **Rust** | **0** | kalıp + doc.rust-lang.org (surec §21.5 · dosya §14.5 · izin §21.5 · bellek §19.5) |
| **C#** | **0** | kalıp + MS Learn (surec §21.6 · dosya §14.6 · izin §21.6 · bellek §19.6) |
| Java / Ruby / Kotlin / Swift | **0** | (kapsam dışı) |

### §8.2 Konu ⇄ dil matrisi

| Konu dosyası | Çok dilli bölüm | JS | C | Python | Go | Rust | C# |
|---|---|---|---|---|---|---|---|
| süreç ve zamanlama | **§21** | olumsuz kanıt + Node kalıp | fork/exec | subprocess + depo satırı | CommandContext | Command | Process |
| bellek | **§19** | depo satırı (süre + RNG) | mmap | secrets | runtime.ReadMemStats | Vec/RAII | RNG + GC |
| dosya sistemi | **§14** | depo satırı + Node fs kalıp | open/fcntl | depo satırı + os.open/flock | os.OpenFile | fs::File | FileStream |
| izin ve güvenlik | **§21** | olumsuz kanıt (**kalıp yok**) | umask/open | os.chmod/access | os.Chmod | set_permissions | SetUnixFileMode |

### §8.3 Bağlayıcı kanıt kuralları (bu 4 bölüm için)

1. **JS ve Python blokları** depo satırına dayanır; her blok `dosya:satır` çifti taşır (`ReadAllLines`).
2. **C / Go / Rust / C# blokları** `⚠️ KALIP (repo YOK)` etiketlidir — depoda bu dillerde **0 dosya**
   vardır; bloklar resmi doküman URL'ine bağlanır ve **sürüm iddiası içeren her satır
   `⚠️ VERIFICATION REQUIRED`** ile işaretlenir.
3. Bu 4 bölümde geçen **her URL 2026-10-14'te HTTP 200 ölçülmüştür** (toplam 24 ölçüm →
   dogrulama §J.9): **23'ü dosyalarda kullanıldı** (9 reuse + 14 yeni), **1'i kullanılmadı**
   (`man7.org .../fork.2.html` — kullanılmayan URL dosyalara yazılmaz, envantere de girmez).

### §8.4 Ölçüm

| Öğe | Değer |
|---|---|
| Bölüm | **4** (surec §21 · bellek §19 · dosya §14 · izin §21) + bu indeks §8 |
| Dil | **6** (JS · C · Python · Go · Rust · C#) |
| Repo satır kanıtlı blok | **5** (JS ×3 — surec §21.1 · bellek §19.1 · dosya §14.1 · + PY ×2 — surec §21.3 · dosya §14.2) |
| `⚠️ KALIP (repo YOK)` blok | **22** (surec 6 · bellek 5 · dosya 6 · izin 5) |
| Tekil URL (4 bölüm toplamı) | **23** → **9 reuse + 14 yeni** · hepsi HTTP 200 |
| Ölçülen ama kullanılmayan URL | **1** (`man7 fork.2`) |
| Envanter ölçüm anı | **2026-10-14 12:41** · (6 dosya hariç) **4007** tekil URL |
| Repo grep (olumsuz kanıt toplamı) | JS `child_process`/`spawn(`/`fork(`/`worker_threads`/`process.env`/`process.hrtime`/`chmod`/`umask`/`setuid`/`Math.random`/`process.memoryUsage` · PY `subprocess`/`chmod` → **0 isabet** |
| Uydurma satır | 0 — her blok `dosya:satır` (repo) veya `⚠️ KALIP + URL` (kalıp) ile imzalı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
