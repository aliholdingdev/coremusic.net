---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-c
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux
evidence:
  - https://docs.kernel.org/userspace-api/seccomp_filter.html
  - https://docs.kernel.org/userspace-api/no_new_privs.html
  - https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/setuid.html
validation:
  - "2026-10-14: 5 kaynak URL GET 200 ile dogrulandi; 8 kombinasyondan 2'si (windows, linux) ornek tasidi, 6'si (macos, ios, android, tizen, webos, reactos) ornek yazmadan VERIFICATION REQUIRED birakildi; repo olcusu 418 PHP+JS / 0 *.c,*.h,*.cpp / grep 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — C (Dil × 8 OS)

## §0 ELI15

Bu kart, **C'nin 8 işletim sistemi kombinasyonunda** (windows, linux, macos, ios, android,
tizen, webos, reactos) OS güvenlik kalıplarını taşır: çekirdek syscall filtresi (`seccomp`),
iş (job) nesnesi ile süreç kapanışı, POSIX izin kalıbı. Hepsi `# kaynak: upstream docs`
imzalı **sözleşmesel** referanstır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 418 PHP+JS; `vendor`/`node_modules`/`.git` hariç)
> **0 `*.c` · 0 `*.h` · 0 `*.cpp`**, 0 `seccomp`/`sock_fprog`/`PR_SET_SECCOMP` isabeti
> (negatif kanıt, §3). Kart **hedef mimari + negatif kanıt** kartıdır; "repo'da uygulanıyor"
> iddiası **YASAKTIR**.

Doğrulama: 5 upstream URL bugün (2026-10-14) GET 200; 6 kombinasyon `⚠️ VERIFICATION REQUIRED`
bırakıldı (§2.3–§2.8), reddedilen adaylar §5'te.

## §1 Dil × OS matrisi (8 OS)

| OS | C güvenlik yüzeyi | Doğrulanmış upstream (2026-10-14) | Durum |
|---|---|---|---|
| windows | Win32 iş (job) nesnesi | learn.microsoft.com job-objects (`CreateJobObject`, `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE`) | ✅ |
| linux | çekirdek `seccomp` filtresi | docs.kernel.org seccomp_filter (`prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, ...)`) | ✅ |
| macos | — | POSIX `chmod`/`setuid` spec'i okundu; macOS'e özgü teyit **yok** (sandbox sayfası redirect/TOC) | ⚠️ VERIFICATION REQUIRED |
| ios | — | iOS'e özgü doğrulanmış C upstream **yok** | ⚠️ VERIFICATION REQUIRED |
| android | — | Bionic upstream'i **404** (reddedildi, §5; bkz `kod-ornekleri-android.md` §2.1) | ⚠️ VERIFICATION REQUIRED |
| tizen | — | docs.tizen.org landing — dil-bazlı güvenlik API'si **içermez** | ⚠️ VERIFICATION REQUIRED |
| webos | — | webosose.org/docs/ landing — dil-bazlı güvenlik API'si **içermez** | ⚠️ VERIFICATION REQUIRED |
| reactos | — | reactos.org ana sayfa — dil-bazlı güvenlik API'si **içermez**; Win32 eşdeğerliği iddia edilmez | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 2 ✅ + 6 ⚠️ (kaynak yok → örnek yazılmaz, uydurma çağrı/ad üretilmez). POSIX spec
sayfaları (pubs.opengroup) tek başına bir OS'un C yüzeyini **teyit etmez** — bu yüzden
macOS satırı `⚠️`'de bırakılmıştır (L-03 kısıtı).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 2 bloğun **hiçbiri bu repodan alınmamıştır**; ikisi de
> `# kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır (çalıştırılabilir
> kod değildir — imza satırı şablon konvansiyonudur). Repo karşılığı §3'te negatif kanıttır;
> kopyalamadan önce S1 kararı gerekir.

### §2.1 — Linux: seccomp syscall filtresi (referans)

```c
/* kaynak: upstream docs - docs.kernel.org/userspace-api/seccomp_filter.html */
#include <sys/prctl.h>
#include <linux/seccomp.h>
#include <linux/filter.h>

/* seccomp_filter.html: filtre kurulmadan ONCE prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0)
   veya CAP_SYS_ADMIN yetenegi gerekir; aksi halde syscall -EACCES dondurur.
   Ayni sayfa: SECCOMP_RET_KILL_PROCESS — yakalanan syscall'ta process tamamen
   sonlandirilir (SECCOMP_RET_KILL_THREAD'in tersine). */
int cm_seccomp_install(struct sock_filter *prog, unsigned int len)
{
    struct sock_fprog fprog = { .len = len, .filter = prog };
    if (prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0) != 0) return -1;
    return prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &fprog);
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `seccomp`/`sock_fprog`/`PR_SET_SECCOMP` grep'i 418 dosyada 0 isabet (2026-10-14) |

### §2.2 — Windows: iş nesnesi ile süreç kapanışı (referans)

