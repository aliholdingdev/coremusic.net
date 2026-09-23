---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — PHP Source Architecture Reference"
type: reference
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/01-php-source-architecture.md"
  source_of_truth: ".ai/CLAUDE.md §18C · .ai/brain.md §18B"
---

# CoreMusic — PHP Source Architecture Reference

**Zorunlu Bağlantılar:** [[05-responsive-architecture]] · [[10-device-specific-guidelines]]

---

## 1. Amaç

PHP backend yapısının ve DeviceManager entegrasyonunun **referans kaynağıdır**. Frontend geliştirme sırasında backend ile etkileşim noktaları burada tanımlanır.

---

## 2. Directory Yapısı

```
home.coremusic.net/
├── index.php                    ← Entry point (PageRouterKernel)
├── header.php                   ← Single Header View
├── footer.php                   ← Single Footer View
├── pages/
│   └── home.php                 ← Single Home View
├── include/
│   ├── auth.php                 ← Auth check
│   ├── session.php              ← Session init
│   └── container.php            ← DI container
└── config/
    ├── constants.php            ← Sabitler
    └── app.php                  ← App config

shared/src/
├── Device/
│   ├── DeviceDetector.php       ← Cihaz tespiti
│   ├── DeviceManager.php        ← Merkezi cihaz yönetimi
│   └── DeviceCssMap.php         ← CSS haritası
├── Theme/
│   └── ThemeManager.php         ← Tema yönetimi (ADR-044)
├── ViewMode/
│   └── ViewModeManager.php      ← View mode yönetimi (ADR-045)
└── PageRouter/
    ├── PageRouterKernel.php     ← Ana kernel
    ├── PageRouter.php           ← Tekil sayfa çözücü
    └── HtmlShellRenderer.php    ← HTML shell
```

---

## 3. DeviceManager API

```php
// Cihaz tespiti
$dm = DeviceManager::fromRequest(
    viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0),
    viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0),
);

// Tier kararları
$dm->isPhone();                        // ≤767px
$dm->isEmbedded();                     // RPi5
$dm->isTablet();                       // Tablet
$dm->isLaptop();                       // Laptop
$dm->isDesktop();                      // Desktop
$dm->is4kTv();                         // 4K TV
$dm->is4kMonitor();                    // 4K Monitor

// Layout kararları
$dm->shouldRenderEmbeddedLayout();     // Embedded/Tablet
$dm->shouldRenderWideLayout();         // 1025-2560px
$dm->shouldRender4kLayout();           // ≥2561px
$dm->shouldShowFallback();             // Always false
$dm->shouldRenderWelcomePopup();       // Sadece RPi5 1024×600

// Feature toggles
$dm->showVolume();                     // Phone hariç
$dm->showFullMetadata();               // Sadece geniş ekran
$dm->showSidebar();                    // Wide/Laptop
$dm->showSeekBar();                    // Tümü
$dm->showPlaylistToggle();             // Phone hariç
$dm->showPodcastWidget();              // Sadece geniş
$dm->showRadioWidget();                // Widget sayısına bağlı
$dm->showUtilityIcons();               // Phone hariç
$dm->showFooterSeekSlider();           // Phone hariç

// CSS outputs
$dm->layoutClass();                    // "layout--desktop"
$dm->allClasses();                     // "layout--desktop device--desktop is-wide"
$dm->dataAttributes();                 // 'data-device="desktop" data-wide="true"'
```

---

## 4. PHP'de Yasak Kodlar

```php
// ❌ YASAK — Sunum kararı PHP'de
$dm->isPhone() ? 'padding: 8px' : 'padding: 16px';
echo '<div style="width: ' . ($dm->is4k() ? '800px' : '400px') . '">';

// ✅ DOĞRU — Davranışsal karar PHP'de
if ($dm->showVolume()) { /* volume HTML */ }
$cssClass = $dm->layoutClass();
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| PHP Files | 12 |
| DeviceManager Methods | 25+ |
| Cross References | 2 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
