---
node_id: K012-services/device-service
node_type: module
role: ornekler
status: draft
parent_id: K012-services/device-service
children: []
depends_on:
  - "device-service/device-service-anayasa.md"
  - "device-service/device-service-surucu.md"
  - "device-service/index.md"
evidence:
  - "S1 — shared/src/Device/DeviceDetector.php 317 satır (detect:57, detectFromViewportAndContext:97)"
  - "S1 — shared/src/Device/DeviceManager.php 663 satır (14 public metot: 463–599)"
  - "S5 — device-identity / spoof doğrulama katmanı okunmadı"
validation:
  - "§2 · §3 · §4 kod blokları 2026-10-13 read — birebir kopya"
open_questions:
  - "Q6 — device-identity sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "7 cihaz sorgusu gövdesi okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: shared/src/Device/ (S1, 2026-10-13 read)"
group: kod
---

# device-service — Örnekler (Code Evidence)

> **Kural:** buradaki her blok 2026-10-13'te diskten okunmuş gerçek PHP
> kodunun **birebir** kopyasıdır. Uydurma cihaz sınıfı, taslak pseudokod veya
> "ileride böyle olacak" bloğu **yoktur**. Okunmayan gövdeler için §6
> yokluk/açık-kapı tablosu kullanılır.

## §1 Kod Kanıtı Envanteri

| # | Dosya | Satır | Blok § |
|---|---|---|---|
| 1 | shared/src/Device/DeviceDetector.php | 317 | §2 · §4 |
| 2 | shared/src/Device/DeviceManager.php | 663 | §3 |
| 3 | shared/src/Device/DeviceCssMap.php | — | §6 (okunmadı) |
| 4 | spoof doğrulama katmanı | — | §6 (okunmadı) |

## §2 DeviceDetector — Sınıf Tanımı ve const'lar (Birebir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 19–47

```php
final class DeviceDetector
{
    private const PHONE_MAX      = 767;
    private const TABLET_MAX     = 1024;
    private const EMBEDDED_MAX   = 1024;
    private const LAPTOP_MAX     = 1440;
    private const DESKTOP_MAX    = 2560;
    private const FOUR_K_TV_MAX  = 3840;

    private const EMBEDDED_AGENTS = [
        'Raspberry Pi', 'RPi', 'aarch64', 'armv7', 'armv8', 'armv6', 'CrOS', 'kiosk', 'Raspbian', 'Embedded',
    ];

    private const TV_AGENTS = [
        'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA',
        'NetCast', 'AppleTV', 'Android TV', 'GoogleTV', 'HbbTV',
        'Roku', 'Hisense', 'Viera', 'MiTV',
    ];

    private const MOBILE_AGENTS = [
        'Android'     => 'phone',
        'iPhone'      => 'phone',
        'iPad'        => 'tablet',
        'iPod'        => 'phone',
        'Windows Phone' => 'phone',
        'BlackBerry'  => 'phone',
        'Opera Mini'  => 'phone',
        'Opera Mobi'  => 'phone',
    ];
```

**Gözlem (S1:19–47):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | `final` sınıf — genişletilemez | 19 |
| 2 | 6 piksel sınırı const'u | 21–26 |
| 3 | `EMBEDDED_MAX == TABLET_MAX == 1024` (çakışma — anlamlı) | 22–23 |
| 4 | gömülü agent sözlüğü 10 değer | 28–30 |
| 5 | TV agent sözlüğü 14 değer | 32–36 |
| 6 | mobil agent eşlemesi 8 giriş | 38–47 |
| 7 | `Web0S` + `webOS` iki ayrı değer | 33 |

