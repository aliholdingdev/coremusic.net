---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-27
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/home-dashboard.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page.png"
---

# CoreMusic — Home Dashboard (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                x:1024 │
│ y:0   ┌── HEADER (h:60) ───────────────────────────────────────────────────────────────────────────────┐  │
│       │  Navbar (16,17) w:993 h:27 · .site-header__logo + .nav-link×N [aria-current] → C01           │  │
│ y:60  └───────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:60  ┌── CONTENT (h:450) ─────────────────────────────────────────────────────────────────────────────┐ │
│       │  ┌ (32,84) Player Info w:392 h:131 ───────┐ ┌ (627,84) Hoparlör w:173 h:51 ┐ (820,84) Hava     │ │
│       │  │ .now-playing__meta-value + C19 mini 2×2│ └──────────────────────────────┘ w:172 h:51         │ │
│       │  │ Playlist Status Div → §5 ÇELİŞKİ-2     │ ┌ (627,155) ROW2 w:365 h:40 · C17 widget ×5 ──────┐ │ │
│       │  │ y:84 ──────────────────────▶ y:215     │ │ Tarih/Saat 109 · EQ 42 · ×5 → bitiş x:992      │ │ │
│       │  └────────────────────────────────────────┘ └─────────────────────────────────────────────────┘ │ │
│       │                                      ┌ (627,215) ROW3 w:365 h:40 ─────────────────────────────┐  │
│       │                                      │ Kütüphanelerim 129×40 + .home-app-btn ×4 → C18 kanıtı  │  │
│       │                                      └─────────────────────────────────────────────────────────┘  │
│       │  ┌ (32,316.5) En Son Dinlenen w:354 h:138.5 ┐┌ (419,317) Playlistler w:353 h:138 ┐┌ (805,317)    │
│       │  │ C19 .home-mini-card ×4 (169×43) 2×2      ││ .playlist-list-card ×3 + button  ││ Sıradaki      │
│       │  └──────────────────────────────────────────┘└───────────────────────────────────┘│ title w:186   │
│       │                                                                                     │ (805,344)     │
│       │                                                                                     │ .up-next-panel│
│       │                                                                                     │ h:109 (1 mini)│
│ y:510 └─────────────────────────────────────────────────────────────────────────────────────┴─────────────┘ │
│ y:510 ┌── FOOTER (h:90) ──────────────────────────────────────────────────────────────────────────────────┐ │
│       │  .footer + .footer-player__inner/__controls/__volume · role=contentinfo aria-label="Oynatıcı"     │ │
│ y:600 └────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Yükseklik hesabı:** 60 (header) + 450 (content) + 90 (footer) = **600 ✓** · Panel alt kenarı y:455 (content y:510'e kadar boşluk korunur).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar | `.site-header` | `__inner`, `__logo`, `__nav` | — (ekran özel) |
| Nav link | `.nav-link` | `[aria-current="page"]` (CSS/PHP kanıtlı) | C01 |
| Şimdi çalan bilgi | `.now-playing` | `__meta-value` | — (ekran özel) |
| Widget slot | `.home-slot` | `--eq` | — (ekran özel) |
| Widget panel (Hoparlör/Hava) | `.home-widget-panel` | `__icon`, `__text`, `__title` | C17 |
| Quick apps satırı | `.home-quick-apps-row` | `__left`, `__right` | — (ekran özel) |
| Uygulama butonu | `.home-app-btn` | `--youtube`, `--heart`, `--sparkle`, `--filemanager`, `--circle` | — (ekran özel) |
| Playlist kartı | `.playlist-list-card` | `__icon` | — (ekran özel) |
| Sıradaki panel | `.up-next-panel` | `.mini-card` (nested) | — (ekran özel) |
| Mini card (En Son) | `.home-mini-card` | `__art`, `__info`, `__title` | C19 |
| Footer | `.footer` | `.footer-player__inner`, `__controls`, `__volume`, `__btn` | — (ekran özel) |

> ⚠️ VERIFICATION REQUIRED — Envanterdışı sınıflar (02-component-inventory.md C01-C19 ile birebir değil): `.site-header`, `.now-playing`, `.home-slot`, `.home-quick-apps-row`, `.home-app-btn`, `.playlist-list-card`, `.up-next-panel`, `.mini-card`, `.footer`, `.footer-player`. Envanter adı olan ama CSS'te doğrulanamayanlar: `.home-widget-panel` (C17), `.home-mini-card` (C19) — envanter adıyla yazıldı, CSS karşılığı kod katmanında doğrulanmalı. Kod tarafı sınıf adı uydurulmadı.

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Header yüksekliği (embedded) | `--cm-header-h-embedded` | `var(--cm-header-h-embedded)` |
| Footer yüksekliği (embedded) | `--cm-footer-h-embedded` | `var(--cm-footer-h-embedded)` |
| İçerik yüksekliği | `--cm-content-h` | `var(--cm-content-h)` |
| Widget panel boyu | `--cm-widget-panel-w` | `var(--cm-widget-panel-w)` |
| Quick app boyu | `--cm-quick-app-w` | `var(--cm-quick-app-w)` |
| Mini card boyu | `--cm-mini-card-w` | `var(--cm-mini-card-w)` |
| Sayfa zemini | `--cm-bg-primary` | `var(--cm-bg-primary)` |
| Vurgu rengi | `--cm-primary` | `var(--cm-primary)` |
| CTA gradyanı | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Odak halkası | `--cm-focus-ring` | `var(--cm-focus-ring)` |
| Touch hedefi (tier 48px) | `--cm-touch-target-lg` | `var(--cm-touch-target-lg)` |
| Kart yarıçapı | `--cm-radius-md` | `var(--cm-radius-md)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| T08 Embedded (matrix `Touch` = 48px) | **48×48 px** | `00-device-matrix.md` Embedded tablosu bağlayıcı; şablonun 44px tabanından katı |
| WCAG 2.2 AA 2.5.8 (yasal alt sınır) | 24×24 px | Bu spec'teki mini-card (43) / app-btn (40) / chip (25) bu sınırın üstünde |
| Şablon geneli (T01-T08, T29, T31) | 44×44 px | `--cm-touch-target: 44px` (master) — T08'de 48px'e yükseltilir |
| Yakınlık kuralı | ≥ 8 px boşluk | Yanlış basma önleme (widget satır aralığı 14px) |
| Komut satırı | `touch target >= 44px (tier embedded/phone)` | Prompt `constraints` ile aynı ifade |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) | GAP — PNG medyan zemin ölçümü: beyaz metin ≈ **2.96:1** (§7 PNG örnekleme) |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 | PASS — `:focus-visible` + `var(--cm-focus-ring)` (`.home-app-btn:focus-visible`, `.playlist-list-card:focus-visible`) |
| 3 | Dokunma hedefi (WCAG 2.5.8) | ≥ 24×24 px | PASS — mini-card 43, app-btn 40, widget 51, chip 25 (15px yıldız yok) |
| 4 | Tier touch (T08, matrix `Touch`=48px) | ≥ 48×48 px | GAP — `--cm-touch-target: 44px` < 48; küçük hedefler (chip 25) |
| 5 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası | PASS — header → content (widget → paneller) → footer |
| 6 | Durum yalnız renkle anlatılmıyor | İkon/metin + aria | PASS — `aria-current="page"` (header.php L82) + `role="contentinfo" aria-label` (footer.php L66) |

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: **BEKLEMEDE** (bu görevde dosya yazım yasağı).

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 2**

**ÇELİŞKİ-1 — Tier/viewport (00-device-matrix ↔ bu dosya):** matrix L92 `T07 | RPi5 7" | 1024×600` ve L295 `1024×600 → T07 Embedded` derken L93 `T08 = 1280×800` (RPi5 10"); bu spec `screens/T08-embedded/` dizininde ve `viewport: 1024x600`. Ek kanıt: L95 "Welcome popup (T07)", L97 T07/T08 dual-ID çelişkisi. Karar: `tier: T08` + `1024x600` **korunur** (görev/dizin tanımı); matrix düzeltmesi **BEKLEMEDE** (`00-device-matrix.md`'e bu görevde dokunma yasağı).

**ÇELİŞKİ-2 — Player Info ↔ Playlist Status Div (Figma ↔ PNG):** Figma'da her ikisi de (32,84) orijininde — `Player Info` 392×131 ve `Playlist Status Div` 506×198 (üst üste binen iki düğüm). PNG'de yalnız `Player Info` görünür → SSOT PNG kazanır: `Playlist Status Div` bu spec'te **yok sayılır**.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(var(--cm-glass-blur))` |
| Arka plan | `var(--cm-glass-bg)` |
| Kenarlık | `var(--cm-border-w) solid var(--cm-glass-border)` |
| Gölge | `var(--cm-glass-shadow)` |
| Fallback (blur desteklenmiyorsa) | solid `var(--cm-glass-bg-strong)` |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır (§5-1 GAP ile tutarlı) |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri SSOT'u `tokens/design-tokens-master` §2.1.2 (Figma `2831:10268`); bu ekranda overlay çelişkisi yok.

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Home Page.png`
- **Klasör:** `home-1024/` (12 PNG) · Alternatif açı: `.ai/.png/home-1920/Linux - 1920 - Home.png`
- **Mockup indeksi:** [[01-mockup-index]]
- **Kullanım sırası:** PNG > Figma extracted > ASCII — çelişki §5'e "ÇELİŞKİ" olarak yazılır (AGENTS.md §7.2)
- **`screens/00-ascii-art-index.md` satırı:** BEKLEMEDE (bu görevde indeks dosyası yazım yasağı)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | T08 1024×600 sabit grid (widget row1 2×2 · row2/row3 1×5 — C17/C18 kuralı); yeniden akış yok | [[05-responsive-architecture]] §7.4 + §12 |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | [[00-device-matrix]] |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | [[05-responsive-architecture]] §12 |
| Portre/Dikey | `N/A (landscape-only)` | `reference/10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity: var(--cm-opacity-full)` | — |
| Hover | `@media (hover:hover)` | `var(--cm-hover-bg)` + `var(--cm-card-shadow-hover)` | `aria-describedby` |
| Pressed | dokunma | `scale(.98)` + `var(--cm-active-bg)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-border-focus)` | `:focus-visible` |
| Active (nav) | sayfa seçimi | `var(--cm-selected-bg)` + altı çizili işaret | `aria-current="page"` |
| Disabled | veri/şarkı yok | `opacity: var(--cm-opacity-disabled)` | `aria-disabled="true"` |
| Loading | veri bekleniyor | skeleton (`.skeleton` → C13) | `aria-busy="true"` |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| PNG doğrulama | ✅ okundu → `status: active` |
| ASCII yükseklik | 60 + 450 + 90 = 600 ✓ |
| §5 ÇELİŞKİ | 2 |
| §6 ÇELİŞKİ | 0 |
| `00-ascii-art-index.md` | BEKLEMEDE (bu görevde yazım yasağı) |
| Cross References | 5 |
| Last Updated | 2026-09-27 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-27
**Mode:** Red Team · Human Mode · Truth Mode
