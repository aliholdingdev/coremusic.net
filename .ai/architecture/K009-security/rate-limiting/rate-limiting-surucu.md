---
node_id: K009-security/rate-limiting/rate-limiting-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-anayasa
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php
  - shared/src/Security/CacheRateLimiter.php
  - shared/src/Api/Middleware/RateLimitMiddleware.php
  - auth.coremusic.net/index.php
  - api.coremusic.net/index.php
validation:
  - AD-05..AD-14 (rate-limiting-dogrulama.md)
open_questions:
  - increment yolu set() true dönerse hiç tetiklenmiyor olabilir ⚠️ VERIFICATION REQUIRED
  - MemoryAdapter fallback sırasında sayaç kaybı senaryosu runtime test edilmedi ⚠️ VERIFICATION REQUIRED
group: mimari
---

# K009 · rate-limiting · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari katman — istek/sayaç akış sürücüsü

Bu belge, bir isteğin **rate limiting katmanından geçişini**, sayaç
yaşam döngüsünü, durum geçişlerini ve hata dallarını adım adım anlatır.
Kararlar `[[rate-limiting-anayasa]]`, kod alıntıları
`[[rate-limiting-ornekler]]` içindedir.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-anayasa]] | Eşikler, fail-safe matrisi, 429 sözleşmesi |
| [[rate-limiting-ornekler]] | Her adımın gerçek PHP karşılığı |
| [[rate-limiting-bilesenler]] | Sürücüyü oluşturan bileşenler |
| [[rate-limiting-dogrulama]] | AD-xx kontrol kanıtları |
| [[rate-limiting-kapsam]] | Kapsam sınırları (3 alt sınır, 9 uç) |
| [[authentication-ornekler]] | Auth servisinde sayaç kullanım örnekleri |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. Katmanlama: Üç Sürücü, Tek Mantık

Rate limiting kodu **üç ayrı yerde** çalışır; aynı kavramı farklı
sarmalayıcılarla uygular:

| Sürücü | Dosya | Ne korur | Sarmalayıcı |
|---|---|---|---|
| HTTP middleware sürücüsü | `shared/src/Middleware/RateLimiterMiddleware.php` | Tüm sayfa istekleri (PageRouter) | PSR-15 middleware |
| API pipe sürücüsü | `shared/src/Api/Middleware/RateLimitMiddleware.php` | `api.coremusic.net` uçları | API pipe (index.php) |
| Servis sürücüsü | `auth.coremusic.net/include/Service/AuthService.php` | Login/register/reset mantığı | `IRateLimiter` (`CacheRateLimiter`) |

Hepsi **aynı depoyu** (APCu / bellek adaptörü) kullanır ve aynı `IRateLimiter`
sözleşmesine bağımlıdır: `shared/src/Contracts/IRateLimiter.php`
(`isLimited` / `increment`).

---

## 2. Ana Akış: İstek → 429

```
[1] İstek gelir (index.php)
[2] IP çözülür                          → RateLimiterMiddleware.php:105-127
[3] Uç fail-closed listesinde mi?        → :20-30 listesi, :47 constructor
[4] Depo erişilebilir mi?
      ├─ HAYIR + fail-closed ucu → 503 + Retry-After   → :53-68
      └─ HAYIR + genel uç        → istek geçer (fail-open) ⚠️  → ADR-013 §3
[5] Sayaç anahtarı üretilir ('rl:' + IP) → :11
[6] set($key, 1, $window) çağrılır       → :74
      ├─ $isNew === true  → ilk istek: kabul    → :75-77
      └─ $isNew === false → increment($key)     → :78-84  ⚠️ (bkz. §4)
[7] Eşik aşıldı mı?
      ├─ EVET → 429 + Retry-After          → :86-100
      └─ HAYIR → $next->handle() (istek devam) → :101
```

### 2.1 Adım detayları

**Adım [2] — IP çözümü.** `REMOTE_ADDR` temel alınır; `X-Forwarded-For`
yalnız trusted proxy listesindeki istemciler için geçerlidir
(`RateLimiterMiddleware.php:105-127`). Sahte `X-Forwarded-For` ile anahtar
kaydırma (rate limit evasion) bu dal ile engellenmeye çalışılır. Kanıt:
`[[rate-limiting-ornekler]]` §4.

**Adım [3] — fail-closed eşleşmesi.** Uç yolu, `AUTH_FAIL_CLOSED_PATHS`
içindeyse `$this->failClosedPaths` üzerinden işaretlenir; constructor bu
listeyi `:47`'de alır (test için değiştirilebilir bağımlılık —
`RateLimiterMiddlewareTest.php` bunu kullanır).

**Adım [4] — depo hata dallı.** Depo OK değilse karar iki uçlu:
fail-closed ucu → **503** (`:53-68`); genel uç → **devam** ⚠️. Bu, sürücünün
tek "sessiz geçme" (fail-open) noktasıdır ve veto adayıdır.

**Adım [6] — sayaç yazımı.** `set()` dönüşü "yeni" kabul edilir; yeni
değilse `increment`. **Buradaki varsayım şüphelidir** — §4.

**Adım [7] — eşik kontrolü.** `increment` sonucu `$count` olarak okunur;
`$count > $this->maxRequests` ise 429 üretilir (`:86-100`), aksi halde istek
bir sonraki middleware'e devredilir.

---

## 3. Servis Katmanı Akışı (Auth)

Auth servisinde sayaç **başarısızlık odaklıdır** — başarılı istekler sayaç
yazmaz (yalnız başarısızlık sayaçlanır, bkz. `AuthService.php:77,90`):

```
Login isteği
  → isLimited($failedKey, 5, 900)  IP bazlı        → AuthService.php:65
  → isLimited($identityKey, 5, 900) kimlik bazlı   → AuthService.php:66
      ├─ herhangi biri true → RateLimitException    → :68
      └─ ikisi de false → şifre kontrolü devam eder
  → ŞİFRE HATALI:
      → increment($failedKey, 900)                  → :77
      → increment($identityKey, 900)                → :78
      → RateLimitException (401/401-like)           → servis akışı
  → ŞİFRE DOĞRU:
      → increment yalnız audit/sayaç bakımından    → :90-91
      → oturum açılır
```

**Kritik sıralama kuralı:** `isLimited` **önce**, `increment` **sonra**
çağrılır. Tersi olsaydı ilk geçerli deneme de sayılacaktı. Kanıt:
`AuthService.php:65-68` (önce kontrol) → `:77-78` (sonra artış).

**İki boyutlu akış nedeni:** Aynı IP'den 5 farklı hesap denendiğinde
`$failedKey` dolar ama `$identityKey` her hesap için ayrıdır → **yanlış
hesaplar masum kullanıcıyı kilitlemez**. Aynı kimlik 5 farklı IP'den
denenirse `$identityKey` dolar → **credential stuffing kapanır**.

Register akışı aynı kalıbı izler: `isLimited($rateKey, 3, 3600)` önce
(`:139`), başarısızlıkta `increment` (`:147,153,166`). Password reset:
`:265,275`.

---

## 4. Hata Dalı: Sayaç Sürekliliği Sorunu ⚠️

Sürücünün en riskli noktası, adım [6]'nın **dönüş değeri varsayımıdır**:

| Gözlem | Kanıt | Sonuç |
|---|---|---|
| Middleware `set()` sonucunu `$isNew` sayar | `RateLimiterMiddleware.php:74-77` | Yeni sanılırsa `increment` atlanır |
| `ApcuAdapter::set()` → `apcu_store()` sonucu | `shared/src/Cache/ApcuAdapter.php` | `apcu_store` mevcut anahtarı **üzerine yazarken de true** döndürebilir ⚠️ |
| `MemoryAdapter::set()` | `shared/src/Cache/MemoryAdapter.php` | Daima `true` olabilir ⚠️ |

**Şüpheli senaryo (runtime doğrulanmadı):** `set()` her istekte `true`
dönerse `increment` yolu hiç çalışmaz, sayaç her istekte `1`'e döner →
**eşik asla aşılmaz → genel uçlarda tam fail-open.**

