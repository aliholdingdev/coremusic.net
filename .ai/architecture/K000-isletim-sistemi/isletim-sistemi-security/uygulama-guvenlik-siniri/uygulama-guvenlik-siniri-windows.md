---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-windows
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects
  - https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-createjobobjecta
  - https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-assignprocesstojobobject
  - https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject
  - https://learn.microsoft.com/en-us/windows/win32/secauthz/appcontainer-isolation
  - https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business
  - https://learn.microsoft.com/en-us/windows/security/threat-protection/windows-defender-application-guard/wd-app-guard-overview
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
  - shared/src/AI/AIEngine.php:292
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
  - "2026-10-14: 7 kaynak HEAD 200 + webfetch icerik okundu; JobObject/AppContainer cagrisi 721 PHP/JS/CSS/SQL dosyasinda 0 isabet; 3 disk kaniti Select-Object -Index (N-1) ile birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# uygulama-guvenlik-siniri — Windows OS Kartı

## §0 ELI15

Bu kart, çalışan bir sürecin **ne kadarının** sınırlanacağını belirleyen
Windows mekanizmalarını taşır: Job Objects ile süreç grubu limitleri
(bellek/öncelik/UI/kapanış davranışı), AppContainer ile en az yetki üzerinden
credential/dosya/ağ/süreç izolasyonu ve App Control (WDAC)'ın hangi kodun
çalışacağını keserek uygulama sınırını ikinci kez çizmesi. Ayrıca hedef
referans olarak PHP/Apache'in çalıştığı sürece bu sınırların ne anlama geldiğini
işler. Repo'da bu mekanizmaların **hiçbiri uygulanmamıştır** (negatif kanıt);
uygulama katmanında sınıra en yakın kanıtlar yol daraltma ve argüman kaçışıdır.

## §1 Windows güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Job Object | Süreç grubunu tek birim olarak yönetir; isimlenebilir, securable, paylaşılabilir; working set, öncelik, uç süre gibi limitler uygular | Çalışan süreç sınırının **ana aracı** | Yüksek |
| `CreateJobObject` | Job oluşturur; `SECURITY_ATTRIBUTES` ile güvenlik tanımı verilir; tanım verilmezse default ACL'ler oluşturucunun primary/impersonation token'ından gelir | Sınırın başlangıcı | Yüksek |
| `AssignProcessToJobObject` | Süreci job'a atar; `hJob` için `JOB_OBJECT_ASSIGN_PROCESS`, `hProcess` için `PROCESS_SET_QUOTA` + `PROCESS_TERMINATE` hakkı gerekir; atamadan sonra süreç job limitlerine tabi | Sınırın **sürece bağlanması** | Yüksek |
| `SetInformationJobObject` | Limit sınıfları: `JobObjectBasicLimitInformation`, `JobObjectBasicUIRestrictions`, `JobObjectCpuRateControlInformation`, `JobObjectExtendedLimitInformation`, `JobObjectEndOfJobTimeInformation` | Limit takımı | Yüksek |
| Breakaway bayrakları | Varsayılan: alt süreçler job'a dahil olur; `JOB_OBJECT_LIMIT_BREAKAWAY_OK` / `JOB_OBJECT_LIMIT_SILENT_BREAKAWAY_OK` ile kaçışa izin verilir | **Kaçış yüzeyinin** denetimi | Yüksek |
| Nested jobs (Windows 8+) | Süreç birden fazla job ağacında olabilir; en kısıtlayıcı limitler devralınır | Katmanlı sınır | Yüksek |
| Job güvenlik limitleri | Vista'dan itibaren güvenlik limitleri **süreç başına** ayrı ayrı ayarlanır; `JOB_OBJECT_SECURITY_NO_ADMIN` / `JOB_OBJECT_SECURITY_ONLY_TOKEN` gibi seçenekler `AssignProcessToJobObject` sonucunu etkiler | Token ile sınır kesişimi | Yüksek |
| AppContainer isolation | İzolasyon birincil hedef: credential (kullanıcı+uygulama birleşimi kimlik, kullanıcı taklidi imkânsız), device (kamera/mikrofon varsayılan kapalı), file/registry, network, process, window izolasyonu; en az yetki | Uygulama **sandbox'ı** | Yüksek |
| App Control (WDAC) sınır etkisi | Hangi kodun çalışacağını politikasıyla keser; audit → enforce; imzalı politika | Uygulama sınırının **ikinci** katmanı (kod seviyesi) | Yüksek |
| Microsoft Defender Application Guard | Güvenilmeyen site/dosyayı Hyper-V kapsayıcısında izole eder | Geçici kapsayıcı sınırı — **kullanım dışı** | Kullanım dışı (deprecated; Windows 11 24H2'ten itibaren yok) |

**Okuma:** 10 satırın 9'u upstream davranışını tarif eder, 1'i (MDAG)
kullanım dışıdır. PHP/Apache için kritik iki satır: **breakaway** (çocuk
süreçlerin sınırdan kaçması) ve **süreç başına güvenlik limiti** — Apache
worker'i farklı süreçlerde çalıştığı için sınır, süreç **ağacına** uygulanır
(hedef referans; uygulanmadı, S1).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki bloklar bu repodan **alınmamıştır**; hepsi
> `kaynak: upstream docs` imzalı Win32 referans kalıplarıdır. Repo'da
> karşılıkları §3'te negatif kanıt olarak ölçülür; kopyalamadan önce S1 gerekir.
> Her bloğun ilk satırı `# kaynak:` imzasıdır; C sözdiziminde bu satır
> preprocessor direktifi **değildir** — derlemeye alınacaksa önce kaldırılır
> (referans bloklar bu repoda derlenmez).

### §2.1 — Job oluşturma kalıbı (referans, C)

```c
# kaynak: upstream docs - nf-winbase-createjobobjecta
// Isimsiz job; SECURITY_ATTRIBUTES NULL ise default guvenlik tanimi kullanilir
// (default ACL'ler olusturucunun primary/impersonation token'indan gelir)
#include <windows.h>

HANDLE hJob = CreateJobObjectA(NULL, NULL);
// Job olustugunda tum limitler inaktiftir; surec atamasi ayri adimdir.
// JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE belirtilirse son handle kapandiginda
// bagli tum surecler sonlanir (upstream Remarks).
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`CreateJobObject`/`JobObject` 721 kaynak dosyada 0 isabet) |
| Kütüphane | `Kernel32.lib` / `Kernel32.dll` (upstream Requirements) |
| Varsayılan | Limitler inaktif, süreç yok, muhasebe sıfır (upstream Remarks) |
| Güvenlik | Tanım `NULL` ise ACL'ler creator token'ından miras — §2.1 tek başına **sınır kurmaz** |

### §2.2 — Süreci job'a bağlama kalıbı (referans, C)

```c
# kaynak: upstream docs - nf-jobapi2-assignprocesstojobobject
// hJob: JOB_OBJECT_ASSIGN_PROCESS; hProcess: PROCESS_SET_QUOTA + PROCESS_TERMINATE
BOOL bagla(HANDLE hJob, HANDLE hProcess)
{
    if (!AssignProcessToJobObject(hJob, hProcess)) {
        return FALSE;   // hata kodu icin GetLastError(); detay yorumu UNKNOWN sayilmaz, log'a yazilir
    }
    return TRUE;        // atama sonrasi surec job limitlerine tabidir (upstream Remarks)
}
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`AssignProcessToJobObject` 0 isabet) |
| Önsart | Süreç zaten bir job'daysa hedef job boş olmalı veya içiçe job hiyerarşisinde olmalı (upstream) |
| Sınır | Atama = **OS katmanı**; PHP tarafındaki yol/argüman denetimleri (§3) bunun yerine geçmez |
| Kullanım sırası | `CreateJobObject` → `SetInformationJobObject` (limit) → `AssignProcessToJobObject` |

## §3 CoreMusic repo durumu

| Yüzey | Repo karşılığı | Not |
|---|---|---|
| Job Object kullanımı (süreç/dosya limiti) | → YOK (negatif kanıt, 2026-10-14) | `JobObject`/`CreateJobObject`/`SetInformationJobObject` 721 kaynak dosyada **0 isabet** |
| AppContainer / sandbox izolasyonu | → YOK (negatif kanıt, 2026-10-14) | `AppContainer` çağrısı 0 isabet; repo'da sandbox etiketli dosya yok |
| WDAC politikası / uygulama kodu sınırı | → YOK (negatif kanıt, 2026-10-14) | `.cip`/`SiPolicy.p7b`/politika XML glob 0 (aynı tarama 2026-10-14) |
| Sürücü/kapsayıcı dosyaları | → YOK (negatif kanıt, 2026-10-14) | `*.sys,*.inf,*.cat,*.vcxproj,*.efi,*.cip,*.p7b,*.wim` glob = 0 |
| PHP/Apache süreç sınırlama (hedef referans) | → YOK (negatif kanıt, 2026-10-14) | Apache'yi job'a bağlama adımı deploy kapsamında değil; **hedef** olarak işaretli, uygulanmadı |
| Yol daraltma (path containment) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189,200` → `realpath` + `str_starts_with($real, $prefix)` | **P sınıfı** — uygulama katmanında **gerçek dosya yolu denetimi**; OS sınırının en yakın karşılığı |
| Argüman kaçışı (shell) | `shared/src/AI/AIEngine.php:292` → `escapeshellarg($filePath)` | **P sınıfı** — komut enjeksiyonuna karşı; OS çağrısı öncesi son uygulama katmanı |
| Uygulama başlığı güvenliği | `home.coremusic.net/.htaccess:10` → `Header always set X-Frame-Options "DENY"` | **P sınıfı** — tarayıcı katmanı; süreç izolasyonu **değil** |

**Okuma:** §3'te 5 negatif + 3 pozitif satır vardır. Pozitifler uygulama
katmanında **sınır niyeti taşır** (yol, argüman, başlık) ama hiçbirisi
süreç izolasyonu değildir — S2'nin en somut kanıtı budur.

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }
($src | Select-String -Pattern 'JobObject|AppContainer|AssignProcessToJobObject').Count   # beklenti: 0

$s = Get-Content 'C:\www\coremusic.net\home.coremusic.net\include\Stream\MusicStreamHandler.php' -Encoding UTF8
$s[188]; $s[199]    # L189 realpath / L200 str_starts_with
```

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Windows'un dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Windows platform geneli -> `isletim-sistemi-turu/windows`.

- **Yol denetimi ≠ süreç izolasyonu:** `realpath` + `str_starts_with` bir
  **uygulama** kararıdır; PHP sürecinin başka sürece erişimini engellemez —
  Job Object/AppContainer bunu OS düzeyinde yapar (§1 vs §3).
- **WDAC ≠ Job Object:** WDAC *hangi kodun başlayacağını*, Job Object
  *başladıktan sonra ne kadarının sınırlanacağını* belirler; ikisi aynı
  katman değildir, biri diğerinin yerine geçmez.
- **MDAG taşınmaz:** Uygulama Guard upstream'te kaldırılmıştır; hedef mimaride
  izolasyon ihtiyacı için "öneri" yapılmaz, durum satırı olarak kalır.

## §5 Referans

### §5.1 URL tablosu (HTTP 200 · 2026-10-14)

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | Job object süreç grubunu birim yönetir; `CreateJobObject`/`AssignProcessToJobObject`/`SetInformationJobObject`; breakaway bayrakları; içiçe job (Win8+); süreç başına güvenlik limiti (Vista+) | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-createjobobjecta | `CreateJobObjectA` imzası; default ACL creator token'ından; `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` davranışı; `Kernel32.dll` | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-assignprocesstojobobject | `AssignProcessToJobObject` imzası; erişim hakları; zorunlu zaman/süreç limiti halleri; `JOB_OBJECT_SECURITY_NO_ADMIN` etkisi | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject | `SetInformationJobObject` imzası; `JOBOBJECTINFOCLASS` limit sınıfları (`BasicLimitInformation`, `BasicUIRestrictions`, `CpuRateControlInformation`, `ExtendedLimitInformation`) | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/secauthz/appcontainer-isolation | AppContainer izolasyonu: credential/device/file/network/process/window; en az yetki; kullanıcı+uygulama birleşimi kimlik | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | App Control politikaları kod çalışmasını sınırlar; audit/enforcement mode; imzalı politika; engelleme listesi politikaları | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/security/threat-protection/windows-defender-application-guard/wd-app-guard-overview | Hyper-V kapsayıcı izolasyonu; **deprecated** — Windows 11 24H2'ten itibaren yok | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |

> **Reddedilen aday (yazılmadı):** `winbase/nf-winbase-assignprocesstojobobjecta`
> → 404 (2026-10-14); doğru kanıt `jobapi2/nf-jobapi2-assignprocesstojobobject`
> üzerinden alındı.

### §5.2 Disk kanıtı tablosu

| Kanıt | Dosya | Satır | Doğrulama | Sınıf |
|---|---|---|---|---|
| `$real = realpath($trimmed);` | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | idx 188 birebir okundu | P (uygulama) |
| `if (!str_starts_with($real, $prefix)) {` | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 200 | idx 199 birebir okundu | P (uygulama) |
| `escapeshellarg($filePath)` | `shared/src/AI/AIEngine.php` | 292 | idx 291 birebir okundu | P (uygulama) |
| `Header always set X-Frame-Options "DENY"` | `home.coremusic.net/.htaccess` | 10 | idx 9 birebir okundu | P (tarayıcı) |

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (Job Object / AppContainer / WDAC) hedef mimaride devreye alınıp alınmayacağı; PHP/Apache sürecinin job'a bağlanıp bağlanmayacağı | **[UNKNOWN]** — kapatılmaz; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (yol/argüman denetimi uygulamada mı, süreç izolasyonu OS'ta mı) | **[UNKNOWN]** — kapatılmaz; §3'teki 3 pozitif satır sınır **niyetini** taşır ama süreç izolasyonu **değildir** |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
