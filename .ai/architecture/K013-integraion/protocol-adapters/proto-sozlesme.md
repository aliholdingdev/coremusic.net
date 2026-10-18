---
node_id: K013-integraion/protocol-adapters/proto-sozlesme
node_type: technology-deepdive
title: "Protocol Adapters Sözleşme Dili — Çift Bazında Tanım, Sürüm, Pazarlık, Hata"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/protocol-adapters/proto-anayasa
  - K013-integraion/protocol-adapters
evidence:
  - "https://www.w3.org/TR/wsdl20/ (2026-10-16 çekildi)"
  - "https://www.w3.org/TR/SOAP12/ (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16 çekildi)"
  - "https://grpc.io/docs/what-is-grpc/core-concepts/ (2026-10-16 çekildi)"
  - "https://html.spec.whatwg.org/multipage/server-sent-events.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc6455.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc6750.html (2026-10-16 çekildi)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 çekimi, içerik alındı)"
  - "Eşleme tablolarındaki kod çiftleri §6 kimlik kartlarıyla (proto-anayasa.md) birebir aynı kaynaktan"
  - "Doğrulanamayan eşleme satırı ⚠️ VERIFICATION REQUIRED ile işaretlidir"
open_questions:
  - "CoreMusic hata gövdesi formatı (Problem Details mı?) → UNKNOWN (openapi sorusu ile kesişir)"
  - "gRPC status ↔ HTTP status eşleme tablosu resmi kaynağı bu turda çekilmedi → ⚠️ VERIFICATION REQUIRED"
  - "Hangi çift production'da kullanılıyor → ⚠️ VERIFICATION REQUIRED (repo kanıtı yok)"
group: mimari
---

# Protocol Adapters Sözleşme Dili — Tanım · Sürüm · Pazarlık · Hata

> **Bu dosya ne değildir:** Anayasa (sınır/kimlik) tekrarı değildir; kod
> örnekleri de değildir. Bu dosya **her protokol çiftinin sözleşmeyi nerede
> yazdığını** gösterir: hangi belge tanımıdır, sürüm nasıl bildirilir,
> iki taraf nasıl anlaşır (negotiation), hata nasıl çevrilir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Repo uygulaması
> ⚠️ VERIFICATION REQUIRED.

---

## §0 ELI15 — "Sözleşme" burada ne demek?

