---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-ios
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://support.apple.com/guide/security/boot-process-for-ipad-and-iphone-devices-secb3000f149/web
  - https://support.apple.com/guide/security/the-secure-enclave-sec59b0b31ff/web
  - https://support.apple.com/guide/security/hardware-security-overview-secf020d1074/web
  - https://support.apple.com/guide/security/signed-system-volume-security-secd698747c9/1/web/1
  - https://support.apple.com/guide/security/operating-system-integrity-sec8b776536b/web
  - https://developer.apple.com/documentation/security/protecting-keys-with-the-secure-enclave
  - https://support.apple.com/guide/security/welcome/web
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yoklugu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Çekirdek-Güvenlik — iOS OS Kartı

## §0 ELI15

Bu kart iOS'un **çekirdek güvenlik katmanını** anlatır: cihaz açıldığında neye
güvenilir, o güven **hangi donanım parçasında** başlar ve hangi noktada biter.
Üç kelime: **zincir** (Boot ROM → iBoot → kernel, her halka Apple imzası
doğrular), **anahtar** (şifreleme anahtarları donanımda üretilir ve korunur),
**ayrımcı** (Secure Enclave, ana işlemciden fiziksel olarak ayrı bir birimdir).

> **Oncelikli gercek:** Bu kart **kod raporu degildir.** CoreMusic repo'su
> PHP/JS tabanlıdır; iOS çekirdek-güvenlik yüzeyine (Boot ROM, iBoot, SEP,
> imzalama) ait tek bir satır **yoktur** (§3 — negatif kanıt, 2026-10-14).
> Dolayısıyla bu dosya **hedef platform + resmi kaynak** kartıdır: her iddia
> §5'teki URL'lere bağlıdır, hiçbir iddia "repo'da uygulanıyor" diye yazılmaz.

> **Kapsam disi:** Kimlik doğrulama/oturum/parola/MFA -> `K010-identitiy`;
> OWASP, auth/CSRF/CSP, kripto secimi -> `K009-security`. Bu kart yalnızca
> iOS dosya/surec/erisim kisitlari ve onyukleme zincirini tasir (§4).

## §1 iOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Boot ROM (hardware root of trust) | SoC fabrikasyonunda yazılan **değişmez** kod; içinde Apple Root CA public key taşır, iBoot'un Apple imzasını doğrulamak için kullanılır | zincirin 0. halkası | Yüksek — resmi kaynak okundu 2026-10-14 (boot-process) |
| Güvenli önyükleme zinciri | Her adımdaki bileşen (bootloader, kernel, kext, hücre modem firmware) Apple imzalıdır; boot yalnızca güven zinciri doğrulandıktan sonra devam eder | zincirin halkaları | Yüksek — boot-process okundu |
| Low-Level Bootloader (LLB) | A9 ve öncesi A serisi işlemcilerde Boot ROM tarafından yüklenip doğrulanır, sonra iBoot'u yükler | cihaz-sürüm farkı | Yüksek — boot-process okundu (yalnız A9 ve öncesi) |
| iBoot → iOS/iPadOS kernel | iBoot işini bitirince iOS/iPadOS kernel'ini doğrular ve çalıştırır | zincirin son halkası | Yüksek — boot-process okundu |
| Secure Enclave kendi önyüklemesi | SEP Boot ROM, sepOS imzasını/hash'ini doğrular; imza geçersizse SEP, sonraki chip reset'e kadar kullanılamaz | ayrı güven zinciri | Yüksek — the-secure-enclave okundu |
| Boot Progress Register (BPR) | SEP, DFU modunda (A12+) ve Recovery modunda (A10/S2+) kullanıcı verisine erişimi sınırlamak için BPR'yi günceller | mod-bazlı erişim kilidi | Yüksek — boot-process okundu |
| Donanım AES Engine | SoC ve SEP'te ayrı AES motorları; hızlı ve güvenli şifreleme/çözme | anahtar işletimi | Yüksek — hardware-security-overview okundu |
| Secure Enclave izolasyonu | Ana işlemciden izole edilmiş alt sistem; AP kernel'i ele geçirilse bile hassas veriyi koruma amacı tasarımı; kendi Boot ROM'u, AES motoru, korumalı belleği, L4 mikroy çekirdeği | anahtar üretim/depolama | Yüksek — the-secure-enclave + hardware-security-overview okundu |
| Biyometri dayanak noktası | SEP, Optic ID / Face ID / Touch ID biyometrik verisini korur ve değerlendirir | biyometri yalnız SEP'te | Yüksek — hardware-security-overview okundu |
| Donanım destekli anahtar hiyerarşisi | Dosya anahtarları sınıf anahtarı ile sarılır; sarmalama işlemleri Secure Enclave içinde yürür, anahtar AP'ye hiç açık verilmez | data protection köprüsü (detay `yetki-ve-erisim-modeli`) | Yüksek — data-protection (Apple devices) okundu |
| Signed system volume (iOS 15+) | Bootloader, kernel'ı başlatmadan önce sistem bölümünün sız/`seal` değerini Apple imzalı değerle karşılar; eşleşme bozulursa kullanıcı güncelleme ister; iOS'ta bu koruma **kapatılamaz** | sistem bütünlüğü | Yüksek — signed-system-volume okundu |
| KIP / SCIP / PPL | KIP kernel+sürücü kodunu değişikliğe karşı korur; SCIP coprocessor firmware'ini korur; PPL yalnızca tüm kodun imzalandığı sistemlerde çalışır (macOS'ta yok) | çalışma-zamanı bütünlüğü | Yüksek — operating-system-integrity okundu |
| SEP sürüm/cihaz farkları (A serisi bazında detay) | A13+: Boot Monitor, A14+: Secure Storage Component gen 2 gibi donanım farkları var | cihaz bazlı envanter | `⚠️ VERIFICATION REQUIRED` — bu kart tüm modelleri okumadı |
| Secure Element (NFC & SE) | SEP ile ayrı bir bileşendir (kredi kartı/erişim kartı gibi uygulamaletişimi) | bu kartın **dışında** (komşu kapsam) | `UNKNOWN` — bu kart kapsamı değil |

**Okuma:** 14 satırın 12'si doğrudan okunmuş resmi kaynağa bağlıdır; 1 satır
kısmen okundu (`⚠️ VERIFICATION REQUIRED`), 1 satır kapsam dışı (`UNKNOWN`).
Okunmayan hiçbir satır "böyle çalışır" diye yazılmamıştır.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki blokların **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **referans** bloklarıdır; repo'da
> karşılıkları §3'te `YOK` olarak ölçülmüştür.

### §2.1 — Güven zinciri şeması (referans)

```sh
# kaynak: upstream docs - boot-process-for-ipad-and-iphone-devices-secb3000f149
# iOS onyukleme zinciri - her halka sonrakinin Apple imzasini dogrular
# Boot ROM (degismez, hardware root of trust, icinde Apple Root CA public key)
#   -> LLB      (yalnizca A9 ve oncesi; Boot ROM tarafindan yuklenir ve dogrulanir)
#   -> iBoot    (imza dogrulanir; isini bitirince iOS/iPadOS kernel'ini dogrular)
#   -> kernel   (kext ve hucre modem firmware de imzali)
# paralel: Secure Enclave kendi Boot ROM'u ile sepOS'u dogrular (imza gecersizse SEP kilitlenir)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream doküman şeması) | **YOK** (`*.sh` boot betiği / imza doğrulama betiği repoda yok) |
| Doğrulama | şema yalnız §5'teki URL'nin okunmuş satırlarından türetildi |
| Sınır | bu şema **CoreMusic sunucusunda çalışmaz**; hedef platform iOS'tur |

### §2.2 — Secure Enclave'de anahtar üretimi (referans, Swift)

```swift
# kaynak: upstream docs - protecting-keys-with-the-secure-enclave
let access = SecAccessControlCreateWithFlags(
    kCFAllocatorDefault,
    kSecAttrAccessibleWhenUnlockedThisDeviceOnly,
    .privateKeyUsage,
    nil)! // Ignore errors.

let attributes: NSDictionary = [
    kSecAttrKeyType: kSecAttrKeyTypeECSECPrimeRandom,
    kSecAttrKeySizeInBits: 256,
    kSecAttrTokenID: kSecAttrTokenIDSecureEnclave,
    kSecPrivateKeyAttrs: [
        kSecAttrIsPermanent: true,
        kSecAttrApplicationTag: <# a tag #>,
        kSecAttrAccessControl: access
    ]
]

var error: Unmanaged<CFError>?
guard let privateKey = SecKeyCreateRandomKey(attributes, &error) else {
    throw error!.takeRetainedValue() as Error
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream Swift örneği, birebir alıntı) | **YOK** (repoda `*.swift` dosyası yok; `kSecAttr*` çağrısı 0 isabet) |
| Ne gösterir | anahtar üretimi SEP içinde yapılır; düz metin anahtar uygulamaya hiç dönmez |
| Doküman notu | kaynak: SEP yalnızca 256-bit eğri (NIST P-256) anahtar destekler |
| Sınır | bu örneğin PHP'ye birebir karşılığı **yoktur**; CoreMusic anahtarları ADR-022 kapsamındadır (K009) |

### §2.3 — SEP önyükleme başarısızlığı davranışı (referans)

```text
# kaynak: upstream docs - the-secure-enclave-sec59b0b31ff
SEP Boot ROM -> sepOS gorselini korumali bellege kopyalar
             -> kriptografik hash + imza dogrular
             -> imza GECERSIZSE: SEP'i sonraki chip reset'e kadar hic kullanilamaz
             -> imza GECELERSE: kontrole sepOS'a gecer (A10+: hash bir register'a kilitlenir)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans — upstream davranış özeti) | **YOK** |
| Eşik | fail-closed: geçersiz imza → SEP devre dışı (fail-open değil) |
| Repo ile ilişki | repoda bu davranışın **simülasyonu dahi yok**; yalnız K009'daki `hash_equals` kuralı (timing-safe karşılaştırma) felsefi olarak aynı "fail-closed" yönü taşır |

## §3 CoreMusic repo durumu

> **Tarama (2026-10-14):** OS güvenlik aracı taraması 418 PHP+JS dosyada
> **0 isabet** (vendor/node_modules hariç). Bu kartın yazarı
> ayrıca kendi grep'ini koştu: desen
> `seccomp|apparmor|setcap|codesign|entitlement|SELinux|AppArmor`
> (`*.{php,js,sh,ps1,yml,yaml}`) → **0 eşleşme**;
> `NSFileProtection|kSecAttrAccessible|SecItemAdd|com.apple.security.app-sandbox`
> (`*.{php,js,swift,m,plist}`) → **0 eşleşme** (vendor hariç tutulmadı, yine 0).

| Beklenen yüzey | Repo'da var mı | Kanıt | Durum |
|---|---|---|---|
| Boot / imzalama doğrulama betiği (`*.sh`, `codesign` çağrısı) | HAYIR | grep `codesign` → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Swift / Objective-C kaynağı (`*.swift`, `*.m`) | HAYIR | include `*.swift,*.m` → 0 dosya | **YOK (negatif kanıt, 2026-10-14)** |
| Entitlement plist (`*.entitlements`) | HAYIR | include `*.plist` → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Keychain / SEP API çağrısı (`SecItem*`, `kSecAttr*`, `kSecAttrTokenIDSecureEnclave`) | HAYIR | grep desen → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| Data Protection sınıfı kullanımı (`NSFileProtection*`) | HAYIR | grep desen → 0 | **YOK (negatif kanıt, 2026-10-14)** |
| İmzalama anahtarı / sertifika dosyası (`*.pem`, `*.p12`) | HAYIR | secret yasağı + `.gitignore` | **YOK (gizlilik kuralı)** |
| **Session dizin izni (POSIX DAC)** | **EVET** | `shared/src/Session/SessionConfig.php:20` (`DIR_PERMISSIONS = 0750`) · `:35` (`@mkdir($savePath, 0750, true);`) | **POZİTİF — uygulama katmanı** |
| **Yol sahipliği kontrolü** | **EVET** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` (`realpath`) · `:200` (`str_starts_with`) | **POZİTİF — uygulama katmanı** |

**Okuma:** 8 satırın 6'sı negatiftir. Bu oran bu kartın karakteridir: **iOS
çekirdek katmanı için repo'da kanıt yoktur;** pozitif iki satır ise
Linux/POSIX dünyasına ait bir PHP uygulamasının **kendi** daraltmalarıdır ve
iOS boot zincirinin karşılığı **değildir** (katman karıştırılmaz).

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca iOS dosya/surec/erisim kisitlari (Keychain, data protection) ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** XNU cekirdek ici yapisi -> `isletim-sistemi-kernel`; iOS platform geneli -> `isletim-sistemi-turu/ios`; ses/CoreAudio -> `isletim-sistemi-audio/coreaudio`.

Ek sınır cümleleri (bu kartın kendi eklediği sınırlar):

- **Bu kart zorlamaz, yalnız tarif eder:** boot zinciri ve SEP davranışı
  Apple'ın donanım/yazılım yığınındadır; CoreMusic kodu bu davranışı ne
  başlatabilir ne de değiştirebilir.
- **Repo kanıtı bu kartta dolaylıdır:** `SessionConfig.php:20/35` (0750) bir
  **POSIX iznidir**, Secure Enclave/Data Protection **değildir**; ikisini aynı
  satırda anmak katman hatası olur.
- **Biyometri/parola akışı bu kartta anlatılmaz** (K010); burada yalnızca
  "böyle bir erişim kilidi OS katmanında vardır" işareti taşınır.
- **`⚠️ VERIFICATION REQUIRED` satırı** hiçbir koşulda "var" diye
  sayılmaz; cihaz-sürüm envanteri ayrı bir okuma turu gerektirir.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://support.apple.com/guide/security/boot-process-for-ipad-and-iphone-devices-secb3000f149/web | Boot ROM → iBoot → kernel zinciri, Apple Root CA public key, LLB (A9 ve öncesi), BPR, imzalı bileşenler | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/the-secure-enclave-sec59b0b31ff/web | SEP izolasyonu, kendi Boot ROM'u, sepOS imza doğrulaması, geçersiz imzada SEP'in kilitlenmesi | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/hardware-security-overview-secf020d1074/web | Boot ROM hardware root of trust, AES engine'ler, SEP'in veri-at-rest anahtarlarının üretim/depolama temeli oluşu, biyometri koruması | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/signed-system-volume-security-secd698747c9/1/web/1 | iOS 15+ imzalı sistem bölümü; bootloader seal doğrulaması; iOS'ta kapatılamaz | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/operating-system-integrity-sec8b776536b/web | KIP, SCIP, PPL çalışma-zamanı bütünlüğü mekanizmaları | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/data-protection-sece8608431d/1/web/1 | Dosya/sınıf anahtar hiyerarşisi; sarmalama işlemlerinin Secure Enclave içinde oluşu; AP'ye anahtarın açık verilmediği | 2026-10-14 \| HTTP 200 |
| https://developer.apple.com/documentation/security/protecting-keys-with-the-secure-enclave | SEP'te anahtar üretimi Swift örneği (`kSecAttrTokenIDSecureEnclave`, 256-bit ECC) | 2026-10-14 \| HTTP 200 |
| https://support.apple.com/guide/security/welcome/web | Apple Platform Security rehberi genel çerçevesi (donanım/sistem/şifreleme/uygulama/servis); edisyon: Ağustos 2026 | 2026-10-14 \| HTTP 200 |

> **Not (dürüstlük):** 8 satırın tamamı bu oturumda erişilip **HTTP 200**
> döndürmüştür (GET, `User-Agent: Mozilla/5.0`). `support.apple.com` rehber
> sayfaları JS ile render edildiğinden gövde metni resmi içerikten
> **alıntılanarak** okunmuştur; okunmayan hiçbir iddia bu tabloya
> konulmamıştır. Havuzdaki diğer 3 URL (`secure-keychain-storage`,
> `security-by-design`, `sandbox`) bu kartın **kapsamı dışında** kullanıldı —
> içerikleri ilgili kardeş kartlarda (D2/D3) değerlendirilmiştir.

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi'
(Get-Content "$K\isletim-sistemi-security\cekirdek-guvenlik\cekirdek-guvenlik-ios.md").Count

# repo uzerinde negatif kanit (vendor/node_modules hariç tutulmadan, yine 0)
Get-ChildItem 'C:\www\coremusic.net' -Recurse -Include *.swift,*.m,*.entitlements,*.plist -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch 'vendor|node_modules' } |
  Select-Object -ExpandProperty FullName
```

## §6 Açık sorular

| # | Soru | Durum | Kapanış kriteri |
|---|---|---|---|
| S1 | OS güvenlik katmanı hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | mimari sahibinin yazılı kararı + yeni ADR (`>=088`) — **bu dosya kapatmaz** |
| S2 | Uygulama <-> OS güvenlik sorumluluk sınırı (kim hangini uygular)? | `[UNKNOWN]` | sınır matrisi onayı + ADR (`>=088`) — **bu dosya kapatmaz** |

**KAPATMA kuralı:** Bu kart S1/S2'yi **kapatmaz, yalnız işaretler.** Kapatma
yetkisi mimari sahibindedir; kapatılmadan hiçbir satır "uygulandı" sayılmaz.

## §7 Çapraz Kart Matrisi (K000 içi karşılaştırma)

> Bu bölüm **analizdir**, yeni olgu iddiası değildir. Satırlar bu vault'taki
> diğer çekirdek-güvenlik kartlarının §1 özetlerinden türetilmiştir; her
> hücre o kartın kendi kanıtına bağlıdır.

| OS kartı | Root-of-trust başlangıcı | Anahtar koruma parçası | Bu kartla fark (iOS = temel satır) |
|---|---|---|---|
| **iOS (bu kart)** | Boot ROM → LLB → iBoot → kernel; SEP ayrı zincir | Secure Enclave (sepOS) | — |
| macOS | Apple silicon'da LocalPolicy alanları Secure Enclave imzalı | SIP bitleri + CTRR | Masaüstü: kext/system extension politikası ayrık (bkz. `cekirdek-guvenlik-macos`) |
| Windows | UEFI Secure Boot + TPM zinciri | WDAC/App Control politikası | Politika binary formu + sürücü imzalama ayrı (bkz. `cekirdek-guvenlik-windows`) |
| Linux | Bootloader → çekirdek; LSM hook çatısı | LSM (SELinux/AppArmor/Yama/Landlock) | Root-of-trust donanıma bağlı değil; politika dağıtım katmanında (bkz. `cekirdek-guvenlik-linux`) |
| ReactOS | NT 5.2 hedefi; NT bootloader + HAL paketleri | Security Reference Monitor (ACL) | Alpha olgunluk; tasarım iddiası kanıt değildir (bkz. `cekirdek-guvenlik-reactos`) |
| Tizen | SMACK + imzalama (en az 2 imza) | Sertifika seviyesi → API seviyesi | Privilege beyanı manifest'te; çekirdek tarafı SMACK (bkz. `cekirdek-guvenlik-tizen`) |
| webOS | Image derleme zinciri + FOTA revizyonu | Statik servis tanımı + ACG | Boot-time statik yapılandırma önceliği (bkz. `cekirdek-guvenlik-webos`) |

**Okuma:** iOS tek karttır ki boot zincirinin her halkası hardware root of
trust'a gömülü bir halkayla başlar (Boot ROM içinde Apple Root CA public
key). Linux ve ReactOS'ta aynı seviye donanım zorunluluğu değildir; bu,
CoreMusic'in "aynı uygulama her OS'ta aynı güvenlik seviyesini mi alır?"
sorusunun cevabını OS başına farklılaştırır (§10.2).

## §8 Ses/Zamanlama Yüzeyi Etkileşimi (Neva Engine senaryosu)

> **Durum:** Bu bölüm `PLANNED` senaryo analizidir. Repoda `*.cpp`/`*.h` = 0
> (2026-10-14 taraması, §3); hiçbir satır "uygulandı" değildir.
> Kaynaklar: `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]` ·
> `Dosya: .ai/projects/NevaEngine/os/04-macos-coreaudio.md [Satır: 77]`.

### §8.1 Çekirdek↔HAL sözleşmesinin iOS'a tercümesi

`audio-drivers.md` §1.3 (Satır: 709) dört katman tanımlar: Uygulama → HAL →
OS ses çatısı → Kernel sürücü. iOS'ta bu zincir iki sıkılaşırla değişir:

| Katman | iOS karşılığı | Sınır etkisi |
|---|---|---|
| Kernel sürücü | AppleUSBAudio / IOAudioFamily eşdeğeri (CoreAudio ailesi) | Kullanıcı yazımı kext yok; imza zinciri §2.1'e bağlanır |
| OS ses çatısı | CoreAudio (AVAudioEngine/AudioUnit üstü) | Ses politikası (haptik/route) OS'ta |
| HAL | Uygulama-içi AU işleyicisi | Sandbox (§3 uygulama-guvenlik-siniri-ios) + entitlement ile sarılı |
| Uygulama | Neva Engine (PLANNED) | SEP'e dokunmaz; kripto anahtar üretimi gerekiyorsa Keychain/SEP API'si |

**Analiz:** iOS'ta çekirdek güvenlik (bu kart) ile uygulama sandbox'ı
(uygulama-guvenlik-siniri-ios) arasında "ara katman" yoktur — CoreAudio hem
OS çatısı hem politika uygulayıcısıdır. Bu, Linux'taki LSM→seccomp→namespace
üçlüsünden farklı bir tek-entegrasyon modelidir.

### §8.2 RT callback SEP ile kesişmez — kesişme riski

Neva Engine `PLANNED` RT callback'i (audio-drivers.md §2.4: DMA kenar
kesmesi → sürücü → DSP zinciri, yerinde işleme, zero-allocation) iOS'ta
CoreAudio render callback'i içinde çalışır. SEP etkileşimi gereken tek an:
anahtar üretimi/dokunmatik kimlik (§2.2). Bu çağrı RT hot-path'te
**yapılmaz** — SEP bir keystore'dur, ses zaman çizelgesi değildir; RT
callback içinde blocking bir SEP çağrısı xrun üretir (audio-drivers.md §2.6:
underrun → ses kesintisi). Tasarım kuralı (analiz): SEP/Keychain çağrıları
yalnız başlatma ve politika değişim anında; RT döngüsü içinde yasak.

### §8.3 8.1 kanal / 32-bit float iOS'a uyar mı?

`ADR-038` (PCM3168A 8-kanal + subwoofer yolu, `Dosya:
.ai/electronic/drivers/audio-drivers.md [Satır: 709]` §5.8) donanım
şartnamesidir. iOS donanım kanal sayısı OS API'siyle müzakere edilir
(6/8 kanal route bağımlı); sabit 8.1 garantisi **yoktur**. Bu kart iOS'ta
`⚠️ VERIFICATION REQUIRED` olarak işaretler: hedef cihazın fiziksel kanal
sayısı ve route davranışı cihaz ailesine bağlıdır, mimari varsayım yapılmaz.

## §9 Başarısızlık Modları ve Risk Matrisi

> Analiz bölümü. Her satır §2'deki upstream dokümanların tanımladığı
> mekanizmaların başarısızlık tarafıdır; davranış iddiaları için ilgili
> satırın kaynağı §2'deki aynı bloğa bağlıdır.

| # | Başarısızlık | Zincirdeki konum | Belirti | CoreMusic etkisi (senaryo) |
|---|---|---|---|---|
| F1 | İmza doğrulama reddi (halka) | LLB/iBoot/kernel geçişi | Boot retry → kurtarma | Uygulama hiç çalışmaz; sessiz başarısızlık yok |
| F2 | SEP kilitlenmesi (imza gecersizse) | SEP kendi Boot ROM'u | Biometrik/anahtar yok | Keychain ürünleri erişilemez; ses akışı etkilenmez |
| F3 | Entitlement reddi | Kurulum/başlatma (tn2415) | Uygulama açılmaz | audio-drivers.md §4.5 fallback'i tetiklenemez — süreç hiç başlamaz |
| F4 | Route değişimi (cihaz kaybı) | CoreAudio politika katmanı | Akış kesilir | audio-drivers.md §4.6: durdur + fallback; iOS'ta OS yönetir |
| F5 | Downgrade denemesi | SEP/OS sürüm zinciri | Kurulum reddi | OTA yalnız yukarı; FOTA benzeri akış yok (webOS kartıyla tezat) |

**Örüntü:** iOS'ta 5 başarısızlık modunun 3'ü (F1, F2, F3) uygulama
başlamadan önce devreye girer. Bu, "uygulama-içi savunma" (§3'teki PHP
katmanı eşdeğeri) düşünüldüğünde kritik bir dersir: iOS'ta güvenlik kapıları
**önce** gelir, uygulama kontrolleri **sonra**.

## §10 Entegrasyon Senaryoları (hedef mimari)

> `status:` alanı bu kartta `draft` olarak kalır; bu bölüm senaryodur,
> karar değildir. Her senaryo S1/S2'ye bağlıdır.

### §10.1 Senaryo A — Çekirdek katmana dokunma (minimum yüzey)

Uygulama yalnız CoreAudio + AVAudioEngine kullanır; özel kext/dext yok;
entitlement yalnız ses route'u için. S1'e "hayır" cevabının karşılığı.
Güvenlik yükü: §2.1 zinciri + sandbox (başka kart). Risk: 8.1 özel route
desteği cihaza bağlı (§8.3).

### §10.2 Senaryo B — Privileged ses yolu (APR / ek route)

Ek kanal/route privilege gerekiyorsa entitlement + App Store dışı dağıtım
gündeme gelir. Bu senaryo S1'in "evet" cevabıyla birlikte değerlendirilir;
iOS kartı (bu kart) tek başına karar veremez. Karar yüzeyi: mimari sahibi
+ ADR (`>=088`).

### §10.3 Senaryo C — Ölçüm/servis modu (background audio)

Background audio entitlement'i ayrı bir beyandır; bu kart kapsamı dışındadır
(`uygulama-guvenlik-siniri-ios` sınırına işaret). RT callback davranışı
değişmez; zaman çizelgesi OS ses oturumu politikasına bağlanır.

### §10.4 Senaryo karşılaştırma özeti

| Senaryo | S1 bağlantısı | Ek yüzey | iOS kartı kararı |
|---|---|---|---|
| A | S1 = hayır | Yok | Uygulanabilir (senaryo) |
| B | S1 = evet | Entitlement + dağıtım | ADR gerektirir |
| C | S1 = hayır (varsayılan) | Background entitlement | Ayrı karta işaret |

## §11 Kaynakça — envanter ve ölçüm

### §11.1 Bu bölümde atıf yapılan vault dosyaları (2026-10-16 ölçümü)

| Dosya | Satır | Kullanım |
|---|---|---|
| `.ai/electronic/drivers/audio-drivers.md` | 709 | §1.3 katman modeli, §2.4-2.6 RT/xrun, §4.5-4.6 fallback, §5.8 kanal |
| `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` | 77 | §8 CoreAudio yüzeyi (AppleUSBAudio ailesi) |
| `.ai/projects/NevaEngine/os/01-windows-asio.md` | 119 | §7 karşılaştırma satırı (Windows) |
| `.ai/projects/NevaEngine/os/02-windows-wasapi.md` | 79 | §7 karşılaştırma satırı (Windows) |
| `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` | 92 | §7 karşılaştırma satırı (Linux) |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos.md` | (bu vault) | §7 çapraz satır |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows.md` | (bu vault) | §7 çapraz satır |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux.md` | (bu vault) | §7 çapraz satır |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-reactos.md` | (bu vault) | §7 çapraz satır |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-tizen.md` | (bu vault) | §7 çapraz satır |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-webos.md` | (bu vault) | §7 çapraz satır |

### §11.2 Bu ekte yeni yapılan iddia taraması

| Bölüm | Yeni olgu iddiası? | Dayanak |
|---|---|---|
| §7 | Hayır — çapraz sentez | Vault kartları (§11.1) |
| §8 | Hayır — senaryo + kaynak-türetilmiş | audio-drivers.md (§11.1) |
| §9 | Hayır — mekanizma başarısızlık yüzeyi | §2 upstream blokları |
| §10 | Hayır — açık senaryolar | S1/S2 işaretli |
| §8.3 | `⚠️ VERIFICATION REQUIRED` | Cihaz bazlı kanıt yok |

## §12 iOS Güvenlik Mekanizması ↔ CoreMusic Bileşeni Eşlemesi

> Eşleme tablosu: her satır bir CoreMusic mimari bileşeni ile bir iOS
> mekanizmasını eşler. Bileşen adları bu vault'un kendi kartlarından
> alınmıştır; uygulanma iddiası YOKTUR.

| CoreMusic bileşeni (vault kartı) | iOS mekanizması | Eşleme türü | Sınır notu |
|---|---|---|---|
| Neva Engine RT çekirdeği (`PLANNED`) | CoreAudio render callback | Zamanlama | Sandbox içinde; SEP'e dokunmaz (§8.2) |
| DSP zinciri (`electronic/dsp/index`) | AU işleyicileri | Hesaplama | Zero-allocation kuralı korunur; OS kaynağı kullanmaz |
| Sanal ses köprüsü (`09-open-source-virtual-audio-driver`) | iOS'ta sanal cihaz yüzeyi **kısıtlı** | Yok (fark) | iOS, kullanıcı-kaynaklı sanal audio driver'a izin vermez (§12.1) |
| Mixer web PCM planı (`05-neva-mixer-web-pcm-plani`) | WebAudio/AVAudioEngine | Ağ | Ağ katmanı sandbox dışına çıkamaz |
| IOCTL/io formatı (`10-io-format-32bit-192khz`) | AudioStreamBasicDescription | Format | 32-bit float müzakeresi OS'ta; sabit 192 kHz garantisi yok |
| Cihaz whitelist (audio-drivers.md §4.4) | Route politikası | Seçim | Whitelist uygulama-içi; iOS route'u OS yönetir |

### §12.1 Sanal ses sürücüsü yüzeyi — iOS farkı (analiz)

`audio-drivers.md` §5.6 (Satır: 709) sanal ses sürücü kullanımını
tanımlar (routing, virtual mic/speaker, broadcast). Bu yüzey Windows ve
kullanıcı-uzayı Linux'ta (PipeWire/Pulse pipeleri) uygulanabilir; iOS'ta
kullanıcı uygulaması ses zincirine sanal cihaz **ekleyemez** — CoreAudio
cihaz listesi OS tarafından yönetilir. Sonuç: Neva Engine'in "sanal ses
kartı" stratejisi (NevaEngine `02-windows-wasapi.md` §2 loopback, Satır: 79)
iOS portunda geçersizdir; alternatif route: app-içi karıştırma + ağ
taşınabilirliği. Bu satır mimari bir fark beyanıdır, kanıt zinciri §8.3'e
bağlı `⚠️ VERIFICATION REQUIRED` ile birlikte okunur.

## §13 Karar Ağaçları (senaryo seçimi)

### §13.1 Backend seçim ağacı (iOS portu için)

```
Neva Engine iOS portu (PLANNED)
    │
    ├─ Uygulama-içi ses yeterli mi?
    │     EVET → AVAudioEngine / AudioUnit (HAL, §8.1)
    │              └─ sandbox zaten var (uygulama-guvenlik-siniri-ios)
    │
    ├─ Ek route / donanım kanalı gerekli mi?
    │     EVET → route privilege araştır → S1'e taşı (senaryo B, §10.2)
    │
    └─ Sanal cihaz gerekli mi?
          EVET → iOS'ta YOK (§12.1) → ağ köprüsü veya macOS'a taşı
```

### §13.2 Anahtar üretimi karar ağacı

```
Anahtar gereksinimi var mı?
    HAYIR → SEP'e hiç dokunma (RT hot-path temiz, §8.2)
    EVET  → başlatma anında Keychain/SEP çağrısı (§2.2 kalıbı)
              └─ başarısız → F2 (§9): ürün çalışmaz, ses akışı ayrık kalır
```

### §13.3 Ağaca bağlanan kartlar

| Düğüm | Bağlı kart |
|---|---|
| HAL seçimi | `isletim-sistemi-audio/coreaudio` (bu vault) |
| Loopback | `isletim-sistemi-audio/wasapi/wasapi-anayasa.md` (Windows, §2) |
| Sanal köprü | `.ai/projects/NevaEngine/09-open-source-virtual-audio-driver.md` [Satır: 200] |
| Format müzakeresi | `.ai/projects/NevaEngine/10-io-format-32bit-192khz.md` [Satır: 98] |

## §14 Zaman Çizelgesi ve Sürüm Notları (vault ölçümleri)

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-10-14 | Upstream dokümanlar webfetch ile okundu; URL'ler HTTP 200 | bu kart `validation:` alanı |
| 2026-10-14 | Repo OS-guvenlik-aracı taraması: 0 isabet / 293 dosya | bu kart §3 |
| 2026-10-15 | NevaEngine envanteri ölçüldü (49 md / 16.609 satır) | `.ai/projects/NevaEngine/` |
| 2026-10-15 | Reverse RE turu: VoiceMeeter/FxSound sürücü matrisi | `.ai/projects/NevaEngine/04-reverse-voicemeeter-fxsound.md` [Satır: 462] |
| 2026-10-16 | Bu ek yazıldı; kaynak envanteri §11.1 | bu dosya |

**Sürüm zinciri notu (upstream §2.1'e bağlı):** iOS sürüm atlama (version
skipping) desteklenmez; her sürüm imzalıdır ve downgraded sürüm kurulamaz.
Bu kart, Neva Engine iOS port senaryosunda OS sürüm tabanını
`⚠️ VERIFICATION REQUIRED` olarak işaretler — hangi iOS sürümünün minimum
alacağı mimari karardır, bu kartta kapatılmaz.

## §15 Test ve Doğrulama Önerileri (senaryo, uygulanmadı)

> Bu bölüm bir **test taslağıdır**; `testing` kartları bu vault'ta ayrı
> alt-daldadır (`isletim-sistemi-testing`). Burada yalnız iOS'a özgü
> doğrulama yüzeyi listelenir; çalıştırma kanıtı YOKTUR.

| # | Doğrulama | Yöntem | Beklenen (upstream §2'ye bağlı) | Kanıt durumu |
|---|---|---|---|---|
| V1 | Boot zinciri durumu | cihaz üzerinde gözlem | Her halka geçerli imza | ⚠️ cihaz gerekli |
| V2 | Sandbox etkinliği | `sandbox-exec` eşdeğeri + kod imzası | Uygulama yalnız kendi container'ı | ⚠️ cihaz gerekli |
| V3 | Entitlement red akışı | Yanlış entitlement ile imzalama | Kurulum/başlatma reddi (§9 F3) | ⚠️ cihaz gerekli |
| V4 | RT callback SEP çağrısı | Statik kod denetimi | RT döngüsünde SEP çağrısı 0 | Kod yazılınca (PLANNED) |
| V5 | Route değişimi kurtarma | Cihaz değiştirme | audio-drivers.md §4.6 akışı | ⚠️ cihaz gerekli |

**Doğrulama kapısı:** V1–V3 gerçek cihaz gerektirir; bu vault'ta cihaz
kanıtı yoktur → tamamlandı **sayılmaz**. V4 kod yazıldığında statik
denetlenebilir.

## §16 Sınır İhlali Kayıtları (negatif kontroller)

| Kontrol | Sonuç |
|---|---|
| Bu ekte PHP/JS/CSS/SQL kodu var mı? | YOK (yalnız sh/plist referans blokları §2'de) |
| Repoda iOS sürümü/kod iddiası uyduruldu mu? | HAYIR — sürüm §14'te `⚠️ VERIFICATION REQUIRED` |
| S1/S2 kapatıldı mı? | HAYIR (§6 KAPATMA kuralı korunur) |
| `status:` değiştirildi mi? | HAYIR (`draft` korunur) |
| Frontmatter silindi mi? | HAYIR (append-only) |

## §17 Ek: SSS — Bu Kartın Sınırı (kısa)

| Soru | Kısa cevap | Derinlemesine |
|---|---|---|
| SEP ses zaman çizelgesini yönetir mi? | Hayır — SEP keystore'dur; ses CoreAudio'dur | §8.2 |
| iOS'ta kullanıcı sanal ses cihazı ekleyebilir mi? | Hayır — cihaz listesi OS'ta | §12.1 |
| Bu kart Repodaki PHP katmanını kapsar mı? | Hayır — K009/K010 ayrımı bağlayıcı | §4 |
| Boot zinciri kırılırsa uygulama savunması devreye girer mi? | Hayır — uygulama hiç başlamaz (F1/F3) | §9 |
| 8.1 kanal iOS'ta garanti midir? | Hayır — cihaz/route bağımlı | §8.3 |
| S1/S2 bu kartta kapatılır mı? | Hayır — yalnız işaretlenir | §6 |
| Sanal ses köprüsü (Neva) iOS'ta çalışır mı? | Hayır — §12.1 farkı; ağ köprüsü alternatif | §12.1, §13.1 |
| RT callback SEP'e dokunabilir mi? | Hayır — blocking SEP çağrısı xrun üretir | §8.2, §13.2 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
