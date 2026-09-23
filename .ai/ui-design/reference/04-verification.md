---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — UI Verification Protocol"
type: protocol
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/04-verification.md"
  source_of_truth: ".ai/ui-design/01-mockup-index.md · .ai/ui-design/02-component-inventory.md"
---

# CoreMusic — UI Verification Protocol

**Zorunlu Bağlantılar:** [[01-mockup-index]] · [[02-component-inventory]] · [[04-accessibility-gaps]]

---

## 1. Amaç

Her frontend görevinden sonra uygulanacak **UI doğrulama protokolüdür**. PNG karşılaştırma, tier bazlı validasyon ve kalite kontrol adımları burada tanımlanır.

---

## 2. Doğrulama Adımları

### Adım 1: PNG Karşılaştırma

```
1. İlgili PNG mockup'ı aç (.ai/.png/)
2. Kod çıktısını tarayıcıda göster
3. Piksel düzeyinde karşılaştır:
   - Header yüksekliği
   - Footer yüksekliği
   - İçerik padding'i
   - Kart boyutları
   - Boşluklar
4. Fark varsa düzelt
```

### Adım 2: Tier Validasyonu

```
1. Hedef tier'ı belirle (phone/embedded/laptop/desktop/4k/tv)
2. Token değerlerini kontrol et:
   --cm-header-h
   --cm-footer-h
   --cm-sidebar-w
   --cm-touch-target
   --cm-font-scale
3. Media query'inin doğru çalıştığını doğrula
4. Cihaz CSS override'ının sadece behavioral olduğunu kontrol et
```

### Adım 3: BEM Kontrolü

```
1. Tüm sınıflar BEM formatında mı?
   .block__element--modifier
2. Namespace korunuyor mu?
   .cm-card, .cm-btn, .cm-input
3. Hardcoded stil var mı?
   ❌ margin: 16px → ✅ margin: var(--cm-space-4)
```

### Adım 4: Token Kullanım Kontrolü

```
1. Hardcoded renk var mı?
   ❌ color: #ffffff → ✅ color: var(--cm-text-primary)
2. Hardcoded boyut var mı?
   ❌ width: 240px → ✅ width: var(--cm-sidebar-w)
3. Hardcoded radius var mı?
   ❌ border-radius: 12px → ✅ border-radius: var(--cm-radius-lg)
```

### Adım 5: Erişilebilirlik Kontrolü

```
1. Touch target ≥ 44px (phone/embedded)
2. Focus visible görünür mü?
3. Contrast oranı ≥ 4.5:1 (text)
4. Screen reader uyumlu mu? (aria-label)
5. prefers-reduced-motion destekleniyor mu?
```

### Adım 6: Cross-Browser Kontrol

```
1. Chrome (latest) ✅
2. Firefox (latest) ✅
3. Safari (latest) ✅
4. Edge (latest) ✅
5. Samsung Internet (mobile) ✅
```

---

## 3. Doğrulama Matrisi

| Tier | PNG Referans | Token Doğrulama | Touch Target | Contrast |
|------|-------------|-----------------|--------------|----------|
| Phone | T01-T04 | ≤767px tokens | 48px ✅ | 4.5:1 ✅ |
| Embedded | T07-T08 | 1024×600 tokens | 48px ✅ | 4.5:1 ✅ |
| Laptop | T09-T10 | 1366-1920px tokens | 32px ✅ | 4.5:1 ✅ |
| Desktop | T11-T12 | 2560-3840px tokens | 28px ✅ | 4.5:1 ✅ |
| 4K | T12 | ≥3840px tokens | 24px ✅ | 4.5:1 ✅ |
| TV | T15-T18 | TV UA tokens | 80-120px ✅ | 4.5:1 ✅ |

---

## 4. Red Flags (Düzeltme Gerekli)

| Flag | Açıklama | Aksiyon |
|------|----------|---------|
| Hardcoded value | CSS'de hardcoded pixel/renk | Token'a çevir |
| Missing BEM | ClassName BEM formatında değil | BEM'e çevir |
| Missing token | Token tanımlı ama kullanılmamış | Token'ı ekle |
| Low contrast | Text contrast < 4.5:1 | Renk düzelt |
| Small touch | Touch target < 44px | Büyüt |
| No focus ring | Focus visible görünmüyor | Focus ring ekle |
| No reduced motion | prefers-reduced-motion destek yok | Ekle |

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Verification Steps | 6 |
| Tier Matrix | 6 rows |
| Red Flags | 7 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
