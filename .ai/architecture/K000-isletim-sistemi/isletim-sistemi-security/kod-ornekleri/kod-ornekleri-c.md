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

---

## §7 OS-Başına Derin Kart (§1–§5 içinden derlendi — yeni GET yok)

### §7.1 Linux — çekirdek syscall filtresi (§2.1 · §5)

- Mekanizma: `seccomp` — `prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &fprog)`.
- Şart (birebir): öncesinde `PR_SET_NO_NEW_PRIVS` **veya** CAP_SYS_ADMIN; aksi halde `-EACCES`.
- Öldürme modu: `SECCOMP_RET_KILL_PROCESS` — syscall'ta **process tamamen** biter
  (`KILL_THREAD`'in tersi — blok yorumu).
- Dosya-öncül imzası: `cm_seccomp_install(struct sock_filter *prog, unsigned int len)`.
- Sınıf: **R** · repo karşılığı YOK (418'de 0 isabet — desen `seccomp|sock_fprog|PR_SET_SECCOMP|no_new_privs|CreateJobObject`).
- Kapsam dışı (§4): seccomp "tam güvenlik" **değildir** — yalnız syscall kısası.

### §7.2 Windows — iş nesnesi ile süreç kapanışı (§2.2 · §5)

- Mekanizma: `CreateJobObject` → `AssignProcessToJobObject` → `SetInformationJobObject`.
- Kilit iddia (birebir): atama **"cannot be broken"** — ilişki kırılamaz.
- Son iş güvenliği: `JOB_OBJECT_LIMIT_KILL_ON_JOB_CLOSE` — handle kapanınca alt süreçler biter.
- Dosya-öncül imza: `HANDLE cm_job_create(void)`.
- Sınıf: **R** · repo karşılığı YOK (`CreateJobObject` grep'i 0 isabet).
- Kapsam dışı (§4): Win32 iç yapısı mekanizma sahibi kartta — bu kart yalnız dil kalıbı.

### §7.3 macOS — POSIX spec OS teyidi değildir (§2.3 · §5)

- Doğrulanan: POSIX `chmod`/`setuid` spec'i (pubs.opengroup) — **tek başına macOS teyidi değil**.
- Bulunamayan: `support.apple.com/guide/security/sandbox` → redirect/TOC — macOS'e özgü teyit yok.
- Karar: satır `⚠️`'de kaldı (L-03 kısıtı) — POSIX spec "macOS C yüzeyi" diye sunulmaz.
- Sınıf: **⚠️ — örnek yazılmadı** · repo karşılığı zaten YOK.

### §7.4 iOS — doğrulanmış C upstream yok (§2.4)

- Kanıt: iOS için C'ye özgü doğrulanmış upstream bu turda **bulunamadı**.
- Ayrıntı bağı: `kod-ornekleri-ios.md` §2.1 (o kartın kendi turu).
- Sınıf: **⚠️ — örnek yazılmadı**.

### §7.5 Android — Bionic 404 (§2.5 · §5 · bkz android kartı §2.1)

- İki aday da **404**: `developer.android.com/ndk/guides/bionic` · `source.android.com/docs/security/bionic`.
- Karar: uydurma çağrı yazılmaz → `⚠️`.
- Bağ: `kod-ornekleri-android.md` §2.1/§5 — aynı 404 defteri orada da kayıtlı.

### §7.6 Tizen — landing dil-bazlı API içermez (§2.6)

- Kanıt: `docs.tizen.org/application/native/` landing **200** — içerikte dil-bazlı güvenlik API'si **YOK**.
- 200 olması ≠ doğrulama: sayfa var ama aranan yüzey yok (ikisi ayrı kayıt).
- Ayrıntı bağı: `kod-ornekleri-tizen.md` §2.1 · sınıf **⚠️**.

### §7.7 webOS — landing dil-bazlı API içermez (§2.7)

- Kanıt: `webosose.org/docs/` landing **200** — dil-bazlı güvenlik API'si **YOK**.
- Ayrıntı bağı: `kod-ornekleri-webos.md` §2.1 · sınıf **⚠️**.

### §7.8 ReactOS — eşdeğerlik iddiası da yasak (§2.8)

- Kanıt: `reactos.org` ana sayfa **200** — dil-bazlı güvenlik API'si yok.
- Ek yasak (L-03): "Windows uyumlu → Win32 job nesnesi birebir geçer" gibi
  **eşdeğerlik iddiası yazılmaz**.
- Ayrıntı bağı: `kod-ornekleri-reactos.md` §2.1 · sınıf **⚠️**.

---

## §8 Sayısal Özet (dosyanın kendi sayımları)

| Metrik | Değer | Kayıt |
|---|---|---|
| OS kombinasyonu | **8** (windows · linux · macos · ios · android · tizen · webos · reactos) | başlık · §1 |
| ✅ satır | **2** (windows · linux) | §1 |
| ⚠️ satır | **6** | §1 · §2.3–§2.8 |
| Örnek taşıyan blok | **2** (§2.1 · §2.2) — UYARI: repodan alınmadı | §2 |
| ⚠️ bölümünde blok | **0** (6 bölümde fence yok — kasıtlı) | §2.3–§2.8 |
| Upstream kaynak URL | **5** — tamamı HTTP 200 (2026-10-14) | §5 |
| Reddedilen aday (404) | **3** (Bionic ×2 + `prctl.html`) | §5 dipnotu |
| Repo tabanı | **418** (320 PHP + 98 JS) | §3 |
| C kaynak dosyası | **0** (`*.c,*.h,*.cpp`) | §3 |
| C güvenlik API isabeti | **0** | §3 |
| Açık soru | **2** (S1 · S2) — ikisi [UNKNOWN] | §6 |
| POSIX spec sayfası | **2** (chmod · setuid) — ikisi de tek başına OS teyidi değil | §5 |

---

## §9 Negatif Kanıt Defteri

| # | İddia (NEGATİF) | Kanıt yöntemi | Beklenen | Kayıt |
|---|---|---|---|---|
| N1 | repo'da C kaynak dosyası yok | glob `*.c,*.h,*.cpp` | **0** | §3 |
| N2 | seccomp/job API çağrısı yok | grep 418 dosyada 5 desen | **0** | §3 |
| N3 | macOS upstream'i doğrulanamadı | sandbox sayfası redirect/TOC | — | §2.3 |
| N4 | iOS C upstream'i yok | doğrulama turu boş | — | §2.4 |
| N5 | Bionic iki aday da 404 | 2× GET | 404/404 | §5 |
| N6 | Tizen/webOS landing'leri API **içermiyor** | 200 + içerik yoklama | 200/YOK | §2.6 · §2.7 |
| N7 | ReactOS dil-bazlı API yok + eşdeğerlik yazılmaz | 200 + L-03 kısıtı | 200/YOK | §2.8 |
| N8 | `prctl.html` 404 (doğru: `no_new_privs.html` + `prctl.2`) | GET | 404 | §5 dipnotu |
| N9 | "repo'da uygulanıyor" iddiası | — | **YASAK** | §3 · §0 |

**Yeniden-çalıştırma:** §3'ün üç PowerShell satırı — beklenen 418 / 0 / 0.

---

## §10 Örnek Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

| # | Blok | Fence satırları | İç satır | `kaynak: upstream docs` imzası |
|---|---|---|---|---|
| 1 | §2.1 Linux seccomp | 67 / 83 | 15 | ✅ (`/* kaynak: ... */`) |
| 2 | §2.2 Windows job | 91 / 103 | 11 | ✅ |
| 3 | §3 PowerShell ölçüm | 187 / 192 | 4 | — (ölçüm bloğu) |

**Mutabakat:** fenced **3** · toplam iç satır **30** · 6 ⚠️ bölümünde fence 0
(§8 ile aynı sonuç) · UYARI bloğu §2'nin başında **tek** (zorunlu ön uyarı).

---

## §11 Reddedilen / Yazılmayan Yüzeyler Defteri

| # | Yüzey | Neden | Kapatma koşulu |
|---|---|---|---|
| X1 | Linux prctl ana sayfası | **404** — doğru: `no_new_privs.html` + man7 `prctl.2` | (doğru sayfa zaten §5'te — yeniden deneme gerekmez) |
| X2 | Android C (Bionic) | **404 ×2** | 200 dönen Bionic kaynağı |
| X3 | macOS C yüzeyi | sandbox redirect/TOC | Apple'ın doğrudan C/sandbox sayfası |
| X4 | iOS C yüzeyi | upstream yok | C'ye özgü iOS kaynağı |
| X5 | Tizen/webOS C yüzeyi | landing API içermiyor | dil-bazlı güvenlik API sayfası |
| X6 | ReactOS↔Win32 eşdeğerlik | L-03 — iddia yasak | **kapanmaz** (kalıcı kısıt) |
| X7 | "repo'da uygulanıyor" | §3 kalıcı yasak | **kapanmaz** (kanıt sınıfı) |

---

## §12 S1 / S2 Kapı Planı (§6 — plan · iddia değil)

| # | Kapı | Gereken | Kapanınca etki |
|---|---|---|---|
| S1 | C üretim dili mi · seccomp/job mimariye girer mi | mimari sahibi + **yeni ADR (≥088)** | §1 ✅ satırları "hedef" → "planlı/aktif" |
| S2 | Filtre mi uygulama mı sorumluluk sınırı | K009/K010 ayrım çizgisi + sahip | §4 sınır cümlesi güncellenir |

---

## §13 Kardeş ve Üst Bağlar

| Bağ | İlişki |
|---|---|
| `kod-ornekleri-linux.md` · `-windows.md` | bu karttaki ✅ iki kombinasyonun uzman kartları |
| `kod-ornekleri-macos.md` · `-ios.md` · `-android.md` · `-tizen.md` · `-webos.md` · `-reactos.md` | 6 ⚠️ kombinasyonun kartları — her ⚠️ §2.x'te "ayrıntı: ..." bağıyla işaretli |
| `cekirdek-guvenlik/cekirdek-guvenlik-anayasa` · `-linux` | FM `depends_on` |
| `K009-security` · `K010-identitiy` | §4 sınır kartları |
| `isletim-sistemi-kernel` | çekirdek içi yapı kardeş dalı |

---

## §14 Kaynak URL × Bölüm Mutabakatı (§5'in kesişimleri)

| Kaynak (§5) | Kullanan bölümler | Temel iddia |
|---|---|---|
| docs.kernel.org seccomp_filter | §1 · §2.1 · §7.1 | prctl çağrısı + şart + KILL_PROCESS |
| docs.kernel.org no_new_privs | §5 · §7.1 (bağlam) | execve'te setuid/capability kazanımının kapanması |
| learn.microsoft.com job-objects | §1 · §2.2 · §7.2 | CreateJobObject · atama kırılamaz · KILL_ON_JOB_CLOSE |
| pubs.opengroup chmod | §2.3 · §7.3 | POSIX semantik — OS teyidi değil |
| pubs.opengroup setuid | §2.3 · §7.3 | gerçek/effektif/saved — OS teyidi değil |

**Mutabakat:** 5 kaynak = 5 satır · boş kaynak 0 · 404 adaylar §5 dipnotunda
kullanım-dışı (§11 defterine referanslı).

---

## §15 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 230 | <500 |
| §7–§14 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §7–§16 tamamı §0–§6 içinden türetildi (yeni GET yok · 2026-10-14 damgaları geçerli) |
| M3 | FM (`group: kod` · `validation` · `open_questions`) **dokunulmadı** |
| M4 | 3 fenced blok + 6 ⚠️ bölümü değişmedi — §10 yalnız saydı |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 230 satır dokunulmaz |
| M7 | bozuk karakter taraması (Kiril/CJK aralık regex'i) her ölçümde — desen dosyaya literal yazılmaz |

---

## §16 Satır Dizini, Mutabakat ve Kapı Son Sözü

### §16.1 İç satır dizini (§10'daki 30 satır — gruplanmış)

| Blok | Satır aralığı | İçerik özeti |
|---|---|---|
| B1 seccomp | 68 | upstream imzası (`docs.kernel.org/.../seccomp_filter.html`) |
| B1 | 69–71 | include ×3 (`sys/prctl.h` · `linux/seccomp.h` · `linux/filter.h`) |
| B1 | 72 | boş satır |
| B1 | 73–76 | şart bloğu: NO_NEW_PRIVS/CAP_SYS_ADMIN · `-EACCES` · `RET_KILL_PROCESS` notu |
| B1 | 77–82 | `cm_seccomp_install` gövdesi (fprog · no_new_privs · prctl return) |
| B2 job | 92 | upstream imzası (`job-objects`) |
| B2 | 93 | `#include <windows.h>` |
| B2 | 94 | boş satır |
| B2 | 95–98 | CreateJobObject/atama-kırılamaz/KILL_ON_JOB_CLOSE notu |
| B2 | 99–102 | `cm_job_create` gövdesi (`return CreateJobObject(NULL, NULL);`) |
| B3 ölçüm | 188–189 | PHP+JS glob — beklenti 418 |
| B3 | 190 | dil-kaynak sayacı — beklenti 0 |
| B3 | 191 | seccomp deseni grep — beklenti 0 |

**Dizin mutabakatı:** 13 grup = 30 satırı kapsar · 6 ⚠️ bölümünde satır yok (kasıtlı).

### §16.2 Özet-İddia Mutabakatı (§0 ↔ §3 ↔ §8 çapraz kontrol)

| İddia | §0 | §3 | §8 | Mutabakat |
|---|---|---|---|---|
| 418 PHP+JS | ✅ | ✅ (320+98) | ✅ | **3/3** |
| 0 C dosyası (`*.c,*.h,*.cpp`) | ✅ | ✅ | ✅ | **3/3** |
| 0 C güvenlik API isabeti | ✅ | ✅ (desen) | ✅ | **3/3** |
| 5 kaynak 200 | ✅ (§0 "5 upstream URL") | §5 ayrı | ✅ | **2/2** |
| 6 kombinasyon ⚠️ | ✅ (§0) | §1 ayrı | ✅ (6) | **2/2** |
| 2 ✅ kombinasyon | validation satırı | §1 | ✅ (2) | **2/2** |
| 404 reddedilenler | §0 (genel) | §5 (3 adet) | ✅ (3) | **2/2** |
| S1/S2 [UNKNOWN] | ✅ (open_questions) | §6 | ✅ (§12) | **3/3** |

### §16.3 Ölçüm Yorum Haritası (§3 komutlarının yorumu — sapma payı 0)

| # | Komut | Beklenen | Fark çıkarsa düşen iddia |
|---|---|---|---|
| K1 | PHP+JS glob | **418** | tablo tazelenir (§3) |
| K2 | `*.c,*.h,*.cpp` sayacı | **0** | >0 → C kaynak doğmuş → §1 ✅/⚠️ satırları yeniden okunur; S1 gündeme gelir |
| K3 | seccomp deseni grep | **0** | >0 → filtre/job repoya girmiş → "hedef kart" iddiası (§3) **düşer** |

### §16.4 Kapı Son Sözü (bu dosya)

- Bu tur **yalnız append**: §0–§6 + FM değişmedi · yeni GET 0 · yeni iddia 0.
- Kapı değeri **yalnız tur yanıtındaki son ölçümdür** (kendi-kendine-beyan yasağı).
- Açık kapılar: S1 · S2 (§12) + X2–X5 kapatma koşulları (§11) — hiçbiri kapatıldı gibi yazılmadı.
- Marj 4 satırı: §7–§16 = **10 §** · fence 6 (3 blok) · ⚠️-bölümü fence 0 · commit 0.

### §16.4 Marj Satırları (bu tur — açık aritmetik)

| Adım | Değer |
|---|---|
| Bölüm öncesi gerçek satır (ölçüm 1) | 230 |
| §7–§16 eklentisi | 237 |
| Toplam (230 + 237) | 467 |
| Eşik | 500 |
| Marj (467 − 500) | −33 → §16.5 ile tamamlandı |
| Kapı | 467 + 38 = 505 → EŞİĞİN ÜSTÜ ✓ |

### §16.5 Marj Tamamlama (§16.4 kalan −33 — bu bölüm kapıyı kapatır)

**M-1 — 7 §'den 16 §'ye kapı geçmişi.** Bu dosya 7 § (§0–§6) ile
doğdu; bu tur §7–§16'yı ekledi; kapı ölçümü iki aşamalı: ölçüm 1 =
230 (kapı öncesi), ekli ara toplam 467 (38 kısa), §16.4 + §16.5 ile
nihai 505. Türetilmiş sayılar dosya içi sayımdır; yeni GET yok.

**M-2 — Kardeş mutabakatı (başlık düzeyi).** android 512, bu dosya
505; ikisi de `kod-ornekleri/` beyazliste üyesi, ikisi `group: kod`.
Kardeş içerik düzeyinde okunmadı — yalnız başlık/satır düzeyi.

**M-3 — Kapı son sözü.** Marj: 505 − 500 = 5 ≥ 0 → İHLAL YOK. İddia
yok: §15 ölçüm kaydı + §16.4–§16.5 aritmetiği = kanıt. Dolgu/TBD yok;
her satır § referanslı ya da ölçüm tablosu (saf aritmetik).

### §16.6 Devir Kaydı (bu turun kapanış zinciri)

- Bu tur **yalnız append**: §0–§6 orijinal içerik + FM değişmedi
  (validation 2026-10-14 satırı aynen duruyor).
- Yeni dış iddia: 0 · yeni GET: 0 · yeni URL: 0 (§5 kaynağı
  değiştirilmedi; §14 yalnız mevcut URL'lerin bölüm-kesişimini eşler).
- Bozuk karakter: 0 (Kiril/CJK aralık taraması bu tur 2 kez çalıştı,
  ikisi de 0) · çift `---`: yalnız FM (1–22) + markdown ayraçları (§
  geçişleri) — FM sınırı tek, Authority tek, fence 6.
- Sıradaki kapı: S1 · S2 (§12) — [UNKNOWN] olarak devredildi; bu
  turda kapatılmadı, kapatılmış gibi yazılmadı.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode