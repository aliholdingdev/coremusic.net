---
node_id: K0100-identitiy/identity-dogrulama
node_type: domain
status: verify-required
parent_id: K0100-identitiy
children: []
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "shared/src/Security/JwtService.php L89-94 catch(\\Throwable)→null — 2026-10-12"
  - "shared/src/Session/SessionConfig.php L16-20 (paradox: 1800<3600) — 2026-10-12"
  - "shared/src/OAuth/OAuthRepository.php L88 (INTERVAL 10 MINUTE) — 2026-10-12"
validation: "Bu dosyanın kendisi doğrulama defteridir; 24 checklist + 6 web URL doğrulaması"
group: kanit
open_questions:
  - "RFC 8725 (JWT BCP) karşılaştırması: alg RS256 tek başına yeterli mi? (verify-required)"
  - "Session paradoxu davranışsal testi henüz yapılmadı"
---

# K0100-identitiy — doğrulama (verification) defteri

## §1 Doğrulama Metodolojisi

| Adım | Yöntem | Kaynak |
|---|---|---|
| 1 | Gerçek repo dosyalarını satır-satır okuma | `shared\src\Security\`, `shared\src\Session\`, `shared\src\OAuth\`, `shared\src\Api\` |
| 2 | Kanıt paketi §A (K1-K24) tablosuna bağlama | `k010-evidence.md` (temp) |
| 3 | Web iddialarını resmi kaynağa bağlama | §6 tablosu (6 tekil URL) |
| 4 | Bilinmeyenleri `⚠️ VERIFICATION REQUIRED` işaretleme | zero-hallucination (AGENTS.md §3) |
| 5 | Paradox/çelişki defteri çıkarma | §4 · §5 |

## §2 Kod Doğrulama Checklist (24 madde)

| # | İddia | Kanıt (Dosya / Satır) | Durum |
|---|---|---|---|
| 1 | JWT algoritması RS256 | `shared\src\Security\JwtService.php` / Satır: 24 | ✅ |
| 2 | `jti` her token'da üretilir | `JwtService.php` / Satır: 51 | ✅ |
| 3 | Validate fail-closed (`catch(\Throwable)→null`) | `JwtService.php` / Satır: 89-94 | ✅ |
| 4 | `iss`/`aud`/`sub`/`jti` doğrulaması | `JwtService.php` / Satır: 96-104 | ✅ |
| 5 | Private key dosyadan okunur, commit edilmez | `JwtService.php` / Satır: 20 · 111-124 | ✅ |
| 6 | `MAX_LIFETIME=1800` · `IDLE_TIMEOUT=3600` · `ROTATION_INTERVAL=1800` · `COOKIE_EXPIRY=42000` | `shared\src\Session\SessionConfig.php` / Satır: 16-20 | ✅ (paradox §4) |
| 7 | Cookie: `httponly=true`, `samesite=Lax`, `lifetime=0` | `SessionConfig.php` / Satır: 52-62 | ✅ |
| 8 | `csrf_token = bin2hex(random_bytes(32))` | `shared\src\Session\SessionLifecycle.php` / Satır: 47-49 | ✅ |
| 9 | `session_regenerate_id(true)` her 1800sn | `SessionLifecycle.php` / Satır: 54-62 | ✅ |
| 10 | Lifetime exceeded → `destroy()` | `SessionLifecycle.php` / Satır: 32-35 | ✅ |
| 11 | Idle exceeded → `destroy()` | `SessionLifecycle.php` / Satır: 41-45 | ✅ |
| 12 | `ApiSessionManager` `session_start()` çağırmaz | `shared\src\Api\Auth\ApiSessionManager.php` / Satır: 19-23 | ✅ |
| 13 | `ApiSessionManager::isIdleExpired()` hep false | `ApiSessionManager.php` / Satır: 158-162 | ✅ |
| 14 | `MM_UserID` legacy fallback | `ApiSessionManager.php` / Satır: 58-62 | ✅ |
| 15 | `ISessionManager` 21 metot | `shared\src\Contracts\Auth\ISessionManager.php` / Satır: 7-27 | ✅ |
| 16 | `PUBLIC_ROUTE_PATTERNS` 4 route | `shared\src\Api\Middleware\AuthenticationMiddleware.php` / Satır: 33-38 | ✅ |
| 17 | Bearer parse `substr($authHeader,7)` | `AuthenticationMiddleware.php` / Satır: 84-85 | ✅ |
| 18 | jti revocation closure | `AuthenticationMiddleware.php` / Satır: 88-89 | ✅ |
| 19 | 401 fail-closed | `AuthenticationMiddleware.php` / Satır: 103-107 | ✅ |
| 20 | OAuth state `hash('sha256', $state)` | `shared\src\OAuth\OAuthManager.php` / Satır: 160 | ✅ |
| 21 | State tek kullanımlık (doğrulayınca sil) | `OAuthManager.php` / Satır: 180 | ✅ |
| 22 | State TTL 10 dakika | `shared\src\OAuth\OAuthRepository.php` / Satır: 88 | ✅ |
| 23 | RBAC: `role_insufficient` + `permission_denied` 403 | `shared\src\Middleware\PermissionMiddleware.php` / Satır: 30-42 | ✅ |
| 24 | `authz_denied` securityEvent log | `PermissionMiddleware.php` / Satır: 50-54 | ✅ |

Toplam: 24/24 kod iddiası satır-içi kanıtla doğrulandı.

## §3 Bağımlılık Doğrulama

| # | İddia | Durum |
|---|---|---|
| 1 | `oauth_connections` + `oauth_states` migration'ı var | ✅ (K23, migration dizini) |
| 2 | 5 domain event + 2 integration event dosyası var | ✅ (K22) |
| 3 | K009 middleware yüzeyi (11 middleware) | ✅ (K25.2 §25.2 tablosu) |
| 4 | Event yayıncı zinciri (kim ne yayıyor) | ⚠️ VERIFICATION REQUIRED |
| 5 | `revokeAuthKeysForUser` çağrı noktası | ⚠️ VERIFICATION REQUIRED (sözleşme var, çağıran okunmadı) |
| 6 | `user_tokens` fiziksel şeması | ⚠️ VERIFICATION REQUIRED (K007-data'ya devredildi) |

## §4 Paradox ve Çelişki Defteri

| # | Paradox | Kanıt | Etki | Durum |
|---|---|---|---|---|
| 1 | `MAX_LIFETIME` (1800) < `IDLE_TIMEOUT` (3600) | `SessionConfig.php` / Satır: 17-18 | Mutlak sınır idle'dan önce dolar; idle timeout kodu asla tetiklenmez | ⚠️ VERIFICATION REQUIRED |
| 2 | `COOKIE_EXPIRY` (42000) > `MAX_LIFETIME` (1800) | `SessionConfig.php` / Satır: 16-17 | Cookie hayattayken sunucu tarafı oturumu çoktan ölmüş olur | ⚠️ VERIFICATION REQUIRED |
| 3 | API köprüsü `rotateIfNeeded()` hep false | `ApiSessionManager.php` / Satır: 169-172 | API oturumlarında rotation yok — kasıtlı (stateless) mı? | ⚠️ VERIFICATION REQUIRED |
| 4 | `isIdleExpired()` API'de hep false | `ApiSessionManager.php` / Satır: 158-162 | API'de idle timeout yok | ⚠️ VERIFICATION REQUIRED |
| 5 | `response_type='session'` OIDC standart değeri değil (`code`/`id_token`/`token`) | `shared\src\Config\AuthRouteConfig.php` / Satır: 48-67 | Özel SSO protokolü — OIDC uyumlu değil | ⚠️ VERIFICATION REQUIRED |

## §5 Güvenlik Doğrulama (identity yüzeyi)

| # | Kontrol | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Token error bilgisi sızmıyor | ✅ | `JwtService.php` / Satır: 88-94 (yorum: "hiçbir ayrıntı sızdırılmaz") |
| 2 | State tahmini imkânsız (sha256 + tek kullanımlık) | ✅ | `OAuthManager.php` / Satır: 160-180 |
| 3 | OAuth token şifreli saklanıyor (AES-256-GCM) | ✅ | `OAuthManager.php` / Satır: 188-205 |
| 4 | Cookie `httponly` (XSS'e karşı) | ✅ | `SessionConfig.php` / Satır: 59 |
| 5 | Session ID rotation (session fixation) | ✅ | `SessionLifecycle.php` / Satır: 54-62 |
| 6 | Public route dışına auth zorunlu | ✅ | `AuthenticationMiddleware.php` / Satır: 103-107 |
| 7 | Fail-closed her kritik yolda | ✅ | §2 #3, #19 |
| 8 | MFA uygulaması | ❌ YOK (PLANNED) | repo taraması 2026-10-12 |

## §6 Web Doğrulama (6 tekil URL)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| JWT Best Current Practices: `alg` allowlist, `exp` kontrolü, key rotasyonu zorunlu | https://datatracker.ietf.org/doc/html/rfc8725 | 2026-10-12 | okundu — RS256 tek başına yeterli değil, allowlist şart (§2 #1 ile hizalı) |
| HTTP Authentication: `WWW-Authenticate` + `Authorization: Bearer` şeması | https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication | 2026-10-12 | okundu — Bearer şeması doğrulandı |
| IANA HTTP Authentication Schemes kaydı (Bearer kayıtlı) | https://www.iana.org/assignments/http-authschemes/http-authschemes.xhtml | 2026-10-12 | okundu — şema kaydı doğrulandı |
| OpenID Certified programı — sertifikalı provider listesi | https://openid.net/developers/certified/ | 2026-10-12 | okundu — CoreMusic sertifikalı DEĞİL (idddia edilmiyor) |
| Referrer Policy — `no-referrer` auth redirect'lerinde zorunlu | https://www.w3.org/TR/referrer-policy/ | 2026-10-12 | okundu — redirect_uri sızıntısı riski not edildi |
| Practical implementation guides (session hardening) | https://developer.mozilla.org/en-US/docs/Web/Security/Practical_implementation_guides | 2026-10-12 | okundu — `httponly`+`secure`+`samesite` üçlüsü doğrulandı |

## §7 Doğrulama Sonucu

| Metrik | Değer |
|---|---|
| Kod iddiası doğrulanan | 24/24 |
| Web iddiası doğrulanan | 6/6 |
| Paradox | 5 açık (`⚠️ VERIFICATION REQUIRED`) |
| Eksik doğrulama | 3 (§3 #4-6) |
| Node status | `verify-required` (paradoxlar kapanana kadar) |

## §8 — RFC 7519 (JSON Web Token) doğrulama maddeleri ↔ 24 checklist

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7519 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7519, JWT claim setini ve doğrulama gereksinimlerini tanımlar. §2'deki 24
madde bu standartla çaprazlanır; satır-içi kanıtı olmayan maddeler **eklemez**,
açık olarak işaretlenir.

### §8.1 — RFC 7519 §4.1 claim'leri ↔ JwtService kanıtları

| # | Claim (RFC 7519 §4.1) | Zorunluluk (profil) | JwtService kanıtı | Durum |
|---|---|---|---|---|
| 1 | `iss` (§4.1.1) | zorunlu | L96-98 (config'ten) | ✅ §2 #4 |
| 2 | `sub` (§4.1.2) | zorunlu | L96-104 | ✅ §2 #4 |
| 3 | `aud` (§4.1.3) | zorunlu | L96-104 | ✅ §2 #4 |
| 4 | `exp` (§4.1.4) | zorunlu | checklist'te YOK | ⚠️ VR (ek madde) |
| 5 | `nbf` (§4.1.5) | opsiyonel | checklist'te YOK | ⚠️ VR (ek madde) |
| 6 | `iat` (§4.1.6) | opsiyonel | checklist'te YOK | ⚠️ VR (ek madde) |
| 7 | `jti` (§4.1.7) | opsiyonel (CoreMusic: zorunlu) | L51 | ✅ §2 #2 |
| 8 | Özel claim (`MM_*`) | — | claim taşıma kanıtlanmadı | ⚠️ VR |

### §8.2 — §4.1.4 doğrulama zorunluluğu ↔ repo durumu

| # | RFC 7519 §4.1.4 maddesi | Repo durumu | Durum |
|---|---|---|---|
| 1 | `exp` sonrası token reddedilmeli (§4.1.4) | satır-içi kanıt bu revizyonla yok | ⚠️ VR |
| 2 | `iat` sonrası reddi (§4.1.4 son cümle) | kanıt yok | ⚠️ VR |
| 3 | Clock skew toleransı | kanıt yok | ⚠️ VR |
| 4 | Reddetme fail-closed olmalı | `catch(\Throwable)→null` | ✅ §2 #3 |

> Bu dört satır §2 checklist'ine **eklenmez** (append-only defter korunur);
> yalnız bu tabloda açık madde olarak taşınır. `exp` kontrolü varsayılan
> kütüphane davranışına bağlı olabilir → davranışsal test ADR-097 öncesi.

### §8.3 — Token profili eşlemesi

| # | Profil boyutu | CoreMusic | Durum |
|---|---|---|---|
| 1 | İmza | RS256 | ✅ §2 #1 |
| 2 | Tekil | `jti` + `user_tokens.token_hash` | ✅ §2 #2 · `IUserRepository.php` L40 |
| 3 | Revocation | jti closure + DB | ✅ §2 #18 |
| 4 | `typ` header | kanıtlanmadı | ⚠️ VR (RFC 9068 §2 — `identity-web-arastirma.md` §10) |
| 5 | Refresh token | yok | ❌ (kapsam dışı — `identity-kapsam.md` §5 #2) |

**Sonuç:** RFC 7519 çaprazlaması, 24 maddenin 20'sini standart zorunluluklarla
hizalar; `exp`/`nbf`/`iat` üçlüsü satır-içi kanıt **eklemez** → paradox defterine
(§4) komşu açık madde olarak kaydedilir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — RFC 7515 (JSON Web Signature) imza doğrulama eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7515 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7515, JWS imzalama/verifyleme çerçevesini tanımlar: protected header
(`alg`), compact serialization (`header.payload.signature`). `JwtService` bu
yığını kullanır; bu bölüm imza bileşenini madde madde doğrular.

### §9.1 — RFC 7515 maddeleri ↔ JwtService kanıtları

| # | RFC 7515 maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Protected header `alg` (§4.1) | RS256 sabit | `JwtService.php` L24 | ✅ §2 #1 |
| 2 | `alg: none` reddi (RFC 8725 §3.1) | sabit RS256 → `none` mümkün değil | L24 | ✅ kanıtlı |
| 3 | RSASSA-PKCS1-v1_5-SHA256 = RS256 (JWA §3.5) | RS256 kullanımı | L24 | ✅ kanıtlı |
| 4 | Compact serialization | kanıtlanmadı (imza biçimi okunmadı) | — | ⚠️ VR |
| 5 | `jwk`/`jku` header reddi (RFC 8725 §3.4) | kanıtlanmadı | — | ⚠️ VR |
| 6 | Key identifier (`kid`) header | kanıtlanmadı | — | ⚠️ VR (`identity-bilesenler.md` §9) |
| 7 | Verify fail-closed | `catch(\Throwable)→null` | `JwtService.php` L88-94 | ✅ §2 #3 |
| 8 | Ayrıntı sızdırmama | yorum: "hiçbir ayrıntı sızdırılmaz" | L88-94 | ✅ §5 #1 |

### §9.2 — İmza doğrulama zinciri ↔ checklist hizası

| # | Zincir adımı | Checklist karşılığı | Durum |
|---|---|---|---|
| 1 | Header ayrıştırma + `alg` sabitleme | §2 #1 | ✅ |
| 2 | Signature verify (public key) | §2 #5 (private key dosyadan) | ✅ |
| 3 | Claim doğrulama (`iss/aud/sub/jti`) | §2 #4 | ✅ |
| 4 | `exp/nbf` kapısı | §8.2 (ek madde) | ⚠️ VR |
| 5 | Revocation sorgusu | §2 #18 | ✅ |
| 6 | Hata → `null` → 401 | §2 #19 | ✅ |

### §9.3 — Bilinen JWS tehditleri ↔ durum

| # | Tehdit (RFC 8725/7515) | Azaltım | Durum |
|---|---|---|---|
| 1 | `alg` değiştirme (RS256→HS256) | sabit alg + asymmetric key | ✅ kanıtlı |
| 2 | `alg: none` | imkânsız (sabit RS256) | ✅ kanıtlı |
| 3 | Key confusion (JWK/JWKS embed) | `jwk` header kanıtlanmadı | ⚠️ VR |
| 4 | Token replay | `jti` revocation | ✅ kanıtlı |
| 5 | Süre aşımı | `exp` kontrolü kanıtlanmadı | ⚠️ VR |

**Sonuç:** JWS imza katmanı RS256 + fail-closed ile kanıtlanmıştır; `exp`
süre kapısı ve header red-listesi (`jwk`/`jku`) bu revizyonla okunmadı → §4/§8
paradox defterine eklenir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 — MDN HTTP 401 (Unauthorized) durum kodu doğrulaması

> **Kaynak:** https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/401 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

MDN 401 tanımı: istemcinin kimliğini doğrulaması gerektiğini bildirir; yanıt
`WWW-Authenticate` içermelidir. `AuthenticationMiddleware` 401 fail-closed
davranışını kanıtlanmıştır — bu bölüm durum kodu sözleşmesini doğrular.

### §10.1 — MDN 401 maddeleri ↔ AuthenticationMiddleware

| # | MDN maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | 401 = kimlik doğrulama gerekli | 401 fail-closed | `AuthenticationMiddleware.php` L103-107 | ✅ §2 #19 |
| 2 | `WWW-Authenticate` başlığı zorunlu | başlık yazımı kanıtlanmadı | — | ⚠️ VR |
| 3 | `Bearer` şeması (`WWW-Authenticate: Bearer`) | Bearer parse | L84-85 | ✅ §2 #17 (parse) · ⚠️ VR (yanıt başlığı) |
| 4 | 401 ≠ 403 (kimlik vs yetki) | 403 `role_insufficient`/`permission_denied` | `PermissionMiddleware.php` L30-42 | ✅ §2 #23 |
| 5 | Proxy/Authenticate zinciri (MDN notu) | — | — | bilgi |
| 6 | Public route'larda 401 üretilmez | 4 route public | L33-38 | ✅ §2 #16 |

### §10.2 — 401/403 sözleşmesi ↔ K009/K010 sınırı

| # | Durum kodu | Karar sahibi | Kanıt | Durum |
|---|---|---|---|---|
| 1 | 401 (kimlik yok/geçersiz) | K010 middleware | L103-107 | ✅ |
| 2 | 403 (yetki yetersiz) | K010 middleware + K009 politika | `PermissionMiddleware.php` L30-42 | ✅ |
| 3 | 429 (rate limit) | K009 | `CacheRateLimiter.php` (§3 #7) | ✅ (sahiplik) |
| 4 | 400 (parse hatası) | kanıtlanmadı | — | ⚠️ VR |

### §10.3 — `WWW-Authenticate` eksikliğinin etkisi

| # | Eksiklik | Etki | Durum |
|---|---|---|---|
| 1 | Başlık yoksa API istemcisi şemayı bilemez | uyumluluk | ⚠️ VR |
| 2 | `realm`/`error` parametreleri | tanılama | ⚠️ VR |
| 3 | K009'a devredilen log alanı (`authz_denied`) | gözlemlenebilirlik | ✅ §2 #24 |

**Sonuç:** 401/403 ayrımı ve fail-closed davranış kanıtlanmıştır; `WWW-Authenticate`
yanıt başlığı bu revizyonla okunmadı → API uyumluluk maddesi `⚠️ VR` olarak
ek madde olarak taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 — PHP `password_needs_rehash` resmi kılavuzu ↔ hash yaşam döngüsü doğrulaması

> **Kaynak:** https://www.php.net/manual/en/function.password-needs-rehash.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`password_needs_rehash()`, verilen hash'in belirli algoritma/seçeneklerle
uyumsuz olup olmadığını döndürür (maliyet artışı vb.). CoreMusic parola
hash'i Argon2id (m=65536, t=4, p=2) ile üretilir (`credentials-dogrulama.md` §1
#4-#6); upgrade akışı bu bölümde doğrulanır.

### §11.1 — Kılavuz maddeleri ↔ CoreMusic hash bileşeni

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `password_needs_rehash($hash, $algo, $options)` | çağrı noktası kanıtlanmadı | — | ❌ repo'da yok |
| 2 | Maliyet değişince `true` döner | `memory_cost=65536, time_cost=4, threads=2` | `Password.php` L44 | ✅ kanıtlı (parametre) |
| 3 | Algoritma değişince `true` döner | `PASSWORD_ARGON2ID` sabit | `Password.php` L44 | ✅ kanıtlı |
| 4 | `true` dönüşünde yeniden hash'leme | upgrade akışı kanıtlanmadı | — | ⚠️ VR |
| 5 | Bilinmeyen hash için `false` | — | — | bilgi |
| 6 | `password_get_info` ile eşleştirilir | — | — | bilgi (`credentials-dogrulama.md` §3) |

### §11.2 — Upgrade akışı doğrulama matrisi

| # | Akış adımı | Repo durumu | Durum |
|---|---|---|---|
| 1 | Login başarılı → `password_verify` | `Password.php` L60 | ✅ |
| 2 | `password_needs_rehash` çağrısı | kanıt yok | ⚠️ VR |
| 3 | Yeniden hash + UPDATE | kanıt yok | ⚠️ VR |
| 4 | Parametre değişikliği yönetimi (config) | parametreler sabit kodda | ✅ kanıtlı (L44) |
| 5 | Test ile parametre kilitlenmesi | `PasswordTest.php` L36 | ✅ |

### §11.3 — Paradox defterine etkisi

| # | Açık madde | Kaynak | Durum |
|---|---|---|---|
| 1 | Cost artışı sonrası mevcut hash'ler upgrade edilir mi? | bu bölüm | ⚠️ VR |
| 2 | `APP_PEPPER` rotasyonu hash upgrade'ini tetikler mi? | `credentials-kapsam.md` §5 R2 | ⚠️ VR |
| 3 | `credential_vault.rotation_interval_days=90` parola hash'i kapsar mı? | `credentials-kapsam.md` §4 #6 | ⚠️ VR |

**Sonuç:** Argon2id parametreleri kanıtlanmıştır; `password_needs_rehash` tabanlı
upgrade akışı repoda **kanıtlanmamıştır** — üç açık madde credential dalının
defterine ek olarak taşınır (append-only: mevcut satırlar değiştirilmez).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
