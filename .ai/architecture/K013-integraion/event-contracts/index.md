---
node_id: K013-integraion/event-contracts/index
node_type: architecture
title: "Event Contracts — İndeks"
type: event-contract-index
status: active
date: 2026-10-16
parent_id: K013-integraion/index
children:
  - K013-integraion/event-contracts/event-anayasa
  - K013-integraion/event-contracts/event-sozlesme
  - K013-integraion/event-contracts/event-dogrulama
  - K013-integraion/event-contracts/event-kod-ornekleri
  - K013-integraion/event-contracts/event-web-arastirma
depends_on:
  - K013-integraion/api-contracts/openapi/index
evidence:
  - "5 dosya üretildi (2026-10-16): event-anayasa / event-sozlesme / event-dogrulama / event-kod-ornekleri / event-web-arastirma"
  - "event-web-arastirma: 15 doğrulanmış URL (2026-10-16), 10 aday 404 elendi (nedenleriyle), 1 iddia reddedildi (RFC 8594)"
  - "event-anayasa: roller, CloudEvents envelope, teslim semantiği, idempotency, imza düzlemi, WebSub abonelik, webhook-vs-akış §9"
  - "event-sozlesme: alan sözleşmesi, HTTP bağlantısı POST/Idempotency-Key, imza başlıkları, durum kodu eşlemesi, retry/tam-jitter, WebSub abonelik, AsyncAPI kanal"
  - "event-dogrulama: 7 doğrulama katmanı, GitHub test vektörü sha256=757107ea…, timing-safe, timestamp penceresi, idempotency dedup, backoff jitter, CI kapısı 10 test"
  - "event-kod-ornekleri: 13 dil (PHP 8.4, JS, TS, Python, Go, Java, C#, Rust, C++, Ruby, Kotlin, Swift, Elixir), Kanıt A/B/C ayrımı, test vektörleri"
  - "15 doğrulanmış kaynak: rfc2104/6234/3339/9110/9421/8594, CloudEvents v1.0.2, WebSub Rec 2026-06-02, AsyncAPI 3.0.0, GitHub/Stripe/Slack Docs, standardwebhooks, IETF draft-mayankpanke"
validation:
  - "DOĞRULANDI: 5/5 dosya yazıldı, her biri ≥350 satır, §GRUPLAR + §Kaynak/§A + alt bilgi içerir"
  - "DOĞRULANDI: frontmatter alanları (node_id/node_type/title/type/status/date/parent_id/children/depends_on/evidence/validation/open_questions/group) 5/5 dosyada mevcut"
  - "DOĞRULANDI: node_id biçimi K013-integraion/event-contracts/<dosya-adı> — 'integraion' imla dokunulmaz"
  - "DOĞRULANDI: 15 URL 2026-10-16 webfetch ile çekildi (200 OK)"
  - "DOĞRULANDI: 10 aday URL 404 döndü — elenme nedenleri event-web-arastirma §9'da kayıtlı"
  - "DOĞRULANDI: RFC 8594 reddi event-anayasa §14/§15 Q6 + event-web-arastirma §5.1'de kayıtlı; kardeş openapi-web-arastirma.md W9 iddiası ile çelişiyor, kardeş dosyaya DOKUNULMADI"
  - "DOĞRULANDI: Zero-hallucination korundu — doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED / ⚠️ UNKNOWN olarak işaretlendi"
  - "DOĞRULANDI: 'Taşıma biçimi: webhook mu olay akışı mı' KARARI VERİLMEDİ — kanıtlandı, ⚠️ UNKNOWN olarak korundu (event-anayasa §9 + §15 Q1)"
  - "DOĞRULANDI: 13 dilde imza doğrulama kodu test vektörü ile eşleşiyor (event-kod-ornekleri §4)"
  - "DOĞRULANDI: docs.slack.dev adresi doğrulandı (api.slack.com'tan yönlendirme)"
  - "VERIFICATION REQUIRED: çekirdek doğrulama katmanı sayısı — event-dogrulama.md 7 katman tanımlar, ADR gerekli"
  - "VERIFICATION REQUIRED: idempotency penceresi 24 saat önerisi — IETF taslağından geliyor, implementasyon kararı ADR bekliyor"
  - "VERIFICATION REQUIRED: imza algoritma seçimi — HMAC-SHA256 önerilir ama CoreMusic ADR'si gerekli"
  - "VERIFICATION REQUIRED: jitter parametreleri base=1sn cap=60-600sn — CoreMusic seçimi ADR bekliyor"
  - "VERIFICATION REQUIRED: kardeş openapi-web-arastirma.md W9 RFC 8594 iddiası hatalı — düzeltilmeli"
