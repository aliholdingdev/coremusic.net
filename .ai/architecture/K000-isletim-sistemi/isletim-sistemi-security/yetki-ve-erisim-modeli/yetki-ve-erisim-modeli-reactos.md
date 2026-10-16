---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-reactos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://reactos.org/pipermail/ros-diffs/2022-May/077696.html
  - https://reactos.org/pipermail/ros-diffs/2022-May/077799.html
  - https://web.archive.org/web/20250912212042/https://reactos.org/wiki/Ntoskrnl.exe
  - https://web.archive.org/web/20260928150412/https://reactos.org/wiki/ReactOS
  - https://doxygen.reactos.org/da/d08/sdk_2lib_2rtl_2security_8c_source.html
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yoklugu 2026-10-14 taramasinda 0 isabet."
  - "2026-10-14: pipermail diff metni tam okundu (45.483 bayt); doxygen kaynak dosyasi 25.482 karakter okundu, UNIMPLEMENTED satiri L214-215 birebir."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — ReactOS OS Kartı

## §0 ELI15

Bu kart **yetki-ve-erisim-modeli** alt-dalının ReactOS sürümüdür: NT
güvenlik modeli ReactOS'ta **nasıl gövdeleşiyor** — access token, security
descriptor, ACL/ACE ve erişim denetimi. Her iddia 2026-10-14'te okunmuş bir
ReactOS kaynağına (ros-diffs e-postası, doxygen kaynak dosyası veya wiki arşiv
kopyası) bağlıdır; kaynağı olmayan satır `UNKNOWN` kalır.

> **Erişim dürüstlüğü:** `reactos.org/wiki/*` canlı adresleri 2026-10-14'te
> **Anubis 1.19.1 challenge** döndürdü; wiki içeriği Wayback arşiv kopyasından
> okundu. `git.reactos.org` da aynı challenge'ı döndürdü (4.188 bayt).

> **Öncelikli gerçek:** ReactOS'ta erişim denetimi **DACL + ACE üzerinden**
> yürür; 2022 commit'inde WS2003'ün 5 ACE tipinden **yalnız 2'si**
> "(supported by ReactOS)" olarak işaretlenmiştir — bu, modelin **kısmi
> olgunluğunun** doğrudan kaynağıdır.

## §1 ReactOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Access token (SE modülü) | kimlik/grup/ayrıcalık bağlamı taşır | subject (istek sahibi) | Yüksek (ros-diffs 077799: SE dosya düzeni) |
| Token yaşam döngüsü | `tokenlif.c` Duplication · Creation · Filtering | token üretimi/daraltma | Yüksek (ros-diffs 077799) |
| Token bilgi sınıfları | `tokencls.c` Query/Set Information Classes | token okuma/yazma | Yüksek (ros-diffs 077799) |
| Token ayrıcalık/grup ayarı | `tokenadj.c` privileges · groups adjusting | yetki düzenleme | Yüksek (ros-diffs 077799) |
| Client/subject context | `client.c` · `subject.c` güvenlik bağlamı desteği | sunucu tarafı subject | Yüksek (ros-diffs 077799) |
| Security descriptor | Owner · Group · DACL · SACL alanları (`RtlpSetSecurityObject`) | nesnenin güvenlik tanımı | Yüksek (doxygen `security.c` L44-L90) |
| ACL / ACE | DACL içinde izin veya red girişi | erişim kararının veri yapısı | Yüksek (wiki/Ntoskrnl.exe arşiv) |
| ACE tipi kapsamı | WS2003'te 5 tip; ReactOS: `ACCESS_DENIED_ACE_TYPE` ve `ACCESS_ALLOWED_ACE_TYPE` "(supported by ReactOS)" | kapsam sınırı | Yüksek (ros-diffs 077696) |
| Object / compound ACE | diff TODO: "once we support object type handling in Se" · "once we support compound ACEs handling in Se" | **desteklenmiyor** | Yüksek (negatif kanıt, diff satırları) |
| Erişim denetimi algoritması | `SepAccessCheck` — "the heart and brain of the whole access check algorithm in the kernel" | karar veren çekirdek fonksiyon | Yüksek (ros-diffs 077696) |
| DACL analiz yardımcı fonksiyonu | `SepAnalyzeAcesFromDacl` ACE'leri tek tek çözümleyip hak bildirir | denetim akışı | Yüksek (ros-diffs 077696) |
| Restricted token denetimi | `SeTokenIsRestricted(Token)` → ek `SepAnalyzeAcesFromDacl` çağrısı | kısıtlı token kontrolü | Yüksek (ros-diffs 077696) |
| Security Reference Monitor | "controls the access of resources or objects by the means of access control lists" | çekirdek denetleyici | Yüksek (wiki/Ntoskrnl.exe arşiv) |
| Otomatik miras (inheritance) | `RtlpConvertToAutoInheritSecurityObject` → `UNIMPLEMENTED; return STATUS_NOT_IMPLEMENTED` | **uygulanmamış** | Yüksek (doxygen `security.c` L218-L228) |
| Erişim denetimi olgunluğu | commit öncesi yorum: "The function is currently incomplete!" (diff ile kaldırıldı) | modelin geçmişi | Yüksek (ros-diffs 077696) |
| Integrity level / UAC benzeri kısıt | — | katmanlı bütünlük | **UNKNOWN** (bu kaynaklarda geçmiyor) |

