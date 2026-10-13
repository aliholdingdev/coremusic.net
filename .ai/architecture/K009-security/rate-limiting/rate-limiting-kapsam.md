---
node_id: K009-security/rate-limiting/kapsam
node_type: module
status: verify-required
parent_id: K009-security/rate-limiting
children: []
depends_on:
  - K009-security/rate-limiting
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php:16-30
  - shared/src/Middleware/RateLimiterMiddleware.php:54-78
  - shared/src/Middleware/RateLimiterMiddleware.php:84-112
  - shared/src/Middleware/RateLimiterMiddleware.php:114-132
  - shared/src/Security/CacheRateLimiter.php:12-42
validation:
  - rate-limiting-kapsam §1 · 3 alt sınır
  - rate-limiting-kapsam §2 · fail-closed 9 uç
  - rate-limiting-kapsam §3 · pencere mekaniği
  - rate-limiting-kapsam §4 · sınır dışı
  - rate-limiting-kapsam §5 · bağımlılık
open_questions:
  - Genel uç fail-open denetimi: gözlem alarmı var mı?
group: mimari
---

# K009-security / rate-limiting — Kapsam

> Authority: Bayram Ali / Vault Steward · 3 alt sınır · kod kanıtları disk'te.

## §1 Alt Sınırlar

### 1.1 Middleware limiti (IP tabanlı)

RateLimiterMiddleware:84-99 — rl: + md5(IP) anahtarı, windowSeconds TTL'li
set/increment. Varsayılan 60 istek / 60 sn (:54-55). Aşım → 429 +
Retry-After (:100-112).

### 1.2 Fail-closed (cache yoksa)

Cache adapter çözülemezse (:59-66 RuntimeException catch):
AUTH_FAIL_CLOSED_PATHS içindeki 9 uç → 503 + Retry-After + securityEvent
(:67-78, ADR-013 §5.4 şart 1a). Diğer uçlar fail-open + gözlem kaydı
(:79-82, ADR-013 §3).

### 1.3 Key-based servis limiti

CacheRateLimiter:12-42 — IRateLimiter: isLimited / increment / reset.
authentication fail-lockout ile ortak kullanım (K009/authentication §2.3).

## §2 Fail-Closed 9 Uç

| # | Uç | | # | Uç |
|---|---|---|---|---|
| 1 | /login | | 6 | /set-gender |
| 2 | /register | | 7 | /select-gender |
| 3 | /forgot-password | | 8 | /session |
| 4 | /reset-password | | 9 | /validate-key |
| 5 | /logout | | | |

(Kanıt: RateLimiterMiddleware:16-30)

## §3 Pencere Mekaniği

| Adım | Kod | Davranış |
|---|---|---|
| 1 | :84-85 | cache set (YENİ pencere → TTL windowSeconds) |
| 2 | :86-88 | yeni ise doğrudan next |
| 3 | :90-98 | increment; başarısızsa sayaç sıfırla + gözlem |
| 4 | :100 | count > maxRequests → 429 |

Not: set() = window_seconds TTL; ilk istek penceresi sabit pencere sayılır
(sliding değil) — ADR E-06.

## §4 Sınır Dışı

- Kullanıcı/API-key tabanlı limit — yok (yalnız IP)
- CAPTCHA / imza tabanlı yavaşlatma — yok (bkz rate-limiting-dogrulama §11)
- Fail-open genel uçlarda ikinci savunma — yok (ADR bekliyor)

## §5 Bağımlılık

- CacheInterface / CacheManager (altyapı — K010-devops sahipliğinde)
- LoggerFactory securityEvent (gözlem — P5-32)
- Trusted-proxies tanımı (TRUSTED_PROXIES sabiti, :41-44)

---
Authority: Bayram Ali / Vault Steward · node_id: K009-security/rate-limiting/kapsam · group: mimari
