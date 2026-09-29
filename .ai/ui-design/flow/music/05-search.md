---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Search Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Search Flow

## 1. Akış Diyagramı (Decision Flow)

### Search & Filter

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

## 1A. Autocomplete Akışı

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

## 2. Ekran Akışı

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

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sonuç yok | "Sonuç bulunamadı" + öneriler |
| Network hatası | "Bağlantı yok" + retry |
| API hatası | "Arama başarısız" + tekrar dene |
| Çok fazla sonuç | Sayfalama (20/sayfa) |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Arama Tipi | Sonuçlar | Filtre |
|------|------------|----------|--------|
| **Phone** | Full-screen search, voice | Scroll list | Bottom sheet |
| **Tablet** | Top bar search | Split list | Sidebar |
| **Embedded** | Top bar search | Split list | Modal |
| **Desktop** | Sidebar search panel | Tabbed results | Sidebar |
| **TV** | Large input, remote | Large cards | Modal |
| **Car** | Voice-first | Simplified list | Voice |
| **Watch** | Crown input | Micro list | Crown |

## 4A. API Endpoint

| Endpoint | Method | Parametre | Açıklama |
|----------|--------|-----------|----------|
| `/api/search` | GET | `q`, `type`, `limit` | Tam arama |
| `/api/search/autocomplete` | GET | `q`, `limit` | Autocomplete |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.input`, `.input__label`, `.input__hint` | eleman — "Arama Inputu" (bu dosya L26) ve ekran [🔍 ____] alanı (L138) | default, focus, error, success, disabled | 02-component-inventory.md L75 (C05) |
| `.dropdown`, `.dropdown__menu` | blok — autocomplete önerileri (bu dosya L31-L33, L116-L122) | closed, open, item-hover, item-active | 02-component-inventory.md L165 (C14) |
| `.tab`, `.tab-list` | eleman — Desktop tier "Tabbed results" (bu dosya L186) | default, hover, active, disabled | 02-component-inventory.md L85 (C06) |
| `.modal` | blok — Embedded/TV tier filtre "Modal" (bu dosya L185, L187) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.card` | blok — sonuç kutuları (Şarkı/Albüm/Sanatçı/Playlist, bu dosya L143-L165) | default, hover, active, loading, skeleton | 02-component-inventory.md L55 (C03) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; numaralı, tek-eylem adım listesi akış doğrulamasından sonra doldurulacak.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
