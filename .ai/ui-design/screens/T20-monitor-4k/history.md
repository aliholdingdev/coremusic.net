---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Geçmiş (History) Screen Specification (Linux 4K)"
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
  authority: ".ai/ui-design/screens/T20-monitor-4k/history.md"
  source_of_truth: ".ai/.png/home-1920/Linux 1920 -  Geçmiş Page.png (4K T20 ölçek uyarlaması) · ⚠️ VERIFICATION REQUIRED — 3840 Geçmiş PNG bekleniyor"
---

# CoreMusic — Geçmiş (T20 Desktop Monitor 3840×2160)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-3840, y:0-2160)

````
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                          x:3840 │
│ y:0 ┌─── NAVBAR (w:993 h:27 @x12,y17) ───────────────────────────────────────────────────────────────┐               │
│     │ logo 108×29 @20,20 · link: Ana Sayfa·Keşfet·Albümler·Sanatçılar·Göz At·Geçmiş·Ayarlar·Hakkımızda│               │
│     │ sağ: Profile 90×24 · Wifi/BT · Battery · Logout @x1649+                                                 │               │
│ y:60└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:89┌─── ACTION BTN (w:481 h:40 @x24,y89) ───┐  ┌─ SEARCH (w:267 h:20) ───┐  ┌─ SAĞ PANEL (w:219) ─┐│  DEKORATİF    │
│     │ geri butonu + "Şimdi Oynatılıyor"       │  │ "Sırasıyla: Music, Video"│  │  Player Info 218×114 ││  BOŞ ZEMİN    │
│ y:129└───────────────────────────────────────┘  └──────────────────────────┘  │  Avatar+düğmeler     ││  (x1920-3840  │
│ y:171┌─── GEÇMİŞ IZGARA — 6 kolon × 1 satır ───────────────────────────────────┐│  Önerilen 2×4 avatar ││  arası manzara│
│     │ [K1 269×818] [K2 269×818] [K3 269×818] [K4 269×818] [K5 269×818] [K6]   ││  Şimdi Çalıyor       ││  fotoğrafı —  │
│     │ kolon pitch ~269 · başlık satırı "label text" @y171 h25                 ││  Öneriler           ││  4K genişlik  │
│ y:989└────────────────────────────────────────────────────────────────────────┘└──────────────────────┘│  boyunca      │
│ y:989├─── EMPTY (h1079) — dekoratif boş zemin (bg + gradient) ────────────────────────────────────────┤  devam eder)  │
│ y:2068└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:2068┌─── FOOTER PLAYER (w:3840 h:92 @x0,y2068 — TAM GENİŞLİK) ──────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 3838×4.8 · Player btn 126×27 @x1790 · Volume 353×14 @x3420 · "350 kpps"                     ││
│ y:2160└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 929 (y60-989) + dekoratif boş zemin 1079 (y989-2068) + footer 92 (y2068-2160) = 2160 ✓

**Yerleşim & 4K Davranışı:**
- 4K Monitor (T20) mimarisinde içerik sol-üst 1:1 bölgeye yerleşir, footer tam genişlikte çalışır.
- Figma SSOT: Sayfa `462:5874` / node `3155:22392` (Geçmiş 1920 frame) temel alınmıştır.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envanter |
|-------|-------|--------------------|----------|
| Sayfa Kabuğu | `.page-history` | `--wide`, `--4k` | C01 |
| Geçmiş Listesi | `.history-grid` | `__col`, `__row` | C03 |
| Kart | `.history-track-card` | `__thumb`, `__title`, `__artist`, `__time` | C19 |
| Arama | `.search-input` | `__field`, `__icon` | C05 |
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
| Geçmiş Kartı | ~40px satır yüksekliği | Tıklanabilir alan yeterli |
| Butonlar | 40×40 px | Minimum touch standardına uygun |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | PASS — Cam panel içi kontrast |
| 2 | Odak görünür | 2px outline ≥3:1 | PASS — `:focus-visible` aktif |
| 3 | Dokunma hedefi | ≥24px mouse, ≥56px 10ft | PASS |
| 4 | Okuma sırası | Görsel = DOM | PASS — Navbar → Eylemler → Liste → Sağ Panel → Footer |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Geçmiş Page.png` (4K T20 ölçek uyarlaması)
- **4K Home Referansı:** `.ai/.png/home-3840/Linux 3840 - Home.png`
- **Figma node:** `3155:22392` (Linux 1920 - Geçmiş Page)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| ≥2561px (4K) | Sol-üst 1:1 yerleşim, tam genişlik footer (3840×92), `--layout-scale-4k: 0.85` | T20 tier |
| 1920-2560px | T17 Wide tier sahne | T17 tier |
| <1600px | T07 Fallback (`history--embedded`) | DeviceManager |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.8` | — |
| Hover | mouse üstünde | `opacity:1` + border `--cm-primary` | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | seçim | `--cm-primary-20` dolgu | `aria-selected="true"` |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-10-28  
**Mode:** Red Team · Human Mode · Truth Mode
