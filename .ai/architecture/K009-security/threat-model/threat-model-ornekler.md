---
node_id: "K009-security/threat-model/threat-model-ornekler"
node_type: "module"
status: "verify-required"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - "shared/src/Middleware/OriginCheckMiddleware.php:36-43 (fail-open dal + 403)"
  - "shared/src/Middleware/OriginCheckMiddleware.php:72-76 (wildcard suffix yasağı)"
  - "shared/src/Middleware/CsrfMiddleware.php:24-63 (safe-method + hash_equals + 403)"
  - "shared/src/Middleware/CorsMiddleware.php:63-75 (allowlist yansıma)"
  - "shared/src/Middleware/BypassAuthMiddleware.php:29-54 (fail-closed + securityEvent)"
  - "shared/src/Middleware/RateLimiterMiddleware.php:20-65 (AUTH_FAIL_CLOSED_PATHS + 503)"
  - "shared/src/Security/ReturnUrlPolicy.php:102-181 (protokol-göreli + scheme/userinfo/host)"
validation: "6/6 Dosya·Satır yüzey-eşleşmesi disk kanıtıyla doğrulandı (2026-10-15, repo HEAD c42a445); Ör-01'de R bağı yok (§8'e göre kayıt önerisi); devamı: anayasa §7 tetikleyicileri"
open_questions: []
group: "kod"
---

# K009 · threat-model · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kod-Yüzey Eşleşme Kayıtları (Tehdit → Dosya·Satır → Kontrol → Kanıt)

---

## §1 KAYIT FORMATI

Her kayıt: **(a)** yüzey, **(b)** tehdit (T-xx / STRIDE etiketi), **(c)** gerçek `Dosya:Satır` kanıtı, **(d)** kontrol, **(e)** kanıt tipi, **(f)** durum.
**Kural (anayasa Z-5 / T-11):** Dosya·Satır olmadan kayıt yazılmaz — tüm satır aralıkları 2026-10-15'te diskten okunarak üretildi (uydurma YOK). Sınıf etiketleri anayasa §3 (S/T/R/I/D/E) ve §3.2 (7-12) içindir; her kayıtta varsa risk kaydı (R-xx) da bağlanır.

---

## §2 KAYITLAR

### Ör-01 — Origin check fail-open (T-04 bypass / spoofing)

| Öğe | Değer |
|---|---|
| Yüzey | 1p API + admin + doküman servislerinde origin doğrulaması |
| Tehdit | Sınıf 10 (misconfiguration, anayasa §3.2) + D/E — origin check fail-open olursa tarayıcı-origin'li saldırgan XHR ile auth'lu endpoint'lere ulaşır |
| Dosya·Satır | `shared/src/Middleware/OriginCheckMiddleware.php:36-43` — origin header yoksa ve `origin_check_dev_fallback=true` ise **allow**: `if ($origin === '' && $fallbackOriginHeader === '') { if ($this->isDevFallbackEnabled()) { return $handler->handle($request); } ... }` |
| Kontrol | Dev-fallback ortam köprüsü (`origin_check_dev_fallback`); prod'da dev fallback **açıkça kapalı**: `OriginCheckMiddleware.php:109-111` — "prod'da fallback asla açık gelmez" |
| Kanıt | Fail-open dali sadece flagtrue ise; flag prod'da default false. Origin varsa ve allowlist'te değilse → **403** `OriginCheckMiddleware.php:43` |
| Durum | ✅ Kontrollü — 2 dal (empty-origin+flag / origin-mismatch) ayrı ayrı izlenmeli (U-4) |

### Ör-02 — CORS allowlist yansıması (T-04 spoofing / bilgi ifşası)

