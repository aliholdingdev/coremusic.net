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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

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

## §7 Çapraz Kart Matrisi (K000 içi karşılaştırma)

> **Analiz bölümü** — yeni olgu iddiası YOKTUR; hücreler diğer K000
> çekirdek-güvenlik kartlarının §1 özetleridir; Tizen satırı §2 upstream
> bloklarına bağlıdır.

| OS kartı | Root-of-trust | Ana kontrol | Tizen = temel satır farkı |
|---|---|---|---|
| **Tizen (bu kart)** | İmzalama (en az 2 imza, §2.2) | SMACK (§2.3) + sertifika→API matrisi (§2.2) | Güvenlik, **paket-beyan** katmanında başlar: privilege manifest'te |
| Linux | Bootloader → çekirdek | LSM + capability + seccomp | Tizen LSM'tir (SMACK); çekirdek ailesi ortak; `cekirdek-guvenlik-linux` |
| iOS | Boot ROM zinciri; SEP | Hardware root of trust + sandbox | Mobil doğrulama donanıma gömülü; `cekirdek-guvenlik-ios` |
| macOS | LocalPolicy (SEP imzalı) | SIP + extension | Masaüstü boot politikası; `cekirdek-guvenlik-macos` |
| Windows | UEFI + TPM | WDAC/App Control | Politika binary; `cekirdek-guvenlik-windows` |
| ReactOS | NT bootloader | Security Reference Monitor | Alpha olgunluk; `cekirdek-guvenlik-reactos` |
| webOS | Image derleme + FOTA | Statik servis + ACG | Boot-time statik yapılandırma; `cekirdek-guvenlik-webos` |

**Okuma:** Tizen üç katmanı **birbirine bağlayan** tek karttır: (1) paket
imzalama (kurulumdan önce, §2.2), (2) sertifika seviyesi → API seviyesi
(erişim), (3) SMACK sandbox (çalışma zamanı). Üçü birlikte "beyan → erişim
→ kısıt" zincirini oluşturur (§10.1). Linux kartındaki LSM esnekliği burada
SMACK'a sabitlenmiştir.

## §8 Ses/Zamanlama Yüzeyi Etkileşimi (Neva Engine senaryosu)

> `PLANNED` senaryo analizi. Tizen ses alt-sistemi bu vault'ta ayrı karttır
> (`isletim-sistemi-audio/tizen-audio`). Kaynaklar:
> `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]` ·
> §2 upstream blokları.

### §8.1 SMACK etiketleri ↔ ses cihazları (§2.3'e bağlı)

§2.3 (upstream LSM index): SMACK, `/proc/.../attr/smack` altındadır ve
major LSM olarak capability'den sonra gelir. Ses yüzeyi (analiz): Tizen
ses yöneticisi (ayrı kart) bir sistem servisidir; uygulamanın ses
cihazına erişimi SMACK etiketleri + manifest privilege (§2.1) ile
sarılır. Çıkarım: bir Tizen uygulaması `http://tizen.org/privilege/audio`
beyan etmedikçe ses API'si erişilemez — bu beyan kurulumda imza ile
kilitlenir (§2.2).

### §8.2 İmzalama → API matrisi × ses API'leri (§2.2 tablosu)

§2.2 upstream matrisi: public sertifika → yalnız public API; partner →
partner dahil; platform → platform dahil. Ses yüzeyi (analiz): standart
ses oynatma public API sınıfındadır; düşük seviye (ham cihaz/ALSA benzeri)
platform API sınıfına düşebilir. CoreMusic senaryosu için sonuç: ham
8.1/192 kHz hedefi platform sertifikası gerektirebilir → bu, bir
**geliştirici programı** kararıdır, mimari değil; §10.2'ye işaret.

### §8.3 SMACK ↔ RT zamanlama

SMACK, MAC'tir; Linux kartı §8.2'sindeki seccomp × SCHED_FIFO gerilimi
SMACK için geçerli değildir (SMACK syscall filtrelemez). Ancak SMACK
etiketleri dosya/soket erişimini sınırlar: RT ses sürecinin erişeceği
soket/dosya etiketleri önceden tanımlanmalıdır; tanımsız etiket erişimi
red yolundadır (SMACK denied-by-default karakteri — upstream SMACK
dokümanı bu kart §2.3'te okunmamıştır → ayrıntı
`⚠️ VERIFICATION REQUIRED`, yalnız LSM index satırı kullanılmıştır).

