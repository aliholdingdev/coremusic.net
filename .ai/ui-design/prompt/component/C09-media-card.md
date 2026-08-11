---
title: "C09 — Media Card Component Prompt"
type: component-prompt
category: ui-design
component_id: "C09"
bem_class: ".media-card"
itcss_layer: "04_Components"
target_file: "css/04_Components/_media-card.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C09 — Media Card (.media-card)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.media-card` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_media-card.css` |
| **Kullanım Alanı** | Album, artist, playlist kartları (grid/list) |
| **Bileşen Türü** | Media thumbnail card |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Thumbnail | 140×140px | Kare görsel |
| Toplam genişlik | ~140px | Thumbnail genişliği |
| Toplam yükseklik | ~180px | Thumbnail + text area |
| Border-radius | 8px | `--radius-md` |
| Text area yüksekliği | ~40px | Title + subtitle |
| Gap (grid) | 12px | Kartlar arası |
| Padding | 0 | Thumbnail edge-to-edge |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--card-thumb` | Thumbnail boyutu | `140×140px` |
| `--radius-md` | Border radius | `border-radius: 8px` |
| `--color-text` | Başlık rengi | Primary text |
| `--color-text-muted` | Alt başlık rengi | Secondary text |
| `--font-size-sm` | Başlık fontu | `14px` |
| `--font-size-xs` | Alt başlık fontu | `12px` |
| `--space-3` | İçerik padding | `12px` |
| `--transition-fast` | Animasyon | `150ms ease` |
| `--shadow-md` | Hover gölgesi | `box-shadow` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Touch target** | ≥44×44px | ✅ Kart boyutu yeterli |
| **Image alt text** | `alt` zorunlu | Albüm/sanatçı adı |
| **Heading hierarchy** | `<h3>` veya lower | Semantic heading |
| **Focus indicator** | visible | Kart outline |
| **Keyboard nav** | Tab ile gezilebilir | Grid navigasyonu |
| **Play button** | `aria-label` | "Şarkıyı çal" |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C09 — Media Card
   ITCSS: 04_Components
   BEM: .media-card
   ============================================ */

.media-card {
  display: flex;
  flex-direction: column;
  width: 140px;
  cursor: pointer;
  text-decoration: none;
  color: inherit;

  /* Transition */
  transition: transform var(--transition-fast),
              box-shadow var(--transition-fast);
}

/* Thumbnail container */
.media-card__thumb {
  position: relative;
  width: 140px;
  height: 140px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--glass-bg);
}

.media-card__thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 300ms ease;
}

/* Play overlay */
.media-card__play {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  opacity: 0;
  transition: opacity 200ms ease;
}

.media-card__play-icon {
  width: 36px;
  height: 36px;
  color: var(--color-white);
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.media-card:hover .media-card__play,
.media-card:focus-within .media-card__play {
  opacity: 1;
}

.media-card:hover .media-card__thumb img {
  transform: scale(1.05);
}

/* Text area */
.media-card__info {
  padding: var(--space-3) 0 0 0;
}

.media-card__title {
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text);
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.media-card__subtitle {
  font-family: var(--font-body);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 2px;
}

/* Focus visible */
.media-card:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: 4px;
  border-radius: var(--radius-md);
}

/* Hover */
.media-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

/* Responsive grid */
.media-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 12px;
}

@media (max-width: 768px) {
  .media-card-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
  }
  .media-card { width: 100%; }
  .media-card__thumb { width: 100%; height: auto; aspect-ratio: 1; }
}
```

## 6. Notlar

- Kart, `<a>` veya `<button>` olabilir ( semantics'e göre)
- Play overlay, sadece hover/focus'ta görünür
- `loading="lazy"` attribute'u thumbnail img'ye eklenmelidir
- Grid responsive: mobilde 3 sütun, desktop'da auto-fill
- Keyboard'da Tab ile her karta erişilebilir olmalı
