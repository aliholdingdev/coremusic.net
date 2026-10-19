---
node_id: K009-security/session-security/session-security-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: [K009-security/encryption/encryption-anayasa, K009-security/secrets/secrets-anayasa]
evidence:
  - "shared/src/Session/SessionConfig.php:16-62 (sabitler / secure algılama / cookieParams)"
  - "shared/src/Session/SessionBootstrapper.php:19-83 (tek session_* giriş noktası)"
  - "shared/src/Session/SessionLifecycle.php:21-127 (timeout / rotation / csrf_token)"
  - "shared/src/Security/SessionKeys.php:5-25"
  - "auth.coremusic.net/include/Service/SessionManager.php:79-82, 133-169"
  - "shared/src/Middleware/CsrfMiddleware.php:13-64"
  - "api.coremusic.net/index.php:114-171"
  - "shared/src/PageRouter/PageRouterKernel.php:330-341"
  - "grep session_regenerate_id (9 çağrı sitesi) · grep use_strict_mode = 0"
  - "auth.coremusic.net/index.php:38-41,56 · config/app.php:22-24 (cookie_secure runtime override + session.lifetime)"
validation: "2026-10-15 — 37 satır-kanıt; U-1 kapandı (secure dinamik), U-7 kısmi (SESSION_LIFETIME config katmanı), U-2..U-6 ve U-8 açık; ⚠️ 7 dükkan (Z-1 idle/rotation ölü kod · Z-2 use_strict_mode yok)"
open_questions:
  - "Z-1: MAX_LIFETIME 1800 = ROTATION_INTERVAL 1800 ve lifetime önce kontrol → rotation/idle pratikte tetiklenir mi?"
  - "Z-2: session.use_strict_mode kodda hiç set edilmiyor — sunucu php.ini'de açık mı?"
group: mimari
---

# K009 · session-security · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari Anayasa (SSOT)

Bu belge **oturum güvenliğinin** anayasasıdır: fixation reddi, cookie bayrakları,
timeout hiyerarşisi, rotation ve CSRF entegrasyonu. Kanıtsız iddia yazılmaz;
analitik çıkarımlar "dedüksiyon" olarak işaretlenir.

## 1. Kapsam

| İçerik | Dışarıda |
|---|---|
| Cookie bayrakları (httponly/samesite/secure/lifetime) | Şifreleme (encryption) |
| Fixation: `session_regenerate_id` | Sır saklama (secrets) |
| Idle / absolute timeout + rotation hiyerarşisi | Rate limiting (rate-limiting) |
| CSRF token üretimi + doğrulama | Yetki modeli (authorization) |
| Session kill / logout / restartFresh | Kimlik doğrulama akışı (authentication) |

## 2. Karar Özeti

| # | Karar | Durum |
|---|---|---|
| T-01 | `session.*` API çağrıları **tek sınıfta**: `SessionBootstrapper` (SSOT) | ✅ Bootstrapper:8-11, 68-83 |
| T-02 | Cookie `httponly=true` | ✅ SessionConfig:59 |
| T-03 | `samesite=Lax` + `lifetime=0` (session cookie) | ✅ SessionConfig:55,60 |
| T-04 | `secure` dinamik: HTTPS **veya** `X-Forwarded-Proto: https` | ✅ SessionConfig:38-48,58 |
| T-05 | Cookie `domain=.coremusic.net` (subdomain birleşimi) | ⚠️ Z-4 |
| T-06 | Fixation: login/rol/reset + rotation + restartFresh yollarında `regenerate_id(true)` | ✅ 9 çağrı sitesi |
| T-07 | **`session.use_strict_mode` kodda hiç set edilmiyor** (grep: 0) | ⚠️ Z-2 |
| T-08 | Idle vs absolute timeout ayrımı kodda var | ⚠️ Z-1: pratikte idle/rotation **ölü kod** olabilir |
| T-09 | CSRF: `hash_equals` sabit-zamanlı + uyumsuzda 403 fail-closed | ✅ CsrfMiddleware:42, 49-64 |
| T-10 | API: CSRF yalnız `method==='session'` (cookie-auth) isteklerinde | ⚠️ Z-3 |
| T-11 | Bypass route listesi **boşaltıldı** (A01:2021 gerekçesi) | ✅ CsrfMiddleware:15-18 |

