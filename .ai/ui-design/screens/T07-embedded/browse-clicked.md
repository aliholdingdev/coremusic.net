---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Browse Clicked Screen Specification"
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
  authority: ".ai/ui-design/screens/T07-embedded/browse-clicked.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png"
---

# CoreMusic — Browse Clicked / Dosya Listesi (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌──────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024  │
│ y:0   ┌── HEADER (y:0-60 · h:60) ─────────────────────────────────┐  │
│       │ NAVBAR (x:16, y:13 · w:993, h:27)                         │  │
│ y:60  └────────────────────────────────────────────────────────────┘  │
│ y:60  ┌── CONTENT (y:60-510 · h:450 · var(--cm-content-h)) ────────┐  │
│       │ ┌ NAVIGATE (21,69 · w:743, h:44) ────────────────────────┐ │  │
│       │ │ geri (27,81 · 40×40) · ileri (69,81 · 40×40)           │ │  │
│       │ │ başlık (114,92 · 229×18, SF Pro 15)                    │ │  │
│       │ │  "Dosya Yöneticisi / Musics : Root"                    │ │  │
│       │ │ url (348,93.5 · 220×15, Poppins 10 RIGHT)              │ │  │
│       │ │  "c:\users\Bayram Ali\Music"                           │ │  │
│       │ │ Search (585,89 · 159×25 r4): "Dosya Ara" (590,95) ·    │ │  │
│       │ │  icon 15×15 (723,94)                                    │ │  │
│       │ └─────────────────────────────────────────────────────────┘ │  │
│       │ ┌ PLAYLIST (17,113 · w:743, h:381) ──────────────────────┐ │  │
│       │ │ scrollbar (183,137 · 3×341)                             │ │  │
│       │ │ explorer (31,140 · 144×342): klasör ağacı —            │ │  │
│       │ │  seçili "USB Disk (E:)" (PNG)                          │ │  │
│       │ │ label bar (206,137 · 554×20, #383838 op.35):           │ │  │
│       │ │  "Şarkı Adı " (216,139) · "Album Adı" (419,139) ·      │ │  │
│       │ │  "Sanatcı" (565,139) · "Süre" (698,140)                │ │  │
│       │ │ Items (216,167 · 514×109, VERTICAL gap=15):            │ │  │
│       │ │  satır 1 (216,167 · 514×16) "Pop Şarkılar Ali" (klasör)│ │  │
│       │ │  satır 2 (216,198 · 514×16) "Göksel - Sevil Neşelen"   │ │  │
│       │ │  satır 3 (216,229 · 514×16) "Göksel - Sevil Neşelen"   │ │  │
│       │ │  satır 4 (216,260 · 514×16) "Göksel - Sevil Neşelen"   │ │  │
│       │ │  (her satırda "selcted" 555×30 @ (206,y-7) —           │ │  │
│       │ │   Selected Item=False → PNG'de GÖRÜNMEZ)               │ │  │
│       │ └─────────────────────────────────────────────────────────┘ │  │
│       │ ┌ SAĞ PANEL (785,71 · w:253, h:420 · r8 · op:.212) ─────┐ │  │
│       │ │ header (800,85 · 175×19): diskadi "USB Disk (E:)" ·    │ │  │
│       │ │  diskmodel "Sandisk Cuzer Balde 2.0" · ikon 12×19 ·    │ │  │
│       │ │  tag (2637→886,97 · 18×7)                               │ │  │
│       │ │ tags y:97 @ x:886, 906, 926, 953                        │ │  │
│       │ │ sep (785,113 · 253×1) · donut (800,130 · 80×80) ·       │ │  │
│       │ │ metadata (901,135 · 85×69) · url bar (797,224 · 192×15)│ │  │
│       │ │ folder grid (809,286 · 144×110) · sep (785,385) ·       │ │  │
│       │ │ multi-donut (800,396 · 80×80) + legend (888,402-455)    │ │  │
│       │ │ (sağ kenar taşması: 785+253=1038 > 1024 — Figma koord.) │ │  │
│       │ └─────────────────────────────────────────────────────────┘ │  │
│ y:510 └────────────────────────────────────────────────────────────┘  │
│ y:510 ┌── FOOTER (y:510-600 · h:90 · var(--cm-footer-h-embedded)) ┐  │
│       │ Footer (0,510 · 1025×90)                                  │  │
│ y:600 └────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────┘
```

> **ÇELİŞKİ (navbar):** Figma katman adı "Göz At" ↔ TEXT metni "Sanatcılar". SSOT sırası PNG > Figma: **PNG'de görünen metin "Sanatcılar"** — katman adı typo'dur (browse.md ile aynı bulgu).
> **ÇELİŞKİ (seçim göstergesi):** Figma'da 4 liste satırının hepsinde 555×30 "selcted" bg katmanı tanımlı (SOLID #BBB op.25 / #FF00C8 op.35 / #808080 op.15) ama tüm satırlarda `Selected Item=False` → render'da kapalı. PNG SSOT'ta liste satırı seçimi görünmüyor; seçim "USB Disk (E:)" explorer öğesinde → PNG kazanır. Spec: liste seçimi varsayılan kapalı, explorer seçimi gradient. (Ham hex Figma katmanından — token eşleşmesi `⚠️ VERIFICATION REQUIRED`.)
> **Gizli katmanlar (PNG SSOT):** Figma'da tanımlı ama PNG'de görünmeyen katmanlar spec'e alınmadı: "Hemen Çal" (809,276 · v5'te yok), "Karışık Çal" (809,316), "…" (809/899,356), Actions forward (91,81).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Navbar öğeleri | `.nav-link` | `—` | C01 |
| Geri / ileri + Search aksiyonu | `.btn` | `--primary`, `--sm` | C04 |
| Sağ panel (varsa) butonları | `.btn` | `--secondary`, `--sm` | C04 |
| (ekran özel) | Navigate bar (743×44) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Playlist tablosu (label + 4 satır) + explorer — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |
| (ekran özel) | Sağ panel (disk/istatistik) — envanterde karşılığı yok | — | — ⚠️ VERIFICATION REQUIRED |

> **ÇELİŞKİ (envanter):** Liste tablosu ve explorer için envanterde (C01-C19) birebir sınıf yok; satır stiline en yakın bileşendirme `C03 (Card)`'tır. Sınıf adları envanter dışı uydurulmaz → envanter güncellemesi gerekir (⚠️ PENDING: `02-component-inventory.md` kapsam dışı).

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| İçerik bandı yüksekliği | `--cm-content-h` | `var(--cm-content-h)` |
| Footer bandı yüksekliği | `--cm-footer-h-embedded` | `var(--cm-footer-h-embedded)` |
| Panel/liste satırı arka planı | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Search input arka planı | `--cm-glass-bg-strong` | `var(--cm-glass-bg-strong)` |
| Panel kenarlığı | `--cm-glass-border` | `var(--cm-glass-border)` |
| Panel gölgesi | `--cm-glass-shadow` | `var(--cm-glass-shadow)` |
| Panel yarıçapı (r8) | `--cm-radius-md` | `var(--cm-radius-md)` |
| Dokunma hedefi | `--cm-touch-target` | `var(--cm-touch-target)` |
| Odak halkası | `--cm-shadow-focus` | `var(--cm-shadow-focus)` |
| Explorer/aktivite vurgu | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Listede kullanım (token yok) | `—` | `⚠️ VERIFICATION REQUIRED` (Figma: opaklık katmanlı düz renk) |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — ihlaller `04-accessibility-gaps.md`'e işlenmelidir |
| Geri / ileri 40×40 | 44×44 px | 40 < 44 → GAP |
| Search input 159×25 | 44×44 px | yükseklik 25 < 44 → GAP |
| Liste satırı 514×16 (pitch 31) | 44×44 px | satır tam tıklama hedefi değil → GAP |
| Explorer satırları | 44×44 px | ölçü `⚠️ VERIFICATION REQUIRED` |
| Yakınlık kuralı | ≥ 8 px boşluk | label bar ↔ satır 10px (137→167 arası 30) → PASS |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı — koyu zemin üstü beyaz 10-11.5px | ≥ 4.5:1 | PASS |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 (`--cm-shadow-focus`) | PASS |
| 3 | Dokunma hedefi — geri/ileri 40×40, satır 16px, search 25px | ≥ 44×44 px | GAP |
| 4 | Durum yalnız renkle anlatılmıyor — seçili satır/explorer | İkon/kalınlık + `aria-selected` | GAP |
| 5 | Okuma sırası / DOM sırası | Görsel sıra = DOM (navbar → navigate → liste → panel → footer) | PASS |
| 6 | ARIA gereklilikleri | `role="tree"` (explorer) · `role="table"` + `aria-selected` (satır) · `aria-label` (geri/ileri) | PENDING — `04-accessibility-gaps.md` kapsam dışı |

> GAP kaydı: `04-accessibility-gaps.md` güncellemesi bu görevin kapsamı dışında (yalnızca 5 spec dosyası yazımı) — ⚠️ PENDING.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | Navigate/Playlist/Sağ Panel → `var(--cm-glass-blur)` (Figma gruplarında blur efekti yok → `none` da kabul; ⚠️ VERIFICATION REQUIRED) |
| Arka plan | `var(--cm-glass-bg)` + Figma grup `opacity: 0.212` |
| Kenarlık | `1px solid` → `var(--cm-glass-border)` |
| Gölge | `var(--cm-glass-shadow)` — Figma: DROP_SHADOW (0,0.5-1) blur 1 `#000` op .8 + Sağ Panel INNER_SHADOW |
| Seçili satır | Figma katman opaklık-yığını → `var(--cm-glass-gradient)` eşleşmesi `⚠️ VERIFICATION REQUIRED` |
| Fallback | `backdrop-filter` desteklenmezse → `background: var(--cm-glass-bg-strong)` (solid) |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png`
- **Klasör:** `home-1024/` (12 PNG) · Mockup indeksi: [[01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
- **Figma:** frame `2831:10282` (v5, abs 1751,2186 · 1024×600) · Items `2831:10359` · label `2831:10364` · Search `2831:10370` · Actions `2831:10374` · Sağ Panel `2831:10380`

**Varyant karşılaştırma (Figma 5 varyant — §7):**

| # | Node ID | Header / Liste | Sağ panel | Footer | Fark (v5'e göre) |
|---|---------|----------------|-----------|--------|------------------|
| v1 | `1976:11757` | "Son Eklenenler" · 514×109 (219,167) | **YOK** | (−20,510) konum farklı | panel yok, footer kaymış |
| v2 | `1976:12013` | "Son Dinlenler" (typo) · 514×171 (219,164) | 219×420 (784,71) | görünür | playlist başlığı var |
| v3 | `1980:13448` | "Favoriler" / "Son Dinlenenler Listesi" | v2 paneli | görünür | başlık metni değişimi |
| v4 | `1980:13692` | 4 playlist satırı | v2 paneli | görünür | satırlar: Super Mixim 10:00:00 · Duygusal 01:00:00 · Ararbesk 02:00:00 · Ali Pop 00:50:00 |
| **v5** | `2831:10282` | "Musics : Root" dosya listesi | 253×420 (785,71) disk/istatistik | görünür | — (spec = v5) |

- v2–v4 ortak sağ panel: album art 100×100 (844,106) r50 · "Sibel Can" (809,223) · "Türkçe Pop" (809,244) · "Hemen Çal" (809,276) · "Karışık Çal" (809,316) · "…" (898,353).
- **Spec = v5 (en güncel).** "Hemen Çal" v5'te yok → spec'e alınmadı (gizli katman, PNG SSOT).

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | 1280→1024: sol panel 760→743 (0.9775) · sağ panel 438→253 (42% shrink; taşma: 785+253=1038 > 1024) | `05-responsive-architecture` §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T07 → T17 → T25 → T29 → T31` | `00-device-matrix` |
| Grid davranışı | Liste 4 kolon (Şarkı Adı / Album Adı / Sanatcı / Süre) korunur | `05-responsive-architecture` §7.4 |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | `N/A (landscape-only)` — 1024×600 gömülü ekran | `reference/10-device-specific-guidelines` |
| **ÇELİŞKİ (tier viewport)** | `00-device-matrix.md` L93: `T08 = 1280×800 (RPi5 10")`; L295: `1024×600 = T07 (RPi5 7")` — bu spec `viewport: 1024x600` (PNG kanıtı), dizin `T07-embedded`'e taşındı. ✅ TAŞINDI — tier: T07 (owner onayı, matrix L92) | `00-device-matrix` L93/L295 |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `var(--cm-glass-bg)` + `var(--cm-glass-border)` | — |
| Pressed | dokunma | `scale(.98)` + `var(--cm-glass-bg-active)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-primary)` | `:focus-visible` |
| Active | satır / explorer seçimi | `var(--cm-pink-primary-button)` (Figma: opaklık-yığını — ⚠️ VERIFICATION REQUIRED) | `aria-selected="true"` |
| Disabled | disk yok / yetki yok | `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | liste yükleniyor | skeleton (C13) / progress `var(--cm-progressbar-fill)` | `aria-busy="true"` |

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
