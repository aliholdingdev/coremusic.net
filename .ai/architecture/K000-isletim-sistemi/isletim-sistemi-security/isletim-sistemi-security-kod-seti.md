---
node_id: "K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-security"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-ornekler"
evidence:
  - "https://man7.org/linux/man-pages/man2/chmod.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/umask.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/seccomp.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/security/app_sandbox (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/setrlimit.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/icacls (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man7/capabilities.7.html (✅ HTTP 200 · 2026-10-14)"
  - "https://php.net/manual/en/function.chmod.php (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesecurity (✅ HTTP 200 · 2026-10-14 — yazım anında webfetch ile doğrulandı)"
validation: "2026-10-15: 18 benzersiz resmi URL (HTTP 200 kapısı) · 20 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: seccomp/Job Object/App Sandbox uygulanacak mı — repo'da 0 isabet (ornekler §8 N1–N8), karar bu dosyada verilmez → UNKNOWN"
  - "O2: macOS üretim/koşucu kanıtı repo'da yok (Q4) → App Sandbox sütunu [TASARIM]"
  - "O3: C# FileSecurity yalnız Windows sınıfıdır; .NET 5+ ACL paketi Linux'ta uygulanamaz → çapraz sınıf sınırı"
  - "O4: capabilities(7) seti hangi üyeye düşürülecek (bounding set) → [TASARIM], ölçülmüş değer yok"
  - "O5: üçüncü parti seccomp sarmalayıcı (libseccomp vb.) bağımlılık ekler → yalnız kavram/R6"
  - "O6: sandbox dili olacak mı (tek sarmalayıcı mı, diller mi?) → politika sahibi bu dosya, karar açık"
group: kod
---

# isletim-sistemi-security — Cross-Platform Kod Seti (dosya izni · sandbox · capability/limits)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-security` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar; kod **Windows · Linux · macOS dahil tüm sınıflarda**
> çalışacak biçimde yazılır. Bu dosya üç OS güvenlik derdini kucaklar: **dosya izni** (GRUP A),
> **sandbox** (GRUP B — **bu dosya politika sahibidir**), **capability/limits** (GRUP C).
> `turu-kod-seti` tekrarı YOKTUR: turu = OS ailesi karşılaştırma; bu dosya = kendi endişesi.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi taşır |
| Repo referansı | Repo gerçekleri kardeş dosyadan (`isletim-sistemi-security-ornekler.md` §1–§13) referanslanır; yeni `Dosya:/Satır:` çifti **üretilmez** |
| Kaynak | Dil/arayüz adları §5'teki **2026-10-14 HTTP 200** resmi dokümanlardan türetildi; içerik alıntısı yok |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Bağımlılık | Üçüncü parti seccomp/log/criterion benzeri paketler **eklenmez**; yalnız kavram + `[TASARIM]` (plan R6) |
| Yasak | `TBD`/`...` dolgusu · kaynaksız API adı · "çalışıyor" iddiası · uydurma repo satırı · sessiz fallback |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış doğrulanmadı ·
`UNKNOWN` = repo'da kanıt yok · `[POLİTİKA]` = bu dosya karar vericidir (GRUP B).

---

## §1. Çalışma sözleşmesi — "koşan tek Linux, yazılan her yerde"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] `ci.yml` — ornekler §13 |
| Yayım yüzeyi | Apache `.htaccess` **+** IIS `web.config` birlikte | [KANIT] ornekler §13 (OS kartları) |
| macOS | repo kanıtı **yok** | [BİLİNMEYEN] Q4 → `[TASARIM]` |
| Sandbox | Linux seccomp · Windows Job Object · macOS App Sandbox — **üçü de hedef sınıf** | [POLİTİKA] GRUP B · ornekler §8 N1–N8 (0 isabet) |
| Capability/limit | Linux `setrlimit`/`capabilities(7)` ↔ Windows Job limit ↔ macOS `[TASARIM]` | [POLİTİKA] GRUP C |
| Dosya izni | POSIX mod ↔ Windows ACL — iki ayrı dil, tek politika | GRUP A |

**Sözleşme kuralları (4 madde — her grup bunlara uyar):**

1. **İzin politikası tek yerden** tanımlanır; her dosya kendi başına `chmod` yazmaz (§GRUP A).
2. **Sandbox uygulaması tek sarmalayıcıdadır** — dil başına çoğaltılmaz (§GRUP B, O6).
3. **Limitler fail-safe**'tir: okunamayan sınır **sessizce geçilmez**, hata `throw`/`error` ile açılır.
4. **`turu-kod-seti`'ne çapraz referans** — OS ailesi karşılaştırması orada; burada yalnız
   güvenlik endişesi. Tekrar 0.

---

## §2. Grup haritası — 3 grup × dil dağılımı

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Dosya izni (chmod/umask ↔ icacls/ACL) | PHP · Python · Go · Bash · PowerShell · C# · JS/Node · Swift · Java/Kotlin | §GRUP A |
| B | Sandbox (seccomp/no_new_privs ↔ Job Object ↔ App Sandbox) | C · C++20 · Rust · C# | §GRUP B |
| C | Capability/limits (setrlimit ↔ Job memory limits ↔ capabilities(7)) | C · Bash · Go · PowerShell · Zig | §GRUP C |

