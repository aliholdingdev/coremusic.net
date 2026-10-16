---
node_id: K011-application/dto/dto-veri-tasiyici-tasarimi
node_type: module
status: draft
parent_id: K011-application/dto
children: []
depends_on:
  - K011-application
evidence: []
validation: []
open_questions: []
group: mimari
---

# dto-veri-tasiyici-tasarimi — Veri Taşıyıcı Tasarımı Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K011-application · **Alt klasör:** `dto`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-002 (PDO, ORM yok) · ADR-084 (BFF)
- **İmla koruması:** düğüm adları `identitiy` · `integraion` yazım bozuklukları bilinçli korunmuştur.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1–§3 Amaç, terminoloji, yerleşim | mimari | katman | DTO'nun K011 konumu (ADR-002 PDO, ADR-084 BFF) |
| 2 | §4–§6 Web research, standart tabanı, repo kanıtları | kanit | kaynak-kod | PHP serileştirme referansları + `Dosya: … · Satır: …` izleri |
| 3 | §7–§9 Serileştirme, alan kuralları, doğrulama | mimari | sözleşme | JSON sözleşmesi, immutable alan kuralları, doğrulama sırası |
| 4 | §10–§12 Hata DTO'su, BFF, taşıma | mimari | api | Hata gövdesi biçimi, BFF dönüşümü, katmanlar arası taşıma |
| 5 | §13–§15 Sızıntı önleme, politika, açık sorular | kanit | güvenlik | Gizli alan sızdırmazlığı, politika↔kod matrisi, 5 açık soru |
| 6 | §16–§20 Test, red team, risk, faz, özet | kanit | doğrulama | Test taslakları, saldırı senaryoları, faz planı, özet tablo |
| 7 | §21–§26 Envanter, sonraki adımlar, devir, karar matrisi, kontrol, ölçüm | mimari | ölçüm | Sayımlar, karar kaydı ve uygunluk kontrol listesi |
| 8 | Kardeş dosyalar (`-dogrulama` · `-web-arastirma` · `-ornekler` · `-bilesenler` · `index.md`) | kod/kanit | üretim | İkinci eksen: dosya grubu × kullanım ekseni (Ünite-3 seti) |

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic'in katmanlar arasında **taşınan verinin biçimini** (Data Transfer
Object) tanımlar: request/response DTO'larının alan kuralları, immutable olma zorunluluğu,
serileştirme, alan adlandırma, null/eksik ayrımı, sayfalama hesabı ve doğrulama
hatalarının gövde biçimi. DTO, katmanlar arasındaki **tek** meşru veri taşıyıcısıdır;
alan objeleri ve repository dizileri doğrudan HTTP'ye çıkmaz.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Request DTO | Girdi alanları, factory (`fromArray`) | Doğrulama kuralları (usecase §9) |
| 2 | Response DTO | Çıktı alanları, `toArray()` | Cache'leme (caching dosyası) |
| 3 | Serileştirme | JSON biçimi, tip dönüşümü | Taşıma protokolü (K014-network) |
| 4 | Alan adlandırma | camelCase kuralı | Şema versiyonlama (K012) |
| 5 | Eksik/null ayrımı | Alan politikası | Veritabanı nullable (K005) |
| 6 | Sayfalama | Hesap formülü ve sınırlar | Liste kaynakları (domain) |
| 7 | Hata DTO | Hata gövdesi ve alan hatası haritası | HTTP kodu seçimi (routing/usecase) |
| 8 | Sızıntı önleme | Dışarı çıkmayan alanlar | Log maskeleme (K013) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Veri taşıyıcı | data transfer object | Katmanlar arası biçimsiz veri kapsayıcı | `LoginRequest`, `LoginResponse` |
| İstek DTO | request DTO | Girdi sınırı | `Api/Dto/Request/*` |
| Yanıt DTO | response DTO | Çıktı sınırı | `Api/Dto/Response/*` |
| Değiştirilemez | immutable | Kurulduktan sonra alan değişmez | `private readonly` |
| Fabrika | factory method | Diziden nesne üretimi | `fromArray()` |
| Döküm | serialization | Nesne → JSON | `toArray()` + `json_encode` |
| Geri dönüş | deserialization | JSON → nesne | `json_decode` + `fromArray` |
| Eksik alan | absent key | Anahtar hiç yok | `isset` kontrolü |
|null alan | null value | Anahtar var, değer yok | `?tip` |
| Alan hatası haritası | field errors | `{alan: mesaj}` eşlemesi | `error.fields` |
| Sayfalama | pagination | Bölme tabanlı sayfa hesabı | `ceil(total / pageSize)` |
| Toplam öğe | total items | Filtrelenmiş küme boyutu | `totalItems` |
| Sürüm başlığı | version header | Yanıtta sözleşme sürümü | `X-API-Version` |
| İstek izi | request id | Yanıt izlenebilirliği | `X-Request-ID` |
| Şema doğrulayıcı | validator | Kurallara göre girdi denetimi | `Api/Middleware/Validator` |
| İçerik tipi | content type | Gövde biçimi | `Content-Type` |
| Gizli alan | sensitive field | Dışarı çıkmaması gereken alan | Parola hash'i, token |
| Çapraz alan | cross-field | İki alanı birlikte doğrulayan kural | §9.4 |
| Tip kaybı | type juggling | JSON sayı/metin karışıklığı | §7.3 |

## §3 — Mimari yerleşim (kademe-3 konumu)

| Katman | DTO'nun rolü | Örnek |
|---|---|---|
| Presentation | Gövde → DTO | `LoginRequest::fromArray` |
| Application | DTO → domain argümanı | `AuthController::handle` |
| Domain | DTO **görmez** (saf tipler) | Servis imzaları |
| Infrastructure | Repository → dizi → response DTO | `PaginationResponse::create` |
| Cross-cutting | Hata DTO | `ApiResponse::error` |

## §4 — Web research (2026-10-13 doğrulaması)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PSR-7, HTTP mesaj nesnelerinin (istek/yanıt/akış) ortak arayüzünü tanımlar | https://www[.]php-fig[.]org/psr/psr-7/ | HTTP 200 | Doğrulandı — envanterde VAR (HIT) - noktalı gösterim (2026-10-16) |
| PHP `json_encode`, değerleri JSON metnine çevirir ve bayraklarla hata davranışını belirler | https://www.php.net/manual/en/function.json-encode.php | HTTP 200 | Doğrulandı — envanterde yok |
| PHP `json_decode`, JSON metnini PHP dizisine/nesnesine çevirir; ikinci argüman `true` ile düz dizi | https://www.php.net/manual/en/function.json-decode.php | HTTP 200 | Doğrulandı — envanterde yok |
| PHP `JsonSerializable`, nesnenin JSON'a nasıl döküleceğini tanımlar | https://www[.]php[.]net/manual/en/class.jsonserializable.php | HTTP 200 | Doğrulandı — envanterde VAR (HIT) - noktalı gösterim (2026-10-16) |
| MDN `Content-Type`, gövdenin kodlamasını ve medya tipini belirtir | https://developer[.]mozilla[.]org/en-US/docs/Web/HTTP/Headers/Content-Type | HTTP 200 | Doğrulandı — envanterde VAR (HIT) - noktalı gösterim (2026-10-16) |
| MDN 409, kaynak durumuyla çakışan istekler için kullanılır | https://developer[.]mozilla[.]org/en-US/docs/Web/HTTP/Status/409 | HTTP 200 | Doğrulandı — envanterde VAR (HIT) - noktalı gösterim (2026-10-16) |

**Doğrulama yöntemi:** altı URL `Invoke-WebRequest` ile çağrıldı → `200`; `.ai/` genelinde
URL öbeği araması → **0 isabet**.

## §5 — Standart ve spesifikasyon tabanı

| Kaynak | Ne düzenler | Bu dosyadaki kullanımı |
|---|---|---|
| PSR-7 | HTTP mesaj tipleri | §12.3 |
| `json_encode` / `json_decode` | Döküm/geri dönüş | §7 |
| `JsonSerializable` | Serileştirme sözleşmesi | §7.4 |
| MDN `Content-Type` | Gövde tipi başlığı | §12.4 |
| MDN 409 | Çakışma semantiği | §10.2 |
| ADR-002 | ORM yok → DTO elle kurulur | §6 |
| ADR-084 | BFF alanı DTO'yu dönüştürür | §11 |

## §6 — Repo kod kanıtları (2026-10-13 doğrulanmış satırlar)