Bir protokol çiftinin çevirmeni, iki tarafın da **aynı şeyi anladığı**
bir tanım olmadan çeviri yapamaz: mesajın neyi çağıracağını (operasyon),
hangi sürümü konuştuğunu, kabul ettiği biçimi (negotiation) ve bozulunca ne
söyleyeceğini (hata) bilmek zorundadır. Bu dosya beş çift için bu dört
sorunun şartname cevabını toplar.
(Kaynak: §Kaynak Tablosu [S1] — WSDL soyut/somut ayrımı; [S2] — SOAP MEP;
[S3] — HTTP içerik pazarlığı)

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır, protokol çiftleri, düzlem ayrımı | 3 |
| mimari | `proto-sozlesme.md` | Sözleşme yüzeyi: tanımlama, sürüm, pazarlık, hata (bu dosya) | 3 |
| kod | `proto-dogrulama.md` | Doğrulama katmanları: şema, el sıkışma, kimlik, CI kapısı | 3 |
| kod | `proto-kod-ornekleri.md` | 13 dilde protokol dönüşüm örneği | 3 |
| kanit | `proto-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia/URL/tarih/durum defteri | 3 |

---

## §1 Sözleşme Yüzeyi Matrisi — "Tanım Nerede Yazılır?"

| Çift | Bir yüzün sözleşmesi | Diğer yüzün sözleşmesi | Çevirmenin okuduğu | Dayanak |
|---|---|---|---|---|
| P1 HTTP↔gRPC | HTTP semantik (yöntem/durum/alan) [S3] | `.proto` servis + mesaj tanımı | ikisi de | [S3], [S4] |
| P2 REST↔SOAP | HTTP + medya tipi [S3] | WSDL (soyut interface + somut binding) + SOAP Envelope [S1], [S5] | ikisi de | [S1], [S5], [S3] |
| P3 JSON↔XML | JSON sözdizimi (RFC 8259) [S6] | XML 1.0 + (opsiyonel) XML Schema [S7], [S8] | ikisi de | [S6], [S7] |
| P4 SSE↔WebSocket | `text/event-stream` alanları [S9] | RFC 6455 el sıkışma + alt protokol [S10] | ikisi de | [S9], [S10] |
| P5 bearer↔API key | RFC 6750 Bearer [S11] | API key'in sahibi sistem (CoreMusic/üçüncü parti) — şartname **yok** | biri şartname, biri özel | [S11] · ⚠️ API key formatı UNKNOWN |

> **P5 notu (dürüstlük):** API key'in hangi header'da/şemada taşınacağına
> dair **evrensel IETF/W3C şartnamesi bulunmamaktadır**; formatı sağlayan
> sistem belirler. Bu yüzden P5'in "diğer yüzü" için kaynak
> `⚠️ VERIFICATION REQUIRED` olarak bırakılmıştır (uydurulmamıştır).

---

## §2 P1 — HTTP ↔ gRPC Sözleşmesi

### §2.1 Tanım katmanı

| Kavram | HTTP yüzü | gRPC yüzü | Dayanak |
|---|---|---|---|
| Operasyon tanımı | URL + method (RFC 9110 §9) | `.proto` içinde `rpc` (servis tanımı) | [S3], [S4] |
| Mesaj tipi | Medya tipi + gövde (`Content-Type`) | protobuf mesajı (field numaralarıyla) | [S3], [S4] |
| Metadata | Header alanları (case-insensitive isimler) | metadata key–value (`grpc-` öneki rezave, `-bin` son ek) | [S3], [S4] |
| Akış | Tek istek → tek yanıt (varsayılan); streaming için ayrıca | 4 tip: unary / server / client / bidi streaming | [S4] |

### §2.2 Sürüm ve pazarlık

| Yüz | Nasıl bildirilir | Dayanak |
|---|---|---|
| HTTP | İstek/yanıt satırındaki sürüm (`HTTP/1.1`, `HTTP/2`); içerik pazarlığı `Accept` | [S3] |
| gRPC | Alt protokol/mekanizma olarak **content-coding** metadata'sı ile sıkıştırma pazarlığı; sürümleme `.proto` alan numaralarıyla **ileriye dönük uyum** sağlanır (eski alanlar atlanabilir) | [S4], [S12] (protobuf wire format: tag ile alan atlanabilir) |

> **⚠️ VERIFICATION REQUIRED:** gRPC istemcisinin sürüm/greater-compatibility
> pazarlığının resmi sayfası bu turda çekilmedi; satır yalnızca
> [S4] (metadata) + [S12] (alan atlanabilirliği) ile sınırlıdır.

### §2.3 Hata çevirisi

| Yüz | Hata biçimi | Dayanak |
|---|---|---|
| HTTP | Durum kodları 4xx/5xx (ör. 400, 401, 403, 413, 415, 422, 500, 502, 503) | [S3] |
| gRPC | RPC durumu: durum kodu + opsiyonel mesaj + trailing metadata (status details) | [S4] |
| Eşleme tablosu | Resmi eşleme sayfası bu turda çekilmedi → **⚠️ VERIFICATION REQUIRED** (uydurulmadı) | — |

---

## §3 P2 — REST ↔ SOAP Sözleşmesi

### §3.1 Tanım katmanı (WSDL)

WSDL 2.0 sözleşmeyi **iki aşamada** yazar (dayanak [S1]):

| Aşama | Bileşen | Ne taşır | Çevirmen için anlamı |
|---|---|---|---|
| Soyut | `interface` + operasyon + mesaj referansları | **Ne** çağrılır (taşımadan bağımsız) | REST yüzüne eşlenecek operasyon kümesi |
| Somut | `binding` + `endpoint` | **Nasıl ve nerede** (taşıma + tel biçimi) | SOAP yüzünün tel kuralı |
| Toplama | `service` | endpoint'ler kümesi | adres bilgisi (çevirmenin değil, taşımanın işi → K014) |

### §3.2 Mesaj ve MEP

| Kavram | SOAP yüzü | REST yüzü | Dayanak |
|---|---|---|---|
| Zarf | `env:Envelope` → `env:Header` + `env:Body` (NS `http://www.w3.org/2003/05/soap-envelope`) | HTTP mesajı: start-line + alanlar + gövde | [S5], [S3] |
| Medya tipi | `application/soap+xml` | `application/json` / `application/xml` / `+xml` | [S5], [S6], [S7] |
| Mesaj değişimi | MEP (message exchange pattern) — WSDL Part 2 Adjuncts operasyon başına tanımlar | Yöntem semantiği (safe/idempotent) ile belirlenir | [S5], [S1] |
| Hata | `env:Fault` — `env:Sender` / `env:VersionMismatch` / `env:Receiver` + `mustUnderstand` davranışı | HTTP durum kodu + gövde | [S5], [S3] |
| Zorunlu işleme | Header'da `mustUnderstand="1"` → alıcı anlam **zorunda** | Yok karşılığı — alan bilinmiyorsa genelde yok sayılır | [S5] · ⚠️ eşleme kararı TASARIM İDDİASI |

