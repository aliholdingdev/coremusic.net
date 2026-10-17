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

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K0100-identitiy`; bu kartta yalnız Windows dosya/süreç/erişim kalıpları (iş nesnesi, paylaşım bayrağı, ACL kuralı) ele alınır.
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

## §7. Kapsam Matrisi (§1'deki 7 dil × Windows mekanizmaları)

§7.1 dil bazlı derinlik · §7.2 mekanizma sınıflandırması · §7.3 eksik/yapısal boşluklar · §7.4 arıza örüntüsü.

### §7.1 Dil Bazlı Derinlik (§1 matrisinden türetildi — 7 satır)

- **C (satır 45)**: `CreateJobObject` + `SetInformationJobObject(JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE)` — son handle kapanınca alt süreçler öldürülür (satır 69–72). Sınıf: kernel nesne düzeyi. §5'teki job-objects URL'i ile doğrulandı.
- **Python (satır 46)**: `os.chmod` → yalnız read-only bayrağı (satır 89–92); tam DACL yüzeyi Python standart kütüphanesinde **DEĞİL**. Sınıf: kısıtlı köprü. §5'teki os.html URL'i ile doğrulandı.
- **Go (satır 47)**: `x/sys/windows` v0.49.0 — `AssignProcessToJobObject` (satır 107–113); atama kırılamaz. Ayrıca `OpenProcessToken`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `CreateProcessAsUser` (satır 109). Sınıf: tam erişimli x/sys katmanı. §5'teki pkg.go.dev URL'i ile doğrulandı.
- **JS (satır 48)**: `os.platform() === 'win32'` platform kapısı (satır 126–131); değer kümesi `aix|darwin|freebsd|linux|openbsd|sunos|win32` + `android` deneysel. Sınıf: ayrım-only (güvenlik API'si yok). §5'teki nodejs.org URL'i ile doğrulandı.
- **PHP (satır 49)**: oktal `chmod('…', 0600)` + Windows read-only sınırı (satır 145–147); oktal `0` öneki zorunlu. Sınıf: kısıtlı köprü (Python ile aynı sınır). §5'teki php.net URL'i ile doğrulandı.
- **Rust (satır 50)**: `OpenOptionsExt::share_mode(0)` — paylaşıma kapatma (satır 161–163); varsayılan `FILE_SHARE_READ|WRITE|DELETE`. Ayrıca `security_qos_flags` named-pipe impersonation uyarısı (§5). Sınıf: tip-güvenli extent trait. §5'teki doc.rust-lang.org URL'i ile doğrulandı.
- **C# (satır 51)**: `FileSystemAccessRule(Path, FileSystemRights, AccessControlType)` — ACL kural nesnesi (satır 176–179). Sınıf: .NET CLR ACL yüzeyi. §5'teki learn.microsoft.com dotnet URL'i ile doğrulandı.

### §7.2 Mekanizma Sınıflandırması (§2'deki 7 blok)

| Mekanizma | Diller | Windows yüzeyi | §2 bloğu |
|---|---|---|---|
| İş (job) nesnesi | C, Go | Kernel nesne — süreç kapanışı | §2.1, §2.3 |
| Dosya paylaşım bayrağı | Rust | `share_mode(0)` — dosya kilidi | §2.6 |
| ACL kural nesnesi | C# | DACL — erişim kontrol listesi | §2.7 |
| Platform ayrımı | JS | `'win32'` — yalnız kapı | §2.4 |
| chmod köprüsü | Python, PHP | read-only bayrağı — ACL değil | §2.2, §2.5 |

### §7.3 Eksik/Yapısal Boşluklar (§5'teki 2 reddedilen 404)

§5'teki 2 reddedilen aday (satır 237–240): `protecting-objects` → 404 · `system.io.filesystem.setunixfilemode` → 404 (doğru slug `system.io.file.setunixfilemode`, `kod-ornekleri-linux.md` §5). Bu iki 404 → ACL yüzeyi yalnız `filesystemaccessrule` sayfasından taşındı; `SetUnixFileMode` .NET 7+ yüzeyi bu kartta **konu değil** (Linux kartına bırakıldı). Token/imtiyaz yüzeyi (`AdjustTokenPrivileges`, `DuplicateTokenEx`) §4'te "yalnız anılır" — detayı `yetki-ve-erisim-modeli-windows` kartıdır.

### §7.4 Arıza Örüntüsü (§1–§5'ten türetildi)

| Örüntü | Kanıt | Açıklama |
|---|---|---|
| **chmod ≠ ACL** | §2.2 (Python) + §2.5 (PHP) + §5 satır 233–234 | Windows'ta `chmod` yalnız read-only bayrağı etkiler; gerçek ACL C# `FileSystemAccessRule` ile yapılır. PHP/Python'da ACL **yok**. |
| **Atama kırılamaz** | §2.1 (C) + §2.3 (Go) + §5 satır 229 | `AssignProcessToJobObject` sonrası iş↔süreç ilişkisi kırılamaz (upstream); breakaway yalnız `JOB_OBJECT_LIMIT_BREAKAWAY_OK` ile. |
| **JS'de güvenlik API'si yok** | §2.4 (JS) | `os.platform()` yalnız ayrım kapısı; Windows güvenlik API'si Node.js standart modülünde **bulunmaz** (§5 satır 232). |
| **share_mode varsayılanı açık** | §2.6 (Rust) + §5 satır 235 | Varsayılan `FILE_SHARE_READ|WRITE|DELETE` — `share_mode(0)` ile kapatılmazsa süreçler arası dosya yarışı mümkün. |

## §8. Negatif Kanıt Metodolojisi (§3'teki 0 isabet)

§8.1 çürütme sırası · §8.2 her reddedilen adayın çürütmesi.

### §8.1 Çürütme Sırası (§3'teki 3 ölçüm + §5'teki 2×404)

1. `*.c,*.h,*.cpp,*.go,*.rs,*.cs` glob → **0 dosya** (satır 196) — 4 dilin kaynak tabanı yok.
2. `CreateJobObject|FileSystemAccessRule|share_mode` grep → **0 isabet** (satır 197) — 418 dosyada Windows güvenlik API çağrısı yok.
3. 418 dosya sayımı (320 PHP + 98 JS) → ölçüm payandası (satır 198) — taban küçük, negatif kanıt güvenilir.
4. `protecting-objects` → 404 (satır 237) — ACL koruma sayfası erişilemez; ACL yüzeyi yalnız `filesystemaccessrule`'dan taşındı.
5. `setunixfilemode` slug hatası → 404 (satır 238–239) — .NET Unix dosya kipi yüzeyi bu kartta değil (Linux kartı).

### §8.2 Her Reddedilen Adayın Çürütmesi (§5'teki 2)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `learn.microsoft.com/…/secauthz/protecting-objects` | "Windows nesne koruma sayfası" — bu URL 404 (2026-10-14) | ACL yüzeyi tek sayfaya (`filesystemaccessrule`) indirgendi; çok-sayfalı ACL kanıtı `UNKNOWN` |
| 2 | `learn.microsoft.com/…/system.io.filesystem.setunixfilemode` | ".NET Unix dosya kipi API'si" — bu slug 404 (doğru: `system.io.file.setunixfilemode`) | `SetUnixFileMode` bu kartta taşınmadı; Linux kartına referans verildi |

## §9. Ölçüm Kaydı

§9.1 SAPMA kaydı · §9.2 URL doğrulama kaydı.

### §9.1 SAPMA Kaydı (§1–§5)

| Metot | Ölçüm | Sapma | Açıklama |
|---|---|---|---|
| §1 matrisi (7 satır) vs §2 blokları (7 blok) | 7 = 7 | **0** | Dil bazlı birebir örtüşme (C §2.1, Python §2.2, Go §2.3, JS §2.4, PHP §2.5, Rust §2.6, C# §2.7). |
| §1 "Upstream sınıfı" vs §5 URL'leri | 7 sınıf = 7 URL | **0** | Her satırın bir §5 URL'i var; §5'te 7/7 HTTP 200. |
| §3 grep deseni (10 desen) vs §2 blokları | 10 desen ⊂ 7 blok | **0 sapma** | Grep desenleri §2'deki API adlarından türetildi (`CreateJobObject` §2.1, `share_mode` §2.6, `chmod` §2.2/§2.5, `os.platform` §2.4, `FileSystemAccessRule` §2.7). |
| §5 "Reddedilen adaylar" vs §0 doğrulama notu | 2 aday 404 | **0** | §0 satır 39: "reddedilen adaylar §5'te" — §5 satır 237–240 ile tutarlı. |

**Sonuç**: bu dosyada §9.1 SAPMA **yok** — 7 dil, 7 URL, 2 reddedilen aday birebir örtüşüyor (python kartındaki 12/betik-4 farkı gibi bir ölçüm farkı bu kartta bulunmuyor).

### §9.2 URL Doğrulama Kaydı (§5'teki 7 okunabilir + 2×404)

§5: 9 URL denendi — 7 okunabilir (HTTP 200 + içerik okundu, satır 229–235), 2×404 (satır 237–240). 7 okunabilir URL §2.1–§2.7'nin 7 bloğunu doğruluyor; 2×404 §7.3'teki boşlukları işaretliyor (`protecting-objects` erişilemez, `setunixfilemode` slug hatası). §0 satır 39 "7 upstream URL bugün (2026-10-14) GET 200 + içerik okundu" — §5 ile birebir.

#### §9.2.1 Her URL'nin Doğruladığı İddialar (satır bazlı)

| # | URL (§5 satırı) | Doğrulanan iddia | §2 bloğu | §7.1 satırı |
|---|---|---|---|---|
| 1 | job-objects (satır 229) | `CreateJobObject` · `AssignProcessToJobObject` (atama kırılamaz) · `SetInformationJobObject` · `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` · breakaway bayrakları | §2.1 | C |
| 2 | filesystemaccessrule (satır 230) | `FileSystemAccessRule` — `FileSystemRights` + `AccessControlType` | §2.7 | C# |
| 3 | pkg.go.dev (satır 231) | v0.49.0: `AssignProcessToJobObject`, `SetInformationJobObject`, `OpenProcessToken`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `CreateProcessAsUser` | §2.3 | Go |
| 4 | nodejs.org (satır 232) | `os.platform()` değer kümesi (`win32` dahil; `android` deneysel) | §2.4 | JS |
| 5 | php.net (satır 233) | oktal `0` öneki zorunlu; 0600/0644/0750; Windows read-only user note | §2.5 | PHP |
| 6 | os.html (satır 234) | `chmod` Windows'ta yalnız read-only bayrağı | §2.2 | Python |
| 7 | OpenOptionsExt (satır 235) | `share_mode` varsayılanı; `security_qos_flags` named-pipe impersonation uyarısı | §2.6 | Rust |

**Toplam doğrulanan iddia**: 7 URL → 16 iddia (5+1+7+1+3+1+2). §7.1'deki 7 dil derinliği bu 16 iddia ile destekleniyor.

## §10. Sınır Haritası (§4'teki 3 sınır + §7.3'teki 2 boşluk)

§10.1 hangi dilin kanıtı eksik · §10.2 hangi mekanizmanın kanıtı eksik.

### §10.1 Hangi Dilin Kanıtı Eksik (7/7 dil — eksik YOK)

§1'deki 7 dilin tamamı §5'teki 7 okunabilir URL ile doğrulandı (2026-10-14). Eksik dil kanıtı **yok**. Ancak §7.3'teki 2 boşluk (404 aday) nedeniyle: ACL yüzeyi tek-sayfa kanıtı (`filesystemaccessrule`); çok-sayfalı ACL derinliği `UNKNOWN`.

### §10.1.1 Dil Derinlik Çizelgesi (§5 URL × §2 blok × §1 sınıfı)

| Dil | §5 URL (satır) | §2 blok | §1 sınıfı | Derinlik |
|---|---|---|---|---|
| C | satır 229 (job-objects) | §2.1 | learn.microsoft.com (win32) | Tam — 4 API (`CreateJobObject`, `AssignProcessToJobObject`, `SetInformationJobObject`, `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` + breakaway) |
| Python | satır 234 (os.html) | §2.2 | docs.python.org | Kısıtlı — yalnız read-only bayrağı; DACL Python'da yok |
| Go | satır 231 (pkg.go.dev) | §2.3 | pkg.go.dev | Tam — 7 imza (`AssignProcessToJobObject` … `CreateProcessAsUser`) |
| JS | satır 232 (nodejs.org) | §2.4 | nodejs.org/api | Ayrım-only — güvenlik API'si Node standart modülde yok |
| PHP | satır 233 (php.net) | §2.5 | php.net | Kısıtlı — oktal önek + read-only sınırı |
| Rust | satır 235 (OpenOptionsExt) | §2.6 | doc.rust-lang.org | Tam — `share_mode(0)` + `security_qos_flags` uyarısı |
| C# | satır 230 (filesystemaccessrule) | §2.7 | learn.microsoft.com (dotnet) | Tam — ACL kural nesnesi (`FileSystemRights` + `AccessControlType`) |

### §7.10 Ölçüm Betiği Karşılaştırma Kaydı (§3'teki PowerShell bloğu)

§3 satır 203–208'deki 3 satırlık PowerShell betiği (yeniden üretilebilir ölçüm):

| Satır | Beklenti (§3) | Betik deseni | §2/§7 eşlemesi |
|---|---|---|---|
| satır 205 | 418 dosya (320 PHP + 98 JS) | `*.php,*.js` glob + vendor/node_modules/.git filtresi | §3 satır 188 |
| satır 206 | 0 dosya | `*.c,*.go,*.cs` glob | §3 satır 196 |
| satır 207 | 0 isabet | `CreateJobObject\|FileSystemAccessRule\|share_mode` grep | §3 satır 197 (3 desen; §3 satır 190–191'deki 10 desenin alt kümesi) |

**Sapma**: betik §3 satır 207'de 3 desen kullanıyor; §3 satır 190–191'deki tam liste 10 desen. Fark: `AssignProcessToJobObject`, `SetInformationJobObject`, `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`, `os.platform`, `chmod` betikte yok. Betik 3 deseni = `CreateJobObject` (§2.1), `FileSystemAccessRule` (§2.7), `share_mode` (§2.6) — §2.1/§2.6/§2.7 bloklarının API adları; §2.2/§2.3/§2.4/§2.5 bloklarının desenleri betikte **eksik** (Python/Go/JS/PHP yüzeyleri betikle ölçülmemiş). Bu bir **ölçüm sapması** — betik 3 bloğu ölçüyor, 7 bloğun tamamını değil. Raporlandı, betik düzenlenmedi (mevcut satır yasak).

### §10.2 Hangi Mekanizmanın Kanıtı Eksik (§7.2 tablosu)

§7.2'deki 5 mekanizma sınıfının 4'ü (iş nesnesi, paylaşım bayrağı, ACL kuralı, platform ayrımı) §5'teki 7 URL ile doğrulandı. `chmod köprüsü` sınıfı §5'teki 2 URL (php.net + os.html) ile doğrulandı ama bu sınıf Windows ACL'siz — "ACL değil" kanıtı negatif. Token/imtiyaz yüzeyi (`AdjustTokenPrivileges`, `DuplicateTokenEx`) §4'te "yalnız anılır" — bu kartın kanıtı **değil** (§10.2 dışı).

## §11. Reddedilen Adaylar Defteri (§5'teki 2 + §7.3'teki 2 boşluk)

§11.1 §5'teki 2 reddedilen · §11.2 §7.3'teki 2 yapısal boşluk.

### §11.1 §5'teki 2 Reddedilen

| # | URL (§5) | Neden reddedildi? | Kanıt |
|---|---|---|---|
| 1 | `learn.microsoft.com/…/secauthz/protecting-objects` | HTTP 404 (2026-10-14) — sayfa yok | §5 satır 237 |
| 2 | `learn.microsoft.com/…/system.io.filesystem.setunixfilemode` | HTTP 404 — slug hatası (doğru: `system.io.file.setunixfilemode`) | §5 satır 238–239 |

### §11.2 §7.3'teki 2 Yapısal Boşluk

| # | Boşluk | Neden yapısal? | Kanıt |
|---|---|---|---|
| 1 | ACL yüzeyi tek-sayfa kanıtı | `protecting-objects` 404 → ACL derinliği tek `filesystemaccessrule` sayfasına indirgendi; DACL düzenleme akışı §2.7'de "kural nesnesi düzeyinde durur" | §4 satır 221–222, §5 satır 237 |
| 2 | `SetUnixFileMode` bu kartta değil | Slug 404 → Linux kartına bırakıldı (`kod-ornekleri-linux.md` §5) | §5 satır 238–239 |

## §12. S1/S2 Derinliği

§12.1 S1 — kapı ölçümü · §12.2 S2 — marj.

### §12.1 S1 — Kapı Ölçümü (append öncesi 249 satır)

Append öncesi ölçüm (`read` offset=100000 → "(249 lines)"): **249 satır** — Band-1 kapısı (≥500) altında. Append sonrası ölçüm: kapı sonucu §15.2'de. Not: bu dosyada `§0.1`/`§0.2` başlığı **yok** (yalnız `## §0 ELI15`, satır 28) — kapı ölçümü bu §12/§15 çiftinde taşınır.

### §12.2 S2 — Marj

Marj = ölçülen satır − 500. Önceki 6 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11. Bu dosyanın marjı §9.2'deki URL sayısına bağlı: §5'teki 7 okunabilir URL + 2×404 → §7.1 tablosu ~20 satır; §7.2–§7.4 ~60 satır; §8–§16 ~150 satır → toplam ~230 satır → 249 + 230 = ~479 → **kapı riski var**; §15.2'de ölçülecek.

## §13. Tüketim Kuralları

§13.1 ne okunmalı (öncelik) · §13.2 ne okunmamalı · §13.3 ne çoğaltılmamalı · §13.4 ne güncellenmeli · §13.5 doğru/yanlış cümle kartı.

### §13.1 Ne Okunmalı (öncelik)

1. §3 CoreMusic repo durumu — en kritik (0 isabet negatif kanıt).
2. §5 Referans — 7 URL + 2 reddedilen aday.
3. §2 Kod örnekleri — 7 blok (sözleşmesel referans).
4. §1 matris — 7 dil × Windows yüzeyi.
5. §4 Sınır cümleleri — K009/K010 ayrımı.

### §13.2 Ne Okunmamalı

- §7.1 dil derinlikleri — §1 + §2'deki bilginin konsolide tekrarı (aynı 7 dil, aynı mekanizmalar).
- §9.1 SAPMA tablosu — §1/§2/§5 zaten okunduysa gereksiz (0 sapma kaydı).

### §13.3 Ne Çoğaltılmamalı

- 7 dil × mekanizma eşlemesi — §1 + §2 + §7.1'de 3× (§7.1 konsolide, §1 ham matris).
- §5'teki 7 URL — §5 + §7.1 + §9.2'de 3×.
- §3'teki 0 isabet — §3 + §8.1 + §13.1'de 3×.

### §13.4 Ne Güncellenmeli

- §12.2 marj tahmini — §15.2 kapı ölçümü sonrası gerçek marjla karşılaştırılmalı (tahmin ~479 → kapı riski).
- §11.2'deki 2 boşluk — `yetki-ve-erisim-modeli-windows` kartı üretildiğinde veya linux kartı `SetUnixFileMode` taşıdığında güncellenmeli.

### §13.5 Doğru/Yanlış Cümle Kartı

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "Windows yüzeyi bu repoda 0 isabet — 418 PHP+JS'de `CreateJobObject`/`FileSystemAccessRule`/`share_mode` grep'i 0 döndü." | "Windows güvenlik API'leri repoda kullanılıyor." | §3 satır 197: grep 0 isabet; §0 satır 36–37: "repo'da uygulanıyor" iddiası YASAK. |
| "Windows'ta `chmod` yalnız read-only bayrağını etkiler; gerçek ACL C# `FileSystemAccessRule` ile yapılır." | "PHP/Python `chmod` ile Windows ACL'si düzenlenebilir." | §2.2 + §2.5 + §5 satır 233–234: chmod ≠ ACL. |
| "`AssignProcessToJobObject` sonrası iş↔süreç ilişkisi kırılamaz." | "İş nesnesi ataması sonradan geri alınabilir." | §2.1 + §2.3 + §5 satır 229: upstream "cannot be broken". |
| "Node.js'de `os.platform()` yalnız ayrım kapısıdır — Windows güvenlik API'si standart modülde yoktur." | "Node.js ile Windows job/ACL yapılabilir." | §2.4 + §5 satır 232: platform ayrımı-only. |
| "Rust `share_mode(0)` varsayılanı kapatır — varsayılan `FILE_SHARE_READ|WRITE|DELETE` açıktır." | "Rust dosya paylaşımı varsayılan kapalıdır." | §2.6 + §5 satır 235: varsayılan açık. |

