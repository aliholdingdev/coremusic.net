---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — SPA Routing Flow"
type: flow
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# SPA Routing Flow

## ASCII Flow Diagram

```
┌──────────────────────────────────────────────────────────────┐
│ KULLANICI TIKLAMA                                           │
│                                                              │
│ Header Nav: [Ana Sayfa] [Keşfet] [Albümler] [Sanatçılar]   │
│             [Göz At] [Geçmiş] [Ayarlar] [Hakkımızda]       │
│                                                              │
│ veya                                                         │
│                                                              │
│ Bottom Tab: [🏠 Ana Sayfa] [📚 Kütüphane] [⚙️ Ayarlar]     │
│                                                              │
│ veya                                                         │
│                                                              │
│ URL Değişikliği: /home → /albums → /artists                 │
└──────────────────────────────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────────────────────────────┐
│ SPA ROUTER                                                   │
│                                                              │
│ ┌─── Guard Pipeline ──────────────────────────────────────┐ │
│ │ 1. Auth Guard    → Giriş yapıldı mı?                   │ │
│ │ 2. Role Guard    → Yetki var mı?                       │ │
│ │ 3. Cache Guard   → Önbellekte var mı?                  │ │
│ │ 4. Transition    → Sayfa geçiş animasyonu              │ │
│ └──────────────────────────────────────────────────────────┘ │
│       │                                                       │
│       ▼                                                       │
│ ┌─── Route Handler ───────────────────────────────────────┐  │
│ │                                                         │  │
│ │  /home        → HomePage.render()                       │  │
│ │  /albums      → AlbumsPage.render()                     │  │
│ │  /artists     → ArtistsPage.render()                    │  │
│ │  /player      → PlayerPage.render()                     │  │
│ │  /settings    → SettingsPage.render()                   │  │
│ │  /auth/login  → AuthPage.render('login')                │  │
│ │                                                         │  │
│ └─────────────────────────────────────────────────────────┘  │
│       │                                                       │
│       ▼                                                       │
│ ┌─── DOM Patch ──────────────────────────────────────────┐   │
│ │ #content-area.innerHTML = newPage HTML                 │   │
│ │ #header-nav.active = currentPage                       │   │
│ │ #footer-player.show() = !isAuthPage                    │   │
│ │ window.history.pushState({}, '', url)                  │   │
│ └────────────────────────────────────────────────────────┘   │
│       │                                                       │
│       ▼                                                       │
│ ┌─── Scroll Restore ─────────────────────────────────────┐   │
│ │ scrollY = sessionStorage.getItem('scroll-' + route)    │   │
│ │ window.scrollTo(0, scrollY || 0)                       │   │
│ └────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────┘
```

## Route Tablosu

| Route | Sayfa | Auth | Footer |
|-------|-------|:----:|:------:|
| `/` | Home Dashboard | ✅ | ✅ |
| `/home` | Home Dashboard | ✅ | ✅ |
| `/albums` | Albums Page | ✅ | ✅ |
| `/artists` | Artists Page | ✅ | ✅ |
| `/player` | Now Playing | ✅ | ❌ |
| `/settings` | Settings | ✅ | ✅ |
| `/auth/login` | Login | ❌ | ❌ |
| `/auth/register` | Register | ❌ | ❌ |
| `/auth/gender` | Select Gender | ❌ | ❌ |

## Animasyonlar

| Geçiş | Süre | Easing |
|-------|------|--------|
| Sayfa girişi | 300ms | ease-out |
| Sayfa çıkışı | 200ms | ease-in |
| Header active | 150ms | ease |
| Footer show/hide | 200ms | ease |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
