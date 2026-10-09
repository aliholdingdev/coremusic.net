---
title: "K007 MIDDLEWARE «SUR» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K007-middleware/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: middleware
ssot: true
risk: medium
owner: security
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K007 MIDDLEWARE «SUR» — Katman Index

> **Authority:** Bu dosya K007 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K007 kartı > `.ai/CLAUDE.md` §5/§6 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K007 |
| Kanonik Ad | MIDDLEWARE |
| Teatral Epitet | «SUR» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2 — SOFTWARE PLANE K000→K15+) |
| Dizin deseni | `.ai/architecture/K007-middleware/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / middleware |
| Owner (`.ai/AGENTS.md` §4 registry) | security |
| Risk | medium — security yüzeyi var (istek-boru hattı = security OFİS yüzeyi: medium); K006 dışı high yok (R4.4) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-008 · ADR-010 · ADR-011 · ADR-012 · ADR-013 · ADR-016 · ADR-094 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K007 MIDDLEWARE «SUR», CoreMusic'in HTTP istek hattıdır: istek K006 SECURITY kararlarıyla
dolmuş olarak bu katmandan geçer, kontrollü bir şekilde K009/K010 uygulama katmanına devredilir.
Anayasa §6 pipeline'ı (OriginCheck → … → Validation → Controller) DEĞİŞTİRİLEMEZdir ve bu katmanın
ana sözleşmesidir; katman K006'yı ATLAYAMAZ (anayasa §5 K7 satırı + §6). Repo kanıtı var:
`shared/src/Middleware/` altında 11 PHP dosyası + `shared/tests/Middleware/` altında 9 test dosyası (ls 2026-10-08).
Kapsam dışı: iş mantığı (K008/K009), kimlik kararı (K006), denetim yazımı (K006/K012) — bu katman yalnız
istek geçişini yönetir.

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K007 | MIDDLEWARE | «SUR» | middleware | PHP 8.4 (PSR-15 · FPM · Apache/nginx) | Request Pipeline · Origin Check · CORS · Rate Limit · Security Headers · Session · PSR-15 (EK A §A.1 · 35 bileşen — anayasa §5 K7) | ham HTTP isteği · K6 kararları (JWT/session/RBAC) · APCu sayaç | Controller'a devredilen doğrulanmış istek · response header'ları (CSP nonce, HSTS) · 4xx/429 red | K000-K006 (alt katmanlar) + port/adapter (EK A aralık) | K007+ üst katmanlara doğrudan erişim (H20) · K006 doğrulamasını bypass (anayasa §5 K7) · katmanlar arası doğrudan veri paylaşımı (H19) | iş verisi YOK; yalnız session durumu + rate-limit sayacı (APCu) | K6 kararlarını UYGULAR, atlayamaz; CSP nonce üretimi #4'te, #5'te session'a yazılır (anayasa §6) | fail-secure (CSRF/red) · 429 rate-limit · 3600s session idle · kısa devre (pipeline stop) | pipeline log + correlation ID + rate-limit sayacı (metrik altyapısı PLANNED) | PHPUnit 11 sıra/sınır testleri — `shared/tests/Middleware/` (9 dosya, ls 2026-10-08) | repo: `shared/src/Middleware/` 11 dosya (ls 2026-10-08) · vault: `.ai/CLAUDE.md §6` + `00-kspace-anayasa.md §A.1 K007` · ADR: ADR-010/011/012/013/094 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B research kapısı) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ (kapı R4.4b) — izinli yalnız alt katman (K000-K006) +
port/adapter; yasak yalnız üst katman/geri çağrı/veri paylaşımı. Hedef ≠ kanıt ayrı yazılır (H10):
"35 bileşen" hedef/anayasa sayısıdır, "11 dosya" repo kanıtıdır — ikisi aynı cümlede birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K007 |
| 2 | KANONİK_AD | MIDDLEWARE |
| 3 | TEATRAL_EPİTET | «SUR» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | middleware |
| 5 | SUBDOMAIN | request-pipeline · cors-origin · rate-limit · security-headers · session · csrf · authn-inject · permission · validation |
| 6 | BOUNDED_CONTEXT | HTTP Request Gating — isteğin uygulamaya giriş kapısı; iş kuralı üretmez, güvenlik kararı K006'dan alır |
| 7 | RUNTIME | PHP 8.4 (strict_types) · PSR-15 middleware arayüzü · SAPI: FPM/Apache · APCu (rate-limit + cache) |
| 8 | SORUMLULUK | Request Pipeline (10 kademe immutable) · Origin Check · CORS · Rate Limit (60 req/60s) · Security Headers (CSP/HSTS/X-Frame) · Session (3600s idle) · PSR-15 uyumu · Request/DTO Validation |
| 9 | GIRDI | ham HTTP isteği + query/body/header · K006 çıkışı (JWT + session claim'leri) · K013'te üretilen yapılandırmalar (env/config) |
| 10 | CIKTI | Controller'a devredilmiş doğrulanmış Request/DTO · set edilmiş response header (CSP nonce, HSTS, CORS) · red durumunda 4xx/429 + hata sözleşmesi |
| 11 | IZINLI_BAGIMLILIK | K000 OS (APCu/process), K001-K005 alt katman erişimleri, K006 SECURITY (karar kaynağı), port/adapter (EK A aralık), PSR-15 (dış standard) |
| 12 | YASAK_BAGIMLILIK | K007 → K008..K020 doğrudan erişim (H20) · geri çağrı · K006'yı atlayarak Auth/Permission kararı · H19 doğrudan veri paylaşımı · üst katmanın iş mantığını middleware'e taşımak |
| 13 | DATA_BOUNDARY | İş verisi taşımaz/yorumlamaz; yalnız oturum durumu (session store) ve rate-limit sayacı (APCu). Kalıcı veriye yazmaz — audit yazımı K006/K012 alanıdır |
| 14 | SECURITY_BOUNDARY | K006'nın uygulama ayağı: CSP nonce üretimi SecurityHeaders (#4), SessionManager (#5) nonce'u session'a kaydeder; sıra değişirse CSP bozulur (anayasa §6 kritik not) · K6 ATLANAMAZ (§5 K7) · BypassAuth yalnız test (`?_bypass=1`), prod devre dışı (ADR-008) |
| 15 | FAILURE_MODE | fail-secure: doğrulama/red → istek durur (401/403/422/429); session idle 3600s → yeniden auth; rate-limit aşımı → 429; fail-open davranışı yasak (repo testi: BypassAuthMiddlewareFailClosedTest) |
| 16 | OBSERVABILITY | pipeline seviye logları (gelen/Red/latency) · correlation ID devri (gateway üretir — anayasa §6A.1) · rate-limit sayaçları; merkezi metrik/trace hedefi (Prometheus) PLANNED |
| 17 | TEST | PHPUnit 11 — `shared/tests/Middleware/`: MiddlewarePipelineTest · OriginCheck · RateLimiter · SessionManager · Csrf · Auth · BypassAuth + FailClosed · Validation (9 dosya ls 2026-10-08); hedef ≥80% (anayasa §17) |
| 18 | KANIT | repo: `shared/src/Middleware/{MiddlewarePipeline,OriginCheckMiddleware,CorsMiddleware,RateLimiterMiddleware,SecurityHeadersMiddleware,SessionManagerMiddleware,CsrfMiddleware,BypassAuthMiddleware,AuthMiddleware,PermissionMiddleware,ValidationMiddleware}.php` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §6` (pipeline tablosu) + `00-kspace-anayasa.md §A.1 K007` · ADR: ADR-010/011/012/013/008/094 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «SUR» — koruyucu, geçidi temsil eden fiziksel metafor; 1 epitet, K-ID'nin önünde değil yanında (R2.3); EK A §A.1 anahtar satırı: `K007 · MIDDLEWARE · «SUR» · SOFTWARE` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu ✓ · (b) IZINLI ∩ YASAK = ∅ ✓ · (c) KANIT 3'lü format (repo | vault/ADR | web ⚠️) ✓ —
`⚠️` olan web ayağı R14 research kapısında doldurulur · (d) veri sınırı tek katmana ait (K007 yalnız oturum/sayaç
durumu; audit K006/K012'ye devredilir) ✓.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K007 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Request Pipeline · Origin Check · CORS · Rate
Limit · Security Headers · Session · PSR-15".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Request Pipeline | Middleware'leri sırayla yürütür; kısa devre (short-circuit) ile reddeder | IMPLEMENTED (repo) | `shared/src/Middleware/MiddlewarePipeline.php` + `shared/tests/Middleware/MiddlewarePipelineTest.php` (ls 2026-10-08) |
| Origin Check | İstek kökenini (Origin/host) doğrular; whitelist CORS reddi | IMPLEMENTED (repo) | `shared/src/Middleware/OriginCheckMiddleware.php` + `OriginCheckMiddlewareTest.php` · ADR-094 (accepted/ ls) |
| CORS | CORS header'larını yönetir (allow-origin/method/header) | IMPLEMENTED (repo) · test dosyası GÖZLENMEDİ (gap) | `shared/src/Middleware/CorsMiddleware.php` (ls 2026-10-08); `CorsMiddlewareTest.php` ls'te yok → ⚠️ |
| Rate Limit | APCu tabanlı 60 req/60s; Timeout 60s | IMPLEMENTED (repo) | `shared/src/Middleware/RateLimiterMiddleware.php` + `RateLimiterMiddlewareTest.php` · ADR-013 (accepted/ ls) · `.ai/CLAUDE.md §6 #3` |
| Security Headers | CSP strict-dynamic + nonce, X-Frame-Options, HSTS; nonce üretimi bu adımda | IMPLEMENTED (repo) · SecurityHeaders test dosyası GÖZLENMEDİ (gap) | `shared/src/Middleware/SecurityHeadersMiddleware.php` (ls) · ADR-012 (accepted/ ls) · `.ai/CLAUDE.md §6 #4 + kritik not` |
| Session | Oturumu başlatır (3600s idle), CSP nonce'u session'a kaydeder | IMPLEMENTED (repo) | `shared/src/Middleware/SessionManagerMiddleware.php` + `SessionManagerMiddlewareTest.php` · ADR-011 (accepted/ ls) · `.ai/CLAUDE.md §6 #5` |
| PSR-15 | Middleware'lerin standart arayüzle (request handler zinciri) yazılması | IMPLEMENTED (tasarım) — EK A sorumluluğu; arayüz kullanımı pipeline yapısıyla uyumlu · PSR-15 doğrudan grep kanıtı: ⚠️ | `00-kspace-anayasa.md §A.1 K007` (Sorumluluk satırı) · `shared/src/Middleware/MiddlewarePipeline.php` (yapı) · ⚠️ VERIFICATION REQUIRED (PSR-15 import grep'i yapılmadı) |

