---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://support.apple.com/guide/security/welcome/web
  - https://support.apple.com/guide/security/boot-process-secac71d5623/web
  - https://support.apple.com/guide/security/system-integrity-protection-secb7ea06b49/1/web/1
  - https://support.apple.com/guide/security/operating-system-integrity-sec8b776536b/web
  - https://support.apple.com/guide/security/startup-disk-security-policy-control-sec7d92dc49f/web
  - https://support.apple.com/guide/security/securely-extending-the-kernel-in-macos-sec8e454101b/web
  - https://support.apple.com/guide/security/signed-system-volume-security-secd698747c9/1/web/1
  - https://support.apple.com/guide/security/contents-a-localpolicy-file-mac-apple-silicon-secc745a0845/web
  - https://support.apple.com/en-us/102445
  - https://developer.apple.com/documentation/security/notarizing-macos-software-before-distribution
  - https://developer.apple.com/documentation/systemextensions
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: 11 kaynak URL HEAD 200 ile dogrulandi; icerezler exa/webfetch ile okundu; repo macOS cekirdek-guvenlik araci taramasi 729 dosyada 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Çekirdek Güvenlik — macOS OS Kartı

## §0 ELI15

Bu kart, macOS'ta **açılıştan çekirdeğe kadar** uzanan güvenlik halkasını taşır:
Apple silicon secure boot (Boot ROM → LLB → iBoot zinciri, Secure Enclave
tarafından imzalanan `LocalPolicy`), System Integrity Protection (SIP),
mandatory access control, Kernel Integrity Protection (KIP), signed system
volume (SSV), kext yerine system extension modeli ve Gatekeeper + notarization.
Hepsi **işletim sistemi katmanıdır** — PHP uygulamasının OWASP yüzeyi değil.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 729 dosya, `vendor`/
> `node_modules`/`.git` hariç) macOS çekirdek-güvenlik aracına dair **0 isabet**
> vardır: `csrutil`/`spctl`/`codesign`/`kextload` çağrısı, entitlement dosyası,
> SIP/boot politikası yapılandırması **yoktur** (negatif kanıt, §3). Bu kart
> dolayısıyla **hedef mimari + negatif kanıt** kartıdır; §2'deki 3 bloğun
> tamamı `kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir komut değildir), repoda karşılığı yoktur.

Repodaki tek OS-yakın satır çifti `SessionConfig.php:20` ve `:35` (session
dizini `0750`) — o da bir **POSIX DAC** ayarıdır, secure boot/SIP halkası
**değildir** (§3). S1 ve S2 açık sorudur, bu kartta kapatılmaz (§6).

## §1 macOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Secure boot zinciri (Apple silicon) | Zincirin kökü Boot ROM'da başlar; LLB sistemle eşleşen imzalı firmware'i doğrular ve `LocalPolicy`'yi yükler; `LocalPolicy`, Secure Enclave'e özel anahtarla imzalanır (cihaza özel); anti-replay değeri eski politikanın geri yüklenmesini engeller → OS sürüm düşürme **ve** güvenlik politikası düşürme koruması | Boot zincirinin **kökü** | Yüksek (Platform Security, 2026-10-14 okundu) |
| Güvenlik politikası seviyeleri (Full / Reduced / Permissive) | Full varsayılandır; Reduced eski macOS ve **kext boot** için gerekir; Permissive kullanıcının kendi XNU çekirdeğini derleyip imzalamasına izin verir; politika değişikliği insan fiziksel erişimi gerektirir (güç düğmesiyle recoveryOS) — malware bu sinyali tetikleyemez | Politika **kapısı** | Yüksek (startup-disk policy, 2026-10-14 okundu) |
| System Integrity Protection (SIP) | Kritik sistem dosyası yollarını **salt-okunur** kısıtlar; 10.11+ yükseltmesinde varsayılan açıktır; **her sürece** uygulanır — sandbox'lı mı admin mi olduğu fark etmez; Apple silicon'da SIP'i kapatmak Permissive Security'yi gerektirir ve `csrutil` yalnız recoveryOS'tan çalışır | Süreç/dosya koruması | Yüksek (SIP sayfası, 2026-10-14 okundu) |
| Mandatory access control (MAC) | Geliştiricinin oluşturduğu, kullanıcı tarafından **aşılamayan** politikalar; DAC'tan (kullanıcı politikayı değiştirebilir) farklıdır; sandboxing, managed preferences, extensions ve SIP'i besleyen altyapıdır | etiket/poli̇ti̇ka katmanı (bu kartın MAC işareti) | Yüksek (SIP sayfası, 2026-10-14 okundu) |
| Kernel Integrity Protection (KIP) | Çekirdek başlatıldıktan sonra iBoot'un yüklediği fiziksel bellek bölgesine **yazmayı reddeder**; MMU yapılandırması ayrıcalıklı kodun korunan bölge dışından eşlenmesini ve yazılabilir eşlemeyi engeller | çekirdek bellek bütünlüğü | Yüksek (OS integrity, 2026-10-14 okundu) |
| System Coprocessor Integrity Protection (SCIP) | Kopya-işlemci firmware'i (Secure Enclave, görüntü sensörü vb.) KIP'ten ayrı, korunan fiziksel bölgeye yüklenir; boot sonrası donanım kilitlenir, yeniden yapılandırma engellenir | firmware bütünlüğü | Yüksek (OS integrity, 2026-10-14 okundu) |
| PPL / SPTM + TXM | PPL (Page Protection Layer) **macOS'te sunulmaz** — dokümanın gerekçesi: macOS "her kodu çalıştırma" üzere tasarlandı; macOS'ta sayfa tabloları **SPTM + TXM** ile korunur (daha yüksek ayrıcalık seviyesi, TXM politika uygular) | sayfa tablosu koruması — **platform farkı** | Yüksek (OS integrity, 2026-10-14 okundu — negatif iddia dokümanın kendisinde) |
| Signed system volume (SSV) | Sistem içeriği salt-okunur + kriptografik mühür (Merkle ağacı kökü, SHA-256); okuma yolunda her bayt hash'lenip metadata ile karşılaştırılır, uyuşmazlıkta veri **geri çevrilir**; boot'ta mühür doğrulanamazsa açılış durur ve yeniden yükleme istenir | sistem dosyası bütünlüğü | Yüksek (SSV, 2026-10-14 okundu) |
| Kext modeli → AuxKC | macOS 11+'da üçüncü taraf kext'ler on-demand çekirdeğe girmez; boot'ta AuxKC'ye **birleştirilir**, ölçümü LocalPolicy'ye yazılır, yeniden başlatma + Reduced Security ister; upstream: *"Kexts are no longer recommended for macOS"* | çekirdek uzantısı politikası | Yüksek (extending the kernel, 2026-10-14 okundu) |
| System extensions | Kext yerine **kullanıcı uzayında** çalışır; yüksek ayrıcalık alır ama çekirdeği paylaşmaz; aktivasyon kuralları: bundle identifier eşleşmesi, aynı Team ID, App Store dağıtımı **veya** notarization | çekirdek yüzeyini daraltan uzantı modeli | Yüksek (SystemExtensions, 2026-10-14 okundu) |
| Gatekeeper + notarization | İndirilen yazılımda Developer ID imzası doğrulanır; Catalina ve sonrası **varsayılan olarak notarization** ister; ilk açılışta kullanıcı onayı istenir; imzasız/notarize edilmemiş yazılım çalıştırmak kullanıcıyı malware'e açar | çalıştırma öncesi imza kapısı | Yüksek (Safely open apps, 2026-10-14 okundu) |

**Okuma:** 11 satırın tamamı 2026-10-14'te Apple'ın kendi dokümanlarından
okunmuştur; bunlardan 1'i (PPL) **"macOS'te yok"** negatif iddiasıdır ve kartta
"öneri" olarak değil, platform farkı satırı olarak taşınır. Kext/System
extension satırları yalnız **politika ve kullanım modelini** anlatır — çekirdek
içi yapısı `isletim-sistemi-kernel` dali sorumludur (§4).

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir komut **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — SIP politika bitlerinin yeri sözleşmesi (referans)

```sh
# kaynak: upstream docs - contents-a-localpolicy-file-mac-apple-silicon (LocalPolicy)
# Apple silicon'da SIP bitleri artik NVRAM'de degil, Secure Enclave imzali
# LocalPolicy alanlarindadir: sip0 (SIP politika bitleri), sip1 (SSV root hash
# dogrulama basarisizligina izin), sip2 (CTRR yazilabilir register kilidi),
# sip3 (boot-args NVRAM degiskeni allow listesi).
# Degisiklik: recoveryOS (1TR) ortaminda csrutil / bputil ile yapilir.
# not: cikti bicimleri bu turda okunmadi -> calistirilmaz, uydurulmaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — `csrutil`/`bputil` grep'i 0 isabet (2026-10-14) |