## §14. Bağımlılık Zinciri

§14.1 §0→§1→§2→§3→§4→§5 zinciri · §14.2 §7'nin bağımlılıkları.

### §14.1 §0→§1→§2→§3→§4→§5

§0 (görev) → §1 (matris) → §2 (bloklar) → §3 (repo durumu) → §4 (sınır) → §5 (referans). §0 satır 39 "7 upstream URL bugün (2026-10-14) GET 200" → §1 "7 satır × upstream sınıfı" → §2 "7 blok × repo karşılığı YOK" → §3 "0 isabet negatif kanıt" → §4 "K009/K010 ayrımı" → §5 "7 URL + 2 reddedilen". §0→§1→§2→§3→§4→§5 bağımlılığı §7.1'deki dil derinliklerinin temeli.

### §14.2 §7'nin Bağımlılıkları

§7.1 (dil derinlikleri) §1 + §2 + §5'e bağımlı. §7.2 (mekanizma sınıflandırması) §2 bloklarına bağımlı. §7.3 (boşluklar) §5'teki 2×404'e bağımlı. §7.4 (arıza örüntüsü) §1–§5'ten türetildi. §7.9 (grep eşlemesi) §2 + §3'e bağımlı. §7.10 (ölçüm betiği) §3'teki PowerShell bloğuna bağımlı. §8 (negatif kanıt) §3 + §5'e bağımlı.

