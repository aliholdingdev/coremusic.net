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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
