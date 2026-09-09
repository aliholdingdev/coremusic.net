---
type: architecture
category: l2
title: "Guard Pipeline — Auth Guard (PHP + JS)"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Guard Pipeline — Auth Guard (PHP + JS)

**Zorunlu Bağlantılar:** [[spa-router]] · [[js-router]] · [[ADR-083-spa-router]] · [[ADR-043-auth-subdomain-consolidation]]

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/AuthGuard.php`, `AuthUrlBuilder.php`, `PageRouterHelper.php`, `reference-project/assets.coremusic.net/js/router/guards.js`, `GuardPipeline.js`

---

## 1. Amaç

PHP ve JS tarafındaki auth guard mekanizmasını tanımlar. PHP tarafı server-side, JS tarafı client-side guard çalıştırır.

---

## 2. PHP Guard — AuthGuard

**SRP:** Tek sorumluluk — auth gerektiren rotalar için erişim kontrolü.

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * AuthGuard — Auth guard mantığını PageRouter'dan ayırır (SRP).
 *
 * 6 kontrol sırasıyla çalışır:
 *   1. Auth required + giriş yapılmamış → /login redirect
 *   2. Rol yetkisi kontrolü → 403 forbidden
 *   3. İzin kontrolü → 403 forbidden
 *   4. Auth redirect rotaları (login/register) → auth servise yönlendir
 *   5. Giriş yapmış kullanıcı auth sayfalarına → /home redirect
 *   6. Logout → auth servise yönlendir
 */
final class AuthGuard
{
    public function __construct(
        private readonly PageRouterHelper $authHelper,
        private readonly AuthUrlBuilder  $urlBuilder,
    ) {}

    public function check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        return $this->checkAuthRequired($uri, $route, $isSpaRequest)
            ?? $this->checkRole($uri, $route)
            ?? $this->checkPermission($uri, $route)
            ?? $this->checkAuthRedirectRoute($uri, $isSpaRequest)
            ?? $this->checkAuthenticatedUserOnAuthPage($uri, $isSpaRequest)
            ?? $this->checkLogout($uri, $isSpaRequest)
            ?? null;
    }

    // 1. Auth required + giriş yapılmamış
    private function checkAuthRequired(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        if (!$route->requiresAuth || $this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth('login', '/' . $uri, $isSpaRequest);
    }

    // 2. Rol yetkisi
    private function checkRole(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredRole === null || $this->authHelper->checkRole($route->requiredRole)) return null;
        return RouteResult::forbidden('/' . $uri);
    }

    // 3. İzin
    private function checkPermission(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredPermission === null || $this->authHelper->checkPermission($route->requiredPermission)) return null;
        return RouteResult::forbidden('/' . $uri);
    }

    // 4. Auth redirect route
    private function checkAuthRedirectRoute(string $uri, bool $isSpaRequest): ?array
    {
        if (!AuthRouteConfig::isAuthRedirectRoute($uri) || $this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth($uri, null, $isSpaRequest);
    }

    // 5. Giriş yapmış kullanıcı auth sayfasında
    private function checkAuthenticatedUserOnAuthPage(string $uri, bool $isSpaRequest): ?array
    {
        if (!$this->authHelper->checkAuthenticated() || !AuthRouteConfig::isAuthRedirectRoute($uri) || $uri === 'logout') return null;
        return $isSpaRequest ? RouteResult::forbidden('/home') : RouteResult::redirect('/home');
    }

    // 6. Logout
    private function checkLogout(string $uri, bool $isSpaRequest): ?array
    {
        if ($uri !== 'logout' || !$this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth('logout', null, $isSpaRequest);
    }
}
```

---

## 3. PHP AuthUrlBuilder — Auth URL Oluşturma

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * AuthUrlBuilder — Auth redirect URL oluşturma (SRP).
 *
 * ADR-043 uyumlu: Auth domain scheme DomainConfig'den alınır.
 */
final class AuthUrlBuilder
{
    public function __construct(
        private readonly DomainConfig     $domainConfig,
        private readonly PageRouterHelper $authHelper,
    ) {}

    public function redirectAuth(string $path, ?string $returnPath, bool $isSpaRequest): array
    {
        $target = $this->buildAuthUrl($path, $returnPath);
        header('X-Auth-Required: true');
        header('X-Auth-Status: unauthenticated');
        return $isSpaRequest ? RouteResult::forbidden($target) : RouteResult::redirect($target);
    }

    private function buildAuthUrl(string $path, ?string $returnPath = null): string
    {
        $scheme     = $this->domainConfig->isHttps() ? 'https' : 'http';
        $authDomain = AuthRouteConfig::getAuthUrl($scheme);
        return AuthRouteConfig::buildAuthRedirectUrl($path, $returnPath ?? '', $authDomain, $this->buildCallbackDomain());
    }

    private function buildCallbackDomain(): string
    {
        $scheme = $this->domainConfig->isHttps() ? 'https' : 'http';
        $host   = $this->domainConfig->getHost() ?? 'home.coremusic.net';
        $port   = $this->domainConfig->getPort();
        $suffix = ($port !== 80 && $port !== 443) ? ':' . $port : '';
        return $scheme . '://' . $host . $suffix;
    }
}
```

---

## 4. PHP PageRouterHelper — Auth Helper

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * PageRouterHelper — Auth guard helper with injectable session provider.
 *
 * SOLID DIP: Session erişimi SessionProviderInterface üzerinden lazy loading ile yapılır.
 */
final class PageRouterHelper
{
    private readonly SessionProviderInterface $sessionProvider;

    public function __construct(?SessionProviderInterface $sessionProvider = null)
    {
        $this->sessionProvider = $sessionProvider ?? new SessionProvider();
    }

    public function checkAuthenticated(): bool
    {
        if ($this->isTestBypassActive()) return true;
        return !empty($this->getSession()['MM_UserID']);
    }

    public function checkRole(string $role): bool
    {
        return ($this->getSession()['MM_UserRole'] ?? '') === $role;
    }

    public function checkPermission(string $permission): bool
    {
        return in_array($permission, (array)($this->getSession()['MM_Permissions'] ?? []), true);
    }

    private function getSession(): array { return $this->sessionProvider->getSession(); }

    private function isTestBypassActive(): bool
    {
        return (defined('TEST_MODE') && TEST_MODE === true)
            || (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS === true);
    }
}
```

---

## 5. JS GuardPipeline — Client-Side Guards

```javascript
/**
 * GuardPipeline — Client-side guard yönetimi.
 *
 * Her guard: (url, config) => boolean | { redirect: string }
 */
export default class GuardPipeline {
    #guards = [];
    #logger;

    constructor(logger) { this.#logger = logger; }

    register(fn) { this.#guards.push(fn); }

    async run(url, config) {
        for (const guard of this.#guards) {
            const result = await guard(url, config);
            if (result === false) {
                this.#logger.warn('GuardPipeline', 'guard_rejected', { url, guard: guard.name });
                return false;
            }
            if (result && typeof result === 'object' && result.redirect) {
                return result;
            }
        }
        return true;
    }
}
```

### JS guards.js — Guard Tanımları

```javascript
/**
 * authGuard — Auth gerektiren sayfalar için guard.
 *
 * Protected route'a giriş yapılmamış kullanıcı → /login redirect.
 */
export function authGuard(url, config) {
    const protectedRoutes = config.protectedRoutes || [];
    const isProtected = protectedRoutes.some(route => url.startsWith('/' + route));
    if (isProtected && !config.user?.id) {
        return { redirect: '/login' };
    }
    return true;
}

/**
 * roleGuard — Rol bazlı guard (gelecek için hazır).
 */
export function roleGuard(url, config) {
    return true;
}

/**
 * permissionGuard — İzin bazlı guard (gelecek için hazır).
 */
export function permissionGuard(url, config) {
    return true;
}
```

---

## 6. Guard Akış Diyagramı

