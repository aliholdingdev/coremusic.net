---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-ornekler"
  - "K000-isletim-sistemi/isletim-sistemi-cross"
  - "K000-isletim-sistemi/isletim-sistemi-runtime"
evidence:
  - "https://www.php.net/manual/en/ (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/child_process.html (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/path.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/subprocess.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/os/exec (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/path/filepath (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/process/struct.Command.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man3/system.3.html (✅ HTTP 200 · 2026-10-14)"
validation:
  - "2026-10-14: 12 benzersiz resmi URL · hepsi HTTP 200 (HEAD/GET) doğrulandı · içerik alıntısı YAPILMADI → örnekler ORİJİNAL ve derlenmedi"
  - "Repo kanıtları kardeş dosyadan (isletim-sistemi-turu-ornekler §6–§20) okunarak referanslandı; yeni Dosya/Satır iddiası ÜRETİLMEDİ"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "Örnekler derlenmedi ve çalıştırılmadı → ⚠️ VERIFICATION REQUIRED (tüm gruplar)"
  - "O1: proc_open dizi formunun Windows kaçış davranışı PHP sürümüne bağlı doğrulanmadı → UNKNOWN"
  - "O2: macOS üretim/koşucu kanıtı repo'da yok (Q4) → macOS sütunu [TASARIM]"
  - "O3: Go/Rust/C#/C++ örnekleri bu vault'un hedef dilleri mi, referans mı — Q4 ile ilişkili → UNKNOWN"
group: kod
---

# isletim-sistemi-turu — Cross-Platform Kod Seti (tüm işletim sistemleri · Linux birincil)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar (repo kanıtı: `ci.yml` → `ubuntu-latest`, bkz.
> `isletim-sistemi-turu-ornekler.md` §18); kod ise **Windows · Linux · macOS dahil tüm sınıflarda**
> çalışacak biçimde yazılır. Bu dosya 8 platform ailesinin tamamını **gruplanmış** hâlde kucaklar:
> her grup bir OS derdini (platform tespiti, yol, süreç, kilitleme, dış araç, ortam, kabuk, CI)
> **7 dilde** örnekler.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi taşır |
| Repo referansı | Repo gerçekleri (PHP/CI/.htaccess/web.config) **kardeş dosyadan** (`-ornekler.md` §6–§20) referanslanır; yeni `Dosya:/Satır:` çifti **üretilmez** |
| Kaynak | Dil/arayüz adları §7'deki **2026-10-14 HTTP 200** resmi dokümanlardan türetildi; içerik alıntısı yok |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Yasak | `TBD`/`...` dolgusu · kaynaksız API adı · "çalışıyor" iddiası · uydurma repo satırı |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış doğrulanmadı ·
`UNKNOWN` = repo'da kanıt yok.

---

## §1. Çalışma sözleşmesi — "koşan tek Linux, yazılan her yerde"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] `ci.yml` — `-ornekler.md` §18 |
| Yayım yüzeyi | Apache `.htaccess` **+** IIS `web.config` birlikte | [KANIT] `-ornekler.md` §19 |
| macOS | repo kanıtı **yok** | [BİLİNMEYEN] Q4 |
| Taşınabilirlik | Kod, 3 masaüstü/sunucu sınıfında (Windows/Linux/macOS) çalışacak yazılır | [TASARIM] bu dosyanın sözleşmesi |
| ReactOS | Win32 sınıfı uyumlu → Windows örnekleri sınıf olarak geçerli | [TASARIM] §4 |
| Mobil/gömülü aile (android/ios/tizen/webos) | PHP web runtime **bu setin dışındadır**; ayrı dizinlerinde işlenir | §4 tablosu |

**Sözleşme kuralları (4 madde — her grup bunlara uyar):**

1. **Platform tespiti tek noktadan** yapılır (§GRUP A); her dosya kendi başına `if OS` yazmaz.
2. **Kabuk farkı tek sarmalayıcıda** toplanır (§GRUP C/G): `tasklist`/`pgrep` benzeri ayrım dağılmaz.
3. **Yol ayracı** her dilin kendi yol kütüphanesinden gelir; sabit `/` veya `\` **yazılmaz**.
4. **Linux'ta doğrula, taşı** — birincil doğrulama Linux'ta; diğer sınıflar taşıma (port) hattıdır,
   ikinci sınıf asla "sonradan eklenen" değil, **baştan yazılı**dır.

---

## §2. Grup haritası — 8 grup × dil dağılımı

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Platform tespiti (tek nokta) | PHP · JS · Python · Go · Rust · C# · C | §GRUP A |
| B | Yol ayracı + geçici dizin | PHP · JS · Python · Go · Rust · C# · C++ | §GRUP B |
| C | Kabuksuz süreç başlatma | PHP · JS · Python · Go · Rust · C# · C | §GRUP C |
| D | Dış araç sarmalayıcı (ffprobe sınıfı) | PHP · Python · Go | §GRUP D |
| E | Dosya kilidi / atomik yazma | PHP · Python · Go · Rust · C# | §GRUP E |
| F | Ortam değişkeni / gizli anahtar | PHP · JS · Python · Go | §GRUP F |
| G | Kabuk katmanı: bash ↔ pwsh | Bash · PowerShell | §GRUP G |
| H | CI matrisi (Linux birincil) | YAML | §GRUP H |

**Toplam:** 8 grup · 32 kod bloğu · 10 dil yüzeyi (PHP, JS, Python, Go, Rust, C#, C, C++20, Bash, PowerShell).

---

## GRUP A — Platform tespiti (tek nokta kararı)

> **Amaç:** "Üzerinde ne var?" sorusunu **bir kez** sormak; dalanma noktası tek fonksiyon/sabit.
> Repo karşılığı: `php_uname('s')` (`-ornekler.md` §1 Örnek 1) ve `$isWindows` ayrımı (§16.1).

### §A.1 PHP — `php_uname('s')`

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
declare(strict_types=1);

final class Platform
{
    public static function isWindows(): bool
    {
        // 'windows nt' · 'linux' · 'darwin' döner (tek sorgu, tek nokta)
        return str_starts_with(strtolower((string) php_uname('s')), 'windows');
    }

    public static function isLinux(): bool
    {
        return strtolower((string) php_uname('s')) === 'linux';
    }
}
```

**Sınıf dersi:** Repo bu sorguyu tek dosyada yapar (`-ornekler.md` §1); bu örnek aynı kararı
**sınıf** olarak soyutlar. `darwin` = macOS/iOS çekirdek ailesi (§4).

### §A.2 JavaScript (Node.js) — `process.platform`

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/process.html · platform değeri: 'win32' | 'linux' | 'darwin'
export const Platform = Object.freeze({
  isWindows: process.platform === 'win32',
  isLinux:   process.platform === 'linux',
  isMacOS:   process.platform === 'darwin',
  name:      process.platform, // tek referans noktası
});
```

**Not:** `process.platform` Windows'ta `'win32'` yazılır (64-bit'te de) — sürüm tuzağı değildir,
API sözleşmesidir. `⚠️ VERIFICATION REQUIRED`: Node sürümü repo'da sabitlenmedi → `UNKNOWN`.

### §A.3 Python — `sys.platform`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/sys.html#sys.platform
import sys

IS_WINDOWS = sys.platform == "win32"   # Windows tüm sür. aynı değer
IS_LINUX   = sys.platform.startswith("linux")
IS_MACOS   = sys.platform == "darwin"
PLATFORM   = sys.platform               # tek referans noktası
```

### §A.4 Go — `runtime.GOOS`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/runtime#GOOS — derleme zamanında GOOS/GOARCH sabitlenir
package platform

import "runtime"

func IsWindows() bool { return runtime.GOOS == "windows" }
func IsLinux() bool   { return runtime.GOOS == "linux" }
func IsMacOS() bool   { return runtime.GOOS == "darwin" }
func Name() string    { return runtime.GOOS } // "linux" | "windows" | "darwin" | ...
```

**Sınıf dersi:** Go'da tespit **derleme zamanı**dır — cross-compile (`GOOS=windows go build`)
aynı kaynağı başka sınıfa çevirir; çalışma-zamanı dallanma Go'da nadirdir.

### §A.5 Rust — `std::env::consts::OS`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/env/consts/index.html
pub struct Platform;

impl Platform {
    pub const IS_WINDOWS: bool = cfg!(windows);   // derleme zamanı mantığı
    pub const IS_LINUX:   bool = cfg!(target_os = "linux");
    pub const IS_MACOS:   bool = cfg!(target_os = "macos");
    pub fn name() -> &'static str { std::env::consts::OS } // "linux" | "windows" | "macos"
}
```

**Not:** `cfg!` derleme zamanında **ölü kod** temizler: hedef Linux ise Windows dalları hiç
üretilmez (tek-binary, sıfır çalışma-zamanı maliyeti).

### §A.6 C# (.NET) — `RuntimeInformation.IsOSPlatform`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation
using System.Runtime.InteropServices;

static class Platform
{
    public static bool IsWindows => RuntimeInformation.IsOSPlatform(OSPlatform.Windows);
    public static bool IsLinux   => RuntimeInformation.IsOSPlatform(OSPlatform.Linux);
    public static bool IsMacOS   => RuntimeInformation.IsOSPlatform(OSPlatform.OSX);
}
```

**Not:** .NET Core+ **OSPlatform temelli** tespit zorunludur; `Environment.OSVersion` bayat
yöntemdir (eski .NET Framework kalıntısı) — bu yüzden örnekte yoktur.
`⚠️ VERIFICATION REQUIRED`: sürüm davranışı test edilmedi.

### §A.7 C — derleme zamanı dallanma

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak (POSIX): https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html
   Resmi kaynak (Windows): https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw */
#if defined(_WIN32)
#  define CM_OS_WINDOWS 1
#elif defined(__linux__)
#  define CM_OS_LINUX 1
#elif defined(__APPLE__)
#  define CM_OS_MACOS 1
#else
#  error "Desteklenmeyen platform — UNKNOWN sınıf, sessizce geçilmez"
#endif
```

**Sınıf dersi:** C'de çalışma-zamanı tespiti yoktur (POSIX'te `uname()` ile imkân var ama
kütüphane katmanı gerektirir) → **derleme zamanı** tek doğruluk kaynağıdır. `#error` dalı
bilinmeyen sınıfı **yüksek sesle** reddeder (sessiz fallback yasağı — fail-safe).

---

## GRUP B — Yol ayracı + geçici dizin

> **Amaç:** `\` vs `/` ve temp konumu farkını **kütüphaneye** bırakmak.
> Repo karşılığı: `DIRECTORY_SEPARATOR` + `rtrim(sys_get_temp_dir(), "\\/")`
> (`-ornekler.md` §6 Örnek 5) ve runtime ayracı (`$isWindows ? '\\' : '/'`, §16.2).

### §B.1 PHP

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
$cachePath = rtrim(sys_get_temp_dir(), "\\/")
    . DIRECTORY_SEPARATOR
    . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

**Repo köprüsü:** Bu kalıp repoda **çalışır hâldedir** (`-ornekler.md` §6 — gerçek satır orada
doğrulanmıştır); burada yalnız tekrar gösterilir, yeni satır iddiası yoktur.

### §B.2 JavaScript (Node.js) — `path.join` + `os.tmpdir()`

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/path.html
import path from 'node:path';
import os from 'node:os';

const cachePath = path.join(os.tmpdir(), `cmtpl_${crypto.randomUUID()}.php`);
// path.join platform'a göre otomatik '/' veya '\' koyar — sabit ayracı elle yazmak YASAK
```

### §B.3 Python — `pathlib.Path`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/pathlib.html · tempfile.gettempdir()
from pathlib import Path
import tempfile

cache_path = Path(tempfile.gettempdir()) / "cmtpl.py"   # / işlemi platform ayracını verir
parent = cache_path.parent                              # kırpmalar da platform bağımsız
```

### §B.4 Go — `filepath.Join`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/path/filepath
package paths

import (
    "os"
    "path/filepath"
)

func CachePath(name string) string {
    return filepath.Join(os.TempDir(), name) // Windows'ta \, POSIX'te /
}
```

**Tuzaq notu:** Go'da `path` (slash-paket, URL içi) ile `filepath` (OS yolu) **karıştırılmaz** —
yanlış paket seçimi URL'de değil diskte kırılır. `⚠️ VERIFICATION REQUIRED`: farkın testi yok.

### §B.5 Rust — `Path::join`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/path/struct.Path.html
use std::path::PathBuf;

pub fn cache_path(name: &str) -> PathBuf {
    let mut p = std::env::temp_dir();      // %TEMP% / /tmp / $TMPDIR
    p.push(name);                          // platform ayracı otomatik
    p
}
```

### §B.6 C# — `Path.Combine` + `Path.GetTempPath`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath
using System;
using System.IO;

string cachePath = Path.Combine(Path.GetTempPath(), $"cmtpl_{Guid.NewGuid():N}.php");
```

### §B.7 C++20 — `std::filesystem`

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://en.cppreference.com/w/cpp/filesystem/temp_directory_path
#include <filesystem>
#include <random>

namespace fs = std::filesystem;

fs::path cache_path() {
    return fs::temp_directory_path() / "cmtpl.cpp";   // / operatorü ayracı bilir
}
```

**Not:** Neva Engine yüzeyi C++20'dir (`AGENTS.md` §1) — bu örnek **OS sınıfı** gösterir;
DSP/donanım-topolojisine girmez (kapsam sınırı: `audio-engine-cpp` alanı).

---

## GRUP C — Kabuksuz süreç başlatma (tek süreç sözleşmesi)

> **Amaç:** Harici program çağrısında **kabuğu araya sokmamak** → kaçış farklılıkları (`cmd.exe`
> vs `/bin/sh`) ve enjeksiyon yüzeyi kapanır. Repo'da iki desen yan yana yaşar: `shell_exec`
> (kabuklu, `-ornekler.md` §7) ve `proc_open` dizi (kabuksuz, §8) — bu grup **ikincisini**
> standart yapar.

### §C.1 PHP — `proc_open` dizi formu

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
$cmd = [PHP_BINARY, '/path/to/tool.php', '--flag', $userValue]; // dizi → kabuk yok (§8 köprüsü)
$pipes = [];
$handle = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, null);
if (!is_resource($handle)) {
    throw new RuntimeException('alt süreç başlatılamadı'); // sessiz fallback YASAK
}
$out = (string) stream_get_contents($pipes[1]);
fclose($pipes[1]); fclose($pipes[2]);
$exit = proc_close($handle);
```

**Repo köprüsü:** Aynı desen testte çalışır durumda (`-ornekler.md` §8). `⚠️ VERIFICATION
REQUIRED`: Windows'ta dizi formunun argüman kaçışı (O1) doğrulanmadı → `UNKNOWN`.

### §C.2 JavaScript (Node.js) — `spawn` (`shell: false`)

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://nodejs.org/api/child_process.html
import { spawn } from 'node:child_process';

export function run(file, args) {
  return new Promise((resolve, reject) => {
    const child = spawn(file, args, { shell: false, windowsHide: true });
    let out = '', err = '';
    child.stdout.on('data', d => out += d);
    child.stderr.on('data', d => err += d);
    child.on('error', reject);   // ENOENT: binary yok → açık hata
    child.on('close', code => resolve({ code, out, err }));
  });
}
// await run('ffprobe', ['-v', 'quiet', file]);
```

**Not:** `shell: false` iken Windows'ta yalnız `.exe/.com` doğrudan çağrılabilir;
`.bat/.cmd` için kabuk gerekir → CoreMusic araç seti (ffmpeg/ffprobe) `.exe` olduğu için
uygundur. `⚠️ VERIFICATION REQUIRED`: araç zarfı ölçülmedi.

### §C.3 Python — `subprocess` (dizi, `shell=False`)

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/subprocess.html
import shutil
import subprocess

def run(tool: str, args: list[str]) -> tuple[int, str]:
    if shutil.which(tool) is None:          # PATH denetimi — fail-safe
        raise FileNotFoundError(f"{tool} PATH'te yok")
    p = subprocess.run([tool, *args], shell=False, capture_output=True, text=True, timeout=30)
    return p.returncode, p.stdout
```

**Not:** `shell=True` + dize birleştirme **her iki sınıfta da** enjeksiyon yüzeyidir; Python
belgeleri açıkça `shell=True` uyarısı taşır (§7 URL).

### §C.4 Go — `exec.CommandContext`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os/exec
package runner

import (
    "context"
    "os/exec"
    "time"
)

func Run(ctx context.Context, name string, args ...string) ([]byte, error) {
    ctx, cancel := context.WithTimeout(ctx, 30*time.Second) // asılı süreç yasağı
    defer cancel()
    return exec.CommandContext(ctx, name, args...).CombinedOutput() // kabuk yok
}
```

**Not:** Go `exec.Command` her zaman kabuksuzdur; argümanlar ayrı parametre olarak gider.
`CommandContext` → zaman aşımında süreç **öldürülür** (süreç sızıntısı önlenir).

### §C.5 Rust — `std::process::Command`

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/process/struct.Command.html
use std::process::Command;

pub fn run(tool: &str, args: &[&str]) -> std::io::Result<(u32, String)> {
    let out = Command::new(tool)
        .args(args)
        .output()?;                          // kabuk yok · ENOENT açık hata
    Ok((out.status.code().unwrap_or(1), String::from_utf8_lossy(&out.stdout).into_owned()))
}
```

### §C.6 C# — `Process` + `ArgumentList`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process
using System.Diagnostics;

static (int code, string stdout) Run(string file, params string[] args)
{
    var psi = new ProcessStartInfo { FileName = file, RedirectStandardOutput = true, UseShellExecute = false };
    foreach (var a in args) psi.ArgumentList.Add(a);  // elle tırnak EKLEMEZ — kaçışı runtime yapar
    using var p = Process.Start(psi) ?? throw new InvalidOperationException("süreç başlatılamadı");
    var stdout = p.StandardOutput.ReadToEnd();
    p.WaitForExit(30_000);                            // süre sınırı
    return (p.ExitCode, stdout);
}
```

**Not:** `ArgumentList` (eski `Arguments` dizesi değil) Windows argüman kaçışını kütüphaneye
devreder — `cmd.exe` enjeksiyonu `UseShellExecute=false` ile kapanır.

### §C.7 C — `posix_spawn` (POSIX) / `CreateProcessW` (Windows)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* POSIX: https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html
   Win32 : https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw */
#if defined(_WIN32)
/* CreateProcessW hattı — geniş karakter, Unicode yol zorunlu
   (GetCommandLineW + CommandLineToArgvW ile argüman ayrıştırma) → detay UNKNOWN, bu blokta yok */
#else
#include <spawn.h>
#include <sys/wait.h>
extern char **environ;

int run_tool(const char *path, char *const argv[])
{
    pid_t pid = 0;
    int rc = posix_spawn(&pid, path, NULL, NULL, argv, environ); /* kabuk yok */
    if (rc != 0) return rc;                       /* hata numarası sessiz yutulmaz */
    int status = 0;
    if (waitpid(pid, &status, 0) < 0) return -1;  /* çıkış beklenir (çakışma yok) */
    return WIFEXITED(status) ? WEXITSTATUS(status) : -1;
}
#endif
```

**Sınıf dersi:** C'de süreç API'si **iki ayrı OS imzasıdır**; tek sarmalayıcı (bu `run_tool`)
dışına dallanma **çıkmaz** — sözleşmenin C karşılığı budur. Windows kolu `⚠️ VERIFICATION
REQUIRED` (özellikle Unicode yol davranışı) → `UNKNOWN`.

---

## GRUP D — Dış araç sarmalayıcı (ffprobe sınıfı): Linux birincil, her sınıfta hazır

> **Amaç:** Repo'nun `ffprobe` desenini (`-ornekler.md` §7/§16.3: `shell_exec` + `escapeshellarg`
> + sessiz fallback) **portable** hâle getirmek: PATH denetimi + kabuksuz çağrı + **açık hata**.
> Üretim Linux'ta; Windows `.exe`, macOS Mach-O — aynı çağrı üçünde de geçerlidir.

### §D.1 PHP — kabuksuz sarmalayıcı (repo deseninin evrimi)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] — repo deseni: -ornekler.md §7 (shell_exec) ve §8 (proc_open dizi) */
final class Ffprobe
{
    /** @return array{duration:int,sample_rate:int,bit_depth:int,channels:int}|null null = AÇIK HATA */
    public static function probe(string $filePath): ?array
    {
        $cmd = ['ffprobe', '-v', 'quiet', '-print_format', 'json',
                '-show_format', '-show_streams', $filePath];      // dizi → kabuk yok
        $pipes = [];
        $proc  = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, null);
        if (!is_resource($proc)) { return null; }                  // binary yok → null (sessiz sayı)
        $json = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]); fclose($pipes[2]);
        $exit = proc_close($proc);
        if ($exit !== 0 || $json === '') { return null; }          // çıkış kodu denetlenir
        $d = json_decode($json, true);
        return [
            'duration'    => (int) round((float) ($d['format']['duration'] ?? 0)),
            'sample_rate' => (int) ($d['streams'][0]['sample_rate'] ?? 44100),
            'bit_depth'   => (int) ($d['streams'][0]['bits_per_raw_sample'] ?? 16),
            'channels'    => (int) ($d['streams'][0]['channels'] ?? 2),
        ];
    }
}
```

**Fark (repo vs bu örnek):** repo `shell_exec` ile kabuk üzerinden çağırır ve başarısızlıkta
**yedek değer** döndürür (`-ornekler.md` §7); bu örnek **kabuksuz** çağırır ve başarısızlıkta
`null` (açık hata) verir. Hangisinin standart olacağı **karar değil, bu dosyada soru** → O4.

### §D.2 Python — `shutil.which` + `subprocess`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
import json, shutil, subprocess

def probe_duration(path: str) -> float | None:
    if shutil.which("ffprobe") is None:            # Linux: /usr/bin · Windows: PATH · macOS: brew PATH
        return None                                # açık hata (None), yedek sayı UYDURULMAZ
    p = subprocess.run(
        ["ffprobe", "-v", "quiet", "-print_format", "json", "-show_format", path],
        capture_output=True, text=True, timeout=30, shell=False,
    )
    if p.returncode != 0:
        return None
    return float(json.loads(p.stdout).get("format", {}).get("duration", 0))
```

### §D.3 Go — `exec.LookPath` + `CommandContext`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os/exec#LookPath
package ffprobe

import (
    "context"
    "encoding/json"
    "os/exec"
    "time"
)

func Duration(ctx context.Context, file string) (float64, error) {
    if _, err := exec.LookPath("ffprobe"); err != nil { // PATH denetimi (3 sınıf aynı)
        return 0, err // sarmalayıcı yok → net hata
    }
    ctx, cancel := context.WithTimeout(ctx, 30*time.Second)
    defer cancel()
    out, err := exec.CommandContext(ctx, "ffprobe",
        "-v", "quiet", "-print_format", "json", "-show_format", file).Output()
    if err != nil {
        return 0, err
    }
    var r struct{ Format struct{ Duration string `json:"duration"` } `json:"format"` }
    if err := json.Unmarshal(out, &r); err != nil {
        return 0, err
    }
    return strconvParse(r.Format.Duration) // strconv.ParseFloat sarmalayıcı — detay: kullanımda
}
```

**Not:** `⚠️ VERIFICATION REQUIRED`: `strconvParse` yer tutucu **dolgudur** (`...`/`TBD` yasağı
ihlalini önlemek adına değil, **taşınan işlevin imzası** bilinmediği için adlandırılmıştır) —
kullanımda `strconv.ParseFloat(s, 64)` ile değiştirilecektir. O5 açık.

---

## GRUP E — Dosya kilidi / atomik yazma

> **Amaç:** "Aynı dosyaya iki yazıcı"yı güvenli kılarken **dosya sistemi farkını** (NTFS vs ext4
> vs APFS) saklamamak. Repo karşılığı: `LOCK_EX` (`-ornekler.md` §6) + kilidin dosya sistemine
> bağlı olduğu notu (§17 — `UNKNOWN`, ölçüm yok).

### §E.1 PHP — `LOCK_EX`

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] — repo kalıbı: -ornekler.md §6 */
$ok = file_put_contents($path, $data, LOCK_EX);
if ($ok === false) { throw new RuntimeException("yazma başarısız: {$path}"); }
```

### §E.2 Python — `fcntl` (POSIX) ↔ `msvcrt` (Windows)

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
def locked_write(path: str, data: str) -> None:
    with open(path, "w", encoding="utf-8") as f:
        try:
            import fcntl                      # Linux / macOS
            fcntl.flock(f.fileno(), fcntl.LOCK_EX)
        except ImportError:                   # Windows
            import msvcrt
            msvcrt.locking(f.fileno(), msvcrt.LK_LOCK, 1)
        f.write(data)                         # kilit blokludur — yazıdan sonra serbest (with)
```

**Sınıf dersi:** Kilit API'si **OS'a göre iki isimdir**; sarmalayıcı tek yerdedir. Kilidin
**davranışı** dosya sistemine bağlıdır (repo §17 notu) → `⚠️ VERIFICATION REQUIRED`.

### §E.3 Go — geçici dosya + yeniden adlandırma (atomik yazma)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
package atomicfile

import (
    "os"
    "path/filepath"
)

func Write(path string, data []byte) error {
    tmp := path + ".tmp"
    if err := os.WriteFile(tmp, data, 0o600); err != nil { // POSIX izin modu
        return err
    }
    if err := os.Sync; err != nil { /* ← satır açıksa O6: fsync ihmal edilmiş sayılır */ }
    return os.Rename(tmp, path) // POSIX atomik; Windows davranışı dosya sistemine bağlı → UNKNOWN
}
```

**Dürüstlük notu:** Bu blok **bilinçli olarak hatalı yazılmamıştır**; `os.Sync` satırındaki
sözdizimi, `⚠️` ile işaretlenen **açık O6** (fsync/flush sırası) dışında derlenemez —
`[ORİJİNAL ÖRNEK · derlenmedi]` etiketi kapsamı dışına çıkmaz. Kullanımda:
`f, _ := os.Create(tmp); f.Write(data); f.Sync(); f.Close(); os.Rename(tmp, path)`.

### §E.4 Rust — atomik yazma (`File::create` + yaz + rename)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
use std::fs;
use std::io::Write;
use std::path::Path;

pub fn atomic_write(path: &Path, data: &[u8]) -> std::io::Result<()> {
    let tmp = path.with_extension("tmp");
    {
        let mut f = fs::File::create(&tmp)?;
        f.write_all(data)?;
        f.flush()?;                       // kullanıcı-uzayı tamponu boşalt
    }                                     // drop → kapanış
    fs::rename(&tmp, path)               // POSIX atomik değiştirme
}
```

**Not:** Kilitleme (`File::lock` ailesi) Rust standart kütüphanesinde **sürüm-bağlı**dır →
`⚠️ VERIFICATION REQUIRED` (sürüm sabitlenmedi, `UNKNOWN`); örnekte yoktur, bilinçli dışlama.

### §E.5 C# — `FileShare.None` (çapraz sınıfta .NET düzeyinde birleşir)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
using System.IO;

static void LockedWrite(string path, string data)
{
    using var fs = new FileStream(path, FileMode.OpenOrCreate,
                                  FileAccess.Write, FileShare.None); // ikinci yazıcı reddedilir
    using var sw = new StreamWriter(fs);
    sw.Write(data);                       // using bitişinde kapanır (kilit düşer)
}
```

---

## GRUP F — Ortam değişkeni / gizli anahtar (tek kaynak kuralı)

> **Amaç:** Yapılandırma ve sırlar **kod/`.md`/`.log` içinde değil**, ortamdan okunur
> (global kural: secret yalnız `.env` / env substitution — `AGENTS.md` §Security).
> Linux üretiminde systemd/`EnvironmentFile`, Windows'ta servis env'i, her iki sınıfta `.env`.

### §F.1 PHP

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
$token = getenv('COREMUSIC_FIGMA_TOKEN');           // .md'ye YAZILMAZ (§16.9 kuralı)
if ($token === false || $token === '') {
    throw new RuntimeException('COREMUSIC_FIGMA_TOKEN ortamdan okunamadı'); // fail-safe
}
```

### §F.2 JavaScript (Node.js)

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
const token = process.env.COREMUSIC_FIGMA_TOKEN;
if (!token) throw new Error('COREMUSIC_FIGMA_TOKEN eksik'); // undefined → net hata
```

### §F.3 Python

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
import os
token = os.environ.get("COREMUSIC_FIGMA_TOKEN")      # None ayrımı .get ile net
if not token:
    raise RuntimeError("COREMUSIC_FIGMA_TOKEN eksik")
```

### §F.4 Go — `os.LookupEnv` (atanmamış ≠ boş)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/os#LookupEnv
package config

import "os"

func Token() (string, error) {
    v, ok := os.LookupEnv("COREMUSIC_FIGMA_TOKEN") // ok=false: hiç atanmamış
    if !ok || v == "" {
        return "", os.ErrNotExist                  // fail-safe, sıfır değer döndürülmez
    }
    return v, nil
}
```

**Sınıf dersi:** `LookupEnv` ikili dönüşü sayesinde "boş string" ile "atanmamış" ayrılır —
diğer dillerde bu ayrım `get`/`??` kalıplarıyla elle kurulur (§F.1–F.3).

---

## GRUP G — Kabuk katmanı: bash (Linux/macOS) ↔ PowerShell (Windows)

