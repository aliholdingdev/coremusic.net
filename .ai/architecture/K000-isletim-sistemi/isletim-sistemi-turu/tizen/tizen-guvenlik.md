---
title: "Tizen — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/tizen"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "shared/src/Device/DeviceDetector.php:33 (Tizen UA — okundu)"
  - "assets.coremusic.net/js/core/breakpoints.js:31 (RE_TV_UA — okundu)"
  - ".ai/.sql/mysql/coremusic_media.sql:72 (platform enum'da tizen YOK — okundu)"
  - ".github/workflows/ci.yml:27 (ubuntu-latest — okundu)"
validation: "2026-10-15 — 6 vendor URL erişildi; docs.tizen.org derin yollar SPA iskeleti döndürdü"
open_questions:
  - "Tizen privilege seviyesi (public/partner/platform) CoreMusic için hangisi geçerli?"
  - "Samsung TV'de author+distributor sertifika akışı repo'da mevcut mu? — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# Tizen — İşletim Sistemi Güvenlik Profili

> **Kapsam:** Tizen'in güvenlik modeli (imza, privilege seviyeleri, manifest izinleri) ve
> CoreMusic deposundaki izleri. Uygulama seviyesi güvenlik (auth/CSRF/CSP) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

Tizen, Samsung'un akıllı televizyonlarında (ve saat/giyilebilir cihazlarda) kullanılan,
Linux çekirdeği üstüne kurulmuş açık kaynak bir işletim sistemidir.

Güvenliğinin merkezinde **iki fikir** var:

1. **Her uygulama bir "bilet" (imza) ile girer.** Tizen'de uygulamalar imzasız çalışmaz.
   İki imza vardır:
   - **Author imzası:** uygulamanın *kimsin* olduğunu söyler (geliştirici kimliği).
   - **Distributor imzası:** uygulamanın *nerede yayınlanacağı* için verilir (mağaza/cihaz).
   İmza yoksa veya bozuksa, uygulama cihaza kurulmaz.
2. **Her izin bir "seviye" ile gelir.** İzinler üç kademeye ayrılır:
   - **public:** herkese açık, zararsız izinler (ör. ağ erişimi).
   - **partner:** dağıtım ortaklarına açık, daha geniş izinler.
   - **platform:** işletim sistemi içi ayrıcalıklar — normal uygulamalara verilmez.

Bir uygulama, manifest'inde (`tizen-manifest`) hangi izinleri istediğini yazar; OS
yalnızca o izinleri verir. İzin istemeyen kod, o yetkiye erişemez.

Buna ek olarak Samsung TV tarafında, uygulamayı *sen* imzalamak için cihaza özel
**sertifika üretmen** gerekir (author + distributor sertifikaları); TV geliştirme SDK'sı
bu sertifika üretimini adım adım tarif eder.

CoreMusic açısından anlamı: Tizen, **TV istemci ortamıdır**. CoreMusic TV tarafında UA
tespitiyle tanınır; OS'nin imza/privilege modeli CoreMusic backend'ini *etkilemez* —
bunun sınırı §4/K009'dur.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar |
|---|---|---|
| Kurulum | **Uygulama imzası** (author + distributor) | İmzasız/bozuk paket cihaza kurulmaz |
| Yetki | **Privilege seviyeleri** (`public` / `partner` / `platform`) | İzinler seviye matrisiyle korunur |
| Manifest | **`tizen-manifest` izin bildirimi** | Uygulama yalnızca bildirdiği izinlere sahiptir |
| Kurulum | **Sertifika zinciri** (author → distributor) | Geliştirici kimliği + yayın yetkisi ayrı doğrulanır |
| App | **Sandbox + Linux çekirdek** | Uygulama izolasyonu (Tizen iddiası — detay §6 açık 1) |
| Ops | **Sertifika oluşturma akışı (Samsung TV)** | Cihaza özel sertifika üretimi SDK'da zorunlu adım |

### 1.2 Varsayılanlar

