---
title: CoreMusic — WiFi Connect Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Wifi Coonect Light.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[F-quickpanel/wifi]]
---

# CoreMusic — WiFi Connect Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux  1024 - Wifi Coonect Light.png`
**Layout Pattern:** Pattern 4: Sub-Dialog (WiFi Modal içinde)

---

## 1. ASCII WIREFRAME

```
┌─ SUB-DIALOG (~350×200px, WiFi modal içinde) ──────────────────────────────────────────────┐
│                                                                                            │
│  Bayram Ali - WiFi  [📶]                                                                  │
│  5GHz · Mükemmel sinyal · 100% · Güvenli Bağlantı                                       │
│                                                                                            │
│  Kablosuz Ağ Şifresi                                                                      │
│  ┌──────────────────────────────────────────────────────────────────────────────────┐     │
│  │ ●●●●●●●●                                                                          │     │
│  │ (C06 form input, pembe border, şifre gizli)                                     │     │
│  └──────────────────────────────────────────────────────────────────────────────────┘     │
│                                                                                            │
│  ☑ Kablosuz ağa her zaman otomatik bağlan  (checkbox)                                     │
│                                                                                            │
│  [İptal] (C05, sınır)  [Bağlan] (C04, pembe)                                            │
│                                                                                            │
└────────────────────────────────────────────────────────────────────────────────────────────┘

Glass efekti, backdrop-filter blur
```

---

## 2. DETAYLAR

| Özellik | Değer |
|---------|-------|
| Ağ adı | Bayram Ali - WiFi |
| Sinyal | 5GHz · Mükemmel sinyal · 100% · Güvenli Bağlantı |
| Şifre input | C06, ~300×56px, pembe border |
| Otomatik bağlan | Checkbox, 12px |
| İptal | C05, ~80×48px |
| Bağlan | C04, ~120×56px, pembe |

---

## 3. WCAG

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 48px, 56px |
| Touch target (checkbox) | ⚠️ ~16px → 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

*WiFi Connect Screen Spec v2.0.0 — CoreMusic UI Design System*
*Last Updated: 2026-08-11*
