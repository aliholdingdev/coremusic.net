---
node_id: "K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-core"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-ornekler"
evidence:
  - "https://man7.org/linux/man-pages/man5/proc.5.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man3/sysconf.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/os.html (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/fs.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/os.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/tempfile.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/os (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/fs/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/fs/struct.Metadata.html (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/env/fn.temp_dir.html (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/env/fn.current_dir.html (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.file-get-contents.php (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.sys-get-temp-dir.php (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.getcwd.php (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.php-uname.php (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/stat.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/stat.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesexw (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.io.file.getattributes (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.getcurrentdirectory (✅ HTTP 200 · 2026-10-14)"
  - "https://en.cppreference.com/w/cpp/filesystem/status (✅ HTTP 200 · 2026-10-14)"
  - "https://en.cppreference.com/w/cpp/filesystem/space.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdtemp.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/getcwd.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man3/mkstemp.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man1/mktemp.1.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 34 benzersiz resmi URL (HTTP 200 kapısı) · 27 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: PHP'nin Windows/macOS sınıfında sistem-bellek ölçümü için yerel API'si YOK → bu dosyada yalnız Linux kolu yazıldı, diğer sınıflar açık throw ile reddedilir; Windows kolu FFI'ye mi devredilecek? UNKNOWN"
  - "O2: Go standart kütüphanesinde sistem-bellek API'si yok (gopsutil [TASARIM]) → taşınabilir Go kolu belirsiz · UNKNOWN"
  - "O3: Rust sistem-bellek okuması için üçüncü parti `sysinfo` [TASARIM] — R6 gereği bağımlılık eklenmedi · UNKNOWN"
  - "O4: Python `os.sysconf` Windows'ta yok → Windows kolu bu dosyada yazılmadı · UNKNOWN"
  - "O5: macOS (Q4 açık) → tüm macOS hücreleri [TASARIM]; `sysctl hw.memsize` macOS kolunun ölçümü yok"
  - "O6: `/proc/meminfo` `MemTotal` satırının birimi (k/B dönüşümü) bu turda metin olarak okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "O7: Örnekler derlenmedi/çalıştırılmadı → tüm gruplar ⚠️ VERIFICATION REQUIRED"
group: kod
---

# isletim-sistemi-core — Cross-Platform Kod Seti (çekirdek OS hizmetleri · Linux birincil)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-core` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar; kod **Windows · Linux · macOS dahil tüm
> sınıflarda** çalışacak biçimde yazılır ("koşan tek Linux, yazılan her yerde").
> Bu dosyanın **sahipliği**: (A) sistem belleği ölçümü, (B) dosya meta + izin **okuma**,
> (C) geçici dizin + çalışma dizini. Kilitleme/atomik yazma **burada tekrar edilmez** →
> `isletim-sistemi-turu-kod-seti.md` §GRUP E'ye çapraz referans verilir.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu ilk satırında dil yorumuyla `[ORİJİNAL ÖRNEK · derlenmedi]` taşır |
| Kaynak | Dil/arayüz adları §5'teki **2026-10-14 HTTP 200** resmi dokümanlardan türetildi; içerik alıntısı **yapılmadı** |
| Repo referansı | Repo gerçekleri yalnız kardeş `isletim-sistemi-core-ornekler.md` **mevcut § numaralarıyla** referanslanır; yeni `Dosya:/Satır:` çifti **üretilmez** |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Hata | Sessiz fallback **yasak**: her başarısız yol `throw` / `error` / `Result` ile açıktır |
| Yer tutucu | `...` · `TBD` · boş blok **yasak**; bilinmeyen imza `UNKNOWN` + §4 O-soru ile işaretlenir |
| Bağımlılık | `gopsutil` · `sysinfo` · `memmap2` gibi paketler **yalnız kavram/[TASARIM]**; kurulum eklenmez (R6) |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış
doğrulanmadı · `UNKNOWN` = kanıt yok.

---

## §1. Çalışma sözleşmesi — "koşan tek Linux, yazılan her yerde"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] `isletim-sistemi-turu-kod-seti.md` §GRUP H · `isletim-sistemi-turu-ornekler.md` §18 |
| Birincil doğrulama | Linux'ta yazılır ve Linux'ta denetlenir | [TASARIM] bu dosyanın sözleşmesi |
| Windows | Win32 sütunu **baştan yazılır**, sonradan eklenmez | [TASARIM] §GRUP A–C |
| macOS | repo kanıtı **yok** (Q4 açık) → tüm macOS hücreleri `[TASARIM]` | [BİLİNMEYEN] §4 O5 |
| Ölçüm birimi | Bellek/dosya sayıları **ham** döner; birim dönüşümü doğrulanana kadar `⚠️` kalır | §4 O6 |

**Sözleşme kuralları (4 madde — her grup bunlara uyar):**

1. **Ölçüm okunur, yorumlanmaz:** bu dosya OS'tan **ham sayı** getirir; eşik kararı/karar
   politikası başka dosyanın işidir.
2. **Platform kolu tek noktada** dallanır; her fonksiyon kendi içinde OS zinciri kurmaz.
3. **Yol ayracı** her dilin kendi kütüphanesinden gelir; sabit `/` veya `\` birleştirici olarak yazılmaz.
4. **Bilinmeyen sınıf sessizce geçilmez:** desteklenmeyen OS kolu açık `throw`/`error`/`Err` verir.

---

## §2. Grup haritası — 3 grup × dil dağılımı

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Sistem belleği ölçümü | PHP · JS · Python · Go · Rust · C# · C · Zig · Swift · Java/Kotlin | §GRUP A |
| B | Dosya meta + izin okuma | C · Python · Go · Rust · C# · C++20 | §GRUP B |
| C | Geçici dizin + çalışma dizini | PHP · JS · Python · Go · Rust · C# · C · Bash · PowerShell | §GRUP C |

**Toplam:** 3 grup · 27 kod bloğu · 13 dil yüzeyi (PHP, JS, Python, Go, Rust, C#, C, C++20, Bash, PowerShell, Zig, Swift, Java/Kotlin).

---

## GRUP A — Sistem belleği ölçümü (ham ölçüm, Linux birincil)

> **Amaç:** "Makinede kaç bayt RAM var?" sorusunu **tek ham sayı** olarak almak.
> Üç OS kolu: Linux `/proc/meminfo` (+ C'de `sysconf`) · Windows `GlobalMemoryStatusEx` ·
> macOS `sysctl hw.memsize` [TASARIM].
> **Sahiplik sınırı:** bu grup yalnız **okuma** yapar; eşik/uyarı politikası bu dosyanın dışındadır.

### §A.1 PHP — Linux `/proc/meminfo` (açık red kolu ile)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://man7.org/linux/man-pages/man5/proc.5.html
   Resmi kaynak: https://php.net/manual/en/function.file-get-contents.php
   Resmi kaynak: https://php.net/manual/en/function.php-uname.php */
declare(strict_types=1);

final class SystemMemory
{
    /** @return array{total:int,unit:string} ham değer + birim (dönüşüm O6 — ⚠️ VERIFICATION REQUIRED) */
    public static function total(): array
    {
        $os = strtolower((string) php_uname('s'));
        if ($os !== 'linux') {
            // PHP'de sistem-belleği için yerel API YOK (O1) → sessiz 0/varsayılan DEĞİL, açık hata
            throw new RuntimeException('sistem belleği yalnız Linux sınıfında; gözlenen: ' . $os);
        }
        $raw = @file_get_contents('/proc/meminfo');   // Linux'a özel dosya arayüzü (bu kolun API'si)
        if ($raw === false) {
            throw new RuntimeException('/proc/meminfo okunamadı');
        }
        if (preg_match('/^MemTotal:\s+(\d+)\s+(\S+)/m', $raw, $m) !== 1) {
            throw new RuntimeException('MemTotal satırı bulunamadı — çıktı biçimi bilinmiyor (UNKNOWN)');
        }
        return ['total' => (int) $m[1], 'unit' => $m[2]];
    }
}
```

