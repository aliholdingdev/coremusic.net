---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-kod-seti"
node_type: code
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kapsam"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-kod-ornekleri"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/index"
evidence:
  - "https://docs.kernel.org/ (✅ HTTP 200 · 2026-10-13 · linux/index.md §evidence)"
  - "https://man7.org/linux/man-pages/man2/syscalls.2.html (✅ HTTP 200 · 2026-10-13 · linux/index.md §evidence)"
  - "https://man7.org/linux/man-pages/man7/pthreads.7.html (✅ HTTP 200 · 2026-10-13)"
  - "https://man7.org/linux/man-pages/man7/cgroups.7.html (✅ HTTP 200 · 2026-10-13)"
  - "https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig (✅ HTTP 200 · 2026-10-14 · Zig std.posix imzaları ham kaynaktan okundu)"
  - "13-dil URL kapısı (✅ HTTP HEAD 200 · 2026-10-14 · 19/19 — ayrıntı §10.1; 404 aday elendi)"
validation:
  - "2026-10-15: 15 benzersiz resmi URL (HTTP 200 kapısı) · 15 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-13: Dosya/Satır UYDURMA YASAK — repo'da Linux-specific .c/.cpp YOK; TÜM kod blokları '[ORİJİNAL ÖRNEK · derlenmedi]' etiketlidir; resmi alıntı YOK."
  - "≥ hedef 500 satır · 10 alanlı frontmatter · linux/ içinde URL ayrışımı (linux-kod-ornekleri.md ile kesişim 0 — bu dosya man7/docs.kernel kök yüzeyi kullanır)."
  - "Linux derleme/çalışma kanıtı YOK → status: verify-required."
  - "2026-10-14: 13-dil genişletmesi — 7 yeni dil bloğu (8 fence: C · C++20 · Zig · Swift · Java · Kotlin · Bash · PowerShell) eklendi (0 silme); tüm fence URL'leri HTTP HEAD 200 (§10.1); derleme kanıtı yok → status verify-required korunur."
open_questions:
  - "L-S1: Linux üretim hedefi mi? (linux/index.md L-D2 · Q4/T-U3) — Raspberry Pi 5 ipucu var, tür kararı DEĞİL → UNKNOWN"
  - "L-S2: systemd/cgroup/ALSA özel desenler bu dosyada YOK — komşu dalda (linux-kod-ornekleri.md); tekrar yasak"
group: kod
---

