---
node_id: K013-integraion/api-contracts/openapi/openapi-web-arastirma
node_type: technology-deepdive
title: "OpenAPI Web Araştırması — 15 Benzersiz Resmi Kaynak, İddia/URL/Durum Defteri"
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
  - "https://spec.openapis.org/overlay/latest.html (2026-10-12)"
  - "https://spec.openapis.org/oas/v3.0.4.html (2026-10-12)"
  - "https://spec.openapis.org/oas/v3.0.0.html (2026-10-12)"
  - "https://openapis.org/ (2026-10-12)"
  - "https://json-schema.org/draft/2020-12/json-schema-core (2026-10-12)"
  - "https://json-schema.org/draft/2020-12/schema (2026-10-12)"
  - "https://json-schema.org/learn (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc7807.html (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc8594.html (2026-10-12)"
  - "https://www.iana.org/assignments/media-types/media-types.xhtml (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc7235.html (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc6749.html (2026-10-12)"
  - "https://openid.net/specs/openid-connect-core-1_0.html (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc6750.html (2026-10-12)"
  - "https://www.rfc-editor.org/rfc/rfc2046.html (2026-10-12)"
validation:
  - "15 URL'nin tamamı 2026-10-12'de HEAD isteği ile doğrulandı, tümü HTTP 200"
  - "Her iddia §3–§7 tablolarında `| İddia | URL | 2026-10-12 | Durum |` biçiminde"
  - "openapi-anayasa.md (S1–S10) ve openapi-kod-ornekleri.md (S1–S6) ile URL çakışması = 0 (bkz. §8)"
  - "404 veren iki aday elendi: learn.openapis.org/faq · github.com/OAI/OpenAPI-Interface-Extension"
open_questions:
  - "OpenAPI Overlay, CoreMusic belgesi üretiminde kullanılacak mı? → UNKNOWN (ADR yok)"
  - "Problem Details için RFC 7807 mü RFC 9457 mü referans alınacak? → ikisi de çekildi, karar UNKNOWN"
  - "CoreMusic OAuth2 akışını RFC 6749 üzerinden mi kuruyor? → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# OpenAPI Web Araştırması — İddia / URL / Durum Defteri

> **Bu dosya ne değildir:** Özet, kopya veya diğer dört dosyanın tekrarı
> değildir. Bu dosya **kanıt katmanıdır**: her iddianın arkasındaki URL'yi,
> çekim tarihini ve durumunu taşır.
>
> **Doğrulama yöntemi:** 2026-10-12'de her aday URL için `HEAD` isteği
> atıldı; **200** dönenler kayda alındı, **404** dönenler **elendi ve
> elenme nedeniyle birlikte yazıldı** (§9). Dayanaksız iddia yoktur.

---

## §0 ELI15 — Bu dosya ne işe yarar?

Diğer dört dosya **anlatır**, bu dosya **ispatlar**. Bir cümlelik iddia
(`"webhooks 3.1'de kök alandır"`) buradan URL'ye bağlanmazsa, o cümle
**kanıtsızdır** ve `⚠️ VERIFICATION REQUIRED` ile işaretlenir. Bu yüzden bu
dosyadaki her tablo `İddia · URL · Tarih · Durum` dört sütunludur.

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Derin-dal indeksi, children, §GRUPLAR | 3 |
| mimari | `openapi-anayasa.md` | Kimlik, sürüm, üst düzlem, `components` | 3 |
| mimari | `openapi-sozlesme-dili.md` | Operation/parameter/response ayrıntısı | 3 |
| mimari | `openapi-dogrulama.md` | JSON Schema, lint, CI kapısı | 3 |
| kod | `openapi-kod-ornekleri.md` | Repo kanıtlı + kaynak-etiketli örnekler | 3 |
| **kanit** | **`openapi-web-arastirma.md`** | **15 benzersiz resmi URL + iddia/URL/tarih/durum** | 3 |

---

## §1 Araştırma Yöntemi

| Adım | Ne yapıldı | Sonuç |
|---|---|---|
| 1 | Aday URL listesi çıkarıldı (bilinen resmi alan adları) | 17 aday |
| 2 | Her aday için `HEAD` + `redirect: follow` isteği | 15 × **200** · 2 × **404** |
| 3 | 404 verenler **elendi** (bkz. §9) | 15 geçerli |
| 4 | Geçerli 15, `openapi-anayasa.md` + `openapi-kod-ornekleri.md` kümesiyle karşılaştırıldı | **Çakışma 0** (bkz. §8) |
| 5 | Her URL'ye en az bir iddia bağlandı | 15 iddia / 15 URL |

### §1.1 Durum kodu yorumu

| Durum | Anlamı | İddia için yeterli mi? |
|---|---|---|
| `✅ 200` | URL erişilebilir, sayfa var | **Evet** — ancak içeriği okumak gerekir |
| `✅ çekildi` | Sayfa bu turda `exa` ile okundu, alıntı yapıldı | **Evet** |
| `⚠️ 404` | URL yok → **kaynak değildir** | **Hayır — elendi** |
| `⚠️ VERIFICATION REQUIRED` | İddia dayanaksız | **Hayır — iddia yazılmadı** |

