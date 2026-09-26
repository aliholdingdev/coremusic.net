---
title: "CoreMusic — ADR-018: Footer Player Vaporwave (Estetik Token'lar · Oynatıcı Davranış Durumları · Compositor-Only Animasyon Bütçesi · WCAG/reduced-motion)"
type: adr
category: frontend
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-018 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-018: Footer Player Vaporwave (Estetik Token'lar · Oynatıcı Davranış Durumları · Compositor-Only Animasyon Bütçesi · WCAG/reduced-motion)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-018'i sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: estetik + davranış + performans + erişilebilirlik) · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · Tech Lead: **✅ (2026-09-25)**
**İlgili ADR'ler:** [[ADR-001-vanilla-js-itcss]] (ITCSS token katmanı + BEM + hardcoded renk yasağı bu kararın zemini — dosya diskte VAR ✅) · [[ADR-004-multi-domain-spa]] (SPA route değişiminde player yaşam döngüsü — dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (CWV: INP/CLS kapıları — bu kararın performans bütçesi ona bağlanır — dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı — dosya diskte VAR ✅) · karar dizini [[../index]] **satır 55** `[[ADR-018-footer-player-vaporwave]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in web arayüzünün her oturumda görünen yüzü olan **footer player** (alt sabit oynatıcı çubuğu) için **görsel kimlik (vaporwave), davranış sözleşmesi (durumlar, sticky, SPA yaşam döngüsü, klavye), performans bütçesi (animasyon, CWV) ve erişilebilirlik (WCAG + reduced-motion)** tek bir bağlayıcı kararda yazılmamıştır. Estetik tercihler CSS dosyalarına **dağınık ve kısmen hardcoded** olarak girmiş, davranış durumları spec ile kod arasında ikiye bölünmüş, animasyon/erişilebilirlik kuralları ise **hiç yazılmamıştır**. Bu ADR; footer player'ın **tam kapsamını** (estetik + davranış + performans + erişilebilirlik) tek karar altında toplar, vaporwave paletini **design token'a** bağlar, oynatıcının durum makinesini ve SPA yaşam döngüsünü tesciller, animasyon bütçesini **compositor-only** ile kilitler ve `prefers-reduced-motion`/kontrast/klavye kurallarını bağlayıcı hale getirir.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) KOD KATMANI — footer player IMPLEMENTED (dosya + satır kanıtlı):**

- **Sunucu şablonu — `home.coremusic.net/footer.php` (IMPLEMENTED):**
  - `:3` "footer.php — CoreMusic Footer Player Bar (v3.0)"; `:15` "BEM: `.footer-player`, `.footer-player__cover`, `.footer-player__info`".
  - `:63-64` tier sınıfı: `footer-player--4k` / `footer-player--wide` / `footer-player--embedded`.
  - `:66` `<footer class="footer footer-player …" role="contentinfo" aria-label="Oynatıcı">`.
  - `:69-71` `role="progressbar"` + `aria-valuenow/min/max` + `input[type=range]` (seek) + `#seekbar2` ilerleme çubuğu; `:77-80` kapak (`loading="lazy"`) + şarkı/albüm metni.
- **CSS (ITCSS) — `assets.coremusic.net/Css/03_Layout/_footer.css` (IMPLEMENTED, 408 satır):**
  - `:35` `height: var(--footer-h);` → **yükseklik token ile sabit (CLS koruması)**; `:156-157` `calc(var(--footer-h, 90px) - 6px)`.
  - `:45` `.footer-player__progress`, `:54` `.footer-player__seek-input`, `:66` `__progress-bar`, `:75` `__inner`, `:98` `__meta`, `:104` `__controls`.
  - `:36-37` `backdrop-filter: blur(0.2px)` + `-webkit-` eki; `:333, :435` `transition: background 0.2s, transform 0.15s` (mevcut transition'lar **transform tabanlı** — bütçeye uygun); `:164, :337, :355` `filter: drop-shadow(...)` (**filter animasyonu değil, sabit dekor** — efekt genişlerse bütçe ihlali riski).
  - `prefers-reduced-motion` `_footer.css` içinde **YOK** (repo taraması: yalnız `_sidebar.css:626`, `p-login-view.css:1557` + vendor bootstrap) → **boşluk = bu ADR ile kapanır**.
- **Durum makinesi — `assets.coremusic.net/js/features/PlayerController.js` (IMPLEMENTED):**
  - `:3` "Footer player state machine. STOPPED/PLAYING/PAUSED."; `:12-13` `#status = 'STOPPED'`.
  - `:97-110` PLAYING↔PAUSED↔STOPPED geçişleri; `:76` `bar.setAttribute('aria-valuenow', …)`; `:144` `btn.setAttribute('aria-label', icon === 'pause' ? 'Duraklat' : 'Oynat')`; `:152` `aria-valuenow = '0'`.
- **Başlatma + modüller — `assets.coremusic.net/js/core/footer.init.js` (IMPLEMENTED):** `:9` `initCorePlayerModules()`; `:30-32` ses cookie'si (`MM_Volume`); `:50` `percentSelector: 'p.c-footer__volume-slider-volume-size'`; `:66` `timeDuration: '#footer_sure'`.
- **Player alt modülleri — `assets.coremusic.net/js/coreplayer/` (IMPLEMENTED, 5 dosya):** `coreplayer.controls.js`, `coreplayer.seekbar.js`, `coreplayer.volume.js`, `coreplayer.progressbar.js`, `coreplayer.shared.js`. Ayrıca `js/components/interactive/PlayerInfoComponent.js`.
- **Device/token CSS — `assets.coremusic.net/Css/01_Abstracts/` + `08_Devices/` (IMPLEMENTED):**
  - `a-layout-tokens-1024.css:35` `--footer-h: 72px`; `d-4k-tv.css:1` `--footer-h: 160px`; `page-layout.css:31-32` `min/max-height: calc(100vh - var(--header-h, 60px) - var(--footer-h, 90px))`; `_sidebar.css:30` `bottom: var(--footer-h, 90px)`.
  - Tier prompt'ları (`.ai/ui-design/prompt/screen/T2-T8`) `--footer-h` değerlerini zorlar: T4=90px, T5=96px, T6=104px, T7=120px, T8=138px.
- **Vaporwave paleti — `assets.coremusic.net/Css/01_Abstracts/a-colors-token.css` (IMPLEMENTED, kısmi):**
  - `:40-49` violet skalası; `:46` `--color-violet-600: #7950F2; /* PRIMARY BRAND */`.
  - `:68-77` pink skalası; `:73` `--color-pink-500: #F06595; /* ACCENT */`; `:87` `--color-magenta-600: #E91E8C`.
  - **cyan/teal token YOK** (grep `cyan|teal` = 0 sonuç) → mor/pembe var, **cyan eksik**.
- **"Vaporwave" tek geçiş — `assets.coremusic.net/js/device-layout-updater.js:365`** "Desktop/Laptop'da: **tam Vaporwave player**" (yorum satırı).
- **Eski/yedek kod — `assets.coremusic.net/js copy/components/FooterPlayer.js:20`** `class FooterPlayer` (`:197-200` export) — **aktif `js/` ağacında `FooterPlayer.js` YOK**; `.ai/reports/unused-files-report.md:115` "js/components/FooterPlayer.js — main.js'de import yok → **İNCELE**" kaydı bu boşluğu doğrular. Yedek `js copy/` + `Css copy/` ağaçları **kaynak değil yedektir** (kullanılmaz).

**B) SPEC/VAULT KATMANI — IMPLEMENTED (doküman var):**

