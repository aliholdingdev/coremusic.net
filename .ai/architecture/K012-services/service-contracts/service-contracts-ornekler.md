---
node_id: K012-services/service-contracts
node_type: module
role: ornekler
status: draft
parent_id: K012-services/service-contracts
children: []
depends_on:
  - "service-contracts/service-contracts-anayasa.md"
  - "service-contracts/service-contracts-surucu.md"
evidence:
  - "S1 — shared/src/Contracts/ 19 interface dosyası, 2026-10-13 tamamı/parçası okundu"
  - "S1 — auth.coremusic.net/include/Service/AuthService.php 349 satır (tamamı, 2026-10-13)"
  - "S1 — auth.coremusic.net/include/Service/SessionManager.php 180 satır (tamamı, 2026-10-13)"
validation:
  - "§2–§11 kod blokları birebir kopya — 2026-10-13 read"
  - "§12 yokluk desenleri — kod YOK alanları"
open_questions:
  - "AIOrchestrator/KnowledgeBase/MemorySystem/PromptEngine dosyaları yalnız imza satırları okundu (gövde/başlık değil) → ⚠️ VERIFICATION REQUIRED"
  - "17 kontratın implementasyonu okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: shared/src/Contracts/ (S1, 2026-10-13 read)"
group: kod
---

# service-contracts — Örnekler (Kod Kanıtı)

> **Bu dosya ne yapar:** kontratların **kendisini** birebir kopyalar.
> Interface gövdesizdir; her blok tam dosya ya da tam imza setidir.
> Yorum eklenmez, sadeleştirilmez.

## §1 Örnek Künye Tablosu

| # | Örnek | Dosya | Satır | Blok |
|---|---|---|---|---|
| 1 | `IMiddleware` — en küçük kontrat | `Contracts/Middleware/IMiddleware.php` | 1–8 | tamamı |
| 2 | `IRateLimiter` | `Contracts/Security/IRateLimiter.php` | 1–10 | tamamı |
| 3 | `IAuthService` | `Contracts/Auth/IAuthService.php` | 1–15 | tamamı |
| 4 | `ISessionManager` | `Contracts/Auth/ISessionManager.php` | 1–28 | tamamı |
| 5 | `IUserRepository` | `Contracts/Auth/IUserRepository.php` | 1–51 | tamamı |
| 6 | `IConfigManager` | `Contracts/Config/IConfigManager.php` | 1–14 | tamamı |
| 7 | `IDatabaseManager` + `IDatabaseRegistry` | `Contracts/Database/` | 13 + 10 | iki tam dosya |
| 8 | `GatewayInterface` + `BffInterface` | `Contracts/Api/` | 26 + 34 | iki tam dosya |
| 9 | `ServiceRegistryInterface` | `Contracts/Api/ServiceRegistryInterface.php` | 1–50 | tamamı |
| 10 | `DomainEventInterface` + `IntegrationEventInterface` | `Contracts/Events/` | 39 + 46 | iki tam dosya |
| 11 | `AIEngineInterface` + `ToolCallingInterface` | `Contracts/AI/` | 62 + 59 | iki tam dosya |
| 12 | AI imza seçkisi (4 dosya) | `Contracts/AI/` | — | imza satırları |

## §2 IMiddleware — 8 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Middleware/IMiddleware.php` · **Satır:** 1–8

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Middleware;

interface IMiddleware
{
    public function handle(array $request, callable $next): array;
}
```

## §3 IRateLimiter — 10 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Security/IRateLimiter.php` · **Satır:** 1–10

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Security;

interface IRateLimiter
{
    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool;
    public function increment(string $key, int $windowSeconds): void;
    public function reset(string $key): void;
}
```

## §4 IAuthService — 15 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Auth/IAuthService.php` · **Satır:** 1–15

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Auth;

interface IAuthService
{
    public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array;
    public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array;
    public function logout(): void;
    public function isAuthenticated(): bool;
    public function getCurrentUser(): ?array;
    public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array;
    public function resetPassword(string $token, string $newPassword): array;
    public function validateSessionKey(string $authKey): array;
}
```

## §5 ISessionManager — 28 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Auth/ISessionManager.php` · **Satır:** 1–28

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Auth;

