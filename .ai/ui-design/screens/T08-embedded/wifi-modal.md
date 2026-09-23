---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — WiFi Modal Screen Specification"
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
  authority: ".ai/ui-design/screens/T08-embedded/wifi-modal.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png"
---

# CoreMusic — WiFi Modal (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HOME DASHBOARD (ARKA PLAN — Bulanık) ────────────────────────────────────────────────────────┐  │
│ y:600└────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│     ┌─── MODAL OVERLAY (bg: rgba(0,0,0,0.5)) ────────────────────────────────────────────────────────┐  │
│     │                                                                                                  │  │
│     │     ┌─── WIFI MODAL (w:480, h:380, center, glass) ──────────────────────────────────────────┐   │  │
│     │     │                                                                                      │   │  │
│     │     │  📶 Wi-Fi                          Wi-Fi Ağlanları                                   │   │  │
│     │     │  Wi-Fi ⟷ (toggle: pembe)                                                            │   │  │
│     │     │                                                                                      │   │  │
│     │     │  ─── Bağlı Olduğu Ağ ───                                                            │   │  │
│     │     │  ┌──────────────────────────────────────────────────────────────────────────────┐     │   │  │
│     │     │  │ 📶 Bayram Ali Home  🟢Mevcut  🟣WPA2  🟡5GHz │  Bağlantıyı Kes  │ (buton) │     │   │  │
│     │     │  │    5GHz · Wikelileriology · 100% · 2.168.1.80                              │     │   │  │
│     │     │  └──────────────────────────────────────────────────────────────────────────────┘     │   │  │
│     │     │                                                                                      │   │  │
│     │     │  ─── Kullanılabilir Ağlar ───                                                        │   │  │
│     │     │  ┌──────────────────────────────────────────────────────────────────────────────┐     │   │  │
│     │     │  │ 📶 Bayram Ali Home  🟢Mevcut  🟣WPA2  🟡5GHz  │  Bağlan │ (pembe buton)  │     │   │  │
│     │     │  │    5GHz · Wikelileriology · M100% · Güzeldi Bağlanın                       │     │   │  │
│     │     │  ├──────────────────────────────────────────────────────────────────────────────┤     │   │  │
│     │     │  │ 📶 Bayram Ali Home  🟣WPA2  🟡5GHz  │  Bağlan │ (pembe buton)             │     │   │  │
│     │     │  │    5GHz · Wikelileriology · M100% · Güzeldi Bağlanın                       │     │   │  │
│     │     │  ├──────────────────────────────────────────────────────────────────────────────┤     │   │  │
│     │     │  │ 📶 Bayram Ali Home  🔴WPA3  🟣WPA2  🟡5GHz  │  Bağlan │ (pembe buton)    │     │   │  │
│     │     │  │    5GHz · Wikelileriology · M100% · Güzeldi Bağlanın                       │     │   │  │
│     │     │  └──────────────────────────────────────────────────────────────────────────────┘     │   │  │
│     │     │                                                                                      │   │  │
│     │     └──────────────────────────────────────────────────────────────────────────────────────┘   │  │
│     │                                                                                                  │  │
│     └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.wifi-modal` | Modal container (w:480, glass) |
| `.wifi-modal__header` | Başlık + toggle |
| `.wifi-modal__toggle` | Wi-Fi açma/kapama toggle |
| `.wifi-modal__connected` | Bağlı ağ bölümü |
| `.wifi-modal__available` | Kullanılabilir ağlar |
| `.wifi-network` | Ağ satırı |
| `.wifi-network__icon` | Sinyal ikonu |
| `.wifi-network__name` | Ağ adı |
| `.wifi-network__badges` | Badge'ler (Mevcut, WPA2, 5GHz) |
| `.wifi-network__signal` | Sinyal gücü |
| `.wifi-network__info` | Frekans, şifreleme, hız |
| `.wifi-network__action` | Bağlan / Bağlantıyı Kes butonu |
| `.wifi-network--connected` | Bağlı ağ (highlight) |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-bg-modal` | rgba(255,255,255,0.95) |
| `--cm-badge-green` | #22c55e |
| `--cm-badge-purple` | #a855f7 |
| `--cm-badge-yellow` | #eab308 |
| `--cm-badge-red` | #ef4444 |
| `--cm-radius-md` | 12px |
| `--cm-spacing-md` | 16px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Wifi Quick Page Base.png` | 1024×600 |
| `Linux 1024 - Wifi Connect Light.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