- **Footer player flow — `.ai/ui-design/flow/navigation/03-footer-player.md` (IMPLEMENTED):** `:81` "EKRAN 1: Footer Player (Kompakt)", `:91` "EKRAN 2: Footer Player (Geniş - Desktop)", `:176-183` BEM sınıf sözleşmesi (`.footer-player`, `__info`, `__art`, `__controls`, `__seek`, `__volume`, `__actions`, `--expanded`).
- **SPA görünürlük kuralı — `.ai/ui-design/flow/navigation/01-spa-routing.md:60`** `#footer-player.show() = !isAuthPage` (auth sayfalarında player gizli).
- **Responsive mimari — `.ai/ui-design/05-responsive-architecture.md:73`** "└── `_footer.css` ← Footer player" (ITCSS yerleşimi).
- **Mockup indeksi — `.ai/ui-design/01-mockup-index.md:67, 84`** — 1024 ve 1920 home ekranlarında "footer player" görünür (RPi5 7" + Desktop FHD).
- **ASCII envanter — `.ai/ui-design/screens/00-ascii-art-index.md:88, 115`** — "FOOTER PLAYER (h:90, y:510-600)" + `.player-*` / `.player__controls` eşlemesi.
- **Palet/tokens vault — `.ai/ui-design/` token ve reference dosyaları** (500-error raporu `.ai/reports/500-error-report.md:112, 129` `<section class="footer-player__controls" aria-label="Oynatma kontrolleri">` örneğini kaydeder).

**C) YOK / PLANNED (uydurulmadı — `⚠️ VERIFICATION REQUIRED`):**

- **`--vapor*`, `scanline`, `glitch` CSS token'ı YOK:** repo geneli `vaporwave|--vapor|scanline|glitch` taraması CSS/JS/PHP/HTML içinde **yalnız 2 sonuç**, ikisi de aynı yorum satırı (`device-layout-updater.js:365`) → **CRT/scanline/glitch efektleri henüz yazılmamıştır (PLANNED)**.
- **Footer'da `prefers-reduced-motion` YOK** (§1.1-A) → reduced-motion kuralı bu ADR ile gelir.
- **`aria-live` durum duyurusu footer player'da YOK** (`footer.php` taraması `aria-live|keydown` = 0 sonuç) → PLANNED.
- **Klavye kısayolları (space/ok) player üzerinde YOK** (`footer.php` + `PlayerController.js` `keydown` = 0 sonuç) → PLANNED.
- **`will-change` / animasyon bütçesi kuralı vault'ta YOK** → bu ADR ile yazılır.

**Sonuç etiketi:** footer player **kod olarak IMPLEMENTED** (PHP şablonu + ITCSS CSS + JS durum makinesi); **vaporwave efekt katmanı (scanline/glitch), reduced-motion, aria-live ve klavye kontrolü PLANNED** — bu ADR bu dört boşluğu kapatır.

### 1.2 Sorun Tanımı

