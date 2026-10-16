---
node_id: K009-security/session-security/session-security-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: [K009-security/session-security/session-security-anayasa]
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php:330-341 (web middleware sırası)"
  - "shared/src/Middleware/SessionManagerMiddleware.php:15-24"
  - "shared/src/Session/SessionLifecycle.php:21-75, 97-127"
  - "shared/src/Session/SessionBootstrapper.php:34-59, 68-83"
  - "api.coremusic.net/index.php:114-171"
  - "shared/src/Middleware/CsrfMiddleware.php:20-64"
  - "auth.coremusic.net/include/Service/SessionManager.php:79-82, 133-169"
validation: "2026-10-15 — 30 satır-kanıt; V-1..V-9 açık; ⚠️ 5 dükkan"
open_questions:
  - "V-3: method==='session' NameConfusion — CSRF kaçışı senaryosu test edildi mi?"
group: mimari
---

# K009 · session-security · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari Sürücü

Bu belge session kararlarının **çalışan akışlarını** sürdürür: web istek sırası, API
pipeline'ı, login fixation reddi, timeout dalı, CSRF kapısı, kill zinciri. Her adım
dosya/satır kanıtıyla.

## 1. Sürüş Ailesi

| # | Sürüş | Tetikleyici |
|---|---|---|
| F-1 | Web middleware sırası (session → csrf) | her web request |
| F-2 | API pipeline (auth-bootstrapper + CSRF kapısı) | her API request |
| F-3 | Login → fixation reddi | kimlik doğrulama |
| F-4 | Lifecycle: lifetime → idle → token → rotation | `startOrExtend` |
| F-5 | CSRF doğrulama (fail-closed 403) | state-changing verb |
| F-6 | Kill / logout / display-cookie temizliği | kullanıcı çıkışı |
| F-7 | restartFresh (bozuk session) | boot anormalliği |
| F-8 | Rotation (kod yolu — ⚠️ anayasa D-2: pratikte ulaşılamaz) | zaman aşımı |

## 2. F-1 · Web Middleware Sırası

```
PageRouterKernel.php:330-341  (sabit sıra, ADR ile değişmez)
  1  OriginCheckMiddleware     :331  — izinsiz Origin 403
  2  CorsMiddleware            :332  — header yayıcı
  3  RateLimiterMiddleware     :333  — throttle
  4  SecurityHeadersMiddleware :334  — header set
  5  SessionManagerMiddleware  :335  — ▼ F-4 burada çalışır
  6  CsrfMiddleware            :336  — ▼ F-5 burada çalışır
  7  BypassAuthMiddleware      :337  — ADR-008 kapsamı
  8  AuthMiddleware            :338
  9  PermissionMiddleware      :339
 10  ValidationMiddleware      :340
```

**F-1a — SessionManagerMiddleware sürücüsü** (`SessionManagerMiddleware.php:15-24`):

```php
public function handle(array $request, callable $next): array
{
    SessionBootstrapper::ensureStarted();                       // :17 tek giriş (T-01)
    $this->sessionInit->startOrExtend($request['_csp_nonce'] ?? null);  // :18 → F-4
    $request['_csp_nonce'] = $this->sessionInit->getCspNonce();  // :20
    $request['_session']   = $this->sessionInit->getSessionData(); // :21
    return $next($request);
}
```

**Karar:** session **SecurityHeaders'ten sonra**, controller'dan **önce**; CSRF ise
**Auth'dan önce** çalışır → kimlik kontrolüne giren her state-changing istek CSRF'den
geçmiştir.

## 3. F-2 · API Pipeline

```
api.coremusic.net/index.php
  :111  /api/* girer
  :114  preg_match ^/api/v1/auth(/|$)
          EVET → SessionBootstrapper::ensureStarted()  :115   ← yalnız auth rotası
  :121  pipelineRequest kur:
          headers['x-csrf-token'] = $_SERVER['HTTP_X_CSRF_TOKEN']  :125
          body = $_POST + JSON input                              :126
  :147  pipeline (ADR-020 sabit sıra):
          ResponseNormalization → OriginCheck → Cors → RateLimit
          → Authentication(hybrid session|Bearer+jti)   :154-159
          → [CSRF kapısı :164-169]                       ▼ aşağı
          → RequestValidation → Authorization            :170-171
```

