---
type: architecture
category: l2
title: "HTML Shell Renderer — SPA HTML Shell Üretimi"
date: 2026-08-16
updated: 2026-09-05
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# HTML Shell Renderer — SPA HTML Shell Üretimi

**Zorunlu Bağlantılar:** [[spa-router]] · [[ADR-083-spa-router]] · [[ADR-012-csp-nonce-strict-dynamic]]

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/HtmlShellRenderer.php`

---

## 1. Amaç

SPA'nın ilk yüklemede ürettiği tam HTML shell'i tanımlar. CSP nonce, device CSS, view mode CSS, header/footer include ve inline script yönetimini yönetir.

**SRP:** Tek sorumluluk — HTML shell üretimi. Auth mantığı `AuthGuard`'a, routing `PageRouter`'a devredildi.

---

## 2. HtmlShellRenderer Yapısı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Device\DeviceCssMap;

/**
 * HtmlShellRenderer — SPA HTML shell üretimi.
 *
 * ADR-043/021 uyumlu: CSRF token DOM patch sonrası JS tarafından güncellenir.
 * L0→L3 layer fix: session data renderer'a inject edilir, $_SESSION'a doğrudan erişim yok.
 */
final class HtmlShellRenderer
{
    private const COOKIE_TTL_SECONDS = 365 * 24 * 60 * 60; // 1 year

    private readonly string $headerPath;
    private readonly string $footerPath;

    public function __construct(
        private readonly ConfigManager $config,
        private readonly DomainConfig $domainConfig,
        ?string $headerPath = null,
        ?string $footerPath = null,
    ) {
        $this->headerPath = $headerPath ?? (defined('HEADER_PATH') ? (string)HEADER_PATH : '');
        $this->footerPath = $footerPath ?? (defined('FOOTER_PATH') ? (string)FOOTER_PATH : '');
    }

    public function render(
        string $container,
        string $route,
        array  $meta,
        string $csrfToken,
        array  $protectedRoutes = [],
        array  $sessionData = []
    ): string {
        // ... buildHeadTag + buildBodyContent + buildInlineScript + buildDeferScripts
    }
}
```

---

## 3. HTML Shell Yapısı

```html
<!doctype html>
<html lang="tr" data-gender="{gender}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <title>{page-title} — {app-name}</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="preconnect" href="{assets-url}">
    
    <!-- CSS: main.css + device.css + viewmode.css + auth.css -->
    <link rel="stylesheet" href="{assets-url}/Css/main.css?v={cache-buster}">
    <link rel="stylesheet" href="{assets-url}/Css/{device-css}?v={cache-buster}" nonce="{csp-nonce}">
    <link rel="stylesheet" href="{assets-url}/Css/{viewmode-css}?v={cache-buster}" nonce="{csp-nonce}">
    
    <meta name="csp-nonce" content="{csp-nonce}">
    <style>.vdisplay { display: none; }</style>
</head>
<body data-device="{device-type}" data-view="{view-mode}">
    <input type="hidden" name="csrf_token" id="csrf-global" value="{csrf-token}">
    
    <div class="containerdiv app-layout-desktop" id="desktop">
        <!-- header.php include -->
        <main class="l-main-wrapper" id="main-content" aria-busy="false">
            {container}
        </main>
        <audio controls id="audio" class="vdisplay" src="{audio-src}"></audio>
        <!-- player scripts -->
        <!-- footer.php include -->
    </div>

    <!-- Inline Script: RouterConfig + session data -->
    <script nonce="{csp-nonce}">
        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.RouterConfig = {
            enabled: true,
            csrfToken: '{csrf-token}',
            protectedRoutes: {protected-routes-json},
            user: {user-data-json},
            domain: {domain-json},
            initialRoute: '{route}',
            ...
        };
    </script>
    
    <!-- Defer Scripts: device-loader + scale + main.js -->
    <script nonce="{csp-nonce}" src="{assets-url}/js/device-loader.js?v={cache-buster}"></script>
    <script nonce="{csp-nonce}" defer src="{assets-url}/js/router/scale/scale.coordinator.js?v={cache-buster}"></script>
    <script nonce="{csp-nonce}" type="module" defer src="{assets-url}/js/router/main.js?v={cache-buster}"></script>
    
    <noscript><p>Bu uygulama JavaScript gerektirmektedir.</p></noscript>
</body>
</html>
```

