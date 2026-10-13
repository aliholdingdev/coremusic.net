---
node_id: K010-identitiy/identity-web-arastirma
node_type: domain
status: verify-required
parent_id: K010-identitiy
children: []
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "8 tekil URL (openid.net ×2, datatracker.ietf.org ×5, iana.org ×1) — 2026-10-12 okundu"
  - "Kanıt paketi §C kök allokasyonu (bu dosya 8 URL; kimseyle paylaşılmaz)"
validation: "identity-dogrulama.md §6 (6 doğrulama URL'i — bu dosyadan farklıdır) · her iddia URL'ye bağlı"
group: kanit
open_questions:
  - "RFC 9449 (DPoP) CoreMusic API'sine taşınabilir mi? (token tipi Bearer — DPoP bound olmaz)"
  - "RFC 8252 (native app PKCE) — CoreMusic web-only mi, native var mı?"
---

# K010-identitiy — web araştırması (research)

> **Kural:** her iddia aşağıdaki tabloya bağlıdır; aynı URL başka hiçbir K010 dosyasında tekrarlanmaz (kanıt paketi §C). Araç: webfetch · tarih: 2026-10-12.

## §1 OIDC — OpenID Connect "How Connect Works"

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| OIDC, OAuth 2.0 üzerine inşa edilmiş identity layer'dır; `id_token` (JWT) ile kimlik kanıtı verir | https://openid.net/developers/how-connect-works/ | 2026-10-12 | doğrulandı |
| Akış: authorization → token → userinfo; RP identity provider ile `iss`/`aud`/`sub` bağ kurar | https://openid.net/developers/how-connect-works/ | 2026-10-12 | doğrulandı |
| CoreMusic `auth.coremusic.net` bir identity provider rolündedir; `coremusic-web` bir relying party'dir | https://openid.net/developers/how-connect-works/ | 2026-10-12 | eşleşti (Dosya: `shared\src\Config\AuthRouteConfig.php` / Satır: 7, 33-37) |

**Sonuç:** OIDC terminolojisi (RP, OP, `id_token`, `nonce`) kademe-2 `sso-entegrasyonu/` ve kademe-3 `oidc/` dosyalarının dilini belirler. CoreMusic'in `response_type='session'` kullanımı OIDC standart response type'ı DEĞİLDİR (`code`, `id_token`, `token`, `id_token token`) → özel/uzantı protokol, `⚠️ VERIFICATION REQUIRED`.

## §2 OAuth 2.0 — RFC 6749 (Authorization Framework)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Authorization Code grant: `code` → token exchange; `code` tek kullanımlık ve kısa ömürlü olmalı | https://datatracker.ietf.org/doc/html/rfc6749 | 2026-10-12 | doğrulandı |
| `state` parametresi CSRF koruması için zorunlu; istemci tarafında tahmin edilemez olmalı | https://datatracker.ietf.org/doc/html/rfc6749 | 2026-10-12 | doğrulandı — CoreMusic `sha256(state)` + tek kullanımlık silme ile karşılar (Dosya: `shared\src\OAuth\OAuthManager.php` / Satır: 160, 180) |
| Token endpoint istemci kimliğini gizlice doğrulamalı; client_secret asla tarayıcıda tutulmamalı | https://datatracker.ietf.org/doc/html/rfc6749 | 2026-10-12 | doğrulandı — gizli istemcilerde `OAUTH_{PROVIDER}_CLIENT_SECRET` sunucu tarafında (Dosya: `OAuthManager.php` / Satır: 240-247) |
| Refresh token: yalnız güvenli kanalda verilir; yenileme istemcisi kimliği doğrulanır | https://datatracker.ietf.org/doc/html/rfc6749 | 2026-10-12 | doğrulandı — `refresh_token_encrypted` AES-256-GCM (Dosya: `shared\src\OAuth\OAuthRepository.php` / Satır: 27-50) |

**Sonuç:** RFC 6749 §4.1 (Authorization Code) CoreMusic'in 10 provider için temel akışıdır; §10.12 (`state`) doğrulaması repoyla birebir örtüşür.

## §3 PKCE — RFC 7636 (Proof Key for Code Exchange)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| PKCE, `code_verifier`/`code_challenge` (S256) ile authorization code interception'ı engeller; public istemcilerde `client_secret` yerine kullanılır | https://datatracker.ietf.org/doc/html/rfc7636 | 2026-10-12 | doğrulandı |
| `code_verifier` yüksek entropi olmalı (43-128 karakter); `S256` tercih edilen challenge metodudur | https://datatracker.ietf.org/doc/html/rfc7636 | 2026-10-12 | doğrulandı |
| CoreMusic `code_verifier`'ı `saveState` ile saklar, `validateState` ile geri verir | https://datatracker.ietf.org/doc/html/rfc7636 | 2026-10-12 | eşleşti (Dosya: `OAuthManager.php` / Satır: 110-133 · `OAuthRepository.php` / Satır: 85-110) |

**Sonuç:** PKCE altyapısı repo'da mevcuttur (`code_verifier` alanı `oauth_states` tablosunda); hangi provider'ların PKCE zorunlu kıldığı kademe-3 `oauth2/oauth2-akislari.md` içinde provider-bazlı analiz edilir.

## §4 Native App Auth — RFC 8252

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Native uygulamalar authorization code + PKCE kullanmalıdır; embedded web view yerine system browser / claimed HTTPS URI (`app-redirect`) zorunludur | https://datatracker.ietf.org/doc/html/rfc8252 | 2026-10-12 | doğrulandı |
| Loopback redirect (`http://127.0.0.1:port`) yalnız geliştirme için kabul edilir | https://datatracker.ietf.org/doc/html/rfc8252 | 2026-10-12 | doğrulandı |

**Sonuç:** CoreMusic repo'sunda native (iOS/Android) uygulama kanıtı bu revizyon kapsamında OKUNMADI → RFC 8252 maddeleri yalnız gelecekteki native istemci için referans; `⚠️ VERIFICATION REQUIRED`.

## §5 OAuth Threat Model — RFC 6819

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| OAuth 2.0 tehdit modeli: authorization code injection, token leakage (referrer, logs), CSRF (state eksikliği), sesión fixation | https://datatracker.ietf.org/doc/html/rfc6819 | 2026-10-12 | doğrulandı |
| Azaltım: `state` üretimi istemcide, tek kullanımlık doğrulama, token'ları log'a yazmama | https://datatracker.ietf.org/doc/html/rfc6819 | 2026-10-12 | doğrulandı |

**Karşılaştırma (repo ↔ RFC 6819):**

| Tehdit | RFC 6819 azaltımı | CoreMusic uygulaması | Durum |
|---|---|---|---|
| Authorization code interception | PKCE (S256) | `code_verifier` + sha256(state) | ✅ kanıtlı |
| CSRF (state eksikliği) | tahmin edilemez `state` | `bin2hex(random_bytes)` benzeri üretici | ⚠️ VERIFICATION REQUIRED (üretim kodu okunmadı — yalnız `hash('sha256', $state)` doğrulandı) |
| Token sızıntısı (referrer/log) | log redaction, `no-referrer` | `LoggerFactory` securityEvent kullanımı | ⚠️ VERIFICATION REQUIRED (K009) |
| Token sızıntısı (storage) | şifreli depolama | AES-256-GCM (Dosya: `OAuthManager.php` / Satır: 188-205) | ✅ kanıtlı |

## §6 DPoP — RFC 9449 (Demonstrating Proof of Possession)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| DPoP, JWT tabanlı proof ile token'ın çalınmasına karşı kanıt (proof-of-possession) sağlar; `DPoP` header + `htm`/`htu` claim'leri | https://datatracker.ietf.org/doc/html/rfc9449 | 2026-10-12 | doğrulandı |
| `WWW-Authenticate: DPoP` ile server token tipini bildirir; bearer'a geçiş `WWW-Authenticate` üzerinden negotiate edilir | https://datatracker.ietf.org/doc/html/rfc9449 | 2026-10-12 | doğrulandı |

