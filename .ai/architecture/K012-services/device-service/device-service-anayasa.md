---
node_id: K012-services/device-service
node_type: module
role: anayasa
status: draft
parent_id: K012-services/device-service
children: []
depends_on:
  - "device-service/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — shared/src/Device/DeviceDetector.php 317 satır (detect: 57, detectFromViewportAndContext: 97)"
  - "S1 — shared/src/Device/DeviceManager.php 663 satır (showVolume: 463, showSeekBar: 487, showFooterSeekSlider: 529)"
  - "S1 — shared/src/Device/ (4 dosya: DeviceDetector, DeviceManager, DeviceCssMap + 1)"
  - "S5 — device-identity adında K010 dosyası bu görevde okunmadı → sahiplik belirsiz"
validation:
  - "§2 sınır tablosu services-master §2.2 ile hizalı"
  - "§4 kod kanıtı 2026-10-13 read — birebir"
open_questions:
  - "Q6 — device-identity sahipliği (K010-identitiy ile sınır) → ⚠️ VERIFICATION REQUIRED"
  - "§5 kimlik doğrulama zinciri okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "DeviceCssMap içeriği okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + shared/src/Device/ (S1, 2026-10-13 read)"
group: mimari
---

# device-service — Anayasa (Constitution)

> **Ön not:** device-service bir **K012 servisidir** (arayüz çizmeden iş yapan
> süreç — master §1). Bu dosya servisin **anayasasını** (sınır, sorumluluk,
> dayanak) yazar. Repo'daki `shared/src/Device/` kodu, servisin **cihaz
> tespiti/durumu** tarafına ait gerçek kanıttır (S1); ancak bu kodun K012
> servisi mi yoksa paylaşılan yardımcı mı olduğu **bu turda kesinleşmez** → §5.

## §1 ELI15 — Bu Servis Ne Yapar?