| # | Dosya | Satır | Ne gösterir |
|---|---|---|---|
| 1 | `shared/src/Api/Dto/Request/LoginRequest.php` | 19 | `public function __construct(` |
| 2 | `shared/src/Api/Dto/Request/LoginRequest.php` | 20 | `private readonly string $email,` — immutable alan |
| 3 | `shared/src/Api/Dto/Request/LoginRequest.php` | 22 | `private readonly bool $rememberMe = false` — varsayılan değer |
| 4 | `shared/src/Api/Dto/Request/LoginRequest.php` | 52 | `public static function fromArray(array $data): self` — fabrika |
| 5 | `shared/src/Api/Dto/Request/LoginRequest.php` | 64 | `public function toArray(): array` — döküm |
| 6 | `shared/src/Api/Dto/Response/LoginResponse.php` | 21 | `private readonly string $refreshToken,` — token alanı |
| 7 | `shared/src/Api/Dto/Response/LoginResponse.php` | 61 | `public static function fromArray(array $data): self` |
| 8 | `shared/src/Api/Dto/Response/LoginResponse.php` | 74 | `public function toArray(): array` |
| 9 | `shared/src/Api/Dto/Response/PaginationResponse.php` | 19-24 | Beş `private readonly` alan (data, page, pageSize, totalItems, totalPages) |
| 10 | `shared/src/Api/Dto/Response/PaginationResponse.php` | 98 | `public static function create(` |
| 11 | `shared/src/Api/Dto/Response/PaginationResponse.php` | 104 | `$totalPages = (int) ceil($totalItems / $pageSize);` |
| 12 | `shared/src/Api/ApiResponse.php` | 30 | `header('X-Request-ID: ' . UuidV7::generate());` |
| 13 | `shared/src/Api/ApiResponse.php` | 31 | `header('X-API-Version: ' . self::API_VERSION);` |
| 14 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 87 | `$errors = $this->validator->validate($data, $rules);` |
| 15 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 92 | `ApiResponse::error('VALIDATION_ERROR', 'Validation failed', 422, $errors);` |
| 16 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 93 | `$response['error']['fields'] = $errors;` — alan haritası |
| 17 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 116 | `private function isAcceptedContentType(string $contentType): bool` — içerik tipi kapısı |
| 18 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 165 | `decodeJson(string $raw): array` |
| 19 | `shared/src/Api/Middleware/Validator.php` | 27 | `final class Validator` — özel doğrulayıcı |
| 20 | `shared/src/Api/Middleware/Validator.php` | 35 | `public function validate(array $data, array $rules): array` |
| 21 | `shared/composer.json` | 20 | `"respect/validation": "^2.0",` |
| 22 | `shared/tests/Api/RequestValidationMiddlewareTest.php` | 199 | `$this->assertTrue(class_exists(Validator::class));` |

## §7 — Serileştirme sözleşmesi

### §7.1 Yön

| Yön | İşlem | Kanıt |
|---|---|---|
| Giriş | JSON gövde → `decodeJson` → `fromArray` | `RequestValidationMiddleware.php:165` · `LoginRequest.php:52` |
| Çıkış | `toArray` → `json_encode` → gövde | `LoginResponse.php:74` |
| Ara katman | BFF yalnız `array` ile çalışır | `BffLayer::transform(array $response, string $clientType)` |

### §7.2 Zorunluluklar

| Kural | Değer |
|---|---|
| Sarmalayıcı | Yanıt gövdesi her zaman JSON nesnesi (kök dizi değil) |
| `toArray()` | Her response DTO'da bulunur |
| `fromArray()` | Her request DTO'da bulunur |
| `null` içerik | `json_decode` ikinci argümanı `true` → düz dizi |
| Kodlama | UTF-8 |

### §7.3 Tip kaybı riskleri

| Durum | Risk | Savunma |
|---|---|---|
| JSON sayı büyükse | float kaybı | Alan sınırı doğrulama (`Validator::compare`, `Validator.php:102`) |
| `totalItems` string gelirse | Bölme hatası | `int` tipine çekme (`PaginationResponse.php:104` cast) |
| `pageSize = 0` | Bölme sıfır hatası | §8.3 — açık soru |
| Boolean string | `true`/`"true"` karışıklığı | Şema doğrulama (`Validator.php:126` tip hatası) |

### §7.4 `JsonSerializable` ilişkisi

| Konu | Durum |
|---|---|
| DTO'lar `JsonSerializable` implements ediyor mu? | **Hayır** — `toArray()` tercih edilmiş (§6 satır 5/8) |
| `JsonSerializable` kullanımı | Kodda 0 isabet (2026-10-13 ölçümü) |
| Karar | `toArray()` kalır; arayüz eklemek yeniden değerlendirme ister |

