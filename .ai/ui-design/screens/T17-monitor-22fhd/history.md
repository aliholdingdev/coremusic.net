---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Geçmiş (History) Screen Specification"
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
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/history.md"
  source_of_truth: ".ai/.png/home-1920/Linux 1920 -  Geçmiş Page.png"
---

# CoreMusic — Geçmiş (T17 Desktop Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

````
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                    x:1920 │
│ y:0 ┌─── NAVBAR (w:993 h:27 @x12,y17) ─────────────────────────────────────────────────────────────────┐        │
│     │ logo 108×29 @20,20 · link: Ana Sayfa·Keşfet·Albümler·Sanatçılar·Göz At·Geçmiş·Ayarlar·Hakkımızda │        │
│     │ sağ: Profile 90×24 · Wifi/BT · Battery · Logout @x1649+                                                               │
│ y:60└───────────────────────────────────────────────────────────────────────────────────────────────────┘        │
│ y:89┌─── ACTION BTN (w:481 h:40 @x24,y89) ───┐  ┌─ SEARCH (w:267 h:20 @x1358,y100) ──┐  ┌─ SAĞ PANEL (w:219) ┐│
│     │ geri butonu + "Şimdi Oynatılıyor"       │  │ "Sırasıyla: Music, Video…" + input  │  │  Player Info 218×114 ││
│ y:129└───────────────────────────────────────┘  └────────────────────────────────────┘  │  Avatar+düğmeler     ││
│ y:171┌─── GEÇMİŞ IZGARA — 6 kolon × 1 satır ─────────────────────────────────────────┐ │  Önerilen 2×4 avatar  ││
│     │ [K1 269×818] [K2 269×818] [K3 269×818] [K4 269×818] [K5 269×818] [K6 269×818]│ │  Şimdi Çalıyor 218×? ││
│     │ kolon pitch ~269 · başlık satırı "label text" @y171 h25                       │ │  Öneriler             ││
│ y:1000└──────────────────────────────────────────────────────────────────────────────┘ └──────────────────────┘│
│ y:1000┌─── FOOTER PLAYER (w:1923 h:92 @x0,y988) ──────────────────────────────────────────────────────────────┐│
│     │ MediaInfo · progress 1921×4.8 · Player btn 126×27 @x899 · Volume 353×14 @x1533 · bitrate "350 kpps"      ││
│ y:1080└────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
````

**Bant toplamı:** navbar 60 (y0-60) + içerik 940 (y60-1000) + footer 80 (y1000-1080) = 1080 ✓

**Figma grup** (`3155:22402` "Geçmiş", 1612×900 @x6952,y-487 → frame-rel x24,y89):
- `label text` GROUP 1612×25 @y171 (başlık satırı)
- 6× `Music Geçmiş` GROUP **269×818** @x24/293/562/832/1100/1330 (kolon) — frame-rel x: 24, 293, 562, 832, 1100, 1330
- `album desc` TEXT "Sırasıyla : Music, Video" 318×12 @x1348,y120
- `Search` GROUP 267×20 @x1358,y100 · `Actions Btns` FRAME 481×40 @x24,y89
- Sağ panel: `Player Status Div-pi` 224×316 · `Player İnfo Div` 218×114 · `Sağ Panel` GROUP 219×420

> ⚠️ ÇELİŞKİ (§1-1): PNG'de tek bir dikey kaydırma listesi görünür (6 kolonlu tek parça liste değil); Figma'da 6 ayrı `Music Geçmiş` GROUP'ı (her biri 269×818) var. PNG > Figma → görsel doğrulamada liste tek sütun, kolon GROUP'ları iç içe veri blokları. Çözüm: Figma'da gruplama revize edilmeli.

**Lejant:** `[PNG]` = PNG'den okundu · `[Figma]` = Figma node ölçüsü · `[türetilmiş]` = çıkarım.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar | `.nav-link` | `__logo`, `__nav`, `--active` | C01 |
| Sağ Panel | `.player-info` | `__avatar`, `__title`, `__controls` | C11 |
| Geçmiş Kolon | `.history-column` | `__label`, `__list`, `__item` | C03 |
| Önerilen | `.recommended` | `__row`, `__avatar`, `--selected` | C11 |
| Şimdi Çalıyor | `.now-playing` | `__art`, `__title`, `__rating` | C07 |
| Search | `.search-input` | `__field`, `__placeholder` | C05 |
| Actions | `.btn` | `--back`, `--icon` | C04 |
| Footer | `.player-bar` | `__progress`, `__controls`, `__volume` | C15, C09 |
| Kolon Başlık | `.column-label` | `__text` | C03 |

> BEM sınıfları `02-component-inventory.md` C01-C17 ile eşleşir; `.history-column` / `.recommended` ekran özelidir (envanterde karşılığı yok → `—`).

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu / aktif | `--cm-primary` | `#ff4fd8` |
| Glass arka plan | `--cm-glass-bg` | `rgba(255,255,255,.05)` |
| Glass kenarlık | `--cm-glass-border` | `rgba(255,255,255,.1)` |
| Blur | `--cm-glass-blur` | `blur(12px)` |
| Kart yarıçapı | `--cm-card-radius` | `var(--cm-card-radius)` |
| Gölge | `--cm-shadow-md` | `var(--cm-shadow-md)` |
| Font başlık | `--cm-font-display` | `var(--cm-font-display)` |
| Font gövde | `--cm-font-body` | `var(--cm-font-body)` |
| Progress dolgu | `--cm-primary-60` | `rgba(255,79,216,.6)` |

> Token adları `tokens/design-tokens-master.md`'den alınmıştır; ham hex/px §6'da gösterilir.

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T17) | 32×32 px (öneri) | Hover affordans mevcut |
| Nav link | ≥24×24 px | 11px font + padding ile |
| Footer player butonları | 27px yükseklik | ≥24 sınırı ✓ |
| Volume slider | 14px yükseklik | ⚠️ dikey padding gerekli (24px) |
| Yakınlık kuralı | ≥8 px boşluk | Yanlış basma önleme |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥4.5:1 normal | GAP — görsel zemin üstü beyaz metin, ölçüm gerekli |
| 2 | Odak görünür | 2px outline ≥3:1 | GAP — PNG'de focus state yok |
| 3 | Dokunma hedefi | Mouse tier 24px | PASS — nav link padding ile |
| 4 | Okuma sırası | Görsel = DOM | PASS — navbar → aksiyon → kolonlar → panel → footer |
| 5 | Durum yalnız renkle | İkon/metin + aria | PASS — avatar + yıldız ikonu |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(12px)` (`--cm-glass-blur`) |
| Arka plan | `rgba(255,255,255,.05)` (`--cm-glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.1)` (`--cm-glass-border`) |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` (`--cm-shadow-md`) |
| Fallback | `background: rgba(255,255,255,.25)` (solid — blur desteklenmiyorsa) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1920/Linux 1920 -  Geçmiş Page.png`
- **Klasör:** `home-1920/` (4 PNG) · Eşdeğer 1024: `.ai/.png/home-1024/Linux  1024 - Geçmiş Page.png` (⚠️ VERIFICATION REQUIRED — 1024 PNG'si envanterde yok)
- **Mockup indeksi:** [[../01-mockup-index]]
- **Figma node:** `3155:22392` (frame) · `3155:22402` (Geçmiş GROUP)
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| 1920-2560px | Sabit 1920 sahne; max-width 1920 ortalanmış | T17 tier |
| Kolon sayısı | 6 kolon sabit; sığmayan yatay kaydırma | PNG |
| <1600px | T17 kapsam dışı → T07 fallback | `05-responsive-architecture` §12 |
| Portre | N/A (landscape-only) | `10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + `--cm-primary` vurgu | `aria-describedby` |
| Pressed | tıklama | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | seçim | `font-weight:600` + kenar çubuğu | `aria-current="true"` |
| Loading | veri bekleniyor | skeleton (C13) | `aria-busy="true"` |
| Empty | geçmiş boş | metin "Geçmiş boş" | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
**Mode:** Red Team · Human Mode · Truth Mode
