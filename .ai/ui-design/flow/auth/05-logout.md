---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Logout Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Logout Flow

## 1. Akış Diyagramı (Decision Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Logout Tıkla    │
│ (Menu/Header)   │
└────────┬────────┘
         │
┌────────▼────────┐
│ Onay Modalı     │
│                  │
│ "Çıkış yapmak   │
│  istediğine emin│
│  misin?"        │
│                  │
│ [Hayır] [Evet]  │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│İptal│ │  │Session Temizle│
│Kapat│ │  │COREMUSIC_SESS │
│Modal│ │  │cookie sil     │
└─────┘ │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │JWT Token Sil   │
        │  │localStorage    │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Cache Temizle   │
        │  │APCu/Redis      │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │State Reset     │
        │  │Player durdur   │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Select Gender  │
        │  │Ekranına Yönlendir│
        │  └───────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ HEADER (sağ üst)                                            │
│                                                              │
│  [👤 Kullanıcı Adı] [⚙️] [🚪 Logout]                       │
│                            │                                 │
│                            ▼                                 │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              ONAY MODALI                            │    │
│  │                                                     │    │
│  │  ⚠️ Çıkış yapmak istediğine emin misin?            │    │
│  │                                                     │    │
│  │  Oturumun kapatılacak ve tüm veriler temizlenecek.  │    │
│  │                                                     │    │
│  │  [Hayır, Kalsın]        [Evet, Çıkış Yap]         │    │
│  │                                                     │    │
│  └─────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────┘
       │
       │ "Evet, Çıkış Yap" tıklandı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ LOADING ANİMASYONU                                          │
│                                                              │
│  ┌─────────────────────────────────────────────────────┐    │
│  │                                                     │    │
│  │              ○ Oturum kapatılıyor...                │    │
│  │                                                     │    │
│  └─────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────┘
       │
       │ Temizlik tamamlandı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ SELECT GENDER EKRANI (İlk ekran)                            │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Seni Tanıyalım                | |
| |                   |  | [👩 Kız] [👨 Erkek] [[V] Nötr]   | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
```

## 2A. Temizlik Kontrol Listesi

| # | Temizlik | Konum | Yöntem |
|---|----------|-------|--------|
| 1 | Session cookie | HTTP Cookie | `COREMUSIC_SESS` sil |
| 2 | JWT token | HTTP Header | Authorization header kaldır |
| 3 | localStorage | Browser | `cm_auth`, `cm_theme` sil |
| 4 | sessionStorage | Browser | Tümü sil |
| 5 | APCu cache | Server | Kullanıcı cache'i temizle |
| 6 | Redis cache | Server | Session key sil |
| 7 | Player state | JS | `PlayerController.stop()` |
| 8 | UI state | JS | `ViewModeManager.reset()` |

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Session zaten dolmuş | Direkt yönlendirme (modal gösterme) |
| API hatası | "Çıkış yapılamadı" + tekrar dene |
| Network hatası | Lokal temizlik + yönlendirme |
| Modal açıkken sayfa değişimi | Modal otomatik kapanır |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Logout Butonu | Onay Tipi | Temizlik |
|------|---------------|-----------|----------|
| **Phone** | Swipe down ongird | Bottom sheet onay | Anlık |
| **Tablet** | Menu item | Modal onay | Anlık |
| **Embedded** | Button click | Modal onay | Anlık |
| **Desktop** | Menu item | Modal onay | Anlık |
| **TV** | Remote long-press | Large modal | Anlık |
| **Car** | Voice "çıkış yap" | Sesli onay | Anlık |
| **Watch** | Crown press | Haptic onay | Anlık |

## 4A. API Endpoint

| Endpoint | Method | Auth | Açıklama |
|----------|--------|:----:|----------|
| `/api/auth/logout` | POST | ✅ | Session sonlandır |
| `/api/auth/logout` | DELETE | ✅ | Token sil |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.modal`, `.modal__content` | blok — Onay Modalı "Çıkış yapmak istediğine emin misin?" (bu dosya L29-L35, L76-L84) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.btn`, `.btn--secondary`, `.btn--danger` | blok — [Hayır, Kalsın] / [Evet, Çıkış Yap] (bu dosya L82) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.nav-link` | eleman — header menü öğesi [🚪 Logout] (bu dosya L72); tier tablosunda "Menu item" (L138, L140) | default, hover, active, disabled | 02-component-inventory.md L35 (C01) |
| `.avatar` | blok — header'da [👤 Kullanıcı Adı] (bu dosya L72) | default, with-image, with-initials, online, offline | 02-component-inventory.md L135 (C11) |
| `.toast`, `.toast--error` | blok — "Çıkış yapılamadı" hata mesajı (bu dosya L129) | showing, hiding, success, error, info | 02-component-inventory.md L185 (C16) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Logout Tıkla | Header (sağ üst) | [🚪 Logout] butonuna tıkla |
| 2 | Çıkışı Onayla | Onay Modalı | [Evet, Çıkış Yap] butonuna tıkla |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
