---
node_id: K013-integraion/event-contracts/event-anayasa
node_type: technology-deepdive
title: "Olay Anayasası — Envelope, Roller, Teslim Semantiği, Abonelik ve İmza Düzlemi"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/event-contracts
  - K013-integraion
  - K009-security
evidence:
  - "https://github.com/cloudevents/spec/blob/v1.0.2/cloudevents/spec.md (2026-10-16)"
  - "https://github.com/cloudevents/spec (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc2104.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9421.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.w3.org/TR/websub/ (2026-10-16)"
  - "https://docs.stripe.com/webhooks (2026-10-16)"
  - "https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries (2026-10-16)"
  - "https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/ (2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 fetch, 15/15 içerik çekildi)"
  - "RFC 8594'ün 'Sunset HTTP Header' olduğu çekimle doğrulandı; kardeş dosya openapi-web-arastirma.md W9 iddiasıyla ÇELİŞİR (bkz. §16 Q6)"
  - "Uydurma alan/ad/ürün adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN"
  - "\"Taşıma biçimi webhook mu olay akışı mı\" KARARI verilmedi — iki olasılık da §11'de kanıtlandı"
open_questions:
  - "Taşıma biçimi: webhook mu olay akışı mı → ⚠️ UNKNOWN (iki olasılık da kanıtlandı, karar ADR bekliyor)"
  - "CoreMusic olay kimliği üretimi: kaynak olay ID mi, yerel UUID mi → ⚠️ VERIFICATION REQUIRED (repo'da üretim kodu okunmadı)"
  - "Dedup penceresi kaç saat olacak (draft 24 saat öneriyor) → UNKNOWN (karar yok)"
  - "Giden (outbound) olay CoreMusic'te var mı → ⚠️ VERIFICATION REQUIRED (repo yüzeyi bu turda taranmadı)"
  - "Kardeş dosya openapi-web-arastirma.md W9 (RFC 8594 = webhook aboneliği) düzeltilecek mi → MO kararı (frozen olmayan kardeş dosya)"
group: mimari
---

# Olay Anayasası — Envelope · Roller · Teslim Semantiği · Abonelik · İmza

> **Bu dosya ne değildir:** Kod, CLI rehberi, kütüphane kurulumu veya
> "hangi aracı seçelim" listesi değildir. Bu dosya, bir **olay sözleşmesinin
> üst düzlemını** *kanıtla* tanımlar: ne taşır, kim gönderir, kim alır,
> nasıl imzalanır, nasıl abone olunur. Her teknik terim §Kaynak
> Tablosu'ndaki bir URL'ye bağlıdır.
>
> **Durum etiketi:** ŞARTNAME IMPLEMENTED (standartlar çekildi) ·
> CoreMusic kodu ⚠️ PLANNED (bu turda `event`/`webhook` üreten veya
> tüketen bir repo dosyası okunmadı).

---

## §0 ELI15 — Olay sözleşmesi bir cümlede

Bir olay sözleşmesi, "bir şey oldu" haberinin **nasıl paketlendiğini,
nasıl imzalandığını ve alıcının aynı haberi iki kez işlerse ne
yapacağını** önceden yazan kurallar bütünüdür; haberi taşıyan boru
(webhook mu, kuyruk mu) ayrı bir karardır ve bu dosyada **karar
verilmez**, iki olasılık da kanıtlanır (§11).
(Kaynak: §Kaynak Tablosu [E1], [E9], [E12])

---

## §1 Bu Klasörün Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Klasör | `K013-integraion/event-contracts` | `.ai/architecture` kademe-2 subdomain |
| Kademe | 2 (subdomain) — içerik dosyaları derin-dal (kademe-3) | `index.md` §GRUPLAR |
| Üst düğüm | `K013-integraion` (dış dünya köprüsü) | `index.md` frontmatter |
| Kardeşler (8) | `adapters` · `external-systems` · `api-contracts` · `protocol-adapters` · `synchronization` · `error-handling` · `integration-testing` | `api-contracts/openapi/index.md` §1 |
| Ne taşır | Gelen/giden olayların biçimi, alanları, teslim şekli | `index.md` ELI15 |
| Ne taşımaz | Olayların **iç** dağıtımı (K012/K016) → ⚠️ UNKNOWN | `index.md` Sınır satırı |
| Dil | Standart dilden bağımsız; örnekler JSON | [E1] |
| Sürüm (referans) | CloudEvents **v1.0.2** (2026-10-16 ölçümünde depodaki sürüm tablosu) | [E2], [E3] |

---

## §2 Roller — Kim Var?

Üç temel rol ve (kuyruk/ara katman durumunda) dördüncü bir ara rol vardır.

| Rol | Ne yapar | Kanıt |
|---|---|---|
| **Olay Üreticisi** (Producer / Publisher / Sender) | Olayı üretir, teslim denemesini başlatır | [E9] §5 |
| **Olay Tüketicisi** (Consumer / Subscriber / Receiver) | Uç noktayı açar, teslimi alır, sonucu **gönderene** bildirir | [E9] §5, [E5] §3 |
| **Ara Katman** (Intermediary / Hub) | Yeniden yayınlar, dağıtır, tamponlar; aşağı yönde üretici, yukarı yönde tüketicidir | [E9] §5 |
| **Abonelik Merkezi** (Hub) | Abonelik kabul eder, niyet doğrular, imzalı dağıtım yapar | [E5] §3.1.1.3 |

### §2.1 Rol adları standart değildir (karışıklık tablosu)

| Konu | Farklı adlandırmalar | Not |
|---|---|---|
| Üretici | `Event Producer` · `Publisher` · `Sender` · `sender` | [E9] `Event Producer` · [E5] `Publisher` · [E9] `sender` |
| Tüketicisi | `Event Consumer` · `Subscriber` · `Receiver` | [E9] `Event Consumer` · [E5] `Subscriber` |
| Abonelik | `Subscription` (WebSub) · `webhook endpoint` (GitHub/Stripe) | [E5] §5 · [E6], [E7] |

> **Gerçek:** "Subscriber" ve "webhook alıcısı" **aynı davranışı**
> (uç noktayı açık tutup POST almak) farklı iki standartta farklı
> adlandırır. CoreMusic sözlüğü tek bir ad seçmelidir → **UNKNOWN**
> (sözlük kararı bu turda alınmadı; `glossary.md` bu terimleri içermiyor).

---

## §3 Envelope (Zarf) — Olay Ne Taşır?

### §3.1 Zorunlu alanlar (CloudEvents v1.0.2)

| Alan | Tip | Ne demektir | Kanıt |
|---|---|---|---|
| `id` | string | Bu olayın **benzersiz** tanımı (kaynak içinde) | [E3] |
| `source` | URI-reference | Olayın kaynağı (üretici bağlamı) | [E3] |
| `specversion` | string | Zarf spesifikasyonu sürümü (ör. `1.0`) | [E3] |
| `type` | string | Olayın türü (etkinlik sınıflandırması) | [E3] |

### §3.2 Opsiyonel alanlar (CloudEvents v1.0.2)

| Alan | Tip | Ne demektir | Kanıt |
|---|---|---|---|
| `datacontenttype` | string | Gövde medya tipi (ör. `application/json`) | [E3] |
| `data` | — | Gövde (payload) | [E3] |
| `dataschema` | URI | Gövdeyi tarif eden şema | [E3] |
| `subject` | string | Kaynak içindeki olay öznesi | [E3] |
| `time` | timestamp | Olay zamanı — **RFC 3339** biçiminde | [E3], [E4] |

### §3.3 Benzersizlik kuralı = idempotency temeli

| Kural | Anlamı | Kanıt |
|---|---|---|
| `source` + `id` çifti benzersizdir | Aynı çift ikinci kez gelirse **aynı olaydır** | [E3] |
| Alıcı bu çifti hatırlarsa | Tekrar teslim ayırt edilebilir | [E3] (uygulama düzeyi çıkarım) |
| Ürün kimliği üretimi | CoreMusic'te `id` ne olacak → ⚠️ VERIFICATION REQUIRED | repo okunmadı |

> **Doğrulanmayan:** "CloudEvents `exactly-once` garanti eder" cümlesi
> **spesifikasyonda yoktur** ve yazılmamıştır. İlgili draft da
> `exactly-once`'u açıkça **non-goal** olarak sayar. (Kaynak: [E9] §3.3)

### §3.4 Boyut sınırı

| Sınır | Değer | Kanıt |
|---|---|---|
| Intermediary'lerin taşıyabileceği azami olay boyutu | **64 KB** | [E3] |
| Çekirdek belge boyutu (JSON örneğiyle) | belge içeriğiyle sınırlı → detay `⚠️ VERIFICATION REQUIRED` (bu turda okunmadı) | [E3] |

---

## §4 Teslim Semantiği — "Erişti mi?" Sorusunun Cevabı

| Kavram | Tanım | Kanıt |
|---|---|---|
| **Teslim Denemesi** (Delivery Attempt) | Tek, bağımsız bir gönderme çabası; diğer denemelerden **bağımsızdır** | [E9] §6.1 |
| **Onay** (Acknowledgment) | Tüketicinin "aldım" sinyali — **işleme başarısı değildir** | [E9] §6.2 |
| **Gönderenin Gördüğü Sonuç** | `Accepted` · `Transient Failure` · `Terminal Failure` | [E9] §6.2 |
| **Yeniden Deneme** (Retry) | Kabul edilmeyen denemeden sonra yeni deneme | [E9] §6.3 |

### §4.1 Üç sonuç ve davranışı

| Sonuç | Gönderen ne yapar? | Kanıt |
|---|---|---|
| `Accepted` | **Yeniden denemez** | [E9] §6.2 |
| `Transient Failure` | Politikasına göre **yeniden deneyebilir** | [E9] §6.2 |
| `Terminal Failure` | **Yeniden denemez** | [E9] §6.2 |

> **Kural:** Gönderen, sonuçları **yalnızca gözlemlenen sinyalden**
> çıkarır; alıcının tarafında ne olduğunu **varsaymaz**. (Kaynak: [E9] §6.2)

### §4.2 Hangi garanti var, hangisi yok?

| İddia | Durum | Kanıt |
|---|---|---|
| `exactly-once` | **Yok** — draft non-goal | [E9] §3.3 |
| `at-most-once` | **Yok** — draft non-goal | [E9] §3.3 |
| `effectively-once` | **Yok** — draft non-goal | [E9] §3.3 |
| Tekrar teslim **mümkündür** | Evet — alıcı toleranslı olmalıdır | [E9] §7.2, [E7] |
| Sıralama **garantisi** | Yok (Stripe: açıkça garanti etmez) | [E7] |

---

## §5 İdempotency ve Tekrar Toleransı

| Kural | Değer | Kanıt |
|---|---|---|
| Kimlik sabitliği | Aynı mantıksal olayın tüm yeniden denemelerinde **aynı** kalır | [E9] §7.1 |
| Kapsam | Üretici-tüketici bağlamına görelidir | [E9] §7.1, §9.4 |
| Entropi | Çarpışmayı ihmal edilemez kılacak yeterli rastgelelik (UUID / ≥128 bit) | [E9] §9.4 |
| **Önerilen minimum dedup penceresi** | **24 saat** | [E9] §7.2 |
| Alıcı davranışı | Dedup **optimizasyondur**, zorunluluk değil | [E9] §7.2 |
| Gönderen davranışı | **Her durumda** tekrar teslimi tolere etmeli | [E9] §7.2 |
| Kimlik yeniden kullanımı | Pencere içinde farklı bir olay için **aynı kimlik kullanılamaz** | [E9] §7.2 |
| Pratik karşılığı | Stripe: tekrar eden `event id`'leri izle | [E7] |

**HTTP bağlantısındaki kimlik:** her teslim denemesi `Idempotency-Key`
başlığını **içermek zorundadır** (draft'ın HTTP bağlantısında).
(Kaynak: [E9] §9.4)

---

## §6 Sıralama ve Zaman

| Konu | Kanıtlanmış durum | Kanıt |
|---|---|---|
| Sıralama garantisi | **Yoktur** — Stripe açıkça "garanti etmez" der | [E7] |
| Sonuç | Alıcı **sıra bağımsız** yazılmalıdır | [E7] (uygulama çıkarımı) |
| Olay zamanı alanı | `time` → RFC 3339 (`2026-10-16T09:30:00Z` biçim ailesi) | [E3], [E4] |
| Alıcı saati (imza penceresi) | Üretici ile alıcı arasında fark sınırlandırılir (Stripe 5 dk, Slack 5 dk) | [E7], [E8] |
| `Retry-After` | Geçici hizada alıcı **minimum bekleme** bildirebilir | [E10] §10.2.3 · [E9] §9.5 |

---

## §7 İmza Düzlemi — HMAC nereden geliyor?

### §7.1 Temel

| Kavram | Tanım | Kanıt |
|---|---|---|
| HMAC | Mesajın gizli anahtarla **imzalanması** — hash'in başına `ipad`, sonuna `opad` karma işlemi eklenir | [E11] |
| Anahtar uzunluğu | Blok uzunluğundan büyükse anahtar önce hash'lenir | [E11] |
| Algoritma ailesi | SHA-1 / SHA-224 / SHA-256 / SHA-384 / SHA-512 tabanlı HMAC kodu resmi RFC ekinde | [E12] |
| Doğrulama | Sahte-imza tuzağına karşı **sabit süreli** (timing-safe) karşılaştırma | [E6], [E8] |

### §7.2 Sektör karşılıkları (çekilmiş, farklılıklar)

| Sağlayıcı | Başlık | Öneki / yapı | Pencere | Kanıt |
|---|---|---|---|---|
| GitHub | `X-Hub-Signature-256` | `sha256=` + hex | belirtilmedi → `⚠️ VERIFICATION REQUIRED` | [E6] |
| Stripe | `Stripe-Signature` | `t=<epoch>,v1=<hex>`; imzalanan metin `t + "." + body` | **5 dakika** (fark 0 ise reddet) | [E7] |
| Slack | `X-Slack-Signature` | `v0=` + hex; taban dizge `v0:<ts>:<body>` | **5 dakika** | [E8] |
| WebSub | `X-Hub-Signature` | `<method>=<hex>` (sha1/sha256/sha384/sha512) | abonelik süresi (lease) | [E5] §7.1 |
| GitHub referans testi | — | secret `It's a Secret to Everybody` + gövde `Hello, World!` → `sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17` | — | [E6] |

> **Uydurma yasak:** Yukarıdaki beş yapı **çekilmiş metinlerden**
> alınmıştır. CoreMusic'in hangi yapıyı seçeceği → **UNKNOWN** (ADR yok).

### §7.3 Uç-uç imza (farklı bir katman)

HTTP Message Signatures, başlıkları ve gövdeyi **birden çok hop**
boyunca bağlayan uç-uç bir imza tanımlar; HMAC-SHA-256 bir imza
algoritması olarak yer alır. (Kaynak: [E14])

---

## §8 Abonelik Yaşam Döngüsü (WebSub — çekilmiş akış)

| Adım | Ne olur | Kanıt |
|---|---|---|
| 1. Keşif | Tüketici, kaynakta `rel="hub"` ve `rel="self"` Link başlıklarını okur | [E5] §4 |
| 2. Abonelik isteği | Hub'a `POST` (form): `hub.callback`, `hub.mode`, `hub.topic` (+ `hub.lease_seconds`, `hub.secret`) | [E5] §5.1 |
| 3. Hub yanıtı | **202 Accepted** | [E5] §5.1.2 |
| 4. Niyet doğrulama | Hub, tüketiciye **GET** ile `hub.challenge` gönderir; tüketici **2xx** ile geri yazar | [E5] §5.3 |
| 5. Uygulama | Doğrulama başarısızsa tüketici **404** döner | [E5] §5.3.1 |
| 6. Dağıtım | Hub → tüketici **POST**; `X-Hub-Signature` varsa imza doğrulanır | [E5] §7 |
| 7. Başarı | Tüketici **2xx** ile **yalnızca alımı** onaylar (hızlı yanıt) | [E5] §7 |
| 8. Aboneliği kapatma | Aynı akış `hub.mode=unsubscribe` | [E5] §5 |
| 9. Süre dolumu | Lease (kira) zorunlu sürelerle biter; **sonsuz lease yasaktır** | [E5] §5.3.1 |

### §8.1 Bazı alanlar (çekilmiş)

| Alan | Zorunlu mu | Not | Kanıt |
|---|---|---|---|
| `hub.callback` | Evet | Tahmin edilemez, abonelik başına eşsiz olmalı | [E5] §5.1.1 |
| `hub.mode` | Evet | `subscribe` / `unsubscribe` | [E5] §5.1 |
| `hub.topic` | Evet | Keşifte bulunan `self` URL'si | [E5] §5.1.1 |
| `hub.lease_seconds` | Hayır | Hub saygı duymayabilir | [E5] §5.1 |
| `hub.secret` | Hayır | **< 200 byte** olmalı; yalnız HTTPS'te kullanılmalı | [E5] §5.1 |

### §8.2 Webhook aboneliğinin diğer karşılığı (kanıtlanmış alternatif)

GitHub/Stripe tipi "webhook" aboneliği, WebSub'un üç-adımlı niyet
doğrulaması yerine **imzalı teslim + geri gönderim** üzerine kuruludur;
abonelik kaydı genelde üreticinin kendi API'si üzerinden yapılır.
(Kaynak: [E6], [E7] — sayfa metinlerinden okundu; adım adım abonelik
API'si bu turda çekilmedi → `⚠️ VERIFICATION REQUIRED`)

---

## §9 Karar Kapısı — Webhook mu, Olay Akışı mı?

> **Bu bölüm KARAR DEĞİLDİR.** İki olasılık da kanıtlanır; seçim
> ADR'ye bırakılır. (`index.md` §A Kapı 2 ile aynı soru.)

| | **A) Webhook (HTTP geri çağırma)** | **B) Olay akışı (kanal/kuyruk)** |
|---|---|---|
| Tanım | Üretici, tüketicinin uç noktasına **HTTP isteğiyle** olay gönderir | Tüketici, bir **kanal/abonelik** üzerinden olayları okur |
| Kurumsal örnekler (çekilmiş) | GitHub `X-Hub-Signature-256` · Stripe `Stripe-Signature` · Slack `X-Slack-Signature` | AsyncAPI 3.0.0 `channels` + `operations` (send/receive) |
| Abonelik | WebSub: `hub.callback`/`hub.mode`/`hub.topic` | Kanal adresi + işlem tanımı (`action: receive`) |
| Alıcının ağırlığı | Uç nokta açık tutulmalı, imza doğrulanmalı | Kanal bağlantısı + tüketim grubu |
| Taşıma | HTTP POST (RS9110) | Protokol bağımsız (AsyncAPI: AMQP/MQTT/Kafka/HTTP…) |
| İmza | Zorunlu pratik (üç sağlayıcı da imzalıyor) | Kanıt bu turda çekildi → `⚠️ VERIFICATION REQUIRED` |
| CoreMusic uyumu | K013 (dış köprü) ile uyumlu görünür → **tasarım iddiası** | K012/K016 iç dağıtım ile ilişkili → **tasarım iddiası** |

**Kanıtlanmış ortak zemin (iki olasılık da uygular):** envelope
alanları (§3) · idempotency (§5) · sıralama garantisi yokluğu (§6) ·
tekrar toleransı (§4.2).

**Kanıtlanmayan:** CoreMusic'in hangi yolu seçtiği → ⚠️ UNKNOWN.

---

## §10 Güvenlik Düzlemi (çekilmiş başlıklar)

| Tehdit | Karşılığı | Kanıt |
|---|---|---|
| Sahte kaynak (spoofing) | HMAC imza + sabit süreli karşılaştırma | [E6], [E8], [E11] |
| Yeniden oynatma (replay) | İmzaya **zaman damgası** katılması + pencere dışı isteğin reddi | [E7], [E8] |
| Gövdeyi değiştirme | İmza ham (deserialize edilmemiş) gövde üzerinden hesaplanır | [E6], [E8] |
| SSRF (geri çağrı URL'si) | Abonelik URL'si doğrulaması → çekilen sayfalarda geçti, ayrıntı `⚠️ VERIFICATION REQUIRED` | [E13] |
| Sızıntı | Gizli anahtar **asla** `.md`/`.json`/`.log`'a yazılmaz | CoreMusic `.ai/CLAUDE.md` kuralı |
| Doğrulama açığı | Doğrulama başarısızsa **işleme başlamaz** (fail-closed) | [E5] §7.1.2 |

---

## §11 Sınır — Bu Dosyada İşlenmeyenler

1. Olay şemasının alanı/alan tipleri → `event-sozlesme.md`
2. Doğrulama katmanları, CI kapısı → `event-dogrulama.md`
3. Dile göre imza kodu → `event-kod-ornekleri.md`
4. URL kanıtı ve 404 elenmeleri → `event-web-arastirma.md`
5. İç dağıtım (K012/K016 kuyruk seçimi) → kapsam dışı, UNKNOWN
6. Küçük harf/protokol seçimi (MQTT/AMQP ayrıntıları) → kapsam dışı

---

## §12 İlgili Düğümler — Ağaçtaki Yeri

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion/event-contracts` | Bu derin-dalın klasörü |
| ↑ | `K013-integraion` | Dış dünya köprüsü |
| ↑ | `K009-security` | HMAC/anahtar/CSRF-CSP yüzeyi |
| ↑ | `K013-integraion/api-contracts/openapi` | OpenAPI 3.1 kök `webhooks` alanı ile sınır |
| ↓ | `K012-services` | Olayı tüketen servis (⚠️ VERIFICATION REQUIRED) |
| ↓ | `K016-…` (iç dağıtım) | Kapsam dışı — UNKNOWN |

---

## §13 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Envelope / Zarf** | Olayın kendisini saran meta alan kümesi (`id`/`source`/`type`…) |
| **Delivery Attempt** | Tek, bağımsız gönderme denemesi |
| **Acknowledgment** | Alım onayı — işleme başarısı **değil** |
| **Accepted / Transient / Terminal** | Gönderenin gözlediği üç sonuç |
| **Idempotency-Key** | Mantıksal olayı tüm denemelerde taşıyan kimlik başlığı |
| **Dedup Penceresi** | Alıcının kimlikleri hatırladığı süre (öneri: 24 saat) |
| **Lease (kira)** | Aboneliğin geçerlilik süresi — sonsuz olamaz |
| **Niyet doğrulama** | Hub'ın aboneliği gerçekten isteyen taraf olduğunu kanıtlaması |
| **Ham gövde** | JSON'a çevrilmemiş orijinal baytlar (imza bunun üstünden hesaplanır) |

---

## §14 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| Onay = işlendi mi? | **Hayır** — yalnızca alındı; işleme ayrı |
| CloudEvents = taşıma | Hayır; **zarf** tanımlar, boruyu **tanımlamaz** |
| WebSub = webhook | İkisi farklı katman: WebSub abonelik+dağıtım protokolü, webhook genel ad |
| İmza = TLS | İmza uç-uç (ara katman yıkabilir), TLS uç-uç kanal koruması |
| Sıralı teslim | Garanti **yok** — sırasız yazılmalı |
| `exactly-once` | Yok; tekrar toleransı **zorunlu** |
| RFC 8594 = webhook aboneliği | **Hayır** — "Sunset HTTP Header Field" (§16 Q6) |

---

## §15 Açık Sorular

| # | Soru | İşaret |
|---|---|---|
| Q1 | Taşıma biçimi: webhook mu olay akışı mı? | ⚠️ UNKNOWN (§9 — iki olasılık kanıtlandı) |
| Q2 | CoreMusic `event.id` üretimi? | ⚠️ VERIFICATION REQUIRED (repo okunmadı) |
| Q3 | Dedup penceresi (24 saat önerisi benimsenecek mi)? | UNKNOWN |
| Q4 | Giden (outbound) olay var mı? | ⚠️ VERIFICATION REQUIRED |
| Q5 | Hangi imza yapısı seçilecek (`sha256=` / `t=,v1=` / `v0=`)? | UNKNOWN (ADR bekliyor) |
| Q6 | Kardeş `openapi-web-arastirma.md` W9 (RFC 8594 = webhook aboneliği) düzeltilecek mi? | MO kararı — bu turda **dokunulmadı** (REDDİ kaydı: `event-web-arastirma.md` §7) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler — 2026-10-16 fetch)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| E1 | Olay sözleşmesi/tanım bağlamı — başlık ve giriş için kullanıldı | https://github.com/cloudevents/spec | 2026-10-16 | ✅ çekildi |
| E2 | Sürüm tablosu (v1.0.2) | https://github.com/cloudevents/spec | 2026-10-16 | ✅ çekildi |
| E3 | Zorunlu alanlar (`id`/`source`/`specversion`/`type`), opsiyonel alanlar, `time`=RFC 3339, 64 KB sınırı, JSON örneği, `source`+`id` benzersizliği | https://github.com/cloudevents/spec/blob/v1.0.2/cloudevents/spec.md | 2026-10-16 | ✅ çekildi |
| E4 | RFC 3339 tarih-saat biçimi | https://www.rfc-editor.org/rfc/rfc3339.html | 2026-10-16 | ✅ çekildi |
| E5 | WebSub akışı: keşif, abonelik parametreleri, 202, niyet doğrulama, `hub.secret`, `X-Hub-Signature`, lease, 410 | https://www.w3.org/TR/websub/ | 2026-10-16 | ✅ çekildi |
| E6 | GitHub imzası: `X-Hub-Signature-256`, `sha256=` öneki, test vektörü, timing-safe karşılaştırma | https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries | 2026-10-16 | ✅ çekildi |
| E7 | Stripe: `t=,v1=`, 5 dk tolerans, sıralama garantisiz, tekrar eden event ID, hızlı 2xx | https://docs.stripe.com/webhooks | 2026-10-16 | ✅ çekildi |
| E8 | Slack: `v0=` öneki, `v0:<ts>:<body>` taban dizgesi, 5 dk pencere, sabit süreli karşılaştırma | https://docs.slack.dev/authentication/verifying-requests-from-slack | 2026-10-16 | ✅ çekildi (api.slack.com adresinden yönlendirme) |
| E9 | Teslim semantiği modeli: roller, üç sonuç, idempotency, 24 sa pencere, HTTP bağlantısı, `exactly-once` non-goal | https://datatracker.ietf.org/doc/draft-mayankpanke-event-delivery-semantics/01/ | 2026-10-16 | ✅ çekildi (I-D, 2026-06-28) |
| E10 | HTTP Semantics — `Retry-After`, durum kodları, POST/idempotent methods | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| E11 | HMAC tanımı (RFC 2104) — ipad/opad, anahtar uzunluğu | https://www.rfc-editor.org/rfc/rfc2104.html | 2026-10-16 | ✅ çekildi |
| E12 | SHA-2 + HMAC-SHA kodu (RFC 6234 eki) | https://www.rfc-editor.org/rfc/rfc6234.html | 2026-10-16 | ✅ çekildi |
| E13 | Standard Webhooks — SSRF/spoofing/replay hedefleri | https://standardwebhooks.com/ | 2026-10-16 | ✅ çekildi |
| E14 | HTTP Message Signatures — HMAC-SHA-256 (§3.3.3), uç-uç imza | https://www.rfc-editor.org/rfc/rfc9421.html | 2026-10-16 | ✅ çekildi |
| E15 | AsyncAPI 3.0.0 — `channels`/`operations`/`send`/`receive`, protokol bağımsız | https://www.asyncapi.com/docs/reference/specification/v3.0.0 | 2026-10-16 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak
> alınmamıştır. `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretli satırlar
> için dayanak **yoktur** ve "kaynak alındı" diye kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · CloudEvents v1.0.2 · **Tarih: 2026-10-16**
