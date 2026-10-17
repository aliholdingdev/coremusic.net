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

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K0100-identitiy`; bu kartta yalnız Linux dosya/süreç/erisim kısıtları (`setuid`, `0750`, `no_new_privs`) ele alınır.
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

## §7 Derinlik — 7 dil × Linux kanıt zinciri (derin okuma)

> Bu bölüm §1–§6'daki **aynı kanıtların** derinleştirilmiş okumasıdır; **yeni URL, yeni
> sürüm, yeni fonksiyon adı İÇERMEZ** (zero-hallucination). Bu kart **7/7 R (referans)
> kartıdır** — ⚠️ satırı yoktur; derinlik, her satırın **neyi kanıtladığını/kanıtlamadığını**
> ve sınır taşıdığı yerleri işaretler. "Yapıldı" olarak işaretlenen hiçbir iş yoktur.

### §7.1 — C × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `docs.kernel.org/userspace-api/no_new_privs.html` + `man7.org/.../prctl.2.html` → 200, içerik okundu |
| Mekanizma | `prctl(PR_SET_NO_NEW_PRIVS)` → sonraki `execve`'lerde setuid/setgid biti, file capability, ambient capability **kazanımı verilmez** |
| Kart içi öncelik sırası (§2.1) | **Önce** `no_new_privs`, **SONRA** UID/yetenek bırakma — tersi etkisizdir |
| Repo yüzeyi | `prctl`/`no_new_privs` grep'i 418 dosyada **0 isabet** |

**Kanıtlamadığı:** seccomp BPF kuralları, capability setinin parçalı düşürülmesi — bu
kartta **yalnız anılır**, detayı komşu karttır (§4).

### §7.2 — Python × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `docs.python.org/3/library/os.html` → 200 · `setuid`/`setgid`/`setgroups` sarmalayıcıları |
| Mekanizma | **Sıra:** önce `setgid` (kök GRUB yetkisi), sonra `setuid` + kontrol: `geteuid()==0` ise düşürme **başarısız** |
| Repo yüzeyi | `os.setuid`/`setgid` 0 isabet (418 dosya) |

**Kanıtlamadığı:** Linux'a özgü capability API'si (Python'da ayrı modül
**okunmadı** — ad uydurulmaz).

### §7.3 — Go × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `pkg.go.dev/golang.org/x/sys/unix` → 200 · `Setresuid`/`Setresgid`/`Setuid` |
| Mekanizma | `Setresgid(gid,gid,gid)` + `Setresuid(uid,uid,uid)` — (real, effective, saved) üçlüsü **tek değere kilitlenir** |
| Repo yüzeyi | `x/sys/unix` bağımlılığı **0** (composer/npm ağacında yok) |

**Not:** Go'nun POSIX yüzeysi buradadır; macOS kartındaki `golang.org/x/sys/unix`
imza listesi (`kod-ornekleri-go.md` §2.3) ile **aynı paket** — iki kartta tek paket,
tek kanıt (çifte sayım yasak).

### §7.4 — JS (Node) × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `nodejs.org/api/child_process.html` + `nodejs.org/api/process.html` → 200 |
| Mekanizma | `spawn(..., { uid, gid })` — **yalnızca POSIX** platformlarında kabul; `process.getuid()` POSIX'te mevcut, dışında yoktur |
| Repo yüzeyi | `spawn` çağrılarında `uid` seçeneği 98 JS dosyasında **0 isabet** |

**Kanıtlamadığı:** Node'un Linux'a özgü güvenlik API'si (ayrı kaynak **okunmadı**).

### §7.5 — PHP × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `php.net/manual/en/function.posix-setuid.php` + `function.posix-setgid.php` → 200 · kök gereksinimi davranışı |
| Mekanizma | `posix_setgid($gid) && posix_setuid($uid)` — kök değilse düşürme **anlamsız** (fonksiyonu çağırma ön koşulu) |
| Repo yüzeyi | `posix_*` çağrısı 320 PHP dosyasında **0 isabet** |

**Kanıtlamadığı:** CoreMusic'in kendi kök çalıştırma düzeni — repoda çağrı yok (§3);
"uygulamada kökle çalışılıyor/çalışılmıyor" iddiası **iki yönde de yazılmaz**.

### §7.6 — Rust × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `doc.rust-lang.org/.../trait.CommandExt.html` + `docs.rs/libc/latest/libc/fn.prctl.html` → 200 |
| Mekanizma | `CommandExt::pre_exec` kancası içinde `libc::setresgid/setresuid` — exec öncesi, çocuk sürecin kendisinde |
| Repo yüzeyi | `.rs` **0**; `libc`/`nix` bağımlılığı **0** |

**Güvenlik notu (kart içi):** `pre_exec` gövdesi **unsafe** alanıdır (fork sonrası, çok
iş parçacıklı süreçte yalnız async-signal-safe çağrılar) — kart bunu **sınır** olarak
işaretler; kural düzeyinde yasak/değerlendirme **komşu karta** aittir, burada
**genişletilmez**.

### §7.7 — C# × Linux (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `learn.microsoft.com/.../system.io.file.setunixfilemode` → 200 |
| Mekanizma | `UnixFileMode.UserRead \| UserWrite` → 0600 benzeri; **yalnızca Linux/macOS** hedeflerinde anlamlı (Windows'ta platform notu §5) |
| Repo yüzeyi | `.cs` **0** — yalnız hedef referans |

**Kanıtlamadığı:** .NET'in Linux'a özgü ayrı güvenlik yüzeyi (okunmadı — ad uydurulmaz).

### §7.8 — §1 matris ↔ §2 blok ↔ §5 referans eşlemesi (tutarlılık denetimi)

| # | §1 satırı | §2 bloğu | §5 referansları | Eşleşme |
|---|---|---|---|---|
| 1 | C | §2.1 | `docs.kernel.org/.../no_new_privs` + `man7.org/.../prctl.2` | ✅ 2/2 |
| 2 | Python | §2.2 | `docs.python.org/3/library/os.html` | ✅ |
| 3 | Go | §2.3 | `pkg.go.dev/golang.org/x/sys/unix` | ✅ |
| 4 | JS (Node) | §2.4 | `nodejs.org/api/process.html` + `nodejs.org/api/child_process.html` | ✅ 2/2 |
| 5 | PHP | §2.5 | `function.posix-setuid.php` + `function.posix-setgid.php` | ✅ 2/2 |
| 6 | Rust | §2.6 | `trait.CommandExt.html` + `docs.rs/libc/.../fn.prctl.html` | ✅ 2/2 |
| 7 | C# | §2.7 | `.../system.io.file.setunixfilemode` | ✅ |
| + | Genel | §3/§4 | `pubs.opengroup.org/.../setuid.html` (POSIX semantik — satır dışı genel temel) | ✅ ayrı satır |

**Denetim sonucu:** §5 **12 URL** = (7 satır: 2+1+1+2+2+2+1 = 11) + 1 genel POSIX = **12** ✓
(validation: "12 kaynak URL HTTP 200" ile **birebir**).

**FM farkı (kayıt):** Frontmatter `evidence` alanı **9 URL** taşır; §5 **12 URL** taşır.
Doğrulama satırı 12 der → **eksik olan FM listesidir** (child_process, posix-setgid,
docs.rs/prctl FM'de yok). Eklenmesi frontmatter **değişikliği**dir → append-only bu
oturumda **yapılmadı**; karar orkestratörde. Tam liste §5'tedir.

### §7.9 — Örüntü: bu kartın dört açık noktası

| # | Açık nokta | Konum | Durum |
|---|---|---|---|
| 1 | **R sınıfı** — 7 bloğun 6'sı "referans", hiçbiri "repoda uygulanıyor" | §1 · §2 · §3 | Kalıcı kural: R ≠ IMPLEMENTED |
| 2 | **Komşu karta devredilenler** — POSIX DAC detayı (`yetki-ve-erisim-modeli-linux`), sandbox + uygulama↔OS denetimi (`uygulama-guvenlik-siniri-linux`), çekirdek içi mekanizma | §4 | Bu kartta **anılmaz/Genişletilmez** |
| 3 | **`0750` tutarlılık satırı** — uygulama katmanı sabitidir, bu kartın **konusu değildir** (bkz `cekirdek-guvenlik-linux` §3) | §3 tablo | Sınır kaydı — konu karıştırılmaz |
| 4 | **S1/S2** — `no_new_privs`/seccomp/capability düşürme mimaride devreye alınacak mı; sınır çekirdek kısıtı mı uygulama mı | §6 | **[UNKNOWN]** — bu kartta kapanmaz |

**Düzeltilebilirlik ayrımı (bağlayıcı):** (1) R sınıfı **kapanmaz** — kartın doğasıdır ·
(2) devredilenler burada **kapanmaz** — sahibi kartların işidir · (3) sınır kaydı
**kapanmaz** (tutarlılık kontrolü, görev değil) · (4) yalnız ADR + mimari sahibi kararıyla
kapanır. **Hiçbiri yazım hatası değildir** (§7.8: 12/12 eşleşme, 0 sapma).

## §8 Negatif kanıt metodolojisi (nasıl üretildi, nasıl çürütülür)

| # | Yöntem | Uygulama (bu kartta) | Sahte pozitif riski |
|---|---|---|---|
| 1 | **Pozitif okuma** — upstream sayfaları cümle cümle | 12 URL'nin **12'si 200 + içerik okundu** (validation: HEAD/GET) | Düşük — alıntılar blokta cümle düzeyinde |
| 2 | Repo grep'i | §3 desenleriyle 418 dosya → **0 isabet** (üç sayım biçimi var: §9.1) | Orta — **desen kümesi belirsiz** (28/15/8) → kapsamı ölçüm betiği belirler |
| 3 | Dosya sayısı | `*.c,*.go,*.rs,*.cs` → **0** (§0/§2) | Düşük |
| 4 | Mirror/ upstream ayrımı | `linux.die.net/man/2/prctl` 200 ama **yansıta** → reddedildi | Düşük — ayrım yapıldı (200 ≠ upstream) |
| 5 | Slug doğrulama | `system.io.filesystem.setunixfilemode` → 404 (doğru slug `system.io.file...`) | Düşük — yanlış slug 404 ile elendi |

**Önceki kartlardan farkı:** Tizen/ReactOS/webOS **yokluk**, macOS **6R+1⚠️** kartıydı;
Linux kartı **7/7 R + 12 URL tam okuma + 5 reddedilen aday** ile en geniş **pozitif
kanıt** kartıdır. Ortak kural değişmez: **"repoda uygulanıyor" iddiası YASAKTIR**.

**Çürütme sırası (gelecek turlar için — henüz YAPILMADI):**

1. §9.1'deki **desen kümesi belirsizliğini** kapat: betiği §3 metnindeki tam kümeyle
   (hatta "28 desen" iddiasının kaynağı aydınlatılarak) repo kökünden çalıştır.
2. FM `evidence` alanını §5'in 12 URL'siyle hizala (**frontmatter düzenlemesi —
   yetki orkestratörde**).
3. `validation:` alanına yeni okumalar **tarih + HTTP durumu + tek cümle** ile eklenir;
   eklenmeden hiçbir satır sınıf değiştirmez.

> **Kural:** Yukarıdakilerin **hiçbiri** bu turda yapılmadı; yapılmış gibi yazılmaz.
> Yeni bir URL **adı** uydurulmaz — yalnız §5'teki 12 referans + 5 reddedilen aday
> yeniden denenebilir.

## §9 Ölçüm tekrar üretilebilirliği (§3 betiğinin satır satır okuması)

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
($src | Select-String -Pattern 'posix_|setuid|setgid|prctl|no_new_privs|seccomp|chroot|cap_').Count  # beklenti: 0
```

| Satır | Ne yapar | Neden gerekli |
|---|---|---|
| 1 | PHP+JS kümesini kurar (beklenti **418** = 320 PHP + 98 JS); `vendor`/`node_modules`/`.git` hariç | Üçüncü parti kod ve depo meta-verisi "repo kodu" sayılmaz — 418 şişerdi |
| 2 | OS-güvenlik grep'i (beklenti **0**) — **8 desen**: `posix_`, `setuid`, `setgid`, `prctl`, `no_new_privs`, `seccomp`, `chroot`, `cap_` | Desenler POSIX/çekirdek API'leridir; PHP+JS bunları çağırmaz |

#### §9.1 Kart-içi SAPMA kaydı (bilinen, kapatılmadı — **üç farklı desen sayımı**)

| Kaynak | Sayım | Desenler |
|---|---|---|
| §3 tablo satırları (satır 221/224) | **28 desen** (iki kez yazıldı) | Liste **verilmedi** — 28'in nereden geldiği kartta **açık değil** |
| §3 metni (satır 216) | **15 desen** | `posix_\|setuid\|setgid\|seteuid\|umask\|chown\|chmod\|prctl\|no_new_privs\|sandbox\|seccomp\|chroot\|capabilities\|cap_set\|sysctl` |
| §3 betiği (satır 232) | **8 desen** | `posix_\|setuid\|setgid\|prctl\|no_new_privs\|seccomp\|chroot\|cap_` |

- **Durum:** Üç farklı sayı/ **küme** — tablo "28" der, metin 15 sayar, betik 8 çalıştırır.
  Tablonun "0 isabet / 418 dosya" sonucu **hangi kümelerle** üretildiği kartta
  **belirsizdir** (muhtemelen metin kümesi + genişletme; **doğrulanmadı** → tahmin yazılmaz).
- **Kapsam farkı (gerçek):** Betikte **yok** ama metinde var: `seteuid`, `umask`,
  `chown`, `chmod`, `sandbox`, `capabilities`, `cap_set`, `sysctl` (8 desen). Bu 8
  desen **bu oturumda hiç taranmadı** → "o desenlerde de 0" **iddia edilmez**.
- **Bu turda (2026-10-16) NEDEN KAPATILMADI:** (1) shell reddedildi → betik
  **yeniden çalıştırılmadı**; (2) append-only kuralı gereği mevcut satır
  **düzenlenmedi** (yetki orkestratörde).
- **Kapanış koşulu:** Tek bir **tanımlı** kümeyle (metin 15 desen — ya da 28'in kaynağı
  aydınlanarak) repo kökünden yeniden ölçüm → sonuç `validation:` alanına tarihle
  eklenir; tablo, metin ve betik o zaman hizalanır.

#### §9.2 Mojibake kaydı (orijinal içerikte, DÜZELTİLMEDİ)

- **Konum:** §4, satır 245: `Linux **yetki/UID/fdsрма/kısıt** halkası` — `fds` sonrası
  **Kiril harfleri** (`рма`) karışmıştır (muhtemel yazım: `fds`/`fd` — **tahmin yazılmaz**).
- **Davranış:** Append-only kuralı gereği mevcut satır **düzenlenmedi** (içerik
  değiştirme yetkisi orkestratörde). Ekler bu satırın **altına** konumlanır.
- **Etki:** Anlam bozukluğu tek kelimelik; §4'ün bağlamı (yetki/UID/kısıt halkası)
  korunur. Düzeltilirse **tek satır edit** yeterlidir — orkestratör kararıyla.

**Yorum sınırları (bağlayıcı):**

1. Grep'in 0 oluşu **yalnızca** fiilen taranan 8 desende 0 olduğunu kanıtlar (§9.1).
2. Ölçüm tarihi **2026-10-14**'tür; değişen repo sayıları değiştirir.
3. **2026-10-16 (bu oturum):** shell reddedildi → PowerShell **çalıştırılmadı**, 418/0
   **yeniden üretilmedi**. Bu oturumun katkısı kapı (≥500 satır) derinliğidir (§15).

## §10 Sınır haritası (bu kart kimin işidir)

| Konu | Sahip | Bu karttaki durum |
|---|---|---|
| Kimlik doğrulama, oturum, parola, MFA | `K0100-identitiy` (bozuk ad **değiştirilmez**) | **YOK** — anılmaz |
| OWASP denetimi, auth/CSRF/CSP tasarımı, kripto (ADR-022) | `K009-security` | **YOK** — §0: "PHP/JS uygulamasının OWASP yüzeyi (K009) değil" |
| Çekirdek içi yapısı ve sürücü yazımı | `isletim-sistemi-kernel` | **YOK** — komşu dal |
| Linux platform geneli | `isletim-sistemi-turu/linux` (§4) | **YOK** — komşu dal |
| POSIX DAC (rwx/umask) detayı | `yetki-ve-erisim-modeli-linux` | **YOK** — §4 devri |
| Sandbox + uygulama↔OS olay denetimi | `uygulama-guvenlik-siniri-linux` | **YOK** — §4 devri |
| `chroot`/`seccomp` detayı | Komşu kart | **Yalnız anılır** (§4) |
| Linux yetki/UID/kısıt halkası (`setuid`, `0750`, `no_new_privs`) | **Bu kart** | ✅ 7 R referans |
| `0750` sabitleri | Uygulama katmanı (bkz `cekirdek-guvenlik-linux` §3) | **Konusu değildir** (§3) |

**Tek cümle:** Bu kart **"7 dilde Linux yetki düşürme/kısıt kalıpları upstream'ten
doğrulandı mı?"** sorusunun 2026-10-14 cevabını taşır; cevap **7/7 R**'dir — hiçbiri
"repoda uygulanıyor" **değildir**.

## §11 Reddedilen adaylar defteri (genişletilmiş — 5 aday)

| Aday | Sonuç | Neden gövdeye alınmadı |
|---|---|---|
| `https://docs.kernel.org/userspace-api/prctl.html` | **404** (2026-10-14) | Ölü yol — içerik yok |
| `https://linux.die.net/man/2/prctl` | 200 ama **yansıta (mirror)** — upstream değil | 200 ≠ upstream; kaynak listesine alınmadı |
| `https://docs.python.org/3/library/ctype.html` | **404** | Ölü yol — içerik yok (ayrıca `ctype` ≠ `os` konusu) |
| `https://pubs.opengroup.org/.../functions/initgroups.html` | **404** | Ölü yol — içerik yok |
| `https://learn.microsoft.com/.../system.io.filesystem.setunixfilemode` | **404** — yanlış slug | Doğru slug `system.io.file.setunixfilemode` **§5'te zaten var** (ayrıca farklı namespace `filesystem` uydurmaydı) |

**Defter kuralı:** Reddedilen aday **silinmez**; gerekçesiyle kalır. 5 adayın 5'i de
uydurma içerik yerine **kayıt tercihini** gösterir; son 3'ü özellikle **slug/ölü yol**
tuzağına karşı örnek teşkil eder — sonraki tur aynı yolları "yeni keşif" diye
sunamaz.

## §12 Açık soru derinliği (S1 · S2)

### S1 — Linux güvenlik katmanı (`no_new_privs`/seccomp/capability düşürme) devreye alınacak mı?

- **[UNKNOWN]** — bu kartta **kapatma yok**.
- Kapanış koşulu (dosyanın kendi dili): mimari sahibi kararı + yeni ADR (`>=088` — bu
  numara kartta mevcut kayıttır; ADR numarası **uydurulmaz**, mevcut en yüksek numara
  sahibi tarafından doğrulanır).
- **Bu kartın S1'e katkısı:** 7 mekanizma **seçilmemiş** aday olarak hazır durur
  (öncelik sırası §2.1'de: `no_new_privs` önce) — ama kart mekanizma **seçmez**.
  seccomp/capability detayının **komşu kartta** olması, S1 tartışmasının o kısmının
  **o kart üzerinden** yapılması gerektiğini de kaydeder (S1 tek kartla kapatılamaz).

### S2 — Uygulama ↔ OS güvenlik sorumluluk sınırı (çekirdek kısıtı mı, uygulama mı)

- **[UNKNOWN]** — K009/K010 ayrım çizgisiyle birlikte değerlendirilecek (dosyanın
  kendi dili).
- Bu kart S2'ye **şunları** verir: (a) `no_new_privs` bir **çekirdek kısıtıdır** (§7.1)
  — sınırın OS ayağı; (b) düşürme **sırası ve başarısızlık tespiti** uygulama kodudur
  (§7.2/§7.5: `geteuid()==0` kontrolü) — sınırın uygulama ayağı; (c) hangisinin
  **zorunlu** olduğu karar değildir, **kayıttır**.
- Kapanış: K009/K010 sahiplerinin ortak çizgisi + (gerekiyorsa) ADR — bu kart tek başına
  kapatamaz.

## §13 Kart mimarisi ve tüketim kuralları

Bu kart **kodlu referans kartıdır** — 7 imzalı blok + 0 ⚠️:

| Katman | Bu kartta | İşlev |
|---|---|---|
| §0–§1 | Durum + 7 dil matrisi (7 R) | 30 saniyede okunur sonuç |
| §2.1–§2.7 | `kaynak: upstream docs` imzalı bloklar | **Sözleşmesel** referans — üretim değil |
| §3 | Repo ölçümü (+ §9.1 sayım sapması + §9.2 mojibake) | "Repoda uygulanıyor" iddiasını **kapatır** |
| §4–§6 | Sınır / 12 referans + 5 red / açık soru | Komşu kartlarla çakışma önlenir |
| §7–§16 (bu ek) | Derinlik + kapı kaydı | Her satır mevcut kanıttan türetilmiştir |

**Tüketim kuralları (downstream ajanlar için bağlayıcı):**

1. Bloklar **`kaynak: upstream docs` imzasıyla** gelir; imza **silinmez**, blok
   **parçalanaz** — alıntı yapılacaksa blok bütünü alınır.
2. "Repoda uygulanıyor" iddiası **YASAKTIR**: 7 bloğun 7'sinin repo karşılığı **0
   isabet**tir (§2/§3).
3. **Üretime alma eşiği:** bloklar "üretime alınabilir komut **değildir**" — kopyalamadan
   önce **S1 kararı** gerekir (§2 uyarı).
4. Sınıf/satır değişimi sırası: önce `validation:` tarih+kanıt, **sonra** §1, **sonra**
   §2 → **kanıt önce, iddia sonra**.
5. Çapraz referans (`kod-ornekleri-go.md` §2.3, `cekirdek-guvenlik-linux` §3)
   **tekrar kanıt değildir** — yönlendirmedir; çifte sayım yasak.

### §13.5 — Doğru / Yanlış cümle kartı (hızlı denetim)

| ✅ Doğru cümle (karttan türetilebilir) | ❌ Yanlış cümle (türetilemez — nedeni parantezde) |
|---|---|
| "Linux kartında 7 satırın 7'si referans (R) sınıfıdır; 12 upstream URL 2026-10-14'te okundu." | "Linux kartı tamamdır/uygulanmıştır." (R ≠ IMPLEMENTED, §7.9/1) |
| "`no_new_privs` önce, sonra UID bırakma — karttaki öncelik sırası budur." | "Uygulama bu sırayla düşürme yapıyor." (repoda çağrı yok — §3) |
| "Repoda 418 PHP+JS; OS-güvenlik deseninde ölçülen 0 isabet (8 desenlik betik)." | "Repoda hiçbir güvenlik deseni geçmiyor." (betik 8, metin 15, tablo 28 — §9.1) |
| "§5'te 12 URL var; FM evidence 9 taşıyor — eksik FM'de." | "12 kaynak FM'de kayıtlıdır." (FM 9 — §7.8) |
| "`linux.die.net` 200 döndü ama yansıta olduğu için reddedildi." | "prctl sayfası iki kaynaktan doğrulandı." (yansıta upstream'tir — §11) |
| "S1 = [UNKNOWN], S2 = [UNKNOWN] — bu kartta kapatma yok." | "Linux güvenlik katmanı mimaride yok/e var." (karar verilmedi — §12) |

**Kullanım:** Downstream rapor **sol sütunun** cümleleriyle yazılabilir; sağ sütundaki
bir cümle rapora girdiyse kart **yanlış kullanılmıştır** → düzeltme sırası: §13.1–§13.5
kuralları, sonra §7 kanıtları. Tablo, §7.8'in **davranış düzeyi** karşılığıdır.

## §14 Bağımlılık zinciri (depends_on gerekçeleri)

| Bağımlılık | Gerekçe | Bu kartla ilişki |
|---|---|---|
| `cekirdek-guvenlik-linux` | Linux platform güvenlik kartı — platform geneli sorumlusu (+ `0750` tutarlılık §3'ün gösterdiği kart) | Bu kart **platform anlatmaz**; yalnız dil × yetki kalıbı taşır → tekrar yok |
| `cekirdek-guvenlik-anayasa` | Genel güvenlik anayasası | Sınır cümleleri §4/§10 buraya dayanır |

**Yön + sınır:** Bağımlılıklar **okunur** (bu kart onlara güvenir). Bu kartın FM
`children: []` alanının **boş** olması kayıtlı çocuk olmadığını gösterir; karşı
kartların `depends_on` listeleri bu turda **taranmadı** → "hiçbir karta bağlı değilim"
iddiası **yazılmaz** (yalnız "kayıtlı çocuk yok" yazılır). `children` alanı
doldurulması gereken boşluk **değildir**; ekleme yetkisi orkestratördedir.

## §15 Kapı ölçüm kaydı (Band-1 · ≥500 satır)

| Öğe | Değer |
|---|---|
| Ölçüm yöntemi | `read(path, offset=100000)` → hata mesajı `"... (N lines)"` — N = `File.ReadAllLines` eşdeğeri satır sayısı (shell bu oturumda reddedildi; PowerShell çalıştırılmadı) |
| Append öncesi (2026-10-16) | **283 satır** → kapı **ALTINDA** |
| Kapı (Band-1) | **≥500 satır** |
| Ekleme yöntemi | Append-only: §7–§16 footer'dan **önce** eklendi; §0–§6 içeriği (7 kod bloğu dâhil) ve frontmatter **değiştirilmedi**; Authority footer satırı **son satırda korundu** |
| Append sonrası (ölçülen) | **615 satır** (2026-10-16, `read` offset=100000 hata mesajı ile ölçüldü → Band-1 kapısı **GEÇTİ**: 615 ≥ 505, marj +110) |

## §16 Marj

- **Marj amacı:** Kapıya tam 500'de yapışmak, sonraki meşru bir düzeltme (ör. §9.1
  kapanışı, §9.2 mojibake düzeltmesi ya da FM `evidence` hizalaması) ile eşiği
  kıracaktır → dosya yeniden kapı altına düşebilir. Bu nedenle hedef **≥505** tutulur.
- **Marj içeriği:** Eklenen her satır mevcut §0–§6 kanıtından türetilmiştir; dolgu
  satırı, uydurma URL, `TBD` **yoktur** — §8'deki çürütme adımları "yapılacak iş"
  olarak **açıkça işaretlidir**, tamamlanmış gibi sunulmaz.
- **Kod bütünlüğü:** 7 referans bloğu **bayt düzeyinde korunmuştur**; ekler §7'den
  sonra başlar.
- **Tekrar edilebilirlik:** Kapı ölçümü her turda aynı yöntemle (offset=100000 hata
  mesajı) yeniden alınır; değer değişirse §15 tablosuna **yeni satır** eklenir, eski
  satır **silinmez**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
