---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Welcome Popup Screen Specification"
type: spec
category: ui-design
date: 2026-09-27
status: draft
version: 1.1.0
tier: T17
viewport: 1920x1080
device: 22" FHD Monitor (Desktop)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/welcome-popup.md"
  source_of_truth: "⚠️ VERIFICATION REQUIRED — PNG bekleniyor (türetildi: .ai/ui-design/screens/T08-embedded/welcome-popup.md + .ai/ui-design/reference/figma/extracted-1920.md §4)"
---

# CoreMusic — Welcome Popup (T17 Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

> ⚠️ **TÜRETİLMİŞ SPEC (Truth Mode):** `.ai/.png/home-1920/` altında **welcome popup PNG'si yok** (tek dosya: `Linux - 1920 - Home.png`). Bu spec; T08 1024 spec'i + Figma `extracted-1920.md` §4 (aynı node `2831:10267`) + uygulama CSS medya sorgularından türetilmiştir. **Görsel doğrulama olmadan TAMAMLANAMAZ** — 1920 welcome PNG'si geldiğinde §1 ve §7 yeniden okunmalıdır.

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                                                                      x:1920  │
│ y:0    ┌─── HOME DASHBOARD 1920 (arka plan — overlay karartık + blur) ──────────────────────────────────────────────────────────┐    │
│        │ (header 8 öğeli nav, widget alanı 4x4+1x8+1x8 grid, en son dinlenenler, footer player)                                │    │
│ y:1080 └────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘    │
│        ┌─── OVERLAY .welcome-modal-overlay (inset:0, bg rgba(0,0,0,.35), blur 3px) ───────────────────────────────────────────┐    │
│        │                                                                                                                        │    │
│        │        ┌─── MODAL .welcome-modal (x:660-1260, y:386-694, w:600, h:308, r:8, ORTALI) ────────────────────────────┐    │    │
│        │        │  [BG IMAGE welcome-popup-girl.png — cover, opacity:.8]                                                 │    │    │
│        │        │   ┌── LOGO IMG (195×130 @rel x:203 y:13) ──┐      ┌── GIRL IMG (w:171 h:242, rel x:429-600 y:66-308) ──┐│    │    │
│        │        │   │  "CoreMusic" (morf logo)                │      │ r:[0,8,8,0]                                       ││    │    │
│        │        │   └────────────────────────────────────────┘      └───────────────────────────────────────────────────┘│    │    │
│        │        │    "Hoş gelidn" (Arima 14px @rel x:274 y:93)                                                             │    │    │
│        │        │    "Prenses Işıl Peri" (Plus Jakarta 20px/600 @rel x:241 y:136)                                          │    │    │
│        │        │    "Sana özel seçilen melodiler… 💜" (DM Sans 10px/300, w:365 @rel x:118 y:194)                           │    │    │
│        │        │    ┌────────────────────────────────┐                                                                     │    │    │
│        │        │    │ Başla (105×25 @rel x:248 y:248, gradient #FF00C8, r:3)                                              │    │    │
│        │        │    └────────────────────────────────┘                                                                     │    │    │
│        │        └────────────────────────────────────────────────────────────────────────────────────────────────────────────┘    │    │
│        │                                                                                                                        │    │
│        └────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Ölçü kontrolü:** modal y ekseni `386 + 308 + 386 = 1080` ✓ · x ekseni `(1920 − 600) / 2 = 660` ✓ (ortalı).

> ⚠️ **Türetme notu (Truth Mode):**
> 1. Modal 600×308 olarak **sabit bırakıldı** — Figma `extracted-1920.md` §4'teki welcome node'u **1024 genişliğindedir** (node `2831:10267`, w=1024 h=601); Figma'da 1920'ye özel welcome varyantı **çekilmemiştir**.
> 2. Uygulama CSS'i 1441-1919px aralığını `max-width:680px` ile büyütür ama **1920px bu sorguya girmez** (`max-width:1919px`) ve 3840 sorgusu da eşleşmez → 1920'de **base 600×308** geçerlidir (`_home-components.css` L946/L954). Bu kasıtlı mı, sorgu hatası mı? → **çözülmemiş çelişki, raporlandı.**
> 3. 1920 özel görsel ölçek (modal 680px + font ~1.15x önerisi) **uygulanmadı** — uygulama CSS'iyle çelişmemek için. PNG gelirse §7.4 kuralına göre revize edilir.

---

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Overlay | `.welcome-modal-overlay` | `--is-hidden` | C07 |
| Modal | `.welcome-modal` | `__logo`, `__logo-img`, `__title`, `__user`, `__input`, `__desc`, `__btn` | C07 · C04 (`__btn`) |
| (ekran özel) | `.welcome-modal` | `::before` beyaz overlay katmanı (`rgba(255,255,255,.10)`) | — |
| (ekran özel) | — | Kapatma öğesi **YOK** (PNG'de × yok; kapanış `Başla` + `Escape`) | — |

**Kod kanıtı:** `assets.coremusic.net/Css/05_Pages/_home-components.css` L706-969 — T17 için **farklı sınıf yok**; 1920, base değerleri kullanır (§1 not 2).

---

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Overlay karartma | `cm-bg-overlay` | `var(--cm-bg-overlay)` — uygulama `.35` = **SSOT Figma** ✓; master değeri `0.60` → **deprecated** (CSS'te hâlâ .60, kod düzeltmesi backend/ui işi) → §6 · master `design-tokens-master.md` §2.1.2 |
| Modal yarıçapı | `cm-modal-radius` | `var(--cm-modal-radius)` |
| Modal gölgesi | `cm-modal-shadow` | `var(--cm-modal-shadow)` |
| Glass overlay katmanı | `cm-glass-bg-strong` | `var(--cm-glass-bg-strong)` |
| Glass kenarlık | `cm-glass-border-strong` | `var(--cm-glass-border-strong)` |
| Z-index (overlay) | `cm-z-overlay` | `var(--cm-z-overlay)` |
| CTA gradyanı | `pink-primary-button-*-color` | `var(--pink-primary-button-left-color)` → `var(--pink-primary-button-color)` → `var(--pink-primary-button-right-color)` — ⚠️ master'da TANIMLI DEĞİL |
| 1920 font scale | `cm-font-scale` | ⚠️ T17'de welcome modal'a uygulanmadı (§1 not 3) |

---

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| T17 Mouse tier | 32×32 px (öneri) | Hover affordans mevcut — `00-device-matrix.md` mouse sınıfı |
| `.welcome-modal__btn` (Başla) | 105×25 px | T17'de 32px önerisinin altında ⚠️ (hover tier olduğu için GAP değil; WCAG 2.5.8 en az 24×24 → **24px PASS**, 25px ✓) |
| `.welcome-modal__btn` — **mitigasyon** | hit-area **44×44 px**, görsel **105×25 px** | **Öneri (T08 ile aynı CSS):** `::after`/`::before` pseudo-element (veya `padding-block` + `background-clip: content-box`) ile vurulabilir alan 44px'e çıkarılır; **görsel boyut Figma'da 105×25 px olarak korunur** — sapma **yalnız erişilebilirlik katmanında**. **`Figma sapması, onay bekliyor`** — onaylanana kadar uygulanmaz, CSS'e dokunulmaz |
| Yakınlık kuralı | ≥ 8 px boşluk | Btn–desc `margin-bottom: 18px` ✓ |

> **⚠️ Mitigasyon notu (Truth Mode — `Figma sapması, onay bekliyor`):** Başla butonu görsel olarak **105×25 px**'tir (Figma `2831:10267` CTA, `extracted-1024.md` L736 — `bg` RECTANGLE `w=105 h=25`). T17 mouse tier'da WCAG 2.5.8 için 24×24 yeterli (PASS), ancak **dokunmatik hedef eşiği olan 44×44** için T08 ile **aynı bileşen** paylaşıldığından mitigasyon önerisi burada da geçerlidir: hit-area pseudo-element/padding ile 44px, **Figma görsel boyutu 105×25 değişmez** — sapma yalnız erişilebilirlik katmanındadır. **Onay bekliyor**; onay gelmeden CSS'e dokunulmaz.

---

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (beyaz metin, görsel arka plan + text-shadow) | GAP ⚠️ — görsel arka plan ölçümü gerekli |
| 2 | Odak (focus) görünür | 2px+ outline, kontrast ≥ 3:1 | PASS — btn `outline: 3px solid #fff`; input `border-bottom-color` vurgusu |
| 3 | Dokunma hedefi | ≥ 24×24 px (mouse tier) | PASS — Başla 105×25 |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası | PASS — logo→title→input→user→desc→btn |
| 5 | Durum yalnız renkle anlatılmıyor | İkon/metin + aria | PASS — `role="dialog" aria-modal="true"` (JS tarafı ⚠️ VERIFICATION REQUIRED) |

---

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| Overlay `backdrop-filter` | **SSOT: Figma `BACKGROUND_BLUR blur=3`** (`2831:10268`, `extracted-1920.md` L544) — uygulama L715 `blur(1.5px)` → **deprecated**: CSS'te hâlâ 1.5, kod düzeltmesi **backend/ui işi** (bu dosyada CSS'e dokunulmadı) |
| Overlay arka plan | **SSOT: Figma `#000000 opacity=0.35` → `rgba(0,0,0,0.35)`** ✓ (uygulama L714 ile aynı) — master `--cm-bg-overlay` **`0.60` → deprecated**: CSS'te hâlâ 0.60, kod düzeltmesi **backend/ui işi** (master kaydı: `tokens/design-tokens-master.md` §2.1.2) |
| Modal arka plan | görsel + `::before` `rgba(255,255,255,.10)` |
| Kenarlık | `1px solid rgba(255,255,255,0.21)` |
| Gölge | `var(--modal-shadow)` |
| Fallback | `backdrop-filter` yoksa solid `rgba(0,0,0,.35)` — kontrast yeniden ölçülür (`05-responsive-architecture` §12) |

---

## 7. PNG Referansı

- **Dosya (Figma export):** `.ai/ui-design/reference/figma/png/1920 - Welcome Div.png` — node `2831:10267` "Welcome Div" (`GET /v1/images … format=png&scale=2`), **2048×1202 px (@2x)**, MD5 `82D18A759F6B4461242934CFECF19895` — `1024 - Welcome Div.png` ile **bayt-bayt aynı** (node her iki breakpoint'te ortak: `reference/figma/_extraction-notes.md` §2). ✅ **Figma export ile doğrulandı.**
- **Klasör:** `reference/figma/png/` (12 Figma export PNG) · **Katman:** Figma (SSOT sırası: PNG > **Figma** > ASCII …)
- **Gerçek uygulama PNG:** ⚠️ **VERIFICATION REQUIRED — `.ai/.png/home-1920/` altında welcome popup PNG'si yok**; spec görsel doğrulama olmadan TAMAMLANAMAZ. **Not: Figma export ile doğrulandı, gerçek uygulama PNG'si bekleniyor.**
- **Mevcut 1920 PNG:** `.ai/.png/home-1920/Linux - 1920 - Home.png` (ana ekran; popup **içinde değil**)
- **Türetme kaynağı:** `.ai/ui-design/screens/T08-embedded/welcome-popup.md` + `reference/figma/extracted-1920.md` §4 (`2831:10267`) + yukarıdaki Figma export PNG
- **Mockup indeksi:** [[01-mockup-index]]
- **Kullanım sırası:** PNG > Figma > ASCII art > Inventory > Tokens > Reference

---

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | Modal `width:100%; max-width:600px` — 1920'de base değer geçerli | `05-responsive-architecture` §7.4 + §12 |
| 1441-1919px | `max-width:680px; min-height:360px` — **1920 bu sorguya girmez** | uygulama L946-951 |
| 1920px (bu tier) | Base 600×308 (⚠️ §1 not 2 — çözülmemiş çelişki) | uygulama L929 + L946 |
| ≥3840px (T25) | `max-width:900px; min-height:500px` | uygulama L954-969; §7.4 + §12 |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25` | `00-device-matrix.md` |
| Fallback | T17 welcome PNG'si yok → T08 spec'i fallback olarak kullanılır (§12 kuralı) | `05-responsive-architecture` §12 |
| Portre/Dikey | N/A (landscape-only) | `reference/10-device-specific-guidelines` |

---

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | overlay açık, modal görünür | `role="dialog" aria-modal="true"` |
| Hover | `@media (hover:hover)` — T17 mouse tier ✓ | btn `#ff5fe0`, `translateY(-1px)` (L874-878) | — |
| Pressed | tıklama (`:active`) | btn `translateY(0)` (L880-882) | — |
| Focus | klavye (Tab) | btn `outline: 3px solid #fff`; input alt çizgi vurgu | `:focus-visible` |
| Loading | isim gönderimi | ⚠️ VERIFICATION REQUIRED — uygulamada loading state'i yok | `aria-busy="true"` |
| Kapalı | `Başla` / `Escape` / overlay tıklaması | `.is-hidden` + opacity 0.22s | `aria-hidden="true"` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-27
**Mode:** Red Team · Human Mode · Truth Mode
