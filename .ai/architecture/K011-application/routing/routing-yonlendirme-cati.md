---
node_id: K011-application/routing/routing-yonlendirme-cati
node_type: module
status: draft
parent_id: K011-application/routing
children: []
depends_on:
  - K011-application
evidence: []
validation: []
open_questions: []
group: mimari
---

# routing-yonlendirme-cati — Yönlendirme Çatısı Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K011-application · **Alt klasör:** `routing`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-021 (SPA router) · ADR-084 (BFF)
- **İmla koruması:** düğüm adları `identitiy` · `integraion` yazım bozuklukları bilinçli korunmuştur.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1–§3 Amaç, terminoloji, yerleşim | mimari | katman | Routing çatısının K011 konumu; iki çatı (API + SPA) ayrımı |
| 2 | §4–§5 Web research + standart tabanı | kanit | kaynak | PSR-15 / RFC 9457 gibi harici kaynaklar (envanter kesişimi 0) |
| 3 | §6–§8 Repo kanıtları + eşleme | kanit | kod-satır | `Dosya: … · Satır: …` izleri ve eşleme algoritması |
| 4 | §9–§12 Metod, middleware, versiyon, hata | mimari | sözleşme | 405 + `Allow`, pipeline sarmalama, `/api/v1`, problem-details |
| 5 | §13–§16 Public/protected, SPA, BFF | mimari | güvenlik-sınır | `public` bayrağı, ikinci çatı, BFF transform sırası |
| 6 | §17–§19 Güvenlik, performans, test | kanit | denetim | Red-team senaryoları, ölçüm kalemleri, test taslakları |
| 7 | §20–§24 Açık sorular, politika, faz, risk | mimari | yol-haritası | 7 açık soru · 6 faz · 5 risk kaydı |
| 8 | §25–§29 Özet, envanter, şablon, ölçüm | kanit | ölçüm | 17 rota / 7 `implemented` sayımları + yeni rota şablonu |
| 9 | Kardeş dosyalar (`-dogrulama` · `-web-arastirma` · `-ornekler` · `-bilesenler` · `index.md`) | kod/kanit | üretim | İkinci eksen: dosya grubu × kullanım ekseni (Ünite-3 seti) |

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic'in HTTP isteklerini **handler'lara yönlendiren çatıyı** tanımlar:
route tablosu biçimi, eşleme algoritması, HTTP metod uyumu, middleware sıralaması,
versiyonlama ve hata yanıtları. İki ayrı yönlendirme yüzeyi vardır ve bu dosya ikisini
de kapsar: **API yönlendirme** (`shared/src/Api/`) ve **SPA sayfa yönlendirme**
(`shared/src/PageRouter/`).

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Route tablosu | Tanım biçimi, zorunlu anahtarlar, dokümantasyon satırı | Handler içi iş mantığı (use-case dosyası) |
| 2 | Eşleme | URI normalizasyonu, öncelik, parametre çıkarma | DNS/host yönlendirme (K014-network) |
| 3 | Metod uyumu | 405 + `Allow` başlığı üretimi | CORS politikası (middleware, ayrı dosya) |
| 4 | Middleware sırası | Pipeline sarmalama yönü | Middleware'in kendi kararı (ilgili dosyalar) |
| 5 | Versiyonlama | `/api/v1` öneki ve eşleme kaydı | Sözleşme değişiklik yönetimi (K012-integraion) |
| 6 | Hata yanıtı | 404 / 405 / problem-details biçimi | Hata günlüğü (K013-audit) |
| 7 | Public/protected | `public` bayrağı ve auth dalı | Rol/izin ataması (K010 authorization) |
| 8 | SPA route | `RouteRegistry` desen eşleşmesi | Sayfa render (presentation) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Rota | route | Metod + URI örüntüsü + handler eşleşmesi | `routes.php` girdileri |
| Rota tablosu | route table | Tüm eşleşmelerin kayıtlı olduğu yapı | `RouteTable` |
| Eşleme | matching | Gelen isteğin rotaya oturtulması | `RouteTable::match()` |
| Parametre | route parameter | Örüntüdeki değişken parça | `matchesPattern()` → `extractParams()` |
| Metod izni | Allow header | 405 yanıtında geçerli metotlar | `methodNotAllowedResponse()` |
| Boru hattı | middleware pipeline | İstek öncesi/sonrası zincir | `MiddlewarePipeline::pipe()` |
| Sarmalama | wrapping | Her middleware'in bir sonrakini çağırması | `wrap()` deseni |
| Versiyon | API version | URI öneki ile belirtilen sözleşme sürümü | `/api/v1` |
| Public rota | public endpoint | Kimlik doğrulamasız erişilebilir rota | `public => true` |
| Handler | handler | Rotayı yürütecek somut sınıf | `handler` anahtarı |
| Servis | service | Handler'ın ait olduğu alt alan | `service` anahtarı |
| İzin verilen metot | allowed methods | Belirli URI için geçerli metot kümesi | `allowedMethods()` |
| Problem-details | problem details | JSON hata gövdesi standardı | RFC 9457 biçimi |
| İstek kimliği | request id | Yanıtla izlenebilir tekil numara | `X-Request-ID` |
| Sayfa rotası | SPA route | İstemci tarafı path eşleşmesi | `RouteRegistry` |
| Normalizasyon | URI normalization | `//` ve sondaki `/` temizliği | `RequestNormalizer` |
| BFF | backend for frontend | İstemci türüne özel dönüşüm katmanı | `SpaBff` / `MobileBff` |
| Yönlendirici | router | Eşlemeyi yapan bileşen | `Gateway::matchRoute()` |

