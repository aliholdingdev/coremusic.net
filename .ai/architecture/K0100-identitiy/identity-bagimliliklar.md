---
node_id: K010-identitiy/identity-bagimliliklar
node_type: domain
status: verify-required
parent_id: K010-identitiy
children: []
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "shared/src/OAuth/OAuthRepository.php (oauth_connections + oauth_states migration, K23) — 2026-10-12"
  - "shared/src/Events/Domain/UserLoggedInEvent.php vb. 5 domain event (K22) — 2026-10-12"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php L88-89 jti revocation closure (K009-K010 sınırı) — 2026-10-12"
validation: "identity-dogrulama.md §3 (bağımlılık okları) · ADR-097 §5.5 kural 5 (depends_on iç+dış) bekleniyor"
group: mimari
open_questions:
  - "K008-domain event sahipliği: UserLoggedInEvent yayılımı domain'de mi, application'da mı?"
  - "K007-data: oauth_states TTL temizliği cron mu, uygulama mu?"
---

# K010-identitiy — bağımlılıklar (dependencies)

## §1 Dış Bağımlılık Okları

| # | Hedef | Yön | İlişki | Kanıt | Status |
|---|---|---|---|---|---|
| 1 | `K009-security` | K010 → K009 | middleware (Auth, Session, Permission, Authorization) K009 politikası altında | `shared\src\Middleware\` (11 middleware) · `shared\src\Api\Middleware\` | kanıtlı |
| 2 | `K009-security` | K010 → K009 | encryption (OAuth token AES-256-GCM, JWT private key) güvenlik anahtarı yönetimi | `OAuthManager.php` L188-235 · `JwtService.php` L111-124 | kanıtlı |
| 3 | `K009-security` | K010 → K009 | `CacheRateLimiter` · `ApiKeyRepository` · `SecurityHelper` · `ReturnUrlPolicy` | `shared\src\Security\` (K21) | kanıtlı |
| 4 | `K008-domain` | K010 → K008 | `UserLoggedInEvent` · `UserLoggedOutEvent` · `UserRegisteredEvent` · `PasswordResetRequestedEvent` · `GenderSetEvent` | `shared\src\Events\Domain\` (K22) | dosya var · sahiplik `⚠️ VERIFICATION REQUIRED` |
| 5 | `K008-domain` | K010 → K008 | `SessionCreatedEvent` · `AuthValidatedEvent` (integration event) | `shared\src\Events\Integration\` (K22) | dosya var · sahiplik `⚠️ VERIFICATION REQUIRED` |
| 6 | `K007-data` | K010 → K007 | `oauth_connections` (upsert, soft delete) · `oauth_states` (10 dk TTL) | `OAuthRepository.php` L27-119 · migration (K23) | kanıtlı |
| 7 | `K007-data` | K010 → K007 | `user_tokens` (token_hash = sha256(jti)) | `IUserRepository.php` L40 · L45 · L50 | kanıtlı |
| 8 | `K007-data` | K010 → K007 | session dosya sistemi (`DIR_PERMISSIONS=0750`) | `SessionConfig.php` L20 | kanıtlı |
| 9 | `K013-music` (⚠️) | K010 → ? | Apple MusicKit developer token (ES256) ile `JwtService` (RS256) paralel token kullanımı | `⚠️ VERIFICATION REQUIRED` (Eski K010 index'iden devredilen iddia; bu revizyon doğrulamadı) | `⚠️ VERIFICATION REQUIRED` |

## §2 Geriye Dönük (inbound) Bağımlılıklar

| # | Kaynak | Yön | İlişki |
|---|---|---|---|
| 1 | K009-security | K009 → K010 | `AuthenticationMiddleware` `ISessionManager` ve `JwtService`'i çağırır (K7) |
| 2 | API gateway (K24) | API → K010 | `ServiceRegistry` (122 satır) auth servislerini kaydeder |
| 3 | BFF katmanı (K24) | BFF → K010 | 5 BFF dosyası auth state taşır |
| 4 | Page router | PageRouter → K010 | `AuthGuard` `SpaRoute.requiresAuth/requiredRole/requiredPermission` okur (K10) |
| 5 | Session middleware | SessionManagerMiddleware → K010 | HTTP pipeline session başlatır (K15) |

## §3 İç Mimari Bağımlılıklar (ADR-097 §5.5 kural 5)

| # | Hedef | Tür | Kapsam |
|---|---|---|---|
| 1 | `K010-identitiy/oidc` | iç düğüm | SSO oturum paylaşımı anayasası |
| 2 | `K010-identitiy/oauth2` | iç düğüm | 10 provider + PKCE akışları |
| 3 | `K010-identitiy/jwt` | iç düğüm | RS256 + jti revocation derinlemesine |
| 4 | `kullanici-modeli` · `rol-ve-yetki` · `oturum-yonetimi` · `dogrulama-servisleri` · `sso-entegrasyonu` · `mfa-ve-sertifika` | kademe-2 çocuklar | kök dosyalardan `children` alanı |

## §4 Bağımlılık Grafiği (metin)

```text
K010-identitiy (kök)
├──→ K009-security   (middleware, crypto, rate limit, return-url)
├──→ K008-domain     (domain/integration event'ler)
├──→ K007-data       (oauth_connections, oauth_states, user_tokens, session dosyaları)
├──→ oidc/           (iç)
├──→ oauth2/         (iç)
└──→ jwt/            (iç)

K009-security ──→ K010-identitiy   (geriye dönük: auth middleware ISessionManager/JwtService kullanır)
API gateway/BFF ──→ K010-identitiy (ServiceRegistry + BFF auth state)
```

## §5 Zayıf Noktalar (verify-required)

| # | Zayıf nokta | Etki | Öneri |
|---|---|---|---|
| 1 | `K008-domain` okları dosya-var kanıtına dayanıyor; event yayıncı/kayıt zinciri okunmadı | olay yayılımı doğrulanamıyor | K008 düğümü ile çapraz inceleme |
| 2 | Apple MusicKit iddiası (eski index'ten) bu revizyonla doğrulanmadı | kanıtsız iddia taşınıyor | ADR-097 öncesi doğrulanmalı veya kaldırılmalı |
| 3 | `ReturnUrlPolicy` sahipliği belirsiz (K009/K010 ortak) | sınır çakışması | ADR-097 G2 kararı |

## §6 — RFC 7009 (Token Revocation) ile bağımlılık eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7009 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7009, OAuth 2.0 token revocation endpoint'ini tanımlar: istemci, AS'a POST
ederek verdiği token'ı geçersiz kılar. K010'da revocation karşılığı **jti
revocation closure**'dır (§1 #1 ile aynı yüzey) — bu bölüm bağımlılık okunun
standart dilini bağlar.

### §6.1 — RFC 7009 maddeleri ↔ CoreMusic revocation yüzeyi

| # | RFC 7009 maddesi | CoreMusic karşılığı | Kanıt (bu vault) | Durum |
|---|---|---|---|---|
| 1 | Revocation endpoint'i `POST` + client auth (§2.1) | Repo'da revocation endpoint'i kanıtlanmadı | — | ⚠️ VR |
| 2 | `token` parametresi body'de (§2.1) | — | — | ⚠️ VR |
| 3 | Refresh token iptali access token'ı da düşürmeli (§2.1) | Refresh token rotasyonu yok | `identity-kapsam.md` §5 #2 | ❌ kanıt yok |
| 4 | Başarılı istek `200` döner (§2.2) | — | — | ⚠️ VR |
| 5 | Token zaten geçersizse de `200` (idempotent) (§2.2) | — | — | ⚠️ VR |
| 6 | Revocation sonra token reddi (§2.1) | jti revocation closure doğrulamayı kapatır | `AuthenticationMiddleware.php` L88-89 | ✅ kanıtlı (mekanizma) |

### §6.2 — Jti revocation ↔ RFC 7009 uç nokta farkı

| # | Boyut | RFC 7009 (AS merkezli) | CoreMusic (JWT lokal validate) |
|---|---|---|---|
| 1 | Karar noktası | Authorization Server | API tarafı middleware (K010 → K009 sınırı) |
| 2 | Kayıt | Revocation endpoint çağrısı | `user_tokens` (token_hash = sha256(jti)) — `IUserRepository.php` L40/45/50 |
| 3 | Doğrulama akışı | Endpoint POST | Bearer parse → jti kontrolü → closure |
| 4 | Durum | endpoint kanıtlanmadı (⚠️ VR) | closure kanıtlı (✅) |
| 5 | Kapsam etkisi | K010 bağımlılık defterinde açık madde | — |

### §6.3 — Kapsamlı revocation bağımlılığı matrisi

| # | Bağımlılık | Hedef | Yön | Status |
|---|---|---|---|---|
| 1 | `user_tokens` kaydı | K007-data | K010 → K007 | ✅ kanıtlı (§1 #7) |
| 2 | `revokeAuthKeysForUser` çağrı noktası | K010 iç | — | ⚠️ VR (§3 #5) |
| 3 | OAuth provider token iptali | provider API | K010 → dış | ⚠️ VR (repo kanıtı yok) |
| 4 | Session destroy (logout) | K010 session | — | ✅ kanıtlı (`SessionLifecycle.php` L21-69) |

**Eşleme sonucu:** RFC 7009, CoreMusic'te **uç-uç revocation endpoint'i olmadığını**
kayıtlı hale getirir; mevcut revocation, lokal jti kontrolü + `user_tokens`
kayıtlarıyla sağlanır. Bağımlılık defterinde bu, K007-data'ya ikinci bir
`user_tokens` bağımlılığı ekler (zaten §1 #7'de kayıtlı).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — RFC 7662 (Token Introspection) ile bağımlılık eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7662 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7662, resource server'ın (RS) access token'ı AS'a sorarak (introspection)
geçerliliğini öğrendiği modeli tanımlar. CoreMusic API'si **stateless lokal
validate** yapar (`JwtService` + jti closure) — bu bölüm, introspection
modelinin hangi bağımlılık oklarını tetikleyeceğini kaydeder.

### §7.1 — RFC 7662 maddeleri ↔ CoreMusic API doğrulama modeli

| # | RFC 7662 maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Introspection endpoint'i client auth ister (§2.1) | repo'da endpoint yok | — | ❌ kanıt yok |
| 2 | Yanıt `active: false` (geçersiz token) (§2.2) | geçersiz token → `null` → 401 | `JwtService.php` L88-94 · `AuthenticationMiddleware.php` L103-107 | ✅ kanıtlı (lokal eşdeğer) |
| 3 | Yanıtta `scope` · `client_id` · `sub` · `exp` (§2.2) | claim'ler token içinde | `JwtService.php` L96-104 | ✅ kanıtlı (lokal) |
| 4 | RS, kararı AS'a devredebilir (§1) | RS kararı lokal — K010 servisi | `AuthenticationMiddleware.php` L83-100 | ✅ kanıtlı (model farkı) |
| 5 | Introspection yanıtı cache'lenmeli (§2.2 pratik) | — | — | ⚠️ VR (endpoint yok) |

### §7.2 — Introspection ↔ ApiSessionManager stateless köprü

| # | Boyut | RFC 7662 modeli | CoreMusic `ApiSessionManager` |
|---|---|---|---|
| 1 | Durum | RS durumsuz; sorar | köprü durumsuz; `session_start()` çağırmaz (L19-23) |
| 2 | Idle timeout | RS'te tanımsız | `isIdleExpired()` hep false (L158-162) |
| 3 | Rotation | RS'te tanımsız | `rotateIfNeeded()` hep false (L169-172) |
| 4 | Karar gecikmesi | network round-trip | lokal imza doğrulama (düşük gecikme) |
| 5 | Durum | — | ⚠️ paradoxlar §4 #3-#4 |

### §7.3 — Bağımlılık etkisi matrisi

| # | Eğer introspection eklenirse | Tetiklenecek bağımlılık | Yön | Status |
|---|---|---|---|---|
| 1 | Endpoint K010'da tanımlanır | client auth (client_secret doğrulama) | K010 → K009 | PLANNED |
| 2 | RS çağrıları artar | HTTP timeout/retry politikası | K010 → dış | PLANNED |
| 3 | Yanıt cache | cache altyapısı (`CacheRateLimiter` yüzeyi) | K010 → K009 | PLANNED |
| 4 | `active:false` → 401 | mevcut 401 zinciri ile hizalanır | K010 iç | ✅ hazır (§5 #7) |

**Eşleme sonucu:** RFC 7662 bu repoda **karşı-model** olarak kaydedilir: CoreMusic
introspection yerine lokal JWT validate'i tercih eder (kanıtlı davranış). Yalnızca
gelecekteki RS-AS ayrışması senaryosunda K010 → K009 ek bağımlılığı doğar —
şimdilik defteri değiştirmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — RFC 8693 (OAuth 2.0 Token Exchange) ile bağımlılık eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc8693 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 8693, bir token'ı (veya SAML assertion'ını) başka bir token'a takas eden
endpoint'i tanımlar (`subject_token` · `actor_token` · `requested_token_type`).
CoreMusic'in mevcut zinciri: OAuth provider token'ı → AES-256-GCM saklama →
lokal RS256 JWT. Bu akış RFC 8693 terminolojisiyle aşağıda eşlenir.

### §8.1 — RFC 8693 maddeleri ↔ CoreMusic token zinciri

| # | RFC 8693 öğesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Token Exchange endpoint (§2.1) | repo'da yok | — | ❌ kanıt yok |
| 2 | `subject_token` + `subject_token_type` (§2.1) | provider access token (kaynak) | `OAuthRepository.php` L27-50 | ✅ kanıtlı (zincir girdisi) |
| 3 | `actor_token` (delege) | — | — | ❌ kanıt yok |
| 4 | `requested_token_type` (§2.1) | hedef: lokal JWT | `JwtService.php` L24 | ✅ kanıtlı (hedef) |
| 5 | `may_act` claim'i (§4.3) | — | — | ❌ kanıt yok |
| 6 | `act` claim'i (§4.2) | — | — | ❌ kanıt yok |
| 7 | `scope` istemi (§2.1) | provider scope'ları | `OAuthManager.php` L48-183 | ⚠️ VR (detay okunmadı) |

### §8.2 — CoreMusic token zinciri ↔ takas modeli

| # | Zincir adımı | RFC 8693 karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Provider'dan token alımı | exchange girdisi (`subject_token`) | `OAuthManager.php` L157-183 | ✅ kanıtlı |
| 2 | Token'ın AES-256-GCM şifrelenmesi | saklama (takas modeli dışı) | `OAuthManager.php` L188-205 | ✅ kanıtlı |
| 3 | `refresh_token_encrypted` yenileme | refresh takası (RFC 8693 değil, RFC 6749 §6) | `OAuthRepository.php` L27-50 | ✅ kanıtlı |
| 4 | Lokal RS256 JWT üretimi | `requested_token_type` karşılığı | `JwtService.php` L24 · L51 | ✅ kanıtlı (işlevsel eşdeğer) |
| 5 | JWT `jti` + `user_tokens` kaydı | takas kaydı | `IUserRepository.php` L40/45/50 | ✅ kanıtlı |

### §8.3 — Bağımlılık defterine etkisi

| # | Soru | Cevap (bu revizyon) | Status |
|---|---|---|---|
| 1 | RFC 8693 endpoint'i repoda var mı? | Hayır — kanıt yok | ❌ |
| 2 | Mevcut akış işlevsel olarak takas mı? | Evet (provider token → lokal JWT) | ✅ (eşdeğerlik yorumu) |
| 3 | Yeni bağımlılık doğar mı? | Hayır — ek ok eklenmez, mevcut §1 #2/#6 korunur | — |
| 4 | `oidc/`/`oauth2/` dosyalarına taşınır mı? | Evet — kademe-3 derinlemesine konu | — |

**Eşleme sonucu:** RFC 8693, CoreMusic token zincirine standart terminoloji
ekler; repoda takas **endpoint'i yoktur**, ancak işlevsel akış (provider token →
lokal JWT) aynı takas modelinin basitleştirilmiş halidir. Bağımlılık defterine
yeni ok **eklenmez**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — PHP `session_regenerate_id` resmi kılavuzu ile oturum bağımlılık eşlemesi

> **Kaynak:** https://www.php.net/manual/en/function.session-regenerate-id.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PHP resmi kılavuzu, `session_regenerate_id()` fonksiyonunun oturum kimliği
rotasyonu ve session fixation savunmasındaki rolünü tanımlar. Bu, K010'ın
`SessionLifecycle` bağımlılığının (§1 satırındaki middleware/oturum zincirinin
alt bileşeni) doğrudan platform karşılığıdır.

### §9.1 — Resmi kılavuz maddeleri ↔ SessionLifecycle kanıtları

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `session_regenerate_id()` mevcut oturum ID'sini değiştirir | her 1800sn'de çağrılır | `SessionLifecycle.php` L54-62 | ✅ kanıtlı |
| 2 | `delete_old_session=true` eski oturum dosyasını siler | parametre kullanımı bu revizyonla okunmadı | — | ⚠️ VR |
| 3 | Rotation fixation saldırısını azaltır (kılavuz notu) | rotasyon kasıtlı savunma | `SessionLifecycle.php` L54-62 | ✅ kanıtlı (niyet) |
| 4 | Oturum verisi korunur (aynı `$_SESSION`) | rotasyon sonrası veri taşıma | — | ⚠️ VR (davranış kanıtlanmadı) |
| 5 | Çağrı öncesi `session_start()` gerekir | HTTP pipeline'da session başlatılır | `SessionManagerMiddleware.php` (19 satır) · `SessionLifecycle.php` L21-69 | ✅ kanıtlı (zincir) |
| 6 | API bağlamında `session_start()` yoksa çağrı anlamsızdır | `ApiSessionManager` `session_start()` çağırmaz | `ApiSessionManager.php` L19-23 | ✅ kanıtlı (ayrı durum) |
| 7 | Eşzamanlı isteklerde uyarı (kılavuz notu) | — | — | ⚠️ VR (davranış testi yok) |

### §9.2 — Bağımlılık okları: `SessionLifecycle` ↔ platform

| # | Bağımlılık | Hedef | Yön | Kanıt | Status |
|---|---|---|---|---|---|
| 1 | `session_regenerate_id` (PHP çekirdeği) | platform | K010 → PHP | `SessionLifecycle.php` L54-62 | ✅ kanıtlı |
| 2 | `ROTATION_INTERVAL=1800` kararı | K010 config | K010 iç | `SessionConfig.php` L16-20 | ✅ kanıtlı |
| 3 | Session dosya deposu | K007-data (fiziksel) | K010 → K007 | `SessionConfig.php` L20 (`DIR_PERMISSIONS=0750`) | ✅ kanıtlı |
| 4 | `MAX_LIFETIME` < `ROTATION_INTERVAL`? | paradox (§4 #1) | — | 1800 = 1800 sınırında | ⚠️ VR |
| 5 | API köprüsünde rotation yok | `ApiSessionManager` | — | `ApiSessionManager.php` L169-172 | ✅ kanıtlı (paradox §4 #3) |

### §9.3 — Kılavuzun oturum paradoxuna etkisi

| # | Paradox (§4) | Kılavuz ışığında değerlendirme | Durum |
|---|---|---|---|
| 1 | `MAX_LIFETIME=1800` = `ROTATION_INTERVAL=1800` | her lifetime sınırında bir rotation — eşanlamlı tetikleyiciler | ⚠️ VR (davranışsal test yok) |
| 2 | `IDLE_TIMEOUT=3600` > `MAX_LIFETIME` | rotation kararı idle'ı etkilemez; idle kodu asla tetiklenmez (§4 #1) | ⚠️ VR |
| 3 | `delete_old_session` bilinmiyor | eski oturum dosyası silinmiyorsa fixation penceresi daralmaz | ⚠️ VR |

**Eşleme sonucu:** PHP `session_regenerate_id` kılavuzu, `SessionLifecycle`
rotasyon bağımlılığının platform kanıtıdır; `delete_old_session` parametresi ve
eşzamanlılık davranışı bu revizyonla okunmadığı için paradoxlar (§4 #1/#3)
**açık kalır**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
