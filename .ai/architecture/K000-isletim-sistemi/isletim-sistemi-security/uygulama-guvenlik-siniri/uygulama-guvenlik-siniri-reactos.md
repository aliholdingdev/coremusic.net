---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-reactos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://reactos.org/architecture/
  - https://raw.githubusercontent.com/reactos/reactos/master/README.md
  - https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Win32
  - https://web.archive.org/web/20250817111135/https://reactos.org/wiki/Csrss.exe
  - https://web.archive.org/web/20260722043216/https://reactos.org/wiki/WINE
  - https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Ntdll.dll
  - https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/user-mode-and-kernel-mode
  - home.coremusic.net/.htaccess:10
validation:
  - "2026-10-14: 7 kaynak URL GET ile HTTP 200 dogrulandi (1 architecture + 1 raw README + 4 wiki arsiv + 1 MS Learn); wiki/canli Anubis challenge (4188 bayt); 2 aday URL 404 ile reddedildi ve hicbir yere yazilmadi."
  - "2026-10-14: repo sinir-terim taramasi 721 kod dosyasinda 0 isabet (ntdll|csrss|win32k|kernel32|gdi32|PreviousMode|ProbeForRead|AppContainer ve seccomp|bubblewrap|sandbox-exec|JobObject|setcap|prctl()); .htaccess L10 ve L18-19 satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Güvenlik Sınırı — ReactOS OS Kartı

## §0 ELI15

Bu kart **uygulama-guvenlik-siniri** alt-dalının ReactOS sürümüdür: bir
uygulamanın işletim sistemiyle temas ettiği sınır **nereden
geçer** — Win32 DLL katmanı → NTDLL Native API → çekirdek — bu sınır neyi
doğrular, uyumluluk ile güvenlik nerede çekişir. Her iddia **2026-10-14'te
okunmuş** bir kaynağa (ReactOS architecture sayfası, GitHub README, dört wiki
arşiv kopyası veya MS Learn) bağlıdır; kaynağı olmayan satır `UNKNOWN` kalır.

> **Erişim dürüstlüğü (önce oku):** `reactos.org/wiki/*` canlı adresleri
> 2026-10-14'te **HTTP 200 + Anubis 1.19.1 challenge** döndürüyor (4.188 bayt
> gövde = bot doğrulama sayfası, içerik değil). Wiki içeriği bu nedenle
> **Wayback Machine arşiv kopyalarından** okunmuştur; hangi iddianın hangi
> URL'den geldiği §5'te yazılıdır.

> **Öncelikli gerçek:** Sınırın iki uçluğu kaynakta net okunuyor: (1)
> architecture sayfası Win32 API'yi **"lowest layer of user mode"** ilan eder
> ve uyumluluk politikasını "implementing their bugs too ... **unless they
> introduce security issues**" diye kurar — yani hata-taklidi güvenlikle
> sınırda biter; (2) CSRSS wiki kopyası, Win32 kütüphanelerinin sistem
> çağrısı yerine CSRSS'e IPC gönderdiğini, bunu **"without compromising the
> kernel"** diye yapar. Kart "ReactOS uygulamaları güvenle çalışır" demez;
> yalnızca sınırın mimarisini ve olgunluk sınırını (README: **Alpha**)
> birlikte taşır.

