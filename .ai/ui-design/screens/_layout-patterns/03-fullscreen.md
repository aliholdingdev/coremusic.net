---
title: CoreMusic — Fullscreen Layout Pattern
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
---

# CoreMusic — Fullscreen Layout Pattern

## Kullanım

Video Playback (Playlist - Video Played)

## ASCII Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ ← Geri ok (44×44px)                                                         │
│                                                                              │
│  ┌── VİDEO (%70) ──────────────────┐  ┌── LİSTE (%30) ──────────────────┐│
│  │                                   │  │ Şarkı Adı        Süre            ││
│  │  [Tam kaplama video/image]        │  │ [thumb] şarkı     00:00          ││
│  │  background-size: cover           │  │ [thumb] şarkı     00:00          ││
│  │                                   │  │ [thumb] şarkı     00:00          ││
│  │                                   │  │ (glass panel, scroll)            ││
│  └───────────────────────────────────┘  └──────────────────────────────────┘│
│                                                                              │
│  ┌─ Mini Player (sol alt) ──────────────────────────────────────────────┐  │
│  │ [thumb] Şarkı Adı · Albüm · Sanatçı · Süre · Seek bar              │  │
│  └──────────────────────────────────────────────────────────────────────┘  │
│                                                                              │
│ m3p3 ★★★★★                                                                 │
└──────────────────────────────────────────────────────────────────────────────┘

Header: YOK (sadece geri oku)
Footer: YOK (mini player ile değiştirildi)
Arka plan: Tam kaplama sanatçı fotoğrafı / video
```

## Kurallar

1. Header/Footer YOK
2. Video alanı tam kaplama
3. Sağ panel yarı saydam (glass)
4. Mini player sol alt köşede
5. Geri ok: sol üst, 44×44px

---

*Fullscreen Layout v2.0.0 — CoreMusic UI Design System*