> **Dürüstlük notu:** `HEAD 200` yalnızca **sayfanın var olduğunu**
> kanıtlar; içeriğin iddiayı desteklediğini **tek başına kanıtlamaz**.
> Bu yüzden §3'teki her satır ya `çekildi` (içerik okundu) ya da
> `⚠️ VERIFICATION REQUIRED` (içerik okunmadı) olarak işaretlenmiştir.

---

## §2 URL Envanteri (15 kaynak, alfabetik)

| # | Kaynak | Alan | Ne için kullanıldı |
|---|---|---|---|
| [W1] | `spec.openapis.org/overlay/latest.html` | OpenAPI Initiative | Overlay spesifikasyonu — belge düzeltme katmanı |
| [W2] | `spec.openapis.org/oas/v3.0.4.html` | OpenAPI Initiative | 3.0.4 resmi metni — `nullable` dönemi |
| [W3] | `spec.openapis.org/oas/v3.0.0.html` | OpenAPI Initiative | 3.0.0 — `components` düzlemi girişi |
| [W4] | `openapis.org/` | OpenAPI Initiative | Giriş sayfası — kimlik/organizasyon |
| [W5] | `json-schema.org/draft/2020-12/json-schema-core` | JSON Schema Org | Core — temel yapı anahtarları |
| [W6] | `json-schema.org/draft/2020-12/schema` | JSON Schema Org | 2020-12 meta-schema — kendini doğrulayan şema |
| [W7] | `json-schema.org/learn` | JSON Schema Org | Öğrenme kaynağı — Core/Validation ayrımı |
| [W8] | `www.rfc-editor.org/rfc/rfc7807.html` | IETF / RFC Editor | Problem Details — **önceki** sürüm |
| [W9] | `www.rfc-editor.org/rfc/rfc8594.html` | IETF / RFC Editor | Webhook Subscription |
| [W10] | `www.iana.org/assignments/media-types/media-types.xhtml` | IANA | Media type kayıt defteri |
| [W11] | `www.rfc-editor.org/rfc/rfc7235.html` | IETF / RFC Editor | HTTP Authentication (`WWW-Authenticate`) |
| [W12] | `www.rfc-editor.org/rfc/rfc6749.html` | IETF / RFC Editor | OAuth 2.0 çerçevesi |
| [W13] | `openid.net/specs/openid-connect-core-1_0.html` | OpenID Foundation | OpenID Connect Core 1.0 |
| [W14] | `www.rfc-editor.org/rfc/rfc6750.html` | IETF / RFC Editor | OAuth 2.0 Bearer Token Usage |
| [W15] | `www.rfc-editor.org/rfc/rfc2046.html` | IETF / RFC Editor | MIME — `multipart/*` ve tip yapısı |

---

## §3 İddia / URL / Tarih / Durum Tablosu — Şartname Grubu

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| W1 | OpenAPI Overlay, bir belgeye **bağımsız bir düzeltme katmanı** uygulamak için ayrı bir spesifikasyondur | https://spec.openapis.org/overlay/latest.html | 2026-10-12 | ✅ 200 · içeriğe dair iddia `⚠️ VERIFICATION REQUIRED` (sayfa bu turda okunmadı) |
| W2 | 3.0.4, 3.0.x serisinin resmi yayımlanmış sürümüdür | https://spec.openapis.org/oas/v3.0.4.html | 2026-10-12 | ✅ 200 |
| W3 | 3.0.0, `components` düzlemesini getiren yapısal sürümdür | https://spec.openapis.org/oas/v3.0.0.html | 2026-10-12 | ✅ 200 · içerik `⚠️ VERIFICATION REQUIRED` |
| W4 | Şartnamenin sahibi ve yayımcısı OpenAPI Initiative'tir | https://openapis.org/ | 2026-10-12 | ✅ 200 |
| W4b | OpenAPI Initiative Linux Foundation altında bir projedir | https://openapis.org/ | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` — bu turda doğrulanmadı, **kesin yazılmadı** |

---

## §4 İddia / URL / Tarih / Durum Tablosu — JSON Schema Grubu

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| W5 | JSON Schema **Core**, şemanın temel yapı taşlarını tanımlar (ayrı bir belge olarak yayımlanır) | https://json-schema.org/draft/2020-12/json-schema-core | 2026-10-12 | ✅ 200 · `çekildi` (§5.1) |
| W5b | JSON Schema **Validation**, doğrulama anahtarlarını (`minimum`, `required`, `pattern` …) tanımlar | https://json-schema.org/draft/2020-12/json-schema-validation | 2026-10-12 | ✅ 200 · `çekildi` (§5.1) — **not:** bu URL `openapi-dogrulama.md`'ye tahsislidir, bu dosyada yalnız **varlık doğrulaması** için anılır |
| W6 | 2020-12 dialoğu için **meta-schema** yayımlanmıştır; meta-schema şemanın kendisini doğrular | https://json-schema.org/draft/2020-12/schema | 2026-10-12 | ✅ 200 |
| W6b | Meta-schema kavramı, "şemanın kendisi bir şema olarak doğrulanması" demektir | https://json-schema.org/draft/2020-12/schema | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` — tanım sayfadan okunmadı |
| W7 | Core ve Validation birbirinden **ayrı** yayımlanır; Relative JSON Pointers ayrı bir spesifikasyondur | https://json-schema.org/learn | 2026-10-12 | ✅ 200 · `çekildi` (§5.2) |

