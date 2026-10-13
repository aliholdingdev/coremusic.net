---
node_id: K013-integraion/api-contracts/openapi/openapi-dogrulama
node_type: module
title: "OpenAPI Doğrulama — Şema, Referans, Örnek, Sürüm ve CI Kapısı"
type: technology-deepdive
status: draft
date: 2026-10-13
parent_id: K013-integraion/api-contracts/openapi
children: []
depends_on:
  - K013-integraion/api-contracts
evidence: []
validation: []
open_questions: []
group: kod
---

# OpenAPI Doğrulama — Doğrulama Katmanları ve CI Kapısı

> **Bu dosya ne değildir:** Belge *üretimi* rehberi veya araç kurulumu
> değildir. Bu dosya bir OpenAPI dokümanının **nasıl yanlış olabileceğini**
> ve bu yanlışlığın **hangi katmanda, hangi araçla, hangi formatta**
> yakalandığını tanımlar; iddialar `json-schema.org` / `redocly.com` /
> `stoplight.io` / GitHub depoları sayfalarından alınmıştır.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod ⚠️ PLANNED
> (CoreMusic repo'sunda OpenAPI belgesi ve belge-doğrulama pipeline'ı bu
> turda tespit edilmedi; repo'da *runtime* doğrulama kanıtı mevcuttur — §10).

---

## §0 ELI15 — Bu dosya ne işe yarar?

Bir OpenAPI dosyası yazıyla, gözle, elle doğrulanmaz: belge önce bir
**şemaya** (belgenin kendisini tarif eden meta-şema) karşı **şema
doğrulamasından** geçirilir, sonra iç **referansları** (`$ref`) çözülür,
sonra **örnekler** şemayla çelişmez mi bakılır, en sonda **lint** kuralları
belge *kalitesini* (tutarlılık, iyi pratik) denetler — bu dört katmanın
hiçbiri atlanmazsa hatalı belge CI hattından geçemez ve kapı kapanır.
(Kaynak: §Kaynak Tablosu [V1], [V2], [V5])

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | openapi-anayasa.md | Kimlik, tarihçe, sürümler, belge yapısı özeti | 3 |
| mimari | openapi-sozlesme-dili.md | Path/operation/parameter/requestBody/response + `$ref`/bileşim/sürüm farkı | 3 |
| kod | openapi-dogrulama.md | Doğrulama katmanları: şema, referans, örnek, CI kapısı (bu dosya) | 3 |
| kod | openapi-kod-ornekleri.md | Gerçek YAML/PHP/JS örnekleri, repo kanıtlı | 3 |
| kanit | openapi-web-arastirma.md | Benzersiz resmi URL + iddia/URL/tarih/durum tablosu | 3 |

---

## §1 Doğrulama Katmanları Genel Bakış

| # | Katman | Ne denetler | Ne yakalamaz | Kanıt |
|---|---|---|---|---|
| 1 | Şema doğrulama | Belge, meta-şemaya (OAS şemasına) uyuyor mu | Anlamsız ama biçimsel doğru içerik | [V1], [V2] |
| 2 | Referans bütünlüğü | Her `$ref` çözülebiliyor mu, döngü var mı | Çözülen ama yanlış şemayı işaret eden ref | [V2], [V4] |
| 3 | Örnek tutarlılığı | `example`/`examples` kendi şemasına uyuyor mu | Uydurma ama geçerli örnek | [V5] |
| 4 | Sürüm uyumluluğu | Belge hangi sürümü hedefliyor, araç o sürümü tanıyor mu | Sürüm-özel anahtar kaybı | [V1], [V3] |
| 5 | Statik lint (kalite) | İsimlendirme, zorunluluk, iyi pratik kuralları | Biçimsel hata (onu 1. katman yakalar) | [V4], [V8] |
| 6 | Runtime doğrulama | Çalışan istek/yanıt belgeye uyuyor mu | Belge-yükü hatası (statik yakalar) | [V6], [V9] |

**Katman sırası zorunludur:** 1. katman geçmeden 2-6 çalışmaz — biçim
bozuk bir belgede referans çözümleme tanımsızdır. (Kaynak: [V2])

---

## §2 Şema Doğrulama — Instance ↔ Schema

| Kavram | Tanım | Kanıt |
|---|---|---|
| Instance | Doğrulanan belge (OpenAPI JSON/YAML'inin JSON'a çevrimi) | [V1] |
| Schema | OpenAPI'nin kendi meta-şeması (belge yapısını tarif eden şema) | [V2] |
| Dialect | Şemanın dayandığı JSON Schema sürümü/dialect'i | [V1], [V3] |

**Sürüm-dialect eşleşmesi zorunludur:** 3.0 belgesi 3.0 meta-şemasıyla,
3.1 belgesi 3.1 (JSON Schema 2020-12 uyumlu) meta-şemasıyla doğrulanır;
yanlış dialect ile doğrulama ya sahte hata ya da sahte temizlik üretir.
(Kaynak: [V1], [V3])

| Sürüm | Meta-şema kaynağı | Kanıt |
|---|---|---|
| 3.0.x | OAI spec deposu `versions/` dizininin sürüm metni + şema paketi | [V2] |
| 3.1.x | JSON Schema 2020-12 uyumlu; `jsonSchemaDialect` alanı kökte tanımlanabilir | [V1], [V3] |
| 3.2.x | Depoda `versions/3.2.0.md` ve `versions/3.2.1.md` metinleri mevcut (2026-10-13 ölçümü) | [V2] |

> **Eleneen aday notu:** OAI'nin eski `validator.oai.org` adresi bu turda
> ad çözülemedi (DNS hatası) → dayanak yapılmadı; `json.schemastore.org`
> altındaki OpenAPI şema yolları 404 döndü → dayanak yapılmadı (bkz. §13).

### §2.1 Şema Doğrulama Geçiş Adımları

| Adım | İşlem | Hata sınıfı | Kanıt |
|---|---|---|---|
| 1 | Dosya okunur (JSON/YAML ayrıştırma) | Sözdizimi hatası (bozuk YAML/JSON) | [V7] |
| 2 | Kök alanlar taranır (`openapi`, `info` …) | Eksik zorunlu kök alan | [V1] |
| 3 | Sürüm okunur, dialect seçilir | Bilinmeyen/desteklenmeyen sürüm | [V1], [V3] |
| 4 | Belge, meta-şemaya karşı doğrulanır | Şema-ihlali (yanlış tip/eksik alan) | [V2] |
| 5 | Uzantı anahtarları (`x-`) kontrol edilir | `x-` dışı bilinmeyen anahtar (uyarı/error — politikaya bağlı) | [V1], [V8] |

**Adım 1 ≠ Adım 4:** Bozuk YAML, JSON-schema katmanına hiç ulaşmaz —
ayrıştırma hatası ayrı bir hata sınıfıdır ve şema-hatası raporuna
karıştırılmamalıdır. (Kaynak: [V7], [V9])

### §2.2 Zorunlu Kök Alan Denetimi Tablosu

| Alan | 3.0 zorunlu | 3.1 zorunlu | İhlal cezası | Kanıt |
|---|---|---|---|---|
| `openapi` | Evet | Evet | Belge geçersiz (error) | [V1] |
| `info` | Evet | Evet | Belge geçersiz (error) | [V1] |
| `info.title` | Evet | Evet | Belge geçersiz (error) | [V1] |
| `info.version` | Evet | Evet | Belge geçersiz (error) | [V1] |
| `paths` | Evet* | Evet* | Belge geçersiz (error) — *webhooks ile ikame | [V1] |
| `components` | Hayır | Hayır | — | [V1] |
| `servers` | Hayır | Hayır | — | [V4] |
| `webhooks` | Hayır (alan yok) | Hayır | 3.0'da error (bilinmeyen kök alan) | [V1] |

---

## §3 Referans Bütünlüğü — `$ref` Kırılması

| Hata sınıfı | Ne demektir | Örnek tetikleyici | Kanıt |
|---|---|---|---|
| Çözülemeyen ref | İşaret edilen JSON Pointer yok | `/components/schemas/Pet` ama `Pet` tanımsız | [V2], [V4] |
| Kırık zincir | Ref, başka bir kırık repe işaret ediyor | `A → B → C` zincirinde `C` tanımsız | [V4] |
| Döngü | Ref kendi kendine (dolaylı) dönüyor | `A → B → A` | [V4] |
| Yanlış kapsam | Ref, bulunduğu yerde geçersiz | `parameters` alanında şema-ref | [V1] |

**Döngü yasağı şartnamede mutlak değildir:** 3.1 (JSON Schema uyumu)
özyinelemeli şemalara izin verir; ancak çoğu araç döngüyü *belge-yükleme*
hatası sayar — bu araç-davranışıdır, şartname-kuralı değil → araç seçimi
ADR'ye yazılmalıdır. (Kaynak: [V1], ⚠️ VERIFICATION REQUIRED: hangi aracın
döngüyü hangi sertlikte ele aldığı bu turda tek tek çekilmedi.)