**Sınıf dersi:** PHP'nin Windows kolunda eşdeğer yerel API **yoktur** (O1) — bu yüzden blok
Linux dışında **sessizce 0 dönmez**, `throw` eder (sessiz fallback yasağı).

### §A.2 JavaScript (Node.js) — `os.totalmem` / `os.freemem`

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/os.html
import os from 'node:os';

export function memorySnapshot() {
  const total = os.totalmem();     // toplam sistem belleği (bayt)
  const free  = os.freemem();      // boş bellek (bayt)
  if (!Number.isFinite(total) || total <= 0) {
    throw new Error('sistem belleği okunamadı — os.totalmem geçersiz değer döndürdü');
  }
  return { total, free, used: total - free };
}
```

**Not:** Node bu ölçümü libuv üzerinden **tek arayüzde** üç sınıfa da taşır; API `os` modülünde
belgelidir. `⚠️ VERIFICATION REQUIRED`: Node sürümü repo'da sabitlenmedi → `UNKNOWN`.

### §A.3 Python — `os.sysconf` (POSIX sayfa hesabı)

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/os.html
import os
import sys

def total_memory_bytes() -> int:
    if not sys.platform.startswith(("linux", "darwin")):
        # os.sysconf yalnız POSIX sınıfında; Windows kolu bu dosyada YOK (O4) → açık hata
        raise RuntimeError(f"sistem belleği bu platformda yazılmadı: {sys.platform}")
    pages = os.sysconf("SC_PHYS_PAGES")       # sayfa sayısı
    page  = os.sysconf("SC_PAGE_SIZE")        # sayfa boyu (bayt)
    if pages <= 0 or page <= 0:
        raise RuntimeError("os.sysconf geçersiz değer döndürdü")
    return pages * page
```

**Not:** `sysconf` POSIX katmanıdır (Linux/macOS); Windows'ta bu çağrı bulunmaz → blok
`RuntimeError` ile **yüksek sesle** reddeder (O4).

### §A.4 Go — `/proc/meminfo` (standart kütüphane ile, paket yok)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os
package memory

import (
	"fmt"
	"os"
	"runtime"
	"strconv"
	"strings"
)

// TotalMem, Linux'ta /proc/meminfo'dan ham toplam bellek değerini döndürür.
// Diğer GOOS sınıfları bu dosyada YAZILMAZ (O2) → açık hata.
func TotalMem() (uint64, error) {
	if runtime.GOOS != "linux" {
		return 0, fmt.Errorf("bellek ölçümü yalnız linux sınıfında yazıldı: %s", runtime.GOOS)
	}
	b, err := os.ReadFile("/proc/meminfo")
	if err != nil {
		return 0, fmt.Errorf("meminfo okunamadı: %w", err)
	}
	for _, line := range strings.Split(string(b), "\n") {
		if !strings.HasPrefix(line, "MemTotal:") {
			continue
		}
		fields := strings.Fields(line)
		if len(fields) < 2 {
			return 0, fmt.Errorf("MemTotal satırı beklenen biçimde değil")
		}
		v, err := strconv.ParseUint(fields[1], 10, 64)
		if err != nil {
			return 0, fmt.Errorf("MemTotal sayısal değil: %w", err)
		}
		return v, nil // birim dönüşümü O6 — ⚠️ VERIFICATION REQUIRED
	}
	return 0, fmt.Errorf("MemTotal satırı bulunamadı")
}
```

**Not:** Go **standart kütüphanesinde** sistem-bellek API'si yoktur; taşınabilir yol üçüncü parti
`gopsutil` olurdu → **[TASARIM], bağımlılık eklenmedi** (R6 · O2).

### §A.5 Rust — `std::fs` ile `/proc/meminfo`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/fs/index.html
use std::fmt;

#[derive(Debug)]
pub struct MemTotal(pub u64);

impl fmt::Display for MemTotal {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        write!(f, "{}", self.0)
    }
}

pub fn total_mem() -> std::io::Result<MemTotal> {
    if !cfg!(target_os = "linux") {
        // Windows/macOS kolu bu dosyada yazılmadı (O3) → Err, sessiz varsayılan değil
        return Err(std::io::Error::new(
            std::io::ErrorKind::Unsupported,
            "bellek ölçümü yalnız linux hedefinde yazıldı",
        ));
    }
    let text = std::fs::read_to_string("/proc/meminfo")?;   // açık hata yayılır
    let line = text
        .lines()
        .find(|l| l.starts_with("MemTotal:"))
        .ok_or_else(|| std::io::Error::new(std::io::ErrorKind::InvalidData, "MemTotal yok"))?;
    let raw = line
        .split_whitespace()
        .nth(1)
        .and_then(|v| v.parse::<u64>().ok())
        .ok_or_else(|| std::io::Error::new(std::io::ErrorKind::InvalidData, "MemTotal sayısal değil"))?;
    Ok(MemTotal(raw)) // birim dönüşümü O6 — ⚠️ VERIFICATION REQUIRED
}
```

**Not:** Rust standart kütüphanesi sistem-bellek sunmaz; `sysinfo` crate'i **[TASARIM]** olarak
kalır — `Cargo.toml` bu görevde **değiştirilmedi** (R6 · O3).

### §A.6 C# (.NET) — Windows `GlobalMemoryStatusEx` (P/Invoke)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex
using System;
using System.ComponentModel;
using System.Runtime.InteropServices;

static class SystemMemory
{
    [StructLayout(LayoutKind.Sequential)]
    private struct MEMORYSTATUSEX
    {
        public uint dwLength;
        public uint dwMemoryLoad;
        public ulong ullTotalPhys;
        public ulong ullAvailPhys;
        public ulong ullTotalPageFile;
        public ulong ullAvailPageFile;
        public ulong ullTotalVirtual;
        public ulong ullAvailVirtual;
        public ulong ullAvailExtendedVirtual;
    }

    [DllImport("kernel32.dll", SetLastError = true)]
    [return: MarshalAs(UnmanagedType.Bool)]
    private static extern bool GlobalMemoryStatusEx(ref MEMORYSTATUSEX lpBuffer);

    public static ulong TotalPhysicalBytes()
    {
        var st = new MEMORYSTATUSEX { dwLength = (uint)Marshal.SizeOf<MEMORYSTATUSEX>() };
        if (!GlobalMemoryStatusEx(ref st))
        {
            throw new Win32Exception(Marshal.GetLastWin32Error(), "GlobalMemoryStatusEx başarısız"); // sessiz fallback yok
        }
        return st.ullTotalPhys;
    }
}
```

**Not:** Bu blok **Windows sınıfı içindir**; Linux kolunun karşılığı `/proc/memini` kalıbıdır
(§A.4/§A.5 ile aynı desen). `⚠️ VERIFICATION REQUIRED`: blok derlenmedi; struct boyutu/doldurma
sırası dokümana göre doğrulanmalı → `UNKNOWN`.

### §A.7 C — üç kol tek derleme biriminde (`sysconf` ↔ `GlobalMemoryStatusEx` ↔ `sysctl`)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Linux  : https://man7.org/linux/man-pages/man3/sysconf.3.html
            https://man7.org/linux/man-pages/man5/proc.5.html
   Windows: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex
   macOS  : https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html */
#include <stdio.h>
#include <stdint.h>

#if defined(_WIN32)
#include <windows.h>

int cm_total_mem(uint64_t *out)
{
    MEMORYSTATUSEX st;
    st.dwLength = sizeof st;
    if (!GlobalMemoryStatusEx(&st)) return -1;      /* hata sessiz yutulmaz */
    *out = (uint64_t)st.ullTotalPhys;
    return 0;
}
#elif defined(__APPLE__)
#include <sys/types.h>
#include <sys/sysctl.h>

int cm_total_mem(uint64_t *out)
{
    int mib[2] = { CTL_HW, HW_MEMSIZE };
    uint64_t value = 0;
    size_t len = sizeof value;
    if (sysctl(mib, 2, &value, &len, NULL, 0) != 0) return -1;
    *out = value;
    return 0;                                        /* [TASARIM] — Q4 kanıtı yok (O5) */
}
#else
#include <unistd.h>

int cm_total_mem(uint64_t *out)
{
    long pages = sysconf(_SC_PHYS_PAGES);            /* Linux: sayfa sayısı */
    long psize = sysconf(_SC_PAGE_SIZE);             /* sayfa boyu (bayt)   */
    if (pages <= 0 || psize <= 0) return -1;
    *out = (uint64_t)pages * (uint64_t)psize;
    return 0;
}
#endif
```

**Sınıf dersi:** C'de üç OS kolu **aynı imzayı** (`int cm_total_mem(uint64_t*)`) paylaşır;
dallanma derleme zamanında tek yerde kapanır. macOS kolu `[TASARIM]` (O5); davranış iddiaları
`⚠️ VERIFICATION REQUIRED`.

### §A.8 Zig — `/proc/meminfo` `MemTotal` (std.fs ile ham okuma)

```zig
// [ORİJİNAL ÖRNEK · derlenmedi] https://ziglang.org/documentation/master/std/
// Linux: https://man7.org/linux/man-pages/man5/proc.5.html
const std = @import("std");

/// Toplam RAM (kB, /proc/meminfo MemTotal) — birim dönüşümü çağıran sorumluluğu (core §4 O6).
pub fn totalMemKb() !u64 {
    var buf: [4096]u8 = undefined;
    // cwd().openFile mutlak yolla Linux'ta kökten çözülür (çalışma dizini farketmez).
    const file = try std.fs.cwd().openFile("/proc/meminfo", .{});
    defer file.close();

    const n = try file.read(buf[0..]);
    const content = buf[0..n];

    const key = "MemTotal:";
    const start = std.mem.indexOf(u8, content, key) orelse return error.MemTotalNotFound;
    const rest = std.mem.trim(u8, content[start + key.len ..], " \t\r\n");
    const end = std.mem.indexOfAny(u8, rest, " \t\r\n") orelse return error.MemTotalMalformed;
    return try std.fmt.parseInt(u64, rest[0..end], 10);
}
```

