---
title: CoreMusic — Video Playback Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Playlist Page - Video Played.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/03-fullscreen]]
  - [[D-player/playlist]]
---

# CoreMusic — Video Playback Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Playlist Page - Video Played.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 3: Fullscreen — Header/Footer YOK
**Rota:** `/playlist/:id` (video modu)

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 3: Fullscreen — Header/Footer YOK                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Göksel - Sevil Neşelen                                                                       │
│  (geri ok — sol üst köşe, 44×44px)                                                             │
│                                                                                                  │
│  ┌── VİDEO ALANI (sol ~70%, ~717px) ──────────┐  ┌── ŞARKI LİSTESİ (sağ ~30%, ~307px) ──┐   │
│  │                                               │  │ Şarkı Adı                     Süre    │   │
│  │    [Tam kaplama video/image]                   │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │    background-size: cover                      │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │    background-position: center                 │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  │                                               │  │ [thumb] Göksel - Sevil Neş.   00:00  │   │
│  └───────────────────────────────────────────────┘  └────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌─ Mini Player (sol alt köşe, ~300×100px) ───────────────────────────────────────────────┐   │
│  │ [50×50 thumb] Göksel - Sevil Neşelen                                                   │   │
│  │                Hayat Rüya Gibi                                                          │   │
│  │                Göksel                                                                    │   │
│  │                00:00:00 / 00:05:00                                                      │   │
│  │                [seek bar — full-width]                                                   │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│  Sol alt köşe: m3p3 ★★★★★ (dosya formatı + yıldız)                                           │
│                                                                                                  │
│ ARKA PLAN: Tam kaplama sanatçı fotoğrafı / video karesi                                       │
│ Header: YOK — sadece geri oku                                                                  │
│ Footer: YOK — mini player ile değiştirildi                                                    │
│ Sağ panel: Yarı saydam, glass efekti                                                          │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. LAYOUT DETAYLARI

### 2.1 — Genel

| Özellik | Değer |
|---------|-------|
| Header | YOK |
| Footer | YOK |
| Geri ok | Sol üst köşe, 44×44px |
| Video alanı | ~70% genişlik |
| Şarkı listesi | ~30% genişlik, sağ taraf |
| Mini player | Sol alt köşe |

### 2.2 — Video Alanı

| Özellik | Değer |
|---------|-------|
| Genişlik | ~717px (%70) |
| Yükseklik | 600px (tam ekran) |
| Arka plan | `background-size: cover; background-position: center` |
| Overlay | Yok (tam kaplama) |

### 2.3 — Şarkı Listesi (Sağ Panel)

| Özellik | Değer |
|---------|-------|
| Genişlik | ~307px (%30) |
| Background | `rgba(0,0,0,0.3)` + `backdrop-filter: blur(8px)` |
| Padding | 8px |
| Satır yüksekliği | ~40px |
| Thumb | 30×30px |
| Başlık | 11px, 500 |
| Süre | 10px, 400 |

### 2.4 — Mini Player

| Özellik | Değer |
|---------|-------|
| Boyut | ~300×100px |
| Pozisyon | Sol alt köşe |
| Background | `rgba(0,0,0,0.5)` + `backdrop-filter: blur(10px)` |
| Border-radius | 12px |
| Thumb | 50×50px |
| Başlık | 12px, 600 |
| Alt metin | 10px, 400, muted |
| Seek bar | Full-width, 3px |

---

## 3. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (geri ok) | ✅ 44×44px |
| Touch target (liste satırı) | ⚠️ ~40px |
| Touch target (mini player) | ✅ ~300×100px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (Escape ile çıkış) |

---

*Video Playback Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
