---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic UI Design — Master Flow Index"
type: flow-index
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/flow/00-flow-index.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/ui-design/01-mockup-index.md"
---

# CoreMusic UI Design — Master Flow Index

**Zorunlu Bağlantılar:** [[../01-mockup-index]] · [[../02-component-inventory]] · [[../03-implementation-plan]] · [[../05-responsive-architecture]]

---

## 1. Amaç

CoreMusic UI tasarımında tüm kullanıcı akışlarının (flow) merkezi indeksidir. 17 akış, 4 kategoride organize edilmiştir. Her akış; **akış diyagramı** (decision flow), **state machine**, **hata senaryoları** ve **tier-bazlı varyasyonları** içerir.

---

## 2. Flow Kategorileri

| # | Kategori | Flow Sayısı | Dosya Yolu | Durum |
|---|----------|:-----------:|------------|:-----:|
| 1 | **Auth** | 5 | `flow/auth/` | ✅ |
| 2 | **Music** | 5 | `flow/music/` | ✅ |
| 3 | **Settings** | 4 | `flow/settings/` | ✅ |
| 4 | **Navigation** | 3 | `flow/navigation/` | ✅ |
| | **TOPLAM** | **17** | | |

---

## 3. Auth Flows (5)

| # | Flow | Dosya | Tanım | Flow Diagram | State Machine |
|---|------|-------|-------|:---:|:---:|
| 1 | Login | `auth/01-login.md` | Kimlik doğrulama, oturum başlatma | ✅ | — |
| 2 | Register | `auth/02-register.md` | 3 adımlı kayıt süreci | ✅ | — |
| 3 | Forgot Password | `auth/03-forgot-password.md` | Şifre sıfırlama | ✅ | — |
| 4 | Select Gender | `auth/04-select-gender.md` | Cinsiyet seçimi (İLK ADIM) | ✅ | — |
| 5 | Logout | `auth/05-logout.md` | Oturum kapatma | ✅ | — |

### Auth Flow Sırası

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│Select Gender│────▶│   Login     │────▶│ Ana Sayfa   │
└──────┬──────┘     └──────┬──────┘     └─────────────┘
       │                   │
       │              ┌────▼─────┐
       │              │ Register │
       │              └────┬─────┘
       │                   │
       │              ┌────▼──────────┐
       │              │Forgot Password│
       │              └────┬──────────┘
       │                   │
       └───────────────────┘
              (Logout)
```

---

## 4. Music Flows (5)

| # | Flow | Dosya | Tanım | Flow Diagram | State Machine |
|---|------|-------|-------|:---:|:---:|
| 1 | Playback | `music/01-playback.md` | Müzik oynatma, kontroller | ✅ | ✅ |
| 2 | Playlist/Queue | `music/02-playlist-queue.md` | Çalma listesi yönetimi | ✅ | — |
| 3 | Album Browse | `music/03-album-browse.md` | Albüm keşfetme | ✅ | — |
| 4 | Artist Browse | `music/04-artist-browse.md` | Sanatçı keşfetme | ✅ | — |
| 5 | Search | `music/05-search.md` | Arama ve sonuç | ✅ | — |

### Music Playback State Machine

```
         ┌──────────┐
    ┌────│ STOPPED  │────┐
    │    └────┬─────┘    │
    │ Play    │    Stop  │
    │    ┌────▼─────┐    │
    │    │ PLAYING  │    │
    │    └────┬─────┘    │
    │ Pause   │    Stop  │
    │    ┌────▼─────┐    │
    └────│ PAUSED   │────┘
         └──────────┘
