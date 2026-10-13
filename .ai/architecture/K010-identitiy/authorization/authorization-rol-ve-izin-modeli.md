---
node_id: K010-identitiy/authorization/authorization-rol-ve-izin-modeli
node_type: module
status: draft
parent_id: K010-identitiy/authorization
children: []
depends_on:
  - K010-identitiy
evidence: []
validation: []
open_questions: []
group: mimari
---

# authorization-rol-ve-izin-modeli — Rol ve İzin Modeli Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K010-identitiy · **Alt klasör:** `authorization` (yeni)
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-097:123/155
- **İmla koruması:** `identitiy` yazımı bilinçli korunmuştur.

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic'te **authorization** (yetkilendirme) kararını tanımlar: rol modeli (RBAC),
nispi politika (ABAC katmanı), OAuth benzeri `scope` kavramı, **deny-by-default** ilkesi ve
ihlal senaryoları. Kimlik doğrulama (authentication) ayrı düğümdür; burada karar “bu kimlik
bu eylemi yapabilir mi?” sorusudur.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Rol modeli | Rol adları, hiyerarşi yokluğu, eşitlik karşılaştırması | Rol üretimi/atribüt yönetimi (K010 account) |
| 2 | İzin (permission) | İzin adlandırma, dizide tutulması, denetim | İzin veritabanı şeması (K016-persistence) |
| 3 | Policy | Route meta üzerinden politika ifadesi | Politika dilinin kendisi (taslak, §15) |
| 4 | Scope | API anahtarı kapsamı ve IP kısıtı | API anahtarı üretimi (K010 tokens) |
| 5 | Deny-by-default | Tanımsız her şeyin reddi | Rate limit (K011 application) |
| 6 | Public yüzey | `public => true` uçların listesi | Kimlik doğrulama akışı (credentials) |
| 7 | Fail-closed | Bypass credential yoksa bypass yok | Ortam değişkeni yönetimi (K018) |
| 8 | İhlal senaryosu | Yetkisiz erişim yolları ve denetimleri | Olay yanıtı/süreç (K018) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Rol | role | Eylem kümesine toplu atanan etiket | `role` · `requiredRole` |
| İzin | permission | Tek bir eyleme ince ayrıcalık | `permission` · `requiredPermission` |
| Kapsam | scope | API anahtarı için daraltılmış yetki | `scopes` · `$requiredScope` |
| Politika | policy | Karar kuralı (kim + ne + koşul) | Route meta (`requiredRole` vb.) |
| Varsayılan red | deny-by-default | Tanımsız kaynak reddedilir | `fail-closed` yorumları |
| Fail-closed | fail-closed | Belirsizlikte reddet | `BypassAuthMiddleware` |
| Public rota | public route | Kimliksiz erişilebilir rota | `public => true` |
| Public olmayan rota | protected route | Kimlik zorunlu | `public => false` |
| Hedef (nazar) | object | Eylemin uygulandığı varlık | `user_id` sahiplik kontrolü |
| Bağlam | context | Kararı etkileyen durum (IP, cihaz) | `clientIp` · `allowedIps` |
| Yetkisiz erişim | privilege escalation | Kendi rolünün üstünde eylem | §12 ihlal senaryosu |
| IDOR | insecure direct object reference | Nesne kimliğiyle yetki aşımı | §12 |
| Yetki sızıntısı | privilege leakage | Yanıtta fazladan yetki/veri | §12 |
| Çapraz yetki | cross-tenant access | Başka kiracının kaynağı | K016 (çok kiracılık yok, §16) |

## §3 — Mimari yerleşim (kademe-3 konumu)

```text
K010-identitiy (kademe-1)
  └── authorization (kademe-2 · yeni klasör)
        └── authorization-rol-ve-izin-modeli (kademe-3 · bu dosya)
              ├─ sözleşmeyi yazar: rol · izin · scope · deny-by-default · ihlal
              ├─ kod okur: shared/src/Middleware/PermissionMiddleware.php
              ├─ kod okur: shared/src/Api/Middleware/AuthorizationMiddleware.php
              ├─ kod okur: shared/src/Repository/ApiKeyRepository.php (scopes)
              └─ test okur: BypassAuthMiddlewareFailClosedTest · AuthMiddlewareTest
```