**Statik ref denetimi CI'da nasıl çalışır:** Belge, yükleme-çözümleme
geçişinden geçer; çözülemeyen her ref, JSON Pointer yolunun yer aldığı bir
hata satırı üretir ve pipeline exit-code ile durur (§8). (Kaynak: [V4], [V5])

### §3.1 Referans Hata Sınıfları — Denetim Matrisi

| Senaryo | `$ref` değeri | Beklenen sonuç | Şiddet | Kanıt |
|---|---|---|---|---|
| Yerel şema mevcut | `#/components/schemas/User` | Çözülür | — | [V2] |
| Yerel şema yok | `#/components/schemas/Ghost` | Kırık ref | error | [V4] |
| Dosya-içi pointer kaçışı | `#/components/schemas/Pet~1Variant` | `/` kaçışı çözülür | — | [V12] |
| Dış dosya (bundle öncesi) | `./common.yaml#/X` | Paketleme adımında birleştirilir | error (paketlenmemişse) | [V4] |
| Bozuk pointer (`~2`) | `#/a/~2/b` | Geçersiz kaçış | error | [V12] |
| Sadece `#` | `#` | Belgé kökü — tek tip, araç-özel | ⚠️ VERIFICATION REQUIRED | [V1] |

**Paketleme (bundle) ↔ ref bütünlüğü ilişkisi:** Çok dosyalı belgede
dış referanslar, bundle adımında tek dosyaya gömülür; gömülmeyen her
dış-ref, CI'da ayrı bir dosya bağımlılığı olarak kalır ve dağıtım
zamanında 404 üretir — bundle, ref-bütünlüğünün *dağıtım* yüzüdür.
(Kaynak: [V4])