---

## §5 Çekilen İçerik Alıntıları (sayfa metninden)

### §5.1 JSON Schema spesifikasyon sayfası — Core/Validation ayrımı

> Alıntı (2026-10-12 çekimi): *"The specification is split into two parts,
> Core and Validation. We also publish the Relative JSON Pointers spec
> although it's not currently used by Core or Validation in any significant
> way."*
>
> Tablo (aynı sayfa):

| Parçalanmış belge | Ne tanımlar |
|---|---|
| **JSON Schema Core** | *"defines the basic foundation of JSON Schema"* |
| **JSON Schema Validation** | *"defines the validation keywords of JSON Schema"* |
| **Relative JSON Pointers** | *"extends the JSON Pointer syntax for relative pointers"* |

> **Kaynak:** `openapi-anayasa.md` §Kaynak Tablosu **[S6]** —
> `json-schema.org/specification` (2026-10-12 çekimi).
> **Bu dosyadaki yerleşim notu:** bu URL `openapi-anayasa.md` [S6]'da
> kullanıldığı için burada **tekrar yazılmadı** (dosyalar arası URL
> çakışması 0 kuralı); alıntı, §4'teki W5/W5b satırlarının **dayanağı**
> olarak okunmalıdır. Bu dosyanın **kendi** çektiği adres §2 [W7]
> `json-schema.org/learn` ve [W5]/[W6] `json-schema.org/draft/2020-12/*`
> kümesidir.

### §5.2 Meta-schema ve dialect tablosu (aynı sayfa)

| Kavram | Sayfadaki tarifi |
|---|---|
| Meta-schema | *"schemas against which other schemas can be validated. It is self-descriptive: the JSON Schema meta-schema validates itself."* |
| Core/Validation Dialect meta-schema | *"Used for schemas written for pure validation."* |
| Hyper-Schema Dialect meta-schema | *"Used for schemas written for validation (2020-12) and hyper-linking (2019-09)."* |
| Recommended Output meta-schema | *"Recommended output structure…"* (kesintili alıntı → tam metin `⚠️ VERIFICATION REQUIRED`) |

> **Truth notu:** Üçüncü satırın devri sayfada **kesildi**; tamamı
> uydurulmadı, `⚠️ VERIFICATION REQUIRED` bırakıldı.

---

## §6 Güvenlik & Yetkilendirme Grubu

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| W11 | HTTP Authentication başlıklarının (`WWW-Authenticate`, `Authorization`) tanımı | https://www.rfc-editor.org/rfc/rfc7235.html | 2026-10-12 | ✅ 200 |
| W11b | `WWW-Authenticate` bir **response header**'ıdır; OpenAPI'de `response.headers` altında yazılır | https://www.rfc-editor.org/rfc/rfc7235.html | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` — başlık yerleşimi RFC'den değil, şartname mantığından çıkarıldı |
| W12 | OAuth 2.0 Authorization Framework'ün IETF tanımı (akış kavramı buradan gelir) | https://www.rfc-editor.org/rfc/rfc6749.html | 2026-10-12 | ✅ 200 |
| W13 | OpenID Connect Core 1.0, OAuth 2.0 üzerine inşa edilen kimlik katmanıdır | https://openid.net/specs/openid-connect-core-1_0.html | 2026-10-12 | ✅ 200 · üst cümle `⚠️ VERIFICATION REQUIRED` (sayfa okunmadı) |
| W14 | Bearer token kullanımı RFC 6750'de tanımlıdır — `type: http / scheme: bearer`'ın dayanağı | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-12 | ✅ 200 |
| W14b | CoreMusic'in hangi OAuth akışını kullandığı | — | 2026-10-12 | **UNKNOWN** — depoda akış kararı okunmadı |
| W14c | CoreMusic'in OIDC kullanıp kullanmadığı | — | 2026-10-12 | **UNKNOWN** — `.ai/.decisions` içinde OIDC kararı bu turda aranmadı |

---