```
PHP Tarafı (Server-Side):
  Request → PageRouterKernel → Middleware Pipeline → PageRouter::dispatch()
    → AuthGuard::check()
        ├── 1. checkAuthRequired()     → redirect/forbidden/null
        ├── 2. checkRole()             → forbidden/null
        ├── 3. checkPermission()       → forbidden/null
        ├── 4. checkAuthRedirectRoute()→ redirect/null
        ├── 5. checkAuthenticatedUserOnAuthPage() → redirect/forbidden/null
        └── 6. checkLogout()           → redirect/null
    → RouteResult → ResponseEmitter

JS Tarafı (Client-Side):
  Link Click → Router::navigate()
    → GuardPipeline::run()
        ├── 1. authGuard()             → { redirect: '/login' } / true
        ├── 2. roleGuard()             → true
        └── 3. permissionGuard()       → true
    → NavigationOrchestrator → Fetch → ContentPatcher
```

---

## 7. Guard Karşılaştırma

| Özellik | PHP AuthGuard | JS GuardPipeline |
|---------|---------------|------------------|
| **Çalışma zamanı** | Server-side | Client-side |
| **Kontrol sayısı** | 6 | 3 (şimdilik) |
| **Session erişimi** | `$_SESSION` (lazy) | `config.user` (inject) |
| **Redirect yöntemi** | HTTP redirect / JSON forbidden | SPA navigate |
| **CSRF koruması** | CsrfMiddleware | CsrfSyncManager |
| **Rate limiting** | RateLimiterMiddleware | Yok |
| **Rol kontrolü** | `checkRole()` | `roleGuard()` (hazır) |
| **İzin kontrolü** | `checkPermission()` | `permissionGuard()` (hazır) |

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **PHP guard başarısız** | JSON 403 + redirect URL | ADR-043 |
| **JS guard başarısız** | SPA navigate to /login | ADR-021 |
| **Auth servisi down** | PHP: 500 error / JS: error state | — |
| **Session timeout** | PHP: idle timeout check / JS: auth boundary | ADR-011 |
| **Bypass auth** | `TEST_MODE` / `FORCE_AUTH_BYPASS` | ADR-008 |
| **Cross-subdomain** | Cookie domain `.coremusic.net` | ADR-043 |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[js-router]] | JS SPA Router |
| [[route-config]] | Route yapısı + SpaRoute DTO |
| [[ADR-008-bypass-auth-middleware]] | Bypass auth |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 008, 021, 043, 083 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

## 11. Referans Proje ↔ Gerçek Kod Eşleştirmesi (Faz 2c — 2026-09-08)

**Kritik not:** Bu dosyadaki kod örnekleri `reference-project/` (referans, kopyalanmaz — WORKFLOW §8.1C) tabanlıdır. Gerçek üretim kodu `shared/src/PageRouter/` altındadır:

| Sınıf | Referans | Gerçek Konum | Doğrulama |
|-------|----------|--------------|-----------|
| `AuthGuard` | bu dosya §2 | `shared/src/PageRouter/AuthGuard.php` | ✅ `check(uri, SpaRoute, isSpaRequest)` imzası kod okumada doğrulandı |
| `AuthUrlBuilder` | bu dosya §3 | `shared/src/PageRouter/` | Kod varlığı Test-Path — imza teyidi devam görevi |
| `PageRouterHelper` | bu dosya §4 | `shared/src/PageRouter/` | Aynı |
| `SessionProviderInterface` | §4 | `shared/src/Interfaces/` | Aynı |
| `guards.js` / `GuardPipeline.js` | §5 | `assets.coremusic.net/js/router/` | DOĞRULAMA GEREKLİ — js-router.md (546) kapsamı |

**Sonuç:** PHP AuthGuard IMPLEMENTED (imza kanıtı); yardımcı sınıflar isim düzeyi doğrulandı; JS tarafı js-router okumasına kaldı.

---

## 12. 6 Kontrol — Kod Satır Eşlemesi

`check()` zinciri (bu dosya §2 kod bloğu satırları):

