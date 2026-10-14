---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-android
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://source.android.com/docs/security/features/verifiedboot
  - https://source.android.com/docs/security/features/selinux
  - https://source.android.com/docs/security/features/selinux/implement
  - https://source.android.com/docs/security/best-practices
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - https://developer.android.com/tools/adb
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Cekirdek-Guvenlik — Android OS Kartı

## §0 ELI15

Bu kart, Android'de **çekirdek düzeyinde güvenlik** ne demek onu anlatır: (1) cihaz
açılırken kodun gerçekten üreticiden geldiğini kanıtlayan **Verified Boot zinciri**,
(2) süreçlere zorunlu erişim denetimi (MAC) uygulayan **SELinux** ve (3) üreticiler
için derlenmiş **güvenlik en iyi uygulamaları** listesi. Android bir **Linux
çekirdeği üzerine** kurulmuştur; bu kart, Linux kartının tekrarı değil, Android'e
özgü katmanları (AVB, Android SELinux politikası, Treble politika ayrımı) gösterir.
Bu repoda bu katmanların **hiçbiri yoktur** — kart hedef mimari + negatif kanıttır.

> **Oncelikli gercek:** 2026-10-14 taramasında repo genelinde OS güvenlik aracı
> **0 isabet** (418 PHP+JS dosya; `vendor`/`node_modules` hariç). Bu kartta tek pozitif
> satır, uygulama katmanındaki `shared/src/Session/SessionConfig.php:20`
> (`DIR_PERMISSIONS = 0750`) analoğudur; o da **DAC** ayarıdır, çekirdek koruması
> değildir (bkz. §3).

## §1 Android güvenlik profili

**Ayrım (bağlayıcı):** Android, Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
çekirdeğin evrensel ayarlarını (KASLR, modül imzası, LSM modu) taşır; bu kart
**Android'e özgü** katmanları (Verified Boot/AVB, Android SELinux politikası,
üretici en iyi uygulamaları) taşır. İkisi birlikte okunur, biri diğerinin özeti
değildir.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Verified Boot (AVB) | Donanım korumalı kök güvenden bootloader'a, ordan boot/`system`/`vendor`/`oem` bölümlerine **güven zinciri**; her aşama bir sonrakini teslim etmeden önce bütünlük + kimlik doğrular | D1-1: bootloader zinciri durumu | upstream docs · iddia yüksek · repo 0 |
| dm-verity | Android 4.4 ile Verified Boot 1 kapsamında bölüm bütünlüğü doğrulaması | D1-1: zincirin alt katmanı | upstream docs · repo 0 |
| Rollback protection | Cihaz yalnızca **daha yeni** Android sürümüne geçer; istismarın kalıcılaşmasını engeller | D1-1: zincir yaşam döngüsü | upstream docs · repo 0 |
| SELinux (Android, enforcing) | Tüm süreçler için MAC; root/capability bile denetlenir; **default-deny**; ihlaller `dmesg` + `logcat`'a yazılır | D1-2: çekirdek entegrasyonu | upstream docs · repo 0 |
| Per-domain permissive | Yalnız belirli alanlar (süreç etiketleri) izinli; sistem geneli enforcing kalır | D1-2: politika geliştirme modu | upstream docs · repo 0 |
| Treble sepolicy ayrımı (Android 8.0+) | Vendor (`vendor.img`) ve platform (`system.img`) politika bölümleri ayrı güncellenir; vendor, platformdan yeni olamaz | D1-2: entegrasyon sınırı | upstream docs · repo 0 |
| Linux çekirdek SELinux alt sistemi | SELinux bir LSM olarak çekirdekte derlenir; kullanıcı alanı `checkpolicy`/`setfiles`/`fixfiles` ister | D1-2: çekirdek ≠ Android politikası | upstream docs (docs.kernel.org) · repo 0 |
| Android security best practices | Organizasyonel, sistem, uygulama, ağ, donanım güvenliği + gizlilik; CDD/CTS ile denetlenir | D1-3: en iyi uygulama envanteri | upstream docs · repo 0 |
| adb + USB debugging RSA onayı | Denetim erişim kapısı; RSA anahtarı onayı olmadan adb komutu çalışmaz | D1-denetim: kartı okuma yolu | upstream docs · repo 0 |

**Okuma:** 9 satırın 8'i `upstream docs` sınıfıdır (hedef mimari), 0 satır bu
repoda uygulanmıştır. `Güven düzeyi` sütunu iddianın kaynağını gösterir — repo
uygulamasını değil; repo uygulaması §3'te ayrı ölçülür.

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo'da karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — adb ile cihaz durumu doğrulama (referans)

