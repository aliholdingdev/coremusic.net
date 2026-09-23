---
title: "CoreMusic — Frontend Architecture Decision Record Template"
type: template
category: adr
date: 2026-09-23
updated: 2026-09-23
version: 1.0.0
status: active
authority: SSOT
---

# CoreMusic — Frontend Architecture Decision Record Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[adr-template]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../brain.md]] · [[../../WORKFLOW.md]]

---

## 1. Amaç

Frontend mimari kararlarının (ITCSS katmanı, BEM namespace, design token'ı, component contract'ı) `adr-template.md` ile **AYNI § başlık dizisinde**, domain'e özgü §8-§11 ek bölümleriyle standart biçimde kaydedilmesini sağlar. **Guardrail #16:** UI Designer frontend kararında bu şablonu kullanır; **Guardrail #11:** karar içeriği mockup/inventory okunmadan doldurulamaz.

| Alan | Değer |
|------|-------|
| Bu şablon neyi üretir | `ADR-NNN-<frontend-slug>.md` karar dosyası |
| Ana iskelet | `adr-template.md` §1 Bağlam → §7 Onay (DRY — birebir korunur) |
| Domain ekleri | §8 Katman Etkisi · §9 WCAG Etkisi · §10 Token Değişikliği · §11 Cihaz Matrisi Etkisi |
| Sorumlu agent | UI Designer (AGENTS.md §6: CSS, ITCSS, BEM, token, responsive) |
| İkincil agent | QA Engineer (WCAG/karne), Security Engineer (CSP nonce/inline script) |
| Zorunlu okuma | `.ai/ui-design/01-mockup-index.md` + `02-component-inventory.md` + `00-device-matrix.md` (Guardrail #11) |
| Yasak | React/Vue/Ayrı HTML/Ayrı branch (ADR-001, Guardrail #17) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| ITCSS katman yerleşimi kararı (`01_Abstracts` … `11_OAuth`) | Backend routing/middleware kararları (→ `adr-template.md`) |
| BEM namespace / block-modifier sözleşmesi (`c-*`, `p-*`, `d-*`, `v-*`, `u-*`) | Veritabanı şema kararı (→ `adr-database-template.md`) |
| Design token eklemesi/değişikliği (`a-layout-tokens.css`) | Güvenlik başlığı politikası (→ `adr-security-template.md`, ADR-012) |
| Component contract (C01-C16 envanter davranışı) | Ses/DSP/donanım kararı (→ `adr-audio-template.md`) |
| Cihaz matrisi (45-tier) / breakpoint davranışı (Guardrail #17) | PHP controller/service yapısı |
| WCAG 2.2 AA etkisi olan her sunum kararı | PWA/Flutter kararları (ADR-031 ayrı ADR'dir) |

- **Dosya tipi:** Markdown ADR — adlandırma `ADR-NNN-<slug>.md`
- **Teknoloji:** Vanilla JS ES6+, ITCSS 9-layer, BEM+BEMIT, CSS custom properties
- **Katman:** K11 (Kullanıcı Deneyimi) / K10 (Uygulama)
- **Guardrail bağları:** #11 (Mockup Before Frontend), #16 (Template Mandatory), #17 (Single Component Responsive)

---

## 3. Mimari

§3.1-§3.8 `adr-template.md` ile **aynı ADR § dizisidir** (DRY); §3.9-§3.12 bu şablonun domain ekleridir. Her alt bölüm tek tablo veya tek kod bloğu içerir (doldurulmuş ÖRNEK ile — boş iskelet değildir).

### 3.1 ADR Künyesi (iskelet §0)

| Alan | Örnek dolgu |
|------|-------------|
| Başlık | `ADR-0NN — <Karar özeti: örn. "Widget grid token'a taşındı">` |
| Durum | `Draft` → `Review` → `Active` → `Frozen` |
| Tarih | `YYYY-MM-DD` |
| Karar Veren | Vault Steward + UI Designer |
| İlgili ADR'ler | `ADR-001` (Vanilla JS+ITCSS), `ADR-044` (Tema), `ADR-048` (View Transition) |
| Etkilenen katman | K11 · K10 |
| Mockup kanıtı | `.ai/ui-design/01-mockup-index.md` (19 PNG) — **okundu ✅** |

### 3.2 §1 Bağlam (Context)

| Alt alan | Doldurma kuralı | Örnek dolgu |
|----------|-----------------|-------------|
| 1.1 Mevcut Durum | Glob/measure kanıtı ile yaz | `04_Components/_widget-grid.css` 4 kolon sabit; `05_Pages/_home-layout.css` gap hardcode |
| 1.2 Sorun Tanımı | Kullanıcıya görünen hata | 1920'de widget taşması; 1024×600'de 2. satır kesiliyor |
| 1.3 Web Araştırması | Query + Sonuç + Alınan Karar 3 satır | `ITSS token layering best practice` → token tek dosyada → `a-layout-tokens.css` |
| 1.4 Kısıtlar | Yasak/limit tablosu | Framework yasak (ADR-001); ayrı HTML yasak (GR-17); PNG > ASCII > Inventory sırası |

### 3.3 §2 Karar (Decision)

```markdown
## 2. Karar (Decision)
Widget grid genişliği sabit px yerine CSS custom property token'ına taşınır:
  --widget-grid-cols / --widget-grid-gap / --home-top-split
Karar: token'lar `01_Abstracts/a-layout-tokens.css` içinde, `:root` = 1024×600
(mockup reference) + 4 media-query override (767 / 768-1024 / 1920 / 3840) ile tanımlanır.
Component HTML'i DEĞİŞMEZ (tek component sistemi, Guardrail #17).
Bileşen sözleşmesi: `.c-widget-grid` block + `.c-widget-grid--wide` modifier.
```

> Doldurma kuralı: karar tek paragraf = **Ne** + **Nerede** + **Nasıl doğrulanır**. Kod değişikliği gerekliyse §3.3 yerine diff örneği (CSS/JS) eklenir.

### 3.4 §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | `device-loader.js` ile tam CSS swap | Cihaz başına ince ayar | FOUC, 2 dosya bakımı | Yasak örüntü (brain.md §18A) |
| 2 | `home-1024.html` + `home-desktop.html` ayrı HTML | Hızlı prototip | 19 PNG × 2 senkron | Guardrail #17 ihlali → revert |
| 3 | JS `if (screenWidth === 1024)` branch | Koşullu render | JS disabled'da bozuk | CSS media query + `var()` doğru olan |
| 4 | Hardcoded `width: 280px` | Sıfır migration | 4K'da bozulur | Token zorunlu (§10) |
| 5 | CSS framework (Bootstrap) build | Hızlı grid | Bundle şişer, vanilla yasak | ADR-001 (frozen) |

### 3.5 §4 Sonuçlar (Consequences)

| Tür | Madde | Etki |
|-----|-------|------|
| Olumlu | Tek token dosyası = tüm breakpoint tek yerden | Bakım ↓ |
| Olumlu | Mockup (1024×600) = `:root` default → PNG ile birebir | Guardrail #11 uyumu |
| Olumsuz | Token adı değişirse tüm `var()` çağrıları güncellenmeli | Kırık link benzeri risk |
| Olumsuz | Yeni token eklemek inventory'yi güncelleme yükü getirir | `02-component-inventory.md` senkronu |
| Risk | Eski `d-*.css` dosyalarında hardcoded değer kalması | Olasılık: Orta → §10 geçiş tablosu |
| Risk | `Css copy/` ikinci kopyanın diverjensi | Olasılık: Yüksek → tek kaynak ilanı gerekli |

### 3.6 §5 Uygulama (Implementation)

| # | Adım | Sorumlu | Kanıt |
|---|------|---------|-------|
| 1 | 19 PNG + ASCII art + inventory oku | UI Designer | `01-mockup-index.md` ✅ |
| 2 | `a-layout-tokens.css`'e token ekle (yoksa) | UI Designer | git diff |
| 3 | Component'te hardcoded → `var()` geçişi | UI Designer | grep: hardcoded px kalmadı |
| 4 | 45-tier matriste 9 kırılım testi | QA Engineer | `00-device-matrix.md` tablosu |
| 5 | WCAG kontrast/focus doğrulama | QA Engineer | `04-accessibility-gaps.md` |
| 6 | Cross-reference + `log.md` append | MO | log satırı |
| R | Geri Dönüş | UI Designer | token adı geri alınır; `git revert` — dosya adı değişmez (In-Place) |

### 3.7 §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| `[[../CLAUDE.md]]` | Guardrail #11/#16/#17 |
| `[[../../brain.md]]` | §18A Responsive CSS Architecture, §18C Device-Aware |
| `[[../../ui-design/01-mockup-index]]` | 19 PNG kanonik indeks |
| `[[../../ui-design/02-component-inventory]]` | C01-C16 BEM + piksel standart |
| `[[../../ui-design/05-responsive-architecture]]` | §7.4 4K No-Center, §12 fallback |
| `[[adr-template]]` | Ana ADR iskeleti (DRY kaynağı) |

### 3.8 §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | {{DATE}} | ✅ |
| UI Designer | {{AUTHOR}} | {{DATE}} | ⏳ |
| Tech Lead | {{TECH_LEAD}} | {{DATE}} | ⏳ |
| Arch Lead | {{ARCH_LEAD}} | {{DATE}} | ⏳ |

### 3.9 §8 Katman Etkisi (DOMAIN)

ITCSS katmanına etki — glob kanıtı `assets.coremusic.net/Css copy/`:

| Katman | Dizin | Karar etkisi | Kanıt durumu |
|--------|-------|--------------|--------------|
| 01 Abstracts | `01_Abstracts/a-layout-tokens.css` | Token eklenirse burası değişir (§10) | brain.md §18A referansı |
| 02 Settings | `02_*` | Değişirse_settings kaydı gerekir | ⚠️ glob'da görünmedi (sınırlı tarama) |
| 03 Tools | `03_*` | Mixin/func eklenirse | ⚠️ glob'da görünmedi |
| 04 Components | `04_Components/c-*.css` (c-card, c-badge, c-buttons, c-modal, c-toast, c-progress, c-toggle, c-forms …) | BEM block değişimi → inventory senkronu | ✅ glob |
| 05 Pages | `05_Pages/p-*.css` (_home-layout, _home-components, p-settings, p-playlist, p-artists, p-albums …) | Layout kararı → §11 cihaz matrisi | ✅ glob |
| 06 Utilities | `06_Utilities/u-helpers-utility.css` | Utility sınıfı eklemek son çare | ✅ glob |
| 07 Vendors | `07_Vendors/v-bootstrap-lib.css` | Yeni vendor yasağı (vanilla) | ✅ glob |
| 08 Devices | `08_Devices/d-embedded/desktop/tablet/laptop/phone/4k*.css` + `d-auth-*` | **Sadece behavioral override** (hover/touch/scrollbar) | ✅ glob (14+ dosya) |
| 09 ViewModes | `09_ViewModes/v-home/v-car/v-pro/v-studio.css` | View mode token'ları | ✅ glob |
| 11 OAuth | `11_OAuth/oauth.css` | Auth paneline özel | ✅ glob |

**Katman karar ağacı:** Yerleşim mi? → `05_Pages` · Bileşen mi? → `04_Components` · Cihaz davranışı mı? → `08_Devices` (yalnız davranış) · Renk/ölçü mü? → `01_Abstracts` token · Hiçbiri → utility `06` (son çare).

### 3.10 §9 WCAG Etkisi (DOMAIN)

| Kriter (WCAG 2.2 AA) | Eşik | Karar etkisi | Doğrulama |
|----------------------|------|--------------|-----------|
| 1.4.3 Kontrast | ≥ 4.5:1 | Token rengi değişirse AA bozulur | Kontrast hesabı |
| 1.4.11 Non-text kontrast | ≥ 3:1 | Border/focus token'ı | Token tablosu |
| 2.4.7 Focus Visible | `:focus-visible` outline | Yeni component zorunlu kural | `04-accessibility-gaps.md` |
| 2.5.8 Target Size | ≥ 24×24px (geniş) / 48×48px (dokunmatik) | `--touch-target` token | Cihaz matrisi §11 |
| 2.1.1 Keyboard | Tüm etkileşim | JS component contract (klavye) | Vitest + manuel |
| 4.1.2 Name/Role/Value | ARIA | BEM block + `aria-*` eşleşmesi | Envane audit |
| 1.3.4 Orientation | Dikey/yatay | Layout token ≠ sabit yükseklik | 45-tier test |

> **Karar kuralı:** WCAG sütununda ✅ olmayan hiçbir frontend ADR `Active` olamaz (§7 onay akışı öncesi QA Engineer imzası).

### 3.11 §10 Token Değişikliği (DOMAIN)

```css
/* 01_Abstracts/a-layout-tokens.css — ÖRNEK karar bloğu */
:root {
  --header-h: 60px;        /* 1024×600 mockup reference (Guardrail #17) */
  --footer-h: 90px;
  --content-h: 450px;
  --home-top-split: 42% 58%;
  --widget-grid-cols: 2;
  --widget-grid-gap: 16px;
  --touch-target: 48px;    /* WCAG 2.5.8 — dokunmatik */
}
@media (max-width: 767px)   { :root { --widget-grid-cols: 1; --footer-h: 80px; } }
@media (min-width: 768px) and (max-width: 1024px) { :root { --widget-grid-cols: 2; } }
@media (min-width: 1920px)  { :root { --header-h: 70px; --footer-h: 104px; --widget-grid-cols: 3; } }
@media (min-width: 3840px)  { :root { --header-h: 80px; --footer-h: 120px; --widget-grid-cols: 3; } }

/* ❌ YASAK: height: 90px  →  ✅ DOĞRU: height: var(--footer-h) */
```

| Token değişiklik tipi | Zorunlu eşik |
|----------------------|--------------|
| Yeni token | `02-component-inventory.md`'ye satır ekle |
| Token silme | grep: çağrı yoksa + Tech Lead onayı |
| Token değer değişimi | 4 breakpoint override'ı birlikte güncelle |
| hardcoded px | `grep -E '[0-9]+px' 04_Components 05_Pages` → 0 hedefi |

### 3.12 §11 Cihaz Matrisi Etkisi (DOMAIN)

45-tier matristen karar etkisi (`00-device-matrix.md` + brain.md §18A):

| Tier | Cihazlar | Viewport | Karar etkisi | Mockup |
|------|----------|----------|--------------|--------|
| Phone | iPhone SE/14, Galaxy S23 | ≤767px | 1 kolon, bottom nav, volume gizli | — |
| Embedded | RPi5 7"/10" | 1024×600 | **Pixel reference**; welcome popup yalnız burada | `home-1024` (12 PNG) |
| Laptop/Desktop | 13"-34" | 1025-2560px | 3 kolon, sidebar | `home-1920` (1 PNG) |
| 4K | 32"/43"+ TV & monitor | ≥2561px | **Ortalamama YASAK** (§7.4) | — |
| Car/Watch/Console | Android Auto, PS5 … | tier behavior | Behavioral override `d-*.css` | `shared-1024` (6 PNG) |

**Kırılım kararı:** Tier davranış farkı CSS token + media query ile; JS'te cihaz branch'i ≠ layout kararı (Yasak Örüntüler tablosu, brain.md §18A).

### 3.13 §(ek) BEM Namespace Sözleşmesi (DOMAIN)

| Prefix | Kategori | Kapsam | Örnek (glob `Css copy/`) | Kural |
|---|---|---|---|---|
| `c-` | component | bileşen | `c-card`, `c-badge`, `c-modal` | inventory'de C01-C16 |
| `p-` | page | sayfa | `p-settings`, `p-playlist` | tek sayfaya özel |
| `d-` | device | cihaz davranışı | `d-phone`, `d-4k` | **yalnız davranış** (08) |
| `v-` | viewmode | görünüm modu | `v-home`, `v-car`, `v-pro` | 09 katmanı |
| `u-` | utility | tek amaçlı | `u-helpers-utility` | son çare (06) |
| `a-` | abstract | token/mixin | `a-layout-tokens` | yalnız 01 |
| `o-`/`s-` | object/standalone | layout/skin | — | ITCSS kuralı |
| `js-` | behavior hook | JS seçicisi | — | stil DEĞİL davranış |

```css
/* ✅ DOĞRU — block + modifier + element (BEMIT) */
.c-card { border: 1px solid var(--border); }
.c-card__title { font-size: var(--fs-md); }
.c-card--wide { grid-column: span 2; }        /* modifier */

/* ❌ YASAK — stil sızması (descendant seçici) */
.home .card span { color: red; }              /* ❌ specificity + kırılgan */

/* ✅ DOĞRU — tek bloğa in, var() kullan */
.c-card__title { color: var(--text-1); }      /* ✅ token (§3.11) */
```

**Modifier vs Element Karar Ağacı:** stil bir kez mi tekrar ediyor? → element `__` · kullanıcı durumu mu? → modifier `--` · cihaz davranışı mı? → `d-` ayrı dosya (08) · üçü de değilse utility `u-` (son çare).

### 3.14 §(ek) ITCSS Yükleme Sırası & Specificity Bütçesi (DOMAIN)

| Sıra | Katman | Specificity bütçesi | Ağırlık | Kural |
|---|---|---|---|---|
| 1 | 01 Abstracts | 0 (değişken/mixin) | en hafif | `:root` token |
| 2 | 02 Settings | 0 | hafif | yapılandırma |
| 3 | 03 Tools | 0–0-0-1 | hafif | mixin/fonksiyon |
| 4 | 04 Elements/Generic | 0-0-1 | hafif | reset/element |
| 5 | 04_Components (`c-*`) | 0-0-2 ideal | orta | block/element |
| 6 | 05_Pages (`p-*`) | 0-0-3 | ağır | layout |
| 7 | 06 Utilities | 0-0-1-0 (tek stil) | **son çare** | helper |
| 8 | 07 Vendors | ⚠️ elle denetle | — | vanilla yasağı (ADR-001) |
| 9 | 08 Devices (`d-*`) | 0-0-2 + media | davranış | override |

```text
Specificity bütçe denetimi (hedef: hiçbir kural 0-0-4+ değil):
  01 Abstracts  →  02 Settings  →  03 Tools
       →  04_Components (c-*)  →  05_Pages (p-*)
            →  06 Utilities (son çare)  →  08 Devices (d-*, davranış)
İhlal: 0-0-4 veya !important → revizyon (token'a taşı, §3.11)
```

### 3.15 §(ek) Frontend Performans Bütçesi (DOMAIN)

| Metrik | Bütçe | Ölçüm | Aşım Eylemi |
|---|---|---|---|
| LCP | ≤ 2.5 s | Lighthouse / PSI | hero görsel/öncelik |
| INP | ≤ 200 ms | field/PSI | JS parçalama |
| CLS | ≤ 0.1 | PSI | layout token (§3.11) |
| CSS (gzip) | ≤ 50 KB | `gzip -c app.css \| wc -c` | ITCSS temizliği |
| JS (gzip) | ≤ 70 KB (sayfa) | bundle ölçümü | Vanilla + defer |
| İstek (3. taraf) | 0 (vendor yasak) | harita | ADR-001 |
| Render-blocking | 0 | Coverage | kritik CSS |

```bash
# Bütçe denetimi (salt-okunur; hedef yollar glob'dan gelir)
gzip -c "assets.coremusic.net/Css copy/04_Components/"*.css | wc -c   # ≤ 50 KB
grep -Rc "!important" "assets.coremusic.net/Css copy/"                # hedef: 0
grep -RE "[0-9]+px" "assets.coremusic.net/Css copy/04_Components"     # token geçişi hedefi
```

---

### 3.16 §(ek) Responsive Token Kırılım Şablonu (DOMAIN)

| Token | Tier 1 (≤480px) | Tier 2 (481–768px) | Tier 3 (769–1280px) | Tier 4 (≥1281px) |
|---|---|---|---|---|
| `--font-size-body` | {{T1_BODY}} | {{T2_BODY}} | {{T3_BODY}} | {{T4_BODY}} |
| `--line-height-body` | {{T1_LEADING}} | {{T2_LEADING}} | {{T3_LEADING}} | {{T4_LEADING}} |
| `--space-section` | {{T1_SPACE}} | {{T2_SPACE}} | {{T3_SPACE}} | {{T4_SPACE}} |
| `--grid-columns` | 1 | 4 | 8 | 12 |
| `--container-max` | `100%` | `100%` | {{T3_MAX}} | {{T4_MAX}} |
| `--border-radius-md` | {{T1_R}} | {{T2_R}} | {{T3_R}} | {{T4_R}} |
| `--z-index-sticky` | {{Z_HEADER}} | {{Z_HEADER}} | {{Z_HEADER}} | {{Z_HEADER}} |

> Kural: Sayısal değerler yalnızca token kaynağında durur; bileşen içinde hardcoded `px` yasak (§4.1). Tier kırılımı yalnızca `@media` + token değişkeni ile yapılır. ⚠️ Eksik: sayısal token değerleri `.ai/ui-design/**` token dosyasından doldurulacak — bu şablon değer uydurmaz.

### 3.17 §(ek) WCAG 2.2 AA Ek Kriter Kontrol Listesi (DOMAIN)

| Kriter | Ad | Uygulama Kontrolü | Sahip |
|---|---|---|---|
| 1.4.10 | Reflow | 320 px genişlikte tek sütun; yatay kaydırma yok | UI Designer |
| 1.4.12 | Text Spacing | `1.5em` satır yüksekliği metni bozmuyor | UI Designer |
| 2.4.11 | Focus Not Obscured (Minimum) | Odak halkası sticky header / glass bar altında kaybolmuyor | UI Designer |
| 2.5.7 | Dragging Moves | Sürüklemenin tıklama/klavye alternatifi var (EQ düğmesi, slider) | UI Designer |
| 2.5.8 | Target Size (Minimum) | Tıklanabilir öğe en az 24×24 CSS piksel | UI Designer |
| 3.3.7 | Redundant Entry | Aynı bilgi formda ikinci kez istenmiyor (adres, kart) | Backend Architect |
| 3.3.8 | Accessible Authentication | Otomatik doldurma açık; bulmaca / zihin testi yok | Security Engineer |

> Kaynak: WCAG 2.2 AA (W3C Recommendation, 2023-10-05); 1.4.10/1.4.12 WCAG 2.1 AA koruma altındadır. Boşluk envanteri: `.ai/ui-design/04-accessibility-gaps.md` (glob ✅). §3.10 WCAG etkisi ile birlikte okunur.

### 3.18 §(ek) Mockup Gate & C01-C16 Uyum Tablosu (DOMAIN)

| # | Kapı (Gate) | Kaynak Dosya | İhlalde Aksiyon |
|---|---|---|---|
| 1 | Mockup okundu mu? | `.ai/ui-design/01-mockup-index.md` | DUR — kod yazılmaz |
| 2 | C01–C16 kodları eşleşiyor mu | `.ai/ui-design/02-component-inventory.md` | RED — inventara dön |
| 3 | 45-tier cihaz kontrolü | `.ai/ui-design/00-device-matrix.md` | RED — tier kuralı ihlali |
| 4 | Responsive fallback zorunlu | `.ai/ui-design/05-responsive-architecture.md` | RED — fallback eksik |
| 5 | Erişilebilirlik boşluğu kapanmış mı | `.ai/ui-design/04-accessibility-gaps.md` | gerekçeye yaz |
| 6 | ITCSS katman sırası korunmuş mu | `assets.coremusic.net/Css/**` | RED — §3.14 ihlali |
| 7 | BEM namespace sızıntısı | bileşen CSS taraması | RED — §3.13 ihlali |

> Okuma sırası: PNG > ASCII art > Inventory > Tokens > Reference (01–10) — `[[../../AGENTS.md]]` §7.2 (Pre-flight). Disk kanıtı: `assets.coremusic.net/Css/**` = 8 ITCSS katmanı.

### 3.19 §(ek) Frontend ADR Karar Noktaları (DOMAIN)

| # | Karar Sorusu | Seçenekler | Sabit Cevap |
|---|---|---|---|
| 1 | Çerçeve (framework) kullanılabilir mi? | evet / hayır | ❌ hayır — Vanilla JS + ITCSS (ADR-001) |
| 2 | Stil kaynağı | hardcoded px / token | ✅ token (§3.16) |
| 3 | Bileşen sınıf adı | serbest / BEM | ✅ BEM namespace (§3.13) |
| 4 | Etkilenen tier | tek / çok | çoksa §3.16 matrisi doldurulur |
| 5 | WCAG etkisi | var / yok | varsa §3.17 + §3.10 işaretlenir |
| 6 | Mockup referansı | okundu / okunmadı | okunmadıysa DUR (§3.18) |
| 7 | Performans bütçesi | aşıldı / aşılmadı | aşıldıysa ADR gerekçesine yaz (§3.15) |
| 8 | Router sözleşmesi | değişiyor / değişmiyor | değişiyorsa ADR-021 immutable contract kontrolü |

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|--------------|
| 1 | Dış iskelet (§1-§7) + domain §8-§11 silinemez; yalnız alan doldurulur | Şablon geçersiz |
| 2 | §2 Karar tek paragrafta Ne/Nerede/Nasıl-doğrulanır içermeli | Review'e dönmez |
| 3 | Mockup + inventory + token + ASCII art okunmadan §3.2 dolgusu yazılamaz (GR-11) | Kod revert + CRITICAL log |
| 4 | §9 WCAG sütunu tam ✅ olmadan Active olunamaz | Onay akışı durur |
| 5 | §10 token değişikliği inventory senkronu olmadan commit edilemez | Vault-kod tutarsızlığı |
| 6 | Ayrı HTML/branch, framework, JS cihaz-branch'i Öneri satırında ❌ ile geçemez | Karar reddedilir |
| 7 | Doğrulanamayan ölçüm/dosya `⚠️ VERIFICATION REQUIRED` | İçerik silinir |
| 8 | Frozen ADR (001-037) çelişkisi → yeni ADR-088+ açılır | Karar durur |

### 4.1 Yasaklı Örüntüler (CSS/JS)

```css
/* ❌ YASAK — !important (specificity kaçamağı) */
.c-card .title span { color: red !important; }   /* ❌ 0-0-3 + !important */

/* ✅ DOĞRU — modifier + token */
.c-card--danger .c-card__title { color: var(--danger-1); }

/* ❌ YASAK — hardcoded ölçü (§3.11 token zorunlu) */
.footer { height: 90px; }                        /* ❌ */
/* ✅ DOĞRU */
.footer { height: var(--footer-h); }             /* ✅ */

/* ❌ YASAK — inline stil (CSP ADR-012 riski) */
<div style="width:280px">                        /* ✗ nonce gerektirir */
```

```javascript
// ❌ YASAK — JS cihaz branch'i (brain.md §18A)
if (window.innerWidth <= 767) { el.classList.add('phone'); }   // ❌ layout JS'te

// ✅ DOĞRU — davranış hook'u (js- prefix) + CSS'e bırak
document.querySelector('.js-menu-toggle')        // ✅ yalnız davranış
  .addEventListener('click', onToggle);
// Layout kararı: CSS media query + var() (§3.14)
```

### 4.2 Guardrail ↔ Bölüm Eşlemesi Tablosu

| Guardrail | Şablondaki Karşılık | Denetim Yeri |
|---|---|---|
| #11 Mockup Before Frontend | §1 tablo "Zorunlu okuma" + §3.6 adım 1 | §6.5 |
| #16 Template Mandatory | §6 kontrol 1–4 | §6.1–6.4 |
| #17 Single Component Responsive | §3.3 karar metni ("HTML DEĞİŞMEZ") + §3.12 | §6.3 |
| ADR-001 Vanilla JS/ITCSS | §3.4 satır 5 + §4.1 vendor yasağı | §6.7 |
| ADR-005 Zero hallucination | §6.7 `⚠️` denetimi | §6.7 |
| Frozen 001–037 | §4 satır 8 → yeni ADR-088+ (⚠️ §1.3 numara çelişkisi) | Onay akışı |

### 4.3 Uçtan Uca Senaryo Tablosu (Domain Etkileşim Haritası)

| Senaryo | Tetikleyen § | Zorunlu Adımlar | Kanıt | Fail |
|---|---|---|---|---|
| Yeni component (`c-x`) | §3.13, §3.9 | inventory satırı → CSS → 45-tier test → WCAG | `02-component-inventory` +1 | CRITICAL revert |
| Token ekleme | §3.11, §3.14 | `a-layout-tokens.css` + 4 breakpoint + grep px | grep 0 hardcoded | 🟠 revizyon |
| Token silme | §3.11 tablo | grep çağrı=0 + Tech Lead onayı | grep kanıtı | 🔴 |
| Cihaz davranışı | §3.12, §3.9 | `08_Devices/d-*.css` yalnız davranış | tier testi | layout taşması |
| View mode (`v-x`) | §3.12 | `09_ViewModes/` + token | mockup karşılaştırma | 🟠 |
| WCAG düzeltmesi | §3.10 | kontrast/focus/token → QA imza | `04-accessibility-gaps` ✅ | AA bloke |
| CSP etkileyen JS | §3.13 js- | nonce/ADR-012 danış | Security onayı | 🔴 |
| Performans düşüşü | §3.15 | ITCSS/Utility denetimi | bütçe ölçümü | 🟠 |
| Mockup güncellendi | GR-11 | PNG yeniden oku → §3.2 revizyon | mockup ref | 🟠 |
| Framework önerisi | §3.4 satır 5 | **RED** (ADR-001 frozen) | — | karar reddi |

```bash
# Senaryo 2/3 kanıt zinciri (salt-okunur)
grep -RE "[0-9]+px" "assets.coremusic.net/Css copy/04_Components" && echo "FAIL: hardcoded px" || echo "PASS"
grep -Rc "!important" "assets.coremusic.net/Css copy/"          # hedef: 0 satır dışarıda
grep -c "^| Token" .ai/ui-design/02-component-inventory.md       # envanter senkronu
```

### 4.4 Şablon Kullanım Kontrolü (Guardrail #16 çıkış)

| # | Çıkış Kontrolü | Değer |
|---|---|---|
| 1 | frontmatter 7 alan dolu | ☐ |
| 2 | `{{DATE}}`/`{{AUTHOR}}` kalmadı (§3.8 hariç ⏳ satırlar) | ☐ |
| 3 | domain §8-§11 boş hücre yok | ☐ |
| 4 | `⚠️` gerektiren iddia işaretli | ☐ |
| 5 | `log.md` append + `index.md` senkron (parent) | ☐ |

---

## 5. Workflow

```text
ŞABLONU SEÇ (.templates/index §7.1.1) → KOPYALA → ADR-NNN-<slug>.md →
GR-11 OKUMA (01-mockup-index, 02-component-inventory, a-layout-tokens, ASCII art, 00-device-matrix) →
§1-§7 doldur (adr-template DRY) → §8-§11 domain doldur →
WCAG ☑ → TOKEN inventory ☑ → 45-Tier test ☑ →
GUARDRAIL #16 DOĞRULA (§6) → Onay (Steward→Tech→Arch) → COMMIT + wiki-link + log.md append
```

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | `.templates/index.md` §7.1.1'den şablon seç | Bu dosya |
| 2 | `ADR-NNN-<slug>.md` olarak kopyala | Yeni ADR |
| 3 | §3.2-§3.6 ana iskelet + §3.9-§3.11 domain doldur | Dolu ADR |
| 4 | §6 kontrol listesi 8/8 | ✅ |
| 5 | MO: cross-reference + `log.md` append | Audit |

### 5.1 Workflow Rolleri & Hata Durumları

| Adım | Rol | Beklenen Kanıt | Hata Durumu | Müdahale |
|---|---|---|---|---|
| 1 şablon seç | UI Designer | index §7.1.1 satırı | dosya yok → DUR | MO senkronu |
| 2 kopyala | UI Designer | `ADR-NNN-<slug>.md` | numara çelişkisi | §1.3 formu |
| 3 GR-11 okuma | UI Designer | mockup ✅ inventory ✅ | okunmadı → CRITICAL revert | QA doğrular |
| 4 §1-§7 doldur | UI Designer | her § ≥1 tablo/kod | boş hücre → revizyon | review |
| 5 §8-§11 domain | UI + QA | WCAG/token/tier ✅ | AA kırılımı → RED | §3.10 eşiği |
| 6 Guardrail #16 | MO | §6 8/8 | eksik → BLOCKED | tamamla |
| 7 Onay | Steward→Tech→Arch | §3.8 imza satırları | red → `Rejected` (§3.4) | gerekçe log |
| 8 Commit | MO | log.md append + wiki-link | kırık link → düzelt | cross-ref |

**Döngü kuralı:** 5. adımdan önceki her RED → 4. adıma döner; 7. adımdaki RED → dosya `Rejected` damgası alır (tekrar 4 değil).

---

## 6. Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | 7 alanlı frontmatter (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) | ☐ |
| 2 | H1 + §1-§7 (adr-template ile aynı başlık dizisi) | ☐ |
| 3 | Domain §8-§11 mevcut ve dolu | ☐ |
| 4 | Her § en az 1 tablo veya 1 kod bloğu içeriyor | ☐ |
| 5 | Mockup okundu (GR-11) — `01-mockup-index.md` referansı ADR'de | ☐ |
| 6 | WCAG ☑ + token inventory ☑ + 45-tier ☑ | ☐ |
| 7 | `⚠️ VERIFICATION REQUIRED` yok (veya işaretli) | ☐ |
| 8 | Onay akışı tablosu tamamlandı | ☐ |

**REFACTOR REPORT:** FILE: adr-frontend-template.md · PURPOSE: Frontend ADR şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + domain §8-§11 + glob kanıtları · RELATED: [[.templates/index]] · [[adr-template]] · [[../CLAUDE.md]]

### 6.1 Ek Doğrulama (glob/bütçe kanıtları)

| # | Kontrol | Komut (salt-okunur) | Beklenen |
|---|---|---|---|
| 9 | ITCSS kanıtı canlı | `glob('assets.coremusic.net/Css copy/**')` | 01–11 katmanları |
| 10 | Token dosyası var | `glob('**/a-layout-tokens.css')` | ≥1 dosya |
| 11 | `!important` sayısı | `grep -Rc '!important' 'Css copy/'` | hedef 0 |
| 12 | hardcoded px (bileşen) | `grep -RE '[0-9]+px' 'Css copy/04_Components'` | hedef 0 |
| 13 | WCAG kanıtı | `glob('.ai/ui-design/04-accessibility-gaps.md')` | dosya var |
| 14 | Cihaz matrisi | `glob('.ai/ui-design/00-device-matrix.md')` | dosya var |
| 15 | Envanter senkronu | `grep -c '^| C[0-9]' 02-component-inventory.md` | token satırıyla eş |
| 16 | Performans bütçesi | §3.15 gzip ölçümleri | ≤50 KB CSS |
| 17 | Frozen dokunulmadı | `git diff .ai/brain.md` | 0 diff |

```bash
# 11-12 kombinasyonu (tek seferde)
grep -Rc "!important" "assets.coremusic.net/Css copy/" | grep -v ":0" || echo "PASS: !important yok"
grep -RE "[0-9]+px" "assets.coremusic.net/Css copy/04_Components" || echo "PASS: hardcoded px yok"
```

---

## 7. Referanslar

| Kaynak | Kullanım |
|--------|----------|
| `[[adr-template]]` | Ana ADR iskeleti (DRY — §1-§7 birebir) |
| `[[.templates/index]]` | Registry (§7.1.1 ADR Templates satırı) |
| `[[../CLAUDE.md]]` | Guardrail #11, #16, #17; §12A UI Design |
| `[[../../AGENTS.md]]` | §6 routing (UI Designer), §7.2 pre-flight |
| `[[../../brain.md]]` | §18A/§18B/§18C responsive & device kuralları |
| `.ai/ui-design/01-mockup-index.md` · `02-component-inventory.md` · `00-device-matrix.md` · `04-accessibility-gaps.md` · `05-responsive-architecture.md` | Zorunlu okuma (glob ✅) |
| `assets.coremusic.net/Css copy/**` | ITCSS/BEM disk kanıtı (glob ✅) |

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
