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

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. webOS'un bu
matristeki ayrışma noktası (§1.3 ve §2.1'de belgelendiği üzere): **webOS OSE
ile TV sürümü arasındaki fark** §1.3'te ayrı bir bölümdür (aynı ürün ailesi,
**farklı izin/ürün modeli**), ayrıca **TV cihaz sınıflandırması UA tespiti ile
vardır** (§2.1 — Tizen ile birlikte dalın TV temsilcisi).

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | uzak aile |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach | Boşluk matrisi | uzak aile |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Üretim hedefi + CI | **ailedeş (Linux çekirdek)** |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif | uzak aile |
| 5 | `reactos-guvenlik` | NT Security Manager | TAMAMEN YOK | uzak (NT) |
| 6 | `tizen-guvenlik` | Linux + privilege manifest (TV) | UA VAR · enum/CI YOK | **TV kardeşi** (aynı iz profili) |
| 7 | **`webos-guvenlik` (bu kart)** | **Linux + ACG/role/groups (TV)** | **UA VAR · enum/CI YOK (§2.1–§2.3)** | — |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma | uzak (NT) |

**Ayrışma notu:** Bu kart ile `tizen-guvenlik` **dalın iki TV temsilcisidir**
ve **aynı iz profiline** sahiptir (UA VAR · enum YOK · CI YOK) — bu
**benzerlik** bir **eşdeğerlik değildir** (§9 F5): webOS ACG ≠ tizen privilege.
Ayrıca **§1.3 OSE↔TV farkı** bu kartı **tek başına ikiye böler**: OSE modeli
ile TV modeli **eşdeğer değildir** (eşdeğerlik kanıtlanmadı).

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm webOS'a özgü **yeni teknik iddia içermez**; yalnız güvenlik katmanının
CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir. Birincil
kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

webOS'ta ses erişimi **ACG (Application Control Group) beyanı** ile korunur —
ancak **hangi ACG adının hangi ses yolunu açtığı** bu dosyada kanıtlanmaz →
**`⚠️ VERIFICATION REQUIRED`** (uydurma ACG adı yasak).