**F-2a — CSRF kapısı** (`index.php:164-169, paraphrase):**

```php
->pipe(static function (array $req, callable $next) use ($csrfMiddleware): array {
    if (($req['_auth_user']['method'] ?? '') !== 'session') {
        return $next($req);            // Bearer/API-key: CSRF yok (Z-3 kapsamı)
    }
    return $csrfMiddleware->handle($req, $next);   // session-auth: zorunlu
})
```

**Gerekçe (kod yorumu `index.php:160-163`):** Bearer istemcisi cookie kullanmaz;
cross-site tarayıcı özel header forging yapamaz; `SameSite=Lax` + `OriginCheck` ek
katman. **⚠️ V-3:** `method` alanı string eşleşmesi → `NameConfusion` senaryosu
`session-security-dogrulama` testinde.

## 4. F-3 · Login → Fixation Reddi

```
POST login (auth.coremusic.net)
  → kimlik doğrulama OK
  → session regenerate yolu
       auth SessionManager::regenerateId()   :79-82
           session_regenerate_id(true)       :81
             ├─ eski id destroy (saldırganın servis ettiği id ölü)
             └─ yeni id + veri kopyalanır
  → MM_* key yazımı (SessionKeys:7-15)
  → ApiSessionManager:85 (API session upgrade aynı koruma)
  ▶ yanıt: yeni Set-Cookie (aynı bayraklar — SessionConfig:52-62)
```

**Doğrulama koşulu (V-4):** saldırgan önceden `COREMUSIC_SESS=ATTACKER` set ettiyse
login sonrası o id **ölü** olmalı → test: login前后 aynı id kullanılamamalı.

**⚠️ U-4:** `regenerateId()` çağıranların tam listesi (login handler dâhil) bu
belgede **doğrulanmadı** — grep zorunlu (`session-security-dogrulama`).

## 5. F-4 · Lifecycle Sürücüsü

```
SessionLifecycle::startOrExtend(nonce)             SessionLifecycle:21-69
  │ SessionBootstrapper::ensureStarted()           :29
  │
  ├─ [1] isSessionExpired()                        :31-39
  │      created_at + MAX_LIFETIME(1800) geçtiyse
  │        → lifetimeTimedOut=true, destroy()      :33-34   ← ÖNCE BUNA BAKILIR
  │      değilse → extendSession()                 :37  (:115-127, yine 1800 kontrolü)
  │
  ├─ [2] isIdleTimedOut()                          :41-45
  │      _session_last_active + IDLE_TIMEOUT(3600)
  │        → idleTimedOut=true, destroy()          :42-43   ⚠️ anayasa D-1: pratikte ulaşılamaz
  │
  ├─ [3] csrf_token üret                          :47-49   bin2hex(random_bytes(32))
  ├─ [4] csp_nonce yaz                           :51-52
  │
  ├─ [5] rotation                                 :54-62
  │      now - _session_rotated_at >= ROTATION_INTERVAL(1800)
  │        → session_regenerate_id(true)          :60     ⚠️ anayasa D-2: pratikte ulaşılamaz
  │
  └─ [6] last_activity / _session_last_active     :64-66
```

**F-4a — destroy zinciri** (`:71-75`):

```php
public function destroy(): void
{
    SessionBootstrapper::restartFresh();   // :73 → F-7
    $this->initSessionKeys();              // :74 (created/last/rotated = now, :87-95)
}
```

**F-4b — timeout sorguları** (`:97-113`): key **yoksa `false` döner** (:100-101,
:109-110) → yeni session'da timeout anında tetiklenmez (doğru başlangıç davranışı).

**Çıktı:** her istekte oturum ya 30. dakikada absolute kill yer, ya uzatılır; idle
ve rotation kolları **kodda var ama değeri erişilemez** (V-1, anayasa §4 D-1/D-2).

## 6. F-5 · CSRF Doğrulama (Fail-Closed)

```
State-changing istek (method ∉ {GET,HEAD,OPTIONS})      CsrfMiddleware:22-27
  ├─ bypassRoutes listesi BOŞ (A01:2021)                :15-18, 29-32 → skip YOK
  ├─ sessionToken = $request['_session']['csrf_token']  :35
  ├─ submitted   = header x-csrf-token                  :37
  │                (yoksa) body csrf_token              :38-40
  ├─ hash_equals(sessionToken, submitted)               :42   ← sabit-zamanlı
  │     boş/uyuşmaz → fail()                            :43
  └─ eşleş → $next                                     :46
```

**F-5a — fail sürücüsü** (`:49-64, paraphrase):**

```php
private function fail(): array
{
    LoggerFactory::getInstance()->securityEvent('csrf_invalid', [   // :52 gözlemlenebilirlik
        'uri' => $_SERVER['REQUEST_URI'] ?? '-',
        'ip'  => $_SERVER['REMOTE_ADDR'] ?? '-',
    ]);
    return ['httpStatus' => 403, 'type' => 'json',
            'body' => ['error' => 'csrf_invalid'], 'halt' => true]; // :57-63 geçiş YOK
}
```

**Çıktı:** uyuşmazlık → **403 + halt** (sessiz reddetme yok, anayasa T-09).

## 7. F-6 · Kill / Logout / Display Cookies

```
logout / privilege düşüşü
  ├─ SessionLifecycle::destroy()                :71-75  → F-7 (yeni boş oturum)
  ├─ auth SessionManager::clearDisplayCookies() :161-169
  │     5 cookie: MM_Username/MM_Image/MM_UserID/MM_UserDesc/MM_UserYear
  │     setcookie(name,'', time-3600, '/', '.coremusic.net')   :166
  │     ⚠️ httponly arg YOK (Z-5) — display amaçlı, JS-okunur
  └─ yeniden boot → yeni id + yeni csrf_token
```

## 8. F-7 · restartFresh (Bozuk Session)

```
SessionBootstrapper::restartFresh()                Bootstrapper:34-59
  neden: veri bozuk / timeout / privilege değişimi / destroy çağrısı
  1. $_SESSION = []                                 :38
  2. session_status ACTIVE → session_destroy()      :40-42
  3. use_cookies → setcookie(boş, expire -42000, path/domain/secure/httponly)  :44-54
  4. configure(): session_name(:74) save_path(:76-78) set_cookie_params(:80)    :56
  5. session_start()                                :57
  6. session_regenerate_id(true)                    :58   ← fixation zinciri de
```

**Fail-closed:** bozuk session **sessizce devam etmez**; sıfırlanır → kullanıcı
guest durumuna düşer (Y-8 UX bilinmiyor, U-6).

**Idempotent:** `ensureStarted()` tekrar çağrılabilir (:13 not, :21-23 guard);
`configure()` da `$configured` guard'lı (:70-72).

## 9. F-8 · Rotation Sürücüsü (kod yolu)

| Yol | Kanıt | Durum |
|---|---|---|
| `SessionLifecycle:54-62` (30 dk) | `ROTATION_INTERVAL=1800` | ⚠️ anayasa D-2: lifetime önce → pratikte ulaşılamaz |
| `auth rotateIfNeeded($interval)` :144-159 | `>` ile **strict** eşik; `$lastRotation==0` → init :148-151 | çağıranlar U-5 (UNKNOWN) |
| `restartFresh` her çağrıda :58 | zorunlu regenerate | ✅ aktif |

**V-2:** iki rotation yolu (`>` vs `>=` eşik, SessionLifecycle:59 vs auth:153) —
tutarlılık testi `session-security-dogrulama`'da.

## 10. Çevrimdışı (Air-Gap) Dalı

| Senaryo | Akış |
|---|---|
| Session save_path ağ diskinde değil | lokal dosya (`SessionConfig:33-36`, temp+0750) → ağ gerekmez ✅ |
| Reboot (temp temizlenirse) | tüm oturumlar düşer → kullanıcılar yeniden login (fail-closed ✅) |
| Bearer-only API istemcisi | session-bootstrapper yalnız auth rotası (:114-116) → CSRF kapısı atlanır (Z-3 kapsamı) |

## 11. Varsayılanlar & Bilinmeyenler

