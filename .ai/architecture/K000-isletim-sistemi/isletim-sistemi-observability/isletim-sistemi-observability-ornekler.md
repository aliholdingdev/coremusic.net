---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/isletim-sistemi-observability-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children: []
depends_on:
  - isletim-sistemi-core
evidence:
  - shared/src/PageRouter/StructuredLogger.php:9
  - shared/src/PageRouter/StructuredLogger.php:47
  - shared/src/PageRouter/StructuredLogger.php:69
  - shared/src/PageRouter/StructuredLogger.php:72
  - shared/src/Log/FileHandler.php:40
  - shared/src/Log/FileHandler.php:220
  - shared/src/Log/FileHandler.php:228
  - shared/src/Log/FileHandler.php:251
  - shared/src/Log/Redactor.php:31
  - shared/src/Log/Redactor.php:65
  - shared/src/PageRouter/PageRouterKernel.php:87
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
  - shared/src/Security/UuidV7.php:19
  - shared/src/Log/LoggerFactory.php:13
validation: "2026-10-14 — §27 PHP error_log → 3 OS köprüsü eklendi (syslog/journald · Event Log/ReportEventW · os_log/Logger) + §28 uygulama süresi vs OS latency ayrımı; §27.1–27.3 kod örnekleri (bash/powershell/swift) repo'da YOK, tarif amaçlı — 14 repo kanıt satırı 2026-10-13'te Read ile doğrulandı; OS gözlemlenebilirlik aracı grep'i 0 isabet (olumsuz kanıt) | 2026-10-13 — 14 repo kanıt satırı Read (offset/limit) ile doğrulandı; her \`Dosya:Satır\` referansı gerçek dosya satırıyla eşleşiyor"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
group: kod
---

# isletim-sistemi-observability — Örnekler (gerçek repo kodu)

> **Bu dosyada:** CoreMusic deposundan **gerçek, diskte doğrulanmış** kod satırları;
> her biri `Dosya:… / Satır:…` ile işaretlidir. Amaç: repo'nun bugünkü
> gözlemlenebilirlik çıktısını OS katmanı perspektifinden göstermek.

## §0 — Örnek okuma kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Her örnek `Dosya:Satır` ile gelir | satır Read ile okundu |
| 2 | Kod blokları dosyadan kopyalanmıştır | uydurma satır yok |
| 3 | OS yorumu ayrı sütun/paragraftadır | kod ile yorum karıştırılmaz |
| 4 | Uygulama katmanı örneği ≠ OS katmanı | ayrım §C.1 matrisindedir |

## §1 — Örnek 1: JSON yapısal log üretimi (uygulama katmanı — sınır örneği)

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 61–72 (Read ile doğrulandı)

```php
$record = array_merge([
    'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
    'level'     => $level,
    'module'    => $module,
    'event'     => $event,
    'traceId'   => $this->traceId,
], $extra);

$payload = '[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur.
error_log(Redactor::redact($payload));
```

| # | Gözlem | OS yorumu |
|---|---|---|
| 1 | `error_log()` çağrısı | PHP'nin SAPI error_log hedefine yazar — syslog/journald/Event Log **değildir** |
| 2 | `timestamp` ATOM biçiminde | ISO-8601; OS günlük zaman damgalarından bağımsızdır |
| 3 | `traceId` satır içinde | uygulama korelasyon kimliği; OS PID/kimliği değildir |
| 4 | `[CoreMusic]` öneki | metin ayrıştırma için önek; OS kanal etiketi değil |

**Sonuç:** bu satır OS log kanalına değil, web sunucusu/uygulama error log dosyasına
yazılır. OS gözlemlenebilirlik katmanı burada **başlamaz**.

## §2 — Örnek 2: seviye tabloları — 4 kademelik uygulama vs 8 kademelik PSR-3

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 9

```php
private const LEVELS = ['debug' => 0, 'info' => 1, 'warn' => 2, 'error' => 3];
```

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 40–49

```php
private const LEVELS = [
    'debug'     => 0,
    'info'      => 1,
    'notice'    => 2,
    'warning'   => 3,
    'error'     => 4,
    'critical'  => 5,
    'alert'     => 6,
    'emergency' => 7,
];
```

| # | Karşılaştırma | StructuredLogger | FileHandler | syslog (RFC) |
|---|---|---|---|---|
| 1 | Kademe sayısı | 4 | 8 (PSR-3) | 8 (0–7) |
| 2 | En düşük | debug (0) | debug (0) | emerg (0) |
| 3 | En yüksek | error (3) | emergency (7) | debug (7) |
| 4 | Uyarı adı | `warn` | `warning` | `warning` (5) |
| 5 | Eşleme | dönüşüm gerekir | birebir | — |

**OS ilişkisi:** syslog severity 0–7 (`log/log-surucu.md` §3); uygulama tabloları bu
hiyerarşiyle **kademelenir ama birebir değildir** — `warn`↔`warning` ad farkı vardır.

## §3 — Örnek 3: metin log satırı kurulumu (dosya logu)

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 207–228 (özet: 220 ve 228)

```php
$timestamp = date('Y-m-d H:i:s');
$uri = $_SERVER['REQUEST_URI'] ?? '-';
$method = $_SERVER['REQUEST_METHOD'] ?? '-';
$ip = $_SERVER['REMOTE_ADDR'] ?? '-';
$trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';

$line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

$line = Redactor::redact($line);

$this->appendToFile('coremusic_php_errors.log', $line);
```

| # | Alan | OS log karşılığı | Ayrım |
|---|---|---|---|
| 1 | `timestamp` | syslog TIMESTAMP | biçim farklı, anlam aynı |
| 2 | `logLevel` | severity etiketi | uygulama adları |
| 3 | `method uri` | — (istek bağlamı) | yalnız uygulama katmanı |
| 4 | `ip` | client addr | OS logunda olabilir (auth) |
| 5 | `trace` | — | uygulama korelasyonu |
| 6 | `coremusic_php_errors.log` | — | dosya kanalı, syslog değil |

## §4 — Örnek 4: ekleme kilitleme — dosya I/O maliyeti

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 248–252

```php
private function appendToFile(string $filename, string $content): void
{
    $filepath = $this->logDir . '/' . $filename;
    @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
}
```

| # | Gözlem | Ölçüm etkisi |
|---|---|---|
| 1 | `FILE_APPEND` | atomik ekleme (POSIX'te); Windows'ta davranış farklıdır |
| 2 | `LOCK_EX` | dosya kilidi — yüksek trafikte beklemeye yol açabilir |
| 3 | `@` | hata bastırma — disk dolu/gizli hata yutulur |
| 4 | log dizini repo kökü | `LoggerFactory.php:32` → `dirname(__DIR__, 3)` |

**OS ilişkisi:** log hacmi ve disk I/O yükü ölçümü `log/log-ornekler.md` §6'dadır;
bu örnek yalnız uygulama I/O maliyetinin kanıtıdır.

## §5 — Örnek 5: istek traceId üretimi ve dağıtımı

**Dosya:** `shared/src/PageRouter/PageRouterKernel.php` · **Satır:** 87–91

```php
$traceId = self::generateTraceId();
$this->logger->setTraceId($traceId);
// P5: FileHandler satırları da aynı traceId ile damgalansın —
// StructuredLogger (JSON) ile dosya log'ları tek istekte birleşir.
\CoreMusic\Log\FileHandler::setTraceId($traceId);
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | tek traceId, iki logger | uygulama içi korelasyon |
| 2 | statik alan (`FileHandler::$traceId`) | istek yaşam döngüsüne bağlı |
| 3 | OS katmanı yok | ftrace/ETW correlation'ı bu kodda başlamaz |

## §6 — Örnek 6: merkezi redaction — tek maskeleme noktası

**Dosya:** `shared/src/Log/Redactor.php` · **Satır:** 31 ve 65–93 (özet)

```php
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

public static function redact(string $line): string
{
    // 1) Anahtar-adı maskesi (key=value / key: value / "key":"value")
    $redacted = preg_replace_callback(self::KEY_VALUE_PATTERN, ...);
    // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);
    // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
    $withEmail = preg_replace_callback(self::EMAIL_PATTERN, ...);
    return $withEmail ?? ($replaced ?? $redacted);
}
```

| # | Mask | Örnek |
|---|---|---|
| 1 | anahtar-değer | `password=x` → `password=[REDACTED]` |
| 2 | hash | 64-char hex → `[REDACTED]` |
| 3 | e-posta | `a@b.com` → `***@b.com` |
| 4 | UUID | 8-4-4-4-12 **korunur** |

**OS ilişkisi:** OS log kanalları (kern.log, Event Log) bu redaction'dan geçmez —
PII yalnız uygulama katmanında maskelenir (kapsam §C.3).

## §7 — Örnek 7: middleware süre ölçümü — uygulama içi latency

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 42–48

```php
$start = hrtime(true);

$response = $middleware->handle($request, $next);

$durationMs = (hrtime(true) - $start) / 1_000_000;
if ($this->logger !== null) {
    $this->logger->timing('MiddlewarePipeline', $name, $durationMs);
}
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | `hrtime(true)` | monotonic saat — `clock_gettime(CLOCK_MONOTONIC)` karşılığı (M-W10) |
| 2 | ms'ye bölme | uygulama içi süre |
| 3 | `timing()` logu | OS latency metriği **değildir** — syscall/kernel latency `trace/trace-surucu.md` §3 |

## §8 — Örnek 8: AI workflow/tool süre ölçümü (microtime)

**Dosya:** `shared/src/AI/AIWorkflow.php` · **Satır:** 51

```php
$startTime = microtime(true);
```

**Dosya:** `shared/src/AI/ToolCalling.php` · **Satır:** 85

```php
$startTime = microtime(true);
```

| # | Gözlem | Not |
|---|---|---|
| 1 | `microtime(true)` | duyarlı saat (wall clock); `hrtime`'dan farklı |
| 2 | iki farklı API kullanımı | repo içinde tutarlılık farkı — yalnız raporlanır |

## §9 — Örnek 9: UuidV7 milisaniye kaynağı

**Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 19

```php
$ms = (int)(microtime(true) * 1000);
```

| # | Gözlem | Not |
|---|---|---|
| 1 | wall-clock milisaniye | UUIDv7 zaman sıralı kimlik üretir |
| 2 | OS saati bağımlılığı | saat kayması UUID sırasını bozabilir — risk notu |

## §10 — Örnek 10: LoggerFactory tekil logger

**Dosya:** `shared/src/Log/LoggerFactory.php` · **Satır:** 13 ve 29–33

```php
private static ?FileHandler $logger = null;

private static function resolveLogDir(): string
{
    // Proje kök dizinine yaz (C:\www\coremusic.net\)
    return dirname(__DIR__, 3);
}
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | singleton | tek logger örneği — log yazıcısı süreç-boyu tekildir |
| 2 | log dizini repo kökü | log dosyaları OS servis kanalına değil, uygulama dizinine gider |

## §11 — Olumsuz örnek: OS gözlemlenebilirlik araçlarının kullanılmaması

| # | Arama | Beklenen kullanım | İsabet |
|---|---|---|---|
| 1 | `ftrace` | Linux trace | 0 |
| 2 | `perf_event` | perf syscall | 0 |
| 3 | `EventWrite` | ETW | 0 |
| 4 | `syslog(` | syslog çağrısı | 0 |
| 5 | `proc/self` | /proc okuma | 0 |
| 6 | `OpenTelemetry` | OTel | 0 |

**Yorum:** gözlemlenebilirlik bugün **yalnız uygulama katmanında**; OS katmanı
tanımsızdır (O1 [UNKNOWN]) — "eksik" değil, hedef mimaride henüz karar verilmemiştir.

## §12 — İyi/kötü uygulama karşılaştırması (bu repo üzerinden)

| # | Konu | İyi (repo'da var) | Kötü (repo'da yok, kaçınılmaz) |
|---|---|---|---|
| 1 | redaction | tek nokta `Redactor::redact()` | her logger'ın kendi maskesi |
| 2 | korelasyon | tek traceId iki logger'a dağıtılır | her dosya kendi ID'si |
| 3 | seviye standardı | PSR-3 8 kademe (`FileHandler`) | serbest metin seviye |
| 4 | kilitleme | `LOCK_EX` ile atomik ekleme | kilipsiz paralel yazım |
| 5 | OS kanalı | — (yok) | syslog/journald/ETW'ye çift yazım |

## §13 — Repo kanıt sayımı

| # | Dosya | Doğrulanan satır |
|---|---|---|
| 1 | `StructuredLogger.php` | 9, 47, 61–72 |
| 2 | `FileHandler.php` | 40–49, 207–228, 248–252 |
| 3 | `Redactor.php` | 31, 65–93 |
| 4 | `PageRouterKernel.php` | 87–91 |
| 5 | `MiddlewarePipeline.php` | 42–48 |
| 6 | `AIWorkflow.php` | 51 |
| 7 | `ToolCalling.php` | 85 |
| 8 | `UuidV7.php` | 19 |
| 9 | `LoggerFactory.php` | 13, 29–33 |

## §14 — Log dosya envanteri (repo'nun yazdığı gerçek kanallar)

**Kaynak:** `shared/src/Log/FileHandler.php` doküman bloğu (satır 19–26) + `write()` (228–245).

| # | Dosya | Ne zaman yazılır | Seviye kapsamı |
|---|---|---|---|
| 1 | `coremusic_php_errors.log` | her zaman | tüm seviyeler |
| 2 | `coremusic_php_warnings.log` | yalnız debug modda | warning-ailesi |
| 3 | `coremusic_php_info.log` | yalnız debug modda | info/debug |
| 4 | `coremusic_php_kernel_debug.log` | ayrı yazıcı (FileHandler değil) | PageRouterKernel |
| 5 | `coremusic_php_security.log` | her zaman (prod dâhil) | [AUTH]/[SECURITY] olayları |

| # | Gözlem | OS yorumu |
|---|---|---|
| 1 | 5 dosya = 5 uygulama kanalı | hiçbiri syslog/journald/Event Log değildir |
| 2 | rotasyon kodda yok | logrotate/Event Log retention bu kanallara uygulanmaz |
| 3 | disk konumu repo kökü | `LoggerFactory.php:32` |

## §15 — Seviye eşleme matrisi (uygulama ↔ syslog severity)

| # | PSR-3 (`FileHandler`) | `StructuredLogger` | syslog severity (RFC) | Karşılık |
|---|---|---|---|---|
| 1 | emergency (7) | — | emerg (0) | birebir |
| 2 | alert (6) | — | alert (1) | birebir |
| 3 | critical (5) | — | crit (2) | birebir |
| 4 | error (4) | error (3) | err (3) | ad birebir, kademe farklı |
| 5 | warning (3) | warn (2) | warning (5) | ad birebir, kademe farklı |
| 6 | notice (2) | — | notice (4) | yalnız PSR-3'te |
| 7 | info (1) | info (1) | info (6) | ad birebir, kademe farklı |
| 8 | debug (0) | debug (0) | debug (7) | ad birebir, kademe farklı |

**Sonuç:** iki uygulama tablosu syslog hiyerarşisiyle **örtüşür ama birebir değildir**;
OS kanalına taşınacaksa açık eşleme tablosu gerekir (surucu §3).

## §16 — TraceId akış adımları (repo üzerinden)

| # | Adım | Dosya:Satır | Eylem |
|---|---|---|---|
| 1 | üretim | `PageRouterKernel.php:87` | `generateTraceId()` |
| 2 | JSON logger'a aktarım | `PageRouterKernel.php:88` | `setTraceId($traceId)` |
| 3 | dosya logger'a aktarım | `PageRouterKernel.php:91` | `FileHandler::setTraceId($traceId)` |
| 4 | JSON satırına gömme | `StructuredLogger.php:66` | `'traceId' => $this->traceId` |
| 5 | metin satırına gömme | `FileHandler.php:213` | `' [trace=' . self::$traceId . ']'` |
| 6 | redaction | `Redactor.php:65` | UUID korunur → traceId hayatta kalır |

## §17 — Redaction regex matrisi (Repo kanıtı)

| # | Desen | Dosya:Satır | Kapsam |
|---|---|---|---|
| 1 | `KEY_VALUE_PATTERN` | `Redactor.php:40` | key=value / key: value / "key":"value" + auth-scheme |
| 2 | `LONG_HASH_PATTERN` | `Redactor.php:49` | 64-char hex/base64 (lookaround ile sınır) |
| 3 | `EMAIL_PATTERN` | `Redactor.php:56` | `***@domain` maskesi |

| # | Kural | Sonuç |
|---|---|---|
| 1 | maskeleme yalnız YAZILACAK satırı etkiler | geçmiş loglara dokunulmaz |
| 2 | UUID korunur | traceId korelasyonu bozulmaz |
| 3 | tek redaction noktası | tüm yazıcılar `Redactor`'dan geçer |

## §18 — Repo gözlemlenebilirlik yüzeyi envanteri (geniş)

| # | Yüzey | Dosya | Durum |
|---|---|---|---|
| 1 | JSON log | `shared/src/PageRouter/StructuredLogger.php` | var |
| 2 | metin log | `shared/src/Log/FileHandler.php` | var |
| 3 | redaction | `shared/src/Log/Redactor.php` | var |
| 4 | logger factory | `shared/src/Log/LoggerFactory.php` | var |
| 5 | traceId üretimi | `shared/src/PageRouter/PageRouterKernel.php` | var |
| 6 | middleware timing | `shared/src/Middleware/MiddlewarePipeline.php` | var |
| 7 | AI timing | `shared/src/AI/AIWorkflow.php` | var |
| 8 | tool timing | `shared/src/AI/ToolCalling.php` | var |
| 9 | UUIDv7 zaman | `shared/src/Security/UuidV7.php` | var |
| 10 | syslog/journald/ETW | — | **yok** (grep 0) |
| 11 | perf/ftrace/BPF | — | **yok** (grep 0) |
| 12 | /proc okuma | — | **yok** (grep 0) |

## §19 — Örnek dosya sonu kontrolü

| # | Kontrol | Değer |
|---|---|---|
| 1 | Örnek sayısı | 10 pozitif + 1 olumsuz |
| 2 | Doğrulanan repo dosyası | 9 |
| 3 | Doğrulanan satır | 21+ |
| 4 | Uydurma satır numarası | 0 |
| 5 | OS yorumu her örnekte var | evet |

## §20 — Uygulama içi süre ölçümü ↔ OS latency ayrım matrisi

| # | Ölçüm | Üreten kod | Birimi | OS latency midir? |
|---|---|---|---|---|
| 1 | middleware süresi | `MiddlewarePipeline.php:42` (`hrtime`) | ms | hayır — uygulama içi |
| 2 | AI workflow süresi | `AIWorkflow.php:51` (`microtime`) | s | hayır |
| 3 | tool çağrısı süresi | `ToolCalling.php:85` (`microtime`) | s | hayır |
| 4 | UUIDv7 milisaniye | `UuidV7.php:19` (`microtime`) | ms | hayır — kimlik üretimi |
| 5 | syscall latency | repo'da yok | — | OS katmanı (`trace/trace-surucu.md` §3) |
| 6 | kesinti gecikmesi | repo'da yok | — | OS katmanı (`trace/trace-surucu.md` §3) |

| # | Saat API'si | Karşılık | Monotonik? |
|---|---|---|---|
| 1 | `hrtime(true)` | `clock_gettime(CLOCK_MONOTONIC)` (M-W10) | evet |
| 2 | `microtime(true)` | `clock_gettime(CLOCK_REALTIME)` | hayır — saat kayması riski |
| 3 | `DateTimeImmutable` (log timestamp) | realtime | hayır |

## §21 — Redaction öncesi/sonrası örnek matrisi (kural bazlı)

| # | Girdi (satır) | Kural | Çıktı |
|---|---|---|---|
| 1 | `password=abc123` | `KEY_VALUE_PATTERN` | `password=[REDACTED]` |
| 2 | `"csrf_token":"xyz"` | tırnaklı değer | `"csrf_token":"[REDACTED]"` |
| 3 | `Authorization: Bearer tok` | auth-scheme | `Authorization: [REDACTED]` |
| 4 | 64-char hex dize | `LONG_HASH_PATTERN` | `[REDACTED]` |
| 5 | `a.b@example.com` | `EMAIL_PATTERN` | `***@example.com` |
| 6 | UUID 8-4-4-4-12 | koruma kuralı | **değişmez** |

> Bu matris kural tabanlıdır; satırlar `Redactor.php` dosya içi kural tanımından
> (satır 31, 40, 49, 56) türetilmiştir — üretim logu okunarak **değil**.

## §22 — Log kanalı ↔ K017 sınırı (örnekler bölümü)

| # | Örnek | K017'ye giden | Bu dalda kalan |
|---|---|---|---|
| 1 | `coremusic_php_errors.log` okuma/parslama | uygulama log metriği | kanal tanımı |
| 2 | `middleware_timing` log satırı | latensi paneli/uyarısı | log üretim kuralı |
| 3 | `securityEvent` stream'i | SIEM korelasyonu | kanal ayrımı |
| 4 | traceId stitching | OTel span korelasyonu | kimlik akış tanımı |

## §23 — Örnek dosyalar arası çapraz referans

| # | Örnek | İlgili dosya | İlgili § |
|---|---|---|---|
| 1 | seviye tabloları | `log/log-surucu.md` | §3 severity standardı |
| 2 | satır kurulumu | `log/log-anayasa.md` | §1 günlük mimarisi |
| 3 | ekleme kilitleme | `log/log-ornekler.md` | §6 ölçüm |
| 4 | hrtime/microtime | `metrik/metrik-anayasa.md` | §1–§2 |
| 5 | traceId | `trace/trace-surucu.md` | §3 latency/korelasyon |
| 6 | redaction | kök kapsam | §C.3 sınır |

## §24 — OS kanal eşlemesi (repo logu → aday OS kanalı, karar bekliyor)

| # | Repo kanalı | Linux adayı | Windows adayı | Karar durumu |
|---|---|---|---|---|
| 1 | `coremusic_php_errors.log` | syslog /dev/log | Event Log uygulama kanalı | [UNKNOWN] (O1) |
| 2 | `coremusic_php_security.log` | audit / secure kanalı | Security log kanalı | [UNKNOWN] — politika security dalında |
| 3 | `coremusic_php_kernel_debug.log` | — | — | yalnız uygulama |
| 4 | StructuredLogger JSON | journald structured | ETW uygulama provider | [UNKNOWN] (O3) |

| # | Not | Açıklama |
|---|---|---|
| 1 | eşleme yalnız tekliftir | repo'da bu yönlendirme kodu yok (grep 0) |
| 2 | OS kanalı PII taşımaz | redaction yalnız uygulama katmanında |
| 3 | çift yazım maliyeti | syslog+dosya = iki I/O; O2 kararı |

## §25 — Örnek sonu — kanıt bütünlüğü tablosu

| # | Dosya | Örnek §'leri | Doğrulama |
|---|---|---|---|
| 1 | `StructuredLogger.php` | §1, §2, §3 | Read (offset/limit) |
| 2 | `FileHandler.php` | §2, §3, §4, §14 | Read |
| 3 | `Redactor.php` | §6, §17, §21 | Read |
| 4 | `PageRouterKernel.php` | §5, §16 | Read |
| 5 | `MiddlewarePipeline.php` | §7, §20 | Read |
| 6 | `AIWorkflow.php` | §8, §20 | Read |
| 7 | `ToolCalling.php` | §8, §20 | Read |
| 8 | `UuidV7.php` | §9, §20 | Read |
| 9 | `LoggerFactory.php` | §10, §14 | Read |

| # | Bütünlük kuralı | Sonuç |
|---|---|---|
| 1 | her `Dosya:Satır` gerçek satır | 0 uydurma |
| 2 | kod blokları dosyadan kopya | evet |
| 3 | olumsuz kanıt grep ile | 8 desen, 0 isabet |
| 4 | uydurma eşik/sayı | 0 |

## §26 — Örnek sonu — okuma rehberi (dosya → hangi örnek)

| # | Okumak istenen | Gidilecek örnek § |
|---|---|---|
| 1 | log satırı nasıl kuruluyor? | §1, §3 |
| 2 | seviye adları nereden geliyor? | §2, §15 |
| 3 | hassas veri nasıl maskeleniyor? | §6, §21 |
| 4 | traceId nasıl dağıtılıyor? | §5, §16 |
| 5 | süre nasıl ölçülüyor? | §7, §8, §20 |
| 6 | log nereye yazılıyor? | §10, §14 |
| 7 | OS katmanı neden yok? | §11 |
| 8 | K017 sınırı nerede? | §22, §24 |

| # | Son kontrol | Değer |
|---|---|---|
| 1 | Toplam örnek/örnek matrisi | 10 pozitif + 1 olumsuz + 8 § genişletme |
| 2 | Doğrulanan dosya | 9 (§25) |
| 3 | Uydurma satır | 0 |

## §27 — OS uygulama köprüsü (PHP `error_log` → OS kanalı eşlemesi, 3 OS)

> §24'teki köprü yalnızca teklif idiyse, bu bölüm **eşlemenin kendisini** kod
> örneğiyle gösterir. Hiçbiri repoda çalışmıyor (§25 olumsuz kanıt); buradaki
> bloklar **tarif** amaçlıdır ve `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.
> Kanıt: `error_log()` PHP çekirdeğidir, OS kanalına yönlendirme `php.ini`
> `error_log` değeriyle belirlenir; bu dalın URL kanıtı §1–§3'tedir.

### §27.1 — Linux: `error_log` → syslog (`/dev/log`) → journald

```ini
; php.ini — Linux hedefi
error_log = /dev/log          ; syslog socket (L-W2: syslog(2))
log_errors = On
error_reporting = E_ALL
```

```bash
# PHP'nin syslog'a yazdığını oku (L-W8: dmesg / L-W7: journalctl)
journalctl -t php-fpm -p warning --since today
# Uygulama kendi mesajını syslog'a yazar (L-W17: logger(1))
logger -p local0.err -t coremusic "payment gateway timeout"
```

### §27.2 — Windows: `error_log` → Event Log (`ReportEventW` / `eventcreate`)

```ini
; php.ini — Windows hedefi
error_log = C:\logs\coremusic_php_errors.log
log_errors = On
```

```powershell
# Olay kaydı yaz (L-W22: eventcreate)
eventcreate /T ERROR /ID 100 /L APPLICATION /SO "CoreMusic" /D "DB connection pool exhausted"

# Kayıtları oku (L-W20: wevtutil / L-W21: Get-WinEvent)
wevtutil qe Application /c:10 /rd:true /f:text
Get-WinEvent -LogName Application -MaxEvents 20 |
    Where-Object { $_.ProviderName -eq 'CoreMusic' }
```

```c
/* Win32 kayıt API'si (L-W23: ReportEventW) — tarif, repoda yok */
ReportEventW(hEventLog, EVENTLOG_ERROR_TYPE, 0, 100, NULL,
             1, 0, lpStrings, NULL);
```

### §27.3 — macOS: `error_log` → unified logging (`os_log` / `Logger`)

```ini
; php.ini — macOS hedefi
error_log = /var/log/php_errors.log
log_errors = On
```

```bash
# Unified log oku (L-W25: log(1))
log show --last 1h --predicate 'process == "php-fpm"' --style compact
# Kendi kategori emit et (L-W26/L-W27: Logger subsystem/category)
log emit --subsystem net.coremusic --category payment --type error "gateway timeout"
```

```swift
// Swift kayıt kaynağı tanımı (L-W27: Logger.init(subsystem:category:))
import os
let logger = Logger(subsystem: "net.coremusic", category: "payment")
logger.error("gateway timeout")
```

### §27.4 — Eşleme matrisi (köprü → OS → kaynak)

| # | PHP yüzeyi | Linux | Windows | macOS |
|---|---|---|---|---|
| 1 | `error_log()` varsayılan | `/dev/log` syslog (L-W2) | dosya (Windows'ta syslog yok) | dosya (syslog devri) |
| 2 | dosya hedefi | `/var/log/...` + logrotate (L-W6) | `C:\logs\...` + Event Log | `/var/log/...` + unified log |
| 3 | okuma komutu | `journalctl` (L-W7) | `wevtutil`/`Get-WinEvent` (L-W20/21) | `log show` (L-W25) |
| 4 | kayıt API'si | `syslog(3)` (L-W1) | `ReportEventW` (L-W23) | `os_log`/`Logger` (L-W12r/26) |
| 5 | audit kanalı | `auditd` (L-W18) | Security log kanalı | unified log (özel category) |
| 6 | repo karşılığı | **yok** (§25 N4) | **yok** (§25 N3) | **yok** (§25 N3) |

> **Karar durumu:** Eşleme yalnızca **tariftir**; O1 çözülmeden CoreMusic bu
> kanallardan **hiçbirine yazmaz**. `error_log()` repoda yalnızca dosyaya yazar
> (`StructuredLogger.php:72` — §12.1), OS kanalı yoktur.

## §28 — OS metrik köprüsü (uygulama süresi → OS latency)

> §7.3 kuralı: `hrtime`/`microtime` ölçümleri **uygulama içi** süredir; OS
> latency metriği değildir. Bu bölüm, iki ölçümün **nasıl ayrı okunacağını**
> tarif eder.

| # | Ölçüm | Uygulama (repo) | OS (bu dal) | Ayrım |
|---|---|---|---|---|
| 1 | middleware süresi | `hrtime(true)` (D15–D16) | — | uygulama |
| 2 | AI workflow süresi | `microtime(true)` (D17) | — | uygulama |
| 3 | CPU kullanıcısı | — | `perf stat` / `Get-Counter` / `powermetrics` | OS |
| 4 | G/Ç beklemesi | — | `iostat` / PDH / `vm_stat` | OS |
| 5 | kesinti/gerçek zamanlı | — | `osnoise tracer` / ETW / `dtrace` | OS |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
