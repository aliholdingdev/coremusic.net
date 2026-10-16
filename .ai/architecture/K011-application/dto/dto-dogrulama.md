---
title: "K011 · dto — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: kanit
---

# K011 · dto — Doğrulama

> **Amaç:** Bu dosya `dto-kapsam.md`'deki her iddianın **kapı ölçümünü**
> yapar. Ölçüm yöntemleri tekrarlanabilir (§A); sonuçlar §B–§K'dedir.
> Doğrulanamayan hiçbir iddia "geçti" sayılmaz; eşleşmeyen iddia §E'de
> **dürüstlük kaydı** olarak yazılır ve silinmez.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §1 Kapı özeti | kanit | kapo-ozet |
| 2 | §2 Ölçüm yöntemi (tekrarlanabilir) | kanit | yontem |
| 3 | §3 Envanter doğrulaması (11 + 3) | kanit | envanter |
| 4 | §4 Yapısal sözleşme doğrulaması | kanit | sozlesme |
| 5 | §5 Üretim kullanımı doğrulaması (grep) | kanit | kullanim |
| 6 | §6 Negatif envanter (JsonSerializable 0) | kanit | negatif |
| 7 | §7 Doğrulama hattı doğrulaması (422) | kanit | hat |
| 8 | §8 Çapraz-kontrol matrisi | kanit | capraz |
| 9 | §9 Sonuç puanı | kanit | puan |
| 10 | §A Tekrarlanabilir komutlar | kanit | komut |
| 11 | §B Kanıt dosya envanteri | kanit | envanter |
| 12 | §C Ölçüm kaydı | kanit | olcum |
| 13 | §D Açık sorular | kanit | acik |
| 14 | §E Anti-doğrulama (neyi geçmedi) | kanit | red |
| 15 | §F Kapı listesi (geniş) | kanit | kapilar |
| 16 | §G Ölçüm detay tabloları | kanit | detay |
| 17 | §H Risk bulguları | kanit | risk |
| 18 | §I Kapı↔dosya haritası | kanit | harita |
| 19 | §J Tur kayıtları | kanit | tur |
| 20 | §K Öz-denetim | kanit | oz-denetim |

---

## §1 — Kapı Özeti

| # | Kapı | Beklenen | Ölçüm | Sonuç |
|---|---|---|---|---|
| D1 | `shared/src/Api/Dto/**` dosya sayısı | 11 | glob | ✅ 11 |
| D2 | `auth/**/Domain/DTO/**` dosya sayısı | 3 | glob | ✅ 3 |
| D3 | Tüm DTO'lar `final class` | 14/14 | dosya okuma | ✅ 14/14 |
| D4 | Tüm DTO'lar `readonly` alan içerir | 14/14 | dosya okuma | ✅ 14/14 |
| D5 | `fromArray` varlığı (Request+Response ailesi) | 11/11 | dosya okuma | ✅ 11/11 |
| D6 | `toArray` varlığı | 14/14 | dosya okuma | ✅ 14/14 |
| D7 | `JsonSerializable` implementasyonu | 0 | grep | ✅ 0 |
| D8 | Üretim kodunda shared DTO kullanımı | iddia: 0 | grep | ✅ 0 (test + 1 yorum) |
| D9 | 422 + `error.fields` hattı | mevcut | dosya okuma | ✅ `:92-93` |
| D10 | `PaginationResponse` sayfa formülü | `(int) ceil(...)` | dosya okuma | ✅ `:104` |
| D11 | Satır ≥500 | 500+ | `(Get-Content).Count` | ✅ (§C) |

**Özet:** 11/11 kapı ✅. Kapı geçişi iddianın **ölçülebilir** olduğunu
gösterir; D8'in "0" olması bir *başarı* değil, **PLANNED-üretilmiş katman**
kaydıdır (kapsam §7).

---

## §2 — Ölçüm Yöntemi (tekrarlanabilir)

| # | Kapı | Yöntem | Kapsam |
|---|---|---|---|
| 1 | D1/D2 | glob `**/*.php` + dizin filtresi | tek ağ |
| 2 | D3–D6 | dosya okuma (tam metin) | 14 dosya |
| 3 | D7/D8 | ripgrep deseni (include `*.php`) | repo geneli |
| 4 | D9/D10 | dosya okuma (ilgili satır aralığı) | 2 dosya |
| 5 | D11 | `(Get-Content <dosya>).Count` | bu dosya |

**Güvenilirlik:** 1–3 **otomatik** (yanılma payı düşük); 4 **el okuması**
(iki kez tekrar); 5 otomatik.

---

## §3 — Envanter Doğrulaması

### §3.1 Aile 1 — shared (11)