| # | Soru | Karar |
|---|---|---|
| V-1 | idle/rotation pratikte tetiklenir mi? (D-1/D-2) | anayasa Z-1 ⚠️ |
| V-2 | rotation eşik tutarlılığı (`>=` vs `>`) | ⚠️ doğrulama |
| V-3 | `method==='session'` NameConfusion? | anayasa Z-3 ⚠️ |
| V-4 | login前后 id değişimi testi | dobrelama testi |
| V-5 | API auth rotalarında regenerate? (U-3) | ⚠️ doğrulama |
| V-6 | `use_strict_mode` sunucu php.ini'de? | anayasa Z-2 UNKNOWN |
| V-7 | Guest'a düşme UX bildirimi? | UNKNOWN |
| V-8 | Cookie prefix `__Host-` mümkün mü? | UNKNOWN (kullanım yok) |
| V-9 | `SESSION_LIFETIME` env kullanılıyor mu? | anayasa U-7 UNKNOWN |

## 12. Hata Dalı Tablosu (sürüş → hata → sonuç)

| Sürüş | Hata durumu | Sonuç | Kanıt |
|---|---|---|---|
| F-1 | session cookie yok | `ensureStarted` yeni oturum açar | Bootstrapper:21-27 |
| F-1 | `save_path` dizin yok | `@mkdir 0750` ile üretilir | SessionConfig:34-36 |
| F-4 | `created_at` key yoksa | `isSessionExpired` → `false` | Lifecycle:109-110 |
| F-4 | `_session_last_active` yoksa | `isIdleTimedOut` → `false` | Lifecycle:100-101 |
| F-4 | lifetime aşıldı | `destroy()` → restartFresh (F-7) | Lifecycle:33-34 |
| F-5 | token boş / uyuşmaz | **403 + halt** + `csrf_invalid` log | Csrf:42-43, 49-64 |
| F-5 | GET/HEAD/OPTIONS | CSRF atlanır | Csrf:24-27 |
| F-5 | bypassRoutes | liste **boş** → atlama yok | Csrf:15-18 |
| F-2 | `_auth_user.method ≠ 'session'` | CSRF atlanır (Bearer) ⚠️ Z-3 | api:165-167 |
| F-7 | session verisi bozuk | `$_SESSION=[]` → destroy → yeni id | Bootstrapper:38-58 |
| F-7 | cookie silme | expire `-42000` (COOKIE_EXPIRY) | Bootstrapper:48 |
| F-6 | logout | destroy + yeni csrf_token | Lifecycle:71-75 |

## 13. Doğrulama Soru Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Yeni oturum/login yolu eklendi mi? | `regenerate_id(true)` çağırıyor mu? | RED (§12.1) |
| 2 | Cookie flag'leri elle set ediliyor mu? | T-01 ihlali → yalnız Bootstrapper | BLOCK |
| 3 | Token karşılaştırması `===` mi? | §12.3 ihlali → RED | `hash_equals` ✓ |
| 4 | CSRF reddi gözetleniyor mu? | §12.4 ihlali → RED | devam |
| 5 | `session_*` API'si Bootstrapper dışında mı? | §12.6 ihlali → BLOCK | devam |
| 6 | Timeout sabitleri değişti mi? | Z-1 yeniden değerlendirme + ADR | devam |
| 7 | API'ye yeni auth metodu mu geldi? | Z-3 CSRF kapısı gözden geçirilir | devam |
| 8 | Yeni cookie adı mı? | domain/httponly/samesite bayrakları | devam |
| 9 | `setcookie` 5 argümanlı mı? | ⚠️ httponly unutulmuş olabilir (Z-5 sınıfı) | devam |
| 10 | Rotation/interval değeri mi değişti? | D-1/D-2 dedüksiyonu yeniden ölçülür | devam |

## 14. API Hata Ayrımı (401 / 403 / 429)

| Durum | HTTP | Kaynak | Bu alanın ilgisi |
|---|---|---|---|
| Kimlik yok/geçersiz (Bearer) | 401 sınıfı | AuthenticationMiddleware (`api:154-159`) | CSRF kapısına **ulaşamaz** |
| CSRF token yok/uyuşmaz | **403** `csrf_invalid` | CsrfMiddleware:57-63 | F-5 |
| İzinli Origin değil | 403 | OriginCheckMiddleware (`api:149`) | Z-3'ün ek katmanı |
| Rate limit | 429 sınıfı | RateLimitMiddleware (`api:151`) | kapsam dışı (rate-limiting) |
| Rol yetmeziz | 403/404 sınıfı | AuthorizationMiddleware (`api:171`) | authorization-kapsam |

