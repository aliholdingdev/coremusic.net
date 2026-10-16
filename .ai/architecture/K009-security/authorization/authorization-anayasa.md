---
node_id: K009-security/authorization/authorization-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/authorization/index
  - K009-security/authentication/authentication-anayasa
  - K009-security/security-master
evidence:
  - "shared/src/Middleware/PermissionMiddleware.php:17-63 — rol + izin kontrolü, 403 + güvenlik olayı"
  - "shared/src/Api/Middleware/AuthorizationMiddleware.php:25-110 — 401/403, public kapı, strict in_array"
  - "shared/src/PageRouter/AuthGuard.php:15-48 — web guard zinciri (role/permission → 403)"
  - "shared/src/PageRouter/PageRouterHelper.php:16-27 — strict rol/izin karşılaştırması"
  - "shared/src/PageRouter/SpaRoute.php:9-12 — requireAuth=true varsayılanı (deny-by-default)"
  - "shared/tests/Api/ApiPipelineSecurityTest.php:127-151 — public geçiş + kimliksiz 401"
validation:
  - "2026-10-15 — 11 kaynak dosya okundu; negatif kanıtlar (grep) ayrıca işaretlendi"
  - "2026-10-15 — URL tablosu EKLENMEDİ (mimari grup, URL tahsisi yok)"
open_questions:
  - "Yetki modelinin adı (RBAC/ABAC) ADR'de kesinleşmedi → ⚠️ VERIFICATION REQUIRED"
  - "Nesne bazlı yetki (IDOR) kontrolü kodda kanıtlanamadı → ⚠️ VERIFICATION REQUIRED"
  - "API _auth_user rolleri boş (bkz §13) → ⚠️ VERIFICATION REQUIRED"
  - "Rol/izin matrisinin tek kaynağı (config vs route meta) → UNKNOWN"
group: mimari
---

# K009 · authorization · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Bağlayıcı ilke / yasak / karar kapısı (K009-security/authorization).

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/authorization/authorization-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | en-az-yetki · deny-by-default · rol/izin ayrımı · PermissionMiddleware/AuthorizationMiddleware kuralları · 401/403 semantiği · nesne bazlı yetki (IDOR) |
| Kapsam dışı | kimlik doğrulama (authentication) · kimlik modeli ve rol **tanımı** (K0100-identitiy) · denetim izi yazımı (audit) · OS düzeyi izinler |
| Sıra kuralı | Önce `authentication-anayasa` (kimlik doğru mu), sonra bu dosya (buna yetkili mi) |

## §2 Terimler (Türkçe / English)

| Türkçe | English | Tanım |
|---|---|---|
| Yetkilendirme | Authorization | Doğrulanmış kimliğin kaynak/eklem üzerinde işlem yapma kararı |
| En az yetki | Least privilege | İşi bitirmek için gereken **en küçük** yetki kümesi |
| Varsayılan red | Deny-by-default | İznin tanımlı olmadığı her şey reddedilir |
| Rol | Role | Kimliğe atanan sıfat (ör. `admin`), eşitlikle kontrol edilir |
| İzin | Permission | Kaynak-eklem düzeyi yetki (`requiredPermission`) |
| RBAC | Role-Based Access Control | Rol üzerinden erişim modeli (kod kanıtı: rol + permission) |
| Özne-nesne | Subject-object | "Bu kimlik, bu kaydın sahibi mi?" kontrolü (IDOR savunması) |
| Red kodu | Failure code | 401 (kimlik yok) vs 403 (yetki yok) |
| Ayrıntı sızıntısı | Verbose error | Reddederken neden/rol bilgisini ifşa etme |
| Denetim olayı | Security event | `authz_denied` kaydı |

## §3 Bağlayıcı İlkeler (Anayasa Maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| Z-01 | **En az yetki**: rota/eklem için gerekmeyen rol/izin verilmez | Code review RED |
| Z-02 | **Deny-by-default**: yetki meta'sı yoksa erişim varsayılan **kapalıdır**; "izin verilmiş say" yasak | RED |
| Z-03 | Yetki kontrolü **yalnız** `requiredRole`/`requiredPermission` üzerinden yürür; rol **tanımı** bu dosyada değildir (K0100-identitiy) | BLOCK |
| Z-04 | Rol karşılaştırması **strict** (`!==`, `===`) — tip juggling yasak | RED |
| Z-05 | İzin karşılaştırması `in_array(..., true)` — strict zorunlu | RED |
| Z-06 | Kimlik yok → **401**; kimlik var yetki yok → **403** — asla karıştırılmaz | BLOCK |
| Z-07 | 403 gövdesi **generic**tir (`Insufficient permissions` / hata kodu); beklenen rol/izin **listesi** sızdırılmaz | RED |
| Z-08 | Kimlik bilgisi boşsa yetki kontrolü **sessizce atlanamaz**; korumanın nerede olduğu yazılı olmalıdır (§13) | BLOCK |
| Z-09 | Yetki reddi **gözlemlenir** (`securityEvent('authz_denied')`); sessiz 403 yasak | BLOCK |
| Z-10 | Nesne bazlı erişim (kayıt sahipliği) **her** sorguda doğrulanır — yetki=rol tek başına yeterli değildir | RED |
| Z-11 | Public rota tanımı **açık ve dar** olmalı; rota listesi kod içinde sınırsız genişletilemez | RED |
| Z-12 | Yetki kuralları middleware **içinde** değil, rota meta'sında tanımlanır (tek kaynak) | BLOCK |

## §4 Karar Kapıları (Decision Gates)

| # | Kapı | Soru | Evet | Hayır |
|---|---|---|---|---|
| G-1 | Kimlik kapısı | `_auth` (web) / `_auth_user` (API) var mı? | §5'e geç | 401 / login redirect (Z-06) |
| G-2 | Public kapısı | Rota public mi? | yetki uygulanmaz | tam kontrol |
| G-3 | Meta kapısı | `requiredRole`/`requiredPermission` tanımlı mı? | §6 kontrolü | **deny-by-default** → rota için ek kontrol gerekir (Z-02) |
| G-4 | Rol kapısı | `$userRole === $requiredRole` mi? | G-5'e | 403 `role_insufficient` |
| G-5 | İzin kapısı | `in_array($requiredPermission, $perms, true)` mi? | geç | 403 `permission_denied` |
| G-6 | Nesne kapısı | Kayıt öznesi kimlikle eşleşiyor mu? (Z-10) | geç | 403 + kayıt |
| G-7 | Red kapısı | Reddediyor muyuz? | generic 403 + `authz_denied` olayı | — |

## §5 Doğrulama Sorusu Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Yeni rota/eklem eklendi mi? | yetki meta'sı tanımlandı mı? (Z-02/Z-12) | BLOCK |
| 2 | `in_array` üçüncü parametresi `true` mı? | Z-05 | RED |
| 3 | Karşılaştırma `==` ile mi? | Z-04 ihlali → RED | Devam |
| 4 | 403 gövdesinde rol/izin listesi var mı? | Z-07 ihlali → RED | Devam |
| 5 | "Kimlik yok" ile "yetki yok" aynı kodda mı? | Z-06 ihlali → BLOCK | Devam |
| 6 | Nesne bazlı kontrol var mı (id parametresi okunuyor mu)? | Z-10 | İnceleme → §12 |
| 7 | Red log'u (`authz_denied`) var mı? | Z-09 | BLOCK |
| 8 | Public rota listesine ekleme mi? | Z-11 → ADR/Security onayı | Devam |

## §6 En Az Yetki Maddeleri (Least Privilege)

| # | Kural | Kanıt / Durum |
|---|---|---|
| E-1 | Varsayılan rota auth **ister** (`requiresAuth = true`) | `shared/src/PageRouter/SpaRoute.php:9` — ✅ KANITLI (deny-by-default iskeleti) |
| E-2 | Varsayılan `requiredRole = null` (rol zorunlu **değil**) | `SpaRoute.php:11` — ⚠️ yani rol tanımsız rota "herkese açık rol" anlamına gelir; nötr/meta yoksa §13 |
| E-3 | Varsayılan `requiredPermission = null` | `SpaRoute.php:12` — ⚠️ aynı nüans |
| E-4 | Rol tanımı/dağıtımı K0100-identitiy'ye ait | `authorization/index.md §4` — sınır |
| E-5 | Üretim rotalarında `requiredRole`/`requiredPermission` atanması | tarama: **0 üretim örneği** (yalnız `SpaRouteTest.php:31`, `AuthGuardTest.php:92,101`) → ⚠️ VERIFICATION REQUIRED |
| E-6 | API rotalarında RBAC meta'sı | `api.coremusic.net/config/routes.php:15` yalnız **yorum**; atama **0** → ⚠️ VERIFICATION REQUIRED |
| E-7 | Bypass kimliğinde izin listesi **boş** (`permissions = []`) | `BypassAuthMiddleware.php:53` — ✅ KANITLI (bypass'ın izni yok) |

> **Açık karar:** E-2/E-3 "meta yoksa ne olur?" sorusunu doğurur. Bu dosya
> **deny-by-default**'u bağlar (Z-02); hangi yüzeyin kimliği yoksa reddettiği
> §13'te kayıt altındadır.

## §7 PermissionMiddleware Bağlayıcı Kuralları (Web — Pipeline #9)

| # | Kural | Kanıt (Dosya:Satır) |
|---|---|---|
| M-1 | Girdi: `_route_meta` + `_auth` | `shared/src/Middleware/PermissionMiddleware.php:19-20` |
| M-2 | `userId` boşsa `$next()` — **bu middleware yetkisiz değil, kimliksiz istekle ilgilenmez** | `PermissionMiddleware.php:22-25` |
| M-3 | Rol: `$requiredRole !== null && $userRole !== $requiredRole` → 403 `role_insufficient` | `:30-33` |
| M-4 | İzin: `!is_array($perms) || !in_array($requiredPermission, $perms, true)` → 403 `permission_denied` | `:36-42` |
| M-5 | Geçerse `$next()` | `:44` |
| M-6 | Red: `securityEvent('authz_denied')` (reason/uri/ip) + `httpStatus 403` + `halt` | `:47-63` |
| M-7 | Rol yoksa `'user'` varsayılır (nötr rol) | `:27` |
| M-8 | Rol kaynağı `_auth['role']` → session (`MM_UserRole`) | `AuthMiddleware.php:19, :26` |
| M-9 | Meta kaynağı `_route_meta` → `SpaRoute` | `PageRouterKernel.php:159-160` |
| M-10 | Sıra: `Bypass → Auth → Permission` (dondurulmuş) | `PageRouterKernel.php:337-339` |

## §8 AuthorizationMiddleware Bağlayıcı Kuralları (API)

| # | Kural | Kanıt (Dosya:Satır) |
|---|---|---|
| N-1 | 404/405 ise RBAC uygulanmaz (yanıt Gateway'de) | `shared/src/Api/Middleware/AuthorizationMiddleware.php:27-34` |
| N-2 | Public rota → geç | `:36-40`, kapı `:94-110` |
| N-3 | `_auth_user === null` → **401** (kimlik yok, yetki değil) | `:42-50` |
| N-4 | `_route` dizisi değilse → geç (404/405 yorumu) | `:52-58` |
| N-5 | `requiredRole` varsa `in_array(..., true)` → 403 generic | `:60-71` |
| N-6 | `requiredPermission` varsa `in_array(..., true)` → 403 generic | `:73-84` |
| N-7 | Red gövdesi: `FORBIDDEN` / `Insufficient permissions` — rol listesi **yok** | `:65-69`, `:78-82` (Z-07 uyumlu) |
| N-8 | Sıra: Authentication → CSRF(session) → Validation → **Authorization** | `api.coremusic.net/index.php:154-171` |
| N-9 | Sıra testi | `shared/tests/Api/ApiPipelineSecurityTest.php:161-184` |

## §9 Rol / İzin Ayrımı (RBAC Sözdizimi)

| Katman | Rol kontrolü | İzin kontrolü | Kanıt |
|---|---|---|---|
| Web — pipeline | `$userRole !== $requiredRole` (strict !==) | `in_array(..., true)` | `PermissionMiddleware.php:31, :39` |
| Web — route guard | `$role === $requiredRole` (strict) | `in_array(..., true)` | `PageRouterHelper.php:16-27` (`:21`, `:27`) |
| Web — guard kararı | `requiredRole === null` → skip | `requiredPermission === null` → skip | `AuthGuard.php:34-48` |
| API — gateway | `in_array($requiredRole, $userRoles, true)` | `in_array($requiredPermission, $userPermissions, true)` | `AuthorizationMiddleware.php:64, :77` |

| Ayrım kuralı | Açıklama |
|---|---|
| Rol = sıfat (eşitlik) | "admin mi?" — tek rol beklenir, `!==`/`===` |
| İzin = üyelik (kümeler) | "iznin arasında var mı?" — liste içinde `in_array` strict |
| İkisi birden tanımlıysa | Önce rol (403 `role_insufficient`), sonra izin (403 `permission_denied`) — `PermissionMiddleware.php:30-42` |
| İzin listesi array değilse | **Red** (`!is_array` → 403) — fail-closed (`:39`) |

## §10 Deny-by-Default ve Public Kapısı

| # | Yüzey | Deney-bay-default davranışı | Kanıt |
|---|---|---|---|
| D-1 | Web rota varsayılanı | `requiresAuth = true` | `SpaRoute.php:9` |
| D-2 | API public kapı | `_route['public']` VEYA `PUBLIC_ROUTE_PATTERNS` (4 önek) | `AuthenticationMiddleware.php:33-38, :117-133` · `AuthorizationMiddleware.php:94-110` |
| D-3 | `_auth_user` yoksa | 401 (API) | `AuthorizationMiddleware.php:43-50` |
| D-4 | İzin listesi array değilse | 403 (fail-closed) | `PermissionMiddleware.php:39` |
| D-5 | Web'de kimlik yok | `AuthGuard` → login redirect (SPA 403 / klasik 302) | `AuthGuard.php:26-32` · `AuthUrlBuilder.php:32-40` · `RouteResult.php:43-56` |
| D-6 | Web'de rol uyuşmuyor | `RouteResult::forbidden` (403) | `AuthGuard.php:34-40` |
| D-7 | Web'de izin uyuşmuyor | `RouteResult::forbidden` (403) | `AuthGuard.php:42-48` |

## §11 Red Semantiği (401 vs 403) ve Ayrıntı Denetimi

| Durum | Web | API | Kanıt |
|---|---|---|---|
| Kimlik yok (auth'lu rota) | login redirect (302 / SPA 403) | **401** `UNAUTHORIZED` | `AuthGuard.php:31` · `AuthorizationMiddleware.php:45-49` |
| Kimlik var, rol yetmiyor | 403 (guard) / 403 `role_insufficient` (pipeline) | 403 `FORBIDDEN` | `AuthGuard.php:39` · `PermissionMiddleware.php:32` · `AuthorizationMiddleware.php:65-69` |
| Kimlik var, izin yetmiyor | 403 `permission_denied` | 403 `FORBIDDEN` | `PermissionMiddleware.php:40` · `AuthorizationMiddleware.php:78-82` |
| CSRF başarısız (kimlikli) | 403 `csrf_invalid` | 403 (yalnız session metodu) | `CsrfMiddleware.php:57-63` · `index.php:164-169` |
| Rate limit | 429 | 429 | `RateLimiterMiddleware.php:93-99` |

| Ayrıntı kuralı | Durum |
|---|---|
| API 403 gövdesi generic (`Insufficient permissions`) | ✅ KANITLI (`AuthorizationMiddleware.php:67,80`) |
| Web pipeline 403 gövdesi kod + beklenen rol/izin **adı** taşır | ⚠️ kayıtlı: `role_insufficient` + `Required role: {$requiredRole}` (`PermissionMiddleware.php:32,40`) → **beklenen değeri ifşa eder**; ayrıntı sızıntısı denetimi Z-07'ye bağlı → açık madde (§19 U-5) |
| Red `authz_denied` olayıyla loglanır | ✅ `PermissionMiddleware.php:50-54` |
| API 403 olay kaydı | ⚠️ KANIT YOK — `AuthorizationMiddleware`'de log çağrısı **görülmedi** (dosya tamamı okundu) → §19 U-4 |

## §12 Nesne Bazlı Yetki (IDOR / Özne-Nesne)

| # | Kural | Durum |
|---|---|---|
| ID-1 | Rol/izin kontrolü **nesne sahipliğini** yerine geçirmez (Z-10) | kural |
| ID-2 | `id`/`uuid` parametresiyle erişimde "kayıt kimin?" sorgusu | ⚠️ VERIFICATION REQUIRED — bu oturumda kod kanıtı **okunmadı** |
| ID-3 | Mass-assignment (toplu alan yazma) denetimi | ⚠️ VERIFICATION REQUIRED (`authorization/index.md §2`: test kanıtı yok) |
| ID-4 | İlgili test (IDOR/mass-assignment) | yok → `security-testing` çocuğu |

## §13 Kayıtlı Zayıf Nokta (Bu Dosyanın Bağlayıcı Kaydı)

| # | Bulgu | Kanıt | Etiket |
|---|---|---|---|
| W-1 | `AuthenticationMiddleware` `_auth_user`'a yalnız `id/authenticated/method` yazar | `AuthenticationMiddleware.php:74-78, :91-95` | ✅ KANITLI |
| W-2 | `AuthorizationMiddleware` `$authUser['roles']` / `$authUser['permissions']` okur | `AuthorizationMiddleware.php:63, :76` | ✅ KANITLI |
| W-3 | `roles`/`permissions` alanları repo genelinde **hiç yazılmıyor** | grep: `_auth_user` atamaları yalnız AuthenticationMiddleware.php:74, :91 (5 isabet) | ✅ NEGATİF KANIT |
| W-4 | Üretim rotalarında `requiredRole`/`requiredPermission` ataması **yok** | grep: yalnız test dosyaları + routes.php:15 yorumu | ✅ NEGATİF KANIT |
| W-5 | Sonuç: API RBAC meta'sı atanırsa rol listesi boş kalır → `in_array` false → 403 | `AuthorizationMiddleware.php:64, :77` | ⚠️ VERIFICATION REQUIRED (davranış testi yok) |
| W-6 | Web'de koruma iki katmanlı: `PermissionMiddleware` (meta varsa) + `AuthGuard` (`requiresAuth`) | `PageRouterKernel.php:339` · `AuthGuard.php:17-24` | ✅ KANITLI |
| W-7 | `PermissionMiddleware` kimliksiz isteği **geçirir** — tek başına koruma **değildir** | `PermissionMiddleware.php:22-25` + `AuthMiddlewareTest.php:84` | ✅ KANITLI |

> **Karar kapısı:** W-1..W-5 giderilmeden API'de rol tabanlı kural **çalıştırılmaz**;
> açılacaksa ADR + davranış testi (§16 Z-5) zorunludur. Düzeltme yetkisi
> Backend Architect + Security Engineer (MO onayı).

## §14 Yasaklar (Forbidden — authorization)

| # | Yasak | Sınıf |
|---|---|---|
| Y-20 | `==` ile rol/izin karşılaştırması | tip juggling |
| Y-21 | `in_array`'de strict parametresi `false`/eksik | üyelik hatası |
| Y-22 | 401 ile 403'ü karıştırmak (kimlik/yetki) | semantik |
| Y-23 | 403 gövdesinde yetki listesini/rol ağacını döndürmek | bilgi sızıntısı |
| Y-24 | Yetki kontrolünü controller/Service içine dağıtmak (tek kural) | savunma derinliği kaybı |
| Y-25 | Public rota eklemek için middleware'i atlamak | erişim |
| Y-26 | Kimliksiz isteği "yetkili" saymak (deny-by-default ihlali) | erişim |
| Y-27 | `authz_denied` logunu kapatmak | gözlemlenebilirlik |
| Y-28 | Rol/izin atamasını kod içine sabitlemek (tek kaynak dışı) | tutarsızlık |
| Y-29 | Nesne bazlı kontrolü "rol admin" ile ikame etmek | IDOR |
| Y-30 | Sıra değiştirerek `Permission`'ı `Auth`'tan önceye almak | sessiz pas geçiş (§13 W-7) |

## §15 Gözlemlenebilirlik Maddeleri

| # | Olay | Kayıt | Kanıt |
|---|---|---|---|
| G-1 | Web yetki reddi | `securityEvent('authz_denied', reason/uri/ip)` | `PermissionMiddleware.php:50-54` |
| G-2 | API yetki reddi (403) | log **görülmedi** | ⚠️ §19 U-4 |
| G-3 | API kimlik reddi (401) | log **görülmedi** | ⚠️ §19 U-4 |
| G-4 | CSRF reddi | `csrf_invalid` | `CsrfMiddleware.php:52-55` |
| G-5 | Rate limit | `rate_limited` / `rate_limiter_unavailable` | `RateLimiterMiddleware.php:57,67,88-92` |
| G-6 | Guard reddi (`RouteResult::forbidden`) | — | ⚠️ KANIT YOK |

## §16 Test Zorunlulukları (Mevcut Envanter)

| Test | Ne kapsıyor | Kanıt |
|---|---|---|
| `ApiPipelineSecurityTest::testAuthorizationMiddlewareAllowsPublicRouteWithoutAuth` | public rota geçişi | `:127-141` |
| `ApiPipelineSecurityTest::testAuthorizationMiddlewareRejectsUnauthenticatedPrivateRoute` | kimliksiz → `UNAUTHORIZED` | `:143-151` |
| `ApiPipelineSecurityTest::testIndexRegistersPipelineInFrozenOrder` | dondurulmuş sıra | `:161-184` |
| `AuthGuardTest` | rol/red senaryoları | `shared/tests/Unit/PageRouter/AuthGuardTest.php:92,101` (grep) |
| `SpaRouteTest` | `requiredRole: 'admin'` varsayımı | `shared/tests/Unit/PageRouter/SpaRouteTest.php:31` (grep) |
| `AuthMiddlewareTest` | `_auth` boş → Permission pasif | `:84` |
| **`PermissionMiddlewareTest`** | — | **dosya YOK** (test envanteri taraması 2026-10-15) → açık Z-1 |

**Zorunlu yeni testler:**

| # | Test | Sahip |
|---|---|---|
| Z-1 | `PermissionMiddleware`: rol uyuşmazlığı → 403 + `authz_denied`; izin yok → 403; izin listesi array değilse → 403 | QA |
| Z-2 | `PermissionMiddleware`: `userId` boş → geçiş (W-7 davranışının sabitlenmesi) | QA |
| Z-3 | API: `requiredRole` atanmış rota + `_auth_user` (roles boş) → **beklenen sonuç** kararı (W-5) | QA + Backend + ADR |
| Z-4 | 403 gövdesinin ayrıntı sızdırmadığı (Z-07 / U-5) | QA + Security |
| Z-5 | IDOR: başkasının `id`'si ile erişim → 403 | QA (security-testing) |

## §17 Kanıt Envanteri (Evidence Inventory)

| # | Dosya:Satır | Ne gösteriyor |
|---|---|---|
| 1 | `shared/src/Middleware/PermissionMiddleware.php:19-20` | `_route_meta` + `_auth` girdisi |
| 2 | `PermissionMiddleware.php:22-25` | kimlik yok → `$next()` (M-2/W-7) |
| 3 | `PermissionMiddleware.php:27,30-33` | rol varsayımı + strict !== → 403 |
| 4 | `PermissionMiddleware.php:36-42` | izin `in_array(..., true)` + `!is_array` fail-closed |
| 5 | `PermissionMiddleware.php:47-63` | `authz_denied` olayı + 403 halt |
| 6 | `shared/src/Api/Middleware/AuthorizationMiddleware.php:27-40` | 404/405 + public geçişi |
| 7 | `AuthorizationMiddleware.php:42-50` | `_auth_user` yok → 401 |
| 8 | `AuthorizationMiddleware.php:60-84` | rol/izin `in_array` strict → 403 generic |
| 9 | `AuthorizationMiddleware.php:94-110` | public kapı (route + pattern) |
| 10 | `shared/src/PageRouter/AuthGuard.php:17-24` | guard zinciri |
| 11 | `AuthGuard.php:26-48` | login redirect / rol / izin → sonuç |
| 12 | `shared/src/PageRouter/PageRouterHelper.php:16-27` | `===` rol, `in_array` izin |
| 13 | `shared/src/PageRouter/SpaRoute.php:9-12` | `requiresAuth=true`, `requiredRole=null` |
| 14 | `shared/src/PageRouter/PageRouterKernel.php:159-160` | `_route_meta` kaynağı |
| 15 | `shared/src/PageRouter/RouteResult.php:43-56` | 302 / 403 yanıt üretimi |
| 16 | `api.coremusic.net/index.php:154-171` | API sırası (Auth → Validation → Authz) |
| 17 | `api.coremusic.net/config/routes.php:15` | RBAC meta dokümantasyonu (yorum) |
| 18 | `shared/tests/Api/ApiPipelineSecurityTest.php:127-151,161-184` | public/401/sıra testleri |
| 19 | grep: `_auth_user` (5), `requiredRole:` (yalnız test ×3) | negatif kanıt (W-3, W-4) |
| 20 | `shared/tests/` envanteri | `PermissionMiddlewareTest` **yok** (Z-1) |

## §18 Uyum Matrisi (Standart / ADR)

| Kural | Kaynak | Uyum |
|---|---|---|
| En az yetki + fail-safe (fail-open yasak) | `.ai/AGENTS.md §18` | ✅ D-1..D-7 |
| Auth middleware değişimi Security'e handover | `.ai/AGENTS.md §9.3` | ✅ §21 |
| Pipeline sırası ADR'siz değişmez | `api.coremusic.net/AGENTS.md §4.4` | ✅ N-8/N-9 |
| Middleware sırası değişikliği → MO → ADR | `api.coremusic.net/AGENTS.md §3.3` | ✅ Y-30 |
| ADR-010 / ADR-043 / ADR-008 | `PermissionMiddleware.php:13` (dosya yorumu) | ✅ sıra notu |
| OWASP A01 (Broken Access Control) | `authorization/index.md §5` URL kaydı | ✅ bu dosyada URL yok |
| `.ai/.decisions/index.md` (ADR-010) | Security okuma listesi | ⚠️ ADR metni bu oturumda **okunmadı** |

## §19 Bilinmeyenler Defteri (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | Yetki modelinin adı ve kapsamı (RBAC vs ABAC) | ⚠️ VERIFICATION REQUIRED |
| U-2 | Nesne bazlı yetki (IDOR) uygulaması | ⚠️ VERIFICATION REQUIRED |
| U-3 | Rol/izin matrisinin tek kaynağı (config vs route meta — ADR) | UNKNOWN |
| U-4 | API 401/403 güvenlik olayı kaydı | ⚠️ VERIFICATION REQUIRED (G-2/G-3) |
| U-5 | Web 403 gövdesindeki `Required role:` ifşasının ayrıntı politikası | ⚠️ VERIFICATION REQUIRED (§11) |
| U-6 | API `roles` boşluğunun canlı davranışı (W-5) | ⚠️ VERIFICATION REQUIRED |
| U-7 | ADR-010/ADR-043 metinleri | ⚠️ VERIFICATION REQUIRED |
| U-8 | `PermissionMiddleware` test kapsamı (dosya yok) | UNKNOWN (Z-1) |

## §20 İlişkili Düğümler (Relations)

| Düğüm | İlişki |
|---|---|
| `K009-security/authentication` | Öncesi — kimlik bağlamını tüketir (`_auth`/`_auth_user`) |
| `K009-security/authorization/index` | Bu çocuğun künyesi ve 8 dosyalık şema |
| `K009-security/audit` | Red kayıtları (audit) — bu dosyada yalnız olay adı |
| `K009-security/rate-limiting` | Deneme kotası (yetki denemeleri) |
| `K0100-identitiy` | Rol/izin **tanımı** kaynağı (tek yönlü) |
| `K009-security/security-master` | Domain anayasası (üst otorite) |

## § BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[authorization-kapsam]] | kardeş | kapsam + iki motor karşılaştırması |
| [[authorization-ornekler]] | kardeş | Permission/Authorization gerçek kodu |
| [[authorization-dogrulama]] | kardeş | 20 disk kontrolü |
| [[authorization-bilesenler]] | kardeş | kademe-3 bileşen planı |
| [[authorization-web-arastirma]] | kardeş | OWASP / CWE / NIST kayıtları |
| [[authentication-anayasa]] | öncesi | kimlik kuralları |
| [[authentication-ornekler]] | öncesi | kimlik kodu |
| [[security-master]] | yukarı | K009 anayasası |
| [[security-kararlar]] | yukarı | ADR kararları |
| [[rate-limiting-kapsam]] | kardeş | kota politikası |
| [[session-security-kapsam]] | kardeş | oturum yaşam döngüsü |
| [[identity-master]] | tek yönlü | rol/izin kaynağı |
| [[transactions-anayasa]] | iskelet | anayasa şablonu |

## §21 Revizyon Günlüğü

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 20 kanıt satırı, §13 zayıf nokta kaydı, URL tablosu yok | Technical Writer (tek yazar) |
| 2026-10-15 | `parent:` yok, `parent_id: K009-security` · `group: mimari` | Technical Writer |

## §22 Sürüm ve Otorite

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Otorite | Bayram Ali / Vault Steward |
| Mod | Red Team · Human Mode · Truth Mode |
| Durum | draft (U-1..U-8 açık) |
| Erişim | `.ai/architecture/K009-security/authorization/authorization-anayasa.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
