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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Windows'un dosya/surec/erisim kisitlari ele alinir.

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

---

## §7 Çapraz kart matrisi (Windows uygulama sınırı kontrol haritası)

§1 profili, §2.1/§2.2 job kalıpları ve §5.1 URL / §5.2 disk kanıtı bu
dosyada çaprazlanır; her satır bir sınır kontrolüdür. Kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Job nesnesi ile süreç bağlama (kaynak/ömür sınırı) | §2.1 job oluşturma | Kernel job objects | Statik + dinamik | P0 |
| K2 | İş/işlem (süreç) otomatik kapanışı (kill-on-close) | §2.2 iş bağlama | Job lifecycle | Dinamik | P0 |
| K3 | Kullanıcı token/privilege ayrımı | §1 kimlik | Access token | Dinamik | P0 |
| K4 | Dosya ACL / erişim denetimi | §1 depolama | ACL/DACL | Statik + dinamik | P1 |
| K5 | Uygulama kontrolü (imza/WDAC politikası) | §1 yürütme | Code integrity | Statik | P0 |
| K6 | Ağ erişim profili (güvenlik duvarı) | §1 ağ | WFP | Dinamik | P1 |
| K7 | Üretim/indirme kaynağı denetimi (Zone/SmartScreen sınıfı) | §1 doğrulama | Attachment/Reputation | Statik + dinamik | P1 |
| K8 | İşlem grupları/kota (IOCTL sınıfı olmayan basit kota) | §1 kaynak | Job + AppLocker benzeri | Ölçüm | P2 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — C kalıp çıktısı, politika XML'i, ACL dökümü (Dosya: ile).
2. **Dinamik kanıt** — erişim ret (ACCESS_DENIED) / job limit olayı.
3. **Ölçüm kanatı** — §5.1/§5.2 tablolarındaki yeniden üretilebilir satırlar.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K3 | §1 token, §10 S3 |
| Uygulama kartları | K5, K7 | §1, §10 S1/S4 |
| Depolama kartları | K4 | §1 ACL, §10 S5 |
| Bağlantı kartları | K6 | §10 S2 |
| Kaynak kartları | K1, K2, K8 | §2.1/§2.2, §10 S5 |
| Bakım kartları | §5.1/§5.2 | kanıt envanteri |

## §8 Ses/RT yüzeyi (Windows için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek makinede bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (ayrıcalıklı ses süreçleri, paylaşılan tampon, cihaz erişimi)
uygulama sınırının ayrıcalıklı noktasıdır: job bağlama (K1/K2) ile süreç
ömür kontrolü, token (K3) ile ayrıcalık, ACL (K4) ile dosya/tampon erişimi
kesişir. Gecikme/kalite (SNR/THD, WASAPI) bu dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses kanalı/süreci başlatır | Job bağlama (K1/K2) | PLANNED |
| A2 | Paylaşılan tampon üzerinden veri akar | ACL/token denetimi (K3/K4) | PLANNED |
| A3 | Süreç kapanır/çöker | Job kill-on-close temizliği (K2) | PLANNED |
| A4 | Kalan/Artık kanal var mı? | K8 kota/ölçüm | PLANNED |

