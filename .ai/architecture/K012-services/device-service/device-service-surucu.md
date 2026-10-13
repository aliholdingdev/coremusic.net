---
node_id: K012-services/device-service
node_type: module
role: surucu
status: draft
parent_id: K012-services/device-service
children: []
depends_on:
  - "device-service/device-service-anayasa.md"
  - "device-service/index.md"
evidence:
  - "S1 — shared/src/Device/DeviceManager.php 663 satır (showVolume:463, showFullMetadata:471, showSidebar:479, showSeekBar:487, showPlaylistToggle:495, showPodcastWidget:503, showRadioWidget:511, showUtilityIcons:520, showFooterSeekSlider:529, shouldRenderWelcomePopup:539, isSupportedResolution:555, shouldShowFallback:565, shouldRender4kLayout:579, shouldRenderEmbeddedLayout:589)"
  - "S1 — shared/src/Device/DeviceDetector.php 317 satır (detect: 57)"
  - "S5 — device-identity doğrulama katmanı bu dosyada arandı, okunmadı"
validation:
  - "§2 · §3 · §4 kod blokları 2026-10-13 read — birebir kopya"
  - "§9 tier tablosu DeviceManager:544–550 DocBlock'undan"
open_questions:
  - "Q6 — device-identity sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "isPhone/isEmbedded/isWide/isLaptop gövdeleri okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "widgetCount() içeriği okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: shared/src/Device/DeviceManager.php (S1, 2026-10-13 read)"
group: mimari
---

# device-service — Sürücü (Driver)

> **Bu dosya ne yapar:** device-service'in **gerçek sürücüsünü** — cihaz
> tespitine göre "ne gösterileceğine" karar veren `DeviceManager` metotlarını —
> adım adım tarif eder. Her karar metodu **boolean** döndürür; çizim
> K011-application'a devredilir (anayasa §11 · §14-B4).

## §1 Sürücü Tanımı

| Alan | Değer | Kanıt |
|---|---|---|
| Ana sürücü | `DeviceManager` (14 public metot) | S1 463–599 |
| Girdi | cihaz türü (detector çıktısı) + viewport | S1 (detector:57) |
| Çıktı | boolean kararlar (`show*` / `shouldRender*`) | S1 463–599 |
| Tespit kaynağı | `DeviceDetector::detect()` | S1:57 |
| Kimlik doğrulama sürücüsü | okunmadı | ⚠️ (§6) |
| Kalıcı durum sürücüsü | kanıt yok | ⚠️ UNKNOWN (K007 sınırı) |

## §2 Sürücü Metot Envanteri (14/14 — Sembil)

Dosya: `shared/src/Device/DeviceManager.php` / Satır: 463–599

| # | Metot | Çıktı | Satır |
|---|---|---|---|
| 1 | `showVolume()` | bool | 463 |
| 2 | `showFullMetadata()` | bool | 471 |
| 3 | `showSidebar()` | bool | 479 |
| 4 | `showSeekBar()` | bool | 487 |
| 5 | `showPlaylistToggle()` | bool | 495 |
| 6 | `showPodcastWidget()` | bool | 503 |
| 7 | `showRadioWidget()` | bool | 511 |
| 8 | `showUtilityIcons()` | bool | 520 |
| 9 | `showFooterSeekSlider()` | bool | 529 |
| 10 | `shouldRenderWelcomePopup()` | bool | 539 |
| 11 | `isSupportedResolution()` | bool | 555 |
| 12 | `shouldShowFallback()` | bool | 565 |
| 13 | `shouldRender4kLayout()` | bool | 579 (deprecated 2.0.1) |
| 14 | `shouldRenderEmbeddedLayout()` | bool | 589 |

## §3 Sürücü Gövdeleri (Birebir — 2026-10-13)

### §3.1 showVolume — Satır: 463–466

```php
public function showVolume(): bool
{
    return !$this->isPhone();
}
```

### §3.2 showFullMetadata — Satır: 471–474

```php
public function showFullMetadata(): bool
{
    return !$this->isEmbedded() && !$this->isPhone();
}
```

### §3.3 showSidebar — Satır: 479–482

```php
public function showSidebar(): bool
{
    return $this->isWide() || $this->isLaptop();
}
```

### §3.4 showSeekBar — Satır: 487–490

```php
public function showSeekBar(): bool
{
    return true; // tüm cihazlarda göster
}
```

### §3.5 showPlaylistToggle — Satır: 495–498

```php
public function showPlaylistToggle(): bool
{
    return !$this->isPhone();
}
```

### §3.6 showUtilityIcons — Satır: 520–523

```php
public function showUtilityIcons(): bool
{
    return !$this->isPhone();
}
```

### §3.7 showFooterSeekSlider — Satır: 529–532

```php
public function showFooterSeekSlider(): bool
{
    return !$this->isPhone();
}
```

### §3.8 shouldRenderWelcomePopup — Satır: 539–542

```php
public function shouldRenderWelcomePopup(): bool
{
    return $this->isEmbedded1024();
}
```

### §3.9 shouldShowFallback / shouldRender4kLayout — Satır: 565–582

```php
public function shouldShowFallback(): bool
{
    return false;
}

/**
 * 4K özel DOM tier KALDIRILDI (v2.0.1 — 4K ölçek sözleşmesi):
 * ...
 * @deprecated 2.0.1 4K DOM tier kaldırıldı — daima false döner.
 */
public function shouldRender4kLayout(): bool
{
    return false;
}
```

## §4 Karar Tablosu (gövde — olgusal)

| # | Metot | Koşul | Sonuç | Kanıt |
|---|---|---|---|---|
| 1 | `showVolume` | `!isPhone` | phone HARİÇ | S1:465 |
| 2 | `showFullMetadata` | `!isEmbedded && !isPhone` | gömülü+phone HARİÇ | S1:473 |
| 3 | `showSidebar` | `isWide || isLaptop` | yalnız geniş/laptop | S1:481 |
| 4 | `showSeekBar` | sabit `true` | TÜM cihazlar | S1:489 |
| 5 | `showPlaylistToggle` | `!isPhone` | phone HARİÇ | S1:497 |
| 6 | `showPodcastWidget` | `isWide` | yalnız geniş | S1:505 |
| 7 | `showRadioWidget` | `widgetCount() >= 5` | sayaç ≥5 | S1:513 |
| 8 | `showUtilityIcons` | `!isPhone` | phone HARİÇ | S1:522 |
| 9 | `showFooterSeekSlider` | `!isPhone` | phone HARİÇ | S1:531 |
| 10 | `shouldRenderWelcomePopup` | `isEmbedded1024` | yalnız 1024 gömülü | S1:541 |
| 11 | `isSupportedResolution` | sabit `true` | her zaman | S1:557 |
| 12 | `shouldShowFallback` | sabit `false` | hiç | S1:567 |
| 13 | `shouldRender4kLayout` | sabit `false` (deprecated) | hiç | S1:581 |
| 14 | `shouldRenderEmbeddedLayout` | `!phone && (embedded||tablet||w<=1024)` | gömülü/tablet | S1:589–599 |

## §5 Sürücü Şeması

~~~text
DeviceDetector::detect()  (anayasa §4)
        ↓ cihaz türü
DeviceManager (state: $this->isPhone()/isEmbedded()/isWide()/...)
        ↓ boolean kararlar
  showVolume / showSeekBar / showFooterSeekSlider / ...
        ↓
K011-application → çizim (CSS/A3 token)
~~~

**Bağımlılık notu (S1):** `DeviceManager` kararları `isPhone`, `isEmbedded`,
`isTablet`, `isWide`, `isLaptop`, `isEmbedded1024` **sorgularına** dayanır;
bu sorgu gövdeleri **okunmadı** → `⚠️ VERIFICATION REQUIRED`. Yani bu dosyadaki
"phone HARİÇ" sonuçları, `isPhone()`'un nasıl çalıştığına bağlıdır (detector'a
bağlılık iddiası, §7).

## §6 Doğrulama / Kimlik Sürücüsü (Açık Kapı)