```c
/* kaynak: upstream docs - learn.microsoft.com/en-us/windows/win32/procthread/job-objects */
#include <windows.h>

/* CreateJobObject: is nesnesi olusturur; AssignProcessToJobObject ile surec
   atandiktan sonra ILISKI KIRILAMAZ (upstream: association "cannot be broken").
   SetInformationJobObject ile JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE konursa son is
   handle'i kapandiginda alt surecler de sonlandirilir. */
HANDLE cm_job_create(void)
{
    return CreateJobObject(NULL, NULL);
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `*.c` 0 dosya; `CreateJobObject` grep'i 0 isabet (2026-10-14) |

### §2.3 — macOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> POSIX `chmod`/`setuid` spec'i doğrulandı (pubs.opengroup) ama bu fonksiyonların macOS'e
> özgü doğrulanmış upstream teyidi bu turda **bulunamadı** (`support.apple.com/guide/security/
> sandbox` redirect/TOC) → POSIX spec "macOS C yüzeyi" diye sunulmaz, uydurma çağrı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış macOS upstream'i yok) | YOK — repo'da C zaten yok (§3) |

### §2.4 — iOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): iOS için
> C diline özgü doğrulanmış upstream doküman bu turda **bulunamadı** → uydurma çağrı yazılmaz
> (ayrıntı: `kod-ornekleri-ios.md` §2.1).

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.5 — Android: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14): Bionic
> (Android'in libc'si) dökümantasyonu için denenen iki aday da **404** döndü
> (`developer.android.com/ndk/guides/bionic`, `source.android.com/docs/security/bionic`,
> §5) → uydurma çağrı yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.6 — Tizen: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> `docs.tizen.org/application/native/` landing sayfası 200 — dil-bazlı güvenlik API'si
> içerikte **YOK** → uydurma çağrı yazılmaz (ayrıntı: `kod-ornekleri-tizen.md` §2.1).

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.7 — webOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> `webosose.org/docs/` landing sayfası 200 — dil-bazlı güvenlik API'si içerikte **YOK** →
> uydurma çağrı yazılmaz (ayrıntı: `kod-ornekleri-webos.md` §2.1).

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

### §2.8 — ReactOS: ⚠️ VERIFICATION REQUIRED

> **Kombinasyon `⚠️ VERIFICATION REQUIRED` — örnek YAZILMADI.** Kanıt (2026-10-14 okundu):
> `reactos.org` ana sayfa 200 — dil-bazlı güvenlik API'si **yok**; "Windows uyumludur, dolayısıyla
> Win32 iş nesnesi birebir geçer" gibi bir **eşdeğerlik iddiası da yazılmaz** (L-03) → uydurma
> çağrı yazılmaz (ayrıntı: `kod-ornekleri-reactos.md` §2.1).

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (kombinasyon — doğrulanmış upstream yok) | YOK — repo'da C zaten yok (§3) |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (320 PHP +
98 JS)**; aynı ağaçta `*.c,*.h,*.cpp` — **0 dosya**; desen =
`seccomp|sock_fprog|PR_SET_SECCOMP|no_new_privs|CreateJobObject` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| C kaynak dosyası (`*.c`, `*.h`, `*.cpp`) | glob | **YOK — 0 dosya** (negatif kanıt) |
| C güvenlik API çağrısı (seccomp/job) | grep 418 dosyada | **0 isabet** (negatif kanıt) |
| PHP+JS tabanı (ölçüm payandası) | dosya sayımı | **418 dosya** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — kart hedef mimari + negatif kanıttır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.c,*.h,*.cpp).Count  # beklenti: 0
($src | Select-String -Pattern 'seccomp|sock_fprog|PR_SET_SECCOMP|no_new_privs|CreateJobObject').Count  # beklenti: 0
```

**Okuma:** 4 satır negatif kanıt + 1 satır ölçüm payandası; C OS güvenlik yüzeyi bu repoda
**hiç başlamamıştır** — bu, bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA → `K010-identitiy`; bu kartta yalnız C ile OS dosya/süreç/erişim kalıpları (filtre, iş nesnesi, izin) taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto seçimi (ADR-022) → `K009-security`; bu kart yalnız OS katmanı + sınır işaretlerini taşır.
> **Komşu dal:** Çekirdek içi yapısı ve sürücü yazımı → `isletim-sistemi-kernel`; dil bağımsız güvenlik kartları → `cekirdek-guvenlik/*`.

Ek sınır: bu kart **dil kalıbını** taşır — mekanizma sahipliği komşu kartlardır. `seccomp`
filtesi "tam güvenlik" diye adlandırılması abartıdır (yalnız syscall kısasıdır); C'de bellek
güvenliği (overflow/UAF) bu kartın konusu **değildir** (kod güvenliği ≠ OS güvenliği, X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.kernel.org/userspace-api/seccomp_filter.html | `prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &prog)`; öncesi `PR_SET_NO_NEW_PRIVS` veya CAP_SYS_ADMIN şartı, aksi halde `-EACCES`; `SECCOMP_RET_KILL_PROCESS` | HTTP 200 · içerik okundu |
| https://docs.kernel.org/userspace-api/no_new_privs.html | `PR_SET_NO_NEW_PRIVS`; sonraki execve'te setuid/capability kazanımının kapanması | HTTP 200 · içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | `CreateJobObject` · `AssignProcessToJobObject` (atama kırılamaz) · `SetInformationJobObject` · `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` | HTTP 200 · içerik okundu |
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | POSIX `chmod` semantiği (oktal mod) — tek başına OS teyidi **değil** | HTTP 200 · içerik okundu |
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/setuid.html | POSIX `setuid` semantiği (gerçek/effektif/saved) — tek başına OS teyidi **değil** | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `developer.android.com/ndk/guides/bionic` → **404** ·
> `source.android.com/docs/security/bionic` → **404** (2026-10-14; ikisi de Android C
> yüzeyi için denendi, gövdeye alınmadı — bkz `kod-ornekleri-android.md` §5) ·
> `docs.kernel.org/userspace-api/prctl.html` → **404** (doğru sayfa `no_new_privs.html`
> + man7 `prctl.2`, bkz `kod-ornekleri-linux.md` §5).

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | C ile OS güvenlik katmanı (seccomp/job) hedef mimaride devreye alınıp alınmayacağı; C'nin üretim dili olup olacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (filtre mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode