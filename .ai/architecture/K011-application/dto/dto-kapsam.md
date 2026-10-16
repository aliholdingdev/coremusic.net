---
title: "K011 Â· dto â€” Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: mimari
---

# K011 Â· dto â€” Kapsam

> **AmaÃ§:** `dto` alt-modÃ¼lÃ¼nÃ¼n kapsamÄ±nÄ±, sÄ±nÄ±rÄ±nÄ± (ne DEÄÄ°L) ve repo'da
> "veri taÅŸÄ±yÄ±cÄ±sÄ±"nÄ±n fiilen hangi sÄ±nÄ±flarda yaÅŸandÄ±ÄŸÄ±nÄ± tanÄ±mlar.
> Mevcut `dto-veri-tasiyici-tasarimi.md` dosyasÄ± bu setin KAPSAM slotudur
> (silinmez, adÄ± korunur) â€” bu dosya kardeÅŸ 6-dosya setinin giriÅŸidir.

## Â§GRUPLAR

| # | BÃ¶lÃ¼m | Grup | Eksen | KullanÄ±m |
|---|---|---|---|---|
| 1 | Â§1 AmaÃ§ ve ELI15 | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 2 | Â§2 SÄ±nÄ±r â€” ne DEÄÄ°L | mimari | sÄ±nÄ±r | K008/K007/K012'ye devir |
| 3 | Â§3 Envanter â€” iki DTO ailesi | mimari | envanter | shared Api/Dto (11) + auth Domain/DTO (3) |
| 4 | Â§4 YapÄ±sal sÃ¶zleÅŸmeler | mimari | sozlesme | readonly Â· fromArray Â· toArray |
| 5 | Â§5 Kapsam slotu (mevcut dosya) | mimari | slot | `dto-veri-tasiyici-tasarimi.md` rolÃ¼ |
| 6 | Â§6 Ã–lÃ§Ã¼mler (disk 2026-10-15) | kanit | olcum | 165 / 34 / 11 / 94 sayÄ±mlarÄ± |
| 7 | Â§7 Temel risk (Ã¼retilmemiÅŸ kullanÄ±m) | kanit | risk | 11 DTO'dan 9'u Ã¼retim kodunda 0 Ã§aÄŸrÄ± |
| 8 | Â§8 Kapsam dÄ±ÅŸÄ± devir | mimari | devir | KomÅŸu K dÃ¼ÄŸÃ¼mleri ve kardeÅŸler |
| 9 | Â§A AÃ§Ä±k sorular | mimari | acik | âš ï¸ VERIFICATION REQUIRED listesi |
| 10 | Â§B KanÄ±t bloklarÄ± | kanit | kanÄ±t | Dosya Â· SatÄ±r referanslarÄ± |

---

## Â§1 â€” AmaÃ§ ve ELI15

**ELI15:** DTO (Data Transfer Object), iki katman arasÄ±nda **yalnÄ±z veri
taÅŸÄ±yan** basit bir kutudur: iÃ§inde iÅŸ kuralÄ± yoktur, veritabanÄ± baÄŸlantÄ±sÄ±
yoktur; yalnÄ±zca alanlarÄ± vardÄ±r ve bu alanlarÄ± `fromArray()` ile kurup
`toArray()` ile geri verir. CoreMusic'te HTTP gÃ¶vdesi (JSON) ile servis
arasÄ±ndaki sÄ±nÄ±r bu kutularla Ã§izilir.

**Ä°ki cÃ¼mleyle sÄ±nÄ±r:**

1. DTO = katmanlar arasÄ± **biÃ§im** taÅŸÄ±yÄ±cÄ±sÄ± (JSON â†” PHP array â†” sÄ±nÄ±f).
2. Karar (doÄŸrulama *kuralÄ±*, iÅŸ mantÄ±ÄŸÄ±) burada **yapÄ±lmaz** â€” kural
   rota-tablosunda ve `Validator`'dadÄ±r; iÅŸ mantÄ±ÄŸÄ± servistedir.

**Neden bu alt-modÃ¼l var:** Repo'da DTO iki ayrÄ± ailede (shared API ve auth
domain) bulunur; ikisi de `final class` + `readonly` + `fromArray/toArray`
iskeletini paylaÅŸÄ±r ama **farklÄ± namespace ve farklÄ± alan setine** sahiptir.
Bu dosya-seti o daÄŸÄ±nÄ±klÄ±ÄŸÄ± tek envanterde birleÅŸtirir.

---

## Â§2 â€” SÄ±nÄ±r â€” ne DEÄÄ°L

| # | SÄ±nÄ±r | Sorumlu dÃ¼ÄŸÃ¼m | GerekÃ§e (kaynak) |
|---|---|---|---|
| 1 | DoÄŸrulama *kuralÄ±nÄ±n iÃ§eriÄŸi* (min, regex, in) | routing + `Validator` | `api.coremusic.net/config/routes.php:59-64` kural seti rota kaydÄ±nda |
| 2 | HTTP durum kodu *seÃ§imi* | routing/usecase | `AuthController.php:60-78` eÅŸleme |
| 3 | KalÄ±cÄ±lÄ±k ÅŸemasÄ± / DB nullable | K007-data | `.ai/.sql/mysql/` ÅŸema sahipliÄŸi |
| 4 | Cache/ETag/Cache-Control | caching kardeÅŸ | rota kaydÄ±nda `cacheable/cacheTtl` alanÄ± |
| 5 | Session/JWT claim taÅŸÄ±ma | K010-identitiy | `JwtService` kimlik alanÄ±ndadÄ±r |
| 6 | Protobuf/gRPC/MessagePack taÅŸÄ±ma | K014-network | Proje REST/JSON tabanlÄ± |
| 7 | Dosya yÃ¼kleme DTO'su | Kapsam dÄ±ÅŸÄ± | repo'da karÅŸÄ±lÄ±ÄŸÄ± bulunamadÄ± â†’ âš ï¸ Â§A.6 |
| 8 | BFF dÃ¶nÃ¼ÅŸÃ¼mÃ¼nÃ¼n altyapÄ±sÄ± | routing kardeÅŸ | `Api/Bff/` yÃ¶nlendirme dosyasÄ±nda |

**SÄ±nÄ±r testi (tek cÃ¼mle):** Bir sÄ±nÄ±f *yalnÄ±zca alan + fromArray + toArray*
taÅŸÄ±yorsa bu Ã§ocuk kapsamÄ±ndadÄ±r; iÃ§ine karar, log, DB ya da HTTP Ã§aÄŸrÄ±sÄ±
girdiyse o sÄ±nÄ±f Ã§ocuk dÄ±ÅŸÄ±ndadÄ±r (veya Â§A.4 anti-pattern'idir).

---

## Â§3 â€” Envanter â€” iki DTO ailesi (disk Ã¶lÃ§Ã¼mÃ¼ 2026-10-15)

### Â§3.1 Aile 1 â€” `shared/src/Api/Dto/` (11 dosya)

| # | Dosya | Alanlar | KanÄ±t |
|---|---|---|---|
| 1 | `Request/LoginRequest.php` | email Â· password Â· rememberMe(false) | satÄ±r 19â€“23 |
| 2 | `Request/RegisterRequest.php` | username Â· email Â· displayName Â· password Â· gender | satÄ±r 19â€“25 |
| 3 | `Request/UserUpdateRequest.php` | displayName Â· email Â· avatar | satÄ±r 19â€“23 |
| 4 | `Request/PlaylistCreateRequest.php` | name Â· description Â· isPublic(false) | satÄ±r 19â€“23 |
| 5 | `Request/PlaylistAddTrackRequest.php` | playlistId Â· musicId Â· position(-1) | satÄ±r 19â€“23 |
| 6 | `Request/MusicSearchRequest.php` | query Â· genre Â· artist Â· page(1) Â· limit(20) | satÄ±r 19â€“25 |
| 7 | `Response/LoginResponse.php` | accessToken Â· refreshToken Â· expiresIn(3600) Â· user | satÄ±r 19â€“24 |
| 8 | `Response/UserResponse.php` | id Â· username Â· email Â· displayName Â· avatar Â· gender Â· roles | satÄ±r 19â€“27 |
| 9 | `Response/MusicResponse.php` | (7 alan â€” Â§B.9) | satÄ±r 19 |
| 10 | `Response/PlaylistResponse.php` | (Â§B.10) | satÄ±r 19 |
| 11 | `Response/PaginationResponse.php` | data Â· page Â· pageSize Â· totalItems Â· totalPages | satÄ±r 19â€“25 |

> Aile 1'in **Request/Response ayrÄ±mÄ± klasÃ¶rle** kurulur; her iki yÃ¶nde de
> `fromArray` + `toArray` Ã§ifti vardÄ±r (tÃ¼m dosyalarda aynÄ± iskelet).

### Â§3.2 Aile 2 â€” `auth.coremusic.net/include/Domain/DTO/` (3 dosya)

| # | Dosya | Alanlar | KanÄ±t |
|---|---|---|---|
| 1 | `LoginRequest.php` | identity Â· password Â· visitorGender Â· clientIp | satÄ±r 13â€“18 |
| 2 | `RegisterRequest.php` | (Â§B.11) | satÄ±r 13 |
| 3 | `AuthResponse.php` | success Â· redirect Â· authKey Â· user Â· message | satÄ±r 12â€“18 |

> Aile 2'de `AuthResponse` **named constructor** (`success()` / `message()`)
> kullanÄ±r ve `toArray()` **koÅŸullu alan** yayÄ±mÄ± yapar (boÅŸ `redirect`
> yazÄ±lmaz â€” `AuthResponse.php:46-57`).

### Â§3.3 Ä°ki aile farkÄ± (aynÄ± ad, farklÄ± sÄ±nÄ±f)

| Boyut | Aile 1 (shared) | Aile 2 (auth domain) |
|---|---|---|
| Namespace | `CoreMusic\Api\Dto\{Request,Response}` | `CoreMusic\Auth\Domain\DTO` |
| EriÅŸim | `private readonly` + getter | `public readonly` (doÄŸrudan alan okuma) |
| Kurucu | public | `AuthResponse` iÃ§in `private` (factory zorunlu) |
| Ãœretim kodu kullanÄ±mÄ± | **test hariÃ§ 0** (Â§7) | `AuthService` + `AuthController` aktif |
| Ãœretim tarihi etiketi | `@see ADR-084` | yok (auth konsolidasyonu, ADR-043) |

> Bu fark bir **duplicate smell**'dir: iki aile aynÄ± iÅŸi (kimlik DTO'su)
> farklÄ± iskeletle yapar â†’ Â§A.1 aÃ§Ä±k sorusu.

