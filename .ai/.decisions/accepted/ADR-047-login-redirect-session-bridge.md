---
title: "CoreMusic — ADR-047: Login Redirect & Session Bridge (returnTo hedef URL saklama · tek kullanımlık imzalı TTL köprü token'ı · cross-subdomain köprü ADR-043 hizası · open-redirect path-only whitelist · CSRF/redirect · logout/expire'te köprü iptali)"
type: "architecture-decision"
category: "security"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic login redirect & session bridge: (a) returnTo hedef URL'si giriş öncesi saklanır (kaynak + imzalı), (b) tek kullanımlık, imzalı, TTL'li köprü token'ı eski oturum verisini + hedef URL'yi taşır (query'de düz veri yok), (c) cross-subdomain köprü ADR-043 ile hizalı, (d) open-redirect savunması path-only whitelist (host kontrolü, //, /\\, kodlanmış bypass testleri), (e) CSRF/redirect ADR-010 (rate limit ADR-013), (f) logout/expire eski köprüyü geçersiz kılar"
kaynak: "Kullanıcı onaylı kapsam a-f + disk/kod kanıtı taraması (2026-09-29: ReturnUrlPolicy.php:7-184 + 2 test dosyası → IMPLEMENTED whitelist; AuthRouteConfig.php:64-65 ?return= üretici IMPLEMENTED / tüketici 0 hit; HomeAuthBridge.php AUTH_KEY_TTL=300 yalnız kimlik; UserRepository.php:219-260 auth_key SHA-256 + 30 sn replay penceresi; _pending_redirect_uri 0 çağrı; session_regenerate_id(true) 9 nokta; ADR-043 OriginCheck boş-Origin fail-open; auth-local OriginCheckMiddleware ölü kod) + web araştırması (6 sorgu / ≈25 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-047: Login Redirect & Session Bridge (Giriş Yönlendirme ve Oturum Köprüsü)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-f) · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona, 18/2/0 KABUL) · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-047-login-redirect-session-bridge`
> **İlgili kararlar:** [[ADR-043-auth-subdomain-consolidation]] (cross-subdomain oturum/köprü hizası — doğrudan öncül) · [[ADR-011-session-management]] (oturum yaşam döngüsü, cookie politikası, `session_regenerate_id`) · [[ADR-010-csrf-protection-strategy]] (CSRF — karar (e)) · [[ADR-013-rate-limiting-apcu]] (rate limit — karar (e)'de token tüketimine uygulanır) · [[ADR-009-clean-url-redirect]] (temiz URL/redirect doktrini) · [[ADR-020-api-public-security]] (API güvenlik katmanı) · [[ADR-005-ultrathink-protocol]] (zero hallucination — §1.1 dürüst etiket) · [[ADR-046-cross-view-state-preservation]] (returnTo/durum taşımada URL-öncelikli hizalanma) · [[../index.md]] · [[../../brain.md]]
> **Ad gerekçesi:** slug `ADR-047-login-redirect-session-bridge` **arşiv kanıtından** alınmıştır — `.ai/archives/prompt2-auth-2026-08-15.md:36` bu adı taşır; ancak oradaki yol `.ai/decisions/accepted/...` (**nokta eksik** — gerçek dizin `.ai/.decisions/`) ve o noktada dosya **yoktu**. Bu dosya o boşluğu doldurur; arşiv satırına wiki-link **yazılmaz** (yanlış yol), düz metin + düzeltme notu kullanılır.
> **⚠️ Düzeltme (prompt ↔ disk):** Üst görevde "ADR-013 (CSRF)" denmişti; **diskte CSRF `ADR-010-csrf-protection-strategy`'dir, `ADR-013-rate-limiting-apcu` rate limiting'dir.** Bu ADR karar (e)'yi **ADR-010 (CSRF)** üzerinden kurar; ADR-013 yalnız token tüketimi rate limit'i olarak anılır.
> **Index durumu:** `.ai/.decisions/index.md`'de **ADR-047 satırı YOKTUR** (ADR-046→048 boşluğu kasıtlıdır); satır ekleme **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı).
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt uyuşmuyor · **KAPSAM DIŞI** = ilgili alanda kod yok (hiçbiri yumuşatılmadı).

#### A) Open-redirect koruması (whitelist) — IMPLEMENTED

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Security/ReturnUrlPolicy.php:7-15` | `ALLOWED_HOSTS` host whitelist'i (izinli host listesi) | **IMPLEMENTED** |
| `ReturnUrlPolicy.php:39` | `isAllowed()` içinde kontrol karakteri reddi | **IMPLEMENTED** |
| `ReturnUrlPolicy.php:51,102-112` | Protokol-göreli `//` ve `/\` reddi — tek katman decode dahil | **IMPLEMENTED** |
| `ReturnUrlPolicy.php:76-86` | Ham vs decode host eşitliği + userinfo (`user@host`) reddi | **IMPLEMENTED** |
| `ReturnUrlPolicy.php:127-184` | `getSafeUrl()`: path-only `/...` dönüşü + scheme/userinfo/suffix kontrolleri | **IMPLEMENTED** |
| `shared/tests/Unit/Security/ReturnUrlPolicyTest.php` · `shared/tests/Security/ReturnUrlPolicyEdgeCaseTest.php` | Bypass vakaları (`//evil.com`, `/%2Fevil.com`, `/%09/`, `%0A`, userinfo, `%23`, `%40`) **reddediliyor** test edilmiş; `/home?page=2` gibi query'li path'ler izinli (`EdgeCaseTest.php:136`) | **IMPLEMENTED** (test) |

#### B) Redirect yüzeyleri — doğrulanmış ve doğrulanmamış ayrımı

| Kanıt | İçerik | Etiket |
|---|---|---|
| `auth.coremusic.net/index.php:103,117,124,135,149` | Giriş/çıkış akışında `Location` hedefleri; **103 ve 124'te `$redirectUrl` değişkenli** (değişken hedef yüzeyi) | **IMPLEMENTED** (yüzey) |
| `AutoRedirectHandler.php:65-80` | `redirect_uri` doğrulaması sonrası `auth_key` iliştirme | **IMPLEMENTED** (doğrulamalı) |
| `AuthController.php:49,256` | `resolveRedirectUrl` → `SecurityHelper::isRedirectUriSafe` zinciri | **IMPLEMENTED** (doğrulamalı) |
| `shared/src/PageRouter/ResponseEmitter.php:83` | `header("Location: $location")` — **içinde doğrulama yok**; sorumluluk çağıran tarafta | **IMPLEMENTED** (nokta) / **RİSK** (çağıran bağımlı) |
| `AuthUrlBuilder` · `AuthGuard.php:31` | `redirectAuth('login', '/'.$uri)` — korumalı URI'den login'e yönlendirme | **IMPLEMENTED** |
| `home.coremusic.net/config/bootstrap.php:56,62,81,89` | Sabit kodlanmış `/home`, `/login` hedefleri | **IMPLEMENTED** (sabit) |

#### C) returnTo (hedef URL saklama) — üretici var, tüketici YOK

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Config/AuthRouteConfig.php:64-65` | `?return=` parametresini (urlencoded) **üreten** tek nokta | **IMPLEMENTED** (üretici) |
| `$_GET['return']` / `'return'` okuyucu | Kod tabanında **0 isabet** — hiçbir yerde `return` okunup yönlendirme yapılmıyor | **PLANNED** (tüketicisi yok) |
| `home.coremusic.net/config/bootstrap.php:40-91` | `/auth/callback` akışının hepsi `header('Location: /home')` ile biter — hedef URL **her zaman yutulur** | **ÇELİŞKİ** (üretilen `?return=` yok sayılıyor) |

#### D) `_pending_redirect_uri` — yazım tanımlı, okuma tek, çağrı 0 (ölü yüzey)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `auth.../SessionManager.php:104` · `HomeSessionManager.php:102` (+arayüz) | `setPendingRedirect` tanımları | **IMPLEMENTED** (tanım) / **PLANNED** (çağrı 0) |
| `AuthController.php:41` | `consumePendingRedirect` okuması | **IMPLEMENTED** (okuma) — ama yazım hiç çağrılmadığı için hep boş |
| `setPendingRedirect` çağıran kod | **0 isabet** → mekanizma bugün **ölü** (tek yazıcı eksik) | **ÇELİŞKİ** |

#### E) Mevcut köprü (auth → home) — yalnız kimlik, hedef YOK

| Kanıt | İçerik | Etiket |
|---|---|---|
| `home.coremusic.net/include/Auth/HomeAuthBridge.php` | Cross-domain köprü: `AUTH_KEY_TTL = 300` sn, retry'lı; **yalnız kullanıcı kimliğini** taşır — hedef URL ve giriş-öncesi oturum verisi **yok** | **IMPLEMENTED** (kimlik) / **PLANNED** (hedef + oturum verisi köprüsü) |
| `shared/src/Config/AuthRouteConfig.php` + `AutoRedirectHandler` | `auth_key` akışının yönlendirme tarafı | **IMPLEMENTED** |

#### F) auth_key — imza/depolanma + 30 sn replay penceresi

| Kanıt | İçerik | Etiket |
|---|---|---|
| `auth.../UserRepository.php:219-260` | `auth_key` **SHA-256 hash** ile saklanır, `expires_at` + `markAuthKeyUsed` (tek kullanım) | **IMPLEMENTED** |
| `UserRepository.php` `findValidAuthKey($key, true)` → `AuthService.php:287-307` | Kullanılmış anahtar, `used_at > DATE_SUB(NOW(), INTERVAL 30 SECOND)` ise **30 saniye daha geçerli sayılır** (retry toleransı) → sınırlı **replay penceresi** | **IMPLEMENTED** (tolerans) / **RİSK** (köprü token'ına taşınırsa tehlike artar) |

#### G) Oturum yaşam döngüsü — yenileme + çıkışta yok etme IMPLEMENTED

| Kanıt | İçerik | Etiket |
|---|---|---|
| `session_regenerate_id(true)` | 9 nokta: `SessionMiddleware.php:40` · `SessionManager.php:76,149` · `SessionBootstrapper.php:58` · `SessionLifecycle.php:60` · `SessionInitializer.php:54,76` · `HomeSessionManager.php:74,147` · `AuthController.php:144,193` — fixation savunması | **IMPLEMENTED** |
| `auth.../SessionMiddleware.php:80-90` | Çıkışta `$_SESSION = []` + cookie temizliği + `session_destroy()` → **eski oturum (ve ona bağlı her durum) sunucu tarafında yok olur** | **IMPLEMENTED** |
| Cookie politikası (ADR-011 yeniden doğrulama) | `domain=.coremusic.net`, `HttpOnly`, `SameSite=Lax`, idle+absolute timeout, rotation — **hâlâ geçerli** | **IMPLEMENTED** |

#### H) ADR-043 yeniden doğrulama (köprü hizası) + yeni bulgu

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Middleware/OriginCheckMiddleware.php:36-41` | **Boş-Origin fail-open**: `Origin` başlığı yoksa istek geçer | **ÇELİŞKİ** (ADR-043'ün bilinen açığı — bu ADR'de köprü talepleri için şart koşulur) |
| `OriginCheckMiddleware.php:68` | Suffix-match (alt-alan adı eşiği) | **IMPLEMENTED** |
| `auth.coremusic.net/config/cors.php:11,15-18` | CORS allowlist `$_ENV['CORS_ALLOWED_ORIGINS']`'ten, **varsayılan boş** | **IMPLEMENTED** (yapılandırma) |
| `auth.../include/Middleware/OriginCheckMiddleware.php:66` | `self::ALLOWED_ORIGINS` **o sınıf içinde tanımsız** → anlık çağrıda fatal olurdu; sınıf **0 kez örnekleniyor** (auth, shared pipeline'ı kullanıyor: `PageRouterKernel::buildDefaultMiddlewares():275-286`, sıra OriginCheck→Cors→RateLimiter→SecurityHeaders→Session→CSRF→BypassAuth→Auth→Permission→Validation) | **ÇELİŞKİ** (ölü kod + latent fatal) |
| `shared/src/Middleware/CsrfMiddleware.php` (`PageRouterKernel.php:281`) | Login/logout POST'ları `X-CSRF-Token` ile korunuyor | **IMPLEMENTED** (ADR-010) |

#### I) Vault kayıtları (bu dosyadan önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/.decisions/index.md` | ADR-047 satırı **yok** (ADR-046→048 boşluğu kasıtlı) | **ÇELİŞKİ** (bu dosya doldurur; satır ekleme reset'e ertelendi) |
| `.ai/archives/prompt2-auth-2026-08-15.md:36` | `ADR-047-login-redirect-session-bridge` adı düz metin; yol `.ai/decisions/...` **nokta eksik**, dosya o an YOK | DOĞRULANDI (bu dosya ile doldu — arşive dokunulmadı) |
| Üst görev yönergesi | "ADR-013 (CSRF)" | **ÇELİŞKİ** → disk: CSRF = **ADR-010**, rate limit = ADR-013 (§1.1-J düzeltmesi) |

> **Bulgu özeti:** Open-redirect whitelist'i **sağlam ve testli** (`ReturnUrlPolicy` + 2 test dosyası); ama **returnTo üretildiği yerde hiç okunmuyor** (`?return=` → 0 tüketici, callback hep `/home`), `_pending_redirect_uri` **ölü kod**, mevcut köprü **yalnız kimlik taşıyor** (hedef/oturum verisi yok), `ResponseEmitter` **doğrulamasız `Location`** atıyor (sorumluluk çağıran tarafta), `auth_key`'de **30 sn replay penceresi** var ve boş-Origin fail-open + auth-local ölü middleware ADR-043'ün açık durumunu teyit ediyor. Karar, bu altı boşluğu tek köprü sözleşmesinde kapatır.

### §1.2 Sorun Tanımı (Problem)

Kullanıcı anonimken korumalı bir sayfaya (`/home`, `/albums/...`) girdiğinde guard onu login'e yolluyor (`AuthGuard.php:31`); **ama hedef URI geri getirilmiyor**: `AuthRouteConfig.php:64-65` `?return=` üretiyor, **hiçbir kod okumuyor** ve callback her seferinde sabit `Location: /home` (`home bootstrap.php:40-91`) ile bitiyor — kullanıcı login sonrası **başladığı sayfaya dönemiyor**. İkinci sorun: giriş anında **eski oturumun anlamlı verisi** (sayfa durumu, seçimler) ile hedef URL **tek bir köprüyle** taşınacak olsa bugünün araçları yetersiz: mevcut `HomeAuthBridge` yalnız kimlik taşıyor (`AUTH_KEY_TTL=300`), `_pending_redirect_uri` writer'ı hiç çağrılmıyor (ölü), `setPendingRedirect`/`consumePendingRedirect` çifti asimetrik. Üçüncü sorun **güvenlik**: hedef URL'yi query'ye düz yazmak open-redirect (CWE-601) ve token sızıntısı (CWE-598 — URL query log/Referer sızar) üretir; `ResponseEmitter.php:83` doğrulamasız `Location` attığı için doğrulama **çağıran zorunluluğu** dağınıktır; `auth_key`'deki 30 sn replay penceresi (`findValidAuthKey($key, true)`) aynen bir köprü token'ına taşınsa **yeniden oynatılabilir** hale gelir. Dördüncü sorun **alan adı sınırı**: köprü auth ↔ home arasında geçeceği için cookie `domain=.coremusic.net` + `SameSite=Lax` (ADR-011) ve Origin/CORS sözleşmesiyle (ADR-043) hizalanmalı — oysa `OriginCheckMiddleware:36-41` boş-Origin'de **fail-open**. Karar; hedefi saklama, taşıma, doğrulama, tek-kullanım ve iptal beşini **tek sözleşme** altında tanımlar.

### §1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | 6 sorgu: (1) "open redirect prevention path-only whitelist validate return URL OWASP" (2) "OAuth 2.0 redirect_uri exact match allowlist RFC 6749 security BCP" (3) "SSO/CDSSO cross-domain login handoff token one-time TTL best practices" (4) "login CSRF protection state parameter one-time token invalidation logout" (5) "token in URL query string leakage CWE-598 referrer log one-time code" (6) "PHP session_regenerate_id session fixation delete_old_session login redirect" |
| Web Search **Konusu** | Open-redirect engelleme (path-only whitelist), OAuth `redirect_uri` birebir eşleşme/allowlist (RFC 6749 + güvenlik BCP), SSO/CDSSO alanlar arası login handoff'ı (tek kullanımlık TTL'li token), login-CSRF + state/token tek-kullanım-sonrası geçersizlik, URL query'de token sızıntısı (CWE-598, Referer/log), PHP `session_regenerate_id` fixation savunması ve login redirect ilişkisi |
| Web Search **Bağlam** | Karar, CoreMusic kod gerçeğiyle yüzleşiyor: whitelist `ReturnUrlPolicy` zaten IMPLEMENTED ve testli (§1.1-A) ama `?return=` üretildiği yerde okunmuyor (§1.1-C), köprü yalnız kimlik taşıyor (§1.1-E), `auth_key` 30 sn replay penceresi var (§1.1-F), `ResponseEmitter.php:83` doğrulamasız `Location` (§1.1-B) ve boş-Origin fail-open (§1.1-H). Araştırma 2026-09-29'da yapıldı; protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (6 sorgu, ≈25 adlandırılmış kaynak, OWASP/RFC/W3C/MDN/PHP resmi kaynakları öncelikli) |
| Web Search **Kısa Açıklama** | Doğru desen **path-only + host allowlist** ile open-redirect'i kökten kesmektir (yönlendirme hedefi hiçbir zaman serbest URL olmaz); taşınan hedef/oturum verisi **imzalı ve tek kullanımlık bir token'a** konur — query'de düz veri veya uzun ömürlü imzasız değer **yasaktır** (CWE-601 + CWE-598); SSO handoff'larında token **kısa TTL + tek kullanım + kullanınca geçersizlik** ile oynatılamaz; login anında eski oturum kimliği `session_regenerate_id(true)` ile yenilenir ve **eski oturum+token bağları kopar** |
| Web Search **Uzun Açıklama** | Kaynaklar beş eksende buluşuyor. (i) **Open redirect:** OWASP Unvalidated Redirects & Forwards ve CWE-601, güvenilir olmayan hedefle yönlendirmenin phishing'e kapı olduğunu; çözümün **whitelist + göreli path zorunluluğu** olduğunu söylüyor — `//evil.com`, `/\evil.com`, `/%2Fevil.com`, `%09`, `%0A`, userinfo (`a@b`) ve kodlanmış ayraç (`%23`, `%40`) gibi bypass vektörleri OWASP/CWE listelerinde birebir CoreMusic `ReturnUrlPolicyEdgeCaseTest` vakalarıyla örtüşüyor; ayrıca "redirect hedefini sunucu tarafında doğrula, çağıran güvenmesin" ilkesi var → `ResponseEmitter` gibi doğrulamasız `Location` atan noktalarda doğrulama **çağıranın zorunluluğu** olarak belgelenmeli. (ii) **OAuth redirect_uri:** RFC 6749 §10.6 + OAuth Security BCP + Auth0/Keycloak/Okta dokümanları `redirect_uri`'nin **birebir eşleşen** kayıtlı değerlerden seçilmesini; kısmi eşleşme/suffix kabulünün (alt-alan adı dâhil dikkatli) açık yarattığını — bu, `AutoRedirectHandler`'daki `redirect_uri` doğrulaması ve ADR-043'ün subdomain hizasıyla aynı prensiptir. (iii) **SSO/CDSSO handoff:** industria rehberleri + IdP dokümanları handoff token'ının **tek kullanımlık, kısa TTL'li (30-300 sn), imzalı** olmasını; kullanıldıkça anında geçersizleşmesini ve logout/expire ile **eski oturuma bağlı tüm türevlerin** iptalini şart koşuyor — `HomeAuthBridge`'in `AUTH_KEY_TTL=300`'ü bu ölçekle tutarlı, ama "yalnız kimlik" taşıması yetersiz. (iv) **Login-CSRF + state:** OWASP CSRF Cheat Sheet ve login-CSRF yazıları, login/logout gibi durum değiştiren GET yönlendirmelerinin **state/token** ile korunmasını; token'ın **tek kullanım sonrası geçersiz** kılınmasını (mevcut `markAuthKeyUsed` deseni tam buna karşılık gelir, `findValidAuthKey(..., true)` 30 sn toleransı ise bilinçli bir retry payıdır — köprü token'ına **taşınmamalı**). (v) **URL'de token sızıntısı:** CWE-598 + PortSwigger/Google kaynakları query-string secret'ının **Referer, tarayıcı geçmişi, proxy/erişim logları ve analytics** üzerinden sızdığını; `Referrer-Policy` + kısa TTL + tek kullanım ile sınırlandığını — dolayısıyla eski oturum verisi **asla query'de düz taşınmaz**, yalnız imzalı token içinde ve mümkünse `POST`/cookie kanalıyla geçer. Ek (vi): PHP `session_regenerate_id(true)` dokümanı login'te eski oturumun **silinerek** fixation'ı kırdığını; bu, "eski oturum verisi köprüsü"nün **eski kimlikten bağımsız** (imza + TTL ile) kurulmasını zorunlu kılar — kimlik yeniden üretilirken veri köprüsü yaşamalıdır. |
| Web Search **Paragraf Veri Uzun** | Login redirect & session bridge, üç soruyu aynı anda yanıtlamalıdır: **hedef nereye saklanır**, **veri nasıl taşınır** ve **ne zaman ölür**? Birinci sorunun cevabıweb'de tek: hedef URL sunucuda, göreli path olarak, host'u allowlist'ten geçirilmiş biçimde yaşar — sorguda yalnız imzalı/özütlenmiş hedef bulunur, serbest URL asla (`//evil.com` benzeri vektörlerin tamamı reddedilir; OWASP/CWE-601). İkinci soru veri kanalını seçer: eski oturum verisi ile hedef URL, **tek kullanımlık imzalı TTL'li token** içine konur; query'ye düz yazılmaz (CWE-598: query log/Referer/geschichte sızıntısı), mümkünse el değiştirme `POST` ve `SameSite=Lax` cookie ile yapılır; token'ın kendisi de URL'de olsa dahi **tek kullanım + kısa TTL + anında geçersizlik** ile penceresi saniyelerle sınırlıdır. Üçüncü soru iptal zinciridir: logout'ta `session_destroy()` (zaten IMPLEMENTED) eski oturumu siler, expire TTL'yi doldurur ve **ikisi de** köprü token'ını kullanılamaz kılar — ayrıca login'de `session_regenerate_id(true)` (9 noktada IMPLEMENTED) eski kimliği yok ettiği için köprü, **kimliğe değil imzaya** dayanır. Tüm bunlar ADR-043'ün alan adı hizası ve ADR-010'un CSRF koruması olmadan tek başına güvenli değildir: redirect'i tetikleyen isteğin kaynağı (Origin/CSRF) doğrulanmadan hedef doğrulaması da phishing'e açık kalır. |
| Web Search **Sonucu** | 6 sorgu ≈ **25 adlandırılmış kaynak** (~4/sorgu); beş kanonik destek: (i) path-only + host allowlist ve bypass vektör kataloğu (OWASP Unvalidated Redirects, CWE-601, `ReturnUrlPolicyEdgeCaseTest` ile birebir örtüşen vakalar), (ii) `redirect_uri` birebir eşleşme (RFC 6749 §10.6, OAuth Security BCP, Auth0/Keycloak/Okta), (iii) tek kullanımlık kısa TTL'li SSO handoff token'ı (IdP/industria rehberleri), (iv) login-CSRF + state/token tek-kullanım-sonrası geçersizlik (OWASP CSRF Cheat Sheet, login-CSRF yazıları; `markAuthKeyUsed` deseni ile uyumlu), (v) URL'de token sızıntısı (CWE-598, PortSwigger, Google `Referrer-Policy` rehberi) + (vi) PHP `session_regenerate_id(true)` fixation/login dokümanı. **Olumsuz/negatif bulgular da var:** kısmi host eşleşmesi (suffix) alt-alan adı bypass'ı yaratır; 30 sn replay toleransı token'a taşınırsa **yeniden oynatma** kapısı açılır; query'de kalan her token Referer/log sızıntısı yüzeyidir; boş-Origin fail-open (bugünkü `OriginCheckMiddleware:36-41`) handoff isteklerini **kaynaksız bırakır**. |
| Web Search **Alınan Karar** | Karar a-f kalemleri bu bulgularla sabitlendi: (a) **returnTo**: hedef URL giriş öncesi saklanır — kaynağı `AuthGuard`/`AuthRouteConfig` (bugünün `?return=` üreticisi) + **imzalı** (HMAC, kısa TTL), geri dönüşte `ReturnUrlPolicy::getSafeUrl()` path-only doğrulamasından geçer; (b) **köprü token'ı**: tek kullanımlık + imzalı + TTL'li; eski oturum verisini + hedef URL'yi **token içinde** taşır; **query'de düz veri yok** (CWE-598), kullanım sonrası anında geçersiz (`markAuthKeyUsed` deseni), 30 sn replay toleransı **kullanılmaz**; (c) **cross-subdomain köprü** ADR-043 ile hizalı: `.coremusic.net` cookie alanı + `SameSite=Lax` (ADR-011) + Origin/CORS doğrulaması — boş-Origin fail-open **köprü için kapatılır şartı**; (d) **open-redirect**: mevcut path-only whitelist (`ReturnUrlPolicy`) tek kapı — host allowlist, `//`, `/\`, kodlanmış bypass testleri **kapı** olarak kalır, `ResponseEmitter` çağrılan her hedef doğrulanmış olmalı; (e) **CSRF/redirect**: ADR-010 (CSRF) — durum değiştiren yönlendirmeler POST + `X-CSRF-Token`; token tüketimi ADR-013 rate limit'ine bağlanır (⚠️ prompt'taki "ADR-013 (CSRF)" düzeltildi — diskte CSRF = ADR-010); (f) **logout/expire**: `session_destroy()` (IMPLEMENTED) + TTL dolumu eski köprü token'ını ve onun taşıdığı oturum verisi bağını **geçersiz kılar** — yeni köprü yalnız yeni login'de üretilir |
| Web Search **Sonuç** | Araştırma, kullanıcı onaylı kapsamı **destekledi ve üç şartı netleştirdi**: (1) hedef URL **yalnız göreli path + host allowlist** olarak yaşar — `ReturnUrlPolicy` testleri **kapı** olur (bypass vektörleri eklenmeden karar uygulanmış sayılmaz); (2) köprü token'ı **tek kullanım + anında geçersizlik** şarttır — `findValidAuthKey(..., true)` 30 sn toleransı köprü kanalında **kapatılmalı** (aksi halde replay); (3) handoff isteği Origin/CSRF doğrulamasından geçmeden (ADR-043 boş-Origin açığı + ADR-010) köprü **kurulmaz**. Yeni bir framework/kütüphane gerekmez; mevcut `ReturnUrlPolicy` + `markAuthKeyUsed` + `HomeAuthBridge` TTL kalıbı üzerine sözleşme eklenir (ADR-001). |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-043 subdomain konsolidasyonu | Köprü auth ↔ home arasında geçer: cookie `domain=.coremusic.net`, CORS allowlist (`$_ENV['CORS_ALLOWED_ORIGINS']`) ve Origin doğrulaması bu ADR'nin **ön koşuludur**; boş-Origin fail-open (`OriginCheckMiddleware.php:36-41`) köprü kanalında açık bırakılamaz |
| ADR-011 oturum yaşam döngüsü | Cookie politikası (`HttpOnly`, `SameSite=Lax`, idle+absolute, rotation) ve çıkışta `session_destroy()` (`SessionMiddleware.php:80-90`) **değiştirilmez**; köprü token'ı bu yaşam döngüsünün üstünde çalışır, kimlik alanına girmez |
| ADR-010 CSRF + ADR-013 rate limit | Durum değiştiren yönlendirmeler POST + `X-CSRF-Token`; köprü token'ı tüketimi rate limit'e bağlanır (⚠️ yönergedeki "ADR-013 (CSRF)" etiketi diskle düzeltilmiştir — CSRF = ADR-010) |
| ADR-009 clean URL redirect | Temiz URL doktrini korunur: köprü verisi kalıcı URL kirletmez, geri dönüş sonrası hedef temizlenir |
| ADR-001-037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| ADR-005 zero hallucination | Diskte/kodda kanıtlanmayan hiçbir satır yazılmaz; `⚠️ VERIFICATION REQUIRED` etiketi kullanılır |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` (log.md = append-only); PowerShell write cmdlet'leri yasak |
| REDACTED | Token, anahtar, cookie değeri, kullanıcı verisi hiçbir koşulda bu ADR'ye yazılmaz; örnek token'lar şematiktir |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz** (arşiv `prompt2:36` yanlış yolu → düz metin); index.md satırı bu işlemde **eklenmez** (reset'e erteli) |

---

## §2 Karar (Decision)

CoreMusic, anonim erişimde korumalı sayfaya giden kullanıcıyı login sonrası **başladığı hedefe döndürür** ve giriş anındaki eski oturum verisini **tek kullanımlık, imzalı, TTL'li bir köprü token'ıyla** taşır — tüm hedefler path-only whitelist'ten geçer, token sorguda düz veri taşımaz, köprü ADR-043 alan adı hizasına bağlıdır ve logout/expire token'ı öldürür.

### §2.1 (a) returnTo — Hedef URL Saklama (kaynak + imzalı)

- **Kaynak:** guard yönlendirmesi anında hedef korumalı URI (`AuthGuard.php:31` → `redirectAuth('login', '/'.$uri)`) tek toplama noktasında okunur; bugünün üreticisi `AuthRouteConfig.php:64-65` (`?return=`) **korunur** — eksik olan **okuyucudur**.
- **Şekil:** hedef **yalnız göreli path** (`/home`, `/albums?x=1` gibi — `ReturnUrlPolicy::getSafeUrl()` çıktısı) + **imza** (HMAC-SHA256, kısa TTL ≤ 300 sn) ile taşınır; `return=/\evil.com` gibi serbest URL değeri **imza dahil reddedilir**.
- **Okuma:** callback'te (`home bootstrap.php:40-91` akışı) `return` okunur → `ReturnUrlPolicy` host/path doğrulaması → geçerliyse hedefe `Location`, **yoksa/şüpheliyse sessizce `/home`'a** (bugünkü davranış korunur — güvenlik varsayılanı).
- **Temizlik:** başarılı geri dönüşten sonra `return` parametresi URL'den **çıkarılır** (ADR-009/016 hizası — tek kullanımlık görünür hedef).

### §2.2 (b) Tek Kullanımlık İmzalı TTL Köprü Token'ı (eski oturum verisi + hedef URL)

| Alan | Karar |
|---|---|
| **İçerik** | Eski oturumun taşınabilir veri kümesi (sayfa durumu/ seçimler — ADR-046 kümesiyle uyumlu, **oturum kimliği DEĞİL**) + hedef URL — **payload içinde**, şema versiyonlu |
| **Şekil** | İmzalı (HMAC-SHA256) compact token; `jti` (tekil id) + `exp` (TTL ≤ 300 sn, `HomeAuthBridge` `AUTH_KEY_TTL` ölçeğiyle eşit) + `iat` |
| **Kanal** | Query'de **düz veri yasak** (CWE-598); mümkünse `POST` body / `HttpOnly` cookie üzerinden el değiştirir, URL'de yalnız token'ın kendisi (kısa ömürlü) bulunur |
| **Tek kullanım** | Tüketimde `jti` sunucu tarafında kaydedilir ve **ikinci kez reddedilir** (`markAuthKeyUsed` deseni — `UserRepository.php:219-260`); ⚠️ `findValidAuthKey($key, true)` **30 sn replay toleransı köprü kanalında KULLANILMAZ** (ayrı okuma yolu) |
| **Taşıma sınırı** | Payload'ta **oturum kimliği, parola, token, PII yok** (REDACTED); yalnız kullanıcı değiştirmeyeceği/kopyalanması zararsız durum verisi |
| **İmzasızlık** | İmzasız veya doğrulanmamış token **reddedilir** — veri bütünlüğü token'ın kendisindedir |

### §2.3 (c) Cross-Subdomain Köprü — ADR-043 Hizası

1. Köprü yalnız `.coremusic.net` ailesi içinde çalışır: cookie `domain=.coremusic.net` + `HttpOnly` + `SameSite=Lax` (ADR-011) **değişmez**.
2. Handoff isteği **Origin/CORS doğrulamasından geçmek zorunda**: allowlist `$_ENV['CORS_ALLOWED_ORIGINS']` (boş → köprü **kurulmaz**, fail-closed); **boş-Origin fail-open (`OriginCheckMiddleware.php:36-41`) köprü kanalında kapatılır** (ADR-043 şartı).
3. `redirect_uri` birebir eşleşme/allowlist ile doğrulanır (`AutoRedirectHandler.php:65-80` kalıbı korunur); suffix-match (`:68`) yalnız kayıtlı alt-alan adlarına uygulanır.
4. **Ölü kod temizliği (tespit):** `auth.../include/Middleware/OriginCheckMiddleware.php:66` (`self::ALLOWED_ORIGINS` tanımsız, 0 örneklenme) **kullanımda değildir ve kullanılmamalıdır** — auth, shared pipeline'ı kullanır (`PageRouterKernel::buildDefaultMiddlewares():275-286`). Bu dosya **silinmez/düzeltilmez** (In-Place Refactoring: dosya adı/onaysız değişiklik yok) — yalnız **§5.1 adım 9'da** Tech Lead onayına sunulur.

### §2.4 (d) Open-Redirect Güvenlik Yolu — Path-Only Whitelist (mevcut kapılar korunur)

- **Tek kapı:** `ReturnUrlPolicy` — host allowlist (`:7-15`), kontrol karakteri reddi (`:39`), `//` ve `/\` reddi + tek katman decode (`:51,102-112`), ham/decode host eşitliği + userinfo reddi (`:76-86`), `getSafeUrl()` path-only (`:127-184`).
- **Doğrulama zorunluluğu:** `ResponseEmitter.php:83` doğrulamasız `Location` attığı için **her çağıran** hedefi doğrulamak zorundadır — bu ADR ile **köprü/returnTo hedefleri için kapı `ReturnUrlPolicy` olur** (yeni doğrulayıcı yazılmaz, mevcut test seti genişletilir).
- **Test kapıları (bypass):** `//evil.com`, `/%2Fevil.com`, `/%09/`, `%0A`, `user@host`, `%23`, `%40` **mevcut testlerde geçiyor**; eklenen her yeni hedef yolu aynı test setinden geçer — **test yoksa karar uygulanmış sayılmaz**.

### §2.5 (e) CSRF / Redirect (ADR-010 — ⚠️ düzeltme)

- Login/logout ve köprü **üretim/tüketim** uçları durum değiştirdiğinden **POST + `X-CSRF-Token`** (`CsrfMiddleware`, `PageRouterKernel.php:281`) ile korunur; **GET ile durum değiştiren köprü kurulumu yasaktır** (GET yalnızca güvenli/nötr dönüş).
- Token tüketimi **rate limit'e** bağlanır (ADR-013 APCu) — token guessing/deneme yüzeyi sınırlanır.
- ⚠️ **Düzeltme:** yönergede "ADR-013 (CSRF)" yazıyordu; **diskte `ADR-010-csrf-protection-strategy` CSRF'dir, `ADR-013-rate-limiting-apcu` rate limiting'dir** — karar ADR-010 üzerinden kurulmuştur.

### §2.6 (f) Logout / Expire — Eski Köprüyü İptal

| Olay | Etki |
|---|---|
| **Logout** | `session_destroy()` (`SessionMiddleware.php:80-90`) eski oturumu yok eder → o oturuma ait **kullanılmamış köprü token'ları** sunucu tarafında (oturum bağı/jti iptal listesi) **geçersizleşir** |
| **TTL expire** | `exp` dolduğu an token reddedilir (≤ 300 sn) — sahipsiz token yaşamaz |
| **Tek kullanım** | İlk başarılı tüketimden sonra `jti` yanar (§2.2) — replay **yok** (30 sn toleransı bu kanalda kapalı) |
| **Yeni login** | `session_regenerate_id(true)` (9 nokta IMPLEMENTED) eski kimliği siler; köprü **imzaya** dayandığı için kimlik yenilemesinden etkilenmez — yeni köprü yalnız yeni login'de üretilir |

### §2.7 Neden Bu Seçenek?

Kod yarısını çoktan yapmış: whitelist **sağlam ve testli** (`ReturnUrlPolicy` + 2 test dosyası), `?return=` **üreticisi var**, köprü TTL kalıbı (`AUTH_KEY_TTL=300`) ve tek-kullanım deseni (`markAuthKeyUsed`) mevcut. Eksik olan **sözleşme**: hedefi nerede saklayacağımız, veriyi **nasıl** (imzalı tek kullanımlık token) taşıyacağımız, hangi kapıdan (path-only) doğrulayacağımız ve **ne zaman** iptal edeceğimiz. Sıfırdan kütüphane/merkezî yönlendirme servisi (alternatif 1) ya da serbest URL'li dönüş (alternatif 2) bu üç eksikliği de çözmeden güvenlik açığı getirir; mevcut parçalar üzerine imzalı-token sözleşmesi en düşük maliyetli tamamlayıcıdır.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Merkezî redirect servisi / yeni kütüphane** (tüm `Location`'lar tek servisten) | Tek doğrulama noktası, `ResponseEmitter` riski kapanır | ADR-001 bağımlılık/benizersizlik artışı; `ResponseEmitter.php:83`'ün tüm çağıranları yeniden yazılır; büyük refactor | Mevcut `ReturnUrlPolicy` zaten aynı işi yapıyor (testli) — kapı mevcut, yalnızca köprü/returnTo için **zorunlu** kılınır (§2.4) |
| 2 | **Hedef URL'yi query'de düz taşı** (`?return=https://evil.com` dahil serbest) | En basit, imzasız, sunucu durumu gerekmez | Open-redirect (CWE-601), token/veri sızıntısı (CWE-598: log/Referer), `ResponseEmitter` doğrulamasız `Location` ile birleşince **doğrudan phishing kapısı** | Web araştırmasının tamamı reddediyor; `ReturnUrlPolicyEdgeCaseTest` zaten bu sınıf vektörleri reddediyor — kararı çelişir |
| 3 | **Eski oturum verisini cookie'ye sığdır** (cross-domain cookie carry) | Sunucu state'siz, hızlı | Cookie boyutu sınırı (4 KB), `HttpOnly` olmayan okuma XSS yüzeyi, ADR-011 cookie alanına gölge veri, logout'ta temizlik çift haneli | Veri **imzalı tek kullanımlık token** ile taşınır (§2.2): boyut sınırı + tek kullanım + TTL aynı kazancı verir |
| 4 | **`_pending_redirect_uri`'yi canlandır** (sunucu tarafı pending kaydı) | Sunucu state, imza gerektirmez | Yazıcı **bugün 0 çağrı** (ölü kod); auth↔ home **farklı oturum** saklar (ADR-043) → aynı oturumda yazılamaz/okunamaz; logout'ta state dağılır | Cross-subdomain'de oturumlar ayrışır; bu yüzden durum **token içinde** taşınır, sunucu-side pending tek başına yetersiz (tek başına değil, §2.2 ile birlikte değerlendirilip **reddedildi**) |
| 5 | **Hedefi localStorage'da tut** (giriş sonrası oku) | Basit, URL kirlenmez | XSS'e açık (imzasız), çok-sekme/çıkış davranışı belirsiz, ADR-046'da sessionStorage yalnız oturum-içi geçici durum sözleşmesinde | Güvenlik (imza + TTL) ve yaşam döngüsü (logout/expire) gerektirir; tarayıcı deposu bu iki şartı **garanti etmez** |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Kullanıcı login sonrası **başladığı sayfaya döner** — bugünkü "her şey `/home`'a düşüyor" kaybı (`bootstrap.php:40-91`) kapanır.
- Eski oturum verisi + hedef URL **tek imzalı token'da** taşınır: query'de düz veri yok (CWE-598 yüzeyi kapalı), tek kullanım + TTL (≤ 300 sn) ile replay penceresi **saniyelerle** sınırlı.
- Open-redirect kapısı **zaten testli** (`ReturnUrlPolicy` + EdgeCaseTest) — yeni kod eklemeden, mevcut kapı **zorunlu** hale getirilir; `ResponseEmitter`'ın doğrulamasız `Location` riski köprü/returnTo hedefleri için kapanır.
- Logout/expire zinciri **bugünden IMPLEMENTED** olan `session_destroy()` + `session_regenerate_id(true)` üzerine oturur — ek altyapı gerekmez.
- ADR-043/011/010/013 ile **çakışmasız**: mevcut cookie, Origin/CORS, CSRF ve rate limit yapıları aynen kalır.

### 4.2 Olumsuz Sonuçlar

- `?return=` okuyucusu + callback davranışı değişir → **regresyon yüzeyi**: yanlış okuma `/home` yerine başka hedefe götürebilir (mitigasyon §4.3-1: doğrulama + fallback).
- Yeni token/`jti` iptal deposu (tek kullanım) **sunucu state** ekler — bellek/DB satırı, TTL temizliği gerekir.
- `OriginCheck` boş-Origin fail-open'ı köprü için kapatılırsa **daha katı** olur: Origin'siz meşru istekler reddedilebilir → uyum testi gerekir.
- auth-local ölü middleware (`OriginCheckMiddleware.php:66` latent fatal) **dokunulmadan** kalır → ileride birisi sınıfı örneklerse fatal riski (düzeltme onaya bağlı, §5.1 adım 9).
- 30 sn replay toleransının köprü kanalında **ayrı** kapatılması, iki okuma yolu demek — akış karmaşıklığı artar.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **Open-redirect kaçışı**: yeni hedef yolu whitelist'ten kaçar (`//`, `/\`, kodlanmış) | 2 (Mümkün) | Yüksek (phishing/CWE-601) | `ReturnUrlPolicy` tek kapı (§2.4) + EdgeCaseTest seti **kapı**: yeni hedef test olmadan geçmez; doğrulamasız `Location` çağıranları yalnız `getSafeUrl()` çıktısıyla yönlendirir |
| 2 **Replay**: köprü token'ı 30 sn toleransıyla ya da `jti` deposu olmadan yeniden oynatılır | 3 (Olası) | Yüksek (oturum verisi hırsızlığı) | `jti` tek kullanım depo + `findValidAuthKey(..., true)` toleransı **bu kanalda kapalı** (§2.2) + TTL ≤ 300 sn + rate limit (ADR-013) |
| 3 **Query sızıntısı**: token/URL Referer, log, tarayıcı geçmişine düşer (CWE-598) | 3 (Olası) | Orta-Yüksek (veri/ kimlik sızıntısı) | Query'de **düz veri yok**; mümkünse POST/cookie kanalı; URL'de kalan token kısa TTL + tek kullanım; `Referrer-Policy` ile sınır (mevcut `SecurityHeaders`); payload'ta PII kimlik yok (REDACTED) |
| 4 **Çalışmaz köprü**: boş-Origin fail-open / CORS varsayılan boşu nedeniyle handoff reddedilir ya da kaynaksız geçer | 3 (Olası) | Orta (login sonrası dönüş kırılır) | Fail-closed şartı (§2.3-2): allowlist boşsa köprü **kurulmaz** ama kullanıcı `/home`'a düşer (kayıpsız fallback); Origin'siz istek köprüde reddedilir; uyum testi §5.1 adım 6 |
| 5 **Oluk tutmaz payload**: eski oturum verisi token'a sığmaz / taşınabilir olmayan veri (kimlik, PII) sızar | 3 (Olası) | Yüksek (KVKK + boyut) | Kapalı veri kümesi (ADR-046 durum alanları; oturum kimliği/PII **yasak**) + boyut sınırı; sığmayan veri **taşınmaz** (fallback: hedef URL tek başına taşınır) |
| 6 **Ölü kod/latent fatal**: auth-local `OriginCheckMiddleware` örneklenirse `self::ALLOWED_ORIGINS` fatal verir | 2 (Mümkün) | Orta (500) | Silme/düzeltme **onaya** bağlı (§5.1 adım 9); use-case yok → örneklenmemeli; shared pipeline tek kaynaktır (`PageRouterKernel`) |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **`return` okuyucusu kapanırsa** sistem bugünkü davranışına döner: herkes `/home`'a — veri kaybı yok, yalnız (a) karşılanmaz sayılır ve §5.1'e geri alınır.
2. **Köprü token'ı devre dışı bırakılırsa** `HomeAuthBridge` yalnız kimlik taşımaya devam eder (bugünkü IMPLEMENTED hâl) — hedef/oturum verisi taşınmaz, login akışı **kırılmaz**.
3. **İmza doğrulama başarısızsa** token reddedilir ve kullanıcı sessizce `/home`'a düşer — güvenlik varsayılanı (asla serbest hedefe gitmez).
4. **Fail-closed Origin/CORS** açılırsa meşru handoff reddedilebilir → kullanıcı `/home`'a düşer (kayıp: dönüş konforu, güvenlik: korunur); uyum testi (§5.1-6) ile ayarlanır.
5. **`jti` deposu kaldırılırsa** tek kullanım garantisi kalkar → (b)+(f) şartları karşılanmaz; karar **kısmen uygulanmamış** sayılır, açıkça raporlanır.
6. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001-037) etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **returnTo üret + oku:** guard hedefini `AuthRouteConfig.php:64-65` deseninde **imzalı** (`HMAC` + TTL ≤ 300 sn, göreli path) üret; callback'te (`home bootstrap.php:40-91` akışı) oku → `ReturnUrlPolicy::getSafeUrl()` doğrulaması → geçerliyse hedefe, yoksa `/home`; başarılı dönüşte parametre temizlenir | Backend + Security | 1 gün |
| 2 | **Köprü token'ı (üretim):** `HomeAuthBridge` üzerine payload (durum + hedef URL; oturum kimliği/PII yok) + `jti` + `exp` (≤ 300 sn) + HMAC üretilir; kanal: mümkünse POST/cookie, URL'de yalnız token | Backend + Security | 1.5 gün |
| 3 | **Köprü token'ı (tüketim):** `jti` tek-kullanım deposu (markAuthKeyUsed deseni); `findValidAuthKey(..., true)` 30 sn toleransı **bu kanalda yok**; ikinci kullanım → 4xx + `/home` fallback | Backend | 1 gün |
| 4 | **Whitelist kapısı zorunluluğu:** köprü/returnTo hedeflerinin **tek** doğrulayıcısı `ReturnUrlPolicy`; bypass test seti (`//evil.com`, `/%2Fevil.com`, `/%09/`, `%0A`, userinfo, `%23`, `%40`) + yeni vektörler eklenir — **test yoksa adım tamamlanmaz** | Security + QA | 1 gün |
| 5 | **CSRF/redirect + rate limit:** uçlar POST + `X-CSRF-Token` (ADR-010, `PageRouterKernel.php:281`); token tüketimi ADR-013 rate limit'ine bağlanır; GET ile köprü kurulumu yasak | Security | 0.5 gün |
| 6 | **Origin/CORS fail-closed + uyum testi:** köprü isteğinde boş-Origin **reddedilir** (`OriginCheckMiddleware.php:36-41` açığı için); CORS allowlist boşsa köprü kurulmaz; Origin'li/Originsiz senaryo testleri | Security + QA | 1 gün |
| 7 | **Logout/expire iptali:** çıkışta (`session_destroy()` IMPLEMENTED) oturum bağı/jti iptal listesine alınır; TTL dolumu otomatik; yeni köprü yalnız yeni login'de | Backend | 0.5 gün |
| 8 | **Testler (kapı):** (i) login sonrası hedefe dönüş, (ii) imzasız/bozuk hedef → `/home`, (iii) token ikinci kullanım → reddedildi, (iv) TTL dolumu → reddedildi, (v) logout sonrası eski token → reddedildi, (vi) bypass vektörleri, (vii) Origin'siz handoff → reddedildi. **Test yoksa karar uygulanmış sayılmaz** | QA | 1.5 gün |
| 9 | **Onay beklili iki kayıt (ERTELENDİ):** (i) `.ai/.decisions/index.md`'ye ADR-047 satırı — **bir sonraki vault reset'ine ertelendi** (bu işlemde dokunulmadı); (ii) auth-local ölü `OriginCheckMiddleware.php:66` (`self::ALLOWED_ORIGINS` tanımsız) — silme/düzeltme **Tech Lead onayına** sunulur (In-Place Refactoring: onaysız değişiklik yok) | Vault Steward + Tech Lead | 0.2 gün |
| 10 | **Debate (persona turları)** tamamlanır → §7'deki Debate/Tech Lead `⏳` satırları güncellenir | Vault Steward | 0.5 gün |

**Toplam ≈ 9.2 gün** (fazlar tekrarlanabilir; adım 8 kapı olmadan yayına çıkılmaz).

### §5.2 Geri Dönüş Planı

1. **Adım 1 tersi:** `return` okuyucusu kaldırılır → callback eski sabit `Location: /home`'a döner (`bootstrap.php:40-91`); üretici (`?return=`) zararsız kalır, başka hiçbir şey kırılmaz.
2. **Adım 2/3 tersi:** köprü token üretimi/tüketimi bayrakla kapatılır (`bridge: false`) → `HomeAuthBridge` yalnız kimlik taşımaya (bugünkü IMPLEMENTED hâle) döner; **jti deposu korunur ama kullanılmaz**, veri silinmez.
3. **Adım 4 tersi:** zorunlu kapı gevşetilmez — bu adım **yalnız genişleme** içerir; geri alınması gereken testler silinmez, yalnız yeni vektör testleri pasifleşir (report edilir).
4. **Adım 5/6 tersi:** CSRF/Origin şartları ADR-010/043'ün kendi alanına döner (köprüye özel ek kapanır); CORS allowlist boşuna geri dönülmez (varsayılan zaten boş).
5. **Adım 7 tersi:** jti iptal listesi durdurulur → yalnız TTL kalır (daha zayıf ama çalışır); `session_destroy()` yolu **etkilenmez**.
6. **Adım 9 geri alma yoktur:** index satırı ve ölü middleware değişikliği henüz **yapılmadı** — ertelenmiş/onaş bekleyen işlerdir.
7. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001-037) etkilenmez.

