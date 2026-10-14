---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-rust
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
evidence:
  - https://doc.rust-lang.org/rustc/platform-support.html
  - https://doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html
  - https://doc.rust-lang.org/std/os/unix/net/struct.UnixListener.html
  - https://doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html
  - https://docs.rs/nix/latest/nix/
  - https://docs.rs/nix/latest/nix/unistd/
  - https://docs.rs/libseccomp/
  - https://docs.rs/ndk/latest/ndk/
  - https://docs.rs/windows/latest/windows/
validation:
  - "2026-10-14: 9 kaynak URL GET 200 ile dogrulandi; 8 kombinasyondan 3'u (tizen/webos/reactos) ornek yazmadan VERIFICATION REQUIRED birakildi; repo olcusu 418 PHP+JS / 0 *.rs / grep 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Rust (Dil × 8 OS)

## §0 ELI15

Bu kart, **Rust'ın 8 işletim sistemi kombinasyonunda** (windows, linux, macos, ios, android,
tizen, webos, reactos) OS güvenlik kalıplarını taşır: POSIX izin, paylaşım bayrakları,
seccomp filtresi, UID denetimi. Hepsi `# kaynak: upstream docs` imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.rs`**, 0 `Cargo.toml`, 0 Rust güvenlik API isabeti (negatif kanıt, §3). Kart **hedef
> mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

Doğrulama: 9 upstream URL bugün (2026-10-14) GET 200; 3 kombinasyon `⚠️ VERIFICATION REQUIRED`
bırakıldı (§2.6–§2.8), reddedilen adaylar §5'te.

## §1 Dil × OS matrisi (8 OS)

| OS | Rust güvenlik yüzeyi | Doğrulanmış upstream (2026-10-14) | Durum |
|---|---|---|---|
| windows | `OpenOptionsExt::share_mode` / `security_qos_flags` (CreateFile dwShareMode, impersonation) | doc.rust-lang.org `OpenOptionsExt` | ✅ |
| linux | seccomp filtresi (`libseccomp`) + `nix::unistd` + `UnixListener` | docs.rs/libseccomp · docs.rs/nix · doc.rust-lang.org | ✅ |
| macos | POSIX DAC `PermissionsExt` (0o750); hedef üçlü Tier 1 (`aarch64-apple-darwin`) | doc.rust-lang.org PermissionsExt · platform-support | � (codesign yüzeyi ⚠️) |
| ios | POSIX DAC `PermissionsExt`; `aarch64-apple-ios` Tier 2 + std ✓; `nix` platform listesi | platform-support · docs.rs/nix | ✅ (entitlement yüzeyi ⚠️) |
| android | `nix::unistd::{getuid,geteuid}` UID denetimi; `aarch64-linux-android` Tier 2 + `ndk` | docs.rs/nix/unistd · docs.rs/ndk · platform-support | ✅ |
| tizen | — | platform-support'ta `tizen` hedef üçlüsü **yok** | ⚠️ VERIFICATION REQUIRED |
| webos | — | platform-support'ta `webos` hedef üçlüsü **yok** | ⚠️ VERIFICATION REQUIRED |
| reactos | — | `windows` crate platform satırı yalnız `x86_64-pc-windows-msvc` | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 5 ✅ + 3 ⚠️ (kaynak yok → örnek yazılmaz, uydurma crate/ad üretilmez). macOS/iOS satırındaki ⚠️ yalnız **alt-yüzeyi** işaretler (codesign/entitlement kaynağı okunamadı).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 5 bloğun **hiçbiri bu repodan alınmamıştır**; hepsi `# kaynak:
> upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır (çalıştırılabilir kod değildir —
> imza satırı şablon konvansiyonudur). Repo karşılığı §3'te negatif kanıttır; kopyalamadan önce S1 kararı gerekir.

### §2.1 — Windows: dosya paylaşım ve kalite-of-service bayrakları (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html
use std::fs::OpenOptions;
use std::os::windows::prelude::*;

// share_mode(0): acikken baska sureclerin okuma/yazma/silmesini engelle
// (varsayilan: FILE_SHARE_READ|FILE_SHARE_WRITE|FILE_SHARE_DELETE)
let file = OpenOptions::new().write(true).share_mode(0).open("session.dat")?;

// security_qos_flags: named pipe'da impersonation seviyesi belirtilmezse kotu niyetli
// program, imtiyazli surecin yetkisini kazanabilir (upstream uyari metni).
// not: SECURITY_IDENTIFICATION upstream ornekte winapi'dendir; winapi docs DOGRULANMADI
let pipe = OpenOptions::new().write(true).create(true)
    .security_qos_flags(winapi::SECURITY_IDENTIFICATION).open(r"\\.\pipe\MyPipe")?;
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repo'da `*.rs` 0; `share_mode`/`security_qos_flags` grep'i 0 isabet (2026-10-14) |

### §2.2 — Linux: seccomp syscall filtresi + yerel socket (referans)

```rust
# kaynak: upstream docs - docs.rs/libseccomp (libseccomp 0.4.0 - crate ornegi)
use libseccomp::*;

// varsayilan ALLOW; "getuid" cagrisina Errno(1) (EPERM) kurali ekle ve surece uygula
let syscall = ScmpSyscall::from_name("getuid")?;
ScmpFilterContext::new(ScmpAction::Allow)?
    .add_arch(ScmpArch::X8664)?
    .add_rule(ScmpAction::Errno(1), syscall)?
    .load()?;   // filtre surece yuklenir (upstream: yukleme sonrasi geri alinmaz)
```

not: crate platform satırı tek platform `x86_64-unknown-linux-gnu`, %100 doküante. Yerel socket
yüzeyi `std::os::unix::net::UnixListener::bind` (Unix only) — socket yolu dosyadır, DAC iznine tabidir.

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `seccomp`/`ScmpFilterContext` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.3 — macOS: POSIX DAC izin kalıbı (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html
use std::fs::{File, Permissions};
use std::os::unix::fs::PermissionsExt;

// hedef uc: aarch64-apple-darwin / x86_64-apple-darwin (rustc platform-support: Tier 1)
// POSIX DAC: sahip rwx + grup r-x, digerleri yok (session dizini kalibi, 0o750)
let file = File::create("session.dat")?;
file.set_permissions(Permissions::from_mode(0o750))?;
```

> **⚠️ alt-yüzey (kısmi):** `codesign`/`sandbox-init`/entitlement çağrısı için doğrulanmış
> Rust upstream kaynağı bu turda **bulunamadı** → o alt-yüzey yazılmadı; boot/SIP halkası
> `cekirdek-guvenlik-macos` kartının konusudur (§4).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — Rust repoda yok (§3); `0o750` yalnız **hedef referans**tır, repodaki PHP `0750` ile aynı değer, farklı katman |

### §2.4 — iOS: dar izin + hedef desteği (referans)

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
mevcuttur. **⚠️ alt-yüzey (kısmi):** iOS sandbox / `com.apple.security.*` entitlement için
doğrulanmış Rust upstream kaynağı yok → o alt-yüzey yazılmadı.

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repo'da `*.rs` 0 (2026-10-14) |

### §2.5 — Android: UID denetimi (referans)

```rust
# kaynak: upstream docs - docs.rs/nix/latest/nix/unistd (nix 0.31.3 - fonksiyon imzalari)
use nix::unistd::{getuid, geteuid};

// Android uygulama sandbox'i UID atamasi ile kurulur; gercek/efektif UID okumasi bu modulde
// fonksiyon olarak DOGRULANDI: getuid() -> Uid, geteuid() -> Uid
let real = getuid();
let effective = geteuid();
// karsilastirma ifadesi modul sayfasinda OKUNMADI -> uydurulmaz; hedef kalip: beklenen uygulama
// UID'i ile efektif UID karsilastirmasi (uygulama koduna birakilir)
```

not: hedef uc `aarch64-linux-android` Tier 2 + std ✓ (platform-support); Android NDK bağlantıları
`ndk` crate 0.9.0 — modülleri asset/audio/native_activity vb., güvenlik modülü yoktur (2026-10-14).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `getuid`/`geteuid` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.6 — Tizen: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu): rustc
> "Platform Support" listesinde `tizen` hedef üçlüsü **yok**; Tizen için resmi Rust upstream
> dokümanı doğrulanamadı → uydurma crate/ad/API yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış kaynak yok) | YOK — repo'da Rust zaten yok (§3) |

### §2.7 — webOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> rustc "Platform Support" listesinde `webos` hedef üçlüsü **yok**; webOS için resmi Rust güvenlik dokümanı 200 ile doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış kaynak yok) | YOK — repo'da Rust zaten yok (§3) |

### §2.8 — ReactOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> platform-support'ta ReactOS hedef üçlüsü **yok**; `windows` crate 0.62.2 docs.rs platform satırı **yalnız** `x86_64-pc-windows-msvc` → uyumluluk kanıtı yok.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış kaynak yok) | YOK — repo'da Rust zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya**; aynı ağaçta
`*.rs,*.cs,*.csproj,*.fsproj,*.sln` — **0 dosya**; desen = `UnixListener|seccomp|PermissionsExt|share_mode|security_qos_flags|ScmpFilterContext|geteuid` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| Rust kaynak dosyası (`*.rs`, `Cargo.toml`) | glob | **YOK — 0 dosya** (negatif kanıt) |
| Rust güvenlik API çağrısı (seccomp / nix / PermissionsExt) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| C# / .NET dosyası (kardeş kartla ortak ölçüm) | glob `*.cs,*.csproj,*.sln` | **YOK — 0 dosya** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.rs,*.cs).Count                      # beklenti: 0
($src | Select-String -Pattern 'seccomp|PermissionsExt|UnixListener|geteuid').Count                 # beklenti: 0
```

**Okuma:** 4 satır negatif kanıt + 1 satır ölçüm payandası; Rust OS güvenlik yüzeyi bu repoda **hiç başlamamıştır** — bu, bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Rust ile OS dosya/surec/erisim kaliplari (izn, bayrak, filtre) tasinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalniz OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; dil bagimsiz guvenlik kartlari -> `cekirdek-guvenlik/*`.

Ek sınır: dosya/rwx izni `yetki-ve-erisim-modeli`, sandbox/olay denetimi `uygulama-guvenlik-siniri`,
boot zinciri/SIP/Gatekeeper `cekirdek-guvenlik-macos` vb. kartlardır. Bu kart **yalnız dil kalıbını**
taşır — mekanizma sahipliği komşu kartlardır. `0o750` bir DAC ayarıdır, "boot koruması" diye
adlandırılması abartıdır; kext/sürücü yazımı bu dala yazılmaz (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://doc.rust-lang.org/rustc/platform-support.html | Hedef seviyeleri: `aarch64-apple-darwin` Tier 1 · `aarch64-apple-ios` / `aarch64-linux-android` Tier 2 + std ✓ · `tizen`/`webos`/ReactOS hedefi **yok** | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/unix/fs/trait.PermissionsExt.html | `mode`/`set_mode`/`from_mode`; örnek `0o700`, `0o644`; "Available on Unix only" | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/unix/net/struct.UnixListener.html | `bind`/`accept`; "Available on Unix only" — Unix domain socket sunucusu | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/std/os/windows/fs/trait.OpenOptionsExt.html | `share_mode` varsayılanı; `security_qos_flags` named-pipe impersonation uyarısı | HTTP 200 · içerik okundu |
| https://docs.rs/nix/latest/nix/ | nix 0.31.3; platform listesi `aarch64-apple-ios`, `aarch64-linux-android`, `x86_64-apple-darwin`; %99.93 dokümantasyon | HTTP 200 · içerik okundu |
| https://docs.rs/nix/latest/nix/unistd/ | `getuid`/`geteuid`/`setuid`/`chroot`/`access`/`mkdir(mode)` fonksiyon imzaları | HTTP 200 · içerik okundu |
| https://docs.rs/libseccomp/ | libseccomp 0.4.0 crate örneği (`ScmpFilterContext`, `add_rule`, `load`); platform `x86_64-unknown-linux-gnu` | HTTP 200 · içerik okundu |
| https://docs.rs/ndk/latest/ndk/ | ndk 0.9.0 (Android NDK bağlantıları); platform `aarch64-linux-android` vb.; güvenlik modülü yok | HTTP 200 · içerik okundu |
| https://docs.rs/windows/latest/windows/ | windows 0.62.2 stub; docs.rs platform satırı yalnız `x86_64-pc-windows-msvc` (ReactOS kanıtı yok) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `std/fs/struct.PermissionsExt.html` → **404** (yanlış modül
> yolu) · `std/os/macos/fs/trait.MetadataExt.html` → **404** · `std/os/macos/index.html` → **404** ·
> `developer.android.com/ndk/guides/rust` → **404** · `microsoft.github.io/windows-docs-rs/` → yanıt
> **boş/2xx doğrulanamadı**. Beşi gövdeye alınmadı; `winapi` crate sayfası da doğrulanmadığı için
> §2.1'de yalnız upstream örneğin kendi metni taşındı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (seccomp/ACL/entitlement/boot) hedef mimaride devreye alınıp alınmayacağı; Rust'ın üretim dili olup olmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (filtre/izin mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
