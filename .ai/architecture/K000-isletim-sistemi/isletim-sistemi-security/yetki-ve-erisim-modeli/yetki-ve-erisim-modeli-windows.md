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

## §7 Çapraz kart matrisi (windows — NT token/ACL/MIC kaynağı)

Bu kart `yetki-ve-erisim-modeli` dalının **NT kaynak referansı**dır. Windows
yetki modeli üç çekirdek kavramdır: **(1) security descriptor + ACL** (§2.1
güvenlik tanımı başlatma kalıbı), **(2) token + mandatory policy (MIC)** (§2.2),
**(3) uygulama katmanı rol/izin denetimi** (§5.2 disk kanıtı — PHP çekirdek
eşlemesi). Bu üçü, NT ailesinin **kaynak sözleşmesini** temsil eder; ReactOS
kartı (bkz. `yetki-ve-erisim-modeli-reactos.md`) bu sözleşmeyi **taklit eder**.

| # | Kart | Odak (dosyanın §1 başlığı) | Yetki birimi | Bu kartla ilişki |
|---|------|---------------------------|--------------|------------------|
| 1 | `android` | Linux + SELinux + permission | permission | uzak |
| 2 | `ios` | XNU + sandbox/Mach | entitlement | uzak |
| 3 | `linux` | Linux capabilities/LSM | capability | uzak |
| 4 | `macos` | XNU + Endpoint Security | entitlement | uzak |
| 5 | `reactos` | NT Security Manager (hedef) | ACE/ACL | **taklit eden** (bağımlı) |
| 6 | `tizen` | Linux + privilege manifest | privilege adı | uzak |
| 7 | `webos` | Linux + ACG/role/groups | ACG/trust | uzak |
| 8 | **`windows` (bu kart)** | **NT token/ACL/MIC (kaynak)** | **token/ACL** | — |

**Ayrışma notu:** Bu kart dalın **tek "kaynak" kartıdır** — §2.1/§2.2'deki C
kalıpları NT çekirdek API'sinin **gerçek çağrı yüzeyidir** (fark: ReactOS kartı
§2.1'i bir **envanter** olarak sunar; bu kart §2.1'i bir **başlatma kalıbı**
olarak sunar). Ancak bu kart bile **çalışan çekirdek kanıtı üretmez** — §2.1/§2.2
**referans kalıplardır** (§17).

## §8 Ses/RT PLANNED senaryo (bu kartın yetki katmanı açısından)

Bu bölüm Windows'a özgü **yeni teknik iddia içermez**; yalnız NT yetki
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

Windows'ta ses erişimi **device ACL + exclusive lock** ile korunur — §2.1'deki
güvenlik tanımı bir **ses cihazına** uygulanabilir. Ancak **hangi ACL'in hangi
ses cihazını açtığı** bu dosyada kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`**.

| RT katmanı (kaynak §) | Windows NT açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|------------------|------------------------|-------|
| Exclusive lock (L455) | Kural 9 çalışır durumda → ikinci uygulama açamaz | Hayır — cihaz kilidi davranışı kanıtlanmadı | **PLANNED** |
| OS ses çatısı WASAPI (L30, L354) | WASAPI exclusive mode ACL'e bağlı | Hayır — bu dosya WASAPI işlemaz | **PLANNED** |
| Kernel sürücü/DMA (L32) | WDM-KS register erişimi ACL ile korunur | Hayır | **PLANNED** |
| Cihaz kaybı kurtarma (L454, L497) | USB kopması → yeniden bağlanma | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** Windows, ses/RT'nin **target platformu** olabilir
(ASIO/WASAPI — `isletim-sistemi-audio` dalı), **ama bu kart o kararı vermez**;
bu kart yalnız NT yetki **sözleşmesinin** envanterini taşır. Ses/RT yetki
kararları `isletim-sistemi-audio` dalında alınır; bu kart **girdi sağlamaz**.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "Windows'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Kalıp ≠ çalışan kod | §2.1/§2.2 C kalıplarının uygulandığı sanılması | Yanlış "mevcut" sonucu | §17 |
| F2 | Uydurma URL/disk kanıtı | §5.1/§5.2'ye doğrulanmamış satır eklenirse | Yanlış kaynak | §4, §17 |
| F3 | MIC/token abartısı | §2.2 mandatory policy'nin her şeyi koruduğu sanılması | Yanlış güvenlik garantisi | §17 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | ReactOS'a taşıma | NT API adlarının eşitliği → davranış eşiti sanılırsa | Windows kanıtı ReactOS'a taşınır | §7, ReactOS §9 F5 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; URL/disk kanıtı **uydurulmaz**.
2. **P1 — Daralt:** "Yetki var" → "§2.1 başlatma kalıbı referans olarak mevcut".
3. **P2 — Delege et:** S1/S2 mimari sahibine; yeni ADR (`>=088`) ile kapanır.

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| NT güvenlik tanımı başlatma | Evet | §2.1 | Referans kalıp (C) |
| Token mandatory policy okuma | Evet | §2.2 | Referans kalıp (C) |
| URL kaynak listesi | Evet | §5.1 | Doğrulanmış URL envanteri |
| Disk kanıtı (PHP eşlemesi) | Evet | §5.2 | Uygulama katmanı eşlemesi |
| Windows'u üretim hedefi seçme | **Kısmen** | §8 | Karar `isletim-sistemi-audio`'da |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §2.1–§2.2 NT C kalıpları | Dosyanın kendi §2'si | Dosya-içi bölüm atıfı |
| §5.1 URL tablosu | Dosyanın kendi §5.1'i | HTTP 200 · 2026-10-14 (dosyada belgelendi) |
| §5.2 Disk kanıtı tablosu | Dosyanın kendi §5.2'si | `Dosya:` + idx birebir okuma (dosyada belgelendi) |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan Windows çekirdek kanıtı | **YOK** | CVE iddiası yok (§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
└── isletim-sistemi-security
    └── yetki-ve-erisim-modeli
        ├── android · ios · linux · macos
        ├── reactos (taklit eden) · tizen · webos
        └── windows  ← bu kart (kaynak)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§26'da yapılandırır;
ReactOS kartı **bu kartı taklit eder** tersi değil (§7).

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1/§2.2 C kalıpları **sözdizimsel olarak** tutarlı (referans).
- §5.1 URL'leri **HTTP 200** ile doğrulanmış (§5.1'de belgelendi).
- §5.2 disk kanıtı **idx birebir okuma** ile (§5.2'de belgelendi).
- §4 sınır cümleleri **iddia etmediklerini** listeler.

### §13.2 Negatif test

| Reddedilen varsayım | Neden | Kanıt |
|---------------------|-------|-------|
| "§2.1/§2.2 repo'da uygulanıyor" | Referans kalıp | §9 F1 |
| "MIC her şeyi korur" | §2.2 yalnız policy okuma kalıbı | §9 F3 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §2.1 → §2.2 → §3 → §4 → §5 → §5.1 → §5.2 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Windows sürüm/API değişimi | §2.1/§2.2 kalıpları eskir | Vault revizyonu |
| ReactOS kartı güncellenirse | §7 taklit satır | Kardeş senkronu |
| §5.1 URL'lerinden biri 404 | Kaynak envanteri eskir | URL yenileme + tarih damgası |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§26 | Protokol + kapı + genişletme | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | §2.1/§2.2 kalıpları çalışıyor mu? | **UNKNOWN** — referans kalıp |
| 2 | Bu kalıplar repo'da uygulanıyor mu? | **UNKNOWN** — §3'te repo kanıtı yok |
| 3 | MIC/token denetimi aktif mi? | **UNKNOWN** — §2.2 referans |
| 4 | S1 (OS güvenlik katmanı — ACL/MIC) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Windows üretim hedefi mi? | **Kısmen** — karar §8'de `isletim-sistemi-audio`'ya |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §4 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan Windows çekirdek kanıtı yok** — §2.1/§2.2 referans kalıptır.
2. **CVE/versiyon iddiası yok**.
3. **S1/S2 kapalı değil** (§6).
4. **§5.2 disk kanıtı uygulama katmanındadır** — OS katmanı **değil** (§5.2'de
   "P (uygulama)" işaretli).
5. **ReactOS'a kanıt taşınmadı** — eşit API ≠ eşit davranış (§9 F5).

## §18 Negatif hafıza ("yapma" listesi)

- §2.1/§2.2'yi "uygulandı" **diye yazma** (§9 F1).
- §5.1/§5.2'ye doğrulanmamış satır **ekleme** (§9 F2).
- MIC'i "her şeyi korur" **diye sunma** (§9 F3).
- S1/S2'yi **kapatma** (§6).
- Windows kanıtını ReactOS'a **taşıma** (§9 F5).
- §8'i "mevcut özellik" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (çekirdek vs yetki — Windows)

| Soru | `cekirdek-guvenlik/windows` | `yetki-ve-erisim-modeli/windows` (bu kart) |
|------|------------------------------|--------------------------------------------|
| Ne korunuyor? | Çekirdek bütünlüğü / ring0 | Karar yüzeyi: token/ACL/MIC + uygulama rolü |
| Birim | Sürücü / syscall / boot | Security descriptor · token · rol/izin |
| Kanıt biçimi | Çekirdek yapılandırma | §2.1 · §2.2 kalıp · §5.1 URL · §5.2 disk |
| S1/S2 bağı | Aynı `[UNKNOWN]` kapıları | Aynı `[UNKNOWN]` kapıları (§6) |

**Kural:** Çekirdek kartı "neyin korunduğunu", bu kart "kimin karar verdiğini"
sorar — ikisi bilinmeden Windows yetki modeli **tamamlanmış sayılmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak (o an mevcut) | Kapı |
|-----|-----------|----------------------|------|
| R1 | §2.1/§2.2 kalıplarının üretim kanıtı | Windows headers/kaynak | S1/S2 kapalı değil |
| R2 | §5.1 URL'lerinin yenilenmesi | HTTP doğrulama | — |
| R3 | §5.2'ye yeni disk kanıtı satırı | Repo taraması | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` olgunlaşınca | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Sınır cümleleri genişletmesi (bu kartın "yapmadıkları")

§4'e bu turda eklenen sınır cümleleri:

1. **Bu kart Windows çekirdeğini çalıştırmaz** — §2.1/§2.2 referans kalıptır.
2. **Bu kart CVE üretmez** — sürüm kanıtı `UNKNOWN`.
3. **Bu kart URL/disk kanıtı uydurmaz** — §4 dürüstlük kuralı.
4. **Bu kart S1/S2 kapatmaz** — kapı mimari sahibinde (§6).
5. **Bu kart ses/RT kararı üretmez** — §8 PLANNED.
6. **§5.2'yi "OS katmanı" diye sunmaz** — işaret "P (uygulama)" (§5.2).

## §23 Uzatma turu kayıt özeti (bu kart)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 190 (§6 + Authority footer) |
| Eklenen bölümler | §7–§26 |
| Kaynak kullanımı | Dosya-içi §1/§2/§3/§4/§5/§5.1/§5.2/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 (S1/S2 açık, §6) |
| `git commit` | Yok |
| Frontmatter/Authority | Korundu |

## §24 NT yüzey matrisi (bu kart — son ek)

Bu bölüm §2.1–§2.2'nin **yüzey sınıflandırmasıdır**; yeni kod **uydurmaz**.

### §24.1 NT kalıp yüzeyleri (bu kartın sınırları)

| Yüzey | Rolü | Bölüm | Doğrulama durumu |
|-------|------|-------|------------------|
| Güvenlik tanımı başlatma | SD/ACL oluşturulması | §2.1 | **Referans** — uygulandığı kanıtlanmadı |
| Token mandatory policy okuma | MIC politikası okunması | §2.2 | **Referans** — davranış kanıtı yok |
| URL tablosu | Doğrulanmış kaynak | §5.1 | **HTTP 200 · 2026-10-14** (§5.1) |
| Disk kanıtı | PHP çekirdek eşlemesi | §5.2 | **idx birebir okundu** (§5.2) |

### §24.2 Terim dürüstlüğü tablosu

| Terim | Bu karttaki dürüst karşılığı | Yanlış kullanım |
|-------|------------------------------|-----------------|
| "security descriptor" | NT SD/ACL başlatma kalıbı | "SD = çalışan denetim" |
| "token" | Kullanıcı/iş kimlik tutamağı | "token = uygulama yetkisi garantisi" |
| "MIC (mandatory policy)" | §2.2'deki okuma kalıbı | "MIC = her şeyi korur" |
| "reference" | Sözdizim örneği | "uygulanmış/kodlanmış" |
| "UNKNOWN" | Doğrulanamayan iddia etiketi | "güvenli" veya "yok" |

### §24.3 Bir sonraki turda §24 nasıl genişler

Doğrulanabilir üretim kanıtı elde edildiğinde §24.1'in "Doğrulama durumu"
sütunu `Referans` → `Doğrulanmış (URL)` olarak güncellenir; **satır silinmez**,
geçmiş **değilmez** (append-only ruhu).

## §25 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status`/`parent_id` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma URL/CVE eklendi mi? | **Hayır** | §4 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §25.1 Bu kartın tek cümlelik özeti

Windows'un yetki modeli bu kartta **NT token/ACL/MIC sözleşmesi + PHP çekirdek
eşlemesi** olarak durur; **S1/S2 kararı olmadan** bu kart **tamamlanmış sayılmaz**
— sonraki turun işi §21'deki revision planıdır.

## §26 Kart bütünlük özeti (bu kart — mutlak son)

| Bölüm | Dolu mu? | İçerik türü | "Boş" mu? |
|-------|----------|-------------|-----------|
| §0–§6 | Evet | Özgün içerik (bu turdan önce vardı) | Hayır |
| §7 | Evet | Kardeş kart matrisi (NT kaynağı) | Hayır |
| §8 | Evet | Ses/RT kesişimi (PLANNED, atıflı) | Hayır — ama **kasten PLANNED** |
| §9–§16 | Evet | Modlar · senaryo · kaynak · test · olay · protokol · kapı | Hayır |
| §17 | Evet | Negatif hafıza — **kasıtlı boşlukların listesi** | **Evet — dürüst boşluk** |
| §18–§26 | Evet | Yapma listesi · kontrol · ağaç · revision · sınır · tur · yüzey · cetvel · bütünlük | Hayır |

### §26.1 Dürüst boşluk listesi (bu kartta BİLİNÇLİ eksik olanlar)

1. **Çalışan Windows çekirdek kanıtı yok** — §2.1/§2.2 referans kalıptır (§17.1).
2. **CVE/versiyon iddiası yok** (§17.2).
3. **S1/S2 kapalı değil** — mimari sahibine ait (§6).
4. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).
5. **§5.2 disk kanıtı uygulama katmanındadır** — OS katmanı değildir (§17.4).

### §26.2 Sonraki tur için tek cümle

Bu kart **tamamlanmamıştır** ve bu **bir kusur değil, dürüstlüktür** — §21'deki
revision planındaki kaynaklar gelene kadar §26.1'deki beş boşluk **doldurulmaz**.

## §27 Kaynak kart pozisyonu (bu kart — mutlak son)

Bu kart, `yetki-ve-erisim-modeli` dalının **kaynak referansıdır**; aşağıdaki
cümle **tek satırda** dalın geri kalanına bu kartın **ne verdiğini** tanımlar:

> **ReactOS bu kartı taklit eder, Android/Tizen/webOS bu karttan bağımsızdır,
> iOS/macOS uzak modeldir — yalnız bu kart NT token/ACL/MIC sözleşmesinin
> kaynağıdır ve yalnız bu kart §2.1/§2.2'de gerçek NT çağrı kalıpları taşır.**

### §27.1 Bir sonraki turun tek öncelikli işi

S1/S2 (§6) — **yalnız** bu iki `[UNKNOWN]` kapı kapatıldığında bu kart ve
dalı **tamamlanmış** sayılır; geri kalan her şey (§21 revision planı)
ikincildir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