| # | Konu | Durum |
|---|---|---|
| 1 | `HTTP_X_DEVICE_TYPE` spoof doğrulaması | okunmadı ⚠️ (anayasa §5) |
| 2 | cihaz kaydı oturum-içi mi, kalıcı mı | kanıt yok ⚠️ UNKNOWN |
| 3 | yetkilendirme cihaz bazlı mı | okunmadı ⚠️ |

## §7 Deprecated / Sözleşme Notları (Repo — Birebir)

| # | Not | Kanıt |
|---|---|---|
| 1 | `shouldRender4kLayout` deprecated (2.0.1) — 4K DOM tier kaldırıldı | S1:576–578 |
| 2 | ≥2561px Wide (1920 tabanlı) markup render eder | S1:572 |
| 3 | 4K uyumluluğu `d-4k.css` (≥3840px zoom) ile | S1:573 |
| 4 | `ScaleManager.js` ≥2561px'te `skip:true` | S1:574 |
| 5 | `shouldRender4kLayout` imzası çağıranlar için korunur | S1:577 |

**Sözleşme koruma notu (S1:577):** deprecated metot **silinmemiş**, daima `false`
döndürecek şekilde bırakılmış; imza `header.php`, `footer.php`, `pages/home.php`
çağrıları için korunuyor. Bu bir uyum (backward-compat) stratejisidir — olgusal.

## §8 Tier Şeması (DeviceManager DocBlock — S1:544–550)

| Tier | Kademe | Çözünürlük | Mockup |
|---|---|---|---|
| 1 | Embedded / Tablet | 1024px | Image 2 |
| 2 | Desktop & Standart | 1920 FHD, 2K, 3K, Laptops | Image 3 |
| 3 | 4K TV & Monitors | 3840px | Scaled 4K |
| 4 | Phone | ≤767px | Mobile |

> Bu tablo DeviceManager içi bir DocBlock'tan alınmıştır (S1:544–550) — kod
> içi dokümantasyondur, ayrı bir vault iddiası değildir.

## §9 Red Team Bulgu Listesi

| # | Bulgu | Severity |
|---|---|---|
| 1 | `isPhone/isEmbedded/isWide` gövdeleri okunmadı — tüm kararlar dolaylı | HIGH |
| 2 | `HTTP_X_DEVICE_TYPE` spoof doğrulaması görünmüyor (anayasa §5) | HIGH |
| 3 | `showRadioWidget` `widgetCount()>=5` — sayaç kaynağı okunmadı | MEDIUM |
| 4 | `shouldRender4kLayout` deprecated ama imza korunuyor — çağıranlar | LOW |
| 5 | Kalıcı cihaz durumu yok — oturum-içi (K007 sınırı) | MEDIUM |

## §10 Açık Sorular

| # | Soru | Kapı |
|---|---|---|
| 1 | Q6 device-identity kimin? | anayasa §5 |
| 2 | `isPhone()` nasıl hesaplıyor? | §5 (gövde okunmadı) |
| 3 | `widgetCount()` ne sayıyor? | §9-3 |
| 4 | `isEmbedded1024()` viewport mu agent mı? | §5 |
| 5 | spoof doğrulama katmanı var mı? | §6 |

## §11 Kalan Metot Gövdeleri (Birebir — 2026-10-13)

### §11.1 showPodcastWidget / showRadioWidget — Satır: 503–514

```php
public function showPodcastWidget(): bool
{
    return $this->isWide();
}

public function showRadioWidget(): bool
{
    return $this->widgetCount() >= 5;
}
```

### §11.2 isSupportedResolution — Satır: 555–558

```php
public function isSupportedResolution(): bool
{
    return true;
}
```

### §11.3 shouldRenderEmbeddedLayout (başlangıç) — Satır: 589–599

```php
public function shouldRenderEmbeddedLayout(): bool
{
    if ($this->isPhone()) {
        return false;
    }

    if ($this->isEmbedded() || $this->isTablet()) {
        return true;
    }

    if ($this->viewportW !== null && $this->viewportW <= 1024) {
```

**Gözlem (S1:599):** `shouldRenderEmbeddedLayout` üç kademeli bir filtredir —
önce phone'u **elemer**, sonra embedded/tablet'i **kabul eder**, en son
viewport ≤1024'a düşer. Gövde bu satırda **kesiliyor** (okuma limiti); kalan
satırlar okunmadı → `⚠️ VERIFICATION REQUIRED` (devamı ne yapıyor bilinmiyor).