> ⚠️ **VERIFICATION REQUIRED:** Bu, kod okumasından çıkarımdır; runtime test
> ile **KANITLANMAMIŞTIR**. Doğrulama adımları ve beklenen kanıt
> `[[rate-limiting-dogrulama]]` AD satırlarındadır; kod alıntısı
> `[[rate-limiting-ornekler]]` §2'dedir. Kanıtlanırsa bu bir **veto
> gerekçesidir** (`.ai/AGENTS.md` §18 fail-open yasağı).

---

## 5. Durum Geçiş Tablosu (sayaç)

| Durum | Tetikleyici | Sonraki durum | Kanıt |
|---|---|---|---|
| YOK (anahtar yok) | İlk istek `set(key,1,ttl)` | SAYAÇ=1 (TTL=window) | `RateLimiterMiddleware.php:74-77` |
| SAYAÇ=n (< eşik) | `increment` | SAYAÇ=n+1 | `:78-84` |
| SAYAÇ=n (= eşik) | `increment` → `n+1 > max` | **429 durumu** | `:86-100` |
| 429 durumu | TTL dolana kadar | YOK (TTL düşer → yeniden başlar) | TTL = `windowSeconds` |
| DEPO HATASI (auth ucu) | depo exception | **503** (terminal) | `:53-68` |
| DEPO HATASI (genel uç) | depo exception | istek **geçer** ⚠️ | ADR-013 §3 |
| DEPO HATASI (MemoryAdapter, worker recycle) | süreç yeniden başlar | YOK (sayaç sıfır) ⚠️ | `api.coremusic.net/index.php:140-164` |

**TTL pencere mantığı:** Sayaç anahtarına `windowSeconds` TTL yazılır;
pencere dolunca anahtar cache'ten düşer ve sayaç doğal olarak sıfırlanır —
ayrıca explicit sıfırlama kodu yoktur. Kanıt: `RateLimiterMiddleware.php:74`
(`$this->windowSeconds` TTL olarak).

---

## 6. Trusted-Proxy Akışı

```
REMOTE_ADDR al
  → trusted proxy listesinde mi?
       ├─ EVET  → X-Forwarded-For zincirinden en sağdaki gerçek istemci IP
       └─ HAYIR → REMOTE_ADDR kullan (X-Forwarded-For YOK say)
  → IP belirlenemezse '0.0.0.0' (DEFAULT_IP)
  → anahtar = 'rl:' + IP
```

Kanıt: `RateLimiterMiddleware.php:105-127` (proxy dalı),
`:11-12` (`CACHE_KEY_PREFIX`, `DEFAULT_IP`).

**Sonuç:** `X-Forwarded-For` header'ı doğrudan anahtar yapılmaz; yapılsa
ydı her istemci kendi anahtarını sıfırlayarak limiti aşabilirdi (evasion).
Doğrulanmamış ortam değişkeni: trusted proxy IP listesi — **UNKNOWN**
(depoya bağlı, `[[rate-limiting-dogrulama]]` AD satırı).

---

## 7. API Pipe Sürücüsü Farkı

`api.coremusic.net` ayrı bir sarmalayıcı kullanır:

| Özellik | HTTP middleware | API pipe |
|---|---|---|
| Dosya | `shared/src/Middleware/RateLimiterMiddleware.php` | `shared/src/Api/Middleware/RateLimitMiddleware.php` |
| Anahtar | `rl:{ip}` | `api:{ip}` / `ip:{ip}` |
| Eşik kaynağı | PageRouter sabitleri (60/60) | `withLimits` ile uç başına |
| Bağlanma | `index.php` middleware yığını | `api.coremusic.net/index.php:140-164` pipe |

Ayrı anahtar alanı (`api:`) sayesinde API trafiği sayfa trafiğinden
**bağımsız** sayaçlanır — aynı IP'nin web ve API kotaları birbirini
ezmez. Kanıt: `[[rate-limiting-ornekler]]` §3.

---

## 8. Sürücü Bileşenleri (özet)

