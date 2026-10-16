---
node_id: K009-security/rate-limiting/rate-limiting-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-anayasa
evidence:
  - https://www.rfc-editor.org/rfc/rfc6585.html
  - https://datatracker.ietf.org/doc/draft-ietf-httpapi-ratelimit-headers/
  - https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429
  - https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Retry-After
  - https://cwe.mitre.org/data/definitions/770.html
  - https://php.net/manual/en/book.apcu.php
  - https://cwe.mitre.org/data/definitions/799.html
  - https://cwe.mitre.org/data/definitions/307.html
validation:
  - HTTP 200 doğrulaması: 2026-10-15 (8/8)
  - AD-04, AD-06 (rate-limiting-dogrulama.md)
open_questions:
  - RateLimit başlık draft'ı henüz RFC olmuştur (standartlaşma durumu) ⚠️
group: kanit
---

# K009 · rate-limiting · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kanıt katmanı — harici standart/tehlike kaynakları

Bu belge, rate limiting kararlarının **harici dayanaklarını** (RFC, IETF
draft, MDN, CWE, PHP el kitabı) toplar. 8 kaynak **2026-10-15 tarihinde
HTTP 200 ile doğrulanmıştır**. Kod karşılıkları §2'dedir; iç denetim
`[[rate-limiting-dogrulama]]` içindedir.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-anayasa]] | Harici dayanakların bağlandığı kararlar |
| [[rate-limiting-ornekler]] | Dayanakların kod karşılığı |
| [[rate-limiting-dogrulama]] | AD-xx iç denetim tablosu |
| [[rate-limiting-kapsam]] | Kapsam sınırları |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. Kaynak Tablosu

| İddia | URL | Tarih: 2026-10-15 | HTTP 200 |
|---|---|---|---|
| HTTP 429 "Too Many Requests" durum kodu RFC 6585 §4 ile tanımlanmıştır; istemci, belirli bir süre sonra tekrar denemelidir | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-15 | ✅ |
| İstek limiti başlıkları (`RateLimit-*`) IETF draft'ı ile standartlaştırılmaktadır; limit, kalan hak ve yenileme zamanı istemciye başlıkla bildirilir | https://datatracker.ietf.org/doc/draft-ietf-httpapi-ratelimit-headers/ | 2026-10-15 | ✅ |
| 429 durum kodu: istek "belirli bir süre" aşıldığında döner; `Retry-After` ile ne zaman tekrar deneneceği bildirilir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429 | 2026-10-15 | ✅ |
| `Retry-After` başlığı 503 ve 429 yanıtlarında kullanılabilir; saniye cinsinden sayı veya HTTP tarihi biçimini alır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Retry-After | 2026-10-15 | ✅ |
| CWE-770: kaynak tahsisi sınırsız/kısıtlanmamış yapılabilir — rate limiting bu sınıfın temel azaltıcısıdır | https://cwe.mitre.org/data/definitions/770.html | 2026-10-15 | ✅ |
| APCu (PHP Shared Memory) eklentisi, istekler arası durum için işletim sistemi düzeyinde paylaşılan bellek sağlar (sayaç deposu zemini) | https://php.net/manual/en/book.apcu.php | 2026-10-15 | ✅ |
| CWE-799: etkileşim sıklığının uygun kontrol edilmemesi (proper control of interaction frequency) — brute-force/otomatik istek sınıfı | https://cwe.mitre.org/data/definitions/799.html | 2026-10-15 | ✅ |
| CWE-307: aşırı kimlik doğrulama denemesinin kısıtlanmaması — login rate limit'in doğrudan dayanağı | https://cwe.mitre.org/data/definitions/307.html | 2026-10-15 | ✅ |

**Sayım:** 8 kaynak · 8/8 HTTP 200 (2026-10-15) · bu dosya haricinde
K009-security içinde tekrar kullanılmıyor.

---

## 2. Kaynak ↔ Kod Xref

| # | Kaynak | Kod karşılığı (Dosya·Satır) | İlişki |
|---|---|---|---|
| 1 | RFC 6585 §4 (429) | `RateLimiterMiddleware.php:93-99` (429 yanıt) · `AuthController.php:59` (429) · `RateLimitMiddleware.php:41-45` (429) | Üç katman da 429 döner → RFC'ye uyum ✅ |
| 2 | RateLimit başlık draft'ı | `RateLimiterMiddleware.php:97` yalnız `Retry-After` gönderir; `RateLimit-Limit`/`RateLimit-Remaining`/`RateLimit-Reset` **yok** ⚠️ | Draft ile **kısmi** uyum: limit bilgisi istemciye açıklanmıyor |
| 3 | MDN 429 | `RateLimiterMiddleware.php:86-100` (`halt`, JSON hata gövdesi `rate_limit_exceeded`) | Durum kodu + gövde semantiği uyumlu ✅ |
| 4 | MDN `Retry-After` | `RateLimiterMiddleware.php:62,97` (saniye cinsinden `windowSeconds` string) · `AuthController.php:62` (`getRetryAfter()`) · `RateLimitMiddleware.php:40` (`header('Retry-After: ...')`) | Saniye biçimi (1. seçenek) kullanılıyor ✅ |
| 5 | CWE-770 | Eşikler: `PageRouterKernel.php:333` (60/60) · `AuthService.php:31-41` | Sınırsız kaynak tahsisi azaltması ✅ |
| 6 | PHP APCu | `api.coremusic.net/index.php:140-164` (APCu varsa `ApcuAdapter`) · `AuthContainer.php:62` (`CacheManager::getAdapter()`) | Sayaç deposu APCu zemininde ✅ |
| 7 | CWE-799 | Genel uç kotaları: `RateLimiterMiddleware.php:39-40` (`maxRequests=60`, `window=60`) | Etkileşim sıklığı kontrolü ✅ |
| 8 | CWE-307 | `AuthService.php:65-68` (login 5/900 çift sayaç) → `RateLimitException::loginRateLimited` | Aşırı auth denemesi kısıtı ✅ |

---

## 3. Uyum Boşlukları (kaynak bazlı)

1. **Draft başlık eksikliği (kaynak #2):** İstemci, kalan hakkını ve
   pencere bitişini `RateLimit-*` başlıklarıyla göremez; yalnız
   `Retry-After` (429 anında) gönderilir. Standart draft'ı henüz RFC
   olmadığı için **ihlal sayılmaz**, ancak AI/istemci dostu API için
   geliştirmedir ⚠️ (open_question: draft'ın RFCleşme durumu).
2. **Fail-open ↔ CWE-770 ilişkisi:** Depo çökmesinde genel uçların limit
   olmadan akması (AD-03), kaynak kısıtlamasının geçici olarak kalkması
   demektir → CWE-770 yüzeyi ⚠️. Kayıt (`securityEvent`) mevcut
   (`RateLimiterMiddleware.php:67`), fakat **azaltma (alert/ban)** yok.
3. **CWE-307 ↔ AD-06 bağı:** Eğer `set()`/`isNew` hatası doğrulanırsa
   login limiti middleware'de değil yalnız `CacheRateLimiter`'da çalışır —
   bu, CWE-307 kontrolünün **tek katmana** inmesi demektir ⚠️.

---

## 4. Kaynak Notları (ne der, ne demez)

- **RFC 6585:** Yalnız 429'un tanımını verir; hangi algoritmanın
  (sabit pencere vs. kayan pencere) kullanılacağını **zorunlu kılmaz** →
  ADR-013'ün fixed-window seçimi bu RFC'ye aykırı **değildir**.
- **IETF draft:** Başlık şemasını önerir; `Retry-After` ile birlikte veya
  onun yerine kullanılabilir. CoreMusic `Retry-After`'ı kullanıyor →
  mevcut davranış draft'ın_ruhsatı içindedir.
- **CWE'ler (770/799/307):** Zafiyet sınıflarıdır — "limit var mı"
  sorusunu sorar, eşik değerini **tayin etmez**. 60/60 ve 5/900 eşikleri
  proje risk kararıdır (ADR-013), CWE kaynaklı zorunluluk değildir.
- **PHP APCu el kitabı:** Paylaşımlı bellek sağlar; **süreç-PHP için
  değildir** (FPM worker'lar arası ortaktır) → `MemoryAdapter` fallback'i
  bu ortaklığı kaybeder (AD-11 ⚠️).

---

## 5. Kaynak Tarama Kaydı

| Kalem | Değer |
|---|---|
| Tarama tarihi | 2026-10-15 |
| Doğrulama yöntemi | HTTP GET → 200 (her 8 kaynak) |
| Kaynak sayısı | 8 / 8 ✅ |
| K009-security içinde tekrar | 0 (envanter kesişimi — K009'un diğer dosyalarında bu 8 URL yok) |
| Uydurma URL | 0 (yalnız doğrulanmış 8 adres) |

**Kod xref bütünlüğü:** §2'deki her satır, `[[rate-limiting-ornekler]]`
içindeki bir alıntı Dosya·Satır referansıyla kesişir; xref'te yalnızca
`[[rate-limiting-dogrulama]]` AD satırlarına gitmeyen bağımsız iddia
**bulunmaz**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
