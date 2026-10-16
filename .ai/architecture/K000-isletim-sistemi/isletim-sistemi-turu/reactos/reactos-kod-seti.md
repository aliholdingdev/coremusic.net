---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-kod-seti"
node_type: code
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kapsam"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-kod-ornekleri"
evidence:
  - "https://reactos.org/ (✅ HTTP 200 · 2026-10-13)"
  - "https://github.com/reactos/reactos (✅ MIT · 2026-10-13 · index.md §Yöntem resmi kaynak)"
  - "https://jira.reactos.org/ (✅ 2026-10-13 · index.md §Yöntem resmi kaynak)"
  - "https://build.reactos.org/ (⚠️ 2026-10-13 · index.md §Yöntem resmi kaynak — build botu)"
validation:
  - "2026-10-15: 13 benzersiz resmi URL (HTTP 200 kapısı) · 15 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-13: Dosya/Satır UYDURMA YASAK — repo 0 .cpp/.h; TÜM kod blokları '[ORİJİNAL ÖRNEK · derlenmedi]' etiketlidir; resmi ReactOS içeriği bot-duvarı arkasında (index.md §Kritik erişim notu) → alıntı YOK."
  - "URL ayrışımı: bu dosya reactos.org/wiki/* KULLANMAZ (10 wiki URL reactos-kod-ornekleri.md'de) → kesişim 0 korunur; yalnız 4 resmi kök kaynak (index.md §Yöntem)."
  - "ReactOS çalışma/derleme kanıtı YOK → status: verify-required."
  - "2026-10-14: §14–§17 eklendi (C · C++20 · Zig · Swift · Java · Kotlin · Bash · PowerShell — 8 fence); fence URL'leri YAZIM ÖNCESİ HEAD 200 (§10.3); Zig/Java/Kotlin/Swift [TASARIM]; getenv URL 404 → C++20 bloğunda std::getenv çıkarıldı (#ifdef _WIN32); ReactOS çalışma süresi UNKNOWN (R-S2) korundu."
open_questions:
  - "R-S1: ReactOS sürümü (0.4.16+ iddiası bot-duvarı nedeniyle doğrulanamadı) → UNKNOWN (index.md §open_questions)."
  - "R-S2: Node/PHP/Go/Rust CLI araçlarının ReactOS üzerinde çalışma durumu → UNKNOWN (cihaz/ortam kanıtı vault'ta YOK)."
  - "R-S3: ReactOS wiki içeriği (bot-duvarı) → [⚠️ VERIFICATION REQUIRED]."
group: kod
---

# reactos-kod-seti — Portable Dil Seti (Win32- sınıf ReactOS hedefi · RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-13 · **Durum:** verify-required
> **Kapsam:** `reactos/` beyazliste — portable çok-dilli çekirdek (JS/Node, Python, Go, Rust,
> PHP, C# + C · C++20 · Zig · Swift · Java/Kotlin · Bash · PowerShell — §14–§17 eklendi
> 2026-10-14) → ReactOS (NT-typ / Win32- sınıf) hedefi. Bu dosya `reactos-kod-ornekleri.md`
> (build-chain/CMake/RosBE) ile **ayrışık**dır: orada derleme zinciri, burada **dil havuzu +
> taşınabilirlik**. 10 alanlı frontmatter.

---

## §0. Kod kaynak politikası (en üst — bağlayıcı)

> **ZERO-HALLUCINATION (bu turda mutlak):** ReactOS deposu bu vault'ta **0 `.cpp`/`.h`**
> içerir; `reactos.org/wiki/*` içeriği **Anubis v1.19.1 bot-duvarı** arkasındadır
> (index.md §Kritik erişim notu; ayrıntı `reactos-cekirdek-mimarisi.md` §11.5) →
> **resmi alıntı YOK**, `Dosya:…/Satır:…` **UYDURMA YASAK**. Bu dosyadaki TÜM bloklar
> `**[ORİJİNAL ÖRNEK · derlenmedi]**` etiketlidir; ReactOS'a özgü API imzaları
> **çıkarılmıştır** (Win32 genel yüzeyi hariç — o da `⚠️` işaretli, derlenmedi).

| Kaynak türü | Durum | Kural |
|---|---|---|
| Repo'daki C++ dosyası | **YOK** (0 `.cpp`/`.h`) | `Dosya:/Satır:` **UYDURMA YASAK** |
| ReactOS wiki alıntısı | **MÜMKÜN DEĞİL** (bot-duvarı) | Alıntı yapılmadı |
| Orijinal örnek (bu vault) | Bu dosyadaki TÜM bloklar | `[ORİJİNAL ÖRNEK · derlenmedi]` zorunlu |
| ReactOS çalışma/derleme kanıtı | **YOK** | `status: verify-required` |

---

## §1. GÖREV & SINIR

**Bu dosya VERİR:** (a) CoreMusic dil havuzunun ReactOS (Win32- sınıf) hedefine
taşınması, (b) Win32- sınıf portable build yüzeyi, (c) dil-bazlı taşınabilirlik
matrisi. **Bu dosya VERMEZ:** ReactOS-özel API desenleri (çekirdek/sürücü/ses —
`reactos-cekirdek-mimarisi.md`, `reactos-surucu-modeli.md`, `reactos-ses-stacki.md`),
build-chain/CMake detayı (`reactos-kod-ornekleri.md`).

**Model:** ReactOS, NT-typ kernel + Win32 API uyumluluk **hedefi**dir (index.md §Kapsam).
Bu dosyanın hedefi **Win32- sınıf** portable CLI/set kodudur — üretim/CI kontratı
Linux'dur (`../linux/index.md` §Contract); ReactOS hedefi **sınıf-içi taşınabilirlik**
kapsamındadır.

---

## §2. GRUP YAPISI