### §11.4 showVolume DocBlock (öncesi) — Satır: 460–462

```php
     * PNG home-1024 footer'da volume VAR (hoparlör + slider + %100) —
     * embedded dahil gösterilir; yalnız phone'da gizlenir.
     */
```

**Kanıt notu (S1:460):** bu DocBlock, kararın **PNG mockup** (`.ai/.png`) ile
gerekçelendirildiğini gösterir — yani `showVolume` kararı görsel kanıta dayanır,
soyut kurala değil. Bu, mockup-gate disipliniyle (kök AGENTS.md §13) tutarlıdır.

## §12 Karar Davranış Tipi Sınıflandırması

14 metot üç davranış tipine ayrılır; bu sınıflandırma kod gövdesinden
türetilmiştir (S1).

| # | Tip | Davranış | Metotlar |
|---|---|---|---|
| 1 | koşullu | cihaz sorgusuna bağlı | showVolume, showFullMetadata, showSidebar, showPlaylistToggle, showPodcastWidget, showUtilityIcons, showFooterSeekSlider, shouldRenderWelcomePopup, shouldRenderEmbeddedLayout |
| 2 | sayaç-bağlı | widget sayısına bağlı | showRadioWidget |
| 3 | sabit | daima aynı değer | showSeekBar (true), isSupportedResolution (true), shouldShowFallback (false), shouldRender4kLayout (false, deprecated) |

**Sabit-davranış gözlemi (S1):** 4 metot sabit değer döndürür. Bunlardan
`shouldRender4kLayout` açıkça **deprecated** (§7); `shouldShowFallback` ve
`isSupportedResolution` ise **gelecekte değişebilir** iması taşıyan DocBlock'lara
sahiptir (S1:561–563). Sabit metotların neden korunduğu: çağıranların imzası
bozulmasın (backward-compat) — bu bir tasarım kararıdır, olgudur.

## §13 Cihaz Sorgu Bağımlılığı (Dolaylı Kararlar)

Şu sorgular bu dosyadaki kararların **ortak bağımlılığıdır**; gövdeleri
okunmadığı için her sonuç dolaylıdır (§5 · §9-1).

| # | Sorgu | Kullanan metotlar | Gövde |
|---|---|---|---|
| 1 | `isPhone()` | showVolume, showFullMetadata, showPlaylistToggle, showUtilityIcons, showFooterSeekSlider, shouldRenderEmbeddedLayout | okunmadı ⚠️ |
| 2 | `isEmbedded()` | showFullMetadata, shouldRenderEmbeddedLayout | okunmadı ⚠️ |
| 3 | `isTablet()` | shouldRenderEmbeddedLayout | okunmadı ⚠️ |
| 4 | `isWide()` | showSidebar, showPodcastWidget | okunmadı ⚠️ |
| 5 | `isLaptop()` | showSidebar | okunmadı ⚠️ |
| 6 | `isEmbedded1024()` | shouldRenderWelcomePopup | okunmadı ⚠️ |
| 7 | `widgetCount()` | showRadioWidget | okunmadı ⚠️ |

**Kritik bağımlılık (S1):** tüm cihaz kararları bu yedi sorguya iner. Bu
sorgular `DeviceDetector`'ın 7 kademesine bağlıysa zincir tutarlıdır; bağımsız
hesaplanıyorsa **iki ayrı cihaz modeli** riski doğar → bu bağ okunmadı →
`⚠️ VERIFICATION REQUIRED` (§14-B2).

## §14 Red Team Bulgu Listesi (Genişletilmiş)

| # | Bulgu | Severity |
|---|---|---|
| B1 | 7 cihaz sorgusu gövdesi okunmadı — tüm kararlar dolaylı (§13) | HIGH |
| B2 | detector kademesi ile manager sorguları arası bağ okunmadı (§13) | HIGH |
| B3 | `HTTP_X_DEVICE_TYPE` spoof doğrulanmıyor (anayasa §5) | HIGH |
| B4 | `shouldRenderEmbeddedLayout` gövdesi yarım okundu (§11.3) | MEDIUM |
| B5 | `widgetCount()` kaynağı bilinmiyor (§13-7) | MEDIUM |
| B6 | kararlar PNG mockup'a dayanıyor — salt okunur SSOT'a bağımlılık (§11.4) | LOW |
| B7 | 4 sabit metot — değişmeyecek mi (§12) | LOW |
| B8 | kalıcı cihaz durumu yok (K007 sınırı) | MEDIUM |

## §15 Sürücü → Anayasa Eşlemesi

| Sürücü konusu | Anayasa karşılığı |
|---|---|
| 14 karar metodu (§2) | §11 (karar üretir, çizmez) |
| cihaz sorguları (§13) | §5 (kimlik/tespit zinciri) |
| spoof kapısı (§6) | §5 · §7-R1 |
| tier DocBlock (§8) | §3 (breakpoint haritası) |
| deprecated 4K (§7) | §3 (4k kademesi) |

## §16 Kapı Özeti

| # | Kapı | Durum |
|---|---|---|
| 1 | Q6 device-identity | açık |
| 2 | 7 cihaz sorgusu gövdesi | okunmadı |
| 3 | `shouldRenderEmbeddedLayout` devamı | yarım okundu |
| 4 | spoof doğrulama | okunmadı |
| 5 | `widgetCount()` kaynağı | okunmadı |

## §17 Bağlantılar

| Dosya | İlişki |
|---|---|
| device-service-anayasa.md | önce okunur |
| device-service-ornekler.md | kod blokları |
| device-service/index.md | künye |
| services-master.md §21.6 | kart |

## §18 Karar → Etkilenen Arayüz Yüzeyi (Tasarım İddiası)

Bu tablo, hangi kararın hangi arayüz yüzeyini etkileyeceğini gösterir —
**tasarım iddiasıdır**; gerçek DOM etkisi K011'de okunmalıdır.

| # | Karar | Etkilediği yüzey (iddia) | Kanıt |
|---|---|---|---|
| 1 | `showVolume` | ana oynatıcı ses kontrolü | S1:460 (PNG notu) |
| 2 | `showFullMetadata` | bitrate/süre metadata satırı | S1:469 |
| 3 | `showSidebar` | Göz At sayfası kenar çubuğu | S1:477 |
| 4 | `showSeekBar` | Now Playing kartı ilerleme | S1:485 |
| 5 | `showPlaylistToggle` | playlist açma/kapama | S1:493 |
| 6 | `showPodcastWidget` | podcast paneli | S1:501 |
| 7 | `showRadioWidget` | radyo paneli | S1:509 |
| 8 | `showUtilityIcons` | footer ikonları (repeat, shuffle, EQ...) | S1:518 |
| 9 | `showFooterSeekSlider` | footer ilerleme slider'ı | S1:526 |
| 10 | `shouldRenderWelcomePopup` | karşılama popup'ı | S1:535 |

**DocBlock kaynaklı yüzey adları (S1):** yukarıdaki "etkilediği yüzey" adları
`DeviceManager` DocBlock'larından alınmıştır (kod-içi dokümantasyon); ayrı bir
vault iddiası değildir. `showUtilityIcons` DocBlock'u ikonların tam listesini
de verir: repeat, shuffle, EQ, fullscreen, playlist, WiFi, BT, settings, AI
terminal (S1:518) — yani bu karar **dokuz** ikonu birden yönetir.

## §19 Davranış Tipi → Risk Matrisi

| # | Tip | Metot sayısı | Risk | Gerekçe |
|---|---|---|---|---|
| 1 | koşullu | 9 | HIGH | 7 gövdesiz sorguya bağlı (§13) |
| 2 | sayaç-bağlı | 1 | MEDIUM | `widgetCount()` kaynağı bilinmiyor |
| 3 | sabit | 4 | LOW | davranış biliniyor; değişme riski |

**Dağılım özeti (S1):** 14 kararın 9'u koşullu, 1'i sayaç-bağlı, 4'ü sabittir.
Yani kararların **%64'ü (9/14)** gövdesi okunmamış cihaz sorgularına bağlıdır —
bu, device-service'in davranışının büyük ölçüde **okunmamış çekirdeğe** dayandığı
anlamına gelir (§14-B1). Bu oran, bir sonraki turun önceliğini belirler (§20).

