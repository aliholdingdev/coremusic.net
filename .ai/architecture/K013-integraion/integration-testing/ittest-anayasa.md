---
node_id: K013-integraion/integration-testing/ittest-anayasa
node_type: technology-deepdive
title: "Entegrasyon Test Anayasası — Unit ≠ Integration ≠ Contract, Test Piramidi, Live vs Mock, K019 Sınırı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/integration-testing
  - K013-integraion
  - K013-integraion/api-contracts
evidence:
  - "https://martinfowler.com/bliki/IntegrationTest.html (2026-10-16)"
  - "https://martinfowler.com/articles/practical-test-pyramid.html (2026-10-16)"
  - "https://docs.pact.io/ (2026-10-16)"
  - "api.coremusic.net/tests/Unit/RouteConfigTest.php — 208 satır (2026-10-16 disk ölçümü)"
  - "auth.coremusic.net/phpunit.xml — Satır 10-12 Integration suite kaldırma notu (2026-10-16)"
  - ".ai/architecture/K019-testing/integration/integration-test-ortami.md (2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 fetch)"
  - "Dar/geniş entegrasyon testi tanımı Fowler IntegrationTest bliki metniyle birebir karşılaştırılmıştır"
  - "Uydurma test/dosya adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN"
open_questions:
  - "CoreMusic dar entegrasyon testleri ne zaman yazılacak → ⚠️ VERIFICATION REQUIRED (tarih yok)"
  - "Sahte sunucu kimin sahipliğinde (QA mı DevOps mı) → ⚠️ VERIFICATION REQUIRED"
  - "Canlı (live) test onay kapısı kimin → UNKNOWN (ADR bekliyor)"
group: mimari
---

# Entegrasyon Test Anayasası — Unit ≠ Integration ≠ Contract · Piramit · Live vs Mock

> **Bu dosya ne değildir:** Test kodu, çalışma kılavuzu veya araç kurulumu değildir.
> Bu dosya entegrasyon testini *tanım ve sınır kanıtıyla* tanımlar; terimler
> `martinfowler.com` ve `docs.pact.io` metinlerinden alınmıştır, tahmin değildir.
>
> **Durum etiketi:** TANIM/Sınır IMPLEMENTED (belge) · Kod ⚠️ PLANNED
> (CoreMusic repo'sunda `tests/Integration/` dizini 2026-10-16 disk ölçümünde YOK).

---

## §0 ELI15 — Entegrasyon testi bir cümlede

Unit test tek parçayı kendi başına sınar, contract test iki parça arasındaki
anlaşmayı (sözleşmeyi) sınar, entegrasyon test ise parçaların **birbirine
bağlandığı sınırda** gerçekten konuşabildiğini sınar — ya gerçek bağımlılıkla
(canlı) ya da onun sadık taklidiyle (mock/stub).
(Kaynak: §Kaynak Tablosu [S1], [S2], [S3])

---

## §1 Üç Test Türü — ELI15 Ayrımı (bu dosyanın çekirdeği)

| Tür | Ne sınar | Sınır nerede | Hız | Kanıt |
|---|---|---|---|---|
| **Unit test** | Tek bir birim (fonksiyon/sınıf) kendi başına | Birim içi — bağımlılıklar taklitle değiştirilir | Çok hızlı | [S2] |
| **Integration test** | Ayrı geliştirilmiş birimler **birbirine bağlanınca** doğru çalışıyor mu | Birim dışı sınır: DB, dosya sistemi, HTTP, kuyruk | Yavaş (dış bağımlılık çalışır) | [S1], [S2] |
| **Contract test** | Mesaj/yanıt biçimlerinin **paylaşılan bir sözleşmeye** uyduğu | İki taraf arasındaki anlaşma (consumer ↔ provider) | Hızlı (çoğunlukla taklidin yanında) | [S3] |

### §1.1 Üç soruluk hızlı ayırt etme

| # | Soru | Evet ise |
|---|---|---|
| 1 | Test tek bir birim içinde, bağımlılıklar sahte mi? | **Unit** |
| 2 | Test gerçek bir dış parçayla (DB/HTTP/dosya) konuşuyor mu? | **Integration** |
| 3 | Test "bu yanıt/bu istek biçimini iki taraf aynı mı anlıyor" mu diyor? | **Contract** |

> **Sık hata:** Contract test'i integration test'in alt kümesi sanmak. Pact
> dokümanı contract test'i "integration point'i **her uygulamayı izole
> test ederek** doğrulama" tekniği olarak tanımlar — yani izole çalışır,
> canlı birleştirme **gerektirmez**. (Kaynak: [S3])

### §1.2 Ayrım Matrisi — Kapsam · Hız · Ortam · Ne Basitçe Kanıtlar

| Boyut | Unit | Integration | Contract |
|---|---|---|---|
| Kapsam | Tek birim | Birim + dış sınır | İki taraf arası anlaşma |
| Dış bağımlılık çalışır mı? | Hayır (sahte) | Evet (DB/HTTP/dosya canlı veya yerel) | Genelde hayır (taklit + periyodik gerçek) |
| Hız | Saniyeler içinde binlerce | Saniyeler–dakikalar | Hızlı; gerçek doğrulama ayrı ritimde |
| Ne kanıtlar | "Bu birim doğru hesaplıyor" | "Sınır gerçekten konuşuyor" | "İki taraf aynı şeyi anlıyor" |
| Sık kırılma nedeni | Refactor'a aşırı bağlı test | Ortam/altyapı | Dış servis değişimi |
| CoreMusic'te durumu | ✅ Mevcut (Unit suite'leri) | ⚠️ Dizin YOK (bkz. §6) | ⚠️ PLANNED (OpenAPI/CDC kararı yok) |