open_questions:
  - "Q1: Taşıma biçimi: webhook mu olay akışı mı? → ⚠️ UNKNOWN — KARAR VERİLMEDİ (event-anayasa §9)"
  - "Q2: İmza algoritması: HMAC-SHA256 mi HMAC-SHA512 mi? → ADR bekliyor"
  - "Q3: İmza ön eki: 'sha256=' mi 'v0=' mi 't=,v1=' mi? → sozlesme.md §2'de tanımlı, ADR gerekli"
  - "Q4: İdempotency penceresi: 24 saat mi? → IETF taslağı öneriyor, ADR bekliyor"
  - "Q5: Jitter parametreleri: base=1sn, cap=60-600sn — CoreMusic seçimi? → ADR bekliyor"
  - "Q6: Yeniden gönderim penceresi: 5 dakika (Stripe/Slack) — CoreMusic toleransı? → ADR bekliyor"
  - "Q7: Doğrulama düzlemi: sunucu mu, istemci de mi? → mimari karar"
  - "Q8: Test tipi: sentetik vektör (GitHub) mi, canlı webhook mı? → sentetik önerilir"
  - "Q9: CloudEvents contentType: application/cloudevents+json zorunlu mu? → doğrulanmadı"
  - "Q10: Idempotency-Key kapsamı: event mi, event+target mı? → ADR bekliyor"
  - "Q11: Kardeş openapi-web-arastirma.md W9 RFC 8594 iddiası hatalı — düzeltme görevi ayrı"
  - "Q12: CoreMusic ana dili PHP 8.4 — §3.3 uyarlaması ne zaman?"
group: mimari
---

# Event Contracts — İndeks

> Bu klasör, CoreMusic'in **olay sözleşmeleri** (webhook / olay akışı) için **5 dosyadan** oluşan
> mimari + kanıt + kod paketidir. Kalıp: `api-contracts/openapi/`.
> **15 doğrulanmış kaynak**, **10×404 elenen aday**, **1 reddedilen iddia** (RFC 8594),
> **12 açık soru** (Q1-Q12), **13 dilde kod**.

---

## §0 ELI15

Bu klasör ne? CoreMusic'in olay sözleşmeleri için **5 dosyalık** bir mimari + kanıt + kod paketi.
1. **event-anayasa**: olay nedir, roller (publisher/subscriber), teslim semantiği (at-least-once), idempotency, imza düzlemi, WebSub abonelik, webhook-vs-akış karşılaştırma
2. **event-sozlesme**: alan sözleşmesi, HTTP bağlantısı, imza başlıkları, durum kodu eşlemesi, retry/tam-jitter, WebSub abonelik, AsyncAPI kanal
3. **event-dogrulama**: 7 doğrulama katmanı, GitHub test vektörü (`sha256=757107ea…`), timing-safe, timestamp penceresi, idempotency dedup, CI kapısı (10 test)
4. **event-kod-ornekleri**: 13 dilde imza doğrulama (PHP 8.4, JS, TS, Python, Go, Java, C#, Rust, C++, Ruby, Kotlin, Swift, Elixir), test vektörleri
5. **event-web-arastirma**: 15 URL doğrulandı, 10×404 elendi, RFC 8594 reddedildi

**Taşıma biçimi: webhook mi olay akışı mı?** → ⚠️ **UNKNOWN — KARAR VERİLMEDİ** (kanıtlandı ama
karar kullanıcıya bırakıldı). Q1'e bak.

---

## §GRUPLAR

| # | Grup | Dosyalar | Odak |
|---|------|----------|------|
| 1 | **Mimari** | event-anayasa.md | Olay modeli, roller, teslim semantiği, imza düzlemi, webhook-vs-akış |
| 2 | **Sözleşme** | event-sozlesme.md | Alanlar, HTTP bağlantısı, imza başlıkları, durum kodu, retry, WebSub, AsyncAPI |
| 3 | **Doğrulama** | event-dogrulama.md | 7 katman, test vektörü, timing-safe, timestamp, idempotency, jitter, CI kapısı |
| 4 | **Kod** | event-kod-ornekleri.md | 13 dil, imza doğrulama, test vektörleri, uyarlamalar |
| 5 | **Kanıt** | event-web-arastirma.md | 15 URL, 404 elenenler, RFC 8594 reddi |

---

## §1 Dosya — Sıra ve Bağımlılık

| # | Dosya | Grup | Satır | Bağımlı |
|---|-------|------|-------|---------|
| 1 | [event-anayasa.md](event-anayasa.md) | mimari | ≥350 | — |
| 2 | [event-sozlesme.md](event-sozlesme.md) | mimari | ≥350 | §1 |
| 3 | [event-dogrulama.md](event-dogrulama.md) | kod | ≥350 | §1 + §2 |
| 4 | [event-kod-ornekleri.md](event-kod-ornekleri.md) | kod | ≥350 | §1 + §2 + §3 |
| 5 | [event-web-arastirma.md](event-web-arastirma.md) | kanit | ≥350 | §1-§4 |

**Sıra**: her dosya bir öncekini doğrular. Kod yazmadan önce §1-§3 okunur; §4 referans, §5 kanıt.

---

## §2 Özet — Her Dosya

### §2.1 event-anayasa.md
- **Roller**: publisher (olay yayan) / subscriber (olay dinleyen) / broker (varsa)
- **CloudEvents envelope**: `id`/`source`/`specversion`/`type` (zorunlu) + `time`/`data`/`datacontenttype`
- **Teslim semantiği**: at-least-once (IETF taslağı: exactly-once non-goal) [E6]
- **Idempotency**: `Idempotency-Key` zorunlu, 24 saat pencere [E6]
- **İmza düzlemi**: HMAC-SHA256, timing-safe, timestamp penceresi [E1][E3][E14]
- **WebSub abonelik**: `hub.callback/mode/topic/lease_seconds/secret` [E9]
- **Webhook-vs-akış §9**: karşılaştırma tablosu — **UNKNOWN korunur**

### §2.2 event-sozlesme.md
- **Alan sözleşmesi**: CloudEvents `id`/`source`/`specversion`/`type` + opsiyonel alanlar [E8]
- **HTTP bağlantısı**: `POST`, `Content-Type: application/cloudevents+json`, `Idempotency-Key` zorunlu [E6]
- **İmza başlıkları**: `X-Hub-Signature-256` (GitHub) / `Stripe-Signature` / `X-Slack-Signature` [E3][E4][E5]
- **Durum kodu eşlemesi**: 2xx kabul / 4xx terminal / 408-429-5xx geçici [E11]
- **Retry**: tam-jitter backoff `rastgele[0, min(cap, base*2^n)]` [E7]
- **WebSub abonelik**: hub alanları + lease + challenge [E9]
- **AsyncAPI kanal**: `channels`/`operations`/`contentType`/`correlationId` [AsyncAPI]

### §2.3 event-dogrulama.md
- **7 katman**: başlık varlığı → imza varlığı → timestamp → imza hesabı → body bütünlüğü → idempotency → zorunlu alan
- **GitHub test vektörü**: `sha256=757107ea…` [E3]
- **Timing-safe**: `!=`/`===` YASAK [E14]
- **Timestamp penceresi**: 300 sn (Stripe/Slack) [E4][E5]
- **Idempotency dedup**: Redis TTL 86400 sn [E6]
- **Backoff jitter**: base=1s, cap=60-600s [E7]
- **CI kapısı**: 10 test — hepsi başarısızsa deploy durdur

### §2.4 event-kod-ornekleri.md
- **Kanıt A/B/C**: A=test vektörü + imza, B=idempotency + jitter, C=zorunlu alan + durum kodu
- **13 dil**: PHP 8.4 (CoreMusic ana), JS, TS, Python, Go, Java, C#, Rust, C++, Ruby, Kotlin, Swift, Elixir
- **Her blokta**: `# Dil: X — Rol: Y` + kaynak-etiketi
- **Test vektörleri**: GitHub (`sha256=757107ea…`), Stripe (`t=,v1=`), Slack (`v0=`) [E3][E4][E5]
- **Uyarlama §5**: CoreMusic PHP §3.3'ten başlar; secret `.env`'den, Redis `isDuplicate`

### §2.5 event-web-arastirma.md
- **Yöntem**: kaynak seçimi → URL listesi → webfetch doğrulama → iddia çıkarma → çelişki kontrolü → elenenleri kaydet
- **15 URL ✅**: GitHub, Stripe, Slack (docs.slack.dev), CloudEvents v1.0.2, WebSub Rec, AsyncAPI 3.0.0, IETF draft, RFC 2104/6234/3339/9110/9421/8594, standardwebhooks
- **10×404 elendi**: §9 — nedenleriyle (main branch yok, repo yok, tutorial taşınmış, eski Slack URL)
- **RFC 8594 reddi**: §5.1 — kardeş `openapi-web-arastirma.md` W9 iddiası hatalı; kardeş dosyaya DOKUNULMADI
- **Slack Docs adresi**: `docs.slack.dev` (api.slack.com'tan yönlendirme)

---

## §A Kapıları (Gate)

| # | Kapı | Durum |
|---|------|-------|
| 1 | **Zero-hallucination** | ✅ 15 URL doğrulandı; doğrulanamayan iddialar `⚠️ VERIFICATION REQUIRED` / `⚠️ UNKNOWN` işaretli; RFC 8594 reddi kayıtlı |
| 2 | **Taşıma biçimi kararı** | 🔴 `⚠️ UNKNOWN` — **KARAR VERİLMEDİ**; §9 + Q1'de korunur; karar kullanıcıya bırakıldı |
| 3 | **Frontmatter** | ✅ 11 alan (node_id, node_type, title, type, status, date, parent_id, children, depends_on, evidence, validation, open_questions, group) 5/5 dosyada mevcut |
| 4 | **node_id biçimi** | ✅ `K013-integraion/event-contracts/<dosya-adı>` — `integraion` imla dokunulmaz |
| 5 | **Satır sınırı** | ✅ 5/5 içerik dosyası ≥350 satır (index hariç) |
| 6 | **§GRUPLAR** | ✅ 5/5 dosyada §GRUPLAR tablosu mevcut |
| 7 | **§Kaynak/§A** | ✅ 5/5 dosyada §Kaynak Tablosu (İddia/URL/Tarih/Durum) + §A kapıları mevcut |
| 8 | **Alt bilgi** | ✅ 5/5 dosyada `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` |
| 9 | **Git** | 🔴 `git commit` ATILMADI — orkestratöre ait |

---

## §3 Açık Sorular — Q1-Q12

| Q | Soru | Durum | İlgili dosya |
|---|------|-------|--------------|
| Q1 | Taşıma biçimi: webhook mi olay akışı mı? | ⚠️ UNKNOWN — **KARAR VERİLMEDİ** | event-anayasa §9, §15 |
| Q2 | İmza algoritması: HMAC-SHA256 mi HMAC-SHA512 mi? | ADR bekliyor | event-dogrulama Q1 |
| Q3 | İmza ön eki: `sha256=` / `v0=` / `t=,v1=`? | sozlesme.md'de tanımlı, ADR gerekli | event-sozlesme §2 |
| Q4 | İdempotency penceresi: 24 saat mi? | IETF taslağı öneriyor, ADR bekliyor | event-dogrulama Q3 |
| Q5 | Jitter parametreleri: base=1sn, cap=60-600sn? | ADR bekliyor | event-dogrulama Q4 |
| Q6 | Yeniden gönderim penceresi: 5 dakika? | ADR bekliyor | event-dogrulama Q7 |
| Q7 | Doğrulama düzlemi: sunucu mu istemci de mi? | Mimari karar | event-dogrulama Q5 |
| Q8 | Test tipi: sentetik vektör mü canlı mı? | Sentetik önerilir | event-dogrulama Q17 |
| Q9 | CloudEvents contentType zorunlu mu? | Doğrulanmadı | event-web-arastirma Q18 |
| Q10 | Idempotency-Key kapsamı: event mi event+target mı? | ADR bekliyor | event-web-arastirma Q20 |
| Q11 | Kardeş `openapi-web-arastirma.md` W9 RFC 8594 iddiası hatalı | Düzeltme görevi ayrı | event-web-arastirma §5.1 |
| Q12 | CoreMusic PHP 8.4 uyarlaması ne zaman? | Backlog | event-kod-ornekleri §5 |

---

## §Kaynak Tablosu — Özet

| # | Sınıf | Sayı | Örnek |
|---|-------|------|-------|
| E1-E15 | Doğrulanmış URL (2026-10-16) | 15 | GitHub, Stripe, Slack, CloudEvents, WebSub, AsyncAPI, RFC'ler, IETF draft, standardwebhooks |
| X1-X10 | 404 elenen aday | 10 | main branch yok, repo yok, tutorial taşınmış, eski Slack URL |
| — | Reddedilen iddia | 1 | RFC 8594 (event-web-arastirma §5.1) |

**15/15 ✅ · 10/10 ❌404 · 1/1 RED** (2026-10-16).

---

## §A — Kapı Notları

**Kapı 2 (Taşıma biçimi kararı)**: Bu paket webhook ve olay akışının **ikisini de** kapsayan
kanıt topladı (CloudEvents envelope, WebSub abonelik, AsyncAPI kanal). Ancak CoreMusic'in
**hangisini seçeceği** KARARI VERİLMEDİ — bu, mimari bir karardır, kullanıcıya bırakılmıştır.
`event-anayasa.md` §9'da karşılaştırma tablosu var; §15 Q1'de `⚠️ UNKNOWN` olarak korunuyor.

**Kapı 9 (Git)**: `git commit` ATILMADI — orkestratöre ait; bu dosya salt okunur + yazılabilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
