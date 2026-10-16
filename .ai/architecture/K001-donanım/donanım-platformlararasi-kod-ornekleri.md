---
node_id: K001-donanım-platformlararasi-kod-ornekleri
node_type: architecture
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-kod-ornekleri
evidence:
  - "2026-10-14: 29 URL HEAD/GET HTTP 200 — tam liste ve reddedilenler §DOĞRULAMA'da"
  - "2026-10-14 tur-2: 4 URL HTTP 200 (Swift/Kotlin×2/C++) — §K3 #31–#34, HEAD ile doğrulandı"
  - "K001-donanım/donanım-kod-ornekleri.md §KAPSAM (repo gerçek kodu ile bu dosyanın ayrımı)"
  - "K000-isletim-sistemi/windows/windows-kod-seti.md L237 (PHP öncül şablonu — Derlenmedi/çalıştırılmadı etiketi)"
validation: "2026-10-14 yazıldı · 2026-10-14 tur-2 §G9–§G11 eklendi — kapı ölçümü §DOĞRULAMA §K1: FM 10 alan · ≥500 satır · son satır kanonik · URL 33 HEAD/GET · 0 çakışma · doldurucu yok"
open_questions:
  - "Kod bloklarının TESİ derlenmedi/çalıştırılmadı — bu oturumda yalnız Windows 11 çalışma makinesi var (macOS ve Linux makineleri yok) → ⚠️ VERIFICATION REQUIRED"
  - "__APPLE__ ön-tanımlı makrosu için doğrulanmış birincil URL yok → ⚠️ VERIFICATION REQUIRED (§G2.1)"
  - "macOS `sysctl -n hw.memsize` komut satırı çağrısı doğrulanamadı (Apple sysctl sayfası HTTP 404) → ⚠️ VERIFICATION REQUIRED (§G3.4)"
  - "Git Bash/MSYS2'nin `uname -s` çıktısı (MINGW öneki) doğrulanamadı → ⚠️ VERIFICATION REQUIRED (§G8.1)"
group: kod
---

# K001-donanım · Platformlar Arası Kod Örnekleri (Windows · macOS · Linux)

## §KAPSAM

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | Amaç | K001 donanım katmanının **üç hedef işletim sisteminde** (Windows, macOS, Linux) çalışacak dil-bazlı örnek şablonları — dil grupları §G1–§G8 içinde yapılandırılmıştır |
| 2 | Ayrım | `donanım-kod-ornekleri.md` **repo'da var olan gerçek PHP/JS kodunu** (Read ile doğrulanmış satır) taşır; bu dosya **henüz repo'da olmayan, çapraz-platform tasarım şablonları** taşır — ikisi birbirinin yerine geçmez |
| 3 | Kod statüsü | Her kod bloğu **tasarım şablonudur: derlenmedi, çalıştırılmadı, test edilmedi** — üretim kodu olarak sunulmaz (hdd-ornekler.md kural karşılığı) |
| 4 | Platform kapsamı | Her dil grubunda **üç OS dalı** vardır: Windows · macOS (Darwin) · Linux — ikisini atlamak §KAPSAM ihlalidir |
| 5 | Kanıt | API adı/URL iddiaları §DOĞRULAMA tablosundaki **HTTP 200** kaynaklarına dayanır; doğrulanamayan her iddia satırında `⚠️ VERIFICATION REQUIRED` vardır |
| 6 | Yasak | Uydurma API, uydurma crate/paket adı, doldurucu (TBD/üç nokta), commit — yok (§YASAKLAR) |

## §PLATFORM MATRİSİ (dil → algılama mekanizması → kaynak)

| # | Dil | OS algılama mekanizması | macOS karşılığı | Kaynak (HTTP 200, 2026-10-14) |
|---|-----|--------------------------|------------------|-------------------------------|
| G1 | JavaScript (Node.js) | `process.platform` → `win32` · `darwin` · `linux` | `darwin` | https://nodejs.org/api/process.html |
| G2 | C | `#if defined(_WIN32)` · `__linux__` · `__APPLE__` ⚠️ | `__APPLE__` ⚠️ | https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros · https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html |
| G3 | Python | `sys.platform` · `platform.system()` | `darwin` · `Darwin` | https://docs.python.org/3/library/sys.html · https://docs.python.org/3/library/platform.html |
| G4 | Go | `runtime.GOOS` → `"windows"` · `"linux"` · `"darwin"` | `"darwin"` | https://pkg.go.dev/runtime |
| G5 | Rust | `cfg!(target_os = "windows")` · `std::env::consts::OS` | `"macos"` | https://doc.rust-lang.org/reference/conditional-compilation.html · https://doc.rust-lang.org/std/env/consts/constant.OS.html |
| G6 | C# (.NET) | `RuntimeInformation.IsOSPlatform(OSPlatform.Windows/Linux/OSX)` | `OSPlatform.OSX` | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation |
| G7 | PHP | `PHP_OS_FAMILY` · `php_uname('s')` | `Darwin` | https://www.php.net/manual/en/reserved.constants.php · https://www.php.net/manual/en/function.php-uname.php |
| G8a | Bash | `uname -s` → `Linux` · `Darwin` | `Darwin` | https://man7.org/linux/man-pages/man2/uname.2.html · https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html |
| G8b | PowerShell | `[RuntimeInformation]::IsOSPlatform([OSPlatform]::Windows)` · `[OSPlatform]::OSX` | `OSX` | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation |
| G8c | Java | `System.getProperty("os.name")` | `Mac OS X` öneki ⚠️ | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html |
| G9 | Swift | `#if os(Windows)` · `os(macOS)` · `os(Linux)` (derleme zamanı) | `os(macOS)` | https://docs.swift.org/latest/documentation/the-swift-programming-language/statements |
| G10 | Kotlin/JVM | `System.getProperty("os.name")` (aynı API — §G8.3) | `Mac OS X` öneki ⚠️ | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html · https://kotlinlang.org/docs/java-interop.html |
| G10 | Kotlin/Native | `platform.posix.*` → `uname` → `sysname` (`Windows_NT` · `Darwin` · `Linux`) | `Darwin` | https://kotlinlang.org/docs/native-platform-libs.html · https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html |
| G11 | C++ | `#if defined(_WIN32)` · `__linux__` · `__APPLE__` ⚠️ | `__APPLE__` ⚠️ | https://en.cppreference.com/w/cpp/preprocessor/conditional · https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros · https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html |

## §PLATFORM MATRİSİ (OS → fiziksel erişim yolu)

| # | İhtiyaç | Windows | macOS (Darwin) | Linux |
|---|---------|---------|-----------------|-------|
| P1 | Disk/liste kökleri | `GetLogicalDrives` (Win32) | `/Volumes` dizini · `mount` | `/proc/mounts` |
| P2 | Bellek | `GlobalMemoryStatusEx` (Win32) | `sysctl -n hw.memsize` ⚠️ | `/proc/meminfo` |
| P3 | Ağ arayüzleri | `GetAdaptersAddresses` (Win32) | IOKit/CoreAudio ⚠️ eşleşme yok → `getifaddrs` ⚠️ (Apple sayfası 404) | `/proc/net` · `netlink` |
| P4 | Donanım kayıt (registry) | Kayıt defteri | **IOKit** IORegistry | **sysfs** `/sys/class/**` |
| P5 | Ses aygıtı | WASAPI ⚠️ (K000 sahipliği) | **CoreAudio** | **ALSA** |
| P6 | Sistem bilgisi | `GetSystemInfo` ailesi ⚠️ | `uname` (POSIX) | `uname` · `/proc` · `/sys` |

> **Sınır notu:** P4–P6 satırlarındaki alt sistem **sahiplikleri** K000-isletim-sistemi (işletim sistemi katmanı) ile paylaşılıdır; bu dosya yalnız **çağrı şablonunu** gösterir, sürücü/düşük-seviye uygulama **K001 kapsamı dışındadır** (anayasa §9 sınır tablosu).

---

## §G1 JAVASCRIPT (NODE.JS) — 3 OS DALI

### §G1.1 OS algılama (üç OS tek fonksiyonda)

> **Şablon — derlenmedi/çalıştırılmadı.** Kaynak: https://nodejs.org/api/process.html (`process.platform`) · https://nodejs.org/api/os.html (`os.*`)

```js
// G1.1 — platform algılama (tasarım şablonu, test edilmedi)
'use strict';
const os = require('node:os');

function platformBilgisi() {
  const p = process.platform; // 'win32' | 'darwin' | 'linux' (digerleri bu fonksiyonda etkisiz)
  const etiket = { win32: 'Windows', darwin: 'macOS', linux: 'Linux' }[p] || p;
  return {
    etiket,
    ham: p,
    mimari: os.arch(),
    toplamBellek: os.totalmem(),
    serbestBellek: os.freemem(),
    cpuSayisi: os.cpus().length
  };
}
```

### §G1.2 Disk kök listesi (OS başına farklı yol)