---

## §4 Örnek / `example` Tutarlılığı

| Kontrol | Ne yakalar | Kanıt |
|---|---|---|
| `example` ↔ `schema` uyumu | Şemaya aykırı örnek veri | [V5] |
| `examples` ↔ `schema` uyumu (çoklu) | Her bir örneğin ayrı ayrı uyumu | [V5] |
| `example` ↔ `encoding`/`style` uyumu | Serileştirilmiş örnekte bozuk biçim | [V5], [V10] |
| `requestBody` örnek ↔ `responses` örnek tutarlılığı | İstek-yanıt örnek zıtlığı (kalite linti) | [V8] |

Örnek doğrulaması, şemanın kendisini doğrulamadan *ayrı* bir geçiştir:
belge biçimsel doğru olabilir, içindeki örnek veri yanlış olabilir. Kısmi
doğrulama (yalnız `example` anahtarının varlığı) yetmez — değerin
şemaya karşı da test edilmesi gerekir. (Kaynak: [V5])

> **Doğrulama-örneği ayrımı (CoreMusic bağlanması, repo kanıtı):** Runtime
> tarafında aynı ayrım repo'da `validation` anahtarıyla kuruludur — route
> kaydı doğrulanabilir (biçim) ama iç kural seti ayrıdır: `Dosya:
> api.coremusic.net/config/routes.php / Satır: 14` (`validation` →
> RequestValidationMiddleware kural seti, 422 + error.fields — gerçek disk
> okuması, 2026-10-13).

### §4.1 Örnek Hata Sınıfları — Denetim Matrisi

| Senaryo | Şema | Örnek | Sonuç | Kanıt |
|---|---|---|---|---|
| Tip uyumsuzluğu | `age: integer` | `"age": "otuz"` | error | [V5] |
| Zorunlu alan eksik | `required: [id]` | `{"name":"x"}` | error | [V5] |
| Enum dışı değer | `enum: [a,b]` | `"c"` | error | [V5] |
| Sınır ihlali | `minimum: 1` | `0` | error | [V5] |
| `nullable` ihlali (3.1) | `type` dizisi `["string","null"]` | `123` | error | [V5], [V3] |
| Zorunlu alan mevcut | `required: [id]`, örnek `id` içeriyor | — | geçer | [V5] |

**Çoklu örnek (`examples`):** Dizi halindeki her eleman *ayrı ayrı*
şemaya karşı test edilir; birinin hatası diğerini temiz yapmaz — rapor
her örnek için ayrı konum üretir. (Kaynak: [V5])

---

## §5 Sürüm Uyumluluğu — Doğrulama Açısından 3.0 / 3.1 / 3.2

| Konu | 3.0 doğrulaması | 3.1 doğrulaması | Kanıt |
|---|---|---|---|
| `nullable` | Geçerli anahtar | **Geçersiz** — `type` dizisi kullanılır | [V1], [V3] |
| `type` dizisi | Tek string — dizi **geçersiz** | Dizi geçerli (JSON Schema) | [V3] |
| `exclusiveMinimum` | boolean | number | [V3] |
| `webhooks` kök alanı | Geçersiz | Geçerli | [V1] |
| `example` (şema içi) | Geçerli | Geçerli (JSON-Schema `examples` dizi formu da tanımlanabilir) | [V3] |
| `$ref` yan anahtar | Yok sayılır (uyarı) | Geçerli | [V1] |

**Yanlış-sürüm doğrulamasının maliyeti:** 3.1 belgesini 3.0 dialect'iyle
doğrulamak `type` dizisini ve `webhooks`u hata; 3.0 belgesini 3.1'le
doğrulamak `nullable`'ı hata sayar — ikisi de *gerçek* hatadır ama
karşı tarafça üretilmiş bir *araç* hatasıdır. Sürüm hedefi belge kökünde
(`openapi` alanı) açıkça yazılmalıdır. (Kaynak: [V1], [V3])

### §5.1 Sürüm-Yükseltme Doğrulama Kontrol Listesi (3.0 → 3.1)

| # | Kontrol | Neden | Kanıt |
|---|---|---|---|
| 1 | `nullable` → `type` dönüşümü | 3.1'de `nullable` geçersiz | [V1], [V3] |
| 2 | `exclusiveMinimum`/`exclusiveMaximum` boolean→number | Tip değişimi | [V3] |
| 3 | Şema-içi `example` → `examples` (dizi) değerlendirmesi | JSON-Schema biçimi | [V3] |
| 4 | `$ref` yan anahtarlarının artık yok-sayılmadığı doğrulanır | 3.0'da yok sayılır | [V1] |
| 5 | `jsonSchemaDialect` eklenip eklenmediği kararı | 3.1 kök alanı | [V1] |
| 6 | `webhooks` ihtiyacı (outbound uç varsa) | 3.1 kök alanı | [V1] |
| 7 | Meta-şemanın 3.1 dialect'ine çevrilmesi | Yanlış dialect sahte hata üretir | [V2], [V3] |

---

## §6 Statik Analiz Araçları (Lint) — Ne Verir, Ne Vermez

| Araç | Katman | Ürettiği çıktı biçimi | URL | Kanıt |
|---|---|---|---|---|
| Redocly CLI | Şema + lint + bundle | Konsol raporu + exit-code | https://redocly.com/ | [V4] |
| Stoplight araç ailesi | Lint + şema + tasarım-time doğrulama | Konsol/CI raporu | https://stoplight.io/ | [V8] |
| Swagger Editor | Şema + anlık doğrulama (tarayıcı) | Tarayıcı hata listesi | https://github.com/char0n/swagger-editor | [V7] |
| Swagger Parser | Şema + ref çözümleme (kütüphane) | Yapısal hata listesi (kütüphane çağrısı) | https://github.com/APIDevTools/swagger-parser | [V9] |
| OpenAPI Generator | Şema + ref + jenerasyon | Kod üretim hatası (önce belgeyi yer) | https://github.com/OpenAPITools/openapi-generator | [V6] |
| validator.swagger.io | Şema doğrulama (servis) | HTTP yanıt + hata listesi | https://validator.swagger.io/ | [V11] |