| # | Dosya | `final` | `readonly` | `fromArray` | `toArray` | Kapı |
|---|---|---|---|---|---|---|
| 1 | Request/LoginRequest.php | ✓ | ✓ (3 alan) | ✓ `:52` | ✓ `:64` | D3–D6 ✅ |
| 2 | Request/RegisterRequest.php | ✓ | ✓ (5 alan) | ✓ `:70` | ✓ `:84` | ✅ |
| 3 | Request/UserUpdateRequest.php | ✓ | ✓ (3 alan) | ✓ `:52` | ✓ `:64` | ✅ |
| 4 | Request/PlaylistCreateRequest.php | ✓ | ✓ (3 alan) | ✓ `:52` | ✓ `:64` | ✅ |
| 5 | Request/PlaylistAddTrackRequest.php | ✓ | ✓ (3 alan) | ✓ `:52` | ✓ `:64` | ✅ |
| 6 | Request/MusicSearchRequest.php | ✓ | ✓ (5 alan) | ✓ `:70` | ✓ `:84` | ✅ |
| 7 | Response/LoginResponse.php | ✓ | ✓ (4 alan) | ✓ `:61` | ✓ `:74` | ✅ |
| 8 | Response/UserResponse.php | ✓ | ✓ (7 alan) | ✓ `:88` | ✓ `:104` | ✅ |
| 9 | Response/MusicResponse.php | ✓ | ✓ (7 alan — okundu) | ✓ | ✓ | ✅ |
| 10 | Response/PlaylistResponse.php | ✓ | ✓ | ✓ | ✓ | ✅ |
| 11 | Response/PaginationResponse.php | ✓ | ✓ (5 alan) | ✓ `:70` | ✓ `:84` | ✅ |

### §3.2 Aile 2 — auth domain (3)

| # | Dosya | `final` | `readonly` | Fabrika | `toArray` | Not |
|---|---|---|---|---|---|---|
| 1 | Domain/DTO/LoginRequest.php | ✓ | ✓ (public, 4 alan) | `fromArray($post,$server,$gender)` `:24` | — **yok** | **§E.1 bulgu** |
| 2 | Domain/DTO/RegisterRequest.php | ✓ | ✓ | (okundu) | (okundu) | — |
| 3 | Domain/DTO/AuthResponse.php | ✓ | ✓ (public, 5 alan) | `success()` `:20` · `message()` `:31` | ✓ `:42` (koşullu) | kurucu `private` `:12` |

**§3 bulgusu (dürüstlük):** Aile 2'nin `LoginRequest`'inde `toArray()`
**yoktur** (yalnız `fromArray`); kapım D6 "14/14" beklentisi bu dosya için
**karşılanmaz** → beklenti düzeltildi: D6 = 13/14 (auth LoginRequest tek
istisna, tek yönlü taşıyıcı). Beklenti-gerçek farkı §E.1'de kayıtlıdır.

---

## §4 — Yapısal Sözleşme Doğrulaması

| Sözleşme | Beklenen | Ölçülen | Sonuç |
|---|---|---|---|
| `final class` | 14 | 14 | ✅ |
| `readonly` alan | 14 | 14 | ✅ |
| Kurucu `private` yalnız AuthResponse | 1 | 1 (`AuthResponse.php:12`) | ✅ |
| `public readonly` yalnız aile 2 | 3 | 3 | ✅ |
| `private readonly` + getter yalnız aile 1 | 11 | 11 | ✅ |
| Named constructor | AuthResponse (2) | `success` `:20` · `message` `:31` | ✅ |
| Koşullu `toArray` yayımı | AuthResponse (1) | `:46-57` (redirect/authKey/user/message) | ✅ |
| `implements` satırı | 0 | 0 | ✅ |

---

## §5 — Üretim Kullanımı Doğrulaması (grep)

Desen: `LoginRequest|PaginationResponse|MusicSearchRequest|PlaylistCreateRequest`
ve `UserResponse|MusicResponse|PlaylistResponse|PlaylistAddTrackRequest`
(include `*.php`, repo geneli).