interface ISessionManager
{
    public function setAuthUser(array $user): void;
    public function setRegisteredUser(array $created): void;
    public function getUserId(): ?string;
    public function isAuthenticated(): bool;
    public function destroy(): void;
    public function regenerateId(): void;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function remove(string $key): void;
    public function setGender(string $gender): void;
    public function getGender(): string;
    public function setPendingRedirect(string $uri): void;
    public function consumePendingRedirect(): ?string;
    public function regenerateCspNonce(): string;
    public function getCspNonce(): string;
    public function isIdleExpired(int $timeoutSeconds): bool;
    public function touch(): void;
    public function rotateIfNeeded(int $intervalSeconds): bool;
    public function clearDisplayCookies(): void;
    public function getCookieParams(): array;
    public function all(): array;
}
```

## §6 IUserRepository — 51 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Auth/IUserRepository.php` · **Satır:** 1–51

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Auth;

interface IUserRepository
{
    public function findByCredential(string $identity): ?array;
    public function findByEmail(string $email): ?array;
    public function findByUsername(string $username): ?array;
    public function findByIdHex(string $uuidHex): ?array;
    public function create(array $userData): array;
    public function updateLastLogin(string $userId): void;
    public function emailExists(string $email): bool;
    public function usernameExists(string $username): bool;
    public function saveResetToken(string $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void;
    public function findValidResetToken(string $tokenHash): ?array;
    public function updatePassword(string $userId, string $newPasswordHash): void;
    public function markResetTokenUsed(string $tokenId): void;
    public function saveAuthKey(string $userId, string $authKey, string $expiresAt, ?string $clientIp = null): void;
    public function findValidAuthKey(string $authKey): ?array;
    public function markAuthKeyUsed(string $tokenId): void;

    /**
     * Kullanıcının kullanılmamış tüm auth_key'lerini (token_type=api_key) iptal eder — logout (B-F-20).
     */
    public function revokeAuthKeysForUser(string $userId): void;

    /**
     * Kullanıcının aktif RBAC rollerini (rol adı + izin listesi) döndürür.
     *
     * @return list<array{role_name: string, permissions: list<string>}>
     */
    public function findRolesForUser(string $userId): array;

    /**
     * JWT access token jti'sini user_tokens'a kaydeder (token_hash = sha256(jti)).
     *
     * @param string $expiresAt 'Y-m-d H:i:s'
     */
    public function saveAccessToken(string $userId, string $jti, string $expiresAt): void;

    /**
     * jti hâlâ geçerli mi? (kayıt var, used_at IS NULL, süresi dolmamış) — revocation kontrolü.
     */
    public function isValidAccessToken(string $jti): bool;

    /**
     * Kullanıcının tüm geçerli access token'larını iptal eder (logout / şifre değişimi).
     */
    public function revokeAccessTokensForUser(string $userId): void;
}
```

**Örnek gözlem:** 14. satır `findValidAuthKey(string $authKey)` — **tek param**.
AuthService bunu 2 argümanla çağırır (`AuthService:316,319`).
Çelişki anayasa §14'te kayıtlıdır; burada kontrat tarafı birebir durur.

## §7 IConfigManager + IDatabase Kontratları

**Dosya:** `shared/src/Contracts/Config/IConfigManager.php` · **Satır:** 1–14

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Config;

interface IConfigManager
{
    public function get(string $key, mixed $default = null): mixed;
    public function getSecure(string $key): string;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function all(): array;
    public function isProduction(): bool;
    public function isDevelopment(): bool;
}
```

**Dosya:** `shared/src/Contracts/Database/IDatabaseManager.php` · **Satır:** 1–13

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

interface IDatabaseManager
{
    public function execute(string $sql, array $params = []): array;
    public function write(string $sql, array $params = []): bool;
    public function lastInsertId(): string;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
}
```

**Dosya:** `shared/src/Contracts/Database/IDatabaseRegistry.php` · **Satır:** 1–10

```php
<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

interface IDatabaseRegistry
{
    public function registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void;
    public function get(string $key): IDatabaseManager;
    public function has(string $key): bool;
}
```

## §8 GatewayInterface ve BffInterface (ADR-084)

**Dosya:** `shared/src/Contracts/Api/GatewayInterface.php` · **Satır:** 1–26

```php
<?php
declare(strict_types=1);

/**
 * Gateway interface for API request dispatching.
 *
 * @file GatewayInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for API Gateway request dispatching.
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

**Dosya:** `shared/src/Contracts/Api/BffInterface.php` · **Satır:** 1–34

```php
<?php
declare(strict_types=1);

/**
 * BFF (Backend for Frontend) interface for response transformation.
 *
 * @file BffInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for BFF response transformation.
 */
interface BffInterface
{
    /**
     * Transform API response for specific client type.
     *
     * @param array $apiResponse The original API response
     * @param array $clientContext Client-specific context
     * @return array Transformed response
     */
    public function transform(array $apiResponse, array $clientContext): array;

    /**
     * Get the client type this BFF handles.
     *
     * @return string Client type identifier
     */
    public function getClientType(): string;
}
```

## §9 ServiceRegistryInterface — 50 Satır (tam dosya)

**Dosya:** `shared/src/Contracts/Api/ServiceRegistryInterface.php` · **Satır:** 1–50

```php
<?php
declare(strict_types=1);

/**
 * Service Registry interface for service discovery and health checks.
 *
 * @file ServiceRegistryInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for service registry management.
 */
interface ServiceRegistryInterface
{
    /**
     * Register a service.
     *
     * @param string $name Service name
     * @param string $url Service URL
     * @param string $healthEndpoint Health check endpoint
     * @return void
     */
    public function register(string $name, string $url, string $healthEndpoint = '/health'): void;

    /**
     * Discover a service by name.
     *
     * @param string $name Service name
     * @return array Service details
     */
    public function discover(string $name): array;

    /**
     * Check health of all registered services.
     *
     * @return array Health status of all services
     */
    public function healthCheck(): array;

    /**
     * Get list of healthy services.
     *
     * @return array List of healthy services
     */
    public function getHealthyServices(): array;
}
```

## §10 Events Kontratları — Domain ve Integration

**Dosya:** `shared/src/Contracts/Events/DomainEventInterface.php` · **Satır:** 1–39

```php
<?php
declare(strict_types=1);

/**
 * Domain Event interface for event-driven architecture.
 *
 * @file DomainEventInterface.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Contracts\Events;

/**
 * Interface for domain events.
 */
interface DomainEventInterface
{
    /**
     * Get the event name.
     *
     * @return string Event name
     */
    public function eventName(): string;

    /**
     * Get the timestamp when the event occurred.
     *
     * @return \DateTimeImmutable Event timestamp
     */
    public function occurredOn(): \DateTimeImmutable;

    /**
     * Get the event payload data.
     *
     * @return array Event payload
     */
    public function payload(): array;
}
```

**Dosya:** `shared/src/Contracts/Events/IntegrationEventInterface.php` · **Satır:** 1–46

```php
<?php
declare(strict_types=1);

/**
 * Integration Event interface for cross-service communication.
 *
 * @file IntegrationEventInterface.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Contracts\Events;

/**
 * Interface for integration events.
 */
interface IntegrationEventInterface
{
    /**
     * Get the event name.
     *
     * @return string Event name
     */
    public function eventName(): string;

    /**
     * Get the source service that originated the event.
     *
     * @return string Source service name
     */
    public function source(): string;

    /**
     * Get the event payload data.
     *
     * @return array Event payload
     */
    public function payload(): array;

    /**
     * Get the timestamp when the event occurred.
     *
     * @return \DateTimeImmutable Event timestamp
     */
    public function occurredOn(): \DateTimeImmutable;
}
```

**Örnek gözlem:** iki dosya arasındaki tek fark `source()` — domain →
integration genişlemesi 1 metottur (anayasa §9).

## §11 AI Kontratları — AIEngineInterface ve ToolCallingInterface

**Dosya:** `shared/src/Contracts/AI/AIEngineInterface.php` · **Satır:** 1–62

```php
<?php declare(strict_types=1);
/**
 * CoreMusic — AI Engine Interface
 * 
 * Ana AI işleme motoru arayüzü.
 * Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction.
 *
 * @package CoreMusic\Contracts\AI
 * @version 1.0.0
 * @since   2026-09-01
 */

namespace CoreMusic\Contracts\AI;

/**
 * AI Engine ana arayüzü.
 * Tüm AI işleme modülleri bu arayüzü implemente eder.
 */
interface AIEngineInterface
{
    /**
     * Müzik önerisi üretir.
     *
     * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
     * @param int                  $limit   Maksimum öneri sayısı
     * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
     */
    public function getRecommendations(array $context, int $limit = 20): array;

    /**
     * Ses dosyasını analiz eder (BPM, key, energy, mood).
     *
     * @param string $filePath Ses dosyası yolu
     * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
     */
    public function analyzeAudio(string $filePath): array;

    /**
     * Otomatik EQ ayarları üretir.
     *
     * @param array<string, mixed> $roomProfile Ortam profili (frekans, reverb, boyut)
     * @return array<int, array{band: int, frequency: float, gain: float, q: float}>
     */
    public function optimizeEQ(array $roomProfile): array;

    /**
     * Donanım performansını analiz eder.
     *
     * @param string $deviceId Cihaz ID'si
     * @return array{status: string, snr: float, thd: float, temperature: float, recommendations: array<int, string>}
     */
    public function analyzeHardware(string $deviceId): array;

    /**
     * Hata tahmini yapar.
     *
     * @param array<string, mixed> $metrics Cihaz metrikleri
     * @return array{risk: string, probability: float, alerts: array<int, string>, maintenance: array<int, string>}
     */
    public function predictFault(array $metrics): array;
}
```

**Dosya:** `shared/src/Contracts/AI/ToolCallingInterface.php` · **Satır:** 1–59

```php
<?php declare(strict_types=1);
/**
 * CoreMusic — Tool Calling Interface
 * 
 * Dış servis ve araç çağrısı arayüzü.
 *
 * @package CoreMusic\Contracts\AI
 * @version 1.0.0
 */

namespace CoreMusic\Contracts\AI;

interface ToolCallingInterface
{
    /**
     * Tool kaydı yapar.
     *
     * @param string $name        Tool adı
     * @param string $description Tool açıklaması
     * @param array<int, array{name: string, type: string, required: bool, description: string}> $parameters Parametre tanımları
     * @param callable $executor  Çalıştırıcı fonksiyon
     * @return bool Başarı durumu
     */
    public function registerTool(string $name, string $description, array $parameters, callable $executor): bool;

    /**
     * Tool çağrısı yapar.
     *
     * @param string $toolName   Tool adı
     * @param array<string, mixed> $params Parametreler
     * @return array{success: bool, result: mixed, error: ?string, executionTime: float}
     */
    public function callTool(string $toolName, array $params): array;

    /**
     * Kayıtlı tool'ları listeler.
     *
     * @return array<string, array{name: string, description: string, parameters: array<int, array{name: string, type: string, required: bool, description: string}>, category: string}>
     */
    public function listTools(): array;

    /**
     * Tool kategorisi filtreler.
     *
     * @param string $category Kategori (file, database, api, audio, hardware, security)
     * @return array<string, array{name: string, description: string}>
     */
    public function getToolsByCategory(string $category): array;

    /**
     * Permission kontrolü yapar.
     *
     * @param string $toolName Tool adı
     * @param string $agentId  Agent ID'si
     * @return array{allowed: bool, reason: ?string}
     */
    public function checkPermission(string $toolName, string $agentId): array;
}
```

## §12 AI İmza Seçkisi — 4 Dosya (yalnız `public function` satırları)

**Dosya:** `shared/src/Contracts/AI/AIOrchestratorInterface.php` (67 satır) · imzalar:

```php
public function dispatchTask(string $type, array $params, string $priority = 'medium'): string;
public function getTaskStatus(string $taskId): array;
public function listActiveTasks(): array;
public function routeToAgent(string $agentId, string $action, array $params): array;
public function getContext(string $sessionId): array;
public function updateContext(string $sessionId, array $data): bool;
```

**Dosya:** `shared/src/Contracts/AI/KnowledgeBaseInterface.php` (60 satır) · imzalar:

```php
public function search(string $query, int $maxResults = 10, array $filters = []): array;
public function ragQuery(string $query, array $context = []): array;
public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string;
public function removeKnowledge(string $id): bool;
public function cleanupLifecycle(): array;
```

**Dosya:** `shared/src/Contracts/AI/MemorySystemInterface.php` (79 satır) · imzalar:

```php
public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool;
public function getSession(string $sessionId, string $key): mixed;
public function clearSession(string $sessionId): bool;
public function setPersistent(string $key, mixed $value, int $ttl = 0): bool;
public function getPersistent(string $key): mixed;
public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed;
public function getHierarchy(): array;
```

**Dosya:** `shared/src/Contracts/AI/PromptEngineInterface.php` (57 satır) · imzalar:

```php
public function generatePrompt(string $templateName, array $variables, array $context = []): array;
public function countTokens(string $text): int;
public function optimizePrompt(string $prompt, int $maxTokens): array;
public function listTemplates(): array;
public function validatePrompt(string $prompt): array;
```

**Seçki notu:** bu dört dosyanın başlık/başlık-yorumu okunmadı; yalnız
`public function` satırları ölçüldü (2026-10-13) → başlık iddiası
`⚠️ VERIFICATION REQUIRED`, imza satırları birebir.

## §13 Örnek Analiz Tabloları

| # | Örnek | Gözlem | Kanıt |
|---|---|---|---|
| 1 | `IMiddleware` tek metot | segregation kuralının tek tam örneği | dosya 8 satır |
| 2 | `IAuthService` eski imza korunuyor | `login` 4 parametreli eski stil; DTO metodları interface'de yok | §4 + AuthService:209 |
| 3 | `IUserRepository` DocBlock'lu 4 metot | Türkçe yorumlar, tip şemaları (`list<array{...}>`) | §6 blok içi |
| 4 | `findValidAuthKey` tek param | AuthService 2 argüman çağırır | §6 + AuthService:316 |
| 5 | `IConfigManager::getSecure` ayrımı | sır okusu `get`'ten ayrı metot | §7 |
| 6 | `IDatabaseRegistry::registerMySql` parola param | kontratta parola geçişi | §7 |
| 7 | `GatewayInterface` tek metot | hata/kimlik/limit kontratı yok | §8 |
| 8 | `BffInterface::getClientType` | BFF kendini tanıtır (routing verisi) | §8 |
| 9 | Events: `source()` farkı | integration = domain + source | §10 |
| 10 | AI: `array{...}` şeması | PHPStan tarzı dönüş şeması — Auth'ta yok | §11 |
| 11 | `ToolCallingInterface::checkPermission` | ikinci izin sistemi (RBAC dışı) | §11 |
| 12 | `MemorySystemInterface::remember` | `callable` parametre — tek callable taşıyan kontrat | §12 |
| 13 | `AIOrchestratorInterface::routeToAgent` | agent yönlendirme kontratta | §12 |
| 14 | `KnowledgeBaseInterface::ragQuery` | RAG kontrat düzeyinde | §12 |
| 15 | ADR `@see` yalnız Api/Events | AI/Auth/Config/Database/Middleware/Security'te ADR yok | dosya başlıkları |

## §14 Yokluk Desenleri (Kod YOK Alanları)

| Alan | Durum | Kaynak |
|---|---|---|
| 17 kontratın implementasyonu | Okunmadı | anayasa §17 Q-B |
| `AuthContainer` DI bağlaması | Okunmadı | `auth.coremusic.net/AGENTS.md` §2 |
| `shared/tests/` kontrat testleri | Okunmadı | envanter (QA satırı) |
| 4 AI dosyanın başlık yorumu | Okunmadı | §12 — yalnız imzalar ölçüldü |
| Somut `UserRepository` SQL (2. parametre dahil) | Okunmadı | anayasa §14 |
| ADR-084 / ADR-086 metinleri | Okunmadı | `.ai/.decisions/` — dosya içi `@see` kanıt |
| Kontrat sürümleme politikası | Yok | `UNKNOWN` |
| `MiddlewareInterface` (auth tarafı) | Okunmadı | `auth.coremusic.net/AGENTS.md` §2 envanteri |

## §15 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | örnekler yazıldı — 11 tam dosya bloğu + 4 imza seçkisi + 15 analiz + 8 yokluk deseni (S1 birebir) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
