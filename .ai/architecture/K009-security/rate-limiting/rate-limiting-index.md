---
node_id: K009-security/rate-limiting
node_type: module
status: verify-required
parent_id: K009-security
children:
  - rate-limiting-kapsam
  - rate-limiting-web-arastirma
  - rate-limiting-dogrulama
  - rate-limiting-ornekler
  - rate-limiting-bilesenler
depends_on:
  - K009-security/session-security
  - K009-security/dogrulama
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php:1-147
  - shared/src/Security/CacheRateLimiter.php:1-42
validation:
  - rate-limiting-index §1 · kapsam özeti
  - rate-limiting-index §2 · dosya matrisi
  - rate-limiting-index §3 · 429/503 kanıtları
  - rate-limiting-index §4 · sınırlar
  - rate-limiting-index §5 · K010 köprüsü
open_questions:
  - fail-open genel uçlar: süreklilik vs. kötüye kullanım dengesi ADR?
group: mimari
---

# K009-security / rate-limiting — Index

> Authority: Bayram Ali / Vault Steward · SSOT: coremusic-katmanli-mimari-agaci-v2.md (§2.9)
> Katman: K009-security · Kademe-3 çocuk · doğrulama bekliyor (ADR-013 §5.4 şart 1a).

## §1 Kapsam Özeti

rate-limiting, API ve auth uçlarına gelen isteklerin zaman penceresi içinde
sınırlandırılmasını (429 Too Many Requests) ve cache adapter'ı
çözülemezse auth uçlarında fail-closed 503 uygulanmasını kapsar. Kimlik
doğrulama (K009-security/authentication) ve oturum (session-security)
katmanlarından AYRIDIR: bu çocuk istek hacmini yönetir, oturum kimliğini değil.

## §2 Dosya Matrisi

| Dosya | Rol | Group |
|---|---|---|
| rate-limiting-index.md | Giriş | mimari |
| rate-limiting-kapsam.md | 3 alt sınır kapsamı | mimari |
| rate-limiting-web-arastirma.md | 8 URL kanıt | kanit |
| rate-limiting-dogrulama.md | kontrol listesi | kanit |
| rate-limiting-ornekler.md | gerçek kod | kod |
| rate-limiting-bilesenler.md | kademe-3 | mimari |

## §3 Kanıtlar (kod disk'te)

| İddia | Kanıt |
|---|---|
| IP tabanlı sliding-window (cache set + increment) | RateLimiterMiddleware:84-99 |
| 429 + Retry-After (P5-32: loglanıyor) | RateLimiterMiddleware:100-112 |
| Cache yoksa auth uçları 503 fail-closed | RateLimiterMiddleware:67-78 |
| Genel uçlar fail-open + gözlem | RateLimiterMiddleware:79-82 |
| AUTH_FAIL_CLOSED_PATHS 9 uç | RateLimiterMiddleware:16-30 |
| Trusted-proxy XFF doğrulaması | RateLimiterMiddleware:114-132 |
| IRateLimiter (key-based) | CacheRateLimiter:12-42 |

## §4 Sınırlar

- Yalnız IP-tabanlı; kullanıcı/API-key tabanlı limit ADR bekliyor
- Fail-open genel uçlar: hizmet sürekliliği (ADR-013 §3)
- 429 loglaması P5-32 ile eklendi (artık gözlemlenebilir)

## §5 K010 Köprüsü

- ADR-013 §5.4 şart 1a (fail-closed matrisi)
- P5-32 (429 gözlemlenebilirliği) — K010-devops loglama senkronu
- CacheRateLimiter: authentication fail-lockout (K009/authentication) ile ortak

---
Authority: Bayram Ali / Vault Steward · node_id: K009-security/rate-limiting · group: mimari