- **Explicit grant:** Manifest'te belirtilmeyen privilege, uygulamada yoktur.
- **Least privilege:** Public privilege bile yalnızca ihtiyaç duyulanla sınırlandırılmalıdır
  (matris `developer.tizen.org` üzerinden erişildi — §5/1).
- **Signature = identity:** İmza zinciri koptuğunda güncelleme/kurulum reddedilir.

> ⚠️ **VERIFICATION REQUIRED:** `Sandbox` ve `seccomp` benzeri düşük seviyeli mekanizmaların
> Tizen'deki tam karşılığı bu oturumda vendor dokümanından birebir doğrulanmadı — §6 açık 1.

### 1.3 Bilinen yapısal riskler

- **Samsung TV sürüm/fragmentation:** Tizen sürümleri cihaz modeline göre farklılık gösterir;
  privilege matrisi sürüme göre değişebilir → **UNKNOWN** (spesifik sürüm eşlemesi yok).
- **Partner/platform privilege istismarı:** Yüksek seviye izin, yanlış verilmişse güçlü
  bir saldırı yüzeyi doğurur (matris disiplini kritik).

### 1.4 Tehdit modeli — CoreMusic bir Tizen TV istemcisi olarak

Bu tablo **yalnızca bu oturumda okunan kanıtlar** (§2) ve erişilen vendor sayfaları (§5)
üzerinden kurulur.

| Varlık | Tehdit | OS'nin savunması | CoreMusic'in savunması (sahip) |
|---|---|---|---|
| Tizen uygulama paketi (TPK) | İmzasız/bozuk paket kurulumu | Author + distributor imzası (§1.1, §5/1) | Paketleme repo'da **0 isabet** → kapsam dışı (§2.4) |
| Manifest dışı erişim | Uygulamanın bildirmediği privilege'ı kullanması | `tizen-manifest` izin bildirimi + privilege matrisi (§5/1) | Repo'da `tizen-manifest` **0 isabet** (§2.4) |
| Yüksek seviye izin (platform) | Ayrıcalık istismarı | Seviye matrisi `public`/`partner`/`platform` (§5/1) | Kullanım durumu **UNKNOWN** (§6/2) |
| Sahte Tizen UA'sı | Tier/limit manipülasyonu | — (OS ile ilgisi yok) | **K009** — §2.1'de UA spoof riski kanıtlandı |
| İstemci → backend kanalı | Token çalınması, MITM, replay | — (privilege sistemi cihaz içi) | **K009** — HTTPS, token, rate limit (§4) |
| `platform` alanı | Yanlış platform kaydı | — | `tizen` enum'da yok → `other` (§2.2) |

**Kritik ayrım:** Tizen'in privilege matrisi **yalnızca cihaz içindeki** erişimleri denetler.
CoreMusic backend'ine gelen uzak HTTPS isteği bu sistemin **dışındadır** → tamamen §4/K009.
İmza doğrulaması (author/distributor) ise **kurulum anında** çalışır; çalışma zamanındaki
ağ trafiğini korumaz.

**Tehdit modelinin bilinmeyenleri:** TV sürümüne göre privilege matrisi değişebilir
(§1.3) → sürüm-eşlemesi `UNKNOWN`. Samsung TV webview sürümü/yama durumu `UNKNOWN`.

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Her satır bu oturumda **okunmuş** gerçek dosya satırıdır.

### 2.1 Tizen UA tespiti — TV cihaz sınıflandırması

| İddia | Kanıt | Durum |
|---|---|---|
| Tizen UA'sı TV olarak tanınır | `shared/src/Device/DeviceDetector.php:33` → `'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA'` | ✅ OKUNDU |
| JS TV UA regex'i Tizen'i kapsar | `assets.coremusic.net/js/core/breakpoints.js:31` → `RE_TV_UA = /Tizen\|Web0S\|webOS\|SmartTV\|NETTV\|Viera\|BRAVIA\|.../i` | ✅ OKUNDU |
| Tizen `Android` regex'ine girmez | `breakpoints.js:84` → yalnız `/Android/i` | ✅ OKUNDU (dürüst sınır) |
| Android TV ayrı etiket (Tizen'den farklı) | `DeviceDetector.php:34` → `'Android TV'` | ✅ OKUNDU |

**Yorum:** CoreMusic, Tizen'i **yalnızca UA string'i üzerinden** TV olarak sınıflandırır.
UA spoof edilebilir → güvenlik kararı olarak **kullanılamaz** (K009 sınırı).

### 2.2 Veritabanı platform enum'u — `tizen` YOK

`.ai/.sql/mysql/coremusic_media.sql:72` (okundu):

```sql
`platform` enum('windows','linux','macos','android','ios','rpi','other') COLLATE utf8mb4_unicode_ci NOT NULL,
```

- **`tizen` enum'da YOK** → Tizen cihazları `device_types.platform` altında `other`'a
  düşmek zorundadır. **Dürüst negatif kanıt.**

### 2.3 CI — Tizen kapsamı yok

`.github/workflows/ci.yml` bu oturumda okundu:
- L27 · L69 · L112 · L144 · L162 · L180 → **tüm job'lar `runs-on: ubuntu-latest`**
- L22 → `PHP_VERSION: '8.4'`

**Tizen SDK / TV emulator / device farm job'ı YOK.**

### 2.4 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama | Sonuç |
|---|---|
| `tizen-manifest` (repo) | **0 isabet** — Tizen paket manifest'i yok |
| `tizen` (kod yüzeyi) | Yalnız UA tespiti: `DeviceDetector.php:33` + `breakpoints.js:31` (2 dosya) |
| Tizen imza/sertifika kodu (author/distributor) | **0 isabet** |
| `reactos` (kod dosyaları) | **0 isabet** |

**Sonuç:** CoreMusic, Tizen'i **desteklenen bir TV istemcisi** olarak tanır (UA) ama
Tizen'e özgü **hiçbir** güvenlik mekanizması (imza, privilege, manifest) içermez.

### 2.6 Özet — depoda Tizen'e dair ne var / ne yok

| Yüzey | Durum | Kanıt (bu oturumda okundu) |
|---|---|---|
| UA ile TV tespiti | **VAR** | `shared/src/Device/DeviceDetector.php:33` · `assets.coremusic.net/js/core/breakpoints.js:31` |
| JS cihaz tier'ı | **VAR** | `breakpoints.js:31` (`RE_TV_UA`) · `:84` (phone/tablet ayrımı) |
| DB `platform` enum'unda `tizen` | **YOK** (`other`'a düşer) | `.ai/.sql/mysql/coremusic_media.sql:72` |
| Tizen paket manifest'i (`tizen-manifest`) | **YOK** — 0 isabet | grep 2026-10-15 |
| İmza/sertifika kodu (author/distributor) | **YOK** — 0 isabet | grep 2026-10-15 |
| Tizen CI job'ı | **YOK** | `.github/workflows/ci.yml` L27 · L69 · L112 · L144 · L162 · L180 → `ubuntu-latest` |
| Tizen'e özgü güvenlik kodu | **YOK** — 0 isabet | grep 2026-10-15 |

**Tek cümlelik sonuç:** CoreMusic, Tizen'i *tanır* (UA) ama Tizen için *hiçbir şey üretmez*
(paket, imza, servis, test yok) → OS güvenliği tamamen platforma devredilmiştir; privilege
matrisi CoreMusic backend'ini etkilemez.

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; OS kartı güvenlik detayları
`isletim-sistemi-security` dalındadır (envanter `isletim-sistemi-security/index.md`
üzerinden okunmuştur: 24 OS kartı + 15 kod kartı):

| Alt dal | Bridge |
|---|---|
| çekirdek-guvenlik | [`../../isletim-sistemi-security/cekirdek-guvenlik/`](../../isletim-sistemi-security/cekirdek-guvenlik/) |
| yetki-ve-erisim-modeli | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/) |
| uygulama-guvenlik-siniri | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/) |
| kod-ornekleri | [`../../isletim-sistemi-security/kod-ornekleri/`](../../isletim-sistemi-security/kod-ornekleri/) |

**Sınır kuralı:** Tizen imza/privilege modeli K000'dir; CoreMusic'in Tizen cihazına karşı
yükümlülükleri (token, TV UA'ya güvenmeme, HTTPS) §4/K009'dur.

Kardeşler: `[android](../android/android-guvenlik.md)` · `[webos](../webos/webos-guvenlik.md)` ·
`[reactos](../reactos/index.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği.

- Sınır hedefi: [`../../K009-security/index.md`](../../K009-security/index.md)

| Soru | Sahip | Not |
|---|---|---|
| Tizen UA'sına güveniliyor mu? | K009 | §2.1 — UA spoof edilebilir |
| `platform` enum'u `tizen` içermiyor | K000 + Data Engineer | `coremusic_media.sql:72` → `other` |
| Samsung TV'de güvenli storage/cookie | K009 | TV webview davranışı `⚠️ VERIFICATION REQUIRED` |
| Uygulama imzası (author/distributor) | K000 (OS paketleme) | Repo'da sertifika kodu **0 isabet** |
| `requiredPermissions` / privilege başvurusu | K000 (OS manifesti) | Repo'da `tizen-manifest` **0 isabet** |

**Geçiş noktası:** Tizen'in privilege matrisi **cihaz içindeki** erişimleri denetler;
CoreMusic backend'ine gelen HTTPS istekleri privilege sisteminin dışındadır → tamamen K009.

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş**. Generic OWASP/NIST/CIS yok.

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://developer.tizen.org/application-signing-and-certificates/ | Author + distributor imza; public/partner/platform privilege matrisi | ✅ erişildi |
| 2 | https://developer.tizen.org/dev-guide/2.4/application/native/tutorials/details/sec_privileges_n.htm | Native privilege listesi (imza/izin modeli) | ✅ erişildi |
| 3 | https://developer.samsung.com/tizen/certificate-signing/creating-certificate.html | Samsung: sertifika oluşturma akışı | ✅ erişildi |
| 4 | https://developer.samsung.com/smarttv/develop/getting-started/setting-up-sdk/creating-certificates.html | Samsung TV SDK'da sertifika üretimi (author+distributor) | ✅ erişildi |
| 5 | https://github.com/Samsung/tizen-docs/blob/master/docs/application/native/tutorials/details/sec-privileges.md | Privilege dokümanının GitHub kaynağı (cross-check) | ✅ erişildi |
| 6 | https://docs.tizen.org/ | Tizen doküman kökü (erişildi — içerik sınırlı) | ✅ erişildi (kısmi) |

**Elenen / sorunlu URL'ler (dürüst kayıt):**
- `docs.tizen.org` **derin yolları** → SPA iskeleti döndü (içerik yok) → iddialar
  `developer.tizen.org` + `developer.samsung.com` + GitHub kaynağı üzerine kuruldu (önceki dal kaydıyla tutarlı).

---

## §6 — Açık Sorular

1. **Tizen sandbox/seccomp karşılığı:** Düşük seviye izolasyon mekanizmasının tam adı ve
   kapsamı vendor dokümanından doğrulanmadı → `⚠️ VERIFICATION REQUIRED`
2. **Privilege seviyesi:** CoreMusic'in TV uygulaması hangi seviyede (`public`/`partner`/
   `platform`) — buna karşılık gelen izin seti bilinmiyor → `UNKNOWN`
3. **`tizen` platform enum'a eklenmeli mi?** `coremusic_media.sql:72` → şu an `other`.
4. **Sertifika akışı repo'da yok** (grep 0 isabet): Tizen paketleme/imzalama adımı
   CoreMusic'te var mı? `⚠️ VERIFICATION REQUIRED`
5. **CI kapsamı:** Tizen için test yok (`ci.yml` ubuntu-latest) — bilinçli kapsam mı?

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
