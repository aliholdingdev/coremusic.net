---
reference_doc: "Kalıp C çekirdek — .ai/.templates/ui-design/prompt-template.md (Guardrail #16)"
title: "CoreMusic — Home Page 1024/1920 Whole-Page Rebuild Scenario Prompt"
type: prompt
category: ui-design
pattern: C
scope: "home-page (header/footer/player-info DIŞINDAKİ tüm component'lar)"
date: 2026-09-30
version: 1.0.0
status: active
assignee: "Senior Frontend Developer (UI Designer, A3 — K10-K11)"
authority: "Prompt — SSOT: .ai/.templates/ui-design/prompt-template.md · .ai/ui-design/01-mockup-index.md"
governance: Red Team · Human Mode · Truth Mode
---

# Home Page Whole-Page Rebuild — Senaryo Prompt (1024 / 1920)

> **UZMAN ATAMASI:** Senior Frontend Developer / UI Designer (A3). Orkestratör dispatch eder, kodu uzman yazar.
> **NE ZAMAN OKUNUR:** Ana Sayfa'nın header, footer (player bar) ve Player Info **dışındaki** tüm bileşenleri
> Figma mockup'ına göre yeniden yazılırken / düzeltilirken.
> **NOT:** Vault'a (`.ai/ui-design/prompt/`) taşınırsa bu dosya Kalıp C'ye katlanır: `## Senaryo` → `### Context`,
> dispatch bloğu → `### Prompt Template`, `## Otomatik Referanslar` → `### Required Inputs`. Kök kopyada senaryo
> sarmalayıcı olarak durur.

---

## Senaryo (Scenario)

**Sahne:** CoreMusic Ana Sayfa (`http://home.coremusic.net:81/home`) 1024×600 (embedded) ve 1920×1080 (desktop)
görüntülerinde Figma mockup'ına birebir oturmalı. Daha önce yapılan Player Info çalışması **tamamlandı ve
dokunulmaz** (S1-S7 düzeltmeleri korunur). Bu görev onun **dışındaki her şeyi** kapsar.

**Uzmanın rolü:** Mevcut sayfayı sıfırdan gözden geçir. **Hatalı olan yerleri sıfırdan yeniden yaz, doğru olan
yerleri olduğu gibi bırak.** Kopyala-yapıştır yok; SSOT'tan (PNG + Figma raw + token) okur, kısa düşünür, edit eder.

**Kapsam (SINIR — ihlal = görev reddi):**

| YAPILACAK | YAPILMAYACAK |
|-----------|--------------|
| Header, footer/player bar, `_player-info` **DIŞINDAKİ** home component'ları: now-playing kartı, welcome banner, widget grid, "En Son Dinlenen Şarkılar", "Son Oluşturulan & Sistem Tarafından Oluşturulan Playlistler", "Sıradaki Şarkılar", welcome popup (modal), sayfa arka planı | Header, footer, `_player-info.css`, `PlayerInfoComponent.js`, `player-info.php` dosyalarına dokunmak |
| Mevcut dosyaların MEVCUT satırlarında edit (CSS/PHP/JS) | **Yeni dosya oluşturmak** (yeni CSS, yeni PHP, yeni test sayfası, yeni test dosyası) |
| CSS yalnız `C:\www\coremusic.net\assets.coremusic.net\Css\` (SSOT) | Başka dizine CSS/JS taşımak veya kopyalamak |
| 1024 ve 1920'yi mockup'a birebir; 4K aynı token zincirinden türer | Uydurma ölç, tahmin token, `clamp()` varsayımı |
| Mevcut sayfada browser testi (Playwright) | Sadece kod okuyup "geçti" demek |
| Doğru çalışan mevcut değerleri koruma | Çalışan kodu gereksiz yeniden yazma |

**Kaçınılan tuzak (bilinçli):** "Bazı veriler 1024'te oktu ama ona göre yap" → 1024'te düzgün görünen bloklara
dokunma; bozuk/eksik/yanlış yerleşen blokları mockup'a göre sıfırdan yaz. Test sayfası **yazılmaz**, test
**mevcut `/home` sayfasında** yapılır.

**Mockup Gate (§13 / Guardrail #11):** Kod yazımdan ÖNCE ilgili PNG'ler okunur — okunamazsa **DUR** ve bildir.

---

## AI Code Generation Prompt

### Context
Ana Sayfa bileşen katmanı (ITCSS `05_Pages/_home*.css` + `04_Components/_*.css`) ve component PHP/JS parçaları
üzerinde mockup-eşitleme görevi. Vanilla JS ES6+ + ITCSS (ADR-001), BEM namespace zorunlu, `innerHTML` yasak,
CSP nedeniyle inline `style`/`script` yasak (ADR-012), token-first (ham hex/px yalnız `01_Abstracts` içinde).
Değişiklik yalnız mevcut dosyalarda edit ile yapılır.

### Required Inputs
- `mockup-1024`: `.ai/.png/home-1024/Linux  1024 - Home Page.png` — 1024 yerleşim SSOT *(zorunlu)*
- `mockup-1024-popup`: `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` — popup SSOT *(zorunlu)*
- `mockup-1920`: `.ai/.png/home-1920/Linux - 1920 - Home.png` — 1920 yerleşim SSOT *(zorunlu)*
- `figma-raw`: `.ai/ui-design/reference/figma/raw/*.json` — ham node ölçüleri (tahmin ölç yasak)
- `css-ssot`: `assets.coremusic.net/Css/` — ITCSS 01→09 katmanları; taşınmaz
- `php-page`: `home.coremusic.net/pages/home.php` + `pages/components/{welcome-banner,widget-grid,recent-tracks}.php`
- `js-components`: `assets.coremusic.net/js/components/composites/{WidgetArea,MiniCard,Card,Modal,Progress,Hero}Component.js`
- `tokens`: `assets.coremusic.net/Css/01_Abstracts/{a-widget-grid,a-welcome-banner,a-layout-tokens,a-layout-tokens-1024,a-design}-tokens*.css`
- `bypass-auth`: ADR-008 BypassAuthMiddleware + `.env` `TEST_MODE=true` — oturum engeli yok
- `tier`: T07 (1024×600 embedded) / T17 (1920×1080 desktop) *(zorunlu)*
- `viewport`: 1024×600 · 1920×1080 (4K = 3840×2160 regresyon kontrolü) *(zorunlu)*

**Otomatik referans toplama (önce çalıştır):**

```powershell
# 1) home ile ilgili TÜM mevcut dosyaları bul (yeni dosya yok — sadece bunlar edit edilir)
Get-ChildItem -Recurse assets.coremusic.net\Css -Filter *home*.css | % FullName
Get-ChildItem -Recurse assets.coremusic.net\Css\04_Components -Filter _*.css | % FullName
Get-ChildItem -Recurse home.coremusic.net\pages -Include *.php | % FullName
Get-ChildItem -Recurse assets.coremusic.net\js\components -Filter *Component.js | % FullName
# 2) mockup + envanter + şablon (Guardrail #11 / #16)
Get-ChildItem -Recurse .ai\.png\home-1024, .ai\.png\home-1920 -Filter *.png | % FullName
# 3) token adları (yanlış token yazma)
Select-String -Path assets.coremusic.net\Css\01_Abstracts\*.css -Pattern '^\s*--cm-[a-z0-9-]+:' | % Line | Sort-Object -Unique
```

### ASCII Reference
```
1024×600 (T07)                                1920×1080 (T17)
┌────────────────────────────────┐            ┌─────────────────────────────────────────┐
│ HEADER (nav)        [dokunulmaz]│            │ HEADER (nav)              [dokunulmaz]  │
├──────────────┬─────────────────┤            ├───────────────┬─────────────────────────┤
│ now-playing  │ widget grid     │            │ now-playing   │ welcome-banner (Hoş     │
│ kartı        │ (sağ kolon)     │            │ kartı (detaylı│ Geldin + istatistik +   │
│ (kapak+ileri │ Hoparlör/Hava/  │            │ yıldız/bitrate│ Keşfetmeye Başla)       │
│ leme)        │ Saat/Kısayollar │            ├───────────────┴─────────────────────────┤
├──────────────┴─────────────────┤            │ widget grid (20 slot — §13.9 kuralı)    │
│ En Son Dinlenen Şarkılar (2×4) │            ├─────────────────────────────────────────┤
│ Son Oluşturulan Playlistler    │            │ En Son Dinlenen Şarkılar (1 sıra ×10)   │
│ (2×4 + "Playlist listesini     │            │ Son Oluşturulan Playlistler (1 × 6)     │
│  görüntüle")                   │            │ [Sıradaki Şarkılar — mockup'ta YOK]     │
│ Sıradaki Şarkılar (sağ kolon)  │            ├─────────────────────────────────────────┤
├────────────────────────────────┤            │ FOOTER/PLAYER [dokunulmaz]              │
│ FOOTER/PLAYER   [dokunulmaz]   │            └─────────────────────────────────────────┘
└────────────────────────────────┘
Popup (1024, mockup 2): .modal--welcome  ~%70 w, backdrop blur+karanlık,
  bg: plaj görseli, logo + "Hoş geldin" + script alt-başlık + açıklama + [Başla] btn
  Show: route /home ilk girişte (localStorage cm_welcome_seen) · Esc/backdrop kapatma yok (yalnız Başla)
```

**Widget grid kanonik sayım (§13.9 — Figma API çıktısı önceliklidir, çelişirse bu kural kazanır):**
`1024` → satır1 **2×2** · satır2 **1×5** · satır3 **1×5** = **12 slot** ·
`1920` → satır1 **4×4** · satır2 **1×8** · satır3 **1×8** = **20 slot**.

### Prompt Template
```json
{
  "task": "Rebuild CoreMusic Home Page components (excluding header/footer/player-info) pixel-matched to Figma mockups at 1024 and 1920",
  "page": "01-home",
  "bem": ".home",
  "states": ["default", "hover", "focus-visible", "active", "loading"],
  "tokens": ["--cm-card", "--cm-btn", "--cm-widget-grid", "--cm-welcome-banner", "--cm-progress", "--color-primary"],
  "constraints": [
    "Vanilla JS ES6+ — framework yasak (ADR-001); ITCSS katmanları korunur",
    "BEM: block__element--modifier; innerHTML yasak — createElement + textContent",
    "CSP: inline style/script YASAK (ADR-012) — data-attr + JS setProperty",
    "CSS yalnız assets.coremusic.net/Css/; YENİ DOSYA YOK — mevcut satırlarda edit",
    "Header, footer, _player-info, PlayerInfoComponent.js, player-info.php DOKUNULMAZ",
    "Ölçü/token yalnız PNG + Figma raw + 01_Abstracts'tan; tahmin/uydurma yasak",
    "Kural ihlali: widget grid §13.9 (1024=12 slot, 1920=20 slot)"
  ]
}
```

### Expected Output
```html
<section class="home" data-component="home" data-viewport="1024">
  <article class="home__now-playing home__now-playing--embedded">
    <img class="home__cover" src="..." alt="Göksel — Sevil Neşelen">
    <div class="home__meta">
      <span class="home__title">Göksel - Sevil Neşelen</span>
      <div class="home__progress" data-progress="30" role="progressbar"
           aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </article>
  <aside class="home__widget-grid" data-slot-count="12"><!-- slot__item x N --></aside>
  <div class="home__popup home__popup--welcome" data-component="modal" hidden>
    <button class="home__popup-cta btn btn--primary" type="button">Başla</button>
  </div>
</section>
```

```css
.home { display: grid; gap: var(--cm-space-md); }
.home__now-playing { background: var(--cm-card-bg); border: 1px solid var(--cm-card-border);
  border-radius: var(--cm-radius-lg); }
.home__progress { width: var(--progress-percent, 0); transition: width .5s linear; }
.home__widget-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--cm-space-sm); }
.home__popup--welcome { position: fixed; inset: 0; background: var(--cm-overlay-bg);
  backdrop-filter: blur(var(--cm-overlay-blur)); }
@media (min-width: 1441px) { .home__widget-grid { grid-template-columns: repeat(8, 1fr); } }
```

### Validation
- [ ] 1024×600 screenshot → mockup ile karşılaştırma: now-playing, widget grid, 2 liste kolonu, Sıradaki Şarkılar yerleşimi eşleşir
- [ ] 1920×1080 screenshot → mockup ile karşılaştırma: welcome banner + tek satır listeler + 20 slot grid eşleşir
- [ ] Widget slot sayısı §13.9 ile birebir: 1024 → 12 · 1920 → 20
- [ ] Header/footer/player-info dosyalarında `git diff` ile DEĞİŞİKLİK YOK
- [ ] `git status` → yeni dosya yok (yalnız mevcut dosya edit'leri)
- [ ] CSS'te 01_Abstracts dışındaki ham hex/px yok; inline `style=`/`<script>` yok (CSP)
- [ ] Console 0 hata/uyarı; CSP ihlali yok (style-src/script-src violation)
- [ ] BEM formatı `block__element--modifier`; `innerHTML` kullanımı yok
- [ ] WCAG 2.2 AA: metin kontrastı ≥ 4.5:1; focus-visible outline `2px solid var(--color-primary)`
- [ ] Popup: `Başla` ile kapanır, kapanınca `localStorage` set edilir, ikinci girişte görünmez
- [ ] Regression: Player Info 392×131 / 469×184 / 750×300 değerleri bozulmadı

---

## Dispatch (orkestratör → uzman, birebir)

```text
SEVİYE: Senior Frontend Developer (UI Designer, A3). Kısa oku, kısa düşün, edit et. Her adımda kanıt üret.
ÖNCE: aaa.md → Senaryo + Required Inputs (otomatik toplama komutları) + 3 PNG oku. Okunamazsa DUR.

GÖREV: Ana Sayfa'yı (header/footer/player-info DIŞINDAKI her şey) 1024 ve 1920'de mockup'a birebir yap.
       Hatalı yerleri SIFIRDAN yaz, doğru yerleri BIRAK.

ADIMLAR:
1. [Keşif] Yukarıdaki otomatik toplama komutlarını çalıştır; mevcut home dosya envanterini çıkar.
   3 PNG'yi oku. Figma raw node ölçülerini çek (uydurma ölç yasak).
2. [Ayıklama] Her bloğu mockup ile karşılaştır: DOĞRU = dokunma · HATALI = listele (dosya:satır + ne yanlış).
   Kısa düşün, listeyi 10 dakikada bitir; sonra edit'e geç.
3. [Edit] Sadece mevcut dosyalarda düzelt. CSS: assets.coremusic.net/Css/ altında ITCSS katmanına uygun dosya.
   Yeni dosya YOK. Inline style/script YOK. Ham hex/px yalnız 01_Abstracts.
4. [Kural] Widget grid §13.9: 1024 → 12 slot · 1920 → 20 slot. Çelişki varsa kural PNG'ye precedanslıdır.
5. [Popup] 1024 welcome popup: mevcut c-modal.css/_welcome-banner.css üzerinden; Başla → localStorage.
6. [Test] Bypass auth aktif (ADR-008, TEST_MODE=true). Playwright MCP ile http://home.coremusic.net:81/home
   - viewport 1024×600 → screenshot + DOM ölçü
   - viewport 1920×1080 → screenshot + DOM ölçü
   - viewport 3840×2160 → regresyon (Player Info bozulmadı mı)
   - Console 0 hata; CSP violation YOK
   Cookie cm_viewport_w lag'ı var: resize ÖNCE navigate, gerekirse 2× yükle; cache takılıysa
   CDP Network.clearBrowserCache + ?v bump (cache-buster sadece JS mtime'ına bakar).
7. [Sonuç topla] 2 breakpoint screenshot'ı + düzeltme tablosu (dosya · satır · ne değişti · neden) +
   Validation checklist cevapları + git status (yeni dosya yok kanıtı). COMMIT ATMA — orkestratöre ait.
```

---

## Otomatik Referanslar (toplanan)

**Görsel SSOT:**
1. `.ai/.png/home-1024/Linux  1024 - Home Page.png` — 1024 ana yerleşim
2. `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` — popup katmanı
3. `.ai/.png/home-1920/Linux - 1920 - Home.png` — 1920 ana yerleşim

**Vault:**
4. `.ai/ui-design/01-mockup-index.md` · `02-component-inventory.md` (C01-C16 BEM/token) · `00-device-matrix.md`
5. `.ai/ui-design/05-responsive-architecture.md` (§7.4 4K ortalamama, §12 fallback)
6. `.ai/ui-design/reference/09-interaction-states.md` (hover vs touch)
7. `.ai/.templates/ui-design/prompt-template.md` — Kalıp C (Guardrail #16)
8. `.ai/AGENTS.md` §13.9 — widget grid kanonik kuralı (değiştirilemez)

**CSS/PHP/JS (mevcut — edit hedefi):**
9. `assets.coremusic.net/Css/05_Pages/_home{,-layout,-components,-inline}.css` · `_player.css`
10. `assets.coremusic.net/Css/04_Components/_{widget-grid,welcome-banner,player-info}.css` · `c-{modal,card,progress,home-song-btn}.css`
11. `assets.coremusic.net/Css/01_Abstracts/a-{widget-grid,welcome-banner,layout-tokens,layout-tokens-1024}-tokens*.css`
12. `assets.coremusic.net/Css/08_Devices/d-{embedded,desktop,laptop,tablet,4k}.css`
13. `home.coremusic.net/pages/home.php` · `pages/components/{welcome-banner,widget-grid,recent-tracks,player-info}.php`
14. `assets.coremusic.net/js/components/composites/{WidgetArea,MiniCard,Card,Modal,Progress,Hero}Component.js`

**Kararlar:**
15. ADR-001 framework yasak · ADR-008 bypass auth · ADR-012 CSP nonce/strict-dynamic
16. `assets.coremusic.net/AGENTS.md` §CSS SSOT (BEM + ITCSS + token-first + inline yasak)
17. `.ai/ui-design/prompt/component/aaaa.md` — Player Info promptu (kapsam dışı, referans)

**Harici (web search ile doğrula, kanıtsız iddia yazma):**
18. MDN — CSS Grid `grid-template-columns` / `repeat()` — https://developer.mozilla.org/en-US/docs/Web/CSS/repeat
19. MDN — `backdrop-filter` — https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter
20. web.dev — CSP `style-src` inline style engelleme — https://web.dev/articles/strict-csp

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-30
**Mode:** Red Team · Human Mode · Truth Mode
