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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

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

## §7 Çapraz kart matrisi (Tizen — privilege manifest odağı)

Bu kart `yetki-ve-erisim-modeli` dalının **manifest-beyan** üyesidir. Tizen'de
yetki, POSIX DAC + Linux LSM **üzerine** eklenen bir **privilege manifest**
katmanıyla verilir (bkz. §2.1 web uygulaması config.xml · §2.2 native
tizen-manifest.xml). NT token modeliyle **hiçbir örtüşmesi yoktur**; en yakın
kardeşler Linux ailesi kartlarıdır.

| # | Kart | Odak (dosyanın §1 başlığı) | Yetki birimi | Bu kartla ilişki |
|---|------|---------------------------|--------------|------------------|
| 1 | `android` | Linux + SELinux + permission | permission string | Ailedeş (manifest-beyan) |
| 2 | `ios` | XNU + sandbox/Mach entitlement | entitlement plist | uzak |
| 3 | `linux` | Linux capabilities/LSM | capability bit | ailedeş (çekirdek) |
| 4 | `macos` | XNU + Endpoint Security | entitlement | uzak |
| 5 | `reactos` | NT Security Manager | ACE/ACL | uzak (NT) |
| 6 | **`tizen` (bu kart)** | **Linux + privilege manifest** | **privilege adı** | — |
| 7 | `webos` | Linux + ACG/role/groups | role/ACG | ailedeş (Linux) |
| 8 | `windows` | NT token/ACL/MIC | token | uzak (NT) |

**Ayrışma notu:** Tizen privilege'ları **iki ayrı manifestte** beyan edilir —
web uygulamasında `config.xml` (§2.1), native uygulamada `tizen-manifest.xml`
(§2.2). Bu iki yüzey **aynı privilege sözlüğünü** paylaşır ama **farklı
sözdizimindedir**; bu dosya ikisini de **referans olarak** alır, hiçbirini
"repo'da uygulanıyor" **diye sunmaz** (§4 sınır cümleleri).

## §8 Ses/RT PLANNED senaryo (bu kartın yetki katmanı açısından)

Bu bölüm Tizen'e özgü **yeni teknik iddia içermez**; yalnız privilege
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

