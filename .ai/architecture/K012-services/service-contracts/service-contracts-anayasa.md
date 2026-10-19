---
node_id: K012-services/service-contracts
node_type: module
role: anayasa
status: draft
parent_id: K012-services/service-contracts
children: []
depends_on:
  - "service-contracts/index.md"
  - "services-master.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — shared/src/Contracts/ 28 dosya (19 interface + 9 CLAUDE.md), 2026-10-13 sayıldı"
  - "S1 — shared/src/Contracts/CLAUDE.md §3 sözleşme kuralları (interface segregation · dependency inversion · contract-first)"
  - "S1 — auth.coremusic.net/include/Service/AuthService.php:15–16 import (IUserRepository, IRateLimiter)"
validation:
  - "§3 envanter 2026-10-13 glob sayımı — 19 interface"
  - "§14 kontrat-çağrı karşılaştırması AuthService gövdesi ile birebir"
open_questions:
  - "IUserRepository::findValidAuthKey tek param — AuthService iki argüman çağırıyor (§14) → ⚠️ VERIFICATION REQUIRED"
  - "17 interface'in implementasyonu okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "I-prefix / prefix-siz ayrımı (§13) kural mı istisna → UNKNOWN"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: shared/src/Contracts/ (S1, 2026-10-13 read)"
group: mimari
---

# service-contracts — Anayasa

> **Bu dosya ne yapar:** servislerin birbirine ve kata söz verdiği
> **interface sözleşmelerinin** tam envanterini, kuralını ve çelişki
> kapılarını taşır. Kontrat = kod değildir; imzadır. Bu dosyada her
> interface'in metot sayısı, dönen tip ve implementasyon eşlemesi vardır.

## §1 ELI15 — Kontrat Nedir, Burada Ne Var?

| Soru | Cevap |
|---|---|
| Kontrat nedir? | `interface` — gövdesiz imza listesi; ne yapılacağını söyler, nasılını söylemez |
| Burada ne var? | `shared/src/Contracts/` altındaki **19 interface** (2026-10-13 sayımı) |
| Burada ne YOK? | Gövde (implementasyon), DTO sınıfı, veri akışı — hepsi başka katman |
| Kim uygular? | Service/Repository/Api sınıfları (`implements` ile) — §13 eşlemesi |
| Neden ayrı dosya? | Contract-first kuralı: önce sözleşme, sonra implementasyon (§5) |

## §2 Sınır Tablosu (bu düğüm ne DEĞİL)

| Sınır | Durum | Kanıt |
|---|---|---|
| Bu düğüm interface **tanımlar**, implementasyon içermez | Geçerli | 19 dosyanın tamamı `interface` + DocBlock |
| Dış (public) API kontratı K013'tedir | Kapsam dışı | `service-contracts/index.md` Sınır satırı |
| DTO sınıfları (`LoginRequest` vb.) burada değil | Kapsam dışı | `auth.coremusic.net/include/Domain/DTO/` envanteri |
| Event sınıf implementasyonları burada değil | Kapsam dışı | `Contracts/Events/` yalnız 2 interface |
| YZ kontratları (6 adet) bu düğümün parçası mı | Açık | index açık sorusu + §12; `⚠️ VERIFICATION REQUIRED` |
| Kademe-3 bounded-context kapsamı | ADR-097 K4 faz (C) bekliyor | `service-contracts/index.md` §A |

## §3 Envanter — 19 Interface (S1, 2026-10-13)