## §20 Tur-3 Önerisi (Sürücü Tarafı)

| # | İş | Hedef kapı | Öncelik |
|---|---|---|---|
| 1 | 7 cihaz sorgusu gövdesi okunacak | §13 · §14-B1 | HIGH |
| 2 | `shouldRenderEmbeddedLayout` devamı okunacak | §11.3 | MEDIUM |
| 3 | detector ↔ manager bağlanacak | §14-B2 | HIGH |
| 4 | spoof doğrulama katmanı aranacak | §14-B3 | HIGH |
| 5 | `widgetCount()` okunacak | §14-B5 | MEDIUM |

## §21 Kapı Kontrol Listesi (Özet)

Bu dosyanın kendi doğrulama kapısı; tur-3'te yeniden çalıştırılır.

| # | Kapı | Hedef | Durum |
|---|---|---|---|
| 1 | ≥500 satır | bu dosya | dış ölçüm (§son) |
| 2 | CJK/FFFD 0 | bu dosya | 0 |
| 3 | `parent:` 0 | bu dosya | 0 |
| 4 | `group` alıntısız | `mimari` | mimari |
| 5 | son satır birebir | authority | birebir |
| 6 | kod kanıtı ≥1 birebir blok | §3 · §11 | var (9 blok) |
| 7 | motor/ürün adı uydurulmadı | §13 | evet |
| 8 | Q6 açık kapı korundu | §10 · §16 | evet |

## §22 Ölçüm Kaydı (Sürücü Dosyaları)

| # | Dosya | Satır | Okunan kısım |
|---|---|---|---|
| 1 | shared/src/Device/DeviceManager.php | 663 | 460–599 |
| 2 | shared/src/Device/DeviceDetector.php | 317 | 1–120 (anayasa) |
| 3 | shared/src/Device/DeviceCssMap.php | — | hiç (S5) |

**Ölçüm notu:** DeviceManager'ın 663 satırından yalnızca 460–599 arası (140
satır) okundu; kalan ~523 satır (kurucu, sorgu gövdeleri, yardımcılar) okunmadı
→ §13'teki 7 sorgu gövdesi bu okunmamış kısımdadır. Kesin satır ucu için `wc -l`
gerekir → `⚠️ VERIFICATION REQUIRED` (anayasa §31 benzeri not).

## §23 Kritik Repo Alıntıları (Referans)

Aşağıdaki blok, sürücünün en "sabit" kararını ve onun gerekçesini gösterir;
`device-service-ornekler.md` ile aynı disk kaynağıdır (tekrar değil, referans).

Dosya: `shared/src/Device/DeviceManager.php` / Satır: 560–568

```php
/**
 * Fallback ekranı gösterilsin mi?
 * Tüm ekran boyutları (Mobil, 1024px Gömülü/Tablet, 1920px Masaüstü/Standart, 4K TV)
 * ilgili optimize HTML/CSS bloklarıyla desteklendiği için false döner.
 */
public function shouldShowFallback(): bool
{
    return false;
}
```

**Gerekçe notu (S1:562–563):** `shouldShowFallback` daima `false` döner çünkü
"dört kademenin de optimize HTML/CSS blokları" vardır. Yani fallback ekranı
**bilinçli olarak devre dışı** bırakılmıştır — bu, dört-tier desteğinin bir
yan etkisidir (§12 sabit-davranış).

## §24 Kapanış Notu (Sürücü)

device-service'in sürücüsü **tek sınıfta** (`DeviceManager`) toplanmış, 14
boolean karar üreten, çizim yapmayan ince bir karar katmanıdır. Davranışının
%64'ü (9/14) gövdesi okunmamış yedi cihaz sorgusuna bağlıdır (§19). Bu dosyadaki
olgusal çekirdek: 9 birebir kod bloğu + 14 sembil + tier DocBlock + deprecated
notu. Geri kalan her şey açık kapı olarak sayılır ve uydurulmaz.

## §25 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | sürücü yazıldı — 14 karar metodu (birebir gövde) + tier DocBlock + deprecated notu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
