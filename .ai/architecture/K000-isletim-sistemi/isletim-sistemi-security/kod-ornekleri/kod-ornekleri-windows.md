---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-windows
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects
  - https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesystemaccessrule
  - https://pkg.go.dev/golang.org/x/sys/windows
  - https://nodejs.org/api/os.html
  - https://www.php.net/manual/en/function.chmod.php
  - https://docs.python.org/3/library/os.html
  - https://doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html
validation:
  - "2026-10-14: 7 kaynak URL GET 200 ile dogrulandi; 7 dil satirinin tamami dogrulanmis upstream tasir; repo olcusu 418 PHP+JS / 0 *.c,*.go,*.cs / grep 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Windows (7 Dil)

## §0 ELI15

Bu kart, **Windows'ta 7 dil bölümünde** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını taşır: iş (job) nesnesi ile süreç kapanışı, dosya paylaşım bayrakları,
ACL kural nesnesi, platform ayrımı. Hepsi `# kaynak: upstream docs` imzalı
**sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**, Windows güvenlik API isabeti **0** (negatif kanıt, §3).
> Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

Doğrulama: 7 upstream URL bugün (2026-10-14) GET 200 + içerik okundu; reddedilen adaylar §5'te.

## §1 OS güvenlik profili × dil matrisi

| Dil | Windows güvenlik yüzeyi | Bu dosyadaki mekanizma | Upstream sınıfı |
|---|---|---|---|
| C | Win32 iş (job) nesnesi | `CreateJobObject` + kapanınca öldürme sınırı | learn.microsoft.com (win32) |
| Python | `os` modülü Windows notu | `chmod` → yalnız read-only bayrağı (ACL değil) | docs.python.org |
| Go | `golang.org/x/sys/windows` | `AssignProcessToJobObject` — süreç↔iş ataması | pkg.go.dev |
| JS (Node) | `os.platform()` ayrımı | `'win32'` platform kapısı | nodejs.org/api |
| PHP | `chmod` Windows notu | oktal `0600` + read-only bayrağı sınırı | php.net |
| Rust | `OpenOptionsExt` | `share_mode(0)` — paylaşıma kapatma | doc.rust-lang.org |
| C# | .NET ACL API'si | `FileSystemAccessRule` (FileSystemRights + AccessControlType) | learn.microsoft.com (dotnet) |

