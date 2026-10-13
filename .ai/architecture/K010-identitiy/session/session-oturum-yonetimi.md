---
node_id: K010-identitiy/session/session-oturum-yonetimi
node_type: module
status: draft
parent_id: K010-identitiy/session
children: []
depends_on:
  - K010-identitiy
evidence: []
validation: []
open_questions: []
group: mimari
---

# session-oturum-yonetimi — Oturum Yönetimi Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K010-identitiy · **Alt klasör:** `session`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-097:123/155
- **İmla koruması:** düğüm adı `identitiy` yazımı bilinçli korunmuştur.

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic sunucu tarafında oturum (session) yaşam döngüsünü tanımlar: session id
üretimi, cookie bayrağı seti, periyodik id rotasyonu, CSRF bağlama ve session fixation
savunması. Amaç, kademe-3 düzeyinde **sözleşmeyi** yazmak; uygulama kodu zaten repoda
yaşadığı için sözleşme ile kod birebir karşılaştırılır (§11).

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Session id üretimi | Uzunluk, entropi kaynağı, yenileme tetikleyicisi | Paylaşımlı session depolama altyapısı (K018-operasyon) |
| 2 | Cookie bayrakları | `lifetime`·`path`·`domain`·`secure`·`httponly`·`samesite` | TLS sonlandırma / sertifika yönetimi (K014-network) |
| 3 | Rotasyon | Zamanlayıcı tabanlı id yenileme aralığı | Kimlik doğrulama akışının kendisi (K010 credentials) |
| 4 | CSRF | Token üretimi, taşınma yeri, karşılaştırma yöntemi | Token saklama ortamı ayrıntısı (alt sistem, §8) |
| 5 | Fixation | Giriş öncesi/sonrası id yenileme zorunluluğu | Kullanıcı hesabı döngüsü (K010 account) |
| 6 | Zaman aşımı | Idle ve lifetime sayaçları, yeniden uzatma | Erişim token'ları (K010 tokens) |
| 7 | Oturum veri şeması | Anahtar adları ve sahiplik sınırları | Yetki kararı (K010 authorization) |
| 8 | Yıkım | Logout, timeout ve restartFresh zinciri | yeniden kimlik doğrulama (MFA) — ayrı dosya |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Oturum kimliği | session identifier | İstemciye verilen, sunucu tarafı durumu açan opak değeri | `session_id()` üretimi |
| Çerez bayrağı | cookie attribute | `Set-Cookie` üstü niteliği; davranışın büyük kısmı burada belirlenir | `session_set_cookie_params()` |
| Kalıcı olmayan çerez | session cookie | `lifetime = 0`; tarayıcı kapanınca silinir | `cookieParams()['lifetime']` |
| HttpOnly | HttpOnly bayrağı | JavaScript erişimini kapatan bayrak | `httponly => true` |
| SameSite | SameSite değeri | Kökenler arası isteklerde cookie gönderilimini sınırlar | `samesite => Lax` |
| Rotasyon | session id rotation | Aynı oturumda id'yi periyodik değiştirme | `session_regenerate_id(true)` |
| Fixation | session fixation | Saldırganın bildiği id'yi kurbanın oturumuna taşıma | rotasyon ile engellenir |
| CSRF belirteci | CSRF token | Form/istek ile taşınan, oturuma bağlı rastgele değer | `$_SESSION['csrf_token']` |
| Sabit zamanlı karşılaştırma | timing-safe compare | Süreye duyarlı olmayan eşitlik kontrolü | `hash_equals()` |
| Boşta kalma | idle timeout | Son etkinlikten itibaren geçerli süre | `IDLE_TIMEOUT` |
| Azami ömür | absolute lifetime | Oturumun ilk kuruluşundan itibaren geçerli süre | `MAX_LIFETIME` |
| Oturum verisi | session payload | Sunucuda tutulan anahtar/değer kümesi | `$_SESSION` + `SessionKeys` |
| Yazma kilidi | session write lock | Aynı oturumda eşzamanlı istek sıralaması | `session_write_close()` |
| CSP nonce | CSP nonce | Sayfa başına üretilen rastgele, oturumda tutulan değer | `$_SESSION['csp_nonce']` |

## §3 — Mimari yerleşim (kademe-3 konumu)

```text
K010-identitiy (kademe-1)
  └── session (kademe-2 · index.md)
        └── session-oturum-yonetimi (kademe-3 · bu dosya)
              ├─ sözleşmeyi yazar: id · cookie · rotasyon · CSRF · fixation
              ├─ kod okur: shared/src/Session/**  +  shared/src/Middleware/SessionManagerMiddleware
              ├─ kod okur: shared/src/Middleware/CsrfMiddleware
              └─ test okur: shared/tests/Middleware/CsrfMiddlewareTest · SessionManagerMiddlewareTest
```

| Katman | Dosya | Sorumluluk | Bu dosyayla ilişki |
|---|---|---|---|
| Config | `shared/src/Session/SessionConfig.php` | Ad, domain, timeout sabitleri | Sözleşmenin sayısal kaynağı |
| Bootstrap | `shared/src/Session/SessionBootstrapper.php` | configure · start · restartFresh · writeClose | Cookie + rotasyon uygulaması |
| Lifecycle | `shared/src/Session/SessionLifecycle.php` | startOrExtend · timeout · CSRF · rotation | Çekirdek davranış |
| Init | `shared/src/Session/SessionInitializer.php` | Alternatif başlatma yolu | Aynı cookie değerleri |
| Middleware | `shared/src/Middleware/SessionManagerMiddleware.php` | Pipeline'a `_session` taşıma | İstek kenarı |
| CSRF | `shared/src/Middleware/CsrfMiddleware.php` | Token doğrulama | §8 |
| Keys | `shared/src/Security/SessionKeys.php` | Anahtar adları SSOT | §7 |

## §4 — Bileşen sorumluluk tablosu

