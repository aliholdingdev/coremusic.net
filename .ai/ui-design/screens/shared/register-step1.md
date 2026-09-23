---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Register Step 1 Screen Specification (Name + Email)"
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
  authority: ".ai/ui-design/screens/shared/register-step1.md"
  source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl.png"
---

# CoreMusic — Register Step 1 (Name + Email) — 1024×600

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]] · [[../shared/login]] · [[../shared/register-step2]] · [[../shared/register-step3]]

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
│ │           │  ✨ Core Music ✨            │   │  │  (y:140) ── Kullanıcı Adı ──────────────────────────── │  │
│ │           │  Logo: ♪ not + çiçek         │   │  │  (y:155)  ┌────────────────────────────────────────┐  │  │
│ │           │                              │   │  │           │ Adınızı yazın? (placeholder)           │  │  │
│ │           │  "İşte karşınızda"            │   │  │           │ bg:#fff, border:#e0e0e0, h:48          │  │  │
│ │           │  "mükemminel!"               │   │  │           │ ● sol kenarda pink/pembe accent        │  │  │
│ │           │  "sistem. Milyonlarca şarkı,  │   │  │           └────────────────────────────────────────┘  │  │
│ │           │  özel seçilmiş playlistler,   │   │  │                                                          │  │
│ │           │  sonsuz müzik keyfi. Burada." │   │  │  (y:210) ── E-posta ────────────────────────────────── │  │
│ │           │                              │   │  │  (y:225)  ┌────────────────────────────────────────┐  │  │
│ │           └──────────────────────────────┘   │  │           │ E-postanızı yazın? (placeholder)        │  │  │
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

## 2. Adım Akışı (3-Step Registration)

| Adım | Dosya | Form Alanları | CTA | Not |
|:----:|-------|---------------|-----|-----|
| **1** | `Register Girl.png` | Kullanıcı Adı + E-posta | Devam Et | İlk kayıt — kimlik bilgisi |
| **2** | `Register Girl step 2.png` | Şifre + Şifre Tekrar | Devam Et | Güvenlik — şifre oluşturma |
| **3** | `Register Girl step 3.png` | Telefon + KVKK checkbox | Kayıt Ol | Tamamlama — doğrulama |

**Flow Mantığı:**
```
Step 1 (Name+Email) → "Devam Et" → Step 2 (Password) → "Devam Et" → Step 3 (Phone) → "Kayıt Ol" → Home
```

---

## 3. Step 1 Detaylı bileşen

### 3.1 Form Alanları

| # | Alan | Placeholder | Tip | Validasyon | Hata Mesajı |
|---|------|-------------|-----|------------|-------------|
| 1 | Kullanıcı Adı | "Adınızı yazın?" | text | zorunlu, 2-50 char, a-zA-ZçğıöşüÇĞIİÖŞÜ | "Adınızı girin (2-50 karakter)" |
| 2 | E-posta | "E-postanızı yazın?" | email | zorunlu, RFC 5322 format | "Geçerli bir e-posta girin" |

### 3.2 Devam Et Butonu

| Özellik | Değer |
|---------|-------|
| Metin | "Devam Et" |
| Gradient | `linear-gradient(135deg, #ff4fd8, #a855f7)` |
| Yükseklik | 48px |
| Radius | 8px |
| Font | 16px, weight 600, color white |
| Hover | translateY(-1px) + boxShadow |
| Disabled | Step 1: her iki alan da dolu değilse disabled |

---

## 4. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.register-page` | Ana container (flex row) | 1024×600, flex |
| `.register-page__brand` | Sol taraf (manzara + logo) | w:60%, overflow:hidden |
| `.register-page__form` | Sağ taraf (form paneli) | w:40%, bg:rgba(255,255,255,0.95) |
| `.register-brand__logo` | Core Music logosu | 40×40, ♪ not ikonu |
| `.register-brand__title` | "İşte karşınızda" | font:24px/700, cursive |
| `.register-brand__subtitle` | "mükemminel!" | font:28px/700, cursive, italic |
| `.register-brand__desc` | "sistem. Milyonlarca..." | font:14px/400 |
| `.register-form__avatar` | Kadın ikonu | 48×48, dairesel, border |
| `.register-form__title` | "Hesap Oluştur" | font:28px/700 |
| `.register-form__subtitle` | "CoreMusic ailesine katıl..." | font:14px/400, color:#666 |
| `.register-form__group` | Input grubu (label+input) | flex column, gap:4px |
| `.register-form__label` | "Kullanıcı Adı" / "E-posta" | font:12px/600, color:#333 |
| `.register-form__input` | Input field | h:48, radius:8px, bg:#fff, border:1px solid #e0e0e0 |
| `.register-form__input--accent` | Sol kenar pink accent | border-left:3px solid #ff4fd8 |
| `.register-form__submit` | "Devam Et" butonu | gradient, h:48, radius:8px |
| `.register-form__divider` | "veya şu linkler ile devam et" | flex row, gap:8px, text gray |
| `.register-form__social` | Sosyal medya grid | 3 sütun, gap:12px |
| `.register-form__social-btn` | Sosyal buton | h:40, radius:8px, border:1px solid #e0e0e0 |
| `.register-form__footer` | "Hesabın yok mu?" | font:14px, color:#666 |
| `.register-form__footer-link` | "Kayıt Ol" | font:14px, bold, color:#ff4fd8 |

