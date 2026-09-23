---
title: "CoreMusic — C09 Media Card Prompt"
type: prompt
category: ui-design
component_id: "C09"
bem_class: ".media-card"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C09 — Media Card (.media-card)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.media-card` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_media-card.css` |
| **Usage** | Album, artist, playlist kartları (grid/list) |
| **Type** | Media thumbnail card |

## 2. ASCII Wireframe

```
┌─── MEDIA CARD ─────────────────────┐
│  ┌──────────────────────────────┐  │
│  │                              │  │
│  │    [Album Art 140×140]       │  │
│  │    border-radius: 8px        │  │
│  │                              │  │
│  │         ▶ Play (hover)       │  │
│  │                              │  │
│  └──────────────────────────────┘  │
│  ┌──────────────────────────────┐  │
│  │  Hayat Rüya Gibi             │  │ 14px, 500
│  │  Göksel                      │  │ 12px, muted
│  └──────────────────────────────┘  │
└────────────────────────────────────┘
  140px width, gap: 12px in grid
```

## 3. Tier Sizes

| Tier | Thumb | Width | Grid Cols |
|------|-------|-------|-----------|
| Phone | 120×120px | auto | 3 cols |
| Embedded | 140×140px | 140px | 4 cols |
| Desktop | 160×160px | 160px | auto-fill |
| 4K TV | 200×200px | 200px | auto-fill |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| Default | `.media-card` | Kare thumbnail |
| Circular | `.media-card--circle` | Dairesel (sanatçı) |
| Wide | `.media-card--wide` | 16:9 landscape |

## 5. States

| State | Visual |
|-------|--------|
| Default | Thumb + text |
| Hover | translateY(-4px), shadow-md, play overlay visible |
| Focus-visible | 2px outline + 4px offset |
| Playing | Thumb border: primary, equalizer icon |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Image alt | `alt` zorunlu (albüm adı) |
| Heading | `<h3>` or lower |
| Play button | `aria-label="Şarkıyı çal"` |
| Keyboard | Tab to card, Enter to open |
| Focus | Visible outline on card |

## 7. Code Example

```css
/* C09 — Media Card | ITCSS: 04_Components */
.media-card {
  display: flex;
  flex-direction: column;
  width: 140px;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
  transition: transform var(--transition-fast), box-shadow var(--transition-fast);
}

.media-card__thumb {
  position: relative;
  width: 140px;
  height: 140px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--glass-bg);
}

.media-card__thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 300ms ease; }

.media-card__play {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  background: rgba(0, 0, 0, 0.4); opacity: 0; transition: opacity 200ms ease;
}

.media-card:hover .media-card__play { opacity: 1; }
.media-card:hover .media-card__thumb img { transform: scale(1.05); }
.media-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

.media-card__info { padding: var(--space-3) 0 0 0; }
.media-card__title { font-size: var(--font-size-sm); font-weight: 500; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.media-card__subtitle { font-size: var(--font-size-xs); color: var(--color-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }

.media-card:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: 4px; border-radius: var(--radius-md); }

.media-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }

@media (max-width: 768px) {
  .media-card-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
  .media-card { width: 100%; }
  .media-card__thumb { width: 100%; height: auto; aspect-ratio: 1; }
}
```

```html
<a href="/album/123" class="media-card">
  <div class="media-card__thumb">
    <img src="/cover.jpg" alt="Hayat Rüya Gibi albüm kapağı" loading="lazy">
    <div class="media-card__play">
      <svg class="media-card__play-icon" aria-hidden="true">▶</svg>
    </div>
  </div>
  <div class="media-card__info">
    <div class="media-card__title">Hayat Rüya Gibi</div>
    <div class="media-card__subtitle">Göksel</div>
  </div>
</a>
```

---

*C09 Media Card v2.0.0 — CoreMusic UI Design System*
