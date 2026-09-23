---
title: "CoreMusic — Screen Specification Index (T08, T17 + All Tiers)"
type: spec
category: index
date: 2026-09-20
updated: 2026-09-20
version: 2.0.0
status: active
---

# Screen Specification Index — T08, T17 + All Tiers

CoreMusic UI screen specifications with ASCII art layouts based on 19 PNG mockups.

## Quick Reference — Completed Screens

| Tier | Device | Viewport | Screen | Status |
|------|--------|----------|--------|:------:|
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | home-dashboard.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | welcome-popup.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | albums.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | album-detail.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | artists.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | now-playing.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | file-browser.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | wifi-modal.md | ✅ |
| T08 | RPi5 7" Touch (Embedded) | 1024×600 | bluetooth-modal.md | ✅ |
| Shared | All Devices | 1024×600 | login.md | ✅ |
| Shared | All Devices | 1024×600 | select-gender.md | ✅ |
| T17 | 22" FHD Monitor (Desktop) | 1920×1080 | home-dashboard.md | ✅ |

## Screen Files — T08 Embedded (1024×600)

| # | Screen | File | PNG Reference |
|---|--------|------|---------------|
| 1 | Home Dashboard | `T08-embedded/home-dashboard.md` | `Linux 1024 - Home Page.png` |
| 2 | Welcome Popup | `T08-embedded/welcome-popup.md` | `Linux 1024 - Home Page Welcome Popup.png` |
| 3 | Albums | `T08-embedded/albums.md` | `Linux 1024 - Albumler Page.png` |
| 4 | Album Detail | `T08-embedded/album-detail.md` | `Linux 1024 - Albumler Details Detay Page.png` |
| 5 | Artists | `T08-embedded/artists.md` | `Linux 1024 - Singer Page.png` |
| 6 | Now Playing | `T08-embedded/now-playing.md` | `Linux 1024 - Playlist Page.png` |
| 7 | File Browser | `T08-embedded/file-browser.md` | `Linux 1024 - Göz At Page.png` |
| 8 | WiFi Modal | `T08-embedded/wifi-modal.md` | `Linux 1024 - Wifi Quick Page Base.png` |
| 9 | Bluetooth Modal | `T08-embedded/bluetooth-modal.md` | `Linux 1024 - Bluetooth Quick Page Base.png` |

## Screen Files — Shared (Auth Screens)

| # | Screen | File | PNG Reference |
|---|--------|------|---------------|
| 10 | Login | `shared/login.md` | `Linux 1024 - Login Girl.png` |
| 11 | Select Gender | `shared/select-gender.md` | `Linux 1024 - Select Gender.png` |

## Screen Files — T17 Desktop (1920×1080)

| # | Screen | File | PNG Reference |
|---|--------|------|---------------|
| 12 | Home Dashboard | `T17-monitor-22fhd/home-dashboard.md` | `Linux - 1920 - Home.png` |

## Planned Screens (Pending)

| Tier | Device | Viewport | Screens |
|------|--------|----------|---------|
| T01-T05 | Phone (Galaxy J7, iPhone 14-16, Galaxy S25-S26) | 720-1440 | home, auth, albums, artists, player |
| T06-T07 | Tablet (iPad Mini, iPad 10) | 1340-1840 | home, auth, albums, artists, player |
| T09-T11 | Tablet (iPad Pro, Surface Pro) | 2048-2880 | home, auth, albums, artists, player |
| T12-T16 | Laptop (MacBook Air/Pro) | 1920-3456 | home, auth, albums, artists, player, sidebar |
| T18-T24 | Monitor (FHD/QHD/4K/Ultrawide) | 1920-5120 | home, auth, albums, artists, player |
| T25-T28 | Smart TV (43"-98") | 1920-3840 | home, player, settings |
| T29-T30 | Car (Android Auto, CarPlay) | 800-1920 | home, player, navigation |
| T31-T33 | Smart Watch (Apple Watch, Galaxy Watch) | 396-502 | now-playing, controls |
| T34-T36 | Console (PS5, Switch, Steam Deck) | 1280-3840 | home, player, library |
| T37-T38 | Desktop App (Electron, Tauri) | Responsive | home, auth, all screens |
| T39-T40 | Mobile App (iOS, Android) | Responsive | home, auth, all screens |
| T41-T45 | Web/Special (PWA, NAS, Speaker, AR/VR) | varies | minimal UI |

## ASCII Art Layout Reference (1024×600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ HEADER (h:60, y:0-60)                                                                                     │
│ Logo(120×40)   Nav Links(8)                              User Avatar(40×40) + Theme Toggle + Search       │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ CONTENT (h:450, y:60-510)                                                                                 │
│ ┌─── Left Panel (42-60%) ───┐  ┌─── Right Panel (40-58%) ──────────────────────────────────────────────┐ │
│ │ Now Playing / Track List   │  │ Widgets / Detail Panel / Artist Info                                  │ │
│ └────────────────────────────┘  └───────────────────────────────────────────────────────────────────────┘ │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ FOOTER PLAYER (h:90, y:510-600)                                                                           │
│ 🎵 Song Info    [⏮][▶][⏹][⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ %100 │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Glassmorphism Design System

| Token | Value | Usage |
|-------|-------|-------|
| `--cm-primary` | #ff4fd8 | Pembe vurgu, progress bar, butonlar |
| `--cm-bg-glass` | rgba(255,255,255,0.15) | Cam efekti |
| `--cm-blur` | blur(20px) | Backdrop filter |
| `--cm-radius-md` | 12px | Orta radius |
| `--cm-radius-lg` | 16px | Büyük radius |
| `--cm-gradient` | linear-gradient(135deg, #ff4fd8, #a855f7) | Buton gradient |

## BEM Naming Convention

| Prefix | Component | Example |
|--------|-----------|---------|
| `.home-*` | Home Dashboard | `.home-content__now-playing` |
| `.album-*` | Albums | `.album-card__image` |
| `.artist-*` | Artists | `.artist-card__name` |
| `.track-*` | Track List | `.track-table__row` |
| `.modal-*` | Modals | `.modal__content` |
| `.wifi-*` | WiFi | `.wifi-network__badges` |
| `.bt-*` | Bluetooth | `.bt-device__action` |
| `.player-*` | Footer Player | `.player__controls` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode

## Quick Reference

| Tier | Device | Viewport | PPI | Class | Layout |
|------|--------|----------|-----|-------|--------|
| T1 | Galaxy J7 | 720×1280 | 267 | Phone HD | Stack, 3-tab bottom nav |
| T2 | iPhone 16 Pro Max | 1290×2796 | 460 | Phone FHD | Stack, 3-tab bottom nav |
| T3 | Galaxy S25 Ultra | 1440×3120 | 505 | Phone QHD | Stack, 3-tab bottom nav |
| T4 | Galaxy Z Flip | 1080×2640 | 425 | Phone Foldable | Stack, 3-tab bottom nav |
| T5 | OnePlus 12 | 1440×3168 | 510 | Phone Ultra | Stack, 3-tab bottom nav |
| T6 | iPad Mini | 1488×2266 | 327 | Tablet 8" | 2-column compact |
| T7 | iPad 10 | 1640×2360 | 264 | Tablet 10" | 2-column |
| T8 | Tab S9 | 1600×2560 | 274 | Tablet 11" | 2-column |
| T9 | iPad Pro 12.9 | 2048×2732 | 265 | Tablet 12" | 2-column wide |
| T10 | Surface Pro | 2880×1920 | 267 | Tablet 13" | Landscape 2-col |
| T11 | Tab S9 FE+ | 1200×2000 | 225 | Tablet 14" | 2-column |
| T12 | MacBook Air 13 | 2560×1664 | 224 | Laptop 13" | Sidebar 200px + content |
| T13 | MacBook Pro 14 | 3024×1964 | 254 | Laptop 14" | Sidebar 220px + content |
| T14 | MacBook Air 15 | 2880×1864 | 224 | Laptop 15" | Sidebar 240px + content |
| T15 | MacBook Pro 16 | 3456×2234 | 254 | Laptop 16" | Sidebar 260px + content |

## Screen Files Per Tier

Each tier contains 10 screen specification files:

| # | Screen | T1-T5 (Phone) | T6-T11 (Tablet) | T12-T15 (Laptop) |
|---|--------|----------------|-----------------|-------------------|
| 1 | home.md | Stack + bottom tabs | 2-column grid | Sidebar + content |
| 2 | auth-login.md | Full screen form | Split layout | Split layout |
| 3 | auth-register.md | 3-step wizard | Split 3-step | Split 3-step |
| 4 | auth-gender.md | Full screen select | Split select | Split select |
| 5 | albums.md | Full screen grid | 2-column split | Sidebar + 60/40 |
| 6 | album-detail.md | Track list scroll | Split detail | Split detail |
| 7 | artists.md | Circular grid | 2-col circular | Sidebar + grid |
| 8 | playlist.md | Stack playlist | Split playlist | Sidebar + playlist |
| 9 | settings.md | Settings list | Split settings | Sidebar + settings |
| 10 | player-bar.md | Mini player bar | Mini player bar | Mini player bar |

## Device Class Layouts

### Phone (T1-T5): Stack Layout
```
┌─────────────────────┐
│     Status Bar      │
├─────────────────────┤
│                     │
│     Content Area    │
│     (scrollable)    │
│                     │
├─────────────────────┤
│   Mini Player Bar   │
├─────────────────────┤
│  Tab Bar (3 items)  │
└─────────────────────┘
```

### Tablet (T6-T11): 2-Column Layout
```
┌──────────────────────────────────┐
│          Status Bar              │
├──────────────┬───────────────────┤
│              │                   │
│   Sidebar    │   Content Area    │
│   (200px)    │   (scrollable)    │
│              │                   │
├──────────────┴───────────────────┤
│        Mini Player Bar           │
└──────────────────────────────────┘
```

### Laptop (T12-T15): Sidebar + Content
```
┌─────────────────────────────────────────────┐
│              Title Bar                      │
├──────────┬──────────────────────────────────┤
│          │                                  │
│ Sidebar  │        Content Area              │
│ (200-    │        (scrollable)              │
│  260px)  │                                  │
│          │                                  │
├──────────┴──────────────────────────────────┤
│            Mini Player Bar                  │
└─────────────────────────────────────────────┘
```

## Reading Protocol

1. **Identify target tier** from device class table
2. **Read ASCII wireframe** for spatial layout
3. **Map components** (C01-C16) to positions
4. **Apply tokens** from design-tokens-master.md
5. **Check responsive notes** for tier-specific overrides

## Token Application

All tiers reference tokens from:
- `../tokens/design-tokens-master.md` — Core design tokens
- `../02-component-inventory.md` — Component specifications (C01-C16)
- `../03-implementation-plan.md` — CSS implementation roadmap

## File Naming Convention

```
T{tier}-{device-class}/{screen}.md
```

Example: `T01-phone-hd/home.md`

---

**Total files:** 151 (1 index + 15 tiers × 10 screens)
**Created:** 2026-09-20
**Author:** Agent 3 — Screen Spec Writer