## 3. Kanıt Tablosu

| # | Dosya | Satır | Kanıt |
|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php` | 16-20 | `COOKIE_EXPIRY=42000` · `MAX_LIFETIME=1800` · `IDLE_TIMEOUT=3600` · `ROTATION_INTERVAL=1800` · `DIR_PERMISSIONS=0750` |
| 2 | `SessionConfig.php` | 31 | session adı: `SESSION_NAME` sabiti, yoksa `COREMUSIC_SESS` |
| 3 | `SessionConfig.php` | 33-36 | save_path: ini ya da temp+`mkdir 0750` |
| 4 | `SessionConfig.php` | 38-41 | `isHttps`: `$_SERVER['HTTPS']` **veya** `HTTP_X_FORWARDED_PROTO=https` |
| 5 | `SessionConfig.php` | 45, 47 | `cookieDomain='.coremusic.net'` · `secure=$isHttps` |
| 6 | `SessionConfig.php` | 52-62 | `cookieParams()`: lifetime **0**, path `/`, secure, **httponly true**, **samesite Lax** |
| 7 | `SessionBootstrapper.php` | 19-28 | `ensureStarted()`: aktifse tekrar yok; `configure` + `session_start` |
| 8 | `SessionBootstrapper.php` | 34-59 | `restartFresh()`: `$_SESSION=[]` → destroy → süresi dolmuş cookie → start → **regenerate(:58)** |
| 9 | `SessionBootstrapper.php` | 68-83 | `configure()`: `session_name(:74)` · `save_path(:76-78)` · `set_cookie_params(:80)` |
| 10 | `SessionLifecycle.php` | 21-39 | `startOrExtend()`: **lifetime önce** kontrol → destroy |
| 11 | `SessionLifecycle.php` | 41-45 | **idle sonra** kontrol → destroy |
| 12 | `SessionLifecycle.php` | 47-49 | `csrf_token = bin2hex(random_bytes(32))` (yoksa üret) |
| 13 | `SessionLifecycle.php` | 51-52 | `csp_nonce` üretimi |
| 14 | `SessionLifecycle.php` | 54-62 | rotation: `_session_rotated_at` ≥ `ROTATION_INTERVAL` → **regenerate(:60)** |
| 15 | `SessionLifecycle.php` | 64-66 | `last_activity` / `_session_last_active` yazımı |
| 16 | `SessionLifecycle.php` | 71-75 | `destroy()`: `restartFresh` + `initSessionKeys` |
| 17 | `SessionLifecycle.php` | 97-113 | `isIdleTimedOut` / `isSessionExpired` (key yoksa `false` — bkz Z-1c) |
| 18 | `shared/src/Security/SessionKeys.php` | 7-15 | key'ler: `MM_UserID`, `MM_Username`, …, `_session_last_active`, `_session_rotated_at` |
| 19 | `SessionKeys.php` | 17-25 | `USER_KEYS` grubu (7 key) |
| 20 | `auth…/SessionManager.php` | 79-82 | `regenerateId()` → `session_regenerate_id(true)` |
| 21 | `auth…/SessionManager.php` | 133-142 | `isIdleExpired(timeout)` · `touch()` (last_active güncelle) |
| 22 | `auth…/SessionManager.php` | 144-159 | `rotateIfNeeded(interval)`: geç → **regenerate(:154)** |
| 23 | `auth…/SessionManager.php` | 161-169 | `clearDisplayCookies()`: 5 `MM_*` cookie süresi dolmuş setcookie (⚠️ httponly arg yok → Z-5) |
| 24 | `grep session_regenerate_id` | 9 site | Lifecycle:60 · Bootstrapper:58 · SessionInitializer:54,76 · auth SessionManager:81,154 · ApiSessionManager:85 · HomeSessionManager:74,147 |
| 25 | `grep use_strict_mode` | **0** | kodda strict mode ayarı YOK → Z-2 |
| 26 | `shared/src/Middleware/CsrfMiddleware.php` | 15-18 | bypassRoutes **boş** (A01:2021 notu) |
| 27 | `CsrfMiddleware.php` | 22-27 | GET/HEAD/OPTIONS serbest; gerisi state-changing |
| 28 | `CsrfMiddleware.php` | 35-40 | session token + `x-csrf-token` header / body `csrf_token` |
| 29 | `CsrfMiddleware.php` | 42-44 | **`hash_equals`** → uyuşmazsa `fail()` |
| 30 | `CsrfMiddleware.php` | 49-64 | `fail()`: `securityEvent('csrf_invalid')` log + **403 json + halt** |
| 31 | `shared/src/Middleware/SessionManagerMiddleware.php` | 15-24 | `ensureStarted` + `startOrExtend` + `_session`/`_csp_nonce` request'e |
| 32 | `shared/src/PageRouter/PageRouterKernel.php` | 330-341 | middleware sırası (§7 tablosu) |
| 33 | `api.coremusic.net/index.php` | 114-116 | yalnız `/api/v1/auth/*` → `SessionBootstrapper::ensureStarted` |
| 34 | `api.coremusic.net/index.php` | 121-127 | `x-csrf-token` header'ı pipeline'a taşınır |
| 35 | `api.coremusic.net/index.php` | 160-169 | CSRF kapısı: `_auth_user.method === 'session'` ise CsrfMiddleware |
| 36 | `shared/config/.env.example` | 32-35 | `SESSION_NAME`/`SESSION_LIFETIME=7200`/`SESSION_COOKIE_DOMAIN` şablonu (⚠️ kullanımı U-7) |
| 37 | `auth.coremusic.net/index.php` · `config/app.php` | 38-41, 56 · 22-24 | `$isHttps` (HTTPS veya `X-Forwarded-Proto`) `$appConfig['session']['cookie_secure']`'i ezdirir; `session.lifetime = SESSION_LIFETIME` config anahtarı (consumption ≠ iletim, U-7) |

## 4. Timeout Hiyerarşisi — ⚠️ Z-1 (ölü kod dedüksiyonu)

```
COOKIE_EXPIRY     = 42000 sn  tarayıcı cookie ömrü (session cookie'da lifetime=0 → pratikte yok sayılır)
MAX_LIFETIME      =  1800 sn  mutlak ömür — 30 dk
IDLE_TIMEOUT      =  3600 sn  boşta kalma — 60 dk
ROTATION_INTERVAL =  1800 sn  id yenileme — 30 dk
```

**Kontrol sırası** (`startOrExtend`, SessionLifecycle:31-62):

1. `isSessionExpired()` (created_at + **1800**) → **önce** bakılır, destroy (:31-39)
2. `isIdleTimedOut()` (last_active + **3600**) → **sonra** bakılır (:41-45)
3. rotation (rotated_at + **1800**) → en sonda (:54-62)

**Dedüksiyonlar (analitik — ⚠️ VERIFICATION REQUIRED):**

| # | Çıkarım | Gerekçe |
|---|---|---|
| D-1 | **Idle timeout pratikte tetiklenemez**: oturum 3600 sn'ye ulaşmadan önce created_at 1800'ü geçer → 1. adım destroy eder | 1800 < 3600 + sıra |
| D-2 | **Rotation pratikte tetiklenemez**: rotated_at ilk değeri created_at ile aynı anda yazılır (:57-58); 1800 eşiği aynı anda dolar, ama lifetime **önce** kontrol edilir → destroy, rotation'a sıra gelmez | ROTATION == MAX + sıra |
| D-3 | Idle/rotation **yalnız** `created_at`/`rotated_at` key'leri eksikken çalışabilir (o durumda fonksiyonlar `false` döner :100-101,109-110 → idle de tetiklenmez) | key yoksa false |

**İki ihtimal (karar bekliyor):**

| İhtimal | Sonuç |
|---|---|
| (a) İstenen: mutlak 30 dk politikası, idle/rotation kodu miras | değerler kasıtlı, **isimler yanıltıcı**; dead-code temizliği ADR ister |
| (b) Hata: idle 30 dk isteniyordu | `SessionConfig:17-19` revizyonu: `MAX_LIFETIME > IDLE_TIMEOUT` düzeni |

Not: bağımsız `rotateIfNeeded($interval)` (auth SessionManager:144-159) **çağrıya özel
interval** alır — çağıranlar bu belge kapsamında **doğrulanmadı** (U-5).

## 5. Cookie Ayrıntıları

| Alan | Değer | Kanıt | Yorum |
|---|---|---|---|
| name | `COREMUSIC_SESS` (override edilebilir) | SessionConfig:31 | ⚠️ env override U-7 |
| lifetime | **0** (session cookie) | SessionConfig:55 | tarayıcı kapanınca silinir ✅ |
| path | `/` | SessionConfig:56 | ✅ |
| domain | `.coremusic.net` | SessionConfig:45,57 | tüm subdomain'ler paylaşır → **Z-4** |
| secure | HTTPS algılanınca `true` | SessionConfig:38-41,58 | proxy `X-Forwarded-Proto` ile ✅ (önceki U-1 **kapatıldı**) |
| httponly | `true` | SessionConfig:59 | XSS ile token çalınamaz ✅ |
| samesite | `Lax` | SessionConfig:60 | cross-site POST'a engel + Z-3 katmanı |
| temizleme | expire `-COOKIE_EXPIRY` | Bootstrapper:44-54 | restartFresh'te cookie süresi düşürülür ✅ |
| secure (auth config) | `app.php:24` `true` → runtime'da `$isHttps` ile ezilir | auth index.php:38-41,56 | cookie'ye yansıması ölçülür (surucu S-13) |

**⚠️ Z-5:** `clearDisplayCookies()` 5 argümanlı `setcookie` kullanır
(auth SessionManager:166) → `httponly` **belirtilmemiş** (PHP'de default `false`).
Değerler: `MM_Username/MM_Image/MM_UserID/MM_UserDesc/MM_UserYear` — auth secret'ı
değil, ama JS-okunur. Doğrulama gerekli.

## 6. Fixation & Lifecycle

### 6.1 Fixation savunması (karar ağacı)

```
Kullanıcı login/rol/şifre-sıfırlama
  → kimlik doğrulama OK
  → session_regenerate_id(true)        ← 9 site (kanıt #24)
       ├─ ESKİ id kapanır → saldırganın servis ettiği id geçersizleşir
       └─ YENİ id + veri kopyalanır
  → MM_* kullanıcı key'leri yazılır     SessionKeys:7-15
  → csrf_token üretimi korunur/muayene  SessionLifecycle:47-49
```

| Çağrı sitesi | Bağlam |
|---|---|
| `SessionBootstrapper:58` | restartFresh (bozuk/timeout/privilege) |
| `SessionLifecycle:60` | 30 dk rotation (⚠️ D-2: pratikte ulaşılamaz) |
| `SessionInitializer:54, 76` | PageRouter session kurulumu |
| `auth SessionManager:81` | `regenerateId()` (login yolu çağırır — ⚠️ çağıran U-4) |
| `auth SessionManager:154` | `rotateIfNeeded` |
| `ApiSessionManager:85` | API session upgrade |
| `HomeSessionManager:74, 147` | home session yönetimi |

**Yasak (§11.1):** yeni oturum veren hiçbir yol `regenerate_id` çağırmazsa fixation
açık — 9 site dışında kalan login yolu **aranmalı** (dobrulama testi U-4).

### 6.2 Lifecycle sırası (her istek)

```
SessionManagerMiddleware:15-24
  1. SessionBootstrapper::ensureStarted()        (T-01: tek giriş)
  2. SessionLifecycle::startOrExtend(nonce)      SessionLifecycle:21-69
        lifetime → idle → csrf_token → csp_nonce → rotation → last_activity
  3. request['_session'] = getSessionData()       (:21)
  ▶ ...
```

## 7. Middleware Sırası & CSRF Entegrasyonu

**Web** (`PageRouterKernel:330-341`, sıra değişmez):

| # | Middleware | Session/CSRF rolü |
|---|---|---|
| 1 | OriginCheck | cross-origin ön kapı |
| 2 | Cors | header yayıcı |
| 3 | RateLimiter | throttle |
| 4 | SecurityHeaders | header set |
| 5 | **SessionManager** | boot + lifecycle (:335) |
| 6 | **Csrf** | state-changing doğrulama (:336) |
| 7 | BypassAuth | (ADR-008) |
| 8 | Auth | kimlik |
| 9 | Permission | yetki |
| 10 | Validation | şema |

**API** (`api/index.php`):

```
:114-116  /api/v1/auth/* → SessionBootstrapper::ensureStarted (yalnız auth rotası)
:121-127  x-csrf-token header pipeline'a taşınır
:147-171  pipeline: Normalization → OriginCheck → Cors → RateLimit → Authentication
                       → [CSRF kapısı :164-169] → Validation → Authorization
:165      if ((_auth_user.method ?? '') !== 'session') → CSRF ATLA
:168      aksi halde CsrfMiddleware::handle
```

**Karar T-10 gerekçesi** (kod yorumu, `index.php:160-163`): Bearer/API-key istemcisi
cookie kullanmaz; tarayıcı cross-site'te özel header forging yapamaz; ek katmanlar
`SameSite=Lax` + `OriginCheck`. **⚠️ Z-3:** koşul `method==='session'` string
eşleşmesi — `NameConfusion` (özel method/alan uyuşmazlığı) senaryosu doğrulanmalı.

## 8. Kill / Logout / Restart

| Aşama | Kanıt |
|---|---|
| `destroy()`: `restartFresh` + key re-init | SessionLifecycle:71-75 |
| restartFresh: `$_SESSION=[]` → destroy → cookie expire → start → regenerate | Bootstrapper:34-59 |
| Logout (auth) tam temizlik | `auth…/SessionManager` (kapsam dışı satır — U-6) |
| Display cookie temizliği | `clearDisplayCookies` :161-169 (⚠️ Z-5) |
| Yazma kapanışı | `writeClose` Bootstrapper:61-66 |

**Kural:** logout/destroy sonrası **eski id ölüdür** (destroy + regenerate) — "ölü id"
kullanılamaz.

## 9. Tehdit Modeli

| Tehdit | Savunma | Kanıt | Boşluk |
|---|---|---|---|
| Session fixation | `regenerate_id(true)` (9 site) + restartFresh | #24, #8 | Z-2: `use_strict_mode` yok |
| Session hijack (XSS) | `httponly=true` | SessionConfig:59 | display cookies: Z-5 |
| CSRF | hash_equals + 403 + Lax + OriginCheck | CsrfMiddleware:42,49-64 | Z-3 (API kapsamı) |
| Cookie sızıntısı (log) | Redactor `session_id|session` maskesi | Redactor:31 | — |
| Uzun ömürlü çalınmış id | timeout + rotation | §4 | ⚠️ D-1/D-2: idle/rotation ölü olabilir |
| Alt-domain cookie tossing | domain `.coremusic.net` paylaşımlı | SessionConfig:45 | Z-4 |
| HTTP sızıntısı | `secure` dinamik | SessionConfig:38-48 | proxy yapılandırması U-8 |
| API'de oturum çalınması | `method==='session'` + auth pipeline (401 önce koşar) | api:164-169 · surucu §3 | U-3: login→regenerate API'de doğrulanmadı |
| Log'a PII sızması (`MM_*`) | Redactor `session` deseni → `[REDACTED]` | Redactor:31,28 | Z-5: display-cookie maskesi doğrulanmadı |
| CSRF token replay | token oturum yaşam döngüsünde geçerli (üretim: Lifecycle:47-49) | SessionLifecycle:47-49 | ⚠️ istek-başına token rotation kodda doğrulanmadı → UNKNOWN |

> Kapsam sınırı: yalnız PHP kod kanıtı modellenir; sunucu `php.ini` (Z-2), proxy (U-8)
> ve WAF/gateway davranışı bu belgenin dışındadır.

## 10. Zayıflıklar (Z)

| # | Bulgu | Kanıt | ⚠️ |
|---|---|---|---|
| Z-1 | `MAX_LIFETIME < IDLE_TIMEOUT` + sıra → idle ve rotation **pratikte ölü kod** (D-1/D-2) | SessionConfig:17-19 · Lifecycle:31-62 | ⚠️ VERIFICATION REQUIRED |
| Z-2 | `session.use_strict_mode` kodda **hiç set edilmiyor** (grep 0) — savunma yalnız regenerate | grep kanıtı | ⚠️ sunucu php.ini UNKNOWN |
| Z-3 | API CSRF yalnız `method==='session'` — NameConfusion açığı | api:164-169 | ⚠️ doğrulama |
| Z-4 | Cookie domain tüm subdomain'ler | SessionConfig:45 | ⚠️ kasıtlı mı? |
| Z-5 | Display cookie'ler `httponly` **değil** (5 arg setcookie) | auth SessionManager:166 | ⚠️ doğrulama |
| Z-6 | `ROTATION_INTERVAL == MAX_LIFETIME` — iki mekanizma çakışıyor | SessionConfig:17,19 | ⚠️ Z-1'in parçası |
| Z-7 | CSRF 403 gövdesi `csrf_invalid` — ip/uri yalnız **log'da** (good) | CsrfMiddleware:52-63 | ✅ sızıntı yok |

## 11. Bilinmeyenler (U)

| # | Soru | Durum |
|---|---|---|
| U-1 | ✅ `secure` flag: dinamik HTTPS algılama **doğrulandı** (önceki U-1 kapandı) | kapatıldı |
| U-2 | Z-1 değer çelişkisi kasıtlı mı? (idle 1800 mi isteniyordu?) | ⚠️ karar bekliyor |
| U-3 | API session rotalarında login → regenerate çağırıyor mu? (`ApiSessionManager:85` çağıran?) | ⚠️ doğrulama |
| U-4 | `regenerateId()` (auth) çağıranları: login yolu dahil mi? | ⚠️ doğrulama |
| U-5 | `rotateIfNeeded` çağıranları ve interval değeri | UNKNOWN |
| U-6 | Logout akışının tam satırları (auth SessionManager destroy) | UNKNOWN |
| U-7 | `SESSION_LIFETIME` **config katmanında okunuyor** (auth `constants.php:47`+`app.php:22` · api `constants.php:92`+`app.php:21`); ancak auth session'ı `SessionBootstrapper::ensureStarted()` **parametresiz** çağırıyor (`auth index.php:107,162`) → cookie/gc'ye iletimi kanıtlanmadı. `SESSION_COOKIE_DOMAIN` tüketimi ayrıca UNKNOWN | ⚠️ VERIFICATION REQUIRED (kısmi) |
| U-8 | Proxy arkasında `secure` doğrulaması (TRUSTED_PROXIES `.env.example:42`) | UNKNOWN |

## 12. Yasaklar (Fail-Closed)

1. Yeni oturum/login yolu `session_regenerate_id(true)` **çağırmadan** bitmek — yasak (fixation).
2. `httponly=false` veya `samesite=None` — yasak.
3. CSRF token'ı `==`/`===` ile karşılaştırmak — yasak; yalnız `hash_equals`.
4. CSRF reddini gözetlemek/403 yerine sessiz geçmek — yasak (CsrfMiddleware:51 notu).
5. Logout'ta yalnız veri silip eski id'yi canlı tutmak — yasak (destroy + regenerate).
6. `session_*` API'sini `SessionBootstrapper` dışında çağırmak — yasak (T-01 SSOT).

## 13. Terimler (Türkçe / English / Kod)

| Türkçe | English | Kod Karşılığı |
|---|---|---|
| Oturum sabitleme | Session fixation | `session_regenerate_id(true)` — 9 site |
| Mutlak ömür | Absolute lifetime | `MAX_LIFETIME` — `SessionConfig:17` |
| Boşta kalma zaman aşımı | Idle timeout | `IDLE_TIMEOUT` — `SessionConfig:18` |
| Oturum kimliği rotasyonu | Session ID rotation | `ROTATION_INTERVAL` — `SessionConfig:19` |
| İstek sahte-içerik token'ı | CSRF token | `$_SESSION['csrf_token']` — `SessionLifecycle:47-49` |
| Zaman-güvenli karşılaştırma | Timing-safe compare | `hash_equals` — `CsrfMiddleware:42` |
| Oturum yeniden başlatma | Fresh restart | `restartFresh` — `SessionBootstrapper:34-59` |
| Çapraz-site kaynak kısıtı | SameSite | `Lax` — `SessionConfig:60` |
| JavaScript-erişilemez | HttpOnly | `true` — `SessionConfig:59` |

## 14. Session Veri Sınıflandırması (hangi key ne kadar hassas)

| Key grubu | Örnek | Hassasiyet | Log maskesi |
|---|---|---|---|
| Kimlik | `MM_UserID`, `MM_Email` | PII — log'a yazılmaz (auth yasağı) | `Redactor:31` (`session` deseni) |
| Oturum denetimi | `_session_created_at`, `_session_last_active`, `_session_rotated_at` | orta (zaman bilgisi) | yazım yasağı zorunlu değil |
| Güvenlik token'ı | `csrf_token`, `csp_nonce` | **yüksek** — çalan saldırgan CSRF/XSS yapar | `Redactor:31` (`csrf_token`) |
| Yardımcı | `cm_gender`, `_pending_redirect_uri` | düşük | — |
| Üretim | `last_activity` | düşük | — |

**Kural:** `csrf_token`/`csp_nonce` hiçbir yanıt gövdesinde/dış log'a çıkmaz
(session/cookie log yasağı — `auth AGENTS.md` yasak 3; maske `Redactor:31`).

## 15. Test Gereksinimleri (`session-security-dogrulama` için)

| # | Test | Kriter |
|---|---|---|
| T-1 | Login前后 session id | id **değişir** (fixation reddi) |
| T-2 | `httponly`/`samesite`/`secure` bayrakları | Set-Cookie'de `HttpOnly; SameSite=Lax`; HTTPS'te `Secure` |
| T-3 | 30 dk oturum + 31 dk bekleme | ⚠️ Z-1: hangisi kırar — lifetime mı idle mı ölçülür |
| T-4 | rotation eşiği | ⚠️ D-2: 31. dk'da id değişiyor mu? (değişmiyorsa dead-code teyidi) |
| T-5 | CSRF'siz POST | 403 `csrf_invalid` + security.log'a `csrf_invalid` satırı |
| T-6 | Doğru token'lı POST | 200, `hash_equals` yolu |
| T-7 | Timing | token farklı uzunlukta → `hash_equals` false, exception yok |
| T-8 | logout sonrası eski id | erişilemez (destroy + regenerate) |
| T-9 | `use_strict_mode` | ⚠️ Z-2: `ini_get` + sunucu php.ini ölçümü |
| T-10 | API Bearer POST | CSRF **atlanır** (kapsam: `_auth_user.method='session'` değil) |
| T-11 | API session POST + `method` alanı uyuşmazlığı | ⚠️ Z-3 NameConfusion senaryosu |
| T-12 | Display cookie `MM_UserID` | ⚠️ Z-5: `HttpOnly` var mı? |
| T-13 | restartFresh çağrısı | yeni id + `csrf_token` yeniden üretilir |
| T-14 | Ölü id senaryosu | saldırganın servis ettiği id login sonrası kapanır |
| T-15 | `Set-Cookie` lifetime | `Max-Age`/`Expires` yok (lifetime=0, session cookie) |
| T-16 | İki sekme + restartFresh | ikisi de yeni id'ye düşer (cookie paylaşımı) |
| T-17 | `X-Forwarded-Proto: https` | `secure=true` (SessionConfig:38-41 dalı) |
| T-18 | `samesite=Lax` doğrulaması | cross-site POST'ta cookie gitmez (Z-3 ek katman) |
| T-19 | `csrf_token` uzunluğu | 64 hex (`bin2hex(random_bytes(32))` — Lifecycle:48) |
| T-20 | `MM_*` display cookie süresi | logout'ta expire edilir (auth:161-169) |

## 16. İlişkiler

```
K009-security/secrets/secrets-anayasa ──(csrf_token / session verisi güvenliği)──▶ session-security
K009-security/encryption/encryption-anayasa ──(secure/HTTPS + cookie)──────────────▶ session-security
session-security ──▶ rate-limiting-kapsam (oturum bazlı throttle) ──▶ authorization-kapsam
```

**BAĞLANTILAR:** [[session-security-kapsam]] · [[session-security-ornekler]] ·
[[session-security-dogrulama]] · [[session-security-bilesenler]] ·
[[session-security-web-arastirma]] · [[authentication-kapsam]] ·
[[authorization-kapsam]] · [[security-master]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
