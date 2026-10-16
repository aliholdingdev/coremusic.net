---
node_id: K009-security/rate-limiting/rate-limiting-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-anayasa
  - K009-security/rate-limiting/rate-limiting-ornekler
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php
  - shared/src/Security/CacheRateLimiter.php
  - shared/src/Api/Middleware/RateLimitMiddleware.php
  - shared/tests/Middleware/RateLimiterMiddlewareTest.php
  - auth.coremusic.net/include/Service/AuthService.php
  - auth.coremusic.net/include/Controller/AuthController.php
validation:
  - AD-01..AD-14 bu dosyada tanımlı
open_questions:
  - AD-06 set() isNew runtime kanıtı yok ⚠️
  - AD-11 MemoryAdapter worker recycle senaryosu test edilmedi ⚠️
  - AD-13 trusted proxy deploy yapılandırması UNKNOWN
group: kanit
---

# K009 · rate-limiting · Doğrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kanıt katmanı — AD-xx kontrol tablosu

Her satır bir iddiayı **dosya + satır kanıtıyla** sınar. Durum kodları:

- ✅ **KANITLI** — disk kanıtı satır satır okundu, iddia birebir doğrulandı.
- ⚠️ **VERIFICATION REQUIRED** — kod okumasından çıkarım; runtime/ortam
  testi gerekir.
- **UNKNOWN** — kanıt diskte yok, doğrulanamıyor.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-anayasa]] | Sınanan kararlar |
| [[rate-limiting-ornekler]] | Kanıt bloklarının tam hâli |
| [[rate-limiting-surucu]] | Akış iddiaları |
| [[rate-limiting-web-arastirma]] | Standart (RFC/CWE/MDN) dayanakları |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. AD-xx Kontrol Tablosu

| AD | Kontrol | Beklenen | Kanıt (Dosya·Satır) | Durum |
|---|---|---|---|---|
| AD-01 | Fail-closed yol listesi 9 uç içeriyor | `/login`, `/register`, `/forgot-password`, `/reset-password`, `/logout`, `/set-gender`, `/select-gender`, `/session`, `/validate-key` | `shared/src/Middleware/RateLimiterMiddleware.php:20-30` | ✅ KANITLI |
| AD-02 | Depo yoksa auth ucu 503 + `Retry-After` döner | `httpStatus: 503`, `Retry-After: windowSeconds`, `halt: true` | `RateLimiterMiddleware.php:53-65` | ✅ KANITLI |
| AD-03 | Depo yoksa genel uç fail-open (istek geçer) | `$next($request)` çağrısı + `fail-open` securityEvent | `RateLimiterMiddleware.php:66-68` | ✅ KANITLI (davranış) · ⚠️ (anayasal statü — §2) |
| AD-04 | Eşik aşımında 429 + `Retry-After` üretilir | `429`, `rate_limit_exceeded`, `Retry-After: 60`, `halt: true` | `RateLimiterMiddleware.php:86-100` · test `RateLimiterMiddlewareTest.php:52-73` | ✅ KANITLI |
| AD-05 | Sayaç anahtarı `rl:` öneki + md5(IP) | `CACHE_KEY_PREFIX = 'rl:'` | `RateLimiterMiddleware.php:11,72` · test `:207` (`assertStringStartsWith('rl:')`) | ✅ KANITLI |
| AD-06 | `set()` dönüşü "yeni kayıt" olarak mı kullanılıyor | `MemoryAdapter::set()` daima `true` ise `increment` yolu hiç çalışmaz → sayaç her istekte sıfır | `RateLimiterMiddleware.php:74-77` (varsayım) · `shared/src/Cache/MemoryAdapter.php` · `shared/src/Cache/ApcuAdapter.php` | ⚠️ VERIFICATION REQUIRED — runtime test gerekli (kritik) |
| AD-07 | `increment` false/null dönerse istek fail-open | `$next($request)` + `rate_limiter_increment_failed` logu | `RateLimiterMiddleware.php:80-84` · test `:105-127,130-152` | ✅ KANITLI |
| AD-08 | Eşik karşılaştırması strict `>` (61. istekte 429) | Tam 60. istek hâlâ geçer | `RateLimiterMiddleware.php:86` · test `testRateLimitExactlyAtMaxAllowed:76-98` | ✅ KANITLI |
| AD-09 | Auth servisi iki boyutlu sayaç kullanır (IP + kimlik) | Çift `isLimited` (önce) + çift `increment` (sonra) | `auth.coremusic.net/include/Service/AuthService.php:65-66,77-78` | ✅ KANITLI |
| AD-10 | Auth eşikleri: login 5/900, register 3/3600, reset 3/3600 | Sabitler kodda birebir | `AuthService.php:31-34,40-41` | ✅ KANITLI |
| AD-11 | APCu yoksa MemoryAdapter → süreç içi sayaç → worker recycle'ta sayaç kaybı | Fallback yapısı `index.php`'de; kayıp senaryosu test edilmemiş | `api.coremusic.net/index.php:140-164` | ⚠️ VERIFICATION REQUIRED (kayıp senaryosu runtime) |
| AD-12 | Trusted proxy yoksa `X-Forwarded-For` yok sayılır | Test `testUntrustedProxyUsesRemoteAddr` bunu doğrular | `RateLimiterMiddleware.php:105-127` · test `:243-272` | ✅ KANITLI |
| AD-13 | Deploy'da trusted proxy listesi hangi IP'ler? | `TRUSTED_PROXIES` sabiti / varsayılan `['127.0.0.1','::1']` | `RateLimiterMiddleware.php:45-46` (varsayılan okundu) · canlı ortam değeri | UNKNOWN (canlı ortam kanıtı yok) |
| AD-14 | `CacheRateLimiter` için birim testi var mı? | `shared/tests/` altında ayrı dosya beklenir | 2026-10-15 araması: `RateLimiterMiddlewareTest.php` dışında rate-limit test dosyası **yok** | ⚠️ TEST BOŞLUĞU |