## §1 ReactOS uygulama güvenlik sınırı profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Win32 alt sistemi (user mode) | "the 32bit Windows/ReactOS subsystem responsible for executing x86 executables" | uygulamanın ilk sınır kapısı | Yüksek (wiki/Win32 arşiv, oldid=35065) |
| Sınırın çekirdek-yazı birimleri | `Kernel32.dll` (base services) · `Gdi32.dll` (GDI) · `User32.dll` (user interface) · `Win32k.sys` (kernel level support) | modül kesiti | Yüksek (wiki/Win32 arşiv) |
| Kalan kütüphane yüzeyi | "There are roughly 40 other libraries that make up the rest of Win32" | sınırın genişliği | Yüksek (wiki/Win32 arşiv) |
| NTDLL Native API | "Native API user mode entry points" + process startup + module loading + RTL | uygulama↔çekirdek en alt user-mode geçidi | Yüksek (wiki/Ntdll arşiv, oldid=48171) |
| NTDLL'nin kaynak aidiyeti | "Ntdll.dll is NOT synced with WINE, it is made by ReactOS" | sınırın sahipliği | Yüksek (wiki/Ntdll arşiv) |
| Wine bağımlılığı | "ReactOS depends on Wine mainly for user mode DLLs"; `kernel32.dll` ve `gdi32.dll` fork edilmek zorunda (Wine sürümü Linux/X'e yönlendirir) | uyumluluk katmanının kaynağı | Yüksek (wiki/WINE arşiv) |
| Wine-hack reddi | Word 2010 registry hack'i: "this hack is not acceptable at all for ReactOS; and developer implemented proper implementation" | uyum ↔ güvenlik çekişmesi, örnek | Yüksek (wiki/WINE arşiv) |
| CSRSS (user-mode uç) | "provides the user mode side of the Win32 subsystem"; kütüphaneler sistem çağrısı yerine "send an inter-process call to the CSRSS process ... without compromising the kernel" | sınırın IPC ucu | Yüksek (wiki/Csrss arşiv, oldid=52188) |
| `win32k.sys` (kernel-mode uç) | "Window manager and GDI services are instead handled by a kernel mode driver (win32k.sys)" | sınırın çekirdek ucu | Yüksek (wiki/Csrss arşiv) |
| Sınır bozulma modu | csrss/winlogon bozulursa NT kernel "shut down the startup process with a BSOD displaying error 0xc000021a" | başarısızlık modu | Yüksek (wiki/Csrss arşiv) |
| Hata-uyumluluğu politikası | Win32'yi "mimic Windows versions ... completely" + "implementing their bugs too" — "unless they introduce security issues" | uyum↔güvenlik kuralı | Yüksek (architecture/ tam metin) |
| Test yüzeyi | "we maintain a vast test suite comprising of millions of tests" | sınır davranışı doğrulaması | Yüksek (architecture/ tam metin) |
| Yüksek-ayrıcalıklı süreçler | winlogon · lsass · smss · SCM: "run with higher privileges than regular applications" — secure coding şart | ayrıcalık sınırı | Yüksek (architecture/ tam metin) |
| Uygulama geliştirme yüzeyi | ReactOS uygulamaları C/C++ ve "use only the base win32 APIs" | uygulamanın gördüğü kapı | Yüksek (architecture/ tam metin) |
| Uyumluluk hedefi + olgunluk | Server 2003 odağı ("currently focused on Windows Server 2003 compatibility") · NT family (NT4, 2000, XP, 2003, Vista, 7) · "Alpha quality operating system" + disk veri kaybı uyarısı | sınırın güvenilirlik sınırı | Yüksek (raw README tam metin) |
| Uygulama sandbox'ı (AppContainer · job kısıtı · capability benzeri 3. parti kısıt) | — | uygulama izolasyonu | **UNKNOWN** (bu kaynaklarda okunmadı) |

**Okuma:** 16 satırın 14'ü doğrudan okunmuş metne bağlıdır, 1'i `UNKNOWN`
kalır. MS Learn'in "private virtual address space + private handle table"
izolasyon modeli §4'te **Windows'un genel tanımı** olarak geçer; ReactOS'un
birebir karşılığı bu kartta **kanıtlanmaz**. `UNKNOWN` satır doldurulmaz.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun **hiçbiri bu repodan alınmamıştır**; hepsi
> ReactOS/NT resmi kaynağından **okunmuş alıntıdır** ve `kaynak: upstream docs`
> ile imzalıdır. Repo'da karşılığı yoktur (§3 negatif kanıt).

### §2.1 — Win32 API katmanı ve hata-uyumluluğu politikası (alıntı, metin)

```text
# kaynak: upstream docs - https://reactos.org/architecture/
# "Win32 API" bolumu (2026-10-14 tam metin):
#   "The Win32 API is lowest layer of user mode and is made up of vast number of
#    dlls. The main dlls can be split into three specific areas; Kernel32.dll
#    contains code to manage areas such as threads, processes, and kernel services
#    such as file interaction. Gdi32.dll contains the drawing code and User32.dll
#    contains code for things such as scroll bar and buttons."
# Sinir-uyum politikasi (ayni sayfa):
#   "it has to mimic Windows versions of the Win32 API completely. This means not
#    only implementing the features so they operate exactly as they do on Windows,
#    but also implementing their bugs too. ... unless they introduce security
#    issues, then we need to make sure our code has these quirks too."
# "ReactOS Applications" bolumu:
#   "All our applications are written in C or C++ and use only the base win32 APIs
#    (and optionally our ATL library which we package into our code base)."
# "System Processes" bolumu:
#   "These system processes run with higher privileges than regular applications,
#    so an understanding of secure coding practices is essential to avoid
#    introducing vulnerabilities."
# Dogrulama yuzeyi: "we maintain a vast test suite comprising of millions of tests"
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/architecture/ · 2026-10-14 HTTP 200 (22.474 bayt) |

### §2.2 — CSRSS IPC sınırı (alıntı, wiki arşiv kopyası)

```text
# kaynak: upstream docs - https://web.archive.org/web/20250817111135/https://reactos.org/wiki/Csrss.exe
# "Client/Server Runtime Subsystem, or csrss.exe ... provides the user mode side of
#  the Win32 subsystem and is included in Windows 2000 and later, while Win32.sys is
#  the kernel-mode portion of the Win32 Subsystem."
# "When a user-mode process calls a function involving console windows, process/thread
#  creation, or side-by-side support, instead of issuing a system call, the Win32
#  libraries (kernel32.dll, user32.dll, gdi32.dll) send an inter-process call to the
#  CSRSS process which does most of the actual work without compromising the kernel."
# "Window manager and GDI services are instead handled by a kernel mode driver
#  (win32k.sys)."
# Basarisizlik modu: "If any of the files is corrupted or otherwise inaccessible,
#  the NT kernel will shut down the startup process with a BSOD displaying error
#  0xc000021a."
# Kaynak yolu (wiki): /reactos/subsystems/win32/csrss
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs, arşiv kopya) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/wiki/Csrss.exe (Wayback 20250817111135) · 2026-10-14 HTTP 200 · oldid=52188 |

### §2.3 — Uyumluluk katmanının kaynak sınırı: Wine vs ReactOS (alıntı)

```text
# kaynak: upstream docs - https://web.archive.org/web/20260722043216/https://reactos.org/wiki/WINE
#           + https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Ntdll.dll
#           + https://raw.githubusercontent.com/reactos/reactos/master/README.md
# Wine (wiki, "Relation to ReactOS"):
#   "ReactOS works with the WINE project to share as much programming effort as
#    possible. ReactOS depends on Wine mainly for user mode DLLs."
#   "kernel32.dll and gdi32.dll have to be forked, since the Wine versions
#    effectively redirect calls to the Linux kernel and X server respectively."
# NTDLL (wiki): "The Native API Architecture is implemented in NTDLL.dll."
#   "Ntdll.dll is NOT synced with WINE, it is made by ReactOS"
# Uyumluluk kapsami (README, birebir parcacik): "(NT4, 2000, XP, 2003, Vista, 7)"
#   "The ReactOS project, although currently focused on Windows Server 2003
#    compatibility, is always keeping an eye toward compatibility with Windows
#    Vista and future Windows NT releases." (birebir)
# Olgunluk (README): "ReactOS is currently an Alpha quality operating system ...
#   Different things may not work well and it can corrupt the data present on your
#   hard disk. It is HIGHLY recommended to test ReactOS on a virtual machine ..."
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs, arşiv + raw README) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | wiki/WINE (20260722043216) · wiki/Ntdll.dll (20250607195257) · raw README · 2026-10-14 HTTP 200 |

## §3 CoreMusic repo durumu

| Yüzey | Beklenen kanıt | Repo durumu | Ölçüm (2026-10-14) | Sonuç |
|---|---|---|---|---|
| NT/ReactOS sınır kodu | `ntdll` · `csrss` · `win32k` · `kernel32` · `gdi32` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| Kullanıcı-parametresi yakalama | `PreviousMode` · `ProbeForRead` · `AppContainer` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| OS sandbox aracı | seccomp · bubblewrap · sandbox-exec · JobObject · setcap · prctl | YOK | 721 kod dosyası (`*.php/js/css/sql`, vendor·node_modules·.git hariç) → **0 isabet** | **YOK (negatif kanıt)** |
| Uygulama katmanı başlık sınırı | `X-Frame-Options "DENY"` | **VAR** | `home.coremusic.net/.htaccess:10` → satır birebir okundu | **POZİTİF — uygulama katmanı, NT sınırı değil** |
| Uygulama katmanı erişim redi | dosya uzantısı red listesi | **VAR** | `home.coremusic.net/.htaccess:18-19` → `<FilesMatch ...>` + `Require all denied` | **POZİTİF — uygulama katmanı, NT sınırı değil** |

**Okuma:** 5 satırın 3'ü negatif (ReactOS/NT sınır yüzeyi bu repoda yoktur),
2'si uygulama katmanı pozitiftir. `.htaccess` satırları **NT sınırının
karşılığı değildir**; "OS sınırı uygulanıyor" diye kullanılamaz.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi -> `isletim-sistemi-kernel`; ReactOS platform geneli -> `isletim-sistemi-turu/reactos`.

- Bu kart **"ReactOS Windows uygulamalarının %X'ini çalıştırır"** demez —
  uyum yüzdesi hiçbir okunmuş kaynakta geçmez → `UNKNOWN`; yalnız README'nin
  yazdığı NT4–7 kapsamı ve Server 2003 odağı taşınır.
- MS Learn'in user mode / kernel mode izolasyon tanımı ("private virtual
  address space and a private handle table" ... "one application can't modify
  another application's data") **Windows'un genel modelidir**; ReactOS'un
  birebir uyguladığı bu kartta **kanıtlanmadı** → eşleme
  `⚠️ VERIFICATION REQUIRED`.
- Wiki iddiaları (Win32 · CSRSS · Wine · NTDLL) **arşiv kopyasından**
  okunmuştur; canlı sayfalar Anubis challenge döndürür — "canlı doğrulandı"
  diye kullanılamaz (§5 durum sütunu).
- `.htaccess` satırları uygulama katmanı savunmasıdır; NT Win32/CSRSS sınırı
  yerine geçmez ve "OS sınırı devrede" diye okunamaz.
- Uygulama sandbox'ı (AppContainer · job kısıtı · capability) bu kaynaklarda
  **okunmadı** → `UNKNOWN`; "yok/eksik" diye de yazılmaz.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://reactos.org/architecture/ | Win32 API "lowest layer of user mode" · hata-uyumluluğu + güvenlik istisnası · milyonlarca test · yüksek-ayrıcalıklı sistem süreçleri · uygulamalar "only the base win32 APIs" | HTTP 200 · tam metin okundu (22.474 bayt) |
| https://raw.githubusercontent.com/reactos/reactos/master/README.md | Server 2003 odağı · NT4–7 kapsam listesi · "Alpha quality operating system" + disk veri kaybı uyarısı | HTTP 200 · tam metin okundu (7.795 bayt) |
| https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Win32 | Win32 = x86 çalıştırma alt sistemi · Kernel32/Gdi32/User32/Win32k.sys · "roughly 40 other libraries" · oldid=35065 | HTTP 200 · tam metin okundu (25.667 bayt) |
| https://web.archive.org/web/20250817111135/https://reactos.org/wiki/Csrss.exe | user mode side of Win32 · IPC "without compromising the kernel" · win32k.sys · BSOD 0xc000021a · oldid=52188 | HTTP 200 · tam metin okundu (30.185 bayt) |
| https://web.archive.org/web/20260722043216/https://reactos.org/wiki/WINE | Wine'a bağımlılık "mainly for user mode DLLs" · kernel32/gdi32 fork zorunluluğu · registry hack ReactOS'a kabul edilmedi | HTTP 200 · tam metin okundu (28.928 bayt) |
| https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Ntdll.dll | Native API NTDLL'de · "NOT synced with WINE, made by ReactOS" · oldid=48171 | HTTP 200 · tam metin okundu (26.473 bayt) |
| https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/user-mode-and-kernel-mode | "Applications operate in user mode" · private VA space + private handle table · uygulama izolasyonu (Windows genel modeli) | HTTP 200 · tam metin okundu (49.497 bayt) |
| https://reactos.org/wiki/Win32 | (canlı havuz URL) | HTTP 200 ama **Anubis 1.19.1 challenge** (4.188 bayt) → içerik **OKUNAMADI** |

> **Dürüstlük notu:** 8 satırın 7'si doğrudan tam metin okumadır (4'ü Wayback
> arşiv kopyası); 1'i challenge nedeniyle okunamayan canlı wiki adresidir.
> Aday 2 MS Learn URL (`.../kernel/capturing-user-parameters` ve
> `.../win32/win32/user-mode-and-kernel-mode`) **HTTP 404** döndü, hiçbir
> iddiada kullanılmadı.

### §5.1 Yeniden üretilebilir ölçüm

```powershell
# 1) canlı wiki = challenge, arsiv = icerik
(Invoke-WebRequest 'https://reactos.org/wiki/Win32' -UseBasicParsing).Content.Length   # 4188 = challenge
(Invoke-WebRequest 'https://web.archive.org/web/20250607195257/https://reactos.org/wiki/Win32' -UseBasicParsing).Content -match 'responsible for executing x86 executables'

# 2) architecture + README alinti dogrulamasi
(Invoke-WebRequest 'https://reactos.org/architecture/' -UseBasicParsing).Content -match 'lowest layer of user mode'
(Invoke-WebRequest 'https://raw.githubusercontent.com/reactos/reactos/master/README.md' -UseBasicParsing).Content -match 'Alpha quality operating system'

# 3) repo sinir-terim taramasi (negatif kanit)
Get-ChildItem '<repo>' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\(vendor|node_modules|\.git)\\' } |
  Select-String -Pattern 'ntdll|csrss|win32k|kernel32|gdi32|PreviousMode|ProbeForRead|AppContainer' -CaseSensitive:$false
# beklenti: 0 isabet (721 dosya)

# 4) disk kaniti (.htaccess L10 / L18-19)
(Get-Content '<repo>\home.coremusic.net\.htaccess' -Encoding UTF8)[9]    # X-Frame-Options "DENY"
(Get-Content '<repo>\home.coremusic.net\.htaccess' -Encoding UTF8)[17,18] # FilesMatch + Require all denied
```

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — bu dosya kapatmaz; mimari sahibi kararı + yeni ADR (`>=088`) gerekir |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — bu dosya kapatmaz; `yetki-ve-erisim-modeli` ile birlikte karar verilir |

**KAPATMA kuralı:** S1/S2 bu kartta **kapatılamaz**; kapatma yetkisi mimari
sahibine aittir ve `validation:` alanına işlenir. Erken kapatma = karar gasbı.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
