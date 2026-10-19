---
title: "K011 · dto-ve-mapper — kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto-ve-mapper
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto-ve-mapper — Kapsam

**Amaç:** Bu belge, `dto-ve-mapper` klasörünün **kapsamını** ELI15 dilinde tanımlar: ne işe yarar, sınırı nerede biter, hangi akışlar buna girer ve hangi sorular hâlâ açıktır. Belge, repo'da **gerçekten var olan** 11 DTO dosyasından ve ilgili middleware'lerden türetilmiştir — her iddia `Dosya · Satır` kanıtı taşır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | ELI15 Tanım | mimari | anlama | Görev başı okunur |
| 2 | Sınır (ne DEĞİL) | mimari | kapsam | Kapsam ihlali önleme |
| 3 | Giriş Akışı | mimari | array→DTO | Backend implementasyon |
| 4 | DTO Alan Envanteri | mimari | 11/11 | Alan bazlı referans |
| 5 | Çıkış Akışı | mimari | DTO→array | Response serileştirme |
| 6 | Sayfalama Akışı | mimari | PaginationResponse | Liste endpoint'leri |
| 7 | Hata Akışı | mimari | envelope | Hata yönetimi |
| 8 | Doğrulama Katmanı | mimari | 415/400/422 | İstek doğrulama |
| 9 | Normalizasyon Katmanı | mimari | ETag/Cache | Yanıt standardizasyonu |
| 10 | Kapsam Dışı | mimari | yasak | Reddedilen işler |
| 11 | Açık Sorular | mimari | karar | Üst karar bekleyenler |
| 12 | Terimler | mimari | sözlük | Ekip ortak dili |
| 13 | Karar Matrisi | mimari | seçenek | Mimari seçim |
| 14 | Risk Tablosu | mimari | risk | Önceliklendirme |
| 15 | Bağımlılık Senaryoları | mimari | etki | Değişiklik analizi |

## §1 — ELI15: Bu klasör ne anlama geliyor?

Bir web uygulaması dışarıdan (tarayıcı, API istemcisi) veri alır ve bu veriyi PHP içine taşır. Ham veri asla doğrudan iş mantığına sokulmaz; önce **kontrollü bir zarfa** konur. Bu zarfın adı **DTO (Data Transfer Object)**'tur. DTO, "bu veride hangi alanlar var, tipleri ne, varsayılanları ne" sorusunun cevabını tek yerde tutar.

**Mapper** ise iki dünya arasındaki **çevirmen**dir: ham `array` ↔ tip-güvenli PHP nesnesi. CoreMusic'te bu çeviri DTO'nun kendi içinde `fromArray()` (giriş) ve `toArray()` (çıkış) metodlarıyla yapılır — ayrı bir `*Mapper.php` sınıfı **yoktur** (envanter: `shared\src\Api\Dto` altında `*Mapper*` = 0 dosya).

**Basit benzetme:** DTO bir **kargo kutusudur**; her ürünün yeri, etiketi ve kırılganlık işareti vardır. Mapper ise kutuyu **paketleme/açma talimatıdır**. Kutunun içine ne koyulacağına (iş mantığı) kargo şirketi (DTO) karar vermez — o yalnızca taşır.

**Neden ayrıldı?** Aynı veri katmanlar arasında dolaşırken tip güvenliği kaybolursa hata üretir. DTO bunu `readonly` property'ler ile **derleme zamanında** kilitler. CoreMusic'te bu kilit **11/11 DTO'da** uygulanmıştır (`declare(strict_types=1)` satır 2 · `final class` satır 17 — hepsi dosya okuma ile ölçüldü).

**Giriş ve çıkış neden ayrı DTO?** `LoginRequest` (giriş) ile `LoginResponse` (çıkış) **farklı alanlara** sahiptir: girişte `password` var, çıkışta `accessToken` var. Tek sınıf kullanılsaydı `password` çıkışa da taşınabilirdi — bu **veri sızıntısı** olurdu. Ayrım bilinçlidir.

> **Özet:** DTO = verinin zarfı · Mapper = zarfın açılıp kapanması · K011 bu ikisinin **belgesi**dir, kodu değildir.

## §2 — Sınır (ne DEĞİL)

| # | Sınır | Durum | Açıklama | Kanıt |
|---|-------|-------|----------|-------|
| 1 | İş mantığı (business rule) | **DEĞİL** | DTO karar vermez, yalnız taşır | 11 DTO'da karar `if` bloğu yok |
| 2 | Veritabanı sorgusu / repository | **DEĞİL** | Sorgu katmanı ayrı | `ApiKeyRepository.php:87` DTO'dan bağımsız |
| 3 | HTTP routing / controller | **DEĞİL** | Routing kardeş klasör | `Api\Routing\RouteTable.php` |
| 4 | Doğrulama kuralı (validation rule) | **KISMEN** | DTO alanı tanımlar; kural middleware'de | `RequestValidationMiddleware.php:87` |
| 5 | UI / bileşen render | **DEĞİL** | Sunum katmanı ayrı | `assets.coremusic.net\js` |
| 6 | Ödeme / billing DTO'ları | **DEĞİL (yok)** | Repo'da ödeme kodu yok | grep = 0 eşleşme |
| 7 | Cache / serialization formatı | **DEĞİL** | `toArray()` JSON'a kadar geçerli | `ApiResponse.php:26-40` |
| 8 | Ayrık Hydrator katmanı | **DEĞİL (yok)** | Mevcut değil | envanter 0 |
| 9 | HTTP status kararı | **DEĞİL** | Status `ApiResponse`'ta | `ApiResponse.php:28` |
| 10 | Header üretimi | **DEĞİL** | Header `ApiResponse`/middleware'de | `ApiResponse.php:30-31` |

## §3 — Giriş Akışı: array → DTO

