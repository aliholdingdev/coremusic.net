---
title: "K015-AI — Kod Örnekleri"
node_id: K015-AI/ai-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K015-AI/index.md
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
evidence:
  - "shared/src/AI/AIOrchestrator.php:26-38 · 2026-10-18"
  - "shared/src/AI/ToolCalling.php:28-40 · 2026-10-18"
  - "shared/src/AI/PromptEngine.php:27-35 · 2026-10-18"
  - "shared/src/AI/MemorySystem.php:18-31 · 2026-10-18"
  - "shared/src/AI/AuthenticationMiddleware.php:22-24 · 2026-10-18"
validation: "2026-10-18 — PHP blokları disk satır pencereleriyle; diğer diller şablon/iskele (production kodu değil)"
open_questions: "⚠️ non-PHP iskeleler production'a taşınmadan önce K015 master runtime etiketi (PLANNED/IMPLEMENTED) doğrulanmalı"
group: kod
---

# K015-AI — Kod Örnekleri

## §1 ELI15

Bu dosya yapay zeka katmanının **kod örneklerini** gösterir. PHP blokları gerçek
diskten gelir (satır numaralarıyla). Diğer diller (JS, TS, Python, C, C++, C#,
Go, Rust, Java, SQL) bu katmanın **diller arası iskeletleridir**: aynı kural
(izinsiz araç yok, injection taraması var, fail-closed) başka dilde nasıl görünür —
bunu gösterir. İskeleler üretim kodu değil, kuralın çevirisidir; üretim yüzeyine
yalnız PHP örneklere benzer şekilde, kanıtla girer.

## §2 Kapsam ve Kural

- Her blok dil etiketlidir, yorumludur (15–60 satır).
- PHP blokları disk kanıtlıdır; **satır numarası uydurulmaz**.
- Diğer diller **şablondur** — "production'a taşınacak" diye beyan edilmez
  (⚠️ §12).

## §3 PHP 8.4 — Disk Örnekleri

### §3.1 AIOrchestrator — agent yönlendirme (`shared/src/AI/AIOrchestrator.php:26-38`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

/**
 * Görev -> agent yönlendirme haritası.
 * KANIT: AIOrchestrator.php:26-38 (AGENT_MAP)
 */
final class AIOrchestrator
{
    // Disk kanıtı: 26-38 satırları arası AGENT_MAP tanımı.
    // Harita sabittir; yeni agent eklemek explicit değişiklik ister (D-01).
    private const AGENT_MAP = [
        'summarize'  => 'reader-agent',
        'translate'  => 'locale-agent',
        'recommend'  => 'reco-agent',
        'analyze'    => 'analyst-agent',
    ];

    public function dispatch(string $task, string $payload): string
    {
        // Bilinmeyen görev asla varsayılan agent'a düşmez (fail-closed D-01).
        if (!isset(self::AGENT_MAP[$task])) {
            throw new UnknownTaskException($task);
        }
        return self::AGENT_MAP[$task];
    }
}
```

### §3.2 ToolCalling — araç izni (`shared/src/AI/ToolCalling.php:28-40`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

/**
 * Agent -> araç beyazlistesi.
 * KANIT: ToolCalling.php:28-40 (AGENT_PERMISSIONS)
 */
final class ToolCalling
{
    // Disk kanıtı: 28-40. Yalnız izinli araçlar çağrılabilir (D-02).
    private const AGENT_PERMISSIONS = [
        'reader-agent' => ['kb.search', 'track.get'],
        'reco-agent'   => ['kb.search', 'user.history.read'],
        'analyst-agent'=> ['metrics.read'],
    ];

    public function allows(string $agent, string $tool): bool
    {
        // Default reddir: izin verilmemiş araç = false (whitelist, D-02).
        return in_array($tool, self::AGENT_PERMISSIONS[$agent] ?? [], true);
    }
}
```

### §3.3 PromptEngine — injection taraması (`shared/src/AI/PromptEngine.php:27-35`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

/**
 * Prompt birleştirme + tarama.
 * KANIT: PromptEngine.php:27-35 (INJECTION_PATTERNS)
 */
final class PromptEngine
{
    // Disk kanıtı: 27-35. Tarama savunma katmanıdır, model güvenli sayılmaz (D-03).
    private const INJECTION_PATTERNS = [
        '/ignore\s+(all\s+)?previous/i',
        '/system\s+prompt/i',
        '/\bact\s+as\b/i',
    ];

    public function sanitize(string $input): string
    {
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $input) === 1) {
                // Şüpheli girdi: prompt'a eklenmez, işaretlenir (D-03).
                throw new InjectionDetectedException($pattern);
            }
        }
        return trim($input);
    }
}
```

### §3.4 AuthenticationMiddleware — fail-closed 401 (`shared/src/AI/AuthenticationMiddleware.php:22-24`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

/**
 * AI ucu kimlik doğrulama kapısı.
 * KANIT: AuthenticationMiddleware.php:22-24 (401, fail-closed)
 */
final class AuthenticationMiddleware
{
    public function handle(Request $req, callable $next): Response
    {
        // Disk kanıtı 22-24: doğrulanamayan istek 401 döner, fail-open yok (D-04).
        $identity = $this->auth->verify($req->token);

        if ($identity === null) {
            return new Response(status: 401); // fail-closed
        }

        return $next($req->withIdentity($identity));
    }
}
```

### §3.5 MemorySystem — L1/L2/L3 (`shared/src/AI/MemorySystem.php:18-31`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\AI;

/**
 * Bellek hiyerarşisi.
 * KANIT: MemorySystem.php:18-31 (HIERARCHY + L1/L2/L3)
 */
final class MemorySystem
{
    // Disk kanıtı: 18-21 katmanlar, 25-31 HIERARCHY (D-05).
    private const L1 = 'session';   // kısa vadeli, RAM
    private const L2 = 'profile';   // kullanıcı profili (K007-data)
    private const L3 = 'archive';   // uzun vadeli arşiv

    public function remember(string $layer, string $key, string $value): void
    {
        if (!in_array($layer, [self::L1, self::L2, self::L3], true)) {
            throw new UnknownLayerException($layer); // bilinmeyen katman reddi
        }
        $this->store->put($layer, $key, $value);
    }
}
```

## §4 JavaScript (Node) — Agent aracı çağrısı iskeleti

```javascript
// K015-AI iskeleti (şablon): agent araç beyazlistesi.
// Kural D-02: izin verilmemiş araç asla çalışmaz.
'use strict';

const AGENT_PERMISSIONS = Object.freeze({
  'reader-agent': ['kb.search', 'track.get'],
  'reco-agent': ['kb.search', 'user.history.read'],
});

function canCall(agent, tool) {
  // Default false — whitelist genişliği otomatik genişlemez.
  return (AGENT_PERMISSIONS[agent] || []).includes(tool);
}

async function callTool(agent, tool, args) {
  if (!canCall(agent, tool)) {
    // Fail-closed: izin yoksa istek kırılır, sessizce geçmez.
    throw new Error(`permission denied: ${agent} -> ${tool}`);
  }
  return registry.invoke(tool, args);
}

module.exports = { canCall, callTool };
```

## §5 TypeScript — Tip güvenli agent sözleşmesi

```typescript
// K015-AI iskeleti (şablon): agent sözleşmesi tip güvenli.
// Kural D-01/D-02: görev adı ve araç adı union tip ile sınırlı.

type TaskName = 'summarize' | 'translate' | 'recommend' | 'analyze';
type ToolName = 'kb.search' | 'track.get' | 'user.history.read' | 'metrics.read';

interface DispatchResult {
  agent: string;
  task: TaskName;
}

// Görev -> agent haritası (PHP AGENT_MAP'in tip karşılığı, D-01).
const AGENT_MAP: Record<TaskName, string> = {
  summarize: 'reader-agent',
  translate: 'locale-agent',
  recommend: 'reco-agent',
  analyze: 'analyst-agent',
};

export function dispatch(task: TaskName, payload: string): DispatchResult {
  // TaskName dışı değer compile hatasıdır — çalışma zamanı sürprizi yok.
  return { agent: AGENT_MAP[task], task };
}

export type { TaskName, ToolName, DispatchResult };
```

## §6 Python — Prompt tarama iskeleti

```python
# K015-AI iskeleti (şablon): prompt injection taraması.
# Kural D-03: tarama savunma katmanıdır; model "güvenli" sayılmaz.
import re

# PHP INJECTION_PATTERNS'in Python karşılığı (D-03).
INJECTION_PATTERNS = (
    re.compile(r"ignore\s+(all\s+)?previous", re.I),
    re.compile(r"system\s+prompt", re.I),
    re.compile(r"\bact\s+as\b", re.I),
)


class InjectionDetected(Exception):
    """Şüpheli prompt kalıbı yakalandı."""


def sanitize(user_input: str) -> str:
    for pattern in INJECTION_PATTERNS:
        if pattern.search(user_input):
            # Şüpheli girdi prompt'a eklenmez (fail-closed, D-03).
            raise InjectionDetected(pattern.pattern)
    return user_input.strip()


def build_prompt(template: str, user_input: str) -> str:
    return template.format(user_input=sanitize(user_input))
```

## §7 C — Araç izni (düşük seviyeli agent döngüsü)

```c
/* K015-AI iskeleti (şablon): agent araç izni.
 * Kural D-02: beyazliste C'de tablo ile kurulur, default reddir. */
#include <string.h>
#include <stdbool.h>

typedef struct {
    const char *agent;
    const char *tool;
} permission_t;

/* Disk örneğinin C karşılığı: agent -> araç satırları. */
static const permission_t AGENT_PERMISSIONS[] = {
    {"reader-agent", "kb.search"},
    {"reader-agent", "track.get"},
    {"reco-agent",   "kb.search"},
};

static const size_t PERMISSION_COUNT =
    sizeof(AGENT_PERMISSIONS) / sizeof(AGENT_PERMISSIONS[0]);

bool can_call(const char *agent, const char *tool) {
    for (size_t i = 0; i < PERMISSION_COUNT; i++) {
        if (strcmp(AGENT_PERMISSIONS[i].agent, agent) == 0 &&
            strcmp(AGENT_PERMISSIONS[i].tool, tool) == 0) {
            return true; /* sadece tabloda varsa izin */
        }
    }
    return false; /* default: reddet (fail-closed, D-02) */
}
```

## §8 C++ — RAII agent oturum iskeleti

```cpp
// K015-AI iskeleti (şablon): agent oturumu RAII ile.
// Kural D-01: oturum kapanınca bellek/iz temizlenir (sızıntı yok).
#include <memory>
#include <stdexcept>
#include <string>

class AgentSession {
public:
    explicit AgentSession(std::string agent)
        : agent_(std::move(agent)) {
        if (agent_.empty()) {
            // Boş agent kabul edilmez (fail-closed, D-01).
            throw std::invalid_argument("agent required");
        }
    }

    ~AgentSession() noexcept {
        // RAII: araç izinleri ve bağlam oturum kapanışında bırakılır.
        revoke_all();
    }

    AgentSession(const AgentSession&) = delete;
    AgentSession& operator=(const AgentSession&) = delete;

    const std::string& agent() const noexcept { return agent_; }

private:
    void revoke_all() noexcept { /* izinleri geri al */ }
    std::string agent_;
};
```

## §9 C# — Fail-closed auth kapısı

```csharp
// K015-AI iskeleti (şablon): AI ucu authentication.
// Kural D-04: doğrulanamayan istek 401 — fail-open asla.
using System;

namespace CoreMusic.AI;

public sealed class AiAuthGate
{
    private readonly ITokenVerifier _verifier;

    public AiAuthGate(ITokenVerifier verifier) => _verifier = verifier;

    public AuthResult Verify(string? token)
    {
        // Token doğrulanamazsa null -> çağırıcı 401 üretir (D-04).
        var identity = string.IsNullOrEmpty(token)
            ? null
            : _verifier.Verify(token);

        return identity is null
            ? new AuthResult(401, null)   // fail-closed
            : new AuthResult(200, identity);
    }

    public readonly record struct AuthResult(int Status, Identity? Who);
}
```

## §10 Go — Agent yönlendirme servisi

```go
// K015-AI iskeleti (şablon): görev -> agent yönlendirme.
// Kural D-01: bilinmeyen görev hata döner, varsayılana düşmez.
package orchestrator

import "fmt"

// agentMap PHP AGENT_MAP'in Go karşılığı (D-01).
var agentMap = map[string]string{
	"summarize": "reader-agent",
	"translate": "locale-agent",
	"recommend": "reco-agent",
	"analyze":   "analyst-agent",
}

// Dispatch görevi agent'a yönlendirir; bilinmeyen görev error döner.
func Dispatch(task string) (string, error) {
	agent, ok := agentMap[task]
	if !ok {
		// Fail-closed: varsayılan agent yok (D-01).
		return "", fmt.Errorf("unknown task: %s", task)
	}
	return agent, nil
}
```

## §11 Rust — Zustand olmayan araç izni (tip güvenli whitelist)

```rust
// K015-AI iskeleti (şablon): araç izni Result ile.
// Kural D-02: izin yoksa Err — sessizce geçmez.
#[derive(Debug, PartialEq, Eq, Clone, Copy)]
pub enum Tool {
    KbSearch,
    TrackGet,
    MetricsRead,
}

/// agent -> izinli araçlar (PHP AGENT_PERMISSIONS'in Rust karşılığı, D-02).
pub fn can_call(agent: &str, tool: Tool) -> bool {
    match agent {
        "reader-agent" => matches!(tool, Tool::KbSearch | Tool::TrackGet),
        "reco-agent" => tool == Tool::KbSearch,
        _ => false, // bilinmeyen agent: default reddir (fail-closed)
    }
}

pub fn call_tool(agent: &str, tool: Tool) -> Result<(), &'static str> {
    if !can_call(agent, tool) {
        return Err("permission denied"); // D-02
    }
    Ok(())
}
```

## §12 Java — PromptEngine (Spring iskeleti)

```java
// K015-AI iskeleti (şablon): prompt tarama servisi.
// Kural D-03: INJECTION_PATTERNS desenleri Java regex ile.
package com.coremusic.ai;

import java.util.List;
import java.util.regex.Pattern;

public final class PromptEngine {

    // PHP INJECTION_PATTERNS'in Java karşılığı (D-03).
    private static final List<Pattern> INJECTION_PATTERNS = List.of(
        Pattern.compile("ignore\\s+(all\\s+)?previous", Pattern.CASE_INSENSITIVE),
        Pattern.compile("system\\s+prompt", Pattern.CASE_INSENSITIVE),
        Pattern.compile("\\bact\\s+as\\b", Pattern.CASE_INSENSITIVE)
    );

    public String sanitize(String input) {
        for (Pattern p : INJECTION_PATTERNS) {
            if (p.matcher(input).find()) {
                // Şüpheli girdi reddedilir (fail-closed, D-03).
                throw new InjectionDetectedException(p.pattern());
            }
        }
        return input.strip();
    }
}
```

## §13 SQL — KnowledgeBase kayıt şeması (K007-data sınırı)

```sql
-- K015-AI iskeleti (şablon): bilgi tabanı kaydı.
-- Kural D-06: RAG hattı PLANNED (ADR-030); tablo hazır, hat bekliyor.
CREATE TABLE ai_knowledge_entry (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source_type VARCHAR(32)      NOT NULL,   -- 'lyrics' | 'doc' | 'meta'
    source_ref  VARCHAR(255)     NOT NULL,   -- kaynak referans (izlenebilir)
    embedding   BLOB             NULL,       -- vektör (ADR-030 PLANNED)
    created_at  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ai_knowledge (source_type, source_ref)  -- tekilleme
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Parametreli ekleme zorunlu (kural: SQL parametreli sorgu).
-- INSERT INTO ai_knowledge_entry (source_type, source_ref) VALUES (?, ?);
```

## §14 PowerShell — AI uç sağlık kontrolü iskeleti (11. dil)

```powershell
# K015-AI iskeleti (şablon): AI uç health check.
# Kural D-04: 401/5xx görünürse uç health değil, fail-closed davranışı doğrular.
param(
    [Parameter(Mandatory = $true)][string]$BaseUrl
)

function Test-AiEndpoint {
    param([string]$Url)
    try {
        $resp = Invoke-WebRequest -Uri "$Url/health" -Method GET -TimeoutSec 5
        return [pscustomobject]@{ Endpoint = $Url; Status = [int]$resp.StatusCode }
    }
    catch {
        # Sağlık hatası yutulmaz: fail-closed UI listeler (D-04 ruhu).
        return [pscustomobject]@{ Endpoint = $Url; Status = 0; Error = $_.Exception.Message }
    }
}

Test-AiEndpoint -Url $BaseUrl
```

## §15 Diller ve Kural Eşlemesi

| # | Dil | Blok | Kural |
|---|-----|------|-------|
| 1 | PHP 8.4 | §3 (5 blok) | disk kanıtı D-01…D-05 |
| 2 | JavaScript | §4 | D-02 whitelist |
| 3 | TypeScript | §5 | D-01 tip güvenli harita |
| 4 | Python | §6 | D-03 tarama |
| 5 | C | §7 | D-02 tablo |
| 6 | C++ | §8 | D-01 RAII oturum |
| 7 | C# | §9 | D-04 fail-closed |
| 8 | Go | §10 | D-01 yönlendirme |
| 9 | Rust | §11 | D-02 Result izin |
| 10 | Java | §12 | D-03 regex |
| 11 | SQL | §13 | D-06 RAG/ADR-030 |
| 12 | PowerShell | §14 | D-04 health |

## §16 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

- ⚠️ §4–§14 blokları **iskele/şablondur** — diskte bunların production karşılığı
  **UNKNOWN**; taşınma kararı master runtime etiketi doğrulanmadan verilmez.
- ⚠️ §13 `embedding` sütunu ADR-030 (PLANNED) hatası dolayısıyla **dolu değil**.
- ⚠️ Disk PHP bloklarında satır numaraları `shared/src/AI/*.php` üzerinden
  2026-10-18 okunmuştur; dosya değişirse pencera güncellenmelidir.

## §17 İlgili Dosyalar

- `ai-bilesenler.md` — blokların bağlandığı bileşenler (C-01…C-06)
- `ai-kararlar.md` — D-01…D-08 karar karşılıkları
- `ai-test-stratejisi.md` — bu örneklerin doğrulanması
- `ai-dogrulama.md` — doğrulama zinciri

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