# linux-kod-seti — Portable Dil Seti (Linux hedefi · Üretim/CI kontratı · RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-13 · **Durum:** verify-required
> **Kapsam:** `linux/` beyazliste — portable çok-dilli çekirdek (13 dil yüzeyi: Node, Python,
> Go, Rust, PHP, C#, C, C++20, Zig, Swift, Java/Kotlin, Bash, PowerShell) → Linux hedefi.
> Bu dosya `linux-kod-ornekleri.md` (systemd/cgroup/ALSA gerçek
> desenleri) ile **ayrışık**dır: orada Linux-çekirdek yüzeyi, burada **dil havuzu +
> taşınabilirlik**. Üretim/CI kontratı **Linux'dur** (`../linux/index.md` §Contract) —
> bu dosyanın hedefi **üretim kontratının kendisi**dir (Linux, taşınabilirlik zincirinin
> üretim ucudur).

---

## §0. Kod kaynak politikası (en üst — bağlayıcı)

> **ZERO-HALLUCINATION (bu turda mutlak):** Repo'da Linux-specific `.c`/`.cpp` **YOK**;
> `Dosya:/Satır:` **UYDURMA YASAK**. systemd/cgroup/ALSA özel desenleri bu dosyada **YOK**
> (komşu dalda — `linux-kod-ornekleri.md`). Bu dosyadaki TÜM bloklar
> `**[ORİJİNAL ÖRNEK · derlenmedi]**` etiketlidir; yalnız **taşınabilir dil çekirdeği** +
> Linux ortam-keşfi desenleri verilir.

| Kaynak türü | Durum | Kural |
|---|---|---|
| Repo'daki Linux-specific dosya | **YOK** (`.c`/`.cpp` 0) | `Dosya:/Satır:` **UYDURMA YASAK** |
| systemd/cgroup/ALSA özel desen | **YOK** (komşu dalda) | `linux-kod-ornekleri.md` — tekrar yasak |
| Orijinal örnek (bu vault) | Bu dosyadaki TÜM bloklar | `[ORİJİNAL ÖRNEK · derlenmedi]` zorunlu |
| Linux derleme/çalışma kanıtı | **YOK** | `status: verify-required` |

---

## §1. GÖREV & SINIR

**Bu dosya VERİR:** (a) CoreMusic dil havuzunun Linux (üretim/CI kontratı) hedefine
taşınması, (b) Linux- sınıf portable build yüzeyi, (c) dil-bazlı taşınabilirlik
matrisi. **Bu dosya VERMEZ:** systemd/cgroup/ALSA özel desenleri (`linux-kod-ornekleri.md`),
Linux çekirdek/API katmanı (`linux-kernel.md`, `linux-api.md`).

**Model:** Linux, üretim/CI kontratının hedefidir (`../linux/index.md` §Contract). Bu
dosyanın hedefi **Linux- sınıf** portable CLI/set kodudur — taşınabilirlik zincirinin
**üretim ucudur**; Windows/macOS/mobil hedefler bu çekirdeğin sınıf-içi türevidir.

---

## §2. GRUP YAPISI

| # | Grup | Ne verir | Dil |
|---|------|---------|-----|
| 2.1 | Portable CLI çekirdeği (Linux hedefi) | Node/Python/pwsh CLI → Linux (üretim) | Node / Python / PowerShell (pwsh) |
| 2.2 | Linux- sınıf portable build yüzeyi | CMake iskeleti (gcc/clang dual) + C/C++ POSIX çekirdek | C / C++20 / CMake / Bash |
| 2.3 | Rust/Go/Zig (Linux GNU hedefi) | taşınabilir sistem dili → Linux | Rust / Go / Zig |
| 2.4 | PHP CLI (Linux hedefi) | PHP/tek dosya → Linux (üretim) | PHP |
| 2.5 | C# / .NET (Linux hedefi) | .NET CLI → Linux | C# |
| 2.6 | JVM (Linux hedefi) | JVM CLI → Linux (üretim) | Java / Kotlin |
| 2.7 | Swift (Linux hedefi) | Swift CLI → Linux (üretim) | Swift |

**Toplam (2026-10-14):** 7 grup · 15 kod bloğu (Java/Kotlin ayrı fence sayılır) · 13 dil yüzeyi
(Node, Python, Go, Rust, PHP, C#, C, C++20, Zig, Swift, Java/Kotlin, Bash, PowerShell;
+ CMake build yüzeyi). 2026-10-13 tabanı: 5 grup · 7 blok · 6 dil yüzeyi → +2 grup · +8 blok ·
+7 dil yüzeyi (0 silme; GRUP 2.6/2.7 sona eklendi — § sırası korundu, bkz §14/§15 notu).

---

## §3. GRUP 2.1 — PORTABLE CLI ÇEKİRDEĞİ (LİNUX HEDEFİ)

### §3.1 Node CLI → Linux (üretim)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI — üretim çekirdeği).
> Linux: üretim/CI kontratı → Node CLI **sınıf-içi taşınabilir** (aynı kod). Aşağıda
> yalnız **fark** (Linux ortam-keşfi) gösterilir.

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi · Node.js 20 LTS · Linux (üretim) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: Linux ortam-keşfi
// (os.platform). Üretim: aynı CLI Linux sunucuda çalışır; bu dosya üretim kontratının
// kendisidir. Derlenmedi/çalıştırılmadı.
'use strict';

const os = require('node:os');

function probe() {
  return {
    platform: os.platform(),   // 'linux'
    arch: os.arch(),            // 'arm64' (Raspberry Pi 5 ipucu) / 'x64'
    eol: os.EOL,                // '\n' (POSIX)
  };
}

const info = probe();
console.log('platform:', info.platform, 'arch:', info.arch, 'eol:', JSON.stringify(info.eol));
```

### §3.2 Python CLI → Linux (üretim)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Python CLI — üretim çekirdeği).
> Linux: üretim/CI kontratı → Python CLI **sınıf-içi taşınabilir** (aynı kod).

```python
# [ORİJİNAL ÖRNEK · derlenmedi · Python 3.11 · Linux (üretim) hedefi]
# Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: Linux ortam-keşfi
# (platform). Derlenmedi/çalıştırılmadı.
from __future__ import annotations
import platform
import sys

def probe() -> dict[str, str]:
    """Linux ortam keşfi (üretim): system/arch/platform."""
    return {
        "system": platform.system(),      # 'Linux'
        "arch": platform.machine(),       # 'aarch64' (Raspberry Pi 5) / 'x86_64'
        "platform": platform.platform(),
    }

def main() -> int:
    info = probe()
    for key, value in info.items():
        print(f"{key}={value}")
    return 0

if __name__ == "__main__":
    sys.exit(main())
```

### §3.3 PowerShell (pwsh) → Linux (üretim) — ⚠️ çapraz yüzey

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §G.2 (pwsh çekirdeği — Windows sınıfı).
> Dürüst ifade: Linux'ta **yerel CI/operasyon kabuğu bash'tir** (§4.4); pwsh (PowerShell 7+)
> Linux'ta resmi kurulum yüzeyiyle **çapraz çalışır** → bu blok `⚠️ çapraz` işaretlidir.
> `$IsLinux` yalnız PowerShell Core'da tanımlıdır (about_automatic_variables — HEAD 200, §10.1);
> Windows PowerShell 5.1'de tanımsızdır → ilk kontrol throw üretir (sessiz geçiş yok).

```powershell
# https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_automatic_variables
# [ORİJİNAL ÖRNEK · derlenmedi · PowerShell 7 (pwsh) · Linux (üretim) hedefi · ⚠️ çapraz yüzey (yerel Linux kabuğu §4.4)]
$ErrorActionPreference = 'Stop'   # hatada dur (bash set -e karşılığı — §G.2 ile aynı disiplin)
if (-not $IsLinux) {
    throw 'Bu betik pwsh ile Linux hedefinde çalıştırılmalıdır; Windows PowerShell 5.1 sınıf dışı'
}
if ([string]::IsNullOrEmpty($env:PATH)) {
    throw 'PATH ortam değişkeni tanımsız — sessiz fallback yok'
}
Write-Output "pwsh=$($PSVersionTable.PSVersion) cpus=$([Environment]::ProcessorCount)"
```

---

## §4. GRUP 2.2 — LINUX- SINIF PORTABLE BUILD YÜZEYİ

### §4.1 CMake iskeleti (Linux hedefi — gcc/clang dual)

> **Referans:** `linux-kod-ornekleri.md` (systemd/cgroup/ALSA desenleri). Bu dosyada
> yalnız **Linux- sınıf portable** CMake yüzeyi (gcc/clang dual) verilir; systemd/cgroup
> detayı komşu dalda.

```cmake
# [ORİJİNAL ÖRNEK · derlenmedi · CMake · Linux (üretim) hedefi]
# Linux- sınıf portable CMake: gcc/clang dual. systemd/cgroup/ALSA detayı
# linux-kod-ornekleri.md'de. Derlenmedi.
cmake_minimum_required(VERSION 3.20)
project(vault_linux_set LANGUAGES C CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

add_library(vault_core STATIC src/core.cpp)
target_include_directories(vault_core PUBLIC ${CMAKE_CURRENT_SOURCE_DIR}/include)

if(CMAKE_CXX_COMPILER_ID MATCHES "Clang")
    target_compile_options(vault_core PRIVATE -Wall -Wextra)
elseif(CMAKE_CXX_COMPILER_ID STREQUAL "GNU")
    target_compile_options(vault_core PRIVATE -Wall -Wextra)
endif()
```

### §4.2 C → Linux (POSIX üretim yüzeyi — isatty)

> **Referans:** Çekirdek platform makrosu `../isletim-sistemi-turu-kod-seti.md` §A.7 ile AYNI
> (`#elif __linux__` derleme-zamanı zinciri — bu blokta tekrar YOK, R4). Bu blok Linux üretim
> yüzeyinin **çalışma-zamanı** POSIX yüzeyidir: stdout TTY tespiti (log biçimlendirme kararı).
> systemd/cgroup/ALSA desenleri komşu dalda (`linux-kod-ornekleri.md`); uzantı arayüzü:
> man3/getenv.3 (HEAD 200, §10.1).

```c
/* https://man7.org/linux/man-pages/man3/isatty.3.html */
/* [ORİJİNAL ÖRNEK · derlenmedi · C11/POSIX · Linux (üretim) hedefi] */
/* Çekirdek platform makrosu: isletim-sistemi-turu-kod-seti.md §A.7 ile AYNI (#elif __linux__). */
#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

int cm_stdout_isatty(int *out_flag) {
    errno = 0;
    int r = isatty(STDOUT_FILENO);
    if (r == 1) { *out_flag = 1; return 0; }
    if (r == 0) { *out_flag = 0; return 0; }
    return errno != 0 ? errno : EIO;   /* -1 durumu: açık hata kodu — sessiz fallback yok */
}

int main(void) {
    int flag = 0;
    int err = cm_stdout_isatty(&flag);
    if (err != 0) {
        fprintf(stderr, "isatty(1) başarısız: errno=%d\n", err);
        return EXIT_FAILURE;
    }
    printf("tty=%d\n", flag);   /* platform sınıfı derleme zamanı: §A.7 (tekrar yok — R4) */
    return EXIT_SUCCESS;
}
```

### §4.3 C++20 → Linux (zorunlu ortam + taşınabilir zaman ölçümü)

> **Referans:** `std::filesystem`/OS sınıfı çekirdeği `../isletim-sistemi-turu-kod-seti.md` §B.7
> ile AYNI taşınabilir sınıf (ayrı blok YOK — R4). Bu blok üretim CLI'ının iki farklı yüzeyini
> verir: zorunlu ortam değişkeni (throw) + `steady_clock` ölçüm kancası. CLOCK_MONOTONIC/jitter
> gerçekliği zamanlama-çekirdek konusudur → komşu dalda (`linux-kod-ornekleri.md` §5); bu dosyada
> iddia edilmez.

```cpp
// https://en.cppreference.com/w/cpp/chrono/steady_clock
// [ORİJİNAL ÖRNEK · derlenmedi · C++20 · Linux (üretim) hedefi]
// getenv arayüzü: https://en.cppreference.com/w/c/program/getenv (HEAD 200, §10.1) — C++ <cstdlib> da sunar.
#include <chrono>
#include <cstdint>
#include <cstdlib>
#include <stdexcept>
#include <string>

std::string cm_required_env(const char *name) {
    const char *value = std::getenv(name);   // nullptr = tanımsız değişken (hatta değil)
    if (value == nullptr) {
        throw std::runtime_error(std::string(name) + " ortam değişkeni tanımsız — sessiz fallback yok");
    }
    return std::string(value);
}

std::int64_t cm_probe_step_ns() {
    const auto t0 = std::chrono::steady_clock::now();
    volatile int sink = 0;
    for (int i = 0; i < 64; ++i) {
        sink = sink + i;                     // ölçüm kancası — DSP/jitter kapsamı komşu dalda
    }
    (void)sink;
    return std::chrono::duration_cast<std::chrono::nanoseconds>(
        std::chrono::steady_clock::now() - t0).count();
}
```

### §4.4 Bash → Linux (yerel CI/operasyon kabuğu)

> **Referans:** Kabuk disiplini (set -euo pipefail, fail-safe) `../isletim-sistemi-turu-kod-seti.md`
> §G.1 (ffprobe sarmalayıcı çekirdeği) ile AYNI; bu blok Linux **ortam-keşfi**dir. `OSTYPE` ve
> `BASH_VERSINFO` bash(1) sayfasında tanımlıdır (fence URL — HEAD 200). Windows'ta yerel bash
> yoktur (Git Bash/WSL varsayımı UNKNOWN — §G.3); bu blok sınıf-içi Linux kabuğudur.

```bash
#!/usr/bin/env bash
# https://man7.org/linux/man-pages/man1/bash.1.html
# [ORİJİNAL ÖRNEK · derlenmedi · bash 5 · Linux (üretim) hedefi — Linux'un yerel CI/operasyon kabuğu]
set -euo pipefail
: "${PATH:?PATH tanımlı değil — betik durur (sessiz fallback yok)}"
if [[ "${OSTYPE}" != linux* ]]; then
  echo "desteklenmeyen OSTYPE=${OSTYPE} — UNKNOWN sınıf, Linux hedefi reddedildi" >&2
  exit 1
fi
tty=false
if [[ -t 1 ]]; then tty=true; fi
printf 'bash=%s.%s OSTYPE=%s tty=%s\n' "${BASH_VERSINFO[0]}" "${BASH_VERSINFO[1]}" "${OSTYPE}" "${tty}"
```

---

## §5. GRUP 2.3 — RUST/GO/ZIG (LİNUX GNU HEDEFİ)

### §5.1 Rust → Linux (Linux GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Rust — üretim çekirdeği).
> Linux: üretim/CI kontratı → Rust (Linux GNU hedefi) **sınıf-içi taşınabilir**.

```rust
// [ORİJİNAL ÖRNEK · derlenmedi · Rust · Linux (üretim) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Rust) ile AYNI taşınabilir desen.
// Fark: Linux GNU hedefi. Derlenmedi/çalıştırılmadı.
use std::env;

fn probe() -> String {
    // Linux ortam keşfi (üretim); Linux-özel API YOK (§0)
    format!("target_os={}", std::env::consts::OS)
}

fn main() {
    let args: Vec<String> = env::args().collect();
    println!("{} · argüman sayısı: {}", probe(), args.len().saturating_sub(1));
}
```

### §5.2 Go → Linux (Linux GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Go — üretim çekirdeği).
> Linux: üretim/CI kontratı → Go (GOOS=linux) **sınıf-içi taşınabilir**.

```go
// [ORİJİNAL ÖRNEK · derlenmedi · Go 1.22 · Linux (üretim) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Go) ile AYNI taşınabilir desen.
// Fark: GOOS=linux. Derlenmedi/çalıştırılmadı.
package main

import (
	"fmt"
	"os"
	"runtime"
)

func probe() string {
	// Linux ortam keşfi (üretim); Linux-özel API YOK (§0)
	return fmt.Sprintf("GOOS-like=%s/%s", runtime.GOOS, runtime.GOARCH)
}

func main() {
	fmt.Printf("%s · argüman sayısı: %d\n", probe(), len(os.Args)-1)
}
```

### §5.3 Zig → Linux (POSIX üretim yüzeyi — rlimit/isatty)

> **Referans:** Çekirdek platform tespiti `../isletim-sistemi-turu-kod-seti.md` §A.8
> (`builtin.os.tag`) ile AYNI (ayrı blok YOK — R4). Bu blok `std.posix` **çalışma-zamanı**
> yüzeyidir; tüm imzalar raw std kaynakından okundu (§10.1): `isatty(handle: fd_t) bool` ·
> `getrlimit(resource) GetrlimitError!rlimit` · `setrlimit(resource, limits) SetrlimitError!void` ·
> `linux.rlimit { cur, max }` · `rlimit_resource.NOFILE`. CPU sayısı yüzeyi:
> `Thread.getCpuCount() CpuCountError!usize` (`std/Thread.zig`, HEAD 200); dil belgesi:
> ziglang.org/documentation/master/ (HEAD 200).

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
// [ORİJİNAL ÖRNEK · derlenmedi · Zig master std · Linux (üretim) hedefi]
// Çekirdek platform tespiti: isletim-sistemi-turu-kod-seti.md §A.8 (builtin.os.tag) ile AYNI.
const std = @import("std");
const posix = std.posix;

/// fd-yoğun üretim süreçleri için NOFILE soft limit'i hard limit'e eşitler.
/// Hata açık: error union (try) — sessiz fallback yok.
pub fn raiseNofile() !void {
    const hard = (try posix.getrlimit(.NOFILE)).max;
    try posix.setrlimit(.NOFILE, .{ .cur = hard, .max = hard });
}

/// Linux üretim ortamı keşfi: fd limiti + stdout TTY (log biçimi kararı).
pub fn probe() !void {
    const limits = try posix.getrlimit(.NOFILE);
    std.debug.print("NOFILE cur={d} max={d} stdout_tty={}\n", .{ limits.cur, limits.max, posix.isatty(1) });
}
```

---

## §6. GRUP 2.4 — PHP CLI (LİNUX HEDEFİ)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §8 (PHP CLI — üretim çekirdeği).
> Linux: üretim/CI kontratı → PHP CLI/tek dosya **sınıf-içi taşınabilir**. Uzman dalı:
> PHP backend (`../index.md` §Model).

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi · PHP 8.4 · Linux (üretim) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §8 (PHP CLI) ile AYNI taşınabilir desen.
// Fark: Linux ortam-keşfi (PHP_OS_FAMILY). Derlenmedi/çalıştırılmadı.
declare(strict_types=1);

// Linux ortam keşfi (üretim): PHP_OS_FAMILY
echo 'os_family=' . PHP_OS_FAMILY . PHP_EOL;   // 'Linux'
echo 'argüman sayısı=' . ($argc - 1) . PHP_EOL;
```

---

## §7. GRUP 2.5 — C# / .NET (LİNUX HEDEFİ)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` C# çekirdeği. Linux: üretim/CI
> kontratı → .NET CLI (net8.0 / Linux hedefi) **sınıf-içi taşınabilir**.

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi · C# / .NET 8 · Linux (üretim) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md C# çekirdeği ile AYNI taşınabilir desen.
// Fark: Linux ortam-keşfi (OSPlatform). Derlenmedi/çalıştırılmadı.
using System;
using System.Runtime.InteropServices;

class Probe
{
    static void Main(string[] args)
    {
        // Linux ortam keşfi (üretim): OSPlatform kontrolü
        Console.WriteLine($"linux={RuntimeInformation.IsOSPlatform(OSPlatform.Linux)}");
        Console.WriteLine($"argüman sayısı={args.Length}");
    }
}
```

---

## §8. PORTABİLİTE TABLOSU (LİNUX HEDEFİ)

> **Öneri (2026-10-13):** Dil bazlı taşınabilirlik, Linux = üretim/CI kontratı olduğundan
> hepsi **sınıf-içi taşınabilir** (aynı kod). Linux, taşınabilirlik zincirinin
> **üretim ucudur**; Windows/macOS/mobil bu çekirdeğin türevidir (L-S1 UNKNOWN).

| Dil | Linux-native | Portable çekirdek | Not |
|-----|--------------|-------------------|-----|
| **C (POSIX)** | ⚠️ | ✔ | çalışma-zamanı POSIX yüzeyi §4.2 · systemd/cgroup/ALSA desenleri `linux-kod-ornekleri.md` |
| **C++20** | ⚠️ | ✔ | zorunlu ortam + steady_clock §4.3 · filesystem çekirdeği master §B.7 |
| **C# (.NET)** | ✔ | ✔ | .NET Linux hedefi — sınıf-içi |
| **JS (Node CLI)** | — | ✔ | Linux ortam-keşfi (§3.1) |
| **Python CLI** | — | ✔ | Linux ortam-keşfi (§3.2) |
| **Go (GOOS=linux)** | — | ✔ | Linux GNU hedefi (§5.2) |
| **Rust (Linux GNU)** | — | ✔ | Linux GNU hedefi (§5.1) |
| **Zig (std.posix)** | ✔ | ✔ | Linux GNU hedefi · ham std imzaları §5.3 |
| **Swift (Foundation)** | ⚠️ | ✔ | toolchain ile Linux yüzeyi [KAVRAM] §15.1 · Foundation Windows [TASARIM] |
| **Java (JVM)** | ✔ | ✔ | çalışma-zamanı tespit (§14.1) |
| **Kotlin (JVM)** | ✔ | ✔ | aynı JVM · Kotlin idiomu (§14.2) |
| **Bash** | ✔ | ❌ | Linux'un yerel CI kabuğu (§4.4) · Windows yerel yüzeyi yok |
| **PowerShell (pwsh)** | ⚠️ | ✔ | çapraz yüzey (§3.3) · yerel Linux kabuğu bash (§4.4) |
| **PHP CLI** | — | ✔ | CLI/tek dosya — Linux (üretim, §6) |

---

## §9. BAĞLANTI MATRİSİ (İLşKİ ÖZETİ)

| Dosya | Bağlantı türü | Ne için |
|-------|--------------|---------|
| `../isletim-sistemi-turu-kod-seti.md` | `depends_on` | Portable dil çekirdeği (§2/§5/§8 — üretim çekirdeği) |
| `../isletim-sistemi-turu-kapsam.md` | `depends_on` | Sınır (§I-II: systemd/cgroup/ALSA komşu dalda) |
| `linux-kod-ornekleri.md` | `depends_on` | systemd/cgroup/ALSA gerçek desenler (bu dosya dil havuzu ile ayrışık) |
| `index.md` | `depends_on` | Dal künyesi + evidence URL'leri (docs.kernel.org, syscalls.2) |
| `linux-kernel.md` | `related` | Linux çekirdek katmanı (bu dosyada YOK) |
| `linux-api.md` | `related` | Linux API katmanı (bu dosyada YOK) |

---

## §10. DOĞRULAMA

### §10.1 Ölçüm özeti (2026-10-13)

- **HEAD/GET 200:** `docs.kernel.org` · `man7.org/linux/man-pages/man2/syscalls.2.html`
  (linux/index.md §evidence) · `man7.org/linux/man-pages/man7/pthreads.7.html` ·
  `man7.org/linux/man-pages/man7/cgroups.7.html` — 200 ölçüldü.
- **URL ayrışımı:** bu dosya `linux-kod-ornekleri.md` ile kesişim 0 korur (bu dosya
  man7/docs.kernel kök yüzeyi kullanır; komşu dal systemd/cgroup/ALSA desenleri).
- **Elenen aday:** `alsa-project.org/wiki/Main_Page` (500) · freedesktop systemd man
  sayfaları (418) — bu dosyada kullanılmadı (komşu dalın konusu; erişim-hatası).

### §10.1a Ölçüm özeti (2026-10-14 · 13-dil genişletmesi)

- **URL kapısı (gate):** `shell` (PowerShell `Invoke-WebRequest`) subagent'de **reddedildi**
  → kapı aynı gate semantiğiyle Code Mode `fetch` üzerinden **HTTP HEAD** ile alındı
  (200 olmadan blok yazılmaz kuralı birebir korundu; yöntem sapması burada kayıtlıdır).
- **HEAD 200 (19/19):** man7 `man3/isatty.3` · `man3/getenv.3` · `man3/sysconf.3` · `man1/bash.1` ·
  cppreference `w/cpp/chrono/steady_clock` · raw GitHub `ziglang/zig` `lib/std/posix.zig` ·
  `lib/std/Thread.zig` · `lib/std/process.zig` · `lib/std/os/linux.zig` · ziglang.org
  `documentation/master/` · apple.com `documentation/swift/continuousclock` ·
  `documentation/foundation/processinfo` · docs.swift.org swift-book **kökü** (`#if os()`
  bölüm URL'i çözülemedi — kök kullanıldı, not §15.1) · Oracle `java.base/java/lang/System.html` ·
  `java.base/java/lang/Runtime.html` · kotlinlang.org `api/latest/jvm/stdlib/` ·
  learn.microsoft.com `about_automatic_variables` ($IsLinux dokümantasyonu) ·
  `installing-powershell-on-linux` · dotnet `system.runtime.interopservices.runtimeinformation`.
- **404 → elendi:** `en.cppreference.com/w/cpp/program/getenv` (HEAD 404) → alternatif
  `en.cppreference.com/w/c/program/getenv` HEAD **200** (§4.3 notunda kullanıldı).
- **Ham kaynak imza doğrulaması (Zig — tahmin yok):** raw std dosyaları GET ile okundu:
  `pub fn isatty(handle: fd_t) bool` · `pub fn getrlimit(resource: rlimit_resource) GetrlimitError!rlimit` ·
  `pub fn setrlimit(resource: rlimit_resource, limits: rlimit) SetrlimitError!void` ·
  `linux.rlimit = extern struct { cur: rlim_t, max: rlim_t }` · `rlimit_resource` içinde `NOFILE` ·
  `pub fn getCpuCount() CpuCountError!usize` (Thread.zig). Apple docs JSON uçları da okundu:
  ContinuousClock → `now`/`Instant` · ProcessInfo → `environment` · Duration → `components (seconds,
  attoseconds)` + `CustomStringConvertible` doğrulandı.
- **Fence ölçümü (self-check):** dil-etiketli fence açılışları (`<lang>` kalıbı; PowerShell
  `Select-String` subagent'de reddedildi → eşdeğer ölçüm grep `^` + üç-tırnak + `[A-Za-z0-9_+#-]+`
  ile alındı) 2026-10-14 öncesi **7** → sonrası **15** fence (+8: C · C++20 · Zig · Swift ·
  Java · Kotlin · Bash · PowerShell; Java/Kotlin **ayrı** fence). Dil yüzeyi 6 → 13
  (matris §8; GRUP listesi §2).

### §10.2 Taşınabilirlik doğrulaması (§8 tablosu gerekçesi)

| Dil | Linux | Gerekçe | Durum |
|-----|-------|---------|-------|
| C (POSIX, §4.2) | ⚠️ | çalışma-zamanı POSIX yüzeyi eklendi; systemd/cgroup/ALSA desenleri komşu dalda (linux-kod-ornekleri.md) | verify-required |
| C++20 (§4.3) | ⚠️ | zorunlu ortam + steady_clock; zamanlama-çekirdek gerçeği komşu dalda | verify-required |
| C# / Node / Python | ✔ | Linux- sınıf portable çekirdek (§3, §7) | verify-required |
| Go / Rust | ✔ | GOOS=linux / Linux GNU hedefi (§5) | verify-required |
| Zig (§5.3) | ✔ | std.posix imzaları raw std kaynakından doğrulandı (§10.1a) | verify-required |
| Java / Kotlin (§14) | ✔ | JVM çalışma-zamanı tespit (os.name çekirdeği §A.10 ile aynı) | verify-required |
| Swift (§15) | ⚠️ | Foundation Linux yüzeyi toolchain ile gelir [KAVRAM]; çalışma kanıtı yok | verify-required |
| Bash (§4.4) | ✔ | Linux'un yerel CI/operasyon kabuğu (bash.1 — HEAD 200) | verify-required |
| PowerShell (§3.3) | ⚠️ | pwsh çapraz çalışır (learn.microsoft — HEAD 200); yerel Linux kabuğu değil (S6) | verify-required |
| PHP CLI | ✔ | CLI/tek dosya — Linux (üretim, §6) | verify-required |

---

## §11. SORU–CEVAP

> **S1: systemd/cgroup/ALSA desenleri neden bu dosyada yok?**
> `../isletim-sistemi-turu-kapsam.md` §I-II: Linux-çekirdek desenler komşu dalındadır —
> `linux-kod-ornekleri.md`. Bu dosyada yalnız **taşıma dil havuzu** kalır.
>
> **S2: Linux üretim hedefi mi?**
> Üretim/CI kontratı Linux'dur (`../linux/index.md` §Contract) — bu dosyanın hedefi
> **üretim kontratının kendisi**dir. L-S1 (linux/index.md L-D2) UNKNOWN (ipucu var,
> tür kararı değil); ancak kontrat SSOT'tur.
>
> **S3: Bu dosya `linux-kod-ornekleri.md` ile çakışır mı?**
> Hayır — orada **systemd/cgroup/ALSA gerçek desenleri**, burada **dil havuzu +
> taşınabilirlik** → kesişim 0 (§10.1).
>
> **S4: Raspberry Pi 5 ipucu ne anlama geliyor?**
> linux/index.md L-D2: `home.coremusic.net/AGENTS.md` §1 "Raspberry Pi 5" (Linux/ARM
> sınıfı donanım) — **ipucu, tür kararı değil** (UNKNOWN). §3.2'de arch `aarch64`
> olarak örneklendi (ipucu-uyumlu, iddia değil).
>
> **S5: Derleme/çalışma kanıtı var mı?**
> Hayır — bu turda derlenmedi/çalıştırılmadı (§0) → `verify-required`.
>
> **S6: PowerShell neden "yerel" değil?**
> Linux'ta yerel CI/operasyon kabuğu **bash**tir (§4.4); pwsh (PowerShell 7+) Linux'ta resmi
> kurulum yüzeyiyle **çapraz çalışır** (learn.microsoft.com — HEAD 200, §10.1a). `$IsLinux`
> yalnız pwsh'de tanımlıdır (about_automatic_variables — HEAD 200; Windows PowerShell 5.1'de
> tanımsız) → §3.3 bloğu `⚠️ çapraz` işaretlidir; N/A değil, çünkü üretim CI yüzeyinde pwsh
> Linux üzerinde çalışır ve bu dosyanın konusu "portable → Linux (üretim)" sınıfıdır.
>
> **S7: GRUP 2.6/2.7 neden dosyanın sonunda (§8–§13 arasına değil)?**
> Mevcut § numaralandırması (§8 tablo · §9 bağlantı · §10 doğrulama · §11 S–C · §12 RED TEAM ·
> §13 otorite) **korunmuştur** — yeniden numaralandırma iç bağımlılıkları kırardı (kural: son
> numaradan devam). GRUP numarası ile § numarası bağımsızdır: GRUP 2.6 → §14, GRUP 2.7 → §15.

---

## §12. RED TEAM

> **R1: "Node/PHP/Go/Rust Linux'ta çalışır" — kanıt?** Linux- sınıf portable çekirdek
> (aynı kod); sınıf-içi taşınabilirlik iddia edilir. Linux-specific derleme kanıtı YOK
> (§0). `⚠️` işaretli (§8).
>
> **R2: "systemd/cgroup/ALSA imzaları çıkarıldı" — neden?** Komşu dal (`linux-kod-ornekleri.md`)
> tekrar yasak (§I-II); repo'da Linux-specific `.c`/`.cpp` YOK (§0) → imzalar uydurulmadı.
>
> **R3: "Linux üretim hedefi mi?"** Evet — üretim/CI kontratı Linux'dur (`../linux/index.md`
> §Contract). L-S1 (tür kararı) UNKNOWN; ancak kontrat SSOT'tur → bu dosyanın hedefi
> üretim kontratıdır.
>
> **R4: "URL ayrışımı korundu mu?"** Evet — bu dosya man7/docs.kernel kök yüzeyi kullanır;
> `linux-kod-ornekleri.md` systemd/cgroup/ALSA desenleri → kesişim 0 (§10.1).
>
> **R5: "Derleme/çalışma kanıtı var mı?**" Hayır — bu turda derlenmedi/çalıştırılmadı
> (§0) → `verify-required`.
>
> **R6: "8 yeni fence hangi kapıdan geçti?"** Tüm fence ilk satırları HTTP **HEAD 200** ile
> doğrulandı (2026-10-14, §10.1a); 404 olan `cppreference /w/cpp/program/getenv` elendi,
> alternatif `w/c/program/getenv` (200) kullanıldı. Zig imzaları raw std kaynakından okundu
> (tahmin yok); `$IsLinux`, ProcessInfo, ContinuousClock dokümantasyonu HEAD 200. Gate yöntemi
> sapması (`shell` reddedildi → `fetch` HEAD) §10.1a'da kayıtlıdır. Derleme kanıtı YOK →
> bloklar `[ORİJİNAL ÖRNEK · derlenmedi]`, dosya `verify-required` (§0).

