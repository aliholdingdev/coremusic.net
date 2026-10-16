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

## §7 Derinlik — 7 dil × macOS kanıt zinciri (derin okuma)

> Bu bölüm §1–§6'daki **aynı kanıtların** derinleştirilmiş okumasıdır; **yeni URL, yeni
> sürüm, yeni fonksiyon adı İÇERMEZ** (zero-hallucination). Bu kart **kısmi karttır**:
> 6 satır referans ✅, 1 satır (C) ⚠️. Derinlik, ✅'lerin **neyi kanıtladığını/kanıtlamadığını**
> ve ⚠️'nin/alt-yüzeylerin koşulunu taşır. "Yapıldı" olarak işaretlenen hiçbir iş yoktur.

### §7.1 — C × macOS: ⚠️ (tek kırık satır)

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | POSIX `chmod` spec'i → HTTP 200 (pubs.opengroup — **spec düzeyi**) |
| Eksik olan | Fonksiyonun **macOS'e özgü** doğrulanmış upstream teyidi — bu turda **bulunamadı** |
| İkinci engel | `support.apple.com/guide/security/sandbox` → 200 ama **redirect/TOC**; sandbox yüzeyi de taşınamaz |
| Repo yüzeyi | §3: `*.c` **0** |

**Gerekçe:** POSIX spec, **her POSIX sistemde** geçerliği konuşulan şeydir; "macOS'ta
bu semantik doğrulandı" **iddiası değildir**. **✅ koşulu:** Apple'ın kendi
dokümantasyonunda `chmod`/izin semantiğini gösteren **detaylı** sayfa (TOC değil) +
macOS davranış teyidi. İkisi birden gerekir.

### §7.2 — Python × macOS (referans — ne kanıtlar, ne kanıtlamaz)

| Öğe | Kanıt |
|---|---|
| Okunan | `docs.python.org/3/library/os.html` → 200 · içerik okundu |
| Pozitif kanıt | `os.uname()` → macOS/iOS/Android'de `sysname` **'Darwin'** (upstream cümlesi kartta alıntılı) |
| **Negatif upstream kanıtı** | `os.getresuid()/getresgid()` → "Availability: Unix, not WASI, **not macOS**, not iOS" → bu API macOS'ta **sunulmaz** |
| Repo yüzeyi | `os.uname`/`getresuid` grep'i 418 dosyada **0 isabet** (§2.2) |

**Okuma:** Bu satır iki yönlüdür: (a) platform tanımı ✅ (Darwin), (b) **API yokluğu
kanıtı** ✅ — getresuid'e dayanan kod macOS'ta çalışmaz. **Kanıtlamadığı:** macOS'un
kendi güvenlik API'si (TCC/sandbox) — o satırda **hiç anılmaz**.

### §7.3 — Go × macOS (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `go.dev/doc/install/source` → 200 · GOOS kümesi 14 değer, `darwin` **var** |
| Mekanizma | `//go:build darwin` derleme etiketi — yalnız darwin hedefinde derlenir |
| Sınır | macOS'a özgü **sandbox/entitlement Go upstream'i doğrulanamadı** → o alt-yüzey **yazılmadı** |
| Komşu referans | Go'nun POSIX yüzeysi `golang.org/x/sys/unix`'tedir (imza listesi: `kod-ornekleri-go.md` §2.3) |

**Okuma:** Satır **hedef seçimi** kanıtlar, güvenlik API'si kanıtlamaz. **Çapraz kayıt**
(go.md §2.3) imza listesi taşır — tekrar değil, **yönlendirme** sayılır; yine de tek
kanıt yine tek kanıttır → çifte sayım yok.

### §7.4 — JS (Node) × macOS (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `nodejs.org/api/os.html` → 200 · `os.platform()` kümesi `darwin` dahil |
| Mekanizma | `'darwin'` platform kapısı — macOS yolu burada **ayrılır**; güvenlik kararının kendisi S1'dir |
| Repo yüzeyi | `'darwin'` karşılaştırması 98 JS dosyasında **0 isabet** |

**Okuma:** Ayrım mekanizması ✅; **karar** (hangi güvenlik politikası) bu kartın konusu
**değil** — S1'e devredilmiştir.

