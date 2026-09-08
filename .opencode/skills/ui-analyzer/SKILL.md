---
title: "CoreMusic — UI Analiz Motoru"
type: skill-instruction
version: 2.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - PNG Mockup Analysis
  - Font Detection
  - Color Palette Extraction
  - Responsive Breakpoint Check
  - Live Page Analysis
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/ui-design/00-mockup-index.md"
    - ".ai/ui-design/01-component-inventory.md"
    - ".ai/ui-design/tokens/design-tokens-master.md"
    - ".ai/ui-design/screens/00-ascii-art-index.md"
  architecture:
    - ".ai/ADR/"
    - ".ai/architecture/l3-presentation/"
  templates:
    - ".ai/.templates/frontend/js-template.md"
    - ".ai/.templates/frontend/css-template.md"
  agents:
    - ".ai/.agents/AGENTS.md"
    - ".ai/.agents/ui-designer.md"
  skills:
    - ".opencode/skills/ui-code-generator/SKILL.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "analysis method change"
      - "output format change"
triggers:
  - "ui analiz"
  - "mockup oku"
  - "font bul"
  - "renk paleti"
  - "sayfa analizi"
  - "design analiz"
  - "ui element"
  - "layout analiz"
  - "png oku"
  - "görsel analiz"
  - "screenshot analiz"
changelog:
  - version: 2.0
    date: 2026-09-04
    changes:
      - Standardized YAML frontmatter
      - Added .ai/ui-design SSOT cross-reference (00-mockup-index, 01-component-inventory)
---

# UI ANALYZER v2.0.0 — ARAYÜZ ANALİZ MOTORU

## 1. KİMLİK

Sen CoreMusic **UI Analysis** motorusun. `.ai/.png/` altındaki 18 PNG mockup'ı, `.ai/ui-design/00-mockup-index.md` indeksini ve `00-ascii-art-index.md` wireframe'lerini analiz ederek detaylı UI analizi yaparsın.

**Kural:** Sadece oku ve analiz et, kod üretme. Kod üretimi için `ui-code-generator` kullan.

## 2. AKTİVASYON

```
ui analiz · mockup oku · font bul · renk paleti
sayfa analiz · design analiz · ui element · layout analiz
png oku · görsel analiz · screenshot analiz
```

**Kullanılmama durumları:**
- Kod üretimi isteği → ui-code-generator
- Erişilebilirlik denetimi → accessibility
- Performans analizi → performance

## 3. ÇALIŞMA AKIŞI (6 Adım)

```
ADIM 1: Girdi türünü belirle ve UI Design SSOT'u yükle
  → .ai/ui-design/00-mockup-index.md'den ilgili ekranın PNG dosyasını bul
  → PNG Mockup: .ai/.png/home-1024/ veya shared-1024/ altındaki görseli oku
  → ASCII Art: .ai/ui-design/screens/ altındaki wireframe ile karşılaştır
  → Font dosyası: .ttf, .otf, .woff, .woff2 oku
  → Canlı sayfa: Chrome DevTools ile analiz et
  → Mevcut kod: CSS/JS dosyalarını oku

ADIM 2: Görsel analiz yap (PNG & C01-C16 eşleşmesi)
  → C01–C16 kanonik bileşenleri tespit et (.ai/ui-design/01-component-inventory.md)
  → Kanonik ölçüleri doğrula (Header 60px y:0-60, İçerik 450px y:60-510, Footer 90px y:510-600)
  → Renk paleti çıkar (primary, secondary, accent: #ff4fd8, neutral)
  → Tipografi analizi (font: Arima, logo: Bickham Script Two, boyut, satır yüksekliği)
  → Grid yapısını tespit et (sütun sayısı, gutter, margin, Split 42/58)
  → Boşluk hiyerarşisini çıkar (4px, 8px, 16px, 24px, 32px, 48px, 64px)
  → Cam efektleri: backdrop-filter blur(20px) saturate(180%), rgba(255,255,255,0.08)
  → Design token eşleşmesini kontrol et (a-layout-tokens.css, a-colors-token.css)

ADIM 3: Font analizi yap
  → Font ailesi adını tespit et
  → Weight aralığını belirle (100-900)
  → Glyph kapsamını kontrol et (Latin, Cyrillic, CJK, vb.)
  → OpenType özelliklerini kontrol et (ligatures, tabular figures, vb.)
  → Web font formatını belirle (WOFF2 tercih, WOFF fallback)

ADIM 4: Responsive breakpoint analizi yap
  → Temel Kanonik Taban: 1024×600 (RPi5 7" dokunmatik)
  → Mobil: ≤767px
  → Masaüstü: 1025px - 1920px (FHD)
  → 4K TV: ≥3840px

ADIM 5: WCAG 2.2 AA erişilebilirlik analizi yap
  → Kontrast oranı kontrolü (min 4.5:1)
  → Dokunmatik hedef boyutu (min 48×48px)
  → Focus göstergesi (min 3px)

ADIM 6: Analiz raporu oluştur
  → Tespit edilen tasarım token'ları
  → C01–C16 bileşen eşleşmeleri
  → Ölçüm sapmaları ve öneriler
```

## 4. İLGİLİ SKILLER

- **ui-code-generator** — Üretim kalitesinde UI kod üretimi
- **accessibility** — WCAG denetimi
- **hallucination-control** — Doğrulama protokolleri
