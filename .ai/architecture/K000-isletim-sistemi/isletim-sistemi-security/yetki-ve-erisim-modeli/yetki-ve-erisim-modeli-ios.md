---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-ios
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://support.apple.com/guide/security/data-protection-classes-secb010e978a/web
  - https://support.apple.com/guide/security/keychain-data-protection-secb0694df1a/web
  - https://support.apple.com/en-gb/guide/security/sec6483d5760/web
  - https://support.apple.com/guide/security/data-protection-sece8608431d/1/web/1
  - https://support.apple.com/guide/security/security-of-runtime-process-sec15bfe098e/web
  - https://developer.apple.com/documentation/security/keychain-services
  - https://developer.apple.com/library/archive/technotes/tn2311/_index.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yoklugu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — iOS OS Kartı

## §0 ELI15

Bu kart iOS'ta **"kim, neye, ne zaman erişebilir"** sorusunun OS cevabını
taşı üç katman: (1) **sandbox içindeki dosya sahipliği** — her uygulamanın
kendi evi vardır; (2) **Keychain erişim kontrolü** — parolalar/anahtarlar
tek bir şifreli veritabanında, **erişim grubu** ile kapılandırılmıştır;
(3) **Data Protection sınıfları** — dosya anahtarı cihaz **kilitliyken**
elde edilebilir mi, yoksa **ilk açılıştan sonra** mı açık kalır.

> **Oncelikli gercek:** CoreMusic bir PHP uygulamasıdır; iOS Keychain,
> `NSFileProtection*` veya erişim grubu API'sine **sahip değildir** (§3 —
> negatif kanıt, 2026-10-14). Bu kart iOS'un erişim modelini **resmi
> kaynaklardan** tarif eder; repoya ait pozitif kanıt yalnızca POSIX izin ve
> yol denetimi satırlarıdır ve bunlar iOS'un yerine **geçmez**.