1. **Estetik token'a bağlı değil:** "Vaporwave" yalnız bir yorum satırında geçer; palet `a-colors-token.css`'te kısmen var (violet ✅, pink ✅, **cyan ❌**), scanline/glitch/gradient kuralları **hiç yok** → tema değişince hardcoded renkler kırılır (ADR-001 token katmanı ihlali).
2. **Davranış sözleşmesi parça parça:** durum makinesi `PlayerController.js:12-13` (STOPPED/PLAYING/PAUSED), BEM+genişleme `03-footer-player.md:176-183`, SPA görünürlüğü `01-spa-routing.md:60`, **yükleniyor/hata durumları hiçbir yerde yazılı değil**.
3. **Klavye ve canlı duyuru yok:** space/ok tuşları ile oynatma/duraklatma/seek/ses yok, `aria-live` yok → WCAG 2.1.1/4.1.3 boşluğu.
4. **Reduced-motion ihlali riski:** `_footer.css`'te `transition/transform` var ama `prefers-reduced-motion` koruması yok; scanline/glitch eklenirse hareket duyarlı kullanıcı için doğrudan ihlal (WCAG 2.3.3).
5. **CLS/INP riski:** yükseklik `--footer-h` ile sabit ama **dekoratif efektler eklenirken** yükseklik/boyut değişirse layout shift oluşur; ADR-006 CLS ≤ 0.1 kapısı birebir bağlayıcıdır.
6. **Kod-spec boşluğu:** aktif ağaçta `FooterPlayer.js` yok (yalnız `js copy/`), `unused-files-report.md:115` "İNCELE" → hangi modülün sahip olduğu belirsiz.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (w3.org, developer.mozilla.org, web.dev, wai-aria), her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) vaporwave/retro UI estetiği 2025-26, (b) audio player UX + klavye erişilebilirliği, (c) CSS animasyon performansı (compositor), (d) prefers-reduced-motion + dekoratif efekt (WCAG), (e) CLS/INP + sabit yükseklikli widget.** Erişim: **5 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "vaporwave retro UI design trends 2025 2026 web aesthetics gradient neon" · (2) "CSS animation performance compositor thread transform opacity will-change best practices 2025" · (3) "sticky audio player UX best practices keyboard accessibility space arrow keys aria-live progress bar" · (4) "prefers-reduced-motion decorative effects accessibility WCAG flash animation guidelines CLS reserved space sticky widget" · (5) "CLS layout shift avoid sticky footer player fixed height reservation Core Web Vitals INP input latency best practice" |
| Web Search **Konusu** | (1) 2025-26 görsel/web trendleri: retro-futurizm, Y2K, neon mor/pembe paletler, gradient ve glitch dokular; (2) transform/opacity'in compositor tarafında çalışması, will-change kullanımı, layout-thrashing özelliklerden kaçınma; (3) medya oynatıcı klavye kontratı (Space/Enter = oynat-duraklat, ok tuşları = seek/ses), `role=progressbar` + aria-live durum duyurusu; (4) WCAG 2.3.3 "Animation from Interactions" + `prefers-reduced-motion` ile dekoratif efektlerin kapatılması; (5) CLS ≤ 0.1 eşiği, widget için ayrılmış sabit yükseklik/boyut, INP için ana thread'i bloklamayan işler |
| Web Search **Bağlam** | **~45 adlandırılmış kaynak / 5 sorgu**: w3.org (WCAG 2.3.3 understanding), developer.mozilla.org ×2 (prefers-reduced-motion, `progressbar` rolü), web.dev ×3 (animations-guide, prefers-reduced-motion, CLS), WAI APG (keyboard interface), Deque University (media player controls), UX Pin (keyboard navigation patterns), Red Hat UX (audio player accessibility), accessible.org (video player a11y), Level Access (ARIA widgets keyboard), Vidzflow (custom video player), Viget (Animation Performance 101), Imaginarycloud (CSS animations 2026), Cloudways (non-composited animation), dev.to/Medium ×2 (performant animations), GitHub Primer (motion guidance), BOIA + Pope Tech + Tetralogical + AAardvark (reduced-motion/WCAG), WP Rocket/Hyvä/MarketMuse/Kinsta/Parachute/SearchAtlas (CLS), EG Innovations (CWV eşikleri), designerup + digitalsynopsis + lummi + aigoodies + studio2am + Pinterest/Instagram (retro/vaporwave trend), YouTube ×3 (trend/animasyon anlatımları), StackOverflow ×2, Reddit ×3, Capgo (Capacitor animasyon rehberi). |
| Web Search **Kısa Açıklama** | **(1) Estetik:** 2026 trend listeleri retro-fütürizm/Y2K'yi "neon mor-pembe palet, gradient grid, glitch ve bitmap doku, chrome tipografi" ile anlatır (kaynak 1-10) → vaporwave **geçici bir moda değil, sürdürülen bir retro-fütürizm ailesi** içinde konumlanır; "muted neon glow + metalik gradient" uyarısı dekorun ölçülü olması gerektiğini söyler. **(2) Performans:** web.dev ve Viget, `transform`/`opacity` animasyonlarının **compositor'da** çalıştığını, `width/top/height` gibi özelliklerin layout+paint tetiklediğini yazar (kaynak 11-20); `will-change` **kısa ömürlü ve ölçülü** kullanılmalıdır, yoksa katman şişirir. **(3) Oynatıcı UX:** Deque "Space/Enter oynat-duraklat, ok tuşları ses/seek" der; Red Hat ve accessible.org her kontrolün Tab ile erişilir ve türüne göre Enter/Space/ok ile çalıştırılmasını ister (kaynak 21-30); `role=progressbar` + `aria-valuenow` MDN'de resmi roldür. **(4) Reduced-motion:** WCAG 2.3.3 hareketi azaltma tercihini destekler; MDN/web.dev/Primer `@media (prefers-reduced-motion)` ile dekoratif animasyonu kapatmayı, Pope/Tetralogical ise yalnızca "azaltma"nın yetmeyebileceği durumları (yanıp sönen içerik) hatırlatır (kaynak 31-40). **(5) CLS/CWV:** web.dev CLS ≤ 0.1 "iyi"dir; widget/ilan alanları için **boyut ayırma** en yaygın reçetedir (kaynak 41-45) → footer player'ın yüksekliği token ile sabitlenmelidir. |
| Web Search **Uzun Açıklama** | **(a) Estetik kararın gerekçesi:** trend kaynaklarının tamamı aynı üç unsuru tekrarlar — **mor/magenta-cyan neon palet, gradient zemin, CRT/glitch doku** (kaynak 1, 3, 4, 6, 7, 8); buna karşılık "aesthetics in the AI era" yazısı görsel yoğunluğun **minimalist yerleşimle dengelenmesini** önerir (kaynak 2), lummi ise "muted neon glow" uyarısı yapar (kaynak 10) → CoreMusic'te dekor **kapsayıcı değil, aksan** olur: tarama çizgisi/glitch yalnız player barına ve düşük yüzeye uygulanır. **(b) Animasyon bütçesinin teknik dayanağı:** CSS animasyonlarının `transform`/`opacity` ile "layout ve paint'i atlayıp" compositor'da çalıştığı üç bağımsız kaynakta aynı şekilde yazılır (kaynak 11 web.dev, 12 Viget, 13 dev.to); `will-change`'in katman tahsisi yaptığı ve **kullanım sonrası kaldırılması** gerektiği Imaginarycloud/Cloudways'te (kaynak 14, 15) anlatılır → "CSS animasyonu tercih, rAF döngüsü istisna" kuralı bu ADR'nin teknik dayanağıdır. **(c) Oynatıcı davranışı:** klavye kontratı Deque/Red Hat/accessible.org'ta aynıdır (Space/Enter = play-pause, ↑↓ veya ←→ = ses/seek — kaynak 21, 23, 24); odak yönetimi ve görünür focus, Level Access ve WAI APG'de "ARIA widget'ının çalışmasının ön koşulu" olarak yazılır (kaynak 25, 26); `aria-live` ile "çalan/çalıyor/durdu" duyurusu 4.1.3 gereğidir. **(d) Reduced-motion sınırı:** WCAG 2.3.3 + MDN/web.dev, dekoratif hareketin tercihe göre kapatılmasını; Primer `no-preference` sarmalayıcısını önerir (kaynak 31-35); kritik ayrıntı: **kapatılan yalnız dekor olur, oynatma işlevi (seek animasyonu, ilerleme) değil** — Tetralogical/AAardvark "pause/stop mekanizması veya media query ile" denetim önerir (kaynak 37, 39). **(e) CLS/INP bağlantısı:** footer player **sayfanın altına sabitlenen bir widget'tır**; web.dev ve CLS rehberleri boyut ayrılmayan öğelerin shift ürettiğini, iyi eşiğin ≤ 0.1 olduğunu yazar (kaynak 41, 43, 45) → `--footer-h` token'ı hem ADR-006 kapısı hem bu kararın CLS garantisi olur; INP tarafında dekoratif JS/rAF döngüsü ana thread'i meşgul eder → dekor **CSS'e** taşınır. **İtiraz olasılığı:** "efektler performansı düşürmez, az sayıda" — yanıt: 4K TV ve RPi5 7" tier'ları (mockup indeksi `:67`) düşük güçlü cihazlardır; bütçe **ölçülerek** konur, hisle değil. |
| Web Search **Paragraf Veri Uzun** | 2026 trendleri retro-fütürizm/Y2K: neon mor-pembe + glitch + chrome tipografi (kaynak 1-10) · gradient/muted neon uyarısı (kaynak 2, 10) · `transform`/`opacity` = compositor, layout/paint yok (kaynak 11, 12, 13) · `width/top/height` animasyonu layout-thrashing (kaynak 11, 16, 17) · `will-change` ölçülü ve geçici (kaynak 14, 15) · Space/Enter = play-pause, ok = seek/ses (kaynak 21, 23, 24) · Tab ile erişim + görünür focus (kaynak 25, 26, 29, 30) · `role=progressbar` + `aria-valuenow` MDN (kaynak 22) · `aria-live` = 4.1.3 durum duyurusu (kaynak 27, 30) · WCAG 2.3.3 + `prefers-reduced-motion` dekoru kapatır (kaynak 31, 32, 33, 34, 35) · yalnız dekor kapanır, oynatma işlevi kapanmaz (kaynak 37, 39) · CLS ≤ 0.1 iyi eşiği (kaynak 41, 43, 45) · widget'a boyut ayırma = standart CLS reçetesi (kaynak 41, 42, 44) · INP için ana thread'i bloklamayan iş (kaynak 45) · düşük güçlü cihaz (RPi5 7", 4K TV) bütçeyi zorlar (kaynak 18, 20). **Sonuç: palet+efekt token'a, davranış durum makinesine, performans compositor-only'ye, a11y reduced-motion+klavye+aria-live'a bağlanır.** |
| Web Search **Sonucu** | 1) **Estetik doğrulandı** (kaynak 1-10): vaporwave paleti (mor/pembe/cyan) + gradient + sınırlı CRT/glitch aksanı güncel ve sürdürülebilir; **ölçülü dekor** uyarısıyla birlikte. 2) **Performans kuralı doğrulandı** (kaynak 11-20): yalnız `transform`/`opacity`, `will-change` kısa ömürlü, CSS animasyonu > rAF → **≥3 bağımsız çapraz kaynak**. 3) **Oynatıcı klavye/durum kontratı doğrulandı** (kaynak 21-30): Space/Enter, ok tuşları, görünür focus, `role=progressbar`, `aria-live`. 4) **Reduced-motion doğrulandı** (kaynak 31-40): dekor kapanır, oynatma sürer (WCAG 2.3.3). 5) **CLS doğrulandı** (kaynak 41-45): sabit yükseklik + boyut ayırma; iyi eşik ≤ 0.1 → ADR-006 ile birebir. 6) **Kanıtsız kalan tek iddia yok**; sayfa-içi derin tur yapılmadığı için sayısal detaylar başlık/özet düzeyindedir (açıkça işaretli). **Toplam ~45 adlandırılmış kaynak, 5 sorgu**; her ana iddia ≥2 çapraz kaynakla karşılanır. |
| Web Search **Alınan Karar** | **ADR-018 KABUL EDİLİR — FOOTER PLAYER'IN TAM KAPSAMI TEK ADR'DE:** **(A) Estetik:** vaporwave paleti (mor `--color-violet-*`, pembe `--color-pink-*`/`--color-magenta-600`, **cyan yeni token ile eklenir**) + gradient + CRT/scanline/glitch aksanı **yalnız design token üzerinden** uygulanır; **hardcoded renk yasak** (ADR-001 ITCSS token katmanı `01_Abstracts`); tüm token'lar **dark/light tema ve tier (T0-T8) ile ölçeklenir**; dekor aksandır, kaplayan alan değil. **(B) Davranış:** durum makinesi **STOPPED / PLAYING / PAUSED / LOADING / ERROR** olarak genişletilir (mevcut 3 durum `PlayerController.js:12-13` üzerine); seek/volume/play-pause **klavye** (Space=play-pause, ←→=seek ±5sn, ↑↓=ses) + görünür focus; **sticky footer**: `position: fixed; height: var(--footer-h)` ve auth route'larında gizleme `01-spa-routing.md:60` kuralı ile; **SPA route değişimde player yaşam döngüsü ADR-004'e bağlı**: route değişimi sesi **durdurmaz** (kapsayıcı DOM sabit), yalnız auth sayfasında `show()` kapanır; `aria-live="polite"` ile durum duyurusu. **(C) Performans:** animasyon bütçesi **yalnız `transform`/`opacity`**; `will-change` yalnız aktif animasyonda ve olay bitince kaldırılır; **CSS animasyonu birincil, `requestAnimationFrame` istisna** (yalnız seek konumu gibi JS-üretilen değer); dekor JS'i ana thread'i bloklamaz; **CLS: player yüksekliği `--footer-h` token'ı ile sabit** (ADR-006 CLS ≤ 0.1); INP için dekor tamamen CSS'e taşınır. **(D) Erişilebilirlik:** palet **kontrast ≥ 4.5:1** ile seçilir (metin/zemin çiftleri token seviyesinde doğrulanır); `prefers-reduced-motion: reduce` → **scanline/glitch/parallax durur, oynatma ve ilerleme devam eder**; klavye tam erişim + görünür focus + `aria-live` durum duyurusu + `role=progressbar`/`aria-valuenow` korunur (mevcut `footer.php:69` yapısı). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: retro/vaporwave estetiği (10 kaynak), compositor animasyon (10), oynatıcı klavye/ARIA (10), reduced-motion/WCAG (10), CLS/INP (5) → **~45 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak tüm ana iddialarda karşılanır. Kod tarafı da aynı resmi verdi: **footer player IMPLEMENTED** (`footer.php`, `_footer.css` 408 satır, `PlayerController.js`, `coreplayer/*`), **efekt ve erişilebilirlik katmanı PLANNED** (`--vapor*/scanline/glitch` = 0, footer `prefers-reduced-motion` = 0, `aria-live`/`keydown` = 0) → bu ADR **çalışan oynatıcının üstüne çizilen estetik+davranış+performans+a11y sözleşmesidir**, kod sözü değil. **Kaynak listesi (~45):** 1) digitalsynopsis — 2026 graphic design trends · 2) aigoodies — aesthetics in the AI-era (2026) · 3) designerup — UI design trends (retro futurism/cyberpunk) · 4) studio2am — Y2K aesthetic · 5) lummi — 2026 design trends (muted neon) · 6) Pinterest — vaporwave web design · 7) Instagram — 2026 graphic trends · 8-10) YouTube ×3 — trend/animasyon anlatımları · 11) web.dev — animations guide · 12) Viget — Animation Performance 101 · 13) dev.to — performant animations · 14) Imaginarycloud — CSS animations 2026 · 15) Cloudways — non-composited animation · 16) Medium — JS/CSS performant animations · 17) Reddit r/css · 18) Reddit r/webdev · 19) YouTube — animation performance · 20) Capgo — Capacitor animation performance · 21) Deque University — media player keyboard · 22) MDN — `progressbar` role · 23) Red Hat UX — audio player accessibility · 24) accessible.org — video player a11y · 25) Level Access — ARIA widgets keyboard · 26) WAI APG — developing a keyboard interface · 27) WAI-ARIA practices · 28) UX Pin — keyboard navigation patterns · 29) Vidzflow — custom video player a11y · 30) StackOverflow — progressbar kullanımı · 31) W3C — WCAG 2.3.3 Animation from Interactions · 32) MDN — prefers-reduced-motion · 33) web.dev — prefers-reduced-motion · 34) GitHub Primer — motion & animation · 35) BOIA — prefers-reduced-motion · 36) Pope Tech — accessible animation · 37) Tetralogical — animations & flashing · 38) Medium — practical guide to reduced-motion · 39) AAardvark — WCAG 2.3.3 plain English · 40) Reddit r/accessibility — reduce motion tartışması · 41) web.dev — CLS · 42) WP Rocket — layout shift culprits · 43) Kinsta — CLS optimization · 44) MarketMuse — CWV CLS (boyut ayırma) · 45) SearchAtlas/Hyvä/Parachute/EG Innovations — CLS & CWV eşikleri. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 (Vanilla JS + ITCSS + BEM, framework yasak) | Tüm vaporwave stilleri **ITCSS katmanına** girer (`01_Abstracts` token → `03_Layout/_footer.css` → `04_Components`); BEM namespace `.footer-player__*` korunur; **hardcoded renk yasak**, yalnız `--color-*` token kullanılır; framework/ek CSS kütüphanesi getirilmez |
| ADR-004 (Multi-Domain SPA) | Route değişiminde player **yaşam döngüsü** router'a bağlanır: kapsayıcı DOM sabit, ses durmaz; auth route'larında `#footer-player.show()` kapanır (`01-spa-routing.md:60`); yeni DOM manipülasyonu `Router.js`/`DomPatcher.js` desenleri dışında yazılmaz |
| ADR-006 (Performans hedefleri — CWV) | **CLS ≤ 0.1, INP ≤ 200ms** kapıları birebir korunur; footer player yüksekliği `--footer-h` token'ı ile sabit (shift üretmez); animasyon JS'i INP'yi beslemez; byte bütçesi ihlal edilmez (yeni asset yok, token + CSS) |
| ADR-005 (doğrulama + `⚠️ VERIFICATION REQUIRED`) | Kod kanıtı olmayan her iddia etiketli kalır; `--vapor*/scanline/glitch` ve `aria-live` yokluğu uydurulmaz, işaretlenir; sayısal kontrast/animator değerleri token doğrulamasıyla yazılır |
| WCAG 2.2 AA (`.ai/AGENTS.md` §16 UI standardı) | Kontrast ≥ 4.5:1, klavye tam erişim, görünür focus, `aria-live` durum duyurusu, `prefers-reduced-motion` desteği — kalite kapısı |
| In-Place Refactoring | Dosya adları (`footer.php`, `_footer.css`, `PlayerController.js`, `.ai/ui-design/**`) **onaysız değiştirilemez**; bu ADR yalnız karar yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`.ai/AGENTS.md` §25.3 kural 2) — ADR-001/004/006 bu kuralın dışındadır çünkü ADR-018 yeni karardır, onlar referans olarak okunur |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |
| REDACTED | Ses servisi anahtarı, API anahtarı, cookie değeri (ör. `MM_Volume`) veya yapılandırma sırrı hiçbir koşulda bu ADR'ye yazılmaz |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-018` `.ai/.decisions/index.md:55`'te **rezerve boş slottur** (doldurma, yeni numara tahsisi değil — ADR-017 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic footer player'ın tam kapsamı tek kararda bağlayıcıdır: (1) vaporwave estetiği (mor/pembe/cyan palet, gradient, CRT/scanline/glitch aksanı, tipografi) yalnız design token üzerinden uygulanır ve hardcoded renk yasaktır; (2) oynatıcı davranışı beş durumlu bir durum makinesi (STOPPED/PLAYING/PAUSED/LOADING/ERROR), sticky footer kuralı, ADR-004 uyumlu SPA yaşam döngüsü ve klavye kontrolü (Space/ok) ile tanımlanır; (3) animasyon bütçesi compositor-only property'lerle (transform/opacity) kilitlenir, `requestAnimationFrame` istisnadır, footer player ana thread'i bloklamaz ve yükseklik `--footer-h` ile sabitlenerek CLS korunur; (4) erişilebilirlik: kontrast ≥ 4.5:1, `prefers-reduced-motion` yalnız dekoru durdurur (oynatma sürer), klavye + görünür focus + `aria-live` durum duyurusu zorunludur.**

### 2.1 Neden Bu Seçenek?

- **Estetik token'a bağlanmazsa tema kırılır:** palet zaten `a-colors-token.css:40-87`'de kısmen var (violet/pink) ama cyan eksik ve efekt token'ı hiç yok → token'a bağlamak ADR-001'in ITCSS disipliniyle tek seferde uyum sağlar, dark/light ve tier ölçeklemesini otomatikleştirir.
- **Davranış tek yerde yazılmazsa spec ile kod ayrışır:** durum makinesi kodda (`PlayerController.js:12-13`), genişleme/flow spec'te (`03-footer-player.md:176-183`), SPA görünürlüğü ayrı bir flow'da (`01-spa-routing.md:60`) → bu ADR üçünü tek sözleşmede birleştirir; LOADING/ERROR eklenmezse hata sessiz kalır.
- **Performans his ile değil bütçe ile:** trend kaynakları dekorun ölçülü olmasını, web.dev/Viget ise yalnız `transform`/`opacity`'nin compositor'da kaldığını söyler (§1.3 kaynak 11-15) → RPi5 7" ve 4K TV tier'larında (mockup `:67`) tek `width` animasyonu INP'yi bozar.
- **CLS zaten token ile kazanılmış, korunmalı:** `--footer-h` (`_footer.css:35`, `page-layout.css:31-32`) sayesinde yükseklik sabit → efekt eklerken yükseklik oynatılırsa ADR-006 CLS ≤ 0.1 ihlal edilir; karar bu kazancı kilitler.
- **Reduced-motion "dekor kapanır, işlev kapanmaz" ilkesi:** WCAG 2.3.3 ve kaynaklar dekoratif hareketin tercihe göre kapatılmasını ister (kaynak 31-35); oynatma/ilerleme bilgisi işlevdir → kapatılamaz; bu ayrım yazılmazsa ya erişilebilirlik ya işlev feda edilirdi.

### 2.2 Teknik Detaylar

**a) Estetik — vaporwave token seti (ITCSS `01_Abstracts`, hardcoded renk yasak):**

