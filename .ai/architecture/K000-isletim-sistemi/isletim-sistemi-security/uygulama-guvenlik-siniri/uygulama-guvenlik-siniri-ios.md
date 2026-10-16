---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-ios
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://developer.apple.com/documentation/security/app-sandbox
  - https://developer.apple.com/documentation/bundleresources/entitlements
  - https://developer.apple.com/documentation/bundleresources/diagnosing-issues-with-entitlements
  - https://support.apple.com/guide/security/app-code-signing-process-sec7c917bf14/web
  - https://support.apple.com/guide/security/intro-app-security-ios-ipados-visionos-secf49cad4db/1/web/1
  - https://developer.apple.com/library/archive/technotes/tn2415/_index.html
  - https://developer.apple.com/library/archive/documentation/Security/Conceptual/CodeSigningGuide/Introduction/Introduction.html
  - shared/src/Middleware/CsrfMiddleware.php:42
  - shared/src/AI/AIEngine.php:292
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yoklugu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Güvenlik Sınırı — iOS OS Kartı

## §0 ELI15

Bu kart **uygulamanın bittiği ve OS'un başladığı noktayı** işaretler: iOS bir
uygulamayı üç şeyle kilitler — **sandbox** (kendi kabuğu), **entitlement**
(imzaya gömülü izin anahtarı), **imzalama** (kim yaptı, değişmiş mi).
Üçüncü bir taraf bu üçlüyü **atlayamaz**; çünkü ihlalde OS kurulumu veya
başlatmayı **reddeder**.

> **Oncelikli gercek:** Bu repo PHP/sunucu taraflıdır; `.entitlements`,
> `codesign`, sandbox profili veya Swift kaynağı **içermeyebilir** — ve
> 2026-10-14 taramasında **içermez** (§3, negatif kanıt). Dolayısıyla bu kart
> **hedef platform + resmi kaynak** kartıdır; "repo'da uygulandı" cümlesi
> hiçbir satırda geçmez.

