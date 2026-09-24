---
title: "CoreMusic — assets.coremusic.net Bağlam"
type: context
folder: "assets.coremusic.net"
category: domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
 authority: "assets.coremusic.net/CLAUDE.md"
 source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md · .ai/ui-design/01-mockup-index.md"
---

# assets.coremusic.net — CLAUDE.md (Detaylı Versiyon)

**Zorunlu Bağlantılar:** · [[../.ai/ui-design/01-mockup-index.md]] · [[../.ai/ui-design/02-component-inventory.md]] · [[../.ai/ui-design/tokens/design-tokens-master.md]]

---

## 1. Bağlam & Amaç

Tüm subdomainler (auth, home, music, admin...) CSS/JS'i bu servisten çeker. **Değişiklikler tüm platformu etkiler** — tek dosya değişikliği dahi çapraz etki analizinden geçer. L3 (Presentation) katmanının kod karşılığı burada ikamet eder.

**ADR-001 (Vanilla JS + ITCSS) mimarisinin tek uygulama noktasıdır.** Framework yasağı bu klasörde en katı şekilde uygulanır.

---

## 2. Dosya Yapısı (Detaylı)

```
assets.coremusic.net/
├── .htaccess ← Apache cache + MIME kuralları
├── web.config ← IIS statik servis
├── Css/
│ ├── main.css ← Master import (01-07 arası tüm katmanlar)
│ ├── auth-bundled.css ← Auth sayfaları için birleştirilmiş CSS
│ ├── CLAUDE.md ← Bu dosya
│ ├── 01_Abstracts/ ← Token'lar, değişkenler, fonksiyonlar
│ │ ├── a-layout-tokens.css ← Header/footer/spacing token'ları (v3.0.0)
│ │ ├── a-colors.css ← Renk token'ları
│ │ ├── a-fonts.css ← Font token'ları
│ │ ├── a-scale-hybrid.css ← Cihaz ölçekleme
│ │ ├── a-breakpoints.css ← Media query breakpoint'leri
│ │ ├── a-color-mode.css ← Dark/light mode
│ │ ├── a-design.css ← Genel tasarım token'ları
│ │ ├── a-light-glass.css ← Cam efekti token'ları
│ │ ├── a-login.css ← Login token'ları
│ │ └── a-semantic.css ← Anlamsal token'lar
│ ├── 02_Base/ ← Reset, base styles
│ │ ├── b-base-core.css ← Body, typography, reset
│ │ └── l-main-structural.css ← Ana yapısal stiller
│ ├── 03_Layout/ ← Header, footer, sidebar
│ │ ├── _header.css ← Header layout (var(--header-h))
│ │ ├── _footer.css ← Footer layout (var(--footer-h))
│ │ └── _sidebar.css ← Sidebar layout
│ ├── 04_Components/ ← Bileşen stilleri (BEM)
│ │ ├── c-footer-seek.css ← Footer seek slider
│ │ ├── c-footer-volume.css ← Footer volume control
│ │ └── c-scrollbar-accent.css ← Scrollbar özelleştirme
│ ├── 05_Pages/ ← Sayfa-specific stiller
│ │ ├── home-layout.css ← Home grid layout (4-tier)
│ │ ├── home-components.css ← Home bileşenleri
│ │ ├── login-view.css ← Login sayfası
│ │ ├── select-gender.css ← Gender seçimi
│ │ └── player.css ← Player stilleri
│ ├── 06_Utilities/ ← Helper classes
│ │ └── u-helpers-utility.css ← Utility fonksiyonlar
│ ├── 07_Vendors/ ← Third-party (minimal)
│ │ └── v-bootstrap-lib.css ← Bootstrap (minimal, framework değil)
│ ├── 08_Devices/ ← Cihaz-specific behavioral overrides
│ │ ├── d-phone.css ← Phone behavioral
│ │ ├── d-tablet.css ← Tablet behavioral
│ │ ├── d-embedded.css ← Embedded behavioral (touch)
│ │ ├── d-laptop.css ← Laptop behavioral
│ │ ├── d-desktop.css ← Desktop behavioral (hover)
│ │ ├── d-4k-tv.css ← 4K TV behavioral
│ │ ├── d-4k-monitor.css ← 4K Monitor behavioral
│ │ └── d-auth-*.css ← Auth-specific varyantlar
│ ├── 09_ViewModes/ ← View mode overrides
│ │ ├── v-home.css ← Home view mode
│ │ ├── v-pro.css ← Pro view mode
│ │ ├── v-studio.css ← Studio view mode
│ │ └── v-car.css ← Car view mode
│ └── 11_OAuth/ ← OAuth stilleri
│ └── oauth.css ← OAuth popup/modal
├── Fonts/ ← 105 font dosyası (DMSans ağırlıklı)
├── Image/
│ ├── background/ ← Login arka planları + welcome popup
│ ├── profiles/ ← Default avatar
│ └── res-pink/ ← Ana ikon/görsel kütüphanesi
│ ├── actions/ ← Aksiyon ikonları
│ ├── app/ ← Uygulama ikonları
│ ├── banner/ ← Banner görselleri
│ ├── disk/ ← Disk/albüm görselleri
│ ├── logo/ ← Logo dosyaları
│ ├── power-system/ ← Güç sistemi görselleri
│ ├── quick-bar/ ← Quick bar ikonları
│ └── wifi/ ← WiFi ikonları
└── js/
 ├── main.js ← Entry point (Router + tüm modülleri başlatır)
 ├── device-loader.js ← Cihaz tespiti (IIFE, non-module)
 ├── device-layout-updater.js ← Cihaz değişikliğinde layout güncelleme
 ├── devices.config.js ← Cihaz yapılandırması
 ├── oauth-manager.js ← OAuth token yönetimi
 ├── CLAUDE.md ← JS CLAUDE.md
 ├── auth/ ← Auth JS modülleri
 │ ├── auth-gender-bg.js
 │ └── gender-select.js
 ├── core/ ← Çekirdek modüller
 │ ├── CoreMusicApp.js ← Lifecycle manager
 │ ├── EventBus.js ← Pub/sub (bağımsız)
 │ ├── footer.init.js ← Footer başlatma
 │ └── helper.js ← Yardımcı fonksiyonlar
 ├── features/ ← Özellik modülleri
 │ ├── CardManager.js ← Event delegation
 │ ├── PlayerController.js ← State machine (STOPPED/PLAYING/PAUSED)
 │ ├── ScrollManager.js ← Route scroll restore
 │ ├── TouchManager.js ← Embedded touch gestures
 │ └── WidgetManager.js ← Home widgets
 ├── managers/ ← Yönetim modülleri
 │ ├── DeviceManager.js ← Cihaz tespiti (device-loader.js bridge)
 │ ├── ScaleManager.js ← Hibrit scale motoru
 │ ├── SidebarManager.js ← Sidebar yönetimi
 │ ├── ThemeManager.js ← ADR-044 gender theme
 │ └── ViewModeManager.js ← ADR-045 view mode
 └── router/ ← SPA router çekirdeği (28 dosya)
 ├── Router.js ← Ana router
 ├── guards.js ← Guard pipeline
 ├── config/ ← Route yapılandırması
 │ ├── auth-routes.js
 │ ├── css-selectors.js
 │ ├── error-types.js
 │ ├── events.js
 │ ├── headers.js
 │ ├── navigate-utils.js
 │ └── signal-utils.js
 └── + 21 diğer modül ← CacheLayer, DomPatcher, vb.
```

