---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/log/log-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/log
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - shared/src/PageRouter/StructuredLogger.php:9
  - shared/src/PageRouter/StructuredLogger.php:55
  - shared/src/PageRouter/StructuredLogger.php:72
  - shared/src/Log/FileHandler.php:40
  - shared/src/Log/FileHandler.php:220
  - shared/src/Log/FileHandler.php:228
  - shared/src/Log/FileHandler.php:234
  - shared/src/Log/FileHandler.php:251
  - shared/src/Log/Redactor.php:31
  - shared/src/Log/Redactor.php:65
  - shared/src/PageRouter/PageRouterKernel.php:87
  - shared/src/Log/LoggerFactory.php:32
  - .github/workflows/ci.yml:104
validation: "2026-10-14 — §21 OS örnek turu eklendi (§21.1 bash: journalctl/dmesg/logger/ausearch · §21.2 powershell: Get-WinEvent/wevtutil/eventcreate · §21.3 swift: os_log/Logger/Console.app + §21.4 eşleme matrisi, kanıt L-W18–L-W27 ayna dipnotlu; repo sınırı: bu bloklar tarif amaçlı, repo'da 0 isabet) | 2026-10-13 — 13 repo kanıt satırı Read (offset/limit) ile doğrulandı; her Dosya:Satır referansı gerçek dosya satırıyla eşleşiyor · OS log aracı grep'i (ftrace, syslog(, journald, proc/self) 0 isabet · log hacmi ölçümü ölçülmüyor, yalnız yöntem tarif ediliyor; 2. tur: L-W15 (rsyslog.conf.5), L-W16 (systemd.journal-fields.7) ve L-W17 (logger.1) webfetch ile HTTP 200 doğrulandı; logger.8 ve syslog.conf.5 HTTP 404 ile reddedildi"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: kod
---

# log-ornekler — Repo Günlük Yüzeyi (gerçek disk kanıtı)

## §0 ELI15

Bu dosya CoreMusic deposunun **bugünkü** log yüzeyini gösterir: iki logger
(JSON ve metin), tek redaksiyon noktası, istek izi (traceId) dağıtımı ve CI'ın
log davranışı. Her kod bloğu `Dosya: / Satır:` ile diskten okunmuştur; tahmini
satır numarası yoktur. Amaç: repo'nun OS log kanalına **yazmadığını** kanıtla-
maktır — "eksiklik" değil, hedef mimaride **tanımsızlık** (O1 [UNKNOWN]).

## §1 Örnek okuma kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Her örnek `Dosya:Satır` ile gelir | satır Read ile okundu (2026-10-13) |
| 2 | Kod blokları dosyadan kopyalanmıştır | uydurma satır yok |
| 3 | OS yorumu ayrı tablo sütunundadır | kod ile yorum karıştırılmaz |
| 4 | Ölçülmeyen sayı yazılmaz | log hacmi/GB bilgisi `[UNKNOWN]` |

## §2 Örnek 1 — StructuredLogger: tek çağrıda tek JSON satırı

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 55–73

```php
public function log(string $level, string $module, string $event, array $extra = []): void
{
    if (!$this->shouldLog($level)) {
        return;
    }

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
}
```

| # | Gözlem | OS yorumu |
|---|---|---|
| 1 | `error_log()` (satır 72) | PHP SAPI hedefine yazar — syslog/journald/Event Log **değildir** |
| 2 | `timestamp` ATOM (satır 62) | ISO-8601; OS zaman damgasından bağımsız |
| 3 | `traceId` satır içinde (satır 66) | uygulama korelasyon kimliği; OS kimliği değil |
| 4 | `[CoreMusic]` öneki (satır 69) | metin ayrıştırma öneki; OS kanal etiketi değil |
| 5 | `shouldLog` kapısı (satır 57) | süreç içi şiddet filtresi — setlogmask değil |

**Sonuç:** satır web sunucusu/uygulama error log dosyasına düşer; OS günlüğü
`log/log-anayasa.md` §1'deki üç kaynağın hiçbirine girmez.

## §3 Örnek 2 — seviye tabloları: 4 kademe (JSON) vs 8 kademe (PSR-3)

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

