---
node_id: K008-domain/kod-ornekleri
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-bilesenler.md
evidence:
  - shared/src/Events/Domain/MusicPlayedEvent.php
  - shared/src/Events/Domain/PlaylistCreatedEvent.php
  - shared/src/Events/EventDispatcher.php
  - shared/src/Repository/ApiKeyRepository.php
  - shared/src/AI/AIEngine.php
validation: 2026-10-12 — her kod parçası diskten okundu; Dosya + Satır kanıtı var
open_questions:
  - "Kod, K3 purizm kuralına %100 uyumlu mu? (ApiKeyRepository persistence içerir — katman tartışması açık)"
group: kod
---

# K008-domain — Kod Örnekleri (Domain Code Evidence)

> **Authority: Bayram Ali / Vault Steward.** Bu dosya **gerçek, diskteki** PHP
> parçalarını Domain katmanının kanıtı olarak taşır. Kural: her örnek
> `Dosya: … / Satır: …` biçiminde; uydurma kod yasak (master §9-C7).
> Çözümleme ve yorum ayrı sütunda; kod değiştirilmez.

## §1 ÖRNEK ENVANTERİ

| # | Dosya | Ne gösteriyor | K008 bağlantısı |
|---|---|---|---|
| Ö1 | `shared/src/Events/Domain/MusicPlayedEvent.php` | Domain event sözleşmesi (sonuç) | master §3.3 |
| Ö2 | `shared/src/Events/Domain/PlaylistCreatedEvent.php` | Aynı sözleşmenin ikinci uygulaması | master §3.3 |
| Ö3 | `shared/src/Events/EventDispatcher.php` | PSR-14 yayımı (öncelik + durdurulabilir) | bagimliliklar §3.2 |
| Ö4 | `shared/src/Repository/ApiKeyRepository.php` | Repository + domain kuralı (ham anahtar yasak) | bilesenler §3.3 |
| Ö5 | `shared/src/AI/AIEngine.php` | Öneri eşiği domain kuralı (K5, CONFLICT C2) | kararlar §6 |

## §2 Ö1 — DOMAIN EVENT: `MusicPlayedEvent`

**Dosya:** `shared/src/Events/Domain/MusicPlayedEvent.php`

```php
// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 1–2
<?php
declare(strict_types=1);

// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 12–15
namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 20–32
final class MusicPlayedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $userId,
        private readonly int $duration
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }
```

```php
// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 37–71
    public function eventName(): string
    {
        return 'music.played';
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function payload(): array
    {
        return [
            'musicId' => $this->musicId,
            'userId' => $this->userId,
            'duration' => $this->duration,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }
```

**Çözümleme:**

| Gözlem | Yorum | K008 kuralı |
|---|---|---|
| `strict_types=1` (s.2) | Dil standardı gereği zorunlu | Global §5 Dil standartları |
| `final class` + `readonly` (s.20–24) | Değiştirilemez event; immutable domain | master §3.2-A4 |
| `DomainEventInterface` (s.14, s.20) | Sözleşme arayüzü `CoreMusic\Contracts\` altında | master §3.3 |
| `StoppableEventTrait` (s.15, s.22) | Durdurulabilir olay (PSR-14 `StoppableEventInterface`) | Ö3 |
| `eventName()` = `'music.played'` (s.39) | Adlandırma `{nesne}.{olay}` (nokta biçimi) | evrensel-dil §6 |
| `payload()` + `format('c')` (s.57–65) | RFC 3339 zaman damgası | evrensel-dil §5 |
| Sınıf PHP doküman yorumunda `@see ADR-086-event-driven-architecture` | Karar referansı kodda gömülü | kararlar §5 (K4 ile ilişkili) |

## §3 Ö2 — `PlaylistCreatedEvent` (aynı sözleşmenin ikinci uygulaması)

**Dosya:** `shared/src/Events/Domain/PlaylistCreatedEvent.php` (keşif: s.20 sınıf, s.39 payload alanı `playlistId`/`userId`)

**Çözümleme:** İki event arasındaki tek fark taşıdığı **veri** (`musicId`+`userId`+`duration`
↔ `playlistId`+`userId`); iskelet (`final`, `readonly`, `occurredOn`, `payload`, `eventName`)
**değişmez**. Bu, `domain-master.md` §3.3 sözleşmesinin ikinci uygulaması olarak
**şablon** işlevi görür: yeni event eklerken bu iki dosya referans alınır.

## §4 Ö3 — PSR-14 DISPATCHER (yayım mekanizması)

**Dosya:** `shared/src/Events/EventDispatcher.php`

```php
// Dosya: shared/src/Events/EventDispatcher.php · Satır: 14–20
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event Dispatcher implementation following PSR-14.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sortedListeners = [];

// Dosya: shared/src/Events/EventDispatcher.php · Satır: 28–45
    public function dispatch(object $event): object
    {
        $eventName = get_class($event);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        foreach ($this->getListeners($eventName) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }

// Dosya: shared/src/Events/EventDispatcher.php · Satır: 50–57, 109–117
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    { /* öncelik ile kayıt, s.50 */ }

    private function sortListeners(string $eventName): array
    { /* usort($b['priority'] <=> $a['priority']), s.109–117 */ }
```

**Çözümleme:**

| Gözlem | Yorum | K008 bağlantısı |
|---|---|---|
| `get_class($event)` (s.30) | Dinleyici anahtarı = sınıf adı (event adı değil) | Karar: `eventName()` yalnız meta |
| `StoppableEventInterface` + `break` (s.37–39) | Durdurulabilir olay yayımı desteklenir | PSR-14 uyumu |
| `priority` + `usort` (s.50, s.109) | Öncelik sıralı dinleyici zinciri | Çocuklarda dinleyici sırası test stratejisinde |
| Senkron `dispatch()` | Async/kuyruk bu sınıfın işi değil | kararlar §5 (K4) |

## §5 Ö4 — REPOSITORY + DOMAIN KURALI: `ApiKeyRepository`

**Dosya:** `shared/src/Repository/ApiKeyRepository.php`

```php
// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 10–14
namespace CoreMusic\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 26–38 (docblock özet)
// Şema (canlı, migration tamam):
//   id binary(16) PK | user_id binary(16) NOT NULL | key_hash UNIQUE |
//   key_prefix varchar(8) | key_name | scopes JSON |
//   key_type ENUM('user','service','server') | rate_limit int DEFAULT 1000 | ...
// Doğrulama akışı (ADR-020 §2.2-1):
//   prefix index taraması →' hash_equals(key_hash, sha256(raw)) →'
//   is_active=1 AND is_deleted=0 AND (expires_at IS NULL OR expires_at > now) →'
//   IP allowlist (varsa) →' scope →' last_used_at güncelle
// Güvenlik: ham anahtar ASLA DB'ye yazılmaz (yalnız SHA-256 hash + prefix)
// ADR-002: PDO prepared statement; SELECT * yasak; ORM yasak.
final class ApiKeyRepository
{
    public const DB_KEY = 'auth';

    /** Ham anahtar deseni: cm_live_<8-char prefix>_<random> (ADR-020 §2.1). */
    public const RAW_KEY_PATTERN = '/^(cm_live|cm_test)_([A-Za-z0-9]{8})_([A-Za-z0-9\-_]{16,})$/';

    public const KEY_TYPES = ['user', 'service', 'server'];
```

**Çözümleme:**

| Gözlem | Yorum | K008 bağlantısı |
|---|---|---|
| `IDatabaseRegistry` / `IDatabaseManager` (s.12–13) | **Arayüz** enjeksiyonu — concrete PDO değil | bagimliliklar §4-Y1 ile uyumlu sınır |
| `public const KEY_TYPES` (s.37) | Enum benzeri domain sabiti (`user`/`service`/`server`) | `domain-dogrulama.md` §7 (enum karşılığı) |
| `RAW_KEY_PATTERN` regex (s.32) | Ham anahtar biçimi domain kuralıdır | evrensel-dil §5 (API Key) |
| Docblock: `SELECT * yasak; ORM yasak` (ADR-002) | SQL kuralı kod yorumunda | data katmanı sahipliğinde; domain yalnız okur |
| `hash_equals(key_hash, sha256(raw))` doğrulama akışı | Zamanlama-saldırı dirençli karşılaştırma | security katmanı ile ortak dil |
| ADR-086 / ADR-020 / ADR-002 referansları | Kod içi ADR gömme deseni | kararlar §8 protokolü |

## §6 Ö5 — ÖNERİ EŞİĞİ: `AIEngine` (K5 — CONFLICT C2)

**Dosya:** `shared/src/AI/AIEngine.php`

```php
// Dosya: shared/src/AI/AIEngine.php · Satır: 28, 30–32, 49
final class AIEngine
{
    private const MAX_RECOMMENDATIONS = 20;   // s.30
    private const MIN_SCORE = 0.7;            // s.31
    private const DIVERSITY_TARGET = 0.30;    // s.32

    public function getRecommendations(...)  // s.49
```

**Çözümleme:**

| Gözlem | Yorum | K008 bağlantısı |
|---|---|---|
| Üç eşik sabiti `private const` | Öneri **domain kuralı** sayılır (eşik, çeşitlilik, tavan) | kararlar §6 (K5) |
| `AIEngine` uygulama sınıfı (K016/kod sahipli) | K008 **kanıt** olarak kullanır, sahipliğini üstlenmez | CONFLICT C2 |
| Eşik değerleri (20, 0.7, 0.30) | `recommendation` çocuğunun `-dogrulama.md`'sine işlenecek | bilesenler §3.8 |

## §7 YENİ ÖRNEK EKLEME PROTOKOLÜ

| Adım | Kural |
|---|---|
| 1 | Kod önce diskten okunur; `Get-Content` ile satır sayısı doğrulanır |
| 2 | Her kod bloğu üstünde `// Dosya: … · Satır: …` yorumu zorunlu |
| 3 | Çözümleme tablosu: gözlem · yorum · K008 kuralı bağlantısı |
| 4 | Kod bloğu **değiştirilmez** (uzunluk kırpma serbest, içerik değil) |
| 5 | Yeni örnek önce çocuk `-ornekler.md`'e; köke yalnız **ortak** desen girer |

---

*Authority: Bayram Ali / Vault Steward · K008-domain kod örnekleri · 2026-10-12 · 5 dosya · satır kanıtlı*
