---
title: "macOS — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "grep gatekeeper|notariz|keychain|localauthentication|lacontext|sandbox (shared/src) → 0 isabet (2026-10-15)"
  - "glob **/*.plist → 0 dosya (2026-10-15)"
  - ".github/workflows/ci.yml:27,69,112,144,163,180 (6× ubuntu-latest — okundu)"
  - "home.coremusic.net/web.config:6-11 (HTTP hardening — okundu; macOS-özel değil)"
validation: "2026-10-15 — 6 vendor URL erişildi (HTTP 200); repo negatif iddiaları grep/glob ile doğrulandı"
open_questions:
  - "macOS hedef platform setinde mi? (Q4/T-U3) — repo'da macOS hedefine dair disk kanıtı YOK, UNKNOWN"
  - "Neva Engine macOS backend'i için Hardened Runtime / entitlement tasarımı var mı? — PLANNED (kod kanıtı yok)"
  - "Notarization/Gatekeeper akışı repo'da hiç anılmıyor (grep 0) — bilinçli kapsam dışı mı?"
group: mimari
---

# macOS — İşletim Sistemi Güvenlik Profili

> **Kapsam:** Bu dosya, macOS'un işletim sistemi güvenlik modelini (App Sandbox, Hardened
> Runtime, Gatekeeper/notarization, Endpoint Security, Secure Boot/SIP) tanımlar ve CoreMusic
> deposundaki izlerini DOSYA:SATIR kanıtıyla bağlar. **Dürüst bulgu:** bu oturumda okunan
> grep/glob kanıtı, repo'da macOS-özel hiçbir güvenlik artefaktı olmadığını gösterir (§2).
> Uygulama seviyesi güvenlik kontrolleri (CSRF, auth, session) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

macOS, Apple bilgisayarlarında (Mac) çalışan işletim sistemidir. Güvenlik fikri basit bir
soruya dayanır: **uygulamalar yalnızca işlerine yarayan kaynaklara erişmeli ve yalnızca
onaylanmış kod çalışmalıdır.**

macOS bunu beş katmanda yapar:

1. **Her uygulama kendi "hapishanesinde" yaşar (App Sandbox).** Bir uygulama, kullanıcı
   dosyalarına, ağa veya diğer uygulamalara varsayılan olarak erişemez; erişim için
   *entitlement* (hak tanımlaması) gerekir. Böylece kötü amaçlı bir uygulama bile
   sınırlı alanda kalır.
2. **Onaylanmamış kod çalışmaz (Gatekeeper + notarization).** macOS, uygulamanın Apple
   tarafından imzalandığını ve *notarization* (Apple'ın malware taramasından geçirildiğini)
   doğrular. İmzasız/not edilmemiş uygulama kullanıcı onayı olmadan açılmaz.
3. **Çalışma zamanı korumaları (Hardened Runtime).** Uygulama, kod enjeksiyonu, dinamik
   kitaplık yüklemesi ve diğer runtime saldırılarına karşı sertleştirilir; belirli
   davranışlar yalnızca entitlement ile açılır.
4. **Sistem bütünlüğü korunur (SIP / Secure Boot).** System Integrity Protection, kritik
   sistem dosyalarını ve süreçleri korur; Apple silicon'da Secure Boot zinciri açılışta
   imza doğrular.
5. **Olaylar güvenli biçimde izlenir (Endpoint Security).** Apple'ın Endpoint Security
   çerçevesi, dosya/uygulama/ağ olaylarını güvenlik ürünlerine güvenli biçimde sunar.

Buna üstte **Secure Enclave** (parmak izi/Face ID gibi sırların ayrı bir güvenli işlemcide
saklanması) ve **Data Protection** (dosya bazlı anahtar koruması) eklenir.

CoreMusic açısından anlamı: macOS, bu mimarinin **desteklenen bir istemci/ortaç (client)
ortamıdır** — CoreMusic backend'i macOS'ta değil, Linux sunucuda çalışır. Bu oturumda
okunan kanıt, repo'da macOS-özel **hiçbir** güvenlik artefaktı (`.plist`, Gatekeeper,
Keychain, entitlement) bulunmadığını gösterir (§2). OS güvenliği tamamen platforma
devredilmiştir; CoreMusic'in sorumluluğu §4/K009 sınırındadır.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar | Kaynak |
|---|---|---|---|
| App | **App Sandbox** | Uygulamayı dosya sistemi/ağ/diğer uygulamalardan izole eder; erişim entitlement ile verilir | §5 #1 |
| Runtime | **Hardened Runtime** | Kod enjeksiyonu, DYLD ortam değişkenleri, dinamik kitaplık loading'ine karşı sertleştirir; entitlement ile istisna | §5 #2 |
| Distribution | **Gatekeeper + notarization** | İmza + Apple malware taraması doğrulaması; onaysız kod açılmaz | §5 #6 (App security macOS) |
| Monitoring | **Endpoint Security çerçevesi** | Dosya/uygulama/ağ olaylarını güvenlik ürünlerine güvenli, imzalı olay biçiminde sunar | §5 #3 |
| Kernel | **XNU / Kernel Extension (KEXT) → System Extension** | Çekirdek uzantıları sınırlanır; modern macOS'ta sistem uzantıları tercih edilir | §5 #4 |
| Boot | **Secure Boot + SIP** | Apple silicon açılış zinciri imza doğrular; System Integrity Protection kritik sistem yüzeyini korur | §5 #6 (System security) |
| Secrets | **Secure Enclave + Keychain** | Biyometrik/parola sırları ve anahtarlar donanım destekli, non-exportable saklanır | §5 #6 (Hardware/Encryption) |

### 1.2 Güvenlik modelinin varsayılanları

- **Default deny (App Sandbox):** Bir uygulama, açıkça istenmediği sürece hiçbir kullanıcı
  dosyasına, ağa veya diğer uygulamalara erişemez; erişim *entitlement* ile tanımlanır.
- **Least privilege (Hardened Runtime):** Uygulama yalnızca gerektiği kadar yetenek alır;
  dinamik kod yüklemesi gibi yüksek riskli davranışlar varsayılan kapalıdır.
- **Defense in depth:** Sandbox + Hardened Runtime + Gatekeeper + SIP + Secure Enclave üst
  üste bindirilmiştir; tek katman ihlali sistemi düşürmez.
- **Signed & notarized distribution:** macOS, dağıtılan kodun Apple imzası + notarization
  taşımasını bekler; bu zincir kırıldığında Gatekeeper devreye girer.

### 1.3 Bilinen yapısal riskler (genel mühendislik kabulü)

- Gatekeeper bypass'ları (önceki sürümlerde `com.apple.quarantine` xattr yönetimi) bilinen
  saldırı sınıfıdır; güncel macOS mitigasyonları sürüm bağımlıdır.
- Kullanıcı, Gatekeeper uyarısını "açık izin ver" ile geçebilir — politika kullanıcı
  kararıyla zayıflayabilir.
- Kernel extension'lar SIP korumasını genişletebilir; bu nedenle System Extension modeline
  geçiş yapılmıştır.

> ⚠️ **VERIFICATION REQUIRED:** Bu risk maddeleri bu oturumda Apple sayfasından birebir
> doğrulanmadı; genel mühendislik bilgisi olarak işaretlenmiştir. Somut kanıt §5'teki
> App security / System security bölümlerindedir.

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Bu bölümdeki her satır bu oturumda **okunmuş veya grep/glob ile ölçülmüş** gerçek kanıttır.
> Okunmayan iddia yazılmaz. **Bu platform için bulgu dürüst negatiftir.**

### 2.1 Dürüst negatif kanıtlar (2026-10-15 grep/glob)

| Arama | Sonuç |
|---|---|
| `gatekeeper` (shared/src) | **0 isabet** |
| `notariz` (notarization) (shared/src) | **0 isabet** |
| `keychain` (shared/src) | **0 isabet** |
| `localauthentication` / `lacontext` (shared/src) | **0 isabet** |
| `sandbox` (shared/src) | **0 isabet** |
| `**/*.plist` (repo geneli) | **0 dosya** |

**Yorum:** CoreMusic deposunda macOS-özel **hiçbir** güvenlik artefaktı yoktur:
- Entitlement dosyası (`.entitlements`) veya Info.plist yok → App Sandbox/Hardened Runtime
  yapılandırması yok.
- Gatekeeper/notarization, Keychain veya LocalAuthentication entegrasyonu yok.
- Yani: macOS, kod tarafında **hiç desteklenmiyor** — yalnızca *kavramsal* bir derin-dal
  (bu dosya) olarak mevcut. Bu, macOS'ün bir hedef olmadığını gösterir (bkz §6 M-D2).