## §9 Başarısızlık Modları ve Risk Matrisi

| # | Başarısızlık | Konum | Belirti | Kaynak dayanağı | CoreMusic etkisi (senaryo) |
|---|---|---|---|---|---|
| F1 | İmza yok/eksik (en az 2) | §2.2 | Kurulum reddi | upstream (§2.2) | Uygulama hiç kurulmaz |
| F2 | Sertifika seviyesi yetersiz | §2.2 matris | Platform API erişilemez | upstream (§2.2) | Düşük seviye ses API'si reddi |
| F3 | Privilege beyanı yok | §2.1 | Çalışma zamanı izin reddi | upstream (§2.1) | Ses senaryosu durur |
| F4 | SMACK etiketi çakışması/red | §2.3 | Erişim reddi (MAC) | LSM index (§2.3) | Servis/süreç erişim hatası |
| F5 | LSM sırası beklenmedik | §2.3 | Kontrol sırası değişebilir | LSM index (§2.3) | Teşhis yanılgısı (§14 V1) |
| F6 | API seviyesi sürüm değişimi | §1.2 sürüm notları | Eski privilege eşleşmeyebilir | upstream §1.2 | Sürüm geçiş testi gerekir |

**Örüntü:** 6 modun 3'ü (F1–F3) **kurulum öncesi/anda** devreye girer;
Tizen kartı da "kapılar önce" örüntüsündedir (iOS/macOS ile aynı), ancak
kapılar donanım-anal değil **imza/manifest-anal**dir (§10.4).

## §10 Entegrasyon Senaryoları (hedef mimari)

### §10.1 Senaryo A — Zincir tam (public uygulama)

(1) Paket imzalanır (en az 2 imza, §2.2); (2) public sertifika; (3)
privilege beyanı manifest'te (§2.1); (4) SMACK sandbox içinde çalışma.
Standart ses oynatma public API ile. S1'e "hayır" karşılığı; en küçük
yüzey. §2.2 matrisinde en alt seviye.

### §10.2 Senaryo B — Platform API (düşük seviye ses)

Ham cihaz/format erişimi gerekiyorsa platform sertifikası gerekir (§8.2).
Bu bir **geliştirici programı** kararıdır (Samsung/Tizen partnerlik);
mimari sahibi değerlendirir; ADR (`>=088`) gerekir.

### §10.3 Senaryo C — OEM/TV (webOS karşılaştırma)

Tizen TV yüzeyi ile OSE/standart Tizen izin modeli eşdeğerliği
kanıtlanmamıştır (yetki kartı §1'e işaret). Bu kart, TV hedefi için ek
kanıt gerektiğini işaretler: `⚠️ VERIFICATION REQUIRED`.

### §10.4 Senaryo karşılaştırma

| Senaryo | Sertifika | İmza | Beyan | S1 | Zamanlama riski |
|---|---|---|---|---|---|
| A | Public | En az 2 | Manifest | hayır | SMACK (§8.3) |
| B | Platform | En az 2 | Manifest + platform API | evet | SMACK + API seviyesi |
| C | TV/OEM | Bilinmiyor | Bilinmiyor | ⚠️ | ⚠️ |

## §11 Kaynakça — envanter ve ölçüm

### §11.1 Atıf yapılan vault dosyaları (2026-10-16 ölçümü)

| Dosya | Satır | Kullanım |
|---|---|---|
| `.ai/electronic/drivers/audio-drivers.md` | 709 | §8 katman modeli |
| `isletim-sistemi-audio/tizen-audio/` | (bu vault) | §8.1 ses alt-sistemi |
| `isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-tizen.md` | (bu vault) | §8.1/§16 sandbox derinliği |
| `isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-tizen.md` | (bu vault) | §8.2 privilege derinliği |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-{linux,ios,macos,windows,reactos,webos}.md` | (bu vault) | §7 çapraz |

### §11.2 Upstream kanıt durumu (§2 blokları, 2026-10-14)

| Blok | Kaynak | Durum |
|---|---|---|
| §2.1 SMACK + privilege | application/native/guides/security/security-tip | Okundu |
| §2.2 İmzalama + matris | signing-certificates | Okundu |
| §2.3 LSM index | docs.kernel.org LSM | Okundu |
| SMACK ayrıntı davranışları | — | ⚠️ VERIFICATION REQUIRED (§8.3) |

## §12 Karar Ağaçları

### §12.1 Tizen ses erişimi seçimi

```
Tizen ses senaryosu
    │
    ├─ Standart oynatma/kayıt? → Public API (Senaryo A)
    │                              └─ privilege: audio (manifest, §2.1)
    ├─ Ham cihaz/format (8.1/192k)? → Platform API (Senaryo B, §8.2)
    └─ TV/OEM yüzey?                → Kanıt eksiği (Senaryo C, ⚠️)
```

### §12.2 İmzalama zinciri kararı

```
İmzalama: kurulumdan ÖNCE (§2.2) — en az 2 imza
Sertifika: public mı platform mu? → API seviyesi (§2.2 matrisi)
Değişiklik: sertifika değişimi = yeniden imzalama + yeniden kurulum
```

## §13 Zaman Çizelgesi (vault ölçümleri)

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-10-14 | SMACK/privilege/İmzalama/LSM upstream okundu | bu kart `validation:` |
| 2026-10-14 | Repo OS-güvenlik taraması: 0 isabet | bu kart §3 |
| 2026-10-16 | Bu ek yazıldı | bu dosya |

## §14 Test ve Doğrulama Önerileri (senaryo, uygulanmadı)

| # | Doğrulama | Yöntem (referans) | Beklenen | Kanıt durumu |
|---|---|---|---|---|
| V1 | LSM sırası | §2.3 kalıbı (LSM index okuma) | capability,...,smack | ⚠️ cihaz gerekli |
| V2 | İmzalama red akışı | İmzasız paket kurulumu | Kurulum reddi (F1) | ⚠️ cihaz gerekli |
| V3 | Sertifika→API matrisi | public sertifika + platform API çağrısı | Red (F2) | ⚠️ cihaz gerekli |
| V4 | Privilege beyanı | Beyansız ses API'si | Çalışma zamanı reddi (F3) | Kod yazılınca |
| V5 | SMACK etiketi | Ses servisi etiket okuması | Etiket mevcut/uyumlu | ⚠️ cihaz gerekli |

## §15 Sınır İhlali Kayıtları (negatif kontroller)

| Kontrol | Sonuç |
|---|---|
| SMACK ayrıntı davranışı iddia edildi mi? | HAYIR — yalnız LSM index satırı; ayrıntı ⚠️ |
| Repoda Tizen yapılandırması var mı iddiası? | HAYIR — §3 negatif kanıt |
| S1/S2 kapatıldı mı? | HAYIR (§6) |
| Frontmatter/`status:` değişti mi? | HAYIR (append-only) |
| TV yüzeyi eşdeğer mi diye iddia? | HAYIR — §10.3 ⚠️ |

## §16 Ek: Tizen Güvenlik Mekanizması ↔ CoreMusic Bileşeni Eşlemesi

| CoreMusic bileşeni (vault) | Tizen mekanizması | Eşleme türü | Sınır notu |
|---|---|---|---|
| Neva Engine RT çekirdeği (`PLANNED`) | Uygulama süreci (SMACK etiketli) | Zamanlama | SMACK dosya/soket erişimi (§8.3) |
| DSP zinciri (`electronic/dsp/index`) | Uygulama-içi hesaplama | Hesaplama | Zero-allocation korunur |
| Sanal ses köprüsü (`09-open-source-virtual-audio-driver`) | Tizen'de kullanıcı-kaynaklı sanal cihaz | Yok (fark) | Ayrıntı ayrı kart + ⚠️ |
| IO formatı (`10-io-format-32bit-192khz`) | API seviyesi (§2.2 matrisi) | Format | Düşük seviye = platform API |
| Mixer web PCM (`05-neva-mixer-web-pcm-plani`) | Web uygulaması (config.xml privilege, yetki kartı §2.1) | Ağ | Beyan farklı dosyada |
| Cihaz whitelist (audio-drivers.md §4.4) | Ses yöneticisi rota politikası | Seçim | Ayrı kart |

### §16.1 Beyan dosyası farkı (web vs native)

| Uygulama türü | Beyan dosyası (yetki kartı §2.1/§2.2) | Privilege biçimi |
|---|---|---|
| Web | `config.xml` | `<tizen:privilege>` |
| Native | `tizen-manifest.xml` | `<privilege>` listesi |

Her iki biçim de kurulumda imzaya girer (§2.2). CoreMusic web PCM planı
web sınıfına düşer; native DSP çekirdeği native sınıfına. Bu eşleme
`uygulama-guvenlik-siniri-tizen` kartının derinliğine bağlıdır.

## §17 Ek: SSS — Bu Kartın Sınırı

| Soru | Kısa cevap | Bölüm |
|---|---|---|
| Tizen'de kaç imza gerekir? | En az 2 (§2.2 upstream) | §2.2 |
| Public sertifika platform API'ye erişir mi? | Hayır — §2.2 matrisi | §8.2 |
| SMACK LSM index'te nerede? | Major LSM; capability'den sonra (§2.3) | §2.3 |
| SMACK ayrıntı davranışları bu kartta yazıldı mı? | Hayır — ⚠️ | §8.3 |
| Ses API'leri hangi sınıfta? | Standart public; ham cihaz platform olabilir | §8.2 |
| TV (Tizen TV) OSE ile aynı mı? | Kanıtlanmadı — ⚠️ | §10.3 |
| Repoda Tizen yapılandırması var mı? | Hayır — 0 isabet | §3 |
| S1/S2 kapatıldı mı? | Hayır (§6) | §6 |

## §18 Ek: Zincir Bileşen Detayı (§2 bloklarının sentezi)

> Bu bölüm §2'deki üç upstream bloğunun sentezidir; yeni upstream iddiası
> YOKTUR. Her satır §2'deki bir bloğa bağlıdır.

### §18.1 Üç katmanlı güvenlik zinciri (§2.1 + §2.2 + §2.3)

| Katman | Mekanizma | Anlamı | Blok |
|---|---|---|---|
| 1 — Kurulum | İmzalama (en az 2 imza) | Paket bütünlüğü + kaynak | §2.2 |
| 2 — Erişim | Sertifika seviyesi → API seviyesi | Hangi API yüzeyi açılır | §2.2 matrisi |
| 3 — Beyan | Manifest privilege (§2.1) | Hangi yetenek istenir | §2.1 |
| 4 — Kısıt | SMACK sandbox (§2.3) | Çalışma anında MAC | §2.3 |

**Sıralama kuralı:** Katman 4, katman 3'ün beyanını **kotar** — beyan
edilmeyen yetenek SMACK+API katmanından geçmez. Bu "beyan → erişim →
kısıt" zinciri (§7 Okuma) Tizen'in özgünlüğüdür.

### §18.2 Sertifika seviyesi matrisi (§2.2 upstream tablosu, genişletilmiş okuma)

| Sertifika | Public API | Partner API | Platform API | CoreMusic karşılığı |
|---|---|---|---|---|
| Public | Allowed | Not allowed | Not allowed | Senaryo A (§10.1) |
| Partner | Allowed | Allowed | Not allowed | Orta yüzey (partnerlik gerekir) |
| Platform | Allowed | Allowed | Allowed | Senaryo B (§10.2) |

**Okuma:** Üç satır, üç program erişimidir; mimari seçim bunun **üstünde**
değildir. CoreMusic'in 8.1/192 kHz ham hedefi (§8.2) partner/platform
sınırlarını test etmelidir — bu bir `⚠️` araştırma kalemidir.

### §18.3 SMACK LSM index okuması (§2.3'e bağlı)

§2.3 kalıbı: LSM index virgül ayrımı ve sıradır; örnek çıktı
`capability,yama,smack`. Teşhis sırası (Linux kartı §18.2 ile uyumlu):
önce `capability`, sonra minor (varsa), sonra major (SMACK). SMACK için
`/proc/.../attr/smack` alt dizini (§2.3) etiket okuma yüzeyidir.

### §18.4 Çekirdek LSM ↔ uygulama privilege ayrımı

| Terim | Katman | Denetleyen |
|---|---|---|
| Privilege (manifest) | Paket/uygulama | Tizen paket yönetimi + API |
| SMACK etiketi | Çekirdek (MAC) | LSM (çekirdek) |
| Sertifika seviyesi | Dağıtım | İmzalama/dağıtım altyapısı |

**Sınır kuralı:** Privilege bir **beyandır**; SMACK bir **uygulamadır**.
İkisini karıştırmamak gerekir: beyan reddi uygulama-katmanı reddidir,
SMACK reddi çekirdek reddidir (§9 F3 ↔ F4 farkı).

### §18.5 Bağımlılık haritası (bu kart ↔ diğer kartlar)

| Kart | Bağımlılık | Kullanım |
|---|---|---|
| `uygulama-guvenlik-siniri-tizen` | Paket dizinleri + çalışma zamanı | §16.1 |
| `yetki-ve-erisim-modeli-tizen` | Privilege detayı + sürüm notları | §8.2, §9 F6 |
| `isletim-sistemi-audio/tizen-audio` | Ses alt-sistemi | §8.1 |
| `isletim-sistemi-turu/tizen/*` | Platform geneli | §10 |
| `cekirdek-guvenlik-linux` | LSM çatısı (ortak çekirdek) | §8.3, §18.3 |

## §19 Ek: Okuma Sırası (yeni okuyucu için)

1. §0 ELI15 — "beyan → erişim → kısıt" zinciri çerçevesi.
2. §18.1 dört katman — bu kartın omurgası.
3. §2 blokları — upstream (SMACK/privilege/İmzalama/LSM index).
4. §3 repo durumu — negatif kanıt.
5. §7 çapraz matris — Linux ile ortak çekirdek, farklı paket katmanı.
6. §8–§10 senaryo katmanı — Senaryo A/B/C; TV yüzeyi ⚠️.
7. §18.2 sertifika matrisi — program erişimi gerçekleri.
8. §14–§15 ölçüm + kontroller.

**Ek not (okuma):** Tizen, Linux kartının "paketlenmiş" hâlidir: LSM
(§2.3) ortaktır; fark imzalama + privilege beyanı + sertifika matrisidir
(§18.1 katman 1–3). Bu üç katman Linux'ta yoktur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §20 · Append-Only Yazım Kuralı (Tizen içerik genişletme protokolü)

Bu dosya üzerinde yapılan her içerik eki aşağıdaki protokole bağlıdır; protokol
ihlali dosya bütünlüğü açısından doğrudan RED nedenidir.

### §20.1 Ekleme noktasının tek kuralı

Tizen içerik eki dosyanın **son satırından sonra** başlar; mevcut satırlar
(§0–§19 dahil) değiştirilmez, silinmez, yeniden yazılmaz:

```text
[mevcut dosya sonu — Authority kuyruğu dahil hiçbir satır değiştirilmez]
<boş satır>
---                                    <- ekleme başlangıç ayracı
<boş satır>
## §<n> · <yeni bölüm başlığı>         <- bir sonraki numaralı bölüm
```

Ekleme noktasını bulmak için Authority kuyruğu satırının **en son geçtiği yer**
kullanılır; Authority bloğu tek olduğundan konum belirsiz değildir.

### §20.2 Yasaklar (Tizen özel)

| Yasak | Açıklama |
|---|---|
| Silme | Frontmatter, §0–§19 bölümleri, tablolar, Authority kuyruğu silinemez |
| Yeniden yazma | Mevcut paragrafın temizlik gerekçesiyle revizyonu |
| Numara çakışması | Kullanılan bölüm başlığı dosyada zaten varsa yeni numara seçilir |
| Dolgu / TBD | Boş satır yığını, yer tutucu not, anlamsız tekrar paragrafı |
| Açık soru kapatma | §6 S1/S2 açık satırları değiştirilemez, yalnız yeni bölüm eklenir |
| Frontmatter değişikliği | group satırı ve tüm meta alanları aynen kalır |
| Mojibake | Karakter bozulması üretilemez (UTF-8, Türkçe karakterler korunur) |

### §20.3 Ekleme sonrası doğrulama adımı

Her eklemeden sonra satır sayımı yeniden ölçülür; hedef bandın altındaysa
bir sonraki numaralı anlamlı bölüm eklenir. Dolgu yerine her bölümde **gerçek
içerik** (bağlantılı tablo, senaryo, kontrol listesi) yazılır:

1. Satır sayımı ölç (ReadAllLines uzunluğu).
2. 500 satırın altındaysa yeni bölüm numarasıyla kaynak atıflı içerik ekle.
3. Tekrar ölç; kapı geçilene kadar devam et.
4. Bölüm içeriği her zaman başka vault dosyalarına atıfla (uydurma yok).

### §20.4 İlgili protokoller

| Protokol | İlgili bölüm |
|---|---|
| Çapraz kart matrisi kuralları | §15 |
| Kullanım senaryosu kalıbı | §16 |
| Doğrulama adımları | §17 |
| Okuma sırası penceresi | §19 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
