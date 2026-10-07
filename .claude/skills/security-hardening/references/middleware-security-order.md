# Middleware Security Pipeline — Sıra, İmmutability & CSP Nonce Zinciri

> Kaynak: `.ai/CLAUDE.md` §6 (Immutable — ADR-010/011/012/013/022) · Guardrail #7 (Middleware
> Order Immutable) · `.ai/CLAUDE.md` §23 Uyarı #1. Kod kaydı: `PageRouterKernel.php`
> `buildDefaultMiddlewares()` (ADR-010 §1.1: satır 273-282).

## 1. Tam Pipeline (bağlayıcı — değiştirilemez)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
→ BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Güvenlik rolü | Timeout |
|---|-----------|-------|---------------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS); Origin yoksa Referer, ikisi yoksa fail-closed 403 (ADR-010 Katman 3) | CSRF'in 3. katmanı; izinsiz origin → 403 `origin_not_allowed` | — |
| 2 | **Cors** | CORS header yönetimi; `X-CSRF-Token` allowlist (ADR-010 SPA akışı) | SPA'nın token header'ının kanalı | — |
| 3 | **RateLimiter** | APCu tabanlı **60 req/60s** per-IP; aşım → 429 + `Retry-After` (ADR-013) | Brute force / kaynak istismarı; oturum maliyeti girmeden reddeder | 60s |
| 4 | **SecurityHeaders** | **CSP nonce üretimi (`bin2hex(random_bytes(32))`)** + CSP `strict-dynamic`, X-Frame-Options, HSTS, Referrer-Policy, X-Content-Type-Options, Trusted Types (ADR-012) | XSS'in çalışma zamanı etkisi; `form-action 'self'`, `frame-ancestors 'none'` | — |
| 5 | **SessionManager** | Session başlatır; **#4'ün ürettiği CSP nonce'u `$_SESSION['csp_nonce']`'a kaydeder**; idle 3600s uzatma (ADR-011) | Nonce ↔ oturum bağlanması; oturum yaşam döngüsü | 3600s idle |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE/PATCH); `hash_equals` timing-safe; eksik/yanlış → 403 `csrf_invalid` (ADR-010 Katman 1) | State-changing istek sahteciliği koruması (multi-tab: session-bound tek token) | — |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), **prod'da devre dışı** (Soft Constraint #4, ADR-008) | TestOnly; genişletme = güvenlik değişikliği | — |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session hibrit — ADR-011; Bearer: `Authorization` header asla otomatik gitmez) | Kimlik doğrulama | — |
| 9 | **Permission** | RBAC yetki kontrolü: `regular / premium / studio / car / admin / system` | Yetkilendirme (en az yetki) | — |
| 10 | **Validation** | Request/DTO validasyonu | Injection/bozuk girdi kapısı | — |
| → | **Controller** | İş mantığı | K6 tarafından korunur | — |

## 2. İmmutability Kuralları

| Kural | Sonuç (ihlalde) |
|-------|-----------------|
| Sıra değiştirilemez (Guardrail #7) | **Sistem durdurulur**; §23-1: CSP nonce üretimi bozulur → güvenlik açığı |
| Sıra yalnız `MiddlewarePipelineTest` güncellemesi + ADR değişikliği + kullanıcı onayıyla değişir | Test yeşil değilse revert |
| `MiddlewarePipelineTest` satır 13: "Pipeline sırası değişirse CSP/CSRF bozulur" (#4→#5, #6_Csrf zorlanır) | Test kırmızı → derhal revert |
| K7 Middleware, K6 güvenlik doğrulamasını atlayamaz; K6 asla bypass edilemez | Layer Violation → revert + log CRITICAL |

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

## 4. Sıra Bozulması — Sonuç Matrisi

| Hata | Sonuç |
|------|-------|
| #4 ↔ #5 yer değiştirme | CSP nonce zinciri kırılır: boş/yanlış nonce → script'ler bloklanır + oturum bağı yok (ADR-012) |
| Csrf (#6), SessionManager (#5) öncesine alınırsa | `$_SESSION['csrf_token']` henüz yok → her mutating istek 403 / token doğrulanamaz (ADR-010) |
| RateLimiter (#3) Auth/Session sonrasına alınırsa | Oturum açma/çalıştırma maliyeti ödenerek reddedilir; brute force oturum kaynaklarını tüketir (ADR-013) |
| Csrf (#6), Auth (#8) sonrasına alınırsa | Kimliksiz istek token'sız geçebilir / hatalı 403'ler — bağımlılık zinciri bozulur |
| OriginCheck (#1) geriye/alınırsa | İlk savunma hattı geçersiz; fail-closed katmanı gereksiz yere devre dışı (ADR-010 Katman 3) |
| BypassAuth (#7) prod'da aktif | Yetkisiz erişim — H001 / Soft Constraint #4 ihlali |
| Middleware eklenirken kayıt güncellenmez | Kayıtsız middleware fiilen çalışmaz (ADR-013 §2.2f "pasif kod" deseni) |