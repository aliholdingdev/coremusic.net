---
reference_doc: "Kalıp C çekirdek — .ai/.templates/ui-design/prompt-template.md (Guardrail #16)"
title: "CoreMusic — Home Page Whole-Page Rebuild + Multi-Resolution Verify (1024→3840)"
type: prompt
category: ui-design
pattern: C
scope: "home-page (header/footer/player-info DIŞINDAKİ tüm component'lar)"
date: 2026-10-01
version: 2.0.0
status: active
assignee: "Senior Frontend Developer (UI Designer, A3 — K10-K11)"
authority: "Prompt — SSOT: .ai/.templates/ui-design/prompt-template.md · .ai/ui-design/01-mockup-index.md"
governance: Red Team · Human Mode · Truth Mode
---

# Home Page Whole-Page Rebuild — Senaryo Prompt (1024 / 1920 / çok çözünürlük doğrulama)

> **UZMAN ATAMASI:** Senior Frontend Developer / UI Designer (A3). Orkestratör dispatch eder, kodu uzman yazar.
> **MAX THINKING: 5000 token.** Kısa düşün · öz yaz · uzun plan kompozisyonu YASAK. 3 başarısız düzeltme → DUR, 1 kısa soru sor.
> **NE ZAMAN OKUNUR:** Ana Sayfa'nın header, footer (player bar) ve Player Info **dışındaki** tüm bileşenleri
> Figma mockup'ına göre yeniden yazılırken / düzeltilirken.
> **NOT:** Vault'a (`.ai/ui-design/prompt/`) taşınırsa bu dosya Kalıp C'ye katlanır.

---

## Senaryo (Scenario)

**Sahne:** CoreMusic Ana Sayfa (`http://home.coremusic.net:81/home`). **1024 ve 1920 İKİ FARKLI SAYFADIR — asla birbirine karıştırma.**

| | 1024 (T07 embedded) | 1920 (T17 desktop) |
|---|---|---|
| Viewport | 1024×600 | 1920×1080 |
| Mockup | `.ai/.png/home-1024/Linux  1024 - Home Page.png` (+ Popup png) | `.ai/.png/home-1920/Linux - 1920 - Home.png` |
| Layout | 2 kolon: now-playing + widget grid (sağ) · 2×4 listeler · Sıradaki Şarkılar | now-playing (detaylı) + welcome-banner · tek satır listeler · Sıradaki yok |
| Widget slot | **12** (§13.9) | **20** (§13.9) |

Player Info çalışması **tamamlandı ve dokunulmaz**. Bu görev onun dışındaki **her şeyi** kapsar: tüm component'lar, HTML, CSS, JS.

**Uzmanın rolü:** Mevcut sayfayı gözden geçir. **Hatalı olan yerleri sıfırdan yeniden yaz, doğru olanları bırak.** PNG + Figma raw + token = tek gerçek kaynak; birebir eşleşme zorunlu.

**Bilinen hatalar (öncelikli):**
1. **Track btn & playlist btn bozuk** — oynat/aksion davranışı ve etiket yanlış; mockup'taki gibi çalışacak (tıklama → doğru eylem, hover/focus state'leri, PNG ile birebir görünüm).
2. Bazı bloklar yanlış render olmuş → tespit et, düzelt, yeniden render et.

**Kapsam (SINIR — ihlal = görev reddi):**

