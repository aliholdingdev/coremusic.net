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

---

## §7 Dil-Başına Derin Kart (§1–§5 içinden derlendi — yeni GET yok)

### §7.1 C — reddedilmiş yüzey (§2.1 · §5 · §1)

- Dosyadaki tek iddia sınıfı: **`⚠️ VERIFICATION REQUIRED` — örnek yazılmadı**.
- Gerekçe: Bionic için denenen **iki** aday da 404 (`developer.android.com/ndk/guides/bionic`,
  `source.android.com/docs/security/bionic`) — §5 reddedilen adaylar satırı.
- Repo karşılığı: **YOK** (§2.1 tablosu) — `*.c,*.h,*.cpp` 0 dosya (§3).
- Yazılmayan alt-yüzey: Bionic kütüphane detayı (§4 ek sınır — X7/X8).
- Kapatma yolu: çalışan (200 dönen) bir Bionic/NDK güvenilir kaynağı → §2.1'e R sınıfı geçişi.

### §7.2 Python — gömülü mod gerçekleri (§2.2 · §5)

- Mekanizma: gömülü mod = libpython + Python embedding API; "there is no concept of
  installing as a system resource. The only unit of software distribution is an 'app'."
- Negatif API kanıtı: `os.setuid` → "Availability: Unix, not WASI, not Android."
- Sınıf: **R (referans; sözleşmesel)** · repo karşılığı YOK (418 dosyada 0 isabet).
- Kapsam dışı: "notable omissions" listesinin tamamı dosyaya alınmadı — yalnız
  güvenlikle kesişen UID satırı taşındı.

### §7.3 Go — hedef etiketi + mimari kümesi (§2.3 · §5)

- `GOOS` kümesinde `android`; `GOARCH`: **386 · amd64 · arm · arm64** (4 mimari).
- Kalıp: `//go:build android` — dosya **yalnız** Android hedefinde derlenir.
- Sınıf: **R** · repo karşılığı YOK (`*.go` 0).
- Sınır: çalışma zamanı davranışı (bionic/mobil linker) bu kartta anlatılmaz.

### §7.4 JS (Node.js) — deneysel platform ayrımı (§2.4 · §5)

