---
node_id: K0100-identitiy/identity-kapsam
node_type: domain
status: verify-required
parent_id: K0100-identitiy
children: []
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "v2 coremusic-katmanli-mimari-agaci-v2.md §2.10 (satır 331-350) — 2026-10-12"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php L33-38 PUBLIC_ROUTE_PATTERNS — 2026-10-12"
  - "shared/src/Middleware/PermissionMiddleware.php L23-25 (boş userId → pass) — 2026-10-12"
validation: "identity-dogrulama.md §2 (kapsam sınırı kontrolü) · ADR-097 §5.5 (grup ekseni) bekleniyor"
group: mimari
open_questions:
  - "Sınır: CSRF/CSP nonce üretimi K010'da mı, K009'da mı (nonce session'da üretiliyor)"
  - "Sınır: return-url allowlist (ReturnUrlPolicy) kimlik akışının parçası mı?"
---

# K0100-identitiy — kapsam (scope) tanımı

## §1 Kapsam İçindekiler

| # | Alan | Tanım | Kod kanıtı |
|---|---|---|---|
| 1 | Kullanıcı kimliği | `MM_UserID` · `MM_Username` · `MM_Email` · `MM_DisplayName` · `MM_AccountType` · `MM_Image` · `cm_gender` | `shared\src\Security\SessionKeys.php` L7-15 |
| 2 | Hesap yaşam döngüsü | kayıt · giriş · logout · şifre sıfırlama · tek kullanımlık auth key | `IUserRepository.php` L11-26 |
| 3 | Rol ve yetki | `findRolesForUser` (role_name + permissions[]) · role/permission kararı | `IUserRepository.php` L33 · `PermissionMiddleware.php` L27-45 |
| 4 | Oturum | başlatma · uzatma · rotation · idle/lifetime destroy · cookie parametreleri | `SessionLifecycle.php` L21-69 · `SessionConfig.php` L16-62 |
| 5 | Token | JWT issuance (RS256, jti) · validation (fail-closed) · jti revocation | `JwtService.php` L24-109 · `AuthenticationMiddleware.php` L83-100 |
| 6 | Credential doğrulama servisleri | login/register/forgot-password/reset DTO'ları · repository sözleşmeleri | `LoginRequest.php` (64) · `RegisterRequest.php` (84) |
| 7 | SSO / identity integration | `auth.coremusic.net` redirect · 10 OAuth provider · state tek kullanımlık | `AuthRouteConfig.php` L47-70 · `OAuthManager.php` L48-183 |
| 8 | MFA ve cihaz sertifikası | **repo'da implementasyon YOK** — yalnız tasarım (PLANNED) | `⚠️ VERIFICATION REQUIRED` |

## §2 Kapsam Dışındakiler

| # | Alan | Gerekçe | Sahip düğüm |
|---|---|---|---|
| 1 | Rate limiting politikası | kimlik doğrulama hız sınırı ayrı güvenlik politikasıdır | K009-security |
| 2 | CSP başlığı üretimi ve nonce politikası | `csp_nonce` session'da üretilir ancak CSP başlığı K009 politikasıdır | K009-security |
| 3 | CSRF doğrulama başlığı/middleware | `csrf_token` üretimi K010'da; doğrulama politikası K009'da | K009-security |
| 4 | Encryption altyapısı (AES-256-GCM, Argon2id) | algoritma seçimi ve key yönetimi güvenlik katmanı | K009-security |
| 5 | DB şeması, migration, BCNF | fiziksel model | K007-data |
| 6 | Domain event sahipliği ve yayılımı | `UserLoggedInEvent` vb. olayların yayılım politikası | K008-domain |
| 7 | UI/UX akış ekranları (login ekranı, gender seçimi) | ekran tasarımı | ui-design vault'u |
| 8 | Audio/embedded kimlik (cihaz lisans) | donanım kimliği | K001-K005 bandı |

## §3 Sınır Karar Matrisi (gri bölgeler)