**Sınıf dersi:** Yalnız **Linux kolu** kodlandı (Amaç §'daki Linux-birincil kuralı); Windows
`GlobalMemoryStatusEx` ve macOS `sysctl` Zig kolları `[TASARIM]` — bu dosyada **uydurulmadı**
(semantik filtre: dil platforma ait değilse blok yok, gerekçe açık). `error.MemTotalNotFound`
ile eksik alan **yüksek sesle** hata döner — `0` döndürmek ölçümü gizler (sessiz fallback yasağı).

### §A.9 Swift — `ProcessInfo.processInfo.physicalMemory` (Foundation)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi] https://developer.apple.com/documentation/foundation/processinfo
import Foundation

/// Toplam fiziksel RAM (bayt) — Darwin hedefi için [KANIT] Apple dokümanı.
let totalBytes = ProcessInfo.processInfo.physicalMemory
```

**Sınıf dersi:** Apple dokümanı bu değeri **fiziksel bellek (bayt)** olarak tanımlar →
macOS kolunda `[TASARIM]` değil, [KANIT] satırıdır. swift-corelibs-foundation (Linux)
aynı değerin davranışını **doğrulamadık** → Linux hücresi `⚠️` kalır (`§5` URL kapı kontrolü
yalnız erişilebilirliği ölçer, davranışı değil).

### §A.10 Java/Kotlin (JVM) — `OperatingSystemMXBean` + `Runtime`

```java
// [ORİJİNAL ÖRNEK · derlenmedi] https://docs.oracle.com/en/java/javase/21/docs/api/java.management/com/sun/management/OperatingSystemMXBean.html
// Ayrıca: https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/Runtime.html
import java.lang.management.ManagementFactory;
import com.sun.management.OperatingSystemMXBean;

public final class SystemMemory {
    private SystemMemory() {}

    /** Toplam fiziksel RAM (bayt); MXBean yüzeyi yoksa -1 — sessiz 0 döndürülmez. */
    public static long totalPhysicalBytes() {
        var os = ManagementFactory.getOperatingSystemMXBean();
        if (os instanceof OperatingSystemMXBean mx) {
            return mx.getTotalPhysicalMemorySize();
        }
        return -1L;
    }

    /** JVM taahhüt edilmiş havuzu (bayt) — RAM ölçüğü DEĞİLDİR; ayrı sinyal olarak işaretli. */
    public static long jvmHeapCommitted() {
        return Runtime.getRuntime().totalMemory();
    }
}
```

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi] https://kotlinlang.org/api/latest/jvm/stdlib/  (JDK 21 API: com.sun.management.OperatingSystemMXBean — Java bloğu URL)
import java.lang.management.ManagementFactory
import com.sun.management.OperatingSystemMXBean

fun totalPhysicalBytes(): Long {
    val os = ManagementFactory.getOperatingSystemMXBean()
    return if (os is OperatingSystemMXBean) os.totalPhysicalMemorySize else -1L
}
```

**Sınıf dersi:** Ölçümün sahibi **JVM** (`com.sun.management` = JDK genişletmesi, JDK 21
API dokümanında); `Runtime.totalMemory()` yalnız JVM havuzudur ve sunucunun toplam RAM'i
**olamaz** — iki sinyal karıştırılmaz. MXBean yokluğu `-1` ile **açık** bildirilir.
Kotlin aynı MXBean'i okur (JVM paylaşımdır) → dil değil platform meselesi.

### §A.11 Ölçüm eşlemesi (grup özeti)

| # | OS | API | Dil kol(lar)ı | Durum |
|---|---|---|---|---|
| 1 | Linux | `/proc/meminfo` · `sysconf(_SC_PHYS_PAGES)` | PHP · Go · Rust · C · Python · Zig | [KANIT] resmi doküman |
| 2 | Windows | `GlobalMemoryStatusEx` | C# · C | [KANIT] resmi doküman · derlenmedi |
| 3 | macOS | `sysctl hw.memsize` | C | `[TASARIM]` — Q4 (O5) |
| 4 | Node (çapraz) | `os.totalmem` | JS | [KANIT] resmi doküman |
| 5 | Taşınabilir Go/Rust | üçüncü parti paket | — | `[TASARIM]` — eklenmedi (O2 · O3) |
| 6 | macOS (Foundation) | `ProcessInfo.physicalMemory` | Swift | [KANIT] Apple dokümanı · Linux corelibs doğrulanmadı |
| 7 | JVM (Linux · Windows · macOS) | `OperatingSystemMXBean` · `Runtime` | Java · Kotlin | [KANIT] JDK 21 API · derlenmedi |

---

## GRUP B — Dosya meta + izin okuma (stat ↔ GetFileAttributesExW ↔ Metadata)

> **Amaç:** "Bu yol nedir (dosya/dizin/symlink), ne zaman değişti, izin bitleri nedir?" sorusunu
> **okumak**. İzin **verme/yazma** (`chmod`/ACL) bu dosyanın **dışındadır** → B2 fazındaki
> `isletim-sistemi-security-kod-seti.md` sahiptir; burada yalnız `stat` sınıfı okunur.
> Repo köprüsü: `isletim-sistemi-core-ornekler.md` §6.0 (G6c dosya durum/grubu) ve §4.13
> (kavram → Linux/Windows/macOS/PHP matrisi).

### §B.1 C — POSIX `stat` (Linux · macOS aynı imza)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Linux : https://man7.org/linux/man-pages/man2/stat.2.html
   POSIX : https://pubs.opengroup.org/onlinepubs/9799919799/functions/stat.html */
#include <sys/stat.h>
#include <string.h>
#include <errno.h>

struct cm_meta {
    unsigned long long size;   /* bayt */
    int is_dir;
    int is_link;               /* stat hedefi izler; symlink için lstat gerekir */
    unsigned mode;             /* POSIX izin bitleri — yalnız OKUMA */
};

int cm_read_meta(const char *path, struct cm_meta *out)
{
    struct stat st;
    if (stat(path, &st) != 0) return -errno;          /* errno açık döner, yutulmaz */
    out->size   = (unsigned long long)st.st_size;
    out->is_dir = S_ISDIR(st.st_mode) ? 1 : 0;
    out->is_link = 0;                                 /* stat hedefi izler */
    out->mode   = (unsigned)(st.st_mode & 07777);
    return 0;
}
```

**Not:** `stat` vs `lstat` farkı **TOCTOU/semantik** sınıfıdır; zincirleme `⚠️ VERIFICATION
REQUIRED` (davranış testi yok → `UNKNOWN`).

### §B.2 C — Windows `GetFileAttributesExW`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesexw */
#if defined(_WIN32)
#include <windows.h>

struct cm_meta_w {
    unsigned long long size;
    int is_dir;
    DWORD attributes;            /* FILE_ATTRIBUTE_* bayrakları — POSIX mod biti DEĞİL */
};

int cm_read_meta_w(const wchar_t *path, struct cm_meta_w *out)
{
    WIN32_FILE_ATTRIBUTE_DATA fad;
    if (!GetFileAttributesExW(path, GetFileExInfoStandard, &fad)) {
        return (int)GetLastError();                   /* hata kodu açık döner */
    }
    out->size = ((unsigned long long)fad.nFileSizeHigh << 32) | fad.nFileSizeLow;
    out->is_dir = (fad.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY) ? 1 : 0;
    out->attributes = fad.dwFileAttributes;
    return 0;
}
#endif
```

**Sınıf dersi:** Windows **izin biti taşımaz** — bayraklar `FILE_ATTRIBUTE_*` sınıfıdır;
POSIX `mode` ile doğrudan eşlenmez (eşleme `UNKNOWN` → §4 O-soru).

### §B.3 Python — `os.stat`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/os.html
import os
import stat as stat_mod

def read_meta(path: str) -> dict:
    try:
        st = os.stat(path)             # hedefi izler (symlink dahil)
    except OSError as exc:
        raise RuntimeError(f"stat başarısız: {path}") from exc   # açık hata, errno korunur
    return {
        "size": st.st_size,
        "is_dir": stat_mod.S_ISDIR(st.st_mode),
        "is_reg": stat_mod.S_ISREG(st.st_mode),
        "mode": stat_mod.S_IMODE(st.st_mode),       # yalnız okuma (chmod bu dosyanın dışı)
        "mtime": st.st_mtime,
    }
```

### §B.4 Go — `os.Stat`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os
package fsinfo

import (
	"fmt"
	"os"
)

type Meta struct {
	Size  int64
	IsDir bool
	Mode  os.FileMode
}

func ReadMeta(path string) (Meta, error) {
	fi, err := os.Stat(path) // symlink hedefini izler (Lstat ayıracı ayrı çağrıdır)
	if err != nil {
		return Meta{}, fmt.Errorf("stat başarısız: %w", err)
	}
	return Meta{Size: fi.Size(), IsDir: fi.IsDir(), Mode: fi.Mode()}, nil
}
```

**Not:** Go `os.FileMode` POSIX modunu **portable sarmalayıcıya** taşır; Windows'ta anlamları
düşüktür → karşılıklık `⚠️ VERIFICATION REQUIRED` (§4 O-soru).

### §B.5 Rust — `fs::Metadata`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/fs/struct.Metadata.html
use std::fs;
use std::path::Path;

pub struct Meta {
    pub len: u64,
    pub is_dir: bool,
    pub is_file: bool,
    pub readonly: bool,
}

pub fn read_meta(path: &Path) -> std::io::Result<Meta> {
    let m = fs::metadata(path)?;             // hata `?` ile yayılır — sessiz fallback yok
    Ok(Meta {
        len: m.len(),
        is_dir: m.is_dir(),
        is_file: m.is_file(),
        readonly: m.permissions().readonly(),
    })
}
```

**Not:** `Metadata::permissions()` **salt-okunur bayrağını** verir; POSIX 9-bit mod ayrı
yüzeydedir (`std::os::unix::fs::PermissionsExt`) → o yüzey bu dosyada **bilinçli yok** (O-suru
değil, kapsam sınırı) ve `UNKNOWN` işaretlenir.

### §B.6 C# (.NET) — `File.GetAttributes`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.file.getattributes
using System;
using System.IO;

static class Meta
{
    public static (long size, bool isDir, FileAttributes attrs) Read(string path)
    {
        if (!File.Exists(path) && !Directory.Exists(path))
        {
            throw new FileNotFoundException("yol bulunamadı", path);   // açık hata
        }
        var attrs = File.GetAttributes(path);                          // Windows bayrakları
        long size = (attrs & FileAttributes.Directory) != 0
            ? 0L
            : new FileInfo(path).Length;
        bool isDir = (attrs & FileAttributes.Directory) != 0;
        return (size, isDir, attrs);
    }
}
```

**Not:** `File.Exists` + `File.GetAttributes` ikilisi **TOCTOU** içerir (kontrol-kullan);
kritik yollarda `DirEntry`/açık-tutma deseni gerekir → `⚠️ VERIFICATION REQUIRED` (§4).

### §B.7 C++20 — `std::filesystem::status` + `space`

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://en.cppreference.com/w/cpp/filesystem/status
// Resmi kaynak: https://en.cppreference.com/w/cpp/filesystem/space.html
#include <filesystem>
#include <system_error>

namespace fs = std::filesystem;

struct FileMeta {
    bool exists;
    bool is_dir;
    bool is_regular;
    std::uintmax_t size;          // bilinmeyen durumda 0 — etiketli eksiklik
};

FileMeta read_meta(const fs::path& p)
{
    std::error_code ec;                            // fırlatmasız kol
    const fs::file_status s = fs::status(p, ec);   // POSIX stat karşılığı
    if (ec) {
        // varlık bilinemez → bilinçli eksiklik: var=false, boyut=0 (sessiz "yok" iddiası DEĞİL)
        return FileMeta{false, false, false, 0};
    }
    FileMeta m{fs::exists(s), fs::is_directory(s), fs::is_regular_file(s), 0};
    if (m.is_regular) {
        std::error_code sec;
        m.size = fs::file_size(p, sec);
        if (sec) m.size = 0;                       // boyut okunamadı → işaretli 0
    }
    return m;
}

// Ek metrik: dosya sistemi kapasitesi (bellek değil, disk)
bool disk_has_room(const fs::path& p, std::uintmax_t needed, std::error_code& ec)
{
    const fs::space_info si = fs::space(p, ec);    // capacity / free / available
    if (ec) return false;                          // hatayı çağıran işler (sessiz yutma yok)
    return si.available >= needed;
}
```

**Not:** Neva Engine yüzeyi C++20'dir (`AGENTS.md` §1) — bu blok **OS sınıfı** örneğidir;
DSP/donanım-topolojisine girmez (kapsam sınırı).

### §B.8 Meta eşlemesi (grup özeti)

| Kavram | Linux/POSIX | Windows | Rust/C#/Go/Py | Durum |
|---|---|---|---|---|
| Boyut | `stat.st_size` | `nFileSizeHigh/Low` | `Metadata::len` · `FileInfo.Length` | [KANIT] |
| Tür | `S_ISDIR`/`S_ISREG` | `FILE_ATTRIBUTE_DIRECTORY` | `is_dir()`/`IsDir` | [KANIT] |
| İzin okuma | `st_mode & 07777` | `FILE_ATTRIBUTE_READONLY` (+ACL ayrı) | `readonly()` · `S_IMODE` | kısmi — karşılık `UNKNOWN` |
| Değişim zamanı | `st_mtime` | `ftLastWriteTime` | `mtime` · `LastWriteTime` | [KANIT] · birim dönüşümü `⚠️` |
| Symlink ayrımı | `lstat` | `FILE_ATTRIBUTE_REPARSE_POINT` | `symlink_metadata` | [KANIT] · test yok |

---

## GRUP C — Geçici dizin + çalışma dizini (kilitli yazma tekrar YOK)

> **Amaç:** Temp dizini ve cwd'yi **kütüphaneden** almak (sabit `/tmp` veya `C:\Temp` yazılmaz).
> **Tekrar yasağı:** kilitleme ve atomik yazma konusu **bu grupta işlenmez** — sahibi
> `isletim-sistemi-turu-kod-seti.md` **§GRUP E**'dir (bkz. §C.10 çapraz referans).
> Repo köprüsü: `isletim-sistemi-core-ornekler.md` Örnek 1 (geçici dizin kavramı) · §6.0 (G6a).

### §C.1 PHP — `sys_get_temp_dir` + `getcwd`

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://php.net/manual/en/function.sys-get-temp-dir.php
   Resmi kaynak: https://php.net/manual/en/function.getcwd.php */
declare(strict_types=1);

function cm_temp_path(string $suffix = '.tmp'): string
{
    $dir = sys_get_temp_dir();                       // %TEMP% / $TMPDIR / /tmp — kütüphane verir
    if ($dir === '') {
        throw new RuntimeException('sys_get_temp_dir boş döndü');
    }
    $name = 'cm_' . bin2hex(random_bytes(8)) . $suffix;
    return rtrim($dir, "\\/") . DIRECTORY_SEPARATOR . $name;  // ayraç: sabit değil, PHP sabiti
}

function cm_cwd(): string
{
    $cwd = getcwd();
    if ($cwd === false) {
        throw new RuntimeException('getcwd başarısız');       // PHP false döner — açık hata
    }
    return $cwd;
}
```

**Repo köprüsü:** geçici dizin kalıbı repoda çalışır hâldedir (`isletim-sistemi-core-ornekler.md`
Örnek 1); burada yalnız tekrar gösterilir — yeni satır iddiası **üretilmez**.

### §C.2 Python — `tempfile.gettempdir` + `os.getcwd`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/tempfile.html
# Resmi kaynak: https://docs.python.org/3/library/os.html
import os
import tempfile

def temp_path(suffix: str = ".tmp") -> str:
    d = tempfile.gettempdir()              # TMPDIR/TEMP/TMP → platform varsayılanı → cwd (son çare)
    if not d:
        raise RuntimeError("gettempdir boş döndü")
    return os.path.join(d, "cm_" + os.urandom(8).hex() + suffix)   # ayraç kütüphaneden

def cwd() -> str:
    try:
        return os.getcwd()
    except OSError as exc:
        raise RuntimeError("çalışma dizini okunamadı") from exc
```

### §C.3 Go — `os.TempDir` + `os.Getwd`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os
package paths

import (
	"fmt"
	"os"
	"path/filepath"
)

func TempFileSpec() (string, error) {
	dir := os.TempDir() // %TEMP% / TMPDIR / /tmp — kütüphane kararı
	if dir == "" {
		return "", fmt.Errorf("os.TempDir boş")
	}
	return filepath.Join(dir, "cm.tmp"), nil // ayraç: filepath.Join (path paketi DEĞİL)
}

func Cwd() (string, error) {
	wd, err := os.Getwd()
	if err != nil {
		return "", fmt.Errorf("çalışma dizini yok: %w", err)
	}
	return wd, nil
}
```

**Tuzaq:** Go'da `path` (URL/slash) ile `filepath` (OS yolu) **karıştırılmaz** — yanlış paket
seçimi diskte kırılır (`⚠️ VERIFICATION REQUIRED`).

### §C.4 Rust — `env::temp_dir` + `env::current_dir`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/env/fn.temp_dir.html
// Resmi kaynak: https://doc.rust-lang.org/std/env/fn.current_dir.html
use std::path::PathBuf;

pub fn temp_file_spec() -> PathBuf {
    let mut p = std::env::temp_dir();   // %TEMP% / $TMPDIR / /tmp
    if p.as_os_str().is_empty() {
        // sessiz varsayılan değil — panik değil, çağıran karar verir
        p = std::path::PathBuf::from(".");
    }
    p.push("cm.tmp");
    p
}

pub fn cwd() -> std::io::Result<PathBuf> {
    std::env::current_dir()             // geçersiz cwd → Err (dokümanda iki hata durumu var)
}
```

### §C.5 C# — `Path.GetTempPath` + `Directory.GetCurrentDirectory`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.getcurrentdirectory
using System;
using System.IO;

static class Paths
{
    public static string TempFileSpec()
    {
        var dir = Path.GetTempPath();
        if (string.IsNullOrEmpty(dir))
        {
            throw new InvalidOperationException("GetTempPath boş döndü");
        }
        return Path.Combine(dir, $"cm_{Guid.NewGuid():N}.tmp");   // ayraç: Path.Combine
    }

    public static string Cwd()
    {
        try
        {
            return Directory.GetCurrentDirectory();
        }
        catch (Exception ex) when (ex is UnauthorizedAccessException or NotSupportedException)
        {
            throw new InvalidOperationException("çalışma dizini okunamadı", ex);   // açık hata
        }
    }
}
```

### §C.6 C — `mkdtemp` (güvenli temp) + `getcwd`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   POSIX : https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdtemp.html
   Linux : https://man7.org/linux/man-pages/man2/getcwd.2.html */
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>

/* Şablon mutlak yolla başlamalı; 'XXXXXX' son bileşende kalır. */
int cm_make_tempdir(char *buf, size_t buflen)
{
    const char *base = getenv("TMPDIR");
    if (base == NULL || base[0] == '\0') base = "/tmp";   /* POSIX son çare varsayılanı */
    if (snprintf(buf, buflen, "%s/cmXXXXXX", base) >= (int)buflen) return -ENAMETOOLONG;
    if (mkdtemp(buf) == NULL) return -errno;              /* yarış güvenli tahsis (mkstemp sınıfı) */
    return 0;
}

int cm_cwd(char *buf, size_t buflen)
{
    if (getcwd(buf, buflen) == NULL) return -errno;       /* ERANGE/ENOENT açık döner */
    return 0;
}
```

**Not:** `mktemp(1)`/`mktemp(3)` **güvensizdir** (ad sonradan oluşturulur — yarış);
`mkdtemp`/`mkstemp` iki adımı atomik yapar → `man7 mkstemp.3` + Python `tempfile` uyarısı ile
aynı ders. `⚠️ VERIFICATION REQUIRED`: POSIX şablon kuralı davranış testi yok.

### §C.7 Bash — `mktemp -d` (Linux/macOS operasyon kabuğu)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man1/mktemp.1.html
set -euo pipefail

tmp_dir="$(mktemp -d "${TMPDIR:-/tmp}/cmXXXXXX")"   # şablon zorunlu: en az 3 ardışık X
trap 'rm -rf -- "$tmp_dir"' EXIT                    # temizlik: kabuk ayrılışında
cwd="$(pwd)"                                        # kabuk yerleşimi — program içi değil
printf 'temp=%s\ncwd=%s\n' "$tmp_dir" "$cwd"
```

**Not:** Betik yolu **program içi** cwd değildir; kabuk betikleri `pwd` kullanır (işletim
kabuğu katmanı — `isletim-sistemi-turu-kod-seti.md` §GRUP G ile aynı disiplin).

### §C.8 JavaScript (Node.js) — `os.tmpdir` + `fs.mkdtemp`

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/fs.html
// Resmi kaynak: https://nodejs.org/api/os.html
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';

export function makeTempDir() {
  const base = os.tmpdir();
  if (!base) throw new Error('os.tmpdir boş döndü');
  const dir = fs.mkdtempSync(path.join(base, 'cm_'));   // atomik tahsis + ayraç kütüphaneden
  return {
    dir,
    cwd: process.cwd(),
    cleanup: () => fs.rmSync(dir, { recursive: true, force: false }),
  };
}
```

**Not:** `force: false` — temizlikte yutma **yok**: kapanamayan yol `throw` ile görünür.

### §C.9 PowerShell — `[IO.Path]::GetTempPath`

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath
$ErrorActionPreference = 'Stop'

$tmpBase = [System.IO.Path]::GetTempPath()
if ([string]::IsNullOrEmpty($tmpBase)) { throw 'GetTempPath boş döndü' }
$spec = [System.IO.Path]::Combine($tmpBase, ('cm_' + [System.Guid]::NewGuid().ToString('N') + '.tmp'))
$cwd  = (Get-Location).Path
Write-Output "temp=$spec"
Write-Output "cwd=$cwd"
```

### §C.10 Çapraz referans — kilitli yazma / atomik yazma (TEKRAR YOK)

| Konu | Sahibi | Bu dosyada var mı? |
|---|---|---|
| `LOCK_EX` · `fcntl`/`msvcrt` kilit | `isletim-sistemi-turu-kod-seti.md` **§GRUP E** | **Hayır** — kopya kod üretilmedi |
| Atomik yazma sırası (`create → write → fsync → close → rename`) | `isletim-sistemi-turu-kod-seti.md` §E.3–§E.4 | **Hayır** — yalnız referans |
| Temp dosya **tahsisi** (bu grup) | bu dosya §C.1–§C.9 | Evet |
| İzin **verme** (`chmod`/ACL) | `isletim-sistemi-security-kod-seti.md` (B2) | **Hayır** |

**Kural:** temp dosya burada **yalnız ad/yer** üretir; içerik yazımı ve kilidi ayrı sahibindedir —
aynı kalıbın iki yerde tekrarı yasaktır (R3).

### §C.11 Temp/cwd eşlemesi (grup özeti)

| Dil | Temp dizin | Çalışma dizini | § |
|---|---|---|---|
| PHP | `sys_get_temp_dir()` | `getcwd()` | C.1 |
| JS (Node) | `os.tmpdir()` | `process.cwd()` | C.8 |
| Python | `tempfile.gettempdir()` | `os.getcwd()` | C.2 |
| Go | `os.TempDir()` | `os.Getwd()` | C.3 |
| Rust | `env::temp_dir()` | `env::current_dir()` | C.4 |
| C# | `Path.GetTempPath()` | `Directory.GetCurrentDirectory()` | C.5 |
| C | `mkdtemp()` | `getcwd()` | C.6 |
| C++20 | `fs::temp_directory_path()` (§GRUP B komşusu) | `fs::current_path()` | [TASARIM] — bu dosyada blok yok (O-soru yok, kapsam) |
| Bash | `mktemp -d` | `pwd` | C.7 |
| PowerShell | `[IO.Path]::GetTempPath()` | `Get-Location` | C.9 |

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux (birincil) | Windows | macOS | Mekanizma (§) |
|---|---|---|---|---|
| PHP | ✅ `/proc/meminfo` + temp/cwd | ⚠️ bellek kolu **yok** (O1) · temp/cwd ✅ | ⚠️ [TASARIM] | A.1 · C.1 |
| JS (Node) | ✅ `os.totalmem` (çapraz) | ✅ | ✅ [TASARIM] (libuv) | A.2 · C.8 |
| Python | ✅ `os.sysconf` | ⚠️ sysconf yok (O4) · temp/cwd ✅ | ⚠️ POSIX sınıfı · [TASARIM] | A.3 · B.3 · C.2 |
| Go | ✅ `/proc/meminfo` | ⚠️ bellek kolu **yok** (O2) · temp/cwd ✅ | ⚠️ bellek kolu **yok** (O2) | A.4 · B.4 · C.3 |
| Rust | ✅ `std::fs` + temp/cwd | ⚠️ bellek kolu **yok** (O3) · temp/cwd ✅ | ⚠️ [TASARIM] | A.5 · B.5 · C.4 |
| C# (.NET) | ✅ (§A.6 Windows kolu; Linux `/proc` kalıbı §A.4/§A.5) | ✅ `GlobalMemoryStatusEx` + `File.GetAttributes` | ⚠️ [TASARIM] (Q4) | A.6 · B.6 · C.5 |
| C | ✅ `sysconf` · `stat` · `mkdtemp` | ✅ `GetFileAttributesExW` kolu (derleme-zamanı) | ⚠️ `sysctl` kolu [TASARIM] (O5) | A.7 · B.1 · B.2 · C.6 |
| C++20 | ✅ `std::filesystem` | ✅ (standart katman) | ✅ [TASARIM] | B.7 |
| Bash | ✅ `mktemp -d` | ❌ (yerel kabuk yok — turu §G.3) | ✅ | C.7 |
| PowerShell | ⚠️ pwsh (cross) | ✅ | ⚠️ pwsh (cross) | C.9 |
| Zig | ✅ `/proc/meminfo` (§A.8) | ⚠️ bellek kolu yok [TASARIM] | ⚠️ bellek kolu yok [TASARIM] | A.8 |
| Swift | ⚠️ corelibs doğrulanmadı [TASARIM] | ⚠️ Foundation Windows [TASARIM] | ✅ `ProcessInfo.physicalMemory` (KANIT) | A.9 |
| Java | ✅ `OperatingSystemMXBean` (JDK) | ✅ | ✅ | A.10 |
| Kotlin | ✅ (aynı JVM) | ✅ | ✅ | A.10 |

**Okuma kuralı:** ✅ = hedef sınıfı vurur (etiket `derlenmedi` **kalkmaz**) · ⚠️ = sınıf geçerli
ama kanıt/uygulama yok · ❌ = sınıf dışı (kural gereği).

---

## §4. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | PHP Windows/macOS sistem-belleği: FFI kolu mı, dış servis mi? | UNKNOWN — bu dosyada yalnız Linux kolu var |
| O2 | Go taşınabilir bellek ölçümü (`gopsutil` [TASARIM]) eklenir mi? | UNKNOWN — R6 gereği paket yok |
| O3 | Rust `sysinfo` [TASARIM] bağımlılığı bu vault'a girer mi? | UNKNOWN — `Cargo.toml` değiştirilmedi |
| O4 | Python Windows kolu (`ctypes`/Win32) bu dosyaya mı ait? | UNKNOWN — yazılmadı |
| O5 | macOS (Q4) → `sysctl hw.memsize` kolunun ölçümü | UNKNOWN — `[TASARIM]` |
| O6 | `/proc/meminfo` `MemTotal` birimi (kB→bayt) metin olarak doğrulandı mı? | `⚠️ VERIFICATION REQUIRED` — dönüşüm kodda açık işaretli |
| O7 | `stat` ↔ `FILE_ATTRIBUTE_*` ↔ `FileMode` karşılık matrisi | UNKNOWN — §B.8 "kısmi" satırı |
| O8 | Örnekler derlenmedi/çalıştırılmadı | `⚠️ VERIFICATION REQUIRED` (tüm gruplar) |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

---

## §5. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | Linux `/proc` dosya sistemi (`/proc/meminfo`) | https://man7.org/linux/man-pages/man5/proc.5.html | 2026-10-14 ✅ |
| 2 | POSIX `sysconf` (`_SC_PHYS_PAGES`) | https://man7.org/linux/man-pages/man3/sysconf.3.html | 2026-10-14 ✅ |
| 3 | Win32 `GlobalMemoryStatusEx` | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex | 2026-10-14 ✅ |
| 4 | macOS `sysctl(3)` (arşiv) | https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html | 2026-10-14 ✅ |
| 5 | Node `os` (`totalmem`/`freemem`/`tmpdir`) | https://nodejs.org/api/os.html | 2026-10-14 ✅ |
| 6 | Node `fs` (`mkdtemp`/`rm`) | https://nodejs.org/api/fs.html | 2026-10-14 ✅ |
| 7 | Python `os` (`sysconf`/`stat`/`getcwd`) | https://docs.python.org/3/library/os.html | 2026-10-14 ✅ |
| 8 | Python `tempfile` | https://docs.python.org/3/library/tempfile.html | 2026-10-14 ✅ |
| 9 | Go `os` (`ReadFile`/`Stat`/`TempDir`/`Getwd`) | https://pkg.go.dev/os | 2026-10-14 ✅ |
| 10 | Rust `std::fs` (`read_to_string`) | https://doc.rust-lang.org/std/fs/index.html | 2026-10-14 ✅ |
| 11 | Rust `fs::Metadata` | https://doc.rust-lang.org/std/fs/struct.Metadata.html | 2026-10-14 ✅ |
| 12 | Rust `env::temp_dir` | https://doc.rust-lang.org/std/env/fn.temp_dir.html | 2026-10-14 ✅ |
| 13 | Rust `env::current_dir` | https://doc.rust-lang.org/std/env/fn.current_dir.html | 2026-10-14 ✅ |
| 14 | PHP `file_get_contents` | https://php.net/manual/en/function.file-get-contents.php | 2026-10-14 ✅ |
| 15 | PHP `sys_get_temp_dir` | https://php.net/manual/en/function.sys-get-temp-dir.php | 2026-10-14 ✅ |
| 16 | PHP `getcwd` | https://php.net/manual/en/function.getcwd.php | 2026-10-14 ✅ |
| 17 | PHP `php_uname` | https://php.net/manual/en/function.php-uname.php | 2026-10-14 ✅ |
| 18 | Linux `stat(2)` | https://man7.org/linux/man-pages/man2/stat.2.html | 2026-10-14 ✅ |
| 19 | POSIX `stat` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/stat.html | 2026-10-14 ✅ |
| 20 | Win32 `GetFileAttributesExW` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesexw | 2026-10-14 ✅ |
| 21 | .NET `File.GetAttributes` | https://learn.microsoft.com/en-us/dotnet/api/system.io.file.getattributes | 2026-10-14 ✅ |
| 22 | .NET `Path.GetTempPath` | https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath | 2026-10-14 ✅ |
| 23 | .NET `Directory.GetCurrentDirectory` | https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.getcurrentdirectory | 2026-10-14 ✅ |
| 24 | C++ `std::filesystem::status` | https://en.cppreference.com/w/cpp/filesystem/status | 2026-10-14 ✅ |
| 25 | C++ `std::filesystem::space` | https://en.cppreference.com/w/cpp/filesystem/space.html | 2026-10-14 ✅ |
| 26 | POSIX `mkdtemp` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdtemp.html | 2026-10-14 ✅ |
| 27 | Linux `getcwd` | https://man7.org/linux/man-pages/man2/getcwd.2.html | 2026-10-14 ✅ |
| 28 | Linux `mkstemp(3)` (güvenli tahsis) | https://man7.org/linux/man-pages/man3/mkstemp.3.html | 2026-10-14 ✅ |
| 29 | Linux `mktemp(1)` (kabuk — güvenli kullanım) | https://man7.org/linux/man-pages/man1/mktemp.1.html | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler blokların **günü** doğrulanmış arayüz referansıdır; içerik alıntısı
yapılmamıştır, bloklar orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`).

---

## §6. İlişkili dosyalar

- Kardeş repo kanıtları (çapraz köprü): [`isletim-sistemi-core-ornekler.md`](isletim-sistemi-core-ornekler.md) — Örnek 1 · §6.0 · §4.13 · §8
- Kapsam: [`isletim-sistemi-core-kapsam.md`](isletim-sistemi-core-kapsam.md)
- Doğrulama: [`isletim-sistemi-core-dogrulama.md`](isletim-sistemi-core-dogrulama.md)
- Dizin: [`index.md`](index.md)
- OS ailesi karşılaştırması (grup sahibi): [`../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`](../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md) — §GRUP E (kilit/atomik yazma) · §GRUP B (yol/temp) · §GRUP A (platform tespiti)
- İzin **verme** sahibi (B2): `../isletim-sistemi-security/isletim-sistemi-security-kod-seti.md` — bu dosyada üretilmedi
- Sürücü/çekirdek yüzeyi: `../isletim-sistemi-kernel/isletim-sistemi-kernel-kod-seti.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