## §3 — Mimari yerleşim (kademe-3 konumu)

| Katman | Yönlendirme bileşeni | Dosya |
|---|---|---|
| Presentation | SPA sayfa eşleşmesi, hash/path çözme | `shared/src/PageRouter/RouteRegistry.php` |
| Application | Servis dispatch, handler çağrısı | `shared/src/Api/Gateway.php` |
| Domain | Route sözleşmesi (veri biçimi) | `shared/src/Api/Routing/RouteTable.php` |
| Infrastructure | Route konfigürasyon dosyası | `api.coremusic.net/config/routes.php` |
| Cross-cutting | Middleware sırası, versiyon kaydı | `shared/src/Middleware/`, `shared/src/Api/Versioning/` |

**Çift çatı gerçeği:** API çatısı `Gateway` + `RouteTable` üzerinden, SPA çatısı
`RouteRegistry` üzerinden çalışır; ikisi aynı URI uzayını farklı amaçlarla değerlendirir
(§15).

## §4 — Web research (2026-10-13 doğrulaması)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PSR-15, HTTP middleware arayüzünü `process(ServerRequestInterface, RequestHandlerInterface)` ile tanımlar | https://www.php-fig.org/psr/psr-15/ | HTTP 200 | Doğrulandı — envanterde URL olarak yok |
| ASP.NET Core routing dokümanı, endpoint routing ve model bağlama sırasını belgeler | https://learn.microsoft.com/en-us/aspnet/core/fundamentals/routing | HTTP 200 | Doğrulandı — envanterde yok |
| MDN 405, `Allow` başlığının 405 yanıtında zorunlu olduğunu belgeler | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/405 | HTTP 200 | Doğrulandı — envanterde yok |
| MDN 404, kaynağın bulunamaması durumunu tanımlar ve yönlendirme hatalarından ayırır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404 | HTTP 200 | Doğrulandı — envanterde yok |
| Azure API design best practices, kaynak tabanlı URI ve durum kodu tutarlılığını önerir | https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design | HTTP 200 | Doğrulandı — envanterde yok |
| RFC 9457, `application/problem+json` hata gövdesi biçimini standartlaştırır | https://datatracker.ietf.org/doc/html/rfc9457 | HTTP 200 | Doğrulandı — envanterde yok |

**Doğrulama yöntemi:** altı URL `Invoke-WebRequest` (HEAD, gerekirse GET) ile çağrıldı →
`200`; `.ai/` genelinde URL öbeği araması → **0 isabet**.

## §5 — Standart ve spesifikasyon tabanı

| Kaynak | Ne düzenler | Bu dosyadaki kullanımı |
|---|---|---|
| PSR-15 | Middleware sözleşme ortakları | §10 |
| PSR-7 | HTTP mesaj tipleri (istek/yanıt) | §6 kanıtlar (ApiRequest benzerliği) |
| MDN 404 / 405 | Durum kodu semantiği | §9 · §12 |
| Azure API design | URI ve durum kodu tutarlılığı | §7 · §12 |
| RFC 9457 | Problem-details gövdesi | §12.2 |
| ADR-021 / ADR-083 | SPA router çerçevesi | §15 |
| ADR-084 | BFF katmanı | §16 |
| ADR-013 | Rate limit — routing sonrası katman | §18.3 |

## §6 — Repo kod kanıtları (2026-10-13 doğrulanmış satırlar)

| # | Dosya | Satır | Ne gösterir |
|---|---|---|---|
| 1 | `api.coremusic.net/config/routes.php` | 15 | `requiredRole / requiredPermission : AuthorizationMiddleware RBAC` — rota anahtarlarının dokümanı |
| 2 | `api.coremusic.net/config/routes.php` | 16 | `cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control` |
| 3 | `api.coremusic.net/config/routes.php` | 36 | `'/api/v1/auth/login' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false]` |
| 4 | `api.coremusic.net/config/routes.php` | 46 | `'public' => false,` — korumalı rota bayrağı |
| 5 | `shared/src/Api/Routing/RouteTable.php` | 96 | `'/api/v1/auth' => ['service' => 'auth', 'handler' => 'authController'],` — önek eşlemesi |
| 6 | `shared/src/Api/Routing/RouteTable.php` | 111 | `public function match(string $uri, string $method): array` — eşleme girişi |
| 7 | `shared/src/Api/Routing/RouteTable.php` | 126 | `'status' => self::STATUS_METHOD_NOT_ALLOWED,` — 405 durumu |
| 8 | `shared/src/Api/Routing/RouteTable.php` | 128 | `'allow' => $this->allowedMethods($uri),` — Allow listesi |
| 9 | `shared/src/Api/Routing/RouteTable.php` | 134 | `return ['status' => self::STATUS_METHOD_NOT_ALLOWED, 'route' => null, 'allow' => $allow];` |
| 10 | `shared/src/Api/Gateway.php` | 50 | `public function dispatch(array $request): array` — giriş noktası |
| 11 | `shared/src/Api/Gateway.php` | 109 | `private function matchRoute(ApiRequest $request): array` |
| 12 | `shared/src/Api/Gateway.php` | 125 | `private function methodNotAllowedResponse(array $allow): array` |
| 13 | `shared/src/Api/Gateway.php` | 152 | `private function invokeHandler(array $route, array $request): array` |
| 14 | `shared/src/Middleware/MiddlewarePipeline.php` | 17 | `public function pipe(IMiddleware $middleware): self` |
| 15 | `shared/src/Middleware/MiddlewarePipeline.php` | 23 | `public function run(array $request, callable $core): array` |
| 16 | `shared/src/Middleware/MiddlewarePipeline.php` | 33 | `$pipeline = $this->wrap($middleware, $pipeline);` — sarmalama yönü |
| 17 | `shared/src/PageRouter/RouteRegistry.php` | 48 | `if ($this->matchesPattern($pattern, $normalized)) {` — SPA eşleşmesi |
| 18 | `shared/src/PageRouter/RouteRegistry.php` | 72 | `private function matchesPattern(string $pattern, string $uri): bool` |
| 19 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 38 | `return match ($uri) {` — handler seviyesinde ikincil eşleme |
| 20 | `shared/src/Api/Versioning/VersionRegistry.php` | 40 | `public function matchRoute(ApiVersion $version, string $uri): ?array` |
| 21 | `shared/src/Api/Versioning/VersionRegistry.php` | 96 | `public function registerStandardV1Routes(): void` |
| 22 | `shared/src/Api/ApiRequest.php` | 38 | `public function getMethod(): string` |
| 23 | `shared/src/Api/ApiResponse.php` | 30 | `header('X-Request-ID: ' . UuidV7::generate());` |
| 24 | `shared/src/Api/ApiResponse.php` | 31 | `header('X-API-Version: ' . self::API_VERSION);` |

## §7 — Rota tablosu sözleşmesi

### §7.1 Zorunlu ve isteğe bağlı anahtarlar

| Anahtar | Tip | Zorunlu | Açıklama |
|---|---|---|---|
| `service` | string | Evet | Handler'ın ait olduğu alt alan adı |
| `handler` | string | Evet | Çalıştırılacak controller/handler |
| `public` | bool | Evet | `true` → auth atlanır; `false` → auth zorunlu |
| `implemented` | bool | Evet | `false` → rota henüz canlı değil (§7.3) |
| `requiredRole` | string | Hayır | RBAC rol şartı (dokümante, §14) |
| `requiredPermission` | string | Hayır | İzin şartı (dokümante, §14) |
| `cacheable` | bool | Hayır | Yanıt önbelleklemeye aday |
| `cacheTtl` | int | Hayır | `cacheable = true` ise saniye |

### §7.2 Doküman satırı (kod içi anayasa)

| Satır | Metin | İşlev |
|---|---|---|
| `routes.php:15` | `requiredRole / requiredPermission : AuthorizationMiddleware RBAC` | Yeni rota ekleyen için zorunlu okuma |
| `routes.php:16` | `cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control` | Cache bayrağının sahibini tanımlar |

### §7.3 `implemented` bayrağı

| Değer | Davranış | Ölçüm (2026-10-13) |
|---|---|---|
| `true` | Rota canlı, dispatch edilir | 7 kayıt |
| `false` | Rota tanımlı ama yanıtı henüz üretilmiyor | `routes.php:36` login kaydı bu sınıfta |
| Bayrak yok | Eksik tanımlama — inceleme reddi | Şart |

**Envanter notu:** `routes.php` içinde `GET` 11 · `POST` 6 · `implemented => true` 7
(2026-10-13 sayımı). Sayı, dosyanın o anki hâlini yansıtır; yeni rota eklenince değişir.

## §8 — Eşleme algoritması

| # | Adım | İşlem | Kanıt |
|---|---|---|---|
| 1 | İstek al | Metod + URI ayrıştırma | `ApiRequest.php:38` (`getMethod`) |
| 2 | Normalizasyon | Çift bölünme ve sondaki `/` temizliği | `RouteRegistry.php:48` (`$normalized`) |
| 3 | Önek eşle | `/api/v1/auth` gibi servis öneki | `RouteTable.php:96` |
| 4 | Tam eşle | Metod + desen | `RouteTable.php:111` (`match`) |
| 5 | Metod kontrolü | Eşleşme varsa metod doğrula | `RouteTable.php:126` |
| 6 | Allow üret | Geçerli metot kümesi | `RouteTable.php:128` |
| 7 | Route yoksa | 404 sınıfı yanıt | §12.1 |
| 8 | Handler çağr | Dispatch | `Gateway.php:152` (`invokeHandler`) |

### §8.1 Eşleme önceliği

| Öncelik | Kural | Gerekçe |
|---|---|---|
| 1 | Tam URI + metod | En spesifik kazanır |
| 2 | Parametreli desen | Dinamik kaynaklar |
| 3 | Önek eşleşmesi (servis) | Servis seviyesi varsayılanı |
| 4 | Yok | 404 |

### §8.2 Karmaşıklık hedefi

| Yaklaşım | Karmaşıklık | CoreMusic durumu |
|---|---|---|
| Lineer tarama | O(n) | Rota sayısı küçükken kabul edilebilir |
| Hash (metod+uri) | O(1) | Büyümede önerilir — §23 faz 3 |
| Desen önceden derleme | Kurulum maliyeti | `matchesPattern` çağrısı istek başına (`RouteRegistry.php:72`) |

## §9 — HTTP metod uyumu

| Durum | HTTP kodu | Başlık | Kanıt |
|---|---|---|---|
| Metod geçerli | 200 / 201 / 204 | — | Handler kararı |
| URI var, metod yok | 405 | `Allow` zorunlu | `RouteTable.php:126-128` |
| URI yok | 404 | — | §12.1 |
| Metod desteklenmiyor (genel) | 405 | `Allow` | MDN 405 |
| Önyönlendirme gerekiyor | 301/302/308 | `Location` | Kapsam: yalnız kaynak taşınması |

**`Allow` üretimi sözleşmesi:** `allowedMethods($uri)` çağrısı yalnız 405 anında yapılır;
yanıt gövdesi `methodNotAllowedResponse()` içinde kurgulanır (`Gateway.php:125`).

## §10 — Middleware sıralaması

### §10.1 Sarmalama yönü

| Sıra | Bileşen | Davranış | Kanıt |
|---|---|---|---|
| 1 | `pipe()` çağrısı | Zincire ekler | `MiddlewarePipeline.php:17` |
| 2 | `run()` | İsteği çeker | `MiddlewarePipeline.php:23` |
| 3 | `wrap()` | Yeni middleware en dışa sarılır | `MiddlewarePipeline.php:33` |
| 4 | `core` | Zincirin sonundaki handler | `run(array $request, callable $core)` |

**Yorum:** `wrap` deseni son eklenen middleware'in **en dışta** çalışmasını sağlar; bu,
güvenlik middleware'lerinin (auth, origin) handler'a **en yakın** değil **en uzak** durması
anlamına gelir. Sıra kaydı eklenirken bu yön açıkça yazılmalıdır.

### §10.2 Önerilen sıra (sözleşme)

