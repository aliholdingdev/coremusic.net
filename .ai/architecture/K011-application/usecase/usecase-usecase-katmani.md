---
node_id: K011-application/usecase/usecase-usecase-katmani
node_type: module
status: draft
parent_id: K011-application/usecase
children: []
depends_on:
  - K011-application
evidence: []
validation: []
open_questions: []
group: mimari
---

# usecase-usecase-katmani — Use-Case Katmanı Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K011-application · **Alt klasör:** `usecase`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-002 (ORM yok, PDO) · ADR-039 (paylaşılan altyapı)
- **İmla koruması:** düğüm adları `identitiy` · `integraion` yazım bozuklukları bilinçli korunmuştur.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1–§3 Amaç, terminoloji, yerleşim | mimari | katman | Use-case katmanının K011 konumu (ADR-002, ADR-039) |
| 2 | §4–§6 Web research, standart tabanı, repo kanıtları | kanit | kaynak-kod | Harici kaynaklar + `Dosya: … · Satır: …` izleri |
| 3 | §7–§9 İşlem, dispatch, doğrulama sırası | mimari | sözleşme | Transaction sözleşmesi, orkestrasyon, yan etki sırası |
| 4 | §10–§12 Hata eşlemesi, girdi kapsamı, çıktı | mimari | api | Hata kodu eşlemesi, girdi sınırı, çıktı DTO sözleşmesi |
| 5 | §13–§15 İdempotency, CQRS, politika | kanit | denetim | İdempotency anahtarı, CQRS ilişki, politika↔kod matrisi |
| 6 | §16–§20 Test, red team, risk, faz, açık sorular | kanit | doğrulama | Test taslakları, saldırı senaryoları, faz planı, açık sorular |
| 7 | §21–§26 Özet, envanter, sonraki adımlar, devir, karar matrisi, kontrol | mimari | ölçüm | Sayımlar, karar kaydı, uygunluk kontrol listesi |
| 8 | Kardeş dosyalar (`-dogrulama` · `-web-arastirma` · `-ornekler` · `-bilesenler` · `index.md`) | kod/kanit | üretim | İkinci eksen: dosya grubu × kullanım ekseni (Ünite-3 seti) |

## §1 — Amaç ve kapsam

Bu dosya, HTTP isteğini **iş kuralına** çeviren use-case (kullanım vakası) katmanının
sözleşmesini tanımlar: girdinin kabulü, doğrulama, iş mantığı orkestrasyonu, işlem
(transaction) sınırları, hata eşlemesi ve çıktının DTO'ya dönüştürülmesi. Katman,
routing ile domain arasında durur ve **tek yönlü** bağımlılık kuralına tabidir.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Dispatch | Aksiyon → iş mantığı eşlemesi | URI eşlemesi (routing dosyası) |
| 2 | Girdi kabulü | DTO → use-case girdi sınırı | Gövde ayrıştırma (DTO dosyası) |
| 3 | Doğrulama | Şema/alan doğrulama sırası | Doğrulama kütüphanesi detayı (altyapı) |
| 4 | İşlem sınırı | Transaction başlangıç/bitiş | Şema tasarımı (K005-data) |
| 5 | Hata eşlemesi | Domain istisnası → HTTP kodu | Hata günlüğü (K013-audit) |
| 6 | Çıktı | Use-case sonucu → response DTO | Cache'leme (caching dosyası) |
| 7 | Yan etki | Domain event yayımı | Event altyapısı (ADR-086) |
| 8 | İdempotency | Tekrarlı çağrının güvenliği | İstemci tarafı tekrar (frontend) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Use-case | use case | Tek bir kullanıcı hedefini yürüten birim | `AuthController::handle*` |
| Dispatch | dispatch | Aksiyon adının iş mantığına bağlanması | `match ($action)` |
| Aksiyon | action | Rota içindeki işlem adı | `route['action']` |
| Sınır | boundary | Katmanlar arası tek veri taşıyıcısı | Request/Response DTO |
| Doğrulama | validation | Girdinin şemaya uygunluğu | `respect/validation` (`composer.json:20`) |
| Transaction | transaction | Ya hep ya hiç yazma grubu | `DatabaseManager::beginTransaction()` |
| Idempotency | idempotency | Aynı isteğin ikinci kez güvenli oluşu | §13 |
| Domain exception | domain failure | Kural ihlalini taşıyan tipli hata | `ConflictException`, `RateLimitException` |
| Hata eşlemesi | error mapping | İstisna → HTTP durum kodu | `AuthController:62-70` |
| Çıktı DTO | response DTO | Kullanıma dönüştürülmüş sonuç | `LoginResponse`, `PaginationResponse` |
| Retry-After | retry after | İstemciye beklenecek süre | `AuthController:63` |
| Side effect | side effect | DB dışı gözlemlenebilir etki | Domain event |
| Servis katmanı | service layer | Use-case'in altındaki iş kuralları | `AuthService` |
| Repository | repository | Kalıcılık erişim soyutlaması | `ApiKeyRepository` |
| Orkestrasyon | orchestration | Birden fazla çağrının sıralanması | §8 |
| Fail-closed | fail-closed | Belirsizlikte reddetme | Hata eşlemesi varsayılanı |
| Handler | handler | Use-case'i çağıran HTTP tarafı | `AuthPostHandler` |
| Kapsam | scope | Use-case'in yazabildiği kaynak kümesi | §11.2 |

## §3 — Mimari yerleşim (kademe-3 konumu)

| Katman | Use-case katmanının gördüğü | Kanıt |
|---|---|---|
| Presentation | Route + gövde dizisi | `AuthPostHandler.php:25` (`$request['uri']`) |
| Application | **Bu dosya** — dispatch, doğrulama, transaction | `AuthController.php:45` |
| Domain | Servis + domain exception | `ConflictException` / `RateLimitException` |
| Infrastructure | Repository, PDO transaction | `ApiKeyRepository.php:74`, `DatabaseManager.php:49` |

**Yön kuralı:** use-case, routing'i **bilmez**; yalnız girdi dizisi ve sonuç dizisi
görür. Routing, use-case'in **adını** bilir (`action`), implementasyonunu bilmez.

## §4 — Web research (2026-10-13 doğrulaması)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Azure CQRS deseni, okuma ve yazma yüklerinin ayrılmasını tanımlar | https://learn.microsoft.com/en-us/azure/architecture/patterns/cqrs | HTTP 200 | Doğrulandı — envanterde yok |
| PSR-17, HTTP factory arayüzlerini (istek/yanıt/çıkış akışı üretimi) tanımlar | https://www.php-fig.org/psr/psr-17/ | HTTP 200 | Doğrulandı — envanterde yok |
| PSR-11, konteyner (container) arayüzünü `get()` / `has()` ile tanımlar | https://www.php-fig.org/psr/psr-11/ | HTTP 200 | Doğrulandı — envanterde yok |
| Microsoft modern web uygulaması mimarisi, katmanlı yapıda katman sorumluluklarını tanımlar | https://learn.microsoft.com/en-us/dotnet/architecture/modern-web-apps-azure/ | HTTP 200 | Doğrulandı — envanterde yok |
| PHP `Throwable` arayüzü, tüm yakalanabilir hataların ortak atasıdır | https://www.php.net/manual/en/class.throwable.php | HTTP 200 | Doğrulandı — envanterde yok |
| PHP PDO transaction dokümanı, `beginTransaction` / `commit` / `rollBack` sırasını tanımlar | https://www.php.net/manual/en/pdo.transactions.php | HTTP 200 | Doğrulandı — envanterde yok |

**Doğrulama yöntemi:** altı URL `Invoke-WebRequest` ile çağrıldı → `200`; `.ai/` genelinde
URL öbeği araması → **0 isabet**.

## §5 — Standart ve spesifikasyon tabanı

| Kaynak | Ne düzenler | Bu dosyadaki kullanımı |
|---|---|---|
| PSR-17 | HTTP factory ortakları | §12.4 |
| PSR-11 | Konteyner arayüzü | §8.3 |
| Azure CQRS | Okuma/yazma ayrımı | §14 |
| PHP `Throwable` | Yakalanabilir hata hiyerarşisi | §10 |
| PDO transaction | İşlem sırası | §7 |
| Modern web apps | Katman sorumlulukları | §3 |
| ADR-002 | ORM yok, doğrudan PDO | §7.3 |
| ADR-086 | Domain event yayımı | §9 |

## §6 — Repo kod kanıtları (2026-10-13 doğrulanmış satırlar)

| # | Dosya | Satır | Ne gösterir |
|---|---|---|---|
| 1 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 25 | `$uri = trim($request['uri'] ?? '', '/');` — girdi normalizasyonu |
| 2 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 27 | `if ($method !== 'POST') {` — metod şartı |
| 3 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 35 | `$normalizedRequest = $this->normalizeRequest($request);` |
| 4 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 38 | `return match ($uri) {` — URI tabanlı dispatch |
| 5 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 45 | `default` dalı: `httpStatus => 404`, `type => json`, `body.success => false` — bilinmeyen aksiyon 404 |
| 6 | `auth.coremusic.net/include/Handler/AuthPostHandler.php` | 51 | `catch (\Throwable $e)` — fail-closed sarmalayıcı |
| 7 | `api.coremusic.net/include/Controller/AuthController.php` | 45 | `public function handle(array $route, array $request): array` — use-case girişi |
| 8 | `api.coremusic.net/include/Controller/AuthController.php` | 47 | `$action = (string) ($route['action'] ?? '');` — aksiyon okuma |
| 9 | `api.coremusic.net/include/Controller/AuthController.php` | 52 | `return match ($action) {` — aksiyon dispatch |
| 10 | `api.coremusic.net/include/Controller/AuthController.php` | 56 | `'forgotPassword' => $this->forgotPassword($body, $ip),` |
| 11 | `api.coremusic.net/include/Controller/AuthController.php` | 62 | `catch (RateLimitException $e) {` — tipli hata yakalama |
| 12 | `api.coremusic.net/include/Controller/AuthController.php` | 63 | `header('Retry-After: ' . $e->getRetryAfter());` |
| 13 | `api.coremusic.net/include/Controller/AuthController.php` | 66 | `catch (ConflictException $e) {` — çakışma hattı |
| 14 | `api.coremusic.net/include/Controller/AuthController.php` | 70 | `return $this->error($code, $e->getMessage(), 409);` — 409 eşlemesi |
| 15 | `shared/src/Api/Dto/Request/LoginRequest.php` | 19 | `public function __construct(` — immutable girdi |
| 16 | `shared/src/Api/Dto/Request/LoginRequest.php` | 52 | `public static function fromArray(array $data): self` |
| 17 | `shared/src/Api/Dto/Request/LoginRequest.php` | 64 | `public function toArray(): array` |
| 18 | `shared/src/Middleware/ValidationMiddleware.php` | 17 | `public function handle(array $request, callable $next): array` |
| 19 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 46 | `public function __invoke(array $request, callable $next): array` |
| 20 | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 116 | `private function isAcceptedContentType(string $contentType): bool` |
| 21 | `shared/src/Database/DatabaseManager.php` | 49 | `public function beginTransaction(): bool` |
| 22 | `shared/src/Repository/ApiKeyRepository.php` | 74 | `public function create(array $params): array` — kalıcılık çağrısı |
| 23 | `shared/composer.json` | 20 | `"respect/validation": "^2.0",` — doğrulama kütüphanesi |
| 24 | `shared/src/Api/Dto/Response/PaginationResponse.php` | 104 | `$totalPages = (int) ceil($totalItems / $pageSize);` — çıktı hesabı |

## §7 — İşlem (transaction) sözleşmesi

### §7.1 Yaşam döngüsü

| # | Adım | Kod | Hata durumu |
|---|---|---|---|
| 1 | Başlat | `beginTransaction()` (`DatabaseManager.php:49`) | Başlatılamazsa use-case **başlamaz** |
| 2 | Yaz | Repository çağrıları (`ApiKeyRepository.php:74`) | Domain exception → rollback |
| 3 | Onayla | `commit()` | Başarısızsa rollback |
| 4 | Geri al | `rollBack()` | İstisna yolunda zorunlu |
| 5 | Serbest bırak | Bağlantı bırakma | `finally` ile |

### §7.2 Sınır kuralları

