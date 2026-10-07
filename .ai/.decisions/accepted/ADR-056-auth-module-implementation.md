---
title: "CoreMusic — ADR-056: Auth Modülü Uygulaması (RBAC modeli + Permission middleware 9. adım + login/register/reset akışları + entity/domain katmanı)"
type: "architecture-decision"
category: "security"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic auth modülü uygulama kararı: (a) RBAC modeli = `user_roles` + `user_assigned_roles` + `permission_audit` (yetki = role JSON kolonu, ayrı `permissions`/`role_permissions` tablosu YOK), (b) Permission middleware = PageRouter pipeline'ının 9. adımı (fail-closed), (c) login/register/reset akışları = `AuthService` tek kapı, (d) entity/domain katmanı = Entity + ValueObject + DTO + Repository + Contract, (e) sınır = ADR-043/047/052/058/059'a uygular ama yeniden karar vermez"
kaynak: "Disk/kod kanıtı taraması (2026-09-29: coremusic_auth.sql user_roles:58 / user_assigned_roles:80 / permission_audit:352 → PermissionMiddleware.php 57 satır IMPLEMENTED → PageRouterKernel.php:285-296 pipeline 9 → AuthService.php 320 satır → UserRepository.php 261 satır / 19 method → AuthMiddleware.php:22-27 'permissions' yazmıyor → authorization 0 üretim rotası → rbac-authorization.md:253-262 implementasyon checklist) + web araştırması (5 sorgu / ~38 adlandırılmış kaynak)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-056: Auth Modülü Uygulaması (RBAC + Permission Middleware + Akışlar + Entity Katmanı)