| # | Katman | Amaç |
|---|---|---|
| 1 | Origin/CORS | Köken kontrolü |
| 2 | Rate limit | Kota (ADR-013) |
| 3 | Session başlatma | Kimlik zemini |
| 4 | Auth (public değilse) | Kimlik doğrulama |
| 5 | Authorization | Rol/izin |
| 6 | Validation | Gövde doğrulama |
| 7 | CSRF (state-changing) | Sahte istek |
| 8 | Handler | İş mantığı |
| 9 | Response normalization | ETag/Cache-Control |

> **⚠️ VERIFICATION REQUIRED:** Gerçek kayıt sırası (config üzerinden) bu dosyada
> **okunmadı**; §10.2 bir tasarım önerisidir, gözlemsel kayıt değildir.

## §11 — Versiyonlama

| Kural | Değer | Kanıt |
|---|---|---|
| URI öneki | `/api/v1` | `routes.php:36` |
| Kayıt yeri | `VersionRegistry` | `VersionRegistry.php:40` |
| Standart v1 rotaları | Tek çağrıda kayıt | `VersionRegistry.php:96` |
| Yanıtta sürüm | `X-API-Version` | `ApiResponse.php:31` |
| Sürüm artırma tetikleyicisi | Kırıcı sözleşme değişikliği | Azure API design |
| Sürüm kaldırma | En az bir tam sürüm ömür desteği | Azure API design (süre: UNKNOWN) |

## §12 — Hata yanıtı sözleşmesi

### §12.1 Durum kodu tablosu

| Durum | Kodu | Kullanım |
|---|---|---|
| Bulunamadı | 404 | URI eşleşmesi yok |
| Metot izinli değil | 405 | `Allow` başlığı ile |
| Kimlik yok | 401 | `public => false` ve kimlik kanıtı yok |
| Yetki yok | 403 | Rol/izin karşılanmıyor |
| Doğrulama hatası | 422 | Gövde şeması geçersiz |
| Çakışma | 409 | Kaynak durumu çakışması |
| Hız sınırı | 429 | Kota doldu (ADR-013) |
| Sunucu hatası | 500 | Ele alınmayan istisna |

### §12.2 Problem-details biçimi

| Alan | Zorunlu | Not |
|---|---|---|
| `type` | Evet | RFC 9457 — sabit URI veya `about:blank` |
| `title` | Evet | Kısa, insan okunur özet |
| `status` | Evet | HTTP durum kodu |
| `detail` | Hayır | İsteğe özgü ayrıntı (iç sızdırmaz) |
| `instance` | Hayır | İstek izi (`X-Request-ID` ile eşleştirilir) |

**Sızıntım kuralı:** `detail` içinde stack trace, SQL veya dahili dosya yolu **yazılmaz**.

### §12.3 Yanıt başlıkları

| Başlık | Değer | Kanıt |
|---|---|---|
| `X-Request-ID` | UuidV7 üretimi | `ApiResponse.php:30` |
| `X-API-Version` | Sabit sürüm | `ApiResponse.php:31` |
| `Allow` | 405 anında metot listesi | `RouteTable.php:128` |
| `Content-Type` | `application/problem+json` (hata) | RFC 9457 |

## §13 — Public / protected rotalar

| Bayrak | Anlamı | Örnek | Kanıt |
|---|---|---|---|
| `public => true` | Auth atlanır | Login | `routes.php:36` |
| `public => false` | Auth zorunlu | Korumalı uç | `routes.php:46` |
| Bayrak yok | Tanım eksik | — | İnceleme reddi |

| Kural | Değer |
|---|---|
| Varsayılan | `public` bayrağı **her rotada açıkça** yazılır |
| Güvenli varsayım | Bayrak eksikse erişim **reddedilir** |
| Genişletme | `public` bayrağını yalnız backend-architect genişletebilir |
| Denetim | Yeni rota PR'ında bayrak tablosu zorunlu |

## §14 — Yetki alanları (dokümante ↔ uygulama)

| Alan | Durum (2026-10-13) | Kanıt |
|---|---|---|
| `requiredRole` | **Tanımlı anahtar — atama yok** | `routes.php:15` yalnız doküman satırı; gerçek atama 0 |
| `requiredPermission` | **Tanımlı anahtar — atama yok** | Aynı ölçüm |
| `AuthorizationMiddleware` | Mevcut, rota verisiyle beslenmiyor | `shared/src/Api/Middleware/AuthorizationMiddleware.php` |

**Açık soru:** RBAC anahtarları dokümante edilmiş ama route kayıtlarına **hiçbir
rota için yazılmamıştır** (§20 açık soru #1). Bu, ya eksik uygulamadır ya da kararın
merkezi politika olarak taşınmasındır — hangisi olduğu **UNKNOWN**.

## §15 — SPA sayfa yönlendirme (ikinci çatı)

| Boyut | API çatısı | SPA çatısı |
|---|---|---|
| Dosya | `shared/src/Api/Gateway.php` + `RouteTable.php` | `shared/src/PageRouter/RouteRegistry.php` |
| Eşleme girişi | `matchRoute(ApiRequest)` (`:109`) | `matchesPattern($pattern, $normalized)` (`:48`) |
| Desenlayıcı | `RouteTable::match` (`:111`) | `matchesPattern` (`:72`) |
| Amaç | Sunucu tarafı handler dispatch | İstemci tarafı sayfa render |
| 405 üretimi | Evet (`:125`) | Hayır (tarayıcı metodu yok) |
| Kimlik | Session/JWT | `AuthGuard` (K010) |

**Çakışma riski:** İki çatı aynı path uzayını kullanır; biri `/settings` sayfasını,
diğeri `/api/v1` önekli uçlarını değerlendirir. Ayrım öneki ile yapılır — önek değişirse
her iki kayıt da güncellenir.

## §16 — BFF katmanı ile ilişki

| Bileşen | Rol | Kanıt |
|---|---|---|
| `Gateway::dispatch` | Yönlendirme | `Gateway.php:50` |
| `BffLayer::transform` | Yanıt dönüşümü (dispatch sonrası) | `shared/src/Api/Bff/BffLayer.php` |
| `SpaBff::transform` | SPA'ya özel biçim | `Bff/SpaBff.php:24` |
| `MobileBff::transform` | Mobil'e özel biçim | `Bff/MobileBff.php:24` |
| `ServiceRegistry::register` | Servis keşfi | `Registry/ServiceRegistry.php:26` |

**Sıra:** `route → auth → handler → BFF transform → response`. BFF, **yönlendirmeyi
değiştirmez**, yalnız yanıtı dönüştürür.

## §17 — Yönlendirme güvenlik kuralları

| # | Kural | Uygulama | Durum |
|---|---|---|---|
| 1 | Açık yönlendirme (open redirect) yasağı | Dönüş URL'si aynı kökenle sınırlı | `shared/src/Security/ReturnUrlPolicy.php` mevcut |
| 2 | Bilgi sızdırmama | Bulunamayan URI'ye "bu rota yok" yerine genel 404 | §12.1 |
| 3 | `Allow` yalnız 405'te | Başka yanıtta metot listesi sızdırılmaz | §9 |
| 4 | Rota listesi dışarı sızdırılmaz | Route tablosu dış servise üretilmez | Sınır: iç yapı |
| 5 | Parametreler prepare-bound | SQL'e rota parametresi doğrudan gitmez | K005-data |
| 6 | `%2e%2e` kod çözme sonrası normalizasyon | Parçalama öncesi ve sonrası kontrol | `RouteRegistry.php:48` `$normalized` |
| 7 | Host başlığına güvensizlik | Eşleme yalnız path üzerinde | `ApiRequest::getUri` (`:46`) |

## §18 — Performans

### §18.1 Ölçüm kalemleri

| Metrik | Hedef | Ölçüm yöntemi | Durum |
|---|---|---|---|
| Eşleme süresi (p95) | < 1 ms | Profil | UNKNOWN — ölçülmedi |
| Route tablosu belleği | Sabit | Tablo boyutu | Rota sayısı: 17 kayıt (2026-10-13) |
| Desen derleme tekrarı | İstek başına en fazla 1 | Statik analiz | `matchesPattern` istek başına çağrılıyor |
| İlk eşleşme oranı | > %80 | Log | UNKNOWN — log alanı yok |

### §18.2 İyileştirme seçenekleri

| # | İyileştirme | Maliyet | Kazanç |
|---|---|---|---|
| 1 | Desenleri başlangıçta derleme | Orta | İstek başına CPU |
| 2 | Hash haritası (metod+uri) | Orta | O(n) → O(1) |
| 3 | Önek ağacı (trie) | Yüksek | Büyük tabloda |
| 4 | Route tablosu önbelleği | Düşük | Dosya ayrıştırma |

### §18.3 Route cache ile rate limit ilişkisi

| Bileşen | Orakül | Kanıt |
|---|---|---|
| Cache arayüzü | `increment(key, step)` | `shared/src/Cache/CacheInterface.php:10` |
| Rate limit sayacı | `set` sonra `increment` | `shared/src/Security/CacheRateLimiter.php:31` ve `:33` |
| Cache anahtarı biçimi | Rotaya göre (yaklaşık) | Ayrı dosya: K011 caching |

## §19 — Test senaryoları (taslak)

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | Bilinen GET + doğru metod | Handler çağrılır |
| 2 | Bilinen URI + yanlış metod | 405 + `Allow` içeriği doğru |
| 3 | Bilinmeyen URI | 404 |
| 4 | `public => false` + kimlik yok | 401 |
| 5 | `implemented => false` | Tanımlı, canlı değil davranış |
| 6 | Çift bölünme (`//api//v1`) | Normalize edilip eşleşir |
| 7 | `%2F` kodlanmış bölünme | Parçalama güvenli |
| 8 | Parametreli desen | Parametreler çıkarılır |
| 9 | Sıra: auth önce, handler sonra | Sıra testi |
| 10 | `X-Request-ID` her yanıtta | Başlık mevcut ve tekil |
| 11 | `X-API-Version` sabit | Sürüm başlığı doğru |
| 12 | `requiredRole` ataması yokken davranış | §14 kararı sonrası yazılacak |

## §20 — Açık sorular ve doğrulanamayan iddialar

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | `requiredRole` / `requiredPermission` neden hiçbir rotada atanmıyor? | `⚠️ VERIFICATION REQUIRED` | security-engineer |
| 2 | Middleware gerçek kayıt sırası nedir? | `⚠️ VERIFICATION REQUIRED` (§10.2 tasarım) | backend-architect |
| 3 | Route eşleme süresi ölçüldü mü? | `UNKNOWN` | performance-engineer |
| 4 | `implemented => false` rotaların canlıya geçiş tarihi? | `UNKNOWN` | product-manager |
| 5 | Versiyon kaldırma süresi (destek penceresi)? | `UNKNOWN` | architect |
| 6 | İki çatının path ayrımı hangi kurala bağlı? (önek münhasırlığı) | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 7 | SPA çatısında 404 yönetimi nasıl ele alınıyor? | `⚠️ VERIFICATION REQUIRED` | frontend-developer |

## §21 — Red team: saldırı senaryoları

| # | Saldırı | Öncül | Savunma |
|---|---|---|---|
| 1 | Açık yönlendirme | `?next=https://dış-site` | `ReturnUrlPolicy` köken kısıtı |
| 2 | Metod karıştırma | Güvenli handler'ı POST ile çağırmak | 405 + Allow tutarlılığı |
| 3 | Yol geçirgenliği | `%2e%2e%2f` ile üst dizin | Normalizasyon + karşılaştırma |
| 4 | Rota keşfi | 404/405 farkıyla haritalama | Yanıt biçimi tutarlılığı |
| 5 | Yetkisiz erişim | `public` bayrağı yanlış `true` | Bayrak denetimi (PR) |
| 6 | İstismar edilmemiş eski sürüm | `/api/v0` kalıntısı | Sürüm allowlist |
| 7 | Handler zinciri ihlali | Sıra değiştirme | Pipeline sabitliği |
| 8 | Cache poisoning | Yanıt header manipülasyonu | `Vary`/`Cache-Control` (dosya: caching) |

## §22 — Politika ↔ kod karşılaştırma tablosu

| Politika | Kod karşılığı | Durum |
|---|---|---|
| Her rota `service/handler/public/implemented` taşır | `routes.php:36` örnek | UYGUN |
| 405 yanıtında `Allow` | `RouteTable.php:128` | UYGUN |
| RBAC anahtarları | Dokümante (`:15`), atama 0 | **EKSİK** (§14) |
| Cache bayrağı dokümanı | `routes.php:16` | DOKÜMANTE |
| Middleware sarmalama | `MiddlewarePipeline.php:33` | UYGUN |
| SPA/API ayrımı | İki ayrı çatı | UYGUN (ayrım kuralı belirsiz, §20 #6) |
| Problem-details | Kodda doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| `X-Request-ID` | `ApiResponse.php:30` | UYGUN |

## §23 — Faz planı

| Faz | Kapsam | Durum |
|---|---|---|
| 0 | Bu sözleşme dosyası | YAZILDI (draft) |
| 1 | `requiredRole`/`requiredPermission` kararının verilmesi (§14) | BEKLEMEDE |
| 2 | Middleware sıra kaydının gözlemlenip §10.2'nin sabitlenmesi | BEKLEMEDE |
| 3 | Eşleme veri yapısının hash/trie'ye taşınması | PLANNED |
| 4 | Problem-details yanıt biçiminin kodlanması | PLANNED |
| 5 | Eşleme metriğinin loglanması (K013) | PLANNED |

## §24 — Risk kaydı

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | RBAC anahtarı kullanılmadan yayınlanma | Yüksek | Orta | §14 kararı P0 |
| 2 | Sıra yanlışlığıyla auth atlanma | Yüksek | Orta | §10.2 sabitleme + sıra testi |
| 3 | Rota sayısında O(n) büyüme | Orta | Orta | Faz 3 |
| 4 | İki çatıda path çakışması | Orta | Düşük | §20 #6 kuralı |
| 5 | `implemented => false` rota yanlışlıkla canlı | Orta | Düşük | Bayrak testi |

## §25 — Özet tablo

| Boyut | Karar | Durum |
|---|---|---|
| Çatı sayısı | 2 (API + SPA) | ÖLÇÜLDÜ |
| Rota kaydı (routes.php) | 17 (11 GET + 6 POST) | ÖLÇÜLDÜ (2026-10-13) |
| `implemented => true` | 7 | ÖLÇÜLDÜ |
| 405 + Allow | Var | KANITLI (`RouteTable.php:126-128`) |
| RBAC rota ataması | 0 | ÖLÇÜLDÜ (açık soru) |
| Versiyon öneki | `/api/v1` | KANITLI (`routes.php:36`) |
| Yanıt izi | `X-Request-ID` | KANITLI (`ApiResponse.php:30`) |
| Sarmalama yönü | Son eklenen en dışta | KANITLI (`MiddlewarePipeline.php:33`) |
| Problem-details | Tasarım, kod yok | `⚠️ VERIFICATION REQUIRED` |

## §26 — Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış repo kod alıntısı | 24 | §6 tablosu |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu (hepsi HTTP 200) |
| Envanter kesişimi | 0 | §4 ölçüm notu |
| Sıra tasarımı (gözlemsel değil) | 1 | §10.2 — işaretli |
| Açık soru | 7 | §20 |
| Saldırı senaryosu | 8 | §21 |
| Test senaryosu | 12 | §19 |
| Faz | 6 (0–5) | §23 |
| Belirsiz/UNKNOWN alan | 4 | §18.1 · §20 |

## §27 — Sonraki adımlar

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | §14 RBAC ataması kararını ver (ADR veya onay) | P0 | security-engineer |
| 2 | Gerçek middleware sırasını oku, §10.2'yi gözlemsel yap | P0 | backend-architect |
| 3 | Eşleme süresini ölç (§18.1) | P1 | performance-engineer |
| 4 | Problem-details yanıtını ekle (faz 4) | P1 | developer |
| 5 | Rota kayıt testlerini yaz (§19) | P1 | qa-engineer |
| 6 | SPA/API path ayrım kuralını yaz (§20 #6) | P2 | architect |

## §28 — Yeni rota kayıt şablonu

| Alan | Değer örneği | Zorunlu | Denetim |
|---|---|---|---|
| Anahtar (URI) | `/api/v1/music/{id}` | Evet | Benzersizlik |
| `service` | `music` | Evet | Servis listesiyle uyum |
| `handler` | `musicController` | Evet | Dosyada var mı |
| `action` | `getTrack` | Evet | Controller'da dal var mı |
| `public` | `false` | Evet | Güvenli varsayım |
| `implemented` | `false` → sonra `true` | Evet | Canlı geçiş |
| `requiredRole` | Karar sonrası (§14) | Hayır | ADR-013/authorization |
| `requiredPermission` | Karar sonrası (§14) | Hayır | ADR-013/authorization |
| `cacheable` | `true` yalnız okuma | Hayır | Cache dosyası |
| `cacheTtl` | 60 | Hayır | `cacheable` ise |

## §29 — Ölçüm özeti

| Ölçüm | Değer | Tarih |
|---|---|---|
| `routes.php` toplam kayıt | 17 | 2026-10-13 |
| GET / POST dağılımı | 11 / 6 | 2026-10-13 |
| `implemented => true` | 7 | 2026-10-13 |
| `requiredRole` / `requiredPermission` ataması | 0 | 2026-10-13 |
| Doğrulanmış repo alıntısı | 24 | 2026-10-13 |
| Doğrulanmış web kaynağı | 6 | 2026-10-13 |
| Envanter kesişimi | 0 | 2026-10-13 |
| Çatı sayısı | 2 (API + SPA) | 2026-10-13 |
| Middleware pipeline metodu | `pipe` / `run` / `wrap` | 2026-10-13 |
| 405 + `Allow` kanıtı | `RouteTable.php:126-128` | 2026-10-13 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
