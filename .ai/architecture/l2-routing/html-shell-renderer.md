---
type: architecture
category: l2
title: "HTML Shell Renderer — SPA HTML Shell Üretimi"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
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

## 9. Auth Route Detection

Auth sayfaları farklı HTML shell üretir:

```php
$isAuthRoute = AuthRouteConfig::isAuthRoute($route);

if ($isAuthRoute) {
    // Minimal shell: header/footer yok, sadece main
    return '<body class="auth-page" data-device="...">'
        . $csrfInput
        . '<main id="main-content">' . $container . '</main>';
}
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
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 012, 043, 044 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
