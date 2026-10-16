---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Admin Login Screen Specification"
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
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/admin-login.md"
  source_of_truth: ".ai/.png/admin-1920/Admin - Login.png"
---

# CoreMusic — Admin Login (T17 Desktop Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                     x:1920 │
│ y:0     ┌─ FOTOĞRAF ARKA PLAN (x:0-1420 · w:1420 · h:1080 · %74) ─┐┌─ SAĞ PANEL (x:1420-1920) ────────────┐ │
│         │ IMAGE scaleMode=FILL (1920×1080)                         ││ w:500 (%26) h:1080 r:10 zemin siyah/%25│ │
│         │ bu bantta arayüz öğesi yok                               ││ + drop-shadow (r:1 dy:1)               │ │
│         │                                                           ││ "Core Music for Ali Admin" @1514 y:107 │ │
│         │                                                           ││ (313×32 · ölçekli text ⚠️ §5/GAP notu) │ │
│         │                                                           ││ "Sisteme Giriş Yapın" @1587 y:169      │ │
│         │                                                           ││ (166×25 · 00543 23px)                   │ │
│ y:261   └───────────────────────────────────────────────────────────┘├─ BİLGİ NOTU (y:261 · h:136 · %13) ────┤ │
│                                                                     ││ info ikon 50×50 @x:1645 y:261           │ │
│                                                                     ││ metin @x:1449 y:331 w:445 h:66 (16px)   │ │
│ y:397                                                               ├─ BOŞLUK (h:110 · %10) ─────────────────┤ │
│ y:507                                                               ├─ FORM (x:1475 · y:507 · w:390 · h:254) ┤ │
│                                                                     ││ "Kullanıcı adı yada Email" y:507 (20px)│ │
│                                                                     ││ INPUT y:545 w:390 h:30 (r:3 · ikon 15)  │ │
│                                                                     ││ "Parola" y:595 (20px)                   │ │
│                                                                     ││ INPUT y:633 w:390 h:30 (r:20 · ikon 15) │ │
│                                                                     ││ checkbox "Beni Hatırla" y:683 (15×15)   │ │
│                                                                     ││ "Şifremi Unuttum" @x:1757 y:685 (link)  │ │
│                                                                     ││ BUTTON y:723 w:390 h:38 (r:3)           │ │
│ y:761                                                               ├─ BOŞLUK (h:180 · %17) ─────────────────┤ │
│ y:941                                                               ├─ QR BLOĞU (x:1479 · y:941 · h:100) ────┤ │
│                                                                     ││ kart 386×80 @y:946 r:10 beyaz/%10        │ │
│                                                                     ││ phone 28×60 · qr 21×21 · metin w:311     │ │
│ y:1041                                                              ├─ ALT BOŞLUK (h:39 · %4) ───────────────┤ │
│ y:1080                                                              └─────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Yükseklik toplamı:** 261 (başlık bandı) + 136 (bilgi notu) + 110 (boşluk) + 254 (form) + 180 (boşluk) + 100 (qr) + 39 (alt) = **1080** ✓

- **Kaynak:** Figma `2161:12506` "Admin - Login" · çıkarım [[ui-design/reference/figma/extracted-admin-1920]] §5 · ham `reference/figma/raw/page-2161-12438.json`.
- **Öncelik:** PNG > ASCII > Inventory > Tokens > Reference — panel blur'u **yalnız PNG'de görünür** (Figma API `effects` alanında `BACKGROUND_BLUR` yok) → PNG kazanır; blur değeri token'dan okunur.
- **Tier notu:** viewport 1920×1080 + CatID `DM-T17` ile **T17** atandı; matrix `T42 NAS Web Interface (Admin panel)` satırıyla semantik çelişki → **açık madde** (`screens/00-ascii-art-index.md` §7 madde 5).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Sağ auth paneli | `.auth-panel` | `__title`, `__subtitle`, `__section` | — |
| Bilgi notu | `.auth-note` | `__icon`, `__text` | — |
| Form kabuğu | `.auth-form` | `__row`, `__actions` | — |
| Form etiketi | `.input__label` | — | C05 |
| Giriş alanları | `.input` | `--error`, `--success`, `__hint` | C05 |
| Giriş butonu | `.btn` | `--primary`, `--lg` | C04 |
| Şifremi unuttum | `.auth-link` | `--primary` | — |
| Beni Hatırla | `.auth-checkbox` | `__box`, `__label` | — |
| QR bloğu | `.auth-qr` | `__card`, `__device`, `__text` | — |

> ⚠️ **VERIFICATION REQUIRED:** `—` ile işaretlenen block'lar envanterde (C01-C19) karşılığı olmayan **ekran özel** yapılardır; `Cxx` eşlemesi bileşen spec'inde yapılır (uydurma `Cxx` yazılmaz).

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| CTA buton zemini | `--cm-primary` | `var(--cm-primary)` |
| Birincil metin | `--cm-text-primary` | `var(--cm-text-primary)` |
| Panel koyu katman | `--cm-bg-scrim` | `var(--cm-bg-scrim)` |
| Input yarıçapı | `--cm-input-radius` | `var(--cm-input-radius)` |
| Buton yarıçapı | `--cm-button-radius` | `var(--cm-button-radius)` |
| Input yatay dolgu | `--cm-input-padding-x` | `var(--cm-input-padding-x)` |
| Glass blur (PNG'deki bulanıklık) | `--cm-glass-blur` | `var(--cm-glass-blur)` |
| Glass zemin | `--cm-glass-bg` | `var(--cm-glass-bg)` |
| Glass kenarlık | `--cm-glass-border` | `var(--cm-glass-border)` |
| Kart/panel gölgesi | `--cm-shadow-xs` | `var(--cm-shadow-xs)` |
| Odak halkası | `--cm-focus-ring` | `var(--cm-focus-ring)` |
| Başlık boyutu | `--cm-text-2xl` | `var(--cm-text-2xl)` |
| Gövde metin | `--cm-text-base` | `var(--cm-text-base)` |
| Link rengi | `--cm-text-link` | `var(--cm-text-link)` |

> **Token-First:** Figma buton/link dolguları master token'lara birebir eşit değil → **token kazanır**; ham Figma değerleri `reference/figma/extracted-admin-1920.md` §7.1'dedir (tabloya hex yazılmaz).

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Mouse tier (T17) | **32×32 px** (öneri) | Input h:30 ve checkbox 15×15 → kodda tıklama alanı ≥32px'e çıkarılır (etiket/dolgu alanı ile) |
| WCAG 2.2 AA (SC 2.5.8) | 24×24 px | Checkbox kutusu 15×15 → **altında**; toplam tıklama alanı 24px'i bulmalı (`04-accessibility-gaps` §2) |
| Dokunmatik tier (T01-T08, T29, T31) | 44×44 px | Bu ekran T17 (Mouse+KB) |
| Yakınlık kuralı | ≥ 8 px boşluk | "Beni Hatırla" (y:683) ile "Şifremi Unuttum" (y:685) aynı satırda; button (y:723) ile arası 20px ✅ |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) — foto üzeri beyaz metin, scrim siyah/%25 | **GAP** |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 — tasarımda odak durumu çizilmemiş | **GAP** |
| 3 | Dokunma hedefi | ≥ 24×24 px (SC 2.5.8) / T17 önerisi 32×32 — Figma checkbox 15×15, input h:30 | **GAP** |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası (başlık → bilgi notu → form → buton → qr) | **PASS** |
| 5 | Durum yalnız renkle anlatılmıyor | İkon/metin + `aria-describedby` — doluluk metinle belli; hata durumu tasarımda yok (kodda metin mesaj zorunlu) | **PASS** |

- **GAP 1:** kontrast kodda ölçülür; blur + scrim ile 4.5:1 güvence altına alınır → `.ai/ui-design/04-accessibility-gaps.md` §3.
- **GAP 2:** `:focus-visible { outline: 2px solid var(--cm-focus-ring) }` → aynı dosya §7.
- **GAP 3:** checkbox 15×15 → AA 24px alt sınırı ihlali → aynı dosya §2.
- **ARIA:** form `aria-label="Yönetici girişi"` · label ↔ input `for/id` · hata `aria-describedby` + `aria-invalid` · buton loading'de `aria-busy="true"` · qr görsel `alt`.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(var(--cm-glass-blur))` |
| Arka plan | `var(--cm-glass-bg)` + `var(--cm-bg-scrim)` katmanı (Figma: siyah/%25 panel) |
| Kenarlık | `1px solid var(--cm-glass-border-strong)` |
| Gölge | `var(--cm-shadow-xs)` |
| Fallback | `background: var(--cm-bg-scrim)` (solid — `backdrop-filter` desteklemeyen tarayıcı) |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır |

## 7. PNG Referansı

- **Dosya:** `.ai/.png/admin-1920/Admin - Login.png`
- **Klasör:** `admin-1920/` (3 PNG) · Alternatif açı: `.ai/.png/admin-1920/Admin - Register.png`
- **Mockup indeksi:** [[ui-design/01-mockup-index]] — `admin-1920/` bu revizyonda indekse eklendi
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | 1920 aralığında sol fotoğraf %74 + sağ panel sabit 500px; 4K'da ortalamama | [[ui-design/05-responsive-architecture]] §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | [[ui-design/00-device-matrix]] |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | 1920 spec'i yoksa T12/T14 (Laptop FHD) spec'i + fallback kuralı | [[ui-design/05-responsive-architecture]] §12 |
| Portre/Dikey | `N/A (landscape-only)` | `reference/10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | input `var(--cm-glass-bg)` + `var(--cm-text-primary)` | — |
| Hover | `@media (hover:hover)` | buton `var(--cm-primary-light)` | `aria-describedby` |
| Pressed | tıklama | buton `var(--cm-primary-dark)` | — |
| Focus | klavye | `outline: 2px solid var(--cm-focus-ring)` | `:focus-visible` |
| Active | form gönderiliyor | buton `var(--cm-primary-90)` | `aria-current="page"` yok — `aria-busy` |
| Disabled | doğrulama başarısız | `var(--cm-text-disabled)` + `cursor: not-allowed` | `aria-disabled="true"` |
| Loading | istek bekleniyor | `var(--cm-glass-bg)` skeleton / spinner | `aria-busy="true"` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Mode:** Red Team · Human Mode · Truth Mode