**Lint ≠ doğrulama:** Lint kuralları iyi-pratik denetimidir (ör. "operationId
eksik" bir *kalite* uyarısıdır, belgeyi geçersiz *yapmaz*); şema
doğrulaması ise geçerlilik denetimidir ("`nullable` 3.1'de yok" belgeyi
geçersiz yapar). CI'da ikisi ayrı kapı olarak tanımlanabilir: geçerlilik
kapısı sert (error → dur), kalite kapısı yumuşak (warning → log). (Kaynak:
[V4], [V8])

> **Araç adı-uyarısı (zero-hallucination):** "Spectral" adlı lint motoru
> Redocly ürün ailesiyle ilişkilidir; bu turda resmi sayfası çekilmedi →
> bu dosyada URL'si yazılmadı, adı yalnızca not olarak geçer → ⚠️
> VERIFICATION REQUIRED.

### §6.1 Araç ↔ Katman Eşleme Matrisi

| Araç | Şema (§2) | Ref (§3) | Örnek (§4) | Lint (§6) | Kanıt |
|---|---|---|---|---|---|
| Redocly CLI | Evet | Evet | Evet | Evet | [V4] |
| Stoplight ailesi | Evet | Evet | Evet | Evet | [V8] |
| Swagger Editor | Evet | Evet | Kısmi | Hayır | [V7] |
| Swagger Parser | Evet | Evet | Hayır | Hayır | [V9] |
| OpenAPI Generator | Evet | Evet | Hayır | Hayır | [V6] |
| validator.swagger.io | Evet | Hayır | Hayır | Hayır | [V11] |

**Matris okuma kuralı:** Hiçbir tek araç altı katmanı da kapatmaz —
`validator.swagger.io` yalnız şema katmanını verir; referans/örnek
katmanları için parser/bundle özelliği olan araç gerekir. (Kaynak: [V9],
[V11])

**Araç-özel sütun boşlukları (UNKNOWN):** "Kısmi" hücre, aracın *hangi*
örnek biçimini desteklediğinin bu turda çekilmediği anlamına gelir →
araç seçimi ADR'sinde desteklenen biçim tek tek doğrulanmalıdır. (⚠️
VERIFICATION REQUIRED)

---

## §7 Statik ↔ Runtime Doğrulama Ayrımı

| Eksen | Statik doğrulama | Runtime doğrulama |
|---|---|---|
| Ne denetler | Belge ↔ meta-şema / referans / örnek | İstek-Yanıt ↔ belge |
| Ne zaman | Commit/PR anında (CI) | Sunucu çalışırken (middleware/filtre) |
| Çıktı | Hata listesi + exit-code | 400/422/500 HTTP hatası |
| Sahibi | DevOps + Backend Architect | Backend Architect |
| Örnek araç/parça | §6'daki statik araçlar | Repo'da `ValidationMiddleware` (§10) |
| Belge yoksa | Denetlenecek şey yok → kapı tanımsız | Runtime doğrulama yine çalışır (belgeden bağımsız) |

**Kritik ayrım:** Statik doğrulama belgeyi; runtime doğrulama *isteği*
doğrular. Belge hatasız ≠ çalışan API belgeye uyar; ikisi ayrı
sorumluluklardır ve ayrı ayrı test edilir. (Kaynak: [V9], [V6])

### §7.1 Zaman Çizelgesi — Bir Değişiklik Nerede Denetlenir?

| Aşama | Ne olur | Denetim | Başarısızlık | Kanıt |
|---|---|---|---|---|
| 1. Editör | Belge yazılır | Anlık şema (editor) | Kırmızı işaret (editör-içi) | [V7] |
| 2. Commit öncesi | Lokal lint çalışır | Şema + ref + lint | Commit engellenmez ama rapor | [V4] |
| 3. PR | CI şema+ref kapısı | Sert kapı | PR FAIL — merge yasak | [V4], [V11] |
| 4. PR | CI lint kapısı | Politikaya bağlı | error → FAIL, warning → log | [V8] |
| 5. Dağıtım | Üretilmiş istemci/sunucu | Runtime doğrulama | 400/422/500 | [V6], [V9] |
| 6. Üretim | Canlı trafik | Gözlem (log/metric) | Alarm — kapı değil, sinyal | [V8] |

**Aşama 5-6 statik kapıya girmez:** Runtime ve üretim gözlemi *pipeline*
kapısı değildir; hata orada yakalanır ama merge'i geri çevirmez — bu,
kapı-kavramının en sık karıştırıldığı sınırdır. (Kaynak: [V6])

---

## §8 CI'a Bağlama — Kapı = Hata → Pipeline Durur

| Kapı | Kural | İhlal durumunda | Kanıt |
|---|---|---|---|
| Şema kapısı | Meta-şema doğrulaması hatasız olmalı | Exit-code ≠ 0 → job FAIL | [V4], [V11] |
| Referans kapısı | Tüm `$ref` çözülebilmeli | Exit-code ≠ 0 → job FAIL | [V4] |
| Örnek kapısı | Tüm örnekler şemaya uygun olmalı | Exit-code ≠ 0 → job FAIL (uyarı da yapılabilir — politika kararı) | [V5] |
| Kalite (lint) kapısı | Seçilen kurallar | Politika: error → FAIL, warning → log | [V8] |

**Exit-code sözleşmesi:** Araçlar başarıda `0`, hatada `0`-olmayan değer
döndürür; CI bu değeri okur ve durdurur — kapı, insan onayı olmadan
geçilemez. Bu sözleşme tüm CLI araçları için ortaktır (araç dokümantasyonu
ortak kabul; tekil sürüm-özel davranışlar ⚠️ VERIFICATION REQUIRED).
(Kaynak: [V4])

**Pipeline yerleşimi (tasarım önerisi — kanıt değil):** `composer test`
benzeri çok-adımlı bir job içinde: `1) belge yükle+çözümle → 2) şema+ref
doğrula → 3) lint → 4) birim/integrasyon testleri`. Sıra, §1 katman
sırasıyla aynıdır; tersi, tanımsız davranış üretir. CoreMusic'te bu job
**kurulu değildir** → ⚠️ VERIFICATION REQUIRED (repo'da belge + job bu turda
tespit edilmedi).

### §8.1 Kapı İhlal Senaryoları

| Senaryo | Ne olur | Kapı davranışı | Kanıt |
|---|---|---|---|
| Şema hatası | Araç exit-code ≠ 0 döndürür | Job FAIL, merge yasak | [V4], [V11] |
| Uyarı-only (lint) | Exit-code 0, rapor log'a | Job PASS, rapor arşiv | [V8] |
| Araç çöker (timeout) | Exit-code ≠ 0 + stderr | Job FAIL — fail-safe (fail-open değil) | [V4] |
| Belge hiç yok | Adım "dosya yok" hatası | Job FAIL (yok-sayma yok) | [V4] |
| Branch kuralı | Ana dala merge öncesi | Kapı merge-içi, branch dışı | [V8] |
| Manuel bypass | Admin override | Loglanır — kapı-devre-dışı gerekçesi zorunlu | [V8] |

**Fail-safe ilkesi:** Araç hata veremez durumda (crash, timeout) kapı
*kapalı* sayılır (job FAIL) — araç-sağlığı yokluğunda sessiz-geçiş,
hatalı belgenin production'a sızmasına izin verir. (Kaynak: [V4])

---

## §9 Hata Raporlama Formatı

| Alan | Ne taşır | Kanıt |
|---|---|---|
| Dosya | Belge dosyası (bundle ise hangi kaynak) | [V4] |
| Konum | JSON Pointer / satır-sütun (araç desteklerse) | [V7], [V9] |
| Kural kodu | Lint kuralı adı veya şema yolu | [V8] |
| Şiddet | `error` / `warning` / `info` | [V4], [V8] |
| Mesaj | Ne yanlış, neden | [V4] |

**JSON Pointer tabanlı konum:** `#/paths/~1api~1v1~1auth~1login/post/requestBody/content/application~1json/schema` — RFC 6901 kaçışları (`~1` = `/`) ile tam belge-yolu yazılır; araç-raporu-araç-raporu taşınabilirliği bu yolla korunur. (Kaynak: [V12], [V4])

**Şiddet eşlemesi (CI):** `error` → kapı kapalı (pipeline durur); `warning`
→ kapı açık ama raporlanır; `info` → yalnız log. Eşlemenin kendisi bir
**politika**dır — eşiği ekip belirler, araç dayatmaz. (Kaynak: [V8])

### §9.1 Hata Raporu Örneği — Şema İskeleti (Tasarım Önerisi)

| Alan | Örnek değer (temsilî) | Not |
|---|---|---|
| `file` | `openapi.yaml` | Kaynak dosya (bundle ise gömülü kaynak) |
| `location` | `#/paths/~1api~1v1~1auth~1login/post/requestBody/content/application~1json/schema/properties/password` | RFC 6901 pointer |
| `rule` | `schema/type-mismatch` | Kural kodu (araç-özel) |
| `severity` | `error` | CI eşlemesi §9 |
| `message` | Beklenen tip `string`, bulunan tip `integer` | İnsan-okur |

