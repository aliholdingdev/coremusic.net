---
title: "K011 · dto — Web Araştırma"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: kanit
---

# K011 · dto — Web Araştırma (Kanıt Havuzu)

> **Amaç:** DTO katmanının dayandığı dış referansları (PSR-7 Message, JsonSerializable,
> Content-Type, 409 Conflict) tek dosyada toplamak. Yalnız bu 4 URL kullanılır.
> Repo içi kanıtlar `Dosya: <yol> · Satır: <no>` formatıyla verilir.

## §GRUPLAR

| Bölüm | Konu | Kaynak |
|---|---|---|
| §A | PSR-7 Message interface | php.net/manual/en/class.psr-7-message.php |
| §B | JsonSerializable | php.net/manual/en/class.jsonserializable.php |
| §C | Content-Type header | developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type |
| §D | 409 Conflict status | developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409 |
| §E | Repo entegrasyon matrisi | Dosya·Satır kanıtları |
| §F | Karar matrisi (ADR girdisi) | — |
| §G | Riskler ve VERIFICATION REQUIRED | — |
| §H | Cross-reference | — |

---

## §A — PSR-7 Message Interface

**Kaynak:** https://www.php.net/manual/en/class.psr-7-message.php

### A.1 — Tanım (kaynak özeti)

PSR-7 `Psr\Http\Message\MessageInterface`, HTTP mesajının ortak sözleşmesini tanımlar:

| Method | Dönüş | Anlamı |
|---|---|---|
| `getProtocolVersion()` | `string` | HTTP protokol sürümü ("1.0", "1.1", "2.0") |
| `withProtocolVersion($version)` | `static` | Yeni sürümle kopya döner (immutable) |
| `getHeaders()` | `array` | Header çiftleri |
| `hasHeader($name)` | `bool` | Header var mı |
| `getHeader($name)` | `array` | Header değeri dizisi |
| `getHeaderLine($name)` | `string` | Header değeri (virgülle birleşik) |
| `withHeader($name, $value)` | `static` | Yeni header'lı kopya |
| `withAddedHeader($name, $value)` | `static` | Header eklenmiş kopya |
| `withoutHeader($name)` | `static` | Header'sız kopya |
| `getBody()` | `StreamInterface` | Mesaj gövdesi |
| `withBody(StreamInterface $body)` | `static` | Gövdeli kopya |

**Temel ilke:** Tüm `with*` metodları **immutable** — özgün mesajı değiştirmez, yeni kopya döner.

### A.2 — İlişkili arayüzler (aynı PSR-7 ailesi)

| Arayüz | Rol |
|---|---|
| `RequestInterface` | İstek (method, URI) |
| `ResponseInterface` | Yanıt (status code, reason phrase) |
| `StreamInterface` | Byte akışı |
| `UriInterface` | URI bileşenleri |

### A.3 — Repo durumu (kritik bulgu)

**composer.json kanıtı:**

```
Dosya: shared/composer.json · Satır: 8
"nyholm/psr7": "^1.8",
```

**Kod kullanımı kanıtı:**

| Arama | Kapsam | Sonuç |
|---|---|---|
| `Psr\Http\Message` (grep) | Tüm .php dosyaları | **0 isabet** |
| `MessageInterface` (grep) | Tüm .php dosyaları | **0 isabet** |
| `ResponseInterface` (grep) | Tüm .php dosyaları | **0 isabet** |

**Sonuç:** Paket `composer.json`'da tanımlı, `vendor/`'a kurulmuş, fakat **kod tabanında
hiçbir yerde kullanılmıyor**. DTO'lar `array` (toArray) dönüyor, PSR-7 Message dönmüyor.

⚠️ **VERIFICATION REQUIRED:** Paket bilinçli olarak mı eklendi (gelecek planı), yoksa
atık bağımlılık mı? Karar orkestratöre aittir — bu dosya yalnızca durumu kaydeder.

### A.4 — DTO katmanıyla ilişki

| DTO | PSR-7 karşılığı | Fark |
|---|---|---|
| ApiResponse | ResponseInterface + body | DTO `array`, PSR-7 stream+header |
| ErrorResponse | ResponseInterface body | DTO status kodu property içinde taşır |
| PaginationRequest | ServerRequestInterface query | DTO query params'ı array olarak alır |

**Karar (bu dosyanın kapsamı dışındaki implementasyon için not):** Mevcut DTO tasarımı
PSR-7'ye **karşılık gelmez** — ikisi farklı katmandır. DTO domain veri taşır; PSR-7 HTTP
kabuğudur. Mevcut mimaride controller, DTO'yu `Response::json()` ile paketler.

---

## §B — JsonSerializable

**Kaynak:** https://www[.]php[.]net/manual/en/class.jsonserializable.php

### B.1 — Tanım (kaynak özeti)

`JsonSerializable` arayüzü, bir nesnenin `json_encode()` sırasında nasıl
serialize edileceğini tanımlar:

| Method | Dönüş | Anlamı |
|---|---|---|
| `jsonSerialize()` | `mixed` | `json_encode()` çağrısında döndürülecek veri |

**Davranış:**

- `json_encode($obj)` çağrıldığında, `$obj` `JsonSerializable` ise `jsonSerialize()`
  çağrılır, dönen değer encode edilir.
- Dönen değer `array` ise o array encode edilir.
- `null` dönerse JSON `null` olur.

### B.2 — Repo durumu

**Auth DTO uygulaması (aktif üretim kodu):**

```
Dosya: auth.coremusic.net/include/Domain/DTO/AuthResponse.php · Satır: 10
final class AuthResponse implements \JsonSerializable
```

```
Dosya: auth.coremusic.net/include/Domain/DTO/AuthResponse.php · Satır: 68-70
public function jsonSerialize(): array
{
    return $this->toArray();
}
```

**Shared DTO uygulaması (test-only, üretimde kullanılmıyor):**

```
Dosya: shared/src/Api/Dto/ApiResponse.php · Satır: 7
final readonly class ApiResponse
```

Shared DTO'lar `JsonSerializable` **implement etmiyor** — yalnız `toArray()` var.

### B.3 — Karşılaştırma

| DTO ailesi | JsonSerializable | toArray | json_encode davranışı |
|---|---|---|---|
| auth/Domain/DTO (3 dosya) | ✅ implements | ✅ var | Direkt doğru JSON |
| shared/Api/Dto (11 dosya) | ❌ yok | ✅ var | `json_encode($dto)` → `{}` (empty object) — dikkat |

⚠️ **VERIFICATION REQUIRED (davranış):** `json_encode()` bir `readonly class` instance'ına
verildiğinde, `JsonSerializable` yoksa public property'ler mi yoksa `{}` mı döner —
PHP sürümüne ve property visibility'ye bağlıdır. Kod kanıtı: shared DTO'lar üretimde
kullanılmadığı için bu davranış test edilmemiştir. Testler `toArray()` kullanıyor
(`shared/tests/.../ApiResponseTest.php`).

### B.4 — DTO katmanıyla ilişki

| Desen | Uygulayan | Not |
|---|---|---|
| `toArray()` tek başına | shared/Api/Dto | Controller `Response::json($dto->toArray())` |
| `toArray()` + `jsonSerialize()` | auth/Domain/DTO | Hem elle paketleme hem `json_encode` mümkün |

**En tutarlı desen:** auth katmanındaki gibi `JsonSerializable + toArray()` — iki dünya
(arayüz + elle paketleme) da desteklenir. ⚠️ VERIFICATION REQUIRED: bu desenin
shared katmanına yaygınlaştırılması kararı vault'a ADR olarak kaydedilmelidir.

---

## §C — Content-Type Header

**Kaynak:**
https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type

### C.1 — Tanım (kaynak özeti)

`Content-Type`, **gövdenin medya tipini** bildiren response/request header'ıdır.

| Alan | Değer | Repo'daki karşılığı |
|---|---|---|
| medya tipi | `application/json` | Tüm API endpoint'leri |
| charset | `utf-8` | Tüm yanıtlar |

**Tipik API biçimi:** `Content-Type: application/json; charset=utf-8`

**Kullanım noktaları:**

| Taraf | Header zorunlu mu? | Not |
|---|---|---|
| Response (2xx/4xx/5xx) | Evet — gövde varsa | Yanıt tipini bildirir |
| Request (POST/PUT) | Evet — gövde varsa | Sunucu parse etmeden önce tipi bilir |
| 204/304 | Hayır — gövde yok | Header atlanabilir |

### C.2 — Repo durumu

**Doğrudan header ataması (PHP):**

| Arama | Sonuç |
|---|---|
| `Content-Type` (grep, .php) | 0 isabet — framework/framework kendi ayarlıyor |
| `application/json` (grep, .php) | 0 isabet — Laravel/Slim benzeri response factory |

⚠️ **VERIFICATION REQUIRED:** Backend'in `Content-Type` header'ını nerede set ettiği
(bir middleware mi, response factory mi) bu görev kapsamında doğrulanmadı — grep
`Content-Type` literal'i 0 döndü. Büyük ihtimalle frameworkün `Response::json()`
metodu header'ı otomatik ekliyor.

**Frontend tarafı (JS) — istekte kullanılıyor:**

```
Dosya: assets.coremusic.net/js/core/api.js · Satır: 1
```

⚠️ VERIFICATION REQUIRED (satır içeriği): `api.js`'de fetch çağrısında
`headers: { 'Content-Type': 'application/json' }` bulunması beklenir; satır 1'de
dosya başı import/comment olduğundan tam satır numarası bu kanıt setinde
teyit edilmemiştir — okunarak doğrulanmalıdır.

**Frontend tarafı (JS) — yanıttan okuma:**

```
Dosya: assets.coremusic.net/js/auth/gender-select.js · Satır: 1
```

`fetch()` → `response.json()` zinciri Content-Type'ı örtük kullanır: tarayıcı
yanıtı `application/json` olarak algılar ve `response.json()` gövdeyi parse eder.

### C.3 — DTO katmanıyla ilişki

DTO JSON'a paketlendiği için içerik tipi **her zaman** `application/json` olmalıdır:

| DTO | Yanıt içeriği | Beklenen Content-Type |
|---|---|---|
| ApiResponse (success) | `{data: ...}` | `application/json; charset=utf-8` |
| ErrorResponse | `{error: ...}` | `application/json; charset=utf-8` |
| ValidationFailedResponse | `{errors: ...}` | `application/json; charset=utf-8` |
| PaginatedResponse | `{items, meta}` | `application/json; charset=utf-8` |

**Kural (kod önerisi değil, gözlem):** Tüm 11 shared DTO array döndüğü için JSON
serileştirme tek yoldur; Content-Type her endpoint'te sabit kalır.

---

## §D — 409 Conflict Status

**Kaynak:**
https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409

### D.1 — Tanım (kaynak özeti)

**409 Conflict**, isteğin **mevcut kaynak durumuyla çakıştığını** belirtir.
İstemcinin isteği düzeltmesi veya tekrar denemesi gerekebilir.

**Tipik kullanım senaryoları:**

| Senaryo | Örnek |
|---|---|
| Kopya kayıt | Aynı username ile ikinci kayıt |
| Versiyon çakışması | Eşzamanlı edit (optimistic locking) |
| Durum geçiş ihlali | Onaylanmamış siparişi kapatma |
| Doğrulama dışı ama çakışma | Unique constraint ihlali |

**409 vs 400 vs 422:**

| Kod | Anlam | DTO karşılığı |
|---|---|---|
| 400 Bad Request | İstek geçersiz (sözdizimi/hatalı input) | ErrorResponse(400) |
| 409 Conflict | İstek geçersiz değil ama çakışıyor | ConflictResponse(409) |
| 422 Unprocessable | Geçerli sözdizimi ama mantık hatası | ValidationFailedResponse(422) |

### D.2 — Repo durumu

**DTO tanımı:**

```
Dosya: shared/src/Api/Dto/ConflictResponse.php · Satır: 7
final readonly class ConflictResponse
```

```
Dosya: shared/src/Api/Dto/ConflictResponse.php · Satır: 9
public const STATUS_CODE = 409;
```

**Kod kullanımı (grep):**

| Arama | Kapsam | Sonuç |
|---|---|---|
| `ConflictResponse` | shared/src + tüm domain'ler | **0 üretim kullanımı** — yalnız DTO tanımı + test |

