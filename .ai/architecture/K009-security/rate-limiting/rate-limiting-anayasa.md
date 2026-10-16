---
node_id: K009-security/rate-limiting/rate-limiting-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-kapsam
  - K009-security/rate-limiting/rate-limiting-index
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php
  - shared/src/Security/CacheRateLimiter.php
  - shared/src/Api/Middleware/RateLimitMiddleware.php
  - auth.coremusic.net/include/Service/AuthService.php
  - auth.coremusic.net/include/Controller/AuthController.php
  - .ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md
validation:
  - AD-01..AD-14 (rate-limiting-dogrulama.md)
open_questions:
  - CacheInterface::set() dönüş değeri "yeni kayıt" olarak mı kullanılıyor — ApcuAdapter::set() her koşulda true döndürüyor olabilir ⚠️ VERIFICATION REQUIRED
  - Genel uçlarda fail-open davranışı .ai/AGENTS.md §18 veto eşiği ile çelişiyor olabilir ⚠️ VERIFICATION REQUIRED
group: mimari
---

# K009 · rate-limiting · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari katman — rate limiting anayasası

Bu belge, CoreMusic rate limiting katmanının **ana kararlarını, eşiklerini ve
fail-safe sözleşmelerini** tanımlar. Uygulama detayı
`[[rate-limiting-surucu]]`, kod alıntıları `[[rate-limiting-ornekler]]`,
bileşen envanteri `[[rate-limiting-bilesenler]]`, kontrol kanıtları
`[[rate-limiting-dogrulama]]` içindedir.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-kapsam]] | Kapsam: 3 alt sınır, 9 fail-closed uç |
| [[rate-limiting-index]] | Dosya indeksi ve kanıt satır referansları |
| [[rate-limiting-surucu]] | İstek → sayaç → eşik → 429 akış sürücüsü |
| [[rate-limiting-ornekler]] | Gerçek PHP kod alıntıları (Dosya·Satır) |
| [[rate-limiting-bilesenler]] | Middleware/servis/adaptör envanteri |
| [[rate-limiting-dogrulama]] | AD-xx kontrol tablosu |
| [[rate-limiting-web-arastirma]] | Harici standart kanıtları (RFC/CWE/MDN/PHP) |
| [[authentication-ornekler]] | Kimlik doğrulama uçlarında sayaç kullanım örnekleri |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. Anayasa Kararları (ADR-013 bağlantılı)

Kaynak: `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md` (accepted).

### 1.1 Mevcut durum: sabit pencere (fixed window)

| Karar | Değer | Kanıt (Dosya·Satır) |
|---|---|---|
| Genel algoritma | Sabit pencere (fixed window) | ADR-013 §1 (accepted) |
| Genel uç eşikleri | 60 istek / 60 saniye | `shared/src/PageRouter/PageRouterKernel.php:333` (`RATE_LIMIT_MAX=60`, `RATE_LIMIT_WINDOW=60`) |
| Login başarısızlık | 5 deneme / 900 saniye | `auth.coremusic.net/include/Service/AuthService.php:31-32` |
| Kayıt (register) | 3 deneme / 3600 saniye | `auth.coremusic.net/include/Service/AuthService.php:33` + `:139` |
| Şifre sıfırlama | 3 deneme / 3600 saniye | `AuthService.php:265,275` (increment) |
| Sayaç deposu | APCu (yoksa bellek adaptörü) | `api.coremusic.net/index.php:140-164` |

### 1.2 Planlanan durum (ADR-013, henüz uygulanmadı ⚠️)

ADR-013 kapsamında planlanan, **mevcut koda henüz yansımayan** kararlar:

- Sliding window log algoritması (sabit pencere yerine)
- Token bucket (burst toleranslı kotalar)
- Exponential backoff / lockout (tekrarlayan ihlalde katlanan ceza)

> ⚠️ VERIFICATION REQUIRED: Bu üç karar `shared/src/` altında uygulanmış bir
> koda rastlanmamıştır (2026-10-15 taraması: `RateLimiterMiddleware.php` ve
> `CacheRateLimiter.php` yalnız fixed-window mantığı içerir — bkz.
> `[[rate-limiting-ornekler]]`). Uygulama durumu ayrı bir doğrulama gerektirir.

### 1.3 Sayaç anahtarı şeması

| Katman | Anahtar formatı | Kanıt |
|---|---|---|
| HTTP middleware (genel) | `rl:{ip}` | `shared/src/Middleware/RateLimiterMiddleware.php:11` (`CACHE_KEY_PREFIX = 'rl:'`) |
| API middleware | `api:{ip}` / `ip:{ip}` | `shared/src/Api/Middleware/RateLimitMiddleware.php` (bkz. ornekler §3) |
| Auth — başarısızlık (IP) | IP bazlı başarısızlık anahtarı | `AuthService.php:65,77` (`$failedKey`) |
| Auth — kimlik | sha256(identity) bazlı anahtar | `AuthService.php:66,78` (`$identityKey`) |

**İki boyutlu sayaç kararı:** Auth uçlarında sayaç tek başına IP'ye değil,
**IP + kimlik (identity) ikilisine** yazılır. Gerekçe: aynı IP'den farklı
hesapların kilitlenmesini (kolay doğrulama reddi / DoS) önlemek ve aynı
kimliğin farklı IP'lerden denenmesini (credential stuffing) kapatmak.
Kanıt: `AuthService.php:65-66` (çift `isLimited`), `:77-78` (çift `increment`).

---

## 2. Fail-Safe Matrisi (en yüksek öncelikli karar)

Rate limiting, **hata modu yönünden ikiye ayrılır**:

### 2.1 Fail-closed (503) — 9 kimlik doğrulama ucu

`RateLimiterMiddleware`, aşağıdaki **9 uç** için sayaç deposuna
erişilemediğinde isteği **503 ile durdurur** (fail-closed) — birebir alıntı:

```php
public const AUTH_FAIL_CLOSED_PATHS = [
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/logout',
    '/set-gender',
    '/select-gender',
    '/session',
    '/validate-key',
];
```

Kanıt: `shared/src/Middleware/RateLimiterMiddleware.php:20-30` (yol listesi),
`:53-68` (fail-closed dalı — depo hatasında `'status' => 503` +
`Retry-After` başlığı).

Kabul kuralı (ADR-013 §5.4 şart 1a): bu uçlarda sayaç servisi düşerse
**istek reddedilir** — rate limit koruması kaybı, kimlik doğrulama
zafiyetinden daha ağırdır.

### 2.2 Fail-open (izin ver) — genel uçlar ⚠️ VETO ADAYI

Genel uçlarda sayaç deposuna erişilemediğinde middleware **istek geçer**
(fail-open). ADR-013 §3 bu davranışı genel uçlar için tanımlar.

> ⚠️ **VETO EŞİĞİ:** `index.md` open_questions ve `.ai/AGENTS.md` §18
> "fail-open tespiti" ifadesine göre, fail-open davranışı **veto eşiği
> olarak işaretlenmiştir**. Bu kararın anayasal statüsü doğrulanmadan
> rate limiting "güvenli" sayılmaz. Doğrulama: `[[rate-limiting-dogrulama]]`
> AD satırları.

### 2.3 Fail-safe karar özeti

| Senaryo | 9 auth ucu | Genel uç | Kanıt |
|---|---|---|---|
| Sayaç deposu OK | Eşik uygulanır | Eşik uygulanır | `RateLimiterMiddleware.php:71-100` |
| Sayaç deposu HATA | **503 fail-closed** | **izin ver (fail-open)** ⚠️ | `RateLimiterMiddleware.php:53-68` (503) · ADR-013 §3 ⚠️ |
| Sayaç deposu YOK (APCu kapalı) | Bellek adaptörü fallback → bellek kaybında sayaç sıfırlanır ⚠️ | Aynı ⚠️ | `api.coremusic.net/index.php:140-164` |

**Bellek adaptörü sınırı:** APCu yokluğunda `MemoryAdapter` kullanıldığında
sayaçlar süreç (process) belleğinde tutulur. FPM worker yeniden
başladığında/bırakıldığında sayaçlar sıfırlanır → **pratikte fail-open**.
Kanıt: `api.coremusic.net/index.php:140-164` (APCu seçilirse ApcuAdapter,
aksi halde MemoryAdapter). Bu durum ayrı bir ⚠️ open_question'dur.

---

## 3. 429 + Retry-After Sözleşmesi

### 3.1 HTTP 429 sözleşmesi

Eşik aşıldığında middleware, `RateLimitException` yerine **doğrudan yanıt
başlığı üretir**:

