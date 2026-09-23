---
title: "CoreMusic — UI Workbench"
type: skill-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - PNG Mockup Analysis
  - Responsive Design Generation
  - WCAG Accessibility Compliance
  - ITCSS 9-Layer Architecture
  - Vanilla JS Code Production
  - Component Generation (C01-C16)
triggers:
  - "html css js"
  - "responsive design"
  - "accessibility"
  - "ui code"
  - "bileşen oluştur"
  - "component"
  - "frontend"
  - "css grid"
  - "flexbox"
  - "wcag"
  - "itcss"
  - "design to code"
  - "mockup to code"
  - "ui analiz"
  - "png oku"
  - "görsel analiz"
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/brain.md"
    - ".ai/ui-design/00-mockup-index.md"
    - ".ai/ui-design/01-component-inventory.md"
    - ".ai/ui-design/tokens/design-tokens-master.md"
    - ".ai/ui-design/screens/00-ascii-art-index.md"
changelog:
  - version: 1.0
    date: 2026-09-20
    changes:
      - Birleştirilmiş: ui-analyzer + ui-code-generator
      - Mockup analizi + kod üretimi tek skill'de
---

# UI WORKBENCH — CoreMusic

## 1. Kimlik

Bu skill, CoreMusic UI'ını **analiz eder ve üretim kalitesinde kod üretir.**

İki modu var:
- **Analiz modu:** PNG mockup oku, C01-C16 eşleştir, token çıkar
- **Üretim modu:** HTML/CSS/JS/PHP kodu üret

**Kural:** Mockup ve C01-C16 envanterini doğrulamadan kod yazma.

---

## 2. Zorunlu Kaynaklar (Guardrail #11)

**Kod yazmadan ÖNCE bu dosyaları oku:**

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | `.ai/ui-design/00-mockup-index.md` | Hangi PNG mockup'lar mevcut? |
| 2 | `.ai/ui-design/01-component-inventory.md` | C01-C16 BEM sınıfları, ölçümler |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` | Renk, boşluk, tipografi token'ları |
| 4 | `.ai/ui-design/screens/00-ascii-art-index.md` | Piksel düzeyinde ASCII layout |
| 5 | `.ai/ui-design/responsive-device-mode.md` | Cihaz bazlı CSS kuralları |

**Referans sırası (çelişki durumunda):** PNG > ASCII art > Inventory > Tokens

---

## 3. Kanonik Referans

| Özellik | Değer |
|---------|-------|
| Temel boyut | 1024×600 (RPi5 7" dokunmatik) |
| Header | h:60px (y:0-60) |
| İçerik | h:450px (y:60-510) |
| Footer | h:90px (y:510-600) |
| Split | 42% / 58% |

### Responsive Breakpoints

| Breakpoint | Düzen |
|------------|-------|
| ≤767px | Mobil — tek sütun, kompakt |
| 1024×600 | Kanonik — mockup birebir |
| 1025-1920px | Desktop — 3 sütun |
| ≥3840px | 4K TV — uzak mesafe |

---

## 4. Çalışma Akışı

### Analiz Modu

```
[1] PNG mockup'ı oku (.ai/.png/)
[2] C01-C16 bileşenlerini eşleştir
[3] Renk paleti çıkar (primary, secondary, accent, neutral)
[4] Tipografi analizi (font, weight, boyut)
[5] Grid yapısını tespit et (sütun, gutter, split)
[6] Token eşleşmesini kontrol et
[7] WCAG 2.2 AA kontrolü
[8] Analiz raporu oluştur
```

### Üretim Modu

```
[1] Analiz modunu çalıştır (zorunlu)
[2] WCAG 2.2 AA kontrolü
  - Kontrast ≥4.5:1 (metin), ≥3:1 (büyük metin)
  - Touch target ≥48×48px
  - ARIA labelleri
  - Klavye navigasyonu
[3] ITCSS katmanını seç (Settings → Generic → Objects → Components → Utilities)
[4] HTML oluştur (semantik, ARIA, data-*)
[5] CSS yaz (ITCSS sırası, mobile-first, BEM, token kullanımı)
[6] JS yaz (Vanilla ES6+, var yasak, AbortController zorunlu, innerHTML yasak)
[7] PHP yaz (gerekirse: strict_types, PDO, CSP nonce, CSRF token)
```

---

## 5. CSS Kuralları

| Kural | Detay |
|-------|-------|
| Mimari | ITCSS 9 katman + CSS @layer |
| Naming | BEM (.block__element--modifier) |
| Token | a-layout-tokens.css, a-colors-token.css |
| Media query | Mobile-first, 4 breakpoint |
| Magic number | YASAK — değerler token'dan gelmeli |
| `var()` | Tüm boyutlar CSS custom property |

### Yasak CSS

```
[X] Hardcoded height: 90px → height: var(--footer-h)
[X] Hardcoded width: 280px → width: var(--sidebar-w)
[X] Ayrı HTML dosyaları (home-1024.html vb.)
[X] PHP'de margin/padding/width kodlamak
```

---

## 6. JavaScript Kuralları

| Kural | Detay |
|-------|-------|
| Dil | Vanilla ES6+ (framework YASAK) |
| Değişken | `var` YASAK — `const`/`let` |
| Fetch | AbortController zorunlu |
| DOM | innerHTML YASAK — DOMParser + TrustedTypes |
| Event | Delegation (parent element üzerinden) |
| Module | ES modules import/export |

---

## 7. PHP Kuralları

| Kural | Detay |
|-------|-------|
| Types | `declare(strict_types=1)` |
| DB | PDO prepared statements (ORM yasak) |
| CSP | Her script için nonce |
| CSRF | Her form için `csrf_token` |

---

## 8. WCAG 2.2 AA

| Kriter | Minimum |
|--------|---------|
| Kontrast (metin) | ≥4.5:1 |
| Kontrast (büyük metin) | ≥3:1 |
| Touch target | ≥48×48px |
| Focus outline | ≥3px |
| ARIA | Tüm interaktif elemanlarda |
| Klavye | Tüm işlevler erişilebilir |

---

## 9. Yasaklar

| Yasaklı | Neden |
|---------|-------|
| Mockup okumadan kod yazma | Guardrail #11 |
| C01-C16'ya aykırı BEM sınıfı | envanter uyumsuzluğu |
| 1024×600 header/footer yüksekliğini bozma | kanonik referans |
| innerHTML kullanımı | XSS riski |
| var kullanımı | ES6+ zorunlu |
| Framework kullanımı | ADR-001 |
| Magic numbers | Token'dan gelmeli |
| Touch target <48px | WCAG ihlali |

---

## 10. Quick Reference

| İhtiyaç | Bölüm |
|---------|-------|
| Hangi dosyaları okumalıyım? | §2 |
| Kanonik boyutlar nedir? | §3 |
| CSS nasıl yazılır? | §5 |
| JS nasıl yazılır? | §6 |
| WCAG kontrolü? | §8 |

---

*UI Workbench v1.0 — CoreMusic*
*Authority: Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
