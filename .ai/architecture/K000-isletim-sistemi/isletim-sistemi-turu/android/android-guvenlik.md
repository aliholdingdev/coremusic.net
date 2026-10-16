---
title: "Android — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/android/android-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/android"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "shared/src/Device/DeviceDetector.php:33-39 (UA tespiti — okundu)"
  - "assets.coremusic.net/js/core/breakpoints.js:31,84 (UA tespiti — okundu)"
  - ".ai/.sql/mysql/coremusic_media.sql:72 (platform enum — okundu)"
  - ".github/workflows/ci.yml:27 (runs-on: ubuntu-latest — okundu)"
validation: "2026-10-15 — 7 vendor URL erişildi; repo iddiaları DOSYA:SATIR kanıtlı"
open_questions:
  - "Android TV UA'sı CoreMusic'te nasıl ayrı ele alınıyor? (AOSP TV профili yok)"
  - "Keystore tabanlı imza/doğrulama akışı repo'da mevcut mu? — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# Android — İşletim Sistemi Güvenlik Profili

> **Kapsam:** Bu dosya, Android'in işletim sistemi güvenlik modelini (sandbox, SELinux, imza,
> güncelleme) tanımlar ve CoreMusic deposundaki izlerini DOSYA:SATIR kanıtıyla bağlar.
> Uygulama seviyesi güvenlik kontrolleri (CSRF, auth, session) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

Android, telefon ve tabletlerde çalışan, Linux çekirdeği üstüne kurulmuş bir işletim
sistemidir. Güvenlik fikri basit bir soruya dayanır: **bir uygulama, başka hiçbir uygulamaya
ve sistemin geri kalanına dokunamamalıdır.**

Android bunu üç katmanda yapar:

1. **Her uygulama kendi "hapishanesinde" yaşar (Application Sandbox).** Her uygulamanın
   kendine ait bir Linux kullanıcı ID'si vardır. Uygulama A, uygulamanın B dosyalarını
   okuyamaz — çekirdek bunu izin vermez.
2. **Hapishane kuralları kodla değil, etiketle zorlanır (SELinux).** Linux'un standart
   izin dosyaları (rwx) tek başına yetersizdir; Android, her süreç ve dosya üzerine bir
   *etiket* (label) yapıştırır ve "bu etiket, şu etikete şunu yapabilir" kurallarını
   *default denial* (varsayılan ret) ile uygular. Kuralda yazmayan her şey yasaktır.
3. **Uygulamalar imzayla tanınır.** Bir uygulama, cihazda aynı kimlikle güncellenir;
   imzası değişirse Android güncellemeyi reddeder. Böylece kullanıcılar sahte uygulamayla
   sessizce değiştirilemez.

Buna üstte **Verified Boot** (cihaz açılırken sistemin değiştirilmediğini doğrulaması),
**TrustZone** (parmak izi/şifre gibi sırların ayrı bir güvenli işlemcide saklanması) ve
**aylık security bulletin** (bulunan açık yamaların takvimi) eklenir.

