---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-ios
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-ios
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://docs.python.org/3/using/ios.html
  - https://docs.python.org/3/library/os.html
  - https://go.dev/doc/install/source
  - https://nodejs.org/api/os.html
  - https://doc.rust-lang.org/rustc/platform-support.html
  - https://docs.rs/nix/latest/nix/
  - https://learn.microsoft.com/en-us/dotnet/what-is-maui
validation:
  - "2026-10-14: 7 kaynak URL GET 200 ile dogrulandi; C/PHP satirlari iOS upstream'i dogrulanamadigi icin, JS satiri os.platform() kumesinde 'ios' bulunmadigi icin ornek yazmadan VERIFICATION REQUIRED birakildi; repo olcusu 418 PHP+JS / 0 *.c,*.go,*.cs / grep 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — iOS (7 Dil)

## §0 ELI15

Bu kart, **iOS'ta 7 dil bölümünde** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını taşır: gömülü (embedded) çalışma modu, hedef mimari seçimi, dar POSIX DAC.
Hepsi `# kaynak: upstream docs` imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**, iOS güvenlik API isabeti **0** (negatif kanıt, §3).
> Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

> **⚠️ alt-yüzey (kısmi):** iOS sandbox / `com.apple.security.*` entitlement yüzeyi bu turda
> **doğrulanamadı** (`support.apple.com/guide/security/sandbox` redirect/TOC) → o alt-yüzey
> yazılmadı; uygulama-kapsamı (app container) modeli yalnız upstream cümleleriyle taşınır.

## §1 OS güvenlik profili × dil matrisi

| Dil | iOS güvenlik yüzeyi | Bu dosyadaki mekanizma | Upstream sınıfı |
|---|---|---|---|
| C | — | iOS'e özgü doğrulanmış upstream **yok** | — → ⚠️ |
| Python | gömülü mod sınırları | embedded mode; `sys.platform == 'ios'`; `subprocess` YOK | docs.python.org |
| Go | `GOOS=ios` + tek mimari | `//go:build ios && arm64` (ios = arm64 only) | go.dev/doc/install/source |
| JS (Node) | — | `os.platform()` değer kümesinde `ios` **YOK** (negatif) | nodejs.org/api → ⚠️ |
| PHP | — | iOS'e özgü doğrulanmış upstream **yok** | — → ⚠️ |
| Rust | hedef desteği + POSIX DAC | `aarch64-apple-ios` Tier 2 + std ✓; `PermissionsExt(0o600)` | doc.rust-lang.org + docs.rs/nix |
| C# | MAUI hedef listesi | Android/iOS/macOS/Windows hedefleri | learn.microsoft.com |

**Okuma:** 4 satır ✅ (üçü kısmi) + 3 satır ⚠️ (C, JS, PHP); hiçbir satır "repoda uygulanıyor"
iddiası **taşımez** — hepsi hedef referanstır (§3).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 4 bölümün **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (üretime alınabilir komut **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — C: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14):
> iOS için C diline özgü doğrulanmış upstream doküman bu turda **bulunamadı** (Apple
> sandbox sayfası redirect/TOC; NDK benzeri bir C yüzeyi iOS için okunamadı) → uydurma
> çağrı/ad yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.2 — Python: gömülü mod + mobil sınırı (referans)

```python
# kaynak: upstream docs - docs.python.org/3/using/ios.html
# kaynak: upstream docs - docs.python.org/3/library/os.html
import sys

# using/ios.html: "Python on iOS is unlike Python on desktop platforms";
# "The only unit of software distribution is an 'app'"; "there is no console"
# -> tek kullanim GOMULU MOD (yorumlayici bir app icine gomulur)
assert sys.platform == 'ios'          # iOS'a ozel platform degeri (upstream)

# os.html: subprocess "Availability: not Android, not iOS, not WASI. This module
# is not supported on mobile platforms" -> surec baslatma yuzeyi iOS'ta YOKTUR
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `sys.platform` iOS dalı 0 isabet (418 dosya, 2026-10-14) |

### §2.3 — Go: `ios` hedef etiketi — tek mimari (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source
//   GOOS kumesinde 'ios' vardir; ios hedefi YALNIZCA arm64 mimarisiyle gelir
//   (upstream: GOARCH listesi ios icin arm64)
//go:build ios && arm64

package main // yalniz iOS hedefinde derlenir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.4 — JS (Node.js): ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> `nodejs.org/api/os.html` — `os.platform()` değer kümesi `aix|darwin|freebsd|linux|openbsd|
> sunos|win32` (+ deneysel `android`); `ios` **LISTEDE YOK** → Node'un iOS platformu
> doğrulanamadı, uydurma platform adı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Node iOS kullanımı zaten yok (§3) |

### §2.5 — PHP: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14):
> php.net'te iOS'e özgü (Apple platform) upstream sayfa bu turda **doğrulanamadı** →
> `posix_*` çağrıları iOS'ta mevcut mu bilinmez; mevcutmuş gibi kod yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da PHP iOS kullanımı zaten yok (§3) |

### §2.6 — Rust: dar izin + hedef desteği (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html
use std::fs::{File, Permissions};
use std::os::unix::fs::PermissionsExt;

// hedef uc: aarch64-apple-ios -> rustc platform-support: Tier 2, std ✓ (full stdlib)
// dar izin: yalnizca sahip okuma/yazma (0o600) - uygulama kapsi icindeki dosya kalibi
let file = File::create("cache.dat")?;
file.set_permissions(Permissions::from_mode(0o600))?;
```

not: `nix` (0.31.3) platform listesi `aarch64-apple-ios` içerir → `nix::unistd` bu hedefte
mevcuttur (docs.rs/nix).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.rs` 0 dosya; `0o600` yalnız hedef referans |

### §2.7 — C#: MAUI hedef listesi (referans)

```csharp
// kaynak: upstream docs - learn.microsoft.com/en-us/dotnet/what-is-maui
// .NET MAUI — tek kod tabani ile hedefler (upstream hedef listesi BIREBIR):
//   Android · iOS · macOS · Windows
// iOS hedefi buradan DOGRULANIR; entitlement/sandbox yuzeyi bu sayfada
// ANLATILMAZ -> o alt-yuzey bu kartta yazilmaz (S1 acik)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — C# kaynak yok (`.cs` 0 dosya); yalnız hedef referans |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`ios|embedded|sys.platform|PermissionsExt|0o600|entitlement` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| iOS güvenlik API çağrısı (entitlement/sandbox) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| `sys.platform` iOS dalı | grep | **0 isabet** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'sys.platform|entitlement|PermissionsExt').Count     # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. iOS OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız iOS dosya/izin/hedef kalıpları ele alınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; Apple platform geneli → `cekirdek-guvenlik-ios` · `cekirdek-guvenlik-macos`.

Ek sınır: App Store politikası, provisioning ve entitlement imzalama bu dala yazılmaz;
`subprocess`/süreç başlatma iOS'ta bulunmadığından (upstream) "sandbox kaçış" senaryoları
bu kartta kurgulanmaz. `0o600` bir DAC ayarıdır, "kaps koruması" diye adlandırılması
abartıdır (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.python.org/3/using/ios.html | gömülü mod tek kullanım; "no console"; dağıtım birimi = app; en düşük iOS 13.0 | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/os.html | `subprocess` "not iOS"; `os.uname()` iOS'ta 'Darwin'; `getresuid` "not macOS, not iOS" | HTTP 200 · içerik okundu |
| https://go.dev/doc/install/source | GOOS kümesinde `ios`; ios = arm64 tek mimari | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer kümesi — `ios` **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/rustc/platform-support.html | `aarch64-apple-ios` Tier 2 + std ✓ | HTTP 200 · içerik okundu |
| https://docs.rs/nix/latest/nix/ | nix 0.31.3 platform listesi `aarch64-apple-ios` içerir | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/what-is-maui | MAUI hedefleri: Android, iOS, macOS, Windows | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `support.apple.com/guide/security/sandbox` → HTTP 200 ama
> **redirect/TOC** — sandbox detay metni gelmedi → entitlement/sandbox kalıbı yazılmadı ·
> `developer.apple.com/documentation/security/...` → **404** (2026-10-14). İkisi de hiçbir
> bloğa girmedi.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | iOS güvenlik katmanı (kaps DAC/entitlement) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (kaps izni mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode