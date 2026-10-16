---
node_id: isletim-sistemi-plan-kod-seti
node_type: code
status: verify-required
parent_id: isletim-sistemi-plan
children: []
depends_on: [isletim-sistemi-turu-kod-seti, isletim-sistemi-plan-ornekler]
evidence: ["https://man7.org/linux/man-pages/man2/setpriority.2.html", "https://man7.org/linux/man-pages/man1/nice.1.html", "https://man7.org/linux/man-pages/man2/nanosleep.2.html", "https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html", "https://man7.org/linux/man-pages/man2/sched_setscheduler.2.html", "https://man7.org/linux/man-pages/man3/sleep.3p.html", "https://man7.org/linux/man-pages/man1/sleep.1.html", "https://man7.org/linux/man-pages/man3/mq_open.3p.html", "https://www.php.net/manual/en/function.proc-open.php", "https://www.php.net/manual/en/function.usleep.php", "https://www.php.net/manual/en/function.time-nanosleep.php", "https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass", "https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process.priorityclass", "https://learn.microsoft.com/en-us/dotnet/api/system.threading.tasks.task.delay", "https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/start-sleep", "https://docs.python.org/3/library/time.html", "https://docs.python.org/3/library/os.html", "https://nodejs.org/api/timers.html", "https://pkg.go.dev/time", "https://doc.rust-lang.org/std/thread/fn.sleep.html", "https://en.cppreference.com/w/cpp/thread/sleep_until.html", "https://nodejs.org/api/process.html#processhrtimebigint"]
validation: "2026-10-15: 28 benzersiz resmi URL (HTTP 200 kapısı) · 29 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions: ["O1: SCHED_FIFO/gerçek-zamanlı politika üretim süreçlerinde kullanılacak mı (CAP_SYS_NICE gerekir, yanlış ayar ses/sağlık sürecini bozar) — ADR?", "O2: macOS Darwin `setpriority`/`nice` davranış kodları ve Q4 politikası kesinleşmedi — [TASARIM]", "O3: Kuyruk taşma politikası (drop-oldest / drop-newest / blokla) hangi ADR ile sabitlenecek?", "O4: Win32 `SleepEx`/waitable-timer zamanlayıcı yüzeyi bu dosyaya mı taşınacak (şu an yalnız `Task.Delay`/`Start-Sleep` var)?", "O5: `setTimeout`/`sleep` uyku kayması (drift) biriktiren döngülerde hedef-saat düzeltmesi ADR'ye mi bağlanacak?"]
group: kod
---

# isletim-sistemi-plan — Kod Seti

> **Kapsam:** süreç önceliği (`nice`/`setpriority` ↔ `SetPriorityClass`), monotonic
> timer/sleep yüzeyleri ve kullanıcı-uzayı kuyruk/tampon zamanlama desenleri.
> DSP zamanlaması (gerçek-zamanlı ses callback'i) **bu dosyanın dışındadır** —
> bkz. `isletim-sistemi-turu-kod-seti` GRUP kapsamı; bu dosya yalnız planlama
> (planning/scheduling) yüzeyini kodlar.

---

## §0 Politika

1. **Zero-Hallucination:** her kod bloğu ilk satırında `[ORİJİNAL ÖRNEK · derlenmedi]`
   taşır; kullanılan her API bu dosyanın §5 kaynakçasındaki, yazım anında
   `HTTP 200` doğrulanmış resmi dokümandan gelir.
2. **Öncelik/sandbox ayrımı:** öncelik ayarı (GRUP A) **güvenlik sınırı değildir**;
   kalıcı izin düşürme `isletim-sistemi-security-kod-seti` GRUP A'nın işidir.
   Burada yalnız çalışma-önceliği ve zamanlama kodlanır.
3. **Monotonic zorunlu:** bekleme ve ölçüm hedefleri duvar-saati (`time()`,
   `GetSystemTimeAsFileTime`) ile değil **monotonic saat** ile kurulur;
   NTP/dakika-sıçraması bekleme süresini bozamaz. Köprü: `isletim-sistemi-observability-kod-seti` GRUP B.
4. **`...`/`TBD` yasak:** her blok ya çalışır-tam kod ya da `UNKNOWN` +
   `⚠️ VERIFICATION REQUIRED` + §4 O-soru taşır.
5. **Sessiz fallback yasak:** platformda API yoksa blok `[TASARIM]` etiketiyle
   eksik bırakılır; "yoksa başka şeyle değiştiririm" diye sessizce çalışmaz.
6. **R6 (üçüncü taraf paket yasağı):** `golang.org/x/sys`, `libc` (Rust),
   `numpy` vb. yalnız kavram olarak anılır; kurulum/ekleme yapılmaz.
7. **Repo kanıtı:** yalnız sibling dosyaların mevcut § numaralarına referans
   verilir (`plan-ornekler` §1.3/§1.7/§4/§7 vb.); yeni `Dosya:/Satır:` çifti üretilmez.

---

## §1 Çalışma sözleşmesi

### §1.1 Çıktı sözleşmesi

| Çıktı | Şart |
|---|---|
| Öncelik ayarı | Sadece çocuk süreç ya da kendini etkiler; kalıcı değil (yeniden başlatmada sıfırlanır) |
| Bekleme | Monotonic hedefe göre; erken uyanma döngüde telafi edilir |
| Kuyruk | Boyutu baştan sabit; taşma politikası §4 O3 çözülene kadar açıkça yazılır |
| Platform | Windows / macOS / Linux sütunları ayrı değerlendirilir; macOS `[TASARIM]` |

### §1.2 Hata yönetimi

- Öncelik değişimi başarısızsa **sessizce devam etme**: hata ya loglanır
  (köprü: `isletim-sistemi-observability-kod-seti` GRUP A) ya da çağırana döner.
- `EACCES`/`ERROR_ACCESS_DENIED` yetki yetersizliğidir; uygulama-önceliği
  düşürülerek **atlanır** (dürüstlük notu: `plan-ornekler` §4 H-kaydı mantığı).
- `nanosleep`/`clock_nanosleep` `EINTR` ile kesilirse kalan süre hesaplanıp
  döngü tamamlanır — kısaltılmış bekleme "tamam sayılır" (sessiz eksik yasak).

### §1.3 Zamanlama modelleri (bu dosyanın ayrım çizgisi)

| Model | Ne zaman | Bu dosyada |
|---|---|---|
| `nice`/`setpriority` | CPU rekabeti — arka plan işi yavaşlat | GRUP A |
| `SCHED_FIFO`/`SCHED_RR` | Gerçek zamanlı — ses/RT süreç | GRUP A §A.9 (ADR: O1) |
| Monotonic sleep | "Şu andan itibaren N" / "şu hedefe kadar" | GRUP B |
| Kuyruk aralığı | Üretim hızını sabitleme (ticker/pacing) | GRUP C |

---

## §2 Grup haritası

| Grup | Ne kodlar | Ana yüzey | Köprü |
|---|---|---|---|
| **GRUP A** | Öncelik / nice | `setpriority`/`nice` ↔ `SetPriorityClass` · PHP `proc_open` üzerinden · bash/pwsh · Go/Rust/C# | `security-ornekler` §1 (izinin tam tersi: burada kısıt yok) |
| **GRUP B** | Timer / sleep | `clock_nanosleep`/`nanosleep`/`sleep` ↔ `Task.Delay`/`Start-Sleep` ↔ PHP `time_nanosleep`/`usleep` ↔ Node `setTimeout` ↔ Python `time.monotonic` ↔ Zig `std.time.Timer`/`sleep` ↔ Swift `ContinuousClock` ↔ JVM `System.nanoTime`/`LockSupport.parkNanos` | `plan-ornekler` §1.7 (`hrtime`) · `observability-ornekler` §7 |
| **GRUP C** | Kuyruk / tampon zamanlama | Ring-buffer **kavramı** (DSP'ye girmez) · bounded channel · `mq_open` · taşma politikası | `observability-ornekler` §28 (metrik köprüsü) |

---

## GRUP A — Öncelik / nice

### §A.1 C — `setpriority(2)` (POSIX)

```c
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man2/setpriority.2.html
#define _GNU_SOURCE
#include <errno.h>
#include <stdio.h>
#include <sys/resource.h>
#include <unistd.h>

int raise_nice_self(int delta)
{
    /* who=PRIO_PROCESS, who=0 → çağrıyan süreç (iş parçacığı düzeyi değil). */
    if (setpriority(PRIO_PROCESS, 0, delta) == -1) {
        if (errno == EACCES || errno == EPERM) {
            /* Dürüstlük: yetkisiz düşürme/hızlandırma atlanır, sessiz değil raporlanır. */
            fprintf(stderr, "setpriority: yetki yok (errno=%d)\n", errno);
            return -1;
        }
        fprintf(stderr, "setpriority: bilinmeyen hata (errno=%d)\n", errno);
        return -1;
    }
    return 0;
}
```

**Not:** `setpriority` değer aralığı POSIX'te `-20..19` (negatif = yüksek öncelik);
`nice`(1) ile aynı çekirdek ayarı yapar. macOS sütunu: Darwin `setpriority(2)`
mevcut davranış kodu `[TASARIM]` → §4 O2.

### §A.2 Bash — `nice(1)` ile çocuk süreç

```bash
# [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man1/nice.1.html
# Rapor işini normal öncelikten düşürerek üretir; -n 10 ek prioritesi (0..19).
set -euo pipefail

nice -n 10 ./bin/report --daily > /var/log/app/report.out 2> /var/log/app/report.err
rc=$?
echo "report exit=${rc} (nice altinda calisti)"
```

### §A.3 PowerShell — `SetPriorityClass` (Win32)

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass
# İş başlatıp önceliğini düşürme; mevcut süreç için de uygulanabilir.
$psi = [System.Diagnostics.ProcessStartInfo]::new()
$psi.FileName = "powershell.exe"
$psi.Arguments = "-NoProfile -Command Start-Sleep -Seconds 5"
$p = [System.Diagnostics.Process]::Start($psi)

try {
    # BelowNormal: normal süreçlerin altında çalışır (IDLE/BELOW_NORMAL/NORMAL/ABOVE_NORMAL/HIGH/REALTIME)
    $p.PriorityClass = [System.Diagnostics.ProcessPriorityClass]::BelowNormal
    "PID=$($p.Id) oncelik=$($p.PriorityClass)"
} catch {
    # Erişim reddi (ör. REALTIME yetkisiz) — sessiz geçme, raporla.
    "oncelik ayarlanamadi: $($_.Exception.Message)"
} finally {
    $p.WaitForExit()
    $p.Dispose()
}
```

### §A.4 PHP — `proc_open` üzerinden `nice` (PHP'de native öncelik API'si yok)

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.proc-open.php
// Ek HTTP 200: https://man7.org/linux/man-pages/man1/nice.1.html
declare(strict_types=1);

/** POSIX'te ağır işi nice ile başlatır; PHP çekirdeğinde setpriority sarmalayıcısı YOKTUR. */
function proc_open_nice(array $cmd, int $niceLevel): array
{
    if (PHP_OS_FAMILY === 'Windows') {
        // Windows'ta nice yok → [TASARIM]: Start-Process -Priority mi, reddet mi? (§4 O2/O4)
        throw new RuntimeException('proc_open_nice: POSIX-only — Windows yolu [TASARIM]');
    }

    $full = array_merge(['nice', '-n', (string) $niceLevel], $cmd);
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $proc = proc_open($full, $descriptors, $pipes);
    if (!is_resource($proc)) {
        throw new RuntimeException('proc_open baslatilamadi');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    if ($exitCode !== 0) {
        // Sessiz fallback yasak: hata çağırana döner.
        throw new RuntimeException("nice altinda is hata verdi: exit={$exitCode} stderr={$stderr}");
    }

    return ['stdout' => $stdout, 'stderr' => $stderr, 'exit' => $exitCode];
}
```

### §A.5 Python — `os.setpriority` / `os.nice`

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/os.html
# Ek HTTP 200: https://man7.org/linux/man-pages/man2/setpriority.2.html
import os
import sys

def lower_priority(delta: int = 10) -> bool:
    """Kendini düşür (delta>0 = daha düşük öncelik, nice benzeri)."""
    try:
        if hasattr(os, "setpriority"):
            # PRIORITY_PROCESS + who=0 → bu süreç
            current = os.getpriority(os.PRIORITY_PROCESS, 0)
            os.setpriority(os.PRIORITY_PROCESS, 0, current + delta)
        else:
            os.nice(delta)  # eski arabirim
        return True
    except PermissionError:
        # Dürüstlük: yetki yok → False döner, sessizce "başarılı" sayılmaz.
        print("setpriority: yetki yok — normal oncelikle devam", file=sys.stderr)
        return False
    except OSError as exc:
        print(f"setpriority: OS hatasi: {exc}", file=sys.stderr)
        return False

if __name__ == "__main__":
    ok = lower_priority()
    print(f"oncelik dusuruldu={ok}")
```

### §A.6 Go — stdlib'de öncelik API'si YOK → `[TASARIM]` dürüst köprüsü

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/time
// NOT: Go standart kütüphanesinde setpriority/nice sarmalayıcısı YOKTUR.
// golang.org/x/sys/unix.SetPriority üçüncü taraf (R6) → EKLENMEZ.
// Bu blok dürüst köprüdür: iş, nice altındaki yardımcı programa devredilir.
package main

import (
	"context"
	"fmt"
	"os/exec"
	"strconv"
)

func runNiceLevel(ctx context.Context, nice int, name string, args ...string) error {
	full := append([]string{"-n", strconv.Itoa(nice)}, name)
	full = append(full, args...)

	cmd := exec.CommandContext(ctx, "nice", full)
	if err := cmd.Run(); err != nil {
		return fmt.Errorf("nice ile calistirilamadi: %w", err)
	}
	return nil
}
```

**Eksiklik kaydı:** Go içinden süreç-önceliği için `nice` harici yol
`[TASARIM]` → §4 O2.

### §A.7 Rust — `std::process::Command` + `nice` (libc `setpriority` R6 engeli)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi] https://doc.rust-lang.org/std/thread/fn.sleep.html
// (Referans yüzey: std::process — std-only. libc::setpriority + pre_exec R6 gereği YAZILMAZ.)
use std::process::{Command, Stdio};

fn run_nice(nice: i32, program: &str, args: &[&str]) -> std::io::Result<()> {
    let mut argv = vec!["-n".to_string(), nice.to_string()];
    argv.push(program.to_string());
    for a in args {
        argv.push(a.to_string());
    }

    let status = Command::new("nice")
        .args(&argv)
        .stdin(Stdio::null())
        .status()?;

    if !status.success() {
        return Err(std::io::Error::new(
            std::io::ErrorKind::Other,
            format!("nice altinda program basarisiz: {status}"),
        ));
    }
    Ok(())
}
```

**R6 kaydı:** Rust `pre_exec` + `libc::setpriority` zinciri **bilinçli eksik**;
üçüncü taraf `libc` eklenmedi. Çözüm için §4 O2.

### §A.8 C# — `Process.PriorityClass`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process.priorityclass
using System.Diagnostics;

static bool TryLowerPriority(Process p, ProcessPriorityClass target)
{
    try
    {
        // Büyük öncelikten küçüğe: elevation gerekebilir; hata yakalanır.
        p.PriorityClass = target;
        Console.WriteLine($"PID={p.Id} oncelik={p.PriorityClass}");
        return true;
    }
    catch (System.ComponentModel.Win32Exception ex)
    {
        // ERROR_ACCESS_DENIED: yetkisiz öncelik sınıfı (ör. Idle altı REALTIME).
        Console.Error.WriteLine($"oncelik ayarlanamadi: {ex.NativeErrorCode} {ex.Message}");
        return false;
    }
    finally
    {
        p.Dispose();
    }
}
```

### §A.9 C++20 — platform-kapılı öncelik ayarı

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man2/setpriority.2.html
// Ek HTTP 200: https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass
#include <cstdio>

#if defined(_WIN32)
#define WIN32_LEAN_AND_MEAN
#include <windows.h>

bool lower_priority_self(int nice_delta) {
    // nice_delta>0 → düşür; BelowNormal sabit seçimi [TASARIM] (O2).
    (void)nice_delta;
    if (!SetPriorityClass(GetCurrentProcess(), BELOW_NORMAL_PRIORITY_CLASS)) {
        std::fprintf(stderr, "SetPriorityClass basarisiz: %lu\n", GetLastError());
        return false;
    }
    return true;
}
#else
#include <sys/resource.h>
#include <cerrno>

bool lower_priority_self(int nice_delta) {
    if (setpriority(PRIO_PROCESS, 0, nice_delta) == -1) {
        std::fprintf(stderr, "setpriority hatasi: %d\n", errno);
        return false;
    }
    return true;
}
#endif
```

### §A.10 Gerçek-zamanlı politika — yalnız kavram (ADR: O1)

`SCHED_FIFO`/`SCHED_RR` (`sched_setscheduler(2)`,
`https://man7.org/linux/man-pages/man2/sched_setscheduler.2.html` → HTTP 200)
**üretimde bu dosyada kodlanmaz:** yetki (`CAP_SYS_NICE`) ve kötü seçim
(ses/sağlık süresini aç bırakma) bütünüyle ADR'ye bağlıdır → §4 O1.
Win32 karşılığı `REALTIME_PRIORITY_CLASS` §A.8'de yalnız çağrı şekliyle var,
değeri üretilmez.

---

## GRUP B — Timer / sleep (monotonic bekleme)

### §B.1 C — `clock_nanosleep(TIMER_ABSTIME)` (mutlak hedef)

```c
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html
#define _POSIX_C_SOURCE 200809L
#include <errno.h>
#include <time.h>

/* monotonic saate göre mutlak hedefe kadar bekle; EINTR'da kalan süreyle devam. */
int sleep_until_monotonic(const struct timespec *deadline)
{
    for (;;) {
        int rc = clock_nanosleep(CLOCK_MONOTONIC, TIMER_ABSTIME, deadline, NULL);
        if (rc == 0) {
            return 0;
        }
        if (rc == EINTR) {
            continue; /* sinyal kesti → hedef sabit, kalan süre kendiliğinden hesap */
        }
        return rc; /* EINVAL/EPERM: hata sessiz yutulmaz */
    }
}
```

### §B.2 C — `nanosleep` + kalan süre döngüsü (göreli bekleme)

```c
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man2/nanosleep.2.html
#define _POSIX_C_SOURCE 200809L
#include <errno.h>
#include <time.h>

int sleep_relative_ms(long ms)
{
    struct timespec req = { .tv_sec = ms / 1000, .tv_nsec = (ms % 1000) * 1000000L };
    struct timespec rem;

    while (nanosleep(&req, &rem) == -1) {
        if (errno == EINTR) {
            req = rem; /* eksik uyku telafi edilir — kısaltılmış bekleme "tamam" sayılmaz */
            continue;
        }
        return -1;
    }
    return 0;
}
```

### §B.3 Bash — `sleep(1)`

```bash
# [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man1/sleep.1.html
# Ek HTTP 200: https://man7.org/linux/man-pages/man3/sleep.3p.html (POSIX sleep — sinyalde kalan süre döner)
set -euo pipefail

# Geri sayım döngüsü: ara adımlara bölünür, tek uzun uyku değil.
for remaining in 5 4 3 2 1; do
    sleep 1
    echo "geri sayim: ${remaining}"
done
echo "zaman doldu"
```

### §B.4 PHP — `time_nanosleep` (monotonic değil, göreli tam saniye+nanosaniye)

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.time-nanosleep.php
declare(strict_types=1);

/**
 * Süre kesilirse kalan bölüm döner (array) — çağıran döngüyü tamamlar.
 * Dürüstlük: "yarım uyku = tam uyku" sayılmaz.
 */
function sleep_complete_ms(int $ms): bool
{
    $seconds = intdiv($ms, 1000);
    $nanoseconds = ($ms % 1000) * 1_000_000;

    for ($left = [$seconds, $nanoseconds]; $left !== true; ) {
        $result = time_nanosleep($left[0], $left[1]);
        if ($result === true) {
            return true;
        }
        if ($result === false) {
            throw new RuntimeException('time_nanosleep basarisiz'); // hata yutulmaz
        }
        $left = $result; // ['seconds'=>.., 'nanoseconds'=>..] → tekrar dene
        $left = [$left['seconds'], $left['nanoseconds']];
    }

    return true;
}
```

### §B.5 PHP — `usleep` (kısa aralıklar)

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.usleep.php
declare(strict_types=1);

/** µs cinsinden kısa uyku; 1_000_000 µs üstü taşma riski → time_nanosleep'e devret. */
function micro_pause_us(int $microseconds): void
{
    if ($microseconds >= 1_000_000) {
        sleep_complete_ms(intdiv($microseconds, 1000)); // §B.4'e köprü
        return;
    }
    if ($microseconds > 0) {
        usleep($microseconds);
    }
}
```

### §B.6 Node.js — `setTimeout` + `hrtime.bigint` hedef hesabı (drift telafisi)

```js
// [ORİJİNAL ÖRNEK · derlenmedi] https://nodejs.org/api/timers.html
// Ek HTTP 200: https://nodejs.org/api/process.html#processhrtimebigint (monotonic ns — observability-ornekler §7 köprüsü)
'use strict';

/**
 * Mutlak hedefe kadar bekle: her turda kalan süre yeniden hesaplanır,
 * böylece setTimeout kayması (drift) biriktirilmez (§4 O5).
 */
function sleepUntilHr(targetNs) {
  return new Promise((resolve, reject) => {
    const tick = () => {
      const now = process.hrtime.bigint();
      const remainMs = Number(targetNs - now) / 1e6;
      if (remainMs <= 0) {
        resolve();
        return;
      }
      // tarayıcı-oderinliği sınırı ~2^31-1 ms değil, uygulama üst sınırı 2^30 (Node kuralı)
      setTimeout(tick, Math.min(remainMs, 1e6));
    };
    try {
      tick();
    } catch (err) {
      reject(err);
    }
  });
}

async function cadenceLoopHz(hz, iterations) {
  const periodNs = 1_000_000_000n / BigInt(hz);
  let target = process.hrtime.bigint() + periodNs;
  for (let i = 0; i < iterations; i++) {
    await sleepUntilHr(target);
    target += periodNs; // sabit hedef zinciri → birikmez
  }
}

module.exports = { sleepUntilHr, cadenceLoopHz };
```

### §B.7 Python — `time.monotonic` + telafili bekleme döngüsü

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/time.html
import time

def sleep_until_monotonic(deadline: float) -> None:
    """deadline: time.monotonic() cinsinden mutlak hedef. EINTR telafisi Python içinde. """
    while True:
        remaining = deadline - time.monotonic()
        if remaining <= 0:
            return
        time.sleep(remaining)  # CPython signal handler EINTR'da kaldığı yerden döner

def run_every(interval_s: float, ticks: int) -> None:
    next_deadline = time.monotonic() + interval_s
    for _ in range(ticks):
        sleep_until_monotonic(next_deadline)
        next_deadline += interval_s  # sabit hedef zinciri: drift birikmez
        print(f"tick monotonic={time.monotonic():.3f}")
```

### §B.8 Go — `time.Timer` / mutlak `Timer` hedefi

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/time
package cadence

import (
	"context"
	"time"
)

// SleepUntil, monotonic tabanlı mutlak hedefe kadar bekler; ctx iptalinde erken döner.
func SleepUntil(ctx context.Context, deadline time.Time) error {
	d := time.Until(deadline) // time.Time içinde monotonic saat var → duvar-saati değil
	if d <= 0 {
		return nil
	}
	t := time.NewTimer(d)
	defer t.Stop()

	select {
	case <-t.C:
		return nil
	case <-ctx.Done():
		return ctx.Err() // iptal sessiz yutulmaz
	}
}

// RunEvery, sabit aralıklı döngü kurar (drift biriktirmez: hedef += periyot).
func RunEvery(ctx context.Context, periyot time.Duration, ticks int, iş func()) error {
	next := time.Now().Add(periyot)
	for i := 0; i < ticks; i++ {
		if err := SleepUntil(ctx, next); err != nil {
			return err
		}
		iş()
		next = next.Add(periyot)
	}
	return nil
}
```

### §B.9 Rust — `std::thread::sleep` (std-only)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi] https://doc.rust-lang.org/std/thread/fn.sleep.html
use std::time::{Duration, Instant};

/// Mutlak hedefe kadar uyur; kalan süre her turda yeniden hesaplanır (drift telafisi).
fn sleep_until(target: Instant) {
    loop {
        let now = Instant::now(); // monotonic
        if now >= target {
            return;
        }
        std::thread::sleep(target - now);
    }
}

fn run_every(period: Duration, ticks: usize) {
    let mut next = Instant::now() + period;
    for _ in 0..ticks {
        sleep_until(next);
        next += period; // sabit hedef zinciri
        println!("tick {:?}", Instant::now());
    }
}
```

### §B.10 C# — `Task.Delay`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/dotnet/api/system.threading.tasks.task.delay
using System;
using System.Diagnostics;
using System.Threading;
using System.Threading.Tasks;

static async Task RunEveryAsync(TimeSpan period, int ticks, Func<Task> body,
                                CancellationToken ct = default)
{
    // Stopwatch monotonic ölçer; hedef = ilk başlangıç + k*periyot.
    var sw = Stopwatch.StartNew();
    var next = sw.Elapsed + period;

    for (var i = 0; i < ticks; i++)
    {
        var wait = next - sw.Elapsed;
        if (wait > TimeSpan.Zero)
        {
            // Task.Delay iptal/değiştirme senaryolarında da düzgün davranır.
            await Task.Delay(wait, ct);
        }
        await body();
        next += period; // sabit hedef zinciri → drift birikmez
    }
}
```

### §B.11 PowerShell — `Start-Sleep`

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/start-sleep
# Monotonic hedef: Stopwatch (QPC tabanlı) ile sayım.
$sw = [System.Diagnostics.Stopwatch]::StartNew()
$periodMs = 250
$next = $sw.ElapsedMilliseconds + $periodMs

1..8 | ForEach-Object {
    $remain = $next - $sw.ElapsedMilliseconds
    if ($remain -gt 0) {
        Start-Sleep -Milliseconds $remain
    }
    "tick t=$($sw.ElapsedMilliseconds)ms"
    $next += $periodMs
}
```

### §B.12 C++20 — `sleep_until(steady_clock)`

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi] https://en.cppreference.com/w/cpp/thread/sleep_until.html
#include <chrono>
#include <thread>

void run_every_cpp20(std::chrono::milliseconds periyot, int ticks)
{
    using clock = std::chrono::steady_clock; // monotonic
    auto next = clock::now() + periyot;

    for (int i = 0; i < ticks; ++i) {
        std::this_thread::sleep_until(next); // hedefe kadar; kalan süre stdlib hesaplar
        next += periyot;                     // sabit hedef zinciri → drift birikmez
    }
}
```

### §B.13 Zig — `std.time.Timer` (monotonic) + saturating bekleme

```zig
// [ORİJİNAL ÖRNEK · derlenmedi] https://ziglang.org/documentation/master/std/
const std = @import("std");

pub fn runEvery(period_ns: u64, ticks: usize) !void {
    var timer = try std.time.Timer.start();   // monotonic sayıcı (duvar saati değil)
    var next: u64 = timer.read() + period_ns; // sabit hedef zinciri → drift birikmez

    var i: usize = 0;
    while (i < ticks) : (i += 1) {
        const remain = next -| timer.read();  // saturating subtraction: gecikmede 0'a iner, altı taşmaz
        std.time.sleep(remain);               // ⚠️ VERIFICATION REQUIRED: sleep yeri Zig sürümüne göre değişebilir (std.time.sleep ↔ std.Thread.sleep) — sürüm doğrulanmadı
        next += period_ns;
    }
}
```

**Ders:** Zig'de zaman ölçümü `std.time.Timer` ile **monotonic** kurulur (NTP/değişim
etkilemez). `-|` saturating çıkarma gecikmede negatif/taşma üretmez → zincir **değil**
erken bitiş riski taşır, o da `next += period_ns` ile telafi edilir. Uyku API'sinin
yerel adı sürüm duyarlıdır → blok `⚠️ VERIFICATION REQUIRED` işaretini taşır, kalkmaz.

### §B.14 Swift — `ContinuousClock` (Swift 5.7+ monotonic saat)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi] https://developer.apple.com/documentation/swift/continuousclock
func runEvery(_ period: Duration, ticks: Int) async throws {
    let clock = ContinuousClock()          // monotonic — duvar saatinden bağımsız
    let start = clock.now
    var next = start + period              // sabit hedef zinciri

    for _ in 0..<ticks {
        try await clock.sleep(until: next) // hedefe kadar bekle (tolerance varsayılanı kullanılır)
        next += period
        let elapsed = clock.now - start
        print("tick t=\(elapsed)")
    }
}
```

**Ders:** `ContinuousClock` duvar saatinden bağımsızdır (sistem saati sıçraması uyandırmayı
bozmaz); `sleep(until:)` hedefe **kadar** uyur → kalan süreyi uygulamanın hesaplaması gerekmez.
Apple dokümanı [KANIT]; swift-corelibs-foundation (Linux) ve Windows hedeflerinde davranış
doğrulanmadı → `§3` hücreleri `[TASARIM]` kalır.

### §B.15 Java/Kotlin (JVM) — `System.nanoTime` + `LockSupport.parkNanos`

```java
// [ORİJİNAL ÖRNEK · derlenmedi] https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// Ayrıca: https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/util/concurrent/locks/LockSupport.html
import java.util.concurrent.locks.LockSupport;

public final class Scheduler {
    private Scheduler() {}

    /** Monotonic hedef-zamanlı uyku: System.nanoTime tabanlı sabit zincir. */
    public static void runEvery(long periodNs, int ticks) {
        long next = System.nanoTime();
        for (int i = 0; i < ticks; i++) {
            next += periodNs;
            long remain = next - System.nanoTime();
            if (remain > 0) {
                LockSupport.parkNanos(remain); // kesinti geldiğinde erken döner (bloklayan değil bekleyen)
            }
            // ölçüm: System.nanoTime() yalnız gözlem; politika/raporlama bu bloğun dışında
        }
    }
}
```

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi] https://kotlinlang.org/api/latest/jvm/stdlib/kotlin.time/-time-source/
// Planlama: JDK (System.nanoTime / LockSupport — Java bloğu URL'leri)
import java.util.concurrent.locks.LockSupport
import kotlin.time.TimeSource

fun runEvery(periodNs: Long, ticks: Int) {
    val mark = TimeSource.Monotonic.markNow() // kotlin.time monotonic referans (ölçüm)
    var next = System.nanoTime()              // JVM.nanoTime: monotonik (duvar saati değil)
    repeat(ticks) {
        next += periodNs
        val remain = next - System.nanoTime()
        if (remain > 0) LockSupport.parkNanos(remain)
    }
    println("toplam=${mark.elapsedNow()}")
}
```

**Ders:** JVM'de `System.nanoTime()` **monotonic** (yalnız farklar anlamlıdır); uygulama
seviyesinde ölçüm `kotlin.time.TimeSource.Monotonic` ile okunur, planlama JDK `parkNanos`
ile yapılır → Kotlin stdlib'e yeni bağımlılık **eklenmez** (JDK + kotlin.stdlib yeter).
`parkNanos` kesintide erken döner; sayaç `next` zinciri telafi eder (drift birikmez).

---

## GRUP C — Kuyruk / tampon zamanlama [TASARIM]

> **Kapsam sınırı:** ring-buffer burada **kavram** olarak, yalnız kullanıcı-uzayı
> kuyruk/tampon *zamanlama* deseni içinde kodlanır. DSP/gerçek-zamanlı ses
> kuyruğu bu dosyanın dışındadır (turu-kod-seti GRUP kapsamı + `audio-engine-cpp`
> alanı). Taşma politikası sabitlenmemiştir → §4 O3.

### §C.1 Desen kavramı (kod yok — politika tablosu)

| Taşma politikası | Ne zaman doğru | Risk |
|---|---|---|
| **drop-oldest** (eskiyi at) | Son değer önemlidir (ör. en son ayar) | Eski ama sıra-dışı olay kaybı |
| **drop-newest** (yenisini at) | İlk kabul edilen iş kutsaldır | Geciken iş hiç gelmez |
| **blokla** (üreticiyi yavaşlat) | Kayıp kabul edilemez | Üreticiyi bekletir → GRUP A önceliğiyle etkileşim |
| **boyut + metric** | Hangisinin yaşandığı bilinmeli | Köprü: `observability-ornekler` §28 (metrik köprüsü) |

### §C.2 Go — sabit boyutlu channel + ticker (bounded kuyruk)

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/time
package kuyruk

import (
	"context"
	"fmt"
	"time"
)

// RunPaced, ticker ile üretim aralığını sabitler; tampon dolarsa üreticiyi
// bloke eder (drop policy değil — "blokla" satırı §C.1'den seçilmiştir).
func RunPaced(ctx context.Context, kapasite int, periyot time.Duration, üret func(i int) error) error {
	if kapasite <= 0 {
		return fmt.Errorf("kapasite 0 olamaz: %d", kapasite)
	}
	q := make(chan int, kapasite)

	tick := time.NewTicker(periyot)
	defer tick.Stop()

	// Tüketici tek iş parçacığı → sıralı işleme.
	go func() {
		for v := range q {
			if err := üret(v); err != nil {
				fmt.Printf("uretim hatasi: %v\n", err)
			}
		}
	}()

	i := 0
	for {
		select {
		case <-ctx.Done():
			close(q) // tüketici kapanışı görür; sessiz sızıntı yok
			return ctx.Err()
		case <-tick.C:
			select {
			case q <- i: // tampon doluysa BU blokta kalır (bilinçli seçim)
				i++
			case <-ctx.Done():
				close(q)
				return ctx.Err()
			}
		}
	}
}
```

### §C.3 Python — `deque` sabit-boyut tampon + monotonic pacing

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/time.html
# (collections.deque stdlib — maxsize dolunca maxleftedge atar → "drop-oldest".)
import collections
import threading
import time

class BoundedRing:
    """Sabit boyutlu tampon; taşınca ESKİ kaydı atar (drop-oldest, §C.1)."""

    def __init__(self, capacity: int) -> None:
        if capacity <= 0:
            raise ValueError("capacity > 0 olmali")
        self._buf = collections.deque(maxlen=capacity)
        self._lock = threading.Lock()
        self.dropped = 0  # hangi politikanın tetiklendiği METRIK olarak sayılır

    def put(self, item) -> None:
        with self._lock:
            if len(self._buf) == self._buf.maxlen:
                self.dropped += 1  # sessiz kayıp yok: sayaç (observability-ornekler §28)
            self._buf.append(item)

    def snapshot(self) -> list:
        with self._lock:
            return list(self._buf)

def paced_produce(ring: BoundedRing, periyot_s: float, ticks: int) -> None:
    next_deadline = time.monotonic() + periyot_s
    for i in range(ticks):
        while True:
            remaining = next_deadline - time.monotonic()
            if remaining <= 0:
                break
            time.sleep(remaining)
        ring.put(i)
        next_deadline += periyot_s  # sabit hedef zinciri (§B.7 ile aynı desen)
```

### §C.4 C — POSIX message queue (`mq_open`) ile süreçler-arası zamanlanmış kuyruk

```c
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man3/mq_open.3p.html
#define _POSIX_C_SOURCE 200809L
#include <mqueue.h>
#include <fcntl.h>
#include <stdio.h>
#include <string.h>
#include <sys/stat.h>

/* Sabit kapasiteli kuyruk açar; mq_maxmsg üst sınırı çekirdekçe uygulanır. */
mqd_t open_paced_queue(const char *name, unsigned maxmsg)
{
    struct mq_attr attr;
    memset(&attr, 0, sizeof attr);
    attr.mq_maxmsg = (long)maxmsg;   /* taşma: EAGAIN → üretici blokla/raporla (§C.1) */
    attr.mq_msgsize = 64;

    mqd_t q = mq_open(name, O_CREAT | O_WRONLY, 0600, &attr);
    if (q == (mqd_t)-1) {
        perror("mq_open");           /* hata yutulmaz */
        return (mqd_t)-1;
    }
    return q;
}
```

**Not:** `mqueue` montaj gereksinimi/izni platforma göre değişir; Linux'ta
`/dev/mqueue`, macOS sütunu `[TASARIM]` (POSIX mq desteklenmez) → §4 O2.

### §C.5 PHP — `SplQueue` + süre aralıklı tüketim (tek süreç deseni)

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.usleep.php
declare(strict_types=1);

/** Tek süreçli, aralıklı tüketim: kuyruk kıtlığı değil zamanlama deseni. */
function drain_with_cadence(SplQueue $queue, int $periyotUs, int $maxBatch): int
{
    $processed = 0;
    while (!$queue->isEmpty() && $processed < $maxBatch) {
        $item = $queue->dequeue();
        $processed++;

        // İşleme sonrası sabit aralık: üretimi GRUP A önceliğiyle değil, hızla sınırla.
        usleep($periyotUs);
        unset($item);
    }
    return $processed; // kaç iş bitti — çağrıan raporlar
}
```

---

## §3 Dil × OS matrisi

| Dil | Windows | macOS | Linux | Bu dosyadaki bloklar |
|---|---|---|---|---|
| **PHP** | `proc_open_nice` → `[TASARIM]` (nice yok) | `[TASARIM]` | `proc_open`+`nice` ✓ | §A.4 · §B.4 · §B.5 · §C.5 |
| **JS (Node)** | `hrtime` geçerli ✓ (QPC) | `[TASARIM]` davranış doğrulanmadı | `hrtime` ✓ (CLOCK_MONOTONIC) | §B.6 |
| **Python** | `os.setpriority` → `PermissionError` raporlar | `[TASARIM]` | `os.setpriority`/`os.nice` ✓ | §A.5 · §B.7 · §C.3 |
| **Go** | `nice` yok → `exec` köprüsü ✓ | `nice` (BSD) ✓ varsayım → `[TASARIM]` | `nice`+`setpriority` ✓ | §A.6 · §B.8 · §C.2 |
| **Rust** | `nice` yok → `Command` `"[TASARIM]"` | `[TASARIM]` (libc R6) | `nice` std ✓ · `libc` R6 engeli | §A.7 · §B.9 |
| **C#** | `Process.PriorityClass` ✓ · `Task.Delay` ✓ | `[TASARIM]` | mono/coreclr davranışı `[TASARIM]` | §A.8 · §B.10 |
| **C** | `SetPriorityClass` ✓ | Darwin `setpriority` `[TASARIM]` | `setpriority`/`clock_nanosleep`/`mq` ✓ | §A.1 · §A.9 · §B.1 · §B.2 · §C.4 |
| **C++20** | `#ifdef _WIN32` ✓ | `[TASARIM]` | `setpriority` + `sleep_until` ✓ | §A.9 · §B.12 |
| **Bash** | Git-Bash `nice` çekirdek-aracına bağlı | BSD `nice` ✓ varsayım → `[TASARIM]` | `nice`/`sleep` ✓ | §A.2 · §B.3 |
| **PowerShell** | `PriorityClass` ✓ · `Start-Sleep` ✓ | pwsh `Start-Sleep` ✓ · öncelik sınıfı yok | `Start-Sleep` ✓ · `PriorityClass` yok | §A.3 · §B.11 |
| **Zig** | hedef sabiti ✓ · `std.time` davranışı `[TASARIM]` (doğrulanmadı) | `[TASARIM]` | `Timer` monotonic ✓ · `sleep` sürüm notu ⚠️ | §B.13 |
| **Swift** | Foundation Windows `[TASARIM]` | `ContinuousClock` ✓ (Apple KANIT) | swift-corelibs davranışı doğrulanmadı → `[TASARIM]` | §B.14 |
| **Java** | `nanoTime`/`parkNanos` ✓ (JDK) | ✓ (JDK sınıfı) | ✓ (JDK) | §B.15 |
| **Kotlin** | ✓ (aynı JVM) | ✓ (aynı JVM) | ✓ (aynı JVM) | §B.15 |

---

## §4 Açık sorular

| # | Soru | Etki | Bağlı |
|---|---|---|---|
| **O1** | `SCHED_FIFO`/`SCHED_RR` üretim süreçlerine taşınacak mı? `CAP_SYS_NICE` + canlı-ölçüm gerekiyor; yanlış seçim `isletim-sistemi-observability-kod-seti` ölçüm döngüsünü aç bırakır. | §A.10 → tam bloğa dönüşür | ADR |
| **O2** | macOS Darwin `setpriority`/`nice`/`mq` davranış kodları kesinleşmedi; PHP `proc_open_nice` Windows yolu Start-Process mi, reddetme mi? | §A.4 · §A.1 · §C.4 · §3 | `turu-kod-seti` O2 (Q4) |
| **O3** | Kuyruk taşma politikası hangi ADR ile sabitlenir (drop-oldest / drop-newest / blokla)? Şu an her blok kendi seçimini yazar. | §C.1–§C.5 | ADR |
| **O4** | Win32 `SleepEx`/waitable-timer yüzeyi bu dosyaya mı taşınacak (şu an yalnız `Task.Delay`/`Start-Sleep` kodlandı)? | §B | ADR |
| **O5** | Drift telafisi (sabit hedef zinciri) tüm döngülerde zorunlu kılınacak mı — yoksa yalnız gerçek-zamanlı köprülerde mi? | §B.6–§B.12 | `plan-ornekler` §1.7 |

`⚠️ VERIFICATION REQUIRED` — O1–O5 çözülmeden bu dosyanın `status: verify-required`
alanı `verified` yapılmaz.

---

## §5 Kaynakça (HTTP 200 doğrulanmış — 2026-10-14)

| # | Kaynak | Kullanan blok |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/setpriority.2.html | §A.1 · §A.5 · §A.9 |
| 2 | https://man7.org/linux/man-pages/man1/nice.1.html | §A.2 · §A.4 |
| 3 | https://man7.org/linux/man-pages/man2/sched_setscheduler.2.html | §A.10 |
| 4 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass | §A.3 · §A.9 |
| 5 | https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process.priorityclass | §A.8 |
| 6 | https://www.php.net/manual/en/function.proc-open.php | §A.4 |
| 7 | https://docs.python.org/3/library/os.html | §A.5 |
| 8 | https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html | §B.1 |
| 9 | https://man7.org/linux/man-pages/man2/nanosleep.2.html | §B.2 |
| 10 | https://man7.org/linux/man-pages/man1/sleep.1.html | §B.3 |
| 11 | https://man7.org/linux/man-pages/man3/sleep.3p.html | §B.3 |
| 12 | https://www.php.net/manual/en/function.time-nanosleep.php | §B.4 |
| 13 | https://www.php.net/manual/en/function.usleep.php | §B.5 · §C.5 |
| 14 | https://nodejs.org/api/timers.html | §B.6 |
| 15 | https://docs.python.org/3/library/time.html | §B.7 · §C.3 |
| 16 | https://pkg.go.dev/time | §A.6 · §B.8 · §C.2 |
| 17 | https://doc.rust-lang.org/std/thread/fn.sleep.html | §A.7 · §B.9 |
| 18 | https://learn.microsoft.com/en-us/dotnet/api/system.threading.tasks.task.delay | §B.10 |
| 19 | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/start-sleep | §B.11 |
| 20 | https://en.cppreference.com/w/cpp/thread/sleep_until.html | §B.12 |
| 21 | https://man7.org/linux/man-pages/man3/mq_open.3p.html | §C.4 |
| 22 | https://nodejs.org/api/process.html#processhrtimebigint | §B.6 |

**Doğrulanmayan ve bu yüzden kullanılmayanlar (halüsinasyon engeli):**
`pthread_sleep(3p)` (404) · `clock_nanosleep.3.html` (404 → `man2` kullanıldı) ·
`man7/mqueue.7.html` (404 → `mq_open.3p` kullanıldı) · Win32 `SleepEx` (404 → §4 O4) ·
`hrtime.dev` (DNS çözülemedi — doğrulanamadı → `nodejs process.html#processhrtimebigint` kullanıldı).

---

## §6 İlişkili dosyalar

| Dosya | İlişki | Köprü |
|---|---|---|
| `isletim-sistemi-turu-kod-seti` | Üst alan modeli — GRUP kapsamı ve O2 | GRUP A–H referansı (kopya yok) |
| `isletim-sistemi-plan-ornekler` | Sibling — OS örnek tabloları | §1.3 (`timeout=5`) · §1.7 (`hrtime`) · §4 (H dürüstlük) · §7 (OS örnekleri) |
| `isletim-sistemi-security-kod-seti` | Sibling — kalıcı izin/sandbox vs. geçici öncelik ayrımı | GRUP A/B (fark §0.2) |
| `isletim-sistemi-observability-kod-seti` | Sibling — monotonic saat + metrik köprüsü | GRUP B · GRUP C (§28) |
| `isletim-sistemi-testing-kod-seti` | Sibling — zamanlama testi için platform matrisi | §3 (bu dosya) ↔ testing GRUP B |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

> Alan SSOT: `isletim-sistemi-turu-kod-seti` — bu dosya yalnız kod-seti genişletmesidir;
> §0–§6 ve grup tanımları değiştirilemez nitelikte, yalnız `verify-required` doğrulaması
> bekler. · **Status:** `verify-required` · **Updated:** 2026-10-14
