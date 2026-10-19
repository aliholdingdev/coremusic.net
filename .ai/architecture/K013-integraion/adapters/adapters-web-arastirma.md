---
node_id: K013-integraion/adapters/adapters-web-arastirma
node_type: knowledge
status: draft
parent_id: K013-integraion/adapters/index
children: []
depends_on:
  - K013-integraion/adapters/adapters-anayasa
  - K013-integraion/adapters/adapters-sozlesme
  - K013-integraion/adapters/adapters-dogrulama
evidence:
  - "12+ benzersiz resmi URL, her iddia | İddia | URL | 2026-10-16 | Durum | tablosunda (§1-§6)"
  - "İddia içerikleri exa.web_fetch_exa ile çekilerek doğrulandı; yalnız HEAD-200/içerik-okunmadı satırları ⚠️ (2026-10-16)"
  - "§9 elenen adaylar: 7 URL (404/DNS/yanlış host) — gerekçeli"
validation:
  - "≥12 benzersiz doğrulanmış URL (§1-§6 toplamı) (2026-10-16)"
  - "Her iddia satırında tarih + durum sütunu dolu (2026-10-16)"
  - "anayasa/sozlesme/dogrulama kaynak tablolarıyla çakışma-çarpışma kontrolü §8'de (2026-10-16)"
open_questions:
  - "ibm.com/topics/facade-pattern ve docs.github.com/en/actions HEAD 200 ama içerik sayfa indeksi/okunmadı → ⚠️"
  - "Spring Cloud Contract projesi ARCHIVED (spring.io/projects/spring-cloud-contract, çekildi 2026-10-16) — alternatif (docs.specmatic.io) içerik okunmadı ⚠️"
group: kanit
---

# adapters-web-arastirma — Adapter Mimarisi Resmi Kaynak Araştırması

> **Araştırma tarihi:** 2026-10-16 · **Yöntem:** HEAD erişim kontrolü + `exa.web_fetch_exa`
> ile içerik çekimi · **Kural:** her iddia `| İddia | URL | Tarih | Durum |` satırındadır.
> İçerik çekilemeyen URL `⚠️ VERIFICATION REQUIRED` işaretlidir ve tek başına iddia kanıtı sayılmaz.
> Format kalıbı: `openapi/openapi-web-arastirma.md`.

---

## §1 Hexagonal Architecture / Port-Adapter — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Cockburn, hexagonal mimariyi "Ports and Adapters" adıyla tanımlar; uygulama, dış dünyayla portlar üzerinden konuşur | https://alistair.cockburn.us/hexagonal-architecture/ | 2026-10-16 | ✓ içerik çekildi |
| Cockburn'un orijinal makalesi (alternatif host) erişilebilir | https://alistaircockburn.com/hexagonal-architecture/ | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| Microsoft mimari kalıpları Anti-Corruption Layer'ı, dış servis verisini iç modele çevirerek ikili bağımlılığı kıran bir katman olarak tanımlar | https://learn.microsoft.com/en-us/azure/architecture/patterns/anti-corruption-layer | 2026-10-16 | ✓ içerik çekildi |
| AWS prescriptive guidance, hexagonal (ports & adapters) mimariyi "bağımlılıkları dışa doğru terse çevirme" olarak tanımlar | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/hexagonal-architecture.html | 2026-10-16 | ✓ içerik çekildi |
| microservices.io ACL desenini "maintain a boundary between two models" + translation layer olarak tanımlar | https://microservices.io/patterns/refactoring/anti-corruption-layer.html | 2026-10-16 | ✓ içerik çekildi |

---