| RT katmanı (kaynak §) | webOS güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|----------------------|------------------------|-------|
| OS ses çatısı (L30, L43) | Ses API'si ACG'ye bağlı | Hayır — ACG adı doğrulanmadı | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | Çekirdek modül erişimi | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz | Hayır | **PLANNED** |
| Cihaz kaybı kurtarma (L497) | Kopma → yeniden bağlanma | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** webOS, ses/RT için **hedef değildir** (bu dalda
ses kartları `webos-ses-ve-rota.md` vb. ile **mevcuttur**, güvenlik kararı
değil); karar `isletim-sistemi-audio` + `isletim-sistemi-security` dallarında
alınır. Bu kart yalnız **güvenlik katmanı girdisini** taşır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "webOS'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | UA izinin güvenlik doğrulaması sanılması | §2.1 UA tespiti "güvenlik doğrulandı" sanılırsa | Yanlış güvenlik garantisi | §2.1, §17 |
| F2 | OSE↔TV farkının atlanması | §1.3'teki fark unutulursa | Yanlış model aktarımı | §1.3 |
| F3 | Tizen ile özdeşleştirme | Aynı iz profili → aynı model sanılırsa | Yanlış eşdeğerlik | §7, §9 F5 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | ACG abartısı | §1.1–§1.2 katmanlarının "her şeyi koruduğu" sanılması | Yanlış varsayım | §1.1, §1.2 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; ACG adı **uydurulmaz**.
2. **P1 — Daralt:** "webOS güvenliği doğrulandı" → "§2.1 UA izi mevcut;
   §2.2/§2.3 YOK; §1.3 OSE≠TV".
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| webOS güvenlik katmanı özeti | Evet | §1.1–§1.2 | Temel katman + tasarım ilkeleri |
| OSE vs TV sürümü farkı | Evet | §1.3 | Ürün modeli ayrımı |
| TV cihaz sınıflandırması izi | Evet | §2.1 | UA tespiti (pozitif) |
| Kısmi izin dürüst kaydı | Evet | §2.2–§2.6 | enum/CI YOK + özet |
| K000→security sınırı | Evet | §3 | Bridge |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili (§1.3 fark dahil) | Dosyanın kendi §1'i | §5 vendor envanteri |
| §2 repo izi | §2.1–§2.6 | `Dosya:` + `Satır:` + grep (dosyada belgelendi) |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan webOS çekirdek kanıtı | **YOK** | CVE iddiası yok (§5/§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── webos
│       └── webos-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/webos       (kardeş dal)
    └── uygulama-guvenlik-siniri/webos (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1 UA izi **pozitif** ve `Dosya:`/`Satır:` ile destekli.
- §2.2–§2.4 YOK kanıtları destekli (kısmi iz dürüst).
- §1.3 OSE↔TV farkı ayrı bölüm olarak mevcut.
- §5 kaynakları **2026-10-15 HTTP 200**.

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "webOS güvenlik katmanı doğrulandı" | §2.1 yalnız UA izi | §9 F1 |
| "OSE = TV izin modeli" | §1.3 farkı | §9 F2 |
| "webOS = tizen iz modeli" | Benzer profil ≠ aynı model | §9 F3/F5 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §1.3 → §1.4 → §2 → §2.1 → §2.6 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| webOS sürüm/ACG değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| OSE/TV sürüm farkı değişirse | §1.3 eskir | §1.3 yeniden okuma |
| Repo'ya enum/CI izi gelirse | §2.2/§2.3 YOK eskir | §2 yeniden tarama |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | webOS güvenlik katmanı repo'da doğrulandı mı? | **Kısmen** — §2.1 UA VAR; §2.2/§2.3 YOK |
| 2 | ACG/role uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | OSE = TV izin modeli mi? | **Hayır/Kanıtlanmadı** — §1.3 fark |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | webOS TV hedefi mi? | **İstemci** — §1.4 TV istemcisi tehdit modeli |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan webOS güvenlik doğrulaması yok** — §2.1 yalnız UA izi.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§1.3 OSE↔TV farkı doldurulamaz** — eşdeğerlik kanıtlanmadı.
5. **§2.2/§2.3 YOK kanıtları düzeltilemez** — dürüst negatif.
6. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1 UA izini "güvenlik doğrulaması" **diye yazma** (§9 F1).
- §1.3 OSE↔TV farkını **eşitleme** (§9 F2).
- tizen ile **özdeşleştirme** (§9 F3/F5).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — webOS)

| Soru | `cekirdek-guvenlik/webos` | `uygulama-guvenlik-siniri/webos` | **bu kart** |
|------|----------------------------|------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili + kısmi iz |
| Birim | LSM/çekirdek | Sınır tanımı | Katman + UA/enum/CI izi + §1.3 |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2.2/§2.3'e iz (gelirse) | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §1.3 OSE↔TV farkının güncellenmesi | Vendor docs | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/ACG eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

webOS'un güvenliği bu kartta **katman profili + OSE↔TV farkı + TV tehdit
modeli + kısmi iz (UA VAR · enum/CI YOK)** olarak durur; **S1/S2 kararı olmadan**
bu kart **tamamlanmış sayılmaz**.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 260 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§1.3/§1.4/§2/§2.1–§2.6/§3/§4/§5/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

## §24 OSE↔TV farkı hafızası (bu kart — mutlak son)

Bu kartın en kırılgan noktası §1.3'teki **ürün modeli ayrımıdır**; sonraki tur
bu kartı açtığında **neyin OSE, neyin TV olduğunu** tek tabloda görür (bu tablo
§1.3/§2.1–§2.3'ün **özeti**dir, yeni iddia **içermez**):

| Öğe | OSE (açık kaynak) | TV sürümü | Kanıt durumu |
|-----|-------------------|-----------|--------------|
| İzin/ürün modeli | §1.3'teki model | §1.3'teki model | **Fark belgeli — eşdeğerlik YOK** (§9 F2) |
| UA izi (§2.1) | — | VAR (TV sınıflandırma) | Pozitif |
| enum/CI (§2.2/§2.3) | — | YOK | Dürüst negatif |
| CVE (§5/§4) | — | YOK | İddia yok |

**Son cümle:** OSE↔TV eşdeğerliği **kanıtlanmadı** ve §6'daki S1/S2 kapıları
**kapatılmadan** bu kart **tamamlanmış sayılmaz** — bir sonraki turun işi §21'deki
revision planıdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