| # | Alt klasör | Dosya | Satır | Metot | İsim öneki |
|---|---|---|---|---|---|
| 1 | `AI/` | `AIEngineInterface.php` | 62 | 5 | `*Interface` |
| 2 | `AI/` | `AIOrchestratorInterface.php` | 67 | 6 | `*Interface` |
| 3 | `AI/` | `KnowledgeBaseInterface.php` | 60 | 5 | `*Interface` |
| 4 | `AI/` | `MemorySystemInterface.php` | 79 | 7 | `*Interface` |
| 5 | `AI/` | `PromptEngineInterface.php` | 57 | 5 | `*Interface` |
| 6 | `AI/` | `ToolCallingInterface.php` | 59 | 5 | `*Interface` |
| 7 | `Api/` | `BffInterface.php` | 34 | 2 | `*Interface` |
| 8 | `Api/` | `GatewayInterface.php` | 26 | 1 | `*Interface` |
| 9 | `Api/` | `ServiceRegistryInterface.php` | 50 | 4 | `*Interface` |
| 10 | `Auth/` | `IAuthService.php` | 15 | 8 | `I` prefix |
| 11 | `Auth/` | `ISessionManager.php` | 28 | 21 | `I` prefix |
| 12 | `Auth/` | `IUserRepository.php` | 51 | 20 | `I` prefix |
| 13 | `Config/` | `IConfigManager.php` | 14 | 7 | `I` prefix |
| 14 | `Database/` | `IDatabaseManager.php` | 13 | 6 | `I` prefix |
| 15 | `Database/` | `IDatabaseRegistry.php` | 10 | 3 | `I` prefix |
| 16 | `Events/` | `DomainEventInterface.php` | 39 | 3 | `*Interface` |
| 17 | `Events/` | `IntegrationEventInterface.php` | 46 | 4 | `*Interface` |
| 18 | `Middleware/` | `IMiddleware.php` | 8 | 1 | `I` prefix |
| 19 | `Security/` | `IRateLimiter.php` | 10 | 3 | `I` prefix |

**Toplam:** 19 interface · 116 method imzası (satır sayıları dosya uzunluğu,
metot sayıları imza satırı sayımıdır — ikisi farklı ölçüttür).

## §4 Alt Klasör Haritası — 9 Bağlam (S1)

| # | Alt klasör | Kontrat sayısı | Bağlam | CLAUDE.md var mı |
|---|---|---|---|---|
| 1 | `AI/` | 6 | YZ motoru, orkestrasyon, bilgi, hafıza, prompt, tool | var |
| 2 | `Api/` | 3 | Gateway, BFF, servis kaydı | var |
| 3 | `Auth/` | 3 | Kimlik, oturum, kullanıcı deposu | var |
| 4 | `Config/` | 1 | Yapılandırma yöneticisi | var |
| 5 | `Database/` | 2 | Yönetici + kayıt (registry) | var |
| 6 | `Events/` | 2 | Domain + integration event | var |
| 7 | `Middleware/` | 1 | PSR-vari pipeline elemanı | var |
| 8 | `Security/` | 1 | Rate limit | var |
| 9 | (kök) | 0 | Bağlam dosyası | var |

**Sayım kanıtı:** `shared/src/Contracts/CLAUDE.md` §2 der "Alt klasör 8" —
8 alt klasör + kök = 9 bağlam. Interface sayısı bu oturumda glob ile
**19** sayıldı; CLAUDE.md interface sayısı yazmaz → çelişki yok.

## §5 Sözleşme Kuralları — SSOT (S1, Contracts/CLAUDE.md §3)

| # | Kural | Metin (birebir) | Yorum |
|---|---|---|---|
| 1 | Interface segregation | "Her interface tek sorumluluk" | ISessionManager 21 metot = ihlal riski (§16 R2) |
| 2 | Dependency inversion | "High-level modül low-level'e bağımlı değil" | AuthService → `IUserRepository` (somut değil) |
| 3 | Contract-first | "Önce sözleşme, sonra implementasyon" | Dosya tarihleri: Contracts/CLAUDE.md 2026-09-21, taşınma 2026-09-29 |

**Ek kural (CLAUDE.md §2 notu):** AI/Auth/Config/Database/Middleware/Security
interface'leri "eski `src/Interfaces/` taşınması, 2026-09-29" — yani
`I`-prefix'li grubun kökeni klasik taşınma, `*Interface`'li grup (Api/Events/AI)
farklı bir yazım geleneğinden geliyor. İki yazımın birleştirme kararı → `UNKNOWN`.

## §6 Auth Kontratları — İmza Envanteri

### §6.1 IAuthService — 8 metot (S1: dosya 15 satır)