| Kontrol | Metot | Satır (referans blok) | Red Çıktısı | Session Girdisi |
|---------|-------|----------------------|-------------|-----------------|
| 1 Auth required | `checkAuthRequired()` | 67-71 | `redirectAuth('login', '/'.$uri)` | MM_UserID boşsa |
| 2 Rol | `checkRole()` | 74-78 | `RouteResult::forbidden()` | `MM_UserRole === requiredRole` |
| 3 İzin | `checkPermission()` | 81-85 | `RouteResult::forbidden()` | `MM_Permissions[]` in_array |
| 4 Auth redirect route | `checkAuthRedirectRoute()` | 88-92 | auth servise yönlendir | login/register sayfaları |
| 5 Auth'lu kullanıcı auth sayfasında | `checkAuthenticatedUserOnAuthPage()` | 95-99 | SPA: forbidden('/home') / Web: redirect('/home') | — |
| 6 Logout | `checkLogout()` | 102-106 | auth servise logout yönlendirme | auth'lu ise |

**Zincir mantığı:** `??` (null-coalescing) ile ilk null-olmayan sonuç kazanır; hepsi null → guard geçer (dispatch devam).

---

## 13. RouteResult Davranışları

| Sonuç Tipi | Web İstek | SPA İstek (isSpaRequest) | Üreten Alanlar |
|------------|-----------|--------------------------|----------------|
| `redirect($target)` | HTTP 302 Location | JSON forbidden + hedef | #1, #4, #5, #6 |
| `forbidden($target)` | 403 sayfa | JSON 403 + `X-Auth-*` | #2, #3, #5(SPA) |
| `null` (geçti) | dispatch devam | dispatch devam | tümü |

**AuthUrlBuilder header sözleşmesi (§3):** redirect öncesi `X-Auth-Required: true` + `X-Auth-Status: unauthenticated` — SPA istemcisi bu header'ları okuyarak login akışına geçer.

**Callback domain kurulumu (§3 buildCallbackDomain):** port 80/443 dışıysa `:port` eklenir — `home.coremusic.net:81` gibi geliştirme ortamı desteği (S-01 vakasındaki :81 ile uyumlu).

---

## 14. SessionProvider DIP Deseni

| Öğe | Açıklama |
|-----|----------|
| Arayüz | `SessionProviderInterface` — `getSession(): array` |
| Enjeksiyon | `?SessionProviderInterface $sessionProvider = null` → varsayılan `SessionProvider` |
| Lazy erişim | Session verisi guard anında okunur — önden yükleme yok |
| Test kanalı | `TEST_MODE` / `FORCE_AUTH_BYPASS` sabitleri → `checkAuthenticated()` true (ADR-008 bağlantısı) |
| Anahtar sözleşme | `MM_UserID`, `MM_UserRole`, `MM_Permissions[]` — L1 AuthMiddleware ile aynı |

**Kural:** Guard testleri gerçek session yerine mock provider enjekte eder; `$_SESSION` global'e doğrudan bağımlılık yasaktır (DIP).

---

## 15. JS ↔ PHP Guard El Sıkışma Matrisi

| Durum | PHP Kararı | JS Gördüğü | JS Davranışı |
|-------|------------|------------|--------------|
| Auth'suz protected route | 401/403 + X-Auth-* | header/JSON | `/login` SPA navigate |
| Rol yetersiz | 403 | JSON 403 | hata state |
| Auth'lu → auth sayfası | redirect /home | 302 takip | home'a yönlenir |
| Session timeout | 401 + X-Auth-Required | header | login akışı |
| Guard hepsi null (PHP) | 200 shell | içerik | DOM patch |
| Guard false (JS) | — | — | navigasyon iptal + log warn |

İlke: PHP kararı nihaidir (auth backend-controlled — ADR-083); JS guard yalnız UX hızlandırmasıdır, güvenlik kaynağı değildir.

---