```

---

## 5. Settings Flows (4)

| # | Flow | Dosya | Tanım | Flow Diagram | State Machine |
|---|------|-------|-------|:---:|:---:|
| 1 | WiFi Connect | `settings/01-wifi-connect.md` | WiFi ağına bağlanma | ✅ | ✅ |
| 2 | Bluetooth Connect | `settings/02-bluetooth-connect.md` | Bluetooth eşleştirme | ✅ | ✅ |
| 3 | Equalizer | `settings/03-equalizer.md` | EQ ayarları | ✅ | — |
| 4 | General | `settings/04-general.md` | Genel ayarlar | ✅ | — |

### WiFi Connection State Machine

```
┌──────────────┐   Scan   ┌──────────────┐  Select  ┌──────────┐
│ Disconnected │────────▶│  Available   │────────▶│ Password │
└──────┬───────┘         └──────────────┘         └────┬─────┘
       ▲                                                │
       │ Disconnect                               Connect│
       │                                                │
┌──────┴───────┐                               ┌───────▼──────┐
│ Disconnected │◀──────────────────────────────│  Connected   │
└──────────────┘                               └──────────────┘
```

---

## 6. Navigation Flows (3)

| # | Flow | Dosya | Tanım | Flow Diagram | State Machine |
|---|------|-------|-------|:---:|:---:|
| 1 | SPA Routing | `navigation/01-spa-routing.md` | Client-side rota yönetimi | ✅ | — |
| 2 | Header Nav | `navigation/02-header-nav.md` | Header navigasyonu | ✅ | — |
| 3 | Footer Player | `navigation/03-footer-player.md` | Footer oynatıcı | ✅ | — |

### SPA Routing Flow

```
┌──────────────┐
│ Route Match  │
└──────┬───────┘
  Match─┤─No Match─▶ [404 Handler]
  │
  ▼
[Guard Pipeline] ──Fail──▶ [Redirect]
  │
  ▼
[Load View] ──Error──▶ [Retry / Fallback]
  │
  ▼
[DOM Patch]
  │
  ▼
[Scroll Restore]
```

---

## 7. Tier-Bazlı Flow Değişiklikleri

| Tier | Auth | Music | Settings | Navigation |
|------|------|-------|----------|------------|
| **Phone** (≤767px) | Full-screen, bottom sheet | Mini player, swipe | Full-page modal | Bottom tab nav |
| **Tablet** (768-1024px) | Split-panel | Sidebar queue | Split-panel | Sidebar nav |
| **Embedded** (1024×600) | Split 42/58 | Bottom player bar | Modal overlay | Top nav, 4 items |
| **Desktop** (≥1920px) | Split-panel | Sidebar queue | Side-panel | Top nav, 8 items |
| **TV** (≥3840px) | Simplified, remote | Large controls, focus | Full-screen modal | D-pad focus ring |
| **Car** | Voice-first, large | Steering wheel | Simplified list | Voice + simplified |
| **Watch** | Micro UI, haptic | Wrist gestures | Minimal toggle | Crown + gestures |

---

## 8. Akış Diyagramı Stili

Tüm flow doc'larda **ASCII Box-Arrow** stili kullanılır:

```
Kullanılan karakterler:
  Yatay çizgi:    ─
  Dikey çizgi:    │
  Köşe:           ┌ ┐ └ ┘
  Kavşak:         ┬ ┤ ├ ┴
  Ok:             ▼
  Karar dalı:     ─┤─ Evet / Hayır
```

---

## 9. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| `00-flow-index.md` | `auth/04-select-gender.md` | İlk akış |
| `00-flow-index.md` | `02-component-inventory.md` | Bileşen referansları |
| `00-flow-index.md` | `05-responsive-architecture.md` | Tier tanımları |
| `00-flow-index.md` | `01-mockup-index.md` | PNG mockup referansları |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Total Flows | 17 |
| Auth Flows | 5 |
| Music Flows | 5 |
| Settings Flows | 4 |
| Navigation Flows | 3 |
| Flow Diagrams | 17/17 ✅ |
| State Machines | 3 (Playback, WiFi, Bluetooth) |
| Tier Coverage | 7 (Phone, Tablet, Embedded, Desktop, TV, Car, Watch) |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