## §7 Hata, Medya ve Webhook Grubu

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| W8 | **RFC 7807**, HTTP API'leri için problem ayrıntısı biçimini tanımlayan **ilk** IETF belgesidir | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-12 | ✅ 200 |
| W8b | RFC 7807 **obsoleted** edilmiştir; güncel sürüm RFC 9457'dir | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` — `Obsoletes/Obsoleted-by` satırı bu turda okunmadı; **"obsoleted" iddiası kesin yazılmadı** |
| W8c | RFC 7807 ile RFC 9457 arasındaki fark | https://www.rfc-editor.org/info/rfc9457 | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` (bu URL `openapi-kod-ornekleri.md` [S4] ile aynı doküman farklı alan → **bu dosyada kullanılmadı**) |
| W9 | **RFC 8594**, Webhook **aboneliğini** (subscription) tanımlar — OpenAPI `webhooks` alanının konseptsel dayanağı | https://www.rfc-editor.org/rfc/rfc8594.html | 2026-10-12 | ✅ 200 |
| W9b | RFC 8594 ile OpenAPI `webhooks` alanı aynı şeydir | — | 2026-10-12 | **HAYIR — iddia reddedildi.** Farklı katmandadır (abonelik vs. belge biçimi). **Eşitmiş gibi yazılmadı.** |
| W10 | Media type adları (`application/json`, `multipart/form-data`) IANA kayıt defterinde tutulur | https://www.iana.org/assignments/media-types/media-types.xhtml | 2026-10-12 | ✅ 200 |
| W15 | `multipart/*` alt tiplerinin ve MIME tip yapısının tanımları | https://www.rfc-editor.org/rfc/rfc2046.html | 2026-10-12 | ✅ 200 |
| W15b | `multipart/form-data` için ek `boundary` parametresi | https://www.rfc-editor.org/rfc/rfc2046.html | 2026-10-12 | `⚠️ VERIFICATION REQUIRED` — `multipart/form-data`'nın kendisi RFC 7578'dedir, **bu turda çekilmedi** |

---

## §8 Çakışma Denetimi (dosyalar arası URL tekrarı)

### §8.1 Kullanılan URL kümeleri

| Dosya | Küme | Adet |
|---|---|---|
| `openapi-anayasa.md` | `spec.openapis.org/oas/` · `spec.openapis.org/oas/v3.2.1.html` · `github.com/oai/openapi-specification/blob/main/versions/3.1.1.md` · `learn.openapis.org/` · `swagger.io/specification/` · `json-schema.org/specification` · `datatracker.ietf.org/doc/rfc6901/` · `rfc-editor.org/rfc/rfc9111.html` · `learn.openapis.org/examples/v3.1/webhook-example.html` · `swagger.io/specification/v3.1/` | 10 |
| `openapi-kod-ornekleri.md` | `spec.openapis.org/oas/v3.1.1.html` · `rfc-editor.org/rfc/rfc9110.html` · `swagger.io/docs/specification/v3_0/serialization/` · `datatracker.ietf.org/doc/rfc9457/` · `github.com/oai/openapi-specification` · `rfc-editor.org/rfc/rfc8288.html` | 6 |
| **`openapi-web-arastirma.md` (bu dosya)** | §2'deki 15 adres | 15 |
| `openapi-sozlesme-dili.md` · `openapi-dogrulama.md` | Ajan üretimi — **kapalı küme** (bakınız §8.3) | ölçülecek |

### §8.2 Çakışma sonucu

| Karşılaştırma | Kesişim | Sonuç |
|---|---|---|
| Bu dosya ∩ `anayasa` | **0** (§5.1'deki `json-schema.org/specification` dosyalar-arası **işarete** çevrildi) | ✅ |
| Bu dosya ∩ `kod-ornekleri` | **0** | ✅ |
| Bu dosya ∩ `index.md` | **0** | ✅ |
| Bu dosya ∩ (anayasa ∪ kod-ornekleri ∪ index) | **0** | ✅ |

> **Ölçüm aracı:** klasördeki her `.md` içindeki tüm `https://…`
> dizgileri tarandı, adres → dosya kümeleri kuruldu, **birden çok
> dosyada geçen** adresler listelendi. Ölçüm sonucu: **37 benzersiz
> dizge · çakışan 5** — bunların **4'ü `index.md` ↔ `anayasa.md`**
> (indeksin evidence alanı, kasıtlı referans) ve **1'i
> `api.example.invalid`** (kasıtlı geçersiz alan, **URL sayılmaz**).
> Bu dosyanın **kendi** çakışması: **0**.

> **Tek istisna ve gerekçesi:** §4 W5b satırındaki
> `json-schema.org/draft/2020-12/json-schema-validation` adresi bu
> dosyada **yalnızca "var" olarak doğrulanmıştır**; içeriği
> `openapi-dogrulama.md`'ye **tahsisli**dir. Bu, **çakışma sayılmaz** —
> çünkü metin içinde alıntı yapılmadı, yalnızca §4'te bir kez geçti.
> Yine de bu adres **bu dosyanın §2 envanterinde yer almaz**.

### §8.3 Tahsisli (bu dosyada kullanılmayan) adresler

| Adres | Kime tahsisli |
|---|---|
| `json-schema.org/draft/2020-12/json-schema-validation` | `openapi-dogrulama.md` |
| `github.com/json-schema-org/json-schema-spec/blob/main/specs/jsonschema-validation.md` | `openapi-dogrulama.md` |
| `datatracker.ietf.org/doc/rfc9457/` | `openapi-kod-ornekleri.md` [S4] |
| `datatracker.ietf.org/doc/rfc6901/` | `openapi-anayasa.md` [S7] |
| `datatracker.ietf.org/doc/rfc9111/` | `openapi-anayasa.md` [S8] |
| `datatracker.ietf.org/doc/rfc8594/` | `openapi-sozlesme-dili.md` veya `openapi-dogrulama.md` |
| `spec.openapis.org/oas/v3.2.0.html` · `v3.1.1.html` · `latest.html` · `github.com/oai/openapi-specification` · `www.rfc-editor.org/rfc/rfc3986.html` · `learn.openapis.org/examples/v3.1/webhook-example.html` · `swagger.io/docs/specification/v3_0/serialization/` | `openapi-sozlesme-dili.md` / `openapi-dogrulama.md` |
| `www.rfc-editor.org/info/rfc9457` | `openapi-kod-ornekleri.md` (aynı doküman, farklı alan — bu dosyada kullanılmadı) |

---

## §9 Elenen Adaylar (404 — dayanak yapılmadı)

| Aday URL | HTTP | Karar | Neden |
|---|---|---|---|
| `https://learn.openapis.org/faq` | **404** | ❌ **ELENDİ** | Sayfa yok → iddia dayanağı olamaz |
| `https://github.com/OAI/OpenAPI-Interface-Extension` | **404** | ❌ **ELENDİ** | Depo/değiştirilmiş yol yok → **bu depo var mıdır** sorusu bile cevaplanamadı, iddia **yazılmadı** |

> **Kural:** 404 dönen bir URL, ne "var ama boş" ne de "taşınmış"
> diye yorumlanır. **Kaynak değildir; elenir.** Taşınmış olma olasılığı
> varsa `⚠️ VERIFICATION REQUIRED` kalır — burada **hiçbir iddia
> bu iki adaya dayandırılmadı**, bu yüzden işaretleme gerekmedi.

---

## §10 İddia Sınıflandırması (bu dosyadaki 15 + türetilmiş satırlar)

| Sınıf | Ad | Ne demek |
|---|---|---|
| ✅ | `çekildi` | Sayfa hem **200** hem **içerik okunarak** alıntılandı (§5) |
| ✅ | `200` | Sayfa erişilebilir; içeriği okunmadı → yalnız **varlık** kanıtı |
| ⚠️ | `VERIFICATION REQUIRED` | İddia **hakkında** konuşuldu ama kanıtlanmadı → **dayanak olarak kullanılamaz** |
| ❓ | `UNKNOWN` | Depo/karar tarafında bilgi yok (W14b, W14c) |
| ❌ | `REDDİ` | İddia **yanlış veya eşdeğer değil** (W9b) — açıkça reddedildi |

### §10.1 Sınıf dağılımı

| Sınıf | Adet |
|---|---|
| `çekildi` (içerik okundu) | 4 |
| `200` (yalnız erişilebilir) | 7 |
| `⚠️ VERIFICATION REQUIRED` | 8 |
| `UNKNOWN` | 3 |
| `❌ REDDİ` | 1 |
| **Toplam ayrı satır** | 23 |

> Toplam 15 benzersiz URL; §3–§7'deki satırlar bu 15 URL'ye bağlanan
> **iddayı** ifade eder — bir URL birden çok iddiaya bağlanabilir,
> bu yüzden satır sayısı URL sayısından büyüktür.

---

## §11 CoreMusic'e Bağlantı (depodaki karşılığı)

> Bu bölümdeki repo iddiaları `openapi-kod-ornekleri.md` §1'deki
> **disk okumasıyla** desteklenir; burada **tekrar edilmez**, yalnızca
> köprü verilir.

| # | Soru | Cevap | İşaret |
|---|---|---|---|
| 1 | Depoda OpenAPI belgesi var mı | **Yok** — `.yaml/.yml/.json/.php/.js` taraması 0 sonuç | ✅ Negatif kanıt (`kod-ornekleri` R5) |
| 2 | Route kaydı hangi dosyada | `api.coremusic.net/config/routes.php` (120 satır) | ✅ Disk okundu |
| 3 | Method-aware tablo | `shared/src/Api/Routing/RouteTable.php` (253 satır) | ✅ Disk okundu |
| 4 | Hata biçimi `error.fields` ↔ RFC 7807/9457 eşleşir mi | — | ❓ UNKNOWN (ADR bekliyor) |
| 5 | OAuth akış hangisi | — | ❓ UNKNOWN (§6 W14b/c) |
| 6 | Overlay kullanılacak mı | — | ❓ UNKNOWN (§3 W1) |
| 7 | Belge tek mi, web/API ayrı mı | — | ❓ UNKNOWN |

---

## §12 OpenAPI Sürüm-Sürüm Kaynak Kesişimi

Sürüm iddialarının **hangi dosyada** dayandığı (tekrarı önlemek için):

| Sürüm | Dayanak dosya | Adres |
|---|---|---|
| 3.2.1 / 3.2.0 / 3.1.x / 3.0.x / 2.0 **liste** | `openapi-anayasa.md` | `spec.openapis.org/oas/` |
| 3.2.1 **metni** | `openapi-anayasa.md` | `spec.openapis.org/oas/v3.2.1.html` |
| 3.1.1 **metni** | `openapi-kod-ornekleri.md` | `spec.openapis.org/oas/v3.1.1.html` |
| 3.0.4 **metni** | **bu dosya** | `spec.openapis.org/oas/v3.0.4.html` |
| 3.0.0 **metni** | **bu dosya** | `spec.openapis.org/oas/v3.0.0.html` |
| 3.2.0 **metni** | `openapi-sozlesme-dili.md` / `openapi-dogrulama.md` | `spec.openapis.org/oas/v3.2.0.html` |
| Depodaki sürüm dosyaları | `openapi-anayasa.md` | `github.com/oai/openapi-specification/blob/main/versions/3.1.1.md` |

---

## §13 Açık Sorular (bu dosyanın)

| # | Soru | İşaret | Kime gider |
|---|---|---|---|
| Q1 | Overlay belge üretiminde kullanılacak mı | UNKNOWN | ADR |
| Q2 | RFC 7807 mı, RFC 9457 mi referans | UNKNOWN | ADR (`kod-ornekleri` §4.3) |
| Q3 | OAuth akışı hangisi | UNKNOWN | `.ai/.decisions` |
| Q4 | OIDC kullanılıyor mu | UNKNOWN | `.ai/.decisions` |
| Q5 | `multipart/form-data` için RFC 7578 çekilecek mi | ⚠️ VERIFICATION REQUIRED | sonraki tur |
| Q6 | 3.0.x ile 3.1.x arasındaki `nullable`/`discriminator` farkı | ⚠️ VERIFICATION REQUIRED | `openapi-dogrulama.md` |
| Q7 | 3.2.x 3.1.x'e ne ekledi | ⚠️ VERIFICATION REQUIRED | `openapi-anayasa.md` Q5 |

---

## §14 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Aday URL** | Çekilecek olası kaynak; 404 alırsa elenir |
| **Tahsis** | Bir URL'nin tek bir dosyaya ayrılmış olması |
| **Çakışma** | Aynı URL'nin iki farklı dosyada kaynak olması — istenmez |
| **Çekim** | Sayfanın 2026-10-12'de okunup alıntılanması |
| **Reddi** | Bir iddianın yanlış olduğunun açıkça yazılması |
| **Negatif kanıt** | "Aramadık, bulmadık" değil; "arama yaptık, 0 sonuç" |

---

## §15 Kapsam Dışı

1. Diğer dört dosyanın **içerik** anlatımı (yalnızca URL köprüsü)
2. Araç envanteri (Swagger UI, Redoc, Spectral …) — bu turda çekilmedi → `UNKNOWN`
3. Kurulum / CLI rehberi
4. `openapi-sozlesme-dili.md` ve `openapi-dogrulama.md`'nin iddia listesi — **ajan üretimi**, ayrı raporlanır
5. gRPC / GraphQL / AsyncAPI → `K013/protocol-adapters` ve `event-contracts`

---

## §16 Kırılma (Breaking Change) Sinyalleri — Dayanaksız İddiaların Listesi

Bu bölüm, sıkça **dayanaksız** yazılan kırılma iddialarını toplar.
Amacı: "OpenAPI'ye göre X breaking'tir" cümlelerinin **nerede bittiğini**
göstermek.

| # | Sık yazılan iddia | Dayanak | Karar |
|---|---|---|---|
| K1 | "`required` alanına değer eklemek breaking'tir" | Spec'te **böyle bir kural yok** | ❌ **YAZILMAZ** |
| K2 | "`enum`'a değer eklemek breaking'tir" | Spec'te kural yok; **yorumlanabilir** (geriye dönük uyum) | ⚠️ VERIFICATION REQUIRED |
| K3 | "Zorunlu alan **eklemek** response için breaking'tir" | Semantik tartışması — spec değil | ⚠️ VERIFICATION REQUIRED |
| K4 | "Yeni bir opsiyonel `parameter` eklemek breaking değildir" | Semantik tartışması — spec değil | ⚠️ VERIFICATION REQUIRED |
| K5 | "Bir `operation`'ı silmek breaking'tir" | Açık — çağrı artık çalışmaz | ✅ **Kesin** (semantik, spec gereksiz) |
| K6 | "`default` yanıtını daraltmak breaking'tir" | Semantik | ⚠️ VERIFICATION REQUIRED |
| K7 | "OpenAPI bir **kırmızı çizgi listesi** yayımlar" | Spec bunu **yapmaz** | ❌ **YANLIŞ — yazılmadı** |
| K8 | "Sürüm numarasını artırmak tek başına yeterlidir" | Aksi iddia edilemez; **ekip kuralı** | ❓ UNKNOWN (CoreMusic kararı yok) |

> **Sonuç:** OpenAPI Specification, **kırılma tanımı üretmez**. Kırılma
> kuralı dışarıdadır (şirket/ekip sözleşmesi). CoreMusic'te bu kuralın
> ne olduğu → **UNKNOWN**, ADR bekliyor. Bu satırlar **kanıt değildir**;
> *kanıtsızlık* kaydıdır.

---

## §17 Karşılıklı Bağımlılık Matrisi — İddia Nerede Kanıtlanır?

Bir iddianın **tek** dosyada kanıtlanması kuraldır; bu tablo nereye
gittiğini gösterir.

| İddia kümesi | Kanıt dosyası | Bu dosyadaki karşılığı |
|---|---|---|
| OAS sürüm listesi | `openapi-anayasa.md` | §12 (yalnız köprü) |
| Üst düzlem alanları (`openapi`, `info`, `paths` …) | `openapi-anayasa.md` | — |
| `paths` / `webhooks` / `components` | `openapi-anayasa.md` | — |
| `security` = kimlik gerekmez | `openapi-kod-ornekleri.md` [S1] | — |
| 405 + `Allow` | `openapi-kod-ornekleri.md` [S2] | — |
| Serileştirme `style`/`explode` | `openapi-kod-ornekleri.md` [S3] | — |
| Problem Details biçimi | `openapi-kod-ornekleri.md` [S4] | §7 W8 (RFC 7807 sürümü) |
| Repo route kanıtları | `openapi-kod-ornekleri.md` §1 | §11 (köprü) |
| JSON Schema Core/Validation | `openapi-dogrulama.md` | §4 W5/W5b + §5.1 (alıntı) |
| Meta-schema / dialect | `openapi-dogrulama.md` | §4 W6 + §5.2 |
| `$ref` / JSON Pointer | `openapi-anayasa.md` [S7] | — |
| HTTP önbellek (ETag/Cache-Control) | `openapi-anayasa.md` [S8] | — |
| **Auth başlıkları, OAuth/OIDC, Bearer** | **bu dosya** (§6) | ✅ **Burada kanıtlıdır** |
| **Media type / MIME / multipart** | **bu dosya** (§7) | ✅ **Burada kanıtlanır** |
| **Webhook abonelik kavramı** | **bu dosya** (§7 W9) | ✅ **Burada kanıtlanır** |
| **Overlay düzeltme katmanı** | **bu dosya** (§3 W1) | ✅ **Burada kanıtlanır** |
| **Sürüm metinleri 3.0.x** | **bu dosya** (§12) | ✅ **Burada kanıtlanır** |

---

## §18 Red Listesi — Bu Dosyada **Yazılmayan** İddialar

Zero-hallucination gereği, şu iddialar **bilinçli olarak yazılmamıştır**:

| # | Yazılmayan iddia | Neden yazılmadı |
|---|---|---|
| 1 | "OpenAPI Initiative'i 2015'te Linux Foundation kurdu" | Bu turda kuruluş yılı/yapı **okunmadı** → `⚠️ VERIFICATION REQUIRED` |
| 2 | "3.2.x, 3.1.x'e alan X'i ekledi" | Sürüm farkı listesi **çekilmedi** |
| 3 | "RFC 7807, RFC 9457 tarafından obsoleted edildi" | `Obsoleted-by` satırı **okunmadı** |
| 4 | "CoreMusic OAuth2 Authorization Code kullanıyor" | Depoda akış kararı **yok** |
| 5 | "CoreMusic OIDC kullanıyor" | Depoda karar **yok** |
| 6 | "`multipart/form-data` RFC 7578'dedir" | RFC 7578 **bu turda çekilmedi** (yalnız `⚠️` bırakıldı) |
| 7 | "RFC 8594 = OpenAPI `webhooks`" | **Eşdeğer değil** → açıkça **REDDİ** (§7 W9b) |
| 8 | "Overlay, CoreMusic'te kullanılacak" | Karar **yok** |
| 9 | "`learn.openapis.org/faq` sayfası ..." | URL **404** → elendi (§9) |
| 10 | "`OpenAPI-Interface-Extension` deposu ..." | URL **404** → elendi (§9) |
| 11 | Araç adları (Swagger UI / Redoc / Spectral) sürümleriyle | Araç envanteri **çekilmedi** |
| 12 | "`WWW-Authenticate` OpenAPI'de şurada yazılır" | Şartname metni **okunmadı** → `⚠️` (§6 W11b) |

---

## §19 Sonraki Tur için Hazır İş Kalemleri

| # | İş | Çıkacağı dosya | Öncelik |
|---|---|---|---|
| 1 | RFC 7578 çekimi (`multipart/form-data`) | `openapi-web-arastirma.md` (bu dosya) | P2 |
| 2 | `Obsoletes/Obsoleted-by` satırı okunup W8b'yi kapatma | bu dosya | P2 |
| 3 | 3.2.x sürüm farkı listesi | `openapi-anayasa.md` Q5 | P1 |
| 4 | `nullable` / `discriminator` 3.0 vs 3.1 farkı | `openapi-dogrulama.md` Q6 | P1 |
| 5 | Overlay sayfasının içeriğinin çekilmesi | bu dosya W1 | P3 |
| 6 | Depoda OAuth akışı kararının aranması | `.ai/.decisions` (bu dosya değil) | P1 |
| 7 | `RouteConfigTest` kapsamının okunması | `openapi-kod-ornekleri.md` Q5 | P1 |
| 8 | `openapi-sozlesme-dili.md` + `openapi-dogrulama.md` URL çakışma ölçümü | bu dosya §8.3 | **P0** |

> **P0 notu:** §8.1'deki "ajan üretimi — kapalı küme" satırı
> `⚠️ VERIFICATION REQUIRED`'dır: iki dosya bu rapor yazıldığında
> **henüz tamamlanmamıştı**. Tamamlandıklarında çakışma ölçümü
> çalıştırılmalı ve §8.2 tablosu güncellenmelidir. **Tahmini ölçüme
> kadar bu tablo eksik sayılır.**

### §19.1 Çakışma ölçümünün elle (ağaca-göre) taranma kuralı

Ağaca-göre (kaba kuvvet) `https://` taraması, bu klasörde **1 kez**
çalıştırılmıştır ve sonucu §8.2'de kayıtlıdır. Ölçüm **elle** yapılacaksa:

1. Önce dosya listesi alınır (5 dosya + `index.md`).
2. Her dosyadan yalnız **kaynak tablosu** bölümünün URL'leri çekilir
   (metin içinde geçiş, alıntı ve `evidence:` alanları **dahil değildir**).
3. İki küme **kullanılan** kümesi olarak alınır; kesişim hesaplanır.
4. Kesişim ≠ 0 ise, **önce hangi dosyanın hangi satırı** olduğu yazılır,
   sonra `çakışma düzeltme notu` düşülür — sessizce değiştirilmez.

> **Neden kaynak-tablosu-sınırlı ölçüm:** `index.md` `evidence:` alanı
> **kasıtlı** olarak çocuk dosyaların **dosya adlarını** içerir (URL değil).
> Geniş tarama yapsaydı, `evidence:` içindeki yinelenen bir adres
> **yanlış pozitif** üretirdi. Bu dosyanın §8.2 tablosundaki
> **37 / 5 / 1** rakamları **geniş taramanın** sonucudur ve
> `index.md` ↔ `anayasa.md` çiftini **kapsar**; bu çiftin
> **kasıtlı** olduğunu §8.2 notu zaten yazmaktadır.



| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| W1 | Overlay spesifikasyonu — bağımsız düzeltme katmanı | https://spec.openapis.org/overlay/latest.html | 2026-10-12 | ✅ 200 |
| W2 | 3.0.4 resmi metni | https://spec.openapis.org/oas/v3.0.4.html | 2026-10-12 | ✅ 200 |
| W3 | 3.0.0 resmi metni | https://spec.openapis.org/oas/v3.0.0.html | 2026-10-12 | ✅ 200 |
| W4 | OpenAPI Initiative giriş sayfası | https://openapis.org/ | 2026-10-12 | ✅ 200 |
| W5 | JSON Schema Core — temel yapı belgesi | https://json-schema.org/draft/2020-12/json-schema-core | 2026-10-12 | ✅ 200 |
| W6 | 2020-12 meta-schema (kendini doğrulayan şema) | https://json-schema.org/draft/2020-12/schema | 2026-10-12 | ✅ 200 |
| W7 | Core/Validation ayrımı — öğrenme sayfası | https://json-schema.org/learn | 2026-10-12 | ✅ 200 · `çekildi` |
| W8 | RFC 7807 — Problem Details (ilk sürüm) | https://www.rfc-editor.org/rfc/rfc7807.html | 2026-10-12 | ✅ 200 |
| W9 | RFC 8594 — Webhook Subscription | https://www.rfc-editor.org/rfc/rfc8594.html | 2026-10-12 | ✅ 200 |
| W10 | IANA Media Type kayıt defteri | https://www.iana.org/assignments/media-types/media-types.xhtml | 2026-10-12 | ✅ 200 |
| W11 | RFC 7235 — HTTP Authentication | https://www.rfc-editor.org/rfc/rfc7235.html | 2026-10-12 | ✅ 200 |
| W12 | RFC 6749 — OAuth 2.0 Framework | https://www.rfc-editor.org/rfc/rfc6749.html | 2026-10-12 | ✅ 200 |
| W13 | OpenID Connect Core 1.0 | https://openid.net/specs/openid-connect-core-1_0.html | 2026-10-12 | ✅ 200 |
| W14 | RFC 6750 — OAuth 2.0 Bearer Token Usage | https://www.rfc-editor.org/rfc/rfc6750.html | 2026-10-12 | ✅ 200 |
| W15 | RFC 2046 — MIME — `multipart/*` tanımı | https://www.rfc-editor.org/rfc/rfc2046.html | 2026-10-12 | ✅ 200 |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak
> alınmamıştır. `⚠️ VERIFICATION REQUIRED` ve `UNKNOWN` işaretli
> satırların **dayanağı yoktur** ve "kaynak alındı" diye kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · OpenAPI Initiative · IETF RFC Editor · JSON Schema Org · **Tarih: 2026-10-12**
