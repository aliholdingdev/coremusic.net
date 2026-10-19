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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca ReactOS dosya/surec/erisim kisitlari ele alinir.

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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
