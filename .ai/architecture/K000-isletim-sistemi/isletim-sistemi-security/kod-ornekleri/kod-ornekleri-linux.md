---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-linux
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://docs.kernel.org/userspace-api/no_new_privs.html
  - https://www.man7.org/linux/man-pages/man2/prctl.2.html
  - https://docs.python.org/3/library/os.html
  - https://pkg.go.dev/golang.org/x/sys/unix
  - https://nodejs.org/api/process.html
  - https://www.php.net/manual/en/function.posix-setuid.php
  - https://doc.rust-lang.org/std/os/unix/process/trait.CommandExt.html
  - https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/setuid.html
validation:
  - "2026-10-14: 12 kaynak URL HTTP 200 ile dogrulandi (HEAD/GET); 5 aday URL reddedildi (bkz §5); repo 418 PHP+JS dosya olcumu OS guvenlik deseninde 0 isabet (negatif kanit, §3)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Linux (7 Dil)

## §0 ELI15

Bu kart, Linux'ta **yetki düşürme, ayrıcalık kazanımını kapatma ve dosya
izinlerini POSIX dilinde** ifade eden 7 dil bölümü taşır: C (`prctl` /
`PR_SET_NO_NEW_PRIVS`), Python (`os.setuid`/`os.setgid`), Go
(`golang.org/x/sys/unix`), Node.js (`child_process` uid/gid), PHP
(`posix_*`), Rust (`CommandExt::pre_exec` + `libc`), C#
(`File.SetUnixFileMode`). Hepsi **işletim sistemi katmanıdır** — PHP/JS
uygulamasının OWASP yüzeyi (K009) değil.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS dosya,
> `vendor`/`node_modules`/`.git` hariç) Linux OS-güvenlik desenine **0
> isabet** vardır: `prctl`/`setuid`/`setgid`/`seccomp`/`chroot`/`capset`
> çağrısı **yoktur** (negatif kanıt, §3). Bu kart dolayısıyla **hedef
> mimari + negatif kanıt** kartıdır; §2'deki 7 bölümün tamamı
> `kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır,
> repoda karşılığı yoktur. S1 ve S2 açık sorudur, bu kartta kapatılmaz (§6).

## §1 OS güvenlik profili × dil matrisi

| Dil | Linux güvenlik yüzeyi | Bu dosyadaki mekanizma | Upstream sınıfı |
|---|---|---|---|
| C | çekirdek `prctl` sistém çağrısı | `PR_SET_NO_NEW_PRIVS` — sonraki `execve`'te setuid/capability kazanımını kapatır | docs.kernel.org (kernel.org = upstream) |
| Python | `os` modülü POSIX sarmalayıcısı | `setgid` → `setuid` sıralı yetki düşürme | docs.python.org |
| Go | `x/sys/unix` (Linux build tag'i) | `Setresgid`/`Setresuid` — gerçek/sakıncalı/effektif UID üçlüsü | pkg.go.dev |
| JS (Node) | `child_process` spawn seçeneği | alt süreci `uid`/`gid` ile düşük yetkiyle doğurma | nodejs.org/api |
| PHP | POSIX uzantısı (`posix_*`) | `posix_setgid` + `posix_setuid` (kök UID'de) | php.net |
| Rust | `libc` + `CommandExt` | `pre_exec` içinde `setresgid`/`setresuid` | doc.rust-lang.org + docs.rs |
| C# | .NET POSIX dosya izni API'si | `File.SetUnixFileMode` (0600 benzeri) | learn.microsoft.com (yalnız C#) |

**Okuma:** 7 satırın tamamı 2026-10-14'te upstream dokümanlardan
doğrulanmıştır; hiçbir satır "repoda uygulanıyor" iddiası **taşımez** —
hepsi hedef referanstır (§3).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 7 bölümün **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans
> kalıplarıdır (üretime alınabilir komut **değildir**); repo'da karşılığı
> §3'te negatif kanıt olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — C: `prctl(PR_SET_NO_NEW_PRIVS)` (referans)

```c
/* kaynak: upstream docs - docs.kernel.org/userspace-api/no_new_privs.html */
/* PR_SET_NO_NEW_PRIVS sonraki execve'leri kapsar: setuid/setgid biti,
   file capability ve ambient capability kazanimi artik verilmez. */