---

### §5.3 Debate Şartları (3 şart — KABUL koşulu)

| # | Şart | Kapsam | Kabul ölçütü |
|---|---|---|---|
| 1 | **Location doğrulama + replay/ölü kod** | **1a:** `ResponseEmitter.php:83` + `index.php:103,124` değişkenli `Location` hedefleri **çağrılan tarafta** `ReturnUrlPolicy`'den geçer (CI denetimi zorunlu); **1b:** köprü kanalında 30 sn replay **kapalı** + `_pending_redirect_uri` / auth-local `OriginCheckMiddleware.php:66` ölü kodu temizliği (§5.1 adım 9 — Tech Lead onayı) | CI denetimi geçti + köprüde ikinci kullanım reddi testi geçti + ölü kod kararı onaylandı |
| 2 | **Origin/CSRF ön koşulu** | ADR-043 boş-Origin fail-open: Origin/CSRF **doğrulanmadan köprü kurulmaz** (fail-closed); ADR-010 `X-CSRF-Token` | §5.1 adım 6 Origin'li/Originsiz senaryo testleri geçmeden köprü aktif edilmez |
| 3 | **Bypass + tek-use testi** | Bypass seti (`//evil.com`, `/\evil.com`, `/%2Fevil.com`, userinfo, `%09`/`%0A`, encoded) + tek-use/replay testi | §5.1 adım 8 test seti **kapı** — test yoksa karar uygulanmış sayılmaz |

> **Kaynak:** §7.1 Debate Kaydı (3 tur / 20 persona, 18 kabul / 2 çekimser / 0 red → KABUL). Şartlar bağlayıcıdır; karşılanmadan (a)-(f) kalemleri uygulanmış sayılmaz.

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../AGENTS.md]] | Agent registry, onay/escalation §10, frozen kuralı §25.3 |
| [[../../WORKFLOW.md]] | Süreçler, fazlar |
| [[../../brain.md]] | Mimari karar özeti |
| [[../../index.md]] | Master katalog |
| [[../../keys.md]] | Keyword haritası |
| [[../../MEMORY.md]] | Session hafızası |
| [[../../log.md]] | Audit trail (append-only) |
| [[../../glossary.md]] | Terimler (returnTo, bridge token, open redirect, replay) |
| [[../index.md]] | Karar dizini — **ADR-047 satırı YOK (reset'e ertelendi — §5.1 adım 9)** |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination — §1.1 dürüst etiket |
| [[ADR-009-clean-url-redirect]] | Temiz URL redirect doktrini — §2.1 hedef temizliği |
| [[ADR-010-csrf-protection-strategy]] | **CSRF** — karar (e) (⚠️ yönergedeki "ADR-013 (CSRF)" düzeltildi) |
| [[ADR-011-session-management]] | Oturum yaşam döngüsü, cookie politikası, logout/`session_destroy` |
| [[ADR-013-rate-limiting-apcu]] | Token tüketimi rate limit'i — karar (e) (CSRF **değildir**) |
| [[ADR-020-api-public-security]] | API güvenlik katmanı — handoff uçları |
| [[ADR-043-auth-subdomain-consolidation]] | **Doğrudan öncül** — cross-subdomain köprü/CORS/Origin hizası (karar c) |
| [[ADR-046-cross-view-state-preservation]] | Taşınan durum kümesi + URL-öncelikli hizalanma (karar a/b payload kapsamı) |
| [[../../../shared/CLAUDE.md]] | `ReturnUrlPolicy`, `AuthRouteConfig`, `ResponseEmitter`, `OriginCheckMiddleware`, `CsrfMiddleware` kanıtları |
| [[../../../auth.coremusic.net/CLAUDE.md]] | `AuthController`, `SessionManager`, `UserRepository` (auth_key), `AutoRedirectHandler` kanıtları |
| [[../../../home.coremusic.net/CLAUDE.md]] | `HomeAuthBridge`, `config/bootstrap.php` (`/auth/callback` → `/home`) kanıtları |
| §2.2 Köprü Token Sözleşmesi | `jti` tek kullanım + `exp` ≤ 300 sn + HMAC + query'de düz veri yok |
| §2.4 Path-Only Kapı | `ReturnUrlPolicy` tek doğrulayıcı + bypass test seti **kapı** |
| §5.3 Debate Şartları (3) | **KABUL koşulu (bağlayıcı):** (1) Location doğrulama + replay/ölü kod (1a-1b), (2) Origin/CSRF ön koşulu, (3) bypass + tek-use testi |
| `.ai/archives/prompt2-auth-2026-08-15.md:36` | **Düz metin:** ADR-047 adının ilk geçtiği arşiv satırı — yol `.ai/decisions/...` nokta eksikti, dosya yoktu (wiki-link yazılmadı) |
| ⚠️ `ADR-083` (SPA router) | **Diskte dosya YOK** → düz metin + `⚠️ VERIFICATION REQUIRED`, wiki-link yazılmadı |

> **Wiki-link doğrulaması:** Yukarıdaki vault hedeflerinin **tamamı** yazımdan önce `Test-Path` ile diskte doğrulanmıştır (2026-09-29). Diskte **olmayan** hedefe (`ADR-083-*`, arşiv `prompt2:36` yanlış yolu) wiki-link **yazılmamış**, düz metin + `⚠️ VERIFICATION REQUIRED` kullanılmıştır. `ADR-047-*` bu dosyanın kendisidir (yazım sonrası doğrulanır).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI (2026-09-29) — 3 tur / 20 persona · Sonuç: 18 kabul / 2 çekimser / 0 red → KABUL.**

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | 20 persona bulgu turu (~25 kaynak / 6 sorgu) | 16 kabul/neutral + 4 uyarı (QA: bypass test seti · DevOps: doğrulamasız `Location` · Critic: replay + ölü kod şart) |
| 2 | İtiraz → çözüm (4 itiraz) | 4 bağlayıcı çözüm → **3 şart** (§5.3) |
| 3 | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Tur 1 — bulgu (20 persona):** `AuthRouteConfig.php:64-65` `?return=` üretiyor, okuyucu **0 hit** · `ReturnUrlPolicy.php:7-184` host allowlist **IMPLEMENTED** + 2 test dosyası (`//`, `/\`, userinfo, kontrol karakteri) · `ResponseEmitter.php:83` **doğrulamasız `Location`** · `index.php:103,124` değişkenli `$redirectUrl` · `HomeAuthBridge.php` IMPLEMENTED **yalnız kimlik** (`AUTH_KEY_TTL=300`), hedef URL yok → imzalı tek-use token **PLANNED** · `_pending_redirect_uri` **0 çağrı (ölü)** · `UserRepository.php:219-260` → `AuthService.php:287-307` **30 sn replay penceresi** · `OriginCheckMiddleware.php:66` tanımsız `self::ALLOWED_ORIGINS` **latent fatal (ölü kod)** · düzeltme: **CSRF = ADR-010**, **ADR-013 = rate limiting** · `index.md`'de ADR-047 satırı **YOK** (ertelendi).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|---|---|---|
| 1 | `ResponseEmitter.php:83` + `index.php:103,124` doğrulamasız `Location` | Çağrılan tarafta `ReturnUrlPolicy` zorunluluğu (CI denetimi) | **Şart 1a** |
| 2 | 30 sn replay + `_pending_redirect_uri` ölü | Köprüde replay kapalı + ölü kod temizliği | **Şart 1b** |
| 3 | ADR-043 ön koşulu (Origin fail-open) | Origin/CSRF doğrulanmadan köprü kurulmaz bağı | **Şart 2** |
| 4 | Test yok | Bypass test seti (`//`, `/\`, userinfo, encoded) + tek-use/replay testi | **Şart 3** |

**Tur 3 — Oy:** 18 kabul / 2 çekimser / 0 red → **KABUL**. **3 şart bağlayıcıdır (§5.3):** (1) Location doğrulama + replay/ölü kod (1a-1b), (2) Origin/CSRF ön koşulu, (3) bypass + tek-use testi.

- **Kapsam:** (a) returnTo hedef URL saklama (kaynak + imzalı) · (b) tek kullanımlık imzalı TTL köprü token'ı (eski oturum verisi + hedef URL; query'de düz veri yok) · (c) cross-subdomain köprü ADR-043 hizası · (d) open-redirect path-only whitelist (host, `//`, `/\`, kodlanmış bypass testleri) · (e) CSRF/redirect (**ADR-010** — yönergedeki "ADR-013 (CSRF)" diskle düzeltildi) · (f) logout/expire köprü iptali — **kullanıcı onaylı**.
- **Kanıt:** `ReturnUrlPolicy.php:7-184` + 2 test dosyası (bypass vakaları reddediliyor) · `AuthRouteConfig.php:64-65` üretici / tüketici **0 hit** · `bootstrap.php:40-91` hep `/home` · `HomeAuthBridge` `AUTH_KEY_TTL=300` yalnız kimlik · `_pending_redirect_uri` yazıcı **0 çağrı** · `UserRepository.php:219-260` + 30 sn replay penceresi · `session_regenerate_id(true)` 9 nokta + logout `session_destroy()` · `ResponseEmitter.php:83` doğrulamasız `Location` · boş-Origin fail-open + auth-local ölü middleware.
- **Araştırma:** 6 sorgu / **≈25** adlandırılmış kaynak (protokol `10-web-research-protocol.md`).
- **Tech Lead:** ✅ (2026-09-29 — 3 tur / 20 persona, 18/2/0 KABUL + 3 şart §5.3) · **Arch Lead:** ⏳ (beklemede).

---

*ADR-047 v1.0.0 | 2026-09-29 | Created*
*Authority: CoreMusic Vault — Login Redirect & Session Bridge*
*Mode: Red Team · Human Mode · Truth Mode*
