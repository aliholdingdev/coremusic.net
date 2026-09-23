---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Now Playing Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/now-playing.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Playlist Page.png"
---

# CoreMusic — Now Playing (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ ← Geri   Şimdi Oynatılıyor                             🔍 Şarkı Ara                               │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── LEFT PANEL (60%, w:614) ──────────────────┐  ┌─── RIGHT PANEL (40%, w:410) ────────────┐   │  │
│     │  │ / │ Şarkı Adı    │ Albüm Adı  │ Sanatçı │   │  │  🎤 Sanatçı Foto (120×120, circle)      │   │  │
│     │  │───┼──────────────┼────────────┼─────────┤   │  │  ┌────────────────────────┐              │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  └────────────────────────┘              │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │                                          │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  Göksel - Sevil Neşelenen                │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  Göksel · Hayat Rüya Gibi                │   │  │
│     │  │───┼──────────────┼────────────┼─────────┤   │  │                                          │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  [❤️] [🔀] [⬇️] [⋯]                    │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │                                          │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  Önerilen Sanatçılar   Takip Et        │   │  │
│     │  │───┼──────────────┼────────────┼─────────┤   │  │  ┌──┐┌──┐┌──┐┌──┐                      │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  │🎤││🎤││🎤││🎤│                      │   │  │
│     │  │ 🎵│ Göksel - S.N │ Hayat R.G. │ Göksel  │   │  │  └──┘└──┘└──┘└──┘                      │   │  │
│     │  └──────────────────────────────────────────────┘  └──────────────────────────────────────────┘   │  │
│     │                                                                                                    │  │
│     └────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:510 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────┐  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.now-playing` | Ana sayfa container |
| `.now-playing__tracklist` | Sol panel: Şarkı listesi (tablo) |
| `.now-playing__sidebar` | Sağ panel: Sanatçı bilgisi |
| `.track-table` | Şarkı tablosu |
| `.track-table__header` | Tablo başlığı (/ , Şarkı, Albüm, Sanatçı, Süre, ★) |
| `.track-table__row` | Tablo satırı |
| `.track-table__row--active` | Çalınan şarkı (pembe highlight) |
| `.track-table__cover` | Küçük kapak (40×40) |
| `.track-table__title` | Şarkı adı |
| `.track-table__album` | Albüm adı |
| `.track-table__artist` | Sanatçı |
| `.track-table__duration` | Süre |
| `.track-table__rating` | Yıldız puanı |
| `.sidebar__photo` | Sanatçı fotoğrafı (circle) |
| `.sidebar__song-info` | Çalınan şarkı bilgisi |
| `.sidebar__actions` | Aksiyon butonları |
| `.sidebar__suggested` | Önerilen sanatçılar |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-active` | rgba(255,79,216,0.2) |
| `--cm-star-filled` | #ffd700 |
| `--cm-radius-md` | 12px |
| `--cm-spacing-sm` | 8px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Playlist Page.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