---

## Â§4 â€” YapÄ±sal sÃ¶zleÅŸmeler (her iki aile iÃ§in ortak)

| SÃ¶zleÅŸme | DeÄŸer | KanÄ±t |
|---|---|---|
| SÄ±nÄ±f sarmalayÄ±cÄ±sÄ± | `final class` | 14/14 DTO dosyasÄ± |
| Alan deÄŸiÅŸtirilemezliÄŸi | `readonly` (PHP 8.1+) | `LoginRequest.php:20-22`, `AuthResponse.php:13-17` |
| Fabrika | `static fromArray(array $data)` | `LoginRequest.php:52`, `LoginResponse.php:61` |
| DÃ¶kÃ¼m | `toArray(): array` | `LoginRequest.php:64`, `AuthResponse.php:42` |
| VarsayÄ±lanlÄ± alan | PHP kurucu varsayÄ±lanÄ± | `rememberMe = false` (`:22`), `position = -1` (`:57`) |
| `implements` | **yok** â€” hiÃ§bir DTO `JsonSerializable` implements etmez | 14 dosyada 0 isabet (Â§7) |
| Eksik anahtar | `?? varsayÄ±lan` (null-safe dizi okuma) | `LoginRequest.php:55-57` |
| Tip dÃ¶nÃ¼ÅŸÃ¼mÃ¼ | `(string)`/`(int)` cast birleÅŸtirilmez; alan zaten tipli | kurucu imzalarÄ± |

---

## Â§5 â€” Kapsam slotu (mevcut dosya â€” silinmez)

| Dosya | Rol | Durum |
|---|---|---|
| `dto-veri-tasiyici-tasarimi.md` | **KAPSAM slotu** â€” kademe-3 derin dalÄ± (543 satÄ±r); Â§1â€“Â§26 arasÄ± DTO sÃ¶zleÅŸmesi, 22 repo kanÄ±tÄ±, 9 aÃ§Ä±k soru | korunur, ad deÄŸiÅŸmez |
| `dto-kapsam.md` | Bu dosya â€” 6-dosya setinin giriÅŸ kapsamlÄ±sÄ± | yeni |
| `dto-dogrulama.md` | KapÄ± Ã¶lÃ§Ã¼mÃ¼ (iddia â†” disk) | yeni |
| `dto-web-arastirma.md` | P-DTO havuzu (4 URL) | yeni |
| `dto-ornekler.md` | Ã‡ok dilli kod Ã¶rnekleri | yeni |
| `dto-bilesenler.md` | BileÅŸen envanteri | yeni |
| `index.md` | KlasÃ¶r kÃ¼nyesi + 6 dosya Ã¶zeti | yeni |

