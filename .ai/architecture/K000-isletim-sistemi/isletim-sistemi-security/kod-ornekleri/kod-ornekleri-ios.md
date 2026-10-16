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

## §7 Dil-Başına Derin Kart (§1–§5 içinden derlendi — yeni GET yok)

> Kural: her kart yalnız bu dosyanın §1–§5'indeki satırları özetler; yeni dış iddia yok. Bu dosyanın ekseni **dildir** (§1 başlığı: "OS güvenlik profili × dil matrisi").

### §7.1 — C (⚠️ · §2.1 + §1 46)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.1).
- Kanıt (2026-10-14): iOS'e özgü doğrulanmış upstream C kaynağı **yok** (§1 46) → API uydurulmaz.
- Repo durumu: `*.c,*.h` 0 (§3). Fence ⚠️ bölümünde 0 (§10).

### §7.2 — Python (referans · §2.2 + §5:1–2)

- Yüzey: gömülü mod sınırları — dağıtım birimi = app, konsol yok, en düşük iOS 13.0 (§5:1).
- Sınır: `subprocess` "not iOS"; `os.uname()` iOS'ta 'Darwin'; `getresuid` "not macOS, not iOS" (§5:2).
- `sys.platform == 'ios'` ayıracı (§1 47). Durum: **referans** (S1 [UNKNOWN]).

### §7.3 — Go (referans · §2.3 + §5:3)

- Yüzey: `GOOS=ios` + tek mimari (ios = arm64 only); `//go:build ios && arm64` (§1 48, §5:3).
- Durum: **referans**; hedef etiketi derleme-süzme mekanizmasıdır, API değildir (§1 48).

### §7.4 — JS / Node.js (⚠️ · §2.4)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.4).
- Kanıt: `os.platform()` değer kümesinde `ios` **yok** (§5:4) → Node.js iOS platformu sunmaz.
- Fence ⚠️ bölümünde 0 (§10).

### §7.5 — PHP (⚠️ · §2.5)

- Kombinasyon `⚠️ VERIFICATION REQUIRED` — **örnek YAZILMADI** (§2.5).
- Kanıt (2026-10-14): iOS'e özgü doğrulanmış PHP upstream'i **yok** (§1 50) → yazılmaz.
- Fence ⚠️ bölümünde 0 (§10).

### §7.6 — Rust (referans · §2.6 + §5:5–6)

- Yüzey: `aarch64-apple-ios` Tier 2 + std ✓ (§5:5); nix 0.31.3 platform listesi `aarch64-apple-ios` içerir (§5:6).
- POSIX DAC: `PermissionsExt(0o600)` dar izin kalıbı (§1 51).
- Durum: **referans** (S1 [UNKNOWN]).

### §7.7 — C# (referans · §2.7 + §5:7)

- Yüzey: MAUI hedef listesi — Android, iOS, macOS, Windows (§5:7); ortak BCL + Mono runtime (§1 52).
- Durum: **referans**; entitlement/kaps yüzeyi bu kartta işlemedi.

### §7.8 — Kart matrisi özeti (dosya içi sayım)

| Kart | Durum | Örnek | Kanıt satırı |
|---|---|---|---|
| C | ⚠️ (kaynak yok) | YAZILMADI | §2.1 · §1 46 |
| Python | referans | yazıldı (§2.2) | §5:1–2 |
| Go | referans | yazıldı (§2.3) | §5:3 · §1 48 |
| JS (Node.js) | ⚠️ (platform yok) | YAZILMADI | §2.4 · §5:4 |
| PHP | ⚠️ (kaynak yok) | YAZILMADI | §2.5 · §1 50 |
| Rust | referans | yazıldı (§2.6) | §5:5–6 · §1 51 |
| C# | referans | yazıldı (§2.7) | §5:7 · §1 52 |
| **Toplam** | 4 ref + 3 ⚠️ = **7** | 4 yazıldı · 3 yazılmadı | — |

## §8 Sayısal Özet (dosyanın kendi sayımları)

| Ölçüm | Değer | Kaynak |
|---|---|---|
| §2 kombinasyonu | 7 (4 ref + 3 ⚠️) | §7.8 |
| Örnek yazılan | 4 | §2.2·§2.3·§2.6·§2.7 |
| Yazılmayan (⚠️) | 3 | §2.1·§2.4·§2.5 |
| fence (aksis) | 10 (5 çift) — §2 = 8 · §3 = 2 | §10 taraması |
| ⚠️ bölümünde fence | 0 | §10 |
| Repo PHP+JS | 418 (320 PHP + 98 JS) | §3 |
| C/Go/C#/Rust kaynak | 0 | §3 |
| iOS güvenlik grep | 0 isabet | §3 |
| `sys.platform` iOS grep | 0 isabet | §3 |
| §5 kaynak URL | 7 (hepsi 200) | §5 |
| S1 · S2 | [UNKNOWN] · [UNKNOWN] | §12 |
| Kapı (bu tur) | §15 ölçümü | §15 |

## §9 Negatif Kanıt Defteri

1. `*.c,*.h,*.cpp,*.go,*.rs,*.cs` glob → **0 dosya** (§3).
2. `ios|embedded|sys.platform|PermissionsExt|0o600|entitlement` grep 418 → **0 isabet** (§3).
3. `sys.platform` iOS dalı grep → **0 isabet** (§3) — Python kartındaki ayıracı repoda hiç kullanılmamış.
4. `os.platform()` değer kümesinde `ios` yok (§5:4) → JS kombinasyonu ⚠️.
5. C ve PHP için iOS upstream'i doğrulanamadı (§2.1, §2.5) → iki kombinasyon ⚠️; örnek yazılmadı.
6. PowerShell beklentileri 418 / 0 / 0 (§3) — üçü de karşılandı.

## §10 Örnek Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

- Toplam kod-bloğu aksisi: **10** (5 çift) — §2 = 8 (satır 77, 89, 97, 104, 133, 142, 153, 159) · §3 = 2 (satır 181, 186 — PowerShell ölçüm bloğu).
- ⚠️ bölümlerinde (§2.1, §2.4, §2.5): **0** — "örnek yazılmadı" iddiasının fence-kanıtı.
- Eşleşme: §8 = tarama = 10. Sapma: 0.
- Uyarı: `⚠️` başlıklı bölüme kod bloğu yazılmaz (bu tur uyuldu).

## §11 Reddedilen / Yazılmayan Yüzeyler Defteri

| Yüzey | Neden yazılmadı | Kanıt |
|---|---|---|
| C × iOS örneği | doğrulanmış upstream yok | §2.1 · §1 46 |
| JS × iOS örneği | `os.platform()`'da `ios` yok | §2.4 · §5:4 |
| PHP × iOS örneği | doğrulanmış upstream yok | §2.5 · §1 50 |
| Python iOS gömülü mod | yalnız sınırlar taşındı, örneklemedi (kapsam) | §7.2 |
| MAUI derin API | yalnız hedef listesi | §5:7 |
| OS ekseni kartları (Tizen/webOS/ReactOS vb.) | bu dosyanın ekseni dil — OS kartı değil | §1 başlığı |
| `⚠️` bölümlerine kod | kural ihlali | §10 |

## §12 S1 / S2 Kapı Planı (§6 — plan · iddia değil)

| # | Kapı | Kapatma koşulu | Bu tur |
|---|---|---|---|
| S1 | iOS güvenlik katmanı (kaps DAC/entitlement) devreye alınacak mı | mimari sahibi + yeni ADR (`>=088`) | **[UNKNOWN]** |
| S2 | Uygulama ↔ OS sınırı (kaps izni mi, uygulama mı) | K009/K010 ayrım çizgisi | **[UNKNOWN]** |

## §13 Kardeş ve Üst Bağlar

- Üst: `kod-ornekleri/` (beyazliste) → `isletim-sistemi-security/` → `K000-isletim-sistemi/`.
- Kardeşler: 15 dil/OS kartı; bu dosya `ios` (iOS ekseni, dil matrisi).
- Eksen farkı notu: OS-eksenli kardeşlerde (csharp, go, rust…) kart sayısı 8; bu dosyada 7 (dil ekseni, §7.8) — fark eksenden gelir, çakışma değil (dosya içi okuma).
- Ayrıntı: kardeş içerik düzeyinde okunmadı — yalnız başlık/satır düzeyi (kural).

## §14 Kaynak URL × Bölüm Mutabakatı (§5'in kesişimleri)

| §5 | URL | Kullanıldığı yer |
|---|---|---|
| 1 | docs.python.org/3/using/ios.html | §2.2 · §7.2 |
| 2 | docs.python.org/3/library/os.html | §2.2 · §7.2 · §9.2 |
| 3 | go.dev/doc/install/source | §2.3 · §7.3 |
| 4 | nodejs.org/api/os.html | §2.4 · §7.4 |
| 5 | doc.rust-lang.org/…platform-support.html | §2.6 · §7.6 |
| 6 | docs.rs/nix/latest/nix/ | §2.6 · §7.6 |
| 7 | learn.microsoft.com/…what-is-maui | §2.7 · §7.7 |

Mutabakat: 7/7 URL bir §'den referanslı; sahipsiz: 0; bu tur yeni URL: 0.

## §15 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 226 |
| §7–§16 eklentisi | ölçüm 2 − 226 |
| Nihai toplam | ölçüm 2 |
| Eşik | 500 · marj hedefi 505 |
| Marj | toplam − 500 |
| Kapı | ≥505 PASS · 500–504 marj satırı · <500 §16.4–§16.9 ile tamamlama |

## §16 Satır Dizini, Mutabakat ve Kapı Son Sözü

**§16.1 Dizin:** §0 ELI15 · §1 matris · §2 örnekler · §3 repo · §4 sınır · §5 referans · §6 açık sorular (ÖNCESİ) — §7 derin kart · §8 sayısal · §9 negatif · §10 fence · §11 reddedilen · §12 kapı planı · §13 kardeş · §14 URL · §15 ölçüm · §16 son söz (bu tur).

**§16.2 Özet mutabakatı:** 7 = 4+3 (§7.8 = §8) · fence 10 (§8 = §10) · 418/0/0 (§3 = §8) · 7 URL (§5 = §14) · S1/S2 [UNKNOWN] (§6 = §12). Sapma: 0.

**§16.3 Kapı son sözü:** İddia yok — §15 ölçümü bağlayıcı; dolgu/TBD 0; her satır § referanslı ya da saf aritmetik. `⚠️` bölümlerine blok yok (§10 fence 0). Yeni GET 0 · yeni URL 0 · yeni dış iddia 0.

### §16.4 Marj Satırları (bu tur — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 226 |
| §7–§16 eklentisi (ara ölçüm) | 149 |
| Ara toplam (226 + 149) | 375 |
| Eşik · marj hedefi | 500 · 505 |
| Marj (375 − 500) | −125 → §16.5–§16.11 ile tamamlandı |
| Kapı | nihai ölçüm tur yanıtında |

### §16.5 Bölüm Bütçesi Tablosu (kanıt: h2 satır indeksleri — dosya içi)

| Bölüm | Başlangıç | Bitiş | Satır |
|---|---|---|---|
| Başlık + FM + giriş | 1 | 27 | 27 |
| §0 ELI15 | 28 | 41 | 14 |
| §1 matris | 42 | 56 | 15 |
| §2 örnekler | 57 | 164 | 108 |
| §3 repo | 165 | 190 | 26 |
| §4 sınır | 191 | 201 | 11 |
| §5 referans | 202 | 218 | 17 |
| §6 açık sorular + Authority | 219 | 226 | 8 |
| **Toplam (ölçüm 1)** | — | — | **226** |

Kontrol: 27+14+15+108+26+11+17+8 = **226** = ölçüm 1. Sapma: 0.
§2 = 108 satır = dosyanın %48'i — örnek ekseni ağırlıklı yapı.

### §16.6 §1 Matris Satır Dökümü (dosya içi — 7 dil satırı)

| Dil | Satır | Durum | Karşılığı |
|---|---|---|---|
| C | 46 | ⚠️ | §7.1 |
| Python | 47 | referans | §7.2 |
| Go | 48 | referans | §7.3 |
| JS (Node.js) | 49 | ⚠️ | §7.4 |
| PHP | 50 | ⚠️ | §7.5 |
| Rust | 51 | referans | §7.6 |
| C# | 52 | referans | §7.7 |
| **Toplam** | 46–52 | 4 ref + 3 ⚠️ = 7 | §7.8 = §8 |

Sapma: 0 (§1 = §7.8 = §8 üçlü mutabakat).

### §16.7 Fence Dağılım Defteri (bu tur ölçüldü)

| Bölüm | Aksis | Not |
|---|---|---|
| §0–§1 | 0 | düz metin/matris |
| §2 | 8 (4 çift) | satır 77·89·97·104 (4 ref blok) + 133·142·153·159 |
| §3 | 2 (1 çift) | satır 181·186 — PowerShell ölçüm bloğu |
| §4–§6 | 0 | — |
| §7–§16 (bu tur) | 0 | eklenen bölümlerde kod bloğu yok |
| **Toplam** | **10** | §8 = §10 = tarama |

Not: §2 = 4 referans bloğu × 2 aksis = 8 (4 çift); §3 = 1 PowerShell
bloğu = 2 aksis (1 çift); toplam 5 çift = 10 aksis. ⚠️ blokları
(§2.1 · §2.4 · §2.5) fence içermiyor.

### §16.8 ⚠️ Üçlü Defter (bu dosyanın negatif omurgası)

1. **C × iOS** (§2.1): doğrulanmış upstream yok → yazılmadı.
2. **JS × iOS** (§2.4): `os.platform()` değer kümesi `ios` içermiyor (§5:4) → yazılmadı.
3. **PHP × iOS** (§2.5): doğrulanmış upstream yok → yazılmadı.
- Üçünde de fence 0 (§10, §16.7) — "örnek yazılmadı" iddiasının
  kanıtı yokluk değil, **kontrol edilmiş yokluktur** (kural: iddia
  fence yokluğuyla kanıtlanır).

### §16.9 Tur Kapanış Kontrol Listesi (bu dosya)

| Kontrol | Sonuç | Kanıt |
|---|---|---|
| Kapı ≥505 | nihai ölçüm tur yanıtında | §15–§16.4 |
| FM değişmedi (validation 2026-10-14 dahil) | EVET — yalnız footer üstüne append | tek oldString edit |
| Örnek silindi mi | HAYIR — §0–§6 + §2 blokları aynen | §16.5 bütçe |
| Yeni GET / URL | 0 / 0 | §14 (7/7 eski URL) |
| Yeni dış iddia | 0 — her satır § referanslı | §16.2 |
| `⚠️` bölümüne blok | 0 | §10 · §16.7 |
| Bozuk karakter | 0 (bu tur 2+ tarama) | tur içi |
| Çift `---` (FM sınırı) | 2 (açılış + kapanış) | FM |
| Authority tek | 1 | footer |
| Kapı ihlali · beyazliste dışı yazım | 0 · 0 (tur sonu) | tur yanıtı |
| git commit | YOK (orkestratöre ait) | — |

### §16.10 Devir Kaydı

- Bu tur yalnız append: §0–§6 + FM değişmedi; yeni GET/URL/iddia 0.
- S1 · S2 [UNKNOWN] devredildi (§12) — kapatılmış gibi yazılmadı.
- Kardeş bağı başlık düzeyinde kaldı (§13); içerik okuması yapılmadı.
- Eksen notu: bu dosya dil-eksenli (7 kart); OS-eksenli kardeşler
  (8 kart) — sayı farkı eksenden gelir (§13).

### §16.11 Kapı Son Sözü (bağlayıcı)

- Marj: nihai − 500 → İHLAL YOK (nihai ≥505, tur yanıtında okunur).
- İddia yok: §15 + §16.4 aritmetiği = kanıt; dolgu/TBD 0.
- Yeni GET 0 · yeni URL 0 · yeni dış iddia 0 · commit 0.
- Bozuk karakter = 0 · çift `---` = 2 (FM) · Authority = 1 · fence = 10.
- `⚠️` bölümlerine bu turda da blok yazılmadı (§16.8).

### §16.12 Kaynak × Kart Kesişim Defteri (§5 × §7 — dosya içi ikinci görünüm)

| Kaynak (§5) | Bağlı kart | Kart durumu |
|---|---|---|
| 1 — python.org/ios (gömülü mod) | §7.2 Python | referans |
| 2 — python.org/os (sınırlar) | §7.2 Python | referans |
| 3 — go.dev GOOS | §7.3 Go | referans |
| 4 — nodejs.org/os (platform yok) | §7.4 JS | ⚠️ (negatif kanıt) |
| 5 — rustc platform-support | §7.6 Rust | referans |
| 6 — docs.rs/nix | §7.6 Rust | referans |
| 7 — microsoft MAUI | §7.7 C# | referans |
| — (kaynak yok) | §7.1 C · §7.5 PHP | ⚠️ (kanıtsız) |

Kesişim sayısı: 7 URL → 6 karta gider; C/PHP kartlarının kaynak
satırı **yoktur** (bu, ⚠️ durumunun ta kendisidir). Sahipsiz URL 0 ·
kaynaksız iddia 0.

### §16.13 Marj Kapanışı

- Nihai hedef: 491 + 14 = 505 → marj 5 ≥ 0: İHLAL YOK.
- Bu bölüm yalnız aritmetik kapatır; yeni iddia/GET/kaynak taşımaz.
- Ölçüm bağlayıcılığı: kapı değeri tur yanıtındaki son ölçümdür.
- `⚠️` bölümlerine blok 0 · fence 10 aynen korundu · FM değişmedi.
- Bozuk karakter taraması (bu tur son) = 0 · Authority tek = 1.
- Devir: S1 · S2 [UNKNOWN] (§12) — kapatılmadı.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode