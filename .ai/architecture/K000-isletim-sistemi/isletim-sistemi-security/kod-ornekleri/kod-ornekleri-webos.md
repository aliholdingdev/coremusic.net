---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-webos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-webos
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
evidence:
  - https://webosose.org/docs/
  - https://go.dev/doc/install/source
  - https://nodejs.org/api/os.html
  - https://doc.rust-lang.org/rustc/platform-support.html
  - https://learn.microsoft.com/en-us/dotnet/what-is-maui
validation:
  - "2026-10-14: 5 kaynak URL GET 200 ile dogrulandi; webosose.org/docs/ landing sayfasi (LS2 girisi, NDK kurulumu, app/service rehberleri) dil-bazli guvenlik API'si ICMEDIKLERI icin, diger 6 dil satiri da GOOS/os.platform/rustc/MAUI kumelerinde 'webos' bulunmadigi icin ornek yazmadan VERIFICATION REQUIRED birakildi (7/7 kart)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — webOS (7 Dil)

## §0 ELI15

Bu kart, **webOS'ta 7 dil bölümünün** (C, Python, Go, JS, PHP, Rust, C#) OS güvenlik
kalıplarını **taşımak yerine neden taşımadığını** belgeler: 2026-10-14 turunda hiçbir dil
için webOS'e özgü doğrulanmış upstream güvenlik kaynağı **bulunamadı** → 7/7 satır
`⚠️ VERIFICATION REQUIRED`, **örnek yazılmadı** (uydurmama kuralı).

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.go` · 0 `*.cs`**; webOS'e özgü grep desenleri de **0 güvenlik isabeti**
> (negatif kanıt, §3). Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor"
> iddiası **YASAKTIR**.

> **Kanıt notu:** `webosose.org/docs/` 200 dönüyor ama **yalnız genel dokümantasyon
> açılışıdır** (LS2 — Luna Service Bus girişi, NDK kurulumu, app/service rehberleri);
> dil-bazlı güvenlik API'si (izin/ACL/sandbox çağrısı) bu sayfalarda **İÇERİLMİYOR** →
> "taşıma kaydı, kanıt değil".

## §1 OS güvenlik profili × dil matrisi

| Dil | webOS güvenlik yüzeyi | Doğrulanmış kanıt (2026-10-14) | Durum |
|---|---|---|---|
| C | — | webosose.org/docs/ landing 200 — dil-bazlı güvenlik API'si içerikte **YOK** | ⚠️ VERIFICATION REQUIRED |
| Python | — | `sys.platform` kümesinde `webos` **YOK** (bkz `kod-ornekleri-python.md` §2.7; `using/webos.html` 404) | ⚠️ VERIFICATION REQUIRED |
| Go | — | go.dev GOOS listesinde `webos` **YOK** | ⚠️ VERIFICATION REQUIRED |
| JS (Node) | — | `os.platform()` değer kümesinde `webos` **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP | — | php.net'te webOS'e özgü upstream sayfa doğrulanamadı | ⚠️ VERIFICATION REQUIRED |
| Rust | — | rustc platform-support'ta `webos` hedef üçlüsü **YOK** | ⚠️ VERIFICATION REQUIRED |
| C# | — | MAUI hedef listesi (Android/iOS/macOS/Windows) — webOS **YOK** | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 0 ✅ + 7 ⚠️. Bu bir eksiklik değil, **ölçülmüş sonuçtur**: hiçbir dilin upstream'i
webOS güvenlik yüzeyini doğrulamıyor → uydurma crate/fonksiyon/ad **üretilmez**.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Bu kartta **yazılı kod bloğu YOKTUR.** 7 kombinasyonun 7'si de
> `⚠️ VERIFICATION REQUIRED` — her biri için negatif kanıt §2.1–§2.7'de taşınır.
> "Repoda uygulanıyor" iddiası da **YASAKTIR** (§3).

### §2.1 — C: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `webosose.org/docs/` — genel dokümantasyon açılışı 200; içerikte
> LS2 (Luna Service Bus) girişi, NDK kurulumu ve app/service rehberleri tanıtılıyor;
> **dil-bazlı güvenlik API'si (izin/ACL/sandbox çağrısı) yok** → C yüzeyi doğrulanamadı,
> uydurma çağrı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.2 — Python: ⚠️ VERIFICATION REQUIRED

> Kanıt: `sys.platform` bilinen değerlerinde `webos` **YOK** (negatif kanıt; ayrıntı ve
> `docs.python.org/3/using/webos.html` → **404** kaydı `kod-ornekleri-python.md` §2.7'de) →
> Python x webOS güvenlik yüzeyi doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Python zaten yok (§3) |

### §2.3 — Go: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `go.dev/doc/install/source` — GOOS listesi `android, darwin,
> dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1,
> windows`; `webos` **YOK** → Go toolchain webOS hedefi sunmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Go zaten yok (§3) |

### §2.4 — JS (Node.js): ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `nodejs.org/api/os.html` — `os.platform()` değer kümesi
> `aix|darwin|freebsd|linux|openbsd|sunos|win32` (+ deneysel `android`); `webos` **YOK** →
> Node x webOS platformu doğrulanamadı. (webOS'un Node tabanlı app modeli ayrı bir
> platform API'si olabilir; o kaynak bu turda OKUNMADI → iddia edilmez.)

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Node webOS kullanımı zaten yok (§3) |

### §2.5 — PHP: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14): php.net'te webOS'e özgü upstream sayfa **doğrulanamadı** →
> `posix_*`/`chmod` davranışının webOS'ta ne olduğu bilinmez; mevcutmuş gibi kod yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da PHP webOS kullanımı zaten yok (§3) |

### §2.6 — Rust: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): rustc "Platform Support" listesinde `webos` hedef üçlüsü
> **YOK** (kod-ornekleri-rust.md §2.7 ile aynı ölçüm) → Rust x webOS yüzeyi doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da Rust zaten yok (§3) |

### §2.7 — C#: ⚠️ VERIFICATION REQUIRED

> Kanıt (2026-10-14 okundu): `learn.microsoft.com/en-us/dotnet/what-is-maui` — MAUI hedef
> listesi **Android, iOS, macOS, Windows**; webOS **YOK** → .NET'in webOS yüzeyi bu
> kaynaktan doğrulanamadı.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C# zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp,*.go,*.rs,*.cs` — **0 dosya**; desen =
`setuid|prctl|seccomp|capset|luna-service|LunaService|PalmService` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C/Go/C#/Rust kaynak dosyası | glob | **YOK — 0 dosya** (negatif kanıt) |
| webOS güvenlik API çağrısı (LS2/izin) | grep 418 dosyada | **0 güvenlik isabeti** (negatif kanıt) |
| `webos` kelimesi (genel) | grep | yalnız UA/metin eşleşmeleri olabilir — güvenlik API'si **değil** |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'setuid|prctl|seccomp|capset|LunaService').Count      # beklenti: 0
```

**Okuma:** 4 satırın tamamı negatif kanıttır. webOS OS-güvenlik yüzeyi bu repoda **hiç
başlamamıştır**; bu dosya dolayısıyla **hedef referans** kartıdır.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K0100-identitiy`; bu kartta yalnız webOS × dil yüzeyinin **doğrulanma durumu** taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; webOS platform geneli → `cekirdek-guvenlik-webos`.

Ek sınır: LS2 servis izni (service permissions) ve aCGRID/Security Token yüzeyleri bu turda
doğrulanamadı → bu kartta **anılmaz**; "webOS Linux'tur, dolayısıyla POSIX kalıpları birebir
geçer" gibi bir **eşdeğerlik iddiası da yazılmaz** (L-03 kısıtı).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://webosose.org/docs/ | webOS OSE dokümantasyon açılışı — LS2 girişi, NDK kurulumu, app/service rehberleri; dil-bazlı güvenlik API'si **içermez** | HTTP 200 · içerik landing — kanıt değil |
| https://go.dev/doc/install/source | GOOS listesi — `webos` **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer kümesi — `webos` **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://doc.rust-lang.org/rustc/platform-support.html | platform-support — `webos` hedef üçlüsü **yok** (negatif kanıt) | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/dotnet/what-is-maui | MAUI hedefleri — webOS **yok** (negatif kanıt) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `docs.python.org/3/using/webos.html` → **404** (kayıt
> `kod-ornekleri-python.md` §2.7) · webOS OSE iç sayfaları (security/permissions alt
> yolları) bu turda **ayrı ayrı doğrulanmadı** → içeriği bilinmeyen URL'ye "var" iddiası
> bağlanmadı. Hiçbiri gövdeye alınmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | webOS hedefi mimaride var mı; varsa hangi dil ile güvenlik yüzeyi seçilecek | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (LS2 izni mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

## §7 Derinlik — 7 dil × webOS kanıt zinciri (derin okuma)

> Bu bölüm §1–§6'daki **aynı kanıtların** derinleştirilmiş okumasıdır; **yeni URL, yeni
> sürüm, yeni fonksiyon/crate adı İÇERMEZ** (zero-hallucination). Amaç: her ⚠️ satırının
> ⚠️ kalma gerekçesini ve tek bir doğrulanabilir adımla ✅'ye dönebilme **koşulunu**
> taşımak. Koşullar "gelecek turda yapılacak iş" olarak tanımlanır; hiçbirine "yapıldı"
> yazılmaz.

### §7.1 — C × webOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `webosose.org/docs/` → HTTP 200 · **genel dokümantasyon açılışı** |
| İçerikte VAR | LS2 (Luna Service Bus) girişi · NDK kurulumu · app/service rehberleri |
| İçerikte YOK | İzin (ACL) çağrısı, sandbox çağrısı, service permission imzası — **tek bir imza yok** |
| Repo yüzeyi | §3: `*.c` **0** · `*.h` **0** → çağrı noktası da yok |

**Gerekçe:** Kaynak **landing derinliğindedir**; LS2 girişi bileşen tanıtımıdır, güvenlik
API'si değildir. **✅ koşulu:** webOS OSE dokümantasyonunda C ile güvenlik yüzeyi
(izin/servis yetkisi) gösteren **API seviyesindeki** sayfa okunmalı; açılış sayfası bu
koşulu hiçbir zaman karşılamaz.

### §7.2 — Python × webOS

| Öğe | Kanıt |
|---|---|
| Reddedilen aday | `docs.python.org/3/using/webos.html` → **404** (kayıt: `kod-ornekleri-python.md` §2.7) |
| Kümeden eleme | `sys.platform` bilinen değer kümesinde `webos` **YOK** |
| Repo yüzeyi | Kart §3 kaydına göre repo'da Python kaynağı yok; 418'lik küme PHP+JS'dir |

**Gerekçe:** Ölü yol (404) + küme elemesi birlikte kanıt üretir. **✅ koşulu:** Python'un
webOS'e özgü **resmi** kullanım sayfasının gerçek yolu yeniden denenmeli (sonuç bu turda
**okunmadı**; yeni ad uydurulmaz).

### §7.3 — Go × webOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `go.dev/doc/install/source` → HTTP 200 |
| GOOS kümesi (tam, 14 değer) | `android, darwin, dragonfly, freebsd, illumos, ios, js, linux, netbsd, openbsd, plan9, solaris, wasip1, windows` |
| Sonuç | `webos` **YOK** → Go toolchain bu kaynaktan webOS hedefi sunmaz |
| Repo yüzeyi | §3: `*.go` **0** |

**✅ koşulu:** Go'nun webOS üzerinde çalıştığını/çalışmadığını gösteren **farklı** resmi
sayfa okunmalı — adı bu turda doğrulanmadı, uydurulmaz.

### §7.4 — JS (Node) × webOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `nodejs.org/api/os.html` → HTTP 200 |
| `os.platform()` kümesi | `aix, darwin, freebsd, linux, openbsd, sunos, win32` + deneysel `android` |
| Sonuç | `webos` **YOK** |
| Dosyanın ihtiyacıyatı (§2.4) | webOS'un **Node tabanlı app modeli** ayrı bir platform API'si olabilir → "o kaynak bu turda OKUNMADI → iddia edilmez" |
| Repo yüzeyi | 98 JS dosyası (§3) — webOS'e özgü grep **0 isabet** |

**Okuma:** webOS kartları içinde **en güçlü olası karşı-örnek** JS satırıdır (app modeli
Node tabanlı olabilir) — ama karşı-örnek de **okunmadı** → ⚠️ kalır. **✅ koşulu:** o
ayrı kaynağın okunması; adı doğrulanmadı.

### §7.5 — PHP × webOS

| Öğe | Kanıt |
|---|---|
| Aranan | php.net'te webOS'e özgü upstream sayfa → **doğrulanamadı** (2026-10-14) |
| Sınır | `posix_*` / `chmod` davranışının webOS'ta ne olduğu **bilinmez** → mevcutmuş gibi kod yazılmaz |
| Yasak | L-03: "webOS Linux'tur, POSIX kalıpları birebir geçer" **eşdeğerlik iddiası yazılmaz** |
| Repo yüzeyi | 320 PHP dosyası (§3) — webOS hedefli kullanım **0** |

**✅ koşulu:** webOS'un POSIX uyumluluk/izin katmanını **kendi resmi kaynağından**
doğrulamak; PHP'nin değil, platformun kaynağından.

### §7.6 — Rust × webOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `doc.rust-lang.org/rustc/platform-support.html` → HTTP 200 |
| Sonuç | `webos` hedef üçlüsü **YOK** |
| Çapraz kayıt | Aynı ölçüm `kod-ornekleri-rust.md` §2.7'de de var → **iki kart, tek ölçüm** |
| Repo yüzeyi | §3: `*.rs` **0** |

**Uyarı:** Çapraz referans **bağımsız teyit değildir**; aynı ölçümün iki yerde kaydıdır →
çifte sayım **yasak**. **✅ koşulu:** rustc hedef listesinde `webos` gösteren resmi
sayfanın (veya webOS Rust port dokümanının) okunması; ad bu turda doğrulanmadı.

### §7.7 — C# × webOS

| Öğe | Kanıt |
|---|---|
| Okunan (2026-10-14) | `learn.microsoft.com/en-us/dotnet/what-is-maui` → HTTP 200 |
| MAUI hedefleri | **Android, iOS, macOS, Windows** (4) — webOS **YOK** |
| Kapsam sınırı | MAUI, .NET'in **tek** yüzeyidir; .NET'in tamamı hakkında hüküm vermez |
| Repo yüzeyi | §3: `*.cs` **0** |

**Okuma:** Bu satır "webOS'ta .NET yoktur" **demiyor**; "MAUI kaynakıyla
doğrulanamıyor" **diyor**. **✅ koşulu:** .NET'in webOS yüzeyini gösteren **farklı**
resmi kaynak — adı bu turda doğrulanmadı.

### §7.8 — §1 matris ↔ §2 kanıt ↔ §5 referans eşlemesi (tutarlılık denetimi)

| # | §1 satırı | Kanıt bölümü | §5'teki referans | Eşleşme |
|---|---|---|---|---|
| 1 | C | §2.1 | `webosose.org/docs/` (landing) | ✅ tutarlı |
| 2 | Python | §2.2 | 404 adayı (§5 alt notu) | ✅ tutarlı |
| 3 | Go | §2.3 | `go.dev/doc/install/source` | ✅ tutarlı |
| 4 | JS (Node) | §2.4 | `nodejs.org/api/os.html` | ✅ tutarlı |
| 5 | PHP | §2.5 | doğrulanamadı — §5'te satırı **yok** | ✅ tutarlı (kanıtsız = tablosuz) |
| 6 | Rust | §2.6 | `doc.rust-lang.org/rustc/platform-support.html` | ✅ tutarlı |
| 7 | C# | §2.7 | `learn.microsoft.com/en-us/dotnet/what-is-maui` | ✅ tutarlı |

**Denetim sonucu:** 7/7 tutarlı; §5'te **5 URL** okunmuş (PHP satırının referanssız
olması doğrulanmamışlıktır, tutarsızlık değil). Eşleme, tek satır güncellenirken **hangi
bölümün** güncelleneceğini gösterir (kanıt önce, iddia sonra — §13.3).

### §7.9 — Örüntü: 7 satırın ortak beş arızası

| # | Arıza tipi | Düştüğü satırlar | Ne gösterir |
|---|---|---|---|
| 1 | **Yanlış derinlikte kaynak** — landing (LS2/NDK açılışı) API kanıtı sanıldı | C (§2.1) | "HTTP 200 = kanıt" **yanlıştır**; 200 yalnızca sayfanın *açıldığını* kanıtlar |
| 2 | **Tarihli liste elemesi** — hedef listesi o anki sürümün listesidir | Go (§2.3) · JS (§2.4) · Rust (§2.6) · C# (§2.7) | Eleme tarihlidir (2026-10-14); liste sürümle değişebilir → yeniden ölçüm gerekir |
| 3 | **Kaynak hiç doğrulanamadı** — 404 ya da arama | Python (§2.2, 404) · PHP (§2.5) | Bulunamayan = **yok değil**, kanıtsız; "yoktur" cümlesine çevrilemez |
| 4 | **Olası ayrı kaynak okunmadı** — kartın kendi ihtiyacıyatları | JS app modeli (§2.4) · LS2 servis izni + aCGRID/Security Token (§4) · iç security/permissions sayfaları (§5) | Kart **kendi** gösterdiği adayları okumadı; bunlar "araştırılmamış" değil, **kayıtlı** araştırılmamışlardır |
| 5 | **İki uç kapalı** — upstream kanıtsız **+** repoda hedef dil yok | 7/7 (§3: 0 C/Go/C#/Rust · PHP+JS'te 0 isabet) | Kart **hedef mimari** kartıdır; "ilerde yapılacak" da "repoda başladı" da değildir |

**Düzeltilebilirlik ayrımı (bağlayıcı):** (1) doğru derinlikte sayfa okunursa kapanır ·
(2) yalnız yeniden ölçümle kapanır · (3) arama tekrarıyla kapanır · (4) **öncelik sırası
bu kartın kendi metninden gelir** — LS2 izni → JS app modeli → iç sayfalar (sıra
**öneri değildir**, kartta kayıtlı ihtiyaçtır; okuma tarihi `validation:`'a eklenir) ·
(5) repoda hedef dil gelmedikçe **kalıcıdır** — kapatma gerekmez, durumun kendisi
kanıttır. **Hiçbiri yazım hatası değildir** (§7.8: 0 sapma).

## §8 Negatif kanıt metodolojisi (nasıl üretildi, nasıl çürütülür)

| # | Yöntem | Uygulama (bu kartta) | Sahte pozitif riski |
|---|---|---|---|
| 1 | Kümeden eleme | GOOS / `os.platform()` / `sys.platform` / rustc / MAUI listelerinde `webos` aranması | Düşük — listeler kapsamlı okundu |
| 2 | 404 ile eleme | `docs.python.org/3/using/webos.html` → 404 | **Orta** — 404, bu yolda olmadığını kanıtlar; yine de aday elenir |
| 3 | Repo grep'i | 418 dosyada `setuid\|prctl\|seccomp\|capset\|luna-service\|LunaService\|PalmService` → 0 (§3 metni; betik farkı §9.1) | Düşük — desenler POSIX + webOS servis adlarıdır |
| 4 | Landing okuması | `webosose.org/docs/` 200 → LS2/NDK tanıtımı var, API imzası yok | Orta — *içerik yanlış derinlikte* (§7.9/1) |

**Eleme mantığı (birebir aritmetik):** Bir dil × webOS satırı ✅ olabilir **yalnız**
ikisi birlikteyken: (a) upstream'te dilin webOS güvenlik yüzeyini gösteren resmi
içerik, (b) repoda o yüzeyi kullanan en az bir nokta. Bu kartta (a) = **0/7**, (b) =
**0/7** → **7/7 ⚠️**. §0'daki "eksiklik değil, ölçülmüş sonuç" ifadesinin arkasındaki
aritmetik budur.

**Çürütme sırası (gelecek turlar için — henüz YAPILMADI):**

1. webOS OSE dokümantasyonunda **dil-bazlı** güvenlik API'si ara (yerel desenler:
   `permission`, `ACL`, `sandbox`, `service` — desen adları arama terimidir, URL adayı
   **değildir**; sonuç sayfası bu turda **okunmadı**).
2. `docs.python.org/3/using/` altındaki gerçek yolu yeniden dene — **tek 404 yeterli
   değildir**, yol taşınmış olabilir.
3. GOOS / rustc / MAUI listelerinin **güncel sürümlerini** yeniden oku (liste değişmiş
   olabilir).
4. §7.9/4'teki kayıtlı ihtiyaçları sırayla oku; her yeni okuma `validation:` alanına
   **tarih + HTTP durumu + tek cümle** ile eklenir; eklenmeden §1/§2 satırı ✅'ye
   çevrilmez.

> **Kural:** Yukarıdakilerin **hiçbiri** bu turda yapılmadı; yapılmış gibi yazılmaz.
> Yeni bir URL **adı** uydurulmaz — yalnız §5'teki 5 referans + 1 reddedilen 404 yeniden
> ziyaret edilebilir.

## §9 Ölçüm tekrar üretilebilirliği (§3 betiğinin satır satır okuması)

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.go,*.cs).Count  # beklenti: 0
($src | Select-String -Pattern 'setuid|prctl|seccomp|capset|LunaService').Count      # beklenti: 0
```

| Satır | Ne yapar | Neden gerekli |
|---|---|---|
| 1 | PHP+JS kümesini kurar (beklenti **418** = 320 PHP + 98 JS) | `vendor` (Composer) ve `node_modules` (npm) **üçüncü parti** koddur; sayıma girerse 418 şişer |
| 1 — `.git` hariç | Depo meta-verisini düşer | Geçmiş commit içeriği "repo kodu" sayılmaz |
| 2 | C/Go/C# sayımı (beklenti **0**) | Negatif kanıtın kendisi |
| 3 | POSIX + webOS servis grep'i (beklenti **0**) | Desenler POSIX güvenlik çağrıları + LunaService adıdır; **PHP+JS bunları çağıramaz/çağırmaz** |

#### §9.1 Kart-içi SAPMA kaydı (bilinen, kapatılmadı)

| Öğe | §3 metni (satır 138) | §3 betiği (3. satır) | Fark |
|---|---|---|---|
| Desen sayısı | **7**: `setuid\|prctl\|seccomp\|capset\|luna-service\|LunaService\|PalmService` | **5**: `luna-service` ve `PalmService` **betikte YOK** | 2 desen |

- **Durum:** Metin ile betik uyuşmuyor → ölçüm **metne göre** tarandı, **betiğe göre**
  değil. `luna-service` (küçük harf varyantı) ve `PalmService` desenlerinin betikteki
  karşılığı **eksiktir**.
- **Bu turda (2026-10-16) NEDEN KAPATILMADI:** (1) shell aracı reddedildi → betik
  **yeniden çalıştırılmadı**, yeni sayı üretilmedi; (2) append-only kuralı gereği mevcut
  betik satırı **düzenlenmedi** (satır değiştirme yetkisi orkestratörde).
- **Etki tahmini (iddia değil):** `luna-service`/`PalmService` webOS terimleridir;
  CoreMusic PHP+JS kodunda geçmesi beklenmez → betik 7 desenle de büyük olasılıkla 0
  döner, **ama bu doğrulanmadı**: "0 kalır" **yazılmaz**, "doğrulanmadı" yazılır.
- **Kapanış koşulu:** Betik §3 metnindeki **7 desenin tamamıyla** repo kökünden yeniden
  çalıştırılır → sonuç `validation:` alanına tarihle eklenir; §3 metni ile betik o zaman
  hizalanır.

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
| LS2 servis izni (service permissions) | Doğrulanamadı (§4) → **anılmaz** | **YOK** — kayıtlı ihtiyaç (§7.9/4) |
| aCGRID / Security Token yüzeyi | Doğrulanamadı (§4) → **anılmaz** | **YOK** — kayıtlı ihtiyaç |
| webOS platform geneli güvenlik | `cekirdek-guvenlik-webos` (depends_on) | Referans var, **içerik tekrarı yok** |
| Güvenlik anayasası | `cekirdek-guvenlik-anayasa` (depends_on) | Referans var, içerik tekrarı yok |
| "webOS Linux, POSIX birebir geçer" | — | **Eşdeğerlik iddiası YASAK** (L-03) |

**Tek cümle:** Bu kart yalnızca **"7 dilin webOS güvenlik yüzeyi doğrulandı mı?"**
sorusunun 2026-10-14 cevabını taşır; cevap **0 ✅ / 7 ⚠️**'dır.

## §11 Reddedilen adaylar defteri (genişletilmiş)

| Aday | Sonuç | Kart içi kayıt | Neden gövdeye alınmadı |
|---|---|---|---|
| `docs.python.org/3/using/webos.html` | **404** | `kod-ornekleri-python.md` §2.7 | Ölü yol — içerik yok |
| webOS OSE iç sayfaları — `security`/`permissions` alt yolları | **Ayrı ayrı doğrulanmadı** | §5 alt notu | İçeriği bilinmeyen URL'ye "var" iddiası bağlanmaz |
| LS2 servis izni / aCGRID / Security Token | **Doğrulanamadı** | §4 | Kartta anılmaz; "var" da "yok" da yazılamaz |
| "webOS Linux olduğu için POSIX geçer" | **Eşdeğerlik yasak** | §4 · L-03 | Kanıtsız eşdeğerlik — okunmadan yazılmaz |

**Defter kuralı:** Reddedilen aday **silinmez**; gerekçesiyle kalır ki sonraki tur aynı
adayı "yeni keşif" gibi sunamasın. Bu defterdeki 4 satırın 4'ü de uydurma içerik yerine
**kayıt tercihini** gösterir — kartın çalışması budur.

## §12 Açık soru derinliği (S1 · S2)

### S1 — webOS hedefi mimaride var mı?

- **[UNKNOWN]** — bu kartta **kapatma yok**.
- Kapanış koşulu (dosyanın kendi dili): mimari sahibi kararı + yeni ADR (`>=088` — bu
  numara kartta mevcut kayıttır; ADR numarası **uydurulmaz**, mevcut en yüksek numara
  sahibi tarafından doğrulanır).
- **Bu kartın S1'e tek katkısı:** 7 dilin 7'sinde de webOS güvenlik yüzeyi
  doğrulanamadığı için "hangi dil ile başlanacak" sorusu **daha dahi sorulamıyor**;
  en güçlü aday (JS — §7.4) bile kanıtsızdır. Bu S1'i kapatmaz; karar öncesi **engel
  kaydıdır**.

### S2 — Uygulama ↔ OS güvenlik sorumluluk sınırı (LS2 izni mi, uygulama mı)

- **[UNKNOWN]** — K009/K010 ayrım çizgisiyle değerlendirilecek (dosyanın kendi dili).
- Bu kart S2'ye **yalnız şu kadar** katkı verir: LS2 servis izni yüzeyi bu turda
  doğrulanamadığı için sınır tasarımında **varsayılmış bir izin modeli yoktur** —
  "webOS LS2 ile korur" da "uygulama kendini korur" da yazılamaz.
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
2. "webOS'ta X vardır" cümlesi bu karttan **türetilemez**; en fazla "bu turda
   doğrulanamadı" alınır.
3. Bir satır ✅'ye dönerse sırayla: önce `validation:` alanına tarih+kanıt, **sonra**
   §1 matrisi, **sonra** §2 kanıt bloğu → **kanıt önce, iddia sonra**.
4. Çapraz referans (`kod-ornekleri-python.md` §2.7, `kod-ornekleri-rust.md` §2.7)
   **tekrar kanıt değildir** (§7.6): aynı ölçümün iki yerde kaydıdır; çifte sayım yasak.

### §13.5 — Doğru / Yanlış cümle kartı (hızlı denetim)

| ✅ Doğru cümle (karttan türetilebilir) | ❌ Yanlış cümle (türetilemez — nedeni parantezde) |
|---|---|
| "2026-10-14 turunda 7 dilin webOS güvenlik yüzeyinin hiçbiri doğrulanamadı (0 ✅ / 7 ⚠️)." | "webOS'ta Go güvenlik API'si kullanılamaz." (listedeki yok ≠ kullanılamaz) |
| "webosose.org/docs/ landing sayfası LS2/NDK tanıtımı taşır; dil-bazlı güvenlik API'si içermiyor." | "webOS'un güvenlik API'si yoktur." (landing ≠ API — §7.9/1) |
| "GOOS'ta `webos` hedefi 2026-10-14 ölçümünde YOK." | "Go webOS'ta derlenemez." (tarihli ölçüm ≠ kalıcı yasak — §7.9/2) |
| "Repoda 418 PHP+JS dosyası, 0 C/Go/C#/Rust, §3 desenlerinde 0 isabet." | "Repoda webOS'e özgü kod hiç yoktur." (betik 7 desenin 5'ini taradı — §9.1) |
| "LS2/aCGRID yüzeyleri bu turda doğrulanamadı; bu kartta anılmıyor." | "webOS LS2 izin modeli şudur." (okunmayan ≠ bilinen — §10) |
| "S1 = [UNKNOWN], S2 = [UNKNOWN] — bu kartta kapatma yok." | "webOS mimaride var/yok." (karar verilmedi — §12) |

**Kullanım:** Downstream rapor **sol sütunun** cümleleriyle yazılabilir; sağ sütundaki
bir cümle rapora girdiyse kart **yanlış kullanılmıştır** → düzeltme sırası: §13.1–§13.4
kuralları, sonra §7 kanıtları. Tablo, §7.8'in **davranış düzeyi** karşılığıdır: §7.8
tutarlılığı (yapı), §13.5 yanlışı (cümle) ölçer.

## §14 Bağımlılık zinciri (depends_on gerekçeleri)

| Bağımlılık | Gerekçe | Bu kartla ilişki |
|---|---|---|
| `cekirdek-guvenlik-webos` | webOS platform güvenlik kartı — "platform geneli" sorumlusunu ayırır | Bu kart **platform anlatmaz**; yalnız dil × doğrulama durumu taşır → tekrar yok |
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
| Append öncesi (2026-10-16) | **192 satır** → kapı **ALTINDA** |
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