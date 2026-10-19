---
node_id: "K000-isletim-sistemi/isletim-sistemi-observability/isletim-sistemi-observability-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-observability"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-observability/isletim-sistemi-observability-ornekler"
evidence:
  - "https://man7.org/linux/man-pages/man3/syslog.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/eventlog/event-logging (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/os/logging (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/clock_gettime.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/clock_gettime.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.hrtime.php (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/process.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/time (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/time/struct.Instant.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.stopwatch (✅ HTTP 200 · 2026-10-14)"
  - "https://freedesktop.org/software/systemd/man/systemd-journald.service.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/perfctrs/ (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.performancecounter (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/getrusage.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/log/syslog (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-gettickcount64 (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/kernel/1462446-mach_absolute_time (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man5/proc.5.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 23 benzersiz resmi URL (HTTP 200 kapısı) · 22 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: OS-level log/metrik toplama hedef mimaride var mı? (ornekler O1 — grep 0) → UNKNOWN"
  - "O2: macOS üretim/koşucu kanıtı repo'da yok (Q4) → os_log/mach_absolute_time sütunları [TASARIM]"
  - "O3: syslog/Event Log'a ÇİFT yazım maliyeti (dosya + OS kanalı) — kararı yok → UNKNOWN"
  - "O4: seviye eşlemesi (PSR-3 ↔ syslog severity) birebir değil (ornekler §15) → açık eşleme tablosu gerekli"
  - "O5: Performance Counter / procfs okuma sıklığı ve bütçesi → [TASARIM], ölçüm yok"
group: kod
---

# isletim-sistemi-observability — Cross-Platform Kod Seti (log hattı · monotonic saat · ölçümler)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-observability` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar; kod **Windows · Linux · macOS dahil tüm sınıflarda**
> çalışacak biçimde yazılır. Bu dosya üç gözleme derdini kucaklar: **log hattı** (GRUP A),
> **monotonic saat** (GRUP B), **ölçümler/sayaç** (GRUP C). `turu-kod-seti` tekrarı YOKTUR.
> Repo gerçeği (ornekler §11/§18): `syslog(`/`ftrace`/`perf_event`/`/proc okuma` **0 isabet** —
> gözlemlenebilirlik bugün yalnız uygulama katmanındadır; bu dosya OS köprüsünü **hedef** olarak yazar.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi taşır |
| Repo referansı | Repo gerçekleri kardeş dosyadan (`isletim-sistemi-observability-ornekler.md` §1–§28) referanslanır; yeni `Dosya:/Satır:` çifti **üretilmez** |
| Kaynak | Dil/arayüz adları §5'teki **2026-10-14 HTTP 200** resmi dokümanlardan türetildi; içerik alıntısı yok |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Bağımlılık | Üçüncü parti log/metrics kütüphaneleri (OTel, prometheus-client vb.) **eklenmez**; yalnız kavram (R6) |
| Yasak | `TBD`/`...` dolgusu · kaynaksız API adı · "çalışıyor" iddiası · uydurma repo satırı · sessiz fallback |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış doğrulanmadı ·
`UNKNOWN` = repo'da kanıt yok.

---

## §1. Çalışma sözleşmesi — "koşan tek Linux, yazılan her yerde"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] `ci.yml` — ornekler §13 |
| Log hedefi bugün | uygulama dosyası (`error_log` → dosya) | [KANIT] ornekler §1/§27 |
| OS log kanalı | syslog/journald · Event Log · os_log — **hedef** | [POLİTİKA] GRUP A · ornekler §27 |
| Monotonic saat | `hrtime` zaten repo'da (`MiddlewarePipeline`) | [KANIT] ornekler §7/§20 |
| Ölçüm/sayaç | procfs + Performance Counter — **hedef** | [POLİTİKA] GRUP C · ornekler §11 |
| macOS | repo kanıtı **yok** | [BİLİNMEYEN] Q4 → `[TASARIM]` |

**Sözleşme kuralları (4 madde — her grup bunlara uyar):**

1. **Seviye eşlemesi tek tabloda** toplanır (§GRUP A, O4): PSR-3 ↔ syslog severity birebir değildir.
2. **Monotonic saat her ölçümde** kullanılır; duyarlı saat (`realtime`) **sadece** zaman damgası içindir
   (ornekler §20 ayrımı).
3. **OS kanalına çift yazım** (dosya + syslog) bilinçli karardır; varsayılan **kapalıdır** (O3).
4. **`turu-kod-seti`'ne çapraz referans** — OS ailesi karşılaştırması orada; burada yalnız
   gözlemlenebilirlik endişesi. Tekrar 0.

---

## §2. Grup haritası — 3 grup × dil dağılımı

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Log hattı (syslog/journald ↔ Event Log ↔ os_log) | PHP · JS · Python · Go · C# | §GRUP A |
| B | Monotonic saat (CLOCK_MONOTONIC ↔ QPC ↔ mach_absolute_time) | C · Go · Rust · C# · PHP · JS · C++20 · Zig · Swift · Java/Kotlin | §GRUP B |
| C | Ölçümler/sayaç (procfs + getrusage ↔ Performance Counters) | Python · C# · Bash · PHP | §GRUP C |

**Toplam:** 3 grup · 24 kod bloğu · 13 dil yüzeyi (PHP, JS, Python, Go, Rust, C#, C, Bash, PowerShell notları, C++20, Zig, Swift, Java/Kotlin).

---

## GRUP A — Log hattı (OS kanalı eşlemesi)

> **Amaç:** Uygulamanın ürettiği olayı **OS kanalına** taşımak; üç OS'un üç ayrı kayıt arayüzünü
> tek eşleme tablosunda tutmak. Repo karşılığı: `error_log(Redactor::redact($payload))`
> (`-ornekler.md` §1) — bu satır **dosyaya** yazar, OS kanalına **değil** (§27 köprüsü).

### §A.1 PHP — `openlog`/`syslog` (POSIX kanalı)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://man7.org/linux/man-pages/man3/syslog.3.html */
declare(strict_types=1);

final class OsLog
{
    private const FACILITY = LOG_LOCAL0;   // uygulama tesisi — kernel/log ile karışmaz

    public static function boot(): void
    {
        // openlog: ident + opsiyonlar + tesis — syslog() çağrısından ÖNCE kurulur
        if (!openlog('coremusic', LOG_PID | LOG_NDELAY, self::FACILITY)) {
            throw new RuntimeException('openlog başarısız'); // sessiz fallback YASAK
        }
    }

    /** severity eşlemesi: PSR-3 adı → syslog 0–7 (O4: birebir değil — §A.6 tablosu). */
    public static function emit(string $psr3Level, string $event, array $ctx = []): void
    {
        $map = [
            'emergency' => LOG_EMERG, 'alert' => LOG_ALERT, 'critical' => LOG_CRIT,
            'error'     => LOG_ERR,   'warning' => LOG_WARNING, 'notice' => LOG_NOTICE,
            'info'      => LOG_INFO,  'debug' => LOG_DEBUG,
        ];
        $sev = $map[$psr3Level] ?? null;
        if ($sev === null) {
            throw new InvalidArgumentException("bilinmeyen seviye: {$psr3Level}"); // fail-safe
        }
        $line = $event . ' ' . json_encode($ctx, JSON_UNESCAPED_UNICODE);
        syslog($sev, '%s', $line);   // biçim dizesi sabit — enjeksiyon yüzeyi kapanır
    }
}
```

**Repo köprüsü:** Repo bugün `error_log` kullanır (`-ornekler.md` §1); bu blok **OS kanalı**
köprüsünü kurar. `⚠️ VERIFICATION REQUIRED`: `openlog` yalnız POSIX'te; Windows PHP'de `syslog()`
kaynaklı davranışı doğrulanmadı → `UNKNOWN` (bkz. §A.6 eşleme).

### §A.2 JavaScript (Node.js) — `console` → dosya/OS ayrımı

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/process.html
// Not: Node'un yerleşik syslog yoktur — stdout/stderr journald'a pipe edilir (systemd).
import { EOL } from 'node:os';

export function osLogLine(level, event, ctx = {}) {
  const rec = JSON.stringify({ ts: new Date().toISOString(), level, event, ...ctx });
  // stdout → journald (systemd) / stderr → konsol; Node syslog(3) çağırmaz (⚠️ bilinçli ayrım)
  if (level === 'error' || level === 'critical') {
    process.stderr.write(rec + EOL);   // hata kanalı
    return;
  }
  process.stdout.write(rec + EOL);     // bilgi kanalı
}
```

**Sınıf dersi:** Node'da "OS log kanalı" **stdout/stderr üzerinden** kurulur (systemd/journald bunu
yakalar); Node kendisi `syslog(3)` çağırmaz. Bu ayrım gizlenmez.

### §A.3 Python — `logging.handlers.SysLogHandler`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man3/syslog.3.html
import logging
import logging.handlers

def build_os_logger() -> logging.Logger:
    log = logging.getLogger("coremusic.os")
    log.setLevel(logging.INFO)
    try:
        h = logging.handlers.SysLogHandler(address="/dev/log")   # Linux syslog socket
    except (FileNotFoundError, ConnectionRefusedError) as e:
        raise RuntimeError("syslog socket yok (journald/dev-log)") from e  # açık hata
    h.setFormatter(logging.Formatter("coremusic[%(process)d]: %(levelname)s %(message)s"))
    log.addHandler(h)
    return log
```

**Not:** `/dev/log` Linux özelidir; macOS'ta aynı socket yolu **farklıdır** (`/var/run/syslog`
adayı — `⚠️ VERIFICATION REQUIRED`, doğrulanmadı → `UNKNOWN`). Windows'ta bu sınıf
kullanılmaz (Event Log hattı §A.5).

### §A.4 Go — `log/syslog`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/log/syslog
//go:build !windows

package oslog

import (
    "log/syslog"
)

func Writer() (*syslog.Writer, error) {
    w, err := syslog.New(syslog.LOG_INFO|syslog.LOG_LOCAL0, "coremusic")
    if err != nil {
        return nil, err // syslog socket yok → hata açıktır
    }
    if err := w.Info("boot tamam"); err != nil {
        w.Close()
        return nil, err
    }
    return w, nil
}
```

**Sınıf dersi:** `log/syslog` paketi **Windows'ta derlenmez** (`//go:build !windows`) —
derleme zamanı koruması, çalışma-zamanı `if`'i değil. Bu, Go'nun platform ayrımı sözleşmesidir
(`turu-kod-seti` §A.4 aynı ilke).

### §A.5 C# — Event Log (Windows kanalı)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/eventlog/event-logging
using System.Diagnostics;

static void WriteOsEvent(string source, string message)
{
    if (!EventLog.SourceExists(source))
    {
        // Kaynak kaydı yetki ister — başarısızlıkta açık hata (sessiz fallback YASAK)
        throw new InvalidOperationException($"olay kaynağı yok: {source}");
    }
    EventLog.WriteEntry(source, message, EventLogEntryType.Error, eventId: 1001);
}
```

**Politika notu:** Event Log **yalnız Windows** sınıfıdır; Linux'ta karşılığı syslog/journald,
macOS'ta `os_log`'dur (§A.6). `⚠️ VERIFICATION REQUIRED`: .NET `EventLog` sınıfı Linux'ta
`PlatformNotSupportedException` üretir — davranış test edilmedi.

### §A.6 Seviye eşleme matrisi (uygulama ↔ OS kanalı — O4)

| # | PSR-3 (`FileHandler`) | syslog severity | Windows Event Log tipi | macOS os_log tipi |
|---|---|---|---|---|
| 1 | emergency (7) | emerg (0) | Error | Fault |
| 2 | alert (6) | alert (1) | Error | Fault |
| 3 | critical (5) | crit (2) | Error | Error |
| 4 | error (4) | err (3) | Error | Error |
| 5 | warning (3) | warning (5) | Warning | Default |
| 6 | notice (2) | notice (4) | Information | Info |
| 7 | info (1) | info (6) | Information | Info |
| 8 | debug (0) | debug (7) | Information | Debug |

**Sonuç (O4):** iki uygulama tablosu OS hiyerarşisiyle **örtüşür ama birebir değildir**
(`-ornekler.md` §15 aynı bulguyu taşır); OS kanalına taşınacaksa **açık eşleme tablosu zorunludur** —
bu matris politika sahibi bu dosyadır.

---

## GRUP B — Monotonic saat (ölçümün doğru kaynağı)

> **Amaç:** "Ne kadar sürdü?" sorusunu **saat kaymasından** korumak. Duyarlı saat (`realtime`)
> NTP/dakika düzeltmesiyle geriye/atlaya bilir; monotonic saat **asla geri gitmez**.
> Repo karşılığı: `$start = hrtime(true)` (`-ornekler.md` §7) — repo doğru aracı zaten kullanıyor.

### §B.1 C — `clock_gettime(CLOCK_MONOTONIC)`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak (Linux): https://man7.org/linux/man-pages/man2/clock_gettime.2.html
   Resmi kaynak (POSIX): https://pubs.opengroup.org/onlinepubs/9799919799/functions/clock_gettime.html */
#include <stdio.h>
#include <errno.h>
#include <string.h>
#include <time.h>

double elapsed_ms(void)
{
    struct timespec a, b;
    if (clock_gettime(CLOCK_MONOTONIC, &a) != 0) {
        fprintf(stderr, "clock_gettime başarısız: %s\n", strerror(errno));
        return -1.0;                       /* hata açık; -1 = geçersiz süre */
    }
    /* iki ölçüm arası: ölçülen iş (IO işlemi / hesap) burada çalışır */
    if (clock_gettime(CLOCK_MONOTONIC, &b) != 0) {
        fprintf(stderr, "clock_gettime başarısız: %s\n", strerror(errno));
        return -1.0;
    }
    return (double)(b.tv_sec - a.tv_sec) * 1000.0
         + (double)(b.tv_nsec - a.tv_nsec) / 1.0e6;
}
```

**Sınıf dersi:** `CLOCK_MONOTONIC` POSIX'te zorunlu değildir (seçenekli saat); `CLOCK_MONOTONIC_RAW`
Linux'a özgüdür ve NTP ayarlamasına dahil **olmaz**. Hangisinin seçileceği O-sorusu → `UNKNOWN`.

### §B.2 Go — `time.Since` (zamanlayıcı)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/time
package timing

import "time"

// ElapsedMS, start'tan geçen süreyi monotonic fark olarak döndürür.
func ElapsedMS(start time.Time) float64 {
    // time.Time taşıyorsa içinde monotonic okuma bulunur (Go 1.9+) — duyarlı saat farkı yok
    return float64(time.Since(start).Nanoseconds()) / 1e6
}

func Measure(fn func()) time.Duration {
    t0 := time.Now()
    fn()
    return time.Since(t0)   // monotonik fark — NTP kaymasından etkilenmez
}
```

**Not:** `time.Since` yalnız iki `time` değeri arasındaki **farkı** kullanır; mutlak zaman
`UnixNano()` ile alınır ve ölçüm için **kullanılmaz** (karışım `UNKNOWN` sınıfı hata).

### §B.3 Rust — `Instant`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/time/struct.Instant.html
use std::time::{Duration, Instant};

pub fn measure_ms(f: impl FnOnce()) -> Duration {
    let start = Instant::now();       // monotonik okuma — duyarlı saat DEĞİL
    f();
    start.elapsed()                    // asla negatif değildir (monotonic garanti)
}
```

**Sınıf dersi:** Rust `Instant` **monotonik** garantisi taşır: `elapsed()` negatif olamaz.
`SystemTime` ise duyarlı saattir (geriye kayabilir) — ikisi karıştırılmaz.

### §B.4 C# — `Stopwatch`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.stopwatch
using System.Diagnostics;

static (double ms, bool ok) Measure(Action fn)
{
    if (!Stopwatch.IsHighResolution)
    {
        // Düşük çözünürlüklü platformda ölçümler yanıltıcı olabilir → bilgi ver, geçir
        Console.Error.WriteLine("uyarı: yüksek çözünürlüklü saat yok");
    }
    var sw = Stopwatch.StartNew();
    fn();
    sw.Stop();
    return (sw.Elapsed.TotalMilliseconds, true);
}
```

**Not:** `Stopwatch` arkasında `QueryPerformanceCounter` kullanır (§B.5 köprüsü);
`Stopwatch.Frequency` çözünürlüğü belirler. `⚠️ VERIFICATION REQUIRED`: çok-çekirdekli
QPC tutarlılığı platforma bağlı test edilmedi.

### §B.5 Windows — `QueryPerformanceCounter` / `GetTickCount64` kavramı

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter
   İkincil: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-gettickcount64
   Not: bu blok Win32 sınıfıdır — Linux derlemesine girmez (#if koruması §B.1 ile aynı desen). */
#if defined(_WIN32)
#include <windows.h>

double elapsed_ms_qpc(LARGE_INTEGER a, LARGE_INTEGER b)
{
    LARGE_INTEGER freq;
    if (!QueryPerformanceFrequency(&freq) || freq.QuadPart == 0) {
        return -1.0;                     /* hata açık */
    }
    return (double)(b.QuadPart - a.QuadPart) * 1000.0 / (double)freq.QuadPart;
}

/* GetTickCount64: 64-bit yükselen sayaç (ms) — QPC'nin daha düşük çözünürlüklü alternatifi */
ULONGLONG ticks_ms(void) { return GetTickCount64(); }
#endif
```

**Sınıf dersi:** QPC (yüksek çözünürlük, kare frekansı) ile `GetTickCount64` (düşük çözünürlük,
64-bit ms) **aynı işi farklı keskinlikte** yapar; hangisinin seçileceği O5 bütçesine bağlı → `UNKNOWN`.

### §B.6 PHP — `hrtime` (repo deseni)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://php.net/manual/en/function.hrtime.php */
declare(strict_types=1);

/** Monotonic süre ölçümü — repo deseni: hrtime(true) (ornekler §7 köprüsü). */
function measure_ms(callable $fn): float
{
    $start = hrtime(true);              // int ns — CLOCK_MONOTONIC karşılığı
    $fn();
    $delta = hrtime(true) - $start;
    if ($delta < 0) {
        throw new RuntimeException('negatif süre — monotonik ihlali'); // imkânsız ama fail-safe
    }
    return $delta / 1_000_000;          // ns → ms
}
```

**Repo köprüsü:** `$durationMs = (hrtime(true) - $start) / 1_000_000` (`-ornekler.md` §7) —
aynı desen. `microtime(true)` (§20) **duyarlı** saattir; yalnız zaman damgası için kullanılır.

### §B.7 JavaScript (Node.js) — `process.hrtime.bigint`

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/process.html
export function measureMs(fn) {
  const start = process.hrtime.bigint();   // BigInt ns — monotonik
  fn();
  const deltaNs = process.hrtime.bigint() - start;
  if (deltaNs < 0n) throw new Error('negatif süre — monotonik ihlali');
  return Number(deltaNs) / 1e6;            // ns → ms
}
```

**Not:** `process.hrtime.bigint()` BigInt döndürür — kayıp yaşatmaz; eski `process.hrtime()`
dizi döndürür ve 32-bit taşma riski taşır (`⚠️ VERIFICATION REQUIRED`: taşma sınırı test edilmedi).

### §B.8 macOS — `mach_absolute_time` `[TASARIM]`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] — [TASARIM] (O2/Q4: macOS repo kanıtı yok) */
/* Resmi kaynak: https://developer.apple.com/documentation/kernel/1462446-mach_absolute_time */
/* Not: mach_absolute_time birimi sabittir; nanosaniyeye çeviri mach_timebase_info ile kurulur. */
#if defined(__APPLE__)
#include <mach/mach_time.h>

double elapsed_ms_mach(uint64_t a, uint64_t b)
{
    mach_timebase_info_data_t tb;
    mach_timebase_info(&tb);                       /* numer/denom oranı */
    uint64_t delta = b - a;                        /* monotonik fark */
    return (double)delta * (double)tb.numer / (double)tb.denom / 1.0e6;
}
#endif
```

**Politika notu:** `mach_absolute_time` macOS/iOS özelidir; Linux'ta `CLOCK_MONOTONIC`,
Windows'ta QPC ile eşleşir (üçlü köprü §B.1 · §B.5 · §B.8). `[TASARIM]` etiketi O2/Q4 nedeniyle korunur.

### §B.9 C++20 — `std::chrono::steady_clock`

```cpp
// https://en.cppreference.com/w/cpp/chrono/steady_clock
// [ORİJİNAL ÖRNEK · derlenmedi]
#include <chrono>

using clk = std::chrono::steady_clock;          // monotonik — duvar saati ayarından bağımsız

double gecen_ms() {
    static const auto bas = clk::now();         // ilk çağrıda sabitlenir
    return std::chrono::duration<double, std::milli>(clk::now() - bas).count();
}
```

**Politika notu:** `steady_clock` ilerleyen saate bağlı değildir (NTP/duvar saati sıçraması
ölçümü bozmaz) — §B.1 `CLOCK_MONOTONIC` ile aynı kavram, C++ sarmalayıcısı. `high_resolution_clock`
kullanılmaz: şeffaf `steady_clock` alias'ı olabilir ama **sözleşme değildir**.

### §B.10 Zig — `std.time.Timer`

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/time.zig
// [ORİJİNAL ÖRNEK · derlenmedi]
const std = @import("std");

pub fn gecen_ns() !u64 {
    var timer = try std.time.Timer.start();     // start() Error!Timer — monotonik sayaç
    return timer.read();                        // read(*Timer) u64 — başlangıçtan itibaren ns
}
```

**Politika notu:** Zig stdlib zaman ölçümü `Timer` üzerinden yürür; mutlak duvar saati
`std.time.timestamp` ayrı yüzeydir (ölçümde kullanılmaz). İmzalar ham kaynaktan doğrulandı
(time.zig master).

### §B.11 Swift — `ContinuousClock`

```swift
// https://developer.apple.com/documentation/swift/continuousclock
// [ORİJİNAL ÖRNEK · derlenmedi]
import Swift

func gecen_ns() -> Int64 {
    let bas = ContinuousClock.now               // static property (Swift 5.7+)
    let gecen = ContinuousClock.now - bas       // Duration — monotonik fark
    return Int64(gecen.components.attoseconds / 1_000_000_000)
        + Int64(gecen.components.seconds) * 1_000_000_000
}
```

**Politika notu:** `ContinuousClock` uyanma saatine (suspending) göre ölçüm için doğru olanıdır;
`SuspendingClock` arka planda duraklar. §B.8 `mach_absolute_time`'in modern sarmalayıcısı.
`⚠️ VERIFICATION REQUIRED`: `Duration.components` alan adları sürümle değişebilir — doğrulanmadı.

### §B.12 Java/Kotlin — `System.nanoTime`

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi]
static long gecen_ns() {
    long bas = System.nanoTime();               // monotonik sayaç — duvar saati DEĞİL
    // ... ölçülecek iş
    return System.nanoTime() - bas;             // yalnız FARK anlamlıdır
}
```

```kotlin
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi]
fun gecenNs(): Long {
    val bas = System.nanoTime()
    // ... ölçülecek iş
    return System.nanoTime() - bas
}
```

**Politika notu:** `nanoTime` mutlak zaman değildir — dönüş değeri yalnızca iki okuma arasındaki
fark için anlam taşır. `currentTimeMillis` duvar saatidir, ölçümde yasak (aynı ayrım §B.1'de
`CLOCK_MONOTONIC` ↔ `CLOCK_REALTIME` olarak geçer).

---

## GRUP C — Ölçümler / sayaç (procfs ↔ Performance Counters)

> **Amaç:** Sistemin **şu anki** durumunu (CPU, bellek, G/Ç) okumak — uygulama içi süre değil.
> Repo karşılığı: **yok** (`-ornekler.md` §11: `/proc okuma` 0 isabet) — bu grup tamamen hedeftir.

### §C.1 Python — procfs + `getrusage`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak (procfs): https://man7.org/linux/man-pages/man5/proc.5.html
# Resmi kaynak (getrusage): https://man7.org/linux/man-pages/man2/getrusage.2.html
import os
import resource

def read_proc_cpu() -> dict[str, int]:
    """ /proc/stat ilk satırı: toplam CPU sayaçları (Linux — procfs). """
    try:
        with open("/proc/stat", "r", encoding="ascii") as f:
            line = f.readline()
    except FileNotFoundError as e:
        raise RuntimeError("procfs yok (Linux dışı sistem?)") from e  # açık hata
    parts = line.split()
    if not parts or parts[0] != "cpu":
        raise ValueError("beklenmeyen /proc/stat biçimi")
    return {name: int(val) for name, val in zip(parts[1:], parts[1:], strict=False)}

def self_usage() -> dict[str, float]:
    """ Sürecin kendi CPU/zaman kullanımı — getrusage(RUSAGE_SELF). """
    ru = resource.getrusage(resource.RUSAGE_SELF)
    return {"user_s": ru.ru_utime, "sys_s": ru.ru_stime, "maxrss_mb": ru.ru_maxrss / 1024}
```

**Sınıf dersi:** `ru_maxrss` birimi **Linux'ta KiB, macOS'ta bayttır** — bu fark `⚠️ VERIFICATION
REQUIRED` sınıfındadır ve §3 matrisinde macOS sütunu `[TASARIM]` kalır.

### §C.2 C# — `PerformanceCounter` (Windows Performance Counters)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.performancecounter
// İkincil: https://learn.microsoft.com/en-us/windows/win32/perfctrs/
using System.Diagnostics;

static (float cpuPercent, float memMb) SampleCounters()
{
    using var cpu = new PerformanceCounter("Processor", "% Processor Time", "_Total");
    using var mem = new PerformanceCounter("Memory", "Available MBytes");
    _ = cpu.NextValue();                       // ilk okuma her zaman 0'dır — ısınma okuması
    Thread.Sleep(200);                         // ikinci okuma aralığı
    float cpuPct = cpu.NextValue();
    float memMb  = mem.NextValue();
    if (cpuPct < 0) throw new InvalidOperationException("geçersiz CPU sayacı");
    return (cpuPct, memMb);
}
```

**Sınıf dersi:** Performance Counter'ın **ilk `NextValue()` okuması her zaman 0**'dır —
ısınma okuması zorunludur (sıklıkla atlanan tuzak). `⚠️ VERIFICATION REQUIRED`: sayaç adları
yerelleştirmeye bağlıdır (İngilizce varsayılan) → doğrulanmadı → `UNKNOWN`.

### §C.3 Bash — procfs okuma (Linux)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man5/proc.5.html
set -euo pipefail

MEMINFO="/proc/meminfo"
[ -r "$MEMINFO" ] || { echo "procfs yok: $MEMINFO" >&2; exit 1; }   # fail-safe

total_kb="$(awk '/^MemTotal:/ {print $2}' "$MEMINFO")"
avail_kb="$(awk '/^MemAvailable:/ {print $2}' "$MEMINFO")"

if [ -z "$total_kb" ] || [ -z "$avail_kb" ]; then
  echo "meminfo okunamadı" >&2
  exit 1
fi

used_pct=$(( 100 - (avail_kb * 100 / total_kb) ))
echo "bellek kullanımı: %${used_pct} (toplam ${total_kb} KiB)"

# Süreç-bazlı sayaç: /proc/self/stat (14. alan = utime, 15. = stime, birim: clock tick)
read -r _comm _rest < /proc/self/stat || { echo "stat okunamadı" >&2; exit 1; }
```

**Not:** `/proc` **yalnız Linux**'tadır (macOS `vm_stat`, Windows Performance Counter).
Bash örneği Linux sınıfıdır; §3 matrisinde macOS/Windows sütunları bu blok için `—` kalır.

### §C.4 PHP — `getrusage` köprüsü (POSIX)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://man7.org/linux/man-pages/man2/getrusage.2.html */
declare(strict_types=1);

/** Sürecin kendi CPU bütçesi — PHP'nin getrusage sarmalayıcısı (POSIX). */
function self_cpu_seconds(): array
{
    $ru = getrusage();                       // false dönebilir (Windows)
    if ($ru === false) {
        throw new RuntimeException('getrusage bu platformda yok'); // fail-safe
    }
    return [
        'user_s' => $ru['ru_utime.tv_sec'] + $ru['ru_utime.tv_usec'] / 1e6,
        'sys_s'  => $ru['ru_stime.tv_sec'] + $ru['ru_stime.tv_usec'] / 1e6,
    ];
}
```

**Repo köprüsü:** Repo'da `getrusage` **0 isabet** (`-ornekler.md` §11); bu blok hedef referanstır.
`⚠️ VERIFICATION REQUIRED`: PHP sarmalayıcısının Windows davranışı doğrulanmadı → `UNKNOWN`.

### §C.5 Ölçüm türü ayrım matrisi (uygulama süresi ↔ OS sayacı)

| # | Ölçüm | Kaynak | Grup | OS sayacı mı? |
|---|---|---|---|---|
| 1 | middleware süresi (`hrtime`) | uygulama | B | hayır |
| 2 | AI workflow süresi (`microtime`) | uygulama | B (duyarlı) | hayır |
| 3 | CPU kullanım yüzdesi | procfs / Perf Counter | C | **evet** |
| 4 | bellek kullanımı | /proc/meminfo / Available MBytes | C | **evet** |
| 5 | süreç CPU bütçesi | getrusage | C | **evet** |
| 6 | syscall latency | repo'da yok (trace-surucu §3) | — | OS katmanı, bu setin dışı |

**Politika notu (GRUP C):** Ayrım zorunludur: GRUP B **süre** ölçer, GRUP C **durum** sayacı okur.
İkisinin karıştırılması yanıltıcı metrik üretir (O5).

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux (birincil) | Windows | macOS | ReactOS (Win32 sınıfı) | Mekanizma (§) |
|---|---|---|---|---|---|
| PHP | ✅ openlog/syslog | ⚠️ Event Log yok — dosya hattı | ⚠️ [TASARIM] | — | A.1 · B.6 · C.4 |
| JS (Node) | ✅ stdout → journald | ✅ stderr | ✅ | — | A.2 · B.7 |
| Python | ✅ SysLogHandler | ⚠️ /dev/log yok | ⚠️ [TASARIM] | — | A.3 · C.1 |
| Go | ✅ `log/syslog` (`!windows`) | ❌ (derlenmez) | ⚠️ sınıf | — | A.4 · B.2 |
| C# | ❌ EventLog Linux'ta yok | ✅ Event Log + Perf Counter | ❌ | — | A.5 · B.4 · C.2 |
| C | ✅ CLOCK_MONOTONIC | ✅ QPC kolu (#if) | ✅ mach kolu `[TASARIM]` | ⚠️ | B.1 · B.5 · B.8 |
| Rust | ✅ Instant | ✅ | ✅ | — | B.3 |
| C++20 | ✅ `steady_clock` (std) | ✅ (std çapraz) | ✅ sınıf `[TASARIM]` (Q4) | — | B.9 |
| Zig | ✅ `std.time.Timer` | ⚠️ std çapraz — koşucu kanıt yok | ⚠️ [TASARIM] | — | B.10 |
| Swift | ✅ `ContinuousClock` (Glibc/Darwin) | ⚠️ sınıf — koşucu kanıt yok | ✅ sınıf `[TASARIM]` (Q4) | — | B.11 |
| Java/Kotlin | ✅ `System.nanoTime` (JVM) | ✅ (JVM çapraz) | ✅ (JVM çapraz) `[TASARIM]` | — | B.12 |
| Bash | ✅ procfs | ❌ (yerel yok) | ❌ (procfs yok) | — | C.3 |

**Okuma kuralı:** ✅ = örnek derlendiği sınıfı hedefler (`[ORİJİNAL ÖRNEK · derlenmedi]` etiketi
**kaldırılmaz**) · ⚠️ = sınıf geçerli ama kanıt yok · ❌ = sınıf dışı (kural gereği, hata değil).

---

## §4. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | OS-level log/metrik toplama hedef mimaride var mı? (grep 0) | UNKNOWN — ornekler §11 |
| O2 | macOS üretim/koşucu kanıtı (Q4) | UNKNOWN — os_log/mach sütunları [TASARIM] |
| O3 | syslog/Event Log'a ÇİFT yazım (dosya + OS kanalı) maliyeti | UNKNOWN — varsayılan kapalı |
| O4 | Seviye eşlemesi (PSR-3 ↔ syslog) birebir değil | UNKNOWN — §A.6 matrisi açık eşleme ister |
| O5 | procfs/Perf Counter okuma sıklığı + bütçesi | UNKNOWN — [TASARIM], ölçüm yok |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

---

## §5. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | Linux `syslog(3)` | https://man7.org/linux/man-pages/man3/syslog.3.html | 2026-10-14 ✅ |
| 2 | Win32 Event Logging | https://learn.microsoft.com/en-us/windows/win32/eventlog/event-logging | 2026-10-14 ✅ |
| 3 | Apple `os_log` | https://developer.apple.com/documentation/os/logging | 2026-10-14 ✅ |
| 4 | Linux `clock_gettime(2)` | https://man7.org/linux/man-pages/man2/clock_gettime.2.html | 2026-10-14 ✅ |
| 5 | POSIX `clock_gettime` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/clock_gettime.html | 2026-10-14 ✅ |
| 6 | Win32 `QueryPerformanceCounter` | https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter | 2026-10-14 ✅ |
| 7 | PHP `hrtime()` | https://php.net/manual/en/function.hrtime.php | 2026-10-14 ✅ |
| 8 | Node `process` | https://nodejs.org/api/process.html | 2026-10-14 ✅ |
| 9 | Go `time` | https://pkg.go.dev/time | 2026-10-14 ✅ |
| 10 | Rust `Instant` | https://doc.rust-lang.org/std/time/struct.Instant.html | 2026-10-14 ✅ |
| 11 | .NET `Stopwatch` | https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.stopwatch | 2026-10-14 ✅ |
| 12 | systemd-journald | https://freedesktop.org/software/systemd/man/systemd-journald.service.html | 2026-10-14 ✅ |
| 13 | Win32 Performance Counters | https://learn.microsoft.com/en-us/windows/win32/perfctrs/ | 2026-10-14 ✅ |
| 14 | .NET `PerformanceCounter` | https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.performancecounter | 2026-10-14 ✅ |
| 15 | Linux `getrusage(2)` | https://man7.org/linux/man-pages/man2/getrusage.2.html | 2026-10-14 ✅ |
| 16 | Go `log/syslog` | https://pkg.go.dev/log/syslog | 2026-10-14 ✅ |
| 17 | Win32 `GetTickCount64` | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-gettickcount64 | 2026-10-14 ✅ |
| 18 | `mach_absolute_time` | https://developer.apple.com/documentation/kernel/1462446-mach_absolute_time | 2026-10-14 ✅ |
| 19 | Linux `proc(5)` | https://man7.org/linux/man-pages/man5/proc.5.html | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler örnek bloklarının **günü** doğrulanmış arayüz referansıdır;
blokların kendisi orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`).

---

## §6. İlişkili dosyalar

- Repo örnekleri (kanıt köprüsü): [`isletim-sistemi-observability-ornekler.md`](isletim-sistemi-observability-ornekler.md)
- Kapsam/sınır: [`isletim-sistemi-observability-kapsam.md`](isletim-sistemi-observability-kapsam.md)
- Doğrulama: [`isletim-sistemi-observability-dogrulama.md`](isletim-sistemi-observability-dogrulama.md)
- Dizin: [`index.md`](index.md)
- OS ailesi karşılaştırma (tekrar yok): `../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`
- Süre/zamanlama çapraz köprü: `../isletim-sistemi-plan/isletim-sistemi-plan-kod-seti.md` (§GRUP B)
- Log sahibi alt-dal: `log/log-surucu.md` (§3 severity standardı — ornekler §23 köprüsü)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
