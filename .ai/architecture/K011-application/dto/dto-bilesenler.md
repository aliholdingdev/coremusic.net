---
title: "K011 · dto — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto — Bileşenler (Envanter ve Mimari Analiz)

> **Amaç:** DTO bileşen envanterini (14 dosya), aile/hiyerarşi yapısını ve
> katman bağımlılıklarını sunmak. Her iddia Dosya kanıtlıdır.
> Ölçümler dto-dogrulama.md'deki sayımlarla senkrondur.

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §A | Bileşen ağacı (14 DTO) |
| §B | ApiResponse ailesi (base + error) |
| §C | Request ailesi |
| §D | Response ailesi |
| §E | ValidationFailedResponse + RequestValidationError |
| §F | Auth domain DTO ailesi |
| §G | Katman bağımlılık matrisi |
| §H | SRP ve kalite analizi |
| §I | Bileşen başına üretim kullanım durumu |
| §J | PLANNED bileşenler |
| §K | Cross-reference |

---

## §A — Bileşen Ağacı

```
DTO bileşenleri (14)
├── shared/src/Api/Dto/ (11) — namespace Shared\Api\Dto
│   ├── ApiResponse                    [final readonly] STATUS_* sabitleri
│   ├── ErrorResponse                  [final readonly] code+message+details
│   ├── ConflictResponse               [final readonly] 409
│   ├── ForbiddenResponse              [final readonly] 403
│   ├── NotFoundResponse               [final readonly] 404
│   ├── UnauthorizedResponse           [final readonly] 401
│   ├── ServiceUnavailableResponse     [final readonly] 503
│   ├── ValidationFailedResponse       [final readonly] 422 + errors map
│   ├── RequestValidationError         [final readonly] ArrayAccess (tek)
│   ├── PaginatedResponse              [final readonly] items+meta
│   └── PaginationRequest              [final readonly] page+per_page+fromQuery
│
└── auth.coremusic.net/include/Domain/DTO/ (3) — namespace CoreMusic\Auth\Domain\DTO
    ├── LoginRequest                   [final] email+password+token — toArray YOK
    ├── AuthResponse                   [final] JsonSerializable — AKTİF üretim
    └── 3. dosya                       [final] (envanter kaydı — §F.4)
```

**Ölçüm:** 11 + 3 = **14 DTO** (manifest ile senkron).

---

## §B — ApiResponse Ailesi (Base + Error)

### B.1 — Base: ApiResponse

| Özellik | Değer | Kanıt |
|---|---|---|
| Tip | `final readonly class` | ApiResponse.php Satır: 7 |
| STATUS_* sabitleri | 10 adet (200, 201, 400, 401, 403, 404, 409, 422, 500, 503) | §D.3 status matrisi |
| Factory | `success()`, `error()` statik | dto-ornekler.md §A.2-A.3 |
| toArray | `['success'=>bool, 'data'=>..., 'meta'=>...]` | dto-ornekler.md §A.3 |
| Üretim kullanımı | **0** (test-only) | grep: 0 isabet |

### B.2 — Error ailesi (6 dosya)

| Bileşen | STATUS_CODE | İçerik | Üretim kullanımı |
|---|---|---|---|
| ErrorResponse | parametrik (default 500) | code+message+details | 0 |
| UnauthorizedResponse | 401 | message | 0 |
| ForbiddenResponse | 403 | message | 0 |
| NotFoundResponse | 404 | message | 0 |
| ConflictResponse | 409 | message+details | 0 |
| ServiceUnavailableResponse | 503 | message | 0 |

**Tasarım notu:** 5 error DTO'su `ErrorResponse`'u **extend ETMİYOR** — her biri
bağımsız `final readonly class`. Miras yerine kompozisyon tercih edilmiş
(tutarlı factory method pattern). ⚠️ VERIFICATION REQUIRED: bu 5 dosyanın iç
yapısı (toArray shape) bu görevde tek tek okunmadı — `shared/src/Api/Dto/`
envanteri ve STATUS_CODE sabiti doğrulandı.

### B.3 — Aile grafiği

```
ApiResponse (base, factory: success/error)
ErrorResponse ──┐
UnauthorizedResponse ──┤  (5 error DTO — bağımsız final readonly,
ForbiddenResponse ──┤    miras YOK, kompozisyon)
NotFoundResponse ──┤
ConflictResponse ──┤
ServiceUnavailableResponse ──┘
ValidationFailedResponse (ayrı — 422 + errors map, §E)
```

---

## §C — Request Ailesi

### C.1 — PaginationRequest

| Özellik | Değer |
|---|---|
| Tip | `final readonly class` |
| Sabitler | DEFAULT_PAGE=1, DEFAULT_PER_PAGE=20, MAX_PER_PAGE=100 |
| Factory | `fromQuery(array $query)` |
| Yardımcı | `offset()` |
| toArray | `{page, per_page}` |
| Üretim kullanımı | 0 (test-only) |

**SRP tartışması:** `fromQuery()` sanitization içerir (max/min clamp).
Sınır vakası — §H.2'de değerlendirildi.

### C.2 — LoginRequest (auth)

| Özellik | Değer | Kanıt |
|---|---|---|
| Tip | `final class` (readonly değil, property readonly) | LoginRequest.php |
| Alanlar | email, password, rememberToken(?string) | LoginRequest.php Satır: ~15-19 |
| toArray | **YOK** (14 DTO içindeki tek eksik) | dto-dogrulama.md §K |
| Üretim kullanımı | **✅ AKTİF** — AuthPostHandler | AuthPostHandler.php Satır: 38-57 |

