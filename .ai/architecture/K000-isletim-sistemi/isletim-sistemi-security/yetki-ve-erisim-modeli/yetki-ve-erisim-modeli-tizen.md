---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-tizen
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://docs.tizen.org/application/security/privilege/
  - https://samsungtizenos.com/docs/application/native/reference/security-privileges
  - https://samsungtizenos.com/docs/application/native/guides/security/privilege
  - https://samsungtizenos.com/docs/application/web/reference/config-xml
  - https://samsungtizenos.com/docs/application/native/reference/manifest-xml
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erisim Modeli — Tizen OS Kartı

## §0 ELI15

Bu kart, Tizen'de **yetkinin (privilege) nasıl adlandırıldığını, kaç katmana
sıralandığını ve neye bağlandığını** anlatır: her yetki bir **URI**'dir
(`http://tizen.org/privilege/...`), üç **seviyeye** ayrılır (**public / partner /
platform**) ve bu seviye **sertifika seviyesine** bağlanır — uygulama hangi seviye
sertifikayla imzalandıysa yalnız o seviyeye kadar API kullanabilir. 3.0'dan beri bir
kategori daha var: kullanıcı açıp kapatabildiği **privacy-related** yetkiler. Tizen'de
yetki, API'ye değil **manifest beyanına** yazılır; sistem çağrıyı beyana göre reddeder.
Bu repoda bu modelin **hiçbiri uygulanmamıştır** — kart hedef mimari + negatif kanıttır.

> **Oncelikli gercek:** 2026-10-14 taramasında uygulama ağaçlarında privilege URI
> deseni (`http://tizen.org/privilege/`) **0 isabet** (727 dosya; `vendor`/`node_modules`/`.ai`
> hariç). CoreMusic yetkisi PHP katmanında yaşar (`CsrfMiddleware` rol/oturum denetimi),
> **Tizen privilege URI'si değildir** — ikisi aynı katmanda bile değildir (bkz. §3).

## §1 Tizen güvenlik profili

**Ayrım (bağlayıcı):** Tizen, Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
capability/ACL/MAC'in çekirdek tanımını taşır; bu kart **Tizen'e özgü privilege
katmanını** (URI adlandırma, public/partner/platform seviyesi, privacy kategorisi,
manifest beyanı) öne çıkarır.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Privilege (URI) | Hassas API'lere erişim yetkisi; `http://tizen.org/privilege/*` biçiminde adlandırılır | D2-1: yetkinin birimi | upstream docs · repo 0 |
| Manifest beyanı | Hassas API kullanan uygulama gerekli privilege'ı `tizen-manifest.xml` (native) veya `config.xml` (web) içinde beyan eder | D2-1: yetkinin kaynağı | upstream docs · repo 0 |
| Privilege level — **public** | En düşük seviye; Tizen Studio ile geliştirilen **her** uygulama kullanabilir | D2-2: seviye hiyerarşisi | upstream docs · repo 0 |
| Privilege level — **partner** | En az partner-seviyesi imzalı sertifika ister (satıcı ile iş ilişkisi olan geliştirici) | D2-2: seviye hiyerarşisi | upstream docs · repo 0 |
| Privilege level — **platform** | En yüksek seviye; en az platform-seviyesi imzalı sertifika ister (satıcı geliştiricisi) | D2-2: seviye hiyerarşisi | upstream docs · repo 0 |
| Sertifika seviyesi ↔ API seviyesi | Public sertifika → yalnız public API; partner → public+partner; platform → hepsi | D2-2: denetim kuralı | upstream docs · repo 0 |
| Privacy kategorisi (tabloda `Privacy` sütunu) | Yetkileri gizlilik özelliğine göre gruplar (ör. Account, Calendar, Call, Camera, Contacts, Storage) | D2-3: kategori ekseni | upstream docs · repo 0 |
| Privacy-related privilege (3.0+) | Kullanıcı runtime'da açıp kapatabilir; beyan edilmiş olsa bile **kapalıysa API çalışmaz** | D2-3: kullanıcı denetimi | upstream docs · repo 0 |
| Privacy name | Bir gizlilik özelliğine bağlı yetkiler grubunun gösterim adı | D2-3: grubun adı | upstream docs · repo 0 |
| Non-API bound privilege | API'ye bağlı olmayan yetkiler; sistem kaynağına eşlenir (`internet`, `mediastorage`, `externalstorage`, `appdir.shareddata`) | D2-4: API dışı denetim | upstream docs · repo 0 |
| `internet` privilege çekirdek denetimi | Yetki yoksa bağlantı **çekirdek katmanında** engellenir ve `connect()` izin hatası döner | D2-4: yetkinin OS'a inmesi | upstream docs · repo 0 |
| App-defined privilege (web) | Sağlayıcı paketin tanımladığı özel yetki; `tizen:appdefined-privilege` / `tizen:provides-appdefined-privilege` | D2-5: özel yetki yüzeyi | upstream docs · repo 0 |
| Privilege checker (SDK aracı) | VS Code Extension / Visual Studio Tools, kaynak kodda privilege ihlallerini ve eksik beyanı denetler | D2-denetim: kartı okuma yolu | upstream docs · repo 0 |