## 15. Test Senaryoları (sürücü düzeyi)

| # | Senaryo | Beklenen |
|---|---|---|
| S-1 | İlk ziyaret | `Set-Cookie: COREMUSIC_SESS=…; HttpOnly; SameSite=Lax; Path=/; Domain=.coremusic.net` |
| S-2 | Login | Set-Cookie'de **yeni** id (eski kapanır) |
| S-3 | POST + geçerli `x-csrf-token` | 200 (F-5 geçer) |
| S-4 | POST + tokensiz | 403 `csrf_invalid` + `coremusic_php_security.log` satırı |
| S-5 | GET/HEAD/OPTIONS | CSRF kontrolü çalışmaz (F-5 atlanır) |
| S-6 | API Bearer POST `/api/v1/*` | CSRF atlanır; session-auth'ta zorunlu (F-2) |
| S-7 | `/api/v1/auth/*` | `SessionBootstrapper::ensureStarted` çalışır (cookie set) |
| S-8 | 30+ dk bekleme | lifetime kill → guest (Z-1 ölçümü: T-3 ile birlikte) |
| S-9 | logout → eski id ile istek | yeni boş oturum, kimlik yok |
| S-10 | `session_destroy` sonrası | `csrf_token` yeniden üretilir (Lifecycle:47-49) |
| S-11 | Thread: iki sekme aynı anda | rotation çakışması gözlemlenir (V-6/U-5) |
| S-12 | `SESSION_LIFETIME=7200` (.env.example:33) | U-7 ölçümü: config katmanı okuyor (auth `app.php:22`) — cookie/gc'ye iletimi var mı? |
| S-13 | auth HTTP isteği (index.php:56) | `Set-Cookie` `secure` bayrağı: `app.php:24` `true` yerine `$isHttps` cookie'ye yansıyor mu? |

> S-13, anayasa §5'teki `secure` satırının tarayıcı-bazlı kanıtıdır
> (auth `index.php:38-41,56` — HTTPS/X-Forwarded-Proto algılama).

## 16. İlk İstek Zaman Çizelgesi (session bakış açısı)

| # | An | Olan | Kanıt |
|---|---|---|---|
| 1 | İlk GET | middleware 1-4 (Origin/Cors/RateLimit/Headers) | PageRouterKernel:331-334 |
| 2 | #5 SessionManager | `ensureStarted` → `configure` (name/path/cookieParams) → `session_start` | SessionManagerMw:17 · Bootstrapper:68-83 |
| 3 | Cookie yazımı | `COREMUSIC_SESS`; lifetime 0 · HttpOnly · Lax · secure(algılanan) | SessionConfig:52-62 |
| 4 | Lifecycle | lifetime → idle → `csrf_token` → `csp_nonce` → rotation → `last_activity` | Lifecycle:31-66 |
| 5 | #6 Csrf | GET: atla; POST: `hash_equals` → 403/pass | Csrf:22-46 |
| 6 | #7-10 | BypassAuth → Auth → Permission → Validation | PageRouterKernel:337-340 |
| 7 | Login ise | `regenerate_id(true)` → yeni Set-Cookie | auth:79-82 |
| 8 | 30. dk | ⚠️ D-1/D-2: lifetime kill (rotation/idle erişilemez) | Lifecycle:31-39 |
| 9 | Logout | destroy → restartFresh → yeni boş oturum | Lifecycle:71-75 |

## 17. Bağlantılar

- [session-security-anayasa](session-security-anayasa.md) — Z-1..Z-7, D-1..D-3
- [secrets-surucu](../secrets/secrets-surucu.md) — `csrf_token`/session sır okuma zinciri
- [encryption-surucu](../encryption/encryption-surucu.md) — HTTPS/secure bayrağı

**BAĞLANTILAR:** [[session-security-kapsam]] · [[session-security-ornekler]] ·
[[session-security-dogrulama]] · [[session-security-bilesenler]] ·
[[session-security-web-arastirma]] · [[authentication-kapsam]] ·
[[rate-limiting-kapsam]] · [[security-kararlar]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
