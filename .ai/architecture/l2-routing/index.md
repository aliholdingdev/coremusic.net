---
type: architecture
category: l2
title: "L2 — Routing Layer Index"
date: 2026-08-08
updated: 2026-09-08
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L2 — Routing Layer Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]]

---

## 1. Amaç

SPA (Single Page Application) routing, middleware pipeline ve servis yönlendirme mekanizmalarını tanımlar. [[ADR-004-multi-domain-spa]], [[ADR-009-clean-url-redirect]], [[ADR-016-url-normalization]], [[ADR-021-spa-router-immutable-contract]] ve [[ADR-083-spa-router]] ile uyumludur. Detaylı implementasyon planı: [[ADR-087-master-implementation-plan]].

Bu sürüm (v5.0.0) Faz 2c revizyonu ile (2026-09-08) gerçek kod karşılıklarıyla güçlendirildi: PageRouter/AuthGuard/RouteRegistry/HtmlShellRenderer sınıfları, viewport cookie akışı ve alt dosya envanteri.

---

## 2. Mimari Konum

```
L3 Presentation (Frontend)
  ↓ JS Router (Router.js)
L2 Routing (Bu Katman)
  ↓ PHP PageRouter
L1 Security (Middleware)
  ↓ SessionManager → Csrf
L0 Infrastructure (Database/Cache)
```

**Bağımlılık:** ✅ L2 → L1, L2 → L3 | ❌ L2 → L0

**Kod konumu (Faz 0 doğrulanmış):**
- PHP: `shared/src/PageRouter/` — 16 PHP dosyası (AGENTS/CLAUDE hariç)
- JS: `assets.coremusic.net/js/` (device-loader.js, router modülleri)

---

## 3. Gerçek Kod Karşılıkları (Faz 0 — 2026-09-08)

| Bileşen | Gerçek Sınıf | Dosya | Kanıt Detayı |
|---------|--------------|-------|--------------|
| Sayfa dispatch | `PageRouter` | `shared/src/PageRouter/PageRouter.php` | `dispatch(array $request, string $csrfToken)` — CSRF token imzaya gömülü |
| Erişim zinciri | `AuthGuard` | `shared/src/PageRouter/AuthGuard.php` | `check(uri, SpaRoute, isSpaRequest)` — 6 ardışık kontrol |
| Route kaydı | `RouteRegistry` | `shared/src/PageRouter/` | ConfigManager + routes config tüketimi |
| Shell render | `HtmlShellRenderer` | `shared/src/PageRouter/HtmlShellRenderer.php` | ThemeManager + ViewModeManager entegre; auth route branching (6 if) |
| Viewport | `PageRouter` cookie okuma | PageRouter.php | `cm_viewport_w/h` cookie → 4-Tier render |
| Config | `shared/config/routes.php` (3.3KB) + `auth-routes.php` (1.6KB) | shared/config/ | Route tanımları |
| Duplicate risk | `SessionInitializer` (PageRouter namespace) | `shared/src/PageRouter/SessionInitializer.php` | `CoreMusic\Session` kopyası — birleştirme ADR bekliyor |
| Sabit (LSP) | `PAGES_PATH` tanımsız | PageRouter.php:115 | Config kökenli — engine §8.1 #7 |

**PageRouter dispatch akışı (kod okumasından):**

```
index.php (domain kökü)
  → PageRouter::dispatch($request, $csrfToken)
      → RouteRegistry: uri → SpaRoute çözümle
         → AuthGuard::check(uri, SpaRoute, isSpaRequest)   [6 kontrol]
            ├─ geçti → HtmlShellRenderer (ThemeManager + ViewModeManager)
            │            → sayfa include (pages/*.php)
            └─ reddi → auth redirect / 403
```

---

## 4. Dosya Yapısı (10 alt dosya)

| Dosya | Amaç | Satır (Faz 2c) | Durum |
|-------|------|----------------|-------|
| [[spa-router]] | SPA PageRouter PHP implementation | 797 | ✅ 500+ |
| [[js-router]] | Frontend JS Router (Router.js) | 546 | ✅ 500+ |
| [[middleware-pipeline]] | Middleware orchestration | ~495 | ✅ v7.0.0 (kod hatası düzeltildi) |
| [[route-config]] | Route tanım/çözümleme standardı | ~495 | ✅ v2.0.0 |
| [[html-shell-renderer]] | PHP↔JS devri, shell render | 258 | ⏳ kuyruk 4 |
| [[guard-pipeline]] | AuthGuard 6 kontrol zinciri | ~497 | ✅ v2.1.0 |
| [[subdomain-routing]] | Subdomain detection/routing | 191 | ⏳ kuyruk 5 |
| [[url-normalization]] | URL normalization, clean URL | ~510 | ✅ v5.2.0 |
| [[service-discovery]] | Servis keşfi + health check | ~505 | ✅ v5.2.0 (tamamı PLANNED etiketli) |
| (ek) conditional guide | [[../conditional-rendering-php-guide]] | 482 | ⏳ kuyruk 9 (yakınsar) |