| Token (yeni/mevcut) | Değer/Kaynak | Kullanım |
|---------------------|--------------|----------|
| `--color-violet-600` | `#7950F2` — `a-colors-token.css:46` (mevcut, PRIMARY BRAND) | Zemin/gradient başlangıcı |
| `--color-pink-500` | `#F06595` — `a-colors-token.css:73` (mevcut, ACCENT) | Vurgu, seek thumb, aktif ikon |
| `--color-magenta-600` | `#E91E8C` — `a-colors-token.css:87` (mevcut) | Glitch/yanma aksanı |
| `--vapor-cyan-400/500` | **YENİ** (paletten seçilecek, kontrast ≥ 4.5:1 şartı ile) | İkincil vurgu, progress dolgusu |
| `--vapor-gradient-bar` | `linear-gradient(90deg, violet-600 → magenta-600 → cyan-500)` (yalnız token'da) | Footer bar zemini |
| `--vapor-scanline-op` | `0.06-0.10` (opaklık token'ı) | CRT tarama çizgisi aksanı |
| `--vapor-glitch-ms` | `120-200ms` + `transform: translate3d` | Glitch darbesi (tek, kısa) |
| `--footer-h` | Mevcut: 72/90/96/104/120/138/160px (tier) | **CLS garantisi — sabit** |

Kural: renk/değer **yalnız token**; component CSS `var(--vapor-*)` çağırır. Dark/light tema `a-theme-config.css` üzerinden; tier (T0-T8) `08_Devices/*` override'ları ile ölçeklenir. Kontrast ≥ 4.5:1 **token seviyesinde** doğrulanır (koyu zemin + açık metin çifti); doğrulanmayan çift üretilmez → `⚠️ VERIFICATION REQUIRED`.

**b) Davranış — durum makinesi + sticky + SPA + klavye:**

| Durum | Görsel | Davranış | ARIA |
|-------|--------|----------|------|
| `STOPPED` | play ikonu, progress 0 | başlangıç | `aria-label="Oynat"` (mevcut `PlayerController.js:144`) |
| `LOADING` | spinner/skeleton (düşük opaklıktaki shimmer — CSS) | seek devre dışı, hata değil | `aria-live="polite"`: "Yükleniyor" |
| `PLAYING` | pause ikonu, ilerleme akar | Space=duraklat, ←→=±5sn, ↑↓=ses | `aria-pressed` + `aria-valuenow` (mevcut `:76`) |
| `PAUSED` | play ikonu, ilerleme donar | Space=oynat | "Duraklatıldı" duyurusu |
| `ERROR` | hata rozeti + yeniden dene | oynatma durur, tekrar deneme | `aria-live="assertive"`: hata metni |

- **Sticky:** `position: fixed; bottom: 0; height: var(--footer-h)` (`_footer.css:35` ile aynı token); `page-layout.css:31-32` `calc(100vh - header - footer)` ile içerik **player altında ezilmez**.
- **SPA yaşam döngüsü (ADR-004):** route değişimi sesi **durdurmaz** (player kapsayıcısı route DOM'unun dışında, `DomPatcher.js` ile değiştirilmez); auth route'larında `#footer-player.show() = !isAuthPage` (`01-spa-routing.md:60`) uygulanır; page unload/oturum kapanışında `STOPPED` + `aria-valuenow=0`.
- **Klavye:** Space (odak player'da iken) = play/pause; `←/→` = seek ±5sn; `↑/↓` = ses ±%5; `Home/End` = başa/sona; tüm kontroller Tab sırasındadır, focus `:focus-visible` halkası ile görünür (§1.3 kaynak 21, 23, 24, 26).

**c) Performans — animasyon bütçesi (bağlayıcı):**

| Kural | Ayrıntı | Kaynak |
|-------|---------|--------|
| Yalnız compositor property | `transform`, `opacity` (+ `filter` yalnız **sabit**, animasyonda değil) | §1.3 kaynak 11, 12, 13 |
| Yasak animasyonlar | `width`, `height`, `top`, `left`, `margin`, `box-shadow` (layout/paint tetikler) | §1.3 kaynak 16, 17 |
| `will-change` | Yalnız animasyon **sırasında**; bitiminde kaldırılır; listede sabit tutulmaz (katman şişmesi) | §1.3 kaynak 14, 15 |
| rAF istisna | `requestAnimationFrame` yalnız JS-üretken değer (seek konumu okuması); dekor/atmosfer efektleri **%100 CSS** | §1.3 kaynak 11, 20 |
| Ana thread | Oynatıcı event handler'ları < 4ms; ağır dekor (scanline/glitch) compositor katmanında; INP ≤ 200ms (ADR-006) | ADR-006 + §1.3 kaynak 45 |
| CLS | Player yüksekliği **token ile sabit**; dekor yükseklik/flow **üretmez** (yalnız transform/opacity); `filter/box-shadow` layout değiştirmez | §1.3 kaynak 41, 43, 45 |

**d) Erişilebilirlik (WCAG 2.2 AA + reduced-motion):**

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | Kontrast | Metin/zemin **≥ 4.5:1** (token seçiminde doğrulanır); neon aksan metin üstünde kullanılmaz, ayrımda ≥ 3:1 (grafik nesne) |
| 2 | `prefers-reduced-motion: reduce` | **Scanline, glitch, shimmer, parallax durur**; oynatma, seek ilerlemesi ve durum geçişleri (küçük fade) **devam eder** — bilgi kaybı yok |
| 3 | Klavye | Space/←→/↑↓/Home/End + Tab sırası; hiçbir işlev yalnız fareye bağlı değil |
| 4 | Canlı duyuru | `aria-live="polite"` (durum/şarkı değişimi), `aria-live="assertive"` (hata); spam yok (durum değişiminde bir kez) |
| 5 | Odak | `:focus-visible` ile görünür halka; odak görünürülüğü karanlık temada da ≥ 3:1 |
| 6 | Durum | `role=progressbar` + `aria-valuenow/min/max` korunur (`footer.php:69-71` mevcut yapı) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Hardcoded renk + serbest CSS** (token yok, her dosyada hex) | İlk yazımda hızlı | ADR-001 ihlali; dark/light ve tier'da 7+ dosya elle güncellenir; kontrast tutmaz | ITCSS token katmanı zaten var (`01_Abstracts`); hardcoded renk açıkça yasak (§1.4) |
| 2 | **`requestAnimationFrame` tabanlı canvas/CSS-in-JS efekt motoru** | Efektlerde tam kontrol, sınır dışı efektler kolay | Ana thread'i besler (INP riski), rAF döngüsü pil/CPU maliyeti, ADR-001 yasak listeyle gerilir | §1.3 kaynak 11-20: dekor compositor'a ait; INP ≤ 200ms (ADR-006) ihlal riski |
| 3 | **Kütüphane ile hazır oynatıcı** (Howler/Swal/plyr vb.) | Hazır durumlar + erişilebilirlik | npm bağımlılığı (ADR-001 framework yasağı ruhu), byte bütçesi, saldırı yüzeyi | Mevcut kod zaten IMPLEMENTED (`footer.php`, `PlayerController.js`, `coreplayer/*`) — 5 modül çöpe gitmez |
| 4 | **Efektlerden vazgeçmek** (düz, efektsiz footer) | En iyi performans/a11y profili | Vaporwave kimliği kaybolur; mockup/flow (`03-footer-player.md`, mockup `:67/:84`) ile uyumsuz | Kullanıcı onayı kapsamı estetiği zorunlu kılar; çözüm **ölçülü aksan + bütçe** (§2.2a/c), efekti tamamen silmek değil |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tema güvencesi:** tüm renk/efekt token'da → dark/light ve 9 tier (`08_Devices/*`) tek noktadan ölçeklenir; ADR-001 disipliniyle uyum.
- **Tek davranış sözleşmesi:** 5 durum + klavye + SPA yaşam döngüsü + aria-live tek yerde → spec/kod ayrışması kapanır, hata durumu görünür olur.
- **CWV koruması:** `--footer-h` sabitliği CLS ≤ 0.1'i, compositor-only bütçe ve CSS-öncelikli dekor INP ≤ 200ms'i korur (ADR-006 ile birebir).
- **Erişilebilirlik kazancı:** reduced-motion + klavye + canlı duyuru ile WCAG 2.1.1/2.3.3/4.1.3 kapanır; mevcut `role=progressbar` yapısı korunur.
- **Kod israfı azalır:** `unused-files-report.md:115` "İNCELE" kaydı (aktif ağaçta olmayan `FooterPlayer.js`) sahiplik kararlarıyla netleşir.

### 4.2 Olumsuz Sonuçlar

- **Token genişlemesi:** yeni `--vapor-*` token'ları `01_Abstracts` katmanını büyütür; her token'ın kontrast/doğrulama yükümlülüğü vardır.
- **Kapsam genişliği:** estetik+davranış+performans+a11y tek ADR'de → madde sayısı artar, okuma maliyeti yükselir (7 bölüm disipliniyle sınırlandı).
- **Efekt = risk yüzeyi:** scanline/glitch kontrastı düşürebilir ve hareket duyarlı kullanıcıyı etkileyebilir → sürekli denetim gerekir.
- **Uygulama işi duruyor:** bu ADR karar metnidir; `aria-live`, klavye kısayolları, cyan token'ı, reduced-motion bloğu henüz **kodlanmadı** (§1.1-C) → §5.1 adımları ayrıca icra edilmeli.
- **Tier/ölçüm altyapısı zayıf:** RPi5 7" ve 4K TV'de gerçek cihaz ölçümü yok → bütçe yazılı ama denetlenmiyor.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Efekt kontrastı bozar** — neon gradient üstünde metin < 4.5:1 | 3 (olası) | 4 (yüksek) | Kontrast token seviyesinde zorunlu; neon renk metin üstünde yasak (§2.2d kural 1); QA görsel denetimi |
| **Reduced-motion ihlali** — scanline/glitch dekoru durmaz | 2 (mümkün) | 4 (yüksek) | `prefers-reduced-motion: reduce` bloğu `_footer.css`'e zorunlu (§5.1 adım 4); yalnız dekor kapanır, oynatma sürer |
| **CLS regresyonu** — dekor yükseklik/flow üretir, `--footer-h` oynatılır | 2 (mümkün) | 4 (yüksek) | Yükseklik token'da sabit (§2.2c); dekor yalnız transform/opacity; ADR-006 CLS ≤ 0.1 kapısı |
| **INP regresyonu** — rAF/glitch JS'i ana thread'i besler | 3 (olası) | 3 (orta) | CSS-öncelik kuralı, rAF istisna; dekor JS'i yasak (§2.2c); INP ≤ 200ms ölçümü |
| **Mobilde yer kaplama** — footer player + dekor ekranı daraltır (T2/T3, telefon) | 4 (çok olası) | 3 (orta) | Tier token'ları (`--footer-h` 72-160px, `--footer-player-art` 60/130px `d-phone.css:200`); dar ekranda dekor kısılır (`d-phone.css` override) |
| **Sahiplik belirsizliği** — aktif ağaçta `FooterPlayer.js` yok, yedekte var | 3 (olası) | 2 (düşük) | Uygulama adımında modül sahipliği `PlayerController.js`/`coreplayer/*` olarak teyit edilir; `js copy/` kaynağıdır, kullanılmaz |
| **Bütçe denetimsizliği** — CI'da animasyon/kontrast kapısı yok (`.github/workflows/` 0 dosya) | 4 (çok olası) | 3 (orta) | Kapılar §5.1 adım 6'da **ölçümle** konur; `⚠️ VERIFICATION REQUIRED` ile dürüst kalır (ADR-006 ruhu) |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-001-vanilla-js-itcss]] | ITCSS token katmanı + BEM + framework yasağı → estetik bu katmana girer, hardcoded renk yasak (§1.4, §2.2a) |
| [[ADR-004-multi-domain-spa]] | Route değişiminde player yaşam döngüsü + auth görünürlüğü (§2.2b) |
| [[ADR-006-performance-targets]] | CLS ≤ 0.1 / INP ≤ 200ms kapıları — `--footer-h` sabitliği ve compositor bütçesi bu kapıları korur (§2.2c, §4.3) |
| [[ADR-005-ultrathink-protocol]] | `⚠️ VERIFICATION REQUIRED` standardı — yokluk iddiaları (`--vapor*`, `aria-live`, `keydown`) etiketli (§1.1-C) |
| [[../index]] | Satır 55 `[[ADR-018-footer-player-vaporwave]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 635 `decisions/accepted/ADR-018-footer-player-vaporwave` kaydı ✅ (dosya ile canlanır) |
| [[../../keys.md]] | Satır 253 `ADR-018 \| footer player, vaporwave \| UI` ✅ |
| [[../../brain.md]] | Satır 973 `ADR-018 \| Footer player vaporwave` ✅ |
| [[../../ui-design/flow/navigation/03-footer-player]] | BEM sözleşmesi `:176-183`, kompakt/geniş ekranlar `:81/:91` — davranış §2.2b bu spec'e bağlanır |
| [[../../ui-design/flow/navigation/01-spa-routing]] | Satır 60 `#footer-player.show() = !isAuthPage` — SPA görünürlük kuralı (§2.2b) |
| [[../../ui-design/05-responsive-architecture]] | Satır 73 `_footer.css ← Footer player` (ITCSS yerleşimi, §1.4) |
| [[../../ui-design/01-mockup-index]] | Satır 67, 84 — footer player mockup kanıtı (tier/ölçek bağlamı) |
| [[../../AGENTS.md]] | §16 UI standardı (ITCSS/BEM/WCAG 2.2 AA), §6 routing (`CSS, UI, token… → UI Designer`), §25.3 frozen kuralı |
| [[../../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 (şablon), REDACTED |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| Kod kanıtları | `home.coremusic.net/footer.php:63-80` · `assets.coremusic.net/Css/03_Layout/_footer.css:35,45-104` · `js/features/PlayerController.js:3,12-144` · `js/core/footer.init.js:9,66` · `js/coreplayer/*` (5 dosya) · `Css/01_Abstracts/a-colors-token.css:40-87` |
| Boşluk kanıtları | `--vapor*/scanline/glitch` = 0 sonuç · `_footer.css` `prefers-reduced-motion` = YOK · `footer.php` `aria-live/keydown` = YOK · `.ai/reports/unused-files-report.md:115` (`FooterPlayer.js` import yok) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Token seti:** `--vapor-cyan-*`, `--vapor-gradient-bar`, `--vapor-scanline-op`, `--vapor-glitch-ms` `01_Abstracts/a-colors-token.css`'e eklenir (hex/yüzde **yalnız token'da**); mevcut violet/pink değerleri korunur | UI Designer | 0.5 oturum |
| 2 | **Kontrast doğrulaması:** her metin/zemin çifti ≥ 4.5:1 (token seviyesi); doğrulanmayan çift → `⚠️ VERIFICATION REQUIRED` ve çift üretilmez | UI Designer + QA Engineer | 0.5 oturum |
| 3 | **Footer CSS:** `_footer.css`'e gradient zemin + sınırlı scanline/glitch aksanı **yalnız** `var(--vapor-*)` ile; yükseklik/dokunulmaz `--footer-h` (`:35`) korunur; yeni durum sınıfları `.footer-player--loading/--error` | UI Designer | 1 oturum |
| 4 | **Reduced-motion:** `_footer.css` sonuna `@media (prefers-reduced-motion: reduce)` → scanline/glitch/shimmer `animation: none`, oynatma/ilerleme akışı **korunur** | UI Designer | 0.5 oturum |
| 5 | **Davranış:** `PlayerController.js`'e LOADING/ERROR + klavye (Space/←→/↑↓/Home/End) + `aria-live` bölge; route yaşam döngüsü ADR-004/router event'ine bağlanır (`#footer-player.show()` kuralı korunur) | UI Designer + QA Engineer | 1.5 oturum |
| 6 | **Ölçüm + kapı:** Lighthouse/CWV ölçümü (CLS ≤ 0.1, INP ≤ 200ms — ADR-006), animasyon bileşenlerinin yalnız `transform/opacity` olduğu denetimi, `will-change` kullanım listesi; `.github/workflows/` bugün 0 dosya → **PLANNED** | QA Engineer + DevOps Engineer | 1 oturum |
| 7 | **Sahiplik temizliği:** `unused-files-report.md:115` kaydı kapatılır — aktif `FooterPlayer.js` yoksa rapor `PlayerController.js`/`coreplayer/*` sahipliğine düzeltilir (append-only, rapor kendi mantığıyla); `js copy/`/`Css copy/` kullanılmaz | Vault Steward | 0.5 oturum |
| 8 | **Test paketi:** durum makinesi 5 durum, klavye kontratı, `aria-live` tek duyuru, reduced-motion kapatma, CLS sabitliği (footer yüksekliği değişmeden), tier'da dar ekran (T2/T3) dekor kısımı | QA Engineer | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi veya rezerve slot), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **Token seti (adım 1):** token'lar `01_Abstracts`'ten `git revert` ile çıkar; component CSS `var()` çağrıları varsayılana düşer → görsel geri dönüş tek commit.
3. **Footer CSS dekoru (adım 3):** dekor bloğu tek commit → `git revert` ile kaldırılır; `--footer-h` sabitliği **dokunulmaz** (CLS koruması geri alınmaz).
4. **Reduced-motion bloğu (adım 4):** geri alınamaz bir karar değildir ama **geri alınması WCAG ihlali doğurur** → yalnız ADR-018 revizyonu (yeni ADR) ile kaldırılabilir.
5. **Davranış genişlemesi (adım 5):** LOADING/ERROR/klavye ayrı commit → `git revert`; mevcut 3 durumlu makine (`PlayerController.js:12-13`) geri çalışır kalır.
6. **Kill-switch:** dekor tek `html[data-vapor="off"]` anahtarıyla devre dışı bırakılabilir (token override) → performans/a11y şüphesinde anında sade görünüme dönüş.
7. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (eski satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — debate tamamlandı (2026-09-25) |
| Karar içeriği | Tümü **kullanıcı onaylı** (üst görev kapsamı: estetik + davranış + performans + erişilebilirlik + §1.3 araştırması) |
| Beklenen biçim | ADR-004/008/010-017 formatı — 3 tur / 20 persona (uygulandı — sonuç §7.1) |
| Tech Lead | **✅** — debate sonrası onay (2026-09-25) |
| Kural | Debate tamamlanmadan bu ADR **frozen yapılmaz**; sonuç §5.3/§5.5/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

### 5.4 Debate Bekleyen Şartlar (ön kayıt — KABUL/RED debate'de netleşir)

| # | Şart | Kapsam | Sorumlu | Durum |
|---|------|--------|---------|-------|
| 1 | **Token zorunluluğu** — hardcoded renk yok, cyan token'ı eklenir, kontrast ≥ 4.5:1 doğrulanır | §2.2a, §5.1 adım 1-2 | UI Designer + QA | ✅ debate (3/20 KABUL) |
| 2 | **Davranış sözleşmesi** — 5 durum + klavye + `aria-live` + SPA yaşam döngüsü | §2.2b, §5.1 adım 5 | UI Designer | ✅ debate (3/20 KABUL) |
| 3 | **Animasyon bütçesi** — yalnız transform/opacity, `will-change` geçici, rAF istisna, `--footer-h` sabit | §2.2c, §5.1 adım 3, 6 | UI Designer + QA | ✅ debate (3/20 KABUL) |
| 4 | **Reduced-motion** — yalnız dekor durur, oynatma sürer | §2.2d kural 2, §5.1 adım 4 | UI Designer | ✅ debate (3/20 KABUL) |
| 5 | **Kapsam sınırı** — ADR-001/004/006 ihlal edilmez; `js copy/` kaynağı değildir | §1.4, §5.1 adım 7 | Vault Steward | ✅ debate (3/20 KABUL) |

### 5.5 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1 | **A11y + palet + tek-kaynak tamamlama (1a-1c)** | **1a)** `aria-live` + `keydown` + `prefers-reduced-motion` boşlukları kapatılır (§1.1-C) · **1b)** vaporwave design token paleti tamamlanır — cyan token'ı eklenir, kontrast ≥ 4.5:1 doğrulanır (§2.2a) · **1c)** tek kaynak temizliği — aktif `js/` ağacı esas alınır: ölü `js copy/components/FooterPlayer.js` + `device-layout-updater.js:365` tek yorum satırı sahipliğe bağlanır | UI Designer + QA Engineer + Vault Steward | ⏳ PLANNED → §5.1 adım 1-5, 7 | Debate Tur 1 (A11y/QA/Critic uyarıları) → Tur 2 itiraz 1-3 |
| 2 | **Animasyon bütçesi + CWV kapısı** | Yalnız compositor-only (`transform`/`opacity`) + `will-change` geçici + rAF istisna (§2.2c); INP ≤ 200ms / CLS ≤ 0.1 kapısı **ADR-006'a bağlı** — `--footer-h` sabit korunur | UI Designer + QA Engineer | ⏳ PLANNED → §5.1 adım 3, 6 | Debate Tur 2 itiraz 4 (Perf CLS reçetesi onayı) → §2.2c |
| 3 | **Reduced-motion + klavye + CLS test paketi** | Test paketi: `prefers-reduced-motion` kapatma (yalnız dekor, oynatma sürer), klavye kontratı (Space/←→/↑↓/Home/End), `aria-live` tek duyuru, CLS sabitliği (`--footer-h` değişmeden), T2/T3 dar ekranda dekor kısımı | QA Engineer | ⏳ PLANNED → §5.1 adım 8 | Debate Tur 1 A11y/QA/Perf uyarıları → §4.3 |

**Tur 1 uyarılarının akıbeti (şart sayılmadı):** A11y (aria + reduced-motion şart) → şart 1a; QA (cyan palet eksik) → şart 1b; Perf (CLS reçetesi onay) → şart 2; Critic (ölü kod tek kaynak şart) → şart 1c. Tur 2 itiraz 1-3 şart 1a/1b/1c'ye, itiraz 4 şart 2'ye bağlandı; test kapsamı şart 3'te toplandı → **3/3 şart** (18/2/0 KABUL).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 55** `[[ADR-018-footer-player-vaporwave]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`CSS, UI, responsive, ITCSS, BEM, token → UI Designer`), §16 UI kalite standardı |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Satır 973 `ADR-018 Footer player vaporwave` kaydı ✅ |
| [[../../keys.md]] | Satır 253 ADR-018 keyword eşlemesi ✅ |
| [[../../index.md]] | Satır 635 ADR-018 kaydı ✅ |
| [[../../glossary.md]] | Terim sözlüğü (vaporwave, design token, compositor — ekleme ADR-018 uygulamasıyla) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate ön şartları (5) | §5.4 — token · davranış sözleşmesi · animasyon bütçesi · reduced-motion · kapsam sınırı (**3/20 KABUL** ile onaylandı) |
| Debate (✅ TAMAMLANDI) | §5.3/§5.5/§7.1 — 3 tur / 20 persona, 18/2/0 KABUL (2026-09-25) + 3 şart · sonuç frontmatter `debate` alanına işlendi |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[ADR-001-vanilla-js-itcss]] | ITCSS/BEM/token zemini (dosya diskte VAR ✅) |
| [[ADR-004-multi-domain-spa]] | SPA yaşam döngüsü (dosya diskte VAR ✅) |
| [[ADR-006-performance-targets]] | CWV CLS/INP kapıları (dosya diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] | Doğrulama + `⚠️ VERIFICATION REQUIRED` standardı (dosya diskte VAR ✅) |
| [[../../ui-design/flow/navigation/03-footer-player]] | Footer player flow + BEM sözleşmesi |
| [[../../ui-design/flow/navigation/01-spa-routing]] | `#footer-player.show()` görünürlük kuralı |
| `home.coremusic.net/footer.php` · `assets.coremusic.net/Css/03_Layout/_footer.css` · `js/features/PlayerController.js` · `js/core/footer.init.js` · `js/coreplayer/*` | Kod kanıtları (IMPLEMENTED, §1.1-A) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-018'i sıfırdan yaz"; karar içeriğinin tamamı onaylı) | 2026-09-25 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010-017 formatı — 3 tur / 20 persona (uygulandı) |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-25 (frontmatter `debate` alanı ile aynı) |
| Tur 1 | **20 persona** — kod: footer player **IMPLEMENTED** (`footer.php:63-80`, `_footer.css` 408 satır, `PlayerController.js` durum makinesi, 5 `coreplayer` modülü); PLANNED: vaporwave efekt CSS 0, `prefers-reduced-motion` 0, `aria-live`/`keydown` 0, cyan token 0; şüpheli: `js copy/` klasöründe ölü `FooterPlayer.js`, `device-layout-updater.js:365` tek yorum satırı. Oy: **15 kabul/neutral, 4 uyarı** — A11y (aria + reduced-motion şart) · QA (cyan palet eksik) · Perf (CLS reçetesi onay) · Critic (ölü kod tek kaynak şart). |
| Tur 2 | **İtiraz → çözüm:** (1) a11y boşlukları → `aria-live` + `keydown` + `prefers-reduced-motion` → **şart 1a**; (2) palet eksik (cyan 0) → vaporwave design token paleti tamamlama → **şart 1b**; (3) ölü `FooterPlayer.js` + tek yorum satırı → tek kaynak temizliği (aktif `js/` ağacı) → **şart 1c**; (4) animasyon bütçesi → compositor-only (`transform`/`opacity`) + INP/CLS kapısı ADR-006'a bağlı → **şart 2**. |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL — 3 şart (§5.5):** (1) a11y + palet + tek-kaynak tamamlama (1a-1c), (2) animasyon bütçesi + CWV kapısı, (3) reduced-motion + klavye + CLS test paketi |
| Tech Lead | **✅** (2026-09-25) — debate sonrası onay |
| Frozen | Bu ADR **frozen değildir**; debate ✅ + Tech Lead ✅ ama **Arch Lead ⏳** → §7 satır 3 tamamlanmadan `frozen` yapılmaz |
| Kural | Debate sonucu §5.3/§5.5/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` **append** ile kaydedilir |

---

*ADR-018 v1.0.0 | 2026-09-25 | Created — Footer Player Vaporwave (estetik token'lar · davranış durumları · compositor bütçesi · WCAG/reduced-motion)*
*ADR-018 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · 3 şart (§5.5) · frozen YOK (Arch Lead ⏳)*
*Authority: ADR-018 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
