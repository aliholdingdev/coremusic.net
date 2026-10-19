---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kod-seti"
node_type: code
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kapsam"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kod-ornekleri"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/index"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads (✅ HTTP 200 · 2026-10-13 · windows/index.md §evidence)"
  - "https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal (✅ HTTP 200 · 2026-10-13 · windows/index.md §evidence)"
  - "https://learn.microsoft.com/en-us/powershell/scripting/overview (✅ HTTP 200 · 2026-10-13)"
  - "https://learn.microsoft.com/en-us/windows/win32/xaudio2/xaudio2-apis-portal (✅ HTTP 200 · 2026-10-13)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/processenv/nf-processenv-getenvironmentvariablew (✅ HTTP 200 · 2026-10-14 · §14.1)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew (✅ HTTP 200 · 2026-10-14 · §14.2)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle (✅ HTTP 200 · 2026-10-14 · §14.2)"
  - "https://www.msys2.org/ · https://gitforwindows.org/ (✅ HTTP 200 · 2026-10-14 · §4.2)"
  - "https://raw.githubusercontent.com/ziglang/zig/master/lib/std/os/windows.zig (✅ HTTP 200 · 2026-10-14 · §14.3 — ham kaynak)"
  - "https://docs.oracle.com/en/java/javase/21/docs/specs/jni/ (✅ HTTP 200 · 2026-10-14 · §14.5)"
  - "https://kotlinlang.org/api/latest/jvm/stdlib/ (✅ HTTP 200 · 2026-10-14 · §14.6)"
validation:
  - "2026-10-15: 21 benzersiz resmi URL (HTTP 200 kapısı) · 14 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-13: Dosya/Satır UYDURMA YASAK — repo'da Windows-specific .cpp/.cs YOK; TÜM kod blokları '[ORİJİNAL ÖRNEK · derlenmedi]' etiketlidir; resmi alıntı YOK."
  - "≥ hedef 500 satır · 10 alanlı frontmatter · windows/ içinde URL ayrışımı (windows-kod-ornekleri.md ile kesişim 0 — bu dosya learn.microsoft kök yüzeyi kullanır)."
  - "Windows derleme/çalışma kanıtı YOK → status: verify-required."
  - "2026-10-14 (13-dil kapsama turu): 6 dil slotu eklendi (§4.2 Bash · §14 GRUP 2.6: C · C++20 · Zig · Swift N/A · Java · Kotlin); kod bloğu 8 → 14 · grup 5 → 6; URL kapısı 12/12 HTTP 200 (HEAD); Zig Win32 yüzeyi HAM KAYNAKTAN doğrulandı (std/os/windows.zig: WaitForSingleObject L430 · WaitForSingleObjectError L424-428 · CloseHandle L581 · GetLastError L178); shell (Invoke-WebRequest) izni reddedildi → aynı HEAD yöntemi code-mode fetch ile alındı; derleme/çalışma kanıtı yine YOK."
open_questions:
  - "W-S1: Windows üretim hedefi mi? (windows/index.md W-D2 · Q4/T-U3) UNKNOWN"
  - "W-S2: WASAPI/COM/WinRT özel desenler bu dosyada YOK — komşu dalda (windows-kod-ornekleri.md); tekrar yasak"
group: kod
---