> **Durum:** ✅ **ACCEPTED** — **Tarih:** 2026-09-29 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-056-auth-module-implementation`
> **İlgili kararlar:** [[ADR-052-hybrid-auth-session-jwt]] (oturum/JWT zemini — bu ADR onu **uygular, yeniden karar vermez**) · [[ADR-043-auth-subdomain-consolidation]] (subdomain/cookie hizası) · [[ADR-047-login-redirect-session-bridge]] (login redirect köprüsü) · [[ADR-011-session-management]] (session yaşam döngüsü) · [[ADR-010-csrf-protection-strategy]] (CSRF — pipeline 6. adım) · [[ADR-008-bypass-auth-middleware]] (bypass = üretimde fail-closed) · [[ADR-013-rate-limiting-apcu]] (rate limit — pipeline 3. adım) · [[ADR-020-api-public-security]] (API auth çözümü) · [[ADR-004-multi-domain-spa]] (çoklu domain SPA) · [[../index.md]] · [[../../CLAUDE.md]] · [[../../raw/brain.md]]
> **Index durumu:** `.ai/.decisions/index.md` **ADR-051–ADR-060 satırlarını İÇERMEZ** — dizin 050 (satır 91) → 061 (satır 92) arasında **atlıyor**. Bu işlemde index.md'ye **yeni satır eklenmedi** (report-only — In-Place Refactoring + SRP); satır ekleme **bir sonraki vault reset'ine ertelenmiştir** (§5.1 adım 7).
> **Kaynaksız numara boşlukları:** **ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, ADR-058, ADR-059, ADR-060** diskte dosya olarak **YOK**. Bu ADR bu numaraları **doldurmaz**; yalnızca sınır koyar.
> **⚠️ VERIFICATION REQUIRED — ADR-058 ve ADR-059:** `.ai/.decisions/accepted/ADR-058-merkezi-auth.md` ve `.ai/.decisions/accepted/ADR-059-mfa.md` için `Test-Path = False`. Bu iki karar **diskte mevcut değil**; bu ADR'de düz metin referans olarak anılır, **wiki-link yapılmaz**. Varlıkları **kanıtlanmamıştır**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; bu dosya **yeni** bir accepted ADR'dir (kural 4: yeni ADR'ler ADR-088+ aralığına ayrılmış olsa da, bu dosya **arşiv belgesinin** atadığı ADR-056 numarasını doldurur — bkz. §5.1 adım 6'daki numara boşluğu notu).

---

## 1. Bağlam (Context)

Bu karar, CoreMusic auth modülünün **ne olduğu**nu (kod + veritabanı kanıtı üzerinden) tek bir yerde toplamak ve **ne olmadığını** (RBAC'ın henüz tam çalışır hali) dürüstçe işaretlemek için alındı. `.ai/archives/prompt2-auth-2026-08-15.md` arşivi bu konuyu **6 kez** ADR-056'ya devretmiştir (satır 37, 98, 183, 270, 533, 680, 687); hedef dosya diskte **yoktu**. Bu ADR o boşluğu doldurur.

### 1.1 Mevcut Durum

**Tablo A — RBAC veritabanı şeması** (kaynak: `.ai/sources/.sql/mysql/coremusic_auth.sql`, 25688 bayt)

| Varlık | Satır | Kanıt | Durum |
|--------|-------|-------|-------|
| `user_roles` | :58 | `role_name` UNIQUE, `permissions JSON` (:62), `is_system`, soft-delete | ✅ IMPLEMENTED |
| `user_assigned_roles` | :80 | user↔role M2M, FK'lar, `UNIQUE(user, role)` | ✅ IMPLEMENTED |
| `permission_audit` | :352 | `grant/deny/revoke/check` olay kaydı | ✅ IMPLEMENTED |
| `roles` / `permissions` / `role_permissions` tabloları | — | dosyada **geçmiyor** | ❌ YOK — yetki, role'ün JSON kolonunda |
| `free_user` seed satırı | :239 | dosyadaki tek `INSERT` = `credential_keys` | ❌ YOK |

**Tablo B — Permission middleware (pipeline 9. adım)**

| Kanıt | Değer | Durum |
|-------|-------|-------|
| `shared/src/Middleware/PermissionMiddleware.php` | 57 satır, başlık "L1 — Pipeline #9" | ✅ IMPLEMENTED |
| Pipeline sırası | `shared/src/PageRouter/PageRouterKernel.php:285-296` → OriginCheck, Cors, RateLimiter, SecurityHeaders, SessionManager, Csrf, BypassAuth, Auth, **Permission (9)**, Validation (10) | ✅ IMPLEMENTED |
| Boş oturum davranışı | `empty($auth['userId'])` → **tamamen atlar** (:23-25) | ✅ IMPLEMENTED |
| Rol kontrolü | strict eşitlik (:31) | ✅ IMPLEMENTED |
| Yetki kontrolü | `in_array($permission, $auth['permissions'])` (:39) → 403 | ✅ IMPLEMENTED |
| `_auth['permissions']` doldurucu | `AuthMiddleware.php:22-27` yalnız `userId/role/username` yazar | ❌ **BOŞLUK** |
| Üretim rotasında `requiredPermission` | **0 atama** (yalnız test: `tests/Unit/PageRouter/SpaRouteTest.php:31`, `AuthGuardTest.php:92,101`) | ⚠️ **0-YÜZEY** |
| `MM_Permissions` ataması | hep `[]` (`BypassAuthMiddleware.php:53`, `home.coremusic.net/config/bootstrap.php:57`, `home.coremusic.net/include/Stream/MusicStreamHandler.php:135`) | ❌ **BOŞLUK** |
| API tarafı 2. uygulama | `shared/src/Api/Middleware/AuthorizationMiddleware.php` (111 satır) `_auth_user['roles'|'permissions']` okur; ama `AuthenticationMiddleware.php:62-66` yalnız `id/authenticated/method` yazar | ⚠️ **PARÇALI** |
| 3. uygulama | `shared/src/PageRouter/AuthGuard.php:36,44` + `PageRouterHelper.php:16-27` (`MM_UserRole`, `MM_Permissions`) | ⚠️ **ÇAKIŞMA** |
| Test | `shared/tests/Middleware/` altında **`PermissionMiddlewareTest` YOK** (yalnız Bypass×2, Csrf, MiddlewarePipeline, RateLimiter, SessionManager) | ❌ **TEST YOK** |

**Tablo C — Auth akışları** (kaynak: `auth.coremusic.net/include/Service/AuthService.php`, 320 satır)

| Akış | Konum | Durum |
|------|-------|-------|
| `final class AuthService implements IAuthService` | :28 | ✅ IMPLEMENTED |
| `loginWithRequest` | :55 | ✅ IMPLEMENTED |
| `registerWithRequest` | :115 | ✅ IMPLEMENTED |
| `login` / `register` / `logout` | :188 / :194 / :200 | ✅ IMPLEMENTED (`logout` — ADR-052 bulgusuyla uyumlu) |
| `isAuthenticated` / `getCurrentUser` | :206 / :211 | ✅ IMPLEMENTED |
| `requestPasswordReset` / `resetPassword` | :225 / :253 | ✅ IMPLEMENTED |
| `validateSessionKey` | :281 | ✅ IMPLEMENTED |
| Kontrat | `shared/src/Contracts/Auth/IAuthService.php` (login/register/logout/resetPassword) | ✅ IMPLEMENTED |

**Tablo D — Entity / domain katmanı** (`auth.coremusic.net/`)

| Katman | Dosya | Ölçü | Durum |
|--------|-------|------|-------|
| Entity | `include/Domain/Entity/User.php` | 76 satır | ✅ IMPLEMENTED |
| ValueObject | `UserId`, `Password`, `Gender`, `Email` | 4 adet | ✅ IMPLEMENTED |
| DTO | `RegisterRequest`, `LoginRequest`, `AuthResponse` | 3 adet | ✅ IMPLEMENTED |
| Repository | `include/Repository/UserRepository.php` | 261 satır / 19 method (`saveResetToken` :…, `findValidResetToken`, `updatePassword`, `markResetTokenUsed`, `saveAuthKey`, `findValidAuthKey`, `markAuthKeyUsed`) | ✅ IMPLEMENTED |
| DI | `Container/AuthContainer.php` | 76 satır | ✅ IMPLEMENTED |
| Controller | `Controller/AuthController.php` | 316 satır | ✅ IMPLEMENTED |
| Oturum | `include/Service/SessionManager.php` | `setAuthUser` :16-26 / `setRegisteredUser` :34 | ✅ IMPLEMENTED |
| Middleware (auth alt alanı) | 6 adet | — | ✅ IMPLEMENTED |
| Sayfalar | `login.php` 13881 B · `register.php` 21555 B · `forgot-password.php` 6861 B · `reset-password.php` 7906 B · `logout.php` 5419 B · `select-gender.php` · `set-gender.php` | 7 adet | ✅ IMPLEMENTED |
| Route tanımı | `shared/config/auth-routes.php` | 1687 bayt | ✅ IMPLEMENTED |
| Kontratlar | `IUserRepository`, `ISessionManager` | 2 adet | ✅ IMPLEMENTED |
| Kritik tutarsızlık | `UserRepository::USER_COLUMNS` (:20) rol kolonu **içermiyor**; login → `setAuthUser()` **`MM_UserRole` yazmıyor**; yalnız `setRegisteredUser()` (:34) yazar (`?? 'free'`); `create()` `role_name: 'free_user'` döndürür (:153) | — | ⚠️ **ROL OTURUMDA KAYIP** |

**Mimari tasarım referansı:** `.ai/architecture/k6-guvenlik/rbac-authorization.md` (262 satır) — rol hiyerarşisi `super_admin > admin > editor > user`, yetki biçimi `resource:action:scope`, yetki matrisi. §"Durum: Implementasyon" (:253-262): `[x]` tasarım; `[ ]` "RBAC middleware implemente edilecek", `[ ]` permission seed data, `[ ]` role-management UI, `[ ]` ownership checks, `[ ]` API key authorization, `[ ]` integration test. Güvenlik listesinde 30 sn authz cache TTL (:241).

### 1.2 Sorun Tanımı

Üç sorun üst üste binmektedir:

1. **Yetki verisi üretilmiyor.** DB şeması rolleri tutuyor (`user_roles.permissions JSON`), middleware yetkiyi okuyor (`in_array`), ama **aradaki bağlantı kopuk**: `AuthMiddleware` oturuma `permissions` yazmıyor, üretim rotalarının **hiçbiri** `requiredPermission` tanımlamıyor, `MM_Permissions` her yerde `[]`. Sonuç: `requiredPermission` tanımlı her rota, **giriş yapmış kullanıcı için bile 403** döner (fail-closed — doğru davranış, ama rota kilitli).
2. **Üç ayrı yetkilendirme uygulaması** var (PageRouter `PermissionMiddleware`, API `AuthorizationMiddleware`, `PageRouter/AuthGuard` + `PageRouterHelper`). Her biri farklı oturum anahtarını okuyor (`_auth['permissions']` vs `_auth_user['roles']` vs `MM_Permissions`). Tek bir SSOT yok.
3. **Rol, oturumda tutarsız.** Kayıttan sonra `MM_UserRole` dolu; **taze login'de dolu değil**. `AuthMiddleware` rol yoksa `'user'` varsayıyor. `free_user` seed satırı da SQL dosyasında yok → `UserRepository::create` rol satırını bulamayınca atamayı sessizce atlıyor (`if (!empty($roleRow))`, :136).

### 1.3 Web'den Araştırması Raporu & Sonuçlar

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte mevcut, v7.2.0) · 5 sorgu · **~38 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "RBAC vs ABAC authorization model comparison NIST SP 800-162 role explosion" · (2) "role explosion RBAC permission model pitfalls role mining best practices" · (3) "authorization middleware pipeline order deny-by-default every request OWASP" · (4) "permission caching invalidation broken access control OWASP Top 10 2025" · (5) "PHP domain driven design auth service repository authorization layers" |
| Web Search **Konusu** | RBAC'ın doğru modeli mi, yetki middleware'inin pipeline konumu ve fail-closed kuralı, yetki verisinin kaynak gerçeği (tablo vs JSON), cache/invalidation ve kırık erişim kontrolü riski, PHP auth mimarisinin katman sınırı |
| Web Search **Bağlamı** | CoreMusic'te yetki `user_roles.permissions` JSON'unda, middleware pipeline'da 9. adım, üretim rotalarında 0 kullanım; hangi modelin sürdürülebilir olduğu ve middleware'in neden deny-by-default olması gerektiği |
| Web Search **Kısa Açıklama** | NIST RBAC standardı (SP 800-162) ve NCCoE SP 1800-3a, basit/hiyerarşik RBAC'ın ölçeklenen doğru model olduğunu; Coyne-Weil (IEEE IT Pro 2013) ve Kuhn-Coyne-Weil (IEEE Computer 2010) ise rol patlamasının (role explosion) kaçınılmaz olmadığını, rol madenciliği ile önlenebileceğini gösteriyor. OWASP Authorization Cheat Sheet her istekte yetki denetimi + deny-by-default + test zorunluluğunu, ASP.NET Core `AuthorizationMiddleware` ve Laravel middleware sıralaması ise auth'nin authz'dan **önce** gelmesini şart koşuyor. OWASP Top 10 2025 A01 (Broken Access Control) ve A07 (Identification & Authentication Failures) ile CVE-2025-69202 (axios cache auth bypass) gibi örnekler, cache'lenmiş yetkinin yanlış servis edilmesinin doğrudan güvenlik açığı olduğunu gösteriyor. |
| Web Search **Uzun Açıklama** | Kaynaklar dört eksen üzerinde buluşuyor. **(i) Model seçimi:** NIST SP 800-162 (RBAC program guideline), SP 800-178 (XACML/ABAC karşılığı), NCCoE SP 1800-3a, IBM (2026-04-15), Unosecur, guptadeepak (2026) ve Splunk (2025-01-08) basit + hiyerarşik RBAC'ın CoreMusic ölçeğinde doğru tercih olduğunu; ABAC'ın policy engine maliyeti getirdiğini belirtiyor. **(ii) Riskler:** NISTIR 6192, Evolveum (2025-08-18), InfoSec SE 151333 ve TechPrescient (2026) rol patlamasını; Microsoft Entra/Azure best practices ise rol sayısını 5-10 bandında tutmayı öneriyor — CoreMusic'te 4 rol (`super_admin/admin/editor/user`) + `free_user` bunun altında, yani rol patlaması riski şu an **düşük**. **(iii) Middleware kalıbı:** OWASP Authorization Cheat Sheet (deny-by-default, her istek, test), spatie laravel-permission, ASP.NET Core `AuthorizationMiddleware` (source.dot.net), Mastering Laravel (2024-03-22) — "auth önce, authz sonra" sırası ADR-052'nin 10/10 pipeline onayıyla **birebir** uyumlu; Vulcan/MITRE deny-by-default, dev.to (2025-10-06) auth-before-authz. **(iv) Cache + katman:** OWASP BLA9:2025 (cache poisoning/bypass), CWE-1436, CVE-2025-69202, CVE-2025-10611 → cache TTL + invalidation mutlaka olmalı; Permit Authorization Academy (GitLab katmanları), Keycloak PAP/PDP/PEP/PIP, OWASP Microservices cheat sheet, Stack Overflow DDD AuthenticationService, League OAuth2 requirements → `AuthService` = servis katmanı, `UserRepository` = altyapı, entity = domain. |
| Web Search **Paragraf Veri Uzun** | 5 sorgu, toplam ~38 adlandırılmış kaynak; her iddia kaynakla eşleştirildi. Birincil standartlar: NIST SP 800-162 (RBAC), NIST SP 800-178, NISTIR 6192 (RBAC workshop), NCCoE SP 1800-3a. Hakemli: Coyne & Weil, *IEEE IT Pro* 2013; Kuhn, Coyne & Weil, *IEEE Computer* 2010. Sektör: OWASP Authorization Cheat Sheet, OWASP Top 10 2025 (A01/A07), OWASP Broken Access Control, OWASP BLA9:2025, OWASP Microservices Cheat Sheet. Ürün: Microsoft Entra/Azure RBAC best practices, spatie laravel-permission, ASP.NET Core `AuthorizationMiddleware` (source.dot.net), Keycloak PAP/PDP/PEP/PIP, Permit.io / Permit Authorization Academy, GitLab authorization layers. Analiz: IBM (2026-04-15), Splunk (2025-01-08), Unosecur, TechPrescient (2026), Evolveum (2025-08-18), Mastering Laravel (2024-03-22), dev.to (2025-10-06), guptadeepak (2026), Stack Overflow DDD AuthenticationService, League OAuth2 Server requirements, PHP 8.4 notları. Zafiyet: CWE-1436, CVE-2025-69202, CVE-2025-10611. |
| Web Search **Sonucu** | (1) CoreMusic'in 4+1 rolü ve `resource:action:scope` biçimi, NIST basit/hiyerarşik RBAC modeline **uyuyor**; ABAC'a geçiş gerekçesi yok. (2) ADR-052 ile onaylı 10 adımlı pipeline'da Permission'ın 9. konumu **doğru** (auth önce, authz sonra). (3) OWASP deny-by-default kuralı mevcut fail-closed davranışı **onaylıyor**. (4) Asıl açık, mimari değil **veri akışı**: `permissions` oturuma yazılmıyor + üretim rotası 0 atama. (5) Yetki cache'i için OWASP/CVE kanıtı TTL + invalidation'ı **zorunlu** kılıyor (rbac-authorization.md:241'deki 30 sn TTL ile uyumlu). (6) DDD katmanları mevcut yapıyla **örtüşüyor**. |
| Web Search **Alınan Karar** | (a) RBAC modeli: `user_roles.permissions JSON` + `user_assigned_roles` **korunur** (şema değişikliği YOK); rol hiyerarşisi 4 rol; `resource:action:scope` biçimi standart. (b) Permission middleware: pipeline **9. adım** olarak kalır, **deny-by-default + fail-closed** doğrulanır; boş oturumda atlama davranışı **korunur** (anonim = yetkisiz, 403 değil 401 yönlendirmesi router'ın işi). (c) Auth akışları: `AuthService` tek kapı, kontrat `IAuthService` — **PLANNED** iyileştirme = login'de rolün de oturuma yazılması. (d) Entity/domain katmanı: mevcut Entity/VO/DTO/Repository/Contract **korunur**. (e) Sınır: ADR-043/047/052/058/059'a uygular, yeniden karar vermez. |
| Web Search **Sonuç** | Karar **destekleniyor**: model, pipeline konumu, fail-closed ve katman sınırı bağımsız kaynaklarla **çapraz doğrulandı**. Kalan iş **uygulama** (seed data, `permissions` yazımı, üretim rotası ataması, unified yetkilendirme servisi, `PermissionMiddlewareTest`) — hepsi §5.1'de PLANNED olarak listelendi. **VERIFICATION REQUIRED:** ADR-058/059 diskte yok. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnızca okur ve atıf yapar |
| In-Place Refactoring | Dosya adı değişikliği **yok**; `index.md`'ye satır eklenmedi (report-only) |
| UTF-8 yazım protokolü | Tüm vault yazımları `vault-utf8-writer.mjs` üzerinden; log.md yalnız `append` |
| Hallucination sweep | Diskte olmayan dosyaya wiki-link **yok**; ADR-058/059 = düz metin + ⚠️ |
| Kanıt = kod | Yalnız `Test-Path`/satır numarası ile doğrulanan iddialar; uygulanmamış şey "PLANNED" veya "0-YÜZEY" |
| Debate | `debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — §7.1'e debate kaydı girildi; 3 şart §5.3'te bağlayıcı |

---

## 2. Karar (Decision)

Auth modülünün uygulaması **aşağıdaki beş başlıkta** sabitlenir:

**(a) RBAC modeli — şema + kod.** Kaynak gerçeği `.ai/sources/.sql/mysql/coremusic_auth.sql`'deki üç tablodur: `user_roles` (rol + `permissions JSON`), `user_assigned_roles` (M2M), `permission_audit` (denetim). **Ayrı `permissions` / `role_permissions` tablosu YOK** ve bu kararla **eklenmez** (yetenek olgunlaşana kadar). Yetki biçimi `resource:action:scope`. Rol hiyerarşisi `super_admin > admin > editor > user` + `free_user`. Seed verisi **eksik** (`free_user` satırı yok) → §5.1 adım 3.

**(b) Permission middleware — 9. pipeline adımı.** `PageRouterKernel.php:285-296` sırası **dokunulmaz** (ADR-052'nin 10/10 onayı): `Permission = 9`, `Validation = 10`. Davranış: boş oturum → **atla** (anonim zaten yetkisiz); dolu oturum → strict rol eşitliği + `in_array` yetki; uyuşmazlık → **403 halt** (fail-closed / deny-by-default). RBAC testi `rbac-authorization.md:253-262` checklist'ine göre **integrasyon testi** ile kapanacak.

**(c) Auth akışları — `AuthService` tek kapı.** `loginWithRequest` / `registerWithRequest` / `requestPasswordReset` / `resetPassword` / `logout` akışları `auth.coremusic.net/include/Service/AuthService.php` içinde tek sorumluluk noktasıdır; kontrat `shared/src/Contracts/Auth/IAuthService.php`. Sayfa katmanı (`login.php` vb.) yalnızca `AuthController`'a çağrı yapar, doğrudan `UserRepository` kullanmaz.

**(d) Entity/domain katmanı.** `User` entity + 4 ValueObject (`UserId`, `Password`, `Gender`, `Email`) + 3 DTO (`RegisterRequest`, `LoginRequest`, `AuthResponse`) + `UserRepository` (261 satır / 19 method) + 2 kontrat (`IUserRepository`, `ISessionManager`) + `AuthContainer` DI. **Yeni katman/servis eklenmez**; mevcut yapı korunur.

**(e) Sınır — uygular ama yeniden karar vermez.** Bu ADR **uygulama kanıtı**dır, politika değil:
- **[[ADR-052-hybrid-auth-session-jwt]]** → hibrit session/JWT kararı **onun**; bu ADR yalnız pipeline sırasını referanslar.
- **[[ADR-043-auth-subdomain-consolidation]]** → cookie domain / Origin / CSRF / nonce **onun**; burada tekrarlanmaz.
- **[[ADR-047-login-redirect-session-bridge]]** → imzalı tek kullanımlık köprü token'ı **onun**; `validateSessionKey` (:281) bu kararı **uygular**.
- **ADR-058 (merkezi auth)** ve **ADR-059 (MFA)** → ⚠️ **VERIFICATION REQUIRED — diskte YOK** (`Test-Path = False`). Bu ADR bu ikisine **atanmış konuyu talep etmez**; merkezî auth ve MFA kararları bu numaralara **ayırtılır**.
- **[[ADR-008-bypass-auth-middleware]]** → bypass'ın üretimde fail-closed olması; Permission middleware bunu **destekler** (boş `userId` → atla, geçiş yok).
- **[[ADR-010-csrf-protection-strategy]] / [[ADR-013-rate-limiting-apcu]] / [[ADR-020-api-public-security]]** → pipeline 6 / 3 / API auth; ilgili ama **bu ADR'nin konusu değil**.

### 2.1 Neden Bu Seçenek?

Mevcut üç yetkilendirme uygulamasını birleştirmek yerine **tek SSOT'u (permission verisinin kaynağı) netleştirip** pipeline'ı olduğu gibi bırakmak, en düşük riskli yoldur: şema zaten doğru (NIST basit/hiyerarşik RBAC ile uyumlu), middleware zaten fail-closed, pipeline sırası zaten ADR-052 ile dondurulmuş. Eksik olan şey **tasarım değil veri akışı** — `permissions`'ın oturuma yazılması ve rotaların `requiredPermission` ile donatılması. Bu, kod yazmaktan çok **bağlantıyı tamamlamak** demek.

### 2.2 Teknik Detaylar

1. **Yetki okuma yolu (PageRouter):** `AuthMiddleware` oturuma `permissions` yazmalı → `PermissionMiddleware:::39` `in_array` bunu okur. **Bug:** `AuthMiddleware.php:22-27` bu anahtarı **yazmıyor** → `requiredPermission` tanımlı her rota 403. **PLANNED.**
2. **Yetki okuma yolu (API):** `AuthenticationMiddleware.php:62-66` → `_auth_user` yalnız `id/authenticated/method`; `AuthorizationMiddleware` ise `roles`/`permissions` bekliyor → **parçalı**. **PLANNED.**
3. **Üçüncü okuma yolu:** `PageRouter/AuthGuard.php:36,44` + `PageRouterHelper.php:16-27` (`MM_UserRole`, `MM_Permissions`) — `MM_Permissions` her yerde `[]`. **PLANNED.**
4. **Rol oturumdaki boşluk:** login → `setAuthUser()` `MM_UserRole` yazmıyor; yalnız `setRegisteredUser()` yazıyor; `AuthMiddleware` rol yoksa `'user'` varsayıyor. **PLANNED.**
5. **Seed boşluğu:** `coremusic_auth.sql` içinde tek `INSERT` = `credential_keys` (:239); `free_user` rolü **yok** → `UserRepository::create` sessizce atlıyor (`if (!empty($roleRow))`, :136). **PLANNED.**
6. **Test boşluğu:** `shared/tests/Middleware/` altında `PermissionMiddlewareTest` **yok**. **PLANNED.**
7. **Cache (tasarım gereği):** `rbac-authorization.md:241` → 30 sn authz cache TTL; OWASP/CVE kanıtı invalidasyonun **zorunlu** olduğunu gösteriyor. Henüz uygulanmadı → **PLANNED.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **RBAC + ayrı `permissions` / `role_permissions` tabloları** | Normalleştirilmiş (BCNF'e uygun), toplu yetki güncelleme kolay, matris sorgusu kolay | Şema değişikliği (migration), mevcut `permission_audit` ile hizalama, seed'i baştan kurmak, `ADR-003`/`ADR-040` DB otoritesi zinciriyle ek işlem | Mevcut 3 tablo **çalışıyor** ve yetki tek yerde (`user_roles.permissions`) — YAGNI. Tasarım `rbac-authorization.md`'de `[x]` işaretli; eksik olan seed/middleware/test, şema değil |
| 2 | **ABAC (attribute-based, policy engine)** | Kapsam/sahiplik kuralları (ownership) için esnek, `resource:action:scope`'u politika olarak ifade eder | Policy engine maliyeti, her istekte evaluation gecikmesi, öğrenme eğrisi, 4 rol için aşırı | NIST SP 800-162 + IBM/Unosecur/Splunk kaynakları ölçek için gereksiz olduğunu; OWASP ve Keycloak PAP/PDP/PEP modelinin bile temelde rol kullandığını gösteriyor. Rol patlaması riski zaten düşük (4+1 rol) |
| 3 | **Yetkiyi yalnızca API'de (`AuthorizationMiddleware`) uygulamak, PageRouter'ı atlamak** | Tek uygulama, kod azalır | PageRouter (SSR sayfaları) **tamamen yetkisiz** kalır → A01 Broken Access Control; iki farklı erişim yüzeyi | CoreMusic'in çoğu yüzeyi PageRouter'dan geçiyor; OWASP deny-by-default "her istek" kuralı ihlal edilir. Ayrıca `AuthorizationMiddleware`'in kendisi de `_auth_user` yazımı eksik |
| 4 | **`PermissionMiddleware`'i kaldırmak (9. adımı düşür)** | Pipeline 9 adım olur, daha az kod | ADR-052'nin **10/10** onayı ihlal; fail-closed garantisi kalkar; A01/A07 doğrudan tetiklenir | Donmuş sayılamayacak kadar yakın bir karar (ADR-052 accepted, 2026-09-29) — kaldırma **yeni ADR** ister, bu ADR kapsamı dışındadır |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- RBAC şeması **değişmeden** karar altına alındı — migration riski sıfır.
- Pipeline sırası ADR-052 ile **çapraz doğrulanmış** durumda (9 = Permission, 10 = Validation).
- Fail-closed / deny-by-default davranışı OWASP ile **bağımsız kaynaktan** onaylandı; mevcut 403 davranışı bir bug değil **doğru kalıp**.
- DDD katmanları (Entity/VO/DTO/Repository/Contract) mevcut ve tutarlı — yeniden tasarım gerekmiyor.
- Boşluklar **net ve sayısal** olarak listelendi (7 adım): hangi işin kaldığı tartışmasız.

### 4.2 Olumsuz Sonuçlar

- ADR-058/059 diskte olmadığı için merkezî-auth ve MFA kapsamı **belirsiz** kaldı (⚠️ VERIFICATION REQUIRED).
- `index.md` hâlâ 051–060 satırlarını içermiyor; bu ADR **dizinde görünmez** (report-only, §5.1 adım 7'ye ertelendi).
- Üç ayrı yetkilendirme uygulaması **devam ediyor** — birleştirme PLANNED olarak kaldı.
- `debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — debate **tamamlandı**; kabul 3 şartla koşulludur (§5.3), Arch Lead onayı ⏳ bekliyor.
- ADR-056 numarası arşiv tarafından kullanılmış olsa da seride 051–060 boşluğu **dokunulmadı**; numara çakışması riski kasıtlı olarak raporlandı (§5.1 adım 6).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Role explosion** — rol sayısı matsız büyür | Düşük | Orta | 4+1 rol bandı korunur; `resource:action:scope` ile yeni yetki = yeni satır, yeni rol değil (NISTIR 6192 / Evolveum uyarısı) |
| **Permission sızıntığı** — `permissions` oturuma yazılırken yanlış kaynak okunur | Orta | Yüksek | Tek SSOT: `user_roles.permissions`; üç uygulama tek servise bağlanacak (§5.1 adım 4) |
| **Stale cache** — 30 sn TTL içinde kaldırılmış yetki kullanılır | Orta | Yüksek | OWASP/CVE kanıtı → invalidasyon zorunlu; rol değişimi → oturum yetkisi yeniden çekilir (§5.1 adım 5) |
| **Middleware bypass** — 9. adım atlanan yol | Düşük | Yüksek | `PageRouterKernel` sırası sabit + `BypassAuth` üretimde fail-closed ([[ADR-008-bypass-auth-middleware]]) + `PermissionMiddlewareTest` (§5.1 adım 6) |
| **Seed yokluğu → sessiz rol atlaması** | **Yüksek** | Orta | `free_user` seed satırı eklenecek; `create()`'deki sessiz `if (!empty($roleRow))` dalı hata/log'lanacak (§5.1 adım 3) |
| **Rol oturumda kayıp** (login'de `MM_UserRole` boş) | **Yüksek** | Orta | `setAuthUser()` rolü de yazacak; varsayılan `'user'` kaldırılacak (§5.1 adım 2) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | `PermissionMiddleware` davranışını **dokümante et**: boş oturum atlar, dolu oturum strict rol + `in_array`, uyuşmazlık 403. Kod **değiştirilmez** (IMPLEMENTED kanıtı). | Vault Steward | 0.5 gün |
| 2 | **PLANNED:** `AuthMiddleware.php:22-27` → oturuma `permissions` yaz; `SessionManager::setAuthUser()` rolü de yazsın (`MM_UserRole`), varsayılan `'user'` kaldır. | Backend | 1 gün |
| 3 | **PLANNED:** `coremusic_auth.sql`'e `free_user` rol seed satırı ekle; `UserRepository::create` :136'daki sessiz `if (!empty($roleRow))` dalını log'a bağla. | Data | 0.5 gün |
| 4 | **PLANNED:** Üç yetkilendirme uygulamasını (PageRouter `PermissionMiddleware`, API `AuthorizationMiddleware`, `PageRouter/AuthGuard`) **tek yetki servisine** bağla; API'de `AuthenticationMiddleware.php:62-66` → `roles`/`permissions` yazsın. | Backend | 2 gün |
| 5 | **PLANNED:** Yetki cache'i + invalidasyon (`rbac-authorization.md:241` → 30 sn TTL); rol/perm değişikliğinde oturum yetkisi yeniden çekilir. | Backend | 1 gün |
| 6 | **PLANNED:** `shared/tests/Middleware/PermissionMiddlewareTest.php` yaz (fail-closed, rol eşitsizliği, yetki yok, boş oturum, bypass) + `rbac-authorization.md` checklist'indeki `[ ]` maddelerini kapat. **Not:** ADR-051/053/054/055/057/060 numaraları **kaynaksız boşluk** olarak atlandı — bu ADR o numaralara dokunmaz; `index.md` 051–060 boşluğu nedeniyle bu ADR'nin dizin satırı da **eklenmedi**. | Testing | 2 gün |
| 7 | **ERTELENEN:** `.ai/.decisions/index.md`'ye ADR-056 satırı ekleme **bir sonraki vault reset'ine** ertelendi (report-only; In-Place Refactoring + SRP). | Vault Steward | sonraki reset |

### 5.2 Geri Dönüş Planı

Bu ADR **kod/değişiklik üretmez** (kanıt raporlamadır) → doğrudan geri dönüş riski yoktur. Uygulama adımları (2–6) için:

1. **Adım 2/3 geri dönüşü:** `AuthMiddleware`'e yazılan `permissions` anahtarı ve `setAuthUser()` rol yazımı **geri alınabilir** — `PermissionMiddleware` zaten `empty($auth['permissions'])` durumunda `in_array` = `false` → 403 üretir, yani anahtar kalksa bile davranış **daha katı** değil, **eskisi gibi** olur (fail-closed korunur). Yalnızca `requiredPermission` tanımlı rotalar etkilenir (bugün üretimde 0 tane).
2. **Adım 4 geri dönüşü:** Üç uygulama birleştirilmeden önce her birinin davranışını karakterize eden test yazılır; servis geri alınırsa testler eski yola döner.
3. **Adım 5 geri dönüşü:** Cache devre dışı bırakılabilir (`TTL = 0`); doğrulama her istekte çalışır, **güvenlik düşmez**, yalnızca gecikme artar.
4. **Adım 3 geri dönüşü:** Seed satırı `DELETE` ile kaldırılabilir — ancak bu durumda `free_user` kaydı yine sessizce atlanır; rollback **önerilmez**, yerine log'lama geri alınır.
5. **Tüm adımlar** tek bir commit serisinde, ADR-052'nin pipeline sözleşmesine dokunmadan (sıra değişikliği **yok**) ilerler.

### 5.3 Debate Şartları (3 şart — bağlayıcı, 2026-09-29)

> Debate sonucu **KABUL (18/2/0)**; kabul **aşağıdaki 3 şartla** koşulludur. Şartlar kapanmadan §5.1 adımları (2–6) **tamamlanmış sayılmaz**.

| # | Şart | Kapsam | Bağlı §5.1 adımı | Sorumlu |
|---|------|--------|------------------|---------|
| 1 | **Tek yetki kaynağı + rol oturuma yazma** | **1a:** Üç yetkilendirme uygulaması (PageRouter `PermissionMiddleware` · API `AuthorizationMiddleware` · `AuthGuard`+`PageRouterHelper`) **tek yetki kaynağına** bağlanır + üretim rotalarına `requiredPermission` bağlama fazı (bugün 0-YÜZEY) · **1b:** login'de rol oturuma yazılır (`setAuthUser()` → `MM_UserRole`), oturum kurtarma yapılır, `'user'` varsayımı kaldırılır | adım 2 + adım 4 | Backend |
| 2 | **Şema tamamlama + audit bağlama** | `permissions`/`role_permissions` boşluğu kararı + `free_user` seed satırı eklenir; `permission_audit` yazımı akışa bağlanır; `create()` :136 sessiz dalı log'lanır | adım 3 | Data |
| 3 | **Deny-by-default + yetki sızıntı testi** | `PermissionMiddlewareTest` (boş oturum, fail-closed, 403) + **yetki sızıntısı** testi (yanlış role ait permission sızıyor mu) | adım 6 | Testing |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme (vault kuralları, UTF-8 protokolü) |
| [[../../raw/brain.md]] | Mimari kararlar |
| [[../../raw/WORKFLOW.md]] | Süreçler |
| [[../index.md]] | ADR dizini — **051–060 satırları eksik, bu ADR satırı eklenmedi (report-only)** |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 zorunlu iskelet (§1.3 web araştırması + 19 doğrulama kapısı) |
| [[ADR-052-hybrid-auth-session-jwt]] | Pipeline 10/10 onayı + hibrit session/JWT — bu ADR'nin **zemini** |
| [[ADR-043-auth-subdomain-consolidation]] | Cookie domain / Origin / CSRF / nonce — **sınır** |
| [[ADR-047-login-redirect-session-bridge]] | İmzalı köprü token'ı (`validateSessionKey` bunu uygular) — **sınır** |
| [[ADR-011-session-management]] | Session yaşam döngüsü (`setAuthUser` / `setRegisteredUser`) |
| [[ADR-010-csrf-protection-strategy]] | CSRF — pipeline 6. adım |
| [[ADR-008-bypass-auth-middleware]] | Bypass = üretimde fail-closed |
| [[ADR-013-rate-limiting-apcu]] | Rate limit — pipeline 3. adım |
| [[ADR-020-api-public-security]] | API auth çözümü (JWT stub `null` bulgusu) |
| [[ADR-004-multi-domain-spa]] | Çoklu domain SPA + cookie haritası |
| [[../../architecture/k6-guvenlik/rbac-authorization.md]] | Rol hiyerarşisi, `resource:action:scope`, implementasyon checklist (:253-262), 30 sn cache TTL (:241) |
| [[../../architecture/index.md]] | Mimari dizin |
| [[../../archives/prompt2-auth-2026-08-15.md]] | ADR-056'yı 6 kez referanslayan arşiv (satır 37, 98, 183, 270, 533, 680, 687) |
| ⚠️ ADR-058 (`ADR-058-merkezi-auth`) | **VERIFICATION REQUIRED — `Test-Path = False`, diskte YOK.** Düz metin referans; wiki-link yapılmadı |
| ⚠️ ADR-059 (`ADR-059-mfa`) | **VERIFICATION REQUIRED — `Test-Path = False`, diskte YOK.** Düz metin referans; wiki-link yapılmadı |

**Kod kanıtı (düz metin, wiki-link değil):** `.ai/sources/.sql/mysql/coremusic_auth.sql` · `shared/src/Middleware/PermissionMiddleware.php` · `shared/src/Middleware/AuthMiddleware.php` · `shared/src/PageRouter/PageRouterKernel.php:285-296` · `shared/src/Api/Middleware/AuthorizationMiddleware.php` · `shared/src/Api/Middleware/AuthenticationMiddleware.php` · `shared/src/PageRouter/AuthGuard.php` · `shared/src/PageRouter/PageRouterHelper.php` · `auth.coremusic.net/include/Service/AuthService.php` · `auth.coremusic.net/include/Repository/UserRepository.php` · `auth.coremusic.net/include/Service/SessionManager.php` · `auth.coremusic.net/include/Domain/Entity/User.php` · `auth.coremusic.net/Container/AuthContainer.php` · `auth.coremusic.net/Controller/AuthController.php` · `shared/config/auth-routes.php` · `shared/tests/Middleware/` · `shared/tests/Unit/PageRouter/SpaRouteTest.php:31` · `shared/tests/Unit/PageRouter/AuthGuardTest.php:92,101`

### 6.1 Debate Şartları — İlgili Dokümanlar

| Şart | İlgili doküman |
|------|----------------|
| Şart 1 (tek yetki kaynağı + rol oturuma yazma) | [[../../architecture/k6-guvenlik/rbac-authorization.md]] (checklist :253-262, cache TTL :241) · [[ADR-052-hybrid-auth-session-jwt]] (pipeline 9) · [[ADR-011-session-management]] (`setAuthUser` / `setRegisteredUser`) |
| Şart 2 (şema tamamlama + audit) | `.ai/sources/.sql/mysql/coremusic_auth.sql` (düz metin, wiki-link değil) · [[../../architecture/k6-guvenlik/rbac-authorization.md]] (permission seed) |
| Şart 3 (deny-by-default + yetki sızıntı testi) | `shared/tests/Middleware/` (düz metin) · [[ADR-008-bypass-auth-middleware]] (fail-closed) · [[../../architecture/k6-guvenlik/rbac-authorization.md]] (integration test) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | CoreMusic Vault Documentation Specialist | 2026-09-29 | ✅ |
| Tech Lead | CoreMusic Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Debate durumu | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | **3** (Tur 1 keşif · Tur 2 itiraz→çözüm · Tur 3 oylama) |
| Persona | **20** |
| Oy dağılımı | **18 kabul / 2 çekimser / 0 red** |
| Sonuç | **KABUL** — 3 şartla (§5.3); Tech Lead ✅ |
| Tech Lead | ✅ 2026-09-29 |
| Arch Lead | ⏳ (debate dışı) |

> Bu ADR **persona debate'den geçmiştir** (3 tur / 20 persona → 18/2/0 **KABUL**, 2026-09-29). Kabul **3 şartla** koşulludur (§5.3); şartlar kapanmadan uygulama adımları tamamlanmış sayılmaz. **Frozen'a geçiş YOK**.
> **Kaynaksız numara boşlukları (not):** ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, ADR-060 diskte dosya olarak **yok** ve bu ADR tarafından **atlandı** (kaynak = `.ai/.decisions/accepted/` glob taraması, 2026-09-29). Bu numaralar bu ADR'nin **kapsamı dışındadır**; her biri kendi kanıtıyla doldurulmalıdır.

#### 7.1.1 Tur 1 — Keşif (20 persona)

| Başlık | Bulgu |
|--------|-------|
| RBAC şeması | **VAR:** `user_roles` :58 (`permissions JSON` :62) · `user_assigned_roles` :80 · `permission_audit` :352 — ama ayrı `permissions`/`role_permissions` tablosu **YOK** + `free_user` seed **YOK** |
| Permission middleware | `PermissionMiddleware.php` 57 satır **IMPLEMENTED**, pipeline 9 (`PageRouterKernel:285-296`) — **ama** `AuthMiddleware:22-27` `_auth['permissions']` **hiç yazmıyor** → üretim rotalarında `requiredPermission` **0 (0-YÜZEY)**, `MM_Permissions` her yerde `[]`, `PermissionMiddlewareTest` **YOK**, **3 ayrı uygulama çakışıyor** |
| Auth akışları + entity | `AuthService.php` 320 satır / 10 method (login/register/reset/logout/validateSessionKey) + `IAuthService` **IMPLEMENTED**; Entity 76 + 4 VO + 3 DTO + Repository 261/19 + 2 kontrat + Container 76 + Controller 316 **IMPLEMENTED** |
| **KRİTİK** | login → `setAuthUser()` **`MM_UserRole` yazmıyor** → rol oturumda kayıp |
| Tasarım + kaynak | `rbac-authorization.md:253-262` tasarım `[x]`, **6 açık** (middleware / seed / UI / ownership / API-key / integration-test) · ~38 kaynak / 5 sorgu (NIST SP 800-162, OWASP deny-by-default) · ADR-058/059 diskte **YOK** → düz metin + ⚠️ VERIFICATION REQUIRED · 051/053-055/057-060 boşluk notu §5.1/§7.1'de · `index.md` 051-060 satırı **YOK** (reset'e erteli) |

**Tur 1 oyu:** 16 kabul/neutral · **4 uyarı** — DevOps: `requiredPermission` 0 şart · QA: test yok şart · Critic: `MM_UserRole` + 3 çakışma şart · DB: şema/seed şart.

#### 7.1.2 Tur 2 — İtiraz → Çözüm (4 madde → 3 şart)

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | `requiredPermission` 0 + 3 çakışan uygulama | Tek yetki kaynağı + üretim rotalarına bağlama fazı | **1a** |
| 2 | `MM_UserRole` login'de yazılmıyor | Rol oturuma yazılır (oturum kurtarma) | **1b** |
| 3 | Şema boşlukları (`permissions` tablosu + `free_user` seed) | Şema tamamlama + audit bağlama | **2** |
| 4 | Test yok | Deny-by-default + yetki sızıntı testi | **3** |

#### 7.1.3 Tur 3 — Oylama

| Kabul | Çekimser | Red | Sonuç |
|-------|----------|-----|-------|
| **18** | **2** | **0** | ✅ **KABUL** — 3 şartla (§5.3) |

---

*ADR-056 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team → Human Mode → Truth Mode*