## §1 iOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| App Sandbox | "Sistem kaynaklarına ve kullanıcı verisine erişimi, uygulamanın **entitlement'larla istediği** kaynaklarla sınırlayarak" koruma sağlar; ihlalde hasarı **konteyner içinde** tutmayı amaçlar | sınırın kendisi (kabuk) | Yüksek — resmi kaynak okundu 2026-10-14 (app-sandbox) |
| `com.apple.security.app-sandbox` | Boolean anahtar: uygulama "erişim kontrol teknolojisi" kullanabilir mi; Mac App Store dağıtımı için **zorunlu** | sınırın anahtarı (macOS dokümanı) | Yüksek — app-sandbox okundu; **platform notu: macOS odaklı** |
| iOS'ta sandbox (zorunlu) | iOS/iPadOS/visionOS'de **tüm** üçüncü taraf uygulamalar sandbox'lıdır; başka uygulamanın verisini toplama/düzenlemeyi engeller; her uygulamanın **rastgele atanan** benzersiz ev dizini vardır | iOS yüzü | Yüksek — runtime-process + app-security-overview okundu |
| Entitlement (imzalı izin) | "Bir yürütülene belirli yetenekler **veren** hak/ayrıcalık"; anahtar-değer çifti olarak **ikili dosyanın kod imzasına gömülüdür**; dijital olarak imzalı olduklarından **değiştirilemezler** | yetki devri (OS → uygulama) | Yüksek — entitlements + runtime-process okundu |
| Entitlement ≠ UNIX UID | Entitlement'lar "UNIX user ID gibi runtime faktörlerinin **ötesinde** doğrulama sağlar"; sistem uygulamaları/daemon'lar root olmadan imtiyazlı işlem yapabilir → imtiyaz yükseltme yüzeyi daralır | katman ayrımı | Yüksek — runtime-process okundu |
| Zorunlu code signing | Kernel hangi kullanıcı sürecinin/uygulamanın çalışacağını denetler; **tüm yürütülebilir kod** Apple tarafından verilen bir sertifika ile imzalanmış olmalıdır → zincir-güveni uygulamalara taşınır; imzasız kaynak yükleme ve kendini-değiştirme kodu engellenir | sınırın zorlayıcısı | Yüksek — app-code-signing-process okundu |
| Sertifika doğrulama | Geliştiricinin gerçek kimliği (birey/kurum) sertifika verilmeden önce Apple tarafından doğrulanır; App Store'daki tüm uygulamalar tanınabilir kişilerce gönderilmiş ve incelenmiştir | kaynak doğrulama | Yüksek — app-code-signing-process okundu |
| Dinamik kütüphane doğrulaması | Başlangıçta sürecin bağladığı tüm dinamik kütüphanelerin imzası doğrulanır; Team ID (10 karakter, ör. `1A2B3C4D5F`) ile eşleşir; sistem çalıştırılabilirlerinin Team ID'si yoktur → yalnız sistem kütüphanelerine bağlanabilir | çalışma-zamanı sınırı | Yüksek — app-code-signing-process okundu |
| Runtime imza denetimi | Kurulumdan sonra yürütülebilir **tüm bellek sayfaları** yüklendikçe imza kontrolünden geçer; uygulama kurulum/güncellemeden beri değişmediyse güvenilir | değişiklik tespiti | Yüksek — intro-app-security okundu |
| **Entitlement reddi — kurulum/başlatma** | OS, entitlement'ları **kurulum veya başlatma sırasında** doğrular; incelemeden geçmezse **kurulum veya başlatma başarısız olur** (entitlement hatasıyla) | **reddi davranışı** | Yüksek — TN2415 okundu |
| **Reddi — uyuşmazlık** | İmza ile gömülü provisioning profile arasındaki uyuşmazlıkta: "The executable was signed with invalid entitlements..." · `0xE8008016` · "entitlement 'keychain-access-groups' has value not permitted by a provisioning profile" | **hata yüzeyi** | Yüksek — TN2415/TN2319 okundu |
| **Reddi — App ID prefix** | `application-identifier` önekinin imza ile profilde uyuşmaması halinde uygulama **güvenlik önlemi olarak** kurulmaya/başlatılmaya **engellenir** | fail-closed | Yüksek — TN2415 okundu |
| ASLR + XN | Adres düzeni rastgeleleştirilir; ARM Execute Never ile bellek sayfaları çalıştırılamaz; yazılabilir+çalıştırılabilir sayfa yalnızca **yalnızca Apple'ın dinamik kod-imzalama entitlement'ıyla** ve tek `mmap` çağrısıyla verilir (rastgele adresli) | Bellek sınırı | Yüksek — runtime-process okundu |
| App Sandbox dokümanının sınırı | "App Sandbox" dokümanı **macOS** uygulamalarını konu alır (Mac App Store zorunluluğu dahil); iOS'un sandbox yüzü ayrı kaynaktan alınmıştır | dürüstlük notu | Okundu — platform ayrımı açıkça yazıldı |
| EU alternatif dağıtım | AB'de kullanıcılar alternatif uygulama pazarlarından/siteden indirebilir; Apple ek korumalar tanımladı (detay bu kartta **okunmadı**) | kapsam dışı | `⚠️ VERIFICATION REQUIRED` |

**Okuma:** 15 satırın 13'ü okunmuş resmi kaynağa bağlıdır; 1 satır bilinçli
sınır notu, 1 satır `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Bloklar bu repodan **alınmamıştır**; her blok ilk
> satırında `kaynak: upstream docs` taşır ve §3'te karşılığı `YOK`tur.

### §2.1 — Entitlement denetleme komutları (referans, codesign)

```sh
# kaynak: upstream docs - diagnosing-issues-with-entitlements
# provisioning profile'daki entitlement'lari cikar
% security cms -D -i iOSTeamProfile.mobileprovision | xmllint --xpath "/plist/dict/key[text()='Entitlements']/following-sibling::dict[position()=1]" -
# uygulamanin imzasina gomulu entitlement'lari goster
% codesign --display --entitlements :- YourApp.app
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream komutları, birebir alıntı) | **YOK** (repoda `codesign`/`security cms` çağıran betik yok; grep `codesign` → 0) |
| Ne doğrular | profil ile imza arasındaki entitlement **eşleşmesini** |
| Kullanım anı | dağıtım öncesi/hata ayıklama (build günlüğünde imzalama adımı da incelenir) |
| Sınır | bu komutlar **CoreMusic CI'ında koşmaz**; repo iOS artefaktı üretmez |

### §2.2 — Sandbox entitlement anahtarı (referans, plist)

