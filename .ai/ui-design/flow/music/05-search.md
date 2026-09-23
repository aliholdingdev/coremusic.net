---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Search Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Search Flow

## 1. Akış Diyagramı (Search & Filter)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Arama Inputu    │
│ ≥3 harf yaz     │
└────────┬────────┘
         │
┌────────▼────────┐
│ Autocomplete    │
│ Önerileri Göster│
│ (300ms debounce)│
└────────┬────────┘
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼───────┐
│Seç    │ │Aramaya   │
│(Öneri)│ │Devam Et  │
└───┬───┘ └──┬───────┘
    │         │
    │    ┌────▼────┐
    │    │Enter    │
    │    │Tıkla    │
    │    └────┬────┘
    │         │
    └────┬────┘
         │
┌────────▼────────┐
│ Arama Çalıştır  │
│ API: /api/search│
└────────┬────────┘
         │
┌────────▼────────┐
│ Sonuçlar Gelir  │
│ Şarkı/Albüm/    │
│ Sanatçı/Playlist│
└────────┬────────┘
         │
┌────────▼────────┐
│ Filtrele        │
│ (Opsiyonel)     │
└────────┬────────┘
    ┌────┼────┐
    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Tür   │││Sanatçı│││Yıl   │
│Filtre│││Filtre │││Filtre│
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
┌────────▼────────┐
│ Sonuç Seç       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Oynat / Ekle /  │
│ Detay Gör       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Footer Player   │
│ Güncellendi     │
└─────────────────┘
```

## 2. Autocomplete Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Input Değişikliği│
└────────┬────────┘
         │
┌────────▼────────┐
│ Harf Sayısı     │
│ ≥3 mü?          │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Bekle│ │  │300ms Debounce  │
└─────┘ │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │API İsteği      │
        │  │/api/search/    │
        │  │autocomplete    │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Önerileri       │
        │  │Göster          │
        │  │┌──────────────┐│
        │  ││ Göksel       ││
        │  ││ Göksel - Sev ││
        │  ││ Gangsta      ││
        │  │└──────────────┘│
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Seç → Tamamla  │
        │  │veya Devam Et  │
        │  └───────────────┘
```

## 3. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Arama Sayfası                                      │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🔍 [________________________] [X]                       │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ SONUÇLAR (24)                                          │ │
│ │                                                         │ │
│ │ 🎵 ŞARKILAR (10)                                       │ │
│ │ ┌─────────────────────────────────────────────────────┐│ │
│ │ │ 🎵 Göksel - Sevil Neşelenen    00:05:00  [▶]       ││ │
│ │ │ 🎵 Göksel - Kabahat Sensin     00:05:00  [▶]       ││ │
│ │ │ 🎵 Gangsta - Çubuklar           00:07:19  [▶]       ││ │
│ │ └─────────────────────────────────────────────────────┘│ │
│ │                                                         │ │
│ │ 💿 ALBÜMLER (5)                                        │ │
│ │ ┌─────────────────────────────────────────────────────┐│ │
│ │ │ 💿 Hayat Rüya Gibi - Göksel    2024                ││ │
│ │ │ 💿 Fantastik Dünyalar - Erkin  1973                ││ │
│ │ └─────────────────────────────────────────────────────┘│ │
│ │                                                         │ │
│ │ 🎤 SANATÇILAR (4)                                     │ │
│ │ ┌─────────────────────────────────────────────────────┐│ │
│ │ │ 🎤 Göksel · 125K takipçi                           ││ │
│ │ │ 🎤 Gangsta · 67K takipçi                           ││ │
│ │ └─────────────────────────────────────────────────────┘│ │
│ │                                                         │ │
│ │ 📋 PLAYLIST'LER (5)                                    │ │
│ │ ┌─────────────────────────────────────────────────────┐│ │
│ │ │ 📋 Yaz Playlisti · 15 şarkı                        ││ │
│ │ │ 📋 Enstrümantal · 23 şarkı                         ││ │
│ │ └─────────────────────────────────────────────────────┘│ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sonuç yok | "Sonuç bulunamadı" + öneriler |
| Network hatası | "Bağlantı yok" + retry |
| API hatası | "Arama başarısız" + tekrar dene |
| Çok fazla sonuç | Sayfalama (20/sayfa) |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Arama Tipi | Sonuçlar | Filtre |
|------|------------|----------|--------|
| **Phone** | Full-screen search, voice | Scroll list | Bottom sheet |
| **Tablet** | Top bar search | Split list | Sidebar |
| **Embedded** | Top bar search | Split list | Modal |
| **Desktop** | Sidebar search panel | Tabbed results | Sidebar |
| **TV** | Large input, remote | Large cards | Modal |
| **Car** | Voice-first | Simplified list | Voice |
| **Watch** | Crown input | Micro list | Crown |

## 6. API Endpoint

| Endpoint | Method | Parametre | Açıklama |
|----------|--------|-----------|----------|
| `/api/search` | GET | `q`, `type`, `limit` | Tam arama |
| `/api/search/autocomplete` | GET | `q`, `limit` | Autocomplete |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
