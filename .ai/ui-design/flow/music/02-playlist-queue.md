---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playlist & Queue Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Playlist & Queue Flow

## 1. Akış Diyagramı (Queue Management)

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

## 2. Şarkı Ekleme Akışı

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

## 3. Sıra Değiştirme Akışı

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

## 4. Ekran Akışı

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

## 5. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Playlist dolu (max 500) | "Playlist dolu" uyarısı |
| Şarkı zaten var | "Bu şarkı zaten playlist'te" |
| DB kaydetme hatası | "Kaydetme başarısız" + tekrar dene |
| Sıra değiştirme başarısız | Eski sıraya geri dön |
| Şarkı silinmiş | listeden kaldır |

## 6. Tier-Bazlı Varyasyonlar

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

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