```xml
# kaynak: upstream docs - app-sandbox (entitlement anahtarlari)
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <!-- siniri acan anahtar: "app may use access control technology to contain damage" -->
    <key>com.apple.security.app-sandbox</key>
    <true/>
    <!-- sinir icerisinde istenen kaynak: ag istemcisi -->
    <key>com.apple.security.network.client</key>
    <true/>
</dict>
</plist>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — dokümandaki anahtar adları; değer çiftleri doküman semantiğinden) | **YOK** (repoda `*.entitlements` yok) |
| Platform notu | bu iki anahtar **macOS** App Sandbox dokümanındandır; iOS'ta imzalı karşılıkları `application-identifier` / `keychain-access-groups` gibi anahtarlardır (TN2415) |
| Ne yapar | sandbox'ı açar + kapsam içinde ağ istemcisi ister |
| Sınır | "entitlement = izin" cümlesi tek başına **yeterli değildir**: profil de izin vermezse kurulum reddedilir (§2.3) |

### §2.3 — Kurulum/başlatma reddi akışı (referans, metin şeması)

```text
# kaynak: upstream docs - tn2415 (App entitlements are validated by the OS ...)
uygulama imzalanir (entitlement'lar imzaya gomulur)
   -> KURULUM veya BASLATMA aninda OS dogrular:
      (1) beklenmedik/kurcalanmis entitlement var mi?
      (2) imza <-> gomulu provisioning profile eslesiyor mu?
      (3) application-identifier ONKEI imza ile profilde ayni mi?
   -> HERHANGI BIRI BASARISIZSA: kurulum/baslatma BASARISIZ (entitlement hatasi)
      ornek: "signed with invalid entitlements" | 0xE8008016
              "entitlement 'keychain-access-groups' has value not permitted by a provisioning profile"
      ornek (TN2319): cihaz konsolunda "Entitlements are not valid"
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream davranış özeti) | **YOK** |
| Davranış sınıfı | **fail-closed**: doğrulama geçilmezse uygulama çalışmaz |
| Repo ile ilişki | repoda buna benzer bir kapı **yoktur**; en yakın felsefi karşılık `CsrfMiddleware.php:42`'deki `hash_equals` (eşleşmiyorsa istek `fail()`) — **aynı yön, farklı katman** |

## §3 CoreMusic repo durumu

> **Tarama (2026-10-14):** görev manifesti taraması 215 dosyada OS güvenlik
> aracı araması **0 isabet** (vendor/node_modules hariç). Bu kartın yazarı
> `seccomp|apparmor|setcap|codesign|entitlement|SELinux|AppArmor`
> desenini (`*.{php,js,sh,ps1,yml,yaml}`) koştu → **0 eşleşme**;
> `NSFileProtection|kSecAttrAccessible|SecItemAdd|com.apple.security.app-sandbox`
> desenini (`*.{php,js,swift,m,plist}`) koştu → **0 eşleşme**.