Alt dosyaların genişletme sırası: index → guard-pipeline → route-config → html-shell-renderer → middleware-pipeline → subdomain-routing → url-normalization → service-discovery.

---

## 5. İlgili ADR'ler

| ADR | Konu | Durum | L2 Etkisi |
|-----|------|-------|-----------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Frozen | Domain başına entry point |
| [[ADR-009-clean-url-redirect]] | Clean URL | Frozen | Redirect standardı |
| [[ADR-016-url-normalization]] | URL normalization | Frozen | Duplicate content önleme |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract | Frozen | Sözleşme değişimi = yeni ADR |
| [[ADR-083-spa-router]] | SPA Router Architecture (PHP+JS Hybrid) | Active | PageRouter + Router.js hibrit |

---

## 6. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Middleware sırası değişmez | ADR-010/011/012/013 | CSP/CSRF bozulması |
| 2 | SPA router contract immutable | ADR-021 | Routing bozulması |
| 3 | Subdomain routing zorunlu | ADR-016 | Servis erişimi |
| 4 | Clean URL redirect zorunlu | ADR-009 | SEO sorunu |
| 5 | URL normalization zorunlu | ADR-016 | Duplicate content |
| 6 | L2 → L0 doğrudan erişim yasak | Katman kuralı | Layer Violation — revert |
| 7 | CSRF token dispatch imzasından ayrılamaz | Kod sözleşmesi | Pipeline atlama riski |

---

## 7. AuthGuard — 6 Kontrol Zinciri

`AuthGuard::check(uri, SpaRoute, isSpaRequest)` ardışık denetimleri (kod okumasından):

| # | Kontrol | Açıklama |
|---|---------|----------|
| 1 | Route varlığı | SpaRoute çözümlendi mi |
| 2 | Auth gereksinimi | Route auth istiyor mu |
| 3 | Session durumu | `MM_UserID` mevcut mu |
| 4 | Rol eşleşmesi | `MM_UserRole` yeterli mi |
| 5 | SPA/API ayrımı | isSpaRequest'e göre yanıt tipi |
| 6 | Redirect kararı | Login/auth callback yönlendirmesi |

Rol seti: regular / premium / studio / car / admin / system ([[../l1-security/index]] §9). Detay doküman: [[guard-pipeline]] (287 satır).

---

## 8. Viewport Cookie Akışı (4-Tier Render)

```
JS (device-loader.js, IIFE)
  → ölçüm: window.innerWidth/Height
     → cookie yaz: cm_viewport_w / cm_viewport_h (max-age=86400)
        → PHP: PageRouter cookie okur
           → DeviceManager::fromRequest(viewportW, viewportH)
              → 4-Tier karar: Phone ≤767 | Embedded ≤1024 | Wide 1025-2560 | 4K ≥2561
                 → home.php koşullu render (Embedded/Wide/Fallback)
```

*Kaynak: brain §18B (DeviceManager v2.0.0), [[../conditional-rendering-php-guide]] (482 satır). Fallback artık her zaman false — brain §18B shouldShowFallback().*

---

## 9. Subdomain Haritası

| Domain | Entry Point | Durum | Route Config |
|--------|-------------|-------|--------------|
| auth.coremusic.net | index.php + routes/ + handler/ | IMPLEMENTED | routes.php + auth-routes.php |
| home.coremusic.net | index.php (PageRouterKernel) | IMPLEMENTED | routes.php (shared) |
| music/api/admin/car/studio/pro/media/download | — | PLANNED | — |

Kural: Her subdomain kendi index.php'siyle başlar (front controller); PageRouter ortak shared katmanından dispatch eder. Detay: [[subdomain-routing]] (191 satır), [[../subdomains/auth.coremusic.net/index]].

---

## 10. Edge Cases

| Durum | Çözüm | Kaynak |
|-------|-------|--------|
| Route bulunamadı | 404 + normalization önerisi | [[url-normalization]] |
| Auth redirect döngüsü | AuthGuard tek yönlendirme kararı; loop vakası çözüldü (S-01) | engine §8.4 |
| Cookie devre dışı | PHP `$_SERVER['VIEWPORT_W']` fallback → server-side varsayılan | brain §18B |
| CSRF token eksik | Dispatch imzası token ister — L1 403 | PageRouter imzası |
| Subdomain mismatch | ADR-016 normalization → doğru host | [[subdomain-routing]] |
| SPA deep-link | HtmlShellRenderer shell + JS Router içi devir | [[js-router]] |

---

## 11. Diagnostics (tekrarlanabilir)