| # | Karşılaştırma | StructuredLogger | FileHandler | syslog severity |
|---|---|---|---|---|
| 1 | Kademe sayısı | 4 | 8 (PSR-3) | 8 (0–7) |
| 2 | En düşük | debug (0) | debug (0) | emerg (0) |
| 3 | Uyarı adı | `warn` | `warning` | warning (5) |
| 4 | Eşleme | dönüşüm gerekir | birebir | `log/log-surucu.md` §16 |

## §4 Örnek 3 — FileHandler: metin satırı kurulumu ve akış kapısı

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 207–235 (özet: 220, 228, 234)

```php
$timestamp = date('Y-m-d H:i:s');
$uri = $_SERVER['REQUEST_URI'] ?? '-';
$method = $_SERVER['REQUEST_METHOD'] ?? '-';
$ip = $_SERVER['REMOTE_ADDR'] ?? '-';
$trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';

$line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

$line = Redactor::redact($line);

$this->appendToFile('coremusic_php_errors.log', $line);

if ($securityStream) {
    $this->appendToFile('coremusic_php_security.log', $line);
}
```

| # | Alan (satır) | OS log karşılığı | Ayrım |
|---|---|---|---|
| 1 | `timestamp` (209) | TIMESTAMP | biçim farklı, anlam aynı |
| 2 | `logLevel` (220) | severity etiketi | uygulama adları |
| 3 | `method uri` (210–211) | — | yalnız uygulama bağlamı |
| 4 | `ip` (212) | client addr | OS auth logunda da olabilir |
| 5 | `trace` (213) | — | uygulama korelasyonu |
| 6 | `coremusic_php_errors.log` (228) | — | dosya kanalı, syslog değil |
| 7 | `coremusic_php_security.log` (234) | audit channel'a benzer | yine uygulama dosyası |

## §5 Örnek 4 — ekleme kilitleme: log I/O maliyetinin tek noktası

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 248–252

```php
private function appendToFile(string $filename, string $content): void
{
    $filepath = $this->logDir . '/' . $filename;
    @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
}
```

| # | Gözlem | Etki |
|---|---|---|
| 1 | `FILE_APPEND` | atomik ekleme (POSIX); Windows'ta davranış farklılığı raporlanır |
| 2 | `LOCK_EX` | dosya kilidi — yüksek log fırtınasında beklemeye yol açabilir |
| 3 | `@` | hata bastırma — disk dolu/gizli hata yutulur (risk §13) |
| 4 | log dizini repo kökü | `LoggerFactory.php:32` → `dirname(__DIR__, 3)` |

**OS ilişkisi:** log hacmi ve disk I/O yükünün ölçümü §11'dedir; bu örnek yalnız
uygulama I/O maliyetinin kanıtıdır.

## §6 Örnek 5 — traceId: istekte tek korelasyon kimliği

**Dosya:** `shared/src/PageRouter/PageRouterKernel.php` · **Satır:** 87–91

```php
$traceId = self::generateTraceId();
$this->logger->setTraceId($traceId);
// P5: FileHandler satırları da aynı traceId ile damgalansın —
// StructuredLogger (JSON) ile dosya log'ları tek istekte birleşir.
\CoreMusic\Log\FileHandler::setTraceId($traceId);
```

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 65–68

```php
public static function setTraceId(string $traceId): void
{
    self::$traceId = $traceId;
}
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | tek traceId, iki logger | uygulama içi korelasyon |
| 2 | statik alan (`FileHandler::$traceId`) | istek yaşam döngüsüne bağlı, OS olay kimliği değil |
| 3 | OS katmanı yok | ftrace/ETW correlation'ı bu kodda **başlamaz** |

> trace–log köprüsünün OS tarafı `trace/trace-surucu.md` §7'dedir; burada
> yalnız uygulama halkası tarif edilir (O3 [UNKNOWN]).

## §7 Örnek 6 — Redactor: tek maskeleme noktası, üç maske

**Dosya:** `shared/src/Log/Redactor.php` · **Satır:** 31

```php
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';
```

**Dosya:** `shared/src/Log/Redactor.php` · **Satır:** 65–93 (özet)

```php
public static function redact(string $line): string
{
    if ($line === '') {
        return $line;
    }

    // 1) Anahtar-adı maskesi (key=value / key: value / "key":"value")
    $redacted = preg_replace_callback(self::KEY_VALUE_PATTERN, ...);

    // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);

    // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
    $withEmail = preg_replace_callback(self::EMAIL_PATTERN, ...);

    return $withEmail ?? ($replaced ?? $redacted);
}
```

| # | Maske | Desen kaynağı (satır) | Örnek |
|---|---|---|---|
| 1 | anahtar-değer | `KEY_VALUE_PATTERN` (40) | `password=x` → `password=[REDACTED]` |
| 2 | hash | `LONG_HASH_PATTERN` (49) | 64-char hex/base64 → `[REDACTED]` |
| 3 | e-posta | `EMAIL_PATTERN` (56) | `a@b.com` → `***@b.com` |
| 4 | UUID | bilinçli koruma | 8-4-4-4-12 **düz metin kalır** |

**OS ilişkisi:** OS log kanalları (kern.log, Event Log, jurnal) bu redaction'dan
geçmez — PII yalnız uygulama katmanında maskelenir (kapsam §C.3).

## §8 Örnek 7 — LoggerFactory: tekil logger ve varsayılan dizin

**Dosya:** `shared/src/Log/LoggerFactory.php` · **Satır:** 13–33

```php
private static ?FileHandler $logger = null;