| Kural | Değer |
|---|---|
| Tek use-case = tek transaction | Çok use-case birleşimi yasak |
| HTTP dispatch içinde transaction **açılmaz** | Handler yalnız orkestre eder |
| Read-only use-case | Transaction açılmaz (okuma) |
| Rollback tetikleyicisi | `Throwable` (§10) |
| Nested transaction | Yok — `⚠️ VERIFICATION REQUIRED` (kodda doğrulanmadı) |

### §7.3 ORM yasağı ile ilişki

| Kayıt | Hükmü |
|---|---|
| ADR-002 | ORM yok; doğrudan PDO prepared statement |
| Sonuç | Use-case, repository'yi **arayüz üzerinden** çağırır; SQL use-case'e yazılmaz |
| Test | Use-case testleri sahte repository ile çalışır (§16.2) |

## §8 — Dispatch ve orkestrasyon

### §8.1 İki dispatch katmanı (gözlemsel)

| Katman | Anahtar | Kanıt | Kapsam |
|---|---|---|---|
| Handler | `uri` | `AuthPostHandler.php:38` | auth.coremusic.net POST uçları |
| Controller | `route['action']` | `AuthController.php:47` ve `:52` | api.coremusic.net aksiyonları |

**İlişki:** handler, URI'yi normalize edip controller'a aktarır; controller action'a
göre somut metodu çağırır. İki seviye de `match` ifadesi kullanır — okunabilirlik
yüksek, tablo büyüklüğünde **switch/match yeniden değerlendirilmelidir** (§23 faz 3).

### §8.2 Aksiyon tablosu (AuthController)

| Aksiyon | Metod | Kanıt |
|---|---|---|
| `login` | `$this->login($body, $ip)` | `AuthController.php:53` |
| `register` | `$this->register($body, $ip)` | `AuthController.php:54` |
| `setGender` | `$this->setGender($body)` | `AuthController.php:55` |
| `forgotPassword` | `$this->forgotPassword($body, $ip)` | `AuthController.php:56` |
| `resetPassword` | `$this->resetPassword($body)` | `AuthController.php:57` |
| `logout` | `$this->logout()` | `AuthController.php:58` |
| `me` | `$this->me()` | `AuthController.php:59` |
| Bilinmeyen | 404 sınıfı `error` çağrısı (`'NOT_FOUND'`) | `AuthController.php:60` |

### §8.3 Konteyner bağımlılığı

| Konu | Durum | Kaynak |
|---|---|---|
| PSR-11 arayüzü | Tasarım referansı | §4 PSR-11 |
| Somut konteyner | `⚠️ VERIFICATION REQUIRED` | Bu dosyada doğrulanmadı |
| Bağımlılık enjeksiyonu | Kurucu üzerinden (şematik) | `AuthPostHandler` constructor kullanımı |

## §9 — Doğrulama ve yan etki sırası

| # | Adım | Sahip | Kanıt |
|---|---|---|---|
| 1 | Metod kontrolü | Handler | `AuthPostHandler.php:27` |
| 2 | URI normalizasyonu | Handler | `AuthPostHandler.php:25` |
| 3 | Gövde ayrıştırma | Middleware | `RequestValidationMiddleware.php:133` |
| 4 | Content-Type kontrolü | Middleware | `RequestValidationMiddleware.php:116` |
| 5 | Şema doğrulama | Middleware | `ValidationMiddleware.php:17` |
| 6 | Rate limit | Middleware | ADR-013 (dosya: caching) |
| 7 | DTO üretimi | Controller | `LoginRequest.php:52` |
| 8 | İş mantığı | Servis | `AuthService` |
| 9 | Kalıcılık | Repository | `ApiKeyRepository.php:74` |
| 10 | Çıktı DTO | Controller | `LoginResponse` |

### §9.1 Domain event yayımı

| Kural | Değer | Kaynak |
|---|---|---|
| Yayım anı | Transaction commit sonrası | §7.2 |
| Örnek event | `UserLoggedInEvent` | `shared/src/Events/Domain/UserLoggedInEvent.php` |
| Başarısızlık | Event hatası use-case'i **bozmaz** | ADR-086 (⚠️ doğrulanmadı — §20 #4) |
| Kayıt | Event adı + payload hash'i | K013-audit |

## §10 — Hata eşlemesi

### §10.1 İstisna → HTTP

| İstisna | HTTP | Ek başlık | Kanıt |
|---|---|---|---|
| `RateLimitException` | 429 | `Retry-After` | `AuthController.php:62-65` |
| `ConflictException` | 409 | — | `AuthController.php:66-70` |
| Bilinmeyen aksiyon | 404 | — | `AuthController.php:60` |
| Bilinmeyen URI | 404 | — | `AuthPostHandler.php:45` |
| Metod yanlış | 405 | — | `AuthPostHandler.php:27-33` |
| Doğrulama hatası | 422 | — | §9.5 (şema) |
| Kimlik yok | 401 | — | Auth middleware |
| Ele alınmayan istisna | 500 | — | `AuthPostHandler.php:51-56` |

### §10.2 Kod eşlemesi (ConflictException)

| Domain kodu | HTTP gövde kodu | Kanıt |
|---|---|---|
| `USERNAME_EXISTS` | `USERNAME_TAKEN` | `AuthController.php:68` |
| `EMAIL_EXISTS` | `EMAIL_TAKEN` | `AuthController.php:68` |
| Genel çakışma | 409 | `AuthController.php:70` |

### §10.3 Hata gövdesi kuralları

| Kural | Doğru | Yanlış |
|---|---|---|
| Sızıntı | Genel mesaj | `getMessage()` ile dahili ayrıntı (bkz. §20 #5) |
| Kararlılık | Kod alanı sabit | Mesaj metnine bağımlı istemci |
| Log | Sunucu tarafı ayrı kayıt | Yanıt gövdesine log |
| Bağlam | `X-Request-ID` | Yalnız metin |

> **⚠️ VERIFICATION REQUIRED:** `AuthController.php:70` hata mesajını `$e->getMessage()`
> ile taşıyor; mesajın dahili bilgi içerip içermediği bu dosyada **doğrulanmadı** (§20 #5).

## §11 — Girdi ve kapsam

### §11.1 Girdi kaynakları

| Kaynak | Kullanım | Kanıt |
|---|---|---|
| `$request['uri']` | URI dispatch | `AuthPostHandler.php:25` |
| `$route['action']` | Aksiyon dispatch | `AuthController.php:47` |
| `$this->body($request)` | Gövde | `AuthController.php:48` |
| `$this->clientIp($request)` | İstemci IP (rate limit bağlamı) | `AuthController.php:49` |

### §11.2 Kapsam (scope) kuralları

| Kural | Değer |
|---|---|
| Use-case yalnız kendi aggregate'ine yazar | Başka aggregate doğrudan güncellenmez |
| Başka aggregate gerekiyorsa | Event veya açık servis çağrısı (§9.1) |
| Cross-use-case çağrı | Yasak — orkestrasyon üst katmanda |
| Kalıcılık erişimi | Yalnız repository arayüzü |

## §12 — Çıktı sözleşmesi

### §12.1 Çıktı biçimi

| Alan | Değer | Kanıt |
|---|---|---|
| Dönen tip | `array` (HTTP gövdesi) | `AuthController.php:45` |
| DTO dönüşümü | Controller içinde | `LoginResponse` |
| Sayfalama hesabı | `ceil(total / pageSize)` | `PaginationResponse.php:104` |
| Başlıklar | `X-Request-ID`, `X-API-Version` | `ApiResponse.php:30-31` |

### §12.2 Sarmalayıcı yapı

| Anahtar | Zorunlu | Açıklama |
|---|---|---|
| `httpStatus` | Evet | HTTP durum kodu |
| `type` | Evet | `json` |
| `body` | Evet | Yanıt gövdesi |
| `success` | Hayır | `true`/`false` bayrağı |

### §12.3 İçerik kuralları

| Kural | Değer |
|---|---|
| Sızdırılmayan alan | Parola hash'i, token, dahili id |
| Tutarlı adlandırma | camelCase (DTO) |
| Eksik alan | `null` mi yoksa yok mu — kararı: `UNKNOWN` (§20 #7) |
| Sayı tipi | JSON sayı olarak (float riski: `ceil` → `int` dönüşümü `PaginationResponse.php:104`) |

### §12.4 Factory kullanımı

| Konu | Durum |
|---|---|
| PSR-17 factory | Tasarım referansı (§4) |
| Somut kullanım | `⚠️ VERIFICATION REQUIRED` — kodda doğrulanmadı |

## §13 — İdempotency

| Aksiyon | İdempotent mi | Gerekçe |
|---|---|---|
| `login` | Hayır (yeni session) | Amaç yeni oturum |
| `register` | Kısmen | Aynı e-posta 409 verir (`AuthController.php:66`) |
| `logout` | Evet | İkinci çağrı da güvenli |
| `forgotPassword` | Kısmen | Tekrar gönderim kotası ADR-013 |
| `resetPassword` | Evet (token tek kullanımlık) | Token tüketimi |
| Para transferi sınıfı | Yok | CoreMusic'te ödeme yok (kapsam dışı) |

**İdempotency anahtarı:** `Idempotency-Key` başlığı için **kod kanıtı yok** →
`⚠️ VERIFICATION REQUIRED` (§20 #6).

## §14 — CQRS ile ilişki

| Boyut | Durum | Not |
|---|---|---|
| Okuma/yazma ayrımı | Kısmi | `LoginResponse`/`PaginationResponse` ayrımı var |
| Ayrı model | Yok | Aynı controller içinde |
| Ayrı depo | Yok | ADR-002 tek PDO |
| Gerekçe | Basitlik | CQRS, ölçekte gerekçeli — eşiği **UNKNOWN** |

## §15 — Politika ↔ kod karşılaştırma tablosu

| Politika | Kod karşılığı | Durum |
|---|---|---|
| Metod şartı | `AuthPostHandler.php:27` | UYGUN |
| Bilinmeyen aksiyon 404 | `AuthPostHandler.php:45`, `AuthController.php:60` | UYGUN |
| Fail-closed catch | `AuthPostHandler.php:51` | UYGUN |
| İstisna → kod eşlemesi | `AuthController.php:62-70` | UYGUN |
| Retry-After | `AuthController.php:63` | UYGUN |
| Doğrulama kütüphanesi | `composer.json:20` | UYGUN |
| Transaction sınırları | `DatabaseManager.php:49` (başlangıç) — commit/rollback doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| Hata mesajı sızıntısı riski | `$e->getMessage()` (`:70`) | `⚠️ VERIFICATION REQUIRED` |
| İdempotency anahtarı | Kod kanıtı yok | EKSİK |

## §16 — Test senaryoları (taslak)

### §16.1 Davranış testleri

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | Bilinen aksiyon + geçerli girdi | 200 sınıfı sonuç |
| 2 | Bilinmeyen aksiyon | 404 (`AuthController.php:60`) |
| 3 | Bilinmeyen URI | 404 (`AuthPostHandler.php:45`) |
| 4 | POST olmayan metod | 405 (`AuthPostHandler.php:27`) |
| 5 | Geçersiz gövde | 422 sınıfı |
| 6 | Kota aşımı | 429 + `Retry-After` (`:63`) |
| 7 | Çakışan kayıt | 409 + doğru kod (`:68-70`) |
| 8 | Beklenmeyen istisna | 500, gövdede dahili ayrıntı yok |
| 9 | Geçerli girdi + repository hatası | Rollback (§7.1 adım 4) |
| 10 | Yan etki (event) | Commit sonrası yayım (§9.1) |

### §16.2 Birim testi stratejisi

| Katman | Çiftlik | Ayrık test |
|---|---|---|
| Use-case | Sahte repository | I/O yok |
| Domain servis | Sahte clock/dep | Deterministik |
| Controller dispatch | Sahte servis | HTTP yok |
| Doğrulama | Gerçek kütüphane | Şema testleri |

## §17 — Red team: saldırı senaryoları

| # | Saldırı | Öncül | Savunma |
|---|---|---|---|
| 1 | Hata mesajı ile bilgi sızdırma | Ayrıntılı `getMessage()` | §10.3 + §20 #5 |
| 2 | Yarım transaction | Commit'siz bırakma | §7.2 `finally` |
| 3 | Yeniden oynatma | Aynı kayıt iki kez | Idempotency (§13) |
| 4 | Aksiyon enjeksiyonu | Sahte `action` değeri | Match default → 404 |
| 5 | Doğrulama atlanma | Middleware bypass | Sıra (routing §10) |
| 6 | Yetki genişleme | Use-case'in yetki okuması | Authorization katmanı (K010) |
| 7 | Yavaş sorgu ile kaynak tüketimi | Uzun use-case | ADR-013 + timeout (§20 #8) |

## §18 — Risk kaydı

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Hata mesajı sızıntısı | Yüksek | Orta | §20 #5 P0 |
| 2 | Transaction commit/rollback kanıtının eksikliği | Yüksek | Orta | §20 #2 |
| 3 | İki dispatch seviyesinin çelişkisi | Orta | Orta | Faz 2 birleştirme |
| 4 | İdempotency eksikliği | Orta | Düşük | Faz 4 |
| 5 | Match ifadelerinin büyümesi | Düşük | Yüksek | Tablo/registry (faz 3) |

## §19 — Faz planı

| Faz | Kapsam | Durum |
|---|---|---|
| 0 | Bu sözleşme dosyası | YAZILDI (draft) |
| 1 | Commit/rollback kanıtının okunup §7.1'in gözlemselleşmesi | BEKLEMEDE |
| 2 | İki dispatch seviyesinin tek sözleşmeye indirilmesi | PLANNED |
| 3 | Aksiyon tablosunun veri yapısına çevrilmesi | PLANNED |
| 4 | İdempotency anahtarı desteği | PLANNED |
| 5 | CQRS ihtiyacının ölçülmesi (§14) | PLANNED |

## §20 — Açık sorular ve doğrulanamayan iddialar

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | Hangi use-case'ler hangi aggregate'e yazıyor? (harita) | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 2 | `commit`/`rollBack` çağrıları hangi dosyada? | `⚠️ VERIFICATION REQUIRED` | data-engineer |
| 3 | PSR-17 factory somut olarak kullanılıyor mu? | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 4 | Event yayımı başarısızlığı use-case'i bozar mı? | `UNKNOWN` | architect |
| 5 | `$e->getMessage()` dahili bilgi sızdırıyor mu? | `⚠️ VERIFICATION REQUIRED` | security-engineer |
| 6 | İdempotency anahtarı gerekiyor mu, istemciler hazır mı? | `UNKNOWN` | product-manager |
| 7 | Eksik alan `null` mı, yok anahtarı mı? | `UNKNOWN` | backend-architect |
| 8 | Use-case başına zaman aşımı var mı? | `UNKNOWN` | sre-engineer |

## §21 — Özet tablo

| Boyut | Karar | Durum |
|---|---|---|
| Dispatch seviyesi | 2 (handler URI + controller action) | ÖLÇÜLDÜ |
| Aksiyon sayısı | 7 + default 404 | ÖLÇÜLDÜ (`:52-60`) |
| Hata eşlemesi | Tipli istisna → kod | KANITLI (`:62-70`) |
| Retry-After | Var | KANITLI (`:63`) |
| Doğrulama kütüphanesi | `respect/validation ^2.0` | KANITLI (`composer.json:20`) |
| Transaction başlangıcı | `beginTransaction` | KANITLI (`DatabaseManager.php:49`) |
| Commit/rollback | Doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| İdempotency | Kod yok | EKSİK |
| Fail-closed catch | Var | KANITLI (`AuthPostHandler.php:51`) |

## §22 — Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış repo kod alıntısı | 24 | §6 tablosu |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu (hepsi HTTP 200) |
| Envanter kesişimi | 0 | §4 ölçüm notu |
| Açık soru | 8 | §20 |
| Saldırı senaryosu | 7 | §17 |
| Test senaryosu | 10 (+4 strateji) | §16 |
| Faz | 6 (0–5) | §19 |
| İşaretli doğrulanmamış alan | 6 | §20 etiketleri |

## §23 — Sonraki adımlar

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | §20 #5 hata mesajı sızıntısını denetle | P0 | security-engineer |
| 2 | §20 #2 commit/rollback kanıtını oku, §7.1'i güncelle | P0 | data-engineer |
| 3 | İki dispatch seviyesini birleştir (faz 2) | P1 | backend-architect |
| 4 | §16.1 testlerini yaz (qa-engineer) | P1 | qa-engineer |
| 5 | İdempotency kararını sor (§20 #6) | P2 | product-manager |
| 6 | CQRS ihtiyacını ölç (§19 faz 5) | P2 | performance-engineer |

## §24 — Kapsam dışı devir tablosu

| Konu | Bu dosyadan çıkan yere | Gerekçe |
|---|---|---|
| URI eşlemesi | `routing/routing-yonlendirme-cati` | Ayrı çatı dosyası |
| Response DTO alanları | `dto/dto-veri-tasiyici-tasarimi` | Taşıyıcı tasarımı ayrı |
| Cache'leme ve ETag | `caching/caching-uygulama-seviyesi-cache` | Cache bayrağı routing'de tanımlı |
| Rol/izin kararı | `K010-identitiy/authorization` | Yetki ayrı K |
| Session/JWT | `K010-identitiy/session` | Kimlik zemini ayrı K |
| Rate limit sayacı | `caching/caching-uygulama-seviyesi-cache` | APCu sayacı bu dosyada değil |
| SQL şema ve index | `K005-data` | Kalıcılık tasarımı ayrı |
| Event yayım altyapısı | ADR-086 | Yalnız kullanım kuralı burada |
| Doğrulama kütüphanesi kurulumu | `composer.json` (altyapı) | Yalnız kullanım sırası burada |
| Hata günlükleme | `K013-audit` | Yalnız eşleme burada |
| BFF dönüşümü | `routing` §16 | Yönlendirme dosyasında işlendi |
| HTTP durum kodu semantiği | `routing` §12 | Kod listesi orada |
| OpenAPI/şema üretimi | `K012-integraion` | Sözleşme dışa aktarımı ayrı |
| Zaman aşımı stratejisi | `K010-identitiy/session` | Oturum ömrü ayrı |

## §25 — Karar matrisi (bu dosyada verilen kararlar)

| # | Karar | Değer | Dayanak |
|---|---|---|---|
| 1 | Dispatch anahtarı | `route['action']` (api) + `uri` (auth) | §8.1 kanıt |
| 2 | Bilinmeyen aksiyon | 404 (fail-closed) | `AuthController.php:60` |
| 3 | Metod uygunsuzluğu | 405 | `AuthPostHandler.php:27` |
| 4 | Hata sarmalayıcı | `catch (\Throwable)` | `AuthPostHandler.php:51` |
| 5 | Kota hatası | 429 + `Retry-After` | `AuthController.php:62-63` |
| 6 | Çakışma | 409 + alan bazlı kod | `AuthController.php:66-70` |
| 7 | Transaction sınırı | Tek use-case = tek transaction | §7.2 |
| 8 | Event yayımı anı | Commit sonrası | §9.1 |
| 9 | ORM | Yok (ADR-002) | §7.3 |
| 10 | İdempotency anahtarı | Henüz yok — karar ertelendi | §13 · §20 #6 |
| 11 | Hata mesajı sızıntısı | Denetim bekliyor | §20 #5 |

## §26 — Uygunluk kontrol listesi

| # | Kontrol | Beklenen kanıt | Durum |
|---|---|---|---|
| 1 | Her aksiyon `match` dalında mı? | `AuthController.php:52-61` | ✓ |
| 2 | Bilinmeyen aksiyon 404 mü? | `AuthController.php:60` | ✓ |
| 3 | Metod şartı var mı? | `AuthPostHandler.php:27` | ✓ |
| 4 | Fail-closed catch var mı? | `AuthPostHandler.php:51` | ✓ |
| 5 | Tipli hata eşlemesi var mı? | `AuthController.php:62-70` | ✓ |
| 6 | Doğrulama kütüphanesi mi? | `composer.json:20` | ✓ |
| 7 | Transaction başlangıcı okundu mu? | `DatabaseManager.php:49` | ✓ |
| 8 | Commit/rollback okundu mu? | §20 #2 | ✗ — işaretli |
| 9 | Hata mesajı denetlendi mi? | §20 #5 | ✗ — işaretli |
| 10 | İdempotency kararı verildi mi? | §20 #6 | ✗ — açık |
| 11 | Testler yazıldı mı? | §16 taslağı | ✗ — bekliyor |
| 12 | Kaynak alıntılar doğrulandı mı? | §6 (24 satır) | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