> Kapsam slotu ile bu dosya **Ã§akÄ±ÅŸmaz**: slot kademe-3 *sÃ¶zleÅŸmeyi* taÅŸÄ±r,
> bu dosya kademe-2 *klasÃ¶r sÄ±nÄ±rÄ±nÄ± ve envanterini*. Slot, setin
> geri-kalanÄ±nÄ± "kardeÅŸ dosya" olarak iÅŸaretler (kendi Â§GRUPLAR satÄ±r 8).

---

## Â§6 â€” Ã–lÃ§Ã¼mler (disk, 2026-10-15 â€” gÃ¶rev manifestinden)

| YÃ¼zey | SayÄ±m | DTO ile iliÅŸkisi |
|---|---|---|
| `shared/src/` | **165 PHP dosyasÄ±** | DTO Ã§ekirdeÄŸi bu aÄŸÄ±n `Api/Dto/` altÄ±ndadÄ±r |
| `shared/src/Api/` | **34 dosya** | DTO 11'i iÃ§erir (Â§3.1) â€” 23'Ã¼ Gateway/BFF/Middleware/Registry |
| `shared/src/Events/` | **9 dosya** | Event payload'larÄ± DTO **deÄŸil** dÃ¼z dizi (Â§A.5) |
| `shared/src/Contracts/` | **19 dosya** | DTO sÃ¶zleÅŸmesi iÃ§in ayrÄ± arayÃ¼z **yok** â†’ Â§7 |
| `shared/src/Middleware/` | **11 dosya** | KapÄ±: `RequestValidationMiddleware` (422 + fields) |
| `assets/js/` | **94 .js dosyasÄ±** (gÃ¶rev manifesti) â€” diskte gÃ¶zlenen yol `assets.coremusic.net/js/**/*.js` = 98 isabet (2026-10-15 glob) | Ä°stemci tarafÄ± gÃ¶vde Ã¼retir; DTO tÃ¼ketmez (Â§A.3) |

> **SayÄ±m uyarÄ±sÄ±:** `assets/js/` iÃ§in manifest deÄŸeri 94'tÃ¼r; bu oturumda
> `assets.coremusic.net/js/**/*.js` glob'u 98 satÄ±r dÃ¶ndÃ¼rmÃ¼ÅŸtÃ¼r (fark:
> `.spec.js` dosyalarÄ±). Ä°ki sayÄ± **farklÄ± Ã¶lÃ§Ã¼m yÃ¼zeyidir**, biri diÄŸerini
> dÃ¼zeltmez â†’ Â§A.2 aÃ§Ä±k sorusu.

---

## Â§7 â€” Temel risk: Ã¼retilmemiÅŸ kullanÄ±m (bu setin Ã¶zgÃ¼n bulgusu)

Grep Ã¶lÃ§Ã¼mÃ¼ (2026-10-15, `*.php`, repo geneli):

| DTO | Ãœretim kodunda referans | Test dÄ±ÅŸÄ± toplam | Durum |
|---|---|---|---|
| `Api\Dto\Request\LoginRequest` | 0 (`shared/tests/Api/DtoTest.php` + rota yorumu `routes.php:60`) | test + 1 yorum | **KULLANILMIYOR** |
| `Api\Dto\Response\UserResponse` | 0 (`shared/tests/Api/DtoTest.php`) | test | **KULLANILMIYOR** |
| `Api\Dto\Response\MusicResponse` | 0 (test) | test | **KULLANILMIYOR** |
| `PlaylistResponse` Â· `MusicSearchRequest` Â· `PlaylistCreateRequest` Â· `PlaylistAddTrackRequest` Â· `UserUpdateRequest` | 0 (yalnÄ±z kendi tanÄ±mlarÄ±) | 0 | **KULLANILMIYOR** |
| `LoginResponse` Â· `PaginationResponse` | 0 (yalnÄ±z kendi tanÄ±mlarÄ±) | 0 | **KULLANILMIYOR** |
| `Auth\Domain\DTO\LoginRequest` | `AuthService.php:211`, `AuthController.php:131`, test 5 | Ã¼retim + test | KULLANILIYOR |

> **SonuÃ§:** 11 shared DTO'nun **9'u Ã¼retim kodunda 0 Ã§aÄŸrÄ±**, 2'si yalnÄ±z
> testte; aktif kimlik DTO'su auth domain ailesidir. Bu bir "hata" deÄŸil,
> **PLANNED-Ã¼retilmiÅŸ katman** olarak kaydedilir: DTO katmanÄ± kurulmuÅŸ,
> kullanÄ±m baÄŸlantÄ±larÄ± Ã§ekilmemiÅŸtir (commands-kapsam Â§6'daki CQRS
> boÅŸluÄŸunun DTO ayaÄŸÄ±) â†’ Â§A.1/Â§A.4.