**Okuma:** 7 satırın tamamı 2026-10-14'te upstream dokümanlardan doğrulanmıştır; hiçbir satır
"repoda uygulanıyor" iddiası **taşımez** — hepsi hedef referanstır (§3).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 7 bölümün **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (üretime alınabilir komut **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — C: iş nesnesi ile süreç kapanışı (referans)

```c
/* kaynak: upstream docs - learn.microsoft.com/en-us/windows/win32/procthread/job-objects */
#include <windows.h>

/* CreateJobObject: is nesnesi olusturur; AssignProcessToJobObject ile surec
   atandiktan sonra ILISKI KIRILAMAZ (upstream: association "cannot be broken").
   SetInformationJobObject ile JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE konursa son is
   handle'i kapandiginda alt surecler de sonlandirilir (upstream: kill-on-close). */
HANDLE cm_job_create(void)
{
    return CreateJobObject(NULL, NULL);
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.c` 0 dosya; `CreateJobObject`/`AssignProcessToJobObject` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.2 — Python: `chmod` Windows'ta yalnız read-only bayrağı (referans)

```python
# kaynak: upstream docs - docs.python.org/3/library/os.html
import os

# os.html Windows notu: bu islev Windows'ta "only affects whether the read-only
# flag is set" — POSIX rwx anlami yoktur; tam ACL (DACL) yuzeyi Python standart
# kutuphanesinde DEGILDIR.
os.chmod("session.dat", 0o600)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.chmod` çağrısı 0 isabet (§3) |

### §2.3 — Go: `x/sys/windows` ile iş↔süreç ataması (referans)

```go
// kaynak: upstream docs - pkg.go.dev/golang.org/x/sys/windows (v0.49.0 - fonksiyon listesi)
package main

import "golang.org/x/sys/windows"

// v0.49.0 fonksiyon listesinde DOGRULANAN imzalar: AssignProcessToJobObject,
// SetInformationJobObject, OpenProcessToken, AdjustTokenPrivileges,
// DuplicateTokenEx, CryptProtectData, CreateProcessAsUser
func assignToJob(process windows.Handle, job windows.Handle) error {
    // Win32 sozlesmesi: atama sonrasi is-surec iliski kirilamaz
    return windows.AssignProcessToJobObject(job, process)
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya; `x/sys/windows` bağımlılığı 0 isabet (2026-10-14) |

### §2.4 — JS (Node.js): platform ayrımı (referans)

```js
// kaynak: upstream docs - nodejs.org/api/os.html
const os = require('node:os');

// os.platform() Windows'ta 'win32' dondurur (upstream deger kumesi:
// aix|darwin|freebsd|linux|openbsd|sunos|win32; 'android' deneysel notlu)
if (os.platform() === 'win32') {
  // Windows'a ozel guvenlik yolu burada AYRILIR; guvenlik kararinin kendisi
  // bu kartin konusu degil (S1 acik)
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.platform()` ile `'win32'` karşılaştırması 98 JS dosyasında 0 isabet |

### §2.5 — PHP: oktal `chmod` + Windows sınırı (referans)

```php
<?php
// kaynak: upstream docs - php.net/manual/en/function.chmod.php
declare(strict_types=1);

// php.net uyari: oktal sayi "must be prefixed with 0" (0640/0755 ornekleri);
// Windows user note: bu platformda chmod yalnızca read-only bayragini etkiler.
chmod('/var/lib/coremusic/session.dat', 0600);
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `chmod(` çağrısı 320 PHP dosyasında 0 isabet (2026-10-14) |

### §2.6 — Rust: dosya paylaşım bayrağı (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html
use std::fs::OpenOptions;
use std::os::windows::prelude::*;

// share_mode(0): acikken baska sureclerin okuma/yazma/silmesini engelle
// (varsayilan: FILE_SHARE_READ|FILE_SHARE_WRITE|FILE_SHARE_DELETE)
let file = OpenOptions::new().write(true).share_mode(0).open("session.dat")?;
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.rs` 0 dosya; `share_mode` grep'i 0 isabet (2026-10-14) |

### §2.7 — C#: `FileSystemAccessRule` (referans)

```csharp
// kaynak: upstream docs - learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesystemaccessrule
using System.Security.AccessControl;

// upstream ozellikleri: FileSystemRights + AccessControlType (ACL kural nesnesi)
var rule = new FileSystemAccessRule(@"BUILTIN\Users", FileSystemRights.Read,
                                     AccessControlType.Deny);
// kural bir ACL'ye uygulanir; ACL uygulama kodu bu repoda YOK (§3)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — C# kaynak yok (`.cs` 0 dosya); yalnız hedef referans |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`CreateJobObject|AssignProcessToJobObject|SetInformationJobObject|AdjustTokenPrivileges|
DuplicateTokenEx|CryptProtectData|FileSystemAccessRule|share_mode|os.platform|chmod`
(2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| Windows güvenlik API çağrısı (job/ACL/token) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'CreateJobObject|FileSystemAccessRule|share_mode').Count  # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. Windows OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır — §2'deki hiçbir blok
"repo'da uygulanıyor" iddiası **taşımez**.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız Windows dosya/süreç/erişim kalıpları (iş nesnesi, paylaşım bayrağı, ACL kuralı) ele alınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; Windows platform geneli → `isletim-sistemi-turu/windows` (§4.3).

Ek sınır: token/imtiyaz (`AdjustTokenPrivileges`, `DuplicateTokenEx`) bu kartta **yalnız
anılır** — detayı `yetki-ve-erisim-modeli-windows` kartıdır. DACL düzenleme akışı C#
örneğinde kural nesnesi düzeyinde durur; uygulama-katmanı dosya erişim kontrolü K010'dur.
Süreç ataması "sandbox" diye adlandırılması abartıdır (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | `CreateJobObject` · `AssignProcessToJobObject` (atama kırılamaz) · `SetInformationJobObject` · `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` · breakaway bayrakları | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesystemaccessrule | `FileSystemAccessRule` — `FileSystemRights` + `AccessControlType` özellikleri | HTTP 200 · içerik okundu |
| https://pkg.go.dev/golang.org/x/sys/windows | v0.49.0 fonksiyon listesi: `AssignProcessToJobObject`, `SetInformationJobObject`, `OpenProcessToken`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `CreateProcessAsUser` | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer kümesi (`win32` dahil; `android` deneysel) | HTTP 200 · içerik okundu |
| https://www.php.net/manual/en/function.chmod.php | oktal `0` öneki zorunlu; 0600/0644/0750 örnekleri; Windows read-only user note | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/os.html | `chmod` Windows'ta yalnız read-only bayrağını etkiler | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html | `share_mode` varsayılanı; `security_qos_flags` named-pipe impersonation uyarısı | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `learn.microsoft.com/en-us/windows/win32/secauthz/protecting-objects`
> → **404** (2026-10-14) · `learn.microsoft.com/en-us/dotnet/api/system.io.filesystem.setunixfilemode`
> → **404** (doğru slug `system.io.file.setunixfilemode`, bkz `kod-ornekleri-linux.md` §5).
> İkisi gövdeye alınmadı; ACL yüzeyi yalnız `filesystemaccessrule` sayfasından taşındı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Windows güvenlik katmanı (iş nesnesi/ACL/token) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (ACL mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode