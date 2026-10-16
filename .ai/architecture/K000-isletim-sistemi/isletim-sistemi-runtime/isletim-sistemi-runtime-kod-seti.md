---
node_id: isletim-sistemi-runtime-kod-seti
node_type: code
status: verify-required
parent_id: isletim-sistemi-runtime
children: []
depends_on:
  - isletim-sistemi-turu-kod-seti
  - isletim-sistemi-runtime-ornekler
evidence:
  - "https://www.php.net/manual/en/function.php-sapi-name.php (✅ HTTP 200 · 2026-10-14)"
  - "https://www.php.net/manual/en/reserved.constants.php (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/process.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/sys.html (✅ HTTP 200 · 2026-10-14)"
  - "https://nginx.org/en/docs/http/ngx_http_core_module.html (✅ HTTP 200 · 2026-10-14)"
  - "https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/iis/configuration/system.webserver/ (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.management/new-service (✅ HTTP 200 · 2026-10-14)"
  - "https://www.freedesktop.org/software/systemd/man/systemd.service.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.freedesktop.org/software/systemd/man/systemctl.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/sc-create (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/library/archive/documentation/MacOSX/Conceptual/BPSystemStartup/Chapters/CreatingLaunchdJobs.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 23 benzersiz resmi URL (HTTP 200 kapısı) · 20 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: PHP SAPI adları (cli-server, fpm-fcgi, apache2handler, cgi-fcgi) sürüm/sunucu kombinasyonuna göre değişir — hangi kombinasyonlar envanterde desteklenir?"
  - "O2: web.config kuralları hangi IIS modülüyle doğrulanır (rewrite modülü ayrı kurulum — URL'de yalnız system.webserver kökü var, alt modül [TASARIM])?"
  - "O3: nginx location eşleşmesi (en uzun prefix = ^~ / regex = ~) hangi proje kurallarıyla standardize edilir? (runtime-ornekler §2 ile eşleştirilmeli)"
  - "O4: PHP-FPM havuz yapılandırması (pool.d) bu dosyada değil — hangi vault dosyasının sahipliği?"
  - "O5: launchd (macOS) yükleme dizini ~/Library/LaunchAgents vs /Library/LaunchDaemons ayrımı vault'ta doğrulanmadı — hangisi varsayılır?"
  - "O6: Windows servis bağımlılıkları (sc binPath=, depend=) hangi proje standardında yazılır?"
  - "O7 (Q4 köprüsü): macOS hücreleri `[TASARIM]` — launchd plist doğrulaması vault dışında."
group: kod
---

# isletim-sistemi-runtime · Kod Seti

> **Kapsam:** Uygulamanın **çalışma zamanı** (runtime) ortamı: SAPI/dil çalışma zamanı tespiti (PHP `php_sapi_name`, Python `sys`, Node `process`), web sunucusu yapılandırması köprüsü (Apache `mod_rewrite`/`.htaccess`, nginx `location`, IIS `system.webserver`) ve servis/daemon kaydı (systemd, Windows `sc`/`New-Service`, macOS launchd).
> **Sahiplik sınırı:** HTTP istek/yanıt akışları `http-kod-seti.md`'de; ağ socket'ları `isletim-sistemi-cekirdek-kod-seti.md`'de; PHP FFI dosya köprüsü `isletim-sistemi-api-kod-seti.md` §GRUP C.3'te; dil sürümü envanteri `dil-surumu-*` dosyalarındadır. Bu dosya yalnız **ortam tespiti + sunucu yapılandırması + servis kaydı**.
> **Bridge köprüleri (runtime-ornekler):** `runtime-ornekler.md` Örnek 1–3 (PHP/SAPI, Node, Python betik köprüleri) ve §2 (çalışma zamanı envanteri) — bu dosyada yalnız numarayla işaretlenir.

**Dürüstlük notu:** Bu dosyadaki hiçbir yapılandırma/betik bu vault'ta çalıştırılmadı (`[ORİJİNAL ÖRNEK · derlenmedi]`). API/sözdizimi resmi sayfalardan (php.net, nodejs.org, docs.python.org, nginx.org, httpd.apache.org, Microsoft Learn, freedesktop.org, Apple Developer Archive) doğrulandı; sürüm/dağıtım davranışı üretimde test edilmelidir.

---

## §0 Politika

- Bu dosyadaki kod, "çalışan" değil, "doğrulanmış resmi API'lerle çizilmiş" koddur: `verify-required`.
- Kod bloğu yazımı `isletim-sistemi-turu-kod-seti.md` §0–§4 (politika, sözçe, dürüstlük notu, yasaklılar, stil) ile birebir aynıdır.
- **Kode sahiplik yasağı:** SAPI tespiti, web sunucusu yapılandırması ve servis kaydı burada tanımlanır; `-ornekler` dosyasındaki konu (çalışma zamanı envanteri, SAPI bağlam notları, dil çalışma zamanı köprüleri) burada **tekrar yazılmaz** — yalnız Örnek/§ numarasıyla köprülenir.
- **Bilinen API sınırı:** Ortam tespiti (SAPI/process/sys), sunucu yapılandırma (Apache/nginx/IIS) ve servis kaydı (systemd/sc/launchd) bu dosyadır; PHP-FPM havuz ayarları (O4), kuyruk/işlem yöneticileri ve konteyner runtime'ları (Docker/Kubernetes) bu dosyada **değildir** (§6).
- **Kapsam dışı:** HTTP protokol kodu, ağ socket katmanı, dil sürümü kurulumu.

## §1 Çalışma sözleşmesi

- Her kod bloğu: dil yorum satırı + `[ORİJİNAL ÖRNEK · derlenmedi]` + resmi URL (ilk satır, tek başına).
- Sessiz fallback yok: uygulanamayan hücre `[TASARIM]` veya `UNKNOWN` + `⚠️ VERIFICATION REQUIRED` + §4 açık soru.
- macOS hücreleri `[TASARIM]` (Q4) — launchd blokları resmi Apple dokümanından çizildi ama vault'ta doğrulanmadı.
- Gövde içinde dosya yolu/URL/anahtar/tekrarlanan değişken adı yok.
- Paragraf 1–3 cümle; her teknik terim ilk geçtiği yerde en fazla 3 cümlede açıklanır (`SAPI`, `location eşleşmesi`, `binPath`).
- Yazım dili Türkçe; resmi API adları ve yapılandırma anahtarları İngilizce.
- Bridge köprüleri yalnız `-ornekler` dosyasının Örnek/§ numarasını işaret eder; içerik buraya kopyalanmaz.

## §2 Grup haritası

| GRUP | Konu | Soru | Dil kapsamı |
|---|---|---|---|
| GRUP A | SAPI/dil çalışma zamanı tespiti | Kod hangi ortamda çalışıyor (CLI, FPM, Apache, Node)? | PHP · Python · Node.js · C · C++20 · Rust · Go · C# · Zig · Swift · Java/Kotlin |
| GRUP B | Web sunucusu yapılandırma köprüsü | Kural hangi sunucuda nasıl yazılır? | Apache · nginx · IIS |
| GRUP C | Servis/daemon kaydı | Uygulama OS açılışında nasıl koşar? | bash · PowerShell · PHP |

**Bridge köprüleri:**

| Konu | Bu dosyada | Köprü |
|---|---|---|
| SAPI tespiti (PHP) | GRUP A | `runtime-ornekler.md` Örnek 1 (PHP/SAPI köprü) |
| Node çalışma zamanı | GRUP A | `runtime-ornekler.md` Örnek 2 (Node betik köprüsü) |
| Python çalışma zamanı | GRUP A | `runtime-ornekler.md` Örnek 3 (Python betik köprüsü) |
| Ortam envanteri | — | `runtime-ornekler.md` §2 (çalışma zamanı envanteri) |
| HTTP istek/yanıt akışı | — | `http-kod-seti.md` (GRUP A–C) |
| Ağ socket katmanı | — | `isletim-sistemi-cekirdek-kod-seti.md` |
| Dil sürümü envanteri | — | `dil-surumu-envanter-*` dosyaları |

---

## GRUP A · SAPI / dil çalışma zamanı tespiti

### A.1 PHP `php_sapi_name()` — hangi SAPI'de çalışıyor?

SAPI (Server Application Programming Interface), PHP betiğinin hangi kabuk içinde çalıştığını belirten arayüzdür: `cli` (satır sonu), `fpm-fcgi` (PHP-FPM), `apache2handler` (Apache), `cli-server` (dahili geliştirme sunucusu). Çıktı bu isimlerden biridir; sunucu/OS adı **değildir** (sunucu adı `php_uname()`, OS family `PHP_OS_FAMILY` — bkz. reserved.constants).

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://www.php.net/manual/en/function.php-sapi-name.php
// php_sapi_name(): betiğin çalıştığı SAPI adı (cli, fpm-fcgi, apache2handler, cli-server, cgi-fcgi).
// PHP_OS_FAMILY: Windows/Linux/Darwin ayrımı (reserved.constants) — SAPI adı bunu TAŞIMAZ.

declare(strict_types=1);

$sapi = php_sapi_name() ?? 'bilinmiyor';       // SAPI adı
$os   = PHP_OS_FAMILY;                          // Windows | Linux | Darwin | FreeBSD

switch ($sapi) {
    case 'cli':
    case 'phpdbg':
        // Satır sonu: iş programı, cron, kuyruk işçisi (runtime-ornekler Örnek 1 köprüsü)
        $mod = 'CLI';
        break;
    case 'fpm-fcgi':
    case 'cgi-fcgi':
    case 'apache2handler':
    case 'apache2filter':
    case 'litespeed':
        // Web: istek-yanıt bağlamı; hata raporu web kanalına yazılır
        $mod = 'WEB (' . $sapi . ')';
        break;
    case 'cli-server':
        // Dahili geliştirme sunucusu — production'da DEĞİLDİR (PHP 7.4+ notu)
        $mod = 'DEV-SERVER';
        break;
    default:
        // Bilinmeyen SAPI: sessiz varsayım YAPILMAZ (§1 dürüstlük)
        fwrite(STDERR, "Bilinmeyen SAPI: {$sapi} (OS: {$os})\n");
        exit(78);                                // EX_CONFIG benzeri: yapılandırma eksik
}

printf("mod=%s sapi=%s os=%s\n", $mod, $sapi, $os);
```

### A.2 Python `sys` modülü — çalışma zamanı tespiti

Python'da `sys.executable` yorumlayıcının yolunu, `sys.argv` komut satırını, `sys.platform` platform etiketini (`linux`, `win32`, `darwin`) verir. `sys.flags` bayrakları (örn. `optimize`, `bytes_warning`) yorumlayıcı yapılandırmasını taşır.

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://docs.python.org/3/library/sys.html
# sys.executable / sys.argv / sys.platform / sys.flags — çalışma zamanı tespiti (docs.python.org/3/library/sys).
# macOS [TASARIM]: 'darwin' etiketi varsayıldı, vault'ta doğrulanmadı (Q4).

import sys

def ortam_raporu() -> str:
    platform = sys.platform                      # 'linux' | 'win32' | 'darwin'
    if platform.startswith('linux'):
        os_ailesi = 'Linux'
    elif platform == 'win32':
        os_ailesi = 'Windows'
    elif platform == 'darwin':
        os_ailesi = 'Darwin'
    else:
        raise RuntimeError(f'Bilinmeyen platform: {platform}')   # sessiz fallback YOK

    mod = 'CLI' if sys.stdin.isatty() else 'NON-INTERACTIVE'      # terminal bağlı mı?
    return f"os={os_ailesi} platform={platform} mod={mod} py={sys.version.split()[0]}"

if __name__ == '__main__':                        # runtime-ornekler Örnek 3 köprüsü
    print(ortam_raporu())
```

### A.3 Node.js `process` nesnesi — çalışma zamanı tespiti

Node'da `process.platform` (`linux`, `win32`, `darwin`), `process.argv` (`argv[0]` yorumlayıcı, `argv[1]` betik yolu), `process.env` ortam değişkenleri ve `process.title` süreç adını verir. `process.env` **büyük/küçük harf duyarlı değildir** (Windows) ve değerler her zaman string'tir.

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://nodejs.org/api/process.html
// process.platform / process.argv / process.env / process.title — çalışma zamanı tespiti (nodejs.org/api/process).
// macOS [TASARIM]: 'darwin' etiketi varsayıldı, doğrulanmadı (Q4).

'use strict';

function ortamRaporu() {
  const platform = process.platform;                 // 'linux' | 'win32' | 'darwin'
  const osAilesi = ({ linux: 'Linux', win32: 'Windows', darwin: 'Darwin' })[platform];
  if (!osAilesi) {
    throw new Error(`Bilinmeyen platform: ${platform}`);   // sessiz fallback YOK (§1)
  }
  const betikYolu = process.argv[1] ?? '';           // argv[0]: node, argv[1]: betik
  const calismaModu = process.env.NODE_ENV ?? 'development';   // env: string | undefined
  return { os: osAilesi, platform, betik: betikYolu, mod: calismaModu };
}

if (require.main === module) {                       // runtime-ornekler Örnek 2 köprüsü
  console.log(ortamRaporu());
}

module.exports = { ortamRaporu };
```

### A.4 C — `isatty` + `INVOCATION_ID`

```c
// https://man7.org/linux/man-pages/man3/isatty.3.html
// [ORİJİNAL ÖRNEK · derlenmedi]
// tespit: stdin TTY mi + systemd koşusu mu (INVOCATION_ID)
#include <stdlib.h>
#include <unistd.h>

typedef enum { CTX_UNKNOWN, CTX_TTY, CTX_REDIRECTED, CTX_SYSTEMD } ctx_t;

ctx_t tespit_et(void) {
    const char *inv = getenv("INVOCATION_ID");   // systemd.unit(5) — kaynak:
                                                // https://man7.org/linux/man-pages/man5/systemd.unit.5.html
    if (inv != NULL && inv[0] != '\0') return CTX_SYSTEMD;
    int r = isatty(STDIN_FILENO);               // isatty(3): 1 = TTY, 0 = değil, -1 = hata
    if (r == 1) return CTX_TTY;
    if (r == 0) return CTX_REDIRECTED;
    return CTX_UNKNOWN;                         // fd geçersiz — sessiz varsayım YOK
}
```

**Ders:** `isatty` üç değerli (1/0/-1) — hata `REDIRECTED'a` düşürülmez, `CTX_UNKNOWN` ayrılır.

### A.5 C++20 — `std::getenv` + POSIX `isatty` köprüsü

```cpp
// https://en.cppreference.com/w/cpp/program/getenv
// [ORİJİNAL ÖRNEK · derlenmedi]
#include <cstdlib>
#include <unistd.h>

enum class Ctx { unknown, tty, redirected, systemd };

Ctx tespit() {
    if (const char* inv = std::getenv("INVOCATION_ID"); inv && *inv)
        return Ctx::systemd;                    // systemd.unit(5)
    const int r = isatty(STDIN_FILENO);         // POSIX — C++ standart kütüphanesinde yok;
                                                // kaynak: https://man7.org/linux/man-pages/man3/isatty.3.html
    if (r == 1) return Ctx::tty;
    if (r == 0) return Ctx::redirected;
    return Ctx::unknown;                        // hata — sessiz varsayım YOK
}
```

**Ders:** C++'ın `isatty`'si yoktur — POSIX `<unistd.h>` köprüsü kaçınılmazdır; `std::getenv` yalnız
env okur, TTY ayrımı C katmanından gelir. (Eski yanlış URL `/w/cpp/utility/getenv` → **404**;
doğru sayfa `/w/cpp/program/getenv`.)

### A.6 Rust — `IsTerminal` + `env::var`

```rust
// https://doc.rust-lang.org/std/io/trait.IsTerminal.html
// [ORİJİNAL ÖRNEK · derlenmedi]
use std::io::IsTerminal;

#[derive(Debug, PartialEq)]
enum Ctx { Unknown, Tty, Redirected, Systemd }

fn tespit() -> Ctx {
    // kaynak: https://doc.rust-lang.org/std/env/fn.var.html
    if std::env::var("INVOCATION_ID").is_ok_and(|v| !v.is_empty()) {
        return Ctx::Systemd;                    // systemd.unit(5)
    }
    match std::io::stdin().is_terminal() {
        true => Ctx::Tty,                       // std trait — çapraz platform
        false => Ctx::Redirected,
    }
}
```

**Ders:** `IsTerminal` std'dedir (3.1+) — `libc` bağımlılığı gerekmez; `env::var` hatası
(değişken yok) `is_ok_and` ile güvenli reddedilir.

### A.7 Go — `os.Stdin.Stat()` + `os.Getenv`

```go
// https://pkg.go.dev/os
// [ORİJİNAL ÖRNEK · derlenmedi]
package runtimectx

import "os"

type Ctx int

const (
	CtxUnknown Ctx = iota
	CtxTTY
	CtxRedirected
	CtxSystemd
)

func Tespit() Ctx {
	if id := os.Getenv("INVOCATION_ID"); id != "" { // systemd.unit(5)
		return CtxSystemd
	}
	fi, err := os.Stdin.Stat()                  // stdlib os — ek bağımlılık yok
	if err != nil {
		return CtxUnknown                       // stat hatası — sessiz varsayım YOK
	}
	if fi.Mode()&os.ModeCharDevice != 0 {
		return CtxTTY                           // karakter aygıtı = etkileşimli terminal
	}
	return CtxRedirected
}
```

**Ders:** Go'da `isatty` stdlib'de yok — `Stat` + `os.ModeCharDevice` idiomi kullanılır
(`golang.org/x/term` ek bağımlılık olurdu, R6 gereği eklenmedi).

### A.8 C# — `Console.Is*Redirected` + `Environment.GetEnvironmentVariable`

```csharp
// https://learn.microsoft.com/en-us/dotnet/api/system.console
// [ORİJİNAL ÖRNEK · derlenmedi]
enum Ctx { Unknown, Tty, Redirected, Systemd }

static Ctx Tespit()
{
    if (!string.IsNullOrEmpty(Environment.GetEnvironmentVariable("INVOCATION_ID")))
        return Ctx.Systemd;                    // systemd.unit(5) — .NET Unix'te erişilebilir

    if (Console.IsInputRedirected || Console.IsOutputRedirected)
        return Ctx.Redirected;                 // yönlendirilmiş akış

    return Ctx.Tty;                            // etkileşimli konsol varsayımı
}
```

**Ders:** `Console` bool döndürür — hata durumu yok; bu yüzden C/C++'taki `CTX_UNKNOWN` burada
yalnızca API dışı senaryolar (paket içi konsolsuz GUI process) için ayrılmıştır, erişilebilir değil.

### A.9 Zig — `posix.isatty` + `hasEnvVarConstant`

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
// [ORİJİNAL ÖRNEK · derlenmedi]
const std = @import("std");
const posix = std.posix;

pub const Ctx = enum { unknown, tty, redirected, systemd };

pub fn tespit() Ctx {
    // imza: std.process.hasEnvVarConstant — std/process.zig (ham kaynak)
    if (std.process.hasEnvVarConstant("INVOCATION_ID")) return .systemd;
    // isatty(handle: fd_t) bool — hata döndürmez; fd 0 = stdin
    return if (posix.isatty(0)) .tty else .redirected;
}
```

**Ders:** Zig POSIX çekirdeği hata döndürmediği için (`isatty: bool`) `.unknown` bu sürümde
erişilemez — bilinçli tutulur; API sürüm değişiminde (Zig std hızla evrilir) geri dönen yol olur.
Sürüm kanıtı: `ziglang.org/documentation/master` (std API'si master üzerinden doğrulandı).

### A.10 Swift — `isatty(0)` + `ProcessInfo.environment`

```swift
// https://developer.apple.com/documentation/foundation/processinfo
// [ORİJİNAL ÖRNEK · derlenmedi]
import Foundation

enum Ctx { case unknown, tty, redirected, systemd }

func tespit() -> Ctx {
    if let id = ProcessInfo.processInfo.environment["INVOCATION_ID"], !id.isEmpty {
        return .systemd                        // systemd.unit(5) — Linux/Foundation köprüsü
    }
    if isatty(0) == 1 { return .tty }          // Darwin/Glibc POSIX katmanı — Foundation'da
                                               // karşılığı yok; fd 0 = stdin
    return .redirected
}
```

**Ders:** Foundation `ProcessInfo` env'i verir; TTY ayrımı POSIX `isatty`'ye düşer
(Darwin/Glibc otomatik import). `.unknown` hata döndürmeyen POSIX yüzeyinde erişilemez, taşınır.

### A.11 Java/Kotlin — `System.console()` + `System.getenv`

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi]
enum Ctx { UNKNOWN, TTY, REDIRECTED, SYSTEMD }

static Ctx tespit() {
    String inv = System.getenv("INVOCATION_ID"); // systemd.unit(5)
    if (inv != null && !inv.isEmpty()) return Ctx.SYSTEMD;

    if (System.console() == null) return Ctx.REDIRECTED; // etkileşimli konsol yok
    return Ctx.TTY;
}
```

```kotlin
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html
// [ORİJİNAL ÖRNEK · derlenmedi]
enum class Ctx { UNKNOWN, TTY, REDIRECTED, SYSTEMD }

fun tespit(): Ctx {
    val inv = System.getenv("INVOCATION_ID")     // systemd.unit(5)
    if (!inv.isNullOrEmpty()) return Ctx.SYSTEMD
    return if (System.console() == null) Ctx.REDIRECTED else Ctx.TTY
}
```

**Ders:** `System.console() == null` hem yönlendirilmiş akış hem de konsolsuz ortam (IDE, cron)
verir — ayrım JVM katmanında mümkün değil; `UNKNOWN` bu belirsizliği taşır, `REDIRECTED`
ile örtbas edilmez (JVM doc: console "null when ... not associated with a terminal").

### A.13 SAPI tespiti köprü özet tablosu (genişletilmiş)

| Dil | Tespit anahtarı | Örnek değerler | OS ayrımı |
|---|---|---|---|
| PHP | `php_sapi_name()` | `cli`, `fpm-fcgi`, `apache2handler`, `cli-server` | `PHP_OS_FAMILY` |
| Python | `sys.platform` / `sys.executable` | `linux`, `win32`, `darwin` | `platform.startswith` |
| Node.js | `process.platform` / `process.argv` | `linux`, `win32`, `darwin` | eşleme tablosu |
| C | `isatty` + `getenv("INVOCATION_ID")` | `CTX_TTY`, `CTX_SYSTEMD`, `CTX_UNKNOWN` | üç değerli `isatty` (1/0/-1) |
| C++20 | `std::getenv` + POSIX `isatty` | `Ctx::tty`, `Ctx::systemd`, `Ctx::unknown` | C++'ta TTY yok — POSIX köprü |
| Rust | `IsTerminal` + `env::var` | `Ctx::Tty`, `Ctx::Systemd` | std trait, `libc` yok |
| Go | `os.Stdin.Stat` + `os.Getenv` | `CtxTTY`, `CtxSystemd`, `CtxUnknown` | `ModeCharDevice` idiomi |
| C# | `Console.Is*Redirected` + `Environment.GetEnvironmentVariable` | `Ctx::Redirected`, `Ctx::Systemd` | bool API — hata durumu yok |
| Zig | `posix.isatty` + `hasEnvVarConstant` | `.tty`, `.systemd` | hata dönmeyen POSIX çekirdeği |
| Swift | `isatty(0)` + `ProcessInfo.environment` | `.tty`, `.systemd` | Darwin/Glibc POSIX köprüsü |
| Java/Kotlin | `System.console()` + `System.getenv` | `TTY`, `SYSTEMD`, `REDIRECTED` | `console()==null` belirsizliği |

---

## GRUP B · Web sunucusu yapılandırma köprüsü

**Dürüstlük notu (genişletilmiş):** Bu GRUP üç ayrı yapılandırma dilini (Apache `.htaccess`/`mod_rewrite`, nginx `location`, IIS `web.config` XML) **eşdeğer kural** üzerinden çizer: "istek yolu → uygulama giriş noktası". Vault'ta hangi sunucunun production'da çalıştığı doğrulanmamıştır (O2, O3).

### B.1 Apache `mod_rewrite` — `.htaccess` kuralı

`mod_rewrite`, `.htaccess` içinde `RewriteRule`/`RewriteCond` ile istek yolunu yeniden yazar. `RewriteCond %{REQUEST_FILENAME} !-f` mevcut dosya değilse kuralı uygular (SPA fallback).

```apache
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html
# mod_rewrite: RewriteEngine + RewriteCond + RewriteRule — mevcut dosya değilse index.php'ye yönlendirme.
# macOS [TASARIM]: Apache 2.4 sözdizimi varsayıldı, doğrulanmadı (Q4).

RewriteEngine On
RewriteBase /

# Mevcut dosya/dizin DEĞİLSE → index.php'ye yönlendir (SPA fallback)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

### B.2 nginx `location` — eşdeğer kural

nginx `location` blokları yol eşleşmesi yapar: `^~` en uzun prefix'i zorlar, `~` regex (büyük/küçük harf duyarlı), `=` tam eşleşme. `try_files` dosya var mı diye bakar, yoksa son argümana (`$uri` → uygulama) düşer — Apache `!-f` deseninin karşılığı.

```nginx
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://nginx.org/en/docs/http/ngx_http_core_module.html
# ngx_http_core_module: try_files / location eşleşmesi — dosya yoksa uygulama girişine düşme.
# macOS [TASARIM]: nginx sürümü doğrulanmadı (Q4).

server {
    listen       80;
    server_name  localhost;
    root         /var/www/app/public;
    index        index.php;

    # Apache B.1 deseninin karşılığı: statik dosya varsa onu ver, yoksa uygulamaya ver
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM uç noktası (O4: havuz ayarları bu dosyanın KAPSAMI DIŞI)
    location ~ \.php$ {
        include       fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass  unix:/run/php/php-fpm.sock;    # socket yolu dağıtıma göre değişir (O4)
    }
}
```

### B.3 IIS `web.config` — eşdeğer kural

IIS yapılandırması `web.config` XML dosyasındadır; yeniden yazma kuralları `system.webServer` altındadır. Aşağıdaki `rewrite` alt bölümü **IIS Rewrite Module** gerektirir (ayrı kurulum — O2, `[TASARIM]`).

```xml
<!-- [ORİJİNAL ÖRNEK · derlenmedi]
     Kaynak: https://learn.microsoft.com/en-us/iis/configuration/system.webserver/
     system.webServer kökü: IIS yapılandırmasının ana bölümleri (defaultDocument, staticContent, httpErrors vb.).
     rewrite alt bölümü Rewrite Module gerektirir — modül kurulumu doğrulanmadı (O2, [TASARIM]).
     macOS [TASARIM]: bu blok IIS/Windows'tur (Q4). -->
<configuration>
  <system.webServer>
    <defaultDocument enabled="true">
      <files>
        <add value="index.php" />
      </files>
    </defaultDocument>
    <!-- [TASARIM] Rewrite Module (O2): eşdeğer kural Apache B.1 / nginx B.2 ile aynı:
         <rewrite>
           <rules>
             <rule name="app-fallback" stopProcessing="true">
               <match url="^(.*)$" />
               <conditions>
                 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
               </conditions>
               <action type="Rewrite" url="index.php" appendQueryString="true" />
             </rule>
           </rules>
         </rewrite> -->
  </system.webServer>
</configuration>
```

### B.4 Web sunucusu eşdeğerlik tablosu

| Kural | Apache | nginx | IIS |
|---|---|---|---|
| Yeniden yazma açık | `RewriteEngine On` | yerleşik (location) | `[TASARIM]` rewrite modülü (O2) |
| Dosya var mı kontrolü | `RewriteCond %{REQUEST_FILENAME} !-f` | `try_files $uri $uri/ /index.php?$query_string` | `matchType="IsFile" negate` |
| Fallback hedefi | `RewriteRule ^ index.php [L]` | `try_files $uri $uri/ /index.php?$query_string` | `<action type="Rewrite" url="index.php">` |
| PHP uç noktası | Apache PHP handler (SAPI A.1) | `fastcgi_pass` (O4) | `fastCgi` bölümü (O2) |
| Konu köprüsü | `runtime-ornekler.md` §2 | `runtime-ornekler.md` §2 | `runtime-ornekler.md` §2 |

---

## GRUP C · Servis / daemon kaydı

**Genel not:** Servis kaydı, uygulamanın OS açılışında (veya kullanıcının oturumunda) arka planda koşmasını sağlar. Üç OS üç farklı kayıt mekanizması kullanır: systemd (Linux), Windows Servis Kontrol Yöneticisi (SCM), launchd (macOS). PHP CLI betikleri (`runtime-ornekler.md` Örnek 1) GRUP A.1'de `cli` SAPI olarak tespit edilir; bu GRUP o betiği servise çevirir.

### C.1 systemd birim dosyası (Linux)

`systemd.service(5)` birim dosyası `[Unit]`/`[Service]`/`[Install]` bölümlerinden oluşur. `ExecStart` ilk argüman **mutlak yol** olmalıdır (systemd göreli yol kabul etmez). `systemctl` ile etkinleştirme/kontrol yapılır.

```ini
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://www.freedesktop.org/software/systemd/man/systemd.service.html
# systemd.service(5): [Unit]/[Service]/[Install] — ExecStart mutlak yol ister.
# systemctl(1): enable/start/status — bkz. https://www.freedesktop.org/software/systemd/man/systemctl.html
# macOS [TASARIM]: systemd Linux'tur (Q4).

[Unit]
Description=CoreMusic kuyruk isci
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=app
WorkingDirectory=/srv/app
ExecStart=/usr/bin/php /srv/app/bin/worker.php
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### C.2 `systemctl` komut sırası (Linux)

```bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://www.freedesktop.org/software/systemd/man/systemctl.html
# systemctl(1): daemon-reload → enable → start → status — birim kaydı ve denetimi sırası.
# macOS [TASARIM]: systemd Linux'tur (Q4).

set -eu
BIRIM="coremusic-worker.service"
# Birim dosyası /etc/systemd/system/${BIRIM} konumuna yerleştirilmelidir (önceki adım).
systemctl daemon-reload                       # birim dosyası değişikliğini yükle
systemctl enable "${BIRIM}"                   # açılışta otomatik başlat
systemctl start "${BIRIM}"                    # hemen başlat
systemctl --no-pager --full status "${BIRIM}" # durum denetimi (çıkış kodu ≠ 0 ise başarısız)
```

### C.3 Windows servis kaydı — `sc.exe create`

Windows SCM (Service Control Manager), `sc.exe` komutuyla servis kaydeder. `binPath=` servis çalıştırılacak yürütülebilir yoludur (boşluklu yollar tırnak içine alınır); `depend=` diğer servislere bağımlılığı belirtir.

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/sc-create
# sc create: binPath= (zorunlu), DisplayName, start= auto — servis kaydı.
# Alternatif: New-Service (Microsoft.PowerShell.Management) — https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.management/new-service
# macOS [TASARIM]: bu blok Windows'tur (Q4).