| YAPILACAK | YAPILMAYACAK |
|-----------|--------------|
| Header, footer/player bar, `_player-info` **DIŞINDAKİ** her şey: now-playing, welcome banner, widget grid, "En Son Dinlenen", "Son Oluşturulan & Sistem Playlistleri", "Sıradaki Şarkılar", track/playlist butonları, welcome popup, sayfa arka planı | Header, footer, `_player-info.css`, `PlayerInfoComponent.js`, `player-info.php` |
| Mevcut dosyalarda MEVCUT satırlarda edit (HTML/CSS/PHP/JS) | **Yeni dosya oluşturmak** |
| CSS yalnız `assets.coremusic.net/Css/` (SSOT) | Başka dizine CSS/JS taşımak |
| 1024 ve 1920'yi mockup'a birebir; 4K aynı token zincirinden türer | Uydurma ölç, tahmin token, `clamp()` varsayımı |
| Mevcut sayfada browser testi (screenshot'lı) | Sadece kod okuyup "geçti" demek |

**Mockup Gate (§13 / Guardrail #11):** Kod ÖNCE PNG'ler okunur — okunamazsa **DUR**.

---

## AI Code Generation Prompt

### Context
Ana Sayfa bileşen katmanı (ITCSS `05_Pages/_home*.css` + `04_Components/_*.css` + PHP/JS). Vanilla JS ES6+ + ITCSS (ADR-001), BEM zorunlu, `innerHTML` yasak, CSP nedeniyle inline `style`/`script` yasak (ADR-012), token-first (ham hex/px yalnız `01_Abstracts`). Değişiklik yalnız mevcut dosyalarda edit.

### Required Inputs
- `mockup-1024`: `.ai/.png/home-1024/Linux  1024 - Home Page.png` · `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` *(zorunlu)*
- `mockup-1920`: `.ai/.png/home-1920/Linux - 1920 - Home.png` *(zorunlu)*
- `figma-png-alt`: `.ai/ui-design/reference/figma/png/` — aynı mockup'ların Figma export kopyaları (`.png`; çelişirse `.ai/.png/home-*` kazanır)
- `figma-raw`: `.ai/ui-design/reference/figma/raw/*.json` — ham node ölçüleri (tahmin yasak):
  `nodes-1024-1920.json` (ana ölçü kaynağı) · `images-1024-1920.json` (export image map) ·
  `page-*.json` (sayfa ağaçları) · `node-1047-15802.json` · `figma_1920_main.json` (reference kökünde)
- `figma-notes`: `.ai/ui-design/reference/figma/_extraction-notes.md` · `token-conflicts.md`
- `css-ssot`: `assets.coremusic.net/Css/` · `php-page`: `home.coremusic.net/pages/home.php` + `pages/components/*.php`
- `js-components`: `assets.coremusic.net/js/components/composites/{WidgetArea,MiniCard,Card,Modal,Progress,Hero}Component.js`
- `tokens`: `assets.coremusic.net/Css/01_Abstracts/a-{widget-grid,welcome-banner,layout-tokens,layout-tokens-1024,design}-tokens*.css`
- `bypass-auth`: ADR-008 + `.env` `TEST_MODE=true`
- `tier`: T07 (1024×600) / T17 (1920×1080)

**Otomatik referans toplama (önce çalıştır):**
```powershell
Get-ChildItem -Recurse assets.coremusic.net\Css -Filter *home*.css | % FullName
Get-ChildItem -Recurse assets.coremusic.net\Css\04_Components -Filter _*.css | % FullName
Get-ChildItem -Recurse home.coremusic.net\pages -Include *.php | % FullName
Get-ChildItem -Recurse assets.coremusic.net\js\components -Filter *Component.js | % FullName
Get-ChildItem -Recurse .ai\.png\home-1024, .ai\.png\home-1920 -Filter *.png | % FullName
Select-String -Path assets.coremusic.net\Css\01_Abstracts\*.css -Pattern '^\s*--cm-[a-z0-9-]+:' | % Line | Sort-Object -Unique
```

### Figma Data API — Adım Adım Ölçü Çekimi (raw JSON yetmezse)

> Token/anahtar **yalnız** `.ai/.env.figma` (`FIGMA_TOKEN` + `FIGMA_FILE_KEY`) — koda/repoya yazma (AGENTS §13.9 kural 3).

```powershell
# 0) Hazır betikler (önce bunları dene — raw JSON bunlardan üretildi)
.\figma-extract.ps1    # node/ölçü çıkarımı → .ai/ui-design/reference/figma/raw/
.\figma-tokens.ps1     # token çıkarımı

# 1) Ham API (betik yoksa / taze veri gerektiğinde)
$env FIGMA_TOKEN  = (Get-Content .ai\.env.figma) -match 'FIGMA_TOKEN=' -replace 'FIGMA_TOKEN=',''
$env FIGMA_FILE_KEY = (Get-Content .ai\.env.figma) -match 'FIGMA_FILE_KEY=' -replace 'FIGMA_FILE_KEY=',''
$H = @{ 'X-Figma-Token' = $env:FIGMA_TOKEN }

# 2) Dosya ağacı (file key = .env.figma'daki değer)
Invoke-RestMethod -Headers $H "https://api.figma.com/v1/files/$($env:FIGMA_FILE_KEY)?depth=2"

# 3) Belirli node ölçüleri (home page node ID'leri raw JSON'daki id'lerden)
Invoke-RestMethod -Headers $H `
  "https://api.figma.com/v1/files/$($env:FIGMA_FILE_KEY)/nodes?ids=1024,1920"

# 4) Mockup export'u (PNG/JPG referans tazeleme — scale=2 retina)
Invoke-RestMethod -Headers $H `
  "https://api.figma.com/v1/images/$($env:FIGMA_FILE_KEY)?ids=<NODE_IDS>&format=png&scale=2"
#    → dönen url'leri .ai/.png/home-1024|home-1920 altına kaydet (mevcut adlarla)
```

**Adım sırası:** `figma-extract.ps1` → raw JSON yetmezse `/nodes` API → ölçüyü raw JSON ile çapraz doğrula →
çelişirse **PNG mockup** kazanır → ancak ondan sonra kod edit'i.

**API referansları (web search ile doğrula):**
- `GET /v1/files/:key` — https://www.figma.com/developers/api#get-files-endpoint
- `GET /v1/files/:key/nodes` — https://www.figma.com/developers/api#get-file-nodes-endpoint
- `GET /v1/images/:key` — https://www.figma.com/developers/api#get-images-endpoint
- Auth header `X-Figma-Token` — https://www.figma.com/developers/api#auth-header

### Figma Verisi — Gömülü Özet (prompt tek başına yeter; ölç çelişmez)

**Node dizini** (`raw/nodes-1024-1920.json`, 11 node — home kapsamı):

| ID | Ad | W×H | x,y |
|---|---|---|---|
| 1639:10160 | Linux 1024 - Home Page | 1024×600 | 637,-339 |
| 1646:17727 | Player Info (1024) [dokunulmaz] | 392×131 | 669,-255 |
| 1639:9775 | Diiv2 Button (widget grid 1024) | 365×171 | 1264,-255 |
| 1639:9773 | Footer (1024) [dokunulmaz] | 1025×90 | 637,171 |
| 1639:9904 | Menu En Son Şarkılar (1024) | 354×138.5 | 669,-22.5 |
| 1639:9892 | Menu Oynatma Listesi (1024) | 353×138 | 1056,-22 |
| 1639:9910 | Sıradaki Şarkı (1024) | 186×109 | 1442,5 |
| 1639:9903 | Sıradaki Şarkılar (başlık, 1024) | 88×45 | 1443,-22 |
| 2831:10267 | Welcome Div (popup, 1024) | 1024×601 | 1751,-339 |
| 2831:13747 | Linux - 1920 - Home | 1920×1080 | 606,-589 |
| 2849:21489 | Player Info (1920) [dokunulmaz] | 469×184 | 659,-490 |
| 2850:21494 | Div2 Button (widget grid 1920) | 752×184.4 | 1679,-490 |
| 2849:21492 | Banner Div (welcome-banner 1920) | 491×184 | 1158,-490 |

**1024 Home — children (depth 2, frame 1639:10160):**
```
Romantic_Background_03 [1639:9770] 1024×600 @ 637,-339   (arka plan görseli)
Siyah Arkaplan Evekt  [1639:9771] 1024×601 @ 637,-339   (siyah overlay)
Ellipse 16            [1646:18007] 720×720 @ 859,-348    (dekoratif daire)
Playlist Status Div   [1639:9772] 506.2×198.2 @ 669,-255 (now-playing kartı gölge+bg = 469×184)
Footer                [1639:9773] 1025×90 @ 637,171      [DOKUNULMAZ]
Navbar                [1639:9774] 993×27 @ 653,-322      [DOKUNULMAZ]
Diiv2 Button          [1639:9775] 365×171 @ 1264,-255    (widget grid → 12 slot)
Player Info           [1646:17727] 392×131 @ 669,-255    [DOKUNULMAZ]
Menu Oynatma Listesi  [1639:9892] 353×138 @ 1056,-22     (Playlistler kolonu)
Menu En Son Şarkılar  [1639:9904] 354×138.5 @ 669,-22.5  (En Son kolonu)
Sıradaki Şarkı        [1639:9910] 186×109 @ 1442,5
Sıradaki Şarkılar     [1639:9903] 88×45 @ 1443,-22       (başlık)
```
**1024 kolon içi (§kanonik):** Menu başlık 124×30 · `Şarkılar Item Btn` **169×43** · 2 kolon × 2 satır arası boşluk: x+184/y+68 ·
`Tümünü Görüntüle Plist` butonu 169×43 @ (1240,73).
**Item Btn anatomy (169×43):** `Container` 169×43 · `Album Cover` **50×43** (sol) · `Frame 11/13` başlık (×31) · `Frame 12/14` sanatçı (×31) · `Sure` **28×9** @ sağ-alt.
**Playlist Status Div içi (now-playing, depth 3):** `background` 469×184 · `Albums Status Image` 168×152 @ +16,+16 ·
metin blokları x=+214: Şarkı Adı 195×17 · Albüm 147×17 · Sanatçı 95×17 · Yıldız 37×17 + 5×star 13×13 (14px pitch) ·
Bit rate 110×17 · Süre 168×17 · icon'lar 12-15px (music/cd/micro/bit-rate/time-machine/play) · `Progressbar` **246×9** ·
`Oynatma Listesi için Lutfen Tıklayınız ...` 210×14.
**Diiv2 Button içi (widget 1024, 365×171):** `Ses Kaynağı` 173×51 · `Hava Durumu` 172×51 · `Depolama Yöneticisi` 173×40 ·
`traih Saat Widet` 109×40 · `EEquaizer Quick Acess` 42×40 · `Dosya Yönetici Btn` 129×40 ·
`Youtube Music Btn`/`Youtube Btn`/`Deezer Btn` 42×40 · 4× kısayol `22/23` 42-50×40.

**1920 Home — children (depth 2, frame 2831:13747):**
```
Romantic_Background_03 [2831:13955] 1920×1080
Footer                [2831:13859] 1923×92 @ 606,399    [DOKUNULMAZ]
Navbar                [2831:13748] 993×27 @ 618,-572    [DOKUNULMAZ]
Player Info           [2849:21489] 469×184 @ 659,-490   [DOKUNULMAZ]
Div2 Button           [2850:21494] 752×184.4 @ 1679,-490 (widget grid → 20 slot)
Banner Div            [2849:21492] 491×184 @ 1158,-490  (welcome-banner)
En Son Dinlenen Şarkılar [2850:21627] 407×45 @ 659,-134  (liste başlığı)
Şarkılar Item Btn ×6  [2856:22290 / 2890:7922-7968] 169×43 — y=-220 ve y=-89, x=659/843/1027/1211/1395/1579
```
**1920 Item Btn:** `Album Cover` 50-51×43 · `Frame 15` başlık ×38 · `Frame 16` sanatçı ×29 · `Sure` 28×9.
**1920 Div2 Button içi:** `Hava Durumu` ×3 (172×54.9 + 169×184 sağ) · `Ses Kaynağı` 173×54.9 · `Depolama` 173×43 ·
`traih Saat` 109×43 · kısayollar 42-50×43 · `Dosya Yönetici` 129×43 · Youtube/Deezer 42×43.

**Welcome Div (popup, 2831:10267 → Frame 15 [2876:6439] 600×308):**
```
welcome back bulur [2831:10268] 1024×601   (backdrop katmanı)
Frame 15           [2876:6439] 600×308      (~%58.6 w — modal kart)
  Welcome Div      [2831:10269] 600×308
    coremusic-text 1 [2831:10278] 195×130   (logo/metin)
    "Hoş gelidn"   [2831:10280] 66×23       (başlık — mockup yazımı korunur)
    "Prenses Işıl Peri" [2831:10281] 118×25 (script alt-başlık)
    açıklama metni [2831:10279] 365×26       ("Sana özel seçilen melodiler... 💜")
    Group 23       [2876.../2831:10274] 105×25  (Başla butonu)
```

**Image export map (`raw/images-1024-1920.json` — node → S3 url):**
```json
{"err":null,"images":{
"1639:10160":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/721273e1-ad02-4551-bb3c-5096fb87be68",
"1646:17727":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/a3a18c36-e60c-4171-9d02-e78f33db0bd6",
"1639:9775":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/8c6eaf9c-ab2e-4757-bd98-e7cdb4d9a00e",
"1639:9773":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/568f54ef-8428-42b8-a22c-bb3da2f30f10",
"1639:9904":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/00d35d0b-5aff-453d-bff9-8aecf0d39f8a",
"1639:9892":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/e1e1dbe9-2ad3-47a4-b0b1-765545d4e574",
"1639:9910":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/af345e2f-1be7-475a-91f9-973cea658350",
"2831:10267":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/fb6b273c-6570-4319-af49-2adee0903609",
"2831:13747":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/6986abac-668c-42df-a25b-5cf46df00916",
"2849:21489":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/c9e5d791-7ef0-49c0-94f8-1a2c0877e2b5",
"2850:21494":"https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/a5e5cd5c-a119-4c1c-a598-a2baa27dc179"}}
```
(S3 url'leri 7 günde expire olur — taze render gerekirse `/v1/images/:key` ile yenile.)

**Derin stil/typography verisi** (fill, font, radius, effect — bu section'da özetlenmedi):
`.ai/ui-design/reference/figma/raw/nodes-1024-1920.json` (1.0 MB) · `figma_1920_main.json` (310 KB) ·
`page-*.json` (sayfa tam ağaçları) · `.ai/ui-design/tokens/tokens-{1024,1920,3840}.json`.
Ölçü bu gömülü özetle çelişirse **raw JSON → PNG** sırası geçerlidir.


### ASCII Reference
```
1024×600 (T07)                                1920×1080 (T17)
┌────────────────────────────────┐            ┌─────────────────────────────────────────┐
│ HEADER (nav)        [dokunulmaz]│            │ HEADER (nav)              [dokunulmaz]  │
├──────────────┬─────────────────┤            ├───────────────┬─────────────────────────┤
│ now-playing  │ widget grid     │            │ now-playing   │ welcome-banner         │
│ kartı        │ 12 slot         │            │ (detaylı)     │ (Hoş Geldin + stats)   │
├──────────────┴─────────────────┤            ├───────────────┴─────────────────────────┤
│ En Son Dinlenen (2×4)          │            │ widget grid 20 slot (§13.9)             │
│ Son Oluşturulan Playlist (2×4) │            ├─────────────────────────────────────────┤
│ Sıradaki Şarkılar (sağ kolon)  │            │ En Son Dinlenen (1×10) · Playlist (1×6) │
├────────────────────────────────┤            ├─────────────────────────────────────────┤
│ FOOTER/PLAYER   [dokunulmaz]   │            │ FOOTER/PLAYER [dokunulmaz]              │
└────────────────────────────────┘            └─────────────────────────────────────────┘
Popup (1024): .modal--welcome ~%70 w, backdrop blur+karanlık, plaj bg, [Başla] → localStorage cm_welcome_seen
Track btn / Playlist btn: etiket + tıklama eylemi + state'ler PNG/Figma ile birebir (oyun/yanlış aksion YASAK)
```

**Widget grid kanonik (§13.9):** `1024` → 12 slot · `1920` → 20 slot.

### Prompt Template
```json
{
  "task": "Rebuild CoreMusic Home Page components (excluding header/footer/player-info) pixel-matched to Figma mockups at 1024 and 1920; fix track btn & playlist btn; verify at 1024→3840 in 500px steps",
  "page": "01-home",
  "bem": ".home",
  "states": ["default", "hover", "focus-visible", "active", "loading"],
  "tokens": ["--cm-card", "--cm-btn", "--cm-widget-grid", "--cm-welcome-banner", "--cm-progress", "--color-primary"],
  "constraints": [
    "Max thinking 5000 token — kısa düşün, öz çıktı",
    "1024 ve 1920 asla karıştırılmaz; her breakpoint kendi mockup'ıyla ölçülür",
    "Vanilla JS ES6+ (ADR-001); ITCSS korunur; BEM; innerHTML yasak; inline style/script yasak (ADR-012)",
    "CSS yalnız assets.coremusic.net/Css/; YENİ DOSYA YOK — mevcut satırlarda edit",
    "Header, footer, _player-info, PlayerInfoComponent.js, player-info.php DOKUNULMAZ",
    "Ölçü/token yalnız PNG + Figma raw + 01_Abstracts; tahmin yasak",
    "Track btn & playlist btn: doğru eylem + PNG/Figma birebir",
    "Git akışı: uzman commit/push ETMEZ — orkestratör sinyali ile git pull alınır"
  ]
}
```

### Expected Output
```html
<section class="home" data-component="home" data-viewport="1024">
  <article class="home__now-playing home__now-playing--embedded">...</article>
  <aside class="home__widget-grid" data-slot-count="12"><!-- slot x N --></aside>
  <button class="home__track-btn" type="button" data-action="play">...</button>
  <div class="home__popup home__popup--welcome" data-component="modal" hidden>
    <button class="home__popup-cta btn btn--primary" type="button">Başla</button>
  </div>
</section>
```

### Validation
- [ ] 1024×600 screenshot → mockup birebir (12 slot, 2 kolon, Sıradaki, popup)
- [ ] 1920×1080 screenshot → mockup birebir (20 slot, welcome-banner, tek satır listeler)
- [ ] **Çok çözünürlük süpürmesi:** genişlik `1024, 1524, 1920, 2024, 2524, 3024, 3524, 3840` → her birinde screenshot; bozuk olanları düzelt + yeniden render (500px adım + 1920 zorunlu ara nokta)
- [ ] Track btn & playlist btn: tıklama doğru eylemi üretir, etiket/state PNG ile birebir
- [ ] Widget slot: 1024 → 12 · 1920 → 20
- [ ] `git diff` → header/footer/player-info değişikliği YOK; `git status` → yeni dosya YOK
- [ ] Ham hex/px yalnız 01_Abstracts; inline style/script yok; Console 0 hata; CSP violation yok
- [ ] BEM `block__element--modifier`; `innerHTML` yok
- [ ] WCAG 2.2 AA: kontrast ≥ 4.5:1; focus-visible `2px solid var(--color-primary)`
- [ ] Popup: Başla → localStorage → ikinci girişte görünmez
- [ ] Regression: Player Info 392×131 / 469×184 / 750×300 bozulmadı

---

## Git Akışı (SIRALAMA — ihlal = dur)

```text
1. Uzman kodu yazar + yerel doğrular. COMMIT/PUSH YAPMAZ (veya commit eder, push ETMEZ).
2. Uzman DURUR → orkestratöre: "değişiklikler hazır, git pull bekleniyor".
3. Orkestratör kullanıcıya sorar. Kullanıcı der ki: "git pull çalıştır" →
   orkestratör/uzman repo'da `git pull` ALIR (pull = yerel güncelleme sinyali).
4. Kullanıcı diğer server PC'de `git pull` çalıştırır ve "devam" der.
5. Uzman/devam aşaması: sunucuda KONTROL EDİR — 8 genişlik screenshot süpürmesi +
   Validation checklist. Hata varsa 2-3'e döner.
NOT: Kullanıcı sinyali olmadan push/pull/deploy ATLANMAZ.
```

---

## Dispatch (orkestratör → uzman, birebir)

```text
SEVİYE: Senior Frontend Developer (UI Designer, A3). Max thinking 5000 token. Kısa oku, kısa düşün, edit et.
ÖNCE: aaa.md → Senaryo + Required Inputs + 3 PNG oku. Okunamazsa DUR.

GÖREV: Ana Sayfa'yı (header/footer/player-info DIŞINDAKI her şey) 1024 ve 1920'de mockup'a birebir yap.
       Track btn & playlist btn'i düzelt. Hatalı render'ları düzelt, doğruları bırak.

ADIMLAR:
1. [Keşif] Toplama komutlarını çalıştır; 3 PNG + figma/png + raw JSON oku (ölçü uydurma yasak).
   Raw yetmezse Figma Data API adım adım (figma-extract.ps1 → /v1/files/:key/nodes → /v1/images/:key), token yalnız .ai/.env.figma.
2. [Ayıklama] Her bloğu kendi mockup'ı ile karşılaştır (1024↔1024, 1920↔1920 — KARIŞTIRMA).
   DOĞRU = dokunma · HATALI = listele (dosya:satır + ne yanlış). 10 dk'da bitir.
3. [Edit] Sadece mevcut dosyalarda düzelt. ITCSS katmanına uygun CSS dosyası. Yeni dosya YOK.
   Inline style/script YOK. Ham hex/px yalnız 01_Abstracts.
4. [Butonlar] Track btn & playlist btn: etiket + aksiyon + hover/focus/active PNG/Figma birebir.
5. [Kural] Widget grid §13.9: 1024 → 12 · 1920 → 20 slot. Çelişkide kural PNG'ye precedanslı.
6. [Popup] 1024 welcome popup: mevcut c-modal.css/_welcome-banner.css; Başla → localStorage.
7. [Test] TEST_MODE=true. http://home.coremusic.net:81/home
   Genişlik süpürmesi: 1024, 1524, 1920, 2024, 2524, 3024, 3524, 3840 (yükseklik tier'e göre:
   600/1080/2160). Her genişlikte screenshot + DOM ölçü. Bozuk çıktı → düzelt → YENİDEN render.
   Console 0 hata; CSP violation YOK; Player Info regression yok.
   Cookie cm_viewport_w lag: resize ÖNCE navigate, gerekirse 2× yükle; cache takılıysa
   CDP Network.clearBrowserCache + ?v bump.
8. [Git gate] COMMIT/PUSH SONRASI DUR → "git pull bekleniyor" de. Kullanıcı "git pull çalıştır" der
   → pull al; kullanıcı diğer PC'de pull yapıp "devam" der → 8 genişlik screenshot ile KONTROL ET.
9. [Sonuç] 8 screenshot + düzeltme tablosu (dosya · satır · ne değişti · neden) + Validation
   checklist + git status (yeni dosya yok kanıtı).
```

---

## Otomatik Referanslar (toplanan)

**Görsel SSOT:**
1. `.ai/.png/home-1024/Linux  1024 - Home Page.png` — 1024 ana yerleşim
2. `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` — popup katmanı
3. `.ai/.png/home-1920/Linux - 1920 - Home.png` — 1920 ana yerleşim
3b. `.ai/ui-design/reference/figma/png/*.png` — Figma export kopyaları (aynı adlı .png/.jpg referanslar; çelişirse 1-3 kazanır)
3c. `.ai/ui-design/reference/figma/raw/{nodes-1024-1920,images-1024-1920,page-*}.json` + Figma REST API (`/v1/files`, `/nodes`, `/images` — token `.ai/.env.figma`)

**Vault:**
4. `.ai/ui-design/01-mockup-index.md` · `02-component-inventory.md` (C01-C16) · `00-device-matrix.md`
5. `.ai/ui-design/05-responsive-architecture.md` (§7.4 4K, §12 fallback)
6. `.ai/ui-design/reference/09-interaction-states.md` — buton state'leri (track/playlist btn buradan)
7. `.ai/.templates/ui-design/prompt-template.md` — Kalıp C (Guardrail #16)
8. `.ai/AGENTS.md` §13.9 — widget grid kanonik kuralı

**CSS/PHP/JS (mevcut — edit hedefi):**
9. `assets.coremusic.net/Css/05_Pages/_home{,-layout,-components,-inline}.css` · `_player.css`
10. `assets.coremusic.net/Css/04_Components/_{widget-grid,welcome-banner,player-info}.css` · `c-{modal,card,progress,home-song-btn}.css`
11. `assets.coremusic.net/Css/01_Abstracts/a-{widget-grid,welcome-banner,layout-tokens,layout-tokens-1024}-tokens*.css`
12. `assets.coremusic.net/Css/08_Devices/d-{embedded,desktop,laptop,tablet,4k}.css`
13. `home.coremusic.net/pages/home.php` · `pages/components/{welcome-banner,widget-grid,recent-tracks,player-info}.php`
14. `assets.coremusic.net/js/components/composites/{WidgetArea,MiniCard,Card,Modal,Progress,Hero}Component.js`

**Kararlar:**
15. ADR-001 framework yasak · ADR-008 bypass auth · ADR-012 CSP
16. `assets.coremusic.net/AGENTS.md` §CSS SSOT (BEM + ITCSS + token-first + inline yasak)
17. `.ai/ui-design/prompt/component/aaaa.md` — Player Info promptu (kapsam dışı)

**Harici (web search ile doğrula):**
18. MDN `repeat()` — https://developer.mozilla.org/en-US/docs/Web/CSS/repeat
19. MDN `backdrop-filter` — https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter
20. web.dev Strict CSP — https://web.dev/articles/strict-csp

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-01
**Mode:** Red Team · Human Mode · Truth Mode