---

## 4. CSS Yükleme Sırası

| # | CSS | Kullanım |
|---|-----|----------|
| 1 | `main.css` | ITCSS 9-layer ana stil |
| 2 | `08_Devices/d-{device}.css` | Cihaz bazlı (desktop, tablet, mobile) |
| 3 | `09_ViewModes/v-{mode}.css` | Görünüm modu (home, pro, studio) |
| 4 | `auth-bundled.css` | Auth sayfaları (opsiyonel) |

**Device Detection:** `DeviceCssMap::toCssPath($deviceType)` ile cihaz tipinden CSS dosyasına dönüşüm.

---

## 5. JS Yükleme Sırası

| # | Script | Tip | Amaç |
|---|--------|-----|------|
| 1 | `device-loader.js` | Sync | Cihaz algılama + resize handler |
| 2 | `scale/scale.coordinator.js` | Defer | Ölçeklendirme koordinatörü |
| 3 | `scale/header.scale.js` | Defer | Header ölçekleme |
| 4 | `scale/footer.scale.js` | Defer | Footer ölçekleme |
| 5 | `scale/home.scale.js` | Defer | Home ölçekleme |
| 6 | `router/main.js` | Module, Defer | SPA Router entry point |

---

## 6. Inline Script — RouterConfig

```javascript
window.CoreMusic = window.CoreMusic || {};
window.CoreMusic.RouterConfig = {
    enabled: true,                    // SPA aktif mi?
    csrfToken: 'abc123...',           // CSRF token (JS sync eder)
    protectedRoutes: ['home', 'kesfet', 'ayarlar'],  // Auth gerektiren route'lar
    user: {
        id: 42,
        username: 'bayram',
        image: '/images/user.jpg',
        role: 'admin',
        permissions: ['music.read', 'admin.write']
    },
    domain: {
        host: 'home.coremusic.net',
        port: 81,
        scheme: 'http',
        isHttps: false
    },
    initialRoute: 'home',             // İlk yükleme route'u
    customGuard: null,                 // Özel guard fonksiyonu
    logLevel: 'info'                   // Log seviyesi
};
```

---

## 7. CSP Nonce Kullanımı

Tüm `<script>` ve `<link rel="stylesheet">` tag'lerinde `nonce` attribute'u zorunlu:

```html
<script nonce="{csp-nonce}" defer src="..."></script>
<link rel="stylesheet" href="..." nonce="{csp-nonce}">
```

**ADR-012 Uyumlu:** CSP `strict-dynamic` + `nonce-based`. Nonce her istekte `SessionInitializer` tarafından üretilir.

---

## 8. Session Data Injection (L0→L3 Layer Fix)

**Kritik:** `HtmlShellRenderer` `$_SESSION`'a doğrudan erişmez. Session verisi `PageRouterKernel` tarafından inject edilir:

```php
// PageRouterKernel'de:
$sessionData = $request['_session'] ?? $_SESSION;
$html = $this->shellRenderer->render($container, $route, $meta, $csrfToken, $protectedRoutes, $sessionData);
```

**L3→L0 Layer Violation Önleme:** Renderer sadece inject edilen `$sessionData` array'ini kullanır.

---

## 9. Auth Route Detection & Conditional Branching

`HtmlShellRenderer::render()` içinde `$isAuthRoute` boolean'ı **6 ayrı conditional bloğu** yönetir:

```php
$isAuthRoute = AuthRouteConfig::isAuthRoute($route);
```

### 9.1 CSS Yükleme Farkları

| Blok | Auth Route | Non-Auth Route |
|------|-----------|---------------|
| Ana CSS | `auth-bundled.css` | `main.css` (ITCSS 9-layer) |
| Cihaz CSS | `d-auth-{device}.css` | `d-{device}.css` |
| View Mode CSS | Yok | `v-{mode}.css` |
| Sidebar CSS | Yok | `s-{mode}.css` |

### 9.2 Body Class Farkı

```php
// Auth route:
'<body class="auth-page" data-device="...">'

// Non-auth route:
'<body data-device="{device-type}" data-view="{view-mode}">'
```

### 9.3 Main Tag Farkı

```php
// Auth route:
'<main id="main-content">' . $container . '</main>'

// Non-auth route:
'<main class="l-main-wrapper" id="main-content" aria-busy="false">' . $container . '</main>'
```

### 9.4 Device Loader Data Attribute

```php
// Auth route:
'data-is-auth="true"'

// Non-auth route:
'data-is-auth="false"'
```

### 9.5 Layout Updater Script

```php
// Non-auth route SADECE:
'<script nonce="{nonce}" src="{assets}/js/device-layout-updater.js"></script>'

// Auth route: Bu script YÜKLENMEZ
```

### 9.6 Auth-Specific Scripts

```php
// Auth route SADECE:
'<script nonce="{nonce}" src="{assets}/js/auth-theme.js"></script>'
'<script nonce="{nonce}" src="{assets}/js/auth-gender-bg.js"></script>'

// Sayfa bazlı ek scriptler (auth route'larda):
// select-gender → gender-select.js
// login → login.js
// register → register.js

// Non-auth route: Bu scriptler YÜKLENMEZ
```

---

## 10. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **CSP nonce kaybı** | SessionInitializer'dan yeniden oku | ADR-012 |
| **Device CSS yok** | Varsayılan desktop CSS | — |
| **Header/footer dosyası yok** | Boş string | — |
| **Cache buster** | `filemtime()` veya versiyon | — |
| **Auth route** | Minimal shell (header/footer yok) | ADR-043 |
| **Gender teması** | `data-gender` attribute'u | ADR-044 |

---

## 11. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[route-config]] | Route yapısı |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce |
| [[ADR-044-dynamic-user-theme-engine]] | Tema engine |

---

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **ADR Uyumlu** | ✅ 012, 043, 044 |
| **Auth Route Branching** | 6 conditional blok (CSS, body, main, data-attr, layout-updater, auth-scripts) |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

## 11. **KRİTİK DÜZELTME — JS Yükleme Listesi Güncel Değil (§5)**

**Faz 2c bulgusu (2026-09-08):** §5 tablosundaki scale script'leri **artık mevcut değil**. MEMORY 2026-09-04 kaydı: "Hibrit Scale Motoru Refactor (SOLID ES6+) — scale*.js 4 dosya silindi, ScaleManager.js (TierResolver + TransformApplier + declarative rules + DPR/aspect + EventBus), main.js v6.0.0".

**SİLİNEN dosyalar:** `scale.coordinator.js`, `header.scale.js`, `footer.scale.js`, `home.scale.js`
**YENİ dosya:** `ScaleManager.js` (v6.0.0 — TierResolver + TransformApplier + EventBus `scale:applied`)

**Güncel JS yükleme sırası (doğrulanmış):**

| # | Script | Tip | Amaç |
|---|--------|-----|------|
| 1 | `device-loader.js` | Sync (IIFE) | Cihaz algılama + viewport cookie + TV/1024 sync |
| 2 | `device-layout-updater.js` | — | Cihaz değişiminde layout güncelleme (non-auth) |
| 3 | `ScaleManager.js` | Module, Defer | Hibrit scale motoru (4 eski dosyanın yerine) |
| 4 | `router/main.js` | Module, Defer | SPA Router entry (v6.0.0 — ScaleManager import+init) |

**Ders:** Bu tür sapmaların kökü — kod refactor'u vault'a yansıtılmamış. Faz kontrol listesi (engine §12.6) maddesi 2-5 bu riski kapatır; JS dosya listesi `assets.coremusic.net/js/` glob'la doğrulanmalıdır.

§5 tablosu güncel haliyle yukarıdaki gibidir; eski satırlar tarihsel referans olarak bu bölümde korunmuştur.

---

## 12. Referans ↔ Gerçek Eşleştirme (Faz 2c — 2026-09-08)

| Öğe | Referans (bu dosya örnekleri) | Gerçek Kod | Doğrulama |
|-----|-------------------------------|------------|-----------|
| `HtmlShellRenderer` sınıfı | §2 yapısı | `shared/src/PageRouter/HtmlShellRenderer.php` | ✅ Mevcut (brain §18B + MEMORY viewportW/H ekleme kaydı) |
| Auth branching | §9 — 6 conditional blok | Gerçek kodda "6 if bloğu" (brain §18B tablo) | ✅ Eşleşiyor |
| Session inject | §8 — PageRouterKernel `_session` | Kernel yapısı kod okuması devam | ⏳ imza teyidi |
| viewportW/H | §2 örnek yok | Kernel + Renderer'a eklendi (2026-09-03 Faz 1-5) | ✅ MEMORY kaydı |
| DeviceCssMap | §4 `toCssPath()` | `shared/src/Device/DeviceCssMap.php` | ✅ brain §18B |
| header/footer path | §2 HEADER_PATH/FOOTER_PATH sabitleri | `home.coremusic.net/` dosyaları | ✅ |
| scale script listesi | §5 (eski) | §11 — DEĞİŞTİ | ✅ düzeltildi |

**Sonuç:** Sınıf IMPLEMENTED; davranış dokümanı büyük ölçüde gerçek. §5 liste sapması tek büyük düzeltme.

---

## 13. Nonce Akışı — Shell İçinde Tam Hat

```
SecurityHeadersMiddleware (#4)        [IMPLEMENTED]
  → _csp_nonce üret (random_bytes(32) → base64)
SessionManager katmanı (#5)           [KISMEN — SessionInitializer]
  → nonce session'a kaydet
HtmlShellRenderer
  → nonce'u al (request attribute / session)
     → CSP header'a yaz (buildCsp)
     → <meta name="csp-nonce">
     → her <script nonce> / <link nonce> tag'ine
        → JS tarafı: CsrfSyncManager token'ı sync eder (§6 RouterConfig.csrfToken)
           → DOM patch sonrası yeni nonce meta'dan okunur (csrf.md §7.2)
```

