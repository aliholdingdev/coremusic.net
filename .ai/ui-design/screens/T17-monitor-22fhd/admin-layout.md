---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Admin Layout Screen Specification"
type: spec
category: ui-design
date: 2026-10-16
status: active
version: 1.0.0
tier: T17
viewport: 1920x1080
device: 22" FHD Monitor (Desktop)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/admin-layout.md"
  source_of_truth: ".ai/.png/admin-1920/Admin - layout.png"
---

# CoreMusic — Admin Layout (T17 Desktop Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                     x:1920 │
│ y:0     ┌─ SIDEBAR (x:0-250 · w:250 · h:1080 · %13) ─┐ ┌─ ÜST BAND (y:0-78 · h:78 · %7) ───────────────────┐ │
│         │ zemin: koyu cam (siyah/%20)                │ │ NAVBAR x:1623 y:18 w:296 h:35 (%15w) zemin siyah/%20│ │
│         │ "Menu" @x:9 y:12 (9px · 22×12)             │ │ avatar 20×20 @x:1630 y:25 · "Bayram Ali - admin" 7px │ │
│         │ ▸ Music Sitesi Ayarları y:42 h:13 (8px)    │ │ wifi 11×12 · bt 11×12 · battery 44×16 "100%" (5px)   │ │
│         │ ▸ Admin Paneli Ayarları y:56 h:13          │ │ logout 15×16 @x:1898 y:26 (r:13)                     │ │
│ y:78    ├─────────────────────────────────────────────┤ ├─ İÇERİK PANELİ (y:78 · h:940 · %87) ───────────────┤ │
│         │ "Şarkılar" @x:9 y:109 (9px)                │ │ x:268 y:78 w:1631 h:940 (%85w) r:10 · beyaz/%21      │ │
│         │ ▸ Şarkı listesi    y:122 h:13 (ikon 13×13) │ │ [Figma HIDDEN rect — planlanan panel zemini]          │ │
│         │ ▸ Şarkı Ekle       y:136 h:13              │ │ "Admin Panelim" @x:297 y:102 (Arial 14 · 117×16)     │ │
│         │ ▸ Playlist Oluştur y:150 h:13              │ │ "Home / Dashboard" @x:1749 y:102 [HIDDEN]            │ │
│         │ (menü bitti — Figma'da başka satır yok)    │ │ içerik kartları → PNG'ye göre doldurulacak            │ │
│ y:1018  └─────────────────────────────────────────────┘ └────────────────────────────────────────────────────┘ │
│ y:1018  BOŞLUK (h:22 · %2): sidebar dikey devam eder · içerik paneli ile footer arasında 22px pay           │
│ y:1040  ┌─ FOOTER (x:1053-1420 · w:367 · h:29 · %19) ────────────────────────────────────────────────────┐   │
│         │ "Username : Bayram Ali" · "IP : 95.95.0.201" · "User Type : Administartor" @y:1060 (9px)       │   │
│         │ "Copyright © 2024-2025 Core Music …" @x:1456 y:1053 w:467 (Respective 18px)                     │   │
│ y:1080  └─────────────────────────────────────────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Yükseklik toplamı:** 78 (üst band) + 940 (içerik) + 22 (boşluk) + 40 (footer bandı, y:1040-1080) = **1080** ✓

- **Kaynak:** Figma `2161:12499` "Admin - layout" · çıkarım [[ui-design/reference/figma/extracted-admin-1920]] §4 (göreli koordinatlar) · ham `reference/figma/raw/page-2161-12438.json`.
- **Öncelik:** PNG > ASCII > Inventory > Tokens > Reference — sidebar/navbar/footer ölçüleri Figma'dan, görünüm `.ai/.png/admin-1920/Admin - layout.png`'den gelir.
- **Tier notu:** viewport 1920×1080 + CatID `DM-T17` (matrix §4c: `2161:12438` → 1920 Figma sayfaları) ile **T17** atandı; matrix'teki `T42 NAS Web Interface — Layout: Admin panel` satırıyla semantik çelişki → **açık madde** (`screens/00-ascii-art-index.md` §7 madde 5).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Sidebar menü satırı | `.nav-link` | `__icon`, `--active`, `--hover` | C01 |
| Navbar profil | `.avatar` | `--sm`, `--with-image` | C11 |
| Navbar durum rozeti | `.badge` | `--primary`, `--with-count` | C10 |
| Sidebar kabuğu | `.admin-sidebar` | `__title`, `__group`, `__item` | — |
| İçerik paneli | `.admin-content` | `__panel`, `__title`, `__breadcrumb` | — |
| Footer | `.admin-footer` | `__meta`, `__meta--user`, `__copyright` | — |
| Arka plan | `.admin-bg` | `__image`, `__scrim` | — |

> ⚠️ **VERIFICATION REQUIRED:** `.admin-sidebar` / `.admin-content` / `.admin-footer` / `.admin-bg` envanterde (C01-C19) karşılığı olmayan **ekran özel** block'lardır — `Cxx` eşlemesi bileşen spec'i açıldığında yapılır; uydurma `Cxx` yazılmaz.

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Aktif/hover vurgu | `--cm-primary` | `var(--cm-primary)` |
| Birincil metin | `--cm-text-primary` | `var(--cm-text-primary)` |
| Sidebar ikincil metin | `--cm-text-secondary` | `var(--cm-text-secondary)` |
| Koyu cam katman (sidebar/navbar) | `--cm-bg-scrim` | `var(--cm-bg-scrim)` |
| İçerik paneli yarıçapı | `--cm-radius-lg` | `var(--cm-radius-lg)` |
| Glass blur | `--cm-glass-blur` | `var(--cm-glass-blur)` |
| Glass kenarlık | `--cm-glass-border` | `var(--cm-glass-border)` |
| Panel gölgesi | `--cm-shadow-sm` | `var(--cm-shadow-sm)` |
| Odak halkası | `--cm-focus-ring` | `var(--cm-focus-ring)` |
| Başlık boşluğu | `--cm-space-4` | `var(--cm-space-4)` |
| Gövde metin boyutu | `--cm-text-base` | `var(--cm-text-base)` |
| Font ailesi | `--cm-font-body` | `var(--cm-font-body)` |
| Link hover | `--cm-text-link-hover` | `var(--cm-text-link-hover)` |

> **Token-First:** Figma vurgu dolgusu master `--cm-primary` ile birebir aynı değil → **token kazanır**; ham Figma değeri `reference/figma/extracted-admin-1920.md` §7.1'dedir (bu tabloya hex yazılmaz).

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T17) | **32×32 px** (öneri) | Hover affordans mevcut — Figma sidebar satırı 13px, ikon 11-16px → kodda ≥32px'e yükseltilir |
| WCAG 2.2 AA (SC 2.5.8) | 24×24 px | `04-accessibility-gaps` §2 ölçüm tabanı |
| Dokunmatik tier (T01-T08, T29, T31) | 44×44 px | Bu ekran T17 (Mouse+KB); portal/mobile türevi için geçerli |
| Yakınlık kuralı | ≥ 8 px boşluk | Figma sidebar satır arası 14px (y:42 → y:56) ✅ |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) — fotoğraf üzeri beyaz metin | **GAP** |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 — tasarımda odak durumu çizilmemiş | **GAP** |
| 3 | Dokunma hedefi | ≥ 24×24 px (SC 2.5.8) / T17 önerisi 32×32 — Figma satır 13px, ikon 11-16px | **GAP** |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası (sidebar → navbar → içerik → footer) | **PASS** |
| 5 | Durum yalnız renkle anlatılmıyor | İkon/metin + `aria-current="page"` — menü satırında ikon + metin daima görünür | **PASS** |

- **GAP 1:** kontrast kodda ölçülür (scrim + blur yoksa 4.5:1 garanti edilmez) → `.ai/ui-design/04-accessibility-gaps.md` §3.
- **GAP 2:** `:focus-visible { outline: 2px solid var(--cm-focus-ring) }` zorunlu → aynı dosya §7.
- **GAP 3:** sidebar tıklama alanı ≥32px (mouse) / ≥24px (AA) → aynı dosya §2.
- **ARIA:** `nav[aria-label="Admin menü"]` · aktif satır `aria-current="page"` · navbar çıkış butonu `aria-label="Çıkış yap"` · footer meta `aria-label="Oturum bilgisi"`.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(var(--cm-glass-blur))` |
| Arka plan | `var(--cm-bg-scrim)` (Figma: siyah/%20 katman) |
| Kenarlık | `1px solid var(--cm-glass-border)` |
| Gölge | `var(--cm-shadow-sm)` |
| Fallback | `background: var(--cm-bg-scrim)` (solid — `backdrop-filter` desteklenmeyen tarayıcı) |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/admin-1920/Admin - layout.png`
- **Klasör:** `admin-1920/` (3 PNG) · Alternatif açı: `.ai/.png/admin-1920/Admin - Login.png`
- **Mockup indeksi:** [[ui-design/01-mockup-index]] — `admin-1920/` bu revizyonda indekse eklendi
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | 1920 aralığında 3 sütun sabit kalır, 4K'da ortalamama; sidebar 250px sabit, içerik esner | [[ui-design/05-responsive-architecture]] §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | [[ui-design/00-device-matrix]] |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | 1920 spec'i yoksa T12/T14 (Laptop FHD) spec'i + fallback kuralı | [[ui-design/05-responsive-architecture]] §12 |
| Portre/Dikey | `N/A (landscape-only)` | `reference/10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `var(--cm-text-secondary)` + `var(--cm-glass-bg)` | — |
| Hover | `@media (hover:hover)` | `var(--cm-glass-bg-hover)` + `var(--cm-primary)` ikon | `aria-describedby` |
| Pressed | tıklama | `var(--cm-glass-bg-active)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-focus-ring)` | `:focus-visible` |
| Active | seçili menü satırı | `var(--cm-glass-bg-strong)` + kalın ağırlık | `aria-current="page"` |
| Disabled | yetki yok | `var(--cm-text-disabled)` + `cursor: not-allowed` | `aria-disabled="true"` |
| Loading | veri bekleniyor | `var(--cm-glass-bg)` skeleton şerit | `aria-busy="true"` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Mode:** Red Team · Human Mode · Truth Mode