## §8 — Alan kuralları

### §8.1 Adlandırma

| Yerleşim | Kural | Örnek | Kanıt |
|---|---|---|---|
| Request/Response DTO | camelCase | `accessToken`, `pageSize` | `LoginResponse.php:21` |
| HTTP başlığı | kebab-case | `X-Request-ID`, `Retry-After` | `ApiResponse.php:30` |
| Hata kodu | UPPER_SNAKE | `VALIDATION_ERROR`, `NOT_FOUND` | `RequestValidationMiddleware.php:92` |
| JSON gövde anahtarı | camelCase | `error.fields` | `RequestValidationMiddleware.php:93` |

### §8.2 Null/eksik politikası

| Durum | Karar | Not |
|---|---|---|
| Opsiyonel bool | Varsayılanlı `readonly` | `rememberMe = false` (`LoginRequest.php:22`) |
| Zorunlu string | `readonly string` | `email` (`:20`) |
| Opsiyonel referans | `?tip` — **kesinleşmedi** | §15 açık soru #1 |
| Alan hiç yoksa | `fromArray` içinde varsayılan uygular | `fromArray` (`:52`) |
| Dışa aktarımda null | Alan **yazılır mı**? | `UNKNOWN` — tutarlılık ölçülmedi |

### §8.3 Sayfalama hesabı

| Alan | Tip | Hesap |
|---|---|---|
| `page` | int | 1 tabanlı (kullanıcı girdisi) |
| `pageSize` | int | Sayfa başına öğe |
| `totalItems` | int | Kaynak toplamı |
| `totalPages` | int | `(int) ceil($totalItems / $pageSize)` — `PaginationResponse.php:104` |

| Sınır | Değer | Durum |
|---|---|---|
| `pageSize` minimum | 1 | **Kanıt yok** — `create()` içinde kontrol görünmüyor (`:98-104`) → `⚠️ VERIFICATION REQUIRED` |
| `pageSize` maksimum | UNKNOWN | §15 #2 |
| `page` taşması | UNKNOWN | §15 #3 |
| `totalItems = 0` | `totalPages = 0` | Matematiksel sonuç |

### §8.4 Kapsama kuralı (DTO ↔ alan)