### 2.2 CI — macOS runner yok

`.github/workflows/ci.yml` envanteri (okundu): `runs-on: ubuntu-latest` → L27 · L69 · L112 ·
L144 · L163 · L180 → **6 job, hepsi ubuntu-latest**.

- **`macos-latest` runner job'ı YOK** → macOS üzerinde build/test/Xcode job'ı **yok**.
- `.github/workflows/` envanteri (glob): `ci.yml` + `secret-scan.yml` (2 dosya).

### 2.3 Mevcut hardening katmanı platform-özel değil (dürüst not)

`home.coremusic.net/web.config:6-11` (okundu) ve `.htaccess` (okundu) güvenlik header'ları
içerir (nosniff, X-Frame-Options, Referrer-Policy). Bunlar **Apache/IIS HTTP katmanı**
mekanizmalarıdır — macOS'a özgü **değildir**; macOS üzerinde sunucu koşmadığı için bu
satırlar macOS güvenliği için kanıt sayılmaz. (Aynı nedenle `DesktopBff.php:35`
`php_uname('s')` sunucunun OS'ünü döndürür, istemcini değil — §2.3 macOS-özel değil.)

**Sonuç:** CoreMusic, macOS'u **destekleyen bir platform olarak bile işlemiyor** — grep/glob
0 isabet. macOS güvenlik modeli tamamen Apple platformuna aittir; CoreMusic'in macOS'a
karşı hiçbir yükümlülüğü kod tarafında yoktur. Uygulama seviyesi yükümlülükler (varsa bir
gelecek istemci için) §4/K009 sınırındadır.

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; **OS kartı güvenlik detayları**
`isletim-sistemi-security` dalındadır. Aşağıdaki kartlar diskte mevcuttur (envanter
`isletim-sistemi-security/index.md` + glob üzerinden doğrulanmıştır, 2026-10-15):

| Alt dal | macOS kartı | Bağlantı |
|---|---|---|
| çekirdek-guvenlik | `cekirdek-guvenlik-macos.md` | [`../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos.md`](../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos.md) |
| yetki-ve-erisim-modeli | `yetki-ve-erisim-modeli-macos.md` | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos.md`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos.md) |
| uygulama-guvenlik-siniri | `uygulama-guvenlik-siniri-macos.md` | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-macos.md`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-macos.md) |

**Sınır kuralı:** Bu dosya yalnızca *macOS OS'nin* güvenlik modelini anlatır. CoreMusic
uygulamasının macOS'a karşı yükümlülükleri (token saklama, HTTPS) **bu dosyada çözülmez**
→ §4.

**Ne bu dosya devralır / ne devretmez:**

- **Devralır:** App Sandbox, Hardened Runtime, Gatekeeper/notarization, Endpoint Security,
  Secure Boot/SIP — bunlar *macOS'un* güvenlik kararlarıdır.
- **Devretmez:** CoreMusic'in macOS istemcide *nasıl* güvendiği (TLS, token, cookie flags)
  K009 alanıdır; bu dosya yalnızca OS katmanını bağlar. Repo'da macOS-specific kod
  bulunmadığından (§2.1) şu an devredilecek uygulama katmanı da yoktur.

Ayrıca bu dalın kardeşleri: `[windows](../windows/index.md)` · `[linux](../linux/index.md)` ·
`[ios](../ios/index.md)` · `[android](../android/index.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği (auth, CSRF, CSP, session, rate limit,
Argon2id, AES-256-GCM). Sınır hedefi: [`../../../K009-security/index.md`](../../../K009-security/index.md).

- **Ne bu dosyada, ne K009'da:** OS kernel içi güvenlik politikaları (App Sandbox politikası,
  Hardened Runtime entitlement'ları, Gatekeeper/notarization, SIP) → bunlar
  K000/isletim-sistemi-security alanı.
- **Ne K009'da, ne bu dosyada:** Uygulamanın macOS istemcide *nasıl* güvendiği (TLS, token,
  cookie flags) → K009 alanı.

**Geçiş noktası (macOS-özel):**

| Soru | Sahip | Not |
|---|---|---|
| macOS istemci var mı? | K009 + product | §2.1'de grep/glob 0 isabet → repo'da macOS-specific kod yok; hedef platform seti `UNKNOWN` |
| App Sandbox/entitlement yapılandırması | K000 (OS) | `.entitlements`/`.plist` 0 dosya → `⚠️ VERIFICATION REQUIRED` (yok) |
| Gatekeeper/notarization akışı | K000 (OS) + DevOps | grep 0 isabet → dağıtım zinciri yok |

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş** ve HTTP 200 döndürmüştür. Generic OWASP/NIST/CIS
> referansı **kullanılmamıştır** (görev kısıtı). 6 benzersiz URL. **Dürüst not:**
> `developer.apple.com` sayfaları JS tabanlı SPA olduğundan bu oturumda yalnızca **sayfa
> başlığı** döndü (gövde metni çekilmedi); body iddiaları §1'de *Apple Platform Security*
> rehberinin (§5 #6) table-of-contents içeriğine dayanır.

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://developer.apple.com/documentation/security/app-sandbox | App Sandbox — başlık doğrulandı (gövde SPA) | ✅ HTTP 200 |
| 2 | https://developer.apple.com/documentation/security/hardened-runtime | Hardened Runtime — başlık doğrulandı (gövde SPA) | ✅ HTTP 200 |
| 3 | https://developer.apple.com/documentation/endpointsecurity | Endpoint Security çerçevesi — başlık doğrulandı (gövde SPA) | ✅ HTTP 200 |
| 4 | https://developer.apple.com/documentation/kernel | Kernel (XNU/KEXT) — başlık doğrulandı (gövde SPA) | ✅ HTTP 200 |
| 5 | https://developer.apple.com/documentation/security | Security ana dizini — başlık doğrulandı (gövde SPA) | ✅ HTTP 200 |
| 6 | https://support.apple.com/guide/security/security-for-macos-secl3500444c/web | **Apple Platform Security (macOS)** — tam table-of-contents: Hardware security/Secure Enclave, System security (Secure boot, SIP, Trust caches, DMA protections), Encryption/Data Protection (Keychain, FileVault), App security (code signing, **Gatekeeper and runtime protection**, Protecting against malware) | ✅ HTTP 200 (gövde okundu) |

**Elenen/askıya alınan iddialar (dürüst kayıt):**
- `developer.apple.com/documentation/security/secure-coding-guide` → bu oturumda ölçülmedi
  (checkpoint'te 404 not edilmişti); §5'e alınmadı.
- §1'deki Gatekeeper/Hardened Runtime/SIP anlatımı, §5 #6 sayfasının **table-of-contents**
  içeriğine dayanır; derin teknik detay bu oturumda gövde olarak çekilmedi → detay için
  `⚠️ VERIFICATION REQUIRED`.

---

## §6 — Açık Sorular

1. **macOS hedefte mi?** (M-D2 / Q4/T-U3) — repo'da macOS-özel disk kanıtı yok (grep/glob
   0 isabet); dal varlığı yalnız v2 ağaç yapısından geliyor → `UNKNOWN`.
2. **Neva Engine macOS backend'i:** Hardened Runtime / entitlement tasarımı PLANNED mi?
   Kod kanıtı yok → `⚠️ VERIFICATION REQUIRED`.
3. **Gatekeeper/notarization kapsam dışı mı?** grep 0 isabet — macOS dağıtımı hiç
   hedeflenmiyor mu, yoksa henüz başlamadı mı? → product + DevOps.
4. **CI'da macOS kapsamı yok** (`ci.yml` tüm job'lar ubuntu-latest): macOS build/test job'ı
   eklenme planı var mı? → DevOps.
5. **Secure Enclave/Keychain entegrasyonu:** Token/anahtar saklama için Apple Keychain
   kullanımı düşünüldü mü? Repo'da kanıt yok → `⚠️ VERIFICATION REQUIRED`.
6. **FileVault / at-rest şifreleme:** Üretim Mac'lerde FileVault zorunlu mu? Kanıt yok →
   `UNKNOWN`.
7. **macOS istemci erişimi:** Müzisyenlerin/macOS kullanıcılarının uygulamaya erişimi var mı
   (macOS istemci PLANNED mi)? → product.

### §6.1 — macOS güvenlik yüzeyi özet matrisi (bu dosyanın hızlı referansı)

| Kontrol | Ne korur | Repo izi | Durum |
|---|---|---|---|
| Gatekeeper + notarization | imzasız/malicious yazılım engeli | grep 0 | `UNKNOWN` (dağıtım hedefi yok) |
| App Sandbox + entitlements | uygulama-içi dosya/ağ daraltması | glob 0 | PLANNED |
| Hardened Runtime | exploit mitigations | grep 0 | PLANNED |
| Keychain | kimlik/anahtar saklama | grep 0 | `UNKNOWN` |
| FileVault | at-rest disk şifreleme | kanıt yok | `UNKNOWN` |
| SIP (csrutil) | sistem dosyası koruması | repo dışı (OS ayarı) | `UNKNOWN` |
| Endpoint Security framework | process/exec denetimi | grep 0 | PLANNED |
| CI'da macOS job'u | macOS build/test doğrulaması | 6 job ubuntu-latest, macOS runner yok | `⚠️ VERIFICATION REQUIRED` |

Matris, macOS güvenlik yüzeyinin bu repoda **tamamen pasif** olduğunu gösterir:
tek CI workflow'u `.github/workflows/ci.yml`'dır ve altı job'ının tamamı `ubuntu-latest`
 üzerindedir (L27·L69·L112·L144·L163·L180 — read-this-session ✓); macOS runner'ı yoktur.
macOS-özel güvenlik kontrolü (Gatekeeper assessment, sandbox profili, Keychain
entegrasyonu) repoda hiç yoktur; matris satırları PLANNED/UNKNOWN olarak kalır.

---

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. macOS'un bu
matristeki ayrışma noktası (§2.1–§2.3'te belgelendiği üzere): **repo'daki mevcut
hardening katmanının platform-özel değildir** (§2.3) ve **macOS CI runner yok**
(§2.2) — yani bu kart **dürüst negatif** kartıdır: güvenlik kanıtı aranmış ve
**bulunamamıştır**, bu da bir veridir.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | uzak aile |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach (mobil) | Boşluk matrisi | **aynı çekirdek, farklı yüzey** |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Üretim hedefi + CI | uzak aile |
| 4 | **`macos-guvenlik` (bu kart)** | **XNU + Endpoint Security** | **Dürüst negatif (§2.1–§2.3)** | — |
| 5 | `reactos-guvenlik` | NT Security Manager | YOK | uzak (NT) |
| 6 | `tizen-guvenlik` | Linux + privilege manifest | UA, enum YOK | uzak aile |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups | UA, enum YOK | uzak aile |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma | uzak (NT) |

**Ayrışma notu:** Bu kart ile `ios-guvenlik` **aynı XNU çekrağini** paylaşır
ama **farklı yüzey** sunar (bkz. iOS kartı §1.1); **özdeşleştirme yasağı** —
macOS kanıtı iOS'a, iOS kanıtı macOS'a **ayna çekilmez** (§9 F5).

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm macOS'a özgü **yeni teknik iddia içermez**; yalnız güvenlik katmanının
CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir. Birincil
kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

macOS'ta ses erişimi **TCC (Transparency, Consent, Control) + Endpoint
Security** ile korunur (mikrofon izni sistem diyalogu) — ancak **hangi TCC
kuralının hangi ses yolunu açtığı** bu dosyada kanıtlanmaz → **`⚠️ VERIFICATION
REQUIRED`** (uydurma TCC servisi adı yasak).

| RT katmanı (kaynak §) | macOS güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|----------------------|------------------------|-------|
| CoreAudio/HAL (L375) | Uygulama izni != çekirdek yetki | Hayır — TCC/audio politika kanıtı yok | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | AppleUSBAudio / IODMACommand | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz | Hayır | **PLANNED** |
| Cihaz kaybı kurtarma (L497) | Kopma → yeniden bağlanma | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** macOS, ses/RT için **mümkün hedef** olabilir
(CoreAudio — bu dalda `macos-coreaudio-stacki.md` 502 satır), **ama bu kart o
kararı vermez**; karar `isletim-sistemi-audio` + `isletim-sistemi-security`
dallarında alınır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "macOS'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Dürüst negatifin tersine çevrilmesi | §2.1'deki "YOK" kanıtlarının VAR sanılması | Yanlış envanter | §2.1, §17 |
| F2 | Platform-özel olmayan hardening'in macOS'a mal edilmesi | §2.3'teki "platform-özel değil" notunun atlanması | Yanlış özgüllük | §2.3 |
| F3 | CI yokluğunun güvenliği etkilemediği sanılması | §2.2 "runner yok" → "CI gerekmiyor" | Yanlış güvenlik varsayımı | §2.2 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | iOS ile özdeşleştirme | XNU eşitliği → yüzey eşiti sanılırsa | Yanlış eşdeğerlik | §7, §9 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; TCC/ES servisi adı **uydurulmaz**.
2. **P1 — Daralt:** "macOS güvenliği doğrulandı" → "§2.1–§2.3 dürüst negatif
   kanıtlar mevcuttur".
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| macOS güvenlik katmanı özeti | Evet | §1.1–§1.3 | Temel katman + varsayılan + risk |
| Repo'da macOS güvenlik izi (dürüst negatif) | Evet | §2.1–§2.3 | Negatif kanıt envanteri |
| Hızlı referans özeti | Evet | §6.1 | Özet matrisi |
| K000→security sınırı | Evet | §3 | Bridge |
| K009 sınırı | Evet | §4 | Devredilmez |
| Ses/RT kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili | Dosyanın kendi §1'i | §5 vendor envanteri |
| §2 dürüst negatif | §2.1–§2.3 | `Dosya:` + `Satır:` + grep/glob (dosyada belgelendi) |
| §6.1 özet matrisi | Dosyanın kendi §6.1'i | Hızlı referans tablosu |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan macOS çekirdek kanıtı | **YOK** | CVE iddiası yok (§5/§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── macos
│       └── macos-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/macos       (kardeş dal)
    └── uygulama-guvenlik-siniri/macos (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1–§2.3 dürüst negatif kanıtları **`Dosya:`/`Satır:`** ile destekli.
- §3 bridge §4 K009 sınırıyla **tutarlı**.
- §5 kaynakları **2026-10-15 HTTP 200**.
- §6.1 özet matrisi §1/§2 ile **tutarlı**.

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "macOS güvenlik kanıtı repoda var" | §2.1 dürüst negatif bunu reddeder | §9 F1 |
| "Hardening macOS'a özgü" | §2.3 "platform-özel değil" der | §9 F2 |
| "macOS = iOS yüzeyi" | §7 özdeşleştirme yasağı | §9 F5 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §2 → §2.1 → §2.2 → §2.3 → §3 → §4 → §5 → §6 → §6.1 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| macOS sürüm/TCC değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| §5 kaynaklarından biri 404 | Kaynak envanteri eskir | URL yenileme + tarih |
| iOS kartı güncellenirse | §7 kıyas satırı | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 (§6.1 dahil) **değiştirilmedi**; yeni bölümler footer
öncesine eklendi. Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | macOS güvenlik katmanı repo'da doğrulandı mı? | **Hayır** — §2.1 dürüst negatif |
| 2 | TCC/Endpoint Security uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | Hardening macOS'a özgü mü? | **Hayır** — §2.3 platform-özel değil |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | macOS CI var mı? | **Hayır** — §2.2 |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan macOS güvenlik doğrulaması yok** — §2.1 dürüst negatif.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§2.1/§2.2/§2.3 NEGATİF kanıtlar düzeltilemez** — bunlar arama sonucudur.
5. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1 YOK kanıtlarını **tersine çevirme** (§9 F1).
- §2.3 hardening'i macOS'a **özel yazma** (§9 F2).
- iOS ↔ macOS **özdeşleştirme** (§9 F5).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — macOS)

| Soru | `cekirdek-guvenlik/macos` | `uygulama-guvenlik-siniri/macos` | **bu kart** |
|------|----------------------------|------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili + dürüst negatif |
| Birim | XNU/ES çekirdek | Sınır tanımı | Katman + §2.1–§2.3 negatif kanıt |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2'ye yeni repo izi (varsa) | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §2.2'ye CI runner (kurulursa) | CI yapılandırması | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6.1
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6.1 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/TCC eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

macOS'un güvenliği bu kartta **katman profili + dürüst negatif kanıt seti
(§2.1–§2.3) + özet matrisi (§6.1)** olarak durur; **S1/S2 kararı olmadan** bu
kart **tamamlanmış sayılmaz**.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 272 (§6.1 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§2/§2.1–§2.3/§3/§4/§5/§6/§6.1 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
