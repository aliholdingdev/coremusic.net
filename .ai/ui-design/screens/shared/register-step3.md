---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Register Step 3 Screen Specification (Phone + KVKK)"
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
  authority: ".ai/ui-design/screens/shared/register-step3.md"
  source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png"
---

# CoreMusic — Register Step 3 (Phone + KVKK) — 1024×600

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]] · [[../shared/register-step1]] · [[../shared/register-step2]] · [[../shared/login]]

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
│ │           │  ✨ Core Music ✨            │   │  │  (y:140) ── Telefon ────────────────────────────────── │  │
│ │           │  Logo: ♪ not + çiçek         │   │  │  (y:155)  ┌────────────────────────────────────────┐  │  │
│ │           │                              │   │  │           │ E-postanızı yazın? (placeholder)       │  │  │
│ │           │  "İşte karşınızda"            │   │  │           │ bg:#fff, border:#e0e0e0, h:48          │  │  │
│ │           │  "mükemminel!"               │   │  │           │ ● sol kenarda pink/pembe accent        │  │  │
│ │           │  "sistem. Milyonlarca şarkı,  │   │  │           └────────────────────────────────────────┘  │  │
│ │           │  özel seçilmiş playlistler,   │   │  │                                                          │  │
│ │           │  sonsuz müzik keyfi. Burada." │   │  │  (y:210)  ☑ Kullanım şartlarını ve politikaları        │  │
│ │           │                              │   │  │              kabul ediyorum                            │  │
│ │           └──────────────────────────────┘   │  │              (checkbox + label, font:12px)              │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:260)  ┌────────────────────────────────────────┐  │  │
│ │                                              │  │           │ ▶ Kayıt Ol (gradient pembe-mor)         │  │  │
│ │                                              │  │           │    linear-gradient(#ff4fd8, #a855f7)   │  │  │
│ │                                              │  │           │    h:48, radius:8px, bold               │  │  │
│ │                                              │  │           └────────────────────────────────────────┘  │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:320)  ─────── veya şu linkler ile devam et ───────  │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:350)  [🍎Apple] [🔍Google] [📘Facebook]             │  │
│ │                                              │  │  (y:390)  [💬WhatsApp] [📷Instagram] [🎵TikTok]         │  │
│ │                                              │  │  (y:430)  [🤖Telegram]                                   │  │
│ │                                              │  │                                                          │  │
│ │                                              │  │  (y:510)                    Hesabın yok mu? Kayıt Ol  │  │
│ │                                              │  │                              ↑ "Kayıt Ol" bold/link   │  │
│ └──────────────────────────────────────────────┘  └──────────────────────────────────────────────────────────┘  │
│                                                                                                              │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Step 3 Detaylı Bileşen

### 2.1 Form Alanları

| # | Alan | Placeholder | Tip | Validasyon | Hata Mesajı |
|---|------|-------------|-----|------------|-------------|
| 1 | Telefon | "E-postanızı yazın?" (placeholder reuse — PNG'de aynı text) | tel | opsiyonel, +90 5XX format | "Geçerli bir telefon numarası girin" |
| 2 | KVKK Checkbox | "Kullanım şartlarını ve politikaları kabul ediyorum" | checkbox | **zorunlu** — checked olmalı | "Kayıt olmak için şartları kabul etmelisiniz" |

> **PNG Placeholder Notu:** PNG'de input placeholder'ı "E-postanızı yazın?" görünüyor — bu muhtemelen placeholder template. Gerçek implementasyonda:
> - Telefon → "+90 5XX XXX XX XX" veya "Telefon numaranızı yazın?"

### 2.2 Kayıt Ol Butonu

| Özellik | Değer |
|---------|-------|
| Metin | "Kayıt Ol" (Step 1-2'deki "Devam Et" yerine) |
| Gradient | `linear-gradient(135deg, #ff4fd8, #a855f7)` |
| Yükseklik | 48px |
| Radius | 8px |
| Font | 16px, weight 600, color white |
| Hover | translateY(-1px) + boxShadow |
| Disabled | KVKK checkbox unchecked ise disabled |

### 2.3 KVKK Checkbox

| Özellik | Değer |
|---------|-------|
| Boyut | 16×16px |
| Renk (checked) | #ff4fd8 |
| Renk (unchecked) | #e0e0e0 border |
| Label font | 12px, color:#333 |
| Link | "Kullanım şartlarını" tıklanabilir → modal açar |
| Link rengi | #ff4fd8, underline |

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
| `.register-form__label` | "Telefon" | font:12px/600 |
| `.register-form__input` | Telefon input | h:48, radius:8px |
| `.register-form__input--accent` | Sol pink accent | border-left:3px solid #ff4fd8 |
| `.register-form__kvkk` | KVKK checkbox grubu | flex row, gap:8px, items:start |
| `.register-form__checkbox` | Checkbox | 16×16, accent:#ff4fd8 |
| `.register-form__checkbox-label` | KVKK metni | font:12px, color:#333 |
| `.register-form__checkbox-link` | "Kullanım şartlarını" | color:#ff4fd8, underline |
| `.register-form__submit` | "Kayıt Ol" | gradient, h:48, radius:8px |
| `.register-form__divider` | Ayırıcı | flex row, text gray |
| `.register-form__social` | Sosyal grid | 3 sütun |
| `.register-form__social-btn` | Sosyal buton | h:40, radius:8px |
| `.register-form__footer` | "Hesabın yok mu?" | font:14px |
| `.register-form__footer-link` | "Kayıt Ol" | bold, color:#ff4fd8 |

---

## 4. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Submit, checkbox, linkler |
| `--cm-primary-end` | #a855f7 | Submit gradient bitişi |
| `--cm-bg-form` | rgba(255,255,255,0.95) | Form glassmorphism |
| `--cm-bg-input` | #ffffff | Input arka planı |
| `--cm-border-input` | #e0e0e0 | Input kenarlığı |
| `--cm-border-accent` | #ff4fd8 | Sol kenar accent |
| `--cm-text-heading` | #0a0a0f | Başlık |
| `--cm-text-body` | #333333 | Label, checkbox text |
| `--cm-text-secondary` | #666666 | Subtitle |
| `--cm-text-placeholder` | #999999 | Placeholder |
| `--cm-checkbox-checked` | #ff4fd8 | Checkbox check rengi |
| `--cm-checkbox-border` | #e0e0e0 | Checkbox unchecked border |
| `--cm-radius-md` | 8px | Input/button radius |
| `--cm-spacing-xs` | 4px | Label-input gap |
| `--cm-spacing-sm` | 8px | Checkbox gap |
| `--cm-spacing-lg` | 24px | Alanlar arası |
| `--cm-font-size-xs` | 11px | KVKK link |
| `--cm-font-size-sm` | 12px | Label, checkbox, divider |
| `--cm-font-size-md` | 14px | Subtitle, footer |
| `--cm-font-size-lg` | 16px | Submit |

---

## 5. Touch Target & WCAG

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Min Touch 48×48 | ✅ | Input h:48, Button h:48, Checkbox area 44×44 (touch padding) |
| Kontrast 4.5:1 | ✅ | Başlık #0a0a0f beyaz üzerinde |
| Keyboard | ✅ | Tab: Telefon → Checkbox → Kayıt Ol → Social |
| Focus Visible | ✅ | Input: border:#ff4fd8; Checkbox: outline 2px |
| Screen Reader | ✅ | `aria-label="Telefon"`, `role="checkbox"`, `aria-checked`, `aria-required="true"` |
| Error State | ✅ | border:#ef4444, aria-describedby |
| Autocomplete | ✅ | `autocomplete="tel"` |
| KVKK Link | ✅ | `aria-label="Kullanım şartlarını oku"`, `target="_blank"`, `rel="noopener"` |
| Legal Compliance | ✅ | Kayıt olmadan önce KVKK onayı zorunlu |

---

## 6. Validasyon Kuralları

### Telefon (Opsiyonel)
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | false (opsiyonel) | — |
| Format | +90 5XX XXX XX XX | "Geçerli bir telefon numarası girin" |
| Min uzunluk | 10 (05XX...) | "Geçerli bir telefon numarası girin" |
| Max uzunluk | 13 (+90 5XX XXX XX XX) | "Telefon numarası çok uzun" |

### KVKK Checkbox
| Kural | Değer | Hata |
|-------|-------|------|
| Zorunlu | **true** | "Kayıt olmak için şartları kabul etmelisiniz" |
| Default | unchecked | — |
| Onay | tıklanmalı | Link tıklanınca modal açılır |

---

## 7. KVKK Modal

```
┌─────────────────────────────────────────────┐
│  📜 Kullanım Şartları ve Politikaları       │
│  ─────────────────────────────────────────   │
│                                              │
│  1. Kabul Edilen Şartlar                     │
│     CoreMusic'e kayıt olarak...              │
│                                              │
│  2. Gizlilik Politikası                      │
│     Kişisel verileriniz...                   │
│                                              │
│  3. Çerez Politikası                         │
│     Bu web sitesi...                         │
│                                              │
│  4. KVKK Aydınlatma                          │
│     6698 sayılı Kanun...                     │
│                                              │
│  ─────────────────────────────────────────   │
│  ┌──────────────┐  ┌──────────────────────┐  │
│  │    Kapat      │  │  Kabul Ediyorum ✓    │  │
│  └──────────────┘  └──────────────────────┘  │
└─────────────────────────────────────────────┘
```

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.kvkk-modal-overlay` | Overlay: rgba(0,0,0,0.5) |
| `.kvkk-modal` | Modal: w:500, max-h:80vh, bg:white, radius:16px |
| `.kvkk-modal__header` | Başlık + kapat butonu |
| `.kvkk-modal__content` | Scrollable içerik |
| `.kvkk-modal__footer` | Kapat + Kabul Et butonları |
| `.kvkk-modal__close` | ✕ kapat butonu |
| `.kvkk-modal__accept` | "Kabul Ediyorum" butonu |

---

## 8. Form Stili

```css
/* ═══ KVKK Checkbox ═══ */
.register-form__kvkk {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  width: 100%;
  margin: 12px 0;
}

.register-form__checkbox {
  width: 16px;
  height: 16px;
  accent-color: #ff4fd8;
  cursor: pointer;
  flex-shrink: 0;
  margin-top: 2px;
}

.register-form__checkbox-label {
  font-size: 12px;
  color: #333;
  line-height: 1.4;
  cursor: pointer;
}

.register-form__checkbox-link {
  color: #ff4fd8;
  text-decoration: underline;
  font-weight: 600;
  cursor: pointer;
}

.register-form__checkbox-link:hover {
  color: #e03cc0;
}

/* ═══ Submit Button — Kayıt Ol ═══ */
.register-form__submit--final {
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

.register-form__submit--final:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(255, 79, 216, 0.3);
}

.register-form__submit--final:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* ═══ Success Animation ═══ */
@keyframes registerSuccess {
  0%   { transform: scale(1); }
  50%  { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.register-form__submit--success {
  animation: registerSuccess 0.3s ease;
  background: linear-gradient(135deg, #22c55e, #16a34a);
}
```

---

## 9. State Durumları

| Durum | Görsel Değişiklik | JS Davranışı |
|-------|-------------------|--------------|
| Default | Input bg:#fff, border:#e0e0e0, checkbox unchecked | Submit disabled |
| Focus | Border:#ff4fd8, box-shadow glow | — |
| Filled | Sol accent korunur | Validation başlar |
| Checkbox Checked | Checkbox ✓ #ff4fd8, Submit enabled | — |
| Checkbox Unchecked | Checkbox empty, Submit disabled | — |
| Error | Border:#ef4444, hata mesajı | aria-describedby |
| Success | Tüm inputlar ✓, buton spinner → "Kayıt Olundu!" | API çağrısı → yönlendirme |
| Loading | Submit spinner, form disabled | API çağrısı |
| KVKK Modal | Overlay + modal, form disabled | Scroll lock |

---

## 10. Kayıt Tamamlama Akışı

```
Step 3 form submit
  → POST /api/auth/register
    → { name, email, password, phone? }
    → Response: { success: true, userId: xxx }
      → Redirect: /home (veya /verify-email)
      → Toast: "Hoş geldiniz! 🎵"
      → Session başlat

Hata durumu:
  → 409 Conflict: "Bu e-posta zaten kayıtlı"
  → 422 Validation: "Geçersiz veri"
  → 500 Server: "Bir hata oluştu, tekrar deneyin"
```

---

## 11. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran form, brand gizli |
| T06-T07 (Tablet) | 768-1024px | Split 50/50 |
| T08 (Embedded) | 1024×600 | Split 60/40 (PNG reference) |
| T12-T16 (Laptop) | 1025-2560px | Split 50/50 |
| T25-T28 (TV) | ≥3840px | Split 50/50, font scale 1.5x |

---

## 12. Adım İlerleme Göstergesi

```
  ┌─────┐       ┌─────┐       ┌─────┐
  │  1  │──────▶│  2  │──────▶│  3  │
  │  ●  │       │  ●  │       │  ●  │
  └─────┘       └─────┘       └─────┘
  Adım 1 ✓      Adım 2 ✓      Adım 3 ●
  (Ad + Email)  (Şifre)       (Telefon)
```

| Step | Durum | Görsel |
|:----:|-------|--------|
| 1 | Tamamlandı | Circle filled #22c55e, ✓ ikonu, line solid #22c55e |
| 2 | Tamamlandı | Circle filled #22c55e, ✓ ikonu, line solid #22c55e |
| 3 | Aktif | Circle filled #ff4fd8, outline glow |

---

## 13. Sosyal Medya Butonları (Tüm Step'ler Ortak)

| Sıra | Platform | Renk | Hover |
|:----:|----------|------|-------|
| 1 | Apple | #000000 bg, white icon | #333 |
| 2 | Google | #ffffff bg, colored icon | #f5f5f5 |
| 3 | Facebook | #1877F2 bg, white icon | #1565c0 |
| 4 | WhatsApp | #25D366 bg, white icon | #1fb855 |
| 5 | Instagram | gradient (pink→purple→orange) | opacity 0.9 |
| 6 | TikTok | #000000 bg, white icon | #333 |
| 7 | Telegram | #0088CC bg, white icon | #006da3 |

**Grid:** 3 sütun, gap:10px, toplam 7 buton (son satırda 1 buton orphan — ortalanmış)

---

## 14. PNG Referansı

| PNG | Viewport | Dosya Yolu | Sıra |
|-----|----------|------------|:----:|
| `Linux 1024 - Register Girl.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 1 |
| `Linux 1024 - Register Girl step 2.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 2 |
| `Linux 1024 - Register Girl step 3.png` | 1024×600 | `.ai/.png/shared-1024/` | Step 3 |

---

## 15. Güvenlik Kontrolleri

| Kontrol | Açıklama |
|---------|----------|
| CSRF Token | `csrf_token` — form submit'te zorunlu |
| Rate Limiting | Kayıt denemesi: max 5/dk, IP bazlı |
| Brute Force | 5 başarısız → 15dk lockout |
| Password Hash | Argon2id, 64MB, 4 iterations, 2 threads |
| Email Verification | Kayıt sonrası doğrulama maili gönder |
| Phone Verification | SMS OTP (opsiyonel) |
| KVKK Compliance | Kayıt öncesi onay zorunlu, audit log |
| XSS Prevention | Input sanitize, output encode |
| SQL Injection | Prepared statement (PDO) |
| Session | Kayıt sonrası secure session başlat |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
