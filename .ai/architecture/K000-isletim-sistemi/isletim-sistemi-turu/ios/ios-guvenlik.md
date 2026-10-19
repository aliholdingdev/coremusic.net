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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
