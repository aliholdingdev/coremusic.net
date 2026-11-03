---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Browse (Dosya Yöneticisi / Disk) Screen Specification (Linux 4K)"
type: spec
category: ui-design
date: 2026-10-28
updated: 2026-10-28
status: active
version: 1.0.0
tier: T20
viewport: 3840x2160
device: "27\" 4K Monitor (Desktop)"
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T20-monitor-4k/browse.md"
  source_of_truth: ".ai/.png/home-1920/Linux 1920 -  Göz At Page.png (4K T20 ölçek uyarlaması) · ⚠️ VERIFICATION REQUIRED — 3840 Göz At PNG bekleniyor"
---

# CoreMusic — Browse (T20 Desktop Monitor 3840×2160)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-3840, y:0-2160)

````
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                          x:3840 │
│ y:0 ┌─── NAVBAR (w:993 h:27 @x12,y17) ───────────────────────────────────────────────────────────────┐               │
│     │ logo 108×29 @20,20 · link: Ana Sayfa·Keşfet·Albümler·Sanatçılar·Göz At·Geçmiş·Ayarlar·Hakkımızda│               │
│     │ sağ: Profile 90×24 @x1649 · Wifi/BT · Battery · Logout                                                                 │
│ y:60└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:89┌─── DOSYA YÖNETİCİSİ / DISK (w:1612 h:879 @x24,y89) ─────────────┐  ┌─ SAĞ PANEL (w:219) ────────┐│  DEKORATİF    │
│     │ Actions Btns (w:481 h:40 @x24,y89) — geri + başlık             │  │ Player İnfo Div 218×114    ││  BOŞ ZEMİN    │
│     │ "Sistem Diskleri" TEXT @y175                                    │  │ Player Status Div-pi       ││  (x1920-3840  │
│     │   [System Disk 160×44] [NAS Drive 160×44]  (pitch 209)         │  │ Sağ Panel GROUP 219×420    ││  arası manzara│
│     │ "HDD & SSD Diskleri" TEXT @y275                                │  │  Avatar 90×24 + başlık     ││  fotoğrafı —  │
│     │   [HDD Drive] [SSD Nvme-2] [SSD Drive]  (pitch 209)            │  │  Önerilen 2×4 avatar       ││  4K genişlik  │
│     │ "USD Diskleri" TEXT @y369                                      │  │  Şimdi Çalıyor             ││  boyunca      │
│     │   [USB Drive] [USB Drive] [CD DVD Drive]  (pitch 209)          │  │                            ││  devam eder)  │
│ y:968└───────────────────────────────────────────────────────────────┘  └────────────────────────────┘│               │
│ y:968├─── EMPTY (h1100) — dekoratif boş zemin (bg + gradient) ────────────────────────────────────────┤               │
│ y:2068└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:2068┌─── FOOTER PLAYER (w:3840 h:92 @x0,y2068 — TAM GENİŞLİK) ──────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 3838×4.8 · Player btn 126×27 @x1790 · Volume 353×14 @x3420 · "350 kpps"                     ││
│ y:2160└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 908 (y60-968) + dekoratif boş zemin 1100 (y968-2068) + footer 92 (y2068-2160) = 2160 ✓

**Yerleşim & 4K Davranışı:**
- 4K Monitor (T20) mimarisinde içerik sol-üste 1:1 ölçekle yerleşir (`--layout-scale-4k: 0.85`), sağ ve alt bölgeler manzara zemin görseliyle kaplanır.
- Footer player 3840 px tam ekran genişliğinde çalışır.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envanter |
|-------|-------|--------------------|----------|
| Sayfa Kabuğu | `.page-browse` | `--wide`, `--4k` | C01 |
| Cam Panel | `.browse-glass` | `__header`, `__body` | C02 |
| Disk Grubu | `.disk-group` | `__title`, `__grid` | C03 |
| Disk Butonu | `.disk-btn` | `__icon`, `__name`, `__capacity`, `--active` | C04 |
| Sağ Panel | `.player-info` | `__avatar`, `__title` | C11 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu rengi | `--cm-primary` | `#ff4fd8` |
| Glass arka plan | `--cm-glass-bg` | `rgba(255,255,255,.05)` |
| Glass kenarlık | `--cm-glass-border` | `rgba(255,255,255,.1)` |
| Blur | `--cm-glass-blur` | `blur(12px)` |
| Font başlık | `--cm-font-display` | `var(--cm-font-display)` |
| Font gövde | `--cm-font-body` | `var(--cm-font-body)` |
| 4K Footer | `--footer-h` | `90px` |
| 4K Header | `--header-h` | `65px` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Disk Butonları | 160×44 px | 4K 10ft / masaüstü mouse tıklaması için rahat alan |
| Geri/Aksiyon | 40×40 px | Minimum dokunma sınırını karşılar |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | PASS — Cam panel üstü açık metinler |
| 2 | Odak görünür | 2px outline ≥3:1 | PASS — `:focus-visible` ile `--cm-primary` |
| 3 | Dokunma hedefi | ≥24px mouse, ≥56px 10ft | PASS |
| 4 | Okuma sırası | Görsel = DOM | PASS — Navbar → Disk Grupları → Sağ Panel → Footer |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Göz At Page.png` (4K T20 ölçek uyarlaması)
- **4K Home Referansı:** `.ai/.png/home-3840/Linux 3840 - Home.png`
- **Figma node:** `3155:18252` (frame) · `3155:18261` (Fiel Manager GROUP)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| ≥2561px (4K) | Sol-üst 1:1 yerleşim, tam genişlik footer (3840×92), `--layout-scale-4k: 0.85` | T20 tier |
| 1920-2560px | T17 Wide tier sahne yerleşimi | T17 tier |
| <1600px | T07 Fallback (`browse--embedded`) | DeviceManager |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.8` | — |
| Hover | mouse üstüne gelme | `opacity:1` + border `--cm-primary` | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | disk seçimi | `--cm-primary` aktif kenarlık | `aria-pressed="true"` |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-10-28  
**Mode:** Red Team · Human Mode · Truth Mode