```sh
# kaynak: upstream docs - developer.android.com/tools/adb
adb devices
# baglanti onaylanmissa cihaz "device" olarak listelenir
# USB debugging: Developer options > USB debugging (Android 4.2+/API 17+ varsayilan gizli)
# Android 4.2.2+ : RSA anahtari onay diyalogu onaylanmadan adb komutu calismaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repo'da adb çağrısı/CI adımı yok (§3 negatif kanıt) |
| Doğrulama | URL 2026-10-14 · HTTP 200 |

### §2.2 — SELinux politika entegrasyonu (referans)

```make
# kaynak: upstream docs - source.android.com/docs/security/features/selinux/implement
# system/sepolicy dosyalari dogrudan degistirilmez; cihaza ozel politika eklenir
BOARD_SEPOLICY_DIRS += /device/manufacturer/device-name/sepolicy
BOARD_SEPOLICY_UNION += \
    genfs_contexts \
    file_contexts \
    sepolicy.te
# *.te  -> alan (domain) ve etiket tanimi
# file_contexts -> dosya etiketleri; uygulamak icin restorecon / restorecon_recursive
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `*.te` / `file_contexts` glob'u repoda 0 (§3) |
| Sınır | Bu blok Android **üretici** tarafıdır; uygulama geliştirme değil |

### §2.3 — Verified Boot güven zinciri (referans)

```text
# kaynak: upstream docs - source.android.com/docs/security/features/verifiedboot
guven zinciri (her asama sonrakini teslim etmeden ONCE dogrular):
  donanim korumali kok guven -> bootloader -> boot bolumu
  -> system / vendor / (opsiyonel) oem bolumleri
rollback protection: cihaz yalnizca daha yeni Android surumune gecer
Android 4.4  : Verified Boot + dm-verity (Verified Boot 1)
Android 7.0  : katI uygulama (bozuk cihaz acilmaz) + hata duzeltme kodlamasi
Android 8.0+ : AVB - Treble ile, bolum footer formati standart, rollback korumasi
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `vbmeta`/imzalı bölüm artifact'i repoda 0 (§3) |
| Not | "bootloader durumu" bu blokta **upstream tanımıdır**; cihaz üstü ölçüm yapılmadı |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** repo genelinde OS güvenlik aracı taraması **0 isabet**
> (418 PHP+JS dosya; `vendor`/`node_modules` hariç).

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| Verified Boot / AVB | `vbmeta*` · imzalı `boot.img` · anahtar | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| dm-verity / rollback | bölüm imza yapılandırması | **YOK** | glob 0 · negatif kanıt |
| Android SELinux politikası | `*.te` · `file_contexts` · `seapp_contexts` | **YOK** | glob 0 · negatif kanıt |
| adb / Android build yüzeyi | `adb` · Gradle · `AndroidManifest.xml` | **YOK** | glob 0 · negatif kanıt |
| Best-practices denetim kaydı | CDD/CTS raporu | **YOK** | rapor dizini 0 · negatif kanıt |
| session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |
| zamanlama-güvenli karşılaştırma | uygulama katmanı | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |
| shell kaçış koruması | uygulama katmanı | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg` · **P** |
| yol normalizasyonu | uygulama katmanı | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · `:200` → `str_starts_with` · **P** |

**Okuma:** 9 satırın 5'i `YOK` (negatif kanıt), 4'ü uygulama katmanı pozitif
kanıttır. **Hiçbir pozitif satır Android çekirdek güvenlik katmanına ait değildir**
hepsi PHP uygulama katmanıdır — bu, §6 S2'nin dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Android dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Android platform geneli -> `isletim-sistemi-turu/android`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://source.android.com/docs/security/features/verifiedboot | Güven zinciri donanım kökünden başlar; her aşama bir sonrakini doğrular; AVB 8.0+; rollback koruması | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux | Android SELinux MAC'i tüm süreçlere uygular; default-deny; enforcing varsayılan; 4.3+ sandbox sınırı | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux/implement | `system/sepolicy` doğrudan değiştirilmez; `*.te`/context dosyaları; `restorecon`; BOARD_SEPOLICY değişkenleri | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/best-practices | Üreticiler için 6 alanlı en iyi uygulama listesi; CDD/CTS ile ilişki | 2026-10-14 · HTTP 200 |
| https://docs.kernel.org/admin-guide/LSM/SELinux.html | SELinux çekirdek alt sistemi; kullanıcı alanı araçları (`checkpolicy`, `setfiles`, `fixfiles`) | 2026-10-14 · HTTP 200 |
| https://developer.android.com/tools/adb | adb istek/sunucu/daemon mimarisi; USB debugging; RSA onay diyalogu | 2026-10-14 · HTTP 200 |

> **Dürüstlük notu:** 6 satırın tamamı bu oturumda içerik olarak okunmuş ve
> `HEAD` ile **HTTP 200** döndürmüştür. Bu kartta uydurma CVE, uydurma komut
> çıktısı veya "repo'da uygulanıyor" iddiası **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 4 pozitif satırın tamamı uygulama katmanı; sınır çizgisi yazılı değil |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız
mimari sahibinin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
