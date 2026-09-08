---
type: architecture
category: l3-presentation
title: "CoreMusic — Dark/Light Mode Architecture"
date: 2026-09-01
updated: 2026-09-01
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/l3-presentation/dark-light-mode-architecture.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
  related:
    - ".ai/architecture/l3-presentation/responsive-frontend-architecture.md"
    - ".ai/ui-design/responsive-device-mode.md"
---

# CoreMusic — Dark/Light Mode Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ADR-044-dynamic-user-theme-engine]] · [[responsive-frontend-architecture]]

---

## 1. Genel Bakış

CoreMusic'e **dark/light mode** desteği eklendi. Mevcut gender teması (female/male/neutral) ile birlikte çalışır.

**İki bağımsız eksen:**
1. **Color Mode:** dark / light / null (OS)
2. **Gender Theme:** female / male / neutral

**Kombinasyonlar:** female+dark, female+light, male+dark, male+light, neutral+dark, neutral+light

---

## 2. CSS Token Mimarisi

### 2.1 — Cascade Sırası

```
1. :root (dark varsayılan — mevcut token'lar)
2. [data-gender] (gender override)
3. @media prefers-color-scheme: light (OS default — sadece data-mode yoksa)
4. html[data-mode="light"] (kullanıcı override — en yüksek öncelik)
5. html[data-mode="light"][data-gender="*"] (gender + light kombinasyonu)
```

### 2.2 — Token Değişiklikleri (Light Mode)

| Token | Dark (Default) | Light |
|-------|----------------|-------|
| `--bg-base` | `#0d0a14` | `#f8f7fc` |
| `--bg-surface` | `#120d1a` | `#ffffff` |
| `--bg-elevated` | `#1a1025` | `#f0eef5` |
| `--text-primary` | `#ffffff` | `#1a1025` |
| `--text-secondary` | `rgba(255,255,255,0.72)` | `rgba(26,16,37,0.72)` |
| `--border-default` | `rgba(255,255,255,0.10)` | `rgba(0,0,0,0.12)` |
| `--glass-1-bg` | `rgba(255,255,255,0.04)` | `rgba(0,0,0,0.03)` |
| `--overlay-dark` | `rgba(0,0,0,0.70)` | `rgba(0,0,0,0.60)` |

### 2.3 — Dosya Yapısı

```
01_Abstracts/
├── a-color-mode-tokens.css    ← YENİ: Dark/light mode token'ları
├── a-semantic-token.css       ← Mevcut: Gender token'ları
├── a-light-glass-tokens.css   ← Güncellendi: Light mode glass override'ları
└── a-colors-token.css         ← Primitif renk paleti
```

---

## 3. HTML Attribute Sistemi

```html
<!-- Dark + Neutral (varsayılan) -->
<html lang="tr" data-gender="neutral">

<!-- Light + Female -->
<html lang="tr" data-gender="female" data-mode="light">

<!-- Dark + Male (explicit) -->
<html lang="tr" data-gender="male" data-mode="dark">

<!-- OS + Neutral (data-mode yok → prefers-color-scheme kullanılır) -->
<html lang="tr" data-gender="neutral">
```

---

## 4. PHP Tarafı

### 4.1 — ThemeManager.php

```php
// Mode tespiti
$colorMode = ThemeManager::detectMode($sessionData); // 'dark'|'light'|null

// Attribute üretimi
echo ThemeManager::injectAttributes($gender, $colorMode);
// Çıktı: data-gender="female" data-mode="light"
```

**Öncelik sırası:**
1. `$_SESSION['cm_color_mode']`
2. `$_SESSION['color_mode']`
3. `$_COOKIE['cm_color_mode']`
4. `null` (OS preferansını kullan)

### 4.2 — HtmlShellRenderer.php

```php
$gender    = ThemeManager::detect($sessionData);
$colorMode = ThemeManager::detectMode($sessionData);

echo '<html lang="tr" ' . ThemeManager::injectAttributes($gender, $colorMode) . '>';
```

---

## 5. JavaScript Tarafı

### 5.1 — ThemeManager.js

```javascript
// Mode değiştir
themeManager.setMode('light');     // Explicit light
themeManager.setMode('dark');      // Explicit dark
themeManager.setMode(null);        // OS preferansına dön

// Toggle
themeManager.toggleMode();         // dark ↔ light

// OS dinleme
themeManager.getSystemMode();      // 'dark'|'light'
```

### 5.2 — Event'ler

```javascript
eventBus.on('modechange', ({ mode, source }) => {
    // mode: 'dark'|'light'
    // source: 'user'|'system' (opsiyonel)
});
```

---

## 6. Cookie & Session

| Key | Cookie | Session | TTL |
|-----|--------|---------|-----|
| `cm_color_mode` | `dark`/`light` | `$_SESSION['cm_color_mode']` | 1 yıl |
| `cm_gender` | `female`/`male`/`neutral` | `$_SESSION['cm_gender']` | 1 yıl |

---

## 7. Settings Sayfası

Her platform için ayarlar sayfası:
- `home.coremusic.net/pages/ayarlar.php` — Dark/Light + Gender
- `car.coremusic.net/pages/ayarlar.php` — Sadece Dark/Light (driving-safe)

**UI:** Radio group butonları, large touch targets (car için 56px+)

---

## 8. Guardrail Uyumluluğu

| Guardrail | Durum |
|-----------|-------|
| #17 Single Component Responsive | ✅ Ayrı HTML yok |
| #10 No Frameworks | ✅ Vanilla JS |
| #5 Single Source of Truth | ✅ Token'lar `a-color-mode-tokens.css`'te |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css` | Dark/light mode token'ları |
| `assets.coremusic.net/Css/01_Abstracts/a-semantic-token.css` | Gender token'ları |
| `assets.coremusic.net/Css/01_Abstracts/a-light-glass-tokens.css` | Glass token'ları |
| `assets.coremusic.net/js/managers/ThemeManager.js` | JS tema yöneticisi |
| `shared/src/Theme/ThemeManager.php` | PHP tema yöneticisi |
| `shared/src/PageRouter/HtmlShellRenderer.php` | HTML shell üreticisi |
| `home.coremusic.net/pages/ayarlar.php` | Ayarlar sayfası |
| `car.coremusic.net/pages/ayarlar.php` | Car ayarlar sayfası |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Color Modes | 3 (dark, light, null/OS) |
| Gender Themes | 3 (female, male, neutral) |
| Combinations | 9 (3×3) |
| PHP Methods | 3 (detectMode, injectModeAttribute, injectAttributes) |
| JS Methods | 5 (setMode, toggleMode, getSystemMode, #applyMode, #loadMode) |
| Settings Pages | 2 (home, car) |
| ADR Uyumlu | ✅ 044 |
| Guardrail #17 Uyumlu | ✅ |

---

*Dark/Light Mode Architecture v1.0.0 — CoreMusic L3 Presentation*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-01*
*Mode: Red Team · Human Mode · Truth Mode*
