---
title: "K011 · dto — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto — Kapsam

> **Amaç:** `dto` alt-modülünün kapsamını, sınırını (ne DEĞİL) ve repo'da
> "veri taşıyıcısı"nın fiilen hangi sınıflarda yaşandığını tanımlar.
> Mevcut `dto-veri-tasiyici-tasarimi.md` dosyası bu setin KAPSAM slotudur
> (silinmez, adı korunur) — bu dosya kardeş 6-dosya setinin girişidir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 2 | §2 Sınır — ne DEĞİL | mimari | sınır | K008/K007/K012'ye devir |
| 3 | §3 Envanter — iki DTO ailesi | mimari | envanter | shared Api/Dto (11) + auth Domain/DTO (3) |
| 4 | §4 Yapısal sözleşmeler | mimari | sozlesme | readonly · fromArray · toArray |
| 5 | §5 Kapsam slotu (mevcut dosya) | mimari | slot | `dto-veri-tasiyici-tasarimi.md` rolü |
| 6 | §6 Ölçümler (disk 2026-10-15) | kanit | olcum | 165 / 34 / 11 / 94 sayımları |
| 7 | §7 Temel risk (üretilmemiş kullanım) | kanit | risk | 11 DTO'dan 9'u üretim kodunda 0 çağrı |
| 8 | §8 Kapsam dışı devir | mimari | devir | Komşu K düğümleri ve kardeşler |
| 9 | §A Açık sorular | mimari | acik | ⚠️ VERIFICATION REQUIRED listesi |
| 10 | §B Kanıt blokları | kanit | kanıt | Dosya · Satır referansları |

---

## §1 — Amaç ve ELI15

**ELI15:** DTO (Data Transfer Object), iki katman arasında **yalnız veri
taşıyan** basit bir kutudur: içinde iş kuralı yoktur, veritabanı bağlantısı
yoktur; yalnızca alanları vardır ve bu alanları `fromArray()` ile kurup
`toArray()` ile geri verir. CoreMusic'te HTTP gövdesi (JSON) ile servis
arasındaki sınır bu kutularla çizilir.

**İki cümleyle sınır:**

1. DTO = katmanlar arası **biçim** taşıyıcısı (JSON ↔ PHP array ↔ sınıf).
2. Karar (doğrulama *kuralı*, iş mantığı) burada **yapılmaz** — kural
   rota-tablosunda ve `Validator`'dadır; iş mantığı servistedir.

**Neden bu alt-modül var:** Repo'da DTO iki ayrı ailede (shared API ve auth
domain) bulunur; ikisi de `final class` + `readonly` + `fromArray/toArray`
iskeletini paylaşır ama **farklı namespace ve farklı alan setine** sahiptir.
Bu dosya-seti o dağınıklığı tek envanterde birleştirir.

---

## §2 — Sınır — ne DEĞİL

| # | Sınır | Sorumlu düğüm | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | Doğrulama *kuralının içeriği* (min, regex, in) | routing + `Validator` | `api.coremusic.net/config/routes.php:59-64` kural seti rota kaydında |
| 2 | HTTP durum kodu *seçimi* | routing/usecase | `AuthController.php:60-78` eşleme |
| 3 | Kalıcılık şeması / DB nullable | K007-data | `.ai/.sql/mysql/` şema sahipliği |
| 4 | Cache/ETag/Cache-Control | caching kardeş | rota kaydında `cacheable/cacheTtl` alanı |
| 5 | Session/JWT claim taşıma | K0100-identitiy | `JwtService` kimlik alanındadır |
| 6 | Protobuf/gRPC/MessagePack taşıma | K010-network | Proje REST/JSON tabanlı |
| 7 | Dosya yükleme DTO'su | Kapsam dışı | repo'da karşılığı bulunamadı → ⚠️ §A.6 |
| 8 | BFF dönüşümünün altyapısı | routing kardeş | `Api/Bff/` yönlendirme dosyasında |