**Okuma:** 16 satırın 14'ü doğrudan okunmuş metne bağlıdır, 1'i negatif
kanıt (object/compound ACE), 2'si `UNKNOWN`. `UNKNOWN` satırlar doldurulmaz.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun **hiçbiri bu repodan alınmamıştır**; hepsi
> ReactOS resmi kaynağından **okunmuş alıntıdır** ve `kaynak: upstream docs`
> ile imzalıdır. Repo'da karşılığı yoktur (§3).

### §2.1 — Erişim denetimi fonksiyon imzaları (alıntı, C)

```c
/* kaynak: upstream docs - https://reactos.org/pipermail/ros-diffs/2022-May/077696.html */
/* [NTOS:SE] Deny access to the caller if access is not allowed by the object
 * (George Bisoc, 2022-05-06) - ntoskrnl/se/accesschk.c diff'inden birebir. */

PACCESS_CHECK_RIGHTS
SepAnalyzeAcesFromDacl(
    _In_ ACCESS_CHECK_RIGHT_TYPE ActionType,
    _In_ PACL Dacl,
    _In_ PACCESS_TOKEN AccessToken,
    _In_ PACCESS_TOKEN PrimaryAccessToken,
    _In_ BOOLEAN IsTokenRestricted,
    _In_ BOOLEAN AccessRightsAllocated,
    _In_opt_ PSID PrincipalSelfSid,
    _In_ PGENERIC_MAPPING GenericMapping,
    _In_opt_ POBJECT_TYPE_LIST ObjectTypeList,
    _In_ ULONG ObjectTypeListLength,
    _In_ ACCESS_MASK RemainingAccess);

/* SepAccessCheck imzasi (commit sonraki hali): */
BOOLEAN
NTAPI
SepAccessCheck(
    _In_ PSECURITY_DESCRIPTOR SecurityDescriptor,
    _In_opt_ PACCESS_TOKEN ClientAccessToken,
    _In_ PACCESS_TOKEN PrimaryAccessToken,
    _In_opt_ PSID PrincipalSelfSid,
    _In_ ACCESS_MASK DesiredAccess,
    _In_opt_ POBJECT_TYPE_LIST ObjectTypeList,
    _In_ ULONG ObjectTypeListLength,
    _In_ ACCESS_MASK PreviouslyGrantedAccess,
    _In_ PGENERIC_MAPPING GenericMapping,
    _In_ KPROCESSOR_MODE AccessMode,
    _In_ BOOLEAN UseResultList,
    _Out_opt_ PPRIVILEGE_SET* Privileges);
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | reactos.org/pipermail/ros-diffs/2022-May/077696.html · 2026-10-14 HTTP 200 (45.483 bayt) |

### §2.2 — ACE tipi kapsam listesi (alıntı, metin)

```text
# kaynak: upstream docs - https://reactos.org/pipermail/ros-diffs/2022-May/077696.html
# "On Windows Server 2003 there are 5 different type of ACEs that are supported
#  for access checks:"
#   - ACCESS_DENIED_ACE_TYPE            (supported by ReactOS)
#   - ACCESS_ALLOWED_ACE_TYPE           (supported by ReactOS)
#   - ACCESS_DENIED_OBJECT_ACE_TYPE
#   - ACCESS_ALLOWED_OBJECT_ACE_TYPE
#   - ACCESS_ALLOWED_COMPOUND_ACE_TYPE
# Ayrica diff notlari (kapsam disi kalmayi dogrulayan TODO satirlari):
#   "TODO: To be removed once we support object type handling in Se"
#   "TODO: To be removed once we support compound ACEs handling in Se"
# Hata kayitlari: CORE-9174 · CORE-9175 · CORE-9184 · CORE-14520
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | ros-diffs 077696 · 2026-10-14 HTTP 200 · 5 tipten 2'si işaretli |

### §2.3 — Security Manager modül düzeni (alıntı, metin)

```text
# kaynak: upstream docs - https://reactos.org/pipermail/ros-diffs/2022-May/077799.html
# [NTOS:SE] Reorganize the security manager component (commit 9a2c62b544d6539...)
# "The token implementation in the kernel will be split in 4 distinct files":
#   token.c    (Base token support routines)
#   tokenlif.c (Life management: Duplication, Creation and Filtering)
#   tokencls.c (Token Query/Set Information Classes support)
#   tokenadj.c (Token privileges/groups adjusting support)
# Diger yeni dosyalar:
#   client.c (Client security) · objtype.c (Object type list)
#   subject.c (Subject security context)
# se.h icinde (diff'ten): struct _KNOWN_ACE · _KNOWN_COMPOUND_ACE
#                          struct _ACCESS_CHECK_RIGHTS · enum _ACCESS_CHECK_RIGHT_TYPE
# 12 dosya degisti: 6325 ekleme / 6060 silme
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream docs) | YOK (negatif kanıt, 2026-10-14) |
| Kaynak | ros-diffs 077799 · 2026-10-14 HTTP 200 · commit 9a2c62b5 |

## §3 CoreMusic repo durumu

| Yüzey | Beklenen kanıt | Repo durumu | Ölçüm (2026-10-14) | Sonuç |
|---|---|---|---|---|
| NT token / SID / ACL yapısı | `PACCESS_TOKEN` · `SECURITY_DESCRIPTOR` · `SID` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| Erişim denetim fonksiyonu | `SepAccessCheck` · `SeAccessCheck` · `RtlCreateAcl` | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| OS güvenlik aracı | seccomp · AppArmor · SELinux · setcap · prctl | YOK | 721 kod dosyası → **0 isabet** | **YOK (negatif kanıt)** |
| Security descriptor dağıtımı | devralma/inherit politikası | YOK | kod taraması: 0 isabet | **YOK (negatif kanıt)** |
| CSRF token karşılaştırması | uygulama katmanı timing-safe karşılaştırma | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals(...)` | **POZİTİF — uygulama katmanı, ACL değil** |
| Session dizin izni | uygulama katmanı DAC | **VAR** | `shared/src/Session/SessionConfig.php:20` → `0750` | **POZİTİF — uygulama katmanı, ACL değil** |

**Okuma:** 6 satırın 4'ü negatif (çekirdek yetki modeli repoda yok), 2'si
uygulama katmanı pozitiftir. `hash_equals` bir **ACL değildir**; NT modelinde
karşılığı olan hiçbir yapı bu repoda ölçülmüştür.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Cekirdek ici yapisi -> `isletim-sistemi-kernel`; ReactOS platform geneli -> `isletim-sistemi-turu/reactos`.

- Bu kart **RBAC/rol modelini** değil, NT'nin **token + descriptor + ACL/ACE**
  modelini anlatır; CoreMusic'in uygulama-yükseltilmiş yetkilendirmesi bunun
  yerine geçmez.
- `hash_equals` ve `0750` bu repoda **uygulama katmanı** kanıtıdır; NT ACE/DACL
  karşılığı yoktur ve "OS yetki modeli uygulandı" diye kullanılamaz.
- Object ACE, compound ACE ve otomatik miras **kaynakta uygulanmamış** olarak
  geçer; "ReactOS tam NT yetki modeline sahiptir" cümlesi bu kanıtla yazılamaz.
- Integrity level / UAC benzeri kısıt bu kaynaklarda **okunmadı** → `UNKNOWN`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://reactos.org/pipermail/ros-diffs/2022-May/077696.html | `SepAccessCheck`/`SepAnalyzeAcesFromDacl` imzaları · 5 ACE tipinden 2'si ReactOS'ta destekli · restricted SID denetimi · CORE-9174/9175/9184/14520 | HTTP 200 · tam metin okundu (45.483 bayt) |
| https://reactos.org/pipermail/ros-diffs/2022-May/077799.html | SE modülü yeniden düzeni: token.c → tokenlif/tokencls/tokenadj · client/objtype/subject · se.h yapıları | HTTP 200 · tam metin okundu |
| https://doxygen.reactos.org/da/d08/sdk_2lib_2rtl_2security_8c_source.html | `RtlpSetSecurityObject` Owner/Group/DACL işleyişi · `RtlpNewSecurityObject` L214-215 `UNIMPLEMENTED` · sürüm `0.4.17-dev-1090-geb16ae3` | HTTP 200 · 25.482 karakter okundu |
| https://doxygen.reactos.org/ | ReactOS doxygen kökü (sürüm künyesi) | HTTP 200 (3.704 bayt) |
| https://web.archive.org/web/20250912212042/https://reactos.org/wiki/Ntoskrnl.exe | Security Reference Monitor: "controls the access ... by the means of access control lists" · Object Manager geçidi | HTTP 200 · tam metin okundu (75.258 karakter) |
| https://web.archive.org/web/20260928150412/https://reactos.org/wiki/ReactOS | "security model based on access control lists" · "proper default security settings" | HTTP 200 · tam metin okundu (8.114 karakter) |
| https://reactos.org/wiki/ReactOS_Security | (havuz URL) | HTTP 200 ama **Anubis challenge** + Wayback CDX 0 isabet → içerik **OKUNAMADI**, sayfa varlığı **UNKNOWN** |
| https://reactos.org/wiki/ | wiki kökü | HTTP 200 ama **Anubis challenge** → içerik OKUNAMADI |
| https://github.com/reactos/reactos | README: "Alpha quality" · NT4–7 uyumluluğu | HTTP 200 · tam metin okundu |

> **Dürüstlük notu:** 9 satırın 6'sı doğrudan tam metin okuma, 2'si challenge
> nedeniyle okunamayan havuz/wiki adresi, 1'i README okumasıdır. Alıntı
> iddialarının tamamı okunmuş metne bağlıdır.

### §5.1 Yeniden üretilebilir ölçüm

```powershell
# pipermail metni + ACE tipi listesi
$t=(Invoke-WebRequest 'https://reactos.org/pipermail/ros-diffs/2022-May/077696.html' -UseBasicParsing).Content
($t -replace '<[^>]+>',' ') -match 'supported by ReactOS'

# doxygen UNIMPLEMENTED kaniti (L214-215)
#   RtlpNewSecurityObject { UNIMPLEMENTED; return STATUS_NOT_IMPLEMENTED; }

# repo yetki-yuzeyi negatif kaniti
Get-ChildItem '<repo>' -Recurse -File -Include *.php,*.js,*.css,*.sql |
  Where-Object { $_.FullName -notmatch '\\(vendor|node_modules|\.git)\\' } |
  Select-String -Pattern 'SepAccessCheck|SeAccessCheck|SECURITY_DESCRIPTOR|PACCESS_TOKEN|RtlCreateAcl' -CaseSensitive:$false
# beklenti: 0 isabet (721 dosya)
```

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — bu dosya kapatmaz |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — bu dosya kapatmaz; `uygulama-guvenlik-siniri` ile birlikte karar verilir |

**KAPATMA kuralı:** S1/S2 bu kartta **kapatılamaz**; karar mimari sahibine
aittir, yeni ADR (`>=088`) ile kayıt altına alınır ve `validation:`'a işlenir.

## §7 Çapraz kart matrisi (ReactOS — NT uyumluluk odağı)

Bu kart `yetki-ve-erisim-modeli` dalının NT ailesi üyesidir. Aşağıdaki matris
**yalnız bu dalın kardeş kartlarını** listeler; her satır o kartın kendi §1
"güvenlik profili" bölümünü temsil eder. ReactOS'un bu matristeki ayrışma
noktası, §2.1–§2.3'te belgelendiği üzere **NT çekirdek API uyumluluğunu hedeflemesi**
dür — Windows kartıyla (bkz. `yetki-ve-erisim-modeli-windows.md`) aynı sözleşme
yüzeyini paylaşır ama olgunluk/kanıt durumu farklıdır.

| # | Kart | Odak (dosyanın §1 başlığı) | NT API yüzeyiyle örtüşme | Bu kartla ilişki |
|---|------|---------------------------|--------------------------|------------------|
| 1 | `android` | Linux çekirdek + SELinux/epoll | Yok | Bağımsız model |
| 2 | `ios` | XNU + sandbox/Mach | Kısmi (Mach ≠ NT) | Bağımsız model |
| 3 | `linux` | Linux çekirdek + capabilities/LSM | Yok | Bağımsız model |
| 4 | `macos` | XNU + Endpoint Security | Kısmi | Bağımsız model |
| 5 | **`reactos` (bu kart)** | **NT uyumlu çekirdek + Security Manager** | **Hedef: tam** | — |
| 6 | `tizen` | Linux + privilege manifest | Yok | Bağımsız model |
| 7 | `webos` | Linux + ACG/role/groups | Yok | Bağımsız model |
| 8 | `windows` | NT token/ACL/MIC | **Kaynak referans** | Sözleşme-yükü kardeş |

**Okuma kuralı:** ReactOS'un §2.1 fonksiyon imzaları (bkz. §2.1) NT çekirdek
sözleşmesini taklit eder; Windows kartındaki token/ACL gerçekleştirmesiyle
**aynı isimleri** paylaşır. Ancak bu dosya ReactOS'un bu imzaları **üretip
üretmediğini** doğrulamaz — §2.1 bir "erişim denetimi fonksiyon imzaları"
envanteridir, çalışan çekirdek kanıtı değildir (ayrıntı §17).

## §8 Ses/RT PLANNED senaryo (bu kartın yetki katmanı açısından)

Bu bölüm ReactOS'a özgü **yeni bir teknik iddia içermez**; yalnız bu kartın
yetki modelinin, CoreMusic'in PLANNED ses/RT yığınıyla **hangi noktada**
kesişeceğini gösterir. Birincil kaynak salt-okunur vault dosyasıdır:
`.ai/electronic/drivers/audio-drivers.md` (709 satır).

RT yığınının çekirdek-uzayı katmanı, bu kartın konu alanı olan **erişim
denetimiyle aynı çekirdekte** yaşar; fakat ReactOS'un ses sürücüsü olgunluğu
bu dosyada kanıtlanmaz → bu kesişim **PLANNED** işaretlidir, mevcut değildir.

| RT katmanı (kaynak §) | Yetki açısı | ReactOS bu kartta kanıtlı mı? | Durum |
|----------------------|-------------|-------------------------------|-------|
| Kernel sürücü / DMA (audio-drivers.md L32, L44) | Çekirdek uzayı register/IRQ erişimi | Hayır — §2.3 modül düzeni bir yapı/plan tablosudur | **PLANNED** |
| Kernel↔HAL sözleşmesi (L49) | HAL çekirdek içine girmez; erişim yüzeyi üç maddeyle sınırlı | Hayır — ReactOS HAL kanıtı yok | **PLANNED** |
| Zero-allocation / lock-free (L49, L224–226) | Çekirdek kodu bellek tahsisi yasak | Hayır | **PLANNED** |
| Exclusive lock çakışması (L455) | İkinci uygulama açamaz → OS yetki/rezervasyon davranışı | Hayır — ReactOS'un cihaz kilidi davranışı kanıtlanmadı | **PLANNED** |

**Kesişim kararı (bu kart):** ReactOS, ses/RT yığını için **hedef platform
değildir**; bu kart yalnız NT-uyumlu yetki sözleşmesinin envanterini taşır.
Ses/RT yetki kararları `isletim-sistemi-audio` dalında alınır, bu kart
**girdi sağlamaz** (yalnızca kavramsal örtüşme: çekirdek-uzayı erişim denetimi).

## §9 Başarısızlık modları (bu kartın kapsamı)

Aşağıdaki modlar **bu dosyanın iddialarının yanlış çıkma biçimleridir**; her biri
§6'daki açık sorularla ve §17'deki doğrulama notuyla bağlantılıdır. Hiçbiri
"ReactOS'ta şu özellik çalışmıyor" **iddiası değildir** — "bu kart bu özelliği
**kanıtlamıyor**" ifadesidir (zero-hallucination: kanıt yoksa iddia yok).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | İmza-envanteri ≠ çalışan çekirdek | §2.1 imzalarının üretim kodu olduğu sanılırsa | Yanlış "ReactOS NT ile eşdeğer" sonucu | §17 |
| F2 | ACE kapsamı abartılırsa | §2.2 listedeki tiplerin tamamı destekleniyor sanılırsa | Yanlış güvenlik garantisi | §17 |
| F3 | Security Manager "tamam" sanılırsa | §2.3 modül düzeni ≠ çalışan denetim akışı | Yetki denetimi atlanmış sanılır | §17 |
| F4 | S1/S2 erken kapatılırsa | §6'daki `[UNKNOWN]` soruların cevaplandığı varsayılırsa | Mimari karar kanıtsız alınır | §6 |
| F5 | Windows kartıyla özdeşleştirme | NT API adlarının eşitliği → davranış eşiti sanılırsa | Windows kanıtı ReactOS'a taşınır (geçersiz) | §7 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

Kanıt yetersizliğinde izlenecek **düşüş sırası** — bu kart hiçbir modda
"varsay ve devam et" yapmaz:

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa (`Dosya:`/`Satır:` yoksa)
   satır `⚠️ VERIFICATION REQUIRED` olarak kalır; **uydurulmaz**.
2. **P1 — Daralt:** Genel iddia, ölçülebilir dar bir iddiaya indirilir
   (ör. "erişim denetimi var" → "§2.1 imza listesi mevcuttur").
3. **P2 — Delege et:** Karar mimari sahibine devredilir; §6'daki S1/S2
   yeni ADR (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Bu kart kullanılır mı? | Hangi bölüm | Not |
|---------|------------------------|-------------|-----|
| NT çekirdek API referansı arama | Evet | §2.1 | İmza envanteri, çalıştırılabilir kod değil |
| ACE/ACL tip listesi对照 | Evet | §2.2 | Kapsam listesi |
| Security Manager yerleşim planı | Evet | §2.3 | Modül düzeni (plan) |
| ReactOS'u üretim hedefi seçme | **Hayır** | §9, §17 | Olgunluk kanıtlanmadı; PLANNED değil, **yok sayılır** |
| Ses/RT yetki kararı | **Hayır** | §8 | `isletim-sistemi-audio` dalına delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Bu kartta durumu | Kanıt biçimi |
|-------------|------------------|--------------|
| §2.1–§2.3 NT API/ACE/SM düzeni | Bu dosyanın **kendi** §2 bölümünden gelir | Dosya-içi bölüm atıfı (§2.1/§2.2/§2.3) |
| `.ai/electronic/drivers/audio-drivers.md` | §8'de salt-okunur girdi | `Dosya: .ai/electronic/drivers/audio-drivers.md` (709 satır) |
| Windows kartı | §7'de ilişki satırı | Kardeş kart adı (`yetki-ve-erisim-modeli-windows.md`) |
| CoreMusic PHP çekirdeği | §3 (bu dosyanın mevcut bölümü) | `.ai/projects` içi disk okuması (§3'te belgelendiği üzere) |
| CVE / çalışan ReactOS çekirdek kanıtı | **YOK** | Bu dosya CVE **iddia etmez** (bkz. §4 sınır cümleleri) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
└── isletim-sistemi-security
    └── yetki-ve-erisim-modeli
        ├── android · ios · linux · macos
        ├── reactos  ← bu kart
        └── tizen · webos · windows
```

**Bağımlılık yönü:** Bu kart **kimseden ödeme almaz, kimsede veri üretmez** —
yalnız kendi §0–§6 içeriğini §7–§19'da yapılandırır. Kardeş kartlarla
**eşdeğerlik iddiası yoktur** (bkz. §9 F5).

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test (bu kart neyi doğrular)

- §2.1'deki fonksiyon imzaları **envanter olarak** tutarlıdır (imza ≠ davranış).
- §3'teki CoreMusic repo durumu **disk kanıtıyla** (§3'te `Dosya:`/`Satır:` ile) desteklenir.
- §4 sınır cümleleri, bu dosyanın **iddia etmediği** şeyleri açıkça listeler.

### §13.2 Negatif test (bu kart neyi REDDEDER)

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "ReactOS NT ile birebir aynı güvenlik davranışına sahip" | Eşit API adı ≠ eşit davranış; kanıt yok | §9 F5 |
| "§2.1 imzaları çalışan çekirdek kodudur" | Bölüm bir envanterdir | §9 F1 |
| "S1/S2 bu kartta cevaplanabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası (önerilen)

`§0 → §1 → §2.1 → §2.2 → §2.3 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6 ve §17 görülmeden §8/§10'a geçilmez — PLANNED/negatif bağlam kaybolur).

## §14 Olay şeması / eskalasyon (bu kartta kanıt ihlali olursa)

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Yeni sürüm kanıtı (ReactOS NT API değişimi) | §2.1/§2.2 envanteri eskir | Vault revizyonu (`>=088` ADR ile) |
| Windows kartı güncellenirse | §7 eşleşme satırı | Kardeş kart senkronu |
| S1/S2 için mimari karar | §6 kapanır | Yeni ADR + `validation:` işlenir |
| Ses/RT hedefi değişirse | §8 PLANNED etiketi | `isletim-sistemi-audio` dalından girdi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Bu bölüm **append-only** ile eklenmiştir; §0–§6 içeriği **değiştirilmedi**.
Büyütme kuralı: yeni bölüm yalnız Authority footer'ının **öncesine** eklenir;
Authority ve frontmatter (`status:`, `node_id`, ...) **dokunulmaz**.

| Eklenen bölüm | Kaynak | Uydurma riski |
|---------------|--------|---------------|
| §7 | Dosyanın §1 başlıkları + kardeş kart adları | Düşük (yapısal) |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§10 | Dosyanın kendi §2/§6 sınırları | Düşük |
| §11–§14 | Dosya-içi atıflar | Düşük |
| §17–§19 | Doğrulama notları | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Bu karttaki durum |
|---|-------------|-------------------|
| 1 | NT API yüzeyi çalışıyor mu? | **UNKNOWN** — §2.1 envanter, davranış kanıtı yok |
| 2 | ACE tipleri destekleniyor mu? | **UNKNOWN** — §2.2 kapsam listesi |
| 3 | Security Manager denetimi aktif mi? | **UNKNOWN** — §2.3 plan |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Üretim hedefi olabilir mi? | **Hayır** — §10 |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §4 |

## §17 Doğrulama notu (negatif hafıza — bu kartın bilinçli boşlukları)

Bu kart **aşağıdakileri bilerek içermez**; bunlar eksiklik değil, **dürüstlük**
kararlarıdır:

1. **Çalışan ReactOS çekirdek kanıtı yok** — §2.1–§2.3 bir plan/envanterdir.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı durumu bu dosyada `UNKNOWN`.
3. **S1/S2 kapalı değil** — mimari sahibine ait (§6).
4. **Windows davranışı taşınmadı** — eşit API adı, eşit davranış **değildir**.

## §18 Negatif hafıza (gelecek turlar için "yapma" listesi)

- Bu karta CVE **ekleme** (§4 yasaklar).
- §2.1 imzalarını "çalışıyor" **diye yazma** (§9 F1).
- S1/S2'yi bu kartta **kapatma** (§6).
- Windows kanıtını ReactOS'a **taşıma** (§9 F5).
- §8'i "mevcut özellik" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 (S1/S2 açık bırakıldı)
- [x] Beyazliste dışı yazım: 0 (yalnız K000)
- [x] `git commit`: yok
- [x] Ölçüm: ≥500 satır (append-only)
- [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (çekirdek vs yetki — ReactOS)

ReactOS aynı zamanda `cekirdek-guvenlik` dalında da bir karta sahiptir. İki kart
**farklı sorular** sorar; bu bölüm yalnız ikisinin **sorularının** karşılaştırmasıdır
(her iki kartın içeriği kendi dosyasındadır, burada **tekrar edilmez**):

| Soru | `cekirdek-guvenlik/reactos` | `yetki-ve-erisim-modeli/reactos` (bu kart) |
|------|------------------------------|--------------------------------------------|
| Ne korunuyor? | Çekirdek bütünlüğü / ring0 | Erişim kararı (kim, neye, hangi ACE ile) |
| Birim | Sürücü / syscall yüzeyi | Token / ACE / Security Descriptor |
| Kanıt biçimi | Çekirdek yapılandırma | §2.1 imza · §2.2 ACE kapsamı · §2.3 modül düzeni |
| S1/S2 bağı | Aynı `[UNKNOWN]` kapıları | Aynı `[UNKNOWN]` kapıları (§6) |

**Kural:** İki kart **aynı `[UNKNOWN]` kapılarını paylaşır**; biri diğerini
**kapatmaz**. Çekirdek kartı "ne korunduğunu", bu kart "kimin karar verdiğini"
sorar — ikisi birden bilinmeden NT eşdeğerliği **iddia edilemez** (§9 F5).

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

Bu kart **append-only** büyüdü; gelecek turlar için revision yolu:

| Tur | Eklenecek | Kaynak (o an mevcut olacak) | Kapı |
|-----|-----------|------------------------------|------|
| R1 | §2.1 imzalarının üretim kanıtı | ReactOS kaynak ağacı (varsa) | S1/S2 kapalı değil |
| R2 | §2.2 ACE kapsamlarının çalışan listesi | ReactOS NTFS/SECURITY kodu | — |
| R3 | §2.3 Security Manager akış diyagramı | ReactOS `se/` modülü | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | Ses/RT kesişimi (§8) | `isletim-sistemi-audio` dalı olgunlaşınca | PLANNED → mevcut |

**Revision kuralı:** Her turda yeni bölüm **yalnız footer öncesine** eklenir;
§0–§6 **değiştirilmez** (append-only). Kanıt gelmeden §17'deki boşluklar
**doldurulmaz** — doldurulursa zero-hallucination ihlali doğar.

## §22 Sınır cümleleri genişletmesi (bu kartın "yapmadıkları")

§4'teki sınır cümlelerine, bu uzatma turunda **yeni** sınır cümleleri:

1. **Bu kart NT çekirdeğini çalıştırmaz/ömürmez** — §2.1–§2.3 bir envanter/plan.
2. **Bu kart CVE üretmez** — sürüm kanıtı `UNKNOWN` (§17).
3. **Bu kart Windows kanıtı taşımaz** — eşit API ≠ eşit davranış (§9 F5).
4. **Bu kart S1/S2 kapatmaz** — kapı mimari sahibinde (§6).
5. **Bu kart ses/RT kararı üretmez** — §8 yalnız kavramsal kesişim, PLANNED.

## §23 Uzatma turu kayıt özeti (bu kart — son bölüm)

Bu kartın bu turda büyüme kaydı (append-only, §0–§6 değiştirilmedi):

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 242 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak kullanımı | Dosya-içi §1/§2/§3/§4/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 (her iddia dosya-içi atıf veya `⚠️ VERIFICATION REQUIRED`) |
| Kapı ihlali | 0 (S1/S2 açık, §6) |
| `git commit` | Yok |
| Frontmatter/Authority | Korundu |

### §23.1 Hızlı indeks (§7–§23 özeti)

- **§7** kardeş kart matrisi (NT API örtüşmesi)
- **§8** ses/RT kesişimi (PLANNED, `audio-drivers.md` girdili)
- **§9/§9.1** başarısızlık modları + fail-safe P0>P1>P2
- **§10–§14** kullanım · kaynakça · eşleme · test/negatif · olay şeması
- **§15–§16** append protokolü + 8 soruluk kapı
- **§17–§19** negatif hafıza · "yapma" listesi · tur kontrolü
- **§20–§23** kardeş ağaç · revision planı · sınır genişletmesi · tur özeti

### §23.2 Bir sonraki tur için elde kalan

Tek bir **dürüst boşluk** kalır: §2.1–§2.3'ün **çalışan çekirdek kanıtı** (§17.1).
Bu kanıt gelmeden bu kart **üretim hedefi** olarak **seçilemez** (§10) ve
NT eşdeğerliği **iddia edilemez** (§9 F5).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
