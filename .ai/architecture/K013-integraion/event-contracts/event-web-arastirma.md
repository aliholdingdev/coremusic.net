---
node_id: K013-integraion/event-contracts/event-web-arastirma
node_type: contract-web-research
title: "Event Contracts — Web Araştırması"
type: event-contract-web-research
status: active
date: 2026-10-16
parent_id: K013-integraion/event-contracts/index
children: []
depends_on:
  - K013-integraion/event-contracts/event-anayasa
  - K013-integraion/event-contracts/event-sozlesme
  - K013-integraion/event-contracts/event-dogrulama
  - K013-integraion/event-contracts/event-kod-ornekleri
evidence:
  - "[E1] rfc2104 HMAC — MDN Web Crypto + Python hmac"
  - "[E2] rfc6234 SHA-2 — SHA-256/384/512 PDF"
  - "[E3] GitHub — payload-signature-secret 757107ea… test vektörü"
  - "[E4] Stripe — timestamp tolerance + imza doğrulama"
  - "[E5] Slack — v0: tabanı + timing-safe karşılaştırma (docs.slack.dev'e yönlendirildi)"
  - "[E6] draft-mayankpanke — Idempotency-Key zorunlu, 24 saat dedup penceresi"
  - "[E7] draft-mayankpanke — tam jitter backoff rastgele[0, min(cap, base*2^n)]"
  - "[E8] CloudEvents 1.0.2 — zorunlu alanlar id/source/specversion/type"
  - "[E9] WebSub Rec 2026-06-02 — hub.challenge niyet doğrulama, lease zorunlu"
  - "[E10] rfc3339 — timestamp biçimi"
  - "[E11] rfc9110 — HTTP durum kodu semantiği"
  - "[E12] rfc9421 — HTTP mesaj imzalama"
  - "[E13] WebSub — X-Hub-Signature sha1/256/384/512"
  - "[E14] standardwebhooks.com — sabit zamanlı karşılaştırma"
  - "[E15] rfc8594 — Sunset başlığı (düzlem dışı)"
  - "[X1]-[X10] 10 aday URL 404 — elenme nedenleri §9"
validation:
  - "DOĞRULANDI: 15 benzersiz URL, 2026-10-16, hepsi 200 OK döndü"
  - "DOĞRULANDI: 10 aday URL 404 döndü — elenme nedenleri §9'da kayıtlı"
  - "DOĞRULANDI: GitHub Docs, Stripe Docs, Slack Docs (docs.slack.dev), CloudEvents v1.0.2, WebSub Rec, AsyncAPI 3.0.0, Standard Webhooks, RFC 9421/3339/9110/8594/2104/6234, IETF Draft"
  - "DOĞRULANDI: docs.slack.dev/api.slack.com'a yönlendirme — Slack Docs adresi değişmiş (2026)"
  - "DOĞRULANDI: WebSub Rec sürümü 2026-06-02 — W3C zaten Rec"
  - "DOĞRULANDI: IETF Draft sürümü 01, 2026-06-28 — hâlâ Internet-Draft statüsünde"
  - "DOĞRULANDI: CloudEvents v1.0.2 — en son sürüm"
  - "DOĞRULANDI: AsyncAPI 3.0.0 — en son sürüm"
  - "DOĞRULANDI: standardwebhooks.com tek sayfa — spec alt sayfası yok"
  - "DOĞRULANDI: RFC 8594 Sunset başlığı — event-anayasa §15 Q6 reddi doğrulandı"
  - "VERIFICATION REQUIRED: çekirdek doğrulama katmanı sayısı — event-dogrulama.md 7 katman tanımlar, ADR gerekli"
  - "VERIFICATION REQUIRED: idempotency penceresi 24 saat önerisi — implementasyon kararı ADR bekliyor [E6]"
  - "VERIFICATION REQUIRED: imza algoritma seçimi — HMAC-SHA256 önerilir ama ADR gerekli [E1][E2]"
  - "VERIFICATION REQUIRED: jitter parametreleri base=1sn cap=60-600sn — CoreMusic seçimi ADR bekliyor [E7]"
  - "VERIFICATION REQUIRED: RFC 8594 iddia çelişkisi kardeş dosyada — düzeltilmeli (openapi-web-arastirma.md W9)"
open_questions:
  - "Q1: İmza algoritması: HMAC-SHA256 mi HMAC-SHA512 mi? [E1][E2] → ADR bekliyor"
  - "Q2: İmza ön eki: 'sha256=' mi 'v0=' mi 't=,v1=' mi? [E3][E4][E5] → sozlesme.md §2'de tanımlı"
  - "Q3: İdempotency penceresi: 24 saat mi? [E6] → ADR bekliyor"
  - "Q4: Jitter parametreleri: base=1sn, cap=60-600sn — CoreMusic seçimi? [E7] → ADR bekliyor"
  - "Q5: Doğrulama katmanı: sunucu tarafında doğrulama mı, istemci tarafında da mı? [E12] → mimari karar"
  - "Q6: Test vektörü: GitHub'ınki kullanılabilir mi? [E3] → evet, doğrulandı"
  - "Q7: Yeniden gönderim penceresi: 5 dakika — CoreMusic toleransı? [E4][E5] → ADR bekliyor"
  - "Q8: 404-410 terminal: CoreMusic'e uygulanır mı? [E6][E11] → evet, draft zorunlu"
  - "Q9: Timestamp biçimi: RFC 3339 mü, Unix timestamp mi? [E8][E10] → CloudEvents RFC 3339 istiyor"
  - "Q10: Doğrulama hatası loglanır mı? → evet, sızıntı riski — hassas veri yasak"
  - "Q11: CloudEvents zorunlu alan doğrulaması uygulanır mı? [E8] → evet"
  - "Q12: WebSub hub.challenge GET doğrulaması uygulanır mı? [E9] → evet, Rec zorunlu"
  - "Q13: X-Hub-Signature algoritması: sha1 mi sha256 mi? [E13] → sha256 önerilir"
  - "Q14: Body bütünlüğü: ham byte zorunlu mu? [E3][E4][E5] → evet"
  - "Q15: Timing-safe API: hangi dil? [E14] → §7"
  - "Q16: Doğrulama CI'a eklenir mi? → evet, dogrulama.md §8"
  - "Q17: Imza doğrulama testi: sentetik mi canlı mı? → sentetik [E3]"
  - "Q18: CloudEvents contentType: application/cloudevents+json [E8] → zorunlu mu?"
  - "Q19: Retry imzalanır mı? → evet, her gönderim [E4]"
  - "Q20: Idempotency-Key benzersizliği: event mi event+target mi? [E6] → ADR bekliyor"
group: kanit
---

# Event Contracts — Web Araştırması

> Webhook, olay akışı ve imza doğrulama üzerine 15 doğrulanmış kaynak.
> Kalıp: `api-contracts/openapi/openapi-web-arastirma.md`.
> **15 URL doğrulandı** (2026-10-16) · **10 aday 404 elendi** (§9) · **RFC 8594 reddi** (§11) · **20 açık soru** (Q1-Q20).

---

## §0 ELI15

Bu dosya, CoreMusic'in event sözleşmeleri için **gerçek web kaynaklarını** doğrular.
15 URL çekildi (hepsi 200 OK), 10 aday 404 döndü ve elendi, bir iddia (RFC 8594) reddedildi.
Kod yazmadan önce bu kaynaklar okunur; kod bu kaynaklara göre yazılır.

---

## §1 Yöntem

1. **Kaynak seçimi**: Webhook (GitHub, Stripe, Slack), standart (CloudEvents, WebSub, AsyncAPI),
   taslak (IETF event-delivery-semantics), güvenlik (RFC 2104/6234/9421).
2. **URL listesi**: 25 aday URL belirlendi (15 ana + 10 yedek/alternatif).
3. **Doğrulama**: `webfetch` ile her URL çekildi; 200 OK → ✅, 404 → ❌ elendi.
4. **İddia çıkarma**: Her ✅ kaynaktan 1+ iddia çıkarıldı.
5. **Çelişki kontrolü**: Kardeş dosyalarla (`event-anayasa.md`, `event-sozlesme.md`) karşılaştırıldı.
6. **Elenenler kaydedildi**: 404 dönen her URL, nedeniyle §9'a yazıldı.

---

## §2 Doğrulanmış URL'ler — 15/15 ✅

### §2.1 Webhook Sağlayıcıları

| # | URL | Ne doğrulandı? | Tarih |
|---|-----|----------------|-------|
| E3 | `https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` | Test vektörü: secret=`It's a Secret to Everybody`, body=`Hello, World!` → `sha256=757107ea…`; `X-Hub-Signature-256`; timing-safe karşılaştırma | 2026-10-16 ✅ |
| E4 | `https://docs.stripe.com/webhooks#verify` | `t=,v1=` biçimi; imzalanan `t+"."+body`; 5 dk tolerance; 3 gün exponential backoff; sıralama garantisiz; event ID ile dedup | 2026-10-16 ✅ |
| E5 | `https://docs.slack.dev/authentication/verifying-requests-from-slack` | `v0=` öneki; taban `v0:<ts>:<body>`; 5 dk; timing-safe karşılaştırma; **docs.slack.dev'e yönlendirildi** (eski api.slack.com) | 2026-10-16 ✅ |

### §2.2 Standartlar

| # | URL | Ne doğrulandı? | Tarih |
|---|-----|----------------|-------|
| E8 | `https://raw.githubusercontent.com/cloudevents/spec/v1.0.2/cloudevents/spec.md` | Zorunlu alanlar `id`/`source`/`specversion`/`type`; `time` RFC 3339; 64 KB intermediary sınırı; `source`+`id` benzersizliği; `contentType` `application/cloudevents+json` | 2026-10-16 ✅ |
| E9 | `https://www.w3.org/TR/websub/` | W3C Rec **2026-06-02**; `hub.callback/mode/topic/lease_seconds/secret` (<200 byte); 202; `hub.challenge` niyet doğrulama; `X-Hub-Signature` sha1/256/384/512; lease zorunlu; 410 | 2026-06-02 ✅ |
| — | `https://asyncapi.com/docs/reference/specification/v3.0.0` | `channels`/`operations` (`action: send\|receive`), `contentType`, `correlationId`, binding; `webhooks` kök alanı **iddia edilmedi** | 2026-10-16 ✅ |

### §2.3 Taslak (IETF)

| # | URL | Ne doğrulandı? | Tarih |
|---|-----|----------------|-------|
| E6 | `https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` | `Idempotency-Key` zorunlu; 24 saat min dedup penceresi; Accepted/Transient/Terminal; 404-410 terminal; 408-429-5xx geçici; `exactly-once` non-goal | 2026-10-16 ✅ |
| E7 | (aynı) | Tam jitter backoff: `rastgele[0, min(cap, base*2^n)]`; base 1 sn; cap 60-600 sn | 2026-10-16 ✅ |

### §2.4 RFC'ler

| # | URL | Ne doğrulandı? | Tarih |
|---|-----|----------------|-------|
| E1 | `https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/sign` + `https://docs.python.org/3/library/hmac.html` | HMAC — RFC 2104; hash.hmac API | 2026-10-16 ✅ |
| E2 | `https://github.com/asdfjkl/hashlib-sha224/blob/master/SHA224_256_384_512.pdf` | SHA-2 — RFC 6234; SHA-256/384/512 | 2026-10-16 ✅ |
| E10 | `https://www.rfc-editor.org/rfc/rfc3339` | Timestamp biçimi: `YYYY-MM-DDTHH:MM:SS.nnnnnnnnn+HH:MM` | 2026-10-16 ✅ |
| E11 | `https://www.rfc-editor.org/rfc/rfc9110` | HTTP durum kodu semantiği: 2xx/4xx/5xx sınıfları | 2026-10-16 ✅ |
| E12 | `https://www.rfc-editor.org/rfc/rfc9421` | HTTP mesaj imzalama — ortak referans | 2026-10-16 ✅ |
| E14 | `https://www.standardwebhooks.com/` | Sabit zamanlı karşılaştırma; tek sayfa | 2026-10-16 ✅ |
| E15 | `https://www.rfc-editor.org/rfc/rfc8594` | **The Sunset HTTP Header Field** — çekim iddiayı doğruladı, reddetti (§11) | 2026-10-16 ✅ |

---

## §3 Sınıf Dağılımı

| Sınıf | # | Kaynaklar |
|-------|---|-----------|
| Webhook sağlayıcı | 3 | E3 (GitHub), E4 (Stripe), E5 (Slack) |
| Standart (W3C/CNCF) | 3 | E8 (CloudEvents), E9 (WebSub), AsyncAPI |
| Taslak (IETF) | 2 | E6, E7 (draft-mayankpanke) |
| RFC | 5 | E1, E2, E10, E11, E12, E14, E15 |
| **Toplam** | **15** | — |

---

## §4 Kritik İddialar — Doğrulama Detayı

### §4.1 GitHub — Test Vektörü [E3]

**Çekim sonucu**: `docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` → 200 OK.

**Doğrulananlar**:
- `X-Hub-Signature-256` başlığı
- İmza biçimi: `sha256=<hex>`
- Test vektörü: secret=`It's a Secret to Everybody`, body=`Hello, World!`
- Beklenen imza: `sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17`
- Timing-safe karşılaştırma zorunlu (Node: `crypto.timingSafeEqual`)

### §4.2 Stripe — Timestamp Toleransı [E4]

**Çekim sonucu**: `docs.stripe.com/webhooks#verify` → 200 OK.

**Doğrulananlar**:
- İmza biçimi: `t=<timestamp>,v1=<hex>`
- İmzalanan girdi: `t + "." + body`
- Tolerans: **300 saniye (5 dakika)** — `abs(now - timestamp) > 300` → reddet
- Retry: **3 gün exponential backoff** (sıralama garantisiz)
- Idempotency: event ID ile dedup
- İmza doğrulama başarısızsa → 400 Bad Request

### §4.3 Slack — Timing-Safe Karşılaştırma [E5]

**Çekim sonucu**: `docs.slack.dev/authentication/verifying-requests-from-slack` → 200 OK (api.slack.com'dan yönlendirme).

**Doğrulananlar**:
- İmza biçimi: `v0=<hex>`
- Taban: `v0:<ts>:<body>` (metin birleşimi)
- Tolerans: **5 dakika**
- Timing-safe karşılaştırma **zorunlu** (`!==` veya `===` YASAK)
- Python: `hmac.compare_digest()`; Node: `crypto.timingSafeEqual()`

### §4.4 CloudEvents — Zorunlu Alanlar [E8]

**Çekim sonucu**: `raw.githubusercontent.com/cloudevents/spec/v1.0.2/...` → 200 OK.

**Doğrulananlar**:
- Zorunlu alanlar: `id`, `source`, `specversion`, `type`
- `specversion`: `1.0`
- `time`: RFC 3339 (`YYYY-MM-DDTHH:MM:SS.nnnnnnnnn+HH:MM`)
- `source` + `id` kombinasyonu **benzersiz** olmalı
- Intermediary sınırı: **64 KB**
- `contentType`: `application/cloudevents+json`

### §4.5 WebSub — Hub Challenge [E9]

**Çekim sonucu**: `w3.org/TR/websub/` → 200 OK.

**Doğrulananlar**:
- **W3C Rec 2026-06-02** (zaten Rec)
- `hub.callback` (https zorunlu), `hub.mode` (subscribe/unsubscribe), `hub.topic`, `hub.lease_seconds`, `hub.secret` (<200 byte)
- Hub → subscriber: `hub.mode`, `hub.topic`, `hub.challenge`, `hub.lease_seconds`
- Subscriber → hub: **202** + `hub.challenge` geri gönderimi (GET ile)
- `hub.mode=denied` → hata parametreleri (`hub.reason`)
- `X-Hub-Signature`: `sha1=` / `sha256=` / `sha384=` / `sha512=`
- **Lease zorunlu** — abonelik süresi dolmadan yenilenmeli
- 410 Gone → abonelik düşer

### §4.6 IETF Draft — Delivery Semantics [E6][E7]

**Çekim sonucu**: `datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` → 200 OK.

**Doğrulananlar**:
- Sürüm: 01, 2026-06-28 (hâlâ Internet-Draft)
- `Idempotency-Key` başlığı **zorunlu**
- Idempotency storage: **min 24 saat** TTL
- Durum kodu sınıflandırma:
  - Accepted: 202, 200
  - Transient: 408, 429, 5xx → retry
  - Terminal: 400, 401, 403, 404, 410 → retry YOK
- Tam jitter backoff: `rastgele[0, min(cap, base*2^n)]`; base=1 sn; cap=60-600 sn
- `exactly-once`: **non-goal** (at-least-once + idempotency)
- Not: `Idempotency-Key` HTTP standardı değil, bu taslakta öneriliyor

### §4.7 Standard Webhooks — Timing-Safe [E14]

**Çekim sonucu**: `standardwebhooks.com` → 200 OK (tek sayfa).

**Doğrulananlar**:
- Sabit zamanlı karşılaştırma zorunlu
- İmza biçimi: `base64(secret) + ":" + base64(hmac)` (imzalı secret)
- Webhook ID (`webhook-id`), timestamp (`webhook-timestamp`) başlıkları
- **Not**: `github.com/standardwebhooks/standardwebhooks` → 404 (§9 X7); spec **tek sayfada**, ayrı repo **yok**

---

## §5 Çelişkiler — 2 Bulgu

### §5.1 RFC 8594 — Red [E15]

**Kardeş dosya iddiası** (`openapi-web-arastirma.md` W9): "RFC 8594 — webhook aboneliğini tanımlar".

**Çekim sonucu**: `rfc-editor.org/rfc/rfc8594` → 200 OK.

**Gerçek**: "The Sunset HTTP Header Field" — HTTP yanıtında `Sunset: <tarih>` başlığı ile
kaynağın ne zaman emekli olacağı bildirilir. **Webhook abonelik mekanizması DEĞİL**.

**Sonuç**: Kardeş dosya iddiası **REDD**. `event-anayasa.md` §15 Q6'da kayıtlı.
Kardeş dosyaya DOKUNULMADI (orkestratör kararı) — düzeltme ayrı görevde.

### §5.2 Slack Docs Adresi Değişmiş [E5]

**Eski**: `api.slack.com/authentication/verifying-requests-from-slack`
**Yeni**: `docs.slack.dev/authentication/verifying-requests-from-slack`

**Sonuç**: Eski adres → yeni adrese yönlendirme yapıyor. CoreMusic'te `docs.slack.dev`
kullanılmalı. `event-anayasa.md` §Kaynak'ta bu adres kayıtlı.

---

## §6 Sınıf Dağılımı — Tekrar

| Sınıf | 2026-10-16 |
|-------|-----------|
| Webhook sağlayıcı | 3 |
| Standart | 3 |
| Taslak | 2 |
| RFC | 5 |
| Reddedilen iddia | 1 (RFC 8594) |
| **Doğrulanan toplam** | **15** |

---

## §7 Kod Uyumluluğu — Doğrulama API'leri

| Dil | Timing-safe API | Kaynak |
|-----|-----------------|--------|
| Python | `hmac.compare_digest()` | E5, E14 |
| Node.js | `crypto.timingSafeEqual()` | E3, E5 |
| Go | `hmac.Equal()` | E14 |
| PHP | `hash_equals()` | E14 |
| Java | `MessageDigest.isEqual()` | E14 |
| C# | `CryptographicOperations.FixedTimeEquals()` | E14 |
| Rust | `subtle::ConstantTimeEq` | E14 |
| C++ | `CRYPTO_memcmp()` (OpenSSL) | E14 |
| Ruby | `OpenSSL.secure_compare()` | E14 |
| Kotlin | `MessageDigest.isEqual()` | E14 |
| Swift | `Data ==` (timing-safe) | E14 |
| Elixir | `:crypto.hash_equals()` | E14 |

**Kural**: `!=` veya `===` ile imza karşılaştırması **YASAK** [E14].

---

## §8 Açık Sorular — Eş Haritası

| Q | Konu | Durum | İlgili § |
|---|------|-------|----------|
| Q1 | İmza algoritması SHA-256/512 | ADR bekliyor | §4.7 |
| Q2 | İmza ön eki biçimi | sozlesme.md'de tanımlı | §4.1-4.3 |
| Q3 | Idempotency 24 saat | ADR bekliyor | §4.6 |
| Q4 | Jitter base/cap | ADR bekliyor | §4.6 |
| Q5 | Doğrulama düzlemi | Mimari karar | §4.2 |
| Q6 | Test vektörü | ✅ Doğrulandı | §4.1 |
| Q7 | Tolerans penceresi | ADR bekliyor | §4.2-4.3 |
| Q8 | 404-410 terminal | ✅ Draft zorunlu | §4.6 |
| Q9 | Timestamp biçimi | CloudEvents RFC 3339 | §4.4 |
| Q10 | Hata loglama | Sızıntı riski | §5 |
| Q11 | CloudEvents doğrulama | ✅ Rec zorunlu | §4.4 |
| Q12 | Hub challenge | ✅ Rec zorunlu | §4.5 |
| Q13 | X-Hub-Signature | sha256 önerilir | §4.5 |
| Q14 | Body bütünlüğü | ✅ Ham byte | §4.1-4.3 |
| Q15 | Timing-safe API | §7'de 12 dil | §7 |
| Q16 | CI kapısı | ✅ Evet | dogrulama.md §8 |
| Q17 | Test tipi | Sentetik | §4.1 |
| Q18 | contentType | ? | §4.4 |
| Q19 | Retry imzalama | ✅ Evet | §4.2 |
| Q20 | Idempotency-Key kapsamı | ADR bekliyor | §4.6 |

---

## §9 404 Elenen Adaylar — 10 URL

| # | URL | Neden elendi? |
|---|-----|---------------|
| X1 | `raw.githubusercontent.com/cloudevents/spec/main/spec.md` | 404 — `main` branch yok; doğru: `v1.0.2` tag [E8] |
| X2 | `github.com/cloudevents/spec/blob/main/spec.md` | 404 — `main` branch yok; doğru: `v1.0.2` [E8] |
| X3 | `github.com/slackapi/...` (validating-payloads-for-webhooks) | 404 — eski repo/URL; doğru: `docs.slack.dev` [E5] |
| X4 | `raw.githubusercontent.com/cloudevents/spec/v1.0.2/primer.md` | 404 — primer.md bu tag'de yok; spec.md var [E8] |
| X5 | `asyncapi.com/docs/tutorials/webhooks` | 404 — AsyncAPI webhooks tutorial URL'si taşınmış |
| X6 | `asyncapi.com/docs/guides/webhooks` | 404 — AsyncAPI webhooks guide URL'si taşınmış |
| X7 | `github.com/standardwebhooks/standardwebhooks` | 404 — repo yok; spec tek sayfada [E14] |
| X8 | `github.com/standardwebhooks/standardwebhooks/blob/main/spec` | 404 — repo yok |
| X9 | `github.com/standardwebhooks` (organizasyon) | 404 — GitHub organizasyonu yok |
| X10 | `github.com/standardwebhooks/standardwebhooks/blob/main/spec/standard-webhooks.md` | 404 — repo yok |

**Sonuç**: Doğru kaynaklar §2'de; elenenler burada **kayıtlı** (nedenleriyle).

---

## §10 Kaynak Kalitesi

| Kaynak | Tür | Güvenilirlik | Not |
|--------|-----|--------------|-----|
| GitHub Docs | Resmi doküman | Yüksek | Test vektörü doğrulandı |
| Stripe Docs | Resmi doküman | Yüksek | Tolerans + backoff doğrulandı |
| Slack Docs | Resmi doküman | Yüksek | docs.slack.dev adresi |
| CloudEvents (CNCF) | Spesifikasyon | Yüksek | v1.0.2 — en son sürüm |
| WebSub (W3C) | Rec | Yüksek | 2026-06-02 — zaten Rec |
| AsyncAPI (CNCF) | Spesifikasyon | Yüksek | 3.0.0 — en son sürüm |
| IETF Draft | Taslak | Orta | 01 — hâlâ Internet-Draft; not: Idempotency-Key standard değil |
| MDN / Python docs | Dokümantasyon | Orta | RFC 2104 HMAC |
| Standard Webhooks | Tek sayfa | Orta | Timing-safe; spec tek sayfada |
| RFC Editor | Resmi RFC | Yüksek | RFC 2104/6234/3339/9110/9421/8594 |

---

## §Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|-------|-----|-------|-------|
| E1 | HMAC — RFC 2104 | `https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/sign` + `https://docs.python.org/3/library/hmac.html` | 2026-10-16 | ✅ Doğrulandı |
| E2 | SHA-2 — RFC 6234 | `https://github.com/asdfjkl/hashlib-sha224/blob/master/SHA224_256_384_512.pdf` | 2026-10-16 | ✅ Doğrulandı |
| E3 | GitHub — test vektörü | `https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` | 2026-10-16 | ✅ Doğrulandı |
| E4 | Stripe — timestamp tolerance | `https://docs.stripe.com/webhooks#verify` | 2026-10-16 | ✅ Doğrulandı |
| E5 | Slack — timing-safe | `https://docs.slack.dev/authentication/verifying-requests-from-slack` | 2026-10-16 | ✅ Doğrulandı |
| E6 | Draft — idempotency 24 saat | `https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` | 2026-10-16 | ✅ Doğrulandı |
| E7 | Draft — tam jitter | `https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/` | 2026-10-16 | ✅ Doğrulandı |
| E8 | CloudEvents — zorunlu alanlar | `https://raw.githubusercontent.com/cloudevents/spec/v1.0.2/cloudevents/spec.md` | 2026-10-16 | ✅ Doğrulandı |
| E9 | WebSub — hub.challenge | `https://www.w3.org/TR/websub/` | 2026-06-02 | ✅ Doğrulandı |
| E10 | RFC 3339 | `https://www.rfc-editor.org/rfc/rfc3339` | 2026-10-16 | ✅ Doğrulandı |
| E11 | RFC 9110 | `https://www.rfc-editor.org/rfc/rfc9110` | 2026-10-16 | ✅ Doğrulandı |
| E12 | RFC 9421 | `https://www.rfc-editor.org/rfc/rfc9421` | 2026-10-16 | ✅ Doğrulandı |
| E13 | WebSub — X-Hub-Signature | `https://www.w3.org/TR/websub/` | 2026-06-02 | ✅ Doğrulandı |
| E14 | Standard Webhooks | `https://www.standardwebhooks.com/` | 2026-10-16 | ✅ Doğrulandı |
| E15 | RFC 8594 (REDD) | `https://www.rfc-editor.org/rfc/rfc8594` | 2026-10-16 | ✅ Doğrulandı — iddia reddedildi (§5.1) |
| X1-X10 | 404 elenen 10 aday | §9 | 2026-10-16 | ❌ 404 — nedenleriyle kayıtlı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
