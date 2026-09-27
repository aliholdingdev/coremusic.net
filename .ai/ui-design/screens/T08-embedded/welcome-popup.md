---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Welcome Popup Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 2.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/welcome-popup.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png"
---

# CoreMusic — Welcome Popup (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                              x:1024    │
│ y:0  ┌─── HOME DASHBOARD (arka plan — overlay ile karartık + blur) ────────────────────┐    │
│      │ (header, widget alanı, en son dinlenenler, footer player görünür ama bulanık)  │    │
│ y:600└────────────────────────────────────────────────────────────────────────────────┘    │
│      ┌─── OVERLAY .welcome-modal-overlay (inset:0, bg rgba(0,0,0,.35), blur 3px) ─────┐    │
│      │                                                                                │    │
│      │    ┌─── MODAL .welcome-modal (x:212-812, y:146-454, w:600, h:308, r:8) ─────┐  │    │
│      │    │  [BG IMAGE welcome-popup-girl.png — cover, opacity:.8]                 │  │    │
│      │    │   ┌── LOGO IMG (195×130 @x:203 y:13) ──┐   ┌── GIRL IMG (w:171 h:242) ┐│  │    │
│      │    │   │  "CoreMusic" (morf logo)            │   │ rel x:429-600            ││  │    │
│      │    │   └────────────────────────────────────┘   │ rel y:66-308, r:[0,8,8,0] ││  │    │
│      │    │    "Hoş gelidn"  (Arima 14px @rel x:274 y:93)                          ││  │    │
│      │    │    "Prenses Işıl Peri" (Plus Jakarta 20px/600 @rel x:241 y:136)        ││  │    │
│      │    │    "Sana özel seçilen melodiler… 💜" (DM Sans 10px/300, w:365          ││  │    │
│      │    │     @rel x:118 y:194)                                                  ││  │    │
│      │    │    ┌──────────────────────────────┐                                     ││  │    │
│      │    │    │ Başla (105×25 @rel x:248 y:248, gradient #FF00C8, r:3)             ││  │    │
│      │    │    └──────────────────────────────┘                                     ││  │    │
│      │    └─────────────────────────────────────────────────────────────────────────┘  │    │
│      │                                                                                │    │
│      └────────────────────────────────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────────────────────────────────────┘
```

**Ölçü kontrolü:** modal y ekseni `146 + 308 + 146 = 600` ✓ · x ekseni `(1024 − 600) / 2 = 212` ✓ (ortalı). İç koordinatlar modal köşesine göredir (Figma `2876:6439` Frame 15).

> ⚠️ **Şablon notu (Truth Mode):** v1.0.0 dosyası bu bölümü "sol %50 görsel / sağ %50 içerik" split layout olarak çiziyordu — PNG ve Figma ile **çelişir** (modal tamamı tek görsel arka plan + ortalanmış içerik + sağda kız görseli). Bu sürümde PNG/Figma hizasına çekildi (Kalıp D §3.4).

---

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Overlay | `.welcome-modal-overlay` | `--is-hidden` | C07 |
| Modal | `.welcome-modal` | `__logo`, `__logo-img`, `__title`, `__user`, `__input`, `__desc`, `__btn` | C07 · C04 (`__btn`) |
| (ekran özel) | `.welcome-modal` | `::before` beyaz overlay katmanı (`rgba(255,255,255,.10)`) | — |
| (ekran özel) | — | Kapatma öğesi **YOK** (PNG'de × yok; kapanış `Başla` + `Escape`) | — |

**Kod kanıtı:** `assets.coremusic.net/Css/05_Pages/_home-components.css` L706-969 (`.welcome-modal-overlay` L710, `.welcome-modal` L729, öğeler L777-887, responsive L889-969).

> ⚠️ **Çelişki (Truth Mode):** v1.0.0 dosyası `.welcome-overlay` / `__image` / `__content` / `__subtitle` / `__close` sınıflarını tanımlıyordu — **hiçbiri diskteki CSS'te yok**; bu sürümde gerçek CSS adlarıyla değiştirildi. `welcome-banner.php` (home.coremusic.net) diskte olmadığından `home-welcome-banner` BEM'i (`assets.coremusic.net/Css copy/05_Pages/_home-layout.css` L709-841) **başka bileşendir** (ana sayfa banner'ı, popup değil) — karıştırılmamalı.

---

## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Overlay karartma | `cm-bg-overlay` | `var(--cm-bg-overlay)` — master `rgba(0, 0, 0, 0.60)`; PNG/Figma `.35` → ⚠️ çelişki, §6 |
| Modal yarıçapı | `cm-modal-radius` | `var(--cm-modal-radius)` (= `--cm-radius-2xl`) |
| Modal gölgesi | `cm-modal-shadow` | `var(--cm-modal-shadow)` |
| Glass overlay katmanı | `cm-glass-bg-strong` | `var(--cm-glass-bg-strong)` (`rgba(255,255,255,0.10)`) |
| Glass kenarlık | `cm-glass-border-strong` | `var(--cm-glass-border-strong)` |
| Z-index (overlay) | `cm-z-overlay` | `var(--cm-z-overlay)` (400 → uygulama L713: `1050`, ⚠️ sapma) |
| Z-index (modal) | `cm-z-modal` | `var(--cm-z-modal)` |
| CTA gradyanı | `pink-primary-button-*-color` | `var(--pink-primary-button-left-color)` → `var(--pink-primary-button-color)` → `var(--pink-primary-button-right-color)` (alfa 0.55/0.65/0.35; hex Figma `bg/gradient stop 0`) — ⚠️ master token dosyasında TANIMLI DEĞİL (yalnız uygulama CSS'inde) |

> ⚠️ v1.0.0 sürümü bu tabloda ham hex (`#ff4fd8`, `#0a0a0f` …) taşıyordu — Kalıp D §3.6 (ham hex yasak) ihlaliydi; token adlarına çevrildi.

---

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| T08 Embedded (touch) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı |
| `.welcome-modal__btn` (Başla) | 105×**25** px | ⚠️ **GAP** — yükseklik 25px < 44px (uygulama L857-858; mobil medya sorgusunda 36px'e çıkar, 1024'te base kalır) |
| `.welcome-modal__btn` — **mitigasyon** | hit-area **44×44 px**, görsel **105×25 px** | **Öneri:** `::after`/`::before` pseudo-element (veya `padding-block` + `background-clip: content-box`) ile tıklama alanı 44px'e çıkarılır; **görsel boyut Figma'da 105×25 olarak korunur** — sapma **yalnız erişilebilirlik katmanında**. **`Figma sapması, onay bekliyor`** — onaylanana kadar uygulanmaz, CSS'e dokunulmaz |
| `.welcome-modal__input` (isim girişi) | 100% × ~54 px | Satır yüksekliği yeterli |
| Yakınlık kuralı | ≥ 8 px boşluk | Btn–desc arası `margin-bottom: 18px` ✓ |
| Kapatma | Escape + overlay tıklaması | × butonu PNG'de yok (L774-775) |

> **⚠️ Mitigasyon notu (Truth Mode — `Figma sapması, onay bekliyor`):** 105×25px buton WCAG 2.2 AA 2.5.8'in **44px** alt sınırının altındadır. Önerilen çözüm **yalnız erişilebilirlik katmanını** değiştirir: vurulabilir alan (hit-area) pseudo-element/padding ile 44px'e genişletilir, **Figma'daki görsel 105×25px boyutuna dokunulmaz**. Kullanıcı onayı **bekliyor** — onay gelmeden bu satır uygulama planına alınmaz ve CSS'e dokunulmaz. Onay sonrası kayıt: `04-accessibility-gaps.md` §2.

---

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (beyaz metin, görsel arka plan + text-shadow) | GAP ⚠️ — görsel arka plan üstünde ölçülemiyor; `text-shadow var(--ts-md)` ile destekli, ölçüm gerekli |
| 2 | Odak (focus) görünür | 2px+ outline, kontrast ≥ 3:1 | PASS — `.welcome-modal__btn:focus-visible { outline: 3px solid #fff }` (L884-887), input alt çizgi `#ff4fd8` (L828-830) |
| 3 | Dokunma hedefi | ≥ 44×44 px | **GAP** ⚠️ — Başla 105×25px; `04-accessibility-gaps.md`'e işlenmeli |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası | PASS — logo→title→input→user→desc→btn (L762-769) |
| 5 | Durum yalnız renkle anlatılmıyor | İkon/metin + aria | PASS — `role="dialog" aria-modal="true"` beklenir (uygulamada JS tarafı ⚠️ VERIFICATION REQUIRED) |

---

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| Overlay `backdrop-filter` | **SSOT: Figma `BACKGROUND_BLUR blur=3`** (`2831:10268`, `extracted-1024.md` L726) — uygulama L715 `blur(1.5px)` → **deprecated**: CSS'te hâlâ 1.5, kod düzeltmesi **backend/ui işi** (bu dosyada CSS'e dokunulmadı) |
| Overlay arka plan | **SSOT: Figma `#000000 opacity=0.35` → `rgba(0,0,0,0.35)`** ✓ (uygulama L714 ile aynı) — master `--cm-bg-overlay` **`0.60` → deprecated**: CSS'te hâlâ 0.60, kod düzeltmesi **backend/ui işi** (master kaydı: `tokens/design-tokens-master.md` §2.1.2) |
| Modal arka plan | görsel + `::before` `rgba(255,255,255,.10)` (Figma `bg-white opacity 0.10`) |
| Kenarlık | `1px solid rgba(255,255,255,0.21)` (Figma stroke `#FFFFFF` weight 1, opacity .21) |
| Gölge | `var(--modal-shadow)`; Figma `0 4px 4px rgba(0,0,0,.15)` |
| Fallback | `backdrop-filter` desteklenmezse overlay yalnız `background: rgba(0,0,0,.35)` (solid) ile devam — blackout azalır, kontrast yeniden ölçülür |

---

## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png`
- **Klasör:** `home-1024/` (12 PNG) · Alternatif açı: **YOK** (1920 klasöründe welcome PNG'si yok → bkz. `T17-monitor-22fhd/welcome-popup.md`)
- **Mockup indeksi:** [[01-mockup-index]]
- **Figma kanıtı:** `reference/figma/extracted-1024.md` §8 (`2831:10267` — overlay + 600×308 modal + CTA `#FF00C8`)
- **Kullanım sırası:** PNG > Figma > ASCII art > Inventory > Tokens > Reference

---

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | Modal `width:100%; max-width:600px` — 1024'te 600px tam kullanılır | `05-responsive-architecture` §7.4 + §12 |
| ≤767px | `max-width:90vw`, radius 12, logo 36px, btn 100×36 | uygulama L890-917 |
| 1025-1440px | `max-width:640px; min-height:340px` | uygulama L925-943 |
| 1441-1919px | `max-width:680px; min-height:360px` | uygulama L946-951 |
| **1920px (T17)** | ⚠️ **Boşluk:** `max-width:1919px` medya sorgusu 1920'yi dışlar → base 600×308 geçerli (kasıtlı mı, sorgu hatası mı? → raporlanır) | uygulama L946 + L954 |
| ≥3840px (T25) | `max-width:900px; min-height:500px`, font scale ~2x | uygulama L954-969; §7.4 (4K'da ortalamama) + §12 (fallback) |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25` | `00-device-matrix.md` |
| Portre/Dikey | T08 landscape-only; modal dikey ortalanır (`align-items:center`) | `reference/10-device-specific-guidelines` |

---

## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | overlay açık, modal görünür | `role="dialog" aria-modal="true"` |
| Pressed | dokunma (`:active`) | btn `transform: translateY(0)` (L880-882) | — |
| Focus | klavye | btn `outline: 3px solid #fff` · input alt çizgi `#ff4fd8` | `:focus-visible` |
| Loading | isim gönderimi | ⚠️ VERIFICATION REQUIRED — uygulamada loading state'i tanımlı değil | `aria-busy="true"` |
| Kapalı | `Başla` / `Escape` / overlay tıklaması | `.is-hidden { display:none }` + opacity 0.22s (L725-727, L722) | `aria-hidden="true"` |
| Hatalı giriş | boş isim | ⚠️ VERIFICATION REQUIRED — uygulamada hata durumu yok (input serbest) | — |

> 📌 **v1.0.0'dan taşınan §9 Animasyon bölümü** Kalıp D'nin 9 bölüm listesinde yer almadığından kaldırıldı; geçiş süreleri (0.22s overlay, 150ms btn) §9 bu tabloya taşındı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-27
**Mode:** Red Team · Human Mode · Truth Mode