| Öğe | Değer |
|---|---|
| Yüzey | CORS preflight + response header set'i |
| Tehdit | Sınıf 10 (misconfiguration, anayasa §3.2) — `Access-Control-Allow-Origin` origin'i yansıtırsa saldırgan başka origin'den yanıtı okur |
| Dosya·Satır | `shared/src/Middleware/CorsMiddleware.php:63-75` — origin **allowlist'e eşitse** yansıtılıyor: `:69-70` — `withHeader('Access-Control-Allow-Origin', $origin)`; allowlist dışı ise header **hiç basılmıyor** (`:65-67`) |
| Kontrol | Allowlist-only yansıma: origin allowlist'i config'den gelir (`CorsMiddleware.php:45`); yansıma kuralı `:63-64` — "wildcard yok, allowCredentials=true ile birlikte rastgele origin yansıtmak yasak" (kod yorumu: ADR-010/022); preflight header listesi `:35-43` — ADR-020 §2.2E (X-CSRF-Token/Authorization/X-Api-Key düşürülmez) |
| Kanıt | Origin header yoksa CORS yanıtı da yok; allowlist dışı origin'e ACAO yansıması yok; wildcard `*` yok |
| Durum | ✅ Kontrollü — allowlist config'den gelir (`CorsMiddleware.php:44-45`); **⚠️** listede hangi origin'lerin olduğu config kanıtı bu dosyada okunmadı |

### Ör-03 — CSRF token doğrulama + bypass kaldırma (sınıf T / ADR-010)

| Öğe | Değer |
|---|---|
| Yüzey | State-changing POST (login/logout/CRUD) |
| Tehdit | Sınıf T (Tampering — CSRF) + E; risk kaydı R-04/R-14 — cross-origin form POST, token tahmini |
| Dosya·Satır | (a) bypass kaldırma: `shared/src/Middleware/CsrfMiddleware.php:15-17` — "auth'daki bypass köprüsü **kaldırıldı** (ADR-010 §4)"; (b) safe-method ayrımı `CsrfMiddleware.php:24`; (c) sabit-zamanlı karşılaştırma `CsrfMiddleware.php:42` — `hash_equals($expected, $given)`; (d) ihlal → **403** `csrf_invalid` + securityEvent `CsrfMiddleware.php:49-63` |
| Kontrol | ADR-010 kararı: token'lı CSRF (double-submit + `hash_equals`); cookie bayrağı SameSite=**Lax** (kanıt: `shared/src/Session/CLAUDE.md:49`); state-changing'de bypass yok |
| Kanıt | Request body'de token yoksa 403; body+header iki kanalı (header `X-CSRF-Token`, form `_token`) — header kanalı `CorsMiddleware.php:39` reflect'ünde görünür |
| Durum | ✅ Kontrollü — OAuth callback/CSRF-Nonce açık sorusu ayrı kayıtta (bkz threat-model/index.md §5) |

### Ör-04 — BypassAuth fail-closed + securityEvent (sınıf S+E · R-01 · sınıf R)