---

## 3. ITCSS 9-Katman Yapısı (Detaylı)

| Katman | Amaç | Dosya Sayısı | Kritik Kurallar |
|--------|------|-------------|-----------------|
| 01_Abstracts | Token'lar, değişkenler, fonksiyonlar | 10 | Ham renk/px token'ları burada tanımlanır |
| 02_Base | Reset, base styles | 2 | Typography, body, reset |
| 03_Layout | Header, footer, sidebar, grid | 3 | `var(--token)` kullanılır |
| 04_Components | Bileşen stilleri (BEM) | 3 | `block__element--modifier` formatı |
| 05_Pages | Sayfa-specific stiller | 5 | Grid layout, component yerleşimi |
| 06_Utilities | Helper classes | 1 | Override edebilmeli |
| 07_Vendors | Third-party (minimal) | 1 | Gerçek framework kullanımı değil |
| 08_Devices | Cihaz behavioral overrides | 7+ | Hover, touch, scrollbar |
| 09_ViewModes | View mode overrides | 4 | Home, pro, studio, car |

**Import sırası:** `main.css` → 01→02→03→04→05→06→07 (08 ve 09 ayrı import)

---

## 4. JS Modül Mimarisi

### 4.1 Modül Haritası

| Modül | Dosya | Sorumluluk |
|-------|-------|------------|
| Entry | main.js | Router + tüm modülleri başlatır |
| App | CoreMusicApp.js | Lifecycle manager |
| EventBus | EventBus.js | Pub/sub (bağımsız) |
| Router | router/ (28 dosya) | SPA routing, guards, cache |
| Player | PlayerController.js | State machine |
| Cards | CardManager.js | Event delegation |
| Scroll | ScrollManager.js | Route scroll restore |
| Touch | TouchManager.js | Embedded touch gestures |
| Widget | WidgetManager.js | Home widgets |
| Device | DeviceManager.js | Cihaz tespiti |
| Scale | ScaleManager.js | Hibrit scale motoru |
| Theme | ThemeManager.js | ADR-044 gender theme |
| ViewMode | ViewModeManager.js | ADR-045 view mode |
| Sidebar | SidebarManager.js | Sidebar yönetimi |

### 4.2 Modül İletişim Kuralları

```
Modüller arası iletişim → EventBus üzerinden
Doğrudan import → Yasak (modül bağımsızlığı)
Global değişken → Yasak (const/let zorunlu)
```

---

## 5. Komşu İlişkiler (Detaylı)

| Yön | Hedef | İlişki | Etki |
|-----|-------|--------|------|
| Parent | [[../AGENTS.md]] | Kök registry | — |
| Tüketen | [[../auth.coremusic.net/CLAUDE.md]] | Login/register CSS+JS çeker | Yüksek (görsel) |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | Home panel asset'leri çeker | Yüksek (görsel) |
| Mimari | [[../.ai/architecture/k11-ux]] | Presentation katman kuralları | Yüksek |
| Router karşılığı | [[../shared/src/PageRouter/CLAUDE.md]] | PHP tarafı HTML shell üretir, JS router devralır | Yüksek |
| Device senkron | [[../shared/src/Device/CLAUDE.md]] | DeviceCssMap ↔ devices.config.js | Yüksek |

---

## 6. Değişiklik Protokolü (Detaylı)

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | CSS değişikliği | İlgili ITCSS katmanında |
| 2 | Token etkisi | `01_Abstracts` kontrolü |
| 3 | Mockup karşılaştırması | PNG doğrulama (Guardrail #11) |
| 4 | JS değişikliği | Modül sınırına saygı (EventBus) |
| 5 | CSP uyumu | Inline style/script yasak |
| 6 | Görsel ekleme | `res-pink` alt kategorisi + BEM adlandırma |
| 7 | Audit | `log.md` + vault-sync |

---

## 7. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Framework (jQuery, React, Vue) | ADR-001 yasağı |
| 2 | `Css/07_Vendors/` dışına vendor kod kopyalamak | Dağınıklık |
| 3 | Inline style/script | CSP nonce uyumsuz |
| 4 | Font/Image dosyalarını onaysız silme | Referans kırılması |
| 5 | `var` kullanımı | `const`/`let` zorunlu |
| 6 | `eval()` / `Function()` | Güvenlik |
| 7 | `innerHTML` | XSS riski (DOMParser + TrustedTypes) |
| 8 | Hardcoded token | CSS variables + media queries |

---

## 8. Kalite Standartları

| Metrik | Hedef |
|--------|-------|
| ITCSS uyumu | %100 |
| BEM formatı | %100 |
| Framework kullanımı | %0 (sıfır) |
| CSP uyumu | %100 |
| Inline style | %0 (sıfır) |
| `var` kullanımı | %0 (sıfır) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