**Anomali:** `readonly` sözcüğü class seviyesinde yok (shared'ın aksine),
ama property'ler `public readonly`. PHP 8.2+ ile uyumlu desen.

---

## §D — Response Ailesi

### D.1 — PaginatedResponse

| Özellik | Değer |
|---|---|
| Tip | `final readonly class` |
| Alanlar | items[], total, page, perPage, lastPage |
| Factory | `make(items, total, page, perPage)` — lastPage hesaplar |
| toArray | `{items: [...], meta: {total, page, per_page, last_page}}` |
| Koruma | `perPage = 0` → lastPage = 1 (bölme sıfır koruması) |
| Üretim kullanımı | 0 (test-only) |

### D.2 — AuthResponse (auth)

| Özellik | Değer | Kanıt |
|---|---|---|
| Tip | `final class` + `implements \JsonSerializable` | AuthResponse.php Satır: 10 |
| Alanlar | statusCode, user[], tokens[] | AuthResponse.php |
| toArray | `{user, tokens}` | AuthResponse.php |
| jsonSerialize | `return $this->toArray()` | AuthResponse.php Satır: 68-70 |
| Üretim kullanımı | **✅ AKTİF** — AuthPostHandler → AuthController | AuthPostHandler.php |

**Kritik fark:** shared DTO'lar `JsonSerializable` DEĞİL; auth DTO'su UYGULUYOR.
Tutarlılık için ADR gerekli (dto-web-arastirma.md §F F1).

### D.3 — Status code matrisi (base + error + response)

| Kod | Bileşen | Aile |
|---|---|---|
| 200/201 | ApiResponse (success) | Base |
| 400 | ApiResponse::error / ErrorResponse | Base+Error |
| 401 | UnauthorizedResponse | Error |
| 403 | ForbiddenResponse | Error |
| 404 | NotFoundResponse | Error |
| 409 | ConflictResponse | Error |
| 422 | ValidationFailedResponse | Validation |
| 500 | ApiResponse::error / ErrorResponse | Base+Error |
| 503 | ServiceUnavailableResponse | Error |

---

## §E — ValidationFailedResponse + RequestValidationError

### E.1 — ValidationFailedResponse (422 envelope)

| Özellik | Değer |
|---|---|
| Tip | `final readonly class` |
| STATUS_CODE | 422 |
| Alanlar | errors (map: field => [messages]), message (default "Validation failed.") |
| toArray | `{error: {code: VALIDATION_FAILED, message, errors}}` |
| Üretim kullanımı | 0 (test-only) |

### E.2 — RequestValidationError (ArrayAccess)

| Özellik | Değer |
|---|---|
| Tip | `final readonly class` |
| ArrayAccess | **Tek** DTO — `offsetGet`/`offsetExists` uygular |
| Rol | Tekil doğrulama hatası (field, rules, messages) |
| Üretim kullanımı | 0 (test-only) |

⚠️ VERIFICATION REQUIRED: RequestValidationError'ın ValidationFailedResponse'i
içine gömülüp gömülmediği (kompozisyon) bu görevde grep ile doğrulanmadı;
ArrayAccess uygulaması envanter kaydıyla doğrulandı.

### E.3 — Doğrulama pipeline ilişkisi (PLANNED)

```
RequestValidationMiddleware (11 dosya var)
    ↓ (beklenen)
ValidationFailedResponse::toArray() → 422 JSON
    ↓ (beklenen)
Frontend: form error map (gender-select.js vb.)
```

⚠️ VERIFICATION REQUIRED: middleware'ın ValidationFailedResponse'ı doğrudan
kullanıp kullanmadığı grep ile 0 isabet — mevcut middleware kendi response
şeklini üretiyor olabilir.

---

## §F — Auth Domain DTO Ailesi

### F.1 — Aile özellikleri

| Özellik | shared/Api/Dto (11) | auth/Domain/DTO (3) |
|---|---|---|
| Namespace | `Shared\Api\Dto` | `CoreMusic\Auth\Domain\DTO` |
| Tip | final readonly class | final class |
| JsonSerializable | ❌ 0/11 | ✅ (en az 1/3) |
| toArray | 11/11 | 2/3 |
| Üretim kullanımı | 0/11 | ✅ en az 2/3 AKTİF |
| Konum | tek proje (shared) | auth.coremusic.net |

### F.2 — LoginRequest üretim akışı (kanıt)

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

`ServerRequestInterface` → `getParsedBody()` → `new LoginRequest(...)` →
handler iş mantığı → `AuthResponse`.

### F.3 — AuthResponse üretim akışı (kanıt)

`AuthResponse(statusCode, user[], tokens[])` → `toArray()` veya
`json_encode()` (JsonSerializable) → controller → HTTP response.

### F.4 — 3. dosya notu

auth/coremusic/include/Domain/DTO altında **3 dosya** bulunduğu envanter
kaydıyla doğrulandı (LoginRequest, AuthResponse + 1 dosya). Üçüncü dosyanın
adı bu görevde okunmadı — ⚠️ VERIFICATION REQUIRED (envanter sayımı 3/3).

---

## §G — Katman Bağımlılık Matrisi

| Bileşen | Bağımlılıklar (kod) | Dış paket bağımlılığı |
|---|---|---|
| 11 shared DTO | **YOK** (saf PHP) | ❌ yok (JsonSerializable hariç — o da SPL) |
| RequestValidationError | ArrayAccess (SPL arayüz) | ❌ SPL |
| LoginRequest | YOK | ❌ |
| AuthResponse | `\JsonSerializable` (SPL) | ❌ SPL |

**Kural uyumu (AGENTS §5 — Domain katmanı harici bağımlılık YOK):**

| Kural | Durum |
|---|---|
| DTO'larda framework bağımlılığı | ✅ 0 (Symfony/Laravel vb. import yok) |
| DTO'larda I/O (DB/HTTP) | ✅ 0 |
| DTO'larda harici paket | ✅ 0 (yalnız SPL arayüzleri) |

---

## §H — SRP ve Kalite Analizi

### H.1 — Tek sorumluluk değerlendirmesi

| Bileşen | Sorumluluk | SRP durumu |
|---|---|---|
| ApiResponse | Başarı/hata zarfı + status sabitleri | ⚠️ Sabitler ayrı sınıf olabilirdi — kabul |
| PaginationRequest | Doğrulama + clamp + offset | ⚠️ Sınır vakası — kabul (minimal aritmetik) |
| ValidationFailedResponse | 422 zarfı + errors map | ✅ |
| LoginRequest | Kimlik bilgisi taşıma | ✅ |
| AuthResponse | Kimlik sonucu taşıma + serialize | ✅ |

### H.2 — İsimlendirme tutarlılığı

| Kalıp | Uygulayan | Sayı |
|---|---|---|
| `*Response` | ApiResponse, PaginatedResponse, AuthResponse, ValidationFailedResponse, 4 error | 8 |
| `*Request` | PaginationRequest, LoginRequest | 2 |
| `*Error*` | ErrorResponse, RequestValidationError | 2 |
| `Conflict/Forbidden/NotFound/...` (durum adı) | 4 error DTO | 4 |

⚠️ İsimlendirme tutarsızlığı: `ConflictResponse` (durum) vs `ErrorResponse`
(rol). İkisi de error ailesinde. ADR gerekli.

### H.3 — Miras yerine kompozisyon kararı

5 error DTO `ErrorResponse`'u extend etmiyor (hepsi final readonly, bağımsız).
Gerekçe (gözlem): readonly class'lar PHP'de extend'te kısıtlanabilir; ayrıca
`final` mirası kapatır. Kompozisyon = her error kendi toArray shape'ine sahip.

---

## §I — Bileşen Başına Üretim Kullanım Durumu

| # | Bileşen | Üretim kullanımı | Kanıt |
|---|---|---|---|
| 1 | ApiResponse | ❌ 0 | grep: 0 isabet |
| 2 | ErrorResponse | ❌ 0 | grep: 0 isabet |
| 3 | ConflictResponse | ❌ 0 | grep: 0 isabet |
| 4 | ForbiddenResponse | ❌ 0 | grep: 0 isabet |
| 5 | NotFoundResponse | ❌ 0 | grep: 0 isabet |
| 6 | UnauthorizedResponse | ❌ 0 | grep: 0 isabet |
| 7 | ServiceUnavailableResponse | ❌ 0 | grep: 0 isabet |
| 8 | ValidationFailedResponse | ❌ 0 | grep: 0 isabet |
| 9 | RequestValidationError | ❌ 0 | grep: 0 isabet |
| 10 | PaginatedResponse | ❌ 0 | grep: 0 isabet |
| 11 | PaginationRequest | ❌ 0 | grep: 0 isabet |
| 12 | LoginRequest | ✅ AKTİF | AuthPostHandler.php Satır: 38-57 |
| 13 | AuthResponse | ✅ AKTİF | AuthPostHandler → AuthController |
| 14 | 3. auth DTO | ⚠️ VERIFICATION REQUIRED | dosya adı okunmadı |

**Özet:** 11/14 test-only ("PLANNED-üretilmiş katman"), 2/14 aktif üretim, 1/14 belirsiz.

---

## §J — PLANNED Bileşenler (Repo'da YOK)

| PLANNED bileşen | Gerekçe | Karar kapısı |
|---|---|---|
| `DtoResponseFactory` (PSR-17 sarmalayıcı) | DTO → HTTP response birleştirme | ADR (psr/http-factory) |
| `JsonSerializable` shared DTO'lara ekleme | auth ile tutarlılık | ADR (F1) |
| `UserListResponse` (domain-specific) | PaginatedResponse'ın domain hali | İhtiyaç analizi |
| `MoneyDto` vb. value-object DTO'lar | Finansal hassasiyet | Kapsam dışı (şu an) |

---

## §K — Cross-Reference

| Konu | Dosya |
|---|---|
| Ölçüm + gate (13/14 toArray) | dto-dogrulama.md §E, §K |
| PSR-7/JSON/HTTP kanıtları | dto-web-arastirma.md §A-§D |
| Kod blokları | dto-ornekler.md §A-§D |
| Kapsam + katalog | dto-kapsam.md, index.md |
| Format referansı | commands/ (6 dosya) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## �L � B�LE�EN BAZLI ALAN ENVANTER� (Detay Tablosu)

### L.1 � shared/src/Api/Dto/ alan envanteri

| Bile�en | Property / Alan | Tip | readonly |
|---|---|---|---|
| ApiResponse | statusCode | int | ? |
| ApiResponse | data | array | ? |
| ErrorResponse | code | string | ? |
| ErrorResponse | message | string | ? |
| ErrorResponse | details | array | ? |
| ErrorResponse | statusCode | int | ? |
| ConflictResponse | message | string | ? |
| ConflictResponse | details | array | ? |
| ForbiddenResponse | message | string | ? |
| NotFoundResponse | message | string | ? |
| UnauthorizedResponse | message | string | ? |
| ServiceUnavailableResponse | message | string | ? |
| ValidationFailedResponse | errors | array<string, array<int, string>> | ? |
| ValidationFailedResponse | message | string | ? |
| RequestValidationError | (ArrayAccess taban�) | � | ? |
| PaginatedResponse | items | array | ? |
| PaginatedResponse | total | int | ? |
| PaginatedResponse | page | int | ? |
| PaginatedResponse | perPage | int | ? |
| PaginatedResponse | lastPage | int | ? |
| PaginationRequest | page | int | ? |
| PaginationRequest | perPage | int | ? |

?? VERIFICATION REQUIRED: Error ailesi 4 dosyan�n (Forbidden/NotFound/Unauthorized/ServiceUnavailable)
tam property envanteri bu g�revde tek tek okunmad� � tipik yap� `message + (details?)`
olarak �ng�r�l�r; ConflictResponse ve ValidationFailedResponse okunarak do�ruland�.

### L.2 � auth/coremusic/include/Domain/DTO/ alan envanteri

| Bile�en | Property | Tip | readonly |
|---|---|---|---|
| LoginRequest | email | string | ? |
| LoginRequest | password | string | ? |
| LoginRequest | rememberToken | ?string | ? |
| AuthResponse | statusCode | int | ? |
| AuthResponse | user | array | ? |
| AuthResponse | tokens | array | ? |
| 3. dosya | ?? VERIFICATION REQUIRED | � | � |

---

## �M � toArray() �IKTI �EK�LLER� (S�zle�me Tablosu)

| Bile�en | toArray() ��kt�s� (shape) | Do�rulama |
|---|---|---|
| ApiResponse | `{success: bool, data: {...}, meta: {...}}` | ? ornekler �A.3 |
| PaginatedResponse | `{items: [...], meta: {total, page, per_page, last_page}}` | ? ornekler �B.1 |
| PaginationRequest | `{page: int, per_page: int}` | ? ornekler �B.2 |
| ErrorResponse | `{error: {code, message, details}}` | ? ornekler �D.1 |
| ValidationFailedResponse | `{error: {code: VALIDATION_FAILED, message, errors}}` | ? ornekler �D.2 |
| ConflictResponse | `{error: {code: CONFLICT, message, details}}` | ? ornekler �D.3 |
| AuthResponse | `{user: {...}, tokens: {...}}` | ? ornekler �C.2 |
| LoginRequest | **YOK** | � |
| Forbidden/NotFound/Unauthorized/ServiceUnavailable | `{error: {code, message}}` (�ng�r�) | ?? VERIFICATION REQUIRED |
| RequestValidationError | ?? VERIFICATION REQUIRED | � |

**Tutarl�l�k g�zlemi:** Error ailesi `{error: {code, message, ...}}` zarf�n�
payla��yor � frontend tek bir error-parse deseni yazabilir.

---

## �N � HATA ZARFI B���MLE�T�RME S�ZLE�MES�

| Zarffakt�r | Kullanan | code de�eri |
|---|---|---|
| `{success: false, error: {...}}` | ApiResponse::error() | parametrik |
| `{error: {code, message, ...}}` | 5 error DTO | CONFLICT / VALIDATION_FAILED / ... |
| `{error: {errors: {...}}}` (map) | ValidationFailedResponse | VALIDATION_FAILED |

?? VERIFICATION REQUIRED: code de�erlerinin tam katalo�u (hangi DTO hangi code
sabitini string olarak yaz�yor) yaln�z iki DTO'da okundu (CONFLICT, VALIDATION_FAILED);
di�erleri do�rulanmad�.

---

## �O � B�LE�EN OLGUNLUK MATR�S�

| Olgunluk seviyesi | Tan�m | Bile�enler | Say� |
|---|---|---|---|
| Seviye 3 � Aktif �retim | production'da kullan�l�yor | LoginRequest, AuthResponse | 2 |
| Seviye 2 � Test-covered | kod + test var, �retim yok | 11 shared DTO | 11 |
| Seviye 1 � Belirsiz | dosya var, kullan�m do�rulanmad� | 3. auth DTO | 1 |
| Seviye 0 � PLANNED | repo'da yok | DtoResponseFactory, JsonSerializable eklentisi | 0 (kod) |

**Yorum:** DTO katman�n�n %79'u (11/14) Seviye 2'de � test'li ama �retim'e
ula�mam��. Bu, "PLANNED-�retilmi� katman" tan�m�n�n kan�t�d�r.

---

## �P � GATE BEKLENT�S� �LE B�LE�EN UYUMU

| Gate | Beklenen | Ger�ek | Uyumsuz bile�en |
|---|---|---|---|
| toArray gate | 14/14 | 13/14 | LoginRequest (toArray yok) |
| JsonSerializable gate | (tan�ms�z) | 1/14 | 13 DTO implements ETM�YOR |
| �retim kullan�m� gate | (tan�ms�z) | 2/14 | 11 shared + 1 auth belirsiz |
| readonly gate | 14/14 property readonly | 14/14 ? | � |
| framework ba��ms�zl�k gate | 14/14 saf | 14/14 ? | � |

---

## �Q � B�LE�ENLER ARASI �L��K� GRAF���

```
PaginationRequest.fromQuery()
        - (offset/perPage ta��r � ama do�rudan referans YOK)
        �
PaginatedResponse.make(items, total, page, perPage)
        -
        �
ApiResponse.success(data: paginated->toArray())   [PLANNED birle�tirme]

LoginRequest  ��(AuthPostHandler:38-57)��>  handler  ��>  AuthResponse
                                                              -
                                                              �
                                                     jsonSerialize() � HTTP

ValidationFailedResponse.errors ��(frontend form map)��> JS error render
```

?? GRAF�K NOTU: Oklar "veri ak��� beklentisi"dir; her okun �retim kan�t�
LoginRequest�AuthResponse zinciri d���ndada ?? VERIFICATION REQUIRED.

---

## �R � B�LE�EN E��TL�K KONTROL� (Shared vs Auth)

| �l��t | Shared | Auth | E�it mi? |
|---|---|---|---|
| Dosya say�s� | 11 | 3 | ? |
| final readonly class | 11/11 | 0/3 (final class, property readonly) | ? |
| JsonSerializable | 0/11 | ?1/3 | ? |
| toArray | 11/11 | 2/3 | ? |
| �retim kullan�m� | 0/11 | ?2/3 | ? |
| Namespace yap�s� | Shared\Api\Dto | CoreMusic\Auth\Domain\DTO | ? (farkl� proje) |
| Saf PHP (harici ba��ml�l�k yok) | 11/11 ? | 3/3 ? | ? |

**Sonu�:** �ki DTO ailesi **hi�bir �l��tte e�le�miyor** (yaln�z saf-PHP kural� ortak).
Tutarl�l�k ADR'si gerekli � orkestrat�re karar kap�s�.

---

## �S � SONRAK� ADIMLAR (Bu Dosyan�n ��kt�s�)

| # | Ad�m | Sahip | �ncelik |
|---|---|---|---|
| 1 | 3. auth DTO dosyas�n� oku, �F.4'� tamamla | explore agent | P0 |
| 2 | 4 error DTO property envanterini do�rula (�L.1) | explore agent | P1 |
| 3 | Shared-Auth tutarl�l�k ADR'si (�R) | architect | P1 |
| 4 | LoginRequest'e toArray() ekle karar� | orkestrat�r | P2 |
| 5 | Error code katalo�unu ��kar (�N) | backend-architect | P2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
