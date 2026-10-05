---
title: "CoreMusic — assets.coremusic.net Agent Talimatları"
type: agent-registry
folder: "assets.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# assets.coremusic.net — AGENTS.md

**Zorunlu Bağlantılar:** [[../.ai/AGENTS.md]] · [[./CLAUDE.md]] · [[../.ai/ui-design/01-mockup-index.md]]

## 1. Amaç

Statik asset servisi: tüm alt domainlerin CSS, JS, font ve görsel varlıkları. ADR-001 (Vanilla JS + ITCSS) mimarisinin **tek uygulama noktasıdır**. Framework yasağı bu klasörde en katı şekilde uygulanır.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `.htaccess`, `web.config` | Apache + IIS statik servis kuralları (cache, MIME) |
| `Css/08_Devices/` + `Css/auth-bundled.css` | Cihaz/auth **tek giriş noktaları** — `Css/main.css` **YOKTUR** (2026-09-30 silindi; disk 0 isabet) |
| `Css/01_Abstracts/` | 20 token dosyası (breakpoint, color-mode, colors, design, fonts, layout-tokens + `-{mobile,tablet,1024,1920,3540,3840}` cihaz ayrımı, light-glass, login, scale-hybrid, semantic, primitive, theme-config, welcome-banner, widget-grid) + 2 kopya artığı (`a-layout-tokens copy*.css` — okunmaz) ⚠️ `a-layout-tokens.css` (eski BASE) diskte **YOK** → BASE artık `a-layout-tokens-1024.css` |
| `Css/02_Base/` | b-base-core, l-main-structural, page-layout |
| `Css/03_Layout/` | _footer, _header, _sidebar, _widget-grid |
| `Css/04_Components/` | 13 dosya: c-badge, c-buttons, c-card, c-footer-seek, c-footer-volume, c-forms, c-home-song-btn, c-modal, c-progress, c-scrollbar-accent, c-toast, c-toggle, _home-components, _player-info, _welcome-banner |
| `Css/05_Pages/` | 12 dosya: p-album-detail, p-albums, p-artists, p-login-view, p-playlist, p-select-gender, p-settings + partial _home, _home-inline, _home-layout, _player, _welcome |
| `Css/06_Utilities/` | u-helpers-utility |
| `Css/07_Vendors/` | v-bootstrap-lib + bootstrap* (salt okunur) |
| `Css/08_Devices/` | 15 device CSS (8 normal: 4k, 4k-monitor, 4k-tv, desktop, embedded, laptop, phone, tablet + 7 auth varyantı) |
| `Css/09_ViewModes/` | v-car, v-home, v-pro, v-studio |
| `Css/10_Helpers/` | h-ellipsis (iskelet) |
| `Css/11_OAuth/` | oauth.css |
| `Fonts/` | 105 font dosyası (DMSans ağırlıklı) |
| `Image/background/` | login arka planları + welcome popup |
| `Image/profiles/` | default-avatar |
| `Image/res-pink/` | Ana ikon/görsel kütüphanesi (actions, app, banner, disk, logo, power-system, quick-bar, wifi alt klasörleri) |
| `js/main.js`, `js/device-loader.js`, `js/device-layout-updater.js`, `js/devices.config.js`, `js/oauth-manager.js` | Giriş noktaları |
| `js/auth/` | auth-gender-bg, gender-select |
| `js/core/` | CoreMusicApp, EventBus, footer.init, helper |
| `js/features/` | CardManager, PlayerController, ScrollManager, TouchManager, WidgetManager |
| `js/managers/` | DeviceManager, ScaleManager, SidebarManager, ThemeManager, ViewModeManager |
| `js/router/` | 28 dosya — SPA router çekirdeği (ADR-021, ADR-083) |
| `js/router/config/` | auth-routes, css-selectors, error-types, events, headers, navigate-utils, signal-utils |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| UI Designer | CSS/JS üretimi — ITCSS 9-layer + BEM zorunlu |
| Security Engineer | CSP uyumu (ADR-012), `oauth-manager.js` token akışı denetimi |

## 4. Kurallar

### Zorunlu
1. Yeni UI kodu öncesi `[[../.ai/ui-design/01-mockup-index.md]]` ve ilgili PNG okunur (Guardrail #11)
2. Token tanımları `Css/01_Abstracts/`'ta; katman dışında ham hex/px token yazılmaz
3. JS: Vanilla ES6+, `var` yasak, `eval()` yasak, `innerHTML` yasak (DOMParser + TrustedTypes)
4. Device CSS ekleme → `js/devices.config.js` + `DeviceCssMap.php` (shared) ikisiyle senkron
5. BEM formatı: `block__element--modifier`

### Yasak
1. Framework, jQuery, React, Vue vb. (ADR-001)
2. `Css/07_Vendors/` dışına vendor kod kopyalamak
3. Inline style/script üretmek (CSP nonce uyumsuz)
4. Font/Image dosyalarını onaysız silmek veya yeniden adlandırmak (referans kırılması)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Bileşen envanteri | [[../.ai/ui-design/02-component-inventory.md]] |
| Design tokens master | [[../.ai/ui-design/tokens/design-tokens-master.md]] |
| CSS mimarisi | [[../.ai/architecture/k11-ux/itcss-9-layer.md]] |
| JS mimarisi | ../.ai/architecture/l3-presentation/js-module-architecture.md ⚠️ DEAD (faz6-D): ../.ai/architecture/l3-presentation/js-module-architecture.md — l3-presentation dizini yok; repo genelinde yok |
| CSS şablonu | [[../.ai/.templates/frontend/css-template.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
