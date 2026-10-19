---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playlist (Çalma Listesi) Screen Specification"
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
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/playlist.md"
  source_of_truth: ".ai/.png/home-1920/Linux 1920 -  Playlist  Page.png"
---

# CoreMusic — Playlist (T17 Desktop Monitor 1920×1080)

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
│ y:89┌─── PLAYLIST (w:1621 h:880 @x24,y89) ────────────────────┐  ┌─ SAĞ PANEL (w:219 @x1680,y89) ──────────┐│
│     │ Actions Btns (w:481 h:40 @x45,y110) — geri + başlık    │  │ Player İnfo Div 218×114 @y89            ││
│     │ Search (w:257 h:25 @x1376,y118) "Şarkı Ara"             │  │ Player Status Div-pi 224×316 @y390      ││
│     │ label text (w:1620 h:34 @y171) — tablo başlığı          │  │ Sağ Panel GROUP 219×420                 ││
│     │   kolon: / · Şarkı Adı · Albüm Adı · Sanatçı · Süre ·  │  │  Avatar + Önerilen 2×4 + Şimdi Çalıyor  ││
│     │          Favori Yıldızı                                 │  │                                         ││
│     │ Playlist FRAME (w:725 h:734 @x72,y217) — satır listesi  │  │                                         ││
│     │   satır h:~32 · 25+ satır · seçili satır `--primary`    │  │                                         ││
│     │ scroolbar (w:3 h:744 @x1641,y210)                       │  │                                         ││
│ y:969└───────────────────────────────────────────────────────┘  └───────────────────────────────────────────┘│
│ y:988┌─── FOOTER PLAYER (w:1923 h:92 @x0,y988) ──────────────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 1921×4.8 · Player btn 126×27 @x899 · Volume 353×14 @x1533 · bitrate "350 kpps"      ││
│ y:1080└────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 928 (y60-988) + footer 92 (y988-1080) = 1080 ✓

**Figma grup** (`3155:20562` "Playlist", 1621×880 @x4942,y-487 → frame-rel x24,y89):
- `label text` GROUP 1620×34 @y171 — tablo başlığı
- `Playlist` FRAME **725×734** @x72,y217 — satır listesi
- `scroolbar` GROUP **3×744** @x1641,y210
- `Search` GROUP **257×25** @x1376,y118 — "Şarkı Ara"
- `Actions Btns` FRAME 481×40 @x45,y110
- Sağ panel: `Player İnfo Div` 218×114 · `Player Status Div-pi` 224×316 · `Sağ Panel` GROUP 219×420

**PNG okuma (tablo kolonları):** `/` · `Şarkı Adı` · `Albüm Adı` · `Sanatçı` · `Süre` · `Favori Yıldızı` — satır içeriği "Göksel - Sevil Neşelen / Hayat Rüya Gibi / Göksel / 00:00:00 / ★★★★★". Seçili satır pembe vurgulu (y≈325).

> ⚠️ ÇELİŞKİ (§1-1): `Playlist` FRAME Figma'da **725×734** (sol blok); PNG'de tablo tüm genişliği (1621) kaplıyor. PNG > Figma → Figma'da liste frame'i daraltılmış; revize edilmeli.

**Lejant:** `[PNG]` = PNG'den okundu · `[Figma]` = Figma node ölçüsü · `[türetilmiş]` = çıkarım.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar | `.nav-link` | `__logo`, `__nav`, `--active` | C01 |
| Tablo Başlık | `.table-head` | `__col`, `--sortable` | C03 |
| Satır | `.track-row` | `__index`, `__title`, `__album`, `__artist`, `__duration`, `--selected` | C03 |
| Favori | `.favorite` | `__star`, `--filled` | C08 |
| Arama | `.search-input` | `__field`, `__icon` | C05 |
| Kaydırma | `.scrollbar` | `__thumb` | C09 |
| Sağ Panel | `.player-info` | `__avatar`, `__title` | C11 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |

> `.track-row` / `.table-head` / `.favorite` envanterde birebir yok → C03 (Card) + C08 (Toggle) türevi.

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
| Favori yıldızı | `--cm-primary` | `#ff4fd8` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T17) | 32×32 px (öneri) | Hover affordans |
| Tablo satırı | ~32px yükseklik | ✓ sınırda |
| Favori yıldızı | ~16px ikon | ⚠️ padding gerekli |
| Search alanı | 25px yükseklik | ⚠️ dikey padding |
| Yakınlık kuralı | ≥8 px | Satır arası boşluk |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | GAP — görsel zemin üstü |
| 2 | Odak görünür | 2px outline ≥3:1 | GAP — focus state yok |
| 3 | Dokunma hedefi | ≥24px mouse | PASS — satır 32px |
| 4 | Okuma sırası | Görsel = DOM | PASS — navbar → aksiyon → tablo → panel → footer |
| 5 | Durum renkle değil | İkon/metin | PASS — seçili satır ikon + vurgu |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |
| Fallback | `background: rgba(255,255,255,.25)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Playlist  Page.png`
- **Klasör:** `home-1920/` (4 PNG) · 1024 karşılığı: `.ai/.png/home-1024/Linux  1024 - Playlist Page.png`
- **Mockup indeksi:** [[../01-mockup-index]]
- **Figma node:** `3155:19834` (frame) · `3155:20562` (Playlist GROUP)
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| 1920-2560px | Sabit 1920 sahne; max-width 1920 | T17 tier |
| Tablo kolonları | 6 kolon sabit; sığmazsa yatay kaydırma | PNG |
| <1600px | T17 kapsam dışı → T07 fallback (`playlist`) | §12 fallback |
| Portre | N/A (landscape-only) | `10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + `--cm-primary` vurgu | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | satır seçimi | `--cm-primary-20` arka plan | `aria-selected="true"` |
| Loading | veri | skeleton (C13) | `aria-busy="true"` |
| Empty | liste boş | "Çalma listesi boş" | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
**Mode:** Red Team · Human Mode · Truth Mode