| # | Gri bölge | Karar | Gerekçe (kanıt) | Status |
|---|---|---|---|---|
| 1 | `csrf_token` üretimi | **K010** (üretim session içindedir) · doğrulama K009 | `SessionLifecycle.php` L47-49 | kanıtlı |
| 2 | `csp_nonce` üretimi | **K010** (session) · başlık K009 | `SessionLifecycle.php` L52 | kanıtlı |
| 3 | `session_set_cookie_params` güvenliği | **K010** (SessionConfig) | `SessionConfig.php` L52-62 | kanıtlı |
| 4 | Bearer token parse + validate | **K010** (middleware çağırır, karar K010 servisindedir) | `AuthenticationMiddleware.php` L84-90 | kanıtlı |
| 5 | Public route tanımı | **K010** (auth akışları public'tir) | `AuthenticationMiddleware.php` L33-38 | kanıtlı |
| 6 | Return URL allowlist | **gri** — `ReturnUrlPolicy.php` (160 satır) var; sahipliği K009/K010 ortak | `shared\src\Security\ReturnUrlPolicy.php` | `⚠️ VERIFICATION REQUIRED` |
| 7 | Rate limit (login deneme) | **K009** — `CacheRateLimiter.php` var, K010'da değil | `shared\src\Security\CacheRateLimiter.php` | kanıtlı |
| 8 | API key yönetimi | **K009** — `ApiKeyRepository.php` | `shared\src\Security\ApiKeyRepository.php` | kanıtlı |

## §4 v2 §2.10 Karşılık Tablosu (eski 9 → yeni 6 + 3)

| v2 düğüm (satır) | Yeni kademe-2 | Kapsam notu |
|---|---|---|
| user-identity (339) | `kullanici-modeli` | kimlik anahtarı + profil alanları |
| account (340) | `kullanici-modeli` | hesap yaşam döngüsü (kayıt/reset) |
| credentials (341) | `dogrulama-servisleri` | credential doğrulama servisleri |
| session (342) | `oturum-yonetimi` | session yaşam döngüsü |
| tokens (343) | `oturum-yonetimi` + `jwt/` | token issuance/revocation |
| roles (344) | `rol-ve-yetki` | rol modeli |
| permissions (345) | `rol-ve-yetki` | permission modeli |
| device-identity (346) | `mfa-ve-sertifika` | cihaz kimliği + MFA (PLANNED) |
| identity-integration (347) | `sso-entegrasyonu` + `oidc/` + `oauth2/` | SSO entegrasyonu |

Birleştirme kuralı: iki veya daha fazla v2 düğümü mantıksal olarak aynı kademe-2 düğümünde toplanır; hiçbir v2 düğümü kapsam dışına itilmez. `mfa-ve-sertifika` v2'de doğrudan karşılığı olmayan (device-identity + yeni MFA gereksinimi) bir genişlemedir.

## §5 Kapsam Dışı İddia Defteri (bu node'un iddia etmediği şeyler)

| # | İddia (EDİLMEZ) | Neden |
|---|---|---|
| 1 | "MFA aktiftir" | repo'da TOTP/passkey/sertifika kodu YOK |
| 2 | "Refresh token rotasyonu uygulanmıştır" | `JwtService` içinde kanıt yok |
| 3 | "SAML entegrasyonu vardır" | SAML dosyası repo'da YOK |
| 4 | "OIDC sertifikalıyız" | sertifikasyon kanıtı yok (`openid.net/developers/certified/` kontrol edilmeli) |
| 5 | "Rate limit login'de uygulanır" | K009 kapsamı; bu node'da kanıt yok |

## §6 — OIDC Core 1.0 ile kapsam eşlemesi (resmi kaynak: OpenID Connect Foundation)

> **Kaynak:** https://openid.net/specs/openid-connect-core-1_0.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OIDC Core 1.0, §1 satır 7'deki "SSO / identity integration" kapsam satırını standart
dile bağlar. Aşağıdaki eşleme spec maddelerini bu node'un **kanıtlanmış** repo
satırlarıyla karşılaştırır: spec iddiaları resmi metinden, repo iddiaları yalnızca
bu vault'ta `Dosya:/Satır:` kanıtı taşıyan satırlardan kurulur.

### §6.1 — OIDC nesneleri ↔ K010 kapsamı

| # | OIDC nesnesi (Core 1.0) | CoreMusic karşılığı | Kanıt (bu vault) | Durum |
|---|---|---|---|---|
| 1 | Authorization Endpoint (§3.1) | `auth.coremusic.net` redirect akışı | `AuthRouteConfig.php` L47-70 | ✅ kanıtlı |
| 2 | Token Endpoint (§3.1.3) | OAuth provider token exchange (provider tarafı) | `OAuthManager.php` L157-183 | ✅ kanıtlı |
| 3 | `client_id` (§3.1.2.1) | IANA kayıtlı parametre adı kullanılıyor | `AuthRouteConfig.php` L58-67 | ✅ kanıtlı |
| 4 | `redirect_uri` (§3.1.2.1) | IANA kayıtlı parametre adı kullanılıyor | `AuthRouteConfig.php` L58-67 | ✅ kanıtlı |
| 5 | `response_type` (§3.1.2.1) | Repo `response_type='session'` — CoreMusic standart dışı değer | `AuthRouteConfig.php` L48-67 | ⚠️ VR (§4 #5) |
| 6 | `scope` (§3.1.2.1) | `openid` scope kullanılıp kullanılmadığı bu revizyonla okunmadı | — | ⚠️ VR |
| 7 | `state` (§3.1.2.1) | `sha256(state)` tek kullanımlık | `OAuthManager.php` L160 · L180 | ✅ kanıtlı |
| 8 | `nonce` (§3.1.2.1) | `csp_nonce` session'da üretilir — OIDC `nonce`'u ile karıştırılmamalı | `SessionLifecycle.php` L52 | ⚠️ VR (OIDC nonce'u kanıtlanmadı) |
| 9 | `id_token` (§2) | RS256 imzalı JWT üretimi `JwtService` — tipik rolü access token | `JwtService.php` L24 · L51 | ⚠️ VR (`id_token` üretimi kanıtlanmadı) |
| 10 | UserInfo Endpoint (§5.3) | Repo'da userinfo endpoint kanıtı bu revizyonla okunmadı | — | ⚠️ VR |
| 11 | RP → OP rol ayrımı (§1) | `auth.coremusic.net` OP · `coremusic-web` RP | `AuthRouteConfig.php` L7 · L33-37 | ✅ kanıtlı |
| 12 | `end_session_endpoint` (§2) | Logout domain event'i var | `UserLoggedOutEvent.php` (K22) | ✅ dosya var · endpoint VR |

### §6.2 — id_token doğrulama maddeleri ↔ JwtService kontrol zinciri

Core 1.0 §3.1.3.7, RP'nin `id_token`'ı doğrularken uyması zorunlu maddeleri sayar.
Bu node `JwtService::validate` zincirinin aynı maddeleri karşılayıp karşılamadığını
karşılaştırır (yalnız kanıtlanmış satırlar):

| # | Core §3.1.3.7 maddesi | JwtService karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `iss` — OP issuer'ı ile eşleşmeli | `iss` kontrolü config'ten okunur | `JwtService.php` L96-98 | ✅ kanıtlı |
| 2 | `aud` — RP client_id içermeli | `aud` kontrolü | `JwtService.php` L96-104 | ✅ kanıtlı |
| 3 | `sub` — son kullanıcı özdeşliği | `sub` kontrolü | `JwtService.php` L96-104 | ✅ kanıtlı |
| 4 | `jti` — tekil token kimliği | her token'da `jti` üretilir | `JwtService.php` L51 | ✅ kanıtlı |
| 5 | `exp` — son kullanma reddi | `exp` kontrolü satır-içi kanıtlanmadı (checklist §2'de yok) | — | ⚠️ VR |
| 6 | `iat` — veriliş zamanı | `iat` kontrolü satır-içi kanıtlanmadı | — | ⚠️ VR |
| 7 | `nonce` — replay koruması | OIDC nonce eşleşmesi kanıtlanmadı | — | ⚠️ VR |
| 8 | Alg allowlist (§3.1.3.7 madde 11) | RS256 sabit; allowlist disiplini ADR-097 (RFC 8725 hizası) | `JwtService.php` L24 | ⚠️ VR |
| 9 | Hata→reddetme (fail-closed) | `catch(\Throwable)→null`, ayrıntı sızdırılmaz | `JwtService.php` L88-94 | ✅ kanıtlı |
| 10 | Şifre doğrulama (`at_hash`/`c_hash`) | CoreMusic'te kanıt yok | — | ⚠️ VR |

### §6.3 — OIDC scope ↔ K010 oturum anahtarları

Core 1.0 §5.4, standart scope'ların UserInfo'ya hangi claim'leri açtığını tanımlar.
K010'da karşılığı olması beklenen oturum anahtarları `SessionKeys`'tedir:

| # | OIDC scope (Core §5.4) | Beklenen claim | K010 SessionKeys karşılığı | Kanıt | Durum |
|---|---|---|---|---|---|
| 1 | `openid` | `sub` | `MM_UserID` | `SessionKeys.php` L7-15 | ✅ kanıtlı (anahtar) |
| 2 | `profile` | `name` | `MM_DisplayName` | `SessionKeys.php` L7-15 | ✅ kanıtlı (anahtar) |
| 3 | `profile` | `picture` | `MM_Image` | `SessionKeys.php` L7-15 | ✅ kanıtlı (anahtar) |
| 4 | `profile` | `gender` (uzantı) | `cm_gender` | `SessionKeys.php` L7-15 | ✅ kanıtlı (anahtar) |
| 5 | `email` | `email` · `email_verified` | `MM_Email` · `is_verified` şema kolonu | `SessionKeys.php` L7-15 · `coremusic_auth.sql` | ✅ kanıtlı (anahtar+kolon) |
| 6 | `phone` | `phone_number` | karşılık yok | — | kapsam dışı |
| 7 | `address` | `address` | karşılık yok | — | kapsam dışı |

> Tablo yalnızca **anahtar adı eşleşmesini** kanıtlar; scope'un gerçekten OAuth
> isteğinde talep edilip edilmediği bu revizyonla okunmadı → `⚠️ VERIFICATION REQUIRED`.

### §6.4 — Core 1.0 standart response_type matrisi ↔ CoreMusic sapması

| # | Standart `response_type` (Core §3) | CoreMusic'te var mı? | Durum |
|---|---|---|---|
| 1 | `code` (Authorization Code) | kanıtlanmadı | ⚠️ VR |
| 2 | `id_token` (Implicit) | kanıtlanmadı | ⚠️ VR |
| 3 | `id_token token` (Hybrid) | kanıtlanmadı | ⚠️ VR |
| 4 | `code id_token` · `code token` · `code id_token token` (Hybrid) | kanıtlanmadı | ⚠️ VR |
| 5 | `session` (CoreMusic özel) | mevcut tek kanıtlanan değer | `AuthRouteConfig.php` L48-67 · ⚠️ standart dışı |

**Eşleme sonucu:** OIDC Core 1.0, `sso-entegrasyonu/` ve `oidc/` dosyalarının
terminolojik temelidir; ancak CoreMusic'in kanıtlanmış tek SSO mekanizması
(`response_type='session'`) standart response type listesinin hiçbirine girmez →
düğüm `verify-required` kalır ve `oidc/` kademe-3 klasörü **PLANNED** statüsünü
korur.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — RFC 9207 (Authorization Server Issuer Identification) ile kapsam eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc9207 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 9207, authorization response'a `iss` (issuer) parametresi ekleyerek **mix-up
saldırısını** azaltır: istemci, yanıtın hangi authorization server'dan (AS)
geldiğini kriptografik olmayan bir parametreyle teyit eder. CoreMusic çoklu
provider (10 OAuth provider) kullandığı için bu madde seti K010 kapsamının
"SSO / identity integration" satırını doğrudan ilgilendirir.

### §7.1 — RFC 9207 maddeleri ↔ CoreMusic SSO kanıtları

| # | RFC 9207 maddesi | CoreMusic karşılığı | Kanıt (bu vault) | Durum |
|---|---|---|---|---|
| 1 | AS, authorization response'a `iss` eklemeli (§2.1) | Repo'da `iss` response parametresi kanıtlanmadı | — | ⚠️ VR |
| 2 | İstemci `iss`'yi tahmin edilen AS ile karşılaştırmalı (§2.2) | Client tarafı kontrol kanıtlanmadı | — | ⚠️ VR |
| 3 | `iss` tek başına mix-up'ı kapatır; `state` hâlâ zorunlu (§1) | `sha256(state)` tek kullanımlık mevcut | `OAuthManager.php` L160 · L180 | ✅ kanıtlı |
| 4 | Çok-AS senaryosunda `iss` olmadan token yanlış AS'a sunulabilir (§1) | CoreMusic 10 provider kullanır (çok-AS) | `OAuthManager.php` L48-183 (10 provider) | ✅ kanıtlı (risk bağlamı) |
| 5 | `iss` bir query response parametresidir, header değildir (§2.1) | Repo'da header-tabanlı AS kimliği kanıtlanmadı | — | ⚠️ VR |

### §7.2 — Mix-up senaryosu ↔ K010 azaltım matrisi

| # | Mix-up adımı (RFC 9207 §1) | CoreMusic azaltımı | Durum |
|---|---|---|---|
| 1 | Saldırgan istemciyi kötü AS'a yönlendirir | `redirect_uri` allowlist `ReturnUrlPolicy` (sahiplik gri — §3 #6) | ⚠️ VR |
| 2 | İyi AS yanıt üretir; saldırgan bunu kötü AS'a ait sanar | `iss` parametresi YOK → sanı riskte | ⚠️ VR (eksiklik kanıtı) |
| 3 | İstemci token'ı yanlış AS'a sunar | Provider endpoint seçimi provider-bazlı | `OAuthManager.php` L48-183 | 
| 4 | `state` tek kullanımlık → replay engellenir | `validateState` + silme | `OAuthManager.php` L180 | ✅ kanıtlı |
| 5 | Token depolama AES-256-GCM | şifreli saklama | `OAuthManager.php` L188-205 | ✅ kanıtlı |

### §7.3 — K010 ↔ K009 sınırında `iss` sorumluluğu

| # | Sorumluluk | Önerilen sahip | Gerekçe | Status |
|---|---|---|---|---|
| 1 | AS issuer metadata (§3) | K010 (`oidc/` kademe-3) | issuer kimliği kimlik akışının parçası | PLANNED |
| 2 | `iss` response parametresi üretimi | K010 | SSO redirect zinciri | PLANNED |
| 3 | Provider listesinin doğrulanması | K010 (`oauth2/`) | 10 provider kataloğu | PLANNED |
| 4 | Redirect allowlist politikası | gri (K009/K010 ortak — §3 #6) | `ReturnUrlPolicy` sahipliği ADR-097 G2 | ⚠️ VR |

**Eşleme sonucu:** RFC 9207, CoreMusic'in kanıtlanmış SSO mimarisinde **eksik
azaltım** tespit eder: `state` koruması mevcut (kanıtlı) ancak `iss` response
parametresi repo'da kanıtlanmamıştır. Çok-provider (10 provider) ortamında bu
eksiklik `oidc/` kademe-3 dosyasına açık soru olarak taşınır — `⚠️ VERIFICATION
REQUIRED` (repo'da olmayabilir de; kanıt yok).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — HTTP Set-Cookie nitelikleri ile kapsam eşlemesi (resmi kaynak: MDN)

> **Kaynak:** https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

MDN `Set-Cookie` referansı, oturum çerezinin niteliklerini (attribute) standart
dille tanımlar. K010 §3 #3, `session_set_cookie_params` güvenliğinin bu node'a
ait olduğunu kanıtlamıştı; bu bölüm aynı çerez yapılandırmasını nitelik
nitelik karşılaştırır.

### §8.1 — Set-Cookie nitelikleri ↔ SessionConfig değerleri

| # | Nitelik (MDN) | Anlamı | CoreMusic değeri | Kanıt | Durum |
|---|---|---|---|---|---|
| 1 | `HttpOnly` | JS erişimi kapalı (XSS çerez hırsızlığına karşı) | `httponly=true` | `SessionConfig.php` L59 | ✅ kanıtlı |
| 2 | `SameSite` | Kökenler arası gönderim sınırlaması | `samesite=Lax` | `SessionConfig.php` L52-62 | ✅ kanıtlı |
| 3 | `Max-Age` / `Expires` | Çerez ömrü | `lifetime=0` (session cookie) | `SessionConfig.php` L52-62 | ✅ kanıtlı |
| 4 | `Secure` | Yalnız HTTPS | bu revizyonla satır-içi kanıtlanmadı | — | ⚠️ VR |
| 5 | `Path` | İstek yolu kapsamı | bu revizyonla satır-içi kanıtlanmadı | — | ⚠️ VR |
| 6 | `Domain` | Alan adı kapsamı | bu revizyonla satır-içi kanıtlanmadı | — | ⚠️ VR |
| 7 | `__Host-` öneki | Host-only + Secure + Path=/ | kullanılmıyor (kanıt yok) | — | PLANNED (öneri) |
| 8 | `__Secure-` öneki | Secure zorunlu önek | kullanılmıyor (kanıt yok) | — | PLANNED (öneri) |

### §8.2 — SameSite değer matrisi ↔ K010 oturum davranışı

| # | Değer (MDN) | Davranış | CoreMusic uyumu | Durum |
|---|---|---|---|---|
| 1 | `Strict` | Hiçbir kökenler arası istekte gönderilmez | kullanılmıyor | bilgi |
| 2 | `Lax` | GET navigasyonunda gönderilir; POST/form'da gönderilmez | **kullanılan değer** | ✅ kanıtlı |
| 3 | `None` | Her zaman gönderilir; `Secure` zorunlu | kullanılmıyor | bilgi |
| 4 | (yok) | Eski tarayıcıda davranış tarayıcıya bağlı | — | bilgi |

> `SameSite=Lax` + CSRF token (`csrf_token`, `SessionLifecycle.php` L47-49) birlikte
> iki katmanlı CSRF azaltımı oluşturur; token doğrulama politikası K009'dadır (§2 #3).

### §8.3 — Oturum çerezinin sunucu tarafı karşılığı

| # | Çerez niteliği | Sunucu tarafı karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `lifetime=0` | sunucu oturumu `MAX_LIFETIME=1800` içinde biter | `SessionConfig.php` L16-20 | ✅ kanıtlı |
| 2 | `HttpOnly` | JS `document.cookie`'dan okunamaz | `SessionConfig.php` L59 | ✅ kanıtlı |
| 3 | Çerez süresi > oturum ömrü (§4 #2 paradoxu) | `COOKIE_EXPIRY=42000` > `MAX_LIFETIME=1800` | `SessionConfig.php` L16-17 | ⚠️ VR (paradox) |
| 4 | Rotasyon | `session_regenerate_id(true)` | `SessionLifecycle.php` L54-62 | ✅ kanıtlı |
| 5 | Dosya sistemi oturumu | `DIR_PERMISSIONS=0750` | `SessionConfig.php` L20 | ✅ kanıtlı |

**Eşleme sonucu:** MDN nitelik listesiyle karşılaştırıldığında CoreMusic oturum
çerezinin `HttpOnly` + `SameSite=Lax` + session-cookie üçlüsü kanıtlanmıştır;
`Secure` · `Path` · `Domain` nitelikleri bu revizyonla satır-içi okunmadı →
`⚠️ VERIFICATION REQUIRED` (K010/K009 sınırında `Secure` zorunluluğu ADR-097'de
değerlendirilmelidir).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — RFC 5280 (X.509) ile mfa-ve-sertifika kapsamı eşlemesi (PLANNED yüzey)

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc5280 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 5280, X.509 sertifika ve CRL profilini tanımlar. §1 satır 8'deki "MFA ve cihaz
sertifikası" kapsam satırının **tamamı PLANNED**'dir (repo'da implementasyon yok);
bu bölüm, PLANNED yüzeyin standart dilini önceden bağlar — kanıt iddiası
**değildir**, şartname referansıdır.

### §9.1 — Sertifika profili bileşenleri ↔ PLANNED cihaz sertifikası

| # | RFC 5280 bileşeni | PLANNED CoreMusic karşılığı | Durum |
|---|---|---|---|
| 1 | `issuer` / `subject` DN alanı | cihaz sertifikası DN modeli (tasarım yok) | PLANNED |
| 2 | `validity` (notBefore/notAfter) | cihaz sertifika ömrü | PLANNED |
| 3 | `subjectPublicKeyInfo` | cihaz anahtar çifti | PLANNED |
| 4 | `extensions: basicConstraints` | CA/leaf ayrımı | PLANNED |
| 5 | `extensions: keyUsage` | dijital imza kısıtı | PLANNED |
| 6 | `extensions: extendedKeyUsage` (clientAuth) | istemci kimlik doğrulama kullanımı | PLANNED |
| 7 | `serialNumber` | sertifika tekili (revocation anahtarı) | PLANNED |
| 8 | İmza algoritması (ör. sha256WithRSAEncryption) | CA imzası | PLANNED |

### §9.2 — Revocation mekanizmaları ↔ PLANNED denetim

| # | RFC 5280 mekanizması | Kullanım senaryosu | CoreMusic durumu |
|---|---|---|---|
| 1 | CRL (§5.3) | iptal listesi indirme + kontrol | PLANNED (repo'da yok) |
| 2 | OCSP (RFC 6960 — bu dosyada kanıtlanmadı) | online iptal sorgusu | PLANNED (repo'da yok) |
| 3 | CRL Distribution Points (§5.2.5) | CRL kaynağı reklamı | PLANNED |
| 4 | Authority Information Access (§5.1) | OCSP/CA kaynağı reklamı | PLANNED |

### §9.3 — MFA bağlamında sertifika ↔ mevcut kanıt boşluğu

| # | Bağlam | Mevcut repo durumu | Durum |
|---|---|---|---|
| 1 | MFA implementasyonu (TOTP/WebAuthn) | repo taramasında bulunamadı | ❌ YOK (PLANNED) |
| 2 | Cihaz sertifikası deposu | `DeviceCertificateStore` bileşeni yok | PLANNED (`identity-bilesenler.md` §5 #2) |
| 3 | Cihaz kimliği (device-identity legacy klasörü) | legacy klasör diskte, içerik bu revizyonun dışında | ⚠️ VR |
| 4 | Sertifika tabanlı client auth (OAuth) | provider auth client_secret tabanlı | ✅ kanıtlı (`OAuthManager.php` L240-247) |
| 5 | WebAuthn/Passkey | repo'da yok | ❌ YOK (PLANNED) |

**Eşleme sonucu:** RFC 5280 bu node'da **gelecek şartname** olarak kaydedilir;
`mfa-ve-sertifika/` düğümü hiçbir sertifika iddiası taşımaz (repo'da implementasyon
YOK — §1 satır 8 ile aynıdır). Sertifika tasarımı başladığında §9.1 matrisi
`mfa-ve-sertifika/` dosyasına taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
