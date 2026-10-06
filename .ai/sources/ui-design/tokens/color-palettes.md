---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Color Palettes"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 3.2.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/color-palettes.md"
  source_of_truth: ".ai/ui-design/tokens/design-tokens-master.md"
---

# CoreMusic — Color Palettes

**Zorunlu Bağlantılar:** [[design-tokens-master]] · [[platform-tokens]] · [[component-tokens]]

---

## 1. Amaç

CoreMusic renk paletlerinin **tek kaynağıdır**. 3 tema, semantik renkler, gri ölçek ve cam efekti renkleri burada tanımlanır.

---

## 2. Tema Renkleri

### 2.1 Female Theme (Varsayılan — #ff4fd8 Pink)

```css
[data-theme="female"] {
  --cm-primary: #ff4fd8;
  --cm-primary-50: #fff0fb;
  --cm-primary-100: #ffe0f7;
  --cm-primary-200: #ffc2ef;
  --cm-primary-300: #ff93e4;
  --cm-primary-400: #ff64d9;
  --cm-primary-500: #ff4fd8;
  --cm-primary-600: #e03ab8;
  --cm-primary-700: #b82a98;
  --cm-primary-800: #901e78;
  --cm-primary-900: #681458;

  --cm-primary-gradient: linear-gradient(135deg, #ff4fd8 0%, #a855f7 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #ff64d9 0%, #b86aff 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #e03ab8 0%, #9333ea 100%);
}
```

### 2.2 Male Theme (#4f9fff Blue)

```css
[data-theme="male"] {
  --cm-primary: #4f9fff;
  --cm-primary-50: #eff6ff;
  --cm-primary-100: #dbeafe;
  --cm-primary-200: #bfdbfe;
  --cm-primary-300: #93c5fd;
  --cm-primary-400: #60a5fa;
  --cm-primary-500: #4f9fff;
  --cm-primary-600: #2563eb;
  --cm-primary-700: #1d4ed8;
  --cm-primary-800: #1e40af;
  --cm-primary-900: #1e3a8a;

  --cm-primary-gradient: linear-gradient(135deg, #4f9fff 0%, #7c3aed 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #60a5fa 0%, #8b5cf6 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #2563eb 0%, #6d28d9 100%);
}
```

### 2.3 Neutral Theme (#a0a0b0 Gray)

```css
[data-theme="neutral"] {
  --cm-primary: #a0a0b0;
  --cm-primary-50: #f5f5f7;
  --cm-primary-100: #e8e8ec;
  --cm-primary-200: #d1d1d9;
  --cm-primary-300: #b3b3bf;
  --cm-primary-400: #a0a0b0;
  --cm-primary-500: #8888a0;
  --cm-primary-600: #6e6e88;
  --cm-primary-700: #585870;
  --cm-primary-800: #424258;
  --cm-primary-900: #2c2c40;

  --cm-primary-gradient: linear-gradient(135deg, #a0a0b0 0%, #7c7c94 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #b3b3bf 0%, #8e8ea8 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #8888a0 0%, #6a6a84 100%);
}
```

---

## 3. Semaantik Renkler

