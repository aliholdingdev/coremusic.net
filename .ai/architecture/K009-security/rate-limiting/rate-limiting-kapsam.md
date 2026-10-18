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

## §6 RATE LIMITING DERİNLEŞTİRME (2026-10-18 — APPEND-ONLY)

> Bu §, §1-§5'i SİLMEZ; mevcut kod kanıtlarını (RateLimiterMiddleware:16-30 · :54-132 ·
> CacheRateLimiter:12-42) tekrar üretmeden genişletir. Yeni iddia yok — sentez + karar matrisi.

### 6.1 Sınır Teknikleri Karşılaştırması (hangisi nerede anlamlı)

| Teknik | Semantik | CoreMusic durumu | Avantaj | Dezavantaj |
|---|---|---|---|---|
| Sabit pencere (fixed window) | pencere başı sayaç | IMPLEMENTED (RateLimiterMiddleware:84-98, ADR E-06) | basit, TTL doğal | pencere sınırında 2× kapasite sızıntısı |
| Kayan pencere (sliding window log) | son N isteğin zaman damgası | YOK | sızıntı yok | bellek maliyeti (istek başına kayıt) |
| Kayan pencere (counter apron) | sabit pencere + ağırlıklı önceki pencere | YOK | ~kayan, O(1) bellek | iki sayaç senkronizasyonu |
| Token bucket | kapasite + dolma oranı | YOK (kapsam §19 sentezinde tanımlandı) | burst destekli | iki parametre (kapasite/oran) |
| Leaky bucket | sabit çıkış oranı | YOK | trafiği düzleştirir | gecikme ekler — API için uygun değil |
| Hesap kilidi (lockout) | N başarısız sonra kilit | authentication dosyalarında (fail-lockout, §1.3) | brute-force'a doğrudan | DoS yüzeyi (kötü niyetli kilit) |

### 6.2 Mevcut Sınırların Parametre Analizi (kod kanıtı üzerinden)

| Parametre | Değer (kod) | Yorum | Risk |
|---|---|---|---|
| maxRequests (varsayılan) | 60 / 60 sn (:54-55) | genel uçlar için makul | auth uçları için özel değer AÇIK (kodda ayrı limit var mı? §2 listesi yalnız PASTHLER — ⚠️ auth limit değeri kodda doğrulanmalı) |
| Anahtar | rl: + md5(IP) (:84-85) | md5 BURADA KRIPTO DEĞİL — önbellek anahtarı; kripto yasağı kapsamı DIŞI (§7/2 yalnız parola/imza) | IP paylaşımı (NAT) → masum kullanıcı etkilenir |
| Pencere | sabit pencere (set TTL) | §3 kaydı | sınır iki katı sızıntısı |
| 429 yanıt | Retry-After (:100-112) | doğru | — |
| Cache yoksa | auth: 503 fail-closed (:67-78) · genel: fail-open + gözlem (:79-82) | ADR-013 §3 bilinçli karar | fail-open'da ikinci savunma YOK (§4) |

### 6.3 Saldırı Senaryoları × Mevcut Savunma

| # | Senaryo | Savunma | Boşluk |
|---|---|---|---|
| 1 | Parola brute-force (tek IP) | fail-closed 503 + authentication fail-lockout | — |
| 2 | Dağıtık brute-force (botnet) | IP limiti etkisiz | Hesap-tabanlı limit YOK → lockout DoS'a dönüşür (kilit = saldırganın amacı) |
| 3 | Pencere sınırı iki katı (61+61 istek/2 sn) | — | sabit pencere sızıntısı; kabul edilebilir mi? karar YOK |
| 4 | Cache'i şişirme (çok IP ile rl: anahtarı) | — | anahtar sayısı IP sayısı kadar; bellek patlaması → fail-closed'a düşer (auth güvenli, genel açık) |
| 5 | Genel uç DoS (fail-open + ikinci savunma yok) | — | §4 kaydı; ADR bekliyor |
| 6 | 429'ları yanlış yorumlayıp Retry-After'ı ihlal eden istemci | Retry-After gönderilir | istemci uyumu zorunlu değil; sunucu tarafı ikinci pencere yine sayar mı? ⚠️ kod okunmadı |

### 6.4 Hesaplama Referansı (davranışsal sınır)

| Formül | Değer |
|---|---|
| Sabit pencere maksimum istek | maxRequests × 2 (iki pencere sınırında) |
| Örnek 60/60sn | en kötü 120 istek / 2 sn diliminde |
| Token bucket eşdeğeri | kapasite 60 + oran 1/sn → burst 60, sonra 1/sn |
| Brute-force azaltma | 60/sn → 60/dk = 60 kat yavaşlama (hesap kilidi ile çarpılır) |

### 6.5 Karar Kapıları (orkestratöre/devreye)

| # | Karar | Bağlam | Öneri | Durum |
|---|---|---|---|---|
| D-RL-1 | Hesap-tabanlı limit eklenmeli mi? | senaryo 2 (dağıtık brute-force) | login/forgot-password'ta hesap+IP ikilisi | ⚠️ karar bekliyor |
| D-RL-2 | Kayan pencereye geçiş | senaryo 3 | mevcut sabit pencere yeterli (2× sızıntı kontrollü) | ⚠️ karar bekliyor |
| D-RL-3 | Genel uç ikinci savunma | senaryo 5 · ADR-013 §3 | fail-open + gözlem alarmı eşiği tanımlanmalı | ADR bekliyor (open_question) |
| D-RL-4 | auth uçları için özel maxRequests | §6.2 parametre analizi | kodda değer doğrulanıp §2 tablosuna satır | ⚠️ kod okuması gerekiyor |

### 6.6 Doğrulama Tablosu (bu § için)

| İddia | Kanıt | Durum |
|---|---|---|
| Sabit pencere davranışı | RateLimiterMiddleware:84-98 + ADR E-06 | IMPLEMENTED |
| fail-closed 9 uç | :16-30 · :67-78 | IMPLEMENTED |
| token bucket / kayan pencere YOK | kod taraması §1-§5 | KAPSAM DIŞI (yok) |
| hesap limiti YOK | §4 + authentication bağımlılığı | YOK (D-RL-1) |
| md5 yalnız önbellek anahtarı | :84-85 + master §7/2 kapsam notu | kripto-dışı kullanım |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---
Authority: Bayram Ali / Vault Steward · node_id: K009-security/rate-limiting/kapsam · group: mimari
