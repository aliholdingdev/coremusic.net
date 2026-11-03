---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playlist (Çalma Listesi) Screen Specification (Linux 4K)"
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
  authority: ".ai/ui-design/screens/T20-monitor-4k/playlist.md"
  source_of_truth: ".ai/.png/home-3840/Linux 3840 - Playlist Page.png"
---

# CoreMusic — Playlist (T20 Desktop Monitor 3840×2160)

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
│ y:89┌─── PLAYLIST (w:1621 h:880 @x24,y89) ────────────────────┐  ┌─ SAĞ PANEL (w:219 @x1680,y89) ────┐│  DEKORATİF    │
│     │ Actions Btns (w:481 h:40 @x45,y110) — geri + başlık    │  │ Player İnfo Div 218×114 @y89      ││  BOŞ ZEMİN    │
│     │ Search (w:257 h:25 @x1376,y118) "Şarkı Ara"             │  │ Player Status Div-pi 224×316 @y390││  (x1920-3840  │
│     │ label text (w:1620 h:34 @y171) — tablo başlığı          │  │ Sağ Panel GROUP 219×420           ││  arası manzara│
│     │   kolon: / · Şarkı Adı · Albüm Adı · Sanatçı · Süre ·  │  │  Avatar + Önerilen + Şimdi Çalıyor ││  fotoğrafı —  │
│     │          Favori Yıldızı                                 │  │                                   ││  4K genişlik  │
│     │ Playlist FRAME (w:725 h:734 @x72,y217) — satır listesi  │  │                                   ││  boyunca      │
│     │   satır h:~32 · 25+ satır · seçili satır `--primary`    │  │                                   ││  devam eder)  │
│     │ scroolbar (w:3 h:744 @x1641,y210)                       │  │                                   ││               │
│ y:969└───────────────────────────────────────────────────────┘  └─────────────────────────────────────┘│               │
│ y:969├─── EMPTY (h1099) — dekoratif boş zemin (bg + gradient) ────────────────────────────────────────┤               │
│ y:2068└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:2068┌─── FOOTER PLAYER (w:3840 h:92 @x0,y2068 — TAM GENİŞLİK) ──────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 3838×4.8 · Player btn 126×27 @x1790 · Volume 353×14 @x3420 · "350 kpps"                     ││
│ y:2160└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 909 (y60-969) + dekoratif boş zemin 1099 (y969-2068) + footer 92 (y2068-2160) = 2160 ✓

**Yerleşim & 4K Davranışı:**
- 4K Monitor (T20) görünümünde, `home-dashboard.md` spec'i ile aynı tasarım prensibi geçerlidir: İçerik alanı sol-üste 1:1 yaslanır; sağ taraftaki boşluk (x1920-3840) ve alt boşluk (y969-2068) manzara arka planıyla doldurulur.
- Footer player (`w:3840 h:92 @y2068`) tam ekran genişliğinde uzanır.
- `Figma SSOT`: Sayfa `462:5874` / node `3155:19834` (Playlist 1920) düzeni, 4K ekranında `page-player--wide` ve `a-layout-tokens-3840.css` token'ları ile ölçeklenir.

**Lejant:** `[PNG]` = PNG'den okundu · `[Figma]` = Figma node ölçüsü · `[türetilmiş]` = 4K çıkarımı.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envanter |
|-------|-------|--------------------|----------|
| Sayfa Kabuğu | `.page-player` | `--wide`, `--4k` | C01 |
| Cam Panel | `.player-glass` | `__header`, `__body` | C02 |
| Tablo Başlık | `.table-head` | `__col`, `--sortable` | C03 |
| Satır | `.track-row` | `__index`, `__title`, `__album`, `__artist`, `__duration`, `--selected` | C03 |
| Favori | `.favorite` | `__star`, `--filled` | C08 |
| Arama | `.search-input` | `__field`, `__icon` | C05 |
| Kaydırma | `.scrollbar` | `__thumb` | C09 |
| Sağ Panel | `.player-info` | `__avatar`, `__title` | C11 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu / seçili satır | `--cm-primary` | `#ff4fd8` |
| Seçili arka plan | `--cm-primary-20` | `rgba(255,79,216,.2)` |
| Glass arka plan | `--cm-glass-bg` | `rgba(255,255,255,.05)` |
| Glass kenarlık | `--cm-glass-border` | `rgba(255,255,255,.1)` |
| Blur | `--cm-glass-blur` | `blur(12px)` |
| Font başlık | `--cm-font-display` | `var(--cm-font-display)` |
| Font gövde | `--cm-font-body` | `var(--cm-font-body)` |
| 4K Footer Yüksekliği | `--footer-h` | `90px` (a-layout-tokens-3840.css) |
| 4K Header Yüksekliği | `--header-h` | `65px` (a-layout-tokens-3840.css) |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T20) | 32×32 px (öneri) | 4K 10ft UI için minimum 56px (`--touch-min`) |
| Tablo satırı | ~32px / 48px | Hover & focus görünür |
| Favori yıldızı | ~24px ikon hit-area | Padding ile genişletilmiş |
| Arama alanı | 36px | Dikey padding ile rahat tıklama |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | PASS — Cam panel zemininde beyaz/açık gri metin |
| 2 | Odak görünür | 2px outline ≥3:1 | PASS — `:focus-visible` ile `--cm-primary` çerçeve |
| 3 | Dokunma hedefi | ≥24px (mouse), ≥56px (10ft) | PASS — Token kontrollü |
| 4 | Okuma sırası | Görsel = DOM | PASS — navbar → aksiyonlar → tablo → sağ panel → footer |
| 5 | Durum renkle değil | İkon/metin | PASS — seçili satır vurgu + ikon |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |
| Fallback | `background: rgba(255,255,255,.25)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Playlist  Page.png` (4K T20 ölçek uyarlaması)
- **4K Home Referansı:** `.ai/.png/home-3840/Linux 3840 - Home.png` (Tam tuval ve footer yerleşimi)
- **Klasör:** `home-1920/` (4 PNG) · `home-3840/` (1 PNG)
- **Figma node:** `3155:19834` (Playlist 1920 frame) · `3175:13502` (Linux 3840 frame)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| ≥2561px (4K) | Sabit sol-üst yerleşim, tam genişlik footer (3840×92), `--layout-scale-4k: 0.85` | T20 tier (a-layout-tokens-3840.css) |
| 1920-2560px | T17 Wide tier sahne yerleşimi | T17 tier |
| <1600px | T07 Fallback (`playlist--embedded`) | DeviceManager |
| Oryantasyon | Yatay (landscape-only) | `00-device-matrix.md` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.8` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + `--cm-primary` border | `aria-describedby` |
| Pressed | tıklama | `scale(.99)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | satır seçimi | `--cm-primary-20` arka plan | `aria-selected="true"` |
| Loading | veri | skeleton (C13) | `aria-busy="true"` |
| Empty | liste boş | "Çalma listesi boş" | — |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-10-28  
**Mode:** Red Team · Human Mode · Truth Mode
