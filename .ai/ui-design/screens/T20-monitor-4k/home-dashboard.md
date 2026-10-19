---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Home Dashboard Screen Specification (Linux 4K)"
type: spec
category: ui-design
date: 2026-10-18
updated: 2026-10-18
status: active
version: 1.0.0
tier: T20
viewport: 3840x2160
device: "27\" 4K Monitor (Desktop)"
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T20-monitor-4k/home-dashboard.md"
  source_of_truth: ".ai/.png/home-3840/Linux 3840 - Home.png"
---

# CoreMusic — Home Dashboard (T20 Desktop Monitor 3840×2160)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-3840, y:0-2160)

````
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                          x:3840 │
│ y:0 ┌─── NAVBAR (w:993 h:27 @x12,y17) ───────────────────────────────────────────────────────────────┐               │
│     │ logo 108×29 @20,20 · link: Ana Sayfa·Keşfet·Albümler·Sanatçılar·Göz At·Geçmiş·Ayarlar·Hakkımızda│               │
│     │ sağ: Profile 90×24 @x1649 · Wifi/BT · Battery · Logout                                                                 │
│ y:99 ├─── TOP (h184) ─────────────────────────────────────────────────────────────────────────────────┤               │
│     │ [Player Info 469×184 @x53] [Banner 491×184 @x552] [Div2 752×184 @x1073]                        │               │
│     │  album 150×150 · metin kolonu · progress 246×9 · hava/tarih/Kütüphanelerim/YT-Deezer 42×43      │               │
│ y:332├─── ROW A "En Son Dinlenen Şarkılar" (h78) ────────────────────────────────────────────────────┤  DEKORATİF    │
│     │ başlık y332 · 10× mini-card 169×43 @x53,237,421,…,1709 (pitch 184) y369                         │  BOŞ ZEMİN    │
│ y:442├─── ROW B "Son Oluşturlan & Sistem Taraından Oluşturlan Playlistler" (h78) ────────────────────┤  (x1920-3840  │
│     │ başlık y442 · 10× mini-card 169×43 y479                                                          │  arası manzara│
│ y:552├─── ROW C "En Son Dinlenen Albumler" (h98) ────────────────────────────────────────────────────┤  fotoğrafı —  │
│     │ başlık y552 · 10× album-card 169×63 @x53…1709 y589                                               │  4K genişlik  │
│ y:682├─── ROW D "Önerilen Sanatçılar" (h98) ─────────────────────────────────────────────────────────┤  boyunca      │
│     │ başlık y682 · 10× artist-card 169×63 y719 (dairesel avatar)                                      │  devam eder)  │
│ y:812├─── ROW E "Önerilen Albumler" (h79) ───────────────────────────────────────────────────────────┤               │
│     │ başlık y812 · 10× album-card 169×63 y849                                                          │               │
│ y:891├─── EMPTY (h1177) — dekoratif boş zemin (bg + gradient) ────────────────────────────────────────┤               │
│ y:2068└────────────────────────────────────────────────────────────────────────────────────────────────┘               │
│ y:2068┌─── FOOTER PLAYER (w:3840 h:92 @x0,y2068 — TAM GENİŞLİK) ──────────────────────────────────────────────────────┐│
│     │ MediaInfo @x91 · progress 3838×4.8 (tam genişlik) · Player btn 126×27 @x1790 · Volume @x3420 · "350 kpps"        ││
│ y:2160└────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** 99 (nav) + 184 (top) + 49 (boşluk y283-332) + 78 + 78 + 98 + 98 + 79 + 1177 (boş zemin) + 92 (footer) = 2032? → kontrol: y0→2160; ROW A y332 + 5 satır (332→891) + boş 891-2068 (1177) + footer 2068-2160 (92) → 891+1177+92 = 2160 ✓

**Figma** (sayfa `3175:13501`, frame `3175:13502` "Linux 3840 - Home" 3840×2160; origin 948,−576):
- `Navbar` INSTANCE 993×27 @rel(12,17) · `Footer` INSTANCE **3840×92** @rel(0,2068)
- `Player Info` GROUP 469×184 @rel(53,99) · `Group 52` (Banner) 491×184 @rel(552,99) · `Div2 Button` 752×184 @rel(1073,99)
- 5 satır GROUP'ı (frame-rel x53): `En Son Dinlenen Şarkılar` 1825×78 @y332 · `Son Oluşturlan…Playlistler` 1825×78 @y442 · `En Son Dinlenen Albumler` 1815×98 @y552 · `Önerilen Sanatçılar` 1815×98 @y682 · `Önerilen Albumler` 1805×79 @y812
- Kart INSTANCE'ları: 10'ar adet 169×43 (mini-card) / 169×63 (album/artist), pitch 184, @x53→1709
- `Romantic_Background_03` 3840×2160 (tam tuval)