> **Eşleme kararı (etiketli):** SOAP `mustUnderstand` için HTTP yüzünde
> birebir karşılık **yoktur**; bu mimaride `mustUnderstand` hataları
> **400/422 sınıfı** istemci hatasına çevrilir — **TASARIM İDDİASI**
> (şartname eşlemesi değildir).

---

## §4 P3 — JSON ↔ XML Sözleşmesi

### §4.1 Sözdizimi sözleşmesi

| Konu | JSON [S6] | XML [S7] | Çeviri notu |
|---|---|---|---|
| Belge tipi | `application/json` | `application/xml`, `text/xml`, `+xml` | Medya tipi çevirisi zorunlu (RFC 7303) |
| Kodlama | Ağda UTF-8; BOM eklenmez | BOM > charset parametresi otoriter; UTF-8+BOM'suz RECOMMENDED | BOM kararı çevirmenin |
| Anahtar/etiket | Üyelerin adları SHOULD unique; string | Element adları namespace + QName | JSON `a.b` → XML `a` içine `b` gibi öneriler **TASARIM İDDİASI** |
| Sayı | Leading zero yok; Infinity/NaN yok | Şekil/alan kısıtları schema ile | Sayı kaybı riski → doğrulama katmanına (dogrulama.md) |
| Boolean/null | `true`/`false`/`null` lowercase | Yok — `xsi:nil` veya element yokluğu [S8] | `null` ↔ element yokluğu / `xsi:nil` kararı **TASARIM İDDİASI** |
| Şema | (JSON Schema — açık soru) | XML Schema Part 1: bileşen modeli, simple/complex type, identity-constraint [S8] | Şema eşlemesi ⚠️ UNKNOWN |

### §4.2 Kayıt sırası

JSON nesneleri **sırasızdır** (RFC 8259 sözdizimi sırayı anlamlı kılmaz) [S6];
XML element sırası **anlamlıdır** (model group `sequence`) [S8]. Bu asimetri
çevirmenin **alan sırasını yeniden kurması** gerektiğini doğurur —
karar (alfabetik/şema sırası) → **UNKNOWN (ADR bekliyor)**.

---

## §5 P4 — SSE ↔ WebSocket Sözleşmesi

### §5.1 Çerçeveleme sözleşmesi

| Konu | SSE yüzü [S9] | WebSocket yüzü [S10] | Çeviri |
|---|---|---|---|
| Biçim | Satırlar: `field: value` + boş satırda dispatch | Çerçeveler: metin/binary/kontrol | `data:` bloğu → WS mesajı; WS mesajı → `data:` satırları |
| Kodlama | **Yalnız UTF-8** | Metin çerçevesi UTF-8 | Kodlama birebir |
| Olay tipi | `event:` alanı (varsayılan `message`) | Alt protokol/uygulama yükü (mesaj tipi belirteci yok) | Olay tipi WS'te **yük içinde** taşınır → TASARIM İDDİASI |
| Sonlu akış | `retry:` + `Last-Event-ID` ile reconnect | Ping/pong + Close kodları | Yeniden bağlanma semantiği farklı → eşleme §5.3 |
| Keep-alive | `:` yorum satırları (15 sn önerisi) | Ping kontrol çerçevesi | İkisi de keep-alive → eşleme TASARIM İDDİASI |
| Bitirme | HTTP 204 → reconnect yok | Close çerçevesi (kod + sebep) | 204 ↔ Close normal (1000) eşlemesi TASARIM İDDİASI |

### §5.2 El sıkışma sözleşmesi (WS yüzü, [S10])

| Adım | İstemci | Sunucu |
|---|---|---|
| 1 | `GET` + `Upgrade: websocket` + `Connection: Upgrade` + `Sec-WebSocket-Key` (16 bayt rastgele, base64) + `Sec-WebSocket-Version: 13` | — |
| 2 | — | `101 Switching Protocols` + `Sec-WebSocket-Accept` = base64(SHA-1(key + GUID `258EAFA5-E914-47DA-95CA-C5AB0DC85B11`)) |
| 3 | Accept doğrular → OPEN | Alt protokol seçimi `Sec-WebSocket-Protocol` (istemcinin listesinden biri) |

> **Sınır notu (proto-anayasa §4 ile tutarlı):** Accept **hesabı** biçim
> bilgisidir (bu düğüm), `101` üzerinden **bağlantı kurma** K014'tür —
> sınır kararı el sıkışma bütünlüğü nedeniyle **⚠️ açık soru** olarak
> taşındı (index §A).

### §5.3 Yeniden bağlanma eşlemesi (TASARIM İDDİASI)

| SSE olayı | WS karşılığı | Etiket |
|---|---|---|
| Bağlantı koptu → reconnect + `Last-Event-ID` | Close (1001 vs.) → uygulama seviyesinde yeniden bağlanma + son mesaj id'si | TASARIM İDDİASI |
| 204 → reconnect yok | Close 1000 (normal) → yeniden bağlama | TASARIM İDDİASI |
| `retry:` ms | WS'te karşılığı yok (uygulama backoff'u) → K014'e devir | TASARIM İDDİASI |

---

## §6 P5 — OAuth Bearer ↔ API Key Sözleşmesi

| Konu | Bearer yüzü (RFC 6750) [S11] | API key yüzü | Dayanak |
|---|---|---|---|
| Taşıma | 3 yöntem: header (zorunlu destek), form gövdesi, query | Sistem belirler → **⚠️ UNKNOWN** (şartname yok) | [S11] |
| Şema | `Authorization: Bearer b64token` | Örn. özel header (format bilinmiyor → uydurulmaz) | [S11] |
| Çoklu yöntem | Aynı istekte **birden fazla yöntem yasak** | — | [S11] |
| Hata | `WWW-Authenticate: Bearer` + `error` = `invalid_request`/`invalid_token`/`insufficient_scope` + HTTP 400/401/403 | Özel hata gövdesi → eşleme **TASARIM İDDİASI** | [S11] |
| TLS | Zorunlu | Genelde zorunlu (yine de format şartname değil) | [S11] |
| Ömür/scope | Kısa ömürlü (≤1 saat) önerisi; audience scope | Anahtar genelde uzun ömürlü → **fark semantiği**: dönüşüm "token↔key" eşdeğerliği **sağlamaz** | [S11] · etiketli uyarı |

> **Güvenlik uyarısı (şartnameden, [S11]):** Bearer token URL'de
> taşımamalıdır (log'lara düşer); API key'e çevirirken query yöntemine
> **önce** geçmek RFC 6750'e aykırı bir kötüleşmedir — dönüştürücü bunu
> engellemelidir (TASARIM İDDİASI).

---

## §7 Ortak Sözleşme Kuralları (beş çifte uygulanır)

| # | Kural | Dayanak |
|---|---|---|
| 1 | Medya tipi her çeviride yeniden yazılır (`Content-Type`) | [S3], [S6], [S7] |
| 2 | Karakter seti kararı verilir ve **tek** tarafta uygulanır (UTF-8 varsayılanı) | [S6], [S7], [S9] |
| 3 | Sürüm bilgisi ya pazarlıkla ya zarfta taşınır | [S3] (HTTP), [S10] (WS version 13), [S4] (proto uyumu) |
| 4 | Hata çevirisi **asla yutulmaz**: her yanıt kodu karşılığını bulur ya da `⚠️` ile işaretlenir | [S3], [S5], [S11] |
| 5 | Kayıp/bilinmeyen alan: XML'de yok sayma, JSON'da **bilinmeyen üye tutma** tercihi → TASARIM İDDİASI | etiketli |
| 6 | Sözleşme tanımı tek kaynaktan okunur (WSDL / `.proto` / şartname); **çift yazılmaz** (SSOT) | TASARIM İDDİASI · [S1] |

---

## §7.1 Ortak Kural-1 Yorumu — Medya Tipi Yazımı (her çeviride)

| Adım | Kural | Hata örneği | Dayanak |
|---|---|---|---|
| 1 | Gelen `Content-Type` **okunur** (parametreleriyle) | Sadece subtype'a bakıp `;charset=` yutmak | [S6], [S7] |
| 2 | Giden tip **yeni yazılır** — kopyalanmaz | `application/json`'u XML yanıtına taşımak | [S3] |
| 3 | Charset/BOM kararı tek kez verilir | İki imas bir arada (charset + BOM çelişkisi) | [S7], [S8] |
| 4 | Bilinmeyen tip → 415 (tasarım) ya da passthrough → karar belgede | Sessiz kabul | [S3] · politika TASARIM |

---

## §7.2 Ortak Kural-3 Yorumu — Sürüm Taşıma Üç Yolu

| Yol | Mekanizma | Hangi çiftte doğal | Dayanak |
|---|---|---|---|
| Pazarlık başlığı | `Accept` / `Upgrade` / alt protokol listesi | P1, P3, P4 | [S3], [S9], [S10] |
| Zarfta taşıma | Mesajın içinde sürüm alanı (ör. `specversion`) | Sarmalayıcı desenler (CloudEvents vb.) | ⚠️ bu dosyada URL yok — desen `proto-anayasa.md` §4, kanıt `proto-web-arastirma.md` S15 |
| Uyumlu ilerleme | Alan ekleme = geriye dönük uyum; silme/yeniden yorum = kırıcı | P1 (protobuf), P2 (WSDL binding) | [S12], [S1] |

> **⚠️ Kırıcı eşik:** "Hangi değişiklik kırıcıdır" için **birebir
> şartname satırı bu dosyada yok** — tablo [S12]'deki alan atlama
> kuralından **türetilmiştir** (etiket: TASARIM İDDİASI).

---

## §7.3 Ortak Kural-4 Yorumu — Hata Asla Yutulmaz Matrisi

| Durum | Yapılacak | Yapılmayacak |
|---|---|---|
| Kaynakta hata var, hedefin kodu yok | `⚠️` + en yakın kod + özgün kod gövdede korunur | 200'e çevirmek |
| Kaynakta hata yok, hedef üretti | Hedefin kodu aynen döner | Kaynağa mal etmek (yanlış kaynak etiketi) |
| Çeviri sırasında hata oluştu | 500/502 sınıfı + `⚠️` işareti | Sessizce boş yanıt (§6 L5 ihlali) |
| Hata gövdesi şeması bilinmiyor | `UNKNOWN` — gövde şeması edilmez | Problem Details **olduğunu** iddia etmek |

> Son satır bilinçlidir: CoreMusic hata gövdesi şeması bu dosyanın
> `open_questions` 1. maddesidir (`UNKNOWN`) — `proto-dogrulama.md` §9.1
> eşleme tablosundaki hücreler de bu yüzden **TASARIM İDDİASI**'dır.

---

## §7.4 Ortak Kural-5 Yorumu — Bilinmeyen Alan / Üye Politikası

| Yön | Varsayılan | Sonuç | Etiket |
|---|---|---|---|
| JSON → XML | Bilinmeyen JSON üyesi XML elementine dönüştürülür (yapı genişler) | Kayıp yok | TASARIM İDDİASI |
| XML → JSON | JSON'da karşılığı olmayan element **tutulur** (bilgi kaybı önlenir) | "unknown" anahtarı | TASARIM İDDİASI |
| protobuf → herhangi | Bilinmeyen **alan numarası** atlanır (şartname buysa) | Bilgi kaybı meşru | [S12] |
| Her iki yön | Düşen alanlar §6 L5 testinde **liste olarak raporlanır** | Kayıp görünür olur | dogrulama §6 |

---

## §7.5 Ortak Kural-6 Yorumu — SSOT ve Çift Yazılmazlığı

| Kural | Uygulama | İhlal belirtisi |
|---|---|---|
| Tek tanım | Operasyon kümesi tek kaynaktan okunur: `.proto` / WSDL / şartname | Aynı operasyonun iki farklı tanımı |
| Çeviri katmanı yazmaz | Çevirmen yalnız **eşleme** tutar, tanım **kopyalamaz** | Eşleme ile tanımın senkron kayması |
| Sözleşme değişirse | Önce kaynak güncellenir, sonra eşleme | Eşleme önce güncellenmişse → drift |
| Doğrulama | Round-trip testi (§6 L5) kırıldığında drift sinyali | Test yoksa drift **görünmez** → ⚠️ |

> **⚠️ Repo durumu:** CoreMusic'te `.proto`/WSDL envanteri bu turda
> **aranmadı** → "tek tanım" kuralının mevcut uygulaması
> `⚠️ VERIFICATION REQUIRED` (`proto-dogrulama.md` §10 madde 1-3 ile
> birlikte okunur).

---

## §8 İlgili Dosyalar ve Düğümler

### §8.1 Dosya Bağımlılık Matrisi

| Hedef | İlişki | Bu dosyanın ondan aldığı | Ona verdiği |
|---|---|---|---|
| `proto-anayasa.md` | yukarı | Çift envanteri ve kimlik kartları (girdi) | Sözleşme yüzeyi (§1-§6) |
| `proto-dogrulama.md` | aşağı | — | Denetlenecek kurallar (L1-L6 girdisi) |
| `proto-kod-ornekleri.md` | aşağı | — | Eşlemelerin dile dökümü |
| `proto-web-arastirma.md` | yan | Doğrulanamayan satırların `⚠️` listesi | İddia başlıkları (S1-S12) |
| `K013-integraion/api-contracts/openapi` | kardeş | HTTP yüzü sözleşmesi (Accept/Content-Type ortaklığı) | Protokol çifti sınırı (P1-P5) |
| `K013-integraion/adapters` | kardeş | Sınır tanımı (protokol ≠ sistem/ACL) | Protokol düzlemi referansı |
| `K010-network` | komşu | Taşıma kapsam dışı devri | El sıkışma/bağlantı NOTU (§5.2) |

### §8.2 Okuma Sırası Önerisi (bu klasör)

`index.md` → `proto-anayasa.md` → **`proto-sozlesme.md` (bu dosya)** →
`proto-dogrulama.md` → `proto-kod-ornekleri.md` → `proto-web-arastirma.md`.

> Sıra gerekçesi: sözleşme (ne konuşulur) doğrulamadan (nasıl denetlenir)
> önce okunur; kanıt dosyası sonradan **kontrol** içindir, öğrenme için
> değil (kalıp: `openapi/index.md` okuma sırası).

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | WSDL 2.0 soyut/somut ayrımı, binding = taşıma + tel biçimi | https://www.w3.org/TR/wsdl20/ | 2026-10-16 | ✅ çekildi |
| S2 | SOAP MEP (mesaj değişimi desenleri) | https://www.w3.org/TR/SOAP12/ | 2026-10-16 | ✅ çekildi |
| S3 | HTTP semantik: method, status, alanlar, içerik pazarlığı | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S4 | gRPC: servis tanımı, streaming, metadata, deadline/status | https://grpc.io/docs/what-is-grpc/core-concepts/ | 2026-10-16 | ✅ çekildi |
| S5 | SOAP 1.2: envelope, mustUnderstand, Fault kodları, medya tipi | https://www.w3.org/TR/SOAP12/ | 2026-10-16 | ✅ çekildi |
| S6 | JSON sözdizimi, UTF-8, unique member names | https://www.rfc-editor.org/rfc/rfc8259.html | 2026-10-16 | ✅ çekildi |
| S7 | XML medya tipleri, BOM/charset otoritesi, `+xml` | https://www.rfc-editor.org/rfc/rfc7303.html | 2026-10-16 | ✅ çekildi |
| S8 | XML Schema Part 1: model group/sequence, xsi:nil, tipler | https://www.w3.org/TR/xmlschema-1/ | 2026-10-16 | ✅ çekildi |
| S9 | SSE: `text/event-stream`, alanlar, 204/301/307, UTF-8 | https://html.spec.whatwg.org/multipage/server-sent-events.html | 2026-10-16 | ✅ çekildi |
| S10 | WebSocket: handshake, Accept hesabı, sürüm 13, kapanış | https://www.rfc-editor.org/rfc/rfc6455.html | 2026-10-16 | ✅ çekildi |
| S11 | Bearer: 3 yöntem, çoklu yöntem yasağı, hata kodları, URL uyarısı | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-16 | ✅ çekildi |
| S12 | protobuf wire format: tag ile alan atlanabilirlik (ileriye dönük uyum) | https://protobuf.dev/programming-guides/encoding/ | 2026-10-16 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `TASARIM İDDİASI` etiketli satırlar dış kaynaksız mimari kararlardır;
> `⚠️ VERIFICATION REQUIRED` satırlarında kaynak yoktur ve uydurulmamıştır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · W3C/IETF/WHATWG/grpc.io · **Tarih: 2026-10-16**

---

## EK-D-2026-10-18 — Sözleşme derinleştirmesi (§D1–§D8)

> Append-only ek; §0–§354 silinmedi/değiştirilmedi. Ek kaynak kısaltmaları
> `../integration-master.md` §19 ana tablosundandır (W-04 gateway routing ·
> W-08 GitHub imza · W-12 Stripe imza · W-14 RFC 9457 problem details ·
> W-15 RFC 6585 ek durum kodları · W-05 circuit breaker).

## §D1 Gateway routing sözleşmesi (W-04 — API Gateway pattern)

W-04'ün tarifi: gateway, istemciye **tek kapıyı** gösterir; yönlendirme,
kimlik doğrulama ve rate limit kapıda toplanır; arka servisler adreslenmez.

| # | Kural | Değer | İddia tipi |
|---|---|---|---|
| 1 | İstemci, arka servis adresini **asla** bilmez | yalnız gateway host'u | ✅ W-04 |
| 2 | Routing kuralı | host/path eşlemesi — eşlemeyen istek 404 | TASARIM İDDİASI |
| 3 | Timeout katmanı | gateway < servis timeout'u (gateway servisten **kısa** yaşar) | TASARIM İDDİASI — W-04 süre değeri vermez, sayısal ⚠️ |
| 4 | Rate limit kapıda | K009 politikası — §1/3 bağımlılığı | TASARIM İDDİASI |
| 5 | Hata gövdesi | RFC 9457 problem+json (§D4) | ✅ W-14 |
| 6 | Internal hata sızıntısı | 5xx gövdesinde stack/detay **YOK** | TASARIM İDDİASI (güvenlik) |

### §D1.1 Gateway timeout eşitsizliği (kritik kural — iddia)

```text
istemci timeout  >  gateway timeout  >  servis timeout  >  DB timeout
   (30 sn)            (25 sn)            (20 sn)           (5 sn)
```

Zincir ters çevrilirse (istemci servisten kısa olursa) **istemci yeniden dener
ama servis işlemeye devam eder** — çift-işlem riski; inbox (W-10) bunu kapatır
ama gateway timeout sırası **ilk savunma hattıdır**.

## §D2 HMAC webhook sözleşmesi — biçim birleşimi (W-08 + W-12)

İki doğrulanmış biçim yan yana (asla birleştirilmez):

| Özellik | Stripe (W-12) | GitHub (W-08) |
|---|---|---|
| Başlık | `Stripe-Signature: t=<ts>,v1=<hex>` | `X-Hub-Signature-256: sha256=<hex>` |
| İmalatçı | `t` + `.` + ham gövde | yalnız ham gövde |
| Zaman damgası | ✅ imzada — tolerans 5 dk | ❌ imza başlıkta yok — ayrı zaman kontrolü gerekir |
| tolerans=0 | ❌ yasak | — (zaman alanı yok) |
| Çoklu imza | çoklu `v1=` — **hiçbiri eşleşmezse** red | tek imza |
| Test vektörü | ⚠️ VERIFICATION REQUIRED (sayfasında açık vektör yok) | ✅ secret `It's a Secret to Everybody` + `Hello, World!` → `sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17` |
| Canlı retry | ✅ 3 güne kadar exponential backoff | ⚠️ VERIFICATION REQUIRED |
| Manuel retry | Dashboard 15 gün / CLI 30 gün | ⚠️ |
| Olay sırası | ❌ **garanti edilmez** — alıcı sıra-dışı işlenmeli | ⚠️ |

**Birleştirme yasağı (TASARIM İDDİASI):** iki biçim tek başlıkta **karıştırılmaz**;
adaptör hangi sağlayıcıdan geldiğini **medya tipi/headers** ile ayırır, yanlış
biçim doğrulaması sessizce geçilmez (§D6).

## §D3 DTO/API versioning sözleşmesi (W-04 + S12 — protobuf ileriye-dönük uyum)

| # | Kural | Kaynak | İddia |
|---|---|---|---|
| 1 | Yalnız **additive** değişiklik backward-uyumludur | S12 (protobuf tag atlaması) | ✅ |
| 2 | Alan çıkarımı/adi değişimi → **yeni sürüm** | S12 tersi | ✅ |
| 3 | Sürüm alanı şemada zorunlu: `event_version` / `api-version` | KAPI-2 | TASARIM İDDİASI |
| 4 | Eski sürüm yaşam süresi: en az bir geçiş dönemi | W-12'nin canlı-retry süresiyle hizalı | TASARIM İDDİASI |
| 5 | JSON'da `unique member names` (S6) — aynı ada iki alan YOK | ✅ S6 | ✅ |
| 6 | Upcaster zinciri alıcıdadır; üretici eski sürümü **bilmez** | KAPI-2 | TASARIM İDDİASI |

## §D4 Hata sözleşmesi — RFC 9457 problem+json (W-14)

W-14 (RFC 9457): hata yanıtı `application/problem+json` medya tipiyle taşınır;
`type` · `title` · `status` · `detail` · `instance` alanları tanımlıdır.

```json
{
  "type": "https://coremusic.net/probs/webhook-imza-gecersiz",
  "title": "Webhook imzası doğrulanamadı",
  "status": 401,
  "detail": "İmza başlığı biçimi beklenen şablonla uyuşmuyor.",
  "instance": "/webhooks/stripe",
  "trace_id": "…"
}
```

| # | Alan | Kural | İddia |
|---|---|---|---|
| 1 | `type` | sabit URI — problem kodunun kimliği | ✅ W-14 |
| 2 | `title` | kısa, sabit, istemci-dostu | ✅ W-14 |
| 3 | `status` | HTTP durumu ile aynı | ✅ W-14 |
| 4 | `detail` | istek-özel açıklama — **sırrı ifşa etmez** | TASARIM İDDİASI |
| 5 | `instance` | hangi uçta hata oldu | ✅ W-14 |
| 6 | `trace_id` | operasyon köprüsü (§9 kopuş analizi) | TASARIM İDDİASI |

## §D5 Durum kodu sözleşmesi (S3 RFC 9110 + W-15 RFC 6585)

| Kod | Kullanım (gateway) | Kaynak |
|---|---|---|
| 200 | başarı — iş tamam | ✅ S3 |
| 202 | alındı-işleniyor (asenkron outbox) | ✅ S3 |
| 400 | imza başlığı biçimi geçersiz | ✅ S3 |
| 401 | imza geçersiz / süresi aşılmış | ✅ S3 |
| 409 | idempotency ihlali (aynı anahtar-farklı gövde) | TASARIM İDDİASI — §8/3 |
| 422 | şema doğrulama hatası (RFC 9457 body) | TASARIM İDDİASI |
| 429 | rate limit (W-15) | ✅ W-15 |
| 500 | iç hata — detaysız (§D1/6) | ✅ S3 |
| 503 | devir-bırak (circuit breaker açık — W-05) | ✅ W-15 |

## §D6 Doğrulama sırası — hangi kapı önce (TASARIM İDDİASI)

```text
1. taşıma doğruluğu (TLS — K010)
2. boyut sınırı (body limit — aşarsa 413, imzaya GECİLMEZ)
3. imza doğrulama (ham gövde — §D2)
4. zaman penceresi (Stripe t= — tolerans 5 dk, 0 YASAK)
5. idempotency (inbox — W-10)
6. şema doğrulama (event-catalog — KAPI-2)
7. iş mantığı (K012)
```

**Sıra kuralı (iddia):** 2–4 **önce** gelir; imza doğrulanmadan iş mantığına
**hiçbir** veri ulaşmaz. Sıra ters çevrilirse (iş önce, imza sonra) işlenmemiş
zararlı veri yan etki üretir — bu ters-sıra **hiçbir §'de kabul edilmez**.

## §D7 Servis-kontrat sözleşmesi (K012 ↔ K013 — §10 yönünün sözleşmesi)

| # | Şart | Biçim | İddia |
|---|---|---|---|
| 1 | K012, K013'ün **arayüzünü** bilir | interface/port | TASARIM İDDİASI (§10) |
| 2 | Adaptörler arayüzü uygular | port/adapter | TASARIM İDDİASI |
| 3 | Kontrat değişimi yalnız dondurma kapısından (KAPI-1) | sürüm + additive | TASARIM İDDİASI |
| 4 | Kontrat testi CI'da (§35 YAML kapısı) | vektör testi | ✅ kod örnekli (§35) |
| 5 | Internal hata tipi dışarı **çıkmaz** | yalnız problem+json | TASARIM İDDİASI (§D4) |

## §D8 Ek kapanışı

1. §D1–§D7 ekler yalnız **ekleme**tir; §0–§354 korunmuştur (append-only).
2. Dayanaklar: W-04 · W-05 · W-08 · W-12 · W-14 · W-15 · S1–S12 (bu dosyanın
   kendi kaynak tablosu) — `../integration-master.md` §19 ana tablosu.
3. `⚠️ VERIFICATION REQUIRED` işaretli satırlar: Stripe test vektörü · GitHub
   canlı retry süresi · gateway timeout sayısal değerleri — **uydurulmadı**.

---

*EK-D-2026-10-18 · proto-sozlesme.md · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*

---

## §D9 EK-D eklemesi — §D1–§D8 kapı denetimi (2026-10-18 sayımı)

| Kapı | §D karşılığı | Durum |
|---|---|---|
| KAPI-1 | §D3/3 (`event_version` alanı) | ✅ eşleşti |
| KAPI-2 | §D3/6 (upcaster alıcıda) | ✅ eşleşti |
| KAPI-3 | §D2 (iki biçim, birleştirme yasağı) | ✅ eşleşti |
| KAPI-4 | §D7/2 (port/adapter) | ✅ eşleşti |
| KAPI-5 | §D6/5 (idempotency sonrası iş) | ✅ eşleşti |
| KAPI-6 | §D5/429 · §D4 (problem+json) | ✅ eşleşti |
| KAPI-7 | §D1/3 timeout eşitsizliği + jitter | ✅ eşleşti |

Denetim sonucu: §D1–§D8'deki 7 kapı da `../index.md` K013 kapı listesiyle
**çelişmiyor**; kapı-özürlük (gate autonomy) ihlali **0**.

---

*EK-D-2 · proto-sozlesme.md · 2026-10-18 · Vault Steward*