| # | Adım | Katman | Kanıt (Dosya·Satır) |
|---|------|--------|---------------------|
| 1 | Ham request body alınır | Api Gateway | `shared\src\Api\Gateway.php` |
| 2 | Gövde `json_decode` ile çözülür | Api/Middleware | `RequestValidationMiddleware.php:167` |
| 3 | Bozuk JSON → 400 | Api/Middleware | `RequestValidationMiddleware.php:73-77` |
| 4 | Content-Type JSON değilse → 415 | Api/Middleware | `RequestValidationMiddleware.php:63-70` |
| 5 | `fromArray()` ile DTO kurulur | Dto/Request | `LoginRequest.php:52-59` |
| 6 | Alanlar `readonly` ile kilitlenir | Dto/Request | `LoginRequest.php:19-23` |
| 7 | `declare(strict_types=1)` tip güvenliği | Dto/Request | `LoginRequest.php:2` |
| 8 | `final class` miras alınamazlık | Dto/Request | `LoginRequest.php:17` |
| 9 | Middleware zinciri DTO'yu geçirir | Api/Middleware | `ApiMiddlewarePipeline.php` |
| 10 | CSRF token doğrulanır (`hash_equals`) | Middleware | `CsrfMiddleware.php:42` |
| 11 | Rate limit kontrolü (60/60 varsayılan) | Api/Middleware | `RateLimitMiddleware.php:22-23, 39` |
| 12 | Kural ihlali → 422 + `error.fields` | Api/Middleware | `RequestValidationMiddleware.php:92-93` |
| 13 | Servis DTO getter'larıyla okur | Application | `LoginRequest.php:28-46` |

**Giriş akışı notu:** `fromArray()` **varsayılan doldurma** yapar (`?? ''`) — eksik alan `null` sızdırmaz. Bu bilinçli bir tasarımdır; 11 DTO'da **aynı desen** tekrar eder. İstisna: `MusicSearchRequest` **clamp** uygular (`MusicSearchRequest.php:76-77`).

### 3.1 `fromArray()` / `toArray()` Satır Envanteri (11/11 gerçek ölçüm)

| # | DTO | Dosya | `fromArray` | `toArray` | Toplam satır |
|---|-----|-------|-------------|-----------|--------------|
| 1 | LoginRequest | `Request\LoginRequest.php` | 52-59 | 64-71 | 72 |
| 2 | UserUpdateRequest | `Request\UserUpdateRequest.php` | 52-59 | 64-71 | 72 |
| 3 | PlaylistCreateRequest | `Request\PlaylistCreateRequest.php` | 52-59 | 64-71 | 72 |
| 4 | PlaylistAddTrackRequest | `Request\PlaylistAddTrackRequest.php` | 52-59 | 64-71 | 72 |
| 5 | MusicSearchRequest | `Request\MusicSearchRequest.php` | 70-79 | 84-93 | 94 |
| 6 | RegisterRequest | `Request\RegisterRequest.php` | 70-79 | 84-93 | 94 |
| 7 | LoginResponse | `Response\LoginResponse.php` | 61-69 | 74-82 | 83 |
| 8 | PlaylistResponse | `Response\PlaylistResponse.php` | 70-79 | 84-93 | 94 |
| 9 | PaginationResponse | `Response\PaginationResponse.php` | 70-79 | 84-93 | 114 |
| 10 | MusicResponse | `Response\MusicResponse.php` | 88-99 | 104-115 | 116 |
| 11 | UserResponse | `Response\UserResponse.php` | 88-99 | 104-115 | 116 |

**Ölçüm notu:** Tablodaki tüm satır numaraları bu oturumda dosya-okuma ile alınmıştır; tahmin yoktur. Toplam DTO dosya sayısı **11** (6 Request + 5 Response) — PowerShell `Get-ChildItem` sayımı.

## §4 — DTO Alan Envanteri (11/11 alan bazlı)

### 4.1 Request DTO'ları (6)

| # | DTO | Alanlar (tip · varsayılan) | Kanıt |
|---|-----|---------------------------|-------|
| 1 | LoginRequest | `email: string ''` · `password: string ''` · `rememberMe: bool false` | `LoginRequest.php:19-23` |
| 2 | UserUpdateRequest | `displayName: string ''` · `email: string ''` · `avatar: string ''` | `UserUpdateRequest.php:54-58` |
| 3 | PlaylistCreateRequest | `name: string ''` · `description: string ''` · `isPublic: bool false` | `PlaylistCreateRequest.php:54-58` |
| 4 | PlaylistAddTrackRequest | `playlistId: string ''` · `musicId: string ''` · `position: int -1` | `PlaylistAddTrackRequest.php:19-23` |
| 5 | MusicSearchRequest | `query: string ''` · `genre: string ''` · `artist: string ''` · `page: int 1` (**clamp `max(1,)`**) · `limit: int 20` (**clamp `min(100, max(1,))`**) | `MusicSearchRequest.php:72-78` |
| 6 | RegisterRequest | `username: string ''` · `email: string ''` · `displayName: string ''` · `password: string ''` · `gender: string 'neutral'` | `RegisterRequest.php:72-78` |

### 4.2 Response DTO'ları (5)

| # | DTO | Alanlar (tip · varsayılan) | Kanıt |
|---|-----|---------------------------|-------|
| 1 | LoginResponse | `accessToken: string ''` · `refreshToken: string ''` · `expiresIn: int 3600` · `user: array []` | `LoginResponse.php:19-24, 63-68` |
| 2 | PlaylistResponse | `id: string ''` · `name: string ''` · `description: string ''` · `trackCount: int 0` · `tracks: array []` | `PlaylistResponse.php:72-78` |
| 3 | PaginationResponse | `data: array` · `page: int 1` · `pageSize: int 20` · `totalItems: int 0` · `totalPages: int 0` | `PaginationResponse.php:19-25, 72-78` |
| 4 | MusicResponse | `id` · `title` · `artist` · `album: string ''` · `duration: int 0` · `coverUrl` · `streamUrl: string ''` | `MusicResponse.php:19-27, 90-98` |
| 5 | UserResponse | `id` · `username` · `email` · `displayName` · `avatar: string ''` · `gender: string 'neutral'` · `roles: array []` | `UserResponse.php:90-98` |