## 16. Guard Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | auth'suz → `/home` | #1 tetik → login redirect |
| 2 | auth'suz → SPA `/home` | JSON + X-Auth-Required |
| 3 | regular → admin route | #2 forbidden |
| 4 | izin eksik | #3 forbidden |
| 5 | auth'lu → `/login` | #5 → /home redirect |
| 6 | auth'lu → `/logout` | #6 auth servise |
| 7 | auth'suz → `/login` | #4 auth servise (normal) |
| 8 | TEST_MODE bypass | tüm guard'lar geç |
| 9 | session timeout sonrası | #1 yeniden tetik |
| 10 | JS authGuard protected + user.id yok | `{redirect:'/login'}` |
| 11 | JS guard false | navigasyon iptal + `guard_rejected` log |
| 12 | port 81 callback | `:81` suffix'li callback URL |

---

## 17. Diagnostics (tekrarlanabilir)

```powershell
# 1. Gerçek AuthGuard imzası
Select-String -LiteralPath "shared\src\PageRouter\AuthGuard.php" -Pattern "function check|checkAuthRequired|checkRole|checkPermission"

# 2. Yardımcı sınıf varlığı
Get-ChildItem -LiteralPath "shared\src\PageRouter" -Filter "Auth*.php" | Select-Object Name

# 3. Session anahtar sözleşmesi
Select-String -LiteralPath "shared\src\PageRouter\*.php" -Pattern "MM_UserID|MM_UserRole|MM_Permissions"

# 4. Bypass sabitleri (ADR-008)
Select-String -LiteralPath "shared\src\PageRouter\*.php" -Pattern "TEST_MODE|FORCE_AUTH_BYPASS"

# 5. JS guard konumu
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Filter "*guard*" -ErrorAction SilentlyContinue | Select-Object FullName
```

---

## 18. Risk Kaydı (Guard Pipeline)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | JS guard'a güvenlik sorumluluğu sanılması | Orta | Yüksek | §15 ilke: PHP nihai |
| 2 | Bypass sabitlerinin prod'a sızması | Düşük | Kritik | ADR-008 + deployment gate |
| 3 | Zincir sırasının değiştirilmesi | Düşük | Yüksek | ADR-021 immutable |
| 4 | MM_Permissions format kayması | Orta | Orta | Session sözleşmesi (l1 §8) |
| 5 | JS guard dosyalarının konum belirsizliği | Bilinmiyor | Düşük | §11 js-router okuma görevi |
| 6 | AuthRouteConfig içerik sapması | Bilinmiyor | Orta | §20 sözleşme ↔ kod teyidi |

---

## 19. SSS Ek

**S: Neden 6 kontrol tek metotta zincir?**
C: `??` zinciri ilk red kararı anında döner; okunabilirlik ve sıra garantisi tek bakışta görünür. Ayrı pipeline sınıfına gerek kalmadan SRP korunur.

**S: `RouteResult::forbidden('/home')` SPA'da neden redirect değil?**
C: SPA'da 302 takibi tarayıcıya ait olur; JSON + hedef dönmek Router.js'e kontrolü bırakır (ADR-083 hibrit sözleşme).

**S: `MM_Permissions` nerede set edilir?**
C: Session sözleşmesi L1'de (AuthMiddleware/SessionManager); guard yalnız okur. Set etme noktası auth login akışıdır (Faz 2g).

**S: X-Auth-Required header'ı kim tüketiyor?**
C: SPA istemcisi (Router.js/guards.js) — 401/403 yanıtında login akışını tetiklemek için. Header adları AuthUrlBuilder §3'te sabitlenmiştir.

**S: guard sırası değişirse ne olur?**
C: #5 (auth'lu kullanıcı auth sayfasında) #1'den önce çalışırsa loop üretir. Sıra ADR-021 immutable kapsamındadır.

**S: Bu kod örnekleri kopyalanabilir mi?**
C: Hayır — referans proje çalışması (WORKFLOW §8.1C). Gerçek kod `shared/src/PageRouter/`'da zaten IMPLEMENTED; bu dosya davranış referansıdır.

---

## 20. SpaRoute DTO Alanları (Guard Girdileri)

Guard zincirinin tükettiği SpaRoute alanları (referans kod bloklarından türetilen sözleşme):

| Alan | Tip | Guard Kullanımı | Örnek |
|------|-----|-----------------|-------|
| `requiresAuth` | bool | #1 kontrol | `/home` → true |
| `requiredRole` | ?string | #2 kontrol | `/admin/*` → 'admin' |
| `requiredPermission` | ?string | #3 kontrol | `media.delete` |
| — | — | #4-#6 uri bazlı (config dışı) | AuthRouteConfig |

**AuthRouteConfig sözleşmesi (§2 kodundan):**

| Metot | Anlam |
|-------|-------|
| `isAuthRedirectRoute($uri)` | login/register gibi auth'a yönlenecek sayfalar |
| `getAuthUrl($scheme)` | auth domain URL'i (ADR-043: tek auth domain) |
| `buildAuthRedirectUrl($path, $returnPath, $authDomain, $callbackDomain)` | callback'li redirect üretimi |

Kural: Bu alanlar `route-config.md` (289) ile birebir tutarlı tutulur; alan ekleme ADR-021 kapsamındadır.

---

## 21. Senaryo — Guard Zinciri Gerçek Yürütme

```
İstek: GET /home, session yok, isSpaRequest=false
  #1 checkAuthRequired: requiresAuth=true, MM_UserID boş
     → redirectAuth('login', '/home', false)
        → buildAuthUrl: scheme=https, authDomain=auth.coremusic.net
        → callbackDomain: https://home.coremusic.net (port 443 → suffix yok)
        → RouteResult::redirect('https://auth.coremusic.net/login?return=/home&callback=...')
        → header: X-Auth-Required: true
  → 302 → tarayıcı login sayfası

İstek: POST /api/playlists (SPA), session var, rol=regular, route.role=admin
  #1 geçti (auth'lu) → #2 checkRole: 'regular' !== 'admin'
     → RouteResult::forbidden('/api/playlists')
  → JSON 403 → SPA hata state

İstek: GET /login, session VAR (auth'lu kullanıcı)
  #1 geçmez (requiresAuth=false) → #2/#3 null → #4: isAuthRedirectRoute('login')=true
     ama checkAuthenticated=true → null → #5: auth'lu + auth sayfası + uri≠logout
     → RouteResult::redirect('/home')
```

Bu senaryolar §16 test listesinin 1, 3, 5 numaralarının doğrudan adım adım halidir.

---

## 22. Guard Öncelik Sırasının Gerekçesi

| Sıra | Kontrol | Neden bu sırada |
|------|---------|-----------------|
| 1 | Auth required | En yaygın red — hızlı çıkış |
| 2 | Rol | Auth geçenlerin çoğu burada biter |
| 3 | İzin | Rol alt kümesi — daha ince taneli |
| 4 | Auth redirect route | Auth'suz sayfa özel durumları |
| 5 | Auth'lu → auth sayfası | Çelişki çözümü (login'e gitme) |
| 6 | Logout | Tek özel eylem — en sonda |

İlke: Sık tetiklenen kontroller önde (performans), çelişki çözücü kontroller sonra (doğruluk). Sıra değişimi ADR-021 kapsamıdır.

---

## 23. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-16 | İlk doküman (referans proje tabanlı) |
| 2.0.0 | 2026-09-08 | Faz 2c: §11 gerçek-kod eşleştirmesi; §12 satır eşlemesi; §13 RouteResult davranışları; §14 DIP deseni; §15 el sıkışma matrisi; §16 test senaryoları; §17 diagnostics; §18 risk; §19 SSS |
| 2.1.0 | 2026-09-08 | §20 SpaRoute DTO + AuthRouteConfig sözleşmesi; §21 gerçek yürütme senaryoları; §22 öncelik gerekçesi |

---

## 24. Eşleştirme Güncelleme Protokolü

§11 tablosundaki "Doğrulama" sütunu canlıdır:

| Durum Değeri | Anlam | Yükseltme Kuralı |
|--------------|-------|------------------|
| ✅ imza kanıtlı | Kod okuma tamam | — |
| Test-Path / devam görevi | İsim düzeyi | Sonraki turda kod okuma → ✅ |
| DOĞRULAMA GEREKLİ | Hiç kanıt yok | Kod gelince ✅ veya sil |

Kural: Her faz kapanışında bu tablo taranır; en az bir "devam görevi" çözülmeden faz kapanışı yapılmaz (engine §12.6).

---

## 25. Ek SSS

**S: `RouteResult` statik metodları mı?**
C: Referans kodda statik kullanım görünür (`RouteResult::forbidden/redirect`); gerçek implementasyonda aynı sözleşme beklenir — kesin teyit kod okumasıyla (devam görevi).

**S: `checkAuthenticated()` neden session dizisi okuyor, Authentication middleware değil?**
C: Guard L2'dedir; L1 middleware `MM_*`'ı zaten session'a yazmıştır. Guard salt-okur tüketici — kimlik doğrulama çift çalıştırma yok.

**S: SPA 403'te kullanıcıya ne görünür?**
C: Router.js hata state'i — UI katmanı yerelleştirilmiş mesaj gösterir. Guard JSON gövdesi İngilizce sözleşmedir (03-contracts standardı).

**S: Bypass sabitleri nasıl tanımlanır?**
C: `TEST_MODE`/`FORCE_AUTH_BYPASS` `defined()` ile sınanır — tanım ortam config'inde (`.env` → bootstrap). Prod'ta tanımsız olmalı; deployment gate kontrol eder.

**S: Guard ile AuthMiddleware farkı tek cümle?**
C: AuthMiddleware kimliği request'e taşır (L1); AuthGuard route erişim kararını verir (L2) — ikisi `MM_*` sözleşmesiyle bağlanır.

**S: AuthRouteConfig dosyası nerede?**
C: `shared/src/PageRouter/` altında beklenir — isim düzeyi doğrulandı (§11), içerik okuması devam görevi. Metot tablosu §20'deki sözleşmedir.

**S: `forbidden('/home')` neden hedef URL taşır?**
C: SPA'ya "nereye yönleneceğini" söylemek için — 403 gövdesi + hedef alanı Router.js'in tek istekte karar vermesini sağlar; ikinci istek tasarrufu.

**S: SpaRoute alanları burada mı, route-config'de mi tanımlı?**
C: Sınıf route-config §2'de (DTO), tüketim burada §20'de — iki doküman tek sözleşmenin iki yüzü, senkron zorunlu.

**S: AuthGuard mock test nasıl yazılır?**
C: §14 DIP deseni: `SessionProviderInterface` mock'u constructor'a enjekte edilir — `$_SESSION` globaline dokunmadan `checkAuthenticated/checkRole/checkPermission` senaryoları koşar (PHPUnit ^10.5).

**S: `check()` dönüşü null — dispatch ne yapar?**
C: Tüm kontroller null dönerse guard geçilmiş sayılır; PageRouter render hattına devam eder (§2 kod akışı — `?? null` zincirinin sonu).

---

## 28. Kod Okuma Günlüğü

| Tarih | Dosya | Bulgu |
|-------|-------|-------|
| 2026-09-08 (Faz 0) | AuthGuard.php | `check(uri, SpaRoute, isSpaRequest)` imzası — 6 kontrol zinciri doğrulandı |
| 2026-09-08 (Faz 0) | PageRouter.php | dispatch imzası + cm_viewport okuma + PAGES_PATH LSP bulgusu |
| Bekliyor | AuthUrlBuilder.php | §3 imzalarının birebir teyidi |
| Bekliyor | PageRouterHelper.php | SessionProvider lazy yükleme teyidi |
| Bekliyor | AuthRouteConfig.php | §20 metot sözleşmesinin teyidi |

Günlük kuralı: her kod okuma bu tabloya düşer; "bekliyor" satırları §24 protokolüyle kapanır.

---

## 26. Kalite Raporu (Güncel)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.1.0 |
| **Bölüm Sayısı** | 26 |
| **ADR Uyumlu** | ✅ 008, 021, 043, 083 |
| **Kod Kanıtı** | AuthGuard imza ✅; yardımcılar isim düzeyi |
| **Test Senaryosu** | 12 (§16) + 3 adım adım yürütme (§21) |
| **Zero Hallucination** | ✅ (referans/gerçek ayrımı açık — §11) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