**Kapsam dışı:** ses algoritması/codec/DSP. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Windows uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Job bağlama/limit ihlali | Limit ret/olay | Süreç durur/kill | §2.1/§2.2 denetimi |
| M2 | Token/privilege reddi | ACCESS_DENIED | İşlem düşer | Token denetimi |
| M3 | ACL ihlali | Erişim ret | Veri erişimi kesilir | ACL denetimi |
| M4 | Süreç çökmesi | EXCEPTION | Job kaynak temizliği | kill-on-close (K2) |
| M5 | Uygulama kontrolü reddi (imza) | Yürütme ret | Uygulama açılmaz | İmzalı/policy uygun |
| M6 | Ağ profili ret | Bağlantı ret | Bağlantı düşer | WFP profili |
| M7 | Kaynak/kota ret | Limit aşımı | Özellik durur | §7 K8 ölçümü |
| M8 | Kaynak/doğrulama ret (Zone/itibar) | Açma ret | Dosya açılmaz | Güvenilir kaynak |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M5:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M3, M6, M8:** tek yüzey.
3. **P2 — M4, M7:** yalnız süreç/kota (M4'te K2 temizliği çalışır).

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Job bağlama (P0)

- **Kural:** Süreç job'a bağlanınca limit/ömür kuralı uygulanır (K1/K2, §9 M1).
- **Yüzey:** Job objects (§2.1/§2.2).
- **Doğrulama:** Test süreci + limit → beklenen: limit olayı/kill.
- **Sonuç:** İhlal K1/K2 → P0. (Durum: PLANNED.)

### §10.2 S2 — Ağ profili (P1)

- **Kural:** Profil dışı bağlantı ret (K6, §9 M6).
- **Yüzey:** WFP (§1).
- **Doğrulama:** Dış hedef → beklenen: ret.
- **Sonuç:** Bağlanırsa K6 ihlali. (Durum: PLANNED.)

### §10.3 S3 — Token/privilege (P0)

- **Kural:** Yetkisiz token ayrıcalıklı işlemi reddeder (K3, §9 M2).
- **Yüzey:** Access token (§1).
- **Doğrulama:** Düşük token ile ayrıcalıklı işlem → beklenen: ACCESS_DENIED.
- **Sonuç:** Geçerse K3 ihlali → P0. (Durum: PLANNED.)

### §10.4 S4 — Uygulama kontrolü (P0)

- **Kural:** Politikaya uymayan yürütme reddedilir (K5, §9 M5).
- **Yüzey:** Code integrity (§1).
- **Doğrulama:** İmzasız test binary'si → beklenen: yürütme ret.
- **Sonuç:** Çalışırsa K5 ihlali → P0. (Durum: PLANNED.)

### §10.5 S5 — Dosya ACL (P1)

- **Kural:** ACL olmayan erişim ret (K4, §9 M3).
- **Yüzey:** ACL/DACL (§1).
- **Doğrulama:** ACL'siz erişim → beklenen: ACCESS_DENIED.
- **Sonuç:** Geçerse K4 ihlali → P1. (Durum: PLANNED.)

### §10.6 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, §2.1/§2.2 C kalıpları, §5.1/§5.2 kanıtlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Microsoft güvenlik dokümanı | K1–K8 | §5.1 URL tablosu üzerinden (2026-10-14) |
| Makine logları | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Job/ACL ölçüm çıktıları | §5.2 disk kanıtı sınıfı | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Makine ölçüm/log (§5.1/§5.2 sınıfı). 2. Dosya içi § referansı. 3. Vault içi
kardeş dosya. 4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-windows.md
|-- Sec0 ELI15
|-- Sec1 Windows guvenlik profili
|-- Sec2 Kod ornekleri (2.1 job | 2.2 is baglama)  [C kaliplari]
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans (5.1 URL | 5.2 disk kaniti)
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
| T1 | §2.1 job oluştur + bağla | Job etkin |
| T2 | İmzalı/policy uygun yürütme (K5) | Çalışır |
| T3 | Yetkili token işlemi (K3) | Çalışır |
| T4 | §5.1/§5.2 kanıt tazeleme | URL/disk satırı güncel |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Job limit aşımı | Limit olay/kill (M1) | K1/K2 ihlali → P0 |
| N2 | Düşük token ile ayrıcalıklı işlem | ACCESS_DENIED (M2) | K3 ihlali → P0 |
| N3 | ACL'siz dosya erişimi | ACCESS_DENIED (M3) | K4 ihlali → P1 |
| N4 | İmzasız yürütme (politika açıkken) | Ret (M5) | K5 ihlali → P0 |
| N5 | Profil dışı bağlantı | Ret (M6) | K6 ihlali → P1 |
| N6 | Şüpheli/kaynağı olmayan dosya açma | Ret (M8) | K7 ihlali → P1 |
| N7 | Kotasız kaynak tüketimi | Limit ret (M7) | K8 ihlali → P2 |

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

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama izin | Sızıntı varsayımı → derhal eskale |
| M4 tekrarı | kill-on-close temizliği denetimi |
| M7 tekrarı | K8 kota limiti revizyonu |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K3, K5 → P1 → K4, K6, K7 → P2 → K8/M4/M7.

### §15 Append-only + kapı özeti (Windows eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Job bağlama etkin mi? | §2.1/§2.2 + §13.2 N1 |
| 2 | kill-on-close temizliği var mı? | §2.2 + §9 M4 |
| 3 | Token/privilege ayrımı doğru mu? | §13.2 N2 |
| 4 | Dosya ACL net mi? | §13.2 N3 |
| 5 | Uygulama kontrolü politikası etkin mi? | §13.2 N4 |
| 6 | Ağ profili tutuyor mu? | §13.2 N5 |
| 7 | Kaynak/doğrulama denetimi açık mı? | §13.2 N6 |
| 8 | Kotası ölçülü mü? | §13.2 N7 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu, eski URL'yi
taze sanmak. Sonraki tur: §8 → makine ölçümü · §10 → dinamik kanıt · §11.2 →
tarihli URL · §13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Uygulama katmanı ayrıntı kontrol listesi (Windows)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir (§7.1).

### §17.1 Job/süreç sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Job kuralları (limit/ömür) | Beklenen limit etkin | §2.1 çıktısı |
| A2 | Sürecin gerçekten job'a bağlı olması | Bağlantı görünür | §2.2 doğrulama |
| A3 | kill-on-close bayrağı | Kapanışta kill | davranış testi |
| A4 | Ayrıcalık seviyesi (token) | En az yetki | token dökümü |
| A5 | Miras/child süreç | Job kapsamı korunur | child ret |
| A6 | IO/ağ/ bellek limitleri | Beklenen limitler | limit olayı |
| A7 | Job dışı kalma denemesi | Kalamaz / ret | ret olayı |
| A8 | Job kapanışında kalan kaynak | Temiz | envanter |

### §17.2 ACL/imza/ağ sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| B1 | Dosya ACL girdileri | En az yetki | ACL dökümü |
| B2 | Dizin devri (inheritance) | Kontrollü devir | ACL dökümü |
| B3 | Uygulama politikası (imza) | Beklenen politika | politika çıktısı |
| B4 | Üretim imzası eşleşmesi | İmza zinciri tam | codesign benzeri |
| B5 | Zone/itibar işareti | Açılışta ret (şüpheli) | ret olayı |
| B6 | Firewall girdileri | Profil dışı ret | WFP çıktısı |
| B7 | Port/kural kapsamı | Beklenen kapsam | kural listesi |
| B8 | Kural çakışması | Çakışma raporlanır | denetim logu |

### §17.3 İzleme adımları (sıra)

1. §17.1 → §17.2 satırlarını sırayla ölç.
2. Her reti §14 şemasına yaz; beklenen ret gelmeyeni işaretle.
3. P0 açık satır için 3 tur sonra üst karar iste.
4. Çıktı yoksa `⚠️ VERIFICATION REQUIRED`.

### §17.4 Kapı ilişkisi + kardeş bağı

| Bağ | Katkı |
|---|---|
| İçerik kapısı | §17 uzunluğuyla ölçülür |
| §6 açık sorular | Etkilenmez (kapatma yasağı) |
| Frontmatter | Değişmez |
| §7 | §17 §7'i ayrıntılandırır |
| §9 / §10 | A/B satırları M/S'ye bağlanabilir |
| §13 / §14 | Sonuçlar negatif tur + olay şemasına akar |
| §5.1 / §5.2 | Kanıt envanteri bu satırları besler |

### §17.5 SSS

**S: §17 satırları PLANNED mı?**
C: Beklenen sütunu şart; gözlenen sütunları kanıt gelene kadar PLANNED.

**S: Sıra?**
C: P0 (A1, A2, A3, A4, A7, B3, B4) önce; B5–B8 son.

**S: Kanıt nereye?**
C: Bu dosyaya eki satır; mevcut satırlar değişmez.

**S: Ne zaman tekrar?**
C: Politika/imza/job değişince + haftalık (§17.6).

### §17.6 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Job kuralları | §17.1 | Job değişiminde |
| ACL/imza | §17.2 | Dosya/paket değişiminde |
| Firewall | §17.2 B6-B8 | Kural değişiminde |
| URL/disk kanıtı (§5.1/§5.2) | Kanıt envanteri | Haftalık tazeleme |
| Olay şeması bütünlüğü | §14 | Olay başına |

### §17.7 Süreklilik

§15 append protokolü + §16 kapı özeti birlikte geçerlidir. Bir sonraki eki
yazacak kişi: §16 → §9.1 → §6 okur, sonra yalnız Authority kuyruğundan sonra
§18 ekler. Ölç-ekle-devam döngüsü sürer; dolgu yasak.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
