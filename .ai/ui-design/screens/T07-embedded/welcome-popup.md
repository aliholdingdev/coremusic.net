---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Welcome Popup Screen Specification"
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
  authority: ".ai/ui-design/screens/T07-embedded/welcome-popup.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png"
---

# CoreMusic — Welcome Popup (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                               x:1024  │
│ y:0   ┌── OVERLAY (0,0) w:1024 h:600 — .welcome-modal__overlay ─────────────────────────────────────────┐  │
│       │  Alt zemin: home-dashboard (header/content/footer) soluk — SSOT overlay alpha 0.35 (§6)         │  │
│       │  ┌── MODAL (212,146) w:600 h:308 — .welcome-modal (tam orta: (1024-600)/2=212, (600-308)/2=146) │  │
│ y:159 │  │  logo (415,159) w:195 h:130 · .welcome-modal__logo / __logo-img                              │  │
│ y:239 │  │  "Hoş gelidn" (486,239) Arima 14 · .welcome-modal__title (PNG yazımı aynen)                   │  │
│ y:282 │  │  "Prenses Işıl Peri" (453,282) Plus Jakarta Sans 20/600                                       │  │
│ y:340 │  │  açıklama (330,340) w:365 h:26 · DM Sans 10/300 · .welcome-modal__desc                        │  │
│ y:394 │  │  CTA (460,394) w:105 h:25 "Başla" · .welcome-modal__btn → --cm-pink-primary-button            │  │
│ y:454 │  └────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────────┘
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Yükseklik hesabı:** overlay h:600 = viewport **600 ✓** (header/content/footer altta soluk). Not: Figma overlay düğümü 1024×**601** → 600'a kırpılır (clip; §5 sayımına girmez).

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Overlay | `.welcome-modal__overlay` | `is-hidden` (welcome-modal.js) | — (ekran özel) |
| Modal | `.welcome-modal` | open/close geçişi (JS) | — (ekran özel) |
| Logo | `.welcome-modal__logo` | `__logo-img` | — (ekran özel) |
| Başlık | `.welcome-modal__title` | iki satır: "Hoş gelidn" / "Prenses Işıl Peri" | — (ekran özel) |
| Açıklama | `.welcome-modal__desc` | — | — (ekran özel) |
| CTA | `.welcome-modal__btn` | `:hover`, `:active`, `:focus-visible` | — (ekran özel) |

> ⚠️ VERIFICATION REQUIRED — Envanterdışı sınıflar (C01-C19 ile birebir değil): `.welcome-modal__overlay`, `.welcome-modal`, `.welcome-modal__logo`, `.welcome-modal__title`, `.welcome-modal__desc`, `.welcome-modal__btn`. C07 Modal envanter adı `.modal` / `.modal__overlay` iken bu ekranda `.welcome-modal*` ailesi CSS'te kanıtlıdır — envanterle birebir örtüşmez, uydurulmadı. "Content Div" (Figma) için CSS sınıfı doğrulanamadı → satır yok.

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Modal z-index | `--cm-z-welcome` | `var(--cm-z-welcome)` |
| Modal yarıçapı | `--cm-modal-radius` | `var(--cm-modal-radius)` |
| Modal gölgesi | `--cm-modal-shadow` | `var(--cm-modal-shadow)` |
| CTA gradyanı | `--cm-pink-primary-button` | `var(--cm-pink-primary-button)` |
| Overlay (deprecated token — SSOT §6'ya bak) | `--cm-bg-overlay` | `var(--cm-bg-overlay)` |
| Overlay blur (referans) | `--cm-glass-blur` | `var(--cm-glass-blur)` |
| Başlık/metin | `--cm-text-primary` | `var(--cm-text-primary)` |
| Açıklama metni | `--cm-text-secondary` | `var(--cm-text-secondary)` |
| Geçiş süresi | `--cm-duration-normal` | `var(--cm-duration-normal)` |
| Odak halkası | `--cm-focus-ring` | `var(--cm-focus-ring)` |

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| T07 Embedded (matrix `Touch` = 48px) | **48×48 px** | Bağlayıcı; mevcut CTA 105×**25** → §5-4 GAP |
| WCAG 2.2 AA 2.5.8 (yasal alt sınır) | 24×24 px | CTA 25px tam sınırda (PASS, marj 1px) |
| Şablon geneli (T01-T08, T29, T31) | 44×44 px | `--cm-touch-target: 44px` (master) |
| Yakınlık kuralı | ≥ 8 px boşluk | Modal içi CTA–açıklama arası 28px |
| Komut satırı | `touch target >= 44px (tier embedded/phone)` | Prompt `constraints` ile aynı ifade |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) | GAP — PNG medyan ölçüm: beyaz metin ≈ **2.65:1** (overlay solgun zemin üstü) |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 | PASS — `.welcome-modal__btn:focus-visible` (CSS L884) + focus trap (welcome-modal.js L8) |
| 3 | Dokunma hedefi (WCAG 2.5.8) | ≥ 24×24 px | PASS — CTA 105×25 (25 ≥ 24) |
| 4 | Tier touch (T07, matrix `Touch`=48px) | ≥ 48×48 px | GAP — CTA 105×25 < 48px yükseklik |
| 5 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası | PASS — overlay → modal (logo → başlık → açıklama → CTA) |
| 6 | Durum yalnız renkle anlatılmıyor | İkon/metin + aria | PASS — CTA metni "Başla" (renk-dışı) + Escape kapanış + `aria-modal dialog` (welcome-modal.js L7-L8) |

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: **BEKLEMEDE** (bu görevde dosya yazım yasağı).

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 1**

**ÇELİŞKİ-1 — Tier/viewport (00-device-matrix ↔ bu dosya):** matrix L95 "Welcome popup (**T07**)" derken L92-L93 `1024×600 = T07` / `T08 = 1280×800`, L295 `1024×600 → T07 Embedded`; bu spec `screens/T07-embedded/` dizininde ve `viewport: 1024x600`. L97 T07/T08 dual-ID çelişkisi de geçerli. Karar: `tier: T07` + `1024x600` **taşındı** (owner onayı, matrix L92); matrix düzeltmesi **GEREKMEDİ**.

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| Overlay alpha (SSOT) | **0.35** (`rgba(0,0,0,0.35)`) — Figma `2831:10268`, master §2.1.2 |
| Overlay `backdrop-filter` (SSOT) | **3px** — Figma `BACKGROUND_BLUR blur=3` |
| Deprecated (CSS'te hâlâ uygulanmış) | `--cm-bg-overlay` = 0.60 (master §2.1) · blur 1.5 (`_home-components.css` L715) → **deprecated**, kod düzeltmesi backend/ui işi |
| Modal katmanı | `var(--cm-z-welcome)` · arka plan `var(--cm-glass-bg)` |
| Fallback (blur desteklenmiyorsa) | solid `rgba(0,0,0,0.35)` (SSOT alpha ile aynı) |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır (§5-1 GAP: 2.65) |

**§6 ÇELİŞKİ sayısı: 1** — SSOT **0.35 / 3px** (Figma) ↔ deprecated **0.60 / 1.5** (master `--cm-bg-overlay` + CSS). SSOT bağlayıcı; CSS düzeltmesi bu spec'in kapsamı dışında (kod katmanı işi). Karşılığı: `tokens/design-tokens-master.md` §2.1.2.

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png`
- **Klasör:** `home-1024/` (12 PNG) · Arka plan PNG: `.ai/.png/home-1024/Linux  1024 - Home Page.png`
- **Mockup indeksi:** [[ui-design/01-mockup-index]]
- **Kullanım sırası:** PNG > Figma extracted > ASCII — çelişki §5/§6'ya "ÇELİŞKİ" olarak yazılır (AGENTS.md §7.2)
- **`screens/00-ascii-art-index.md` satırı:** BEKLEMEDE (bu görevde indeks dosyası yazım yasağı)

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | T07 1024×600 — modal sabit 600×308, tam orta; yeniden akış yok | [[ui-design/05-responsive-architecture]] §7.4 + §12 |
| Tier sıçraması | `T01 → T03 → T07 → T17 → T25 → T29 → T31` | [[ui-design/00-device-matrix]] |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + §12 fallback kuralı | [[ui-design/05-responsive-architecture]] §12 |
| Portre/Dikey | `N/A (landscape-only)` | `reference/10-device-specific-guidelines` |

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default (kapalı) | ilk render | `overlay.is-hidden` + `opacity: var(--cm-opacity-full)` | `aria-hidden="true"` |
| Open | sayfa açılışı | alpha 0.35 + blur 3px (§6 SSOT) | `aria-modal="true"` (js L8) |
| Closing | "Başla" / Escape | `classList.add('is-hidden')` (js L60, L69) | — |
| Hover (CTA) | `@media (hover:hover)` | `translateY(-1px)` + gölge artışı (CSS L874-878) | `aria-describedby` |
| Focus (CTA) | klavye | `var(--cm-focus-ring)` | `:focus-visible` (CSS L884) |
| Pressed (CTA) | dokunma | `translateY(0)` (CSS L880-882) | — |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Version | 1.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| PNG doğrulama | ✅ okundu → `status: active` |
| ASCII yükseklik | overlay 600 = 600 ✓ |
| §5 ÇELİŞKİ | 1 |
| §6 ÇELİŞKİ | 1 |
| `00-ascii-art-index.md` | BEKLEMEDE (bu görevde yazım yasağı) |
| Cross References | 5 |
| Tier düzeltmesi | tier T08→T07 düzeltildi (matrix L92) |
| Last Updated | 2026-09-27 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
