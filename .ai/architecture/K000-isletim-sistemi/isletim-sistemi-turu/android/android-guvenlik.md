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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
