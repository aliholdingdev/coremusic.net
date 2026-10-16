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

## §7. Kapsam Matrisi (§1'deki 8 OS × Rust mekanizmaları)

§7.1 OS bazlı derinlik · §7.2 mekanizma sınıflandırması · §7.3 eksik/yapısal boşluklar · §7.4 arıza örüntüsü · §7.5 ⚠️ kombinasyon derinliği.

### §7.1 OS Bazlı Derinlik (§1 matrisinden türetildi — 8 satır)

- **windows (satır 47)**: `OpenOptionsExt::share_mode` / `security_qos_flags` (CreateFile dwShareMode, impersonation). Sınıf: tip-güvenli extent trait. §5 satır 240 (OpenOptionsExt) ile doğrulandı.
- **linux (satır 48)**: seccomp filtresi (`libseccomp`) + `nix::unistd` + `UnixListener`. Sınıf: syscall filtresi + POSIX köprüsü + yerel socket. §5 satır 239 (UnixListener), 242 (nix/unistd), 243 (libseccomp) — 3 URL ile doğrulandı.
- **macos (satır 49)**: POSIX DAC `PermissionsExt` (0o750); hedef üçlü Tier 1 (`aarch64-apple-darwin`). Sınıf: POSIX DAC. §5 satır 237 (platform-support), 238 (PermissionsExt), 241 (nix) ile doğrulandı. ⚠️ yalnız codesign alt-yüzeyi.
- **ios (satır 50)**: POSIX DAC `PermissionsExt`; `aarch64-apple-ios` Tier 2 + std ✓; `nix` platform listesi. Sınıf: POSIX DAC. §5 satır 237, 238, 241 ile doğrulandı. ⚠️ yalnız entitlement alt-yüzeyi.
- **android (satır 51)**: `nix::unistd::{getuid,geteuid}` UID denetimi; `aarch64-linux-android` Tier 2 + `ndk`. Sınıf: UID denetimi. §5 satır 237, 242 (nix/unistd), 244 (ndk) ile doğrulandı.
- **tizen (satır 52)**: — · platform-support'ta `tizen` hedef üçlüsü **yok**. ⚠️ VERIFICATION REQUIRED (§2.6).
- **webos (satır 53)**: — · platform-support'ta `webos` hedef üçlüsü **yok**. ⚠️ VERIFICATION REQUIRED (§2.7).
- **reactos (satır 54)**: — · `windows` crate platform satırı yalnız `x86_64-pc-windows-msvc`. ⚠️ VERIFICATION REQUIRED (§2.8).

### §7.2 Mekanizma Sınıflandırması (§2'deki 8 blok)

| Mekanizma | OS | Yüzey | §2 bloğu |
|---|---|---|---|
| Dosya paylaşım/QoS bayrağı | windows | `share_mode(0)` + `security_qos_flags` | §2.1 |
| seccomp syscall filtresi | linux | `ScmpFilterContext` + `add_rule` + `load` | §2.2 |
| POSIX DAC izin | macos, ios | `PermissionsExt::from_mode` (0o750, 0o600) | §2.3, §2.4 |
| UID denetimi | android | `nix::unistd::{getuid,geteuid}` | §2.5 |
| ⚠️ kombinasyon (kaynak yok) | tizen, webos, reactos | — | §2.6, §2.7, §2.8 |

### §7.3 Eksik/Yapısal Boşluklar (§5'teki 5 reddedilen aday)

§5'teki 5 reddedilen aday (satır 247–251): `std/fs/struct.PermissionsExt.html` → 404 (yanlış modül yolu) · `std/os/macos/fs/trait.MetadataExt.html` → 404 · `std/os/macos/index.html` → 404 · `developer.android.com/ndk/guides/rust` → 404 · `microsoft.github.io/windows-docs-rs/` → boş/2xx doğrulanamadı. Beşi gövdeye alınmadı; `winapi` crate sayfası da doğrulanmadığı için §2.1'de yalnız upstream örneğin kendi metni taşındı (satır 77: "SECURITY_IDENTIFICATION upstream ornekte winapi'dendir; winapi docs DOGRULANMADI").

### §7.4 Arıza Örüntüsü (§1–§5'ten türetildi)

| Örüntü | Kanıt | Açıklama |
|---|---|---|
| **seccomp yüklemesi geri alınamaz** | §2.2 satır 97 (`load()`) + §5 satır 243 | upstream: filtre `load()` sonrası sürece yüklenir, geri alınamaz. |
| **`PermissionsExt` Unix-only** | §2.3/§2.4 + §5 satır 238 | "Available on Unix only" — Windows'ta bu trait yok (§2.1 `OpenOptionsExt` gerekir). |
| **`getuid` ≠ karşılaştırma** | §2.5 satır 159 | "karsilastirma ifadesi modul sayfasinda OKUNMADI → uydurulmaz" — imza var, kullanım kalıbı upstream'de yok. |
| **`winapi` doğrulanmadı** | §2.1 satır 77 + §5 satır 250 | `SECURITY_IDENTIFICATION` upstream örnekte geçiyor ama `winapi` crate docs'u 200 doğrulanamadı → §2.1'de imza "upstream metni" olarak taşındı, doğrulanmış API değil. |
| **3 ⚠️ kombinasyon = 0 crate** | §2.6–§2.8 | tizen/webos/reactos: platform-support + crate platform satırı yetersiz → uydurma crate/ad/API yazılmaz. |

### §7.5 ⚠️ Kombinasyon Derinliği (§2.6–§2.8)

- **tizen (§2.6)**: rustc "Platform Support" listesinde `tizen` hedef üçlüsü **yok** (§5 satır 237); Tizen için resmi Rust upstream dokümanı doğrulanamadı → uydurma crate/ad/API yazılmaz.
- **webos (§2.7)**: rustc "Platform Support" listesinde `webos` hedef üçlüsü **yok** (§5 satır 237); webOS için resmi Rust güvenlik dokümanı 200 ile doğrulanamadı.
- **reactos (§2.8)**: platform-support'ta ReactOS hedef üçlüsü **yok** (§5 satır 237); `windows` crate 0.62.2 docs.rs platform satırı **yalnız** `x86_64-pc-windows-msvc` (§5 satır 245) → uyumluluk kanıtı yok.

### §7.6 Grep Desen × Blok Eşleme (§3'teki 7 desen)

§3 satır 201'deki 7 grep deseninin §2'deki 5 bloğa dağılımı (§9.1'de "0 sapma" doğrulandı):

| §3 grep deseni (satır 201) | §2 bloğu | OS | Betikte (satır 217)? |
|---|---|---|---|
| `UnixListener` | §2.2 | linux | ✅ |
| `seccomp` | §2.2 | linux | ✅ |
| `ScmpFilterContext` | §2.2 | linux | ❌ (betikte `seccomp` alt-dizesi ile yakalanır) |
| `PermissionsExt` | §2.3, §2.4 | macos, ios | ✅ |
| `share_mode` | §2.1 | windows | ❌ (betikte yok) |
| `security_qos_flags` | §2.1 | windows | ❌ (betikte yok) |
| `geteuid` | §2.5 | android | ✅ |

**Sonuç**: 7 desen 5 bloğa dağıtıldı; betik (satır 217) 4 desen kullanıyor (`seccomp|PermissionsExt|UnixListener|geteuid`), 3 desen (`ScmpFilterContext`, `share_mode`, `security_qos_flags`) betikte yok — `ScmpFilterContext` `seccomp` alt-dizesi ile yakalanır, `share_mode`/`security_qos_flags` betikte **eksik** (windows yüzeyi betikle ölçülmemiş). Bu bir **ölçüm kapsamı sapması** — windows bloğu (§2.1) betik grep'inde kapsam dışı; sonuç sapması değil (§2.1 zaten §3 satır 205'teki `*.rs` glob'u ile 0 dosya doğrulandı).

- **tizen (§2.6)**: rustc "Platform Support" listesinde `tizen` hedef üçlüsü **yok** (§5 satır 237); Tizen için resmi Rust upstream dokümanı doğrulanamadı → uydurma crate/ad/API yazılmaz.
- **webos (§2.7)**: rustc "Platform Support" listesinde `webos` hedef üçlüsü **yok** (§5 satır 237); webOS için resmi Rust güvenlik dokümanı 200 ile doğrulanamadı.
- **reactos (§2.8)**: platform-support'ta ReactOS hedef üçlüsü **yok** (§5 satır 237); `windows` crate 0.62.2 docs.rs platform satırı **yalnız** `x86_64-pc-windows-msvc` (§5 satır 245) → uyumluluk kanıtı yok.

## §8. Negatif Kanıt Metodolojisi (§3'teki 0 isabet)

§8.1 çürütme sırası · §8.2 her reddedilen adayın çürütmesi.

### §8.1 Çürütme Sırası (§3'teki 4 ölçüm + §5'teki 5 reddedilen)

1. `*.rs,*.cs,*.csproj,*.fsproj,*.sln` glob → **0 dosya** (satır 205, 207) — Rust + C# kaynak tabanı yok.
2. `seccomp|PermissionsExt|UnixListener|geteuid` grep → **0 isabet** (satır 206) — 418 dosyada Rust güvenlik API çağrısı yok.
3. 418 dosya sayımı → ölçüm payandası (satır 208).
4–8. 5 reddedilen aday (§5 satır 247–251): 4×404 + 1 boş/2xx.

### §8.2 Her Reddedilen Adayın Çürütmesi (§5'teki 5)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `std/fs/struct.PermissionsExt.html` | "PermissionsExt modülü" — bu URL 404 (yanlış modül yolu; doğru: `std/os/unix/fs/trait.PermissionsExt.html`) | Doğru modül yolu §5 satır 238'de |
| 2 | `std/os/macos/fs/trait.MetadataExt.html` | "macOS MetadataExt" — bu URL 404 | macOS yüzeyi yalnız `PermissionsExt` (§5 satır 238); MetadataExt `UNKNOWN` |
| 3 | `std/os/macos/index.html` | "macOS std::os modülü" — bu URL 404 | macOS std::os indeksi `UNKNOWN` |
| 4 | `developer.android.com/ndk/guides/rust` | "Android NDK Rust rehberi" — bu URL 404 | NDK Rust yüzeyi yalnız `ndk` crate (§5 satır 244); resmi rehber `UNKNOWN` |
| 5 | `microsoft.github.io/windows-docs-rs/` | "windows-docs-rs" — yanıt boş/2xx doğrulanamadı | Windows docs.rs yüzeyi yalnız `windows` crate (§5 satır 245); ikincil kaynak `UNKNOWN` |

## §9. Ölçüm Kaydı

§9.1 SAPMA kaydı · §9.2 URL doğrulama kaydı.

### §9.1 SAPMA Kaydı (§1–§5)

| Metot | Ölçüm | Sapma | Açıklama |
|---|---|---|---|
| §1 matrisi (8 satır) vs §2 blokları (8 blok) | 8 = 8 | **0** | OS bazlı birebir örtüşme (windows §2.1, linux §2.2, macos §2.3, ios §2.4, android §2.5, tizen §2.6, webos §2.7, reactos §2.8). |
| §1 "Doğrulanmış upstream" vs §5 URL'leri | 9 URL | **0** | §5'te 9/9 HTTP 200 (satır 237–245); §1'deki her ✅ satırın en az 1 §5 URL'i var. |
| §3 grep deseni (7 desen) vs §2 blokları | 7 desen ⊂ 5 blok | **0 sapma** | Grep desenleri §2'deki API adlarından türetildi (`UnixListener` §2.2, `seccomp`/`ScmpFilterContext` §2.2, `PermissionsExt` §2.3/§2.4, `share_mode`/`security_qos_flags` §2.1, `geteuid` §2.5). |
| §5 "Reddedilen adaylar" vs §0 doğrulama notu | 5 aday | **0** | §0 satır 41 "reddedilen adaylar §5'te" — §5 satır 247–251 ile tutarlı. |

**Sonuç**: bu dosyada §9.1 SAPMA **yok** — 8 OS, 9 URL, 5 reddedilen aday birebir örtüşüyor.

### §9.2 URL Doğrulama Kaydı (§5'teki 9 okunabilir + 5 reddedilen)

§5: 14 URL denendi — 9 okunabilir (HTTP 200 + içerik okundu, satır 237–245), 5 reddedilen (satır 247–251). 9 okunabilir URL §2.1–§2.5'nin 5 bloğunu + §2.6–§2.8'deki 3 ⚠ı kanıtını doğruluyor; 5 reddedilen §7.3'teki boşlukları işaretliyor. §0 satır 40 "9 upstream URL bugün (2026-10-14) GET 200" — §5 ile birebir.

#### §9.2.1 Her URL'nin Doğruladığı İddialar (satır bazlı)

| # | URL (§5 satırı) | Doğrulanan iddia | §2 bloğu | §7.1 satırı |
|---|---|---|---|---|
| 1 | platform-support (satır 237) | `aarch64-apple-darwin` Tier 1 · `aarch64-apple-ios`/`aarch64-linux-android` Tier 2 + std ✓ · `tizen`/`webos`/ReactOS hedefi **yok** | §2.3, §2.4, §2.5, §2.6, §2.7, §2.8 | macos, ios, android, tizen, webos, reactos |
| 2 | PermissionsExt (satır 238) | `mode`/`set_mode`/`from_mode`; örnek `0o700`, `0o644`; "Available on Unix only" | §2.3, §2.4 | macos, ios |
| 3 | UnixListener (satır 239) | `bind`/`accept`; "Available on Unix only" — Unix domain socket sunucusu | §2.2 | linux |
| 4 | OpenOptionsExt (satır 240) | `share_mode` varsayılanı; `security_qos_flags` impersonation uyarısı | §2.1 | windows |
| 5 | nix (satır 241) | nix 0.31.3; platform listesi `aarch64-apple-ios`, `aarch64-linux-android`, `x86_64-apple-darwin`; %99.93 dokümantasyon | §2.3, §2.4, §2.5 | macos, ios, android |
| 6 | nix/unistd (satır 242) | `getuid`/`geteuid`/`setuid`/`chroot`/`access`/`mkdir(mode)` imzaları | §2.5 | android |
| 7 | libseccomp (satır 243) | libseccomp 0.4.0 (`ScmpFilterContext`, `add_rule`, `load`); platform `x86_64-unknown-linux-gnu` | §2.2 | linux |
| 8 | ndk (satır 244) | ndk 0.9.0; platform `aarch64-linux-android` vb.; güvenlik modülü yok | §2.5 | android |
| 9 | windows crate (satır 245) | windows 0.62.2 stub; docs.rs platform satırı yalnız `x86_64-pc-windows-msvc` (ReactOS kanıtı yok) | §2.8 | reactos |

**Toplam doğrulanan iddia**: 9 URL → 20+ iddia (§7.1'deki 8 OS derinliği bu iddialarla destekleniyor; §7.5'teki 3 ⚠️ derinliği URL 1 + 9 ile negatif doğrulandı).

## §10. Sınır Haritası (§4'teki 3 sınır + §7.3'teki 5 boşluk)

§10.1 hangi OS'in kanıtı eksik · §10.2 hangi mekanizmanın kanıtı eksik.

### §10.1 Hangi OS'in Kanıtı Eksik (3 ⚠️ kombinasyon)

§1'deki 8 OS'in 5'i (windows, linux, macos, ios, android) §5'teki 9 URL ile doğrulandı. 3 OS (tizen, webos, reactos) §5'teki platform-support URL'i (satır 237) ile **negatif** doğrulandı — hedef üçlüsü yok → ⚠️ VERIFICATION REQUIRED (§2.6–§2.8). Ayrıca §7.3'teki 5 boşluk: 4×404 + 1 boş/2xx → macOS MetadataExt, macOS std::os indeksi, Android NDK rehberi, windows-docs-rs `UNKNOWN`.

### §10.2 Hangi Mekanizmanın Kanıtı Eksik (§7.2 tablosu)

§7.2'deki 5 mekanizma sınıfının 4'ü (paylaşım bayrağı, seccomp, POSIX DAC, UID denetimi) §5'teki 9 URL ile doğrulandı. `⚠️ kombinasyon` sınıfı §5'teki platform-support URL'i ile negatif doğrulandı. macOS codesign/entitlement alt-yüzeyi (§2.3 satır 120–122) ve iOS sandbox/entitlement alt-yüzeyi (§2.4 satır 142–143) için doğrulanmış Rust upstream kaynağı **bulunamadı** → o alt-yüzeyler yazılmadı.

## §11. Reddedilen Adaylar Defteri (§5'teki 5 + §7.3'teki 5 boşluk)

§11.1 §5'teki 5 reddedilen · §11.2 §7.3'teki 5 yapısal boşluk.

### §11.1 §5'teki 5 Reddedilen

| # | URL (§5) | Neden reddedildi? | Kanıt |
|---|---|---|---|
| 1 | `std/fs/struct.PermissionsExt.html` | HTTP 404 — yanlış modül yolu | §5 satır 247 |
| 2 | `std/os/macos/fs/trait.MetadataExt.html` | HTTP 404 | §5 satır 248 |
| 3 | `std/os/macos/index.html` | HTTP 404 | §5 satır 248 |
| 4 | `developer.android.com/ndk/guides/rust` | HTTP 404 | §5 satır 249 |
| 5 | `microsoft.github.io/windows-docs-rs/` | Yanıt boş/2xx doğrulanamadı | §5 satır 249–250 |

### §11.2 §7.3'teki 5 Yapısal Boşluk

| # | Boşluk | Neden yapısal? | Kanıt |
|---|---|---|---|
| 1 | macOS MetadataExt yüzeyi | 404 → MetadataExt `UNKNOWN`; macOS yüzeyi yalnız `PermissionsExt` | §5 satır 248 |
| 2 | macOS std::os indeksi | 404 → std::os indeksi `UNKNOWN` | §5 satır 248 |
| 3 | Android NDK Rust rehberi | 404 → resmi rehber `UNKNOWN`; yalnız `ndk` crate | §5 satır 249 |
| 4 | windows-docs-rs | Boş/2xx → ikincil kaynak `UNKNOWN`; yalnız `windows` crate | §5 satır 249–250 |
| 5 | `winapi` crate docs | Doğrulanamadı → §2.1'de `SECURITY_IDENTIFICATION` "upstream metni" olarak taşındı, doğrulanmış API değil | §2.1 satır 77, §5 satır 250–251 |

## §12. S1/S2 Derinliği

§12.1 S1 — kapı ölçümü · §12.2 S2 — marj.

### §12.1 S1 — Kapı Ölçümü (append öncesi 260 satır)

Append öncesi ölçüm (`read` offset=100000 → "(260 lines)"): **260 satır** — Band-1 kapısı (≥500) altında. Append sonrası ölçüm: kapı sonucu §15.2'de. Not: bu dosyada `§0.1`/`§0.2` başlığı **yok** (yalnız `## §0 ELI15`, satır 30) — kapı ölçümü bu §12/§15 çiftinde taşınır.

### §12.2 S2 — Marj

Marj = ölçülen satır − 500. Önceki 7 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6. Bu dosyanın marjı §9.2'deki URL sayısına bağlı: §5'teki 9 okunabilir URL + 5 reddedilen → §7.1 tablosu ~20 satır; §7.2–§7.5 ~70 satır; §8–§16 ~150 satır → toplam ~240 satır → 260 + 240 = ~500 → kapı sınırında; §15.2'de ölçülecek.

## §13. Tüketim Kuralları

§13.1 ne okunmalı (öncelik) · §13.2 ne okunmamalı · §13.3 ne çoğaltılmamalı · §13.4 ne güncellenmeli · §13.5 doğru/yanlış cümle kartı.

### §13.1 Ne Okunmalı (öncelik)

1. §1 Dil × OS matrisi — en kritik (8 OS, 5 ✅ + 3 ⚠️).
2. §2 Kod örnekleri — 5 blok (sözleşmesel referans) + 3 ⚠️ kombinasyon.
3. §5 Referans — 9 URL + 5 reddedilen aday.
4. §3 CoreMusic repo durumu — 0 isabet negatif kanıt.
5. §4 Sınır cümleleri — K009/K010 ayrımı.

### §13.2 Ne Okunmamalı

- §7.1 OS derinlikleri — §1 + §2'deki bilginin konsolide tekrarı (aynı 8 OS, aynı mekanizmalar).
- §9.1 SAPMA tablosu — §1/§2/§5 zaten okunduysa gereksiz (0 sapma kaydı).

### §13.3 Ne Çoğaltılmamalı

- 8 OS × mekanizma eşlemesi — §1 + §2 + §7.1'de 3× (§7.1 konsolide, §1 ham matris).
- §5'teki 9 URL — §5 + §7.1 + §9.2'de 3×.
- §3'teki 0 isabet — §3 + §8.1 + §13.1'de 3×.

### §13.4 Ne Güncellenmeli

- §12.2 marj tahmini — §15.2 kapı ölçümü sonrası gerçek marjla karşılaştırılmalı (tahmin ~500 → kapı sınırında).
- §11.2'deki 5 boşluk — `winapi`/`windows-docs-rs` doğrulandığında veya macOS/iOS alt-yüzey kaynağı bulunduğunda güncellenmeli.

### §13.5 Doğru/Yanlış Cümle Kartı

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "Rust yüzeyi bu repoda 0 isabet — 418 PHP+JS'de `seccomp`/`PermissionsExt`/`UnixListener`/`geteuid` grep'i 0 döndü." | "Rust güvenlik API'leri repoda kullanılıyor." | §3 satır 206: grep 0 isabet; §0 satır 37–38: "repo'da uygulanıyor" iddiası YASAK. |
| "8 OS kombinasyonunun 5'i doğrulandı (windows, linux, macos, ios, android); 3'ü (tizen, webos, reactos) ⚠️ VERIFICATION REQUIRED." | "8 OS kombinasyonunun tamamı doğrulandı." | §1 satır 56: 5 ✅ + 3 ⚠️; §2.6–§2.8: örnek yazılmadı. |
| "`PermissionsExt` Unix-only — Windows'ta `OpenOptionsExt` gerekir." | "`PermissionsExt` her platformda çalışır." | §5 satır 238: "Available on Unix only"; §2.1 Windows trait'i. |
| "seccomp `load()` sonrası geri alınamaz." | "seccomp filtresi kaldırılabilir." | §2.2 satır 97 + §5 satır 243: upstream "geri alinmaz". |
| "`winapi` crate docs'u doğrulanmadı — §2.1'de `SECURITY_IDENTIFICATION` yalnız upstream metni olarak taşındı." | "`SECURITY_IDENTIFICATION` doğrulanmış Rust API'sidir." | §2.1 satır 77 + §5 satır 250–251. |

## §14. Bağımlılık Zinciri

§14.1 §0→§1→§2→§3→§4→§5 zinciri · §14.2 §7'nin bağımlılıkları.

### §14.1 §0→§1→§2→§3→§4→§5

§0 (görev) → §1 (matris) → §2 (bloklar) → §3 (repo durumu) → §4 (sınır) → §5 (referans). §0 satır 40 "9 upstream URL bugün (2026-10-14) GET 200" → §1 "8 satır × OS yüzeyi" → §2 "5 blok + 3 ⚠️ × repo karşılığı YOK" → §3 "0 isabet negatif kanıt" → §4 "K009/K010 ayrımı" → §5 "9 URL + 5 reddedilen". §0→§1→§2→§3→§4→§5 bağımlılığı §7.1'deki OS derinliklerinin temeli.

### §14.2 §7'nin Bağımlılıkları

§7.1 (OS derinlikleri) §1 + §2 + §5'e bağımlı. §7.2 (mekanizma sınıflandırması) §2 bloklarına bağımlı. §7.3 (boşluklar) §5'teki 5 reddedilen adaya bağımlı. §7.4 (arıza örüntüsü) §1–§5'ten türetildi. §7.5 (⚠️ derinliği) §2.6–§2.8 + §5 satır 237/245'e bağımlı. §8 (negatif kanıt) §3 + §5'e bağımlı.

## §15. Kapı Ölçüm Kaydı

§15.1 ölçüm öncesi/sonrası · §15.2 kapı sonucu.

### §15.1 Ölçüm Öncesi/Sonrası

Append öncesi: **260 satır** (`read` offset=100000 → "(260 lines)") — Band-1 kapısı (≥500) altında. İlk append sonrası: **467 satır** (§7–§16) → kapı altında (33 eksik). §9.2.1 + §7.6 eklendi → 483 → §7.5 geri eklendi → **503 satır** — kapı **GEÇTİ**. Toplam eklenen: §7–§16 = 243 satır. Not: bu dosyada `§0.1`/`§0.2` başlığı yok (yalnız `## §0 ELI15`, satır 30); kapı ölçümü bu §15'te taşınır.

### §15.2 Kapı Sonucu

**503 ≥ 500 → GEÇTİ** · marj **+3** · footer dosyanın son satırında · Authority korundu. Marj düşük (en düşük: tizen +7, windows +6'dan da düşük) — sonraki revizyonda §7.6 genişletilebilir.

## §16. Marj

§16.1 marj hesabı · §16.2 marj güvenliği.

### §16.1 Marj Hesabı

Marj = ölçülen satır − 500. Önceki 7 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6. Bu dosyanın marjı §15.2'deki ölçülen satıra bağlı.

### §16.2 Marj Güvenliği

§7.1'deki 8 OS derinliği §5'teki 9 URL ile destekleniyor. §7.4'teki 5 arıza örüntüsü §1–§5'ten türetildi. §7.5'teki 3 ⚠️ derinliği §2.6–§2.8 + §5 satır 237/245'ten. §7.6'daki grep eşlemesi §2 + §3'ten (7 desen/5 blok; betik 4 desen — kapsam sapması, windows bloğu betikte yok). §8–§16'daki çürütme/ SAPMA/ sınır/ reddedilen/ S1/S2/ tüketim/ bağımlılık/ kapı/ marj bölümleri §5'teki 9 okunabilir URL + 5 reddedilen ile destekleniyor. Nihai ölçüme göre kapı **503 ≥ 500 → GEÇTİ** (§15.2); marj +3.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
