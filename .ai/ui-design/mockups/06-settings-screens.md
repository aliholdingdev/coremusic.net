---
title: "CoreMusic — Settings Screen Mockups"
type: reference
category: ui-design/mockups
date: 2026-08-19
updated: 2026-08-19
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
parent: "[[00-mockup-index]]"
screens:
  - "PNG #10 — WiFi Modal"
  - "PNG #11 — WiFi Bağlan"
  - "PNG #12 — Bluetooth Modal"
png_source: ".ai/.png/home-1024/"
---

# Settings Screen Mockups

**3 PNG — home-1024/ dizininde.** WiFi, Bluetooth ve ayarlar ekranları.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Settings Ekran Envteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 10 | **WiFi Modal** | `Linux  1024 - Wifi Qucik Page Base.png` | overlay | Pattern 4: Modal | `04_Components/c-modal.css` |
| 11 | **WiFi Bağlan** | `Linux  1024 - Wifi Coonect Light.png` | overlay (sub-dialog) | Pattern 4: Modal | `04_Components/c-modal.css` |
| 12 | **Bluetooth Modal** | `Linux  1024 - Bluethoot Qucik Page Base.png` | overlay | Pattern 4: Modal | `04_Components/c-modal.css` |

---

## ASCII Art — Settings Ekranları

### WIFI MODAL — PNG #10

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — bulanık]                                                                              │
│ [ANA SAYFA — bulanık, backdrop-filter: blur(4px), rgba(0,0,0,0.5)]                            │
│                                                                                                 │
│     x:320                                                                                       │
│ y:130 ┌─────────────────────────────────────────────────────────────────────────────────────┐   │
│        │ 📶 Wi-Fi                                                                           │   │
│        │ Ağ Bağlantıları                                                                   │   │
│        │                                                                                    │   │
│        │ Wi-Fi  [━━━━━━○ toggle, 50×28px]                                                  │   │
│        │                                                                                    │   │
│        │ Bağlı Olan Ağ                                                                      │   │
│        │ ┌────────────────────────────────────────────────────────────────────────────┐     │   │
│        │ │ [📶] Bayram Ali Home [Güçlü] 5GHz · Mükemmel · 100%     [Bağlantıyı Kes] │     │   │
│        │ │  ↑       ↑              ↑        ↑      ↑              ↑                   │     │   │
│        │ │  icon    name          badge   freq   signal          btn                  │     │   │
│        │ └────────────────────────────────────────────────────────────────────────────┘     │   │
│        │                                                                                    │   │
│        │ Kullanılabilir Ağlar                                                                │   │
│        │ ┌────────────────────────────────────────────────────────────────────────────┐     │   │
│        │ │ [📶] Bayram Ali Home [Güçlü] 5GHz · Mükemmel · 100%      [Bağlan]        │     │   │
│        │ │ [📶] Bayram Ali Home [Orta]  2.4GHz · İyi · 80%           [Bağlan]        │     │   │
│        │ │ [📶] Bayram Ali Home [Zayıf] [Gizli] 2.4GHz · Orta · 60%  [Bağlan]       │     │   │
│        │ └────────────────────────────────────────────────────────────────────────────┘     │   │
│        │                                                                                    │   │
│ y:470  └────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ Modal: glass efekti, ~380×340px, backdrop-filter: blur(20px) saturate(180%)                   │
│ Badge renkleri: Güçlü=#22c55e, Orta=#eab308, Zayıf=#ef4444                                    │
│ Badge: pembe arka plan (Güçlü, Orta, Gizli)                                                   │
│ C16 Network Row satırları: ~48px yükseklik                                                    │
│ Toggle: WiFi açma/kapama, pembe track                                                         │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### WIFI BAĞLAN — PNG #11

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — bulanık]                                                                              │
│ [ANA SAYFA — bulanık]                                                                           │
│                                                                                                 │
│     x:320                                                                                       │
│ y:230 ┌─────────────────────────────────────────────────────────────────────────────────────┐   │
│        │ Bayram Ali - WiFi 📶                                                               │   │
│        │                                                                                    │   │
│        │ 5GHz · Mükemmel sinyal · 100% · Güçlü Bağlantı                                   │   │
│        │                                                                                    │   │
│        │ Kablosuz Ağ Şifresi                                                                │   │
│        │ ┌────────────────────────────────────────────────────────────────────────────┐     │   │
│        │ │ ●●●●●●●●  (C06 Form Input, 56px yükseklik)                                │     │   │
│        │ └────────────────────────────────────────────────────────────────────────────┘     │   │
│        │                                                                                    │   │
│        │ ☐ Kablosuz ağa her zaman otomatik bağlan                                          │   │
│        │                                                                                    │   │
│        │                          [İptal] (C05)  [Bağlan] (C04, pembe)                     │   │
│ y:370  └────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ Modal: ~380×140px, glass efekti                                                               │
│ Sub-dialog: WiFi modal üzerine bindirme                                                       │
│ Input: pembe arka plan (focus durumunda)                                                      │
│ Butonlar: İptal (sınır) + Bağlan (pembe, C04)                                                │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### BLUETOOTH MODAL — PNG #12

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — bulanık]                                                                              │
│ [ANA SAYFA — bulanık, backdrop-filter: blur(4px), rgba(0,0,0,0.5)]                            │
│                                                                                                 │
│     x:320                                                                                       │
│ y:130 ┌─────────────────────────────────────────────────────────────────────────────────────┐   │
│        │ ✳ Bluetooth                                                                        │   │
│        │ Cihaz Bağlantıları                                                                 │   │
│        │                                                                                    │   │
│        │ Bluetooth  [━━━━━━○ toggle, 50×28px]                                              │   │
│        │                                                                                    │   │
│        │ Bağlı Olan Cihaz                                                                   │   │
│        │ ┌────────────────────────────────────────────────────────────────────────────┐     │   │
│        │ │ [✳] Kim - 50 [A2DP]  Tarayıcı · Pil: Dolu · 100%     [Bağlantıyı Kes]   │     │   │
│        │ └────────────────────────────────────────────────────────────────────────────┘     │   │
│        │                                                                                    │   │
│        │ Kullanılabilir Cihazlar                                                             │   │
│        │ ┌────────────────────────────────────────────────────────────────────────────┐     │   │
│        │ │ [✳] Kim - 50 [A2DP][HFP] Tarayıcı · Pil: Dolu · 100%     [Bağlan]       │     │   │
│        │ │ [✳] Car BT [A2DP][Müzik]   Tarayıcı · Pil: Dolu · 100%     [Bağlan]       │     │   │
│        │ │ [✳] Samsung TV [A2DP][HFP] Televizyon · Pil: Dolu · 100%   [Bağlan]       │     │   │
│        │ └────────────────────────────────────────────────────────────────────────────┘     │   │
│        │                                                                                    │   │
│ y:470  └────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ Modal: ~380×340px, glass efekti                                                               │
│ Badge renkleri: A2DP=pembe, HFP=mor, Müzik=yeşil                                             │
│ C16 Device Row satırları: ~48px yükseklik                                                    │
│ Toggle: Bluetooth açma/kapama, pembe track                                                   │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[01-auth-screens]] | Auth ekranları |
| [[02-home-screens]] | Home ekranları |
| [[03-music-screens]] | Music ekranları |
| [[04-player-screens]] | Player ekranları |
| [[05-filemanager-screens]] | FileManager ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*Settings Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
