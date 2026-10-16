---
node_id: K013-integraion/protocol-adapters/proto-anayasa
node_type: technology-deepdive
title: "Protocol Adapters Anayasa — Kimlik, Sınır, Protokol Çiftleri, Düzlem Ayrımı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/protocol-adapters
  - K013-integraion
evidence:
  - "https://www.w3.org/TR/SOAP12/ (2026-10-16 çekildi)"
  - "https://www.w3.org/TR/wsdl20/ (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc8259.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc7303.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16 çekildi)"
  - "https://grpc.io/docs/what-is-grpc/core-concepts/ (2026-10-16 çekildi)"
  - "shared/src/OAuth/Provider/ (disk listesi, 2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 çekimi, içerik alındı)"
  - "Sınır iddiaları (K014/ağ, adapters kardeşi) etiketlidir: TASARIM İDDİASI olarak işaretlenmiştir"
  - "Uydurma protokol/sürüm/ad yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN"
open_questions:
  - "CoreMusic'te gerçek bir protokol çifti dönüşümü (ör. SOAP↔REST) repo'da var mı? → ⚠️ VERIFICATION REQUIRED (bu turda tespit edilmedi)"
  - "protocol-adapters kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
  - "gRPC/protobuf CoreMusic hedef yığınına girecek mi? → UNKNOWN (ADR yok)"
group: mimari
---

# Protocol Adapters Anayasa — Kimlik · Sınır · Protokol Çiftleri · Düzlem

> **Bu dosya ne değildir:** Kod, kütüphane kurulumu veya taşıma (transport)
> rehberi değildir. Bu dosya `protocol-adapters` düğümünün **kim olduğunu,
> neyin kendisi olduğunu ve neyin olmadığını** belgeler; protokol tanımları
> W3C/IETF/gRPC.io sayfalarından alınmıştır, tahmin değildir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Repo uygulaması
> ⚠️ VERIFICATION REQUIRED (protokol dönüşümü yapan bir sınıf/bileşen bu
> turda diskte tespit edilmedi).

---

## §0 ELI15 — protocol-adapters bir cümlede

İki farklı **konuşma dili** (protokol) arasında çevirmenlik yapar: taraflar
biri HTTP/JSON, diğeri gRPC/protobuf konuşuyorsa, aradaki bu düğüm mesajı
alır, o dilin kurallarına göre **yeniden yazar** ve karşı tarafa kendi dilinde
iletir — mesajın **kime gideceği** (yönlendirme/transport) onun işi değildir,
mesajın **ne dille yazıldığı** onun işidir.
(Kaynak: §Kaynak Tablosu [S5] — HTTP "intermediation protocol" tanımı; [S6] —
gRPC servis/tanım modeli)

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır, protokol çiftleri, düzlem ayrımı (bu dosya) | 3 |
| mimari | `proto-sozlesme.md` | Her çiftin sözleşme yüzeyi: tanımlama, sürüm, pazarlık, hata | 3 |
| kod | `proto-dogrulama.md` | Doğrulama katmanları: şema, el sıkışma, kimlik, CI kapısı | 3 |
| kod | `proto-kod-ornekleri.md` | 13 dilde protokol dönüşüm örneği | 3 |
| kanit | `proto-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia/URL/tarih/durum defteri | 3 |

**Gruplama kuralı:** registry §8.1 + ADR-097 §5.5 —
`{kategori}/{teknoloji}/{teknoloji}-{rol}.md`.
`group: mimari | kod | kanit` alanı her dosyanın frontmatter'ında zorunludur.

---

## §1 Düğüm Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Düğüm | `K013-integraion/protocol-adapters` | v2 §2.13 çocuk adı (index.md evidence) |
| Kademe | Kademe-2 subdomain (K013'ün 8 çocuğundan biri) | `index.md` frontmatter |
| Üst düğüm | `K013-integraion` — "dış dünyaya tek kapı" | v2 §2.13 |
| Grup | `mimari` (bu dosya), `mimari`/`kod`/`kanit` (kardeşler) | ADR-097 §5.5 |
| İş | Alt katman **PROTOKOL dönüşümü**: HTTP↔gRPC, REST↔SOAP, JSON↔XML, SSE↔WebSocket, OAuth bearer↔API key | Görev tanımı (etiketli iddia, §4) |
| Sahip agent | Backend Architect (A2) / Architect gözdenetimi | `.ai/AGENTS.md` §4 |
| Durum | draft — kademe-3 genişletmesi ADR-097 K4 faz (C) bekliyor | `index.md` §A |

---

## §2 Sınır Tablosu — Bu Düğüm Neyin İçi, Neyin Dışı?

> **Etiket kuralı:** Aşağıdaki tablonun "dayanak" sütununda `[şartname]`
> yazan satırlar dış kaynağa bağlıdır; `TASARIM İDDİASI` yazan satırlar bu
> mimarinin **kendi karar**ıdır (disk/URL kanıtı yok, etiketlidir).

| Yüzey | Sahibi | Ne yapar | protocol-adapters ilişkisi | Dayanak |
|---|---|---|---|---|
| **PROTOKOL dönüşümü** | **protocol-adapters (bu düğüm)** | İki protokolün mesaj **biçimini/lügatını** çevirir (envelope, alan adı, kodlama, hata biçimi) | **Sahibi** | TASARIM İDDİASI |
| Üçüncü-parti **sistem/ACL** dönüşümü | kardeşi `adapters` | Harici sistem kimliği/izin dönüşümü (OAuth provider adaptörleri vb.) | **Kardeş — çakışma yasak** | TASARIM İDDİASI · sınır referansı: `adapters/adapters-anayasa.md` §2 |
| API **sözleşmesi tanımı** | kardeşi `api-contracts` (openapi/) | API'nin uç/parametre/yanıt **tanımını yazar** | Bu düğüm o tanımın **protokol çevirisini** uygular | TASARIM İDDİASI |
| **Olay** sözleşmesi | kardeşi `event-contracts` | Olay türü ve yük şeması | Bu düğüm olayı **hangi protokolle** taşınacağına çevirir (ör. SSE↔WS) | TASARIM İDDİASI |
| **Ağ taşıması** | `K014-network` | Bağlantı, port, TLS, DNS, yük dengeleme, yeniden deneme | Bu düğüm **yalnızca çevirir**; bağlantı kurmaz/taşımaz | TASARIM İDDİASI (görev tanımı) |
| İş kuralı | `K008-domain` | Alan mantığı | Dokunmaz | TASARIM İDDİASI (index.md sınır beyanı) |
| Kimlik/kripto | `K009-security` | Token üretimi, imzalama, oturum | Bu düğüm yalnızca **kimlik bilgisini bir protokol alanından diğerine taşır** (ör. bearer → header) | TASARIM İDDİASI |

### §2.1 protocol-adapters ↔ adapters kardeşi sınırı (açık beyan)

| Soru | protocol-adapters | `adapters` (kardeş) |
|---|---|---|
| Ne çevirir? | **Protokol**: mesaj biçimi, zarf (envelope), hata biçimi, aktarım semantiği | **Sistem/ACL**: üçüncü-parti hizmet kimliği, izin eşlemesi, sağlayıcı arayüzü |
| Örnek | SOAP Envelope → JSON nesnesi; SSE `data:` satırı → WebSocket mesajı | `IOAuthProvider` → Discord/Facebook/… sağlayıcı uyarlaması (disk: `shared/src/OAuth/Provider/`) |
| Bağımlılık yönü | Bu düğüm, sistem adaptörünün **öncesinde/sonrasında** çalışabilir | Sistem adaptörü, protokol dönüşümünün **üzerindeki** iş katmanına seslenir |
| Çakışma | Aynı dosya iki gruba ait olamaz — sahiplik tek | Aynı |

> **Dayanak:** `shared/src/OAuth/Provider/` disk listesi 2026-10-16'da
> okundu: `IOAuthProvider.php`, `BaseOAuthProvider.php` ve 10 somut
> sağlayıcı (Discord, Facebook, Instagram, LinkedIn, Pinterest, Reddit,
> Snapchat, TikTok, X, YouTube). Bunlar **sistem/ACL yüzeyidir** →
> kardeşi `adapters` kapsamı; protokol çevirisi değildir.

---

## §3 Protokol Çiftleri Envanteri (5 çift)

> Bu envanter görev tanımından gelir; her satırın **şartname dayanağı**
> ayrıca §6'da. Çiftlerin CoreMusic repo'sunda gerçek uygulanıp
> uygulanmadığı → ⚠️ VERIFICATION REQUIRED (açık sorular).

| # | Çift | Yön | Dönüşüm neyi değiştirir | Şartname dayanağı |
|---|---|---|---|---|
| P1 | HTTP ↔ gRPC | çift yönlü | JSON gövde ↔ protobuf binary; HTTP durum kodu ↔ gRPC status kodu; header ↔ metadata | [S6], [S7], [S9] |
| P2 | REST ↔ SOAP | çift yönlü | Gövde + header ↔ SOAP Envelope (`soapenv` NS); HTTP durum kodu ↔ SOAP Fault kodu | [S1], [S2] |
| P3 | JSON ↔ XML | çift yönlü | İsimlendirme (camelCase/snake_case), sayı/boolean/null yazımı, charset/BOM | [S3], [S4] |
| P4 | SSE ↔ WebSocket | çift yönlü | Satır tabanlı `text/event-stream` ↔ çerçeveli (framed) iki yönlü akış | [S10], [S11], [S12] |
| P5 | OAuth bearer ↔ API key | yönü karara bağlı | `Authorization: Bearer …` ↔ `X-…-Api-Key`/query anahtarı; hata biçimi (`WWW-Authenticate`) ↔ özel hata gövdesi | [S13], [S5] |

> **[S2] WSDL notu:** SOAP↔REST dönüşümünde WSDL 2.0, soyut (interface) ve
> somut (binding) ayrımıyla **binding = taşıma + tel biçimi** tanımını verir;
> bu, "çeviri nerede başlar" sorusunun şartname tarafındaki dayanağıdır.

### §3.1 Çiftlerin aidiyeti (hangi çift kime komşu)

| Çift | Yukarı komşu | Aşağı komşu | Not |
|---|---|---|---|
| P1 | `K014-network` (HTTP/2 taşıması) | `api-contracts` (sözleşme) | Taşıma K014'te; **kodlama çevirisi** burada → etiketli iddia |
| P2 | `api-contracts` (SOAP yüzeyi var mı → ⚠️ UNKNOWN) | `external-systems` (eski partner sistemler) | ⚠️ CoreMusic'te SOAP kullanılıp kullanılmadığı UNKNOWN |
| P3 | `event-contracts` | `synchronization` | JSON↔XML genelde veri göçü/parsing'de |
| P4 | `event-contracts` | `synchronization` (akış sırası) | Akış **sırası** bozulmamalı; SSE tek yön, WS iki yön |
| P5 | `K009-security` | `adapters` (sağlayıcı adaptörleri) | Bearer'ın **taşınması** burada, **üretilmişi** K009'da |

---

## §4 Düzlem Ayrımı — "Çevirmek" ≠ "Taşımak"

**Etiketli tasarım iddiası (TASARIM İDDİASI):** Bu mimaride
`protocol-adapters` **yalnızca mesajı çözer ve yeniden yazar**; TCP/HTTP
bağlantısını, portu, TLS'yi, yeniden denemeyi ve yönlendirmeyi
**yapmaz** — bunlar `K014-network`'ün yüzeyidir.

Şartnameyle örtüştüğü nokta (dış dayanak): RFC 9110, HTTP'yi
"intermediation protocol" olarak tanımlar — proxy/gateway'ler "non-HTTP
information systems into a more generic interface" çevirebilir; yani
**çeviri davranışı taşımanın üstünde bir semantik katmandır**. (Kaynak: [S5])

| Görev | protocol-adapters | K014-network | Dayanak |
|---|---|---|---|
| JSON gövdeyi protobuf'a serileştirmek | ✅ | ❌ | TASARIM İDDİASI |
| HTTP durumunu gRPC status'a eşlemek | ✅ | ❌ | TASARIM İDDİASI |
| TCP bağlantısı kurmak, port seçmek | ❌ | ✅ | TASARIM İDDİASI |
| TLS el sıkışması | ❌ | ✅ | TASARIM İDDİASI |
| Yeniden deneme/backoff | ❌ | ✅ | TASARIM İDDİASI |
| WebSocket **el sıkışmasının yanıtını hesaplamak** (biçim) | ✅ (biçim bilgisi) | ⚠️ Sınır: `Sec-WebSocket-Accept` üretimi hem "çeviri" hem "handshake" olabilir → açık soru | [S11] · sınır kararı UNKNOWN |

---

## §5 Dönüşüm Boyutları (her çift bu 5 boyutta tanımlanır)

| Boyut | Ne değişir | Örnek | Dayanak |
|---|---|---|---|
| 1. **Çerçeveleme (framing)** | Mesaj sınırı nasıl belli | HTTP gövde ↔ WebSocket çerçeves ↔ SSE satır bloğu | [S11], [S12] |
| 2. **Kodlama (encoding)** | Bayt biçimi, karakter seti | JSON UTF-8 (BOM yasak) ↔ XML charset/BOM ↔ protobuf varint | [S3], [S4], [S9] |
| 3. **Sözleşme (contract)** | Alan adları, tipler, zorunluluk | `camelCase` JSON ↔ `soapenv:Body` ↔ `.proto` alanı | [S1], [S6] |
| 4. **Hata (error)** | Hata biçimi ve dili | HTTP 4xx/5xx ↔ SOAP Fault ↔ gRPC status ↔ `WWW-Authenticate` | [S5], [S1], [S6], [S13] |
| 5. **Kimlik (auth)** | Kimlik bilgisinin taşındığı alan | `Authorization: Bearer` ↔ API key header/query | [S13] |

**Kural (etiketli):** Bir çift için 5 boyutun **tamamı** tanımlanmadan
dönüşüm "tamamlanmış" sayılmaz; eksik boyut `⚠️ VERIFICATION REQUIRED`
ile işaretlenir.

---

## §6 Protokol Kimlik Kartları (şartname özetleri)

### §6.1 SOAP 1.2 (REST↔SOAP çiftinin zarf dili) — [S1]

| Alan | Değer |
|---|---|
| Belge | SOAP 1.2 Part 1: Messaging Framework (W3C Recommendation, 2007-04-27, 2. baskı) |
| Zarf namespace | `http://www.w3.org/2003/05/soap-envelope` |
| Media type | `application/soap+xml` |
| Roller (roles) | `next`, `none`, `ultimateReceiver` |
| Zorunlu işleme | `mustUnderstand` |
| Hata zarfı | `env:Fault` — kodlar `env:Sender` / `env:VersionMismatch` (ve `env:Receiver`) |
| İlişki | SOAP 1.1'i supersede eder (W3C REC metni) |