| Katman | Dosya | Sorumluluk | Bu dosyayla ilişki |
|---|---|---|---|
| Kimlik kaynağı | `shared/src/Middleware/AuthMiddleware.php` | Oturumdan rol/izin okuma | Karar girdisi |
| API kimliği | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | API isteği kimliği | Karar girdisi (API) |
| Rol/izin denetimi | `shared/src/Middleware/PermissionMiddleware.php` | `requiredRole`·`requiredPermission` | Çekirdek RBAC |
| API denetimi | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | Public + rol denetimi | API yüzeyi |
| Scope denetimi | `shared/src/Repository/ApiKeyRepository.php` | `verify($rawKey, $clientIp, $requiredScope)` | Scope katmanı |
| Fail-closed | `shared/src/Middleware/BypassAuthMiddleware.php` | Credential yoksa bypass yok | Deny-by-default |
| Route meta | `api.coremusic.net/config/routes.php` | `public`·`requiredRole`·`requiredPermission` | Politika kaynağı |
| Test | `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | Fail-closed kanıtı | §13 |

## §4 — Model seçimi: RBAC + scope + sınırlı ABAC

| # | Model | Kullanım | Depoda karşılık | Karar |
|---|---|---|---|---|
| 1 | RBAC | Rol → rota eşlemesi | `requiredRole` denetimi | Birincil model |
| 2 | RBAC-permission | İzin → eylem eşlemesi | `requiredPermission` denetimi | İnce ayrıcalık |
| 3 | Scope (OAuth benzeri) | API anahtarı daraltması | `scopes` + `$requiredScope` | API yüzeyi |
| 4 | ABAC (nispi) | Rol + bağlam birleşimi | Sınırlı: `clientIp`·`allowedIps` | Şu an kısmi |
| 5 | Policy engine | Ayrışık politika dili | Repoda yok | ⚠️ VERIFICATION REQUIRED |
| 6 | ACL | Nesne bazlı erişim listesi | Repoda yok | K016 için açık soru |
| 7 | Macaristan (capability) | Taşınabilir yetki jetonu | Repoda yok | Gerekçe yok, YAGNI |
| 8 | RBAC hiyerarşisi | `admin > editor > user` | Repoda eşitlik var, hiyerarşi yok | §15 açık soru |

**Karar gerekçesi:** İki denetim noktası (`PermissionMiddleware` API dışı yüzey,
`AuthorizationMiddleware` API yüzeyi) aynı meta alanlarını okur; üçüncü bir motor eklemek
karar noktasını çoğaltır. YAGNI: mevcut rota sayısı `GET 11 · POST 6` düzeyindedir.

## §5 — Rol ve izin adlandırma sözleşmesi

| # | Kavram | Değer | Kaynak | Not |
|---|---|---|---|---|
| 1 | Varsayılan rol | `user` | `PermissionMiddleware` (`?? 'user'`) | Rol yoksa en düşük rola düşer |
| 2 | Rol alanı oturumda | `MM_UserRole` | `SessionKeys::` karşılığı | `AuthMiddleware` okur |
| 3 | Rol karşılaştırması | `!==` (kesin eşitlik) | `PermissionMiddleware:31` | Kısmi eşleşme yok |
| 4 | İzin dizisi | `permissions` anahtarı | `AuthMiddleware:21` | `?? []` boş dizi |
| 5 | İzin karşılaştırması | `in_array($perm, $list, true)` | `PermissionMiddleware:39` | Strict |
| 6 | İzin adı biçimi | Route meta ile birebir | Route tablosu | Büyük/küçük harf duyarlı |
| 7 | API rolü | `roles` dizisi | `AuthorizationMiddleware:63` | Dizi, tekil değil |
| 8 | Eksik rol alanı | `?? null` → denetim atlanır | Her iki middleware | Denetim atlanınca ne olur §15 |
| 9 | Scope adı | Dizide taşınır | `ApiKeyRepository` | `encodeScopes`/`decodeScopes` |
| 10 | Scope doğrulama | `verify($rawKey, $clientIp, $requiredScope)` | `ApiKeyRepository:140` | Zorunlu kapsam yoksa atlanır mı §15 |

## §6 — Karar algoritması (sözleşme)

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Rota public mi? | `public => true` | Kimliksiz devam | 2. adıma geç |
| 2 | Kimlik var mı? | `_auth` / oturum dolu mu? | 3. adıma geç | 401 `unauthenticated` |
| 3 | Rol gerekiyor mu? | `requiredRole` tanımlı | 4. adıma geç | 5. adıma geç |
| 4 | Rol eşleşiyor mu? | `userRole === requiredRole` | 5. adıma geç | 403 `role_insufficient` |
| 5 | İzin gerekiyor mu? | `requiredPermission` tanımlı | 6. adıma geç | 7. adıma geç |
| 6 | İzin dizide mi? | `in_array(strict)` | 7. adıma geç | 403 `permission_denied` |
| 7 | Scope gerekiyor mu? | `$requiredScope` verilmiş | 8. adıma geç | 9. adıma geç |
| 8 | Scope kapsıyor mu? | Anahtar kapsamı içeriyor | 9. adıma geç | 403 `scope_denied` |
| 9 | Bağlam kısıtı | IP izinli mi | Devam | 403 `ip_not_allowed` |
| 10 | Varsayılan | — | Devam | **Red (deny-by-default)** |

**Denetim kodu (repo, birebir):**

```php
Dosya: shared/src/Middleware/PermissionMiddleware.php / Satır: 31
if ($requiredRole !== null && $userRole !== $requiredRole) {
    return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
}
```

```php
Dosya: shared/src/Middleware/PermissionMiddleware.php / Satır: 39
if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
    return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
}
```

## §7 — Deny-by-default (varsayılan red)

| # | Yüzey | Varsayılan | Kaynak | Kanıt |
|---|---|---|---|---|
| 1 | Tanımsız bypass credential | Bypass yok | `BypassAuthMiddleware` | Satır 30: boş dizi |
| 2 | Tanımsız bypass yapılandırması | `next()` ile normal akış, kimlik enjeksiyonu yok | `BypassAuthMiddleware` | Satır 43 |
| 3 | `public` alanı yoksa | Public değil | Route meta (`?? false` mantığı) | `routes.php:46` (`false`) |
| 4 | Kimlik yoksa | 401 | Auth katmanı | `AuthMiddleware` |
| 5 | Rol yoksa | `user` rolü ile en düşük yetki | `PermissionMiddleware` | Satır 27 |
| 6 | İzin dizisi değilse | Red | `PermissionMiddleware:39` | `!is_array` dalı |
| 7 | Boş scope listesi | Erişim dar | `ApiKeyRepository` | encode/decode |
| 8 | Hata yönü | Fail-closed, fail-open yasak | §8 | Test: `FailClosedTest` |
| 9 | Bilinmeyen rota | 404 (yetki sorusu yok) | Routing | §16 |
| 10 | Bilinmeyen method | 405 + `Allow` | Routing | §16 |

**Fail-closed kanıtı (repo):**

```php
Dosya: shared/src/Middleware/BypassAuthMiddleware.php / Satır: 30
return []; // Fail-closed: credential eksikse bypass devre dışı
```

## §8 — Scope (API anahtarı) modeli

| # | Alan | Tip | Kaynak | Denetim |
|---|---|---|---|---|
| 1 | `scopes` | Saklı dizi (hex) | `api_keys` satırı | `encodeScopes` / `decodeScopes` |
| 2 | `key_type` | Anahtar türü | `api_keys` | Yetki genişliği |
| 3 | `rate_limit` | Anahtar başına hız | `api_keys` | 429 üretimi |
| 4 | `allowed_ips` | IP izin listesi | `encodeScopes` | Bağlam kısıtı |
| 5 | `is_active` | Pasif anahtar | `api_keys` | Red |
| 6 | `expires_at` | Bitiş | `api_keys` | Süre dolunca red |
| 7 | `key_hash` | Hash-only saklama | `api_keys` | Ham anahtar saklanmaz |
| 8 | `key_prefix` | Görünür ön ek | `api_keys` | Log/destek |
| 9 | `verify()` | Doğrulama girişi | `ApiKeyRepository:140` | `requiredScope` opsiyonel |
| 10 | `inspect()` | Salt okuma | `ApiKeyRepository:153` | Yan etkisiz denetim |
| 11 | `last_used_at` | Kullanım damgası | `api_keys` | Şüpheli kullanım |
| 12 | `requiredScope` null | Denetim atlanabilir | `verify()` imzası | §15 açık soru |

## §9 — Politika ifade biçimi (route meta)

| # | Anahtar | Tip | Okuyan | Örnek kullanım |
|---|---|---|---|---|
| 1 | `public` | bool | Auth/Authorization | `true` → kimliksiz |
| 2 | `requiredRole` | string | Authorization + Permission | Rol eşitliği |
| 3 | `requiredPermission` | string | Permission | İzin dizisi üyeliği |
| 4 | `action` | string | Controller dispatch | `me` · `login` · `forgotPassword` |
| 5 | `validation` | dizi | RequestValidation | 422 + `error.fields` |
| 6 | `cacheable` | bool | ResponseNormalization | ETag + Cache-Control |
| 7 | `cacheTtl` | int | ResponseNormalization | `max-age` |
| 8 | `service` / `handler` | string | Container resolver | Yönlendirme anahtarı |
| 9 | `implemented` | bool | RouteTable | `false` → 405 |
| 10 | Scope anahtarı | — | Repoda route meta'da yok | ⚠️ VERIFICATION REQUIRED |

**Gözlem:** `api.coremusic.net/config/routes.php` belge satırında
`requiredRole / requiredPermission : AuthorizationMiddleware RBAC` yazıyor; dosyada `=>` ile
atanmış bir `requiredRole` kaydı **yoktur** (yalnız belge satırı 15). Yani politika alanı
tanımlı ama kullanılmıyor → §15 açık soru 1.

## §10 — Public yüzey envanteri (GET 11 · POST 6)

| # | Yüzey | Method | `public` | `implemented` | Not |
|---|---|---|---|---|---|
| 1 | `/api/v1/auth` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 2 | `/api/v1/user` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 3 | `/api/v1/music` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 4 | `/api/v1/playlist` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 5 | `/api/v1/media` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 6 | `/api/v1/download` (prefix) | GET | tanımsız (prefix) | tanımsız (prefix) | Prefix fallback |
| 7 | `/api/v1/auth/login` | GET | `true` | `false` | 405 + Allow |
| 8 | `/api/v1/auth/register` | GET | `true` | `false` | 405 + Allow |
| 9 | `/api/v1/auth/forgot-password` | GET | `true` | `false` | 405 + Allow |
| 10 | `/api/v1/auth/reset-password` | GET | `true` | `false` | 405 + Allow |
| 11 | `/api/v1/auth/me` | GET | `false` | `true` | Kimlik zorunlu |
| 12 | `/api/v1/auth/login` | POST | `true` | `true` | Doğrulama var |
| 13 | `/api/v1/auth/register` | POST | `true` | `true` | Doğrulama var |
| 14 | `/api/v1/auth/forgot-password` | POST | `true` | `true` | Hız sınırı |
| 15 | `/api/v1/auth/reset-password` | POST | `true` | `true` | Token doğrulama |
| 16 | `/api/v1/auth/set-gender` | POST | `true` | `true` | Doğrulama var |
| 17 | `/api/v1/auth/logout` | POST | `true` | `true` | Oturum yıkımı |

## §11 — Repo kanıtları (gerçek kod, doğrulanmış satır)

| # | Dosya | Satır | Gözlem | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/PermissionMiddleware.php` | 22 | Auth yoksa denetim atlanır, `next()` çağrılır | `Select-Object -Index 21` |
| 2 | `shared/src/Middleware/PermissionMiddleware.php` | 31 | `requiredRole` eşitlik reddi | `Select-Object -Index 30` |
| 3 | `shared/src/Middleware/PermissionMiddleware.php` | 39 | `in_array(strict)` izin reddi | `Select-Object -Index 38` |
| 4 | `shared/src/Middleware/PermissionMiddleware.php` | 47 | `forbidden()` üretici metot | `Select-Object -Index 46` |
| 5 | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 61 | `$requiredRole` route meta okuması | `Select-Object -Index 60` |
| 6 | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 64 | `in_array` rol kontrolü | `Select-Object -Index 63` |
| 7 | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 94 | `isPublicRoute()` | `Select-Object -Index 93` |
| 8 | `shared/src/Middleware/AuthMiddleware.php` | 21 | `permissions` oturumdan okunur | `Select-Object -Index 20` |
| 9 | `shared/src/Repository/ApiKeyRepository.php` | 140 | `verify($rawKey, $clientIp, $requiredScope)` imzası | `Select-Object -Index 139` |
| 10 | `shared/src/Middleware/BypassAuthMiddleware.php` | 30 | Fail-closed: boş dizi | `Select-Object -Index 29` |
| 11 | `shared/src/Middleware/BypassAuthMiddleware.php` | 43 | Fail-closed: kimlik enjekte edilmez | `Select-Object -Index 42` |
| 12 | `api.coremusic.net/config/routes.php` | 15 | `requiredRole / requiredPermission` belgesi | `Select-Object -Index 14` |
| 13 | `api.coremusic.net/config/routes.php` | 46 | `'public' => false` | `Select-Object -Index 45` |
| 14 | `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | 41 | `testFailClosedWhenNoConstantsDefinedDoesNotInjectAuth` | `Select-Object -Index 40` |

```php
Dosya: shared/src/Api/Middleware/AuthorizationMiddleware.php / Satır: 64
if (!in_array($requiredRole, $userRoles, true)) {
    // 403 Forbidden döner
}
```

## §12 — İhlal senaryoları (Red Team)

| # | Senaryo | Saldırgan adımı | Mevcut kontrol | Kalan boşluk | Öncelik |
|---|---|---|---|---|---|
| 1 | Rol yükseltme (oturum alanı) | `MM_UserRole` sahtelendirmesi | Sunucuda oturum, istemci yazamaz | Oturum bozulursa | Yüksek |
| 2 | İzin dizisi şişirme | Oturuma fazla izin ekleme | Diziyi yalnız sunucu yazar | Giriş akışı doğrulaması §15 | Yüksek |
| 3 | IDOR (`/user/{id}`) | Başka id ile kayıt | Nesne sahiplik kontrolü ayrı düğümde | K010 account ile sınır §16 | Yüksek |
| 4 | Public rota genişletme | Yeni rota `public => true` unutulur | Review + `implemented` ayrımı | Otomatik tarama yok | Orta |
| 5 | `requiredRole` atlanır | Meta yoksa denetim yok | Belge satırı + test | Route meta denetim testi | Yüksek |
| 6 | Scope zorlaması | `requiredScope = null` çağrı | İmza opsiyonel | Zorunlu kapsam politikası | Orta |
| 7 | Pasif anahtar kullanımı | `is_active = 0` | `verify()` kontrolü | Sıkılaştırma §15 | Orta |
| 8 | Bypass credential sızıntısı | Sabit `BYPASS_*` | Fail-closed + log | Ortam değişkeni koruması | Yüksek |
| 9 | Yetki sızıntısı (yanıt) | Yanıttaki fazla alan | DTO sınırı (K011 dto) | Sızdırmazlık testi | Orta |
| 10 | 405/404 ile keşif | Method taraması | `Allow` yalnız bilinen yolda | Bilgi sızıntısı kabul edildi | Düşük |
| 11 | Method override | `X-HTTP-Method-Override` | Repoda yok (§15) | Kapalıysa sorun yok | Düşük |
| 12 | Çapraz rota CSRF | State-changing GET | CSRF tüm isteklerde | GET'te yazma yasağı | Orta |
| 13 | Hata mesajı ile rol sızıntısı | 403 gövdesi | `role_insufficient` mesajı rol adını içerir | Mesaj sadeleştirme | Düşük |
| 14 | Zamanlama ile izin taraması | `in_array` süresi | Dizi küçük, sabit zamanlı değil | Düşük risk, not | Düşük |

## §13 — Test senaryoları (kabul kriterleri)

| # | Senaryo | Beklenen | Test kaynağı | Durum |
|---|---|---|---|---|
| 1 | Credential yokken bypass | `_auth` yazılmaz | `BypassAuthMiddlewareFailClosedTest:41` | Repoda mevcut |
| 2 | Tek credential eksik | Bypass yok | `BypassAuthMiddlewareFailClosedTest:73` | Repoda mevcut |
| 3 | Bypass aktifken session | `MM_UserID` yazılmaz (fail-closed) | Aynı test | Repoda mevcut |
| 4 | Auth middleware oturum okuma | Rol/izin çıkarılır | `AuthMiddlewareTest` | Repoda mevcut |
| 5 | Pipeline sırası | Auth → Permission → Validation | `MiddlewarePipelineTest` | Repoda mevcut |
| 6 | `requiredRole` uyuşmazlığı | 403 `role_insufficient` | Eklenmedi | §15 |
| 7 | `requiredPermission` yokluğu | 403 `permission_denied` | Eklenmedi | §15 |
| 8 | İzin dizisi string değilse | 403 (fail-closed) | Eklenmedi | §15 |
| 9 | Public rota + kimlik yok | 200 | Eklenmedi | §15 |
| 10 | `public => false` + kimlik yok | 401 | Eklenmedi | §15 |
| 11 | Scope eşleşmiyor | 403 `scope_denied` | Eklenmedi | §15 |
| 12 | Pasif anahtar | 401/403 | Eklenmedi | §15 |
| 13 | IP kısıtı ihlali | 403 `ip_not_allowed` | Eklenmedi | §15 |
| 14 | Route meta'da `requiredRole` yok | Denetim atlanmasının sonucu belirsiz | Eklenmedi | §15 (P0) |

## §14 — Gözlemlenebilirlik

| # | Sinyal | Etiket | Eşik | Amaç |
|---|---|---|---|---|
| 1 | `authz_denied_total` | `code`·`route`·`role` | Ani sıçrama | Saldırı/yanlış politika |
| 2 | `authz_granted_total` | `route`·`role` | Günlük dağılım | Politika sağlığı |
| 3 | `auth_public_route_hit` | `route` | Beklenen liste dışıysa uyarı | Public sızıntısı |
| 4 | `apikey_verify_failed` | `reason` | Ani artış | Kötüye kullanım |
| 5 | `bypass_active` | `source` | 1 ise log zorunlu | Bypass gözetimi |
| 6 | `scope_denied_total` | `scope` | Tarama belirtisi | API kapsamı |
| 7 | `role_missing` | `route` | > 0 ise politika boşluğu | §15-1 |
| 8 | `authz_decision_ms` | p95 | < 5 ms | Karar maliyeti |

| # | Log alanı | Örnek | Gizlilik | Not |
|---|---|---|---|---|
| 1 | `decision` | `allow` / `deny` | Düşük | Zorunlu |
| 2 | `code` | `role_insufficient` | Düşük | Zorunlu |
| 3 | `route` | `/api/v1/auth/me` | Düşük | Zorunlu |
| 4 | `required_role` | Rol adı | Orta | Mesajda da var |
| 5 | `user_id_hash` | Hash-only | Yüksek | Ham id yasak |
| 6 | `scope` | `music:read` | Orta | API anahtarıyla ilişkili |
| 7 | `ip` | İstemci IP | Yüksek | Maskelenmeli (KVKK) |
| 8 | `request_id` | UuidV7 | Düşük | Korelasyon |

## §15 — Riskler ve açık sorular

| # | Açık soru | Neden önemli | Kanıt durumu | Önerilen kapanış |
|---|---|---|---|---|
| 1 | Route meta'da `requiredRole`/`requiredPermission` hiç kullanılmamış | Politika alanı tanımlı, atama yok | `routes.php` taraması: yalnız belge satırı 15 | Atama + test ekleme kararı |
| 2 | `requiredScope = null` ise kapsam denetimi | Zorunlu kapsam politikası belirsiz | `verify()` imzası okundu | Varsayılan red kararı |
| 3 | Policy engine gereksinimi | ABAC tam mı, kısmi mi | Repoda motor yok | YAGNI kararı ya da ADR |
| 4 | Rol hiyerarşisi | `admin > user` gerekti mi | Yalnız eşitlik var | Ürün kararı |
| 5 | Nesne sahiplik denetimi nerede | IDOR yüzeyi | K010 account'a devredildi | Sınır sözleşmesi |
| 6 | `role_insufficient` mesajında rol adı | Bilgi sızıntısı | Kod satırı okundu | Mesaj sadeleştirme |
| 7 | `PermissionMiddleware` auth yoksa atlaması | `next()` çağrısı sırasına bağlı | Satır 22 | Sıra testi |
| 8 | Method override başlığı | Yönlendirme bypassı | Repoda başlık yok | Kapalı olduğunu belgele |
| 9 | IP maskelenmesi (KVKK) | Log'da kişisel veri | §14 | Redaksiyon kuralı |
| 10 | Scope adı evreni | Adlandırma standartı | `encodeScopes` okundu | Scope kataloğu |
| 11 | Yetki önbelleklenmesi | Yanlış cache | K011 caching | Kişiselleşmiş yanıt `no-store` |
| 12 | API/sayfa iki yüzeyi | Çelişen kararlar | İki middleware | Tek karar noktası kararı |

## §16 — Komşu düğümlerle sınır

| # | Komşu | Veri yönü | Sözleşme | Çakışma riski |
|---|---|---|---|---|
| 1 | `K010-identitiy/session` | Oturum → rol/izin | `MM_UserRole`·`permissions` | Anahtar adı değişimi |
| 2 | `K010-identitiy/credentials` | Kimlik doğrulama sonucu | Başarılı giriş → rol | Çift kontrol |
| 3 | `K010-identitiy/tokens` | Token → kimlik | `jti` + rol claim | Claim çelişkisi |
| 4 | `K010-identitiy/mfa` | Step-up gereksinimi | Yüksek riskli eylemde ikinci faktör | Yeni politika bayrağı |
| 5 | `K011-application/routing` | Route meta → karar | `requiredRole`·`implemented` | Meta yoksa denetim yok |
| 6 | `K011-application/dto` | Yanıt sızdırmazlığı | Yetki dışı alan çıkarılır | Yetki sızıntısı |
| 7 | `K009-security` | OWASP gereksinimleri | Fail-closed · least privilege | Veto yetkisi |
| 8 | `K016-persistence` | Nesne sahiplik sorguları | IDOR kontrolü | Sınır belirsizliği |
| 9 | `K018-operasyon` | Denetim logu | §14 alanları | PII sızıntısı |
| 10 | `K013-integraion` | Dış servise yetki | Scope eşlemesi | İmla: `integraion` korunur |

## §17 — Web araştırması (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Authorization, authentication'dan ayrı bir karar katmanıdır; 403 kimlik değil yetki reddidir | https://datatracker.ietf.org/doc/html/rfc9110 | 2026-10-13 | Doğrulandı (HTTP 200) |
| Bulut RBAC'inde rol, eylem kümesine atanan temel yetki birimidir | https://learn.microsoft.com/en-us/azure/role-based-access-control/overview | 2026-10-13 | Doğrulandı (HTTP 200) |
| 403 Forbidden, istek anlaşıldığı ancak yetkisiz olduğu durumu bildirir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/403 | 2026-10-13 | Doğrulandı (HTTP 200) |
| `Authorization` başlığı kimlik bilgisi taşır; loglanmamalıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Authorization | 2026-10-13 | Doğrulandı (HTTP 200) |
| Token tabanlı yetkilendirmede erişim token'ı scope ile daraltılır | https://datatracker.ietf.org/doc/html/rfc6750 | 2026-10-13 | Doğrulandı (HTTP 200) |
| ASP.NET Core yetkilendirme girişi, politika tabanlı denetimi ve varsayılan reddi tanımlar | https://learn.microsoft.com/en-us/aspnet/core/security/authorization/introduction | 2026-10-13 | Doğrulandı (HTTP 200) |
| IDOR, doğrudan nesne referansıyla yetki aşımının önüne geçilmesi gereken sınıf olarak sınıflandırılır | https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html | 2026-10-13 | Doğrulandı (HTTP 200) |

## §18 — Doğrulama kontrol listesi

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Frontmatter 8 zorunlu alan + `group` | Şema okuması | Tamam | 2026-10-13 |
| 2 | `parent` alanı yok → `parent_id` | Dizin taraması | `parent:` = 0 | 2026-10-13 |
| 3 | `group` = `mimari` (tırnaksız) | FM satırı | Tamam | 2026-10-13 |
| 4 | Satır ≥ 500 | `(Get-Content).Count` | §ölçüm | 2026-10-13 |
| 5 | Gerçek kod ≥ 1 | §11 (14 satır) | Tamam | 2026-10-13 |
| 6 | Satır doğrulama | `Select-Object -Index` | Tamam | 2026-10-13 |
| 7 | URL envanter kesişimi | Set farkı | §ölçüm | 2026-10-13 |
| 8 | URL HTTP 200 | `Invoke-WebRequest` | 7/7 | 2026-10-13 |
| 9 | Yasaklı dolgu belirteci yok | Tarama | 0 | 2026-10-13 |
| 10 | Mojibake | UTF-8 okuma | 0 | 2026-10-13 |
| 11 | İmla koruması | `identitiy`·`integraion` | Korundu | 2026-10-13 |
| 12 | Beyazliste dışı yazım | Dosya yolu | 0 | 2026-10-13 |
| 13 | Son satır Authority | Dosya sonu | Tamam | 2026-10-13 |
| 14 | Commit yok | Subagent kuralı | Atılmadı | 2026-10-13 |

## §19 — Karar matrisi (girdi × sonuç)

| # | Kimlik | `public` | `requiredRole` | Rol eşleşti | İzin var | Sonuç |
|---|---|---|---|---|---|---|
| 1 | Yok | `true` | — | — | — | 200 devam |
| 2 | Yok | `false` | — | — | — | 401 |
| 3 | Var | `true` | — | — | — | 200 devam |
| 4 | Var | `false` | Tanımlı | Evet | — | 200 devam |
| 5 | Var | `false` | Tanımlı | Hayır | — | 403 `role_insufficient` |
| 6 | Var | `false` | Tanımsız | — | — | 200 devam (§15-1) |
| 7 | Var | `false` | Tanımsız | — | İzin tanımlı | İzin denetimi |
| 8 | Var | `false` | — | — | Evet | 200 devam |
| 9 | Var | `false` | — | — | Hayır | 403 `permission_denied` |
| 10 | Var (pasif anahtar) | `false` | — | — | — | 401/403 (§15-2) |
| 11 | Var | `false` | Rol alanı yok | — | — | `user` rolü ile denetim |
| 12 | Bypass credential eksik | — | — | — | — | Kimlik yazılmaz (fail-closed) |

## §20 — Uygulama reçetesi (kabul adımları)

| # | Adım | İş | Kanıt | Bitmişse |
|---|---|---|---|---|
| 1 | Karar noktası sayısı | İki middleware, tek meta | §3 | Evet |
| 2 | Girdi bütünlüğü | Oturumdan rol/izin okuma | `AuthMiddleware:21` | Evet |
| 3 | Rol denetimi | Eşitlik + strict | `PermissionMiddleware:31` | Evet |
| 4 | İzin denetimi | Strict dizi üyeliği | `PermissionMiddleware:39` | Evet |
| 5 | API rol denetimi | Dizi üyeliği | `AuthorizationMiddleware:64` | Evet |
| 6 | Public ayrımı | `isPublicRoute()` | `AuthorizationMiddleware:94` | Evet |
| 7 | Fail-closed bypass | Credential yoksa kimlik yok | `BypassAuthMiddleware:30` | Evet |
| 8 | Fail-closed test | İki test metodu | `FailClosedTest:41`·`:73` | Evet |
| 9 | Scope denetimi | `verify()` | `ApiKeyRepository:140` | Kısmi (§15-2) |
| 10 | Hata kodları | `role_insufficient`·`permission_denied` | `forbidden()` | Evet |
| 11 | Route meta ataması | `requiredRole` kullanımı | Yok (§15-1) | Hayır |
| 12 | IDOR denetimi | Nesne sahipliği | K016 | Hayır |
| 13 | Politika motoru | Ayrışık dil | Repoda yok | YAGNI |
| 14 | Denetim logu | §14 alanları | K018 | Hayır |

## §21 — Boşluk kapatma planı

| # | Boşluk | Etki | Öncelik | Kapanış ölçütü |
|---|---|---|---|---|
| 1 | `requiredRole` atanmamış | Politika kağıt üzerinde | P0 | Atama + 403 testi |
| 2 | `requiredScope` zorunlu değil | Kapsam aşımı | P0 | Zorunlu kapsam kararı |
| 3 | Nesne sahiplik sınırı | IDOR | P0 | K016 sözleşmesi |
| 4 | 403/401 testleri yok | Regresyon sessiz | P1 | Dört test eklendi |
| 5 | `role_insufficient` mesajı | Rol adı sızıntısı | P1 | Sadeleştirilmiş mesaj |
| 6 | Policy engine belirsizliği | Gelecek kapsam | P2 | YAGNI/ADR kararı |
| 7 | Scope adlandırma kataloğu | Tutarlılık | P2 | Katalog belgesi |
| 8 | Denetim logu | Gözlem | P1 | §14 yayını |
| 9 | IP maskeleme | KVKK | P1 | Redaksiyon kuralı |
| 10 | Rol hiyerarşisi | Ürün gereksinimi | P3 | Ürün kararı |

## §22 — Yayılım ve geri alma

| # | Aşama | Değişiklik | Geri alma | Doğrulama |
|---|---|---|---|---|
| 1 | Okuma | Bu belge | Dosya silinir | Şema |
| 2 | Test | 403/401 senaryoları | Test kaldırılır | PHPUnit |
| 3 | Meta ataması | `requiredRole` ekleme | Anahtar kaldırılır | 405/401 matrisi |
| 4 | Scope zorunluluğu | `requiredScope` | Opsiyonele dönüş | API testi |
| 5 | Mesaj sadeleştirme | Rol adı çıkarılır | Eski mesaj | Sözleşme testi |
| 6 | Log yayını | §14 metrikleri | Metrik kapanır | Pano |
| 7 | IDOR sınırı | K016 sözleşmesi | Eski sınır | Sızma testi |
| 8 | Kapanış | Açık soru kalmaz | — | Vault sync |

## §23 — Sık karıştırılan noktalar

| # | Yanlış varsayım | Doğrusu | Kaynak | Not |
|---|---|---|---|---|
| 1 | "Authentication = authorization" | Kimlik ≠ yetki | §1 | 401 ≠ 403 |
| 2 | "public rota = güvenli rota" | Public yalnızca kimlik atlar | §7 | Rate limit şart |
| 3 | "Rol yetmez, izin gerekir" | İkisi de route meta ile belirlenir | §9 | İkisi bağımsız |
| 4 | "`in_array` loose olur" | Strict `true` kullanılıyor | Satır 39 | Tip güvenliği |
| 5 | "Bypass credential yoksa bypass olur" | Tam tersi: yoksa bypass yok | Satır 30 | Fail-closed |
| 6 | "Scope API'de route meta ile gelir" | Scope anahtarı API anahtarında | §8 | İki farklı alan |
| 7 | "404 yetki suçudur" | 404 rota yokluğu | Routing | §16 |
| 8 | "Denetim middleware'de tek nokta" | İki yüzeyde iki middleware | §3 | İkisi de gerekli |
| 9 | "İzin listesi istemciden gelir" | Yalnız oturumda sunucu üretir | `AuthMiddleware:21` | İstemci yazılamaz |
| 10 | "Yetki kararı önbelleklenir" | Kişiselleşmiş yanıt `no-store` | K011 caching | Aksi yanlış olur |

## §24 — Sözleşme özeti (tek sayfa)

| # | Kavram | Sözleşme değeri | Kaynak | Değişirse |
|---|---|---|---|---|
| 1 | Birincil model | RBAC + scope + kısmi ABAC | §4 | Karar yeniden yazılır |
| 2 | Varsayılan rol | `user` | `PermissionMiddleware` | Yetki artışı |
| 3 | Rol karşılaştırması | `!==` (kesin) | Satır 31 | Kısmi eşleşme riski |
| 4 | İzin karşılaştırması | `in_array(strict)` | Satır 39 | Tip karışıklığı |
| 5 | Rol kaynağı (sayfa) | `MM_UserRole` (oturum) | `AuthMiddleware` | Anahtar değişirse kırılır |
| 6 | İzin kaynağı | `MM_Permissions` (oturum) | `AuthMiddleware:21` | Dizi değilse red |
| 7 | API rol kaynağı | `roles` dizisi (`_auth`) | `AuthorizationMiddleware` | Çelişki doğar |
| 8 | Public anahtarı | `public => bool` | Route meta | Yüzey genişler |
| 9 | Politika anahtarları | `requiredRole`·`requiredPermission` | Route meta | §15-1 |
| 10 | Scope denetimi | `verify(ip, requiredScope)` | `ApiKeyRepository:140` | §15-2 |
| 11 | Bypass | Fail-closed | `BypassAuthMiddleware:30` | Veto |
| 12 | Hata kodları | `role_insufficient`·`permission_denied` | `forbidden()` | Sözleşme kırılır |
| 13 | HTTP eşlemesi | 401 kimlik · 403 yetki | §19 | İstemci kırılır |
| 14 | Red yönü | Fail-closed (varsayılan red) | §7 | Güvenlik vetosu |
| 15 | Envanter | GET 11 · POST 6 · implemented true 7 | `routes.php` | Sözleşme ölçüsü |
| 16 | Test temeli | `FailClosedTest` iki metodu | `shared/tests` | Kapsam düşer |
| 17 | Karar noktası | İki middleware, tek meta | §3 | Çelişki riski |
| 18 | Politika motoru | Yok (YAGNI) | §4 | Gerekçeyle açılabilir |
| 19 | Nesne sahipliği | K016'ya devredildi | §16 | IDOR yüzeyi |
| 20 | Bağımlılık | `depends_on: K010-identitiy` | Frontmatter | Şema ihlali |

## §25 — Etki analizi (ne değişirse ne kırılır)

| # | Değişen | Kırılan | Belirtilen semptom | Koruma |
|---|---|---|---|---|
| 1 | `requiredRole` ataması | Erişilebilirlik | Beklenen 403 yerine 200 | 403 testi |
| 2 | `requiredRole` kaldırma | Yetki sınırı | Herkes erişir | Veto + test |
| 3 | `public` alanı | Kimlik zorunluluğu | 401/200 değişimi | Route testi |
| 4 | `in_array` strict bayrağı | Tip güvenliği | Eşleşmeyen izin geçer | Veto |
| 5 | Rol adı (`user`) | Oturum uyumu | Mevcut oturumlar düşer | Geçiş planı |
| 6 | `MM_Permissions` anahtarı | İzin okuması | Hep boş dizi → 403 | `SessionKeys` sözleşmesi |
| 7 | Scope kataloğu | API anahtarları | 403 patlaması | Uyumluluk testi |
| 8 | Fail-closed yorumu | Bypass davranışı | Test bypass'ı çalışmaz | Veto |
| 9 | 403 mesaj biçimi | İstemci ayrımı | Yanlış ekran | Sözleşme testi |
| 10 | Middleware sırası | Karar bütünlüğü | Auth yokken yetki kararı | Pipeline testi |
| 11 | Log alanları | Gözlem | Denetim kör noktası | §14 |
| 12 | IP maskeleme | KVKK uyumu | Kişisel veri logda | Denetim |
| 13 | Hız sınırı eşlemesi | Abuse savunması | Tarama | K011 rate limit |
| 14 | Karar noktası sayısı | Tutarlılık | İki yüzey çelişir | §3 kararı |
| 15 | Route meta şeması | Tüm politika | Politika okunamaz | Şema testi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