**Toplam:** 3 grup · 28 kod bloğu · 13 dil yüzeyi (PHP, Python, Go, C#, C, C++20, Rust, Bash, PowerShell, JS/Node, Swift, Java/Kotlin, Zig).

---

## GRUP A — Dosya izni (POSIX mod ↔ Windows ACL)

> **Amaç:** "Bu dosyayı kim okuyabilir?" sorusunu **tek politika** olarak kurmak; POSIX `rwx`
> üçlüsü ile Windows ACL'nin farklı diller olduğunu saklamamak. Repo karşılığı:
> `DIR_PERMISSIONS = 0750` (`isletim-sistemi-security-ornekler.md` §1.1) ve `@mkdir($savePath, 0750, true)`
> (§1.2) — repo **yalnız POSIX modu** bilir, ACL yüzeyi 0 (§8 N-serisi).

### §A.1 PHP — `chmod`

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://php.net/manual/en/function.chmod.php
   POSIX karşılığı: https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html */
declare(strict_types=1);

final class FilePermission
{
    /** 0600: yalnız sahip okur/yazar — oturum/anahtar dosyası sınıfı. */
    public static function hardenSecret(string $path): void
    {
        // Umask etkisi: umask yeni dosyanın iznini kısar (§A.4) — chmod bunu geri açabilir.
        if (!chmod($path, 0600)) {
            throw new RuntimeException("izin uygulanamadı: {$path}"); // sessiz fallback YASAK
        }
    }

    /** 0750: sahip yaz, grup oku, diğer erişemez — repo deseni (§1.1–§1.2 köprüsü). */
    public static function hardenDir(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0750, true) && !is_dir($path)) {
            throw new RuntimeException("dizin oluşturulamadı: {$path}");
        }
        if (!chmod($path, 0750)) {
            throw new RuntimeException("dizin izni uygulanamadı: {$path}");
        }
    }
}
```

**Repo köprüsü:** Aynı `0750` değeri repo'da sabit olarak çalışır (`-ornekler.md` §1.1); bu örnek
aynı değeri **fonksiyon** olarak kurar. `⚠️ VERIFICATION REQUIRED`: Windows'ta PHP `chmod`
yalnızca "yazılabilir/yazılabilir değil" ayrımını uygular (ACL değil) → tam rwx davranışı doğrulanmadı.

### §A.2 Python — `os.chmod`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man2/chmod.2.html
import os
import stat

def harden_secret(path: str) -> None:
    # S_IRUSR | S_IWUSR = 0o600 — yalnız sahip okur/yazar
    os.chmod(path, stat.S_IRUSR | stat.S_IWUSR)
    got = stat.S_IMODE(os.stat(path).st_mode)
    if got != 0o600:
        raise PermissionError(f"izin beklenen değil: {oct(got)}")  # doğrula, güvenme

def harden_dir(path: str) -> None:
    os.makedirs(path, mode=0o750, exist_ok=True)
    os.chmod(path, 0o750)
```

**Sınıf dersi:** `os.chmod` POSIX'te tam mod uygular; Windows'ta Python yalnızca yazılabilirlik
bitini çevirir — ACL **değildir**. Bu yüzden doğrulama satırı (`got != 0o600`) **zorunludur**:
sessizce farklı bir izin uygulanırsa hata alınır.

### §A.3 Go — `os.Chmod`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/chmod.2.html
package harden

import "os"

// HardenSecret, dosya iznini 0600'a çeker ve hatayı çağırana devreder.
func HardenSecret(path string) error {
    if err := os.Chmod(path, 0o600); err != nil { // POSIX mod; Windows'ta yazılabilirlik bitine iner
        return err
    }
    fi, err := os.Stat(path)
    if err != nil {
        return err
    }
    if fi.Mode().Perm() != 0o600 {
        return os.ErrPermission // beklenen mod uygulanmadı → fail-safe
    }
    return nil
}
```

**Not:** Go `os.Chmod` Windows'ta yalnızca `0o200` (yazılabilir) bitini yönetir; tam ACL `icacls`
veya `FileSecurity` sınıfıdır (§A.6). `⚠️ VERIFICATION REQUIRED`: mod≠ACL sınırı test edilmedi.

### §A.4 Bash — `chmod` + `umask`

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man2/umask.2.html
set -euo pipefail

umask 077                                  # yeni dosya: 600 · yeni dizin: 700 — en kısıtlayıcı
SECRET_DIR="${1:?kullanım: $0 <dizin>}"     # argüman yoksa hata (sessiz geçiş YASAK)
mkdir -p "$SECRET_DIR"
chmod 700 "$SECRET_DIR"

secret="$SECRET_DIR/secret.env"
( umask 077; : > "$secret" )               # alt kabukta umask — dosya 600 doğar
chmod 600 "$secret"

mode="$(stat -c '%a' "$secret" 2>/dev/null || stat -f '%Lp' "$secret")"
if [ "$mode" != "600" ]; then
  echo "izin doğrulanamadı: $mode" >&2
  exit 1                                    # fail-safe: geçme
fi
```

**Sınıf dersi:** `umask` **yeni** dosyayı kısar, mevcut dosyayı **değiştirmez** — ikisi birlikte
kullanılır. `stat`'ın iki biçimi (`-c` Linux / `-f` macOS) tek `||` sarmalayıcıdadır:
kabuk farkı tek yerde toplanır (`turu-kod-seti` §GRUP G sözleşmesi).

### §A.5 PowerShell — `icacls` (Windows ACL)

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/icacls
$ErrorActionPreference = 'Stop'          # hatada dur
$secretDir = $args[0]
if (-not $secretDir) { Write-Error 'kullanım: script.ps1 <dizin>'; exit 64 }

New-Item -ItemType Directory -Force -Path $secretDir | Out-Null

# Tüm miras kalıntısı izinleri kaldırılır; yalnız SYSTEM + mevcut kullanıcı bırakılır.
icacls $secretDir /inheritance:r
icacls $secretDir /grant:r "${env:USERNAME}:(OI)(CI)F"
icacls $secretDir /grant:r "SYSTEM:(OI)(CI)F"
icacls $secretDir /remove:g "Users" "Everyone" "Authenticated Users"

$out = icacls $secretDir
if ($out -match 'Everyone') { Write-Error 'ACL daraltması başarısız'; exit 1 }
```

**Not:** `(OI)(CI)` = object-inherit + container-inherit — alt dosya/dizinler de miras alır.
`icacls` Windows yerelidir; bash karşılığı §A.4'tedir, biri diğerine **çevrilmez**.

### §A.6 C# — `FileSecurity` (Windows ACL · .NET)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesecurity
// Not: FileSecurity yalnız Windows sınıfıdır — Linux/macOS'ta uygulanamaz (§3 matrisi).
using System.IO;
using System.Security.AccessControl;
using System.Security.Principal;

static void HardenSecret(string path)
{
    var info = new FileInfo(path);
    FileSecurity sec = info.GetAccessControl();

    // Mevcut kullanıcı dışında herkesin izni kaldırılır (miras + explicit).
    sec.SetAccessRuleProtection(isProtected: true, preserveInheritance: false);

    var self = WindowsIdentity.GetCurrent().User
               ?? throw new InvalidOperationException("kimlik okunamadı");
    sec.AddAccessRule(new FileSystemAccessRule(
        self, FileSystemRights.FullControl, AccessControlType.Allow));

    info.SetAccessControl(sec);           // kalıcı yazım
}
```

**Sınıf dersi:** `FileSecurity`, POSIX `rwx`'in karşılığı **değildir** — DACL/SACL modeli
(ACE listesi) taşır. `⚠️ VERIFICATION REQUIRED`: .NET 5+ üzerinde `GetAccessControl`
`System.IO.FileSystem.AccessControl` paketini gerektirir (R6: paket eklenmedi, yalnız imza).

### §A.7 JS/Node.js — `fs.chmod`

```js
// https://nodejs.org/api/fs.html
// [ORİJİNAL ÖRNEK · derlenmedi]
// GRUP A — POSIX mod: 0750/0600 (repo: DIR_PERMISSIONS = 0750)
const { chmodSync } = require("node:fs");

const DIR_MODE  = 0o750;                       // dizin: owner rwx, group rx
const FILE_MODE = 0o600;                       // dosya: owner rw

function izinUygula(yol, dir = false) {
  chmodSync(yol, dir ? DIR_MODE : FILE_MODE);
  // Windows: yalnız yazılabilirlik biti etkilenir (ACL modeli yok) — §A.5 köprüsü
}

module.exports = { izinUygula };
```

**Ders:** `fs.chmod` POSIX modu taşır; Windows'ta Node yalnızca "yazılabilir" bitini
eşler — ACL listeleme/grant yoktur (§A.10 matrisinde karşılığı kısmi).

### §A.8 Swift — `FileManager.setAttributes(.posixPermissions)`

```swift
// https://developer.apple.com/documentation/foundation/filemanager
// [ORİJİINAL ÖRNEK · derlenmedi]
import Foundation

func izinUygula(url: URL, dir: Bool) throws {
    let mod: NSNumber = dir ? 0o750 : 0o600    // POSIX mod
    try FileManager.default.setAttributes(
        [.posixPermissions: mod], ofItemAtPath: url.path)
    // macOS ACL yüzeyi ayrı ailedir (setAccessControl) — bu blokta yok, gizlenmez
}
```

**Ders:** Foundation `.posixPermissions` POSIX modu verir; macOS ACL (extended attributes)
farklı API'dir — ikisi eşdeğer değildir, ikisi de bu dosyanın sahipliğindedir.

### §A.9 Java/Kotlin — `Files.setPosixFilePermissions`

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Files.html
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.attribute.PosixFilePermissions;

static void izinUygula(Path yol, boolean dir) throws Exception {
    String mod = dir ? "rwxr-x---" : "rw-------";   // 0750 / 0600
    Files.setPosixFilePermissions(yol, PosixFilePermissions.fromString(mod));
    // Windows: UnsupportedOperationException — ACL yüzeyi farklı (§A.5–A.6 köprüsü)
}
```

```kotlin
// https://kotlinlang.org/api/latest/jvm/stdlib/
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.nio.file.Files
import java.nio.file.Path
import java.nio.file.attribute.PosixFilePermissions

fun izinUygula(yol: Path, dir: Boolean) {
    val mod = if (dir) "rwxr-x---" else "rw-------"  // 0750 / 0600
    Files.setPosixFilePermissions(yol, PosixFilePermissions.fromString(mod))
    // Windows: UnsupportedOperationException — ACL yüzeyi farklı (§A.5–A.6 köprüsü)
}
```

**Ders:** NIO-2 POSIX yüzeyi POSIX modu string olarak da kabul eder; Windows'ta bu aile
`UnsupportedException` fırlatır — Java "sessiz geçmez", fırlatır (fail-safe yön).

### §A.10 İzin modu karşılaştırma matrisi (POSIX mod ↔ ACL kavramı)

| # | Kavram | POSIX (§A.1–A.4 · A.7–A.9) | Windows ACL (§A.5–A.6) | Karşılıklı mı? |
|---|---|---|---|---|
| 1 | Birim | 3 bit × 3 (owner/group/other) | ACE (kim + hak maskesi + allow/deny) | hayır |
| 2 | Varsayılan kısıtlama | `umask` | miras (inheritance) bayrağı | benzer niyet, farklı mekanizma |
| 3 | En kısıtlayıcı | `0700` / `0600` | `/inheritance:r` + tek kullanıcı grant | evet (niyet) |
| 4 | Grup kavramı | GID | Security Identifier (SID) | hayır |
| 5 | Denetim (audit) | POSIX'te ayrı (auditd) | SACL (`FileSystemAuditRule`) | hayır |
| 6 | Doğrulama | `stat` mod okuma | `icacls` çıktısı okuma | evet (zorunlu) |

**Politika notu (GRUP A):** Bu vault'ta izin politikası **POSIX mod + fail-safe doğrulama**'dır;
ACL yüzeyi yalnız Windows sınıfı taşır. macOS sütunu `[TASARIM]` (O2/Q4).

---

## GRUP B — Sandbox (seccomp/no_new_privs ↔ Job Object ↔ App Sandbox)

> **Amaç:** "Süreç ne yapamaz?" sorusunu OS düzeyinde kapatmak. **Bu dosya politika sahibidir**
> (`[POLİTİKA]`): hangi sandbox mekanizmasının nerede uygulanacağına bu dosya karar verir;
> diğer alt alanlar çapraz referans kullanır. Repo gerçeği: seccomp/JobObject/sandbox-exec
> **0 isabet** (`-ornekler.md` §8 N1–N8) — buradaki bloklar hedef referanstır.

### §B.1 C — `seccomp` + `no_new_privs` (Linux)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://man7.org/linux/man-pages/man2/seccomp.2.html
   Not: seccomp syscall'ı prctl/ptrace üzerinden kurulur; BPF filtresi üçüncü parti
   kütüphane (libseccomp) olmadan elle kurulur → R6: paket eklenmez, yalnız çekirdek imzası. */
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <unistd.h>
#include <sys/prctl.h>
#include <sys/syscall.h>
#include <linux/filter.h>
#include <linux/seccomp.h>
#include <linux/audit.h>

/* execve() hariç tüm "tehlikeli" syscall'ları reddet politikası:
   SECCOMP_RET_KILL_PROCESS — ihlalde süreç öldürülür (fail-safe). */
int sandbox_apply(void)
{
    if (prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0) != 0) {
        /* no_new_privs: setuid binary dahil yeni yetki kazanılamaz — SECCOMP için ön koşul */
        fprintf(stderr, "no_new_privs başarısız: %s\n", strerror(errno));
        return -1;                       /* hata açık; sessiz fallback YASAK */
    }

    struct sock_filter filter[] = {
        BPF_STMT(BPF_LD | BPF_W | BPF_ABS,
                 offsetof(struct seccomp_data, nr)),          /* syscall numarası */
        BPF_JUMP(BPF_JMP | BPF_JEQ | BPF_K, __NR_exit_group, 0, 1),
        BPF_STMT(BPF_RET | BPF_K, SECCOMP_RET_ALLOW),
        BPF_STMT(BPF_RET | BPF_K, SECCOMP_RET_KILL_PROCESS), /* default: öldür */
    };
    struct sock_fprog prog = {
        .len  = (unsigned short)(sizeof(filter) / sizeof(filter[0])),
        .filter = filter,
    };

    if (prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &prog) != 0) {
        fprintf(stderr, "seccomp uygulanamadı: %s\n", strerror(errno));
        return -1;
    }
    return 0;                            /* 0 = sandbox aktif */
}
```

**Sınıf dersi:** `no_new_privs` **önce** kurulur — seccomp BPF filtresi onun olmadan
`EPERM` verir. Sıra zorunlu. `⚠️ VERIFICATION REQUIRED`: filtre yalnız `exit_group`'a izin verir
(bilinçli kısıtlayıcı örnek); gerçek üretim filtresi O6/O4 kararına bağlı → `UNKNOWN`.

### §B.2 C++20 — RAII sandbox sarmalayıcısı (Linux)

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/seccomp.2.html
// Not: C++20 yüzeyi RAII ile sandbox ömrünü bağlar; syscall çağrısı C §B.1'e devredilir.
#include <stdexcept>
#include <string_view>

extern "C" int sandbox_apply();   /* §B.1 — tek uygulama noktası (O6 sözleşmesi) */

namespace cm::security {

class ScopedSandbox final {
public:
    explicit ScopedSandbox(std::string_view where)
    {
        if (sandbox_apply() != 0) {
            throw std::runtime_error(std::string("sandbox uygulanamadı: ") + std::string(where));
        }
    }
    ScopedSandbox(const ScopedSandbox&)            = delete;  /* tekil — kopyalanmaz */
    ScopedSandbox& operator=(const ScopedSandbox&) = delete;
    ~ScopedSandbox() = default;   /* çekirdek sandbox süreç bitene kadar sürer — geri alınamaz */
};

} // namespace cm::security
```

**Politika notu (O6):** Sandbox **geri alınabilir değildir** (seccomp filtresi düşürülemez) —
RAII yalnız hata/yönetim sığasını verir, "kapsayıcıdan çıkma" vermez. Bu bilinçli tasarım kararıdır.

### §B.3 Rust — `Command` öncesi sandbox (Linux · `pre_exec` kavramı)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/seccomp.2.html
// Not: `pre_exec` unsafe bir kapıdır; fork ile exec arasındaki tek an — sandbox burada kurulur.
// Üçüncü parti seccomp sarmalayıcısı (seccomp, libseccomp-rs) KULLANILMAZ (R6: yalnız kavram).
use std::process::{Command, Stdio};

pub fn run_sandboxed(tool: &str, args: &[&str]) -> std::io::Result<(u32, String)> {
    let out = Command::new(tool)
        .args(args)
        .stdin(Stdio::null())
        .stdout(Stdio::piped())
        .stderr(Stdio::piped())
        .pre_exec(|| {
            // prctl(PR_SET_NO_NEW_PRIVS) → sonra seccomp filter (§B.1 sırası)
            // İmza: raw syscall çağrısı gerekir → bilinçli eksik blok, ⚠️ VERIFICATION REQUIRED
            Err(std::io::Error::other(
                "sandbox_apply syscall imzası doğrulanmadı — blok bilinçli eksik",
            ))
        })
        .output()?;
    Ok((out.status.code().unwrap_or(1), String::from_utf8_lossy(&out.stdout).into_owned()))
}
```

**Dürüstlük notu:** Rust'ın `pre_exec` imzası `unsafe extern "C" fn() -> Result<Infallible, Error>`
şeklindedir; syscall numaraları için `libc` bağımlılığı gerekir → **R6 gereği** paket eklenmedi,
blok bilinçli eksik bırakıldı ve `⚠️ VERIFICATION REQUIRED` + O-soru açıldı (uydurma imza yazılmaz).

### §B.4 C# — Windows Job Object (sandbox karşılığı)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects
// Not: Job Object, Windows'un seccomp karşılığı DEĞİL — süreç grubu sınırlama mekanizmasıdır.
using System.Diagnostics;

static void SandboxChild(ProcessStartInfo psi)
{
    using var job = new JobObject();   // System.Diagnostics(JobObject) — R6: NuGet eklenmedi

    var info = new JobObjectBasicLimitInformation
    {
        LimitFlags = JobObjectLimitFlags.KillOnJobClose
                   | JobObjectLimitFlags.DieOnUnhandledException,
    };
    if (!JobObject.SetInformationJobObject(job, JobObjectLimitFlags.KillOnJobClose, info))
    {
        throw new InvalidOperationException("Job Object limiti uygulanamadı"); // fail-safe
    }

    using var child = Process.Start(psi)
        ?? throw new InvalidOperationException("süreç başlatılamadı");
    job.AssignProcessToJobObject(child);   // child artık Job kapanınca ölür
}
```

**Politika notu:** Job Object **işlem grubu** kisidir (kill-on-close, bellek/işlemci limiti —
§GRUP C) — syscall filtresi **değildir**. Bu ayrım politika sahibi bu dosyada kayıtlıdır (O6).
`⚠️ VERIFICATION REQUIRED`: `JobObject` sarmalayıcısı .NET'te yerleşik değildir; blok
kavramsal iskelettir, derlenmedi.

### §B.5 macOS — App Sandbox entitlement `[TASARIM]`

```xml
<!-- [ORİJİNAL ÖRNEK · derlenmedi] — [TASARIM] (O2/Q4: macOS repo kanıtı yok) -->
<!-- Resmi kaynak: https://developer.apple.com/documentation/security/app_sandbox -->
<!-- Bu bir entitlement plist'idir; kodla değil, imzalama anında uygulanır. -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
  "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>com.apple.security.app-sandbox</key>
    <true/>
    <key>com.apple.security.files.user-selected.read-only</key>
    <true/>
    <key>com.apple.security.network.client</key>
    <true/>
</dict>
</plist>
```

**Sınıf dersi:** App Sandbox **kod içi** bir çağrı değildir — imzalama ve `codesign` ile devreye
girer; macOS'ta seccomp/Job Object'in doğrudan karşılığı **yoktur**. Bu yüzden `[TASARIM]`
etiketi taşır ve macOS sütunu §3'te düşüktür (O2).

### §B.6 Politika sahipliği tablosu (GRUP B — bu dosyanın kararı)

| # | OS | Mekanizma | Neyi kısıtlar | Sahip dosya | Repo durumu |
|---|---|---|---|---|---|
| 1 | Linux | `seccomp` + `no_new_privs` (§B.1–B.3) | syscall yüzeyi | **bu dosya** | 0 isabet (§8 N1/N3) |
| 2 | Windows | Job Object (§B.4) | işlem grubu + limit | **bu dosya** | 0 isabet (§8 N7) |
| 3 | macOS | App Sandbox entitlement (§B.5) | dosya/ağ erişimi | **bu dosya** `[TASARIM]` | 0 isabet |
| 4 | Diğer alt alanlar | bu tabloya çapraz referans | — | turu · core · kernel | — |

**Çapraz referans kuralı:** `isletim-sistemi-kernel`, `isletim-sistemi-api` ve `isletim-sistemi-runtime`
sandbox konusuna **bu dosyadan** bakar; kendi içinde sandbox kodu **üretmez** (R3: tekrar 0).

---

## GRUP C — Capability / limits (setrlimit ↔ Job memory limits ↔ capabilities(7))

> **Amaç:** "Süreç en fazla ne kadar kaynak kullanabilir?" — sınır koymak, gözlemek değil.
> Repo karşılığı: `timeout = 5` uygulama bütçesi (`isletim-sistemi-plan-ornekler.md` §1.3);
> OS sınırı repo'da **yok** — bu grup hedef referanstır.

### §C.1 C — `setrlimit` + `RLIMIT_*`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://man7.org/linux/man-pages/man2/setrlimit.2.html */
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <sys/resource.h>

int limits_apply(void)
{
    struct rlimit rl;

    /* RLIMIT_NOFILE: açık dosya sayısı — kaynak sızıntısı tavanı */
    rl.rlim_cur = 512;  rl.rlim_max = 1024;
    if (setrlimit(RLIMIT_NOFILE, &rl) != 0) {
        fprintf(stderr, "NOFILE sınırı uygulanamadı: %s\n", strerror(errno));
        return -1;
    }

    /* RLIMIT_AS: adres uzayı (bellek) — taşma OOM öldürülmeden önce reddedilir */
    rl.rlim_cur = 512UL * 1024 * 1024;   /* 512 MiB soft */
    rl.rlim_max = 1024UL * 1024 * 1024;  /* 1 GiB hard */
    if (setrlimit(RLIMIT_AS, &rl) != 0) {
        fprintf(stderr, "AS sınırı uygulanamadı: %s\n", strerror(errno));
        return -1;
    }

    /* RLIMIT_CORE: çekirdek dosyası kapalı — üretimde sızıntı yok */
    rl.rlim_cur = 0;  rl.rlim_max = 0;
    if (setrlimit(RLIMIT_CORE, &rl) != 0) {
        fprintf(stderr, "CORE sınırı uygulanamadı: %s\n", strerror(errno));
        return -1;
    }
    return 0;                          /* 0 = limitler aktif */
}
```

**Sınıf dersi:** `rlim_cur` (soft, süreç kendi düşürebilir) ile `rlim_max` (hard, yalnız yetkili
yükseltebilir) ayrımı zorunludur. `⚠️ VERIFICATION REQUIRED`: `RLIMIT_AS` davranışı dosya
sistemine/çekirdeğe bağlı ölçülmedi.

### §C.2 Bash — `ulimit` (POSIX kabuk)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man2/setrlimit.2.html
set -euo pipefail

ulimit -n 512 2>/dev/null || { echo "NOFILE sınırı uygulanamadı" >&2; exit 1; }  # -n: dosya
ulimit -v 524288 2>/dev/null || { echo "AS sınırı uygulanamadı" >&2; exit 1; }    # -v: bellek (KiB)
ulimit -c 0    2>/dev/null || { echo "CORE sınırı uygulanamadı" >&2; exit 1; }    # -c: çekirdek

# Doğrulama: okunan değer beklenenle eşleşmeli (fail-safe)
cur="$(ulimit -n)"
if [ "$cur" -gt 512 ]; then
  echo "NOFILE beklenenden yüksek: $cur" >&2
  exit 1
fi

"$@"    # sınırlandırılmış alt süreç — argüman yoksa hata üretmez, boş çalışır
```

**Not:** `ulimit` **kabuk içi** sınırdır; kalıcı değildir (alt süreçler miras alır ama oturum
biter). `2>/dev/null` yalnızca kabuğun sınırsız desteklediği durumları yutar — asıl hata
`||` ile alınır (sessiz fallback yasağına uygun).

### §C.3 Go — `syscall.Setrlimit` kavramı (Linux)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/setrlimit.2.html
// Not: `syscall` paketi platforma özgüdür; GOOS=windows derlemesinde bu dosya derlenmez.
//go:build linux

package limits

import "syscall"

// ApplyNOFILE, açık dosya sayısını 512'ye kısar; hata çağırana devreder.
func ApplyNOFILE(soft, hard uint64) error {
    rl := &syscall.Rlimit{Cur: soft, Max: hard}
    if err := syscall.Setrlimit(syscall.RLIMIT_NOFILE, rl); err != nil {
        return err // sessiz yutma YASAK
    }
    var got syscall.Rlimit
    if err := syscall.Getrlimit(syscall.RLIMIT_NOFILE, &got); err != nil {
        return err
    }
    if got.Cur != soft {
        return syscall.EINVAL // beklenen sınır uygulanmadı → fail-safe
    }
    return nil
}
```

**Sınıf dersi:** Go'da `syscall` **derleme zamanı** ayrımı (`//go:build linux`) ile korunur —
Windows derlemesinde blok hiç üretilmez. `⚠️ VERIFICATION REQUIRED`: `Rlimit` alan tipleri
(32/64-bit) mimariye bağlı test edilmedi.

### §C.4 PowerShell — Job Object bellek limiti (Windows)

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject
$ErrorActionPreference = 'Stop'

# Windows'ta bellek sınırı doğrudan PowerShell ile kurulmaz — Win32 Job Object gerekir (§B.4).
# Bu blok yalnız kavramı gösterir: Process + bellek limiti eşlemesi.
$proc = Start-Process -FilePath 'child.exe' -PassThru -WindowStyle Hidden
if (-not $proc) { Write-Error 'süreç başlatılamadı'; exit 1 }

# Bellek limiti için .NET sarmalayıcı: WorkingSet (MB) — Job Object içine atanır.
$mb = 512
try {
    $proc.MaxWorkingSetSize = $mb * 1MB
    $proc.MinWorkingSetSize = 64MB
} catch {
    Write-Error "WorkingSet sınırı uygulanamadı: $($_.Exception.Message)"
    exit 1
}

$proc.WaitForExit()
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
```

**Dürüstlük notu:** `Process.MaxWorkingSetSize` **.NET sarmalayıcısıdır**; tam Job Object limiti
(`JobObjectMemoryLimitInformation`) Win32 `SetInformationJobObject` üzerinden kurulur (§B.4 köprüsü).
`⚠️ VERIFICATION REQUIRED`: .NET özelliği yalnızca sistem yetkisiyle uygulanabilir — doğrulanmadı
→ `UNKNOWN`.

### §C.5 capabilities(7) — yetki seti kavramı (Linux)

| # | Kavram | Açıklama | Kaynak | Karşılığı |
|---|---|---|---|---|
| 1 | `CAP_NET_BIND_SERVICE` | 1024 altı port bağlama yetkisi | capabilities.7 | root yerine tek yetki |
| 2 | `CAP_SYS_ADMIN` | geniş admin yetkisi — **kısıtlanacak ilk aday** | capabilities.7 | — |
| 3 | bounding set | süreç asla kazanamayacağı yetki üst sınırı | capabilities.7 | sandbox'un yetki kolu |
| 4 | `setcap` / `getcap` | dosya bazlı yetki atama/kalıcı kılma | capabilities.7 | ACL yerine yetki |
| 5 | Windows karşılığı | yetki modeli yok — Job Object + ACL | Job Objects | ayrım açık |

**Politika notu (GRUP C):** capabilities(7) **yalnız Linux** kavramıdır; Windows'ta karşılığı
**yoktur** (ACL + Job Object ikilisi kullanılır). Bu eşitsizlik gizlenmez, §3 matrisinde `❌`
ile gösterilir. Repo durumu: 0 isabet (`-ornekler.md` §8 N2).

### §C.6 Zig — `posix.setrlimit` (süreç limiti)

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
// [ORİJİNAL ÖRNEK · derlenmedi]
const std = @import("std");
const posix = std.posix;

pub fn open_file_limitini_kis() !void {
    var lim: posix.rlimit = undefined;         // rlimit = struct { cur: rlim_t, max: rlim_t }
    try posix.getrlimit(.NOFILE, &lim);        // mevcut soft/hard limiti oku
    lim.cur = @min(lim.cur, 4096);             // yalnız SOFT limiti düşür
    try posix.setrlimit(.NOFILE, lim);         // SetrlimitError!void — hard'a dokunma
}
```

**Ders (GRUP C):** `setrlimit` yalnız soft limiti düşürmek için güvenlidir; hard limit
artırma yetkisi ayrı (kök/capability) yüzeydir. `NOFILE` alanı ham kaynaktan doğrulandı
(c.zig/linux.zig). Windows karşılığı Job Object bellek/süreç limitleri (§C.4 köprüsü);
macOS aynı POSIX yüzeyi çalışır — üretim kanıtı yok, `[TASARIM]`.

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux (birincil) | Windows | macOS | ReactOS (Win32 sınıfı) | Mekanizma (§) |
|---|---|---|---|---|---|
| PHP | ✅ [KANIT] CI | ⚠️ chmod yalnız yazılabilirlik biti | ⚠️ [TASARIM] (Q4) | sınıf uyumu | A.1 |
| Python | ✅ | ⚠️ (ACL değil — doğrulama zorunlu) | ⚠️ [TASARIM] | — | A.2 |
| Go | ✅ (derleme zamanı GOOS) | ⚠️ chmod daraltma | ⚠️ [TASARIM] | — | A.3 · C.3 |
| Bash | ✅ | ❌ (yerel yok — turu §G.3) | ✅ (`stat -f` dalı) | — | A.4 · C.2 |
| PowerShell | ⚠️ pwsh (cross) | ✅ `icacls` | ⚠️ pwsh (cross) | ⚠️ | A.5 · C.4 |
| C# | ⚠️ `FileSecurity` Windows-sınıfı | ✅ | ❌ (ACL yok) | — | A.6 · B.4 |
| C | ✅ seccomp + setrlimit | ⚠️ (Win32 kolu `UNKNOWN`) | ⚠️ sınıf | ⚠️ | B.1 · C.1 |
| C++20 | ✅ RAII sarmalayıcı | ✅ (bağımlı) | ⚠️ [TASARIM] | — | B.2 |
| Rust | ✅ (`pre_exec` kavramı) | ✅ (sınıf) | ⚠️ [TASARIM] | — | B.3 |
| JS/Node | ✅ `fs.chmod` (POSIX mod) | ⚠️ yalnız yazılabilirlik biti (ACL yok) | ⚠️ [TASARIM] | — | A.7 |
| Swift | ✅ `FileManager` POSIX perms | ⚠️ CRT `chmod` sınıfı — doğrulanmadı | ✅ sınıf (Darwin) `[TASARIM]` | — | A.8 |
| Java/Kotlin | ✅ `Files.setPosixFilePermissions` | ❌ `UnsupportedOperationException` (ACL sınıfı) | ✅ JVM çapraz `[TASARIM]` | — | A.9 |
| Zig | ✅ POSIX + `setrlimit(.NOFILE)` | ❌ (POSIX yüzey — Win32 sınıfı değil) | ⚠️ POSIX `[TASARIM]` | — | C.6 |

**Okuma kuralı:** ✅ = örnek derlendiği sınıfı hedefler (`[ORİJİNAL ÖRNEK · derlenmedi]` etiketi
**kaldırılmaz**) · ⚠️ = sınıf geçerli ama kanıt yok · ❌ = sınıf dışı (kural gereği, hata değil).

---

## §4. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | seccomp/Job Object/App Sandbox uygulanacak mı? | UNKNOWN — ornekler §8 N1–N8: 0 isabet |
| O2 | macOS üretim/koşucu kanıtı (Q4) | UNKNOWN — App Sandbox sütunu [TASARIM] |
| O3 | C# `FileSecurity` .NET 5+ ACL paketi bağımlılık mı? (R6) | UNKNOWN — paket eklenmedi |
| O4 | capabilities(7) bounding set hangi üyeye düşürülecek? | UNKNOWN — [TASARIM], ölçülmüş değer yok |
| O5 | Üçüncü parti seccomp sarmalayıcısı kullanılacak mı? | UNKNOWN — R6: yalnız kavram |
| O6 | Sandbox dili olacak mı (tek sarmalayıcı)? | UNKNOWN — bu dosya politika sahibi, karar açık |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

---

## §5. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | Linux `chmod(2)` | https://man7.org/linux/man-pages/man2/chmod.2.html | 2026-10-14 ✅ |
| 2 | Linux `umask(2)` | https://man7.org/linux/man-pages/man2/umask.2.html | 2026-10-14 ✅ |
| 3 | Linux `seccomp(2)` | https://man7.org/linux/man-pages/man2/seccomp.2.html | 2026-10-14 ✅ |
| 4 | Win32 Job Objects | https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | 2026-10-14 ✅ |
| 5 | Apple App Sandbox | https://developer.apple.com/documentation/security/app_sandbox | 2026-10-14 ✅ |
| 6 | Linux `setrlimit(2)` | https://man7.org/linux/man-pages/man2/setrlimit.2.html | 2026-10-14 ✅ |
| 7 | Win32 `SetInformationJobObject` | https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject | 2026-10-14 ✅ |
| 8 | Windows `icacls` | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/icacls | 2026-10-14 ✅ |
| 9 | POSIX `chmod` (Open Group) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html | 2026-10-14 ✅ |
| 10 | Linux `capabilities(7)` | https://man7.org/linux/man-pages/man7/capabilities.7.html | 2026-10-14 ✅ |
| 11 | PHP `chmod()` | https://php.net/manual/en/function.chmod.php | 2026-10-14 ✅ |
| 12 | .NET `FileSecurity` sınıfı | https://learn.microsoft.com/en-us/dotnet/api/system.security.accesscontrol.filesecurity | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler örnek bloklarının **günü** doğrulanmış arayüz referansıdır;
blokların kendisi orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`).

---

## §6. İlişkili dosyalar

- Repo örnekleri (kanıt köprüsü): [`isletim-sistemi-security-ornekler.md`](isletim-sistemi-security-ornekler.md)
- Kapsam/sınır: [`isletim-sistemi-security-kapsam.md`](isletim-sistemi-security-kapsam.md)
- Doğrulama: [`isletim-sistemi-security-dogrulama.md`](isletim-sistemi-security-dogrulama.md)
- Dizin: [`index.md`](index.md)
- OS ailesi karşılaştırma (tekrar yok): `../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`
- Plan/limits çapraz köprü: `../isletim-sistemi-plan/isletim-sistemi-plan-kod-seti.md` (§GRUP C)
- Sandbox politika sahibi: **bu dosya** (§B.6) — diğer alt alanlar çapraz referans kullanır

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
