---
node_id: K013-integraion/protocol-adapters/proto-dogrulama
node_type: module
title: "Protocol Adapters Doğrulama — Şema, El Sıkışma, Kimlik, Sözleşme ve CI Kapısı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/protocol-adapters/proto-sozlesme
  - K013-integraion/protocol-adapters
evidence:
  - "https://www.w3.org/TR/xmlschema-1/ (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc8259.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc6455.html (2026-10-16 çekildi)"
  - "https://html.spec.whatwg.org/multipage/server-sent-events.html (2026-10-16 çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc6750.html (2026-10-16 çekildi)"
  - "shared/src/OAuth/Provider/IOAuthProvider.php (disk, 2026-10-16)"
  - "shared/src/Cache/ (disk: CacheInterface + 3 adaptör, 2026-10-16)"
validation:
  - "Her doğrulama katmanının dayanağı §Kaynak Tablosu'nda URL ile eşleşir (2026-10-16 çekimi)"
  - "Repo kanıtları yalnız disk listesi düzeyindedir; davranış iddiası değildir"
  - "CI/benchmark değeri uydurulmadı — ölçüm gereken satırlar ⚠️ VERIFICATION REQUIRED"
open_questions:
  - "CoreMusic test suite'inde protokol çifti testi var mı → ⚠️ VERIFICATION REQUIRED"
  - "Doğrulama üretimde mi testte mi çalışıyor → UNKNOWN (ADR bekliyor)"
  - "XML yüzeyi repo'da kullanılıyor mu → ⚠️ VERIFICATION REQUIRED (bu turda tespit edilmedi)"
group: kod
---

# Protocol Adapters Doğrulama — Katmanlar, El Sıkışma, Kimlik, CI Kapısı

> **Bu dosya ne değildir:** Çeviri kodunun kendisi değildir (örnekler
> `proto-kod-ornekleri.md`'dedir). Bu dosya bir protokol dönüşümünün
> **nasıl yanlış olabileceğini** ve bu yanlışlığın **hangi katmanda**
> yakalandığını tanımlar; dayanaklar W3C/IETF/WHATWG şartmeleri ve
> 2026-10-16 disk okumasıdır.
>
> **Durum etiketi:** ŞARTNAME IMPLEMENTED · Repo test kanıtı
> ⚠️ VERIFICATION REQUIRED (protokol dönüşümü testi bu turda bulunamadı).

---

## §0 ELI15 — Doğrulama neden ayrı katman?

Çevirmen üç yerde yanılır: (1) mesaj **biçimi** bozuktur (şemaya uymaz),
(2) iki taraf **anlaşmaz** (el sıkışma/pazarlık yanlış), (3) **kimlik**
yanlış alandan geçer (token yanlış formata girdi). Tek bir kontrol üçünü de
yakalayamaz; bu yüzden doğrulama katman katmandır ve her katmanın
**kendi** şartname dayanağı vardır.
(Kaynak: §Kaynak Tablosu [S1], [S2], [S5], [S6])

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır, protokol çiftleri, düzlem ayrımı | 3 |
| mimari | `proto-sozlesme.md` | Sözleşme yüzeyi: tanımlama, sürüm, pazarlık, hata | 3 |
| kod | `proto-dogrulama.md` | Doğrulama katmanları ve CI kapısı (bu dosya) | 3 |
| kod | `proto-kod-ornekleri.md` | 13 dilde protokol dönüşüm örneği | 3 |
| kanit | `proto-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia/URL/tarih/durum defteri | 3 |

---

## §1 Doğrulama Katmanları Genel Bakış

| # | Katman | Ne denetler | Ne yakalamaz | Şartname dayanağı |
|---|---|---|---|---|
| L1 | **Sözdizimi / şema** | Mesaj biçimi doğru mu (JSON parse, XML Schema, protobuf decode) | Sözleşme anlamı yanlışsa (yanlış operasyon) yakalamaz | [S1], [S2], [S3] |
| L2 | **Medya tipi / kodlama** | `Content-Type` ve charset beklenenle tutarlı mı, BOM/UTF-8 kuralları mı? | Gövde içeriği yanlışsa yakalamaz | [S4], [S2] |
| L3 | **El sıkışma / pazarlık** | WS Accept doğru mu, SSE `Content-Type: text/event-stream` mi, sürüm/alt protokol uyuyor mu? | Bağlantı sonrası hataları yakalamaz | [S5], [S6] |
| L4 | **Kimlik taşıma** | Bearer↔API key dönüşümü tek yöntemle mi, hata biçimi korundu mu? | Yetki (scope) hatalarını yakalamaz (K009) | [S7] |
| L5 | **Sözleşme / round-trip** | Çevrilmiş mesaj geri çevrilince özgünle eşit mi (kaybız)? | performans sorunlarını yakalamaz | TASARIM İDDİASI (ölçüm gerekli ⚠️) |
| L6 | **CI kapısı** | Yukarıdakilerin hepsi otomatik çalışır; başarısızsa pipeline durur | — | TASARIM İDDİASI (proje kararı) |

> **Etkinlik (etiketli iddia):** Katmanlar sırayla uygulanır; L1 geçmeden
> L5 anlamlı değildir. Bu öncelik sırası mimari karardır (TASARIM İDDİASI).

---

## §2 L1 — Şema / Sözdizimi Doğrulaması

### §2.1 JSON yüzü (P1, P3, P5)

| Kontrol | Kural | Dayanak |
|---|---|---|
| Parse | Geçerli JSON değilse **reddet** (sessizce onarma yok) | [S2] |
| Literaller | `true`/`false`/`null` lowercase; `NaN`/`Infinity`/leading zero **reddedilir** | [S2] |
| Kodlama | Ağda UTF-8; **BOM kabul edilmez/üretilmez** | [S2] |
| Üyelerin adı | MUST unique sayılmaz (SHOULD) — tekrar eden ad varsa **uyarı**: alıcı "son üye kazanır" davranabilir | [S2] |
| `eval()` | Kaynak JSON'u asla `eval()` ile çözme (güvenlik uyarısı) | [S2] |

### §2.2 XML yüzü (P3)

| Kontrol | Kural | Dayanak |
|---|---|---|
| Şema doğrulama | XML Schema Part 1'e göre **assessment**: simple/complex type, identity-constraint (key/keyref/unique) denetimi | [S1] |
| Instance nitelikleri | `xsi:type` / `xsi:nil` / `xsi:schemaLocation` tutarlılığı | [S1] |
| Kodlama | BOM varsa charset'ten otoriter; UTF-8+BOM'suz RECOMMENDED | [S4] |
| Medya tip | `application/xml`/`text/xml`/`+xml` doğru kayda | [S4] |

### §2.3 protobuf yüzü (P1)

| Kontrol | Kural | Dayanak |
|---|---|---|
| Decode | Bilinmeyen **alan numarası** atlanabilir (tag ile) — hata değil | [S3] |
| Wire type | 6 tip (VARINT/I64/LEN/SGROUP/EGROUP/I32); tag = `(field<<3)\|wire_type` | [S3] |
| Sınırlar | Serileştirilmiş mesaj **< 2 GiB**; string UTF-8, max 2 GB | [S3] |
| Deterministiklik | Varsayılan serileştirme **deterministik değildir** — hash/imza testleri bu yüzden yanıltır | [S3] |

---

## §3 L2 — Medya Tipi ve Kodlama Doğrulaması

| Çift yüzü | Beklenen | Reddedilecek | Dayanak |
|---|---|---|---|
| JSON yanıt | `application/json` | Uyuşmayan tip → çevirmen yeniden kodlamalı ya da reddetmeli | [S2], [S4] |
| XML yanıt | `application/xml` (+ charset/BOM kararı) | `text/xml`'de HTML yığını (RFC 7303'ün güvenlik notu) | [S4] |
| SSE istek yanıt | `Content-Type: text/event-stream` **olmak zorunda** — değilse bağlantı fail | Yanlış tip → EventSource fail olur | [S6] |
| Bearer istek | `Authorization: Bearer …` | Aynı istekte **ikinci** taşıma yöntemi (query/form birlikte) → `invalid_request` | [S7] |
| SOAP | `application/soap+xml` | Şekil hatası → `env:Fault`/`env:Sender` | [S8] |

> **Not (dürüstlük):** L2'nin "yanıtta medya tipi yanlışsa düşür" davranışı
> **TASARIM İDDİASI**'dır; şartname yalnızca "şu tipte üret/zorunlu"
> der, düşürme politikası proje kararıdır.

---

## §4 L3 — El Sıkışma / Pazarlık Doğrulaması

### §4.1 WebSocket Accept doğrulaması (P4) — [S5]

Beklenen değer: `base64( SHA-1( key_dizesi + "258EAFA5-E914-47DA-95CA-C5AB0DC85B11" ) )`.

| Adım | Doğrulama | Hata davranışı |
|---|---|---|
| 1 | Yanıt `101 Switching Protocols` mu? | Değilse HTTP hatası olarak işle |
| 2 | `Upgrade: websocket` ve `Connection: Upgrade` var mı? | Yoksa bağlantı **fail** |
| 3 | `Sec-WebSocket-Accept` beklenen SHA-1/base64 ile eşleşiyor mu? | Eşleşmiyorsa bağlantı **fail** |
| 4 | Sunucu, istemcinin istemediği extension/alt protokol bildirdi mi? | Bildirdiyse bağlantı **fail** |

> Bu 4 adım RFC 6455 §4.1 istemci doğrulama listesidir (kaynak [S5]).

### §4.2 SSE doğrulaması (P4) — [S6]

| Kontrol | Kural | Dayanak |
|---|---|---|
| Durum kodu | 200 değilse bağlantı fail; **204 reconnect'i durdurur** | [S6] |
| Medya tip | `text/event-stream` değilse fail | [S6] |
| Kodlama | Akış daima UTF-8 decode (BOM strip edilir) | [S6] |
| Alan ayrımı | `event`/`data`/`id`/`retry` — **literal** karşılaştırma (case folding yok) | [S6] |
| Dispatch | Boş satır gelmedikçe olay **gönderilmez** (yarım blok yutulur) | [S6] |
| `id` alanı | NULL (U+0000) içeriyorsa yok sayılır | [S6] |

### §4.3 Sürüm pazarlığı

| Çift | Sürüm sinyali | Doğrulama | Dayanak |
|---|---|---|---|
| WS | `Sec-WebSocket-Version: 13` | İstemci sürümü 13 değilse fail | [S5] |
| HTTP | İstek/yanıt sürüm satırı | Sürüm tanınmıyorsa 505 (HTTP Version Not Supported) | [S4] |
| gRPC/protobuf | Alan numaraları | Bilinmeyen alan atlanır (uyumlu); **yanlış wire type** hata | [S3] |
| SSE | `retry:` alanı | Yalnız ASCII rakam ise kabul; değilse yok sayılır | [S6] |

---

## §5 L4 — Kimlik Taşıma Doğrulaması (P5) — [S7]

| # | Kontrol | Hata çıktısı | Dayanak |
|---|---|---|---|
| 1 | İstekte **tek** taşıma yöntemi mi? (header XOR form XOR query) | `invalid_request` + HTTP 400 | [S7] |
| 2 | Header yönteminde `Bearer` şeması + `b64token` sözdizimi doğru mu? | 400/401 | [S7] |
| 3 | Token süresi/iptali (çözüm K009'da) | `invalid_token` + HTTP 401 | [S7] |
| 4 | Scope yetersizse | `insufficient_scope` + HTTP 403 (+ `scope` attribute) | [S7] |
| 5 | API key'e çevirirken **query'ye** düşürülüyor mu? | Engellenmeli — URL'de taşımak SHOULD NOT (log sızıntısı) | [S7] · engelleme TASARIM İDDİASI |
| 6 | Cookie'ye yazılıyor mu? | Bearer cookie'de **taşınmaz** (şartname: MUST NOT) | [S7] |

> **Doğrulama sınırı:** Bearer'ın **kendi** çözülmesi (imza/omur) K009'dur;
> bu katman yalnızca **taşınma şeklini** doğrular (sınır: proto-anayasa §2).

---

## §6 L5 — Sözleşme / Round-Trip Doğrulaması

| Test | Ne ispatlar | Ölçüm | Dayanak |
|---|---|---|---|
| A→B→A çevrimi (round-trip) | Kaybızlık: alan sayısı/değerleri korunur | eşitlik assert | TASARIM İDDİASI |
| Kayıp alan listesi | Hangi alanlar düştü (bilinmeyen alan politikası) | bilgi | TASARIM İDDİASI · proto-sozlesme §7.4 |
| Hata yutma testi | Hata kodu hiçbir yerde 200'e çevrilmez | matris | TASARIM İDDİASI |
| Charset testi | UTF-8 dışı girdi → kararlı reddetme | assert | [S2], [S4], [S6] |
| **Performans (round-trip gecikme)** | ⚠️ VERIFICATION REQUIRED — ölçüm yapılmadı, sayı uydurulmadı | — | — |

---

## §7 L6 — CI Kapısı

| Kapı | Koşul | Durum |
|---|---|---|
| G1 | L1–L4 birim testleri geçer | TASARIM İDDİASI (proje CI'ı mevcut: `.github/workflows/ci.yml` diskte — davranış ⚠️ doğrulanmadı) |
| G2 | Sözleşme testleri (round-trip) geçer | TASARIM İDDİASI |
| G3 | Kodlama/medya tipi matrisi (§3) geçer | TASARIM İDDİASI |
| G4 | Güvenlik: URL'de token / cookie taşımama kuralı (§5.5–5.6) | TASARIM İDDİASI |
| G5 | ⚠️ Bu kapıların repo'da fiilen tanımlı olupluğu → VERIFICATION REQUIRED | — |

> **Disk kanıtı (2026-10-16):** `.github/workflows/` envanteri bu dosyada
> **yeniden sayılmadı**; yalnız `shared/src/OAuth/Provider/` ve
> `shared/src/Cache/` listeleri okundu. CI dosyası iddiası bu yüzden
> sayı/URL içermeksizin yalnızca mevcut vault kayıtlarına atıfla taşındı.

---

## §8 Repo Disk Kanıtı (2026-10-16 okuma — davranış iddiası değil)

| Yüzey | Disk bulgusu | Doğrulama katmanı ile ilişkisi |
|---|---|---|
| `shared/src/OAuth/Provider/` | `IOAuthProvider.php`, `BaseOAuthProvider.php` + 10 somut sağlayıcı (Discord, Facebook, Instagram, LinkedIn, Pinterest, Reddit, Snapchat, TikTok, X, YouTube) | P5'in **sistem yüzü** (adapters kardeşi) — protokol çevirisi değil; L4 için sınır referansı |
| `shared/src/Cache/` | `CacheInterface.php`, `ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php` | Adaptör pattern'i repo'da yerleşik (isim kanıtı) — protokol adaptörü **değil** |
| `shared/src/Api/Routing/RouteTable.php` | Dosya mevcut | HTTP yüzeyinin varlığı (içerik bu turda okunmadı → ⚠️) |
| Protokol dönüşümü yapan sınıf | **Tespit edilmedi** | ⚠️ VERIFICATION REQUIRED |

---

## §9 Hata Yakalama Matrisi (katman × çift)

| Çift \ Katman | L1 şema | L2 medya | L3 el sıkışma | L4 kimlik |
|---|---|---|---|---|
| P1 HTTP↔gRPC | JSON/protobuf decode | Content-Type | — (gRPC kanal pazarlığı ⚠️) | metadata taşıma |
| P2 REST↔SOAP | XML/Envelope | `application/soap+xml` | — | header taşıma |
| P3 JSON↔XML | parse + schema | charset/BOM | — | — |
| P4 SSE↔WebSocket | blok/satır ayrımı | `text/event-stream` | WS Accept + sürüm 13 | Origin (WS) [S5] |
| P5 bearer↔API key | token sözdizimi | Authorization header | — | §5 matrisi |

---

## §9.1 Hata Kodu Eşleme Matrisi (dönüşüm yönü — TASARIM İDDİASI)

> Bu matris **çevirmenin** hata kodunu nasıl taşıdığını yazar; her hücre
> `proto-sozlesme.md` §7 (ortak tablo) ile uyumludur. Eşlemenin
> **şartname karşılığı olmayan** hücreleri açıkça etiketlenmiştir.

| Kaynak yüz → Hedef yüz | 400 | 401 | 403 | 406/415 | 5xx |
|---|---|---|---|---|---|
| HTTP → gRPC | INVALID_ARGUMENT | UNAUTHENTICATED | PERMISSION_DENIED | — (şartname yok → ⚠️) | map etmesi gereken: INTERNAL / UNAVAILABLE ⚠️ |
| gRPC → HTTP | 400 | 401 | 403 | — | 500/503 (eşleme ⚠️) |
| SOAP Fault `env:Sender` → HTTP | 400/422 (tasarım) | — | — | — | — |
| SOAP Fault `env:Receiver` → HTTP | — | — | — | — | 500 (tasarım) |
| Bearer `invalid_request/token/scope` → API key yüzü | 400 / 401 / 403 (birebir) | | | | özel gövde ⚠️ |

> **⚠️ Açık hücreler:** gRPC durum kodu ↔ HTTP durum kodu resmi eşleme
> sayfası bu turda çekilmedi (`proto-web-arastirma.md` §6 madde 2).
> Tablodaki eşlemeler **TASARIM İDDİASI**'dır; `⚠️` hücrelerde eşleme
> edilmemiştir (uydurulmamıştır).

---

## §9.2 Red Team Senaryoları (senaryo × beklenen yakalama)

> Mod: Red Team · Human Mode · Truth Mode. "Beklenen yakalama" sütunu
> yalnızca §1-§5'te tanımlı katmanlara atıf yapar; **yeni katman yoktur**.

| # | Senaryo (saldırı/girdi) | Bozulma | Yakalamalı | Beklenen sonuç |
|---|---|---|---|---|
| R1 | Gövde `{"a":1,}` (JSON kapanmıyor) | biçimsel | L1 (§2.1) | reddet, onarma yok |
| R2 | JSON'da `NaN` | sözdizimi | L1 (§2.1) | reddet |
| R3 | XML'de tanımsız entity (XXE benzeri yük) | şema/kodlama | L1-L2 (§2.2) | reddet — ayrıntı güvenlik katmanı K006 |
| R4 | Yanıt `Content-Type: text/html` + JSON gövdesi | medya tipi | L2 (§3) | düşür/reddet (politika TASARIM) |
| R5 | SSE yanıtı `text/event-stream; charset=iso-8859-1` | kodlama | L2 + L3 (§3, §4.2) | bağlantı fail (yalnız UTF-8) |
| R6 | WS Accept header'ında 1 bayt fark | el sıkışma | L3 (§4.1) | bağlantı fail |
| R7 | WS sürüm `Sec-WebSocket-Version: 8` | sürüm | L3 (§4.3) | fail / 400 |
| R8 | Aynı istekte `Authorization: Bearer` + `?access_token=` | kimlik | L4 (§5 satır 1) | `invalid_request` 400 |
| R9 | Bearer'ı query'ye çevirip log'a yazma | kimlik/sızıntı | L4 (§5 satır 5) | engellenmeli (tasarım) |
| R10 | Cookie'ye bearer yazma | kimlik | L4 (§5 satır 6) | MUST NOT ihlali — reddet |
| R11 | Protobuf'ta yanlış wire type (alan tipiyle uyuşmuyor) | şema | L1 (§2.3) | hata |
| R12 | Hata yanıtının 200'e çevrilmesi (yutma) | sözleşme | L5 (§6) | matris ihlali |
| R13 | UTF-8 dışı bayt dizisi (bozuk) | kodlama | L1/L2 (§6 charset satırı) | kararlı reddetme |
| R14 | Tek mesajda iki pazarlık başlığı (Accept + Upgrade çakışması) | pazarlık | L3 + §9.1 | öncelik kuralı TASARIM — bkz. §9.3 |

> **R14 notu:** Pazarlık sırası/önceliği (`proto-web-arastirma.md` §4 son
> satır) bir **TASARIM İDDİASI**'dır; bu senaryonun beklenen sonucu da
> tasarımdır, şartnameden gelmez.

---

## §9.3 Pazarlık Çakışma Kuralları (TASARIM İDDİASI)

| # | Çakışma | Öncelik kararı | Etiket |
|---|---|---|---|
| 1 | Aynı istekte `Accept` + `Upgrade` | El sıkışma (Upgrade) önce çözülür; bağlantı OPEN ise Accept uygulanır | TASARIM İDDİASI |
| 2 | `Content-Type` ile gövde şeması çelişiyor | Şema (L1) kazanır; medya tipi reddin gerekçesi olur | TASARIM İDDİASI |
| 3 | Bearer hata kodu ile SOAP Fault çelişiyor | Kaynak yüzün kodu korunur; hedef yüz ekler, **değiştirmez** | TASARIM İDDİASI · §6 L5 |
| 4 | İki kodlama iması (charset parametresi + BOM) | Charset parametresi otoriter → XML (RFC 7303) | [S4] |
| 5 | gRPC metadata ↔ HTTP header çakışması (aynı isim) | `grpc-` öneki reserved — HTTP'ye olduğu gibi taşınmaz | `proto-sozlesme.md` S4 (bu dosyanın S3'ü protobuf'dur) |

---

## §10 Açık Sorular (dosya `open_questions` özeti)

1. CoreMusic test suite'inde protokol çifti testi var mı → ⚠️ VERIFICATION REQUIRED
2. Doğrulama üretimde mi testte mi → UNKNOWN (ADR bekliyor)
3. XML yüzeyi repo'da kullanılıyor mu → ⚠️ VERIFICATION REQUIRED
4. CI kapıları (G1–G4) repo'da fiilen var mı → ⚠️ VERIFICATION REQUIRED

---

## §11 Kabul Kriterleri (bu dosyanın kendi tamamlanma testi)

| # | Kriter | Durum |
|---|---|---|
| 1 | Her katmanın (L1-L6) şartname veya disk dayanağı var | ✅ §1 tablosu |
| 2 | Her URL §Kaynak Tablosu'nda tarih+durum ile | ✅ S1-S9 (2026-10-16) |
| 3 | Ölçülmeyen hiçbir sayı yazılmadı (performans/coverage) | ✅ §6 satır 5 `⚠️` |
| 4 | Repo iddiası disk listesiyle sınırlı | ✅ §8 + §7 disk notu |
| 5 | Yeni protokol çifti iddiası edilmedi (5 çift korunur) | ✅ §9 matrisi P1-P5 |
| 6 | CI kapıları "var" diye yazılmadı | ✅ §7 G5 `⚠️` |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | XML Schema Part 1: assessment, tipler, identity-constraint, xsi:* | https://www.w3.org/TR/xmlschema-1/ | 2026-10-16 | ✅ çekildi |
| S2 | JSON sözdizimi, UTF-8/BOM, literal kuralları, eval() uyarısı | https://www.rfc-editor.org/rfc/rfc8259.html | 2026-10-16 | ✅ çekildi |
| S3 | protobuf wire format: tag/wire type, bilinmeyen alan atlama, 2 GiB, deterministiklik | https://protobuf.dev/programming-guides/encoding/ | 2026-10-16 | ✅ çekildi |
| S4 | XML medya tipleri, BOM/charset otoritesi, `text/xml` güvenlik notu | https://www.rfc-editor.org/rfc/rfc7303.html | 2026-10-16 | ✅ çekildi |
| S5 | WebSocket istemci doğrulama adımları (Accept, Upgrade, extension) | https://www.rfc-editor.org/rfc/rfc6455.html | 2026-10-16 | ✅ çekildi |
| S6 | SSE: medya tipi zorunluluğu, 204, alan ayrımı, UTF-8, `retry`/`id` kuralları | https://html.spec.whatwg.org/multipage/server-sent-events.html | 2026-10-16 | ✅ çekildi |
| S7 | Bearer: tek yöntem kuralı, 400/401/403 hata eşlemesi, URL/cookie yasakları | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-16 | ✅ çekildi |
| S8 | SOAP Fault (`env:Sender` vb.) ve `application/soap+xml` | https://www.w3.org/TR/SOAP12/ | 2026-10-16 | ✅ çekildi |
| S9 | HTTP durum kodu kümesi (400/415/422/505 vb.) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `TASARIM İDDİASI` satırları dış kaynaksız proje kararlarıdır;
> `⚠️ VERIFICATION REQUIRED` satırlarında kaynak yoktur.

---

## §12 Sınır Notları (bu dosyanın kapsam dışı bıraktıkları)

| # | Konu | Nereye gider | Neden bu dosyada değil |
|---|---|---|---|
| 1 | Token'ın kriptografik çözülmesi (imza, ömür, iptal) | K009 kimlik/güvenlik | Bu dosya yalnız **taşınma şeklini** doğrular (§5) |
| 2 | TCP/TLS/HTTP2 bağlantı kurma, retry, backoff zamanlaması | K014-network | L3 yalnızca **başlık/biçim** doğrular, soket değil |
| 3 | Üçüncü parti sistem entegrasyonu (Discord vb. sağlayıcılar) | K013 `adapters` (kardeş) | Orası SYSTEM/ACL dönüşümüdür, protokol değil (proto-anayasa §2) |
| 4 | İş kuralı doğrulaması (geçerlilik, yetki iş mantığı) | K008-domain | Protokol düzlemi iş düzlemine karıştırılmaz (proto-anayasa §4) |
| 5 | Gözlemlenebilirlik (metrik/trace/log alan adları) | Gözlemlenebilirlik düğümü — **adı UNKNOWN** (registry'de karşılığı bu turda doğrulanmadı) | Bu dosyada telemetri iddiası edilmez |

> **Not:** 5 numaralı satırdaki hedef düğüm adı bilinmiyor; registry'de
> `observability`/`telemetry` karşılığı **aranmadı** → `UNKNOWN`
> (uydurulan düğüm adı yazılmadı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · W3C/IETF/WHATWG · **Tarih: 2026-10-16**