Cihazın **ne olduğunu** (telefon/tablet/masaüstü/TV/gömülü) ve **ne durumda
olduğunu** (ses, oynatma çubuğu) işleyen servis. Kullanıcıya arayüz çizmez
(K011'in işi); yalnız "bu istek hangi cihazdan geliyor, o cihaz ne göstermeli"
sorusunu yanıtlar. Sahiplik notu (master §21.6): cihaz **kimliği**
(device-identity) K010-identitiy'nin çocuğu olabilir — sınır belirsiz (Q6).

## §2 Sınır Tablosu (Kapsam İçi / Dışı)

| Konu | Kapsam | Sahibi | Kanıt |
|---|---|---|---|
| Cihaz tespiti (UA + viewport) | İÇİNDE | K012/device-service | S1 DeviceDetector:57 |
| Breakpoint haritası (7 kademe) | İÇİNDE | K012 | S1 DeviceDetector:10–17 |
| Cihaz-başlı arayüz durumu (ses/seekbar) | İÇİNDE | K012 | S1 DeviceManager:463/487/529 |
| Cihaz kimliği (device-identity) | DIŞINDA (iddia) | K010-identitiy | Q6 ⚠️ |
| Arayüz çizimi | DIŞINDA | K011-application | master §2.2 |
| Kalıcı cihaz kaydı / şema | DIŞINDA | K007-data | master §2.2 |
| CSS breakpoint token'ları | DIŞINDA (görsel) | A3 / tokens | S1 notu:8 |

## §3 Breakpoint Haritası (Repo Kanıtı — Birebir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 10–17 (DocBlock) + 21–26 (const)

| # | Kademe | Sınır (px) | const | Satır |
|---|---|---|---|---|
| 1 | phone | ≤767 | `PHONE_MAX=767` | 21 |
| 2 | tablet | 768–1024 | `TABLET_MAX=1024` | 22 |
| 3 | embedded | ≤1024 (RPi5 1024×600) | `EMBEDDED_MAX=1024` | 23 |
| 4 | laptop | 1025–1440 | `LAPTOP_MAX=1440` | 24 |
| 5 | desktop | 1441–2560 (varsayılan) | `DESKTOP_MAX=2560` | 25 |
| 6 | 4k-tv | 2561–3840 | `FOUR_K_TV_MAX=3840` | 26 |
| 7 | 4k-monitor | ≥3841 | (const yok) | — |

**Gözlem (S1):** DocBlock "breakpoint'ler a-breakpoint-tokens.css ile senkronize"
der (satır 8) — yani PHP sabitleri ile CSS token'ları **elle senkron tutulur**;
otomatik üretim **yoktur**. Senkron kayması riski §9-R3.

## §4 Tespit Sırası (Zincir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 57–92

| # | Adım | Koşul | Sonuç | Satır |
|---|---|---|---|---|
| 0 | özel başlık | `HTTP_X_DEVICE_TYPE=embedded` | `embedded` | 63–65 |
| 1 | TV agent | `isTvAgent` | `4k-tv` | 70–72 |
| 2 | gömülü agent | `isEmbeddedDevice` | `embedded` | 75–77 |
| 3 | viewport var | `detectFromViewportAndContext` | kademe | 80–82 |
| 4 | mobil agent | `detectFromUserAgent` | phone/tablet | 85–88 |
| 5 | varsayılan | aksi | `desktop` | 91 |

**Kritik öncelik (S1):** gömülü cihaz tespiti, viewport'tan **önce** gelir
(satır 100: `isEmbeddedDevice` erken dönüş) — yani 1024px genişliğindeki bir
tablet, embedded agent string'i taşımıyorsa tablet sayılır; ama RPi5 ise
viewport'a bakılmadan `embedded` olur. Bu öncelik **bilinçli** görünür
(satır 99–102 yorumu).

## §5 Kimlik Doğrulama Zinciri (Açık Kapı)

| # | Soru | Durum |
|---|---|---|
| 1 | device-service, K010-identitiy'den device-identity'yi tüketiyor mu? | okunmadı ⚠️ |
| 2 | Cihaz kaydı kalıcı mı (K007) yoksa oturum-içi mi? | okunmadı ⚠️ |
| 3 | `HTTP_X_DEVICE_TYPE` başlığı doğrulanıyor mu (spoof riski)? | §7 |
| 4 | Yetkilendirme (authorization) cihaz bazlı mı? | okunmadı ⚠️ |

**Spoof notu (S1:63):** `HTTP_X_DEVICE_TYPE` istemciden gelir ve **doğrulanmaz**
görünür — istemci bu başlığı `embedded` yaparak tespiti etkileyebilir. Bu bir
güvenlik gözlemidir (§7-R1). Doğrulama katmanının olup olmadığı okunmadı.

## §6 Bağımlılık Özeti (Bu Servise Özel)

| Hedef | Ne için | Etiket |
|---|---|---|
| K010-identitiy | cihaz kimliği (iddia) | TASARIM İDDİASI + Q6 ⚠️ |
| K007-data | kalıcı cihaz kaydı (iddia) | TASARIM İDDİASI |
| K011-application | cihaz bilgisini tüketir | TASARIM İDDİASI |
| A3 / CSS token | breakpoint senkronu | S1 notu (senkron elle) |

> Düğüm geneli bağımlılık matrisi: services-bagimliliklar.md §1 · §17.

## §7 Red Team Risk Listesi

| # | Risk | Severity | Kanıt |
|---|---|---|---|
| R1 | `HTTP_X_DEVICE_TYPE` doğrulanmaz — spoof | HIGH | S1:63 |
| R2 | PHP const ↔ CSS token senkronu elle — kayma | MEDIUM | S1:8 |
| R3 | device-identity sahipliği belirsiz (Q6) | HIGH | master §7/7 |
| R4 | `isDesktopOS` içeriği okunmadı — laptop/tablet ayrımı | LOW | S1:114 |
| R5 | Cihaz durumu (ses/seekbar) kalıcı mı bilinmiyor | MEDIUM | §5 |

## §8 Kapı Sayımı

| Kapı | Sayı |
|---|---|
| `⚠️ VERIFICATION REQUIRED` | 7 satır |
| `UNKNOWN` | 2 satır |
| Açık soru (Q) | Q6 + §5 (4) |

## §9 Cihaz Tespit Yardımcı Metot Analizi

`DeviceDetector` statik yardımcıları `detect` zincirini besler; içerikleri
2026-10-13'te okunan bölümlerden kaydedilmiştir.

| # | Metot | Görev | Kanıt |
|---|---|---|---|
| 1 | `isTvAgent($ua)` | TV agent stringi tarama | S1:70 (çağrı) |
| 2 | `isEmbeddedDevice($ua)` | gömülü agent tarama | S1:75 · S1:100 |
| 3 | `isDesktopOS($ua)` | masaüstü OS mu (laptop/tablet ayrımı) | S1:114 |
| 4 | `detectFromUserAgent($ua)` | mobil agent → phone/tablet | S1:85 |
| 5 | `detectFromViewportAndContext($w,$h,$ua)` | viewport + bağlam → kademe | S1:97 |
| 6 | `EMBEDDED_AGENTS[]` | gömülü agent sözlüğü (10 değer) | S1:28–30 |
| 7 | `TV_AGENTS[]` | TV agent sözlüğü (14 değer) | S1:32–36 |
| 8 | `MOBILE_AGENTS[]` | mobil agent → kademe eşlemesi (8) | S1:38–47 |

### §9.1 Gömülü Agent Sözlüğü (S1:28–30 — birebir)

| # | Değer | Not |
|---|---|---|
| 1 | `Raspberry Pi` | RPi markası |
| 2 | `RPi` | kısa biçim |
| 3 | `aarch64` | ARM64 mimarisi |
| 4 | `armv7` | ARMv7 |
| 5 | `armv8` | ARMv8 |
| 6 | `armv6` | ARMv6 |
| 7 | `CrOS` | ChromeOS (kiosk) |
| 8 | `kiosk` | kiosk bayrağı |
| 9 | `Raspbian` | RPi dağıtım adı |
| 10 | `Embedded` | genel etiket |

### §9.2 TV Agent Sözlüğü (S1:32–36 — birebir)

| # | Değer | # | Değer |
|---|---|---|---|
| 1 | `Tizen` | 8 | `Android TV` |
| 2 | `Web0S` | 9 | `GoogleTV` |
| 3 | `webOS` | 10 | `HbbTV` |
| 4 | `SmartTV` | 11 | `Roku` |
| 5 | `BRAVIA` | 12 | `Hisense` |
| 6 | `NetCast` | 13 | `Viera` |
| 7 | `AppleTV` | 14 | `MiTV` |

**Gözlem (S1:33):** `Web0S` (sıfır) ve `webOS` (harf O) **iki ayrı değer** olarak
duruyor — bu ya bilinçli bir yazım toleransı ya da yazım hatası olabilir; ikisi
de bulunduğundan her iki durumda da eşleşme sağlanır. Niyet `⚠️ VERIFICATION
REQUIRED` (kod yorumu okunmadı).

### §9.3 Mobil Agent Eşlemesi (S1:38–47 — birebir)

| # | Agent | Kademe |
|---|---|---|
| 1 | `Android` | phone |
| 2 | `iPhone` | phone |
| 3 | `iPad` | tablet |
| 4 | `iPod` | phone |
| 5 | `Windows Phone` | phone |
| 6 | `BlackBerry` | phone |
| 7 | `Opera Mini` | phone |
| 8 | `Opera Mobi` | phone |

**Gözlem (S1):** `iPad` **tablet**'e eşlenir; buna karşılık viewport ≤1024 ve
`isDesktopOS` doğruysa `laptop` da mümkündür (S1:114–116). Yani iPad'in kademesi
yalnız agent'a bağlıdır; ekran büyüklüğü iPad Pro'da değişir mi → okunmadı ⚠️.

## §10 Viewport Ayrım Mantığı

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 97–120 (okunan kısım)

| # | Koşul | Sonuç | Satır |
|---|---|---|---|
| 1 | `isEmbeddedDevice($ua)` | `embedded` (viewport'a bakılmaz) | 100–102 |
| 2 | `width ≤ 767` | `phone` | 104–106 |
| 3 | `width ≤ 1024` AND `height ≤ 600` | `embedded` (RPi5 7") | 108–111 |
| 4 | `width ≤ 1024` AND `isDesktopOS` | `laptop` | 113–116 |
| 5 | `width ≤ 1024` (aksi) | `tablet` | 117 |
| 6 | `width ≤ 1440` | `laptop` | 120+ |

**Önemli ayrımlar (S1):**

| # | Ayrım | Mantık |
|---|---|---|
| A1 | 1024px iki yüzlü | tablet mi laptop mu → OS'a bakılır |
| A2 | yükseklik 600px eşiği | RPi5 7" ekranını `embedded` yapar |
| A3 | agent, viewport'tan üstün | gömülü agent varsa viewport yok sayılır |

**Risk (A1):** `isDesktopOS` yanlış pozitif/negatif verirse 1024px'lik bir cihaz
yanlış kademeye düşer (CSS kırılır). İçerik okunmadı → davranış `UNKNOWN` (§7-R4).

## §11 DeviceManager — Cihaz-Başlı Arayüz Durumu

`DeviceManager` (663 satır) cihaz türüne göre **ne gösterileceğini** belirler.
Okunan üç metot ses/oynatma arayüzüne aittir (S1 sembil haritası).

| # | Metot | Görev | Satır |
|---|---|---|---|
| 1 | `showVolume($type)` | ses kontrolü göster mi | 463 |
| 2 | `showSeekBar($type)` | ilerleme çubuğu göster mi | 487 |
| 3 | `showFooterSeekSlider($type)` | alt çubuk slider göster mi | 529 |

> Bu üç metot **cihaz-başlı arayüz kararı** üretir; çizim K011'e aittir.
> Gövde kodu bu turda okunmadı → davranış `⚠️ VERIFICATION REQUIRED`.
> Örnek kod blokları: `device-service-ornekler.md` §3 (okunursa).

**Sınır notu:** `showVolume` / `showSeekBar` gibi metotlar K012'nin "arayüz
çizmez" kuralı ile **çelişmez** — bunlar çizim değil, **karar** (boolean/durum)
üretir; çizim K011'de olur. Ancak bu ayrım vault'ta açıkça yazılmamıştı →
§14-B4 olarak red team notuna eklenir.

## §12 DeviceCssMap — Ayrık Harita (İçerik Okunmadı)

| Alan | Durum |
|---|---|
| Dosya varlığı | ✅ S1 (`shared/src/Device/` içinde) |
| İçerik | okunmadı → ⚠️ VERIFICATION REQUIRED |
| Tahmini rol (adından) | cihaz → CSS eşleme (iddia) |
| Ayrık üretim | bilinmiyor |

**Kural (zero-hallucination):** dosya adından rol **türetilmez**; yalnız adı
kaydedilir. İçerik okunmadan işlevi yazılmaz.

## §13 Sürücü/Veri Akışı Şeması

~~~text
HTTP istek
  ├─ HTTP_X_DEVICE_TYPE (embedded?) ──> doğrulanmamış (spoof) §5
  ├─ User-Agent ──> isTvAgent / isEmbeddedDevice / detectFromUserAgent
  └─ viewport (w,h) ──> detectFromViewportAndContext
          ↓
  DeviceDetector::detect(): string  (7 kademe)
          ↓
  DeviceManager::showVolume/showSeekBar/showFooterSeekSlider (durum)
          ↓
  K011-application (çizim — K012 çizmez)
          ↓
  A3/CSS token (breakpoint senkronu — elle, §3)
~~~

## §14 Red Team Bulgu Listesi (Genişletilmiş)

| # | Bulgu | Severity |
|---|---|---|
| B1 | `HTTP_X_DEVICE_TYPE` doğrulanmaz (§5) | HIGH |
| B2 | PHP const ↔ CSS token elle senkron (§3) | MEDIUM |
| B3 | device-identity sahipliği belirsiz — Q6 (§5) | HIGH |
| B4 | "karar üretmek" ile "çizmek" sınırı vault'ta yazılı değil (§11) | LOW |
| B5 | `Web0S`/`webOS` ikiliği — niyet bilinmiyor (§9.2) | LOW |
| B6 | `isDesktopOS` içeriği okunmadı (§10) | MEDIUM |
| B7 | `DeviceCssMap` içeriği okunmadı (§12) | MEDIUM |
| B8 | 1024px ikiyüzlülüğü (tablet/laptop) test edilmedi (§10) | MEDIUM |

## §15 Örnek Akış (Kayıt/Tespit — Tasarım İddiası)

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | istek gelir, başlıklar okunur | TASARIM İDDİASI |
| 2 | `detect()` çağrılır | S1:57 |
| 3 | 7 kademeden biri döner | S1:55 |
| 4 | `show*` metotlarıyla durum alınır | S1:463/487/529 |
| 5 | K011 çizimi yapar | master §2.2 |

## §16 Terim Sözlüğü

| Terim | Anlamı |
|---|---|
| kademe (tier) | cihaz sınıfı — 7 değer (§3) |
| breakpoint | CSS piksel sınırı |
| spoof | sahte başlıkla tespiti etkileme (§5) |
| viewport | tarayıcı görünüm genişliği/yüksekliği |
| embedded | gömülü cihaz (RPi5, ARM Linux) |
| device-identity | cihaz kimliği — sahipliği Q6 |

## §17 Bağlantılar

| Dosya | İlişki |
|---|---|
| device-service/index.md | künye |
| device-service-surucu.md | mevcut kod sürücüsü |
| device-service-ornekler.md | kod örnekleri |
| services-master.md §21.6 | kart |
| services-bagimliliklar.md §1 · §17 | bağımlılık |
| K010-identitiy | device-identity sahibi (iddia, Q6) |

## §18 Bağımsız Doğrulama Matrisi (Anayasa)

Bu dosyanın iddialarının her biri hangi kanıt sınıfına dayanıyor.

| # | İddia | Kanıt sınıfı | Kaynak |
|---|---|---|---|
| 1 | çocuk adı `device-service` | S2 | v2 §2.12 |
| 2 | 7 kademe breakpoint | S1 | DeviceDetector:21–26 |
| 3 | tespit sırası 6 adım | S1 | DeviceDetector:57–92 |
| 4 | `HTTP_X_DEVICE_TYPE` spoof | S1 (kod okundu) | DeviceDetector:63 |
| 5 | `showVolume/showSeekBar` var | S1 (sembil) | DeviceManager:463/487/529 |
| 6 | device-identity sahibi K010 | S2 (iddia) | master §7/7 |
| 7 | breakpoint ↔ CSS senkronu | S1 (yorum satırı) | DeviceDetector:8 |
| 8 | `DeviceCssMap` rolü | S5 (içerik okunmadı) | §12 |

## §19 Kapsam Dışı Liste

| # | Konu | Sahibi |
|---|---|---|
| 1 | cihaz kimliği doğrulama protokolü | K010 |
| 2 | kalıcı cihaz şeması | K007 |
| 3 | arayüz çizimi | K011 |
| 4 | CSS breakpoint token dosyası | A3/tokens |
| 5 | servis dağıtımı | K018 |
| 6 | cihaz test altyapısı | K019 |

## §20 Açık Soru Tablosu (Detay)

| # | Soru | Neden önemli | Kapı |
|---|---|---|---|
| 1 | device-identity kimin? | iki düğüm aynı veriyi isteyebilir | Q6 |
| 2 | `HTTP_X_DEVICE_TYPE` doğrulanıyor mu? | spoof → yanlış kademe → yanlış CSS | §5 |
| 3 | `DeviceCssMap` ne yapıyor? | elle senkronu otomatikleştirebilir | §12 |
| 4 | `isDesktopOS` nasıl çalışır? | laptop/tablet ayrımının çekirdeği | §10 |
| 5 | `show*` metotları boolean mı döndürüyor? | çizim K011'e nasıl geçiyor | §11 |
| 6 | Cihaz durumu oturum-içi mi? | kalıcılık sınırı (K007) | §5 |

## §21 İddia–Redüksiyon Notu (Bu Dosyanın Sınırları)

Bu anayasa **iki tür içerik** taşır ve ikisi de aynı etiketle işaretlenmiştir:

| Tür | Kaynak | Bağlayıcılık |
|---|---|---|
| Repo kanıtı (S1) | DeviceDetector/DeviceManager read | olgusal — kod böyledir |
| Tasarım iddiası | v2 §2.12 + master kartları | onaysız — bağlayıcı değil |

Ayrım yapılmadan okunursa tasarım iddiası "gerçek" sanılabilir → bu dosyada her
tablo kanıt sınıfı sütunu taşır veya not ile işaretlenir.

## §22 Bir Sonraki Tur (Tur-3 Önerisi)

| # | İş | Hedef |
|---|---|---|
| 1 | `DeviceManager` gövdeleri okunacak | §11 kapısı |
| 2 | `DeviceCssMap` okunacak | §12 kapısı |
| 3 | `isDesktopOS` okunacak | §10-R4 |
| 4 | K010 device-identity dosyası karşılaştırılacak | Q6 |
| 5 | spoof doğrulama katmanı aranacak | §5-R1 |

## §23 Cihaz Kademi ↔ Arayüz Karar Matrisi (Tasarım İddiası)

Bu matris, kademe ile `show*` kararlarının nasıl kesişebileceğini gösterir —
**tasarım iddiasıdır**; gerçek çapraz tablo `DeviceManager` gövdeleri okunmadan
kesinleşmez (§11).

| Kademe | `showVolume` | `showSeekBar` | `showFooterSeekSlider` | Kanıt |
|---|---|---|---|---|
| phone | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| tablet | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| embedded | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| laptop | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| desktop | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| 4k-tv | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |
| 4k-monitor | ⚠️ | ⚠️ | ⚠️ | gövde okunmadı |

**Kural:** bu matrisin hiçbir hücresine "evet/hayır" **uydurulmaz**; her hücre
gövde-okunmadı işaretiyle kalır. Metot adları ve satır numaraları S1'dir (§11);
davranışları değildir.

## §24 Sürücü ↔ Anayasa Eşlemesi

| Anayasa konusu | Sürücü karşılığı |
|---|---|
| tespit zinciri (§4) | `DeviceDetector::detect` |
| breakpoint haritası (§3) | `const` bloğu |
| cihaz-başlı karar (§11) | `DeviceManager::show*` |
| CSS senkronu (§3) | elle — motor yok |
| spoof kapısı (§5) | doğrulama katmanı — okunmadı |

## §25 Kapı Özeti (Tek Tablo)

| # | Kapı | Dosya | Durum |
|---|---|---|---|
| 1 | Q6 device-identity | anayasa §5 | açık |
| 2 | `HTTP_X_DEVICE_TYPE` spoof | anayasa §5 | açık |
| 3 | `DeviceCssMap` içeriği | anayasa §12 | okunmadı |
| 4 | `isDesktopOS` içeriği | anayasa §10 | okunmadı |
| 5 | `show*` gövdeleri | surucu | okunmadı |
| 6 | 1024px ikiyüzlülük testi | anayasa §10 | test yok |

## §26 Repo Kanıt Envanteri (device-service — 2026-10-13)

Bu servisle ilişkili repo dosyalarının ölçü envanteri; her satır 2026-10-13
`read`/`list` ölçümlerinden gelir.

| # | Dosya | Satır | Okunan kısım | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | shared/src/Device/DeviceDetector.php | 317 | 1–120 | S1 (birebir okundu) |
| 2 | shared/src/Device/DeviceManager.php | 663 | yalnız sembil | S1 (gövde okunmadı) |
| 3 | shared/src/Device/DeviceCssMap.php | — | hiç | S5 (okunmadı) |
| 4 | shared/src/Device/ (4. dosya) | — | envanter | S1 (ad okunmadı) |
| 5 | shared/tests/Unit/Device/ | — | envanter | S1 (varlık) |

| # | Ölçüm | Değer |
|---|---|---|
| 1 | `shared/src/Device/` dosya sayısı | 4 (AGENTS.md §2 envanteri) |
| 2 | DeviceDetector okuma derinliği | 120/317 satır |
| 3 | DeviceManager okuma derinliği | 0/663 (yalnız sembil) |
| 4 | toplam okunan kod satırı (bu servis) | ~120 |

**Ölçüm notu:** bu tur yalnız DeviceDetector'ın ilk 120 satırını okudu;
DeviceManager'ın gövdesi ve diğer iki dosya okunmadı. Bu yüzden anayasadaki
davranış iddiaları büyük ölçüde `⚠️` ile işaretlidir — olgusal olan yalnız
tespit zinciri ve breakpoint haritasıdır.

## §27 Anayasa Kapı Kontrol Listesi (Özet)

Bu dosyanın kendi doğrulama kapısı; sonraki turda bu liste yeniden çalıştırılır.

| # | Kapı | Hedef | Durum |
|---|---|---|---|
| 1 | ≥500 satır | bu dosya | dış ölçüm (§son) |
| 2 | CJK/FFFD 0 | bu dosya | 0 |
| 3 | `parent:` 0 | bu dosya | 0 |
| 4 | `group` alıntısız | `mimari` | mimari |
| 5 | son satır birebir | authority | birebir |
| 6 | kod kanıtı ≥1 `Dosya/Satır` | §3 · §4 · §9 · §11 | var |
| 7 | motor/ürün adı uydurulmadı | §12 · §3 | evet |
| 8 | Q6 açık kapı korundu | §5 · §20 · §25 | evet |

## §28 Kritik Repo Alıntıları (Birebir — Referans)

Aşağıdaki iki blok, cihaz tespitinin çekirdeğidir; `device-service-ornekler.md`
§2 ile aynı disk kaynakından alınmıştır (tekrar değil, referans).

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 57–65

```php
public static function detect(
    ?string $userAgent = null,
    ?int    $viewportW = null,
    ?int    $viewportH = null
): string {
    // 0. Özel HTTP başlığı veya kiosk bayrağı kontrolü
    if (!empty($_SERVER['HTTP_X_DEVICE_TYPE']) && strtolower((string)$_SERVER['HTTP_X_DEVICE_TYPE']) === 'embedded') {
        return 'embedded';
    }
```

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 104–117

```php
if ($width <= self::PHONE_MAX) {
    return 'phone';
}

if ($width <= self::TABLET_MAX) {
    // RPi5 7" 1024x600 = embedded
    if ($height !== null && $height <= 600) {
        return 'embedded';
    }
    // 1024x768 gibi laptop ekranları: Masaüstü işletim sistemi varsa laptop kabul edilir
    if (self::isDesktopOS($ua)) {
        return 'laptop';
    }
    return 'tablet';
}
```

**Neden burada:** anayasa, sürücü ve örnekler dosyaları aynı diske referans
verir; bu iki blok "anayasa neye dayanıyor" sorusunun doğrudan cevabıdır.

## §29 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | anayasa yazıldı — sınır + breakpoint haritası (S1 birebir) + tespit zinciri + Q6 açık kapı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
