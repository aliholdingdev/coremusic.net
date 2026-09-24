---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Layout Pattern: Spatial AR/VR"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# CoreMusic — Layout Pattern: Spatial AR/VR

## 1. Viewport Aralığı

| Property | Value |
|----------|-------|
| **Viewport** | 1920×1080 (per eye) — 3840×3840 (stereoscopic) |
| **Cihazlar** | Meta Quest 3, Apple Vision Pro, Varjo, Pimax |
| **Input** | Hand tracking, eye tracking, controller, voice |
| **FOV** | 90°-120° horizontal |
| **Refresh** | 90Hz-120Hz (stereo rendering) |
| **IPD** | 58-72mm (interpupillary distance) |

## 2. ASCII Wireframe

```
┌─── Spatial Canvas (3D Environment) ─────────────────────────┐
│                                                              │
│    Z-FORWARD (-Z into screen)                               │
│         ↑                                                    │
│         │    ┌─────────────────────────┐                    │
│         │    │  SPATIAL PANEL          │                    │
│         │    │  (floating in 3D)       │                    │
│         │    │                         │                    │
│    ─────┼────│  ┌───────────────────┐  │────→ X-RIGHT      │
│         │    │  │  Album Art 3D    │  │                    │
│         │    │  │  (diegetic)      │  │                    │
│         │    │  └───────────────────┘  │                    │
│         │    │                         │                    │
│         │    │  Transport (hand grab)  │                    │
│         │    │  ▶ ■ ◄◄ ►► ◉           │                    │
│         │    │                         │                    │
│         │    │  EQ Visualizer (3D)     │                    │
│         │    │  ╔═══╗╔═══╗╔═══╗       │                    │
│         │    │  ║███║║███║║███║       │                    │
│         │    │  ╚═══╝╚═══╝╚═══╝       │                    │
│         │    └─────────────────────────┘                    │
│         │                                                    │
│         ↓ Y-DOWN                                            │
│                                                              │
│    ┌── GAZE RETICLE ──┐    ┌── HAND CURSOR ──┐            │
│    │    ◉ (center)     │    │    ✋ (pinch)    │            │
│    └───────────────────┘    └─────────────────┘            │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## 3. Grid Tanımı

```css
/* Spatial Layout — 3D positioned, not 2D grid */
.layout--spatial {
  /* No traditional CSS grid — use 3D transforms */
  perspective: 1200px;
  transform-style: preserve-3d;
}

/* Spatial panel — floating in 3D space */
.spatial-panel {
  position: absolute;
  /* Positioned via JS with 3D coordinates */
  transform: translate3d(var(--x), var(--y), var(--z))
             rotateY(var(--rot-y, 0deg))
             rotateX(var(--rot-x, 0deg));
  width: 600px; /* Virtual size at 1m distance */
  background: rgba(20, 20, 30, 0.85);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 24px;
}

/* Diegetic UI — inside the 3D world */
.spatial-panel--diegetic {
  background: transparent;
  border: none;
  backdrop-filter: none;
}

/* Non-diegetic — overlay on screen */
.spatial-panel--overlay {
  position: fixed;
  z-index: 1000;
  pointer-events: none;
}

/* Eye-tracked gaze reticle */
.spatial-gaze-reticle {
  position: fixed;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid var(--theme-primary);
  pointer-events: none;
  transition: transform 50ms ease-out;
}
```

## 4. Token Değişiklikleri

| Token | Base Value | Spatial Value | Reason |
|-------|-----------|---------------|--------|
| `--touch-min` | 44px | **40px** | Hand tracking precision |
| `--font-size-title` | 16px | **24px** | 3D depth readability |
| `--font-size-body` | 14px | **18px** | 3D depth readability |
| `--panel-width` | — | **600px** | Virtual panel at 1m |
| `--panel-bg` | var(--surface) | **rgba(20,20,30,0.85)** | Glassmorphism in 3D |
| `--panel-blur` | — | **20px** | Spatial depth effect |
| `--panel-border` | 1px solid var(--border) | **1px solid rgba(255,255,255,0.1)** | Subtle 3D edge |
| `--depth-z` | — | **-1000px** | Default panel depth |

## 5. BEM Sınıfları

```
.layout--spatial
.layout--spatial__canvas          → 3D transform container
.layout--spatial__panel           → Floating 3D panel
.layout--spatial__panel--diegetic → Inside-world UI
.layout--spatial__panel--overlay  → HUD overlay
.layout--spatial__gaze-reticle    → Eye tracking cursor
.layout--spatial__hand-cursor     → Hand tracking cursor
.layout--spatial__eq-visualizer   → 3D EQ bars
.layout--spatial__album-art-3d    → 3D album art plane
.layout--spatial__transport-3d    → 3D transport controls
.layout--spatial__room-env        → Room environment

/* Interaction states */
.is-gazing                        → Eye gaze hovering
.is-grabbing                      → Hand pinch grabbed
.is-pointing                      → Finger pointing
```

## 6. Responsive Davranış

| Durum | Davranış |
|-------|----------|
| 90Hz rendering | Stereo çift göz, 1920×1080 per eye |
| Hand tracking | Pinch to select, grab to move |
| Eye tracking | Gaze reticle, foveated rendering |
| 3DOF (rotation only) | Panel follows head rotation |
| 6DOF (full movement) | Panel positioned in room |
| Controller | Laser pointer selection |
| Voice | "Hey CoreMusic, play..." |
| Multi-panel | Up to 3 panels simultaneously |
| Passthrough AR | Panels overlaid on real world |

## 7. Spatial Interaction Patterns

| Gesture | Action |
|---------|--------|
| Pinch (thumb + index) | Select / Play |
| Grab (full hand) | Move panel |
| Point (index finger) | Hover / Focus |
| Swipe (air) | Next / Previous track |
| Spread (two hands) | Resize panel |
| Push (palm forward) | Dismiss panel |
| Pull (fingers close) | Summon panel |
| Voice | "Play", "Pause", "Next", "Volume up" |

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Stereoscopic | ✅ Per-eye rendering |
| Hand Tracking | ✅ Pinch, grab, point |
| Eye Tracking | ✅ Gaze reticle + foveated |
| 6DOF | ✅ Full spatial positioning |
| Diegetic UI | ✅ In-world panels |
| Glassmorphism | ✅ Backdrop blur in 3D |
| BEM Classes | 11 spatial-specific |
| ASCII Wireframe | ✅ |
