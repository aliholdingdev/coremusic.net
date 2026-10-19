---
title: "K011 Ünite-2 — Web Katmanı Bileşen Envanteri"
id: K011-U2-WEB-BILESENLAR
type: architecture
category: application
status: active
version: 1.0.0
source: "glob sayımı (shared/src, shared/tests, assets js, Css, .ai/.sql)"
verified: "2026-10-15 glob + grep sayımı (shell yok → araç sayımı)"
updated: "2026-10-15"
kodeme: kademe-2
module: K011-application
parent: web
group: mimari
---

# K011 Ünite-2 — Web Katmanı Bileşen Envanteri (mimari)

> **Kapsam:** web klasörünün bileşen/envanter dosyası. Sayılar bu oturumda
> araçla (glob/grep/read) ölçüldü; PowerShell kapı betiği **çalıştırılmadı**
> (shell `permission.rejected`) → betik burada metin olarak durur, orkestratör
> koşar. Havuz URL'leri bu dosyada YOK (tek kullanım yeri:
> `-web-arastirma.md`).

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §GRUPLAR

| # | Grup | Eksen | Kapsam | Kanıt / Dosya | Durum |
|---|------|-------|--------|---------------|-------|
| 1 | Envanter | dosya × sayı | PHP/JS/CSS/SQL sayım tablosu | glob+grep (2026-10-15) | ölçüldü |
| 2 | Sayım betiği | kapı × araç | PowerShell gate script | §B | belge (shell yok) |
| 3 | Bağımlılık grafiği | katman × akış | web isteği → response zinciri | §C | aktif |
| 4 | Middleware | backend × sıra | 11 ortak + 7 API middleware | §D | sayıldı |
| 5 | Frontend modülleri | JS × grup | 8 grup / 94 dosya | §E | sayıldı |
| 6 | Boşluklar | eksik × PLANNED | 5 madde (test 45/47, PCRT, …) | §F | açık |

---

## §A — ENVANTER TABLOSU (ölçümler, 2026-10-15)

| # | Alan | Desen | Ölçüm | Sonuç | Yöntem |
|---|------|-------|-------|-------|--------|
| 1 | `shared/src` PHP | `**/*.php` | strict_types isabeti | **165** | grep `declare(strict_types=1)` → Found 165 |
| 2 | `shared/tests` PHP | `**/*.php` | glob listesi | **45** | glob (43'ünde strict_types, 2 fixture hariç) |
| 3 | `assets.../js` JS | `**/*.js` | glob listesi | **94** | glob (limit 300, kırpılma yok) |
| 4 | `js/router/**` JS | alt klasör | glob listesi | **28** | glob (23 çekirdek + 5 `config/`) |
| 5 | `Css/01_Abstracts` | `*.css` | glob listesi | **19** | glob (token katmanı) |
| 6 | `.ai/.sql/mysql` | `*.sql` | önceki oturum ölçümü | 20 | ⚠️ bu turda tekrar sayılmadı |
| 7 | `web/index.md` | satır | read+split | 41 | önceki oturum ölçümü |

### §A.1 Çelişki kaydı

```text
Şartname (k011-kademe2-spec.md §1): "shared/tests 47 PHP"
Ölçüm (2026-10-15): 45 dosya
Fark: -2  →  ⚠️ VERIFICATION REQUIRED (uydurma sayı yok; iki değer de kayıtlı)
```

### §A.2 `shared/src` alt kırılımı (sınıf/interface/trait sayımı)

| Alt alan | Dosya | Not |
|----------|-------|-----|
| `Api/` (Bff, Middleware, Dto, Registry, Routing, Versioning, Auth) | 5 Bff + 7 Api\\Middleware + 11 DTO + 3 Registry + ... | sınıf imzası 164 isabet (bir dosya yalnız `declare`) |
| `Middleware/` (ortak) | 11 | §D.1 |
| `PageRouter/` | 14 | SPA sayfa router çekirdeği |
| `Contracts/` | 18 interface | DI sözleşmeleri |
| `Events/` (Domain 9 + Integration 3 + Dispatcher 2) | 14 | olay yayılımı |
| `OAuth/Provider/` | 13 (12 sağlayıcı + IOAuthProvider) | Base + 12 |
| `Component/` | 10 | PHP tarafı bileşen render |
| `Cache/`, `Log/`, `Security/`, `Session/`, `Config/`, `Database/`, `Device/`, `Theme/`, `ViewMode/`, `AI/`, `Repository/`, `Bootstrap/` | 6+3+6+4+4+3+4+1+1+7+1+1 | §A.2 altı satır toplamı |

> Kırılım satırları dosya adlarından okundu; **tam ara toplamlar** §B betiği
> ile tekrar üretilecek (bu tablo iddia, betik kanıt).

---

## §B — SAYIM KAPI BETİĞİ (PowerShell · repo kökünden · shell yok → belge)

```powershell
# K011 Ünite-2 web — envanter sayımı (gate A)
$root = 'C:\www\coremusic.net'
$items = [ordered]@{
  'shared/src *.php'    = @{ p = "$root\shared\src";    e = '*.php' }
  'shared/tests *.php'  = @{ p = "$root\shared\tests";  e = '*.php' }
  'assets js *.js'      = @{ p = "$root\assets.coremusic.net\js"; e = '*.js' }
  'assets 01_Abstracts' = @{ p = "$root\assets.coremusic.net\Css\01_Abstracts"; e = '*.css' }
  'sql *.sql'           = @{ p = "$root\.ai\.sql\mysql"; e = '*.sql' }
}
foreach ($k in $items.Keys) {
  $c = (Get-ChildItem -Path $items[$k].p -Filter $items[$k].e -Recurse -File).Count
  "{0,-24} = {1}" -f $k, $c
}
# Beklenen: 165 · 45 (şartname 47 → fark raporlanır) · 94 · 19 · 20

# Alt kırılım (js/router, Events, OAuth, Component)
(Get-ChildItem "$root\assets.coremusic.net\js\router" -Filter *.js -Recurse -File).Count   # 28
(Get-ChildItem "$root\shared\src\Events"  -Filter *.php -Recurse -File).Count               # 14
(Get-ChildItem "$root\shared\src\OAuth"   -Filter *.php -Recurse -File).Count               # 14
(Get-ChildItem "$root\shared\src\Component" -Filter *.php -Recurse -File).Count             # 10
(Get-ChildItem "$root\shared\src\PageRouter" -Filter *.php -Recurse -File).Count            # 14
(Get-ChildItem "$root\shared\src\Contracts"  -Filter *.php -Recurse -File).Count            # 18
(Get-ChildItem "$root\shared\src\Middleware" -Filter *.php -Recurse -File).Count            # 11
(Get-ChildItem "$root\shared\src\Api\Middleware" -Filter *.php -Recurse -File).Count        # 7

# Gate: satır + §GRUPLAR + footer + group son alan
$d = "$root\.ai\architecture\K011-application\web"
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0,-28} {1}" -f $_.Name, [System.IO.File]::ReadAllLines($_.FullName).Length
}
Select-String -Path "$d\*.md" -Pattern '^## §GRUPLAR$' | Measure-Object | Select-Object Count  # 6
Select-String -Path "$d\*.md" -Pattern 'Last Updated:\*\* 2026-10-15' | Measure-Object | Select-Object Count  # 6
Select-String -Path "$d\*.md" -Pattern '^group: ' | Group-Object { $_.Line } | Select-Object Count, Name
```

**Durum:** bu oturumda `permission.rejected` → betik KOŞULMADI; sayılar
glob/grep ile alındı, betik orkestratöre devredildi (aynı not
`web-dogrulama.md` §L ve `web-web-arastirma.md` §F).

---

## §C — BAĞIMLILIK GRAFİĞİ (web isteği → yanıt)

```text
[ Tarayıcı ]
    │  fetch (FetchWrapper: timeout 10s / retry 2 / CSRF header)
    ▼
[ assets.coremusic.net ]  ── statik: Css/**, js/**, Fonts/**, Image/**
    │                        (.htaccess / web.config cache + MIME)
    ▼
[ subdomain PHP entry ]  ── index.php (ör. api.coremusic.net/index.php L14-44)
    │   require autoload → config (constants/app/cors/routes)
    │   RuntimeBootstrap::boot → LoggerFactory
    ▼
[ Ortak middleware (shared/src/Middleware, 11 adet) ]
    SecurityHeaders (nonce+header) → Cors → OriginCheck → Csrf
    → Auth → BypassAuth → SessionManager → RateLimiter → Permission
    → Validation → MiddlewarePipeline (sıra: §D.1)
    ▼
[ PageRouter (14 dosya) ]  ── RequestNormalizer → RouteRegistry/SpaRoute
    │                          → AuthGuard → HtmlShellRenderer → ResponseEmitter
    ▼
[ API yolu: Gateway + Api\\Middleware (7) ]
    ApiMiddlewarePipeline → Authentication → Authorization → RateLimit
    → RequestValidation → ResponseNormalization (+ Validator)
    │
    ├── Bff katmanı: BffLayer + SpaBff/MobileBff/EmbeddedBff/DesktopBff
    ▼
[ Veri: DatabaseManager/Registry (PDO) ─ CoreMusic DB (MySQL) ]
    └── olaylar: EventDispatcher → Domain(9) + Integration(3) event

[ Frontend (js/, 94 dosya) ]
    main.js → device-loader (devices.config.js SSOT) → core/EventBus
    → router/ (28) : Router + GuardPipeline + ContentFetcher/DomPatcher
    → features|managers|components|coreplayer
    └── geri dönüş: EventBus.emit → UI güncellemesi
```

**Yön kuralı:** oklar tek yön (Presentation → Application → Domain →
Infrastructure); geri dönüş yalnız olay (EventDispatcher / EventBus) ile.

---

## §D — MIDDLEWARE ENVANTERİ

### D.1 Ortak middleware (`shared/src/Middleware` — 11 dosya)

| # | Dosya | Rol | Kanıt |
|---|-------|-----|-------|
| 1 | `SecurityHeadersMiddleware.php` | nonce + 9 başlık (+COOP/COEP) | sınıf L8, handle L14 |
| 2 | `CorsMiddleware.php` | CORS | sınıf L15 (grep) |
| 3 | `OriginCheckMiddleware.php` | origin doğrulama | sınıf L15 |
| 4 | `CsrfMiddleware.php` | CSRF token | sınıf L7 |
| 5 | `AuthMiddleware.php` | oturum auth | sınıf L7 |
| 6 | `BypassAuthMiddleware.php` | bypass (fail-closed test var) | sınıf L9 |
| 7 | `SessionManagerMiddleware.php` | session yaşam döngüsü | sınıf L9 |
| 8 | `RateLimiterMiddleware.php` | hız limiti | sınıf L9 |
| 9 | `PermissionMiddleware.php` | rol/izin | sınıf L15 |
| 10 | `ValidationMiddleware.php` | istek doğrulama | sınıf L15 |
| 11 | `MiddlewarePipeline.php` | zincir yürütücüsü | sınıf L8 |

### D.2 API middleware (`shared/src/Api/Middleware` — 7 dosya)

| # | Dosya | Rol |
|---|-------|-----|
| 1 | `ApiMiddlewarePipeline.php` | API zinciri (sıra: bu dosyada DEĞİL → ⚠️ §F.4) |
| 2 | `AuthenticationMiddleware.php` | API kimlik |
| 3 | `AuthorizationMiddleware.php` | API izin |
| 4 | `RateLimitMiddleware.php` | API rate limit |
| 5 | `RequestValidationMiddleware.php` | DTO doğrulama |
| 6 | `ResponseNormalizationMiddleware.php` | JSON sadeleştirme |
| 7 | `Validator.php` | kural motoru (yardımcı) |

**Sıra iddiası yok:** use-listesi alfabetiktir; gerçek yürütme sırası
`ApiMiddlewarePipeline` içinde → Ünite-3 görevi (PLANNED, §F.4).

---

## §E — FRONTEND MODÜL GRUPLARI (js/** = 94)

| # | Grup | Dosya | Öne çıkanlar |
|---|------|-------|--------------|
| 1 | `router/` | 28 | Router, GuardPipeline, FetchWrapper, CsrfSyncManager, DomPatcher, CacheLayer, PrefetchManager, MemoryWatchdog + `config/` (headers, error-types, signal-utils, css-selectors, auth-routes) |
| 2 | `components/` | 30 | base (5) + composites (14) + interactive (7) + primitives (8, yarısı `.spec.js`) |
| 3 | `core/` | 7 | CoreMusicApp, EventBus, helper, breakpoints (+spec), footer.init |
| 4 | `coreplayer/` | 5 | controls, progressbar, seekbar, shared, volume |
| 5 | `managers/` | 5 | Device, Scale, Sidebar, Theme, ViewMode |
| 6 | `features/` | 6 | Card, Player, Scroll, Touch, Widget, welcome-modal |
| 7 | `auth/` + `main.js` + loader/config | 5 | auth-gender-bg, gender-select, main, device-loader, device-layout-updater, devices.config |
| 8 | `oauth-manager.js` | 1 | token akışı (security-engineer denetimi, assets AGENTS §3) |

**Toplam kontrol:** 28+30+7+5+5+6+6+1 = **88** → kalan 6 dosya
(`js/router/config/*` 5'i zaten 28 içinde değil: 23+5=28; `managers` 5 …)
→ ⚠️ grup toplamı betikle tekrar doğrulanacak (§B), elle toplam
**iddia değildir**.

> Düzeltme notu: 94 = 28 (router) + 30 (components) + 7 (core) + 5 (coreplayer)
> + 5 (managers) + 6 (features) + 5 (auth/main/loader) + 1 (oauth-manager)
> + 7 (core kök artığı: breakpoints.spec, helper …) → gruplar betikle
> tekrar sayılacak; **hangi sayının SSOT olduğu belirsiz → ⚠️**.

---

## §F — BOŞLUKLAR (PLANNED / ⚠️)

| # | Boşluk | Tür | Ne dolacak |
|---|--------|-----|------------|
| F1 | `shared/tests` 45 vs şartname 47 | çelişki | Ünite-3: spec revizyonu veya 2 dosyanın kaybı araştırılır |
| F2 | js grup toplamı elle 88 ≠ 94 | sayım belirsiz | §B betiği koşulacak |
| F3 | `.sql` 20 sayımı bu turda taze değil | ölçüm yaşı | §B betiği |
| F4 | `ApiMiddlewarePipeline` çalışma sırası okunmadı | kanıt eksik | Ünite-3 (dosya: `shared/src/Api/Middleware/ApiMiddlewarePipeline.php`) |
| F5 | Windows/web.config cache kuralı okunmadı | kanıt eksik | Ünite-3 (`assets.coremusic.net/web.config`) |

---

## §G — KARDEŞ DOSYA BAĞLANTILARI (şema-yok, yolsuz)

| Hedef | Dosya | Ne alındı |
|-------|-------|-----------|
| kapsam | `web-kapsam.md` | §GRUPLAR eksenleri |
| doğrulama | `web-dogrulama.md` §H | test envanteri (45) |
| araştırma | `web-web-arastirma.md` §A | havuz URL listesi (okundu, kopyalanmadı) |
| örnekler | `web-ornekler.md` §K | 7 kanıtlı örnek |
| index | `web/index.md` | children güncellenecek (5 dosya) |

---

---

## §H — ITCSS KATMAN ENVANTERİ (Css/** · 2026-10-15 list_directory)

| # | Katman | Dosya | Sayı | Not |
|---|--------|-------|------|-----|
| 1 | `01_Abstracts` | token dosyaları (breakpoint, color-mode, colors, design, fonts, layout×6, light-glass, login, primitive, scale-hybrid, semantic, theme-config, welcome-banner, widget-grid) | **19** | sabit değer yalnız burada |
| 2 | `02_Base` | b-base-core, l-main-structural, page-layout | **3** | |
| 3 | `03_Layout` | _footer, _header, _sidebar, _widget-grid | **4** | |
| 4 | `04_Components` | c-badge, c-buttons, c-card, c-footer-seek, c-footer-volume, c-forms, c-home-song-btn, c-modal, c-progress, c-scrollbar-accent, c-toast, c-toggle, _home-components, _player-info, _welcome-banner | **15** | BEM blokları |
| 5 | `05_Pages` | p-album-detail, p-albums, p-artists, p-history, p-login-view, p-playlist, p-select-gender, p-settings, _home, _home-inline, _home-layout, _player, _welcome | **13** | ⚠️ assets AGENTS envanteri 12 diyor (`p-history` listelenmemiş) |
| 6 | `06_Utilities` | u-helpers-utility | **1** | |
| 7 | `07_Vendors` | v-bootstrap-lib + bootstrap* (css/map/min/rtl = 32 dosya) | **33** | salt okunur |
| 8 | `08_Devices` | 8 normal (4k, 4k-monitor, 4k-tv, desktop, embedded, laptop, phone, tablet) + 7 auth | **15** | |
| 9 | `09_ViewModes` | v-car, v-home, v-pro, v-studio | **4** | |
| 10 | `10_Helpers` | h-ellipsis | **1** | |
| 11 | `11_OAuth` | oauth | **1** | |
| | **Toplam** | | **109** | 19+3+4+15+13+1+33+15+4+1+1 |

### §H.1 Çelişki kaydı (envanter ≠ disk)

| # | Kaynak | İddia | Ölçüm | Karar |
|---|--------|-------|-------|-------|
| 1 | `assets.coremusic.net/AGENTS.md` §2 | `05_Pages` 12 dosya | 13 (`p-history.css` dahil) | ⚠️ — AGENTS envanteri güncellenmeli (ben düzeltmem, raporlarım) |
| 2 | `AGENTS.md` §2 notu | `a-layout-tokens copy*.css` 2 kopya artığı | bu turda listede YOK | diskte silinmiş görünüyor → ⚠️ (git status değil, glob) |
| 3 | `devices.config.js` L19-20 | `4k-tv`/`4k-monitor` → `d-4k.css` | `d-4k-tv.css` + `d-4k-monitor.css` da var | iki dosya HOME_CSS'te maplenmiyor → kullanımı Ünite-3 araştırır (id nokta) |

---

## §I — TEST ENVANTERİ (shared/tests/** = 45)

| # | Grup | Dosya sayısı | Test dosyaları |
|---|------|--------------|----------------|
| 1 | `Api/` | 8 | Versioning, RouteTable, ResponseNormalizationMiddleware, RequestValidationMiddleware, GatewayMethodRouting, Dto, ApiResponse, ApiPipelineSecurity |
| 2 | `Middleware/` | 9 | MiddlewarePipeline, Csrf, BypassAuth, BypassAuthFailClosed, Auth, RateLimiter, OriginCheck, SessionManager, Validation |
| 3 | `Unit/` | 16 | PageRouter×6 (SpaRoute, RouteRegistry, HtmlShellRendererGuard, HtmlShellRendererChrome, AuthGuard, AuthUrlBuilder), Device×4, Log×2, Security×2, Config×1, Theme×1 |
| 4 | `Security/` | 2 | SessionKeys, ReturnUrlPolicyEdgeCase |
| 5 | `Events/` | 2 | DomainEvent, EventDispatcher |
| 6 | `Repository/` | 4 | ApiKeyCreateCommand, ApiKeyRepository + Sqlite (2 yardımcı, adında Test yok) |
| 7 | `OAuth/` | 1 | OAuthManager |
| 8 | `Component/` | 1 | TemplateEngine |
| 9 | `Fixtures/` | 2 | chrome/header, chrome/footer (fixture, test değil) |
| | **Toplam** | **45** | 8+9+16+2+2+4+1+1+2 |

**Şartname farkı:** 47 (şartname) − 45 (ölçüldü) = 2 → §A.1 `⚠️`.

**Gözlem:** gerçek test = 43 (fixture 2 + yardımcı 2 hariç tutulursa 41) —
hangi sayının kapı sayıldığı şartnamede belirsiz → `⚠️` (uydurma yok).

---

## §J — SQL ŞEMA BAĞLANTISI (web katmanı ↔ veri)

| # | Tablo (`.ai/.sql/mysql/coremusic_auth.sql`) | Web katmanı kullanıcısı | Kanıt |
|---|----------------------------------------------|--------------------------|-------|
| 1 | `api_keys` (L56-79) | `bin/api-key-create.php`, `Repository/ApiKeyRepository` | şema okundu; PHP tarafı sınıfları glob ile var |
| 2 | `api_usage` (L81-90) | API rate-limit/audit | şema okundu |
| 3 | `admin_users` (L50-54) | rol/izin (PermissionMiddleware) | FK `users.id` CASCADE |
| 4 | `users` | Auth/Session | ⚠️ bu turda okunmadı (tablo başı 50. satırdan önce) |

**Yön:** SQL yalnızca veri katmanı; web katmanı PDO `DatabaseManager` üzerinden
erişir (bkz §C grafiği). SQL dosyasına kod örneği olarak **yazılmaz** →
yalnız şema referansı.

---

## §K — KAPI KONTROL LİSTESİ (bu dosya, bitişte)

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Satır | >= 500 | ☐ |
| 2 | `## §GRUPLAR` | 1 başlık, >= 6 satır | ✓ (6) |
| 3 | Footer + `Last Updated: 2026-10-15` | var | ✓ |
| 4 | frontmatter `group:` son alan | evet | ✓ (group: mimari) |
| 5 | Havuz-dışı tam URL | 0 | ☐ (bitişte grep) |
| 6 | Ölçüm sayısı (165/45/94/28/19) | betikle eşit | ☐ (§B orkestratör) |
| 7 | Çelişki kayıtları | 45/47, 12/13, 88/94 | ✓ §A.1, §H.1, §E |

---

---

## §L — shared/src ALT DİZİN SAYIMI (2026-10-15 · recursive list · yalnız `*.php`)

| Alt dizin | PHP |
|-----------|-----|
| `AI/` | 7 |
| `Api/` | 34 |
| `Bootstrap/` | 1 |
| `Cache/` | 6 |
| `Component/` | 10 |
| `Config/` | 4 |
| `Contracts/` | 19 |
| `Database/` | 3 |
| `Device/` | 4 |
| `Events/` | 14 |
| `Exception/` | 8 |
| `Log/` | 3 |
| `Middleware/` | 11 |
| `OAuth/` | 14 |
| `PageRouter/` | 14 |
| `Repository/` | 1 |
| `Security/` | 6 |
| `Session/` | 4 |
| `Theme/` | 1 |
| `ViewMode/` | 1 |
| **Toplam** | **165** |

**Kontrol:** alt dizin toplamı = grep `declare(strict_types=1)` isabeti (165) =
glob `**/*.php` (165) → üç yöntem uyuştu.

### §L.1 PHP olmayan dosyalar (belge katmanı)

```text
shared/src altında 38 adet CLAUDE.md (her alt dizinde 1) — katman belgeleri.
Sayım: 38 (list_directory; *.php sayımına girmez).
```

---

## §M — JS DOSYA LİSTESİ (assets.coremusic.net/js/** = 94 · glob listesi)

> Liste glob çıktısından kopyalandı (dosya adı = kanıt; **rol iddiası yok**).
> Roller okunarak Ünite-3'te yazılacak — bu tablo salt envanterdir.

| # | Dosya |
|---|-------|
| 1 | `main.js` |
| 2 | `device-loader.js` |
| 3 | `device-layout-updater.js` |
| 4 | `devices.config.js` |
| 5 | `oauth-manager.js` |
| 6 | `router/ContentFetcher.js` |
| 7 | `router/IRouter.js` |
| 8 | `router/guards.js` |
| 9 | `router/GuardPipeline.js` |
| 10 | `router/FocusManager.js` |
| 11 | `router/FetchWrapper.js` |
| 12 | `router/PrefetchManager.js` |
| 13 | `router/NavigationOrchestrator.js` |
| 14 | `router/NavigationGuardRunner.js` |
| 15 | `router/CacheLayer.js` |
| 16 | `router/MemoryWatchdog.js` |
| 17 | `router/UrlUtils.js` |
| 18 | `router/Logger.js` |
| 19 | `router/AuthBoundaryDetector.js` |
| 20 | `router/LifecycleManager.js` |
| 21 | `router/ContentPatcher.js` |
| 22 | `router/SPARouterAdapter.js` |
| 23 | `router/CsrfSyncManager.js` |
| 24 | `router/RouterEventManager.js` |
| 25 | `router/ScrollRestorer.js` |
| 26 | `router/ScriptInjector.js` |
| 27 | `router/Router.js` |
| 28 | `router/DomPatcher.js` |
| 29 | `router/config/css-selectors.js` |
| 30 | `router/config/auth-routes.js` |
| 31 | `router/config/error-types.js` |
| 32 | `router/config/headers.js` |
| 33 | `router/config/signal-utils.js` |
| 34 | `auth/gender-select.js` |
| 35 | `auth/auth-gender-bg.js` |
| 36 | `core/breakpoints.js` |
| 37 | `core/EventBus.js` |
| 38 | `core/CoreMusicApp.js` |
| 39 | `core/breakpoints.spec.js` |
| 40 | `core/footer.init.js` |
| 41 | `core/helper.js` |
| 42 | `coreplayer/coreplayer.seekbar.js` |
| 43 | `coreplayer/coreplayer.progressbar.js` |
| 44 | `coreplayer/coreplayer.controls.js` |
| 45 | `coreplayer/coreplayer.shared.js` |
| 46 | `coreplayer/coreplayer.volume.js` |
| 47 | `managers/SidebarManager.js` |
| 48 | `managers/ScaleManager.js` |
| 49 | `managers/DeviceManager.js` |
| 50 | `managers/ThemeManager.js` |
| 51 | `managers/ViewModeManager.js` |
| 52 | `features/ScrollManager.js` |
| 53 | `features/PlayerController.js` |
| 54 | `features/CardManager.js` |
| 55 | `features/welcome-modal.js` |
| 56 | `features/TouchManager.js` |
| 57 | `features/WidgetManager.js` |
| 58 | `components/composites/WidgetAreaComponent.js` |
| 59 | `components/composites/TooltipComponent.js` |
| 60 | `components/composites/ToastComponent.js` |
| 61 | `components/composites/TabsComponent.js` |
| 62 | `components/composites/QuickAppsComponent.js` |
| 63 | `components/composites/ProgressComponent.js` |
| 64 | `components/composites/NavLinkComponent.js` |
| 65 | `components/composites/ModalComponent.js` |
| 66 | `components/composites/MiniCardComponent.js` |
| 67 | `components/composites/HeroComponent.js` |
| 68 | `components/composites/DropdownComponent.js` |
| 69 | `components/composites/CardComponent.js` |
| 70 | `components/composites/AvatarComponent.js` |
| 71 | `components/primitives/ToggleComponent.spec.js` |
| 72 | `components/primitives/ToggleComponent.js` |
| 73 | `components/primitives/SliderComponent.spec.js` |
| 74 | `components/primitives/SliderComponent.js` |
| 75 | `components/primitives/SkeletonComponent.spec.js` |
| 76 | `components/primitives/SkeletonComponent.js` |
| 77 | `components/primitives/InputComponent.spec.js` |
| 78 | `components/primitives/InputComponent.js` |
| 79 | `components/primitives/ButtonComponent.spec.js` |
| 80 | `components/primitives/ButtonComponent.js` |
| 81 | `components/primitives/BadgeComponent.spec.js` |
| 82 | `components/primitives/BadgeComponent.js` |
| 83 | `components/interactive/ToastComponent.js` |
| 84 | `components/interactive/TabsComponent.js` |
| 85 | `components/interactive/PlayerInfoComponent.js` |
| 86 | `components/interactive/InfiniteScroll.js` |
| 87 | `components/interactive/HistoryComponent.js` |
| 88 | `components/interactive/DropdownComponent.js` |
| 89 | `components/interactive/AccordionComponent.js` |
| 90 | `components/base/ComponentRegistry.js` |
| 91 | `components/base/ComponentLoader.js` |
| 92 | `components/base/ComponentEvents.js` |
| 93 | `components/base/ComponentBase.js` |
| 94 | `components/base/ComponentAccessibility.js` |
| | **94** |

**Dağılım:** `router/` 28 · `components/` 30 · `core/` 7 · `coreplayer/` 5 ·
`managers/` 5 · `features/` 6 · kök+auth 6 · `oauth-manager` 1 · toplam 88+6
→ §E uyarındaki belirsizlik: kök dosya gruplaması elle yapıldığı için
`⚠️` (betik §B ile dosya bazında doğrulanacak).

---

## §N — OKUMA GÜNLÜĞÜ (bu oturumda hangi dosya, hangi satır okundu)

| # | Dosya | Satır | Kullanım |
|---|-------|-------|----------|
| 1 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 1-60 | `web-ornekler.md` §A, `web-dogrulama.md` §B |
| 2 | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 1-57 | `web-ornekler.md` §D |
| 3 | `assets.coremusic.net/js/core/EventBus.js` | 1-73 | `web-ornekler.md` §B |
| 4 | `assets.coremusic.net/js/router/FetchWrapper.js` | 1-60 | `web-ornekler.md` §C |
| 5 | `.ai/.sql/mysql/coremusic_auth.sql` | 50-90 | `web-ornekler.md` §E.3, §J |
| 6 | `assets.coremusic.net/js/devices.config.js` | 1-45 | `web-ornekler.md` §I |
| 7 | `api.coremusic.net/index.php` | 1-50 | `web-ornekler.md` §J |
| 8 | `web/web-kapsam.md`, `web/web-dogrulama.md` | tamam | kapı kontrolü (520, 502) |
| 9 | glob: `shared/src`, `shared/tests`, `js`, `01_Abstracts` | — | §A sayıları |
| 10 | list_directory: `Css/*` (11 katman), `shared/src` (20 dizin) | — | §H, §L |

**Okunmayanlar (kayıtlı):** `index.html` (viewport claim), `ApiMiddlewarePipeline`
(çalışma sırası), `web.config` (cache) → §F PLANNED.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
