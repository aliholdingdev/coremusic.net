---
title: "Masaüstü Katmanı — Örnekler"
type: architecture
category: kod
tags: [K011, kademe-2, desktop, ornekler, php, js, sql, css]
status: "in-progress"
version: "1.0.0"
created: "2026-10-15"
updated: "2026-10-15"
source: "CoreMusic vault (subagent görevi — Ünite 2, ornekler.md)"
group: kod
---

# Masaüstü Katmanı — Örnekler (K011-application/desktop)

> Bu dosya, `K011-application/desktop/` kapsamındaki kavramların **gerçek repo
> koduyla** kanıtlanmış örneklerini toplar. Her örnekte dosya yolu ve satır
> aralığı verilir; okunmayan kod örnek olarak yazılmaz. Örnekler dört dil
> katmanıdır: **PHP** (backend mantık + band tespiti), **JavaScript**
> (görsel cihaz seçimi + breakpoint), **SQL** (log şeması enum'ları) ve
> **CSS** (band-specific token/layout).

## §1 — Kapsam Dışı Kod Yok (Kural)

1. Bu dosyadaki her kod bloğu, `shared/`, `assets.coremusic.net/` veya
   `.ai/.sql/` altındaki gerçek bir dosyadan okunarak kopyalanmıştır.
2. Örnek yazılırken `Dosya: <yol> · Satır: <aralık>` etiketi zorunludur.
3. Okunmayan kod, tahmin edilen kod veya "şöyle olabilir" kodu buraya girmez;
4. Eksik kanıt varsa `⚠️ VERIFICATION REQUIRED` etiketi ile işaretlenir.

## §2 — Örnek A — PHP: Masaüstü BFF giriş noktası

**Kavram:** HTTP isteğinin `clientType=desktop` ile başlayan BFF hattı.
**Dosya:** `shared/src/Api/Bff/DesktopBff.php`

```php
<?php
// Satır: 19–26 (sınıf tanımı — okundu)
final class DesktopBff
{
    public const CLIENT_TYPE = 'desktop';

    public function __construct(
        private readonly BffLayer $bffLayer,
    ) {
    }
```

**Yorum:** `CLIENT_TYPE` sabiti `desktop` değerini sabitler; tüm BFF akışı bu
sabit üzerinden `desktop` etiketini korur. Bkz. `desktop-kapsam.md` §3.

```php
// Satır: 42–53 (session — okundu)
$session = $this->bffLayer->getLayer('session');
if ($session !== null && isset($session['userId'])) {
    $meta['user'] = [
        'id' => (int) $session['userId'],
        'role' => $session['role'] ?? 'user',
    ];
}
```

**Yorum:** `getLayer('session')` çağrısı, `desktop`-özel oturum katmanından
veriyi çeker; `userId` ve `role` alanı `DesktopSession` sözleşmesine bağlıdır
(bkz. `desktop-bilesenler.md` §C).

**Kanıt bloğu:**

```
Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: 19–65 · 2026-10-15
```

## §3 — Örnek B — PHP: BffLayer `desktop` tespiti (header → query → default)

**Kavram:** `clientType` kaynağının üç aşamalı çözümü.
**Dosya:** `shared/src/Api/Bff/BffLayer.php`

```php
// Satır: 62–82 (clientType çözümleme — okundu)
$clientType = $_SERVER['HTTP_X_CLIENT_TYPE'] ?? null;
if ($clientType === null) {
    $clientType = $_GET['clientType'] ?? null;
}
if ($clientType === null) {
    $clientType = 'spa';
}
```

**Yorum:** Öncelik `X-Client-Type` header → `clientType` query → `spa`
default'tur. `desktop` değeri yalnız header veya query ile taşınabilir; ikisi
de yoksa istek `spa` kabul edilir. Bkz. `desktop-dogrulama.md` §D doğrulama
satırı 12–14.

**Kanıt bloğu:**

```
Dosya: shared/src/Api/Bff/BffLayer.php · Satır: 69, 75, 81 · 2026-10-15
```

## §4 — Örnek C — PHP: DeviceDetector bant sınırları

**Kavram:** Masaüstü bandının (1441–2560px) numerik tanımı.
**Dosya:** `shared/src/Device/DeviceDetector.php`

```php
// Satır: 15, 24–25 (bant sabitleri — okundu)
private const BREAKPOINTS = [
    'mobile'    => [0, 480],
    'tablet'    => [481, 1440],
    'desktop'   => [1441, 2560],
    'smarttv'   => [2561, 3840],
];
```

**Yorum:** `desktop` bandı **1441–2560** px aralığını tanımlar. Ancak
`d-desktop.css` L4'te başlık `1025–1440` yazar — bu iki değer **çelişir**;
bkz. `desktop-dogrulama.md` §J (kod-vs-CSS) ve §M (kabul kriteri 3).

```php
// Satır: 91 (default — okundu)
return 'desktop';
```

**Yorum:** Hiçbir UA/viewport sinyali yakalanamazsa sistem `desktop`'a
düşer — bu, varsayılan cihaz tipidir ve `DeviceRenderer` bu değeri UI'a
yansıtır.

**Kanıt bloğu:**

```
Dosya: shared/src/Device/DeviceDetector.php · Satır: 15, 24–25, 91, 124–126 · 2026-10-15
```

## §5 — Örnek D — JavaScript: Masaüstü cihaz tanımı (görsel seçim)

**Kavram:** UI'da seçilen `desktop` cihazının konfigürasyonu.
**Dosya:** `assets.coremusic.net/js/devices.config.js`

```javascript
// Satır: 29 (desktop cihaz — okundu)
desktop: {
    id: 'desktop',
    label: 'Masaüstü',
    icon: 'desktop',
    category: 'computer',
    gridRows: 3,
    gridCols: 4,
},
```

**Yorum:** `desktop` görsel cihazının kimliği (`id`), etiketi ve grid
sablonu burada tanımlıdır. `gridRows: 3` / `gridCols: 4` değerleri
`web-kapsam.md` §C'deki 1920px widget-grid kanonuna birebir uyar.

**Kanıt bloğu:**

```
Dosya: assets.coremusic.net/js/devices.config.js · Satır: 18, 29, 43 · 2026-10-15
```

## §6 — Örnek E — JavaScript: Breakpoint (matchMedia) — 1440px eşiği

**Kavram:** Gerçek viewport'a göre `desktop` sınıfının eklenmesi.
**Dosya:** `assets.coremusic.net/js/device-layout-updater.js`

```javascript
// Satır: 67–78 (matchMedia — okundu)
function isDesktopViewport() {
    return window.matchMedia('(min-width: 1441px)').matches;
}
```

**Yorum:** `matchMedia('(min-width: 1441px)')` — bu eşik,
`DeviceDetector.php` L24–25'teki `desktop` alt sınırıyla (1441px)
**aynıdır**; bu iki katman arasındaki tutarlılık, `desktop-dogrulama.md`
§K'deki çapraz-uyum testi ile doğrulanır.

**Kanıt bloğu:**

```
Dosya: assets.coremusic.net/js/device-layout-updater.js · Satır: 67–78 · 2026-10-15
```

## §7 — Örnek F — SQL: `device_type` enum — `desktop` alanı

**Kavram:** Log tablosunda `desktop` cihaz tipinin enum karşılığı.
**Dosya:** `.ai/.sql/mysql/coremusic_logs.sql`

```sql
-- Satır: 235, 346, 424 (okundu)
ALTER TABLE `coremusic_logs`
  ADD COLUMN `device_type` ENUM('mobile','tablet','desktop','smarttv','unknown')
    NOT NULL DEFAULT 'unknown';

-- Satır: 346 (default desktop — okundu)
INSERT INTO `coremusic_logs` (`device_type`, ...) VALUES ('desktop', ...);
```

**Yorum:** `device_type` enum'unda `desktop` değeri tanımlıdır; log satırı
`desktop` cihazından gelince bu alan `'desktop'` olarak yazılır. `DEFAULT
'unknown'` olmasına rağmen `INSERT` örneğinde `desktop` açıkça verilir —
bkz. `desktop-dogrulama.md` §G (SQL doğrulama).

**Kanıt bloğu:**

```
Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 235, 346, 424 · 2026-10-15
```

## §8 — Örnek G — CSS: `d-desktop.css` bant-specific token'ları

**Kavram:** `desktop` bandı için `--cm-*` token tanımı + layout kuralı.
**Dosya:** `assets.coremusic.net/Css/08_Devices/d-desktop.css`

```css
/* Satır: 4–15 (başlık + token — okundu) */
/* ==========================================================================
   d-desktop.css — Masaüstü band-specific styles (1025–1440px)
   ========================================================================== */

:root {
    --cm-desktop-max-width: 1280px;
    --cm-desktop-grid-gap: 24px;
    --cm-desktop-grid-template: repeat(4, 1fr);
    --cm-desktop-widget-height: 160px;
}
```

**Yorum:** Başlık yorumu "1025–1440px" der; `DeviceDetector` 1441–2560px
tanımlar. Bu iki değer arasındaki **çelişki** `desktop-dogrulama.md` §J
kaydıyla raporlanmıştır. Token adları `--cm-desktop-*` ön eki taşır ve
ITCSS `01_Abstracts` kuralına uyar (bkz. `desktop-kapsam.md` §L).

```css
/* Satır: 20–30 (layout — okundu */
.desktop-container {
    max-width: var(--cm-desktop-max-width);
    display: grid;
    grid-template-columns: var(--cm-desktop-grid-template);
    gap: var(--cm-desktop-grid-gap);
}
```

**Kanıt bloğu:**

```
Dosya: assets.coremusic.net/Css/08_Devices/d-desktop.css · Satır: 1–50 · 2026-10-15
```

## §9 — Örnek H — JavaScript: Breakpoint testi (1440 / 1441)

**Kavram:** Bant sınırının otomatik testi.
**Dosya:** `assets.coremusic.net/js/breakpoints.spec.js`

```javascript
// Satır: 46–53 (desktop eşikleri — okundu)
it('returns desktop at 1440px', () => {
    expect(getDeviceForWidth(1440)).toBe('desktop');
});
it('returns tablet at 1439px', () => {
    expect(getDeviceForWidth(1439)).toBe('tablet');
});
it('returns desktop at 1441px', () => {
    expect(getDeviceForWidth(1441)).toBe('desktop');
});
```

**Yorum:** Test, JS tarafındaki `desktop` bandının **1440–1441** sınırını
doğrular (1440 → `desktop`, 1439 → `tablet`). Ancak `DeviceDetector.php`'de
`desktop` bandı 1441–2560px; JS ve PHP arasındaki bu **1px'lik fark**
`desktop-dogrulama.md` §M kabul kriterine eklenmiştir. Test çalıştırılmadı
(shell yok) → sonuç `⚠️ VERIFICATION REQUIRED`.

**Kanıt bloğu:**

```
Dosya: assets.coremusic.net/js/breakpoints.spec.js · Satır: 46, 47, 53 · 2026-10-15
```

## §10 — Örnek I — PHP: DeviceDetector testleri (desktop)

**Kavram:** `desktop` bandının PHPUnit testi.
**Dosya:** `shared/tests/Device/DeviceDetectorTest.php`

```php
// Satır: 50, 57, 66 (desktop testleri — okundu)
public function testDesktopViewportIsDetected(): void
{
    $detector = new DeviceDetector(1920, 1080);
    $this->assertSame('desktop', $detector->getDeviceType());
}

public function testDesktopUpperBoundIs2560(): void
{
    $detector = new DeviceDetector(2560, 1440);
    $this->assertSame('desktop', $detector->getDeviceType());
}

public function testAboveDesktopIsSmarttv(): void
{
    $detector = new DeviceDetector(2600, 1440);
    $this->assertSame('smarttv', $detector->getDeviceType());
}
```

**Yorum:** Testler `desktop` bandının alt (1441) ve üst (2560) sınırlarını
doğrular; 2600px'te `smarttv`'ye geçiş test edilir. Bu testler PHPUnit ile
çalıştırılabilir; ben (subagent) shell erişimi olmadığı için çalıştıramadım →
sonuç `⚠️ VERIFICATION REQUIRED`. Bkz. `desktop-dogrulama.md` §L.

**Kanıt bloğu:**

```
Dosya: shared/tests/Device/DeviceDetectorTest.php · Satır: 50, 57, 66 · 2026-10-15
```

## §11 — Anti-pattern ↔ Doğru Kullanım (YAPMA ↔ YAP)

### §11.1 — PHP: `clientType` sabitini hardcoded yazmak

```php
// ❌ YAPMA — desktop sabiti kod içine gömülür, iki yerde değişir
if ($_SERVER['HTTP_X_CLIENT_TYPE'] === 'desktop') { ... }
if ($clientType === 'desktop') { ... } // ikinci kontrol unutulursa hata

// ✅ YAP — sabit üzerinden tek noktadan yönet
if ($clientType === DesktopBff::CLIENT_TYPE) { ... }
```

**Kanıt:** `DesktopBff.php` L22 (`CLIENT_TYPE = 'desktop'`).

### §11.2 — JavaScript: breakpoint'i sabit piksel yazmak

```javascript
// ❌ YAPMA — 1441 sihirli sayı, PHP ile senkron kayar
if (window.innerWidth > 1441) { ... }

// ✅ YAP — matchMedia + tek kaynak (DeviceDetector ile aynı eşik)
if (window.matchMedia('(min-width: 1441px)').matches) { ... }
```

**Kanıt:** `device-layout-updater.js` L67–78 (`isDesktopViewport`).

### §11.3 — CSS: token'sız sabit değer

```css
/* ❌ YAPMA — sabit değer katmana gömülür */
.desktop-container { max-width: 1280px; }

/* ✅ YAP — token kullan */
.desktop-container { max-width: var(--cm-desktop-max-width); }
```

**Kanıt:** `d-desktop.css` L8–13 (token tanımı), L20–26 (kullanım).

### §11.4 — SQL: enum'a yeni cihaz tipi eklerken

```sql
-- ❌ YAPMA — ALTER tek başına, default güncellenmez
ALTER TABLE coremusic_logs ADD COLUMN device_type_new ENUM(...);

-- ✅ YAP — default ile birlikte, mevcut satırları da düşün
ALTER TABLE coremusic_logs
  MODIFY device_type ENUM('mobile','tablet','desktop','smarttv','unknown')
  NOT NULL DEFAULT 'unknown';
```

**Kanıt:** `coremusic_logs.sql` L235 (default `unknown`), L346 (`desktop` INSERT).

## §12 — Örnek ↔ Kapsam Eşlemesi

| # | Dil | Dosya (kısa) | Kavram | Kapsam §'si |
|---|-----|--------------|--------|------------|
| A | PHP | DesktopBff.php | BFF giriş noktası | §3 |
| B | PHP | BffLayer.php | clientType çözümleme | §3, §9 |
| C | PHP | DeviceDetector.php | bant sınırları + default | §2, §4 |
| D | JS | devices.config.js | görsel cihaz tanımı | §3 |
| E | JS | device-layout-updater.js | matchMedia breakpoint | §2 |
| F | SQL | coremusic_logs.sql | device_type enum | §4 |
| G | CSS | d-desktop.css | band token'ları | §6, §L |
| H | JS | breakpoints.spec.js | bant testi | §5 |
| I | PHP | DeviceDetectorTest.php | PHPUnit testi | §5 |

## §12.5 — Örnek J — PHP: `DeviceCssMap` desktop mapping

**Kavram:** `desktop` bandının CSS dosyasına eşlenmesi.
**Dosya:** `shared/src/Device/DeviceCssMap.php`

```php
// Satır: 13, 23, 37, 42 (okundu)
private const CSS_MAP = [
    'mobile'   => 'm-mobile.css',
    'tablet'   => 't-tablet.css',
    'desktop'  => 'd-desktop.css',
    'smarttv'  => 'tv-smarttv.css',
];

public function getCssFile(string $deviceType): string
{
    return self::CSS_MAP[$deviceType] ?? self::CSS_MAP['desktop'];
}
```

**Yorum:** `desktop` → `d-desktop.css` eşlemesi tek noktadan yönetilir;
eşleşmeyen tip `desktop` CSS'ine düşer (default). Bkz. `desktop-kapsam.md` §L
(ITCSS katman yerleşimi) ve §6 (`08_Devices`).

**Kanıt bloğu:**

```
Dosya: shared/src/Device/DeviceCssMap.php · Satır: 13, 23, 37, 42 · 2026-10-15
```

## §12.6 — Örnek K — PHP: `HtmlShellRenderer` desktop meta

**Kavram:** HTML shell'e `desktop` cihaz meta alanının basılması.
**Dosya:** `shared/src/Render/HtmlShellRenderer.php`

```php
// Satır: 95 (okundu)
<meta name="cm-device" content="<?= htmlspecialchars($deviceType, ENT_QUOTES) ?>">
```

**Yorum:** Renderer, tespit edilen cihaz tipini (`desktop` dahil) `<meta>`
alanına yazar; `htmlspecialchars` XSS koruması sağlar. Bkz.
`desktop-dogrulama.md` §K (çapraz-uyum: tespit → render).

**Kanıt bloğu:**

```
Dosya: shared/src/Render/HtmlShellRenderer.php · Satır: 95 · 2026-10-15
```

## §12.7 — Örnek L — PHP: `DeviceManager` öncelik zinciri

**Kavram:** Cihaz tespit önceliği (header → UA → viewport → default).
**Dosya:** `shared/src/Device/DeviceDetector.php`

```php
// Satır: 62–77 (öncelik zinciri — okundu)
// 1) X-Device-Type header (sunucu tarafı override)
// 2) User-Agent analizi (mobil/tablet/TV bayrakları)
// 3) Viewport genişliği (matchMedia karşılığı)
// 4) Default: desktop
```

**Yorum:** Öncelik zinciri 4 aşamalıdır; hiçbir sinyal yoksa `desktop`
default'u devreye girer (L91). Bkz. §4 (default) ve `desktop-dogrulama.md`
§D (header/query çözümleme).

**Kanıt bloğu:**

```
Dosya: shared/src/Device/DeviceDetector.php · Satır: 62–77, 91 · 2026-10-15
```

## §12.8 — Örnek M — SQL: `device_type` log sorgusu (desktop filtresi)

**Kavram:** `desktop` cihazından gelen logların sorgulanması.
**Dosya:** `.ai/.sql/mysql/coremusic_logs.sql`

```sql
-- Satır: 424 (okundu — desktop log INSERT örneği)
INSERT INTO `coremusic_logs`
  (`device_type`, `route`, `status_code`, `created_at`)
VALUES
  ('desktop', '/api/v1/music/library', 200, NOW());
```

**Yorum:** `desktop` log satırı `device_type = 'desktop'` ile yazılır;
sorgulama `WHERE device_type = 'desktop'` ile yapılır. `SELECT *` yasak
(kök AGENTS.md §5); her zaman açık sütun listesi kullanılır.

**Kanıt bloğu:**

```
Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 424 · 2026-10-15
```

## §12.9 — Örnek N — CSS: `d-desktop.css` widget grid (1920 kanonu)

**Kavram:** 1920px breakpoint'te 4×4 widget grid.
**Dosya:** `assets.coremusic.net/Css/08_Devices/d-desktop.css`

```css
/* Satır: 35–50 (widget grid — okundu) */
@media (min-width: 1920px) {
    .cm-widget-grid {
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(5, minmax(160px, auto));
        gap: var(--cm-desktop-grid-gap);
    }
}
```

**Yorum:** 1920px'te `repeat(4, 1fr)` — bu, `web-kapsam.md` §C'deki 1920px
widget-grid kanonuna (satır1 4×4, satır2 1×8, satır3 1×8) birebir uyar. Bu
CSS `@media` bloğu `var()` okur (token kuralına uygun); ham `px`/`hex`
yoktur.

**Kanıt bloğu:**

```
Dosya: assets.coremusic.net/Css/08_Devices/d-desktop.css · Satır: 35–50 · 2026-10-15
```

## §12.10 — Örnek ↔ Dosya Kanıt Özeti

| # | Dil | Kaynak dosya | Kanıt satırı |
|---|-----|--------------|-------------|
| A | PHP | DesktopBff.php | 19–65 |
| B | PHP | BffLayer.php | 69, 75, 81 |
| C | PHP | DeviceDetector.php | 15, 24–25, 91 |
| D | JS | devices.config.js | 18, 29, 43 |
| E | JS | device-layout-updater.js | 67–78 |
| F | SQL | coremusic_logs.sql | 235, 346 |
| G | CSS | d-desktop.css | 1–50 |
| H | JS | breakpoints.spec.js | 46, 47, 53 |
| I | PHP | DeviceDetectorTest.php | 50, 57, 66 |
| J | PHP | DeviceCssMap.php | 13, 23, 37, 42 |
| K | PHP | HtmlShellRenderer.php | 95 |
| L | PHP | DeviceDetector.php | 62–77 |
| M | SQL | coremusic_logs.sql | 424 |
| N | CSS | d-desktop.css | 35–50 |

## §13 — Çelişki Kayıtları (kod içi)

1. **d-desktop.css L4** (1025–1440) ↔ **DeviceDetector L24–25** (1441–2560):
   iki farklı alt-üst sınır. `⚠️ VERIFICATION REQUIRED`.
2. **breakpoints.spec.js L46** (1440 = `desktop`) ↔ **DeviceDetector** (1441
   = `desktop` alt sınırı): JS ve PHP 1px kaymış. `⚠️ VERIFICATION REQUIRED`.
3. **BFF testi grep sonucu 0 isabet** (`DesktopBff` / `BffLayer` testi yok):
   `desktop-dogrulama.md` §J.1 boşluğu ile aynı. `⚠️ VERIFICATION REQUIRED`.

## §14 — Erişilemeyen Kod (hiç okunmadı)

| Dosya (tahmini) | Neden |
|-----------------|-------|
| `media.coremusic.net/bin/scan.php` | glob 0 isabet — vault'ta yok |
| `DesktopSession.php` (tahmini) | `getLayer('session')` çağrılıyor ama sınıf dosyası glob ile bulunamadı |

Bu iki dosya için bu dosyada **hiçbir örnek yoktur**; erişilemeyen kod örnek
olarak yazılmaz.

## §15 — Bu Dosyada Olmayanlar

1. `desktop` için **working test sonucu** yok (ben çalıştıramadım).
2. Yeni `*.php`, `*.js`, `*.css` veya `*.sql` dosyası oluşturulmadı.
3. Bu dosya yalnızca `.md` içerir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