**Sonuç:** CoreMusic API'si şimdilik saf `Bearer` kullanır (Dosya: `AuthenticationMiddleware.php` / Satır: 84); DPoP'a geçiş ayrı ADR gerektirir — `identity-kararlar.md` §D-07.

## §7 IANA OAuth 2.0 Parameters Kayıt Defteri

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| `client_id`, `client_secret`, `redirect_uri`, `scope`, `state`, `code`, `code_verifier`, `code_challenge` IANA'da resmi kayıtlı parametrelerdir | https://www.iana.org/assignments/oauth-parameters/oauth-parameters.xhtml | 2026-10-12 | doğrulandı |
| CoreMusic'in kullandığı parametre adları (`client_id`, `response_type`, `redirect_uri`) IANA kaydıyla uyumlu | https://www.iana.org/assignments/oauth-parameters/oauth-parameters.xhtml | 2026-10-12 | eşleşti (Dosya: `AuthRouteConfig.php` / Satır: 58-67) |

## §8 OIDC Discovery 1.0

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| `.well-known/openid-configuration` metadata endpoint'i OP'nin yeteneklerini (issuer, authorization_endpoint, token_endpoint, jwks_uri, response_types_supported) yayınlar | https://openid.net/specs/openid-connect-discovery-1_0.html | 2026-10-12 | doğrulandı |
| `issuer` metadata'daki değer ile `id_token.iss` birebir eşleşmelidir; uyuşmazlık → token reddi | https://openid.net/specs/openid-connect-discovery-1_0.html | 2026-10-12 | doğrulandı |

**Sonuç:** CoreMusic `auth.coremusic.net` için discovery metadata'sı repo'da BULUNAMADI (`.well-known` dosyası okunmadı, `⚠️ VERIFICATION REQUIRED`); `JwtService` `iss` kontrolü metadata yerine config'ten okur (Dosya: `JwtService.php` / Satır: 96-98).

## §9 Araştırma Özeti

| # | Kaynak | K010'a etkisi | Taşınan karar |
|---|---|---|---|
| 1 | OIDC How Connect Works | RP/OP rol ayrımı | `sso-entegrasyonu/` + `oidc/` dilinin temeli |
| 2 | RFC 6749 | Authorization Code + state zorunluluğu | `oauth2/` anayasası |
| 3 | RFC 7636 | PKCE zorunluluğu (public istemci) | `oauth2/oauth2-akislari.md` |
| 4 | RFC 8252 | Native app kuralları | `⚠️` (native kanıt yok) |
| 5 | RFC 6819 | Tehdit modeli → risk defteri | `identity-riskler.md` R-04…R-07 |
| 6 | RFC 9449 | DPoP opsiyonu | `identity-kararlar.md` D-07 |
| 7 | IANA OAuth Parameters | Parametre ad standardı | `oauth2/oauth2-evrensel-dil.md` |
| 8 | OIDC Discovery | Metadata/issuer eşleşmesi | `oidc/oidc-dogrulama.md` |

Toplam benzersiz URL: 8 (bu dosya) + 6 (identity-dogrulama.md) = kök 14; kademe-2/3 dosyaları ayrı allokasyon kullanır.

