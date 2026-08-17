---
title: CoreMusic — Register Step 2-3 Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Register Girl step 2.png + step 3.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/05-auth-screen]]
  - [[A-auth/register-step1]]
---

# CoreMusic — Register Step 2-3 Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Images:** `Linux  1024 - Register Girl step 2.png` + `Linux  1024 - Register Girl step 3.png`
**Layout Pattern:** Pattern 5: Auth Screen (78/22 split)

---

## 1. STEP 2 — ŞİFRE

```
┌── SAĞ PANEL (~22%) ─────────────────────────────────────┐
│                                                            │
│  [女神 ikonu]                                              │
│                                                            │
│  Hesap Oluştur                                            │
│                                                            │
│  Şifre                                                     │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: password)       │                    │
│  └───────────────────────────────────┘                    │
│  Şifre Tekrar                                              │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: password)       │                    │
│  └───────────────────────────────────┘                    │
│                                                            │
│  [Devam Et] (C04, pembe)                                  │
│                                                            │
│  [🍎][G][f][💬][📷][🎵][🎤]                                │
│  Hesabın yok mu?  Kayıt Ol                                │
└──────────────────────────────────────────────────────────┘

Adım 2/3: Şifre + Şifre Tekrar
```

---

## 2. STEP 3 — TELEFON + KAYIT

```
┌── SAĞ PANEL (~22%) ─────────────────────────────────────┐
│                                                            │
│  [女神 ikonu]                                              │
│                                                            │
│  Hesap Oluştur                                            │
│                                                            │
│  Telefon                                                    │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: tel)            │                    │
│  └───────────────────────────────────┘                    │
│                                                            │
│  ☑ Gizlilik Politikası ve Kullanım şartlarını             │
│    kabul ediyorum  (checkbox, 11px)                        │
│                                                            │
│  [Kayıt Ol] (C04, pembe, full-width)                      │
│                                                            │
│  [🍎][G][f][💬][📷][🎵][🎤]                                │
│  Hesabın yok mu?  Kayıt Ol                                │
└──────────────────────────────────────────────────────────┘

Adım 3/3: Telefon + KVKK onayı + Kayıt Ol
```

---

## 3. FORM DETAYLARI

### Step 2

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Şifre | password | 56px | Min 8 karakter |
| Şifre Tekrar | password | 56px | Eşleşme kontrolü |
| Devam Et | button | 56px | Pembe |

### Step 3

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Telefon | tel | 56px | +90 XXX XXX XX XX |
| KVKK | checkbox | — | Zorunlu onay |
| Kayıt Ol | button | 56px | Pembe, son adım |

---

## 4. DAVRANIŞ

```
Step 1 → "Devam Et" → Step 2
Step 2 → "Devam Et" → Step 3 (şifre eşleşmeli)
Step 3 → "Kayıt Ol" → Select Gender sayfasına yönlendirme
```

---

## 5. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 56px |
| Touch target (checkbox) | ⚠️ ~16px → 44px |
| Focus indicator | ✅ |
| Error messaging | ⚠️ EKSİK |
| Password strength | ⚠️ EKSİK |

---

*Register Step 2-3 Screen Spec v2.0.0 — CoreMusic UI Design System*
*Last Updated: 2026-08-11*
