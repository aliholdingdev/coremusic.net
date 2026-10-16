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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

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

---

## §7 Çapraz kart matrisi (ReactOS uygulama sınırı kontrol haritası)

§1 profili, §2 alıntılar (Win32 API katmanı / CSRSS IPC / Wine kaynak sınırı)
ve §5.1 ölçüm bu dosyada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Win32 API katmanı hata-uyumluluğu sınırı | §2.1 API katmanı | Win32 subsystem | Statik + dinamik | P0 |
| K2 | CSRSS IPC sınırı (istemci/sunucu ayrımı) | §2.2 CSRSS | CSRSS/ALPC benzeri | Statik + dinamik | P0 |
| K3 | Uyumluluk katmanı kaynak sınırı (Wine vs ReactOS) | §2.3 kaynak | Üretilen kod + build | Statik | P1 |
| K4 | Token/privilege ayrımı (kullanıcı sınırı) | §1 kimlik | Kernel token | Dinamik | P0 |
| K5 | İş/işlem (job) sınıfı kaynak bağlama | §1 süreç | Job objects | Statik + dinamik | P1 |
| K6 | Dosya ACL/erişim denetimi | §1 depolama | FS ACL | Statik + dinamik | P1 |
| K7 | Ağ erişim profili | §1 ağ | Redirector/firewall | Dinamik | P1 |
| K8 | Servis/oturum izolasyonu | §1 oturum | SCM + Session | Dinamik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — kaynak kod/alıntı satırı, build çıktısı, yapılandırma (Dosya: ile).
2. **Dinamik kanıt** — API ret (ACCESS_DENIED/ERROR_INVALID_PARAMETER sınıfı).
3. **Ölçüm kanatı** — §5.1 yeniden üretilebilir ölçüm çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır. ReactOS olgunluk kısıtı nedeniyle
"bilinen eksik" satırlar da ayrı işaretlenir; örtbas edilmez.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K4 | §1 token, §10 S3 |
| Servis kartları | K2, K8 | §2.2, §10 S4 |
| Depolama kartları | K6 | §1 ACL, §10 S5 |
| Bağlantı kartları | K7 | §10 S2 |
| Uygulama kartları | K1, K3 | §2.1/§2.3, §10 S1 |
| Kaynak kartları | K5 | §5.1 ölçüm, §13 T3 |