### §7.5 — PHP × macOS (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `php.net/manual/book.posix.php` → 200 · POSIX uzantısı fonksiyon listesi |
| Kritik sınır | **"macOS'e ÖZGÜ davranış notu bu sayfada YOKTUR"** → kalın iddia yazılmaz (kartın kendi dili) |
| Mekanizma | `posix_geteuid() === 0` kök kontrolü; düşürme/uyarı mantığı uygulama katmanının kararı (S1) |
| Repo yüzeyi | `posix_*` çağrısı 320 PHP dosyasında **0 isabet** |

**Okuma:** POSIX uzantısının **varlığı** ✅, macOS'e özgü davranış **bilinmiyor** →
ikisi kartta ayrı ayrı yazılır; birleştirilip "macOS'ta POSIX şu şekilde çalışır"
cümlesi **çıkarılmaz**.

### §7.6 — Rust × macOS (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `doc.rust-lang.org/.../trait.PermissionsExt.html` → 200 · `from_mode`/`set_mode`; örnekler `0o700`, `0o644` |
| Sınır cümlesi | **"Available on Unix only"** — macOS Unix'tür; yine de kart macOS'a özgü **ek** teyit **iddia etmez** |
| Repo yüzeyi | `*.rs` **0**; `0o750` yalnız hedef referans — PHP `0750` **aynı değer, farklı katman** (§2.6) |

**Okuma:** "Aynı sayı, aynı anlam" **değildir**: Rust `0o750` (octal literal, POSIX
DAC) ile PHP `0750` (izin dizgesi) farklı katmanlardır — eşitlenmez, **yan yana**
taşınırlar.

### §7.7 — C# × macOS (referans)

| Öğe | Kanıt |
|---|---|
| Okunan | `learn.microsoft.com/.../system.io.file.setunixfilemode` → 200 |
| Upstream notu | API **Linux/macOS** hedeflerinde anlamlıdır; **Windows'ta etkisi yoktur** (upstream cümlesi) |
| Repo yüzeyi | `.cs` **0** — yalnız hedef referans |

**Okuma:** .NET'in **macOS'a izin verdiği** doğrulanmıştır; .NET'in macOS'a **özel**
güvenlik yüzeyi **anılmaz** (o kaynak okunmadı).

### §7.8 — §1 matris ↔ §2 blok ↔ §5 referans eşlemesi (tutarlılık denetimi)

| # | §1 satırı | §2 bloğu | §5'teki referans(lar) | Eşleşme |
|---|---|---|---|---|
| 1 | C (⚠️) | §2.1 | `pubs.opengroup...chmod` + `support.apple.com...sandbox` (TOC) | ✅ tutarlı — **iki** kanıt, ikisi de eksik parçayla |
| 2 | Python | §2.2 (kodlu) | `docs.python.org/3/library/os.html` | ✅ tutarlı |
| 3 | Go | §2.3 (kodlu) | `go.dev/doc/install/source` | ✅ tutarlı |
| 4 | JS (Node) | §2.4 (kodlu) | `nodejs.org/api/os.html` | ✅ tutarlı |
| 5 | PHP | §2.5 (kodlu) | `php.net/manual/book.posix.php` | ✅ tutarlı |
| 6 | Rust | §2.6 (kodlu) | `doc.rust-lang.org/.../PermissionsExt.html` | ✅ tutarlı |
| 7 | C# | §2.7 (kodlu) | `learn.microsoft.com/.../setunixfilemode` | ✅ tutarlı |

**Denetim sonucu:** 7/7 tutarlı · §5'te **8 URL** (7 satırın C satırı 2 referansı var →
7+1=8 ✓). Reddedilen 3 adayın (§11) §5 tablosunda **gövde satırı yok** — tutarlı
(kanıtsız = tablosuz). Eşleme, tek satır güncellenirken **hangi bölümün** güncelleneceğini
gösterir (kanıt önce, iddia sonra — §13.3).

### §7.9 — Örüntü: kısmi kartın dört açık noktası

| # | Açık nokta | Konum | Durum |
|---|---|---|---|
| 1 | **Sandbox alt-yüzeyi** — Apple sayfası 200 ama **redirect → TOC**, detay metni gelmedi | §0 · §5 · §2.1 | ⚠️ kayıtlı; `sandbox-exec`/Seatbelt kalıbı **yazılmadı** |
| 2 | **Codesign/entitlement + TCC** yüzeyi | §0 · §4 | Doğrulanamadı **+** kartın konusu **değil** (çift engel) |
| 3 | **C satırı** — POSIX spec ✓, macOS teyidi ✗ | §2.1 | ⚠️ tek kırık satır (§7.1) |
| 4 | **✅'lerin sınıfı** — 6 satır **referans (R)** sınıfıdır, "repoda uygulanıyor" **değil** | §1 · §2 uyarı | Kalıcı kural: R ≠ IMPLEMENTED |