> **Dürüstlük notu:** Tablodaki `rule` kodu **temsilîdir** — hangi aracın
> hangi kural kodunu ürettiği bu turda tek tek çekilmedi → gerçek kural
> kodu adı ⚠️ VERIFICATION REQUIRED (uydurma araç-kuralı yazılmadı).

**Rapor-taşınabilirliği:** Rapor çıktısı CI artefaktı olarak saklanır;
araç değiştirilse bile JSON Pointer konum biçimi korunduğu için eski
raporlar okunabilir kalır. (Kaynak: [V12], [V4])

---

## §10 CoreMusic Repo Karşılığı — Runtime Doğrulama Kanıtı (Tasarım İddiası + Gerçek Satırlar)

| Sözleşme kavramı | Repo karşılığı | Kanıt (gerçek disk okuması, 2026-10-13) |
|---|---|---|
| Statik belge doğrulama (CI) | — | ⚠️ VERIFICATION REQUIRED (repo'da `openapi.yaml`/`.json` + job tespit edilmedi) |
| Runtime istek doğrulama | `ValidationMiddleware` (Pipeline #10) | `shared/src/Middleware/ValidationMiddleware.php` Satır 15 (`final class ValidationMiddleware implements IMiddleware`) |
| Kural kaynağı | Route meta `validation` anahtarı → 422 + error.fields | `api.coremusic.net/config/routes.php` Satır 14 |
| Tanımlı-ama-yok sayılan uç | `implemented: false` → 405 + Allow (404 değil) | `api.coremusic.net/config/routes.php` Satır 12 |
| Doğrulama testi yüzeyi | `RequestValidationMiddlewareTest.php` | `shared/tests/RequestValidationMiddlewareTest.php` (dosya varlığı) |
| Route-tablosu testi | `RouteTableTest.php` | `shared/tests/RouteTableTest.php` (dosya varlığı) |
| Test çalıştırıcı | PHPUnit ^10.5 | `api.coremusic.net/composer.json` Satır 25 (`"phpunit/phpunit": "^10.5"`) |
| Test kaydı (require-dev) | `require-dev` bloğu | `api.coremusic.net/composer.json` Satır 24 (`"require-dev": {`) |

**Önemli dürüstlük notu:** Repo'daki doğrulama, OpenAPI belgesine karşı
**runtime** doğrulamadır; belge-karşı-statik-doğrulama (bu dosyanın §2-§5
katmanları) repo'da **kurulu değildir** — bu, CoreMusic'in bu gate'deki
**PLANNED** yüzeyidir. (Kaynak: [V6], [V9] — uydurma pipeline adı yazılmadı.)

### §10.1 Repo Test Yüzeyi — Doğrulama İlişkili Test Dosyaları

| Test dosyası | Ne test eder | Doğrulama katmanı ilişkisi |
|---|---|---|
| `shared/tests/RequestValidationMiddlewareTest.php` | İstek doğrulama kuralları | Runtime katmanı (§7) |
| `shared/tests/RouteTableTest.php` | Route tablosu davranışı | 404/405 ayrımı (§9) |
| `shared/tests/GatewayMethodRoutingTest.php` | Gateway metod yönlendirme | Sözleşme-davranış (§7) |
| `shared/tests/ResponseNormalizationMiddlewareTest.php` | Yanıt normalizasyonu | Çıktı biçimi (§7) |
| `shared/tests/VersioningTest.php` | API sürüm davranışı | Sürüm uyumluluğu (§5) |
| `shared/tests/ApiPipelineSecurityTest.php` | Pipeline güvenlik sırası | Kapı fail-safe (§8) |

**Test ↔ gate ilişkisi (tasarım önerisi):** OpenAPI statik kapısı bu
testlerin *önündedir* — belge hatalıysa testler değil, belge düzeltilir;
testler ise *belgeye değil koda* bağlıdır. İkisinin merge sırası:
statik kapı → test → merge. (Kaynak: [V4], [V6])

---

## §11 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| Meta-şema | Belge yapısını tarif eden şema (OpenAPI'nın kendi şeması) |
| Instance | Doğrulanan somut belge örneği |
| Dialect | JSON Schema sürümü/ varyantı (2020-12, 2019-09 …) |
| Lint | İyi-pratik kalite denetimi (geçerlilikden ayrı) |
| Kapı (gate) | CI'da hatada pipeline'ı durduran kontrol |
| Exit-code | Aracın başarı/hata dönüş değeri (0 / 0-değil) |
| Şiddet | Hata raporu sertliği: error / warning / info |
| Bundle | Çok dosyalı belgeyi tek dosyaya katlama işlemi |
| Kırık referans | Çözülemeyen `$ref` |

---

## §12 Sık Karşılaştırılan Kavramlar

| Kavram A | Kavram B | Fark |
|---|---|---|
| Statik doğrulama | Runtime doğrulama | Belge ↔ şema vs istek ↔ belge |
| Şema doğrulaması | Lint | Geçerlilik (zorunlu) vs kalite (politika) |
| Referans bütünlüğü | Şema doğrulaması | `$ref` çözülebilirlik vs belge yapısı |
| Meta-şema | API şeması | Belgeyi tarif eder vs API verisini tarif eder |
| Exit-code | Hata mesajı | Makine kararı vs insan-okur açıklama |
| Kapı politikası | Araç davranışı | Ekip kararı vs aracın varsayılanı |

---

## §13 Kapsam Dışı (bu dosyada işlenmez)

| Konu | Nerede | Not |
|---|---|---|
| Belge yapısı (`paths`, `components` …) | `openapi-sozlesme-dili.md` §4-§13 | Sözleşme dilinin kendisi |
| Sürüm tarihçesi | `openapi-anayasa.md` §2/§8 | Kimlik dosyası |
| Kod/CLI örnekleri | `openapi-kod-ornekleri.md` | Uygulama dosyası |
| Elenen adayların tam listesi + red listesi | `openapi-web-arastirma.md` §9/§18 | Denetim dosyası |

**Bu dosyada dayanaksız yazılmayanlar:** araç sürüm numaraları · CVE ·
uydurma pipeline adı · uyduma kural kodu · `validator.oai.org`/schemastore
bağlantıları (404/DNS — dayanak yapılmadı, yalnızca elenme notu olarak geçti).

---

## §14 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | Doğrulama üretimde mi testte mi? | UNKNOWN (ADR bekliyor — `index.md` §4 ile aynı) |
| 2 | Lint kapı politikası: tüm warning'ler error mü? | UNKNOWN (ekip kararı) |
| 3 | Belge hangi sürümü hedefliyor (3.0/3.1/3.2)? | UNKNOWN (ADR bekliyor) |
| 4 | Döngüsel `$ref` politikası: yasak mı, izin mi? | ⚠️ VERIFICATION REQUIRED (araç davranışı çekilmedi) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler — 2026-10-13 HEAD 200)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| JSON Schema öğrenme kaynağı (instance/schema/dialect kavramları) | https://json-schema.org/understanding-json-schema | 2026-10-13 | 200 |
| OAI spec deposu sürüm metinleri (`versions/` dizini) | https://github.com/OAI/OpenAPI-Specification/tree/main/versions | 2026-10-13 | 200 |
| Şema doğrulama servisi (HTTP + hata listesi) | https://validator.swagger.io/ | 2026-10-13 | 200 |
| Lint/bundle/CLI ailesi | https://redocly.com/ | 2026-10-13 | 200 |
| Lint + tasarım-time doğrulama ailesi | https://stoplight.io/ | 2026-10-13 | 200 |
| Parser kütüphanesi (şema + ref çözümleme) | https://github.com/APIDevTools/swagger-parser | 2026-10-13 | 200 |
| Generator (belgeyi yer, sonra kod üretir) | https://github.com/OpenAPITools/openapi-generator | 2026-10-13 | 200 |
| Editor (tarayıcı-içi anlık doğrulama) | https://github.com/char0n/swagger-editor | 2026-10-13 | 200 |

**Tahsis sapması notu (dürüstlük kaydı):** Görev tahsisi bu dosya için
`https://github.com/OAI/OpenAPI-Specification/tree/main/schemas` yolunu
önermişti; 2026-10-13 ölçümünde bu yol **404** döndü (depo kökünde
`schemas/` dizini listelenmiyor) → yerine **200** döndüren resmi `versions/`
dizini kullanılmıştır; `validator.oai.org` adı çözülemedi (DNS) ve
`json.schemastore.org` OpenAPI yolları 404 döndü → dayanak yapılmadı.
Uydurma URL yazılmadı, elenme notu olarak §2/§13'te geçti.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · **Tarih: 2026-10-13**