---

## §13. OTORİTE & KAYNAK

| Bilgi | Kaynak | Durum |
|-------|--------|-------|
| Üretim/CI kontratı | `../linux/index.md` §Contract | SSOT (bilgi) |
| Linux model (çekirdek API) | `linux/index.md` §Bağlam | SSOT (bilgi) |
| systemd/cgroup/ALSA desenleri | `linux-kod-ornekleri.md` | Komşu dal (bu dosyada YOK) |
| Linux üretim hedefi (tür kararı) | `linux/index.md` L-D2 | UNKNOWN (L-S1) — kontrat SSOT |
| Raspberry Pi 5 ipucu | `linux/index.md` L-D2 · `home.coremusic.net/AGENTS.md` §1 | İpucu (UNKNOWN) |
| pthreads(7) / cgroups(7) | `man7.org/.../pthreads.7.html` · `.../cgroups.7.html` (200) | Bilgi |
| Taşınabilir dil çekirdeği | `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8 | SSOT (bilgi) |
| 13-dil blok arayüz referansları (200 · 2026-10-14) | man7 (isatty.3/getenv.3/sysconf.3/bash.1) · cppreference (steady_clock, c/getenv) · raw zig std (posix/Thread/process/linux) · apple.com (continuousclock, processinfo) · docs.swift.org kökü · Oracle (System, Runtime) · kotlinlang JVM stdlib · learn.microsoft ($IsLinux, pwsh-linux, RuntimeInformation) | §10.1a — HTTP HEAD 200 |

**Öneri (2026-10-13):** `docs.kernel.org` ve `man7.org` (syscalls.2, pthreads.7, cgroups.7)
HEAD 200 ölçüldü. systemd/cgroup/ALSA imzaları bu dosyada **çıkarıldı** (komşu dal —
`linux-kod-ornekleri.md`); yalnız **taşınabilir dil çekirdeği** + Linux ortam-keşfi
verildi. Üretim/CI kontratı Linux'dur (`../linux/index.md` §Contract) — bu dosyanın
hedefi üretim kontratının kendisidir. `alsa-project.org` (500) ve freedesktop systemd
(418) erişim-hatası → bu dosyada kullanılmadı. Derleme/çalışma kanıtı yok →
`verify-required`. URL ayrışımı korundu (§10.1).

**Öneri (2026-10-14 · 13-dil genişletmesi):** GRUP 2.1/2.2/2.3 dil listeleri güncellendi;
GRUP 2.6 (JVM — §14) ve GRUP 2.7 (Swift — §15) sona eklendi (§ sırası korundu). PowerShell
`⚠️ çapraz` (§3.3 · S6) — yerel Linux kabuğu bash (§4.4). Fence 7 → 15; dil yüzeyi 6 → 13
(matris §8 · §10.2 güncellendi). Tüm fence URL'leri HTTP HEAD 200 (§10.1a); derleme kanıtı
yok → `verify-required` korunur.

---

## §14. GRUP 2.6 — JVM (LİNUX HEDEFİ)

> **Numaralandırma notu:** GRUP 2.6 bu dosyanın **sonuna** eklendi — §8–§13 (tablolar, doğrulama,
> RED TEAM, otorite) yeniden numaralandırılmadı (S7). JVM, Linux üretim hedefinde
> **çalışma-zamanı** platform tespiti verir (derleme-zamanı dallanma yok).

### §14.1 Java → Linux (JVM — çalışma-zamanı tespit + zorunlu env)

> **Referans:** Çekirdek platform tespiti `../isletim-sistemi-turu-kod-seti.md` §A.10 (Java —
> `os.name`) ile AYNI taşınabilir desen (ayrı blok YOK — R4). Fark: Linux üretim yüzeyinde
> zorunlu env + işlemci sayısı. JDK 21 API: `java.lang.System` (fence URL) ·
> `java.lang.Runtime` (HEAD 200, §10.1a).

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi · Java 21 · Linux (üretim) hedefi]
// Çekirdek platform tespiti: isletim-sistemi-turu-kod-seti.md §A.10 ile AYNI (os.name).
import java.util.Locale;

public final class LinuxProbe {
    private LinuxProbe() {}

    public static String osName() {
        return System.getProperty("os.name", "").toLowerCase(Locale.ROOT);
    }

    public static String requireEnv(String name) {
        String value = System.getenv(name);
        if (value == null || value.isBlank()) {
            throw new IllegalStateException(name + " ortam değişkeni tanımsız — sessiz fallback yok");
        }
        return value;
    }

    public static void main(String[] args) {
        if (!osName().contains("linux")) {
            throw new IllegalStateException("Linux sınıfı dışında: " + osName());
        }
        String path = requireEnv("PATH");
        int cpus = Runtime.getRuntime().availableProcessors();   // JVM'in gördüğü işlemci sayısı
        System.out.println("linux=true cpus=" + cpus + " pathLen=" + path.length() + " args=" + args.length);
    }
}
```