| Bileşen | Dosya | Rol |
|---|---|---|
| `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php` | Ana HTTP sürücüsü (429/503 üretir) |
| `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php` | `IRateLimiter` uygulaması (auth servisi) |
| `RateLimitMiddleware` | `shared/src/Api/Middleware/RateLimitMiddleware.php` | API pipe sürücüsü |
| `IRateLimiter` | `shared/src/Contracts/IRateLimiter.php` | Sözleşme (`isLimited`/`increment`) |
| `RateLimitException` | `shared/src/Exception/RateLimitException.php` | Servis katmanı istisnası |
| Cache adaptörleri | `shared/src/Cache/{Apcu,Memory}Adapter.php` | Sayaç deposu |

Detaylı envanter: `[[rate-limiting-bilesenler]]`.

---

## 9. Test Sürücüsü ile Doğrulama

Sürücü davranışı birim testleriyle iki dalı kapsar:

- **Fail-closed dalı:** `RateLimiterMiddlewareTest.php` içinde, depo
  hatası eklendiğinde 503 beklentisi.
- **Eşik dalı:** `maxRequests` aşıldığında 429 + `Retry-After` beklentisi.

Test dosyası: `shared/tests/Middleware/RateLimiterMiddlewareTest.php`
(10 test). Tam liste ve satır referansları:
`[[rate-limiting-ornekler]]` §5 · `[[rate-limiting-dogrulama]]`.

> ⚠️ `CacheRateLimiter` (servis sürücüsü) için **ayrı test dosyası
> görülmemiştir** (2026-10-15 araması) — bu bir test boşluğudur.

---

## 10. Auth Servis Akışları — Tam Yol (kod kanıtlı)

### 10.1 Login (satır referansları `AuthService.php`)

| Adım | Kod | Sonuç |
|---|---|---|
| 1. Anahtar üretimi | `:59` `$failedKey = 'rate_limit:login:' . clientIp` · `:63` `$identityKey = 'rate_limit:login:id:' . sha256(strtolower(identity))` | İki anahtar |
| 2. Limit kontrolü (önce) | `:65-66` çift `isLimited(max=5, window=900)` | Birisi `true` → `:68` `RateLimitException::loginRateLimited(900)` |
| 3. Alan doğrulama | `:71-73` boş alan → `ValidationException` | **Sayaç yazılmaz** (boş istek limiti etkilemez) |
| 4. Kullanıcı bulunamadı | `:76-79` çift `increment` + `AuthenticationException` | Her iki sayaç +1 |
| 5. Banlı kullanıcı | `:84-86` `banned()` | Sayaç yazılmaz (önceden biliniyor) |
| 6. Parola hatalı | `:89-92` çift `increment` + `invalidCredentials` | Her iki sayaç +1 |
| 7. Başarılı giriş | parola doğrular → akış devam | Sayaç **yazılmaz** |

**Gözlem:** Adım 4 ve 6 dışındaki dallar sayaç yazmaz → sayaç yalnızca
**gerçek deneme** artar; bu, CWE-307 için doğru davranıştır ✅.

### 10.2 Register (satır referansları)

| Adım | Kod | Sonuç |
|---|---|---|
| Limit kontrolü | `:139` `isLimited($rateKey, 3, 3600)` | Aşım → `RateLimitException` |
| Başarısızlık/yeniden deneme | `:147,153,166` `increment($rateKey, 3600)` | Sayaç +1 |

### 10.3 Password reset (satır referansları)

| Adım | Kod | Sonuç |
|---|---|---|
| Limit kontrolü | `PASSWORD_RESET_MAX_ATTEMPTS=3`, `WINDOW=3600` (`:40-41`) | `passwordResetRateLimited` |
| Deneme artışı | `:265,275` `increment($rateKey, 3600)` | Sayaç +1 |

---

## 11. Zaman Çizelgesi (pencere davranışı)

```
t=0s    ilk istek → set(key,1,60) → TTL=60sn başlar
t=1..59 increment'lar → sayaç 2,3,...,60
t=59    60. istek: count=60, 60 > 60 = false → GEÇER (strict >)
t=60    61. istek: count=61 > 60 → 429 + Retry-After: kalan
        ... TTL döner ...
t=60+   anahtar cache'ten DÜŞER (TTL bitimi) → sonraki istek set→isNew
        → sayaç 1'e sıfır (explicit reset kodu YOK — TTL tek mekanizma)
```

**Kritik zamanlama uyarısı ⚠️:** `set(key, 1, window)` her çağrıda
`$isNew` false dönerse `:82`'deki gibi TTL'yi **yeniler** — bu, throttled
istemcinin her isteğinde pencereyi uzatma (sliding'e kayma) etkisi
yaratır mı? Yalnız `set`'in mevcut anahtarda TTL'yi güncelleyip
güncellemediği adaptöre bağlıdır → **UNKNOWN, runtime test gerekli** ⚠️
(kaynak: `RateLimiterMiddleware.php:74,82` · adaptör: `ApcuAdapter.php`).

---

## 12. Hata Dalı Tablosu (tek yerde)

| # | Hata | Tetikleyici | Tepki | Kanıt | Sınıf |
|---|---|---|---|---|---|
| E1 | Adaptör çözülemedi (exception) | `CacheManager::getAdapter()` fırlatır | auth ucu: **503** · genel uç: **geç** | `RateLimiterMiddleware.php:52-68,129-136` | fail-safe ⚠️ |
| E2 | `increment` `false` | adaptör `increment` desteklemiyor/hata | **geç** + `rate_limiter_increment_failed` log | `:80-84` | fail-open ✅ testli |
| E3 | `increment` `null` | adaptör `null` döner (test sahtesi) | **geç** | test `:130-152` | fail-open ✅ testli |
| E4 | `set()` her zaman `true` | adaptör semantics | sayaç **sıfırlanır**, 429 asla | `:74-77` | ⚠️ AD-06 (test EDİLMEMİŞ) |
| E5 | Bellek adaptörü + worker recycle | APCu yok / süreç biter | sayaç **kaybolur** | `api.coremusic.net/index.php:140-164` | ⚠️ AD-11 |
| E6 | Geçersiz IP | `filter_var` başarısız | `0.0.0.0` anahtarı (tümü paylaşır) | `:12,121` | fail-closed-ish ⚠️ |
| E7 | Servis istisnası | `RateLimitException` | controller → 429 + `getRetryAfter()` | `AuthController.php:58-62` | normal akış ✅ |

> **E6 notu:** `0.0.0.0`'a düşen tüm istekler **tek sayaç** paylaşır →
> geçersiz IP durumunda masum kullanıcılar birbirini kilitleyebilir
> (availabilty yönü). Tersi güvenlik yönü: tek anahtar == tek saldırı
> yüzeyi. Karar bilinçli mi **UNKNOWN** ⚠️.

---

## 13. Gözlemlenebilirlik Akışı (log → denetim)

```
RateLimiterMiddleware
   ├─ rate_limiter_unavailable {path, mode: fail-closed|fail-open}  :57,:67
   ├─ rate_limiter_increment_failed {path}                          :81
   └─ rate_limited {ip, path, code: 429}                            :88-92
              │
              ▼
LoggerFactory::securityEvent (B14) → güvenlik olayı akışı
              │
              ▼
[.] Denetim izi — index.md §5 "429 ve bot olayları denetim izine" bağı
    (audit düğümü bu çocukta DEĞİL — sınır referansı, §4)
```

**Veto veri kaynağı:** AD-03 fail-open'ı izlenebilir olduğundan, `.ai/AGENTS.md`
§18 "fail-open tespiti" **log üzerinden otomatikleştirilebilir** — bu, anayasa
§2.2'deki veto eşiğinin pratik kapanış yoludur.

---

## 14. Sürücü Kontrol Listesi (özet)

- [x] IP → anahtar → eşik → 429 akışı kodda mevcut (§2)
- [x] 9 auth ucunda 503 fail-closed (§2, AD-01/AD-02)
- [x] İki boyutlu auth sayaç sıralaması doğru: kontrol → artış (§3)
- [x] TTL tek pencere mekanizması; explicit reset yok (§5)
- [x] Trusted-proxy-only XFF (§6)
- [x] API anahtarı ayrı alan `api:` (§7)
- [ ] `set()`/`isNew` sürekliliği ⚠️ (§4 — AD-06)
- [ ] Bellek fallback kayıp senaryosu ⚠️ (AD-11)
- [ ] `CacheRateLimiter` testi ⚠️ (AD-14)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