## §10 — RFC 9068 (JWT Access Token Profile) — access token claim seti

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc9068 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 9068, OAuth 2.0 access token'ları için JWT profilini tanımlar: `typ:
at+jwt` header'ı, zorunlu claim seti (`iss` · `sub` · `aud` · `exp` · `iat`),
`client_id` claim'i ve scope taşıma. CoreMusic'in RS256 JWT'si bu profile
kısmen benzer; eşleme aşağıdadır.

| İddia | RFC 9068 maddesi | CoreMusic kanıtı | Tarih: 2026-10-13 | Durum |
|---|---|---|---|---|
| `typ: at+jwt` header'ı (§2.1) | zorunlu | `typ` kullanımı kanıtlanmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `iss` zorunlu claim (§2.2) | zorunlu | `JwtService.php` / Satır: 96-98 (config'ten okur) | 2026-10-13 | ✅ eşleşti |
| `sub` zorunlu claim (§2.2) | zorunlu | `JwtService.php` / Satır: 96-104 | 2026-10-13 | ✅ eşleşti |
| `aud` zorunlu claim (§2.2) | zorunlu | `JwtService.php` / Satır: 96-104 | 2026-10-13 | ✅ eşleşti |
| `exp` zorunlu claim (§2.2) | zorunlu | checklist'te satır-içi kanıt yok | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `iat` zorunlu claim (§2.2) | zorunlu | kanıt yok | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `client_id` claim'i (§2.2) | zorunlu | kanıt yok | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `scope` claim'i (§2.2) | opsiyonel | RBAC session'da (`findRolesForUser`) | 2026-10-13 | ⚠️ VERIFICATION REQUIRED (token scope'u) |
| `jti` token tekili (§2.2) | profil dışı ama CoreMusic'te var | `JwtService.php` / Satır: 51 | 2026-10-13 | ✅ eşleşti (uzantı) |
| Bearer taşıma (§2.1) | `Authorization: Bearer` | `AuthenticationMiddleware.php` / Satır: 84-85 | 2026-10-13 | ✅ eşleşti |

**Sonuç:** CoreMusic JWT'si profilin `iss/sub/aud` zorunluluklarıyla örtüşür;
`typ: at+jwt` ve `exp/iat/client_id` claim'leri bu revizyonla kanıtlanmadı →
`jwt/` kademe-3 dosyasına açık madde taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 — RFC 9126 (Pushed Authorization Requests) — PAR yüzeyi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc9126 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 9126, authorization request parametrelerinin back-channel'da PAR endpoint'ine
push edilip `request_uri` alınmasını tanımlar; tarayıcı yönlendirmesi yalnız
`request_uri + client_id` taşır. CoreMusic'te bu endpoint kanıtlanmamıştır —
bu bölüm yüzeyi **kayıt altına alır**.

| İddia | RFC 9126 maddesi | CoreMusic kanıtı | Tarih: 2026-10-13 | Durum |
|---|---|---|---|---|
| PAR endpoint'i `POST` + client auth (§2) | zorunlu | repo'da PAR endpoint'i yok | 2026-10-13 | ❌ YOK (kanıt yok) |
| `request_uri` tek kullanımlık (§4) | zorunlu | — | 2026-10-13 | ❌ |
| `expires_in` kısa ömür (§4) | önerilir | `oauth_states` 10 dk TTL benzeyen mekanizma | 2026-10-13 | ⚠️ benzerlik (PAR değil) |
| Back-channel parametre aktarımı (§1) | çekirdek | `saveState` DB'ye yazar | 2026-10-13 | ⚠️ işlevsel benzer (PAR değil) |
| Tarayıcı yönlendirmesinde yalnız `request_uri` (§5) | zorunlu | redirect tüm parametreleri taşır | 2026-10-13 | ⚠️ farklı model |
| `state` hâlâ zorunlu (§7.1) | korunur | `sha256(state)` tek kullanımlık | 2026-10-13 | ✅ eşleşti (temel koruma) |
| PKCE ile birlikte kullanım (§7.2) | önerilir | `code_verifier` `oauth_states`'te | 2026-10-13 | ✅ eşleşti (altyapı) |
| Idempotent yeniden kullanım reddi (§4) | zorunlu | state silinir → tek kullanımlık | 2026-10-13 | ✅ eşleşti (işlevsel) |

**Sonuç:** PAR repoda **yoktur**; mekanizma (`oauth_states` + tek kullanımlık
state + PKCE) PAR'a işlevsel olarak yakındır ama PAR protokolü değildir —
`oauth2/` kademe-3 dosyasına PLANNED madde olarak kaydedilir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 — RFC 9101 (JWT-Secured Authorization Request, JAR) — imzalı istek yüzeyi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc9101 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 9101, authorization request'in JWT olarak imzalanıp `request` parametresiyle
taşınmasını tanımlar. CoreMusic'te imzalı istek kanıtlanmamıştır; mevcut koruma
`state` + `redirect_uri`'dir.

| İddia | RFC 9101 maddesi | CoreMusic kanıtı | Tarih: 2026-10-13 | Durum |
|---|---|---|---|---|
| `request` parametresi JAR JWT taşır (§4) | çekirdek | repo'da `request` parametresi yok | 2026-10-13 | ❌ YOK |
| JAR imzalayıcı client ile aynı olmalı (§4) | zorunlu | — | 2026-10-13 | ❌ |
| JAR `exp` kısa ömür taşır (§4) | zorunlu | — | 2026-10-13 | ❌ |
| JAR ile `request_uri` birlikte (RFC 9126 §2) | opsiyonel kombinasyon | — | 2026-10-13 | ❌ |
| Parametre çakışması reddedilir (§5) | zorunlu | — | 2026-10-13 | ❌ |
| `state` JAR dışında da korunur (§1) | hâlâ gerekli | `sha256(state)` + tek kullanımlık | 2026-10-13 | ✅ eşleşti (temel) |
| `redirect_uri` allowlist | JAR öncesi koruma | `ReturnUrlPolicy.php` (160 satır) | 2026-10-13 | ⚠️ sahiplik gri (§3 #6) |
| JWT imza altyapısı mevcut mu? | JAR'ın ön koşulu | `JwtService` RS256 | 2026-10-13 | ✅ altyapı var |

**Sonuç:** JAR repoda **yoktur**; JAR'ın ön koşulu olan RS256 imza altyapısı
mevcuttur (kanıtlanmış), bu nedenle JAR gelecekteki `oauth2/` derinliğinde
**PLANNED** seçenek olarak kaydedilir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 — RFC 8176 (Authentication Method Reference Values) — amr/ACR yüzeyi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc8176 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 8176, `amr` (Authentication Method References) claim değerlerini kayıt
altına alır (`pwd` · `otp` · `mfa` · `hwk` · `face` · `fingerprint` …). MFA
K010'da tamamı PLANNED'dir (`identity-kapsam.md` §1 satır 8) — bu bölüm
kayıtlı `amr` değerlerini CoreMusic kimlik modeliyle eşler.

| İddia | RFC 8176 değeri | CoreMusic karşılığı | Tarih: 2026-10-13 | Durum |
|---|---|---|---|---|
| `pwd` — parola doğrulama | kayıt altındaki değer | `password_verify` (Argon2id) | 2026-10-13 | ✅ implemente (credentials) |
| `otp` — tek kullanımlık kod | kayıt altındaki değer | repo'da TOTP/OTP kodu yok | 2026-10-13 | ❌ PLANNED |
| `mfa` — çok faktörlü | kayıt altındaki değer | MFA implementasyonu yok | 2026-10-13 | ❌ PLANNED |
| `hwk` — donanım anahtarı | kayıt altındaki değer | cihaz sertifikası yok | 2026-10-13 | ❌ PLANNED |
| `face` · `fingerprint` — biyometrik | kayıt altındaki değer | yok | 2026-10-13 | ❌ PLANNED |
| `kba` · `swk` · `pin` diğerleri | kayıt altındaki değer | yok | 2026-10-13 | ❌ PLANNED |
| `amr` claim'i token'a yazılır | profil mekanizması | `amr` claim'i kanıtlanmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `acr` (OIDC §2) — kalite göstergesi | OIDC mekanizması | `acr` kanıtlanmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| `auth_time` oturum zamanı | OIDC mekanizması | `last_login_at` şema kolonu var | 2026-10-13 | ⚠️ bağlanmadı (VR) |
| `amr` yalnız oturum açan değerdir | RFC 8176 §2 | — | 2026-10-13 | bilgi |

**Sonuç:** RFC 8176, CoreMusic'te **yalnız `pwd` değerinin** implemente
olduğunu kayıt altına alır; `otp`/`mfa`/`hwk`/biyometrik değerler PLANNED
düzeyindedir. `mfa-ve-sertifika/` kademe-2 dosyası bu tabloyu başlangıç
koşulu olarak devralır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