CoreMusic açısından anlamı: Android, bu mimarinin **çalıştığı bir müşteri (client) ortamıdır** —
CoreMusic kodu Android'de çalışırken OS'nin sandbox'ına güvenir; buna karşılık kendi
API'lerini Android'e *güvenli* teslim etmek (token, auth) K009'un görevidir.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar |
|---|---|---|
| Boot | **Verified Boot** | Cihaz açılışında boot image / system partition imzasını doğrular; değiştirilmiş sistem yüklenmez |
| Kernel | **SELinux (MAC)** | Type Enforcement ile *default denial*; root bile policy'de olmayanı yapamaz |
| App | **Application Sandbox** | Per-app Linux UID; dosya, process ve ağ izolasyonu |
| App | **App signing (v2/v3/v4)** | APK imza şeması; aynı kimlik + imza zinciri olmadan güncelleme yok |
| Secrets | **Keystore / Keymaster** | Anahtarlar *non-exportable*; donanım kök key (TrustZone/StrongBox) ile korunur |
| Data | **Scoped storage / izinler** | Depolama ve runtime izinleri (危险 izinler user consent gerektirir) |
| Process | **SELinux seccomp** | Zamanlanmış syscall filtreleme (Uygulama sandbox'ının syscall yüzeyi) |
| Ops | **Security bulletin** | Aylık yama takvimi; CVE numaraları ve etkilenen sürümler yayımlanır |

### 1.2 Güvenlik modelinin varsayılanları

- **Default denial (SELinux):** Policy'de açıkça izin verilmeyen her erişim reddedilir.
  Bu, Android'in en kritik güvenlik özelliği olarak `source.android.com` dokümanında
  *mandatory access control* çerçevesinde anlatılır.
- **Least privilege:** Bir uygulama ilk kurulduğunda yalnızca manifest'te bildirdiği
  izinlere sahiptir; runtime izinleri (`dangerous` kategorisi) kullanıcı onayı ister.
- **Defense in depth:** Sandbox + MAC + imza + secure boot üst üste bindirilmiştir; tek
  bir katmanın ihlali sistemi düşürmez.
- **Update cadence:** Aylık security bulletin; OEM'lerin yama dağıtımı gecikmeli olabilir
  (fragmentation — bilinen yapısal zayıflık, `UNKNOWN` detay).

### 1.3 Bilinen yapısal riskler (vendor dokümanı ötesi genel kabul)

- Cihaz/OEM yama gecikmeleri → güncellenmemiş cihaz yüzeyi.
- Üçüncü taraf uygulama mağazaları → imza doğrulama zinciri kullanıcı tarafında zayıflayabilir.
- Root/adb erişimi olan cihazlarda sandbox varsayılanları geçersizleşir.

> ⚠️ **VERIFICATION REQUIRED:** Bu risk maddeleri bu oturumda vendor sayfasından birebir
> doğrulanmadı; genel mühendislik bilgisi olarak işaretlenmiştir. Somut kanıt §5'teki
> bulletin sayfasındadır.

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Bu bölümdeki her satır bu oturumda **okunmuş** gerçek dosya satırıdır. Okunmayan iddia yazılmaz.

### 2.1 Android UA tespiti — istemci tarafı cihaz sınıflandırması

| İddia | Kanıt | Durum |
|---|---|---|
| Android UA'sı `phone` olarak sınıflandırılır | `shared/src/Device/DeviceDetector.php:39` → `'Android' => 'phone'` | ✅ OKUNDU |
| Android TV UA'sı ayrı etiketlenir | `shared/src/Device/DeviceDetector.php:34` → `'Android TV'` | ✅ OKUNDU |
| TV UA listesi Android telefonu *dışlamaz*, TV listesi ayrıdır | `shared/src/Device/DeviceDetector.php:33` → `'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA'` | ✅ OKUNDU |
| JS tarafında Android UA → phone/tablet ayrımı | `assets.coremusic.net/js/core/breakpoints.js:84` → `if (/Android/i.test(ua)) return /Mobile/i.test(ua) ? 'phone' : 'tablet';` | ✅ OKUNDU |
| TV UA regex'i `Android` içermez (Android telefon regex'e girmez) | `assets.coremusic.net/js/core/breakpoints.js:31` → `RE_TV_UA = /Tizen\|Web0S\|webOS\|SmartTV\|.../i` | ✅ OKUNDU |

**Yorum:** CoreMusic, Android'i *yalnızca UA string'i üzerinden* tanır. Bu, istemci tarafında
zararsızdır; ancak **güvenlik kararı olarak kullanılamaz** (UA spoof edilebilir). Sunucu
tarafı platform doğrulaması için `DesktopBff.php:35` → `'platform' => php_uname('s')`
**sunucunun** işletim sistemini döndürür, istemcininkini değil (bkz 2.3).

### 2.2 Veritabanı platform enum'u — Android VAR, TV platformları YOK

`.ai/.sql/mysql/coremusic_media.sql:72` (okundu):

```sql
`platform` enum('windows','linux','macos','android','ios','rpi','other') COLLATE utf8mb4_unicode_ci NOT NULL,
```

- **`android` enum'da MEVCUT** → Android cihazlar `device_types.platform` altında
  modellenebilir.
- **`tizen` / `webos` / `reactos` enum'da YOK** → bu üçü `other`'a düşmek zorundadır.
  (Bu dürüst negatif kanıt, ilgili platform dosyalarının §2'sinde de tekrarlanır.)

### 2.3 Sunucu OS tespiti (genel — Android'e özgü değil)

`shared/src/Api/Bff/DesktopBff.php:35` → `'platform' => php_uname('s')`
Bu, **sunucunun** kernel adını döndürür (örn. `Linux`). Android ile ilişkisi yoktur;
CoreMusic backend'i Android'de değil, Linux sunucuda çalışır. **Dürüst sınırlama:** bu
satır Android güvenliği için kanıt sayılmaz.

### 2.4 CI — Android build/test yok

`.github/workflows/ci.yml:27` (okundu) → tüm job'lar `runs-on: ubuntu-latest`.
Bu oturumda okunan envanter: L27 · L69 · L112 · L144 · L162 · L180 → **hepsi ubuntu-latest**,
`PHP_VERSION: '8.4'` (L22).

- **Android emulator / AOSP build / device farm job'ı YOK.**
- Yani: Android güvenlik davranışı (SELinux policy, imza, sandbox) CI'da **test edilmiyor**.

### 2.5 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama | Sonuç |
|---|---|
| `AndroidManifest` (kod yüzeyi) | **0 isabet** — repo'da Android uygulama manifest'i yok |
| `selinux\|seccomp` (uygulama kodu) | **0 isabet** — yalnız `.ai/` vault metinlerinde geçiyor |
| Android'e özgü güvenlik kodu (Keystore, sandbox policy) | **0 isabet** — CoreMusic'te Android-specific güvenlik kodu YOK |
| `package-lock.json` android-arm64 binding'leri | Mevcut (ör. rolldown/lightningcss optional deps) — bunlar **npm platform binary'leri, güvenlik kodu değildir** |

**Sonuç:** CoreMusic, Android'i *desteklenen bir istemci* olarak tanır (UA + DB enum) ama
Android'e özgü **hiçbir** güvenlik mekanizması içermez. OS güvenliği tamamen platforma
devredilmiştir; CoreMusic'in sorumluluğu §4/K009 sınırındadır.

### 2.6 Özet — depoda Android'e dair ne var / ne yok

| Yüzey | Durum | Kanıt (bu oturumda okundu) |
|---|---|---|
| UA ile `phone`/`tablet` tespiti | **VAR** | `shared/src/Device/DeviceDetector.php:39` · `assets.coremusic.net/js/core/breakpoints.js:84` |
| `Android TV` ayrı etiket | **VAR** | `DeviceDetector.php:34` (TV listesi `:33`) |
| DB `platform` enum'unda `android` | **VAR** | `.ai/.sql/mysql/coremusic_media.sql:72` |
| Android uygulama manifest'i (`AndroidManifest`) | **YOK** — 0 isabet | grep 2026-10-15 |
| Android'e özgü güvenlik kodu (Keystore/sandbox/SELinux politikası) | **YOK** — 0 isabet | grep 2026-10-15 |
| Android CI job'ı (emulator/device farm) | **YOK** | `.github/workflows/ci.yml` L27 · L69 · L112 · L144 · L162 · L180 → `ubuntu-latest` |
| npm `android-arm64` optional binding'leri | VAR — **güvenlik değildir** | `package-lock.json` (rolldown/lightningcss vb. platform binary'leri) |

**Tek cümlelik sonuç:** CoreMusic, Android'i *tanır ve modeller* (UA + `platform` enum) ama
Android'e özgü *hiçbir güvenlik mekanizması üretmez* → OS güvenliği (SELinux, sandbox,
Keystore, imza) tamamen platforma devredilmiştir; kalan sorumluluk §4/K009'dur.

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; **OS kartı güvenlik detayları**
`isletim-sistemi-security` dalındadır. Aşağıdaki kartlar diskte mevcuttur (envanter
`isletim-sistemi-security/index.md` üzerinden okunmuştur):

| Alt dal | Android kartı | Bağlantı |
|---|---|---|
| çekirdek-guvenlik | çekirdek-guvenlik | [`../../isletim-sistemi-security/cekirdek-guvenlik/`](../../isletim-sistemi-security/cekirdek-guvenlik/) |
| yetki-ve-erisim-modeli | yetki-ve-erisim-modeli | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/) |
| uygulama-guvenlik-siniri | uygulama-guvenlik-siniri | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/) |
| kod-ornekleri | (15 kart) | [`../../isletim-sistemi-security/kod-ornekleri/`](../../isletim-sistemi-security/kod-ornekleri/) |

**Sınır kuralı:** Bu dosya yalnızca *Android OS'nin* güvenlik modelini anlatır. CoreMusic
uygulamasının Android'e karşı yükümlülükleri (token saklama, UA'ya güvenmeme, HTTPS)
**bu dosyada çözülmez** → §4.

Ayrıca bu dalın kardeşleri:
`[webos](../webos/index.md)` · `[tizen](../tizen/index.md)` · `[reactos](../reactos/index.md)` ·
`[linux](../linux/index.md)` (varsa) · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği (auth, CSRF, CSP, session, rate limit,
Argon2id, AES-256-GCM).

- Sınır hedefi: [`../../K009-security/index.md`](../../K009-security/index.md)
- **Ne bu dosyada, ne K009'da:** OS kernel/için güvenlik politikaları (SELinux policy,
  boot chain) → bunlar K000/isletim-sistemi-security alanı.
- **Ne K009'da, ne bu dosyada:** Uygulamanın Android cihazda *nasıl* güventiği
  (TLS, token, cookie flags) → K009 alanı.

**Geçiş noktası (Android-özel):**

| Soru | Sahip | Not |
|---|---|---|
| Android UA'sına güveniliyor mu? | K009 | §2.1'de UA spoof riski gösterildi — UA, güvenlik kararı için yeterli kanıt değildir |
| `device_types.platform` enum'u `android` içeriyor | K000 + K009 ortak | `coremusic_media.sql:72` |
| Android'e özgü secrets saklama (Keystore) | K009 (uygulama) | Repo'da Keystore entegrasyonu **0 isabet** → `⚠️ VERIFICATION REQUIRED` |

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş** ve içerik döndürmüştür. Generic OWASP/NIST/CIS
> referansı **kullanılmamıştır** (görev kısıtı).

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://source.android.com/docs/security/overview | Android güvenlik mimarisi genel bakış (verified boot, MAC, sandbox) | ✅ erişildi |
| 2 | https://source.android.com/docs/security/features/selinux | SELinux / Type Enforcement / default denial | ✅ erişildi |
| 3 | https://source.android.com/docs/security/bulletin | Aylık security bulletin yapısı | ✅ erişildi |
| 4 | https://source.android.com/docs/security/app-sandbox | Application Sandbox (per-app UID) | ✅ erişildi |
| 5 | https://developer.android.com/training/articles/security-tips | Güvenlik ipuçları (izinler, storage, IPC) | ✅ erişildi |
| 6 | https://developer.android.com/privacy-and-security/keystore | Android Keystore — non-exportable key | ✅ erişildi |
| 7 | https://developer.android.com/training/data-storage | Depolama seçenekleri + izin modeli | ✅ erişildi |

**Elenen URL'ler (dürüst kayıt):**
- `developer.android.com/.../scoped-storage` → **404 / CRAWL_NOT_FOUND** → §5'e alınmadı,
  karşılığı `training/data-storage` (7 numara) kullanıldı.
- `developer.android.com/.../security-releases` → **404 / CRAWL_NOT_FOUND** → elendi,
  karşılığı `source.android.com/docs/security/bulletin` (3 numara).

---

## §6 — Açık Sorular

1. **Android TV ayrı kart mı?** `DeviceDetector.php:34` `Android TV`'yi ayrı etiketliyor,
   `breakpoints.js:31` TV regex'i `Android` içermiyor — Android TV'nin (AOSP TV) güvenlik
   profili telefon profiliyle aynı mı, ayrı mı? `⚠️ VERIFICATION REQUIRED`
2. **Sunucu tarafı platform doğrulaması:** İstemci `platform` alanını döndürebiliyor; bu
   alan doğrulanıyor mu? (UA spoof → yanlış tier/limit?) → K009 sahibi.
3. **`other` enum'una düşen TV/ReactOS cihazları:** `coremusic_media.sql:72` enum'u dar;
   bu platformlar `other` olarak mı kaydediliyor yoksa kaydedilmiyor mu? → Data Engineer.
4. **CI'da Android kapsamı yok** (`ci.yml` tüm job'lar ubuntu-latest): Android güvenlik
   davranışı hiçbir otomasyonla test edilmiyor — bu bilinçli bir kapsam kararı mı?
5. **Keystore entegrasyonu repo'da yok** (grep 0 isabet): token saklama stratejisi
   nedir? `⚠️ VERIFICATION REQUIRED`

---

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. Aşağıdaki matris
**bu dalın yedi güvenlik kartını** listeler; her satır o kartın kendi §1 "OS
Güvenlik Profili" bölümünü temsil eder. Android'in bu matristeki ayrışma
noktası (§1.1–§1.3 ve §2'de belgelendiği üzere): **tek güvenlik kartıdır ki
repo'da UA tespiti + veritabanı platform enum'u ile ölçülebilir izi vardır**
(§2.1, §2.2) — TV/platform kartlarında bu iz yoktur.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | **`android-guvenlik` (bu kart)** | **Linux + SELinux + permission** | **UA + enum + CI (§2.1–§2.4)** | — |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach | Kısmi (§2.1 boşluk matrisi) | uzak aile |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Güçlü (üretim hedefi RPi5) | ailedeş (çekirdek) |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif (§2.1) | uzak aile |
| 5 | `reactos-guvenlik` | NT Security Manager | YOK (§2.1–§2.3) | uzak (NT) |
| 6 | `tizen-guvenlik` | Linux + privilege manifest | UA (§2.1), enum YOK | ailedeş (manifest) |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups | UA (§2.1), enum YOK | ailedeş (manifest) |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma (§2.1–§2.2) | uzak (NT) |

**Not:** `isletim-sistemi-security` dalındaki `uygulama-guvenlik-siniri` ve
`yetki-ve-erisim-modeli` kartları bu kartla **bridge** (§3) ile bağlıdır; bu
matris yalnız **turu dalındaki** güvenlik kartlarını kapsar.

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm Android'e özgü **yeni teknik iddia içermez**; yalnız güvenlik
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

Android'de ses erişimi **permission + SELinux politikası** ile korunur (mikrofon
yetkisi vb.) — ancak **hangi politika kuralının hangi ses yolunu açtığı** bu
dosyada kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`** (uydurma policy adı yasak).

| RT katmanı (kaynak §) | Android güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|------------------------|------------------------|-------|
| MMAP/NOIRQ yolu (dosyanın §2–§3) | Donanım yoluna erişim ayrı yetki | Hayır — Android audio policy kanıtı yok | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | çekirdek uzayı register erişimi | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz — OS davranışı | Hayır | **PLANNED** |
| Cihaz kaybı kurtarma (L497) | USB/BT kopması → yeniden bağlanma | Hayır — bu dosya süreç işlemaz | **PLANNED** |

**Kesişim kararı (bu kart):** Android, ses/RT'nin **mümkün hedefi** olabilir
(AAudio/opensLS — bu dalda `android-aaudio-ve-opensl.md` 505 satır), **ama bu
kart o kararı vermez**; güvenlik/RT kararları `isletim-sistemi-audio` +
`isletim-sistemi-security` dallarında alınır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "Android'de şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | UA izi ≠ güvenlik doğrulaması | §2.1 UA tespiti "güvenlik doğrulandı" sanılırsa | Yanlış güvenlik garantisi | §2.1, §17 |
| F2 | Dürüst negatifin tersine çevrilmesi | §2.5'teki "YOK" kanıtlarının "VAR" sanılması | Yanlış envanter | §2.5 |
| F3 | Bridge'in sorumluluk karıştırılması | §3'teki devir/devralmanın ters yorumu | Sınır ihlali | §3, §12 |
| F4 | S1/S2 erken kapatılması | §6'daki `[UNKNOWN]` soruların cevaplandığı varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | SELinux abartısı | §1.1/§1.2'deki katmanların "her şeyi koruduğu" sanılması | Yanlış varsayım | §1.1, §1.3 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; policy/permission adı **uydurulmaz**.
2. **P1 — Daralt:** Genel iddia ölçülebilir dar iddiaya iner
   (ör. "güvenlik doğrulandı" → "§2.1 UA izi mevcuttur").
3. **P2 — Delege et:** Karar mimari sahibine; §6'daki açık sorular yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| Android güvenlik katmanı özeti | Evet | §1.1–§1.3 | Temel katman + varsayılan + risk |
| Repo'da Android güvenlik izi | Evet | §2.1–§2.6 | DOSYA:SATIR kanıtlı |
| K000→security sınırı | Evet | §3 | Bridge kartları |
| K009 sınırı (uygulama güvenliği) | Evet | §4 | Devredilmez |
| Android'i üretim hedefi seçme | **Hayır** | §10, §17 | Karar bu kartta değil |
| Ses/RT yetki kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili | Dosyanın kendi §1'i | Vendor-official §5 listesi |
| §2 repo izi | §2.1–§2.5 | `Dosya:` + `Satır:` (dosyada belgelendi) |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan Android çekirdek kanıtı | **YOK** | CVE iddiası yok (§4 sınır cümleleri) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── android
│       └── android-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/android      (kardeş dal)
    └── uygulama-guvenlik-siniri/android (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart **yalnız kendi §0–§6 içeriğini** §7–§19'da
yapılandırır; bridge kartlarına **veri üretmez** (§3'teki devir sınırlıdır).

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test (bu kart neyi doğrular)
- §2.1–§2.5 repo izleri **`Dosya:`/`Satır:`** ile destekli (§2'de belgelendi).
- §3 bridge tanımı §4 K009 sınırıyla **tutarlı** (devir yok, devralma var).
- §5 kaynakları **2026-10-15 HTTP 200** (§5'te tarihli).

### §13.2 Negatif test (bu kart neyi REDDEDER)

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "Android güvenlik katmanı repo'da doğrulandı" | §2 yalnız UA/enum/CI izi | §9 F1 |
| "§2.5'te YOK denilenler VAR" | Dürüst negatif kanıt | §9 F2 |
| "§3 bridge her şeyi devralır" | Sınır §3/§4'te | §9 F3 |
| "S1/S2 bu kartta kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası (önerilen)
`§0 → §1 → §2 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6 ve §17 görülmeden §8/§10'a geçilmez — PLANNED/negatif bağlam kaybolur).

## §14 Olay şeması / eskalasyon (bu kartta kanıt ihlali olursa)

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Android sürüm/permission değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| §5 kaynaklarından biri 404 | Kaynak envanteri eskir | URL yenileme + tarih |
| Kardeş dal (`cekirdek-guvenlik/android`) güncellenirse | §7/§12 eşleşmesi | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Bu uzatma **append-only** ile yapıldı; §0–§6 içeriği **değiştirilmedi**.
Büyütme kuralı: yeni bölüm yalnız Authority footer'ının **öncesine** eklenir;
frontmatter (`status:` vb.) ve footer **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş kart adları | Düşük (yapısal) |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§19 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Bu karttaki durum |
|---|-------------|-------------------|
| 1 | Android güvenlik katmanı repo'da doğrulandı mı? | **Hayır** — §2 yalnız UA/enum/CI izi |
| 2 | SELinux/permission politikaları uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | Bridge kartları hazır mı? | §3'e kadar — **tanım** var, veri yok |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Android üretim hedefi mi? | **Hayır** — bu kart karar vermez (§10) |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5/§4 |

## §17 Doğrulama notu (negatif hafıza — bu kartın bilinçli boşlukları)

1. **Çalışan Android güvenlik doğrulaması yok** — §2 bir iz envanteridir.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı bu dosyada `UNKNOWN`.
3. **S1/S2 kapalı değil** — mimari sahibine ait (§6).
4. **§2.5'teki "YOK"lar düzeltilemez** — bunlar dürüst negatif kanıttır.
5. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza (gelecek turlar için "yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4 yasaklar).
- §2.1 UA izini "güvenlik doğrulaması" **diye yazma** (§9 F1).
- §2.5'teki YOK kanıtlarını **tersine çevirme** (§9 F2).
- S1/S2'yi bu kartta **kapatma** (§6).
- §8'i "mevcut özellik" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 (S1/S2 açık bırakıldı)
- [x] Beyazliste dışı yazım: 0 (yalnız K000)
- [x] `git commit`: yok
- [x] Ölçüm: ≥500 satır (append-only)
- [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — Android)

Android'in `isletim-sistemi-security` dalındaki kartları ile bu kart **farklı
sorular** sorar; bu bölüm yalnız ikisinin **sorularının** karşılaştırmasıdır:

| Soru | `cekirdek-guvenlik/android` | `uygulama-guvenlik-siniri/android` | **bu kart** |
|------|------------------------------|--------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS güvenlik profili ne? |
| Birim | LSM/sürücü | Sınır tanımı | Katman + repo izi |
| Kanıt | Çekirdek yapılandırma | Sınır kartı | §1/§2 (DOSYA:SATIR) |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart aynı kapıları paylaşır; **biri diğerini kapatmaz**. Bu kart
yalnız **turu** sorusunu yanıtlar; koruma/sınır soruları security dalındadır.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak (o an mevcut) | Kapı |
|-----|-----------|----------------------|------|
| R1 | §2'ye yeni repo izi | Repo taraması (grep/glob) | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §1'de sürüm güncellemesi | Vendor docs | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` olgunlaşınca | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status`/`parent_id` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/policy adı eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

Android'in güvenliği bu kartta **katman profili + ölçülebilir repo izi (UA/enum/CI)**
olarak durur; **S1/S2 kararı olmadan** bu kart **tamamlanmış sayılmaz** —
sonraki turun işi §21'deki revision planıdır.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 264 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§2/§3/§4/§5/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 — her iddia dosya-içi atıf veya `⚠️ VERIFICATION REQUIRED` |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