> **Kaçınılan uydurma:** Yukarıdaki "hız" ve "kırılma nedeni" sütunları
> kaynakların nitel ifadesidir; sayısal süre/süreç hedefi **uydurulmamıştır**
> (süreç hedefi varsa → UNKNOWN, §11 Q5).

---

## §2 Kavram Karmaşası — "Entegrasyon Test" Neden belirsiz?

Fowler, "integration test" teriminin sektörde bulanıklaştığını, iki farklı
şeyin birbirine karıştırıldığını söyler:

| Anlam | Kapsam | Ne gerektirir | Kanıt |
|---|---|---|---|
| **Dar (narrow) entegrasyon testi** | Yalnızca dış servisle konuşan kod parçası | Dış servisin **taklidi** (in-process veya over-the-wire double) — çoğu zaman unit test framework'üyle aynı hızda | [S1] |
| **Geniş (broad) entegrasyon testi** | Tüm servislerin canlı sürümleri | Test ortamı + ağ erişimi | [S1] |

- Dar test: "dar kapsam; çoğu zaman bir unit testten büyük değil, aynı
  framework'le koşar" (Fowler). [S1]
- Geniş test: "tüm servislerin canlı sürümü, kapsamlı test ortamı ve ağ
  erişimi gerektirir" (Fowler). [S1]

> **Terim uyarısı (kanıtlı):** Fowler geniş entegrasyon testleri için
> "system test"/"end-to-end test" adını tercih ettiğini, dar olan için
> "narrow" sıfatını kullandığını yazar; okuyucunun bağlam araması gerektiğini
> söyler. (Kaynak: [S1])

### §2.1 Üçüncü bir kullanım — "integration = sociable unit test"

Bazı yazarlar "unit test"i yalnızca **yalnız (solitary)** unit test olarak
tanımlar (her bağımlılık sahte); bu tanım altında "integration test"
ifadesi **sosyal unit test** anlamına gelir. Fowler bunu ayrı bir kavram
karmaşası olarak kaydeder ve "hangi kind olduğu bağlama bakmadan anlaşılmaz"
der. (Kaynak: [S1])

---

## §3 Test Piramidi — Katmanlar ve Oran Mantığı

Piramit, Mike Cohn'un *Succeeding with Agile* kitabından gelir; testleri
farklı granularity'de gruplamanın görsel metaforudur (Vocke/Fowler
makalesinde kaynak gösterilir). [S2]

| Katman | Ne | Oran mantığı | Kanıt |
|---|---|---|---|
| Alt (baz) | **Unit testler** — küçük, hızlı, bol | En çok sayıda | [S2] |
| Orta | **Service/Integration testler** — dar entegrasyon, kontrat | Birimden az | [S2] |
| Üst | **UI/End-to-end testler** — uçtan uca | **En az** sayıda | [S2] |

**İki temel kural (kaynaklı):** (1) farklı granularity'de test yaz;
(2) yükseldikçe test sayısı azalsın. Tersi "buzdağı/koniler" (ice-cream
cone) olarak adlandırılır ve bakımı pahalıdır. [S2]

> **Doğruluk notu:** Orijinal piramit katman adları (Unit / Service / UI)
> kaynak makalede "yanıltıcı olabilir, ekip içinde tutarlı bir ad
> seçilebilir" diye nitelendirilir — katman *adı* değil *oranı* esastır.
> (Kaynak: [S2])

---

## §4 Live (Canlı) mı, Mock (Sahte) mi? — Kaynaklı Karar Tablosu

| Senaryo | Kaynağın önerisi | Kanıt |
|---|---|---|
| Dar entegrasyon testi | Bağımlılığı **yerel çalıştır** (yerel MySQL) veya **sahte sürümlü** (fake/stub) | [S2] |
| 3. parti servis, yerel çalıştırılamıyor | **Özel test instance'ı** kullan | [S2] |
| Üretim sistemi | **Üretime otomatik testle vurmak yasak** — logları kirletir, en kötüde DoS sayılır | [S2] |
| Dış servisle sözleşme doğrulama | Testleri **test instance'ına** karşı çalıştır; canlıya "kaçınılmazsa" sahiplerle konuş, ekstra dikkatli ol | [S4] |
| Contract test ritmi | Dış servisin değişme ritmine göre — **günde bir kez** çoğu zaman yeterli; normal deploy pipeline'ına dahil etmek zorunlu değil | [S4] |
| Contract test başarısızlığı | Normal test gibi build'i kırpmayabilir; **tutarlılığı onaran bir görev** açar (taklit güncellenir, sahiplerle konuşulur) | [S4] |

### §4.1 Sahtenin güvenilirliği — "double gerçekten sadık mı?"

Sahte ile test etmenin tek sorusu: taklit gerçek servisi doğru
temsil ediyor mu? Bunun çözümü **contract test'leri**dir: tüm çağrıların
taklitten döndürdüğü ile gerçek servisten döndürünün aynı olmasını sınar;
hata varsa taklit (ve muhtemelen kod) güncellenir. (Kaynak: [S4])

> **CoreMusic uygulanabilirlik iddiası (kanıt değil, tasarım):** Tekrarlanan
> canlı doğrulama yerine "dar entegrasyon + contract" kombinasyonu, gerçek
> servise hiç çıkmadan dış entegrasyonu güvenle test etmeyi mümkün kılar
> (Fowler, IntegrationTest). Gerçek servisle uçtan uca test kalan takdirde
> **yalnızca sınırlı yollarda son bir smoke test** olarak nitelendirilir. [S1]

### §4.2 Karar Ağacı — Bu Test Ne Tür?

```text
Test başlarken sor:
├─ Dış bağımlılık (DB/HTTP/dosya/kuyruk) çalışıyor mu?
│   ├─ HAYIR → UNIT TEST (bağımlılık sahte)
│   └─ EVET ↓
├─ Amaç "parçalar konuşuyor mu" (tek sınır)?
│   ├─ EVET → DAR ENTEGRASYON TESTİ (taklit veya yerel servis)
│   └─ ↓
├─ Amaç "iki taraf aynı şeyi mi anlıyor" (anlaşma)?
│   ├─ EVET → CONTRACT TEST (periyodik gerçek doğrulamayla)
│   └─ ↓
└─ Tüm servisler canlı, uçtan uca akış?
    └─ EVET → UÇTAN UCA / SİSTEM TESTİ (en az sayıda; smoke test)
```

> **Ağaç kanıtı:** Dallar `IntegrationTest` bliki (dar/geniş ayrımı, smoke
> test) [S1] + Practical Test Pyramid (katmanlar ve oran) [S2] + Pact
> (contract test tanımı) [S3] metinlerinden kurulmuştur; uydurma dal yoktur.

---

## §5 K019 Sınırı — Bu Klasörün Sahip Olmadığı ⚠️

| Konu | Sahip | Bu klasörün durumu | Kanıt |
|---|---|---|---|
| Ortak test altyapısı, kapsam | **K019-testing** | Kapsam dışı | `integration-testing/index.md` §Sınır |
| Entegrasyon test ortamı (DB izolasyonu, HTTP sözleşmesi, katmanlar) | **K019-testing/integration** | Kapsam dışı → ⚠️ VERIFICATION REQUIRED | `.ai/architecture/K019-testing/integration/integration-test-ortami.md` (525 satır, 2026-10-16) |
| Test container'ı olup olmaması | K019 dokümanı iddiası | ⚠️ K019 içeriği bu dosyada **yeniden iddia edilmez**; tek yetkili tanım oradadır | [R4] |

> **⚠️ K019 sınır etiketi (zorunlu):** Bu dosya yalnızca *tanım ve kavram*
> düzeyinde entegrasyon testini işler. Ortam, izolasyon, container, test
> verisi stratejisi gibi **altyapı kararları K019-testing/inin münhasır
> alanıdır**; burada tekrar yazılmaz, çelişki varsa K019 kazanır. (Kaynak:
> `integration-testing/index.md` §Sınır · [R4])

### §5.1 Sınır Uygulama Kuralları (bu klasör için bağlayıcı)

| # | Kural | Sonuç |
|---|---|---|
| 1 | Bu klasörde container/DB-izolasyonu *tarifi* yazılmaz | K019'a yönlendirilir |
| 2 | K019 ile bu klasör arasında çelişki çıkarsa | K019-testirik kazanır (altyapı sahibi) |
| 3 | Bu klasör yalnızca: tanım · sözleşmeler · doğrulama kuralı · örnek | — |
| 4 | Ortam ihtiyacı doğarsa | Önce `K019-testing/integration/integration-test-ortami.md` okunur |
| 5 | Test *kapsamı* (hangi test?) K019'da, test *sözleşmesi* (ne beklenir?) burada | İkisi ayrımlıdır |

---

## §6 Repo Durum Kanıtı (2026-10-16 disk ölçümü)

| Kanıt | Dosya / Satır | Ne gösteriyor |
|---|---|---|
| Unit test mevcut | `api.coremusic.net/tests/Unit/RouteConfigTest.php` — **208 satır** | Test kültürü var; dosya adı `Unit/` altında |
| Integration suite kaldırılmış | `auth.coremusic.net/phpunit.xml` satır 10-12: "Integration suite KALDIRILDI (2026-09-29): tests/Integration/ dizini diskte YOK → PHPUnit 10 … exit 2 ile TÜM testleri başlatmıyordu" | **`tests/Integration/` dizini diskte yok** — negatif kanıt, kendi ağzından |
| CI yorumu | `.github/workflows/ci.yml` satır 77: "auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK" | CI da bu gerçeği kaydeder |
| API suite | `api.coremusic.net/phpunit.xml` — tek `<testsuite name="Unit">` | Entegrasyon suite'i yok |
| Paylaşılan suite | `shared/phpunit.xml` — 8 suite: Unit · Api · Repository · Events · Security · Middleware · Component · OAuth (hepsi `tests/` altı; `Integration` yok) | "Api/Security" adları suite adıdır, **bu bir entegrasyon testi dizini kanıtı değildir** → ⚠️ |
| Sahte fetch (mock) | `assets.coremusic.net/tests/mocks/player-api.mock.js` — 184 satır, `createMockFetch`, senaryolar `success/notfound/unauthorized/servererror/network` | Repo'daki tek mock-sunucu benzeri kaynak |

> **Doğruluk notu:** `shared/tests/Api/` gibi dizinlerdeki testlerin "dar
> entegrasyon" sayılıp sayılmayacağı bu turda **dosya bazında okunmadı** →
> `⚠️ VERIFICATION REQUIRED`.

### §6.1 CI Test Yüzeyi (`.github/workflows/ci.yml` — gözlem, 2026-10-16)

| Yüzey | Satır (yaklaşık) | Gözlem |
|---|---|---|
| PHP test matrix | 67-108 | `phpunit` koşulur; auth için `--testsuite Unit` (satır 80) — entegrasyon suite'i çalıştırılmaz |
| Not | satır 77 | "tests/Integration dizini diskte YOK" — CI metninde kayıt |
| JS unit (Vitest) | 151-157 | unit katmanı |
| JS coverage | 167-175 | `continue-on-error: true` — **yumuşak** kapısı |
| JS E2E (Playwright) | 179-202 | `continue-on-error: true` — **yumuşak** kapısı (A6 notu) |

> **Yorum-sınırlı iddia:** "CI entegrasyon testi koşmuyor" cümlesi,
> yukarıdaki satır gözlemlerinden **türetilmiştir**; CI'nın gerçekten geçip
> geçmediği (başarı durumu) bu turda **doğrulanmadı** → ⚠️.

---

## §7 Rol Kesişimi — QA · DevOps · K013 · K019

| Rol | Bu dosyadaki sorumluluk | Kanıt |
|---|---|---|
| QA Engineer | Test stratejisi, piramit oranı, kapsam | `.ai/AGENTS.md` §6 (test → QA Engineer) |
| DevOps Engineer | CI kapısını koşturur (test job'ları) | `.ai/AGENTS.md` §6 |
| K013-integraion (bu klasör) | Sözleşme + sahte çevre *kuralı* | `index.md` §Amaç |
| K019-testing | Ortam + kapsam *altyapısı* | `index.md` §Sınır · [R4] |

---

## §8 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu | Kanıt |
|---|---|---|
| "Integration test = yavaş uçtan uca test" | **Dar** entegrasyon testi taklidin yanında, unit test kadar hızlı koşabilir | [S1] |
| "Contract test = OpenAPI şeması doğrulama" | Contract test **davranış** testidir (gerçek çağrı taklitle aynı mı); Pact bunu "contract by example" der, şema/spec ise statik belgedir | [S3] |
| "Mock sunucu = gerçek servis" | Taklidin sadakati contract test ile ayrıca sınanır | [S4] |
| "Her test entegrasyon testidir" (her şey birbirine bağlı diye) | Piramit buna karşıdır: baz unit, tepe uçtan uca | [S2] |
| `tests/Api/` = entegrasyon testi | Suite **adı** kanıt değildir; dosyanın ne yaptığı okunmalı → ⚠️ | [R2] |

---

## §9 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Test Double** | Gerçek nesnenin test için yerine konan sahte (mock, stub, spy, fake, dummy ailesi) — Fowler makalesinde `TestDouble` bliki'ne bağlanır [S2] |
| **Stub** | Cevapları önceden belirlenmiş sahte; "kutulu cevap" verir [S2] |
| **Mock** | Beklenti/etkileşim de üreten sahte (doğrulama içerir) | 
| **Fake** | Davranışı basitleştirilmiş ama çalışan gerçekçi sahte (ör. in-memory DB) | 
| **Narrow test** | Yalnızca sınır kodunu çağıran dar entegrasyon testi [S1] |
| **Broad test** | Tüm servisleri canlı isteyen geniş test [S1] |
| **CDC** | Consumer-Driven Contract — tüketici yazdığı beklentiyi üretici koşturur [S2], [S3] |
| **Smoke test** | Uçtan uca, sınırlı yol kapsayan son kontrol [S1] |

---

## §10 Kapsam Dışı (bu dosyada işlenmez)

1. `ittest-sozlesme.md` — ortam sözleşmesi, stub sözleşmesi, canlı onay kapısı
2. `ittest-dogrulama.md` — sözleşme doğrulama, mock tutarlılığı, CI kapısı
3. `ittest-kod-ornekleri.md` — 12 dilde gerçek kalıp örnekler
4. `ittest-web-arastirma.md` — tüm URL kanıtları
5. Ortam/izolasyon/container stratejisi → **K019-testing** (⚠️ sınır)
6. OpenAPI belgesinin kendisi → `K013-integraion/api-contracts/openapi/`

---

## §11 Açık Sorular (özet — tam liste: `ittest-web-arastirma.md` §Açık Sorular)

| # | Soru | İşaret |
|---|---|---|
| Q1 | CoreMusic dar entegrasyon testleri ne yazılacak, kim yazacak? | ⚠️ VERIFICATION REQUIRED |
| Q2 | Canlı (live) test için onay kapısı ve sahibi kim? | UNKNOWN (ADR bekliyor) |
| Q3 | `shared/tests/Api/` altındaki dosyalar dar entegrasyon mu? | ⚠️ VERIFICATION REQUIRED (okunmadı) |
| Q4 | Sahte sunucu sahipliği (QA/DevOps) | ⚠️ VERIFICATION REQUIRED |
| Q5 | Piramit oranı CoreMusic'te sayısal hedef (ör. %X unit)? | UNKNOWN |

---

## §12 Okuma Haritası — 5 Dosyalık Seri

| # | Dosya | Ne verir |
|---|---|---|
| 1 | **`ittest-anayasa.md`** (bu dosya) | Unit≠Integration≠Contract, piramit, live vs mock, K019 sınırı |
| 2 | `ittest-sozlesme.md` | Ortam sözleşmesi, stub sözleşmesi, canlı test onay kapısı |
| 3 | `ittest-dogrulama.md` | Sözleşme doğrulama, mock tutarlılığı, eski kayıt, secret tarama, CI |
| 4 | `ittest-kod-ornekleri.md` | 12 dilde kalıp örnekler |
| 5 | `ittest-web-arastirma.md` | Her iddianın arkasındaki URL + reddedilenler |

---

## §13 İlgili Düğümler — Ağaçtaki Yeri

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion/integration-testing` | Bu teknoloji serisi o rolün içindedir |
| ↑ | `K013-integraion` | Dış dünya köprüsü |
| ↑ | `K013-integraion/api-contracts/openapi` | Sözleşme belgesi — contract test'in girdisi |
| ↔ | `K019-testing/integration` | ⚠️ Ortam altyapısı sahibi — sınır |
| ↓ | `K011-application` / `K012-services` | Test edilen uygulama/servisler |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | Dar/geniş entegrasyon testi tanımı, "integration test bulanık" uyarısı, smoke test, taklit+contract birleşimi | https://martinfowler.com/bliki/IntegrationTest.html | 2026-10-16 | ✅ çekildi |
| S2 | Test piramidi (Cohn), unit/integration/contract/e2e katmanları, dar entegrasyon adımları, üretimde test yasağı, sıralı oran kuralı | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✅ çekildi |
| S3 | Contract test tanımı ("izole test ederek mesaj uyumu"), consumer/provider terminolojisi, contract-by-example vs statik şema | https://docs.pact.io/ | 2026-10-16 | ✅ çekildi |
| S4 | Contract test ritmi (günde 1), canlıya dikkat, taklidin doğruluğu, build'i kırma yerine görev açma | https://martinfowler.com/bliki/ContractTest.html | 2026-10-16 | ✅ çekildi |
| R1 | Klasör künyesi ve sınır satırı ("Ortak test altyapısı ve kapsam sahibi K019-testing") | `integration-testing/index.md` | 2026-10-16 | ✅ disk |
| R2 | Unit test kanıtı + suite durumları | `api.coremusic.net/tests/Unit/RouteConfigTest.php` · `api.coremusic.net/phpunit.xml` · `shared/phpunit.xml` | 2026-10-16 | ✅ disk |
| R3 | Integration suite kaldırma notu + CI yorumu | `auth.coremusic.net/phpunit.xml` (satır 10-12) · `.github/workflows/ci.yml` (satır 77) | 2026-10-16 | ✅ disk |
| R4 | K019 entegrasyon test ortamı dokümanı (sınır sahibi) | `.ai/architecture/K019-testing/integration/integration-test-ortami.md` | 2026-10-16 | ✅ disk (525 satır) |
| R5 | Tek mock-sunucu benzeri kaynak | `assets.coremusic.net/tests/mocks/player-api.mock.js` (184 satır) | 2026-10-16 | ✅ disk |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `⚠️ VERIFICATION REQUIRED` işaretli satırlar için **kaynak yoktur** ve
> dayanak olarak kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · martinfowler.com · docs.pact.io · **Tarih: 2026-10-16**
