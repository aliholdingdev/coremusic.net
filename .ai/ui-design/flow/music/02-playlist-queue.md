---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playlist & Queue Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Playlist & Queue Flow

## 1. Akış Diyagramı (Decision Flow)

### Queue Management

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Playlist Seç    │
│ veya Oluştur    │
└────────┬────────┘
         │
┌────────▼────────┐
│ İşlem Seç       │
└────────┬────────┘
    ┌────┼────────────┐
    │    │            │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Şarkı │ │ │Sırayı   │ │ │Playlist │
│Ekle  │ │ │Değiştir │ │ │Sil      │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Ara   │ │ │Drag-    │ │ │Onay     │
│→Seç  │ │ │Drop     │ │ │"Emin    │
│→Ekle │ │ │Bırak    │ │ │misin?"  │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
    │    │ ┌──▼──────┐ │    │
    │    │ │Sıra     │ │    │
    │    │ │Güncelle │ │    │
    │    │ │DB Kaydet│ │    │
    │    │ └─────────┘ │    │
    │    │              │    │
    └────┴──────────────┴────┘
              │
         ┌────▼────┐
         │Playlist │
         │Güncellendi│
         └─────────┘
```

## 1A. Şarkı Ekleme Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ "+" Butonu Tıkla│
└────────┬────────┘
         │
┌────────▼────────┐
│ Kaynak Seç      │
│ [🔍 Ara]        │
│ [📁 Dosya]      │
│ [📋 Yapıştır]   │
└────────┬────────┘
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼───────┐
│Aramadan│ │Dosyadan  │
│Ekle    │ │Yükle     │
└───┬───┘ └──┬───────┘
    │         │
┌───▼───┐ ┌──▼───────┐
│Sonuçlar│ │Format    │
│→Seç   │ │Kontrol   │
└───┬───┘ └──┬───────┘
    │         │
    └────┬────┘
         │
    ┌────▼────┐
    │Playliste│
    │Ekle     │
    └────┬────┘
         │
    ┌────▼────┐
    │Queue    │
    │Güncelle │
    └─────────┘
```

## 1B. Sıra Değiştirme Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Şarkı Seç       │
│ (Basılı Tut)    │
└────────┬────────┘
         │
┌────────▼────────┐
│ Drag Mode Başla │
│ Opacity: 0.5    │
│ Shadow ekle     │
└────────┬────────┘
         │
┌────────▼────────┐
│ Hedef Pozisyonu │
│ Göster (mavi    │
│ çizgi)          │
└────────┬────────┘
         │
┌────────▼────────┐
│ Bırak (Drop)    │
└────────┬────────┘
         │
┌────────▼────────┐
│ Sıra Güncelle    │
│ DB Kaydet       │
│ Animasyon       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Queue           │
│ Güncellendi     │
└─────────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ PLAYLIST DETAYI                                              │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🎵 Yaz Playlisti                    [🔀] [▶] [⋯]       │ │
│ │ 15 şarkı · 1 saat 23 dakika                           │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ 1. 🎵 Göksel - Sevil Neşelenen     00:05:00  ⋮        │ │
│ │ 2. 🎵 Göksel - Kabahat Sensin      00:05:00  ⋮        │ │
│ │ 3. 🎵 Gangsta - Çubuklar            00:07:19  ⋮        │ │
│ │ 4. 🎵 Keyifli Enstrümantal          00:05:13  ⋮        │ │
│ │ 5. 🎵 Erkin Koray - Fantastik       00:10:00  ⋮        │ │
│ │ ...                                                      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ [+ Şarkı Ekle]                          [Playlisti Sil]│ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Playlist dolu (max 500) | "Playlist dolu" uyarısı |
| Şarkı zaten var | "Bu şarkı zaten playlist'te" |
| DB kaydetme hatası | "Kaydetme başarısız" + tekrar dene |
| Sıra değiştirme başarısız | Eski sıraya geri dön |
| Şarkı silinmiş | listeden kaldır |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Ekleme | Sıra Değiştirme | Silme |
|------|--------|-----------------|-------|
| **Phone** | Swipe + tap | Swipe to reorder | Swipe left |
| **Tablet** | Tap + modal | Drag handles | Tap + confirm |
| **Embedded** | Tap + modal | Drag handles | Tap + confirm |
| **Desktop** | Click + modal | Full drag-drop | Right-click menu |
| **TV** | Remote select | D-pad reorder | Long press |
| **Car** | Voice | Voice reorder | Voice delete |
| **Watch** | Crown | Crown scroll | Crown press |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.btn`, `.btn--primary`, `.btn--danger` | blok — "+" Butonu (bu dosya L68), [+ Şarkı Ekle] / [Playlisti Sil] (L159) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.modal`, `.modal__footer` | blok — silme onayı "Emin misin?" (bu dosya L41-L43); tier tablosu "Tap + modal" (L179-L180) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.dropdown`, `.dropdown__menu` | blok — [⋯] satır menüsü (bu dosya L149) ve Desktop "Right-click menu" (L181) | closed, open, item-hover, item-active | 02-component-inventory.md L165 (C14) |
| `.card`, `.card__title` | blok — playlist detay kartı (bu dosya L148-L160) | default, hover, active, loading, skeleton | 02-component-inventory.md L55 (C03) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Playlist Seç | playlist | listeden bir playlist satırına tıkla [[screens/T07-embedded/playlist]] |
| 2 | Şarkı Ekle | playlist | [+ Şarkı Ekle] butonuna tıkla [[screens/T07-embedded/playlist]] |
| 3 | Kaynak Seç | playlist | [🔍 Ara] kaynağına tıkla [[screens/T07-embedded/playlist]] |
| 4 | Arama Sonucu Seç | playlist | arama sonuçlarından bir şarkı satırı seç [[screens/T07-embedded/playlist]] |
| 5 | Sırayı Değiştir | playlist | şarkı satırını basılı tut [[screens/T07-embedded/playlist]] |
| 6 | Pozisyona Bırak | playlist | mavi çizgi ile gösterilen hedef pozisyona bırak [[screens/T07-embedded/playlist]] |
| 7 | Playlisti Sil | playlist | [Playlisti Sil] butonuna tıkla [[screens/T07-embedded/playlist]] |
| 8 | Silmeyi Onayla | playlist | "Emin misin?" onay modalında Evet'i tıkla [[screens/T07-embedded/playlist]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