## §8 Ses/RT yüzeyi (ReactOS için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek (veya emüle) ortamda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (mixer/cihaz erişimi, sürücü IOCTL yüzeyi, önbellek paylaşımı)
uygulama sınırının ayrıcalıklı geçişidir: IOCTL/driver erişimi K1/K4 ile,
süreç içi paylaşımlar K2/K5 ile kesişir. Windows uyumluluk kısıtları nedeniyle
"var" varsayımı tehlikelidir — bu yüzden tüm satırlar PLANNED kalır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses cihazı IOCTL'u açar | IOCTL erişim denetimi (K1/K4) | PLANNED |
| A2 | Karıştırıcı/ses ayarı yapar | Token privilege denetimi (K4) | PLANNED |
| A3 | Paylaşılan tampon üzerinden veri akar | K2/K5 sınırı ihlal edilmemeli | PLANNED |
| A4 | Uygulama sonlanır | Kanal/job temizlenir (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec/driver geliştirme. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (ReactOS uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | API uyumsuzluk (desteklenmeyen çağrı) | Hata kodu / desteklenmiyor | İşlem düşer | Uyumluluk açıklaması |
| M2 | CSRSS IPC reddi | Sistem servisi hatası | UI/oturum etkilenir | Alt sistem logu |
| M3 | Kaynak sınırı aşımı (Wine kaynak) | Build/çalışma hatası | Derleme/çalışma durur | Kaynak kapsamı |
| M4 | Süreç çökmesi | EXCEPTION | Kaynak sızıntısı riski | Job temizliği |
| M5 | Token/privilege reddi | ACCESS_DENIED | İşlem düşer | Token denetimi |
| M6 | ACL ihlali | Erişim ret | Veri erişimi kesilir | ACL denetimi |
| M7 | Ağ ret/sızma | Bağlantı hatası veya boşluk | Bağlantı etkilenir | Profil denetimi |
| M8 | Servis/oturum karışması | Yanlış oturum davranışı | Oturum ihlali | SCM/oturum logu |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1 (sınır ihlali şeklinde), M2, M5, M6:** fail-closed.
2. **P1 — M7, M8:** tek yüzey.
3. **P2 — M3, M4:** yalnız build/süreç.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.
ReactOS'ta "bilinen eksik" (upstream açık) modları ayrı etiketlenir; çalışır
varsayılmaz (Zero-Hallucination).

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — API sınırı hata-uyumluluğu (P0)

- **Kural:** Desteklenmeyen API çağrısı tanımlı hata döner, sessizce farklı davranmaz (K1, §9 M1).
- **Yüzey:** Win32 katmanı (§2.1).
- **Doğrulama:** Test çağrısı → beklenen hata kodu + log.
- **Sonuç:** Farklı davranış K1 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — Ağ erişim profili (P1)

- **Kural:** Profil dışı bağlantı ret (K7, §9 M7).
- **Yüzey:** redirector/firewall sınıfı (§1).
- **Doğrulama:** Dış hedef bağlantısı → beklenen ret/izleme.
- **Sonuç:** Beklenene aykırıysa K7 ihlali. (Durum: PLANNED.)

### §10.3 S3 — Token/privilege denetimi (P0)

- **Kural:** Yetkisiz token ayrıcalıklı işlemi reddeder (K4, §9 M5).
- **Yüzey:** kernel token (§1).
- **Doğrulama:** Ayrıcalıklı işlem (düşük token) → beklenen: ACCESS_DENIED.
- **Sonuç:** Geçerse K4 ihlali → P0. (Durum: PLANNED.)

### §10.4 S4 — CSRSS IPC sınırı (P0)

- **Kural:** Geçersiz istemci isteği reddedilir (K2, §9 M2).
- **Yüzey:** CSRSS (§2.2).
- **Doğrulama:** Kötü niyetli/geçersiz IPC isteği → beklenen: ret.
- **Sonuç:** Geçerse K2 ihlali → P0. (Durum: PLANNED.)

### §10.5 S5 — Dosya ACL denetimi (P1)

- **Kural:** ACL olmayan erişim ret (K6, §9 M6).
- **Yüzey:** FS ACL (§1).
- **Doğrulama:** ACL'siz erişim → beklenen: ACCESS_DENIED.
- **Sonuç:** Geçerse K6 ihlali → P1. (Durum: PLANNED.)

### §10.6 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |
| Bilinen eksik (upstream) | Varsa zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, §2.1–§2.3 alıntılar, §5.1 ölçüm, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| ReactOS wiki/upstream (§2.2/§2.3 alıntı kaynağı) | K1–K3 | Alıntı satırda arşiv notu korunur |
| Çalışır ortam logları | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| §5.1 ölçüm çıktıları | statik/ölçüm kanıtı | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Ortam ölçüm/log. 2. Dosya içi § referansı/alıntı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-reactos.md
|-- Sec0 ELI15
|-- Sec1 ReactOS uygulama guvenlik siniri profili
|-- Sec2 Kod/alinti ornekleri (2.1 Win32 | 2.2 CSRSS | 2.3 Wine kaynak)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans + 5.1 olcum  (yeniden uretilebilir kanit)
|-- Sec6 Acik sorular          (KAPATMA YASAGI)
|-- Sec7 Cizraz matris         (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED        (sinirli yuzey)
|-- Sec9 Basarisizlik          (M1-M8 + fail-safe)
|-- Sec10 Senaryolar           (S1-S5 kalip)
|-- Sec11 Kaynakca             (guven sirasi)
|-- Sec12 Esleme agaci         (bu satir)
'-- Sec13+ Test/ozet           (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | Desteklenen Win32 çağrısı | Başarılı/uyumlu sonuç |
| T2 | Geçerli IPC oturumu (K2) | Çalışır |
| T3 | §5.1 kaynak ölçümü (K5) | Beklenen değer |
| T4 | Yetkili token işlemi (K4) | Çalışır |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Desteklenmeyen API çağrısı | Tanımlı hata (M1) | K1 ihlali → P0 |
| N2 | Geçersiz IPC isteği | Ret (M2) | K2 ihlali → P0 |
| N3 | Düşük token ile ayrıcalıklı işlem | ACCESS_DENIED (M5) | K4 ihlali → P0 |
| N4 | ACL'siz dosya erişimi | ACCESS_DENIED (M6) | K6 ihlali → P1 |
| N5 | Profil dışı ağ bağlantısı | Ret (M7) | K7 ihlali → P1 |
| N6 | Oturumlar arası yetki denemesi | Ret (M8) | K8 ihlali → P1 |
| N7 | Kaynak sınırı aşımı denemesi | Build/run ret (M3) | K3 ihlali → P2 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P2 |
| bilinen-eksik | upstream açık ise ayrı alan |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama izin | Sızıntı varsayımı → derhal eskale |
| M2/M8 tekrarı | Alt sistem/oturum denetimi |
| M3 tekrarı | Kaynak kapsamı revizyonu |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4 → P1 → K6, K7, K8 → P2 → K3, K5.

### §15 Append-only + kapı özeti (ReactOS eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | API hata-uyumluluğu tanımlı mı? | §2.1 + §13.2 N1 |
| 2 | CSRSS IPC sınırı korunuyor mu? | §2.2 + §13.2 N2 |
| 3 | Wine kaynak sınırı ölçül mü? | §2.3 + §13.1 T3 |
| 4 | Token/privilege denetimi etkin mi? | §13.2 N3 |
| 5 | Job/kaynak bağlama etkin mi? | §7 K5 |
| 6 | Dosya ACL doğru mu? | §13.2 N4 |
| 7 | Ağ profili tutuyor mu? | §13.2 N5 |
| 8 | Servis/oturum izolasyonu var mı? | §13.2 N6 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu, bilinen
eksikleri örtbas etmek. Sonraki tur: §8 → ortam ölçümü · §10 → dinamik kanıt ·
§11.2 → tarihli URL · §13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
