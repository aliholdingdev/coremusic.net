---
title: "Video Playback - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S08"
resolution: "1024x600"
layout_pattern: "Fullscreen"
components: [C01, C04, C15]
png: "home-1024/Linux  1024 - Playlist Page - Video Played.png"
full_spec: "D-player/video-playback.md"
---

# Video Playback

## Layout Wireframe

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
│  ┌─ Mini Player (sol alt köşe, ~250×80px) ────────────────────────────────────────────────┐   │
│  │ [○ 50×50圆形] Göksel - Sevil Neşelen                                                   │   │
│  │               Hayat Rüya Gibi                                                          │   │
│  │               Göksel                                                                    │   │
│  │               00:00:00 / 00:05:00                                                      │   │
│  │               [seek bar — full-width, pembe]                                            │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│  Sol alt köşe: mp3 ★★★★★ (dosya formatı + yıldız)                                            │
│                                                                                                  │
│ ARKA PLAN: Tam kaplama sanatçı fotoğrafı / video karesi                                       │
│ Header: YOK — sadece geri oku                                                                  │
│ Footer: YOK — mini player ile değiştirildi                                                    │
│ Sağ panel: Yarı saydam, glass efekti                                                          │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Video alanı | sol | ~717px (%70) | — |
| Şarkı listesi | sağ | ~307px (%30) | — |
| Geri ok | sol üst | 44×44px | — |
| Mini player | sol alt köşe | 250×80px | — |
| Liste satır yüksekliği | — | ~40px | — |
| Liste thumb | — | 30×30px | — |
| Mini player thumb | — | 50×50px daire | — |
| Touch target | — | ≥48px | `--touch-min` |
| Glass blur | — | blur(8px) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.video-area__back` | sol üst köşe | 44×44px, daire |
| C04 | `.mini-player` | sol alt köşe | 250×80px, border-radius: 12px |
| C15 | `.video-track-row` | sağ panel satırları | 100%×40px min, thumb: 30×30px |
| C15 | `.video-tracks` | sağ panel | 307px, bg: rgba(0,0,0,0.3) + blur(8px) |

## Video Area

| Özellik | Değer |
|---------|-------|
| Genişlik | ~717px (%70) |
| Yükseklik | 600px (tam ekran) |
| Arka plan | `background-size: cover; background-position: center` |
| Overlay | Yok (tam kaplama) |

## Mini Player

| Özellik | Değer |
|---------|-------|
| Boyut | ~250×80px |
| Pozisyon | Sol alt köşe |
| Background | `rgba(0,0,0,0.5)` + `backdrop-filter: blur(10px)` |
| Border-radius | 12px |
| Thumb | 50×50px, daire |
| Başlık | 12px, 600 |
| Seek bar | Full-width, 3px, pembe |

## CSS Hints

```css
.video-layout {
  display: grid;
  grid-template-columns: 1fr 307px;
  height: 100vh;
}
.video-area {
  background-size: cover;
  background-position: center;
}
.video-tracks {
  background: rgba(0,0,0,0.3);
  backdrop-filter: blur(8px);
}
.mini-player {
  position: absolute;
  bottom: 16px; left: 16px;
  width: 250px;
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(10px);
  border-radius: 12px;
}
```

---

*QR Video Playback v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