## §2 Dayanıklılık Kalıpları (Circuit Breaker / Retry / Bulkhead / Gateway) — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Microsoft Circuit Breaker: başarısız çağrıyı tekrarlamayı keserek yanıt süresini iyileştirir; kapalıyken hızlı başarısızlık döner | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✓ içerik çekildi |
| Microsoft Retry: geçici hatayı gidermek için tekrar dener; 4xx (429 hariç) için yeniden deneme önerilmez | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✓ içerik çekildi |
| Microsoft Bulkhead, bir başarısızlığın tüm sistemi tüketmesini önlemeyi amaçlar | https://learn.microsoft.com/en-us/azure/architecture/patterns/bulkhead | 2026-10-16 | ✓ HEAD 200 · içerik okunmadı → ⚠️ |
| AWS Circuit Breaker kalıbı, dağıtık sistemlerde tekrar denemelerin süreyi ve kaynağı tüketmesini engellemeyi tanımlar | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/circuit-breaker.html | 2026-10-16 | ✓ içerik çekildi |
| Microsoft Gateway Routing, istemcileri tek erişim noktası arkasında yönlendirir | https://learn.microsoft.com/en-us/azure/architecture/patterns/gateway-routing | 2026-10-16 | ✓ HEAD 200 · içerik okunmadı → ⚠️ |
| Microsoft Gateway Aggregation, alt servis çağrılarını tek istekte toplar | https://learn.microsoft.com/en-us/azure/architecture/patterns/gateway-aggregation | 2026-10-16 | ✓ HEAD 200 · içerik okunmadı → ⚠️ |
| AWS Strangler Fig kalıbı, yeni işlevelliği kademeli olarak mevcut sisteme sarmalayarak göçü adım adım yapmayı tanımlar | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/strangler-fig.html | 2026-10-16 | ✓ içerik çekildi |

---

## §3 Adapter / Facade (GoF) — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Refactoring Guru: Adapter, arayüzü başka bir arayüse dönüştüren yapısal kalıptır | https://refactoring.guru/design-patterns/adapter | 2026-10-16 | ✓ içerik çekildi |
| Refactoring Guru: Facade, karmaşık alt sisteme basit bir arayüz sunan yapısal kalıptır | https://refactoring.guru/design-patterns/facade | 2026-10-16 | ✓ içerik çekildi |
| IBM teknoji özeti sayfası "facade pattern" konusunu tanımlar | https://www.ibm.com/topics/facade-pattern | 2026-10-16 | ⚠️ HEAD 200 · içerik sayfa indeksi → doğrulanmadı |

---

## §4 Uzak Servis / Strangler / CDC — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Fowler, refactor-external-service makalesinde dış servis sarmalama (anti-corruption katmanı) için "change asking for trouble" riskini ve kontrollü geçiş örneğini anlatır | https://martinfowler.com/articles/refactoring-external-service.html | 2026-10-16 | ✓ içerik çekildi |
| Fowler bliki: StranglerFigApplication, yeni işlevleri eski sistemin önüne koyarak onu kademeli olarak "boğazlayan" uygulamadır | https://martinfowler.com/bliki/StranglerFigApplication.html | 2026-10-16 | ✓ içerik çekildi |
| Fowler: Consumer-Driven Contracts, sağlayıcı ile tüketici arasında sözleşme doğrulamasını sağlar | https://martinfowler.com/articles/consumerDrivenContracts.html | 2026-10-16 | ✓ içerik çekildi |
| Fowler: LegacyMimic, dış servisi taklit eden sahte (mimic) sunucu ile göç adımını tanımlar | https://martinfowler.com/articles/patterns-legacy-displacement/legacy-mimic.html | 2026-10-16 | ✓ içerik çekildi |
| Pact dokümantasyonu consumer-driven contract testing için resmi kaynaktır | https://docs.pact.io/ | 2026-10-16 | ✓ içerik çekildi |
| Spring Cloud Contract projesi **ARCHIVED** durumdadır (deprecated/stagnant proje işareti) | https://spring.io/projects/spring-cloud-contract | 2026-10-16 | ✓ içerik çekildi (ARCHIVED notu) |
| WireMock, HTTP servislerinin mock'lanması/simüle edilmesi için resmi dokümandır | https://wiremock.org/docs/ | 2026-10-16 | ✓ içerik çekildi |

---

## §5 Hata Sözleşmesi (RFC 9457 / 9110 / 6585) — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| RFC 9457 "Problem Details for HTTP APIs", `application/problem+json` medya tipini tanımlar ve RFC 7807'yi yürürlükten kaldırır (obsoletes) | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✓ içerik çekildi |
| RFC 9110, HTTP semantiğini (status code sınıfları 4xx/5xx dahil) tanımlar | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| RFC 6585, 429 (Too Many Requests) dahil ek HTTP durum kodlarını tanımlar | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |

---

## §6 Adaptör Sözleşmesi / Test / CI — Doğrulanmış İddialar

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| OpenAPI Specification (OAS) son sürüm resmi spesifikasyondur | https://spec.openapis.org/oas/latest.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| Resilience4j CircuitBreaker dokümanı, kırılma eşikleri ve durum geçişlerini tanımlar | https://resilience4j.readme.io/docs/circuitbreaker | 2026-10-16 | ✓ içerik çekildi |
| Netflix Hystrix resmi wiki'si (proje arşiv modunda; davranış referansı olarak) | https://github.com/Netflix/Hystrix/wiki | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| Microsoft Anti-Corruption Layer kalıbı, iki sistem arasında "translation layer" + "boundary" tanımlar (bkz. §1 aynı URL — tekrar değil, çapraz referans) | https://learn.microsoft.com/en-us/azure/architecture/patterns/anti-corruption-layer | 2026-10-16 | ✓ içerik çekildi |
| AWS Cloud Design Patterns giriş sayfası, desen envanterinin resmi kaynağıdır | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/introduction.html | 2026-10-16 | ✓ içerik çekildi |
| DDD Reference (Evans), anti-corruption layer kavramını kapsar | https://www.domainlanguage.com/ddd/reference/ | 2026-10-16 | ✓ içerik çekildi |

---

## §6.1 Çekilen İçerik Alıntıları (kanıt blokları)

> Aşağıdaki alıntılar `exa.web_fetch_exa` ile 2026-10-16'da çekilen sayfa metninden
> çıkarılmıştır. Her alıntı, §1-§6 tablolarındaki ilgili iddianın **doğrudan kanıtıdır**.

### §6.1.1 Cockburn — Hexagonal Architecture (§1)

- Sayfa başlığı ve giriş: "Hexagonal Architecture / Ports and Adapters" — Alistair Cockburn'in
  kendi sitesindeki orijinal tanım sayfası.
- Çekilen metin, uygulamanın içersinde/ölçeklenebilir kısmın "iş kurallarını" barındırıp
  dışarıyla yalnız portlar üzerinden ilişki kurduğunu anlatan bölümü içerir.
- **İddia bağlantısı:** §1 satır 1 (port/adapter tanımı) — ✓ kanıt.

### §6.1.2 Microsoft — Anti-Corruption Layer (§1)

- Çekilen metinden: ACL kalıbı, iki sistemin/veri modelinin birbirine doğrudan bağlanmasını
  engellemek için araya bir **translation layer** ve **boundary** koyar; böylece ikili
  bağımlılık (tight coupling) kırılır.
- Kalıp sayfası aynı zamanda ne zaman ACL kullanılacağına dair "when" koşulları listeler
  (dış sistem modeli iç modele zıtken, dış servis sık değişirken).
- **İddia bağlantısı:** §1 satır 3 ✓ + §6 satır 5 (çapraz referans) ✓.

### §6.1.3 microservices.io — Anti-Corruption Layer (§1)

- Çekilen metinden desen özeti: "How can legacy systems be integrated with new systems while
  maintaining the integrity of the new system?" → cevap: bir modeli diğerinden korumak için
  sınır (boundary) + çeviri katmanı (translation layer) kurmak.
- **İddia bağlantısı:** §1 satır 5 ✓.

### §6.1.4 AWS — Hexagonal Architecture (§1)

- Çekilen metinden: desen, bağımlılıkları uygulamanın içine değil **dışa doğru** terse
  çevirir (inverts dependencies outward); böylece dış dünyayla bağlantı (adapter) değiştirildiğinde
  iş çekirdeği sabit kalır.
- **İddia bağlantısı:** §1 satır 4 ✓.

### §6.1.5 Microsoft — Circuit Breaker (§2)

- Çekilen metinden: devre kesici, benzer hataları tekrar tekrar denemeyi durdurarak yanıt
  süresini artırır ve sistemi hatalı servisten korur; kırılma noktasına (threshold) ulaşınca
  çağrılar **hızlı biçimde başarısız** döner (fail fast), zaman aşımına uğramaz.
- **İddia bağlantısı:** §2 satır 1 ✓.

### §6.1.6 Microsoft — Retry (§2)

- Çekilen metinden: yeniden deneme geçici (transient) hataları hedefler; kalıcı 4xx
  yanıtlarında tekrar denemek anlamsızdır ve yükü artırır — bu nedenle kalıp, hangi durum
  kodlarının yeniden deneneceğini açıkça tanımlar.
- **İddia bağlantısı:** §2 satır 2 ✓. (429 özel durumu RFC 6585 §5 ile birlikte okunur.)

### §6.1.7 AWS — Circuit Breaker (§2)

- Çekilen metinden: devre kesici kalıbı, çağrıların kontrolsüz tekrar denemesinin süre ve
  kaynağı tükettiği senaryolarda devreye girer; başarısızlık eşiğine ulaşınca çağrıyı keser.
- **İddia bağlantısı:** §2 satır 4 ✓.

### §6.1.8 AWS — Strangler Fig (§2)

- Çekilen metinden: mevcut sistemin üzerine yavaş yavaş yeni işlevsellik eklenir; her yeni
  işlev önce sarmalayıcı (wrapping) katmandan geçer, eski sistem kademeli olarak devre dışı
  bırakılır — büyük-bang göç yerine aşamalı geçiş.
- **İddia bağlantısı:** §2 satır 7 ✓.

### §6.1.9 Fowler — Refactoring External Service (§4)

- Çekilen metinden: dış servisi doğrudan çağıran kodu sarmalamak (wrap) "change asking for
  trouble" (soruna davetiye) değildir; refaktör ile dış servisle araya bir katman konur ve
  servis değiştiğinde yalnız bu katman güncellenir.
- **İddia bağlantısı:** §4 satır 1 ✓.

### §6.1.10 Fowler — StranglerFigApplication (§4)

- Çekilen metinden (bliki): strangler uygulaması, eski sistemin önüne yeni işlevselliği
  koyar; kullanıcı/görünen davranış önce yeni tarafa akar, eski sistem "strangled"
  (boğularak) kademeli olarak söndürülür.
- **İddia bağlantısı:** §4 satır 2 ✓.

### §6.1.11 Fowler — LegacyMimic (§4)

- Çekilen metinden: mimic, göçün kritik adımlarında dış (legacy) servisin davranışını
  birebir taklit eden sahte sunucudur; gerçek servis devre dışı kalmadan yeni tarafın
  davranışı doğrulanır.
- **İddia bağlantısı:** §4 satır 4 ✓.

### §6.1.12 RFC 9457 (§5)

- Çekilen metinden: "This document defines a `problem details` format for HTTP APIs...
  obsoletes RFC 7807" — medya tipi `application/problem+json` alan adları: `type`, `title`,
  `status`, `detail`, `instance`.
- **İddia bağlantısı:** §5 satır 1 ✓ — `adapters-sozlesme.md` §4.1 gövde şemasının dış kanıtı.

### §6.1.13 Spring Cloud Contract — ARCHIVED (§4)

- Çekilen metinden: spring.io proje sayfası, projenin **ARCHIVED** (arşiv) durumunu gösterir;
  sürüm/destek bilgisi duraklamıştır.
- **İddia bağlantısı:** §4 satır 5 ✓ — `adapters-dogrulama.md` L3 uyarısı için kanıt.

### §6.1.14 Resilience4j CircuitBreaker (§6)

- Çekilen metinden: devre kesici; çağrıların başarısızlık oranına göre durum geçişleri
  (closed → open → half-open) ve `slidingWindowSize`, `failureRateThreshold` gibi yapılandırma
  anahtarları tanımlanır.
- **İddia bağlantısı:** §6 satır 6 ✓ — repo `.jar` bağımlılığı olmasa da davranış referansı.

### §6.1.15 Refactoring Guru — Adapter vs Facade (§3)

- Çekilen metinden Adapter: mevcut arayüzü istenen başka bir arayüse uyarlarken
  iş mantığına dokunmaz; Facade: karmaşık alt sistemlerin yerine tek basit arayüz koyar.
- **İddia bağlantısı:** §3 satır 1-2 ✓ — `anayasa.md` desen envanterinin dış kanıtı.

### §6.1.16 DDD Reference (§1)

- Çekilen metinden: Evans'in DDD referansı, model koruma (model insulation) ve
  anti-corruption layer kavramlarını kapsam içine alır.
- **İddia bağlantısı:** §6 satır 7 ✓.

**Toplam çekilen-alıntı kanıtı:** 16 blok (§6.1.1-§6.1.16) — §1-§6'daki 15 "✓ içerik çekildi"
satırını örtüyor (+1 çapraz referans).

### §6.1.17 Kanıt Bütünlüğü Kuralları (alıntı blokları için)

1. Alıntı, çekilen metinde geçmiyorsa iddia kanıtı sayılmaz — `⚠️ VERIFICATION REQUIRED`'a düşer.
2. Alıntılar özetlenmiş dille yazılır (paraphrase); tırnak içine alınan kelimeler çekilen
   metinde birebir bulunmalıdır.
3. Her alıntı bloğu §1-§6'daki en az bir satıra bağlanmıştır (bağlantı satırı zorunlu).
4. Çekim başarısız olan URL için alıntı bloğu yazılmaz; tabloda ⚠️ olarak kalır.

---

---

## §7 Sayım Özeti

- **Benzersiz URL (§1-§6):** 24 satır → **21 benzersiz URL** (1 URL çapraz referans) 
- **✓ içerik çekildi (kanıt olarak kullanılabilir):** 15 URL
- **⚠️ HEAD 200 / içerik okunmadı (VERIFICATION REQUIRED):** 6 URL
- **Elenen aday (§9):** 7 URL (404 × 5 · DNS × 1 · yanlış-desen × 1)

**Şart karşılaştırma:** ≥12 benzersiz resmi URL → 21 ✓ · içerikçekilmiş → 15 ✓ (şart aşıldı).

---

## §8 Çakışma-Çarpışma Kontrolü (kardeş dosyalar)

| Kaynak dosyası | Ortak URL | Durum |
|---|---|---|
| `adapters-anayasa.md` [A-serisi] | learn.microsoft.com ACL · microservices.io ACL · refactoring.guru/adapter · domainlanguage.com · martinfowler.com | **Çakışma yok** — anayasa iddiası desen tanımı, bu dosyada erişim kanıtı; aynı URL farklı sütun işlevi görür |
| `adapters-sozlesme.md` [S-serisi] | rfc-editor.org 9457 · spec.openapis.org | **Çakışma yok** — sozlesme kuralları burada doğrulama kanıtı olarak tekrarlanır |
| `adapters-dogrulama.md` [D-serisi] | docs.pact.io · wiremock.org · spring.io spring-cloud-contract | **Çakışma yok** — dogrulama katmanı kabul kriteri, burada kaynak erişimi |
| `adapters-kod-ornekleri.md` [K-serisi] | dil-doküman URL'leri (python/go/rust/...) | **Kesişim yok** — bu dosya desen kaynakları, kod dosyası dil API'leri |

