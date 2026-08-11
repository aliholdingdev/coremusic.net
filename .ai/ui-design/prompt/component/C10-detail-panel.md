---
title: "C10 — Detail Panel Component Prompt"
type: component-prompt
category: ui-design
component_id: "C10"
bem_class: ".detail-panel"
itcss_layer: "03_Layout"
target_file: "css/03_Layout/_detail-panel.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C10 — Detail Panel (.detail-panel)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.detail-panel` |
| **ITCSS Layer** | `03_Layout` |
| **Hedef Dosya** | `css/03_Layout/_detail-panel.css` |
| **Kullanım Alanı** | Albüm/sanatçı detay sayfası |
| **Bileşen Türü** | Split layout panel (art + content) |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Art boyutu | 300×300px | Sol tarafta büyük görsel |
| Panel genişliği | ~30-40% | Sağ tarafta içerik |
| Gap | 24px | Art ile panel arası |
| Border-radius | 12px | Art görseli için |
| Padding | 24px | İçerik padding |
| Min yükseklik | 300px | Panel minimum yükseklik |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--detail-art` | Art boyutu | `300×300px` |
| `--space-4` | Padding/gap | `24px` |
| `--radius-lg` | Art border radius | `12px` |
| `--glass-bg` | Panel arka plan | Glassmorphism |
| `--glass-border` | Panel kenarlık | Glass border |
| `--color-text` | Başlık rengi | Albüm adı |
| `--color-text-muted` | Metadata rengi | Yıl, tür bilgisi |
| `--font-size-xl` | Başlık fontu | `24px` |
| `--font-size-sm` | Metadata fontu | `14px` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Image alt text** | `alt` zorunlu | Albüm adı |
| **Heading hierarchy** | `<h1>` | Albüm adı |
| **Keyboard nav** | Tab ile gezilebilir | Tüm interaktif elemanlar |
| **Focus indicator** | visible | Button/link outline |
| **Responsive** | Stacked on mobile | 768px breakpoint |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C10 — Detail Panel
   ITCSS: 03_Layout
   BEM: .detail-panel
   ============================================ */

.detail-panel {
  display: flex;
  gap: var(--space-4);
  padding: var(--space-4);
  min-height: 300px;
}

/* Art / Cover image */
.detail-panel__art {
  flex-shrink: 0;
  width: 300px;
  height: 300px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.detail-panel__art img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Content panel */
.detail-panel__content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  padding: var(--space-4);
  min-width: 0;
}

/* Title area */
.detail-panel__header {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.detail-panel__title {
  font-family: var(--font-body);
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.2;
}

.detail-panel__meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}

.detail-panel__meta-divider {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: var(--color-text-muted);
}

/* Action buttons */
.detail-panel__actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

/* Track list area */
.detail-panel__tracks {
  flex: 1;
  overflow-y: auto;
}

/* Scrollbar */
.detail-panel__tracks::-webkit-scrollbar {
  width: 6px;
}

.detail-panel__tracks::-webkit-scrollbar-track {
  background: transparent;
}

.detail-panel__tracks::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 3px;
}

/* Responsive — stacked layout */
@media (max-width: 768px) {
  .detail-panel {
    flex-direction: column;
    align-items: center;
  }

  .detail-panel__art {
    width: 200px;
    height: 200px;
  }

  .detail-panel__content {
    align-items: center;
    text-align: center;
  }

  .detail-panel__meta {
    justify-content: center;
  }

  .detail-panel__actions {
    justify-content: center;
  }
}

/* Ultra small */
@media (max-width: 480px) {
  .detail-panel__art {
    width: 160px;
    height: 160px;
  }

  .detail-panel__title {
    font-size: 20px;
  }
}
```

## 6. Notlar

- Detail panel, split layout (sol: art, sağ: content) kullanır
- Mobilde stacked layout'a geçer (column direction)
- `object-fit: cover` ile görsel kırpılır
- Scrollbar, WebKit tarayıcıları için özelleştirilmiş
- Metadata area: year, genre, track count gibi bilgiler
