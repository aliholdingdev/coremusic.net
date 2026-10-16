---
node_id: K013-integraion/event-contracts/event-sozlesme
node_type: technology-deepdive
title: "Olay Sözleşmesi Dili — Zarf Alanları, HTTP Bağlantısı, Başlıklar, Durum Kodları, Yeniden Deneme"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/event-contracts/event-anayasa
  - K013-integraion/event-contracts
  - K013-integraion
evidence:
  - "https://github.com/cloudevents/spec/blob/v1.0.2/cloudevents/spec.md (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/ (2026-10-16)"
  - "https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries (2026-10-16)"
  - "https://docs.stripe.com/webhooks (2026-10-16)"
  - "https://www.w3.org/TR/websub/ (2026-10-16)"
  - "https://www.asyncapi.com/docs/reference/specification/v3.0.0 (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc3339.html (2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 fetch)"
  - "Durum kodu sınıflandırması draft §9.2.1–9.2.3 tablolarından birebir alınmıştır"
  - "Kod blokları KAYNAK-ETİKETLİDİR: hangi belgeden türetildiği satırında yazılır; CoreMusic'e ait olduğu iddia edilmez"
open_questions:
  - "CoreMusic gelen olay uç noktası yolu ve adı → ⚠️ VERIFICATION REQUIRED (repo'da tanımlı değil)"
  - "Zarf biçimi: CloudEvents mi, düz özel JSON mu → UNKNOWN (ADR bekliyor)"
  - "Yeniden deneme üst sınırı (üstel backoff kapağı) → UNKNOWN (draft 60–600 sn aralığı öneriyor, karar yok)"
  - "Giden olay sözleşmesi CoreMusic'te var mı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# Olay Sözleşmesi Dili — Alan · Başlık · Durum Kodu · Yeniden Deneme

> **Bu dosya ne değildir:** Kütüphane kurulumu veya araç rehberi değildir.
> Bu dosya bir olay sözleşmesinin **dilbilgisini** yazar: hangi alan
> hangi tipte, hangi HTTP başlığı taşınır, hangi durum kodu ne demektir,
> gönderen yeniden denemeyi nasıl kurgular.
>
> **Kanıt kuralı:** `Kaynak: [E#]` etiketli her satır çekilmiş bir
> belgeden gelir. CoreMusic'in **gerçek** uç noktası, başlığı veya
> kodu bu turda diskten okunmadı → ilgili satırlar
> `⚠️ VERIFICATION REQUIRED` işaretlidir.

---

## §0 ELI15 — Bu dosya ne işe yarar?

Anayasa "neyin içinde olduğu"nu söyler (`event-anayasa.md`); bu dosya
**cümlenin nasıl kurulacağını** söyler: alan adı, alan tipi, başlık
adı, önek, durum kodu, bekleme kuralı. Bir ekip bu dosyayı okuyup
karşılıklı iki sistemi (üretici + tüketicisi) **aynı davranışı**
bekleyecek şekilde yazabilir. (Kaynak: §Kaynak Tablosu [E1], [E3])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| **mimari** | **`event-anayasa.md`** | Roller, envelope, teslim semantiği, abonelik, imza düzlemi | 3 |
| **mimari** | **`event-sozlesme.md`** | Alan dili, HTTP bağlantısı, başlık/durum kodu, retry | 3 |
| kod | `event-dogrulama.md` | Doğrulama katmanları, test vektörleri, CI kapısı | 3 |
| kod | `event-kod-ornekleri.md` | 12+ dilde webhook/imza örnekleri | 3 |
| kanit | `event-web-arastirma.md` | ≥12 benzersiz resmi URL + 404 elenmeleri | 3 |

---

## §1 Zarf Alan Sözleşmesi (alan ↔ tip ↔ zorunluluk)

### §1.1 Zorunlu alanlar — yoksa olay geçersizdir

| Alan | Tip | Boş olabilir mi? | Örnek değer | Kaynak |
|---|---|---|---|---|
| `id` | string | Hayır | `a1b2-c3d4-e5f6` | [E1] |
| `source` | URI-reference | Hayır | `urn:coremusic:billing` | [E1] |
| `specversion` | string | Hayır | `1.0` | [E1] |
| `type` | string | Hayır | `payment.succeeded` | [E1] |

### §1.2 Opsiyonel alanlar

| Alan | Tip | Not | Kaynak |
|---|---|---|---|
| `datacontenttype` | string | Gövde medya tipi (ör. `application/json`) | [E1] |
| `data` | — | Gövde | [E1] |
| `dataschema` | URI | Gövde şeması | [E1] |
| `subject` | string | Olayın öznesi (kaynak içinde) | [E1] |
| `time` | timestamp | **RFC 3339** biçiminde yazılır | [E1], [E2] |

### §1.3 Zarf örneği (kaynak-etiketli — CoreMusic'e ait değildir)

```json
{
  "specversion": "1.0",
  "id": "01994f2c-7b1e-7c3a-9f2b-2f6c0b6a1111",
  "source": "urn:example:billing",
  "type": "payment.succeeded",
  "subject": "invoice:1001",
  "time": "2026-10-16T09:30:00Z",
  "datacontenttype": "application/json",
  "data": { "amount": 100, "currency": "TRY" }
}
```

> **Kaynak:** alan adları ve `time`/`specversion` zorunluluğu [E1]'den;
> örnek **değerler** bu dosya için **uydurulmuş temsilidir** ve gerçek
> bir CoreMusic olayı **değildir**. (`urn:example:` öneki bunu gösterir.)

### §1.4 Sürüm uyumluluğu

| Kural | Değer | Kaynak |
|---|---|---|
| Sürüm alanı | `specversion` | [E1] |
| Referans sürüm | `1.0` (depo sürüm tablosu: **v1.0.2**) | [E1] |
| Yeni alan ekleme | Alıcı **bilinmeyen alanı yok saymak** zorunda değildir → ayrıntı: `⚠️ VERIFICATION REQUIRED` (bu kural metinden okunmadı) | [E1] |

---

## §2 HTTP Bağlantısı — İstek Sözleşmesi

| Madde | Değer | Kaynak |
|---|---|---|
| Yöntem | **POST** (öneri); `PUT` yalnız "yerine koyma" anlaşılmışsa | [E3] §9.1 |
| Güvenli yöntemler (`GET`/`HEAD`/`OPTIONS`) | Teslim denemesi için **önerilmez** (durum değiştirmez, ara katman önbellekleyebilir) | [E3] §9.1 |
| `Content-Type` | Gövde medya tipini **kesin** göstermeli | [E3] §9.1 |
| Kimlik başlığı | `Idempotency-Key` — HTTP bağlantısında **her denemede zorunlu** | [E3] §9.4 |
| Kimlik değeri | Aynı mantıksal olayın tüm denemelerinde **aynı**; UUID veya ≥128 bit rastgele (base64url) | [E3] §9.4 |
| Kimlik kapsamı | (Üretici kimliği, tüketicinin uç nokta URI'si) çiftine görelidir | [E3] §9.4 |

### §2.1 İmza başlıkları (sektör karşılıkları — çekilmiş)

| Sağlayıcı | Başlık adı | Yapı | Kaynak |
|---|---|---|---|
| GitHub | `X-Hub-Signature-256` | `sha256=<hex>` | [E4] |
| Stripe | `Stripe-Signature` | `t=<saniye>,v1=<hex>` · imzalanır: `t + "." + gövde` | [E5] |
| Slack | `X-Slack-Signature` | `v0=<hex>` · taban: `v0:<timestamp>:<gövde>` | [E6] |
| WebSub | `X-Hub-Signature` | `<algorithm>=<hex>` (`sha1`/`sha256`/`sha384`/`sha512`) | [E7] §7.1 |
| WebSub | `Link` başlığı | dağıtım isteğinde `rel=hub` ve `rel=self` | [E7] §7 |

> **Başlık adı büyük/küçük harfe duyarsızdır** (Slack bunu açıkça
> yazar). (Kaynak: [E6])

> ⚠️ **CoreMusic başlığı hangisi?** → UNKNOWN. Repo'da webhook
> doğrulayan bir middleware bu turda okunmadı.

### §2.2 Alıcı yanıtı — hızlı 2xx

| Kural | Değer | Kaynak |
|---|---|---|
| Başarı | **2xx** = alındı (kabul edildi) | [E3] §9.2.1, [E7] §7 |
| Yanıt gövdesi | Alım onayında gövdenin içeriği **sonucu değiştirmez** | [E3] §9.2.1 |
| Hız | WebSub: tüketicisi **mümkün olduğunca çabuk** yanıtlamalı; yanıt yalnız **alımı** bildirir | [E7] §7 |
| Stripe pratik | Gönderenin zaman aşımını önlemek için **hızlı 2xx** + işi sonra kuyruğa al | [E5] |
| Abonelik silme | WebSub: tüketici **410 Gone** döndürerek aboneliğin kalktığını bildirebilir | [E7] §7 |

---

## §3 Durum Kodu → Sonuç Eşlemesi (draft §9.2 — birebir)

### §3.1 `Accepted` (yeniden deneme YOK)

| Kod | Ad | Not | Kaynak |
|---|---|---|---|
| 200 | OK | Kabul edilir | [E3] §9.2.1 |
| 201 | Created | Kabul edilir | [E3] §9.2.1 |
| 202 | Accepted | Kabul edilir | [E3] §9.2.1 |
| 204 | No Content | Kabul edilir | [E3] §9.2.1 |
| 207 | Multi-Status | **Vücut okunmadan kabul edilmez**; iki taraf önceden anlaşmalı olmalı | [E3] §9.2.1 |
| Diğer 2xx | — | Varsayılan: kabul (önceden anlaşma yoksa) | [E3] §9.2.1 |

### §3.2 `Transient Failure` (yeniden denenebilir)

| Kod | Ad | Not | Kaynak |
|---|---|---|---|
| 408 | Request Timeout | Belirsiz — güvenli deneme `Idempotency-Key`'e bağlı | [E3] §9.2.2 |
| 421 | Misdirected Request | Yeniden çözümlenen uç noktada denenebilir | [E3] §9.2.2 |
| 425 | Too Early | Erken-veri durumu geçince dene | [E3] §9.2.2 |
| 429 | Too Many Requests | `Retry-After` varsa **onu uygula** | [E3] §9.2.2 |
| 500 | Internal Server Error | Üstel beklemeyle dene | [E3] §9.2.2 |
| 502 | Bad Gateway | Yeniden dene | [E3] §9.2.2 |
| 503 | Service Unavailable | `Retry-After` varsa uygula | [E3] §9.2.2 |
| 504 | Gateway Timeout | Belirsiz — güvenli deneme `Idempotency-Key`'e bağlı | [E3] §9.2.2 |
| 511 | Network Authentication Required | Genelde yakalama portalı; bandışı çözülür | [E3] §9.2.2 |
| Diğer 5xx | — | Varsayılan: geçici (önceden anlaşma yoksa) | [E3] §9.2.2 |

### §3.3 `Terminal Failure` (yeniden deneme YOK)

| Kod | Ad | Not | Kaynak |
|---|---|---|---|
| 400 | Bad Request | Bozuk istek — aynı sonuç doğar | [E3] §9.2.3 |
| 401 | Unauthorized | Dönen kimlik bilgisiyle **bir** deneme hakkı olabilir | [E3] §9.2.3 |
| 403 | Forbidden | Politika değişmedikçe deneme işe yaramaz | [E3] §9.2.3 |
| 404 | Not Found | Uç nokta yok — yapılandırma gerekir | [E3] §9.2.3 |
| 405 | Method Not Allowed | Yöntem uyuşmazlığı (bkz. §2) | [E3] §9.2.3 |
| 410 | Gone | Uç nokta kalıcı olarak kalktı — aboneliği pasifleştir | [E3] §9.2.3 |
| 413 | Content Too Large | Gövde limiti aşıldı | [E3] §9.2.3 |
| 414 | URI Too Long | Üretici yapılandırma hatası | [E3] §9.2.3 |
| 415 | Unsupported Media Type | Medya tipi bandışı yeniden konuşulmalı | [E3] §9.2.3 |
| 422 | Unprocessable Content | Şema/semantik doğrulama hatası | [E3] §9.2.3 |
| 451 | Unavailable for Legal Reasons | Kayıt + operatöre eskalasyon | [E3] §9.2.3 |
| Diğer 4xx | — | Varsayılan: son (önceden anlaşma yoksa) | [E3] §9.2.3 |

### §3.4 Diğer sınıflar

| Sınıf | Muamele | Kaynak |
|---|---|---|
| 1xx | RFC 9110'a göre; tek başına sonuç **değildir** | [E3] §9.2.4 |
| 3xx | `308` → üretici uç nokta URI'sini **günceller**; `307` → **güncellemez** | [E3] §9.2.4 |
| Ağ seviyesi hata (bağlantı reddi/reset/TLS/zaman aşımı/DNS) | Geçici sayılır | [E3] §9.3 |
| Yanıt tam alınamadı | "Sonuç bilinmeyen geçici hata" → güvenli deneme kimliğe bağlı | [E3] §9.3 |
| Sorun ayrıntısı gövdesi | Önerilir (`problem+json` ailesi) — **sonuç yine durum kodundan** çıkarılır | [E3] §9.2.5 |

---

## §4 Yeniden Deneme Sözleşmesi

| Madde | Değer | Kaynak |
|---|---|---|
| Karar ölçütü | **Yalnız** gönderenin gözlediği sonuç | [E3] §6.2 |
| `Retry-After` | Varsa **alt sınır** kabul edilir; erken deneme yapılmaz | [E3] §9.5 |
| `Retry-After: 0` | Tüketicinin tercihi yoktur → gönderenin kendi politikası geçerli | [E3] §9.5 |
| Algoritma özellikleri | 1) artmayan ortalama bekleme (backoff) 2) rastgelelik (jitter) 3) **sonlu** üst sınır (deneme sayısı ve/veya toplam süre) | [E3] §9.6 |
| Yaygın varsayılan | **Tam-jitter üstel bekleme:** `rastgele[0, min(cap, taban × 2^n)]` · taban genelde **1 sn**, kapak **60–600 sn** | [E3] §9.6 |
| `Retry-After` > hesap | `Retry-After` **alt sınır** olarak uygulanır | [E3] §9.6 |
| Ara katman | `Idempotency-Key` **değiştirilmeden** aşağıya iletilir; `Retry-After` yukarı **iletilmez** | [E3] §9.7 |
| Pratik karşılığı | Stripe: **3 gün** boyunca üstel geri çekilme ile yeniden gönderir | [E5] |
| Üst sınır (CoreMusic) | UNKNOWN (karar ADR bekliyor) | — |

### §4.1 Sarf malzemesi — hangi olay tekrarlanır?

| Durum | Davranış | Kaynak |
|---|---|---|
| Alıcı `4xx` sonucu verdi | Aynı olay **tekrar gönderilmez** | [E3] §9.2.3 |
| Alıcı `5xx`/ağ hatası | Tekrar **serbesttir** — alıcı idempotent olmak zorunda | [E3] §9.2.2, §9.3 |
| Alıcı aynı kimliği ikinci kez gördü | **Ayırt edilebilir** (pencere içinde) → işlenmez/özetlenir | [E3] §7.2 |

---

## §5 Abonelik Sözleşmesi (WebSub alanları — çekilmiş)

| Alan | Zorunlu | Tip/format | Kaynak |
|---|---|---|---|
| `hub.callback` | Evet | URL — tahmin edilemez, abonelik başına eşsiz | [E7] §5.1.1 |
| `hub.mode` | Evet | `subscribe` \| `unsubscribe` | [E7] §5.1 |
| `hub.topic` | Evet | Keşifteki `self` URL'si | [E7] §5.1.1 |
| `hub.lease_seconds` | Hayır | Pozitif tam sayı (saniye) | [E7] §5.1 |
| `hub.secret` | Hayır | Kriptografik rastgele; **< 200 byte**; yalnız HTTPS | [E7] §5.1 |
| İçerik tipi (abonelik isteği) | Evet | `application/x-www-form-urlencoded`, UTF-8 | [E7] §5.1 |
| Hub yanıtı | — | **202 Accepted** | [E7] §5.1.2 |
| Niyet doğrulama | — | Hub **GET** gönderir: `hub.challenge` geri yazılır; uyuşmazsa **404** | [E7] §5.3, §5.3.1 |
| Dağıtım başarısı | — | **2xx**; **410** abonelik silinebilir | [E7] §7 |

**Keşif:** kaynakta `rel="hub"` ve `rel="self"` `Link` başlıkları
(en az bir hub, tam bir self). (Kaynak: [E7] §4)

---

## §6 Olay Türü ve Kanal Sözleşmesi (akış tarafı — AsyncAPI 3.0.0)

| Madde | Değer | Kaynak |
|---|---|---|
| Kanal | Adreslenebilir bileşen; `address` alanı "topic/konu adı" ya da "olay türü" olabilir | [E8] |
| İşlem | `action: send` \| `receive` — uygulamanın gönderdiği/aldığı | [E8] |
| Mesaj | `headers` + `payload`; `contentType` (ör. `application/json`) | [E8] |
| İlişkilendirme | `correlationId` — mesaj izleme/eşleme | [E8] |
| Protokol | Bağımlı değil (AMQP/MQTT/Kafka/HTTP…) · protokol bilgisi **binding**'lerde | [E8] |
| Çapraz okuma | Gönderen belgesinden alıcı belgesi **türetilmez** (alan adları anlamsızlaşır) | [E8] |

> **Not:** AsyncAPI 3.0.0'da `webhooks` adında ayrı bir kök alanı
> **idDia edilmez** — bu dosyada öyle bir alan **yazılmamıştır**
> (metin bu turda okundu; 2.x ile fark `⚠️ VERIFICATION REQUIRED`).

---

## §7 Giden (Outbound) Olay Sözleşmesi

| Soru | Durum |
|---|---|
| CoreMusic dışarıya olay gönderiyor mu? | ⚠️ VERIFICATION REQUIRED (repo taranmadı) |
| Gönderiyorsa imza başlığı hangisi? | UNKNOWN |
| Gönderiyorsa yeniden deneme politikası? | UNKNOWN |

**Bu bölüm bilerek boştur:** dayanaksız satır yazılmamıştır.

---

## §8 Sürümleme ve Kırılma

| Kural | Değer | Kaynak |
|---|---|---|
| Zarf sürümü | `specversion` alanı taşır | [E1] |
| Olay türü değişimi | `type` yeni değer = **yeni olay türü** (eskiyi koruyan alıcı için uyumlu) → tasarım kuralı, `⚠️ VERIFICATION REQUIRED` (standartta bu kural yok) | — |
| Alan kaldırma | Bilinmeyen alan davranışı standarttan okunmadı → `⚠️ VERIFICATION REQUIRED` | [E1] |
| Sürüm politikası kararı | UNKNOWN (ADR yok) | — |

---

## §9 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **HTTP bağlantısı (binding)** | Soyut modelin HTTP'ye çevirilişi (yöntem, başlık, kod) |
| **Ham gövde** | İmza hesabına giren, dönüştürülmemiş baytlar |
| **Taban dizge (basestring)** | İmzaya giren birleştirilmiş metin (ör. `v0:<ts>:<body>`) |
| **Tam-jitter** | Üstel beklemede `[0, üst]` aralığından rastgele seçim |
| **Sonlu sınır** | Deneme sayısının ve/veya toplam sürenin bitmesi |
| **Lease** | Aboneliğin kira süresi |
| **Binding (AsyncAPI)** | Protokole özgü alanların tanımlandığı ek |

---

## §10 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| 2xx = işlendi | Hayır — yalnızca **alındı** |
| 429 = hemen bırak | Hayır — **yeniden denenebilir**, `Retry-After`'a uy |
| 404 = geçici | Hayır — **sonuç** (uç nokta eksik) |
| 307 ile 308 aynı | Hayır — 308 uç noktayı günceller, 307 güncellemez |
| `Idempotency-Key` alıcıda üretilir | Hayır — **üreticinin** kimliğidir, denemeler arasında sabittir |
| `Retry-After` üst sınırdır | Hayır — **alt sınırdır** |
| CloudEvents alanları HTTP başlığıdır | Hayır — zarf alanları gövdede/uygulama katmanındadır |

---

## §11 Kapsam Dışı

| Konu | Nerede |
|---|---|
| Roller, semantik, imza düzlemi | `event-anayasa.md` §2–§7 |
| Doğrulama katmanı ve CI | `event-dogrulama.md` |
| Dile göre kod | `event-kod-ornekleri.md` |
| URL kanıtı + 404 | `event-web-arastirma.md` |
| OpenAPI `webhooks` kök alanı | `api-contracts/openapi/openapi-anayasa.md` §3 |

---

## §12 Açık Sorular

| # | Soru | İşaret |
|---|---|---|
| Q1 | Zarf biçimi CloudEvents mi özel JSON mu? | UNKNOWN (ADR) |
| Q2 | Gelen olay uç noktası yolu? | ⚠️ VERIFICATION REQUIRED |
| Q3 | İmza başlığı seçimi? | UNKNOWN |
| Q4 | Retry kapağı / toplam süre? | UNKNOWN (draft önerisi 60–600 sn) |
| Q5 | Giden olay var mı? | ⚠️ VERIFICATION REQUIRED |
| Q6 | Bilinmeyen alan davranışı (drop mı, kabul mü)? | ⚠️ VERIFICATION REQUIRED (metinden okunmadı) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler — 2026-10-16 fetch)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| E1 | Zarf alanları, zorunlu/opsiyonel ayrımı, `specversion` | https://github.com/cloudevents/spec/blob/v1.0.2/cloudevents/spec.md | 2026-10-16 | ✅ çekildi |
| E2 | RFC 3339 zaman biçimi (`time` alanı için) | https://www.rfc-editor.org/rfc/rfc3339.html | 2026-10-16 | ✅ çekildi |
| E3 | HTTP bağlantısı: POST, `Idempotency-Key`, durum kodu sınıfları, `Retry-After`, tam-jitter backoff, ara katman kuralları | https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/ | 2026-10-16 | ✅ çekildi (I-D 2026-06-28) |
| E4 | GitHub `X-Hub-Signature-256` + `sha256=` + timing-safe | https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries | 2026-10-16 | ✅ çekildi |
| E5 | Stripe `Stripe-Signature` (`t=,v1=`), 5 dk, 3 gün retry, sıralama garantisiz | https://docs.stripe.com/webhooks | 2026-10-16 | ✅ çekildi |
| E6 | Slack `X-Slack-Signature` (`v0=`), taban dizge, 5 dk, başlık büyük/küçük harf | https://docs.slack.dev/authentication/verifying-requests-from-slack | 2026-10-16 | ✅ çekildi |
| E7 | WebSub: keşif, abonelik alanları, 202, niyet doğrulama, `X-Hub-Signature`, 410, hızlı 2xx | https://www.w3.org/TR/websub/ | 2026-10-16 | ✅ çekildi |
| E8 | AsyncAPI 3.0.0: channel/operation/message, `send`/`receive`, contentType, correlationId, binding | https://www.asyncapi.com/docs/reference/specification/v3.0.0 | 2026-10-16 | ✅ çekildi |
| E9 | HTTP Semantics — `Retry-After` (§10.2.3) ve durum kodu tanımları | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| E10 | HMAC tanımı (imza hesabı için altlık) | https://www.rfc-editor.org/rfc/rfc2104.html | 2026-10-16 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen URL dayanak alınmamıştır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · CloudEvents v1.0.2 · **Tarih: 2026-10-16**