### §6.2 WSDL 2.0 (sözleşme tanım dili) — [S2]

| Alan | Değer |
|---|---|
| Belge | WSDL 2.0 Part 1: Core Language (W3C Recommendation, 2007-06-26) |
| Namespace | `http://www.w3.org/ns/wsdl` |
| Soyut düzey | Mesajlar + `interface` (operasyonlar; taşımadan bağımsız) |
| Somut düzey | `binding` (**taşıma + tel biçimi**), `endpoint` (adres), `service` |
| Media type | `application/wsdl+xml` (Appendix A) |
| SOAP/HTTP binding'leri | Part 2: Adjuncts'ta (bu dosya Part 1'i kaynak alır) |

### §6.3 JSON — [S3]

| Alan | Değer |
|---|---|
| Belge | RFC 8259 (2017-12), obsoletes 7159 |
| Media type | `application/json` |
| Ağ için kodlama | UTF-8 zorunlu; **BOM eklenmez** |
| Literaller | `true`/`false`/`null` — tamamı **lowercase** |
| Ad tekilliği | Üyelerin adları SHOULD unique |
| Sayı yasağı | Leading zero yok; `Infinity`/`NaN` yok |
| Güvenlik | `eval()` kullanımı güvenlik riski olarak uyarılır |

### §6.4 XML / XML Media Types — [S4]

| Alan | Değer |
|---|---|
| Belge | RFC 7303 (2014-07), obsoletes 3023 |
| Media types | `application/xml`, `text/xml`; `+xml` structured suffix |
| Otorite sırası | BOM, `charset` parametresinden daha otoriterdir |
| Önerilen | UTF-8, BOM'suz |
| XML Schema doğrulama | W3C XML Schema Part 1 (2. baskı, 2004-10-28) — bileşen modeli, `xsi:type`/`xsi:nil`/`xsi:schemaLocation` [S8] |

### §6.5 HTTP Semantics (her çiftin taşıma üstü sözleşmesi) — [S5]

| Alan | Değer |
|---|---|
| Belge | RFC 9110 (2022-06), STD 97; obsoletes 7230/7231/7232/7233/7235… |
| Kapsam | Yöntem, durum kodu, alan (field), içerik pazarlığı, kimlik doğrulama |
| Durum kodları | 1xx–5xx (ör. 415 Unsupported Media Type, 422 Unprocessable Content, 502 Bad Gateway) |
| İçerik pazarlığı | `Accept`, `Accept-Encoding`, `Content-Type` |
| Çeviri tanımı | Intermediary'ler HTTP olmayan sistemleri "more generic interface"e çevirebilir |

### §6.6 gRPC — [S6], [S7]

| Alan | Değer |
|---|---|
| Tanım dili | Varsayılan **protocol buffers** (IDL) — servis + mesaj yapısı |
| Yöntem tipleri | unary · server streaming · client streaming · bidirectional |
| Sıra garantisi | Tek RPC çağrısı içinde mesaj sırası korunur (streaming) |
| Metadata | key–value; anahtarlar case-insensitive ASCII; `grpc-` öneki rezerve; binary değerler `-bin` ile biter |
| Deadline | Süre aşımı → `DEADLINE_EXCEEDED` |
| Kanal/istub | channel (bağlantı soyutlaması) + stub/client |
| Tel biçimi | protobuf: tag = `(field_number << 3) | wire_type`, 6 wire type (VARINT/I64/LEN/SGROUP/EGROUP/I32) [S9] |

### §6.7 SSE (Server-Sent Events) — [S12]

| Alan | Değer |
|---|---|
| Belge | WHATWG HTML Living Standard §9.2 (2026-10-14 güncellemesi) |
| Media type | `text/event-stream` |
| Kodlama | **Yalnız UTF-8** (başka encoding seçilemez) |
| Alanlar | `event`, `data`, `id`, `retry` (büyük/küçük harf duyarsız **değil** — literal karşılaştırılır) |
| Yön | Sunucu → istemci (tek yönlü push) |
| Yeniden bağlanma | `Last-Event-ID` header'ı; 204 yanıtı reconnect'i durdurur; 301/307 yönlendirme desteklenir |
| Yorum satırı | `:` ile başlayan satırlar yok sayılır (keep-alive için) |

### §6.8 WebSocket — [S11]

| Alan | Değer |
|---|---|
| Belge | RFC 6455 (2011-12) |
| El sıkışma | HTTP Upgrade isteği → `101 Switching Protocols` |
| Doğrulama | `Sec-WebSocket-Key` + GUID `258EAFA5-E914-47DA-95CA-C5AB0DC85B11` → SHA-1 → base64 → `Sec-WebSocket-Accept` |
| Sürüm | `Sec-WebSocket-Version: 13` |
| Şemalar/portlar | `ws:` 80 · `wss:` 443 (varsayılan) |
| Çerçeveler | metin (UTF-8) / binary / kontrol (close, ping, pong) |
| Alt protokol | `Sec-WebSocket-Protocol` ile pazarlık |
| Maskeleme | İstemciden sunucuya maskelenir (altyapı saldırılarına karşı) |

### §6.9 CloudEvents (olay zarfı — SSE/WS taşınan yük) — [S10]

| Alan | Değer |
|---|---|
| Tanım | "Olay verisini ortak biçimde tanımlayan şartname" (cloudevents.io) |
| Sahiplik | CNCF — **Graduated** (2024-01-25) |
| Sürüm tarihçesi | 1.0 (2019-10-24) · 1.0.1 (2020-12-10, WebSocket binding eklendi) · 1.0.2 (2022-02-05, Protobuf batching) |
| Protokol binding'leri | HTTP, WebSocket (1.0.1) ve diğerleri — SDK listesinde PHP de var |
| İlgili | CloudEvents SQL v1 (2024-06-13) |

### §6.10 OAuth Bearer (P5 çiftinin bir yüzü) — [S13]

| Alan | Değer |
|---|---|
| Belge | RFC 6750 (2012-10) |
| Taşıma yöntemleri | (1) `Authorization: Bearer …` header (2) form gövdesi `access_token` (3) URI query parametresi |
| Kural | Her istekte **birden fazla yöntem kullanılamaz** |
| Sunucu zorunluluğu | Resource server **header yöntemini desteklemek zorundadır** |
| Zorunlu | TLS — şartname kapsamında zorunlu |
| Hata | `WWW-Authenticate: Bearer` + `error`: `invalid_request` / `invalid_token` / `insufficient_scope` |
| Öneriler | URL'de token taşımak SHOULD NOT · kısa ömürlü token (≤1 saat) · audience scope |

---

## §7 Karar Kriterleri — Ne Zaman protocol-adapters?

| Durum | Karar | Dayanak |
|---|---|---|
| İki taraf aynı protokolü, farklı sürüm konuşuyor | **Burada** (sürüm çevirisi) | TASARIM İDDİASI |
| İki taraf aynı protokolü konuşuyor, farklı kimlik sistemi | Önce `K009-security`, sonra **burada** yalnız alan taşıma | TASARIM İDDİASI |
| Yalnızca bağlantı/timeout/yeniden deneme | **Değil** → `K014-network` | TASARIM İDDİASI |
| Üçüncü-parti hizmetinin izin modeli dönüşümü | **Değil** → `adapters` (kardeş) | TASARIM İDDİASI · `adapters-anayasa.md` §2 |
| API'nin uç tanımı (endpoint/parametre) | **Değil** → `api-contracts` | TASARIM İDDİASI |

---

## §8 İlgili Düğümler — Ağaçtaki Yeri

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion` | Dış dünya köprüsü (üst kademe) |
| ↑ | `K000–K5000 Architecture Registry` | Ağacın kökü |
| ← | `K013-integraion/adapters` | Kardeş — sistem/ACL dönüşümü (sınır §2.1) |
| ← | `K013-integraion/api-contracts` | Kardeş — sözleşme tanımı |
| ← | `K013-integraion/event-contracts` | Kardeş — olay sözleşmesi |
| ↔ | `K014-network` | Taşıma üstü/düzü sınırı (§4) |
| ↔ | `K009-security` | Kimlik üretimi (bu düğüm yalnız taşır) |
| ↓ | `K012-services` | Dönüşüm sonucunu tüketen servis |

---

## §9 Durum Etiketi Matrisi

| Yüzey | Durum | Dayanak |
|---|---|---|
| Şartname/definition | IMPLEMENTED (W3C/IETF/WHATWG/grpc.io çekildi) | §Kaynak Tablosu |
| Repo'da protokol dönüşümü uygulaması | ⚠️ VERIFICATION REQUIRED | Bu turda tespit edilmedi |
| Repo'da kimlik sağlayıcı adaptörleri (sistem/ACL) | IMPLEMENTED (disk) | `shared/src/OAuth/Provider/` 2026-10-16 |
| Kademe-3 kapsamı | ⚠️ VERIFICATION REQUIRED | ADR-097 K4 faz (C) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | SOAP 1.2 envelope NS, `application/soap+xml`, roller, mustUnderstand, Fault kodları | https://www.w3.org/TR/SOAP12/ | 2026-10-16 | ✅ çekildi |
| S2 | WSDL 2.0 interface/binding ayrımı, `application/wsdl+xml` | https://www.w3.org/TR/wsdl20/ | 2026-10-16 | ✅ çekildi |
| S3 | JSON `application/json`, UTF-8, BOM yasak, lowercase literaller | https://www.rfc-editor.org/rfc/rfc8259.html | 2026-10-16 | ✅ çekildi |
| S4 | XML media types, `+xml` suffix, BOM/charset otoritesi | https://www.rfc-editor.org/rfc/rfc7303.html | 2026-10-16 | ✅ çekildi |
| S5 | HTTP semantik, status kodları, içerik pazarlığı, intermediation | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S6 | gRPC servis tanımı, 4 yöntem tipi, metadata, deadline | https://grpc.io/docs/what-is-grpc/core-concepts/ | 2026-10-16 | ✅ çekildi |
| S7 | gRPC genel bakış sayfası (dil listesi, doküman yapısı) | https://grpc.io/docs/what-is-grpc/ | 2026-10-16 | ✅ çekildi |
| S8 | XML Schema Part 1 bileşen modeli, xsi:* instance nitelikleri | https://www.w3.org/TR/xmlschema-1/ | 2026-10-16 | ✅ çekildi |
| S9 | protobuf tel biçimi: tag/wire type, varint, packed, 2 GiB sınırı | https://protobuf.dev/programming-guides/encoding/ | 2026-10-16 | ✅ çekildi |
| S10 | CloudEvents tanımı, CNCF Graduated, sürüm tarihçesi | https://cloudevents.io/ | 2026-10-16 | ✅ çekildi |
| S11 | WebSocket el sıkışma, Accept hesabı, çerçeve tipleri | https://www.rfc-editor.org/rfc/rfc6455.html | 2026-10-16 | ✅ çekildi |
| S12 | SSE `text/event-stream`, alanlar, Last-Event-ID, UTF-8 | https://html.spec.whatwg.org/multipage/server-sent-events.html | 2026-10-16 | ✅ çekildi |
| S13 | Bearer token 3 yöntem, header zorunluluğu, hata kodları | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-16 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `TASARIM İDDİASI` etiketli satırların **dış URL'si yoktur**; bu mimarinin
> kendi kararlarıdır ve dayanak olarak URL gibi kullanılamaz.
> `⚠️ VERIFICATION REQUIRED` işaretli satırlar için **kaynak yoktur**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · W3C/IETF/WHATWG/grpc.io · **Tarih: 2026-10-16**
