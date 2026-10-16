---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-macos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://docs.python.org/3/library/os.html
  - https://go.dev/doc/install/source
  - https://nodejs.org/api/os.html
  - https://www.php.net/manual/book.posix.php
  - https://doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html
  - https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://support.apple.com/guide/security/sandbox
validation:
  - "2026-10-14: 8 kaynak URL GET 200 ile dogrulandi; apple sandbox sayfasi redirect ile Security TOC'ye duser (icrik detay YOK) -> sandbox alt-yuzeyi ⚠️ birakildi; C satiri POSIX spec dogrulanmasina ragmen macOS dogrulamasi olmadigi icin ⚠️."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — macOS (7 Dil)

## §0 ELI15

Bu kart, **macOS'ta 7 dil bölümünde** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını taşır: POSIX DAC izin kalıbı, Darwin platform ayrımı, POSIX uzantısı.
Hepsi `# kaynak: upstream docs` imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**, macOS güvenlik API isabeti **0** (negatif kanıt, §3).
> Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

> **⚠️ alt-yüzey (kısmi):** `sandbox-exec`/Seatbelt profili ve codesign/entitlement yüzeyi bu
> turda **doğrulanamadı** — `support.apple.com/guide/security/sandbox` URL'si 200 dönüyor ama
> redirect ile Apple Platform Security **TOC'sine** düşüyor, detay metni gelmiyor → o
> alt-yüzey yazılmadı (boot/SIP/Gatekeeper halkası `cekirdek-guvenlik-macos` kartıdır).

## §1 OS güvenlik profili × dil matrisi

| Dil | macOS güvenlik yüzeyi | Bu dosyadaki mekanizma | Upstream sınıfı |
|---|---|---|---|
| C | — | POSIX `chmod` spec'i okundu; macOS'e özgü doğrulama **yok** | pubs.opengroup (spec) + apple TOC (detay yok) → ⚠️ |
| Python | `os` modülü Darwin notları | `os.uname()` → 'Darwin'; `getresuid` macOS'ta **yok** (negatif) | docs.python.org |
| Go | `GOOS=darwin` hedef seçimi | `//go:build darwin` derleme etiketi | go.dev/doc/install/source |
| JS (Node) | `os.platform()` ayrımı | `'darwin'` platform kapısı | nodejs.org/api |
| PHP | POSIX uzantısı (`posix_*`) | `posix_geteuid` kök kontrolü + macOS notu | php.net (book.posix) |
| Rust | `PermissionsExt` POSIX DAC | `from_mode(0o750)` | doc.rust-lang.org |
| C# | .NET POSIX dosya izni | `File.SetUnixFileMode` (Linux/macOS) | learn.microsoft.com |

**Okuma:** 6 satır ✅ (1 satır kısmi) + 1 satır ⚠️; hiçbir satır "repoda uygulanıyor" iddiası
**taşımez** — hepsi hedef referanstır (§3).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 6 bölümün **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (üretime alınabilir komut **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — C: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> POSIX `chmod` spec'i doğrulandı (pubs.opengroup), ancak bu fonksiyonun macOS'e özgü
> doğrulanmış upstream teyidi bu turda **bulunamadı**; `support.apple.com/guide/security/sandbox`
> redirect/TOC olduğundan sandbox yüzeyi de taşınamaz → uydurma çağrı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış macOS upstream'i yok) | YOK — repo'da C zaten yok (§3) |

### §2.2 — Python: Darwin tanısı + macOS'a özgü eksik API (referans)

```python
# kaynak: upstream docs - docs.python.org/3/library/os.html
import os

# os.uname(): "On macOS, iOS and Android, this returns the kernel name and
# release (i.e., 'Darwin' on macOS and iOS ...)" -> kirlog adi, kullanici surfu degil
kernel = os.uname().sysname          # macOS'ta 'Darwin'

# os.getresuid()/getresgid(): "Availability: Unix, not WASI, not macOS, not iOS."
# -> macOS'ta SUNULMAZ (upstream negatif kanit); bu API'ye dayanan kod macOS'ta calismaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.uname`/`getresuid` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.3 — Go: `GOOS=darwin` derleme etiketi (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source (GOOS kumesi: android, darwin,
//   dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris,
//   wasip1, windows -> 'darwin' macOS hedefidir)
//go:build darwin

package main // yalniz darwin (macOS) hedefinde derlenir
```

not: bu kart macOS'a özgü sandbox/entitlement Go upstream'i **doğrulanamadı** → o alt-yüzey
yazılmadı; Go'nun POSIX yüzeysi `golang.org/x/sys/unix`'tedir (imza listesi
`kod-ornekleri-go.md` §2.3).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.4 — JS (Node.js): platform ayrımı (referans)

```js
// kaynak: upstream docs - nodejs.org/api/os.html
const os = require('node:os');

// os.platform() macOS'ta 'darwin' dondurur (upstream deger kumesi: aix|darwin|
// freebsd|linux|openbsd|sunos|win32; 'android' deneysel notlu)
if (os.platform() === 'darwin') {
  // macOS'a ozel guvenlik yolu burada AYRILIR; guvenlik kararinin kendisi S1'dir
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `'darwin'` karşılaştırması 98 JS dosyasında 0 isabet |

### §2.5 — PHP: POSIX uzantısı kök kontrolü (referans)

```php
<?php
// kaynak: upstream docs - php.net/manual/book.posix.php (POSIX Functions listesi)
// kaynak: upstream docs - php.net/manual/en/function.posix-setuid.php (posix_geteuid)
declare(strict_types=1);

// php.net book.posix sayfasi POSIX uzanti fonksiyonlarini listeler; macOS bu
// uzantiyi sunar. macOS'e OZGUR davranis notu bu sayfada YOKTUR -> kalin iddia yazilmaz.
if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
    // koku UID: dusurme/uyari mantigi uygulama katmaninin karari (S1)
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `posix_*` çağrısı 320 PHP dosyasında 0 isabet (2026-10-14) |

### §2.6 — Rust: POSIX DAC izin kalıbı (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html
use std::fs::{File, Permissions};
use std::os::unix::fs::PermissionsExt;

// POSIX DAC: sahip rwx + grup r-x, digerleri yok (0o750); "Available on Unix only"
let file = File::create("session.dat")?;
file.set_permissions(Permissions::from_mode(0o750))?;
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.rs` 0 dosya; `0o750` yalnız hedef referans (PHP `0750` aynı değer, farklı katman) |

### §2.7 — C#: `File.SetUnixFileMode` (referans)

```csharp
// kaynak: upstream docs - learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode
using System.IO;

var mode = UnixFileMode.UserRead | UnixFileMode.UserWrite;   // 0600 benzeri
File.SetUnixFileMode("/Users/shared/session", mode);
// upstream: bu API Linux/macOS hedeflerinde anlamlidir (Windows'ta etkisi yok)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — C# kaynak yok (`.cs` 0 dosya); yalnız hedef referans |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`darwin|SetUnixFileMode|posix_geteuid|from_mode|os.uname|share_mode` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| macOS güvenlik API çağrısı | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| Seatbelt/sandbox/codesign kalıbı | grep | **0 isabet** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'SetUnixFileMode|posix_geteuid|sandbox-exec|seatbelt').Count  # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. macOS OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız macOS dosya/izin/platform kalıpları ele alınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; boot zinciri/SIP/Gatekeeper → `cekirdek-guvenlik-macos`.

Ek sınır: TCC izin penceresi, kod imzalama ve sandbox profili bu kartın **konusu değildir**
(doğrulanamadı da, §0). `0o750` bir DAC ayarıdır, "boot koruması" diye adlandırılması
abartıdır; kext/sürücü yazımı bu dala yazılmaz (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.python.org/3/library/os.html | `os.uname()` → macOS'ta 'Darwin'; `getresuid`/`getresgid` "not macOS, not iOS" | HTTP 200 · içerik okundu |
| https://go.dev/doc/install/source | GOOS kümesi içinde `darwin` (macOS hedefi) | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer kümesi (`darwin` dahil) | HTTP 200 · içerik okundu |
| https://www.php.net/manual/book.posix.php | POSIX uzantısı fonksiyon listesi (posix_* çekirdeği) | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html | `from_mode`/`set_mode`; örnek `0o700`, `0o644`; "Available on Unix only" | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode | `File.SetUnixFileMode` — POSIX dosya izni (Linux/macOS) | HTTP 200 · içerik okundu |
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | POSIX `chmod` semantiği (oktal mod) | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/sandbox | Apple Platform Security sandbox sayfası — **yalnız TOC** (redirect; detay metin gelmedi) | HTTP 200 · içerik TOC — detay YOK |

> **Reddedilen adaylar (yazılmadı):** `docs.python.org/3/using/macos.html` → **404** (2026-10-14) ·
> `developer.apple.com/documentation/.../preventing-insecure-exception-behavior` → **404** ·
> `support.apple.com/guide/security/sandbox` gövdesi → **TOC düzeyi, detay yok** → sandbox
> kalıbı yazılmadı (reddedilen içerik, §1 notu). Üçü de hiçbir bloğa girmedi.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | macOS güvenlik katmanı (POSIX DAC/TCC/sandbox) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (izin mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode