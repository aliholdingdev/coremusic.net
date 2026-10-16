---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-reactos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-reactos
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://reactos.org/
  - https://go.dev/doc/install/source
  - https://nodejs.org/api/os.html
  - https://doc.rust-lang.org/rustc/platform-support.html
  - https://learn.microsoft.com/en-us/dotnet/what-is-maui
validation:
  - "2026-10-14: 5 kaynak URL GET 200 ile dogrulandi; reactos.org ana sayfa Windows uyumluluk misyonunu tasir ama dil-bazli guvenlik API'si ICMEDIKLERI icin, diger 6 dil satiri da GOOS/os.platform/rustc hedef kumelerinde 'reactos' bulunmadigi (ve MAUI hedef listesinde olmadigi) icin ornek yazmadan VERIFICATION REQUIRED birakildi (7/7 kart)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — ReactOS (7 Dil)

## §0 ELI15

Bu kart, **ReactOS'ta 7 dil bölümünün** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını **taşımak yerine neden taşımadığını** belgeler: 2026-10-14 turunda hiçbir dil
için ReactOS'a özgü doğrulanmış upstream güvenlik kaynağı **bulunamadı** → 7/7 satır
`⚠️ VERIFICATION REQUIRED`, **örnek yazılmadı** (uydurmama kuralı).

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**; ReactOS'e özgü grep desenleri de **0 güvenlik isabeti**
> (negatif kanıt, §3). Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor"
> iddiası **YASAKTIR**.

> **Kanıt notu:** `reactos.org` 200 dönüyor; içerikte 0.4.16 sürümü (2026-08-29), 30. yıl ve
> "Windows uygulama/sürücü uyumluluğu" misyonu taşınır — ama **dil-bazlı güvenlik API'si
> (izin/ACL/token çağrısı) yoktur** → "Windows uyumludur, dolayısıyla Win32 çağrıları birebir
> geçer" gibi bir **eşdeğerlik iddiası yazılmaz** (L-03 kısıtı).

## §1 OS güvenlik profili × dil matrisi

| Dil | ReactOS güvenlik yüzeyi | Doğrulanmış kanıt (2026-10-14) | Durum |
|---|---|---|---|
| C | — | reactos.org ana sayfa 200 — dil-bazlı güvenlik API'si içerikte **YOK** | ⚠️ VERIFICATION REQUIRED |
| Python | — | `sys.platform` kümesinde `reactos` **YOK** (bkz `kod-ornekleri-python.md`; `win32` altındadır, ayrı değer yok) | ⚠️ VERIFICATION REQUIRED |
| Go | — | go.dev GOOS listesinde `reactos` **YOK** | ⚠️ VERIFICATION REQUIRED |
| JS (Node) | — | `os.platform()` değer kümesinde `reactos` **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP | — | php.net'te ReactOS'a özgü upstream sayfa doğrulanamadı | ⚠️ VERIFICATION REQUIRED |
| Rust | — | `windows` crate docs.rs platform satırı **yalnız** `x86_64-pc-windows-msvc` | ⚠️ VERIFICATION REQUIRED |
| C# | — | MAUI hedef listesi (Android/iOS/macOS/Windows) — ReactOS **YOK** | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 0 ✅ + 7 ⚠️. Bu bir eksiklik değil, **ölçülmüş sonuçtur**: hiçbir dilin upstream'i
ReactOS güvenlik yüzeyini doğrulamıyor → uydurma crate/fonksiyon/ad **üretilmez**.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Bu kartta **yazılı kod bloğu YOKTUR.** 7 kombinasyonun 7'si de
> `⚠️ VERIFICATION REQUIRED` — her biri için negatif kanıt §2.1–§2.7'de taşınır.
> "Repoda uygulanıyor" iddiası da **YASAKTIR** (§3).

### §2.1 — C: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `reactos.org` — ana sayfa 200; 0.4.16 (2026-08-29), 30. yıl,
> Windows uygulama/sürücü uyumluluk misyonu; **dil-bazlı güvenlik API'si (izin/ACL/token
> çağrısı) yok** → C yüzeyi doğrulanamadı, uydurma çağrı yazılmaz. Win32 API eşdeğerliği de
> iddia edilmez (L-03).

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.2 — Python: ⚠️ VERIFICATION REQUIRED

> Kanıt: `sys.platform` bilinen değerlerinde `reactos` **YOK** (negatif kanıt; Python
> Windows'ta `sys.platform == 'win32'` verir — ReactOS için ayrı bir platform değeri
> belgelenmemiştir, kaydı `kod-ornekleri-python.md` §2.1) → Python x ReactOS güvenlik
> yüzeyi doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Python zaten yok (§3) |

### §2.3 — Go: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `go.dev/doc/install/source` — GOOS listesi `android, darwin,
> dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1,
> windows`; `reactos` **YOK** → Go toolchain ReactOS hedefi sunmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Go zaten yok (§3) |

### §2.4 — JS (Node.js): ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `nodejs.org/api/os.html` — `os.platform()` değer kümesi
> `aix|darwin|freebsd|linux|openbsd|sunos|win32` (+ deneysel `android`); `reactos` **YOK** →
> Node x ReactOS platformu doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Node ReactOS kullanımı zaten yok (§3) |

### §2.5 — PHP: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14): php.net'te ReactOS'a özgü upstream sayfa **doğrulanamadı** →
> `chmod`/`posix_*` davranışının ReactOS'ta ne olduğu bilinmez; mevcutmuş gibi kod yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da PHP ReactOS kullanımı zaten yok (§3) |

### §2.6 — Rust: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `windows` crate docs.rs platform satırı **yalnız**
> `x86_64-pc-windows-msvc` (kod-ornekleri-rust.md §2.8 ile aynı ölçüm) → ReactOS uyumluluk
> kanıtı yok.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Rust zaten yok (§3) |

### §2.7 — C#: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `learn.microsoft.com/en-us/dotnet/what-is-maui` — MAUI hedef
> listesi **Android, iOS, macOS, Windows**; ReactOS **YOK** → .NET'in ReactOS yüzeyi bu
> kaynaktan doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C# zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`CreateJobObject|AdjustTokenPrivileges|DuplicateTokenEx|CryptProtectData` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| Win32 token/jobs API çağrısı | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| `reactos` kelimesi (genel) | grep | yalnız UA/metin eşleşmeleri olabilir — güvenlik API'si **değil** |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'CreateJobObject|AdjustTokenPrivileges|DuplicateTokenEx').Count  # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. ReactOS OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız ReactOS × dil yüzeyinin **doğrulanma durumu** taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; ReactOS platform geneli → `cekirdek-guvenlik-reactos`.

Ek sınır: ReactOS çekirdek geliştirme (sürücü/halka) bu dala yazılmaz; "ReactOS = Windows"
şeklinde bir **platform eşdeğerliği** bu kartta ne doğrulanır ne de varsayılır; uygulama
uyumluluk yüzdesi `UNKNOWN`'dur (kayıt: 3. tur notu).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://reactos.org/ | 0.4.16 sürümü (2026-08-29); 30. yıl; Windows uygulama/sürücü uyumluluk misyonu; dil-bazlı güvenlik API'si **içermez** | HTTP 200 · içerik okundu — misyon düzeyi |
| https://go.dev/doc/install/source | GOOS listesi — `reactos` **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer kümesi — `reactos` **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/rustc/platform-support.html | platform-support — ReactOS hedef üçlüsü **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/what-is-maui | MAUI hedefleri — ReactOS **yok** (negatif kanıt) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `wiki.reactos.org/...` → **bağlantı hatası** (yanıt
> alınamadı, 2026-10-14) · ReactOS iç sayfalarının security alt yolları bu turda **ayrı ayrı
> doğrulanmadı** → içeriği bilinmeyen URL'ye "var" iddiası bağlanmadı. Hiçbiri gövdeye
> alınmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | ReactOS hedefi mimaride var mı; varsa hangi dil ile güvenlik yüzeyi seçilecek | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (Win32 ACL mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode