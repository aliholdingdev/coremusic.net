---
node_id: K013-integraion/api-contracts/openapi/openapi-sozlesme-dili
node_type: module
title: "OpenAPI Sözleşme Dili — Belgé Yapısı, Yol/İşlem/Parametre/Yanıt Düzlemi"
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
group: mimari
---

# OpenAPI Sözleşme Dili — Belgé Yapısı ve Bileşen Düzlemi

> **Bu dosya ne değildir:** Kod, CLI rehberi veya araç kurulumu değildir.
> Bu dosya OpenAPI dokümanının **yapısını** — kök alanlar, yol yapısı,
> işlem nesnesi, parametre, istek gövdesi, yanıt, components, `$ref`,
> şema bileşimi ve sürüm farkını — *belge kanıtıyla* tanımlar; sürüm ve
> terim iddiaları `spec.openapis.org` / `swagger.io` sayfalarından alınmıştır.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod ⚠️ PLANNED
> (CoreMusic repo'sunda `openapi.yaml`/`.json` çıktısı bu turda tespit edilmedi).

---

## §0 ELI15 — Bu dosya ne işe yarar?

Bir OpenAPI dosyası, bir klasör ağacı gibi katmanlıdır: en üstte belgenin
kimliği (`openapi` + `info`), altında sunucu adresleri (`servers`), sonra
 uç noktalar (`paths`) ve her ucun girdi/çıktı tarifi (parametre, istek
gövdesi, yanıt) yer alır; tekrarlanan parçalar `components` altında
saklanır ve `$ref` ile oradan çağrılıp `allOf`/`oneOf`/`anyOf` gibi
bileşim anahtarlarıyla birleştirilir — bu dosya her birini tek tek tarif
eder ve 3.0 ile 3.1 arasındaki dil farkını gösterir.
(Kaynak: §Kaynak Tablosu [D1], [D2], [D3])

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | openapi-anayasa.md | Kimlik, tarihçe, sürümler, belge yapısı özeti | 3 |
| mimari | openapi-sozlesme-dili.md | Path/operation/parameter/requestBody/response/security + `$ref`/bileşim/sürüm farkı (bu dosya) | 3 |
| kod | openapi-dogrulama.md | Doğrulama katmanları: şema, referans, örnek, CI kapısı | 3 |
| kod | openapi-kod-ornekleri.md | Gerçek YAML/PHP/JS örnekleri, repo kanıtlı | 3 |
| kanit | openapi-web-arastirma.md | Benzersiz resmi URL + iddia/URL/tarih/durum tablosu | 3 |

---

## §1 Belgé Kök Nesnesi — Bir OpenAPI dosyasının üst düzey alanları

| Alan | Zorunlu mu | Ne taşır | Kanıt |
|---|---|---|---|
| `openapi` | Evet | Şartnamenin sürümü (örn. `3.1.1` — açık bir semver string'i) | [D1] |
| `info` | Evet | Belgé kimliği: `title`, `version`, `description` | [D1] |
| `servers` | Hayır | Sunucu taban URL'leri ve değişkenleri | [D4] |
| `paths` | Evet* | Yol → Path Item eşlemesi | [D1] |
| `webhooks` | Hayır (3.1+) | Dışarı çağrılan (outbound) webhook tanımları | [D1] |
| `components` | Hayır | Yeniden kullanılan şema/parametre/yanıt/güvenlik tanımları | [D1] |
| `security` | Hayır | Belge geneli güvenlik gereksinimleri | [D1] |
| `tags` | Hayır | Etiket tanımı ve dış doküman bağlantısı | [D5] |
| `externalDocs` | Hayır | Dış doküman referansı | [D5] |
| `jsonSchemaDialect` | Hayır (3.1+) | Varsayılan JSON Schema dialect URI'si | [D1] |

\* `paths` ve `webhooks` en az birinin bulunması gerekir — belge "sadece
webhook" tanımlayabilir; bu kural `spec.openapis.org` 3.1 metninde yazılıdır.
(Kaynak: [D1])

**Uzantı alanı:** `x-` önekiyle başlayan anahtarlar her nesnede serbesttir;
araçlar bilinmeyen `x-` anahtarlarını yok sayar (genişletilebilirlik
kanalı). (Kaynak: [D1])

---

## §2 `info` — Belgé Kimlik Düzlemi

| Alan | Tip | Zorunlu | Ne işe yarar | Kanıt |
|---|---|---|---|---|
| `title` | string | Evet | Belgé adı | [D1] |
| `summary` | string | Hayır (3.1+) | Tek cümlelik özet | [D1] |
| `description` | string | Hayır | Uzun açıklama (Markdown destekli) | [D1] |
| `termsOfService` | string (URI) | Hayır | Kullanım koşulları | [D1] |
| `contact` | object | Hayır | Ad/e-posta/URL | [D1] |
| `license` | object | Hayır | Lisans adı + URI | [D1] |
| `version` | string | Evet | Belgé sürümü (açık semver değildir — serbest string) | [D1] |

> **İki sürüm kavramı karıştırılmamalı:** `openapi` alanı *şartnamenin*
> sürümünü (3.0.4 / 3.1.1 / 3.2.0) taşır; `info.version` ise *API'nin kendi*
> sürümünü (ör. `1.4.2`) taşır. İkisi bağımsızdır. (Kaynak: [D1], [D2])

---

## §3 `servers` — Taban URL ve Sunucu Değişkenleri

| Alan | Ne taşır | Kanıt |
|---|---|---|
| `servers[].url` | Taban URL; `{değişken}` yer tutucuları destekler | [D4] |
| `servers[].description` | İnsan-okur açıklama | [D4] |
| `servers[].variables` | Değişken adı → `enum`/`default`/`description` | [D4] |

Sunucu tanımları belge, yol veya işlem düzeyinde tekrarlanabilir — işlem
düzeyindeki tanım üsttekini geçersiz kılar. Yol düzeyi `servers` bir
`$ref` zinciri içinde çözülemez (3.0 kısıtı; 3.1'de `$ref` her yerde
serbesttir — bkz. §11). (Kaynak: [D4], [D1])

**CoreMusic bağlanması (tasarım iddiası — kanıt değil):** Repo'daki route
tabloları sabit `/api/v1/auth` yolları kullanır; `servers` tanımı varsa
`https://api.coremusic.net` tabanını taşımalıdır — bu bir *tasarım iddiasıdır*,
repo'da `openapi.yaml` tespit edilmediği için ⚠️ VERIFICATION REQUIRED.
`Dosya: api.coremusic.net/config/routes.php / Satır: 25` (`'GET' => [` bloğu
sabit yolları başlatır — gerçek disk okuması, 2026-10-13).

---

## §4 `paths` — Yol Yapısı ve Path Item

| Alan | Ne taşır | Kanıt |
|---|---|---|
| `paths` | Yol şablonu → Path Item nesnesi eşlemesi | [D1] |
| Path Item anahtarları | `get`/`put`/`post`/`delete`/`options`/`head`/`patch`/`trace` + `$ref` + `summary`/`description`/`servers` | [D1] |

Yol anahtarları `/` ile başlamalıdır ve templating destekler:
`/users/{userId}` — `{userId}` sonradan `parameters` ile tanımlanır.
Yolun kendisi **yöntemden bağımsızdır**; aynı yol birden çok metod
içerebilir. (Kaynak: [D1])

**CoreMusic karşılığı (repo kanıtı):** Aynı prensip repo'da uygulanır —
`/api/v1/auth/me` yolu tek bir kayıt içinde `GET` bloğunda,
POST-only uçlar `POST` bloğunda durur; metod ayrımı tablonun
kendisindedir, yolun değil. `Dosya: api.coremusic.net/config/routes.php /
Satır: 42` (`'/api/v1/auth/me' => [` — gerçek disk okuması, 2026-10-13).
Ayrıca `Dosya: shared/src/Api/Routing/RouteTable.php / Satır: 28-30`
(`STATUS_OK` / `STATUS_METHOD_NOT_ALLOWED` / `STATUS_NOT_FOUND` sabitleri
— metod-uyumsuzluğu ile bulunmazlığın ayrıldığı gerçek satırlar).

---

## §5 İşlem Nesnesi (Operation Object) — HTTP Metodu Ne Taşır?

Her metod anahtarı (`get`, `post`, …) şu alanları taşır:

| Alan | Zorunlu | Ne taşır | Kanıt |
|---|---|---|---|
| `tags` | Hayır | Etiket listesi | [D1] |
| `summary` | Hayır | Tek satır özet | [D1] |
| `description` | Hayır | Uzun açıklama (Markdown) | [D1] |
| `externalDocs` | Hayır | Dış doküman | [D5] |
| `operationId` | Hayır | Belge genelinde benzersiz işlem adı | [D1] |
| `parameters` | Hayır | Parametre nesneleri dizisi | [D1] |
| `requestBody` | Hayır | İstek gövdesi tanımı | [D1] |
| `responses` | Evet | Durum kodu → Response nesnesi eşlemesi | [D1] |
| `callbacks` | Hayır | Geri-çağırım (inbound webhook) tanımı | [D1] |
| `deprecated` | Hayır | `true` ise işlem bayatlamıştır | [D5] |
| `security` | Hayır | İşlem düzeyi güvenlik gereksinimi (belge genelini ezer) | [D1] |
| `servers` | Hayır | İşlem düzeyi sunucu (üsttekini ezer) | [D4] |

**İşlem nesnesi tek başına bir HTTP isteğinin *sözleşmesidir*:** girdi
(parametre + gövde), çıktı (yanıt) ve kimlik doğrulama (security) — üçü
bir arada. (Kaynak: [D1], [D3])

---

## §6 `operationId` — Tek Adres Taşıyan İsim

| Kural | Durum | Kanıt |
|---|---|---|
| Belge genelinde benzersiz olmalı | Zorunlu (şartnamede açık yazar) | [D1] |
| Kod üretiminde fonksiyon adı kaynağı | Araç davranışı (generator'lar adı fonksiyona çevirir) | [D8] |
| Tekrar eden `operationId` | Belge geçersizdir — statik araçlar hata verir | [D8], [D10] |

`operationId` yoksa araçlar yolu/fonksiyon adını kendisi türetir (daha
kırılgan). API-üretim (codegen) akışında `operationId` zorunlu Practicedir.
(Kaynak: [D8])

> **Gerçek repo bağlantısı (repo kanıtı, uydurma satır yok):** Repo'daki
> karşılığı `action` anahtarıdır — `Dosya: api.coremusic.net/config/routes.php /
> Satır: 45` (`'action' => 'me'` — gerçek disk okuması, 2026-10-13).
> `action`, controller dispatch anahtarıdır; OpenAPI belgesi üretilirse
> `operationId` bu alanla eşleştirilmelidir (tasarım önerisi).

---

## §7 `parameters` — Parametre Düzlemi

| Alan | Zorunlu | Değerler / Not | Kanıt |
|---|---|---|---|
| `name` | Evet | Parametre adı | [D1] |
| `in` | Evet | `path` · `query` · `header` · `cookie` | [D1] |
| `description` | Hayır | Açıklama | [D1] |
| `required` | Hayır | `path` için **her zaman `true`** olmalı (şartname kuralı) | [D1] |
| `deprecated` | Hayır | Bayat sinyali | [D5] |
| `allowEmptyValue` | Hayır | Yalnızca `query`/`cookie` | [D1] |
| `style` · `explode` | Hayır | Serileştirme stili (bkz. §16) | [D4] |
| `allowReserved` | Hayır | Rezerve URI karakterleri serbest mi | [D1] |
| `schema` | Hayır | Tip/limit şeması | [D1] |
| `example` / `examples` | Hayır | Tekil / çoklu örnek | [D1] |
| `content` | Hayır | `schema` yerine medya-tipi içerik (tek alternatif) | [D1] |

`content` ile `schema` aynı parametrede birlikte yazılamaz — ikisi
birbirinin alternatifidir; `style`/`explode` yalnız `schema` ile
kullanılır. (Kaynak: [D1])

**CoreMusic bağlanması (repo kanıtı):** RequestValidationMiddleware'in
okuduğu `validation` anahtarı parametre doğrulama kural setinin repo'daki
karşılığıdır: `Dosya: api.coremusic.net/config/routes.php / Satır: 14`
(`*   validation        : RequestValidationMiddleware kural seti (422 +
error.fields)` — gerçek disk okuması, 2026-10-13).

---

## §8 `requestBody` — İstek Gövdesi

| Alan | Zorunlu | Ne taşır | Kanıt |
|---|---|---|---|
| `description` | Hayır | Açıklama | [D1] |
| `content` | Evet | Medya tipi (`application/json` …) → Media Type Object | [D1] |
| `required` | Hayır | Gövde zorunlu mu (varsayılan `false`) | [D1] |

`requestBody` yalnızca gövde taşır; sorgu/yol/header parametreleri
`parameters` kanalındadır. `content` anahtarı boş bırakılamaz — medya
tipi en az bir olmalıdır. (Kaynak: [D1])

`GET`/`DELETE` işlemlerinde `requestBody` kullanılması tartışmalıdır;
şartname yasaklamaz ancak aracı-uyumluluğu için kaçınılır — bu araç-uyum
iddiası ⚠️ VERIFICATION REQUIRED (bu turda hangi aracın ne yaptığı
çekilmedi). (Kaynak: [D1] — yalnız `requestBody` tanımlaması çekildi.)

---

## §9 `responses` — Yanıt Düzlemi

| Alan | Zorunlu | Ne taşır | Kanıt |
|---|---|---|---|
| Anahtarlar | Evet | Durum kodu (`200`, `404` …) veya `default` | [D1] |
| `description` | Evet | Yanıt açıklaması (her yanıtta zorunlu) | [D1] |
| `headers` | Hayır | Dönen başlıklar | [D1] |
| `content` | Hayır | Medya tipi → Media Type Object | [D1] |
| `links` | Hayır | Sonraki işlem bağlantısı (HATEOAS-benzeri) | [D1] |

`default`, tanımlanmayan tüm durum kodları için yedektir; belgede hem
kod-anahtarı hem `default` varsa kod-anahtarı önceliklidir. (Kaynak: [D1])

**Repo karşılığı (kanıt):** RouteTable üç durumu `STATUS_OK` /
`STATUS_METHOD_NOT_ALLOWED` / `STATUS_NOT_FOUND` olarak ayırır ve
`method_not_allowed` durumunda `Allow` başlığı döner — `Dosya:
shared/src/Api/Routing/RouteTable.php / Satır: 28-30` (gerçek disk
okuması, 2026-10-13). Bu üç durum, OpenAPI belgesinde sırasıyla
`200` / `405` (+ `Allow` başlığı) / `404` yanıt tanımlarına karşılık gelir
(tasarım eşlemesi).

---

## §10 `components` — Yeniden Kullanım Düzlemi

| Anahtar | Ne saklar | Kanıt |
|---|---|---|
| `schemas` | Şema (tip) tanımları | [D1] |
| `responses` | Tekrarlanan yanıt tanımları | [D1] |
| `parameters` | Tekrarlanan parametre tanımları | [D1] |
| `examples` | Tekrarlanan örnekler | [D1] |
| `requestBodies` | Tekrarlanan istek gövdeleri | [D1] |
| `headers` | Tekrarlanan başlıklar | [D1] |
| `securitySchemes` | Kimlik doğrulama şemaları | [D1] |
| `links` | Tekrarlanan bağlantılar | [D1] |
| `callbacks` | Tekrarlanan geri-çağırımlar | [D1] |
| `pathItems` | Tekrarlanan yol öğeleri (3.1+) | [D1] |

`components` altındaki anahtarlar `[a-zA-Z0-9._-]` karakter kümesiyle
sınırlıdır; `$ref` ile `/components/{kategori}/{ad}` yoluyla çağrılır.
(Kaynak: [D1])

**Önemli sınır:** `components` yalnızca *tanım deposudur* — tek başına
istek/yanıt üretmez; yalnızca bir yerden `$ref` ile çağrıldığında belgeye
girer. (Kaynak: [D1])

---

## §11 `$ref` Çözümleme — Referans Zinciri

| Kural | Durum | Kanıt |
|---|---|---|
| Biçim | URI referansı (RFC 3986) — göreli veya tam | [D12] |
| Yerel işaretçi biçimi | JSON Pointer (RFC 6901) — `/components/schemas/X` | [D7] |
| 3.0'da `$ref` yanındaki anahtarlar | **Yok sayılır** (şartnamede açık) | [D1] |
| 3.1'de `$ref` yanındaki anahtarlar | JSON Schema 2020-12 uyumu — birlikte geçerli | [D1], [D6] |
| Döngüsel referans | Şartname yasağı yok; araçların davranışı ⚠️ VERIFICATION REQUIRED | [D1] |

**RFC 6901 bağlantı noktası:** `#/components/schemas/Pet` ifadesinde `#`
sonrasının tamamı JSON Pointer'dır; her `/` bir seviye, `~1` ve `~0`
 kaçışları sırasıyla `/` ve `~` demektir. (Kaynak: [D7])

**Çözümleme sırası:** Belge yüklenir → tüm `$ref` değerleri çözülür →
çözülemeyen referans = geçersiz belge (statik araçlar bunu hata sayar,
bkz. `openapi-dogrulama.md` §3). (Kaynak: [D1], [D10])

---

## §12 Şema Bileşimi — `allOf` · `oneOf` · `anyOf` · `not`

| Anahtar | Anlamı | Tipik kullanım | Kanıt |
|---|---|---|---|
| `allOf` | Tüm şemalar **aynı anda** geçerli olmalı | Üst-sınıf şemasına genişletme (miras) | [D3] |
| `oneOf` | Tam olarak **biri** geçerli olmalı | Varyantlar (kimlik kartı vs pasaport) | [D3] |
| `anyOf` | **En az biri** geçerli olmalı | İsteğe bağlı zorunluluklar | [D3] |
| `not` | Şema **geçmemeli** | Dışlama kuralı | [D3] |

Bileşim anahtarları doğrudan bir şemanın içindedir ve `properties` gibi
diğer anahtarlarla karıştırılabilir; JSON Schema 2020-12'de `allOf`
alt-shemalarının `required` birleşimi araç-özel davranıştır — ⚠️
VERIFICATION REQUIRED (bu turda hangi aracın nasıl birleştirdiği
çekilmedi). (Kaynak: [D3], [D6])

---

## §13 `discriminator` — Varyant Ayıracı

| Alan | Zorunlu | Ne işe yarar | Kanıt |
|---|---|---|---|
| `propertyName` | Evet | Varyantı taşıyan alan adı (ör. `petType`) | [D3] |
| `mapping` | Hayır | Değer → şema `$ref` eşlemesi | [D3] |

Kullanım önkoşulu: `discriminator` olan şema, varyant şemalarına
`oneOf`/`anyOf` ile bağlanmalı ve `propertyName`, en az bir varyant
şemasında `required` listesinde bulunmalıdır. (Kaynak: [D3])

`mapping` yoksa araç, şema adını değer olarak alır (imali davranış —
ad-özdeşliği kuralı); bu varsayım kod üretiminde sürpriz yaratır,
`mapping` yazmak pratikte önerilir (araç-özel ⚠️ VERIFICATION REQUIRED).
(Kaynak: [D3])

---

## §14 Sürüm Semantiği — 3.0 ↔ 3.1 Farkı (İki Büyük Kırılma)

| Konu | 3.0.x | 3.1.x | Kanıt |
|---|---|---|---|
| Şema dili | JSON-Schema-*benzeri* (spec kendi dialect'ini tanımlar) | Tam JSON Schema 2020-12 uyumu | [D2], [D6] |
| `null` gösterimi | `nullable: true` anahtarı | `type: ["string", "null"]` (JSON Schema biçimi) | [D2], [D6] |
| `exclusiveMinimum` | `boolean` (`true` = min dahil değil) | `number` (eşik değerin kendisi) | [D6] |
| `example(s)` | Şema içinde `example` | JSON-Schema uyumu için `examples` (dizi) — `example` yan anahtar olarak kalır | [D6] |
| `webhooks` alanı | Yok | Var (kök alan) | [D1] |
| `jsonSchemaDialect` | Yok | Var (kök alan) | [D1] |
| `pathItems` (components) | Yok | Var | [D1] |
| `$ref` yan anahtarlar | Yok sayılır | Geçerli | [D1] |

> **3.2 notu (kanıt sınırı):** `spec.openapis.org` 3.2.x sayfaları
> canlıdır (2026-10-13 HEAD 200 — §Kaynak Tablosu [D2a], [D2b]); ancak
> 3.2'nin 3.1'e eklediği yapısal alanlar bu turda metin olarak
> çekilmemiştir → ⚠️ VERIFICATION REQUIRED (uydurulmadı).

**CoreMusic etkisi (tasarım iddiası):** Şema üretiminde `nullable`
kullanılırsa 3.1 hedefiyle uyumsuzluk doğar; hedef sürüm kararı ADR
bekliyor (`openapi-anayasa.md` açık soruları ile aynı sorun).

---

## §15 Medya Tipleri ve Serileştirme

| Alan | Ne taşır | Kanıt |
|---|---|---|
| Media Type Object `schema` | Gövde/parametre şeması | [D3] |
| Media Type Object `example(s)` | Örnek veri | [D3] |
| `encoding` | Gövde içi alt-parçaların başlık/serileştirme kuralları | [D4] |

Parametre serileştirme `style` + `explode` ile kontrol edilir:
`style: form` + `explode: true` bir sorgu parametresini `a=1&a=2`
yapısına, `explode: false` onu `a=1,2` yapısına çevirir. Tablo ve
kayıt tipleri için resmi serileştirme tablosu `swagger.io` belgesinde
çekilmiştir (§Kaynak Tablosu [D4]). (Kaynak: [D4])

---

## §16 Hata Yanıtları — Problem Details Bağlantısı

OpenAPI hata yanıtı tanımı (`4xx`/`5xx` `responses`) içerik biçimini
kodelleştirir; içerik biçimi olarak RFC 9457 *Problem Details for HTTP
APIs* kullanılabilir (`application/problem+json`). RFC 9457 şu alanları
tanımlar: `type`, `title`, `status`, `detail`, `instance`. (Kaynak: [D11])

Repo-ölçü kanıt: `routes.php` `validation` anahtarı `422 + error.fields`
döndürür — bu, RFC 9457 `errors` alanı ile birebir aynı yapıda değildir;
farklılık bilinçli midir → ⚠️ VERIFICATION REQUIRED. `Dosya:
api.coremusic.net/config/routes.php / Satır: 14` (gerçek disk okuması,
2026-10-13).

---

## §17 Önbellek Sinyalleri — 9111 Bağlantısı

HTTP önbellekleme başlıkları (`ETag`, `Cache-Control`, `Vary`) RFC 9111
(HTTP Caching) ile tanımlıdır; OpenAPI `responses[].headers` altında bu
başlıkların *varlığını* kodelleştirir, önbellek *politikasını* değil.
(Kaynak: [D9])

Repo-ölçü kanıt: `routes.php` `cacheable` / `cacheTtl` anahtarları
ResponseNormalization'ın ETag + Cache-Control ürettiğini belirtir —
`Dosya: api.coremusic.net/config/routes.php / Satır: 16` (gerçek disk
okuması, 2026-10-13).

---

## §18 Webhook Yönü — 8594 Bağlantısı

3.1+ kökündeki `webhooks` alanı, API'nin **dışarı** çağırdığı uçları
tarif eder (yol/üretim yönü tersindir). Teslim başlıkları (HTTP Header
Fields for Webhook Delivery) RFC 8594 ile tanımlıdır; OpenAPI `webhooks`
alanı yalnız uç/tip sözleşmesini kodelleştirir. (Kaynak: [D13], [D1])

---

## §19 CoreMusic Repo Karşılığı Özeti (Tasarım İddiaları — Kanıt Satırları Gerçektir)

| Sözleşme kavramı | Repo karşılığı | Kanıt (gerçek disk okuması, 2026-10-13) |
|---|---|---|
| Yol + metod ayrımı | Method-aware `RouteTable` (GET/POST ayrı bloklar) | `api.coremusic.net/config/routes.php` Satır 25 · 42 |
| `operationId` eşleniği | `action` anahtarı | `api.coremusic.net/config/routes.php` Satır 45 |
| Parametre doğrulama | `validation` → RequestValidationMiddleware (422) | `api.coremusic.net/config/routes.php` Satır 14 |
| Kimlik koruma bayrağı | `public` anahtarı (auth'sız erişim) | `api.coremusic.net/config/routes.php` Satır 11 |
| 405 sinyali | `method_not_allowed` sabiti + `Allow` | `shared/src/Api/Routing/RouteTable.php` Satır 29 |
| 404 sinyali | `not_found` sabiti | `shared/src/Api/Routing/RouteTable.php` Satır 30 |
| Belgé dosyası | `openapi.yaml`/`openapi.json` | ⚠️ VERIFICATION REQUIRED (bu turda diske tespit edilmedi) |

---

## §20 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| Kök nesne | OpenAPI dosyasının üst düzey yapısı (`openapi` + `info` + …) |
| Path Item | Tek bir yolun tüm metod tanımlarını taşıyan nesne |
| İşlem nesnesi | Tek bir HTTP metodunun girdi/çıktı/güvenlik sözleşmesi |
| JSON Pointer | `/a/b/0` biçimli iç-belge adresleme (RFC 6901) |
| Bileşim anahtarı | `allOf`/`oneOf`/`anyOf`/`not` — şemaları birleştiren anahtarlar |
| Ayıracı (discriminator) | Varyant şemalar arasında seçim yapan alan |
| Varyant şema | `oneOf` kollarındaki birbirinden farklı nesne tanımları |
| Serileştirme stili | `style`+`explode` ile parametre → URL kodlama kuralı |

---

## §21 Sık Karşılaştırılan Kavramlar

| Kavram A | Kavram B | Fark |
|---|---|---|
| `openapi` (alan) | `info.version` | Şartname sürümü vs API sürümü — bağımsız |
| `parameters` | `requestBody` | URL/başlık girdisi vs gövde girdisi |
| `paths` | `webhooks` | Gelen istek yolu vs giden (outbound) uç |
| `allOf` | `oneOf` | Hepsi-aynı-an-birden vs tam-biri |
| `$ref` (3.0) | `$ref` (3.1) | Yan anahtarlar yok sayılır vs geçerli |
| `nullable: true` | `type` dizisi içine `null` (JSON Schema biçimi) | 3.0 biçimi vs 3.1 (JSON Schema) biçimi |

---

## §22 Kapsam Dışı (bu dosyada işlenmez)

| Konu | Nerede | Kanıt |
|---|---|---|
| Doğrulama katmanları, CI kapısı, hata raporu | `openapi-dogrulama.md` | [D10], [D14] |
| Kod/CLI/araç kurulumu | `openapi-kod-ornekleri.md` | [D8] |
| Sürüm tarihçesi, kimlik kartı | `openapi-anayasa.md` | [D2] |
| URL/iddia denetimleri, red listesi | `openapi-web-arastirma.md` | [D14] |

---

## §23 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | CoreMusic hedef sürümü 3.0 mü 3.1 mi 3.2 mi? | UNKNOWN (ADR bekliyor) |
| 2 | `webhooks` (K013 event-contracts) bu şemaya mı bağlanır? | UNKNOWN |
| 3 | `operationId` ↔ `action` eşlemesi ADR'ye mi yazılır? | ⚠️ VERIFICATION REQUIRED |
| 4 | 3.2'nin 3.1'e eklediği yapısal alanlar neler? | ⚠️ VERIFICATION REQUIRED (metin çekilmedi) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler — 2026-10-13 HEAD 200)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Kök nesne, yollar, bileşim, discriminator tanımları (3.2.0 metni) | https://spec.openapis.org/oas/v3.2.0.html | 2026-10-13 | 200 |
| 3.2 sürüm sayfası canlıdır | https://spec.openapis.org/oas/v3.2.html | 2026-10-13 | 200 |
| 3.1.0 metni: JSON Schema uyumu, `nullable` farkı | https://spec.openapis.org/oas/v3.1.0.html | 2026-10-13 | 200 |
| Sürüm listesi sayfası | https://spec.openapis.org/ | 2026-10-13 | 200 |
| Swagger 2.0 (eski) spec yüzeyi — sürüm semantiği karşılaştırması | https://swagger.io/specification/v2/ | 2026-10-13 | 200 |
| Spec deposu (versions/ dizini, sürümler) | https://github.com/OAI/OpenAPI-Specification | 2026-10-13 | 200 |
| JSON Pointer sözdizimi | https://www.rfc-editor.org/rfc/rfc6901 | 2026-10-13 | 200 |
| Problem Details (RFC 9457) | https://www.rfc-editor.org/rfc/rfc9457 | 2026-10-13 | 200 |
| HTTP Caching (RFC 9111) | https://www.rfc-editor.org/rfc/rfc9111 | 2026-10-13 | 200 |
| URI/`$ref` biçimi (RFC 3986) | https://www.rfc-editor.org/rfc/rfc3986 | 2026-10-13 | 200 |
| Webhook teslim başlıkları (RFC 8594) | https://www.rfc-editor.org/rfc/rfc8594 | 2026-10-13 | 200 |
| `style`/`explode` serileştirme tablosu | https://swagger.io/docs/specification/serialization/ | 2026-10-13 | 200 |
| Data-models rehberi (şema/parametre/yanıt) | https://swagger.io/docs/specification/data-models/ | 2026-10-13 | 200 |

**Çakışma denetimi:** Bu dosyadaki 13 URL, `.ai/architecture` envanterinde
(2026-10-13 ölçümü — 1441 benzersiz URL) **bulunmayan** adlardan seçilmiştir;
`spec.openapis.org/oas/latest.html`, `v3.1.1.html`, `v3.2.1.html`, `oas/`,
`swagger.io/specification/`, `learn.openapis.org/` envanterde mevcut
olduğu için bu dosyada **kullanılmamıştır** (benzer işlevdeki `v3.2.0.html`,
`v3.1.0.html`, `spec.openapis.org/`, `specification/v2/` tercih edilmiştir).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · **Tarih: 2026-10-13**
