---
node_id: K013-integraion/error-handling/error-dogrulama
node_type: technology-deepdive
title: "Error Doğrulama — Kapılar · Test Senaryoları · Sınıflandırma Denetimi · Ölçüm"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/error-handling
children: []
depends_on:
  - K013-integraion/error-handling
  - K013-integraion/error-handling/error-anayasa
  - K013-integraion/error-handling/error-sozlesme
  - K013-integraion
evidence:
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16)"
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
  - "Her kapı bir kaynak iddiasıyla eşleşir (§Kaynak Tablosu [S#])"
  - "Test senaryoları kaynaksız parametre içermeyen, sınırlayıcı (property) testlerdir"
  - "CoreMusic implementasyonu bu turda tespit edilmedi → kapılar PLANNED, ⚠️ VERIFICATION REQUIRED"
open_questions:
  - "Kapıların CI'da nerede çalıştığı (hangi job) → ⚠️ UNKNOWN"
  - "Ölçüm metriklerinin gözlem katmanı sahibi (K017) → ⚠️ VERIFICATION REQUIRED"
  - "CoreMusic retry parametreleri kapı eşikleri olarak henüz tanımlı değil → ⚠️ UNKNOWN"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: kod
---

# Error Doğrulama — Kapılar · Test Senaryoları · Sınıflandırma Denetimi · Ölçüm

> **Bu dosya ne değildir:** Uygulama kodu veya belirli bir test framework'ü
> rehberi değildir. Bu dosya **doğrulama yüzeyini** tanımlar: hata yönetiminin
> hangi kapılarla denetleneceği, her kapının hangi kaynak iddiasını
> test ettiği ve ne ölçüleceği. `status: draft` — CoreMusic kodunda hata
> yönetimi implementasyonu bu turda tespit edilmedi; kapılar **PLANNED**'tır.

---

## §0 ELI15 — Doğrulama neden ayrı dosya?

"Hata yönetimi var" demek yetmez: retry gerçekten backoff uyguluyor mu,
jitter gerçekten rastgele mi, 429'te Retry-After gerçekten izleniyor mu,
non-idempotent istekte retry gerçekten duruyor mu, circuit breaker gerçekten
Open'da kesiyor mu — bunlar **ölçülür**. Bu dosya her iddiası bir kapıya,
her kapıyı bir test senaryosuna bağlar.
(Kaynak: §Kaynak Tablosu [S1]–[S12])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `error-anayasa.md` | Sınıflandırma eksenleri, mekanizma seçimi, sınır | Kademe-3 |
| mimari | `error-sozlesme.md` | Hata sözleşmesi, Problem Details, eşleme tablosu | Kademe-3 |
| **kod** | **`error-dogrulama.md`** | **Doğrulama kapıları ve test senaryoları** | Kademe-3 |
| kod | `error-kod-ornekleri.md` | 13 dilde retry/circuit breaker örneği | Kademe-3 |
| kanit | `error-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Kapı Mimarisi — 9 Kapı

| # | Kapı | Soru | Kaynak |
|---|---|---|---|
| D-1 | Sınıflandırma kapısı | Her hata doğru sınıfa (geçici/kalıcı/ince) mı atandı? | [S3] [S7] |
| D-2 | Retry cap kapısı | Retry deneme sayısı gerçekten sınırlandırılmış mı? | [S5] |
| D-3 | Backoff kapısı | Bekleme süreleri gerçekten üstel artıyor mu (cap ile)? | [S5] [S7] |
| D-4 | Jitter kapısı | Bekleme gerçekten rastgele mi (eşzamanlı istemci kümelenmesi kırılıyor)? | [S6] [S12] |
| D-5 | Retry-After kapısı | 429/503'te sunucu değeri gerçekten izleniyor mu? | [S3] [S4] [S11] |
| D-6 | Idempotency kapısı | Non-idempotent istekte retry gerçekten duruyor mu? | [S3 §9.2.2] [S7] |
| D-7 | Circuit breaker kapısı | Open durumda çağrı gerçekten kesiliyor mu; Half-Open gerçekten sınırlı mı? | [S8] |
| D-8 | Sözleşme kapısı | Hata gövdesi RFC 9457'ye gerçekten uyuyor mu; hassas veri sızıyor mu? | [S1] [S12] |
| D-9 | DLQ kapısı | Umutsuz mesaj gerçekten izole ediliyor mu; retention gerçekten DLQ > kaynak mı? | [S12] |

---

## §2 D-1 — Sınıflandırma Kapısı

**Soru:** Gelen her hata doğru sınıfa atandı mı?

| Test | Beklenen | Kanıt |
|---|---|---|
| 400/401/403/404/405/422 ver → sınıflandırıcı | `permanent`, `retryable=false` | [S3 §15.5] |
| 408/429/500/502/503/504 ver → sınıflandırıcı | `transient`, `retryable=true` | [S3 §15] [S9] |
| Bağlantı refused/reset/EOF ver → sınıflandırıcı | `transient` | [S9] |
| 409/412 ver → sınıflandırıcı | `permanent` (+ ince hata adayı etiketi) | [S3 §15.5.10/15.5.13] |
| Timeout sonrası "success ama doğrulanamıyor" senaryosu | `subtle` (idempotency gerekli) | [S7] |

**Sınır testi (property):** Sınıflandırıcı asla `permanent` bir hatayı
`transient` olarak atamaz (retry storm riski). Tersi (transient→permanent)
fail-fast nedeniyle kritik değil ama ölçülür.

---

## §3 D-2 — Retry Cap Kapısı

**Soru:** Retry gerçekten duruyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| Sürekli transient hata ver → çağrı | Tam olarak N deneme sonrası son hata yükseltilir (N = max_attempts) | [S5] (AWS default N=3; CoreMusic N → ⚠️ UNKNOWN) |
| Deneme sayacı gözlemlenir | Sayaç N'yi aşmaz | [S5] |
| AWS SDK davranışı referans | standard retry'da max_attempts default 3 | [S5] |

**Yasak:** N değeri uydurulmaz — kapı, N'nin **tanımlı ve saygı
gördüğünü** test eder, spesifik sayı dayatmaz.

---

## §4 D-3 — Backoff Kapısı

**Soru:** Bekleme gerçekten üstel mi, cap uygulanıyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| ardışık bekleme süreleri kaydedilir | `d[i+1] ≈ m × d[i]` (m = multiplier; m ≥ 2 önerilir, CoreMusic m → ⚠️ UNKNOWN) | [S5] [S7-GCP] |
| Uzun backoff | `d[i] ≤ cap` (AWS cap 20000 ms referans; CoreMusic cap → ⚠️ UNKNOWN) | [S5] |
| Nested retry (iç içe) | Toplam gecikme bütçe ihlali tespit edilir → fail-fast beklentisi | [S7] |

---

## §5 D-4 — Jitter Kapısı

**Soru:** Rastgelelik gerçekten var mı, kümelenme kırılıyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| Aynı deneme indeksinde M istemciden bekleme örneklenir | Bekleme değerleri **eşit değildir** (rastgelelik) | [S6] |
| Full-jitter özelliği | `0 ≤ d ≤ min(cap, base×2^retry)` (üstel sınırlar içinde rastgele) | [S5] |
| Kümelenme testi (simülasyon) | jitter'sız backoff'a göre çağrı dağılımı daha homojen | [S6] |
| Deterministiklik | Retry testlerinde tohum (seed) sabitlenebilir — jitter testi flaky değildir | (test mühendisliği; ⚠️ uygulama detayı UNKNOWN) |

---

## §6 D-5 — Retry-After Kapısı

**Soru:** Sunucu kuralı gerçekten izleniyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| 429 + `Retry-After: 10` (Graph örneği) ver | İlk yeniden deneme ≥10 sn sonra | [S11] |
| 429 + `Retry-After` HTTP-date | Tarihe kadar bekleme | [S3 §10.2.3] |
| 503 + `Retry-After` | Aynı davranış | [S3 §15.6.4] |
| 429 + Retry-After **YOK** | Jitter'lı exponential backoff'a düşer | [S11] |
| GitHub: 403 + `x-ratelimit-remaining: 0` | `x-ratelimit-reset`'e kadar retry yok | [S10] |
| GitHub: secondary + `retry-after` yok | ≥60 sn bekleme | [S10] |
| AWS: `x-amz-retry-after` | O kadar bekleme | [S5] |

---

## §7 D-6 — Idempotency Kapısı (kritik)

**Soru:** Non-idempotent istekte retry gerçekten duruyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| POST (non-idempotent) + ağ hatası | Otomatik retry **YOK** | [S3 §9.2.2] ("A client SHOULD NOT automatically retry a request with a non-idempotent method") |
| PUT (idempotent) + ağ hatası | Retry serbest | [S3 §9.2.2] |
| Idempotency key ile POST + timeout | Aynı key ile retry → çift yazma YOK | [S7] [S10-Stripe-idempotency] |
| İnce hata: timeout sonrası | Sonuç doğrulanmadan "başarılı" sayılmaz | [S7] |
| Proxy seviyesi | "A proxy MUST NOT automatically retry non-idempotent requests" | [S3 §9.2.2] |

---

## §8 D-7 — Circuit Breaker Kapısı

**Soru:** Durum makinesi gerçekten çalışıyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| Closed'da eşik+aşım başarısızlık | → Open; zamanlayıcı başlar | [S8] |
| Open'da istek | Çağrı **yapılmaz**, hemen hata döner | [S8] |
| Zamanlayıcı doldu | → Half-Open; **sınırlı** deneme | [S8] |
| Half-Open tüm denemeler başarılı | → Closed; sayaç sıfırlanır | [S8] |
| Half-Open tek başarısızlık | → Open; zamanlayıcı yeniden | [S8] |
| Eşik zaman bazlı | Periyodik sayaç sıfırlaması (ara sıra hata Open'a götürmez) | [S8] |
| 429 ivmeleme | Tekrarlayan 429 → anında trip (varsa) | [S8] |
| Retry etkileşimi | Circuit breaker Open'ken retry **durmaz ama anında hata alır**; circuit breaker hatasında retry kontrendikedir | [S7] [S8] |

---

## §9 D-8 — Sözleşme Kapısı

**Soru:** Gövde RFC 9457'ye uyuyor mu; sızıntı var mı?

| Test | Beklenen | Kanıt |
|---|---|---|
| Hata gövdesi Content-Type | `application/problem+json` | [S1] |
| Zorunlu `type` alanı | Mevcut (veya `about:blank`) | [S1] |
| `status` alanı | HTTP status ile eşleşir (number) | [S1] |
| Aynı `type` için `title` | Sabit (değişmez) | [S1] |
| `detail` sızıntı taraması | stack trace / SQL / token / anahtar **YOK** (K009) | [S1 §3] + K009 |
| 429 yanıtında Retry-After | Header'da; gövdede tekrar **YOK** | [S4] [S3] |
| RFC 7807 referansı | Yeni işlerde kullanılmaz (RFC 9457 esas) | [S1] |

---

## §10 D-9 — DLQ Kapısı

**Soru:** Umutsuz iş gerçekten izole ediliyor mu?

| Test | Beklenen | Kanıt |
|---|---|---|
| `maxReceiveCount` aşıldı | Mesaj DLQ'ya taşınır | [S12] |
| `maxReceiveCount = 1` | Tek başarısızlık yeter (düşük değer uyarısı) | [S12] |
| Redrive allow: `denyAll` | DLQ kullanılamaz | [S12] |
| Retention: DLQ < kaynak | Best-practice ihlali tespit edilir (uyarı) | [S12] |
| FIFO + DLQ | Enqueue timestamp sıfırlanır; retention hesabı buna göre | [S12] |
| Redrive | DLQ → kaynak kuyruk mümkün | [S12] |

---

## §11 Entegrasyon Senaryoları (uçtan uca)

| # | Senaryo | Kapılar |
|---|---|---|
| E-1 | Dış API 429 döner, Retry-After verir → istemci beklemez | D-5 ihlali |
| E-2 | Dış API 503 döner, istemci 3 kez dener, hepsi başarısız → son hata yükseltilir | D-2, D-3 |
| E-3 | Bağlantı kesilir → jitter'lı retry; 10 istemci aynı anda → kümelenme yok | D-4 |
| E-4 | POST + timeout → istemci idempotency key ile dener, çift yazma yok | D-6 |
| E-5 | Sağlayıcı çöker → circuit breaker Open → çağrılar kesilir, sağlık normale dönünce kapanır | D-7 |
| E-6 | Kuyruk mesajı 3 kez başarısız → DLQ; redrive ile geri gelir | D-9 |
| E-7 | Kullanıcı 422 alır → RFC 9457 gövdesi; detayda sızıntı yok | D-8, D-1 |

---

## §12 Ölçüm Tablosu (ne sayılır?)

| Ölçüm | Kapı | Birim | Çıkış |
|---|---|---|---|
| Retry denemesi (sayı/sonuç) | D-2 | adet | metrik (K017 sınırında) |
| Bekleme süresi dağılımı | D-3/D-4 | ms | histogram (K017) |
| 429 + Retry-After izleme oranı | D-5 | oran | metrik (K017) |
| Non-idempotent retry ihlali | D-6 | adet (hedef 0) | alarm (K017) |
| Circuit breaker durum geçişi | D-7 | adet + süre | event (K017) |
| RFC 9457 uyumsuz gövde | D-8 | adet (hedef 0) | CI çıktısı |
| DLQ derinliği/yaşı | D-9 | adet/sn | alarm (K017) |

> **Sınır:** Bu ölçümlerin **gözlem katmanında** nasıl toplanacağı/aksiyonu
> K017'ye ait olabilir → etiketli (`error-anayasa.md` §9).

---

## §13 CI Kapısı — Nerede çalışır?

⚠️ UNKNOWN — CoreMusic CI job isimleri/hiyerarşisi bu turda doğrulanmadı.
Önerilen yerleşim (taslak, onay bekliyor): birim kapıları (D-1..D-6, D-8)
unit test job'unda; entegrasyon kapıları (D-7, D-9, E-*) integration job'unda.

---

## §12A Örnek Zaman Çizelgeleri (kapıların beklenen çıktısı)

### T-1 — Retry + full jitter zaman çizelgesi (referans parametreler)

AWS referans: base 1000 ms (throttling 1000 ms [S5]), cap 20000 ms, N=3.
`backoff_i = min(20000, 1000×2^i)` · `bekleme_i = rand(0, backoff_i)`.

| Deneme | backoff üst sınırı | Bekleme (örnek, rastgele) | Not |
|---|---|---|---|
| 1 (attempt=0 sonrası) | 2000 ms | 412 ms | rand(0, 2000) |
| 2 | 4000 ms | 3517 ms | rand(0, 4000) |
| 3 | 8000 ms | 901 ms | cap doldu → son hata yükseltilir |

**Toplam gecikme (örnek):** 412+3517+901 = 4830 ms — her örneklemede
**farklıdır** (jitter). Kapı D-3/D-4 bu tabloyu property olarak doğrular
(P-2/P-4).

### T-2 — Retry-After ezilmesi (D-5)

| Olay | Beklenen |
|---|---|
| t=0: istek → 429 + `Retry-After: 10` | Sınıflandırıcı: transient+rate-limited |
| t=0: jitter'lı backoff hesaplanır ama **uygulanmaz** | Sunucu kuralı üstün [S11] |
| t≥10s: yeniden deneme | İhlal varsa D-5 kırmızı |

### T-3 — Circuit breaker durum geçişi (D-7)

| t | Olay | Durum | Beklenen |
|---|---|---|---|
| 0s | 5 başarısızlık (eşik: 5 — ÖRNEK, UNKNOWN) | Closed → Open | Zamanlayıcı başlat (ÖRNEK süre: UNKNOWN) |
| 0s–Xs | İstekler | Open | Hemen hata; downstream çağrı 0 |
| Xs | Zamanlayıcı doldu | → Half-Open | Sınırlı deneme (K → UNKNOWN) |
| Xs+δ | Denemeler başarılı | → Closed | Sayaç sıfır |
| Xs+δ | (alternatif) tek başarısızlık | → Open | Zamanlayıcı yeniden |

> Tablodaki eşik/süre ÖRNEK işaretlidir — kapı testi bu değerleri
> **sabit input** olarak alır (sistemden bağımsız), CoreMusic politikası
> değildir (zero-hallucination).

---

## §14 Yasaklar (bu dosya)

1. Eşik/değer **uydurulmaz** (max_attempts, cap, multiplier, open-duration) —
   ya kaynakla ya ⚠️ UNKNOWN (D-2/D-3).
2. Jitter testi tohumsuz **flaky** çalıştırılmaz (seed sabitlenir — §5).
3. "Kapı geçti" iddiası kanıtsız **yazılmaz** (verification-before-completion).
4. Gözleme aksiyonu (dashboard/alarm kurulumu) bu klasörde **uygulanmaz** (K017; §12).
5. DLQ kapısı belirli bir kuyruk teknolojisine **bağlanmaz** (kavramsal; §10).

---

## §15 İş Kalemleri (bu dosya için)

| # | İş | Durum |
|---|---|---|
| 1 | CoreMusic retry parametrelerinin onayı (ADR) → kapı eşikleri | ⏳ BEKLEMEDE — open_question |
| 2 | Circuit breaker eşik onayı | ⏳ BEKLEMEDE — open_question |
| 3 | CI job yerleşimi onayı | ⏳ BEKLEMEDE — §13 |
| 4 | Eşleme tablosu uygulama katmanı kararı (error-sozlesme §3) | ⏳ BEKLEMEDE |
| 5 | Gözleme sahipliği K017 ile netleştirme | ⏳ BEKLEMEDE |

---

## §15A Sahte Sunucu (Mock) Davranış Kataloğu

Kapıların testi için gereken **sentetik hata üreteçleri**. Her satır bir
mock davranışını tanımlar; değerler kaynaklıdır (uydurma yok).

| # | Mock davranışı | Protokol | Kapı |
|---|---|---|---|
| M-1 | Her zaman 429 + `Retry-After: 10` | HTTP | D-5 |
| M-2 | Her zaman 429, Retry-After YOK | HTTP | D-4 (jitter'a düşer) |
| M-3 | 503 + `Retry-After: <HTTP-date>` | HTTP | D-5 (date biçimi) |
| M-4 | İlk 2 istek 503, 3. istek 200 | HTTP | D-2 (retry kazanır) |
| M-5 | Sürekli 500 | HTTP | D-2 (cap doldu → son hata) |
| M-6 | 400 + `ThrottlingException` gövdesi (AWS tarzı) | HTTP | D-1 (sınıflandırıcı: rate-limited) |
| M-7 | 403 + `x-ratelimit-remaining: 0` + `x-ratelimit-reset: <epoch>` | HTTP | D-5 (reset'e kadar bekleme) |
| M-8 | 429 + `Stripe-Rate-Limited-Reason: global-concurrency` | HTTP | D-1 (sınıf) |
| M-9 | 429 + kod `lock_timeout` (header YOK) | HTTP | D-1 (rate-limit DEĞİL; retry edilebilir) |
| M-10 | Bağlantı reset (TCP seviyesi) | TCP | D-1 (transient) |
| M-11 | Timeout (yanıt yok) | TCP | D-1 (transient) + D-6 (idempotency) |
| M-12 | 200 ama yanıt gövdesi yarım (ince hata) | HTTP | D-6 |
| M-13 | Batch: HTTP 200 + iç istek 429 + JSON içi `retry-after` (Graph tarzı) | HTTP | D-5 (batch) |
| M-14 | DLQ kuyruğu: mesajı 3 kez başarısız teslim | kuyruk | D-9 |

**Kurgu sınırı:** M-8/M-9 Stripe'ın **gerçek** header/kod adlarıyla
kurulmuştur ([S10]); M-6 AWS kod adıyla ([S5]); M-13 Graph kalıbıyla
([S11]). Bu adlar dışındaki uydurma sağlayıcı kodu mock'lara **konmaz**.

---

## §15B Property-Based Testler (sınır testleri)

Tek tek senaryo yerine **sınırlar** test edilir; dil-bağımsızdır.

| # | Property | Beklenen |
|---|---|---|
| P-1 | ∀ transient hata serisi: deneme sayısı ≤ max_attempts | cap'a asla aşılmaz |
| P-2 | ∀ deneme i: `0 ≤ bekleme_i ≤ cap` | negatif/taşan bekleme yok |
| P-3 | ∀ permanent hata: deneme sayısı == 1 | kalıcıda ikinci deneme yok |
| P-4 | Aynı (attempt, base, cap) için 100 örnekleme | ≥2 farklı bekleme değeri (jitter canlı) |
| P-5 | Retry-After verildiğinde: `bekleme ≥ Retry-After` | sunucu kuralı ezilmez |
| P-6 | Post + idempotency key'siz + hata: çağrı sayısı == 1 | çift etki yok |
| P-7 | CB Open'da: downstream çağrı sayısı == 0 | kesme çalışıyor |
| P-8 | Gövde üretimi: `detail` içinde /(stack|select|password|token|key)/i eşleşmesi == 0 | sızıntı yok (K009) |

---

## §15C Ölçüm Eşikleri (kapı geçiş kriteri — taslak)

| Kapı | Geçiş kriteri | Eşik değeri |
|---|---|---|
| D-2 | Sayaç ihlali == 0 | 0 (mutlak) |
| D-4 | P-4 ihlali == 0 | 0 (mutlak) |
| D-5 | Retry-After ezilmesi == 0 | 0 (mutlak) |
| D-6 | Non-idempotent otomatik retry == 0 | 0 (mutlak) |
| D-8 | Sızıntı taraması == 0 | 0 (mutlak) |
| D-3/D-7 | Zaman-tüketimi testleri | ⚠️ UNKNOWN (CoreMusic bütçe/eşik kararı yok) |

> Mutlak-0 kapılar (yukarıdaki 5) CoreMusic politika onayından **bağımsız**
> uygulanabilir; diğerleri eşik onayı bekler.

---

## §15D Dil-Bağımsız Test Veri Matrisi

Kapı testlerinde kullanılan **sentetik giriş kümeleri**. Değerler kaynaklı
veya açıkça işaretli örneklerdir.

| Veri kümesi | İçerik | Kapı |
|---|---|---|
| V-1 Kalıcı set | 400, 401, 403, 404, 405, 409, 412, 415, 422 | D-1, D-2 |
| V-2 Geçici set | 408, 429, 500, 502, 503, 504 | D-1, D-2 |
| V-3 Bağlantı seti | refused, reset, EOF, DNS-fail | D-1 |
| V-4 Retry-After biçimleri | `10` (saniye) · `Fri, 31 Dec 1999 23:59:59 GMT` (date — RFC 9110 örneği [S3]) · bozuk `abc` · yok | D-5 |
| V-5 Rate-limit header seti | `x-ratelimit-remaining: 0` + reset epoch · `Stripe-Rate-Limited-Reason: global-rate` · `: endpoint-rate` · `: global-concurrency` · `: resource-specific` | D-1, D-5 |
| V-6 AWS throttling gövdesi | `ThrottlingException`, `Throttling`, `TooManyRequestsException`, `RequestThrottled`, `RequestThrottledException` | D-1 |
| V-7 Sızıntı gövdeleri | stack-trace gövdesi · SQL gövdesi · `password=` içeren `detail` · `token` içeren `detail` | D-8 |
| V-8 RFC 9457 gövdesi | §6.1–§6.3'teki üç gövde (error-sozlesme) | D-8 |

**Kurgu sınırı:** V-4'teki date örneği RFC 9110 §10.2.3'teki birebir
örnektir [S3]; V-5/V-6 adları kaynaklıdır (§15A M-8/M-9/M-6 ile aynı
kaynaklar). Gözleme metrik adı, dashboard adı **uydurulmaz**.

---

## §15E Hata Yükseltme (Escalation) Sırası — Kapılarla İlişkisi

```text
Kapı D-1 (sınıflandırma)
  → kalıcı    → hata gövdesi (D-8) → müşteriye
  → geçici    → D-6 (idempotency kontrolü)
      → geçersiz → hata gövdesi (D-8)
      → geçerli → D-2/D-3/D-4 (retry: cap + backoff + jitter)
          → Retry-After varsa D-5 (sunucu kuralı)
          → cap doldu → D-7 (circuit breaker'a sinyal)
              → trip → Open (D-7) → fail-fast hata (D-8)
              → kuyruk varsa → D-9 (DLQ)
```

---

## §Kaynak Tablosu

| # | Kaynak | URL | Erişim | Durum |
|---|---|---|---|---|
| S1 | RFC 9457 (gövde alanları, medya tipi, title sabitliği) | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✅ çekildi |
| S2 | RFC 7807 (tarihsel) | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-16 | ✅ çekildi |
| S3 | RFC 9110 (§9.2.2 idempotent + proxy kuralı; §15 status; §10.2.3 Retry-After) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S4 | RFC 6585 §4 (429) | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ çekildi |
| S5 | AWS SDK retry behavior (max_attempts 3; cap 20000ms; full jitter; x-amz-retry-after) | https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html | 2026-10-16 | ✅ çekildi |
| S6 | AWS Architecture Blog — Exponential Backoff And Jitter (Full/Equal/Decorrelated; kümelenme) | https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ | 2026-10-16 | ✅ çekildi |
| S7 | Azure Retry pattern (idempotency; nested retry; fail-fast) | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✅ çekildi |
| S8 | Azure Circuit Breaker pattern (durum makinesi; 429 ivmeleme) | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ çekildi |
| S9 | Google Cloud Storage retry strategy (retry'lenebilir kodlar; bağlantı hataları) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi |
| S10 | GitHub REST rate limits (403+remaining:0; secondary; ≥60 sn) | https://docs.github.com/en/rest/using-the-rest-api/rate-limits-for-the-rest-api | 2026-10-16 | ✅ çekildi |
| S11 | Microsoft Graph throttling (Retry-After:10 örneği) | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ çekildi |
| S12 | Amazon SQS dead-letter queues (maxReceiveCount; retention; FIFO) | https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-dead-letter-queues.html | 2026-10-16 | ✅ çekildi |

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | CoreMusic retry/circuit-breaker eşikleri → ⚠️ UNKNOWN (kapılar PLANNED) | FM open_questions |
| 2 | CI job yerleşimi → ⚠️ UNKNOWN | FM open_questions |
| 3 | Gözleme sahipliği (K017) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 4 | Eşleme uygulama katmanı → ⚠️ UNKNOWN | error-sozlesme §7 |
| 5 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
