---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-tizen
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://docs.tizen.org/native/guides/security/
  - https://samsungtizenos.com/docs/application/native/guides/security/security-tip
  - https://samsungtizenos.com/docs/application/native/guides/concepts/signing-certificates
  - https://samsungtizenos.com/docs/application/native/reference/security-privileges
  - https://docs.kernel.org/admin-guide/LSM/index.html
  - shared/src/Session/SessionConfig.php:20
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Cekirdek-Guvenlik — Tizen OS Kartı

## §0 ELI15

Bu kart, Tizen'de **çekirdek düzeyinde güvenlik** ne demek onu anlatır: (1) Tizen'in
**Linux çekirdeği** üzerine kurulduğunu, altta **SMACK** adlı MAC (zorunlu erişim
denetimi) modülünün uygulama sandbox'ını taşıdığını, (2) her uygulamanın **imzalama**
(author + distributor, en az 2 imza) ile kurulduğunu ve (3) **sertifika seviyesinin**
hangi API seviyesine karşılık geldiğini (public / partner / platform). Tizen bir Linux
dağılımıdır; bu kart, Linux kartının tekrarı değil, **Tizen'e özgü privilege + SMACK +
imzalama** üçlüsünü gösterir. Bu repoda bu katmanların **hiçbiri yoktur** — kart hedef
mimari + negatif kanıttır.

> **Oncelikli gercek:** 2026-10-14 taramasında uygulama ağaçlarında Tizen güvenlik
> aracı (`smack` / `tizen-manifest` / `<tizen:privilege` / `config.xml` güvenlik
> beyanı) **0 isabet** (727 dosya; `vendor`/`node_modules`/`.ai` hariç). Literal
> `Tizen` dizesi 3 satırda geçer ve **üçü de UA cihaz algılamadır** (bkz. §3).
> Tek pozitif satırlar uygulama katmanındadır (ör. `shared/src/Session/SessionConfig.php:20`
> → `DIR_PERMISSIONS = 0750`) ve o da **DAC** ayarıdır, çekirdek koruması değildir.

## §1 Tizen güvenlik profili

**Ayrım (bağlayıcı):** Tizen, Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
çekirdeğin evrensel ayarlarını (KASLR, modül imzası, LSM modu) taşır; bu kart
**Tizen'e özgü** privilege/sandbox katmanını (SMACK etiketleri, çekirdek privilege
kavramı, imza + sertifika seviyesi) öne çıkarır. İkisi birlikte okunur, biri diğerinin
özeti değildir.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Linux LSM çerçevesi | MAC denetimlerini çekirdeğe hook'lar; Smack "major" modül olarak listelenir; aktif liste `/sys/kernel/security/lsm` | D1-1: çekirdek zemini | upstream docs (docs.kernel.org) · repo 0 |
| SMACK sandbox | Uygulama iç depolamadaki dosyalar **varsayılan olarak** diğer uygulamalar tarafından erişilemez | D1-2: süreç/veri izolasyonu | upstream docs · repo 0 |
| Süreç izolasyonu + MAC | Tizen güvenlik mimarisi "Linux basic security model" üzerine kuruludur — süreç izolasyonu + zorunlu erişim denetimi | D1-1: mimarinin tanımı | upstream docs · repo 0 |
| Çekirdek privilege kavramı | Tizen 3.0'da platform **core privileges** ile erişim denetler; uygulama privilege beyanı kavramı değişmez | D1-2: yetkinin çekirdeğe inmesi | upstream docs · repo 0 |
| SMACK kural grubu = privilege | Amaçlarına göre gruplanmış SMACK kural setine "privilege" denir (Tizen 2.X modeli) | D1-2: privilege'ın fiziksel karşılığı | upstream docs · repo 0 |
| Uygulama imzalama | Tüm uygulamalar kurulumdan önce dijital olarak imzalanır; en az 2 imza: **author** + **distributor** | D1-3: güven zinciri | upstream docs · repo 0 |
| Sertifika → API seviyesi matrisi | Public sertifika yalnız public API; partner sertifika public+partner; platform sertifika hepsi | D1-3: yetkinin anahtarı | upstream docs · repo 0 |
| Mağaza imzası | Dağıtıcı (mağaza) imzası uygulamanın kullanabileceği API setini belirler | D1-3: dağıtım kapısı | upstream docs · repo 0 |
| Secure Key Management | Anahtar/sertifika deposu; **non-exportable** anahtar ham değeri istemciye verilmez | D1-4: kripto anahtarı koruması | upstream docs · repo 0 |
| YACA (Yet Another Crypto API) | Simetrik/asimetrik şifreleme, digest, dijital imza | D1-4: kripto işlemi | upstream docs · repo 0 |
| Device Policy Management (DPM) | Kurumsal cihaz politikaları (IT yöneticisi kontrolü) | D1-5: cihaz politikası yüzeyi | upstream docs · repo 0 |
| Malware scanning + URL reputation (CSR) | Dosya/dizin taraması ve URL itibar kontrolü | D1-5: tehdit tarama yüzeyi | upstream docs · repo 0 |
| Device Certificate Manager | Doğrulama ve güvenli iletişim için sertifika/anahtar hizmeti | D1-5: sertifika hizmeti | upstream docs · repo 0 |

**Okuma:** 13 satırın tamamı `upstream docs` sınıfıdır (hedef mimari), 0 satır bu
repoda uygulanmıştır. `Güven düzeyi` sütunu iddianın kaynağını gösterir — repo
uygulamasını değil; repo uygulaması §3'te ayrı ölçülür.

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo'da karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — SMACK sandbox + manifest privilege beyanı (referans)

```text
# kaynak: upstream docs - application/native/guides/security/security-tip
# tizen icin cekirdek guvenlik modeli (metin ozeti, upstream satirlarindan)
A Tizen application is sandboxed by SMACK.
  -> internal storage dosyalari varsayilan olarak diger uygulamalara kapali
manifest: tizen-manifest.xml -> SMACK etiketlerini ve gerekli privilege'lari aciklar
Tizen 2.X : izinler yuklenen SMACK kurallariyla verilir
  -> amaclara gore gruplanmis kural setine "privilege" denir
Tizen 3.0+: platform "core privileges" ile erisim denetler
  -> uygulamanin manifestte privilege beyan etme kavrami DEGISMEZ
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda `tizen-manifest.xml` / SMACK etiketi glob'u 0 (§3) |
| Doğrulama | URL 2026-10-14 · HTTP 200 (yayın sayfası + ham `.md` kaynağı) |
| Sınır | SMACK kural yazımı bu kartta **tanımlanmaz**; yalnız varlığı taşınır |

### §2.2 — İmzalama ve sertifika → API seviyesi (referans)

```text
# kaynak: upstream docs - application/native/guides/concepts/signing-certificates
# tum Tizen uygulamalari kurulumdan ONCE imzalanmak zorundadir (en az 2 imza)
imzalar:
  author signature     -> gelistiricinin kendi author anahtari ile
  distributor signature -> dagitici (magaza) imzasi; API setini belirler

# sertifika seviyesi x API seviyesi matrisi (upstream tablo)
# | certificate level | public API | partner API | platform API |
# | public            | Allowed    | Not allowed | Not allowed  |
# | partner           | Allowed    | Allowed     | Not allowed  |
# | platform          | Allowed    | Allowed     | Allowed      |
imza semasi: W3C "XML Digital Signatures for Widgets" spesifikasyonu
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `.p12`/sertifika/imzalama anahtarı artifact'i glob 0 (§3) |
| Not | "Allowed/Not allowed" hücreleri upstream tablosunun birebir taşımasıdır |
| Sınır | Kripto seçimi (ADR-022) bu kartta değil → `K009-security` |

### §2.3 — Çekirdek LSM durumu okuma (referans)

```sh
# kaynak: upstream docs - https://docs.kernel.org/admin-guide/LSM/index.html
cat /sys/kernel/security/lsm
# ornek cikti (virgulle ayrilmis, siralama kontrol sirasidir):
#   capability,yama,smack   -> capability her zaman ilk, sonra minor, sonra major
ls /proc/self/attr/
# Smack icin /proc/.../altinda kendi alt dizini vardir (attr/smack)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `/sys/kernel/security/lsm` okuması repoda 0 (§3) |
| Bağımlılık | Linux çekirdeği; SMACK `CONFIG_DEFAULT_SECURITY` / `security=` boot parametresiyle seçilir |
| Çıkış sınıfı | sistem dosyası okuması — belirsiz yorum `UNKNOWN`, uydurma yasak |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** kapsam `*.php,*.js,*.sql,*.css,*.xml,*.json,*.yml,*.yaml,*.sh`
> → **727 dosya** (`vendor`/`node_modules`/`.git`/`.ai`/`storage`/`logs` hariç).
> Tizen güvenlik aracı deseni (`tizen|smack|<tizen:privilege|security-privileges`)
> → **0 isabet**. Literal `Tizen` → 3 isabet, **üçü de UA cihaz algılama**.

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| SMACK etiketi / politika dosyası | `smack*` · `load_policy` · `*.rules` | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| Tizen manifest / config.xml güvenlik beyanı | `tizen-manifest.xml` · `<tizen:privilege` | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| privilege URI envanteri | `http://tizen.org/privilege/*` | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| imzalama sertifikası / anahtarı | `*.p12` · `*.pem` (release) | **YOK** | `.gitignore` + secret yasağı · gizlilik kuralı |
| LSM durum okuma adımı | `cat /sys/kernel/security/lsm` | **YOK** | komut 0 · negatif kanıt |
| UA cihaz algılama (Tizen dizesi) | TV user-agent listesi | **VAR (güvenlik değil)** | `assets.coremusic.net/js/core/breakpoints.js:31` · `shared/src/Device/DeviceDetector.php:33` · `breakpoints.spec.js:54` → **davranış testi kapsamı** |
| session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |
| zamanlama-güvenli karşılaştırma | uygulama katmanı | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |
| shell kaçış koruması | uygulama katmanı | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg` · **P** |
| yol normalizasyonu | uygulama katmanı | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · `:200` → `str_starts_with` · **P** |

**Okuma:** 11 satırın 5'i `YOK` (negatif kanıt), 1'i "VAR ama güvenlik dışı" (UA
algılama), 5'i uygulama katmanı pozitif kanıttır. **Hiçbir pozitif satır Tizen çekirdek
güvenlik katmanına ait değildir** — hepsi PHP/JS uygulama katmanıdır; bu, §6 S2'nin
dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Tizen platform geneli -> `isletim-sistemi-turu/tizen`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://samsungtizenos.com/docs/application/native/guides/security/security-tip | Uygulama SMACK ile sandbox'lanır; manifest SMACK etiketi + privilege beyanı açar; Tizen 3.0 core privileges; privilege = gruplanmış SMACK kural seti | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/guides/concepts/signing-certificates | En az 2 imza (author + distributor); sertifika seviyesi → API seviyesi matrisi; W3C widget digsig spesifikasyonu | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/reference/security-privileges | Güvenlik mimarisi Linux basic security model (süreç izolasyonu + MAC) üzerine privilege + imzalama ile kurulur; public/partner/platform seviyeleri | 2026-10-14 · HTTP 200 |
| https://samsungtizenos.com/docs/application/native/guides/security/overview | Native güvenlik modülleri: secure key, privilege info, privacy permissions, YACA, DPM, CSR, DCM | 2026-10-14 · HTTP 200 |
| https://docs.kernel.org/admin-guide/LSM/index.html | LSM MAC uzantılarına SELinux/Smack/TOMOYO/AppArmor örnekleri; aktif liste `/sys/kernel/security/lsm`; `/proc/.../attr/smack` Smack'e ait | 2026-10-14 · HTTP 200 |
| https://docs.tizen.org/native/guides/security/ | Görev havuzundaki URL — **içerik taşımıyor** | 2026-10-14 · HTTP 200 (302 → `samsungtizenos.com/docs/` açılış sayfası) |

> **Dürüstlük notu (zorunlu):** 6 satırın 5'i bu oturumda **içerik olarak okunmuş**
> ve HTTP 200 döndürmüştür (ham `.md` kaynakları `raw.githubusercontent.com/Samsung/tizen-docs/master`
> üzerinden de 200 ile çekilmiştir). 6. satır olan `docs.tizen.org` havuz URL'si
> **200 döner ama tüm alt yolları ana sayfaya yönlendirir** — yani sayfa içeriği
> **yoktur**; bu satır kanıt olarak **kullanılmaz**, yalnızca taşıma kaydıdır.
> Bu kartta uydurma CVE, uydurma komut çıktısı veya "repo'da uygulanıyor" iddiası **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 5 pozitif satırın tamamı uygulama katmanı; SMACK/privilege sınırı repoda hiç başlamamıştır |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız mimari
sahibinin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
