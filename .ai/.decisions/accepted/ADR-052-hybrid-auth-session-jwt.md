---
title: "CoreMusic — ADR-052: Hybrid Auth Session + JWT (session/JWT sınırı · köprü · token ömrü + rotasyon · logout/iptal · cross-subdomain cookie)"
type: "architecture-decision"
category: "security"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic hibrit auth kararı: (a) sınır = tarayıcı/sunucu-state'i session, API/mobil/servisler-arası JWT, (b) köprü = ApiSessionManager + ADR-047 imzalı köprü token'ı + ADR-043 subdomain hizası, (c) ömür = kısa TTL access (≤15 dk) + rotasyonlu tek kullanımlık refresh + reuse yakalama → aile iptali, (d) iptal = sunucu tarafı reddetme listesi/token versiyonu + refresh reddi (access TTL'i kadar gecikme kabul), (e) cross-subdomain = cookie domain .coremusic.net (ADR-043) + Origin/CSRF/nonce katmanı (SameSite tek başına yeterli değil)"
kaynak: "Disk/kod kanıtı taraması (2026-09-29: ApiSessionManager.php 206 satır IMPLEMENTED · composer.json jwt/lcobucci 0 isabet · validateJwtToken stub null (AuthenticationMiddleware.php:111-131) · logout session yüzeyi IMPLEMENTED (AuthService.php:200-204, AuthController.php:215, SessionMiddleware.php:88) · kendi token iptal yüzeyi 0 (token_blacklist/revoked_token/token_version 0) · PageRouterKernel.php:285-296 pipeline = opencode.json frozen sıra) + web araştırması (4 sorgu / 25 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-052: Hybrid Auth Session + JWT (Hibrit Kimlik Doğrulama)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-052-hybrid-auth-session-jwt`
> **İlgili kararlar:** [[ADR-011-session-management]] (session yaşam döngüsü + hibrit saklama kararı — bu ADR'nin session zemini) · [[ADR-010-csrf-protection-strategy]] (CSRF + **şart 3**: JWT stub kapanana kadar cookie-auth tek yol) · [[ADR-013-rate-limiting-apcu]] (rate limit — köprü/token uçlarına uygulanır) · [[ADR-020-api-public-security]] (API auth üçlüsü + JWT Bearer kilidi; stub `null` bulgusu) · [[ADR-043-auth-subdomain-consolidation]] (cookie domain tekliği, Origin/CSRF/nonce, JWT anahtarı tek kaynak) · [[ADR-047-login-redirect-session-bridge]] (imzalı tek kullanımlık köprü token'ı) · [[ADR-008-bypass-auth-middleware]] (bypass = üretimde fail-closed) · [[ADR-004-multi-domain-spa]] (çoklu domain SPA + cookie haritası) · [[../index.md]] · [[../../raw/brain.md]]
> **Index durumu:** `.ai/.decisions/index.md` **ADR-051–ADR-060 satırlarını İÇERMEZ** — dizin 050 (satır 91) → 061 (satır 92) arasında **atlıyor**. Bu işlemde index.md'ye **yeni satır eklenmedi** (report-only — In-Place Refactoring + SRP); satır ekleme **bir sonraki vault reset'ine ertelenmiştir** (§5.1 adım 7).
> **ADR-051 notu:** ADR-051 numarası **kaynaksız boşluk** olarak atlanmıştır (dosya diskte YOK, index satırı YOK, karar metni YOK) → bu ADR o boşluğu **doldurmaz**, numara **kullanılmaz**; ayrıntı §5.1 adım 7 ve §7.1.
> **Frozen notu:** ADR-001–037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir; **Frozen'a geçiş YOK**.
> **Slug uyumsuzluğu (rapor):** `shared/src/Api/Auth/ApiSessionManager.php:9` `@see ADR-052-hybrid-auth` yazar; bu dosyanın gerçek slug'ı `ADR-052-hybrid-auth-session-jwt`'dir. Kod dosyası **bu işlemde düzenlenmedi** (report-only — kod dosyası adı/şablonu Backend domaininde, onaysız değişiklik yasak).

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıt — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **STUB** = kod var ama gövde boş (geri dönüş değeri sabit) · **0 YÜZEY** = aranıyor, isabet yok.

#### A) Session köprüsü — `ApiSessionManager.php` IMPLEMENTED (206 satır)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Api/Auth/ApiSessionManager.php:1-10` | Başlık `API-side session bridge for hybrid auth (ADR-052)` + `@see ADR-052-hybrid-auth` | **IMPLEMENTED** (dosya var, 206 satır) |
| `:24` | `final class ApiSessionManager implements ISessionManager` — `CoreMusic\Contracts\Auth\ISessionManager` | **IMPLEMENTED** |
| `:16-23` | `session_start()` **ÇAĞIRMAZ**: aktif oturum varsa `$_SESSION` üzerinden okur/yazar, yoksa istek-boyu `$store` belleği; API pipeline'ı cookie üretmez | **IMPLEMENTED** (köprü tek yönlü: API → mevcut session) |
| `:62-70` | `destroy()` → `$store=[]`, `$_SESSION=[]` (aktifse) | **IMPLEMENTED** (session sonlandırma yüzeyi) |
| `:72-77` | `regenerateId()` → `session_regenerate_id(true)` (aktif + header gönderilmemişse) | **IMPLEMENTED** (fixation ilacı köprüde de var) |
| `:148-167` | `isIdleExpired()` → hep `false` ("idle timeout web oturumunun sorumluluğu"), `touch()`/`rotateIfNeeded()`/`clearDisplayCookies()` → **no-op** | **IMPLEMENTED** (bilinçli sınır: zamanlama session katmanında — ADR-011) |
| `:35-60` | `setAuthUser`/`getUserId`/`isAuthenticated` — state tek yanlı | **IMPLEMENTED** |

#### B) JWT — üretim 0, doğrulama STUB (ADR-020/011 bulguları teyit edildi)

| Kanıt | İçerik | Etiket |
|---|---|---|
| Tüm `composer.json` (jwt/lcobucci taraması) | `jwt` / `lcobucci` **0 isabet** | **JWT paketi YOK** |
| Repo geneli `lcobucci\|firebase/php-jwt\|web-token` | Yalnız **vault markdown** içinde (`.ai/raw/brain.md:74,109,136`, `.ai/.agents/backend-architect.md:81,118,188`, arşiv prompt'lar) — kod/composer **0** | `brain.md:74,109` `lcobucci/jwt` iddiası → **⚠️ VERIFICATION REQUIRED** (kod/ağacın 3 composer.json'ında geçmiyor — `.ai/.agents/backend-architect.md:118` aynı bulguyu taşıyor) |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php:111-131` | `validateJwtToken(string $token): null` — "simplified implementation… return null (not validated)", `RS256 verification` yalnız **yorum satırı** (:121) | **STUB** (Bearer yolu fiilen ÖLÜ — ADR-020 §1.1-B.2 ile aynı) |
| Kodda `new Builder` / token üretimi / `HS256\|RS256` imzalama | Yalnız yorum (`AuthenticationMiddleware.php:121`) — **üretim 0** | **0 YÜZEY** (JWT üretilmiyor, doğrulanmıyor) |

#### C) Logout / iptal yüzeyi — session VAR, kendi token iptali 0

| Kanıt | İçerik | Etiket |
|---|---|---|
| `auth.coremusic.net/include/Service/AuthService.php:200-204` | `logout()` → `session->destroy()` + `session->clearDisplayCookies()` | **IMPLEMENTED** (session iptali) |
| `auth.coremusic.net/include/Controller/AuthController.php:215-227` | `handleLogout()` + `authEvent('logout')` audit | **IMPLEMENTED** |
| `auth.coremusic.net/pages/logout.php:79` | `POST /v1/auth/logout` (CSRF header + `credentials:include`) | **IMPLEMENTED** |
| `auth.coremusic.net/include/Middleware/SessionMiddleware.php:88` | `session_destroy()` | **IMPLEMENTED** |
| `token_blacklist` / `revoked_token` / `token_version` / `denylist` (kod + SQL) | **0 isabet** (yalnız `.ai/archives/prompt1-spa-router-2026-09-01.md:733` ve prompt arşiv notları = niyet) | **0 YÜZEY** — kendi tokenımız için **iptal mekanizması YOK** |
| `revoke` eşleşmeleri | Yalnız `shared/src/OAuth/Provider/*OAuth.php` + `IOAuthProvider::revokeToken()` (12 sağlayıcı — **üçüncü taraf** token'ı) | **IMPLEMENTED (kapsam dışı)** — CoreMusic kendi token'ını iptal etmiyor |

#### D) Middleware zinciri — frozen sıra kodda birebir DOĞRULANDI

| Kaynak | Sıra | Etiket |
|---|---|---|
| `.opencode/opencode.json:57,79` | `OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation` ("frozen pipeline") | direktif |
| `shared/src/PageRouter/PageRouterKernel.php:285-296` (`buildDefaultMiddlewares()`) | `OriginCheckMiddleware → CorsMiddleware → RateLimiterMiddleware → SecurityHeadersMiddleware → SessionManagerMiddleware → CsrfMiddleware → BypassAuthMiddleware → AuthMiddleware → PermissionMiddleware → ValidationMiddleware` | **IMPLEMENTED — sıra birebir eşleşiyor (10/10)** |

#### E) Öncül ADR'lerin bulguları (tekrar doğrulandı)

| Kayıt | İçerik | Etiket |
|---|---|---|
| [[ADR-011-session-management]] `:25,31,34,37` | OWASP seti + **hibrit saklama** (sunucu session = SSOT; JWT access ≤15 dk + rotasyonlu tek kullanımlık refresh); cookie `domain=.coremusic.net` IMPLEMENTED; `restartFresh()` logout invalidation IMPLEMENTED; **JWT stub → hibrit saklama PLANNED** | DOĞRULANDI (karar metni + kod bulgusu tutarlı) |
| [[ADR-043-auth-subdomain-consolidation]] `:43,56,82,84,85` | Cookie domain tek değer `.coremusic.net` **8 nokta IMPLEMENTED**; **E1: OriginCheck boş-Origin'de fail-open** (`shared/.../OriginCheckMiddleware.php:36-41`); E3 suffix-match; E4 `/validate-key` origin muaf | DOĞRULANDI (bu ADR risklerine girer) |
| [[ADR-020-api-public-security]] `:45,58,72` | Session/cookie → sonra `Bearer `; `validateJwtToken()` hep `null` → Bearer yolu ÖLÜ; API key doğrulama yok; pipeline kaydı bu ADR'de şart | DOĞRULANDI |
| [[ADR-047-login-redirect-session-bridge]] `:9,75` | Tek kullanımlık imzalı TTL köprü token'ı; `HomeAuthBridge` `AUTH_KEY_TTL=300` yalnız kimlik IMPLEMENTED, hedef+oturum verisi PLANNED | DOĞRULANDI |

> **Bulgu özeti:** CoreMusic'te **session tarafı canlı** (köprü + logout + fixation koruması IMPLEMENTED), **JWT tarafı tamamen kağıtta** (paket 0, üretim 0, doğrulama stub, kendi token iptali 0). Bu ADR bu asimetriyi **sınır tablosu** ile tanımlar ve iptal problemi (d) maddesini açıkça sahiplenir.

### §1.2 Sorun Tanımı (Problem)

Beş sorun üst üste biniyor. **(1) Sınır yok:** "tarayıcıda session mı, API'de JWT mi?" sorusunun bağlayıcı tek kaydı yok — ADR-011 hibrit saklamayı kararlaştırıyor ama *hangi istemcinin hangi kimlikle* çalıştığını yazmıyor; her yeni uç (BFF, mobil, gömülü cihaz) kendi yolunu seçebilir. **(2) Köprü tanımsız:** `ApiSessionManager` kodda var (§1.1-A) ama "API, session'ı ne zaman okur, JWT'yi ne zaman kabul eder" kuralı ADR'de değil → köprü ile JWT arasında ikili yorum riski. **(3) Ömür/rotasyon yazılmadı:** access TTL, refresh rotasyonu ve **reuse yakalama** yalnız ADR-011/020'de taslak; aile iptali kimde, ne zaman uygulanır belirsiz. **(4) İptal deliği:** session iptali IMPLEMENTED ama JWT üretilmeye başlandığı gün "üretimde iptal yok" sorunu doğar — `token_blacklist`/versiyon **0** (§1.1-C). **(5) Cross-subdomain yüzeyi:** cookie `domain=.coremusic.net` zaten geniş (8 nokta) ve OriginCheck boş-Origin'de **fail-open** (ADR-043 E1) — hibrit auth bu iki olguyu **varsayılan kabul ederek** kurulmalı, sonra düzeltmek için değil. Ek boşluk: **ADR-051 numarası kaynaksız atlandı** (§5.1 adım 7) ve **index.md 051–060 satırlarını içermiyor** (künye index durumu).

### §1.3 Web'den Araştırma Raporu & Sonuçları

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅ — birincil/resmî kaynak önce; her ana iddia ≥2 bağımsız çapraz kaynak). Tarih: 2026-09-29.

| Alan | Değer |
|------|-------|
| Web Search **Query** | 4 sorgu: (1) "session vs JWT 2025 best practice when to use server-side sessions instead of stateless tokens" (2) "refresh token rotation reuse detection OAuth 2.0 BCP RFC 9700 token revocation 2025" (3) "JWT token revocation strategies server-side denylist token version invalidate access token short TTL 2025" (4) "cross-subdomain shared cookie session SSO security SameSite CSRF sibling subdomains 2025 session fixation regenerate id" |
| Web Search **Konusu** | Session ↔ JWT seçim kriteri (kimin kontrol istediği, state nerede), rotasyonlu refresh + reuse tespiti (RFC 9700 BCP 240), JWT iptal stratejileri (denylist / kısa TTL + refresh reddi / token versiyonu / hibrit), cross-subdomain paylaşımlı cookie (SSO ön koşulu + XSS/cookie-tossing yüzeyi) ve SameSite'in kardeş alt alanlara yetmezliği, session fixation (WSTG-SESS-03 — login'de id yenileme) |
| Web Search **Bağlam** | Karar disk gerçeğiyle yüzleşiyor: session canlı (IMPLEMENTED), JWT paket/üretim/doğrulama = **0/0/STUB**, kendi token iptali **0** (§1.1). Araştırma 2026-09-29'da yapıldı; 4 sorgu / 25 adlandırılmış kaynak; birincil kaynak olarak **RFC 9700 (BCP 240, Ocak 2025)** bizzat okundu. CoreMusic'e taşınan sonuçlar §2 (a)–(e) kalemlerine bağlanmıştır |
| Web Search **Kısa Açıklama** | Üç ailede toplanıyor: **(i) seçim kriteri** — tek-alan/tarayıcı-öncelikli akışta sunucu-side session (denetim + anlık iptal), API/mobil/çok-alanda token (stateless ölçek); JWT'nin "session yerine" önerilmesi 2016'dan beri tartışmalı, varsayılan session'dır. **(ii) ömür+rotasyon** — RFC 9700 §4.14.2: public client'larda refresh **sender-constrained OLA rotasyonlu** (MUST); rotasyon her yanıtta yeni refresh üretir, eskisini **ilişkisiyle birlikte saklayarak** iptal eder; geçersiz refresh tekrar gelirse **ihlal sinyali** = aile iptali + yeniden yetkilendirme. **(iii) iptal** — erişim token'ı stateless olduğu için anlık iptal ya denylist (Redis) ya **token versiyonu** ya da "kısa TTL + refresh'i reddet" ile sağlanır; hibrit (kısa TTL + rotasyon + seçici denylist) en dengeli. **(iv) çapraz alan** — `Domain=parent` SSO'nun ön koşulu ama XSS yüzeyini kardeş alt alanlara yayar; SameSite kardeş alt alanları **korumaz**, savunma Origin + token + nonce katmanıdır; fixation'in ana ilacı login'de `session_regenerate_id` |
| Web Search **Uzun Açıklama** | (a) **Session vs JWT:** Authgear tablosu "session = tek domain/tarayıcı akışları; token = API/SPA/mobil/çok-alanda" der; LoginRadius aynı ayrımı "denetim vs esneklik" olarak kurar; joepie91 "Stop using JWT for sessions" (HttpOnly+Secure session cookie'nin erişilemezliğini) ve Stack Overflow/Reddit tartışmaları JWT'nin oturum için **varsayılan olmadığını** tekrarlar — yani literatür "hibrit"i, "her yerde JWT"yi değil, **kontrol session'da, taşınabilirlik JWT'de** olarak önerir. (b) **Rotasyon:** RFC 9700 §4.14.2 ("prev refresh invalidated, relationship retained → breach bildirimi → active refresh revoke → forced fresh grant") + Auth0 otomatik reuse detection diyagramı (aile iptali, `ferrt` log olayı) + ObsidianSecurity "reuse detection olmadan rotation minimal fayda" + Ssojet "silinen eski refresh = alarmın kendisi yok edilir" + Paragon "eşzamanlı refresh yarışı gerçek risk" + CerberAuth "reuse grace period" ve "RFC 'the active refresh token'ı revoke eder, her şeyi sürmek değil" — altı kaynak aynı üç kuralda buluşur: **ilişki saklanır, aile iptal edilir, yarış penceresi yönetilir.** (c) **İptal:** SuperTokens 7 yol (kısa ömür, versiyon, denylist, refresh-only) + OneUptime karşılaştırma tablosu (denylist anlık/Redis; kısa TTL+refresh 5-15 dk/Excellent; versiyon anlık/Very low storage) + c-sharpcorner "refresh-only revocation = önerilen varsayılan, access TTL'i kadar pencere" + waiting-for-dev denylist'in "en basit gerçek iptal" olması + Stack Exchange "stateless JWT iptal edilemez, saklama gerekir" → üç strateji **karşılıklı dışlayıcı değil, katmanlı**. (d) **Cross-subdomain + fixation:** SentinelOne MITRE referanslı **çapraz-alt-alan cookie fixation** (kardeş uygulamanın zayıflığı üstalan oturumunu sabitler) + OWASP WSTG-SESS-03 ("mevcut session id önce geçersiz kılınır, sonra doğrulamada yeni id verilir") + Symfony CVE-2022-24895 ("yalnız id yenilemek yetmez, oturum atribütleri taşınırsa CSRF token'ı da taşınır") + OWASP CSRF Cheat Sheet / lirantal / andrewlock / Stack Exchange 223473 / PortSwigger: **SameSite kısmi koruma, kardeş alt alanlar same-site sayılır → Origin+token+nonce gerekir.** |
| Web Search **Paragraf Veri Uzun** | CoreMusic'in sorunu "session mı JWT mi" değil, **kimin neyi taşıdığı ve ne zaman iptal edebileceği**: session zaten var ve canlı (ApiSessionManager köprüsü + logout + regenerate_id IMPLEMENTED), JWT ise sıfırda (paket 0, üretim 0, doğrulama stub) — yani literatürün önerdiği **ters orantılı başlangıç** burada doğru: varsayılan session, **ihtiyaç duyulan yerde** JWT. İkinci ders, iptalin **tek mekanizma olmadığını**: anlık iptal isteyen yer (şifre değişimi, şüpheli oturum, admin kilidi) denylist/versiyon ister; toleranslı yer (kısa access TTL) yalnız refresh'i reddederek "anında" olmasa da ≤15 dakikada kapanır — bu yüzden karar (d) **üçünü birden** ister ve hepsini tek bir veri modeline (grant + token ailesi + versiyon) bağlar. Üçüncü ders, rotasyonun **yanlış uygulanmasının güvenlik kazancını sıfırlaması**: ilişki saklanmazsa reuse yakalanamaz, aile iptali olmaz → saldırgan başka bir sibling ile devam eder (Ssojet/Obsidian). Dördüncü ders, cross-subdomain'de `Domain=.coremusic.net`'in **hem SSO ön koşulu hem ortak XSS yüzeyi** olması: kardeş alt alan aynı-site sayıldığı için SameSite onu korumaz (OWASP/Stack Exchange), üstelik paylaşımlı cookie fixation vektörü MITRE'de kayıtlıdır → bu yüzden karar (e) cookie'yi **geniş bırakır ama savunmayı Origin + csrf_token + nonce + kısa oturum** üzerine kurar ve ADR-043 E1 boş-Origin fail-open'ını bu ADR'nin **öncül riski** olarak devralır. Sonuç: literatür "tek yöntem" değil **katmanlı hibrit** dayatıyor; bu ADR o katmanları (a)–(e) olarak sabitler. |
| Web Search **Sonucu** | 4 sorgu / **25 adlandırılmış kaynak**: (1) **RFC 9700 — BCP 240, OAuth 2.0 Security Best Current Practice (Ocak 2025) — resmi, sayfa bizzat okundu** (§2.2.2, §4.14.1/4.14.2), (2) Auth0 — Refresh Token Rotation + Automatic Reuse Detection, (3) ObsidianSecurity — Refresh Token Security best practices (reuse detection + atomic DB + family tracking), (4) Ssojet — Refresh Tokens implementation guide (ilişki saklanmazsa alarm yok; aile geneli iptal; RFC 10017 "rotation tek başına yeterli değil"), (5) CerberAuth — rotation integration challenges (grace period; "active refresh" revoke kapsamı), (6) Paragon — OAuth refresh at scale (eşzamanlı refresh yarışı), (7) nhimg — OAuth refresh token rotation sözlük girişi, (8) SuperTokens — Revoking access with a JWT blacklist (7 yol), (9) OneUptime — How to Handle JWT Revocation (strateji karşılaştırma tablosu), (10) c-sharpcorner — Token Revocation + Session Tracking engine (refresh-only revocation önerisi), (11) waiting-for-dev — JWT revocation strategies (denylist), (12) Stack Exchange Security 266204 — stateless JWT neden iptal edilemez, (13) Stack Overflow 21978658 — Invalidating JSON Web Tokens, (14) r/Backend — "why JWT token should be short", (15) Authgear — Session vs token authentication (karar tablosu), (16) LoginRadius — JWT vs session-based authentication, (17) joepie91 — Stop using JWT for sessions, (18) businesscompassllc — Session-based auth vs JWT (2026), (19) Stack Overflow 43452896 — Authentication: JWT usage vs session, (20) SentinelOne — Session fixation (MITRE çapraz-alt-alan cookie fixation), (21) **OWASP WSTG-SESS-03 — session fixation prevention (SentinelOne üzerinden; resmi WSTG sayfası ⚠️ derin okuma yapılmadı)**, (22) Medium/@rramgattie — SameSite and Subdomains, (23) Stack Exchange Security 223473 — SameSite with subdomains (same-site tanımı), (24) lirantal — CORS, SameSite and CSRF: 3 dimensions + OWASP CSRF Prevention Cheat Sheet + andrewlock — Understanding SameSite (çapraz kaynak üçlü), (25) PortSwigger Web Security Academy — Bypassing SameSite restrictions |
| Web Search **Alınan Karar** | Karar a-e kalemleri bu bulgularla sabitlendi: (a) **Sınır:** tarayıcı/SPA (çerez taşıyan istemci) → **session**; API/mobil/servisler-arası (çerez taşımaz) → **JWT**; istisna yok, istisna ADR ile açılır (Authgear/LoginRadius ayrımı + ADR-011 hibrit zemini). (b) **Köprü:** API, session varsa onu okur (`ApiSessionManager` — IMPLEMENTED), session yoksa JWT ister; köprü **tek yönlüdür**, session üretmez; login akışı ADR-047 imzalı köprüsüyle, domain hizası ADR-043 ile (joepie91/LoginRadius: kontrol session'da). (c) **Ömür:** access **≤15 dk** (ADR-011 taslağıyla aynı) + rotasyonlu **tek kullanımlık** refresh; **ilişki saklanır**, eski refresh geçerliyken gelirse **aile iptali + yeniden yetkilendirme** (RFC 9700 §4.14.2 + Auth0 + Obsidian + Ssojet); eşzamanlı refresh yarışı grace-window ile yönetilir (CerberAuth/Paragon). (d) **İptal:** üç katman — ① session sonlandırma (mevcut, IMPLEMENTED), ② refresh reddi + **token versiyonu** (anlık, kullanıcı bazlı), ③ seçici **denylist** (şüpheli access için, TTL boyunca) → "üretimde iptal 0" bu ADR ile kapanır (OneUptime/SuperTokens/c-sharpcorner hibrit stratejisi). (e) **Cross-subdomain:** cookie `domain=.coremusic.net` **korunur** (ADR-043 8 nokta), savunma **Origin whitelist + csrf_token + nonce + kısa oturum** ile kurulur — SameSite kardeş alt alan için yeterli sayılmaz (OWASP/Stack Exchange/PortSwigger); boş-Origin fail-open (ADR-043 E1) bu ADR'nin **öncül riski**dir ve §5.1 kapı adımıdır; login/register/şifre değişiminde `session_regenerate_id` **zorunlu** (WSTG-SESS-03 + Symfony CVE-2022-24895 dersi). |
| Web Search **Sonuç** | Araştırma kararı **destekledi ve üç şart netleştirdi**: (1) **tek reçete yok** — "her yerde JWT" de "yalnız session" da tek başına yanlış; doğru kalıp **kontrol session'da, taşıma JWT'de** (hibrit); (2) **rotasyon + reuse + aile iptali bir pakettir** — ilişki saklanmadan rotasyon dekorasyondur (Ssojet/Obsidian) ve rotation tek başına yeterli değildir (RFC 10017 dipnotu — ⚠️ bu iddia tek kaynaklı, çapraz teyit PLANNED); (3) **iptal katmanlıdır** — denylist/versiyon/refresh-reddi üçü birlikte, anlık beklentisi yalnız versiyon+denylist karşılar. Ek iki doğrulama: `lcobucci/jwt` iddiası composer'da **kanıtsız** (⚠️ VERIFICATION REQUIRED) → JWT kütüphanesi seçimi §5.1 açık adımı olarak bırakıldı (bu ADR kütüphane **seçmez**); cross-subdomain fixation + boş-Origin fail-open **bugünden** mevcut risktir → §4.3 risk tablosuna girdi, debate şartına bağlandı. |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-011-session-management]] session = SSOT | Oturumun üretimi/timeout/rotasyonu/hijyeni bu ADR'nin **tekelinde değildir**; bu ADR yalnız **sınıra** ve JWT ayağına karar verir (SRP) |
| [[ADR-010-csrf-protection-strategy]] şart 3 | JWT stub'ı kapanana kadar **cookie-auth tek yol**; Bearer ile oturum açma bu kapı kapanmadan **açılmaz** |
| [[ADR-043-auth-subdomain-consolidation]] | Cookie domain tekliği, Origin/CSRF/nonce kuralları, imzalama anahtarı tek kaynak + rotasyon — bu ADR bu kararlara **çıkamaz** |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect köprüsü (imzalı, tek kullanımlık, TTL'li) — yeniden tanımlanmaz, bu ADR onu **kullanır** |
| [[ADR-020-api-public-security]] | API key (hash+scope+rotasyon), Bearer kilidi, rate limit, hata gizleme bu ADR'nin dışındadır; JWT uçları o ADR'nin uç güvenliğine tabidir |
| [[ADR-008-bypass-auth-middleware]] | `BypassAuthMiddleware` üretimde fail-closed; hibrit auth bypass **kapsamını genişletmez** |
| ADR-001–037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| In-Place Refactoring | Dosya adları onaysız **değiştirilmez**; `index.md` 051–060 satır boşluğu ve `ApiSessionManager.php:9` slug uyuşmazlığı bu işlemde **düzeltilmez** (reset'e erteli — report-only) |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` (log.md = append-only); PowerShell write cmdlet'leri yasak |
| REDACTED | Signing anahtarı, API key, connection string, parola, kullanıcı verisi bu ADR'ye **yazılmaz** (anahtar rotasyonu ADR-043'ün tekelindedir) |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz** (ADR-051, ADR-053–060 diskte YOK → düz metin); kanıtsız iddia `⚠️ VERIFICATION REQUIRED` |
| Kütüphane seçimi yok | JWT imzalama kütüphanesi (brain.md `lcobucci/jwt` iddiası) **bu ADR'de seçilmez** — composer'da paket yok, seçim ayrı işlem/adım (§5.1 adım 4) |

---

## §2 Karar (Decision)

CoreMusic kimlik doğrulamayı **hibrit** yürütür: **tarayıcı ve sunucu state'i session'da kalır, API/mobil/servisler-arası taşıma JWT ile yapılır**; ikisi arasında tek yönlü bir köprü vardır; JWT kısa ömürlüdür ve rotasyonlu refresh ile yenilenir; iptal sunucuda durur; cross-subdomain cookie davranışı ADR-043'e bağlıdır.

### §2.1 (a) Sınır — Ne Zaman Session, Ne Zaman JWT

| İstemci / kanal | Kimlik | Neden | Karar durumu |
|---|---|---|---|
| Tarayıcı (SPA + sunucu-render sayfalar) | **Session cookie** (`domain=.coremusic.net`, HttpOnly, SameSite=Lax, Secure) | Anlık iptal, CSRF katmanı, `HttpOnly` ile JS erişimi yok; ADR-011 zaten burada | **Kararlı** (mevcut davranış korunur) |
| API (`api.coremusic.net`, `/v1/*`) — **çerez taşıyan** istemci | **Session** (`ApiSessionManager` okur) | Tarayıcı aynı site'dir; köprü zaten IMPLEMENTED | **Kararlı** (§1.1-A) |
| API — **çerez taşımayan** istemci (mobil uygulama, CLI, üçüncü taraf, gömülü cihaz) | **JWT** (access ≤15 dk + rotasyonlu refresh) | Cookie yok → stateless taşınabilir kimlik; state sunucuda refresh ailesinde | **Karar (c)** |
| Servisler arası (PSR-14 event hariç HTTP) | **JWT (service claim)** + [[ADR-039-7-service-platform-architecture]] kısıtı | Kullanıcı cookie'si servis kimliği değildir (ADR-043 s2s) | **Kararlı — kapsam notu:** servis↔servis HTTP ADR-039'a bağlıdır, bu ADR onu **açmaz** |
| `BypassAuth` / test kimliği | **Ne session ne JWT** — gate [[ADR-008-bypass-auth-middleware]] | Üretimde fail-closed | **Kararlı** |
| Belirsiz / yeni istemci | **Session** (varsayılan) | Varsayılan-önce güvenli: iptal edilebilir olan taraf | **Karar (a) kuralı:** JWT için ADR gerekir |

**Tek cümlelik kural:** *Çerez taşıyan istemci session, çerez taşımayan istemci JWT kullanır; hangisinin geçerli olduğuna bu tablo, uyuşmazlıkta session karar verir.*

### §2.2 (b) Session ↔ JWT Köprüsü

| Kalem | Karar |
|---|---|
| **Yön** | Köprü **tek yönlüdür**: API, aktif session varsa onu okur/yazar (`ApiSessionManager` — `session_start()` çağırmaz, cookie üretmez); session **yoksa** JWT ister. API, session **üretemez** (üretim = login ucu, ADR-047 köprüsü) |
| **Bağımlılık** | `ISessionManager` sözleşmesi shared katmanında (`ApiSessionManager`) karşılanır — AuthenticationMiddleware'in bağımlılığı **IMPLEMENTED** |
| **Login akışı** | Giriş öncesi durum + hedef URL: ADR-047 imzalı, **tek kullanımlık, TTL'li** köprü token'ı; `HomeAuthBridge` yalnız kimlik taşır (kapsam genişletilmez) |
| **Domain hizası** | Köprü uçları ADR-043 Origin/CSRF/nonce kurallarına tabidir; **boş-Origin fail-open (E1) beklenti olarak yazılmaz** → §5.1 kapı adımı |
| **Yeniden oturum (re-auth)** | Session sona erdiyse / refresh ailesi iptal edildiyse → istemci login'e döner; köprü **çift kimlik penceresi açmaz** (ADR-047 felsefesi) |
| **CSRF** | Köprü ve login uçları ADR-010 katmanı + `csrf_token` altında; **Bearer uçları CSRF'den muaftır** (çerez yok → CSRF yüzeyi yok) — ADR-010 şart 3 ile aynı kapı |

### §2.3 (c) Token Ömrü + Yenileme (Access + Rotasyonlu Refresh)

| Kalem | Karar |
|---|---|
| **Access JWT** | **≤15 dk** TTL (`exp` zorunlu); `iss`/`aud` doğrulanır; algoritma **allowlist** (RFC 8725 — ADR-020 §1.3); imzalama anahtarı tek kaynak + rotasyon (ADR-043) |
| **Refresh** | **Tek kullanımlık**; her yenilemede **yeni** refresh üretilir, eskisi **ilişkisiyle birlikte saklanarak** iptal edilir |
| **Reuse yakalama** | Daha önce iptal edilmiş bir refresh tekrar gelirse = **ihlal**: **tüm aile iptal** + kullanıcı yeniden yetkilendirilir (RFC 9700 §4.14.2); sunucu hangi tarafın saldırgan olduğunu ayırt **edemez** — varsayılan kilit |
| **Yarış penceresi** | Eşzamanlı refresh (iki sekme/cihaz) için **kısa grace-window** (aynı yeni çift döner); pencere içinde ikinci refresh → yeni aile, eskisi iptal |
| **İlişki saklama** | Rotasyon **silerek değil saklayarak** yapılır: `grant_id` + `token_family_id` + `prev_hash` (eski refresh'in SHA-256'sı) — silinirse reuse **yakalanamaz** |
| **Access iptali** | Access stateless olduğundan **anında silinemez** → en geç **TTL (≤15 dk)** içinde düşer; anlık gerekirse §2.4 katman 2/3 |
| **Durum** | **PLANNED** (kodda JWT üretimi/doğrulama yok — §1.1-B); bu ADR **sözleşmedir**, uygulaması §5.1 |

### §2.4 (d) Logout / İptal — Sunucu Tarafı Reddetme

| Katman | Mekanizma | Ne zaman | Hız | Durum |
|---|---|---|---|---|
| **1** | **Session sonlandırma** (`destroy()` + cookie temizleme + `regenerate_id`) | Tarayıcı logout, şüpheli oturum | **Anlık** | **IMPLEMENTED** (§1.1-C) |
| **2** | **Token versiyonu** — kullanıcının `token_version`'ı artırılır; versiyonu düşük tüm access/refresh **reddedilir** | Toplu iptal (şifre değişimi, hesap kilidi, "tüm cihazlardan çıkış") | **Anlık** (ilk doğrulamada) | **PLANNED** (0 — §1.1-C) |
| **3** | **Refresh reddi** — refresh ailesi DB'den düşer | Logout (API/mobil), reuse tespiti | **Anlık refresh'te**; mevcut access **≤15 dk** yaşar | **PLANNED** |
| **4** | **Seçici denylist** — belirli `jti` reddetme listesinde (TTL = access kalan ömrü) | Tekil sızıntı şüphesi | Anlık | **PLANNED (ikincil — versiyon + refresh reddi yeterli değilse)** |
| **Yasak** | Denetimsiz "logout = 200 dön, token sunucuda geçerli kalsın" | — | — | **YASAK** (iptal deliği) |

**Kural:** *Logout, hem session'ı hem refresh ailesini düşürür; access token en geç TTL kadar yaşar — "anında her şeyi iptal" iddiası **yazılmaz** (yalnızca katman 2/4 ile kısmen sağlanır).*

### §2.5 (e) Cross-Subdomain Cookie Davranışı

| Kalem | Karar |
|---|---|
| **Cookie domain** | `domain=.coremusic.net` **korunur** (ADR-043: 8 nokta IMPLEMENTED; ADR-011 §2.2a-1) — SSO'nun ön koşulu |
| **Savunma katmanı** | SameSite **tek başına yeterli sayılmaz** (kardeş alt alanlar same-site'tır) → savunma **Origin whitelist (ADR-043) + csrf_token (ADR-010) + CSP nonce (ADR-012) + HttpOnly/Secure + kısa oturum** |
| **Boş-Origin** | ADR-043 E1 fail-open **bu ADR'nin öncül riskidir** → kapatma §5.1 kapı adımıdır; karar metni "fail-open kabul edilmez" der |
| **Fixation** | Login / register / **yetki değişiminde** `session_regenerate_id(true)` zorunlu (WSTG-SESS-03); yalnız id değiştirmek yetmez — oturum atribütleri (CSRF token dahil) taşınırsa **yeniden üretilir** (Symfony CVE-2022-24895 dersi) |
| **Çapraz-alan fixation** | Paylaşımlı üstalan cookie'si kardeş uygulamanın zayıflığından **oturum sabitleyebilir** (MITRE) → her alt alanın **kendi session kurması yasak**, doğrulama auth'a taşınır (ADR-043) |
| **Kapsam genişletme yok** | Bu ADR cookie parametrelerini **değiştirmez**; değiştirirse ayrı revizyon/gerekçe gerekir |

### §2.6 Neden Bu Seçenek?

Zemin tek yöne işaret ediyor: **session tarafı zaten IMPLEMENTED** (köprü 206 satır, logout, regenerate_id, cookie domain 8 nokta), **JWT tarafı tamamen boş** (paket 0, üretim 0, doğrulama stub `null`, iptal 0). İki gerçekliğin ortasında en küçük-tamamlayıcı karar hibrittir: session'ı **yok sayıp** sıfırdan stateless'a geçmek mevcut canlı yüzeyi (CSRF, logout, fixation koruması) devre dışı bırakırdı; JWT'yi **hiç tanımlamadan** bırakmak ise mobil/servis kapısını ADR-010 şart 3'e rağmen belirsiz bırakırdı. Karar, literatürün de önerdiği "**kontrol session'da, taşıma JWT'de**" kalıbıdır (Authgear/LoginRadius/joepie91) ve iptal sorununu **gizlemeyip** katmanlı olarak sahiplenir (§2.4). Rotasyon + reuse + aile iptali tek paket olarak yazılır çünkü relationship silinirse rotasyonun güvenlik kazancı sıfırlanır (RFC 9700 §4.14.2 + Ssojet/Obsidian).

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Her yerde JWT** (session tamamen kaldırılır) | Tek mekanizma, stateless ölçek | Anlık iptal yok (yalnız TTL); logout yüzeyi **0** olan bir sistemde "logout yaptım" hissi **yalan** olur; CSRF yüzeyi cookie'siz iyi ama çerezli SPA'da HttpOnly koruması kaybolur; mevcut 206 satır köprü + logout + fixation kodu çöp olur | §1.1'deki canlı session yüzeyi yok sayılır; literatür varsayılan olarak session'ı önerir (joepie91, Authgear); anlık iptal için denylist/versiyon kurmak gerekir — yani ek iş |
| 2 | **Her yerda session** (API/mobil de cookie tutsın) | Tek state, anlık iptal | Mobil/CLI/third-party istemcide cookie yok → taşınamaz; ADR-020 public API için Bearer kilidi anlamsızlaşır; cross-origin istemcilerde SameSite sorunları | API/mobil kanalı kapsam dışı kalır; ADR-020 + ADR-011 hibrit saklama kararıyla **çelişir** |
| 3 | **Uzun ömürlü JWT (7-30 gün), iptal yok** | Sıfır sunucu durumu, en basit | Çalınan token haftalarca geçerli; "üretimde iptal 0" sorunu **kalıcı** hâle gelir; RFC 8725/9700 kısa access + rotasyon gerekçesiyle doğrudan ters | İptal deliği (§1.2-4) bilinçli olarak açılır; OWASP A07/A02 riski |
| 4 | **Harici IdP / token servisi** (Auth0, Keycloak, managed) | Hazır rotasyon/revokasyon/MFA | Yeni bağımlılık + dışarıya kimlik verisi çıkışı; ADR-043 imzalama anahtarı "tek kaynak" kararına aykırı; CoreMusic tek instance ölçeğinde YAGNI | Mimari bağımlılık + veri egemenliği (ADR-040/043 ruhu); §4.4 fallback'te geri dönülebilir **olmayan** tek seçenek |
| 5 | **Denetimli denylist tek başına** (access uzun, her istekte Redis'e bak) | Anlık iptal, esnek | Her istekte ek Redis gecikmesi + state = "stateless" iddiası çöker; uzun access ile saldırı penceresi yine geniş | §2.3 kısa TTL + §2.4 katmanlı iptal **daha ucuz** ve aynı hızı sunar (OneUptime/SuperTokens karşılaştırması) |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Sınır tek cümlede biter:** çerez taşıyan → session, taşımayan → JWT; belirsiz istemci → session (varsayılan). Yeni uç açılırken karar aranmaz.
- **Mevcut kod yeniden kullanılır:** `ApiSessionManager` (IMPLEMENTED), logout üçlüsü ve `regenerate_id` noktaları **olduğu gibi** kalır — hibrit için yeni session altyapısı gerekmez.
- **İptal sorunu sahiplenilir:** "logout yaptım ama JWT yaşıyor" boşluğu §2.4'ün 4 katmanıyla kapanır; en kötü durumda bile erişim ≤15 dk.
- **Reuse = erken uyarı:** rotasyonlu refresh, çalınan refresh'i **ilk denemede** yakalar ve aileyi düşürür (RFC 9700) — sessiz kalıcılık engellenir.
- **Cross-subdomain bilinçli korunur:** SSO (cookie genişliği) kazanımı kaybedilmez; savunma SameSite yerine Origin+token+nonce'a taşınır (ADR-043 hizası).

### 4.2 Olumsuz Sonuçlar

- **İki kimlik modeli = iki bakım yüzeyi:** session zamanlaması (ADR-011 çelişkisi: idle 3600 / absolute 1800) ile JWT TTL ayrı izlenir; **çift state** riski (§4.3 risk 3).
- **JWT bugün yok:** karar kağıtta tam, kod 0 (paket/üretim/doğrulama = 0) → "hibrit auth var" **denemez**; §5.1 adımları kapanana kadar **PLANNED** kalır.
- **İptal ≤15 dk gecikmeli:** katman 3/4 olmadan logout sonrası mevcut access yaşar — bu **kabul edilmiş** bir açıktır, gizlenmez.
- **Köprü tek yönlüdür:** API'de session başlatılamaz; "API'de login" uçları ayrı tasarlanmak zorunda (ADR-047 kapsamı) — karmaşıklık kalır.
- **OriginCheck E1 (boş-Origin fail-open) açık:** hibrit auth bu durumda **çalışır ama zayıf** — bu ADR onu kapatamaz (kod değişikliği ayrı adım), yalnız **risk + kapı** yazar.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **İptal deliği:** JWT üretilmeye başlar ama katman 2-4 kurulmaz → logout yüzeyi 0 kalır | 4 (Çok olası — bugün zaten 0) | Yüksek (güvenlik) | §5.1 adım 5 **kapı adımı**: katman 2/3 kurulmadan "JWT ile giriş açık" sayılmaz; §2.4 yasağı |
| 2 **Refresh reuse atlanır:** ilişki silinir, aile iptali yapılmaz → saldırgan sibling ile devam eder | 3 (Olası) | Yüksek (kalıcı yetki) | §2.3 `prev_hash` + `token_family_id` şartı; §5.1 adım 6 testi (reuse → aile iptali) |
| 3 **Çift state tutarsızlığı:** session iptal edilir, refresh ailesi unutulur (veya tersi) | 3 (Olası) | Orta-Yüksek (yarım logout) | §2.4: logout **tek ucu** hem katman 1 hem katman 3'ü çağırır; test: logout sonrası ikisi de ölü |
| 4 **Köprü replay / çift kimlik:** ADR-047 köprü token'ı + JWT taşınan durum | 2 (Mümkün — tek kullanımlık + TTL 300 sn) | Orta | ADR-047 tek kullanımlık + imza; bu ADR köprüye **yeni durum eklemez** |
| 5 **Cross-subdomain zayıflığı:** boş-Origin fail-open (E1) + paylaşımlı cookie fixation | 3 (Olası — E1 açık) | Yüksek (oturum sabitleme / origin bypass) | §5.1 adım 3 kapı: E1 kapatılmadan hibrit auth **üretimde açılmaz**; fixation → `regenerate_id` zorunlu (§2.5) |
| 6 **Kütüphane/anahtar belirsizliği:** composer'da JWT paketi yok; `brain.md` `lcobucci/jwt` iddiası kanıtsız | 4 (Çok olası — zaten 0) | Orta (teslim gecikmesi) | §5.1 adım 4: kütüphane seçimi ayrı onay + composer kanıtı; bu ADR **seçim yapmaz** (⚠️ VERIFICATION REQUIRED) |
| 7 **Vault drift:** index.md 051–060 satır yok; ADR-051 kaynaksız boşluk; `ApiSessionManager.php:9` slug uyuşmaz | 4 (Çok olası — tespit edildi) | Düşük (katalog kirliliği) | Report-only (§5.1 adım 7) — bu işlemde düzeltilmedi; bir sonraki vault reset'inde gözden geçirilir |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **JWT hiç kurulmazsa:** sistem bugünkü (session-tek) durumunda kalır — davranışı **değişmez**, çünkü JWT zaten 0'dır (§1.1-B); API/mobil kapısı ADR-010 şart 3 gereği **kapalı** kalır.
2. **Katman 2/3 kurulamazsa:** "üretimde JWT ile giriş" **açılmaz** (§4.3 risk 1); yalnız session yolu canlı kalır — bu, varsayılan güvenli geri dönüşdür.
3. **Rotasyon geri alınırsa:** refresh ömrü kısaltılır + aile iptali manuel tetiklenir; güvenlik kaybı **açıkça** log'a yazılır (sessiz geri dönüş yasak).
4. **E1 kapatılamazsa:** hibrit auth production'da **pasif** kalır (yalnız development'ta açık) — fail-open bilerek yayılmaz.
5. **Debate reddederse:** karar Draft/Review'a döner; (a)–(e) maddeleri uygulanmaz sayılır, dosya adı **değişmez** (In-Place Refactoring).
6. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; frozen ADR'ler (001–037) etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Kanıt envanteri teyidi:** §1.1-A/B/C/D tabloları (ApiSessionManager 206 satır · jwt paketi 0 · validateJwtToken stub · logout 3 IMPLEMENTED · token iptali 0 · pipeline 10/10) | Vault Steward | 0.25 gün ✅ (2026-09-29 taraması yapıldı) |
| 2 | **Sınır tablosu (§2.1) route'lara işlenir:** her `/v1/*` ucu "session / JWT / public" olarak işaretlenir; belirsiz uca **session** atanır (varsayılan-önce-güvenli) | Backend Architect + Security Engineer | 1 gün |
| 3 | **Boş-Origin fail-open (ADR-043 E1) kapatılır:** `shared/src/Middleware/OriginCheckMiddleware.php:36-41` boş `HTTP_ORIGIN` → whitelist'e bakılır (non-GET isteklerde **REJECT**); `/validate-key` muafiyeti (E4) yeniden değerlendirilir | Security Engineer | 1 gün (**kapı** — §4.3 risk 5) |
| 4 | **JWT kütüphanesi + anahtar:** composer'a JWT paketi eklenir (`brain.md` `lcobucci/jwt` iddiası **⚠️ VERIFICATION REQUIRED** — composer kanıtı olmadan kabul edilmez, alternatif de reddedilebilir); imzalama anahtarı ADR-043 tek kaynak + rotasyon | Security Engineer + Backend Architect | 1 gün |
| 5 | **İptal katmanları 2–3 (+4):** `token_version` kolonu + refresh ailesi tablosu (`grant_id`, `token_family_id`, `prev_hash`, `used_at`) + logout'un **tek uçta** hem session hem aile iptali | Data Engineer + Security Engineer | 2 gün (**kapı** — §4.3 risk 1) |
| 6 | **Rotasyon + reuse testleri:** (i) aynı refresh ikinci kez → **aile iptali**, (ii) eşzamanlı refresh → grace-window, (iii) logout sonrası hem session hem refresh ölü, (iv) access TTL ≤15 dk sınırı, (v) boş-Origin POST → reddi | QA Engineer | 1.5 gün (**kapı** — §4.3 risk 2) |
| 7 | **Ertelemeler (report-only — ADR-051 + index boşluğu):** (i) `index.md` **ADR-051–060 satırları YOK** (050 → 061 atlıyor) — bu işlemde satır **eklenmedi**, reset'te 052 satırı + 051/053-060 aralığının kararı sorulur; (ii) **ADR-051 kaynaksız boşluk olarak atlandı** (dosya/index/metin YOK) — numaralandırma **dokunulmaz**, kapatma ayrı karar ister; (iii) `ApiSessionManager.php:9` `@see ADR-052-hybrid-auth` slug uyuşmazlığı → kod dosyası Backend domaininde, onaysız değişiklik yasak | Vault Steward + Tech Lead | 0.2 gün |
| 8 | **Debate 3 tur** → §7 Debate/Tech Lead satırları (⏳ → ✅) — **tamamlandı** (3 tur / 20 persona, 18 kabul / 2 çekimser / 0 red → KABUL; §7.1) | Vault Steward | 0.5 gün ✅ (2026-09-29) |

**Toplam ≈ 7.45 gün** (adım 3 + 5 + 6 kapı — bu üçü olmadan "hibrit auth üretimde" **denemez**; adım 8 debate'i **tamamladı** — §7.1).

### §5.2 Geri Dönüş Planı

1. **Adım 2 tersi:** route işaretleri kaldırılır → ucar yeniden belirsizleşir; mevcut davranış **değişmez** (bugün de session öncelikli).
2. **Adım 3 tersi:** E1 düzeltmesi tek bayrakla kapatılabilir → boş-Origin fail-open'a **dönülür** (güvenlik gerilemesi bilerek ve `log.md`'ye yazılarak).
3. **Adım 4 tersi:** composer paketi kaldırılır + `validateJwtToken` stub'a döner → Bearer yolu yeniden **ölü** (ADR-010 şart 3 otomatik geçerli) — davranış **bugünkü** hâline birebir döner.
4. **Adım 5 tersi:** `token_version` + refresh ailesi tabloları düşürülür; session logout (katman 1) **dokunulmaz** — geri dönüş tek yönü: iptal hızı "anlık"tan "≤15 dk"a iner.
5. **Adım 6 geri alınamaz:** testler yalnız denetim içerir; kalıcı silme yasak (kanıt kaybı).
6. **Adım 7 geri alma yoktur:** ertelenen işler henüz yapılmadı (index.md'ye, ADR-051'e, kod dosyasına dokunulmadı).
7. **Adım 8 debate reddederse:** karar Draft'a döner, adımlar 2–6 durdurulur (§4.4-5); dosya adı değişmez.
8. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; frozen ADR'ler (001–037) etkilenmez.

### §5.3 Debate Şartları (Kabul Koşulu — taslak; bağlayıcı sürüm §5.4)

| Şart | İçerik | Bağlantı |
|---|---|---|
| **1** | **İptal kapısı:** `token_version` + refresh ailesi (katman 2/3) kurulmadan JWT ile giriş **üretimde açılmaz**; logout tek ucu hem session hem aileyi düşürür | §2.4 · §5.1 adım 5 |
| **2** | **Reuse kapısı:** iptal edilmiş refresh tekrar gelirse **aile iptali** test ile kanıtlanır; ilişki (`prev_hash`) silinerek "sahte rotasyon" yapılamaz | §2.3 · §5.1 adım 6 |
| **3** | **Origin/fixation kapısı:** boş-Origin fail-open kapatılır + login/register/şifre değişiminde `session_regenerate_id` zorunluluğu test edilir | §2.5 · §5.1 adım 3 |

> **Kabul koşulu:** 3 şart kapanmadan hibrit auth **üretimde yayına alınmaz**; debate **✅ TAMAMLANDI** — 3 tur / 20 persona, **18/2/0 KABUL** (§7.1); bağlayıcı sürüm aşağıdaki §5.4.

### §5.4 Debate Şartları (bağlayıcı — 3/20 debate KABUL, 2026-09-29)

Debate 3. turunda **18 kabul / 2 çekimser / 0 red** ile KABUL edilen 3 şart; §5.3 taslak maddeleriyle **çelişmez, onları bağlayıcı hâle getirir**.

| Şart | İçerik (Tur 2 itiraz → çözüm) | Durum | Bağlantı |
|---|---|---|---|
| **1** | **JWT PLANNED + iptal fazı** — (1a) JWT fazı yalnız **PLANNED** etiketiyle yürütülür; `lcobucci/jwt` composer **kanıt kapısı** (kod kanıtı olmadan paket kabul edilmez) kapanmadan JWT üretime açılmaz, (1b) **4 katmanlı iptal** (denylist · `token_version` · kısa TTL · refresh ailesi) **uygulama fazı** olarak zorunlu — iptal yüzeyi 0 iken JWT ile giriş kapalı kalır | 🔒 Bağlayıcı | §2.3 · §2.4 · §5.1 adım 4 + adım 5 · §5.3 şart 1 |
| **2** | **Origin/CSRF ön koşulu ([[ADR-043-auth-subdomain-consolidation]])** — `OriginCheckMiddleware` boş-Origin **fail-open** (E1, `:36-41`) **kapatılmadan köprü genişletilmez**: fail-open kapalı değilse köprüye yeni kimlik yolu eklenmez | 🔒 Bağlayıcı | §2.5 · §5.1 adım 3 · [[ADR-043-auth-subdomain-consolidation]] |
| **3** | **Reuse + logout-sonra-red testi** — (i) iptal edilmiş refresh ikinci kez gelirse **aile iptali** testi, (ii) **logout sonrası** token reddi testi (hem session hem refresh ölü) yazılmadan yayına alınmaz | 🔒 Bağlayıcı | §2.3 · §2.4 · §5.1 adım 6 · §5.3 şart 2-3 |

> **Kabul koşulu (bağlayıcı):** bu 3 şart kapanmadan hibrit auth **üretimde yayına alınmaz**; debate **✅ TAMAMLANDI** (3 tur / 20 persona — 18/2/0 KABUL, §7.1).

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../raw/AGENTS.md]] | Agent registry — §6 keyword routing (auth/session = Security Engineer), §25.3 frozen/append kuralları |
| [[../../raw/WORKFLOW.md]] | Süreçler, fazlar |
| [[../../raw/brain.md]] | Mimari karar özeti (`lcobucci/jwt` satırı :74/:109 → ⚠️ VERIFICATION REQUIRED) |
| [[../../index.md]] | Master katalog |
| [[../../log.md]] | Audit trail (append-only — bu ADR için tek satır append) |
| [[../../raw/keys.md]] | Keyword haritası (hybrid auth, JWT, session) |
| [[../../raw/glossary.md]] | Terimler (session, JWT, refresh rotation, reuse detection, token family, denylist) |
| [[../index.md]] | Karar dizini — **ADR-051–060 satırları YOK (050 → 061 atlıyor); bu ADR'nin satırı eklenmedi → reset'e ertelendi (§5.1 adım 7)** |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[ADR-011-session-management]] | §2.1/§2.4 zemini: OWASP seti + hibrit saklama (session = SSOT) |
| [[ADR-010-csrf-protection-strategy]] | §2.2: şart 3 — JWT stub kapanana kadar cookie-auth tek yol; Bearer CSRF muafiyeti |
| [[ADR-013-rate-limiting-apcu]] | §2.2/§5.1: login/refresh/köprü uçlarında rate limit |
| [[ADR-020-api-public-security]] | §1.1-B stub bulgusu + Bearer kilidi + API uç güvenliği |
| [[ADR-043-auth-subdomain-consolidation]] | §2.5: cookie domain tekliği (8 nokta) + Origin/CSRF/nonce + **E1 fail-open riski** |
| [[ADR-047-login-redirect-session-bridge]] | §2.2: imzalı tek kullanımlık köprü token'ı — yeniden tanımlanmaz |
| [[ADR-008-bypass-auth-middleware]] | §2.1: bypass ≠ kimlik; üretimde fail-closed |
| [[ADR-004-multi-domain-spa]] | §2.5: `domain=.coremusic.net` cookie haritasının kaynağı |
| [[ADR-039-7-service-platform-architecture]] | §2.1 servis-sınırı **düz metin atıf** (bu ADR servis↔servis HTTP'yi açmaz) |
| `shared/src/Api/Auth/ApiSessionManager.php` (206 satır) | §1.1-A köprü kanıtı — **kod kanıtı (düz metin, wiki-link değil)** |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php:111-131` · `shared/src/PageRouter/PageRouterKernel.php:285-296` | JWT stub + pipeline 10/10 — **kod kanıtı (düz metin)** |
| `auth.coremusic.net/include/Service/AuthService.php:200-204` · `include/Controller/AuthController.php:215` · `pages/logout.php:79` · `include/Middleware/SessionMiddleware.php:88` | Logout yüzeyi IMPLEMENTED — **kod kanıtı (düz metin)** |
| ADR-051 · ADR-053–ADR-060 | **Diskte YOK** (dosya + index satırı + metin yok) → **düz metin, wiki-link KURULMAZ**; ADR-051 kaynaksız boşluk (§5.1 adım 7) · **⚠️ VERIFICATION REQUIRED** (053-060 numaralarının kaderi reset'te sorulur) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü — **düz metin** (vault dışı, diskte VAR ✅) |

> **Wiki-link doğrulaması:** Yazımdan önce `Test-Path` ile diskte doğrulandı (2026-09-29). Diskte **olmayan** hedefe wiki-link **yazılmadı** — ADR-051/053-060 düz metin + `⚠️ VERIFICATION REQUIRED`; doğrulanamayan iddialar (`lcobucci/jwt` composer iddiası, RFC 10017 tek-kaynak iddiası, OWASP WSTG-SESS-03 derin okuması) işaretlidir. `ADR-052-hybrid-auth-session-jwt` hedefi bu dosyanın kendisidir.

> **Debate şartı bağları (§5.4 — 3/20 debate KABUL, 2026-09-29):** Şart 1a-1b → `shared/src/Api/Middleware/AuthenticationMiddleware.php` (JWT stub) + §5.1 adım 4 (`lcobucci/jwt` kanıt kapısı) / adım 5 (4 katmanlı iptal) · Şart 2 → [[ADR-043-auth-subdomain-consolidation]] (Origin/CSRF ön koşulu; E1 fail-open) + §5.1 adım 3 · Şart 3 → §5.1 adım 6 (refresh reuse + logout-sonra-red testi) · Debate kaydı: §7.1.

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → KABUL (2026-09-29).** Bağlayıcı 3 şart §5.4'te; taslak sürüm §5.3.

**Tur 1 — 20 persona kanıt taraması (bulgu):** `ApiSessionManager.php` **IMPLEMENTED** 206 satır (`:24` class · `:62` destroy · `:72` regenerateId · `:148` isIdleExpired hep `false`) · JWT **0/0/STUB** (3 composer.json `jwt|lcobucci` 0 isabet · `validateJwtToken()` fn `:117` → `return null` `:130` · RS256 yalnız yorum `:121` · Bearer çağrısı `:75`) · logout **session VAR** (`AuthService.php:200`, `AuthController.php:215`, `SessionMiddleware.php:88`, `logout.php:79`) ama **token iptali 0 yüzey** (`token_blacklist|revoked_token|token_version|denylist` = 0) · pipeline **10/10** doğrulandı (`PageRouterKernel.php:285-296`, `opencode.json:57,79` frozen sıra) · `OriginCheckMiddleware.php:36-41` **fail-open açık** · `AuthenticationMiddleware` satır alıntıları **5 yer düzeltildi** (`:106-120→:111-131`, `:110→:121`) · 25 kaynak / 4 sorgu (**RFC 9700 bizzat okundu**) · `index.md` 051-060 satırı YOK (reset'e erteli) · ADR-051 kaynaksız boşluk §5.1/§7.1'de. Oy: **16 kabul/neutral + 4 uyarı** (DevOps: JWT 0 paket şartı; QA: reuse/iptal testi; Critic: iptal 0 şartı).

**Tur 2 — İtiraz → çözüm (4 itiraz → 3 şart):**

1. JWT 0 paket/STUB → JWT fazı **PLANNED** etiketi + **lcobucci kanıt kapısı** → **şart 1a**
2. İptal yüzeyi 0 → **4 katmanlı iptal** (denylist / versiyon / TTL / aile) uygulama fazı → **şart 1b**
3. `OriginCheck` fail-open köprüyü etkiliyor → **ADR-043 şartına** bağlanır (fail-open kapalı değilse köprü genişletilmez) → **şart 2**
4. Reuse/iptal testi yok → **refresh reuse + logout-sonra-red testi** → **şart 3**

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL.**

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | 20 persona kanıt taraması (bulgu listesi yukarıda) | **16 kabul/neutral · 4 uyarı** → Tur 2'ye taşındı |
| 2 | İtiraz → çözüm (4 madde) | **Şart 1a-1b** (JWT PLANNED + 4 katmanlı iptal fazı) · **Şart 2** (Origin/CSRF ön koşulu — ADR-043) · **Şart 3** (refresh reuse + logout-sonra-red testi) |
| 3 | Oy (20 persona) | **18 kabul / 2 çekimser / 0 red → KABUL** |

- **Şartlar (bağlayıcı):** §5.4 — (1) JWT PLANNED + iptal fazı (1a/1b), (2) Origin/CSRF ön koşulu (ADR-043), (3) reuse + logout-sonra-red testi.
- **Tech Lead:** ✅ · **Arch Lead:** ⏳ · **Durum:** `accepted` (debate ✅ TAMAMLANDI; Arch Lead onayı §7'de beklenir — **Active/Frozen'a geçiş YOK**).
- **ADR-051 notu (tekrar):** ADR-051 numarası **kaynaksız boşluk** olarak atlanmıştır — bu ADR o numarayı **kullanmaz** ve boşluğu **doldurmaz**; 051'ın kaderi (yazılacak mı / numara düşürülecek mi) ayrı bir Vault Steward + Tech Lead kararıdır (§5.1 adım 7). Ayrıca `index.md` 051–060 aralığını **hiç içermiyor** (050 → 061) — bu ADR'nin dizin satırı **eklenmedi** (reset'e erteli).
- **Wiki-link teyidi:** bu bölümde **yeni wiki-link yok** (yalnız §6 tablosu); bağlantılar yazımdan sonra yeniden doğrulandı (report-only — index.md'ye dokunulmadı).

---

*1.0.0 | 2026-09-29 | Created*
*Authority: SSOT — CoreMusic Hybrid Auth Session + JWT (ADR-052)*
*Mode: Red Team · Human Mode · Truth Mode*
