---
title: CoreMusic — Playlist Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Playlist Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/01-standard-60-40]]
---

# CoreMusic — Playlist Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Playlist Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 1: Standard 60/40 (Tablo + Detail Panel)
**Rota:** `/playlist/:id`

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Şimdi Oynatılıyor                                              [Şarkı Ara 🔍]            │
│                                                                                                  │
│  ┌── TABLO (sol ~65%) ─────────────────────────────────────────────────────────────────────┐   │
│  │ /  | Şarkı Adı          | Albüm Adı          | Sanatçı  | Süre     | Favori Yıldızı    │   │
│  ├─────────────────────────────────────────────────────────────────────────────────────────┤   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★☆☆☆          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | PEMBE VURGU     │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌── SAĞ PANEL (~300px) ──────────────────────────────────────────────────────────────────┐   │
│  │ [圆形 Artist Photo]                                                                      │   │
│  │ Göksel - Sevil Neşelen                                                                 │   │
│  │ Göksel, Hayat Rüya Gibi                                                                │   │
│  │                                                                                         │   │
│  │ [♫][♥][▼][⋯]  (aksiyon ikonları)                                                      │   │
│  │                                                                                         │   │
│  │ ── Önerilen Sanatçılar ──            ── Takip Edilen Sanatçılar ──                    │   │
│  │ [thumb×4 grid]                       [thumb×4 grid]                                     │   │
│  │                                                                                         │   │
│  │ ── Son Öneriler ──                   ── Tüm Sanatçılar ──                             │   │
│  │ [thumb×4 grid]                       [thumb×4 grid]                                     │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Aktif satır: pembe arka plan (opacity)
Tablo başlığı: sabit üstte, sıralanabilir
```

---

## 2. TABLO DETAY

| Sütun | Genişlik | İçerik |
|-------|----------|--------|
| # | ~30px | Sıra numarası |
| Şarkı Adı | ~%35 | Şarkı başlığı + thumb |
| Albüm Adı | ~%25 | Albüm adı |
| Sanatçı | ~%15 | Sanatçı adı |
| Süre | ~60px | 00:00:00 |
| Favori | ~100px | 5 yıldız (C12) |

**Aktif satır:** `background: rgba(255,79,216,0.15)`, `border-left: 3px solid var(--theme-primary)`

---

## 3. SAĞ PANEL

| Bölüm | İçerik |
|-------|--------|
| Üst |圆形 Artist Photo (100×100px) + şarkı adı + albüm |
| Aksiyon | ♫ ♥ ▼ ⋯ ikonları (44×44px hit area) |
| Öneriler | 4× grid × 2 bölüm (thumb 50×50px) |

---

## 4. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (satır) | ❌ ~40px → 48px |
| Touch target (ikon) | ✅ 44px |
| Touch target (yıldız) | ❌ ~20px → 48px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

*Playlist Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
