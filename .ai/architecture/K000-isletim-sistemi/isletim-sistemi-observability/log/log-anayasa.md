---
id: isletim-sistemi-observability-log-anayasa
title: "Günlük (Log) Anayasası — İşletim Sistemi Kademesi"
node_type: subdomain
group: mimari
status: active
tags: [observability, log, syslog, journald, etw, rfc5424]
children: []
---
# §0 ELI15 — Günlük ne işe yarar, bu dosya neyi bağlar

Günlük (log) bir kaydın **zaman damgası + kaynak + şiddet + metin** ile diske veya
halkaya yazılmasıdır. İşletim sistemi kademesinde günlük üç yerde doğar:
çekirdek (`printk` → `/dev/kmsg` → `dmesg`), sistem servisi (`journald` / syslog
kuyruğu) ve kullanıcı alanı süreci (ETW sağlayıcısı / dosya yazıcısı).

Bu anayasa **OS düzeyi** günlük üretimini bağlar: çekirdek mesajı, journald
alanları, RFC 5424 çerçeve yapıtaşları, Windows olay günlüğü sağlayıcısı, Apple
os_log. **Uygulama ve dağıtık** günlük (OTel LogRecord, APM gönderici, log
korelasyonu) `K017-observability` dalının sorumluluğundadır; buradan
bağlantılar yalnız komşu sınır matrisinde çizilir.

Değişmez üç madde:

1. Şiddet etiketi hiç olmasa da kaynak her zaman yazılabilir; uydurulmuş
   etiket, uydurulmuş eşik, uydurulmuş hacim sayısı yazılmaz — `UNKNOWN` yazılır.
2. Hassas alan redaksiyondan **önce** halkaya girmez (repo'da tek nokta:
   `Redactor::redact`).
3. Doğrulanmayan URL envantere girmez; bu dosyadaki her URL HTTP HEAD ile
   200 almıştır (§5, tarih 2026-10-13).

Bu dosyanın kanıt düzlemi iki koldur: (a) resmi OS belgeleri (§5), (b) bu
repoda **Read ile okunmuş** üretim kodu (§6–§7). Kod bloklarındaki satır
numaraları dosyanın kendisinden alınmıştır; tahmini satır numarası yoktur.

## §1 Günlük mimarisi — üç OS kaynağı

| Kaynak | Doğduğu yer | Halka/saklama | Kanıt |
| --- | --- | --- | --- |
| Çekirdek mesajı | `printk` → `/dev/kmsg` | Ring buffer → `dmesg` okuması | L-W9 printk-formats · L-W8 dmesg.1 |
| Sistem günlüğü | `journald` (Linux) / ETW (Windows) | Jurnal dosyası / olay kanalı | L-W13 systemd-journald.8 · L-W10 windows-event-log |
| RFC 5424 kuyruğu | `syslog(3)` / `rsyslog` / `syslog-ng` | UDP/TCP/unix socket | L-W1 syslog.3 · L-W3 rfc3164 · L-W4 rfc5424 |

Akış zinciri (Linux):

```text
uygulama/daemon --syslog(3)--> /dev/log --journald--> jurnal dosyası
çekirdek --printk--> /dev/kmsg halkası --dmesg--> kullanıcı okuması
journald --ForwardToSyslog=--> syslog kuyruğu (L-W13)
```

`dmesg.1` (L-W8) çekirdek halkasının kullanıcıya açılan arayüzüdür; halka
taşması OS tarafından sessizce yapılır ve en eski kayıt düşer — bu,
uygulama tarafında tekrarlanan bir kuyruk değildir (bkz. `log-surucu.md` §2).

```bash
dmesg --level=err,warn,crit,alert,emerg -T
dmesg -T | tail -n 40
```

## §2 Kaynak türleri ve alan modeli

| Tür | Alanlar | Redaksiyon sorumluluğu |
| --- | --- | --- |
| Çekirdek (`printk`) | seviye (`<n>`), facility, metin, sequence no | Çekirdek metni üreten kod — kullanıcı girdisi doğrudan `printk`a verilmez |
| Journald | `MESSAGE`, `PRIORITY`, `SYSLOG_IDENTIFIER`, `_PID`, `_COMM`, `UNIT` + kullanıcı alanı | Servis; kullanıcı alanı ekleyen uygulama |
| RFC 5424 | HEADER (PRI, version, timestamp, hostname, appname, procid, msgid) + STRUCTURED-DATA + MSG | Uygulama/daemon |
| Windows ETW | Provider GUID, event id, level, keywords, payload | Sağlayıcı |
| Apple os_log | subsystem, category, format, privacy belirteci | Format belirtecipeci (`%{private}`) |

RFC 5424 (L-W4) `PRI` değerini `facility * 8 + severity` olarak kurar; bu
hesap tek başına `setlogmask(3)` (L-W5) ile açılıp kapatılabilen şiddet
filtresiyle birlikte OS günlüğünün iki temel kapısıdır.

```text
<pri>version timestamp hostname appname procid msgid [sd-id sd-param] msg
```

Bileşenlerin bağlayıcı okuması:

| Bileşen | Ne taşır | Düşerse |
| --- | --- | --- |
| `<pri>` | facility + severity tek sayıda | Şiddet kapısı çalışmaz |
| `version` | Çerçeve sürümü (5424 = 1) | Ayrıştırıcı varsayımı kayar |
| `timestamp` | ISO 8601 an | Sıralama kayar |
| `hostname` / `appname` / `procid` | Kaynak üçlüsü | Kaynak bilinmez olur |
| `msgid` | Olay kimliği | Olay gruplaması kayar |
| STRUCTURED-DATA | `[id key="value"]` çiftleri | Yapısal alanlar metne gömülür |
| MSG | Serbest metin | — |

## §3 Şiddet — OS ölçeği, uydurulmayan tek ölçek

| Ölçek | Aralıklar | Kaynak |
| --- | --- | --- |
| syslog şiddeti (RFC 5424 / 3164) | 0 emerg … 7 debug | L-W4 rfc5424 · L-W3 rfc3164 · L-W1 syslog.3 |
| `dmesg --level` | emerg, alert, crit, err, warn, notice, info, debug | L-W8 dmesg.1 |
| Windows olay seviyesi | Critical, Error, Warning, Information, Verbose | L-W10 windows-event-log |
| journald `SyslogLevel` | 0–7 (syslog eşlemesi) | L-W6 journald.conf.5 |
| `printk` level prefix | `KERN_CONT` dâhil level öneki | L-W9 printk-formats |
| PSR-3 (repo) | debug…emergency (8 kademe) | `FileHandler.php:40–49` |

Şiddetin OS karşılığı vardır; uygulama içi özel etiket (`verbose2` gibi)
uydurulmaz, varsa syslog karşılığı haritalanır ve **eşleme tablosuna yazılır**.
Repo'da bu eşleme PSR-3 kanonik adlarıyla sürülür (§6.2).

## §4 Üretici taraf — `syslog(3)` ve maskeleme

`syslog(3)` (L-W1) çağrısı connectionless'tir: proses ilk çağrıda socket'i
açar, kapanana kadar kullanır. `openlog(3)` ile `ident`/`facility` sabitlenir,
`closelog(3)` ile kapatılır. `setlogmask(3)` (L-W5) bitmask ile hem `LOG_UPTO`
hem `LOG_MASK` biçiminde seviye kapıları kurar — erken elenmesi gereken `debug`
trafiğini OS kuyruğuna hiç düşürmek mümkündür.

```c
openlog("coremusic-router", LOG_PID | LOG_NDELAY, LOG_LOCAL0);
setlogmask(LOG_MASK(LOG_ERR) | LOG_MASK(LOG_CRIT));
syslog(LOG_ERR, "route %s rejected: %s", route_name, reason);
closelog();
```

| Çağrı | Ne yapar | Kanıt |
| --- | --- | --- |
| `openlog` | ident/facility/ops bayraklarını sabitler | L-W1 |
| `syslog` | Tek çağrıda PRI + MSG gönderir | L-W1 |
| `setlogmask` | `LOG_UPTO` / `LOG_MASK` ile kapı | L-W5 |
| `closelog` | Socket'i kapatır | L-W1 |
| `klogctl` (syscall) | Çekirdek halkasına doğrudan erişim | envanter kesişimi nedeniyle bu dosyada **kullanılmadı** (bkz. §5 dipnot) |

## §5 Resmi yapı taşları — envanter dışı, HTTP 200 doğrulanmış

Envanter öncesi 2097 URL taranmış; aşağıdaki 13 URL envanterle kesişmez ve
HTTP HEAD ile 200 ile doğrulanmıştır (tümü 2026-10-13).

| # | İddia | URL | Tarih | Durum |
| --- | --- | --- | --- | --- |
| L-W1 | `syslog(3)` ident/facility/openlog semantiği | https://man7.org/linux/man-pages/man3/syslog.3.html | 2026-10-13 | 200 |
| L-W2 | `syslog(2)` syscall kabuğu ve tarih değişimi | https://man7.org/linux/man-pages/man2/syslog.2.html | 2026-10-13 | 200 |
| L-W3 | BSD syslog protokolü (RFC 3164 çerçevesi) | https://www.rfc-editor.org/rfc/rfc3164 | 2026-10-13 | 200 |
| L-W4 | RFC 5424 yapı taşları (PRI/SD/MSG) | https://www.rfc-editor.org/rfc/rfc5424 | 2026-10-13 | 200 |
| L-W5 | `setlogmask(3)` LOG_UPTO/LOG_MASK | https://man7.org/linux/man-pages/man3/setlogmask.3.html | 2026-10-13 | 200 |
| L-W6 | `journald.conf(5)` RateLimit/SystemMaxUse alanları | https://www.freedesktop.org/software/systemd/man/latest/journald.conf.html | 2026-10-13 | 200 |
| L-W7 | `journalctl(1)` filtre/saat bayrakları | https://www.freedesktop.org/software/systemd/man/latest/journalctl.html | 2026-10-13 | 200 |
| L-W8 | `dmesg(1)` halka okuma ve `--level` | https://man7.org/linux/man-pages/man1/dmesg.1.html | 2026-10-13 | 200 |
| L-W9 | Çekirdek `printk` format/level kuralları | https://www.kernel.org/doc/html/latest/core-api/printk-formats.html | 2026-10-13 | 200 |
| L-W10 | Windows Event Log sağlayıcı/kaydı | https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log | 2026-10-13 | 200 |
| L-W11 | Windows Event Log kullanım akışı | https://learn.microsoft.com/en-us/windows/win32/wes/using-windows-event-log | 2026-10-13 | 200 |
| L-W12 | Apple unified logging üretici kılavuzu | https://developer.apple.com/documentation/os/logging/generating-log-messages-from-your-code | 2026-10-13 | 200 |
| L-W13 | `systemd-journald(8)` servis davranışı | https://www.freedesktop.org/software/systemd/man/latest/systemd-journald.html | 2026-10-13 | 200 |

**Dipnot (red kayıtları):** `klogctl.2`, `vmstat.1`, `bpftool.8`, `logger.8`,
`syslog.conf.5` HTTP 404 aldığı için bu dosyaya alınmamıştır; `getrusage.2`,
`gettimeofday.2`, `vdso.7`, `wnode-header` ise envanter kesişimi nedeniyle
elenmiştir. `logrotate.8` (L-W14) bu anayasada değil, `log-surucu.md` §3'te
kullanılır (200 doğrulanmış, 2026-10-13).

`journalctl` saat dilimi/UTC bayrakları (L-W7) bu dalda yalnızca **OS
görüntüleme** düzeyinde geçer; uygulama log'unun UTC'ye zorlanması
`K017-observability` konusudur.

## §6 Doğrulanmış repo kanıtı — üretimdeki günlük zinciri

### 6.1 Yapısal logger — tek çağrıda tek JSON satırı

Dosya: `shared/src/PageRouter/StructuredLogger.php` · Satır: 1 —
`<?php declare(strict_types=1);` üretim günlüğü tiplerini sıkı tutar.

Dosya: `shared/src/PageRouter/StructuredLogger.php` · Satır: 9 — seviye
tablosu sabittir, seviye adı uydurulmaz:

```php
    private const LEVELS = ['debug' => 0, 'info' => 1, 'warn' => 2, 'error' => 3];
```

Dosya: `shared/src/PageRouter/StructuredLogger.php` · Satır: 55–73 — kayıt
üretimi, tek satır JSON, redaksiyon, tek çıkış (`error_log`):

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

Dosya: `shared/src/PageRouter/StructuredLogger.php` · Satır: 47–53 — süre
kaydı `timing` olayı olarak aynı kanala girer (metrik üretimi `metrik/`
alt-dalının konusudur):

```php
    public function timing(string $module, string $middleware, float $durationMs): void
    {
        $this->log('debug', $module, 'middleware_timing', [
            'middleware'  => $middleware,
            'durationMs' => round($durationMs, 3),
        ]);
    }
```

### 6.2 PSR-3 kanonik seviye tablosu

Dosya: `shared/src/Log/FileHandler.php` · Satır: 40–49 — sekiz kademe,
kanonik adlar; OS syslog 0–7 aralığına bu tablodan haritalanır:

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

Dosya: `shared/src/Log/FileHandler.php` · Satır: 52 — uyarı ailesi ayrı
dosya seçkisini belirler:

```php
    private const WARNING_FAMILY = ['warning', 'error', 'critical', 'alert', 'emergency'];
```

### 6.3 Tek yazım yolu — zaman + kaynak + iz + redaksiyon

Dosya: `shared/src/Log/FileHandler.php` · Satır: 207–228 — satır üretimi ve
tek maskeleme noktasından geçiş:

```php
    private function write(string $fileLevel, string $message, array $context, string $logLevel, bool $securityStream = false): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'] ?? '-';
        $method = $_SERVER['REQUEST_METHOD'] ?? '-';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
        $trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

        // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur;
        // e-postalar maskelenir (B-F-17: PII logda düz metin durmasın).
        $line = Redactor::redact($line);

        // Tüm seviyeler errors.log'a yazılır (mevcut davranış korunur —
        // kullanici onayli kanonik 4 dosya: errors/warnings/info/kernel_debug).
        $this->appendToFile('coremusic_php_errors.log', $line);
```

Dosya: `shared/src/Log/FileHandler.php` · Satır: 248–252 — dosya eklemesi
`LOCK_EX` ile atomik kuyruğa alınır:

```php
    private function appendToFile(string $filename, string $content): void
    {
        $filepath = $this->logDir . '/' . $filename;
        @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
    }
```

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11–22 — tekil fabrika,
tek dosya yazıcısı:

```php
final class LoggerFactory
{
    private static ?FileHandler $logger = null;

    public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
    {
        if (self::$logger === null) {
            $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
            self::$logger = new FileHandler($logDir, $level);
        }
        return self::$logger;
    }
```

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 29–33 — log dizini kök
dizinden çözülür (OS dosya ağacıyla tek temas noktası):

```php
    private static function resolveLogDir(): string
    {
        // Proje kök dizinine yaz (C:\www\coremusic.net\)
        return dirname(__DIR__, 3);
    }
```

### 6.4 Olay kayıtları — güvenlik akışı

Dosya: `shared/src/Log/FileHandler.php` · Satır: 139–163 — auth olayı seviye
eşlemesi `match` ile sabitlenir, seviye uydurulmaz:

```php
    public function authEvent(string $event, array $context = []): void
    {
        $message = "[AUTH] {$event}";
        if (!empty($context['email'])) {
            $message .= " email={$context['email']}";
        }
        if (!empty($context['ip'])) {
            $message .= " ip={$context['ip']}";
        }
        if (!empty($context['user_id'])) {
            $message .= " user_id={$context['user_id']}";
        }
        if (!empty($context['reason'])) {
            $message .= " reason={$context['reason']}";
        }

        $level = match ($event) {
            'login_success', 'register_success', 'logout' => 'info',
            'login_failed', 'register_failed', 'rate_limited' => 'warning',
            'login_error', 'register_error' => 'error',
            default => 'info',
        };

        $this->logAt($level, $message, $context, securityStream: true);
    }
```

Dosya: `shared/src/Log/FileHandler.php` · Satır: 169–184 — güvenlik olayı
ayrı akışa (`coremusic_php_security.log`) yazılır; bu, OS audit trail'ine
en yakın repo yüzeyidir:

```php
    public function securityEvent(string $event, array $context = []): void
    {
        $message = "[SECURITY] {$event}";
        foreach (['ip', 'uri', 'origin', 'role', 'code', 'reason', 'path'] as $key) {
            if (!empty($context[$key])) {
                $message .= " {$key}={$context[$key]}";
            }
        }

        $level = match ($event) {
            'origin_blocked', 'csrf_invalid', 'authz_denied', 'rate_limited', 'bypass_activated' => 'warning',
            default => 'warning',
        };

        $this->logAt($level, $message, $context, securityStream: true);
    }
```

Dosya: `shared/src/Log/FileHandler.php` · Satır: 188–199 — seviye kapısı ve
dosya seçimi tek yerdedir:

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

        $fileLevel = in_array($level, self::WARNING_FAMILY, true) ? 'error' : 'info';
        $this->write($fileLevel, $message, $context, strtoupper($level), $securityStream);
    }
```

## §7 Redaksiyon — halkadan önce düşen alanlar

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — hassas anahtar listesi
tek satırda sabittir (case-insensitive eşleşme):

```php
    private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';
```

Dosya: `shared/src/Log/Redactor.php` · Satır: 40 — key=value / key: value /
JSON üç biçim tek desende:

```php
    private const KEY_VALUE_PATTERN = '/(\b(?:' . self::SENSITIVE_KEYS . ')\b["\']?\s*[=:]\s*)("[^"]*"|\'[^\']*\'|(?:Bearer|Basic|Digest)\s+[^\s,&;)\]}]+|[^\s,&;)\]}]+)/i';
```

Dosya: `shared/src/Log/Redactor.php` · Satır: 49 — 64 karakterlik hash
lookaround ile; UUID (8-4-4-4-12) korunur:

```php
    private const LONG_HASH_PATTERN = '/(?<![A-Za-z0-9+\/])[A-Za-z0-9+\/]{64}(?![A-Za-z0-9+\/])/';
```

Dosya: `shared/src/Log/Redactor.php` · Satır: 56 — e-posta maskesi, alan
adı kalır, yerel kısım gizlenir:

```php
    private const EMAIL_PATTERN = '/(?<![A-Za-z0-9._%+-])[A-Za-z0-9._%+-]+@([A-Za-z0-9.-]+\.[A-Za-z]{2,})/';
```

Dosya: `shared/src/Log/Redactor.php` · Satır: 65–93 — üç aşamalı tek geçiş;
bir aşama `null` dönerse satır aynen korunur (fail-safe):

```php
    public static function redact(string $line): string
    {
        if ($line === '') {
            return $line;
        }

        // 1) Anahtar-adı maskesi (key=value / key: value / "key":"value")
        $redacted = preg_replace_callback(
            self::KEY_VALUE_PATTERN,
            static fn (array $m): string => self::maskValue($m[1], $m[2]),
            $line
        );

        if ($redacted === null) {
            return $line;
        }

        // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
        $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);

        // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
        $withEmail = preg_replace_callback(
            self::EMAIL_PATTERN,
            static fn (array $m): string => '***@' . $m[1],
            $replaced ?? $redacted
        );

        return $withEmail ?? ($replaced ?? $redacted);
    }
```

| Kural | Repo davranışı | Satır |
| --- | --- | --- |
| Hassas anahtar → `[REDACTED]` | 3 desen (key=value, `: `, JSON) | `Redactor.php:40` |
| 64-char hash → `[REDACTED]` | lookaround, `\b` değil | `Redactor.php:49` |
| UUID korunur | tire deseni hash kuralına girmez | `Redactor.php:47` (yorum satırı) |
| E-posta maskesi | `***@alan` | `Redactor.php:56` |
| Geçmiş dosyalara dokunulmaz | yalnız yazılacak satır | `Redactor.php:22` (yorum satırı) |
| Tek nokta | FileHandler + StructuredLogger + fatal hepsi buradan | `Redactor.php:10–11` (yorum satırı) |

## §8 Platform karşılaştırması — tek tablo

| Boyut | Linux journald | Linux syslog | Windows ETW / Event Log | Apple os_log |
| --- | --- | --- | --- | --- |
| Kaynak belgesi | L-W13 · L-W6 | L-W1 · L-W4 | L-W10 · L-W11 | L-W12 |
| Üretici | `sd_journal_send` / `syslog(3)` devri | `syslog(3)` | Sağlayıcı kayıtçısı | `os_log` / `os.Logger` |
| Saklama | Jurnal dosyası + `SystemMaxUse` | Kuyruk + rotasyon | Kanal dosyaları | Tekil depo |
| Filtre | `journalctl` alan filtresi (L-W7) | facility/severity mask (L-W5) | Level/keyword/ID | Subsystem/category |
| Hassas alan | Alan politikası | Uygulama sorumluluğu | Olay alanı | `%{private}` |
| Bu dalda kanıt | Env. + repo redaksiyonu | Env. | Yalnız envanter (repo 0 isabet) | Yalnız envanter |

Windows ve Apple satırlarında repo kanıtı **yoktur** — bu dosyada `UNKNOWN`
olarak işaretlenir; desktop kabuğunun ETW os_log üretimi `UNKNOWN` (O3).

## §9 K017 sınırı ve komşu matrisi

| Konu | Burada mı? | Karşı taraf |
| --- | --- | --- |
| journald/syslog/ETW halkası | Evet | — |
| `dmesg` çekirdek okuması | Evet | `isletim-sistemi-kernel` (kaynak) |
| Uygulama log korelasyonu, OTel LogRecord | Hayır | `K017-observability` |
| Log metriği (`rate(logs)`) | Hayır | `metrik/` alt-dalı + `K017-observability` |
| Audit trail güvenliği | Hayır | `isletim-sistemi-security` |
| Log bozulma/parçalanma testi | Hayır | `isletim-sistemi-testing` |
| Syslog uçları / API log sözleşmesi | Hayır | `isletim-sistemi-api` |
| OS araç farkları (platform turu) | Hayır | `isletim-sistemi-turu` |
| `/proc` üreten çekirdek arayüzü | Hayır | `isletim-sistemi-core` |
| Kernel event / planlayıcı | Hayır | `isletim-sistemi-plan` |

## §10 Üretici–taşıyıcı–saklayıcı sorumluluk matrisi

| Rol | Linux journald | Linux syslog zinciri | Windows | Bu repoda |
| --- | --- | --- | --- | --- |
| Üretici | `syslog(3)` / `sd_journal_send` | `syslog(3)` | Uygulama `ReportEvent`/ETW | `StructuredLogger`, `FileHandler` |
| Taşıyıcı | journald socket'i | UDP/TCP/unix kuyruk | Olay taşıma servisi | `file_put_contents` (LOCK_EX) |
| Saklayıcı | Jurnal dosyası | Dönen dosyalar | Kanal dosyaları | Repo kökü `.log` dosyaları |
| Redaksiyon | Alan politikası (L-W6/L-W13) | Uygulama sorumluluğu | Sağlayıcı alanı | `Redactor::redact` tek nokta |
| Gösterim | `journalctl` (L-W7) | `grep`/log viewer | Event Viewer (L-W11) | Dosya okuma (`grep`) |

Sorumluluk zincirinde **hiçbir halka atlanmaz**: redaksiyon yapan yoksa
kayıt hassas veriyle diske düşer; bu dalın bağlayıcı tek noktası
`Redactor`'un yazım yoluna **girmeden önce** çağrılmasıdır (§7).

## §11 Açık sorular

| # | Soru | Durum |
| --- | --- | --- |
| O1 | Günlük hacim eşiği (GB/gün) üretimde nedir? | `[UNKNOWN]` — ölçülmedi |
| O2 | Mevcut rotasyon kural dosyasının adı/konumu? | `[UNKNOWN]` — repo/envanter 0 isabet |
| O3 | Windows ETW / Apple os_log üreten repo kodu var mı? | `[UNKNOWN]` — grep 0 isabet |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
