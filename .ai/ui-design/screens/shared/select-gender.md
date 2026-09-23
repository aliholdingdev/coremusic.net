---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Select Gender Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/shared/select-gender.md"
  source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Select Gender.png"
---

# CoreMusic — Select Gender Screen (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                              x:1024    │
│                                                                                                            │
│ ┌─── LEFT SIDE (60%, w:614) ──────────────┐  ┌─── RIGHT SIDE (40%, w:410) ────────────────────────────┐  │
│ │                                          │  │                                                          │  │
│ │  🏔️ Manzara arka planı                  │  │  👤 Kullanıcı ikonu (48×48, dairesel)                  │  │
│ │  (dağ, çiçekler, gün batımı)            │  │                                                          │  │
│ │                                          │  │  Seni Tanıyalım                                         │  │
│ │  ┌─── Brand Area (ortada) ───────────┐  │  │  Müzik deneyimini sana özel hale getirelim             │  │
│ │  │                                   │  │  │                                                          │  │
│ │  │    ✨ Core Music ✨               │  │  │  ┌─── Gender Option (Kız) ─────────────────────────┐  │  │
│ │  │                                   │  │  │  │ 👩 Kız                                   ▶      │  │  │
│ │  │    Seni Tanıyalım                 │  │  │  │    Theme pembe, öneriler…                         │  │  │
│ │  │    Müzik deneyimini sana          │  │  │  └──────────────────────────────────────────────────┘  │  │
│ │  │    özel hale getirelim            │  │  │                                                          │  │
│ │  │                                   │  │  │  ┌─── Gender Option (Erkek) ───────────────────────┐  │  │
│ │  └───────────────────────────────────┘  │  │  │ 👨 Erkek                                ▶      │  │  │
│ │                                          │  │  │    Theme mavi, öneriler…                         │  │  │
│ │                                          │  │  └──────────────────────────────────────────────────┘  │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  ┌─── Gender Option (Belirtmek İstemiyorum) ──────┐  │  │
│ │                                          │  │  │ 🔀 Cinsiyetimi belirtmek istemiyorum   ▶      │  │  │
│ │                                          │  │  │    Theme nötr, öneriler…                       │  │  │
│ │                                          │  │  └──────────────────────────────────────────────────┘  │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  ┌────────────────────────────────────────────────┐     │  │
│ │                                          │  │  │ ▶ Devam Et (gradient pembe)                   │     │  │
│ │                                          │  │  └────────────────────────────────────────────────┘     │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │              Hayatın ritmini sen de yaşama…              │  │
│ │                                          │  │              ♪ Müziğinle parti! ♪                       │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  Devam ederek [Gizlilik Politikamızı] kabul etmiş      │  │
│ │                                          │  │  olursunuz.                                             │  │
│ └──────────────────────────────────────────┘  └──────────────────────────────────────────────────────────┘  │
│                                                                                                            │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.gender-page` | Ana sayfa container | full screen, flex row |
| `.gender-page__brand` | Sol taraf (manzara + logo) | w:60%, position:relative |
| `.gender-page__form` | Sağ taraf (form) | w:40%, bg:white/95 |
| `.gender-brand__logo` | Core Music logosu | 48×48, center |
| `.gender-brand__title` | "Seni Tanıyalım" | font:24px/700 |
| `.gender-brand__subtitle` | "Müzik deneyimini..." | font:14px/400 |
| `.gender-form__avatar` | Kullanıcı avatar ikonu | 48×48, circle |
| `.gender-form__title` | "Seni Tanıyalım" | font:28px/700 |
| `.gender-form__subtitle` | "Müzik deneyimini..." | font:14px/400 |
| `.gender-option` | Cinsiyet seçeneği | h:60, glass, radius:12px |
| `.gender-option__icon` | Cinsiyet ikonu | 24×24 |
| `.gender-option__label` | "Kız" / "Erkek" | font:16px/600 |
| `.gender-option__description` | "Theme pembe..." | font:12px/400 |
| `.gender-option__arrow` | Sağ ok ikonu | 16×16 |
| `.gender-option--selected` | Seçili durum | border:2px solid #ff4fd8 |
| `.gender-form__submit` | Devam Et butonu | gradient, h:48, radius:8px |
| `.gender-form__privacy` | Gizlilik politikası linki | font:12px |

---

## 3. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Seçili border, submit butonu |
| `--cm-primary-pink` | #ff4fd8 | Kız teması rengi |
| `--cm-primary-blue` | #4f9fff | Erkek teması rengi |
| `--cm-primary-neutral` | #a0a0b0 | Nötr tema rengi |
| `--cm-bg-form` | rgba(255,255,255,0.95) | Form arka planı |
| `--cm-bg-option` | rgba(255,255,255,0.10) | Seçenek arka planı |
| `--cm-border-option` | rgba(255,255,255,0.2) | Seçenek kenarlığı |
| `--cm-border-selected` | #ff4fd8 | Seçili kenarlık |
| `--cm-text-primary` | #0a0a0f | Başlık |
| `--cm-text-secondary` | #666666 | Açıklayıcı metin |
| `--cm-radius-md` | 8px | Input/button radius |
| `--cm-radius-lg` | 12px | Seçenek radius |
| `--cm-spacing-lg` | 24px | Form padding |
| `--cm-font-size-2xl` | 28px | Başlık |
| `--cm-font-size-lg` | 16px | Seçenek etiketi |
| `--cm-font-size-sm` | 12px | Açıklama |

---

## 4. Touch Target

| Cihaz | Min Touch | Not |
|-------|-----------|-----|
| T08 RPi5 7" | 48×48px | Seçenek h:60, Button h:48 |
| T01 Phone | 48×48px | Minimum |
| T25 TV | 80×80px | D-pad ile navigasyon |

---

## 5. WCAG Uyumu

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Kontrast 4.5:1 | ✅ | Başlık #0a0a0f beyaz arka plan |
| Keyboard | ✅ | Tab: Kız → Erkek → Nötr → Devam Et |
| Focus Visible | ✅ | Seçenek focus: border #ff4fd8, outline |
| Screen Reader | ✅ | `role="radiogroup"`, `role="radio"`, `aria-checked` |
| Error State | ✅ | Seçim yapılmadan submit: hata mesajı |

---

## 6. Seçenek Stili

```css
/* ═══ Gender Option ═══ */
.gender-option {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.10);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  cursor: pointer;
  transition: border-color 0.2s ease, background 0.2s ease;
}

.gender-option:hover {
  border-color: rgba(255, 79, 216, 0.5);
  background: rgba(255, 255, 255, 0.15);
}

.gender-option--selected {
  border-color: #ff4fd8;
  background: rgba(255, 79, 216, 0.10);
}

/* ═══ Submit Button ═══ */
.gender-form__submit {
  width: 100%;
  height: 48px;
  background: linear-gradient(135deg, #ff4fd8, #a855f7);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.gender-form__submit:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(255, 79, 216, 0.3);
}
```

---

## 7. PNG Referansı

| PNG | Viewport | Dosya Yolu |
|-----|----------|------------|
| `Linux 1024 - Select Gender.png` | 1024×600 | `.ai/.png/shared-1024/` |
| `Linux 1024 - Select Gender - selected.png` | 1024×600 | `.ai/.png/shared-1024/` |

---

## 8. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran form, brand area gizli |
| T06-T07 (Tablet) | 768-1024px | Split 50/50 |
| T08 (Embedded) | 1024×600 | Split 60/40 |
| T12-T16 (Laptop) | 1025-2560px | Split 50/50, larger form |
| T25-T28 (TV) | ≥3840px | Split 50/50, font scale 1.5x |

---

## 9. Tema Değişikliği

| Seçim | Tema Renk | Otomatik Uygulama |
|-------|-----------|-------------------|
| Kız | #ff4fd8 (Pembe) | CSS variables güncellenir |
| Erkek | #4f9fff (Mavi) | CSS variables güncellenir |
| Nötr | #a0a0b0 (Gri) | CSS variables güncellenir |

---

## 10. State Durumları

| Durum | Görsel Değişiklik |
|-------|-------------------|
| Default | Seçenekler: border rgba(255,255,255,0.2) |
| Hover | Seçenek: border rgba(255,79,216,0.5) |
| Selected | Seçenek: border #ff4fd8, bg rgba(255,79,216,0.10) |
| Submit Hover | translateY(-1px), box-shadow |
| Submit Loading | Spinner |
| Error | Seçim yapılmamışsa hata mesajı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
