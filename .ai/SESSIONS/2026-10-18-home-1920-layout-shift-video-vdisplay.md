# 2026-10-18 — Home 1920 Layout Shift (root cause: vdisplay video in flow)

**Talep:** "1920 de layout kaymış — figma data göre düzelt · ortalama yok"
**Durum:** ✅ Tamamlandı (working tree — commit YOK, onay bekliyor)

## Kök Neden (dikey kayma)

`shared/src/PageRouter/HtmlShellRenderer.php:193` kalıcı `<video id="video" class="vdisplay">` render ediyor.
Bu video için **hiçbir CSS kuralı yoktu**. `b-base-core.css:61` `img, svg, video, canvas { display: block }`
kuralı video'yu `display:block` yapıyor → default **300×150** replaced-element boyutuyla `<body>` normal
akışında duruyor → `.l-main-wrapper` (tüm sayfa içeriği) **150px** aşağı itiliyor.

- `<audio class="vdisplay">` bu kuralda olmadığı için yer kaplamıyor (h0) — etkisiz.
- Sonuç: üst satır y99 olması gerekirken **y249**'da (150px aşağı). Bu, tüm cihaz/sayfada geçerli bir bug.

### Kanıt (tarayıcı ölçümü, before → after)
| Element | Before | After | Figma SSOT |
|---|---|---|---|
| `.l-main-wrapper` | y210 | y60 | — |
| `.home-layout__top--wide` | y249 | **y99** | y99 ✓ |
| `.player-info` | 455×202 @x53 | **469×184 @x53** | 469×184 @x53 ✓ |
| `.welcome-banner` | 491×184 @x552 | 491×184 @x552 | 491×184 @x552 ✓ |
| `.widget-grid` | 752×184 @x1073 | 752×184 @x1073 | 752×184 @x1073 ✓ |
| `video.vdisplay` | display:block h150 | **display:none h0** | — |

`margin-top:60 (header) + padding-top:39 = y99` — yani **tek** sapma kaynağı videonun 150px'iydi.
Yatay konumlar (x53/552/1073) baştan beri Figma ile eşleşiyordu (469fr 491fr 752fr grid).

## Değişiklik (2 dosya, benim)

1. **`assets.coremusic.net/Css/02_Base/b-base-core.css`**
   `img, svg, video, canvas` kuralından SONRA yeni kural:
   `audio.vdisplay, video.vdisplay { display: none; }`
   - Neden hedefli: `video{display:block}` genel kuralı meşru inline videoları bozmasın diye yalnız kalıcı shell medya elemanları (`.vdisplay`) gizleniyor.
   - coreplayer `#video/#audio`'yu `getElementById` ile erişir — `display:none` media API'yi (play/src) bozmaz.
   - Tüm device bundle'ları `b-base-core.css` yükler → video her cihazda akış dışına çıkar (embedded'i de düzeltir/bBozmaz).

2. **`assets.coremusic.net/Css/01_Abstracts/a-layout-tokens-1920.css`**
   `--cm-player-info-w: 455px → 469px`, `--cm-player-info-h: 202px → 184px`
   - Figma 1920 SSOT: `extracted-1920.md` → `2849:21489 "Player Info" w=469 h=184`, kapak `2849:21461` 150×150.
   - Bu token'lar **yalnız** `a-layout-tokens-1920.css`'te (1024'te tanımsız). Yalnız `d-desktop.css` + `d-laptop.css` import eder; `d-embedded.css` import etmez → embedded player `width:var(--cm-player-info-w)` tanımsız → auto, etkilenmez.
   - Kart 469px → grid kolonunu (469fr) doldurur; banner'a gap 30px (Figma).
   - Yükseklik 184 → üst satır 184'e oturur; banner + widget `align-items:stretch` ile 184'e eşitlenir (Figma hizası). Kapak 150 + pad-top20 = 170 < 184 → clip yok (14px alt boşluk).

## Doğrulama

- Tarayıcı (chrome-devtools, viewport 1920×1080, cache-bypass reload): üst satır y99, player 469×184, banner 491×184, widget 752×184, kapak clip yok.
- Konsol: 0 hata.
- Şarkı şeridi `.home-song__mini-card` 169×43 @x53 (Figma boyut + sol hiza tam), y358 (Figma y369 — **11px fark**, minimal).

## Bilinen residual (düşük öncelik, bu görev kapsamı DIŞI)

- Şarkı şeridi dikeyi ~11px yüksek. `.home-cards-wrapper--wide` / `.home-layout__bottom--wide` CSS sınıfları DOM'da YOK — wide-specific kart-wrapper padding kuralları farklı sınıflara (`home-song-btn-list-div` / `home_song__btn-dashboard`) hedefliyor ve uygulanmıyor. Ayrı bir class-wiring meselesi; istenirse follow-up.

## Working tree notu (benim değişikliklerim DIŞINDA)

`git diff` working tree'de önceki session'dan kalma **commit EDİLMEMİŞ** değişiklikler var (bunlar bana ait değil):
`_home-layout.css` (align-items start→stretch), `_home-components.css`, `_welcome-banner.css`,
`Shared/HomeLayoutRouter.php`, `pages/home.php`, `WelcomeBannerComponent.php`, `welcome-banner.php`.

## Kapılar

- Commit **atılmadı** (onay bekliyor). Subagent commit atmaz; pathspec ile yalnız 2 dosya commit edilebilir.
- Yasaklar uyuldu: FIGMA_TOKEN hiçbir .md/.json/.log'a yazılmadı; mockup gate (`.ai/.png/home-1920/`) okundu.