**Kritik zincir kuralı:** Header'daki nonce ile tag nonce'ları AYNI olmalı — biri farklıysa script engellenir. Pipeline sırası (#4 önce #5) bu yüzden immutable'dır.

**PageCache etkileşimi (route-config §13 uyarısı):** cache'lenen shell eski nonce taşır → tüm script'ler engellenir. Çözüm seçenekleri: (a) shell cache'siz, yalnız container fragment cache; (b) nonce'suz external script + CSP hash. Karar PLANNED — Faz 2c devam tasarım sorusu.

---

## 14. Cache Buster Politikası

| Yöntem | Açıklama | Tercih |
|--------|----------|--------|
| `?v={filemtime}` | Dosya değişince otomatik değişir | Dev + küçük prod |
| `?v={versiyon sabiti}` | Release sürümü (composer/app version) | Prod standart — kontrol edilebilir |
| İçerik hash'i | En kesin ama maliyetli | PLANNED |

Kural: Tüm asset referansları buster taşır; buster'sız asset stale-cache üretir. Buster üretimi renderer'da tek noktadan yapılır.

---

## 15. CSS Sırası — ITCSS 9-Layer Bağlantısı

§4'teki `main.css` ITCSS katmanlarını tek dosyada toplar (asset pipeline concatenation hedefi):

```
01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors
08_Devices (ayrı d-{device}.css — behavioral)
09_ViewModes (ayrı v-{mode}.css — view override)
```

**Farklılıkların ayrı dosyalarda olması:** device/view CSS'i main.css'ten AYRI tutulur — tek bileşen + override ilkesi (Guardrail #17). Renderer cihaz kararına göre hangi override dosyasının yükleneceğini DeviceCssMap'ten seçer.

Auth sayfalarında `auth-bundled.css` minimal set — ana ITCSS yüklenmez (hafif auth shell).

---

## 16. Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | Standart route shell | meta csp-nonce + global csrf_token input |
| 2 | Auth route shell | auth-bundled.css + auth-page body class |
| 3 | Non-auth shell | device-layout-updater.js yüklenir |
| 4 | Auth route'da layout-updater | YÜKLENMEZ (§9.5) |
| 5 | gender setli session | `data-gender` attribute |
| 6 | admin kullanıcı | RouterConfig.user.role=admin |
| 7 | protectedRoutes JSON | kesfet/ayarlar gibi auth route key'leri |
| 8 | RouterConfig.initialRoute | dispatch edilen route |
| 9 | Eksik header.php | boş string — fatal yok (§10) |
| 10 | viewport cookie | device kararına yansır (brain §18B) |
| 11 | scale script | ScaleManager.js yüklü, eski scale*.js YOK |
| 12 | noscript | fallback mesaj |

---

## 17. Diagnostics (tekrarlanabilir)

```powershell
# 1. Gerçek sınıf + viewport parametreleri
Select-String -LiteralPath "shared\src\PageRouter\HtmlShellRenderer.php" -Pattern "viewportW|class HtmlShellRenderer"

# 2. Eski scale script referansı kalıntısı (beklenen: 0 — temizlendiyse)
Select-String -LiteralPath "shared\src\PageRouter\HtmlShellRenderer.php" -Pattern "scale\.coordinator|header\.scale" -ErrorAction SilentlyContinue

# 3. Yeni scale motor referansı
Select-String -LiteralPath "shared\src\PageRouter\HtmlShellRenderer.php" -Pattern "ScaleManager"

# 4. JS tarafında gerçek dosyalar
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Filter "*.js" | Select-Object Name

# 5. DeviceCssMap
Select-String -LiteralPath "shared\src\Device\DeviceCssMap.php" -Pattern "toCssPath" -ErrorAction SilentlyContinue
```

---

## 18. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Doküman-kod JS listesi sapması | YAŞANDI (düzeltildi) | Orta | §11 + js glob doğrulama |
| 2 | PageCache + nonce çakışması | Orta | Kritik | csp.md §21 + route-config §13 — çözüm bekliyor |
| 3 | HEADER_PATH/FOOTER_PATH sabitleri (LSP ailesi) | Orta | Orta | Config kökeni çözümü |
| 4 | RouterConfig'e secret sızması | Düşük | Kritik | Yalnız public user/domain verisi |
| 5 | Cache buster'sız asset | Orta | Düşük | §14 kural |
| 6 | Auth shell'e ağır ITCSS yüklenmesi | Düşük | Düşük | auth-bundled.css ayrımı |

---

## 19. Ek SSS

**S: Renderer neden $_SESSION'a erişmiyor?**
C: L0→L3 layer fix (§8) — session verisi Kernel'dan parametreyle akar; renderer saf fonksiyon gibi çalışır, test edilebilirlik artar.

**S: `data-gender` nereden geliyor?**
C: ThemeManager (ADR-044) — shell `<html data-gender>` attribute'u; CSS temaları buna göre aktive olur. Gender seçilmeden 'neutral'.

**S: Auth sayfalarında neden device-layout-updater yok?**
C: Auth sayfaları sabit merkezli minimal layout — cihaz değişiminde layout updater gerekmiyor; ayrıca gender-bg/theme script'leri kendi davranışını taşır (§9.6).

**S: RouterConfig.user neden JSON olarak inline?**
C: SPA'nın ilk render'da user bilgisine ihtiyacı var (guard kararları) — ekstra API çağrısı önlenir. Hassas veri YASAK: id/username/role/permissions yeterli (§18 risk 4).

**S: audio elementi neden shell'de?**
C: Kalıcı oynatma — SPA navigasyonunda audio kesintisiz devam etmeli; DOM patch audio'ya dokunmaz. `<audio id="audio" class="vdisplay">` gizli eleman olarak sabit kalır.

**S: Cache buster release'de nasıl değişir?**
C: §14 — prod'da versiyon sabiti (composer/app release ile). filemtime dev'de yeterli.

**S: Bu şablon auth ve home domain'lerinde ortak mı?**
C: Evet — shared renderer; fark route kararlarından gelir (§9 branching). Domain başına ayrı renderer YOK (DRY).

---

## 20. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 2.0.0 | 2026-09-05 | Device-aware güncelleme (viewportW/H) |
| 3.0.0 | 2026-09-08 | Faz 2c: **§11 JS liste sapması düzeltildi** (scale*.js silinmişti → ScaleManager.js); §12 referans↔gerçek; §13 nonce tam hat + PageCache uyarısı; §14 buster politikası; §15 ITCSS bağlantısı; §16-§19 test/diagnostics/risk/SSS |

---

## 21. Head Tag Bileşenleri (buildHeadTag detay)

| Bileşen | Kaynak | Not |
|---------|--------|-----|
| `<html lang="tr">` | Sabit (i18n PLANNED — ADR-079) | Dil anahtarı shell'e girer |
| `data-gender` | ThemeManager/session | female/male/neutral (ADR-044) |
| viewport meta | Sabit | `user-scalable=no` embedded dokunma standardı |
| title | SpaRoute.title + app adı | route-config §2 alanı |
| preconnect assets | DomainConfig assets URL | font/CSS hız önceden bağlanma |
| main.css + device + viewmode + auth | §4 sıra | cache buster'lı |
| `<meta name="csp-nonce">` | Session nonce | JS CsrfSyncManager okur |
| favicon | Sabit | — |

Kural: Head üretiminde koşulsuz HTML birleştirme yok — her bileşen kaynağı tabloda tanımlı.

---

## 22. Body Content Bileşenleri

```
<body data-device data-view data-is-auth>
  csrf_token hidden input (global — #csrf-global)
  .containerdiv (desktop container)
    header.php include (non-auth)
    <main id="main-content"> {container} </main>
    <audio id="audio" class="vdisplay">   ← kalıcı oynatma (§19 SSS)
    footer.php include (non-auth)
  inline RouterConfig script (§6)
  defer script'ler (§11 güncel sıra)
  <noscript>
```

Bileşen sırası sabittir — audio'nun container DIŞINA çıkması navigasyon sırasında kesinti olmaması içindir.

---

## 23. Defer Mantığı

| Script | Yükleme | Neden |
|--------|---------|-------|
| device-loader.js | sync (head-sonu/body-başı) | Cookie'yi ilk render'dan önce yazmalı — PHP zaten okuduğu için sonraki navigasyon için |
| ScaleManager.js | defer + module değil | main.js'ten ÖNCE global hazır olmalı |
| main.js | type=module defer | ES module import zinciri; defer sırayla |

Kural: module'ler arası yükleme sırası import grafiğiyle garantidir; sync script yalnız device-loader'a izin verilir (kural dışı sync eklemek render bloklar).

---

## 24. DeviceCssMap — Seçim Örneği

`DeviceCssMap::toCssPath($deviceType)` çıktıları (brain §18B 7 cihaz):

| deviceType | CSS Dosyası |
|------------|-------------|
| phone | d-phone.css |
| tablet | d-tablet.css |
| embedded | d-embedded.css |
| laptop | d-laptop.css |
| desktop | d-desktop.css |
| 4k-tv | d-4k-tv.css |
| 4k-monitor | d-4k-monitor.css |

View mode CSS: `v-home.css`, `v-pro.css`, `v-studio.css`, `v-car.css` (4 mod — ADR-045).

Kural: Var olmayan cihaz tipi → desktop fallback (§10 edge). DeviceCssMap tek doğruluk kaynağı — renderer kendi eşlemesini kopyalamaz.

---

## 25. Inline Script Güvenlik Kuralları

| Kural | Neden |
|-------|-------|
| Yalnız nonce'lu inline script | CSP (§13) |
| JSON encode ile veri gömme | `</script>` injection önleme (`json_encode` + flags) |
| Kullanıcı içeriği RouterConfig'e koyulmaz | XSS yüzeyi |
| Yalnız public alanlar (id/role/permissions) | §18 risk 4 |
| Session verisi inject'ten gelir | §8 layer fix |

**Kritik:** RouterConfig'a `json_encode` WITHOUT `JSON_HEX_TAG` yazılırsa `</script>` içinde payload çalışır. Standart: `json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)`.

---

## 26. Auth Route → Script Eşlemesi (§9.6 Genişletme)

| Route | Ek Script |
|-------|-----------|
| select-gender | gender-select.js |
| login | login.js |
| register (+step) | register.js |
| Ortak (tüm auth) | auth-theme.js, auth-gender-bg.js |

Kaynak: ui-design screens A-auth klasörü (login/register/gender-select ekranları). Non-auth sayfaların sayfa-bazlı script'leri fragment üzerinden yüklenir (SPA navigasyonu) — shell'de yalnız ortak set.

---

## 27. render() Sözleşmesi

```php
render(
    string $container,        // route'a göre üretilen sayfa içeriği
    string $route,            // normalize route key (initialRoute)
    array  $meta,             // SpaRoute.meta (redirect_to vb. — route-config §15)
    string $csrfToken,        // L1 pipeline'dan geçmiş token
    array  $protectedRoutes = [],   // RouterConfig.protectedRoutes (Registry'den)
    array  $sessionData = []  // Kernel inject (§8 — MM_* kaynaklı public alanlar)
): string
```

| Parametre | Tüketen | Not |
|-----------|---------|-----|
| `$container` | main tag içi | Auth/non-auth farkı §9.3 |
| `$meta` | RouterConfig + head | redirect_to meta auth sayfalarında JS redirect üretir |
| `$sessionData` | RouterConfig.user | MM_* → public alan dönüşümü Kernel'da |

İmza referans koddan; gerçek dosyada satır düzeyi teyit devam görevi (guard-pipeline §24 protokolü).

---

## 28. Ek SSS

**S: `<style>.vdisplay{display:none}</style>` neden inline?**
C: Audio/gizli elementlerin CSS yüklenmeden görünmesini engeller (FOUC önleme) — kritik 1 satır; CSP style-src unsafe-inline bunu kapsar (csp.md §5.3).

**S: `aria-busy="false"` neden var?**
C: SPA navigasyon başlangıcında `true`'ya çevrilir (js-router) — ekran okuyucu yükleniyor durumunu bildirir (WCAG live region pratiği).

**S: audio src nereden geliyor?**
C: PlayerController (JS) çalışma anında atar — shell'de boş/placeholder. Statik attribute örnekleyicidir.

**S: `data-is-auth` ne işe yarar?**
C: device-layout-updater.js'in auth sayfalarında çalışmamasını JS tarafında da bilmesi — PHP yalnız HTML üretir, JS kararını attribute'tan okur (§9.4).

**S: Header/footer include auth'ta yok — login sayfası header'sız mı?**
C: Evet — auth-bundled.css + minimal shell; auth ekranları kendi görsel kimliğini taşır (ui-design A-auth mockup'ları). ADR-043 konsolidasyon kararına uygun.

---

## 29. İzlenebilirlik Tablosu Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| scale*.js silindi → ScaleManager.js | MEMORY 2026-09-04 + §11 | Oturum kaydı |
| 6 if branching | brain §18B | ✅ |
| viewportW/H ekleme | MEMORY 2026-09-03 | ✅ |
| DeviceCssMap 7 cihaz | brain §18B | ✅ |
| auth-bundled.css | Referans kod + §9.1 | Kod teyidi devam |
| JSON_HEX_TAG kuralı | PHP standardı | Genel güvenlik pratiği |

---

## 30. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Bölüm Sayısı** | 30 |
| **ADR Uyumlu** | ✅ 012, 021, 043, 044 (+083 çapraz) |
| **Kritik Düzeltme** | §11 JS liste sapması (scale*.js → ScaleManager.js) |
| **Test Senaryosu** | 12 (§16) |
| **Risk Kaydı** | 6 (§18) |
| **Zero Hallucination** | ✅ (sapma açıkça belgelendi) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