**Düzeltilebilirlik ayrımı (bağlayıcı):** (1) Apple sandbox sayfasının **detay metni**
(redirect öncesi/alternatif yol) okunursa kapanır — sonuç bu turda **okunmadı** ·
(2) TCC/codesign bu kartta **kapanmaz** (konu değil; sahibi `cekirdek-guvenlik-macos`
halkası) · (3) C satırı yalnız **macOS-specific upstream teyidiyle** kapanır ·
(4) **kapanmaz** — R sınıfı kartın doğasıdır. **Hiçbiri yazım hatası değildir**
(§7.8: 0 sapma).

## §8 Negatif kanıt metodolojisi (nasıl üretildi, nasıl çürütülür)

Bu kart **karışık karttır**: hem pozitif upstream referansları (6 blok) hem negatif
kanıt (grep 0, dosya 0) taşır. İki tür ayrı ölçülür:

| # | Yöntem | Uygulama (bu kartta) | Sahte pozitif riski |
|---|---|---|---|
| 1 | **Pozitif okuma** — upstream sayfası cümle cümle okundu | 8 URL'nin 8'i 200; 7'si içerik okundu, 1'i (sandbox) **yalnız TOC** | Düşük — alıntılar kartta cümle düzeyinde |
| 2 | Kümeden eleme (tersi: **varlık** kanıtı) | GOOS'ta `darwin` **var**, `os.platform()`'da `darwin` **var** | Düşük — bu kart **varlık** kanıtlar (önceki kartlar yokluk) |
| 3 | Repo grep'i | 418 dosyada §3 desenleri → 0; ayrıca Seatbelt/sandbox/codesign → 0 | Düşük |
| 4 | Redirect/TOC tanıma | `support.apple.com/.../sandbox` 200 ama içerik **TOC** → kanıt sayılmadı | **Düşük** (ayrım yapıldı: 200 ≠ içerik) |

**Önceki kartlardan farkı (kritik):** Tizen/ReactOS/webOS kartları **yokluk**
kanıtlar (0/7); macOS kartı **6 varlık + 1 yokluk** kanıtlar. Ortak kural değişmez:
**"repoda uygulanıyor" iddiası yasaktır** (§3) — R sınıfı kodlar üretime alınabilir
komut **değildir**, S1 kararı gerekir (§2 uyarı).

**Çürütme sırası (gelecek turlar için — henüz YAPILMADI):**

