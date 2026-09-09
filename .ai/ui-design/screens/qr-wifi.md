---
title: "WiFi Modal - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S11"
resolution: "1024x600"
layout_pattern: "Modal"
components: [C01, C04, C14, C16]
png: "home-1024/Linux  1024 - Wifi Qucik Page Base.png"
full_spec: "F-quickpanel/wifi.md"
---

# WiFi Modal

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 4: Modal Overlay                                                            │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [HEADER — arka plan bulanık]                                                                    │
│ [ANA SAYFA — arka plan bulanık, backdrop-filter: blur(4px)]                                    │
│                                                                                                  │
│ ┌─ MODAL (~400×350px, merkez) ─────────────────────────────────────────────────────────────┐   │
│ │                                                                                            │  │
│ │  [📶 icon] Wi-Fi                                                                          │  │
│ │  Ağ Bağlantıları                                                                         │  │
│ │                                                                                            │  │
│ │  Wi-Fi  [━━━━━━○] (C15 toggle — pembe, aktif)                                           │  │
│ │                                                                                            │  │
│ │  Bağlı Olan Ağ                                                                           │  │
│ │  ┌──────────────────────────────────────────────────────────────────────────────────┐     │  │
│ │  │ [📶] Bayram Ali Home [Güçlü][5GHz] 5GHz · Mükemmel sinyal · 100%  [Bağlan]    │     │  │
│ │  └──────────────────────────────────────────────────────────────────────────────────┘     │  │
│ │                                                                                            │  │
│ │  Kullanılabilir Ağlar                                                                     │  │
│ │  ┌──────────────────────────────────────────────────────────────────────────────────┐     │  │
│ │  │ [📶] Bayram Ali Home [Güçlü]  5GHz · Mükemmel · -55BS              [Bağlan]    │     │  │
│ │  │ [📶] Bayram Ali Home [Orta]   5GHz · İyi · -70BS                   [Bağlan]    │     │  │
│ │  │ [📶] Bayram Ali Home [Zayıf]  2.4GHz · Orta · -85BS                [Bağlan]    │     │  │
│ │  └──────────────────────────────────────────────────────────────────────────────────┘     │  │
│ │                                                                                            │  │
│ │  Kapat: backdrop click veya ✕ butonu (sağ üst, 44×44px)                                 │  │
│ └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│ Glass efekti: backdrop-filter: blur(20px) saturate(180%)                                       │
│ Modal border-radius: ~16px                                                                      │
│ Modal border: 1px solid rgba(255,255,255,0.1)                                                  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Modal | merkez | ~380×340px | — |
| Overlay | tam ekran | 100%×100% | `--overlay-bg` |
| Overlay blur | — | blur(4px) | `--overlay-blur` |
| Modal blur | — | blur(20px) | `--glass-blur` |
| WiFi toggle | modal başlık | 50×28px | `--toggle-w, --toggle-h` |
| Ağ satır yüksekliği | — | ~48px | `--network-row-h` |
| WiFi ikonu | satır içi | 24×24px | — |
| Kapat butonu | sağ üst | 44×44px | — |
| Touch target | — | ≥48px | `--touch-min` |
| Modal border-radius | — | ~16px | — |
| Modal border | — | 1px solid rgba(255,255,255,0.1) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.header` | arka plan (bulanık) | 100%×60px |
| C04 | `.footer` | arka plan (bulanık) | 100%×90px |
| C14 | `.wifi-modal` | merkez overlay | ~380×340px |
| C15 | `.wifi-toggle` | modal başlık sağında | 50×28px |
| C16 | `.wifi-network` | modal içinde | 100%×48px satır |

## Network Row Structure

| Özellik | Değer |
|---------|-------|
| İkon | 📶 (24×24px) |
| İsim | Ağ adı |
| Badge'ler | [Güçlü/Orta/Zayıf] (yeşil/sarı/kırmızı) + [5GHz/2.4GHz] (pembe) |
| Detay | Frekans · Sinyal gücü · Oran |
| Buton | [Bağlan] (C05, sınır) veya [Bağlantıyı Kes] (C05, pembe) |

## WiFi Toggle States

| Durum | Görünüm |
|-------|---------|
| Açık | Pembe track, beyaz thumb sağda |
| Kapalı | Gri track, beyaz thumb solda |

## CSS Hints

```css
.wifi-modal {
  width: 380px;
  max-height: 80vh;
  background: var(--modal-bg);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
}
.wifi-modal__body {
  padding: var(--space-4);
  overflow-y: auto;
  max-height: calc(80vh - 60px);
}
.wifi-section__title {
  font-size: var(--text-sm);
  color: var(--white-70);
  margin-bottom: var(--space-2);
}
```

---

*QR WiFi v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
