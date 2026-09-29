---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Wifi Connect Light Screen Specification"
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
  authority: ".ai/ui-design/screens/T07-embedded/wifi-connect-light.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Wifi Connect Light.png"
---

# CoreMusic — Wifi Connect Light / Şifre Modalı (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌──────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024  │
│ y:0   ┌── OVERLAY "Wifi Passwd Div" (0,0 · 1024×601) ────────────┐  │
│       │ #000 op.55 · BACKGROUND_BLUR 3 → blur(3px)               │  │
│       │ (alt ekran = Ana Sayfa, karartma altında — kapsam dışı)  │  │
│ y:60  ├───────────────────────────────────────────────────────────┤  │
│       │                                                           │  │
│       │      ┌── MODAL (356,236 · 312×129) ──────────────────┐    │  │
│       │      │ BLUR 8 → var(--cm-glass-blur-sm)              │    │  │
│       │      │ gölge (0,4) blur 4 op.15                      │    │  │
│       │      │ başlık (368,244 · 93×14) "Bayram Ali - WIFI"  │    │  │
│       │      │  + lock 9×9 (452,246.5)                        │    │  │
│       │      │ desc (368,263) "5GHz . Mükemmel sinyal :      │    │  │
│       │      │  100% . Güvenli Bağlantı" (Arima 6.5)          │    │  │
│       │      │ label (367,293) "Kullanılacak Ağ Şifresi"      │    │  │
│ y:310  │      │ input (367,310 · 128×15) "*******"            │    │  │
│       │      │  stroke #F200D0 · fill #FF91EF                 │    │  │
│       │      │ checkbox (367,342 · 196×14):                   │    │  │
│       │      │  box 13×13 (367,343) · text (385,342)          │    │  │
│       │      │  "Kablosuz ağa her zaman otomatik bağlan"      │    │  │
│       │      │ İptal (566,343 · 43×14) · Bağlan (616,343 ·    │    │  │
│       │      │  43×14) — gap 7px · grup 93 = 43+7+43          │    │  │
│ y:365  │      └────────────────────────────────────────────────┘    │  │
│ y:510  │                                                           │  │
│ y:600  └───────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────┘
```

> **ÇELİŞKİ (overlay yüksekliği):** Figma overlay 1024**×601** — canvas 600px, 1px taşar. PNG'de tam ekran karartma görünür → PNG kazanır; overlay `inset: 0` (100%) olarak uygulanır.
> **ÇELİŞKİ (navbar):** Alt ekrandaki katman "Göz At" ↔ metin "Sanatcılar" (tüm ekranlarda ortak, browse.md §1). Alt ekran bu spec kapsamında değil — karartma altında.
> **ÇELİŞKİ (tier viewport):** `00-device-matrix.md` L93: `T08 = 1280×800`; L295: `1024×600 = T07` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı (owner onayı, matrix L92; §8'de tam metin).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| İptal / Bağlan butonları | `.btn` | `--secondary` (İptal), `--primary` (Bağlan), `--sm` | C04 |
| Karartma katmanı | `.overlay` | `--modal` | — ⚠️ VERIFICATION REQUIRED (envanterde karşılığı yok) |
| (ekran özel) | Modal kabuğu (312×129) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Şifre input + checkbox satırı — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Modal arka planı (glass) | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Modal backdrop blur (BLUR 8) | `--cm-glass-blur-sm` | `var(--cm-glass-blur-sm)` |
| Overlay backdrop blur (BLUR 3) | `—` | token eşleşmesi yok → `⚠️ VERIFICATION REQUIRED` |
| Overlay karartma (#000 op.55) | `—` | token eşleşmesi yok → `⚠️ VERIFICATION REQUIRED` |
| Modal gölgesi | `--cm-glass-shadow` | `var(--cm-glass-shadow)` |
| Bağlan butonu (pembe) | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Input stroke (pembe) | `--cm-primary` | `var(--cm-primary)` |
| Odak halkası | `--cm-shadow-focus` | `var(--cm-shadow-focus)` |
| Z-seviyesi (modal) | `--cm-z-modal` | `var(--cm-z-modal)` |
| Dokunma hedefi | `--cm-touch-target` | `var(--cm-touch-target)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — ihlaller `04-accessibility-gaps.md`'e işlenmelidir |
| İptal / Bağlan 43×14 | 44×44 px | 43×14 → GAP |
| Şifre input 128×15 | 44×44 px | yükseklik 15 → GAP |
| Checkbox 13×13 | 44×44 px | → GAP (etiket tıklanabilir alanı genişletilmeli) |
| Yakınlık kuralı | ≥ 8 px boşluk | İptal ↔ Bağlan arası **7px → GAP** (grup 93 = 43+7+43) |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı — beyaz 11px üzerine dolu pembe (Bağlan) | ≥ 4.5:1 (gövde), ≥ 3:1 (UI) | GAP — pembe gradient ~3:1 |
| 2 | Metin kontrastı — desc #DAD8D8 / beyaz üzerine koyu glass | ≥ 4.5:1 | PASS |
| 3 | Odak (focus) görünür + modal focus trap | 2px outline · `aria-modal="true"` | PASS |
| 4 | Dokunma hedefi — buton 43×14, input 15, checkbox 13 | ≥ 44×44 px | GAP |
| 5 | Etiket ilişkisi — input ↔ label, checkbox ↔ metin | `<label for>` / `aria-labelledby` | PASS (görsel eşleme PNG'de mevcut) |
| 6 | Yakınlık — buton arası 7px | ≥ 8 px | GAP |

> GAP kaydı: `04-accessibility-gaps.md` güncellemesi bu görevin kapsamı dışında (yalnızca 5 spec dosyası yazımı) — ⚠️ PENDING.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` (modal) | `var(--cm-glass-blur-sm)` — Figma BACKGROUND_BLUR 8 eşleşmesi birebir |
| `backdrop-filter` (overlay) | Figma BACKGROUND_BLUR 3 → `blur(3px)`; `--cm-glass-blur-sm` (8px) ile eşleşmiyor → token yok `⚠️ VERIFICATION REQUIRED` |
| Arka plan (overlay) | `#000 op.55` (Figma) — token eşleşmesi yok `⚠️ VERIFICATION REQUIRED` |
| Arka plan (modal) | `var(--cm-glass-bg)` (PNG'de saydam glass) |
| Gölge | Figma (0,4) blur 4 op .15 → `var(--cm-glass-shadow)` (0 4px 30px) yaklaşır; blur farkı → `⚠️ VERIFICATION REQUIRED` |
| Fallback | `backdrop-filter` desteklenmezse → `background: var(--cm-glass-bg-strong)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Wifi Connect Light.png`
- **Klasör:** `home-1024/` (12 PNG) · Mockup indeksi: [[01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **Figma:** frame abs (2869,3113) · 1024×600 · overlay "Wifi Passwd Div" (0,0 · 1024×601) · modal (356,236 · 312×129) · İptal `874:12340` · Bağlan `873:12335`
- **Alt ekran:** Ana Sayfa (PNG'de karartma altında görünür) — detay bu spec'in kapsamı dışında.

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Modal konumu | Yatay ortalanmış: x=356 = (1024−312)/2 → 1280'de x=484; dikey sabit y=236 | `05-responsive-architecture` §7.4 (4K'da ortalamama) |
| Overlay | Her viewport'ta `inset:0` tam ekran | `05-responsive-architecture` §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T07 → T17 → T25 → T29 → T31` | `00-device-matrix` |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | `N/A (landscape-only)` — 1024×600 gömülü ekran | `reference/10-device-specific-guidelines` |
| **ÇELİŞKİ (tier viewport)** | `00-device-matrix.md` L93: `T08 = 1280×800 (RPi5 10")`; L295: `1024×600 = T07 (RPi5 7")` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı. ✅ TAŞINDI — tier: T07 (owner onayı, matrix L92) | `00-device-matrix` L93/L295 |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | modal açıldı | `var(--cm-glass-bg)` + `var(--cm-glass-blur-sm)` | `role="dialog" aria-modal="true"` |
| Pressed | dokunma (Bağlan/İptal) | `scale(.98)` + `var(--cm-glass-bg-active)` | — |
| Focus | klavye / modal açılış odak input'ta | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | input odaklı (stroke vurgusu) | `var(--cm-primary)` stroke | `aria-invalid` |
| Disabled | şifre boş / uzunluk yetersiz | Bağlan `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | bağlanıyor | spinner/bar `var(--cm-progressbar-fill)` | `aria-busy="true"` |

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