### §14.2 Kotlin → Linux (aynı JVM — Kotlin idiomu)

> **Referans:** Çekirdek `../isletim-sistemi-turu-kod-seti.md` §A.10 (Kotlin çekirdeği) ile AYNI;
> JVM arayüzü `java.lang.System`/`java.lang.Runtime` (§14.1 Java URL'si). Fark: Kotlin idiomu —
> `error()` ile açık hata (throw), invariant lowercase.

```kotlin
// https://kotlinlang.org/api/latest/jvm/stdlib/
// [ORİJİNAL ÖRNEK · derlenmedi · Kotlin (JVM) · Linux (üretim) hedefi]
// JDK arayüzü: java.lang.System (Java bloğu URL'si §14.1). Çekirdek: §A.10 ile AYNI.

fun osName(): String =
    System.getProperty("os.name", "").lowercase()   // invariant lowercase — locale bağımsız

fun requireEnv(name: String): String {
    val value = System.getenv(name)
    if (value.isNullOrBlank()) {
        error("$name ortam değişkeni tanımsız — sessiz fallback yok")   // açık hata (throw)
    }
    return value
}

fun main() {
    if ("linux" !in osName()) error("Linux sınıfı dışında: ${osName()}")
    val path = requireEnv("PATH")
    val cpus = Runtime.getRuntime().availableProcessors()
    println("linux=true cpus=$cpus pathLen=${path.length}")
}
```

---

## §15. GRUP 2.7 — SWIFT (LİNUX HEDEFİ)

> **Numaralandırma notu:** §14 gibi sona eklendi (S7). Swift Linux'ta toolchain ile gelen
> Foundation yüzeyinde çalışır [KAVRAM] (`swift-corelibs-foundation`); platform dalı derleme
> zamanında `#if os(Linux)` ile kapanır (çekirdek §A.9 ile aynı).

### §15.1 Swift → Linux (ProcessInfo env + ContinuousClock)

> **Referans:** Çekirdek platform tespiti `../isletim-sistemi-turu-kod-seti.md` §A.9 (`#if os(Linux)`
> koşullu derlemesi) ile AYNI taşınabilir desen (ayrı blok YOK — R4). Fark: Linux üretim
> yüzeyinde zorunlu env
> (`ProcessInfo.environment`) + taşınabilir zaman ölçümü (`ContinuousClock` — saat uykuda da
> ilerler; dokümantasyon doğrulaması §10.1a). `#if os(Linux)` bölüm URL'i çözülemedi → swift-book
> **kökü** kullanıldı: `https://docs.swift.org/swift-book/documentation/the-swift-programming-language/`
> (HEAD 200).

```swift
// https://developer.apple.com/documentation/swift/continuousclock
// [ORİJİNAL ÖRNEK · derlenmedi · Swift 5.7+ (ContinuousClock) · Linux (üretim) hedefi]
// ProcessInfo arayüzü: https://developer.apple.com/documentation/foundation/processinfo (HEAD 200, §10.1a)
import Foundation

enum ProbeError: Error {
    case missingEnv(String)
    case nonLinuxClass(String)
}

func requireEnv(_ name: String) throws -> String {
    guard let value = ProcessInfo.processInfo.environment[name], !value.isEmpty else {
        throw ProbeError.missingEnv(name)   // açık hata — sessiz fallback yok
    }
    return value
}

func probe() throws -> String {
    #if os(Linux)
    let osClass = "linux"
    #else
    let osClass = "non-linux"   // bilinmeyen sınıf açık yazılır (çekirdek: §A.9 ile aynı)
    #endif
    guard osClass == "linux" else { throw ProbeError.nonLinuxClass(osClass) }
    let path = try requireEnv("PATH")
    let clock = ContinuousClock()
    let start = clock.now            // Instant — ContinuousClock sözleşmesi: uykuda da ilerler
    let elapsed = clock.now - start  // Duration (components: seconds/attoseconds — §10.1a)
    return "\(osClass) pathLen=\(path.count) elapsed=\(elapsed)"
}
```