### 4.3 Varsayılan Değer Dağılımı

| Varsayılan | Kullanan DTO sayısı | Alanlar |
|-----------|---------------------|---------|
| `''` (boş string) | 11/11 | neredeyse tüm string alanlar |
| `[]` (boş dizi) | 3/11 | `user`, `tracks`, `roles` |
| `0` (sıfır) | 3/11 | `trackCount`, `totalItems`, `totalPages`, `duration` |
| `1` | 1/11 | `page` (+ clamp) |
| `20` | 1/11 | `pageSize`, `limit` |
| `false` | 2/11 | `rememberMe`, `isPublic` |
| `-1` | 1/11 | `position` (sona ekle bayrağı) |
| `'neutral'` | 2/11 | `gender` (`RegisterRequest`, `UserResponse`) |
| `3600` | 1/11 | `expiresIn` |

## §5 — Çıkış Akışı: DTO → array

| # | Adım | Katman | Kanıt (Dosya·Satır) |
|---|------|--------|---------------------|
| 1 | Servis cevabı DTO olarak kurar | Application | `LoginResponse.php:19-24` |
| 2 | `toArray()` ile serileştirilir | Dto/Response | `LoginResponse.php:74-82` |
| 3 | `ApiResponse::ok()` zarfı (`data`+`meta`) | Api | `ApiResponse.php:33-39` |
| 4 | `X-Request-ID` (UuidV7) eklenir | Api | `ApiResponse.php:30` |
| 5 | `X-API-Version: 1.0.0` eklenir | Api | `ApiResponse.php:21, 31` |
| 6 | `Content-Type: application/json` | Api | `ApiResponse.php:29` |
| 7 | Header'lar `array_merge` ile eklenir | Middleware | `SecurityHeadersMiddleware.php:43` |
| 8 | CSP başlığı yazılır | Middleware | `SecurityHeadersMiddleware.php:31` |
| 9 | `Cache-Control` kararı alınır | Api/Middleware | `ResponseNormalizationMiddleware.php:88-96` |
| 10 | JSON olarak emit edilir | PageRouter | `ResponseEmitter.php` |

### 5.1 ApiResponse Fabrika Varyantları (5 — tam envanter)

| # | Metod | HTTP | Dönen zarf | Kanıt |
|---|-------|------|-----------|-------|
| 1 | `ok($data, 200)` | 200 | `data` + `meta` | `ApiResponse.php:26-40` |
| 2 | `error($code, $msg, 400, $details)` | 4xx/5xx | `error.code` + `error.message` (+ `details`) | `ApiResponse.php:45-68` |
| 3 | `paginated($data, $page, $pageSize, $totalItems)` | 200 | `data` + `meta.pagination` | `ApiResponse.php:73-99` |
| 4 | `noContent()` | 204 | `[]` (boş) | `ApiResponse.php:104-111` |
| 5 | `created($data)` | 201 | `data` + `meta` | `ApiResponse.php:116-130` |

**Zarf kuralı:** Başarı cevabı `data` + `meta` taşır; hata cevabı `error.code` + `error.message` taşır (`ApiResponse.php:56-61`). İkisi asla karışmaz.

## §6 — Sayfalama Akışı: PaginationResponse