**Sayım:** 14 satır · ✅ KANITLI: 8 · ⚠️: 5 · UNKNOWN: 1.

---

## 2. Veto İlişkisi (AD-03 ve AD-06)

`.ai/AGENTS.md` §18 (index.md open_questions atfı) "fail-open tespiti"
ifadesini veto eşiği olarak tanımlar. Bu tablo açısından:

1. **AD-03** → fail-open davranışı **kodda mevcut ve kasıtlı**
   (ADR-013 §3 atfı ile satır içi yorumda belirtilmiş,
   `RateLimiterMiddleware.php:16,66`). Davranışın kendisi KANITLI;
   anayasal kabulü ayrı bir karar sorusudur ⚠️.
2. **AD-06** → eğer `set()` her zaman `true` dönerse **istisnasız tüm
   genel uçlar** fail-open olur — bu, kasıtlı ADR-013 fail-open'ından
   (depo çökmesi senaryosu) **farklı ve kapsamı çok daha geniş** bir
   boşluktur. Kanıtlanırsa doğrudan veto gerekçesi ⚠️.

> Doğrulama adımı (öneri, bu belge kapsamı dışı ⚠️): `MemoryAdapter` ve
> `ApcuAdapter` üzerinde `set()` iki kez çağrılıp dönüş değerleri
> karşılaştırılmalı; `RateLimiterMiddlewareTest`'e "ikinci istek
> sayacı arttırır mı?" testi eklenmelidir.

---

## 3. Test Envanteri Kanıtı

**Dosya:** `shared/tests/Middleware/RateLimiterMiddlewareTest.php` (319 satır, 10 test)

| # | Test | Satır | Durum |
|---|---|---|---|
| 1 | `testFirstRequestAlwaysAllowed` | 23-45 | ✅ mevcut |
| 2 | `testRateLimitExceededReturns429` | 52-73 | ✅ mevcut |
| 3 | `testRateLimitExactlyAtMaxAllowed` | 76-98 | ✅ mevcut |
| 4 | `testCacheIncrementFailureAllowsRequest` | 105-127 | ✅ mevcut |
| 5 | `testCacheIncrementNullAllowsRequest` | 130-152 | ✅ mevcut |
| 6 | `testNoCacheAvailableAllowsRequest` | 159-176 | ✅ mevcut |
| 7 | `testClientIpFromRemoteAddr` | 183-207 | ✅ mevcut |
| 8 | `testTrustedProxyXForwardedFor` | 210-240 | ✅ mevcut |
| 9 | `testUntrustedProxyUsesRemoteAddr` | 243-272 | ✅ mevcut |
| 10 | `testCustomMaxRequests` | 279-298 | ✅ mevcut |

**Kapsam boşlukları:**

- Test #4/#5/#6 fail-open'ı **doğrular gibi yazılmış** ("should allow
  request (fail-open)") → proje testleri fail-open'ı **kabul eden** konumdadır.
- `CacheRateLimiter::isLimited/increment/reset` için test yok ⚠️.
- Auth servisindeki iki boyutlu sayaç (`AuthService.php:65-78`) için
  rate-limit testi görülmedi ⚠️ (auth testleri ValueObject ağırlıklı).

---

## 4. Bilinen Sınır ve Bilinmeyenler

| Konu | Durum |
|---|---|
| Fail-closed 9 uç, 503, Retry-After | ✅ KANITLI (AD-01, AD-02) |
| 429 + Retry-After (middleware + controller) | ✅ KANITLI (AD-04 · `AuthController.php:58-62`) |
| İki boyutlu auth sayaç | ✅ KANITLI (AD-09) |
| `set()`/`isNew` sayaç sürekliliği | ⚠️ VERIFICATION REQUIRED (AD-06) |
| MemoryAdapter süreç-içi kayıp | ⚠️ VERIFICATION REQUIRED (AD-11) |
| Canlı ortam trusted-proxy listesi | UNKNOWN (AD-13) |
| ADR-013 sliding window / token bucket uygulaması | ⚠️ kodda rastlanmadı (anayasa §1.2) |
| `CacheRateLimiter` testi | ⚠️ yok (AD-14) |

---

## 5. Yeniden Doğrulama Protokolü (nasıl tekrarlanır)

1. **Kod kanıtı (bu belge):** ilgili Dosya·Satır satırları okunur; satır
   numarası kaymışsa tablo güncellenir (yalnız satır, iddia değil).
2. **Test kanıtı:** `shared/tests/Middleware/RateLimiterMiddlewareTest.php`
   suite'i çalıştırılır; AD-04, AD-07, AD-08, AD-12 için test geçişi
   yeterlidir (test adı + satır §3'te).
3. **Runtime kanıtı (AD-06, AD-11 için zorunlu):**
   - `MemoryAdapter` üzerinde ardışık iki `set($key,1,60)` → 2. dönüş
     `false` olmalı (isNew semantiği); `true` çıkarsa AD-06 **kanıtlanmış
     fail-open** olur.
   - Aynı test `ApcuAdapter` ile tekrarlanır (APCu ortamında).
   - `RateLimiterMiddleware`'e "2. istek 429'a ilerler mi" testi eklenir.
4. **Ortam kanıtı (AD-13):** deploy `TRUSTED_PROXIES` değeri okunur →
   UNKNOWN kapanır.
5. **Durum güncelleme:** her madde için Durum sütunu yeniden yazılır;
   ⚠️ → ✅ yalnız kanıt eklenerek olur, metinle değil.

**Değişiklik kuralı:** bu belge `status: verify-required` taşır; AD-06 ve
AD-11 ⚠️ iken `active` yapılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