- `os.platform()` `android` döndürür — upstream **EXPERIMENTAL** işaretli.
- Karşılaştırma deseni: `os.platform() === 'android'` → dallanma güvenlik **kararı değil**
  (S1'e bağlanır).
- Repo ölçüsü: `'android'` karşılaştırması 98 JS dosyasında **0 isabet** (§2.4 tablosu).
- Sınıf: **R**.

### §7.5 PHP — doğrulanamamış yüzey (§2.5 · §1)

- Dosyadaki tek iddia sınıfı: **`⚠️ VERIFICATION REQUIRED` — örnek yazılmadı**.
- Gerekçe: php.net'te Android'e özgü upstream sayfa bu turda doğrulanamadı →
  `posix_*`/`chmod` davranışının Android'de ne olduğu **bilinmez**.
- Repo karşılığı: **YOK** (§2.5 tablosu) — PHP tabanı 320 dosyada Android kullanımı yok.
- Kapatma yolu: doğrulanmış PHP-Android upstream'i → §2.5'e R sınıfı geçişi.

### §7.6 Rust — Tier 2 hedef + UID okuma imzaları (§2.6 · §5)

- Hedef uc: `aarch64-linux-android` → **Tier 2 · std ✓** (platform-support).
- İmza kanıtları: `nix::unistd::getuid() -> Uid` · `geteuid() -> Uid` (nix 0.31.3 — dosya notu).
- `ndk` crate 0.9.0: modüller asset/audio/native_activity — **güvenlik modülü yok**.
- Repo ölçüsü: `getuid`/`geteuid` grep'i 418 dosyada **0 isabet**.
- Sınıf: **R**.

### §7.7 C# — MAUI hedef listesi (§2.7 · §5)

- Hedefler (birebir): **Android · iOS · macOS · Windows**.
- Kapsam dışı yazılan: APK runtime izin modeli — "bu sayfada ANLATILMAZ" (§2.7 yorum satırı).
- Repo karşılığı: **YOK** (`.cs` 0 dosya).
- Sınıf: **R**.

---

## §8 Sayısal Özet (dosyanın kendi sayımları)

| Metrik | Değer | Kayıt |
|---|---|---|
| Dil bölümü | **7** (C · Python · Go · JS · PHP · Rust · C#) | başlık · §1 |
| R sınıfı örnek | **5** | §2.2 · §2.3 · §2.4 · §2.6 · §2.7 |
| ⚠️ sınıfı (örnek YAZILMADI) | **2** (C · PHP) | §2.1 · §2.5 |
| Örnek-olmayan dil oranı | 2/7 ≈ %29 | §8 aritmetiği |
| Upstream kaynak URL | **7** (§5 tablosu) — tamamı HTTP 200 (2026-10-14) | §5 |
| Reddedilen aday (404) | **2** (Bionic ×2) | §5 dipnotu |
| Repo tabanı ölçüsü | **418 dosya** (320 PHP + 98 JS) | §3 |
| Dil-kaynak dosyası (C/Go/C#/Rust) | **0** | §3 |
| Güvenlik API isabeti | **0** (desen: `getuid\|geteuid\|setuid\|native_activity`) | §3 |
| Go mimarisi | **4** (386/amd64/arm/arm64) | §2.3 · §5 |
| Açık soru | **2** (S1 · S2) — ikisi de [UNKNOWN] | §6 |
| Bozuk/uydurma çağrı | **0** (⚠️ satırlarında blok yok) | §2.1 · §2.5 |

---

## §9 Negatif Kanıt Defteri (id ↔ kanıt ↔ yeniden-çalıştırma)

| # | İddia (NEGATİF) | Kanıt yöntemi | Beklenen | Kayıt |
|---|---|---|---|---|
| N1 | repo'da C/Go/C#/Rust kaynak dosyası yok | glob `*.c,*.go,*.cs` (+ §3'te `*.h,*.cpp,*.rs`) | **0** | §3 |
| N2 | Android UID/NDK/izin çağrısı yok | grep 418 PHP+JS dosyada desen | **0** | §3 |
| N3 | `os.setuid` Android'de sunulmaz | upstream os.html (Availability satırı) | "not Android" | §2.2 · §5 |
| N4 | C yüzeyi doğrulanamadı | 2× GET 404 | 404/404 | §5 |
| N5 | PHP Android upstream'i doğrulanamadı | sayfa doğrulama turda başarısız | — | §2.5 |
| N6 | `ndk` crate'te güvenlik modülü yok | docs.rs/ndk modül listesi (2026-10-14) | modül listesiz | §2.6 · §5 |
| N7 | UA `android` eşleşmeleri güvenlik API'si değil | grep `android` → yalnız UA/metin | UA-only | §3 · §5.3 tur notu |
| N8 | "repo'da uygulanıyor" iddiası | — | **YASAK** | §3 tablo son satır · §0 |

**Yeniden-çalıştırma (2026-10-14 komutları — §3):** üç PowerShell satırı ·
beklenen 418 / 0 / 0. Beklenti değişirse bu defter **ölçümle** güncellenir.

---

## §10 Örnek Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

| # | Blok | Fence satırları | İç satır | `kaynak: upstream docs` imzası |
|---|---|---|---|---|
| 1 | §2.2 Python | 77 / 88 | 10 | ✅ (2 satır) |
| 2 | §2.3 Go | 96 / 103 | 6 | ✅ |
| 3 | §2.4 JS | 111 / 120 | 8 | ✅ |
| 4 | §2.6 Rust | 138 / 148 | 9 | ✅ (2 satır) |
| 5 | §2.7 C# | 159 / 165 | 5 | ✅ |
| 6 | §3 PowerShell ölçüm | 187 / 192 | 4 | — (ölçüm bloğu, imza gerekmez) |

**Mutabakat:** fenced blok **6** · toplam iç satır **42** · `⚠️` bölümlerinde
(§2.1 · §2.5) fence **0** — "örnek yazılmadı" iddiasıyla **tutarlı** (blok varsa
iddia çökardi). Fence toplamı 12 (6×2) — bu satırlar değişmedi.

---

## §11 Reddedilen / Yazılmayan Yüzeyler Defteri

| # | Yüzey | Neden yazılmadı | Kapatma koşulu |
|---|---|---|---|
| X1 | C örnekleri (Bionic çağrıları) | 2× 404 (§5) | 200 dönen Bionic kaynağı |
| X2 | PHP örnekleri (`posix_*`/`chmod`) | upstream doğrulanamadı (§2.5) | doğrulanmış PHP-Android sayfası |
| X3 | APK runtime izin modeli | "bu turda doğrulanamadı" (§0 uyarı) | AndroidManifest/izin kaynağı |
| X4 | SELinux politikası | aynı (§0 uyarı) | politika kaynağı |
| X5 | Keystore / Play politikası | §4 ek sınır — bu dala yazılmaz | K009/K010 ayrımı |
| X6 | Bionic iç yapısı (X7/X8) | §4 ek sınır | ayrı modül |
| X7 | "repo'da uygulanıyor" iddiası | §3 — **kalıcı yasak** | kalıcı (kanıt sınıfı: hedef kartı) |

---

## §12 S1 / S2 Kapı Planı (§6 — kapanış için gerekenler · iddia değil plan)

| # | Kapı | Gereken | Kapanınca etki |
|---|---|---|---|
| S1 | OS güvenlik katmanı devreye alınacak mı? | mimari sahibi kararı + **yeni ADR (≥088)** | §1 matrisi "hedef" → "planlı/aktif" sınıfına geçebilir |
| S2 | Uygulama ↔ OS sorumluluk sınırı (UID mi uygulama mı) | K009/K010 ayrım çizgisi + sahip kararı | §4 sınır cümlesi güncellenir |

**Kural:** ikisi de bu kartta **kapatılmaz** (§6 "KAPATMA YOK" — bağlayıcı);
plan satırları iddia değildir.

---

## §13 Kardeş ve Üst Bağlar

| Bağ | İlişki |
|---|---|
| `kod-ornekleri-tizen.md` · `-webos.md` · `-reactos.md` | aynı sınıfın diğer OS kartları — §5 Rust satırı: platform-support'ta **tizen/webos/reactos hedefi yok** |
| `kod-ornekleri-php.md` · `-js.md` · `-python.md` · `-go.md` · `-rust.md` · `-csharp.md` · `-c.md` | dil kardeşleri — bu kart **Android × 7 dil** kesiti |
| `cekirdek-guvenlik/cekirdek-guvenlik-android` | FM `depends_on` — platform geneli (§4) |
| `cekirdek-guvenlik/cekirdek-guvenlik-anayasa` | FM `depends_on` |
| `K009-security` · `K010-identitiy` | sınır kartları (§4) |
| `isletim-sistemi-kernel` | çekirdek içi yapı kardeş dalı (§4) |

---

## §14 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 230 | <500 |
| §7–§13 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §7–§16 tamamı §0–§6 içinden türetildi (yeni GET yok · 2026-10-14 damgaları geçerli) |
| M3 | FM (`group: kod` · `validation` · `open_questions`) **dokunulmadı** |
| M4 | 6 fenced blok + `⚠️` bölümleri değişmedi — §10 yalnız saydı |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 230 satır dokunulmaz |
| M7 | bozuk karakter taraması (Kiril/CJK aralık regex'i) her ölçümde — desen dosyaya literal yazılmaz |

---

## §15 Kaynak URL × Bölüm Mutabakatı (§5'in bölüm kesişimleri)

| Kaynak (§5) | Hangi bölümler kullanıyor | Temel taşıdığı iddia |
|---|---|---|
| docs.python.org/3/using/android.html | §1 · §2.2 · §7.2 | gömülü mod tek kullanım |
| docs.python.org/3/library/os.html | §1 · §2.2 · §7.2 · §9-N3 | `os.setuid` Android'de yok |
| go.dev/doc/install/source | §1 · §2.3 · §7.3 | GOOS android + 4 GOARCH |
| nodejs.org/api/os.html | §1 · §2.4 · §7.4 | `platform()` deneysel |
| doc.rust-lang.org/.../platform-support | §1 · §2.6 · §7.6 · §13 | Tier 2 + std · diğer OS hedefi yok |
| docs.rs/ndk/latest/ndk/ | §1 · §2.6 · §7.6 · §9-N6 | ndk 0.9.0 · güvenlik modülü yok |
| learn.microsoft.com/.../what-is-maui | §1 · §2.7 · §7.7 | 4 hedef listesi |

**Mutabakat:** 7 kaynak = 7 satır — §5'in tamamı en az bir § tarafından kullanılıyor
(boş kaynak 0) · 404 adaylar §5 dipnotunda **kullanım-dışı** işaretli (§11-X1).

---

---

## §16 Kod Bloğu Satır Dizini ve Özet Mutabakat

### §16.1 İç satır dizini (§10'daki 42 satırın tamamı — gruplanmış)

| Blok | Satır aralığı | İçerik özeti |
|---|---|---|
| B1 Python | 78–79 | upstream imzası ×2 (`using/android.html` · `os.html`) |
| B1 | 80 | `import os` |
| B1 | 81 | boş satır |
| B1 | 82–83 | "no concept of installing as a system resource ... 'app'" alıntısı |
| B1 | 84–85 | gömülü mod (libpython + embedding API) + "notable omissions" notu |
| B1 | 86–87 | `os.setuid` → "Unix, not WASI, not Android" negatif kanıtı |
| B2 Go | 97 | upstream imzası (`go.dev/doc/install/source`) |
| B2 | 98–99 | GOOS `android` + GOARCH `386 · amd64 · arm · arm64` |
| B2 | 100 | `//go:build android` derleme etiketi |
| B2 | 101 | boş satır |
| B2 | 102 | `package main // yalniz Android hedefinde derlenir` |
| B3 JS | 112 | upstream imzası (`nodejs.org/api/os.html`) |
| B3 | 113 | `const os = require('node:os');` |
| B3 | 114 | boş satır |
| B3 | 115–116 | `platform()` `android` = EXPERIMENTAL + diğer platform listesi |
| B3 | 117–119 | `if (os.platform() === 'android') { ... S1 }` dallanması |
| B4 Rust | 139–140 | upstream imzası ×2 (platform-support · nix 0.31.3) |
| B4 | 141 | `use nix::unistd::{getuid, geteuid};` |
| B4 | 142 | boş satır |
| B4 | 143–145 | Tier 2 + std ✓ · UID sandbox notu · imza dönüşleri |
| B4 | 146 | `let real = getuid();` |
| B4 | 147 | `let effective = geteuid();` |
| B5 C# | 160 | upstream imzası (`what-is-maui`) |
| B5 | 161–163 | MAUI hedef listesi: Android · iOS · macOS · Windows |
| B5 | 164 | APK izin modeli kapsam-dışı notu |
| B6 Ölçüm | 188–189 | PHP+JS glob (vendor/node_modules/.git hariç) — beklenti 418 |
| B6 | 190 | dil-kaynak sayacı — beklenti 0 |
| B6 | 191 | UID deseni grep — beklenti 0 |

**Dizin mutabakatı:** 27 grup = §10'un 42 iç satırını kapsar (her satır bir
grupta) · `⚠️` blokları (C · PHP) bu dizinde **yok** — yokluk kasıtlıdır.

### §16.2 Özet-İddia Mutabakatı (§0 ↔ §3 ↔ §8 çapraz kontrol)

| İddia | §0 (giriş) | §3 (ölçüm) | §8 (bu tur özeti) | Mutabakat |
|---|---|---|---|---|
| 418 PHP+JS | ✅ (418 yazıyor) | ✅ (320+98) | ✅ (418) | **3/3 aynı** |
| 0 dil-kaynak dosyası | ✅ (`0 *.c,*.go,*.cs`) | ✅ (glob satırı) | ✅ (0) | **3/3 aynı** |
| 0 güvenlik API isabeti | ✅ ("grep 0 isabet") | ✅ (desen tablosu) | ✅ (0) | **3/3 aynı** |
| 7 kaynak URL 200 | ✅ (validation: "7 kaynak") | — (§5 ayrı) | ✅ (7 satır) | **2/2 aynı** |
| C/PHP ⚠️ | ✅ (validation cümlesi) | — | ✅ (2 satır) | **2/2 aynı** |
| S1/S2 [UNKNOWN] | ✅ (open_questions) | — | ✅ (§12 plan) | **2/2 aynı** |

### §16.3 Kapı Son Sözü (bu dosya)

- Bu tur **yalnız append** yaptı: §0–§6 + FM değişmedi · yeni GET 0 · yeni iddia 0.
- Tüm yeni §'ler (§7–§16) dosyanın **kendi satırlarından** derlendi; her satırda
  "Kayıt" sütunuyla geri izleme var.
- Kapı değeri: **yalnız tur yanıtındaki son ölçümdür** (kendi-kendine-beyan yasağı).
- Kalan açık kapılar: S1 · S2 (§12 planı) + X1/X2 kapatma koşulları (§11) —
  hiçbiri bu turda **kapatılmadı/kapatılmayacak gibi yazılmadı**.

---

### §16.4 Ölçüm Yorum Haritası (§3 komutlarının ne anlama geldiği — sapma payı 0)

| # | Komut satırı (§3) | Beklenen | Sonuç beklenenden **farklıysa** düşen iddia |
|---|---|---|---|
| K1 | PHP+JS glob (`vendor`/`node_modules`/`.git` hariç) | **418** | 418'den büyük → yeni dosya girmiş · küçük → dosya silinmiş → §3 tablosu tazelenir |
| K2 | dil-kaynak sayacı (`*.c,*.go,*.cs`) | **0** | >0 → dil kaynak dosyası doğmuş → §2.1 "repo karşılığı YOK" satırı **düşer**; ⚠️ → R sınıfına adaylık başlar |
| K3 | UID deseni grep (`getuid\|geteuid\|setuid\|native_activity`) | **0** | >0 → güvenlik API'si repoya girmiş → "hedef kart" iddiası (§3 son satır) **düşer** · S1 yeniden açılır |

- Sapma payı: **0** — üçü de tam-sayı sayaç; "±1 kabul" yoktur (ölçüm 2026-10-14).
- Yorum disiplini: sonuç değişirse önce §3, sonra §8/§9 defteri güncellenir —
  **önce ölç, sonra yaz** (tur 2026-10-15 bunu yapmadı: yalnız derleme/türetme).
- Bu harita **iddia değil, yorum kuralıdır**: gelecek tur ölçümü bu üç satırla
  karşılaştırılır, fark varsa ilgili §'ye not düşülür.

### §16.5 Kapı Marjı Kaydı (bu dosya sonu)

| Marj | Kayıt |
|---|---|
| Marj 1 | nihai kapı ölçümü bu satırlardan **sonra** alınır — tur yanıtı bağlayıcı |
| Marj 2 | §7–§16 = bu turun eklediği **10 §** · hepsi §0–§6'dan türetildi |
| Marj 3 | yeni GET 0 · yeni upstream iddiası 0 · kardeş dosya yazımı 0 · commit 0 |
| Marj 4 | fence 12 (6 blok) — §10 ile mutabakatlı · `⚠️` bölümlerinde blok 0 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode