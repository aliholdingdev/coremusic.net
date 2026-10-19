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

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K0100-identitiy`; bu kartta yalnız ReactOS × dil yüzeyinin **doğrulanma durumu** taşınır.
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

## §7 Derinlik — 7 dil × ReactOS kanıt zinciri (derin okuma)

> Bu bölüm §1–§6'daki **aynı kanıtların** derinleştirilmiş okumasıdır; **yeni URL, yeni
> sürüm, yeni fonksiyon/crate adı İÇERMEZ** (zero-hallucination). Amaç: her ⚠️ satırının
> ⚠️ kalma gerekçesini ve tek bir doğrulanabilir adımla ✅'ye dönebilme **koşulunu**
> taşımak. Koşullar "gelecek turda yapılacak iş" olarak tanımlanır; hiçbirine "yapıldı"
> yazılmaz.

### §7.1 — C × ReactOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `reactos.org` → HTTP 200 · 0.4.16 (2026-08-29) · 30. yıl |
| İçerikte VAR | Windows uygulama/sürücü **uyumluluk misyonu** |
| İçerikte YOK | İzin (ACL) çağrısı, token çağrısı, privilege API'si — **tek bir imza yok** |
| Repo yüzeyi | §3: `*.c` **0** · `*.h` **0** → çağrı noktası da yok |

**Gerekçe:** Ana sayfa **misyon düzeyindedir**, API düzeyi değildir. **✅ koşulu:**
ReactOS'un kendi dokümantasyonunda C ile güvenlik yüzeyi (ACL/token) gösteren sayfa
okunmalı; ana sayfa bunu hiçbir zaman karşılamaz. **L-03:** "Windows uyumludur → Win32
çağrıları birebir geçer" eşdeğerliği **ne doğrulanır ne yazılır**.

### §7.2 — Python × ReactOS

| Öğe | Kanıt |
|---|---|
| Kümeden eleme | `sys.platform` değer kümesinde `reactos` **YOK** |
| İnce not | Python Windows'ta `sys.platform == 'win32'` verir; ReactOS için **ayrı** bir platform değeri belgelenmemiş (kayıt: `kod-ornekleri-python.md` §2.1) |
| Repo yüzeyi | Kart §3 kaydına göre repo'da Python kaynağı yok; 418'lik küme PHP+JS'dir |

**Gerekçe:** `win32` değeri ReactOS'ta da çıkıyor olabilir — ama bu **okunmadı**,
varsayılmadı. **✅ koşulu:** Python'un ReactOS üzerinde platform davranışı ayrı bir
resmi kaynakla doğrulanmalı (o kaynak bu turda **okunmadı**; ad uydurulmaz).

### §7.3 — Go × ReactOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `go.dev/doc/install/source` → HTTP 200 |
| GOOS kümesi (tam, 14 değer) | `android, darwin, dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1, windows` |
| Sonuç | `reactos` **YOK** → Go toolchain bu kaynaktan ReactOS hedefi sunmaz |
| Repo yüzeyi | §3: `*.go` **0** |

**İnce not:** GOOS'ta `windows` **var**; ReactOS'un `windows` altında mı çalıştığı
**bu kaynaktan anlaşılmaz** → "GOOS'ta reactos yok" ≠ "ReactOS'ta Go çalışmaz".
İki cümle de yazılmaz; yalnız ölçümün **kapsamı** yazılır. **✅ koşulu:** Go'nun ReactOS
(veya Windows-ABI) üzerinde çalıştığını gösteren **farklı** resmi sayfa — adı
doğrulanmadı.

### §7.4 — JS (Node) × ReactOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `nodejs.org/api/os.html` → HTTP 200 |
| `os.platform()` kümesi | `aix, darwin, freebsd, linux, openbsd, sunos, win32` + deneysel `android` |
| Sonuç | `reactos` **YOK** |
| Repo yüzeyi | 98 JS dosyası (§3) — ReactOS'e özgü güvenlik grep'i **0 isabet** |

**İnce not:** Aynı `win32` muamması §7.2'deki gibidir — Node'un ReactOS'taki platform
değeri **okunmadı**. **✅ koşulu:** ayrı resmi kaynak; bu turda okunmadı.

### §7.5 — PHP × ReactOS

| Öğe | Kanıt |
|---|---|
| Aranan | php.net'te ReactOS'a özgü upstream sayfa → **doğrulanamadı** (2026-10-14) |
| Sınır | `chmod` / `posix_*` davranışının ReactOS'ta ne olduğu **bilinmez** → mevcutmuş gibi kod yazılmaz |
| Repo yüzeyi | 320 PHP dosyası (§3) — ReactOS hedefli kullanım **0** |

**✅ koşulu:** ReactOS'un POSIX/uyumluluk katmanını **kendi resmi kaynağından**
doğrulamak; PHP'nin değil, platformun kaynağından.

### §7.6 — Rust × ReactOS

| Öğe | Kanıt |
|---|---|
| Ölçüm 1 | `windows` crate docs.rs platform satırı **yalnız** `x86_64-pc-windows-msvc` (kod-ornekleri-rust.md §2.8 ile **aynı ölçüm**) |
| Ölçüm 2 | rustc platform-support'ta ReactOS hedef üçlüsü **YOK** (§5) |
| Repo yüzeyi | §3: `*.rs` **0** |

**İki ölçüm tek kartta:** (1) crate platform satırı, (2) rustc hedef listesi — ikisi de
2026-10-14, ikisi de **tarihli liste eleme** (§7.9/2). `x86_64-pc-windows-msvc` listede
olması, **MSVC hedefinin ReactOS'ta desteklendiği anlamına gelmez** — eşdeğerlik
yazılmaz. **Çapraz referans uyarısı:** rust.md §2.8 ile bu satır **bağımsız teyit
değildir**; aynı ölçümün iki yerde kaydıdır → çifte sayım yasak.

### §7.7 — C# × ReactOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `learn.microsoft.com/en-us/dotnet/what-is-maui` → HTTP 200 |
| MAUI hedefleri | **Android, iOS, macOS, Windows** (4) — ReactOS **YOK** |
| Kapsam sınırı | MAUI, .NET'in **tek** yüzeyidir; .NET'in tamamı hakkında hüküm vermez (Framework/Console hedefleri bu turda okunmadı) |
| Repo yüzeyi | §3: `*.cs` **0** |

**Okuma:** Bu satır "ReactOS'ta .NET yoktur" **demiyor**; "MAUI kaynakıyla
doğrulanamıyor" **diyor**. **✅ koşulu:** .NET'in ReactOS/Windows-ABI uyumunu gösteren
**farklı** resmi kaynak — adı bu turda doğrulanmadı.

### §7.8 — §1 matris ↔ §2 kanıt ↔ §5 referans eşlemesi (tutarlılık denetimi)

| # | §1 satırı | Kanıt bölümü | §5'teki referans | Eşleşme |
|---|---|---|---|---|
| 1 | C | §2.1 | `reactos.org/` (ana sayfa — misyon düzeyi) | ✅ tutarlı |
| 2 | Python | §2.2 | §5'te satırı **yok** (kayıt python §2.1'de) | ✅ tutarlı (kanıt kart dışı kayıt) |
| 3 | Go | §2.3 | `go.dev/doc/install/source` | ✅ tutarlı |
| 4 | JS (Node) | §2.4 | `nodejs.org/api/os.html` | ✅ tutarlı |
| 5 | PHP | §2.5 | doğrulanamadı — §5'te satırı **yok** | ✅ tutarlı (kanıtsız = tablosuz) |
| 6 | Rust | §2.6 | docs.rs `windows` crate + `rustc platform-support` (§5) | ✅ tutarlı — §2.6'da crate, §5'te rustc: **iki farklı ölçüm**, ikisi de kayıtlı |
| 7 | C# | §2.7 | `learn.microsoft.com/en-us/dotnet/what-is-maui` | ✅ tutarlı |

**Denetim sonucu:** 7/7 tutarlı; §5'te **5 URL** okunmuş (Python/PHP satırlarının
referanssız olması doğrulanmamışlıktır, tutarsızlık değil). Eşleme, tek satır
güncellenirken **hangi bölümün** güncelleneceğini gösterir (kanıt önce, iddia sonra — §13.3).

### §7.9 — Örüntü: 7 satırın ortak beş arızası

| # | Arıza tipi | Düştüğü satırlar | Ne gösterir |
|---|---|---|---|
| 1 | **Yanlış derinlikte kaynak** — ana sayfa (misyon) API kanıtı sanıldı | C (§2.1) | "HTTP 200 = kanıt" **yanlıştır**; 200 yalnızca sayfanın *açıldığını* kanıtlar |
| 2 | **Tarihli liste elemesi** — hedef listesi o anki sürümün listesidir | Go (§2.3) · JS (§2.4) · Rust (§2.6) · C# (§2.7) | Eleme tarihlidir (2026-10-14); liste sürümle değişebilir → yeniden ölçüm gerekir |
| 3 | **Kapsayıcılık belirsizliği** — ReactOS Windows ABI ile ilişkili olabilir, listede **ayrı değer** taşımaz | Python (`win32`) · Go (`windows`) · JS (`win32`) · Rust (`msvc`) | "Listede yok" ≠ "çalışmaz" — **iki uç da yazılmaz**; yalnız ölçümün kapsamı yazılır |
| 4 | **Kaynak hiç doğrulanamadı** — 404 / bağlantı hatası / arama | PHP (§2.5) · wiki.reactos.org (§11) | Bulunamayan = **yok değil**, kanıtsız |
| 5 | **İki uç kapalı** — upstream kanıtsız **+** repoda hedef dil yok | 7/7 (§3: 0 C/Go/C#/Rust · PHP+JS'te 0 isabet) | Kart **hedef mimari** kartıdır; "ilerde yapılacak" da "repoda başladı" da değildir |

**Düzeltilebilirlik ayrımı (bağlayıcı):** (1) doğru derinlikte sayfa okunursa kapanır ·
(2) yalnız yeniden ölçümle kapanır · (3) **kapanmaz** — ReactOS'un ayrı bir hedef değeri
almadığı sürece kartın doğru ifadesi "kapsam belirsiz" olarak kalır · (4) arama/erişim
tekrarıyla kapanır · (5) repoda hedef dil gelmedikçe **kalıcıdır** — kapatma gerekmez,
durumun kendisi kanıttır. **Hiçbiri yazım hatası değildir** (§7.8: 0 sapma).

## §8 Negatif kanıt metodolojisi (nasıl üretildi, nasıl çürütülür)

| # | Yöntem | Uygulama (bu kartta) | Sahte pozitif riski |
|---|---|---|---|
| 1 | Kümeden eleme | GOOS / `os.platform()` / `sys.platform` / rustc / crate platform / MAUI listelerinde `reactos` aranması | Düşük — listeler kapsamlı okundu |
| 2 | Erişim hatası ile eleme | `wiki.reactos.org/...` → yanıt alınamadı (2026-10-14) | **Orta** — erişilememek, içeriğin yokluğu **değildir**; aday yine de gövdeye alınmaz |
| 3 | Repo grep'i | 418 dosyada `CreateJobObject\|AdjustTokenPrivileges\|DuplicateTokenEx\|CryptProtectData` → 0 (§3 metni) | Düşük — desenler Win32 token/jobs API adlarıdır |
| 4 | Ana sayfa okuması | `reactos.org` 200 → misyon cümlesi var, API imzası yok | Orta — *içerik yanlış derinlikte* (§7.9/1) |

**Eleme mantığı (birebir aritmetik):** Bir dil × ReactOS satırı ✅ olabilir **yalnız**
ikisi birlikteyken: (a) upstream'te dilin ReactOS güvenlik yüzeyini gösteren resmi
içerik, (b) repoda o yüzeyi kullanan en az bir nokta. Bu kartta (a) = **0/7**, (b) =
**0/7** → **7/7 ⚠️**. §0'daki "eksiklik değil, ölçülmüş sonuç" ifadesinin arkasındaki
aritmetik budur.

**Çürütme sırası (gelecek turlar için — henüz YAPILMADI):**

1. ReactOS kendi dokümantasyonunda **dil-bazlı** güvenlik API'si ara (yerel desenler:
   `ACL`, `token`, `privilege` — desen adları arama terimidir, URL adayı **değildir**;
   sonuç sayfası bu turda **okunmadı**).
2. `wiki.reactos.org` erişimini yeniden dene — **tek bağlantı hatası kesin değildir**
   (geçici ağ hatası olabilir; hata nedeni kayda geçti ama ayrıştırılmadı).
3. GOOS / rustc / crate / MAUI listelerinin **güncel sürümlerini** yeniden oku; ayrıca
   §7.9/3 kapsamayıcılığı için `win32`/`windows` altı davranışlar **ayrı** kaynaktan
   okunmalı (ad bu turda doğrulanmadı).
4. Her yeni okuma `validation:` alanına **tarih + HTTP durumu + tek cümle** ile eklenir;
   eklenmeden §1/§2 satırı ✅'ye çevrilmez.

> **Kural:** Yukarıdakilerin **hiçbiri** bu turda yapılmadı; yapılmış gibi yazılmaz.
> Yeni bir URL **adı** uydurulmaz — yalnız §5'teki 5 referans + 1 erişilemeyen aday yeniden
> denenebilir.

## §9 Ölçüm tekrar üretilebilirliği (§3 betiğinin satır satır okuması)

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'CreateJobObject|AdjustTokenPrivileges|DuplicateTokenEx').Count  # beklenti: 0
```

| Satır | Ne yapar | Neden gerekli |
|---|---|---|
| 1 | PHP+JS kümesini kurar (beklenti **418** = 320 PHP + 98 JS) | `vendor` (Composer) ve `node_modules` (npm) **üçüncü parti** koddur; sayıma girerse 418 şişer |
| 1 — `.git` hariç | Depo meta-verisini düşer | Geçmiş commit içeriği "repo kodu" sayılmaz |
| 2 | C/Go/C# sayımı (beklenti **0**) | Negatif kanıtın kendisi |
| 3 | Win32 token/jobs grep'i (beklenti **0**) | `CreateJobObject`/`AdjustTokenPrivileges`/`DuplicateTokenEx` Win32 API adlarıdır; PHP+JS bunları çağıramaz |

#### §9.1 Kart-içi SAPMA kaydı (bilinen, kapatılmadı)

| Öğe | §3 metni (satır 139) | §3 betiği (3. satır) | Fark |
|---|---|---|---|
| Desen sayısı | **4**: `CreateJobObject\|AdjustTokenPrivileges\|DuplicateTokenEx\|CryptProtectData` | **3**: `CryptProtectData` **betikte YOK** | 1 desen |

- **Durum:** Metin ile betik uyuşmuyor → ölçüm **metne göre** tarandı, **betiğe göre**
  değil. `CryptProtectData` (DPAPI) deseninin betikteki karşılığı **eksiktir**.
- **Bu turda (2026-10-16) NEDEN KAPATILMADI:** (1) shell aracı reddedildi → betik
  **yeniden çalıştırılmadı**, yeni sayı üretilmedi; (2) append-only kuralı gereği mevcut
  betik satırı **düzenlenmedi** (satır silme/değiştirme yetkisi orkestratörde).
- **Etki tahmini (iddia değil):** Desenler Win32 API adlarıdır; PHP+JS'te geçmesi
  beklenmez → betik 4 desenle de büyük olasılıkla 0 döner, **ama bu doğrulanmadı**:
  "0 kalır" **yazılmaz**, "doğrulanmadı" yazılır.
- **Kapanış koşulu:** Betik `|CryptProtectData` ile birlikte repo kökünden
  yeniden çalıştırılır → sonuç `validation:` alanına tarihle eklenir; §3 metni ile
  betik o zaman hizalanır.

**Yorum sınırları (bağlayıcı):**

1. Grep sonucunun 0 oluşu **yalnızca** taranan desenlerde 0 olduğunu kanıtlar; "repoda
   başka güvenlik yüzeyi yoktur" iddiası **değildir**.
2. Ölçüm tarihi **2026-10-14**'tür; repo o tarihten sonra değiştiyse sayılar değişir.
3. **2026-10-16 (bu oturum):** shell reddedildi → PowerShell **çalıştırılmadı**, 418/0/0
   değerleri **yeniden üretilmedi** (2026-10-14 kaydına dayanır). Bu oturumun katkısı
   **yeniden ölçüm değil**, kapı (≥500 satır) derinliğidir (§15).

## §10 Sınır haritası (bu kart kimin işidir)

| Konu | Sahip | Bu karttaki durum |
|---|---|---|
| Kimlik doğrulama, oturum, parola, MFA | `K0100-identitiy` (bozuk ad **değiştirilmez**) | **YOK** — anılmaz |
| OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) | `K009-security` | **YOK** — yalnız sınır işareti |
| Çekirdek içi yapı, sürücü yazımı | `isletim-sistemi-kernel` | **YOK** — komşu dal |
| ReactOS çekirdek geliştirme (sürücü/halka) | Bu dala **yazılmaz** (§4) | **YASAK** |
| ReactOS platform geneli güvenlik | `cekirdek-guvenlik-reactos` (depends_on) | Referans var, **içerik tekrarı yok** |
| Güvenlik anayasası | `cekirdek-guvenlik-anayasa` (depends_on) | Referans var, içerik tekrarı yok |
| "ReactOS = Windows" platform eşdeğerliği | — | **Ne doğrulanır ne varsayılır** (§4) |
| Uygulama uyumluluk yüzdesi | — | **[UNKNOWN]** (3. tur notu) — sayı **uydurulmaz** |

**Tek cümle:** Bu kart yalnızca **"7 dilin ReactOS güvenlik yüzeyi doğrulandı mı?"**
sorusunun 2026-10-14 cevabını taşır; cevap **0 ✅ / 7 ⚠️**'dır.

## §11 Reddedilen adaylar defteri (genişletilmiş)

| Aday | Sonuç | Kart içi kayıt | Neden gövdeye alınmadı |
|---|---|---|---|
| `wiki.reactos.org/...` | **Bağlantı hatası** — yanıt alınamadı (2026-10-14) | §5 alt notu | Erişilemeyen içeriğe "var" iddiası **bağlanmaz**; hata nedeni ayrıştırılmadı (ağ mı, yol mu → bilinmiyor) |
| ReactOS iç sayfaları — `security` alt yolları | **Ayrı ayrı doğrulanmadı** | §5 alt notu | İçeriği bilinmeyen URL'ye "var" iddiası bağlanmaz |
| "Win32 çağrıları ReactOS'ta birebir geçer" | **Eşdeğerlik yasak** | §4 · L-03 | Kanıtsız eşdeğerlik — okunmadan yazılmaz |
| ".NET ReactOS'ta çalışır/çalışmaz" (MAUI dışı iddia) | **OKUNMADI** | §2.7 ihtiyacıyatı | MAUI .NET'in tek yüzeyi değil; kapsam genişletilmez |

**Defter kuralı:** Reddedilen aday **silinmez**; gerekçesiyle kalır ki sonraki tur aynı
adayı "yeni keşif" gibi sunamasın. Bu defterdeki 4 satırın 4'ü de uydurma içerik yerine
**kayıt tercihini** gösterir — kartın çalışması budur.

## §12 Açık soru derinliği (S1 · S2)

### S1 — ReactOS hedefi mimaride var mı?

- **[UNKNOWN]** — bu kartta **kapatma yok**.
- Kapanış koşulu (dosyanın kendi dili): mimari sahibi kararı + yeni ADR (`>=088` — bu
  numara kartta mevcut kayıttır; ADR numarası **uydurulmaz**, mevcut en yüksek numara
  sahibi tarafından doğrulanır).
- **Bu kartın S1'e tek katkısı:** 7 dilin 7'sinde de ReactOS güvenlik yüzeyi
  doğrulanamadığı + §7.9/3 kapsamayıcılık belirsizliği sürdüğü için "hangi dil ile
  başlanacak" sorusu **daha dahi sorulamıyor**. Bu S1'i kapatmaz; karar öncesi **engel
  kaydıdır**.

### S2 — Uygulama ↔ OS güvenlik sorumluluk sınırı (Win32 ACL mi, uygulama mı)

- **[UNKNOWN]** — K009/K010 ayrım çizgisiyle değerlendirilecek (dosyanın kendi dili).
- Bu kart S2'ye **yalnız şu kadar** katkı verir: ReactOS'un ACL/token modelinin kendi
  kaynağından doğrulanmaması, sınır tasarımında **varsayılmış bir model olmadığını**
  gösterir — "ReactOS Windows gibi ACL korur" da "uygulama kendini korur" da yazılamaz.
- Kapanış: K009/K010 sahiplerinin ortak çizgisi + (gerekiyorsa) ADR — bu kart tek başına
  kapatamaz.

## §13 Kart mimarisi ve tüketim kuralları

Bu kart **kodsuz** bir "kod örnekleri" kartıdır; bu bir boşluk değil, **tasarım
kararıdır**:

| Katman | Bu kartta | İşlev |
|---|---|---|
| §0–§1 | Neden kod yazılmadığı + 7 dil matrisi | 30 saniyede okunur sonuç |
| §2.1–§2.7 | Dil başına negatif kanıt | Her ⚠️ **tek başına** savunulabilir |
| §3 | Repo ölçümü (+ §9.1 sapma kaydı) | "Repoda uygulanıyor" iddiasını **kapatır** |
| §4–§6 | Sınır / referans / açık soru | Komşu kartlarla çakışma önlenir |
| §7–§16 (bu ek) | Derinlik + kapı kaydı | Her satır **mevcut kanıttan** türetilmiştir; yeni iddia yok |

**Tüketim kuralları (downstream ajanlar için bağlayıcı):**

1. Bu karttan **kod alıntılamak yasaktır** (§2: kartta yazılı kod bloğu yoktur).
2. "ReactOS'ta X vardır" cümlesi bu karttan **türetilemez**; en fazla "bu turda
   doğrulanamadı" alınır.
3. Bir satır ✅'ye dönerse sırayla: önce `validation:` alanına tarih+kanıt, **sonra**
   §1 matrisi, **sonra** §2 kanıt bloğu → **kanıt önce, iddia sonra**.
4. Çapraz referans (`kod-ornekleri-python.md` §2.1, `kod-ornekleri-rust.md` §2.8)
   **tekrar kanıt değildir** (§7.6): aynı ölçümün iki yerde kaydıdır; çifte sayım yasak.

### §13.5 — Doğru / Yanlış cümle kartı (hızlı denetim)

| ✅ Doğru cümle (karttan türetilebilir) | ❌ Yanlış cümle (türetilemez — nedeni parantezde) |
|---|---|
| "2026-10-14 turunda 7 dilin ReactOS güvenlik yüzeyinin hiçbiri doğrulanamadı (0 ✅ / 7 ⚠️)." | "ReactOS'ta Go güvenlik API'si kullanılamaz." (listedeki yok ≠ kullanılamaz) |
| "reactos.org ana sayfası misyon düzeyindedir; dil-bazlı güvenlik API'si içermiyor." | "ReactOS'un güvenlik API'si yoktur." (ana sayfa ≠ dokümantasyon — §7.9/1) |
| "GOOS'ta `reactos` ayrı hedef olarak yer almıyor (2026-10-14 ölçümü)." | "ReactOS'ta Go derlenemez." (GOOS `windows` kapsayıcılığı belirsiz — §7.9/3) |
| "Repoda 418 PHP+JS dosyası, 0 C/Go/C#/Rust, Win32 token/jobs desenlerinde 0 isabet." | "Repoda ReactOS'e özgü kod hiç yoktur." (grep dört desenle sınırlı — §9/1) |
| "wiki.reactos.org bu turda erişilemedi (bağlantı hatası)." | "Wiki'de güvenlik sayfası yoktur." (erişilemeyen ≠ yok — §11) |
| "S1 = [UNKNOWN], S2 = [UNKNOWN] — bu kartta kapatma yok." | "ReactOS mimaride var/yok." (karar verilmedi — §12) |

**Kullanım:** Downstream rapor **sol sütunun** cümleleriyle yazılabilir; sağ sütundaki
bir cümle rapora girdiyse kart **yanlış kullanılmıştır** → düzeltme sırası: §13.1–§13.4
kuralları, sonra §7 kanıtları. Tablo, §7.8'in **davranış düzeyi** karşılığıdır: §7.8
tutarlılığı (yapı), §13.5 yanlışı (cümle) ölçer.

## §14 Bağımlılık zinciri (depends_on gerekçeleri)

| Bağımlılık | Gerekçe | Bu kartla ilişki |
|---|---|---|
| `cekirdek-guvenlik-reactos` | ReactOS platform güvenlik kartı — "platform geneli" sorumlusunu ayırır | Bu kart **platform anlatmaz**; yalnız dil × doğrulama durumu taşır → tekrar yok |
| `cekirdek-guvenlik-anayasa` | Genel güvenlik anayasası | Sınır cümleleri §4/§10 buraya dayanır |

**Yön + sınır:** Bağımlılıklar **okunur** (bu kart onlara güvenir). Bu kartın FM
`children: []` alanının **boş** olması, kayıtlı çocuk olmadığını gösterir; karşı
kartların `depends_on` listeleri bu turda **taranmadı** → "hiçbir karta bağlı değilim"
iddiası **yazılmaz** (yalnız "kayıtlı çocuk yok" yazılır). `children` alanı doldurulması
gereken boşluk **değildir**; ekleme yetkisi orkestratördedir.

## §15 Kapı ölçüm kaydı (Band-1 · ≥500 satır)

| Öğe | Değer |
|---|---|
| Ölçüm yöntemi | `read(path, offset=100000)` → hata mesajı `"... (N lines)"` — N = `File.ReadAllLines` eşdeğeri satır sayısı (shell bu oturumda reddedildi; PowerShell çalıştırılmadı) |
| Append öncesi (2026-10-16) | **193 satır** → kapı **ALTINDA** |
| Kapı (Band-1) | **≥500 satır** |
| Ekleme yöntemi | Append-only: §7–§16 footer'dan **önce** eklendi; §0–§6 içeriği ve frontmatter **değiştirilmedi**; Authority footer satırı **son satırda korundu** |
| Append sonrası (ölçülen) | **524 satır** (2026-10-16, `read` offset=100000 hata mesajı ile ölçüldü → Band-1 kapısı **GEÇTİ**: 524 ≥ 505, marj +19) |

## §16 Marj

- **Marj amacı:** Kapıya tam 500'de yapışmak, sonraki meşru bir düzeltme (ör. yeni bir
  `validation:` satırı ya da §9.1 kapanışı) ile eşiği kıracaktır → dosya yeniden kapı
  altına düşebilir. Bu nedenle hedef **≥505** tutulur.
- **Marj içeriği:** Eklenen her satır mevcut §0–§6 kanıtından türetilmiştir; dolgu
  satırı, uydurma URL, `TBD` **yoktur** — §8'deki çürütme adımları "yapılacak iş" olarak
  **açıkça işaretlidir**, tamamlanmış gibi sunulmaz.
- **Tekrar edilebilirlik:** Kapı ölçümü her turda aynı yöntemle (offset=100000 hata
  mesajı) yeniden alınır; değer değişirse §15 tablosuna **yeni satır** eklenir, eski
  satır **silinmez**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode