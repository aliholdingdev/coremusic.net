---
node_id: K013-integraion/error-handling/error-web-arastirma
node_type: technology-deepdive
title: "Error Web Araştırması — Kaynak Doğrulama Raporu (13 resmi kaynak)"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/error-handling
children: []
depends_on:
  - K013-integraion/error-handling
  - K013-integraion
evidence:
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc7807.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16)"
  - "https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html (2026-10-16)"
  - "https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ (2026-10-16)"
  - "https://cloud.google.com/storage/docs/retry-strategy (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/retry (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/throttling (2026-10-16)"
  - "https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
  - "https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html (2026-10-16)"
validation:
  - "13 benzersiz resmi kaynak fetch ile doğrulandı (2026-10-16) — hedef ≥12 AŞILDI"
  - "Her iddia §3 tablosunda İddia | URL | Tarih | Durum satırına sahiptir"
  - "Fetch edilemeyen/elenen adaylar §9'da nedeniyle listelenmiştir"
  - "404 iddiası UYDURULMAZ — yalnız çekim sırasında 404/alınamayanlar elenir"
open_questions:
  - "GitHub Actions rate limits sayfası bu turda çekilmedi → ek kaynak olarak eklenebilir"
  - "Google API design/errors genel sayfası bu turda çekilmedi → iddia kapsamı dışında"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: kanit
---

# Error Web Araştırması — Kaynak Doğrulama Raporu (13 resmi kaynak)

> **Bu dosyanın işi:** `error-*.md` dosyalarındaki her önemli iddianın bir
> **fetch edilmiş resmi kaynakla** eşleşmesini sağlamaktır. Zero-hallucination:
> doğrulanamayan iddia bu klasörde barınamaz. Erişim tarihi: 2026-10-16.

---

## §0 ELI15 — Bu dosyanın işi