| Öğe | Değer |
|---|---|
| Yüzey | `?dev-bypass-token` header ile auth bypass kapısı |
| Tehdit | Sınıf S + E (auth atlatma); risk kaydı R-01 (**AÇIK — test yok**) — debug bayrağı prod'da açık gelirse herkes auth'suz "admin" olur; ayrıca sınıf R (log yoksayma, R-07) |
| Dosya·Satır | (a) fail-closed: `shared/src/Middleware/BypassAuthMiddleware.php:29-31` — flag yoksa ve header varsa **reddet**; (b) header yoksa "dev bypass yok" `BypassAuthMiddleware.php:42-44`; (c) **securityEvent** yazımı: `BypassAuthMiddleware.php:49-54` |
| Kontrol | Env-gated bypass (flag'lar env'den; kodda gizli anahtar yok — anayasa §7 tetikleyici 7); sınıf R: bypass her kullanıldığında olay satırı |
| Kanıt | Log satırı: `shared/logs/app.log` — `DEV BACC 006` entry'si (2026-10-09) bypass'ın **kullanıldığında** göründüğünü gösteriyor |
| Durum | ✅ Kontrollü — kanıt canlı (log'da gerçek satır) |

### Ör-05 — Rate limiter fail-closed yolları (sınıf D · R-12/R-13)

| Öğe | Değer |
|---|---|
| Yüzey | Login/OTP/reset gibi auth uçları |
| Tehdit | Sınıf D (Denial of Service); risk kaydı R-12/R-13 — rate-limiter servisi düşerse credential stuffing serbest kalır |
| Dosya·Satır | (a) kapalı liste: `shared/src/Middleware/RateLimiterMiddleware.php:20-30` — `AUTH_FAIL_CLOSED_PATHS = ['/api/auth/login', '/api/auth/otp', ...]`; (b) 503 fail-closed: `RateLimiterMiddleware.php:53-65` — "auth fail-closed: rate-limiter yoksa **istek geçmez**"; (c) ihlal → 429 + `RATE 005` securityEvent `RateLimiterMiddleware.php:86-99` |
| Kontrol | R-13 kararı (ADR-013): auth yollarında fail-closed; genel yollar fail-open (`RateLimiterMiddleware.php:66-68`) |
| Kanıt | 503/429 + log satırı; genel yollar "best-effort" olarak not edilmiş (kapsam sınırı §1c) |
| Durum | ✅ Kontrollü — genel (auth-dışı) yolların fail-open'ı bilinçli kabul (kapsam dışı, bkz Ör-06 sınırı) |

### Ör-06 — ReturnUrl open-redirect savunması (sınıf 8 · R-05)

| Öğe | Değer |
|---|---|
| Yüzey | Login sonrası redirect |
| Tehdit | Sınıf 8 (açık redirect, anayasa §3.2) + T; risk kaydı R-05 — `?next=https://evil.tld` ile phishing redirect'i |
| Dosya·Satır | (a) protokol-göreli `//evil`: `shared/src/Security/ReturnUrlPolicy.php:102-112` ("göreli sanılan ama `//` ile protokol-göreli"); (b) kontrol-karakterleri `ReturnUrlPolicy.php:39-41` + `121-125`; (c) scheme reddi: `ReturnUrlPolicy.php:169-171`; (d) userinfo (`@`) reddi: `ReturnUrlPolicy.php:173-175`; (e) host allowlist: `ReturnUrlPolicy.php:177-181` |
| Kontrol | Zaman damgalı imzalı return-ticket (ADR-010 §2 5-ek) + allowlist host (`ReturnUrlPolicy.php:177-181`) |
| Kanıt | 5 savunma katmanı ayrı ayrı satır-aralığıyla mevcut |
| Durum | ✅ Kontrollü — ReturnUrlPolicy sınırlı yüzey (login flow) |

### Ör-07 — PermissionMiddleware RBAC reddi + gözlem (sınıf E · R-14)

| Öğe | Değer |
|---|---|
| Yüzey | Route meta RBAC kontrolü (`requiredRole` / `requiredPermission`) — admin ve auth'lu uçlar |
| Tehdit | Sınıf E (Elevation of Privilege) — rol/izin karşılaştırmada zaaf veya sessiz reddi; risk kaydı R-14 (AZALTILMIŞ) |
| Dosya·Satır | (a) auth verisi yoksa geç: `shared/src/Middleware/PermissionMiddleware.php:23-25`; (b) rol eşitlik kontrolü `:30-33` → 403 `role_insufficient`; (c) izin **strict** `in_array(..., true)` `:36-42` → 403 `permission_denied`; (d) reddet → `securityEvent('authz_denied')` + 403 `halt` `:47-62` |
| Kontrol | RBAC + fail-safe reddi; docblock `:13` frozen sıra: "Auth → Permission → Validation → Controller" (ADR-010/043 uyumlu, `:8` L1 Pipeline #9) |
| Kanıt | Reddin kendisi loglanır: `:50-54` (`reason`, `uri`, `ip`) — kod yorumu `:49` "P5-32: authz reddi gözlemlenir (öncekte sessizce 403 dönüyordu)" — sınıf R bu bileşende karşılanır |
| Durum | ✅ Kontrollü — R-14 kanıtı bu örnekle Dosya·Satır'a bağlandı (dogrulama AD-12) |

### Ör-08 — ValidationMiddleware şema doğrulaması (sınıf T · R-06)

| Öğe | Değer |
|---|---|
| Yüzey | State-changing (POST/PUT/DELETE) body doğrulaması — tüm 1p uçlar |
| Tehdit | Sınıf T (Tampering — girdi enjeksiyonu girişi) + I; risk kaydı R-06 (**KISMİ** — "şema kanıtı dalga-3") |
| Dosya·Satır | (a) safe-method atlama: `shared/src/Middleware/ValidationMiddleware.php:22-24` (GET/HEAD/OPTIONS doğrulanmaz); (b) `required_fields` route meta `:26-31`; (c) required/min_length/max_length/email/enum kuralları `:41-69`; (d) ihlal → **422** `validation_failed` + `halt` `:72-80` |
| Kontrol | Route-meta tabanlı şema doğrulaması — ADR-002 (parametreli PDO + ORM yasak) ile katmanlı (riskler §2 R-06) |
| Kanıt | Boşluk da kanıttır: `:29-31` — `required_fields` **boşsa doğrulama atlanır**; uç route meta'sında tanım yoksa kontrol çalışmaz (route tanımları `shared/config/routes.php` bu örnekte okunmadı ⚠️) |
| Durum | ⚠️ Kısmi — mekanizma ✓, uç/envanter kanıtı ayrı (dogrulama AD-13 · AD-11) |

### Ör-09 — SecurityHeaders başlık set'i + CSP nonce (sınıf I/T · ADR-012 · bulgu B-2)

| Öğe | Değer |
|---|---|
| Yüzey | Global yanıt başlıkları + Content-Security-Policy |
| Tehdit | Sınıf I (referrer/script sızıntısı) + T (XSS); **HSTS eksikliği** → sınıf I/M (HTTPS downgrade → MITM, bulgu B-2) |
| Dosya·Satır | (a) nonce üretimi: `shared/src/Middleware/SecurityHeadersMiddleware.php:17-19` (`bin2hex(random_bytes(32))`); (b) 9 başlık `:26-36` (`X-Frame-Options: DENY` `:28`, `Referrer-Policy` `:30`); (c) yalnız HTTPS'te COOP/COEP `:38-41`; (d) `'strict-dynamic' 'nonce-…'` `:54-56`; (e) `frame-ancestors 'none'` `:71` · `form-action 'self'` `:73` |
| Kontrol | ADR-012 (CSP nonce + strict-dynamic); başlık set'i tek dizide — `Strict-Transport-Security` **bu dizide yok** (`:26-41`) |
| Kanıt | Yokluk da Dosya·Satır kanıtidir: 9+2 başlık sayımı `:26-41` okunarak yapıldı; HSTS satırı set'te bulunmaz (dogrulama AD-07/AD-14, bulgu B-2) |
| Durum | ⚠️ Kısmi — başlıklar ✓, HSTS eksik (R-09/R-15 ilişkili; HSTS ekleme ayrı aksiyon) |

### Ör-10 — Oturum → kimlik aktarım zinciri (sınıf S/I · R-09)

| Öğe | Değer |
|---|---|
| Yüzey | Session başlatma → `_session` → `_auth` → RBAC zinciri (tüm auth'lu uçlar) |
| Tehdit | Sınıf S (oturum sabitleme / sahte kimlik) + I (çerez hırsızlığı) — risk kaydı R-09 (AZALTILMIŞ, koşullu secure) |
| Dosya·Satır | (a) `SessionManagerMiddleware.php:17` `SessionBootstrapper::ensureStarted()`; (b) `:18` `startOrExtend(nonce)`; (c) `:20-21` `_csp_nonce` + `_session` request'e yazılır; (d) `AuthMiddleware.php:11-13` `_auth` init; (e) `:18-21` session anahtarları (`MM_UserID`, `MM_UserRole`, `MM_Permissions`); (f) `:23-31` `is_string($userId) && $userId !== ''` + RBAC payload aktarımı |
| Kontrol | Aktarım tek yönlüdür; AuthMiddleware **imza doğrulamaz** — oturumun güveni session katmanındadır (ADR-011 · riskler §2 R-09 `SessionInitializer.php·44-46`) |
| Kanıt | `AuthMiddleware.php:23` yalnızca biçim kontrolü yapar; oturum içeriği doğrulanmaz, taşınır → sınırlı yüzey bilgisi (bu örnek imza/çerez bayraklarını **kanıtlamaz**) |
| Durum | ⚠️ Yarı — aktarım ✓; cookie bayrakları ayrı kayıtta (dogrulama AD-08 · `Session/CLAUDE.md:49`) |

### Ör-11 — MiddlewarePipeline sıra + gözlem (sınıf R / §3.2-10 · R-07 bağlamı)

| Öğe | Değer |
|---|---|
| Yüzey | Middleware kayıt (pipe), sarma sırası ve timing ölçümü |
| Tehdit | Sınıf R (ölçüm/log yoksayma) + §3.2-10 (sıra değişirse bir kontrol atlar → yetkisiz geçiş) |
| Dosya·Satır | (a) `MiddlewarePipeline.php:17-21` `pipe()` ekleme; (b) `:32-34` `array_reverse` ile sarma (ilk pipe en dışta); (c) `:41-48` sınıf adı + `timing('MiddlewarePipeline', …)` ölçümü; (d) **frozen sıra testi**: `shared/tests/Middleware/MiddlewarePipelineTest.php:190-199` — `1_OriginCheck → 2_Cors → 3_RateLimiter → 4_SecurityHeaders → 5_SessionManager → 6_Csrf → 7_BypassAuth → 8_Auth → 9_Permission → 10_Validation` |
| Kontrol | Sıra regresyon testi + `shared/AGENTS.md` §4 kural 2 (middleware ekleme → `MiddlewarePipeline` kaydı gerekir) |
| Kanıt | Davranış kod satırında (`:32-34`); test dosyası varlığı **ek** kanıttır (anayasa Z-5 kuralı — test tek başına yeterli değil) |
| Durum | ✅ Kontrollü — sıra testle korunuyor (dogrulama AD-15); üretim pipe kaydı `PageRouterKernel.php:300` (`pipe()` döngüsü — bu örnekte satır içeriği okunmadı ⚠️) |

### Ör-12 — Redactor log maskeleme (sınıf I · R-08)

| Öğe | Değer |
|---|---|
| Yüzey | Tüm log yazma yolları (FileHandler, StructuredLogger, PageRouterKernel fatal) |
| Tehdit | Sınıf I (sır/parola/token log'a düşmesi) — risk kaydı R-08 (AZALTILMIŞ) |
| Dosya·Satır | (a) hassas anahtar listesi `shared/src/Log/Redactor.php:31` (`password`, `csrf_token`, `api_key`, `authorization`, `cookie` …); (b) key=value / JSON / Bearer deseni `:40`; (c) 64-char hex+base64 `:49`; (d) e-posta maskesi `:56`; (e) `redact()` akışı `:65-93` |
| Kontrol | Tek maskeleme noktası — docblock `:10-11`: "Tüm log yazma yolları … satırı BURADAN geçirir; tek maskeleme noktası"; `.ai/AGENTS.md` §17 Edge-3 (`[REDACTED]`) |
| Kanıt | Test kanıtı: `shared/tests/Unit/Log/LoggerRedactionTest.php:122-138` — 64-char hash maskelenir (`:127`), base64 maskelenir (`:134`), UUID korunur (`:137`) |
| Durum | ✅ Kontrollü — kod + test (dogrulama AD-17); maskeleme yalnız yazılan satırı etkiler (`:22-23`) |

---

## §3 ÖRNEK ÖZETİ

| # | Sınıf (anayasa §3/§3.2) | Dosya·Satır | Kontrol · R bağı |
|---|---|---|---|
| Ör-01 | 10 (misconfig) + D/E | `OriginCheckMiddleware.php:36-43,109-111` | env-gated dev-fallback + prod kapalı · R bağı YOK (§8'e göre kayıt) |
| Ör-02 | 10 (misconfig) | `CorsMiddleware.php:63-75` | allowlist-only yansıma (ADR-010/022) |
| Ör-03 | T (CSRF) + E | `CsrfMiddleware.php:15-17,42,49-63` | ADR-010: token + hash_equals + 403 · R-04, R-14 |
| Ör-04 | S + E; R | `BypassAuthMiddleware.php:29-31,49-54` | fail-closed + securityEvent · R-01 (AÇIK) |
| Ör-05 | D | `RateLimiterMiddleware.php:20-30,53-65,86-99` | auth fail-closed 503 · R-12, R-13 (KABUL) |
| Ör-06 | 8 (open redirect) + T | `ReturnUrlPolicy.php:102-181` | 5 katman open-redirect savunması · R-05 |
| Ör-07 | E (+ R gözlemi) | `PermissionMiddleware.php:23-42,47-62` | strict RBAC reddi + `authz_denied` securityEvent · R-14 |
| Ör-08 | T (+ I) | `ValidationMiddleware.php:22-31,41-80` | 422 şema doğrulaması; `required_fields` boşsa atlama · R-06 (KISMİ) |
| Ör-09 | I + T | `SecurityHeadersMiddleware.php:17-19,26-41,54-56,71-73` | 11 başlık + CSP nonce/strict-dynamic; **HSTS yok** · B-2, R-09/R-15 |
| Ör-10 | S + I | `SessionManagerMiddleware.php:17-21` · `AuthMiddleware.php:11-31` | oturum → `_auth` aktarımı (imza doğrulamaz — session katmanı) · R-09 |
| Ör-11 | R / §3.2-10 | `MiddlewarePipeline.php:17-34,41-48` · `MiddlewarePipelineTest.php:190-199` | pipe + reverse sarma + timing; frozen sıra testi · R-07 bağlamı |
| Ör-12 | I | `Redactor.php:31,40,49,56,65-93` · `LoggerRedactionTest.php:122-138` | tek nokta maskeleme + test kanıtı · R-08 |

**Kapsanmayan sınıflar** (kayıt yok = yüzey kanıtı yok, **bilinmeyen**): R (repudiation) — audit-log kapsamı risk defterinde R-07 (SIEM yok → D-32); §3.2-12 (bağımlılık CVE) — R-19 UNKNOWN; §3.2-7 (güvensiz serileştirme) — yüzey kanıtı yok (bkz web-arastirma §1 OWASP Deserialization).

> **Not (2026-10-15 tur-2):** Ör-07/Ör-11/Ör-12 sınıf **R/I gözlem** yüzeyini kanıtlar (securityEvent, timing, redaction) —
> bu, R-07'deki **SIEM/merkezî toplama** açık kalesini **kapatmaz**; risk defterindeki `R-07 KISMİ` durumu geçerlidir.
> Kapsam sınırları değişmez (kapsam §4: tehdit modeli ≠ risk defteri).

## §4 TUR-2 OKUMA KAYDI (2026-10-15 — Ör-07..Ör-12 kanıtlarının kaynağı)

| Dosya | Okuma | Kapsam |
|---|---|---|
| `shared/src/Middleware/` (glob) | 11 dosya / `IMiddleware` sınıfı 10 | envanter — `LoggingMiddleware.php`, `ApiVersionMiddleware.php` **0 glob isabeti (YOK)** |
| `PermissionMiddleware.php` | tam (64 satır) | Ör-07 |
| `ValidationMiddleware.php` | tam (84 satır) | Ör-08 |
| `AuthMiddleware.php` · `SessionManagerMiddleware.php` | tam (35 · 25 satır) | Ör-10 |
| `SecurityHeadersMiddleware.php` | tam (104 satır) | Ör-09 |
| `MiddlewarePipeline.php` · `tests/Middleware/MiddlewarePipelineTest.php:190-199` | tam · aralık | Ör-11 |
| `Log/Redactor.php` · `tests/Unit/Log/LoggerRedactionTest.php:122-138` | tam · aralık | Ör-12 |

Kural: bu tabloda bir dosya yoksa o dosyadan iddia üretilmez (anayasa T-11 / Y-03); okunmayan satır `⚠️ VERIFICATION REQUIRED` kalır.

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-surucu]] · [[threat-model-kapsam]] · [[threat-model-dogrulama]] · [[threat-model-bilesenler]] · [[threat-model-web-arastirma]] · [[index]]
- [[ADR-010-csrf-protection-strategy]] · [[security-riskler]] · [[security-bilesenler]] · [[session-security-kapsam]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
