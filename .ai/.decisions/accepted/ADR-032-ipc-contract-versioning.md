---
id: ADR-032
title: IPC Sözleşmesi ve Sürümleme — Şema, Semver Benzeri Sürüm, Geriye Uyumluluk, Doğrulama, Olay Zarfı Sürümü
type: adr
category: architecture
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-032 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Tech Lead", "Backend Developer", "Embedded Engineer", "DSP Firmware Engineer"]
consulted: ["Data Engineer", "Security Engineer", "Windows Software Engineer"]
informed: ["QA Engineer", "DevOps Engineer", "Master Orchestrator"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]"
  - "[[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]]"
  - "[[.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md]]"
  - "[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]"
---

# ADR-032: IPC Sözleşmesi ve Sürümleme — Şema, Sürüm, Geriye Uyumluluk, Doğrulama, Olay Zarfı Sürümü

## 1. Bağlam ve Kod Kanıtı

### 1.1 Proje durumu (kod kanıtı — IMPLEMENTED / PLANNED ayrımı)

Bu ADR, CoreMusic'te **mesaj formatı + alan şeması + hata kodları + sürüm + geriye uyumluluk + doğrulama + olay zarfı sürümü** sözleşmesini yazır. Kanıt iki kategoride derlendi: **IMPLEMENTED** (kodda mevcut) ve **PLANNED** (vault spesifikasyonu var, kod karşılığı 0).

**IMPLEMENTED (kodda mevcut):**

- **Sürüm çözümleme + müzakere (version negotiation)** — `shared/src/Api/Versioning/VersionResolver.php:34-48`: önce URI path (`/api/v1|v2|internal|public|admin`, `:61-67`), sonra `Accept-Version` header'ı (`:71-81`), bulunamazsa **default `V1`** → üç kademeli fallback fiilen çalışıyor.
- **Sürüm enum + kayıt-defteri** — `shared/src/Api/Versioning/ApiVersion.php:17-24` (5 değer) · `shared/src/Api/Versioning/VersionRegistry.php:24-46` (`registerRoute` / `matchRoute`).
- **URL-segment sürümü + uç haritası** — `shared/src/Api/Gateway.php:82-87` → `/api/v1/{auth,user,music,playlist,media,download}`; middleware allowlist `shared/src/Api/Middleware/AuthenticationMiddleware.php:74-77`.
- **Sürüm zarfı (envelope) header + body** — `shared/src/Api/ApiResponse.php:21` `private const API_VERSION = '1.0.0'`; `:31,54,84,121` → `X-API-Version` header; `:37,96,127` → body'de `version` alanı.
- **Çalışma zamanı şema doğrulama (kurallı)** — `shared/src/Api/Middleware/RequestValidationMiddleware.php:28-38,60-64`: route'a bağlı `validation` kuralları → `Validator` (bağımlılık `shared/composer.json:18` → `respect/validation ^2.0`). **JSON-Schema kütüphanesi composer'da YOK** (`opis/json-schema|justinrainbow|json-schema` grep → 0) ve repo genelinde `*.schema.json` = **0 dosya** → JSON-Schema katmanı **PLANNED**.
- **Yüzey-spesifik dönüşüm (web ↔ masaüstü ↔ mobil ↔ gömülü)** — `shared/src/Api/Bff/BffLayer.php:16-40` + `DesktopBff.php`, `MobileBff.php`, `EmbeddedBff.php`, `SpaBff.php` (`shared/src/Api/Bff/`) → aynı gövde 4 istemci yüzeyine farklı biçimde sarılıyor; sözleşmenin yüzey başına sürüm kapsamı bu katmana bağlı.
- **Olay arayüzleri — zarf var, SÜRÜM YOK** — `shared/src/Contracts/Events/IntegrationEventInterface.php:24-46` (`eventName`, `source`, `payload`, `occurredOn`) ve `shared/src/Contracts/Events/DomainEventInterface.php:20-40`; **`version`/`schemaVersion` alanı 0** → bu ADR'nin (e) ekseninin açık noktası. Olay sınıfları mevcut: `shared/src/Events/Domain/*Event.php` (9 adet) + `shared/src/Events/Integration/*Event.php` (3 adet).
- **Veritabanı şema sürümü takibi (veri katmanı)** — `.ai/.sql/mysql/coremusic_patch.sql:13-14` → `schema_versions` tablosu ("Her DB'nin versiyon takibi"); `.ai/.sql/mysql/coremusic_system.sql:301-318` → `system_schema_versions` (+ `uk_schema_versions_db_version` unique key).
- **IPC'ye tek kod geçişi (yorum satırı)** — `shared/src/AI/AIEngine.php:154` → "Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)" → **niyet var, implementasyon yok**.

**PLANNED (kod kanıtı 0 — dürüst etiket):**

- **Süreç-arası iletişim (IPC) spesifikasyonu** — `.ai/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md:83,94` `IPCMessageHeader` · `:388` ```protobuf bloğu · `:556-606` `COREMUSIC_IPC_H` başlık dosyası; eşlikçi doküman `.ai/architecture/k0-isletim-sistemi/README.md:298-301` (`struct IPCMessage`) ve `windows-core.md:237-244` (`\\.\pipe\CoreMusicIPC`). Repo genelinde `*.proto` / `*.xc` / `*.cpp` / `*.cs` = **0 dosya** → hepsi spec.
- **İç servis gRPC/protobuf** — `.ai/architecture/k9-api-routing/grpc-internal.md:75-109` protobuf servis + `google.protobuf.Timestamp` tanımı; kod karşılığı 0 (yukarıdaki glob ile aynı sonuç).
- **Firmware mesajlaşma (web ↔ XMOS)** — `.ai/architecture/firmware/xmos-firmware.md:35` "Engine / Control / Comms / Mgmt" kanal yapısı + `:48-52` USB endpoint/GPIO dosya planı; `.xc` dosyası 0 → **PLANNED**. **Görselleştirme protokolü YOK:** `.ai/architecture/firmware/*.md` + ADR-017 üzerinde `görselleştirme|visualization protocol` grep → **0 eşleşme** (uydurulmadı, açıkça yok sayılıyor).
- **Outbox olay tablosu** — `.ai/.sql/mysql/*.sql` içinde `outbox` grep → **0 eşleşme**; ADR-081'in `outbox_events` şeması (`id`, `event_type`, `payload` — §2.2 a) henüz SQL'e yazılmamış ve **`version` sütunu içermiyor** → (e) ekseni bu ADR ile kapanır.
- **Golden fixture (şema sürümü kapı testi)** — `shared/tests/Fixtures/golden-routes.php` = **False** (dizin yok) → ADR-021'in Koruma 1'i gibi şema golden fixture'ı da **PLANNED**.

**Hizalama sorularının dürüst cevapları:**

- **ADR-021 (API contract gate + golden fixture):** desen **hizalı** — ADR-021 route sözleşmesini, bu ADR ise IPC/şema sözleşmesini aynı "tek gerçeklik + CI kapısı + insan onaylı güncelleme" usulüyle bağlar (ADR-021 §1.3 kaynak 1-24, `[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]`). ADR-021'in golden fixture'ı bugün diskte YOK (PLAN); bu ADR onu şema düzeyinde tekrar etmez, **kapı desenini devralır**.
- **ADR-017 (XMOS/firmware mesajlaşma):** ADR-017 üç katmanı (firmware / JUCE-DSP / ASIO-WASAPI host) sınırlar; **görselleştirme protokolü ADR-017'de de yok** (grep 0). Firmware ↔ host mesajlaşması ADR-017'de yalnız USB UAC2 + I²S/TDM veri yolu olarak var; **kontrol/sözleşme mesajı tanımlı değil** → bu ADR'nin (a) eksenine girer.
- **ADR-081 (outbox event versioning):** ADR-081 outbox'ın atomikliğini, teslimat semantiğini (at-least-once + `event_id` dedup) ve lag bütçelerini yazar; **zarf sürümü alanını yazmaz** (şema §2.2'de `version` yok). Bu ADR onu **tamamlar, çelişmez**: `payload` JSON kalır, zarf'a `schema_version` eklenir.

### 1.2 Sorun Tanımı

Web backend'i (PHP), XMOS firmware'i (ADR-017), masaüstü istemcisi (WASAPI/ASIO — ADR-017) ve servisler arasındaki mesajların **formatı, alan şeması, hata kodları ve sürümü hiçbir dosyada yazılmıyor**. Kodda sürümleme yalnız HTTP yüzeyinde (`/api/v1` + `X-API-Version`) duruyor; olay zarfında, BFF dönüşümlerinde ve firmware kontrol kanalında sürüm kavramı yok. Sonuç: (1) bir alan eklendiğinde/çıkarıldığında hangi istemcinin kırılacağı bilinmiyor, (2) outbox olayı (ADR-081) sürümsüz yayılırsa tüketici şeması sessizce sapabilir, (3) firmware'in web'den geç güncellenmesi durumunda hangi mesajın destekleneceği tanımsız. Bu ADR üç boşluğu da **kuralla** kapatır: sürüm tanımı, uyumluluk yasağı ve doğrulama kapısı.

### 1.3 İlgili web araştırması

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "API versioning best practices 2025 2026 URI vs header semantic versioning breaking change" · (2) "schema evolution rules backward compatible field addition removal protobuf breaking change 2025" · (3) "JSON Schema compatibility breaking change additive fields CI validation best practice" · (4) "event-driven architecture event versioning envelope version field schema idempotent consumer outbox 2025" · (5) "JSON vs protobuf performance overhead small messages IPC when to choose protobuf" |
| Web Search **Konusu** | IPC sözleşmesinin beş karar ekseninin güncel ekosistem kanıtı: sürümleme stratejisi (URI/header), şema evrimi kuralları (additive vs breaking), JSON-Schema uyumluluk denetimi + CI kapısı, olay zarfı sürümü/outbox uyumluluğu ve protobuf'un JSON'a göre performans/olgunluk dengesi. |
| Web Search **Bağlam** | 2025-2026 güncel verisi okundu: docs.io 2026 sürümleme rehberi + xmatters + capgo + odown (strateji seçimi ve "strateji değiştirmek de breaking'tir"), Confluent schema-registry uyumluluk belgeleri + Conduktor 8 pratik + ByteByteGo + protobuf alan-numarası kuralları (softwaremill/medium/oneuptime 2026), json-schema-org topluluk issue #984 (GSoC 2026 uyumluluk denetleyicisi) + jundago/medium CI bloklama, The Burning Monk (2025) event versioning + NILUS zarf kalıpları + dataexpert domain events, LinkedIn/witrpc/apipark protobuf-JSON performans karşılaştırmaları. Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✓) — resmi/anahtar kaynak önce, her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. |
| Web Search **Kısa Açıklama** | Sürümleme: herkese açık yüzeyde URI `/v1/`, iç yüzeyde header (bizde `VersionResolver` zaten ikisini de okuyor); şema evrimi: **ekleme uyumlu, silme/renaming/zorunluluk daraltması kırıcı**, yeni alanlara `default` şart; JSON-Schema uyumluluğu CI'da denetlenir ve breaking change kırmızıya düşer; olaylarda zarf-a `schema_version` ile additive evrim; protobuf küçük/ağır mesajlarda %40-60 daha küçük payload verir ama olgunluk/kolay doğrulama JSON'da kalır. |
| Web Search **Uzun Açıklama** | **(1) Sürümleme:** docs.io (2026) dört stratejiyi (URI/header/query/media-type) karşılaştırıp major bump'ın breaking değişimle geldiğini; xmatters deprecation yaşam döngüsünü; capgo kararı testlerle bağlamayı; odown "strateji değiştirmek başlı başına breaking'tir — bir strateji seç ve onda kal" der → mevcut `VersionResolver` (URI → `Accept-Version` → V1) zaten doğru ikili stratejiye sahip, karar onu IPC'ye genişletmektir. **(2) Şema evrimi:** Confluent backward/forward/full uyumluluk modlarını ve registry'de kayıt anında kural uygulanmasını; Conduktor "yeni alanlara default koy, required'ı asla silme, değişimden önce uyumluluk testi"; ByteByteGo "backward-uyumlu değişim, tüketicinin önce yükseltilmesini gerektirir"; protobuf'ta **alan numarası asla yeniden kullanılmaz** (silinen alan `reserved`), yeniden adlandırma güvenli çünkü önemli olan numaradır (medium/oneuptime 2026/softwaremill) → JSON tarafında karşılığı: **alan adı kırıcıdır**, bu yüzden silme/rename yasak, ekleme serbest. **(3) JSON-Schema + CI:** json-schema-org #984 "bu şema değişimi var olan veriyi kırar mı?" sorusunun standarda dahil olmadığını ve GSoC 2026'da denetleyici üretildiğini; jundago "CI breaking change'i bloklamalı — lint + registry uyumluluk + consumer contract her PR'da"; medium/stackoverflow/agileseekers aynı sonuca varır: opsiyonel alan ekleme risksiz, silme/rename/tip daraltması riskli → bizim kapı: golden şema fixture'ı + diff insan onayı. **(4) Olay zarfı:** The Burning Monk (2025-04) event versioning stratejilerini (zarf sürümü / ayrı topic-ürün / upcaster) karşılaştırıp additive değişim için zarf sürümünü önerir; NILUS zarf kalıplarında `Event Versioning → additive change` ile `Idempotent Consumer → message identity`yi birlikte şart koşar; dataexpert "domain olaylarını versiyonlu API sözleşmesi gibi yönet, outbox/CDC ile taşı"; conduktor outbox'ın olayı aynı transaksiyonda yazdığını → ADR-081 + bu ADR birlikte tamamlanır. **(5) protobuf vs JSON:** LinkedIn karşılaştırması %40-60 daha küçük mesaj ve ~3x hızlı serileştirme; witrpc taşıyıcı/serileştirici benchmark ızgarası; apipark yüksek performanslı IPC olarak gRPC'yi; researchgate çalışma protobuf/Avro'yu JSON'a göre verimli bulur → **protobuf'ın kazancı gerçek amaCoreMusic'te kod 0** (`.proto` = 0 dosya) → PLANNED kapıda tutulur. |
| Web Search **Paragraf Veri Uzun** | 5 paragraf, **30 adlandırılmış kaynak, 5 sorgu**: sürümleme (7), şema evrimi/protobuf (8), JSON-Schema+CI (5), olay zarfı/outbox (6), JSON-protobuf performans (4). Çapraz doğrulama ≥2 kaynak dört ana iddiada da karşılanır; firmware kontrol mesajı ve "görselleştirme protokolü" için web kanıtı aranmadı — konu vault içi kod kanıtına bağlı (§1.1). |
| Web Search **Sonucu** | (a) Sürümleme stratejisi **doğrulandı**: herkese açık URI + iç header, tek stratejiye sadık kal (7 kaynak); (b) uyumluluk kuralı **doğrulandı**: ekleme uyumlu / silme+rename+zorunluluk daraltması kırıcı / yeni alanda `default` şart (≥2 çapraz, 8 kaynak); (c) **CI kapısı zorunluluk** olarak doğrulandı: uyumluluk denetimi PR'da kırmızıya düşer, insan onaylı güncelleme (5 kaynak); (d) **zarf sürümü** outbox ile birlikte okunur şekilde doğrulandı (6 kaynak); (e) protobuf kazancı gerçek ama CoreMusic'te `.proto` = 0 → **PLANNED** (4 kaynak). |
| Web Search **Alınan Karar** | **(a) IPC sözleşmesi = mesaj formatı + alan şeması + hata kodları** (tek dosyada, JSON birincil); **(b) sürümleme = semver benzeri** (`MAJOR.MINOR.PATCH`) **+ ayrı şema sürümü** (`schema_version`, zarf alanındaki tamsayı/sürüm) — API sürümü (`/api/v1`) ile şema sürümü **birbirinden ayrı** yaşar; **(c) geriye uyumluluk kuralı: breaking change YASAK** — ekleme (yeni opsiyonel alan, yeni mesaj tipi, yeni hata kodu) serbest; silme / yeniden adlandırma / tip veya zorunluluk daraltması yalnız **yeni major + yeni ADR** ile (ADR-021 usulü); **(d) şema doğrulama = çalışma zamanı (mevcut `RequestValidationMiddleware` + JSON-Schema PLANNED) + CI (golden şema fixture, diff = kırmızı)**; **(e) event versioning = ADR-081 outbox zarfına `schema_version` alanı** (`IntegrationEventInterface`'e `version()` eklenir), tüketici eski sürümü de okuyabilir (additive upcaster). Format: **JSON birincil**, **protobuf opsiyonel PLANNED** (küçük/ağır mesaj; `.proto` dosyası 0). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: sürümleme (7) + şema evrimi (8) + JSON-Schema/CI (5) + olay zarfı/outbox (6) + JSON/protobuf (4) → **30 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak dört ana iddiada da sağlanır. Kod tarafı da aynı resmi verdi: sürümleme/doğrulama/zarf **kısmen IMPLEMENTED** (VersionResolver, X-API-Version, RequestValidationMiddleware, event arayüzleri), **şema-sürümü + outbox sürümü + JSON-Schema + protobuf/firmware IPC = PLANNED** (0 kod) → bu ADR **sözleşme ve kapı kararıdır**, kod taahhüdü değil; uygulama §5.1 adımlarına bağlıdır. |

**Kaynak listesi (30):** 1) docsio.co — API Versioning: The Complete 2026 Guide · 2) xmatters.com — API Versioning: Strategies & Best Practices · 3) medium.com/@haroldfinch — Best Practice For public APIs (URI /v1, iç: header) · 4) vickybytes.com — API Versioning Strategies · 5) digitalapi.ai — REST API Versioning Definition, Strategies & Best Practices (2026) · 6) capgo.app — API Versioning Strategy: A Complete Decision Guide · 7) odown.com — API Versioning: Preventing Breaking Changes in Production · 8) docs.confluent.io — Schema Evolution and Compatibility for Schema Registry · 9) conduktor.io — Schema Evolution: 8 Kafka Best Practices · 10) dataexpert.io — Backward Compatibility in Schema Evolution Guide · 11) blog.bytebytego.com — Schema Evolution: Changing the Contract Without Breaking It · 12) medium.com/@annxsa — Schema Evolution in Protobuf: Field Number You Must Never Reuse · 13) softwaremill.com — Good practices for schema evolution with Protobuf · 14) oneuptime.com (2026-01-24) — How to Handle Protocol Buffer Evolution · 15) jundago.com — Schema Versioning Strategy: A Practical Guide · 16) github.com/json-schema-org/community/issues/984 — GSoC 2026: JSON Schema Compatibility Checker · 17) medium.com/@tuananhbk1996 — Ways to Handle Schema Evolution Safely in Production Systems · 18) stackoverflow.com/questions/75094004 — Validate a new JSON schema is backward compatible · 19) agileseekers.com — Managing Schema Evolution in Data-Intensive Products · 20) estuary.dev — Schema Evolution in Real-Time Systems · 21) theburningmonk.com (2025-04) — Event versioning strategies for event-driven architectures · 22) dataexpert.io — Managing Domain Events in Event-Driven Architectures · 23) nilus.be — Message Envelope Patterns in Event-Driven Systems · 24) conduktor.io — Outbox Pattern for Reliable Event Publishing · 25) oneuptime.com (2026-01-30) — How to Implement Event Versioning Strategies · 26) medium.com/@iamprovidence — Event-Driven Architecture (zarf `schema version` alanı) · 27) linkedin.com (Satvik Kaurav) — REST vs gRPC: JSON vs Protobuf (%40-60 smaller) · 28) witrpc.io — Performance Tuning: Transports and Serializers · 29) apipark.com — A Deep Dive into gRPC & TRPC (yüksek performanslı IPC) · 30) researchgate.net — Performance Characterization of Communication Protocols in Microservice Applications *(hepsi 2026-09-25 erişimi; başlık/özet düzeyi derleme — sayfa-içi derin tur yapılmadı, açıkça işaretli)*.

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR 001-037 dokunulmaz | Bu ADR yeni dosyadır; frozen metinler okunur/referanslanır, düzenlenmez (AGENTS.md §25.3 kural 2) |
| JSON birincil, protobuf opsiyonel | Doğrulama kolaylığı + mevcut PHP yığını (`ext-json`, `respect/validation`) → protobuf yalnız `.proto` dosyası yazıldıktan ve ölçüldükten sonra PLANNED'den çıkar |
| Breaking change yasak (ADR-021 usulü) | Ekleme serbest; silme/değişim = yeni major + yeni ADR. Firmware tarafında istemciler eşzamanlı güncellenemediği için kural istisnasız uygulanır |
| Firmware güncelleme gecikmesi | XMOS firmware'i (ADR-017) web'den ayrı güncellenir → sözleşme, **eski sürümü okuyabilen** taraflar için yazılır (idempotent + additive upcaster) |
| REDACTED | Sözleşme dosyalarına/olay payload'ına secret, token, credential yazılmaz |
| In-Place Refactoring | Dosya adları (`VersionResolver.php`, `RequestValidationMiddleware.php`, `IntegrationEventInterface.php` vb.) onaysız değiştirilemez; bu ADR yalnız sözleşme ve kapı yazar |

---

## 2. Karar

**CoreMusic'te tüm IPC yüzeyleri (web backend PHP ↔ XMOS firmware ↔ masaüstü WASAPI/ASIO ↔ servisler arası) TEK bir sözleşme belgesine bağlanır ve sürüm altına alınır: (a) sözleşme = mesaj formatı + alan şeması + hata kodları; (b) sürüm = semver benzeri (`MAJOR.MINOR.PATCH`) ve ayrı bir `schema_version`; (c) geriye uyumluluk zorunlu — breaking change yasak, ekleme serbest; (d) doğrulama = çalışma zamanı + CI golden şema fixture; (e) ADR-081 outbox olaylarının zarfına `schema_version` alanı eklenir. Birincil format JSON'dur; protobuf opsiyonel (PLANNED) kapılıdır.**

### 2.1 Gerekçe 1 — Sürümleme zaten yarım kurulu, sözleşme onu tamamlar

Sürüm çözümleme üçlüsü (`VersionResolver.php:34-48`) ve sürüm zarfı (`ApiResponse.php:21,31`) **çalışıyor** — yani "sürüm kavramı yok" değil, "sürüm kavramı yalnız HTTP'de ve şemaya bağlı değil". Şema (`schema_versions` — `coremusic_patch.sql:14`) ile olay (`IntegrationEventInterface` — sürüm alanı 0) katmanları arasında bağ yok. Bu ADR, mevcut mekanizmayı **genişletir**, yeni bir sürüm sistemi icat etmez: URI `/v1` (herkese açık) + `Accept-Version` (iç) ikilisi korunur (§1.3 kaynak 1-5: tek stratejiye sadık kal).

### 2.2 Gerekçe 2 — Uyumluluk yasağı, iki ayrı ADR'nin boşluğunu kapatır

ADR-021 **yol** sözleşmesini (golden route listesi + CI kapısı) yazdı; ADR-081 **olay taşıma** semantiğini (outbox + at-least-once + `event_id` dedup) yazdı; ikisi de **alan şemasının kendisini** kapsamıyor. §1.3 kaynak 8-15 aynı hükmü veriyor: ekleme uyumlu, silme/renaming/zorunluluk daraltması kırıcı, protobuf'ta alan numarası `reserved` ile korunur, JSON'ta alan adı kırıcıdır → **bizim kural: alan adı dahil hiçbir mevcut alan silinmez/değiştirilmez**; değişiklik yalnız yeni `schema_version` + yeni ADR ile.

### 2.3 Teknik Detaylar

**(a) Sözleşme belgesi (yeni dosya, PLANNED):** `.ai/architecture/k9-api-routing/ipc-contract.md` — dört bölüm: (1) mesaj formatı (JSON zarf: `schema_version`, `message_id`, `type`, `occurred_on`, `payload`), (2) alan şeması tablosu (alan adı, tip, zorunluluk, varsayılan, sürüme eklendiği minor), (3) **hata kodu şeması** (`E_CONTRACT_INVALID`, `E_SCHEMA_VERSION_UNSUPPORTED`, `E_PAYLOAD_MALFORMED` + mevcut HTTP durum eşlemesi), (4) sürüm geçmişi tablosu. Farklı yüzeyler (`BffLayer` → spa/desktop/mobile/embedded) bu tek belgeden okur; ikinci kopya yazılmaz (§1.3 kaynak 15: sözleşmeyi okuyan tek "gerçek").

**(b) Sürümleme:**

| Katman | Sürüm alanı | Değer örneği | Durum |
|--------|-------------|---------------|-------|
| API URL sürümü | `/api/v1/*` (path) | `v1`, `v2` | IMPLEMENTED (`Gateway.php:82`) |
| API sürüm header'ı | `X-API-Version` + `Accept-Version` | `1.0.0` | IMPLEMENTED (`ApiResponse.php:31`) |
| Şema sürümü | zarf `schema_version` (int, monoton artan) | `1`, `2` | **PLANNED (bu ADR)** |
| Paket sürümü | `MAJOR.MINOR.PATCH` (sözleşme belgesi) | `1.2.0` | **PLANNED (bu ADR)** |
| Veritabanı şema sürümü | `schema_versions` | mevcut tablo | IMPLEMENTED (`coremusic_patch.sql:14`) |

Semver benzeri kural: **MAJOR** = breaking (yalnız yeni ADR ile), **MINOR** = additive (yeni opsiyonel alan/yeni mesaj tipi/yeni hata kodu), **PATCH** = şema dışı düzeltme (açıklama, hata metni). Şema sürümü API sürümünden **bağımsız** ilerler: `/api/v1` altında `schema_version` 1 → 2 olabilir (additive), `/api/v2` yalnız breaking'de açılır.

**(c) Geriye uyumluluk kuralı:**

| Değişiklik | Sınıf | Karar |
|-----------|-------|-------|
| Yeni opsiyonel alan ekleme (varsayılanlı) | ADDITIVE | Serbest — MINOR + golden fixture güncellemesi |
| Yeni mesaj tipi / yeni hata kodu ekleme | ADDITIVE | Serbest — MINOR |
| Alan silme | BREAKING | **Yasak** — yeni major + yeni ADR (silinen ad `reserved`) |
| Alan yeniden adlandırma / tip değiştirme | BREAKING | **Yasak** — yeni major + yeni ADR |
| Zorunluluk daraltması (opsiyonel → required) | BREAKING | **Yasak** |
| Zorunluluk gevşetmesi (required → opsiyonel) | ADDITIVE | Serbest (tüketiciler yeni alanı `default` ile okur) |

İhlal usulü ADR-021 ile aynıdır: sözleşme ihlali = bu ADR'nin amendmanı = **yeni ADR** (metin silinmez).

**(d) Şema doğrulama — iki kapı:**

- **Çalışma zamanı (IMPLEMENTED, genişletilecek):** `RequestValidationMiddleware` route kurallarıyla doğrular; bu ADR şema tablosunu kurallara **eşler**. Ek PLANNED: JSON-Schema dosyaları (`*.schema.json`, `opis/json-schema`) — repo'da bugün 0 (§1.1).
- **CI (PLANNED):** `shared/tests/Fixtures/golden-schema.php` (veya `*.schema.json` golden set) — mevcut şema ile golden farkı = test kırmızı; güncelleme **insan onaylı** (`-u` benzeri bilinçli geçiş), CI'da otomatik overwrite yok (§1.3 kaynak 15-19). ADR-021'in golden route kapısı ile **aynı CI job'unda** çalışır.

**(e) Olay zarfı sürümü (ADR-081 tamamlayıcısı):**

- `IntegrationEventInterface`'e `public function version(): int;` eklenir (mevcut 4 metod korunur — imza değişimi **additive değildir**, bu yüzden minor sürümde **yeni arayüz** `VersionedIntegrationEventInterface` olarak eklenir ve yeni olaylar onu uygular; eski olaylar `version(): 1` default'u ile okunur → geriye uyumluluk bozulmaz).
- Outbox şemasına `schema_version INT NOT NULL DEFAULT 1` sütunu eklenir (ADR-081 §2.2 a'ya **ekleme**, silme yok).
- Tüketiciler (ADR-081 idempotent + `event_id` dedup) `schema_version` ile **upcaster** yapar: eski sürümü okur, yeniye ascended eder; bilinmeyen/yüksek sürüm → `E_SCHEMA_VERSION_UNSUPPORTED` + DLQ (ADR-081 retry/DLQ zinciri).

**(f) Format kararı:** JSON birincil (okunabilir, `ext-json` zorunlu, doğrulama kütüphaneleri olgun). Protobuf **PLANNED** — tetikleyici: tek bir iç uçta payload > 64 KB/saniyede tekrarlanan veya ölçülmüş serialization bütçesi aşımı (§1.3 kaynak 27-30: %40-60 boyut kazancı gerçek) → o zaman `.proto` yazılır, alan numarası `reserved` kuralı uygulanır ve şema yine golden fixture'a bağlanır.

**Fallback (geri çekilme yolu):**

1. **Version negotiation** — zaten var (`VersionResolver.php:34-48`): URI okunamazsa `Accept-Version`, o da yoksa **V1 default** → yeni sürüm okunamayan istemci asla 404/500 almaz, V1 muamelesi görür.
2. **Feature flag** — yeni alanlar rollout'ta flag arkasında açılır; flag kapalıysa alan hiç üretilmez (eski tüketici riski sıfır).
3. **Add-only yayını** — outbox'ta `schema_version` bilinmeyen tüketiciye **eski sürüm kopyası** yayınlayabilir (relay tarafında downgrade yok, sadece okunabilirlik).

---

## 3. Alternatifler

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Hiç sürüm yok, her değişimi herkes aynı anda günceller** | İlk gün en hızlı | Firmware (ADR-017) web'den ayrı güncellenir; at-least-once outbox'ta (ADR-081) eski olay yaşayan tüketici çöker | §1.3 kaynak 4, 21-25: sürüm olmadan additive evrim mümkün değil; sahada sessiz sapma üretir |
| 2 | **Yalnız URL major versioning (`/api/v1`) — IPC'ye dokunmadan bırakmak** | Sıfır çaba, mevcut `Gateway.php:82` zaten var | Olay zarfı, BFF dönüşümü ve firmware mesajı URL'de yaşamıyor; `X-API-Version` şemaya bağlı değil → sözleşme kapsamı yarım kalır | Bu ADR'nin tam sorunu: sürüm kavramı HTTP'de kalmış. §1.3 kaynak 2-3 tek başına URL sürümü şema uyumluluğunu sağlamaz |
| 3 | **Protobuf birincil, JSON opsiyonel** | %40-60 boyut, ~3x serileştirme (§1.3 kaynak 27-30) | Repo'da `.proto` = **0 dosya**; PHP/XMOS/JS tarafında kod üretimi + öğrenme eğrisi; insan-okunur debug ve `respect/validation` ekosistemi devre dışı | Doğrulama kolaylığı ve mevcut yığın (§1.4 kısıt) kazanıyor; protobuf kapısı açık bırakıldı (§2.3 f tetikleyicisi) |
| 4 | **Tam consumer-driven contract (Pact CDC) kurmak** | Ayrı dağıtılan istemciler için güçlü | ADR-021'in aynı-deploy çıkarımı: sunucu + istemci tek repoda → CDC ağır, golden şema yeterli (⚠️ çıkarım işaretli, §1.3 ADR-021 §1.3 kaynak 5-8) | Ağırlık/ekip maliyeti; golden fixture + CI kapısı aynı korumayı sıfır altyapıyla verir |
| 5 | **Kurumsal schema registry (Confluent vb.)** | Kayıt anında uyumluluk kuralı, merkezi sürüm defteri | ADR-081'in ölçek hükmü ile aynı: CoreMusic ölçekte broker+registry işletimi ağır (ADR-081 §3 #5 ruhu) | Golden fixture + `schema_versions` tablosu (mevcut) aynı denetimi repo-içi verir; altyapı öncelik değil |

---

## 4. Sonuç ve Sonuçlar

**Olumlu:**

- Sürümleme üçlüsü (URL + header + negotiation) zaten kodda → bu ADR **ek yapı değil, mevcut mekanizmayı sözleşmeyle bağlar**; ilk gün maliyeti düşüktür (§2.1).
- Kural seti (ekleme serbest / silme yasak) hem firmware gecikmesini hem outbox eski olaylarını tek prensiple çözer: **hiçbir tüketici bilmediği sürümle karşılaşmaz** (§1.3 kaynak 21-25).
- Tek belge + golden fixture, ADR-021'in CI kapısı desenini aynı job'da çoğaltır → sözleşme sapması kod review'da değil, **testte** yakalanır.
- Olay zarfına `schema_version`, ADR-081'in idempotent tüketici sözleşmesini güçlendirir (DLQ sebebi görünür hâle gelir).

**Olumsuz:**

- Ek arayüz (`VersionedIntegrationEventInterface`) + outbox sütunu = veri/görev kalemi; ADR-081 uygulamasıyla **eşzamanlı** değilse iki kez iş çıkar.
- Sözleşme belgesi elle güncellenir → `*-schema.json` yokluğunda tutulmazsa **kendi sapma riskimizi** yaratır (mitigasyon: golden fixture, kayıt zorunlu).
- Protobuf'ın performans kazancı ertelenir → iç uçlarda büyük mesajlarda bandwidth/latency maliyeti geçici olarak kalır.

**Nötr:**

- Sürüm patlaması (çok sayıda `schema_version` yan yana) teknik olarak engellenmez; yönetişimle (major yalnız yeni ADR) sınırlanır.
- Hata kodu şeması ilk kez tek yerde toplanır → eski dağınık HTTP durum kodları geçiş döneminde birlikte yaşar.

### 4.1 Risk → Fallback matrisi

| # | Risk | Olasılık | Etki | Mitigasyon | Fallback (geri çekilme yolu) |
|---|------|---------|------|------------|------------------------------|
| R1 | **Şema sapması** — sözleşme belgesi ile kod/olay payload'ı sessizce farklılaşır | Olası (3) | Yüksek (4) | Golden şema fixture + CI'da diff = kırmızı; güncellemeler insan onaylı (§1.3 kaynak 15-19) | Sapma tespitinde üretim kısa devre: doğrulama **log + reject** yerine **log + geç** (warn) moduna alınır, fixture düzeltilip kapı geri açılır |
| R2 | **Firmware güncelleme gecikmesi** — XMOS (ADR-017) web'den aylarca geride kalır | Olası (3) | Yüksek (4) | Kural: firmware **her zaman eski `schema_version`'ı okuyabilir** (additive only); firmware'e giden mesajlarda yeni alan **flag arkasında** | Firmware yalnız V1 zarfı okuyorsa web tarafı `EmbeddedBff` transformer'ında eski zarfı üretir (downgrade okunabilirliği, alan silme yok) |
| R3 | **Versiyon patlaması** — her olay ayrı `schema_version`, sürüm defteri şişer | Mümkün (2) | Orta (3) | MINOR yalnız additive; MAJOR yalnız yeni ADR; şema sürümü API sürümünden ayrı ama **yılda ≤1 major** bütçesi | Eski major'lar okuma modunda tutulur (yazım durdurulur), yazım tek sürümde birleşir |
| R4 | JSON-Schema katmanı hiç kurulmaz → kapı yalnız route kurallarıyla çalışır | Olası (3) | Orta (3) | §5.1 adım 3 (JSON-Schema PLANNED) sahipliği Backend + QA; kapı 1 (golden fixture) bağımsız çalışır | `RequestValidationMiddleware` kuralları tek doğrulama olarak kalır; şema dosyaları PLANNED etiketiyle raporlanır (IMPLEMENTED'a uydurulmaz) |
| R5 | Protobuf'a erken geçiş (ölçmeden) | Nadir (1) | Orta (3) | §2.3 f tetikleyicisi: yalnız ölçümle (payload > 64 KB tekrarlayan ya da bütçe aşımı) | `.proto` yazılmaz; JSON kalır — sözleşme belgesi format-agnostik yazıldığı için geçiş yalnız serileştirici katmanı etkiler |

---

## 5. İlgili Kararlar

### 5.1 Wiki-linkler (diskte doğrulandı — hepsi VAR ✅)

- `[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]` → **contract gate + golden fixture usulü**; bu ADR breaking/additive taksonomisini ve CI kapısını bu ADR'den devralır (§2.2, §3-4).
- `[[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]]` → web ↔ firmware ↔ masaüstü sınırı; firmware kontrol mesajının sözleşmesiz kalmasının kaynağı (§1.1, §1.4).
- `[[.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md]]` → outbox şeması + idempotent tüketici; (e) ekseninin taşıyıcısı — `schema_version` bu şemaya eklenir (§1.1, §2.3 e).
- `[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]` → public API yüzeyi + Gateway `/api/v1/*`; URL sürümleme bu kararın üstünde yaşar (§2.1).
- `[[.ai/.decisions/accepted/ADR-004-multi-domain-spa.md]]` → çift router + BFF/istemci yüzeyleri; sözleşme tek belgeden okunur (§2.3 a).
- `[[.ai/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md]]` → IPC mesaj başlığı + protobuf spec (PLANNED, kod 0) — (a) ekseninin vault kaynağı (§1.1).
- `[[.ai/architecture/k9-api-routing/grpc-internal.md]]` → iç servis protobuf spec (PLANNED) — (f) format kapısı (§1.1, §2.3 f).
- `[[.ai/architecture/k7-middleware/request-validation.md]]` → JSON-Schema doğrulama önerisi (`justinrainbow/json-schema`, composer'da 0) — (d) ekseninin PLANNED kaynağı (§1.1).
- `[[.ai/architecture/firmware/xmos-firmware.md]]` → Control/Comms kanal planı (spec, `.xc` = 0) — firmware mesajlaşmasının spesifikasyonu (§1.1).
- `[[.ai/.sql/mysql/coremusic_patch.sql]]` → `schema_versions` tablosu — veri katmanı sürüm takibi IMPLEMENTED (§1.1).
- `[[.ai/.templates/adr/adr-template.md]]` → 7 bölüm + §1.3 9 alan iskeleti (Guardrail #16).
- `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` → §1.3 web araştırma protokolü (diskte VAR ✓).
- `[[.ai/.decisions/index.md]]` → karar dizini (kayıt satırı ayrı işlemdir).
- Düz metin (wiki-link **kurulmuyor**): `ADR-086-event-driven-architecture` — kod `@see`'lerinde geçiyor, `.ai/.decisions/accepted/` altında dosya **YOK** → `⚠️ VERIFICATION REQUIRED`.

### 5.2 Karar parçaları (appendix)

| # | Parçanın adı | Sahibi | Sonu |
|---|--------------|--------|------|
| 1 | Sözleşme belgesi `ipc-contract.md` (mesaj + şema + hata kodları + sürüm geçmişi) | Backend Developer | §2.3 a — tek gerçeklik, BFF/firmware buradan okur |
| 2 | Zarf alanları + `VersionedIntegrationEventInterface` + outbox `schema_version` sütunu | Backend + Data Engineer | §2.3 b/e — ADR-081 şemasına additive ekleme |
| 3 | Golden şema fixture + CI kapısı (ADR-021 job'una bitişik) | QA Engineer | §2.3 d — sapma testte yakalanır |
| 4 | JSON-Schema dosyaları (`*.schema.json`) + çalışma zamanı doğrulayıcı | Backend Developer | §2.3 d — PLANNED → IMPLEMENTED geçişi |
| 5 | Firmware kontrol mesajı sözleşmesi (USB/Comms kanalı) + eski-sürüm okuma garantisi | DSP Firmware + Embedded | §2.3 c/f — ADR-017 katman 1 hizası |
| 6 | Protobuf tetikleyici ölçümü (payload > 64 KB tekrarlayan / bütçe aşımı) | Backend + DevOps | §2.3 f — ölçüm yoksa geçiş yok |
| 7 | Debate şartı 1a — olay zarfı `version()` alanı + outbox `schema_version` sütunu | Backend Developer + Data Engineer | §6.2 — §2.3 e (ADR-081 tamamlayıcısı) |
| 8 | Debate şartı 1b — golden şema fixture yolu `shared/tests/Fixtures/` | QA Engineer | §6.2 — §2.3 d CI kapısı |
| 9 | Debate şartı 2 — firmware XMOS↔host IPC sözleşme maddesi (ADR-017 hizası) | DSP Firmware + Embedded | §6.2 — §2.3 a/c — spec var, kod 0 |
| 10 | Debate şartı 3 — ADR-086 slug referans teyidi | Vault Steward | §6.2 — §5.1 düz metin referansı |

### 5.3 Çapraz referans matrisi (kaynak → bölüm → durum)

| Kaynak | Kullanıldığı bölüm | İlişki | Disk kanıtı |
|--------|--------------------|--------|-------------|
| `[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]` | §1.1, §2.2, §3-4 | Contract gate + golden fixture usulü, breaking/additive taksonomisi | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]]` | §1.1, §1.2, §1.4 | Firmware/host/masaüstü sınırı; görselleştirme protokolü YOK (grep 0) | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md]]` | §1.1, §2.3 e, §4.1-R1 | Outbox şeması + idempotent tüketici; zarf sürümü eklenen taraf | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]` | §1.1, §2.1 | `/api/v1` Gateway + sürümleme zeminin sahibi | ✅ VAR |
| `[[.ai/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md]]` | §1.1 (PLANNED) | `IPCMessageHeader` + protobuf spec; kod 0 | ✅ VAR (spec) |
| `[[.ai/architecture/k9-api-routing/grpc-internal.md]]` | §1.1, §2.3 f | İç protobuf/gRPC spec; `.proto` = 0 | ✅ VAR (spec) |
| `[[.ai/architecture/k7-middleware/request-validation.md]]` | §1.1, §2.3 d | JSON-Schema kütüphanesi önerisi; composer 0 | ✅ VAR (spec) |
| `[[.ai/architecture/firmware/xmos-firmware.md]]` | §1.1, §5.2/5 | Control/Comms kanal planı; `.xc` = 0 | ✅ VAR (spec) |
| `[[.ai/.sql/mysql/coremusic_patch.sql]]` | §1.1, §2.3 b | `schema_versions` — DB sürüm takibi IMPLEMENTED | ✅ VAR |
| Kod (vault dışı) `shared/src/Api/Versioning/*`, `ApiResponse.php`, `RequestValidationMiddleware.php`, `Contracts/Events/*` | §1.1, §2.1, §2.3 | IMPLEMENTED kanıtı — dosya:satır referanslı | ✅ VAR (repo) |
| `[[.ai/.templates/adr/adr-template.md]]` | §6, §7 | 7 bölüm + §1.3 9 alan (Guardrail #16) | ✅ VAR |

---

## 6. Statü ve Debate

- **Status:** `accepted` — sözleşme çerçevesi (a-e beş eksen + format kararı + fallback) kullanıcı onaylı kapsam olarak kabul edildi; uygulama kalemleri §5.2 parçalarına bağlıdır.
- **Debate:** `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — 2026-09-25; debate kaydı §6.1'de, bağlayıcı 3 şart §6.2'de (sahiplik §5.2 parçaları 7-10), Tech Lead onayı §7'de.
- **Frozen ADR'lar (001-037):** bu dosya frozen ADR kapsama alanında değildir; dondurma kuralı yalnız mevcut frozen ADR'ların değiştirilemezliğini korur. ADR-032 bu yazımla yeni dosya olarak eklenmiştir.

### 6.1 Debate kaydı (3 tur / 20 persona — 2026-09-25)

- **Tur 1 — Kanıt sunumu (20 persona · 30 kaynak):** kod kanıtı **IMPLEMENTED** — sürüm müzakere (`shared/src/Api/Versioning/VersionResolver.php:34-48`), zarf header/body (`shared/src/Api/ApiResponse.php:21,31,37` → `X-API-Version`), URL-segment (`shared/src/Api/Gateway.php:82-87` → `/api/v1`), çalışma zamanı doğrulama (`shared/src/Api/Middleware/RequestValidationMiddleware.php:28-38`), `schema_versions` (`.ai/.sql/mysql/coremusic_patch.sql:14`); olay zarfı `shared/src/Contracts/Events/IntegrationEventInterface.php:24-46` var ama `version()` alanı **0**; **PLANNED (kod 0)** — `*.proto` / gRPC · outbox `schema_version` sütunu · JSON-Schema (`*.schema.json`) · `shared/tests/Fixtures/` dizini. Oy dağılımı: **15 kabul/neutral · 4 uyarı** — Embedded (firmware IPC şartı), QA (Fixtures yolu), Critic (event version boşluğu + ADR-086 yok şartı).
- **Tur 2 — İtiraz → çözüm (4 madde):**
  1. *İtiraz:* event zarfında `version()` 0 → *Çözüm:* `IntegrationEventInterface`'e `version()` + outbox `schema_version` sütunu → **şart 1a** (sahiplik §5.2 parça 7).
  2. *İtiraz:* `shared/tests/Fixtures/` dizini yok → *Çözüm:* golden şema yolu kurulur → **şart 1b** (sahiplik §5.2 parça 8).
  3. *İtiraz:* firmware IPC kod 0 (spec var) → *Çözüm:* XMOS↔host sözleşme maddesi ADR-017 hizasında yazılır → **şart 2** (sahiplik §5.2 parça 9).
  4. *İtiraz:* ADR-086 diskte yok → *Çözüm:* düz metin + slug teyidi (wiki-link kurulmaz) → **şart 3** (sahiplik §5.2 parça 10).
- **Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL.**

### 6.2 Bağlayıcı şartlar (3)

| # | Şart | Kapsam ve doğrulama | Sahibi | Durum |
|---|------|---------------------|--------|-------|
| 1 | Olay sürümü + golden şema yolu | **1a:** `IntegrationEventInterface`'e `version(): int` (§2.3 e — `VersionedIntegrationEventInterface`) + outbox `schema_version INT NOT NULL DEFAULT 1` sütunu · **1b:** `shared/tests/Fixtures/` altında golden şema dosyası (§2.3 d — CI kapısı, diff = kırmızı) | Backend Developer + Data Engineer (1a) · QA Engineer (1b) | 1a ⏳ · 1b ⏳ |
| 2 | Firmware IPC sözleşmesi | XMOS↔host kontrol/sözleşme mesajı `ipc-contract.md`'ye (§2.3 a) yazılır ve ADR-017 katman 1 hizasında kilitlenir; firmware her zaman eski `schema_version`'ı okur (§4.1-R2) | DSP Firmware Engineer + Embedded Engineer | ⏳ |
| 3 | ADR-086 referans teyidi | Düz metin `ADR-086-event-driven-architecture` slug'u diskte teyit edilir; dosya varsa wiki-link'e çevrilir, yoksa `⚠️ VERIFICATION REQUIRED` korunur (§5.1 son madde) | Vault Steward | ⏳ |

**Statü özeti:** debate `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` → Tech Lead §7'de `⏳ → ✅` (2026-09-25) → Arch Lead `⏳ PENDING` → frozen **YOK** (§7 3. satır tamamlanmadan `frozen` yapılmaz).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — (a) sözleşme · (b) semver benzeri sürüm + ayrı şema sürümü · (c) breaking yasak/additive serbest · (d) çalışma zamanı + CI doğrulama · (e) outbox zarf sürümü) |
| Tech Lead | — | 2026-09-25 | ✅ **(debate sonrası `⏳ → ✅`)** — 3 tur / 20 persona, 18/2/0 KABUL; 3 şart bağlayıcı (§6.2) |
| Arch Lead | — | — | ⏳ PENDING |

---

**1.0.0 | 2026-09-25 | Created**
*ADR-032 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) → Tech Lead ✅ → 3 şart (§6.2) → frozen YOK (Arch Lead ⏳)*

*Authority: ADR-032 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