$ad        = 'CoreMusicWorker'
$betik     = 'C:\app\bin\worker.ps1'
$aciklama  = 'CoreMusic kuyruk isci'

# Yol 1: sc.exe (SCM doğrudan)
& sc.exe create $ad binPath= "powershell.exe -NoProfile -ExecutionPolicy Bypass -File `"$betik`"" start= auto DisplayName= $aciklama
if ($LASTEXITCODE -ne 0) { throw "sc create başarısız: $LASTEXITCODE" }   # sessiz fallback YOK

# Yol 2: New-Service (önerilen PowerShell API'si)
# New-Service -Name $ad -BinaryPathName "powershell.exe -NoProfile -File `"$betik`"" -DisplayName $aciklama -StartupType Automatic

# Denetim:
& sc.exe query $ad
```

### C.4 macOS launchd plist (iOS/macOS sistem başlatma)

launchd, macOS/iOS açılış servislerini `plist` dosyalarıyla kaydeder (`CreatingLaunchdJobs` dokümanı). `LaunchDaemons` (tüm kullanıcılar, root) ile `LaunchAgents` (kullanıcı oturumu) dizinleri ayrıdır (O5). `ProgramArguments` **dizi** olarak yazılır — ilk eleman yürütülebilir yol, sonrakiler argümandır.

```xml
<!-- [ORİJİNAL ÖRNEK · derlenmedi]
     Kaynak: https://developer.apple.com/library/archive/documentation/MacOSX/Conceptual/BPSystemStartup/Chapters/CreatingLaunchdJobs.html
     CreatingLaunchdJobs: Label + ProgramArguments (dizi) + RunAtLoad + KeepAlive — launchd plist anahtarları.
     Not: Apple dokümanı Archive statüsündedir; güncel sürüm doğrulanmadı (O7).
     Yükleme dizini: /Library/LaunchDaemons (tüm kullanıcılar) — O5 açık soru. -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
  "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>net.coremusic.worker</string>
    <key>ProgramArguments</key>
    <array>
        <string>/usr/bin/php</string>
        <string>/Users/app/bin/worker.php</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
    <key>KeepAlive</key>
    <true/>
    <key>StandardErrorPath</key>
    <string>/var/log/coremusic-worker.err.log</string>
</dict>
</plist>
```

### C.5 launchctl yükleme/denetim (macOS)

`launchd` plist dosyası yazıldıktan sonra `launchctl` ile yüklenir ve denetlenir. Apple `CreatingLaunchdJobs` dokümanı `launchctl load`/`unload` akışını anlatır; daha yeni `bootstrap`/`bootout` alt komutları vault'ta doğrulanmamıştır (`⚠️ VERIFICATION REQUIRED`, O7).

```bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://developer.apple.com/library/archive/documentation/MacOSX/Conceptual/BPSystemStartup/Chapters/CreatingLaunchdJobs.html
# CreatingLaunchdJobs: plist yükleme + launchctl ile denetim (load/unload akışı dokümanda anlatılır).
# bootstrap/bootout alt komutları doğrulanmadı — [TASARIM] (O7). macOS [TASARIM] (Q4).

set -eu
PLIST="/Library/LaunchDaemons/net.coremusic.worker.plist"   # dizin seçimi O5
LABEL="net.coremusic.worker"

# Yükleme: -w bayrağı kalıcı etkinleştirme yazar (doküman akışı)
launchctl load -w "${PLIST}"

# Denetim: yüklenmiş job listesinde etiket aranır
if launchctl list | grep -q "${LABEL}"; then
    printf 'durum: yuklu\n'
else
    printf 'durum: YUKLU DEGIL — plist/izin denetimi gerekir (O5)\n'
    exit 1
fi

# Kaldırma (ters işlem)
# launchctl unload -w "${PLIST}"
```

### C.6 Servis köprü özet tablosu

**Sıra kuralı:** Servis kaydı, uygulamanın kendi kurulumundan **sonra** yapılır; birim/servis dosyası yoksa `systemctl`/`sc`/`launchctl` komutları hata döner (sessiz fallback yok, §1). Servis olarak çalışan PHP/Node/Python betikleri GRUP A'da ilgili SAPI/platform ile tespit edilir — tespit edilemeyen ortamda servis kaydı yapılmaz.

| OS | Mekanizma | Kayıt | Denetim | Not |
|---|---|---|---|---|
| Linux | systemd | birim dosyası + `systemctl enable` | `systemctl status` | `ExecStart` mutlak yol (C.1) |
| Windows | SCM (`sc.exe` / `New-Service`) | `sc create binPath=` | `sc query` | `binPath=` tırnaklama (C.3) |
| macOS | launchd | plist + yükleme | `launchctl load` | Archive doküman (O7) |

---

## §3 Dil × OS matrisi

| Dil | Linux | Windows | macOS |
|---|---|---|---|
| PHP | GRUP A.1 (`cli`/`fpm-fcgi`) + C.1–C.2 (systemd worker) | GRUP A.1 + C.3 (sc/New-Service) | GRUP A.1 + C.4 (launchd) `[TASARIM]` (Q4) |
| Python | GRUP A.2 (`sys.platform`) | GRUP A.2 (`win32`) | GRUP A.2 (`darwin`) `[TASARIM]` (Q4) |
| Node.js | GRUP A.3 (`process.platform`) | GRUP A.3 (`win32`) | GRUP A.3 (`darwin`) `[TASARIM]` (Q4) |
| C | GRUP A.4 (`isatty` + `INVOCATION_ID`) | ❌ POSIX yüzey — Win32 sınıfı değil | GRUP A.4 (Darwin POSIX) `[TASARIM]` (Q4) |
| C++20 | GRUP A.5 (`std::getenv` + `isatty`) | ❌ POSIX yüzey — Win32 sınıfı değil | GRUP A.5 `[TASARIM]` (Q4) |
| Rust | GRUP A.6 (`IsTerminal` — std) | GRUP A.6 (std çapraz platform) | GRUP A.6 `[TASARIM]` (Q4) |
| Go | GRUP A.7 (`os.Stdin.Stat`) | GRUP A.7 (`ModeCharDevice` çapraz) | GRUP A.7 `[TASARIM]` (Q4) |
| C# | GRUP A.8 (.NET çapraz yüzey) | GRUP A.8 (`Console.Is*Redirected`) | GRUP A.8 `[TASARIM]` (Q4) |
| Zig | GRUP A.9 (`posix.isatty`) | ❌ POSIX yüzey — Win32 sınıfı değil | GRUP A.9 `[TASARIM]` (Q4) |
| Swift | GRUP A.10 (Glibc köprüsü) | ⚠️ CRT `_isatty` sınıfı — doğrulanmadı | GRUP A.10 (Darwin) `[TASARIM]` (Q4) |
| Java/Kotlin | GRUP A.11 (`System.console()`) | GRUP A.11 (JVM çapraz) | GRUP A.11 `[TASARIM]` (Q4) |
| Apache/nginx/IIS | GRUP B.1–B.2 (Apache, nginx) | GRUP B.3 (IIS `web.config`) | `[TASARIM]` — Apache/nginx macOS kurulumu (Q4) |

---

## §4 Açık sorular

- **O1:** Hangi SAPI kombinasyonları envanterde desteklenir (örn. `fpm-fcgi` + `apache2handler` birlikte mi, yalnız biri mi)? (`runtime-ornekler.md` Örnek 1 ile eşleştirilmeli.)
- **O2:** IIS `web.config` rewrite modülü kurulacak mı (ayrı kurulum, `[TASARIM]`)? Kurulacaksa modül sürümü ne olacak?
- **O3:** nginx `location` eşleşmesi (`^~` vs `~`) hangi proje kurallarıyla standardize edilir? (`runtime-ornekler.md` §2 envanteri ile eşleştirilmeli.)
- **O4:** PHP-FPM havuz yapılandırması (`pool.d/*.conf`) hangi vault dosyasının sahipliğinde?
- **O5:** launchd yükleme dizini: `/Library/LaunchDaemons` (tüm kullanıcılar, root) mu, `~/Library/LaunchAgents` (kullanıcı oturumu) mu?
- **O6:** Windows servis bağımlılıkları (`sc binPath=`, `depend=`) hangi proje standardında yazılır (tek bağımlılık mı, zincir mi)?
- **O7 (Q4 köprüsü):** macOS hücreleri `[TASARIM]` — launchd plist (`CreatingLaunchdJobs` Archive dokümanı) vault dışında doğrulanmadı.

## §5 Kaynakça

1. https://www.php.net/manual/en/function.php-sapi-name.php
2. https://www.php.net/manual/en/reserved.constants.php
3. https://nodejs.org/api/process.html
4. https://docs.python.org/3/library/sys.html
5. https://nginx.org/en/docs/http/ngx_http_core_module.html
6. https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html
7. https://learn.microsoft.com/en-us/iis/configuration/system.webserver/
8. https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/sc-create
9. https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.management/new-service
10. https://www.freedesktop.org/software/systemd/man/systemd.service.html
11. https://www.freedesktop.org/software/systemd/man/systemctl.html
12. https://developer.apple.com/library/archive/documentation/MacOSX/Conceptual/BPSystemStartup/Chapters/CreatingLaunchdJobs.html

> URL'ler 2026-10-14'te HTTP 200 ile doğrulandı. Liste dışı API kullanımı için self-fetch zorunlu.

## §6 İlişkili dosyalar

| Dosya | İlişki | Köprü |
|---|---|---|
| `isletim-sistemi-turu-kod-seti.md` | SSOT · §0–§4 sözçe · §GRUP C süreç | stil + süreç |
| `isletim-sistemi-runtime-kod-seti.md` (bu dosya) | SAPI tespiti + sunucu yapılandırması + servis kaydı | GRUP A/B/C |
| `isletim-sistemi-runtime-ornekler.md` | Konu köprüsü (Örnek/§ numaraları) | Örnek 1 · Örnek 2 · Örnek 3 · §2 |
| `isletim-sistemi-runtime-acik-mimari.md` | Mimari bağlam | §GRUP B/runtime kararları |
| `isletim-sistemi-api-kod-seti.md` | PHP FFI dosya köprüsü | §GRUP C.3 |
| `http-kod-seti.md` | HTTP istek/yanıt akışları | §GRUP A–C |
| `isletim-sistemi-cekirdek-kod-seti.md` | Ağ socket katmanı | §GRUP A |
| `dil-surumu-envanter-*` | Dil sürümü envanteri | — |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