#include <sys/prctl.h>
#ifndef PR_SET_NO_NEW_PRIVS
#define PR_SET_NO_NEW_PRIVS 38   /* asm-generic/unistd.h degeri */
#endif
int start_sandbox(void) {
    if (prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0) != 0) return -1;
    /* oncelik sirasi: once no_new_privs, SONRA uid/yetenek birakma */
    return 0;
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `prctl`/`no_new_privs` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.2 — Python: sıralı yetki düşürme (referans)

```python
# kaynak: upstream docs - docs.python.org/3/library/os.html
import os

def drop_privileges(uid: int, gid: int) -> None:
    # sira onemli: once gid (kök GRUB yetkisi), sonra uid
    os.setgid(gid)          # os.setgroups() de ayni modulde belgelenir
    os.setuid(uid)
    if os.geteuid() == 0:
        raise RuntimeError("yetki dusurme basarisiz")
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.setuid`/`setgid` 0 isabet (418 dosya, 2026-10-14) |

### §2.3 — Go: `x/sys/unix` ile gerçek UID üçlüsü (referans)

```go
// kaynak: upstream docs - pkg.go.dev/golang.org/x/sys/unix
package main

import "golang.org/x/sys/unix"

func dropPrivileges(uid, gid int) error {
    // Setresgid/Setresuid: (real, effective, saved) ayni degere kilitlenir
    if err := unix.Setresgid(gid, gid, gid); err != nil {
        return err
    }
    return unix.Setresuid(uid, uid, uid)
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `x/sys/unix` bağımlılığı 0 isabet (composer/npm ağacında yok) |

### §2.4 — JS (Node.js): alt süreci düşük yetkiyle doğurma (referans)

```js
// kaynak: upstream docs - nodejs.org/api/child_process.html
// kaynak: upstream docs - nodejs.org/api/process.html
const { spawn } = require('node:child_process');

// spawn options: uid/gid — yalnizca POSIX platformlarinda kabul edilir
const child = spawn('/usr/bin/id', [], { uid: 65534, gid: 65534 });
if (typeof process.getuid === 'function' && process.getuid() === 0) {
  console.error('UYARI: koku UID 0 ile calisiyor');   // POSIX disinda bu yok
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `spawn` çağrılarında `uid` seçeneği 0 isabet (98 JS dosyası) |

### §2.5 — PHP: `posix_setgid` + `posix_setuid` (referans)

```php
<?php
// kaynak: upstream docs - php.net/manual/en/function.posix-setuid.php
// kaynak: upstream docs - php.net/manual/en/function.posix-setgid.php
declare(strict_types=1);

function cm_drop_privileges(int $uid, int $gid): bool
{
    if (!function_exists('posix_setgid') || posix_geteuid() !== 0) {
        return false; // kök değilse dusurme anlamsiz
    }
    return posix_setgid($gid) && posix_setuid($uid);
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `posix_*` çağrısı 320 PHP dosyasında 0 isabet (2026-10-14) |

### §2.6 — Rust: `CommandExt::pre_exec` (referans)

```rust
// kaynak: upstream docs - doc.rust-lang.org/std/os/unix/process/trait.CommandExt.html
// kaynak: upstream docs - docs.rs/libc/latest/libc/fn.prctl.html
use std::process::Command;
use std::os::unix::process::CommandExt;

fn build_child(cmd: &str, uid: u32, gid: u32) -> Command {
    let mut c = Command::new(cmd);
    unsafe {
        c.pre_exec(move || {
            if libc::setresgid(gid, gid, gid) != 0 { return Err(std::io::Error::last_os_error()); }
            if libc::setresuid(uid, uid, uid) != 0 { return Err(std::io::Error::last_os_error()); }
            Ok(())
        });
    }
    c
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — Rust ağaç yok (`.rs` 0 dosya); `libc`/`nix` bağımlılığı 0 |

### §2.7 — C#: `File.SetUnixFileMode` (referans)

```csharp
// kaynak: upstream docs - learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode
// yalnizca Linux/macOS hedeflerinde anlamlidir (Windows'ta platform notu §5)
using System.IO;

var mode = UnixFileMode.UserRead | UnixFileMode.UserWrite;   // 0600 benzeri
File.SetUnixFileMode("/var/lib/coremusic/session", mode);
// hedef: yalnizca sahibi okuyabilsin; grub/diger okuma kapali
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — C# kaynak yok (`.cs` 0 dosya); yalnız hedef referans |

## §3 CoreMusic repo durumu

Tarama: repo kökü (`shared` + `home.coremusic.net` + diğer ağaçlar),
`*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; desen =
`posix_|setuid|setgid|seteuid|umask|chown|chmod|prctl|no_new_privs|sandbox|seccomp|chroot|capabilities|cap_set|sysctl`
(2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| Ayrıcalık düşürme çağrısı (`setuid`/`setgid`/`posix_*`) | grep 28 desen | **YOK** — 418 dosya / 0 isabet (negatif kanıt) |
| Çekirdek kısıtı (`prctl`/`no_new_privs`/`seccomp`/`chroot`) | grep | **YOK** (negatif kanıt) |
| Dosya izni kalıbı (`chmod`/`umask`/`setfacl`) | grep | **YOK** (negatif kanıt) |
| Linux OS-güvenlik yüzeyi (toplam) | grep 28 desen | **0 isabet / 418 dosya** (negatif kanıt) |
| Dosya izni hedef referansı — POSIX DAC | tutarlılık kontrolü | `0750` sabitleri uygulama katmanıdır (bkz `cekirdek-guvenlik-linux` §3) — bu kartın **konusu değildir** |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
($src | Select-String -Pattern 'posix_|setuid|setgid|prctl|no_new_privs|seccomp|chroot|cap_').Count  # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. Linux OS-güvenlik yüzeyi bu
repoda **hiç başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır —
§2'deki hiçbir blok "repo'da uygulanıyor" iddiası **taşımez**.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız Linux dosya/süreç/erisim kısıtları (`setuid`, `0750`, `no_new_privs`) ele alınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; Linux platform geneli → `isletim-sistemi-turu/linux`.

Ek sınır: bu kart Linux **yetki/UID/fdsрма/kısıt** halkasını taşır; POSIX DAC
(rwx/umask) detayı `yetki-ve-erisim-modeli-linux`, sandbox ve uygulama↔OS olay
denetimi `uygulama-guvenlik-siniri-linux` kartlarındadır. §2'deki 7 blok
**kullanım modelidir**; çekirdek içi uygulama mekanizması bu dala yazılmaz.
`chroot`/`seccomp` burada yalnız anılır — detayı komşu karta aittir.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.kernel.org/userspace-api/no_new_privs.html | `PR_SET_NO_NEW_PRIVS`; sonraki execve'te setuid/capability kazanımının kapanması | HTTP 200 · içerik okundu |
| https://www.man7.org/linux/man-pages/man2/prctl.2.html | `prctl` argümanları; `PR_SET_NO_NEW_PRIVS` değeri | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/os.html | `os.setuid`/`os.setgid`/`os.setgroups` sarmalayıcıları | HTTP 200 · içerik okundu |
| https://pkg.go.dev/golang.org/x/sys/unix | `Setresuid`/`Setresgid`/`Setuid` UNIX sarmalayıcıları | HTTP 200 · içerik okundu |
| https://nodejs.org/api/process.html | `process.getuid()` — POSIX'te mevcut | HTTP 200 · içerik okundu |
| https://nodejs.org/api/child_process.html | `spawn` seçenekleri (`uid`/`gid`) | HTTP 200 · içerik okundu |
| https://www.php.net/manual/en/function.posix-setuid.php | `posix_setuid` davranışı ve kök gereksinimi | HTTP 200 · içerik okundu |
| https://www.php.net/manual/en/function.posix-setgid.php | `posix_setgid` | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/unix/process/trait.CommandExt.html | `pre_exec` — exec öncesi ayrıcalık düşürme kancası | HTTP 200 · içerik okundu |
| https://docs.rs/libc/latest/libc/fn.prctl.html | `libc::prctl` Rust bağlantısı | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode | `File.SetUnixFileMode` — POSIX dosya izni (Linux/macOS) | HTTP 200 · içerik okundu |
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/setuid.html | POSIX `setuid` semantiği (gerçek/effectif/saved) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):**
> `https://docs.kernel.org/userspace-api/prctl.html` → **404** (2026-10-14);
> `https://linux.die.net/man/2/prctl` → HTTP 200 ama **yansıta (mirror), upstream
> değil** → kaynak listesine alınmadı; `https://docs.python.org/3/library/ctype.html`
> → **404**; `https://pubs.opengroup.org/onlinepubs/9699919799/functions/initgroups.html`
> → **404**; `https://learn.microsoft.com/en-us/dotnet/api/system.io.filesystem.setunixfilemode`
> → **404** (doğru slug `system.io.file.setunixfilemode`).

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Linux güvenlik katmanı (`no_new_privs`/seccomp/capability düşürme) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (çekirdek kısıtı mı, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
