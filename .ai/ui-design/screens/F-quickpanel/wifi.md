---
title: CoreMusic — WiFi Modal Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Wifi Qucik Page Base.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/04-modal]]
---

# CoreMusic — WiFi Modal Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Wifi Qucik Page Base.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 4: Modal Overlay
**Rota:** overlay (header'daki WiFi ikonuna tıklama)

---

## 1. ASCII WIREFRAME

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

---

## 2. MODAL DETAYLARI

### 2.1 — Başlık

| Özellik | Değer |
|---------|-------|
| İkon | 📶 (WiFi ikonu, 24×24px) |
| Başlık | "Wi-Fi" (16px, 600) |
| Alt başlık | "Ağ Bağlantıları" (12px, 400, muted) |

### 2.2 — WiFi Toggle (C15)

| Özellik | Değer |
|---------|-------|
| Durum | Açık (pembe track) |
| Boyut | ~50×28px |
| Konum | Başlığın sağında |

### 2.3 — Ağ Satırları (C16)

**Bağlı Olan Ağ:**
| Özellik | Değer |
|---------|-------|
| İkon | 📶 (24×24px) |
| İsim | Bayram Ali Home |
| Badge'ler | [Güçlü] (yeşil), [5GHz] (pembe) |
| Detay | 5GHz · Mükemmel sinyal · 100% |
| Buton | [Bağlantıyı Kes] (C05, pembe) |

**Kullanılabilir Ağlar:**
| Özellik | Değer |
|---------|-------|
| İsim | Bayram Ali Home |
| Badge'ler | [Güçlü/Orta/Zayıf] + [5GHz/2.4GHz] |
| Sinyal gücü | -55BS / -70BS / -85BS |
| Buton | [Bağlan] (C05, sınır) |

---

## 3. BİLEŞEN KULLANIMI

| Bileşen | ID | Sayı |
|---------|-----|------|
| Modal | C14 | 1 |
| Toggle | C15 | 1 |
| Network Row | C16 | ~4 |
| Secondary Button | C05 | ~4 |

---

## 4. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (toggle) | ⚠️ ~28px |
| Touch target (buton) | ✅ 48px |
| Touch target (satır) | ✅ 48px |
| Touch target (kapat) | ✅ 44px |
| Focus indicator | ✅ |
| Escape ile kapatma | ✅ |
| ARIA | ✅ `role="dialog"` |

---

*WiFi Modal Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