| Alan | Değer | Kanıt |
|---|---|---|
| Status | `429 Too Many Requests` | `RateLimiterMiddleware.php:86-100` |
| `Retry-After` | kalan pencere saniyesi (`windowSeconds`) | `RateLimiterMiddleware.php:97` |
| 503 durumunda `Retry-After` | `windowSeconds` | `RateLimiterMiddleware.php:62` |

### 3.2 Servis katmanı 429 sözleşmesi

Auth servisinde eşik aşımı `RateLimitException` fırlatır; controller bunu
429'a çevirir:

| Sözleşme | Kanıt |
|---|---|
| `RateLimitException::loginRateLimited($windowSeconds)` fırlatılır | `AuthService.php:68` |
| Controller yakalar → `429` + `Retry-After` | `auth.coremusic.net/include/Controller/AuthController.php:50-79` |
| `index.php` halt işleyişi (middleware düzeyi yanıt) | `auth.coremusic.net/index.php:75-119` |

> **Standart uyumu:** 429 + `Retry-After` kullanımı RFC 6585 §4 ve
> `Retry-After` başlık tanımlarına dayanır — kanıtı
> `[[rate-limiting-web-arastirma]]` §1'dedir (MDN 429, MDN Retry-After).

---

## 4. IP Çözümleme Kararı (trusted proxy)

Sayaç anahtarı IP'ye bağlı olduğu için **IP çözümü sahtelemeye (spoofing)
açık bir yüzeydir**. Karar:

| Kural | Kanıt |
|---|---|
| `X-Forwarded-For` yalnız trusted proxy arkasından okunur | `RateLimiterMiddleware.php:105-127` |
| Trusted proxy yoksa doğrudan `REMOTE_ADDR` kullanılır | `RateLimiterMiddleware.php:105-127` |
| Varsayılan IP `0.0.0.0` (bilinmeyen) | `RateLimiterMiddleware.php:12` (`DEFAULT_IP = '0.0.0.0'`) |