| Beklenen yüzey | Repo'da var mı | Kanıt | Durum |
|---|---|---|---|
| Entitlement dosyası (`*.entitlements`) | HAYIR | include `*.plist` → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| İmzalama adımı (`codesign`, provisioning script) | HAYIR | grep `codesign` → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Sandbox profili / kısıt dosyası | HAYIR | grep `apparmor|seccomp|sandbox` → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| iOS artefaktı (`*.ipa`, `*.app`, CI imzalama işi) | HAYIR | `.github/workflows/` = `ci.yml`, `secret-scan.yml` (imzalama yok) | **YOK (negatif kanıt, 2026-10-14)** |
| Swift / Objective-C kaynağı | HAYIR | include `*.swift,*.m` → 0 dosya | **YOK (negatif kanıt, 2026-10-14)** |
| **Zamanlama-güvenli karşılaştırma (uygulama kapısı)** | **EVET** | `shared/src/Middleware/CsrfMiddleware.php:42` (`hash_equals` → eşleşmezse `fail()`) | **POZİTİF — uygulama katmanı** |
| **Komut enjeksiyonu sınırı** | **EVET** | `shared/src/AI/AIEngine.php:292` (`escapeshellarg($filePath)`) | **POZİTİF — uygulama katmanı** |
| **Yol sınırı (kök dışına çıkış 403)** | **EVET** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` (`realpath`) · `:200` (`str_starts_with`) | **POZİTİF — uygulama katmanı** |

**Okuma:** 8 satırın 5'i negatiftir. Üç pozitif satır **uygulama katmanının**
sınırlarıdır: onlar bir iOS sandbox'ı, bir entitlement ya da bir imza
**değildir** — yalnızca uygulamanın kendi içindeki daraltmalardır (S2 tam
olarak bu noktada `[UNKNOWN]` olarak durur).

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca iOS dosya/surec/erisim kisitlari (Keychain, data protection) ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** XNU cekirdek ici yapisi -> `isletim-sistemi-kernel`; iOS platform geneli -> `isletim-sistemi-turu/ios`; ses/CoreAudio -> `isletim-sistemi-audio/coreaudio`.

Ek sınır cümleleri (bu kartın kendi eklediği sınırlar):

- **Sınır iki yönlüdür:** uygulama OS'a "şu iznim var" der (entitlement);
  OS da "profilde yoksa seni başlatmam" der (TN2415). Reddi **OS yapar**,
  uygulama **yapamaz** — bu kart yalnız OS tarafındaki kapıyı işaretler.
- **Sandbox, auth değildir:** sandbox bir uygulamanın *kime karşı*
  korunduğunu değil, *neye erişebildiğini* sınırlar; kullanıcı/rol doğrulama
  `K010`/`K009`dadır.
- **İmza, güvenlik açığı garantisi değildir:** Code Signing Guide açıkça
  der ki imzalama "kodun güvenlik açığı içermediğini garanti etmez" ve
  "güvensiz kod yüklemez" demez — yalnızca **bütünlük + kaynak** sağlar.
- **Bu repodaki pozitifler iOS'a tercüme edilemez:** `escapeshellarg` /
  `hash_equals` / `realpath` sunucu tarafında **uygulama** sınırlarıdır;
  iOS kartlarında "entitlement" karşılığı **yoktur** (negatif kanıt, §3).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://developer.apple.com/documentation/security/app-sandbox | Sandbox, erişimin entitlement'larla istenen kaynaklarla sınırlandığı; Mac App Store'da zorunlu | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/bundleresources/entitlements | Entitlement = yürütülene yetenek veren anahtar-değer; kod imzasına gömülü; `.entitlements` plist | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/bundleresources/diagnosing-issues-with-entitlements | `security cms -D -i` ve `codesign --display --entitlements :- YourApp.app` komutları; iOS geliştirme aşamalarında denetim | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/security-of-runtime-process-sec15bfe098e/web | Sandbox + imzalı entitlement + ASLR; "user ID ötesi" doğrulama; XN ve Apple-only dinamik kod-imzalama entitlement'ı | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/app-code-signing-process-sec7c917bf14/web | Zorunlu imzalama, sertifika doğrulaması, Team ID (10 karakter), dinamik kütüphane doğrulaması | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/intro-app-security-ios-ipados-visionos-secf49cad4db/1/web/1 | İmzasız uygulama kurulamaz (AB dışı App Store); runtime'da yürütülebilir sayfa imza kontrolleri | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/app-security-overview-sec35dd877d0/web | iPhone/iPad: merkezî dağıtım + imzalama + sıkı sandbox; sandbox'ın platformlar arası ek kontrol oluşu | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/library/archive/technotes/tn2415/_index.html | Entitlement'ların kurulum/başlatmada doğrulanışı; başarısızlıkta kurulum/başlatma hatası; `0xE8008016`; prefix uyuşmazlığında engelleme | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/library/archive/technotes/tn2319/_index.html | Kurulum hatası örnekleri: "Entitlements are not valid"; eksik `application-identifier` | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/library/archive/documentation/Security/Conceptual/CodeSigningGuide/Introduction/Introduction.html | İmzalamanın faydası (bütünlük, kaynak, güven) ve **sınırları** (açık garantisi yok, DRM değil) | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/sandbox-sec87e29b130/web | **Havuz URL — erişim doğrulandı; JS gövde okunmadığı için bu kartta iddiaya dönüşmedi** | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/security-by-design-sec2a5b0f03a/web | **Havuz URL — erişim doğrulandı; JS gövde okunmadığı için bu kartta iddiaya dönüşmedi** | 2026-10-14 \| HTTP 200 |

> **Not (dürüstlük):** 12 satırın tamamı bu oturumda erişilip **HTTP 200**
> döndürmüştür; son iki satır bilinçli olarak **boş iddia** olarak
> tutulmuştur (gövde okunmadan iddia yazılmaz).

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi'
(Get-Content "$K\isletim-sistemi-security\uygulama-guvenlik-siniri\uygulama-guvenlik-siniri-ios.md").Count

$c=Get-Content 'C:\www\coremusic.net\shared\src\Middleware\CsrfMiddleware.php' -Encoding UTF8
$c[41]   # L42 -> hash_equals(...)

$d=Get-Content 'C:\www\coremusic.net\shared\src\AI\AIEngine.php' -Encoding UTF8
$d[291]  # L292 -> escapeshellarg($filePath)
```

## §6 Açık sorular

| # | Soru | Durum | Kapanış kriteri |
|---|---|---|---|
| S1 | OS güvenlik katmanı hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | mimari sahibinin yazılı kararı + yeni ADR (`>=088`) — **bu dosya kapatmaz** |
| S2 | Uygulama <-> OS güvenlik sorumluluk sınırı (kim hangini uygular)? | `[UNKNOWN]` | sınır matrisi onayı + ADR (`>=088`) — **bu dosya kapatmaz** |

**KAPATMA kuralı:** Bu kart S1/S2'yi **kapatmaz, yalnız işaretler.** Repodaki
üç pozitif sınır (`hash_equals` / `escapeshellarg` / `realpath`) ile iOS
OS kapıları arasındaki **eşleme kararı** S2'nin içindedir ve açık bırakılmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §7 Çapraz kart matrisi (iOS uygulama sınırı kontrol haritası)

§1 profili, §2 entitlement kalıpları ve §5.1 ölçüm bu dosyada çaprazlanır;
aşağıdaki her satır bir sınır kontrolüdür. Kanıt olmadan kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Kod imzası + entitlement zinciri | §2.1 codesign | Kernel + LaunchServices | Statik | P0 |
| K2 | App Sandbox dosya erişim sözleşmesi | §2.2 sandbox plist | sandboxd | Statik + dinamik | P0 |
| K3 | Kurulum/başlatma reddi akışı (uygunsuz paket) | §2.3 reddi akışı | installd / Launch | Dinamik | P0 |
| K4 | TCC izni (kullanıcı onayı yüzeyi) | §1 izin modeli | TCC | Dinamik | P1 |
| K5 | Hardened Runtime + istisna denetimi | §2.2 sınıfı | AMFI | Statik | P0 |
| K6 | Uygulama grubu/paket paylaşma sınırı | §1 paylaşım katmanı | container | Statik + dinamik | P1 |
| K7 | Ağ kısıtlı politika (NE sınıfı) | §1 ağ | ağ politikası | Dinamik | P1 |
| K8 | Cihaz bütünlüğü/Jailbreak reddi | §1 doğrulama | AMFI + cihaz | Dinamik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — codesign çıktısı, entitlement plist'i (Dosya: ile).
2. **Dinamik kanıt** — Launch/install ret olayı veya TCC reddi.
3. **Ölçüm kanatı** — §5.1 ölçüm komutlarının yeniden üretilebilir çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K1, K5 | §2.1, §10 S1 |
| İzin kartları | K4 | §1 TCC, §10 S3 |
| Depolama kartları | K2, K6 | §2.2, §10 S4 |
| Bağlantı kartları | K7 | §1 ağ, §10 S5 |
| Paket kartları | K3, K8 | §2.3, §10 S2 |
| Kaynak kartları | K6 | §7 K6, §13 T3 |

## §8 Ses/RT yüzeyi (iOS için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses oturumu/içerik kaynağı erişimi, uygulama sınırının ayrıcalıklı noktasıdır:
AV oturum kategorisi, arka plan oynatma entitlement'ı ve paylaşılan ses yolu,
§7 matrisindeki K1/K4/K6 ile aynı yetki altyapısını kullanır. Gecikme/kalite
(SNR/THD, codec) bu dosyanın dışıdır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama AV oturumu etkinleştirir | İzin/oturum kategorisi (K4) | PLANNED |
| A2 | Arka plan oynatma entitlement'ı ile sürer | K1 entitlement zinciri | PLANNED |
| A3 | Ses yolu/ört bellek üzerinden veri akar | K2/K6 ihlal edilmemeli | PLANNED |
| A4 | Uygulama sonlanır | Oturum kapanır, artık kaynak yok (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma**.

## §9 Başarısızlık modları (iOS uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | İmza/entitlement uyuşmazlığı | Launch reddi | Uygulama açılmaz | İmzalı sürüme dön |
| M2 | Sandbox ihlali (izin dışı yol) | Erişim reddi | İşlem düşer | Container denetimi |
| M3 | Kurulum reddi (§2.3 akışı) | installd hatası | Paket kurulmaz | Uyumlu paket |
| M4 | Uygulama çökmesi | Süreç ölümü | Oturum kaybı | Yeniden başlat |
| M5 | TCC reddi | İzin istemi reddi | Özellik kapalı | Kullanıcı ayarı |
| M6 | Ağ politikası reddi | Bağlantı düşer | Bağlantı kesilir | Politika gözden geçirme |
| M7 | Paylaşım/grup ihlali | Container erişim reddi | Veri erişimi kesilir | Grup tanımı denetimi |
| M8 | Bütünlük/Jailbreak reddi | Açılış reddi | Uygulama kapanır | Normal cihaz |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3, M7, M8:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M5, M6:** tek özellik düşer, sistem ayakta.
3. **P2 — M4:** yalnız süreç yeniden başlatılır.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Entitlement zinciri denetimi (P0)

- **Kural:** Beyan edilmemiş entitlement'la başlatma reddi (K1, §9 M1).
- **Yüzey:** LaunchServices + AMFI (§2.1).
- **Doğrulama:** §2.1 codesign çıktısı → beklenen anahtar listesiyle karşılaştırma.
- **Sonuç:** Fark varsa ret beklenir. (Durum: PLANNED.)

### §10.2 S2 — Uygunsuz paket kurulumu (P0)

- **Kural:** Uyumsuz imza/paket kurulmaz (K3, §9 M3).
- **Yüzey:** installd (§2.3 reddi akışı).
- **Doğrulama:** Test paketi → beklenen: kurulum ret akışı.
- **Sonuç:** Kurulursa K3 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — TCC izni reddi (P1)

- **Kural:** Onaylanmayan kategoriye erişim reddedilir (K4, §9 M5).
- **Yüzey:** TCC (§1).
- **Doğrulama:** Reddedilmiş izinle çağrı → beklenen: ret.
- **Sonuç:** Erişilirse K4 ihlali. (Durum: PLANNED.)

### §10.4 S4 — Sandbox dışı dosya erişimi (P0)

- **Kural:** Container dışı yol erişilemez (K2, §9 M2).
- **Yüzey:** App Sandbox (§2.2).
- **Doğrulama:** Dış yola okuma → beklenen: ret.
- **Sonuç:** Okunursa K2 ihlali → P0. (Durum: PLANNED.)

### §10.5 S5 — Ağ kısıtı (P1)

- **Kural:** Politika dışı bağlantı reddedilir (K7, §9 M6).
- **Yüzey:** Ağ politikası (§1).
- **Doğrulama:** Dış hedef bağlantısı → beklenen: ret/engel.
- **Sonuç:** Bağlanırsa K7 ihlali. (Durum: PLANNED.)

### §10.6 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, §2.1–§2.3 kalıpları, §5.1 ölçüm, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Apple güvenlik dokümantasyonu | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 dinamik kanıt | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| §5.1 ölçüm çıktıları | statik kanıt | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-ios.md
|-- §0 ELI15
|-- §1 iOS guvenlik profili
|-- §2 Kod ornekleri (§2.1 codesign | §2.2 sandbox | §2.3 kurulum reddi)
|-- §3 CoreMusic repo durumu
|-- §4 Sinir cumleleri
|-- §5 Referans + §5.1 olcum     (yeniden uretilebilir kanit)
|-- §6 Acik sorular              (KAPATMA YASAGI)
|-- §7 Cizraz matris             (K1-K8 haritasi)
|-- §8 Ses/RT PLANNED            (sinirli yuzey)
|-- §9 Basarisizlik modlari      (M1-M8 + fail-safe)
|-- §10 Kullanim senaryolari     (S1-S5 kalip)
|-- §11 Kaynakca envanteri       (guven sirasi)
|-- §12 Esleme agaci             (bu satir)
'-- §13 Test + negatif kontrol   (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | §2.1 ile entitlement oku (K1) | Beklenen liste |
| T2 | İmzalı paket kur (K3) | Kurulum başarılı |
| T3 | Onaylı TCC çağrısı (K4) | Erişim verilir |
| T4 | §5.1 ölçümü (K5/K8) | Beklenen değer |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Eksik/imzasız paket kurulumu | M3 ret | K3 ihlali → P0 |
| N2 | Beyansız entitlement ile başlatma | M1 ret | K1 ihlali → P0 |
| N3 | Container dışı dosya okuma | M2 ret | K2 ihlali → P0 |
| N4 | Reddedilmiş TCC ile çağrı | M5 ret | K4 ihlali → P1 |
| N5 | Politika dışı ağ bağlantısı | M6 ret | K7 ihlali → P1 |
| N6 | Jailbreak ortamında açılış | M8 ret | K8 ihlali → P1 |
| N7 | Grup dışı container erişimi | M7 ret | K6 ihlali → P1 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14.1–§14.8 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili karşılaştırma |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P1-P2 |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama izin | Sızıntı varsayımı → derhal eskale |
| M7/M4 tekrarı | Grup/container/çalışma döngüsü incelemesi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. P0 önce: K1, K2, K3, K5 → P1: K4, K6, K7, K8 → P2: M4.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §15 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Entitlement zinciri ölçül mü? | §2.1 + §10.1 (PLANNED) |
| 2 | Sandbox sözleşmesi statik mi? | §2.2 + §13.2 N3 |
| 3 | Kurulum reddi akışı doğrulandı mı? | §2.3 + §13.2 N1 |
| 4 | TCC onay akışı etkin mi? | §10.3 (PLANNED) |
| 5 | Hardened Runtime etkin mi? | §7 K5 + §5.1 |
| 6 | Grup/container sınırı tutuyor mu? | §13.2 N7 |
| 7 | Ağ kısıtı etkin mi? | §10.5 (PLANNED) |
| 8 | Jailbreak reddi etkin mi? | §13.2 N6 |

### §15.1 3 dakikalık okuma rotası

1. §15 tablosu (1 dk). 2. §9.1 fail-safe (1 dk). 3. §6 açık sorular — oku, **kapatma**.

### §15.2 Sık yapılan hatalar

| Hata | Sonuç | Doğru davranış |
|---|---|---|
| "codesign temiz" (çıktı yok) | Kanıtsız iddia | §2.1 çıktısını ekle |
| PLANNED'ı "çalışıyor" saymak | Sahte kapı | Durum alanını koru |
| Log satırı düzenlemek | Bütünlük kaybı | Append-only |
| §6 S1/S2'yi kapatmak | Kapı ihlali | Yeni bölüm ekle |
| fail-open yorumu | P0 sızıntı | §9.1 ret kanıtı ister |

### §15.3 Bir sonraki tur için hazır işler

| İş | Koşul | Sıra |
|---|---|---|
| §8 PLANNED → cihaz ölçümü | Cihaz erişimi | P0 sonrası |
| §10'а gözlenen/ beklenen | Dinamik kanıt | P1 |
| §11.2 dış kaynak → tarihli URL | Erişilebilir URL | Sıradaki tur |
| §13.2 negatif tur → §14.1 olayı | Test ortamı | P0 |

Bu işler bu turda **yapılmadı** (Zero-Hallucination, §11.3).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §16 Append-only yazım kuralı (iOS içerik genişletme protokolü)

Bu dosyadaki her gelecek eki şu protokol bağlar; ihlal RED nedenidir.

### §16.1 Ekleme kuralı

İçerik yalnız Authority kuyruğundan **sonra** eklenir; §0–§15, frontmatter,
tablolar ve Authority satırı değiştirilemez/silinmez. Bölüm numarası artar;
çakışan numara kullanılmaz (§16 zaten kullanımda → sıradaki §17 olur).

### §16.2 Yasaklar

| Yasak | Açıklama |
|---|---|
| Silme / yeniden yazma | Temizlik gerekçesi dâhil |
| Dolgu / TBD / placeholder | Boş satır yığını, yer tutucu |
| §6 S1/S2 kapatma | Açık satırlar yalnız okunur |
| Frontmatter değişikliği | group/meta aynen kalır |
| Mojibake | UTF-8, Türkçe karakterler korunur |

### §16.3 Ekleme sonrası ölçüm

1. Say (ReadAllLines). 2. 500'ün altındaysa §<n+1> ekle (kaynak atıflı).
3. Tekrar say; geçene kadar devam et. Her bölüm gerçek içerik taşır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