| # | İmza | Dönüş | AuthService karşılığı |
|---|---|---|---|
| 1 | `login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1')` | `array` | uygulanır (`:209`) |
| 2 | `register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral')` | `array` | uygulanır (`:215`) |
| 3 | `logout()` | `void` | uygulanır (`:221`) |
| 4 | `isAuthenticated()` | `bool` | uygulanır (`:235`) |
| 5 | `getCurrentUser()` | `?array` | uygulanır (`:240`) |
| 6 | `requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1')` | `array` | uygulanır (`:254`) |
| 7 | `resetPassword(string $token, string $newPassword)` | `array` | uygulanır (`:282`) |
| 8 | `validateSessionKey(string $authKey)` | `array` | uygulanır (`:310`) |

**Kontrat gözlemi:** kontratta `loginWithRequest`/`registerWithRequest`
**YOK** — DTO'lu metodlar arayüzün dışında (`AuthService:28` yalnız
`implements IAuthService`). Yani kontrat eski imzayı, implementasyon yeni
DTO yolunu da taşır. Eski imza korunur çünkü `IAuthService` onu şart koşar.

### §6.2 ISessionManager — 21 metot (S1: dosya 28 satır)

| # | Metot | Dönüş | Grup |
|---|---|---|---|
| 1 | `setAuthUser(array $user)` | `void` | oturum yaz |
| 2 | `setRegisteredUser(array $created)` | `void` | oturum yaz |
| 3 | `getUserId()` | `?string` | okuma |
| 4 | `isAuthenticated()` | `bool` | okuma |
| 5 | `destroy()` | `void` | yaşam döngüsü |
| 6 | `regenerateId()` | `void` | yaşam döngüsü |
| 7 | `get(string $key, mixed $default = null)` | `mixed` | genel KV |
| 8 | `set(string $key, mixed $value)` | `void` | genel KV |
| 9 | `remove(string $key)` | `void` | genel KV |
| 10 | `setGender(string $gender)` | `void` | cinsiyet |
| 11 | `getGender()` | `string` | cinsiyet |
| 12 | `setPendingRedirect(string $uri)` | `void` | yönlendirme |
| 13 | `consumePendingRedirect()` | `?string` | yönlendirme |
| 14 | `regenerateCspNonce()` | `string` | CSP |
| 15 | `getCspNonce()` | `string` | CSP |
| 16 | `isIdleExpired(int $timeoutSeconds)` | `bool` | yaşam döngüsü |
| 17 | `touch()` | `void` | yaşam döngüsü |
| 18 | `rotateIfNeeded(int $intervalSeconds)` | `bool` | yaşam döngüsü |
| 19 | `clearDisplayCookies()` | `void` | cookie |
| 20 | `getCookieParams()` | `array` | cookie |
| 21 | `all()` | `array` | okuma |

**Kontrat gözlemi:** 7. ve 8. metot (`get`/`set`) oturumu **genel KV**
haline açar — interface segregation kuralına (§5/1) en zayıf halka.

### §6.3 IUserRepository — 20 metot (S1: dosya 51 satır)

| # | Metot | Dönüş | AuthService çağrısı |
|---|---|---|---|
| 1 | `findByCredential(string)` | `?array` | var (`:75`) |
| 2 | `findByEmail(string)` | `?array` | var (`:263`) |
| 3 | `findByUsername(string)` | `?array` | çağrılmadı |
| 4 | `findByIdHex(string)` | `?array` | var (`:246`) |
| 5 | `create(array)` | `array` | var (`:177`) |
| 6 | `updateLastLogin(string)` | `void` | var (`:108`) |
| 7 | `emailExists(string)` | `bool` | var (`:152`) |
| 8 | `usernameExists(string)` | `bool` | var (`:146`) |
| 9 | `saveResetToken(string,string,string,?string)` | `void` | var (`:274`) |
| 10 | `findValidResetToken(string)` | `?array` | var (`:292`) |
| 11 | `updatePassword(string,string)` | `void` | var (`:302`) |
| 12 | `markResetTokenUsed(string)` | `void` | var (`:303`) |
| 13 | `saveAuthKey(string,string,string,?string)` | `void` | var (`:124`, `:191`) |
| 14 | `findValidAuthKey(string)` | `?array` | **2 argümanla** çağrılır (`:316`, `:319`) → §14 |
| 15 | `markAuthKeyUsed(string)` | `void` | var (`:336`) |
| 16 | `revokeAuthKeysForUser(string)` | `void` | var (`:227`) |
| 17 | `findRolesForUser(string)` | `array` | var (`:113`) |
| 18 | `saveAccessToken(string,string,string)` | `void` | çağrılmadı |
| 19 | `isValidAccessToken(string)` | `bool` | çağrılmadı |
| 20 | `revokeAccessTokensForUser(string)` | `void` | var (`:228`) |

**Sayım:** 20 imza · 18'i AuthService'te çağrılıyor · 2'si JWT tarafı
(`saveAccessToken`, `isValidAccessToken`) bu serviste kullanılmıyor
→ çağıran katman JWT yüzeyi ayrı, `⚠️ VERIFICATION REQUIRED`.

## §7 IRateLimiter — 3 metot (S1: dosya 10 satır)

| # | İmça | Dönüş | AuthService çağrı sayısı |
|---|---|---|---|
| 1 | `isLimited(string $key, int $maxAttempts, int $windowSeconds)` | `bool` | 4 (giriş ×2, kayıt ×1, sıfırlama ×1) |
| 2 | `increment(string $key, int $windowSeconds)` | `void` | 7 |
| 3 | `reset(string $key)` | `void` | 4 |

**Kontrat gözlemi:** kontrat **sayacın nerede durduğunu söylemez** (APCu /
dosya / DB) — somut sınıf `Security` altında olabilir ama okunmadı → `UNKNOWN`.
Sürücü dosyasındaki Q7 ile aynı kapı.

## §8 Api Kontratları — Gateway / BFF / Kayıt (S1)

| Interface | Metot | Dönüş | ADR referansı (dosya içi) |
|---|---|---|---|
| `GatewayInterface` | `dispatch(array $request)` | `array` | `@see ADR-084-api-gateway-architecture` |
| `BffInterface` | `transform(array $apiResponse, array $clientContext)` | `array` | `@see ADR-084-api-gateway-architecture` |
| `BffInterface` | `getClientType()` | `string` | `@see ADR-084-api-gateway-architecture` |
| `ServiceRegistryInterface` | `register(string $name, string $url, string $healthEndpoint = '/health')` | `void` | `@see ADR-084-api-gateway-architecture` |
| `ServiceRegistryInterface` | `discover(string $name)` | `array` | aynı |
| `ServiceRegistryInterface` | `healthCheck()` | `array` | aynı |
| `ServiceRegistryInterface` | `getHealthyServices()` | `array` | aynı |

**Kontrat gözlemi:** üçü de **tek dosyada `@see ADR-084`** taşıyor —
API katmanının kontratları bir ADR'ye bağlıdır; AI kontratları ise
`@version 1.0.0` + `@since 2026-09-01` taşır, ADR yok. İki yazım
geleneği → §13 imzasızlıkla aynı aile.

## §9 Events Kontratları — Domain ve Integration (S1)

| Interface | Metot | Dönüş | Fark |
|---|---|---|---|
| `DomainEventInterface` | `eventName()` | `string` | — |
| `DomainEventInterface` | `occurredOn()` | `\DateTimeImmutable` | — |
| `DomainEventInterface` | `payload()` | `array` | — |
| `IntegrationEventInterface` | `eventName()` | `string` | ortak |
| `IntegrationEventInterface` | `source()` | `string` | **yalnız integration** |
| `IntegrationEventInterface` | `payload()` | `array` | ortak |
| `IntegrationEventInterface` | `occurredOn()` | `\DateTimeImmutable` | ortak |

**Kontrat gözlemi:** integration = domain + `source()`. İkisi de tek bir
şeyi **eksik taşır**: kimin **kim için** dinlediğine dair routing alanı yok
(`consumer`/`topic`) → relay tasarımı başka katmanda → `⚠️ VERIFICATION REQUIRED`.
Her iki dosya da `@see ADR-086-event-driven-architecture`.

## §10 Database Kontratları (S1)

| Interface | Metot | Dönüş |
|---|---|---|
| `IDatabaseManager` | `execute(string $sql, array $params = [])` | `array` |
| `IDatabaseManager` | `write(string $sql, array $params = [])` | `bool` |
| `IDatabaseManager` | `lastInsertId()` | `string` |
| `IDatabaseManager` | `beginTransaction()` | `bool` |
| `IDatabaseManager` | `commit()` | `bool` |
| `IDatabaseManager` | `rollBack()` | `bool` |
| `IDatabaseRegistry` | `registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4')` | `void` |
| `IDatabaseRegistry` | `get(string $key)` | `IDatabaseManager` |
| `IDatabaseRegistry` | `has(string $key)` | `bool` |

**Kontrat gözlemi:** `registerMySql` **parola parametresi alır** (şifreli
metin) — kontrat metninde sızıntı yüzeyi; nereye yazıldığı → kontrat
seviyesinde `UNKNOWN`. Transaction üçlüsü (`begin/commit/rollBack`)
mevcut — user-service sürücüsündeki "transaction yok" gözlemi bu kontratın
**var olduğunu**, AuthService'in **çağırmadığını** gösterir (tutarlı).

## §11 Config / Middleware / Security Kontratları (S1)

| Interface | Metot sayısı | Öne çıkan imza |
|---|---|---|
| `IConfigManager` | 7 | `getSecure(string $key): string` — güvenli okuma ayrı metot |
| `IMiddleware` | 1 | `handle(array $request, callable $next): array` |
| `IRateLimiter` | 3 | §7 |

**Kontrat gözlemi:** `IMiddleware` **tek** metotlu — "her interface tek
sorumluluk" kuralına (§5/1) tek örtüşen örnek. `getSecure` ayrımı
(`IConfigManager`) sırları normal `get`'ten ayırır — iyi bölünme.

## §12 AI Kontratları — 6 Interface / 33 metot (S1)

| Interface | Metot | Örnek imza |
|---|---|---|
| `AIEngineInterface` | 5 | `getRecommendations(array $context, int $limit = 20): array` |
| `AIOrchestratorInterface` | 6 | `dispatchTask(string $type, array $params, string $priority = 'medium'): string` |
| `KnowledgeBaseInterface` | 5 | `ragQuery(string $query, array $context = []): array` |
| `MemorySystemInterface` | 7 | `remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed` |
| `PromptEngineInterface` | 5 | `validatePrompt(string $prompt): array` |
| `ToolCallingInterface` | 5 | `checkPermission(string $toolName, string $agentId): array` |

**Kontrat gözlemi:** AI grubu **PHPStan tarzı array şemaları** kullanır
(`array{success: bool, result: mixed, error: ?string, executionTime: float}`)
— Auth grubu düz `array` döner. İki tip disiplini aynı depoda yan yana.
Hangisi standart → `UNKNOWN`.

**Teknik kapı:** `ToolCallingInterface::checkPermission` kontratı
**izin kapısını tool katmanına** koyar; `IAuthService`/RBAC tarafı bunu
bilmez. İki izin sistemi (RBAC vs tool permission) nasıl birleşir →
`⚠️ VERIFICATION REQUIRED` (K012–K016 sınır sorusu ile ilgili).

## §13 Implementasyon Eşlemesi — Okunan Kadar (S1)

| Interface | Okunan implementasyon | Kanıt |
|---|---|---|
| `IAuthService` | `AuthService` (349 satır) | `AuthService.php:28 implements IAuthService` |
| `ISessionManager` | `SessionManager` (180 satır) | `SessionManager.php:8 implements ISessionManager` |
| `IUserRepository` | okunmadı | `auth.coremusic.net/include/Repository/UserRepository.php` envanterde, okunmadı |
| `IRateLimiter` | okunmadı | `use CoreMusic\Contracts\Security\IRateLimiter` (AuthService:16) |
| Diğer 15 interface | okunmadı | — |

**İsimlendirme gözlemi:** 19 kontrattan 9'u `I`-prefix (Auth 3, Config 1,
Database 2, Middleware 1, Security 1), 10'u `*Interface` soneki (AI 6, Api 3,
Events 2 = 11… sayım: AI 6 + Api 3 + Events 2 = 11, I-prefix = 8 → 11+8=19).
**Düzeltme:** I-prefix grubu = Auth 3 + Config 1 + Database 2 + Middleware 1 +
Security 1 = **8**; `*Interface` sonek grubu = AI 6 + Api 3 + Events 2 = **11**;
8 + 11 = 19 ✓. İki yazım geleneği; birleştirme kuralı `UNKNOWN`.