| # | Grup | Ne verir | Dil |
|---|------|---------|-----|
| 2.1 | Portable CLI çekirdeği (Win32 hedefi) | Node/Python CLI → Win32- sınıf | Node / Python |
| 2.2 | Win32- sınıf portable build yüzeyi | CMake iskeleti (build-chain dosyasına referans) | CMake / Bash |
| 2.3 | Rust/Go (Win32 GNU hedefi) | taşınabilir sistem dili → Win32 | Rust / Go |
| 2.4 | PHP CLI (Win32 hedefi) | PHP/tek dosya → Win32- sınıf | PHP |
| 2.5 | C# (Win32 / .NET hedefi) | .NET CLI → Win32- sınıf | C# |
| 2.6 | C/C++20 (Win32 yerel yüzeyi) | Win32 API doğrudan (NT-typ hedefi) | C / C++20 |
| 2.7 | Zig/Swift (Win32 hedefi — [TASARIM]) | systems/portable dil → Win32- sınıf | Zig / Swift |
| 2.8 | Java/Kotlin (JVM — Win32 hedefi) | JVM CLI → Win32- sınıf | Java / Kotlin |
| 2.9 | Kabuk betikleri (Win32 hedefi) | POSIX karşılaştırma (bkz 2.2) + Windows kabuğu | Bash / PowerShell |
| — | **Toplam (§2.1–§2.9)** | **9 grup · 15 kod bloğu** (§14–§17 eklendi 2026-10-14) | Node · Python · CMake · Bash · Rust · Go · PHP · C# · C · C++20 · Zig · Swift · Java · Kotlin · PowerShell |

---

## §3. GRUP 2.1 — PORTABLE CLI ÇEKİRDEĞİ (WIN32 HEDEFİ)

### §3.1 Node CLI → ReactOS (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI — üretim çekirdeği).
> ReactOS: NT-typ/Win32- sınıf → Node CLI **sınıf-içi taşınabilir**; ancak ReactOS
> üzerinde Node çalışma süresi **UNKNOWN** (R-S2; vault'ta cihaz kanıtı YOK). Kod aynıdır.

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi · Node.js 20 LTS · ReactOS (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: ReactOS (NT-typ)
// üzerinde Node çalışma süresi UNKNOWN (R-S2) → CLI, ortamda Node ister.
// Üretim: aynı CLI Linux sunucuda çalışır; ReactOS'ta sınıf-içi taşınabilirlik.
'use strict';

const { execFile } = require('node:child_process');

// Win32- sınıf: PowerShell yerine cmd.exe fallback (taşınabilir desen)
function probe(cmd, args) {
  return new Promise((resolve, reject) => {
    execFile(cmd, args, (err, stdout, stderr) => {
      if (err) reject(new Error(`${cmd}: ${stderr || err.message}`));
      else resolve(stdout);
    });
  });
}

// CLI
(async () => {
  const [,, input, output] = process.argv;
  if (!input || !output) {
    console.error('kullanım: node reactos-probe.js <girdi> <çıktı>');
    process.exit(1);
  }
  try {
    // Win32- sınıf ortam keşfi (ReactOS/Windows): os platform kontrolü
    const os = require('node:os');
    console.log('platform:', os.platform(), 'arch:', os.arch());
    console.log('hazır:', input, '→', output);
  } catch (e) {
    console.error('probe HATA:', e.message);
    process.exit(1);
  }
})();
```

### §3.2 Python CLI → ReactOS (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Python CLI — üretim çekirdeği).
> ReactOS: Win32- sınıf → Python CLI **sınıf-içi taşınabilir**; ReactOS üzerinde
> çalışma süresi **UNKNOWN** (R-S2).

```python
# [ORİJİNAL ÖRNEK · derlenmedi · Python 3.11 · ReactOS (Win32- sınıf) hedefi]
# Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: ReactOS (NT-typ)
# üzerinde Python çalışma süresi UNKNOWN (R-S2) → CLI, ortamda Python ister.
# Üretim: aynı CLI Linux sunucuda çalışır; ReactOS'ta sınıf-içi taşınabilirlik.
from __future__ import annotations
import argparse
import platform
import sys

def probe() -> str:
    """Win32- sınıf ortam keşfi (ReactOS/Windows): platform/arch döndürür."""
    return f"platform={platform.system()} arch={platform.machine()}"

def main() -> int:
    ap = argparse.ArgumentParser(description="ReactOS (Win32- sınıf) ortam keşfi")
    ap.add_argument("input", help="girdi dosya yolu")
    ap.add_argument("output", help="çıktı dosya yolu")
    args = ap.parse_args()
    print(probe())
    print(f"hazır: {args.input} → {args.output}")
    return 0

if __name__ == "__main__":
    sys.exit(main())
```

---

## §4. GRUP 2.2 — WIN32- SINIF PORTABLE BUILD YÜZEYİ

### §4.1 CMake iskeleti (Win32 hedefi — build-chain dosyasına referans)

> **Referans:** `reactos-kod-ornekleri.md` §2 (CMake iskeleti — build-chain). Bu dosyada
> yalnız **Win32- sınıf portable** CMake yüzeyi (MSVC/MinGW dual) verilir; ReactOS'a özgü
> build adımları `reactos-kod-ornekleri.md` §1'de (bot-duvarı → içerik okunamadı).

```cmake
# [ORİJİNAL ÖRNEK · derlenmedi · CMake · ReactOS (Win32- sınıf) hedefi]
# Win32- sınıf portable CMake: MSVC/MinGW dual. ReactOS build-chain adımları
# reactos-kod-ornekleri.md §1'de (bot-duvarı → içerik okunamadı). Derlenmedi.
cmake_minimum_required(VERSION 3.20)
project(vault_reactos_set LANGUAGES C CXX)

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

---

## §5. GRUP 2.3 — RUST/GO (WIN32 GNU HEDEFİ)

### §5.1 Rust → ReactOS (Win32 GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Rust — üretim çekirdeği).
> ReactOS: Win32- sınıf → Rust (GNU toolchain / mingw-w64 hedefi) **sınıf-içi
> taşınabilir**; ReactOS üzerinde derleme/çalışma **UNKNOWN** (R-S2).

```rust
// [ORİJİNAL ÖRNEK · derlenmedi · Rust · ReactOS (Win32 GNU hedefi)]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Rust) ile AYNI taşınabilir desen.
// Fark: Win32- sınıf (mingw-w64 GNU hedefi); ReactOS üzerinde derleme UNKNOWN (R-S2).
use std::env;
use std::process;

fn probe() -> Result<String, String> {
    // Win32- sınıf ortam keşfi (ReactOS/Windows); ReactOS-specific API YOK (§0)
    Ok(format!("target_os={}", std::env::consts::OS))
}

fn main() {
    let args: Vec<String> = env::args().collect();
    if args.len() < 3 {
        eprintln!("kullanım: cargo run -- <girdi> <çıktı>");
        process::exit(1);
    }
    match probe() {
        Ok(info) => println!("{info} · hazır: {} → {}", args[1], args[2]),
        Err(e) => {
            eprintln!("probe HATA: {e}");
            process::exit(1);
        }
    }
}
```

### §5.2 Go → ReactOS (Win32 GNU hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Go — üretim çekirdeği).
> ReactOS: Win32- sınıf → Go (GOOS=windows) **sınıf-içi taşınabilir**; ReactOS üzerinde
> çalışma **UNKNOWN** (R-S2).

```go
// [ORİJİNAL ÖRNEK · derlenmedi · Go 1.22 · ReactOS (Win32 GNU hedefi)]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Go) ile AYNI taşınabilir desen.
// Fark: Win32- sınıf (GOOS=windows); ReactOS üzerinde çalışma UNKNOWN (R-S2).
package main

import (
	"fmt"
	"os"
	"runtime"
)

func probe() string {
	// Win32- sınıf ortam keşfi (ReactOS/Windows); ReactOS-specific API YOK (§0)
	return fmt.Sprintf("GOOS-like=%s/%s", runtime.GOOS, runtime.GOARCH)
}

func main() {
	if len(os.Args) < 3 {
		fmt.Fprintln(os.Stderr, "kullanım: go run . <girdi> <çıktı>")
		os.Exit(1)
	}
	fmt.Printf("%s · hazır: %s → %s\n", probe(), os.Args[1], os.Args[2])
}
```

---

## §6. GRUP 2.4 — PHP CLI (WIN32 HEDEFİ)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §8 (PHP CLI — üretim çekirdeği).
> ReactOS: Win32- sınıf → PHP CLI/tek dosya **sınıf-içi taşınabilir**; ReactOS üzerinde
> PHP çalışma süresi **UNKNOWN** (R-S2). Uzman dalı: PHP backend (`../index.md` §Model).

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi · PHP 8.4 · ReactOS (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §8 (PHP CLI) ile AYNI taşınabilir desen.
// Fark: ReactOS (NT-typ) üzerinde PHP çalışma süresi UNKNOWN (R-S2) → CLI, ortamda PHP ister.
// Üretim: aynı CLI Linux sunucuda çalışır; ReactOS'ta sınıf-içi taşınabilirlik.
declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "kullanım: php reactos-probe.php <girdi> <çıktı>\n");
    exit(1);
}

[, $input, $output] = $argv;

// Win32- sınıf ortam keşfi (ReactOS/Windows): PHP_OS_FAMILY
echo "os_family=" . PHP_OS_FAMILY . "\n";
echo "hazır: {$input} → {$output}\n";
```

---

## §7. GRUP 2.5 — C# (WIN32 / .NET HEDEFİ)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` (C# — .NET çekirdeği; §1'de dil
> havuzu). ReactOS: Win32- sınıf → .NET CLI (net8.0 / Windows hedefi) **sınıf-içi
> taşınabilir**; ReactOS üzerinde .NET çalışma süresi **UNKNOWN** (R-S2).

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi · C# / .NET 8 · ReactOS (Win32- sınıf) hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md C# çekirdeği ile AYNI taşınabilir desen.
// Fark: ReactOS (NT-typ) üzerinde .NET çalışma süresi UNKNOWN (R-S2) → runtime ister.
// Üretim: aynı konsol Linux sunucuda çalışır; ReactOS'ta sınıf-içi taşınabilirlik.
using System;
using System.Runtime.InteropServices;

class Probe
{
    static void Main(string[] args)
    {
        if (args.Length < 2)
        {
            Console.Error.WriteLine("kullanım: dotnet run -- <girdi> <çıktı>");
            Environment.Exit(1);
        }
        // Win32- sınıf ortam keşfi (ReactOS/Windows): OSPlatform kontrolü
        Console.WriteLine($"win32={RuntimeInformation.IsOSPlatform(OSPlatform.Windows)}");
        Console.WriteLine($"hazır: {args[0]} → {args[1]}");
    }
}
```

---

## §8. PORTABİLİTE TABLOSU (REACTOS HEDEFİ)

> **Öneri (2026-10-13):** Dil bazlı taşınabilirlik, ReactOS = Win32- sınıf olduğundan
> Windows ile **aynı** (NT API yüzeyi hedeflenir). ReactOS üzerinde çalışma durumu
> her dil için **UNKNOWN** (R-S2; vault'ta cihaz kanıtı YOK).

| Dil | ReactOS-native | Portable çekirdek | Not |
|-----|----------------|-------------------|-----|
| **C/C++ (Win32)** | ⚠️ | ✔ | NT-typ hedefi — `reactos-kod-ornekleri.md` (build-chain) · blok örnekleri §14 |
| **C# (.NET)** | — | ✔* | *ReactOS üzerinde .NET çalışma süresi UNKNOWN (R-S2) |
| **JS (Node CLI)** | — | ✔* | *ReactOS üzerinde Node çalışma süresi UNKNOWN (R-S2) |
| **Python CLI** | — | ✔* | *ReactOS üzerinde Python çalışma süresi UNKNOWN (R-S2) |
| **Go (GOOS=windows)** | — | ✔* | *ReactOS üzerinde Go derleme/çalışma UNKNOWN (R-S2) |
| **Rust (mingw-w64)** | — | ✔* | *ReactOS üzerinde Rust derleme/çalışma UNKNOWN (R-S2) |
| **PHP CLI** | — | ✔* | *ReactOS üzerinde PHP çalışma süresi UNKNOWN (R-S2) |
| **Zig (mingw-w64)** | — | ✔* [TASARIM] | *ReactOS derleme/çalışma UNKNOWN (R-S2) — blok §15.1 |
| **Swift (Win32 toolchain)** | — | ✔* [TASARIM] | *Swift çalışma süresi ReactOS'ta UNKNOWN (R-S2) — blok §15.2 |
| **Java (JVM)** | — | ✔* [TASARIM] | *JVM ReactOS'ta UNKNOWN (R-S2) — blok §16.1 |
| **Kotlin (JVM)** | — | ✔* [TASARIM] | *JVM ReactOS'ta UNKNOWN (R-S2) — blok §16.2 |
| **Bash (POSIX katman)** | — | ⚠️ | *ReactOS yerel kabuğu cmd.exe; bash MSYS2/Git Bash katmanı — blok §17.1 |
| **PowerShell (Windows kabuğu)** | — | ✔* [TASARIM] | *PowerShell ReactOS'ta UNKNOWN (R-S2) — blok §17.2 |

---

## §9. BAĞLANTI MATRİSİ (İLşKİ ÖZETİ)

| Dosya | Bağlantı türü | Ne için |
|-------|--------------|---------|
| `../isletim-sistemi-turu-kod-seti.md` | `depends_on` | Portable dil çekirdeği (§2/§5/§8 — üretim çekirdeği) |
| `../isletim-sistemi-turu-kapsam.md` | `depends_on` | Sınır (§I-II: ReactOS-özel desenler komşu dalda) |
| `reactos-win32-uyumlulugu.md` | `depends_on` | Win32 API hedefi (NTOSKRNL/kernel32/ntdll) |
| `reactos-kod-ornekleri.md` | `depends_on` | Build-chain/CMake (bu dosya dil havuzu ile ayrışık) |
| `reactos-cekirdek-mimarisi.md` | `related` | NT-typ kernel (ReactOS-özel — bu dosyada YOK) |
| `reactos-surucu-modeli.md` | `related` | WDM sürücü (ReactOS-özel — bu dosyada YOK) |
| `reactos-ses-stacki.md` | `related` | Kernel Mixer/WaveRT (ReactOS-özel — bu dosyada YOK) |

---

## §10. DOĞRULAMA

### §10.1 Ölçüm özeti (2026-10-13)

- **HEAD/GET 200:** `https://reactos.org/` (200 ölçüldü — index.md §Yöntem resmi kaynak).
- **Resmi kaynaklar (index.md §Yöntem):** `github.com/reactos/reactos` (MIT) ·
  `jira.reactos.org` · `build.reactos.org` — bu dosyada yalnız kök düzeyinde referans.
- **⚠️ Bot-duvarı:** `reactos.org/wiki/*` HTTP 200 döner ancak gövde **Anubis v1.19.1**
  bot-duvarı sayfasıdır → wiki **içeriği** okunamadı (index.md §Kritik erişim notu;
  ayrıntı `reactos-cekirdek-mimarisi.md` §11.5). Bu nedenle ReactOS-özel API imzaları
  bu dosyada YOK; yalnız Win32- sınıf portable çekirdek verildi.
- **URL ayrışımı:** bu dosya `reactos.org/wiki/*` **kullanmaz** (10 wiki URL
  `reactos-kod-ornekleri.md`'de) → kesişim 0 korunur.

### §10.2 Taşınabilirlik doğrulaması (§8 tablosu gerekçesi)

| Dil | ReactOS | Gerekçe | Durum |
|-----|---------|---------|-------|
| C/C++ (Win32) | ⚠️ | NT-typ hedefi; build-chain `reactos-kod-ornekleri.md` (bot-duvarı) | verify-required |
| C# / Node / Python | ✔* | Win32- sınıf portable çekirdek; ReactOS çalışma süresi UNKNOWN (R-S2) | verify-required |
| Go / Rust | ✔* | GOOS=windows / mingw-w64 GNU hedefi; ReactOS derleme UNKNOWN (R-S2) | verify-required |
| PHP CLI | ✔* | CLI/tek dosya — Win32- sınıf; ReactOS çalışma süresi UNKNOWN (R-S2) | verify-required |
| Zig / Swift | ✔* [TASARIM] | Win32 hedefi (mingw-w64 / Swift-Windows toolchain); ReactOS çalışma/sürüm kanıtı YOK | verify-required |
| Java / Kotlin (JVM) | ✔* [TASARIM] | JVM → Win32- sınıf; ReactOS JVM kanıtı YOK (R-S2) | verify-required |
| Bash | ⚠️ | POSIX karşılaştırma katmanı; ReactOS yerel kabuğu cmd.exe → bash yok, MSYS2/Git Bash bağımlı | verify-required |
| PowerShell | ✔* [TASARIM] | Windows- sınıf kabuk; ReactOS PowerShell kanıtı YOK (R-S2) | verify-required |

### §10.3 Ek blok URL doğrulaması (2026-10-14 · §14–§17 kapısı)

> **URL kapısı (Zero-Hallucination):** aşağıdaki 8 blok URL'si blok yazılmadan **önce** HEAD ile
> doğrulandı; **200 olmadan blok yazılmadı**. Tam yollar §14–§17 fence ilk satırlarındadır.

| Blok | URL (fence ilk satırı) | Sonuç |
|------|------------------------|-------|
| C §14.1 | `learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilea` | HEAD 200 ✅ |
| C++20 §14.2 | `en.cppreference.com/w/cpp/chrono/steady_clock` | HEAD 200 ✅ |
| Zig §15.1 | `ziglang.org/documentation/master/` | HEAD 200 ✅ |
| Swift §15.2 | `developer.apple.com/documentation/foundation/processinfo` | HEAD 200 ✅ |
| Java §16.1 | `docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html` | HEAD 200 ✅ |
| Kotlin §16.2 | `kotlinlang.org/api/latest/jvm/stdlib/kotlin.system/` | HEAD 200 ✅ |
| Bash §17.1 | `man7.org/linux/man-pages/man1/bash.1.html` | HEAD 200 ✅ |
| PowerShell §17.2 | `learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_environment_variables` | HEAD 200 ✅ |

- **HEAD 200 alternatif (bilgi — kullanılmadı):** `.../sysinfoapi/nf-sysinfoapi-getsysteminfo` ·
  `.../fileapi/nf-fileapi-createfilew` · `learn.microsoft.com/en-us/powershell/scripting/overview` ·
  `kotlinlang.org/api/latest/jvm/stdlib/` (kök).
- **404 → blok revize (uydurma YOK):** `en.cppreference.com/w/cpp/program/getenv` (havuz "dikkat"
  notuna rağmen) **+ 3 alternatif** (`/w/cpp/c/stdlib/getenv` · `/w/cpp/utility/stdlib/getenv` ·
  `/w/cpp/string/byte/getenv`) **404** → C++20 bloğunda `std::getenv` **çıkarıldı**, `#ifdef _WIN32`
  derleme-zamanı dallanma kullanıldı. `kotlinlang.org/api/latest/jvm/stdlib/kotlin.system/-exit-process/`
  **404** → `kotlin.system/` paket URL'i (200).
- **Zig ham std imzaları (raw.githubusercontent.com/ziglang/zig/master — GET 200, 2026-10-14):**
  `lib/std/process.zig` → `pub fn argsWithAllocator(allocator: Allocator) ArgIterator.InitError!ArgIterator` ·
  `pub fn hasEnvVarConstant(comptime key: []const u8) bool` · ArgIterator `pub fn next(self: *Self) ?[:0]const u8` +
  `pub fn deinit(self: *Self)`; `lib/std/fs/File.zig` → `pub fn stdout() File` · `pub fn writer(file: File, buffer: []u8) Writer`;
  `lib/std/Io/Writer.zig` → `pub fn print(w: *Writer, comptime fmt: []const u8, args: anytype) Error!void` ·
  `pub fn flush(w: *Writer) Error!void`; `lib/std/Thread.zig` → `pub fn getCpuCount() CpuCountError!usize`.
  Bunlar havuzdaki teyitli imzalar **dışındaki** blok imzalarıdır (ham kaynaktan doğrulandı).

---

## §11. SORU–CEVAP

> **S1: ReactOS-özel API desenleri neden bu dosyada yok?**
> `../isletim-sistemi-turu-kapsam.md` §I-II: ReactOS-özel (çekirdek/sürücü/ses) desenler
> komşu dalındadır — bu dosyada tekrar YASAK. Burada yalnız **taşıma dil havuzu** kalır.
>
> **S2: ReactOS çalışma süresi neden UNKNOWN?**
> Vault'ta ReactOS cihaz/ortam kanıtı **YOK** (R-S2); wiki içeriği bot-duvarı arkasında
> (index.md §Kritik erişim notu). Zero-hallucination: çalışma süresi iddia edilmez.
>
> **S3: Win32 API imzaları neden çıkarılmadı?**
> Win32 genel yüzeyi (`reactos-win32-uyumlulugu.md`'de) bilgi olarak var; ancak bu dosya
> **derlenmemiş** portable örnekler taşır → ReactOS-özel imzalar §0 gereği çıkarıldı;
> yalnız ortam-keşfi desenleri (platform/arch) verildi.
>
> **S4: Bu dosya `reactos-kod-ornekleri.md` ile çakışır mı?**
> Hayır — orada **build-chain** (CMake/RosBE, 10 wiki URL), burada **dil havuzu +
> taşınabilirlik** (4 resmi kök kaynak, wiki yok) → kesişim 0 (§10.1).

---

## §12. RED TEAM

> **R1: "Node/PHP/Go/Rust ReactOS'ta çalışır" — kanıt?** Hayır — ReactOS çalışma süresi
> UNKNOWN (R-S2). Sınıf-içi taşınabilirlik (Win32- sınıf) iddia edilir; çalışma iddia
> edilmez. `⚠️`/UNKNOWN işaretli (§8).
>
> **R2: "ReactOS-özel API imzaları çıkarıldı" — neden?** Wiki bot-duvarı (ölçüm) +
> repo 0 `.cpp`/`.h` (§0) → imzalar doğrulanamadı/uydurulmadı. Zero-hallucination.
>
> **R3: "Bu dosya build-chain içerir mi?"** Hayır — build-chain `reactos-kod-ornekleri.md`
> §1-§2'de (bot-duvarı → içerik okunamadı). Bu dosya yalnız dil havuzu (§2-§7).
>
> **R4: "URL ayrışımı korundu mu?"** Evet — bu dosya `reactos.org/wiki/*` kullanmaz
> (10 wiki URL orada) → 4 resmi kök kaynak (§10.1); kesişim 0.
>
> **R5: "Sürüm 0.4.16+ iddiası?"** UNKNOWN — bot-duvarı nedeniyle doğrulanamadı
> (index.md §open_questions; R-S1). Bu dosyada sürüm iddiası YOK.

---

## §13. OTORİTE & KAYNAK

| Bilgi | Kaynak | Durum |
|-------|--------|-------|
| ReactOS model (NT-typ / Win32 hedefi) | `index.md` §Kapsam | SSOT (bilgi) |
| Üretim/CI kontratı | `../linux/index.md` §Contract | SSOT (bilgi) |
| Win32 API hedefi | `reactos-win32-uyumlulugu.md` | Bilgi (10 URL) |
| Build-chain/CMake | `reactos-kod-ornekleri.md` §1-§2 | ⚠️ bot-duvarı (içerik okunamadı) |
| ReactOS-özel (çekirdek/sürücü/ses) | `reactos-cekirdek-mimarisi.md` / `reactos-surucu-modeli.md` / `reactos-ses-stacki.md` | Bu dosyada YOK (§I-II) |
| Sürüm 0.4.16+ | `index.md` §open_questions | UNKNOWN (R-S1) |
| Taşınabilir dil çekirdeği | `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8 | SSOT (bilgi) |

**Öneri (2026-10-13):** `reactos.org` HEAD 200 ölçüldü; `reactos.org/wiki/*` içeriği
**Anubis v1.19.1 bot-duvarı** arkasında olduğu için ReactOS-özel API imzaları bu dosyada
**çıkarıldı** (yalnız Win32- sınıf portable çekirdek verildi). ReactOS çalışma süresi
her dil için **UNKNOWN** (R-S2; vault'ta cihaz kanıtı YOK). URL ayrışımı korundu (bu
dosya wiki kullanmaz → kesişim 0). Sürüm iddiası UNKNOWN (R-S1). Taşınabilirlik
sınıf-içi (§10.2); derleme/çalışma kanıtı yok → `verify-required`.

---

## §14. GRUP 2.6 — C/C++20 (WIN32 YEREL YÜZEYİ)

> **Çerçeve:** ReactOS = NT-typ çekirdek + Win32 API uyumluluk **hedefi**; çekirdek dili C'dir →
> C/C++20 + Win32 API bu dosyada **sınıf-içi doğrudan** hedeftir. Win32 API uyumluluk çerçevesi
> `reactos-win32-uyumlulugu.md`'de (sahiplik — R4), build-chain `reactos-kod-ornekleri.md` §1-§2'de.

### §14.1 C → ReactOS (Win32 native)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §A.7 (C — derleme zamanı dallanma) ve
> §C.7 (CreateProcessW yüzeyi). ReactOS üzerinde derleme/çalışma kanıtı YOK (R-S2) → blok
> `⚠️ derlenmedi`; hata açık: return kodu + `stderr` (sessiz fallback YOK).

```c
https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilea
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* C11 · ReactOS (Win32 native hedefi) — Win32 API doğrudan (kernel32). */
/* Hata açık: her hata satırı stderr'e yazılır + sıfır olmayan return kodu. */
#include <windows.h>
#include <stdio.h>

int main(int argc, char **argv)
{
    if (argc < 3) {
        fprintf(stderr, "kullanım: reactos-probe.exe <girdi> <çıktı>\n");
        return 1;
    }

    /* Win32- sınıf ortam keşfi (ReactOS/Windows): işlem mimarisi + CPU sayısı */
    SYSTEM_INFO si;
    GetSystemInfo(&si);

    /* Girdi dosyasını Win32 yüzeyinden aç — başarısızlık sessiz geçilmez */
    HANDLE h = CreateFileA(argv[1], GENERIC_READ, FILE_SHARE_READ, NULL,
                           OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    if (h == INVALID_HANDLE_VALUE) {
        fprintf(stderr, "probe HATA: CreateFileA(%s) başarısız: GetLastError=%lu\n",
                argv[1], (unsigned long)GetLastError());
        return 1;
    }
    CloseHandle(h);

    MEMORYSTATUSEX ms;
    ms.dwLength = sizeof(ms);
    if (!GlobalMemoryStatusEx(&ms)) {
        fprintf(stderr, "probe HATA: GlobalMemoryStatusEx başarısız: GetLastError=%lu\n",
                (unsigned long)GetLastError());
        return 1;
    }

    printf("arch=%u cpus=%lu mem_load=%lu%%\n",
           (unsigned)si.wProcessorArchitecture,
           (unsigned long)si.dwNumberOfProcessors,
           (unsigned long)ms.dwMemoryLoad);
    printf("hazır: %s → %s\n", argv[1], argv[2]);
    return 0;
}
```

### §14.2 C++20 → ReactOS (Win32 native)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §B.7 (C++20 — `std::filesystem`).
> `std::getenv` bu turda **kullanılmadı**: havuzdaki getenv URL'i + 3 alternatif **404** (§10.3)
> → dallanma `#ifdef _WIN32` ile derleme zamanına taşındı. ReactOS derleme/çalışma UNKNOWN (R-S2).

```cpp
https://en.cppreference.com/w/cpp/chrono/steady_clock
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* C++20 · ReactOS (Win32 native hedefi) — hata açık: exception + return kodu. */
/* getenv URL 404 (§10.3) → std::getenv YOK; Win32 sınıfı #ifdef _WIN32 ile sabit. */
#include <chrono>
#include <iostream>
#include <stdexcept>
#include <string>

std::string probe()
{
#ifdef _WIN32
    // Win32- sınıf (ReactOS/NT-typ hedefi) — derleme zamanında sabit.
    return "win32-class";
#else
    throw std::runtime_error("bu derleme Win32- sınıf hedefi değil (_WIN32 tanımsız)");
#endif
}

int main(int argc, char **argv)
{
    if (argc < 3) {
        std::cerr << "kullanım: probe <girdi> <çıktı>\n";
        return 1;
    }
    try {
        const auto t0 = std::chrono::steady_clock::now();
        const std::string os = probe();
        const auto dt = std::chrono::steady_clock::now() - t0;
        std::cout << "os=" << os
                  << " ns=" << std::chrono::duration_cast<std::chrono::nanoseconds>(dt).count()
                  << "\n";
        std::cout << "hazır: " << argv[1] << " → " << argv[2] << "\n";
    } catch (const std::exception &e) {
        std::cerr << "probe HATA: " << e.what() << "\n";
        return 1;
    }
    return 0;
}
```

---

## §15. GRUP 2.7 — ZIG/SWIFT (WIN32 HEDEFİ — [TASARIM])

> **Çerçeve:** ReactOS'in kendi sürüm zinciri Zig/Swift içermez → bu bloklar **[TASARIM]**
> taşınabilirlik örnekleridir: dil, Win32- sınıf hedefe (mingw-w64 / Swift-Windows toolchain)
> derlenebilir; ReactOS üzerinde derleme/çalışma/versiyon kanıtı **YOK** (R-S2/S-R1).

### §15.1 Zig → ReactOS (Win32 GNU/mingw hedefi)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §A.8 (Zig — `builtin.os.tag`). İmzalar
> **ham std kaynakından** doğrulandı (§10.3 — raw.githubusercontent ziglang/zig master);
> havuz imzalarından `hasEnvVarConstant` · `Thread.getCpuCount` kullanıldı.

```zig
https://ziglang.org/documentation/master/
// [ORİJİNAL ÖRNEK · derlenmedi]
// Zig · ReactOS (Win32 GNU/mingw hedefi) — [TASARIM] · ReactOS derleme/çalışma UNKNOWN (R-S2).
// Ham std doğrulama: lib/std/{process.zig, fs/File.zig, Io/Writer.zig, Thread.zig} (§10.3).
// Hata açık: error union — `try`/`orelse` ile propagate; sessiz fallback YOK.
const std = @import("std");

pub fn main() !void {
    var args = try std.process.argsWithAllocator(std.heap.page_allocator);
    defer args.deinit();
    _ = args.next();
    const input = args.next() orelse return error.MissingInput;
    const output = args.next() orelse return error.MissingOutput;

    // Win32- sınıf ortam keşfi (ReactOS/Windows): OS ortam değişkeni var mı?
    if (!std.process.hasEnvVarConstant("OS")) return error.OsEnvMissing;
    const cpus = try std.Thread.getCpuCount();

    const stdout = std.fs.File.stdout();
    var buf: [512]u8 = undefined;
    var writer = stdout.writer(&buf);
    try writer.print("cpus={d} · hazır: {s} → {s}\n", .{ cpus, input, output });
    try writer.flush();
}
```

### §15.2 Swift → ReactOS (Win32- sınıf — [TASARIM])

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §A.9 (Swift — `#if os(...)`) §A.10 (JVM
> ortam keşfi deseni). Foundation `ProcessInfo` yüzeyi Win32- sınıf araç zincirinde (Swift
> toolchain for Windows) mevcuttur; ReactOS üzerinde Swift çalışma süresi **UNKNOWN** (R-S2).

```swift
https://developer.apple.com/documentation/foundation/processinfo
// [ORİJİNAL ÖRNEK · derlenmedi]
// Swift · ReactOS (Win32- sınıf) hedefi — [TASARIM] · ReactOS çalışma süresi UNKNOWN (R-S2).
// Hata açık: throw + do/catch; sessiz fallback YOK.
import Foundation

enum ProbeError: Error, CustomStringConvertible {
    case missingArguments
    case missingOsEnv

    var description: String {
        switch self {
        case .missingArguments: return "eksik argüman: <girdi> <çıktı> bekleniyor"
        case .missingOsEnv: return "OS ortam değişkeni yok (Win32- sınıf beklenti)"
        }
    }
}

func probe() throws -> String {
    // Win32- sınıf ortam keşfi (ReactOS/Windows): OS ortam değişkeni (örn. Windows_NT)
    guard let os = ProcessInfo.processInfo.environment["OS"] else {
        throw ProbeError.missingOsEnv
    }
    return "os_env=\(os) · \(ProcessInfo.processInfo.operatingSystemVersionString)"
}

let args = CommandLine.arguments
guard args.count >= 3 else {
    FileHandle.standardError.write(Data("kullanım: probe <girdi> <çıktı>\n".utf8))
    exit(1)
}
do {
    let info = try probe()
    print("\(info) · hazır: \(args[1]) → \(args[2])")
} catch {
    FileHandle.standardError.write(Data("probe HATA: \(error)\n".utf8))
    exit(1)
}
```

---

## §16. GRUP 2.8 — JAVA/KOTLIN (JVM — WIN32 HEDEFİ)

> **Çerçeve:** ReactOS JVM **dahil etmez** (R-S2); Java/Kotlin → Win32- sınıf **[TASARIM]**
> taşınabilirlik: JVM, Windows sürümünde Win32 sınıfını hedefler. ReactOS üzerinde JVM
> çalışma süresi **UNKNOWN**. Dosya/CLI çekirdek desenleri ana sette (R4 — kopyalanmaz).

### §16.1 Java → ReactOS (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §A.10 (Java/Kotlin — `System.getProperty("os.name")`).

```java
https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi]
// Java 21 · ReactOS (Win32- sınıf) hedefi — [TASARIM] · ReactOS JVM kanıtı YOK (R-S2).
// Hata açık: exception (throw) + System.exit(1); sessiz fallback YOK.
public final class Probe {
    private Probe() {}

    public static void main(String[] args) throws Exception {
        if (args.length < 2) {
            System.err.println("kullanım: java Probe <girdi> <çıktı>");
            System.exit(1);
        }

        // Win32- sınıf ortam keşfi (ReactOS/Windows): os.name örn. "Windows ..."
        String osName = System.getProperty("os.name");
        if (osName == null || osName.isBlank()) {
            throw new IllegalStateException("os.name yok (Win32- sınıf beklenti)");
        }

        System.out.println("os_name=" + osName);
        System.out.println("hazır: " + args[0] + " → " + args[1]);
    }
}
```

### §16.2 Kotlin/JVM → ReactOS (Win32- sınıf)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §A.10 (Java/Kotlin — ortam keşfi deseni).

```kotlin
https://kotlinlang.org/api/latest/jvm/stdlib/kotlin.system/
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kotlin/JVM · ReactOS (Win32- sınıf) hedefi — [TASARIM] · ReactOS JVM kanıtı YOK (R-S2).
// Hata açık: runCatching + onFailure (Result); sessiz fallback YOK.
import kotlin.system.exitProcess

fun probe(): Result<String> = runCatching {
    // Win32- sınıf ortam keşfi (ReactOS/Windows): os.name örn. "Windows ..."
    val osName = System.getProperty("os.name")
        ?: error("os.name yok (Win32- sınıf beklenti)")
    require(osName.isNotBlank()) { "os.name boş" }
    "os_name=$osName"
}

fun main(args: Array<String>) {
    if (args.size < 2) {
        System.err.println("kullanım: ProbeKt <girdi> <çıktı>")
        exitProcess(1)
    }
    probe().fold(
        onSuccess = { info ->
            println("$info · hazır: ${args[0]} → ${args[1]}")
        },
        onFailure = { e ->
            System.err.println("probe HATA: ${e.message}")
            exitProcess(1)
        }
    )
}
```

---

## §17. GRUP 2.9 — KABUK BETİKLERİ (BASH/POWERSHELL)

> **Çerçeve:** ReactOS **yerel kabuğu `cmd.exe`'dir**. Bash = POSIX karşılaştırma katmanı
> (yalnız MSYS2/Git Bash gibi bir katmanla gelir); PowerShell = Windows- sınıf kabuk.
> Her ikisi de ReactOS'ta **çalışma kanıtsız** (R-S2). Seçim matrisi ana sette
> `../isletim-sistemi-turu-kod-seti.md` §G.3'te (sahiplik — R4).

### §17.1 Bash → ReactOS (POSIX karşılaştırma katmanı)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §G.1 (Bash — CI birincil kabuğu).
> POSIX imzalar (man7) yalnız **karşılaştırma** içindir; ReactOS'ta bash yok → katman bağımlı.

```bash
https://man7.org/linux/man-pages/man1/bash.1.html
# [ORİJİNAL ÖRNEK · derlenmedi]
# Bash 5 · ReactOS (Win32- sınıf) hedefi — POSIX karşılaştırma katmanı (bkz §G.1).
# ReactOS yerel kabuğu cmd.exe'dir; bash yalnız MSYS2/Git Bash katmanıyla gelir →
# ReactOS üzerinde bash çalışma süresi UNKNOWN (R-S2).
# Hata açık: set -euo pipefail + stderr + exit kodu; sessiz fallback YOK.
set -euo pipefail

if [ "$#" -lt 2 ]; then
  echo "kullanım: probe.sh <girdi> <çıktı>" >&2
  exit 1
fi

input=$1
output=$2

# Win32- sınıf ortam keşfi (ReactOS/Windows): OS ortam değişkeni (örn. Windows_NT)
if [ -z "${OS:-}" ]; then
  echo "probe HATA: OS ortam değişkeni yok (Win32- sınıf beklenti)" >&2
  exit 1
fi

echo "os=${OS}"
echo "hazır: ${input} → ${output}"
```

### §17.2 PowerShell → ReactOS (Windows- sınıf kabuk)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §G.2 (PowerShell — Windows/IIS servis
> sınıfı). ReactOS üzerinde PowerShell kanıtı YOK (R-S2) → [TASARIM] taşınabilirlik.

```powershell
https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_environment_variables
# [ORİJİNAL ÖRNEK · derlenmedi]
# PowerShell 7 · ReactOS (Win32- sınıf) hedefi — Windows- sınıf kabuk (bkz §G.2).
# ReactOS üzerinde PowerShell kanıtı YOK (R-S2).
# Hata açık: throw + try/catch + exit kodu; sessiz fallback YOK.
[CmdletBinding()]
param(
    [Parameter(Mandatory = $true, Position = 0)]
    [string]$InputPath,
    [Parameter(Mandatory = $true, Position = 1)]
    [string]$OutputPath
)

$ErrorActionPreference = 'Stop'

try {
    # Win32- sınıf ortam keşfi (ReactOS/Windows): OS ortam değişkeni (örn. Windows_NT)
    if (-not $env:OS) {
        throw "OS ortam değişkeni yok (Win32- sınıf beklenti)"
    }
    $arch = [System.Environment]::GetEnvironmentVariable('PROCESSOR_ARCHITECTURE')
    if (-not $arch) {
        throw "PROCESSOR_ARCHITECTURE ortam değişkeni yok"
    }
    Write-Output "os=$($env:OS) arch=$arch"
    Write-Output "hazır: $InputPath → $OutputPath"
}
catch {
    [Console]::Error.WriteLine("probe HATA: $_")
    exit 1
}
```

> **Sahiplik notu (R4):** §14–§17 yalnız **dil yüzeyi** verir. Win32 API uyumluluk çerçevesi
> `reactos-win32-uyumlulugu.md`'de · build-chain/CMake `reactos-kod-ornekleri.md` §1-§2'de ·
> dil çekirdek desenleri (dosya yolu · süreç · ortam · kabuk seçim) ana sette
> `../isletim-sistemi-turu-kod-seti.md` §A/§B/§C/§G'de — bu dosyaya **kopyalanmaz**.
> Tüm §14–§17 blokları ReactOS'ta **çalışma kanıtsızdır** (R-S2); Java/Kotlin/Zig/Swift
> blokları **[TASARIM]**, Bash katman bağımlıdır.