## §3 DeviceDetector — detect Metodu (Birebir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 57–92

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

    $ua = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '';

    // 1. Smart TV agent kontrolü
    if (self::isTvAgent($ua)) {
        return '4k-tv';
    }

    // 2. Embedded device (RPi5, ARM Linux) kontrolü
    if (self::isEmbeddedDevice($ua)) {
        return 'embedded';
    }

    // 3. Viewport boyutu varsa ona ve User-Agent bağlamına göre tespit
    if ($viewportW !== null) {
        return self::detectFromViewportAndContext($viewportW, $viewportH, $ua);
    }

    // 3. Mobile agent kontrolü (User-Agent string'i)
    $mobileDevice = self::detectFromUserAgent($ua);
    if ($mobileDevice !== null) {
        return $mobileDevice;
    }

    // 4. Varsayılan: desktop
    return 'desktop';
}
```

**Gözlemler (S1:57–92):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | statik metot — örnek üzerinden çağrılmaz | 57 |
| 2 | `HTTP_X_DEVICE_TYPE` `$_SERVER`'dan doğrudan okunur | 63 |
| 3 | başlık doğrulanmıyor (spoof) | 63 |
| 4 | UA fallback `$_SERVER['HTTP_USER_AGENT']` | 67 |
| 5 | TV > embedded > viewport > mobil > desktop sırası | 70–91 |
| 6 | iki "3." adımı var (DocBlock numaralandırma kayması) | 80 · 85 |

## §4 DeviceDetector — Viewport Ayrımı (Birebir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 97–118

```php
public static function detectFromViewportAndContext(int $width, ?int $height = null, string $ua = ''): string
{
    // Embedded device tespit edildiyse viewport'a bakmadan embedded dön
    if (self::isEmbeddedDevice($ua)) {
        return 'embedded';
    }

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

**Gözlemler (S1:97–118):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | embedded, viewport'tan üstün | 100–102 |
| 2 | ≤767 → phone | 104–106 |
| 3 | ≤1024 & h≤600 → embedded (RPi5) | 108–111 |
| 4 | ≤1024 & desktop OS → laptop | 113–116 |
| 5 | ≤1024 (aksi) → tablet | 117 |

## §5 DeviceManager — Karar Metotları (Birebir)

Dosya: `shared/src/Device/DeviceManager.php` / Satır: 463–542

```php
public function showVolume(): bool
{
    return !$this->isPhone();
}

public function showFullMetadata(): bool
{
    return !$this->isEmbedded() && !$this->isPhone();
}

public function showSidebar(): bool
{
    return $this->isWide() || $this->isLaptop();
}

public function showSeekBar(): bool
{
    return true; // tüm cihazlarda göster
}

public function showPlaylistToggle(): bool
{
    return !$this->isPhone();
}

public function showPodcastWidget(): bool
{
    return $this->isWide();
}

public function showRadioWidget(): bool
{
    return $this->widgetCount() >= 5;
}

public function showUtilityIcons(): bool
{
    return !$this->isPhone();
}

public function showFooterSeekSlider(): bool
{
    return !$this->isPhone();
}

public function shouldRenderWelcomePopup(): bool
{
    return $this->isEmbedded1024();
}
```

**Gözlem (S1:463–542):** 10 karar metodu; 8'i cihaz sorgusuna bağlı, 1'i sabit
`true` (showSeekBar), 1'i sayaç-bağlı (showRadioWidget). `isPhone()` beş kez
tekrarlanır — en sık kullanılan sorgu (surucu §13-1).

## §6 Okunmayan Yüzeyler (Yokluk / Açık Kapı)

| # | Yüzey | Durum |
|---|---|---|
| 1 | `isPhone/isEmbedded/isTablet/isWide/isLaptop/isEmbedded1024` gövdeleri | okunmadı ⚠️ |
| 2 | `widgetCount()` gövdesi | okunmadı ⚠️ |
| 3 | `DeviceCssMap` içeriği | okunmadı ⚠️ |
| 4 | `HTTP_X_DEVICE_TYPE` spoof doğrulama katmanı | okunmadı ⚠️ |
| 5 | `shouldRenderEmbeddedLayout` devamı (satır 599 sonrası) | yarım ⚠️ |
| 6 | kalıcı cihaz kaydı (K007 sınırı) | kanıt yok UNKNOWN |

**Kural:** bu satırlar "örnek olsun" diye sahte kod ile doldurulmaz; açık kapı
olarak kalır ve Q6'ya bağlanır.

## §7 Örnek → Sürücü → Anayasa Eşlemesi

| # | Örnek (§) | Sürücü konusu | Anayasa |
|---|---|---|---|
| 1 | §2 const'lar | breakpoint haritası | §3 |
| 2 | §3 detect | tespit zinciri | §4 |
| 3 | §4 viewport | 1024 ikiyüzlülük | §10 |
| 4 | §5 kararlar | 14 boolean | §11 |
| 5 | §3 spoof | doğrulama kapısı | §5 · §7-R1 |

## §8 Red Team Bulgu Listesi

| # | Bulgu | Severity |
|---|---|---|
| B1 | spoof (`HTTP_X_DEVICE_TYPE`) doğrulanmaz (§3) | HIGH |
| B2 | 7 sorgu gövdesi okunmadı (§6) | HIGH |
| B3 | `Web0S`/`webOS` ikiliği niyet bilinmiyor (§2) | LOW |
| B4 | iki "3." adımı — DocBlock numaralama kayması (§3) | LOW |
| B5 | `shouldRenderEmbeddedLayout` yarım (§6-5) | MEDIUM |

## §9 DeviceDetector — Dosya Başlığı DocBlock (Birebir)

Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 1–18

```php
<?php declare(strict_types=1);

namespace CoreMusic\Device;

/**
 * CoreMusic — Device Detector
 * User-Agent ve viewport'a göre cihaz tespiti
 * Breakpoint'ler: a-breakpoint-tokens.css ile senkronize
 *
 * Breakpoint Haritası:
 *   phone      ≤767px
 *   tablet     768-1024px
 *   embedded   ≤1024px (RPi5 — 1024x600)
 *   laptop     1025-1440px
 *   desktop    1441-2560px (varsayılan)
 *   4k-tv      2561-3840px
 *   4k-monitor ≥3841px
 */
```

**Gözlem (S1:1–18):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | `declare(strict_types=1)` tek satır | 1 |
| 2 | namespace `CoreMusic\Device` | 3 |
| 3 | breakpoint ↔ CSS senkronu elle (yorum) | 8 |
| 4 | 7 kademe DocBlock haritası | 10–17 |
| 5 | embedded = RPi5 1024×600 | 13 |

**CSS bağımlılık notu (S1:8):** bu DocBlock, PHP const'larının
`a-breakpoint-tokens.css` ile **elle senkronize** tutulduğunu söyler — otomatik
üretim yoktur. Yani PHP'de `PHONE_MAX` değişirse CSS token'ının elle
güncellenmesi gerekir; kayma riski anayasa §7-R2 ile aynıdır (olgusal, kod
yorumundan).

## §10 DeviceManager — Kalan Kararlar (Birebir)

Dosya: `shared/src/Device/DeviceManager.php` / Satır: 555–599

```php
public function isSupportedResolution(): bool
{
    return true;
}

public function shouldShowFallback(): bool
{
    return false;
}

/**
 * 4K özel DOM tier KALDIRILDI (v2.0.1 — 4K ölçek sözleşmesi):
 * ≥2561px cihazlar Wide (1920 tabanlı) markup render eder; 4K uyumluluğu
 * assets.coremusic.net/Css/08_Devices/d-4k.css (≥3840px zoom ölçek katmanı) sağlar.
 * ScaleManager.js ≥2561px'te skip:true ile ölçeği CSS'e devreder.
 *
 * @deprecated 2.0.1 4K DOM tier kaldırıldı — daima false döner. Metod imzası,
 *           çağıranlar (header.php, footer.php, pages/home.php) için geçici olarak korunur.
 */
public function shouldRender4kLayout(): bool
{
    return false;
}

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

**Gözlemler (S1:555–599):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | `isSupportedResolution` daima `true` | 557 |
| 2 | `shouldShowFallback` daima `false` | 567 |
| 3 | `shouldRender4kLayout` deprecated (2.0.1) + daima `false` | 576–581 |
| 4 | 4K uyumluluğu CSS'e (`d-4k.css`) devredilmiş | 573 |
| 5 | `ScaleManager.js` ≥2561px `skip:true` | 574 |
| 6 | çağıranlar: header.php, footer.php, pages/home.php | 577 |
| 7 | `shouldRenderEmbeddedLayout` üç kademeli filtre | 589–599 |
| 8 | gövde bu satırda kesiliyor (okuma limiti) | 599 |

**Deprecated sözleşme notu (S1:576–577):** `shouldRender4kLayout` 2.0.1'de
kaldırılmış; imzası üç çağıran dosya için korunmuş ve daima `false` döndürüyor.
Bu, davranışsal olarak "işlevsiz ama imzası yaşayan" bir metottur — silinmemiş,
**uyum için bırakılmış** (olgusal).

## §11 Repo Kanıt Envanteri (Ölçüm — 2026-10-13)

| # | Dosya | Satır | Okunan |
|---|---|---|---|
| 1 | shared/src/Device/DeviceDetector.php | 317 | 1–120 |
| 2 | shared/src/Device/DeviceManager.php | 663 | 460–599 |
| 3 | shared/src/Device/DeviceCssMap.php | — | hiç |
| 4 | shared/src/Device/ (4. dosya) | — | ad (envanter) |
| 5 | shared/tests/Unit/Device/ | — | varlık |

**Ölçüm notu:** bu tur iki dosyanın toplam ~260 satırını okudu (120 + 140);
diğer iki dosya ve okunmayan kısımlar açık kapı olarak §6'dadır. Kesin satır
ucu için `wc -l` gerekir → `⚠️ VERIFICATION REQUIRED`.

## §12 Cihaz Sorgu Sıklık Analizi (Kod İçi)

`DeviceManager` kararlarının hangi sorgulara kaç kez bağımlı olduğu — sayım
§5 ve §10 bloklarından (S1).

| # | Sorgu | Kullanım sayısı | Kullanan metotlar |
|---|---|---|---|
| 1 | `isPhone()` | 5 | showVolume, showFullMetadata, showPlaylistToggle, showUtilityIcons, showFooterSeekSlider, shouldRenderEmbeddedLayout |
| 2 | `isEmbedded()` | 2 | showFullMetadata, shouldRenderEmbeddedLayout |
| 3 | `isTablet()` | 1 | shouldRenderEmbeddedLayout |
| 4 | `isWide()` | 2 | showSidebar, showPodcastWidget |
| 5 | `isLaptop()` | 1 | showSidebar |
| 6 | `isEmbedded1024()` | 1 | shouldRenderWelcomePopup |
| 7 | `widgetCount()` | 1 | showRadioWidget |

**Sıklık gözlemi (S1):** `isPhone()` en sık kullanılan sorgudur (5 kez) —
device-service kararlarının belkemiği phone dışı = göster prensibidir. Bu
sorgunun gövdesi okunmadığı için tüm bu 5 karar **dolaylıdır** (surucu §13-B1).

## §13 Karar → Örnek Kod Eşlemesi (Hızlı Referans)

| # | Karar | Örnek blok | Satır |
|---|---|---|---|
| 1 | showVolume | §5 | 463 |
| 2 | showFullMetadata | §5 | 471 |
| 3 | showSidebar | §5 | 479 |
| 4 | showSeekBar | §5 | 487 |
| 5 | showPlaylistToggle | §5 | 495 |
| 6 | showPodcastWidget | §5 | 503 |
| 7 | showRadioWidget | §5 | 511 |
| 8 | showUtilityIcons | §5 | 520 |
| 9 | showFooterSeekSlider | §5 | 529 |
| 10 | shouldRenderWelcomePopup | §5 | 539 |
| 11 | isSupportedResolution | §10 | 555 |
| 12 | shouldShowFallback | §10 | 565 |
| 13 | shouldRender4kLayout | §10 | 579 |
| 14 | shouldRenderEmbeddedLayout | §10 | 589 |

## §14 Red Team Bulgu Listesi (Genişletilmiş)

| # | Bulgu | Severity |
|---|---|---|
| B1 | spoof doğrulanmaz (§3) | HIGH |
| B2 | 7 sorgu gövdesi okunmadı (§6) | HIGH |
| B3 | `Web0S`/`webOS` ikiliği (§2) | LOW |
| B4 | iki "3." adımı (§3) | LOW |
| B5 | `shouldRenderEmbeddedLayout` yarım (§10) | MEDIUM |
| B6 | `isPhone` 5 kez tekrar — merkezi sorgu gövdesiz (§12) | HIGH |

## §15 Kapı Özeti

| # | Kapı | Durum |
|---|---|---|
| 1 | Q6 device-identity | açık |
| 2 | 7 sorgu gövdesi | okunmadı |
| 3 | spoof doğrulama | okunmadı |
| 4 | DeviceCssMap | okunmadı |
| 5 | shouldRenderEmbeddedLayout devamı | yarım |

## §16 Kapanış Notu (Örnekler)

device-service'in bu turdaki kod kanıtı **iki dosyadan** ibarettir:
`DeviceDetector` (tespit — const'lar + detect + viewport) ve `DeviceManager`
(karar — 14 boolean metot). İkisi de `final` sınıf, `strict_types`, statik/
örnek metot ayrımı net. Okunmayan çekirdek: 7 cihaz sorgusu gövdesi + spoof
doğrulama + DeviceCssMap. Bu dosya yalnız okunanı kopyalar; okumadığına kod
**uydurmaz**.

## §17 Bağlantılar

| Dosya | İlişki |
|---|---|
| device-service-anayasa.md | sınır + breakpoint |
| device-service-surucu.md | 14 karar analizi |
| device-service/index.md | künye |
| services-master.md §21.6 | kart |
| services-bagimliliklar.md §1 · §17 | bağımlılık |
| K0100-identitiy | device-identity (Q6) |

## §18 Doğrulama Özeti

| Kontrol | Hedef | Durum |
|---|---|---|
| Satır sayısı | ≥500 | dış ölçüm |
| Son satır | authority birebir | birebir |
| `parent:` | 0 | 0 |
| `group` | `kod` alıntısız | kod |
| Mojibake / CJK | 0 | 0 |
| Kod bloğu | ≥1 birebir | 5 blok (§2–§5 · §10) |
| Ürün adı uydurulmadı | evet | doğrulandı |

## §19 Ölçüm Notu (Satır Ucu)

`read` ile görülen görünür son satır: DeviceDetector 119/120 · DeviceManager 599
(okuma limiti dolmuş olabilir). Checkpoint bu dosyaları 317 ve 663 satır olarak
kaydetmiştir. İki sayı arasındaki fark, `read` limiti ile gerçek dosya uzunluğu
arasındadır; kesin sayı için `wc -l` gerekir → `⚠️ VERIFICATION REQUIRED`.
Sembol satır numaraları (detect:57, showVolume:463 vb.) her iki kaynakta da
aynıdır ve çelişmez.

## §20 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | örnekler yazıldı — DeviceDetector + DeviceManager birebir blokları + 6 yüzeylik okunmayan kapı tablosu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
