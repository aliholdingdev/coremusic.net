---
node_id: K18-testing-kod-ornekleri
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K009-security, K007-data, K17-devops]
evidence:
  - ".ai/architecture/K18-testing/testing-master.md §4 (toolchain: phpunit ^10.5 · vitest ^5.0.3 · @playwright/test ^1.63.0 — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-dogrulama.md §2/§4 (55 PHP + 10 JS envanteri · PHPUnit --testsuite · Playwright 1.63 — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md §3 (pyramid katları — her örnek bir kata bağlanır — 2026-10-18)"
validation: "Her blok 15-60 satır, etiketli ve pyramid katına bağlı; örnekler ŞABLON'dur (repo kodu değildir); repo'ya ait iddialar satır kanıtıyla ayrı işaretlenir."
open_questions:
  - "⚠️ Bloklar şablon örnektir — repo'daki gerçek test dosyalarının içeriği bu turda satır satır okunmadı (envanter glob ile kanıtlanmıştır, içerik değil)"
  - "⚠️ C/Ruby/Kotlin/Swift blokları CoreMusic toolchain'inde DEĞİL (K003-audio/K004-dsp ekipleri için şablon)"
group: kod
---

# Testing — Kod Örnekleri

> **Bu dosyanın statüsü:** Aşağıdaki 12 blok **şablon örnektir** — her biri bir
> pyramid katına bağlı, hangi dilde o katın nasıl yazıldığını gösterir. Bloklar
> repo'daki gerçek test dosyalarının kopyası değildir (repo envanteri glob ile
> kanıtlanmıştır: 55 PHP + 10 JS — `testing-master.md` §4.1). Amaç: yeni test
> yazacak biri şablonu kopyalayıp kendi senaryosunu yazsın.

## §1. ELI15 — Kod örnekleri neden 12 dilde?

Test pyramid'i dille ilgili değil, **davranışla** ilgili: her dilde aynı üç
kural geçerlidir — (1) birim testi izoledir, (2) entegrasyon testi sınır
dokunur, (3) E2E akışı tarayıcıda koşar. CoreMusic'in kendi dili PHP +
TypeScript'tir (`testing-master.md` §4); diğer 10 dil, K003-audio/K004-dsp
(C/C++) ve komşu ekipler için şablon olarak taşınır. Her blokta **Arrange
(ayarla) → Act (çalıştır) → Assert (doğrula)** üçlüsü görünür
(martinfowler.com practical-test-pyramid, 2026-10-18 doğrulandı).

## §2. Dil Blokları (12 dil · pyramid katı etiketli)

### Blok 1 — PHP (PHPUnit ^10.5) · Kat: UNIT · Çocuk: unit/ ✅ kanıtlı araç

```php
<?php
declare(strict_types=1);

// ŞABLON ÖRNEK — pyramid katı: UNIT (testing-master.md §2.1 taban kat)
// Repo kanıtı: phpunit ^10.5 ×4 composer.json; shared/tests/Unit = 15 dosya
namespace Shared\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Shared\PageRouter\RouteTable;

final class RouteTableTest extends TestCase
{
    public function testRegisterAddsRoute(): void
    {
        // Arrange
        $table = new RouteTable();

        // Act
        $table->register('GET', '/api/v1/tracks', 'TracksController::index');

        // Assert
        self::assertTrue($table->has('GET', '/api/v1/tracks'));
    }

    public function testDuplicateRouteThrows(): void
    {
        // Arrange
        $table = new RouteTable();
        $table->register('GET', '/x', 'A::b');

        // Act + Assert (boundary durumu)
        $this->expectException(\LogicException::class);
        $table->register('GET', '/x', 'C::d');
    }
}
```

### Blok 2 — TypeScript (Vitest ^5.0.3) · Kat: UNIT · Çocuk: unit/ ✅ kanıtlı araç

```typescript
// ŞABLON ÖRNEK — pyramid katı: UNIT
// Repo kanıtı: root package.json vitest ^5.0.3 + jsdom ^30.1.1; assets 9 .spec.js
import { describe, it, expect } from 'vitest';
import { formatDurationMs } from './format-duration';

describe('formatDurationMs', () => {
  it('saniyeyi mm:ss formatına çevirir', () => {
    // Arrange
    const ms = 125_000;

    // Act
    const result = formatDurationMs(ms);

    // Assert
    expect(result).toBe('2:05');
  });

  it('negatif süreyi clamp eder (boundary)', () => {
    expect(formatDurationMs(-1)).toBe('0:00');
  });
});
```

### Blok 3 — JavaScript (Vitest) · Kat: INTEGRATION · Çocuk: integration/ ✅ kanıtlı

```javascript
// ŞABLON ÖRNEK — pyramid katı: INTEGRATION (bileşenler arası)
// Repo kanıtı: assets.coremusic.net 9 .spec.js (js/components/primitives ×6)
import { describe, it, expect, beforeEach } from 'vitest';
import { PlayerStore } from '../js/core/player-store';
import { formatDurationMs } from '../js/core/format-duration';

describe('PlayerStore + formatDuration (birlikte)', () => {
  let store;

  beforeEach(() => {
    store = new PlayerStore(); // gerçek bağımlılık — mock YOK (sociable stil)
  });

  it('parça yüklenince süre biçimlendirilmiş olarak görünür', () => {
    store.load({ title: 'Test', durationMs: 125_000 });
    expect(store.displayDuration()).toBe('2:05');
  });
});
```

### Blok 4 — Python (pytest) · Kat: UNIT · şablon (CoreMusic toolchain'inde DEĞİL)

```python
# ŞABLON ÖRNEK — pyramid katı: UNIT (kapsam dışı dil — komşu ekipler için)
# Not: CoreMusic'in Python test altyapısı diskte YOK (UNKNOWN); bu blok yalnız şablondur.
import pytest
from recommender import DiversityReranker


def test_reranker_min_score_filter():
    # Arrange
    reranker = DiversityReranker(min_score=0.7)
    items = [{"id": 1, "score": 0.9}, {"id": 2, "score": 0.4}]

    # Act
    kept = reranker.filter(items)

    # Assert
    assert [i["id"] for i in kept] == [1]


def test_reranker_empty_input():
    # boundary
    assert DiversityReranker(min_score=0.7).filter([]) == []
```

### Blok 5 — Go (`testing`) · Kat: INTEGRATION · şablon

```go
// ŞABLON ÖRNEK — pyramid katı: INTEGRATION (repo'da Go test altyapısı YOK — şablon)
package pipeline

import "testing"

func TestPipelineOrder(t *testing.T) {
    // Arrange
    p := NewPipeline([]Stage{StageDiscover, StageScore, StageDiversify})

    // Act
    got := p.Order()

    // Assert
    if len(got) != 3 || got[0] != StageDiscover {
        t.Fatalf("beklenen ilk aşama discover, gelen: %v", got)
    }
}
```

### Blok 6 — Rust (`cargo test`) · Kat: UNIT · şablon (Neva Engine ekipleri için)

```rust
// ŞABLON ÖRNEK — pyramid katı: UNIT (audio engine şablonu — K003/K004 ekipleri)
// Not: ring buffer ölçümü "zero-allocation" kuralına bağlı (registry §16).
#[cfg(test)]
mod tests {
    use super::RingBuffer;

    #[test]
    fn wrap_around_sayaci_dogru() {
        // Arrange
        let mut rb = RingBuffer::new(4);

        // Act
        rb.push(1.0); rb.push(2.0); rb.pop(); rb.push(3.0);

        // Assert
        assert_eq!(rb.len(), 2);
        assert_eq!(rb.pop(), Some(2.0));
    }
}
```

### Blok 7 — C# (xUnit) · Kat: UNIT · şablon (Windows SW ekipleri için)

```csharp
// ŞABLON ÖRNEK — pyramid katı: UNIT (WASAPI ekip şablonu)
using Xunit;

public class WasapiBufferTests
{
    [Fact]
    public void SharedBuffer_WriteBeyondCapacity_Throws()
    {
        // Arrange
        var buffer = new SharedBuffer(capacity: 512);

        // Act + Assert (boundary)
        Assert.Throws<InvalidOperationException>(() =>
            buffer.Write(new float[513]));
    }
}
```

### Blok 8 — Java (JUnit 5) · Kat: CONTRACT · şablon

```java
// ŞABLON ÖRNEK — pyramid katı: CONTRACT (API sözleşmesi şekli)
// Repo karşılığı: shared/tests/Api (ApiResponseTest · VersioningTest — PHP hâli)
import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

class ApiContractTest {
    @Test
    void versionedResponseHasEnvelope() {
        // Arrange
        var resp = ApiResponses.of("/api/v1/tracks", List.of(track));

        // Act
        var json = resp.toJson();

        // Assert (sözleşme: { data, meta } zorunlu)
        assertTrue(json.has("data"));
        assertTrue(json.has("meta"));
        assertEquals("v1", json.at("/meta/version").asText());
    }
}
```

### Blok 9 — C (Unity + CTest) · Kat: UNIT · şablon (DSP ekipleri için)

```c
/* ŞABLON ÖRNEK — pyramid katı: UNIT (C20 DSP — biquad katsayı hesabı)
 * Not: C test altyapısı repo'da YOK (UNKNOWN) — şablon. */
#include "unity.h"
#include "biquad.h"

void setUp(void) { }
void tearDown(void) { }

void test_lowpass_cutoff_katsayisi(void)
{
    /* Arrange */
    Biquad bq;

    /* Act */
    biquad_lowpass(&bq, 1000.0f, 48000.0f, 0.707f);

    /* Assert */
    TEST_ASSERT_TRUE(bq.b0 > 0.0f);
    TEST_ASSERT_FLOAT_WITHIN(1e-6f, bq.a0, 1.0f); /* normalize */
}
```

### Blok 10 — Bash (shunit2/stub) · Kat: BUILD-VERIFICATION · Çocuk: build-verification/ ✅ kanıtlı

```bash
#!/usr/bin/env bash
# ŞABLON ÖRNEK — pyramid katı: BUILD-VERIFICATION (lint/audit kapısı)
# Repo kanıtı: ci.yml php-lint (L25) · composer-audit (L110) job'ları
set -euo pipefail

fail=0

# Arrange: repo kökünde çalıştırılır
echo "== PHP lint taraması =="
while IFS= read -r -d '' f; do
  # Act + Assert: sözdizimi hatası varsa kapı düşer
  php -l "$f" > /dev/null || { echo "LINT FAIL: $f"; fail=1; }
done < <(find shared auth.coremusic.net api.coremusic.net home.coremusic.net \
         -name '*.php' -not -path '*/vendor/*' -print0)

exit $fail
```

### Blok 11 — PowerShell (Pester) · Kat: BUILD-VERIFICATION · şablon (Windows rig)

```powershell
# ŞABLON ÖRNEK — pyramid katı: BUILD-VERIFICATION (Windows donanım rig'i)
# Not: Pester altyapısı repo'da YOK (UNKNOWN) — şablon.
Describe 'WASAPI rig sağlık kontrolü' {
    It 'ses cihazı bağlı mı' {
        # Arrange
        $devices = Get-PnpDevice -Class AudioEndpoint -Status OK

        # Act + Assert (rig doğrulama — hardware-validation/ için ön koşul)
        $devices.Count | Should -BeGreaterThan 0
    }
}
```

### Blok 12 — SQL (assertion helper) · Kat: INTEGRATION · Çocuk: integration/ ✅ kanıtlı

```sql
-- ŞABLON ÖRNEK — pyramid katı: INTEGRATION (DB katmanı testi)
-- Repo kanıtı: shared/tests/Repository (ApiKeyRepositoryTest · ApiCreateCommandTest)
-- Kural: parametreli sorgu zorunlu; test fixture'ı Fixtures/ kütüphanesinden gelir.

-- Arrange: geçici API anahtarı (transaction içinde — test sonunda rollback)
BEGIN;
INSERT INTO api_keys (user_id, key_hash, created_at)
VALUES (:user_id, :key_hash, NOW());

-- Act: repository fonksiyonunun etkisi
UPDATE api_keys SET last_used_at = NOW() WHERE key_hash = :key_hash;

-- Assert: tek satır etkilendi ve alan dolu
SELECT CASE
    WHEN COUNT(*) = 1 AND MIN(last_used_at) IS NOT NULL
    THEN 'PASS' ELSE 'FAIL' END AS assertion
FROM api_keys WHERE key_hash = :key_hash;

ROLLBACK;
```

## §3. Pyramid ↔ Blok Eşlemesi (özet)

| Kat | Bloklar | Repo kanıtı |
|---|---|---|
| UNIT | 1 (PHP) · 2 (TS) · 4 (Python*) · 6 (Rust*) · 7 (C#*) · 9 (C*) | ✅ 55 PHP + 10 JS (PHP/TS/JS gerçeği) |
| INTEGRATION | 3 (JS) · 5 (Go*) · 12 (SQL) | ✅ tests/Api · Middleware · Repository · Events |
| CONTRACT | 8 (Java*) | ✅ ApiResponseTest · RouteTableTest · VersioningTest |
| BUILD-VERIFICATION | 10 (Bash) · 11 (PS*) | ✅ ci.yml php-lint · composer-audit · js-coverage |
| E2E | (bu turda blok YOK — 1 spec kanıtlı: player-info.spec.ts, kapsam dar ⚠️) | 🔶 kısmi |

> `*` = CoreMusic toolchain'inde DEĞİL; şablon olarak taşınır (UNKNOWN altyapı).

## §4. Kaynaklar

- `testing-master.md` §2.1/§3/§4 — pyramid + envanter + araç (2026-10-18)
- `testing-dogrulama.md` §2 — 18 iddia/S-sınıfı (2026-10-18)
- martinfowler.com/articles/practical-test-pyramid.html (2026-10-18, HTTP 200 — Arrange-Act-Assert + kat tanımları)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