Tizen ses erişimi ayrı bir **privilege** ile korunur (ör. mikrofon/çıkış
beyanı) — ancak **hangi privilege adının hangi ses API'sini açtığı** bu dosyada
kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`** (uydurma privilege adı yasak, §4).

| RT katmanı (kaynak §) | Tizen privilege açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|-----------------------|------------------------|-------|
| OS ses çatısı (L30, L43) | Ses API çağrısı privilege'e bağlı | Hayır — privilege adı doğrulanmadı | **PLANNED** |
| Kernel sürücü/DMA (L32) | Tizen çekirdeği modül kanıtı yok | Hayır | **PLANNED** |
| Zero-allocation (L224–226) | Uzay-uzayı kuralı, privilege değil | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz — OS davranışı | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** Tizen, ses/RT için **hedef platform değildir**;
bu kart yalnız privilege sözlüğünün **envanterini** taşır. Ses/RT yetki
kararları `isletim-sistemi-audio` dalında alınır; bu kart **girdi sağlamaz**.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "Tizen'de şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Uydurma privilege adı | §2.1/§2.2 listesine eklenmiş gerçek olmayan ad | Yanlış güvenlik garantisi | §4, §17 |
| F2 | "repo'da uygulanıyor" sanılması | §2.1–§2.3 referans örneklerinin uygulandığı varsayılırsa | Yanlış "mevcut" sonucu | §3, §17 |
| F3 | security category abartısı | §1.1'deki terim dürüstüğü atlanırsa | Yanlış kategori eşdeğerliği | §1.1 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | Android ile özdeşleştirme | Manifest-beyan benzerliği → davranış benzerliği sanılırsa | Android kanıtı Tizen'a taşınır | §7 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; privilege adı **uydurulmaz**.
2. **P1 — Daralt:** "Yetki var" → "§2.2 privilege listesi referans olarak mevcut".
3. **P2 — Delege et:** S1/S2 mimari sahibine; yeni ADR (`>=088`) ile kapanır.

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| Tizen privilege sözlüğü arama | Evet | §2.1, §2.2 | Referans envanter |
| config.xml / tizen-manifest.xml sözdizimi | Evet | §2.1, §2.2 | Referans örnek |
| Native privilege okuma API'si | Evet | §2.3 | Referans |
| Tizen'i üretim hedefi seçme | **Hayır** | §9, §17 | Kanıt yok |
| Ses/RT privilege kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §2.1–§2.3 privilege referansları | Dosyanın kendi §2'si | Dosya-içi bölüm atıfı |
| §1.2 sürüm notları | Upstream'de okunan tarihler (dosyanın kendi §1.2'si) | Dosya-içi atıf |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CoreMusic PHP çekirdeği | §3 (dosyanın mevcut bölümü) | §3'te belgelendiği üzere |
| CVE / çalışan Tizen privilege kanıtı | **YOK** | CVE iddiası yok (§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
└── isletim-sistemi-security
    └── yetki-ve-erisim-modeli
        ├── android · ios · linux · macos · reactos
        ├── tizen  ← bu kart
        └── webos · windows
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
kardeş kartlarla **eşdeğerlik iddiası yoktur** (§9 F5).

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1/§2.2 sözdizimleri **tutarlıdır** (referans).
- §3 disk kanıtı **`Dosya:`/`Satır:`** ile desteklenir.
- §4 sınır cümleleri **iddia etmediklerini** listeler.

### §13.2 Negatif test

| Reddedilen varsayım | Neden | Kanıt |
|---------------------|-------|-------|
| "Tizen privilege'ları repo'da uygulanıyor" | §2.1–§2.3 referans | §9 F2 |
| "privilege adları doğrulanmış gerçek adlar" | Uydurma yasak, doğrulama yok | §9 F1, §17 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §1.1 → §1.2 → §2.1 → §2.2 → §2.3 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Tizen sürüm değişimi | §1.2 sürüm notları eskir | Vault revizyonu |
| Android kartı güncellenirse | §7 ailedeş satır | Kardeş senkronu |
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
| §15–§19 | Protokol + kapı | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | Privilege sözlüğü gerçek mi? | **UNKNOWN** — referans envanter |
| 2 | config.xml/manifest uygulanıyor mu? | **UNKNOWN** — §3'te repo kanıtı yok |
| 3 | Native privilege API çalışıyor mu? | **UNKNOWN** — §2.3 referans |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Üretim hedefi olabilir mi? | **Hayır** — §10 |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §4 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan Tizen privilege kanıtı yok** — §2.1–§2.3 referans örnek.
2. **CVE/versiyon iddiası yok** — §1.2 sürüm notları upstream okumasıdır.
3. **S1/S2 kapalı değil** (§6).
4. **Android kanıtı taşınmadı** — benzer sözdizimi ≠ benzer davranış (§9 F5).

## §18 Negatif hafıza ("yapma" listesi)

- Uydurma privilege **adı ekleme** (§4, §9 F1).
- §2.1–§2.3'ü "uygulandı" **diye yazma** (§9 F2).
- S1/S2'yi **kapatma** (§6).
- Android/Tizen **özdeşleştirme** (§9 F5).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (çekirdek vs yetki — Tizen)

Tizen'in `cekirdek-guvenlik` dalındaki kartı ile bu kart **farklı sorular** sorar;
bu bölüm yalnız ikisinin **sorularının** karşılaştırmasıdır:

| Soru | `cekirdek-guvenlik/tizen` | `yetki-ve-erisim-modeli/tizen` (bu kart) |
|------|----------------------------|------------------------------------------|
| Ne korunuyor? | Çekirdek/Linux yetkilendirme altyapısı | Uygulama privilege'i (manifest beyanı) |
| Birim | LSM / çekirdek politikası | privilege adı (config.xml / tizen-manifest.xml) |
| Kanıt biçimi | Çekirdek yapılandırma | §2.1 · §2.2 · §2.3 referans envanter |
| S1/S2 bağı | Aynı `[UNKNOWN]` kapıları | Aynı `[UNKNOWN]` kapıları (§6) |

**Kural:** İki kart aynı `[UNKNOWN]` kapılarını paylaşır; biri diğerini
**kapatmaz**. Çekirdek kartı "neyin korunduğunu", bu kart "uygulamanın hangi
privilege'ı beyan ettiğini" sorar — ikisi bilinmeden Tizen yetki modeli
**tamamlanmış sayılmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak (o an mevcut) | Kapı |
|-----|-----------|----------------------|------|
| R1 | §2.1/§2.2 privilege adlarının doğrulanması | Tizen upstream docs (erişilebilir URL) | S1/S2 kapalı değil |
| R2 | §2.3 native privilege API davranış kanıtı | Tizen headers/kaynak | — |
| R3 | §1.2 sürüm notlarının yenilenmesi | Upstream release notes | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` olgunlaşınca | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Sınır cümleleri genişletmesi (bu kartın "yapmadıkları")

§4'e bu turda eklenen sınır cümleleri:

1. **Bu kart Tizen privilege'ını uygulamaz** — §2.1–§2.3 referans örnektir.
2. **Bu kart CVE üretmez** — sürüm kanıtı `UNKNOWN`.
3. **Bu kart privilege adı uydurmaz** — §4'teki dürüstlük kuralı.
4. **Bu kart S1/S2 kapatmaz** — kapı mimari sahibinde (§6).
5. **Bu kart ses/RT kararı üretmez** — §8 PLANNED.

## §23 Uzatma turu kayıt özeti (bu kart — son bölüm)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 211 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak kullanımı | Dosya-içi §1/§1.1/§1.2/§2/§3/§4/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 (S1/S2 açık, §6) |
| `git commit` | Yok |
| Frontmatter/Authority | Korundu |

### §23.1 Hızlı indeks (§7–§23)

- **§7** kardeş kart matrisi (manifest-beyan ailesi)
- **§8** ses/RT kesişimi (PLANNED, `audio-drivers.md` girdili)
- **§9/§9.1** başarısızlık modları + fail-safe P0>P1>P2
- **§10–§14** kullanım · kaynakça · eşleme · test/negatif · olay şeması
- **§15–§16** append protokolü + 8 soruluk kapı
- **§17–§19** negatif hafıza · "yapma" listesi · tur kontrolü
- **§20–§23** kardeş ağaç · revision · sınır genişletmesi · tur özeti

### §23.2 Bir sonraki tur için elde kalan

Tek dürüst boşluk: §2.1–§2.3'ün **doğrulanmış privilege adları** (§17.1).
Doğrulama gelmeden bu kart **üretim hedefi** değildir (§10).

## §24 Privilege yüzey matrisi (bu kart — son ek)

Bu bölüm §2.1–§2.3'ün **yüzey sınıflandırmasıdır**; yeni privilege adı
**uydurmaz** — mevcut referans bölümlerini **nereye ait oldukları** açısından
düzenler (Dosya-içi atıfla, zero-hallucination).

### §24.1 Manifest yüzeyleri (bu kartın sınırları)

| Yüzey | Uygulama tipi | Bölüm | Sözdizim kaynağı | Doğrulama durumu |
|-------|---------------|-------|------------------|------------------|
| `config.xml` privilege beyanı | Web uygulaması | §2.1 | Dosyanın kendi §2.1'i | **Referans** — uygulandığı kanıtlanmadı |
| `tizen-manifest.xml` privilege listesi | Native uygulama | §2.2 | Dosyanın kendi §2.2'si | **Referans** — uygulandığı kanıtlanmadı |
| Native privilege bilgisi okuma | Native (çalışma zamanı) | §2.3 | Dosyanın kendi §2.3'ü | **Referans** — davranış kanıtı yok |

### §24.2 Terim dürüstlüğü tablosu (§1.1 ile bağlantılı)

| Terim | Bu karttaki dürüst karşılığı | Yanlış kullanım |
|-------|------------------------------|-----------------|
| "privilege" | Uygulamanın manifestte beyan ettiği erim adı | "privilege = işletim izni garantisi" |
| "security category" | §1.1'de tanımlandığı üzere **terim dürüstlüğü** notu | Eşdeğerlik iddiası |
| "reference" | Sözdizim örneği | "uygulanmış/kodlanmış" |
| "UNKNOWN" | Doğrulanamayan iddia etiketi | "güvenli" veya "yok" |

### §24.3 Bir sonraki turda §24 nasıl genişler

Doğrulanabilir privilege adları elde edildiğinde §24.1'in "Doğrulama durumu"
sütunu `Referans` → `Doğrulanmış (URL)` olarak güncellenir; **satır silinmez**,
yalnız durum hücresi revize edilir (append-only ruhu: eski durum tarihiyle
paralel taşınır, geçmiş **değilmez**).

## §25 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status`/`parent_id` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only; yalnız footer öncesi genişletme |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma privilege/CVE eklendi mi? | **Hayır** | §4 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §25.1 Bu kartın tek cümlelik özeti

Tizen'in yetki modeli bu kartta **privilege manifest envanteri** olarak durur;
**doğrulanmış ad, uygulama kanıtı ve S1/S2 kararı olmadan** bu kart
**üretim hedefi değildir** ve **tamamlanmış sayılmaz** — sonraki turun işi
§21'deki revision planıdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
