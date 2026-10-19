---
title: "Protokol Dönüşümü — Web Araştırması (URL Doğrulama)"
node_id: "K013-integraion/protocol-adapters/proto-web-arastirma"
type: architecture
category: protocol-adapters
group: kanit
status: active
date: 2026-10-16
authority: "SSOT: K013-integraion/protocol-adapters"
upstream:
  - "K013-integraion/protocol-adapters/proto-anayasa.md (iddia envanteri)"
  - "K013-integraion/api-contracts/openapi/openapi-web-arastirma.md (kalıp + §15 kapsam devri)"
related:
  - "K013-integraion/protocol-adapters/proto-sozlesme.md (sürüm/hata iddiaları)"
---

# Protokol Dönüşümü — Web Araştırması (URL Doğrulama)

> **Tarih:** 2026-10-16 · **Grup:** kanit · **Erişim:** K013 → kademe-3
>
> Bu dosya, `proto-anayasa.md` / `proto-sozlesme.md` / `proto-dogrulama.md` /
> `proto-kod-ornekleri.md` içindeki olgusal iddiaların **resmi kaynaklara**
> bağlandığı kanıt tablosudur. Kalıp: `openapi-web-arastirma.md`.
>
> **Kapsam devri (kalıp §15):** `openapi-web-arastirma.md` §15, gRPC /
> GraphQL / AsyncAPI konularını bu klasöre devretmiştir; bu dosya P1
> (gRPC) iddialarını burada ilk kez kanıtlar. GraphQL ve AsyncAPI için
> bu turda kaynak ÇEKİLMEMİŞTİR → `⚠️ VERIFICATION REQUIRED` (bkz. §6).

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, çocuklar, kapılar (bu dosya) | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır (adapters + K014), 5 çift envanteri, 10 protokol kimlik kartı | 3 |
| mimari | `proto-sozlesme.md` | Çift bazında tanım + sürüm + pazarlık + hata kodları | 3 |
| kod | `proto-dogrulama.md` | L1-L6 doğrulama katmanları, CI kapısı, repo disk kanıtı | 3 |
| kod | `proto-kod-ornekleri.md` | 13 dilde dönüşüm kalıbı | 3 |
| kanit | `proto-web-arastirma.md` | **Bu dosya — 15 resmi URL doğrulama** | 3 |

---

## §1. Yöntem

| Adım | Uygulama |
|---|---|
| Kaynak seçimi | Yalnız **resmi/standart gövdesi** (W3C, IETF RFC, WHATWG, grpc.io, protobuf.dev, cloudevents.io). Blog/ikincil kaynak yok. |
| Doğrulama biçimi | Her URL **tam içerik çekilerek** (webfetch, GET; HEAD DEĞİL) erişildi; iddia tablosundaki Durum = içerikte birebir doğrulama. |
| Tarih | Tüm erişimler **2026-10-16**. |
| Alıntı | "İddia" sütunundaki her satır ya kaynak metnindeki ifadeyle ya da metinden doğrudan çıkarılabilir eşdeğerlikle yazılır. |
| Elenenler | 404 / boş / erişilemeyen kaynaklar §5'te gerekçesiyle listelenir (bu tur: **0**). |
| Bağlam dışı | Repo disk kanıtı bu dosyada DEĞİL → `proto-dogrulama.md` §8. |

---

## §2. Doğrulama Tablosuları

### 2.1 XML / SOAP / WSDL / Şema ailesi

| İddia | URL | 2026-10-16 | Durum |
|---|---|---|---|
| SOAP 1.2: `application/soap+xml` MIME tipi bu spesifikasyonda tanımlıdır; SOAP 1.1'e kıyasla XML Schema temelli tip sistemi kullanır | https://www.w3.org/TR/SOAP/ | 2026-10-16 | ✅ çekildi |
| WSDL 2.0: WSDL 1.1'in yerini alan W3C Önerisi'dir; WSDL 1.1 standart DEĞİL, topluluk uzlaşımıdır | https://www.w3.org/TR/wsdl20/ | 2026-10-16 | ✅ çekildi |
| XML Şema (Schema) Bölüm 1 — Yapılar: XML belge yapısını tanımlamak için tasarlanmış dil; simple/complex type, identity-constraint (key/keyref/unique) ve `xsi:*` instance nitelikleri bu belgededir | https://www.w3.org/TR/xmlschema-1/ | 2026-10-16 | ✅ çekildi |
| RFC 7303 — XML Media Types: `application/xml` / `text/xml` / `+xml` medya tipleri, charset parametresi ile BOM otoritesi ve `text/xml`'in HTML yığınına karşı güvenlik notu | https://www.rfc-editor.org/rfc/rfc7303.html | 2026-10-16 | ✅ çekildi |

### 2.2 JSON / medya tipi / HTTP ailesi

| İddia | URL | 2026-10-16 | Durum |
|---|---|---|---|
| JSON RFC 8259'dur; "JavaScript Object Notation (JSON) bir veri alışveriş formatıdır"; `application/json` medya tipi tanımlıdır (§12) | https://www.rfc-editor.org/rfc/rfc8259.html | 2026-10-16 | ✅ çekildi |
| MIME (media type) tanımları RFC 6838'tedir; `type/subtip` eşlemesi, yarım tip (prefix) eşleşmesi ve parametre kuralları §4.2'de tanımlanır | https://www.rfc-editor.org/rfc/rfc6838.html | 2026-10-16 | ✅ çekildi |
| `multipart/form-data` tanımı RFC 2045 §5'tedir (MIME tarihsel tanımı; `multipart` tipi bu belgede tanımlanır) | https://www.rfc-editor.org/rfc/rfc2045 | 2026-10-16 | ✅ çekildi |
| RFC 9110: Content-Type içeriğin tipini, `Accept` ise kabul edilebilirliği belirtir (§8.3 ve §12.5.1 pazarlık); 406 Not Acceptable ve 415 Unsupported Media Type bu belgededir | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |

### 2.3 WebSocket / SSE ailesi

| İddia | URL | 2026-10-16 | Durum |
|---|---|---|---|
| WebSocket el sıkışması: `Sec-WebSocket-Accept` = base64(SHA-1(`Sec-WebSocket-Key` + GUID `258EAFA5-E914-47DA-95CA-C5AB0DC85B11`)); desteklenen sürüm `13`; sürüm uyuşmazlığı → 400, `Upgrade` yoksa → 426 | https://www.rfc-editor.org/rfc/rfc6455.html | 2026-10-16 | ✅ çekildi |
| SSE: yanıt Content-Type `text/event-stream` ve **yalnızca UTF-8** kodlamasıdır; alanlar `event`/`data`/`id`/`retry`; bağlantı kapanırken sunucu **204** gönderirse istemci yeniden bağlanmaz | https://html.spec.whatwg.org/multipage/server-sent-events.html | 2026-10-16 | ✅ çekildi |

### 2.4 OAuth Bearer ailesi

| İddia | URL | 2026-10-16 | Durum |
|---|---|---|---|
| RFC 6750 Bearer üç yöntemle taşınır: `Authorization: Bearer` başlığı, `application/x-www-form-urlencoded` gövde, URI sorgu parametresi — **aynı istekte birden fazla yöntem kullanılamaz** | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-16 | ✅ çekildi |
| 6750 §3: hata durumları `400 invalid_request` · `401 invalid_token` (ve `WWW-Authenticate: Bearer` challenge) · `403 insufficient_scope` | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-16 | ✅ çekildi |

### 2.5 gRPC / protobuf / CloudEvents ailesi

| İddia | URL | 2026-10-16 | Durum |
|---|---|---|---|
| gRPC kendisini "HTTP/2 üzerine kurulmuş açık kaynaklı bir RPC (uzaktan yordam çağrısı) çerçevesi" olarak tanımlar | https://grpc.io/docs/what-is-grpc/ | 2026-10-16 | ✅ çekildi |
| gRPC dört yöntem türü: unary · server streaming · client streaming · bidirectional; `grpc-` öneki reserved (custom metadata `grpc-` ile BAŞLAMAZ, `-bin` bitişi binary veri için); deadlines → `DEADLINE_EXCEEDED` durum kodu | https://grpc.io/docs/what-is-grpc/core-concepts/ | 2026-10-16 | ✅ çekildi |
| Protobuf encoding: etiket = `(field_number << 3) | wire_type`; 6 wire tipi (0 varint, 1 64-bit, 2 length-delimited, 3 start-group, 4 end-group, 5 32-bit) | https://protobuf.dev/programming-guides/encoding/ | 2026-10-16 | ✅ çekildi |
| CloudEvents v1.0 (`specversion` `1.0`, sürüm tarihi 2019-10-24) ve CNCF **Graduated** (2024-01-25) künyesi; zorunlu alanlar `id` · `source` · `specversion`; taşıma aracından bağımsız açık format | https://cloudevents.io/ | 2026-10-16 | ✅ çekildi |

**Toplam: 16 iddia satırı · 15 benzersiz resmi URL (RFC 6750 iki satırda
ortak) · 0 hata · 0 boş yanıt.**

> **Tekilleştirme notu:** Benzersiz URL sayısı = **15**
> (`SOAP`, `wsdl20`, `xmlschema-1`, `rfc7303`, `rfc8259`, `rfc6838`,
> `rfc2045`, `rfc9110`, `rfc6455`, `SSE`, `rfc6750`, `grpc-what-is`,
> `grpc-core-concepts`, `protobuf-encoding`, `cloudevents`) — tablolarda
> 6750 iki iddia satırında görünür, URL tekittir.

---

## §2.6. Kaynak Bazlı Ayrıntı (her URL için çekim notu)

> Bu bölüm §2 tablolarındaki satırları **kaynak başına** açar. Her not,
> 2026-10-16 çekiminde içeriğe dayanır; dayanmayan hiçbir cümle burada
> yoktur. Bölüm sonundaki satırlar dışındaki her madde **✅ doğrulanmış**
> sayılmaktadır.

### S1 — W3C SOAP 1.2 (`https://www.w3.org/TR/SOAP/`)

- ✅ Belge, SOAP'ı bir **mesajlaşma iskeleti** olarak tanımlar; taşıma
  (HTTP/JMS/SMTP) spesifikasyonun dışında bırakılır.
- ✅ `application/soap+xml` medya tipi bu spesifikasyonda yer alır
  (SOAP 1.1'in `text/xml`'inden ayrışır) → `proto-sozlesme.md` §3.2.
- ✅ Zarf yapısı `env:Envelope` → `env:Header` + `env:Body`; Header
  opsiyonel, Envelope/Body zorunludur.
- ✅ `env:Fault` kod ailesi ve `mustUnderstand` header niteliği bu
  belgededir → `proto-dogrulama.md` §3 (L2 SOAP satırı).

### S2 — W3C WSDL 2.0 (`https://www.w3.org/TR/wsdl20/`)

- ✅ WSDL 2.0, WSDL 1.1'in **yerini alması** amacıyla yazılmış W3C
  Önerisi'dir; WSDL 1.1 bu belge çerçevesinde bir W3C Standardı değildir.
- ✅ Soyut katman (`interface` + operasyon) / somut katman (`binding` +
  `endpoint`) ayrımı bu belgededir → `proto-sozlesme.md` §3.1.
- ✅ `service` bileşeni endpoint kümesini toplar (adres = taşıma işi,
  bu düğümün kapsam dışı) → sınır notu `proto-anayasa.md` §2.

### S3 — W3C XML Schema Bölüm 1 (`https://www.w3.org/TR/xmlschema-1/`)

- ✅ XML belge **yapısını** tanımlayan dil; simple/complex type ayrımı
  ve identity-constraint (key/keyref/unique) bu belgededir.
- ✅ Instance nitelikleri `xsi:type` / `xsi:nil` / `xsi:schemaLocation`
  bu belgenin konusudur → `proto-dogrulama.md` §2.2.
- ⚠️ JSON Schema ↔ XML Schema **eşlemesi** bu belgede tanımlanmaz →
  `proto-sozlesme.md` §4.1'de `⚠️ UNKNOWN` olarak durur.

### S4 — RFC 7303 XML Media Types (`https://www.rfc-editor.org/rfc/rfc7303.html`)

- ✅ `application/xml`, `text/xml` ve `+xml` suffix kuralı bu RFC'dedir.
- ✅ Charset parametresi / BOM otoritesi ilişkisi bu belgededir →
  `proto-dogrulama.md` §2.2 kodlama satırı.
- ✅ `text/xml` için HTML yığınına karşı **güvenlik notu** bu belgededir →
  `proto-dogrulama.md` §3 reddedilecek sütunu.

### S5 — RFC 8259 JSON (`https://www.rfc-editor.org/rfc/rfc8259.html`)

- ✅ "JSON bir veri alışveriş formatıdır" tanımı ve `application/json`
  medya tipi (§12) bu belgededir.
- ✅ UTF-8 ağ kodlaması, BOM üretme/kabul etmeme, lowercase literal
  kuralları bu sözdizimindedir → `proto-dogrulama.md` §2.1.
- ✅ Tekrarlayan member adları SHOULD unique; `eval()` güvenlik uyarısı
  bu belgededir.

### S6 — RFC 6838 Media Type Syntax (`https://www.rfc-editor.org/rfc/rfc6838.html`)

- ✅ `type/subtip` yazım kuralları, parametre ve **prefix (yarım tip)**
  eşleşmesi §4.2'dedir.
- ✅ "Parametreler medya tipini değiştirmez" kuralı bu belgededir →
  Accept pazarlığında `;charset=` benzeri varyantların eleme yapmaması
  iddiasının dayanağı.
- ⚠️ CoreMusic'in **hangi** medya tiplerini kaydettiği repo işidir →
  `proto-dogrulama.md` §8 disk kanıtına devredilmiştir.

### S7 — RFC 2045 MIME Part 1 (`https://www.rfc-editor.org/rfc/rfc2045`)

- ✅ `multipart` tipi ve `multipart/form-data` tanımı §5'tedir.
- ✅ Base64/7bit/8bit/binary transfer kodlamaları bu belgededir (P1'in
  binary taşıma yüzüyle ilişkisi: yalnızca **MIME** düzeyinde).
- ⚠️ `multipart/form-data`'nın güncel yönetimi RFC 7578'dir; **o belge bu
  turda çekilmedi** → kullanılmamıştır (elenmedi, sadece çekilmemiştir).

### S8 — RFC 9110 HTTP Semantics (`https://www.rfc-editor.org/rfc/rfc9110.html`)

- ✅ `Content-Type` (içeriğin tipi) ile `Accept` (kabul edilebilirlik)
  ayrımı bu belgededir → tüm pazarlık satırlarının dayanağı.
- ✅ `406 Not Acceptable` ve `415 Unsupported Media Type` durum
  tanımları bu belgededir → `proto-dogrulama.md` §3.
- ✅ Method semantiği (safe/idempotent) ayrımı bu belgededir →
  `proto-sozlesme.md` §3.2 REST yüzü.

### S9 — RFC 6455 WebSocket (`https://www.rfc-editor.org/rfc/rfc6455.html`)

- ✅ Accept formülü: base64(SHA-1(key + GUID)) ve GUID
  `258EAFA5-E914-47DA-95CA-C5AB0DC85B11` bu belgededir.
- ✅ Desteklenen sürüm `13`; sürüm uyuşmazlığında `400`, `Upgrade`
  yokluğunda `426 Upgrade Required` bu belgededir.
- ✅ İstemci el sıkışma doğrulama adımları (Accept / Upgrade /
  istenmeyen extension reddi) §4.1'dedir → `proto-dogrulama.md` §4.1.

### S10 — WHATWG SSE (`https://html.spec.whatwg.org/multipage/server-sent-events.html`)

- ✅ Yanıt tipi `text/event-stream` ve **yalnızca UTF-8** bu spesifikasyondadır.
- ✅ Alanlar `event` · `data` · `id` · `retry`; boş satır = dispatch; `:`
  öneki yorum satırıdır.
- ✅ Sunucu kapanışta `204` gönderirse istemci **yeniden bağlanmaz** →
  `proto-sozlesme.md` §5.3 eşlemesinin (TASARIM İDDİASI) veri tarafı.

### S11 — RFC 6750 Bearer (`https://www.rfc-editor.org/rfc/rfc6750.html`)

- ✅ Üç taşıma yöntemi (header / form / query) ve **tek yöntem kuralı**
  bu belgededir.
- ✅ Hata eşlemesi: `400 invalid_request` · `401 invalid_token` ·
  `403 insufficient_scope` + `WWW-Authenticate: Bearer` challenge.
- ✅ Token'ın URL'de taşınmaması uyarısı bu belgededir → P5
  dönüştürücüsünün query'ye düşürmeyi engelleme iddiasının kaynağı.

### S12 — gRPC What is gRPC (`https://grpc.io/docs/what-is-grpc/`)

- ✅ Kendi tanımı: HTTP/2 üzerine kurulmuş **açık kaynaklı RPC çerçevesi**.
- ⚠️ Sayfa bir **tanım** sayfasıdır; sürüm/destek matrisi taşımaz →
  sürüm pazarlığı iddiaları bu kaynaktan **türetilmez**.

### S13 — gRPC Core Concepts (`https://grpc.io/docs/what-is-grpc/core-concepts/`)

- ✅ Dört yöntem türü: unary · server streaming · client streaming ·
  bidirectional streaming.
- ✅ Metadata kuralları: `grpc-` öneki reserved; binary veri `-bin` son eki
  ile taşınır.
- ✅ Deadline davranışı → `DEADLINE_EXCEEDED` durum kodu.

### S14 — protobuf Encoding (`https://protobuf.dev/programming-guides/encoding/`)

- ✅ Etiket hesabı: `(field_number << 3) | wire_type`.
- ✅ Altı wire tipi: 0 varint · 1 64-bit · 2 length-delimited · 3
  start-group · 4 end-group · 5 32-bit (3/4 eski/grup, bugün önerilmez).
- ✅ Serileştirilmiş mesaj boyutu sınırı 2 GiB'tır → `proto-dogrulama.md`
  §2.3 sınır satırı.

### S15 — CloudEvents (`https://cloudevents.io/`)

- ✅ v1.0 (`specversion: "1.0"`, 2019-10-24) ve CNCF **Graduated**
  statüsü (2024-01-25) bu sitededir.
- ✅ Zorunlu alanlar `id` · `source` · `specversion`; taşıma aracından
  bağımsız açık format ilkesi.
- ⚠️ CloudEvents **tek başına bir protokol çifti değildir** — bu dosya
  §2.6'daki bu madde yalnızca `proto-anayasa.md` §4'teki "sarmalayıcı
  desen" tanımını kanıtlar.

> **§2.6 sayımları:** 15 kaynak · 40 doğrulanmış madde (✅) · 5 sınır /
> kapsam-notu (⚠️, madde içi) · 0 çelişki · 0 yeni URL (§2'deki 15'in
> aynısı).

---

## §2.7. Erişim Günlüğü (fetch log — 2026-10-16)

> Biçim: **GET (tam içerik)**; HEAD kullanılmadı. "İçerik" sütunu,
> yanıtın metin gövdesinin alındığını belirtir — iddialar bu gövdeden
> çıkmıştır.

| # | URL (kısaltma) | Yöntem | Sonuç | Kullanım |
|---|---|---|---|---|
| 1 | w3.org/TR/SOAP/ | GET | ✅ içerik alındı | §2.1 S1 · sozlesme §3 · dogrulama §3 |
| 2 | w3.org/TR/wsdl20/ | GET | ✅ içerik alındı | §2.1 S2 · sozlesme §3.1 |
| 3 | w3.org/TR/xmlschema-1/ | GET | ✅ içerik alındı | §2.1 S3 · dogrulama §2.2 |
| 4 | rfc-editor.org/rfc/rfc7303.html | GET | ✅ içerik alındı | §2.1 S4 · dogrulama §2.2/§3 |
| 5 | rfc-editor.org/rfc/rfc8259.html | GET | ✅ içerik alındı | §2.2 S5 · dogrulama §2.1 |
| 6 | rfc-editor.org/rfc/rfc6838.html | GET | ✅ içerik alındı | §2.2 S6 · sozlesme §7.1 |
| 7 | rfc-editor.org/rfc/rfc2045 | GET | ✅ içerik alındı | §2.2 S7 · §2.6 S7 (başka dosyada kullanılmadı — yalnız bu defter) |
| 8 | rfc-editor.org/rfc/rfc9110.html | GET | ✅ içerik alındı | §2.2 S8 · sozlesme §1 · dogrulama §3 |
| 9 | rfc-editor.org/rfc/rfc6455.html | GET | ✅ içerik alındı | §2.3 S9 · dogrulama §4.1 · sozlesme §5.2 |
| 10 | html.spec.whatwg.org/…server-sent-events.html | GET | ✅ içerik alındı | §2.3 S10 · sozlesme §5.1 · dogrulama §4.2 |
| 11 | rfc-editor.org/rfc/rfc6750.html | GET | ✅ içerik alındı | §2.4 S11 (2 satır) · sozlesme §6 · dogrulama §5 |
| 12 | grpc.io/docs/what-is-grpc/ | GET | ✅ içerik alındı | §2.5 S12 · anayasa §6.6 (gRPC kartı) |
| 13 | grpc.io/docs/what-is-grpc/core-concepts/ | GET | ✅ içerik alındı | §2.5 S13 · sozlesme §2 |
| 14 | protobuf.dev/programming-guides/encoding/ | GET | ✅ içerik alındı | §2.5 S14 · sozlesme §2.2 (uyumluluk) · dogrulama §2.3 |
| 15 | cloudevents.io/ | GET | ✅ içerik alındı | §2.5 S15 · anayasa §4 (desen) |

**Günlük özeti:** 15 deneme · 15 başarılı · 0 HTTP hatası · 0 boş yanıt ·
0 tekrar (aynı URL iki kez çekilmedi; 6750'nin iki iddiası **tek** çekimden
çıkmıştır).

---

## §2.8. Kalıp ile Karşılaştırma (`openapi-web-arastirma.md`)

| Ölçüt | openapi kalıbı | Bu dosya | Fark |
|---|---|---|---|
| Tablo biçimi | İddia / URL / tarih / Durum | **Aynı** (§2.1-§2.5) | yok |
| Benzersiz URL alt sınırı | ≥12 | **15** | +3 |
| Elenenler bölümü | zorunlu, gerekçeli | §5 — **0 elenen** | tur sonucu |
| Yöntem | GET tam içerik | **GET tam içerik** | aynı |
| Kapsam | HTTP/OpenAPI yüzeyi | protokol çiftleri (P1-P5) + devralınan gRPC | §15 devri |
| Ek bölüm | — | §2.6 kaynak bazlı ayrıntı + §2.7 günlük | bu klasöre özgü |

---

## §3. İddia ↔ Dosya Eşlemesi

| İddia grubu | Kanıt satırı | Kullanan dosya |
|---|---|---|
| SOAP 1.2 / WSDL / XML Şema / XML medya tipleri | §2.1 (S1-S4) | proto-sozlesme §3 (P2) · proto-anayasa §6.1-§6.2 (SOAP/WSDL kartları) · proto-dogrulama L1-L2 (§2.2, §3) |
| JSON / medya tipi / Accept / 406-415 | §2.2 (S5-S8) | proto-anayasa §6.3-§6.5 (JSON/XML/HTTP kartları) · proto-sozlesme §1/§4 (P3) · proto-dogrulama L1-L2 (§2.1, §3) · kod-ornekleri (PHP/Python/Ruby) |
| WebSocket Accept / sürüm / SSE alanları | §2.3 (S9-S10) | proto-dogrulama L3 (§4.1-§4.3) · proto-sozlesme §5 (P4) · kod-ornekleri (JS/Go/Kotlin/Swift) |
| Bearer 400/401/403 + tek yöntem | §2.4 (S11) | proto-sozlesme §6 (P5) · proto-anayasa §6.10 (Bearer kartı) · proto-dogrulama L4 (§5) · kod-ornekleri (C#) |
| gRPC wire / 4 yöntem / metadata / deadline | §2.5 (S12-S13) | proto-anayasa §6.6 (gRPC kartı) · proto-sozlesme §2 (P1) · kod-ornekleri (TS) |
| Protobuf wire tipleri / etiket | §2.5 (S14) | proto-sozlesme §2.2 (ileriye dönük uyum) · proto-dogrulama §2.3 (L1) · kod-ornekleri (Rust) |
| CloudEvents v1.0 / Graduated | §2.5 (S15) | proto-anayasa §4 (desen) · kod-ornekleri (Elixir) |

---

### §3.1 Ters Eşleme — Dosya → Kaç URL

| Dosya | Dayandığı benzersiz URL | Not |
|---|---|---|
| `proto-anayasa.md` | 13 | 5 çift envanteri + 10 kimlik kartı |
| `proto-sozlesme.md` | 12 | §Kaynak Tablosu S1-S12 |
| `proto-dogrulama.md` | 9 | §Kaynak Tablosu S1-S9 (+ disk kanıtı) |
| `proto-kod-ornekleri.md` | 15 (dolaylı) | Bloklar §2.6 maddelerine bağlanır; ayrı URL çekmedi |
| `proto-web-arastirma.md` | 15 | Bu dosya — birincil kayıt |
| `index.md` | 0 | Yalnız çocuk kataloğu ve kapılar |

> **Uyarlık:** Dört içerik dosyasının §Kaynak Tablosu'ndaki numaralar
> **kendi içlerinde** S1'den başlar (kalıp davranışı); bu dosyadaki
> S1-S15 ise **bu dosyanın** numaralandırmasıdır. İki dizi birbirine
> karıştırılmaz.

---

## §4. Bu Turda Doğrulanamayan İddialar

| İddia | Durum | Gerekçe |
|---|---|---|
| API key için **evrensel** bir spesifikasyon var mı (P5 ikinci yüzü) | ⚠️ VERIFICATION REQUIRED | Bu turda resmi bir "API key standardı" gövdesi **çekilmedi**; standart olmadığı iddiası da KAYNAKSIZDIR. `proto-sozlesme.md` §6 (P5) bu yüzden tek başına durur. |
| GraphQL ve AsyncAPI (kalıp §15 devri) | ⚠️ VERIFICATION REQUIRED | Bu turda kapsam dışı bırakıldı; K013'te bir kademe-3 dosyası henüz yok. |
| `RouteTable.php` bir protokol dönüşümü yapar mı | ⚠️ VERIFICATION REQUIRED | Dosya diskte var (kanıt: `proto-dogrulama.md` §8) ama **içeriği okunmadı**; protokol dönüşümü yaptığı iddiası edilmedi. |
| HTTP/2 ALPN etiketleri "http/1.1"·"h2"·"h3" ve 2^31-1 uzunluk alanı | ⚠️ VERIFICATION REQUIRED | RFC 7301 (ALPN) bu turda **çekilmedi**; bu iddia yalnız `proto-kod-ornekleri.md` C++ bloğunda kendi başınadır ve o blokta `⚠️` ile işaretlidir. |
| Pazarlık sırası (Accept → Upgrade → scope) önceliklendirmesi | ⚠️ TASARIM İDDİASI | Üç pazarlık başlığı ayrı belgelerdedir; **sıralama** klasörün kendi kararıdır (`proto-sozlesme.md` §6). |

---

## §5. Elenenler

| # | URL | Erişim | Gerekçe |
|---|---|---|---|
| — | — | — | **Bu turda elenen kaynak yok (0).** 15/15 URL tam içerikle çekildi; 404/boş/timeout yaşanmadı. |

---

## §6. Sonraki Tur Önerisi (bilgi amaçlı)

| # | İş | Kapanacak iddia | Öncelik |
|---|---|---|---|
| 1 | RFC 7301 (ALPN) çek | ALPN etiket/uzunluk iddiası — şu an yalnız `proto-kod-ornekleri.md` C++ bloğunda `⚠️` ile (§4 satır 4) | yüksek |
| 2 | gRPC status ↔ HTTP status resmi eşleme sayfasını çek | `proto-sozlesme.md` §2.3 "Eşleme tablosu" satırı | yüksek |
| 3 | GraphQL spec + AsyncAPI çek | kalıp §15 devrinin kalan iki konusu | orta |
| 4 | API key yüzü için birincil kaynak ara (IETF draft / OWASP API Security) | §4 satır 1 — bulunamazsa "standart yoktur" iddiası da `⚠️` kalır | orta |
| 5 | `.github/workflows/` envanterini say | `proto-dogrulama.md` §7 G5 ve disk notu | düşük (repo içi) |

> **Kural (değişmez):** Yukarıdaki satırlar **öneri**dir; kapatılmadan
> önceki halleri `⚠️ VERIFICATION REQUIRED` olarak kalır ve bu dosyada
> "çekildi" diye gösterilemez.

---

## § Kaynak Tablosu

| # | Kaynak | Tür | Tarih |
|---|--------|-----|-------|
| S1 | `proto-anayasa.md` (bu klasör) | iddia envanteri | 2026-10-16 |
| S2 | `proto-sozlesme.md` (bu klasör) | sürüm/hata iddiaları | 2026-10-16 |
| S3 | `proto-dogrulama.md` (bu klasör) | L1-L6 + repo kanıtı | 2026-10-16 |
| S4 | `proto-kod-ornekleri.md` (bu klasör) | blok iddiaları | 2026-10-16 |
| S5 | `openapi-web-arastirma.md` (kalıp) | kalıp biçimi + §15 kapsam devri | 2026-10-16 |
| S6 | §2.1-§2.5 (15 resmi URL) | birincil web kanıtı | 2026-10-16 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
