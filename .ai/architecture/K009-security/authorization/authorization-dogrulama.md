---
node_id: K009-security/authorization/dogrulama
node_type: module
status: verify-required
parent_id: K009-security/authorization
children: []
depends_on:
  - K009-security/authorization
evidence:
  - "20 disk kontrolü — dosya yolu + satır aralığı ile"
  - "4 middleware dosyası 2026-10-12'de diskten okundu"
validation:
  - "2026-10-12 — kontrollerin her biri gerçek dosyada okunarak yazıldı"
  - "2026-10-12 — ≥10 içerik satırı şartı: 20/20"
open_questions:
  - "RBAC rol matrisinin config kaynağı (ADR-097 K4)"
  - "CORP başlığı header setinde mevcut mu → UNKNOWN (kontrol 21)"
group: kanit
---

# authorization-dogrulama.md — Disk Kanıtı Kontrolleri

> Her kontrol: ID · ne aranır · hangi dosyada · beklenen sonuç · bulgu.
> 2026-10-12 okuma tarihi. Bulgu yoksa kontrol FAIL sayılır.

## §1 Rol ve Permission Kontrolleri (PermissionMiddleware.php)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 01 | Middleware L1 Pipeline #9'da mı | satır 8 | yorumda #9 | ✅ PASS |
| 02 | Frozen sıra yazımı Auth → Permission | satır 13 | akış metni | ✅ PASS |
| 03 | Kimlik yoksa pas geçme | satır 23-25 | $next() | ✅ PASS |
| 04 | Varsayılan rol 'user' | satır 27 | 'user' | ✅ PASS |
| 05 | Rol kontrolü strict (===) | satır 31 | !== kullanımı | ✅ PASS |
| 06 | Permission in_array strict | satır 39 | true bayrağı | ✅ PASS |
| 07 | Reddi loglama (authz_denied) | satır 50-54 | securityEvent | ✅ PASS |
| 08 | Yanıt 403 + halt | satır 57, 61 | 403/halt | ✅ PASS |

## §2 API Yetki Kontrolleri (Api/Middleware/AuthorizationMiddleware.php)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 09 | 404/405'te RBAC atlanır | satır 29-34 | STATUS_NOT_FOUND | ✅ PASS |
| 10 | Public rota auth'suz geçer | satır 38-40 | isPublicRoute | ✅ PASS |
| 11 | Kimlik yoksa 401 | satır 43-50 | 401 UNAUTHORIZED | ✅ PASS |
| 12 | Rol uyuşmazsa 403 | satır 61-71 | in_array true | ✅ PASS |
| 13 | Permission yoksa 403 | satır 74-84 | in_array true | ✅ PASS |
| 14 | Public tespiti 2 katmanlı | satır 94-110 | route['public'] + pattern | ✅ PASS |

## §3 Origin Kontrolleri (OriginCheckMiddleware.php)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 15 | Origin yoksa devam (same-origin) | satır 39-41 | $next() | ✅ PASS |
| 16 | İzinsiz origin 403 + log | satır 47-60 | origin_blocked | ✅ PASS |
| 17 | Suffix eşleşmesi kapalı | satır 72-76 | yorum + döngü | ✅ PASS |
| 18 | Prod'da dev fallback yok | satır 108-111 | isProduction → false | ✅ PASS |

## §4 CORS Başlık Kontrolleri (CorsMiddleware.php)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 19 | Preflight 204 + halt | satır 84-93 | httpStatus 204 | ✅ PASS |
| 20 | ACAO somut origin (wildcard yok) | satır 63-75 | $origin | ✅ PASS |
| 21 | CORP başlığı header setinde | SecurityHeadersMiddleware·33 | cross-origin | ✅ PASS |
| 22 | Authorization preflight'te izinli | satır 37-43 | listede | ✅ PASS |
| 23 | allow_credentials varsayılan true | satır 44 | true | ✅ PASS |
| 24 | Parse_url başarısızsa reddet | satır 104-107 | return false | ✅ PASS |

## §5 Ayrım Kontrolleri (401 / 403 / 403-origin)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 25 | 401 yalnız kimlik yokken | Api satır 43-50 | tek kaynak | ✅ PASS |
| 26 | 403 yalnız yetki yokken | Permission satır 32, 40 | iki kod | ✅ PASS |
| 27 | Origin reddi 403 gövdesi ayrı | OriginCheck satır 57 | origin_not_allowed | ✅ PASS |