```css
:root {
  /* ═══ Success ═══ */
  --cm-success-50: #ecfdf5;
  --cm-success-100: #d1fae5;
  --cm-success-200: #a7f3d0;
  --cm-success-300: #6ee7b7;
  --cm-success-400: #34d399;
  --cm-success-500: #10b981;
  --cm-success-600: #059669;
  --cm-success-700: #047857;
  --cm-success-800: #065f46;
  --cm-success-900: #064e3b;

  /* ═══ Warning ═══ */
  --cm-warning-50: #fffbeb;
  --cm-warning-100: #fef3c7;
  --cm-warning-200: #fde68a;
  --cm-warning-300: #fcd34d;
  --cm-warning-400: #fbbf24;
  --cm-warning-500: #f59e0b;
  --cm-warning-600: #d97706;
  --cm-warning-700: #b45309;
  --cm-warning-800: #92400e;
  --cm-warning-900: #78350f;

  /* ═══ Error ═══ */
  --cm-error-50: #fef2f2;
  --cm-error-100: #fee2e2;
  --cm-error-200: #fecaca;
  --cm-error-300: #fca5a5;
  --cm-error-400: #f87171;
  --cm-error-500: #ef4444;
  --cm-error-600: #dc2626;
  --cm-error-700: #b91c1c;
  --cm-error-800: #991b1b;
  --cm-error-900: #7f1d1d;

  /* ═══ Info ═══ */
  --cm-info-50: #eff6ff;
  --cm-info-100: #dbeafe;
  --cm-info-200: #bfdbfe;
  --cm-info-300: #93c5fd;
  --cm-info-400: #60a5fa;
  --cm-info-500: #3b82f6;
  --cm-info-600: #2563eb;
  --cm-info-700: #1d4ed8;
  --cm-info-800: #1e40af;
  --cm-info-900: #1e3a8a;
}
```

---

## 4. Gri Ölçek

```css
:root {
  --cm-gray-50: #f8f9fa;
  --cm-gray-100: #f1f3f5;
  --cm-gray-200: #e9ecef;
  --cm-gray-300: #dee2e6;
  --cm-gray-400: #ced4da;
  --cm-gray-500: #adb5bd;
  --cm-gray-600: #868e96;
  --cm-gray-700: #495057;
  --cm-gray-800: #343a40;
  --cm-gray-900: #212529;
  --cm-gray-950: #0a0a0f;
}
```

---

## 5. Cam Efekti Renkleri

```css
:root {
  /* ═══ Glass Background ═══ */
  --cm-glass-white-5: rgba(255, 255, 255, 0.05);
  --cm-glass-white-8: rgba(255, 255, 255, 0.08);
  --cm-glass-white-10: rgba(255, 255, 255, 0.10);
  --cm-glass-white-12: rgba(255, 255, 255, 0.12);
  --cm-glass-white-15: rgba(255, 255, 255, 0.15);
  --cm-glass-white-20: rgba(255, 255, 255, 0.20);
  --cm-glass-white-30: rgba(255, 255, 255, 0.30);

  /* ═══ Glass Border ═══ */
  --cm-glass-border-5: rgba(255, 255, 255, 0.05);
  --cm-glass-border-8: rgba(255, 255, 255, 0.08);
  --cm-glass-border-10: rgba(255, 255, 255, 0.10);
  --cm-glass-border-14: rgba(255, 255, 255, 0.14);
  --cm-glass-border-18: rgba(255, 255, 255, 0.18);
  --cm-glass-border-24: rgba(255, 255, 255, 0.24);

  /* ═══ Glass Text ═══ */
  --cm-glass-text-primary: rgba(255, 255, 255, 0.95);
  --cm-glass-text-secondary: rgba(255, 255, 255, 0.70);
  --cm-glass-text-tertiary: rgba(255, 255, 255, 0.50);

  /* ═══ Dark Glass ═══ */
  --cm-dark-glass-bg: rgba(10, 10, 15, 0.70);
  --cm-dark-glass-border: rgba(255, 255, 255, 0.08);
}
```

---

## 6. Renk Eşleoğrunculuk Matrisi

| Kullanım | Renk Token | CSS Değeri |
|----------|-----------|------------|
| Ana buton | `--cm-primary` | `#ff4fd8` |
| Ana buton hover | `--cm-primary-light` | `#ff7ee4` |
| İkincil buton | `--cm-secondary` | `#a855f7` |
| Başarı durumu | `--cm-success` | `#10b981` |
| Uyarı durumu | `--cm-warning` | `#f59e0b` |
| Hata durumu | `--cm-error` | `#ef4444` |
| Bilgi durumu | `--cm-info` | `#3b82f6` |
| Varsayılan arka plan | `--cm-bg-primary` | `#0a0a0f` |
| Yükseltilmiş arka plan | `--cm-bg-elevated` | `#22222e` |
| Birincil metin | `--cm-text-primary` | `#ffffff` |
| İkincil metin | `--cm-text-secondary` | `#b0b0c0` |
| Cam arka plan | `--cm-glass-bg` | `rgba(255,255,255,0.05)` |

### 6.1 Figma Çıkarım Renkleri (SSOT: `tokens-1024.json` · `tokens-1920.json`)

> **Kaynak:** `tokens/tokens-1024.json` (colors: 14) + `tokens/tokens-1920.json` (colors: 12) — Figma API çıkarımı. Birleşim: **17 anahtar · 14 benzersiz hex**. SSOT sırası: PNG > Figma extracted > ASCII > Inventory > Tokens — bu tablo "Figma extracted" katmanıdır (AGENTS §7.2).
>
> **Çapraz doğrulama (2026-09-27):** İki dosyada ortak **9 anahtarın 9'u da aynı hex** → tierler arası renk çelişkisi yok. Hex'lerin hiçbiri bu dosyada daha önce bulunmuyordu → tamamı eklendi.
>
> **Kod kanıtı:** `#FF65E9` → `Css/04_Components/_player-info.css` L195-196 (`/* Figma: #FF65E9 */`) · `#FF00C8` → `Css/05_Pages/_home-components.css` L860 (`--pink-primary-button-*` fallback'ları `rgba(255, 0, 200, …)` = #FF00C8 tabanı).

| # | Figma Anahtar | Token Adı (yeni) | 1024 | 1920 | Hex | Kanıt / Not |
|---|---------------|------------------|:----:|:----:|-----|-------------|
| 1 | `KursatGurel / stroke` | `--cm-avatar-stroke` | ✓ | ✓ | `#707070` | Avatar stroke (`extracted-1920.md` L31, L113); `--cm-text-tertiary #707088`'e yakın ama **farklı rol/değer** → eşlenmedi |
| 2 | `Stroke Effect / fill` | `--cm-stroke-effect-fill` | ✓ | ✓ | `#373737` | Widget stroke dolgusu; master `:root`'ta karşılığı yok ⚠️ |
| 3 | `Stroke Effect / gradient stop 0` | `--cm-stroke-effect-gradient-stop-0` | ✓ | ✓ | `#070607` | Stroke gradient başlangıcı ⚠️ |
| 4 | `Stroke Effect / gradient stop 1` | `--cm-stroke-effect-gradient-stop-1` | ✓ | ✓ | `#5B5A5B` | Stroke gradient bitişi ⚠️ |
| 5 | `ProgressbarValue / fill` | `--cm-progressbar-value-fill` | ✓ | ✓ | `#FF38E3` | Ana progressbar değeri (`extracted-1920.md` L36) ⚠️ |
| 6 | `ProgressbarTick / gradient stop 0` | `--cm-progressbar-tick-stop-0` | ✓ | ✓ | `#FFBEF6` | Tick gradient kenarı ⚠️ |
| 7 | `Progressbar / gradient stop 1` | `--cm-progressbar-gradient-stop-1` | ✓ | ✓ | `#D5D5D5` | Progressbar ray ortası (`extracted-1920.md` L65) ⚠️ |
| 8 | `Core Music / stroke` | `--cm-logo-stroke` | ✓ | ✓ | `#FE00E4` | Logo stroke (`extracted-1920.md` L76) ⚠️ |
| 9 | `bg / gradient stop 0` | `--cm-pink-primary-button` | ✓ | ✓ | `#FF00C8` | **Kod kanıtlı:** `_home-components.css` L860 — `--pink-primary-button-*` bu dokümanda/`design-tokens-master`'da **tanımsız** (bilinen açık çelişki) |
| 10 | `Romantic_Background_03 (2) / fill` | `--cm-page-background-fill` | ✓ | — | `#FFFFFF` | Romantik zemin; `--cm-text-primary #ffffff` ile aynı hex, **rol farklı** |
| 11 | `Siyah Arkaplan Evekt / gradient stop 0` | `--cm-black-bg-effect-gradient-stop-0` | ✓ | — | `#000000` | Siyah overlay gradient 0 ⚠️ |
| 12 | `Siyah Arkaplan Evekt / gradient stop 1` | `--cm-black-bg-effect-gradient-stop-1` | ✓ | — | `#120C14` | Siyah overlay gradient 1 ⚠️ |
| 13 | `Ellipse 16 / gradient stop 0` | `--cm-ellipse-16-gradient-stop-0` | ✓ | — | `#140E16` | Dekoratif elips zemini ⚠️ |
| 14 | `ProgresbarValue / fill` | `--cm-progressbar-tick-stop-1` | ✓ | — | `#FF65E9` | Anahtar Figma'da yazım hatalı (1024: `Progresbar`); **kod kanıtlı:** `_player-info.css` L196 |
| 15 | `Linux - 1920 - Home / fill` | `--cm-page-background-fill` | — | ✓ | `#FFFFFF` | 1920 sayfa zemini |
| 16 | `Ana Sayfa / stroke` | `--cm-black-bg-effect-gradient-stop-0` | — | ✓ | `#000000` | 1920 sayfa stroke — hex aynı, **rol farklı** (satır 11 gradient stop ile aynı token'a eşlendi; Stroke rolü için ayrı ad gerekirse `-figma` soneki kuralı devreye girer) ⚠️ |
| 17 | `ProgressbarTick / gradient stop 1` | `--cm-progressbar-tick-stop-1` | — | ✓ | `#FF65E9` | 1920 tick orta stop — 1024'te aynı hex farklı anahtarda (`ProgresbarValue`) |

**Adlandırma notu (2026-09-27):** 14 benzersiz hex → 14 token adı; 17 satırın tekrar eden hex'leri (satır 10/15, 11/16, 14/17) **aynı adı** taşır. Adlar İngilizce/kebab-case/`--cm-` öneklidir; Figma node adından türetilmiştir. Repo geneli çakışma taraması **0 eşleşme** → `-figma` soneki gerekmedi (ayrıntı: `design-tokens-master.md` §2.1.1). **Adlar yalnızca bu vault tablosundadır — CSS `:root`'a tanımsız (CSS'e dokunulmadı).**

**Çelişki / Deprecated kontrolü (Truth Mode):**

- Figma hex'leri mevcut tema hex'leriyle (`--cm-primary #ff4fd8` vb.) **aynı rolde çelişmiyor** (roller: progressbar/overlay/stroke vs tema primary) → **deprecated işaretlenmedi.**
- Tek yakın değer: `#707070` (Figma stroke) vs `--cm-text-tertiary #707088` — eşit değil, rol farklı; ikisi de korunur.
- **Kapanan açık:** 14 hex'ten yalnız `#FF65E9` ve `#FF00C8` kodda kanıtlanıyordu; kalan 12'sine CSS custom property adı **atanmamıştı** → 2026-09-27'de **14/14'e ad atandı** (uydurma yok: her adın hex + Figma node karşılığı tabloda yazılı). ⚠️ Kalan doğrulama: adların CSS'e uygulanması **kod katmanı işidir** (backend/ui), bu dosyada yapılmadı.
- **Overlay alpha/blur çelişkisi** bu dosyanın konusu değil; SSOT kaydı: `design-tokens-master.md` §2.1.2 (Figma `0.35`/`3` SSOT · `0.60`/`1.5` deprecated).

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.2.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Theme Count | 3 (female, male, neutral) |
| Semantic Scales | 4 (success, warning, error, info) × 10 |
| Gray Scale | 11 steps (50-950) |
| Glass Colors | 15 |
| Figma Extracted Colors | 14 benzersiz hex · 17 anahtar (1024: 14 · 1920: 12) |
| Figma Token Names | 14/14 ad atandı (§6.1) — çakışma 0 |
| Total Color Values | 99+ |
| Cross References | 3 |
| Last Updated | 2026-09-27 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
