---
node_id: K009-security/authorization/authorization-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/authorization/authorization-anayasa
  - K009-security/authorization/index
  - K009-security/authentication/authentication-surucu
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php:330-341 — web middleware sırası (#9 Permission)"
  - "shared/src/PageRouter/PageRouterKernel.php:63-78 — AuthGuard inşası + PageRouter enjeksiyonu"
  - "shared/src/PageRouter/AuthGuard.php:15-48 — route guard zinciri (role/permission → 403)"
  - "shared/src/Middleware/PermissionMiddleware.php:17-63 — web yetki motoru + authz_denied"
  - "api.coremusic.net/index.php:147-171 — API pipeline sırası (Authorization #7)"
  - "shared/src/Api/Middleware/AuthorizationMiddleware.php:25-110 — API yetki motoru (401/403)"
  - "shared/tests/Api/ApiPipelineSecurityTest.php:127-186 — public/401/frozen-sıra testleri"
validation:
  - "2026-10-15 — 11 kaynak dosya + 2 negatif grep bu oturumda doğrulandı"
  - "2026-10-15 — URL tablosu EKLENMEDİ (mimari grup, URL tahsisi yok)"
open_questions:
  - "API'de _auth_user rollsuz/izinssiz → zorunlu meta 403 mü üretir (§8 Z-1) → ⚠️ VERIFICATION REQUIRED"
  - "Üretim rotalarında requiredRole/requiredPermission ATANMADI (grep negatif) → UNKNOWN"
  - "API 401/403 güvenlik olayı kaydı (securityEvent) kodda yok → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · authorization · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Adım adım akış / durum geçişi / hata kolu (K009-security/authorization).

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/authorization/authorization-surucu |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | istek → yetki kararı sürüşü · PermissionMiddleware (web #9) · AuthorizationMiddleware (API) · AuthGuard zinciri · durum geçişleri · hata kolları (401/403/redirect) · sırasızlık riskleri |
| Kapsam dışı | kimlik doğrulama adımları (`[[authentication-surucu]]`) · rol/izin **tanımı** (K0100-identitiy) · denetim kaydı yazımı (audit) |
| Bağlantı | Kurallar `[[authorization-anayasa]]` (Z-01..Z-12), kod `[[authorization-ornekler]]` |
| Sıra kuralı | Kimlik sürüşü (`[[authentication-surucu]]`) tamamlanmadan bu dosyanın adımları SÜRÜLEMEZ — `_auth`/`_auth_user` bu dosyanın girdisidir |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir isteğin adım adım yetki kontrolünden geçmesi |
| Yetki motoru | Authorization engine | Kararı veren bileşen (web: PermissionMiddleware + AuthGuard; API: AuthorizationMiddleware) |
| Guard zinciri | Guard chain | `AuthGuard::check()` içindeki `??` kısaltma zinciri |
| Hata kolu | Error branch | 401 / 403 / redirect / pass-through yolları |
| Durum geçişi | State transition | ANONIM → AUTHENTICATED → PASS / DENIED |
| Sırasızlık | Ordering hazard | Doğru sırada çalışmayan kontrolün güvenlik etkisi |
| Public kapı | Public gate | Yetkinin sürülmediği açık uç kümesi |
| Kanıt | Evidence | Her adımın Dosya:Satır karşılığı |

## §3 Web Ana Sürüş Akışı (Pipeline #9 + Route Guard — Happy Path)

Web'de yetki **iki yerde** sürülür: pipeline'da `PermissionMiddleware` (#9) ve route
seviyesinde `AuthGuard` zinciri. İkisi farklı veri okur (pipeline `_auth`/route meta,
guard `$_SESSION` üzerinden `PageRouterHelper`). Sıra kanıtı:

| # | Middleware | Kanıt |
|---|---|---|
| 1 | `OriginCheckMiddleware` | `shared/src/PageRouter/PageRouterKernel.php:331` |
| 2 | `CorsMiddleware` | `PageRouterKernel.php:332` |
| 3 | `RateLimiterMiddleware` | `PageRouterKernel.php:333` |
| 4 | `SecurityHeadersMiddleware` | `PageRouterKernel.php:334` |
| 5 | `SessionManagerMiddleware` | `PageRouterKernel.php:335` |
| 6 | `CsrfMiddleware` | `PageRouterKernel.php:336` |
| 7 | `BypassAuthMiddleware` | `PageRouterKernel.php:337` |
| 8 | `AuthMiddleware` (`_auth` doldurur) | `PageRouterKernel.php:338` |
| **9** | **`PermissionMiddleware` (web yetki motoru)** | `PageRouterKernel.php:339` |
| 10 | `ValidationMiddleware` | `PageRouterKernel.php:340` |

### 3.1 PermissionMiddleware Sürüşü (pipeline #9)

| Adım | Koşul | Sonuç | Kanıt |
|---|---|---|---|
| PM-1 | `_route_meta` ve `_auth` okunur | meta `[]`/auth `[]` varsayılır | `shared/src/Middleware/PermissionMiddleware.php:19-20` |
| PM-2 | `empty($auth['userId'])` | **yetki SÜRÜLMEZ** → `$next($request)` (pass-through) | `PermissionMiddleware.php:22-25` |
| PM-3 | `role` alanı yok | `$userRole = 'user'` (düşük varsayılan) | `PermissionMiddleware.php:27` |
| PM-4 | `requiredRole` tanımı var ve `$userRole !== $requiredRole` | 403 `role_insufficient` (strict `!==`) | `PermissionMiddleware.php:30-33` |
| PM-5 | `requiredPermission` tanımı var ve `in_array(..., true)` başarısız | 403 `permission_denied` (strict) | `PermissionMiddleware.php:36-42` |
| PM-6 | Kontroller geçildi | `$next($request)` — handler'a devir | `PermissionMiddleware.php:44` |
| PM-7 | Herhangi bir 403 | `securityEvent('authz_denied', ...)` + `halt: true` | `PermissionMiddleware.php:47-63` |

### 3.2 AuthGuard Zinciri Sürüşü (route seviyesi)

`check()` altı kolu sırayla dener; **ilk null olmayan sonuç kazanır** (`??` zinciri):

| Sıra | Kol | null ise | Sonuç üretirse | Kanıt |
|---|---|---|---|---|
| G-1 | `checkAuthRequired` | rota auth istemiyor VEYA kimlik var | — | `shared/src/PageRouter/AuthGuard.php:26-30` |
| G-1b | (devam) | kimlik yok | login'e **redirect** (SPA/klassik) | `AuthGuard.php:31` → `AuthUrlBuilder.php:30-40` |
| G-2 | `checkRole` | `requiredRole === null` VEYA rol eşleşti | — | `AuthGuard.php:34-37` |
| G-2b | (devam) | rol eşleşmedi | `RouteResult::forbidden` (**403**) | `AuthGuard.php:39` |
| G-3 | `checkPermission` | `requiredPermission === null` VEYA izin var | — | `AuthGuard.php:42-45` |
| G-3b | (devam) | izin yok | `RouteResult::forbidden` (**403**) | `AuthGuard.php:47` |
| G-4 | `checkAuthRedirectRoute` | auth rota değil / kimlik var / skip | — | `AuthGuard.php:50-57` |
| G-4b | (devam) | anonim auth sayfasında | login'e redirect | `AuthGuard.php:59` |
| G-5 | `checkAuthenticatedUserOnAuthPage` | kimlik yok / auth sayfası değil / logout | — | `AuthGuard.php:62-68` |
| G-5b | (devam) | kimlikli kullanıcı auth sayfasında | `/home`'a redirect | `AuthGuard.php:70` |
| G-6 | `checkLogout` | logout değil / kimlik yok / skip | — | `AuthGuard.php:73-76` |
| G-6b | (devam) | kimlikli logout | logout redirect | `AuthGuard.php:78` |
| G-7 | tümü null | — | `null` → rota akışına devam | `AuthGuard.php:23` |

### 3.3 Guard'ın tükettiği yardimcilar (session üzerinden)

| Yardımcı | Karar | Kanıt |
|---|---|---|
| `checkAuthenticated()` | `MM_UserID` (fallback `_session_user_id`) dolu + string | `shared/src/PageRouter/PageRouterHelper.php:7-14` |
| `checkRole($r)` | `MM_UserRole` (fallback `_session_user_role`) **strict** `===` | `PageRouterHelper.php:16-22` |
| `checkPermission($p)` | `MM_Permissions` üzerinden `in_array(..., true)` | `PageRouterHelper.php:24-28` |

### 3.4 İki Katmanlı Yetki: Pipeline #9 vs Route Guard (aynı istekte iki karar noktası)

Web'de yetki kararı **iki ayrı katmanda** alınır; ikisi de aynı istek üzerinde
sürülür ama farklı veri okur ve farklı sonuç tipi üretir:

| Boyut | Pipeline katmanı (#9) | Guard katmanı |
|---|---|---|
| Bileşen | `PermissionMiddleware` | `AuthGuard::check()` zinciri |
| Kurulum | `PageRouterKernel.php:78` → `:339` | `PageRouterKernel.php:63-65` (inşa) → `:67-76` (PageRouter'a enjeksiyon) |
| Veri kaynağı | `$request['_auth']` (Auth #8) + `$request['_route_meta']` | `$_SESSION` (via `PageRouterHelper`) + `SpaRoute` meta |
| Okuma kanıtı | `PermissionMiddleware.php:19-20` | `PageRouterHelper.php:7-28` · `AuthGuard.php:28,36,44` |
| Rol varsayımı | `role` yoksa `'user'` (`PermissionMiddleware.php:27`) | Rol yoksa `requiredRole === null` → kolu atlar (`AuthGuard.php:36`) |
| Kimlik yoksa | pass-through (`PermissionMiddleware.php:22-25`) | login redirect (`AuthGuard.php:31`) |
| Yetki yoksa | 403 + `authz_denied` + halt (`PermissionMiddleware.php:47-63`) | 403 JSON, olay kaydı yok (`AuthGuard.php:39,47` → `RouteResult.php:53-63`) |
| Sonuç tipi | `{httpStatus:403, halt:true}` | `{httpStatus:403}` veya redirect |

> Sürüş sonucu (anayasa Z-08 karşılığı): pipeline #9 boş kimlikte **sessizce
> geçer**; route seviyesindeki koruma `SpaRoute::requireAuth` varsayılanı ve
> `AuthGuard` ile yürür. `check()` nihai olarak `PageRouter` tarafından çağrılır
> (enjeksiyon `PageRouterKernel.php:67-76`; çağrının bulunduğu satır bu oturumda
> `PageRouter.php` okunmadığı için `⚠️ VERIFICATION REQUIRED`).

## §4 API Ana Sürüş Akışı (JSON Gateway — Happy Path)

API'de yetki TEK yerde sürülür: pipeline'ın 7. (son) aşaması
`AuthorizationMiddleware`. Sıra kanıtı `api.coremusic.net/index.php:147-171`
(frozen — ADR-020):

| Adım | Eylem | Kanıt |
|---|---|---|
| A-1 | `ResponseNormalizationMiddleware` | `api.coremusic.net/index.php:148` |
| A-2 | OriginCheck (#) + Cors | `index.php:149-150` |
| A-3 | `RateLimitMiddleware` | `index.php:151` |
| A-4 | `AuthenticationMiddleware` → `_auth_user` (yoksa **401** ve sürüş biter) | `index.php:154-159` · 401: `shared/src/Api/Middleware/AuthenticationMiddleware.php:103-107` |
| A-5 | CSRF — yalnız `method === 'session'` | `index.php:164-169` |
| A-6 | `RequestValidationMiddleware` | `index.php:170` |
| A-7 | `AuthorizationMiddleware` (bu dosyanın motoru) | `index.php:171` |
| A-8 | Gateway → controller | `index.php:173-182` |

### 4.1 AuthorizationMiddleware Sürüşü

| Adım | Koşul | Sonuç | Kanıt |
|---|---|---|---|
| Z-1 | `_route_status` 404/405 | RBAC **sürülmez** → `$next` (yanıtı Gateway üretir) | `shared/src/Api/Middleware/AuthorizationMiddleware.php:29-34` |
| Z-2 | `isPublicRoute` doğru | `$next($request)` — yetki yok | `AuthorizationMiddleware.php:38-40` (tanım `:94-110`) |
| Z-3 | `_auth_user === null` | **401** `UNAUTHORIZED` | `AuthorizationMiddleware.php:43-50` |
| Z-4 | `_route` array değil | `$next($request)` (yukarıdaki 404/405 savunması) | `AuthorizationMiddleware.php:53-58` |
| Z-5 | `requiredRole` var → `$authUser['roles']` içinde **strict** yok | **403** `FORBIDDEN` / `Insufficient permissions` | `AuthorizationMiddleware.php:61-71` |
| Z-6 | `requiredPermission` var → `$authUser['permissions']` içinde **strict** yok | **403** `FORBIDDEN` | `AuthorizationMiddleware.php:74-84` |
| Z-7 | kontroller geçildi | `$next($request)` | `AuthorizationMiddleware.php:86` |

### 4.2 Public Kapı Sürüşü (API)

| Kaynak | Kural | Kanıt |
|---|---|---|
| RouteTable | `_route['public']` dolu ve boş değilse public | `AuthorizationMiddleware.php:96-99` |
| Fallback | `PUBLIC_ROUTE_PATTERNS` üzerinden `str_starts_with` | `AuthorizationMiddleware.php:101-107` |
| Sonuç | Public ise auth + authorization **ikisi de** sürülmez | `AuthenticationMiddleware.php:67-69` · `AuthorizationMiddleware.php:38-40` |

## §5 Durum Makineleri

### 5.1 Web durum geçişleri

| Durum | Tetik | Sonraki durum | Çıkış |
|---|---|---|---|
| ANONIM | — | SESSION_OKUMA (PM-2) | pass-through (yetki sürülmez) |
| AUTHENTICATED | `_auth['userId']` dolu (Auth #8) | YETKI_KONTROL | PM-3..PM-5 |
| YETKI_KONTROL | `requiredRole`/`requiredPermission` **tanımsız** (üretim durumu — §8 Z-2) | PASS | `$next` · `PermissionMiddleware.php:44` |
| YETKI_KONTROL | meta tanımlı, eşleşme var | PASS | `$next` |
| YETKI_KONTROL | meta tanımlı, eşleşme yok | DENIED-403 | `forbidden()` · `PermissionMiddleware.php:47-63` |
| PASS | guard G-2/G-3 | PASS veya DENIED-403 | `AuthGuard.php:34-48` |
| PASS | guard G-1 (anonim korumalı rota) | REDIRECT-LOGIN | `AuthGuard.php:31` |
| DENIED-403 | — | SON (halt) | `PermissionMiddleware.php:61` (`halt: true`) |

### 5.2 API durum geçişleri

| Durum | Tetik | Sonraki durum | Çıkış |
|---|---|---|---|
| ROUTE_YOK (404/405) | `_route_status` | PASS (RBAC yok) | `AuthorizationMiddleware.php:29-34` |
| PUBLIC | kapı doğru | PASS | `AuthorizationMiddleware.php:38-40` |
| KIMLIK_YOK | `_auth_user === null` | DENIED-401 | `AuthorizationMiddleware.php:43-50` |
| KIMLIK_VAR | `requiredRole`/`requiredPermission` **null** (üretim durumu — §8 Z-2) | PASS | `AuthorizationMiddleware.php:86` |
| KIMLIK_VAR | meta tanımlı → `roles`/`permissions` **strict** eşleşmiyor | DENIED-403 | `AuthorizationMiddleware.php:61-84` |
| KIMLIK_VAR | meta tanımlı → eşleşme var | PASS → controller | `AuthorizationMiddleware.php:86` |

## §6 Hata Kolları (Error Branches)

### 6.1 401 vs 403 vs redirect — hangi motor hangisini üretir

| Koşul | Web motoru | API motoru |
|---|---|---|
| Kimlik yok + korumalı rota | **redirect** (login) — 401 DEĞİL · `AuthGuard.php:31` | **401** · `AuthorizationMiddleware.php:43-50` |
| Kimlik var, rol/izin yok | **403** · `AuthGuard.php:39,47` → `RouteResult.php:53-63` | **403** · `AuthorizationMiddleware.php:61-84` |
| Kimlik yok + pipeline #9 | pass-through (401 DEĞİL, guard'a devreder) · `PermissionMiddleware.php:22-25` | 401 yukarıda (A-4) |
| Public rota | guard'da `requiresAuth=false` → devam | `isPublicRoute` → `$next` · `:38-40` |
| 404/405 | `RouteResult::notFound` (404) · `RouteResult.php:30-41` | RBAC sürülmez · `AuthorizationMiddleware.php:29-34` |

### 6.2 403 gövdeleri (ayrıntı sızıntısı denetimi — anayasa Z-07)

| Motor | Gövde | Kanıt |
|---|---|---|
| Web guard | `{error: "forbidden", redirect: "<rota>"}` — **generic**, rol/izin adı YOK | `RouteResult.php:53-63` |
| Web pipeline | `{error: "role_insufficient", message: "Required role: <rol>"}` — **rol adı VAR**; `{error: "permission_denied", message: "Required permission: <izin>"}` — **izin adı VAR** | `PermissionMiddleware.php:32,40` + `:59` |
| API | `{error: {code: "FORBIDDEN", message: "Insufficient permissions"}}` — **generic** | `AuthorizationMiddleware.php:65-69, 78-82` |

> Denetim notu (bağlayıcı kayıt — anayasa Z-07/U-5): web pipeline 403 gövdesi
> beklenen rol/izin **adını** gövdeye yazar; bunun istemciye ifşa politikası
> ADR'de kesinleşmemiştir → `⚠️ VERIFICATION REQUIRED`.

### 6.3 Sırasızlık Riskleri (ordering hazards)

| # | Risk | Sürüşteki gerçek durum | Kanıt |
|---|---|---|---|
| O-1 | Yetki motoru, kimlik motorundan ÖNCE sürülürse | Sürülmez: API'de Authorization, Authentication'ın **sonrasında** (A-4 → A-7); web'de Permission #9, Auth #8'in **sonrasında** | `api.coremusic.net/index.php:154,171` · `PageRouterKernel.php:338-339` |
| O-2 | CSRF, Authorization'dan ÖNCE sürülürse | Öyle: API'de CSRF (A-5) yetkiden (A-7) önce — CSRF 403'ü yetki reddiyle karışmaz çünkü ayrı hata tipi | `index.php:164-169,171` |
| O-3 | Validation, Authorization'dan ÖNCE sürülürse | Öyle (frozen sıra) — doğrulanmamış girdi yetki kapısına ulaşmaz | `index.php:170-171` · test: `ApiPipelineSecurityTest.php:161-186` |
| O-4 | Pipeline #9 yetkiyi atlayıp guard'a güvenirse | Atlatma gerçekleşir: `PermissionMiddleware` boş `userId`'de **pass-through** yapar; tek koruma route meta'sı + `AuthGuard` | `PermissionMiddleware.php:22-25` · `AuthGuard.php:15-24` |
| O-5 | Guard, pipeline'dan farklı veri okursa | Gerçek: guard `$_SESSION` (PageRouterHelper), pipeline `_auth` (AuthMiddleware) — iki okuma yüzeyi, tek oturum kaynağı | `PageRouterHelper.php:7-28` · `PageRouterKernel.php:337-339` |

### 6.4 Redirect mi, JSON Red mi? — SPA Algılama Sürüşü

Web yetki reddi iki biçimde çıkar: klasik istekte **302 redirect**, SPA isteğinde
**JSON**. Biçim kararı `$isSpaRequest` bayrağına bağlıdır ve guard'ın tüm
redirect kolları bu bayrağı tüketir (`AuthGuard.php:31,59,70,78`):

| Adım | Algı | Kanıt |
|---|---|---|
| S-1 | `X-Requested-With: XMLHttpRequest` → SPA | `shared/src/PageRouter/PageRouterKernel.php:307-310` |
| S-2 | `Accept: application/json` içeriyorsa → SPA | `PageRouterKernel.php:312-316` |
| S-3 | İkisi de yok → klasik (redirect) | `PageRouterKernel.php:318` (dönüş `false`) |
| S-4 | Redirect gövdesi | 302 + `Location` başlığı · `shared/src/PageRouter/RouteResult.php:43-51` |
| S-5 | Red gövdesi (guard) | 403 + `{error:"forbidden", redirect}` · `RouteResult.php:53-63` |

| İstek tipi | Kimlik yok (korumalı rota) | Yetki yok |
|---|---|---|
| Klasik | 302 login redirect (`AuthGuard.php:31`) | 403 JSON (`AuthGuard.php:39,47`) |
| SPA | 403 JSON redirect gövdesi ile (aynı kol, SPA bayrağı ile) | 403 JSON (aynı) |

> Denetim notu: SPA kullanıcısı "kimliksiz" durumda 401 **görmez** — login yönlendirmesi
> JSON gövdesindeki `redirect` alanıyla taşınır. API ile asimetridir: API'de kimliksizlik
> her zaman 401'dir (`AuthorizationMiddleware.php:43-50`). Karışan istemciler bu farkı
> bilmek zorundadır (anayasa §5 tablosunun web sütunu).

## §7 Mevcut Test Sürüşleri (Kanıtlı)

| Test | Ne sürer | Kanıt |
|---|---|---|
| Public rota, kimliksiz geçiş | `AuthorizationMiddleware` public kapı → `$next` çağrılır | `shared/tests/Api/ApiPipelineSecurityTest.php:127-141` |
| Kimliksiz özel rota | `_auth_user` yok → `UNAUTHORIZED` (401) | `ApiPipelineSecurityTest.php:143-151` |
| Frozen sıra | `index.php` içinde marker sırası: ResponseNormalization → Cors → RateLimit → Authentication → RequestValidation → **Authorization** | `ApiPipelineSecurityTest.php:161-186` |
| Eşleşmeyen yol | 404 yine pipeline'dan geçer (rate-limit/CORS kaçağı yok) | `ApiPipelineSecurityTest.php:112-125` |

**Test kapsamı boşlukları (negatif kanıt — bu oturumda grep ile doğrulandı):**

| Boşluk | Ölçüm | Sonuç |
|---|---|---|
| `requiredRole`/`requiredPermission` ataması | `shared/config/**` içinde grep | **0 isabet** — üretim rotası bu meta'yı doldurmuyor (§8 Z-2) |
| `PermissionMiddlewareTest` | `shared/tests/**` içinde grep | **0 isabet** — web yetki motorunun birim testi dosyası YOK (anayasa U-8) |
| Rol eşleşmesi senaryosu | `ApiPipelineSecurityTest` içinde `requiredRole` senaryosu | Yok — testler yalnız public/401/sıra sürer (§7 satır 1-3) |

## §8 Kayıtlı Zayıf Nokta Sürüyor (anayasa §13 karşılığı — bağlayıcı)

Bu bölüm, `[[authorization-anayasa]]` §13'te tescilli zayıf noktaların **sürüş
kanıtıdır**; susma değil, kayıt devamıdır.

### Z-1 — API `_auth_user` rollsuz/izinssiz üretilir, motor `roles`/`permissions` okur

| Adım | Gerçek durum | Kanıt |
|---|---|---|
| 1 | Session kimliği: `_auth_user = {id, authenticated, method:'session'}` — **roles/permissions YOK** | `AuthenticationMiddleware.php:72-80` |
| 2 | JWT kimliği: `_auth_user = {id, authenticated, method:'jwt'}` — **roles/permissions YOK** | `AuthenticationMiddleware.php:91-95` |
| 3 | Motor okur: `$authUser['roles'] ?? []` · `$authUser['permissions'] ?? []` | `AuthorizationMiddleware.php:63,76` |
| 4 | Sonuç: bir API rotası `requiredRole` tanımlarsa kullanıcı ister admin olsun, `in_array` **her zaman başarısız** → 403 | `AuthorizationMiddleware.php:64,77` |

> Sürüş sonucu: API'de rol bazlı koruma **fiilen kullanılamaz durumdadır** (kullanıcı
> hiçbir rolle geçemez — fail-closed yönünde sapar, açık yönünde değil). Canlı
> davranışı izleyen entegrasyon testi yok → `⚠️ VERIFICATION REQUIRED` (anayasa U-6).

### Z-2 — Üretim rotalarında yetki meta'sı ATANMADI (grep negatif)

| Ölçüm | Kapsam | Sonuç |
|---|---|---|
| `requiredRole|requiredPermission` grep | `shared/config/` (route configleri) | **0 isabet** |

> Sürüş sonucu: §3 PM-4/PM-5 ve §4 Z-5/Z-6 kolları üretimde **hiç sürülmez**;
> anayasa Z-02 (deny-by-default) bakımından motorlar "hazır" ama rota tarafı
> sessizdir. Rol/izin meta'sı dolmadan yetki motoru **kanıtlanamaz** → UNKNOWN
> (anayasa U-3: tek kaynak kararı ADR bekliyor).

### Z-3 — Web'de `authz_denied` var, API'de güvenlik olayı YOK

| Motor | Red olayı | Kanıt |
|---|---|---|
| Web | `LoggerFactory::securityEvent('authz_denied', {reason, uri, ip})` her 403'te | `PermissionMiddleware.php:49-54` |
| Web guard | `RouteResult::forbidden` içinde olay kaydı **YOK** | `RouteResult.php:53-63` |
| API | `AuthorizationMiddleware` 401/403 üretir; `securityEvent` çağrısı dosyada **YOK** (1-111 arası okundu) | `AuthorizationMiddleware.php:43-84` |

> Sürüş sonucu: yetki reddi gözlemlenebilirliği **motorlarda tutarsızdır**
> (anayasa Z-09 ihlali API tarafında) → `⚠️ VERIFICATION REQUIRED` (anayasa U-4).

## §9 Denetim Sürüş Reçetesi (her incelemede — anayasa §5 karşılığı)

| # | Sürülecek soru | Beklenen kanıt |
|---|---|---|
| D-1 | Yeni rota `requiredRole`/`requiredPermission` içeriyor mu? | route meta + §8 Z-1 etkisi (API'de 403 riski) |
| D-2 | 401/403/redirect doğru motor tarafından mı üretildi? | §6.1 tablosu + Dosya:Satır |
| D-3 | 403 gövdesi generic mi? | §6.2 tablosu (web pipeline mesaj ifşası — U-5) |
| D-4 | Red gözlemleniyor mu? | `securityEvent('authz_denied')` — web pipeline'da var, guard/API'da yok (§8 Z-3) |
| D-5 | Sıra değişti mi? | `ApiPipelineSecurityTest.php:161-186` frozen testi + `PageRouterKernel.php:330-341` |
| D-6 | Public kapı genişledi mi? | `AuthorizationMiddleware.php:94-110` + `AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS` |

## §10 Bilinmeyenler Defteri (anayasa U-listesi ile hizalı)

| # | Soru | Etiket |
|---|---|---|
| U-1 | Yetki modelinin adı (RBAC vs ABAC) | ⚠️ VERIFICATION REQUIRED |
| U-2 | Nesne bazlı yetki (IDOR) — sürüş kanıtı bu oturumda YOK | ⚠️ VERIFICATION REQUIRED |
| U-3 | Rol/izin meta'sının tek kaynağı (config vs route meta) — grep 0 isabet | UNKNOWN |
| U-4 | API 401/403 güvenlik olayı kaydı (§8 Z-3) | ⚠️ VERIFICATION REQUIRED |
| U-5 | Web pipeline 403 mesajındaki rol/izin ifşası politikası (§6.2) | ⚠️ VERIFICATION REQUIRED |
| U-6 | API `roles` boşluğunun canlı davranışı (§8 Z-1) | ⚠️ VERIFICATION REQUIRED |
| U-7 | ADR-010/ADR-043 metinleri bu oturumda okunmadı | ⚠️ VERIFICATION REQUIRED |
| U-8 | `PermissionMiddlewareTest` dosyası yok (grep 0 isabet) | UNKNOWN |

## § BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[authorization-anayasa]] | kardeş | bağlayıcı ilkeler Z-01..Z-12 + §13 zayıf nokta tescili |
| [[authorization-kapsam]] | kardeş | kapsam + iki motor karşılaştırması |
| [[authorization-ornekler]] | kardeş | Permission/Authorization gerçek kodu |
| [[authorization-dogrulama]] | kardeş | disk kontrol listesi |
| [[authorization-bilesenler]] | kardeş | kademe-3 bileşen planı |
| [[authorization-web-arastirma]] | kardeş | OWASP / CWE / NIST kayıtları |
| [[authentication-surucu]] | öncesi | kimlik sürüşü (bu dosyanın girdisi) |
| [[authentication-anayasa]] | öncesi | kimlik kuralları |
| [[security-master]] | yukarı | K009 anayasası |
| [[security-kararlar]] | yukarı | ADR kararları |
| [[rate-limiting-kapsam]] | kardeş | kota politikası (yetki denemeleri) |
| [[session-security-kapsam]] | kardeş | oturum yaşam döngüsü |
| [[identity-master]] | tek yönlü | rol/izin tanımı kaynağı (K0100-identitiy) |
| [[transactions-anayasa]] | iskelet | anayasa şablonu |

## §11 Revizyon Günlüğü

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 11 kaynak dosya + 2 negatif grep kanıtı · §8 zayıf nokta sürüş kaydı · URL tablosu yok | Technical Writer (tek yazar) |

## §12 Sürüm ve Otorite

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Otorite | Bayram Ali / Vault Steward |
| Mod | Red Team · Human Mode · Truth Mode |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
