---
title: "WiFi Connect Dialog — Quick Reference"
type: ascii-qr
screen_id: "S12"
resolution: "1024x600"
layout_pattern: "Modal"
components: [C04, C06, C14]
png: "home-1024/Linux  1024 - Wifi Coonect Light.png"
full_spec: "F-quickpanel/wifi-connect.md"
---

# WiFi Connect Dialog

## Layout Wireframe

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

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Sub-dialog | WiFi modal üzerine bindirme | ~380×140px | — |
| Overlay | tam ekran | rgba(0,0,0,0.5) | `--overlay-bg` |
| Modal blur | — | blur(20px) | `--glass-blur` |
| Şifre input | form içinde | ~300×56px | `--input-h-lg` |
| İptal butonu | sol alt | ~80×48px | — |
| Bağlan butonu | sağ alt | ~120×56px | — |
| Checkbox | input altında | 12px | — |
| Touch target | — | ≥48px | `--touch-min` |
| Input focus border | — | pembe | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C04 | `.wifi-connect` | WiFi modal üzerine bindirme | ~380×140px |
| C04 | `.wifi-connect__connect` | sağ alt | ~120×56px, pembe |
| C06 | `.wifi-connect__input` | form içinde | ~300×56px, pembe border |
| C14 | WiFi modal arka plan | — | backdrop-filter blur |

## Form Fields

| Alan | Özellik | Değer |
|------|---------|-------|
| Başlık | Ağ adı + detay | Bayram Ali - WiFi, 5GHz · Mükemmel sinyal · 100% |
| Şifre input | C06, pembe border | ~300×56px, şifre gizli (●) |
| Otomatik bağlan | Checkbox | 12px, label: "Kablosuz ağa her zaman otomatik bağlan" |
| İptal | C05, sınır buton | ~80×48px |
| Bağlan | C04, pembe buton | ~120×56px |

## WCAG

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 48px, 56px |
| Touch target (checkbox) | ⚠️ ~16px → 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

## CSS Hints

```css
.wifi-connect {
  width: 380px;
  background: var(--modal-bg);
  backdrop-filter: var(--modal-blur) var(--modal-saturate);
  border: var(--modal-border);
  border-radius: var(--modal-radius);
  padding: var(--space-4);
}
.wifi-connect__input {
  width: 100%;
  min-height: 56px;
  padding: var(--input-padding-y) var(--input-padding-x);
  background: var(--input-bg);
  border: var(--input-border);
  border-radius: var(--input-radius);
}
.wifi-connect__input:focus {
  border: var(--input-focus-border);
  box-shadow: 0 0 0 3px var(--accent-bg);
}
.wifi-connect__auto {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  min-height: 44px;
}
```