### §4.2 Anayasa §5 K-Matrix Satırı (K7) — 35 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K7 Middleware | OriginCheck, CORS, RateLimit,
SecurityHeaders, Session, CSRF, PSR-15 | 35 bileşen | K6 güvenlik doğrulamasını atlayamaz.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| OriginCheck | Köken doğrulama (whitelist CORS) | IMPLEMENTED | `shared/src/Middleware/OriginCheckMiddleware.php` (ls 2026-10-08) · `.ai/CLAUDE.md §6 #1` |
| CORS | CORS header yönetimi | IMPLEMENTED | `shared/src/Middleware/CorsMiddleware.php` (ls) · `.ai/CLAUDE.md §6 #2` |
| RateLimit | APCu 60 req/60s, timeout 60s | IMPLEMENTED | `RateLimiterMiddleware.php` (ls) · ADR-013 · `.ai/CLAUDE.md §6 #3` |
| SecurityHeaders | CSP strict-dynamic + nonce, X-Frame-Options, HSTS | IMPLEMENTED | `SecurityHeadersMiddleware.php` (ls) · ADR-012 · `.ai/CLAUDE.md §6 #4` |
| Session | Oturum + CSP nonce kaydı, 3600s idle | IMPLEMENTED | `SessionManagerMiddleware.php` (ls) · ADR-011 · `.ai/CLAUDE.md §6 #5` |
| CSRF | `csrf_token` doğrulama (POST/PUT/DELETE) — `_csrf_token` yasak (Guardrail #6) | IMPLEMENTED | `CsrfMiddleware.php` + `CsrfMiddlewareTest.php` (ls) · ADR-010 · `.ai/CLAUDE.md §6 #6` |
| PSR-15 | Middleware arayüz standardı | IMPLEMENTED (yapı) / grep kanıtı ⚠️ | `.ai/CLAUDE.md §5 K7 satırı` · `00-kspace-anayasa.md §A.1 K007` · ⚠️ |
| "35 bileşen" sayımı | §5 K7 bileşen sayısı (hedef/envanter) — üst-kademeli toplam | HEDEF (anayasa envanteri) · alt-bileşen dökümü ⚠️ | `.ai/CLAUDE.md §5 K7 satırı` · H10: hedef ≠ kanıt — repo'da 11 middleware dosyası sayıldı (ls) |

### §4.3 Anayasa §6 Middleware Pipeline (Immutable — ADR-010/011/012/013/022) — gerçek hat

```text
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf →
BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev (anayasa §6) | Timeout | Repo dosyası (ls 2026-10-08) | Test dosyası | Durum |
|---|---|---|---|---|---|---|
| 1 | OriginCheck | Köken doğrulama (whitelist CORS) | — | OriginCheckMiddleware.php | OriginCheckMiddlewareTest.php | IMPLEMENTED |
| 2 | Cors | CORS header yönetimi | — | CorsMiddleware.php | (gözlenmedi → gap ⚠️) | IMPLEMENTED / test gap |
| 3 | RateLimiter | APCu tabanlı, 60 req/60s | 60s | RateLimiterMiddleware.php | RateLimiterMiddlewareTest.php | IMPLEMENTED |
| 4 | SecurityHeaders | CSP strict-dynamic, X-Frame-Options, HSTS — **nonce üretimi burada** | — | SecurityHeadersMiddleware.php | (gözlenmedi → gap ⚠️) | IMPLEMENTED / test gap |
| 5 | SessionManager | Session başlatır, **CSP nonce'u session'a kaydeder** | 3600s idle | SessionManagerMiddleware.php | SessionManagerMiddlewareTest.php | IMPLEMENTED |
| 6 | Csrf | `csrf_token` doğrulama (POST/PUT/DELETE) | — | CsrfMiddleware.php | CsrfMiddlewareTest.php | IMPLEMENTED |
| 7 | BypassAuth | Test bypass (`?_bypass=1`), prod'da devre dışı | — | BypassAuthMiddleware.php | BypassAuthMiddlewareTest.php + BypassAuthMiddlewareFailClosedTest.php | IMPLEMENTED (fail-closed testli) |
| 8 | Auth | Auth bilgisi inject (JWT + Session) | — | AuthMiddleware.php | AuthMiddlewareTest.php | IMPLEMENTED |
| 9 | Permission | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | — | PermissionMiddleware.php | (gözlenmedi → gap ⚠️) | IMPLEMENTED / test gap |
| 10 | Validation | Request/DTO validasyonu | — | ValidationMiddleware.php | ValidationMiddlewareTest.php | IMPLEMENTED |

**Kritik not (anayasa §6 bağlayıcı):** CSP nonce üretimi #4 SecurityHeaders içindedir; #5
SessionManager bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur; middleware sırası
**DEĞİŞTİRİLEMEZ** (Guardrail #7 — Middleware Order Immutable).

### §4.3.1 Kademe 1 · OriginCheck (köken doğrulama)

| Boyut | İçerik |
|---|---|
| Kural | Whitelist dışındaki Origin/host istekleri CORS yanıtı almadan reddedilir (anayasa §6 #1) |
| Edge case | Preflight (OPTIONS) vs actual istek ayrımı; eksik Origin başlığı (aynı-origin istek) davranış politikası |
| Red davranışı | 403 köken reddi; log satırı (K012 girdisi) |
| Kanıt | `shared/src/Middleware/OriginCheckMiddleware.php` + `OriginCheckMiddlewareTest.php` (ls 2026-10-08) · ADR-094 (accepted/ ls — origin/csrf API pipeline) |
| İlişki | K009 gateway CORS config'i ile hizalı; whitelist kaynağı K006/K009 yapılandırmasıdır |

### §4.3.2 Kademe 2 · Cors (CORS header yönetimi)

| Boyut | İçerik |
|---|---|
| Kural | CORS başlıkları yalnız onaylı origin'ler için yazılır; `Access-Control-Allow-*` seti dar tutulur (anayasa §6 #2) |
| Edge case | `credentials: true` ile wildcard `*` birlikte YASAK (standart ihlali); preflight cache süresi |
| Red davranışı | Whitelist dışı → header yazılmaz → tarayıcı isteği bloklar |
| Kanıt | `shared/src/Middleware/CorsMiddleware.php` (ls 2026-10-08) · test dosyası GÖZLENMEDİ → ⚠️ (R9: 1/3 → UNKNOWN kapsamı test) |
| İlişki | OriginCheck (#1) ile çift katman: #1 reddeder, #2 yalnız onaylıya header yazar |

### §4.3.3 Kademe 3 · RateLimiter (APCu 60 req/60s)

| Boyut | İçerik |
|---|---|
| Kural | APCu tabanlı sayaç, 60 istek / 60 saniye; Timeout 60s (anayasa §6 #3 · §12 "APCu 60 req/60s") |
| Edge case | APCu'nun istek-başına paylaşımlı doğası (FPM worker ayrımı); sayaç anahtarının IP+session kırılımı |
| Red davranışı | 429 Too Many Requests + Retry-After; sayacı aşan istek Controller'a ulaşmaz |
| Kanıt | `RateLimiterMiddleware.php` + `RateLimiterMiddlewareTest.php` (ls) · ADR-013 (accepted/ ls) |
| İlişki | K006 Rate Limiting kararı (§5 K6 satırı) burada uygulanır; K012 metrik üretimi için sayaç dışa aktarımı PLANNED |

### §4.3.4 Kademe 4 · SecurityHeaders (CSP/HSTS/X-Frame — nonce üretimi)

| Boyut | İçerik |
|---|---|
| Kural | CSP `strict-dynamic` + nonce, X-Frame-Options, HSTS (anayasa §6 #4); **nonce ÜRETİMİ bu kademede** |
| Edge case | Sıra ihlali (#4 sonra #5 gelmezse) → nonce session'a yazılmaz → CSP sayfayı kırar (§6 kritik not) |
| Red davranışı | İstek devam eder; başlıklar response'a yazılır (red değil, zorlama katmanı) |
| Kanıt | `SecurityHeadersMiddleware.php` (ls) · ADR-012 csp-nonce-strict-dynamic (accepted/ ls) · `.ai/CLAUDE.md §6 kritik not` |
| İlişki | Nonesu `strict-dynamic` ile `unsafe-inline` yerine geçer; K011 UX bu nonce'a uymak zorunda (script-src) |

### §4.3.5 Kademe 5 · SessionManager (oturum + nonce kaydı)

| Boyut | İçerik |
|---|---|
| Kural | Session başlatılır, CSP nonce'u session'a kaydedilir; idle timeout 3600s (anayasa §6 #5) |
| Edge case | Süre dolumu → otomatik yeniden auth (anayasa §22 "Session timeout 3600s"); multi-tab CSRF = session-bound tek token (ADR-010) |
| Red davranışı | Süresi dolan istek Auth kademesinde 401 → K009 hata sözleşmesi |
| Kanıt | `SessionManagerMiddleware.php` + `SessionManagerMiddlewareTest.php` (ls) · ADR-011 (accepted/ ls) |
| İlişki | #4'ün çıktısını (nonce) alır; #8 Auth bu oturumu okur |

### §4.3.6 Kademe 6 · Csrf (`csrf_token` doğrulama)

| Boyut | İçerik |
|---|---|
| Kural | POST/PUT/DELETE gövdelerinde `csrf_token` doğrulanır; `_csrf_token` adı YASAK (Guardrail #6 · ADR-010) |
| Edge case | Multi-tab: aynı token her sekmede geçerli mi (session-bound tek token — anayasa §22) |
| Red davranışı | Geçersiz token → 403; iş emri hiç çalışmaz |
| Kanıt | `CsrfMiddleware.php` + `CsrfMiddlewareTest.php` (ls) · ADR-010 (accepted/ ls) · ADR-094 (accepted/ ls) |
| İlişki | Güvenlik duvarı son savunma hattı K006'dan önce; K006 auditor red olayını K012'ye iletir |

### §4.3.7 Kademe 7 · BypassAuth (test bypass — prod devre dışı)

| Boyut | İçerik |
|---|---|
| Kural | `?_bypass=1` yalnız test ortamında etkin; prod'da tamamen devre dışı (anayasa §6 #7 · Soft Constraint #4) |
| Edge case | Prod'da bayrak gelirse: fail-closed (reddet, sessizce geçme) — repo testi bu davranışı kilitler |
| Red davranışı | Prod: bayrak yok sayılır; auth akışı normal devam eder |
| Kanıt | `BypassAuthMiddleware.php` + `BypassAuthMiddlewareTest.php` + `BypassAuthMiddlewareFailClosedTest.php` (ls 2026-10-08) · ADR-008 (accepted/ ls) |
| İlişki | Yalnız BypassAuth şarta bağlıdır; diğer 9 kademe şartsızdır (sıra değişmez) |

### §4.3.8 Kademe 8 · Auth (JWT + session bilgisi inject)

| Boyut | İçerik |
|---|---|
| Kural | Auth bilgisi isteğe inject edilir: hybrid JWT + session (anayasa §6 #8 · §6A.1 gateway) |
| Edge case | Token süresi dolu + refresh yok → 401; yetkisiz ama kimlikli → #9 Permission'a devir |
| Red davranışı | Kimliksiz korumalı rota → 401 (Controller'a asla ulaşmaz) |
| Kanıt | `AuthMiddleware.php` + `AuthMiddlewareTest.php` (ls) · ADR-052 hybrid-auth-session-jwt · ADR-095 hybrid-jwt-rs256 (accepted/ ls) |
| İlişki | Kimlik kaynağı K006 (JWT/RS256 — ADR-095); K007 yalnız taşır ve doğrular |

### §4.3.9 Kademe 9 · Permission (RBAC — 6 rol)

| Boyut | İçerik |
|---|---|
| Kural | RBAC kontrolü: regular / premium / studio / car / admin / system (anayasa §6 #9) |
| Edge case | Rol eksikliği, rol-quarter süresi (premium bitişi), panel-bazlı yetki (K010 panelleri) |
| Red davranışı | Yetkisiz → 403; K010'da rol-uyumsuz panel isteği API'de kesilir |
| Kanıt | `PermissionMiddleware.php` (ls) · `.ai/CLAUDE.md §6 #9` · test dosyası GÖZLENMEDİ → ⚠️ |
| İlişki | Yetki modeli K006 RBAC; rolenvanteri anayasa §6 satırı |

### §4.3.10 Kademe 10 · Validation (Request/DTO)

| Boyut | İçerik |
|---|---|
| Kural | Request/DTO validasyonu; hatalı giriş 422 (anayasa §6 #10 · §6A.1 "validation" gateway görevi) |
| Edge case | İdempotent olmayan gövde + kısmi DTO; enum/şema ihlali; dosya yükleme boyut sınırı |
| Red davranışı | 422 + alan-bazlı hata listesi (K009 Error Contract'a devir) |
| Kanıt | `ValidationMiddleware.php` + `ValidationMiddlewareTest.php` (ls) |
| İlişki | Çıktı (doğrulanmış DTO) K009/K010 use-case'inin tek meşru girdisidir (§6A.5 SPA → ApiClient kuralı) |

### §4.3.11 Kademe 11 · Controller (devir noktası — K009/K010)

| Boyut | İçerik |
|---|---|
| Kural | 10 kademeyi geçen istek Controller/use-case'e devredilir (anayasa §6 sonu) |
| Sınır | K007'nin bittiği yer: buradan sonrası iş mantığı — K007'nin veri/çalışma yetkisi kalmaz |
| Kanıt | `.ai/CLAUDE.md §6` pipeline sonu · `shared/src/Middleware/MiddlewarePipeline.php` (devir yapısı) |

### §4.4 Ek Kalemler (pipeline dışı K007 kapsamı)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| BypassAuth politikası | Test ortamında `?_bypass=1`; prod'da devre dışı (Soft Constraint #4) | IMPLEMENTED | `BypassAuthMiddleware.php` + 2 test (ls) · ADR-008 (accepted/ ls) · `.ai/CLAUDE.md §8 #4` |
| Pipeline kısayol/red sözleşmesi | Red → 401/403/422/429; hata gövdesi K009 hata sözleşmesine devredilir | IMPLEMENTED (davranış) / hata sözleşmesi sahibi K009 | `MiddlewarePipeline.php` (ls) · `.ai/CLAUDE.md §6A` · anayasa §A.1 K009 "Error Contract" |
| Correlation ID devri | Gateway (K009) üretir; middleware logger'a geçirir | PLANNED (K009'da tanımlı; K007 logger devri grep edilmedi → ⚠️) | `.ai/CLAUDE.md §6A.1` · ⚠️ VERIFICATION REQUIRED |
| URL normalizasyonu / temiz URL | Çift slash, trailing slash, query temizliği | Kapsam kararı ⚠️ (ADR-016 accepted'te var; middleware'de uygulaması grep edilmedi) | ADR-016 (accepted/ ls) · ⚠️ |
| Katman sırası denetimi | Pipeline sıra testi (immutable sözleşme) | IMPLEMENTED | `shared/tests/Middleware/MiddlewarePipelineTest.php` (ls 2026-10-08) |

### §4.5 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K7 hedefi | 35 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (ls 2026-10-08) | 11 middleware dosyası + 9 test dosyası |
| Pipeline kademesi kapsama | 10/10 kademenin repo dosyası var ✓ |
| Test gap'leri | Cors · SecurityHeaders · Permission test dosyaları ls'te gözlenmedi → ⚠️ |
| PSR-15 import doğrulaması | ⚠️ VERIFICATION REQUIRED (grep yapılmadı) |

### §4.6 K007 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K007 etkisi |
|---|---|---|
| ADR-008-bypass-auth-middleware | Test bypass (`?_bypass=1`) stratejisi | Kademe #7 davranış kilidi (fail-closed testli) |
| ADR-010-csrf-protection-strategy | Token adı `csrf_token`; session-bound tek token | Kademe #6 + multi-tab edge case |
| ADR-011-session-management | Oturum yaşam döngüsü, 3600s idle | Kademe #5 timeout + yeniden auth |
| ADR-012-csp-nonce-strict-dynamic | CSP nonce + strict-dynamic | Kademe #4 nonce üretimi — §6 kritik notun temeli |
| ADR-013-rate-limiting-apcu | APCu 60 req/60s | Kademe #3 sayaç politikası |
| ADR-016-url-normalization | Temiz URL davranışı | Kapsam sınırı ⚠️ (middleware uygulaması grep edilmedi) |
| ADR-094-api-pipeline-origin-csrf | Origin + CSRF API pipeline birleşimi | Kademe #1/#6 sözleşmesi (K009 ile ortak) |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi · dizin deseni · hibrit kayıt | Bu dosyanın format ve bağımlılık yönü kaynağı |

### §4.7 K007 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K007'nin verdiği | K007'nin beklendiği | Kanıt |
|---|---|---|---|---|
| K006 SECURITY | karar/claim girdisi | doğrulanmış kimlik (JWT + session) için kararları K006'dan alır | kimlik/token emniyeti (RS256 — ADR-095) | `.ai/CLAUDE.md §6 #8` · ADR-095 (accepted/ ls) |
| K009 API | gateway sözleşmesi | doğrulanmış Request/DTO + header seti (CSP/HSTS/CORS) | correlation ID üretimi + error contract'ı | `.ai/CLAUDE.md §6A.1` · anayasa §A.1 K009 |
| K010 APPLICATION | korumalı rota | yalnız yetkili ve doğrulanmış istek Controller'a ulaşır | `csrf_token` gönderimi (SPA ApiClient üzerinden) | `.ai/CLAUDE.md §9` + §6A.5 · §5 K10 satırı ("Yalnızca K9 API") |
| K011 UX | CSP nonce yükümlülüğü | sayfaya `nonce`'lu script/CSS kabulü | satır içi script YASAĞI'na uyum (strict-dynamic) | ADR-012 (accepted/ ls) · `.ai/CLAUDE.md §6 #4` |
| K012 OBSERVABILITY | log/olay akışı | red/olay satırları + sayaç okuması | merkezi metrik/trace omurgası (PLANNED) | `rules.md R6.2` · `.ai/CLAUDE.md §5 K12 satırı` |
| K013 CI/CD | test kapısı | pipeline sıra testleri + statik analiz girdisi | `--check`/test çalıştırma zorunluluğu | `shared/tests/Middleware/` (ls) · `.github/workflows/ci.yml` (ls) |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000 OS | aşağı | APCu/PHP runtime, process/signal yönetimi | `00-kspace-anayasa.md §A.1 K007` "izinli=K000-K006" |
| K001-K005 (HARDWARE → DATA) | aşağı | alt katman erişim aralığı; K005'e doğrudan SQL YOK — repository/port üzerinden | anayasa §A.1 K007 · `.ai/CLAUDE.md §5.1` (L6→L0 izinli) |
| K006 SECURITY | aşağı (karar kaynağı) | Auth/Permission/CSRF/CSP kararları K006'dan gelir; middleware yalnız uygular | `.ai/CLAUDE.md §5 K7` "K6 güvenlik doğrulamasını atlayamaz" + §6 |
| port/adapter | yan | EK A "yalnız port/adapter" istisnası (sıçrama kuralı — R6.3) | `00-kspace-anayasa.md §A.1 K007 Sınır satırı` |
| PSR-15 (dış standard) | dış | middleware arayüz sözleşmesi | `.ai/CLAUDE.md §5 K7` · anayasa §A.1 |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K007 → K008-K020 doğrudan erişim / geri çağrı (H20) | Klasik yön: izinli = alt katman + port/adapter; senkron çağrı yukarı yasak | `rules.md R6.1` · `ADR-096 §2 (Dependency yönü)` · `.ai/CLAUDE.md §5.1` (Layer Violation → revert + CRITICAL log) |
| K006 doğrulamasını bypass etmek | Hard Guardrail: K6 asla bypass edilemez; middleware K6'nın uygulama ayağıdır | `.ai/CLAUDE.md §5 K6/K7 satırları` + §6 |
| Katmanlar arası doğrudan veri paylaşımı (H19) | Veri sınırı ihlali (R4.4d) | `rules.md R6.1` · `ADR-096 §2` |
| Middleware'de iş mantığı (use-case) | Karışık sorumluluk; K007 yalnız gating | `00-kspace-anayasa.md §A.1 K007 Sorumluluk` (kapsam: pipeline gating) |
| `SELECT *` / ORM / framework kullanımı | ADR-001/002 mutlak yasakları | `rules.md R17` · ADR-001, ADR-002 (accepted/ ls) |

### §5.3 Boundary Matrisi

| Boundary | K007 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | Session store + APCu sayacı; kalıcı iş verisi yok | K005 (DB) · K012 (log havuzu) |
| SECURITY_BOUNDARY | K6 kararlarının istek hattı uygulaması; nonce üretimi #4, kaydı #5; K6 atlanamaz | K006 (karar) · K009 (gateway auth akışı) |
| FAILURE_MODE | fail-secure: red → 4xx/429; fail-open yasak; session idle 3600s | K006 (fail-secure genel) · K012 (olay logu) |
| RUNTIME boundary | PHP-FPM istek yaşam döngüsü; 60s rate-limit / 3600s session zaman aşımları | K000 (runtime) · K010 (30s/60s timeout soft constraint) |
| Olay (event) yukarı serbest | Middleware red/olaylarını K012'ye log/olay olarak YUKARI yayınlayabilir; senkron geri çağrı yasak (R6.2) | K012 OBSERVABILITY |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay/log yayını, yukarı SERBEST)  ←  K007 middleware red/olayları
      ↑                                                          │
      │ (okuma)                                        [senkron çağıramaz — H20]
      └──────────── K008 SERVICES ─────────────────────┘
                         │
        K007 yalnız alttan beslenir: K006 kararı + K000 runtime  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| Middleware → K012 log/olay | yukarı | Olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| Middleware → K008 senkron çağrı | — | YASAK (H20 geri çağrı) | `rules.md R6.1` · `ADR-096 §2` |
| K006 → K007 karar akışı | aşağı | JWT/RBAC/CSRF kararları alttan gelir | `.ai/CLAUDE.md §6` |
| K007 → Controller (K009/K010) | devir | Sıranın meşru sonu; istek tek yönlü | `.ai/CLAUDE.md §6` |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası (pipeline boyunca)

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| OriginCheck | ham istek (Origin/host) | köken kararı (devret/red) | whitelist K006/K009 config'inden |
| Cors | preflight/actual istek | CORS header'ları | origin çaprazı yalnız whitelist |
| RateLimiter | client anahtarı (IP/session) | sayaç ±1; 60/60s üstü → 429 | APCu; iş verisi yok |
| SecurityHeaders | istek bağlamı | CSP/HSTS/X-Frame header + nonce | nonce → #5'e devredilir |
| SessionManager | cookie/session ID | oturum + nonce kaydı; 3600s idle | kimlik verisi K006 alanından |
| Csrf | POST/PUT/DELETE gövdesi | `csrf_token` doğrulama sonucu | Guardrail #6: ad `csrf_token` |
| BypassAuth | `?_bypass=1` + ortam bayrağı | test bypass (prod: pasif) | fail-closed (repo testi) |
| Auth | JWT + session | auth bilgisi inject | hybrid JWT — ADR-052/095 (accepted/ ls) |
| Permission | kullanıcı rolü | RBAC kararı (6 rol) | `.ai/CLAUDE.md §6 #9` |
| Validation | request/DTO | doğrulanmış DTO / 422 | K010/K009 DTO sözleşmesi |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Dil/SAPI | PHP 8.4 (strict_types) · FPM/Apache · port 80/81 (§11 Port Register) | `.ai/CLAUDE.md §12` · §11 |
| Cache/sayaç | APCu (RateLimiter 60 req/60s; CacheManager zinciri) | `.ai/CLAUDE.md §6 #3` · §12 |
| Zaman aşımları | RateLimit 60s · Session idle 3600s | `.ai/CLAUDE.md §6 #3/#5` |
| Standart | PSR-15 middleware arayüzü | `.ai/CLAUDE.md §5 K7` (⚠️ import grep'i yok) |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Pipeline log (gelen/Red) | `shared/src/Middleware/*` + `shared/src/PageRouter/StructuredLogger.php` (yapısal log) | IMPLEMENTED (logger altyapısı) · middleware'e özel log satırları grep edilmedi → ⚠️ |
| Correlation ID | gateway (K009) üretimi → middleware devri | PLANNED (§6A.1) · ⚠️ |
| Rate-limit metriği | APCu sayaç dışa aktarımı | PLANNED (Prometheus yok — K012 §'si) |
| Audit olayı | CSRF/Auth red → audit zinciri (K006) | K006 alanı; K007 yalnız üretici |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| Middleware birim/sınır | PHPUnit 11 | `shared/tests/Middleware/` 9 dosya · `shared/phpunit.xml` (ls 2026-10-08) | ≥80% (anayasa §17), hedef ≥90% |
| Sıra sözleşmesi | MiddlewarePipelineTest | repo (ls) | immutable sıra + kısa devre |
| Fail-closed | BypassAuthMiddlewareFailClosedTest | repo (ls) | prod bypass kapalı |
| E2E (pipeline üstü) | Playwright (`assets.coremusic.net/playwright.config.ts`) | repo (ls) | KAPI 9 / §11 kapıları |

### §6.5 Failure Mode Senaryoları (failure=fail-over · fail-secure)

| # | Senaryo | K007 davranışı | Kullanıcı etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Geçersiz `csrf_token` (POST) | Kademe #6 → 403, iş emri çalışmaz | Form/aksiyon reddi | `CsrfMiddlewareTest.php` (ls) · ADR-010 |
| 2 | 60s penceresinde 60+ istek | Kademe #3 → 429 + Retry-After | Geçici yavaşlama/uyarı | `RateLimiterMiddlewareTest.php` · ADR-013 |
| 3 | Oturum 3600s idle doldu | #5 oturumu kapatır → #8 401 | Yeniden giriş (otomatik redirect beklenir — K009/K010) | anayasa §6 #5 · §22 satırı |
| 4 | Whitelist dışı Origin | #1 reddeder / #2 header yazmaz | Tarayıcı isteği engellenir | `OriginCheckMiddlewareTest.php` |
| 5 | Prod'da `?_bypass=1` | #7 fail-closed: bypass yok, normal auth | Etkisiz (davranış değişmez) | `BypassAuthMiddlewareFailClosedTest.php` |
| 6 | Sıra değişikliği (nonce #4↔#5 ayrışır) | CSP nonce eşleşmez → sayfa script'leri reddedilir | UI kırılır | `.ai/CLAUDE.md §6 kritik not` · Guardrail #7 |
| 7 | Yetkisiz rol (RBAC dışı rota) | #9 → 403 | Erişim reddi | `.ai/CLAUDE.md §6 #9` · test gap ⚠️ |
| 8 | APCu/memcache erişilemez | Rate-limit/LRU davranışı belirsiz → fail-open riski | Sınır koruması zayıflayabilir | ⚠️ VERIFICATION REQUIRED (davranış grep edilmedi) |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K007 durumu |
|---|---|
| KAPI 1 vault oku | Tam — anayasa §A.1 + §5/§6 + rules.md + ADR-096 okundu (bu üretim) |
| KAPI 9 hallucination damgası | `⚠️` işaretli ayağlar §7'de listelendi (PSR-15 grep, Cors/Security/Permission testleri, correlation, APCu fail davranışı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #6 (`csrf_token`) | Uygun — `CsrfMiddleware.php` + testi var; `_csrf_token` kullanımı grep edilmedi → ⚠️ |
| Guardrail #7 (sıra immutable) | Uygun — pipeline testi var (`MiddlewarePipelineTest.php`) |
| Guardrail #9/#10 (ORM/framework yasak) | K007'de ORM kullanımına rastlanmadı (dosya adları/kapsam) · tam grep ⚠️ |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | Pipeline kapsamı | 10/10 kademenin repo dosyası | ✓ (ls 2026-10-08) |
| K4 | Test alt yapısı | her kademe için asgari 1 test | 7/10 ✓ · 3 gap (G2-G4) |
| K5 | K6 atlanamaz kanıtı | BypassAuth fail-closed testi | ✓ (`BypassAuthMiddlewareFailClosedTest.php`) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | ✗ → ⚠️ (G8, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K007 - MIDDLEWARE «SUR»` (+ §A.0 anahtar satırı) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt satırları — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K7 satırı: 35 bileşen · K6 atlanamaz) · `§6` (pipeline + kritik nonce notu) · `§11` (port) · `§12` (stack) · `§17` (test) | dosya yolu (vault read 2026-10-08) | K-matrix + pipeline açılımı |
| 3 | `shared/src/Middleware/*.php` (11 dosya) · `shared/tests/Middleware/*Test.php` (9 dosya) | repo grep/ls (2026-10-08) | IMPLEMENTED durumları |
| 4 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-008 · ADR-010 · ADR-011 · ADR-012 · ADR-013 · ADR-016 · ADR-094 · ADR-096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2.4/§2.5` | dosya yolu (vault read) | format + bağımlılık yönü |
| 6 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |
| 7 | PSR-15 import grep'i · middleware log satırları · correlation ID uygulaması | ⚠️ VERIFICATION REQUIRED | R14 research kapısında doldurulur |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | PSR-15 arayüz import/uygulama grep'i yapılmadı | EK C alan 11/18 (IZINLI/KANIT) | repo grep → `shared/src/Middleware/*.php` içinde `Psr\Http\Server` araması |
| G2 | `CorsMiddleware` test dosyası ls'te gözlenmedi | §4.1/§4.3 test sütunu | `shared/tests/Middleware/` teyidi + yoksa test açma önerisi (K013) |
| G3 | `SecurityHeadersMiddleware` test dosyası ls'te gözlenmedi | §4.3 #4 test sütunu | aynı |
| G4 | `PermissionMiddleware` test dosyası ls'te gözlenmedi | §4.3 #9 test sütunu | aynı |
| G5 | `_csrf_token` (yasak ad) grep'i yapılmadı | Guardrail #6 uyum iddiası | repo grep → 0 isabet beklenir |
| G6 | Correlation ID'nin middleware logger'a devri | EK C alan 16 (OBSERVABILITY) | K009 gateway kodunda correlation üretimi + logger entegrasyonu grep'i |
| G7 | APCu erişilemezliği durumunda rate-limit davranışı | EK C alan 15 (FAILURE_MODE) | kod okuma → fail-open/fail-closed kararı + test |
| G8 | Web kanıtı (URL+tarih) — pipeline/domain iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) → yeni iddialar EK B'ye eklenir |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI».
Üst/yasak yön (H20): K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» ·
K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL». İlişki türü: kardeş katmanlar arası yalnız
`refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K007-middleware.md | K007 MIDDLEWARE «SUR» | bu dosya (draft) |
| b1-K008-servisler.md | K008 SERVICES «KULE» | band-1 setinin parçası |
| b1-K009-api.md | K009 API «HÜCRE» | band-1 setinin parçası |
| b1-K010-uygulama.md | K010 APPLICATION «DÜĞÜM» | band-1 setinin parçası |
| b1-K011-ux.md | K011 UX «OMURGA» | band-1 setinin parçası |
| b1-K012-izleme.md | K012 OBSERVABILITY «AYNA» | band-1 setinin parçası |
| b1-K013-cicd.md | K013 CI/CD «PUSULA» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.