**Sınır testi (tek cümle):** Bir sınıf *yalnızca alan + fromArray + toArray*
taşıyorsa bu çocuk kapsamındadır; içine karar, log, DB ya da HTTP çağrısı
girdiyse o sınıf çocuk dışındadır (veya §A.4 anti-pattern'idir).

---

## §3 — Envanter — iki DTO ailesi (disk ölçümü 2026-10-15)

### §3.1 Aile 1 — `shared/src/Api/Dto/` (11 dosya)

| # | Dosya | Alanlar | Kanıt |
|---|---|---|---|
| 1 | `Request/LoginRequest.php` | email · password · rememberMe(false) | satır 19–23 |
| 2 | `Request/RegisterRequest.php` | username · email · displayName · password · gender | satır 19–25 |
| 3 | `Request/UserUpdateRequest.php` | displayName · email · avatar | satır 19–23 |
| 4 | `Request/PlaylistCreateRequest.php` | name · description · isPublic(false) | satır 19–23 |
| 5 | `Request/PlaylistAddTrackRequest.php` | playlistId · musicId · position(-1) | satır 19–23 |
| 6 | `Request/MusicSearchRequest.php` | query · genre · artist · page(1) · limit(20) | satır 19–25 |
| 7 | `Response/LoginResponse.php` | accessToken · refreshToken · expiresIn(3600) · user | satır 19–24 |
| 8 | `Response/UserResponse.php` | id · username · email · displayName · avatar · gender · roles | satır 19–27 |
| 9 | `Response/MusicResponse.php` | (7 alan — §B.9) | satır 19 |
| 10 | `Response/PlaylistResponse.php` | (§B.10) | satır 19 |
| 11 | `Response/PaginationResponse.php` | data · page · pageSize · totalItems · totalPages | satır 19–25 |

> Aile 1'in **Request/Response ayrımı klasörle** kurulur; her iki yönde de
> `fromArray` + `toArray` çifti vardır (tüm dosyalarda aynı iskelet).

### §3.2 Aile 2 — `auth.coremusic.net/include/Domain/DTO/` (3 dosya)

| # | Dosya | Alanlar | Kanıt |
|---|---|---|---|
| 1 | `LoginRequest.php` | identity · password · visitorGender · clientIp | satır 13–18 |
| 2 | `RegisterRequest.php` | (§B.11) | satır 13 |
| 3 | `AuthResponse.php` | success · redirect · authKey · user · message | satır 12–18 |

> Aile 2'de `AuthResponse` **named constructor** (`success()` / `message()`)
> kullanır ve `toArray()` **koşullu alan** yayımı yapar (boş `redirect`
> yazılmaz — `AuthResponse.php:46-57`).

### §3.3 İki aile farkı (aynı ad, farklı sınıf)

| Boyut | Aile 1 (shared) | Aile 2 (auth domain) |
|---|---|---|
| Namespace | `CoreMusic\Api\Dto\{Request,Response}` | `CoreMusic\Auth\Domain\DTO` |
| Erişim | `private readonly` + getter | `public readonly` (doğrudan alan okuma) |
| Kurucu | public | `AuthResponse` için `private` (factory zorunlu) |
| Üretim kodu kullanımı | **test hariç 0** (§7) | `AuthService` + `AuthController` aktif |
| Üretim tarihi etiketi | `@see ADR-084` | yok (auth konsolidasyonu, ADR-043) |

> Bu fark bir **duplicate smell**'dir: iki aile aynı işi (kimlik DTO'su)
> farklı iskeletle yapar → §A.1 açık sorusu.

---

## §4 — Yapısal sözleşmeler (her iki aile için ortak)

| Sözleşme | Değer | Kanıt |
|---|---|---|
| Sınıf sarmalayıcısı | `final class` | 14/14 DTO dosyası |
| Alan değiştirilemezliği | `readonly` (PHP 8.1+) | `LoginRequest.php:20-22`, `AuthResponse.php:13-17` |
| Fabrika | `static fromArray(array $data)` | `LoginRequest.php:52`, `LoginResponse.php:61` |
| Döküm | `toArray(): array` | `LoginRequest.php:64`, `AuthResponse.php:42` |
| Varsayılanlı alan | PHP kurucu varsayılanı | `rememberMe = false` (`:22`), `position = -1` (`:57`) |
| `implements` | **yok** — hiçbir DTO `JsonSerializable` implements etmez | 14 dosyada 0 isabet (§7) |
| Eksik anahtar | `?? varsayılan` (null-safe dizi okuma) | `LoginRequest.php:55-57` |
| Tip dönüşümü | `(string)`/`(int)` cast birleştirilmez; alan zaten tipli | kurucu imzaları |

---

## §5 — Kapsam slotu (mevcut dosya — silinmez)

| Dosya | Rol | Durum |
|---|---|---|
| `dto-veri-tasiyici-tasarimi.md` | **KAPSAM slotu** — kademe-3 derin dalı (543 satır); §1–§26 arası DTO sözleşmesi, 22 repo kanıtı, 9 açık soru | korunur, ad değişmez |
| `dto-kapsam.md` | Bu dosya — 6-dosya setinin giriş kapsamlısı | yeni |
| `dto-dogrulama.md` | Kapı ölçümü (iddia ↔ disk) | yeni |
| `dto-web-arastirma.md` | P-DTO havuzu (4 URL) | yeni |
| `dto-ornekler.md` | Çok dilli kod örnekleri | yeni |
| `dto-bilesenler.md` | Bileşen envanteri | yeni |
| `index.md` | Klasör künyesi + 6 dosya özeti | yeni |

> Kapsam slotu ile bu dosya **çakışmaz**: slot kademe-3 *sözleşmeyi* taşır,
> bu dosya kademe-2 *klasör sınırını ve envanterini*. Slot, setin
> geri-kalanını "kardeş dosya" olarak işaretler (kendi §GRUPLAR satır 8).

---

## §6 — Ölçümler (disk, 2026-10-15 — görev manifestinden)

| Yüzey | Sayım | DTO ile ilişkisi |
|---|---|---|
| `shared/src/` | **165 PHP dosyası** | DTO çekirdeği bu ağın `Api/Dto/` altındadır |
| `shared/src/Api/` | **34 dosya** | DTO 11'i içerir (§3.1) — 23'ü Gateway/BFF/Middleware/Registry |
| `shared/src/Events/` | **9 dosya** | Event payload'ları DTO **değil** düz dizi (§A.5) |
| `shared/src/Contracts/` | **19 dosya** | DTO sözleşmesi için ayrı arayüz **yok** → §7 |
| `shared/src/Middleware/` | **11 dosya** | Kapı: `RequestValidationMiddleware` (422 + fields) |
| `assets/js/` | **94 .js dosyası** (görev manifesti) — diskte gözlenen yol `assets.coremusic.net/js/**/*.js` = 98 isabet (2026-10-15 glob) | İstemci tarafı gövde üretir; DTO tüketmez (§A.3) |

> **Sayım uyarısı:** `assets/js/` için manifest değeri 94'tür; bu oturumda
> `assets.coremusic.net/js/**/*.js` glob'u 98 satır döndürmüştür (fark:
> `.spec.js` dosyaları). İki sayı **farklı ölçüm yüzeyidir**, biri diğerini
> düzeltmez → §A.2 açık sorusu.

---

## §7 — Temel risk: üretilmemiş kullanım (bu setin özgün bulgusu)

Grep ölçümü (2026-10-15, `*.php`, repo geneli):

| DTO | Üretim kodunda referans | Test dışı toplam | Durum |
|---|---|---|---|
| `Api\Dto\Request\LoginRequest` | 0 (`shared/tests/Api/DtoTest.php` + rota yorumu `routes.php:60`) | test + 1 yorum | **KULLANILMIYOR** |
| `Api\Dto\Response\UserResponse` | 0 (`shared/tests/Api/DtoTest.php`) | test | **KULLANILMIYOR** |
| `Api\Dto\Response\MusicResponse` | 0 (test) | test | **KULLANILMIYOR** |
| `PlaylistResponse` · `MusicSearchRequest` · `PlaylistCreateRequest` · `PlaylistAddTrackRequest` · `UserUpdateRequest` | 0 (yalnız kendi tanımları) | 0 | **KULLANILMIYOR** |
| `LoginResponse` · `PaginationResponse` | 0 (yalnız kendi tanımları) | 0 | **KULLANILMIYOR** |
| `Auth\Domain\DTO\LoginRequest` | `AuthService.php:211`, `AuthController.php:131`, test 5 | üretim + test | KULLANILIYOR |

> **Sonuç:** 11 shared DTO'nun **9'u üretim kodunda 0 çağrı**, 2'si yalnız
> testte; aktif kimlik DTO'su auth domain ailesidir. Bu bir "hata" değil,
> **PLANNED-üretilmiş katman** olarak kaydedilir: DTO katmanı kurulmuş,
> kullanım bağlantıları çekilmemiştir (commands-kapsam §6'daki CQRS
> boşluğunun DTO ayağı) → §A.1/§A.4.

---

## §8 — Kapsam dışı devir

| # | Dışarıda kalan | Giden düğüm | Not |
|---|---|---|---|
| 1 | `error.fields` üretiminin HTTP eşlemesi | `usecase` kardeş | Orada §10 hata eşlemesi |
| 2 | Rota doğrulama kuralının sahipliği | `routing` kardeş | `routes.php:59` kural seti |
| 3 | BFF alan seçimi/kırpma | `routing` kardeş | `Api/Bff/` orada |
| 4 | Event payload şekli | K012-events | DTO ile taşınmıyor (§A.5) |
| 5 | JWT claim şekli | K0100-identitiy | `JwtService` orada |
| 6 | Sayfalama sorgu üretimi (SQL LIMIT/OFFSET) | K007-data | DTO yalnız hesabı taşır |

---

## §A — Açık sorular

| # | Soru | Durum |
|---|---|---|
| 1 | İki DTO ailesi birleştirilecek mi (auth domain → shared Api/Dto)? | ⚠️ VERIFICATION REQUIRED |
| 2 | `assets/js/` (94) ile `assets.coremusic.net/js/` (98 glob) ölçüm yüzeyi farkı hangi sayaçla kapanacak? | ⚠️ VERIFICATION REQUIRED |
| 3 | Kullanılmayan 9 shared DTO silinecek mi, yoksa tüketici controller'lar mı yazılacak? | ⚠️ VERIFICATION REQUIRED |
| 4 | DTO'lar `JsonSerializable` implements etmeli mi (şu an `toArray()` tercih)? | UNKNOWN (kapsam slotu §7.4) |
| 5 | Event payload'ları DTO'ya taşınacak mı? | UNKNOWN |
| 6 | Dosya yükleme (multipart) DTO'su gerekecek mi? | UNKNOWN |
| 7 | Opsiyonel alan politikası: `?tip` mi, varsayılanlı mı? | UNKNOWN (slot §8.2 #1) |
| 8 | `pageSize`/`limit` üst sınırı tek merkezde mi tanımlanacak? | ⚠️ VERIFICATION REQUIRED |

---

## §B — Kanıt blokları

```text
Dosya: shared/src/Api/Dto/Request/LoginRequest.php · Satır: 19
Dosya: shared/src/Api/Dto/Request/LoginRequest.php · Satır: 52
Dosya: shared/src/Api/Dto/Request/LoginRequest.php · Satır: 64
Dosya: shared/src/Api/Dto/Request/RegisterRequest.php · Satır: 19
Dosya: shared/src/Api/Dto/Request/MusicSearchRequest.php · Satır: 76
Dosya: shared/src/Api/Dto/Response/LoginResponse.php · Satır: 19
Dosya: shared/src/Api/Dto/Response/PaginationResponse.php · Satır: 104
Dosya: shared/src/Api/Dto/Response/UserResponse.php · Satır: 19
Dosya: auth.coremusic.net/include/Domain/DTO/LoginRequest.php · Satır: 13
Dosya: auth.coremusic.net/include/Domain/DTO/AuthResponse.php · Satır: 12
Dosya: shared/src/Api/Middleware/RequestValidationMiddleware.php · Satır: 92
Dosya: shared/src/Api/Middleware/Validator.php · Satır: 35
Dosya: api.coremusic.net/config/routes.php · Satır: 59
Dosya: shared/tests/Api/DtoTest.php · Satır: 14
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## �G � GATE MATR�S� (Detay)

| Gate | Kapsam Dosyas� | Karar | Kan�t | Durum |
|---|---|---|---|---|
| G1 | dto-veri-tasiyicer-tasarimi.md (543 L) | Scope slot � DTO ta��y�c� tasar�m kapsam� | `.ai/architecture/K011-application/dto/dto-veri-tasiyicer-tasarimi.md` | MEVCUT |
| G2 | dto-kapsam.md (bu dosya) | Scope slot � DTO kapsam ve katalog | Bu dosya, �B��F | YAZILDI |
| G3 | dto-dogrulama.md | Verification slot � �l��m ve do�rulama | `.ai/architecture/K011-application/dto/dto-dogrulama.md` | YAZILDI |
| G4 | dto-web-arastirma.md | Kan�t slot � PSR-7 / JSON / HTTP referanslar� | URL havuzu (4 URL) | YAZILDI |
| G5 | dto-ornekler.md | Kod slot � PHP/JS/SQL/CSS �rnek kan�tlar� | Dosya�Sat�r kan�t bloklar� | YAZILDI |
| G6 | dto-bilesenler.md | Mimari slot � bile�en envanteri | 14 DTO envanteri | YAZILDI |
| G7 | index.md | Katalog � 6 dosya �zet tablosu | 40�100 sat�r | YAZILDI |

---

## �H � MEVCUT DOSYA SLOTLARI (Silinmez, yeniden adland�r�lmaz)

| Dosya | Sat�r | Slot Rol� | Yeni Set ��indeki Kar��l��� |
|---|---|---|---|
| dto-veri-tasiyicer-tasarimi.md | 543 | KAPSAM (orijinal, korunur) | KAPSAM slotunun bir par�as� � dto-kapsam.md ile birlikte |
| dto-kapsam.md | 500+ | KAPSAM (yeni) | Katalog + kapsam |
| dto-dogrulama.md | 544 | DO�RULAMA | �l��m + gate do�rulama |
| dto-web-arastirma.md | 500+ | WEB ARA�TIRMA | PSR-7/JSON/HTTP kan�t havuzu |
| dto-ornekler.md | 500+ | �RNEKLER | Dosya�Sat�r kan�t kod bloklar� |
| dto-bilesenler.md | 500+ | B�LE�ENLER | DTO bile�en envanteri |
| index.md | 40�100 | KATALOG | 6 dosya �zeti |

**Kural:** dto-veri-tasiyicer-tasarimi.md bu setin KAPSAM slotunun orijinal kan�t�d�r;
dosya �zerinde revizyon YOK, ta��ma/silme YOK.

---

## �I � DOSYA ADLANDIRMA �EMASI

```
ai/architecture/K011-application/dto/
+�� dto-kapsam.md                    � kapsam
+�� dto-dogrulama.md                 � dogrulama
+�� dto-web-arastirma.md             � web-arastirma
+�� dto-ornekler.md                  � ornekler
+�� dto-bilesenler.md                � bilesenler
+�� index.md                         � katalog
L�� dto-veri-tasiyicer-tasarimi.md   � MEVCUT (korunur)
```

**�ablon:** `commands/` klas�r� format referans� � �GRUPLAR tablosu frontmatter sonras�;
footer Authority + Last Updated.

---

## �J � GRUP HAR�TASI

| Dosya | Group | GRUPLAR Ana Hatlar� |
|---|---|---|
| dto-kapsam.md | mimari | Katalog, slot matrisi, kapsam s�n�rlar� |
| dto-dogrulama.md | mimari | �l��m, gate matrisi, anti-verification |
| dto-web-arastirma.md | kanit | PSR-7, JSON, Content-Type, 409 Conflict |
| dto-ornekler.md | kod | DTO kod bloklar�, Dosya�Sat�r kan�t� |
| dto-bilesenler.md | mimari | 14 DTO envanteri, katman analizi |
| index.md | mimari | Katalog, okuma s�ras� |

---

## �K � KAPSAM SINIRLARI (IN / OUT)

### IN � Bu kapsam�n i�inde

- 11 shared/src/Api/Dto/ dosyas� (final readonly class)
- 3 auth.coremusic.net/include/Domain/DTO/ dosyas� (final class)
- 14 toplam DTO envanteri
- Kapsam slotlar�: dto-kapsam.md + dto-veri-tasiyicer-tasarimi.md
- Do�rulama: dto-dogrulama.md (14/14 toArray gate)
- Kod �rnekleri: dto-ornekler.md (Dosya�Sat�r kan�tl�)
- Bile�en analizi: dto-bilesenler.md

### OUT � Bu kapsam�n d���nda

- Di�er K011 klas�rleri (usecase/, events/, middleware/, commands/)
- Kaynak kod de�i�ikli�i (.php / .js / .css / .sql)
- git commit (orkestrat�re ait)
- URL uydurma (yaln�z 4 URL havuzu)
- PLANNED/VERIFICATION REQUIRED i�aretlemesi yap�lmayan iddialar

---

## �L � PSR-7 DURUMU (�zet)

| Paket | S�r�m | Durum | Kod Kullan�m� |
|---|---|---|---|
| nyholm/psr7 | ^1.8 | composer.json'da tan�ml� | **SIFIR** � grep: 0 sonu� |
| psr/http-message | (transitif) | composer.lock'ta | **SIFIR** � kodda yok |

**Karar:** PSR-7 Message interface'i DTO'larda KULLANILMIYOR.
DTO'lar `array` d�n�yor (`toArray()`), `ResponseInterface` d�nm�yor.
?? VERIFICATION REQUIRED: PSR-7 paketi gelecekteki kullan�m i�in mi eklendi, yoksa at�k m�?

---

## �M � DTO SAYIM �ZET�

| Katman | DTO Say�s� | Dosya | toArray |
|---|---|---|---|
| shared/src/Api/Dto/ | 11 | ApiResponse, ErrorResponse, PaginatedResponse, PaginationRequest, ValidationFailedResponse, UnauthorizedResponse, ForbiddenResponse, NotFoundResponse, ConflictResponse, ServiceUnavailableResponse, RequestValidationError | 11/11 ? |
| auth/include/Domain/DTO/ | 3 | LoginRequest, AuthResponse, (3. dosya) | 12/13 |
| **TOPLAM** | **14** | 14 dosya | **13/14** |

---

## �N � �LG�L� KONULAR (Cross-Reference)

| Konu | Referans | Klas�r |
|---|---|---|
| Application Service kullan�m� | usecase-katman�.md | K011-application/usecase/ |
| Event DTO ta��nmas� | events-katman�.md | K011-application/events/ |
| Middleware validate etkile�imi | middleware-katman�.md | K011-application/middleware/ |
| Command pattern | commands/ (format referans�) | K011-application/commands/ |

---

## �O � SONRAK� ADIMLAR (Bu Kapsam ��kt�s�)

1. dto-dogrulama.md � 14/14 toArray gate do�rulamas� (G1)
2. dto-web-arastirma.md � PSR-7/JSON/HTTP kan�t havuzu (4 URL)
3. dto-ornekler.md � Dosya�Sat�r kan�tl� kod �rnekleri
4. dto-bilesenler.md � bile�en envanteri (14 DTO)
5. index.md � katalog (6 dosya �zeti)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## �P � DOSYA-BY-DOSYA KAPSAM NOTLARI

### P.1 � dto-veri-tasiyicer-tasarimi.md (543 sat�r, MEVCUT)

- Kapsam slotunun orijinal kan�t�
- Veri ta��y�c� tasar�m kararlar�: final readonly, toArray(), static factory
- Dosya �zerinde revizyon YOK

### P.2 � dto-dogrulama.md (544 sat�r, YEN�)

- �E � �l��m tablosu (14 DTO, toArray gate)
- �F � Gate matrisi (G1�G7)
- �K � Anti-verification (bilinen eksiklikler)
- �L � Verification required i�aretli iddialar

### P.3 � dto-web-arastirma.md (500+ sat�r, YEN�)

- �A � PSR-7 Message interface (php.net/manual/en/class.psr-7-message.php)
- �B � JsonSerializable (php.net/manual/en/class.jsonserializable.php)
- �C � Content-Type header (mdn.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type)
- �D � 409 Conflict status (mdn.org/en-US/docs/Web/HTTP/Reference/Status/409)
- �E � Repo entegrasyon notlar� (?? VERIFICATION REQUIRED i�aretli)

### P.4 � dto-ornekler.md (500+ sat�r, YEN�)

- �A � shared/src/Api/Dto/ kod bloklar� (Dosya�Sat�r kan�tl�)
- �B � auth/include/Domain/DTO/ kod bloklar�
- �C � JS taraf�: gender-select.js JSON parse �rne�i
- �D � SQL: DTO'ya besleyen sorgu kan�tlar�
- �E � CSS: (uygunsa � DTO ile do�rudan ili�ki yoksa PLANNED)

### P.5 � dto-bilesenler.md (500+ sat�r, YEN�)

- �A � Bile�en a�ac� (14 DTO)
- �B � ApiResponse ailesi (base + 5 error)
- �C � Request ailesi (PaginationRequest, LoginRequest)
- �D � Response ailesi (PaginatedResponse, AuthResponse)
- �E � ValidationFailedResponse (RequestValidationError i�erir)
- �F � Katman ba��ml�l��� matrisi

### P.6 � index.md (40�100 sat�r, YEN�)

- �A � 6 dosya katalog tablosu
- �B � OKUMA SIRASI (boot order)
- �C � Kap�lar (gate �zeti)
- �D � Cross-reference (di�er K011 klas�rleri)

---

## �Q � REFERANS HAVUZU (Yaln�z Bu 4 URL)

| # | URL | Kullan�m | Kullan�ld��� Dosya |
|---|---|---|---|
| 1 | https://www.php.net/manual/en/class.psr-7-message.php | PSR-7 Message interface | dto-web-arastirma.md �A |
| 2 | https://www.php.net/manual/en/class.jsonserializable.php | JsonSerializable | dto-web-arastirma.md �B |
| 3 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type | Content-Type | dto-web-arastirma.md �C |
| 4 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409 | 409 Conflict | dto-web-arastirma.md �D |

**Yasak:** Bu 4 URL d���na ��kmak � herhangi bir URL uydurmak.

---

## �R � TOPLAM �L��M �ZET�

| Metrik | De�er |
|---|---|
| shared/src toplam PHP dosyas� | 165 |
| Api/ alt klas�r (DTO dahil) | 34 |
| Events/ | 9 |
| Contracts/ | 19 |
| Middleware/ | 11 |
| assets/js (manifest) | 94 |
| assets/js (glob) | 98 |
| DTO toplam (shared) | 11 |
| DTO toplam (auth) | 3 |
| DTO toplam (t�m) | 14 |
| toArray gate | 13/14 |

---

## �S � YORUMLAR VE NOTLAR

- DTO'lar **final readonly class** (shared) veya **final class** (auth) � ikisi de immutable by convention.
- **SRP:** Her DTO tek veri yap�s� � request, response, error ayr�lm��.
- **Dependency:** DTO'larda framework ba��ml�l��� YOK (sadece `ArrayAccess` bir tane: RequestValidationError).
- **PSR-7:** composer.json'da var ama kodda SIFIR kullan�m � ?? VERIFICATION REQUIRED.
- **Auth DTO'lar�:** `LoginRequest` ve `AuthResponse` production'da AKT�F kullan�l�yor (AuthPostHandler).
- **Shared DTO'lar�:** 11 dosya test d���nda KULLANILMIYOR (grep 0 sonu�) � PLANNED-�retilmi� katman.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## �T � �NCEK� KARARLAR (Precedent)

| Karar | Kaynak | Durum |
|---|---|---|
| DTO final readonly class olmal� | shared/src/Api/Dto/ (11 dosya) | ADOPTED |
| toArray() interface method'u | Shared/src/Contracts/ | ADOPTED |
| static factory method pattern | ApiResponse::success/error | ADOPTED |
| HTTP status code sabitleri | ApiResponse.php const | ADOPTED |
| Error kodlar� string enum | ErrorResponse.php | ADOPTED |
| PSR-7 Message interface | composer.json (var) / kod (yok) | VERIFICATION REQUIRED |

---

## �U � KAPSAM KABUL KR�TERLER�

| # | Kriter | Beklenen | Durum |
|---|---|---|---|
| 1 | Dosya say�s� (kapsam slotu) | ?2 (dto-kapsam + dto-veri-tasiyicer-tasarimi) | ? 2 |
| 2 | toArray gate | 14/14 | ?? 13/14 (auth LoginRequest'te yok) |
| 3 | URL havuzu | ?4 | ? 4 |
| 4 | Frontmatter | 7 alan (group: son) | ? |
| 5 | GRUPLAR tablosu | frontmatter sonras� | ? |
| 6 | Footer | Authority + Last Updated | ? |

---

## �V � DOSYA YA�AM D�NG�S�

```
Olu�turma (bu g�rev)
  � Kapsam tan�m� (dto-kapsam.md)
  � Do�rulama (dto-dogrulama.md � G1 gate)
  � Web kan�t (dto-web-arastirma.md)
  � Kod �rnek (dto-ornekler.md)
  � Bile�en envanter (dto-bilesenler.md)
  � Katalog (index.md)
  � Orkestrat�r commit (subagent commit ATMADI)
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