---

## 5. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Submit butonu gradient başlangıcı, sol accent, linkler |
| `--cm-primary-end` | #a855f7 | Submit butonu gradient bitişi |
| `--cm-bg-form` | rgba(255,255,255,0.95) | Form arka planı (glassmorphism) |
| `--cm-bg-input` | #ffffff | Input arka planı |
| `--cm-border-input` | #e0e0e0 | Input kenarlığı |
| `--cm-border-accent` | #ff4fd8 | Sol kenar accent (input focus) |
| `--cm-text-heading` | #0a0a0f | Başlık |
| `--cm-text-body` | #333333 | Label |
| `--cm-text-secondary` | #666666 | Açıklayıcı metin, subtitle |
| `--cm-text-placeholder` | #999999 | Input placeholder |
| `--cm-radius-md` | 8px | Input/button radius |
| `--cm-spacing-xs` | 4px | Label-input gap |
| `--cm-spacing-sm` | 8px | Divider gap |
| `--cm-spacing-md` | 16px | Form padding horizontal |
| `--cm-spacing-lg` | 24px | Form gap (alanlar arası) |
| `--cm-spacing-xl` | 40px | Form padding top/bottom |
| `--cm-font-size-sm` | 12px | Label, divider text |
| `--cm-font-size-md` | 14px | Subtitle, description |
| `--cm-font-size-lg` | 16px | Submit buton text |
| `--cm-font-size-2xl` | 28px | Form başlığı, brand subtitle |
| `--cm-font-size-3xl` | 32px | Brand title |
| `--cm-shadow-sm` | 0 2px 8px rgba(0,0,0,0.08) | Form paneli gölgesi |
| `--cm-shadow-input-focus` | 0 0 0 3px rgba(255,79,216,0.15) | Input focus gölgesi |

---

## 6. Touch Target

| Cihaz | Min Touch | Uygulama |
|-------|-----------|----------|
| T08 Embedded (1024×600) | 48×48px | Input h:48, Button h:48, Social btn h:40 |
| T01 Phone (≤767px) | 48×48px | Tüm interaktif elemanlar |
| T25 TV (≥3840px) | 80×80px | D-pad navigasyon için |

---

## 7. WCAG Uyumu

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Kontrast 4.5:1 | ✅ | Başlık #0a0a0f beyaz arka plan üzerinde |
| Kontrast 3:1 | ✅ | Placeholder #999 yeterli mi? → #666 önerilir |
| Keyboard | ✅ | Tab sırası: Kullanıcı Adı → E-posta → Devam Et → Social butonlar |
| Focus Visible | ✅ | Input focus: border #ff4fd8, outline 2px, box-shadow |
| Screen Reader | ✅ | `aria-label="Kullanıcı Adı"`, `aria-required="true"`, form `role="form"` |
| Error State | ✅ | Hatalı input: border #ef4444, aria-describedby ile hata mesajı |
| Autocomplete | ✅ | `autocomplete="name"`, `autocomplete="email"` |
| Heading Hierarchy | ✅ | h1: "Hesap Oluştur", h2: "Kullanıcı Adı" |
| Landmark | ✅ | `<main>`, `<form>`, `<footer>` landmark'ları |

---

## 8. Form Stili

```css
/* ═══ Page Container ═══ */
.register-page {
  display: flex;
  width: 1024px;
  height: 600px;
  overflow: hidden;
}

/* ═══ Brand Side ═══ */
.register-page__brand {
  width: 60%;
  position: relative;
  background: url('register-bg.jpg') center/cover no-repeat;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 0 40px;
}

.register-brand__logo {
  width: 40px;
  height: 40px;
}

.register-brand__title {
  font-size: 32px;
  font-weight: 700;
  font-style: italic;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.register-brand__subtitle {
  font-size: 28px;
  font-weight: 700;
  font-style: italic;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.register-brand__desc {
  font-size: 14px;
  color: rgba(255,255,255,0.85);
  max-width: 280px;
  line-height: 1.5;
}

/* ═══ Form Side ═══ */
.register-page__form {
  width: 40%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  padding: 24px 32px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  overflow-y: auto;
}

/* ═══ Avatar ═══ */
.register-form__avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 2px solid #e0e0e0;
  object-fit: cover;
}

/* ═══ Title ═══ */
.register-form__title {
  font-size: 24px;
  font-weight: 700;
  color: #0a0a0f;
  margin-top: 8px;
}

.register-form__subtitle {
  font-size: 13px;
  color: #666;
  text-align: center;
  margin-top: 4px;
  margin-bottom: 16px;
}

/* ═══ Input Group ═══ */
.register-form__group {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 12px;
}

.register-form__label {
  font-size: 12px;
  font-weight: 600;
  color: #333;
}

.register-form__input {
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

.register-form__input:focus {
  outline: none;
  border-color: #ff4fd8;
  border-left-color: #ff4fd8;
  box-shadow: 0 0 0 3px rgba(255, 79, 216, 0.15);
}

.register-form__input::placeholder {
  color: #999;
  font-style: italic;
}

/* ═══ Submit Button ═══ */
.register-form__submit {
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
  margin-top: 8px;
}

.register-form__submit:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(255, 79, 216, 0.3);
}

.register-form__submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* ═══ Divider ═══ */
.register-form__divider {
  display: flex;
  align-items: center;
  width: 100%;
  margin: 16px 0;
  gap: 8px;
}

.register-form__divider-line {
  flex: 1;
  height: 1px;
  background: #e0e0e0;
}

.register-form__divider-text {
  font-size: 12px;
  color: #999;
  white-space: nowrap;
}

/* ═══ Social Grid ═══ */
.register-form__social {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  width: 100%;
}

.register-form__social-btn {
  height: 40px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease, transform 0.1s ease;
  font-size: 16px;
}

.register-form__social-btn:hover {
  background: #f5f5f5;
  transform: translateY(-1px);
}

/* ═══ Footer ═══ */
.register-form__footer {
  font-size: 14px;
  color: #666;
  margin-top: auto;
  padding-top: 16px;
}

.register-form__footer-link {
  color: #ff4fd8;
  font-weight: 700;
  text-decoration: none;
  margin-left: 4px;
}

.register-form__footer-link:hover {
  text-decoration: underline;
}

/* ═══ Error State ═══ */
.register-form__input--error {
  border-color: #ef4444;
  border-left-color: #ef4444;
}

.register-form__input--error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.register-form__error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 2px;
}

/* ═══ Success State ═══ */
.register-form__input--success {
  border-color: #22c55e;
  border-left-color: #22c55e;
}

.register-form__check-icon {
  color: #22c55e;
  font-size: 14px;
  margin-left: 8px;
}
```

---

## 9. State Durumları

| Durum | Görsel Değişiklik | JS Davranışı |
|-------|-------------------|--------------|
| Default | Input bg:#fff, border:#e0e0e0, sol accent:#ff4fd8 | — |
| Focus | Border:#ff4fd8, box-shadow glow | — |
| Filled | Sol accent korunur, validation başlar | Real-time validation |
| Error | Border:#ef4444, hata mesajı göster | aria-describedby, aria-invalid |
| Success | Border:#22c55e, ✓ ikonu | Geçerli input |
| Loading | Submit spinner animasyonu | API çağrısı |
| Disabled | Opacity:0.5, cursor:not-allowed | Buton disabled |

---

## 10. Validasyon Kuralları

### Kullanıcı Adı
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | true | "Adınızı girin" |
| Min uzunluk | 2 | "En az 2 karakter girin" |
| Max uzunluk | 50 | "En fazla 50 karakter girin" |
| Pattern | `[a-zA-ZçğıöşüÇĞIİÖŞÜ ]` | "Sadece harf ve boşluk kullanın" |
| İlk karakter | Harf olmalı | "İlk karakter harf olmalı" |

### E-posta
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | true | "E-posta adresinizi girin" |
| Pattern | RFC 5322 | "Geçerli bir e-posta adresi girin" |
| Unique | DB kontrol | "Bu e-posta zaten kayıtlı" |

---

## 11. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran form, brand gizli, fullscreen |
| T06-T07 (Tablet) | 768-1024px | Split 50/50 |
| T08 (Embedded) | 1024×600 | Split 60/40 (PNG reference) |
| T12-T16 (Laptop) | 1025-2560px | Split 50/50,更大的 form |
| T25-T28 (TV) | ≥3840px | Split 50/50, font scale 1.5x |

---

## 12. PNG Referansı

| PNG | Viewport | Dosya Yolu | Sıra |
|-----|----------|------------|:----:|
| `Linux 1024 - Register Girl.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 1 |
| `Linux 1024 - Register Girl step 2.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 2 |
| `Linux 1024 - Register Girl step 3.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 3 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