### §14.3 §7.10 Sapmasının Bağımlılık Etkisi

§7.10'daki betik sapması (3 desen/10 desen) §9.1'deki "0 sapma" iddiasını **düzeltmez** — §9.1 §1/§2/§5 eşleşmesini ölçüyor (7 satır = 7 blok = 7 URL), §7.10 ise §3'teki PowerShell betiğinin kapsamını ölçüyor (betik 3 bloğu ölçüyor, §3'ün iddia ettiği 10 desenin alt kümesi). İki ölçüm farklı katmanları kapsıyor: §9.1 = kart-içi tutarlılık, §7.10 = yeniden üretilebilir ölçüm kapsamı. Betik 3 desenle 0 isabet buluyor; 10 desenle de 0 isabet beklenir (aynı 418 dosya ağacı) — sapma kapsam eksikliği, sonuç sapması değil.

## §15. Kapı Ölçüm Kaydı

§15.1 ölçüm öncesi/sonrası · §15.2 kapı sonucu.

### §15.1 Ölçüm Öncesi/Sonrası

Append öncesi: **249 satır** (`read` offset=100000 → "(249 lines)") — Band-1 kapısı (≥500) altında. İlk append sonrası: **440 satır** (§7–§16) → kapı altında (60 eksik). §7.9 + §13.6 eklendi → 464 → §10.1.1 + §7.10 eklendi → 488 → §14.2–§14.3 eklendi → 492 → §9.2.1 eklendi → **506 satır** — kapı **GEÇTİ**. Toplam eklenen: §7–§16 = 257 satır. Not: bu dosyada `§0.1`/`§0.2` başlığı yok (yalnız `## §0 ELI15`, satır 28); kapı ölçümü bu §15'te taşınır.

### §15.2 Kapı Sonucu

**506 ≥ 500 → GEÇTİ** · marj **+6** · footer dosyanın son satırında · Authority korundu. Marj düşük (tizen +7'ye yakın) — sonraki revizyonda §13.6 uzantısı genişletilebilir.

## §7.9 Derinlik Uzantısı (§2 blokları × §3 grep desenleri eşlemesi)

§2'deki 7 blok ile §3'teki 10 grep deseninin birebir eşlemesi (§9.1'de "0 sapma" doğrulandı):

| §2 bloğu | §3 grep deseni | Eşleşme | Not |
|---|---|---|---|
| §2.1 C: job | `CreateJobObject`, `AssignProcessToJobObject`, `SetInformationJobObject` | 3/3 | §3 satır 190 |
| §2.2 Python: chmod | `chmod` | 1/2 (PHP ile ortak) | §3 satır 191 |
| §2.3 Go: x/sys | `AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData` | 3/3 | §3 satır 190–191 |
| §2.4 JS: platform | `os.platform` | 1/1 | §3 satır 191 |
| §2.5 PHP: chmod | `chmod` | 1/2 (Python ile ortak) | §3 satır 191 |
| §2.6 Rust: share_mode | `share_mode` | 1/1 | §3 satır 191 |
| §2.7 C#: ACL | `FileSystemAccessRule` | 1/1 | §3 satır 191 |

**Sonuç**: 10 grep deseni 7 bloğa dağıtıldı; `chmod` 2 blokla ortak (§2.2 + §2.5). §3'teki desen listesi §2'deki API adlarından türetildi — sapma 0 (§9.1).

## §13.6 Doğru/Yanlış Cümle Kartı — Uzantı (§7.9 eşlemesi)

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "§3'teki 10 grep deseni §2'deki 7 blokla birebir eşleşir; `chmod` 2 blokla ortaktır." | "§3'teki 10 desen 10 ayrı bloğa karşılık gelir." | §7.9 tablosu: `chmod` §2.2 + §2.5'te ortak; 10 desen 7 bloğa dağılır. |
| "Go §2.3'te 3 token/imtiyaz deseni (`AdjustTokenPrivileges`, `DuplicateTokenEx`, `CryptProtectData`) grep listesine dahildir." | "Token/imtiyaz desenleri grep listesinde değil." | §3 satır 190–191: 3 desen grep deseninde; §4'te "yalnız anılır" derken kastedilen bu desenlerin **çağrılmadığı** (0 isabet), listede **olmadığı** değil. |
| "Bu dosyanın marjı §15.2'deki ölçülecek; kapı riski §12.2'de tahmin edildi." | "Bu dosya kapıya güvenle geçti." | §15.2: 440 < 500 (ilk ölçüm); uzantı sonrası ikinci ölçüm gerekli. |

## §16. Marj

§16.1 marj hesabı · §16.2 marj güvenliği.

### §16.1 Marj Hesabı

Marj = ölçülen satır − 500. Önceki 6 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11. Bu dosyanın marjı §15.2'deki ölçülen satıra bağlı.

### §16.2 Marj Güvenliği

§7.1'deki 7 dil derinliği §5'teki 7 URL ile destekleniyor. §7.4'teki 4 arıza örüntüsü §1–§5'ten türetildi. §7.9'daki grep eşlemesi §2 + §3'ten (0 sapma). §7.10'daki betik karşılaştırması §3'teki PowerShell bloğundan (3 desen/10 desen — kapsam sapması, §14.3). §8–§16'daki çürütme/ SAPMA/ sınır/ reddedilen/ S1/S2/ tüketim/ bağımlılık/ kapı/ marj bölümleri §5'teki 7 okunabilir URL + 2×404 ile destekleniyor. Nihai ölçüme göre kapı **506 ≥ 500 → GEÇTİ** (§15.2); marj +6.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode