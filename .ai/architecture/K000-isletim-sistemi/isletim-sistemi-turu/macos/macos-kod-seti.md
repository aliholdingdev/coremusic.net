---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-coreaudio-stacki"
  - "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-kernel"
evidence:
  - "https://developer.apple.com/documentation/coreaudio (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/audiotoolbox/audiounit (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/kernel/mach (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/foundation/processinfo (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/foundation/filemanager (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man7/pthreads.7.html (✅ HTTP 200 · 2026-10-14)"
  - "https://en.cppreference.com/w/c/program/getenv · https://developer.apple.com/documentation/xcode (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/reference/conditional-compilation.html · https://doc.rust-lang.org/std/env/fn.var.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/os.html · https://pkg.go.dev/os · https://pkg.go.dev/cmd/go · https://nodejs.org/api/os.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.php.net/manual/en/function.getenv.php · https://kotlinlang.org/api/latest/jvm/stdlib/ (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Paths.html · .../Files.html · .../lang/System.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation · .../system.environment.getenvironmentvariable (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/powershell/scripting/installing-powershell-on-macos?view=powershell-7.5 · .../about_automatic_variables (✅ HTTP 200 · 2026-10-14)"
  - "https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig · .../time.zig · https://ziglang.org/documentation/master/ (✅ HTTP 200 · 2026-10-14)"
validation:
  - "2026-10-15: 28 benzersiz resmi URL (HTTP 200 kapısı) · 21 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-14: 5 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-14 (2. tur — 10 dil slotu): +11 blok (C++20 · Rust · Python · Go · C# · PHP · JS/Node · Zig · Java · Kotlin · PowerShell — java+kotlin AYRI fence) · tüm URL'ler HEAD 200 ile kapıdan geçirildi · 404 → alternatif: /w/cpp/program/getenv → /w/c/program/getenv · powershell install (viewsiz) → ?view=powershell-7.5"
  - "İçerik semantik kontrolü (fetch): zig posix.zig/time.zig sembolleri doğrulandı (getrlimit/setrlimit/getppid/isatty/Timer.start/read) · kotlin.io + kotlin.io.path'te createTemp* YOK → Kotlin bloğu JDK'ye düştü (N/A gerekçeli) · about_automatic_variables içinde 'IsMacOS' doğrulandı · node os.html içinde 'os.tmpdir()' doğrulandı"
  - "ProcessInfo/FileManager/pthreads URL'leri ios-kod-seti.md ile ortak kullanımda — bu dosya yalnız macOS'e özgü iddialarda kullanır; iOS dosyası AVFoundation yüzeyini taşır (disjoints içerikte, URL'de paylaşımlı — açıkça işaretlendi)"
  - "Repo'da macOS hedefine dair disk kanıtı YOK (Q4/M-D2) → macOS sütunları [TASARIM]"
open_questions:
  - "Örnekler derlenmedi/çalıştırılmadı → ⚠️ VERIFICATION REQUIRED (tüm gruplar)"
  - "M1: macOS üretim/koşucu kanıtı repo'da yok (Q4) → CI/dağıtım satırları [TASARIM]"
  - "M2: Mach/launchd çağrı imzaları derin doğrulanmadı → yalnız arayüz atıfı ⚠️"
group: kod
---

# macos-kod-seti — macOS (Darwin/XNU masaüstü) kod seti (orijinal, derlenmemiş)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / macos` · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar (kök set §1); kod ise **tüm sınıflarda**
> çalışacak biçimde yazılır. macOS, POSIX/Linux ailesiyle **en çok örtüşen** masaüstü sınıfıdır —
> bu dosya örtüşmeyi (ortak POSIX) ve ayrışmayı (Mach, launchd, notarization) **ayrı gruplarda** gösterir.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` taşır |
| Kaynak | Arayüz adları §7'deki **2026-10-14 HTTP 200** sayfalardan türetildi; içerik alıntısı yok |
| Derleme | macOS makinesi yok → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Repo izi | macOS hedefine dair disk kanıtı **YOK** (M-D2) → dağıtım/CI satırları [TASARIM] |
| Yasak | `TBD` dolgusu · kaynaksız API adı · "macOS'ta çalıştı" iddiası |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyet · `[TASARIM]` = tercih, kanıt
değil · `⚠️ VERIFICATION REQUIRED` = doğrulanmadı · `UNKNOWN` = repo'da kanıt yok.

---

## §1. Platform sözleşmesi — "koşan tek Linux, POSIX sınıfına taşınan kod"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] kök set §H |
| macOS çekirdek ailesi | Darwin/XNU (Mach + BSD — hibrit) | [KANIT] §GRUP E (Mach atıfı) |
| POSIX uyumu | Linux ile **yüksek** örtüşme (yol, süreç, pthread) | §GRUP A–C |
| Ayrışma | launchd (systemd yok) · Gatekeeper/notarization · APFS | §GRUP D/F |
| PHP runtime | Sunucu sınıfı — masaüstünde yalnız geliştirme | [KANIT] sınıf tanımı |

**Sözleşme kuralları (bu aileye özgü 4 madde):**

1. **POSIX çekirdeği ortaktır** → Linux örnekleri (kök set A–G grupları) macOS'ta da geçerlidir;
   bu dosya **yalnız ayrışmayı** ekler, ortak kısmı **kopyalamaz** (tekrar yasağı).
2. **systemd YOKTUR** — servis/otomatik başlatma `launchd` ile olur (§D).
3. **Gatekeeper/zarflama** dağıtımın parçasıdır: imzasız binary macOS'ta **çalışmaz** (yetki
   ayarı gerekir) → imzalama adımı kodun kendisi kadar zorunludur (§F).
4. **Ses katmanı CoreAudio'dur** (AUHAL) — ayrıntı `macos-coreaudio-stacki.md`; burada yalnız
   arayüz atıfı.

---

## §2. Grup haritası (bu dosyanın grupları)

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | POSIX örtüşmesi: yollar/temp | C · Swift · C++20 · Rust · Python · Go · JS (Node) · PHP · Java · Kotlin | §GRUP A |
| B | Süreç + zamanlama (POSIX ortak) | C · Zig | §GRUP B |
| C | Kilit/kuyruk (pthread — ortak POSIX) | C | §GRUP C |
| D | macOS ayrışması: launchd servisi | XML (plist) · Bash | §GRUP D |
| E | macOS ayrışması: Mach atıfı | C (niyet) | §GRUP E |
| F | Dağıtım: imzalama/notarization | Bash | §GRUP F |
| G | Ortam/CI (tasarım) | Swift · YAML · C# · PowerShell | §GRUP G |

**Toplam:** 7 grup · 21 kod bloğu · 17 dil/yazı-yüzeyi (C, Swift, Bash, XML/plist, YAML, not, C++20, Rust, Python, Go, JS/Node, PHP, Java, Kotlin, Zig, C#, PowerShell) + matris §3.

---

## GRUP A — POSIX örtüşmesi: yollar/temp (Linux ile ortak)

### §A.1 C — POSIX yolu (Linux ile aynı satır)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* macOS da POSIX'tir: '/' ayracı, TMPDIR env — kök set §B gruplarıyla AYNI sözleşme */
#include <stdlib.h>
#include <stdio.h>

int cm_temp_path(char *out, size_t n)
{
    const char *tmp = getenv("TMPDIR");        /* macOS launchd TMPDIR verir; yoksa /tmp */
    if (!tmp) tmp = "/tmp";
    return snprintf(out, n, "%s/cm-cache", tmp) > 0 ? 0 : -1;
}
```

**Ortaklık dersi:** Bu örneğin Linux karşılığı kök set §B'dedir — **aynı POSIX sözleşmesi**;
bu dosyada tekrar gösterilmesinin nedeni, macOS'ta `TMPDIR`'ın **launchd tarafından verilmesi**
farkıdır (masaüstü Linux'ta genelde yoktur).

### §A.2 Swift — `FileManager` (Foundation yüzeyi)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.apple.com/documentation/foundation/filemanager (✅ 2026-10-14)
import Foundation
let cache = FileManager.default.urls(for: .cachesDirectory, in: .userDomainMask).first
// macOS'ta kapsayıcı zorunlu değildir ama ~ alanları kullanıcı-özel kalır (izolasyon)
```

### §A.3 C++20 — `std::getenv("TMPDIR")` (toolchain destekli ortak POSIX)

```cpp
https://en.cppreference.com/w/c/program/getenv
[ORİJİNAL ÖRNEK · derlenmedi]
// Toolchain: https://developer.apple.com/documentation/xcode (✅ 200 · 2026-10-14) — Apple Clang / Xcode Command Line Tools
// Kapı notu: /w/cpp/program/getenv → 404 (2026-10-14); getenv C ve C++ ortak yüzeyidir → C sayfası kaynaktır (200).
// macOS farkı: TMPDIR launchd tarafından kullanıcıya verilir (§A.1 dersi); masaüstü Linux'ta genelde yoktur.
#include <cstdlib>
#include <cstdio>

int cm_macos_cache_dir(char *out, size_t n)
{
    const char *tmp = std::getenv("TMPDIR");
    if (!tmp || !*tmp) {
        std::fprintf(stderr, "TMPDIR yok — launchd ortamı beklenirdi\n"); // hata açık
        return -1;                                                        // sessiz fallback YOK
    }
    return std::snprintf(out, n, "%s/cm-cache", tmp) > 0 ? 0 : -1;
}
```

**Sınıf dersi:** C++'ta macOS'a özgü **std yüzeyi yoktur** (N/A gerekçe: std katmanı POSIX'tir) —
ayrışma **toolchain**'dedir (Apple Clang / Xcode CLT, URL 200). Bu blok "toolchain destekli ortak
POSIX" kaydıdır; `std::filesystem` yolu kök set §B.7'dedir (R4: tekrar yok, birlikte okunur).

### §A.4 Rust — `#[cfg(target_os = "macos")]` derleme kapısı (cross gerçek)

```rust
https://doc.rust-lang.org/reference/conditional-compilation.html
[ORİJİNAL ÖRNEK · derlenmedi]
// std::env::var: https://doc.rust-lang.org/std/env/fn.var.html (✅ 200 · 2026-10-14)
// Cross gerçek: hedef macOS ise blok derlenir; kök set §A.5 (runtime tespiti) aynı sorunu
// çalışma zamanında çözer — bu dosya derleme-zamanı kapısını taşır.
#[cfg(target_os = "macos")]
pub fn macos_cache_dir() -> Result<std::path::PathBuf, std::env::VarError> {
    // launchd TMPDIR; yoksa VarError yayılır — sessiz fallback yok
    let tmp = std::env::var("TMPDIR")?;
    Ok(std::path::PathBuf::from(tmp).join("cm-cache"))
}
```

### §A.5 Python — `os.environ` (CLI yüzeyi — dürüst sınır)

```python
https://docs.python.org/3/library/os.html
[ORİJİNAL ÖRNEK · derlenmedi]
# Dürüst sınır: macOS Python'u CLI yüzeyidir — GUI/framework iddiası YOK.
# Tespit imzası kök set §A.3 (sys.platform) ile aynı; bu blok yalnız macOS temp yolunu taşır.
import os
import sys

def macos_cache_dir() -> str:
    if sys.platform != "darwin":
        raise RuntimeError(f"macOS dışı platform: {sys.platform}")  # açık hata — sessiz fallback yok
    tmp = os.environ.get("TMPDIR")                                  # launchd (§A.1 dersi)
    if not tmp:
        raise RuntimeError("TMPDIR yok — launchd ortamı beklenirdi")
    return os.path.join(tmp, "cm-cache")
```

### §A.6 Go — `//go:build darwin` dosya kapısı (cross gerçek)

```go
https://pkg.go.dev/os
[ORİJİNAL ÖRNEK · derlenmedi]
// Build constraints: https://pkg.go.dev/cmd/go (✅ 200 · 2026-10-14)
// Fark: kök set §A.4 runtime.GOOS ile aynı sorunu, `//go:build darwin` DOSYA DÜZEYİNDE çözer
// (bu dosya derlenmez — çalışma-zamanı dalı değil, derleme kapısı).
//go:build darwin

package cache

import (
	"os"
	"path/filepath"
)

func CacheDir() (string, error) {
	tmp := os.Getenv("TMPDIR") // launchd; boşsa hata — sessiz fallback yok
	if tmp == "" {
		return "", os.ErrNotExist
	}
	return filepath.Join(tmp, "cm-cache"), nil
}
```

### §A.7 JavaScript (Node.js) — `os.tmpdir()` (CLI yüzeyi — dürüst sınır)

```js
https://nodejs.org/api/os.html
[ORİJİNAL ÖRNEK · derlenmedi]
// Köprü: kök set §B.2 aynı çağırır (path.join + os.tmpdir) — bu blok macOS kapısını ekler.
// Dürüst sınır: Node CLI yüzeyi; tarayıcı/tuş takımı iddiası YOK.
import os from "node:os";
import path from "node:path";

if (process.platform !== "darwin") {
  throw new Error(`macOS dışı platform: ${process.platform}`); // açık hata (tespit kök set §A.2)
}
const tmp = os.tmpdir(); // macOS: launchd'in verdiği TMPDIR (§A.1 dersi)
if (!path.isAbsolute(tmp)) {
  throw new Error(`beklenmeyen tmpdir: ${tmp}`); // sessiz fallback yok
}
export const dir = path.join(tmp, "cm-cache");
```

### §A.8 PHP — `getenv('TMPDIR')` (sunucu sınıfı — macOS'ta yalnız CLI)

```php
https://www.php.net/manual/en/function.getenv.php
[ORİJİNAL ÖRNEK · derlenmedi]
<?php
/* Dürüst sınır: PHP sunucu sınıfıdır; macOS yüzeyi = CLI/geliştirme (§1 sözleşmesi). */
/* R4: tespit kök set §A.1 (php_uname) · token kök set §F.1 (getenv) — bu blok temp yolu taşır. */
declare(strict_types=1);

function macosCacheDir(): string
{
    $tmp = getenv('TMPDIR');                               // launchd (§A.1 dersi)
    if ($tmp === false || $tmp === '') {
        throw new RuntimeException('TMPDIR yok — launchd ortamı beklenirdi'); // hata açık
    }
    return $tmp . DIRECTORY_SEPARATOR . 'cm-cache';        // sessiz fallback YOK
}
```

### §A.9 Java (JVM) — `System.getenv` + `Paths.get` (macOS = JVM kavramı)

```java
https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Paths.html
[ORİJİNAL ÖRNEK · derlenmedi]
// System.getenv: https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html (✅ 200 · 2026-10-14)
// os.name tespiti kök set §A.10'da (R4: tekrar yok).
// KAVRAM: macOS = Oracle/OpenJDK JDK 21 sınıfı — macOS kurulum kanıtı repo'da YOK (⚠️ VERIFICATION REQUIRED).
import java.nio.file.Path;
import java.nio.file.Paths;

public final class MacosCache {
    private MacosCache() {}

    public static Path cacheDir() {
        String tmp = System.getenv("TMPDIR");               // launchd (§A.1)
        if (tmp == null || tmp.isEmpty()) {
            throw new IllegalStateException("TMPDIR yok — launchd ortamı beklenirdi"); // açık hata
        }
        return Paths.get(tmp, "cm-cache");                  // POSIX yolu — Linux ile aynı sözleşme
    }
}
```

### §A.10 Kotlin (JVM) — JDK `Files.createTempDirectory` (N/A gerekçeli stdlib sınırı)

```kotlin
https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Files.html
[ORİJİNAL ÖRNEK · derlenmedi]
// Kotlin stdlib: https://kotlinlang.org/api/latest/jvm/stdlib/ (✅ 200 · 2026-10-14)
// N/A GEREKÇE (2026-10-14 içerik kontrolü): kotlin.io ve kotlin.io.path paketlerinde
// createTempFile/createTempDirectory YOK → Kotlin'in kendi temp API'si N/A'dır; blok JDK'ye düşer
// (Kotlin = JVM kavramı, §A.9 ile aynı runtime).
import java.nio.file.Files
import java.nio.file.Path

fun macosCacheDir(): Path {
    val tmp = System.getenv("TMPDIR")
        ?: error("TMPDIR yok — launchd ortamı beklenirdi")   // null → açık hata (sessiz fallback yok)
    return Files.createTempDirectory(Path.of(tmp), "cm-cache") // atomik dizin oluşturma — IOException yayılır
}
```

---

## GRUP B — Süreç + zamanlama (POSIX ortak yüzey)

### §B.1 C — `posix_spawn` (Linux ile aynı API — kök set §C.7 ile ortak imza)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* POSIX: https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html (kök set §7 — HTTP 200 2026-10-14) */
#include <spawn.h>
#include <sys/wait.h>
extern char **environ;

int run_tool_macos(const char *path, char *const argv[])
{
    pid_t pid = 0;
    int rc = posix_spawn(&pid, path, NULL, NULL, argv, environ);
    if (rc != 0) return rc;
    int status = 0;
    if (waitpid(pid, &status, 0) < 0) return -1;
    return WIFEXITED(status) ? WEXITSTATUS(status) : -1;
}
```

**Not:** İmza POSIX ortaktır (kök set §C.7 ile **aynı**); macOS'taki fark çağrılan **binary
sınıfıdır** (Mach-O, imzalı) — kaçış davranışı aynıdır. Tekrar edilmemesi için bu blok
**yalnız köprü** olarak taşındı.

### §B.2 Zig — `getrlimit`/`setrlimit` + `getppid` + `time.Timer` (cross gerçek)

```zig
https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
[ORİJİNAL ÖRNEK · derlenmedi]
// time.Timer: https://raw.githubusercontent.com/ziglang/zig/master/lib/std/time.zig (✅ 200 · 2026-10-14)
// Üst kavram: https://ziglang.org/documentation/master/ (✅ 200 · 2026-10-14)
// İmza doğrulaması (posix.zig/time.zig İÇERİĞİ fetch edilerek, 2026-10-14):
//   getrlimit(rlimit_resource) GetrlimitError!rlimit · setrlimit(rlimit_resource, rlimit) SetrlimitError!void
//   · getppid() pid_t · isatty(fd_t) bool · rlimit_resource.NOFILE mevcut · Timer.start() Error!Timer · Timer.read()
// Cross gerçek: Zig tek derleyiciyle hedef macOS üretir (kök set §A.8 builtin.os.tag ile birlikte okunur).
const std = @import("std");
const posix = std.posix;

/// Açık dosya sınırını iki katına çıkar (üst sınırı aşmadan); hata yayılır — sessiz fallback yok.
pub fn raiseOpenFiles() !void {
    const lim = try posix.getrlimit(.NOFILE);              // rlimit{cur,max}
    const want: @TypeOf(lim.cur) = @min(lim.cur * 2, lim.max);
    try posix.setrlimit(.NOFILE, .{ .cur = want, .max = lim.max }); // PermissionDenied/LimitTooBig yayılır
}

/// Üst süreç (macOS'ta launchd/terminal) + monotonic süre ölçümü.
pub fn frameBudgetExceeded(ns_budget: u64) !bool {
    _ = posix.getppid();                                   // pid_t — süreç üstü (POSIX ortak)
    var timer = try std.time.Timer.start();                // TimerUnsupported yayılır
    return timer.read() >= ns_budget;                      // ⚠️ VERIFICATION REQUIRED: ölçüm döngüsü — RT öncelik iddiası YOK (§C.1 notu)
}
```

**Not:** `RLIMIT.NOFILE` tamsayı değeri hedefe bağlıdır (darwin ≠ linux) — sabit sayı
**yazılmadı**; dar değer yalnız sembolik üye `.NOFILE` üzerinden kullanılır.

---

## GRUP C — Kilit/kuyruk (pthread — ortak POSIX)

### §C.1 C — öncelik devri mutex (kök set/linux dosyaları ile ortak desen)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* pthreads(7) POSIX yüzeyi: https://man7.org/linux/man-pages/man7/pthreads.7.html (✅ 2026-10-14) */
#include <pthread.h>

int audio_mutex_init_macos(pthread_mutex_t *m)
{
    pthread_mutexattr_t a;
    if (pthread_mutexattr_init(&a) != 0) return -1;
    if (pthread_mutexattr_setprotocol(&a, PTHREAD_PRIO_INHERIT) != 0) {
        pthread_mutexattr_destroy(&a);
        return -1;
    }
    int rc = pthread_mutex_init(m, &a);
    pthread_mutexattr_destroy(&a);
    return rc;
}
```

**Not:** Kural (kayıt `⚠️`): macOS gerçek-zamanlı öncelik **kullanıcı izni** ile sınırlıdır
(`THREAD_TIME_CONSTRAINT_POLICY` vb. — imzalar doğrulanmadı) → `SCHED_FIFO` eşleniği bu
ailede **ayrı bir karardır**; bu dosya yalnız POSIX mutex çekirdeğini gösterir.

---

## GRUP D — macOS ayrışması: launchd (systemd karşılığı)

### §D.1 XML (plist) — LaunchDaemon iskeleti

```xml
<!-- [ORİJİNAL ÖRNEK · derlenmedi] — systemd yerine launchd (M2: imzalar ⚠️) -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>            <string>net.coremusic.daemon</string>
    <key>ProgramArguments</key>
    <array>
        <string>/usr/local/libexec/coremusicd</string>
    </array>
    <key>RunAtLoad</key>        <true/>
    <key>KeepAlive</key>        <true/>   <!-- crash'te yeniden başlatma — systemd Restart=always eşleniği -->
</dict>
</plist>
```

**Sınıf dersi:** Linux'taki systemd birimi (`.service`) ile macOS'taki launchd plist'i
**aynı sorunu** (otomatik servis) **farklı biçimde** çözer; kök set'te systemd yüzeyi
Linux grubundadır, bu dosya **eşlemeyi** taşır. `⚠️ M2` — plist anahtarlarının tam listesi
derin doğrulanmadı.

### §D.2 Bash — servis durumu okuma

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
set -euo pipefail
launchctl print system/net.coremusic.daemon 2>/dev/null | head -n 15 || echo "servis yok (durum raporlanır)"
```

---

## GRUP E — macOS ayrışması: Mach (hibrit çekirdek atıfı)

### §E.1 C — Mach port iskeleti (niyet — imza ⚠️)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://developer.apple.com/documentation/kernel/mach (✅ 2026-10-14) */
/* NİYET: XNU = Mach (mesajlaşma/port) + BSD (POSIX) — bu blok yalnız Mach YÜZEYİNİ işaret eder.
   Gerçek mach_port_* / mach_msg imzaları doğrulanmadı (M2) → yazılmadı. */
typedef unsigned int cm_mach_port_t;   /* yerel işaretçi — gerçek tip ⚠️ */
```

**Dürüstlük notu:** Mach derinliği bu dosyanın **kapsam dışıdır** (kapsam: `macos-kernel.md`
+ `isletim-sistemi-kernel`); burada yalnız "hibrit çekirdeğin POSIX üstünde Mach taşıdığı"
mimari gerçeğinin **kod düzeyinde karşılığı nerede** onu işaret eder.

---

## GRUP F — Dağıtım: imzalama/notarization (macOS'e özgü zorunlu adım)

### §F.1 Bash — ad-hoc imza + zarf denetimi (geliştirme)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi] — Gatekeeper sınıfı: imzasız binary macOS'ta ENGELLENİR
set -euo pipefail
BIN="$1"
codesign --force --sign - "$BIN"          # ad-hoc imza (geliştirme); üretimde Developer ID
codesign --verify --verbose=2 "$BIN"      # denetim — başarısızlık çıkış kodu 1 (sessiz yutulmaz)
spctl --assess --type execute "$BIN" 2>&1 || echo "spctl: engellendi (notarization eksik olabilir)" >&2
```

**Kural:** Bu adımlar Linux/Windows sınıflarında **yoktur** — bu, "aynı binary her yerde
çalışmaz" sözleşmesinin macOS ayağıdır (kök set §1: Linux birincil, taşıma hattı ayrı).

---

## GRUP G — Ortam/CI (tasarım — repo'da macOS kanıtı yok, M1)

### §G.1 Swift — ortamdan okuma (kök set §F ile aynı kural)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
import Foundation
guard let token = ProcessInfo.processInfo.environment["COREMUSIC_FIGMA_TOKEN"], !token.isEmpty else {
    fatalError("COREMUSIC_FIGMA_TOKEN eksik")   // fail-safe — .md'ye YAZILMAZ
}
```

### §G.2 CI — macOS taşıma hattı (kök set §H.1'e satır)

```yaml
# [ORİJİNAL ÖRNEK · derlenmedi] — repo'da yalnız ubuntu var (§8 notu) → [TASARIM]
  macos-port:
    runs-on: macos-latest
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
      - run: php --version          # macOS sınıfı doğrulama (PHP sürümü UNKNOWN → sabitlenmez)
```

### §G.3 C# (.NET) — `RuntimeInformation.IsOSPlatform(OSPlatform.OSX)` + ortam okuma

```csharp
https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation
[ORİJİNAL ÖRNEK · derlenmedi]
// Environment.GetEnvironmentVariable: https://learn.microsoft.com/en-us/dotnet/api/system.environment.getenvironmentvariable (✅ 200 · 2026-10-14)
// KAVRAM: macOS = .NET/Mono çalışma-zamanı yüzeyi (OSPlatform.OSX) — kurulum kanıtı repo'da YOK.
// R4: tespit imzası kök set §A.6 ile AYNI; bu blok G grubu sözleşmesiyle (ortamdan okuma + fail-safe) taşır.
using System;
using System.IO;
using System.Runtime.InteropServices;

static string MacosCacheDir()
{
    if (!RuntimeInformation.IsOSPlatform(OSPlatform.OSX))
        throw new PlatformNotSupportedException("yalnız macOS");               // açık hata
    var tmp = Environment.GetEnvironmentVariable("TMPDIR");                    // launchd (§A.1)
    if (string.IsNullOrEmpty(tmp))
        throw new InvalidOperationException("TMPDIR yok — launchd ortamı beklenirdi"); // fail-safe
    return Path.Combine(tmp, "cm-cache");                                      // sessiz fallback yok
}
```

### §G.4 PowerShell (pwsh) — `$IsMacOS` kapısı (macOS kurulumu gerçek)

```powershell
https://learn.microsoft.com/en-us/powershell/scripting/installing-powershell-on-macos?view=powershell-7.5
[ORİJİNAL ÖRNEK · derlenmedi]
# $IsMacOS: https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_automatic_variables (✅ 200 — "IsMacOS" İÇERİKTE doğrulandı)
# Kapı notu: ?view= parametresiz adres 404 (2026-10-14) → view'lı sürüm 200.
# Gerçek: pwsh macOS'a resmi sayfadan kurulur; kurulum KOMUTU yazılmadı (içerik alıntısı yasak).
# R4: kök set §G.2 Windows betiğidir — tek kabuk yok, iki betik var (kök set §G.3 kabuk seçim matrisi).
if (-not $IsMacOS) {
    Write-Error 'Bu betik yalnız macOS (pwsh) üzerinde çalışır'; exit 1   # açık hata — sessiz fallback yok
}
$tmp = $env:TMPDIR
if (-not $tmp) {
    Write-Error 'TMPDIR yok — launchd ortamı beklenirdi'; exit 1
}
Join-Path $tmp 'cm-cache'
```

**Sınıf dersi:** pwsh betiği "Windows'a taşınan" değil, **iki ayrı betik** strategy'sidir —
macOS ayağı `$IsMacOS` kapısıyla başlar; bash (§D.2/§F.1) ile **yarışmaz**, sınıf ayrımıdır.

---

## §3. Taşınabilir dil × macOS kapsama matrisi

| Dil | macOS'ta durumu | Mekanizma | Etiket |
|---|---|---|---|
| C / C++ | ✅ Birincil (POSIX + CoreAudio) · C++ toolchain: Apple Clang/Xcode CLT | clang + POSIX | §A–§C · §A.3 |
| Swift | ✅ Birincil (Foundation/AudioToolbox) | SDK | §A.2/§G.1 |
| PHP | ✅ Sunucu sınıfı (CLI) | Homebrew/klasik kurulum ⚠️ (kanıt yok) | kök §A.1/F.1 · §A.8 |
| JS (Node) | ✅ (CLI yüzeyi — dürüst sınır) | POSIX runtime | kök §A.2/B.2 · §A.7 |
| Python | ✅ (CLI yüzeyi — dürüst sınır) | POSIX runtime | kök §A.3/B.3 · §A.5 |
| Go | ✅ (`GOOS=darwin` / `//go:build darwin`) | derleme zamanı | kök §A.4/B.4 · §A.6 |
| Rust | ✅ (`cfg(target_os="macos")`) | derleme zamanı | kök §A.5/B.5 · §A.4 |
| Zig | ✅ (cross gerçek — tek derleyici) | derleme zamanı (`builtin.os.tag`) | kök §A.8 · §B.2 |
| C# (.NET) | ✅ (Mono/.NET kavramı) | `IsOSPlatform(OSX)` | kök §A.6/B.6 · §G.3 |
| Java (JVM) | ✅ (JVM kavramı) | `os.name` + JDK API | kök §A.10 · §A.9 |
| Kotlin (JVM) | ✅ (JVM kavramı) — **stdlib temp API'si N/A** | JDK API (`Files.*`) | kök §A.10 · §A.10 |
| Bash | ✅ | kabuk | §D.2/§F.1 |
| PowerShell | ⚠️→✅ pwsh (macOS kurulum sayfası resmi · URL 200) | `$IsMacOS` + kurulum | kök §G.2/G.3 · §G.4 |

**Okuma kuralı:** ✅ sınıf içi · ⚠️ imkân var, kanıt yok · ❌ sınıf dışı.

---

## §4. Sınıf dersleri (bu ailenin mimari mesajı)

| # | Ders | Karşılığı |
|---|---|---|
| 1 | macOS = POSIX + Mach + launchd; **POSIX kısmı ortak**, ayrışma **servis/imza**dadır | §D/§F |
| 2 | `posix_spawn`/pthread **aynı imzadır** — tekrar değil, köprü | §B.1/§C.1 |
| 3 | İmzsız binary macOS'ta **çalışmaz** — dağıtım = kodun parçası | §F.1 |
| 4 | `TMPDIR` launchd'den gelir — Linux'taki gibi her yerde değildir | §A.1 |
| 5 | RT öncelik bu ailede **izin-kısıtlı** — Linux `SCHED_FIFO` kopyalanmaz | §C.1 notu |

---

## §5. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| M1 | macOS üretim/koşucu kanıtı (Q4) | UNKNOWN — §G [TASARIM] |
| M2 | Mach/launchd imza derinliği | ⚠️ doğrulanmadı — §D.1/§E.1 |

---

## §6. Red-team — olası 5 eleştiri ve yanıt

1. **"POSIX blokları kök setle tekrar."** Yanıt: bilinçli **köprü** olarak taşındı (§B.1/§C.1
   notları); tekrar değil, "aynı imza, farklı binary sınıfı" kaydı.
2. **"Mach örneği neredeyse boş."** Yanıt: derinlik kapsam dışı (§E.1 notu) — imza 404/dogra
   riski olmadan yalnız atıf; derinlik `macos-kernel.md`.
3. **"plist anahtarları doğrulanmadı."** Yanıt: M2 — `⚠️` satırda; derin doğrulama launchd
   dokümanı ayrı tur işidir.
4. **"PHP satırı [TASARIM], repo'da macOS PHP kanıtı yok."** Yanıt: evet — M1; evrensel
   matanma (§3) sınıf düzeyindedir, sürüm iddiası yok.
5. **"CI örneği çok ince."** Yanıt: repo'da yalnız ubuntu var (kök set §H) → macOS hattı
   **taşınma kapısıdır**, tamamlanması O5 sorusuna bağlı.

---

## §7. Kabul kontrol listesi (bu dosya)

- [x] §0–§6 (politika, sözleşme, 7 grup, 21 blok, matris, dersler, sorular, red-team)
- [x] 10 alanlı frontmatter · `node_type: code` · `group: kod` · `status: verify-required`
- [x] Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]`
- [x] **2. tur (2026-10-14): 10 dil slotu → +11 blok** — §A.3 C++20 · §A.4 Rust · §A.5 Python · §A.6 Go · §A.7 JS/Node · §A.8 PHP · §A.9 Java · §A.10 Kotlin · §B.2 Zig · §G.3 C# · §G.4 PowerShell (java+kotlin **ayrı fence**) · §2 Toplam + §3 matris güncellendi
- [x] Yeni URL'ler tek tek **HEAD 200** kapıdan geçirildi (§8) · 2×404 alternatifle değiştirildi · hiçbir 404 URL blok içinde kullanılmadı
- [x] 5 benzersiz URL (§7) · iOS dosyasıyla paylaşımlı 3 URL — içerik ayrıştırıldı (disjoints notu)
- [x] **≥500 satır** — ölçüm: **664 satır** (2026-10-14 · 2. tur sonrası) · fence sayacı **42 = 21 açılış + 21 kapanış → 21 kod bloğu** (java+kotlin ayrı fence)

---

## §8. Kaynakça ve doğrulama (2026-10-14)

| # | Konu | URL | Durum |
|---|---|---|---|
| 1 | CoreAudio | https://developer.apple.com/documentation/coreaudio | ✅ 200 |
| 2 | AudioUnit | https://developer.apple.com/documentation/audiotoolbox/audiounit | ✅ 200 |
| 3 | Mach (XNU) | https://developer.apple.com/documentation/kernel/mach | ✅ 200 |
| 4 | ProcessInfo | https://developer.apple.com/documentation/foundation/processinfo | ✅ 200 |
| 5 | FileManager | https://developer.apple.com/documentation/foundation/filemanager | ✅ 200 |
| — | POSIX posix_spawn | https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html | ✅ 200 (kök set §7 ile ortak) |
| — | pthreads(7) | https://man7.org/linux/man-pages/man7/pthreads.7.html | ✅ 200 |
| 6 | C++ getenv (C/C++ ortak yüzey) | https://en.cppreference.com/w/c/program/getenv | ✅ 200 |
| 7 | Apple toolchain (Xcode/CLT) | https://developer.apple.com/documentation/xcode | ✅ 200 |
| 8 | Rust koşullu derleme | https://doc.rust-lang.org/reference/conditional-compilation.html | ✅ 200 |
| 9 | Rust `std::env::var` | https://doc.rust-lang.org/std/env/fn.var.html | ✅ 200 |
| 10 | Python `os` | https://docs.python.org/3/library/os.html | ✅ 200 |
| 11 | Go `os` | https://pkg.go.dev/os | ✅ 200 |
| 12 | Go build constraints | https://pkg.go.dev/cmd/go | ✅ 200 |
| 13 | Node.js `os` (os.tmpdir) | https://nodejs.org/api/os.html | ✅ 200 · içerik: `os.tmpdir()` doğrulandı |
| 14 | PHP `getenv` | https://www.php.net/manual/en/function.getenv.php | ✅ 200 |
| 15 | JDK 21 `Paths` | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Paths.html | ✅ 200 |
| 16 | JDK 21 `Files` | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Files.html | ✅ 200 |
| 17 | JDK 21 `System` | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html | ✅ 200 |
| 18 | Kotlin stdlib | https://kotlinlang.org/api/latest/jvm/stdlib/ | ✅ 200 · içerik: kotlin.io + kotlin.io.path'te `createTemp*` **YOK** → N/A gerekçe |
| 19 | .NET `RuntimeInformation` | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation | ✅ 200 |
| 20 | .NET `Environment.GetEnvironmentVariable` | https://learn.microsoft.com/en-us/dotnet/api/system.environment.getenvironmentvariable | ✅ 200 |
| 21 | PowerShell macOS kurulum | https://learn.microsoft.com/en-us/powershell/scripting/installing-powershell-on-macos?view=powershell-7.5 | ✅ 200 · viewsiz sürüm 404 → alternatif |
| 22 | pwsh otomatik değişkenler (`$IsMacOS`) | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_automatic_variables | ✅ 200 · içerik: `IsMacOS` doğrulandı |
| 23 | Zig `std.posix` | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig | ✅ 200 · içerik: `getrlimit`/`setrlimit`/`getppid`/`isatty` doğrulandı |
| 24 | Zig `std.time.Timer` | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/time.zig | ✅ 200 · içerik: `Timer.start`/`read` doğrulandı |
| 25 | Zig dokümantasyon | https://ziglang.org/documentation/master/ | ✅ 200 |

**404 → alternatif kayıtları (2026-10-14, ikinci kapı turu):** `en.cppreference.com/w/cpp/program/getenv`
→ 404 (C++ hâli yok) → `w/c/program/getenv` **200** · `learn.microsoft.com/.../install/install-macos` → 404 ·
`learn.microsoft.com/.../installing-powershell-on-macos` **(viewsiz)** → 404 → `?view=powershell-7.5` **200** ·
`developer.apple.com/documentation/foundation/continuousclock` → 404 (**kullanılmadı** — pool'da
listeliydi, HEAD ile elendi). Hiçbiri bloklarda URL olarak geçmez; bloklara yalnız 200 adresler girmiştir.

**İçerik semantik kontrolü (2. tur):** URL kapısı yalnız varlık değil, **imza** da sorgulandı —
zig `posix.zig`/`time.zig` ham dosyaları fetch edilip `getrlimit(rlimit_resource)!rlimit` ·
`setrlimit(rlimit_resource, rlimit)!void` · `getppid() pid_t` · `isatty(fd_t) bool` ·
`Timer.start()!Timer` · `Timer.read()` satır satır doğrulandı; Kotlin stdlib paket sayfalarında
`createTemp*` arandı ve **bulunamadı** → §A.10 JDK'ye düştü (uydurma API yazılmadı).

**Paylaşımlı URL notu:** ProcessInfo/FileManager/pthreads bu dosya ile `ios-kod-seti.md`
arasında **ortak** kullanılır (Apple ailesi ortak zemin); içerik ayrıştırılmıştır — iOS
dosyası AVFoundation yüzeyini, bu dosya launchd/Gatekeeper/Mach ayrışmasını taşır.

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** 10 alanlı frontmatter · 8 tablo · 21 kod bloğu (fence 42/2 = 10 ilk tur + 11 2. tur) · 664 satır · 27 benzersiz URL (7 ilk tur · +20 2. tur — hepsi HEAD 200)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