"Retry üstel backoff ister" demek kolay; önemli olan bunun **resmi bir
kaynakta** yazdığı ve tarihinin alınabildiğidir. Bu dosya 13 kaynağı
**çekti**, iddiaları tek tek eşledi, çekilemeyenleri nedeniyle eleme
kaydına yazdı. Diğer `error-*.md` dosyalarındaki `[S#]` işaretleri bu
tablodaki numaralara gider.

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `error-anayasa.md` | Sınıflandırma eksenleri, mekanizma seçimi, sınır | Kademe-3 |
| mimari | `error-sozlesme.md` | Hata sözleşmesi, Problem Details, eşleme tablosu | Kademe-3 |
| kod | `error-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `error-kod-ornekleri.md` | 13 dilde retry/circuit breaker örneği | Kademe-3 |
| **kanit** | **`error-web-arastirma.md`** | **≥12 benzersiz resmi kaynak** | Kademe-3 |

---

## §1 Yöntem

| Adım | İşlem |
|---|---|
| 1 | Konu kümeleri: RFC'ler · AWS · Google · Microsoft · GitHub · Stripe |
| 2 | `websearch` ile aday URL toplama (2 sorgu) |
| 3 | `webfetch` ile her adayın **tam metni** çekilir (title + içerik doğrulanır) |
| 4 | İddia → çekilen metindeki cümleye bağlanır |
| 5 | 404/alınamayan adaylar elenir + neden yazılır (§9) |
| 6 | Çapraz doğrulama: kritik iddialar ikinci kaynakla teyit edilir (§6) |

**Kural:** Websearch sonuçları **kanıt değildir** (§5); yalnız fetch edilmiş
sayfalar kanıt sayılır.

---

## §2 İddia Kümesi — Bu Turda Kanıtlanan Konular

| # | Konu | Dosya(lar) |
|---|---|---|
| C-1 | HTTP durum kodu sınıflandırması (hangi kod hangi sınıf) | anayasa §2, sozlesme §3.2 |
| C-2 | 429 Too Many Requests + Retry-After (RFC) | anayasa §7, sozlesme §5 |
| C-3 | Problem Details RFC 7807 → RFC 9457 (obsoletes) | sozlesme §1 |
| C-4 | Idempotent/non-idempotent retry kuralı (RFC 9110 §9.2.2) | anayasa §2, dogrulama D-6 |
| C-5 | AWS retry parametreleri (3 / 50ms / 1000ms / 20000ms / full jitter / quota) | anayasa §4.2, kod-ornekleri §0 |
| C-6 | Jitter çeşitleri ve kümelenme kırılması | anayasa §4.3, dogrulama D-4 |
| C-7 | Circuit breaker durum makinesi (Closed/Open/Half-Open) | anayasa §5, dogrulama D-7 |
| C-8 | Google retry'lenebilir kodlar + C++/Go defaultları | anayasa §4.2, sozlesme §3.3 |
| C-9 | GitHub rate limit (60/5000/saat; header'lar; secondary; ban) | anayasa §7.1, sozlesme §3.4 |
| C-10 | Stripe rate limit (100/25 rps; Stripe-Rate-Limited-Reason; lock_timeout) | sozlesme §3.4 |
| C-11 | Microsoft Graph throttling (429 + Retry-After) | anayasa §7.1 |
| C-12 | Dead-letter queue (maxReceiveCount; retention; FIFO) | anayasa §8 |
| C-13 | Azure retry stratejileri (Cancel / Retry immediately / Retry after delay) | anayasa §3.2 |

---

## §3 Ana Doğrulama Tablosu (13 doğrulanmış kaynak)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| RFC 9110 §9.2.2: "A client SHOULD NOT automatically retry a request with a non-idempotent method" + "A proxy MUST NOT automatically retry non-idempotent requests" | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| RFC 9110 §10.2.3: Retry-After = HTTP-date / delay-seconds; 503 ve 3xx yanıtlarında kullanılır | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| RFC 9110 §15.6.4: 503 Service Unavailable — "The server MAY send a Retry-After header field" | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| RFC 9110 §15.5.9: 408 Request Timeout tanımlıdır | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| RFC 6585 §4: 429 Too Many Requests tanımlandı; Retry-After MAY; 429 cache'lenemez | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ çekildi |
| RFC 7807: Problem Details for HTTP APIs — ilk tanım; type/title/status/detail/instance alanları | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-16 | ✅ çekildi |
| RFC 9457 başlık satırı: "Obsoletes: 7807"; application/problem+json; IANA HTTP Problem Types registry; Appendix A JSON Schema | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✅ çekildi |
| AWS SDK retry: standard/adaptive/legacy; standard max_attempts default 3; transient base 50ms; throttling base 1000ms; delay cap 20000ms; full jitter (delay = random(0,1) × min(20000ms, base×2^retry)); retry quota 500 (transient 14, throttling 5); throttling kodları (ThrottlingException vb.); x-amz-retry-after | https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html | 2026-10-16 | ✅ çekildi |
| AWS Architecture Blog (Marc Brooker, 4 Mar 2015; May 2023 güncellemesi): jitter'sız exponential backoff'ta çağrılar kümelere biner; Full Jitter çağrı sayısını yarıdan fazla azaltır; Equal Jitter ve Decorrelated Jitter tarifleri; "no-jitter exponential backoff is the clear loser" | https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ | 2026-10-16 | ✅ çekildi |
| Google Cloud Storage retry: retry'lenebilir = HTTP 408/429/500/502/503/504 + bağlantı hataları; gcloud truncated binary exponential backoff (max 32); C++: auto retry true, initial 1s, multiplier 2, max wait 5dk, max süre 15dk; Go (gax): initial 1s, multiplier 2.0, max delay 30s; idempotent-koşullu operasyonlar (ifGenerationMatch/etag) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi |
| Azure Retry pattern: stratejiler Cancel / Retry immediately / Retry after delay; idempotency uyarısı; nested retry gecikme uyarısı; erken denemeler informational log, son deneme error | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✅ çekildi |
| Azure Circuit Breaker pattern: Closed/Open/Half-Open durumları; eşikli failure counter; 429 ivmeleme (accelerated breaking); manual override; retry circuit breaker hatalarına duyarlı olmalı | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ çekildi |
| Microsoft Graph throttling: 429 + Retry-After header (örnek: 10); "Avoid immediate retries"; Retry-After izlemek en hızlı toparlanma; yoksa exponential backoff; batch içinde tek istek 429 olabilir, batch 200 döner | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ çekildi |
| GitHub REST rate limits: unauth 60/saat; auth 5000/saat; Enterprise Cloud app 15000/saat; secondary: 100 eşzamanlı, 900 puan/dk (REST), 80 içerik/dk; x-ratelimit-limit/remaining/used/reset/resource header'ları; aşım = 403 veya 429 + remaining:0; secondary'da retry-after yoksa ≥1 dakika + exponential; "Continuing to make requests while rate limited may result in banning" | https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api | 2026-10-16 | ✅ çekildi |
| Stripe rate limits: live global 100 rps; sandbox 25 rps; endpoint 25 rps; concurrency global 50 (live); 429 + Stripe-Rate-Limited-Reason (global-rate/endpoint-rate/global-concurrency/endpoint-concurrency/resource-specific); header yoksa rate limit değil (lock_timeout); jitter + token bucket tavsiyesi; Ruby SDK Stripe.max_network_retries = 2 | https://docs.stripe.com/rate-limits | 2026-10-16 | ✅ çekildi |
| Amazon SQS DLQ: redrive policy maxReceiveCount; redrive allow policy (byQueue ≤10 ARN / denyAll); standard queue'da enqueue timestamp sabit, FIFO'da sıfırlanır; DLQ retention > kaynak retention best practice; maxReceiveCount>3 ise 3+ receive'te kuyruk arkasına taşınma | https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html | 2026-10-16 | ✅ çekildi |

**Sayım:** 17 satır · **13 benzersiz URL** (RFC 9110 dört iddia taşır) ·
hedef ≥12 **AŞILDI**.

---

## §4 İddia → Kaynak Eşlemesi (dosyalar arası)

| Dosya | İddia | Kaynak |
|---|---|---|
| error-anayasa §2.2 | 408/429/5xx geçici; 4xx kalıcı | RFC 9110 §15 + Google retry listesi [S9-GCP] |
| error-anayasa §4.2 | AWS parametreleri (3/50/1000/20000/full jitter/quota) | AWS SDK [S5] |
| error-anayasa §4.3 | Jitter kümelenme kırar | AWS Blog [S6] |
| error-anayasa §5.1 | Closed/Open/Half-Open | Azure CB [S8] |
| error-anayasa §7.1 | Retry-After semantics | RFC 9110 §10.2.3 [S1] + RFC 6585 [S2] |
| error-anayasa §8.1 | maxReceiveCount / retention / FIFO | SQS DLQ [S13] |
| error-sozlesme §1 | RFC 9457 alanları + obsoletes | RFC 9457 [S3] |
| error-sozlesme §3.4 | Sağlayıcı eşlemeleri (AWS/GitHub/Stripe/Graph) | [S5] [S10] [S12] [S11] |
| error-dogrulama D-5 | Retry-After izleme testi | [S11] [S10] [S1] |
| error-dogrulama D-6 | Non-idempotent retry yasağı | RFC 9110 §9.2.2 [S1] |
| error-dogrulama D-7 | CB durum geçiş testi | Azure CB [S8] |
| error-kod-ornekleri §0 | Ortak formül + AWS ayarı | [S5] [S6] |

---

## §5 Websearch Kayıtları (ön araştırma — kanıt DEĞİL)

| # | Sorgu | Kullanım |
|---|---|---|
| W-1 | "AWS SDK retry behavior exponential backoff jitter" | AWS SDK doc + blog adayları |
| W-2 | "RFC 6585 429 Retry-After RFC 9457 problem details" | RFC adayları |

> Websearch sonuçları yalnız aday üretir; kanıt §3'teki fetch'lerdir.

---

## §6 Çapraz Doğrulama (ikinci kaynakla teyit)

| İddia | Birinci kaynak | Teyit kaynakı | Sonuç |
|---|---|---|---|
| 429 Retry-After taşır | RFC 6585 [S2] | RFC 9110 §10.2.3 [S1] | ✅ tutarlı |
| Retry-After izleme pratikleri | Microsoft Graph [S11] | GitHub [S10] + Stripe [S12] | ✅ üç kaynak aynı yönde |
| Jitter şart | AWS Blog [S6] | Stripe [S12] ("add randomness ... thundering herd") + AWS SDK full jitter [S5] | ✅ tutarlı |
| 429 → circuit breaker ivmeleme | Azure CB [S8] | (tek kaynak — çapraz yok) | ⚠️ tek kaynak; iddia dar kullanıldı |
| Retry + circuit breaker etkileşimi | Azure Retry [S7] | Azure CB [S8] | ✅ iki Azure sayfası tutarlı |
| Non-idempotent retry yasağı | RFC 9110 §9.2.2 [S1] | Azure Retry idempotency [S7] | ✅ tutarlı |

---

## §7 Çakışma Denetimi (bu klasör)

| Alan | Kaynak-1 | Kaynak-2 | Çakışma? |
|---|---|---|---|
| Retry-After dili | RFC 6585 (MAY) | Graph (pratik: var) | Hayır — RFC izin verir, sağlayıcılar uygular |
| Base delay | AWS 50ms/1000ms | Google 1s | Hayır — sağlayıcı-bazlı; CoreMusic değeri UNKNOWN olarak bırakıldı |
| Max attempts | AWS 3 | gcloud 32 | Hayır — farklı ürün; kapı N'nin varlığını test eder, sayı dayatmaz |
| CB eşik | Azure sayı vermez | — | Hayır — sayı uydurulmadı (UNKNOWN) |

---

## §8 Eleme Denetimi — URL Çekim Sonuçları

| # | Aday | Sonuç |
|---|---|---|
| 1 | rfc-editor.org/rfc/rfc6585.html | ✅ |
| 2 | rfc-editor.org/rfc/rfc7807.html | ✅ |
| 3 | rfc-editor.org/rfc/rfc9457.html | ✅ |
| 4 | rfc-editor.org/rfc/rfc9110.html | ✅ |
| 5 | docs.aws.amazon.com/sdkref/.../feature-retry-behavior.html | ✅ |
| 6 | aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ | ✅ |
| 7 | cloud.google.com/storage/docs/retry-strategy | ✅ |
| 8 | learn.microsoft.com/azure/architecture/patterns/retry | ✅ |
| 9 | learn.microsoft.com/azure/architecture/patterns/circuit-breaker | ✅ |
| 10 | learn.microsoft.com/graph/throttling | ✅ |
| 11 | docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api | ✅ |
| 12 | docs.stripe.com/rate-limits | ✅ |
| 13 | docs.aws.amazon.com/AWSSimpleQueueService/.../sqs-dead-letter-queues.html | ✅ |

---

## §9 404 / Elenen Adaylar

| Aday | Neden elendi |
|---|---|
| — | Bu turda 404/alınamayan aday **yoktur**; 13/13 aday başarıyla çekildi |

> Not: "404 diye listelenen gerçek URL yok" — uydurma eleme kaydı da
> yazılmaz (zero-hallucination).

---

## §10 Red Listesi — Bu Turda Çekilmeyen / Yazılmayan İddialar

| İddia/Aday | Durum |
|---|---|
| Google API design/errors genel sayfası (cloud.google.com/apis/design/errors) | Çekilmedi — kapsam dışı (dahili API tasarım rehberi; hata yönetimi iddiası için şart değil) |
| GitHub Actions rate limits | Çekilmedi — ek kaynak; sonraki tur adayı |
| Resilience4j / Polly / Failsafe kütüphane dokümanları | Çekilmedi — kütüphane seçimi YAGNI (kod-ornekleri §18) |
| CoreMusic'e özgü retry/eşik değerleri | Yok — politika onayı bekliyor (UNKNOWN) |
| Circuit breaker adaptif eşik (AI/ML) uygulaması | Yok — Azure sayfasında geçiyor; uygulama varsayılmadı |
| Dead-letter için MySQL/Redis spesifik doküman | Yok — kavramsal SQS yeterli; altyapı kararı UNKNOWN |

---

## §11 Sonraki Tur İş Kalemleri

| # | İş | Öncelik |
|---|---|---|
| 1 | GitHub Actions + Google API errors fetch (ek kanıt) | Düşük |
| 2 | RFC 9457 §4.2 IANA registry'deki kayıtlı tip listesinin derin çekimi | Orta — sozlesme §1.4 açık |
| 3 | CoreMusic retry politikası ADR'si → kapı eşiklerinin kilitlenmesi | Yüksek |

---

## §12 İddia Detayları — Hangi Cümle Hangi Kapıdan Geçti

> Her kayıt: iddia → çekilen metinden **ham alıntı** (paraphrase değil,
> kaynaktaki anlam) → hangi dosyada kullanıldığı.

### W1 — RFC 9457 obsoletes (C-3)

- **Kaynak:** rfc-editor.org/rfc/rfc9457.html (fetch 2026-10-16)
- **Ham içerik:** Başlık metni "Obsoletes: 7807" satırını taşır; medya
  tipleri `application/problem+json` / `application/problem+xml`;
  §4.2 IANA'da "HTTP Problem Types" registry kurulur; Appendix A JSON
  Schema tanımlanır.
- **Kullanım:** error-sozlesme §1.1 (RFC 7807 artık tarihsel),
  error-anayasa §11.2 (yasağı).
- **Geçiş:** ✅ doğrudan alıntı-destekli.

### W2 — RFC 9110 non-idempotent retry yasağı (C-4)

- **Kaynak:** rfc-editor.org/rfc/rfc9110.html (fetch 2026-10-16; §9.2.2)
- **Ham içerik:** "A client SHOULD NOT automatically retry a request with
  a non-idempotent method" + "A proxy MUST NOT automatically retry
  non-idempotent requests."
- **Kullanım:** error-anayasa §2.1/§11.4, error-dogrulama D-6,
  error-sozlesme §3.2.
- **Geçiş:** ✅ kelimesi kelimesine.

### W3 — RFC 9110 Retry-After biçimi (C-2)

- **Kaynak:** rfc-editor.org/rfc/rfc9110.html (§10.2.3)
- **Ham içerik:** `Retry-After = HTTP-date / delay-seconds`; örnekler
  `Retry-After: Fri, 31 Dec 1999 23:59:59 GMT` ve `Retry-After: 120`;
  503'te "The server MAY send a Retry-After header field" (§15.6.4).
- **Kullanım:** error-anayasa §7.1, error-sozlesme §5.1,
  error-dogrulama D-5.
- **Geçiş:** ✅.

### W4 — RFC 6585 §4 (429) (C-2)

- **Kaynak:** rfc-editor.org/rfc/rfc6585.html (fetch 2026-10-16)
- **Ham içerik:** 429 Too Many Requests tanımı; Retry-After eklenebilir
  (MAY); yanıt cache'lenmemeli (§4 "The 429 status code ... does not
  allow caching" kavramı).
- **Kullanım:** error-anayasa §7.1, error-sozlesme §3.2/§5.
- **Geçiş:** ✅.

### W5 — AWS SDK retry parametreleri (C-5)

- **Kaynak:** docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html
- **Ham içerik:** Standard retry modu; `max_attempts` default 3; transient
  için base 50 ms, throttling için 1000 ms; delay cap 20000 ms;
  `delay = random(0,1) × min(20000ms, base×2^retry)` (full jitter);
  retry quota default 500 — transient başarısızlık 14 token, throttling
  5 token; `x-amz-retry-after` header'ı; throttling kod listesi
  (ThrottlingException, Throttling, TooManyRequestsException,
  RequestThrottled, RequestThrottledException) + 429/503.
- **Kullanım:** error-anayasa §4.2, error-kod-ornekleri §0 (13 dil
  bu ayara göre), error-dogrulama D-2/D-3.
- **Geçiş:** ✅ — parametrelerin **tamamı** bu sayfadan; uydurma yok.

### W6 — AWS Architecture Blog jitter (C-6)

- **Kaynak:** aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/
  (Marc Brooker; 04 Mar 2015; sayfada May 2023 güncellemesi var)
- **Ham içerik:** "Full Jitter", "Equal Jitter", "Decorrelated Jitter"
  üçlüsü tanımlı; 100 rakipli simülasyonda Full Jitter çağrı sayısını
  yarıdan fazla azaltır; "The no-jitter exponential backoff approach is
  the clear loser"; OCC simülasyonu N² iş yükünü makul seviyeye indirir.
- **Kullanım:** error-anayasa §4.3, error-dogrulama D-4 (P-4 property).
- **Geçiş:** ✅ — formül sembolsel (blogda grafik-formül); tam sayısal
  formül AWS SDK sayfasından (W5) alınır.

### W7 — Google Cloud Storage retry (C-8)

- **Kaynak:** cloud.google.com/storage/docs/retry-strategy
- **Ham içerik:** İki faktör — güvenli retry (idempotency) + retryable
  yanıt; retry'lenebilir: HTTP 408, 429, 500, 502, 503, 504 + socket
  hataları (ErrUnexpectedEOF, connection refused, connection reset,
  net.ErrClosed); gcloud CLI: truncated binary exponential backoff, max
  retry 32; C++: auto retry true, max süre 15 dk, initial 1 sn,
  multiplier 2, max wait 5 dk; Go (gax): initial 1 sn, multiplier 2.0,
  max delay 30 sn, resumable upload chunk timeout 32 sn; koşullu-güvenli
  retry (ifGenerationMatch/ifMetagenerationMatch/etag).
- **Kullanım:** error-anayasa §2.2/§4.2, error-sozlesme §3.3,
  error-kod-ornekleri §10.
- **Geçiş:** ✅ — sayfa Cloud Storage'a özgüdür; genel-GCP diye
  sunulmadığı §Kaynak notunda belirtildi.

### W8 — Azure Retry pattern (C-13)

- **Kaynak:** learn.microsoft.com/en-us/azure/architecture/patterns/retry
  (ms.date 2024-07-18; updated_at 2025-12-09)
- **Ham içerik:** Üç strateji — Cancel / Retry immediately / Retry after
  delay; nested retry gecikme uyarısı ("this extra layer of retries can
  add long delays"); idempotency uyarısı ("retries could cause the
  operation to be executed more than once"); logging önerisi (erken
  denemeler informational, son deneme error); circuit breaker'a devreden
  cümle ("prevent further requests going to the same resource and report
  a failure immediately").
- **Kullanım:** error-anayasa §3.2/§6, error-dogrulama D-6/D-3.
- **Geçiş:** ✅.

### W9 — Azure Circuit Breaker pattern (C-7)

- **Kaynak:** learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker
  (ms.date 2025-02-05)
- **Ham içerik:** Closed/Open/Half-Open; failure counter zaman-bazlı ve
  periyodik sıfırlanır; success counter eşikli; Half-Open flood koruması;
  429/503 notu (throttling/availability ayrımı); ivmeleme (accelerated
  breaking — hata yanıtına göre anında trip); manual override; retry
  duyarlılığı ("stop retry attempts if the circuit breaker indicates that
  a fault isn't transient"); Cosmos DB 429 örneği (Open → cached response
  → Monitor alarm).
- **Kullanım:** error-anayasa §5, error-dogrulama D-7,
  error-sozlesme §3.5.
- **Geçiş:** ✅ — eşik sayfada YOK → UNKNOWN (uydurulmadı).

### W10 — GitHub rate limits (C-9)

- **Kaynak:** docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api
- **Ham içerik:** unauth 60/saat (IP-bazlı); auth 5000/saat; Enterprise
  Cloud app 15000/saat; GITHUB_TOKEN 1000/saat/repo; secondary: 100
  eşzamanlı, REST 900 puan/dk, GraphQL 2000 puan/dk, 90 sn CPU/60 sn,
  80 içerik/dk + 500 içerik/saat, 2000 OAuth token/saat; puan tablosu
  (GET=1, POST/PATCH/PUT/DELETE=5); header'lar x-ratelimit-limit/
  remaining/used/reset/resource; aşım 403 veya 429 + remaining:0;
  secondary'da retry-after yoksa ≥60 sn + exponential; "may result in the
  banning of your integration".
- **Kullanım:** error-anayasa §7.1, error-sozlesme §3.4, error-dogrulama D-5.
- **Geçiş:** ✅.

### W11 — Stripe rate limits (C-10)

- **Kaynak:** docs.stripe.com/rate-limits
- **Ham içerik:** live 100 rps global; sandbox 25 rps; endpoint 25 rps;
  PaymentIntent 1000 update/saat/obje; concurrency global 50 (live);
  `Stripe-Rate-Limited-Reason` 5 değer; header'sız 429 = lock_timeout
  olabilir (kod: `lock_timeout`); jitter + token bucket tavsiyesi;
  `Stripe.max_network_retries = 2` (Ruby); sandbox load-test tavsiyesi
  dışı (limitler düşük).
- **Kullanım:** error-sozlesme §3.4, error-anayasa §7.1,
  error-kod-ornekleri §11.
- **Geçiş:** ✅.

### W12 — Microsoft Graph throttling (C-11)

- **Kaynak:** learn.microsoft.com/en-us/graph/throttling
  (ms.date 2025-01-14)
- **Ham içerik:** 429 + `Retry-After` (örnek yanıt: `Retry-After: 10`,
  JSON `error.code: TooManyRequests`, `request-id`); "Avoid immediate
  retries"; Retry-After izlemek en hızlı toparlanma; yoksa exponential
  backoff; SDK'lar Retry-After handler'ı içerir; batch'te istekler tek tek
  limitlere tabi, batch 200 döner, JSON içi `retry-after` ile tekrar.
- **Kullanım:** error-anayasa §7.1, error-sozlesme §3.4, error-dogrulama D-5.
- **Geçiş:** ✅.

### W13 — Amazon SQS DLQ (C-12)

- **Kaynak:** docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html
- **Ham içerik:** redrive policy `maxReceiveCount` (1 = tek başarısızlık
  yeter; "set high enough"); redrive allow policy (varsayılan: tümü;
  byQueue ≤10 ARN; denyAll); standard queue'da enqueue timestamp
  değişmez, FIFO'da sıfırlanır; retention best practice: DLQ > kaynak;
  `maxReceiveCount > 3` ve 3+ receive → kuyruk arkasına taşınma;
  redrive ile geri taşıma.
- **Kullanım:** error-anayasa §8, error-sozlesme §6, error-dogrulama D-9.
- **Geçiş:** ✅ — CoreMusic kuyruk altyapısı UNKNOWN (kavramsal kullanıldı).

---

## §13 Tur Özeti

| Metrik | Değer |
|---|---|
| Fetch edilen kaynak | 13/13 benzersiz URL (hedef ≥12 → AŞILDI) |
| İddia satırı (§3) | 17 satır / 13 URL |
| 404/alınamayan | 0 |
| Çapraz doğrulanan iddia | 5 (§6) |
| Tek-kaynak kalan iddia | 1 (CB ivmeleme — daraltıldı) |
| Uydurma parametre | 0 (tüm sayısal değerler W5/W7/W10/W11 kaynaklı) |

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | 13/13 kaynak fetch ✅ — hedef ≥12 AŞILDI | §3 |
| 2 | 404 eleneni yok (dürüst kayıt) | §9 |
| 3 | Çapraz doğrulama: 1 iddia tek kaynak (CB ivmeleme) — dar kullanıldı | §6 |
| 4 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