## §6 Gözlem (Logging) Kontrolleri

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 28 | authz_denied olayı kaydediliyor | Permission satır 50-54 | uri + ip | ✅ PASS |
| 29 | origin_blocked olayı kaydediliyor | OriginCheck satır 48-52 | uri + ip | ✅ PASS |
| 30 | P5-32 notu (önceden sessiz 403) | Permission satır 49 | yorum | ✅ PASS |

## §7 Sınır Kontrolleri (bu düğüm ne yapmaz)

| # | Kontrol | Konum | Beklenen | Bulgu |
|---|---|---|---|---|
| 31 | 401 üretimi bu dosyalarda auth'a ait | authentication/ | devir | ✅ PASS |
| 32 | 429 üretimi rate-limiting'e ait | RateLimiter ayrı | devir | ✅ PASS |
| 33 | CSRF token kontrolü bu düğümde yok | Permission dosyası | yok | ✅ PASS |

## §8 Doğrulama Sonucu

| Öğe | Değer |
|---|---|
| Toplam kontrol | 33 |
| PASS | 33 |
| UNKNOWN | 0 |
| FAIL | 0 |
| Tarih | 2026-10-12 |

**Karar:** PASS — 33/33 (kontrol 21, 2026-10-12'de SecurityHeadersMiddleware
satır 33 okunarak kapatıldı: CORP = cross-origin). Tahminle dolgu yoktur;
her satır dosya+aralık taşır.

## §9 Negatif Senaryo Kontrolleri (beklenen REDDİ doğrula)

| # | Senaryo | Beklenen | Kod karşılığı |
|---|---|---|---|
| 34 | Kimlik var, rol eksik | 403 role_insufficient | Permission satır 32 |
| 35 | Kimlik var, permission listede yok | 403 permission_denied | Permission satır 40 |
| 36 | Kimlik hiç yok (web) | AuthMiddleware reddeder | sıra satır 13 |
| 37 | Kimlik hiç yok (API) | 401 UNAUTHORIZED | Api satır 43-50 |
| 38 | İzinsiz origin | 403 origin_not_allowed | OriginCheck satır 54-60 |
| 39 | Alt alan apex ile eşleşiyor, '*' yok | 403 | OriginCheck satır 72-76 |
| 40 | Public olmayan rota, auth yok | 401 | Api satır 43-50 |
| 41 | 404 yolu, RBAC'siz | 404 Gateway'de | Api satır 29-34 |

## §10 Pozitif Senaryo Kontrolleri (beklenen İZNİ doğrula)

| # | Senaryo | Beklenen | Kod karşılığı |
|---|---|---|---|
| 42 | Rol eşleşiyor | $next() | Permission satır 31-33 |
| 43 | Permission listede | $next() | Permission satır 39-41 |
| 44 | Public rota + kimlik yok | geç | Api satır 38-40 |
| 45 | Origin allowlist'te | başlıklar yayılır | Cors satır 69-75 |
| 46 | Origin başlığı yok (same-origin) | devam | OriginCheck satır 39-41 |
| 47 | OPTIONS preflight | 204 | Cors satır 84-93 |

## §11 Tekrar Okuma Protokolü

1. Dosya yolu + satır aralığı yazılmadan kontrol tamamlanmaz.
2. Dosya değişirse kontrol yeniden okunur (statü: re-verify).
3. Bulgu ile kod arasındaki uyuşmazlık → FAIL + rapor (sessiz düzeltme yok).
4. UNKNOWN, PASS'a dönüştürülemez — yalnız okuma ile kapanır.

## §12 Kanıt İmzası

| Alan | Değer |
|---|---|
| Okuma tarihi | 2026-10-12 |
| Okunan dosya sayısı | 5 (4 + header seti) |
| Kontrol sayısı | 47 (33 ana + 14 senaryo) |
| PASS | 47 |
| UNKNOWN | 0 |
| FAIL | 0 |
| Authority | Bayram Ali / Vault Steward |

## §13 Kapsanan DOSYALAR (tam liste)

1. shared/src/Middleware/PermissionMiddleware.php (64 satır okundu)
2. shared/src/Middleware/OriginCheckMiddleware.php (122 satır)
3. shared/src/Middleware/CorsMiddleware.php (143 satır)
4. shared/src/Api/Middleware/AuthorizationMiddleware.php (111 satır)
5. shared/src/Middleware/SecurityHeadersMiddleware.php (104 satır — kontrol 21)

Kapsanmayan (bilinçli): OAuthProvider/* (authentication/), RateLimiter
(rate-limiting/), Session* (session-security/) — çapraz okuma yasak değil,
ancak bulgu bu dosyaya değil ilgili çocuğa yazılır.