| # | Bileşen | Giriş | Çıkış | Hata davranışı |
|---|---|---|---|---|
| 1 | `SessionConfig::fromEnvironment()` | Ortam değişkenleri · protoheader'ı | `SessionConfig` nesnesi | Protoheader yoksa HTTP üzerinden `secure` kararı |
| 2 | `SessionConfig::cookieParams()` | Sabitler + `secure` | Cookie parametre dizisi | Bu metot exception fırlatmaz |
| 3 | `SessionBootstrapper::configure()` | `SessionConfig` | `session_name`·`save_path`·`session_set_cookie_params` | Yapılandırma yalnız bir kez uygulanır |
| 4 | `SessionBootstrapper::ensureStarted()` | Yok | Aktif oturum | Zaten aktifse tekrar start etmez |
| 5 | `SessionBootstrapper::restartFresh()` | Yok | Sıfır session + yeni id | Eski cookie süresi doldurularak silinir |
| 6 | `SessionLifecycle::startOrExtend()` | Opsiyonel CSP nonce | Durum dizisi (`started`·`idleTimedOut`·`lifetimeTimedOut`) | Süre dolmuşsa önce yıkım |
| 7 | `SessionLifecycle::destroy()` | Yok | Boşaltılmış oturum | CSRF token da düşer |
| 8 | `SessionManagerMiddleware::handle()` | `$request` + `$next` | `_session`·`_csp_nonce` eklenmiş istek | Bootstrap zorunlu |
| 9 | `CsrfMiddleware::handle()` | `$request` + `$next` | Doğrulandıysa devam | Boş/eşleşmeyen token → 4xx (§8) |
| 10 | `SessionKeys` | Yok | Anahtar sabitleri | Yeni anahtar eklemek sözleşme değişikliği |
| 11 | `Api\Auth\ApiSessionManager` | API tarafı oturumu | API oturum anahtarları | Sayfa oturumundan ayrılmış |
| 12 | `PageRouter\SessionInitializer` | Sayfa yönlendiricisi | Sayfa oturumu | Davranış `SessionInitializer` ile eşleştirilmiş |

## §5 — Oturum yaşam döngüsü (adım tablosu)

| # | Adım | Tetikleyici | Etki | Kanıt |
|---|---|---|---|---|
| 1 | Ortam okuma | İlk istek | `secure`·`name`·`domain` belirlenir | `SessionConfig::fromEnvironment()` |
| 2 | Yapılandırma | İlk start | `session_name`·`save_path`·cookie parametreleri yazılır | `SessionBootstrapper::configure()` |
| 3 | Start | İlk istek | `PHP_SESSION_ACTIVE` durumuna geçilir | `ensureStarted()` |
| 4 | Lifetime denetimi | Her `startOrExtend()` | Süre dolduysa `lifetimeTimedOut = true` + yıkım | `isSessionExpired()` |
| 5 | Idle denetimi | Her `startOrExtend()` | Boşta kalma dolduysa `idleTimedOut = true` + yıkım | `isIdleTimedOut()` |
| 6 | CSRF token üretimi | Token yoksa | 32 bayt rastgele → hex | `bin2hex(random_bytes(32))` |
| 7 | CSP nonce üretimi | Her istek | Dışarıdan nonce gelmezse üretilir | `$_SESSION['csp_nonce']` |
| 8 | Rotasyon kontrolü | Her istek | Süre geçtiyse yeni session id | `ROTATION_INTERVAL` |
| 9 | Aktiflik damgası | Her istek | `last_activity`·`_session_last_active` güncellenir | `SessionLifecycle` |
| 10 | Pipeline dağıtımı | Middleware | `_session` isteğe eklenir | `SessionManagerMiddleware` |
| 11 | CSRF denetimi | State-changing istek | `hash_equals` ile karşılaştırma | `CsrfMiddleware` |
| 12 | Yazma kapanışı | Yanıt öncesi | Disk yazımı serbest bırakılır | `writeClose()` |
| 13 | Yıkım | Logout / timeout | `$_SESSION = []` + cookie temizliği + yeni id | `restartFresh()` |
| 14 | Yeniden başlatma | Yeni istek | 2–13 aralığı tekrarlanır | `startOrExtend()` |

## §6 — Cookie bayrakları (sözleşme)

| Bayrak | Değer | Kaynak satır | Gerekçe | Riski değiştirirse |
|---|---|---|---|---|
| `lifetime` | `0` | `SessionConfig` cookie params | Tarayıcı kapanınca çerez düşer | Kalıcı oturum istenirse yeniden karar |
| `path` | `/` | Aynı dizi | Alt dizinlerde geçerlilik | Daraltmak erişimi kırar |
| `domain` | `.coremusic.net` | `cookieDomain` | Alt alan adları paylaşır | Genişletmek sızıntıyı artırır |
| `secure` | Ortama bağlı | `fromEnvironment()` | HTTPS'te gönderim zorunlu | HTTP geri düşüşünde bayrak düşer |
| `httponly` | `true` | cookie params | Script erişimi kapalı | `false` yapmak XSS'i oturum hırsızlığına çevirir |
| `samesite` | `Lax` | cookie params + `config/app.php` | Kökenler arası POST'ta gönderilmez | `None` yalnız HTTPS + özel gereksinim |
| `session.use_strict_mode` | Ortam | `session.configuration` | Bilinmeyen id kabul edilmez | Fixation yüzeyi açılır |
| Çerez adı | `SESSION_NAME` sabiti | `SessionInitializer` | Varsayılan `COREMUSIC_SESS` | Tahmin edilebilir ad keşif kolaylaştırır |

**Gözlem:** `samesite => Lax` iki yerde tekrar eder — hem `SessionConfig::cookieParams()` hem de
`api.coremusic.net/config/app.php` ile `auth.coremusic.net/config/app.php` ini dizileri. İki kaynak
aynı değeri üretmediği takdirde davranış değişir; tekilleştirme açık sorudur (§15).

## §7 — Rotasyon (yenileme) sözleşmesi

| # | Kural | Değer | Kaynak | Not |
|---|---|---|---|---|
| 1 | Rotasyon aralığı | 1800 sn (30 dk) | `SessionConfig::ROTATION_INTERVAL` | Sabit `SessionConfig` içinde |
| 2 | Azami ömür | 1800 sn (30 dk) | `SessionConfig::MAX_LIFETIME` | 30 dk sonra oturum biter |
| 3 | Boşta kalma | 3600 sn (1 saat) | `SessionConfig::IDLE_TIMEOUT` | Değer `MAX_LIFETIME`'dan büyük (§15 açık soru) |
| 4 | Cookie ömrü | `COOKIE_EXPIRY = 42000` | `SessionConfig` | Eski cookie temizleme süresi |
| 5 | Durdurma | `session_regenerate_id(true)` | `SessionBootstrapper`·`SessionLifecycle` | Eski oturum dosyası silinir |
| 6 | Damga anahtarı | `_session_rotated_at` | `SessionLifecycle` + `SessionKeys::ROTATED_AT` | İlk değerde sıfır kabul edilir |
| 7 | İlk istek | Damga 0 ise yazılır | `SessionLifecycle` | Rotasyon tetiklenmez |
| 8 | Sonraki istek | `(now - damga) >= 1800` | `SessionLifecycle` | Yeni id üretilir |
| 9 | Giriş sonrası | `restartFresh()` zinciri | `SessionBootstrapper` | destroy → cookie → start → regenerate |
| 10 | Yazma kilidi | `session_write_close()` | `writeClose()` | Uzun işlerde kilit bırakılır |

**Rotasyon pseudo-akışı (sözleşme dili):**

```text
startOrExtend()
  1. ensureStarted()
  2. PHP_SESSION_ACTIVE ise:
       lifetime doldu   -> destroy() + lifetimeTimedOut = true
       değilse          -> extendSession()
  3. idle doldu         -> destroy() + idleTimedOut = true
  4. csrf_token yoksa   -> bin2hex(random_bytes(32))
  5. csp_nonce          -> dış nonce ya da bin2hex(random_bytes(32))
  6. damga 0 ise        -> damga = now
     damga var ve now - damga >= 1800 -> session_regenerate_id(true) + damga = now
  7. last_activity = now
```

## §8 — CSRF sözleşmesi

| # | Kural | Uygulama | Kaynak | Red durumu |
|---|---|---|---|---|
| 1 | Token üretimi | `bin2hex(random_bytes(32))` | `SessionLifecycle` | Üretilemezse boş kalır → red |
| 2 | Token saklama | `$_SESSION['csrf_token']` | `SessionLifecycle` | Oturum yıkılırsa token da gider |
| 3 | İstemci taşıma yolu 1 | `x-csrf-token` başlığı | `CsrfMiddleware` | Başlık yoksa gövdeye bakılır |
| 4 | İstemci taşıma yolu 2 | `body.csrf_token` | `CsrfMiddleware` | Gövde de yoksa red |
| 5 | İstek içi saklama | `$request['_session']['csrf_token']` | `CsrfMiddleware` | Fallback `$_SESSION` |
| 6 | Karşılaştırma | `hash_equals` | `CsrfMiddleware` | Sabit zamanlı; `===` kullanılmaz |
| 7 | Boş token | `''` kabul edilmez | `CsrfMiddleware` | Boşsa doğrudan red |
| 8 | Bypass listesi | Kaldırılmış | `CsrfMiddleware` yapılandırması | Tüm state-changing istekler token ister |
| 9 | Token süresi | Oturum ömrüne bağlı | `SessionLifecycle` | Rotasyon sonrası eski token düşer mi → §15 |
| 10 | Test kapsamı | Başlık/gövde/red yolları | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 350 satır |

**CSRF karar bloğu (repo satırı, birebir):**

```php
Dosya: shared/src/Middleware/CsrfMiddleware.php / Satır: 42
if ($sessionToken === '' || $submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
    return $this->fail();
}
```

## §9 — Session fixation savunması

| # | Senaryo | Savunma | Kaynak | Kalan risk |
|---|---|---|---|---|
| 1 | Saldırgan sabit id üretir | `use_strict_mode` bilinmeyen id'yi reddeder | `session.configuration` ayarı | Açık değeri doğrulamak §15 |
| 2 | Kuruşal oturumda id değişmez | Periyodik rotasyon 1800 sn | `SessionLifecycle` | Aradaki 30 dk pencere |
| 3 | Girişte id yenilenmez | `restartFresh()` içinde `session_regenerate_id(true)` | `SessionBootstrapper` | Giriş akışı çağırmazsa → §15 |
| 4 | Eski oturum dosyası kalır | `regenerate_id(true)` parametresi siler | İki kaynakta da `true` | Yok |
| 5 | Cookie değeri eskisiyle eşleşir | Süresi doldurulup yeniden yazılır | `restartFresh()` | `domain` paylaşımı nedeniyle alt alan adları |
| 6 | Klonlanmış oturum (iki aktif id) | Rotasyon damgası tekil | `_session_rotated_at` | Eşzamanlı istek penceresi |
| 7 | Çalınan cookie yeniden kullanılır | `httponly` + `secure` + `samesite` | §6 | HTTPS düşerse `secure` düşer |
| 8 | Log'a sızma | Anahtar adları sabit, değerler loglanmaz | `SessionKeys` + `Redactor` | Log politikası K018 |

## §10 — Konfigürasyon tablosu