**Okuma:** 13 satırın tamamı `upstream docs` sınıfıdır, 0 satır bu repoda
uygulanmıştır. `Güven düzeyi` sütunu iddianın kaynağını gösterir; repo uygulaması §3'te
ayrı ölçülür.

### §1.1 Terim dürüstlüğü — "security category"

Görev tanımındaki **"security category"** terimi, okuduğum upstream metinlerde
(`reference/security-privileges.md`, `guides/security/privilege.md`, `reference/manifest-xml.md`)
**geçmemektedir**. Upstream'in kullandığı eksenler şunlardır: `Level`
(public/partner/platform) · `Privacy` (kategori sütunu) · `Non-API bound` (API dışı grup).
`manifest-xml.md` yalnızca "privilege categories differ for each API type" der.
→ "Tizen'de 'security category' adlı resmi bir katman var" iddiası
`⚠️ VERIFICATION REQUIRED` işaretlenir ve **bu kartta yazılmaz**.

### §1.2 Sürüm notları (upstream'de okunan tarihler)

| Sürüm | Değişiklik | Kaynak durumu |
|---|---|---|
| 2.X | İzinler yüklenen SMACK kurallarıyla verilir; gruplanmış kural seti = privilege | upstream docs |
| 3.0+ | `shared/data` dizini için `http://tizen.org/privilege/appdir.shareddata` zorunlu; privacy-related kategorisi başladı | upstream docs |
| 4.0+ | Privacy privilege runtime'da çözümlenir (Privacy Privilege Manager) | upstream docs |
| 5.5+ | UI uygulaması pop-up'ları `group mode`; cevapsız istek **Deny** sayılır | upstream docs |
| 8.0+ | Privacy Privilege Manager API'leri deprecated; `http://tizen.org/feature/security.privacy_privilege` feature'ı tüm profillerde devre dışı | upstream docs |

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo'da karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — config.xml privilege beyanı (referans — web uygulaması)

```xml
<!-- kaynak: upstream docs - application/web/reference/config-xml -->
<!-- <tizen:privilege/> : web uygulamasinin gerektigi API erisim yetkileri -->
<!-- occurrences: 0 or more | name (mandatory): URI of the Device API privilege -->
<tizen:privilege name="http://tizen.org/privilege/application.launch"/>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda `config.xml` / `<tizen:privilege` deseni 0 (§3) |
| Doğrulama | URL 2026-10-14 · HTTP 200 (ham `.md` kaynağı da 200) |
| Sınır | Bu blok **manifest beyanıdır**, çalışma zamanı denetimi değildir |

### §2.2 — tizen-manifest.xml privilege listesi (referans — native uygulama)

```xml
<!-- kaynak: upstream docs - application/native/reference/manifest-xml -->
<privileges>
   <privilege>http://tizen.org/privilege/application.admin</privilege>
   <privilege>http://tizen.org/privilege/appmanager.launch</privilege>
   <privilege>http://tizen.org/privilege/account.read</privilege>
</privileges>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `tizen-manifest.xml` glob'u repoda 0 (§3) |
| Not | `<privileges>` 1 (optional) · `<privilege>` 1 veya daha fazla (optional) |
| Sınır | Seviye (public/partner/platform) bu blokta **yazılmaz**; seviye sertifikadan gelir (§2 alternatifi: signing kartı → `cekirdek-guvenlik`) |

### §2.3 — Native privilege bilgisi okuma (referans)

```c
/* kaynak: upstream docs - application/native/guides/security/privilege */
#include <privilege_information.h>

char* display_name = NULL;
int ret = privilege_info_get_display_name("2.2",
                                          "http://tizen.org/privilege/application.launch",
                                          &display_name);
/* ayrica: privilege_info_get_description()      -> yetki aciklamasi
   privilege_info_get_privilege_info_list()      -> yetki listesi (GList)
   donus: privilege_error_e / privilege_info_error_e */
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `privilege_information.h` çağrısı repoda 0 (§3) |
| Bağımlılık | Tizen Native SDK · `CAPI_SECURITY_FRAMEWORK_PRIVILEGE_INFO` |
| Not | Bu blok yetki **bilgisini okur**; yetki **vermez** |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** kapsam `*.php,*.js,*.sql,*.css,*.xml,*.json,*.yml,*.yaml,*.sh`
> → **727 dosya** (`vendor`/`node_modules`/`.git`/`.ai`/`storage`/`logs` hariç).
> Desen `http://tizen.org/privilege` → **0 isabet**; `tizen-manifest|<tizen:privilege`
> → **0 isabet**; `smack` → **0 isabet**.

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| Privilege URI envanteri | `http://tizen.org/privilege/*` beyanları | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| Manifest beyan dosyası | `tizen-manifest.xml` · `config.xml` güvenlik bölümü | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| Sertifika seviyesi / imzalama anahtarı | `*.p12` · partner/platform sertifika | **YOK** | `.gitignore` + secret yasağı · gizlilik kuralı |
| Privacy runtime izin akışı | `ppm_check_permission` benzeri çağrı | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| Tizen privilege checker (SDK) | CI adımı / CLI çağrısı | **YOK** | komut 0 · negatif kanıt (2026-10-14) |
| CoreMusic rol/oturum denetimi (uygulama katmanı) | PHP middleware | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |
| Session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |
| Shell kaçış koruması (uygulama katmanı) | PHP yardımcı fonksiyonu | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg` · **P** |
| Yol normalizasyonu (uygulama katmanı) | PHP dosya denetimi | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · `:200` → `str_starts_with` · **P** |

**Okuma:** 9 satırın 5'i `YOK` (negatif kanıt), 4'ü uygulama katmanı pozitif kanıttır.
**Hiçbir pozitif satır Tizen yetki modeline ait değildir.** CoreMusic'te yetki
PHP middleware'inde yaşar; Tizen'de yetki **manifest + sertifika + çekirdek denetimi**
üçlüsüne bağlıdır — bu fark §6 S2'nin dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Tizen platform geneli -> `isletim-sistemi-turu/tizen`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://samsungtizenos.com/docs/application/native/reference/security-privileges | Privilege URI'leri; public/partner/platform seviyeleri; privacy-related 3.0+; non-API bound (`internet` çekirdek katmanında engellenir); 8.0 depreme notu | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/guides/security/privilege | Privilege Info API; privilege name/description/privacy name; manifest beyanı edilmiş olsa bile privacy kapalıysa API çalışmaz | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/web/reference/config-xml | `<tizen:privilege name="..."/>` elementi; `name` zorunlu, URI; `tizen:appdefined-privilege` | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/reference/manifest-xml | `<privileges>` / `<privilege>` hiyerarşisi; URI beklenen değer; privilege beyanı zorunluluğu | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/guides/concepts/signing-certificates | Sertifika seviyesi → API seviyesi matrisi (public/partner/platform) | 2026-10-14 · HTTP 200 |
| https://docs.tizen.org/application/security/privilege/ | Görev havuzundaki URL — **içerik taşımıyor** | 2026-10-14 · HTTP 200 (302 → `samsungtizenos.com/docs/` açılış sayfası) |

> **Dürüstlük notu (zorunlu):** 6 satırın 5'i bu oturumda **içerik olarak okunmuş**
> ve HTTP 200 döndürmüştür (ham `.md` kaynakları `raw.githubusercontent.com/Samsung/tizen-docs/master`
> üzerinden de 200 ile çekilmiştir). 6. satır olan `docs.tizen.org` havuz URL'si
> **200 döner ama tüm alt yolları ana sayfaya yönlendirir** — sayfa içeriği **yoktur**;
> bu satır kanıt olarak **kullanılmaz**, yalnızca taşıma kaydıdır. Bu kartta uydurma
> CVE, uydurma privilege adı veya "repo'da uygulanıyor" iddiası **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 4 pozitif satır PHP katmanında; Tizen privilege katmanı hiç başlamamış |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız mimari
sahibinin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
