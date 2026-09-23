---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Welcome Popup Screen Specification"
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
  authority: ".ai/ui-design/screens/T08-embedded/welcome-popup.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png"
---

# CoreMusic — Welcome Popup (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                              x:1024    │
│ y:0 ┌─── HOME DASHBOARD (ARKA PLAN — Bulanık) ────────────────────────────────────────────────────────┐  │
│     │ (Tüm home dashboard içeriği arka planda görünür, bulanıklaştırılmış)                           │  │
│ y:600└────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│     ┌─── MODAL OVERLAY (tüm ekranı kaplar, bg: rgba(0,0,0,0.5)) ──────────────────────────────────────┐ │
│     │                                                                                                  │ │
│     │     ┌─── MODAL (w:600, h:308, center) ──────────────────────────────────────────────────────┐   │ │
│     │     │                                                                                      │   │ │
│     │     │  ┌─── MODAL IMAGE (sol %50, w:300) ──┐  ┌─── MODAL CONTENT (sağ %50, w:300) ─────┐  │   │ │
│     │     │  │                                    │  │                                          │  │   │ │
│     │     │  │  🏖️ Plaj manzarası                 │  │    ✨ Core Music ✨                      │  │   │ │
│     │     │  │  (kadın + gün batımı + deniz)      │  │                                          │  │   │ │
│     │     │  │                                    │  │    Hoş Geldin                            │  │   │ │
│     │     │  │                                    │  │                                          │  │   │ │
│     │     │  │                                    │  │    İsmini Söyle Bize                     │  │   │ │
│     │     │  │                                    │  │                                          │  │   │ │
│     │     │  │                                    │  │    "Sana özel seçimler, zevklere göre     │  │   │ │
│     │     │  │                                    │  │     öneriler ve unutulmaz anlar için      │  │   │ │
│     │     │  │                                    │  |     sadece bir adım uzağındayız.           │  │   │ │
│     │     │  │                                    │  │     Core Music ile müziğin tadını çıkar. 💜"│  │   │ │
│     │     │  │                                    │  │                                          │  │   │ │
│     │     │  │                                    │  │    ┌──────────────────────────────────┐   │  │   │ │
│     │     │  │                                    │  │    │ ▶ Başla                           │   │  │   │ │
│     │     │  │                                    │  │    └──────────────────────────────────┘   │  │   │ │
│     │     │  │                                    │  │                                          │  │   │ │
│     │     │  └────────────────────────────────────┘  └──────────────────────────────────────────┘  │   │ │
│     │     │                                                                                      │   │ │
│     │     └──────────────────────────────────────────────────────────────────────────────────────┘   │ │
│     │                                                                                                  │ │
│     └──────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.welcome-overlay` | Modal arka plan overlay | full screen, bg:rgba(0,0,0,0.5) |
| `.welcome-modal` | Ana modal container | w:600, h:308, radius:20px |
| `.welcome-modal__image` | Sol taraftaki görsel | w:50%, object-fit:cover |
| `.welcome-modal__content` | Sağ taraftaki içerik | w:50%, padding:32px |
| `.welcome-modal__logo` | Core Music logosu | 48×48, center |
| `.welcome-modal__title` | "Hoş Geldin" başlığı | font:24px/700, center |
| `.welcome-modal__subtitle` | "İsmini Söyle Bize" | font:16px/500, center |
| `.welcome-modal__description` | Açıklama metni | font:12px/400, center |
| `.welcome-modal__cta` | Başla butonu | gradient, w:200, h:48, radius:24px |
| `.welcome-modal__close` | Kapat butonu (opsiyonel) | 24×24, top-right |

---

## 3. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | CTA butonu gradient |
| `--cm-primary-light` | #ff7ee4 | Hover durumu |
| `--cm-primary-dark` | #d63cb8 | Active durumu |
| `--cm-bg-overlay` | rgba(0,0,0,0.5) | Modal arka plan |
| `--cm-bg-modal` | rgba(255,255,255,0.95) | Modal arka plan |
| `--cm-bg-glass` | rgba(255,255,255,0.15) | Cam efekti |
| `--cm-radius-xl` | 20px | Modal radius |
| `--cm-radius-full` | 24px | CTA buton radius |
| `--cm-text-primary` | #0a0a0f | Koyu başlık |
| `--cm-text-secondary` | #666666 | Açıklayıcı metin |
| `--cm-spacing-xl` | 32px | Modal padding |
| `--cm-font-size-2xl` | 24px | Başlık |
| `--cm-font-size-lg` | 16px | Alt başlık |
| `--cm-font-size-sm` | 12px | Açıklama |

---

## 4. Touch Target

| Cihaz | Min Touch | Not |
|-------|-----------|-----|
| T08 RPi5 7" | 48×48px | Başla butonu: 200×48px |
| T01 Phone | 48×48px | Minimum |
| T25 TV | 80×80px | D-pad ile navigasyon |

---

## 5. WCAG Uyumu

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Kontrast 4.5:1 | ✅ | Koyu başlık (#0a0a0f) beyaz arka plan |
| Kontrast 3:1 | ✅ | İkincil metin (#666) beyaz arka plan |
| Keyboard | ✅ | Tab: Close → CTA |
| Focus Visible | ✅ | CTA butonu focus ring |
| Screen Reader | ✅ | `role="dialog"`, `aria-modal="true"`, `aria-labelledby` |
| Escape Key | ✅ | Escape ile modal kapatma |
| Click Outside | ✅ | Overlay tıklaması ile kapatma |

---

## 6. Glassmorphism / Modal Stili

```css
/* ═══ Overlay ═══ */
.welcome-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

/* ═══ Modal ═══ */
.welcome-modal {
  display: flex;
  width: 600px;
  height: 308px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

/* ═══ Image Side ═══ */
.welcome-modal__image {
  width: 50%;
  object-fit: cover;
}

/* ═══ Content Side ═══ */
.welcome-modal__content {
  width: 50%;
  padding: 32px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

/* ═══ CTA Button ═══ */
.welcome-modal__cta {
  background: linear-gradient(135deg, #ff4fd8, #a855f7);
  color: white;
  border: none;
  border-radius: 24px;
  padding: 12px 40px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.welcome-modal__cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 79, 216, 0.4);
}
```

---

## 7. PNG Referansı

| PNG | Viewport | Dosya Yolu |
|-----|----------|------------|
| `Linux 1024 - Home Page Welcome Popup.png` | 1024×600 | `.ai/.png/home-1024/` |

---

## 8. Responsive Davranış

| Tier | Viewport | Modal Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran modal, w:100%, h:100% |
| T06-T07 (Tablet) | 768-1024px | 500×280px modal |
| T08 (Embedded) | 1024×600 | 600×308px modal |
| T12-T16 (Laptop) | 1025-2560px | 600×308px modal |
| T25-T28 (TV) | ≥3840px | 800×400px modal, font scale 1.5x |

---

## 9. Animasyon

| Animasyon | Süre | Easing |
|-----------|------|--------|
| Modal giriş | 300ms | ease-out |
| Overlay fade-in | 200ms | ease |
| CTA pulse | 2s loop | ease-in-out |
| Close exit | 200ms | ease-in |

---

## 10. State Durumları

| Durum | Görsel Değişiklik |
|-------|-------------------|
| Default | Modal açık, overlay bulanık |
| Hover (CTA) | translateY(-2px), box-shadow |
| Active (CTA) | Scale(0.98) |
| Loading | CTA'da spinner |
| Kapalı | Modal kaybolur, overlay fade-out |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
