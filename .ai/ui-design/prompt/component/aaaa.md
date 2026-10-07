---
reference_doc: "Template (Guardrail #16) — .ai/.templates/ui-design/prompt-template.md (Kalıp C)"
title: "CoreMusic — Player Info (1024/1920/4K) Frontend Production Prompt"
type: prompt
category: ui-design
pattern: C
component: player-info
date: 2026-09-30
version: 2.0.0
status: active
authority: "Prompt (Kalıp C) — SSOT: .ai/.templates/ui-design/prompt-template.md"
governance: Red Team · Human Mode · Truth Mode
assignee: "Frontend-Developer (UI Designer, A3 — K10-K11)"
reference:
  authority: ".ai/.templates/ui-design/prompt-template.md"
  source_of_truth: ".ai/ui-design/prompt/00-prompt-index.md §7 · assets.coremusic.net/AGENTS.md §CSS SSOT"
---

# Player Info — Frontend Production Prompt (1024 / 1920 / 4K)

> **GÖREV SAHİBİ:** Frontend-Developer (UI Designer). **SEVİYE:** Senior — kopyala-yapıştır yok, SSOT'tan oku, kısa düşün, edit et.
> **NE ZAMAN OKUNUR:** Player Info bileşeni Figma "if renderer" karşılığı 1024×600 / 1920×1080 / 3840×2160 görünümlerinde kodlanırken ve regresyon test edilirken.

**Zorunlu Bağlantılar:** [[00-prompt-index]] · [[../../AGENTS.md]] · [[../../../CLAUDE.md]] · [[C06-form-input]] (kalıp örneği)

---

## 1. Context

**Problem:** Player Info bileşeninin 1024 ve 1920 px görünümleri Figma'ya birebir oturmalı; 4K devamı aynı token zincirinden gelmeli. Mevcut kodda **7 kritik sorun** tespit edildi ve düzeltildi — CSP ihlali, cascade hatası, renk uyumsuzluğu, yanlış import, eksik JS init. Düzeltmeler yapıldı, **browser testi bekliyor**.

**7 Düzeltilen Sorun (2026-09-30):**

| # | Sorun | Dosya | Çözüm |
|---|-------|-------|-------|
| S1 | CSP ihlali: `style="width:X%"` inline — `style-src` `'unsafe-inline'` yok | `player-info.php:44,61` | Inline style + nonce attribute kaldırıldı; `data-progress` korundu |
| S2 | JS init'de `setProgress()` çağrılmıyor — progress 0% | `PlayerInfoComponent.js:71` | `mount()` içine `this.setProgress(this.#progress)` eklendi |
| S3 | 4K renk uyumsuz — bg beyaz vs pembe | `a-layout-tokens-1024.css:69-70` | `--cm-player-bg` + `--cm-player-border` pembe ile senkron |
| S4 | `d-laptop.css` 4K token (750×300) import ediyor | `d-laptop.css:2` | Import `a-layout-tokens.css` olarak değiştirildi |
| S5 | Cascade: `@media(1441px)` END'de 3840 bloğunu eziyor | `a-layout-tokens.css:717` | `and (max-width:3839px)` eklendi |
| S6 | `.env` auth bypass zaten aktif ama `TEST_MODE=false` | `home.coremusic.net/config/.env` | `TEST_MODE=true` yapıldı |
| S7 | CSS versiyon `?v=1.1.0` — cache busting | 5 device CSS | `?v=2.0.0`'a bump edildi |

**CSS versiyon bump — etkilenen dosyalar:**
- `d-embedded.css`, `d-desktop.css`, `d-laptop.css`, `d-tablet.css`, `d-4k.css` → `_player-info.css?v=2.0.0`

**CSP-safe progress akışı (S1+S2 düzeltmesi sonrası):**
1. PHP: `data-progress="X"` attribute render eder (inline style YOK)
2. CSS: `width: var(--progress-percent, 0)` — JS set edene kadar 0
3. JS: `mount()` → `setProgress()` → `style.setProperty('--progress-percent', 'X%')` (CSP-safe)
4. `ProgressComponent.js` ile tutarlı (aynı custom property approach)

**Kapsam (SINIR — ihlal = görev reddi):**

| YAPILACAK | YAPILMAYACAK |
|-----------|--------------|
| CSS yalnız `C:\www\coremusic.net\assets.coremusic.net\Css\` (SSOT) — **taşınmaz, kopyalanmaz** | Başka dizine CSS/JS taşımak |
| **Mevcut sayfa** üzerinde düzeltme (`home.coremusic.net/pages/...`, `/home` route'u) | **Yeni test sayfası / yeni test dosyası / yeni dosya oluşturmak** |
| Figma raw node ölçülerine birebir hizalama | Uydurma ölç, tahmin token, `clamp()` varsayımı |
| Browser'da 1024×600, 1920×1080, 3840×2160 doğrulama | Sadece kod okuyup "geçti" demek |

**Mevcut durum (2026-09-30):** 7 sorun düzeltildi, CSS/JS/PHP edit edildi. Token zinciri: T07 `392×131` embedded · T17 `469×184` wide · 4K `750×300` wide. **Browser testi + unit/E2E test koşusu bekliyor.**

---

## 2. Required Inputs

| # | Dosya | Ne için |
|---|-------|---------|
| 1 | `.ai/ui-design/reference/figma/raw/nodes-1024-1920.json` | Ham Figma SSOT — node `1639:9772` (Playlist Status Div, `Res:1920`, `visible:false`) → background `469×184` @(670,-255), cover `168×152` @(685,-239). **Not:** `1646:17727` / `2849:21489` / `1646:17709` node'ları bu JSON'da YOK — eski referanslar, düzeltilmiştir. |
| 2 | `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css` | 768–1024 bloğu → T07 pin (`--cm-player-w: 392px`); 1441px bloğu → T17 pin (`469px`, `and (max-width:3839px)` ile 4K'yı ezmeyecek); 3840px bloğu → 4K (`750×300`) |
| 3 | `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens-1024.css` | 4K `--cm-player-*` (w750/h300, satır 63); `d-4k.css` import eder. Renkler pembe ile senkron (S3 fix). `d-laptop.css` artık `a-layout-tokens.css` import ediyor (S4 fix). |
| 4 | `assets.coremusic.net/Css/04_Components/_player-info.css` | BEM bileşeni; `?v=2.0.0`; progress fill `width: var(--progress-percent, 0)` (S1 fix); `max-width:1440px` → `__duration { display:none }` |
| 5 | `assets.coremusic.net/AGENTS.md` §CSS SSOT | BEM + ITCSS + token-first + CSP (inline yasak) + ADR-001 (framework yasak) |
| 6 | `assets.coremusic.net/tests/{e2e/player-info.spec.ts, components/player-info.spec.js}` | 21 E2E + 47 unit — **mevcut dosyalar**, yenisi yazılmaz |
| 7 | `assets.coremusic.net/js/components/interactive/PlayerInfoComponent.js` | JS interactivity; `mount()` içine `setProgress()` eklendi (S2 fix); `setProperty('--progress-percent', ...)` CSP-safe (S1 fix) |

**Mockup Gate (§13):** `.ai/.png/` altındaki `home-1024` / `home-1920` görselleri kod yazımdan ÖNCE okunur. **Not:** AI modeli görsel okuyamıyor — Figma JSON SSOT kullanılır, görsel doğrulama kullanıcı tarafından yapılır.

---

## 3. ASCII Reference

```
1024×600 (T07, d-embedded.css)         1920×1080 (T17, d-desktop.css) / 4K (d-4k.css)
┌──────────────────────────┐           ┌────────────────────────────────────┐
│ now-playing--embedded    │           │ player-info--wide   469×184 / 750×300
│  392 × 131 px            │           │  [cover] [title] [progress]        │
│  [.] [title] [00:05:00]  │           │  .player-info__duration  GÖRÜNÜR   │
│  .media-progress__time   │           │  (≤1440px'de display:none)         │
└──────────────────────────┘           └────────────────────────────────────┘

Progress fill akışı (CSP-safe):
  PHP: data-progress="30" (inline style YOK)
  CSS: width: var(--progress-percent, 0) → 0% initially
  JS:  mount() → setProgress(30) → setProperty('--progress-percent', '30%')
       → CSS 30% render eder (transition: width 0.5s linear)
```

---

## 4. Prompt (Dispatch — Frontend-Developer'e birebir)

```text
SEVİYE: Senior Frontend Developer. Kısa oku, kısa düşün, edit et. Adım adım git, her adımda kanıt üret.

GÖREV: Player Info'yi Figma "if renderer" karşılığı gibi 1024/1920/4K'da YAP, MEVCUT SAYFADA test et.

KURALLAR (ihlal = dur):
R1. CSS yalnız C:\www\coremusic.net\assets.coremusic.net\Css\ — taşınmaz, başka yere yazılmaz.
R2. YENİ DOSYA YOK: yeni test sayfası, yeni test dosyası, yeni bileşen dosyası YOK. Düzeltme = mevcut
    dosyaların MEVCUT satırlarında edit.
R3. Test = mevcut sayfada (http://home.coremusic.net:81/home) Playwright MCP ile browser'da; bypass auth
    (ADR-008 BypassAuthMiddleware) ile oturum engeli kaldırılır. .env'de FORCE_AUTH_BYPASS=true zaten aktif.
R4. Ölçü = Figma raw SSOT (nodes-1024-1920.json). Tahmin/uydurma ölç yok. Node 1639:9772 → 469×184.
R5. Sadece token kullan (--cm-player-*); 01_Abstracts dışında ham px/hex YOK; inline style/script YOK (CSP).
R6. Progress fill: inline style="width:X%" YASAK. data-progress attribute + JS setProperty('--progress-percent').

ADIMLAR:
1. [Oku] Required Inputs tablosundaki 1-7 dosyayı oku. 7 sorun düzeltildi — mevcut PASS değerlerine
   DOKUNMA, bozulanı düzelt.
2. [Test-1024] Playwright: viewport 1024×600, /home navigate (2 kez yükle — cookie cm_viewport_w lag'ı var,
   resize ÖNCE navigate). Doğrula: w=392 h=131 cls=embedded token=392px time=visible progress>0%.
3. [Test-1920] viewport 1920×1080 → w=469 h=184 cls=wide token=469px time=visible progress>0%.
4. [Test-4K] viewport 3840×2160 → w=750 h=300 cls=wide token=750px time=visible progress>0%.
5. [CSP doğrula] Console'da CSP ihlali YOK (style-src violation). Progress fill genişliği JS sonrası set edilmiş.
6. [Regresyon] Console 0 hata/uyarı; CSS cache'e takılıysa CDP Network.clearBrowserCache; CSS sürüm
   ?v=2.0.0 (cache-buster sadece JS mtime'larına bakar — HtmlShellRenderer.php busterFiles sadece JS).
7. [Testleri koştur] npm run test:unit -- player-info.spec.js · npm run test:e2e -- player-info.spec.ts
   (mevcut dosyalar; başarısızlık varsa MEVCUT testte değil KODDA ara).
8. [Sonuç topla] 3 breakpoint tablosu + console + CSP + test sonuçları + değişen dosyalar listesi.
```

---

## 5. Expected Output (Düzeltilen + Korunan)

**Düzeltilen (S1-S7):**
- ✅ S1: `style="width:X%"` inline → kaldırıldı; `width: var(--progress-percent, 0)` CSP-safe
- ✅ S2: JS `mount()` → `setProgress()` çağrıldı; progress initial 0% değil
- ✅ S3: 4K `--cm-player-bg: rgba(220,50,150,0.2)` pembe (beyaz değil)
- ✅ S4: `d-laptop.css` → `a-layout-tokens.css` import (4K token değil)
- ✅ S5: `@media(1441px) and (max-width:3839px)` — 3840 bloğunu ezmez
- ✅ S6: `.env` `TEST_MODE=true` (bypass zaten aktifti)
- ✅ S7: `_player-info.css?v=2.0.0` (5 device CSS)

**Korunan (doğru değerler):**
- ✅ T07 pin: `--cm-player-w: 392px; --cm-player-h: 131px` (768-1024 bloğu)
- ✅ T17 pin: `--cm-player-w: 469px; --cm-player-h: 184px` (1441-3839 bloğu)
- ✅ 4K: `--cm-player-w: 750px; --cm-player-h: 300px` (3840px bloğu / a-layout-tokens-1024.css)
- ✅ `.player-info__duration` `@media(max-width:1440px) → display:none`
- ✅ BEM: `player-info__element--modifier` · ITCSS Layer 04_Components · WCAG 2.2 AA
- ✅ `d-4k.css` import zinciri korundu (hala `a-layout-tokens-1024.css` import ediyor)

---

## 6. Validation (Definition of Done)

| # | Kriter | Kanıt |
|---|--------|-------|
| V1 | 1024×600 → 392×131 embedded, time görünür, progress>0% | Playwright evaluate + screenshot `c06-t07-1024.png` |
| V2 | 1920×1080 → 469×184 wide, time görünür, progress>0% | screenshot `c06-t17-1920.png` |
| V3 | 3840×2160 → 750×300 wide, token tanımlı, pembe bg | screenshot `c06-4k-3840.png` |
| V4 | Console 0 hata/uyarı — CSP ihlali YOK | CDP console listesi |
| V5 | unit 47 + E2E 21 test | `npm run test:unit` / `test:e2e` çıktısı |
| V6 | Yeni dosya üretilmedi; CSS yolu sabit | `git status` (yalnız mevcut dosya edit'leri) |
| V7 | Değişiklik commit edilmedi | commit orkestratöre ait (verify-loop §0) |
| V8 | `d-laptop.css` → `a-layout-tokens.css` import | grep `a-layout-tokens` d-laptop.css |
| V9 | Progress fill `var(--progress-percent,0)` | grep `_player-info.css` |
| V10 | JS `mount()` → `setProgress()` çağrısı | grep `PlayerInfoComponent.js` |

---

## 7. Değişen Dosyalar Listesi

| Dosya | Değişiklik |
|-------|-----------|
| `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css` | S5: `@media(1441px)` → `and (max-width:3839px)` |
| `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens-1024.css` | S3: bg + border pembe senkron |
| `assets.coremusic.net/Css/08_Devices/d-laptop.css` | S4: import → `a-layout-tokens.css`; S7: `?v=2.0.0` |
| `assets.coremusic.net/Css/04_Components/_player-info.css` | S1: `width: var(--progress-percent, 0)` |
| `assets.coremusic.net/Css/08_Devices/d-embedded.css` | S7: `?v=2.0.0` |
| `assets.coremusic.net/Css/08_Devices/d-desktop.css` | S7: `?v=2.0.0` |
| `assets.coremusic.net/Css/08_Devices/d-tablet.css` | S7: `?v=2.0.0` |
| `assets.coremusic.net/Css/08_Devices/d-4k.css` | S7: `?v=2.0.0` |
| `assets.coremusic.net/js/components/interactive/PlayerInfoComponent.js` | S2: `mount()` init setProgress; S1: `setProperty('--progress-percent')` |
| `home.coremusic.net/pages/components/player-info.php` | S1: inline style + nonce kaldırıldı |
| `home.coremusic.net/config/.env` | S6: `TEST_MODE=true` |

---

## 8. References (otomatik toplandı)

**Harici:**
1. MDN — `clamp()` CSS function (2026-08-12; Baseline: July 2020) — https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/clamp
2. web.dev — Min, max, clamp: CSS functional AKA (2020-10-14) — https://web.dev/articles/min-max-clamp
3. MDN — CSS Custom Properties (`--*`) — https://developer.mozilla.org/en-US/docs/Web/CSS/--*
4. MDN — CSP `style-src` directive — https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/style-src
5. caniuse — mdn-css_types_clamp — https://caniuse.com/mdn-css_types_clamp

**Depo içi:**
6. Figma ham SSOT — `.ai/ui-design/reference/figma/raw/nodes-1024-1920.json` (node `1639:9772`)
7. ADR-008 Bypass Auth Middleware — `.ai/.decisions/accepted/ADR-008-*`
8. ADR-012 CSP Nonce strict-dynamic — `.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md`
9. CSS SSOT kuralı — `assets.coremusic.net/AGENTS.md` · cache-buster gerçeği — `shared/src/PageRouter/HtmlShellRenderer.php:83` (`busterFiles` = JS only)
10. C06 örnek prompt kalıbı — `.ai/ui-design/prompt/component/C06-form-input.md`
11. `DeviceCssMap.php` — `shared/src/Device/DeviceCssMap.php` (device → CSS dosya eşlemi)
12. `shouldRender4kLayout()` — `shared/src/Device/DeviceManager.php:579` (deprecated, her zaman false; 4K Wide markup + `d-4k.css` ile çalışır)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-30 (v2.0.0 — 7 sorun düzeltildi)
**Mode:** Red Team · Human Mode · Truth Mode
