---
title: "C13 — Track List Component Prompt"
type: component-prompt
category: ui-design
component_id: "C13"
bem_class: ".track-row"
itcss_layer: "04_Components"
target_file: "css/04_Components/_track-row.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C13 — Track List (.track-row)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.track-row` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_track-row.css` |
| **Kullanım Alanı** | Albümdetay, playlist, queue track listesi |
| **Bileşen Türü** | Track list row with actions |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Row yüksekliği | ~40px | ⚠️ WCAG: 48px minimum |
| Row padding | 0 12px | Yatay padding |
| Number genişliği | 24px | Track numarası |
| Thumb boyutu | 40×40px | Küçük thumbnail |
| Title alan | flex: 1 | Kalan genişlik |
| Duration | 40px | Sağ tarafta |
| Gap | 12px | Elemanlar arası |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Hover bg / Playing indicator | Aktif satır |
| `--color-text` | Başlık rengi | Şarkı adı |
| `--color-text-muted` | Sanatçı/süre rengi | Secondary text |
| `--font-size-sm` | Başlık fontu | `14px` |
| `--font-size-xs` | Sanatçı fontu | `12px` |
| `--space-3` | Padding | `12px` |
| `--transition-fast` | Animasyon | `150ms ease` |
| `--border-subtle` | Separator | Alt çizgi |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ⚠️ 40px → 48px |
| **Touch target** | 44×44px | ⚠️ Row height genişletilmeli |
| **Role** | `listitem` | Her row için |
| **List role** | `list` | Container için |
| **Playing indicator** | `aria-current="track"` | Çalan şarkı |
| **Play button** | `aria-label` | "Şarkıyı çal" |
| **More menu** | `aria-label` | "Daha fazla seçenek" |
| **Keyboard** | Enter/Space | Çalma/tıklama |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C13 — Track List
   ITCSS: 04_Components
   BEM: .track-row
   ============================================ */

/* Track list container */
.track-list {
  display: flex;
  flex-direction: column;
  width: 100%;
}

/* Individual track row */
.track-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 4px 12px; /* Vertical padding for WCAG */
  min-height: 48px;  /* WCAG minimum */
  border-bottom: 1px solid var(--border-subtle);
  cursor: pointer;
  text-decoration: none;
  color: inherit;

  /* Transition */
  transition: background-color var(--transition-fast);
}

.track-row:last-child {
  border-bottom: none;
}

/* Hover */
.track-row:hover {
  background: rgba(255, 255, 255, 0.05);
}

/* Playing state */
.track-row[aria-current="track"],
.track-row.is-playing {
  background: rgba(var(--theme-primary-rgb), 0.08);
}

.track-row.is-playing .track-row__number {
  color: var(--theme-primary);
}

/* Number / Playing indicator */
.track-row__number {
  width: 24px;
  text-align: center;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text-muted);
}

.track-row.is-playing .track-row__number {
  display: none;
}

.track-row__playing-icon {
  display: none;
}

.track-row.is-playing .track-row__playing-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  color: var(--theme-primary);
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Thumbnail */
.track-row__thumb {
  width: 40px;
  height: 40px;
  border-radius: 4px;
  object-fit: cover;
  flex-shrink: 0;
}

/* Title group */
.track-row__title-group {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.track-row__title {
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.track-row.is-playing .track-row__title {
  color: var(--theme-primary);
}

.track-row__artist {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Duration */
.track-row__duration {
  font-family: var(--font-body);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  flex-shrink: 0;
}

/* Actions (on hover) */
.track-row__actions {
  display: flex;
  align-items: center;
  gap: 4px;
  opacity: 0;
  transition: opacity var(--transition-fast);
}

.track-row:hover .track-row__actions {
  opacity: 1;
}

.track-row__action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  background: none;
  color: var(--color-text-muted);
  cursor: pointer;
}

.track-row__action-btn:hover {
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text);
}

/* Focus visible */
.track-row:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: -2px;
}

/* Responsive */
@media (max-width: 768px) {
  .track-row__duration { display: none; }
  .track-row__actions { opacity: 1; }
}
```

## 6. Notlar

- Row yüksekliği 40px, WCAG 48px standardının altındadır → `min-height: 48px` ile düzeltilmeli
- Playing state'de number yerine animasyonlu icon gösterilir
- Actions, sadece hover'da görünür (mobilde her zaman görünür)
- `aria-current="track"` ile çalan şarkı belirtilir
- Keyboard: Enter/Space ile şarkı çalınır
