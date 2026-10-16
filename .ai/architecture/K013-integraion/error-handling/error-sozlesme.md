---
node_id: K013-integraion/error-handling/error-sozlesme
node_type: technology-deepdive
title: "Error Sözleşme — Problem Details (RFC 9457) · Dış API → İç Hata Eşleme · Hata Gövdesi Şeması · Sınıf Sözleşmesi"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/error-handling
children: []
depends_on:
  - K013-integraion/error-handling
  - K013-integraion/error-handling/error-anayasa
  - K013-integraion
evidence:
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc7807.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16)"
  - "https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/retry (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker (2026-10-16)"
  - "https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/throttling (2026-10-16)"
  - "https://cloud.google.com/storage/docs/retry-strategy (2026-10-16)"
  - "https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html (2026-10-16)"
validation:
  - "RFC 9457 alanları (type/title/status/detail/instance) + 'Obsoletes: 7807' + IANA registry + Appendix A JSON Schema doğrulandı (2026-10-16 fetch)"
  - "Eşleme tablosundaki her dış kod bir fetch edilmiş kaynakla eşleşir"
  - "CoreMusic'e özgü type URI / error-code alanı → ⚠️ UNKNOWN (domain kararı yok)"
  - "Gözleme ve politika sınırları etiketli (K017 / K009)"
open_questions:
  - "CoreMusic Problem Details `type` URI şeması (örn. urn:coremusic:error:...) → ⚠️ UNKNOWN"
  - "CoreMusic `instance` kullanımı (request-id mi, path mi) → ⚠️ UNKNOWN"
  - "Dış API eşleme tablosunun uygulama katmanı (middleware vs service) → ⚠️ UNKNOWN"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: mimari
---

# Error Sözleşme — Problem Details (RFC 9457) · Dış API → İç Hata Eşleme · Hata Gövdesi Şeması · Sınıf Sözleşmesi

> **Bu dosya ne değildir:** Uygulama kodu veya belirli bir framework'ün hata
> sınıfı kataloğu değildir. Bu dosya **sözleşme yüzeyini** tanımlar: dış
> dünyaya dönen hata gövdesinin standardı (RFC 9457), dış API hatalarının
> iç hata sınıflarına eşlenme kuralı ve hata nesnesinin alan sözleşmesi.
> Mekanizma kararları `error-anayasa.md`'dedir; kapılar
> `error-dogrulama.md`'dedir.

---

## §0 ELI15 — Bu dosyanın işi

Dışarıya dönen her hata **aynı zarf** ile döner (Problem Details:
`type`/`title`/`status`/`detail`/`instance`); dışarıdan gelen her hata
**iç sınıfa** eşlenir (geçici/kalıcı/ince + kod); eşleme **tek yerdedir**
(anti-corruption layer). Müşteriye stack trace, SQL veya token asla gitmez
(K009 sınırı). Gözleme sinyalleri üretilir ama aksiyonu K017'dedir.
(Kaynak: §Kaynak Tablosu [S1]–[S12])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `error-anayasa.md` | Sınıflandırma eksenleri, mekanizma seçimi, sınır | Kademe-3 |
| **mimari** | **`error-sozlesme.md`** | **Hata sözleşmesi, Problem Details, eşleme tablosu** | Kademe-3 |
| kod | `error-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `error-kod-ornekleri.md` | 13 dilde retry/circuit breaker örneği | Kademe-3 |
| kanit | `error-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Problem Details — RFC 9457 (RFC 7807'nin yerlisi)

### §1.1 Tarihçe ve statü

| Belge | Statü | Kanıt |
|---|---|---|
| RFC 7807 (2016) | Problem Details for HTTP APIs — ilk tanım | [S2] |
| RFC 9457 (2023) | **RFC 7807'yi obsoletes eder** ("Obsoletes: 7807" başlık satırı 2026-10-16'da çekildi) · `application/problem+json` medya tipi · IANA HTTP Problem Types registry · Appendix A JSON Schema | [S1] |

> **Kural:** Yeni işlerde RFC 9457 esastır; RFC 7807 yalnız tarihsel
> referanstır. `error-anayasa.md` §11.2 yasağı ile aynı.

### §1.2 Zorunlu ve opsiyonel alanlar [S1]

| Alan | Zorunlu | Anlam | Çekirdek mi (RFC 9457 §3) |
|---|---|---|
| `type` | Evet (var sayılır) | Hatanın kimliği — bir URI; çözüm için `about:blank` kullanılır | Evet |
| `title` | Hayır | İnsana-okunur kısa, sabit tanımlama (aynı tip için sabit kalır) | Evet |
| `status` | Hayır | HTTP durum kodunun number hali (`status` = `title`'ın HTTP karşılığı) | Evet |
| `detail` | Hayır | Bu **özel** örneğe dair insana-okunur açıklama (kullanıcıya gösterilebilir; log'a değil) | Evet |
| `instance` | Hayır | Bu örneğin tanımlayıcısı — bir URI (ör. isteğin yolu veya request-id) | Evet |

Ek alanlar eklenebilir (RFC 9457 izin verir); `type` URI'si kayıttır.

### §1.3 Content-Type ve medya tipleri [S1]

| Medya tipi | Kullanım |
|---|---|
| `application/problem+json` | Problem Details gövdesi (JSON) |
| `application/problem+xml` | Problem Details gövdesi (XML) |

### §1.4 IANA registry — HTTP Problem Types (RFC 9457 §4.2'de tanımlı) [S1]

RFC 9457, IANA'da "HTTP Problem Types" registry'sini kurar. Registry'deki
kayıtlı tipler çekildi; bu dosyada sayı/derinlik uydurulmaz. CoreMusic'in
kendi `type` URI'ları için kayıt gerekliliği → ⚠️ UNKNOWN (vault'ta karar yok).

### §1.5 Örnek şekil (RFC 9457 §3 — şekli bozmadan)

```json
{
  "type": "https://example.com/probs/out-of-credit",
  "title": "You do not have enough credit.",
  "status": 403,
  "detail": "Your current balance is 30, but that costs 50.",
  "instance": "/account/12345/msgs/abc"
}
```

(Kaynak: [S1] §3 örnek — birebir aynı kavramsal örnek; burada kavramsal
gösterim için sadeleştirilmiştir.)

---

## §2 Hata Gövdesi Şeması — CoreMusic Sözleşmesi (taslak)

> ⚠️ CoreMusic'e özgü alan adları/URI'lar vault'ta onaylı değildir — bu
> bölüm RFC 9457'ye uyumlu **taslak** bir şekildir; `⚠️ UNKNOWN` işaretli
> alanlar onay bekler.

| Alan | Zorunlu | Kaynak | CoreMusic değeri |
|---|---|---|---|
| `type` | Evet | RFC 9457 | ⚠️ UNKNOWN — URI şeması kararı yok (open_question) |
| `title` | Hayır | RFC 9457 | Sabit metin (dile göre) — çeviri politikası K009'a bağlı |
| `status` | Hayır | RFC 9457 | HTTP status ile eşleşir |
| `detail` | Hayır | RFC 9457 | Kullanıcıya gösterilebilir; **K009 filtresinden** geçer |
| `instance` | Hayır | RFC 9457 | ⚠️ UNKNOWN — request-id mi path mi kararı yok |
| `error_code` (uzantı) | — | RFC 9457 uzantı izni | ⚠️ UNKNOWN — iç kod alanı adı kararı yok |

### §2.1 Yasaklar (gövde)

1. `detail` içinde stack trace, SQL sorgusu, token, kripto anahtarı →
   **YASAK** (K009; `error-anayasa.md` §10).
2. 429 yanıtında `Retry-After` header'ı gövdeye **kopyalanmaz** — header'da
   kalır (RFC 6585 / RFC 9110 §10.2.3) [S3] [S4].
3. Aynı `type` için `title` sabittir (RFC 9457 §3: "constant, human-readable
   short summary") [S1].
4. Gövde `Content-Type: application/problem+json` ile döner [S1].

---

## §3 Dış API → İç Hata Eşleme — Anti-Corruption Layer

### §3.1 Eşleme prensibi

Dış API'nin hata dili (kod, JSON yapısı, header) **iç domain diliyle**
karışmaz. Her dış sağlayıcı için tek bir eşleme tablosu vardır; dış kod
**asla** domain kodu olarak dışarı sızdırılmaz (dış kod log'a + `instance`
detayına girebilir, müşteriye değil).

### §3.2 HTTP-status tabanlı genel eşleme (tüm dış API'ler) [S3] [S5] [S8] [S9] [S11] [S12]

| Dış durum | İç sınıf | İç retry? | Not |
|---|---|---|---|
| 408 Request Timeout | geçici | Evet | Google retry listesinde [S9]; RFC 9110 §15.5.9 [S3] |
| 429 Too Many Requests | geçici (rate-limited) | Evet — Retry-After önce | RFC 6585 §4 [S4] |
| 500 Internal Server Error | geçici* | Evet (Google retry listesinde) | *Bazı 500'ler kalıcıdır; içeriden gelen "unhandled" ise ince hata sayılır |
| 502 Bad Gateway | geçici | Evet | [S9] |
| 503 Service Unavailable | geçici | Evet — Retry-After varsa izle | RFC 9110 §15.6.4 [S3] |
| 504 Gateway Timeout | geçici | Evet | [S9] |
| 400 Bad Request | kalıcı | Hayır | RFC 9110 §15.5.1 [S3] |
| 401 Unauthorized | kalıcı | Hayır (token yenileme ayrı akış) | RFC 9110 §15.5.2 [S3] |
| 403 Forbidden | kalıcı | Hayır | RFC 9110 §15.5.4 [S3] |
| 404 Not Found | kalıcı | Hayır | RFC 9110 §15.5.5 [S3] |
| 405 Method Not Allowed | kalıcı | Hayır | RFC 9110 §15.5.6 [S3] |
| 409 Conflict | kalıcı (ince hata adayı) | Hayır — yeniden oku + karar ver | RFC 9110 §15.5.10 [S3] |
| 412 Precondition Failed | kalıcı (ince hata adayı) | Hayır — çakışma çözümü | RFC 9110 §15.5.13 [S3] |
| 422 Unprocessable Content | kalıcı | Hayır | RFC 9110 §15.5.21 [S3] |

> `error-anayasa.md` §2.2'deki kaynak ekseninin sözleşmeye çevrilmiş hâli.

### §3.3 Bağlantı-seviyesi eşleme (HTTP dışı) [S9] [S5]

| Dış durum | İç sınıf | Retry? |
|---|---|---|
| Bağlantı reddedildi (connection refused) | geçici | Evet (Go kütüphanesi retry listesinde [S9]) |
| Bağlantı sıfırlandı (connection reset by peer) | geçici | Evet [S9] |
| `ErrUnexpectedEOF` / beklenmeyen kapanma | geçici | Evet [S9] |
| DNS çözümü başarısız | kalıcı* | *DNS geçici olabilir; politika ⚠️ UNKNOWN |
| TLS sertifika hatası | kalıcı | Hayır (güvenlik — K009) |

### §3.4 Sağlayıcı-bazlı zengin eşleme (örnekler — kaynaklı)

| Sağlayıcı | Dış kod | İç karşılığı | Kanıt |
|---|---|---|---|
| AWS SDK | 400 + `ThrottlingException`/`Throttling`/`TooManyRequestsException`/`RequestThrottled`/`RequestThrottledException` | rate-limited (geçici) | [S5] |
| AWS SDK | 429 / 503 (+ `x-amz-retry-after` varsa) | rate-limited; header kadar bekle | [S5] |
| GitHub | 403 + `x-ratelimit-remaining: 0` | rate-limited (primary limit!) — `x-ratelimit-reset`'e kadar bekle | [S10] |
| GitHub | 429 + secondary limit mesajı | rate-limited; `retry-after` varsa izle, yoksa ≥60 sn | [S10] |
| Stripe | 429 + `Stripe-Rate-Limited-Reason: global-rate` | rate-limited (global) | [S12] |
| Stripe | 429 + `Stripe-Rate-Limited-Reason: endpoint-rate` | rate-limited (endpoint) | [S12] |
| Stripe | 429 + `Stripe-Rate-Limited-Reason: global-concurrency` / `endpoint-concurrency` | concurrency limit; eşzamanlılık düşür | [S12] |
| Stripe | 429 + kod `lock_timeout` (header YOK) | kilit zaman aşımı — rate limit DEĞİL; retry edilebilir | [S12] |
| Microsoft Graph | 429 + `Retry-After: 10` (örnek) | rate-limited; header esas | [S11] |
| Microsoft Graph | Batch içinde tek istek 429 | batch 200 döner; başarısız istekleri JSON içi `retry-after` ile tekrar dene | [S11] |
| Google Cloud Storage | 408/429/500/502/503/504 | retry'lenebilir | [S9] |
| Google Cloud Storage | İdempotent-koşullu operasyonlar | `ifGenerationMatch`/`ifMetagenerationMatch`/`etag` varsa koşullu-güvenli retry | [S9] |

### §3.5 Circuit breaker sinyali eşleme [S8]

| Dış davranış | İç karşılığı |
|---|---|
| 429 tekrarlayan | Circuit breaker **ivmeleme** ile trip edebilir (Azure Cosmos örneği: ilk 429 → Open → cache'li yanıt) [S8] |
| Circuit breaker Open durumu | İçe dönen hata: "downstream_unavailable" sınıfı; retry **durur** (Azure: retry circuit breaker hatalarına duyarlı olmalı [S7]) |
| Half-Open deneme başarısı | Closed'a dönüş + sayaç sıfırlama; iç hata kapatılır |

---

## §4 Sınıf Sözleşmesi — İç Hata Nesnesi (taslak)

> ⚠️ CoreMusic sınıf adı/alanları vault'ta onaylı değildir; bu tablo
> `error-anayasa.md` §2 sınıflarını sözleşmeye çeviren **taslak**tır.

| Alan | Tip | Açıklama |
|---|---|---|
| `class` | enum | `transient` / `permanent` / `subtle` (anayasa §2.1) |
| `code` | string | İç domain kodu (dış kod DEĞİL) — ⚠️ UNKNOWN (kod ad alanı kararı yok) |
| `http_status` | int | Dışarı dönerken RFC 9457 `status` olur |
| `retryable` | bool | Mekanizma seçimine girmeden sözleşme beyanı |
| `retry_after` | int/sn veya null | 429/503'te sunucu değeri korunur (header) |
| `cause` | string/null | İç log için kök neden (dışarı çıkmaz) |
| `provider` | string/null | Dış sağlayıcı adı (log/ gözlem için) |

### §4.1 Sınıf → dışarı yansıma

| İç sınıf | Dışarı döner mi? | Nasıl? |
|---|---|---|
| transient | Evet | RFC 9457 gövdesi + uygun status (502/503/429) |
| permanent | Evet | RFC 9457 gövdesi + orijinal status (400/401/403/404/422) |
| subtle | Evet | 500/409; `detail` "unknown state" dili (RFC 9457 `about:blank` tipi kullanılabilir) |
| Circuit breaker Open | Evet | 503; `Retry-After` header'ı ⚠️ UNKNOWN (CoreMusic open-duration kararı yok) |

---

## §5 Header Sözleşmesi

| Header | Yön | Kural | Kanıt |
|---|---|---|---|
| `Content-Type: application/problem+json` | giden | Hata gövdesi için zorunlu | [S1] |
| `Retry-After` | giden (429/503) | Sunucu kuralı; HTTP-date veya delay-seconds | [S3 §10.2.3] [S4] |
| `Retry-After` | gelen (dış API) | Değer **korunur**, iç backoff'a ezilmez | [S11] [S10] [S12] |
| `x-ratelimit-*` (GitHub) | gelen | Otorite; `reset`'e kadar bekleme | [S10] |
| `x-amz-retry-after` (AWS) | gelen | Varsa o kadar bekleme | [S5] |
| `Stripe-Rate-Limited-Reason` | gelen | 429 nedenini sınıflandırır | [S12] |

> **Gözleme aktarımı:** Bu header'ların/metriklerin gözlem katmanına
> iletilmesi K017 sınırındadır (`error-anayasa.md` §9).

### §5.1 Header Ayrıştırma Kuralları (sözleşme)

| Header | Beklenen biçim | Ayrıştırma kuralı | Bozuk/eksik durum |
|---|---|---|---|
| `Retry-After` (gelen) | delay-seconds (tam sayı) veya HTTP-date | Tam sayı → saniye; HTTP-date → `date - now` | Bozuk değer → jitter'lı backoff'a düş ([S1] diline göre) |
| `Retry-After` (giden) | delay-seconds | Yalnız tam sayı saniye gönderilir | Date biçiminde gönderim [S1] izinli ama CoreMusic tercihi → ⚠️ UNKNOWN |
| `x-ratelimit-reset` (GitHub, gelen) | UTC epoch seconds | `reset - now` kadar bekleme; **negatifse 0** | Header yoksa rate-limit sınıfı uygulanmaz |
| `x-ratelimit-remaining` (GitHub, gelen) | tam sayı | `0` ise primary limit → reset'e kadar istek YOK | Header yoksa normal jitter akışına devam |
| `Stripe-Rate-Limited-Reason` (gelen) | 5 geçerli değer | §3.4 tablosuna göre sınıf | Başka değer → generic rate-limited |
| `x-amz-retry-after` (AWS, gelen) | milisaniye (SDK dahili) | SDK yönetir; el-kodunda okuma ⚠️ UNKNOWN | — |
| `Content-Type` (giden hata) | `application/problem+json` | Charset opsiyonel (`; charset=utf-8`) | Farklı tip → D-8 kapısı kırmızı |

> **Sınır:** Header **değerleri** saklanmaz (KVKK/veri minimizasyonu — K009
> sınırı); yalnız karar için gereken sayısal/enum değeri işlenir.

---

## §6 Gövde Örnekleri (RFC 9457 Şekline Sadık)

### §6.1 Kalıcı hata — 404 (müşteriye dönülebilir)

```json
{
  "type": "about:blank",
  "title": "Resource not found.",
  "status": 404,
  "detail": "The requested album does not exist.",
  "instance": "/api/v1/albums/42"
}
```

> `type: about:blank` — RFC 9457 §3: tip URI'si yoksa `about:blank`
> kullanılır; `title` bu durumda zorunlu-role girer [S1]. `detail`
> spesifik ama güvenli (K009 filtresinden geçer).

### §6.2 Rate-limit — 429 (Retry-After HEADER'da)

```http
HTTP/1.1 429 Too Many Requests
Content-Type: application/problem+json
Retry-After: 10

{
  "type": "about:blank",
  "title": "Too many requests.",
  "status": 429,
  "detail": "Rate limit exceeded. Retry after the indicated delay."
}
```

> `Retry-After` gövdede **tekrarlanmaz** (§2.1.2); Graph örneğindeki
> `Retry-After: 10` header'ı [S11] birebir bu kalıptır.

### §6.3 İnce hata — 409/500 (durum belirsiz)

```json
{
  "type": "about:blank",
  "title": "Operation state unknown.",
  "status": 500,
  "detail": "The operation may have completed. Verify before retrying."
}
```

> İnce hata (subtle) sözleşmesi: istemciye "emin olunamadı" söylenir;
> yeniden göndermeden **önce doğrulama** istenir (error-anayasa §3.3).

### §6.4 Dış sağlayıcı hatasının iç gövdeye çevirisi (anti-corruption)

```json
// DIŞ (varsayısal bir sağlayıcı gövdesi — gerçekçi ama kurgusal):
{ "error": { "code": "ThrottlingException", "message": "...", "requestId": "abc-123" } }

// İÇ (müşteriye dönen — dış kod sızdırılmaz):
{
  "type": "about:blank",
  "title": "Service busy.",
  "status": 429,
  "detail": "The service is processing a high volume of requests.",
  "instance": "/api/v1/sync"
}
```

> Dış `requestId` yalnız **iç log'a** (`cause`/trace) gider; gövdeye değil
> (error-anayasa §3.1 / §10). AWS throttling kodlarının listesi [S5].

---

## §6 DLQ Sözleşmesi (kavramsal) [S12]

| Alan | Kural |
|---|---|
| Taşıma tetikleyici | `maxReceiveCount` aşıldı (SQS redrive policy) |
| Mesaj zenginleştirme | DLQ'ya giden mesaj orijinal + son hata + deneme sayısı içerir |
| Redrive | DLQ'dan kaynak kuyruğa redrive yapılır (SQS: dead-letter queue redrive) |
| FIFO notu | FIFO'da enqueue timestamp sıfırlanır; retention hesabı buna göre |
| CoreMusic uygulaması | ⚠️ UNKNOWN (kuyruk altyapısı kararı yok) |

---

## §7 Eşleme Tablosu Bakım Kuralları

1. Tablo **tek SSOT**'dur; servisler kendi eşlemesini yazamaz (duplikasyon
   yasak — anti-corruption layer tek noktada).
2. Yeni dış sağlayıcı = yeni satır + kaynak fetch (zero-hallucination:
   sağlayıcı kodu uydurulmaz).
3. Sağlayıcı davranışı değişirse (doküman) tablo güncellenir + tarih
   eklenir.
4. Tablo `error-web-arastirma.md` §4'teki iddia→kaynak eşlemesiyle
   hizalanır.

### §7.1 Sürümleme Kuralı

| Değişiklik tipi | Aksiyon | Örnek |
|---|---|---|
| Yeni dış sağlayıcı ekleme | Satır ekle + fetch kanıtı | Yeni API entegrasyonu |
| Sağlayıcı kod adı değişikliği | Satır güncelle + yeni fetch tarihi | Sağlayıcı sürüm atlaması |
| RFC revizyonu (ör. 9457 → yeni) | §1 tablosunu güncelle; obsoletes işaretini taşı | Beklenen değil — tetiklenmez |
| İç eşdeğerlik değişikliği | Sınıf/alan sözleşmesi değişikliği = kırıcı (breaking) — sürüm notu zorunlu | `retryable` alanının anlamı |

### §7.2 Uyumluluk (Compatibility) Kuralı

RFC 9457 alanları **genişletilebilir** (ek alan serbest [S1]); mevcut
alanların **anlamı değiştirilemez**. İç sözleşme (§4 taslak) değişirse:
eski istemcilerin `retryable`/`class` alanlarına bağımlılığı gözden
geçirilir; `status` alanı her zaman HTTP status ile eşleşir (kırılmaz).

---

## §8 Yasaklar (bu dosya)

1. RFC 7807 yeni işlerde kullanılmaz — RFC 9457 esastır (§1.1).
2. Dış sağlayıcı kodu müşteriye sızdırılmaz (§3.1).
3. `detail`'e hassas veri konmaz (K009; §2.1).
4. `Retry-After` gövdeye taşınmaz (§2.1.2).
5. Eşleme tablosu dışı kod **uydurulmaz** (zero-hallucination — §7.2).
6. Gözleme aksiyonu (alarm eşikleri) bu klasörde tanımlanmaz (K017; §5).

---

## §8 Sınır Kartı — Bu Dosyanın Kapsamı Dışı

| Konu | Sahip | Not |
|---|---|---|
| Gözleme aksiyonu (alarm eşikleri, dashboard) | K017 → ⚠️ | Bu dosya yalnız aktarılacak sinyali etiketler (§5) |
| Politika/kripto (gizlilik, şifreleme) | K009 → ⚠️ | `detail` filtresi bu kararlara tabidir (§2.1) |
| Circuit breaker eşik değerleri | error-anayasa §5.3 (UNKNOWN) | Burada tekrarlanmaz |
| Retry parametreleri | error-anayasa §4.2 (kaynaklı/UNKNOWN) | Burada tekrarlanmaz |
| Uygulama kodu (middleware/servis) | error-kod-ornekleri (şablon) | Sözleşme ≠ uygulama |
| Kuyruk altyapısı | ⚠️ UNKNOWN | §6 yalnız kavramsal |

---

## §Kaynak Tablosu

| # | Kaynak | URL | Erişim | Durum |
|---|---|---|---|---|
| S1 | RFC 9457 Problem Details (Obsoletes: 7807; medya tipleri; IANA registry; Appendix A JSON Schema) | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✅ çekildi |
| S2 | RFC 7807 (ilk sürüm — tarihsel) | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-16 | ✅ çekildi |
| S3 | RFC 9110 (status kodları §15, Retry-After §10.2.3, idempotent §9.2.2) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S4 | RFC 6585 §4 (429 + Retry-After MAY) | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ çekildi |
| S5 | AWS SDK retry behavior (throttling kodları; x-amz-retry-after) | https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html | 2026-10-16 | ✅ çekildi |
| S6 | Azure Retry pattern (retry + circuit breaker etkileşimi) | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✅ çekildi |
| S7 | Azure Circuit Breaker pattern (429 ivmeleme; durum geçişleri) | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ çekildi |
| S8 | GitHub REST rate limits (403+remaining:0; secondary; ban) | https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api | 2026-10-16 | ✅ çekildi |
| S9 | Google Cloud Storage retry strategy (retry'lenebilir kodlar; idempotent-koşullu) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi |
| S10 | Stripe rate limits (Stripe-Rate-Limited-Reason; lock_timeout) | https://docs.stripe.com/rate-limits | 2026-10-16 | ✅ çekildi |
| S11 | Microsoft Graph throttling (Retry-After örneği; batch) | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ çekildi |
| S12 | Amazon SQS dead-letter queues (redrive; retention; FIFO) | https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html | 2026-10-16 | ✅ çekildi |

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | CoreMusic `type` URI şeması → ⚠️ UNKNOWN | FM open_questions |
| 2 | CoreMusic `instance` kullanımı → ⚠️ UNKNOWN | FM open_questions |
| 3 | Eşleme uygulama katmanı (middleware vs service) → ⚠️ UNKNOWN | FM open_questions |
| 4 | İç hata sınıfı adları/alanları taslak — onay bekliyor | §4 |
| 5 | DLQ kuyruk altyapısı → ⚠️ UNKNOWN | §6 |
| 6 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