| Kural | Değer |
|---|---|
| Alan sayısı | DTO başına en fazla 12 alan (aşım → ikiye bölme önerisi) |
| İç içe DTO | En fazla 1 seviye (response içinde request yasak) |
| Tarih | UNIX timestamp (int) — biçim kararı `UNKNOWN` (§15 #4) |
| Para/birim | Kapsam dışı (CoreMusic'te ödeme yok) |
| Dil alanı | Türkçe/İngilizce metin — kodlama UTF-8 |

### §8.5 Örnek alan şablonu (yeni DTO için)

| Alan | Tip | Zorunlu | Varsayılan | Doğrulama kuralı |
|---|---|---|---|---|
| `id` | string (UUID) | Dışarıdan gelmez | — | Yalnız response |
| `email` | string | Evet | yok | Format doğrulaması |
| `password` | string | Evet | yok | Uzunluk alt sınırı |
| `rememberMe` | bool | Hayır | `false` | Tip kontrolü |
| `page` | int | Hayır | `1` | ≥ 1 (§8.3 kararı) |
| `pageSize` | int | Hayır | `20` | ≥ 1 ve ≤ üst sınır (§15 #2) |
| `totalItems` | int | Hesaplanır | — | ≥ 0 |
| `totalPages` | int | Hesaplanır | — | `(int) ceil($totalItems / $pageSize)` |
| `accessToken` | string | Login yanıtında | — | Asla loglanmaz |
| `refreshToken` | string | Login yanıtında | — | Asla loglanmaz |
| `createdAt` | int (UNIX ts) | Hayır | — | Format kararı §15 #4 |
| `error` | object | Hata yanıtında | — | §10.1 yapısı |

## §9 — Doğrulama sözleşmesi

### §9.1 Sıra

| # | Adım | Sahip | Kanıt |
|---|---|---|---|
| 1 | Content-Type kontrolü | Middleware | `RequestValidationMiddleware.php:116` |
| 2 | Gövde ayrıştırma | Middleware | `:165` (`decodeJson`) |
| 3 | Kural uygulama | `Validator` | `:87` · `Validator.php:35` |
| 4 | Hata haritası | `{alan: mesaj}` | `:93` |
| 5 | 422 yanıt | `ApiResponse::error` | `:92` |
| 6 | Geçtiyse | `$next($request)` | `:98` |

### §9.2 Hata gövdesi biçimi

| Anahtar | İçerik | Zorunlu |
|---|---|---|
| `error.code` | `VALIDATION_ERROR` | Evet (`:92`) |
| `error.message` | `Validation failed` (genel) | Evet (`:92`) |
| `error.fields` | `{alan: mesaj}` haritası | Evet (Faz 3a sözleşmesi, `:89-93`) |
| `error.details` | Eski sözleşmenin kalıntısı | Evet — iki anahtar da taşınıyor (`:90-91`) |

### §9.3 Çift anahtar gerçeği

`error.details` (Faz 2) ve `error.fields` (Faz 3a) **aynı haritayı** taşıyor
(`RequestValidationMiddleware.php:89-93`). Bu, iki sözleşme sürümünün aynı anda
canlıda olması demektir; kaldırma kararı **UNKNOWN** (§15 #5).

### §9.4 Çapraz alan kuralları

| Örnek | Durum |
|---|---|
| `password` + `passwordConfirm` eşitliği | `UNKNOWN` — kodda doğrulanmadı |
| `page` ≤ `totalPages` | `UNKNOWN` |
| Şartlı alan (`resetToken` varsa `newPassword`) | `UNKNOWN` |

> Bu satırlar "yok" demek değil, **okunmadı** demektir; §15 işaretlidir.

## §10 — Hata DTO'su

### §10.1 Genel yapı

| Alan | Tip | Kanıt |
|---|---|---|
| `httpStatus` | int | `AuthPostHandler.php:29` sınıfı yapı |
| `type` | string (`json`) | `AuthPostHandler.php:30` |
| `error.code` | UPPER_SNAKE | `RequestValidationMiddleware.php:92` |
| `error.message` | string | Aynı |
| `error.fields` | object (ops.) | `:93` |

### §10.2 İlişkili HTTP kodları

| Kod | Kullanım | Kanıt |
|---|---|---|
| 409 | Çakışma (kayıt zaten var) | `AuthController.php:70` |
| 422 | Doğrulama | `RequestValidationMiddleware.php:92` |
| 429 | Kota | `AuthController.php:65` |
| 404 | Bulunamadı | `AuthController.php:60` |

## §11 — BFF ile ilişki

| Boyut | Kural | Kanıt |
|---|---|---|
| BFF girdisi | `array` | `BffLayer::transform(array $response, string $clientType)` |
| BFF çıktısı | `array` | Aynı |
| DTO'ya dönüş | Controller katmanında | §3 |
| İstemci türüne özel alan | SPA/mobile alan seçimi | `SpaBff.php:24`, `MobileBff.php:24` |
| Fazla alan kırpma | `MobileBff::stripRedundantFields` | `MobileBff.php:65` |

## §12 — Taşıma

### §12.1 Başlıklar

| Başlık | Değer | Kanıt |
|---|---|---|
| `Content-Type` | `application/json` | `RequestValidationMiddleware.php:116` (kabul listesi) |
| `X-Request-ID` | UuidV7 | `ApiResponse.php:30` |
| `X-API-Version` | Sabit | `ApiResponse.php:31` |

### §12.2 Kodlama

| Kural | Değer |
|---|---|
| Karakter seti | UTF-8 |
| Türkçe karakter | Doğrudan (mojibake yasak) |
| HTML kaçış | JSON içinde gerekmez; yanıtta `Content-Type` doğru olmalı |

### §12.3 PSR-7 ilişkisi

| Konu | Durum |
|---|---|
| PSR-7 mesaj nesneleri | Tasarım referansı (§4) |
| Somut kullanım | `⚠️ VERIFICATION REQUIRED` — `ApiRequest`/`ApiResponse` kendine özgü array tabanlı |
| Gerçek biçim | `array` tabanlı (§6 satır 7/15) |

### §12.4 İçerik tipi kapısı

| Kural | Davranış | Kanıt |
|---|---|---|
| Kabul edilmeyen tip | Red | `RequestValidationMiddleware.php:116` |
| Kabul listesi | `application/json` (+) | `:116` — tam liste okunmadı (§15 #6) |
| Gövdesiz GET | Query üzerinden doğrulama | `:80-84` |

## §13 — Sızıntı önleme

| # | Alan sınıfı | Dışarı çıkar mı? | Kanıt/Dayanak |
|---|---|---|---|
| 1 | Parola / hash | Hayır | Doğrulama DTO'su yalnız `email` + `password` (girdi) |
| 2 | Refresh token | Yalnız login yanıtında | `LoginResponse.php:21` |
| 3 | Dahili id / token hash'i | Hayır | §8.1 sarmalayıcı kuralı |
| 4 | Stack trace | Hayır | `AuthPostHandler.php:51-55` → ⚠️ mesaj içeriği §20 usecase #5 |
| 5 | SQL | Hayır | ADR-002 prepared statement |
| 6 | Debug alanları | Hayır | Ortam bayrağı `UNKNOWN` (§15 #7) |

## §14 — Politika ↔ kod karşılaştırma tablosu

| Politika | Kod karşılığı | Durum |
|---|---|---|
| Immutable DTO | `private readonly` (`LoginRequest.php:20`, `PaginationResponse.php:19-24`) | UYGUN |
| `fromArray` fabrika | `LoginRequest.php:52`, `LoginResponse.php:61` | UYGUN |
| `toArray` döküm | `LoginRequest.php:64`, `LoginResponse.php:74` | UYGUN |
| 422 + `fields` | `RequestValidationMiddleware.php:92-93` | UYGUN |
| Çift anahtar (`details` + `fields`) | `:89-91` | GÖZLEMLENEN (karar gerekli, §9.3) |
| Sayfa formülü | `PaginationResponse.php:104` | UYGUN (sıfır koruması yok, §8.3) |
| `respect/validation` kullanımı | composer'da var; `src/` içinde isabet **0** | **DİKKAT** (§15 #8) |
| `JsonSerializable` | Kullanım 0 | Tercih: `toArray()` (§7.4) |
| PSR-7 | Array tabanlı gerçek biçim | `⚠️ VERIFICATION REQUIRED` |

## §15 — Açık sorular ve doğrulanamayan iddialar

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | Opsiyonel alanlar `?tip` mi, varsayılanlı mı? | `UNKNOWN` | backend-architect |
| 2 | `pageSize` üst sınırı ne? | `UNKNOWN` | backend-architect |
| 3 | `page` taşması (negatif/sıfır) nasıl? | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 4 | Tarih alanının JSON biçimi (int ts mı, ISO string mi)? | `UNKNOWN` | architect |
| 5 | `error.details` kaldırılacak mı? | `UNKNOWN` | product-manager |
| 6 | Kabul edilen `Content-Type` listesinin tamamı? | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 7 | Debug alanları hangi ortamda açık? | `UNKNOWN` | devops-engineer |
| 8 | `respect/validation` neden kullanılmıyor? (bağımlılık + özel Validator) | `⚠️ VERIFICATION REQUIRED` | dependency-manager |
| 9 | `pageSize = 0` çağrısı nerede engelleniyor? | `⚠️ VERIFICATION REQUIRED` | qa-engineer |

## §16 — Test senaryoları (taslak)

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | `fromArray` eksik zorunlu alan | Hata/Varsayılan (karar §8.2) |
| 2 | `fromArray` fazladan alan | Yok sayılır (bilinmeyen anahtar) |
| 3 | `toArray` → `json_encode` → `json_decode` turu | Alanlar korunur |
| 4 | `pageSize = 0` | §8.3 kararı: reddedilmeli |
| 5 | `totalItems = 0` | `totalPages = 0` |
| 6 | Geçersiz içerik tipi | 415/422 sınıfı redd |
| 7 | Geçersiz JSON gövde | 400/422 sınıfı redd |
| 8 | Alan hatası | `error.fields` dolu, `error.code = VALIDATION_ERROR` |
| 9 | Türkçe karakter turu | Mojibake yok |
| 10 | Büyük `totalItems` | int taşması yok |
| 11 | Yanıtta hash sızıntısı | Alan yok |
| 12 | `X-Request-ID` her yanıtta | Tekil başlık |

## §17 — Red team: saldırı senaryoları

| # | Saldırı | Öncül | Savunma |
|---|---|---|---|
| 1 | Aşırı büyük gövde | Bellek tüketimi | Boyut sınırı (middleware) — kanıt: `UNKNOWN` (§20) |
| 2 | Derin iç içe JSON | Yığın/performans | Derinlik sınırı — kanıt yok |
| 3 | Tip karıştırma | `"1"` vs `1` | `Validator::typeError` (`Validator.php:126`) |
| 4 | Alan taşması | Bilinmeyen anahtarla enjeksiyon | Allowlist doğrulama |
| 5 | Alan adı çakışması | `error.details` eski anahtar | §9.3 — iki anahtar aynı veri |
| 6 | Yanıt sızdırma | Fazla alan | §13 |
| 7 | Büyük `pageSize` | Kaynak tüketimi | §15 #2 üst sınır |

## §18 — Risk kaydı

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | `pageSize = 0` → istisna | Orta | Orta | §15 #9 P0 |
| 2 | Kullanılmayan `respect/validation` bağımlılığı | Düşük | Yüksek | §15 #8 |
| 3 | Çift hata anahtarı kalıcılığı | Orta | Yüksek | §15 #5 |
| 4 | Opsiyonel alan tutarsızlığı | Orta | Orta | §15 #1 |
| 5 | Gövde boyutu sınırının yokluğu | Yüksek | Düşük | §17 #1 |

## §19 — Faz planı

| Faz | Kapsam | Durum |
|---|---|---|
| 0 | Bu sözleşme dosyası | YAZILDI (draft) |
| 1 | `pageSize`/`page` sınırı + sıfır koruması | PLANNED |
| 2 | `error.details` → `error.fields` geçişi ve kademeli kaldırma | PLANNED |
| 3 | Opsiyonel alan politikasının (§8.2) sabitlenmesi | PLANNED |
| 4 | Gövde/derinlik limiti middleware | PLANNED |
| 5 | `respect/validation` kararı (kullan veya kaldır) | PLANNED |

## §20 — Özet tablo

| Boyut | Karar | Durum |
|---|---|---|
| DTO tipi | Request + Response | ÖLÇÜLDÜ (`Api/Dto/**`) |
| Değiştirilemezlik | `private readonly` | KANITLI |
| Fabrika/döküm | `fromArray` / `toArray` | KANITLI |
| Doğrulama | Özel `Validator` + 422 `fields` | KANITLI |
| Sayfa formülü | `(int) ceil(total / pageSize)` | KANITLI (`:104`) |
| Sıfır koruması | Yok görünüyor | `⚠️ VERIFICATION REQUIRED` |
| `respect/validation` | composer'da, `src/` kullanımı 0 | `⚠️ VERIFICATION REQUIRED` |
| Çift hata anahtarı | `details` + `fields` | GÖZLEMLENEN |
| `JsonSerializable` | Kullanım 0 | ÖLÇÜLDÜ |

## §21 — Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış repo kod alıntısı | 22 | §6 tablosu |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu (hepsi HTTP 200) |
| Envanter kesişimi | 0 | §4 ölçüm notu |
| Açık soru | 9 | §15 |
| Saldırı senaryosu | 7 | §17 |
| Test senaryosu | 12 | §16 |
| Faz | 6 (0–5) | §19 |
| Ölçümle saptanan kullanılmayan bağımlılık | 1 | `respect/validation` (§14) |

## §22 — Sonraki adımlar

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | §15 #9 `pageSize = 0` yolunu bul ve kapat | P0 | qa-engineer |
| 2 | §15 #8 `respect/validation` kararını ver | P1 | dependency-manager |
| 3 | §15 #5 `error.details` kaldırma takvimi | P1 | product-manager |
| 4 | §8.2 opsiyonel alan politikasını sabitle | P1 | backend-architect |
| 5 | §16 testlerini yaz | P1 | qa-engineer |
| 6 | Gövde boyutu/derinlik limitini ekle (faz 4) | P2 | backend-architect |

## §23 — Kapsam dışı devir tablosu

| Konu | Bu dosyadan çıkan yere | Gerekçe |
|---|---|---|
| HTTP kodu seçimi | `routing` §12 · `usecase` §10 | Kod listesi başka dosyada |
| Doğrulama kurallarının içeriği | `usecase` §9 | Sıra burada, içerik orada |
| Cache'leme / ETag | `caching/caching-uygulama-seviyesi-cache` | Bayrak routing'de |
| Session/JWT claim taşıma | `K010-identitiy/session` | Kimlik ayrı K |
| Şema/alan tipleri (DB) | `K005-data` | Kaynak tipleri ayrı |
| Log maskeleme | `K013-audit` | Yalnız dışa aktarım kuralı burada |
| OpenAPI şeması | `K012-integraion` | Sözleşme dışa aktarımı ayrı |
| BFF dönüşüm altyapısı | `routing` §16 | Yönlendirme dosyası |
| Rate limit başlıkları | `caching` dosyası | Sayaç APCu'da |
| Versiyonlama politikası | `routing` §11 | Sürüm başlığı orada |
| JSON kütüphane seçimi | Karar yok — PHP yerleşik | §7 |
| Tarih formatı kararı | §15 #4 (açık) | Henüz verilmedi |
| Dosya yükleme DTO'ları | Kapsam dışı | CoreMusic'te yok (UNKNOWN) |
| Protobuf/gRPC taşıma | Kapsam dışı | Proje REST tabanlı |

## §24 — Karar matrisi (bu dosyada verilen kararlar)

| # | Karar | Değer | Dayanak |
|---|---|---|---|
| 1 | DTO biçimi | Array tabanlı sınıf | §6 · §12.3 |
| 2 | Değiştirilemezlik | `private readonly` | `LoginRequest.php:20` |
| 3 | Fabrika/döküm | `fromArray` / `toArray` | §14 |
| 4 | `JsonSerializable` | Kullanılmaz | §7.4 ölçüm |
| 5 | Alan adı | camelCase | §8.1 |
| 6 | Hata kodu biçimi | UPPER_SNAKE | §8.1 |
| 7 | Doğrulama hataları | 422 + `error.fields` | `:92-93` |
| 8 | Çift anahtar | `details` + `fields` kalıcı (karar gerekli) | §9.3 |
| 9 | Sayfa formülü | `(int) ceil(total / pageSize)` | `:104` |
| 10 | `pageSize` sıfır koruması | Henüz yok | §8.3 · §15 #9 |
| 11 | Opsiyonel alan | Varsayılanlı `readonly` (kesin değil) | §8.2 |
| 12 | Sarmalayıcı | JSON nesnesi (kök dizi değil) | §7.2 |

## §25 — Uygunluk kontrol listesi

| # | Kontrol | Beklenen kanıt | Durum |
|---|---|---|---|
| 1 | Tüm DTO'larda `readonly` var mı? | §6 satır 2/6/9 | ✓ |
| 2 | Her request DTO'da `fromArray` var mı? | `LoginRequest.php:52` | ✓ (örnek) |
| 3 | Her response DTO'da `toArray` var mı? | `LoginResponse.php:74` | ✓ (örnek) |
| 4 | 422 yanıtında `fields` var mı? | `RequestValidationMiddleware.php:93` | ✓ |
| 5 | Sayformülü int'e çevriliyor mu? | `PaginationResponse.php:104` | ✓ |
| 6 | `pageSize = 0` engelleniyor mu? | §15 #9 | ✗ — işaretli |
| 7 | İçerik tipi kapısı var mı? | `:116` | ✓ |
| 8 | Yanıtta sızdıran alan var mı? | §13 denetimi | ✗ — ölçülmedi |
| 9 | `respect/validation` kullanılıyor mu? | §14 ölçümü (0) | ✗ — karar gerekli |
| 10 | Gövde boyutu sınırı var mı? | §17 #1 | ✗ — kanıt yok |
| 11 | Testler yazıldı mı? | §16 taslağı | ✗ — bekliyor |
| 12 | Kaynak alıntılar doğrulandı mı? | §6 (22 satır) | ✓ |

## §26 — Ölçüm özeti

| Ölçüm | Değer | Tarih |
|---|---|---|
| İncelenen DTO dosyası (örnek) | `LoginRequest`, `LoginResponse`, `PaginationResponse` | 2026-10-13 |
| `Api/Dto/` altındaki dosya sayısı | Request 6 · Response 5 | 2026-10-13 |
| `JsonSerializable` kullanımı | 0 isabet | 2026-10-13 |
| `respect/validation` içe aktarımı | 0 isabet | 2026-10-13 |
| Özel `Validator` metot sayısı | 5 (`validate`, `fieldErrors`, `compare`, `typeError`, `isEmail`) | 2026-10-13 |
| 422 yanıt üretim yeri | `RequestValidationMiddleware.php:92` | 2026-10-13 |
| Çift anahtar taşıma satırı | `:89-93` | 2026-10-13 |
| Sayfa formülü satırı | `PaginationResponse.php:104` | 2026-10-13 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