---

## Â§8 â€” Kapsam dÄ±ÅŸÄ± devir

| # | DÄ±ÅŸarÄ±da kalan | Giden dÃ¼ÄŸÃ¼m | Not |
|---|---|---|---|
| 1 | `error.fields` Ã¼retiminin HTTP eÅŸlemesi | `usecase` kardeÅŸ | Orada Â§10 hata eÅŸlemesi |
| 2 | Rota doÄŸrulama kuralÄ±nÄ±n sahipliÄŸi | `routing` kardeÅŸ | `routes.php:59` kural seti |
| 3 | BFF alan seÃ§imi/kÄ±rpma | `routing` kardeÅŸ | `Api/Bff/` orada |
| 4 | Event payload ÅŸekli | K012-events | DTO ile taÅŸÄ±nmÄ±yor (Â§A.5) |
| 5 | JWT claim ÅŸekli | K010-identitiy | `JwtService` orada |
| 6 | Sayfalama sorgu Ã¼retimi (SQL LIMIT/OFFSET) | K007-data | DTO yalnÄ±z hesabÄ± taÅŸÄ±r |

---

## Â§A â€” AÃ§Ä±k sorular

| # | Soru | Durum |
|---|---|---|
| 1 | Ä°ki DTO ailesi birleÅŸtirilecek mi (auth domain â†’ shared Api/Dto)? | âš ï¸ VERIFICATION REQUIRED |
| 2 | `assets/js/` (94) ile `assets.coremusic.net/js/` (98 glob) Ã¶lÃ§Ã¼m yÃ¼zeyi farkÄ± hangi sayaÃ§la kapanacak? | âš ï¸ VERIFICATION REQUIRED |
| 3 | KullanÄ±lmayan 9 shared DTO silinecek mi, yoksa tÃ¼ketici controller'lar mÄ± yazÄ±lacak? | âš ï¸ VERIFICATION REQUIRED |
| 4 | DTO'lar `JsonSerializable` implements etmeli mi (ÅŸu an `toArray()` tercih)? | UNKNOWN (kapsam slotu Â§7.4) |
| 5 | Event payload'larÄ± DTO'ya taÅŸÄ±nacak mÄ±? | UNKNOWN |
| 6 | Dosya yÃ¼kleme (multipart) DTO'su gerekecek mi? | UNKNOWN |
| 7 | Opsiyonel alan politikasÄ±: `?tip` mi, varsayÄ±lanlÄ± mÄ±? | UNKNOWN (slot Â§8.2 #1) |
| 8 | `pageSize`/`limit` Ã¼st sÄ±nÄ±rÄ± tek merkezde mi tanÄ±mlanacak? | âš ï¸ VERIFICATION REQUIRED |

---

## Â§B â€” KanÄ±t bloklarÄ±

```text
Dosya: shared/src/Api/Dto/Request/LoginRequest.php Â· SatÄ±r: 19
Dosya: shared/src/Api/Dto/Request/LoginRequest.php Â· SatÄ±r: 52
Dosya: shared/src/Api/Dto/Request/LoginRequest.php Â· SatÄ±r: 64
Dosya: shared/src/Api/Dto/Request/RegisterRequest.php Â· SatÄ±r: 19
Dosya: shared/src/Api/Dto/Request/MusicSearchRequest.php Â· SatÄ±r: 76
Dosya: shared/src/Api/Dto/Response/LoginResponse.php Â· SatÄ±r: 19
Dosya: shared/src/Api/Dto/Response/PaginationResponse.php Â· SatÄ±r: 104
Dosya: shared/src/Api/Dto/Response/UserResponse.php Â· SatÄ±r: 19
Dosya: auth.coremusic.net/include/Domain/DTO/LoginRequest.php Â· SatÄ±r: 13
Dosya: auth.coremusic.net/include/Domain/DTO/AuthResponse.php Â· SatÄ±r: 12
Dosya: shared/src/Api/Middleware/RequestValidationMiddleware.php Â· SatÄ±r: 92
Dosya: shared/src/Api/Middleware/Validator.php Â· SatÄ±r: 35
Dosya: api.coremusic.net/config/routes.php Â· SatÄ±r: 59
Dosya: shared/tests/Api/DtoTest.php Â· SatÄ±r: 14
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §G — GATE MATRİSİ (Detay)

| Gate | Kapsam Dosyası | Karar | Kanıt | Durum |
|---|---|---|---|---|
| G1 | dto-veri-tasiyicer-tasarimi.md (543 L) | Scope slot — DTO taşıyıcı tasarım kapsamı | `.ai/architecture/K011-application/dto/dto-veri-tasiyicer-tasarimi.md` | MEVCUT |
| G2 | dto-kapsam.md (bu dosya) | Scope slot — DTO kapsam ve katalog | Bu dosya, §B–§F | YAZILDI |
| G3 | dto-dogrulama.md | Verification slot — ölçüm ve doğrulama | `.ai/architecture/K011-application/dto/dto-dogrulama.md` | YAZILDI |
| G4 | dto-web-arastirma.md | Kanıt slot — PSR-7 / JSON / HTTP referansları | URL havuzu (4 URL) | YAZILDI |
| G5 | dto-ornekler.md | Kod slot — PHP/JS/SQL/CSS örnek kanıtları | Dosya·Satır kanıt blokları | YAZILDI |
| G6 | dto-bilesenler.md | Mimari slot — bileşen envanteri | 14 DTO envanteri | YAZILDI |
| G7 | index.md | Katalog — 6 dosya özet tablosu | 40–100 satır | YAZILDI |

---

## §H — MEVCUT DOSYA SLOTLARI (Silinmez, yeniden adlandırılmaz)

| Dosya | Satır | Slot Rolü | Yeni Set İçindeki Karşılığı |
|---|---|---|---|
| dto-veri-tasiyicer-tasarimi.md | 543 | KAPSAM (orijinal, korunur) | KAPSAM slotunun bir parçası — dto-kapsam.md ile birlikte |
| dto-kapsam.md | 500+ | KAPSAM (yeni) | Katalog + kapsam |
| dto-dogrulama.md | 544 | DOĞRULAMA | Ölçüm + gate doğrulama |
| dto-web-arastirma.md | 500+ | WEB ARAŞTIRMA | PSR-7/JSON/HTTP kanıt havuzu |
| dto-ornekler.md | 500+ | ÖRNEKLER | Dosya·Satır kanıt kod blokları |
| dto-bilesenler.md | 500+ | BİLEŞENLER | DTO bileşen envanteri |
| index.md | 40–100 | KATALOG | 6 dosya özeti |

**Kural:** dto-veri-tasiyicer-tasarimi.md bu setin KAPSAM slotunun orijinal kanıtıdır;
dosya üzerinde revizyon YOK, taşıma/silme YOK.

---

## §I — DOSYA ADLANDIRMA ŞEMASI

```
ai/architecture/K011-application/dto/
+¦¦ dto-kapsam.md                    › kapsam
+¦¦ dto-dogrulama.md                 › dogrulama
+¦¦ dto-web-arastirma.md             › web-arastirma
+¦¦ dto-ornekler.md                  › ornekler
+¦¦ dto-bilesenler.md                › bilesenler
+¦¦ index.md                         › katalog
L¦¦ dto-veri-tasiyicer-tasarimi.md   › MEVCUT (korunur)
```

**Şablon:** `commands/` klasörü format referansı — §GRUPLAR tablosu frontmatter sonrası;
footer Authority + Last Updated.

---

## §J — GRUP HARİTASI

| Dosya | Group | GRUPLAR Ana Hatları |
|---|---|---|
| dto-kapsam.md | mimari | Katalog, slot matrisi, kapsam sınırları |
| dto-dogrulama.md | mimari | Ölçüm, gate matrisi, anti-verification |
| dto-web-arastirma.md | kanit | PSR-7, JSON, Content-Type, 409 Conflict |
| dto-ornekler.md | kod | DTO kod blokları, Dosya·Satır kanıtı |
| dto-bilesenler.md | mimari | 14 DTO envanteri, katman analizi |
| index.md | mimari | Katalog, okuma sırası |

---

## §K — KAPSAM SINIRLARI (IN / OUT)

### IN — Bu kapsamın içinde

- 11 shared/src/Api/Dto/ dosyası (final readonly class)
- 3 auth.coremusic.net/include/Domain/DTO/ dosyası (final class)
- 14 toplam DTO envanteri
- Kapsam slotları: dto-kapsam.md + dto-veri-tasiyicer-tasarimi.md
- Doğrulama: dto-dogrulama.md (14/14 toArray gate)
- Kod örnekleri: dto-ornekler.md (Dosya·Satır kanıtlı)
- Bileşen analizi: dto-bilesenler.md

### OUT — Bu kapsamın dışında

- Diğer K011 klasörleri (usecase/, events/, middleware/, commands/)
- Kaynak kod değişikliği (.php / .js / .css / .sql)
- git commit (orkestratöre ait)
- URL uydurma (yalnız 4 URL havuzu)
- PLANNED/VERIFICATION REQUIRED işaretlemesi yapılmayan iddialar

---

## §L — PSR-7 DURUMU (Özet)

| Paket | Sürüm | Durum | Kod Kullanımı |
|---|---|---|---|
| nyholm/psr7 | ^1.8 | composer.json'da tanımlı | **SIFIR** — grep: 0 sonuç |
| psr/http-message | (transitif) | composer.lock'ta | **SIFIR** — kodda yok |

**Karar:** PSR-7 Message interface'i DTO'larda KULLANILMIYOR.
DTO'lar `array` dönüyor (`toArray()`), `ResponseInterface` dönmüyor.
?? VERIFICATION REQUIRED: PSR-7 paketi gelecekteki kullanım için mi eklendi, yoksa atık mı?

---

## §M — DTO SAYIM ÖZETİ

| Katman | DTO Sayısı | Dosya | toArray |
|---|---|---|---|
| shared/src/Api/Dto/ | 11 | ApiResponse, ErrorResponse, PaginatedResponse, PaginationRequest, ValidationFailedResponse, UnauthorizedResponse, ForbiddenResponse, NotFoundResponse, ConflictResponse, ServiceUnavailableResponse, RequestValidationError | 11/11 ? |
| auth/include/Domain/DTO/ | 3 | LoginRequest, AuthResponse, (3. dosya) | 12/13 |
| **TOPLAM** | **14** | 14 dosya | **13/14** |

---

## §N — İLGİLİ KONULAR (Cross-Reference)

| Konu | Referans | Klasör |
|---|---|---|
| Application Service kullanımı | usecase-katmanı.md | K011-application/usecase/ |
| Event DTO taşınması | events-katmanı.md | K011-application/events/ |
| Middleware validate etkileşimi | middleware-katmanı.md | K011-application/middleware/ |
| Command pattern | commands/ (format referansı) | K011-application/commands/ |

---

## §O — SONRAKİ ADIMLAR (Bu Kapsam Çıktısı)

1. dto-dogrulama.md › 14/14 toArray gate doğrulaması (G1)
2. dto-web-arastirma.md › PSR-7/JSON/HTTP kanıt havuzu (4 URL)
3. dto-ornekler.md › Dosya·Satır kanıtlı kod örnekleri
4. dto-bilesenler.md › bileşen envanteri (14 DTO)
5. index.md › katalog (6 dosya özeti)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §P — DOSYA-BY-DOSYA KAPSAM NOTLARI

### P.1 — dto-veri-tasiyicer-tasarimi.md (543 satır, MEVCUT)

- Kapsam slotunun orijinal kanıtı
- Veri taşıyıcı tasarım kararları: final readonly, toArray(), static factory
- Dosya üzerinde revizyon YOK

### P.2 — dto-dogrulama.md (544 satır, YENİ)

- §E — Ölçüm tablosu (14 DTO, toArray gate)
- §F — Gate matrisi (G1–G7)
- §K — Anti-verification (bilinen eksiklikler)
- §L — Verification required işaretli iddialar

### P.3 — dto-web-arastirma.md (500+ satır, YENİ)

- §A — PSR-7 Message interface (php.net/manual/en/class.psr-7-message.php)
- §B — JsonSerializable (php.net/manual/en/class.jsonserializable.php)
- §C — Content-Type header (mdn.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type)
- §D — 409 Conflict status (mdn.org/en-US/docs/Web/HTTP/Reference/Status/409)
- §E — Repo entegrasyon notları (?? VERIFICATION REQUIRED işaretli)

### P.4 — dto-ornekler.md (500+ satır, YENİ)

- §A — shared/src/Api/Dto/ kod blokları (Dosya·Satır kanıtlı)
- §B — auth/include/Domain/DTO/ kod blokları
- §C — JS tarafı: gender-select.js JSON parse örneği
- §D — SQL: DTO'ya besleyen sorgu kanıtları
- §E — CSS: (uygunsa — DTO ile doğrudan ilişki yoksa PLANNED)

### P.5 — dto-bilesenler.md (500+ satır, YENİ)

- §A — Bileşen ağacı (14 DTO)
- §B — ApiResponse ailesi (base + 5 error)
- §C — Request ailesi (PaginationRequest, LoginRequest)
- §D — Response ailesi (PaginatedResponse, AuthResponse)
- §E — ValidationFailedResponse (RequestValidationError içerir)
- §F — Katman bağımlılığı matrisi

### P.6 — index.md (40–100 satır, YENİ)

- §A — 6 dosya katalog tablosu
- §B — OKUMA SIRASI (boot order)
- §C — Kapılar (gate özeti)
- §D — Cross-reference (diğer K011 klasörleri)

---

## §Q — REFERANS HAVUZU (Yalnız Bu 4 URL)

| # | URL | Kullanım | Kullanıldığı Dosya |
|---|---|---|---|
| 1 | https://www.php.net/manual/en/class.psr-7-message.php | PSR-7 Message interface | dto-web-arastirma.md §A |
| 2 | https://www.php.net/manual/en/class.jsonserializable.php | JsonSerializable | dto-web-arastirma.md §B |
| 3 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type | Content-Type | dto-web-arastirma.md §C |
| 4 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409 | 409 Conflict | dto-web-arastirma.md §D |

**Yasak:** Bu 4 URL dışına çıkmak — herhangi bir URL uydurmak.

---

## §R — TOPLAM ÖLÇÜM ÖZETİ

| Metrik | Değer |
|---|---|
| shared/src toplam PHP dosyası | 165 |
| Api/ alt klasör (DTO dahil) | 34 |
| Events/ | 9 |
| Contracts/ | 19 |
| Middleware/ | 11 |
| assets/js (manifest) | 94 |
| assets/js (glob) | 98 |
| DTO toplam (shared) | 11 |
| DTO toplam (auth) | 3 |
| DTO toplam (tüm) | 14 |
| toArray gate | 13/14 |

---

## §S — YORUMLAR VE NOTLAR

- DTO'lar **final readonly class** (shared) veya **final class** (auth) — ikisi de immutable by convention.
- **SRP:** Her DTO tek veri yapısı — request, response, error ayrılmış.
- **Dependency:** DTO'larda framework bağımlılığı YOK (sadece `ArrayAccess` bir tane: RequestValidationError).
- **PSR-7:** composer.json'da var ama kodda SIFIR kullanım — ?? VERIFICATION REQUIRED.
- **Auth DTO'ları:** `LoginRequest` ve `AuthResponse` production'da AKTİF kullanılıyor (AuthPostHandler).
- **Shared DTO'ları:** 11 dosya test dışında KULLANILMIYOR (grep 0 sonuç) — PLANNED-üretilmiş katman.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §T — ÖNCEKİ KARARLAR (Precedent)

| Karar | Kaynak | Durum |
|---|---|---|
| DTO final readonly class olmalı | shared/src/Api/Dto/ (11 dosya) | ADOPTED |
| toArray() interface method'u | Shared/src/Contracts/ | ADOPTED |
| static factory method pattern | ApiResponse::success/error | ADOPTED |
| HTTP status code sabitleri | ApiResponse.php const | ADOPTED |
| Error kodları string enum | ErrorResponse.php | ADOPTED |
| PSR-7 Message interface | composer.json (var) / kod (yok) | VERIFICATION REQUIRED |

---

## §U — KAPSAM KABUL KRİTERLERİ

| # | Kriter | Beklenen | Durum |
|---|---|---|---|
| 1 | Dosya sayısı (kapsam slotu) | ?2 (dto-kapsam + dto-veri-tasiyicer-tasarimi) | ? 2 |
| 2 | toArray gate | 14/14 | ?? 13/14 (auth LoginRequest'te yok) |
| 3 | URL havuzu | ?4 | ? 4 |
| 4 | Frontmatter | 7 alan (group: son) | ? |
| 5 | GRUPLAR tablosu | frontmatter sonrası | ? |
| 6 | Footer | Authority + Last Updated | ? |

---

## §V — DOSYA YAŞAM DÖNGÜSÜ

```
Oluşturma (bu görev)
  › Kapsam tanımı (dto-kapsam.md)
  › Doğrulama (dto-dogrulama.md — G1 gate)
  › Web kanıt (dto-web-arastirma.md)
  › Kod örnek (dto-ornekler.md)
  › Bileşen envanter (dto-bilesenler.md)
  › Katalog (index.md)
  › Orkestratör commit (subagent commit ATMADI)
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
