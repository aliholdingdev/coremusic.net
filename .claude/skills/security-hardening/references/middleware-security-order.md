# Middleware Security Pipeline — Sıra, İmmutability & CSP Nonce Zinciri

> Kaynak: `.ai/CLAUDE.md` §6 (Immutable — ADR-010/011/012/013/022) · Guardrail #7 (Middleware
> Order Immutable) · `.ai/CLAUDE.md` §23 Uyarı #1 · ADR-010 §1.1 (kod kaydı:
> `PageRouterKernel.php` `buildDefaultMiddlewares()`, satır 273-282).
> Dosya kanıtı: `shared/src/Middleware/` = 11 PHP (10 middleware + `MiddlewarePipeline.php`).
> Dış doğrulama (kavramsal): OWASP — "framework korumasını önce aç, sonra derinleştir"
> (`references/sources.md` #3).

## 1. Tam Pipeline (bağlayıcı — değiştirilemez)

```text
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
→ BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Güvenlik rolü | Timeout |
|---|-----------|-------|---------------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS); Origin yoksa Referer, ikisi yoksa fail-closed 403 (ADR-010 Katman 3) | CSRF'in 3. katmanı; izinsiz origin → 403 `origin_not_allowed` | — |
| 2 | **Cors** | CORS header yönetimi; `X-CSRF-Token` allowlist (ADR-010 SPA akışı) | SPA'nın token header'ının kanalı | — |
| 3 | **RateLimiter** | APCu tabanlı **60 req/60s** per-IP; aşım → 429 + `Retry-After` (ADR-013) | Brute force / kaynak istismarı; oturum maliyeti ödemeden reddeder | 60s |
| 4 | **SecurityHeaders** | **CSP nonce üretimi (`bin2hex(random_bytes(32))`)** + CSP `strict-dynamic`, X-Frame-Options, HSTS, Referrer-Policy, X-Content-Type-Options, Trusted Types (ADR-012) | XSS'in çalışma zamanı etkisi; `form-action 'self'`, `frame-ancestors 'none'` | — |
| 5 | **SessionManager** | Session başlatır; **#4'ün ürettiği CSP nonce'u `$_SESSION['csp_nonce']`a kaydeder**; idle 3600s uzatma (ADR-011) | Nonce ↔ oturum bağı; oturum yaşam döngüsü | 3600s idle |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE/PATCH); `hash_equals` timing-safe; eksik/yanlış → 403 `csrf_invalid` (ADR-010 Katman 1) | State-changing istek sahteciliği (multi-tab: session-bound tek token) | — |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), **prod'da devre dışı** (Soft Constraint #4, ADR-008) | TestOnly; genişletme = güvenlik değişikliği | — |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session hibrit — ADR-011/ADR-052; Bearer: `Authorization` header asla otomatik gitmez) | Kimlik doğrulama | — |
| 9 | **Permission** | RBAC yetki kontrolü: `regular / premium / studio / car / admin / system` | Yetkilendirme (en az yetki) | — |
| 10 | **Validation** | Request/DTO validasyonu | Injection/bozuk girdi kapısı | — |
| → | **Controller** | İş mantığı | K6 tarafından korunur | — |

### 1.1 Sıra Neden Bu Şekilde? (mantık katmanları)

Pipeline rastgele değil, **maliyet ve bağımlılık sırasıyla** dizilmiştir:

| Grup | Katmanlar | Sıra mantığı |
|------|-----------|--------------|
| **Ucuz reddetme** | #1–#3 | Kimlik/oturum maliyeti ödemeden reddet: köken uygunsuzsa (#1), kanal kural dışıysa (#2), aşımsa (#3) istek daha hiçbir state'e dokunmadan düşer |
| **Yanıt-sunum hazırlığı** | #4–#5 | Nonce (#4'te CSPRNG ile üretilir) → oturum başlar ve nonce kaydedilir (#5); şablon bu bağa ihtiyaç duyar |
| **State-changing kapı** | #6 | Oturum var, token doğrulanabilir → mutating istek onaylanır |
| **Kimlik-yetki** | #7–#9 | Bypass (yalnız test) → kimlik → rol; en az yetki en sonda, kimlik sonrası |
| **Girdi temizliği** | #10 | Doğrulanmış kimlik+token sonrası DTO temizliği → Controller |

** tersi neyi kırar (tek cümle):** Ucuz reddetme (#1–#3) sona alınırsa her istek oturum+kripto
maliyeti ödeyerek düşer (DoS yüzeyi); #4/#5 terslenirse CSP bağı, #5/#6 terslenirse token bağı
kopar (§4 matrisi).

## 2. İmmutability Kuralları

| Kural | Sonuç (ihlalde) |
|-------|-----------------|
| Sıra değiştirilemez (Guardrail #7) | **Sistem durdurulur**; §23-1: CSP nonce üretimi bozulur → güvenlik açığı |
| Sıra yalnız `MiddlewarePipelineTest` güncellemesi + ADR değişikliği + kullanıcı onayıyla değişir | Test yeşil değilse revert |
| `MiddlewarePipelineTest` satır 13: "Pipeline sırası değişirse CSP/CSRF bozulur" (#4→#5, #6_Csrf zorlanır) | Test kırmızı → derhal revert |
| K7 Middleware, K6 güvenlik doğrulamasını atlayamaz; K6 asla bypass edilemez | Layer Violation → revert + log CRITICAL |
| Yeni middleware **kayıt zorunlu** (`buildDefaultMiddlewares()`); kayıtsız = pasif kod (ADR-013 §2.2f) | Koruma fiilen yok ama "var" görünür — denetimde HIGH bulgu |

## 3. CSP Nonce Zinciri (neden #4 → #5 sırası kutsal)

```text
[İstek] → #4 SecurityHeaders: nonce = bin2hex(random_bytes(32)) → $request['_csp_nonce']
          → #5 SessionManager: startOrExtend(nonce) + $_SESSION['csp_nonce'] geri okuma
          → HtmlShellRenderer: <meta name="csp-nonce"> + <script nonce="...">
[Yanıt]  → Content-Security-Policy: script-src 'strict-dynamic' 'nonce-{...}'
```

- Nonce **#4'te üretilir** (SessionManager'dan önce), **#5'te oturuma yazılır**.
- `#5 → #4` ters sıralarsa: #5 henüz oturumu başlatmamıştır → nonce kaydedilemez → şablonda
  boş nonce → tüm inline script/style **CSP tarafından engellenir** (sayfa kırılır) ve nonce
  oturumla bağlanmaz (XSS'e açık hale gelir).
- Nonce her istekte **yeniden** üretilir; eski nonce'un yeni istekte geçerli olmaması beklenen
  davranıştır (replay'e kapalı). Nonce değeri log'lara/raporlara kopyalanmaz.

## 4. Sıra Bozulması — Sonuç Matrisi

| Hata | Sonuç |
|------|-------|
| #4 ↔ #5 yer değiştirme | CSP nonce zinciri kırılır: boş/yanlış nonce → script'ler bloklanır + oturum bağı yok (ADR-012) |
| Csrf (#6), SessionManager (#5) öncesine | `$_SESSION['csrf_token']` henüz yok → her mutating istek 403 / token doğrulanamaz (ADR-010) |
| RateLimiter (#3) Auth/Session sonrasına alınırsa | Oturum açma/çalıştırma maliyeti ödenerek reddedilir; brute force oturum kaynaklarını tüketir (ADR-013) |
| Csrf (#6), Auth (#8) sonrasına | Kimliksiz istek token'sız geçebilir / hatalı 403'ler — bağımlılık zinciri bozulur |
| OriginCheck (#1) geriye/alınırsa | İlk savunma hattı geçersiz; fail-closed katmanı gereksiz yere devre dışı (ADR-010 Katman 3) |
| BypassAuth (#7) prod'da aktif | Yetkisiz erişim — H001 / Soft Constraint #4 ihlali |
| Middleware eklenirken kayıt güncellenmez | Kayıtsız middleware fiilen çalışmaz (ADR-013 §2.2f "pasif kod" deseni) |
| Validation (#10), Auth öncesine | Kimliksiz girdi işlenir; hata yüzeyi genişler |

## 5. Pipeline Dışı Bileşenler (bu dosyanın sınırı)

| Bileşen | İlişki | Kural |
|---------|--------|-------|
| K6 güvenlik katmanı | Controller öncesi son savunma | Bypass edilemez (Guardrail #7) |
| API Gateway (`api.coremusic.net`) | Routing, auth, rate limit, validation, logging, correlation ID (CLAUDE §6A.1) | Gateway kendi içinde aynı sıra mantığını korur — **⚠️ VERIFICATION REQUIRED**: Gateway middleware dizilimi kodda teyit edilmeden "aynı sıra" diye yazılmaz |
| `auth.coremusic.net` (ADR-058) | Merkezî kimlik doğrulama | Login akışları buraya yönlenir; dağıtık auth middleware çoğaltma yasak |
| HtmlShellRenderer | Nonce'un şablona girişi | Boş nonce = template hata, fail-closed (CSP engeller) |

## 6. Denetim Adımları (bu dosya özelinde)

| # | Kontrol | Beklenen | Kanıt |
|---|---------|----------|-------|
| 1 | Sıra 1-10 birebir | Aynı | `MiddlewarePipelineTest` + CLAUDE §6 tablosu |
| 2 | #4 nonce üretimi mevcut | `random_bytes` | `SecurityHeadersMiddleware.php` (dosya:satır ile) |
| 3 | #5 nonce kaydı mevcut | `$_SESSION['csp_nonce']` | `SessionManagerMiddleware.php` |
| 4 | Yeni middleware kayıtlı | `buildDefaultMiddlewares()` satırı | `PageRouterKernel.php` |
| 5 | BypassAuth prod koşulu | Ortam bayrağı kapalı | ADR-008 + config kanıtı |
| 6 | 429 + `Retry-After` | Header mevcut | `RateLimiterMiddleware.php` |

Kanıt bulunamayan madde `⚠️ VERIFICATION REQUIRED` ile raporlanır; "geçti" **denmez**.

*CoreMusic Skill v3.0 — security-hardening/references — 4.0.0 — 2026-10-21*
