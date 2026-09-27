---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Bluetooth Quick Panel Screen Specification"
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
  authority: ".ai/ui-design/screens/T08-embedded/bluetooth-quick.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png"
---

# CoreMusic — Bluetooth Quick Panel (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌──────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024  │
│ y:0   ┌── OVERLAY (2,0 · 1024×601) ─────────────────────────────┐   │
│       │ #000 op.55 · BACKGROUND_BLUR 3 → blur(3px)              │   │
│       │ (x:2 başlangıç — PNG'de tam ekran karartma → inset:0)    │   │
│ y:60  ├──────────────────────────────────────────────────────────┤   │
│       │      ┌── PANEL BLUETOOTH (356,147 · 312×308) ────────┐   │   │
│       │      │ BLUR 8 → var(--cm-glass-blur-sm)              │   │   │
│       │      │ header (364,155 · 86×23): icon 20×20 (364,156)│   │   │
│       │      │  · "Bluetooth" (385,155) ·                     │   │   │
│       │      │  "Cihaz Bağlantıları" (385,167)                │   │   │
│ y:198 │      │ toggle (370,198 · 72×15): bg 65×15 r5 ·        │   │   │
│       │      │  "Bluetooth" (375,199.5) · Switch ON (418,201   │   │   │
│       │      │   · 24×10, pembe)                               │   │   │
│ y:229 │      │ "Bağlı Olan Cihaz" (371,229)                    │   │   │
│       │      │ connected `2831:9696` (371,249 · 148×17 r5):    │   │   │
│       │      │  bg 279×30 · icon 22×22 (375,254) ·             │   │   │
│       │      │  name+tag (400,254 · 53×11): "Km - 50" +        │   │   │
│       │      │   "Bağlı" (434,255.2) ·                          │   │   │
│       │      │  desc (400,264) "Kulaklık . Mükemmel sinyal:    │   │   │
│       │      │   100% . AAC . Pil : 100%" ·                     │   │   │
│       │      │  "Bağlantıyı Kes" (599.5,256.5 · 44×15)         │   │   │
│ y:293 │      │ "Kullanılabilir Cihazlar" (372,293)               │   │   │
│       │      │ item 1 (371,313 · 148×17): "Km - 50" + tag ×2    │   │   │
│       │      │  (Şifresiz · Bağlı Değil) · desc (400,328)        │   │   │
│       │      │  "Kulaklık . Mükemmel sinyal: 100%" ·             │   │   │
│       │      │  "Bağlan" (616,322 · 28×12)                       │   │   │
│       │      │ item 2 (371,360 · 148×17): "Car BT" + tag ×2 ·    │   │   │
│       │      │  desc (400,375) "Kulaklık . Mükemmel sinyal:      │   │   │
│       │      │   100%" · "Bağlan" (616,369 · 28×12)              │   │   │
│ y:438 │      │ item 3 (371,407 · 148×17): "Samsung TV" + tag ×2   │   │   │
│       │      │  (Pin Kodlu · Bağlı Değil) · desc (400,422)        │   │   │
│       │      │  "Televizyon . Mükemmel sinyal: 100%" ·            │   │   │
│       │      │  "Bağlan" (616,416 · 28×12)                        │   │   │
│ y:455 │      └────────────────────────────────────────────────────┘   │
│ y:510 │   (FOOTER y:510-600 — alt ekran karartma altında)          │   │
│ y:600 └──────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────┘
```

> **ÇELİŞKİ (overlay konumu):** Figma overlay (2,0 · 1024×601) — x:2'de başlar, canvas 1px taşar. PNG SSOT'ta karartma tam ekran ve 600px'de biter → PNG kazanır; overlay `inset: 0` uygulanır.
> **ÇELİŞKİ (navbar):** Figma katman adı "Göz At" ↔ TEXT metni "Sanatcılar" — tüm ekranlarda ortak; PNG kazanır (browse.md §1 ile aynı bulgu). Alt ekran bu spec kapsamında değil.
> **ÇELİŞKİ (tier viewport):** `00-device-matrix.md` L93: `T08 = 1280×800`; L295: `1024×600 = T07` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T08-embedded` korundu (§8'de tam metin).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Bağlan / Bağlantıyı Kes butonları | `.btn` | `--primary`, `--sm` | C04 |
| Bluetooth aç/kapa | `.switch` | `--on` | — ⚠️ VERIFICATION REQUIRED (envanterde karşılığı yok) |
| (ekran özel) | Quick panel kabuğu (312×308) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Cihaz satırı kartı (279×30 / 148×17) + tag pill'leri — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Cihaz ikon + Bt Tags grubu — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Panel arka planı (glass) | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Panel backdrop blur (BLUR 8) | `--cm-glass-blur-sm` | `var(--cm-glass-blur-sm)` |
| Overlay backdrop blur (BLUR 3) | `—` | token eşleşmesi yok → `⚠️ VERIFICATION REQUIRED` |
| Overlay karartma (#000 op.55) | `—` | token eşleşmesi yok → `⚠️ VERIFICATION REQUIRED` |
| Panel gölgesi | `--cm-glass-shadow` | `var(--cm-glass-shadow)` |
| Panel yarıçapı | `--cm-radius-md` | `var(--cm-radius-md)` |
| Switch / tag yarıçapı (r5 / pill) | `--cm-toggle-radius` | `var(--cm-toggle-radius)` |
| Switch açık + tag + buton (pembe) | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Odak halkası | `--cm-shadow-focus` | `var(--cm-shadow-focus)` |
| Z-seviyesi (quick panel) | `--cm-z-modal` | `var(--cm-z-modal)` |
| Dokunma hedefi | `--cm-touch-target` | `var(--cm-touch-target)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — ihlaller `04-accessibility-gaps.md`'e işlenmelidir |
| "Bağlan" 28×12 | 44×44 px | → GAP |
| "Bağlantıyı Kes" 44×15 | 44×44 px | genişlik tam, yükseklik 15 → GAP |
| Switch 24×10 (track 65×15) | 44×44 px | → GAP |
| Cihaz satırı 148×17 (bg 279×30) | 44×44 px | satır tıklama hedefi 30px → GAP |
| Yakınlık kuralı | ≥ 8 px boşluk | satırlar arası 17px (313→360, 30px kart) → PASS |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı — beyaz başlık/etiketler (koyu cam panel üstü) | ≥ 4.5:1 | PASS |
| 2 | Metin kontrastı — satır içi desc 8px beyaz üzerine koyu satır bg | ≥ 4.5:1 | PASS |
| 3 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 (`--cm-shadow-focus`) | PASS |
| 4 | Dokunma hedefi — Bağlan 28×12, switch 24×10, satır 30px | ≥ 44×44 px | GAP |
| 5 | Durum yalnız renkle anlatılmıyor — tag "Bağlı" / "Bağlı Değil" | Metin etiketi + renk (ikon/metin var) | PASS |
| 6 | ARIA gereklilikleri | `role="switch"` · `role="list"`/`listitem` · `aria-label` (Bağlan) · pil `aria-valuenow` | PASS |

> GAP kaydı: `04-accessibility-gaps.md` güncellemesi bu görevin kapsamı dışında (yalnızca 5 spec dosyası yazımı) — ⚠️ PENDING.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` (panel) | `var(--cm-glass-blur-sm)` — Figma BACKGROUND_BLUR 8 eşleşmesi birebir |
| `backdrop-filter` (overlay) | Figma BACKGROUND_BLUR 3 → `blur(3px)`; token eşleşmesi yok `⚠️ VERIFICATION REQUIRED` |
| Arka plan (overlay) | `#000 op.55` (Figma) — token eşleşmesi yok `⚠️ VERIFICATION REQUIRED` |
| Arka plan (panel) | `var(--cm-glass-bg)` — PNG'de koyu saydam cam |
| Kenarlık | `1px solid` → `var(--cm-glass-border)` (PNG'de ince ayraç sep) |
| Gölge | `var(--cm-glass-shadow)` |
| Fallback | `backdrop-filter` desteklenmezse → `background: var(--cm-glass-bg-strong)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png`
- **Klasör:** `home-1024/` (12 PNG) · Mockup indeksi: [[01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **Figma:** frame abs (1751,3113) · 1024×600 · overlay (2,0 · 1024×601) · panel (356,147 · 312×308) · connected `2831:9696` (371,249 · 148×17) · items y:313/360/407
- **İçerik (PNG SSOT):** 1 bağlı cihaz ("Km - 50" · "Kulaklık . Mükemmel sinyal: 100% . AAC . Pil : 100%") + 3 kullanılabilir cihaz: "Km - 50" (Şifresiz · Bağlı Değil) · "Car BT" (tag metinleri ⚠️ VERIFICATION REQUIRED) · "Samsung TV" (Pin Kodlu · Bağlı Değil · "Televizyon . Mükemmel sinyal: 100%").

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Panel konumu | Yatay ortalanmış: x=356 = (1024−312)/2 → 1280'de x=484; dikey sabit y=147 — wifi-quick ile aynı iskelet | `05-responsive-architecture` §7.4 (4K'da ortalamama) |
| Satır genişliği | bg 279px sabit (panel iç padding) — viewport artınca panel ortalanır, satır sabit | `05-responsive-architecture` §7.4 |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | `00-device-matrix` |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | `N/A (landscape-only)` — 1024×600 gömülü ekran | `reference/10-device-specific-guidelines` |
| **ÇELİŞKİ (tier viewport)** | `00-device-matrix.md` L93: `T08 = 1280×800 (RPi5 10")`; L295: `1024×600 = T07 (RPi5 7")` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T08-embedded` korundu. ⚠️ VERIFICATION REQUIRED — tier ataması owner onayı | `00-device-matrix` L93/L295 |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | panel açıldı | `var(--cm-glass-bg)` + `var(--cm-glass-blur-sm)` | `role="dialog"` |
| Pressed | dokunma (satır / buton) | `scale(.98)` + `var(--cm-glass-bg-active)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | bağlı cihaz satırı / switch açık | `var(--cm-pink-primary-button)` (bg #FFF op.18 — token yok ⚠️) | `aria-checked="true"` / `aria-selected="true"` |
| Disabled | Bluetooth kapalı (switch OFF) | satırlar `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | cihaz taraması sürüyor | spinner/bar `var(--cm-progressbar-fill)` | `aria-busy="true"` |

> T08 dokunmatik tier — `reference/09-interaction-states.md` gereği `Hover` satırı yok, `Pressed` kullanılır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-27
**Mode:** Red Team · Human Mode · Truth Mode