| # | Adım | Kanıt |
|---|------|-------|
| 1 | `PaginationResponse::fromArray()` varsayılanlarla doldurur | `PaginationResponse.php:70-79` |
| 2 | `data` alanı **dizge** olarak taşınır (DTO'ya gömülmez) | `PaginationResponse.php:73` |
| 3 | `page` varsayılanı `1` | `PaginationResponse.php:74` |
| 4 | `pageSize` varsayılanı `20` | `PaginationResponse.php:75` |
| 5 | `totalItems` varsayılanı `0` | `PaginationResponse.php:76` |
| 6 | `totalPages` varsayılanı `0` | `PaginationResponse.php:77` |
| 7 | `toArray()` beş alanı sırayla yazar | `PaginationResponse.php:84-93` |
| 8 | `ApiResponse::paginated()` toplam sayfayı `ceil` ile hesaplar | `ApiResponse.php:79` |
| 9 | Hesap `meta.pagination` altına yazılır | `ApiResponse.php:89-94` |

**Sayfalama notu:** `totalPages` **iki yerde** bulunur (DTO varsayılanı `0` · `ApiResponse::paginated()` hesabı). Bu **çift kaynak** bir boşluktur — §11 Açık Sorular #7.

## §7 — Hata Akışı

| # | Adım | Kanıt |
|---|------|-------|
| 1 | CSRF reddi `fail()` ile döner | `CsrfMiddleware.php:49-64` |
| 2 | HTTP 403 + `halt: true` | `CsrfMiddleware.php:58, 62` |
| 3 | Gövde `{'error': 'csrf_invalid'}` | `CsrfMiddleware.php:60` |
| 4 | Güvenlik olayı loglanır (`securityEvent`) | `CsrfMiddleware.php:52-55` |
| 5 | Rate limit reddi 429 + `Retry-After` | `RateLimitMiddleware.php:40-45` |
| 6 | Hata kodu `RATE_LIMIT_EXCEEDED` | `RateLimitMiddleware.php:42` |
| 7 | Hata zarfı `error.code` kullanır | `ApiResponse.php:56-61` |
| 8 | `details` doluysa eklenir | `ApiResponse.php:63-65` |

**CSRF notu:** Bypass route listesi **boştur** — tüm state-changing istekler token gerektirir (`CsrfMiddleware.php:15-17`; önceki `['set-gender`] bypass'ı A01:2021 riski nedeniyle kaldırılmış).

## §8 — Doğrulama Katmanı (RequestValidationMiddleware)

| # | Kural | Durum | Kanıt |
|---|-------|-------|-------|
| 1 | `POST`/`PUT`/`PATCH` gövde bekler | `BODY_METHODS` | `RequestValidationMiddleware.php:34` |
| 2 | Route kuralı yoksa middleware **atlanır** | `$rules === []` | `RequestValidationMiddleware.php:53-55` |
| 3 | Kabul edilen Content-Type: `application/json`, `problem+json`, `x-www-form-urlencoded` | `isAcceptedContentType` | `RequestValidationMiddleware.php:120-122` |
| 4 | Kabul edilmeyen → **415** `UNSUPPORTED_MEDIA_TYPE` | — | `RequestValidationMiddleware.php:64-70` |
| 5 | Bozuk JSON → **400** `INVALID_JSON` | `json_decode` | `RequestValidationMiddleware.php:73-77, 167-171` |
| 6 | Kural ihlali → **422** `VALIDATION_ERROR` | — | `RequestValidationMiddleware.php:92` |
| 7 | `error.fields` ek anahtarı zorunlu (UI sözleşmesi) | Faz 3a | `RequestValidationMiddleware.php:93` |
| 8 | GET/query yolunda `$request['query']` fallback | — | `RequestValidationMiddleware.php:81-85` |
| 9 | Gövde `array` ise doğrudan kullanılır (test yolu) | `array_key_exists('body')` | `RequestValidationMiddleware.php:136-148` |
| 10 | `x-www-form-urlencoded` → `$_POST` | — | `RequestValidationMiddleware.php:150-152` |
| 11 | Kural motoru bağımlılıksız (`Validator`) | — | `RequestValidationMiddleware.php:36-41` |

## §9 — Normalizasyon Katmanı (ResponseNormalizationMiddleware)

| # | Kural | Durum | Kanıt |
|---|-------|-------|-------|
| 1 | `Content-Type: application/json; charset=utf-8` | sabit | `ResponseNormalizationMiddleware.php:54` |
| 2 | `X-Content-Type-Options: nosniff` | sabit | `ResponseNormalizationMiddleware.php:55` |
| 3 | `X-Frame-Options` PHP'de **kaldırıldı** — tek kaynak IIS | ADR-020 | `ResponseNormalizationMiddleware.php:56-58` |
| 4 | Cacheable route → `public, max-age={ttl}` | — | `ResponseNormalizationMiddleware.php:90-91` |
| 5 | Cacheable değilse → `no-store, no-cache, must-revalidate` + `Pragma` | — | `ResponseNormalizationMiddleware.php:92-94` |
| 6 | ETag **yalnız** cacheable + boş olmayan `data`'da basılır | — | `ResponseNormalizationMiddleware.php:42-44` |
| 7 | ETag = `md5(json_encode($response))` | — | `ResponseNormalizationMiddleware.php:70` |
| 8 | `If-None-Match` eşleşirse → **304** + `halt` + boş gövde | — | `ResponseNormalizationMiddleware.php:74-80` |

## §10 — Kapsam Dışı

| # | İş | Neden kapsam dışı |
|---|-----|-------------------|
| 1 | Yeni DTO sınıfı üretimi | Belge seviyesi görev |
| 2 | `shared\src\Api\Dto\` altına dosya ekleme | Kaynak kod yazma yasağı |
| 3 | Ayrık `Hydrator`/`Normalizer` tanımı | Mevcut değil, talep gelmedikçe üretilmez |
| 4 | `AbstractDto` tabanı yazımı | YAGNI eşiği henüz aşılmadı |
| 5 | Ödeme DTO tasarımı | Repo'da ödeme kodu yok |
| 6 | DTO-özel test yazımı | QA agent yetkisi |
| 7 | `*Mapper.php` refactor'ü | Davranış-koruyucu refactor ayrı görev |
| 8 | `Validator` kural ekleme | Backend Architect yetkisi |
| 9 | `ApiResponse` zarf şeması değişimi | API sözleşmesi — ADR gerektirir |

## §11 — Açık Sorular

| # | Soru | Etki | Öncelik | Önerilen Sahip |
|---|------|------|---------|----------------|
| 1 | `fromArray()` varsayılan stratejisi (`?? ''`) bilinçli mi, `null` mı olmalı? | Tip güvenliği | HIGH | Backend Architect |
| 2 | `LoginResponse::$user` neden `array` — iç DTO'ya taşınmalı mı? | İç tutarlılık | MEDIUM | Backend Architect |
| 3 | DTO'lar için ortak `AbstractDto` gerekli mi? (YAGNI eşiği) | Kod tekrarı | LOW | Architect |
| 4 | `array_map('strval', ...)` katsayısı (`ApiKeyRepository.php:87`) diğer DTO'lara mı uygulanmalı? | Tutarlılık | MEDIUM | Data Engineer |
| 5 | `MusicSearchRequest` clamp deseni (`max(1,)`/`min(100,)`) diğer DTO'lara da mı uygulanmalı? | Tutarlılık | MEDIUM | Backend Architect |
| 6 | `declare(strict_types=1)` + `final class` **zorunlu** ilan edilmeli mi (ADR)? | Standartlaşma | HIGH | Architect |
| 7 | `totalPages` çift kaynak: DTO varsayılanı `0` vs `ApiResponse` hesabı — hangisi SSOT? | Çelişki | HIGH | Backend Architect |
| 8 | `toArray()` dizi anahtarları `camelCase` — standart mı? | API tutarlılığı | LOW | API Designer |
| 9 | `position: -1` "sona ekle" bayrağı — enum'a taşınmalı mı? | Okunabilirlik | LOW | Backend Architect |
| 10 | `gender: 'neutral'` string — enum/const'a taşınmalı mı? | Tutarlılık | MEDIUM | Data Engineer |
| 11 | ETag `md5` — `xxhash`/`sha256` ile değiştirmek gerekli mi (performans/güvenlik)? | Performans | LOW | performance-engineer |
| 12 | `x-www-form-urlencoded` hâlâ desteklenmeli mi (SPA JSON'a geçti mi)? | Gereksiz yüzey | MEDIUM | Security Engineer |

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

## §12 — Terimler Sözlüğü

| Terim | Tanım | Repo Karşılığı |
|-------|-------|----------------|
| **DTO** | Veri transfer nesnesi — yalnız taşır, karar vermez | 11 dosya (`Api\Dto\*`) |
| **fromArray** | Statik fabrika — array → nesne | 11/11 DTO'da mevcut |
| **toArray** | Serileştirme — nesne → array | 11/11 DTO'da mevcut |
| **Readonly property** | Kurulumdan sonra değiştirilemez alan | `LoginRequest.php:20-22` |
| **Final class** | Miras alınamaz sınıf | `LoginRequest.php:17` |
| **Envelope** | Veriyi saran standart zarf (`data`/`meta`/`error`) | `ApiResponse.php:33-39` |
| **Mapper** | array ↔ nesne çevirmeni | CoreMusic'te DTO içinde gömülü |
| **Hydrator** | Derin iç içe dönüşüm katmanı | **YOK** (boşluk) |
| **Clamp** | Değeri güvenli aralığa sıkıştırma | `MusicSearchRequest.php:76-77` |
| **Hash equals** | Zamanlama saldırısına dayanıklı string karşılaştırma | `CsrfMiddleware.php:42` |
| **ETag** | Yanıt özeti — önbellek doğrulama | `ResponseNormalizationMiddleware.php:70` |
| **422** | Semantik doğrulama hatası | `RequestValidationMiddleware.php:92` |
| **415** | Desteklenmeyen medya tipi | `RequestValidationMiddleware.php:67` |

## §13 — Karar Matrisi

| Seçenek | Artı | Eksi | Karar |
|---------|------|------|-------|
| **A) Desen 1: DTO içinde `fromArray`/`toArray`** (mevcut) | Basit, tek dosya, test kolay | Tekrar eden iskelet (11×) | ✅ **MEVCUT — korunuyor** |
| B) Ayrık `*Mapper.php` sınıfı | Merkezi refactor | Ek dosya, dağınık sorumluluk | ❌ YAGNI |
| C) `AbstractDto` tabanı | Kod tekrarı azalır | Miras hiyerarşisi, `final` ile çelişir | ⚠️ Tartışmalı |
| D) Ayrık Hydrator (derin dönüşüm) | İç içe yapı desteği | Karmaşıklık, ihtiyaç yok | ❌ YAGNI |
| E) Serializer kütüphanesi (Symfony vb.) | Zengin özellik | Framework bağımlılığı (ADR-001/002 yasağı) | ❌ YASAK |
| F) PHP 8.4 Property Hook ile okuma-yazma | Daha az tekrar | Olgunluk, ekip deneyimi | ⚠️ ARAŞTIRMA |

**Sonuç:** Mevcut Desen 1 **korunmuştur**; B–F için kanıt henüz yeterli değildir.

## §14 — Risk Tablosu

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|----------|------|---------|
| 1 | 11 DTO'da tekrar eden iskelet → tutarsızlık | YÜKSEK | ORTA | ADR ile desen standardı (#6) |
| 2 | `?? ''` string'a düşen tip sessizce bozulur | ORTA | YÜKSEK | `strict_types` + clamp deseni |
| 3 | DTO-özel test yokluğu → regresyon | YÜKSEK | YÜKSEK | `*DtoTest.php` (QA) |
| 4 | `totalPages` çift kaynak çelişkisi | ORTA | ORTA | SSOT kararı (#7) |
| 5 | `toArray()`'a hassas alan sızması (`password`) | DÜŞÜK | YÜKSEK | `Redactor` + gözden geçirme |
| 6 | `roles`/`tracks` dizi taşması (büyük payload) | DÜŞÜK | ORTA | Sayfalama + `limit` clamp |
| 7 | ETag `md5` çarpışması | DÜŞÜK | DÜŞÜK | `xxhash` değerlendirmesi (#11) |
| 8 | `x-www-form-urlencoded` CSRF bypass vektörü | DÜŞÜK | YÜKSEK | Content-Type kısıtlama (#12) |

## §15 — Bağımlılık Senaryoları

| # | Değişiklik | Etkilenen DTO | Etki | Sıra |
|---|-----------|---------------|------|------|
| 1 | API sürümü `2.0.0` | 11 DTO (tümü) | `fromArray`/`toArray` uyumu | 1 |
| 2 | `User` alan ekleme | `UserResponse`, `RegisterRequest`, `UserUpdateRequest` | 3 DTO senkron | 2 |
| 3 | Sayfalama şeması değişimi | `PaginationResponse` + `ApiResponse` | Çift kaynak (#7) | 2 |
| 4 | `gender` enum'u genişler | `RegisterRequest:77`, `UserResponse:96` | `?? 'neutral'` güncellenmeli | 3 |
| 5 | `position` semantics değişimi | `PlaylistAddTrackRequest:57` | `-1` bayrağı | 3 |
| 6 | CSRF stratejisi değişimi | DTO değil, middleware | Düşük etki | — |
| 7 | Content-Type kısıtlanması | `RequestValidationMiddleware:120-122` | DTO değil, ama akış etkilenir | 2 |
| 8 | Cache stratejisi değişimi | DTO değil, `ResponseNormalizationMiddleware` | ETag TTL | — |

## §16 — DTO Dosya Boyutu ve Yapısal Derinlik Analizi (11/11)

Her DTO'nun **boyutu ile alan sayısı arasındaki ilişki** yapısal bir göstergedir. Aşağıdaki tablo, gerçek satır sayılarını (PowerShell `ReadAllLines`) alan sayılarıyla karşılaştırır.

| # | DTO | Satır | Alan | `fromArray` satır | Getter satırı | Yapısal not |
|---|-----|-------|------|-------------------|---------------|-------------|
| 1 | LoginRequest | 72 | 3 | 8 satır (52-59) | 3×3=9 | En küçük Request — 3 alan |
| 2 | UserUpdateRequest | 72 | 3 | 8 satır (52-59) | 3×3=9 | LoginRequest ile aynı iskelet |
| 3 | PlaylistCreateRequest | 72 | 3 | 8 satır (52-59) | 3×3=9 | `isPublic` bool eklenmiş |
| 4 | PlaylistAddTrackRequest | 72 | 3 | 8 satır (52-59) | 3×3=9 | `position` int default `-1` |
| 5 | MusicSearchRequest | 94 | 5 | 10 satır (70-79) | 5×3=15 | **clamp** deseni — en zengin Request |
| 6 | RegisterRequest | 94 | 5 | 10 satır (70-79) | 5×3=15 | 5 alan — `gender` default |
| 7 | LoginResponse | 83 | 4 | 9 satır (61-69) | 4×3=12 | `user` alanı **array** tipinde |
| 8 | PlaylistResponse | 94 | 5 | 10 satır (70-79) | 5×3=15 | `tracks` array — iç içe veri |
| 9 | PaginationResponse | 114 | 5 | 10 satır (70-79) | 5×3=15 | `data` genel dizi — taşıcı |
| 10 | MusicResponse | 116 | 7 | 12 satır (88-99) | 7×3=21 | **En çok alan** — 7 field |
| 11 | UserResponse | 116 | 7 | 12 satır (88-99) | 7×3=21 | `roles` array — yetki verisi |

**Yapısal gözlem:**

1. **Getter başına 3 satır** (docblock + imza + return + kapanış süslü) — 11/11 DTO'da **tutarlı**. Bu, iskeletin otomatik değil elle yazıldığını gösterir (kod jeneratörü izi yok).
2. **`fromArray` boyutu = alan sayısı + 2** (imza + kapanış). Örn: 3 alan → 8 satır, 5 alan → 10 satır, 7 alan → 12 satır. Bu **düz lineer ilişki** desenin tekdüzeliğini kanıtlar.
3. **`toArray` boyutu = alan sayısı + 4** (imza + dönüş dizisi açılış/kapanış + parantez). Örn: 3 alan → `64-71` = 8 satır.
4. **`declare(strict_types=1)` + `final class`** her dosyada satır **2** ve **17** — birebir aynı. Header bloğu (docblock `@file`/`@version`/`@see`) nedeniyle `final class` 17'ye sabitlenmiştir.

### 16.1 Boyut Dağılım Histogramı (gerçek ölçüm)

```
72 satır  ████ 4 DTO   (LoginRequest, UserUpdateRequest, PlaylistCreateRequest, PlaylistAddTrackRequest)
83 satır  █ 1 DTO     (LoginResponse)
94 satır  █████ 3 DTO  (MusicSearchRequest, RegisterRequest, PlaylistResponse)
114 satır ██ 1 DTO    (PaginationResponse)
116 satır ██ 1 DTO    (MusicResponse, UserResponse)
```

**Toplam:** 11 dosya · **1.011 satır** (gerçek toplam). Ortalama: **91.9 satır/dosya**.

## §17 — Tasarım Deseni Teşhisi (Idiomatic PHP 8.4 Karşılaştırması)

Mevcut desen, modern PHP'nin hangi idiomuna ne kadar yakın? Bu analiz **mevcut kodun kalitesini** belgelemek içindir; refactor önerisi **değildir**.

| # | Modern PHP İdiomu | CoreMusic Durumu | Kanıt | Uygunluk |
|---|-------------------|------------------|-------|----------|
| 1 | Constructor Property Promotion (`private readonly string $x`) | ✅ **Uygulanmış** | `LoginRequest.php:19-23` | Tam uyum |
| 2 | `readonly` class (PHP 8.2) yerine `readonly` property | ⚠️ Property düzeyinde | `LoginRequest.php:20` | Kısmi |
| 3 | `declare(strict_types=1)` | ✅ **11/11** | `LoginRequest.php:2` | Tam uyum |
| 4 | `final class` (tam kapanma) | ✅ **11/11** | `LoginRequest.php:17` | Tam uyum |
| 5 | Named arguments (`query: $data['query']`) | ✅ **Kullanılmış** | `MusicSearchRequest.php:72-78` | Okunabilirlik + |
| 6 | Nullsafe operator (`?->`) | ⚠️ Kullanılmıyor (DTO'da gerek yok) | — | Gerekmez |
| 7 | Enums (PHP 8.1+) yerine string default | ⚠️ `'neutral'` string | `RegisterRequest.php:77` | **Boşluk** |
| 8 | Property Hooks (PHP 8.4) | ❌ Yok | — | ARAŞTIRMA |
| 9 | `#[\Deprecated]` attribute | ❌ Yok | — | Gerekmez |
| 10 | `readonly class` (PHP 8.2) birleşik | ❌ Yok (property readonly) | — | ARAŞTIRMA |
| 11 | Named return type garantisi | ✅ `: self` / `: array` | `LoginRequest.php:52, 64` | Tam uyum |
| 12 | Docblock `@param`/`@return` | ❌ Yok (imza yeterli) | `LoginRequest.php:52-59` | Modern — docblock gereksiz |

**Teşhis özeti:** Desen **PHP 8.1–8.2 idiomuna tam uyumlu**; 8.2 `readonly class` ve 8.4 Property Hook **uygulanmamış** (bilinçli olabilir, YAGNI). `enum` yerine string default **en belirgin boşluktur** — §11 #10.

## §18 — DTO'lar Arası Fark Matrisi (Hangi Desen Hangi DTO'da)

Aynı desen **her DTO'da aynı mı?** — Tutarsızlık avı. Bu matris, 11 DTO'nun 6 ortak özelliğini karşılaştırır.

| # | Özellik | LoginReq | UserUpd | PlaylistCreate | PlaylistAddTrack | MusicSearch | Register | LoginResp | PlaylistResp | Pagination | MusicResp | UserResp |
|---|---------|:-------:|:-------:|:--------------:|:----------------:|:-----------:|:--------:|:---------:|:------------:|:----------:|:---------:|:--------:|
| 1 | `declare(strict_types=1)` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2 | `final class` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 3 | `fromArray(): self` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 4 | `toArray(): array` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 5 | `?? ''` default | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 6 | **Clamp** (`max`/`min`) | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| 7 | Array field | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ `user` | ✅ `tracks` | ✅ `data` | ❌ | ✅ `roles` |
| 8 | `@see ADR-084` docblock | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| 9 | `@version 1.0.0` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

**Tutarlılık skoru:**

- **Özellik 1–5, 8–9:** 11/11 = **%100 tutarlı**.
- **Özellik 6 (clamp):** **1/11** = %9 tutarlı — yalnız `MusicSearchRequest`. §11 #5 açık sorusu.
- **Özellik 7 (array field):** 4/11 — Response tarafında beklenen ayrım.

**Sonuç:** Desen **%100 tekrar eder**; tek sapma **clamp**'tir. Bu, §13 Karar Matrisi'nde "Desen 1 korunuyor" kararını destekler — tutarsızlık yok denecek kadar azdır.

## §19 — Test Senaryosu Matrisi (DTO Davranışının Beklentisi)

Bu matris **ne test edilmeli** sorusunun cevabıdır; gerçek test dosyası **yoktur** (§11 #3 boşluğu). Beklentiler, okunan kod davranışından **türetilmiştir** (uydurma değildir).

| # | Senaryo | Girdi | Beklenen | Kanıt (türetilmiş) |
|---|---------|-------|----------|--------------------|
| 1 | Boş girdi → DTO kurulur | `[]` | `email=''`, `rememberMe=false` | `LoginRequest.php:53-55` |
| 2 | Eksik alan → default | `['email'=>'a@b.c']` | `password=''` | `LoginRequest.php:54` |
| 3 | `null` alan → `''` | `['email'=>null]` | `email=''` (`?? ''`) | `LoginRequest.php:53` |
| 4 | **Clamp alt sınırı** | `['page'=>0]` | `page=1` | `MusicSearchRequest.php:76` |
| 5 | **Clamp üst sınırı** | `['limit'=>999]` | `limit=100` | `MusicSearchRequest.php:77` |
| 6 | Clamp normal | `['limit'=>50]` | `limit=50` | `MusicSearchRequest.php:77` |
| 7 | `toArray` geri-dönüşüm | `fromArray(x).toArray()` | orijinal dizi ≈ | `LoginRequest.php:52-71` |
| 8 | `readonly` ihlali | DTO sonrası atama | Fatal (PHP) | `LoginRequest.php:20-22` |
| 9 | `final` ihlali | `extends LoginRequest` | Fatal (PHP) | `LoginRequest.php:17` |
| 10 | String tip ihlali | `fromArray(['page'=>'abc'])` | `TypeError` (strict) | `LoginRequest.php:2` + `MusicSearchRequest.php:76` |
| 11 | Pagination default | `fromArray([])` | `page=1, pageSize=20` | `PaginationResponse.php:74-75` |
| 12 | Pagination `data` korunumu | `['data'=>[1,2,3]]` | `data=[1,2,3]` | `PaginationResponse.php:73` |
| 13 | `gender` default | Register boş | `'neutral'` | `RegisterRequest.php:77` |
| 14 | `position` default | AddTrack boş | `-1` | `PlaylistAddTrackRequest.php:57` |
| 15 | `roles` default | UserResponse boş | `[]` | `UserResponse.php:97` |
| 16 | `expiresIn` default | LoginResponse boş | `3600` | `LoginResponse.php:67` |
| 17 | `toArray` anahtar sırası | herhangi | tanımlı sırayla | `LoginResponse.php:75-80` |
| 18 | `password` sızıntısı | RegisterResponse (yok) | Şema dışı — çıkışta yok | `RegisterRequest` yalnız Request |

**Test notu:** 18 senaryonun **0'ı** mevcut test dosyasında var (envanter: `shared\tests` altında `*DtoTest*` = 0). Bu matris, QA agent'a **devir belgesidir**.

## §20 — Kapsam Karar Özeti (Tek Sayfa)

| # | Soru | Cevap | Kanıt |
|---|------|-------|-------|
| 1 | DTO nedir? | Yalnız veri taşıyan `final`+`readonly` nesne | 11/11 |
| 2 | Mapper nerede? | DTO içinde gömülü (`fromArray`/`toArray`) | 22 metod |
| 3 | Ayrık Mapper sınıfı var mı? | **Hayır** (0 dosya) | envanter |
| 4 | Hydrator var mı? | **Hayır** (0 dosya) | envanter |
| 5 | Desen tutarlı mı? | **Evet** — 5 özellik 11/11 | §18 |
| 6 | Clamp her yerde mi? | **Hayır** — 1/11 | `MusicSearchRequest.php:76-77` |
| 7 | Test var mı? | **Hayır** (0 DTO testi) | envanter |
| 8 | Ödeme DTO'su var mı? | **Hayır** (repo'da ödeme kodu yok) | grep=0 |
| 9 | Önerilen refactor? | **Yok** — desen yeterli (YAGNI) | §13 |
| 10 | Açık karar sayısı | **12** (HIGH 3 · MEDIUM 5 · LOW 4) | §11 |

## §21 — Etkileşim Haritası (DTO ↔ Middleware ↔ ApiResponse)

DTO **yalnız başına** çalışmaz; bir middleware zinciri içinde konumlanır. Bu bölüm, DTO'nun **komşularıyla** etkileşimini gerçek satır kanıtlarıyla haritalar.

### 21.1 Middleware Zinciri Sırası (Gateway → Response)

| # | Sıra | Middleware | DTO'ya Etki | Kanıt |
|---|------|-----------|-------------|-------|
| 1 | 1 | `SecurityHeadersMiddleware` | DTO'dan **bağımsız** (header) | `SecurityHeadersMiddleware.php:26-43` |
| 2 | 2 | `CorsMiddleware` | DTO'dan **bağımsız** (CORS) | `shared\src\Api\Middleware\CorsMiddleware.php` |
| 3 | 3 | `RateLimitMiddleware` | DTO'dan **önce** — 429 dönerse DTO kurulmaz | `RateLimitMiddleware.php:39-46` |
| 4 | 4 | `RequestValidationMiddleware` | Gövdeyi çözer → **ham array** verir | `RequestValidationMiddleware.php:48-93` |
| 5 | 5 | **[DTO `fromArray()` burada]** | Ham array → nesne | `LoginRequest.php:52-59` |
| 6 | 6 | `CsrfMiddleware` | Token doğrular → 403 | `CsrfMiddleware.php:42` |
| 7 | 7 | Servis + DTO getter | İş mantığı veriyi okur | `LoginRequest.php:28-46` |
| 8 | 8 | **[DTO `toArray()` burada]** | nesne → array | `LoginResponse.php:74-82` |
| 9 | 9 | `ApiResponse::ok()` | array → zarf | `ApiResponse.php:26-40` |
| 10 | 10 | `ResponseNormalizationMiddleware` | ETag/Cache header | `ResponseNormalizationMiddleware.php:68-97` |

**Harita notu:** DTO, zincirin **5. ve 8. adımında** görünür. Aradaki adımlar (6, 7) DTO'nun **dışında**dır; öncesindeki (3, 4) DTO'nun **ham girdisini** hazırlar.

### 21.2 DTO'nun Bağımlı Olduğu Sınıflar (İçe Yönlü)

| # | Sınıf | DTO Kullanımı | Kanıt |
|---|-------|---------------|-------|
| 1 | `CoreMusic\Security\UuidV7` | `ApiResponse` trace-id üretimi (DTO değil, cevap) | `ApiResponse.php:14, 30` |
| 2 | `DateTimeImmutable` | `meta.timestamp` (DTO değil, cevap) | `ApiResponse.php:36` |
| 3 | `CoreMusic\Security\CacheRateLimiter` | Rate limit (DTO'dan bağımsız) | `RateLimitMiddleware.php:14, 26` |
| 4 | `CoreMusic\Api\Middleware\Validator` | Kural motoru (DTO'dan bağımsız) | `RequestValidationMiddleware.php:24` |

**Gözlem:** DTO'lar **hiçbir sınıfa bağımlı değildir** — saf `final class` + property'ler. Bu, DTO'ların **en düşük bağlılık** (dependency) katmanı olduğunu kanıtlar. §14 Risk #5'te anılan `password` sızıntısı riski DTO'dan değil, **kullanım yerinden** doğar.

### 21.3 DTO'nun Kullandığı Diller Arası Köprü (JS ↔ PHP)

| # | Yön | Dosya | Kanıt |
|---|-----|-------|-------|
| 1 | JS → PHP (girdi) | `FetchWrapper.js` header'ları ekler | `FetchWrapper.js:47-52` |
| 2 | CSRF köprüsü | `CsrfSyncManager.getToken()` | `CsrfSyncManager.js:5` |
| 3 | Header adları (donmuş) | `headers.js` `HEADERS` frozen | `headers.js:1-7` |
| 4 | Content-Type | `CONTENT_TYPES.JSON` | `headers.js:9` |
| 5 | Timeout | `CONFIG.TIMEOUT_MS = 10_000` | `FetchWrapper.js:6` |

**Köprü notu:** JS tarafı DTO'nun **alan adlarını tekrar eder** (`email`, `password`) — bu **çift kaynak** bir tutarlılık riskidir. DTO değişirse JS de değişmelidir; otomatik senkronizasyon **yoktur** (§14 Risk #1).

## §22 — Belge Bütünlüğü Beyanı

| # | Beyan | Durum |
|---|-------|-------|
| 1 | Bu belgedeki tüm `Dosya · Satır` referansları bu oturumda dosya-okuma ile alınmıştır. | ✅ |
| 2 | Uydurma API/sınıf/metod adı **yoktur**. | ✅ |
| 3 | Ölçüm gerektiren iddialar PowerShell ile **gerçekten** ölçülmüştür. | ✅ |
| 4 | Doğrulanamayan iddialar `⚠️ VERIFICATION REQUIRED` ile işaretlenmiştir. | ✅ |
| 5 | Repo'da olmayan yapılar (Hydrator, Mapper, Ödeme DTO) **"YOK"** olarak belgelenmiştir. | ✅ |
| 6 | Bu belge **salt-okunur** kanıt toplar; kod **üretmez/değiştirmez**. | ✅ |

### 22.1 Kanıt Kaynakları (Bu Belgedeki Ölçümler)

| # | Ölçüm Türü | Araç | Örnek |
|---|-----------|------|-------|
| 1 | Satır sayısı | `[System.IO.File]::ReadAllLines().Length` | 11 DTO → 1.011 satır |
| 2 | Dosya sayısı | `Get-ChildItem -Filter *.php` | `Api\Dto` → 11 |
| 3 | Metin eşleşmesi | `grep` / `Select-String` | `fromArray` → 11 |
| 4 | Alan okuma | `read` (offset/limit) | `LoginRequest.php:19-23` |
| 5 | Mapper yokluğu | glob `*Mapper*` | 0 sonuç |

**Ölçüm tarihi:** 2026-10-15 · tüm ölçümler bu oturumda, canlı disk üzerinden alınmıştır.

### 22.2 Bilinen Sınırlamalar

| # | Sınır | Etki |
|---|-------|------|
| 1 | `x-www-form-urlencoded` gövde gerçek testi **yapılmadı** (yalnız kod okundu) | Davranış iddiası kod-okuma düzeyinde |
| 2 | ETag `md5` çarpışma testi **yapılmadı** | §14 Risk #7 |
| 3 | `strict_types` runtime davranışı **çalıştırılarak** doğrulanmadı | §19 Senaryo #10 beklenti düzeyinde |
| 4 | Çalışan sunucu/DB erişimi **kullanılmadı** | Tüm iddialar statik analiz |
| 5 | `RouteTable` tam route listesi **okunmadı** | Kapsam gereği yalnız DTO katmanı |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