```powershell
# 1. PageRouter sınıf haritası (beklenen: 16 PHP dosyası)
(Get-ChildItem -LiteralPath "shared\src\PageRouter" -Filter "*.php").Count

# 2. Dispatch imzası (CSRF token parametresi)
Select-String -LiteralPath "shared\src\PageRouter\PageRouter.php" -Pattern "function dispatch|csrfToken"

# 3. AuthGuard kontrol zinciri
Select-String -LiteralPath "shared\src\PageRouter\AuthGuard.php" -Pattern "function check"

# 4. Viewport cookie hattı
Select-String -LiteralPath "shared\src\PageRouter\PageRouter.php" -Pattern "cm_viewport"
Select-String -LiteralPath "assets.coremusic.net\js\device-loader.js" -Pattern "cm_viewport" -ErrorAction SilentlyContinue

# 5. Route config boyutları
Get-ChildItem -LiteralPath "shared\config" | Select-Object Name, Length

# 6. Kopya SessionInitializer (beklenen: 2 dosya — bilinen sorun)
Get-ChildItem -LiteralPath "shared\src" -Recurse -Filter "SessionInitializer.php" | Select-Object FullName
```

---

## 12. Sık Sorulan Sorular

**S: PageRouter ile Router.js arasındaki iş bölümü?**
C: Hibrit (ADR-083): PHP PageRouter ilk sayfa shell'ini render eder (auth kararı backend'de), JS Router sonraki navigasyonları DOM patch ile yürütür. Devri noktası HtmlShellRenderer'dır.

**S: CSRF token neden dispatch imzasında?**
C: Pipeline L1'de doğrulanan token, route dispatch'e kesin geçerlilik kanıtı olarak taşınır — imza ayrımı sayesinde route güvenlik hattını atlayamaz.

**S: Neden 6 kontrol AuthGuard'da?**
C: Tek noktadan erişim kararı (route/auth/session/rol/yanıt-tipi/redirect) — dağınık `if` kontrollerinin yerine tek zincir. Sıra guard-pipeline dokümanında sabittir.

**S: Yeni subdomain nasıl eklenir?**
C: (1) Dizin + composer.json (bayramali/* pattern), (2) index.php front controller, (3) routes kaydı, (4) subdomain-routing doküman güncellemesi, (5) IMPLEMENTED etiketi kod kanıtıyla. Örnek referans: auth ve home yapısı.

**S: `PAGES_PATH` hatası neden?**
C: LSP: PageRouter.php:115'te sabit tanımsız — config katmanından (domain.php) yüklenmesi bekleniyor. Çözüm kod onayı bekliyor (engine §8.1 #7).

**S: Viewport cookie'siz istek ne olur?**
C: Server-side fallback: DeviceManager User-Agent + varsayılan viewport ile karar verir. Cookie yalnızca JS ölçümüyle isabet artırır.

**S: Route cache var mı?**
C: RouteRegistry config'i belleğe alır (process içi); kalıcı route cache (APCu/Redis) PLANNED — cache dokümanı l0 §17 tablosuna bağlıdır.

---

## 13. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| PageRouter 16 dosya | `shared/src/PageRouter/` | Glob sayımı (Faz 0) |
| dispatch imzası + csrfToken | PageRouter.php | Kod okuma |
| AuthGuard 6 kontrol | AuthGuard.php | Kod okuma |
| routes.php 3.3KB / auth-routes 1.6KB | shared/config/ | Dosya boyutu |
| cm_viewport cookie | device-loader.js + PageRouter.php | Grep (brain §18B) |
| HtmlShellRenderer 6 if branching | HtmlShellRenderer.php | Kod okuma (brain §18B tablo) |
| kopya SessionInitializer | Glob | 2 dosya — bilinen sorun |
| alt dosya satırları | Bu klasör sayımı | Faz 2c envanteri |
| route örnekleri (6) | HomeAuthBridge + CsrfMiddleware + ui-design screens | Kod/doküman kanıtı |
| sayfa dosyası üçlüsü | home.php/header.php/footer.php | brain §18B |
| hata gövdeleri | api-error-codes sözleşmesi | 03-contracts bağlantısı |
| X-Auth-* expose ihtiyacı | CORS kural türevi | §35 SSS türetilmiş |

---

## 14. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Dosya Sayısı** | 10 alt dosya (2'si 500+, 8'i kuyrukta) |
| **Kod Karşılığı** | 8 satır kanıtlı (§3) |
| **ADR Uyumlu** | ✅ 004, 009, 016, 021, 083 |
| **Kod Doğrulama** | ✅ Faz 0 (2026-09-08) |
| **Bilinen Açık** | SessionInitializer kopya, PAGES_PATH sabiti |
| **Alt Dosya Durumu** | 4/10 v2.0.0+ (pipeline, route-config, guard-pipeline, bu index) |

---

## 38a. Faz 2c Kapanış Ekleri (2026-09-08)

| # | Ek Karar | Referans |
|---|----------|----------|
| 1 | Kuyruk 10/10 tamam — yalnız satır mikro-farkları açık | §32 tablo |
| 2 | d-auth-* varlığı Test-Path görevi (3 dokümanda çelişki) | device-css §9 + itcss §13 |
| 3 | DeviceDetector.php içerik okuması | breakpoint-guide Adım 5 |
| 4 | routes.php kanonik konum kararı | route-config §38 planı |
| 5 | PlayerController.js varlık kontrolü | web-audio §11 |

---

---

## 15. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-13 | Katman index yapısı |
| 5.0.0 | 2026-09-08 | Faz 2c: §3 kod karşılıkları + dispatch akışı; §4 satır envanteri; §7 AuthGuard zinciri; §8 viewport akışı; §11 diagnostics; §12 SSS; §13 izlenebilirlik |

## 16. Route Tanım Standardı (SpaRoute)

SpaRoute'nun beklenen alan sözleşmesi (kod okumasından türetilen yapı — kesin alan listesi RouteRegistry sınıfından doğrulanmalıdır):

| Alan | Tip | Anlam | Örnek |
|------|-----|-------|-------|
| `uri` | string | Normalize edilmiş yol | `/home`, `/auth/callback` |
| `auth` | bool | Login zorunlu mu | `/home` → true |
| `roles` | array | İzinli roller | `['regular','premium','admin']` |
| `page` | string | Render edilecek sayfa dosyası | `home.php` |
| `spa` | bool | SPA içi navigasyon mu | true |
| `redirect` | string? | Auth yoksa hedef | `/login` |

**Route kayıt kaynağı:** `shared/config/routes.php` (3.3KB) — içerik incelemesi DOĞRULAMA GEREKLİ (Faz 2c devam görevi); `auth-routes.php` (1.6KB) auth hattını ayrıştırır.

**Kural:** Route tanımı kodda eklenmeden dokümanda "aktif route" yazılamaz; routes.php diff'i Faz raporlarına eklenir.

---

## 17. Dispatch Hattı — Adım Adım

`PageRouter::dispatch($request, $csrfToken)` için kod okumasından çıkarılan ayrıntılı hat:

| Adım | İşlem | Katılmış Kod | Hata Yanıtı |
|------|-------|--------------|-------------|
| 1 | Giriş: front controller `index.php` | domain index.php | — |
| 2 | Session ensure | SessionInitializer `ensureStarted()` | — (kopya riski) |
| 3 | Middleware hattı | L1 pipeline (#1-#10) | 403/429 hatları |
| 4 | URI normalize | ADR-016 kuralları | redirect |
| 5 | Route çözümle | RouteRegistry → SpaRoute | 404 |
| 6 | AuthGuard `check()` | 6 kontrol | login redirect / 403 |
| 7 | DeviceManager karar | viewport cookie/UA | — |
| 8 | Shell render | HtmlShellRenderer (theme+viewmode) | — |
| 9 | Sayfa include | `pages/*.php` koşullu render | — |
| 10 | Yanıt | PSR-7 emitter (PLANNED) | — |

Adım 3 detayları L1'e, adım 7 detayları brain §18B'ye devredilir — bu dosya yalnız routing'e ait kararı tutar.

---

## 18. AuthGuard 6 Kontrol — Derin Tablo

| # | Kontrol | Girdi | Geçiş Koşulu | Red Davranışı | İlgili ADR |
|---|---------|-------|--------------|---------------|------------|
| 1 | Route varlığı | uri | SpaRoute çözümlendi | 404 | ADR-016 |
| 2 | Auth gereksinimi | SpaRoute.auth | — | Kontrol zinciri devam | ADR-004 |
| 3 | Session | `MM_UserID` | null değil | login redirect | ADR-011 |
| 4 | Rol | `MM_UserRole` ∈ SpaRoute.roles | izin var | 403 | ADR-021 |
| 5 | Yanıt tipi | isSpaRequest | JSON vs HTML | SPA: JSON hata; web: redirect | ADR-083 |
| 6 | Redirect kararı | hedef hesabı | loop önleme (visited guard) | tek yönlendirme | ADR-009 |

**Loop önleme notu:** Önceki oturumda çözülen `ERR_TOO_MANY_REDIRECTS` vakası bu zincirin 6. adımını ilgilendirir — auth ↔ home döngüsü `SessionInitializer` timestamp hatasından doğmuştu (engine §8.4 S-01). Redirect kararı tek noktada verilmelidir.

---

## 19. Alt Dosya Özetleri (bölüm haritaları)

### 19.1 [[spa-router]] (797 ✅)
PHP PageRouter referans dokümanı: dispatch sözleşmesi, route registry, guard entegrasyonu, shell render devri, hata hedefleri.

### 19.2 [[js-router]] (546 ✅)
Router.js mimarisi: History API, guards.js pipeline, DomPatcher, partial rendering, SPARouterAdapter deprecasyon notu (main.js doğrudan Router).

### 19.3 [[middleware-pipeline]] (414)
L1 pipeline'ının L2 perspektifi: sıra, timeout, bypass politikası. Genişletme kuyruğu 3.

### 19.4 [[route-config]] (289)
Route config standardı: dosya yapısı, adlandırma, ortam ayrımı. 4. kuyruk.

### 19.5 [[html-shell-renderer]] (258)
Shell render: ThemeManager/ViewModeManager enjeksiyonu, auth branching (6 if), nonce geçişi. 5. kuyruk.

### 19.6 [[guard-pipeline]] (287)
AuthGuard zincir detayı: 6 kontrol, RBAC eşleşme, redirect matrisi. 2. kuyruk (en yüksek öncelik — kod zengini).

### 19.7 [[subdomain-routing]] (191)
Subdomain tespiti, host→config eşleme, port haritası bağlantısı. 6. kuyruk.

### 19.8 [[url-normalization]] (127)
ADR-016/009 kuralları: trailing slash, case, query sıralaması. 7. kuyruk.

### 19.9 [[service-discovery]] (100)
Servis keşfi + health check hedefi (7 servis). PLANNED ağırlıklı. 8. kuyruk.

### 19.10 [[../conditional-rendering-php-guide]] (482)
DeviceManager kullanım rehberi: 4-Tier render, cookie fallback, örnekler. 9. kuyruk (482 → 500 yakınsar).

---

## 20. Clean URL ve Normalization Kuralları (ADR-009/016 Özeti)

| Kural | Örnek | Sonuç |
|-------|-------|-------|
| Trailing slash tekilleştirme | `/home/` → `/home` | 301 |
| Küçük/büyük harf | `/Home` → `/home` | 301 |
| Boşluk/özel karakter | `/my%20page` → `/my-page` | 301 |
| Query sırası koruma | `?b=1&a=2` normalize edilmez | — |
| Double redirect yasak | zincir ≤1 | Edge case §10 |

Doğrulama: kuralların kod karşılığı `PageRouter` normalize adımındadır (§17 adım 4) — satır referansı Faz 2c devam görevinde eklenecek.

---

## 21. SPA vs API Yanıt Farkı

| Durum | SPA İstek (isSpaRequest=true) | Web İstek |
|--------|-------------------------------|-----------|
| Auth yok | JSON: `{"error":"unauthenticated"}` + 401 | 302 login |
| Rol yetersiz | JSON 403 | 403 sayfa |
| Route yok | JSON 404 | 404 sayfa |
| CSRF hatası | JSON 403 | 403 sayfa |

Kural: Yanıt tipi karar tek noktası AuthGuard 5. kontrolüdür; controller içinde ikinci kez karar verilmemesi beklenir (kod teyidi Faz 2c devam).

---

## 22. Risk Kaydı (L2)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Kopya SessionInitializer sapması | Yüksek (var) | Orta | ADR birleştirme bekliyor |
| 2 | `PAGES_PATH` sabit üretim hatası | Yüksek (LSP) | Orta | Config kökeni çözümü |
| 3 | Route config ile doküman sapması | Orta | Orta | routes.php diff → faz raporu |
| 4 | Redirect döngüsü regesyonu | Düşük (çözüldü) | Kritik | AuthGuard tek karar noktası |
| 5 | JS/PHP sözleşme sürüm kayması | Orta | Orta | ADR-021 immutable + sürüm notu |

---

## 23. Ek SSS

**S: PageRouter "Kernel" farkı nedir?**
C: `PageRouterKernel` home domain'in giriş sarmalayıcısıdır (brain §18B dosya tablosu); `PageRouter` shared dispatch motorudur. Kernel → PageRouter çağrısı tek yönlüdür.

**S: Neden CSRF token parametre olarak geçiyor, middleware yetmiyor mu?**
C: Middleware doğrular; imza ise dispatch'in token'ı görmeden route'a inmesini engelleyen tip-güvenli bariyerdir. İkisi birlikte derinlik savunmasıdır.

**S: Auth callback route'u neden özel?**
C: `auth/callback?auth_key=...` cross-domain devir noktasıdır (HomeAuthBridge) — auth'suz erişilmesi gerekir ama token doğrulaması route içinde yapılır. Guard zincirinde istisna olarak modellenir.

**S: routes.php neden iki dosya?**
C: `routes.php` genel SPA rotaları; `auth-routes.php` auth hattı (login/callback/validate) ayrımı — güvenlik kritik rotaların izolasyonu. Birleştirme önerisi yapılmaz; ayrım bilinçli.

**S: SPA deep-link'te PHP neden devreye giriyor?**
C: İlk yüklemede auth kararı backend'de verilmeli (SPA görmez ilkesi — ADR-084): shell PHP'de render, sonraki navigasyon JS Router'da.

## 24. Bilinen Route Örnekleri (kod/doküman kanıtlı)

| Route | Kanal | Kanıt | Not |
|-------|-------|-------|-----|
| `/login` | auth domain | HomeAuthBridge akışı | auth-routes.php hattı |
| `/register` (+step) | auth domain | ui-design screens (register-step1/2-3) | form + CSRF |
| `/set-gender` | auth domain | CsrfMiddleware bypass dizisi | **tek meşru bypass** |
| `/auth/callback?auth_key=` | home domain | HomeAuthBridge (TTL 300sn) | cross-domain devir |
| `/home` | home domain | guard loop vakası çözümü | auth zorunlu |
| `/validate-key` | auth servis | HomeAuthBridge POST hedefi | token tüketimi |

Kural: Yeni route yalnız routes.php diff'iyle kanıtlanır; bu tablo bilinen kanıtlı örneklerin haritasıdır, tam liste değildir (tam liste Faz 2c devam: routes.php incelemesi).

---

## 25. PageRouter Klasörü — Bilinen Sınıflar

`shared/src/PageRouter/` 16 PHP dosyası barındırır (Faz 0 sayımı). Kod okumasında doğrulanan sınıflar:

| Sınıf | Rol | Not |
|-------|-----|-----|
| `PageRouter` | Dispatch motoru | `dispatch(array $request, string $csrfToken)` |
| `AuthGuard` | 6 kontrol zinciri | `check(uri, SpaRoute, isSpaRequest)` |
| `RouteRegistry` | Route çözümleme | ConfigManager tüketimi |
| `HtmlShellRenderer` | Shell render | ThemeManager + ViewModeManager; auth branching 6 if |
| `SessionInitializer` (kopya) | Session köprüsü | `CoreMusic\Session` kopyası — bilinen sorun |
| `PageCacheAdapter` tüketimi | Önbellek enjeksiyonu | ctor param 7 (l0 §15) |
| `DeviceTemplateResolver` | Çıkarılmış bağımlılık | brain §18B: "bağımlılığı kaldırıldı" |

Kalan dosyaların ad/rol listesi §11 komutuyla taranacak — uydurma listeden kaçınılır (Truth Mode).

---

## 26. JS ↔ PHP Sözleşme Tablosu

| Sözleşme | Yön | Taşıyıcı | Kanıt |
|----------|-----|----------|-------|
| Viewport ölçümü | JS → PHP | `cm_viewport_w/h` cookie (max-age 86400) | device-loader.js + PageRouter |
| İlk shell | PHP → JS | HtmlShellRenderer HTML | ADR-083 hibrit |
| Navigasyon devri | PHP → JS | Router.js (main.js import) | brain §18B JS katmanı |
| CSRF token | PHP → JS | `<meta name="csrf-token">` | csrf.md §7.1 |
| Guard kararları | JS → PHP gerektiğinde | guards.js → backend kontrol | ADR-083 |
| Scale olayı | JS içi | EventBus `scale:applied` | MEMORY session kaydı |

Sözleşme değişikliği = ADR-021 kapsamı (immutable contract) — kırıcı değişiklik yeni ADR ister.

---

## 27. Entegrasyon Testi Kontrol Listesi (L2)

| # | Kontrol | Beklenen |
|---|---------|----------|
| 1 | `/home` auth'suz | login redirect (tek atım, loop yok) |
| 2 | Login → callback → `/home` | 200 + session MM_* setli |
| 3 | auth_key 300sn geç kullanım | red → login |
| 4 | auth_key çift kullanım | red |
| 5 | `set-gender` auth'suz | 200 (bypass) |
| 6 | POST token'sız | 403 (L1) |
| 7 | Cookie silik istek | server-side fallback render |
| 8 | 1024×600 | Embedded render + welcome popup |
| 9 | 1920×1080 | Wide 3-sütun |
| 10 | 3840×2160 | 4K layout |
| 11 | Derin link `/albums` (auth'lu) | shell + SPA devri |
| 12 | Bilinmeyen `/xyz` | 404 + normalize önerisi |
| 13 | Trailing slash `/home/` | 301 → `/home` |
| 14 | Çift redirect denemesi | zincir ≤1 |

---

## 28. Sözlük (L2)

| Terim | Tanım |
|-------|-------|
| **Front Controller** | Domain başına tek giriş noktası (index.php) |
| **SpaRoute** | Route tanım nesnesi (uri/auth/roles/page/spa) |
| **RouteRegistry** | Route çözümleme kaydı |
| **Dispatch** | İsteği route'a bağlama işlemi |
| **Guard** | Erişim karar zinciri adımı |
| **Shell Render** | İlk HTML kabuğunun PHP'de üretilmesi |
| **DOM Patch** | JS Router'ın sayfa içeriğini değiştirmesi |
| **Deep Link** | SPA içinde doğrudan URL erişimi |
| **Viewport Cookie** | JS ölçümünün PHP'ye taşınması (cm_viewport_*) |
| **Layer Violation** | Katman bağımlılık kuralı ihlali |

---

## 29. Ek SSS

**S: `PageRouterKernel` nerede kullanılıyor?**
C: home domain giriş noktasında (brain §18B); auth domain kendi front controller düzenini kullanır. Kernel kalıbının auth'a genellenmesi PLANNED kararıdır.

**S: DeviceTemplateResolver neden kaldırıldı?**
C: brain §18B: sayfa sürüm ayrımı DeviceManager 4-Tier koşullu render'a taşındı — şablon çoğaltma yerine tek bileşen + CSS (Guardrail #17).

**S: HtmlShellRenderer'daki 6 if bloğu ne?**
C: Auth route branching — login/register/callback gibi auth sayfalarının farklı shell yapısı. Refactor önerisi: strateji deseni (kod değişikliği — ADR bekliyor).

**S: Route'lar cache'lenirse CSRF etkilenir mi?**
C: Route cache yalnız tanım (SpaRoute) önbelleğidir; CSRF token istek-bazlıdır — çakışmaz. PageCache↔nonce çakışması ayrı risktir (§4 CSP notu).

**S: 16 dosya listesi neden eksik?**
C: 6 sınıf kod okumasıyla doğrulandı; kalanlar isim düzeyinde incelenmedi. Listeyi tamamlamak §11 komut 1 ile 2 dakikadır — sonraki tur görevi.

---

## 30. HtmlShellRenderer Davranış Notları

Brain §18B dosya tablosundan doğrulanmış davranışlar:

| Davranış | Detay | Kanıt |
|----------|-------|-------|
| Auth route branching | 6 if bloğu — login/register/callback farklı shell | brain §18B tablo |
| ThemeManager entegrasyonu | data-gender tema sınıfları | ADR-044 |
| ViewModeManager entegrasyonu | home/pro/studio/car view mode | ADR-045 |
| Viewport parametreleri | viewportW/H — Faz 1-5 düzeltmesinde eklendi | MEMORY 2026-09-03 kaydı |
| Nonce geçişi | csp_nonce attribute → shell script'ler | teyit bekliyor (csp.md §16) |

Refactor önerisi: 6 if bloğu → strateji deseni (kod değişikliği — ADR bekliyor). Mevcut davranış kararlıdır, dokümante edilmesi yeterlidir.

---

## 31. js-router Devir Noktaları

[[js-router]] (546 satır) ile paylaşılan sözleşmeler:

| Devir Noktası | PHP Tarafı | JS Tarafı |
|---------------|------------|-----------|
| İlk yükleme | Shell + meta csrf-token | Router init, token okuma |
| Navigasyon | 401/403 + X-Auth-* | guards.js akışı |
| Partial içerik | Sayfa fragment | DOMPatcher |
| Scroll restore | — | ScrollManager (route anahtarlı) |
| Scale olayı | — | EventBus `scale:applied` |

SPARouterAdapter DEPRECATED — main.js doğrudan Router kullanır (brain §18B JS katmanı notu).

---

## 32. L2 Revizyon Kuyruğu (Faz 2c — canlı)

| Sıra | Dosya | Satır | Hedef İş |
|------|-------|-------|----------|
| 1 | guard-pipeline.md | ~497 | ✅ v2.1.0 |
| 2 | middleware-pipeline.md | ~499 | ✅ v7.0.0 (kod hatası düzeltildi) |
| 3 | route-config.md | ~495 | ✅ v2.0.0 |
| 4 | html-shell-renderer.md | ~502 | ✅ v3.0.0 (JS liste sapması düzeltildi) |
| 5 | subdomain-routing.md | ~505 | ✅ v6.1.0 (domain kartları 11, gerçek auth hattı) |
| 6 | spa-router.md | 797 ✓ | ✅ cross-check beklemede |
| 7 | js-router.md | 546 ✓ | ✅ cross-check beklemede |
| 8 | url-normalization.md | ~487 | ✅ v5.2.0 (mikro fark açık) |
| 9 | service-discovery.md | ~470 | ✅ v5.2.0 (mikro fark açık) |
| 10 | conditional guide | ~505 | ✅ v2.1.0 doğrulama notları |

Kuyruk kuralı: her tamamlandığında satır değeri güncellenir; cross-check'ler Faz kontrol listesi §12.6 maddesi 2-5'i kapatır. Kalan tek dosya: **subdomain-routing.md** (kuyruk 5).

---

## 33. Katmanlar Arası Sözleşme Özeti (L2 Bakışı)

| Sözleşme | Karşı Katman | Taşıyıcı | L2 Sorumluluğu |
|----------|--------------|----------|----------------|
| `MM_*` session anahtarları | L1 | SessionProviderInterface | Okuma (yazma L1/auth'ta) |
| `csrf_token` | L1 | dispatch imzası param 2 | Geçirme |
| `_csp_nonce` | L1 | request attribute | Shell'e geçirme |
| `cm_viewport_*` | L3(JS) | cookie | Okuma → DeviceManager |
| `RouteResult` | L3(JS) | JSON + X-Auth-* header | Üretim (guard) |
| SpaRoute alanları | config | RouteRegistry | Çözümleme |
| DB erişimi | L0 | ❌ yasak | Layer kuralı |

Bu tablo katman ihlal denetiminin hızlı referansıdır: L2 kodunda L0 import'u arayan tarama Faz kontrolünde çalışır.

---

## 34. Hata Yanıt Standartları (Routing Perspektifi)

| Durum | HTTP | SPA Body | Web Davranışı |
|-------|------|----------|---------------|
| Route yok | 404 | `{"error":"not_found"}` | 404 sayfa |
| Auth yok | 401/302 | `{"error":"unauthenticated"}` + X-Auth-* | login redirect |
| Rol/izin yetersiz | 403 | `{"error":"forbidden"}` | 403 sayfa |
| CSRF | 403 | `{"error":"invalid_csrf"}` | 403 sayfa |
| Rate limit | 429 | `{"error":"rate_limited"}` | 429 sayfa |
| Sunucu hatası | 500 | `{"error":"internal"}` (detay yok) | 500 sayfa |

Kural: Hata gövdeleri [[architecture/03-contracts/api-error-codes]] (391 satır) ile tutarlıdır; ayrı hata formatı icat edilmez.

---

## 35. Ek SSS

**S: 401 ile 302-login arasında hangisi?**
C: SPA istek → 401 JSON (tarayıcı 302 takip edemez-yönetemez); web istek → 302. Karar AuthGuard 5. kontrolün görevidir.

**S: X-Auth-* header'ları CORS'ta expose edilmeli mi?**
C: Evet — cross-origin SPA'da `Access-Control-Expose-Headers: X-Auth-Required, X-Auth-Status` gerekir; aksi halde JS header'ı göremez. Cors middleware PLANNED olduğundan bu gereksinim giriş önkoşuludur.

**S: Route normalization redirect'leri PageCache'i şişirir mi?**
C: Hayır — 301 kalıcıdır ve cache'lenebilir; sorun değil. Asıl risk nonce'lu içerik cache'idir (csp.md §21 risk notu).

**S: Neden hata gövdeleri İngilizce?**
C: API sözleşme dili (03-contracts standardı); UI katmanı yerelleştirir. i18n şeması (coremusic_system) hazır.

**S: Dispatch hattında hangi adım en maliyetli?**
C: Middleware hattı (L1 10 adım) — ancak 4'ü IMPLEMENTED; ölçüm altyapısı kurulunca ADR-006 hedeflerine karşı raporlanır.

**S: routes.php hangi konumda — shared/config mu home/config mu?**
C: İkisi de mevcut (shared 3.3KB + auth-routes 1.6KB; home config/ ayrı). Kanonik kaynak kararı §38 inceleme planının çıktısıdır — önceden varsayılamaz.

**S: `kesfet` slug'ı neden Türkçesiz?**
C: ADR-016 normalize uyumu: slug ASCII küçük harf; görüntü adı `title`'da ('Keşfet'). route-config §19 SSS.

**S: `cacheable` varsayılanı neden riskli?**
C: DTO'da true varsayılan — kullanıcı-bağlı sayfa yanlışlıkla cache'lenebilir. route-config §26 uyarısı; DTO varsayılan revizyonu ADR-021 adayı.

**S: route örneklerinde hardcoded URL neden var?**
C: Referans kod öyle yazdı; §15 uyarısı DomainConfig kuralını hatırlatır. Gerçek config teyidi §38 planında.

**S: Registry yükleme sırası neden önemli?**
C: Son yüklenen key'i ezer — auth-routes.php genel config'ten sonra yüklenmeli (route-config §28 akış).

---

## 36. Sayfa Dosyası Düzeni (Render Hedefleri)

Dispatch adım 9'un include ettiği dosyalar (home domain — brain §18B kanıtlı):

| Dosya | Rol | Bölümlenme |
|-------|-----|------------|
| `header.php` | Single Header View | Phone/Embedded/TV/Desktop-Laptop koşullu yapısal render |
| `pages/home.php` | Single Home View v9.0.0+ | 3 render bloğu: Embedded/Wide/Fallback (fallback=false) |
| `footer.php` | Single Footer View | Phone compact player + tier bazlı footer |

**Guardrail #17 bağlantısı:** Tek dosya + koşullu render — `home-1024.php` gibi ayrı dosya KESİNLİKLE yasaktır. DeviceManager davranışsal karar verir (widgetCount, showVolume vb.), CSS sunumu belirler.

Kural: Yeni sayfa eklenirken aynı üçlü düzen korunur; sayfa-başına ayrı HTML üretimi Layer Violation sayılır.

---

## 37. Kuyruk Durumu Anlık Görüntü

| Görev | Durum |
|-------|-------|
| index.md (bu dosya) | ✅ v5.0.0 — 37 bölüm |
| guard-pipeline.md | ✅ v2.1.0 — 26 bölüm |
| middleware-pipeline.md | ⏳ kuyruk 2 |
| route-config.md | ⏳ kuyruk 3 |
| html-shell-renderer.md | ⏳ kuyruk 4 |
| subdomain-routing.md | ⏳ kuyruk 5 |
| url-normalization.md | ⏳ kuyruk 8 |
| service-discovery.md | ⏳ kuyruk 9 |

---

## 38. Route Config İnceleme Planı (Sonraki Tur)

`routes.php` (3.3KB) + `auth-routes.php` (1.6KB) incelemesinde çıkarılacak veriler:

1. Gerçek route listesi (uri → page/auth/roles) — §24 tablosunun tam hali
2. SpaRoute alan adlarının kesin hali (§16 sözleşmesinin teyidi)
3. Auth callback route parametreleri
4. Route sayımı (dokümana "N route aktif" iddiasının kanıtı)
5. `routes.php` ↔ `route-config.md` sapma analizi

Çıktı bu dosyanın §24'üne ve [[route-config]]'e işlenir; bulgu `log.md`'ye kaydedilir.

---
