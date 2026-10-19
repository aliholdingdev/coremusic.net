---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Browse (Dosya Yöneticisi / Disk) Screen Specification"
type: spec
category: ui-design
date: 2026-10-18
updated: 2026-10-18
status: active
version: 1.0.0
tier: T17
viewport: 1920x1080
device: "22\" FHD Monitor (Desktop)"
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/browse.md"
  source_of_truth: ".ai/.png/home-1920/Linux 1920 -  Göz At Page.png"
---

# CoreMusic — Browse (T17 Desktop Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

````
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                    x:1920 │
│ y:0 ┌─── NAVBAR (w:993 h:27 @x12,y17) ─────────────────────────────────────────────────────────────────┐        │
│     │ logo 108×29 @20,20 · link: Ana Sayfa·Keşfet·Albümler·Sanatçılar·Göz At·Geçmiş·Ayarlar·Hakkımızda │        │
│     │ sağ: Profile 90×24 @x1649 · Wifi/BT · Battery · Logout                                                                  │
│ y:60└───────────────────────────────────────────────────────────────────────────────────────────────────┘        │
│ y:89┌─── DOSYA YÖNETİCİSİ / DISK (w:1612 h:879 @x24,y89) ─────────────┐  ┌─ SAĞ PANEL (w:219 @x1654,y89) ────┐│
│     │ Actions Btns (w:481 h:40 @x24,y89) — geri + başlık             │  │ Player İnfo Div 218×114 @y89       ││
│     │ "Sistem Diskleri" TEXT @y175                                    │  │ Player Status Div-pi 224×316 @y390 ││
│     │   [System Disk 160×44] [NAS Drive 160×44]  (pitch 209)         │  │ Sağ Panel GROUP 219×420           ││
│     │ "HDD & SSD Diskleri" TEXT @y275                                │  │                                   ││
│     │   [HDD Drive] [SSD Nvme-2] [SSD Drive]  (pitch 209)            │  │  Avatar 90×24 + başlık            ││
│     │ "USD Diskleri" TEXT @y369                                      │  │  Önerilen 2×4 avatar              ││
│     │   [USB Drive] [USB Drive] [CD DVD Drive]  (pitch 209)          │  │  Şimdi Çalıyor                    ││
│     │ disk butonu: 160×44 · kapasite progress bar alt                │  │                                   ││
│ y:968└───────────────────────────────────────────────────────────────┘  └───────────────────────────────────┘│
│ y:988┌─── FOOTER PLAYER (w:1923 h:92 @x0,y988) ──────────────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 1921×4.8 · Player btn 126×27 @x899 · Volume 353×14 @x1533 · bitrate "350 kpps"      ││
│ y:1080└────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 928 (y60-988) + footer 92 (y988-1080) = 1080 ✓

**Figma grup** (`3155:18261` "Fiel Manager", 1612×879 @x2949,y-487 → frame-rel x24,y89):
- `Sistem Diskleri` TEXT 87×16 @y175 · 2× `Disk Buttons` INSTANCE **160×44** @x58/267 (frame-rel), y219
- `HDD & SSD Diskleri` TEXT 113×16 @y275 · 3× `Disk Buttons` @x58/267/476, y317
- `USD Diskleri` TEXT 73×16 @y369 · 3× `Disk Buttons` @x58/267/476, y415
- `Actions Btns` FRAME 481×40 @x24,y110
- Sağ panel: `Player İnfo Div` 218×114 · `Player Status Div-pi` 224×316 · `Sağ Panel` GROUP 219×420

> ⚠️ ÇELİŞKİ (§1-1): PNG'de disk butonları `System Disk`, `NAS Drive`, `HDD Drive`, `SSD Nvme-2 Drive`, `SSD Drive`, `USB Drive`, `USB Drive`, `CD DVD Drive` (8 adet, 3 satırda 2+3+3). Figma'da `Disk Buttons` INSTANCE sayısı: Sistem 2, HDD&SSD 3, USB 3 = 8 ✓ eşleşir. Sorun yok.

**Lejant:** `[PNG]` = PNG'den okundu · `[Figma]` = Figma node ölçüsü · `[türetilmiş]` = çıkarım.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar | `.nav-link` | `__logo`, `__nav`, `--active` | C01 |
| Başlık | `.page-title` | `__back`, `__text` | C04 |
| Disk Bölümü | `.disk-section` | `__label`, `__grid` | C03 |
| Disk Butonu | `.disk-btn` | `__icon`, `__name`, `__capacity`, `--active` | C04 |
| Kapasite | `.capacity-bar` | `__fill` | C15 |
| Sağ Panel | `.player-info` | `__avatar`, `__title` | C11 |
| Önerilen | `.recommended` | `__row`, `__avatar` | C11 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |

> `.disk-section` / `.disk-btn` envanterde karşılığı yok → ekran özel.

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu / dolgu | `--cm-primary` | `#ff4fd8` |
| Glass arka plan | `--cm-glass-bg` | `rgba(255,255,255,.05)` |
| Glass kenarlık | `--cm-glass-border` | `rgba(255,255,255,.1)` |
| Blur | `--cm-glass-blur` | `blur(12px)` |
| Kart yarıçapı | `--cm-card-radius` | `var(--cm-card-radius)` |
| Gölge | `--cm-shadow-md` | `var(--cm-shadow-md)` |
| Font başlık | `--cm-font-display` | `var(--cm-font-display)` |
| Progress dolgu | `--cm-primary-60` | `rgba(255,79,216,.6)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T17) | 32×32 px (öneri) | Hover affordans |
| Disk butonu | 160×44 px | ✓ yeterli |
| Nav link | ≥24×24 px | padding ile |
| Footer player butonları | 27px | ≥24 ✓ |
| Yakınlık kuralı | ≥8 px boşluk | Disk butonları arası 49px boşluk |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | GAP — görsel zemin üstü, ölçüm gerekli |
| 2 | Odak görünür | 2px outline ≥3:1 | GAP — focus state PNG'de yok |
| 3 | Dokunma hedefi | ≥24px mouse | PASS — disk butonları 44px |
| 4 | Okuma sırası | Görsel = DOM | PASS — navbar → başlık → disk bölümleri → panel → footer |
| 5 | Durum renkle değil | İkon + metin | PASS — disk ikonu + ad + kapasite bar |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |
| Fallback | `background: rgba(255,255,255,.25)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Göz At Page.png`
- **Klasör:** `home-1920/` (4 PNG) · 1024 karşılığı: `.ai/.png/home-1024/Linux  1024 - Göz At Page.png`
- **Mockup indeksi:** [[../01-mockup-index]]
- **Figma node:** `3155:18252` (frame) · `3155:18261` (Fiel Manager GROUP)
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| 1920-2560px | Sabit 1920 sahne; max-width 1920 | T17 tier |
| Disk grid | 2+3+3 düzeni; sığmazsa alt satıra akış | PNG |
| <1600px | T17 kapsam dışı → T07 fallback (`browse`) | §12 fallback |
| Portre | N/A (landscape-only) | `10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + `--cm-primary` vurgu | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | disk seçimi | `--cm-primary-20` arka plan + kenarlık | `aria-pressed="true"` |
| Loading | tarama | skeleton (C13) | `aria-busy="true"` |
| Empty | disk yok | "Disk bulunamadı" | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
**Mode:** Red Team · Human Mode · Truth Mode
