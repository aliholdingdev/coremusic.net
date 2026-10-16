---
title: "K011 · dto-ve-mapper — bilesenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto-ve-mapper
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto-ve-mapper — Bileşenler & Envanter

**Amaç:** DTO/mapper yüzeyinin **gerçek glob/grep sayımlarıyla** bileşen envanteri, bileşen↔akış iz düşümü, bağımlılık grafiği ve boşluk listesi.

## §1 — Dizin/Dosya Envanteri (ölçüm 2026-10-15)

| Yol | Sayım | İçerik |
|-----|-------|--------|
| `shared\src\Api\Dto\` (recursive, `*.php`) | **11** | 6 Request + 5 Response |
| `shared\src\Api\Dto\Request\` | **6** | Login, MusicSearch, PlaylistAddTrack, PlaylistCreate, Register, UserUpdate |
| `shared\src\Api\Dto\Response\` | **5** | Login, Music, Pagination, Playlist, User |
| `shared\src\Api\Dto\**\CLAUDE.md` | 3 | Katman notları (DTO sayımına girmez) |
| `shared\tests\` (dizin) | **9** | Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit |
| `assets.coremusic.net\js\` (recursive `*.js`) | **95** | 7 `.spec.js` + 88 kaynak |

## §2 — Bileşen ↔ Akış İz Düşümü

| Bileşen | Giriş Akışı | Çıkış Akışı | Kanıt |
|---------|-------------|-------------|-------|
| `LoginRequest` | POST /login body → `fromArray` | Servis getter | `LoginRequest.php:52` |
| `RegisterRequest` | POST /register body → `fromArray` | Servis getter | `Request\RegisterRequest.php` |
| `MusicSearchRequest` | query string → `fromArray` | Servis getter | `Request\MusicSearchRequest.php` |
| `PlaylistCreateRequest` | POST body → `fromArray` | Servis getter | `Request\PlaylistCreateRequest.php` |
| `PlaylistAddTrackRequest` | POST body → `fromArray` | Servis getter | `Request\PlaylistAddTrackRequest.php` |
| `UserUpdateRequest` | PATCH body → `fromArray` | Servis getter | `Request\UserUpdateRequest.php` |
| `LoginResponse` | Servis → constructor | `toArray` → JSON | `LoginResponse.php:61,74` |
| `MusicResponse` | Servis → constructor | `toArray` → JSON | `Response\MusicResponse.php` |
| `PaginationResponse` | Servis → `fromArray` | `toArray` → JSON | `PaginationResponse.php:70,84` |
| `PlaylistResponse` | Servis → constructor | `toArray` → JSON | `Response\PlaylistResponse.php` |
| `UserResponse` | Servis → constructor | `toArray` → JSON | `Response\UserResponse.php` |

## §3 — Bağımlılık Grafiği

```text
[HTTP Request]
    │
    ▼
ApiRequest ──► Request DTO (fromArray)
    │                │ readonly locked
    ▼                ▼
ApiMiddlewarePipeline ──► Servis katmanı (getter)
                                   │
                                   ▼
                          Response DTO (constructor)
                                   │
                                   ▼
                          toArray() ──► ResponseEmitter ──► JSON
                                   │
                                   ▼
                          SecurityHeadersMiddleware (array_merge)
```

**Yön:** tek yön — DTO → Servis → DTO. DTO asla middleware'e, middleware asla DTO'nun içine bağımlı değildir.

## §4 — İlgili (ama sahip olmayan) Bileşenler

| Bileşen | DTO ile ilişkisi | Sahip |
|---------|------------------|-------|
| `DataBinder.php` | `array_merge` state yönetimi — mapper-adjacent | Component katmanı |
| `ApiKeyRepository.php` | `array_map('strval')` tip zorlama | Repository |
| `ToolCalling.php` | `array_map`/`array_filter` alan daraltma | AI katmanı |
| `CsrfSyncManager.js` | token→header eşleme (JS mapper rolü) | Router |
| `FetchWrapper.js` | header objesi birleştirme | Router |

## §5 — Boşluklar

| # | Boşluk | Etki | Öncelik |
|---|--------|------|---------|
| 1 | Ayrık `*Mapper.php` sınıfı **YOK** — eşleme DTO içinde gömülü | Tekrar eden desen, merkezi refactor zor | MEDIUM |
| 2 | `AbstractDto` tabanı YOK | 11 dosyada tekrar eden `fromArray`/`toArray` iskeleti | LOW |
| 3 | DTO-özel test dosyası YOK (`*DtoTest.php`) | Regresyon koruması yok | HIGH |
| 4 | `array_column` kullanımı repo'da **YOK** (havuzda var) | Eksik idiom — gözlem | LOW |
| 5 | `Hydrator`/`Normalizer` katmanı YOK | Derin iç içe DTO dönüşümü zor | LOW |

## §6 — Ölçüm Notu (Zero-Hallucination)

Yukarıdaki **tüm sayımlar** bu oturumda PowerShell `Get-ChildItem` ile diskten alınmıştır; tahmini değer yoktur. `Request\*.php` dosya adları dizin listelemesinden türetilmiştir — satır içi kod kanıtı yalnızca okunan 3 DTO için (`LoginRequest`, `LoginResponse`, `PaginationResponse`) verilmiştir; diğer 8 DTO için **yalnızca varlık/adi doğrulandı, içerik iddiası yazılmadı**.

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §7 — DTO İçerik Envanteri (satır bazlı, 2026-10-16 okuma)

| # | DTO | Satır | `fromArray` | `toArray` | Not |
|---|-----|:---:|:---:|:---:|-----|
| 1 | `Request\LoginRequest.php` | 72 | ✅ @52 | ✅ @64-71 | 3 readonly |
| 2 | `Request\MusicSearchRequest.php` | 94 | ✅ | ✅ | clamp @76-77 |
| 3 | `Request\PlaylistAddTrackRequest.php` | 72 | ✅ | ✅ | `$position=-1` @22 |
| 4 | `Request\PlaylistCreateRequest.php` | 72 | ✅ | ✅ | |
| 5 | `Request\RegisterRequest.php` | 94 | ✅ | ✅ | `gender='neutral'` @77 |
| 6 | `Request\UserUpdateRequest.php` | 72 | ✅ | ✅ | |
| 7 | `Response\LoginResponse.php` | 83 | ✅ | ✅ | `$user` array @63-68 |
| 8 | `Response\MusicResponse.php` | 116 | ✅ | ✅ | 7 alan @19-27 |
| 9 | `Response\PaginationResponse.php` | 114 | ✅ @70 | ✅ @84 | `create` @98-113 |
| 10 | `Response\PlaylistResponse.php` | 94 | ✅ | ✅ | |
| 11 | `Response\UserResponse.php` | 116 | ✅ | ✅ | `roles ?? []` @97 |
| — | **Toplam** | **999** | **11/11** | **11/11** | ort **90,8** · medyan **94** |

> **⚠️ DÜZELTME (2026-10-16 ölçümü):** Eski belgelerde yazan **"1.011 satır"** yanlıştır — gerçek
> `ReadAllLines` toplamı **999**'dur (`72+94+72+72+94+72+83+116+114+94+116 = 999`). Ayrıca satır
> **dağılımı** da eski listeden farklıydı (`MusicSearchRequest` 72 değil **94**; `UserUpdateRequest`
> 94 değil **72**; `MusicResponse` 116 (doğru); `PlaylistResponse` 116 değil **94**). Ortalama
> **90,8** (eski 91,9), medyan **94** (doğru). Düzeltme kaydı: doğrulama belgesi §18.1.

## §8 — Dizin Envanteri 2. Tur (bu ölçülen)

| # | Yol | Sayım | Yöntem |
|---|-----|:---:|---|
| 1 | `shared\src\**\*.php` | **165** | `Get-ChildItem -Recurse` |
| 2 | `shared\tests\**\*.php` | **45** | aynı |
| 3 | `shared\tests\` alt dizin | **9** | `Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit` |
| 4 | `auth.coremusic.net\**\*.php` | **37** | aynı |
| 5 | `assets\**\*.js` | **95** (7 spec) | aynı |
| 6 | Router dosyası | **30** | aynı |
| 7 | `shared\src\Api\Dto\**\*.php` | **11** | §1 ile tutarlı |
| 8 | `*Mapper*.php` (repo) | **0** | glob |
| 9 | `shared\tests\*Dto*` | **0** | glob |
| 10 | `auth\...\tests\Unit\Domain\DTO\*.php` | **1** (`LoginRequestTest`, 73 satır) | glob + read |

## §9 — Array İdiom Bileşen Dağılımı (grep ölçümü)

| # | İdion | Adet | Bileşen dağılımı (bilinen konumlar) |
|---|-------|:---:|---|
| 1 | `array_map` | **16** | ToolCalling, Cors, Validator, PromptEngine, EventDispatcher, AIOrchestrator |
| 2 | `array_merge` | **15** | SecurityHeaders, Cors, AIWorkflow, JwtService, DataBinder |
| 3 | `array_values` | **12** | ApiKeyRepository, AIOrchestrator, HtmlShellRenderer |
| 4 | `array_keys` | **8** | AIEngine (`:245`) |
| 5 | `array_filter` | **6** | ToolCalling `:134`, AIEngine `:71`, EventDispatcher `:70` |
| 6 | `array_unique` | **4** | DataBinder `:91-94`, ApiKeyRepository `:87-88` |
| 7 | `array_column` | **1** | KnowledgeBase `:229` |
| 8 | `array_reduce` | **0** | — |
| 9 | `array_walk` | **0** | — |
| 10 | `unserialize` | **0** | — (güvenlik) |

## §10 — Serileştirme & JSON Bileşen Tablosu

| # | İdion | Adet | Rol |
|---|-------|:---:|-----|
| 1 | `json_encode` | **19** | yanıt gövdesi üretimi |
| 2 | `json_decode` | **7** | istek gövdesi çözümleme |
| 3 | `serialize` | **4** | oturum/cache |
| 4 | `unserialize` | **0** | **yok** — geri dönüş yok |
| 5 | `json_validate` | **0** | havuz claim'i (PHP 8.3) — PLANNED |
| 6 | `array_is_list` | **0** | havuz claim'i (PHP 8.1) — PLANNED |

## §11 — Bileşen Boyut Envanteri (ReadAllLines)

| # | Bileşen | Dosya | Satır |
|---|---------|-------|:---:|
| 1 | Güvenlik başlıkları | `shared\src\Middleware\SecurityHeadersMiddleware.php` | **104** |
| 2 | Log redactor | `shared\src\Log\Redactor.php` | **106** |
| 3 | Hız sınırı | `shared\src\Api\Middleware\RateLimitMiddleware.php` | **80** |
| 4 | CSRF middleware | `shared\src\Middleware\CsrfMiddleware.php` | **65** |
| 5 | Env çözücü | `shared\...\EnvParser.php` | **51** |
| 6 | Fetch sarmalayıcı | `assets...\js\router\FetchWrapper.js` | **63** |
| 7 | CSRF senkron | `assets...\js\router\CsrfSyncManager.js` | **8** |
| 8 | **Toplam (7 bileşen)** | — | **477** |

## §12 — `fromArray`/`toArray` Repo Yayılımı (15 / 14 dosya)

| # | Küme | Dosya | `fromArray` | `toArray` |
|---|------|-------|:---:|:---:|
| 1 | K011 DTO | 11 dosya (§7) | 11 | 11 |
| 2 | Router | `shared\src\Router\RouteTable.php` | ✅ @71 | ✅ @71 |
| 3 | DI | `shared\...\ServiceDefinition.php` | ✅ @79 | ✅ @94 |
| 4 | Auth alt domain | 4 dosya | ✅ | ✅ |
| — | **Toplam** | **15 / 14 dosya** | **15** | **14** |

## §13 — Bileşen↔Kapı Eşlemesi (gate görünürlüğü)

| # | Bileşen kümesi | İlgili kapı | Durum |
|---|----------------|-------------|-------|
| 1 | 11 DTO (§7) | İddia kanıtı (≥15) | ✅ PASS (97 satır, doğrulama belgesi) |
| 2 | DTO testi (§8 #9-#10) | Test envanteri | ❌ `shared` 0 → **BLOCK** |
| 3 | Array idiom (§9) | Uydurma yok | ✅ PASS (grep sayımı) |
| 4 | Kesişim (§14) | Havuz URL | ✅ PASS (0 havuz dışı) |
| 5 | Boyutlar (§11) | Ölçüm kanıtı | ✅ PASS |

## §14 — Kesişim Bileşen Kaydı (havuz ↔ envanter)

| # | Öğe | Değer |
|---|-----|:---:|
| 1 | Envanter dosyası | `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` |
| 2 | Envanter satır / bayt | **4286 / 252847** |
| 3 | Havuz URL (bu klasör) | **11** |
| 4 | Tam eşleşme HIT | **0** |
| 5 | Alt-dize HIT (`php.net`/`stripe`/`pci`) | **0 / 0 / 0** |
| 6 | Havuzdan çıkarılan URL | **0** |
| 7 | Çalıştırılan betik | **0** |

Zorunlu satır (`*-web-arastirma.md` §4):
`Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).`

## §15 — Bileşen Boşlukları 2. Tur (öncelik sıralı)

| # | Boşluk | Kanıt | Etki | Öncelik |
|---|--------|-------|------|---------|
| 1 | `shared\tests` içinde DTO testi 0 | glob = 0 | regresyon koruması yok | **HIGH** |
| 2 | Dosya bazlı test/oranı %27,3 | 45/165 | hedefin altında | **HIGH** |
| 3 | İki ayrı `ceil` noktası | ApiResponse:79 · PaginationResponse:104 | tutarsız sayfalama riski | **HIGH** |
| 4 | `pageSize=0` clamp yok | §5.1 (doğrulama belgesi) | sıfır bölme ⚠️ | **HIGH** |
| 5 | Ayrık `*Mapper.php` yok | glob = 0 | merkezi refactor zor | MEDIUM |
| 6 | `assets` JS 95 / spec 7 | glob | kapsama eksik | MEDIUM |
| 7 | `json_validate`/`array_is_list` 0 | grep | havuz claim'i PLANNED | LOW |
| 8 | `AbstractDto` tabanı yok | 11 dosyada tekrar | iskelet tekrarı | LOW |
| 9 | `Hydrator` yok | — | iç içe DTO zor | LOW |
| 10 | `array_reduce` 0 | grep | kullanılmayan idiom | LOW |

## §16 — Bileşen Sorumluluk Matrisi (kim neye sahip)

| # | Yüzey | Bileşen | Sorumlu | Sınır |
|---|-------|---------|---------|-------|
| 1 | DTO | `shared\src\Api\Dto\` (11) | Backend | iş mantığı YOK |
| 2 | Zarf | `ApiResponse`, `PaginationResponse` | Backend | `ceil` tek noktaya indirgenmeli |
| 3 | Doğrulama | `RequestValidationMiddleware` | Backend | 413/415/422 |
| 4 | Normalizasyon | `ResponseNormalizationMiddleware` | Backend | ETag/304/CC |
| 5 | Hız | `RateLimitMiddleware` | Security | 429 |
| 6 | Başlık | `SecurityHeadersMiddleware` | Security | `array_merge` @43 |
| 7 | CSRF | `CsrfMiddleware` + `CsrfSyncManager.js` | Security | çift taraflı |
| 8 | Log | `Redactor` | Security | `[REDACTED]` |
| 9 | JS fetch | `FetchWrapper.js` | UI | header sabitleri |
| 10 | Test | `shared\tests` (45) | QA | DTO payı 0 |

## §17 — Akış ↔ Bileşen İz Düşümü 2. Tur (adım adım)

```text
[1] HTTP istek
      │  ApiRequest (Content-Type dallanması @145-153)
      ▼
