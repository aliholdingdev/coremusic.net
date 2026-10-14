---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-windows
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control
  - https://learn.microsoft.com/en-us/windows/win32/secauthz/mandatory-integrity-control
  - https://learn.microsoft.com/en-us/windows/win32/secauthz/access-tokens
  - https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-initializesecuritydescriptor
  - https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-gettokeninformation
  - shared/src/Middleware/PermissionMiddleware.php:31
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
  - "2026-10-14: 5 kaynak HEAD 200 + webfetch icerik okundu; DACL/MIC/AccessToken cagrisi 721 PHP/JS/CSS/SQL dosyasinda 0 isabet; 2 disk kaniti Select-Object -Index (N-1) ile birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# yetki-ve-erisim-modeli — Windows OS Kartı

## §0 ELI15

Bu kart, Windows'un "kim ne yapabilir" sorusunu yanıtladığı yetki modelini
taşır: DACL/SACL ile nesne erişimi, erişim token'ları ile kimlik ve yetki
taşıma, Mandatory Integrity Control (UAC/MIC) ile DACL'den **önce** çalışan
zorunlu katman ve Windows'ta rol/izin denetiminin karşılığı. Kart, bu
mekanizmaların CoreMusic repoda OS düzeyinde **karşılığı olmadığını** kanıtlar;
uygulama katmanındaki rol/izin satırları ise bilinçli olarak "Windows yetki
modeli" diye sunulmaz. Kod blokları `kaynak: upstream docs` imzalıdır.

## §1 Windows güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| DACL (Discretionary ACL) | Nesneye kimin erişebileceğini ACE listesiyle belirler; "DACL'ler erişimi nasıl kontrol eder" upstream'in temel görevidir | Erişim kararının **çekirdeği** | Yüksek |
| SACL (System ACL) | Denetim (audit) kaydını tutar; MIC etiketleri (`SYSTEM_MANDATORY_LABEL_ACE`) de SACL'de saklanır | Denetim + MIC etiketi taşıyıcısı | Yüksek |
| Güvenlik tanımı (security descriptor) | Sahip, birincil grup, DACL, SACL ve kontrol bayraklarını kapsar; `InitializeSecurityDescriptor` onu **boş** (DACL/SACL/sahip yok) başlatır | Nesne güvenliğinin kapsayıcısı | Yüksek |
| Erişim token'ı | Süreç/thread'in güvenlik bağlamı: kullanıcı SID, üyelik grup SID'leri, logon SID, privilege listesi, sahip SID, birincil grup, default DACL, primary/impersonation durumu | **Kimlik + yetki** kaynağı — Windows'ta "rol" burada taşınır | Yüksek |
| Mandatory Integrity Control (MIC) | DACL erişim denetiminden **önce** değerlendirilir; dört seviye: low / medium / high / system; varsayılan maske `SYSTEM_MANDATORY_LABEL_NO_WRITE_UP` | DACL'i tamamlayan **zorunlu** katman | Yüksek |
| MIC seviye dağılımı | Standart kullanıcı **medium**, yükseltilmiş (elevated) **high**; sistem servisleri **system**; etiketsiz nesneler **medium** sayılır; yeni süreç `min(kullanıcı, dosya)` seviyesinde doğar | UAC/yetki yükseltme yüzeyi | Yüksek |
| Zorunlu politika (mandatory policy) | LSA token'ı oluştururken `TOKEN_MANDATORY_POLICY` ile belirler; `NO_WRITE_UP` / `NO_READ_UP` / `NO_EXECUTE_UP` değerleri | Yetki spiralini kapatan kural | Yüksek |
| Windows'ta kimlik/rol denetimi | Windows "rolü" token'taki **grup SID'leri + privilege listesi** olarak taşır; grup üyeliği `CheckTokenMembership` ile sorgulanır | K010 sınırı: kimlik doğrulama/oturum K010'da; burada yalnız OS token mekanizması | Yüksek (mekanizma) / rol eşlemesi `UNKNOWN` |
| C2-level Security | Access Control başlığında referans verilen uyumluluk düzeyi | Denetim/sertifika referansı | Orta (başlık düzeyi — derin içerik okunmadı) |