public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
{
    if (self::$logger === null) {
        $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
        self::$logger = new FileHandler($logDir, $level);
    }
    return self::$logger;
}

private static function resolveLogDir(): string
{
    // Proje kök dizinine yaz (C:\www\coremusic.net\)
    return dirname(__DIR__, 3);
}
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | singleton (satır 13) | süreç başına tek logger — OS servisi değil |
| 2 | repo köküne yazma (satır 32) | log dosyaları uygulama ağacında; rotasyon için `logrotate` gerekir (`log-surucu.md` §3) |

## §9 Örnek 8 — şiddet kapıları: debug guard, shouldLog, bilinmeyen seviye

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 188–205

```php
private function logAt(string $level, string $message, array $context, bool $securityStream = false): void
{
    if ($level === 'debug') {
        if (!$this->debugMode || !$this->shouldLog('debug')) {
            return;
        }
    } elseif (!$this->shouldLog($level)) {
        return;
    }
    // ...
}

private function shouldLog(string $level): bool
{
    return (self::LEVELS[$level] ?? 0) >= (self::LEVELS[$this->minLevel] ?? 0);
}
```

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 125–132

```php
public function log($level, string|Stringable $message, array $context = []): void
{
    $normalized = strtolower((string) $level);
    if (!isset(self::LEVELS[$normalized])) {
        $normalized = 'error';
    }
    $this->logAt($normalized, (string) $message, $context);
}
```

| # | Kapı | Davranış | OS karşılığı |
|---|---|---|---|
| 1 | debug guard (190–193) | debug yalnız debug modda | setlogmask benzeri, süreç içi |
| 2 | shouldLog (202–205) | min seviye altı yazılmaz | severity filtresi |
| 3 | bilinmeyen seviye → error (128–130) | fail-safe: kayıt kaybolmaz | OS'ta karşılığı yok — uygulama kararı |

## §10 Örnek 9 — timing: süre ölçümü log satırına düşer, metrik deposuna değil

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 47–53

```php
public function timing(string $module, string $middleware, float $durationMs): void
{
    $this->log('debug', $module, 'middleware_timing', [
        'middleware'  => $middleware,
        'durationMs' => round($durationMs, 3),
    ]);
}
```

| # | Gözlem | Not |
|---|---|---|
| 1 | süre `debug` seviyesiyle loglanır | üretimde (minLevel=info) **yazılmaz** |
| 2 | metrik deposu yok | zaman serisi toplanmaz — OS sayaç okuması hiç yok |
| 3 | kaynak `hrtime` | `MiddlewarePipeline.php:42` — süre ölçümünün kendisi `metrik/metrik-ornekler.md` §2'dedir |

## §11 Ölçüm — log hacmi ve I/O yükü nasıl ölçülür (tarif)

> Bu bölümde **sayısal değer yoktur** — ölçüm yapılmamıştır (O1 [UNKNOWN]).
> Yalnız hangi OS aracının hangi soruyu ölçeceği tarif edilir; araç kanıtları
> `metrik/` alt-dalındadır (M-W2 iostat, M-W1 proc).

| # | Soru | Ölçüm aracı | Kanıt |
|---|---|---|---|
| 1 | log dosyası ne kadar yer kaplıyor? | `du` / `ls -l` (repo kökü) | repo içi komut — tarif |
| 2 | disk G/Ç yükü ne kadar? | `iostat -xz 1` | M-W2 (iostat.1) |
| 3 | log satır hızı (satır/sn)? | dosyanın satır sayısının zamana oranı | uygulama ölçümü — OS metriği değil |
| 4 | rotasyon sonrası disk geri kazanımı? | rotasyon öncesi/sonrası `du` farkı | `log-surucu.md` §3 |

## §12 Olumsuz kanıt — repo'da olmayan OS log araçları

| # | Desen | Beklenen kullanım | İsabet |
|---|---|---|---|
| 1 | `syslog(` | syslog(3) kütüphane çağrısı | 0 |
| 2 | `journald` | journald yapılandırması | 0 |
| 3 | `proc/self` | /proc üzerinden log okuma | 0 |
| 4 | `EventWrite` | ETW etkinlik yazımı | 0 |
| 5 | `ftrace` | çekirdek trace logu | 0 |
| 6 | `OpenTelemetry` / `OTel` | dağıtık log kütüphanesi | 0 |

> Repo geneli `*.{php,js,yml,yaml,json}` taraması (vendor hariç), 2026-10-13.
> Sonuç: OS log kanalı kullanılmıyor — "eksiklik" değil, **tanımsızlık** (O1).

## §13 CI log yüzeyi — workflow dosyasının kanıtlanmış davranışları

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 104–108

```yaml
      - name: PHPUnit
        run: vendor/bin/phpunit "$PHPUNIT_ARGS"
        env:
          PHPUNIT_ARGS: ${{ matrix.args }}
        working-directory: ${{ matrix.project }}
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 182–183

```yaml
    # A6: e2e lock'a alındı; ilk koşular bilinen-riskli → soft job.
    continue-on-error: true
```

| # | Gözlem | Satır | Log etkisi |
|---|---|---|---|
| 1 | test çıktısı CI loguna akar | 104–108 | GitHub Actionsstdout'u = geçici CI logu; OS logu değil |
| 2 | soft job (`continue-on-error`) | 183 | e2e hatası CI logunda kalır, işi kırmaz |
| 3 | `composer audit` job'u | 123–127 | bağımlılık güvenlik çıktısı CI logunda |

> CI loglarının saklanması/rotasyonu GitHub'ın sorumluluğundadır; bu dalın
> `logrotate`/journald kapsamı dışındadır (kapsam §A.2).

## §14 Riskler ve anti-pattern'ler (repo gözlemi)

| # | Gözlem | Risk | Durum |
|---|---|---|---|
| 1 | `@file_put_contents` (251) | disk dolu/hata sessiz yutulur | raporlandı — davranış değişikliği bu turda YOK |
| 2 | `LOCK_EX` + fırtına | bekleyen istek birikimi | `log-surucu.md` §10 freni OS katmanında tarif |
| 3 | log dosyaları repo kökünde (32) | rotasyon yapılandırması repo'da tanımsız (O1) | [UNKNOWN] |
| 4 | UUID düz metin (Redactor) | korelasyon kimliği PII değil — bilinçli | koruma kararı |
| 5 | `microtime` vs `hrtime` ikiliği | iki farklı saat API'si | yalnız raporlandı — `metrik/metrik-ornekler.md` §3–§4 |

## §15 K017 sınırı — bu dosyanın dokunmadığı alanlar

| # | Konu | Gittiği yer |
|---|---|---|
| 1 | OTel LogRecord / log collector | `K016-observability` |
| 2 | log metriği (satır/sn) ve alert | `K016-observability` |
| 3 | uygulama kuyruğu/saklama politikası | `K016-observability` |
| 4 | OS servisi kurulumu (rsyslog/journald yapılandırması) | O1 çözülünceye kadar tanımsız |

## §16 Kaynakça (envanter dışı, HTTP 200 — 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| L-W1 | `syslog(3)` Linux sistem günlüğü arayüzüdür | https://man7.org/linux/man-pages/man3/syslog.3.html | 200 |
| L-W3 | setlogmask(3) kayıt seviyesi maskesi verir | https://man7.org/linux/man-pages/man3/setlogmask.3.html | 200 |
| L-W5 | dmesg(1) çekirdek halka tamponunu okur | https://man7.org/linux/man-pages/man1/dmesg.1.html | 200 |
| L-W14 | logrotate(8) log rotasyonunu yönetir | https://man7.org/linux/man-pages/man8/logrotate.8.html | 200 |

> Tam envanter `log/log-anayasa.md` §5 ve `log/log-surucu.md` §15'tedir (L-W1…L-W14).
> Doğrulanamayan adaylar (logger.8, syslog.conf.5, klogctl.2 — HTTP 404) bu
> dosyaya alınmamıştır (kök `-dogrulama.md` §4).

## §17 logger(1) ↔ repo log yüzeyi karşılaştırması (bu tur)

**Kanıt:** logger(1) (L-W17, 2026-10-13 webfetch HTTP 200) — `man7.org/linux/man-pages/man1/logger.1.html`.

| # | logger(1) | Repo karşılığı (§2–§4) | Fark |
|---|---|---|---|
| 1 | sistem günlüğüne mesaj yazar | `error_log()` PHP SAPI hedefine yazar (§2) | OS kanalı ≠ uygulama dosyası |
| 2 | facility/level listesi taşır | `LEVELS` 4/8 kademe (§3) | adlar uygulamaya ait |
| 3 | RFC 3164 / RFC 5424 biçimleri | `[CoreMusic] {JSON}` / metin satırı | biçim standardı yok |
| 4 | `--journald` seçeneği | yok — repo'da journald deseni 0 isabet (§12) | tanımsızlık (O1) |
| 5 | `--prio-prefix` (facility×8 + level) | yok — seviye öneki üretilmez | kural: `log-surucu.md` §16 |
| 6 | stderr yerine sistem günlüğüne yönlenme | `error_log` hedefi SAPI yapılandırması | ikisi de OS kanalı **değil** |

| # | Seviye karşılaştırma | logger/syslog (0–7) | FileHandler (§3) | StructuredLogger (§3) |
|---|---|---|---|---|
| 1 | en kritik | emerg (0) | emergency (7) | error (3) |
| 2 | en hafif | debug (7) | debug (0) | debug (0) |
| 3 | uyarı | warning (5) | warning (3) | `warn` (2) |
| 4 | yön | artan = kritik | artan = kritik | artan = kritik |
| 5 | eşleme | — | birebir (8 kademe) | dönüşüm gerekir (4 kademe) |

| # | Karar | Gerekçe |
|---|---|---|
| 1 | repo, logger(1) **çağırmıyor** | `logger` deseni grep'de 0 isabet (§12 ile aynı tarama) |
| 2 | facility/level eşlemesi bu dosyada **uydurulmaz** | O1 çözülmedi |
| 3 | `--journald`/`--prio-prefix` yalnız arayüz kanıtı olarak taşınır | kullanım kararı K002-altyapi |

## §18 rsyslog.conf(5) ve systemd.journal-fields(7) ↔ repo (bu tur)

**Kanıt:** rsyslog.conf(5) (L-W15) · systemd.journal-fields(7) (L-W16) — 2026-10-13 webfetch HTTP 200.

| # | OS kavramı | rsyslog.conf(5) | Repo karşılığı | Fark |
|---|---|---|---|---|
| 1 | selector (facility.priority) | `/etc/rsyslog.conf` kuralları | yok (dosya kanalı §4) | OS yönlendirme ≠ uygulama dosyası |
| 2 | template | satır şablonları | `$line` biçimi (§4, satır 220) | repo şeması kendi içinde sabit |
| 3 | output channel (`$outchannel`) | kanal + max-size | yok — boyut sınırı tanımsız | [UNKNOWN] (§14.3) |
| 4 | property replacer | alan dönüşümü | `Redactor` maskeleme (§7) | dönüşüm ≠ redaksiyon |
| 5 | yönlendirme (UDP/TCP/RELP) | uzak sunucuya aktarım | yok | dağıtık log tanımsız (O3) |
| 6 | queued operations | kuyruk/çekirdek | yok — `LOCK_EX` ekleme (§5) | uygulama kilidi ≠ OS kuyruğu |

| # | jurnal alanı | Ne taşır | Repo karşılığı | Fark |
|---|---|---|---|---|
| 1 | `MESSAGE` | metin | `$message` | aynı işlev, farklı katman |
| 2 | `PRIORITY` (0–7, syslog uyumlu) | şiddet | `logLevel` (§3) | ad/ölçek farklı |
| 3 | `SYSLOG_*` alanları | gönderen/kimlik | yok | — |
| 4 | güvenilir alanlar `_PID`, `_UID`, `_COMM`, `_TRANSPORT` | çekirdek doğrulaması | yok — yalnız `$_SERVER` IP/URI (§4) | uygulama bilgisi ≠ OS doğrulaması |
| 5 | adres alanları `__CURSOR`, `__REALTIME_TIMESTAMP` (`__MONOTONIC_TIMESTAMP`) | kayıt adresleme/konum | yok — `traceId` + ATOM timestamp | jurnal imleci ≠ uygulama korelasyonu |

| # | Karar | Gerekçe |
|---|---|---|
| 1 | bu iki kaynak **OS log kanalının** arayüz tanımıdır | repo bu kanala **yazmaz** (§12) |
| 2 | jurnal alanları repo'da **eşlenmez** | eşleme kararı O1/O3 çözülmeden yazılmaz |
| 3 | `__REALTIME_TIMESTAMP` ile repo ATOM timestamp aynı **sayılmaz** | farklı katman + farklı kaynak |
| 4 | `_PID/_UID` güvenilir alanları uygulamadan **gelmez** | çekirdek/gönderen doğrulaması |

## §19 CI log akışı — genişletilmiş satır kanıtları (ci.yml, 203 satır)

| # | Satır | İçerik | Log etkisi |
|---|---|---|---|
| 1 | 104–108 | `PHPUnit` adımı + `PHPUNIT_ARGS` matrisi | test çıktısı CI loguna akar |
| 2 | 123–127 | `composer install` + `composer audit` (shared) | güvenlik çıktısı CI logunda |
| 3 | 157–158 | `Vitest (unit)` / `npm run test:js` | JS test çıktısı |
| 4 | 161 | `name: JS Coverage (gate — soft, A7)` | job adı eşik niyetini taşır |
| 5 | 165–166 | `A7 kararı: %80 eşiği şimdilik erişilemez (12.9k satır / <100 test)` | eşik gerekçesi yorumda |
| 6 | 167 | `continue-on-error: true` | job kırmızı görünür, iş kırılmaz |
| 7 | 175–176 | `Vitest coverage` / `npm run test:js:coverage` | kapsam çıktısı |
| 8 | 182–183 | `A6: e2e lock'a alındı` + `continue-on-error: true` | ikinci soft job |
| 9 | 17–19 | `cancel-in-progress: true` | iptal edilen işin logu GitHub kalır |
| 10 | 27,70,113,145,164,181 | `timeout-minutes` 15/15/10/10/15/20 | süre aşımında iş logla biter |

| # | CI log kuralı | Neden |
|---|---|---|
| 1 | CI logu **geçicidir** ve GitHub'ındır | `logrotate`/journald kapsamı dışı (§13) |
| 2 | soft job (`continue-on-error`) hata **gizlemez** — kırmızı görünür | dürüst rapor |
| 3 | CI loguna OS logu **yazılmaz** | repo'da yönlendirme yok (§12) |
| 4 | `timeout-minutes` ölçüm **değildir** | emniyet sınırı |

## §20 Bu tur doğrulanan / reddedilen kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | L-W15 | rsyslog.conf(5) selector/template/yönlendirme tanımlar | https://man7.org/linux/man-pages/man5/rsyslog.conf.5.html | 200 (bu tur) |
| 2 | L-W16 | systemd.journal-fields(7) jurnal alanlarını tanımlar | https://man7.org/linux/man-pages/man7/systemd.journal-fields.7.html | 200 (bu tur) |
| 3 | L-W17 | logger(1) sistem günlüğüne mesaj yazar | https://man7.org/linux/man-pages/man1/logger.1.html | 200 (bu tur) |
| 4 | — | `man7.org/.../man8/logger.8.html` | (aday) | 404 — kullanılmadı |
| 5 | — | `man7.org/.../man3/openlog.3.html` | (aday) | 200 döndü ama içerik **syslog(3)** (L-W1) — mükerrer, yeni ID yok |

| # | Red korunan adaylar | Sonuç |
|---|---|---|
| 1 | `syslog.conf.5` (kök §4) | 404 |
| 2 | `klogctl.2` (kök §4) | 404 |

## §21 OS örnek turu — günlük okuma/yazma komut örnekleri (2026-10-14)

> §17–§18 repo'nun **OS kanalına yazmadığını** kanıtladı. Bu bölüm repo
> dışı, **OS düzeyinde** okuma/yazma örneklerini tarif eder — anayasa
> kuralıdır, repoda çalışan kod değildir. Kanıtlar 2. tur doğrulamasından
> (L-W18–L-W27, tam liste dogrulama §1.1) — tümü HTTP 200.

### §21.1 Linux — journald / syslog / auditd

```bash
# journald: unit bazlı, zaman aralıklı okuma (L-W7)
journalctl -u php-fpm --since "10 min ago" -p err..emerg

# dmesg: çekirdek halkası, hata seviyeleri (L-W8)
dmesg --level=err,warn,crit -T | tail -n 40

# logger: satır içi syslog üretimi (L-W17)
logger -p local0.err "route rejected: coremusic"

# auditd: güvenlik olayı sorgu (L-W19)
ausearch -m USER_LOGIN -ts today
```

`auditd` (L-W18) disk kaydı üretir; `ausearch` (L-W19) bu kayıtları sorgular.
Repo'da `logger(1)` çağrısı **yoktur** (§17 kararı 1); repo `error_log` ile
`file_put_contents` kullanır (§4–§5).

### §21.2 Windows — Event Log

```powershell
# wevtutil: olay okuma, son 10 kayıt (L-W20)
wevtutil qe Application /c:10 /f:text

# Get-WinEvent: PowerShell filtreli okuma (L-W21)
Get-WinEvent -FilterHashtable @{LogName='Application'; Level=2} -MaxEvents 20

# eventcreate: satır içi olay yazma (L-W22)
eventcreate /T ERROR /ID 101 /L APPLICATION /SO "CoreMusic" /D "route rejected"

# XPath sorgu: zaman aralıklı filtre (L-W24)
wevtutil qe Security /q:"*[System[TimeCreated[timediff(@SystemTime) <= 600000]]]"
```

`eventcreate` (L-W22) komut satırı köprüsüdür; üretim kodu `ReportEventW`
(L-W23) Win32 API'sini kullanır — ikisi de aynı olay kanalına yazar.
`Get-WinEvent` (L-W21) `microsoft.powershell.diagnostics` modülündedir;
`utility` modülünde değildir (X10, dogrulama §4.1).

### §21.3 macOS — unified logging

```bash
# log show: subsystem filtreli okuma (L-W25)
log show --last 10m --predicate 'subsystem == "net.coremusic"' --info

# log emit: satır içi kayıt üretimi (L-W25)
log emit --level error --message "route rejected"
```

```swift
// Logger: Swift kayıt üretimi (L-W26 · L-W27)
import OSLog
let logger = Logger(subsystem: "net.coremusic", category: "router")
logger.error("route rejected: \(name, privacy: .public)")
```

`Logger(subsystem:category:)` (L-W27) kayıt kaynağı tanımıdır; hassas
alanlar `privacy: .public` yerine varsayılan gizli modda kalır (L-W12:
`%{private}` belirteci). Bu, repo'daki `Redactor::redact`'in OS karşılığıdır.

> **Dipnot (ayna):** `log.1` (L-W25), `powermetrics.1` (M-W33) gibi Xcode
> man sayfaları üçüncü taraf aynasından (keith.github.io) doğrulanmıştır;
> belge sürümü `UNKNOWN` (dogrulama §1.1).

### §21.4 OS örnek — eşleme matrisi

| OS | Örnek #1 (okuma) | Örnek #2 (yazma) | Kanıt |
|---|---|---|---|
| Linux | `journalctl -u ... -p err..emerg` | `logger -p local0.err ...` | L-W7 · L-W17 |
| Windows | `Get-WinEvent -FilterHashtable ...` | `eventcreate /T ERROR ...` | L-W21 · L-W22 |
| macOS | `log show --predicate 'subsystem ...'` | `Logger(subsystem:category:)` | L-W25 · L-W27 |

> **Repo ile sınır:** Bu üç örnek de **repo'da çalıştırılmaz** — repo
> `error_log`/`file_put_contents` kullanır (§4–§5). Örnekler yalnız OS
> kanalının **arayüz kanıtı**dır; kullanım kararı `K002-altyapi` (§17.3).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
