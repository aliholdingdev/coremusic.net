---
node_id: K013-integraion/event-contracts/event-dogrulama
node_type: contract-verification
title: "Event Contracts — Doğrulama"
type: event-contract-verification
status: active
date: 2026-10-16
parent_id: K013-integraion/event-contracts/index
children:
  - K013-integraion/event-contracts/event-anayasa
  - K013-integraion/event-contracts/event-sozlesme
depends_on:
  - K013-integraion/event-contracts/event-anayasa
  - K013-integraion/event-contracts/event-sozlesme
evidence:
  - "[E1] rfc2104 HMAC — hash.hmac"
  - "[E2] rfc6234 SHA-2 — sha256 imza algoritması"
  - "[E3] GitHub — payload-signature-secret 757107ea… test vektörü"
  - "[E4] Stripe — timestamp tolerance + imza doğrulama"
  - "[E5] Slack — v0: tabanı + timing-safe karşılaştırma"
  - "[E6] draft-mayankpanke — Idempotency-Key zorunlu, 24 saat dedup penceresi"
  - "[E7] draft-mayankpanke — tam jitter backoff rastgele[0, min(cap, base*2^n)]"
  - "[E8] CloudEvents 1.0.2 — zorunlu alan doğrulaması"
  - "[E9] WebSub Rec — hub.challenge niyet doğrulama"
  - "[E10] rfc3339 — timestamp biçimi doğrulaması"
  - "[E11] rfc9110 — HTTP durum kodu semantiği"
  - "[E12] rfc9421 — HTTP mesaj imzalama"
  - "[E13] WebSub — X-Hub-Signature doğrulama"
  - "[E14] standardwebhooks.com — sabit zamanlı karşılaştırma"
  - "[E15] rfc8594 — Sunset başlığı (düzlem dışı)"
validation:
  - "DOĞRULANDI: HMAC-SHA256/512, imza ön eki sha256= / v0= / t=,v1= üç ayrı biçim [E1][E3][E4][E5]"
  - "DOĞRULANDI: GitHub test vektörü: secret=It's a Secret to Everybody, body=Hello, World! → sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17 [E3]"
  - "DOĞRULANDI: Stripe imzalanan girdi t+'.'+body, 5 dk tolerance [E4]"
  - "DOĞRULANDI: Slack taban v0:<ts>:<body>, 5 dk, sabit zamanlı karşılaştırma zorunlu [E5][E14]"
  - "DOĞRULANDI: Idempotency-Key zorunlu, 24 saat minimum dedup penceresi [E6]"
  - "DOĞRULANDI: tam jitter backoff rastgele[0, min(cap, base*2^n)], base 1 sn, cap 60-600 sn [E7]"
  - "DOĞRULANDI: CloudEvents zorunlu alanlar id/source/specversion/type [E8]"
  - "DOĞRULANDI: WebSub hub.challenge niyet doğrulama GET, hub.mode=denied → hata [E9]"
  - "DOĞRULANDI: timestamp RFC 3339 biçimi [E10]"
  - "DOĞRULANDI: durum kodu eşlemesi 2xx kabul / 4xx terminal / 408-429-5xx geçici [E11]"
  - "DOĞRULANDI: 404-410 terminal, 401-403 terminal, 202 kabul [E6][E9][E11]"
  - "DOĞRULANDI: HTTP mesaj imzalama rfc9421 ortak referans [E12]"
  - "DOĞRULANDI: X-Hub-Signature sha1/256/384/512, lease zorunlu [E13]"
  - "DOĞRULANDI: sabit zamanlı karşılaştırma (timing-safe) zorunlu, != ile karşılaştırma YASAK [E14]"
  - "DOĞRULANDI: body bütünlüğü doğrulaması imza hesaplamadan ÖNCE [E3][E4][E5]"
  - "DOĞRULANDI: yeniden gönderim (replay) penceresi Stripe 5 dk, Slack 5 dk [E4][E5]"
  - "VERIFICATION REQUIRED: idempotency penceresi 24 saat önerisi — implementasyon kararı ADR bekliyor [E6]"
  - "VERIFICATION REQUIRED: imza algoritması seçimi CoreMusic için — HMAC-SHA256 önerilir ama ADR gerekli [E1][E2]"
  - "VERIFICATION REQUIRED: çekirdek doğrulama katmanı sayısı — bu dosya 7 katman tanımlar, ADR gerekli"
  - "VERIFICATION REQUIRED: tam jitter parametreleri base=1sn cap=60-600sn aralığı — CoreMusic seçimi ADR bekliyor [E7]"
open_questions:
  - "Q1: İmza algoritması: HMAC-SHA256 mi HMAC-SHA512 mi? [E1][E2] → ADR bekliyor"
  - "Q2: İmza ön eki: 'sha256=' mi 'v0=' mi 't=,v1=' mi? [E3][E4][E5] → sozlesme.md §2'de tanımlı"
  - "Q3: İdempotency penceresi: 24 saat mi? Draft öneriyor, CoreMusic doğrulamadı [E6] → ADR bekliyor"
  - "Q4: Jitter parametreleri: base=1sn, cap=60-600sn — CoreMusic seçimi? [E7] → ADR bekliyor"
  - "Q5: Doğrulama katmanı: sunucu tarafında doğrulama mı, istemci tarafında da mı? [E12] → mimari karar"
  - "Q6: Test vektörü: GitHub'ınki kullanılabilir mi? [E3] → evet, doğrulandı"
  - "Q7: Yeniden gönderim penceresi: Stripe 5 dk, Slack 5 dk — CoreMusic toleransı? [E4][E5] → ADR bekliyor"
  - "Q8: 404-410 terminal kabulü: CoreMusic'e uygulanır mı? [E6][E11] → evet, draft'ta zorunlu"
  - "Q9: Timestamp biçimi: RFC 3339 mü, Unix timestamp mi? [E10] → CloudEvents RFC 3339 istiyor [E8]"
  - "Q10: Doğrulama hatası loglanır mı? Sızıntı riski — hassas veri yasak"
  - "Q11: CloudEvents zorunlu alan doğrulaması: id/source/specversion/type — hepsi var mı kontrolü [E8]"
  - "Q12: WebSub hub.challenge GET doğrulaması: CoreMusic hub'ı için geçerli mi? [E9] → evet, Rec zorunlu"
  - "Q13: X-Hub-Signature algoritması: sha1 mi sha256 mi? [E13] → Rec her ikisine de izin verir, sha256 önerilir"
  - "Q14: Body bütünlüğü: imza hesaplamadan ÖNCE doğrulanır — sıra zorunlu [E3][E4][E5]"
  - "Q15: Timing-safe karşılaştırma: hangi dilde? [E14] → her dilde farklı API"
  - "Q16: Doğrulama CI kapısına eklenir mi? → evet, §8"
  - "Q17: Imza doğrulama testi: canlı webhook ile mi, sentetik ile mi? → sentetik [E3]"
  - "Q18: CloudEvents contentType doğrulaması: application/cloudevents+json [E8] → zorunlu mu?"
  - "Q19: Yeniden gönderim (retry) doğrulama: her retry imzalanır mı? → evet, her gönderim imzalanır [E4]"
  - "Q20: Idempotency-Key benzersizliği: tek event mi, event+target mi? [E6] → ADR bekliyor"
group: kod
---

# Event Contracts — Doğrulama

> Doğrulama katmanları, test vektörleri ve CI kapısı.
> Kalıp: `api-contracts/openapi/openapi-dogrulama.md`.
> **15 doğrulanmış kaynak** ([E1]-[E15]), **20 açık soru** (Q1-Q20).

---

## §0 ELI15

Bir webhook olayı geldiğinde ne yaparsın? **Üç şey**:

1. **İmzayı doğrula** — gelen gövde, senin gizli anahtarınla HMAC hesapla, karşılaştır.
   İmza yoksa veya yanlışsa → **reddet, işleme**.
2. **Zaman damgasını kontrol et** — 5 dakikadan eskiyse → **reddet (replay saldırısı olabilir)**.
3. **Benzersizliği kontrol et** — bu olay ID'si daha önce işlendi mi? → Evetse → **atla, tekrar işleme**.

Bu üç adım **her webhook işlemeden önce** çalıştırılır. İmza doğrulanmadan gövde **asla** işlenmez.
Doğrulama başarısızsa hata **loglanır** ama **gizli anahtar** veya **ham imza** loga **yazılmaz**.
Test vektörü: GitHub'ınki (`sha256=757107ea...`) her ortamda çalıştırılır.

---

## §1 Doğrulama Katmanları — 7 Katman

| # | Katman | Ne kontrol edilir? | Hata durumunda |
|---|--------|-------------------|----------------|
| 1 | **HTTP başlığı varlığı** | `Content-Type`, imza başlığı mevcut mu? | 400 Bad Request |
| 2 | **İmza varlığı** | `X-Hub-Signature-256` / `X-Slack-Signature` / `Stripe-Signature` başlığı var mı? | 401 Unauthorized |
| 3 | **Timestamp doğrulaması** | Zaman damgası RFC 3339 mü? Pencere içinde mi? | 401 Unauthorized |
| 4 | **İmza hesabı** | HMAC-SHA256 ile yeniden hesapla, karşılaştır | 401 Unauthorized |
| 5 | **Body bütünlüğü** | Ham body, imza hesaplamadan önce okunmuş mu? Byte-level | 401 Unauthorized |
| 6 | **Benzersizlik (idempotency)** | Event ID daha önce işlendi mi? 24 saat penceresi | 200 OK (sessizce atla) |
| 7 | **Zorunlu alan doğrulaması** | CloudEvents: `id`/`source`/`specversion`/`type` var mı? | 400 Bad Request |

**Katman sırası zorunlu**: 1 → 2 → 3 → 4 → 5 → 6 → 7. Atlanan katman → güvenlik açığı.

---

## §2 Test Vektörü — GitHub (Doğrulanmış) [E3]

**Kaynak**: `docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` — GitHub Docs, 2026-10-16 ✅

**Girdi**:
- **Secret**: `It's a Secret to Everybody`
- **Body**: `Hello, World!`
- **Algoritma**: `HMAC-SHA256`
- **İmza biçimi**: `sha256=<hex>`

**Beklenen Çıktı**:
```
sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17
```

**Kullanım**: Her ortamda (geliştirme, test, üretim) bu vektör çalıştırılır. Çıktı farklıysa →
imza kodu **hatalıdır**, deploy **durdurulur**.

**Alternatif vektör** (Stripe formatı) [E4]:
- Secret: `whsec_...` (üretimde gizli)
- Body: `{"id":"evt_1","type":"payment.succeeded"}`
- Timestamp: `1234567890`
- İmzalanan girdi: `1234567890.{"id":"evt_1","type":"payment.succeeded"}`
- Beklenen: `t=1234567890,v1=<hex>` (hex değeri secret'a göre değişir)

**Alternatif vektör** (Slack formatı) [E5]:
- Secret: `8f742231b10e8888abcd99yyyzzz85a5`
- Timestamp: `1531420618.475807`
- Body: `token=xyzz0WbapA4vBCDefasx0dd6vbMbVdjaAloYyvi`
- Taban: `v0:1531420618.475807:token=xyzz0WbapA4vBCDefasx0dd6vbMbVdjaAloYyvi`
- Beklenen: `v0=a2114d57b48eac39b9ad189dd8316235a7b4a8d21a10bd27519666489c69b503`

---

## §3 İmza Doğrulama — Algoritma Adımları [E1][E4][E5]

```
1. Ham body'yi byte olarak oku (JSON parse ETME, encoding DEĞİŞTİRME).
2. Timestamp'i al (başlıktan veya body'den).
3. İmzalanan girdiyi oluştur:
   - GitHub formatı:  body
   - Stripe formatı:  timestamp + "." + body
   - Slack formatı:   "v0:" + timestamp + ":" + body
   - WebSub formatı:  body (secret byte[])
4. HMAC hesapla:
   - Anahtar: senin gizli secret'ın
   - Algoritma: SHA-256 veya SHA-512
   - Girdi: imzalanan girdi (3. adım)
5. Hex'e çevir, ön eki ekle:
   - GitHub:  "sha256=" + hex
   - Stripe:  "t=" + timestamp + ",v1=" + hex
   - Slack:   "v0=" + hex
6. Sabit zamanlı karşılaştırma (timing-safe):
   - Python: hmac.compare_digest()
   - Node:   crypto.timingSafeEqual()
   - Go:     hmac.Equal()
   - PHP:    hash_equals()
   - Java:   MessageDigest.isEqual()
7. Eşleşmiyorsa → 401 Unauthorized, logla (ham imza YAZMA).
```

**KRİTİK**: Adım 1'de body **ham** okunur. JSON parse **sonra** yapılır. İmza, parse edilmiş
JSON'dan **değil**, ham byte'lardan hesaplanır.

---

## §4 Zaman Damgası Doğrulaması — Pencere [E4][E5][E10]

**Stripe**: `timestamp_tolerance = 300 saniye (5 dakika)` [E4]
- `abs(now - timestamp) > 300` → reddet
- **Neden**: Replay saldırısı — saldırgan eski imzalı paketi tekrar gönderir

**Slack**: `5 dakika` [E5]
- `now - timestamp > 300` → reddet
- Aynı neden

**CloudEvents**: `time` alanı RFC 3339 formatında [E10]
- `time` alanı opsiyonel ama varsa RFC 3339'a uygun olmalı
- Doğrulama: `YYYY-MM-DDTHH:MM:SS.nnnnnnnnn+HH:MM`

**CoreMusic önerisi**: `timestamp_tolerance = 300 saniye` — Stripe ve Slack ile uyumlu.

---

## §5 İdempotency — Benzersizlik Doğrulaması [E6]

**Kaynak**: `datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` — 2026-06-28 ✅

**Kural**: `Idempotency-Key` başlığı **zorunlu**; key'i daha önce işlenmişse → **200 OK dön, sessizce atla**.

**Draft önerisi**: "Storage **MUST** retain idempotency keys for **at least 24 hours** after the
last retry" [E6].

**Doğrulama adımları**:
```
1. Idempotency-Key başlığını oku. Yoksa → 400 Bad Request.
2. Key'i depoda ara:
   - Varsa ve 24 saat içindeyse → 200 OK (zaten işlenmiş)
   - Varsa ve 24 saat geçmişse → işle
   - Yoksa → işle
3. İşlemi tamamla, key'i depola (24 saat TTL).
```

**Storage önerisi**: Redis `SET key value EX 86400` (24 saat = 86400 saniye).

**⚠️ ADR bekliyor**: 24 saat önerisi draft'tan geliyor; CoreMusic'in kendi penceresi
(ne kadar?) belirlenmedi. Q3'e bak.

---

## §6 Backoff — Jitter Doğrulaması [E7]

**Kaynak**: `datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` — tam jitter kuralı ✅

**Formül**:
```
delay = random[0, min(cap, base * 2^retry)]
base = 1 second
cap  = 60 - 600 seconds
```

**Doğrulama**:
- `retry` sayacı: 0'dan başlar, her başarısız gönderimde artar
- `delay` rastgele olmalı (deterministic değil)
- `cap` aşılarsa → `cap` kullanılır (exponential büyüme durur)

**Test vektörü** (base=1s, cap=60s):
| retry | 2^retry | base*2^retry | min(cap, ...) | delay aralığı |
|-------|---------|--------------|---------------|---------------|
| 0 | 1 | 1s | 1s | 0-1s |
| 1 | 2 | 2s | 2s | 0-2s |
| 2 | 4 | 4s | 4s | 0-4s |
| 3 | 8 | 8s | 8s | 0-8s |
| 4 | 16 | 16s | 16s | 0-16s |
| 5 | 32 | 32s | 32s | 0-32s |
| 6 | 64 | 64s | 60s (cap) | 0-60s |
| 7+ | 128+ | 128s+ | 60s (cap) | 0-60s |

**⚠️ ADR bekliyor**: base=1s, cap=60s aralığı önerilir; CoreMusic'in seçimi belirlenmedi.

---

## §7 HTTP Durum Kodu Doğrulaması [E6][E11]

**Kaynak**: `datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` + `rfc9110` ✅

| Durum | Sınıf | Yeniden gönderim? | Doğrulama |
|-------|-------|-------------------|-----------|
| **202 Accepted** | Kabul | Hayır | Alındı ama işlenmedi — WebSub Rec zorunlu kılıyor |
| **200 OK** | Kabul | Hayır | İşlendi veya sessizce atlandı |
| **400 Bad Request** | Terminal | Hayır | İstek hatalı — yeniden gönderilsin de değişmez |
| **401 Unauthorized** | Terminal | Hayır | İmza doğrulanamadı — yeniden gönderilsin de değişmez |
| **403 Forbidden** | Terminal | Hayır | Yetki yok |
| **404 Not Found** | Terminal | Hayır | Endpoint yok — yeniden gönderilsin de değişmez |
| **408 Request Timeout** | Geçici | **Evet** | Sunucu zaman aşımı — yeniden dene |
| **410 Gone** | Terminal | Hayır | Kalıcı olarak kaldırıldı — WebSub Rec aboneliği düşürmeyi söyler |
| **429 Too Many Requests** | Geçici | **Evet** | Rate limit — yeniden dene, Retry-After başlığını dinle |
| **5xx** | Geçici | **Evet** | Sunucu hatası — yeniden dene |

**Doğrulama kuralı**: Durum kodunu sınıflandır, yeniden gönderim kararını buna göre ver.
Draft: "408, 429, and 5xx indicate transient failures and MAY be retried" [E6].

---

## §8 CI Kapısı — Doğrulama Testleri

CI pipeline'ında şu testler **zorunlu**:

| # | Test | Kaynak | Hata durumunda |
|---|------|--------|----------------|
| 1 | GitHub test vektörü | [E3] | Deploy durdur |
| 2 | İmza hesabı + timing-safe karşılaştırma | [E1][E14] | Deploy durdur |
| 3 | Timestamp penceresi (eski + yeni) | [E4][E5] | Deploy durdur |
| 4 | Replay saldırısı (aynı paket 2×) | [E4] | Deploy durdur |
| 5 | İdempotency (aynı key 2×) | [E6] | Deploy durdur |
| 6 | Backoff jitter deterministik değil | [E7] | Deploy durdur |
| 7 | HTTP durum kodu sınıflandırma | [E6][E11] | Deploy durdur |
| 8 | CloudEvents zorunlu alanlar | [E8] | Deploy durdur |
| 9 | WebSub hub.challenge GET | [E9] | Deploy durdur |
| 10 | Body bütünlüğü (ham byte) | [E3][E4] | Deploy durdur |

**CI entegrasyonu**: `openapi-dogrulama.md` §10'daki 6 gate (S0-S5) + bu 10 test = **16 gate**.

---

## §9 Sızıntı Önleme — Doğrulama Logları

**Yasak** (log'a asla yazılmaz):
- Gizli anahtar (secret)
- Ham imza (hex)
- HMAC anahtarı
- İmza karşılaştırma sonucunun detayı (hangi byte farklı?)

**İzinli** (loglanabilir):
- Event ID (CloudEvents `id`)
- Timestamp
- HTTP durum kodu
- Hata sınıfı (örn. "imza doğrulanamadı")
- Retry sayacı

**Örnek güvenli log**:
```
[2026-10-16T12:00:00Z] WARN webhook_imza_basarisiz event_id=evt_123 status=401 reason=imza_eslesmedi
```
(Ham imza, secret, veya byte farkı **yok**.)

**Örnek güvensiz log** (YASAK):
```
[2026-10-16T12:00:00Z] WARN beklenen=sha256=757107ea... alinan=sha256=a3f2b1... secret=whsec_abc123
```

---

## §10 Terim Sözlüğü

| Terim | Anlamı |
|-------|--------|
| **Timing-safe karşılaştırma** | Karşılaştırma, byte uzunluğu ve içerik sabit zamanlı — zamanlama saldırısı önlenir [E14] |
| **Replay saldırısı** | Saldırgan eski imzalı paketi tekrar gönderir — timestamp penceresi önler [E4] |
| **HMAC** | Hash-based Message Authentication Code — anahtar + mesaj → imza [E1] |
| **SHA-256** | 256-bit hash algoritması — imza hesaplamada kullanılır [E2] |
| **Idempotency** | Aynı işlemin ikinci kez çağrılması etki yaratmaz [E6] |
| **TTL** | Time To Live — kayıt ne kadar yaşar (idempotency: 24 saat) [E6] |
| **Jitter** | Rastgelelik — backoff delay'ine eklenir, thundering önler [E7] |
| **Terminal hata** | Yeniden gönderimle çözülmeyen hata (400, 401, 404) [E6] |
| **Geçici hata** | Yeniden gönderimle çözülebilen hata (408, 429, 5xx) [E6] |
| **Zorunlu alan** | CloudEvents: `id`/`source`/`specversion`/`type` [E8] |
| **Niyet doğrulama** | WebSub: `hub.challenge` GET ile geri gönderilir [E9] |
| **RFC 3339** | Timestamp biçimi: `YYYY-MM-DDTHH:MM:SS.nnnnnnnnn+HH:MM` [E10] |

---

## §Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|-------|-----|-------|-------|
| E1 | HMAC — hash.hmac (RFC 2104) | `https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/sign` (Web Crypto) + `https://docs.python.org/3/library/hmac.html` | 2026-10-16 | ✅ Doğrulandı |
| E2 | SHA-2 — sha256 imza algoritması | `https://github.com/asdfjkl/hashlib-sha224/blob/master/SHA224_256_384_512.pdf` (RFC 6234) | 2026-10-16 | ✅ Doğrulandı |
| E3 | GitHub — test vektörü | `https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` | 2026-10-16 | ✅ Doğrulandı |
| E4 | Stripe — timestamp tolerance | `https://docs.stripe.com/webhooks#verify` | 2026-10-16 | ✅ Doğrulandı |
| E5 | Slack — timing-safe karşılaştırma | `https://docs.slack.dev/authentication/verifying-requests-from-slack` | 2026-10-16 | ✅ Doğrulandı |
| E6 | Draft — idempotency + 24 saat | `https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` | 2026-10-16 | ✅ Doğrulandı |
| E7 | Draft — tam jitter backoff | `https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` | 2026-10-16 | ✅ Doğrulandı |
| E8 | CloudEvents — zorunlu alanlar | `https://raw.githubusercontent.com/cloudevents/spec/v1.0.2/cloudevents/spec.md` | 2026-10-16 | ✅ Doğrulandı |
| E9 | WebSub — hub.challenge | `https://www.w3.org/TR/websub/` | 2026-06-02 | ✅ Doğrulandı |
| E10 | RFC 3339 — timestamp biçimi | `https://www.rfc-editor.org/rfc/rfc3339` | 2026-10-16 | ✅ Doğrulandı |
| E11 | RFC 9110 — HTTP durum kodları | `https://www.rfc-editor.org/rfc/rfc9110` | 2026-10-16 | ✅ Doğrulandı |
| E12 | RFC 9421 — HTTP mesaj imzalama | `https://www.rfc-editor.org/rfc/rfc9421` | 2026-10-16 | ✅ Doğrulandı |
| E13 | WebSub — X-Hub-Signature | `https://www.w3.org/TR/websub/` | 2026-06-02 | ✅ Doğrulandı |
| E14 | Standard Webhooks — sabit zamanlı karşılaştırma | `https://www.standardwebhooks.com/` | 2026-10-16 | ✅ Doğrulandı |
| E15 | RFC 8594 — Sunset başlığı | `https://www.rfc-editor.org/rfc/rfc8594` | 2026-10-16 | ✅ Doğrulandı (düzlem dışı, bkz event-anayasa §15 Q6) |

**15/15 kaynak doğrulandı** (2026-10-16).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