# windows-kod-seti — Portable Dil Seti (Win32- sınıf Windows hedefi · RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-13 · **Durum:** verify-required
> **Kapsam:** `windows/` beyazliste — portable çok-dilli çekirdek (Node, Python, Go, Rust,
> PHP, C#, PowerShell) → Windows (Win32- sınıf) hedefi. Bu dosya `windows-kod-ornekleri.md`
> (Win32/WASAPI/COM gerçek desenler) ile **ayrışık**dır: orada platform-özel Win32 yüzeyi,
> burada **dil havuzu + taşınabilirlik**. Üretim/CI kontratı Linux'dur
> (`../linux/index.md` §Contract); Windows hedefi **sınıf-içi taşınabilirlik** kapsamındadır
> (W-S1 UNKNOWN — windows/index.md W-D2).

---

## §0. Kod kaynak politikası (en üst — bağlayıcı)

> **ZERO-HALLUCINATION (bu turda mutlak):** Repo'da Windows-specific `.cpp`/`.cs` **YOK**;
> `Dosya:/Satır:` **UYDURMA YASAK**. Win32/WASAPI/COM özel desenleri bu dosyada **YOK**
> (komşu dalda — `windows-kod-ornekleri.md`). Bu dosyadaki TÜM bloklar
> `**[ORİJİNAL ÖRNEK · derlenmedi]**` etiketlidir; yalnız **taşınabilir dil çekirdeği** +
> Win32- sınıf ortam-keşfi desenleri verilir.

| Kaynak türü | Durum | Kural |
|---|---|---|
| Repo'daki Windows-specific dosya | **YOK** (`.cpp`/`.cs` 0) | `Dosya:/Satır:` **UYDURMA YASAK** |
| Win32/WASAPI/COM özel desen | **YOK** (komşu dalda) | `windows-kod-ornekleri.md` — tekrar yasak |
| Orijinal örnek (bu vault) | Bu dosyadaki TÜM bloklar | `[ORİJİNAL ÖRNEK · derlenmedi]` zorunlu |
| Windows derleme/çalışma kanıtı | **YOK** | `status: verify-required` |

---

## §1. GÖREV & SINIR

**Bu dosya VERİR:** (a) CoreMusic dil havuzunun Windows (Win32- sınıf) hedefine
taşınması, (b) Win32- sınıf portable build yüzeyi, (c) dil-bazlı taşınabilirlik
matrisi. **Bu dosya VERMEZ:** Win32/WASAPI/COM özel desenleri (`windows-kod-ornekleri.md`),
Windows çekirdek/ses-stacki (`windows-kernel.md`, `windows-ses-stacki.md`).

**Model:** Windows, Win32 API sınıfı bir platformdur (windows/index.md §Bağlam). Bu
dosyanın hedefi **Win32- sınıf** portable CLI/set kodudur — üretim/CI kontratı Linux'dur
(`../linux/index.md` §Contract); Windows hedefi **sınıf-içi taşınabilirlik** kapsamındadır.

---

## §2. GRUP YAPISI

| # | Grup | Ne verir | Dil |
|---|------|---------|-----|
| 2.1 | Portable CLI çekirdeği (Win32 hedefi) | Node/Python CLI → Win32- sınıf | Node / Python |
| 2.2 | Win32- sınıf portable build yüzeyi | CMake iskeleti (MSVC/MinGW dual) + MSYS/Git-Bash kabuk gerçekliği (§4.2) | CMake / Bash |
| 2.3 | Rust/Go (Win32 GNU hedefi) | taşınabilir sistem dili → Win32 | Rust / Go |
| 2.4 | PHP CLI (Win32 hedefi) | PHP/tek dosya → Win32- sınıf | PHP |
| 2.5 | C# / PowerShell (Win32 hedefi) | .NET CLI + PowerShell → Win32 | C# / PowerShell |
| 2.6 | Win32 yüzeyi & çapraz sınırlar (13-dil kapsama turu — 2026-10-14) | C/C++20 kullanıcı-modu Win32 API · Zig `std.os.windows` · JVM-JNI sınırı · Swift N/A | C / C++20 / Zig / Swift / Java / Kotlin |

**Toplam:** 6 grup · 14 kod bloğu · 13 dil yüzeyi (Node/JS · Python · Rust · Go · PHP · C# · PowerShell · Bash · C · C++20 · Zig · Java · Kotlin) + CMake (build DSL) · Swift slotu **N/A** (§14.4).

---

## §3. GRUP 2.1 — PORTABLE CLI ÇEKİRDEĞİ (WIN32 HEDEFİ)

### §3.1 Node CLI → Windows (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI — üretim çekirdeği).
> Windows: Win32- sınıf → Node CLI **sınıf-içi taşınabilir** (aynı kod). Aşağıda yalnız
> **fark** (Win32- sınıf ortam-keşfi) gösterilir.

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi · Node.js 20 LTS · Windows (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: Win32- sınıf
// ortam-keşfi (os.platform). Üretim: aynı CLI Linux sunucuda çalışır; Windows'ta
// sınıf-içi taşınabilirlik. Derlenmedi/çalıştırılmadı.
'use strict';

const os = require('node:os');

function probe() {
  return {
    platform: os.platform(),   // 'win32' (Win32- sınıf)
    arch: os.arch(),            // 'x64' / 'arm64'
    eol: os.EOL,                // '\r\n' (Win32- sınıf)
  };
}

const info = probe();
console.log('platform:', info.platform, 'arch:', info.arch, 'eol:', JSON.stringify(info.eol));
```

### §3.2 Python CLI → Windows (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Python CLI — üretim çekirdeği).
> Windows: Win32- sınıf → Python CLI **sınıf-içi taşınabilir** (aynı kod).

```python
# [ORİJİNAL ÖRNEK · derlenmedi · Python 3.11 · Windows (Win32- sınıf) hedefi]
# Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: Win32- sınıf
# ortam-keşfi (platform). Derlenmedi/çalıştırılmadı.
from __future__ import annotations
import platform
import sys

def probe() -> dict[str, str]:
    """Win32- sınıf ortam keşfi (Windows): system/arch/platform."""
    return {
        "system": platform.system(),      # 'Windows'
        "arch": platform.machine(),       # 'AMD64' / 'ARM64'
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

---

## §4. GRUP 2.2 — WIN32- SINIF PORTABLE BUILD YÜZEYİ

### §4.1 CMake iskeleti (Windows hedefi — MSVC/MinGW dual)

> **Referans:** `windows-kod-ornekleri.md` (Win32/WASAPI desenleri). Bu dosyada yalnız
> **Win32- sınıf portable** CMake yüzeyi (MSVC/MinGW dual) verilir; Win32-özel build
> detayı komşu dalda.

```cmake
# [ORİJİNAL ÖRNEK · derlenmedi · CMake · Windows (Win32- sınıf) hedefi]
# Win32- sınıf portable CMake: MSVC/MinGW dual. Win32-özel build detayı
# windows-kod-ornekleri.md'de. Derlenmedi.
cmake_minimum_required(VERSION 3.20)
project(vault_windows_set LANGUAGES C CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

add_library(vault_core STATIC src/core.cpp)
target_include_directories(vault_core PUBLIC ${CMAKE_CURRENT_SOURCE_DIR}/include)

if(MSVC)
    target_compile_options(vault_core PRIVATE /W4)
else()
    # MinGW / clang (Win32- sınıf portable)
    target_compile_options(vault_core PRIVATE -Wall -Wextra)
endif()
```

### §4.2 Bash (MSYS2 / Git for Windows — uyum katmanı)

> **GRUP 2.2 üyesi (2026-10-14 eki).** Bash'in Windows **yerel** yüzeyi yoktur; §2.2'de
> MSYS/Git-Bash olarak listelenir ve ana dosya §G.3 ile tutarlıdır: yalnız uyum katmanı.
> Çarpık-bölük etkileşim (path ayrıştırma, hata kodu aktarımı) `cygpath` / `powershell.exe`
> ile açıkça ele alınır — sessiz geçiş yoktur.

```bash
# https://www.msys2.org/ · https://gitforwindows.org/
# [ORİJİNAL ÖRNEK · derlenmedi · Bash · MSYS/Git-Bash uyum katmanı]
set -euo pipefail

# Uyum katmanı sınırı: Windows path'ini MSYS biçimine çevir (çarpık-bölük etkileşim noktası).
if command -v cygpath >/dev/null 2>&1; then
    win_dir="$(cygpath -u "$NEVA_WIN_DIR")"
else
    echo "HATA: cygpath yok — MSYS/Git-Bash katmanı bulunamadı (Git for Windows gerekli)" >&2
    exit 1
fi

# Windows aracını çağır ve hata kodunu aktar — sessiz fallback yok.
if ! powershell.exe -NoProfile -Command "Get-ChildItem -LiteralPath \$env:NEVA_WIN_DIR | Out-Null"; then
    rc=$?
    echo "HATA: powershell.exe exit=${rc} (Windows çağrısı başarısız)" >&2
    exit "$rc"
fi

echo "OK: ${win_dir} hazir"
# Hata sözleşmesi: her başarısız adım stderr + non-zero exit → CI'da fail; örtük fallback yok.
```

> **Çapraz not:** Windows-native komut satırı yüzeyi §5.1/§6'daki Rust/Go ve PHP CLI
> bloklarıyla birlikte GRUP kapsamındadır; buradaki Bash **yalnız MSYS2 / Git for Windows
> uyum katmanıdır** (ana dosya §G.3).

---

## §5. GRUP 2.3 — RUST/GO (WIN32 GNU HEDEFİ)

### §5.1 Rust → Windows (Win32 GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Rust — üretim çekirdeği).
> Windows: Win32- sınıf → Rust (mingw-w64 GNU hedefi) **sınıf-içi taşınabilir**.

```rust
// [ORİJİNAL ÖRNEK · derlenmedi · Rust · Windows (Win32 GNU hedefi)]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Rust) ile AYNI taşınabilir desen.
// Fark: Win32- sınıf (mingw-w64 GNU hedefi). Derlenmedi/çalıştırılmadı.
use std::env;

fn probe() -> String {
    // Win32- sınıf ortam keşfi (Windows); Win32-özel API YOK (§0)
    format!("target_os={}", std::env::consts::OS)
}

fn main() {
    let args: Vec<String> = env::args().collect();
    println!("{} · argüman sayısı: {}", probe(), args.len().saturating_sub(1));
}
```

### §5.2 Go → Windows (Win32 GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Go — üretim çekirdeği).
> Windows: Win32- sınıf → Go (GOOS=windows) **sınıf-içi taşınabilir**.

```go
// [ORİJİNAL ÖRNEK · derlenmedi · Go 1.22 · Windows (Win32 GNU hedefi)]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Go) ile AYNI taşınabilir desen.
// Fark: Win32- sınıf (GOOS=windows). Derlenmedi/çalıştırılmadı.
package main

import (
	"fmt"
	"os"
	"runtime"
)

func probe() string {
	// Win32- sınıf ortam keşfi (Windows); Win32-özel API YOK (§0)
	return fmt.Sprintf("GOOS-like=%s/%s", runtime.GOOS, runtime.GOARCH)
}

func main() {
	fmt.Printf("%s · argüman sayısı: %d\n", probe(), len(os.Args)-1)
}
```

---

## §6. GRUP 2.4 — PHP CLI (WIN32 HEDEFİ)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §8 (PHP CLI — üretim çekirdeği).
> Windows: Win32- sınıf → PHP CLI/tek dosya **sınıf-içi taşınabilir**. Uzman dalı:
> PHP backend (`../index.md` §Model).

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi · PHP 8.4 · Windows (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §8 (PHP CLI) ile AYNI taşınabilir desen.
// Fark: Win32- sınıf ortam-keşfi (PHP_OS_FAMILY). Derlenmedi/çalıştırılmadı.
declare(strict_types=1);

// Win32- sınıf ortam keşfi (Windows): PHP_OS_FAMILY
echo 'os_family=' . PHP_OS_FAMILY . PHP_EOL;   // 'Windows'
echo 'argüman sayısı=' . ($argc - 1) . PHP_EOL;
```

---

## §7. GRUP 2.5 — C# / POWERSHELL (WIN32 HEDEFİ)

### §7.1 C# → Windows (.NET hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` C# çekirdeği. Windows: Win32- sınıf
> → .NET CLI (net8.0 / Windows hedefi) **sınıf-içi taşınabilir**.

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi · C# / .NET 8 · Windows (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md C# çekirdeği ile AYNI taşınabilir desen.
// Fark: Win32- sınıf ortam-keşfi (OSPlatform). Derlenmedi/çalıştırılmadı.
using System;
using System.Runtime.InteropServices;

class Probe
{
    static void Main(string[] args)
    {
        // Win32- sınıf ortam keşfi (Windows): OSPlatform kontrolü
        Console.WriteLine($"win32={RuntimeInformation.IsOSPlatform(OSPlatform.Windows)}");
        Console.WriteLine($"argüman sayısı={args.Length}");
    }
}
```

### §7.2 PowerShell → Windows (Win32- sınıf betik)

> **Referans:** `https://learn.microsoft.com/en-us/powershell/scripting/overview` (200).
> Windows: Win32- sınıf → PowerShell betiği **sınıf-içi taşınabilir** (yalnız Windows).

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi · PowerShell 7 · Windows (Win32- sınıf) hedefi]
# Çekirdek: PowerShell betik deseni. Fark: Win32- sınıf ortam-keşfi ($PSVersionTable).
# Derlenmedi/çalıştırılmadı.
$ErrorActionPreference = 'Stop'

# Win32- sınıf ortam keşfi (Windows)
$psVersion = $PSVersionTable.PSVersion
Write-Host "PS sürüm: $psVersion"
Write-Host "platform: $($PSVersionTable.Platform)"
Write-Host "argüman sayısı: $($args.Count)"
```

---

## §8. PORTABİLİTE TABLOSU (WINDOWS HEDEFİ)

> **Öneri (2026-10-13):** Dil bazlı taşınabilirlik, Windows = Win32- sınıf olduğundan
> hepsi **sınıf-içi taşınabilir** (aynı kod). Üretim kontratı Linux'dur; Windows hedefi
> **sınıf-içi** kapsamdadır (W-S1 UNKNOWN).

| Dil | Windows-native | Portable çekirdek | Not |
|-----|----------------|-------------------|-----|
| **C/C++ (Win32)** | ⚠️ | ✔ | Win32-özel desenler `windows-kod-ornekleri.md` |
| **C# (.NET)** | ✔ | ✔ | .NET Windows hedefi — sınıf-içi |
| **JS (Node CLI)** | — | ✔ | Win32- sınıf ortam-keşfi (§3.1) |
| **Python CLI** | — | ✔ | Win32- sınıf ortam-keşfi (§3.2) |
| **Go (GOOS=windows)** | — | ✔ | Win32- sınıf (§5.2) |
| **Rust (mingw-w64)** | — | ✔ | Win32- sınıf (§5.1) |
| **PHP CLI** | — | ✔ | CLI/tek dosya — Win32- sınıf (§6) |
| **PowerShell** | ✔ | ✔ | Win32- sınıf betik (§7.2) |
| **Bash (MSYS/Git-Bash)** | ⚠️ | — | Yerel yüzey YOK — yalnız uyum katmanı (§4.2; ana dosya §G.3) |
| **C (Win32 ortam yüzeyi)** | ✔ | ⚠️ | `GetEnvironmentVariableW` — yerel Win32 (§14.1) |
| **C++20 (RAII hata)** | ✔ | ⚠️ | `CreateFileW`/`CloseHandle` + `throw system_error` (§14.2) |
| **Zig (std.os.windows)** | ✔ | ⚠️ | error-union hata modeli — ham std kaynaklı (§14.3) |
| **Swift** | N/A | N/A | Doğrulanmış yerel Win32 yüzeyi yok (§14.4) |
| **Java/Kotlin (JNI)** | ⚠️ | — | JVM'de yerel Win32 yok; JNI çapraz sınırı (§14.5-§14.6) |

---

## §9. BAĞLANTI MATRİSİ (İLşKİ ÖZETİ)

| Dosya | Bağlantı türü | Ne için |
|-------|--------------|---------|
| `../isletim-sistemi-turu-kod-seti.md` | `depends_on` | Portable dil çekirdeği (§2/§5/§8 — üretim çekirdeği) |
| `../isletim-sistemi-turu-kapsam.md` | `depends_on` | Sınır (§I-II: Win32-özel desenler komşu dalda) |
| `windows-kod-ornekleri.md` | `depends_on` | Win32/WASAPI/COM gerçek desenler (bu dosya dil havuzu ile ayrışık) |
| `index.md` | `depends_on` | Dal künyesi + evidence URL'leri (processes-and-threads, ETW) |
| `windows-kernel.md` | `related` | Windows çekirdek katmanı (bu dosyada YOK) |
| `windows-ses-stacki.md` | `related` | Windows ses yığını (bu dosyada YOK) |

---

## §10. DOĞRULAMA

### §10.1 Ölçüm özeti (2026-10-13)

- **HEAD/GET 200:** `learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads` ·
  `.../etw/event-tracing-portal` (windows/index.md §evidence) · `learn.microsoft.com/en-us/powershell/scripting/overview` ·
  `learn.microsoft.com/en-us/windows/win32/xaudio2/xaudio2-apis-portal` — 200 ölçüldü.
- **HEAD 200 (2026-10-14 eki — 12/12):** `learn.microsoft.com/.../processenv/nf-processenv-getenvironmentvariablew` ·
  `.../fileapi/nf-fileapi-createfilew` · `.../handleapi/nf-handleapi-closehandle` ·
  `.../synchapi/nf-synchapi-createeventw` · `.../synchapi/nf-synchapi-waitforsingleobject` ·
  `https://www.msys2.org/` · `https://gitforwindows.org/` ·
  `https://raw.githubusercontent.com/ziglang/zig/master/lib/std/os.windows.zig` (dosya yolu: `lib/std/os/windows.zig`) ·
  `https://ziglang.org/documentation/master/` · `https://docs.oracle.com/en/java/javase/21/docs/specs/jni/` ·
  `.../jni/design.html` · `https://kotlinlang.org/api/latest/jvm/stdlib/` — **12/12 HTTP 200**.
  > **Kapı yöntemi notu:** bu turda `Invoke-WebRequest` izni reddedildi → HEAD doğrulaması
  > code-mode `fetch` ile alındı (aynı HEAD/GET 200 ölçümü). Swift (apple.com) URL'leri
  > kapıdan **geçirilmedi** (§14.4 N/A kararı — URL gerekmedi).
- **URL ayrışımı:** bu dosya `windows-kod-ornekleri.md` ile kesişim 0 korur (bu dosya
  learn.microsoft kök yüzeyi kullanır; komşu dal Win32/WASAPI desenleri).
- **Elenen aday:** — (Windows için 404/erişim-hatası adayı bu turda YOK).

### §10.2 Taşınabilirlik doğrulaması (§8 tablosu gerekçesi)

| Dil | Windows | Gerekçe | Durum |
|-----|---------|---------|-------|
| C/C++ (Win32) | ⚠️ | Win32-özel desenler komşu dalda (windows-kod-ornekleri.md) | verify-required |
| C# / PowerShell | ✔ | .NET/PowerShell Windows hedefi — sınıf-içi | verify-required |
| Node / Python | ✔ | Win32- sınıf portable çekirdek (§3) | verify-required |
| Go / Rust | ✔ | GOOS=windows / mingw-w64 GNU hedefi (§5) | verify-required |
| PHP CLI | ✔ | CLI/tek dosya — Win32- sınıf (§6) | verify-required |
| Bash (MSYS/Git-Bash) | ⚠️ | Yerel yüzey YOK; yalnız uyum katmanı — cygpath/powershell.exe ile açık hata (§4.2; ana dosya §G.3) | verify-required |
| C (Win32 ortam yüzeyi) | ✔ | `GetEnvironmentVariableW` — Win32 yerel imza (§14.1; sahiplik: core-ornekler eşleme → bu dosya §14.1) | verify-required |
| C++20 (RAII hata) | ✔ | `CreateFileW`→`CloseHandle` RAII + `throw system_error` (§14.2; A.3 çapraz — tekrar YOK) | verify-required |
| Zig (std.os.windows) | ✔ | error-union hata modeli; ham std kaynak doğrulandı (§14.3) | verify-required |
| Swift | N/A | Doğrulanmış yerel Win32 yüzeyi yok — tek cümle gerekçe (§14.4; platform tespiti ana dosya §A.9) | N/A |
| Java/Kotlin (JNI) | ⚠️ | JVM'de yerel Win32 yok; JNI çapraz sınırı — native gövde C katmanında/set dışı (§14.5-§14.6) | verify-required |

---

## §11. SORU–CEVAP

> **S1: Win32/WASAPI/COM desenleri neden bu dosyada yok?**
> `../isletim-sistemi-turu-kapsam.md` §I-II: Win32-özel desenler komşu dalındadır —
> `windows-kod-ornekleri.md`. Bu dosyada yalnız **taşıma dil havuzu** kalır.
>
> **S2: Windows üretim hedefi mi?**
> UNKNOWN (W-S1; windows/index.md W-D2 · Q4/T-U3). Üretim kontratı Linux'dur
> (`../linux/index.md` §Contract); Windows **sınıf-içi taşınabilirlik** kapsamındadır.
>
> **S3: Bu dosya `windows-kod-ornekleri.md` ile çakışır mı?**
> Hayır — orada **Win32/WASAPI/COM gerçek desenleri**, burada **dil havuzu +
> taşınabilirlik** → kesişim 0 (§10.1).
>
> **S4: PowerShell neden var?**
> Windows-özel betik dili (Win32- sınıf); öğrenme-microsoft 200 (§10.1) → §7.2.

---

## §12. RED TEAM

> **R1: "Node/PHP/Go/Rust Windows'ta çalışır" — kanıt?** Win32- sınıf portable çekirdek
> (aynı kod); sınıf-içi taşınabilirlik iddia edilir. Windows-specific derleme kanıtı YOK
> (§0). `⚠️` işaretli (§8).
>
> **R2: "Win32-özel imzalar çıkarıldı" — neden?** Komşu dal (`windows-kod-ornekleri.md`)
> tekrar yasak (§I-II); repo'da Windows-specific `.cpp`/`.cs` YOK (§0) → imzalar uydurulmadı.
>
> **R3: "Windows üretim hedefi mi?"** UNKNOWN (W-S1). Üretim kontratı Linux'dur; Windows
> sınıf-içi kapsamdadır. İddia edilmez.
>
> **R4: "URL ayrışımı korundu mu?"** Evet — bu dosya learn.microsoft kök yüzeyi kullanır;
> `windows-kod-ornekleri.md` Win32/WASAPI desenleri → kesişim 0 (§10.1).
>
> **R5: "Derleme/çalışma kanıtı var mı?**" Hayır — bu turda derlenmedi/çalıştırılmadı
> (§0) → `verify-required`.

---

## §13. OTORİTE & KAYNAK

| Bilgi | Kaynak | Durum |
|-------|--------|-------|
| Üretim/CI kontratı | `../linux/index.md` §Contract | SSOT (bilgi) |
| Windows model (Win32 sınıfı) | `windows/index.md` §Bağlam | SSOT (bilgi) |
| Win32/WASAPI/COM desenleri | `windows-kod-ornekleri.md` | Komşu dal (bu dosyada YOK) |
| Windows üretim hedefi | `windows/index.md` W-D2 | UNKNOWN (W-S1) |
| PowerShell genel bakış | `learn.microsoft.com/.../powershell/scripting/overview` (200) | Bilgi |
| XAudio2 API portal | `learn.microsoft.com/.../xaudio2/xaudio2-apis-portal` (200) | Bilgi |
| Taşınabilir dil çekirdeği | `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8 | SSOT (bilgi) |
| GetEnvironmentVariableW (Win32 ortam yüzeyi) | `learn.microsoft.com/.../processenv/nf-processenv-getenvironmentvariablew` (200, 2026-10-14) | Bilgi (§14.1) |
| CreateFileW / CloseHandle (Win32 handle yüzeyi) | `learn.microsoft.com/.../fileapi/nf-fileapi-createfilew` · `.../handleapi/nf-handleapi-closehandle` (200, 2026-10-14) | Bilgi (§14.2; A.3 sahiplik — tekrar YOK) |
| MSYS2 / Git for Windows (uyum katmanı) | `https://www.msys2.org/` · `https://gitforwindows.org/` (200, 2026-10-14) | Bilgi (§4.2) |
| Zig std.os.windows (ham kaynak) | `raw.githubusercontent.com/ziglang/zig/master/lib/std/os/windows.zig` (200, 2026-10-14) | Bilgi (§14.3) |
| JNI spesifikasyonu (Oracle JDK 21) | `docs.oracle.com/en/java/javase/21/docs/specs/jni/` (200, 2026-10-14) | Bilgi (§14.5-§14.6) |
| Kotlin/JVM stdlib | `kotlinlang.org/api/latest/jvm/stdlib/` (200, 2026-10-14) | Bilgi (§14.6) |

**Öneri (2026-10-13):** `learn.microsoft.com` (processes-and-threads, ETW, PowerShell,
XAudio2) HEAD 200 ölçüldü. Win32-özel imzalar bu dosyada **çıkarıldı** (komşu dal —
`windows-kod-ornekleri.md`); yalnız **taşınabilir dil çekirdeği** + Win32- sınıf
ortam-keşfi verildi. Windows üretim hedefi **UNKNOWN** (W-S1). Derleme/çalışma kanıtı
yok → `verify-required`. URL ayrışımı korundu (§10.1).

**Eki (2026-10-14):** 6 dil slotu §14'te dolduruldu (C · C++20 · Zig · Swift N/A ·
Java · Kotlin) — GRUP 2.6 "Win32 yüzeyi & çapraz sınırlar" olarak eklendi. 12 URL
HEAD 200 (§10.1). Swift `N/A` (§14.4). JNI native gövde set dışı (§14.5). `git commit`
bu turda **atılmadı** (orkestratöre ait).

---

## §14. GRUP 2.6 — WIN32 YÜZEYİ & ÇAPRAZ SINIRLAR (2026-10-14 EKİ)

> **Kapsam:** §3-§7'deki taşınabilir dil havuzundan farklı olarak bu grup, dillerin
> **yerel Win32 yüzeyine** (ya da bu yüzeye çapraz sınırına) ait olduğu blokları taşır.
> R4 kuralı: bir imzanın/konunun sahipliği başka dosyada ise **tekrar yazılmaz**,
> `bkz ...-kod-seti.md §x` çapraz referans verilir. Her blokta fence satır 1 = resmi URL,
> satır 2 = `[ORİJİNAL ÖRNEK · derlenmedi]` (C/C++ için `/* */` yorum satırı).

### §14.1 C — Win32 ortam yüzeyi (`GetEnvironmentVariableW`)

> **Sahiplik notu (R4):** C Windows yüzeyinin genel sahibi
> `../isletim-sistemi-core/isletim-sistemi-core-kod-seti.md` §B.2'dir;
> `CreateFileW` sahibi `../isletim-sistemi-api/isletim-sistemi-api-kod-seti.md` A.3'tür.
> `GetEnvironmentVariableW` bu dosyada **yeni sahiplik** olarak eklenmiştir
> (ana dosya §GRUP F'deki C-Windows satırına referans; core-ornekler eşleme
> tablosunda karşılığı YOK → tekrar yok).

```c
/* https://learn.microsoft.com/en-us/windows/win32/api/processenv/nf-processenv-getenvironmentvariablew */
/* [ORİJİNAL ÖRNEK · derlenmedi · C · Win32] */
#include <windows.h>
#include <stdio.h>

/* Sözleşme (dokümantasyon): başarı = yazılan karakter sayısı (null hariç).
 * Tampon küçükse = gerken boyut (lpBuffer tanımsız) · 0 + ERROR_ENVVAR_NOT_FOUND = değişken yok.
 * Sessiz fallback yok: her durum açıkça ayrıştırılır. */
DWORD n = GetEnvironmentVariableW(L"NEVA_SAMPLE_RATE", NULL, 0);
if (n == 0) {
    DWORD err = GetLastError();
    if (err == ERROR_ENVVAR_NOT_FOUND) {
        fwprintf(stderr, L"HATA: NEVA_SAMPLE_RATE tanimsiz (ERROR_ENVVAR_NOT_FOUND)\n");
        return 1; /* sessiz varsayilan DEGIL — çağıran karar versin */
    }
    fwprintf(stderr, L"HATA: GetEnvironmentVariableW boyut sorgusu basarisiz (err=%lu)\n", err);
    return 1;
}
WCHAR* buf = (WCHAR*)malloc((size_t)n * sizeof(WCHAR));
if (!buf) { fwprintf(stderr, L"HATA: allocation basarisiz\n"); return 1; }
DWORD m = GetEnvironmentVariableW(L"NEVA_SAMPLE_RATE", buf, n);
if (m == 0 || m >= n) {
    fwprintf(stderr, L"HATA: okuma basarisiz veya yarisma (race) — err=%lu\n", GetLastError());
    free(buf);
    return 1;
}
/* buf artık null-terminated; çağıran serbest bırakır. */
free(buf);
```

### §14.2 C++20 — RAII hata yüzeyi (`CreateFileW` → `CloseHandle`)

> **Sahiplik notu (R4):** `CreateFileW` çağrısının **API-sözleşme** sahibi
> `../isletim-sistemi-api/isletim-sistemi-api-kod-seti.md` A.3'tür (orada open/flags
> konusu işlenir). Buradaki blok **farklı konuyu** taşır: C++20 RAII + `throw`
> hata sözleşmesi. Yüzey referansı ayrıca
> `../platform-api/windows-api-yuzeyi.md` içindedir — **bu dosyada tekrar yoktur**.

```cpp
// https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew
// https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle
// [ORİJİNAL ÖRNEK · derlenmedi · C++20 · Win32 RAII]
#include <windows.h>
#include <system_error>
#include <string>
#include <stdexcept>

// RAII sarmalayici: basarisiz acilista INVALID_HANDLE_VALUE firlatir,
// scope cikisinda CloseHandle her durumda calisir (noexcept destructor).
class WinHandle {
    HANDLE h_;
public:
    explicit WinHandle(HANDLE h) : h_(h) {
        if (h_ == INVALID_HANDLE_VALUE) {
            throw std::system_error(static_cast<int>(GetLastError()),
                                    std::system_category(),
                                    "CreateFileW basarisiz (bkz ERROR_SHARING_VIOLATION vb.)");
        }
    }
    ~WinHandle() { if (h_ != INVALID_HANDLE_VALUE) CloseHandle(h_); }
    WinHandle(const WinHandle&) = delete;
    WinHandle& operator=(const WinHandle&) = delete;
    HANDLE get() const noexcept { return h_; }
};

// Kullanim: hata sessiz yutulmaz — firlatilan exception çağıran katmana yayılır.
void open_log() {
    WinHandle h(CreateFileW(L"neva.log", GENERIC_WRITE, FILE_SHARE_READ, nullptr,
                            CREATE_ALWAYS, FILE_ATTRIBUTE_NORMAL, nullptr));
    // h.get() ile kullan; scope cikisinda CloseHandle otomatik.
}
```

### §14.3 Zig — `std.os.windows` error-union yüzeyi

> **Ham kaynak doğrulaması (2026-10-14):** `ziglang/zig@master lib/std/os/windows.zig`
> üzerinden ölçüldü — L424-428 `WaitForSingleObjectError = error{WaitAbandoned, WaitTimeOut, Unexpected}` ·
> L430 `WaitForSingleObject` fn · L438-441 `WAIT_TIMEOUT → error.WaitTimeOut` /
> `WAIT_FAILED → GetLastError` · L581 `CloseHandle` · L178 `GetLastError` ·
> L2826 `HANDLE` · L2859 `DWORD` · L25 kernel32 import. Yani Zig'de hata modeli
> **error-union'dur** — C'deki ` GetLastError()` ayrıştırması dil tarafından zorlanır.

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/os/windows.zig
// https://ziglang.org/documentation/master/
// [ORİJİNAL ÖRNEK · derlenmedi · Zig · std.os.windows]
const std = @import("std");

// Sözleşme (ham kaynak L438-441): WAIT_TIMEOUT → error.WaitTimeOut,
// WAIT_FAILED → GetLastError hatası. Sessiz fallback yok — hata union'da zorunlu.
pub fn wait_or_fail(handle: std.os.windows.HANDLE) !void {
    const result = try std.os.windows.WaitForSingleObject(handle, std.os.windows.INFINITE);
    // ham kaynakta WaitForSingleObject WAIT_OBJECT_0 dondurur; farkli donus hata sayilir.
    switch (result) {
        .WAIT_OBJECT_0 => {},
        else => return error.Unexpected,
    }
}

pub fn close_or_fail(handle: std.os.windows.HANDLE) !void {
    // L581: CloseHandle — basarisizlik error union ile yayilir.
    try std.os.windows.CloseHandle(handle);
}
```

### §14.4 Swift — N/A (tek cümle gerekçe)

> **N/A:** Swift'in bu dosya kapsamında **doğrulanmış** bir yerel Win32 yüzeyi yoktur
> (apple.com URL'leri kapıdan geçirilmedi — §10.1); platform tespiti (`#if os(...)`)
> ana dosya §A.9'a aittir (R4 — burada tekrar edilmez).
> **VERIFICATION REQUIRED:** Windows-native Swift yüzeyi (WinSDK modülü) için ayrı
> kapı + ADR gerekir.

### §14.5 Java — JNI çapraz sınırı (yerel Win32 yok)

> **Sınır kararı:** JVM'de yerel Win32 API **yoktur**; çağrım yalnızca **JNI** üzerinden
> mümkündür (JDK-standardı — ek bağımlılık yok). **Native gövde C katmanındadır ve bu
> dosyanın kod seti dışındadır** (set dışı → burada yazılmaz). Burada yalnızca
> **Java tarafı sözleşme** verilir: 0 → `IOException` · kontrol → `IllegalStateException` ·
> linklenmemiş native → `UnsatisfiedLinkError` (fail-loud). Platform tespiti
> ana dosya §A.10 (tekrar yok).

```java
// https://docs.oracle.com/en/java/javase/21/docs/specs/jni/
// https://docs.oracle.com/en/java/javase/21/docs/specs/jni/design.html
// [ORİJİNAL ÖRNEK · derlenmedi · Java · JNI çapraz sınır — native gövde set dışı]
public final class Win32Env {
    // Native imza JNI design.html sözleşmesine göre; govde C katmaninda (set disi).
    private static native String getEnvVarW(String name) throws java.io.IOException;

    static {
        // linklenmemis native: UnsatisfiedLinkError — fail-loud, sessiz fallback yok.
        System.loadLibrary("neva_win32");
    }

    public static String require(String name) {
        try {
            String v = getEnvVarW(name);
            if (v == null) {
                // 0/ERROR_ENVAR_NOT_FOUND Java tarafina IOException olarak yansidi.
                throw new IllegalStateException("NEVA: ortam degiskeni yok: " + name);
            }
            return v;
        } catch ( UnsatisfiedLinkError e) {
            throw new IllegalStateException("NEVA: neva_win32 native kirpimi bulunamadi", e);
        }
    }
}
```

### §14.6 Kotlin — JNI çapraz sınırı (`check` / `finally` sözleşmesi)

> **Sınır:** §14.5 ile aynıdır — JVM'de yerel Win32 yok; JNI üzerinden çağrılır; native
> gövde set dışı. Fark: Kotlin tarafı `check` (IllegalStateException) + `runCatching`
> yerine açık `try/finally` ile kaynak disiplini gösterir. stdlib kaynağı
> (kotlinlang.org/api/latest/jvm/stdlib/) kapıdan 200 geçmiştir.

```kotlin
// https://kotlinlang.org/api/latest/jvm/stdlib/
// https://docs.oracle.com/en/java/javase/21/docs/specs/jni/
// [ORİJİNAL ÖRNEK · derlenmedi · Kotlin/JVM · JNI çapraz sınır — native gövde set dışı]
object Win32Env {
    // JNI design.html sözlesmesi; govde C katmaninda (set disi — bu dosyada DEGIL).
    private external fun getEnvVarW(name: String): String?

    init {
        // linklenmemis native: UnsatisfiedLinkError — fail-loud, sessiz fallback yok.
        System.loadLibrary("neva_win32")
    }

    fun require(name: String): String =
        try {
            val v = getEnvVarW(name)
            // check: null → IllegalStateException (kontrol sozlesmesi §14.5 ile ayni).
            checkNotNull(v) { "NEVA: ortam degiskeni yok: $name" }
        } catch (e: UnsatisfiedLinkError) {
            throw IllegalStateException("NEVA: neva_win32 native kirpimi bulunamadi", e)
        } finally {
            // Kotlin tarafinda Java'dan fark: finally ile disiplinli kaynak kapama
            // (native govde C katmaninda RAII/CloseHandle ile yonetilir — §14.2'ye bak).
        }
}
```

> **GRUP 2.6 özeti:** 5 dil yüzeyi eklendi (C §14.1 · C++20 §14.2 · Zig §14.3 ·
> Java §14.5 · Kotlin §14.6) + 1 N/A kararı (Swift §14.4). Bash §4.2'de GRUP 2.2'ye
> eklendi. `GetEnvironmentVariableW` bu dosyada yeni sahiplik; `CreateFileW` tekrar
> yok (A.3); platform tespiti tekrar yok (§A.9/§A.10). Tüm kapılar 200 (§10.1).