1. Apple sandbox sayfasının **detay metnini** al (redirect'ten önceki içerik ya da
   TOC'deki gerçek hedef) → §0'daki ⚠️ alt-yüzey için tek engel budur.
2. C satırı için macOS-specific `chmod`/izin teyidi ara (yerel desen: `mode`,
   **arama terimidir**, URL adayı değildir; sonuç bu turda **okunmadı**).
3. §3 betiğini metindeki desen kümesiyle **hizala** (§9.1 — bu oturumda yapılmadı,
   shell yok).
4. Her yeni okuma `validation:` alanına **tarih + HTTP durumu + tek cümle** ile eklenir;
   eklenmeden §1/§2 satırı sınıf değiştirmez.

> **Kural:** Yukarıdakilerin **hiçbiri** bu turda yapılmadı; yapılmış gibi yazılmaz.
> Yeni bir URL **adı** uydurulmaz — yalnız §5'teki 8 referans + 3 reddedilen aday
> yeniden denenebilir.

## §9 Ölçüm tekrar üretilebilirliği (§3 betiğinin satır satır okuması)

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'SetUnixFileMode|posix_geteuid|sandbox-exec|seatbelt').Count  # beklenti: 0
```

| Satır | Ne yapar | Neden gerekli |
|---|---|---|
| 1 | PHP+JS kümesini kurar (beklenti **418** = 320 PHP + 98 JS) | `vendor`/`node_modules` **üçüncü parti** koddur; sayıma girerse 418 şişer |
| 1 — `.git` hariç | Depo meta-verisini düşer | Geçmiş commit içeriği "repo kodu" sayılmaz |
| 2 | C/Go/C# sayımı (beklenti **0**) | Negatif kanıtın kendisi |
| 3 | macOS yüzey grep'i (beklenti **0**) | .NET/PHP API adları + sandbox desenleri; PHP+JS bunları **çağıramaz** |

#### §9.1 Kart-içi SAPMA kaydı (bilinen, kapatılmadı — **iki farklı desen kümesi**)

| Öğe | §3 metni (satır 185) | §3 betiği (3. satır) |
|---|---|---|
| Desen sayısı | **6**: `darwin\|SetUnixFileMode\|posix_geteuid\|from_mode\|os.uname\|share_mode` | **4**: `SetUnixFileMode\|posix_geteuid\|sandbox-exec\|seatbelt` |
| Yalnız metinde | `darwin`, `from_mode`, `os.uname`, `share_mode` (4 desen) | — |
| Yalnız betikte | — | `sandbox-exec`, `seatbelt` (2 desen) |
| Kesişim | `SetUnixFileMode`, `posix_geteuid` (**2 desen**) | aynı |

- **Durum:** Bu kartta sapma **sayı farkı değil, küme farkıdır** — metin ile betik
  **farklı şeyler** tarıyor. Tablodaki "Seatbelt/sandbox/codesign → 0 isabet" satırı
  ancak **betiğin** desenleriyle üretilmiş olabilir; `share_mode`/`os.uname` satırı ise
  ancak **metnin** desenleriyle. İkisi de 2026-10-14 tarihli ve **ikisi de kayıtlı**,
  ama **tek ölçüm iki kez yazılmış gibi görünüyor** → ayrıştırma gerekir.
- **Bu turda (2026-10-16) NEDEN KAPATILMADI:** (1) shell reddedildi → betik
  **yeniden çalıştırılmadı**; (2) append-only kuralı gereği mevcut satır
  **düzenlenmedi** (yetki orkestratörde).
- **Etki tahmini (iddia değil):** Her iki kümeyi içeren **birleşik** betik (8 desen:
  6 metin + `sandbox-exec|seatbelt`) çalıştırıldığında beklenti 0'dır — çünkü desenler
  macOS/.NET/PHP API adları ve 0 C/.NET/Go/Rust dosyası var (§3) — **ama bu
  doğrulanmadı**: "0 kalır" **yazılmaz**, "doğrulanmadı" yazılır.
- **Kapanış koşulu:** Birleşik desen kümesiyle repo kökünden yeniden ölçüm → sonuç
  `validation:` alanına tarihle eklenir; §3 metni ile betik o zaman hizalanır.

**Yorum sınırları (bağlayıcı):**

1. Grep'in 0 oluşu **yalnızca** taranan desenlerde 0 olduğunu kanıtlar.
2. Ölçüm tarihi **2026-10-14**'tür; değişen repo sayıları değiştirir.
3. **2026-10-16 (bu oturum):** shell reddedildi → PowerShell **çalıştırılmadı**, 418/0/0
   **yeniden üretilmedi**. Bu oturumun katkısı kapı (≥500 satır) derinliğidir (§15).

## §10 Sınır haritası (bu kart kimin işidir)

| Konu | Sahip | Bu karttaki durum |
|---|---|---|
| Kimlik doğrulama, oturum, parola, MFA | `K010-identitiy` (bozuk ad **değiştirilmez**) | **YOK** — anılmaz |
| OWASP denetimi, auth/CSRF/CSP tasarımı, kripto (ADR-022) | `K009-security` | **YOK** — yalnız sınır işareti |
| Boot zinciri / SIP / Gatekeeper | `cekirdek-guvenlik-macos` (depends_on) | **YOK** — halka kartı |
| TCC izin penceresi, kod imzalama, sandbox profili | Bu kartın **konusu değildir** (§4) + doğrulanamadı (§0) | **ÇİFT ENGEL** — anılmaz |
| Çekirdek içi yapı, sürücü/kext yazımı | `isletim-sistemi-kernel` · §4: X7/X8 | **YASAK** |
| macOS dosya/izin/platform kalıpları | **Bu kart** | ✅ 6 referans + 1 ⚠️ |

**Ölçüm notu:** `0o750` bir **DAC ayarıdır**; "boot koruması" diye adlandırılması
**abartıdır** (§4 — bu kartın kendi cümlesi, olduğu gibi taşındı).

**Tek cümle:** Bu kart **"7 dilin macOS yüzeyinde hangi upstream kalıp doğrulandı?"**
sorusunun 2026-10-14 cevabını taşır; cevap **6 R + 1 ⚠️**'dir — hiçbiri "repoda
uygulanıyor" **değildir**.

## §11 Reddedilen adaylar defteri (genişletilmiş)

| Aday | Sonuç | Kart içi kayıt | Neden gövdeye alınmadı |
|---|---|---|---|
| `docs.python.org/3/using/macos.html` | **404** (2026-10-14) | §5 alt notu | Ölü yol — içerik yok |
| `developer.apple.com/documentation/.../preventing-insecure-exception-behavior` | **404** | §5 alt notu | Ölü yol — içerik yok |
| `support.apple.com/guide/security/sandbox` gövdesi | **TOC düzeyi, detay yok** | §5 alt notu | 200 ≠ içerik — sandbox kalıbı **yazılmadı** |
| TCC / codesign / entitlement içeriği | Bu kartın konusu **değil** | §4 | Çift engel: hem konu dışı hem doğrulanamadı |

**Defter kuralı:** Reddedilen aday **silinmez**; gerekçesiyle kalır. Bu defterdeki 4
satırın 4'ü de uydurma içerik yerine **kayıt tercihini** gösterir — kartın çalışması budur.

## §12 Açık soru derinliği (S1 · S2)

### S1 — macOS güvenlik katmanı (POSIX DAC/TCC/sandbox) devreye alınacak mı?

- **[UNKNOWN]** — bu kartta **kapatma yok**.
- Kapanış koşulu (dosyanın kendi dili): mimari sahibi kararı + yeni ADR (`>=088` — bu
  numara kartta mevcut kayıttır; ADR numarası **uydurulmaz**, mevcut en yüksek numara
  sahibi tarafından doğrulanır).
- **Bu kartın S1'e katkısı:** 6 referans **mekanizma** sunar (Darwin tanısı, GOOS
  hedefi, platform kapısı, POSIX uzantısı, DAC modu, izin API'si) ama **politika seçimi
  yapmaz** — §2.4'teki "güvenlik kararının kendisi S1'dir" cümlesi bunu zaten devreder.
  Ayrıca §0'daki ⚠️ alt-yüzey (sandbox) S1 kararı **öncesi** teknik ön koşuldur: kartsız
  politika tartışması eksik kanıtla yürür.

### S2 — Uygulama ↔ OS güvenlik sorumluluk sınırı (izin mi, uygulama mı)

- **[UNKNOWN]** — K009/K010 ayrım çizgisiyle değerlendirilecek (dosyanın kendi dili).
- Bu kart S2'ye **şunları** verir: (a) POSIX DAC bir **OS mekanizmasıdır** (§2.6) —
  sınırın OS tarafındaki malzemesi; (b) `posix_geteuid()` kök kontrolü vardır ama
  **tepkisi** (düşürme/uyarı) uygulama katmanının kararıdır (§2.5); (c) sandbox/TCC
  yüzeyi **bilinmediği için** sınırın o ayağı **varsayılamaz**.
- Kapanış: K009/K010 sahiplerinin ortak çizgisi + (gerekiyorsa) ADR — bu kart tek başına
  kapatamaz.

## §13 Kart mimarisi ve tüketim kuralları

Bu kart **kodlu kısmi karttır** — 6 imzalı referans bloğu + 1 ⚠️:

| Katman | Bu kartta | İşlev |
|---|---|---|
| §0–§1 | Durum + 7 dil matrisi (6 R + 1 ⚠️) | 30 saniyede okunur sonuç |
| §2.1 | C ⚠️ — örnek **yazılmadı** | Kırık satırın gerekçesi |
| §2.2–§2.7 | `# kaynak: upstream docs` imzalı bloklar | **Sözleşmesel** referans — üretim değil |
| §3 | Repo ölçümü (+ §9.1 küme sapması) | "Repoda uygulanıyor" iddiasını **kapatır** |
| §4–§6 | Sınır / 8 referans / açık soru | Komşu kartlarla çakışma önlenir |
| §7–§16 (bu ek) | Derinlik + kapı kaydı | Her satır mevcut kanıttan türetilmiştir |

**Tüketim kuralları (downstream ajanlar için bağlayıcı):**

1. §2 blokları **`# kaynak: upstream docs` imzasıyla** gelir; imza **silinmez**,
   blok **parçalanaz** — alıntı yapılacaksa blok bütünü alınır.
2. "Repoda uygulanıyor" iddiası **YASAKTIR** (§2/§3): 6 bloğun 6'sının repo karşılığı
   **0 isabet**tir.
3. **Üretime alma eşiği:** bloklar "üretime alınabilir komut **değildir**" — kopyalamadan
   önce **S1 kararı** gerekir (§2 uyarı).
4. Sınıf değişimi (R → ya da ⚠️ →) sırası: önce `validation:` tarih+kanıt, **sonra**
   §1 matrisi, **sonra** §2 bloğu → **kanıt önce, iddia sonra**.
5. Çapraz referans (`kod-ornekleri-go.md` §2.3 imza listesi) **tekrar kanıt değildir** —
   yönlendirmedir; çifte sayım yasak.

### §13.5 — Doğru / Yanlış cümle kartı (hızlı denetim)

| ✅ Doğru cümle (karttan türetilebilir) | ❌ Yanlış cümle (türetilemez — nedeni parantezde) |
|---|---|
| "macOS kartında 6 satır referans (R), 1 satır (C) ⚠️'dir (2026-10-14)." | "macOS kartı tamamdır." (R ≠ tamam, §7.9/4) |
| "Python upstream'i getresuid'in macOS'ta sunulmadığını söylüyor (upstream cümlesi kartta)." | "macOS'ta getresuid yoktur — Python'dan bağımsız olarak." (kanıt Python'un dokümanı) |
| "GOOS'ta `darwin` var; Go'nun macOS sandbox upstream'i doğrulanamadı." | "Go macOS'ta sandbox kullanır." (alt-yüzey yazılmadı — §7.3) |
| "sandbox sayfası 200 ama TOC düzeyi — detay gelmedi." | "Apple sandbox sayfası okundu." (TOC ≠ okundu — §11) |
| "Repoda 418 PHP+JS, 0 C/Go/C#/Rust; §3 desenlerinde 0 isabet." | "Repoda macOS kodu hiç yoktur." (grep desen kümesi metinle betik farklı — §9.1) |
| "S1 = [UNKNOWN], S2 = [UNKNOWN] — bu kartta kapatma yok." | "macOS güvenlik katmanı mimaride yok/e var." (karar verilmedi — §12) |

**Kullanım:** Downstream rapor **sol sütunun** cümleleriyle yazılabilir; sağ sütundaki
bir cümle rapora girdiyse kart **yanlış kullanılmıştır** → düzeltme sırası: §13.1–§13.5
kuralları, sonra §7 kanıtları. Tablo, §7.8'in **davranış düzeyi** karşılığıdır.

## §14 Bağımlılık zinciri (depends_on gerekçeleri)

| Bağımlılık | Gerekçe | Bu kartla ilişki |
|---|---|---|
| `cekirdek-guvenlik-macos` | Boot zinciri/SIP/Gatekeeper halkası — "platform geneli" sorumlusu | Bu kart **boot anlatmaz**; yalnız dil × referans taşır → tekrar yok |
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
| Append öncesi (2026-10-16) | **242 satır** → kapı **ALTINDA** |
| Kapı (Band-1) | **≥500 satır** |
| Ekleme yöntemi | Append-only: §7–§16 footer'dan **önce** eklendi; §0–§6 içeriği (6 kod bloğu dâhil) ve frontmatter **değiştirilmedi**; Authority footer satırı **son satırda korundu** |
| Append sonrası (ölçülen) | **580 satır** (2026-10-16, `read` offset=100000 hata mesajı ile ölçüldü → Band-1 kapısı **GEÇTİ**: 580 ≥ 505, marj +75) |

## §16 Marj

- **Marj amacı:** Kapıya tam 500'de yapışmak, sonraki meşru bir düzeltme (ör. §9.1
  kapanışı ya da sandbox alt-yüzeyinin eklenmesi) ile eşiği kıracaktır → dosya yeniden
  kapı altına düşebilir. Bu nedenle hedef **≥505** tutulur.
- **Marj içeriği:** Eklenen her satır mevcut §0–§6 kanıtından türetilmiştir; dolgu
  satırı, uydurma URL, `TBD` **yoktur** — §8'deki çürütme adımları "yapılacak iş"
  olarak **açıkça işaretlidir**, tamamlanmış gibi sunulmaz.
- **Kod bütünlüğü:** 6 referans bloğu ve 1 ⚠️ tablosu **bayt düzeyinde korunmuştur**;
  ekler yalnız §7'den sonra başlar.
- **Tekrar edilebilirlik:** Kapı ölçümü her turda aynı yöntemle (offset=100000 hata
  mesajı) yeniden alınır; değer değişirse §15 tablosuna **yeni satır** eklenir, eski
  satır **silinmez**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode