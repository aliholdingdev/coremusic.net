---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Wifi Quick Panel Screen Specification"
type: spec
category: ui-design
date: 2026-09-27
status: active
version: 1.1.0
tier: T07
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T07-embedded/wifi-quick.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png"
---

# CoreMusic — Wifi Quick Panel (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌──────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024  │
│ y:0   (HEADER y:0-60 + CONTENT + FOOTER y:510-600 — alt ekran       │
│        Ana Sayfa, soluk perde altında; overlay katmanı Figma         │
│        extracted'da belgelenmemiş → ⚠️ VERIFICATION REQUIRED)        │
│ y:60  ┌── CONTENT (y:60-510 · h:450) ──────────────────────────────┐ │
│       │      ┌── PANEL WIFI (356,147 · 312×308) ──────────────┐    │ │
│       │      │ header (371,157 · 77×23): icon (yeşil bars) ·   │    │ │
│       │      │  "WIFI" + "Ağ Bağlantıları"                     │    │ │
│ y:198 │      │ toggle (371,198 · 52×13): "WIFI" etiketi ·      │    │ │
│       │      │  Switch ON (399,199 · 24×10, pembe)             │    │ │
│ y:229 │      │ "Bağlı Olan Ağ" (371,229)                        │    │ │
│       │      │ connected (371,249 · 279×31):                    │    │ │
│       │      │  bg (371,249.356 · 279×30) #FFF op.18 r3        │    │ │
│       │      │  signal (381,256.356 · 15×15) · lock (379,254.356│    │ │
│       │      │   · 8×8) · name (404,255.356) "Bayram Ali - Home"│    │ │
│       │      │  + Wifi Tags (404,255.356 · 84×11)               │    │ │
│       │      │  desc (404,264.356) "5GHz . Mükemmel sinyal :    │    │ │
│       │      │   100% . 2.4GB Kullanıldı"                        │    │ │
│       │      │  "Bağlantıyı Kes" (599.5,256.9 · 44×15)          │    │ │
│ y:293 │      │ "Kullanılabilir Ağlar" (372,293)                   │    │ │
│       │      │ item 1 (371,313 · 279×31): name (404,319.356) ·   │    │ │
│       │      │  desc (404,328) "…Güvenli Bağlantı" ·             │    │ │
│       │      │  "Bağlan" (616,322.4 · 28×12)                     │    │ │
│       │      │ item 2 (371,359 · 279×31): name (404,365.356) ·   │    │ │
│       │      │  desc (404,374) · "Bağlan" (616,368.4 · 28×12)    │    │ │
│ y:436 │      │ item 3 (371,405 · 279×31): name (404,411.356) ·   │    │ │
│       │      │  desc (404,420) · "Bağlan" (616,414.4 · 28×12)    │    │ │
│ y:455 │      └────────────────────────────────────────────────────┘    │ │
│ y:510 └────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

> **ÇELİŞKİ (navbar):** Figma katman adı "Göz At" ↔ TEXT metni "Sanatcılar" — tüm ekranlarda ortak; PNG kazanır (browse.md §1 ile aynı bulgu). Alt ekran bu spec kapsamında değil.
> **ÇELİŞKİ (overlay):** bluetooth-quick'de overlay (#000 op.55 BLUR 3) tanımlı; wifi-quick extracted'ında overlay katmanı YOK, PNG'de alt ekran soluk/perdeli görünür → overlay varlığı `⚠️ VERIFICATION REQUIRED` (uydurulmadı).
> **ÇELİŞKİ (tier viewport):** `00-device-matrix.md` L93: `T08 = 1280×800`; L295: `1024×600 = T07` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı (owner onayı, matrix L92; §8'de tam metin).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Bağlan / Bağlantıyı Kes butonları | `.btn` | `--primary`, `--sm` | C04 |
| WIFI aç/kapa | `.switch` | `--on` | — ⚠️ VERIFICATION REQUIRED (envanterde karşılığı yok) |
| (ekran özel) | Quick panel kabuğu (312×308) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Ağ satırı kartı (279×31) + tag pill'leri — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Sinyal/lock ikon grubu — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Panel arka planı (glass) | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Panel backdrop blur (BLUR 8) | `--cm-glass-blur-sm` | `var(--cm-glass-blur-sm)` |
| Panel gölgesi | `--cm-glass-shadow` | `var(--cm-glass-shadow)` |
| Panel yarıçapı | `--cm-radius-md` | `var(--cm-radius-md)` |
| Switch yarıçapı | `--cm-toggle-radius` | `var(--cm-toggle-radius)` |
| Switch açık (pembe) | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Bağlan butonu (pembe) | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Odak halkası | `--cm-shadow-focus` | `var(--cm-shadow-focus)` |
| Z-seviyesi (quick panel) | `--cm-z-modal` | `var(--cm-z-modal)` |
| Satır bg (#FFF op.18 r3) | `—` | token eşleşmesi yok → `⚠️ VERIFICATION REQUIRED` |
| Dokunma hedefi | `--cm-touch-target` | `var(--cm-touch-target)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — ihlaller `04-accessibility-gaps.md`'e işlenmelidir |
| "Bağlan" 28×12 | 44×44 px | → GAP |
| "Bağlantıyı Kes" 44×15 | 44×44 px | genişlik tam, yükseklik 15 → GAP |
| Switch 24×10 (track 52×13) | 44×44 px | → GAP (track tamamansa 52×13 yine < 44 h) |
| Ağ satırı 279×31 | 44×44 px | satır tıklama hedefi 31px → GAP |
| Yakınlık kuralı | ≥ 8 px boşluk | satırlar arası 15px (313→359, 31px kart) → PASS |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı — beyaz başlık/etiketler (panel glass üstü) | ≥ 4.5:1 | PASS |
| 2 | Metin kontrastı — satır içi desc (6.5-8px, soluk beyaz pembe üstü açık satır bg) | ≥ 4.5:1 | GAP — ölçüm `⚠️ VERIFICATION REQUIRED`, güvenli taraf GAP |
| 3 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 (`--cm-shadow-focus`) | PASS |
| 4 | Dokunma hedefi — Bağlan 28×12, switch 24×10, satır 31px | ≥ 44×44 px | GAP |
| 5 | Durum yalnız renkle anlatılmıyor — WIFI switch (açık/kapalı) | Konum + `role="switch"` + `aria-checked` | PASS |
| 6 | ARIA gereklilikleri | `role="switch"` · `role="list"`/`listitem` (agrid) · `aria-label` (Bağlan) | PASS |

> GAP kaydı: `04-accessibility-gaps.md` güncellemesi bu görevin kapsamı dışında (yalnızca 5 spec dosyası yazımı) — ⚠️ PENDING.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` (panel) | `var(--cm-glass-blur-sm)` — Figma BACKGROUND_BLUR 8 eşleşmesi birebir |
| `backdrop-filter` (overlay) | extracted'da overlay yok; PNG'de soluk perde → varsa `blur(3px)` + `⚠️ VERIFICATION REQUIRED` |
| Arka plan (panel) | `var(--cm-glass-bg)` + PNG'de saydam cam |
| Kenarlık | `1px solid` → `var(--cm-glass-border)` (PNG'de ince ayraç) |
| Gölge | `var(--cm-glass-shadow)` |
| Ağ satırı bg | Figma #FFF op.18 r3 → token eşleşmesi yok `⚠️ VERIFICATION REQUIRED` |
| Fallback | `backdrop-filter` desteklenmezse → `background: var(--cm-glass-bg-strong)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png`
- **Klasör:** `home-1024/` (12 PNG) · Mockup indeksi: [[ui-design/01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **Figma:** frame abs (640,3106) · 1024×600 · panel "Wifi Quick" (356,147 · 312×308) · connected bg (371,249.356 · 279×30) · items y:313/359/405 · Switch (399,199 · 24×10)
- **İçerik (PNG SSOT):** 1 bağlı ağ ("Bayram Ali - Home" · desc "…2.4GB Kullanıldı") + 3 kullanılabilir ağ (hepsi "Bayram Ali - Home" · desc "…Güvenli Bağlantı").

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Panel konumu | Yatay ortalanmış: x=356 = (1024−312)/2 → 1280'de x=484; dikey sabit y=147 | `05-responsive-architecture` §7.4 (4K'da ortalamama) |
| Satır genişliği | 279px sabit (panel iç padding) — viewport artınca satır sabit, panel ortalanır | `05-responsive-architecture` §7.4 |
| Tier sıçraması | `T01 → T03 → T07 → T17 → T25 → T29 → T31` | `00-device-matrix` |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | `N/A (landscape-only)` — 1024×600 gömülü ekran | `reference/10-device-specific-guidelines` |
| **ÇELİŞKİ (tier viewport)** | `00-device-matrix.md` L93: `T08 = 1280×800 (RPi5 10")`; L295: `1024×600 = T07 (RPi5 7")` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı. ✅ TAŞINDI — tier: T07 (owner onayı, matrix L92) | `00-device-matrix` L93/L295 |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | panel açıldı | `var(--cm-glass-bg)` + `var(--cm-glass-blur-sm)` | `role="dialog"` |
| Pressed | dokunma (satır / buton) | `scale(.98)` + `var(--cm-glass-bg-active)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | bağlı ağ satırı / switch açık | `var(--cm-pink-primary-button)` (bg #FFF op.18 — token yok ⚠️) | `aria-checked="true"` / `aria-selected="true"` |
| Disabled | WIFI kapalı (switch OFF) | satırlar `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | ağ taraması sürüyor | spinner/bar `var(--cm-progressbar-fill)` | `aria-busy="true"` |

> T07 dokunmatik tier — `reference/09-interaction-states.md` gereği `Hover` satırı yok, `Pressed` kullanılır.

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Sections (§1-§9) | 9/9 |
| Version | 1.1.0 |
| Tier düzeltmesi | tier T08→T07 düzeltildi (matrix L92) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