## §14 Kontrat–Çağrı Çelişkisi (Red Team — en yüksek kapı)

| # | İddia | Kontrat (S1) | Çağrı (S1) | Durum |
|---|---|---|---|---|
| 1 | `findValidAuthKey` parametre sayısı | **1 param**: `findValidAuthKey(string $authKey): ?array` | **2 argüman**: `findValidAuthKey($authKey, false)` / `($authKey, true)` | `AuthService:316,319` — `IUserRepository.php` 14. metot |
| 2 | Sonuç | PHP'de interface'e uyum denetimi çalışma anında **sınıf** üzerinden yapılır: somut `UserRepository` ikinci parametreyi **seçimsel** alıyorsa çalışır | doğrulanamadı | `⚠️ VERIFICATION REQUIRED` (somut sınıf okunmadı) |
| 3 | İkinci parametrenin anlamı | AuthService akışı: false = kullanılmamış, true = kullanılmış (§9 surucu) | akış çıkarımı | kod akışından çıkarım, SQL'i okunmadı |
| 4 | Statik analiz etkisi | PHPStan/LSP bu satırı **interface ihlali** sayabilir | analiz iddiası | LSP hatası bu vault'ta umursanmaz (kural), kontrat gerçeği yine de kayıtta |

**Karar:** çelişki **kayıtlıdır, kapatılmamıştır.** Somut sınıf okunmadan
"ihlal" veya "uyum" iddiası yazılmaz. Açık kapı: Q-A (bkz §19).

## §15 Kural ≠ Kanıt Ayrımı (bu dosyaya özgü)

| İddia | Seviye | Kaynak |
|---|---|---|
| "Her interface tek sorumluluk" | **Kural** | `Contracts/CLAUDE.md` §3 |
| `ISessionManager` 21 metot = geniş arayüz | **Kanıt** | dosya imza sayımı (§6.2) |
| "Contract-first" | **Kural** | aynı §3 |
| Dosyaların tarih sırası sözü tutuyor | **Kanıt yok** | commit tarihleri okunmadı → `UNKNOWN` |
| Argon2id (auth kontratı bağımlısı) | **Kural** (başka dosya) | `auth.coremusic.net/AGENTS.md` §4.3 — Password VO içi kanıt yok (user-service anayasa §6 ile aynı) |

## §16 Red Team Risk Listesi

| # | Risk | Kanıt | Ciddiyet |
|---|---|---|---|
| 1 | Kontrat-çağrı imza çelişkisi (`findValidAuthKey`) | §14 | Yüksek |
| 2 | `ISessionManager` 21 metot — segregation ihlal riski | §6.2 + §5/1 | Orta |
| 3 | `IConfigManager::getSecure` dışındaki her yer `get` ile sır okuyabilir mi | kontrat ayrımı var, çağıranlar okunmadı | Orta |
| 4 | İki izin sistemi (RBAC vs `ToolCallingInterface::checkPermission`) | §12 | Orta |
| 5 | İki yazım geleneği (`I` vs `*Interface`) + iki tip disiplini (düz `array` vs şema) | §13, §12 | Düşük |
| 6 | `IDatabaseRegistry::registerMySql` parola parametresi kontratta | §10 | Düşük (tasarım gerçeği) |
| 7 | `GatewayInterface::dispatch` tek metotlu — hata/kimlik/limit kontratı yok | §8 | Orta (gateway genişlerse kontrat yetmez) |

## §17 Kapsam Dışı — Bu Dosyanın Bilmediği Şeyler

| Konu | Neden yok |
|---|---|
| 17 interface implementasyonu | Okunmadı (yalnız 2'si eşlendi, §13) |
| DTO sınıfları (`LoginRequest`, `AuthResponse`) | `auth.coremusic.net/include/Domain/DTO/` — başka düğüm |
| Kontrat testleri | `shared/tests/` envanterde, okunmadı |
| `Contracts/CLAUDE.md` kök dışındaki 8 alt CLAUDE.md | yalnız kök + Auth okundu |
| ADR-084 / ADR-086 metinleri | `.ai/.decisions/` bu turda okunmadı — dosya içi `@see` yeterince kanıt |
| Kontratların sürümleme politikası | `@version 1.0.0` tekil örnek; politika dosyası yok → `UNKNOWN` |

## §18 Bağımlılık Özeti

| Bağımlı | Bağımlı olduğu | Tür |
|---|---|---|
| `AuthService` | `IAuthService`, `ISessionManager`, `IUserRepository`, `IRateLimiter` | import (AuthService:13–16) |
| `SessionManager` | `ISessionManager`, `SessionBootstrapper` | import (SessionManager:5–6) |
| Bu düğüm (anayasa) | `services-master.md` §21, `services-bagimliliklar.md` §1 | vault |
| Üst kademe (index) | `K012-services` | `parent_id` |
| Interface'lerin kendisi | dış bağımlılık **yok** (yalnız `\DateTimeImmutable` — PHP çekirdeği) | §9 |

**Gözlem:** 19 kontratın hiçbirinde CoreMusic sınıfı import etmemesi
beklenir; `\DateTimeImmutable` tek dış tiptir (Events 2 dosya).
Kontrat katmanı **bağımlılıksız** — doğru katman yönü.

## §19 Açık Sorular (Kalıcı)

| # | Soru | Kapı |
|---|---|---|
| Q-A | `findValidAuthKey` ikinci parametresi somut sınıfta var mı (§14) | Kontrat çelişkisi |
| Q-B | 17 interface implementasyonu hangi sınıflarda? | §13 |
| Q-C | AI kontratları (6) bu düğümün mü, K016'nın mı? | index açık sorusu ile aynı aile |
| Q-D | İki yazım geleneği (`I` vs `*Interface`) birleştirme kararı | §13 |
| Q-E | `GatewayInterface` genişleyecek mi (hata/kimlik kontratı)? | §16 R7 |
| Q-F | `ToolCallingInterface::checkPermission` RBAC ile nerede birleşir? | §12 |

## §20 Kesişim Tablosu

| Dosya | Kesişen konu | Bu dosyanın tutumu | Çakışma |
|---|---|---|---|
| `user-service/user-service-anayasa.md` | Auth kontratları, Argon2id kural≠kanıt | Aynı kapıları tekrarlar, §15'te bağlar | Uyumlu |
| `user-service/user-service-surucu.md` §18 | `pepper` enjeksiyon, I* çağrıları | Kontrat imzasını ben doğrularım | Uyumlu |
| `services-master.md` §21.4 | ai-service kontratları | AI 6 kontratın sahipliği Q-C'de açık | `⚠️ VERIFICATION REQUIRED` |
| `shared/src/Contracts/CLAUDE.md` §3 | 3 sözleşme kuralı | §5'te birebir alındı | Uyumlu |
| K013 (dış API) | public kontrat sınırı | index "dış API K013'te" der — bu dosya dokunmaz | Uyumlu |

## §21 Kod Bloğu — Sözleşmenin Kendisi (S1 birebir)

```php
// shared/src/Contracts/Middleware/IMiddleware.php (8 satır, tamamı)
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Middleware;

interface IMiddleware
{
    public function handle(array $request, callable $next): array;
}
```

```php
// shared/src/Contracts/Security/IRateLimiter.php — gövdesiz 3 imza
interface IRateLimiter
{
    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool;
    public function increment(string $key, int $windowSeconds): void;
    public function reset(string $key): void;
}
```

```php
// shared/src/Contracts/Api/GatewayInterface.php — ADR-084 bağlantılı tek metot
/**
 * @see ADR-084-api-gateway-architecture
 */
interface GatewayInterface
{
    /**
     * Dispatch an API request and return the response.
     *
     * @param array $request The incoming request data
     * @return array The response data
     */
    public function dispatch(array $request): array;
}
```

```php
// shared/src/Contracts/Events/IntegrationEventInterface.php — domain'e source() ekler
interface IntegrationEventInterface
{
    public function eventName(): string;
    public function source(): string;
    public function payload(): array;
    public function occurredOn(): \DateTimeImmutable;
}
```

## §22 Dönüş Tipi Dağılımı — 116 İmzada Tip Sıklığı

| Dönüş tipi | Tahmini sıklık | Örnek | Disiplin |
|---|---|---|---|
| `array` | en sık (Auth, Api, AI şemasız dönüşler) | `login(): array` | düz `array` |
| `void` | yazma metotları (repo, session) | `reset(): void` | — |
| `bool` | kapı/soru metotları | `isLimited(): bool` | — |
| `string` | ad/id/nonce dönenler | `getCspNonce(): string` | — |
| `?array` | koşullu okuma | `findByCredential(): ?array` | nullability |
| `?string` | koşullu okuma | `getUserId(): ?string` | nullability |
| `mixed` | genel KV | `get(): mixed` | geniş |
| `int` | sayım | `countTokens(): int` | — |
| `IDatabaseManager` | tek somut dönüş | `IDatabaseRegistry::get()` | — |
| `\DateTimeImmutable` | 2 event kontratı | `occurredOn()` | — |

**Gözlem (§12 ile aynı kapı):** AI kontratları dönüşü **şemalı array**
anlatır (`array{...}` DocBlock), Auth/Api düz `array` — tip disiplini
kontratlar arasında tek değildir.

## §23 Metot İsimlendirme Desenleri

| Desen | Kullanan kontrat | Örnek | Sıklık |
|---|---|---|---|
| `is*` / `has*` (soru) | IRateLimiter, IDatabaseRegistry, IConfigManager | `isLimited`, `has`, `isProduction` | 8 |
| `get*` (okuma) | ISessionManager, IConfigManager, AI grubu | `getUserId`, `getRecommendations` | 20+ |
| `set*` / `save*` (yazma) | ISessionManager, IUserRepository | `setAuthUser`, `saveAuthKey` | 15+ |
| `find*` (sorgu) | IUserRepository | `findByCredential`, `findValidResetToken` | 5 |
| `revoke*` (iptal) | IUserRepository | `revokeAuthKeysForUser` | 2 |
| `mark*` (damga) | IUserRepository | `markAuthKeyUsed`, `markResetTokenUsed` | 2 |
| `*Exists` (varlık) | IUserRepository | `emailExists`, `usernameExists` | 2 |
| `check*` / `validate*` | ToolCallingInterface, PromptEngineInterface | `checkPermission`, `validatePrompt` | 3 |
| `register*` / `discover*` | ServiceRegistryInterface, ToolCallingInterface | `register`, `discover` | 3 |

**Gözlem:** `find*` ile `get*` ayrımı (repo sorgusu vs okuma) tutarlı görünür;
ama `ISessionManager::get` ile `IUserRepository::find*` aynı anlamı farklı
söyler — iki kontrat ailesi farklı geleneğe ait (§13).

## §24 Sık Sorular (bu dosyaya özgü)

| # | Soru | Cevap |
|---|---|---|
| 1 | Kontrat dosyaları çalışır mı? | Hayır — gövdesiz; derlenir ama hiçbiri tek başına iş yapmaz |
| 2 | Neden interface'ler `auth.coremusic.net` içinde değil? | Ortak kullanım — `shared/src/Contracts/` 8 alt klasör (§4) |
| 3 | Kontrat değişirse ne olur? | Tüm `implements` eden sınıflar etkilenir — bu yüzden §14 gibi imza çelişkileri kayda girer |
| 4 | `mixed` kullanmak yasak mı? | Hayır — `ISessionManager::get` ve `MemorySystemInterface` `mixed` kullanır (kod kanıtı) |
| 5 | ADR referansı hangi kontratlarda var? | Api 3 dosya `ADR-084`, Events 2 dosya `ADR-086` (dosya içi `@see`) |
| 6 | Kontrat testleri bu düğümde mi? | Hayır — `shared/tests/` envanterde, okunmadı (§17) |
| 7 | `IMiddleware` neden tek metotlu? | Pipeline deseni: `handle($request, $next)` — birleşik çağrı (§11) |

**Ölçüm notu:** §3'teki satır sayıları dosyanın son satırı sayılmadan
(boş satır dahil edilmeden) 2026-10-13'te okunmuştur; imza sayıları
`public function` satırı sayımıdır.

## §25 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | anayasa yazıldı — 19 interface envanteri, 116 imza özeti, 9 bağlam, 3 sözleşme kuralı, `findValidAuthKey` imza çelişkisi (S1 birebir) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
