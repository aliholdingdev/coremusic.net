---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Logout Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
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

## 3. Temizlik Kontrol Listesi

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

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Session zaten dolmuş | Direkt yönlendirme (modal gösterme) |
| API hatası | "Çıkış yapılamadı" + tekrar dene |
| Network hatası | Lokal temizlik + yönlendirme |
| Modal açıkken sayfa değişimi | Modal otomatik kapanır |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Logout Butonu | Onay Tipi | Temizlik |
|------|---------------|-----------|----------|
| **Phone** | Swipe down ongird | Bottom sheet onay | Anlık |
| **Tablet** | Menu item | Modal onay | Anlık |
| **Embedded** | Button click | Modal onay | Anlık |
| **Desktop** | Menu item | Modal onay | Anlık |
| **TV** | Remote long-press | Large modal | Anlık |
| **Car** | Voice "çıkış yap" | Sesli onay | Anlık |
| **Watch** | Crown press | Haptic onay | Anlık |

## 6. API Endpoint

| Endpoint | Method | Auth | Açıklama |
|----------|--------|:----:|----------|
| `/api/auth/logout` | POST | ✅ | Session sonlandır |
| `/api/auth/logout` | DELETE | ✅ | Token sil |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
