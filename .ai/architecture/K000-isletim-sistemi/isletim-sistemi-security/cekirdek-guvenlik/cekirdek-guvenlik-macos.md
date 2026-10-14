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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca macOS dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
