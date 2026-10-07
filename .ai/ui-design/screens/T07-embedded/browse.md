---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Browse Screen Specification"
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
  authority: ".ai/ui-design/screens/T07-embedded/browse.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Göz At Page.png"
---

# CoreMusic — Browse (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌──────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024  │
│ y:0   ┌── HEADER (y:0-60 · h:60) ─────────────────────────────────┐  │
│       │ NAVBAR (x:16, y:17 · w:993, h:27)                         │  │
│       │  Logo "Core Music" (16,20 · 82×29, Respective 16px)        │  │
│       │  nav (y:27, Avalon 10px): Ana Sayfa 118 · Keşfet 196 ·    │  │
│       │   Albumler 249 · Sanatcılar 312 · Göz At 389 ·            │  │
│       │   Geçmiş 448 · Ayarlar 506 · Hakkımızda 570               │  │
│       │  pill grubu (756,21 · 268×24): Profile 90×24 (756,21) ·    │  │
│       │   Wifi+BT 44×23 (850,21) · Battery 47×23 (898,21) ·       │  │
│       │   Logout 75×23 (949,21) · bg pill #FFF op.212 r50          │  │
│ y:60  └────────────────────────────────────────────────────────────┘  │
│ y:60  ┌── CONTENT (y:60-510 · h:450 · var(--cm-content-h)) ────────┐  │
│       │ ┌ DISKS (23,71 · w:743, h:424 · r8 · op:.212) ──────────┐ │  │
│       │ │ geri (33,81 · 40×40) · başlık (78,92 · 159×18)         │ │  │
│       │ │  "Dosya Yöneticisi / Disk" (SF Pro 15)                  │ │  │
│       │ │ başlık (39,138): "Sistem Diskleri" (SF Pro 13)          │ │  │
│       │ │  disk y:169 — 160×44 @ x:52 (System Disk),             │ │  │
│       │ │                        x:261 (NAS Drive)                │ │  │
│       │ │ başlık (39,248): "Harici / Taşınabilir Diskleri"        │ │  │
│       │ │  disk y:279 — 160×44 @ x:52 (HDD Drive),                │ │  │
│       │ │   x:261 (SSD Nvme 2 Drive), x:470 (SSD Drive)           │ │  │
│       │ │ başlık (39,358): "Çıkarılabilir Diskleri"               │ │  │
│       │ │  disk y:389 — 160×44 @ x:52 (USB Drive),                │ │  │
│       │ │   x:261 (USB Drive), x:470 (CD DVD Drive)               │ │  │
│       │ │  her disk: bg 180×50 r5 · icon 36×37 (x+4, y+7.3) ·    │ │  │
│       │ │   ad Poppins 10 (x+43, y+9) · progress 115×8            │ │  │
│       │ │   (x+43, y+29.3 · value:52.207)                         │ │  │
│       │ └──────────────────────────────────────────────────────────┘ │  │
│       │ ┌ SAĞ PANEL (783,70 · w:219, h:420 · r8 · op:.212) ──────┐ │  │
│       │ │ "System Disk" (798,85 · 191×11, Poppins 10/600)         │ │  │
│       │ │ "Hard Disk . Dahili Disk" (798,101 · Poppins 8)         │ │  │
│       │ │ sep (783,122 · 219×1) · sep (783,181 · 219×1) ·         │ │  │
│       │ │  sep (783,231 · 219×1) — #FF00C8 op.1                   │ │  │
│       │ │ icon 35×35 (798,132) · "32 GB" (842,132 · 45×11)        │ │  │
│       │ │ progress 147×5 (842,148 · value:66.734)                 │ │  │
│       │ │ "16 GB Kullanılabilir" (842,158 · 94×11) ·              │ │  │
│       │ │  "%50" (966,158 · 23×11, RIGHT)                         │ │  │
│       │ │ stats ikon 15×15 (y:194) @ x:803,844,885,926,967 ·      │ │  │
│       │ │  etiket y:214/220 @ x:801,841,882,923,964               │ │  │
│       │ │  (4.521/Şarkı · 100/Album · 150/Sanatcı · 50/Video ·    │ │  │
│       │ │   20/Radio) · dikey ayırıcı 1×34 x:830,871,912,953 y:190│ │  │
│       │ │ btn 169×25 x:809 — y:252 "Göz At" (grad #FF00C8) ·      │ │  │
│       │ │  y:287 "Bütün Şarkıları Çal" · y:322 "Şarkıları Göz At"│ │  │
│       │ │  y:357 "Sanatcıları Göz At" · y:392 "Videoları Göz At"  │ │  │
│       │ │ "…" ×3 (49.5×28) x:809,869,928 · y:437                  │ │  │
│       │ └──────────────────────────────────────────────────────────┘ │  │
│ y:510 └────────────────────────────────────────────────────────────┘  │
│ y:510 ┌── FOOTER (y:510-600 · h:90 · var(--cm-footer-h-embedded)) ┐  │
│       │ progress (0,510 · 1025×4.8 · value:438.428)               │  │
│       │ player btns 296.7×62 (453,533) · süre/bitrate satırı        │  │
│ y:600 └────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────┘
```

> **ÇELİŞKİ (navbar):** Figma katman adı "Göz At" (x:312) ↔ TEXT metni "Sanatcılar". SSOT sırası PNG > Figma: **PNG'de görünen metin "Sanatcılar"** — katman adı typo'dur; x:389'daki ikinci öğe ("Göz At") ile çelişmez.
> **Gizli katmanlar (PNG SSOT):** Figma Navigate Div (21,69) altındaki fwd/home/URL/Search elemanları ile Actions Btns fwd (91,81) PNG'de görünmüyor — bu spec'e alınmadı.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar öğeleri | `.nav-link` | `—` | C01 |
| Geri / aksiyon butonu | `.btn` | `--primary`, `--sm` | C04 |
| Sağ panel aksiyonları | `.btn` | `--primary`, `--secondary`, `--ghost`, `--sm` | C04 |
| Disk doluluk + footer progress | `.progress` | `__bar`, `__fill` | C15 |
| (ekran özel) | Disk kartı (Disks) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Sağ panel kartı + "…" menü — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| İçerik bandı yüksekliği | `--cm-content-h` | `var(--cm-content-h)` |
| Footer bandı yüksekliği | `--cm-footer-h-embedded` | `var(--cm-footer-h-embedded)` |
| Kart arka planı (glass) | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Kart kenarlığı | `--cm-glass-border` | `var(--cm-glass-border)` |
| Kart gölgesi | `--cm-glass-shadow` | `var(--cm-glass-shadow)` |
| Kart yarıçapı (r8) | `--cm-radius-md` | `var(--cm-radius-md)` |
| Dokunma hedefi | `--cm-touch-target` | `var(--cm-touch-target)` |
| "Göz At" gradient butonu | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Disk progress value | `--cm-progressbar-value-fill` | `var(--cm-progressbar-value-fill)` |
| Progress tick orta stop | `--cm-progressbar-tick-stop-1` | `var(--cm-progressbar-tick-stop-1)` |
| Progress tick kenar stop | `--cm-progressbar-tick-stop-0` | `var(--cm-progressbar-tick-stop-0)` |
| Panel Stroke Effect | `--cm-stroke-effect-fill` | `var(--cm-stroke-effect-fill)` |
| Pembe vurgu (nav/footer) | `--cm-primary` | `var(--cm-primary)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — bu ekrandaki ihlaller `04-accessibility-gaps.md`'e işlenmelidir |
| Disk butonu 160×44 | 44×44 px | Tam sınırda → PASS |
| `.btn` (sağ panel 169×25 · "…" 49.5×28 · geri 40×40) | 44×44 px | 25-40px arası → GAP |
| Yakınlık kuralı | ≥ 8 px boşluk | Sağ panel buton arası 10px (y:252→287) → PASS |
| Komut satırı | `touch target >= 44px (tier embedded/phone)` | Prompt `constraints` ile aynı ifade |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı — beyaz 11px üzerine pembe gradient buton | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) | GAP |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 (`--cm-shadow-focus`) | PASS |
| 3 | Dokunma hedefi — panel 169×25, "…" 49.5×28, geri 40×40 | ≥ 44×44 px | GAP |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası (navbar → disks → panel → footer) | PASS |
| 5 | Durum yalnız renkle anlatılmıyor — disk doluluk yalnız progress rengi | İkon/metin + aria (`aria-valuenow`) | GAP |
| 6 | ARIA gereklilikleri | `aria-current="page"` (nav) · `aria-label` (disk butonu) · `aria-valuenow` (progress) | PASS |

> GAP kaydı: `04-accessibility-gaps.md` güncellemesi bu görevin kapsamı dışında (yalnızca 5 spec dosyası yazımı) — ⚠️ PENDING.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | Figma'da Disks/Sağ Panel gruplarında blur efekti **yok** → `none` (glass blur istenirse `var(--cm-glass-blur)`; ⚠️ VERIFICATION REQUIRED) |
| Arka plan | `var(--cm-glass-bg)` + Figma grup `opacity: 0.212` |
| Kenarlık | `1px solid` → `var(--cm-glass-border)` |
| Gölge | `var(--cm-glass-shadow)` — Figma: DROP_SHADOW (0,1) blur 1 `#000` op .8 |
| Fallback | `backdrop-filter` desteklenmezse → `background: var(--cm-glass-bg-strong)` (solid) |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Göz At Page.png`
- **Klasör:** `home-1024/` (12 PNG) · Alternatif açı: `.ai/.png/home-1920/Linux - 1920 - Home.png`
- **Mockup indeksi:** [[ui-design/01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **Figma:** frame `2831:9555` "Linux  1024 - Göz At Page" (typo yok) · Disk Buttons `18:3607` ailesi · Navigate `891:9227` (gizli alt elemanlar dâhil)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | Tier aralığında yeniden akış / sabit grid — Disks 3-sütun disk grid'i, Sağ Panel sabit 219px | `05-responsive-architecture` §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T07 → T17 → T25 → T29 → T31` | `00-device-matrix` |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | `N/A (landscape-only)` — 1024×600 gömülü ekran | `reference/10-device-specific-guidelines` |
| **ÇELİŞKİ (tier viewport)** | `00-device-matrix.md` L93: `T08 = 1280×800 (RPi5 10")`; L295: `1024×600 = T07 (RPi5 7")` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı. ✅ TAŞINDI — tier: T07 (owner onayı, matrix L92) | `00-device-matrix` L93/L295 |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` + `var(--cm-glass-bg)` | — |
| Pressed | dokunma | `scale(.98)` + `var(--cm-glass-bg-active)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | seçim (nav / disk butonu) | `font-weight:600` + `var(--cm-pink-primary-button)` | `aria-current="page"` |
| Disabled | yetki/veri yok | `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | veri bekleniyor | skeleton (C13) / spinner | `aria-busy="true"` |

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
