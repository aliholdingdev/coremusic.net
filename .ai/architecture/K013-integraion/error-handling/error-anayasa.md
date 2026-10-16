---
node_id: K013-integraion/error-handling/error-anayasa
node_type: technology-deepdive
title: "Error Anayasa — Hata Sınıflandırma · Retry/Backoff/Jitter · Circuit Breaker · Rate Limit · Timeout Bütçesi · Hata Eşleme · Problem Details · Dead-Letter"
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
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc7807.html (2026-10-16)"
  - "https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html (2026-10-16)"
  - "https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/retry (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker (2026-10-16)"
  - "https://cloud.google.com/storage/docs/retry-strategy (2026-10-16)"
  - "https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/throttling (2026-10-16)"
  - "https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html (2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda [S#] ile URL eşlemesine sahiptir (2026-10-16 fetch)"
  - "Retry/rate-limit PARAMETRE DEĞERLERİ yalnız kaynaklıdır; kaynaksız değer ⚠️ UNKNOWN olarak işaretlidir"
  - "RFC 9457 → RFC 7807 obsoletes kanıtı 'Obsoletes: 7807' satırıyla doğrulandı (2026-10-16)"
  - "Gözleme aksiyonu sahipliği (K017) ve politika/kripto (K009) sınırı etiketlenmiştir — karar VERİLMEZ"
open_questions:
  - "CoreMusic'e özgü retry max-attempts / base-delay / cap değerleri → ⚠️ UNKNOWN (politika kararı yok)"
  - "CoreMusic circuit breaker eşikleri (failure threshold / open-duration) → ⚠️ UNKNOWN"
  - "Gözleme (metrics/alert) aksiyonu sahipliği K017 mi? → ⚠️ VERIFICATION REQUIRED — bu dosya yalnız aktarım sözleşmesini etiketler"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: mimari
---

# Error Anayasa — Hata Sınıflandırma · Retry/Backoff/Jitter · Circuit Breaker · Rate Limit · Timeout Bütçesi · Hata Eşleme · Problem Details · Dead-Letter

> **Bu dosya ne değildir:** Kod, kütüphane seçimi veya satır-içi uygulama
> rehberi değildir. Bu dosya **mimari karar yüzeyini** tanımlar: hataların
> hangi eksenlerde sınıflandırılacağı, hangi mekanizmanın hangi hata sınıfına
> uygulanacağı ve CoreMusic sınırı. Uygulama örnekleri
> `error-kod-ornekleri.md`'dedir; doğrulama kapıları `error-dogrulama.md`'dedir;
> kaynak kanıtı `error-web-arastirma.md`'dedir.
>
> **Durum etiketi:** KAVRAM/şartname IMPLEMENTED (**13 resmi kaynak fetch**
> ile doğrulandı — §Kaynak Tablosu) · CoreMusic kodu ⚠️ PLANNED (bu turda
> hata-yönetim implementasyonu depoda tespit edilmedi →
> `⚠️ VERIFICATION REQUIRED`).

---

## §0 ELI15 — Hata yönetimi bir cümlede

Hata önce **sınıflandırılır** (geçici mi, kalıcı mı, ince mi), sonra
sınıfa göre **mekanizma** seçilir: geçici ise retry (üstel backoff + jitter),
tekrarlayan geçici hata circuit breaker ile kesilir, hız sınırı
(Rate Limit/429) sunucunun verdiği `Retry-After`'ı izler, her denemenin
**timeout bütçesi** vardır, dış API hatası **iç hata koduna eşlenir** ve
müşteriye Problem Details (RFC 9457) ile döner, umutsuz mesajlar
**dead-letter kuyruğuna** gider, her şey **gözlem katmanına** aktarılır
(aksiyon sahibi K017 → etiketli).
(Kaynak: §Kaynak Tablosu [S1]–[S13])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| **mimari** | **`error-anayasa.md`** | **Sınıflandırma eksenleri, mekanizma seçimi, sınır** | Kademe-3 |
| mimari | `error-sozlesme.md` | Hata sözleşmesi, Problem Details, eşleme tablosu | Kademe-3 |
| kod | `error-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `error-kod-ornekleri.md` | 13 dilde retry/circuit breaker örneği | Kademe-3 |
| kanit | `error-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Kavram Sözlüğü — Kanıtlanmış Terimler

| Terim | Anlam | Kanıt |
|---|---|---|
| Transient fault (geçici hata) | Kısa sürede kendiliğinden düzelen hata (ağ kesintisi, timeout, throttling); retry adayı | [S7] [S8] |
| Permanent fault (kalıcı hata) | Tekrar denemekle düzelmez (400/401/403/404/422); retry YASAK, fail-fast | [S1] [S7] |
| Subtle fault (ince hata) | Dışarıdan başarı gibi görünen ama sonucu bozan hata (timeout sonrası success varsayımı, kısmi yazma, yanlış veri); yalnız idempotency + doğrulama ile yakalanır | ⚠️ TERMINOLOJI — endüstri literatüründe tek standart ad yok; bu dosyada "ince hata" adı verilmiştir (§3.3) |
| Idempotent method | Aynı isteğin tekrarının aynı etkiyi yaptığı method (GET/HEAD/PUT/DELETE); retry güvenlidir | [S1 §9.2.2] |
| Non-idempotent method | POST gibi; otomatik retry istemci tarafında önerilmez (RFC 9110: "A client SHOULD NOT automatically retry a request with a non-idempotent method") | [S1 §9.2.2] |
| Exponential backoff | Her denemede bekleme süresinin sabit çarpanla üssünün artırılması (cap ile sınırlı) | [S5] [S6] |
| Full jitter | `sleep = random(0, 1) × min(cap, base × 2^attempt)` — AWS SDK standard retry jitter'i | [S5] [S6] |
| Equal jitter | Backoff'un bir kısmının her zaman tutulduğu jitter çeşidi (`sleep = backoff/2 + random(0, backoff/2)`) | [S6] |
| Decorrelated jitter | Önceki rastgele değere göre üst sınırı büyüten jitter çeşidi | [S6] |
| Retry quota | AWS SDK'da istemciye verilen kredi bütçesi: başarılı istekler kredi ekler, hatalılar düşer (default 500; transient 14, throttling 5 kredi) | [S5] |
| Circuit breaker (Closed/Open/Half-Open) | Başarısızlık eşiğine ulaşan çağrıyı geçici olarak kesen 3 durumlu proxy; kapanan çağrılar hemen hata döner | [S8] |
| Retry-After | Sunucunun önerdiği bekleme süresi; HTTP-date veya delay-seconds (RFC 9110 §10.2.3); 503'te MAY | [S1 §10.2.3] [S2] |
| 429 Too Many Requests | Hız sınırı aşıldığında dönen kod; RFC 6585'de tanımlandı; Retry-After MAY; cache'lenemez | [S2] |
| Problem Details | Hata gövdesi standardı; RFC 7807 → RFC 9457 ile obsolete; `application/problem+json`; type/title/status/detail/instance | [S3] [S4] |
| Dead-letter queue (DLQ) | İşlenemeyen mesajların izole edildiği kuyruk; redrive policy `maxReceiveCount` ile DLQ'ya taşıma | [S13] |
| Idempotency key | Aynı isteğin tekrarını aynı sonuçla karşılayan istemci anahtarı; ince hata ve non-idempotent retry'ın sigortası | [S12] |
| Retry-on / Retry-off sınıfı | HTTP durum kodunun retry edilip edilmeyeceğinin sınıflandırılması (408/429/5xx → retry; 4xx çoğu → retry-off) | [S7] |
| Timeout budget | Zincir boyunca paylaşılan toplam bekleme süresi; her katman kendi timeout'unu bütçeden harcar | ⚠️ VERIFICATION REQUIRED — "timeout budget" terimi bu turda resmi bir kaynaktan çekilmedi; kavram Azure retry "nested retry" uyarısından [S7] türetilmiştir (§6) |

---

## §2 Hata Sınıflandırma Eksenleri — 3 Eksenli Model

### §2.1 Eksen 1 — Yaşam Süreci (transient / permanent / subtle)

| Sınıf | Tanım | Mekanizma | Kanıt |
|---|---|---|---|
| **Geçici (transient)** | Tekrarlayan deneme muhtemelen başarır; kısa sürede düzeller | Retry + exponential backoff + jitter | [S7] [S8] |
| **Kalıcı (permanent)** | Tekrar denemek asla成功 getirmez; istemci hatası veya kaynak yokluğu | Fail-fast; kullanıcıya/çağıraya hata döndür; retry YASAK | [S1] [S7] |
| **İnce (subtle)** | Çağrı "başarılı" döner ama sonuç geçersizdir (timeout sonrası emin olunamayan durum, kısmi yazma, dönüşüm kaybı) | Idempotency key + sonucu doğrula + ince hata denetimi | [S7] (idempotency bölümü) [S12] |

> ⚠️ "İnce hata" adı bu dosyanın takma adıdır; RFC/standart terim değildir —
> §1'deki uyarıya bak. Kavramsal dayanak: RFC 9110 §9.2.2'nin non-idempotent
> retry uyarısı + Azure retry deseninin idempotency bölümü [S7].

### §2.2 Eksen 2 — Kaynak (hangi katmandan geldi)

| Kaynak | Tipik hatalar | Not |
|---|---|---|
| Ağ/taşıma | Bağlantı kesildi, reset, DNS | Transient; Google Go istemcisi `connection refused`/`reset by peer`/`ErrUnexpectedEOF`'u retry eder [S7-GCP] |
| Sunucu (5xx) | 500/502/503/504 | 503 + Retry-After sunucu kuralıdır [S1 §15.6.4]; Google 500/502/503/504'ü retry eder [S7-GCP] |
| Hız sınırı | 429 (+ Retry-After) | Sunucu kuralına uy [S2] [S10] [S12] [S13] |
| zaman aşımı | 408 Request Timeout | Google retry listesinde [S7-GCP]; RFC 9110 §15.5.9'da tanımlı [S1] |
| İstemci (4xx) | 400/401/403/404/405/409/412/422 | Kalıcı; retry-off [S1] |
| Kilit/time-out (Stripe özel) | 429 + `lock_timeout` kodu | Rate limit'ten farklı neden; SDK retry eder, istemci de edebilir [S12] |

### §2.3 Eksen 3 — Etki (çağrı zinciri üzerindeki sonuç)

| Etki | Sonuç | Karşı önlem |
|---|---|---|
| Artan yük | Retry storm / thundering herd; aynı anda N istemci aynı anda dener | Jitter şart [S6] [S12] |
| Kaynak tükenmesi | Timeout bekleyen thread/bağlantı birikmesi | Kısa timeout + circuit breaker [S8] |
| Kademeli çöküş (cascading failure) | Bir bağımlılığın çökmesi tüm zinciri götürür | Circuit breaker [S8] |
| Yanlış etki | Non-idempotent retry → çift yazma | Idempotency key [S1] [S7] [S12] |

---

## §3 Mekanizma Seçim Matrisi — Hangi Sınıfa Hangi Araç

### §3.1 Matris

| Hata sınıfı | Retry | Circuit breaker | Rate-limit kuralı | DLQ | Problem Details |
|---|---|---|---|---|---|
| Transient (ağ/5xx) | ✅ backoff+jitter | İkinci aşamada | — | — | Müşteriye 502/503 eşlemesi |
| 429 / throttling | ✅ Retry-After önce | ✅ (throttling'i trip edebilir — Azure Cosmos örneği) | ✅ Retry-After izlenir; yoksa exponential bekleme | — | 429 eşlemesi |
| Permanent (4xx) | ❌ | — | — | — | Aynı kod döner (eşleme §7) |
| İnce (subtle) | Koşullu: idempotency key varsa | — | — | Doğrulama başarısız mesaj → DLQ | 500/409 eşlemesi |
| Umutsuz (DLQ'ya giden) | ❌ (redrive hariç) | — | — | ✅ | ⚠️ UNKNOWN — CoreMusic DLQ gövde şeması kararı yok |

### §3.2 Retry karar akışı

```text
Hata alındı
  → Sınıflandır (geçici / kalıcı / ince)
    → Kalıcı → hata döndür (retry YASAK) [S1 §9.2.2]
    → İnce → idempotency key var mı?
        → Hayır → hata döndür (retry YASAK — çift etki riski)
        → Evet → retry (key korunarak) + sonucu doğrula
    → Geçici → sunucu Retry-After verdi mi?
        → Evet → Retry-After kadar bekle, yeniden dene [S1 §10.2.3] [S13]
        → Hayır → jitter'lı exponential backoff ile dene
          → max_attempts doldu → son hatayı yükselt (throw)
          → (Azure: "prevent further requests going to the same resource
             and report a failure immediately" → circuit breaker'a devret) [S7]
```

### §3.3 İnce hata (subtle) — neden ayrı sınıf

RFC 9110 §9.2.2: non-idempotent istekler otomatik retry edilmemelidir; ama
idempotent method ile retry de "istek işlendi ama yanıt kayboldu" durumunda
sonucu doğrulamayı gerektirir. Azure retry deseni aynı noktayı vurgular:
"hizmet isteği alıp başarıyla işleyip yanıtı gönderemeyebilir — retry
isteği tekrar gönderir" [S7]. İnce hata sınıfı bu boşluğu kapatır: retry
mekanizması idempotency key ile birlikte çalışır, retry sonunda **sonuç
doğrulanır** (Stripe 429/lock_timeout örneğindeki "intermittent ise retry"
tavsiyesi [S12]).

---

## §4 Exponential Backoff + Jitter — Matematik ve Kaynaklı Değerler

### §4.1 Formül

| Varyant | Formül | Kaynak |
|---|---|---|
| Full jitter | `sleep = random(0, 1) × min(cap, base × 2^retry)` | [S5] (AWS SDK dokümanı: "delay = random(0,1) × min(20000ms, base×2^retry)") |
| Equal jitter | `sleep = backoff/2 + random(0, backoff/2)` | [S6] (AWS blog: "keep some of the backoff and jitter by a smaller amount") |
| Decorrelated jitter | `sleep = min(cap, random(base, previous × 3))` | [S6] (blog tarifi; tam formül blogda sembolsel — uygulamada dikkat) |
| Truncated binary exponential (gcloud) | Üstel bekleme, cap ile kesilir | [S7-GCP] ("truncated binary exponential backoff") |

### §4.2 Kaynaklı parametre tablosu — UYDURULMAZ

| Parametre | Değer | Kaynak |
|---|---|---|
| AWS SDK standard retry — max attempts (default) | **3** | [S5] |
| AWS SDK standard retry — transient base delay | **50 ms** | [S5] |
| AWS SDK standard retry — throttling base delay | **1000 ms** | [S5] |
| AWS SDK retry — delay cap | **20000 ms** | [S5] |
| AWS SDK retry — jitter tipi | **full jitter** | [S5] |
| AWS SDK retry — retry quota (default) | **500 token**; transient başarısızlık **14**, throttling **5** token harcar | [S5] |
| AWS SDK — throttling algılama | HTTP 400 + `ThrottlingException`/`Throttling`/`TooManyRequestsException`/`RequestThrottled`/`RequestThrottledException` + 429 + 503 | [S5] |
| AWS SDK — `x-amz-retry-after` header'ı | Varsa istemci o kadar bekler | [S5] |
| Google Cloud Storage C++ — initial wait | **1 saniye** | [S7-GCP] |
| Google Cloud Storage C++ — multiplier | **2** | [S7-GCP] |
| Google Cloud Storage C++ — max wait | **5 dakika** | [S7-GCP] |
| Google Cloud Storage C++ — max retry süresi | **15 dakika** | [S7-GCP] |
| Google Cloud Storage Go — initial delay / multiplier / max delay | **1 sn / 2.0 / 30 sn** (gax defaultları) | [S7-GCP] |
| gcloud CLI — max retry | **32** | [S7-GCP] |
| Google Cloud Storage retry'lenebilir HTTP kodları | **408, 429, 500, 502, 503, 504** + bağlantı hataları | [S7-GCP] |
| Stripe SDK — max network retries örneği | `Stripe.max_network_retries = 2` (Ruby örneği) | [S12] |
| CoreMusic retry max-attempts | **⚠️ UNKNOWN** (politika kararı yok) | — |
| CoreMusic base delay / cap | **⚠️ UNKNOWN** | — |

> **Yasak:** Yukarıdaki değerler CoreMusic'e kopyalanmaz; her değer kaynağıyla
> birlikte taşınır. CoreMusic'e özgü değerler onaylı bir ADR ile eklenir.

### §4.3 Jitter neden şart — kanıt

AWS Architecture Blog (Marc Brooker, 2015): jitter'sız exponential backoff'ta
istemciler hâlâ **kümelere** biner; 100 rakipli simülasyonda Full Jitter
çağrı sayısını **yarıdan fazla** azaltır; jitter'sız backoff en kötü
sonuçtur (hem daha çok iş hem daha çok süre) [S6]. GitHub rate limit
rehberi de "wait for at least one minute ... continue with an exponentially
increasing amount of time" ile aynı yönde tavsiye verir [S10]. Stripe:
"add randomness to the backoff schedule to avoid a thundering herd effect"
[S12].

---

## §5 Circuit Breaker — Durum Makinesi (Azure tanımı)

### §5.1 Durumlar [S8]

| Durum | Davranış | Geçiş |
|---|---|---|
| **Closed** | İstekler geçirilir; başarısızlık sayacı tutulur (zaman bazlı, periyodik sıfırlanır) | Sonuç sayısı eşik+aşım → Open; zamanlayıcı başlatılır |
| **Open** | İstekler **hemen** hata döner; işlem çağrılmaz | Zamanlayıcı doldu → Half-Open |
| **Half-Open** | **Sınırlı** sayıda deneme isteği geçirilir | Hepsi başarılı → Closed (sayaç sıfırlanır); herhangi biri başarısız → Open (zamanlayıcı yeniden başlar) |

### §5.2 Uygulama ilkeleri (kaynaklı) [S8]

- **Retry ile ayrım:** Retry "başarır umuduyla" tekrarlar; circuit breaker
  "başaramaz" der ve keser. İkisi birlikte kullanılabilir ama retry,
  circuit breaker'ın döndürdüğü hataya duyarlı olmalıdır.
- **Exception türüne göre eşik:** Timeout istisnaları için daha yüksek eşik
  gerekebilir (timeout = overload sinyali ≠ service-down).
- **Eşikler geleneksel olarak sabittir;** adaptif (AI/ML tabanlı) eşik
  yaklaşımı Azure sayfasında "adaptive techniques" olarak anılır — bu
  dosyada uygulama varsayılmaz.
- **İvmeleme (accelerated breaking):** Bazı hata yanıtları (örn. 429)
  circuit breaker'ın anında trip edip minimum bekleme uygulamasını
  gerektirebilir.
- **Gözlem:** Durum değişiminde event yayılır; Open'a geçiş alarm tetikler
  (Azure Cosmos DB örneğinde 429 → Open → cache'li yanıtlama → Monitor
  alarmı) [S8]. **Aksiyon sahipliği K017'ye ait olabilir → §9'da etiketli.**

### §5.3 Eşik değerleri

| Parametre | CoreMusic değeri |
|---|---|
| Failure threshold | ⚠️ UNKNOWN |
| Open duration (zamanlayıcı) | ⚠️ UNKNOWN |
| Half-Open deneme sayısı | ⚠️ UNKNOWN |

> Azure dokümanı eşik sayı vermez; sayı uydurulmaz.

### §5.4 Uygulama Yerleşimi Seçenekleri (kaynaklı) [S8]

| Yerleşim | Artı | Eksi |
|---|---|---|
| Uygulama-katmanı (kod içi proxy) | İnce ayar; dil-yerel | Her dilde tekrar; çok-instance'ta tutarsız olabilir |
| Service mesh sidecar (ör. Envoy tarzı) | Kod değişikliği olmadan kesme; tek noktadan politika | Altyapı bağımlılığı; CoreMusic'te mesh var mı → ⚠️ UNKNOWN |
| Kütüphane (resilience kütüphanesi) | Hazır durum makinesi | Bağımlılık artışı (K009/YAGNI — kod-ornekleri §18: kütüphane seçimi karar değil) |

> Azure: "service meshes often support circuit breaking as a sidecar or as a
> standalone capability without modifying application code" [S8]. Hangi
> yerleşimin CoreMusic'te kullanılacağı → ⚠️ UNKNOWN (karar yok).

### §5.5 Yarı-açık (Half-Open) Deneme Akışı — Adım Adım

```text
Open → zamanlayıcı doldu
  → Half-Open: en fazla K deneme sızar (K → UNKNOWN)
    → Deneme-1 başarılı → success counter ++
      → success counter ≥ eşik → Closed (sayaç sıfır)
    → Herhangi bir deneme başarısız → Open'a dön
      → zamanlayıcı YENİDEN başlar (artışır zamanlayıcı opsiyonu: [S8])
```

> Azure notu: Half-Open, kurtulan servisin aniden istek baskısıyla tekrar
> çökmesini engeller — "a flood of work can cause the service to time out
> or fail again" [S8]. Bu yüzden Half-Open deneme sayısı **sınırlıdır**.

---

## §6 Timeout Bütçesi — Kavram (türetmeli)

Azure retry deseni "nested retry" uyarısı verir: bir task'ın retry policy'si
diğer task'ın retry policy'sini çağırırsa **uzun gecikmeler** eklenir;
alt katmanın fail-fast yapıp nedeni yukarı iletmesi önerilir [S7]. Circuit
breaker sayfası da uzun timeout'ların thread'leri blokladığını ve zincirin
geri kalanını beslediğini belirtir [S8].

Bu iki kaynaktan türetilen mimari kural (bu dosyanın sentezi):

```text
Toplam timeout bütçesi (B)
  = Σ (katman timeout'ları) ≤ B
  → Her katman kendi timeout'unu bütçe içinden seçer
  → Retry toplamı da bütçeye dahildir: Σ (deneme timeout + bekleme) ≤ B
  → Bütçe aşımında: iç katman fail-fast + üst katmana neden iletimi [S7]
```

| Bütçe değeri | CoreMusic |
|---|---|
| Toplam dış çağrı bütçesi | ⚠️ UNKNOWN |

> ⚠️ "Timeout budget" bu formülasyonda bu dosyanın sentezidir; tek bir
> resmi kaynaktan alınmış terim değildir (bkz. §1 timeout budget satırı).

---

## §7 Rate Limit / 429 — Sunucu Kuralı Esas

### §7.1 Kaynaklı davranışlar

| Kaynak | Davranış | Kanıt |
|---|---|---|
| RFC 6585 | 429 tanımlandı; sunucu `Retry-After` MAY gönderir; 429 cache'lenemez (§4) | [S2] |
| RFC 9110 §10.2.3 | Retry-After = HTTP-date veya delay-seconds; 3xx ve 503'te kullanılır | [S1] |
| RFC 9110 §15.6.4 | 503'te sunucu Retry-After MAY gönderir | [S1] |
| GitHub | Primary limit aşımında 403/429 + `x-ratelimit-remaining: 0`; `x-ratelimit-reset`'e kadar retry YASAK; secondary limitte `retry-after` varsa onu izle, yoksa **en az 1 dakika** bekle; sonra exponential artan bekleme; ban riski | [S10] |
| GitHub header'ları | `x-ratelimit-limit` / `-remaining` / `-used` / `-reset` / `-resource`; bunlar otoritedir | [S10] |
| Stripe | 429 + `Stripe-Rate-Limited-Reason` header'ı (global-rate / endpoint-rate / global-concurrency / endpoint-concurrency / resource-specific); 429 ama header yoksa rate limit DEĞİL (lock_timeout olabilir) | [S12] |
| Stripe limit değerleri | Live global 100 rps; sandbox 25 rps; endpoint 25 rps; concurrency global 50 (live) | [S12] |
| Microsoft Graph | 429 + `Retry-After` (örnek: 10); "immediate retry" YASAK; `Retry-After` izlemek en hızlı toparlanma; yoksa exponential backoff | [S13] |
| Google | 429 retry'lenebilir kodlar listesinde | [S7-GCP] |

### §7.2 İç eşleme kuralı (özet)

429 → iç hata: "rate_limited" sınıfı; sunucu `Retry-After` varsa **o değer
esas**; yoksa jitter'lı exponential backoff. Ayrıntı: `error-sozlesme.md` §5.

---

## §8 Dead-Letter — Umutsuz İşin İzolasyonu

### §8.1 Kaynaklı kavramlar (Amazon SQS) [S13]

| Kavram | Anlam |
|---|---|
| Redrive policy | `maxReceiveCount` — mesaj kaç kez alınıp silinmeden DLQ'ya taşınır (1 yaparsan tek başarısızlık yeter; "set high enough to allow for sufficient retries") |
| Redrive allow policy | Hangi kaynak kuyruklar bu DLQ'yu kullanabilir (`byQueue` ≤ 10 ARN / `denyAll`) |
| FIFO + DLQ | FIFO'da enqueue timestamp DLQ'ya geçerken **sıfırlanır**; standard kuyrukta değişmez — retention hesabı buna göre yapılır |
| Retention en iyi uygulama | DLQ retention > kaynak kuyruk retention (her zaman) |
| 3+ receive (standard) | `maxReceiveCount > 3` iken mesaj 3+ kez alınıp silinmezse kuyruk arkasına taşınır |

### §8.2 Kapsam sınırı

DLQ'nun **kuyruk teknolojisi seçimi** (SQS/RabbitMQ/MySQL tablo) bu dosyanın
kapsamı dışındadır; burada yalnız kavramsal sözleşme tanımlanır. CoreMusic'e
özgülük: ⚠️ UNKNOWN (kuyruk altyapısı kararı yok).

---

## §9 Gözleme Aktarımı — Sınır Etiketi

Hata mekanizmalarının gözlemi (metrik, log, alarm, tracing) **K017'ye ait
olabilir**. Bu dosya yalnız **aktarım sözleşmesini** etiketler:

| Sinyal | Gözlem katmanına aktarım | Sahip |
|---|---|---|
| Retry denemesi (sayı, sonuç, bekleme) | Metrik + log | ⚠️ K017 sınırında |
| Circuit breaker durum değişimi | Event + alarm | ⚠️ K017 sınırında (Azure: Open'a geçiş alarm tetikler [S8]) |
| 429 / Retry-After | Sayım + kaynak etiketi | ⚠️ K017 sınırında |
| DLQ derinliği / yaş | Alarm | ⚠️ K017 sınırında (SQS alarm dokümanına atıf [S13]) |
| Son denemenin hatası (last error) | Log (Azure: erken denemeler informational, son deneme error [S7]) | ⚠️ K017 sınırında |

> **Sınır kararı VERİLMEZ:** Gözleme aksiyonu (hangi metrik, hangi alarm
> eşiği, dashboard sahipliği) K017'ye devredilir; bu dosyada yalnız hangi
> sinyalin üretileceği tanımlanır.

---

## §10 Politika / Kripto Sınırı — K009

Hata sözleşmesindeki **mesaj içeriği** güvenlik duvarından geçer:
`detail` alanı stack trace, SQL, token, kripto anahtarı **içeremez**
(RFC 9457 §3.6 "The 'detail' field... is used to carry non-database
description of an error" + güvenlik: hassas bilgi sızdırmama). CoreMusic
gizlilik politikası ve şifreleme kararları **K009-security'ye** aittir;
bu dosya yalnız hata gövdesinin bu kararlara tabi olduğunu beyan eder.

---

## §11 Yasaklar (bu dosya)

1. Retry/rate-limit **parametre değeri uydurulmaz** — ya kaynakla taşınır ya
   `⚠️ UNKNOWN` (§4.2).
2. RFC 7807 "geçerli standart" diye kullanılmaz — **RFC 9457 obsoletes
   etmiştir** (§Kaynak [S3] "Obsoletes: 7807" satırı).
3. 429'da `Retry-After` varsa jitter'lı backoff ile **ezer** (sunucu kuralı
   ihlali); önce Retry-After.
4. Non-idempotent (POST) istekte otomatik retry yok — RFC 9110 §9.2.2 [S1].
5. Gözleme aksiyonu (alarm/dashboard) bu klasörde **uygulanmaz** — K017'ye
   devredilir (§9).
6. Politika/kripto kararları (K009) bu dosyada **verilmez** (§10).
7. Circuit breaker eşik sayısı **uydurulmaz** (§5.3).

---

## §Kaynak Tablosu

| # | Kaynak | URL | Erişim | Durum |
|---|---|---|---|---|
| S1 | RFC 9110 HTTP Semantics (§9.2.2 idempotent, §10.2.3 Retry-After, §15.5.9 408, §15.6.4 503) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S2 | RFC 6585 §4 429 Too Many Requests | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ çekildi |
| S3 | RFC 9457 Problem Details (Obsoletes: 7807; IANA registry; Appendix A JSON Schema) | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✅ çekildi |
| S4 | RFC 7807 Problem Details for HTTP APIs (ilk sürüm) | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-16 | ✅ çekildi |
| S5 | AWS SDK retry behavior (standard/adaptive/legacy; max_attempts 3; 50ms/1000ms; cap 20000ms; full jitter; quota 500/14/5; x-amz-retry-after) | https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html | 2026-10-16 | ✅ çekildi |
| S6 | AWS Architecture Blog — Exponential Backoff And Jitter (Marc Brooker; Full/Equal/Decorrelated) | https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ | 2026-10-16 | ✅ çekildi |
| S7 | Microsoft Azure Retry pattern (stratejiler, idempotency, nested retry, logging) | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✅ çekildi |
| S8 | Microsoft Azure Circuit Breaker pattern (Closed/Open/Half-Open, eşikler, 429 ivmeleme) | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ çekildi |
| S9 | Google Cloud Storage retry strategy (408/429/5xx; truncated binary exponential; C++/Go defaultları) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi |
| S10 | GitHub REST API rate limits (60/saat unauth; 5000/saat auth; 100 eşzamanlı; 900 puan/dk; x-ratelimit-* header'ları; 403/429; ban) | https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api | 2026-10-16 | ✅ çekildi |
| S11 | Microsoft Graph throttling (429 + Retry-After:10 örneği; immediate retry yasak; batch) | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ çekildi |
| S12 | Stripe rate limits (100/25 rps; Stripe-Rate-Limited-Reason; concurrency 50; lock_timeout; jitter) | https://docs.stripe.com/rate-limits | 2026-10-16 | ✅ çekildi |
| S13 | Amazon SQS dead-letter queues (maxReceiveCount, redrive allow, retention, FIFO) | https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html | 2026-10-16 | ✅ çekildi |

Not: S9 GCP sayfası C++/Go defaultları için kullanılmıştır; sayfanın
"Retry strategy" ana sayfası olup Cloud Storage'a özgüdür — genel GCP
retry politikası olarak sunulmaz.

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | Retry/rate-limit CoreMusic parametre değerleri → ⚠️ UNKNOWN | FM open_questions |
| 2 | Circuit breaker eşikleri → ⚠️ UNKNOWN | FM open_questions |
| 3 | Gözleme aksiyonu sahipliği (K017) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 4 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 5 | "Timeout budget" terimi türetmeli — tek resmi kaynak yok (§6) | §6 + §1 |
| 6 | "İnce hata" takma ad — standart terim değil (§1/§3.3) | §1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