> ⚠️ Trusted proxy yapılandırması (hangi IP'lerin trusted sayıldığı) deploy
> ortamına bağlıdır ve bu belgede **UNKNOWN** olarak işaretlenir. Doğrulama:
> `[[rate-limiting-dogrulama]]`.

---

## 5. Sayaç Yaşam Döngüsü

```
İstek gelir
  → IP çözülür (trusted-proxy kuralı)                    [RateLimiterMiddleware.php:105-127]
  → fail-closed ucu mu?                                   [RateLimiterMiddleware.php:20-30,47]
  → sayaç deposu hatası var mı?
       ├─ fail-closed ucu → 503 + Retry-After             [RateLimiterMiddleware.php:53-68]
       └─ genel uç      → izin ver (fail-open) ⚠️          [ADR-013 §3]
  → sayaç oku/yaz (set → isNew, yoksa increment)          [RateLimiterMiddleware.php:71-84]
  → eşik aşıldı mı?
       ├─ evet → 429 + Retry-After                        [RateLimiterMiddleware.php:86-100]
       └─ hayır → istek devam eder
Pencere dolunca (windowSeconds) sayaç TTL'si düşer → sayaç sıfırlanır
```

### 5.1 Sayaç yazım yolu ⚠️ KRİTİK

`RateLimiterMiddleware.php:71-84` iki yollu bir sayaç kurar:

1. `$isNew = $cache->set($key, 1, $windowSeconds)` — anahtar **yeni mi**?
2. `$isNew === true` ise → ilk istek kabul (`:75-77`)
3. Değilse → `$cache->increment($key)` ile sayaç arttırılır (`:78-84`)

> ⚠️ **VERIFICATION REQUIRED (en kritik açık soru):**
> `CacheInterface::set()` dönüş değeri, **"anahtar yeni mi" anlamında**
> yorumlanıyor (`$isNew`). Ancak:
> - `ApcuAdapter::set()` → `apcu_store()` sonucunu döndürür ve
>   `apcu_store` **mevcut anahtarı ezerken de `true` döndürür** (sürüm ve
>   çalışma zamanına bağlı ⚠️),
> - `MemoryAdapter::set()` → daima `true` döndürebilir ⚠️.
>
> `set()` her zaman `true` dönerse `$isNew` dalı **her istekte** çalışır ve
> `increment` yolu **hiç tetiklenmez** → sayaç etkili biçimde her istekte
> `1`'e sıfırlanır → **süreklilik fail-open**. Bu durum doğrulanana kadar
> rate limiting'in gerçekten sayaç tuttuğu **KANITLI DEĞİLDİR**.
> Kanıt dosyaları: `shared/src/Cache/ApcuAdapter.php`,
> `shared/src/Cache/MemoryAdapter.php`, `shared/src/Cache/CacheInterface.php`.
> Doğrulama: `[[rate-limiting-dogrulama]]` AD satırları,
> `[[rate-limiting-ornekler]]` §2.

---

## 6. Eşik Tablosu (tek bakışta)

| Uç grubu | Eşik | Pencere | Fail-safe | Kanıt |
|---|---|---|---|---|
| Genel HTTP (PageRouter) | 60 | 60 sn | fail-open ⚠️ | `PageRouterKernel.php:333` |
| API (`api.coremusic.net`) | 60 | 60 sn | fail-open ⚠️ | `Api/Middleware/RateLimitMiddleware.php` |
| Login başarısızlığı | 5 | 900 sn | fail-closed 503 | `AuthService.php:31-32,65-68` |
| Register | 3 | 3600 sn | fail-closed 503 | `AuthService.php:33,139` |
| Password reset | 3 | 3600 sn | fail-closed 503 | `AuthService.php:265,275` |
| 9 auth ucu (depo hatası) | — | — | **503** | `RateLimiterMiddleware.php:20-30,53-68` |

---

## 7. Karar Değeri ve Sonuçlar

**Kabul edilen değer:** APCu tabanlı sabit pencere sayacı, iki boyutlu
(IP + kimlik) anahtar şeması, 9 auth ucunda fail-closed 503, genel uçlarda
fail-open, 429 + `Retry-After` sözleşmesi.

**Sonuçlar (olumlu):**

- Kimlik doğrulama brute-force'u iki boyutlu sayaçla sınırlandırılır
  (`AuthService.php:65-78`).
- Depo çökmesinde auth uçları korumasız kalmaz (503 fail-closed,
  `RateLimiterMiddleware.php:53-68`).
- Standart HTTP semantiği kullanılır (429 + Retry-After).

**Sonuçlar (olumsuz / açık riskler):**

- Genel uçlarda fail-open → `.ai/AGENTS.md` §18 veto eşiğiyle çelişme
  olasılığı ⚠️.
- `set()` → `$isNew` varsayımı sayaç sürekliliğini bozabilir ⚠️ (bkz. §5.1).
- MemoryAdapter fallback, FPM worker döngüsünde sayaç kaybı → pratik
  fail-open ⚠️.
- ADR-013'ün sliding window / token bucket / backoff kararları
  uygulanmamış durumda ⚠️.

---

## 8. Tehlike Sınıfı Eşlemesi (OWASP/CWE ↔ Karar)

| Tehlike sınıfı | Sınayan karar | Kanıt |
|---|---|---|
| Aşırı auth denemesi (CWE-307) | Login 5/900 iki boyutlu sayaç | `AuthService.php:31-32,65-78` |
| Etkileşim sıklığı kontrolsüzlüğü (CWE-799) | Genel uç 60/60 sabit pencere | `PageRouterKernel.php:333` · `RateLimiterMiddleware.php:39-40` |
| Sınırsız kaynak tahsisi (CWE-770) | Tüm uçlarda eşik + `MemoryAdapter` fallback ⚠️ | `RateLimitMiddleware.php:22-23` · `api.coremusic.net/index.php:140-164` |
| 429 semantiği (RFC 6585 §4) | Üç katmanda da 429 + `Retry-After` | `RateLimiterMiddleware.php:93-99` · `AuthController.php:58-62` |
| Parola-sprey / credential stuffing | `identityKey` SHA-256 bucket'ı | `AuthService.php:60-63` |
| IP spoofing ile limit evasion | Trusted-proxy-only XFF okuma | `RateLimiterMiddleware.php:105-127` · test `:243-272` |

Dayanak URL'leri: `[[rate-limiting-web-arastirma]]` §1 (8 kaynak, 2026-10-15 HTTP 200).

---

## 9. Yasaklar (bu düğümde yapılmaz)

1. **Eşik değerini dosyada uydurmak** — eşik yalnız okunan koddan yazılır
   (60/60, 5/900, 3/3600 — kaynak: §1.1, §6).
2. **Fail-open'ı varsayılan güvenli saymak** — §2.2'deki ⚠️ veto
   açık sorusu kapanmadan "güvenli" nitelemesi yazılmaz.
3. **`set()`/`isNew` kanıtını kod okumasıyla kesinleştirmek** — yalnız
   runtime test KANITLI yapar (§5.1).
4. **Sayaç anahtarına ham kimlik/API anahtarı yazmak** — kimlik SHA-256
   (`AuthService.php:63`), API anahtarı SHA-256 (`RateLimitMiddleware.php:62`)
   ile hash'lenir; IP md5 (`RateLimiterMiddleware.php:72`).
5. **Sessiz fail-open** — kod bunu yasağın sınırında tutar: her fail-open
   `securityEvent` yazar (`:67,81`); log'suz fail-open **bu kodda yoktur** ✅.

---

## 10. Açık Sorular (kapanış kriteri ile)

| # | Soru | Kapanış kriteri | Durum |
|---|---|---|---|
| Q1 | `set()` → `$isNew` sayaç sürekliliği | `MemoryAdapter`/`ApcuAdapter` üzerinde ardışık `set()` dönüş testi + middleware'e "2. istek sayacı arttırır mı" testi | ⚠️ AD-06 |
| Q2 | Genel uç fail-open anayasal statüsü | `.ai/AGENTS.md` §18 ile ADR-013 §3'ün uzlaştırılması (üst karar) | ⚠️ veto eşiği |
| Q3 | `MemoryAdapter` fallback sayaç kaybı | FPM worker recycle senaryosu runtime testi | ⚠️ AD-11 |
| Q4 | Canlı `TRUSTED_PROXIES` değeri | Deploy ortamı kanıtı | UNKNOWN AD-13 |
| Q5 | ADR-013 sliding window/token bucket/backoff uygulaması | `shared/src/` içinde ilgili kod araması sonucu | ⚠️ §1.2 |
| Q6 | `CacheRateLimiter` testi | Yeni birim test dosyası | ⚠️ AD-14 |

Bu belge yalnız `status: draft` taşır; Q1–Q6 kapanmadan `active` yapılmaz.

---

## 11. Anayasal Maddeler (özet — madde madde)

| Madde | Karar | Kanıt | Durum |
|---|---|---|---|
| A1 | Algoritma: sabit pencere (fixed window) | ADR-013 · `RateLimiterMiddleware.php:74-86` | ✅ KANITLI (uygulama) |
| A2 | Genel eşik: 60 istek / 60 sn | `PageRouterKernel.php:333` · `RateLimiterMiddleware.php:39-40` | ✅ KANITLI |
| A3 | Login: 5 / 900 sn · Register: 3 / 3600 sn · Reset: 3 / 3600 sn | `AuthService.php:31-41` | ✅ KANITLI |
| A4 | İki boyutlu auth sayaç (IP + SHA-256 kimlik) | `AuthService.php:59-78` | ✅ KANITLI |
| A5 | 9 auth ucunda depo hatası → 503 fail-closed | `RateLimiterMiddleware.php:20-30,53-65` | ✅ KANITLI |
| A6 | Genel uçlarda depo hatası → fail-open (log'lu) | `RateLimiterMiddleware.php:66-68` | ✅ KANITLI davranış · ⚠️ statü (Q2) |
| A7 | 429 + `Retry-After` (saniye) zorunlu | `:93-99` · `AuthController.php:58-62` | ✅ KANITLI |
| A8 | XFF yalnız trusted proxy'den okunur | `RateLimiterMiddleware.php:105-127` · test `:210-272` | ✅ KANITLI |
| A9 | Sayaç deposu: APCu → bellek fallback | `api.coremusic.net/index.php:140-164` | ✅ yapı · ⚠️ kayıp senaryosu (Q3) |
| A10 | `set()`/`isNew` süreklilik varsayımı | `RateLimiterMiddleware.php:74-77` | ⚠️ Q1 (kritik) |
| A11 | Plan: sliding window + token bucket + backoff | ADR-013 §1.2 | ⚠️ uygulanmadı (Q5) |
| A12 | Her fail-open `securityEvent` ile izlenir | `:57,67,81,88-92` | ✅ KANITLI |

**Madde bütünlüğü kuralı:** A1–A12 bu belgenin değiştirilebilir
yüzeyidir; değişiklik ADR (`[[rate-limiting-index]]` üzerinden) ve
`[[rate-limiting-dogrulama]]` AD tablosu güncellenmeden yapılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