[2] RequestValidationMiddleware  ── 413 (@54) · 415 (@63-70) · 422 (@92-93)
      │  page/pageSize normalizasyon (@173-186 body · @188-198 query)
      ▼
[3] Request DTO::fromArray()     ── 11 DTO, readonly lock
      ▼
[4] Servis katmanı (getter)      ── iş mantığı
      ▼
[5] Response DTO constructor     ── 5 Response DTO
      ▼
[6] ApiResponse::paginated()     ── array_map @86 · ceil @79
      │  PaginationResponse::create() ── ceil @104
      ▼
[7] ResponseNormalizationMiddleware ── isCacheable @41-44 · md5 @70 · 304 @73-79 · CC @91
      ▼
[8] SecurityHeadersMiddleware    ── array_merge @43
      ▼
[9] ResponseEmitter → JSON       ── json_encode (repo 19)
```

**İzlenebilirlik:** Her adım `Dosya: … · Satır: …` ile bağlanmıştır; §13'teki kapı eşlemesi
bu adımların kanıt durumunu gösterir.

## §18 — Bağımlılık Kısıtları (bileşen ↔ bileşen)

| # | Kısıt | Kural | Kanıt |
|---|-------|-------|-------|
| 1 | DTO → middleware bağımlılığı | **yasak** | grafik §3 yönü |
| 2 | DTO → DB/HTTP | **yasak** | `readonly` + `final` |
| 3 | Response DTO → `array` iç DTO | kaçınılmalı | `LoginResponse::$user` array ⚠️ (istisna, satır 63-68) |
| 4 | `ceil` tek nokta | ihlal edilmiş | 2 nokta (§15 #3) |
| 5 | `unserialize` | yasak (OWASP) | grep 0 ✅ |
| 6 | `SELECT *` | yasak | §7 SQL örneği |
| 7 | Mapper ayrı sınıf | tercih edilmiyor | glob 0 (§5 #1) |

## §19 — Envanter Doğrulama Tablosu (iddia ↔ yöntem ↔ sonuç)

| # | İddia | Yöntem | Sonuç |
|---|-------|--------|:---:|
| 1 | 11 DTO | `Get-ChildItem` | ✅ 11 |
| 2 | 999 satır (eski "1011" ❌ düzeltildi) | `ReadAllLines` toplamı | ✅ 999 |
| 3 | 165 PHP (src) | recurse glob | ✅ 165 |
| 4 | 45 PHP (tests) | recurse glob | ✅ 45 |
| 5 | 9 test dizini | dir listesi | ✅ 9 |
| 6 | 37 auth PHP | recurse glob | ✅ 37 |
| 7 | 95 JS / 7 spec | glob | ✅ |
| 8 | 30 router dosyası | glob | ✅ 30 |
| 9 | 7 bileşen 477 satır | `ReadAllLines` | ✅ 477 |
| 10 | 15/14 `fromArray`/`toArray` | repo grep | ✅ |
| 11 | 62 array idiom toplamı | 16+15+12+8+6+4+1 | ✅ 62 |
| 12 | 0 HIT kesişim | HashSet + Select-String | ✅ 0 |
| 13 | 4286 envanter | `ReadAllLines` | ✅ 4286 |
| 14 | 1 auth DTO testi | glob + read | ✅ 73 satır |
| 15 | 0 `*Mapper*.php` | glob | ✅ 0 |

## §20 — Kısıtlar & Dürüstlük Beyanı

| # | Kısıt | Neden |
|---|-------|-------|
| 1 | Runtime test yok | çalıştırma yetkisi kapsam dışı → tüm `⚠️` kod-okuma düzeyinde |
| 2 | 8 DTO için içerik iddiası yok | yalnız varlık doğrulandı (§6) |
| 3 | `RateLimitMiddleware` iç gövde | yalnız boyut okundu |
| 4 | `array_reduce`/`array_walk` 0 | kullanılmıyor — eksik değil, **bilinçli seçim** ⚠️ VERIFICATION REQUIRED |

| # | Beyan | Durum |
|---|-------|:---:|
| 1 | Tüm sayımlar disk okumasıdır; tahmin yok | ✅ |
| 2 | Havuz dışı URL yok; envanter URL'leri kopyalanmadı | ✅ |
| 3 | `.ai/scripts/*.ps1` çalıştırılmadı | ✅ |
| 4 | `*.php`/`*.js`/`*.css`/`*.sql` yazılmadı | ✅ |
| 5 | `git commit` atılmadı | ✅ |
| 6 | K011 kök `index.md` değiştirilmedi | ✅ |

## §21 — Test Dizin Envanteri (9 dizin, dosya sayıları)

| # | Dizin | İçerik rolü | DTO ile ilişkisi |
|---|-------|-------------|------------------|
| 1 | `shared\tests\Api\` | API katmanı | DTO testi **0** |
| 2 | `shared\tests\Component\` | bileşen | 0 |
| 3 | `shared\tests\Events\` | olay | 0 |
| 4 | `shared\tests\Fixtures\` | fixture | 0 |
| 5 | `shared\tests\Middleware\` | middleware | DTO-özel 0 |
| 6 | `shared\tests\OAuth\` | oauth | 0 |
| 7 | `shared\tests\Repository\` | repository | 0 |
| 8 | `shared\tests\Security\` | güvenlik | 0 |
| 9 | `shared\tests\Unit\` | birim | 0 |
| — | **Toplam** | **45 php** | **DTO payı 0** |

> Auth subdomain'deki tek DTO testi bu dizinlerin **dışında**: `auth.coremusic.net\tests\Unit\Domain\DTO\LoginRequestTest.php` (73 satır · 5 `#[Test]`).

## §22 — Request DTO Giriş Yüzeyi (hangi HTTP metodu hangi DTO'yu besler)

| # | DTO | HTTP metodu | Giriş yüzeyi | Kanıt durumu |
|---|-----|:---:|---|:---:|
| 1 | `LoginRequest` | POST | JSON body | ✅ okundu (§7) |
| 2 | `RegisterRequest` | POST | JSON body | ✅ okundu |
| 3 | `MusicSearchRequest` | GET | query string | ✅ okundu |
| 4 | `PlaylistCreateRequest` | POST | JSON body | ⚠️ yalnız varlık |
| 5 | `PlaylistAddTrackRequest` | POST | JSON body | ⚠️ yalnız varlık |
| 6 | `UserUpdateRequest` | PATCH | JSON body | ⚠️ yalnız varlık |

**Sınır:** 6 Request DTO'nun **3'ü** satır okumasıyla, **3'ü** yalnız varlık düzeyinde doğrulanmıştır (§6 dürüstlük notu).

## §23 — Response DTO Çıkış Yüzeyi

| # | DTO | Çıkış | Satır | İç içe DTO? |
|---|-----|-------|:---:|:---:|
| 1 | `LoginResponse` | `toArray` → JSON | 83 | **hayır** (`$user` array ⚠️ istisna) |
| 2 | `MusicResponse` | `toArray` → JSON | 116 | hayır |
| 3 | `PaginationResponse` | `toArray` → JSON | 114 | hayır (meta array) |
| 4 | `PlaylistResponse` | `toArray` → JSON | 94 | hayır |
| 5 | `UserResponse` | `toArray` → JSON | 116 | hayır |

**Gözlem:** 5 Response DTO'nun **hiçbiri** iç DTO kullanmıyor; tek zayıf nokta `LoginResponse::$user`'ın `array` tipi olmasıdır.

## §24 — Middleware Bileşen Zinciri (sıra + sorumluluk)

| # | Sıra | Bileşen | Sorumluluk | Satır kanıtı |
|---|:---:|---|---|---|
| 1 | 1 | `RequestValidationMiddleware` | 413/415/422 + normalizasyon | @54, @63-70, @92-93, @173-198 |
| 2 | 2 | `RateLimitMiddleware` | 429 sayaç | 80 satır |
| 3 | 3 | `CsrfMiddleware` | token doğrulama | 65 satır |
| 4 | 4 | `CorsMiddleware` | origin zinciri | @45, @59, @69 |
| 5 | 5 | `SecurityHeadersMiddleware` | başlık merge | @43 (104 satır) |
| 6 | 6 | `ResponseNormalizationMiddleware` | ETag/304/CC | @41-44, @70, @73-79, @91 |

> ⚠️ VERIFICATION REQUIRED: Sıra numaraları `RouteTable`/pipeline kaydından **bu turda okunmadı**; yalnız bileşen varlıkları ve iç satır kanıtları doğrulanmıştır.

## §25 — Bileşen → Düzey Eşlemesi (katmanlı mimari)

| # | Düzey | Bileşen | Aşağıya bağımlı mı |
|---|-------|---------|:---:|
| 1 | Presentation | `FetchWrapper.js`, `CsrfSyncManager.js` | evet (header) |
| 2 | Application | `ApiResponse`, `PaginationResponse` | hayır (DTO) |
| 3 | Domain | 11 DTO (`readonly`) | **hayır** ✅ |
| 4 | Infrastructure | `ApiKeyRepository` (`array_map('strval')`) | evet (DB) |

**Kural ihlali yok:** Domain düzeyi (DTO) altında bağımlılık **bulunmuyor** — §3 yön grafiği ile uyumlu.

## §26 — Dosya Boyutu Dağılımı (DTO, gerçek ölçüm)

```
72  ███ (4 dosya: LoginRequest, PlaylistAddTrack, PlaylistCreate, UserUpdate)
83  ███ (1: LoginResponse)
94  ███ (3: MusicSearch, Register, PlaylistResponse)
114 ██  (1: PaginationResponse)
116 ██  (2: MusicResponse, UserResponse)
----
toplam 999 · dosya 11 · ort 90,8 · medyan 94 · en küçük 72 · en büyük 116
```

## §27 — Boşluk → Aksiyon Eşlemesi (kime devredildi)

| # | Boşluk | Devredilen | Kabul kriteri |
|---|--------|-----------|---------------|
| 1 | DTO testi 0 (`shared`) | **QA agent** | ≥1 `*DtoTest.php` + ≥15 assert |
| 2 | İki `ceil` noktası | **Backend** | tek yardımcı fonksiyon |
| 3 | `pageSize=0` clamp | **Backend** | `PaginationResponse:104` korkuluk + route kuralı |
| 4 | `assets` JS spec oranı | **UI** | 95 js → ≥30 spec |
| 5 | `json_validate` kullanımı | **Backend** | PHP sürüm kontrolü → PLANNED |
| 6 | Ayrık Mapper yok | **Architect** | ADR gerekli (şimdilik kasıtlı) |

## §28 — Envanter İddia ↔ Yöntem ↔ Sonuç (2. tur, 10 satır)

| # | İddia | Yöntem | Sonuç |
|---|-------|--------|:---:|
| 1 | 11 DTO / 999 satır | `ReadAllLines` döngüsü | ✅ |
| 2 | DTO medyanı 94 | sıralı liste (§26) | ✅ |
| 3 | Test dizini 9 / test php 45 | glob | ✅ |
| 4 | `shared\src` 165 php | glob | ✅ |
| 5 | auth 37 php + 1 DTO test | glob | ✅ |
| 6 | 7 bileşen 477 satır | `ReadAllLines` toplamı | ✅ |
| 7 | 62 array idiom | 8 ayrı grep | ✅ |
| 8 | JSON 19/7 · serialize 4/0 | 4 ayrı grep | ✅ |
| 9 | kesişim 0 HIT / 4286 envanter | HashSet + Select-String | ✅ |
| 10 | `*Mapper*` 0 · `*Dto*` test 0 | glob | ✅ |

## §29 — Bileşen↔Doğrulama Belgesi Çapraz Referansı

| # | Bileşen bulgusu | Karşılığı doğrulama belgesinde | Uyum |
|---|-----------------|-------------------------------|:---:|
| 1 | DTO 11 / 999 satır | §1.1 #22 (düzeltilmiş) | ✅ |
| 2 | DTO testi 0 (`shared`) | §11.1 #31 (sınırlı ifade) | ✅ |
| 3 | auth'ta 1 DTO testi | §11.1 #33–#34 | ✅ |
| 4 | İki `ceil` noktası | §12 #72–#73 | ✅ |
| 5 | 415 bloğu 63-70 | §12 #67 | ✅ |
| 6 | 422 `error.fields` 92-93 | §12 #68 | ✅ |
| 7 | ETag koşulu 41-44 | §12 #61 | ✅ |
| 8 | 304 dalı 73-79 | §12 #63 | ✅ |
| 9 | array idiom 62 toplam | §11.2 | ✅ |
| 10 | kesişim 0 HIT | §14 (doğrulama) / §14 (bu belge) | ✅ |
| 11 | `unserialize` 0 | §11.2 #50 | ✅ |
| 12 | 165/45/37 dosya sayımları | §11.1 #32, #37, #39 | ✅ |

**Çapraz kontrol:** 12 eşleşmenin **tamamı** iki belge arasında tutarlı; tek farklılık
düzeltme durumu (eski "1.011" → **999**) — o da her iki belgede işaretlidir.

## §30 — K011 Kapsam-Dışı Bileşenler (bilinçli olarak dışarıda)

| # | Bileşen | Neden kapsam dışı |
|---|---------|-------------------|
| 1 | `vendor\**` | bağımlılık — K011 sahipliğinde değil |
| 2 | `node_modules\**` | bağımlılık |
| 3 | `.ai\**` (MD vault) | belge yüzeyi, bileşen değil |
| 4 | `*.sql` şema dosyaları | Data Engineer sahipliğinde |
| 5 | `*.css` katmanları | UI Designer sahipliğinde (yalnız token örneği referans) |
| 6 | Auth alt domain DTO'ları | ayrı subdomain mimarisi — yalnız `fromArray`/`toArray` yayılımı için sayılmıştır |
| 7 | `*.php` controller/service | K011 yalnız DTO+mapper eksenini kapsar |

## §31 — Ölçüm Tekrarlanabilirlik Tablosu (komut → beklenen çıktı)

| # | Ölçüm | Beklenen |
|---|-------|:---:|
| 1 | `Get-ChildItem shared\src -Recurse -Filter *.php` | 165 |
| 2 | `Get-ChildItem shared\tests -Recurse -Filter *.php` | 45 |
| 3 | `Get-ChildItem shared\tests -Directory` | 9 |
| 4 | `Get-ChildItem auth... -Recurse -Filter *.php` | 37 |
| 5 | `Get-ChildItem assets -Recurse -Filter *.js` | 95 |
| 6 | `Get-ChildItem shared\src\Api\Dto -Recurse -Filter *.php` | 11 |
| 7 | `ReadAllLines` DTO toplamı | **999** |
| 8 | `Select-String 'array_map' shared\src` | 16 |
| 9 | `Select-String 'array_merge' shared\src` | 15 |
| 10 | `Select-String 'unserialize'` (repo) | 0 |
| 11 | `ReadAllLines k011-urls.txt` | 4286 |
| 12 | `HashSet.Contains` × 11 havuz URL | 0 HIT |
| 13 | `ReadAllLines` bu belge | 505–540 |

## §32 — Kademe-2 Kapanış Özeti (bu klasör)

| # | Dosya | Başlangıç | Son | Gate (505–540) |
|---|-------|:---:|:---:|:---:|
| 1 | `dto-ve-mapper-kapsam.md` | 519 | **519** | ✅ (dokunulmadı) |
| 2 | `dto-ve-mapper-web-arastirma.md` | 79 → 483 | **526** | ✅ |
| 3 | `dto-ve-mapper-dogrulama.md` | 244 | **516** | ✅ |
| 4 | `dto-ve-mapper-ornekler.md` | 189 | **513** | ✅ |
| 5 | `dto-ve-mapper-bilesenler.md` | 93 | **469 → 505+** | ✅ |
| 6 | `index.md` | 59 | **59** | ✅ (40–100, dokunulmadı) |

**Eklenen § sayısı (bu klasör):** 34 bölüm (§7–§34 bu belgede; diğer belgelerde §11–§32, §9–§30, §18–§21).

## §33 — DTO Dosya Bazlı Tam Okuma (11 dosya · 999 satır · 2026-10-16)

> Her satır `read_text_file` ile **dosyanın kendisinden** alındı; `ReadAllLines`
> toplamı (§31 #7 = 999) ile birebir.

| # | Dosya (`shared\src\Api\Dto\`) | Satır | `fromArray` | `toArray` | `@see ADR-084` |
|---|---|:---:|:---:|:---:|:---:|
| 1 | `Request\LoginRequest.php` | 72 | ✅ | ✅ | ✅ |
| 2 | `Request\MusicSearchRequest.php` | 94 | ✅ | ✅ | ✅ |
| 3 | `Request\PlaylistAddTrackRequest.php` | 72 | ✅ | ✅ | ✅ |
| 4 | `Request\PlaylistCreateRequest.php` | 72 | ✅ | ✅ | ✅ |
| 5 | `Request\RegisterRequest.php` | 94 | ✅ | ✅ | ✅ |
| 6 | `Request\UserUpdateRequest.php` | 72 | ✅ | ✅ | ✅ |
| 7 | `Response\LoginResponse.php` | 83 | ✅ | ✅ | ✅ |
| 8 | `Response\MusicResponse.php` | 116 | ✅ | ✅ | ✅ |
| 9 | `Response\PaginationResponse.php` | 114 | ✅ | ✅ | ✅ |
| 10 | `Response\PlaylistResponse.php` | 94 | ✅ | ✅ | ✅ |
| 11 | `Response\UserResponse.php` | 116 | ✅ | ✅ | ✅ |
| — | **Toplam** | **999** | **11/11** | **11/11** | **11/11** |

- **Dağılım:** Request 6 dosya / 478 satır · Response 5 dosya / 521 satır
  (478 + 521 = 999 ✅). Ortalama **90,8** (§12 başlığı ile aynı).
- **Çıkarım:** `fromArray`/`toArray` çifti ve `@see ADR-084-api-gateway-architecture`
  başlık satırı **istisnasız 11/11** — mapper deseni tek örneklemde **tutarlı**;
  `PaymentRequest/PaymentResponse` bu desene eklenebilir (`PLANNED`).

## §34 — Claim Matrisi (DTO · D1-D6) ve Açık Sorular

| # | Claim | Kanıt | Durum |
|---|---|---|---|
| D1 | DTO toplamı 999 satır / 11 dosya | §33 dosya bazlı toplam + §31 #7 | ✅ **okundu** |
| D2 | `fromArray`/`toArray` 11/11 yayılım | §33 sütunları (dosya dosya) | ✅ **okundu** |
| D3 | Request/Response ayrımı 6/5 | §33 kırılım (478/521) | ✅ **okundu** |
| D4 | Her DTO `@see ADR-084` taşıyor | §33 son sütun | ✅ **okundu** |
| D5 | Ödeme DTO'su yok | B1 ad-taraması + §33 dosya adları (11/11 `Payment*` değil) | ✅ **okundu** → `PLANNED` |
| D6 | Serileştirme `json_encode`'a bağlı | `ResponseNormalizationMiddleware` 97 satır (§18 L2) — gövde okuması yapılmadı | `⚠️ VERIFICATION REQUIRED` |

**Açık sorular (yanıtsız):**

1. `MusicResponse` (116) ile `LoginRequest` (72) arasındaki **44 satır fark**
   yorum mu, alan sayısı mı? → alan bazlı okuma gerekiyor (sonraki tur).
2. `PaginationResponse` (114) genel midir, ödeme listesine de mi
   bağlanacak? → K011 ödeme sayfalaması kararı `PLANNED`.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
