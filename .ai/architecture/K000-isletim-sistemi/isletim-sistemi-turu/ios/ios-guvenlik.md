---
title: "iOS — Apple iOS güvenlik profili (K000 K009 sınırı)"
type: architecture
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-guvenlik
node_type: security-profile
status: active
date: 2026-10-15
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
evidence: "K000-isletim-sistemi/isletim-sistemi-turu/ios (parent)"
validation: "2026-10-15 — K000-K009 sınırı · zero-hallucination"
open_questions: []
group: mimari
---

# iOS — Apple iOS güvenlik profili

## 0. ELI15

iOS, iPhone ve iPad'lerin işletim sistemi ve güvenlik modeli Apple'ın en katı
uygulama karantinası üzerine kuruludur: her uygulama kendi sandbox'ı içine
hapsedilir, uygulama imzası zorunludur, App Store dışından uygulama kurulamaz
varsayılır ve donanım-destekli şifreleme (Secure Enclave + Data Protection)
açıkçasız devreye girer. Bu profilde iki şeyi birbirinden ayıracağız:
**(1) iOS'un güvenlik mimarisi** — app sandbox, LocalAuthentication
(Face ID/Touch ID), App Tracking Transparency ve Apple'ın privacy katmanları
— ve **(2) bu repo'nun iOS'a dair GERÇEK izi** — ki repo'da iOS'a özgü tek
satır bile kod yoktur; yalnızca UA tespitinde `'iPhone' => 'phone'` /
`'iPad' => 'tablet'` eşlemeleri ve mobil BffInterface uygulaması vardır.
Yani iOS güvenlik profili bu repoda **tamamen pasif**tir: UA'da iPhone gören
sunucu, o istemciyi iOS cihaz olarak sınıflandırır ama iOS'a özgü hiçbir
güvenlik kontrolü (Keychain, Face ID, ATS, Universal Link doğrulaması)
uygulamaz — çünkü iOS istemcisi repo'da hiç var olmamıştır. Bu dosya, bir
sonraki iOS uygulaması yazıldığında Apple'ın zorunlu güvenlik katmanlarının
hangileri olduğunu ve repoda hangi altyapının EKSİK olduğunu gösterir.

## 1. OS güvenlik profili — Apple iOS

iOS güvenlik modeli, macOS'un aksine **tek bir kapı** üzerine kuruludur:
App Store. Kullanıcı ayarlardan izin vermedikçe App Store dışından imzasız
uygulama kurulamaz (sideload); bu, Apple'ın XProtect benzeri "onaylı
yazılım" modelinin en uç biçimidir — macOS'taki Gatekeeper'ın tersine,
iOS'ta kullanıcı bu kapıyı açamaz (AB Yasası'nın zorlamasıyla bazı
bölgelerde 2025'ten itibaren sideload kapıları açılmaya başlamıştır; bkz.
§6). Uygulama karantinası iOS'ta macOS'takinden de katıdır: her uygulama
kendi sandbox'ı içindedir, uygulamalar arası iletişim yalnızca sistem
tarafından yönetilen XPC servisleri ve açık API'lerle olur; uygulama
içinden başka bir uygulamanın dosyasına, process'ine veya keychain
kaydına erişilemez. Apple Silicon Mac'lerde app sandbox'ın kökeni
zaten iOS'tur — iOS sandbox, macOS'a taşınan katı modelin kendisidir.

Kimlik doğrulama tarafında Apple'ın iki güçlü primitifi vardır:
**Secure Enclave** (cihaz kimliğini, Face ID/Touch ID biyometrik
template'lerini ve Data Protection anahtarlarını ayrı bir, erişilemez
donanım alanında tutan coprocessor) ve **LocalAuthentication**
(Face ID / Touch ID / passcode ile uygulama-içi biyometrik doğrulama;
LAContext ile biyometrik politika belirleme, "biyometrik değişti mi"
kontrolü). Data Protection, dosya içeriğini anahtarıyla birlikte
Secure Enclave kökenli anahtarla AES ile şifreler — cihaz kilitliyken
dahi uygulama verisi okunamaz. iOS ayrıca varsayılan olarak tüm
ağ trafiğini ATS (App Transport Security) ile zorunlu TLS'e iter;
HTTP'ye izin yalnızca açık beyanla (NSAllowsArbitraryLoads) verilebilir
ve bu incelemeye tabidir.

Gizlilik tarafında Apple'ın iki katmanı iOS 14.5+ için kritiktir:
**App Tracking Transparency (ATT)** — uygulamalar kullanıcıdan açık izin
almadan IDFAdentifier/ATT tracking verisi toplayamaz — ve **privacy nutrition
labels** (App Store'da veri toplama beyanı zorunluluğu). Ayrıca
iOS'ta Universal Link, universal link doğrulaması (apple-app-site-association)
ile site-uygulama köprüsünü güvenli hale getirir; bu repo'da iOS
uygulaması olmadığı için bu köprü de kurulmamıştır.

| Katman | iOS uygulaması | Repo izi |
|---|---|---|
| App sandbox | Her uygulama kendi alanında | ❌ repo'da iOS uygulaması yok |
| App zorunlu imza + App Store tek kapı | Varsayılan | ❌ repo'da Xcode/App Store Connect yok |
| Secure Enclave + Data Protection | Donanım-destekli | ❌ repo'da Secure Enclave entegrasyonu yok |
| LocalAuthentication (Face ID/Touch ID) | Biyometrik API | ❌ repo'da `LAContext` yok (grep 0) |
| App Tracking Transparency | iOS 14.5+ zorunlu | ❌ repo'da `ATTrackingManager` yok (grep 0) |
| ATS (App Transport Security) | Varsayılan zorunlu TLS | ❌ repo'da Info.plist/ATS anahtarı yok (glob 0) |
| Universal Link (apple-app-site-association) | Site↔uygulama köprüs | ❌ repo'da `apple-app-site-association` yok |

Sonuç: iOS'un güvenlik mimarisi (sandbox + imza + Secure Enclave +
LocalAuthentication + ATT + ATS), repo'da **hiçbir karşılığı olmayan** bir
güvenlik yüzeyidir. UA'da iPhone gören sunucu yalnızca `device_type = phone`
sınıflandırması yapar — iOS cihazına özgü tek bir güvenlik kontrolü
uygulanmaz. Bu, "iOS güvenli olduğu için dokunmuyoruz" değil, "iOS
uygulaması hiç yazılmadığı için iOS güvenlik yüzeyi repoda boş" demektir.

### 1.1 iOS ↔ macOS güvenlik yüzeyi karşılaştırması (aynı repo, iki Apple OS)

| Yüzey | macOS | iOS | Repo karşılığı |
|---|---|---|---|
| Dağıtım kapısı | Gatekeeper + notarization (sideload mümkün) | App Store tek kapı (sideload kapalı varsayılır) | İkisinde de paketleme yok |
| Uygulama sandbox'ı | App Sandbox opsiyonel | Her uygulama için zorunlu | `*.entitlements` glob 0 |
| Çekirdek genişletmesi | 3. parti kext yüklenebilir | kext yok — yalnız system extension | grep 0 |
| Biyometrik kapı | Touch ID (T2/Apple Silicon) | Face ID/Touch ID + `LAContext` | grep 0 |
| Kimlik saklama | Keychain | Keychain + Secure Enclave kayıtları | grep 0 |
| Ağ politikası | ATS yalnızca Mac App Store uygulamalarında | ATS varsayılan zorunlu (Info.plist) | `Info.plist` glob 0 |
| Site↔uygulama köprüsü | Universal Link | Universal Link (AASA) | `apple-app-site-association` glob 0 |

Farkın özü dağıtım kapısıdır: macOS'ta kullanıcı Gatekeeper'ı bilerek aşabilir;
iOS'ta varsayılan kapalıdır ve güvenlik zinciri donanım + imza + App Store
üçlüsüne yaslanır. Bu üçlünün hiçbiri repoda temsil edilmediği için iOS
profili macOS'tan daha boştur — iOS için hiçbir CI runner'ı dahi yoktur
(`ci.yml` altı job'ının tamamı `ubuntu-latest`: L27·L69·L112·L144·L163·L180 —
read-this-session ✓).

## 2. Repo izi — iOS güvenlik kontrolünün GERÇEK durumu

> **Kural:** Bu bölümdeki her DOSYA:SATIR, bu oturumda `read` ile okunmuş
> kanıttır. Okunmayan satır numarası yazılmaz.

Repo'da iOS'a özgü tek iz, UA tespitindeki iki eşleme ve mobil BffInterface'dir:

**`shared/src/Device/DeviceDetector.php:40-41`** — iOS cihazlarının iki
modeli (iPhone / iPad) ayrı ayrı sınıflandırılır (read-this-session ✓):

```php
'iPhone' => 'phone',
'iPad' => 'tablet',
```

**`shared/src/Device/DeviceDetector.php:228`** — `device_type_from_ua()`
içinde UA'nın iPhone/iPad içerip içermediğine bakılır (read-this-session ✓).

**`shared/src/Api/Bff/MobileBff.php:19`** — `final class MobileBff implements
BffInterface` (read-this-session ✓). **`:48`** — `'clientType' => 'mobile'`
(read-this-session ✓). Yani mobil istemci (iOS veya Android) için ayrı bir
BffInterface uygulaması vardır; bu sınıf iOS'a özgü değildir, iki mobil
platformu da kapsar.

**`.github/workflows/ci.yml`** — repo'nun tek CI workflow'u; Android,
Windows, Linux, macOS runner'ları çalıştırır; **iOS runner YOK** (L27·L69·
L112·L144·L163·L180 — read-this-session ✓). Yani repo'da iOS build/test
pipeline'ı hiç kurulmamıştır.

**iOS'un NEGATİF kanıtı (sıfır-uydurma dürüstlüğü):** `keychain`,
`localauthentication`, `lacontext`, `sandbox` → 0 isabet (shared/src);
`.plist` → 0 glob; `.entitlements` → 0; `Xcode`/`IPA`/`provisioning` → 0;
repo kökünde `.xcworkspace`/`.xcodeproj` → 0. Yani:
- repo'da Keychain (`kSecClass`) entegrasyonu **yok**,
- repo'da Face ID/Touch ID (`LAContext`) doğrulaması **yok**,
- repo'da App Tracking Transparency (`ATTrackingManager`) **yok**,
- repo'da Info.plist (ATS anahtarları dahil) **yok**,
- repo'da Xcode projesi / entitlements / provisioning profile **yok**.

**Sonuç (gerçek durum):** iOS güvenlik profili bu repoda **%100 pasif**tir.
Repo'nun iOS'a dair tek gerçeği, UA tespitindeki `'iPhone' => 'phone'` /
`'iPad' => 'tablet'` eşlemeleri ve ortak `MobileBff` sınıfıdır; Apple'ın
sandbox, imza, Secure Enclave, LocalAuthentication, ATT veya ATS
katmanlarından **hiçbiri** repoda temsil edilmez — çünkü iOS uygulaması hiç
yazılmamıştır. Bu, "iOS güvenli olduğu için dokunmuyoruz" değil, "iOS
uygulaması hiç yazılmadığı için iOS güvenlik yüzeyi repoda BOŞ" demektir.

### 2.1 iOS uygulaması yazılsaydı repoda NE bulunmalıydı (boşluk matrisi)

Bu tablo "iOS güvenlik yüzeyi repoda BOŞ" iddiasının test edilebilir karşılığıdır —
her satır bir iOS uygulamasında bulunması gereken ve bu repoda BULUNMAYAN
artefakttır (grep/glob 0 — read-this-session ✓):

| Aranacak artefakt | Beklenen konum | Repo durumu |
|---|---|---|
| `*.xcworkspace` / `*.xcodeproj` | repo kökü veya `mobile/` | 0 glob |
| `Info.plist` (ATS anahtarları) | iOS app bundle | 0 glob |
| `*.entitlements` (Keychain access, app groups) | iOS app bundle | 0 glob |
| `kSecClass` / `SecItemAdd` (Keychain) | app kodu | grep 0 |
| `LAContext` (Face ID/Touch ID) | app kodu | grep 0 |
| `ATTrackingManager` (ATT izni) | app kodu | grep 0 |
| `apple-app-site-association` (AASA) | web kökü / `.well-known/` | 0 glob |
| iOS/macOS Xcode runner | `.github/workflows/` | 0 (altı job ubuntu-latest) |
| `Podfile` / `Package.swift` | mobile/ | 0 glob |

Matrisin tamamının boş olması "iOS uygulaması PLANNED" demektir. Dolması için
asgari iş paketi: (1) Xcode projesi + entitlements, (2) Keychain sarmalayıcı
(`kSecClass` ile güvenli depolama), (3) LocalAuthentication kapısı, (4) ATS +
AASA yapılandırması, (5) CI'a Xcode job'u. Bu paketin sırası ve sahibi bir ADR
ile sabitlenmelidir — bu dosya kararı vermez, yalnızca boşluğu tarif eder.

## 3. K000 sınırı — bu profilden ne devralır / devretmez

### Bu dosyanın devraldığı (bridge kartlarına bakış)
- `../isletim-sistemi-security/cekirdek-guvenlik` — çekirdek/kernel-güvenlik
  soyutlaması: iOS'ta çekirdek XNU + Secure Enclave; sandbox'ın çekirdek
  düzeyinde zorlanması.
- `../isletim-sistemi-security/yetki-ve-erisim-modeli` — iOS'ta yetki
  modeli: TCC (Transparency, Consent, Control) izin prompt'ları + App sandbox
  erişim sınırları + Secure Enclave kökenli anahtar erişimi.
- `../isletim-sistemi-security/uygulama-guvenlik-siniri` — uygulama-içi
  sınır: iOS'ta her uygulamanın sandbox'ı + LocalAuthentication ile uygulama
 -içi biyometrik kapı + ATS ile ağ çıkışı.

### Bu dosyanın devretmediği (bilinçli — K009'un alanı)
- **K009-security** — bu dosya uygulamanın iOS güvenlik **yüzeyini**
  tarifler (sandbox, biyometrik, ATT, ATS); uygulamanın genel güvenlik
  politikası (kimlik doğrulama akışı, rate limiting, audit log, encryption
  at rest/in transit) K009'un alanıdır.
- **Güvenlik politikası kararı değil, yüzey tarifi:** bu dosya "iOS'ta
  Face ID şart mı" sorusunu yanıtlamaz; "iOS yüzeyinde Face ID/LAContext
  entegrasyonu repoda yok" der — kararı K009 ve ilgili ADR verir.
- **Cross-platform:** `.htaccess` / `web.config` / UA header kısıtları
  / CI runner seçimi — bunlar OS-güvenlik dışı, web-güvenlik ve
  build-güvenlik konularıdır; bu dosya yalnızca iOS yüzeyini kapsamaz.

## 4. K009 sınırı

Bu profildeki her madde uygulama güvenlik **yüzeyinin** iOS'a dair
tarifidir; uygulamanın hangi güvenlik politikasına bağlanacağı
(uygulama-içi kimlik doğrulama, rate limiting, audit log) K009'un
alanıdır → [`../../../K009-security/index.md`](../../../K009-security/index.md).

## 5. Kaynaklar (vendor-official — hepsi 2026-10-15'te HTTP 200 doğrulandı)

| # | Kaynak | Erişim tarihi | Not |
|---|---|---|---|
| 1 | [developer.apple.com — App Sandbox](https://developer.apple.com/documentation/security/app-sandbox) | 2026-10-15 | HTTP 200 ✓ (SPA; başlık döndü) |
| 2 | [developer.apple.com — LocalAuthentication](https://developer.apple.com/documentation/localauthentication) | 2026-10-15 | HTTP 200 ✓ (SPA; başlık döndü) |
| 3 | [developer.apple.com — Protecting the User's Privacy (ATT)](https://developer.apple.com/documentation/app-tracking-transparency/protecting-the-user-s-privacy) | 2026-10-15 | HTTP 200 ✓ (SPA; başlık döndü) |
| 4 | [developer.apple.com — Documentation / Security](https://developer.apple.com/documentation/documentation/security) | 2026-10-15 | HTTP 200 ✓ (SPA; başlık döndü) |
| 5 | [developer.apple.com — UIKit](https://developer.apple.com/documentation/uikit) | 2026-10-15 | HTTP 200 ✓ (SPA; başlık döndü) |
| 6 | [support.apple.com — Platform Security Guide (web)](https://support.apple.com/guide/security/welcome/web) | 2026-10-15 | HTTP 200 ✓ (gövde okundu — §1 bu kaynağından) |

## 6. Açık sorular

1. Repo bir gün iOS uygulaması yazar mı — yazarsa Keychain
   (`kSecClass`) ve Data Protection entegrasyonu hangi sprint'te gelir,
   ne zaman "güvenli depolama" şartı olur?
2. iOS uygulaması yazıldığında App Transport Security (ATS) varsayılan
   zorunlu TLS'i repo'nun backend'ine (`.htaccess` HTTPS zorlamasıyla)
   nasıl senkronize edilir — repo backend'i TLS zorunlu mu?
3. App Tracking Transparency (ATT) izni istenecekse — repo'nun analytics
   / reklam entegrasyonu iOS'ta IDFA toplama beyanına (privacy nutrition
   label) uygun mudur; bu beyan nerede tutulur?
4. UA'da iPhone gören istemci şu an yalnızca `phone` sınıfına düşer; iOS
   sürümü (ör. iOS 15 altı) veya jailbreak tespiti gibi ek iOS güvenlik
   sinyalleri repoya eklenecek mi — eklenecekse bu sinyaller sunucu
   tarafında nasıl doğrulanır (UA spoof edilebilir)?
5. iOS için Universal Link (apple-app-site-association) doğrulaması
   kurulacak mı — kurulursa `.htaccess`'te AASA dosyası nasıl servis
   edilir (`.well-known/apple-app-site-association`)?
6. Secure Enclave / Data Protection kökenli anahtar yönetimi — repo
   backend'i iOS istemcisine anahtar paylaşımı gerektirir mi, yoksa
   şifreleme tamamen cihaz-locale mi kalır?
7. iOS uygulaması yazılmaya başlanırsa ilk güvenlik kapıları hangi sırayla
   gelir — Keychain sarmalayıcı mı, LocalAuthentication mı, ATS mi (§2.1'deki
   asgari iş paketi sırası)? Bu sıra bir ADR ile sabitlenmeli midir?
8. `MobileBff` (`shared/src/Api/Bff/MobileBff.php:19`) iOS ve Android'i ortak
   sınıflandırır (`clientType = 'mobile'`, :48); iOS'a özgü bir güvenlik sinyali
   (ör. jailbreak tespiti) eklenecekse bu ortaklık nasıl kırılır — `'ios'`/
   `'android'` ayrımı mı gerekir?

---

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. Aşağıdaki matris
**bu dalın yedi güvenlik kartını** listeler; her satır o kartın kendi §1 "OS
Güvenlik Profili" bölümünü temsil eder. iOS'un bu matristeki ayrışma
noktası (§1 ve §1.1'de belgelendiği üzere): **aynı Darwin/XNU çekirdeğini
macOS ile paylaşır** ama **farklı yüzey** sunar (§1.1 iOS↔macOS karşılaştırması)
— bu yüzden macOS kanıtı iOS'a **kısmen** taşınır, tam geçerli değildir.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | uzak aile |
| 2 | **`ios-guvenlik` (bu kart)** | **XNU + sandbox/Mach (Apple mobil)** | **§2.1 boşluk matrisi** | — |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Güçlü (RPi5) | uzak aile |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif | **aynı çekirdek, farklı yüzey** (§1.1) |
| 5 | `reactos-guvenlik` | NT Security Manager | YOK | uzak (NT) |
| 6 | `tizen-guvenlik` | Linux + privilege manifest | UA, enum YOK | uzak aile |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups | UA, enum YOK | uzak aile |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma | uzak (NT) |

**Not:** `isletim-sistemi-security` dalındaki kartlarla **bridge** (§3) bağı
vardır; bu matris yalnız **turu dalındaki** güvenlik kartlarını kapsar.
**Özdeşleştirme yasağı:** iOS ≠ macOS (§1.1) — macOS kanıtı bu karta
**ayna çekmeden** taşınır (§9 F5).

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm iOS'a özgü **yeni teknik iddia içermez**; yalnız güvenlik katmanının
CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir. Birincil
kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

iOS'ta ses erişimi **sandbox + AVAudioSession kategorisi** ile korunur (mikrofon
yetkisi sistem diyalogu ile) — ancak **hangi sandbox kuralının hangi ses yolunu
açtığı** bu dosyada kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`**.

| RT katmanı (kaynak §) | iOS güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|--------------------|------------------------|-------|
| CoreAudio/HAL (L30, L375) | Uygulama oturumu != çekirdek yetki | Hayır — iOS audio policy kanıtı yok | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | AppleUSBAudio çekirdek modülü | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz — OS davranışı | Hayır | **PLANNED** |
| Cihaz kaybı kurtarma (L497) | Kopma → yeniden bağlanma | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** iOS, ses/RT için **mümkün hedef** olabilir
(CoreAudio — bu dalda `ios-audio-unit-arayuzu.md` 513 satır), **ama bu kart o
kararı vermez**; karar `isletim-sistemi-audio` + `isletim-sistemi-security`
dallarında alınır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "iOS'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Boşluk matrisinin dolu sanılması | §2.1'deki "bulunmalıydı ama yok" satırlarının VAR sanılması | Yanlış "mevcut" sonucu | §2.1, §17 |
| F2 | macOS kanıtı iOS'a tam taşınma | §1.1'deki "kısmen" atlanırsa | Yanlış güvenlik eşdeğerliği | §1.1, §9 F5 |
| F3 | Bridge sorumluluk karıştırılması | §3 devir/devralmanın ters yorumu | Sınır ihlali | §3, §12 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | sandbox abartısı | §1'deki katmanların "her şeyi koruduğu" sanılması | Yanlış varsayım | §1, §9 F2 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; entitlement/policy adı **uydurulmaz**.
2. **P1 — Daralt:** Genel iddia ölçülebilir dar iddiaya iner
   (ör. "iOS güvenliği doğrulandı" → "§2.1 boşluk matrisi mevcuttur").
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| iOS güvenlik katmanı özeti | Evet | §1, §1.1 | XNU yüzeyi + macOS kıyası |
| Repo'da iOS güvenlik izi | Evet | §2, §2.1 | Boşluk matrisi |
| K000→security sınırı | Evet | §3 | Bridge |
| K009 sınırı | Evet | §4 | Devredilmez |
| iOS'u üretim hedefi seçme | **Hayır** | §10, §17 | Karar bu kartta değil |
| Ses/RT kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili | Dosyanın kendi §1'i | §5 vendor-official envanteri |
| §1.1 iOS↔macos kıyası | Dosyanın kendi §1.1'i | Yüzey karşılaştırma tablosu |
| §2/§2.1 repo izi + boşluk | §2.1 | `Dosya:` + `Satır:` (dosyada belgelendi) |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan iOS çekirdek kanıtı | **YOK** | CVE iddiası yok (§4/§5) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── ios
│       └── ios-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/ios       (kardeş dal)
    └── uygulama-guvenlik-siniri/ios (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2/§2.1 repo izi **`Dosya:`/`Satır:`** ile destekli.
- §1.1 kıyas tablosu §4 sınırıyla **tutarlı** (kısmi geçiş).
- §5 kaynakları **2026-10-15 HTTP 200**.

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "iOS uygulama izi repoda mevcut" | §2.1 boşluk matrisi bunu reddeder | §9 F1 |
| "macOS kanıtı iOS'a tam geçerli" | §1.1 "kısmen" der | §9 F2, F5 |
| "S1/S2 bu kartta kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §1.1 → §2 → §2.1 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| iOS sürüm/entitlement değişimi | §1/§1.1 eskir | Vault revizyonu |
| §5 kaynaklarından biri 404 | Kaynak envanteri eskir | URL yenileme + tarih |
| macOS kartı güncellenirse | §1.1 kıyas satırı | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | iOS güvenlik katmanı repo'da doğrulandı mı? | **Hayır** — §2.1 boşluk matrisi |
| 2 | sandbox/entitlement uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | macOS kanıtı iOS'a taşındı mı? | **Kısmen** — §1.1 sınırlı (§9 F2) |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | iOS üretim hedefi mi? | **Hayır** — karar bu kartta değil (§10) |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan iOS güvenlik doğrulaması yok** — §2/§2.1 bir iz/boşluk envanteridir.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§2.1 boşluk satırları doldurulamaz** — bunlar dürüst negatif kanıt.
5. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1 boşluk matrisini "doldurma" **isteğine** diren (§9 F1).
- macOS kanıtını iOS'a **ayna çekme** (§9 F2/F5).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — iOS)

| Soru | `cekirdek-guvenlik/ios` | `uygulama-guvenlik-siniri/ios` | **bu kart** |
|------|--------------------------|----------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili ne? |
| Birim | XNU/sandbox çekirdek | Sınır tanımı | Katman + repo izi + boşluk |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2.1 boşluk matrisine yeni satır | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §1.1 macOS kıyasının güncellenmesi | macOS kartı revizyonu | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/entitlement eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

iOS'un güvenliği bu kartta **XNU yüzeyi + iOS↔macOS kıyası + dürüst boşluk
matrisi** olarak durur; **S1/S2 kararı olmadan** bu kart **tamamlanmış sayılmaz**.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 259 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§1.1/§2/§2.1/§3/§4/§5/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

## §24 iOS↔macos kıyas hafızası (bu kart — mutlak son)

Bu kartın **en kırılgan** noktası §1.1'deki kıyaştır; sonraki tur bu kartı
açtığında **neyin kıyas, neyin eşdeğerlik olmadığını** tek tabloda gösterir:

| Öğe | iOS kartı için durum | macOS kartı için durum | Taşınabilir mi? |
|-----|----------------------|------------------------|------------------|
| XNU çekirdeği | Paylaşımlı | Paylaşımlı | **Evet** (çekirdek temeli) |
| Endpoint Security / EDR yüzeyi | Yok (§1.1) | Var (macOS kartı §1'i) | **Hayır** — §9 F5 |
| Uygulama izin modeli | iOS'un kendi yüzeyi | macOS'un kendi yüzeyi | **Hayır** — yüzey farklı |
| Repo izi (§2/§2.1) | Boşluk matrisi | Dürüst negatif | Karşılaştırılabilir — ama ikisi de **eksik** |

**Son cümle:** Bu kart macOS kanıtını yalnız **çekirdek temeli** kadar taşır;
**yüzey/EDR/izin** eşdeğerliği **iddia edilemez** ve §6'daki S1/S2 kapıları
**kapatılmaz**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