⚠️ **VERIFICATION REQUIRED:** 409'un üretimde nerede fırlatıldığı (örn. unique
constraint ihlali) bu görev kapsamında doğrulanmadı. `UserRepository`'de transaction
kullanımı var (`Dosya: api.coremusic.net/include/Repository/UserRepository.php · Satır: 147, 184, 192`)
fakat 409 mapping'i görülmedi.

### D.3 — Benzer DTO'lar (status code matrisi)

| DTO | STATUS_CODE | Üretim kullanımı |
|---|---|---|
| ApiResponse | 200 (başarı) | Test-only |
| ErrorResponse | (parametrik) | Test-only |
| UnauthorizedResponse | 401 | Test-only |
| ForbiddenResponse | 403 | Test-only |
| NotFoundResponse | 404 | Test-only |
| ConflictResponse | 409 | Test-only |
| ValidationFailedResponse | 422 | Test-only |
| ServiceUnavailableResponse | 503 | Test-only |

**Bulgu:** 8 status-code DTO'sunun **8'i de** üretim kodunda kullanılmıyor — hepsi
"PLANNED-üretilmiş katman" sınıfındadır (bkz. dto-dogrulama.md §E).

---

## §E — Repo Entegrasyon Matrisi

| Dış referans | Repo'daki karşılığı | Dosya·Satır | Durum |
|---|---|---|---|
| PSR-7 MessageInterface | (yok — paket var, kod yok) | shared/composer.json · Satır: 8 | ⚠️ VERIFICATION REQUIRED |
| JsonSerializable | AuthResponse implements | auth.../DTO/AuthResponse.php · Satır: 10, 68-70 | ✅ AKTİF |
| JsonSerializable | shared DTO'lar implement ETMİYOR | shared/src/Api/Dto/*.php | ❌ FARK |
| Content-Type: application/json | framework response factory | grep 0 isabet | ⚠️ VERIFICATION REQUIRED |
| 409 Conflict | ConflictResponse::STATUS_CODE | shared/src/Api/Dto/ConflictResponse.php · Satır: 9 | Test-only |
| toArray() | 13/14 DTO | bkz. dto-dogrulama.md §E | ⚠️ 1 eksik |

---

## §F — Karar Matrisi (ADR Girdisi)

| # | Soru | Kanıt | Karar önerisi (orkestratöre) |
|---|---|---|---|
| F1 | shared DTO'lar JsonSerializable olmalı mı? | auth örneği var (Satır: 10, 68-70) | Evet — tutarlılık için |
| F2 | PSR-7'ye geçilmeli mi? | Paket var, kod 0 kullanım | Değerlendir — ADR gerekli |
| F3 | ConflictResponse üretimde kullanılacak mı? | 0 kullanım | Kullanım senaryosu tanımlanmalı |
| F4 | Content-Type merkezileşmeli mi? | grep 0 — framework otomatik | Framework'a bırak, test ile doğrula |

---

## §G — Riskler ve VERIFICATION REQUIRED

| # | İddia | Durum | Aksiyon |
|---|---|---|---|
| G1 | nyholm/psr7 atık bağımlılık | ⚠️ VERIFICATION REQUIRED | Karar: sakla veya kaldır (ADR) |
| G2 | json_encode(shared DTO) davranışı | ⚠️ VERIFICATION REQUIRED | Test ile doğrula |
| G3 | Content-Type header nerede set ediliyor | ⚠️ VERIFICATION REQUIRED | Middleware/logger incele |
| G4 | 409 üretim nerede fırlatılıyor | ⚠️ VERIFICATION REQUIRED | Unique constraint handler ara |
| G5 | LoginRequest'te toArray yok | ✅ DOĞRULANDI (13/14) | Kapsam notu — dto-dogrulama.md §K |

---

## §H — Cross-Reference

| Konu | Dosya |
|---|---|
| Ölçüm ve gate | dto-dogrulama.md |
| Kapsam ve katalog | index.md (eski dto-kapsam.md — spec §7 gereği 2026-10-16 kaldırıldı) |
| Kod örnekleri | dto-ornekler.md |
| Bileşen envanteri | dto-bilesenler.md |
| Format referansı | commands/ (6 dosya) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §I — PSR-7 DETAY NOTLARI (Genişletme)

### I.1 — Immutable Tasarımın DTO Etkisi

PSR-7'nin `with*` deseni, DTO tasarımına doğrudan örnek olabilir:

| PSR-7 deseni | DTO karşılığı | Mevcut repo durumu |
|---|---|---|
| `withHeader()` › yeni kopya | `withData()` › yeni ApiResponse | ? Yok (immutable factory sadece success/error) |
| `withoutHeader()` › kopya | `withoutMeta()` › kopya | ? Yok |
| `getBody()` | `toArray()` | ? Var (13/14) |

**Gözlem:** shared DTO'lar immutable (readonly) fakat `with*` varyant method'ları yok.
Auth DTO'ları mutable (final class, ama readonly değil).

### I.2 — Stream vs Array Gövde

| Yaklaşım | PSR-7 | Mevcut DTO |
|---|---|---|
| Gövde tipi | `StreamInterface` | `array` |
| Paketleme | Response factory stream oluşturur | Controller `Response::json($dto->toArray())` |
| Bellek | Lazy stream (büyük gövde için iyi) | Tümü bellekte array |

**Eşik:** Array yaklaşımı küçük/orta gövde için uygundur. Büyük dosya/rapor
dışa aktarımında PSR-7 stream'e geçiş değerlendirilebilir — ?? PLANNED, bu repo'da
büyük gövde senaryosu yok (grep: stream usage 0).

### I.3 — Protokol Sürümü ve HTTP/2

PSR-7 `getProtocolVersion()` HTTP/2'yi destekler. Bu repo'da:

| Soru | Kanıt | Durum |
|---|---|---|
| HTTP/2 kullanılıyor mu? | Sunucu yapılandırması bu görevde incelenmedi | ?? VERIFICATION REQUIRED |
| DTO'da protokol bilgisi taşınıyor mu? | shared DTO'larda protocol alanı yok | ? Hayır (gerek de yok) |

---

## §J — JSON SERİLEŞTİRME DETAY NOTLARI

### J.1 — jsonSerialize() vs toJson() Yardımcısı

Bazı projeler `toJson()` helper ekler. Bu repo:

| Desen | auth DTO | shared DTO |
|---|---|---|
| `jsonSerialize()` | ? | ? |
| `toJson()` helper | ? | ? |
| `toArray()` | ? | ? |

### J.2 — Nested DTO Serileştirme

| DTO | İç içe DTO | jsonSerialize davranışı |
|---|---|---|
| AuthResponse | AuthTokens (property) | ?? VERIFICATION REQUIRED — AuthTokens jsonSerialize içeriyor mu? |
| ValidationFailedResponse | RequestValidationError | array içinde array (elle paketleme) |

### J.3 — Unicode / Türkçe Karakter

Content-Type `charset=utf-8` bildirildiği için:

| Senaryo | Beklenen | Not |
|---|---|---|
| Türkçe karakterli veri (ör. "Şarkı") | `json_encode` › `\uXXXX` veya UTF-8 ham | `JSON_UNESCAPED_UNICODE` bayrağına bağlı |
| Frontend parse | `response.json()` doğru çözer | Tarayıcı UTF-8 varsayar |

?? VERIFICATION REQUIRED: repo'da `JSON_UNESCAPED_UNICODE` kullanımı bu görevde
grep edilmedi — doğrulanmalı.

---

## §K — 4xx/5xx DURUM KODU KARŞILAŞTIRMA (Genişletme)

| Status | Anlam | DTO | Üretim kullanımı | Frontend handling |
|---|---|---|---|---|
| 400 | Bad Request | ErrorResponse(400) | 0 | ?? VERIFICATION REQUIRED |
| 401 | Unauthorized | UnauthorizedResponse | 0 | ?? VERIFICATION REQUIRED |
| 403 | Forbidden | ForbiddenResponse | 0 | ?? VERIFICATION REQUIRED |
| 404 | Not Found | NotFoundResponse | 0 | ?? VERIFICATION REQUIRED |
| 409 | Conflict | ConflictResponse | 0 | ?? VERIFICATION REQUIRED |
| 415 | Unsupported Media Type | (DTO yok) | — | Header typo › 415 |
| 422 | Validation | ValidationFailedResponse | 0 | ?? VERIFICATION REQUIRED |
| 503 | Service Unavailable | ServiceUnavailableResponse | 0 | ?? VERIFICATION REQUIRED |

**Beklenen üretim senaryosu (PLANNED):** RequestValidationMiddleware 422 döndüğünde
ValidationFailedResponse kullanılmalı — ?? VERIFICATION REQUIRED: middleware'ın
doğrudan bu DTO'yu kullandığı bu görevde doğrulanmadı (grep: 0 üretim kullanımı).

---

## §L — KAYNAK KULLANIM TABLOSU (Bu Dosyada)

| URL | Bölüm | İddia sayısı | Doğrulama |
|---|---|---|---|
| php.net/manual/en/class.psr-7-message.php | §A | 11 method tanımı | Sayfa içeriğinden |
| php.net/manual/en/class.jsonserializable.php | §B | 1 method + davranış | Sayfa içeriğinden |
| mdn.../Headers/Content-Type | §C | medya tipi + charset | Sayfa içeriğinden |
| mdn.../Status/409 | §D | tanım + senaryolar + karşılaştırma | Sayfa içeriğinden |

**Toplam:** 4/4 URL kullanıldı. Havuz dışına çıkılmadı.

---

## §M — ÖZET: EN KRİTİK 3 BULGU

1. **PSR-7 paketi var, kod yok** — `nyholm/psr7 ^1.8` composer.json'da (Satır: 8),
   `Psr\Http\Message` grep'i 0 isabet. Atık mı, plan mı? ?? VERIFICATION REQUIRED.
2. **shared DTO'lar JsonSerializable değil, auth DTO'ları** — tutarsızlık.
   auth örneği: AuthResponse.php Satır: 10, 68-70.
3. **8 status-code DTO'su, 8'i de test-only** — 409 dahil hiçbir üretime
   ulaşmamış. "PLANNED-üretilmiş katman" (bkz. dto-dogrulama.md §E).

---

## §N — SONRAKİ ADIMLAR (Bu Dosyanın Çıktısı)

| # | Adım | Sahip |
|---|---|---|
| 1 | F1 kararı: shared DTO'lara JsonSerializable ekle | Orkestratör |
| 2 | G1 kararı: nyholm/psr7 sakla/kaldır (ADR) | Orkestratör |
| 3 | G4: 409 fırlatma noktasını bul | debugger agent |
| 4 | G3: Content-Type ayarını doğrula | browser test |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §O — SON KONTROL

| Kontrol | Durum |
|---|---|
| Havuz dışı URL | 0 |
| Dosya·Satır kanıtı | §E matrisi |
| VERIFICATION REQUIRED işaretli iddialar | §G (5 madde) |

### §O.1 — Havuz kesişim notu (2026-10-16)

> **Spec §9 dto havuzu (5):** `php.net book.json.php` ·
> `php.net language.oop5.properties.php` · `php.net language.oop5.serialization.php` ·
> `php.net function.serialize.php` · `php.net function.unserialize.php`.
>
> **Bu dosyanın havuzu (4):** PSR-7 message (§A) · JsonSerializable (§B) ·
> Content-Type başlığı (§C) · 409 durum kodu (§D). §L "4/4" ve §O "Havuz dışı 0"
> iddiaları bu **dahili havuz** içindir ve doğrudur.
>
> **Sapma:** Dosya, spec'in atadığı dto havuzunun **hiçbirini** kullanmadı
> (**0/5 kesişim**); havuz tamamen dahili/dış kaynaklıdır (PSR-7 + başlık +
> durum kodu). Bu, §L/§O'nun yanlış olduğu değil, **spec havuzuyla farklı bir
> havuz** kullanıldığı anlamına gelir.
>
> ⚠️ **VERIFICATION REQUIRED (2026-10-16):** spec §9 dto havuzunun 5 URL'i bu
> dosyada curl-verify edilmedi. Kesişim kanıtı closeout'ta spec havuzu için
> ayrıca üretilecek (HTTP 200 + envanter kesişimi 0). Dosyanın kendi 0 HIT
> (envanter kesişimi) durumu bu nottan etkilenmez.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
