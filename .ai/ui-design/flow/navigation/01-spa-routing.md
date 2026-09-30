---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — SPA Routing Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# SPA Routing Flow

## 1. Akış Diyagramı (Decision Flow)

### ASCII Flow Diagram

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

## 1A. Route Tablosu

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

## 1B. Animasyonlar

| Geçiş | Süre | Easing |
|-------|------|--------|
| Sayfa girişi | 300ms | ease-out |
| Sayfa çıkışı | 200ms | ease-in |
| Header active | 150ms | ease |
| Footer show/hide | 200ms | ease |

---
## 2. Ekran Akışı

| # | Ekran / Sahne | Kaynak dosya | Geçiş koşulu |
|---|---------------|--------------|--------------|
| 1 | home-dashboard | `screens/T07-embedded/home-dashboard.md` | `/home` (bu dosya L80) |
| 2 | welcome-popup | `screens/T07-embedded/welcome-popup.md` | — |
| 3 | albums | `screens/T07-embedded/albums.md` | `/albums` (bu dosya L81) |
| 4 | album-detail | `screens/T07-embedded/album-detail.md` | — |
| 5 | singer | `screens/T07-embedded/singer.md` | `/artists` (bu dosya L82) |
| 6 | playlist | `screens/T07-embedded/playlist.md` | — |
| 7 | playlist-video | `screens/T07-embedded/playlist-video.md` | — |
| 8 | browse | `screens/T07-embedded/browse.md` | — |
| 9 | browse-clicked | `screens/T07-embedded/browse-clicked.md` | — |
| 10 | wifi-quick | `screens/T07-embedded/wifi-quick.md` | — |
| 11 | wifi-connect-light | `screens/T07-embedded/wifi-connect-light.md` | — |
| 12 | bluetooth-quick | `screens/T07-embedded/bluetooth-quick.md` | — |
| 13 | login | `screens/shared/login.md` | `/auth/login` (bu dosya L85) |
| 14 | register-step1 | `screens/shared/register-step1.md` | `/auth/register` (bu dosya L86) |
| 15 | register-step2 | `screens/shared/register-step2.md` | `/auth/register` (bu dosya L86) |
| 16 | register-step3 | `screens/shared/register-step3.md` | `/auth/register` (bu dosya L86) |
| 17 | select-gender | `screens/shared/select-gender.md` | `/auth/gender` (bu dosya L87) |
| 18 | select-gender-selected | `screens/shared/select-gender-selected.md` | `/auth/gender` (bu dosya L87) |
| 19 | home-dashboard (1920) | `screens/T17-monitor-22fhd/home-dashboard.md` | `/home` (bu dosya L80) |
| 20 | welcome-popup (1920) | `screens/T17-monitor-22fhd/welcome-popup.md` | — *(status: draft)* |
| 21 | Screen Specification Index | `screens/00-ascii-art-index.md` | yukarıdaki 20 satırın kaynağı (§3 L50-L65, §4 L67-L76, §5 L78-L83) |

> **Kaynak:** [[../../screens/00-ascii-art-index]] v6.2.0 — §3 (T07 12 dosya), §4 (shared 6 dosya), §5 (T17 2 dosya) + bu indeks = **21 dosya** (index L27 "20 screen spec", L44 toplam 20). `singer` ↔ `/artists` eşlemesi: index L58 ("Sanatçılar: dairesel kartlar") + bu dosya L82 (Artists Page). Route tablosunda karşılığı olmayan sahne "—" ile işaretlendi (uydurma geçiş yazılmadı). T17 `welcome-popup` `status: draft` + PNG yok (index L83) → frontend kanıtı olarak kullanılamaz.

---

## 3. Hata Senaryoları

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; hata senaryoları QA doğrulamasından sonra 4 sütunlu tablo (Hata · Tetikleyici · Çözüm · Max Retry) olarak doldurulacak.

---

## 4. Tier-Bazlı Varyasyonlar

| Tier | Cihaz | Varyasyon tipi | Örnek davranış | Kaynak |
|------|-------|----------------|----------------|--------|
| Phone (PH-T01–PH-T05) | T01–T05 telefonlar | Alt tab navigasyon | Bottom Tab: [🏠 Ana Sayfa] [📚 Kütüphane] [⚙️ Ayarlar] (bu dosya L29); matrix özellikleri "Bottom tab nav" (L86) | 00-device-matrix.md L71-L86 |
| T07 (EM-T07) | RPi5 7" — 1024×600 | 2 sütun, sidebar yok | Header Nav 4 öğe (bu dosya L24) | 00-device-matrix.md L111 |
| T17 (DM-T17) | 22" FHD Monitor — 1920×1080 | 3 sütun | Header Nav 8 öğe (bu dosya L24-L25) | 00-device-matrix.md L138 |

> **Kaynak:** [[../../00-device-matrix]] §3 (L69-L284). Bu dosyanın frontmatter'ında `tier:` alanı yok (L1-L12); satırlar yalnızca dosya içi kanıtla (L24-L29) eşleştirildi — kanıtsız tier için satır üretilmedi. Route Handler tier'dan bağımsızdır (bu dosya L48-L57).

---

## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.nav-link`, `.nav-link--active` | eleman — Header Nav linkleri (bu dosya L24-L25) ve `#header-nav.active` (L62) | default, hover, active, disabled | 02-component-inventory.md L35 (C01) |
| `.tab`, `.tab-list`, `.tab--active` | eleman — Bottom Tab: [🏠 Ana Sayfa] [📚 Kütüphane] [⚙️ Ayarlar] (bu dosya L29) | default, hover, active, disabled | 02-component-inventory.md L85 (C06) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`). Footer player'ın `#footer-player.show()` durumu (bu dosya L63) DOM/id katmanıdır, BEM sınıfı değildir.

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Ana Sayfa'ya Geç | home-dashboard | Header Nav'da [Ana Sayfa] linkine tıkla [[screens/T07-embedded/home-dashboard]] |
| 2 | Albümler'e Geç | albums | Header Nav'da [Albümler] linkine tıkla [[screens/T07-embedded/albums]] |
| 3 | Sanatçılar'a Geç | singer | Header Nav'da [Sanatçılar] linkine tıkla [[screens/T07-embedded/singer]] |
| 4 | Kütüphane'ye Geç | Kütüphane | Phone Bottom Tab'da [📚 Kütüphane] sekmesine tıkla |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