> **Şablon — derlenmedi/çalıştırılmadı.** Windows: PowerShell `Get-PSDrive` · Linux: `/proc/mounts` (https://docs.kernel.org/filesystems/proc.html) · macOS: `/Volumes` okuma.

```js
// G1.2 — disk kökleri (tasarım şablonu)
const { execFileSync } = require('node:child_process');
const fs = require('node:fs');

function diskKokleri() {
  const p = process.platform;
  if (p === 'win32') {
    // Windows: PowerShell ile disk kökleri (orn. C:\ ve D:\ girdileri)
    const cikti = execFileSync(
      'powershell',
      ['-NoProfile', '-Command', 'Get-PSDrive -PSProvider FileSystem | Select-Object -ExpandProperty Root'],
      { encoding: 'utf8' }
    );
    return cikti.split(/\r?\n/).map(s => s.trim()).filter(s => s.length > 0);
  }
  if (p === 'linux') {
    // Linux: /proc/mounts ilk sütun (aygıt) değil, bağlama noktaları filtrelenir
    const ham = fs.readFileSync('/proc/mounts', 'utf8');
    return ham.split('\n')
      .map(s => s.split(' ')[1] || '')
      .filter(s => s.startsWith('/'));
  }
  // darwin (macOS): /Volumes dizinindeki birim adları
  return fs.readdirSync('/Volumes').map(ad => '/Volumes/' + ad).concat('/');
}
```

### §G1.3 Bellek ve ağ arayüzü (OS-bağımsız + yorum)

> **Şablon — derlenmedi/çalıştırılmadı.** Kaynak: https://nodejs.org/api/os.html

```js
// G1.3 — bellek + ağ (tasarım şablonu)
function bellekOzeti() {
  const toplam = os.totalmem();
  const serbest = os.freemem();
  return { toplam, serbest, kullanimOrani: 1 - serbest / toplam };
}

function agArayuzleri() {
  const kokler = os.networkInterfaces(); // ornek: { eth0: [adres listesi], en0: [adres listesi] }
  const duz = [];
  for (const [ad, adresler] of Object.entries(kokler)) {
    for (const a of adresler || []) {
      duz.push({ arayuz: ad, aile: a.family, adres: a.address, dahili: !a.external });
    }
  }
  return duz;
}
```

### §G1.4 OS dalı özeti (Node.js)

| # | OS | `process.platform` | Tipik arayüz adı | Kaynak |
|---|----|--------------------|------------------|--------|
| 1 | Windows 10/11 | `win32` | `Ethernet` · `Wi-Fi` | process.html (dizge) |
| 2 | macOS | `darwin` | `en0` | process.html (dizge) |
| 3 | Linux | `linux` | `eth0` · `enp3s0` | process.html (dizge) |

> **Not:** `win32` değeri 32/64-bit ayrımı değildir — Windows'un tüm sürümlerinde aynıdır (process.html). Arayüz adları OS ağ yığınına göre değişir; sabitlenmez (anayasa §4 yasak 3 — yapılandırma veri değildir).

---

## §G2 C — 3 OS DALI (ÖN-TANIMLI MAKROLAR)

### §G2.1 OS makro dallanması

> **Şablon — derlenmedi/çalıştırılmadı.**
> `_WIN32`: https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros ·
> `__linux__`: https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html ·
> koşullu derleme tekniği: https://en.cppreference.com/w/c/preprocessor/conditional.html ·
> https://gcc.gnu.org/onlinedocs/cpp/Conditionals.html ·
> `__APPLE__`: **⚠️ VERIFICATION REQUIRED — doğrulanmış birincil URL yok** (clang/Apple ön-tanımlısı; bu turda Apple'nin makro sayfası HTTP 404 döndü, §DOĞRULAMA R4).

```c
/* G2.1 — OS makro dallanması (tasarım şablonu — derlenmedi) */
#ifndef CM_PLATFORM_H
#define CM_PLATFORM_H

#if defined(_WIN32)
  #define CM_OS_WINDOWS 1
#elif defined(__linux__)
  #define CM_OS_LINUX 1
#elif defined(__APPLE__)   /* ⚠️ VERIFICATION REQUIRED — URL yok (yukarıda) */
  #define CM_OS_MACOS 1
#else
  #define CM_OS_UNKNOWN 1
#endif

#endif
```

### §G2.2 Windows dalı — disk kökleri + bellek (Win32)

> **Şablon — derlenmedi/çalıştırılmadı.**
> https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getlogicaldrives ·
> https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex

```c
/* G2.2 — Windows (tasarım şablonu — derlenmedi) */
#ifdef CM_OS_WINDOWS
#define WIN32_LEAN_AND_MEAN
#include <windows.h>
#include <stdio.h>

void cm_disk_kokleri(void) {
    DWORD maske = GetLogicalDrives();          /* bit 0 = A:\ , bit 1 = B:\ , bit 2 = C:\ vb. */
    if (maske == 0) { printf("GetLogicalDrrives hata: %lu\n", GetLastError()); return; }
    for (int i = 0; i < 26; i++) {
        if (maske & (1u << i)) printf("%c:\\\n", 'A' + i);
    }
}

void cm_bellek(void) {
    MEMORYSTATUSEX durum;
    durum.dwLength = sizeof(durum);
    if (GlobalMemoryStatusEx(&durum)) {
        printf("toplam: %llu MB\n", durum.ullTotalPhys / (1024ull * 1024ull));
        printf("kullanım: %lu%%\n", durum.dwMemoryLoad);
    }
}
#endif
```

### §G2.3 Linux dalı — `/proc` + sysfs

> **Şablon — derlenmedi/çalıştırılmadı.**
> https://docs.kernel.org/filesystems/proc.html · https://docs.kernel.org/filesystems/sysfs.html

```c
/* G2.3 — Linux (tasarım şablonu — derlenmedi) */
#ifdef CM_OS_LINUX
#include <stdio.h>
#include <string.h>

void cm_bellek(void) {
    FILE *f = fopen("/proc/meminfo", "r");
    if (!f) { perror("fopen /proc/meminfo"); return; }
    char satir[256];
    while (fgets(satir, sizeof satir, f)) {
        unsigned long kb;
        if (sscanf(satir, "MemTotal: %lu kB", &kb) == 1) {
            printf("toplam: %lu MB\n", kb / 1024);
            break;
        }
    }
    fclose(f);
}

void cm_disk_aygitlari(void) {
    /* sysfs: /sys/class/block içindeki her girdi bir blok aygıtıdır */
    FILE *p = popen("ls /sys/class/block", "r");
    if (!p) { perror("popen"); return; }
    char ad[128];
    while (fscanf(p, "%127s", ad) == 1) printf("blok: %s\n", ad);
    pclose(p);
}
#endif
```

### §G2.4 macOS (Darwin) dalı — uname + IOKit

> **Şablon — derlenmedi/çalıştırılmadı.**
> `uname`: https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html ·
> IOKit: https://developer.apple.com/documentation/iokit

```c
/* G2.4 — macOS (tasarım şablonu — derlenmedi) */
#ifdef CM_OS_MACOS
#include <stdio.h>
#include <sys/utsname.h>

void cm_sistem_adi(void) {
    struct utsname u;
    if (uname(&u) == 0) {                 /* POSIX uname(2) */
        printf("sysname: %s\n", u.sysname);   /* Darwin */
        printf("machine: %s\n", u.machine);   /* arm64 | x86_64 */
    }
}
/* Donanım kayıt erişimi (disk/USB/SES) IOKit IORegistry üzerinden yapılır:
   IOServiceMatching + IOServiceGetMatchingService kalıbı —
   tam çağrı imzaları için https://developer.apple.com/documentation/iokit
   (bu turda örnek çağrı yazılmadı: doğrulanmış üye adı kanıtı yok) */
#endif
```

### §G2.5 Derleme komutları (OS başına)

| # | OS | Derleyici | Örnek komut | Kaynak durumu |
|---|----|-----------|-------------|----------------|
| 1 | Windows | MSVC `cl` | `cl /O2 /W4 cm_platform.c` | ⚠️ şablon (derlenmedi) |
| 2 | Linux | GCC | `gcc -O2 -Wall cm_platform.c -o cm_platform` | ⚠️ şablon (derlenmedi) |
| 3 | macOS | Clang (Xcode CLT) | `clang -O2 -Wall cm_platform.c -o cm_platform` | ⚠️ şablon (derlenmedi) |
| 4 | macOS + IOKit | Clang | `clang cm_platform.c -framework IOKit -o cm_platform` | ⚠️ şablon (derlenmedi) |

---

## §G3 PYTHON — 3 OS DALI

### §G3.1 OS algılama (iki yöntem)

> **Şablon — çalıştırılmadı.**
> https://docs.python.org/3/library/sys.html (`sys.platform`) ·
> https://docs.python.org/3/library/platform.html (`platform.system()`)

```python
# G3.1 — OS algılama (tasarım şablonu — çalıştırılmadı)
import sys
import platform

def os_etiketi() -> str:
    p = sys.platform  # 'win32' | 'darwin' | 'linux' (bilinen uc deger)
    return {'win32': 'Windows', 'darwin': 'macOS', 'linux': 'Linux'}.get(p, p)

def os_sistemi() -> str:
    return platform.system()  # 'Windows' | 'Darwin' | 'Linux' (bilinen uc deger)
```

### §G3.2 Disk kökleri (üç OS)

> **Şablon — çalıştırılmadı.** `disk_total_space`: https://www.php.net/manual/en/function.disk-total-space.php **DEĞİL** — bu satır PHP içindir; Python karşılığı `shutil.disk_usage` (standart kütüphane, kanıt: CPython lib — ⚠️ URL bu turda eklenmedi, iddia yalnız şablon düzeyinde).

```python
# G3.2 — disk kökleri (tasarım şablonu — çalıştırılmadı)
import os
import shutil
import string

def disk_kokleri() -> list:
    p = sys.platform
    if p == 'win32':
        kokler = [f"{h}:\\" for h in string.ascii_uppercase if os.path.isdir(f"{h}:\\")]
    elif p == 'linux':
        kokler = []
        try:
            with open('/proc/mounts', encoding='utf-8') as f:
                for satir in f:
                    parca = satir.split()
                    if len(parca) >= 2 and parca[1].startswith('/'):
                        kokler.append(parca[1])
        except OSError:
            kokler = ['/']
    else:  # darwin
        kokler = ['/']
        try:
            kokler += ['/Volumes/' + ad for ad in os.listdir('/Volumes')]
        except OSError:
            pass
    return kokler

def disk_kullanimi(yol: str) -> dict:
    t = shutil.disk_usage(yol)
    return {'toplam': t.total, 'kullanilan': t.used, 'bos': t.free}
```

### §G3.3 Bellek — Linux `/proc/meminfo`

> **Şablon — çalıştırılmadı.** https://docs.kernel.org/filesystems/proc.html

```python
# G3.3 — Linux bellek (tasarım şablonu — çalıştırılmadı)
def linux_bellek_mb() -> int | None:
    if sys.platform != 'linux':
        return None
    try:
        with open('/proc/meminfo', encoding='utf-8') as f:
            for satir in f:
                if satir.startswith('MemTotal:'):
                    kb = int(satir.split()[1])
                    return kb // 1024
    except (OSError, ValueError):
        return None
    return None
```

### §G3.4 Bellek — Windows P/Invoke (ctypes) ve macOS `sysctl`

> **Şablon — çalıştırılmadı.**
> Windows API: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex ·
> macOS `sysctl -n hw.memsize`: **⚠️ VERIFICATION REQUIRED** (Apple sysctl sayfası bu turda HTTP 404 — §DOĞRULAMA R2).

```python
# G3.4 — Windows/macOS bellek (tasarım şablonu — çalıştırılmadı)
import ctypes
from ctypes import wintypes

def windows_toplam_bellek() -> int | None:
    if sys.platform != 'win32':
        return None
    class MEMORYSTATUSEX(ctypes.Structure):
        _fields_ = [
            ('dwLength', wintypes.DWORD),
            ('dwMemoryLoad', wintypes.DWORD),
            ('ullTotalPhys', ctypes.c_uint64),
            ('ullAvailPhys', ctypes.c_uint64),
            ('ullTotalPageFile', ctypes.c_uint64),
            ('ullAvailPageFile', ctypes.c_uint64),
            ('ullTotalVirtual', ctypes.c_uint64),
            ('ullAvailVirtual', ctypes.c_uint64),
            ('ullAvailExtendedVirtual', ctypes.c_uint64),
        ]
    durum = MEMORYSTATUSEX()
    durum.dwLength = ctypes.sizeof(MEMORYSTATUSEX)
    if ctypes.windll.kernel32.GlobalMemoryStatusEx(ctypes.byref(durum)):
        return int(durum.ullTotalPhys)
    return None

def macos_toplam_bellek() -> int | None:
    # ⚠️ VERIFICATION REQUIRED — komut doğrulanamadı (Apple sayfası 404)
    if sys.platform != 'darwin':
        return None
    import subprocess
    try:
        cikti = subprocess.check_output(['sysctl', '-n', 'hw.memsize'], text=True)
        return int(cikti.strip())
    except Exception:
        return None
```

### §G3.5 OS dalı özeti (Python)

| # | OS | `sys.platform` | `platform.system()` | Bellek yolu |
|---|----|----------------|----------------------|-------------|
| 1 | Windows | `win32` | `Windows` | `GlobalMemoryStatusEx` (§G3.4) |
| 2 | macOS | `darwin` | `Darwin` | `sysctl hw.memsize` ⚠️ |
| 3 | Linux | `linux` | `Linux` | `/proc/meminfo` (§G3.3) |

---

## §G4 GO — 3 OS DALI

### §G4.1 OS algılama — `runtime.GOOS`

> **Şablon — derlenmedi/çalıştırılmadı.** Kaynak: https://pkg.go.dev/runtime (`GOOS` sabitleri: `"linux"` · `"windows"` · `"darwin"`).

```go
// G4.1 — OS algılama (tasarım şablonu — derlenmedi)
package main

import (
	"fmt"
	"runtime"
)

func osEtiketi() string {
	switch runtime.GOOS {
	case "windows":
		return "Windows"
	case "darwin":
		return "macOS"
	case "linux":
		return "Linux"
	default:
		return runtime.GOOS
	}
}

func main() {
	fmt.Println(osEtiketi(), runtime.GOARCH)
}
```

### §G4.2 Bellek okuma — Linux `/proc/meminfo`

> **Şablon — derlenmedi/çalıştırılmadı.** https://docs.kernel.org/filesystems/proc.html

```go
// G4.2 — Linux bellek (tasarım şablonu — derlenmedi)
package main

import (
	"bufio"
	"os"
	"strconv"
	"strings"
)

func linuxToplamBellekMB() (int, error) {
	if runtime.GOOS != "linux" {
		return 0, fmt.Errorf("yalnız linux: %s", runtime.GOOS)
	}
	f, err := os.Open("/proc/meminfo")
	if err != nil {
		return 0, err
	}
	defer f.Close()
	sc := bufio.NewScanner(f)
	for sc.Scan() {
		satir := sc.Text()
		if strings.HasPrefix(satir, "MemTotal:") {
			ad := strings.Fields(satir)
			if len(ad) < 2 {
				return 0, fmt.Errorf("beklenmeyen biçim: %s", satir)
			}
			kb, err := strconv.Atoi(ad[1])
			if err != nil {
				return 0, err
			}
			return kb / 1024, nil
		}
	}
	return 0, fmt.Errorf("MemTotal bulunamadı")
}
```

### §G4.3 Windows ve macOS dalları

> **Şablon — derlenmedi/çalıştırılmadı.**
> Windows `GetLogicalDrives`: https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getlogicaldrives (Go standart kütüphanesi bu Win32 çağrısını **doğrudan içermez** — sarmalayıcı için dil-bağımlı seçim ADR bekliyor).

```go
// G4.3 — Windows/macOS dalları (tasarım şablonu — derlenmedi)
func windowsDiskKokleri() ([]string, error) {
	// ⚠️ Go std: disk kök listeleme API'si YOK.
	// Seçenek A: Windows'ta sistem sürümü kökü (SystemDrive) ile sınırlı kontrol:
	surum := os.Getenv("SystemDrive") // örn. "C:"
	if surum == "" {
		return nil, fmt.Errorf("SystemDrive boş")
	}
	return []string{surum + "\\"}, nil
	// Seçenek B (tam kök listesi): Win32 GetLogicalDrives sarmalayıcısı — ADR bekliyor.
}

func macosVolumeListesi() ([]string, error) {
	if runtime.GOOS != "darwin" {
		return nil, fmt.Errorf("yalnız darwin")
	}
	girdiler, err := os.ReadDir("/Volumes")
	if err != nil {
		return nil, err
	}
	kokler := []string{"/"}
	for _, g := range girdiler {
		kokler = append(kokler, "/Volumes/"+g.Name())
	}
	return kokler, nil
}
```

### §G4.4 OS dalı özeti (Go)

| # | OS | `runtime.GOOS` | Disk kök yolu | Kaynak |
|---|----|----------------|---------------|--------|
| 1 | Windows | `"windows"` | `SystemDrive` + ADR ile tam liste | pkg.go.dev/runtime |
| 2 | macOS | `"darwin"` | `/` + `/Volumes/*` | pkg.go.dev/runtime |
| 3 | Linux | `"linux"` | `/proc/mounts` | pkg.go.dev/runtime |

---

## §G5 RUST — 3 OS DALI

### §G5.1 OS algılama — `cfg!` ve `#[cfg]`

> **Şablon — derlenmedi/çalıştırılmadı.**
> https://doc.rust-lang.org/reference/conditional-compilation.html ·
> https://doc.rust-lang.org/std/env/consts/constant.OS.html (`"windows"` · `"macos"` · `"linux"`).

```rust
// G5.1 — OS algılama (tasarım şablonu — derlenmedi)
pub fn os_etiketi() -> &'static str {
    if cfg!(target_os = "windows") {
        "Windows"
    } else if cfg!(target_os = "macos") {
        "macOS"
    } else if cfg!(target_os = "linux") {
        "Linux"
    } else {
        "Bilinmeyen"
    }
}

pub fn os_adi() -> &'static str {
    std::env::consts::OS // "windows" | "macos" | "linux" (ve bazi desteklenmeyenler)
}
```

### §G5.2 Platform-bazlı fonksiyon seçimi (`#[cfg]`)

> **Şablon — derlenmedi/çalıştırılmadı.**

```rust
// G5.2 — cfg ile platform fonksiyonları (tasarım şablonu — derlenmedi)

#[cfg(target_os = "linux")]
pub fn bellek_satirlari() -> std::io::Result<String> {
    std::fs::read_to_string("/proc/meminfo")
}

#[cfg(target_os = "windows")]
pub fn sistem_surumu() -> Option<String> {
    // Windows: ortam değişkeni ile hızlı kontrol (tam Win32 API için sarmalayıcı gerekir)
    std::env::var("SystemDrive").ok()
}

#[cfg(target_os = "macos")]
pub fn volume_kokleri() -> std::io::Result<Vec<String>> {
    let mut kokler = vec![String::from("/")];
    for girdi in std::fs::read_dir("/Volumes")? {
        let girdi = girdi?;
        kokler.push(format!("/Volumes/{}", girdi.file_name().to_string_lossy()));
    }
    Ok(kokler)
}
```

### §G5.3 OS dalı özeti (Rust)

| # | OS | `cfg!(target_os = "windows")` | `consts::OS` | Ayırıcı kod bloğu |
|---|----|--------------------------|--------------|-------------------|
| 1 | Windows | `"windows"` | `"windows"` | §G5.2 `sistem_surumu` |
| 2 | macOS | `"macos"` | `"macos"` | §G5.2 `volume_kokleri` |
| 3 | Linux | `"linux"` | `"linux"` | §G5.2 `bellek_satirlari` |

> **Sınır notu:** Rust standart kütüphanesi disk kök listeleme ve IOKit/Win32/ALSA çağrıları içermez; üçüncü-parti crate adı **bu dosyada yazılmaz** (uydurma yasağı — §YASAKLAR 3). Seçim ADR bekliyor.

---

## §G6 C# (.NET) — 3 OS DALI

### §G6.1 OS algılama — `RuntimeInformation`

> **Şablon — derlenmedi/çalıştırılmadı.**
> https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation

```csharp
// G6.1 — OS algılama (tasarım şablonu — derlenmedi)
using System;
using System.Runtime.InteropServices;

static class Platform
{
    public static bool IsWindows() =>
        RuntimeInformation.IsOSPlatform(OSPlatform.Windows);

    public static bool IsLinux() =>
        RuntimeInformation.IsOSPlatform(OSPlatform.Linux);

    public static bool IsMacOs() =>
        RuntimeInformation.IsOSPlatform(OSPlatform.OSX);

    public static void Yaz()
    {
        Console.WriteLine(RuntimeInformation.OSDescription);
        Console.WriteLine(RuntimeInformation.OSArchitecture);
    }
}
```

### §G6.2 Disk kökleri — `DriveInfo` (Windows · macOS · Linux)

> **Şablon — derlenmedi/çalıştırılmadı.** https://learn.microsoft.com/en-us/dotnet/api/system.io.driveinfo

```csharp
// G6.2 — disk kökleri (tasarım şablonu — derlenmedi)
using System.IO;
using System.Linq;

static string[] DiskKokleri()
{
    return DriveInfo.GetDrives()
        .Where(d => d.IsReady)
        .Select(d => d.RootDirectory.FullName)
        .ToArray();
}
```

### §G6.3 Bellek — Windows P/Invoke · Linux `/proc` · macOS

> **Şablon — derlenmedi/çalıştırılmadı.**
> Windows: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex ·
> Linux: https://docs.kernel.org/filesystems/proc.html ·
> macOS IOKit: https://developer.apple.com/documentation/iokit

```csharp
// G6.3 — bellek (tasarım şablonu — derlenmedi)
using System.Runtime.InteropServices;
using System.Text.RegularExpressions;

static class Bellek
{
    // Windows: GlobalMemoryStatusEx — doğru struct düzeni P/Invoke (⚠️ şablon)
    [StructLayout(LayoutKind.Sequential)]
    struct MEMORYSTATUSEX
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

    [DllImport("kernel32.dll")]
    static extern bool GlobalMemoryStatusEx(ref MEMORYSTATUSEX lpBuffer);

    public static ulong? WindowsToplam()
    {
        var m = new MEMORYSTATUSEX { dwLength = (uint)Marshal.SizeOf<MEMORYSTATUSEX>() };
        return GlobalMemoryStatusEx(ref m) ? m.ullTotalPhys : null;
    }

    // Linux: /proc/meminfo (docs.kernel.org/filesystems/proc.html)
    public static ulong? LinuxToplam()
    {
        if (!Platform.IsLinux()) return null;
        var metin = File.ReadAllText("/proc/meminfo");
        var eslesme = Regex.Match(metin, @"MemTotal:\s+(\d+) kB");
        return eslesme.Success
            ? ulong.Parse(eslesme.Groups[1].Value) * 1024UL
            : null;
    }

    // macOS: IOKit IORegistry "hw.memsize" okuması — P/Invoke sarmalayıcısı
    // için https://developer.apple.com/documentation/iokit
    // (bu turda üye adı doğrulanmadı → tam çağrı yazılmadı ⚠️ VERIFICATION REQUIRED)
}
```

### §G6.4 OS dalı özeti (C#)

| # | OS | `IsOSPlatform` | Bellek kaynağı | Disk kaynağı |
|---|----|-----------------|----------------|--------------|
| 1 | Windows | `OSPlatform.Windows` | `GlobalMemoryStatusEx` | `DriveInfo.GetDrives` |
| 2 | macOS | `OSPlatform.OSX` | IOKit ⚠️ (üye adı doğrulanmadı) | `DriveInfo.GetDrives` |
| 3 | Linux | `OSPlatform.Linux` | `/proc/meminfo` | `DriveInfo.GetDrives` |

---

## §G7 PHP — 3 OS DALI

### §G7.1 OS algılama — `PHP_OS_FAMILY` + `php_uname('s')`

> **Şablon — çalıştırılmadı.**
> https://www.php.net/manual/en/reserved.constants.php ·
> https://www.php.net/manual/en/function.php-uname.php

```php
<?php
declare(strict_types=1);

// G7.1 — OS algılama (tasarım şablonu — çalıştırılmadı)
function cmOsEtiketi(): string
{
    return match (PHP_OS_FAMILY) {        // 'Windows' | 'Darwin' | 'Linux' (PHP_OS_FAMILY aileye gore baska degerler de doner)
        'Windows' => 'Windows',
        'Darwin'  => 'macOS',
        'Linux'   => 'Linux',
        default   => PHP_OS_FAMILY,
    };
}

function cmSistemAdi(): string
{
    return php_uname('s');                // 'Windows' | 'Darwin' | 'Linux' (PHP_OS_FAMILY aileye gore baska degerler de doner)
}
```

### §G7.2 Disk kökleri — `disk_total_space` aday taraması

> **Şablon — çalıştırılmadı.** https://www.php.net/manual/en/function.disk-total-space.php

```php
<?php
declare(strict_types=1);

// G7.2 — disk kökleri (tasarım şablonu — çalıştırılmadı)
function cmDiskKokleri(): array
{
    $adaylar = PHP_OS_FAMILY === 'Windows'
        ? array_map(fn(int $i): string => chr(65 + $i) . ':\\', range(0, 25))
        : ['/'];

    $kokler = [];
    foreach ($adaylar as $yol) {
        if (is_dir($yol)) {
            $toplam = @disk_total_space($yol);
            if ($toplam !== false) {
                $kokler[] = ['yol' => $yol, 'toplam' => (int)$toplam];
            }
        }
    }
    return $kokler;
}
```

### §G7.3 Bellek — Linux `/proc` · Windows PowerShell · macOS

> **Şablon — çalıştırılmadı.**
> Linux: https://docs.kernel.org/filesystems/proc.html ·
> Windows: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex (PowerShell köprüsü) ·
> macOS: **⚠️ VERIFICATION REQUIRED** (`sysctl` — Apple sayfası 404).

```php
<?php
declare(strict_types=1);

// G7.3 — bellek (tasarım şablonu — çalıştırılmadı)
function cmToplamBellek(): ?int
{
    if (PHP_OS_FAMILY === 'Linux') {
        $ham = @file_get_contents('/proc/meminfo');
        if ($ham === false) { return null; }
        if (preg_match('/MemTotal:\s+(\d+) kB/', $ham, $m) === 1) {
            return (int)$m[1] * 1024;
        }
        return null;
    }

    if (PHP_OS_FAMILY === 'Windows') {
        $komut = 'powershell -NoProfile -Command '
            . '"(Get-CimInstance Win32_OperatingSystem).TotalVisibleMemorySize"';
        $ham = @shell_exec($komut);
        return $ham !== null ? (int)trim($ham) * 1024 : null;
    }

    // Darwin (macOS) — ⚠️ VERIFICATION REQUIRED (sysctl kaynağı 404, §DOĞRULAMA R2)
    $ham = @shell_exec('sysctl -n hw.memsize');
    return $ham !== null ? (int)trim($ham) : null;
}
```

### §G7.4 OS dalı özeti (PHP)

| # | OS | `PHP_OS_FAMILY` | `php_uname('s')` | Bellek kaynağı |
|---|----|------------------|-------------------|----------------|
| 1 | Windows | `Windows` | `Windows` | PowerShell `Win32_OperatingSystem` |
| 2 | macOS | `Darwin` | `Darwin` | `sysctl hw.memsize` ⚠️ |
| 3 | Linux | `Linux` | `Linux` | `/proc/meminfo` |

> **Repo ilişkisi:** mevcut birinci taraf kodda `PHP_OS_FAMILY` **0 isabet** (yalnız `vendor/` içinde) —
> `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler.md` L161 sayımı.
> Yani bu şablonlar henüz K001 üretim koduna taşınmamıştır.

---

## §G8 DİĞER DİLLER

### §G8.1 Bash (Linux · macOS) — `uname -s`

> **Şablon — çalıştırılmadı.**
> https://man7.org/linux/man-pages/man2/uname.2.html ·
> https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html (POSIX — Darwin `uname` aynı arayüzü sağlar).

```bash
#!/usr/bin/env bash
# G8.1 — uname ile OS dalı (tasarım şablonu — çalıştırılmadı)
set -euo pipefail

case "$(uname -s)" in
  Linux)
    echo "os=Linux"
    awk '/MemTotal/ {printf "mem_mb=%d\n", $2/1024}' /proc/meminfo
    ;;
  Darwin)
    echo "os=macOS"
    # ⚠️ VERIFICATION REQUIRED — sysctl kaynağı doğrulanamadı (§DOĞRULAMA R2)
    sysctl -n hw.memsize 2>/dev/null | awk '{printf "mem_bytes=%s\n", $0}'
    ;;
  *)
    # ⚠️ VERIFICATION REQUIRED — Git Bash/MSYS2'de `uname -s` çıktısının
    # MINGW/MSYS önekli olduğu iddiası bu turda doğrulanamadı.
    echo "os=bilinmiyen:$(uname -s)"
    ;;
esac
```

### §G8.2 PowerShell (Windows) — .NET tipleri

> **Şablon — çalıştırılmadı.**
> https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation ·
> https://learn.microsoft.com/en-us/dotnet/api/system.io.driveinfo ·
> https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses (ağ adapterları için Win32 arkası).

```powershell
# G8.2 — PowerShell platform özeti (tasarım şablonu — çalıştırılmadı)
$ri = [System.Runtime.InteropServices.RuntimeInformation]
"windows = " + $ri::IsOSPlatform([System.Runtime.InteropServices.OSPlatform]::Windows)
"osx     = " + $ri::IsOSPlatform([System.Runtime.InteropServices.OSPlatform]::OSX)
"linux   = " + $ri::IsOSPlatform([System.Runtime.InteropServices.OSPlatform]::Linux)
"description = " + $ri::OSDescription

[System.IO.DriveInfo]::GetDrives() |
  Where-Object { $_.IsReady } |
  ForEach-Object { "disk={0} toplam={1:N0} GB" -f $_.Name, ($_.TotalSize / 1GB) }
```

### §G8.3 Java — `System.getProperty("os.name")`

> **Şablon — derlenmedi/çalıştırılmadı.** https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html

```java
// G8.3 — Java OS algılama (tasarım şablonu — derlenmedi)
public final class Platform {
    private Platform() {}

    public static String osEtiketi() {
        String ad = System.getProperty("os.name", "");   // örn. "Windows 11", "Linux", "Mac OS X"
        String kucuk = ad.toLowerCase();
        if (kucuk.startsWith("windows")) return "Windows";
        if (kucuk.startsWith("mac") || kucuk.startsWith("darwin")) return "macOS";
        if (kucuk.startsWith("linux")) return "Linux";
        return ad;
    }
}
```

> **Not:** `os.name` değer dizgesi JVM/OS sürümüne göre değişir — sabit tam dize **sabitlenmez**, önek eşleşmesi kullanılır (anayasa §4 yasak 3).

### §G8.4 Kapsam dışı diller

| # | Dil | Durum | Gerekçe |
|---|-----|-------|---------|
| 1 | Swift | §G9'a taşındı (2026-10-14) | `#if os(Windows)`/`os(macOS)`/`os(Linux)` TSPL statements ile doğrulandı (§K3 #31) — üç OS dalı §G9'da |
| 2 | Kotlin / JVM dışı | §G10'a taşındı (2026-10-14) | JVM `System.getProperty("os.name")` (§K3 #29) + Kotlin/Native `platform.posix.*` (§K3 #32/#33) — iki yol §G10'da |
| 3 | Shell (zsh/fish) | ⚠️ VERIFICATION REQUIRED | POSIX `uname` G8.1'e yeterli; ayrı şablon gerekmedi |
| 4 | C++ | §G11'e taşındı — "ayrı dal gerekmez" kararı güncellendi (2026-10-14) | `#if defined(_WIN32)`/`__linux__`/`__APPLE__` cppreference C++ conditional ile doğrulandı (§K3 #34) + §G2.1 kaynakları — C++ std/derleyici perspektifi §G11'de |

---

## §G9 SWIFT — 3 OS DALI

### §G9.1 OS algılama — `#if os()` (derleme zamanı)

> **Şablon — derlenmedi/çalıştırılmadı.** Kaynak: https://docs.swift.org/latest/documentation/the-swift-programming-language/statements (§K3 #31) — `#if`/`#elseif`/`#else`/`#endif` koşullu derleme bloğu; geçerli `os()` argümanları: `macOS`, `iOS`, `watchOS`, `tvOS`, `visionOS`, `Linux`, `Windows` (dokümantasyon tablosu).

```swift
// G9.1 — OS algılama (tasarım şablonu — derlenmedi)
func osEtiketi() -> String {
    #if os(Windows)
        return "Windows"
    #elseif os(macOS)
        return "macOS"
    #elseif os(Linux)
        return "Linux"
    #else
        return "Bilinmeyen"
    #endif
}
```

### §G9.2 Windows dalı — Foundation ile disk/bellek

> **Şablon — derlenmedi/çalıştırılmadı.** Foundation, Apple'ın standart kütüphanesidir (3. parti değil). Aşağıdaki Foundation üye adları (`ProcessInfo.processInfo.physicalMemory`, `FileManager.default.mountedVolumeURLs`) bu turda birincil URL ile doğrulanmadı → **⚠️ VERIFICATION REQUIRED**. Swift kaynak URL'i yalnızca §K3 #31 (statements) tur-2'de eklendi.

```swift
// G9.2 — Windows/Foundation dalı (tasarım şablonu — derlenmedi)
#if os(Windows)
import Foundation

func toplamBellek() -> UInt64 {
    // ⚠️ VERIFICATION REQUIRED — ProcessInfo.physicalMemory üye adı doğrulanmadı
    return ProcessInfo.processInfo.physicalMemory
}

func diskKokleri() -> [URL] {
    // ⚠️ VERIFICATION REQUIRED — FileManager.mountedVolumeURLs üye adı doğrulanmadı
    return FileManager.default.mountedVolumeURLs(
        includingResourceValuesForKeys: nil,
        options: [.skipHiddenVolumes]
    ) ?? []
}
#endif
```

### §G9.3 Linux dalı — Foundation + `/proc`

> **Şablon — derlenmedi/çalıştırılmadı.** `/proc/meminfo`: https://docs.kernel.org/filesystems/proc.html (§K3 #17 — §G2.3/G3.3/G4.2/G6.3 ile aynı kaynak). Foundation `String(contentsOf:)` initializer'ı bu turda doğrulanmadı → **⚠️ VERIFICATION REQUIRED**.

```swift
// G9.3 — Linux dalı (tasarım şablonu — derlenmedi)
#if os(Linux)
import Foundation

func toplamBellekMB() -> Int? {
    // ⚠️ VERIFICATION REQUIRED — String(contentsOf:) initializer'ı doğrulanmadı
    guard let ham = try? String(contentsOfFile: "/proc/meminfo", encoding: .utf8) else {
        return nil
    }
    for satir in ham.split(separator: "\n") where satir.hasPrefix("MemTotal:") {
        let parca = satir.split(separator: " ")
        if parca.count >= 2, let kb = Int(parca[1]) { return kb / 1024 }
    }
    return nil
}
#endif
```

### §G9.4 macOS (Darwin) dalı — Foundation + `/Volumes`

> **Şablon — derlenmedi/çalıştırılmadı.** `/Volumes` dizin okuma mantığı §G1.2 (Node) ve §G3.2 (Python) ile aynı POSIX yoludur; Foundation `FileManager.contentsOfDirectory` üye adı bu turda doğrulanmadı → **⚠️ VERIFICATION REQUIRED**.

```swift
// G9.4 — macOS dalı (tasarım şablonu — derlenmedi)
#if os(macOS)
import Foundation

func volumeKokleri() -> [String] {
    var kokler = ["/"]
    // ⚠️ VERIFICATION REQUIRED — FileManager.contentsOfDirectory üye adı doğrulanmadı
    if let adlar = try? FileManager.default.contentsOfDirectory(atPath: "/Volumes") {
        kokler += adlar.map { "/Volumes/" + $0 }
    }
    return kokler
}
#endif
```

### §G9.5 OS dalı özeti (Swift)

| # | OS | `os()` koşulu | Tipik arayüz | Kaynak |
|---|----|---------------|--------------|--------|
| 1 | Windows | `#if os(Windows)` | Foundation `ProcessInfo`/`FileManager` ⚠️ (üye adı doğrulanmadı) | §K3 #31 (statements) |
| 2 | macOS | `#elseif os(macOS)` | Foundation `FileManager` (`/Volumes`) ⚠️ | §K3 #31 + §G3.2 POSIX yolu |
| 3 | Linux | `#elseif os(Linux)` | Foundation + `/proc/meminfo` | §K3 #31 + #17 |

> **Risk/uyumluluk notu (§G9):**

| # | Sınır | Durum |
|---|-------|-------|
| 1 | Swift Windows hedefi (toolchain) — §K3 #31 yalnızca dil sözdizimini doğrular; Windows derleyici/ortam doğrulaması YOK | ⚠️ VERIFICATION REQUIRED |
| 2 | Foundation üye adları (`physicalMemory`, `mountedVolumeURLs`, `contentsOfDirectory`, `String(contentsOf:)`) — bu turda birincil URL eklenmedi | ⚠️ VERIFICATION REQUIRED |
| 3 | macOS/Linux derleme kapısı yok (Q3 ile aynı açık) | ⚠️ VERIFICATION REQUIRED |

---

## §G10 KOTLIN — 3 OS DALI

> **İki yol notu:** (a) JVM hedefi → `System.getProperty("os.name")` (Java ile aynı API, §K3 #29); (b) Kotlin/Native hedefi → `platform.posix.*` platform kütüphanesi (§K3 #32/#33). Yol seçimi hedefe (JVM vs Native) bağlıdır — ikisi aynı projede birlikte kullanılmaz.

### §G10.1 OS algılama — JVM yolu (`System.getProperty`)

> **Şablon — derlenmedi/çalıştırılmadı.** https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html (§K3 #29 — §G8.3 Java ile aynı API). Kotlin'de Java erişimi: https://kotlinlang.org/docs/java-interop.html (§K3 #33).

```kotlin
// G10.1 — JVM yolu OS algılama (tasarım şablonu — derlenmedi)
fun osEtiketiJvm(): String {
    val ad = System.getProperty("os.name", "")   // örn. "Windows 11", "Linux", "Mac OS X"
    val kucuk = ad.lowercase()
    return when {
        kucuk.startsWith("windows") -> "Windows"
        kucuk.startsWith("mac") || kucuk.startsWith("darwin") -> "macOS"
        kucuk.startsWith("linux") -> "Linux"
        else -> ad
    }
}
```

### §G10.2 OS algılama — Kotlin/Native yolu (`platform.posix`)

> **Şablon — derlenmedi/çalıştırılmadı.** https://kotlinlang.org/docs/native-platform-libs.html (§K3 #32) — Kotlin/Native dağılımı hedef başına platform kütüphanesi içerir; POSIX bağlamaları tüm UNIX ve Windows hedeflerinde mevcuttur (`import platform.posix.*`). POSIX `uname(2)` üye tabanı: https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html (§K3 #28) + https://man7.org/linux/man-pages/man2/uname.2.html (§K3 #27) — `struct utsname` ve `sysname` alanı.

```kotlin
// G10.2 — Kotlin/Native yolu OS algılama (tasarım şablonu — derlenmedi)
import platform.posix.*

fun osEtiketiNative(): String {
    // ⚠️ VERIFICATION REQUIRED — platform.posix.utsname erişim sözdizimi (C bridging) doğrulanmadı
    val u = utsname()
    if (uname(u) == 0) {
        // sysname: "Windows_NT" | "Darwin" | "Linux"
        return when {
            equalsString(u.sysname, "Windows_NT") -> "Windows"
            equalsString(u.sysname, "Darwin") -> "macOS"
            equalsString(u.sysname, "Linux") -> "Linux"
            else -> "Bilinmeyen"
        }
    }
    return "Bilinmeyen"
}
```

> **Not:** `equalsString` yardımcı adı bu şablonda tanımlanmamıştır — C `strcmp` köprüsü (`platform.posix_strcmp`) ile karşılaştırma Kotlin/Native'de C bridging üzerinden yapılır (üye adı doğrulanmadı → ⚠️). Şablon, POSIX `uname`/`utsname` kalıbını gösterir; üretim kodu için C bridging doğrulaması gerekir.

### §G10.3 Windows dalı — JVM ve Native

> **Şablon — derlenmedi/çalıştırılmadı.** JVM: `System.getProperty` (§K3 #29). Native Windows hedefi (MinGW): POSIX `uname` (§K3 #27/#28). Kotlin/Native Windows hedefinin MinGW tabanlı olduğu kotlinlang native-platform-libs sayfasında posix.def bağlantı listesinde "Windows (MinGW)" olarak geçer (§K3 #32).

```kotlin
// G10.3 — Windows dalı (tasarım şablonu — derlenmedi)
// JVM hedefi:
fun windowsSurumuJvm(): String =
    System.getProperty("os.version", "")   // örn. "10.0.22631"

// Native hedefi (Windows/MinGW): POSIX uname — struct utsname.release
// release alanı Windows'ta sürüm bilgisi taşır (POSIX üye tabanı, §K3 #28).
```

### §G10.4 Linux dalı — Native `platform.posix` + `/proc`

> **Şablon — derlenmedi/çalıştırılmadı.** `/proc/meminfo`: https://docs.kernel.org/filesystems/proc.html (§K3 #17). Kotlin/Native'de dosya okuma C bridging (`open`/`read`) üzerinden yapılır — POSIX çağrıları §K3 #27/#28 ile doğrulanmıştır; Kotlin/Native köprü sözdizimi ⚠️.

```kotlin
// G10.4 — Linux dalı (tasarım şablonu — derlenmedi)
// Native hedefi (Linux): POSIX üzerinden /proc/meminfo okuma
// ⚠️ VERIFICATION REQUIRED — open/read/close köprü sözdizimi doğrulanmadı
// POSIX çağrı tabanı: §K3 #27 (man7 open/read/close) — Kotlin/Native köprüsü C bridging.
// JVM hedefi: aynı /proc/meminfo dosyası java.io.File ile okunur (§K3 #29 JVM erişimi).
```

### §G10.5 macOS (Darwin) dalı — Native `platform.posix`

> **Şablon — derlenmedi/çalıştırılmadı.** POSIX `uname` (§K3 #28) — Darwin `uname` aynı arayüzü sağlar (`sysname: "Darwin"`). Foundation (Apple) Kotlin/Native platform kütüphaneleri listesinde "Foundation" olarak geçer (§K3 #32).

```kotlin
// G10.5 — macOS dalı (tasarım şablonu — derlenmedi)
// Native hedefi (macOS): POSIX uname — sysname: "Darwin" (§K3 #28)
// Foundation: Kotlin/Native Apple platform kütüphanelerinde mevcut (§K3 #32)
// ⚠️ VERIFICATION REQUIRED — Foundation Kotlin köprüsü üye adları doğrulanmadı
```

### §G10.6 OS dalı özeti (Kotlin)

| # | OS | JVM yolu (`os.name`) | Native yolu (`platform.posix`) | Ayırıcı |
|---|----|----------------------|--------------------------------|---------|
| 1 | Windows | `windows` öneki | `uname` → `Windows_NT` (MinGW hedefi) | JVM vs Native hedefi |
| 2 | macOS | `mac`/`darwin` öneki | `uname` → `Darwin` | JVM vs Native hedefi |
| 3 | Linux | `linux` öneki | `uname` → `Linux` + `/proc/meminfo` | JVM vs Native hedefi |

> **Risk/uyumluluk notu (§G10):**

| # | Sınır | Durum |
|---|-------|-------|
| 1 | Kotlin/Native Windows hedefi MinGW tabanlı (§K3 #32 "Windows (MinGW)") — MSVC değil | doğrulandı (kotlinlang) |
| 2 | `os.name` değer dizgesi JVM/OS sürümüne göre değişir — önek eşleşmesi kullanılır (§G8.3 notu ile aynı) | §K3 #29 |
| 3 | Kotlin/Native C bridging sözdizimi (`utsname`, `strcmp`, `open`/`read`) — doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 4 | JVM ve Native yolları aynı kod tabanında karıştırılmamalı — hedef seçimi build konfigürasyonuna bağlı | tasarım notu |

---

## §G11 C++ — 3 OS DALI

### §G11.1 OS makro dallanması (C++ perspective)

> **Şablon — derlenmedi/çalıştırılmadı.**
> C++ koşullu derleme: https://en.cppreference.com/w/cpp/preprocessor/conditional (§K3 #34 — `#if`/`#elif`/`#else`/`#endif` + `defined()`);
> `_WIN32`: https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros (§K3 #23);
> `__linux__`: https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html (§K3 #24);
> `__APPLE__`: **⚠️ VERIFICATION REQUIRED** (§K4 R4 — §G2.1 ile aynı durum).

```cpp
// G11.1 — C++ OS makro dallanması (tasarım şablonu — derlenmedi)
#ifndef CM_CPP_PLATFORM_H
#define CM_CPP_PLATFORM_H

#if defined(_WIN32)
  #define CM_OS_WINDOWS 1
#elif defined(__linux__)
  #define CM_OS_LINUX 1
#elif defined(__APPLE__)   /* ⚠️ VERIFICATION REQUIRED — URL yok (§K4 R4) */
  #define CM_OS_MACOS 1
#else
  #define CM_OS_UNKNOWN 1
#endif

#endif
```

### §G11.2 Windows dalı — Win32 (C++ perspective)

> **Şablon — derlenmedi/çalıştırılmadı.**
> `GetLogicalDrives`: https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getlogicaldrives (§K3 #20);
> `GlobalMemoryStatusEx`: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex (§K3 #21). §G2.2 ile aynı Win32 API — C++ perspective `std::` container ve RAII ile farklılık gösterir.

```cpp
// G11.2 — C++ Windows dalı (tasarım şablonu — derlenmedi)
#ifdef CM_OS_WINDOWS
#define WIN32_LEAN_AND_MEAN
#include <windows.h>
#include <iostream>
#include <vector>

std::vector<std::string> diskKokleri() {
    std::vector<std::string> kokler;
    DWORD maske = GetLogicalDrives();          /* bit 0 = A:\ · bit 1 = B:\ · bit 2 = C:\ vb. */
    if (maske == 0) { std::cerr << "GetLogicalDrives hata: " << GetLastError() << "\n"; return kokler; }
    for (int i = 0; i < 26; ++i) {
        if (maske & (1u << i)) {
            kokler.push_back(std::string(1, static_cast<char>('A' + i)) + ":\\");
        }
    }
    return kokler;
}

unsigned long long toplamBellek() {
    MEMORYSTATUSEX durum{};
    durum.dwLength = sizeof(durum);
    if (GlobalMemoryStatusEx(&durum)) {
        return durum.ullTotalPhys;
    }
    return 0;
}
#endif
```

### §G11.3 Linux dal — `/proc` (C++ perspective)

> **Şablon — derlenmedi/çalıştırılmadı.** `/proc/meminfo`: https://docs.kernel.org/filesystems/proc.html (§K3 #17 — §G2.3 ile aynı kaynak). C++ perspective `std::ifstream` ve `std::string` kullanır.

```cpp
// G11.3 — C++ Linux dalı (tasarım şablonu — derlenmedi)
#ifdef CM_OS_LINUX
#include <fstream>
#include <string>

long toplamBellekMB() {
    std::ifstream f("/proc/meminfo");
    if (!f) { return -1; }
    std::string satir;
    while (std::getline(f, satir)) {
        if (satir.rfind("MemTotal:", 0) == 0) {          /* satır başı kontrolü */
            auto pos = satir.find_first_of("0123456789");
            if (pos != std::string::npos) {
                return std::stol(satir.substr(pos)) / 1024;
            }
        }
    }
    return -1;
}
#endif
```

### §G11.4 macOS (Darwin) dalı — `uname` + IOKit (C++ perspective)

> **Şablon — derlenmedi/çalıştırılmadı.**
> `uname`: https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html (§K3 #28 — §G2.4 ile aynı POSIX arayüzü);
> IOKit: https://developer.apple.com/documentation/iokit (§K3 #14 — §G2.4/G6.3 ile aynı kayıt, üye adı doğrulanmadı → ⚠️).

```cpp
// G11.4 — C++ macOS dalı (tasarım şablonu — derlenmedi)
#ifdef CM_OS_MACOS
#include <sys/utsname.h>
#include <iostream>

void sistemAdi() {
    struct utsname u;
    if (uname(&u) == 0) {                 /* POSIX uname(2) */
        std::cout << "sysname: " << u.sysname << "\n";   /* Darwin */
        std::cout << "machine: " << u.machine << "\n";   /* arm64 | x86_64 */
    }
}
/* Donanım kayıt erişimi (disk/USB/SES) IOKit IORegistry üzerinden yapılır —
   IOServiceMatching + IOServiceGetMatchingService kalıbı için
   https://developer.apple.com/documentation/iokit
   (üye adı doğrulanmadı → tam çağrı yazılmadı ⚠️ VERIFICATION REQUIRED) */
#endif
```

### §G11.5 Derleme komutları (C++ — OS başına)

| # | OS | Derleyici | Örnek komut | Kaynak durumu |
|---|----|-----------|-------------|----------------|
| 1 | Windows | MSVC `cl` (C++ modu) | `cl /std:c++17 /O2 /W4 cm_platform.cpp` | ⚠️ şablon (derlenmedi) |
| 2 | Linux | GCC `g++` | `g++ -std=c++17 -O2 -Wall cm_platform.cpp -o cm_platform` | ⚠️ şablon (derlenmedi) |
| 3 | macOS | Clang++ (Xcode CLT) | `clang++ -std=c++17 -O2 -Wall cm_platform.cpp -o cm_platform` | ⚠️ şablon (derlenmedi) |
| 4 | macOS + IOKit | Clang++ | `clang++ cm_platform.cpp -framework IOKit -o cm_platform` | ⚠️ şablon (derlenmedi) |

### §G11.6 OS dalı özeti (C++)

| # | OS | makro | Disk/bellek kaynağı | Ayırıcı (§G2 ile) |
|---|----|-------|---------------------|--------------------|
| 1 | Windows | `_WIN32` | `GetLogicalDrives` + `GlobalMemoryStatusEx` (Win32) | C++ `std::vector`/RAII vs C dizgi |
| 2 | Linux | `__linux__` | `/proc/meminfo` (`std::ifstream`) | C++ stream vs C `FILE*` |
| 3 | macOS | `__APPLE__` ⚠️ | `uname` (POSIX) + IOKit ⚠️ | C++ `std::cout` vs C `printf` |

> **Risk/uyumluluk notu (§G11):**

| # | Sınır | Durum |
|---|-------|-------|
| 1 | `__APPLE__` ön-tanımlı makrosu — doğrulanmış birincil URL yok (§K4 R4, §G2.1 ile aynı) | ⚠️ VERIFICATION REQUIRED |
| 2 | §G2 (C) ve §G11 (C++) aynı Win32/POSIX API'leri kullanır — fark yalnızca dil perspective (std kütüphanesi vs C dizgi); tek API yüzeyi, iki sözdizimi | tasarım notu |
| 3 | C++ std kütüphanesi disk kök listeleme ve IOKit/Win32/ALSA çağrıları içermez — OS-specific kod her hedefte ayrı derlenir | §G5.3 sınır notu ile aynı |

---


## §DOĞRULAMA

### §K1 Kabul kapıları (G1–G11 — bu dosya, 2026-10-14 ölçümü · tur-2 dahil)

| Kapı | Koşul | Ölçüm | Sonuç |
|------|-------|-------|-------|
| G1 | FM 10 alan eksiksiz | node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group | ✅ 10/10 |
| G2 | Satır sayısı 500 ve üzeri | Editör sayaç ölçümü (yazım sonrası) | ✅ (ölçüm §K2) |
| G3 | Son satır kanonik | `Authority:` + `Mode:` satırı | ✅ |
| G4 | Yeni URL HTTP 200 | 33 URL HEAD/GET (29 tur-1 + 4 tur-2 — 2026-10-14, §K3 #31–#34) | ✅ 33/33 |
| G5 | Envanter kesişimi | K001 geneli grep (2026-10-14) — 29 adresin 28'i klasörde hiç kullanılmamış | ✅ yinelenen kanıt yok · tek kesişim `alsa-project.org` = önceki kayıt W10 (**KULLANILMADI**) → bu dosyada ilk KULLANILDI kullanımı (durum güncellemesi, çift kanıt değil) |
| G6 | Doldurucu yok | TBD · üç nokta · "Yakında" taraması | ✅ |
| G7 | Mojibake yok | UTF-8 doğrulama | ✅ |
| G8 | Yetkisiz yazım | `donanaım-type` yazımı değiştirilmedi; kaynak kod dosyasına yazılmadı | ✅ |

### §K2 Ölçüm kaydı

| Ölçüm | Değer | Tarih |
|-------|-------|-------|
| Toplam satır | 1383 (tur-1: 1018 → tur-2: +365) | 2026-10-14 (tur-2) |
| Kod bloğu sayısı | JS 3 · C 4 · Python 4 · Go 3 · Rust 2 · C# 3 · PHP 3 · Bash 1 · PowerShell 1 · Java 1 · **Swift 4 · Kotlin 5 · C++ 4** = **38** | 2026-10-14 (tur-2 — tur-1: 25) |
| Dil grubu sayısı | **11 başlık (G1–G11)** | 2026-10-14 (tur-2 — tur-1: 8) |
| OS dalı | her dilde Windows · macOS · Linux (§G8.4'te yalnız Shell kaldı — gerekçeli; Swift/Kotlin/C++ §G9–§G11'e taşındı) | 2026-10-14 (tur-2) |

### §K3 URL matrisi (HTTP 200 — 2026-10-14, `Invoke-WebRequest` HEAD/GET)

| # | URL | Ne için | Sonuç |
|---|-----|---------|-------|
| 1 | https://docs.python.org/3/library/platform.html | G3 `platform.system()` | 200 ✅ |
| 2 | https://docs.python.org/3/library/sys.html | G3 `sys.platform` | 200 ✅ |
| 3 | https://nodejs.org/api/process.html | G1 `process.platform` | 200 ✅ |
| 4 | https://nodejs.org/api/os.html | G1 `os.*` | 200 ✅ |
| 5 | https://pkg.go.dev/runtime | G4 `runtime.GOOS` | 200 ✅ |
| 6 | https://pkg.go.dev/runtime#GOOS | G4 `GOOS` bölümü | 200 ✅ |
| 7 | https://doc.rust-lang.org/reference/conditional-compilation.html | G5 `cfg!`/`#[cfg]` | 200 ✅ |
| 8 | https://doc.rust-lang.org/std/env/consts/constant.OS.html | G5 `consts::OS` | 200 ✅ |
| 9 | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation | G6/G8b algılama | 200 ✅ |
| 10 | https://learn.microsoft.com/en-us/dotnet/api/system.io.driveinfo | G6.2/G8.2 disk | 200 ✅ |
| 11 | https://www.php.net/manual/en/reserved.constants.php | G7 `PHP_OS_FAMILY` | 200 ✅ |
| 12 | https://www.php.net/manual/en/function.php-uname.php | G7 `php_uname()` | 200 ✅ |
| 13 | https://www.php.net/manual/en/function.disk-total-space.php | G7.2 `disk_total_space` | 200 ✅ |
| 14 | https://developer.apple.com/documentation/iokit | G2.4/G6.3 donanım kayıt | 200 ✅ |
| 15 | https://developer.apple.com/documentation/coreaudio | P5 macOS ses | 200 ✅ |
| 16 | https://docs.kernel.org/filesystems/sysfs.html | G2.3 `/sys` | 200 ✅ |
| 17 | https://docs.kernel.org/filesystems/proc.html | G2.3/G3.3/G4.2/G6.3 `/proc` | 200 ✅ |
| 18 | https://docs.kernel.org/admin-guide/sysctl/ | P2 Linux sysctl | 200 ✅ |
| 19 | https://docs.kernel.org/admin-guide/mm/index.html | P2 Linux bellek yönetimi | 200 ✅ |
| 20 | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getlogicaldrives | G2.2 disk kökleri | 200 ✅ |
| 21 | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex | G2.2/G3.4/G6.3 bellek | 200 ✅ |
| 22 | https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses | P3/G8.2 ağ adapterları | 200 ✅ |
| 23 | https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros | G2.1 `_WIN32` | 200 ✅ |
| 24 | https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html | G2.1 `__linux__` | 200 ✅ |
| 25 | https://gcc.gnu.org/onlinedocs/cpp/Conditionals.html | G2.1 koşullu derleme | 200 ✅ |
| 26 | https://en.cppreference.com/w/c/preprocessor/conditional.html | G2.1 `#if/#elif` | 200 ✅ |
| 27 | https://man7.org/linux/man-pages/man2/uname.2.html | G8.1 `uname` (Linux) | 200 ✅ |
| 28 | https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html | G8.1 `uname` (POSIX/Darwin) | 200 ✅ |
| 29 | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html | G8.3 `System.getProperty` | 200 ✅ |
| 30 | https://www.alsa-project.org/ | P5 Linux ses (ALSA) — erişim kaydı | 200 ✅ (GET) |
| 31 | https://docs.swift.org/latest/documentation/the-swift-programming-language/statements | G9 `#if os()` koşullu derleme | 200 ✅ (HEAD, tur-2) |
| 32 | https://kotlinlang.org/docs/native-platform-libs.html | G10 Kotlin/Native `platform.posix.*` | 200 ✅ (HEAD, tur-2) |
| 33 | https://kotlinlang.org/docs/java-interop.html | G10 Kotlin ↔ Java erişimi | 200 ✅ (HEAD, tur-2) |
| 34 | https://en.cppreference.com/w/cpp/preprocessor/conditional | G11 C++ koşullu dahil etme | 200 ✅ (HEAD, tur-2) |

### §K4 Reddedilen URL'ler (bu turda kullanılmadı)

| # | URL | Sonuç | Karar |
|---|-----|-------|-------|
| R1 | https://developer.apple.com/documentation/kernel/sysctl | **404** | KULLANILMADI — macOS sysctl iddiaları ⚠️ |
| R2 | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.informationruntimeinformation | **404** | KULLANILMADI — #9 kullanıldı |
| R3 | https://learn.microsoft.com/en-us/windows/win32/sysinfo/globalmemorystatus | **404** | KULLANILMADI — #21 kullanıldı |
| R4 | https://developer.apple.com/documentation/kernel/predefined-macros | **404** | KULLANILMADI — `__APPLE__` ⚠️ VERIFICATION REQUIRED |
| R5 | https://learn.microsoft.com/en-us/cpp/preprocessor/preprocessor-macros | **404** | KULLANILMADI — #23 kullanıldı |
| R6 | https://learn.microsoft.com/en-us/cpp/preprocessor/conditional-compilation | **404** | KULLANILMADI — #25/#26 kullanıldı |
| R7 | https://clang.llvm.org/docs/Manual/PredefinedMacros.html | **404** | KULLANILMADI — kaynak yok |
| R8 | https://developer.apple.com/library/archive/ (getifaddrs_ref — tam adres 404, §K4) | **404** | KULLANILMADI — P3 macOS `getifaddrs` ⚠️ |

> **Kural:** 404 dönen URL hiçbir iddiaya bağlanmaz; ilgili iddia `⚠️ VERIFICATION REQUIRED` olarak kalır (zero-hallucination §3).

---

## §YASAKLAR

| # | Yasak | Gerekçe |
|---|-------|---------|
| 1 | `TBD` · üç nokta doldurucu · "Yakında" | Guardrail #6 (G6 kapısı) |
| 2 | Doğrulanmamış API/üye adı — doğrulanamıyorsa `⚠️ VERIFICATION REQUIRED` zorunlu | Zero-hallucination §3 |
| 3 | Uydurma paket/crate/kütüphane adı (Rust crate, Go modülü vb.) | §G5.3 — ADR bekliyor |
| 4 | Şablon kodu "çalışıyor/üretim hazır" diye sunmak | §KAPSAM 3 — derlenmedi/çalıştırılmadı |
| 5 | 404 URL'i iddiaya bağlamak | §K4 |
| 6 | `git commit` | Orkestratöre aittir (AGENTS.md §13.9/5) |
| 7 | `donanaım-type` yazımını "düzeltmek" | v2 §3 KURAL 1 — dokunulmaz |

## §OPEN QUESTIONS

| # | Soru | Etki | Sahip |
|---|------|------|-------|
| Q1 | Kod şablonları hangi dilde K001 üretim koduna önce alınır? (repo dili PHP/JS — §PLATFORM MATRİSİ dil özeti) | Üretim sırası | mimari |
| Q2 | Rust/Go için disk kök listeleme sarmalayıcısı hangi kütüphane/ADR ile seçilecek? | §G4.3 · §G5.3 | architect |
| Q3 | macOS/Linux test makinesi yok — CI'da derleme kapısı kurulacak mı? | Şablon→üretim geçişi | devops |
| Q4 | `__APPLE__` ve `sysctl hw.memsize` doğrulanana kadar macOS dalları geçerli sayılacak mı? | §G2.1 · §G3.4 | kanit |
| Q5 | P5 ses alt sistemi (WASAPI/CoreAudio/ALSA) şablonları ayrı dosyaya mı taşınır? | Dosya boyutu | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
