---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-android
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-android
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://docs.python.org/3/using/android.html
  - https://docs.python.org/3/library/os.html
  - https://go.dev/doc/install/source
  - https://nodejs.org/api/os.html
  - https://doc.rust-lang.org/rustc/platform-support.html
  - https://docs.rs/ndk/latest/ndk/
  - https://learn.microsoft.com/en-us/dotnet/what-is-maui
validation:
  - "2026-10-14: 7 kaynak URL GET 200 ile dogrulandi; C/PHP satirlari Android upstream'i dogrulanamadigi icin ornek yazmadan VERIFICATION REQUIRED birakildi (bionic adaylari 404); repo olcusu 418 PHP+JS / 0 *.c,*.go,*.cs / grep 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Android (7 Dil)

## §0 ELI15

Bu kart, **Android'de 7 dil bölümünde** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını taşır: gömülü (embedded) çalışma modu, UID tabanlı uygulama sandbox'ı,
hedef mimari seçimi. Hepsi `# kaynak: upstream docs` imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**, Android güvenlik API isabeti **0** (negatif kanıt, §3).
> Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor" iddiası **YASAKTIR**.

> **⚠️ alt-yüzey (kısmi):** APK runtime izin modeli (AndroidManifest izinleri), SELinux politikası
> ve Bionic kütüphanesi bu turda **doğrulanamadı** (bionic sayfaları 404, §5) → o alt-yüzeyler
> yazılmadı; yalnız dil dokümanlarının taşıdığı UID/süreç gerçekleri yazıldı.

## §1 OS güvenlik profili × dil matrisi

| Dil | Android güvenlik yüzeyi | Bu dosyadaki mekanizma | Upstream sınıfı |
|---|---|---|---|
| C | — | Bionic upstream'i **404** (reddedildi, §5) | — → ⚠️ |
| Python | gömülü mod sınırları | embedded mode; `os.setuid` Android'de **YOK** (negatif) | docs.python.org |
| Go | `GOOS=android` + 4 mimari | `//go:build android` (386/amd64/arm/arm64) | go.dev/doc/install/source |
| JS (Node) | deneysel platform | `os.platform() === 'android'` (deneysel notu) | nodejs.org/api |
| PHP | — | Android'e özgü doğrulanmış upstream **yok** | — → ⚠️ |
| Rust | hedef desteği + NDK bağlantıları | `aarch64-linux-android` Tier 2 + std ✓; `ndk` crate | doc.rust-lang.org + docs.rs/ndk |
| C# | MAUI hedef listesi | Android/iOS/macOS/Windows hedefleri | learn.microsoft.com |

**Okuma:** 5 satır ✅ (dördü kısmi) + 2 satır ⚠️ (C, PHP); hiçbir satır "repoda uygulanıyor"
iddiası **taşımez** — hepsi hedef referanstır (§3).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 5 bölümün **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (üretime alınabilir komut **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — C: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): Bionic
> (Android'in libc'si) dökümantasyonu için denenen iki aday da **404** döndü
> (`developer.android.com/ndk/guides/bionic`, `source.android.com/docs/security/bionic`,
> §5) → C yüzeyi doğrulanamadı, uydurma çağrı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.2 — Python: gömülü mod + Android'e özgü eksik API (referans)

```python
# kaynak: upstream docs - docs.python.org/3/using/android.html
# kaynak: upstream docs - docs.python.org/3/library/os.html
import os

# using/android.html: "there is no concept of installing as a system resource.
# The only unit of software distribution is an 'app'." -> tek kullanim GOMULU MOD
# (libpython + Python embedding API); standart kutuphanede "notable omissions"
#
# os.setuid -> "Availability: Unix, not WASI, not Android." -> Android'de SUNULMAZ
# (uygulama sandbox'i UID atamasi ile kurulur; bu islev dil seviyesinde kapali)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.setuid` çağrısı 0 isabet (418 dosya, 2026-10-14) |

### §2.3 — Go: `android` hedef etiketi — 4 mimari (referans)

```go
// kaynak: upstream docs - go.dev/doc/install/source
//   GOOS kumesinde 'android' vardir; android GOARCH destegi:
//   386 · amd64 · arm · arm64 (upstream mimari listesi)
//go:build android

package main // yalniz Android hedefinde derlenir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.go` 0 dosya (§3) |

### §2.4 — JS (Node.js): deneysel platform ayrımı (referans)

```js
// kaynak: upstream docs - nodejs.org/api/os.html
const os = require('node:os');

// os.platform() 'android' dondurur — upstream bu degeri EXPERIMENTAL olarak
// isaretler (diger degerler: aix|darwin|freebsd|linux|openbsd|sunos|win32)
if (os.platform() === 'android') {
  // Android'e ozel yol deneysel platforma baglidir; guvenlik karari S1'dir
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `'android'` karşılaştırması 98 JS dosyasında 0 isabet |

### §2.5 — PHP: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): php.net'te
> Android'e özgü upstream sayfa bu turda **doğrulanamadı** → `posix_*`/`chmod` davranışının
> Android'de ne olduğu bilinmez; mevcutmuş gibi kod yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da PHP Android kullanımı zaten yok (§3) |

### §2.6 — Rust: hedef desteği + UID denetimi yüzeyi (referans)

```rust
# kaynak: upstream docs - doc.rust-lang.org/rustc/platform-support.html
# kaynak: upstream docs - docs.rs/nix/latest/nix/unistd (nix 0.31.3 - fonksiyon imzalari)
use nix::unistd::{getuid, geteuid};

// hedef uc: aarch64-linux-android -> rustc platform-support: Tier 2, std ✓
// Android uygulama sandbox'i UID atamasi ile kurulur; gercek/efektif UID okumasi
// nix::unistd'te DOGRULANAN imzalar: getuid() -> Uid, geteuid() -> Uid
let real = getuid();
let effective = geteuid();
```

not: Android NDK bağlantıları `ndk` crate 0.9.0 — modülleri asset/audio/native_activity vb.,
güvenlik modülü **yoktur** (2026-10-14, docs.rs/ndk).

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `getuid`/`geteuid` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.7 — C#: MAUI hedef listesi (referans)

```csharp
// kaynak: upstream docs - learn.microsoft.com/en-us/dotnet/what-is-maui
// .NET MAUI — tek kod tabani ile hedefler (upstream hedef listesi BIREBIR):
//   Android · iOS · macOS · Windows
// Android hedefi buradan DOGRULANIR; APK runtime izin modeli bu sayfada
// ANLATILMAZ -> o alt-yuzey bu kartta yazilmaz (S1 acik)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — C# kaynak yok (`.cs` 0 dosya); yalnız hedef referans |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`android|getuid|geteuid|setuid|embedded|native_activity` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| Android güvenlik API çağrısı (UID/NDK/izin) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| UA algılama (tek benzer desen — güvenlik değil) | grep `android` | yalnız UA/metin eşleşmeleri — güvenlik API'si **değil** (bkz §5.3 tur notu) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'getuid|geteuid|setuid|native_activity').Count       # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. Android OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız Android dosya/UID/hedef kalıpları ele alınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; Android platform geneli → `cekirdek-guvenlik-android`.

Ek sınır: APK izin modeli, keystore ve Play politikası bu dala yazılmaz; `getuid` okuması
"sandbox doğrulaması" değil, yalnız UID denetim kalıbıdır; UID karşılaştırma ifadesi
uygulama koduna aittir (kaynak modülde okunmadı, §2.6). Bionic/kütüphane detayı X7/X8.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.python.org/3/using/android.html | gömülü mod tek kullanım (libpython + embedding API); "no system resource"; std kütüphanede kısıtlar | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/os.html | `os.setuid` "Unix, not WASI, not Android" (negatif kanıt) | HTTP 200 · içerik okundu |
| https://go.dev/doc/install/source | GOOS kümesinde `android`; GOARCH: 386/amd64/arm/arm64 | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` `android` değeri — **deneysel** işaretli | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/rustc/platform-support.html | `aarch64-linux-android` Tier 2 + std ✓; tizen/webos/reactos hedefi yok | HTTP 200 · içerik okundu |
| https://docs.rs/ndk/latest/ndk/ | ndk 0.9.0 (Android NDK bağlantıları); güvenlik modülü yok | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/what-is-maui | MAUI hedefleri: Android, iOS, macOS, Windows | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `developer.android.com/ndk/guides/bionic` → **404** ·
> `source.android.com/docs/security/bionic` → **404** (2026-10-14). İkisi de gövdeye
> alınmadı; C satırı bu nedenle `⚠️` bırakıldı (§2.1).

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Android güvenlik katmanı (UID sandbox/izin) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (UID mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode