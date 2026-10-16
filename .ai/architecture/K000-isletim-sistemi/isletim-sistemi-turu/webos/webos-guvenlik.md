---
title: "webOS — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/webos"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "shared/src/Device/DeviceDetector.php:33 (Web0S/webOS UA — okundu)"
  - "assets.coremusic.net/js/core/breakpoints.js:31 (RE_TV_UA — okundu)"
  - ".ai/.sql/mysql/coremusic_media.sql:72 (platform enum'da webos YOK — okundu)"
  - ".github/workflows/ci.yml:27 (ubuntu-latest — okundu)"
validation: "2026-10-15 — 7 vendor URL erişildi; lg.com (eski alan) NXDOMAIN"
open_questions:
  - "webOS TV'de uygulama imzası + Developer Mode anahtar akışı repo'da nasıl ele alınıyor?"
  - "LS2 (Luna Service) çağrıları için CoreMusic'in bir servisi var mı? — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# webOS — İşletim Sistemi Güvenlik Profili

> **Kapsam:** webOS'un (TV + OSE) güvenlik modeli — Luna Bus (LS2), Access Control Groups
> (ACG), Trust Level, uygulama izinleri — ve CoreMusic deposundaki izleri.
> Uygulama seviyesi güvenlik (auth/CSRF/CSP) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

webOS, LG'nin televizyonlarda (ve açık kaynak sürümü webOS OSE'de) kullandığı işletim
sistemidir. Linux çekirdeği üstüne kuruludur ve güvenlik mantığı **"konuşma izni"** etrafında
döner.

TV'de her şey birbirine *mesajla* konuşur: uygulama, ayarlar, medya oynatıcı… Bu konuşmayı
taşıyan yol **Luna Service Bus (LS2)** adlı bir otobüstür. Doğrudan dosyaya veya sisteme
erişmek yerine, uygulama bir servise "şunu yap" der; servis isterse yapar.

Peki herkes her şeyi söyleyebilir mi? Hayır. webOS bunu iki anahtarla kısıtlar:

1. **Access Control Group (ACG):** Her servis ve uygulama bir *gruba* atanır; kimin kiminle
   konuşabileceği `/var/luna-service2/` altındaki konfigürasyon dosyalarında yazılıdır.
   Yazılı olmayan çağrı reddedilir. Buna *service-level permissions* denir.
2. **Trust Level:** Uygulamanın kime ait olduğu — `dev` (geliştirici), `part` (partner),
   `oem` (üretici) — belirler hangi izinlere sahip olabileceği. Düşük güven = az izin.

Ek olarak TV tarafında uygulamalar **imzalanır** ve geliştirici, cihazda **Developer Mode**
açmak için özel bir akıştan geçer (hesap + özel anahtar). Böylece sıradan bir uygulama
sisteme kapıdan girip *root* olamaz.

CoreMusic açısından anlamı: webOS, **TV istemci ortamıdır**. CoreMusic bir TV uygulaması
olarak `appinfo.json` içinde `requiredPermissions` bildirir; OS bu izinleri ACG/Trust Level
ile denetler. CoreMusic'in kendi API güvenliği ise K009'dur.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar |
|---|---|---|
| IPC | **Luna Service Bus (LS2)** | Tüm sistem çağrıları mesaj tabanlı servis çağrıları üzerinden |
| IPC | **Access Control Groups (ACG)** | Servisler arası izin matrisi; konfigürasyon dosyalarıyla tanımlı |
| App | **Trust Level** (`dev` / `part` / `oem`) | Uygulamanın kaynağine göre izin genişliği |
| App | **Uygulama imzası + `requiredPermissions`** | `appinfo.json` içinde bildirilen izinler OS tarafından denetlenir |
| Dev | **Developer Mode** (TV) | Cihazda geliştirici modunu açmak için hesap + özel anahtar akışı |
| Base | **Linux çekirdek + sandbox** | webOS OSE, Linux tabanlı; servis izolasyonu ACG ile |
| Ops | **Self-checklist (dağıtım)** | LG'nin yayınlama öncesi güvenlik kontrol listesi |

### 1.2 Varsayılanlar ve tasarım ilkeleri

- **Least privilege:** Bir servis yalnızca ACG dosyasında izin verdiği çağrıları alır.
- **Explicit grant:** `appinfo.json`'da bildirilmeyen izin, uygulamada yoktur.
- **Trust Level ile ayrıcalık basamaklanması:** `part`/`oem` seviyesi `dev`'den geniştir;
  dağıtım zinciri güveni belirler.
- **Tek giriş (TV):** Uygulamalar mağaza/dağıtım sürecinden geçmeden cihaza tam erişemez.

### 1.3 webOS OSE ile TV sürümü arasındaki fark

| | webOS OSE (açık kaynak) | webOS TV (LG) |
|---|---|---|
| Kaynak | `webosose.org` — ACG + Trust Level açık dokümante | `webostv.developer.lge.com` — Developer Mode, imza, self-check |
| Geliştirme | Kurulum/paket yapılandırması (security-guide) | SDK + sertifika oluşturma + dağıtım |
| Olgunluk | ACG geçişi 1.8.0'da tamamlandı (blog kaydı) | Üretim cihazlar |

> **Not:** `www.webostv.lg.com` alanı bu oturumda **NXDOMAIN** döndürdü; geçerli geliştirici
> alanı **`webostv.developer.lge.com`**'dur (erişildi, §5).

### 1.4 Tehdit modeli — CoreMusic bir webOS TV istemcisi olarak

Bu tablo **yalnızca bu oturumda okunan kanıtlar** (§2) ve erişilen vendor sayfaları (§5)
üzerinden kurulur; varsayılan yeni iddia üretilmez.

| Varlık | Tehdit | OS'nin savunması | CoreMusic'in savunması (sahip) |
|---|---|---|---|
| TV uygulama paketi | Yetkisiz kurulum / değiştirilmiş paket | Uygulama imzası + Trust Level (§1.1) | Paketleme repo'da **0 isabet** → kapsam dışı (§2.4) |
| Cihaz içi servis çağrıları | Uygulamanın izinsiz servise erişimi | ACG konfigürasyon dosyaları (§1.1, §5/6) | CoreMusic'te LS2 çağrısı **0 isabet** (§2.4) |
| UA spoof | Sahte `Web0S` UA'sı ile tier/limit manipülasyonu | — (OS ile ilgisi yok) | **K009** — §2.1'de risk kanıtlandı; UA güvenlik kararı değildir |
| İstemci → backend kanalı | Token çalınması, MITM, replay | — (ACG yalnız cihaz içi) | **K009** — HTTPS, token, rate limit (§4) |
| `platform` alanı | Yanlış platform kaydı → yanlış davranış | — | `webos` enum'da yok → `other` (§2.2) |

**Kritik ayrım:** ACG (Access Control Groups) **yalnızca aynı cihaz içindeki** Luna Service
çağrılarını denetler. CoreMusic backend'ine gelen uzak HTTPS isteği ACG'nin kapsamı
**dışındadır** — bu yüzden §4'te tamamen K009'a devredilir. Bu sınır, webOS güvenliği ile
uygulama güvenliğini ayıran ana çizgidir.

**Tehdit modelinin bilinmeyenleri:** TV webview'inin (Chromium tabanlı) sürümü ve yama
durumu, cihaz başına farklılık gösterir → `UNKNOWN`. Geliştirici-modu-açık cihazların ek
yüzeyi (§6/4) bu oturumda doğrulanamadı → `⚠️ VERIFICATION REQUIRED`.

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Her satır bu oturumda **okunmuş** gerçek dosya satırıdır.

### 2.1 webOS UA tespiti — TV cihaz sınıflandırması

| İddia | Kanıt | Durum |
|---|---|---|
| webOS UA'sı TV olarak tanınır | `shared/src/Device/DeviceDetector.php:33` → `'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA'` | ✅ OKUNDU |
| UA listesi `Web0S` (LG'nin yazımı) + `webOS` içerir | aynı satır | ✅ OKUNDU |
| JS tarafında TV UA regex'i webOS'u kapsar | `assets.coremusic.net/js/core/breakpoints.js:31` → `RE_TV_UA = /Tizen\|Web0S\|webOS\|SmartTV\|NETTV\|Viera\|BRAVIA\|.../i` | ✅ OKUNDU |
| webOS `Android` regex'ine girmez (phone/tablet değil) | `breakpoints.js:84` → yalnız `/Android/i` eşleşir | ✅ OKUNDU (dürüst sınır) |

**Yorum:** CoreMusic, webOS'u **yalnızca UA string'i üzerinden** TV olarak sınıflandırır.
UA spoof edilebilir → bu tespit **güvenlik kararı için yeterli değildir** (K009 sınırı).

### 2.2 Veritabanı platform enum'u — `webos` YOK

`.ai/.sql/mysql/coremusic_media.sql:72` (okundu):

```sql
`platform` enum('windows','linux','macos','android','ios','rpi','other') COLLATE utf8mb4_unicode_ci NOT NULL,
```

- **`webos` enum'da YOK** → webOS cihazları `device_types.platform` altında `other`'a
  düşmek zorundadır. **Dürüst negatif kanıt.**
- `android` enum'da var → platform bazlı sorgulamada webOS cihazları android'den
  ayırt edilemez (yalnız `type_name`/UA üzerinden).

### 2.3 CI — webOS kapsamı yok

`.github/workflows/ci.yml` bu oturumda okundu:
- L27 · L69 · L112 · L144 · L162 · L180 → **tüm job'lar `runs-on: ubuntu-latest`**
- L22 → `PHP_VERSION: '8.4'`

**webOS SDK / emulator / device test job'ı YOK.** webOS güvenlik davranışı (ACG, imza,
Developer Mode) CI'da test edilmiyor.

### 2.4 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama | Sonuç |
|---|---|
| `luna-service` / `LS2` / `ls2` (uygulama kodu) | **0 isabet** — CoreMusic'te Luna Service çağrısı yok |
| `appinfo.json` (repo) | **0 isabet** — webOS uygulama manifest'i yok |
| `webos` (kod yüzeyi) | Yalnız UA tespiti: `DeviceDetector.php:33` + `breakpoints.js:31` (2 dosya) |
| `reactos` (kod dosyaları) | **0 isabet** (kardeş dosyanın da kanıtı) |

**Sonuç:** CoreMusic, webOS'u **desteklenen bir TV istemcisi** olarak tanır (UA) ama
webOS'e özgü **hiçbir** güvenlik mekanizması (LS2, ACG, imza) içermez. OS güvenliği
platforma devredilmiştir; CoreMusic'in yükümlülüğü §4/K009'dur.

### 2.6 Özet — depoda webOS'e dair ne var / ne yok

| Yüzey | Durum | Kanıt (bu oturumda okundu) |
|---|---|---|
| UA ile TV tespiti | **VAR** | `shared/src/Device/DeviceDetector.php:33` · `assets.coremusic.net/js/core/breakpoints.js:31` |
| JS cihaz tier'ı | **VAR** | `breakpoints.js:31` (`RE_TV_UA`) + `:84` (phone/tablet) |
| DB `platform` enum'unda `webos` | **YOK** (`other`'a düşer) | `.ai/.sql/mysql/coremusic_media.sql:72` |
| webOS paket manifest'i (`appinfo.json`) | **YOK** — 0 isabet | grep 2026-10-15 |
| Luna Service (LS2) çağrısı | **YOK** — 0 isabet | grep 2026-10-15 |
| webOS CI job'ı | **YOK** | `.github/workflows/ci.yml` L27 · L69 · L112 · L144 · L162 · L180 → `ubuntu-latest` |
| webOS'e özgü güvenlik kodu | **YOK** — 0 isabet | grep 2026-10-15 |

**Tek cümlelik sonuç:** CoreMusic, webOS'u *tanır* (UA) ama webOS için *hiçbir şey üretmez*
(paket, servis, güvenlik kodu, test yok) → OS güvenliği tamamen platforma devredilmiştir.

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; OS kartı güvenlik detayları
`isletim-sistemi-security` dalındadır. Envanter `isletim-sistemi-security/index.md`
üzerinden okunmuştur (24 OS kartı: çekirdek/yetki/uygulama-sınırı × 8 OS + 15 kod kartı):

| Alt dal | Bridge |
|---|---|
| çekirdek-guvenlik | [`../../isletim-sistemi-security/cekirdek-guvenlik/`](../../isletim-sistemi-security/cekirdek-guvenlik/) |
| yetki-ve-erisim-modeli | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/) |
| uygulama-guvenlik-siniri | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/) |
| kod-ornekleri | [`../../isletim-sistemi-security/kod-ornekleri/`](../../isletim-sistemi-security/kod-ornekleri/) |

**Sınır kuralı:** webOS'un ACG/Trust Level/LS2 detayları K000 güvenlik dalındadır;
CoreMusic'in webOS cihazına karşı yükümlülükleri (token, TV UA'ya güvenmeme, HTTPS) §4/K009.

Kardeşler: `[android](../android/android-guvenlik.md)` · `[tizen](../tizen/index.md)` ·
`[reactos](../reactos/index.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği.

- Sınır hedefi: [`../../K009-security/index.md`](../../K009-security/index.md)

| Soru | Sahip | Not |
|---|---|---|
| webOS UA'sına güveniliyor mu? | K009 | §2.1 — UA spoof edilebilir; güvenlik kararı olarak kullanılamaz |
| `platform` enum'u `webos` içermiyor | K000 + Data Engineer | `coremusic_media.sql:72` → `other`'a düşüş |
| TV'de güvenli cookie / storage davranışı | K009 | webOS TV tarayıcısı / uygulama webview'i özelikleri `⚠️ VERIFICATION REQUIRED` |
| Uygulama `requiredPermissions` bildirimi | K000 (OS manifesti) | Repo'da `appinfo.json` **0 isabet** → webOS paketleme yok |

**Geçiş noktası:** OS'nin ACG'si, CoreMusic'in API çağrısını *durmaz* — ACG yalnızca
**aynı cihaz içindeki** servis çağrılarını denetler. CoreMusic backend'ine gelen HTTPS
istekleri ACG'nin dışındadır → tamamen K009 (auth, rate limit, CSRF).

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş** ve içerik döndürmüştür. Generic OWASP/NIST/CIS yok.

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://webostv.developer.lge.com/develop/guides/privacy-guideline | TV gizlilik/rehber kılavuzu | ✅ erişildi |
| 2 | https://webostv.developer.lge.com/develop/getting-started/developer-mode-app | Developer Mode + özel anahtar akışı | ✅ erişildi |
| 3 | https://webostv.developer.lge.com/develop/references/appinfo-json | `appinfo.json` alanları (izinler dahil) | ✅ erişildi |
| 4 | https://webostv.developer.lge.com/distribute/app-self-checklist | Yayınlama öncesi güvenlik self-check | ✅ erişildi |
| 5 | https://webostv.developer.lge.com/develop/specifications | Platform spesifikasyonları | ✅ erişildi |
| 6 | https://www.webosose.org/docs/guides/development/configuration-files/security-guide/ | ACG + Trust Level (`dev`/`part`/`oem`) + LS2 konfigürasyon dosyaları | ✅ erişildi |
| 7 | https://www.webosose.org/blog/2019/06/17/webos-ose-1-8-0-release/ | ACG migration'ın tamamlandığı sürüm kaydı | ✅ erişildi |

**Elenen / sorunlu URL'ler (dürüst kayıt):**
- `www.webostv.lg.com` → **NXDOMAIN** → geçerli alan `webostv.developer.lge.com` (yukarıdakiler).

---

## §6 — Açık Sorular

1. **TV uygulaması paketleme var mı?** Repo'da `appinfo.json` 0 isabet → CoreMusic'in
   webOS TV paketi (`ipk`) mi yoksa yalnız web tarayıcısı üzerinden mi sunulduğu `UNKNOWN`.
2. **UA'ya dayalı tier/limit güvenliği:** `breakpoints.js:31` TV UA'sı → `embedded`/TV tier'ı;
   bu bilgi sunucuda güvenlik kararı olarak kullanılıyor mu? → K009.
3. **`webos` platform enum'a eklenmeli mi?** `coremusic_media.sql:72` → şu an `other`.
   Veri doğruluğu için Data Engineer kararı.
4. **Developer Mode cihazlarında ek yüzey:** Geliştirici modu açık TV'ler ek erişim açar mı;
   CoreMusic bunu fark ediyor mu? `⚠️ VERIFICATION REQUIRED`
5. **CI kapsamı:** webOS için hiçbir test yok (`ci.yml` ubuntu-latest) — bilinçli kapsam mı?

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