### §2.2 — Güvenlik politikası seviyesi geçiş koşulları (referans)

```sh
# kaynak: upstream docs - startup-disk-security-policy-control (Apple Platform Security)
# Full Security   : varsayilan - kurulum zamanindaki son yazilim bilinen yazilimdir
# Reduced Security: eski macOS + kext boot icin gerekli
# Permissive      : kullanici imzali kendi XNU cekirdegini boot edebilir;
#                   SIP'i kapatmak icin once Permissive gerekir
# politika degisikligi: recoveryOS (guc dugmesi ile) + yonetici sifresi
# 3. taraf kext'ler Reduced Security + kutu isareti ile etkinlesir (AuxKC)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — boot politikası yapılandırması / `.plist` satırı 0 |

### §2.3 — System extension aktivasyon kuralları (referans)

```sh
# kaynak: upstream docs - developer.apple.com/documentation/systemextensions
# 1) Uzanti bundle identifier'i, dosya adi (uzanti haric) ile ESLESMELIDIR
# 2) Uzanti ile ayni Team ID ile imzalanMALIDIR
# 3) App uygulamasi Mac App Store uzerinden dagitilmali VEYA notarize edilmelidir
# 4) Kurulum SystemExtensions framework'u ile calisma zamani yapilir;
#    kullanici uygulamayi silerse uzanti da silinir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `SystemExtensions`/`.appex` glob = 0 (2026-10-14) |

## §3 CoreMusic repo durumu

Tarama: repo kökü (`shared` + `home.coremusic.net` + diğer ağaçlar),
`*.php,*.js,*.css,*.sql,*.sh,*.conf,*.yml,*.yaml`, `vendor`/`node_modules`/`.git`
hariç — **729 dosya**; desen =
`sandbox-exec|Seatbelt|sandbox_init|codesign|spctl|csrutil|tccutil|notariz|com.apple.security|.entitlements|seccomp|bwrap|bubblewrap|setfacl|getfacl|capset|capget|chroot|pivot_root|EndpointSecurity|systemextensions|kextload|SMAppService`
(2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| SIP / boot politikası yapılandırması (`csrutil`, LocalPolicy, `.plist`) | grep 22 desen | **YOK** — 729 dosya / 0 isabet (negatif kanıt) |
| Kod imzası aracı çağrısı (`codesign`, `spctl`, notarization adımı) | grep | **YOK** (negatif kanıt) |
| Kext / system extension varlığı (`.kext`, `.appex`, `SystemExtensions`) | glob + grep | **YOK** (negatif kanıt) |
| Entitlement dosyası / `com.apple.security.*` anahtarı | grep | **YOK** (negatif kanıt) |
| macOS güvenlik aracı çağrısı (toplam) | grep 22 desen | **0 isabet / 729 dosya** (negatif kanıt) |
| session dizin izni sabiti — `SessionConfig.php:20` | idx 19 birebir | `public const DIR_PERMISSIONS = 0750;` — **uygulama katmanının OS'a tek teması** (POSIX DAC; secure boot/SIP halkası **değildir**) |
| session dizin oluşturma — `SessionConfig.php:35` | idx 34 birebir | `@mkdir($savePath, 0750, true);` — uygulama katmanı (mkdir izni OS'ta `umask` ile birlikte uygulanır) |
| uygulama güvenlik başlığı — `home.coremusic.net/.htaccess:10` | idx 9 birebir | `X-Frame-Options "DENY"` — **P sınıfı**: uygulama/tarayıcı katmanı (K009), OS boot güvenliği **değildir** |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js,*.css,*.sql,*.sh,*.conf,*.yml,*.yaml |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 729
($src | Select-String -Pattern 'csrutil|spctl|codesign|kextload|com\.apple\.security|SystemExtensions').Count  # beklenti: 0
```

**Okuma:** 8 satırın 5'i negatif kanıt, 2'si tek satır çiftinin pozitif DAC
kanıtı (`0750`), 1'i uygulama katmanı başlığıdır. macOS çekirdek güvenlik
yüzeyi bu repoda **hiç başlamamıştır** — bu, bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca macOS dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; macOS platform geneli -> `isletim-sistemi-turu/macos`.

Ek sınır: bu kart macOS **boot/SIP/kimlik-imza** halkasını taşır; POSIX DAC
(rwx/umask/entitlement) detayı `yetki-ve-erisim-modeli-macos`, sandbox ve
uygulama↔OS olay denetimi `uygulama-guvenlik-siniri-macos` kartlarındadır.
`0750` bir DAC ayarıdır — "secure boot koruması" diye adlandırılması abartıdır.
Kext/System extension satırları **kullanım modelidir**; çekirdek içi uygulama
mekanizması bu dala yazılmaz (X7/X8).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://support.apple.com/guide/security/welcome/web | Apple Platform Security rehberi girişi (bütün iddiaların üst kaynağı) | HTTP 200 (HEAD) · içerik okundu |
| https://support.apple.com/guide/security/boot-process-secac71d5623/web | Boot ROM → LLB zinciri; sistemle eşleşen firmware; Secure Enclave imzalı `LocalPolicy`; anti-replay; Full/Reduced/Permissive anlatımı | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/system-integrity-protection-secb7ea06b49/1/web/1 | SIP kritik yolları salt-okunur kılar; 10.11+ varsayılan açık; her sürece uygulanır; MAC teknolojisi; MAC ≠ DAC ayrımı | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/operating-system-integrity-sec8b776536b/web | KIP (çekirdek yazılamaz bölge), SCIP (kopya-işlemci firmware), **PPL macOS'te yok**, macOS'ta SPTM + TXM | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/startup-disk-security-policy-control-sec7d92dc49f/web | 3 politika seviyesi; recoveryOS kapısı; kext = Reduced Security; SIP kapatma = Permissive + `csrutil` (1TR) | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/securely-extending-the-kernel-in-macos-sec8e454101b/web | macOS 11+ kext → AuxKC birleştirme; *"Kexts are no longer recommended"*; user-approved kext loading; sistem uzantıları kullanıcı uzayında | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/signed-system-volume-security-secd698747c9/1/web/1 | SSV salt-okunur sistem volume + Merkle kök mührü; okuma yolunda hash doğrulama; uyuşmazlıkta boot durur | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/contents-a-localpolicy-file-mac-apple-silicon-secc745a0845/web | `sip0`–`sip3` alanları; `csrutil`/`bputil` ile 1TR'den değişimi; `sip2` CTRR kilidi | HTTP 200 · içerik okundu |
| https://support.apple.com/en-us/102445 | Gatekeeper; Developer ID imza denetimi; Catalina+ varsayılan notarization; ilk açılışta kullanıcı onayı | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/security/notarizing-macos-software-before-distribution | macOS yazılımının dağıtım öncesi notarization adımı (sayfa varlığı + başlık) | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/systemextensions | Kullanıcı uzayında system extension; kext'e göre güvenlik/stabilite gerekçesi; aktivasyon kuralları (bundle ID, Team ID, App Store/notarization) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `.../security/notarizing-macos-apps-before-distribution`
> → **404** (2026-10-14) · `.../kernel/extensions-in-macos` ve
> `.../kernel/extensions_in_macos` → **404** · `support.apple.com/en-us/103229`
> → HTTP 200 ama içerik *"TCP and UDP ports used by Apple software products"*
> (**konu uyuşmazlığı** + ağ kapsam dışı K013) · `support.apple.com/en-us/103231`
> → HTTP 200 ama içerik *"Send your Apple TV to Apple for service"* (**konu
> uyuşmazlığı**). Üçü de kart gövdesine alınmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (SIP/KIP/SSV/Gatekeeper/entitlement) hedef mimaride devreye alınıp alınmayacağı; kext vs system extension politikası seçimi | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (boot zinciri mi, uygulama mı) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

## §7 Çapraz Kart Matrisi (K000 içi karşılaştırma)

> **Analiz bölümü** — yeni olgu iddiası YOKTUR; hücreler diğer K000
> çekirdek-güvenlik kartlarının §1 özetleridir.

| OS kartı | Root-of-trust | Boot politikası deposu | macOS = temel satır farkı |
|---|---|---|---|
| **macOS (bu kart)** | Apple silicon: Secure Enclave imzalı LocalPolicy | sip0–sip3 alanları (§2.1) | Boot politikası donanım-analidir; kext↔dext ayrık |
| iOS | Boot ROM → LLB → iBoot → kernel; SEP ayrı zincir | İmza zinciri (§2.1 iOS kartı) | Mobil: kext yok, uygulama-içi sınır; `cekirdek-guvenlik-ios` |
| Linux | Bootloader → çekirdek | LSM + sysctl (runtime) | Donanım zorunluluğu yok; `cekirdek-guvenlik-linux` |
| Windows | UEFI Secure Boot + TPM | WDAC/App Control binary | Politika binary formu; `cekirdek-guvenlik-windows` |
| ReactOS | NT bootloader + HAL | Security Reference Monitor | Alpha; `cekirdek-guvenlik-reactos` |
| Tizen | İmzalama + SMACK | Sertifika→API matrisi | Manifest privilege; `cekirdek-guvenlik-tizen` |
| webOS | Image derleme + FOTA | Statik servis + ACG | Boot-time statik; `cekirdek-guvenlik-webos` |

**Okuma:** macOS, Linux'un tersine boot politikasını **donanım-analı bir
alan** (LocalPolicy) içine koyar ve değişimini recoveryOS'a (1TR) taşır
(§2.1/§2.2). Bu, politika değişiminin fiziksel erişim + yönetici şifresi
ile sarılması demektir — uzaktan yapılandırma yolu yoktur. CoreMusic için
sonuç: macOS güvenlik yapılandırması kurulum anında verilir, çalışma
anında değişmez (§10.1).

## §8 Ses/Zamanlama Yüzeyi Etkileşimi (Neva Engine senaryosu)

> `PLANNED` senaryo analizi. Repo `*.cpp`/`*.h` = 0 (§3). Kaynaklar:
> `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]` ·
> `Dosya: .ai/projects/NevaEngine/os/04-macos-coreaudio.md [Satır: 77]`.

### §8.1 CoreAudio hem HAL hem çatı — güvenlik sonucu

`audio-drivers.md` §1.6 (Satır: 709): macOS'ta CoreAudio hem HAL hem ses
çatısıdır; çekirdek tarafında AppleUSBAudio/IOAudioFamily vardır.
Güvenlik açısı: kext yerine **system extension** modeli (§2.3) ile ses
işleyicileri kullanıcı uzayına taşınır. Üç kural (§2.3 upstream): bundle
identifier eşleşmesi, aynı Team ID imzası, Mac App Store + notarize
dağıtım. Sonuç (analiz): macOS, ses zincirindeki üçüncü taraf kodu Linux'ta
olduğu gibi çekirdeğe değil, sarılı bir uzantıya alır.

### §8.2 Hardened Runtime × RT callback (Neva Engine)

NevaEngine `04-macos-coreaudio.md` §1 (Satır: 77) AUHAL render callback'i
(Float32 · 8 kanal) tanımlar. Hardened Runtime (§2.2) JIT hariç runtime-
exception'ları kapatır. RT callback'i için analiz: AUHAL callback'i JIT
ihtiyacı duymaz (AOT derlenmiş Objective-C++/Swift); dolayısıyla normal
olarak **hiçbir runtime-exception entitlement'ı gerekmez** — §2.2 kuralı
("yalnızca mutlak gerekli olanlar") bu bağlamda "hiçbiri" demektir.
Kod enjeksiyonu koruması (§2.2 madde 5) RT callback'ini de korur.

### §8.3 Endpoint Security × ses olayları

§2.3 upstream: olay sınıfları sürücü calistirma (process exec), mount,
fork, sinyal yukseltme. Ses zinciri bu sınıflara girmez — CoreAudio
render callback'i bir olay sınıfı değildir. Sonuç: Endpoint Security
kaydı ses gecikme bütçesine dokunmaz; ancak ses süreci başlatma (exec)
olayı yakalanır ve süreç kimliği doğrulanabilir (§10.2 senaryo).

### §8.4 TCC × korumalı kaynaklar (ses donanımı yüzeyi)

TCC (§1, `yetki-ve-erisim-modeli-macos` kartının konusu) korumalı
kaynakları yönetir. Ses donanımı TCC'nin klasik mikrofon kategorisine
girer; CoreAudio route'u bu kategoriden ayrıdır. Analiz kuralı: Neva
Engine bir **monitor/routing** uygulaması ise mikrofon TCC'si yalnız
capture yolu için istenir; playback-only senaryoda hiç istenmez. Karar
uygulama katmanında, TCC metni OS'tadır.

## §9 Başarısızlık Modları ve Risk Matrisi

| # | Başarısızlık | Konum (kart içi) | Belirti | Çıkış | CoreMusic etkisi (senaryo) |
|---|---|---|---|---|---|
| F1 | LocalPolicy doğrulama reddi | Apple silicon boot | Boot retry/recovery | 1TR'e düşüş | Uygulama hiç başlamaz |
| F2 | Politika seviyesi uyuşmazlığı | §2.2 geçiş koşulları | kext/AuxKC yüklenmez | Reduced Security gerekir | Dext/kext bağımlı özellik çalışmaz |
| F3 | System extension aktivasyon reddi | §2.3 kurallar | Uzanti kurulmaz | 4 kuraldan biri ihlal | Ses işleyici yüklenmez; fallback CoreAudio çekirdek yolu |
| F4 | Notarization/Gatekeeper reddi | §2.2 + dagitim | Uygulama açılmaz | Gatekeeper diyalogu | Süreç hiç başlamaz |
| F5 | Hardened Runtime ihlali (enjeksiyon denemesi) | §2.2 madde 5 | Süreç sonlanır (SIGKILL ailesi) | OS müdahalesi | Savunma başarılı; kayıt §15 |
| F6 | Cihaz kaybı (CoreAudio) | §4.6 eşdeğeri (audio-drivers.md) | Akış kesilir | Otomatik yeniden bağlan | audio-drivers.md §4.6 akışı |
| F7 | TCC reddi (mikrofon) | §8.4 | Capture açılamaz | TCC diyalogu/red | Playback ayrık kalır; capture senaryosu durur |

**Örüntü:** 7 modun 5'i (F1–F5) uygulama başlamadan/çalışırken OS
müdahalesiyle biter; macOS kartı da iOS gibi "kapılar önce" tarafındadır
(§9 iOS kartı F1–F3 ile aynı örüntü).

## §10 Entegrasyon Senaryoları (hedef mimari)

### §10.1 Senaryo A — Varsayılan politika + notarized uygulama

Full Security (§2.2) açık; uygulama notarize + Hardened Runtime; system
extension yok; yalnız CoreAudio/AUHAL. §8.2 sonucu: runtime-exception
entitlement'ı 0. S1'e "hayır" karşılığı. En küçük yüzey; §2.2/§2.3
hiçbir kuralı tetiklemez.

### §10.2 Senaryo B — Audio system extension (işleyici uzantısı)

§2.3'ün dört kuralıyla ses işleyici system extension'ı. Extension bundle
identifier'ı dosya adıyla eşleşmeli, aynı Team ID ile imzalı olmalı,
MAS/notarize dağıtılmalı, kurulum runtime'da. Exec olayı Endpoint
Security ile doğrulanabilir (§8.3). S1 "evet" ile değerlendirilir;
ADR (`>=088`) gerekir.

### §10.3 Senaryo C — Ölçüm/audit (Endpoint Security kaydı)

§2.3 mod 2 (bildirim); ses süreci exec olaylarını kaydeder. Ses zaman
çizelgesine dokunmaz (§8.3). Ayrı bir ölçüm katmanıdır; §15 V6'ya bağlı.

### §10.4 Senaryo karşılaştırma

| Senaryo | Politika etkisi | İmza yükü | S1 | Zamanlama riski |
|---|---|---|---|---|
| A | Yok | Notarize | hayır | Sıfır (§8.2) |
| B | Extension kuralları (4) | Team ID + MAS | evet | Sıfır (callback kullanıcı uzayı) |
| C | Yok | Ayrı uzantı | evet | Düşük (exec olayları) |

## §11 Kaynakça — envanter ve ölçüm

### §11.1 Atıf yapılan vault dosyaları (2026-10-16 ölçümü)

| Dosya | Satır | Kullanım |
|---|---|---|
| `.ai/electronic/drivers/audio-drivers.md` | 709 | §8.1 CoreAudio=HAL+çatı, §9 F6 |
| `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` | 77 | §8.2 AUHAL callback |
| `.ai/projects/NevaEngine/os/01-windows-asio.md` | 119 | §7 çapraz (Windows) |
| `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` | 92 | §7 çapraz (Linux) |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-{ios,linux,windows,reactos,tizen,webos}.md` | (bu vault) | §7 çapraz satırlar |
| `isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos.md` | (bu vault) | §8.4 sınır |

### §11.2 Upstream kanıt durumu (§2 blokları, 2026-10-14)

| Blok | Kaynak | Durum |
|---|---|---|
| §2.1 LocalPolicy alanları | contents-a-localpolicy-file (Apple) | Okundu |
| §2.2 Politika seviyesi geçişleri | startup-disk-security-policy-control | Okundu |
| §2.3 System extension kuralları | developer.apple.com/documentation/systemextensions | Okundu |
| §2.2/§2.3 çıktı biçimleri | — | `⚠️ VERIFICATION REQUIRED` (§2 notu korunur) |

## §12 Karar Ağaçları

### §12.1 macOS genişletme seçimi

```
macOS ses genişlemesi gerekiyor mu?
    │
    ├─ Hayır → Senaryo A (AUHAL, §10.1)
    │
    ├─ İşleyici uzantısı mı?
    │     EVET → Senaryo B (§10.3 kuralları 1-4)
    │              └─ 3. taraf kext mi? → Reduced + AuxKC (§2.2) — S1'e taşı
    │
    └─ Exec denetimi/audit mi?
          EVET → Senaryo C (Endpoint Security, §8.3)
```

### §12.2 Politika seviyesi seçimi (§2.2'ye bağlı)

```
Gereksinim: Standart uygulama → Full Security (varsayılan)
Gereksinim: Eski sürüm + kext        → Reduced Security
Gereksinim: İmzalanmamış kendi XNU   → Permissive (SIP kapatmak için önce bu)
Per karar: mimari sahibi + ADR (>=088); bu kartta kapatılmaz (S1)
```

## §13 Zaman Çizelgesi (vault ölçümleri)

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-10-14 | LocalPolicy / politika seviyesi / system extension upstream okundu | bu kart `validation:` |
| 2026-10-14 | Repo OS-guvenlik taraması: 0 isabet | bu kart §3 |
| 2026-10-15 | NevaEngine macOS backend ölçüldü (77 satır) | `.ai/projects/NevaEngine/os/04` |
| 2026-10-16 | Bu ek yazıldı | bu dosya |

## §14 Test ve Doğrulama Önerileri (senaryo, uygulanmadı)

| # | Doğrulama | Yöntem (referans) | Beklenen | Kanıt durumu |
|---|---|---|---|---|
| V1 | Politika seviyesi | `bputil`/ sistem bilgisi (§2.2 ortamı) | Full Security | ⚠️ 1TR/ekipman gerekli |
| V2 | Extension kuralları | Kurulum denemesi (§2.3) | 4 kural da sağlanırsa kurulum | Kod yazılınca |
| V3 | Notarization | `spctl`/Gatekeeper denemesi | Kabul | ⚠️ imzalı paket gerekli |
| V4 | Hardened Runtime statik | İmza okuması (§2.2) | runtime-exception 0 (Senaryo A) | İmzalı paketle |
| V5 | Exec olayı | Endpoint Security (§8.3) | Ses süreci exec kaydı | Kod yazılınca |
| V6 | Cihaz kaybı kurtarma | Fiziksel çıkarım | audio-drivers.md §4.6 akışı | ⚠️ donanım gerekli |

## §15 Sınır İhlali Kayıtları (negatif kontroller)

| Kontrol | Sonuç |
|---|---|
| SIP/kext kapatma komutu uyduruldu mu? | HAYIR — §2.1/§2.2 yalnız upstream kalıpları; çıktı biçimi `⚠️` |
| Repoda macOS güvenlik yapılandırması var mı iddiası? | HAYIR — §3 negatif kanıt |
| S1/S2 kapatıldı mı? | HAYIR (§6) |
| Frontmatter/`status:` değişti mi? | HAYIR (append-only) |
| ADR uyduruldu mu? | HAYIR — yalnız `>=088` eşiği anıldı |

## §16 Ek: macOS Güvenlik Mekanizması ↔ CoreMusic Bileşeni Eşlemesi

| CoreMusic bileşeni (vault) | macOS mekanizması | Eşleme türü | Sınır notu |
|---|---|---|---|
| Neva Engine RT çekirdeği (`PLANNED`) | AUHAL render callback (§8.2) | Zamanlama | Hardened Runtime uyumlu; exception 0 |
| DSP zinciri (`electronic/dsp/index`) | AU işleyicileri / vDSP | Hesaplama | Zero-allocation korunur |
| Sanal ses köprüsü (`09-open-source-virtual-audio-driver`) | CoreAudio-içi aggregate cihaz | Yönlendirme | iOS'taki yasak (ios kartı §12.1) burada yok; macOS'ta kullanıcı uzayı köprü mümkün — ayrıntı `⚠️ VERIFICATION REQUIRED` |
| Mixer web PCM (`05-neva-mixer-web-pcm-plani`) | AVAudioEngine ağ | Ağ | Ağ sandbox içinde |
| IO formatı (`10-io-format-32bit-192khz`) | AudioStreamBasicDescription | Format | 32-bit float müzakere; 192 kHz cihaza bağlı |
| Driver whitelist (audio-drivers.md §4.4) | Route politikası | Seçim | Whitelist uygulama-içi |

### §16.1 Aggregate cihaz vs sanal sürücü (macOS farkı)

iOS kartı §12.1'de kullanıcı-kaynaklı sanal audio cihazının olmadığını
söyler. macOS'ta bu yüzey farklıdır: CoreAudio aggregate cihazlar (çoklu
fiziksel cihazın birleştirilmesi) kullanıcı uzayında kurulabilir; özel
kernel sanal cihazı (kext) içinse §2.2/§2.3 kapıları geçilmelidir. Analiz:
Neva Engine macOS portunda "sanal kart" gereksinimi aggregate cihazla
karşılanabilirse kext/system extension kapıları hiç tetiklenmez (Senaryo A
kapsamında kalır). Bu satır mimari bir fark beyanıdır; doğrulama §14 V2'ye
bağlıdır.

## §17 Ek: SSS — Bu Kartın Sınırı

| Soru | Kısa cevap | Bölüm |
|---|---|---|
| SIP bitleri NVRAM'de mi? | Apple silicon'da LocalPolicy alanındadır (§2.1 upstream) | §2.1 |
| kext mi dext mi? | 3. taraf kext Reduced + AuxKC (§2.2); dext system extension (§2.3) — S1 seçimi | §10, §12.1 |
| Hardened Runtime JIT'e izin verir mi? | Yalnız runtime-exception entitlement ile (§2.2 madde 2) | §8.2 |
| Authorization Services sandbox içinde çalışır mı? | Hayır — upstream "Important" (yetki kartı §2.3) | §8.4 |
| Endpoint Security ses olaylarını yakalar mı? | Sınıflar exec/mount/fork/sinyal — render callback yok | §8.3 |
| Politika değişimi uzaktan yapılabilir mi? | Hayır — recoveryOS + yönetici şifresi (§2.2) | §10.1 |
| Repoda macOS güvenlik yapılandırması var mı? | Hayır — 0 isabet | §3 |
| macOS sanal ses cihazı mümkün mü? | Aggregate: kullanıcı uzayı olası; özel kext: kapılar (§16.1) | §16.1 |

## §18 Ek: macOS Katman Güvenlik Matrisi (bu kart §1 satırları ↔ Neva katman modeli)

> Katman modeli `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]`
> §1.1-§1.3'ten alınmıştır; macOS sütunu bu kartın §1-§2 upstream
> satırlarıyla eşlenir. Yeni olgu iddiası YOKTUR.

| Katman (audio-drivers.md §1.2) | macOS karşılığı | Güvenlik kontrolü | Kart bölümü |
|---|---|---|---|
| Uygulama (Neva Engine, PLANNED) | Uygulama süreci | Hardened Runtime + notarize | §8.2, §10.1 |
| HAL | CoreAudio (AUHAL) | Sandbox + entitlement (uygulama katmanı) | §8.1, §8.4 |
| OS ses çatısı | CoreAudio politika | Route/TCC | §8.4 |
| Kernel sürücü | AppleUSBAudio/IOAudioFamily | İmza zinciri (boot halkası) | §2.1, §8.1 |
| Donanım | Ses donanımı | Donanım root of trust (Apple silicon) | §2.1 |

### §18.1 Katman ihlali durumları (macOS gözünden)

| İhlal | audio-drivers.md §1.10 savunması | macOS ek savunması |
|---|---|---|
| HAL'ın çekirdek register'ı yazması | Yalnız sürücü yazar | Sandbox + dext modeli (§2.3) — kullanıcı uzayı register'a erişemez |
| DSP'nin tamponu devri | Kilitli protokol | Hardened Runtime bellek koruması (§2.2 madde 5) |
| İkinci uygulamanın exclusive kanalı açması | Exclusive Lock ret | CoreAudio multi-client (macOS karta göre "Evet" — §1 macOS satırı audio-drivers.md §5.4) |

**Okuma:** macOS'ta birinci katman ihlali OS tarafından da engellenir
(sandbox), Linux'ta ise yalnız LSM politikası varsa (Senaryo C). Bu,
CoreMusic'in cross-platform güvenlik eşitsizliği beyanıdır — §12.1
ağaçlarının gerekçesi.

### §18.2 Kritik bağımlılık: extension ≠ kext

| Özellik | kext | system extension (§2.3) |
|---|---|---|
| Konum | Çekirdek uzayı | Kullanıcı uzayı (sarılı süreç) |
| Etkinleşme | Reduced Security + AuxKC (§2.2) | Runtime kurulum + 4 kural (§2.3) |
| Çökme etkisi | Sistem çökmesi | Uzantı süreci çöker, sistem kalır |
| CoreMusic etkisi | S1 "evet" + ADR | Senaryo B; daha küçük yüzey |

**Karar notu (işaret):** S1 "evet" ise tercih sırası extension → kext'tir
(başarısızlık yüzeyi §18.2 sütun 4). Bu bir karar değildir; mimari sahibi
onaylar (§6).

## §19 Ek: Okuma Sırası ve Bağlantı Haritası (yeni okuyucu için)

### §19.1 Önerilen okuma sırası

1. §0 ELI15 — çerçeve (SIP/KIP/SSV/Gatekeeper/entitlement beşlisi).
2. §1 profil + §2 upstream blokları (LocalPolicy, politika seviyesi, extension).
3. §3 repo durumu — negatif kanıt; §2 bloklarının "repo karşılığı" satırları.
4. §7 çapraz matris — iOS/Linux/Windows farkları.
5. §8–§10 senaryo katmanı (Neva `PLANNED`; S1/S2 bağlantıları).
6. §16–§18 eşleme/matris — uygulama-yüzeyi ilişkisi.
7. §14–§15 ölçüm + negatif kontroller.

### §19.2 Bağlantı haritası (bu kartın işaret ettiği dosyalar)

| Hedef | Bağlantı türü |
|---|---|
| `isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-macos.md` | Sandbox/Endpoint Security derinliği |
| `isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos.md` | TCC/entitlement/Authorization Services |
| `isletim-sistemi-audio/coreaudio/` | CoreAudio stack detayı |
| `isletim-sistemi-turu/macos/macos-guvenlik.md` | Platform geneli güvenlik |
| `isletim-sistemi-kernel/surucu-ve-modul-modeli/` | kext/dext sürüm notları |
| `.ai/electronic/drivers/audio-drivers.md` [Satır: 709] | Katman modeli (§18) |

### §19.3 Uzun vadeli yenileme kuralı

macOS sürüm WWDC sonrası bu kart §2'deki üç upstream bloğu yeniden
okunur; LocalPolicy alanları (§2.1) değişirse §12.2 karar ağacı revize
edilir. Yenileme tetikleyicileri §13 zaman çizelgesine işlenir.

**Ek not (okuma):** Bu kart iOS kartının genişletilmiş masaüstü karşılığıdır;
iki kart birlikte okunduğunda "kapılar önce" örüntüsü (§9) tek seferde
görülür.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