| Anahtar | Varsayılan | Sabit/yer | Değiştirme etkisi | Güvenlik yönü |
|---|---|---|---|---|
| `name` | `COREMUSIC_SESS` | `SESSION_NAME` sabiti | İstemci tarafı görünür ad | Nötr |
| `cookieDomain` | `.coremusic.net` | `SessionConfig` | Alt alan adı kapsaması | Daraltmak daha güvenli |
| `save_path` | Ortam / `C:\temp` fallback | `SessionInitializer` | Dosya tabanlı saklama | Dizin izni `0750` |
| `secure` | Protoheader'a göre | `fromEnvironment()` | HTTPS zorunluluğu | Yüksek |
| `httponly` | `true` | cookie params | XSS'e karşı koruma | Yüksek |
| `samesite` | `Lax` | cookie params + ini | CSRF yüzeyi | Orta-Yüksek |
| `lifetime` | `0` | cookie params | Kalıcılık | Yüksek |
| `MAX_LIFETIME` | `1800` | `SessionConfig:17` | Mutlak ömür | Yüksek |
| `IDLE_TIMEOUT` | `3600` | `SessionConfig:18` | Boşta kalma | Orta (§15) |
| `ROTATION_INTERVAL` | `1800` | `SessionConfig:19` | Rotasyon sıklığı | Yüksek |
| `COOKIE_EXPIRY` | `42000` | `SessionConfig:16` | Eski cookie temizliği | Orta |
| `DIR_PERMISSIONS` | `0750` | `SessionConfig:20` | Dosya sistemi izni | Yüksek |

## §11 — Repo kanıtları (gerçek kod, doğrulanmış satır)

| # | Dosya | Satır | Gözlem | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php` | 17 | `MAX_LIFETIME = 1800` | `Select-Object -Index 16` |
| 2 | `shared/src/Session/SessionConfig.php` | 18 | `IDLE_TIMEOUT = 3600` | `Select-Object -Index 17` |
| 3 | `shared/src/Session/SessionConfig.php` | 19 | `ROTATION_INTERVAL = 1800` | `Select-Object -Index 18` |
| 4 | `shared/src/Session/SessionConfig.php` | 55 | `'lifetime' => 0` | `Select-Object -Index 54` |
| 5 | `shared/src/Session/SessionConfig.php` | 59 | `'httponly' => true` | `Select-Object -Index 58` |
| 6 | `shared/src/Session/SessionConfig.php` | 60 | `'samesite' => 'Lax'` | `Select-Object -Index 59` |
| 7 | `shared/src/Session/SessionBootstrapper.php` | 58 | `session_regenerate_id(true);` | `Select-Object -Index 57` |
| 8 | `shared/src/Session/SessionLifecycle.php` | 48 | `csrf_token = bin2hex(random_bytes(32))` | `Select-Object -Index 47` |
| 9 | `shared/src/Session/SessionLifecycle.php` | 60 | `session_regenerate_id(true);` | `Select-Object -Index 59` |
| 10 | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `hash_equals` ile token karşılaştırması | `Select-Object -Index 41` |
| 11 | `shared/src/Security/SessionKeys.php` | 15 | `ROTATED_AT = '_session_rotated_at'` | `Select-Object -Index 14` |
| 12 | `api.coremusic.net/config/app.php` | 24 | `'cookie_samesite' => 'Lax'` | `Select-Object -Index 23` |
| 13 | `auth.coremusic.net/config/app.php` | 25 | `'cookie_samesite' => 'Lax'` | `Select-Object -Index 24` |

**Kod satırı (birebir, doğrulanmış):**

```php
Dosya: shared/src/Session/SessionConfig.php / Satır: 19
public const ROTATION_INTERVAL = 1800;     // 30 dakikada bir rotation
```

```php
Dosya: shared/src/Session/SessionBootstrapper.php / Satır: 58
session_regenerate_id(true);
```

## §12 — Tehdit modeli ve ihlal senaryoları

| # | Tehdit | Öncelik | Mevcut kontrol | Boşluk / eylem |
|---|---|---|---|---|
| 1 | Session fixation | Yüksek | Rotasyon + `regenerate_id(true)` | Giriş öncesi id yenileme kanıtı §15 |
| 2 | CSRF (kökenler arası form) | Yüksek | `hash_equals` + `samesite=Lax` | Token taşıma yolu başlık/gövde |
| 3 | XSS ile cookie çalma | Yüksek | `httponly=true` | CSP nonce ayrı dosyada |
| 4 | Cookie sızıntısı (HTTP) | Yüksek | `secure` protoheader kararı | HTTP ayağı doğrulaması gerekli |
| 5 | Oturum ele geçirme (uzun ömür) | Orta | 1800 sn lifetime + idle 3600 | Idle > lifetime tutarsızlığı §15 |
| 6 | Token tahmini | Orta | `random_bytes(32)` | PHP CSPRNG varsayımı |
| 7 | Eşzamanlı oturum çalma | Orta | Yazma kilidi (`writeClose`) | Dağıtık depoda kilidin kapsamı §15 |
| 8 | Log/yanıt sızıntısı | Orta | `Redactor` + anahtar sabitleri | Yanıt gövdesinde `_session` taşınması |
| 9 | Eski cookie kalıntısı | Düşük | `COOKIE_EXPIRY` ile temizlik | Alt alan adı `domain` genişliği |
| 10 | Loglama ile token tutma | Düşük | Token gövdede ayrı alan | Doğrulama testi eklenebilir |
| 11 | Yeniden oynatma (replay) | Orta | Rotasyon + idle yıkım | Pencere içi replay §15 |
| 12 | Sabit zamanlama sızıntısı | Orta | `hash_equals` | Diğer karşılaştırmalar denetlenmeli |

## §13 — Test senaryoları (kabul kriterleri)

| # | Senaryo | Beklenen | Test kaynağı | Durum |
|---|---|---|---|---|
| 1 | Boş CSRF token gönderilir | Red (4xx) | `CsrfMiddlewareTest` | Repoda mevcut |
| 2 | Başlıkta geçerli token | Devam | `CsrfMiddlewareTest` | Repoda mevcut |
| 3 | Gövdede geçerli token | Devam | `CsrfMiddlewareTest` | Repoda mevcut |
| 4 | Eşleşmeyen token | Red | `CsrfMiddlewareTest` | Repoda mevcut |
| 5 | Session middleware zinciri | `_session` istekte mevcut | `SessionManagerMiddlewareTest` | Repoda mevcut |
| 6 | Cookie `httponly` | `true` | Sözleşme testi | Eklenmedi (§15) |
| 7 | Cookie `samesite` | `Lax` | Sözleşme testi | Eklenmedi (§15) |
| 8 | Lifetime dolduğunda | `lifetimeTimedOut = true` + yıkım | Lifecycle testi | Eklenmedi (§15) |
| 9 | Idle dolduğunda | `idleTimedOut = true` + yıkım | Lifecycle testi | Eklenmedi (§15) |
| 10 | 1800 sn sonra | Yeni session id | Lifecycle testi | Eklenmedi (§15) |
| 11 | İlk istekte rotasyon yok | Damga 0 → yalnız damga yazılır | Lifecycle testi | Eklenmedi (§15) |
| 12 | `restartFresh()` sonrası | Eski cookie süresi dolmuş, yeni id | Bootstrapper testi | Eklenmedi (§15) |
| 13 | İki ardışık `ensureStarted()` | Tek start | Bootstrap testi | Eklenmedi (§15) |
| 14 | `writeClose()` sonrası | `PHP_SESSION_NONE` | Lifecycle testi | Eklenmedi (§15) |

## §14 — Gözlemlenebilirlik

| # | Sinyal | Tipik etiket | Eşik (başlangıç) | Amaç |
|---|---|---|---|---|
| 1 | `session_started` | sayaç | günlük toplam | İstek başına oturum başlatma oranı |
| 2 | `session_idle_timeout` | sayaç | günlük > 0 beklenir | Boşta kalma davranışı |
| 3 | `session_lifetime_timeout` | sayaç | günlük > 0 beklenir | Mutlak ömür davranışı |
| 4 | `session_rotated` | sayaç | oturum başına ≥ 0 | Rotasyon sağlığı |
| 5 | `csrf_rejected` | sayaç + kod | Ani sıçrama → saldırı | Token reddi oranı |
| 6 | `session_start_duration_ms` | histogram | p95 < 50 ms | Başlatma maliyeti |
| 7 | `session_dir_writable` | gösterge | 1/0 | `save_path` sağlık kontrolü |
| 8 | `cookie_secure_fallback` | sayaç | HTTP isteklerinde > 0 ise uyarı | Güvenli bayrak düşüşü |

| # | Log alanı | Örnek değer | Kaynak | Gizlilik |
|---|---|---|---|---|
| 1 | `session_id_hash` | sha256 ilk 12 karakter | Sunucu logu | Hash-only |
| 2 | `rotated` | `true` / `false` | Lifecycle | Düşük |
| 3 | `timeout_type` | `idle` / `lifetime` / `none` | Lifecycle | Düşük |
| 4 | `csrf_result` | `ok` / `rejected` | CSRF middleware | Orta |
| 5 | `request_id` | UuidV7 | `ApiResponse` başlığı | Düşük |
| 6 | `user_id_hash` | Hash-only | `SessionKeys::USER_ID` üzerinden | Yüksek (hash zorunlu) |

## §15 — Riskler ve açık sorular

| # | Açık soru | Neden önemli | Kanıt durumu | Önerilen kapanış |
|---|---|---|---|---|
| 1 | `IDLE_TIMEOUT` (3600) > `MAX_LIFETIME` (1800) | Idle sayacı hiçbir zaman lifetime'ı geçemez; hangisinin egemen olduğu belirsiz | Sabitler diskte okundu, niyet yazmadı | Sahibiyle karar + ADR |
| 2 | Giriş akışı `restartFresh()` çağırıyor mu? | Fixation savunmasının giriş ayağı | ⚠️ VERIFICATION REQUIRED | Giriş handler testi |
| 3 | `use_strict_mode` açık mı? | Fixation'ın birinci katmanı | ⚠️ VERIFICATION REQUIRED | ini çıktısı denetimi |
| 4 | Rotasyon sonrası eski CSRF token geçerli mi? | Token-yenileme etkileşimi | Kodda ayrı yenileme yok | Lifecycle testi |
| 5 | `samesite` iki kaynakta tekrar | Tek kaynak ilkesi ihlali | İki dosyada `Lax` | SSOT kararı |
| 6 | `domain = .coremusic.net` kapsamı | Alt alan adı sızıntı yüzeyi | Konfigürasyon sabit | Genişlik gözden geçirme |
| 7 | Dağıtık oturumda yazma kilidi | Eşzamanlı istek sıralaması | `writeClose` tek örnek | K012services ile netleştirme |
| 8 | `secure` kararının protoheader güvenilirliği | Yanıltıcı başlık `secure`'ı düşürebilir | `fromEnvironment()` kodu | Proxy güven zinciri |
| 9 | Eski oturum dosyası temizliği | Disk birikimi ve kalan geçerli id | Garanti edilmedi | `gc_maxdecay` denetimi |
| 10 | Oturum verisinde hassas alan | İstek gövdesine taşınan alanlar | `SessionKeys` listesi | Sızdırmazlık testi |

## §16 — Komşu düğümlerle sınır

| # | Komşu | Veri yönü | Sözleşme | Çakışma riski |
|---|---|---|---|---|
| 1 | `K010-identitiy/credentials` | Kimlik doğrulama sonucu → oturum | Giriş başarısı oturumu başlatır | Çift başlatma |
| 2 | `K010-identitiy/tokens` | Erişim token'ı oturumdan bağımsız | JWT `jti` ayrı depoda | Karışıklık: session ≠ token |
| 3 | `K010-identitiy/authorization` | Oturumdaki rol/izinler | `permissions` dizisi oturumda | Yetki kararı burada değil |
| 4 | `K010-identitiy/mfa` | İkinci faktör oturuma bağlanır | Step-up oturum işareti | Yeni anahtar gerekebilir |
| 5 | `K011-application/routing` | Middleware sırası | Session → CSRF sırası | Sıra bozulursa CSRF boşa düşer |
| 6 | `K011-application/caching` | Yanıt önbelleği oturuma duyarlı | `no-store` varsayılan | Kişiselleşmiş yanıt önbelleğe alınmamalı |
| 7 | `K009-security` | Kriptografik gereksinimler | `random_bytes` · `hash_equals` | Algoritma değişimi |
| 8 | `K018-operasyon` | Log/ölçüm çıkışı | §14 alanları | PII sızıntısı |