> **Ölçek notu (PNG > matris):** `00-device-matrix` DM-T20 satırı `1.5x` ölçek öngörür; PNG'de kartlar **169×43 ile 1:1** çizilmiş (ölçeklenmemiş, içerik sol-üst 1920×1080 bölgesinde). PNG > Figma/matrix → 4K'da ölçekleme yerine geniş tuvale 1:1 yerleşim + tam genişlik footer tercih edilmiş. `05-responsive-architecture §7.4` (4K'da ortalamama) ile **çelişme** notu: içerik ortalanmıyor, sol-üste yaslı.

**Lejant:** `[PNG]` = PNG'den okundu · `[Figma]` = Figma node ölçüsü · `[türetilmiş]` = çıkarım.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar | `.nav-link` | `__logo`, `__nav`, `--active` | C01 |
| Top Bölge | `.home-widget-area` | `__player`, `__banner`, `__quick` | C17 |
| Quick App | `.home-quick-apps` | `__icon`, `--yt`, `--deezer` | C18 |
| Mini Kart | `.home-mini-card` | `__thumb`, `__title`, `__meta` | C19 |
| Albüm Kartı | `.album-card` | `__cover`, `__title`, `__play` | C03 |
| Sanatçı Kartı | `.artist-card` | `__avatar`, `__name` | C11 |
| Progress | `.progress` | `__fill` | C15 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |
| Buton | `.btn` | `--icon`, `--primary` | C04 |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu / progress | `--cm-primary` | `#ff4fd8` |
| Progress dolgu | `--cm-primary-60` | `rgba(255,79,216,.6)` |
| Glass arka plan | `--cm-glass-bg` | `rgba(255,255,255,.05)` |
| Glass kenarlık | `--cm-glass-border` | `rgba(255,255,255,.1)` |
| Blur | `--cm-glass-blur` | `blur(12px)` |
| Kart yarıçapı | `--cm-card-radius` | `var(--cm-card-radius)` |
| Gölge | `--cm-shadow-md` | `var(--cm-shadow-md)` |
| Font başlık | `--cm-font-display` | `var(--cm-font-display)` |
| Font gövde | `--cm-font-body` | `var(--cm-font-body)` |
| Z-player | `--cm-z-player` | `var(--cm-z-player)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T20) | 32×32 px (öneri) | Hover affordans |
| Mini kart | 169×43 | ✓ |
| Quick app ikonları | 42×43 | ✓ |
| Nav link | ≥24×24 px | padding ile |
| Footer butonları | 27px | ≥24 ✓ |
| Yakınlık kuralı | ≥8 px boşluk | Kart pitch 184 (169+15) |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 | GAP — fotoğraf zemin üstü beyaz metin, ölçüm gerekli |
| 2 | Odak görünür | 2px outline ≥3:1 | GAP — PNG'de focus state yok |
| 3 | Dokunma hedefi | ≥24px mouse | PASS — kartlar 43px+ |
| 4 | Okuma sırası | Görsel = DOM | PASS — nav → top → A/B/C/D/E satırları → footer |
| 5 | Durum renkle değil | İkon/metin | PASS — oynat ikonu + başlık |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |
| Fallback | `background: rgba(255,255,255,.25)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-3840/Linux 3840 - Home.png`
- **Klasör:** `home-3840/` (1 PNG · 3840×2160)
- **Mockup indeksi:** [[../01-mockup-index]]
- **Figma node:** `3175:13502` (frame · sayfa `3175:13501` "Core Music - Linux 3840")
- **Ham çıkarım:** `reference/figma/extracted-3175-13501.md` (v1.0.0)
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **1920 karşılığı:** [[T17-monitor-22fhd/home-dashboard]] — 1920 sürümünde 2 satır (10+6 kart); 4K sürümünde **5 satır × 10 kart** (462:5874 v1.2.0 ile 1920 sayfası da 5 satıra güncellendi — extracted-462-5874.md §3.1 aynı grup adlarını taşır)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| 3840 sabit | Tuval 3840×2160; içerik 1:1 (ölçek yok), sol-üste yaslı | PNG (SSOT) |
| Footer | Tam genişlik 3840 (1920'de 1923) | Figma `3175:13504` |
| 2560-3840 arası | T20 kapsam dışı → T17 fallback | `05-responsive-architecture` §12 |
| Ölçek (matrix 1.5x) | PNG'de uygulanmadı — PNG > matrix | §1 notu |
| Portre | N/A (landscape-only) | `10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + `--cm-shadow-lg` | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Playing | oynatılıyor | progress dolgu + pause ikonu | `aria-pressed="true"` |
| Loading | veri | skeleton (C13) | `aria-busy="true"` |
| Empty | satır boş | bölüm başlığı + "Öğe yok" | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
**Mode:** Red Team · Human Mode · Truth Mode
