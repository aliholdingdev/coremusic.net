---
node_id: K013-integraion/api-contracts/openapi/openapi-anayasa
node_type: technology-deepdive
title: "OpenAPI Anayasa — Kimlik, Sürümler, Belgе Yapısı, Bileşen Düzlemi"
type: technology-deepdive
status: draft
date: 2026-10-12
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/api-contracts/openapi
  - K013-integraion/api-contracts
  - K013-integraion
evidence:
  - "https://spec.openapis.org/oas/ (2026-10-12)"
  - "https://spec.openapis.org/oas/v3.2.1.html (2026-10-12)"
  - "https://swagger.io/specification/ (2026-10-12)"
  - "https://github.com/oai/openapi-specification/blob/main/versions/3.1.1.md (2026-10-12)"
  - "https://learn.openapis.org/ (2026-10-12)"
  - "https://json-schema.org/specification (2026-10-12)"
  - "https://datatracker.ietf.org/doc/rfc6901/ (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc9111.html (2026-10-12)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-12 fetch)"
  - "Sürümler spec.openapis.org sayfasındaki liste ile birebir karşılaştırılmıştır"
  - "Uydurma anahtar/ad yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN"
open_questions:
  - "CoreMusic hedef sürümü 3.1 mi 3.2 mi? → UNKNOWN (ADR bekliyor)"
  - "Belge tek dosya mı bundle mı? → ⚠️ VERIFICATION REQUIRED (repo'da belge tespit edilmedi)"
group: mimari
---

# OpenAPI Anayasa — Kimlik · Sürümler · Yapı · Bileşenler

> **Bu dosya ne değildir:** Kod, CLI rehberi veya araç kurulumu değildir.
> Bu dosya OpenAPI'yi *belge kanıtıyla* tanımlar; sürümler ve terimler
> `spec.openapis.org` sayfasından alınmıştır, tahmin değildir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod ⚠️ PLANNED
> (CoreMusic repo'sunda `openapi.yaml`/`.json` çıktısı bu turda tespit edilmedi).

---

## §0 ELI15 — OpenAPI bir cümlede

OpenAPI, bir HTTP API'nin uç noktalarını, girdilerini, çıktısını ve
güvenlik kurallarını **dilden bağımsız** tek bir tanımda toplayan açık bir
şartnamedir; bu tanım hem insanın okuyup anlayabileceği hem de aracın
okuyup doküman/istemci/sunucu üretebileceği bir veri modelidir.
(Kaynak: §Kaynak Tablosu [S1], [S2])

---

## §1 OpenAPI Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | OpenAPI Specification (OAS) | [S1] |
| Eski adı | Swagger 2.0 Specification | [S3] |
| Yayımlayan | OpenAPI Initiative (Linux Foundation altında) | [S1] |
| Tanım dili | Dilden bağımsız (JSON/YAML serileştirilir) | [S1] |
| Belge biçimi | Tek JSON nesnesi; JSON veya YAML olarak yazılır | [S2] |
| Belgedeki uzantı alanı | `x-` önekiyle başlayan anahtarlar | [S2] |
| Dış belge referansı | `$ref` (RFC 3986 URI başvurusu) | [S2] |
| Şema dili | JSON Schema (OpenAPI 3.1'de doğrudan JSON Schema uyumu) | [S6] |
| Anahtar yolu sözdizimi | RFC 6901 JSON Pointer | [S7] |
| Lisans / sahiplik | OpenAPI Initiative — spec deposu `oai/openapi-specification` | [S3] |
| CoreMusic sahibi agent | Backend Architect (A2 katmanı) | `.ai/AGENTS.md` §4/§15 |
| CoreMusic routing | "API, endpoint, routing, middleware, PHP" → Backend (primary) / Security (secondary) | `.ai/AGENTS.md` §6 |

**OpenAPI = OpenAPI Specification.** "Swagger" adı, spec'in 2.0 sürümünün
adıydı; araç ailesinin adı olarak kaldı, şartnamenin adı değil. 3.0'dan
beri resmi ad **OpenAPI Specification**'tır. (Kaynak: [S3])

---

## §2 Sürüm Yüzeyi — spec.openapis.org neyi listeliyor?

Kaynak sayfa (2026-10-12 ölçümü) aşağıdaki sürümleri listeliyor:

| Sürüm grubu | Listelenen sürümler | Kanıt |
|---|---|---|
| 3.x yeni | **v3.2.1**, v3.2.0 | [S1] |
| 3.x orta | v3.1.2, v3.1.1, v3.1.0 | [S1] |
| 3.0 | v3.0.4, v3.0.3, v3.0.2, v3.0.1, v3.0.0 | [S1] |
| 2.x | **v2.0** (Swagger dönemi) | [S1] |

### §2.1 3.1 ve 3.2 arasındaki yapısal fark ne?

| Konu | 3.1 | 3.2 | Kanıt |
|---|---|---|---|
| Şema dili | JSON Schema ile uyum (dokümanda JSON Schema referansı) | Sürüm notu için ilgili sürüme bakınız → ⚠️ VERIFICATION REQUIRED (bu turda 3.2 değişiklik listesi çekilmemiştir) | [S2], [S1] |
| Resmi HTML yayımlama | `spec.openapis.org/oas/v3.1.x.html` | `spec.openapis.org/oas/v3.2.1.html` | [S2] |
| Depo sürümleri | `oai/openapi-specification/versions/3.1.1.md` | ⚠️ VERIFICATION REQUIRED (3.2 dosyası bu turda okunmadı) | [S3] |

> **Truth notu:** 3.2.x'in 3.1.x'e göre hangi alanları eklediğini bu turda
> **çekmedik**; uydurmak yerine `⚠️ VERIFICATION REQUIRED` yazıldı.

---

## §3 Belgеnin Üst Düzlem Alanları (Root Object)

Bir OpenAPI dokümanının kökünde şunlar bulunur:

| Alan | Tip | Zorunlu mu | Ne işe yarar | Kanıt |
|---|---|---|---|---|
| `openapi` | string | 3.x'te **ZORUNLU** | Şartname sürümü (ör. `3.1.1`) | [S2] |
| `info` | object | **ZORUNLU** | `title` + `version` zorunlu; `description`, `termsOfService`, `contact`, `license` opsiyonel | [S2] |
| `jsonSchemaDialect` | string | opsiyonel | Belgelerde varsayılan JSON Schema dialect'i (3.1) | [S2] |
| `servers` | array | opsiyonel | Sunucu taban URL'leri, sunucu değişkenli | [S2] |
| `paths` | object | 3.0'da zorunlu (bkz. §3.1) | Yol → işlem eşlemesi | [S2] |
| `webhooks` | object | opsiyonel | Gelen olay bildirimleri (3.1+) | [S9] |
| `components` | object | opsiyonel | Yeniden kullanılabilir parçalar (bkz. §7) | [S2] |
| `security` | array | opsiyonel | Belge geneli güvenlik gereksinimleri | [S2] |
| `tags` | array | opsiyonel | İşlem gruplandırma | [S2] |
| `externalDocs` | object | opsiyonel | Dış doküman başvurusu | [S2] |

### §3.1 `paths` her zaman zorunlu mu?

**Hayır — 3.1 ile.** Spesifikasyonda "zorunlu alanlar" tablosunda `paths`,
3.1 sürümü için zorunlu olmaktan çıkmış ve `webhooks` eklenmiştir. Yani
yalnızca gelen olayları tarif eden bir belge `paths` içermeyebilir.
(Kaynak: [S2])

> ⚠️ **VERIFICATION REQUIRED:** `paths`'in 3.0.x'te zorunlu olup olmadığı
> iddiası bu turda 3.0.x metninden **okunmadı**; 3.1 metni üzerinden
> "3.1'de zorunlu değil" denmektedir.

### §3.2 `info` zorunlu alt alanları

| Alan | Zorunlu | Not |
|---|---|---|
| `title` | **Evet** | İnsan-okur ad |
| `version` | **Evet** | **Belgenin** sürümü — API sürümünden ve OAS sürümünden **farklıdır** |
| `summary` | Hayır | Kısa özet (3.1) |
| `description` | Hayır | CommonMark zengin metin |
| `termsOfService` | Hayır | URI biçiminde olmalı |
| `contact` | Hayır | İletişim nesnesi |
| `license` | Hayır | Lisans nesnesi |

**Üç sürüm aynı addadır, birbirine karıştırılmaz:**

| Sürüm | Nerede | Örnek |
|---|---|---|
| OpenAPI Specification sürümü | kök `openapi` alanı | `3.1.1` |
| API'nin kendi sürümü | `info.version` | `2.4.0` |
| Belgenin sürümü | `info.version` (aynı alan, farklı anlam bağlamı) | ⚠️ Belge/API sürümü ayrımı için resmi terim → UNKNOWN |

> **Truth notu:** Belge sürümü ile API sürümünün ayrı ayrı yazıldığı bir
> standart alan (ör. `info.documentVersion`) **spec'te yoktur**; bu ayrım
> ekiplerce `description` içine not edilir. Standart alan var iddiası
> **uydurmadır**, yazılmadı.

---

## §4 Yol Yapısı — `paths` ve `path item`

| Kavram | Tanım | Kanıt |
|---|---|---|
| Yol anahtarı | Baş `/` ile başlayan URI yolu; sunucu tabanı üzerine eklenir | [S2] |
| Yol parametresi | `{messageId}` gibi süslü parantezli bölge | [S2] |
| Path Item | O yola ait `get/post/put/delete/patch/...` işlemleri + ortak `parameters` | [S2] |
| Dış yola referans | `$ref` ile başka bir `paths` girdisine | [S2] |

### §4.1 HTTP işlem anahtarları

| Anahtar | Tip |
|---|---|
| `get`, `put`, `post`, `delete`, `options`, `head`, `patch`, `trace` | Operation nesnesi |

> **Yalnızca bu sekiz.** `GET`/`get` büyük-küçük harf duyarlıdır; OpenAPI
> anahtarları **küçük harftir**. Büyük harfle `GET:` yazmak geçersiz bir
> anahtardır ve araçlarca reddedilir. (Kaynak: [S2])

### §4.2 Seviye sıralaması (override zinciri)

Bir `parameter` tanımı üç seviyede tanımlanabilir ve **daralan öncelik**
sırası şöyledir:

| Seviye | Nerede | Kapsamı |
|---|---|---|
| 1 | `paths./yol.parameters` (Path Item) | O yoldaki **tüm** işlemler |
| 2 | `components.parameters.X` + `$ref` | Belge geneli yeniden kullanım |
| 3 | `operation.parameters` | **Yalnız** o işlem |

**Operation'daki tanımlar, aynı (`name`, `in`) çifti için Path Item'dakini
ezmez — *birleştirme* (merge) yapılır; çakışırsa operation'ınki baskın gelir.**
(Kaynak: [S2])

---

## §5 `components` — Yeniden Kullanım Düzlemi

`components`, belge içinde `$ref` ile çağrılabilen parçaların deposudur.
Aşağıdaki alt anahtarlar standarttır (tam liste için [S2]):

| Alt anahtar | Ne saklar |
|---|---|
| `schemas` | JSON şemaları |
| `responses` | Yanıt tanımları |
| `parameters` | Parametre tanımları |
| `examples` | Örnek değerler |
| `requestBodies` | İstek gövdeleri |
| `headers` | Başlık tanımları |
| `securitySchemes` | Güvenlik şemaları (bkz. §6) |
| `links` | Yanıt içinde gezinme bağlantıları |
| `callbacks` | Geri çağırma (webhook) tanımları |
| `pathItems` | Yeniden kullanılan yollar (3.1) |

### §5.1 `$ref` kuralları

| Kural | Değer | Kanıt |
|---|---|---|
| Sözdizimi | RFC 6901 JSON Pointer: `#/components/schemas/Track` | [S7] |
| Bağlam dışı referans | Tam URI ile başka belgeye (`./common.yaml#/...`) | [S2] |
| Birleştirme | `$ref` olan nesnede **yalnızca `$ref`** bulunur; kardeş anahtarlarla merge edilmez (3.1 kuralı) → ⚠️ detay için 3.1 metnine bakınız | [S2] |
| Döngü (cycle) | Şema kendi kendine referans verebilir (özyinelemeli tipler) | [S6] |

**Döngü tehlikesi:** `$ref` zinciri döngüye girerse belge çözülemez.
Araçlar bu durumu ya hata verir ya da sınırsız döngüde kalır. CoreMusic
tarafında döngü denetimi bir **CI kapısı** olmalıdır (bkz.
`openapi-dogrulama.md`).

---

## §6 Güvenlik Düzlemi — `securitySchemes`

Kök `security` alanı belge geneli gereksinimdir; `components.securitySchemes`
ise şemaların tanımlandığı yerdir.

| Şema tipi | Ne taşır | Tipik alanlar |
|---|---|---|
| `apiKey` | Başlık/çerez/query'de anahtar | `name`, `in` |
| `http` | HTTP autentikasyon şeması | `scheme`, `bearerFormat` |
| `mutualTLS` | Karşılıklı TLS (3.1) | — |
| `oauth2` | OAuth 2.0 akışları | `flows` |
| `openIdConnect` | OpenID Connect discovery URI | `openIdConnectUrl` |

### §6.1 OAuth 2.0 akışları

| Akış | Alan adı |
|---|---|
| Authorization Code | `authorizationCode` |
| Implicit | `implicit` |
| Client Credentials | `clientCredentials` |
| Password | `password` |

**OAuth 2.0 akışlarının tek tek kapsamı ve CoreMusic'in hangi akışı
kullanacağı → UNKNOWN** (bu turda `.ai/.decisions` içinde OAuth akış kararı
okunmadı; uydurulmadı).

### §6.2 Güvenlik gereksinim mantığı

| Yapı | Anlamı |
|---|---|
| Bir şemanın boş `{}` değeri | "Kimlik gerekmez" (anonim erişim) |
| Aynı satırda iki şema | **VE** — ikisi de sağlanmalı |
| Farklı satırlarda şemalar | **VEYA** — herhangi biri yeterli |

> Bu mantık **AND/OR** semantiğidir; sıklıkla ters yorumlanır.
> (Kaynak: [S2])

---

## §7 Bileşen Düzlemi Özeti — `openapi-sozlesme-dili.md`'e köprü

| Bölüm | Nerede işlenir |
|---|---|
| `paths` + `operation` + `parameter` | `openapi-sozlesme-dili.md` §1–§4 |
| `requestBody` + `response` + `mediaType` | `openapi-sozlesme-dili.md` §5–§7 |
| `securitySchemes` (bu dosya §6) | uygulama kararı → `openapi-dogrulama.md` §6 |
| `schemas` / JSON Schema | `openapi-dogrulama.md` §1–§3 |
| Doğrulama, lint, CI kapısı | `openapi-dogrulama.md` |
| Gerçek YAML/PHP/JS | `openapi-kod-ornekleri.md` |
| URL kanıtı | `openapi-web-arastirma.md` |

---

## §8 Zaman Çizelgesi (kanıtlı satırlar)

| Dönem | Olay | Kanıt |
|---|---|---|
| ~2011 | "Swagger" adıyla doğar | [S3] |
| 2015 | OpenAPI Initiative kurulur; spec OpenAPI adını alır | [S3] |
| 3.0 | Yapısal yeniden yazım; `components` düzlemi | [S1] |
| 3.1 | JSON Schema uyumu | [S1] |
| 3.1.1 | Depoda `versions/3.1.1.md` | [S3] |
| 3.2.0 | 2025-09-19 tarihli yayımlama | [S1] |
| 3.2.1 | En güncel listelenen sürüm (2026-10-12 ölçümü) | [S1] |

> **Yıl/iddia ayrımı:** OpenAPI Initiative'in kuruluş yılının 2015 olduğu
> bu turda **resmi bir sayfadan doğrulanmadı** → `⚠️ VERIFICATION REQUIRED`.

---

## §9 CoreMusic'te Bu Teknolojinin Rolü (tasarım iddiası — kanıt değil)

> Bu bölüm **tasarım iddiasıdır**; repo kanıtı `openapi-kod-ornekleri.md`
> §Kanıt Durumu'nda aranır.

| Kullanım | Beklenen yer | Durum |
|---|---|---|
| Dış API'lerin sözleşmesi (üretici) | K013 / K012 integration-service | ⚠️ VERIFICATION REQUIRED |
| İç servisler arası kontrat | K011-application | ⚠️ VERIFICATION REQUIRED |
| CI'da sözleşme doğrulaması | `.github/workflows/` | ⚠️ VERIFICATION REQUIRED (workflow dosyaları mevcut, içerik bu turda OpenAPI açısından okunmadı) |
| Doküman üretimi | statik site / dahili portal | UNKNOWN |

**Kapsam dışı (bu dosyada işlenmez):** gRPC/protobuf (`protocol-adapters`),
webhook olay şeması (`event-contracts`), SDK üretimi, araç kurulumu.

---

## §10 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **OAS** | OpenAPI Specification kısaltması |
| **OAD** | OpenAPI Description — spesifikasyonun tarif ettiği belge biçimi |
| **Operation** | Bir HTTP method + yoldan oluşan tek bir işlem |
| **Path Item** | Bir yola ait tüm işlemlerin kabı |
| **Media Type** | `application/json` gibi içerik tipi + şema eşlemesi |
| **Dialect** | JSON Schema'da hangi vocabulary kümesinin kullanıldığı |
| **Bundle** | Çok parçalı belgeyi tek dosyada birleştirme işlemi |
| **Override** | Seviyeler arası öncelik (operation > path > component) |

---

## §11 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| "Swagger = OpenAPI" | Swagger 2.0 **sürümün** adı; OpenAPI **şartnamenin** adı |
| `info.version` = API sürümü mü, belge sürümü mü? | Aynı alan yazılır; ayrım standart alanda **değildir** (bkz. §3.2) |
| `components.schemas` = veritabanı şeması | Hayır; **istek/yanıt** veri modeli. DB şeması K007-data'dadır |
| `security` = yetkilendirme (authorization) | `security` **autentikasyon** gereksinimidir; yetki/rol ayrı katmandır |
| Belge geçerli = API doğru | Hayır; spec uyumu ile davranış uyumu ayrı testtir |

---

## §12 Kapsam Dışı (bu dosyada işlenmez)

1. `openapi-sozlesme-dili.md` — operation/parameter/response ayrıntısı
2. `openapi-dogrulama.md` — JSON Schema, lint, CI
3. `openapi-kod-ornekleri.md` — gerçek örnekler
4. `openapi-web-arastirma.md` — tam URL kanıtı
5. Vendor araçlar (Swagger UI, Redoc, Spectral vb.) — adları burada
   **listelenmedi**, çünkü araç envanteri bu turda çekilmedi → UNKNOWN
6. GraphQL/gRPC — `K013/protocol-adapters` kapsamı

---

## §13 Açık Sorular (özet — tam liste: `openapi-web-arastirma.md` §Açık Sorular)

| # | Soru | İşaret |
|---|---|---|
| Q1 | CoreMusic hedef sürümü 3.1.x mi 3.2.x mi? | UNKNOWN |
| Q2 | Repo'da OpenAPI belgesi var mı, nerede? | ⚠️ VERIFICATION REQUIRED |
| Q3 | Doğrulama üretimde mi testte mi? | UNKNOWN |
| Q4 | K013 mü yazar, K012 mi tüketir? | ⚠️ VERIFICATION REQUIRED |
| Q5 | 3.2.x 3.1.x'e ne ekledi? | ⚠️ VERIFICATION REQUIRED (çekilmedi) |
| Q6 | `paths`'in 3.0.x'te zorunluluğu? | ⚠️ VERIFICATION REQUIRED (3.0 metni okunmadı) |

---

## §14 Okuma Haritası — 5 Dosyalık Seri

| # | Dosya | Ne verir |
|---|---|---|
| 1 | **`openapi-anayasa.md`** (bu dosya) | Kimlik, sürüm, üst düzlem, `components`, güvenlik |
| 2 | `openapi-sozlesme-dili.md` | Bir uç nokta nasıl tarif edilir |
| 3 | `openapi-dogrulama.md` | Nasıl yakalanır (JSON Schema + CI) |
| 4 | `openapi-kod-ornekleri.md` | Gerçek örnekler ve kanıt durumu |
| 5 | `openapi-web-arastirma.md` | Her iddianın arkasındaki URL |

---

## §15 Hiyerarşi Örneyi (nadir kod bloğu — belge şekli)

```yaml
openapi: "3.1.1"            # şartname sürümü (kök, 3.x'te zorunlu)
info:
  title: "CoreMusic Dış API" # zorunlu
  version: "1.0.0"           # zorunlu — API/belge sürümü
servers:
  - url: "https://api.example.invalid"
paths:
  /v1/tracks/{trackId}:
    parameters:
      - $ref: "#/components/parameters/TrackId"
    get:
      operationId: getTrack
      responses:
        "200":
          description: "İşlem başarılı"
components:
  parameters:
    TrackId:
      name: trackId
      in: path
      required: true
      schema:
        type: string
```

> **Not:** `api.example.invalid` **kasıtlı olarak geçersiz bir alandır** —
> örnek belgede gerçek bir uç nokta **uydurulmaz**.

---

## §16 `servers` — Taban URL ve Sunucu Değişkenleri

Bir belgedeki herhangi bir yol anahtarı (`/v1/tracks`), `servers` içindeki
tablana **ön eklenerek** tam URI'ye dönüşür. `servers` yoksa yollar
bağıl olarak okunur → araçlar bu durumu genelde hata sayar.

| Alan | Tip | Açıklama | Kanıt |
|---|---|---|---|
| `url` | string | Sunucu tabanı. Değişken içerebilir | [S2] |
| `description` | string | İnsan-okur açıklama | [S2] |
| `variables` | object | `url` içindeki `{değişken}` adlarının karşılığı | [S2] |

### §16.1 Sunucu değişkeni alanları

| Alan | Zorunlu | Açıklama |
|---|---|---|
| `default` | **Evet** | Değişkenin varsayılan değeri |
| `enum` | Hayır | İzin verilen değer kümesi |
| `description` | Hayır | Açıklama |

**Zorunlu alan `default`'tur.** `enum` verilmişse `default` listeden bir
olmalıdır; verilmemişse araç varsayılanı kullanır. (Kaynak: [S2])

```yaml
servers:
  - url: "https://{region}.api.example.invalid/{basePath}"
    variables:
      region:
        default: "eu"          # zorunlu
        enum: ["eu", "us", "ap"]
        description: "Veri merkezi bölgesi"
      basePath:
        default: "v1"
```

> `api.example.invalid` **kasıtlı geçersiz alandır** — gerçek uç nokta
> uydurulmaz.

---

## §17 `tags` ve `externalDocs` — Düzen Düzlemi

| Alan | Ne yapar | Etkisi var mı? |
|---|---|---|
| `tags[].name` | İşlemleri gruplar (dokümanda bölüm başlığı olur) | Semantik — davranış değiştirmez |
| `tags[].description` | Grup açıklaması | Hayır |
| `tags[].externalDocs` | Gruba özel dış doküman | Hayır |
| Operation'daki `tags` | İşlemin hangi gruba girdiği | Hayır |
| `externalDocs.url` | Belge geneli dış doküman | Hayır |

**Önemli ayrım:** `tags` **yalnızca gösterim ve gruplamadır**; bir
işlemin davranışını, yetkisini veya şemasını **değiştirmez**. Etiket
eklemek API'yi kırmaz; çıkarmak doküman düzenini değiştirir.
(Kaynak: [S2])

---

## §18 `deprecated` ve Uyumluluk Sinyalleri

| Alan | Nerede | Etki |
|---|---|---|
| `operation.deprecated: true` | Tek işlem | Araçlar işlemi **üstü çizili** gösterir |
| `info.termsOfService` | Kök | Hukuksal metin — teknik etkisi yok |

**OpenAPI, sürüm kırılma (breaking change) kurallarını kendisi
tanımlamaz.** Ne "zorunlu alan eklenirse kırılır" ne de "alan silinirse
kırılır" gibi bir kural spec'te yoktur. Kırılma tanımı **dış bir
sözleşmeye** (ekibin/şirketin kuralına) bağlıdır → bu karar CoreMusic'te
**`UNKNOWN`** ve ADR beklemektedir.

> **Uydurma yasak:** "OpenAPI'ye göre `required` eklemek breaking'tir"
> gibi bir cümle **spec'te yoktur**, yazılmamıştır.

---

## §19 Belgе Dağıtımı — Tek Dosya mı, Bundle mı?

| Model | Yapı | Artısı | Eksiği |
|---|---|---|---|
| Tek dosya | Her şey kökte | Basit, her araç okur | Birim yeniden kullanılamaz |
| Bağlamalı (bundle) | `$ref` ile birden çok dosya → tek çıktıya derlenir | Ekip çalışması, tekilleştirme | Derleme adımı gerekir |
| Paylaşılan (`$ref` uzak) | Dosyalar doğrudan dış URI'ye referans verir | Tekesthizalı paylaşım | Ağ bağımlılığı, çözümleme maliyeti |

**Karar noktası:** Kaç belge, kim sahip, bundle nerede koşuyor → bu dosyanın
kapsamı **dışıdır** ve `UNKNOWN` işaretlenmiştir. Doğrulama/CI tarafı
`openapi-dogrulama.md` §5'tedir.

---

## §20 `callbacks` — Yan İş Yönü (Outbound)

Bir operasyon, başarı durumunda sunucunun **kendisi** geri çağırma
( webhook benzeri ) yapacaksa `callbacks` kullanır:

| Alan | Açıklama |
|---|---|
| `callbacks.{ad}` | Bir olay adı → Runtime Expression anahtarı |
| Anahtar değeri | `{$request.body#/callbackUrl}` gibi **runtime expression** |
| Değer | Normal bir Path Item (`post` + `requestBody` + `responses`) |

**`callbacks` = yan iş (sunucu → istemci).** `webhooks` (3.1 kök alanı) ise
**gelen** bildirimlerdir. İkisi sık karıştırılır:

| | `components.callbacks` | kök `webhooks` |
|---|---|---|
| Yön | Bu API'nin **çağırdığı** | Bu API'ye **gelen** |
| Nerede | Operation içinde | Kök seviye |
| Uygulama | Operasyon bağlamında tanımlı | Bağımsız |

(Kaynak: [S2], [S9])

---

## §21 Yanıt Kodu Anahtarları — Semantik ve `default`

| Anahtar | Tip | Açıklama |
|---|---|---|
| `"200"`, `"404"` | Tam durum kodu | Belirli duruma karşılık |
| `"2XX"`, `"4XX"` | Aralık | Aynı kod ailesine sahip tüm durumlar |
| `default` | Özel anahtar | Hiçbiri eşleşmeyen durumlar |
| `description` | string | **Her yanıt için zorunlu** |

**`default` bir HTTP durum kodu değildir**; yakalayıcı (catch-all)dır.
`description` her yanıt nesnesinde **zorunludur** — yoksa belge geçersizdir.
(Kaynak: [S2])

---

## §22 İlgili Düğümler — Ağaçtaki Yeri

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion/api-contracts` | Bu teknoloji o rolün içindedir |
| ↑ | `K013-integraion` | Dış dünya köprüsü |
| ↑ | `K009-security` | `securitySchemes` için kimlik/kripto katmanı |
| ↑ | `K010-network` | HTTP taşıma katmanı |
| ↓ | `K012-services` | Kontratı tüketen servis |
| ↓ | `K011-application` | Uygulama akışının dış çağrısı |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | Spec sürümleri listesi (3.2.1 … 2.0) + OAS tanımı | https://spec.openapis.org/oas/ | 2026-10-12 | ✅ çekildi |
| S2 | OAS v3.2.1 resmi metni — üst düzlem alanları, `paths`/`webhooks`, `info` zorunlu alanları, `components`, `security` | https://spec.openapis.org/oas/v3.2.1.html | 2026-10-12 | ✅ çekildi |
| S3 | Sürüm dosyaları deposu (`versions/3.1.1.md`) + "Swagger → OpenAPI" adı | https://github.com/oai/openapi-specification/blob/main/versions/3.1.1.md | 2026-10-12 | ✅ çekildi |
| S4 | OpenAPI Initiative'e giriş ve öğrenme kaynağı | https://learn.openapis.org/ | 2026-10-12 | ✅ çekildi |
| S5 | Swagger.io resmi şartname sayfası | https://swagger.io/specification/ | 2026-10-12 | ✅ çekildi |
| S6 | JSON Schema spesifikasyonu (Core/Validation, meta-schema) | https://json-schema.org/specification | 2026-10-12 | ✅ çekildi |
| S7 | RFC 6901 — JSON Pointer (`$ref` sözdizimi) | https://datatracker.ietf.org/doc/rfc6901/ | 2026-10-12 | ✅ çekildi |
| S8 | RFC 9111 — HTTP önbellekleme (etag/`Cache-Control` yanıt başlıkları) | https://www.rfc-editor.org/rfc/rfc9111.html | 2026-10-12 | ✅ çekildi |
| S9 | Webhook örneği (OpenAPI 3.1 `webhooks`) | https://learn.openapis.org/examples/v3.1/webhook-example.html | 2026-10-12 | ✅ çekildi |
| S10 | 3.1.1 metni (depo) | https://swagger.io/specification/v3.1/ | 2026-10-12 | ✅ çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `⚠️ VERIFICATION REQUIRED` işaretli satırlar için **kaynak yoktur** ve
> dayanak olarak kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · OpenAPI Initiative · **Tarih: 2026-10-12**
