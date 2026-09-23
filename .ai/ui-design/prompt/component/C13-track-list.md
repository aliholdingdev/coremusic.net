---
title: "CoreMusic — C13 Track List Prompt"
type: prompt
category: ui-design
component_id: "C13"
bem_class: ".track-row"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C13 — Track List (.track-row)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.track-row` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_track-row.css` |
| **Usage** | Albüm detay, playlist, queue track listesi |
| **Type** | Track list row with actions |

## 2. ASCII Wireframe

```
┌─── TRACK ROW (default) ──────────────────────────────────────┐
│  1   [thumb 40×40] Sevil Neşelen          05:00   [...]     │
│  num   album art   title + artist         dur   actions     │
│  24px  40px        flex:1                 40px              │
│  min-height: 48px (WCAG)                                    │
└──────────────────────────────────────────────────────────────┘

┌─── TRACK ROW (playing) ──────────────────────────────────────┐
│  ♪   [thumb 40×40] Sevil Neşelen ←PEMBE  05:00   [...]     │
│  anim   album art   title primary color   dur   actions     │
└──────────────────────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Row Height | Font Title | Font Artist | Thumb |
|------------|------------|-------------|-------|
| Phone: 52px | 14px | 12px | 40px |
| Embedded: 48px | 14px | 12px | 40px |
| Desktop: 48px | 14px | 12px | 44px |
| 4K TV: 56px | 16px | 14px | 48px |

## 4. States

| State | Visual |
|-------|--------|
| Default | border-bottom: subtle |
| Hover | bg: white×0.05 |
| Playing | bg: primary×0.08, title: primary, anim icon |
| Focus-visible | 2px outline, inset |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="listitem"` on row, `list` on container |
| Playing | `aria-current="track"` |
| Play button | `aria-label="Şarkıyı çal"` |
| More menu | `aria-label="Daha fazla seçenek"` |
| Keyboard | Enter/Space to play |
| Touch target | min 44×44px |

## 6. Code Example

```css
/* C13 — Track List | ITCSS: 04_Components */
.track-list { display: flex; flex-direction: column; width: 100%; }

.track-row {
  display: flex; align-items: center; gap: 12px;
  padding: 4px 12px; min-height: 48px;
  border-bottom: 1px solid var(--border-subtle);
  cursor: pointer; text-decoration: none; color: inherit;
  transition: background-color var(--transition-fast);
}

.track-row:last-child { border-bottom: none; }
.track-row:hover { background: rgba(255, 255, 255, 0.05); }
.track-row[aria-current="track"],
.track-row.is-playing { background: rgba(var(--theme-primary-rgb), 0.08); }

.track-row__number { width: 24px; text-align: center; font-size: 14px; font-weight: 500; color: var(--color-text-muted); }
.track-row.is-playing .track-row__number { display: none; }

.track-row__playing-icon { display: none; }
.track-row.is-playing .track-row__playing-icon {
  display: flex; align-items: center; justify-content: center;
  width: 24px; height: 24px; color: var(--theme-primary);
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

.track-row__thumb { width: 40px; height: 40px; border-radius: 4px; object-fit: cover; flex-shrink: 0; }

.track-row__title-group { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.track-row__title { font-size: var(--font-size-sm); font-weight: 500; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.track-row.is-playing .track-row__title { color: var(--theme-primary); }
.track-row__artist { font-size: var(--font-size-xs); color: var(--color-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.track-row__duration { font-size: var(--font-size-xs); color: var(--color-text-muted); flex-shrink: 0; }

.track-row__actions { display: flex; align-items: center; gap: 4px; opacity: 0; transition: opacity var(--transition-fast); }
.track-row:hover .track-row__actions { opacity: 1; }
.track-row__action-btn { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: none; border-radius: 6px; background: none; color: var(--color-text-muted); cursor: pointer; }
.track-row__action-btn:hover { background: rgba(255, 255, 255, 0.08); color: var(--color-text); }

.track-row:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: -2px; }

@media (max-width: 768px) {
  .track-row__duration { display: none; }
  .track-row__actions { opacity: 1; }
}
```

```html
<div class="track-list" role="list" aria-label="Şarkı listesi">
  <div class="track-row is-playing" role="listitem" aria-current="track" tabindex="0">
    <span class="track-row__number">1</span>
    <svg class="track-row__playing-icon" aria-hidden="true">♪</svg>
    <img class="track-row__thumb" src="/cover.jpg" alt="">
    <div class="track-row__title-group">
      <span class="track-row__title">Sevil Neşelen</span>
      <span class="track-row__artist">Göksel</span>
    </div>
    <span class="track-row__duration">05:00</span>
    <div class="track-row__actions">
      <button class="track-row__action-btn" aria-label="Daha fazla seçenek">⋯</button>
    </div>
  </div>
</div>
```

---

*C13 Track List v2.0.0 — CoreMusic UI Design System*