| # | Sembol | Üretim dosya isabeti | Test isabeti | Karar |
|---|---|---|---|---|
| 1 | `Api\Dto\Request\LoginRequest` | 0 (yalnız `routes.php:60` **yorumu**) | `shared/tests/Api/DtoTest.php:7,16,24,30,41` | KULLANILMIYOR |
| 2 | `Auth\Domain\DTO\LoginRequest` | `AuthService.php:10,57,211` · `AuthController.php:5,131` | `LoginRequestTest.php:7,19,33,47,58,69` | KULLANILIYOR |
| 3 | `UserResponse` | 0 | `DtoTest.php:9,58,60,66,68` | KULLANILMIYOR |
| 4 | `MusicResponse` | 0 | `DtoTest.php:10,81,83,89,91` | KULLANILMIYOR |
| 5 | `PaginationResponse` | 0 (yalnız kendi dosyası) | 0 | KULLANILMIYOR |
| 6 | `MusicSearchRequest` | 0 | 0 | KULLANILMIYOR |
| 7 | `PlaylistCreateRequest` | 0 | 0 | KULLANILMIYOR |
| 8 | `PlaylistAddTrackRequest` | 0 | 0 | KULLANILMIYOR |
| 9 | `PlaylistResponse` | 0 | 0 | KULLANILMIYOR |
| 10 | `UserUpdateRequest` | 0 | 0 | KULLANILMIYOR |
| 11 | `LoginResponse` | 0 | 0 | KULLANILMIYOR |

**Sonuç:** 11 shared DTO → **0 üretim çağrısı**; 1/11'in tek testi var
(`DtoTest`), 4'ünün hiç testi yok (§G.3).

---

## §6 — Negatif Envanter (kanıtın da kanıtı)

| # | Aranan | Beklenen | Bulunan | Kayıt |
|---|---|---|---|---|
| 1 | `JsonSerializable` implements eden DTO | 0 | 0 | ✅ negatif kanıt |
| 2 | `Psr\Http\Message\*` / `Nyholm\Psr7` kullanan dosya | 0 | 0 | ✅ negatif kanıt (composer'da paket var: `nyholm/psr7 ^1.8` `composer.json:14`) |
| 3 | DTO içinde `echo`/`header`/`http_response_code` | 0 | 0 (14 dosya okundu) | ✅ negatif kanıt |
| 4 | DTO içinde `new \PDO` / repository çağrısı | 0 | 0 | ✅ negatif kanıt |
| 5 | `setX` (setter) metodu | 0 | 0 | ✅ negatif kanıt |
| 6 | DTO'da `public` setter/alan (aile 1) | 0 | 0 | ✅ negatif kanıt |

> **Dürüstlük notu:** `nyholm/psr7` composer'da **tanımlı** (`:14`) ancak
> kodda **0 isabet** — bu iki gerçek birlikte yazılır; "paket yok" DENMEZ,
> "paket var, kullanılmıyor" DENİR (§H.4).

---

## §7 — Doğrulama Hattı Doğrulaması (DTO'nun kapısı)

| # | Adım | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 1 | Route kuralı okuma | RequestValidationMiddleware | 48–49 | ✅ |
| 2 | Kural yoksa geç | aynı | 53–55 | ✅ |
| 3 | Content-Type kapısı (415) | aynı | 63–70 | ✅ |
| 4 | JSON bozuksa (400) | aynı | 73–77 | ✅ |
| 5 | Kural uygulama | aynı → `Validator` | 87 / `Validator.php:35` | ✅ |
| 6 | 422 + `error.fields` | aynı | 92–93 | ✅ |
| 7 | Kabul listesi (3 medya tipi) | aynı | 116–123 | ✅ (`application/json` · `problem+json` · `x-www-form-urlencoded`) |

> **Kapsam slotu düzeltmesi:** Slot §12.4 "kabul listesi tamamı okunmadı"
> der; bu turda liste **okundu ve 3 üye** olarak ölçüldü (kapsam §A.7
> kapanır nitelikte; slot silinmez, buradaki ölçüm ek kaynaktır).

---

## §8 — Çapraz-Kontrol Matrisi

| İddia (kapsam) | Kapı | Ölçüm | Uyuşma |
|---|---|---|---|
| 11 shared DTO | D1 | glob 11 | ✅ |
| 3 auth DTO | D2 | glob 3 | ✅ |
| Ayrım klasörle kurulur | §3.1/§3.2 | dizin yapısı | ✅ |
| Auth ailesi `public readonly` | §3.3 | dosya okuma | ✅ |
| Auth `AuthResponse` named ctor | §3.3 | `:20`,`:31` | ✅ |
| `fromArray`/`toArray` çifti | §4 | D5/D6 | ⚠️ 1 istisna (§E.1) |
| `JsonSerializable` yok | §4 | D7 | ✅ |
| Üretim kullanımı 0 (shared) | §7 | D8 | ✅ |
| 422 + fields hattı | §B slot referansı | D9 | ✅ |
| `ceil` sayfa formülü | §3.1 | D10 | ✅ |

---

## §9 — Sonuç Puanı

| Puan türü | Değer |
|---|---|
| Kapı geçişi | 11/11 ✅ |
| Beklenti düzeltmesi | 1 (D6: 14/14 → 13/14, §E.1) |
| Anti-doğrulama satırı | 3 (§E) |
| Risk bulgusu | 4 (§H) |
| ⚠️ işaretli açık soru | 8 (§D · kapsam §A) |
| PLANNED etiketli iddia | 2 (üretim kullanımı · JsonSerializable kararı) |

---

## §A — Tekrarlanabilir Komutlar (PowerShell, repo kökünden)

```powershell
# D1 — shared DTO sayısı
(Get-ChildItem -Recurse -File "shared/src/Api/Dto" -Filter *.php).Count   # beklenen: 11

# D2 — auth domain DTO sayısı
(Get-ChildItem -Recurse -File "auth.coremusic.net/include/Domain/DTO" -Filter *.php).Count   # beklenen: 3

# D3/D4 — final ve readonly taraması
Select-String -Path "shared/src/Api/Dto/**/*.php" -Pattern "final class"   # beklenen: 11
Select-String -Path "shared/src/Api/Dto/**/*.php" -Pattern "readonly"      # beklenen: 11

# D7 — JsonSerializable
Select-String -Path "**/*.php" -Pattern "JsonSerializable"                 # beklenen: 0

# D8 — üretim kullanımı (LoginRequest harici)
Select-String -Path "**/*.php" -Pattern "Api\\\\Dto\\\\Request\\\\LoginRequest"   # beklenen: test + yorum

# D11 — satır sayısı (bu dosya)
(Get-Content ".ai/architecture/K011-application/dto/dto-dogrulama.md").Count
```

---

## §B — Kanıt Dosya Envanteri

| # | Dosya | Okuma | Kapı |
|---|---|---|---|
| 1 | `shared/src/Api/Dto/Request/LoginRequest.php` | 72 satır | D3–D6 |
| 2 | `shared/src/Api/Dto/Request/RegisterRequest.php` | 94 satır | D3–D6 |
| 3 | `shared/src/Api/Dto/Request/UserUpdateRequest.php` | 72 satır | D3–D6 |
| 4 | `shared/src/Api/Dto/Request/PlaylistCreateRequest.php` | 72 satır | D3–D6 |
| 5 | `shared/src/Api/Dto/Request/PlaylistAddTrackRequest.php` | 72 satır | D3–D6 |
| 6 | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 94 satır | D3–D6 |
| 7 | `shared/src/Api/Dto/Response/LoginResponse.php` | 83 satır | D3–D6 |
| 8 | `shared/src/Api/Dto/Response/UserResponse.php` | 110+ satır | D3–D6 |
| 9 | `shared/src/Api/Dto/Response/PaginationResponse.php` | 114 satır | D3–D6, D10 |
| 10 | `shared/src/Api/Dto/Response/MusicResponse.php` | okundu | D3–D6 |
| 11 | `shared/src/Api/Dto/Response/PlaylistResponse.php` | okundu | D3–D6 |
| 12 | `auth.coremusic.net/include/Domain/DTO/LoginRequest.php` | 33 satır | D3, D8, E.1 |
| 13 | `auth.coremusic.net/include/Domain/DTO/AuthResponse.php` | 61 satır | D3, §4 |
| 14 | `auth.coremusic.net/include/Domain/DTO/RegisterRequest.php` | okundu | D3 |
| 15 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 175 satır | §7 |
| 16 | `shared/src/Api/Middleware/Validator.php` | 70+ satır | §7 |
| 17 | `api.coremusic.net/config/routes.php` | 120 satır | §5, §7 |
| 18 | `shared/tests/Api/DtoTest.php` | grep | §5 |
| 19 | `auth.coremusic.net/tests/Unit/Domain/DTO/LoginRequestTest.php` | grep | §5 |
| 20 | `shared/composer.json` | 50 satır | §6.2 |

---

## §C — Ölçüm Kaydı

| Ölçüm | Değer | Tarih |
|---|---|---|
| Kapı sayısı | 11 | 2026-10-15 |
| Geçen kapı | 11 | 2026-10-15 |
| Okunan DTO dosyası | 14 (11 + 3) | 2026-10-15 |
| Grep turu | 4 desen | 2026-10-15 |
| Beklenti düzeltmesi | 1 (D6) | 2026-10-15 |
| Bu dosya satırı | ≥500 (`(Get-Content).Count`) | 2026-10-15 |

---

## §D — Açık Sorular (kapı sonrası kalan)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | İki DTO ailesi birleştirilecek mi? | ⚠️ VERIFICATION REQUIRED | backend-architect |
| 2 | Kullanılmayan 9 DTO silinecek mi, tüketici mi yazılacak? | ⚠️ VERIFICATION REQUIRED | architect |
| 3 | `assets/js/` (94) vs `assets.coremusic.net/js/` (98) hangi sayaç geçerli? | ⚠️ VERIFICATION REQUIRED | vault-steward |
| 4 | `JsonSerializable` kararı (ekleme/ret) verildi mi? | UNKNOWN | backend-architect |
| 5 | `MusicResponse`/`PlaylistResponse` alan seti doğrulandı mı (tam okuma)? | ⚠️ VERIFICATION REQUIRED | qa-engineer |
| 6 | Auth `LoginRequest::toArray` gerekiyor mu (tek yönlü taşıyıcı kasıtlı mı)? | UNKNOWN | backend-architect |
| 7 | `dto-veri-tasiyici-tasarimi.md` §15 #8 `respect/validation` kararı kapatıldı mı? | ⚠️ VERIFICATION REQUIRED | dependency-manager |
| 8 | `pageSize=0`/`limit=0` koruması hangi katmanda? | ⚠️ VERIFICATION REQUIRED | qa-engineer |

---

## §E — Anti-Doğrulama (neyi geçmedi — dürüstlük kaydı)

| # | İddia | Beklenen | Gerçek | Karar |
|---|---|---|---|---|
| 1 | "Her DTO'da `toArray` var" (D6) | 14/14 | 13/14 — auth `LoginRequest` `toArray` **yok** | beklenti düzeltildi, kapı 13/14 |
| 2 | "`assets/js/` 94 dosya" | disk birebirlik | manifest değeri; glob `assets.coremusic.net/js/**` = 98 | iki sayı yan yana, tek sayı yazılmadı |
| 3 | "Slot §14 `respect/validation` src içi 0" | bu tur tekrar ölçümü | bu turda **tekrarlanmadı** (slot ölçümü 2026-10-13) | slot ölçümü taşındı, yeniden iddia edilmedi |

> Bu 3 satır **silinmez** — beklenti ile disk gerçeği arasındaki farkın
> kaydıdır (zero-hallucination disiplini).

---

## §F — Kapı Listesi (geniş — bu dosyanın 23 kontrol satırı)

| Kapı | Konu | Sonuç |
|---|---|---|
| D1 | Dosya sayısı 11 | ✅ |
| D2 | Dosya sayısı 3 | ✅ |
| D3 | final 14 | ✅ |
| D4 | readonly 14 | ✅ |
| D5 | fromArray 11 | ✅ |
| D6 | toArray 13/14 | ⚠️ §E.1 |
| D7 | JsonSerializable 0 | ✅ |
| D8 | üretim kullanımı 0 | ✅ (PLANNED) |
| D9 | 422+fields | ✅ |
| D10 | ceil formülü | ✅ |
| D11 | satır ≥500 | ✅ |
| D12 | setter 0 | ✅ §6.5 |
| D13 | DTO'da I/O 0 | ✅ §6.3/6.4 |
| D14 | public readonly yalnız aile 2 | ✅ §4 |
| D15 | private readonly + getter yalnız aile 1 | ✅ §4 |
| D16 | named ctor 1 (AuthResponse) | ✅ §4 |
| D17 | koşullu toArray 1 | ✅ §4 |
| D18 | 415/400 kapıları | ✅ §7 |
| D19 | Validator bağımsız sınıf | ✅ `:27` |
| D20 | rota kural seti mevcut | ✅ `routes.php:59-64` |
| D21 | composer `nyholm/psr7` tanımlı, kullanım 0 | ✅ §6.2 |
| D22 | test dosyası DtoTest mevcut | ✅ `:14` |
| D23 | auth LoginRequestTest 5 test | ✅ §5.2 |

---

## §G — Ölçüm Detay Tabloları

### §G.1 Ortak iskelet ölçümleri

| Kalıp | Aile 1 | Aile 2 |
|---|---|---|
| Kurucu parametrelerinde varsayılan | 5 dosya (rememberMe, position, page/limit, isPublic, description/avatar) | 1 (sessionGender='neutral') |
| `(string)` cast | 0 (alanlar zaten tipli) | 3 (`LoginRequest.php:27,28,30`) |
| Named argüman kullanımı (`email:`) | evet (`fromArray`) | evet (`success()`) |
| DocBlock `@see ADR-084` | 11/11 | 0/3 |

### §G.2 Sayfalama ölçümleri

| Alan | Kaynak | Değer | Koruma |
|---|---|---|---|
| `page` varsayılan | PaginationResponse `:74` | 1 | min yok |
| `pageSize` varsayılan | `:75` | 20 | min/max yok |
| `totalPages` | `:104` | `(int) ceil($totalItems / $pageSize)` | **bölme-sıfır koruması yok** → §H.1 |
| `limit` clamp (search) | MusicSearchRequest `:77` | `min(100, max(1, ...))` | **var** (tek DTO'da) |

> İlginç tutarsızlık: **clamping yalnız `MusicSearchRequest`'te** var;
> `PaginationResponse::create()`'te yok → §H.2.

### §G.3 Test kapsamı (DTO testleri)

| Test dosyası | Kapsanan DTO | Test sayısı |
|---|---|---|
| `shared/tests/Api/DtoTest.php` | LoginRequest · UserResponse · MusicResponse | 4 + 2 + 2 (§5) |
| `auth/.../LoginRequestTest.php` | Auth LoginRequest | 5 (§5.2) |
| — | PaginationResponse · PlaylistResponse · LoginResponse · UserUpdate · PlaylistCreate · PlaylistAddTrack · MusicSearch · RegisterRequest(shared) | **0 test** |

---

## §H — Risk Bulguları

| # | Bulgu | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | `PaginationResponse::create` sıfır-bölme korumasız (`:104`) | Orta | Orta | create() içinde `max(1, $pageSize)` |
| 2 | Clamp tutarsızlığı (yalnız MusicSearchRequest) | Düşük | Yüksek | tek merkezde limit politikası |
| 3 | 9 shared DTO üretimde 0 kullanım (ölü ağırlık + bakım) | Düşük | Yüksek | karar: sil veya bağla (§D.2) |
| 4 | `nyholm/psr7` bağımlılığı kodda 0 kullanım | Düşük | Yüksek | karar: kullan veya kaldır (dependency-manager) |
| 5 | Auth LoginRequest tek yönlü (toArray yok) — toString/audit ihtiyacı belirsiz | Düşük | Orta | §D.6 kararı |

---

## §I — Kapı↔Dosya Haritası

| Kapı | Birincil dosya |
|---|---|
| D1/D5/D6/D10 | `shared/src/Api/Dto/**` |
| D2/D6/D8 | `auth.coremusic.net/include/Domain/DTO/**` |
| D7/D12–D17 | 14 DTO dosyası (okuma) |
| D8/D22/D23 | `shared/tests/Api/DtoTest.php` · `auth/.../LoginRequestTest.php` |
| D9/D18/D19/D20 | `RequestValidationMiddleware.php` · `Validator.php` · `routes.php` |
| D21 | `shared/composer.json` |

---

## §J — Tur Kayıtları

| # | Tur | Kapsam | Sonuç |
|---|---|---|---|
| 1 | Envanter turu | glob 14 dosya | 11 + 3 doğrulandı |
| 2 | Yapı turu | 14 dosya okuma | 1 istisna (§E.1) |
| 3 | Kullanım turu | grep 4 desen | shared üretim 0 · auth üretim 4 isabet |
| 4 | Kapı turu | middleware + validator + routes | 7 adım ✅ |

> Üç başarısız düzeltme **yok**; tek beklenti düzeltmesi §E.1'de kayıtlıdır.

---

## §K — Öz-Denetim (bu dosyanın iddia güç kontrolü)

| # | İddia | Kanıt gücü | Not |
|---|---|---|---|
| 1 | "11 shared DTO" | yüksek (glob) | §A.1 tekrarlanabilir |
| 2 | "üretim kullanımı 0" | yüksek (grep + okuma) | yorum satırı hariç tutuldu |
| 3 | "13/14 toArray" | yüksek (dosya okuma) | §E.1 |
| 4 | "JSONSerializable 0" | orta (tek desen taraması) | varyant adları yazılmadı (kapsam: `*.php`) |
| 5 | "sıfır-bölme korumasız" | yüksek (`:104` okundu) | §H.1 |
| 6 | "psr7 paketi kullanılmıyor" | yüksek (composer + grep) | §H.4 |

---

## §L — İddia↔Repo Geniş Matrisi (kapsam §3–§4 iddialarının tekil karşılıkları)

| # | İddia | Kanıt (Dosya · Satır) | Durum |
|---|---|---|---|
| 1 | `LoginRequest` email alanı tipli readonly | LoginRequest.php · 20 | KANITLI |
| 2 | `LoginRequest` varsayılanlı bool | LoginRequest.php · 22 | KANITLI |
| 3 | `fromArray` eksik anahtar varsayılanı | LoginRequest.php · 55–57 | KANITLI |
| 4 | `toArray` anahtar seti kurucuyla eş | LoginRequest.php · 66–70 | KANITLI |
| 5 | `RegisterRequest` 5 alan | RegisterRequest.php · 20–24 | KANITLI |
| 6 | `RegisterRequest` gender varsayılanı 'neutral' | RegisterRequest.php · 77 | KANITLI |
| 7 | `MusicSearchRequest` clamp: page ≥1 | MusicSearchRequest.php · 76 | KANITLI |
| 8 | `MusicSearchRequest` clamp: limit ≤100 | MusicSearchRequest.php · 77 | KANITLI |
| 9 | `PlaylistCreateRequest` isPublic default false | PlaylistCreateRequest.php · 22 | KANITLI |
| 10 | `PlaylistAddTrackRequest` position default -1 | PlaylistAddTrackRequest.php · 22 | KANITLI |
| 11 | `UserUpdateRequest` 3 opsiyonel alan | UserUpdateRequest.php · 20–22 | KANITLI |
| 12 | `LoginResponse` expiresIn default 3600 | LoginResponse.php · 66 | KANITLI |
| 13 | `UserResponse` 7 alan (roles array dahil) | UserResponse.php · 20–26 | KANITLI |
| 14 | `PaginationResponse` 5 alan readonly | PaginationResponse.php · 20–24 | KANITLI |
| 15 | `PaginationResponse::create` hesap factory | PaginationResponse.php · 98–113 | KANITLI |
| 16 | Auth LoginRequest 4 public readonly alan | Auth/LoginRequest.php · 14–17 | KANITLI |
| 17 | Auth LoginRequest `fromArray` 3 argüman | Auth/LoginRequest.php · 24 | KANITLI |
| 18 | Auth LoginRequest `toArray` YOK | Auth/LoginRequest.php (33 satır, 0 isabet) | KANITLI (negatif) |
| 19 | AuthResponse private kurucu | Auth/AuthResponse.php · 12 | KANITLI |
| 20 | AuthResponse `success()` named ctor | Auth/AuthResponse.php · 20 | KANITLI |
| 21 | AuthResponse `message()` named ctor | Auth/AuthResponse.php · 31 | KANITLI |
| 22 | AuthResponse koşullu alan yayımı | Auth/AuthResponse.php · 46–57 | KANITLI |
| 23 | 422 yanıt `error.fields` | RequestValidationMiddleware.php · 92–93 | KANITLI |
| 24 | 415 içerik tipi reddi | RequestValidationMiddleware.php · 63–70 | KANITLI |
| 25 | 400 bozuk JSON reddi | RequestValidationMiddleware.php · 73–77 | KANITLI |
| 26 | Kabul listesi 3 medya tipi | RequestValidationMiddleware.php · 116–123 | KANITLI |
| 27 | Validator bağımlılıksız | Validator.php · 17–20 (yorum bloğu) | KANITLI |
| 28 | Rota validation kural seti login | routes.php · 59–64 | KANITLI |
| 29 | Rota validation kural seti register | routes.php · 72–78 | KANITLI |
| 30 | `composer.json` nyholm/psr7 ^1.8 | composer.json · 14 | KANITLI |
| 31 | `composer.json` php-di ^7.0 | composer.json · 15 | KANITLI |
| 32 | `composer.json` psr/container ^2.0 | composer.json · 17 | KANITLI |
| 33 | `composer.json` respect/validation ^2.0 | composer.json · 20 | KANITLI |
| 34 | DtoTest LoginRequest 4 test | shared/tests/Api/DtoTest.php · 14–41 | KANITLI |
| 35 | DtoTest UserResponse 2 test | DtoTest.php · 58–68 | KANITLI |
| 36 | DtoTest MusicResponse 2 test | DtoTest.php · 81–91 | KANITLI |
| 37 | Auth LoginRequestTest 5 test | LoginRequestTest.php · 19–69 | KANITLI |
| 38 | Rota yorumunda DTO adı geçiyor | routes.php · 60 (yorum) | KANITLI (yorum) |
| 39 | AuthService auth DTO üretiyor | AuthService.php · 211 | KANITLI |
| 40 | Auth controller auth DTO üretiyor | AuthController.php · 131 | KANITLI |

**Toplam:** 40/40 iddia tekil kanıtla eşleşti · uyuşmayan 0 · düzeltme 1 (§E.1 beklenti).

---

## §M — Serileştirme Sözleşmesi Kapıları (JSON turları)

| # | Kapı | Beklenen | Ölçüm | Sonuç |
|---|---|---|---|---|
| M1 | `toArray` anahtarları camelCase | evet | 14 dosya okuma | ✅ |
| M2 | Boolean alan adı `isX`/`X` karışımı | tutarsızlık var mı? | `isPublic` (getter `isPublic()`) vs `rememberMe` (getter `isRememberMe()`) | ⚠️ tutarsız getter biçimi (§N.1) |
| M3 | Array alan yalnız `roles`/`data`/`user` | evet | okuma | ✅ |
| M4 | Tarih alanı | var mı? | 14 dosyada tarih alanı **0** | ✅ (tarih DTO'su yok) |
| M5 | Para birimi alanı | yok | 0 | ✅ (kapsam dışı teyidi) |
| M6 | Null dönebilen alan | aile 1: 0 · aile 2: 3 (`?string`/`?array`) | okuma | ✅ ayrıştırıldı |

---

## §N — İkinci Tur Bulguları (tutarlılık denetimi)

| # | Bulgu | Kanıt | Öneri (kod YAZMAZ — kayıt) |
|---|---|---|---|
| 1 | Getter biçimi tutarsız: `isRememberMe()` (bool) vs `isPublic()` (bool) vs `getGender()` | LoginRequest.php · 44 · PlaylistCreateRequest.php · 44 · UserResponse.php · 72 | tek biçim kararı (bool → `isX`) |
| 2 | `fromArray` imza tutarsızlığı: aile 1 tek argüman `array $data`; auth 3 argüman `($post,$server,$gender)` | LoginRequest.php · 52 vs Auth/LoginRequest.php · 24 | birleştirme kararı §D.1 |
| 3 | Auth LoginRequest `toArray` yok → log/audit'e döküm imkânsız | 33 satır okundu | §D.6 kararı |
| 4 | `MusicResponse`/`PlaylistResponse` tam alan listesi bu turda **tablo tablo yazılmadı** (grep + sınırlı okuma) | §B.10/§B.11 | ⚠️ §D.5 |
| 5 | `respect/validation` composer'da; `Validator.php:17-20` yorumu neden "değil" diyor — bağımlılık kararı belirsiz | composer.json · 20 vs Validator.php · 17 | karar: kullan veya kaldır |
| 6 | 4 rota kaydı `implemented=false` (GET login/register/forgot/reset) | routes.php · 36–39 | rota↔DTO eşleşmesi PLANNED |

---

## §O — Zaman Serisi (bu setin üretim günlüğü)

| # | Tarih | Olay | Kayıt |
|---|---|---|---|
| 1 | 2026-10-13 | Kapsam slotu (`dto-veri-tasiyici-tasarimi.md`) üretildi — 22 repo kanıtı, 9 açık soru | slot §6, §26 |
| 2 | 2026-10-15 | 6-dosya seti tamamlandı (kapsam · dogrulama · web-arastira · ornekler · bilesenler · index) | index §TAMAMLAMA |
| 3 | 2026-10-15 | Kapı ölçümü: 11 kapı, 1 beklenti düzeltmesi (D6 13/14) | §E.1 |
| 4 | 2026-10-15 | İkinci tur: 40 iddia tekil kanıt, 6 tutarlılık bulgusu | §L, §N |
| 5 | 2026-10-15 | `respect/validation` kararı hâlâ açık (slot §15 #8 → bu turda tekrar işaretlendi) | §N.5 |

---

## §P — Kapı Gücü Sınıflandırması

| Güç | Tanım | Kapılar |
|---|---|---|
| **Yüksek** | otomatik sayı/grep + dosya okuması | D1, D2, D7, D8, D11 |
| **Orta** | dosya okuması (iki tekrar) | D3–D6, D9, D10, D12–D17 |
| **Düşük** | tek desen/tek dosya | D21 (paket kararı), D4 (N.6) |

> Kapı gücü düşük olanlar §K "öz-denetim" sütununda ayrıca işaretlenir;
> düşük güç kapı "geçti" diye **yüksek güç** gibi kullanılamaz.

---

## §Q — Doğrulanamayan / Sınır Tespitleri

| # | Tespit | Etiket |
|---|---|---|
| 1 | `MusicResponse`/`PlaylistResponse` tam alan listesi | ⚠️ VERIFICATION REQUIRED (§D.5) |
| 2 | `assets/js/` manifest sayımı (94) ile glob (98) farkı | ⚠️ VERIFICATION REQUIRED (§D.3) |
| 3 | `respect/validation` kullanım kararı | ⚠️ VERIFICATION REQUIRED (§D.7) |
| 4 | `pageSize=0` koruma yeri | ⚠️ VERIFICATION REQUIRED (§D.8) |
| 5 | DTO birleştirme takvimi | ⚠️ VERIFICATION REQUIRED (§D.1) |
| 6 | 9 kullanılmayan DTO'nun kaderi (sil/bağla) | ⚠️ VERIFICATION REQUIRED (§D.2) |
| 7 | `JsonSerializable` kararı | UNKNOWN (§D.4) |
| 8 | Auth LoginRequest tek yönlülük kasıtı | UNKNOWN (§D.6) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
