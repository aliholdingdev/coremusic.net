---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Register Step 2 Screen Specification (Password)"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: shared-1024
viewport: 1024x600
device: Linux Embedded (Shared 1024px)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/shared/register-step2.md"
  source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png"
---

# CoreMusic — Register Step 2 (Password) — 1024×600

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]] · [[shared/register-step1]] · [[shared/register-step3]] · [[shared/login]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                    x:1024  │
│                                                                                                              │
│ ┌─── LEFT SIDE (60%, w:614) ──────────────────┐  ┌─── RIGHT SIDE (40%, w:410) ──────────────────────────┐  │
│ │                                              │  │                                                          │  │
│ │  🌸 Pembe sunset manzara arka planı          │  │  (y:28-60)  👤 Kadın ikonu (48×48, dairesel)            │  │
│ │  (dağ, çiçekler, gün batımı pembe)           │  │  (y:68)      ★ Hesap Oluştur                            │  │
│ │                                              │  │  (y:88)      CoreMusic ailesine katıl,                   │  │
│ │  (y:240)  ┌─── Brand Area ──────────────┐   │  │              müziğin keyfini çıkar                        │  │
│ │           │                              │   │  │                                                          │  │
│ │           │  ✨ Core Music ✨            │   │  │  (y:140) ── Şifre ──────────────────────────────────── │  │
│ │           │  Logo: ♪ not + çiçek         │   │  │  (y:155)  ┌────────────────────────────────────────┐  │  │
│ │           │                              │   │  │           │ E-postanızı yazın? (placeholder)       │  │  │
│ │           │  "İşte karşınızda"            │   │  │           │ bg:#fff, border:#e0e0e0, h:48          │  │  │
│ │           │  "mükemminel!"               │   │  │           │ ● sol kenarda pink/pembe accent        │  │  │
│ │           │  "sistem. Milyonlarca şarkı,  │   │  │           └────────────────────────────────────────┘  │  │
│ │           │  özel seçilmiş playlistler,   │   │  │                                                          │  │
│ │           │  sonsuz müzik keyfi. Burada." │   │  │  (y:210) ── Şifre Tekrar ───────────────────────────── │  │
│ │           │                              │   │  │  (y:225)  ┌────────────────────────────────────────┐  │  │
│ │           └──────────────────────────────┘   │  │           │ E-postanızı yazın? (placeholder)       │  │  │
│ │                                              │  │           │ bg:#fff, border:#e0e0e0, h:48          │  │  │
│ │                                              │  │           │ ● sol kenarda pink/pembe accent        │  │  │
│ │                                              │  │           └────────────────────────────────────────┘  │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:290)  ┌────────────────────────────────────────┐  │  │
│ │                                              │  │           │ ▶ Devam Et (gradient pembe-mor)         │  │  │
│ │                                              │  │           │    linear-gradient(#ff4fd8, #a855f7)   │  │  │
│ │                                              │  │           │    h:48, radius:8px, bold               │  │  │
│ │                                              │  │           └────────────────────────────────────────┘  │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:350)  ─────── veya şu linkler ile devam et ───────  │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:380)  [🍎Apple] [🔍Google] [📘Facebook]             │  │
│ │                                              │  │  (y:420)  [💬WhatsApp] [📷Instagram] [🎵TikTok]         │  │
│ │                                              │  │  (y:460)  [🤖Telegram]                                   │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:510)                    Hesabın yok mu? Kayıt Ol  │  │
│ │                                              │  │                              ↑ "Kayıt Ol" bold/link   │  │
│ └──────────────────────────────────────────────┘  └──────────────────────────────────────────────────────────┘  │
│                                                                                                              │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Step 2 Detaylı Bileşen

### 2.1 Form Alanları

| # | Alan | Placeholder | Tip | Validasyon | Hata Mesajı |
|---|------|-------------|-----|------------|-------------|
| 1 | Şifre | "E-postanızı yazın?" (placeholder reuse — PNG'de aynı text) | password | zorunlu, min 8 char, 1 büyük + 1 küçük + 1 rakam | "En az 8 karakter, büyük harf, küçük harf ve rakam içermeli" |
| 2 | Şifre Tekrar | "E-postanızı yazın?" (placeholder reuse) | password | zorunlu, Step 1 ile eşleşme | "Şifreler eşleşmiyor" |

> **PNG Placeholder Notu:** PNG'de her iki input'ta da "E-postanızı yazın?" placeholder'ı görünüyor — bu muhtemelen placeholder template/generic. Gerçek implementasyonda:
> - Şifre → "Şifrenizi yazın?"
> - Şifre Tekrar → "Şifrenizi tekrar yazın?"

### 2.2 Devam Et Butonu

| Özellik | Değer |
|---------|-------|
| Metin | "Devam Et" |
| Gradient | `linear-gradient(135deg, #ff4fd8, #a855f7)` |
| Yükseklik | 48px |
| Radius | 8px |
| Font | 16px, weight 600, color white |
| Hover | translateY(-1px) + boxShadow |
| Disabled | Her iki alan da dolu ve eşleşmiyorsa disabled |

---

## 3. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.register-page` | Ana container | 1024×600, flex row |
| `.register-page__brand` | Sol taraf | w:60%, manzara bg |
| `.register-page__form` | Sağ taraf | w:40%, bg:rgba(255,255,255,0.95) |
| `.register-form__avatar` | Kadın ikonu | 48×48, circle |
| `.register-form__title` | "Hesap Oluştur" | font:24px/700 |
| `.register-form__subtitle` | "CoreMusic ailesine katıl..." | font:14px/400 |
| `.register-form__group` | Input grubu | flex column, gap:4px |
| `.register-form__label` | "Şifre" / "Şifre Tekrar" | font:12px/600 |
| `.register-form__input` | Password input | h:48, radius:8px |
| `.register-form__input--accent` | Sol pink accent | border-left:3px solid #ff4fd8 |
| `.register-form__submit` | "Devam Et" | gradient, h:48, radius:8px |
| `.register-form__divider` | Ayırıcı | flex row, text gray |
| `.register-form__social` | Sosyal grid | 3 sütun |
| `.register-form__social-btn` | Sosyal buton | h:40, radius:8px |
| `.register-form__footer` | "Hesabın yok mu?" | font:14px |
| `.register-form__footer-link` | "Kayıt Ol" | bold, color:#ff4fd8 |

---

## 4. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Submit, sol accent, linkler |
| `--cm-primary-end` | #a855f7 | Submit gradient bitişi |
| `--cm-bg-form` | rgba(255,255,255,0.95) | Form glassmorphism |
| `--cm-bg-input` | #ffffff | Input arka planı |
| `--cm-border-input` | #e0e0e0 | Input kenarlığı |
| `--cm-border-accent` | #ff4fd8 | Sol kenar accent |
| `--cm-text-heading` | #0a0a0f | Başlık |
| `--cm-text-body` | #333333 | Label |
| `--cm-text-secondary` | #666666 | Subtitle |
| `--cm-text-placeholder` | #999999 | Placeholder |
| `--cm-radius-md` | 8px | Input/button radius |
| `--cm-spacing-xs` | 4px | Label-input gap |
| `--cm-spacing-lg` | 24px | Alanlar arası |
| `--cm-spacing-xl` | 40px | Form padding |

---

## 5. Touch Target & WCAG

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Min Touch 48×48 | ✅ | Input h:48, Button h:48 |
| Kontrast 4.5:1 | ✅ | Başlık #0a0a0f beyaz üzerinde |
| Keyboard | ✅ | Tab: Şifre → Şifre Tekrar → Devam Et → Social |
| Focus Visible | ✅ | border:#ff4fd8, box-shadow glow |
| Screen Reader | ✅ | `aria-label="Şifre"`, `aria-label="Şifre Tekrar"`, `aria-required="true"` |
| Error State | ✅ | border:#ef4444, aria-describedby |
| Autocomplete | ✅ | `autocomplete="new-password"` (her ikisi de) |
| Şifre gücü | ✅ | Opsiyonel: strength indicator bar |

---

## 6. Validasyon Kuralları

### Şifre
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | true | "Şifrenizi girin" |
| Min uzunluk | 8 | "En az 8 karakter olmalı" |
| Büyük harf | ≥1 | "En az bir büyük harf içermeli" |
| Küçük harf | ≥1 | "En az bir küçük harf içermeli" |
| Rakam | ≥1 | "En az bir rakam içermeli" |
| Özel karakter | ≥1 (opsiyonel) | "Özel karakter önerilir" |

### Şifre Tekrar
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | true | "Şifrenizi tekrar girin" |
| Eşleşme | Şifre ile aynı | "Şifreler eşleşmiyor" |

### Şifre Güç Göstergesi (Opsiyonel)

```
┌──────────────────────────────────────────┐
│ Güç: [■■■□□] Orta                        │
│ Kriterler:                                │
│   ✅ En az 8 karakter                     │
│   ✅ Büyük harf var                       │
│   ❌ Büyük harf yok                       │
│   ✅ Rakam var                            │
│   ❌ Özel karakter yok                    │
└──────────────────────────────────────────┘
```

| Güç | Bar | Renk | Kriter |
|-----|-----|------|--------|
| Zayıf | ■□□□□ | #ef4444 | <3 kriter |
| Orta | ■■■□□ | #f59e0b | 3-4 kriter |
| Güçlü | ■■■■□ | #22c55e | 4-5 kriter |
| Çok Güçlü | ■■■■■ | #16a34a | 5/5 kriter |

---

## 7. Form Stili

```css
/* ═══ Password Input ═══ */
.register-form__input[type="password"] {
  width: 100%;
  height: 48px;
  padding: 0 16px;
  border: 1px solid #e0e0e0;
  border-left: 3px solid #ff4fd8;
  border-radius: 8px;
  font-size: 14px;
  background: #fff;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.register-form__input[type="password"]:focus {
  outline: none;
  border-color: #ff4fd8;
  border-left-color: #ff4fd8;
  box-shadow: 0 0 0 3px rgba(255, 79, 216, 0.15);
}

/* ═══ Password Toggle ═══ */
.register-form__password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #999;
  font-size: 16px;
}

.register-form__password-toggle:hover {
  color: #666;
}

/* ═══ Password Strength ═══ */
.register-form__strength {
  display: flex;
  gap: 4px;
  margin-top: 6px;
}

.register-form__strength-bar {
  flex: 1;
  height: 3px;
  border-radius: 2px;
  background: #e0e0e0;
  transition: background 0.3s ease;
}

.register-form__strength-bar--active { background: #ef4444; }
.register-form__strength-bar--medium { background: #f59e0b; }
.register-form__strength-bar--strong { background: #22c55e; }

.register-form__strength-text {
  font-size: 11px;
  margin-top: 2px;
}

/* ═══ Mismatch Indicator ═══ */
.register-form__mismatch {
  display: flex;
  align-items: center;
  gap: 4px;
  color: #ef4444;
  font-size: 12px;
  margin-top: 2px;
}

.register-form__match {
  display: flex;
  align-items: center;
  gap: 4px;
  color: #22c55e;
  font-size: 12px;
  margin-top: 2px;
}
```

---

## 8. State Durumları

| Durum | Görsel Değişiklik | JS Davranışı |
|-------|-------------------|--------------|
| Default | Input bg:#fff, border:#e0e0e0, sol accent:#ff4fd8 | — |
| Focus | Border:#ff4fd8, box-shadow glow | — |
| Filled | Sol accent korunur | Real-time validation |
| Mismatch | Her iki input da border:#ef4444, hata mesajı | Eşleşme kontrolü |
| Match | Her iki input da border:#22c55e, ✓ ikonu | Eşleşme doğrulandı |
| Error | Border:#ef4444, hata mesajı | aria-describedby |
| Loading | Submit spinner | API çağrısı |
| Disabled | Opacity:0.5 | Buton disabled |

---

## 9. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran form, brand gizli |
| T06-T07 (Tablet) | 768-1024px | Split 50/50 |
| T08 (Embedded) | 1024×600 | Split 60/40 (PNG reference) |
| T12-T16 (Laptop) | 1025-2560px | Split 50/50 |
| T25-T28 (TV) | ≥3840px | Split 50/50, font scale 1.5x |

---

## 10. Adım İlerleme Göstergesi (Opsiyonel)

```
  ┌─────┐       ┌─────┐       ┌─────┐
  │  1  │──────▶│  2  │──────▶│  3  │
  │  ●  │       │  ●  │       │  ○  │
  └─────┘       └─────┘       └─────┘
  Adım 1 ✓      Adım 2 ●      Adım 3 ○
  (Ad + Email)  (Şifre)       (Telefon)
```

| Step | Durum | Görsel |
|:----:|-------|--------|
| 1 | Tamamlandı | Circle filled #ff4fd8, ✓ ikonu, line solid #ff4fd8 |
| 2 | Aktif | Circle filled #ff4fd8, outline glow |
| 3 | Bekliyor | Circle outline #e0e0e0, number gray |

---

## 11. PNG Referansı

| PNG | Viewport | Dosya Yolu | Sıra |
|-----|----------|------------|:----:|
| `Linux 1024 - Register Girl.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 1 |
| `Linux 1024 - Register Girl step 2.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 2 |
| `Linux 1024 - Register Girl step 3.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 3 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
