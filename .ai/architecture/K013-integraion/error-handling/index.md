---
node_id: K013-integraion/error-handling
node_type: subdomain
status: draft
parent_id: K013-integraion
children:
  - K013-integraion/error-handling/error-anayasa
  - K013-integraion/error-handling/error-sozlesme
  - K013-integraion/error-handling/error-dogrulama
  - K013-integraion/error-handling/error-kod-ornekleri
  - K013-integraion/error-handling/error-web-arastirma
depends_on:
  - K013-integraion
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16)"
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
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
  - "Kademe-3 genişletmesi (5 dosya) 2026-10-16'da üretildi — 13 resmi kaynak fetch ile doğrulandı (error-web-arastirma.md §3)"
  - "Retry/rate-limit parametreleri yalnız kaynaklıdır (AWS SDK defaultları referans) — uydurma değer yok"
  - "RFC 9457 → RFC 7807 obsoletes doğrulandı (2026-10-16)"
  - "Gözleme (K017) ve politika/kripto (K009) sınırları tüm dosyalarda etiketli"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Rate limit / retry politika değerleri → ⚠️ UNKNOWN (CoreMusic'e özgü max_attempts/base/cap kararı yok)"
  - "Circuit breaker eşikleri → ⚠️ UNKNOWN"
  - "Gözleme aksiyonu sahipliği K017 mi? → ⚠️ VERIFICATION REQUIRED"
  - "Problem Details type URI şeması → ⚠️ UNKNOWN"
  - "DLQ kuyruk altyapısı → ⚠️ UNKNOWN"
group: mimari
---


# error-handling — K013-integraion (kademe-2 · subdomain)

**ELI15:** Dış çağrılarda hata sınıflandırma, tekrar-dene ve hız/rate sınırı yönetimi.

**Sınır (ne DEĞİL):** Güvenlik politikası K009-security · olay yayını K012/K017'ye ait olabilir → ⚠️ · gözleme aksiyonu (metrik/alarm/dashboard) K017 sınırında → etiketli.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** kademe-3 (5 dosya) üretildi (2026-10-16) · kod yüzeyi PLANNED (CoreMusic implementasyonu depoda tespit edilmedi) · kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `error-anayasa.md` | Hata sınıflandırma eksenleri, mekanizma seçimi (retry/backoff/jitter, circuit breaker, rate limit, timeout bütçesi, DLQ), sınır | Kademe-3 |
| mimari | `error-sozlesme.md` | Problem Details (RFC 9457) gövde şeması, dış API → iç hata eşleme tablosu, sınıf sözleşmesi | Kademe-3 |
| kod | `error-dogrulama.md` | 9 doğrulama kapısı (D-1..D-9), test senaryoları, ölçüm | Kademe-3 |
| kod | `error-kod-ornekleri.md` | 13 dilde retry + exponential backoff + jitter örneği (clock soyutlamalı) | Kademe-3 |
| kanit | `error-web-arastirma.md` | 13 benzersiz resmi kaynak fetch doğrulama raporu | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Rate limit / retry politika değerleri → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi (module katmanı) | ADR-097 K4 faz (C) bekliyor |
| 4 | Circuit breaker eşikleri → ⚠️ UNKNOWN | FM open_questions |
| 5 | Gözleme aksiyonu sahipliği (K017) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 6 | Problem Details type URI şeması → ⚠️ UNKNOWN | FM open_questions |
| 7 | DLQ kuyruk altyapısı → ⚠️ UNKNOWN | FM open_questions |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