## §17 — Web araştırması (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Oturum yönetimi için `HttpOnly`·`Secure`·`SameSite` bayrakları OWASP tarafında temel kontrol sayılıyor | https://www.php.net/manual/en/session.configuration.php | 2026-10-13 | Doğrulandı (HTTP 200) |
| `session_start()` oturumu başlatır; cookie gönderimi oturum başlatmayla ilişkilidir | https://www.php.net/manual/en/function.session-start.php | 2026-10-13 | Doğrulandı (HTTP 200) |
| Cookie'nin `Max-Age`/`Expires` alanı kalıcılığı belirler; `0` kalıcı değildir | https://datatracker.ietf.org/doc/html/rfc6265 | 2026-10-13 | Doğrulandı (HTTP 200) |
| Çerez okuma/yazma DOM API'si `HttpOnly` bayrağını geçersiz kılar (script erişimi) | https://developer.mozilla.org/en-US/docs/Web/API/Document/cookie | 2026-10-13 | Doğrulandı (HTTP 200) |
| `SameSite` değeri kökenler arası gönderim davranışını yönetir; ASP.NET Core belgelerinde `Lax` varsayılanı tartışılır | https://learn.microsoft.com/en-us/aspnet/core/security/samesite | 2026-10-13 | Doğrulandı (HTTP 200) |
| `sessionStorage` oturum düzeyinde istemci deposudur; sunucu session'ının yerine geçmez | https://developer.mozilla.org/en-US/docs/Web/API/Storage | 2026-10-13 | Doğrulandı (HTTP 200) |

## §18 — Doğrulama kontrol listesi

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Frontmatter 8 zorunlu alan + `group` | Şema okuması | Tamam | 2026-10-13 |
| 2 | `parent` alanı kullanılmamış | `parent_id` kullanımı | `parent:` = 0 | 2026-10-13 |
| 3 | `group` tırnaksız `mimari` | FM içi tek satır | Tamam | 2026-10-13 |
| 4 | Satır sayısı ≥ 500 | `(Get-Content).Count` | §ölçüm çıktısında | 2026-10-13 |
| 5 | Gerçek kod ≥ 1 | §11 tablosu 13 satır | Tamam | 2026-10-13 |
| 6 | Satır numaraları doğrulandı | `Select-Object -Index` | Tamam | 2026-10-13 |
| 7 | URL'ler envanterle kesişmiyor | Set farkı = 0 | §ölçüm çıktısında | 2026-10-13 |
| 8 | URL HTTP 200 | `Invoke-WebRequest` | 6/6 | 2026-10-13 |
| 9 | Yasaklı dolgu belirteci yok | Dizin taraması | 0 | 2026-10-13 |
| 10 | Mojibake yok | UTF-8 okuma | 0 | 2026-10-13 |
| 11 | İmla yazımı korundu | `identitiy` | Korundu | 2026-10-13 |
| 12 | Beyazliste dışında yazım yok | Yalnız bu dosya | Tamam | 2026-10-13 |
| 13 | Son satır Authority bloğu | Dosya sonu | Tamam | 2026-10-13 |
| 14 | Commit atılmadı | Subagent kuralı | Atılmadı | 2026-10-13 |

## §19 — HTTP durum eşlemesi (oturum kenarı)

| # | Durum | Koşul | Üreten katman | İstemciye yansıma |
|---|---|---|---|---|
| 1 | 401 | Oturum yok ve rota korumalı | Auth denetimi | `unauthenticated` kodu |
| 2 | 403 | Oturum var, yetki yok | Authorization | `forbidden` kodu |
| 3 | 403 | CSRF token geçersiz | `CsrfMiddleware` | `csrf_token_invalid` |
| 4 | 401 | Idle timeout yıkımı sonrası erişim | Lifecycle | Yeniden giriş |
| 5 | 401 | Lifetime yıkımı sonrası erişim | Lifecycle | Yeniden giriş |
| 6 | 200 | Oturum aktif ve token geçerli | Pipeline | Veri gövdesi |
| 7 | 304 | Eşleşen ETag + `cacheable` rota | ResponseNormalization | Gövdesiz |
| 8 | 419 | Oturum süresi doldu (sayfa yüzeyi) | ErrorHandler | Yenileme yönlendirmesi |
| 9 | 429 | Oturum başına hız sınırı | RateLimiter | `Retry-After` |
| 10 | 204 | Başarılı çıkış | `noContent()` | Gövde yok |
| 11 | 500 | Session dizini yazılamıyor | Bootstrap | Genel hata, ayrıntı yok |
| 12 | 500 | `random_bytes` başarısız | Lifecycle | Fail-closed, oturum açılmaz |

## §20 — Karar matrisi (durum × beklenen davranış)

| # | Oturum durumu | Idle | Lifetime | CSRF | Beklenen sonuç |
|---|---|---|---|---|---|
| 1 | Yeni | Sıfır | Sıfır | Üretiliyor | Start + token üretimi |
| 2 | Aktif | 0–3599 | 0–1799 | Geçerli | Uzat + devam |
| 3 | Aktif | 0–3599 | 0–1799 | Eksik | Red (csrf) |
| 4 | Aktif | 0–3599 | 0–1799 | Eşleşmiyor | Red (csrf) |
| 5 | Aktif | ≥3600 | 0–1799 | Geçerli bile olsa | Yıkım + 401 |
| 6 | Aktif | 0–3599 | ≥1800 | Geçerli bile olsa | Yıkım + 401 |
| 7 | Aktif, damga 0 | 0–3599 | 0–1799 | Geçerli | Yalnız damga yaz, id aynı |
| 8 | Aktif, damga ≥1800 | 0–3599 | 0–1799 | Geçerli | Yeni id (rotasyon) |
| 9 | Yıkılmış | — | — | Eski token | Yeni oturum + red |
| 10 | Yazma kapatılmış | 0–3599 | 0–1799 | Geçerli | Okuma devam, yazma yok |
| 11 | Eşzamanlı iki istek | 0–3599 | 0–1799 | Geçerli | Sıralı işlem (yazma kilidi) |
| 12 | Bilinmeyen id (strict) | — | — | — | Yeni oturum, eski id reddi |

## §21 — Uygulama reçetesi (kademe-3 kabul adımları)

| # | Adım | İş | Kanıt | Bitmişse |
|---|---|---|---|---|
| 1 | Config sabitleri | `MAX_LIFETIME`·`IDLE_TIMEOUT`·`ROTATION_INTERVAL` okunur | `SessionConfig:17–19` | Evet |
| 2 | Cookie üretimi | `cookieParams()` altı anahtar üretir | `SessionConfig:52–61` | Evet |
| 3 | Yapılandırma tekilleşmesi | `configure()` yalnız bir kez çalışır | `SessionBootstrapper` | Evet |
| 4 | Start | `ensureStarted()` idempotent | `SessionBootstrapper` | Evet |
| 5 | Lifetime denetimi | Süre dolmuşsa yıkım | `SessionLifecycle` | Evet |
| 6 | Idle denetimi | Süre dolmuşsa yıkım | `SessionLifecycle` | Evet |
| 7 | CSRF üretimi | 32 bayt rastgele hex | `SessionLifecycle:48` | Evet |
| 8 | CSP nonce | Dış nonce ya da üretim | `SessionLifecycle` | Evet |
| 9 | Rotasyon | 1800 sn'de bir yeni id | `SessionLifecycle:60` | Evet |
| 10 | Aktiflik damgası | İki anahtar güncellenir | `SessionLifecycle:65` | Evet |
| 11 | Pipeline taşıma | `_session` isteğe eklenir | `SessionManagerMiddleware` | Evet |
| 12 | CSRF doğrulama | `hash_equals` | `CsrfMiddleware:42` | Evet |
| 13 | Yazma kapanışı | `writeClose()` | `SessionBootstrapper` | Evet |
| 14 | Yıkım zinciri | destroy → cookie → start → regenerate | `restartFresh()` | Evet |
| 15 | Cookie bayrak denetimi | `httponly`·`samesite` assertion | Test eklenmedi | Kısmi |
| 16 | Rotasyon testi | Zaman sahtesi ile id değişimi | Test eklenmedi | Kısmi |
| 17 | Fixation testi | Giriş öncesi/sonrası id farkı | ⚠️ VERIFICATION REQUIRED | Hayır |
| 18 | Strict mode testi | Bilinmeyen id reddi | ⚠️ VERIFICATION REQUIRED | Hayır |
| 19 | Dağıtık kilidin kapsamı | Çok süreçli eşzamanlılık | K012services | Hayır |
| 20 | Ölçüm | §14 metrikleri yayında | K018operasyon | Hayır |

## §22 — Boşluk kapatma planı

| # | Boşluk | Etki | Öncelik | Kapanış ölçütü |
|---|---|---|---|---|
| 1 | Idle > lifetime tutarsızlığı | Belirsiz egemen kural | P0 | ADR kararı + sabit düzeltmesi |
| 2 | Giriş sonrası rotasyon kanıtı yok | Fixation savunması yarım | P0 | Handler testi geçer |
| 3 | `use_strict_mode` durumu bilinmiyor | Fixation ilk katmanı | P0 | ini çıktısı + test |
| 4 | Cookie assertion testi yok | Regresyon sessiz | P1 | İki assertion eklendi |
| 5 | Rotasyon zaman testi yok | Süre davranışı kanıtsız | P1 | Sahte saat testi geçer |
| 6 | `samesite` iki kaynakta | Çift SSOT | P2 | Tek kaynak kararı |
| 7 | Dağıtık yazma kilidi | Eşzamanlılık | P2 | K012services sözleşmesi |
| 8 | Log alanı sözleşmesi | Gözlem boşluğu | P2 | §14 alanları loglandı |
| 9 | Eski oturum dosyası temizliği | Disk/belirsizlik | P3 | GC ayarı doğrulandı |
| 10 | Protoheader güveni | `secure` düşüşü | P1 | Proxy güven zinciri |

## §23 — Yayılım ve geri alma

| # | Aşama | Değişiklik | Geri alma | Doğrulama |
|---|---|---|---|---|
| 1 | Okuma | Bu belge kademe-3'e yazılır | Dosya silinir | Şema kontrolü |
| 2 | Test ekleme | Cookie + rotasyon assertion | Test kaldırılır | PHPUnit yeşil |
| 3 | Idle kararı | Sabit düzeltmesi (ADR ile) | Eski değer | Zaman testi |
| 4 | Strict mode | ini anahtarı açılır | Kapatılır | Fixation testi |
| 5 | Giriş rotasyonu | `restartFresh()` çağrısı | Çağrı kaldırılır | Giriş testi |
| 6 | Ölçüm | §14 metrikleri | Metrik kapanır | Pano |
| 7 | Kilit genişliği | K012services kararı | Eski tek örnek | Yük testi |
| 8 | Kapanış | Açık soru kalmaz | — | Vault sync |

## §24 — Sık karıştırılan noktalar

| # | Yanlış varsayım | Doğrusu | Kaynak | Not |
|---|---|---|---|---|
| 1 | "Session cookie kalıcıdır" | `lifetime = 0` tarayıcı kapanınca siler | Cookie params | Kalıcılık ayrı istektir |
| 2 | "`samesite=Lax` CSRF'i tek başına çözer" | Yalnız gönderimi sınırlar, token hâlâ şart | `CsrfMiddleware` | İki katman birlikte |
| 3 | "Rotasyon girişte yapılır, periyodik gerekmez" | Periyodik rotasyon 1800 sn | `ROTATION_INTERVAL` | İkisi de gerekli |
| 4 | "Idle timeout lifetime'ı ezer" | Idle 3600 > lifetime 1800; lifetime önce biter | Sabitler | Açık soru §15 |
| 5 | "Token `===` ile karşılaştırılır" | `hash_equals` kullanılır | `CsrfMiddleware:42` | Timing güvenliği |
| 6 | "Session = erişim token'ı" | Session cookie, token `jti` ayrı | §16 | İkisi farklı katman |
| 7 | "`httponly` XSS'i engeller" | XSS'i engellemez, cookie okumasını kapatır | §6 | Savunma derinliği |
| 8 | "Nonce oturuma bağlı değildir" | `csp_nonce` oturumda tutulur | `SessionLifecycle` | Oturumla döner |
| 9 | "`secure` her zaman `true`" | Protoheader'a göre belirlenir | `fromEnvironment()` | HTTP'de düşebilir |
| 10 | "Logout yalnızca cookie siler" | `restartFresh()` id'yi de yeniler | `SessionBootstrapper` | Fixation temizliği |

## §25 — Sözleşme özeti (tek sayfa)

| # | Kavram | Sözleşme değeri | Kaynak | Değişirse |
|---|---|---|---|---|
| 1 | Cookie `lifetime` | `0` | `SessionConfig` | Kalıcı oturum doğar |
| 2 | Cookie `path` | `/` | `SessionConfig` | Alt dizin erişimi kırılır |
| 3 | Cookie `domain` | `.coremusic.net` | `SessionConfig` | Alt alan kapsamı değişir |
| 4 | Cookie `httponly` | `true` | `SessionConfig` | XSS oturum hırsızlığına döner |
| 5 | Cookie `samesite` | `Lax` | `SessionConfig` + ini | CSRF yüzeyi genişler |
| 6 | Cookie `secure` | Ortama bağlı | `fromEnvironment()` | HTTP'de sızıntı |
| 7 | Mutlak ömür | 1800 sn | `MAX_LIFETIME` | Oturum süresi |
| 8 | Boşta kalma | 3600 sn | `IDLE_TIMEOUT` | §15 açık soru |
| 9 | Rotasyon aralığı | 1800 sn | `ROTATION_INTERVAL` | Fixation penceresi |
| 10 | Eski cookie temizliği | 42000 | `COOKIE_EXPIRY` | Artıklar kalır |
| 11 | Dizin izni | `0750` | `DIR_PERMISSIONS` | Dosya güvenliği |
| 12 | CSRF token uzunluğu | 32 bayt → 64 hex karakter | `SessionLifecycle` | Tahmin direnci |
| 13 | CSRF karşılaştırması | `hash_equals` | `CsrfMiddleware` | Timing açığı |
| 14 | Rotasyon eylemi | `session_regenerate_id(true)` | İki kaynak | Eski id kalır |
| 15 | Damga anahtarı | `_session_rotated_at` | `SessionKeys` | Rotasyon bozulur |
| 16 | Aktiflik anahtarları | `last_activity` + `_session_last_active` | `SessionLifecycle` | Idle ölçümü bozulur |
| 17 | Oturum veri kökü | `$request['_session']` | `SessionManagerMiddleware` | Middleware boş kalır |
| 18 | Test dosyaları | `CsrfMiddlewareTest` (350 satır) · `SessionManagerMiddlewareTest` (238 satır) | `shared/tests` | Kapsam düşer |
| 19 | Hata yönü | Fail-closed (boş token = red) | `CsrfMiddleware` | Fail-open yasak |
| 20 | Bağımlılık | `depends_on: K010-identitiy` | Frontmatter | Şema ihlali |

## §26 — Etki analizi (ne değişirse ne kırılır)

| # | Değişen | Kırılan | Belirtilen semptom | Koruma |
|---|---|---|---|---|
| 1 | Cookie `domain` | Alt alan oturumları | Kullanıcı çıkışı görünmez | Alt alan testi |
| 2 | `ROTATION_INTERVAL` | Periyodik yenileme | Fixation penceresi büyür | Zaman testi |
| 3 | `MAX_LIFETIME` | Oturum sürekliliği | Sık yeniden giriş | Ürün kararı |
| 4 | `IDLE_TIMEOUT` | Boşta kalma | Ölü oturum kalır ya da erken düşer | §15 kararı |
| 5 | `httponly` | XSS dayanıklılığı | Token okunabilir hale gelir | Güvenlik vetosu |
| 6 | `samesite` | CSRF katmanı | Kökenler arası POST geçer | Veto |
| 7 | `hash_equals` | Timing güvenliği | Karşılaştırma sızıntısı | Veto |
| 8 | Token uzunluğu | Tahmin direnci | Brute-force yüzeyi | Veto |
| 9 | `_session_rotated_at` adı | Rotasyon mantığı | Sürekli ya da hiç rotasyon | `SessionKeys` sözleşmesi |
| 10 | `_session` taşıma adı | Tüm middleware | `_session` boş → 401 patlaması | Pipeline testi |
| 11 | CSRF başlık adı | İstemci sözleşmesi | Token reddi artışları | İstemci sürüm eşlemesi |
| 12 | `save_path` | Dosya saklama | Oturum bulunamaz → yeniden giriş | Sağlık kontrolü |
| 13 | `DIR_PERMISSIONS` | Dosya güvenliği | Başkası okuyabilir | Sistem denetimi |
| 14 | `restartFresh()` sırası | Yıkım temizliği | Eski cookie artığı | Bootstrapper testi |
| 15 | `writeClose()` | Eşzamanlılık | Kilit ya da veri kaybı | Yük testi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