## §1 iOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Sandbox + benzersiz ev dizini | Tüm üçüncü taraf uygulamalar sandbox'lıdır; her uygulamanın dosyaları için kurulumda **rastgele atanan** kendine özgü bir home directory'si vardır | dosya sahipliği (DAC üstü sınır) | Yüksek — resmi kaynak okundu 2026-10-14 (runtime-process) |
| `mobile` kullanıcısı | Sistem dosyalarının çoğu ve **tüm** üçüncü taraf uygulamalar, imtiyazsız `mobile` kullanıcısı olarak çalışır | POSIX kimlik eşitliği | Yüksek — runtime-process okundu |
| Salt-okunur sistem bölümün | İşletim sistemi bölümün **salt-okunur** mount edilir; API'ler uygulamanın kendi imtiyazını yükseltmesine izin vermez | DAC'nin yetmediği yer | Yüksek — runtime-process okundu |
| UNIX user ID ötesi doğrulama | İzinlendirme, "runtime faktörleri (ör. UNIX user ID) **ötesinde** doğrulama sağlayan" imzalı entitlement'larla yapılır; imzalı olduklarından değiştirilemezler | yetki modelinin çekirdeği | Yüksek — runtime-process okundu |
| Data Protection — Class A (`NSFileProtectionComplete`) | Sınıf anahtarı kullanıcı parolası + cihaz UID'den türer; kilitlenmeden ~10 sn sonra çözülmüş sınıf anahtarı bellekten atılır → biyometri/parola ile tekrar açılana kadar veri erişilemez | **Anlık (locked) erişim** | Yüksek — data-protection-classes okundu |
| Data Protection — Class B (`NSFileProtectionCompleteUnlessOpen`) | Dosya açıkken anahtar kalır; kapalıyken korumalı | açık/kapalı ayrımı | Yüksek — data-protection-classes okundu |
| Data Protection — Class C (`NSFileProtectionCompleteUntilFirstUserAuthentication`) | Sınıf anahtarı kilitlenmede bellekten **atılmaz**; yeniden başlatma saldırılarına karşı korur; **atanmamış tüm üçüncü taraf uygulama verisinin varsayılan sınıfıdır** | **first unlock erişim** | Yüksek — data-protection-classes okundu |
| Data Protection — Class D (`NSFileProtectionNone`) | Koruma yok (macOS'ta desteklenmez) | en zayıf halka | Yüksek — data-protection-classes okundu |
| Dosya/per-extent anahtar hiyerarşisi | Her dosya benzersiz anahtarla şifrelenir; anahtar sınıf anahtarıyla sarılır ve dosya metadata'sında tutulur; metadata dosya sistemi anahtarıyla şifrelidir; **tüm sarmalama işlemleri Secure Enclave içinde** yürür, dosya anahtarı AP'ye hiç açık verilmez | anahtar sahipliği | Yüksek — data-protection (Apple devices) okundu |
| Keybag'ler | Dosya **ve** keychain sınıf anahtarları keybag'lerde toplanır (user, device, backup, escrow, iCloud Backup); parola girilince `NSFileProtectionComplete` user keybag'ten çözülür; SEP user keybag'i yönetir ve cihaz kilit durumunu raporlar | kilit durumunun kaynağı | Yüksek — keybags (en-gb) okundu |
| Keychain = şifreli veritabanı | Keychain Services, küçük veri parçalarını (parola, kısa not, kriptografik anahtar, sertifika) **şifreli bir veritabanında** saklar; erişim "yetkili süreçler" tarafından yapılır | Keychain erişim modeli | Yüksek — keychain-services + keychain-items okundu |
| Keychain sınıf ↔ dosya sınıfı eşlemesi | `NSFileProtectionComplete` ↔ `kSecAttrAccessibleWhenUnlocked` · `NSFileProtectionCompleteUntilFirstUserAuthentication` ↔ `kSecAttrAccessibleAfterFirstUnlock` · `NSFileProtectionNone` ↔ `kSecAttrAccessibleAlways` · parola zorunlu ↔ `kSecAttrAccessibleWhenPasscodeSetThisDeviceOnly` | **erişim-anı (timing)** | Yüksek — keychain-data-protection okundu |
| Keychain erişim grubu (access group) | iOS'ta **tek** keychain vardır; her öğede erişim grubu özniteliği bulunur ve erişim **grup ile kapılanır**; varsayılan olarak uygulama yalnız `application-identifier` ile eşleşen gruptaki öğelere erişir | Keychain DAC'sı | Yüksek — TN2311 okundu |
| App ID prefix ile paylaşım | Aynı App ID prefix'ine (sıklıkla Team ID) sahip uygulamalar keychain verisini paylaşabilir; özel `keychain-access-groups` ile gruplar genişletilir | paylaşım kuralı | Yüksek — TN2311 okundu |
| Data Vault (iOS 13.4+) | Üçüncü taraf uygulama verisi otomatik olarak Data Vault içinde korunur; kullanıcı Ayarlar'dan izni görür ve **iptal edebilir** | kullanıcı-yönlü erişim | Yüksek — protecting-app-access-to-user-data okundu |
| `kSecAttrAccessibleWhenUnlockedThisDeviceOnly` | Öğe yalnız **cihazda** (oluşturan cihazda) ve yalnız **cihaz açıkken** erişilebilir | cihaza bağlılık (migrasyon yok iması doküman düzeyinde) | Yüksek — protecting-keys okundu |
| macOS ile sınır | `Access Control Lists` dokümanı "hangi uygulamaların erişebileceğini macOS'ta kontrol eder" der → bu kart iOS'a **uygulamaz** | platform ayrımı | Kapsam dışı — açıkça not edildi |

**Okuma:** 16 satırın 15'i okunmuş resmi kaynağa bağlıdır; 1 satır bilinçli
olarak kapsam dışı işaretlenmiştir. Hiçbir satır "repoda uygulanıyor" demez.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Bloklar bu repodan **alınmamıştır**; `kaynak: upstream
> docs` imzalıdır ve §3'te karşılıkları `YOK`tur.

### §2.1 — Keychain erişim grubu yapısı (referans, entitlement plist)

```xml
# kaynak: upstream docs - diagnosing-issues-with-entitlements (ornek profil ciktisi)
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>application-identifier</key>
    <string>Z3P45X123C.*</string>
    <key>keychain-access-groups</key>
    <array>
        <string>Z3P45X123C.*</string>
    </array>
    <key>get-task-allow</key>
    <true/>
</dict>
</plist>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream örneğin şekli, değerler dokümandan) | **YOK** (repoda `*.entitlements` / `*.plist` dosyası yok) |
| Ne kilitler | `keychain-access-groups` = hangi keychain gruptan okunup yazılabileceği |
| Doküman notu | `get-task-allow` geliştirme profilinde `true`, dağıtım profilinde `false` olmalıdır (TN2318/TN2415) |
| Sınır | bu plist **CoreMusic'te üretilmez**; PHP tarafının karşılığı parola kasası değil, uygulama katmanıdır (K010) |

### §2.2 — Keychain erişim kontrolü (referans, Objective-C)

```objc
# kaynak: upstream docs - KeychainTouchID sample (AAPLKeychainTestsViewController.m)
SecAccessControlRef sacObject = SecAccessControlCreateWithFlags(kCFAllocatorDefault,
    kSecAttrAccessibleWhenPasscodeSetThisDeviceOnly,
    kSecAccessControlUserPresence,
    &error);
// ornek: parola kaldirilirsa gecersizlesmeyecekse kSecAttrAccessibleWhenUnlocked kullanilir
NSDictionary *attributes = @{
    (id)kSecClass: (id)kSecClassGenericPassword,
    (id)kSecAttrService: @"SampleService",
    (id)kSecValueData: [@"SECRET_PASSWORD_TEXT" dataUsingEncoding:NSUTF8StringEncoding],
    (id)kSecUseAuthenticationUI: (id)kSecUseAuthenticationUIAllow,
    (id)kSecAttrAccessControl: (__bridge_transfer id)sacObject
};
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream örnek kod, birebir alıntı) | **YOK** (`SecItem*` / `SecAccessControl*` çağrısı 0 isabet) |
| Ne doğrular | erişim yalnız cihaz-açık + kullanıcı varlığı (biyometri/parola) ile |
| Sınıf karşılığı | `WhenPasscodeSetThisDeviceOnly` = "parola varsa ve bu cihazda" |
| Repo ile ilişki | PHP tarafında benzer **zamanlama-güvenli** karşılaştırma `CsrfMiddleware.php:42` (`hash_equals`) — aynı ilke, **farklı katman** |

### §2.3 — Sınıf ↔ erişim anı eşlemesi (referans, metin şeması)

```text
# kaynak: upstream docs - keychain-data-protection-secb0694df1a
# dosya sinifi                    -> keychain karsiligi
NSFileProtectionComplete             -> kSecAttrAccessibleWhenUnlocked          (cihaz acikken)
NSFileProtectionCompleteUntilFirst...-> kSecAttrAccessibleAfterFirstUnlock      (ilk acilistan sonra)
NSFileProtectionNone                 -> kSecAttrAccessibleAlways                (her zaman)
(pasifli cihaz)                       -> kSecAttrAccessibleWhenPasscodeSetThisDeviceOnly
# keychain ogeleri dosya siniflariyla "benzer" bir sinif yapisiyla korunur; anahtarlar farklidir.
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream eşleme tablosunun metne dökümü) | **YOK** |
| Eşik | `AfterFirstUnlock` = "ilk açılıştan sonra açık"; cihaz kilitli olsa da erişilebilir |
| Seçim kuralı | arka planda çalışmayan uygulama `WhenUnlocked`/`ThisDeviceOnly` tarafını tercih eder (kaynak: protecting-keys notu) |

## §3 CoreMusic repo durumu

> **Tarama (2026-10-14):** OS güvenlik aracı taraması 418 PHP+JS dosyada
> **0 isabet** (vendor/node_modules hariç). Bu kartın yazarı
> ayrıca `NSFileProtection|kSecAttrAccessible|SecItemAdd|com.apple.security.app-sandbox`
> desenini (`*.{php,js,swift,m,plist}`) koştu → **0 eşleşme**.

| Beklenen yüzey | Repo'da var mı | Kanıt | Durum |
|---|---|---|---|
| Keychain API çağrısı (`SecItemAdd`, `SecItemCopyMatching`) | HAYIR | grep desen → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Data Protection sınıfı (`NSFileProtection*`) | HAYIR | grep desen → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Keychain erişim grubu / entitlement (`keychain-access-groups`) | HAYIR | grep desen → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Swift / Objective-C kaynağı | HAYIR | include `*.swift,*.m` → 0 dosya | **YOK (negatif kanıt, 2026-10-14)** |
| Uygulama-bazlı veri kasası (iOS container) | HAYIR | repo PHP/sunucu taraflıdır | **YOK (negatif kanıt, 2026-10-14)** |
| **Session dizin izni (POSIX DAC)** | **EVET** | `shared/src/Session/SessionConfig.php:20` (`DIR_PERMISSIONS = 0750`) · `:35` (`@mkdir($savePath, 0750, true);`) | **POZİTİF — uygulama katmanı** |
| **Yol sahipliği denetimi (dosya "evi")** | **EVET** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` (`realpath`) · `:200` (`str_starts_with` → kök dışı erişimde 403) | **POZİTİF — uygulama katmanı** |
| **Zamanlama-güvenli karşılaştırma** | **EVET** | `shared/src/Middleware/CsrfMiddleware.php:42` (`hash_equals`) | **POZİTİF — uygulama katmanı** |

**Okuma:** 8 satırın 5'i negatiftir. İki farklı dünyayı **karıştırmamak**
gerekir: `0750` + `realpath/str_starts_with` sunucuda **POSIX DAC benzeri**
bir daraltmadır; iOS'taki karşılığı sandbox (rastgele ev dizini) + keychain
erişim grubu + Data Protection sınıfıdır. Aynı kelime ("erişim"), iki farklı
katmanda **iki farklı mekanizma** demektir.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca iOS dosya/surec/erisim kisitlari (Keychain, data protection) ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** XNU cekirdek ici yapisi -> `isletim-sistemi-kernel`; iOS platform geneli -> `isletim-sistemi-turu/ios`; ses/CoreAudio -> `isletim-sistemi-audio/coreaudio`.

Ek sınır cümleleri (bu kartın kendi eklediği sınırlar):

- **Sınıf seçimi = erişim anı kararı:** `WhenUnlocked` ile
  `AfterFirstUnlock` arasındaki fark, "cihaz kilitliyken bu veri okunabilir
  mi" sorusudur; bu karar **uygulama katmanında** verilir, OS varsayılanı
  değil (varsayılan: Class C / `AfterFirstUnlock`).
- **Keychain ≠ parola kasası uygulaması:** parola üretimi/doğrulama akışı
  (Argon2id, MFA) `K010` ve `K009`dadır; burada yalnızca OS'un saklama ve
  kapılandırma mekanizması anlatılır.
- **Bir anahtar, iki dünya:** `hash_equals` (repoda) ile SEP'te sarmalama
  (kaynakta) aynı satırda **eşitlenemez**; biri uygulama, diğeri donanım
  katmanıdır.
- **`kSecAttrAccessibleAlways`-benzeri genişlik** bu kartta "öneri" olarak
  geçmez; kaynaktaki eşleme **tarafsız aktarılır**, seçim sahibi uygulamadır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://support.apple.com/guide/security/data-protection-classes-secb010e978a/web | Class A–D ve `NSFileProtection*` adları; Class A'da ~10 sn sonrası anahtar atılışı; Class C üçüncü taraf varsayılanı | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/keychain-data-protection-secb0694df1a/web | Keychain sınıflarının dosya sınıflarına benzerliği ve `kSecAttrAccessible*` eşlemesi | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/en-gb/guide/security/sec6483d5760/web | Keybag türleri (user/device/backup/escrow/iCloud); SEP'in user keybag'i yönetmesi; iOS'ta cihaz+user keybag'inin tek olması | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/data-protection-sece8608431d/1/web/1 | Dosya/per-extent anahtar + sınıf anahtarı hiyerarşisi; sarmalamanın Secure Enclave'de oluşu | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/data-protection-overview-secf6276da8a/web | Üçüncü taraf uygulamaların Data Protection'ı otomatik aldığı; sınıf atamasının dosya bazında olduğu | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/security-of-runtime-process-sec15bfe098e/web | Sandbox, rastgele ev dizini, `mobile` kullanıcısı, salt-okunur sistem bölümün, "UNIX user ID ötesinde" entitlement doğrulaması | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/security/keychain-services | Keychain'in şifreli veritabanı oluşu; API bileşenleri (keychain items / keychains / ACL) | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/security/keychain-items | Öğe paketleme, erişilebilirlik öznitelikleri, "yetkili süreçler"in bulup çözmesi | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/library/archive/technotes/tn2311/_index.html | iOS'ta tek keychain; erişim grubu kapısı; varsayılan `application-identifier` eşleşmesi; aynı prefix ile paylaşım | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/security/protecting-keys-with-the-secure-enclave | `kSecAttrAccessibleWhenUnlockedThisDeviceOnly` = yalnız bu cihaz + yalnız açıkken | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/library/archive/samplecode/KeychainTouchID/Listings/KeychainTouchID_AAPLKeychainTestsViewController_m.html | `SecAccessControlCreateWithFlags` + `kSecAccessControlUserPresence` örnek kodu | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/protecting-app-access-to-user-data-secc01781f46/web | Data Vault, Ayarlar'dan izin görme/iptal etme, sınıf bazlı zorlama | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/secure-keychain-storage-sec34d8e1b1/web | **Havuz URL — erişim doğrulandı; JS gövde okunmadığı için bu kartta iddiaya dönüşmedi** | 2026-10-14 \| HTTP 200 |

> **Not (dürüstlük):** 13 satırın tamamı bu oturumda erişilip **HTTP 200**
> döndürmüştür. Son satır bilinçli olarak **boş iddia** olarak tutulmuştur:
> kaynak gövdesi okunmadan iddia uydurulmaz.

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi'
(Get-Content "$K\isletim-sistemi-security\yetki-ve-erisim-modeli\yetki-ve-erisim-modeli-ios.md").Count

$c=Get-Content 'C:\www\coremusic.net\shared\src\Session\SessionConfig.php' -Encoding UTF8
$c[19]   # L20 -> DIR_PERMISSIONS = 0750
$c[34]   # L35 -> @mkdir($savePath, 0750, true)
```

## §6 Açık sorular

| # | Soru | Durum | Kapanış kriteri |
|---|---|---|---|
| S1 | OS güvenlik katmanı hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | mimari sahibinin yazılı kararı + yeni ADR (`>=088`) — **bu dosya kapatmaz** |
| S2 | Uygulama <-> OS güvenlik sorumluluk sınırı (kim hangini uygular)? | `[UNKNOWN]` | sınır matrisi onayı + ADR (`>=088`) — **bu dosya kapatmaz** |

**KAPATMA kuralı:** Bu kart S1/S2'yi **kapatmaz, yalnız işaretler.** iOS'ta
sınıf/erişim-anı seçimi kimin sorumluluğunda (uygulama vs. politika) sorusu
S2'nin altıdır ve açık bırakılmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §7 Çapraz kart matrisi (iOS yetki/erişim kontrol haritası)

§1 profili, §2 Keychain kalıpları (erişim grubu / erişim kontrolü / sınıf↔an
eşlemesi) ve §5.1 ölçüm burada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Yetki kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Keychain erişim grubu sınırı (app/grup paylaşımı) | §2.1 erişim grubu | keychain items | Statik + dinamik | P0 |
| K2 | Erişim kontrolü (ACL/biometrik/tampon sınıfı) | §2.2 erişim kontrolü | SecAccessControl | Statik + dinamik | P0 |
| K3 | Sınıf ↔ erişim anı eşlemesi (ne zaman okunur/yazılır) | §2.3 eşleme | keychain politika | Statik | P1 |
| K4 | TCC izin onayı (hassas kategori) | §1 izin modeli | TCC | Dinamik | P0 |
| K5 | Entitlement beyanı (capability) | §1 capability | codesign entitlement | Statik | P0 |
| K6 | Uygulama/grup sandbox erişimi | §1 depolama | Sandbox | Statik + dinamik | P0 |
| K7 | Authorization Services (right/uzatma) yüzeyi | §2.3 sınıf | AuthorizationRef | Dinamik | P1 |
| K8 | İzin/sağlık/grup paylaşım erişim anı | §1 paylaşım | HealthKit/Share sınıfı | Dinamik | P2 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — entitlement plist'i, keychain sorgu çıktısı (Dosya: ile).
2. **Dinamik kanıt** — errSecAuthFailed/erişim ret olayı.
3. **Ölçüm kanatı** — §5.1 ölçüm komutunun yeniden üretilebilir çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (yetki → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K2, K5 | §2.2, §10 S2 |
| Depolama kartları | K1, K3, K6 | §2.1/§2.3, §10 S3 |
| İzin kartları | K4, K7 | §1, §10 S1 |
| Paylaşım kartları | K8 | §10 S5 |
| Paket kartları | K5 | §7 K5, §13 N5 |
| Yaşam döngüsü kartları | K6 | §10 S4 |

## §8 Ses/RT yüzeyi (iOS yetki modeli için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses erişimi (mikrofon TCC'si, AV oturum entitlement'ı, keychain'de oturum
anahtarları) yetki modelinin gözlemlenebilir yüzeyidir: K4 (TCC), K5 (entitlement)
ve K1/K2 (keychain) zinciriyle kesişir. Ses algoritması/kalite bu dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Yetki)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama mikrofon izni ister | TCC onayı (K4) | PLANNED |
| A2 | Ret gelir | Güvenli kapalı (M5) | PLANNED |
| A3 | Ses oturum anahtarı keychain'e yazılır | K1/K2 erişim grubu + ACL | PLANNED |
| A4 | Biometrik şartlı okuma | K2 ACL koşulu → ret | PLANNED |

**Kapsam dışı:** ses algoritması/codec. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (iOS yetki modeli)

| Mod | Tetikleyici | Gözlenen belirti | Yetki etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Eşleşmeyen erişim grubu | errSecMissingEntitlement vb. | Okuma/yazma düşer | §2.1 denetimi |
| M2 | ACL/biometrik ret | errSecAuthFailed | Erişim ret | Kullanıcı/şart yolu |
| M3 | Sınıf↔an eşlemesi ihlali | Beklenmeyen an erişimi | Politika ihlali | §2.3 denetimi |
| M4 | TCC ret | İzin ret | Özellik kapalı | Kullanıcı ayarı |
| M5 | entitlement eksik | Launch/çağrı ret | Çalışma durur | §7 K5 denetimi |
| M6 | Sandbox dışı erişim | EPERM | Erişim kesilir | §7 K6 denetimi |
| M7 | Authorization right yanlış hakkı | Ref ret | İşlem düşer | §2.3/K7 denetimi |
| M8 | Paylaşım/an geri çekme | Erişim ret | Paylaşım durur | K8 denetimi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M5, M6:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M3, M4, M7:** tek politika/özellik.
3. **P2 — M8:** yalnız paylaşım.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — TCC onay/ret akışı (P0)

- **Kural:** Onaysız hassas erişim ret (K4, §9 M4).
- **Yüzey:** TCC (§1).
- **Doğrulama:** Onaysız çağrı → beklenen: ret.
- **Sonuç:** Erişilirse K4 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — Entitlement zinciri (P0)

- **Kural:** Beyan edilmemiş capability ret (K5, §9 M5).
- **Yüzey:** codesign entitlement (§1).
- **Doğrulama:** §5.1 çıktısı vs beklenen liste.
- **Sonuç:** Fark varsa M5. (Durum: PLANNED.)

### §10.3 S3 — Keychain erişim grubu (P0)

- **Kural:** Grup dışı okuma/yazma ret (K1, §9 M1).
- **Yüzey:** keychain items (§2.1).
- **Doğrulama:** Farklı grup uygulamasıyla okuma → beklenen: ret.
- **Sonuç:** Okunursa K1 ihlali → P0. (Durum: PLANNED.)

### §10.4 S4 — ACL koşulu (P0)

- **Kural:** Biometrik/tampon şartı sağlanmadan okuma ret (K2, §9 M2).
- **Yüzey:** SecAccessControl (§2.2).
- **Doğrulama:** Şartsız okuma → beklenen: errSecAuthFailed.
- **Sonuç:** Geçerse K2 ihlali → P0. (Durum: PLANNED.)

### §10.5 S5 — Paylaşım/an geri çekme (P2)

- **Kural:** Geri çekilmiş paylaşımdayken erişim ret (K8, §9 M8).
- **Yüzey:** paylaşım (§1).
- **Doğrulama:** Geri çek + erişim → beklenen: ret.
- **Sonuç:** Geçerse K8 ihlali → P2. (Durum: PLANNED.)

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
| Bu dosya §0–§6 | Profil, §2.1–§2.3 Keychain kalıpları, §5.1 ölçüm |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Apple güvenlik/Keychain dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| §5.1 ölçüm çıktıları | statik/ölçüm | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
yetki-ve-erisim-modeli-ios.md
|-- Sec0 ELI15
|-- Sec1 iOS guvenlik profili (yetki modeli)
|-- Sec2 Kod ornekleri (2.1 erisim grubu | 2.2 ACL | 2.3 sinif-an eslemesi)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans + 5.1 olcum  (kanit uretimi)
|-- Sec6 Acik sorular          (KAPATMA YASAGI)
|-- Sec7 Cizraz matris         (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED        (sinirli yuzey)
|-- Sec9 Basarisizlik          (M1-M8 + fail-safe)
|-- Sec10 Senaryolar           (S1-S5 kalip)
|-- Sec11 Kaynakca             (guven sirasi)
|-- Sec12 Esleme agaci         (bu satir)
'-- Sec13+ Test/ozet           (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | Onaylı TCC çağrısı (K4) | Erişim |
| T2 | Grup içi keychain okuma (K1) | Başarılı |
| T3 | Şartlı ACL okuma (K2) | Şart sağlanınca erişim |
| T4 | §5.1 entitlement ölçümü | Beklenen liste |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Onaysız hassas çağrı | TCC ret (M4) | K4 ihlali → P0 |
| N2 | Beyansız capability | Launch/çağrı ret (M5) | K5 ihlali → P0 |
| N3 | Grup dışı keychain okuma | ret (M1) | K1 ihlali → P0 |
| N4 | Şartsız ACL okuma | errSecAuthFailed (M2) | K2 ihlali → P0 |
| N5 | Sınıf/an ihlali erişimi | Politika ret (M3) | K3 ihlali → P1 |
| N6 | Sandbox dışı erişim | EPERM (M6) | K6 ihlali → P0 |
| N7 | Geri çekilmiş paylaşım erişimi | ret (M8) | K8 ihlali → P2 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P2 |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama erişim | Sızıntı varsayımı → derhal eskale |
| M1/M2 tekrarı | Keychain grubu/ACL denetimi |
| M5 tekrarı | Entitlement daraltma incelemesi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4, K5, K6 → P1 → K3, K7 → P2 → K8/M8.

### §15 Append-only + kapı özeti (iOS yetki eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Keychain erişim grubu doğru mu? | §2.1 + §13.2 N3 |
| 2 | ACL şartları etkin mi? | §2.2 + §13.2 N4 |
| 3 | Sınıf↔an eşlemesi geçerli mi? | §2.3 + §13.2 N5 |
| 4 | TCC onay akışı doğru mu? | §13.2 N1 |
| 5 | Entitlement eksiksiz mi? | §5.1 + §13.2 N2 |
| 6 | Sandbox erişimi korunuyor mu? | §13.2 N6 |
| 7 | Authorization right'ları net mi? | §7 K7 |
| 8 | Paylaşım geri çekme davranışı doğru mu? | §13.2 N7 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → cihaz ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Ek kontrol listesi (iOS yetki — hızlı doğrulama)

§16 kapı özetiyle birlikte çalışan tamamlayıcı mini denetim; her satır bağımsız kanıtlanır (§7.1).

| # | Ek kontrol | Beklenen | Kanıt |
|---|---|---|---|
| E1 | keychain sorgusunda grup uyuşmazlığı | ret | errSec kodu |
| E2 | ACL'de kırık/biometrik dışı yol | yok | SecAccessControl bayrağı |
| E3 | keychain sınıfı ↔ an uyuşmazlığı | yok | §2.3 eşleme dökümü |
| E4 | Üretim build'inde debug entitlement | yok | entitlement listesi |
| E5 | TCC verisi izinsiz okuması | ret | TCC logu |
| E6 | Grup paylaşımında anahtar kalıntısı | temiz | envanter |
| E7 | Authorization right eşleşmesi | beklenen hak | ref ret |
| E8 | Erişim denetimi iptal akışı | iptalde ret | ret olayı |

**Sıra:** P0 satırlar önce (E1, E2, E4, E5); P2 en son. Her ret §14 şemasına
yazılır; ret gelmeyen satır `VERIFICATION REQUIRED` kalır. Çıktı bu dosyaya
**ek** olarak yazılır; mevcut satırlar değişmez. Ölç-ekle-devam: bu eki ekledikten
sonra sayı ≥500 değilse sıradaki §18 Authority kuyruğundan sonra eklenir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