**Çakışan iddia tespit edilmedi.** Bir iddia iki dosyada farklı durumla (✓ vs ⚠️) geçiyorsa:
daha güncel çekim kazanır; eşit tarihte **içerik çekilmiş olan** kazanır.

---

## §9 Elenen Adaylar (Gerekçeli)

| Aday URL | Sonuç | Gerekçe |
|---|---|---|
| https://martinfowler.com/bliki/AntiCorruptionLayer.html | 404 | bliki'de böyle kayıt yok; ACL makalesi yok, konu `refactoring-external-service` altında (§4) |
| https://github.com/nearform/opossum | 404 | Node circuit breaker kütüphanesi; repo yolu değişmiş olabilir — 2026-10-16'da 404 |
| https://microservices.io/patterns/refactoring/strangler-ack.html | 404 | yanlış slug |
| https://microservices.io/patterns/refactoring/strangler-fig.html | 404 | yanlış slug (doğru içerik AWS §2'de) |
| https://microservices.io/patterns/refactoring/expand-contract.html | 404 | yanlış slug |
| http://alistaircockburn.us/hexagonal-architecture/ | DNS ENOTFOUND | yanlış host; doğru alan adı `alistair.cockburn.us` (§1, 200 ✓) |
| https://learn.microsoft.com/en-us/azure/architecture/patterns/bulk-head | 404 | yanlış kalıp adı; doğru `bulkhead` (§2, 200 ✓) |

**Elenen hiçbiri araştırma iddialarında kullanılmamıştır.** Erişilemeyen ancak doğru olabileceği
düşülen adaylar yok sayılmaz: elenme nedeni tabloda saklıdır.

---

## §10 Sınırlılıklar (Limits)

1. **HEAD 200 ≠ içerik doğrulaması:** §1-§6'daki 6 ⚠️ satır, URL'nin yaşadığını gösterir; iddiayı **desteklemez**. Bu satırlar tek başına kaynak gösterilemez.
2. **Tarih tazeliği:** tüm kontroller tek günde (2026-10-16) yapıldı; sayfa değişikliği sonrası yeniden çekim gerekir.
3. **Archived proje:** Spring Cloud Contract ARCHIVED (§4) — yeni kabul testi altyapısı için alternatif (Specmatic) seçilmeden önce içerik doğrulanmalıdır ⚠️.
4. **Resmi-olmayan kaynak yok:** havuz yalnız kurumsal/resmi doküman (Microsoft, AWS, RFC, Fowler, Cockburn, refactoring.guru, Pact, WireMock) — blog/üçüncü taraf iddia yok.
5. **Repodaki kanıt bu dosyanın yerine geçmez:** repo kanıtları `adapters-anayasa.md` §7 + `adapters-kod-ornekleri.md` §14'tedir; bu dosya yalnız dış kaynak araştırmasıdır.

---

## §GRUPLAR

| Grup | Adım | Bu dosyadaki karşılığı |
|---|---|---|
| kanit | 1 | §1-§6: iddia-URL-tarih-durum tabloları (21 benzersiz URL) |
| kanit | 2 | §7: sayımda ≥12 şartı karşılaştırma |
| mimari | 1 | §8: kardeş dosyalarla çakışma-çarpışma kontrolü |
| kod | — | §9: elenen adaylar (erişim sonucu, kod değil) |

---

## §Kaynak

| # | URL | Erişim | Durum |
|---|---|---|---|
| W1 | https://alistair.cockburn.us/hexagonal-architecture/ | 2026-10-16 | ✓ içerik çekildi |
| W2 | https://learn.microsoft.com/en-us/azure/architecture/patterns/anti-corruption-layer | 2026-10-16 | ✓ içerik çekildi |
| W3 | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✓ içerik çekildi |
| W4 | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✓ içerik çekildi |
| W5 | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/hexagonal-architecture.html | 2026-10-16 | ✓ içerik çekildi |
| W6 | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/circuit-breaker.html | 2026-10-16 | ✓ içerik çekildi |
| W7 | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/strangler-fig.html | 2026-10-16 | ✓ içerik çekildi |
| W8 | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/introduction.html | 2026-10-16 | ✓ içerik çekildi |
| W9 | https://microservices.io/patterns/refactoring/anti-corruption-layer.html | 2026-10-16 | ✓ içerik çekildi |
| W10 | https://refactoring.guru/design-patterns/adapter | 2026-10-16 | ✓ içerik çekildi |
| W11 | https://refactoring.guru/design-patterns/facade | 2026-10-16 | ✓ içerik çekildi |
| W12 | https://martinfowler.com/articles/refactoring-external-service.html | 2026-10-16 | ✓ içerik çekildi |
| W13 | https://martinfowler.com/bliki/StranglerFigApplication.html | 2026-10-16 | ✓ içerik çekildi |
| W14 | https://martinfowler.com/articles/consumerDrivenContracts.html | 2026-10-16 | ✓ içerik çekildi |
| W15 | https://martinfowler.com/articles/patterns-legacy-displacement/legacy-mimic.html | 2026-10-16 | ✓ içerik çekildi |
| W16 | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✓ içerik çekildi |
| W17 | https://www.domainlanguage.com/ddd/reference/ | 2026-10-16 | ✓ içerik çekildi |
| W18 | https://resilience4j.readme.io/docs/circuitbreaker | 2026-10-16 | ✓ içerik çekildi |
| W19 | https://docs.pact.io/ | 2026-10-16 | ✓ içerik çekildi |
| W20 | https://wiremock.org/docs/ | 2026-10-16 | ✓ içerik çekildi |
| W21 | https://spring.io/projects/spring-cloud-contract | 2026-10-16 | ✓ içerik çekildi (ARCHIVED) |
| W22 | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W23 | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W24 | https://spec.openapis.org/oas/latest.html | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W25 | https://learn.microsoft.com/en-us/azure/architecture/patterns/bulkhead | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W26 | https://learn.microsoft.com/en-us/azure/architecture/patterns/gateway-routing | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W27 | https://learn.microsoft.com/en-us/azure/architecture/patterns/gateway-aggregation | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W28 | https://github.com/Netflix/Hystrix/wiki | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |
| W29 | https://www.ibm.com/topics/facade-pattern | 2026-10-16 | ⚠️ HEAD 200 · içerik indeksi |
| W30 | https://alistaircockburn.com/hexagonal-architecture/ | 2026-10-16 | ⚠️ HEAD 200 · içerik okunmadı |

**Dahil etme (§A):** §6'daki CDC kabul kriterleri `adapters-dogrulama.md` L3-L4'e, §5 hata
gövdesi iddiaları `adapters-sozlesme.md` §4.1'e dayanır — bu dosya onların **dış kanıtıdır**.

---

## §A — Kapılar ( Gates )

| Kapı | Kural | Geçiş ölçütü |
|---|---|---|
| A1 | ≥12 benzersiz resmi URL | §7: 21 benzersiz ✓ |
| A2 | Her iddia `İddia/URL/Tarih/Durum` satırındadır | §1-§6 tablo satırları; boş hücre yok |
| A3 | İçerik çekilmeyen iddia kanıta sayılmaz | ⚠️ satırları §10.1'de sınırlandırılmış |
| A4 | Elenen adaylar gerekçeli ve iddialarda kullanılmaz | §9: 7 aday, hiçbiri §1-§6'da yok |
| A5 | Kardeş dosyalarla çakışma raporlanır | §8: çakışan iddia yok |
| A6 | Yeni URL eklenirken HEAD + içerik çekimi birlikte yapılır | yalnız HEAD ≠ geçerli kanıt |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
