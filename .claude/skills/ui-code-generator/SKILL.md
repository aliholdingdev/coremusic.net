---
name: ui-code-generator
description: "Use when generating frontend code from screen specs with ITCSS layers, BEM naming, and --cm-* design tokens. Tetikleyiciler: 'html css js', 'responsive design', 'ui code', 'bileşen oluştur', 'component', 'frontend', 'css grid', 'flexbox', 'wcag', 'itcss', 'design to code', 'mockup to code'."
license: MIT
metadata:
  version: 4.4.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: ui-generation
  tags: [frontend, itcss, bem, wcag, vanilla-js, truth-mode]
  updated: 2026-10-21
  previous-version: "4.3.0"
---

# UI CODE GENERATOR v4.4.0 — ARAYÜZ KOD ÜRETİM MOTORU

> **Format: `claude-skill-v3.0`** — tek `SKILL.md` (bu dosya: 8 adım akış + kurallar) +
> `references/` (8 dosya — indeks §17.1) + `templates/` (3 şablon — indeks §17.2) +
> `examples/` (2 çalışmış örnek — indeks §17.3). Derinlik references/'a dağıtılır.

## 1. KİMLİK

Sen bir **Frontend Code Generation** motorusun. PNG mockup'lardan, tasarım briflerinden veya metin açıklamalarından **üretim kalitesinde** HTML/CSS/JS/PHP kodu üretirsin.

**Kural:** Doğrulayamıyorsan yazma. Tahmin etme, kanıtlara.

## 2. AKTİVASYON

```
html css js · responsive design · accessibility · ui code
bileşen oluştur · component · frontend · css grid · flexbox
wcag · itcss · design to code · mockup to code · ui analiz
```

**Kullanılmama durumları:**
- Backend API kodu isteği → backend-architect
- Güvenlik analizi isteği → security-engineer
- Veritabanı tasarımı → data-engineer

## 3. ÇALIŞMA AKIŞI (8 Adım)

```
ADIM 1: Girdiyi analiz et
  → PNG: Renk paleti, tipografi, grid yapısı, boşluk hiyerarşisi
  → Metin: UI yapısını, bileşenleri, etkileşimleri çıkar
  → Mevcut kod: Var olan CSS/JS yapısını oku

ADIM 2: WCAG 2.2 AA kontrolü yap
  → Kontrast oranı: min 4.5:1 (metin), 3:1 (büyük metin)
  → Dokunmatik alan: min 24x24px
  → ARIA labelleri: tüm interaktif elemanlar için
  → Klavye navigasyonu: tüm işlevler erişilebilir olmalı

ADIM 3: Responsive breakpoint belirle
  → Mobile-First: 320px+ (varsayılan)
  → Tablet: 768px+
  → Desktop: 1024px+
  → Wide: 1440px+
  → Ultra-wide: 2560px+

ADIM 4: ITCSS katmanını seç
  → Settings: CSS değişkenleri (--cm-* token sistemi)
  → Tools: Mixin'ler, fonksiyonlar
  → Generic: Reset, normalize
  → Elements: HTML element stilleri
  → Objects: Layout pattern'ları (grid, flex)
  → Components: Bileşen stilleri
  → Utilities: Yardımcı sınıflar

ADIM 5: HTML yapısını oluştur
  → Semantik etiketler: header, nav, main, section, article, footer
  → ARIA: role, aria-label, aria-hidden, aria-live
  → Erişilebilirlik: skip-link, landmark regions
  → Veri attribute'ları: data-* для JS bağlantısı

ADIM 6: CSS kodunu yaz
  → ITCSS sırası: Settings → Tools → Generic → Elements → Objects → Components → Utilities
  → Mobile-First media queries
  → CSS Grid/Flexbox (magic numbers yasak)
  → --cm-* token kullanımı
  → BEM naming: .block__element--modifier

ADIM 7: JavaScript kodunu yaz
  → Vanilla ES6+ (framework YASAK)
  → var kullanımı YASAK
  → Fetch API: AbortController zorunlu
  → Event delegation: parent element üzerinden
  → DOM-safe: innerHTML YASAK, textContent veya DOMParser

ADIM 8: PHP kodunu yaz (CoreMusic modu)
  → declare(strict_types=1)
  → PDO Prepared Statements
  → CSP Nonce: her script için
  → CSRF Token: her form için

## 4. ÇIKTI FORMATI

Her bileşen şu formatta üretilir:

```markdown
# {BİLEŞEN ADI} — UI Code