**Okuma:** 9 satırın tamamı upstream dokümanından okunmuştur; MIC satırı bu
alt-dalın **imzasıdır** çünkü Windows erişim kararını tek başına DACL'e
bırakmaz. Son satırda "rol eşlemesi" kısmı: Windows grup SID'si ≠ CoreMusic
rolü — bu eşleme uygulamada yapılır ve **S2 [UNKNOWN]** alanıdır.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki bloklar bu repodan **alınmamıştır**; hepsi
> `kaynak: upstream docs` imzalı Win32 referans kalıplarıdır. Repo'da
> karşılıkları §3'te negatif kanıt, uygulama katmanı karşılıkları §3'ün
> pozitif satırlarındadır. Her bloğun ilk satırı `# kaynak:` imzasıdır; C
> sözdiziminde bu satır preprocessor direktifi **değildir** — derlemeye
> alınacaksa önce kaldırılır (referans bloklar bu repoda derlenmez).

### §2.1 — Güvenlik tanımı başlatma kalıbı (referans, C)

```c
# kaynak: upstream docs - nf-securitybaseapi-initializesecuritydescriptor
// dwRevision SECURITY_DESCRIPTOR_REVISION olmali; cagri sonunda SACL/DACL/sahip/bag YOKTUR (bos tanim)
#include <windows.h>   // securitybaseapi.h bu dosya icinde gelir (upstream Requirements)

BOOL InitEmptyDescriptor(PSECURITY_DESCRIPTOR pSecurityDescriptor)
{
    return InitializeSecurityDescriptor(pSecurityDescriptor, SECURITY_DESCRIPTOR_REVISION);
}
// Dikkat: bos tanim = hicbir kimse erisemez DEGILDIR; DACL sonradan eklenmeden
// nesne guvenligi eksik kalir. Disaridan gelen tanim kullanilacaksa onay S1'e baglidir.
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`InitializeSecurityDescriptor` / `SECURITY_DESCRIPTOR` 721 kaynak dosyada 0 isabet) |
| Kütüphane | `Advapi32.lib` / `Advapi32.dll` (upstream Requirements) |
| Sınır | Bu blok **nesne güvenliği kurmaz**, yalnızca iskeleti başlatır |
| İlişki | DACL eklemek ayrı bir adımdır (upstream: "Creating a Security Descriptor for a New Object") |

### §2.2 — Token mandatory policy okuma kalıbı (referans, C)

```c
# kaynak: upstream docs - nf-securitybaseapi-gettokeninformation
// TokenMandatoryPolicy: token'in zorunlu politikasini (NO_WRITE_UP vs.) okur
// Token icin TOKEN_QUERY erisim hakki gerekir (upstream: TokenQueryAccess)
BOOL okuMandatoryPolicy(HANDLE hToken)
{
    TOKEN_MANDATORY_POLICY policy;
    DWORD len = 0;
    return GetTokenInformation(
        hToken,
        TokenMandatoryPolicy,   // TokenInformationClass degeri
        &policy,
        sizeof(policy),
        &len);
}
// Uretimde donus degeri kontrol edilmeden yorum YASAKTIR; hata = GetLastError()
```

| Sınıf | R (referans — upstream docs) |
|---|---|
| Repo karşılığı | YOK (`GetTokenInformation` / `TOKEN_MANDATORY_POLICY` 0 isabet) |
| Kaynak | İmza ve `TokenMandatoryPolicy` bağlantısı upstream'te birebir okundu |
| Eşik | Karşılaştırma/güvenlik kararı `hash_equals` ile **bu kartta** yapılmaz (K009) |
| Kullanım | MIC seviyesini **denetlemek** içindir; MIC **atamak** ayrı işlemdir `[UNKNOWN]` |

## §3 CoreMusic repo durumu

| Yüzey | Repo karşılığı | Not |
|---|---|---|
| Windows ACL yapılandırması (SDDL / `Set-Acl` / `icacls`) | → YOK (negatif kanıt, 2026-10-14) | 721 PHP/JS/CSS/SQL dosyasında 0 isabet |
| MIC / integrity etiketi uygulaması | → YOK (negatif kanıt, 2026-10-14) | `Set-ItemProperty ... Integrity` / MIC çağrısı 0 isabet |
| Erişim token'ı sorgulama (`GetTokenInformation`, `CheckTokenMembership`) | → YOK (negatif kanıt, 2026-10-14) | Win32 API çağrısı 0 isabet |
| UAC / yetki yükseltme politikası | → YOK (negatif kanıt, 2026-10-14) | Manifest/manifest politikası dosyası glob 0 |
| Rol denetimi (uygulama katmanı) | `shared/src/Middleware/PermissionMiddleware.php:27-33` → `$userRole` vs `$requiredRole` | **P sınıfı** — ama bu **uygulama** rol denetimidir; Windows token'ı **değil** |
| İzin denetimi (uygulama katmanı) | `shared/src/Middleware/PermissionMiddleware.php:36-40` → `in_array($requiredPermission, ...)` | **P sınıfı** — izin listesi uygulamanın kendisinde |
| Zaman-güvenli karşılaştırma (uygulama katmanı) | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals(...)` | **P sınıfı** — token karşılaştırması; K009 kapsamı |
| Dosya izni analojisi (POSIX DAC) | `shared/src/Session/SessionConfig.php:20,35` → `0750` | **P sınıfı** — POSIX modu **≠** Windows ACL; birebir karşılığı yoktur |

**Dürüstlük notu:** §3'te 4 negatif + 4 pozitif satır vardır. Pozitiflerin
**tamamı uygulama katmanındadır**; Windows DACL/MIC/token düzeyinde tek bir
satır bile yoktur. Bu oran bu alt-dalın S2 bulgusudur.

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }
($src | Select-String -Pattern 'DACL|SACL|GetTokenInformation|InitializeSecurityDescriptor|TokenMandatoryPolicy').Count   # beklenti: 0

$c = Get-Content 'C:\www\coremusic.net\shared\src\Middleware\PermissionMiddleware.php' -Encoding UTF8
$c[30]   # L31 -> role karsilastirma satiri
```

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Windows'un dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Windows platform geneli -> `isletim-sistemi-turu/windows`.

- **Token ≠ uygulama rolü:** Windows'ta "rol" grup SID'leri/privilege iken,
  CoreMusic rolü `PermissionMiddleware`'te uygulama seviyesinde sınanır; ikisi
  aynı katman değildir ve biri diğerinin kanıtı sayılmaz.
- **MIC ≠ UAC arayüzü:** Bu kart MIC'ın **erişim denetimi** davranışını
  (DACL'den önce, `NO_WRITE_UP`) taşır; UAC arayüz/ prompting tasarımı K009 ve
  `isletim-sistemi-turu/windows` kapsamındadır.
- **POSIX `0750` ≠ Windows ACL:** Repo'daki tek dosya izni kanıtı POSIX
  modudur; Windows'a birebir çevrilmiş sayılır (bkz. §3 notu).

## §5 Referans

### §5.1 URL tablosu (HTTP 200 · 2026-10-14)

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control | Erişim denetimi modeli; DACL/SACL/ACE/privilege/audit/securable object başlıkları; C2-level Security | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/secauthz/mandatory-integrity-control | MIC DACL'den önce değerlendirilir; 4 seviye; etiketsiz nesne = medium; `NO_WRITE_UP` varsayılan; `TOKEN_MANDATORY_POLICY` | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/secauthz/access-tokens | Token = güvenlik bağlamı; grup SID'leri, privilege listesi, sahip SID, default DACL, primary/impersonation; grup üyeliği `CheckTokenMembership` | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-initializesecuritydescriptor | Yeni güvenlik tanımı başlatır; SACL/DACL/sahip/grup yok; `SECURITY_DESCRIPTOR_REVISION` zorunlu; `Advapi32.dll` | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |
| https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-gettokeninformation | Token hakkında bilgi getirir; `TOKEN_QUERY` hakkı; `TokenMandatoryPolicy` sorgusu; grup üyeliği için `CheckTokenMembership` | 2026-10-14 · HTTP 200 (HEAD) + webfetch içerik okundu |

### §5.2 Disk kanıtı tablosu

| Kanıt | Dosya | Satır | Doğrulama | Sınıf |
|---|---|---|---|---|
| `$userRole !== $requiredRole` rol denetimi | `shared/src/Middleware/PermissionMiddleware.php` | 31 | idx 30 birebir okundu | P (uygulama) |
| `in_array($requiredPermission, ...)` izin denetimi | `shared/src/Middleware/PermissionMiddleware.php` | 39 | idx 38 birebir okundu | P (uygulama) |
| `hash_equals($sessionToken, ...)` | `shared/src/Middleware/CsrfMiddleware.php` | 42 | idx 41 birebir okundu | P (uygulama) |
| `DIR_PERMISSIONS = 0750` | `shared/src/Session/SessionConfig.php` | 20 | idx 19 birebir okundu | P (POSIX DAC) |

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (ACL/MIC/token denetimi) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — kapatılmaz; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (Windows token'ı mı, uygulama rolü mü hangi kararı verir) | **[UNKNOWN]** — kapatılmaz; §3'teki 4 pozitif satırın tamamı uygulama katmanındadır, eşleme kararı bekleniyor |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
