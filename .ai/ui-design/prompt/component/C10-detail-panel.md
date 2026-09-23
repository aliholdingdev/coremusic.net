---
title: "CoreMusic — C10 Detail Panel Prompt"
type: prompt
category: ui-design
component_id: "C10"
bem_class: ".detail-panel"
itcss_layer: "03_Layout"
date: 2026-09-20
version: 2.0.0
status: active
---

# C10 — Detail Panel (.detail-panel)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.detail-panel` |
| **ITCSS Layer** | `03_Layout` |
| **Target File** | `css/03_Layout/_detail-panel.css` |
| **Usage** | Albüm/sanatçı detay sayfası, split layout sağ taraf |
| **Type** | Split layout panel (art + content) |

## 2. ASCII Wireframe

```
┌─── DETAIL PANEL (394px) ─────────────────────┐
│                                               │
│  ┌────────────┐                               │
│  │ Album Art  │  Hayat Rüya Gibi              │
│  │ 300×300px  │  Göksel                       │
│  │ radius:12px│                               │
│  └────────────┘  2024 · Arabesk · 12 şarkı   │
│                                               │
│  [Hemen Çal] (C04, primary)                   │
│  [Karışık Çal] (C05, secondary)               │
│                                               │
│  ── Metadata ──                               │
│  Kalite: 24 Bit / 48 kHz                      │
│  Boyut: 2 GB | İndirme: 2                     │
│  Süre: 00:30:00                               │
│                                               │
│  ── Track List (scrollable) ──                │
│  1. Sevil Neşelen        05:00                │
│  2. Kabahat              04:30                │
│  ...                                          │
└───────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Art Size | Panel Width | Font |
|------|----------|-------------|------|
| Phone | 200×200px | 100% (stacked) | 14px |
| Embedded | 300×300px | 394px (40%) | 16px |
| Desktop | 320×320px | 420px (40%) | 16px |
| 4K TV | 400×400px | 500px (40%) | 18px |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| Album | `.detail-panel--album` | Kare art, track list |
| Artist | `.detail-panel--artist` | Dairesel art, bio |
| Playlist | `.detail-panel--playlist` | Playlist cover |

## 5. States

| State | Visual |
|-------|--------|
| Default | Art + metadata + actions |
| Loading | Skeleton placeholder |
| Error | Fallback art + error message |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Image alt | `alt` zorunlu |
| Heading | `<h1>` for title |
| Actions | Button links with labels |
| Scroll | `role="region"` for track list |
| Keyboard | Tab through all interactive elements |

## 7. Code Example

```css
/* C10 — Detail Panel | ITCSS: 03_Layout */
.detail-panel {
  display: flex;
  gap: var(--space-4);
  padding: var(--space-4);
  min-height: 300px;
}

.detail-panel__art {
  flex-shrink: 0;
  width: 300px;
  height: 300px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.detail-panel__art img { width: 100%; height: 100%; object-fit: cover; }
.detail-panel__art--circle { border-radius: 50%; }

.detail-panel__content { flex: 1; display: flex; flex-direction: column; gap: var(--space-4); padding: var(--space-4); min-width: 0; }
.detail-panel__title { font-size: var(--font-size-xl); font-weight: 700; color: var(--color-text); line-height: 1.2; }
.detail-panel__meta { display: flex; align-items: center; gap: 8px; font-size: var(--font-size-sm); color: var(--color-text-muted); }
.detail-panel__meta-divider { width: 4px; height: 4px; border-radius: 50%; background: var(--color-text-muted); }
.detail-panel__actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.detail-panel__tracks { flex: 1; overflow-y: auto; }

@media (max-width: 768px) {
  .detail-panel { flex-direction: column; align-items: center; }
  .detail-panel__art { width: 200px; height: 200px; }
  .detail-panel__content { align-items: center; text-align: center; }
  .detail-panel__meta { justify-content: center; }
  .detail-panel__actions { justify-content: center; }
}
```

```html
<div class="detail-panel">
  <div class="detail-panel__art">
    <img src="/cover.jpg" alt="Hayat Rüya Gibi albüm kapağı">
  </div>
  <div class="detail-panel__content">
    <h1 class="detail-panel__title">Hayat Rüya Gibi</h1>
    <div class="detail-panel__meta">
      <span>Göksel</span>
      <span class="detail-panel__meta-divider"></span>
      <span>2024</span>
      <span class="detail-panel__meta-divider"></span>
      <span>12 şarkı</span>
    </div>
    <div class="detail-panel__actions">
      <button class="btn-primary btn-primary--sm">Hemen Çal</button>
      <button class="btn-secondary btn-secondary--sm">Karışık Çal</button>
    </div>
    <div class="detail-panel__tracks" role="region" aria-label="Şarkı listesi">
      <!-- C13 track rows -->
    </div>
  </div>
</div>
```

---

*C10 Detail Panel v2.0.0 — CoreMusic UI Design System*