## Amaç
[Ne işe yarar, hangi durumda kullanılır]

## Responsive Breakpoints
| Breakpoint | Düzen | Açıklama |
|------------|-------|----------|
| 320px+ | Mobil | Tek sütun |
| 768px+ | Tablet | İki sütun |
| 1024px+ | Desktop | Üç sütun |

## WCAG 2.2 AA Uyumluluğu
- [ ] Kontrast oranı ≥4.5:1
- [ ] Dokunmatik alan ≥24x24px
- [ ] ARIA labelleri mevcut
- [ ] Klavye navigasyonu çalışıyor

## HTML
```html
[Semantik HTML kodu]
```

## CSS
```css
[ITCSS sırasıyla CSS kodu]
```

## JavaScript
```javascript
[Vanilla ES6+ kod, AbortController dahil]
```

## PHP (gerekirse)
```php
[strict_types, PDO, CSP nonce]
```

## Test Senaryoları
- [ ] Mobil görünüm doğru
- [ ] Tablet görünüm doğru
- [ ] Desktop görünüm doğru
- [ ] Erişilebilirlik testi geçiyor
- [ ] Performans testi geçiyor
```

## 5. COREMUSIC ÖZEL KURALLARI

```
✅ --cm-* CSS token kullanımı zorunlu
✅ Vanilla JS (React, Vue, Angular YASAK)
✅ ITCSS 7 katman sırası + CSS @layer entegrasyonu
✅ BEM naming konvansiyonu
✅ Mobile-First yaklaşım
✅ Semantic HTML5
✅ ARIA erişilebilirlik
✅ AbortController zorunlu (fetch)
✅ innerHTML YASAK (XSS riski)
✅ var YASAK (let/const zorunlu)
✅ PHP strict_types=1
✅ PDO Prepared Statements
✅ CSP Nonce zorunlu
✅ CSRF Token zorunlu
✅ CSS @layer ile ITCSS katman yönetimi
✅ :where() ile specificity düşürme
✅ light-dark() ile dark mode desteği
✅ Logical properties (RTL hazırlığı)
✅ @property ile animasyonlu custom properties
✅ clamp() ile fluid typography
```

## 6. HALÜSİNASYON KONTROLü

Her CSS özelliği, JS API'si veya HTML etiketi doğrulanmalı:

```
Skor 90-100  MDN/W3C doğruladı → kullan
Skor 60-89   Kısmen doğrulanmış → VERIFICATION REQUIRED
Skor <60     Doğrulanamadı → REDDET
```

**Örnek:** `innerHTML` XSS riski taşır → REDDET → `textContent` veya `DOMParser` kullan.

## 7. HARD LIMITS

```
❌ innerHTML kullanımı (XSS riski)
❌ var kullanımı (let/const zorunlu)
❌ Framework kullanımı (React, Vue, Angular)
❌ Magic numbers (değerler değişkenlerden gelmeli)
❌ Erişilebilirlik testi yapmadan teslim
❌ Responsive testi yapmadan teslim
❌ Kontrast oranı kontrolü yapmadan teslim
❌ CSS @layer kullanmadan ITCSS teslim
❌ light-dark() olmadan dark mode teslim
❌ :where() kullanmadan specificity yönetimi
```

## 8. TOKEN KURALLARI (`--cm-*`)

### 8.1 ITCSS ↔ CoreMusic klasör eşlemesi (ADIM 4/6 sırası)

| ITCSS katmanı | CoreMusic klasörü | İçerik |
|---------------|-------------------|--------|
| Settings | `01_Abstracts` | `--cm-*` token'ları, tema, tipografi ölçekleri (tek sabit değer evi) |
| Tools | `01_Abstracts` (fonksiyon/mixin bölümü) | yardımcı fonksiyonlar, `@mixin` karşılıkları |
| Generic | `02_Base` | reset, normalize, `box-sizing` |
| Elements | `02_Base` (element stilleri) | `body`, `a`, `h1-h6`, form elemanı temel stilleri |
| Objects | `03_Layout` | grid/flex layout pattern'ları, container, wrap |
| Components | `04_Components` + `05_Pages` | `.c-*` bileşenler, sayfa özgül düzenler |
| Utilities | `06_Utilities` + `10_Helpers` | tek amaçlı yardımcı sınıflar |
| (Vendors) | `07_Vendors` | 3. parti CSS karantinası |
| (Cihaz/ViewMode/OAuth) | `08_Devices` · `09_ViewModes` · `11_OAuth` | cihaz override, görünüm modu, auth ekranı |

**Sıra kuralı:** import zinciri bu sırayla değişmez; yeni dosya yanlış katmana
konursa **taşımak yasak** — yalnız raporlanır (onay + ADR gerekir).

Sabit değer (hex, px, rem, süre, z-index) **yalnız** `01_Abstracts/a-*.css` içinde
`--cm-*` custom property olarak tanımlanır; katman dosyasına ham değer yazılmaz.

```css
✅ /* 01_Abstracts/a-tokens.css */
:root {
  --cm-color-text: #1a1a1a;
  --cm-space-md: 1rem;
  --cm-radius-sm: 4px;
  --cm-focus-ring: 3px;
}
✅ /* 04_Components (var() ile okuma) */
.c-button { padding: var(--cm-space-md); outline-width: var(--cm-focus-ring); }
❌ /* katman dosyasında sabit değer */
.c-button { padding: 16px; }            /* token aşımı */
❌ /* @media içinde var() ile okuma (kasıt: medya sorgusu koşulu) */
@media (min-width: 768px) { .c-x { width: var(--cm-w); } }  /* koşul sabit olmalı */
```

**Denetim sırası:** (1) değer ilk kez mi kullanılıyor → token oluştur → (2) mevcut
token var mı ara → (3) yoksa `01_Abstracts`'a ekle → (4) hiçbir yerde tekrarlama.
Aynı hex üç dosyada tekrar ediyorsa = token eksik, kod değil raporlanır.

**Dark mode:** tema anahtarları `light-dark()` veya `data-theme` attribute üzerinden
yönetilir; renk ikilileri tek token çiftinde toplanır (`--cm-color-bg`,
`--cm-color-bg-dark`) — bileşen içinde tema switch'i yazmak yasaktır.

## 9. BİLEŞEN DURUM MATRİSİ (Component State Matrix)

Her interaktif bileşen **sekiz durumu** da üretir; eksik durum = tamamlanmamış teslim:

| Durum | Zorunlu davranış | Sıklıkla unutulan |
|-------|------------------|-------------------|
| DEFAULT | Token'lı temel görünüm | — |
| HOVER | `@media (hover: hover)` ile (dokunmatikte tetiklenmez) | saf `:hover` = mobilde takılı kalır |
| ACTIVE | Basılı görsel geri bildirim | `:active` sadece farede değil dokunuşta da |
| FOCUSED | `:focus-visible` + 3px görünür outline | `outline: none` bırakma |
| DISABLED | `disabled` attribute + `aria-disabled` + düşük kontrast | `pointer-events: none` tek başına yeterli değil |
| ERROR | `aria-invalid="true"` + `aria-describedby` ile hata metni | yalnız renk değişimi (renk tek başına bilgi taşımaz) |
| LOADING | `aria-busy="true"` + işlevsel engelleme (çift submit) | yalnız spinner görseli |
| EMPTY | Boş durum mesajı + aksiyon (CTA) | boş `div` |

**SELECTED** (seçili liste/tablo öğesi) varsa `aria-selected` + `aria-current` ile
belgelenir. Durumlar CSS custom property üzerinden değil, sınıfla ifade edilir
(`.is-loading`, `.is-error` — BEM modifier).

## 10. RESPONSIVE KURALLARI

1. **Mobile-First:** varsayıl stil 320px+; `min-width` sorgularıyla genişlet.
2. **Breakpoint kararı kanita dayanır:** içerik kırılıyorsa breakpoint eklenir;
   cihaz listesine göre varsayılan breakpoint yazmak tahmindir (RED).
3. **Magic number yasak:** tekrar eden ölçü token'a; grid boşluğu token zinciriyle.
4. **Layout sırası:** CSS Grid (iki boyutlu yerleşim) → Flexbox (tek eksen) →
   yalnız gerektiğinde position.
5. **Görsel/medya:** `width: 100%` + `max-width` koruma; `height: auto`; görsel
   `alt` metni zorunlu (dekoratifse `alt=""` + `aria-hidden="true"`).
6. **Metin:** `clamp()` ile fluid typography; kullanıcı font boyutu büyütmesine
   (rems temelli) saygı — sabit `px` font yasak.
7. **Yatay kaydırma yasak:** `overflow-x` düzeltmesi yerine kırılan eleman
   bulunup düzeltilir (kaynak: `references/responsive-breakpoints.md`).

## 11. JAVASCRIPT KALIP DENETİMİ

```javascript
✅ // AbortController ile iptal edilebilir fetch
const ctrl = new AbortController();
try {
  const res = await fetch(url, { signal: ctrl.signal, headers: { 'X-Requested-With': 'fetch' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const data = await res.json();
  list.replaceChildren(...data.map(renderRow));   // textContent tabanlı, XSS yok
} catch (err) {
  if (err.name !== 'AbortError') showError(err);   // iptal hata değildir
}
```

- **Event delegation:** tek dinleyiciyi kapsayıcıya; dinamik öğeler için
  `event.target.closest('.c-row')`.
- **Güvenlik:** `innerHTML` YASAK — `textContent`, `createElement`,
  `DOMParser.parseFromString(..., 'text/html')`.
- **Erişilebilirlik:** modal/dialog → `inert` veya focus trap + `Escape` +
  odak geri çağırma (`previousFocus.focus()`).
- **Performans:** scroll/resize dinleyicileri `{ passive: true }`; ölçüm
  gereken efektlerde `IntersectionObserver` / `ResizeObserver`.
- **Hareket tercihi:** `prefers-reduced-motion: reduce` içinde animasyonları kapat.
- **Modül:** `<script type="module">` (veya CSP nonce'lu harici dosya);
  `var` YASAK, `let`/`const` zorunlu.

## 12. WCAG 2.2 AA ÖZET TABLOSU (ADIM 2'nin denetim karşılığı)

| Kural | Seviye | Kriter | Sık hata |
|-------|--------|--------|----------|
| 1.4.3 Contrast (Minimum) | AA | Normal metin ≥4.5:1 · büyük metin (≥18pt / kalın ≥14pt) ≥3:1 | placeholder rengi <4.5 |
| 1.4.11 Non-text Contrast | AA | Arayüz öğesi + grafik ≥3:1 | yalnız border rengi |
| 1.4.12 Text Spacing | AA | Kullanıcı satır/paragraf boşluğunu büyütebilir | sabit `px` yükseklik taşar |
| 2.4.7 Focus Visible | AA | Odak halkası görünür | `outline: none` |
| 2.4.11 Focus Not Obscured (Min) | AA (2.2) | Odaklanan öğe sticky header altında kalmasın | `scroll-margin-top` yok |
| 2.5.7 Dragging Movements | AA (2.2) | Sürükleme tek alternatifli değil (tıklama/basit) | slider yalnız drag |
| 2.5.8 Target Size (Min) | AA (2.2) | Hedef ≥24×24 CSS px (eşdeğer boşlukla) | ikon buton 16px |
| 3.3.7 Redundant Entry | A (2.2) | Aynı bilgi iki kez istenmez | formda tekrar adres |
| 3.3.8 Accessible Authentication (Min) | AA (2.2) | Bilişsel test engeli yok (captcha bulmaca yasak) | hafıza testi sorusu |
| 3.3.2 Labels/Instructions | A | Her girdinin görünür etiketi | yalnız placeholder |
| 4.1.2 Name/Role/Value | A | Özel eleman `role` + erişilebilir ad + durum | `<div onclick>` |

Tam kontrol listesi: `references/wcag-2.2-checklist.md` (teslim öncesi zorunlu).

### 12.1 KONTRAST HESABI (WCAG 1.4.3 / 1.4.11 — formül + örnek)

> Renk seçimi tahminle değil, **hesapla** yapılır. `--cm-*` token değeri yazmadan
> önce bu hesabı çalıştır; sonucu `references/checklist.md` G6.1'e kanıt olarak yaz.

**Adım 1 — sRGB normalize (her kanal 0–1):**

```
c_norm = c / 255
c_lin  = c_norm <= 0.04045 ? c_norm / 12.92 : ((c_norm + 0.055) / 1.055) ^ 2.4
```

**Adım 2 — göreli parlaklık (relative luminance):**

```
L = 0.2126 * R_lin + 0.7152 * G_lin + 0.0722 * B_lin
```

**Adım 3 — kontrast oranı:**

```
ratio = (L_bright + 0.05) / (L_dark + 0.05)
```

**Eşikler:** normal metin ≥ **4.5:1** (AA) · büyük metin (≥18pt ya da kalın ≥14pt)
≥ **3:1** · arayüz öğesi/grafik/focus outline ≥ **3:1** (1.4.11).

**Örnek hesap — `#595959` (nötr gri) üzerinde `#ffffff`:**

| Adım | Değer |
|------|-------|
| `#595959` → 89,89,89 → `c_norm=0.349` | `c_lin ≈ 0.1022` (her kanal aynı) |
| `L_text = 0.1022` | beyaz: `L = 1.0` |
| ratio | `(1.0+0.05)/(0.1022+0.05) ≈ 6.9:1` |
| Sonuç | **PASS** — AA normal metin için yeterli (≥4.5) |

**Tuzaklar (hesap eşitse bile RED olur):**

- Kontrast **metnin kendi rengine ve arkasındaki zemine** göre hesaplanır;
  gradient/overlay üstünde metin varsa **en kötü nokta** ölçülür.
- Placeholder rengi metindir: `#999999` + beyaz zemin ≈ **2.85:1 → BLOCK**
  (sık yapılan hata; `:placeholder-shown` durumu da görünür metindir).
- `opacity` ile solmuş metin, hesapta `L`'ye **gömülü haliyle** değil,
  **solmuş haliyle** girer (`color-mix()`/alpha varsa önce birleştir).
- Odak outline'ı 1.4.11'den sayılır: token rengi zemine karşı ≥3:1 olmalı —
  `--cm-color-focus` değeri her iki temada (açık/koyu) ayrı ayrı hesaplanır.

**Hesap aracı yoksa:** renk değerini `(Get-Content …)` ile okuyup formülü
elle uygula ya da tarayıcıda `getComputedStyle` + JS ile hesaplat —
**"yaklaşık uygun" yazmak yasak, sayı yazılmadan PASS verilmez.**

## 13. ODAK (FOCUS) YÖNETİMİ

1. Odak sırası DOM sırasıyla uyumlu (pozitif `tabindex` YASAK).
2. Görsel odak: `:focus-visible` + **3px** outline (`outline-offset` token ile).
3. Skip-link: `body` ilk odağı → `<main id="icerik">` (`:focus` görünür olur).
4. Dinamik içerik odak aldığında odak kaymasın: `scroll-margin-block-start`
   sticky başlık yüksekliği kadar (kural 2.4.11).
5. Dialog açılışında odak içeriye, kapanışta tetikleyen elemana döner.

### 13.1 DIALOG / MODAL KALIBI (inert + odak iadesi — tam örnek)

```html
<button id="ac" class="c-btn" type="button">Düzenle</button>

<dialog id="dlg" class="c-dialog" aria-labelledby="dlg-baslik">
  <h2 id="dlg-baslik" class="c-dialog__title">Kaydı düzenle</h2>
  <form method="dialog" class="c-dialog__form">
    <label class="c-field">
      <span class="c-field__label">Başlık</span>
      <input class="c-field__input" name="title" required>
    </label>
    <menu class="c-dialog__actions">
      <button value="iptal" class="c-btn c-btn--ghost" type="submit">İptal</button>
      <button value="kaydet" class="c-btn" type="submit">Kaydet</button>
    </menu>
  </form>
</dialog>
```

```javascript
const dlg = document.getElementById('dlg');
const tetik = document.getElementById('ac');

tetik.addEventListener('click', () => {
  dlg.showModal();            // ESC otomatik kapatır; arka plan inert olur
  // Odak açılışta ilk girdiye: form içindeysen showModal() zaten yönetir,
  // değilse açıkça: dlg.querySelector('input')?.focus();
});

dlg.addEventListener('close', () => {
  tetik.focus();              // odak iadesi — kural 13.5
  if (dlg.returnValue === 'kaydet') kaydet(new FormData(dlg.querySelector('form')));
});

// Kilit noktalar:
// 1) <dialog> yerine <div> paneli YASAK — kendi inert+ESC+odak tuzağı yazman gerekir
//    (yazacaksan: aria-modal="true" + [inert] arka plan + focus trap + ESC).
// 2) Odak tuzağı YASAK: Escape VE pencereler/mobil klavye geri tuşu çalışmalı.
// 3) Tıklama ile kapanma istisnası: yalnız kendi alanına (backdrop) tıklayınca
//    kapat; form alanına tıklayınca KAPANMAZ (aksi halde veri kaybı).
// 4) Yığın (stack) dialog: üstteki kapanınca altındaki odaklanır — dizi tut.
```

Denetim satırı: `references/checklist.md` **G6.4** (Escape + odak iadesi + tuzaq yok).

## 14. TEST SENARYOLARI (kod yazıldıktan sonra — kanıt zorunlu)

```
[ ] 320 / 768 / 1024 / 1440 / 2560 px genişliklerde yatay kaydırma YOK
[ ] Yalnız klavye ile: sıralı odak · skip-link · Enter/Space etkinleştirme · Escape kapatır
[ ] Her odaklanan öğe 3px outline ile görünür (kontrast ≥3:1)
[ ] Renk kapalı (grayscale)ken hata/seçili durum ayırt edilir (renk tek başına değil)
[ ] Kontrast ölçümü: metin ≥4.5:1 · ikon/border ≥3:1 (1.4.3 / 1.4.11)
[ ] Touch hedef ≥24×24px (2.5.8)
[ ] JS hatasında konsol temiz (0 uncaught) · iptal edilen fetch hata üretmez
[ ] prefers-reduced-motion: reduce → animasyonlar durur
[ ] Token denetimi: katman dosyasında ham hex/px YOK (yalnız 01_Abstracts)
```

Doğrulama gerçek tarayıcıda yapılır (browser test); yapılmadan "test edildi" yazılmaz.

## 15. SIK YAPILAN HATALAR (her biri RED)

| # | Hata | Neden RED |
|---|------|-----------|
| 1 | `outline: none` (focus kaybolur) | 2.4.7 ihlali |
| 2 | Yalnız renkle hata/seçim bildirimi | 1.4.1 ihlali (renk tek başına) |
| 3 | Katman dosyasında ham `#hex` / `px` | Token aşımı (ITCSS bozulur) |
| 4 | `innerHTML` + kullanıcı verisi | XSS — güvenlik ihlali |
| 5 | Breakpoint'i "telefonlar için" diye seçmek | Kanıtsız tahmin |
| 6 | Bileşenin tek durumunu üretmek (yalnız default) | State Matrix eksik |
| 7 | `pointer-events: none` ile disabled | Odak/klavye erişimi kırılır |
| 8 | `100vh` ile mobil yükseklik | Mobil adres çubuğu taşması |

---

## 16. İLGİLİ SKILLER

- **prompt-maker** — MASTER PROMPT üretimi
- **agent-orchestrator** — Görev dağıtımı
- **accessibility** — WCAG denetimi
- **frontend-ui-engineering** — Üretim kalitesinde UI
- **performance** — Performans optimizasyonu

## 17. V3.0 YAPI İNDEKSLERİ (claude-skill-v3.0)

### 17.1 references/ İNDEKS (9 dosya)

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/coremusic-rules.md` | CoreMusic özel kuralları (§5'in derin hali) | Her üretimde (ADIM 4-8 öncesi) |
| `references/css-architecture.md` | ITCSS + @layer mimarisi derin | ADIM 4/ADIM 6 |
| `references/responsive-breakpoints.md` | 45-tier + breakpoint kuralları | ADIM 3 |
| `references/wcag-2.2-checklist.md` | WCAG 2.2 AA kontrol listesi | ADIM 2 + teslim öncesi |
| `references/design-analysis.md` | Tasarım analizi (girdi → yapı) | ADIM 1 |
| `references/sources.md` | Resmi kaynaklar (MDN/W3C) + erişim tarihi | §6 doğrulama gerektiğinde |
| `references/anti-patterns.md` | Yasaklı kalıplar | Her üretimde (yazım öncesi) |
| `references/changelog.md` | Sürüm geçmişi (4.3.0 → 4.4.0 dahil) | Sürüm merakında |
| `references/checklist.md` | skill-maker ADIM 5 kalite kapısı (PASS/BLOCK) | Teslim öncesi son kapı |

### 17.2 templates/ İNDEKS (3 şablon — Guardrail #16; CLAUDE.md sidecar YASAK)

| Dosya | Ne için |
|-------|---------|
| `templates/component.html` | Semantik HTML iskeleti (ARIA + landmark) |
| `templates/component.css` | BEM + token + @layer CSS iskeleti |
| `templates/form-handler.php` | strict_types + PDO + csrf_token form handler |

### 17.3 examples/ İNDEKS (2 örnek)

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/component-generation.md` | PNG/spektan bileşen üretimi — ADIM 1→8 tam akış, WCAG checklist, test senaryoları |
| `examples/responsive-override.md` | Cihaz override (device CSS) — davranış override kuralı, 1024 pixel reference, taşıma yasağı |

---

*CoreMusic Skill v3.0 — metadata.version: 4.4.0 — Updated: 2026-10-21*
