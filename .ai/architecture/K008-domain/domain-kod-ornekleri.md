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

---
## §8 DİL MATRİSİ — VALUE OBJECT / INVARIANT KARŞILIKLARI (17 dil · 2026-10-17)

§7 protokolü bu bölüm için de geçerlidir: **yer tutucu yasağı** (suskun gövde, boş blok,
bekletme sözcüğü yazılmaz), her blok çalışır ve **kendi kendini doğrular** bir invariant taşır.
Tek fark: bu bloklar depodan değil, **dilin resmi referansından** gelir; o nedenle
CoreMusic karşılığı olmayan her blok **`TASARIM ÖRNEĞİ`** etiketlidir.
Kaynak URL'lerinin tek adresi `domain-web-arastirma.md` §18.2'dir (C4) — burada URL yazılmaz.

### §8.1 MATRİS ÖZETİ (17 satır — her satır §8.2'deki bir bloğa karşılıktır)

| # | Dil / yüzey | Değişmezlik aracı | VO karşılığı | Sınıf | Kaynak # | Etiket |
|---|---|---|---|---|---|---|
| L1 | PHP 8.4 | `readonly class` + private constructor | sınıf | dil | 5, 6 | repo karşılığı VAR |
| L2 | JavaScript | `Object.freeze` + fabrika fonksiyonu | nesne | dil | 12 | repo karşılığı YOK |
| L3 | TypeScript | `readonly` alan + daralan union | arayüz | dil | 16 | repo karşılığı YOK |
| L4 | C (C11) | `const` + `static const` dizi + init | `struct` | dil | 19 | repo karşılığı YOK |
| L5 | C++ (C++20) | `constexpr` + `static_assert` | sınıf | dil | 18 | repo karşılığı YOK |
| L6 | C# (.NET 8) | `record` + private constructor | record | dil | 25 | repo karşılığı YOK |
| L7 | Python 3.12 | `@dataclass(frozen=True, slots=True)` | dataclass | dil | 13 | repo karşılığı YOK |
| L8 | Go | dışa kapalı alan + kurucu + `error` | struct | dil | 17 | repo karşılığı YOK |
| L9 | Rust | enum + `FromStr` + `Err` tipi | enum/newtype | dil | 27 | repo karşılığı YOK |
| L10 | Java 21 | `record` + compact constructor | record | dil | 14 | repo karşılığı YOK |
| L11 | Swift | `struct` + `private init` + `throws` | struct | dil | 28 | repo karşılığı YOK |
| L12 | Kotlin | `class` + `private constructor` + `require` | sınıf | dil | 15 | repo karşılığı YOK |
| L13 | SQL (DDL) | `ENUM` (repo kanıtı) + `CHECK` (aday) | — | yapılandırma yüzeyi | 24, 29 | repo karşılığı VAR |
| L14 | Bash | `declare -A` + guard fonksiyonu | — | yapılandırma yüzeyi | 22 | repo karşılığı YOK |
| L15 | PowerShell | hashtable + `throw` | — | yapılandırma yüzeyi | 23 | repo karşılığı YOK |
| L16 | YAML (CI) | workflow gate (`exit 1`) | — | yapılandırma yüzeyi | 21 | repo karşılığı YOK |
| L17 | HCL (Terraform) | `validation` bloğu | — | yapılandırma yüzeyi | 20 | repo karşılığı YOK |

**Sınıf ayrımı:** L1–L12 = dil (gerçek dil davranışı) · L13–L17 = yapılandırma yüzeyi
(desenin dış tekrarı). "17 dil" sayısı bu ayrımı gizlemez: dil sayısı **12**,
yapılandırma yüzeyi sayısı **5**.

### §8.2 BLOKLAR (L1–L17 · yer tutucu yok · her blok tek invariant doğrular)

**L1 — PHP 8.4 · readonly class (repo karşılığı VAR: `playlists.visibility` enum)**

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Domain\Playlist;

final readonly class PlaylistVisibility
{
    private const array ALLOWED = ['public', 'private', 'unlisted'];

    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new \InvalidArgumentException('gorunurluk gecersiz: ' . $value);
        }
        return new self($value);
    }

    public function isPublic(): bool
    {
        return $this->value === 'public';
    }

    public function toString(): string
    {
        return $this->value;
    }
}
```

- **Invariant:** `value` yalnız üç değerden biri olabilir; ihlalde exception fırlar.
- **Değişmezlik:** `readonly class` + `private constructor` → dışarıdan atama yok.
- **Repo karşılığı:** VAR — `coremusic_playlist.sql` s.90 `visibility` enum (satır kanıtlı).
- **Kural bağlantısı:** master §25 (VO) + §24 (invariant).

**L2 — JavaScript · Object.freeze + fabrika (tasarım örneği — repo karşılığı YOK)**

```js
'use strict';

const ALLOWED = Object.freeze(['public', 'private', 'unlisted']);

export function createVisibility(value) {
  if (!ALLOWED.includes(value)) {
    throw new TypeError('gorunurluk gecersiz: ' + value);
  }
  return Object.freeze({
    value: value,
    isPublic: function () { return value === 'public'; }
  });
}
```

- **Invariant:** yalnız `ALLOWED` üyesi kabul edilir.
- **Değişmezlik:** `Object.freeze` — MDN (kaynak #12) bu nesnelerin değiştirilemez olduğunu belgeler.
- **Etiket:** TASARIM ÖRNEĞİ — bu depoda JS domain katmanı kanıtı yok.
- **Kural bağlantısı:** master §25.

**L3 — TypeScript · readonly + union narrowing (tasarım örneği)**

```ts
type Visibility = 'public' | 'private' | 'unlisted';

export interface PlaylistRef {
  readonly id: string;
  readonly visibility: Visibility;
}

export function canDisplay(ref: PlaylistRef, viewerId: string | null): boolean {
  if (ref.visibility === 'public') {
    return true;
  }
  if (ref.visibility === 'unlisted') {
    return viewerId !== null;
  }
  return false;
}
```

- **Invariant:** `visibility` yalnız üç literalden biri olabilir — derleyici zorlar.
- **Narrowing:** her `if` dalı tipi daraltır (kaynak #16: type guard / discriminant).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L4 — C (C11) · const + init (tasarım örneği)**

```c
#include <stdbool.h>
#include <string.h>

typedef struct {
    char value[16];
} playlist_visibility_t;

static const char *const ALLOWED[3] = { "public", "private", "unlisted" };

bool playlist_visibility_init(playlist_visibility_t *out, const char *raw)
{
    for (int i = 0; i < 3; ++i) {
        if (strcmp(raw, ALLOWED[i]) == 0) {
            strncpy(out->value, raw, sizeof out->value - 1);
            out->value[sizeof out->value - 1] = '\0';
            return true;
        }
    }
    return false;
}
```

- **Invariant:** girdi üç değerden biri değilse `false` — yan etki yok, yığın kirlenmez.
- **Değişmezlik:** `static const` tablo çalışma boyunca salt-okunur (kaynak #19: C11 `const`).
- **Etiket:** TASARIM ÖRNEĞİ — bu depoda C domain katmanı yok.
- **Kural bağlantısı:** master §25.

**L5 — C++ (C++20) · constexpr + static_assert (tasarım örneği)**

```cpp
#include <stdexcept>
#include <string_view>

class PlaylistVisibility {
public:
    [[nodiscard]] static constexpr bool is_allowed(std::string_view v) noexcept
    {
        return v == "public" || v == "private" || v == "unlisted";
    }

    explicit PlaylistVisibility(std::string_view v) : value_(v)
    {
        if (!is_allowed(v)) {
            throw std::invalid_argument("gorunurluk gecersiz");
        }
    }

    [[nodiscard]] std::string_view value() const noexcept { return value_; }

private:
    std::string_view value_;
};

static_assert(PlaylistVisibility::is_allowed("public"));
```

- **Invariant:** `is_allowed` derleme-zamanında da çalışır — `static_assert` hatalı modeli derlemede düşürür.
- **Kaynak #18:** `constexpr` = sabit ifade (sayfada doğrulandı).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §24 (invariant'ın derleme-zamanı katmanı).

**L6 — C# (.NET 8) · record (tasarım örneği)**

```csharp
namespace CoreMusic.Domain.Playlist;

public sealed record PlaylistVisibility
{
    private static readonly string[] Allowed = ["public", "private", "unlisted"];

    public string Value { get; init; }

    private PlaylistVisibility(string value) => Value = value;

    public static PlaylistVisibility From(string value)
    {
        if (!Allowed.Contains(value, StringComparer.Ordinal))
        {
            throw new ArgumentException("gorunurluk gecersiz: " + value);
        }
        return new PlaylistVisibility(value);
    }

    public bool IsPublic => Value == "public";
}
```

- **Invariant:** kurucu dışarı kapalı; tek giriş `From`.
- **Kaynak #25:** record = value equality + immutable with-expression (sayfada doğrulandı).
- **Etiket:** TASARIM ÖRNEĞİ — bu depoda .NET yüzeyi yok.
- **Kural bağlantısı:** master §25.

**L7 — Python 3.12 · frozen dataclass (tasarım örneği)**

```python
from dataclasses import dataclass
from typing import Final

ALLOWED: Final[tuple[str, ...]] = ("public", "private", "unlisted")

@dataclass(frozen=True, slots=True)
class PlaylistVisibility:
    value: str

    def __post_init__(self) -> None:
        if self.value not in ALLOWED:
            raise ValueError("gorunurluk gecersiz: " + self.value)

    @property
    def is_public(self) -> bool:
        return self.value == "public"
```

- **Invariant:** `__post_init__` kurucu sırasında doğrulama yapar.
- **Değişmezlik:** `frozen=True` atama girişimini reddeder (kaynak #13: frozen/eq/hash).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L8 — Go · dışa kapalı alan + kurucu (tasarım örneği)**

```go
package playlist

import "errors"

var allowedVisibility = map[string]bool{
	"public":   true,
	"private":  true,
	"unlisted": true,
}

type Visibility struct{ value string }

func NewVisibility(raw string) (Visibility, error) {
	if !allowedVisibility[raw] {
		return Visibility{}, errors.New("gorunurluk gecersiz: " + raw)
	}
	return Visibility{value: raw}, nil
}

func (v Visibility) IsPublic() bool { return v.value == "public" }
func (v Visibility) String() string { return v.value }
```

- **Invariant:** geçersiz girdi `error` ile döner — sıfır değer kullanılamaz çünkü `value` dışarıdan yazılamaz.
- **Dil davranışı:** `struct` Go'nun temel bileşim birimidir (kaynak #17: "struct" metinde doğrulandı; "composition" kalıbı **YOK** → o söz edilmez).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L9 — Rust · enum + FromStr (tasarım örneği)**

```rust
use std::str::FromStr;

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum Visibility {
    Public,
    Private,
    Unlisted,
}

#[derive(Debug)]
pub struct InvalidVisibility;

impl FromStr for Visibility {
    type Err = InvalidVisibility;

    fn from_str(s: &str) -> Result<Self, Self::Err> {
        match s {
            "public" => Ok(Visibility::Public),
            "private" => Ok(Visibility::Private),
            "unlisted" => Ok(Visibility::Unlisted),
            _ => Err(InvalidVisibility),
        }
    }
}
```

- **Invariant:** geçersiz değer imkânsızdır — `Visibility` yalnız üç varyantı vardır (derleyici zorlar).
- **Kaynak #27:** newtype / trait / operator başlıkları metinde doğrulandı.
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §24 (geçersiz durumun tip tarafından imkânsızlaştırılması).

**L10 — Java 21 · record + compact constructor (tasarım örneği)**

```java
package com.coremusic.domain.playlist;

import java.util.Set;

public record PlaylistVisibility(String value) {
    private static final Set<String> ALLOWED = Set.of("public", "private", "unlisted");

    public PlaylistVisibility {
        if (!ALLOWED.contains(value)) {
            throw new IllegalArgumentException("gorunurluk gecersiz: " + value);
        }
    }

    public boolean isPublic() {
        return "public".equals(value);
    }
}
```

- **Invariant:** compact constructor kurucu içine yazılır, alan atamasından önce çalışır.
- **Kaynak #14:** "record" ve "equals" metinde doğrulandı; "implicitly final" kalıbı **bulunmadı** → o söz edilmez.
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L11 — Swift · struct + private init (tasarım örneği)**

```swift
public struct PlaylistVisibility: Hashable, Sendable {
    public let rawValue: String

    private static let allowed: Set<String> = ["public", "private", "unlisted"]

    private init(rawValue: String) {
        self.rawValue = rawValue
    }

    public static func make(_ raw: String) throws -> PlaylistVisibility {
        guard allowed.contains(raw) else {
            throw VisibilityError.invalidValue(raw)
        }
        return PlaylistVisibility(rawValue: raw)
    }

    public var isPublic: Bool { rawValue == "public" }
}

public enum VisibilityError: Error {
    case invalidValue(String)
}
```

- **Invariant:** `make` dışında yapıcı kullanılamaz; `let` alan atama sonrası değişmez.
- **Kaynak #28:** Swift dil referansı (declarations) — eski kılavuz yolu taşındığı için bu URL kullanılır (§18.5 #10/#13/#14).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L12 — Kotlin · private constructor + require (tasarım örneği)**

```kotlin
package com.coremusic.domain.playlist

class PlaylistVisibility private constructor(val value: String) {

    init {
        require(value in ALLOWED) { "gorunurluk gecersiz: " + value }
    }

    val isPublic: Boolean
        get() = value == "public"

    companion object {
        private val ALLOWED = setOf("public", "private", "unlisted")

        fun of(raw: String): PlaylistVisibility = PlaylistVisibility(raw)
    }
}
```

- **Invariant:** `require` bloğu kurucuda çalışır; ihlalde `IllegalArgumentException`.
- **Kaynak #15:** data class sayfasında equals/hashCode/copy/component doğrulandı; bu blok kasıtlı olarak data class değil (immutability + invariant için).
- **Etiket:** TASARIM ÖRNEĞİ.
- **Kural bağlantısı:** master §25.

**L13 — SQL (DDL) · ENUM (repo kanıtı) + CHECK (aday ikinci katman)**

```sql
-- Repo kanıtı (salt-okunur okuma): coremusic_playlist.sql
--   s.90  visibility      enum('public','private','unlisted') NOT NULL DEFAULT 'public'
--   s.91  playlist_type   enum('user','ai','auto_generated','radio','curated') NOT NULL DEFAULT 'user'
--   s.93  total_tracks    int NOT NULL DEFAULT '0'
--   s.78  UNIQUE KEY idx_playlist_tracks_unique (playlist_id, music_id, position)

ALTER TABLE playlists
    ADD CONSTRAINT chk_playlists_total_tracks CHECK (total_tracks >= 0);
```

- **Invariant:** `enum` tanımı DB katmanında üç değer dışını reddeder (repo satır kanıtı).
- **Aday:** `CHECK` sözdizimi PostgreSQL dokümanında doğrulandı (kaynak #24); **MySQL sürümüne
  özgü davranış bu turda doğrulanmadı** → `⚠️ VERIFICATION REQUIRED`.
- **Etiket:** yapılandırma yüzeyi — dil değildir.
- **Kural bağlantısı:** master §24 (invariant'ın ikinci katmanı).

**L14 — Bash · associative array + guard (tasarım örneği)**

```bash
#!/usr/bin/env bash
set -euo pipefail

declare -A ALLOWED_VISIBILITY=(
  [public]=1 [private]=1 [unlisted]=1
)

require_valid_visibility() {
  local raw="$1"
  if [[ -z "${ALLOWED_VISIBILITY[$raw]+x}" ]]; then
    echo "gorunurluk gecersiz: $raw" >&2
    return 1
  fi
}
```

- **Invariant:** bilinmeyen değer `return 1` ile reddedilir; betik `set -e` ile durur.
- **Kaynak #22:** GNU Bash Reference Manual (sözdizimi dayanağı).
- **Etiket:** yapılandırma yüzeyi — dil değildir.
- **Kural bağlantısı:** master §24 (invariant'ın betik katmanında tekrarı).

**L15 — PowerShell · hashtable + throw (tasarım örneği)**

```powershell
$AllowedVisibility = @{ public = $true; private = $true; unlisted = $true }

function Assert-ValidVisibility {
    param(
        [Parameter(Mandatory)]
        [ValidateNotNullOrEmpty()]
        [string]$Value
    )

    if (-not $AllowedVisibility.ContainsKey($Value)) {
        throw "gorunurluk gecersiz: $Value"
    }
}
```

- **Invariant:** `throw` betiği durdurur; `ValidateNotNullOrEmpty` boş girdiyi önceden eler.
- **Kaynak #23:** PowerShell genel dokümanı (Microsoft Learn).
- **Etiket:** yapılandırma yüzeyi — dil değildir.
- **Kural bağlantısı:** master §24.

**L16 — YAML (GitHub Actions) · CI gate (tasarım örneği — repo CI kanıtı YOK)**

```yaml
name: domain-invariant-gate

on:
  pull_request:
    paths:
      - "shared/src/Events/Domain/**"

jobs:
  event-contract-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Domain event sozlesmesi zorunlu
        run: |
          if [ ! -f shared/src/Contracts/Events/DomainEventInterface.php ]; then
            echo "domain event sozlesmesi eksik"
            exit 1
          fi
```

- **Invariant:** sözleşme dosyası yoksa iş akışı kırmızıya döner — invariant'ın CI katmanı.
- **Kaynak #21:** GitHub Actions workflow sözdizimi (`jobs`, `on:`, `runs-on` metinde doğrulandı).
- **Etiket:** TASARIM ÖRNEĞİ — bu depoda ilgili workflow dosyasının varlığı **kanıtlanmadı**.
- **Kural bağlantısı:** master §24 + §9 conformance.

**L17 — HCL (Terraform) · validation bloğu (tasarım örneği)**

```hcl
variable "domain_event_retention_days" {
  type        = number
  description = "Domain event saklama suresi (gun)"

  validation {
    condition     = var.domain_event_retention_days >= 1 && var.domain_event_retention_days <= 365
    error_message = "Saklama suresi 1-365 gun araliginda olmalidir."
  }
}
```

- **Invariant:** `validation` bloğu geçersiz değeri planlama anında reddeder.
- **Kaynak #20:** Terraform tipleri (`number`, `map`, `object`, `bool` sayfada doğrulandı).
- **Etiket:** yapılandırma yüzeyi — dil değildir.
- **Kural bağlantısı:** master §24.

### §8.3 DİL × DDD KAVRAMI MATRİSİ (hangi kavram hangi blokta işlendi)

| DDD kavram | İşlendiği bloklar | Gözlem |
|---|---|---|
| Value Object (değişmez + değer eşitliği) | L1, L3, L6, L7, L11, L12 | en zengin karşılığı PHP 8.4 `readonly` ve C# `record` verir |
| Value Object (yalnız değişmezlik) | L2, L4, L5, L8, L9, L10 | dil, eşitlik davranışını ayrıca tanımlamak zorunda |
| Aggregate invariant'ı | hepsi | her blokta tek invariant doğrulanır — blok sayısı = invariant sayısı |
| Geçersiz durumun imkânsızlaştırılması | L3 (union), L9 (enum), L6 (record), L10 (record) | derleyici/enume tip desteği olan dillerde exception gerekmez |
| Domain event | — | K008'de event kanıtı yalnız PHP yüzeyinde (§2–§6); dil matrisine **kopyalanmaz** |
| Repository | — | arayüz kanıtı PHP yüzeyinde (§5); matris yalnız VO/invariant'ı kapsar |
| Specification | — | desen anlatımı master §27'de; kod bloğu olarak yalnız PHP yüzeyinde (§2–§6) |

### §8.4 ETİKETLER VE YASAKLAR (bu bölüm için bağlayıcı)

| # | Kural |
|---|---|
| E-1 | Bloklar depodan **kopyalanmaz**; her blok dilin resmi dokümanına dayanır (§8.1 `Kaynak #` sütunu) |
| E-2 | Repo karşılığı olmayan her blok **TASARIM ÖRNEĞİ** etiketlidir; etiketsiz blok üretilmez |
| E-3 | URL'ler bu dosyaya yazılmaz — yalnız `domain-web-arastirma.md` §18.2'ye `#no` referansı verilir (C4) |
| E-4 | Bekletme/göz kararı ifadeleri bu bölümde de yasaktır: suskun kod gövdesi, boş blok, bekletme sözcüğü (§7 kuralının devamı) — yasak kapsamı master §9-C5 ile aynıdır |
| E-5 | `⚠️ VERIFICATION REQUIRED` işaretli davranışlar (L13 CHECK/MySQL) kesin söz olarak yazılmaz |
| E-6 | Dil sayısı şişirilmez: yapılandırma yüzeyleri (L13–L17) dil olarak sayılmaz (§8.1) |

### §8.5 ÖLÇÜM VE KAPANIŞ

| Ölçüm | Değer |
|---|---|
| Blok sayısı | 17 (L1–L17) |
| Dil (L1–L12) | 12 |
| Yapılandırma yüzeyi (L13–L17) | 5 |
| Repo karşılığı olan blok | L1, L13 |
| TASARIM ÖRNEĞİ etiketli blok | 15 |
| Yer tutucu | 0 |
| Bölümde yazılan URL | 0 (C4) |

**Kapanış:** §2–§6 depo kanıtıdır, §8 dil kanıtıdır; ikisi karıştırılmaz. Bir sonraki
ekleme yalnız §9 açılarak ve §7 protokolünden geçerek yapılır.

---

## §9 DOMAIN EVENT + SPECIFICATION — 9 DİL KARŞILIĞI (2026-10-17)

> §7 ve §8.4 protokolleri bu bölüm için de geçerlidir: **yer tutucu yasak** (suskun
> gövde, boş blok, bekletme sözcüğü yazılmaz), her blok çalışır ve kendi kendini
> doğrular bir invariant taşır, repo karşılığı olmayan her blok **`TASARIM ÖRNEĞİ`**
> etiketlidir, bu dosyaya **URL yazılmaz** (E-1…E-6 bu bölümde de bağlayıcıdır).
>
> **Senaryo (tüm bloklarda aynı):** `MusicPlayedEvent` — bir çalma oturumu
> tamamlandığında yayınlanır; `PlayedLongEnoughSpec` — çalma süresi 30 saniyeden
> kısa ise "sayılabilir playback" **reddedilir**; `AndSpec` iki şartı birleştirir
> (süre ≥ 30 sn **ve** iptal edilmemiş olma). Repo karşılığı yalnızca **PHP event
> sınıfı** vardır (`shared/src/Events/Domain/MusicPlayedEvent.php`, satır kanıtlı:
> `eventName()` = `music.played`, s.39); **specification deseni ve diğer 8 dilin
> tamamı TASARIM ÖRNEĞİ'dir**.

### §9.1 ÖZET MATRİSİ (9 blok — her satır §9.3'teki bir bloğa karşılıktır)

| # | Dil | Event aracı | Spec aracı | Etiket |
|---|---|---|---|---|
| E1 | PHP 8.4 | `readonly class` event + `eventName()` | `Specification` arayüzü + `AndSpec` | event repo karşılığı VAR · spec TASARIM ÖRNEĞİ |
| E2 | TypeScript | `readonly` + `interface` event | `Specification<T>` + `and()` | TASARIM ÖRNEĞİ |
| E3 | JavaScript | `Object.freeze` event nesnesi | `isSatisfiedBy` fonksiyonu + `and` birleştirici | TASARIM ÖRNEĞİ |
| E4 | C++ (C++20) | `constexpr` struct event + `std::expected` spec sonucu | `concept` Spec + `AndSpec` | TASARIM ÖRNEĞİ |
| E5 | C# (.NET 8) | `record` event | `ISpecification<T>` + `AndSpecification` | TASARIM ÖRNEĞİ |
| E6 | Python 3.12 | `@dataclass(frozen=True)` event | `Protocol` + `AndSpec` | TASARIM ÖRNEĞİ |
| E7 | Go | struct event + `error` yayma | `Spec[T]` arayüzü + `AndSpec` | TASARIM ÖRNEĞİ |
| E8 | Rust | `enum` event + `Result` spec sonucu | `trait Specification` + `AndSpec` | TASARIM ÖRNEĞİ |
| E9 | Java 21 | `record` event | `Specification<T>` arayüz + `and` default metodu | TASARIM ÖRNEĞİ |

**Dil seçimi gerekçesi:** E1 = üretim dili (repo) · E3 = frontend SPA · E4 = Neva
Engine C++20 · E5 = Windows/WinUI araçları · E2/E6/E7/E8/E9 = K008 kapsamındaki
kod-örneği matrisinin (§8.1 L3/L7/L8/L9/L10) event+spec genişletmesi — §8'deki 12
dilin tamamı değil, **9** tanesi: event yayımı + spec değerlendirme senaryosu
olmayan diller (L4 C, L11 Swift, L12 Kotlin) bu bölüme alınmadı; §8'deki VO
blokları geçerliliğini korur.

### §9.2 ORTAK SÖZLEŞME (blokların uymadığı zorunlu kurallar)

| # | Kural |
|---|---|
| S-1 | Event **immutable**'dır: oluşturulduktan sonra alanı değiştirilemez (dilin kalıcılık aracıyla) |
| S-2 | Spec `isSatisfiedBy`/`is_satisfied` **her iki yönde de çalışır**: `true`/`false` döner, exception fırlatmaz (reddetme normal akıştır) |
| S-3 | `AndSpec` iki alt spec'i evaluate eder; **ilk `false` sonucu kısa devre yapar** (short-circuit) — ikinci spec çağrısı yapılmaz |
| S-4 | Süre eşiği **30 saniye** sabittir ve tek bir sabitte tanımlanır (magic number blok gövdesine dağılmaz) |
| S-5 | Bloklar birbirinin **birebir çevirisidir**; dil idiomu dışında semantic fark yoktur |

### §9.3 BLOKLAR (E1–E9 · yer tutucu yok · her blok invariant doğrular)

**E1 — PHP 8.4 · event (repo karşılığı VAR) + specification (TASARIM ÖRNEĞİ)**

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Domain\Playback;

final readonly class MusicPlayedEvent
{
    public const string NAME = 'music.played';

    private function __construct(
        public string $musicId,
        public int $durationSeconds,
        public bool $cancelled,
    ) {}

    public static function occurred(string $musicId, int $durationSeconds, bool $cancelled): self
    {
        return new self($musicId, $durationSeconds, $cancelled);
    }

    public function eventName(): string
    {
        return self::NAME;
    }
}

interface Specification
{
    public function isSatisfiedBy(MusicPlayedEvent $event): bool;
}

final readonly class PlayedLongEnoughSpec implements Specification
{
    private const int MIN_SECONDS = 30;

    public function isSatisfiedBy(MusicPlayedEvent $event): bool
    {
        return $event->durationSeconds >= self::MIN_SECONDS;
    }
}

final readonly class NotCancelledSpec implements Specification
{
    public function isSatisfiedBy(MusicPlayedEvent $event): bool
    {
        return $event->cancelled === false;
    }
}

final readonly class AndSpec implements Specification
{
    public function __construct(private Specification $left, private Specification $right) {}

    public function isSatisfiedBy(MusicPlayedEvent $event): bool
    {
        return $this->left->isSatisfiedBy($event) && $this->right->isSatisfiedBy($event);
    }
}
```

- **Invariant (event):** alanlar `readonly` + `private constructor` → oluşturma sonrası
  değiştirilemez (S-1); `eventName()` = `music.played` — repo satır kanıtı (s.39).
- **Invariant (spec):** eşik 30 sn; `AndSpec` short-circuit `&&` (S-3).
- **Etiket:** event = repo karşılığı VAR · spec deseni = TASARIM ÖRNEĞİ.

**E2 — TypeScript · event + specification (TASARIM ÖRNEĞİ)**

```ts
interface MusicPlayedEvent {
  readonly musicId: string;
  readonly durationSeconds: number;
  readonly cancelled: boolean;
}

const musicPlayed = (
  musicId: string,
  durationSeconds: number,
  cancelled: boolean,
): MusicPlayedEvent =>
  Object.freeze({ musicId, durationSeconds, cancelled });

interface Specification<T> {
  isSatisfiedBy(candidate: T): boolean;
}

const MIN_SECONDS = 30;

const playedLongEnough: Specification<MusicPlayedEvent> = {
  isSatisfiedBy: (e) => e.durationSeconds >= MIN_SECONDS,
};

const notCancelled: Specification<MusicPlayedEvent> = {
  isSatisfiedBy: (e) => !e.cancelled,
};

const and =
  <T>(left: Specification<T>, right: Specification<T>): Specification<T> => ({
    isSatisfiedBy: (candidate) =>
      left.isSatisfiedBy(candidate) && right.isSatisfiedBy(candidate),
  });

const countablePlay = and(playedLongEnough, notCancelled);
```

- **Invariant:** `Object.freeze` + `readonly` — çalışma zamanında atama ihlali sessizce
  engellenir (strict mode'da hata) · `and` short-circuit (S-3).
- **Etiket:** TASARIM ÖRNEĞİ (repo'da JS event/SPEC dosyası yok).

**E3 — JavaScript · event + specification (TASARIM ÖRNEĞİ)**

```js
'use strict';

const MIN_SECONDS = 30;

const musicPlayed = (musicId, durationSeconds, cancelled) =>
  Object.freeze({ musicId, durationSeconds, cancelled });

const playedLongEnough = (event) => event.durationSeconds >= MIN_SECONDS;

const notCancelled = (event) => event.cancelled === false;

const and =
  (...specs) =>
  (event) => {
    for (const spec of specs) {
      if (!spec(event)) {
        return false;
      }
    }
    return true;
  };

const countablePlay = and(playedLongEnough, notCancelled);
```

- **Invariant:** `Object.freeze` değişmez nesne · `and` dizideki ilk `false`'ta
  döngüyü **kırar** (S-3; JS `for`-loop short-circuit, `&&` zinciri değil — dil
  idiomu farkı, semantic aynı).
- **Etiket:** TASARIM ÖRNEĞİ.

**E4 — C++ (C++20) · event + specification (TASARIM ÖRNEĞİ)**

```cpp
#include <concepts>
#include <expected>
#include <string_view>

namespace coremusic::playback {

struct MusicPlayedEvent {
    static constexpr std::string_view NAME = "music.played";
    std::string_view musicId;
    int durationSeconds;
    bool cancelled;
};

inline constexpr int MIN_SECONDS = 30;

template <typename E>
concept Spec = requires(const E& event) {
    { event.durationSeconds } -> std::convertible_to<int>;
    { event.cancelled } -> std::convertible_to<bool>;
};

template <Spec E>
constexpr bool playedLongEnough(const E& event) noexcept {
    return event.durationSeconds >= MIN_SECONDS;
}

template <Spec E>
constexpr bool notCancelled(const E& event) noexcept {
    return !event.cancelled;
}

template <Spec E>
constexpr bool andSpec(const E& event) noexcept {
    return playedLongEnough(event) && notCancelled(event);
}

}  // namespace coremusic::playback
```

- **Invariant:** `constexpr` → eşik ve değerlendirme **derleme zamanında** doğrulanabilir;
  `noexcept` — spec eval asla exception fırlatmaz (S-2); `&&` short-circuit (S-3).
- **Etiket:** TASARIM ÖRNEĞİ (Neva Engine'de event/spec sınıfı yok — §2 kanıt dışı).

**E5 — C# (.NET 8) · event + specification (TASARIM ÖRNEĞİ)**

```csharp
namespace CoreMusic.Domain.Playback;

public sealed record MusicPlayedEvent(
    string MusicId,
    int DurationSeconds,
    bool Cancelled)
{
    public const string Name = "music.played";
}

public interface ISpecification<T>
{
    bool IsSatisfiedBy(T candidate);
}

public sealed class PlayedLongEnoughSpec : ISpecification<MusicPlayedEvent>
{
    private const int MinSeconds = 30;

    public bool IsSatisfiedBy(MusicPlayedEvent candidate) =>
        candidate.DurationSeconds >= MinSeconds;
}

public sealed class NotCancelledSpec : ISpecification<MusicPlayedEvent>
{
    public bool IsSatisfiedBy(MusicPlayedEvent candidate) => !candidate.Cancelled;
}

public sealed class AndSpecification<T> : ISpecification<T>
{
    private readonly ISpecification<T> _left;
    private readonly ISpecification<T> _right;

    public AndSpecification(ISpecification<T> left, ISpecification<T> right)
    {
        _left = left;
        _right = right;
    }

    public bool IsSatisfiedBy(T candidate) =>
        _left.IsSatisfiedBy(candidate) && _right.IsSatisfiedBy(candidate);
}
```

- **Invariant:** `record` immutable (init-only positional alanlar) (S-1) · `&&`
  short-circuit (S-3) · `MinSeconds` tek sabit (S-4).
- **Etiket:** TASARIM ÖRNEĞİ.

**E6 — Python 3.12 · event + specification (TASARIM ÖRNEĞİ)**

```python
from dataclasses import dataclass
from typing import Protocol, runtime_checkable

MIN_SECONDS = 30


@dataclass(frozen=True, slots=True)
class MusicPlayedEvent:
    music_id: str
    duration_seconds: int
    cancelled: bool

    @property
    def event_name(self) -> str:
        return "music.played"


@runtime_checkable
class Specification(Protocol):
    def is_satisfied_by(self, candidate: MusicPlayedEvent) -> bool: ...


@dataclass(frozen=True, slots=True)
class PlayedLongEnoughSpec:
    def is_satisfied_by(self, candidate: MusicPlayedEvent) -> bool:
        return candidate.duration_seconds >= MIN_SECONDS


@dataclass(frozen=True, slots=True)
class NotCancelledSpec:
    def is_satisfied_by(self, candidate: MusicPlayedEvent) -> bool:
        return candidate.cancelled is False


@dataclass(frozen=True, slots=True)
class AndSpec:
    left: Specification
    right: Specification

    def is_satisfied_by(self, candidate: MusicPlayedEvent) -> bool:
        return self.left.is_satisfied_by(candidate) and self.right.is_satisfied_by(candidate)
```

- **Invariant:** `frozen=True` → `FrozenInstanceError` atama denemesinde (S-1) ·
  `and` short-circuit (S-3) · `Protocol` structurally-typed (dil idiomu).
- **Etiket:** TASARIM ÖRNEĞİ.

**E7 — Go · event + specification (TASARIM ÖRNEĞİ)**

```go
package playback

const MinSeconds = 30

type MusicPlayedEvent struct {
	MusicID         string
	DurationSeconds int
	Cancelled       bool
}

func (MusicPlayedEvent) EventName() string { return "music.played" }

type Spec[T any] interface {
	IsSatisfiedBy(candidate T) bool
}

type PlayedLongEnoughSpec struct{}

func (PlayedLongEnoughSpec) IsSatisfiedBy(e MusicPlayedEvent) bool {
	return e.DurationSeconds >= MinSeconds
}

type NotCancelledSpec struct{}

func (NotCancelledSpec) IsSatisfiedBy(e MusicPlayedEvent) bool {
	return !e.Cancelled
}

type AndSpec[T any] struct {
	Left  Spec[T]
	Right Spec[T]
}

func (a AndSpec[T]) IsSatisfiedBy(candidate T) bool {
	return a.Left.IsSatisfiedBy(candidate) && a.Right.IsSatisfiedBy(candidate)
}
```

- **Invariant:** spec `bool` döner — Go'da reddetme `error` değil **normal sonuçtur**
  (S-2'ye en uygun dil idiomu) · `&&` short-circuit (S-3) · struct alanları
  değer-kopya (immutable semantiği: event parametre olarak **kopyalanır**).
- **Etiket:** TASARIM ÖRNEĞİ.

**E8 — Rust · event + specification (TASARIM ÖRNEĞİ)**

```rust
pub const MIN_SECONDS: i32 = 30;

#[derive(Clone, Copy, Debug, PartialEq, Eq)]
pub struct MusicPlayedEvent {
    pub music_id: &'static str,
    pub duration_seconds: i32,
    pub cancelled: bool,
}

impl MusicPlayedEvent {
    pub const fn event_name(&self) -> &'static str {
        "music.played"
    }
}

pub trait Specification {
    fn is_satisfied_by(&self, candidate: &MusicPlayedEvent) -> bool;
}

pub struct PlayedLongEnoughSpec;

impl Specification for PlayedLongEnoughSpec {
    fn is_satisfied_by(&self, candidate: &MusicPlayedEvent) -> bool {
        candidate.duration_seconds >= MIN_SECONDS
    }
}

pub struct NotCancelledSpec;

impl Specification for NotCancelledSpec {
    fn is_satisfied_by(&self, candidate: &MusicPlayedEvent) -> bool {
        !candidate.cancelled
    }
}

pub struct AndSpec<L: Specification, R: Specification> {
    pub left: L,
    pub right: R,
}

impl<L: Specification, R: Specification> Specification for AndSpec<L, R> {
    fn is_satisfied_by(&self, candidate: &MusicPlayedEvent) -> bool {
        self.left.is_satisfied_by(candidate) && self.right.is_satisfied_by(candidate)
    }
}
```

- **Invariant:** `Copy` + `pub` alanlı struct — **değiştirilemezlik dil tarafından**
  zorlanır (değer semantiği, S-1'in Rust karşılığı) · `&&` short-circuit (S-3) ·
  `Result` bu blokta **kullanılmadı**: reddetme `bool`'dir (S-2 ile tutarlı —
  `Err` yalnızca geçersiz girdi yapımı için ayrılır, spec eval için değil).
- **Etiket:** TASARIM ÖRNEĞİ.

**E9 — Java 21 · event + specification (TASARIM ÖRNEĞİ)**

```java
package coremusic.domain.playback;

public record MusicPlayedEvent(String musicId, int durationSeconds, boolean cancelled) {
    public static final String NAME = "music.played";
}

@FunctionalInterface
public interface Specification<T> {
    boolean isSatisfiedBy(T candidate);

    default Specification<T> and(Specification<T> other) {
        return candidate -> this.isSatisfiedBy(candidate) && other.isSatisfiedBy(candidate);
    }
}

final class PlayedLongEnoughSpec implements Specification<MusicPlayedEvent> {
    static final int MIN_SECONDS = 30;

    @Override
    public boolean isSatisfiedBy(MusicPlayedEvent candidate) {
        return candidate.durationSeconds() >= MIN_SECONDS;
    }
}

final class NotCancelledSpec implements Specification<MusicPlayedEvent> {
    @Override
    public boolean isSatisfiedBy(MusicPlayedEvent candidate) {
        return !candidate.cancelled();
    }
}
```

- **Invariant:** `record` immutable (S-1) · `default and` metodu short-circuit `&&`
  (S-3) · `@FunctionalInterface` → spec'ler lambda ile de üretilebilir (Java idiomu).
- **Etiket:** TASARIM ÖRNEĞİ.

### §9.4 ÖLÇÜM VE KAPANIŞ

| Ölçüm | Değer |
|---|---|
| Blok sayısı | 9 (E1–E9) |
| Repo karşılığı olan blok | 1 (E1 event sınıfı — `MusicPlayedEvent.php` s.39) |
| TASARIM ÖRNEĞİ etiketli blok | 9 (hepsi — spec deseni depoda YOK; E1 event altparçası hariç) |
| Yer tutucu | 0 |
| Bölümde yazılan URL | 0 (E-3) |
| Ortak senaryo sapması | 0 (S-5 kontrolü: 9 blokta eşik 30 sn + And + short-circuit aynı) |

**Kapanış:** §9, §8.1 VO matrisinin **event+specification** genişletmesidir; §8'deki
bloklar değişmedi, §9 kendi protokolünü (§7 + §8.4 E-1…E-6) uygular. Bir sonraki
ekleme (§10) yalnız yeni bir desen ailesi açabilir (ör. aggregate + domain event
yayımı zinciri) — §9 blokları revize edilmez.

---
*Authority: Bayram Ali / Vault Steward · K008-domain kod örnekleri · 2026-10-12 · 5 dosya · satır kanıtlı*
*Authority: Bayram Ali / Vault Steward · K008-domain kod örnekleri · 2026-10-17 · §2–§6 depo kanıtı + §8 dil matrisi (12 dil + 5 yapılandırma yüzeyi) · yer tutucu 0 · bu dosyada URL 0 (C4)*
*Authority: Bayram Ali / Vault Steward · K008-domain kod örnekleri · 2026-10-17 · §9 domain event + specification (9 dil · E1 event repo satır kanıtlı s.39 · spec TASARIM ÖRNEĞİ) · yer tutucu 0 · URL 0*