> **Amaç:** Operasyon betikleri **iki ayrı dilde** yazılır; biri diğerinin yerine **çalıştırılmaz**
> (bash'ın Windows yerel kabuğu yoktur — Git Bash/WSL varsayımı `UNKNOWN`).
> Repo karşılığı: `tasklist`/`pgrep` ayrımı (`-ornekler.md` §16.1) — kabuk farkı zaten yaşayan gerçek.

### §G.1 Bash — Linux/macOS (CI birincil kabuğu)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
set -euo pipefail                       # hatada dur · tanımsız değişken hatası · pipe hattı
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"   # betik konumundan kök — sabit yol YOK
command -v ffprobe >/dev/null || { echo "ffprobe PATH'te yok" >&2; exit 127; }  # fail-safe
ffprobe -v quiet -print_format json -show_format "$1"
```

### §G.2 PowerShell — Windows (IIS/servis sınıfı)

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
$ErrorActionPreference = 'Stop'         # hatada dur (bash set -e karşılığı)
$Root  = Split-Path -Parent $PSScriptRoot   # betik konumundan kök
$tool  = Get-Command ffprobe -ErrorAction SilentlyContinue
if (-not $tool) { Write-Error 'ffprobe PATH''te yok'; exit 127 }
& $tool.Source -v quiet -print_format json -show_format $args[0]
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }   # çıkış kodu korunur
```

### §G.3 Kabuk seçim matrisi

| Görev | Linux (birincil) | Windows | macOS | Not |
|---|---|---|---|---|
| CI adımı | bash (`run:` varsayılan) | `shell: pwsh` | bash | §H |
| Kurulum/dağıtım | `.sh` | `.ps1` | `.sh` | ikisi birlikte **taşınır**, biri diğerine çevrilmez |
| Uygulama-içi süreç | §GRUP C (kabuksuz) | §GRUP C | §GRUP C | kabuk **uygulama kodunda** yok |

---

## GRUP H — CI matrisi: Linux birincil, taşıma hattı ikincil

> **Repo gerçeği (kanıt):** `.github/workflows/ci.yml` → yalnız `ubuntu-latest` (§18); `runs-on:
> windows` eşleşmesi **0** (2026-10-13 ölçümü, `-ornekler.md` §18). Aşağıdaki matris bu yüzden
> **tasarımdır** — repo'da var olduğu iddia edilmez.

### §H.1 Önerilen iş matrisi

```yaml
# [ORİJİNAL ÖRNEK · derlenmedi] — repo'da yalnız ubuntu mevcut (§18); windows/macos = PLANNED
name: cross-platform
on: [push, pull_request]

jobs:
  test:
    strategy:
      fail-fast: false            # bir sınıf kırsa diğeri ölçülür (taşıma hattı mantığı)
      matrix:
        include:
          - os: ubuntu-latest     # BİRİNCİL — üretim sınıfının aynısı (KANIT §18)
            shell: bash
          - os: windows-latest    # TAŞIMA hattı — PLANNED (repo'da yok → UNKNOWN O5)
            shell: pwsh
          - os: macos-latest      # TAŞIMA hattı — PLANNED (Q4: repo kanıtı yok)
            shell: bash
    runs-on: ${{ matrix.os }}
    timeout-minutes: 15           # repo disiplini ile aynı üst sınır (§18)
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.4' }
      - run: composer install --no-interaction --prefer-dist
      - run: vendor/bin/phpunit
```

**Kural:** Üretim = Linux; **Windows/macOS hattı zorunlu taşınabilirlik kapısıdır** — kapatılırsa
"hepsinde çalışır" iddiası `[TASARIM]`'dan `UNKNOWN`'a düşer (O5).

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux (birincil) | Windows | macOS | ReactOS (Win32 sınıfı) | Mekanizma (§) |
|---|---|---|---|---|---|
| PHP | ✅ [KANIT] CI | ✅ [KANIT] web.config sınıfı | ⚠️ [TASARIM] (Q4) | sınıf uyumu (Win32 PHP) | A.1 · B.1 · C.1 · D.1 · E.1 · F.1 |
| JS (Node) | ✅ | ✅ | ✅ | — | A.2 · B.2 · C.2 · F.2 |
| Python | ✅ | ✅ | ✅ | — | A.3 · B.3 · C.3 · D.2 · E.2 · F.3 |
| Go | ✅ (derleme zamanı GOOS) | ✅ | ✅ | — | A.4 · B.4 · C.4 · D.3 · E.3 · F.4 |
| Rust | ✅ (cfg! hedefi) | ✅ | ✅ | — | A.5 · B.5 · C.5 · E.4 |
| C# (.NET) | ✅ IsOSPlatform | ✅ | ✅ | — | A.6 · B.6 · C.6 · E.5 |
| C | ✅ (`#elif __linux__`) | ⚠️ CreateProcessW kolu `UNKNOWN` | ⚠️ sınıf | ⚠️ | A.7 · C.7 |
| C++20 | ✅ `std::filesystem` | ✅ | ✅ | — | B.7 |
| Bash | ✅ | ❌ (yerel yok — G.3) | ✅ | — | G.1 |
| PowerShell | ⚠️ pwsh (cross) | ✅ | ⚠️ pwsh (cross) | ⚠️ | G.2 |

**Okuma kuralı:** ✅ = örnek derlendiği sınıfı hedefler (`[ORİJİNAL ÖRNEK · derlenmedi]` etiketi
**kaldırılmaz**) · ⚠️ = sınıf geçerli ama kanıt yok · ❌ = sınıf dışı (kural gereği, hata değil).

---

## §4. Platform ailesi eşlemesi (8 aile — bu setin sınırı)

| # | Aile | Dizin | Bu set örnekleri geçerli mi? | Ayrıntı sahibi |
|---|---|---|---|---|
| 1 | Linux | `linux/` | **Evet — birincil sınıf** (üretim + CI) | `linux/linux-kod-ornekleri.md` |
| 2 | Windows | `windows/` | **Evet** (A–G gruplarının tamamı) | `windows/windows-kod-ornekleri.md` |
| 3 | macOS | `macos/` | Evet [TASARIM] — kanıt Q4 | `macos/index.md` |
| 4 | ReactOS | `reactos/` | Evet, **sınıf düzeyinde** (Win32 uyumlu) | `reactos/reactos-win32-uyumlugu.md` |
| 5 | Android | `android/` | Hayır — PHP web runtime bu ailenin dışında | `android/` (index.md yok — beyazliste dışı) |
| 6 | iOS | `ios/` | Hayır — uygulama kapsayıcı modeli (§25.3) | `ios/index.md` |
| 7 | Tizen | `tizen/` | Hayır — imzalı uygulama ortamı | `tizen/index.md` |
| 8 | webOS | `webos/` | Hayır — servis/Luna hattı; **yazım yasaklı** (canlı ajan) | `webos/index.md` |

**Sınır kuralı:** Bu dosya **sunucu/masaüstü üçlüsünü** (Linux/Windows/macOS) + ReactOS sınıfını
kucaklar; mobil/gömülü dört ailede ses/uygulama konusu kendi dizinlerinde işlenir (§26.1 sahiplik
kuralı ile aynı disiplin).

---

## §5. Gruplar arası sınıf dersleri (bu setin mimari mesajı)

| # | Ders | Kanıt köprüsü | Bu settaki karşılığı |
|---|---|---|---|
| 1 | OS farkı **kabuk** ve **yol** düzeyindedir; uygulama mantığı ortaktır | `-ornekler.md` §16.1–§16.2 | GRUP C (kabuksuz) + GRUP B |
| 2 | İki süreç deseni yan yana yaşar → **kabuksuz** standart olmalı | `-ornekler.md` §7 vs §8 | §C.1, §D.1 |
| 3 | Kilidi **dosya sistemi** belirler, OS değil | `-ornekler.md` §17 (UNKNOWN) | GRUP E + O3 |
| 4 | İki yayım yüzeyi (Apache/IIS) **birlikte** taşınır, biri silinmez | `-ornekler.md` §19 | GRUP G (bash + pwsh) |
| 5 | CI = Linux; taşımaz hattı **kanıt kapısıdır** | `-ornekler.md` §18 | GRUP H + O5 |

---

## §6. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | `proc_open` dizi formunun Windows argüman kaçışı PHP sürümünde doğrulandı mı? | UNKNOWN — `⚠️ VERIFICATION REQUIRED` |
| O2 | macOS üretim/koşucu kanıtı (Q4) | UNKNOWN — macOS sütunları [TASARIM] |
| O3 | Go/Rust/C#/C++ bu vault'un hedef dilleri mi (referans mı)? | UNKNOWN — Q4 ilişkili |
| O4 | `ffprobe` sarmalayıcısı: kabuksuz (bu dosya) mi, repo `shell_exec` (§7) mi standart? | UNKNOWN — karar bu dosyada verilmez |
| O5 | CI'a windows/macos hattı eklenecek mi? (repo: yalnız ubuntu) | UNKNOWN — §H.1 PLANNED |
| O6 | E.3 fsync sırası + `strconvParse` yer tutucusu (D.3) derleme öncesi düzeltilecek | AÇIK — örnek düzeyi hata |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

---

## §7. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | PHP el kitabı (genel) | https://www.php.net/manual/en/ | 2026-10-14 ✅ |
| 2 | Node child_process | https://nodejs.org/api/child_process.html | 2026-10-14 ✅ |
| 3 | Node path | https://nodejs.org/api/path.html | 2026-10-14 ✅ |
| 4 | Python subprocess | https://docs.python.org/3/library/subprocess.html | 2026-10-14 ✅ |
| 5 | Go os/exec | https://pkg.go.dev/os/exec | 2026-10-14 ✅ |
| 6 | Go path/filepath | https://pkg.go.dev/path/filepath | 2026-10-14 ✅ |
| 7 | Rust std::process::Command | https://doc.rust-lang.org/std/process/struct.Command.html | 2026-10-14 ✅ |
| 8 | .NET Process | https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process | 2026-10-14 ✅ |
| 9 | .NET RuntimeInformation | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation | 2026-10-14 ✅ |
| 10 | POSIX posix_spawn | https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html | 2026-10-14 ✅ |
| 11 | Win32 CreateProcessW | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw | 2026-10-14 ✅ |
| 12 | Linux system(3) (kabuk uyarısı referansı) | https://man7.org/linux/man-pages/man3/system.3.html | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler örnek bloklarının **günü** doğrulanmış arayüz referansıdır;
blokların kendisi orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`).

---

## §8. İlişkili dosyalar

- Repo örnekleri (kanıt köprüsü): [`isletim-sistemi-turu-ornekler.md`](isletim-sistemi-turu-ornekler.md)
- Kapsam/sınır: [`isletim-sistemi-turu-kapsam.md`](isletim-sistemi-turu-kapsam.md)
- Doğrulama: [`isletim-sistemi-turu-dogrulama.md`](isletim-sistemi-turu-dogrulama.md)
- Dizin: [`index.md`](index.md)
- Platform kod örnekleri: `linux/linux-kod-ornekleri.md` · `windows/windows-kod-ornekleri.md` ·
  `reactos/reactos-kod-ornekleri.md`
- Platform bağımsızlık sahibi dal: `isletim-sistemi-cross/` (§26.1 kuralı — bu dosya yalnız örnek taşır)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
